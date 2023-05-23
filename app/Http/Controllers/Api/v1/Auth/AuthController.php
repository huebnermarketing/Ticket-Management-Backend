<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RestResponse;
use Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required'
            ]);

            if ($validate->fails()) {
                return RestResponse::validationError($validate->errors());
            }


            $credentials = $request->only('email', 'password');
            $credentials['is_verified'] = 1;
            $credentials['is_active'] = 1;
            $checkUser = Auth::attempt($credentials);
            if (!$checkUser) {
                return RestResponse::warning('Incorrect user OR password', 422);
            }

            $getUser = User::where('email', '=', $credentials['email'])->first();
            if ($getUser['is_verified'] == 0){
                return RestResponse::warning('An Account has already been registered, but was never verified. Please verify your account.', 422);
            }
            $user = Auth::user();
            $response['access_token'] = $user->createToken('Api Token')->accessToken;
            $response['user'] = $user;
            return RestResponse::success($response, 'Access token successfully retrieved');
        } catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function logout()
    {
        Auth::user()->token()->delete();
        return RestResponse::success([], 'Token successfully removed');
    }
}
