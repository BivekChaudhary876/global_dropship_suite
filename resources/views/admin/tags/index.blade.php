@extends('layouts.app')
@section('title', 'Tags')
@section('content')
<div class="page-pad">
    <h1 class="mb-md">Tags</h1>
    <form method="POST" action="{{ route('admin.tags.store') }}" class="tag-form">
        @csrf
        <input type="text" name="name" placeholder="New tag name" required>
        <button type="submit" class="btn-signal">Add</button>
    </form>
    <table>
        <thead><tr><th>Name</th><th>Products</th><th></th></tr></thead>
        <tbody>
            @foreach ($tags as $tag)
                <tr>
                    <td>{{ $tag->name }}</td>
                    <td>{{ $tag->products_count }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" onsubmit="return confirm('Delete?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection