<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

// Sits behind the admin middleware group in routes/web.php, same as
// TagController. Two safety rules that don't exist on the other admin
// CRUD screens, because this one is destructive in a way products/tags
// aren't: an admin can't delete or demote their own account, and
// deleting a user cascades to their orders/reviews (enforced at the DB
// level via cascadeOnDelete in the migrations) - so we warn about that
// explicitly rather than let it happen silently.
class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::withCount(['orders', 'reviews'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'customer'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')->with('status', 'User created.');
    }

    public function show(User $user)
    {
        $user->load(['orders' => fn($q) => $q->latest(), 'reviews.product']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'customer'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Can't demote your own account - would lock you out of this
        // screen with no other admin necessarily available to fix it.
        if ($user->id === $request->user()->id && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot remove your own admin access.']);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'User updated.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        // Deleting cascades to this user's orders and reviews (see the
        // migrations - both foreign keys are cascadeOnDelete). Block it
        // if they have order history, rather than silently destroy it.
        if ($user->orders()->exists()) {
            return back()->withErrors(['user' => 'This user has order history and cannot be deleted.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'User deleted.');
    }
}