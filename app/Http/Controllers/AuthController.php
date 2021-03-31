<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRegisterRequest;
use App\Models\User;
use App\Traits\HTTPResponseTrait;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use HTTPResponseTrait;

    public function register(AuthRegisterRequest $request)
    {
        try {
            // Check if user exists
            $user = User::where('email', $request->email)->first();

            // Handle returning user
            if ($user) {
                $accessToken = $user->createToken('authToken')->accessToken;
                return $this->successResponse('Returning user', 200, ['user' => $user, 'access_token' => $accessToken]);
            }

            // Handle new user
            $user_data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'pickup_address' => $request->pickup_address,
                'pickup_coordinates' => $request->get('pickup_coordinates'),
                'pickup_time' => $request->get('pickup_time'),
                'password' => bcrypt("password")
            ];

            $user = User::create($user_data);

            $accessToken = $user->createToken('authToken')->accessToken;

            return $this->successResponse('Create new user', 201, ['user' => $user, 'access_token' => $accessToken]);
        } catch (\Exception $ex) {
            if (isset($user)) {
                $user->forceDelete();
            }
            return $this->errorResponse('Something went wrong: ' . $ex->getMessage());
        }
    }
}
