<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Auth\AuthService;
use App\DTOs\Auth\LoginDTO;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $dto = new LoginDTO($request->all());

        $result = $this->authService->login($dto);

        return response()->json($result, isset($result['error']) ? 401 : 200);
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $result = $this->authService->register($validated);

        return response()->json($result, 201);
    }
}
