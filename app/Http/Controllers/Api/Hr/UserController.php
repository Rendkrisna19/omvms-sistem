<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET: List Semua User (Lengkap dengan Role & Data Karyawan)
    public function index(Request $request)
    {
        // Eager load relasi 'role' dan 'employee' agar efisien
        $query = User::with(['role', 'employee']);

        // Filter by Role (misal: tampilkan yang admin saja)
        if ($request->role_id) {
            $query->where('role_id', $request->role_id);
        }

        // Search by Username atau Email
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->latest()->paginate(10)
        ]);
    }

    // POST: Buat User Baru (Registrasi Akun)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
            // employee_id opsional (Super Admin mungkin tidak punya data karyawan)
            'employee_id' => 'nullable|exists:employees,id|unique:users,employee_id', 
            'is_active' => 'boolean'
        ]);

        // Hash Password sebelum simpan
        $validated['password'] = Hash::make($validated['password']);
        
        // Default active true jika tidak dikirim
        $validated['is_active'] = $request->boolean('is_active', true);

        $user = User::create($validated);

        return response()->json([
            'status' => 'success', 
            'message' => 'User berhasil dibuat',
            'data' => $user
        ], 201);
    }

    // GET: Detail User
    public function show($id)
    {
        $user = User::with(['role', 'employee.department'])->findOrFail($id);
        
        return response()->json([
            'status' => 'success', 
            'data' => $user
        ]);
    }

    // PUT: Update Data User
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
            'employee_id' => 'nullable|exists:employees,id|unique:users,employee_id,' . $id,
            'is_active' => 'boolean'
        ];

        // Password divalidasi hanya jika diisi (untuk ganti password)
        if ($request->filled('password')) {
            $rules['password'] = 'min:6';
        }

        $validated = $request->validate($rules);

        // Jika ada password baru, hash ulang. Jika kosong, hapus dari array agar tidak tertimpa null/kosong
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'status' => 'success', 
            'message' => 'User berhasil diperbarui',
            'data' => $user
        ]);
    }

    // DELETE: Hapus User
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Preventif: Jangan biarkan menghapus diri sendiri saat sedang login
        if (auth()->id() == $id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak bisa menghapus akun anda sendiri.'
            ], 403);
        }

        $user->delete(); // Soft delete

        return response()->json([
            'status' => 'success', 
            'message' => 'User berhasil dihapus'
        ]);
    }
}