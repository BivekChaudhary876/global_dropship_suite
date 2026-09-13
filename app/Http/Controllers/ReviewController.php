<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        // updateOrCreate so a user can only ever have one review per product
        $product->reviews()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return back()->with('status', 'Thanks for your review!');
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review); // uses ReviewPolicy

        $product = $review->product;
        $review->delete();

        return redirect()->route('products.show', $product)->with('status', 'Review deleted.');
    }
}