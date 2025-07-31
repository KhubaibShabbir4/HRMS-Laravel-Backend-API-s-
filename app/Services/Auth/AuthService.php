<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\DTOs\Auth\LoginDTO;
use Spatie\Permission\Models\Role;

class AuthService
{
    public function login(LoginDTO $dto)
    {
        $credentials = ['email' => $dto->email, 'password' => $dto->password];

        if (!$token = JWTAuth::attempt($credentials)) {
            return ['error' => 'Invalid credentials'];
        }

        return [
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => JWTAuth::factory()->getTTL() * 60,
        ];
    }

    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        // Assign role — ensure the role exists
        $roleName = $data['role'] ?? 'Employee'; // default role if not provided

        if (Role::where('name', $roleName)->exists()) {
            $user->assignRole($roleName);
        } else {
            throw new \Exception("Role {$roleName} does not exist");
        }

        return [
            'user' => $user,
            'token' => auth()->login($user),
        ];
    }
}
