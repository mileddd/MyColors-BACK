<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(array $data)
    {
        // Find user by username
        $user = User::where([
            ['username', $data['username']],
            ['password','like',$data['password']]
        ]
        )->first();

        if (!$user) {
            return null;
        }

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
