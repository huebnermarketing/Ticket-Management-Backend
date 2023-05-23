<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\User;
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

            $getUser = User::where('email', '=', $credentials['email'])->withTrashed()->select('deleted_at', 'email_verify')->first();
            if ($getUser['deleted_at']) {
                return RestResponse::warning('User is deactivate in system, Please contact to administration.', 422);
            }elseif ($getUser['email_verify'] == 0){
                return RestResponse::warning('An Account has already been registered, but was never verified. Please verify your account.', 422);
            }
            $user = Auth::user();
            $response['access_token'] = $user->createToken('Api Token')->accessToken;
            return RestResponse::success($response, 'Access token successfully retrieved');
        } catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
