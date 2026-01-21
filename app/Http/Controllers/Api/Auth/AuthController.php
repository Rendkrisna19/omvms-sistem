<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Ambil user
        $user = User::where('email', $request->email)->first();

        // 3. Cek user & password
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau password salah'
            ], 401);
        }

        // 4. Cek status user
        if (! $user->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun anda dinonaktifkan'
            ], 403);
        }

        // 5. (Optional) hapus token lama
        $user->tokens()->delete();

        // 6. Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 7. Response
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
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    }
}
