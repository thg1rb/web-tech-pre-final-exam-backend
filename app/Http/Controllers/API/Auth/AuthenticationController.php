<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function login(Request $request) {
        # Request body validation
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);
        
        # Find user by email
        $user = User::query()->where('email', $request->input('email'))->first();
        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'message' => 'Invalid Credential'
            ], 401);
        } 
            
        # Create auth token (with role ability)
        $token = $user->createToken('auth_token', [$user->role])->plainTextToken;

        # Return the response
        return response()->json([
            'token' => $token,
            'exdpires_at' => now()->addMinutes(config('sanctum.expiration'))->toDateTimeString(),
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    }

    public function revoke(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Token revoked'
        ]);
    }
}
