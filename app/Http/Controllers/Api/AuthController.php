<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
    public function register(RegisterRequest $request) {
        $data = $request->validated();

        $user = new User();
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->save();

        $device_name = $data['device_name'] ?? $request->userAgent() ?? 'mobile';
        $token = $user->createToken($device_name)->plainTextToken;

        return $this->success(
            data: [
                'user' => new UserResource($user),
                'authToken' => $token,
                'tokenType' => 'Bearer',
            ],
            message: "Registered successfully",
            status: 201
        );
    }

    public function login(LoginRequest $request) {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return $this->error(
                message: 'Invalid email or password',
                status: 401
            );
        }

        $device_name = $data['device_name'] ?? $request->userAgent() ?? 'mobile';
        $user->tokens()->where('name', $device_name)->delete();
        $token = $user->createToken($device_name)->plainTextToken;

        return $this->success(
            data: [
                'user' => new UserResource($user),
                'authToken' => $token,
                'tokenType' => 'Bearer',
            ],
            message: 'Logged in successfully'
        );
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return $this->success(
            message: 'Logged out successfully'
        );
    }
}
