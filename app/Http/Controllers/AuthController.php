<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Auth\AuthService;
use App\DTOs\Auth\LoginDTO;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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

    public function logout(Request $request)
    {
        try {
            Auth::guard('api')->logout();

            return response()->json(['message' => 'Successfully logged out']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to logout'], 500);
        }
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
            'role' => 'nullable|string|exists:roles,name',
        ]);

        $result = $this->authService->register($validated);
        $user = $result['user'];

        Mail::to($user->email)->send(new WelcomeMail($user->name));

        return response()->json($result, 201);
    }
}
