<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ApiController::errorResponse($validator->errors(), 401);
        }

        // Find user
        $user = User::where([
            ['username', $request->username],
            ['password','like',$request->password]
        ])->first();

        if( !$user )
        {
            return ApiController::errorResponse('Invalid credentials !', 401);
        }
        // Generate token (using Laravel Sanctum or simple token for now)
        $token = $user->createToken('auth_token')->plainTextToken;

        return ApiController::successResponse('Login successful !', 200, $token);
    }
}
