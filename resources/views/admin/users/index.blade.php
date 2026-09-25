@extends('layouts.app')
@section('title', 'Users')
@section('content')
<div class="page-pad">
    <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:1.5rem;">
        <h1>Users</h1>
        <a href="{{ route('admin.users.create') }}" class="btn-signal">+ Add user</a>
    </div>

    <table>
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Orders</th><th>Reviews</th><th></th></tr></thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="pill" style="{{ $user->isAdmin() ? 'color:var(--signal);background:rgba(255,90,60,0.12);' : '' }}">{{ ucfirst($user->role) }}</span></td>
                    <td>{{ $user->orders_count }}</td>
                    <td>{{ $user->reviews_count }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn-ghost" style="padding:0.4rem 0.9rem;">View</a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-ghost" style="padding:0.4rem 0.9rem;">Edit</a>
                        @if ($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user? This cannot be undone.');" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-lg">{{ $users->links() }}</div>
</div>
@endsection