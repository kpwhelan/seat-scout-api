<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
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

        $token = $user->createToken('authToken')->plainTextToken;

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
}
