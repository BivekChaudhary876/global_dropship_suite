<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Category;
use App\Models\Supplier;
use App\Models\Tag;

/**
 * ProductController@create and @edit both need the same three dropdown
 * lists (categories, suppliers, tags) to build the product form. Previously
 * each method fetched them independently - identical code, two places to
 * update if a query ever needs to change (e.g. adding ->where('active', true)).
 */
trait HasProductFormOptions
{
    protected function productFormOptions(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ];
    }
}