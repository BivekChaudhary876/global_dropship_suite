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

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:tags,name,' . $tag->id],
        ]);

        $tag->update($data);

        return redirect()->route('admin.tags.index')->with('status', 'Tag updated.');
    }

    public function destroy(Tag $tag)
    {
        $tag->products()->detach(); // clean up pivot rows first
        $tag->delete();

        return back()->with('status', 'Tag deleted.');
    }
}