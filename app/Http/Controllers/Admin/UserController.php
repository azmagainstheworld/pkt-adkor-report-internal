<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
        })->paginate(10)->withQueryString();

        return view('admin.users.manajemen-pengguna', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            // super_admin hanya bisa dibuat via seeder/artisan, TIDAK lewat form
            'role'     => 'required|in:admin,karyawan',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required'      => 'Nama lengkap wajib diisi!',
            'email.required'     => 'Email wajib diisi!',
            'email.unique'       => 'Email ini sudah terdaftar!',
            'role.required'      => 'Role wajib dipilih!',
            'role.in'            => 'Role tidak valid!',
            'password.required'  => 'Password wajib diisi!',
            'password.confirmed' => 'Konfirmasi password tidak cocok!',
            'password.min'       => 'Password minimal 8 karakter!',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'role'      => $request->role,
            'password'  => Hash::make($request->password),
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$request->name} berhasil didaftarkan sebagai {$request->role}.");
    }

    // FUNGSI CHANGE PASSWORD SUDAH DIHAPUS DARI SINI

    public function toggleStatus(User $user)
    {
        // Super Admin tidak bisa dinonaktifkan oleh siapapun
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error_modal', 'Akun Super Admin tidak dapat dinonaktifkan atau diubah statusnya.');
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$user->name} berhasil {$status}.");
    }

    public function destroy(User $user)
    {
        // Super Admin tidak bisa dihapus oleh siapapun
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error_modal', 'Akun Super Admin tidak dapat dihapus secara permanen.');
        }

        $nama = $user->name;
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$nama} berhasil dihapus permanen.");
    }
}