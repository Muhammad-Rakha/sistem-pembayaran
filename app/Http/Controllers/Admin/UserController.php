<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->q;
        $users = User::with('roles')
            ->when($q, fn ($query) => $query->where('nama', 'like', "%$q%")->orWhere('username', 'like', "%$q%"))
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles', 'q'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|exists:roles,name',
            'status'   => 'required|in:aktif,nonaktif',
        ]);
        $user = User::create([
            'nama'     => $data['nama'],
            'username' => $data['username'],
            'password' => $data['password'],
            'status'   => $data['status'],
        ]);
        $user->assignRole($data['role']);
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nama'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role'     => 'required|exists:roles,name',
            'status'   => 'required|in:aktif,nonaktif',
        ]);
        $update = ['nama' => $data['nama'], 'username' => $data['username'], 'status' => $data['status']];
        if (!empty($data['password'])) {
            $update['password'] = $data['password'];
        }
        $user->update($update);
        $user->syncRoles([$data['role']]);
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Pengguna dihapus.');
    }
}
