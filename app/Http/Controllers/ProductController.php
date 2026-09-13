<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with(['category', 'supplier', 'reviews']);

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->integer('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->latest()->paginate(9)->appends($request->query());
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        return view('products.create', [
            'categories' => Category::orderBy('name')->get(),
            'suppliers'  => Supplier::orderBy('name')->get(),
            'tags'       => Tag::orderBy('name')->get(),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $data         = $request->validated();
        $data['slug'] = Str::slug($data['name']).'-'.Str::random(6);
        $tagIds       = $request->input('tags', []);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);
        $product->tags()->sync($tagIds);

        return redirect()->route('products.show', $product)->with('status', 'Product created.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'supplier', 'reviews.user', 'tags']);

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
        return view('products.edit', [
            'product'    => $product,
            'categories' => Category::orderBy('name')->get(),
            'suppliers'  => Supplier::orderBy('name')->get(),
            'tags'       => Tag::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $tagIds = $request->input('tags', []);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        $product->tags()->sync($tagIds);

        return redirect()->route('products.show', $product)->with('status', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();

        return redirect()->route('products.index')->with('status', 'Product deleted.');
    }
}