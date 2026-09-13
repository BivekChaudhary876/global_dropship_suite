<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return view('admin.tags.index', ['tags' => Tag::withCount('products')->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:tags,name'],
        ]);

        Tag::create($data);

        return back()->with('status', 'Tag added.');
    }

    public function destroy(Tag $tag)
    {
        $tag->products()->detach(); // clean up pivot rows first
        $tag->delete();

        return back()->with('status', 'Tag deleted.');
    }
}