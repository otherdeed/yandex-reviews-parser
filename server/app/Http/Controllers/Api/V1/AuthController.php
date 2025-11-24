<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'login'     => 'required|string|max:255|unique:users,login',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'login'     => $request->login,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user,
            'token'   => $token,
            'type'    => 'bearer',
            'expires_in'  => config('jwt.ttl') * 60 . ' seconds'
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string|max:255',
            'password' => 'required',
        ]);

        if (!$token = JWTAuth::attempt($request->only('login', 'password'))) {
            throw ValidationException::withMessages([
                'login' => ['Неверный login или пароль'],
            ]);
        }

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'type'    => 'bearer',
            'expires_in'  => config('jwt.ttl') * 60
        ]);
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function updateUrl(Request $request)
    {
        $request->validate([
            'url' => [
                'required',
                'url:http,https',
                'max:255',
                Rule::unique('users', 'url')->ignore(auth('api')->id()),
            ],
        ]);

        $url = $request->input('url');
        
        if (!str_contains($url, 'yandex.ru/maps')) {
            return response()->json([
                'success' => false,
                'message' => 'Некорректная ссылка на Яндекс.Карты'
            ], 400);
        }

        /** @var \App\Models\User $user */
        $user = auth('api')->user();

        $user->update(['url' => $request->url]);

        return response()->json(auth('api')->user());
    }

    public function refresh()
    {
        return response()->json([
            'token' => JWTAuth::refresh(),
            'type'  => 'bearer',
            'expires_in'  => config('jwt.ttl') * 60 . ' seconds'
        ]);
    }
}