@extends('layouts.app')
@section('title', 'Categories')
@section('content')
<div class="page-pad">
    <h1 class="mb-md">Categories</h1>
    <form method="POST" action="{{ route('admin.categories.store') }}" class="tag-form">
        @csrf
        <input type="text" name="name" placeholder="New category name" required>
        <button type="submit" class="btn-signal">Add</button>
    </form>
    <table>
        <thead><tr><th>Name</th><th>Products</th><th></th></tr></thead>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->products_count }}</td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn-ghost">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection