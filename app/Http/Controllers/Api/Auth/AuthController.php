<?php

namespace App\Http\Controllers\Api\Auth; 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Cek Kredensial (Email & Password)
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau password salah.'
            ], 401);
        }

        // 3. Ambil Data User
        $user = User::where('email', $request->email)->first();

        // Cek apakah user aktif?
        if (!$user->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun anda dinonaktifkan.'
            ], 403);
        }

        // 4. Generate Token Sanctum
        // 'auth_token' adalah nama tokennya.
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Return Response JSON
        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    public function logout(Request $request)
    {
        // Hapus token yang sedang dipakai
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ]);
    }
    
    // API untuk cek user sedang login siapa (Profile)
    public function me(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    }
}