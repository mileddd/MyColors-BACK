<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;

class AuthController extends ApiController
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->only(['username', 'password']));

        if (!$result) {
            return $this->errorResponse('Invalid credentials !', 401);
        }

        return $this->successResponse('Login successful !', 200, $result['token']);
    }
}
