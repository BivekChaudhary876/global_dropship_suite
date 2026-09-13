@extends('layouts.app')
@section('title', 'Tags')
@section('content')
    <h1>Tags</h1>
    <form method="POST" action="{{ route('admin.tags.store') }}" style="display:flex;gap:0.5rem;max-width:400px;margin-bottom:1rem;">
        @csrf
        <input type="text" name="name" placeholder="New tag name" required>
        <button type="submit" class="btn">Add</button>
    </form>
    <table style="width:100%;border-collapse:collapse;background:#fff;">
        <tr><th style="text-align:left;padding:0.5rem;">Name</th><th>Products</th><th></th></tr>
        @foreach ($tags as $tag)
            <tr>
                <td style="padding:0.5rem;">{{ $tag->name }}</td>
                <td>{{ $tag->products_count }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" onsubmit="return confirm('Delete?');">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none;border:none;color:#dc2626;cursor:pointer;">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection