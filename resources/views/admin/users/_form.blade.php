@php $u = $user ?? null; @endphp

<label>Name</label>
<input type="text" name="name" value="{{ old('name', $u->name ?? '') }}" required>

<label>Email</label>
<input type="email" name="email" value="{{ old('email', $u->email ?? '') }}" required>

<label>Role</label>
<select name="role" required>
    <option value="customer" @selected(old('role', $u->role ?? 'customer') === 'customer')>Customer</option>
    <option value="admin" @selected(old('role', $u->role ?? '') === 'admin')>Admin</option>
</select>

<label>Password @if($u) (leave blank to keep current) @endif</label>
<input type="password" name="password" {{ $u ? '' : 'required' }}>

<label>Confirm password</label>
<input type="password" name="password_confirmation" {{ $u ? '' : 'required' }}>