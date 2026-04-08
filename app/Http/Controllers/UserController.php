<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Menampilkan daftar anggota (Hanya role 'siswa').
     */
    public function index(): View
    {
        $users = User::where('role', 'siswa')->latest()->get();
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Menampilkan form tambah anggota.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Menyimpan anggota baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|unique:users,username',
            'password'     => 'required|string|min:6',
            'alamat'       => 'nullable|string',
        ]);

        User::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'username'     => $validated['username'],
            'password'     => Hash::make($validated['password']),
            'alamat'       => $validated['alamat'],
            'role'         => 'siswa',
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update data anggota.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|unique:users,username,' . $user->id,
            'alamat'       => 'nullable|string',
            'password'     => 'nullable|string|min:6',
        ]);

        // Gunakan method only() untuk mengambil data selain password terlebih dahulu
        $user->fill($request->only(['nama_lengkap', 'username', 'alamat']));

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'Data anggota diperbarui.');
    }

    /**
     * Hapus anggota.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }
}