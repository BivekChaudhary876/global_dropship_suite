<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasProductFormOptions;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Support\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    use HasProductFormOptions;

    public const SORT_OPTIONS = [
        'newest' => 'Newest',
        'price_asc' => 'Price: Low to High',
        'price_desc' => 'Price: High to Low',
        'name_asc' => 'Name: A to Z',
        'name_desc' => 'Name: Z to A',
        'rating' => 'Top Rated',
    ];

    public function index(Request $request)
    {
        $query = Product::query()->with(['category', 'reviews', 'tags'])
            ->withAvg('reviews', 'rating');

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($category) use ($search) {
                        $category->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('tags', function ($tag) use ($search) {
                        $tag->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($categoryId = $request->integer('category_id')) {
            $query->where('category_id', $categoryId);
        }
        
        $sort = $request->string('sort')->value();
        if (! array_key_exists($sort, self::SORT_OPTIONS)) {
            $sort = 'newest';
        }

        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            // Products with no reviews yet have a null average - sort those
            // after every rated product instead of letting the database
            // put nulls first, which would bury every rated item at the bottom.
            'rating' => $query->orderByRaw('reviews_avg_rating is null')->orderByDesc('reviews_avg_rating'),
            default => $query->latest(),
        };

        $products = $query->paginate(9)->appends($request->query());
        $categories = Category::orderBy('name')->get();

        $stats = [
            'products' => Product::count(),
            'categories' => Category::count(),
            'avgRating' => round(\App\Models\Review::avg('rating') ?? 0, 1),
        ];

        return view('products.index', compact('products', 'categories', 'stats', 'sort'));
    }

    public function create()
    {
        return view('products.create', $this->productFormOptions());
    }

    public function store(StoreProductRequest $request)
    {
        $data         = $request->validated();
        $data['slug'] = Str::slug($data['name']).'-'.Str::random(6);
        $tagIds       = $request->input('tags', []);

        $uploadedPaths       = ImageUploader::storeMany($request->file('images', []));
        $data['image_path']  = $uploadedPaths[0] ?? null;

        $product = Product::create($data);
        $product->tags()->sync($tagIds);

        foreach ($uploadedPaths as $path) {
            $product->images()->create(['path' => $path]);
        }

        return redirect()->route('products.show', $product)->with('status', 'Product created.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'reviews.user', 'tags', 'images']);

        // Third-party API integration: convert the price to USD/EUR using a
        // free public exchange-rate API, cached for an hour so we don't hit
        // it on every single page view.
        $converted = Cache::remember('fx-rates-aud', 3600, function () {
            try {
                $response = Http::timeout(3)->get('https://open.er-api.com/v6/latest/AUD');
                if ($response->successful()) {
                    $rates = $response->json('rates');
                    return ['USD' => $rates['USD'] ?? null, 'EUR' => $rates['EUR'] ?? null];
                }
            } catch (\Throwable $e) {
                // API unreachable - fail gracefully, page still works without conversion
            }
            return null;
        });

        return view('products.show', compact('product', 'converted'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', ['product' => $product] + $this->productFormOptions());
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $tagIds = $request->input('tags', []);

        // Remove any existing images the admin ticked for removal, then add
        // any newly-uploaded ones - both are optional, independent of each
        // other, so an edit can do neither, either, or both at once.
        $removeIds = $request->input('remove_images', []);
        $newFiles = $request->file('images', []);
        $galleryChanged = (bool) $removeIds || (bool) $newFiles;

        if ($removeIds) {
            $product->images()->whereIn('id', $removeIds)->get()->each(function ($image) {
                ImageUploader::delete($image->path);
                $image->delete();
            });
        }

        foreach (ImageUploader::storeMany($newFiles) as $path) {
            $product->images()->create(['path' => $path]);
        }

        // image_path is the "cover" photo used everywhere that only shows
        // one image (catalogue grid, product card) - keep it pointed at
        // whichever image is now first in the gallery. Only touch it when
        // the gallery actually changed, so a product whose image_path
        // predates this feature (and so has no product_images row yet)
        // doesn't get wiped out by an edit that didn't touch its photos.
        if ($galleryChanged) {
            $data['image_path'] = $product->images()->oldest('id')->value('path');
        }

        $product->update($data);
        $product->tags()->sync($tagIds);

        return redirect()->route('products.show', $product)->with('status', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        if ($product->orderItems()->exists()) {
            return back()->with('status', 'Cannot delete a product that has already been ordered — it needs to stay so past orders keep their history.');
        }

        // images->pluck('path') normally already includes image_path (it's
        // kept pointed at the first gallery row), but a product untouched
        // since before the gallery feature existed may still only have the
        // legacy image_path with no matching product_images row - merge
        // both so nothing is left behind on disk either way.
        $paths = $product->images->pluck('path')->push($product->image_path)->filter()->unique();
        ImageUploader::deleteMany($paths);
        $product->delete();

        return redirect()->route('products.index')->with('status', 'Product deleted.');
    }
}