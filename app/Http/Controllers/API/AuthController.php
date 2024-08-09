<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $roleuser = Role::where('name', 'user')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $roleuser->id,
        ]);

        $curUser = User::with('roles')->where('email', $request->email)->first();

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Register Berhasil',
            'token' => $token,
            'user' => $curUser,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'User Invalid'], 401);
        }

        $user = User::with('roles')->where('email', $request->email)->first();

        return response()->json([
            // 'message' => 'User Berhasil Login',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Logout Berhasil']);
    }

    public function me()
    {
        $user = User::with('roles', 'profile')->find(Auth::guard('api')->id());

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'message' => 'Berhasil Get User',
            'user' => $user
        ]);
    }

}
