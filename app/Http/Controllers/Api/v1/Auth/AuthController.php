<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendPasswordVerificationLink;
use App\Models\PasswordVerificationEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RestResponse;
use Validator;
use File;
use Storage;

class AuthController extends Controller
{
    private $perUserAuth;
    public function __construct()
    {
        $this->perUserAuth = config('constant.PERMISSION_USER_AUTH');
    }
    public function login(Request $request)
    {
        try {
            if($request['permission'] == $this->perUserAuth){
                $validate = Validator::make($request->all(), [
                    'email' => 'required',
                    'password' => 'required|min:6'
                ]);

                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }

                $credentials = $request->only('email', 'password');
                $credentials['is_verified'] = 1;
                $credentials['is_active'] = 1;
                $checkUser = Auth::attempt($credentials);
                if (!$checkUser) {
                    return RestResponse::warning('Incorrect user OR password.', 422);
                }

                $getUser = User::with('role')->where('email', '=', $credentials['email'])->first();
                if ($getUser['is_verified'] == 0){
                    return RestResponse::warning('An account has already been registered, but was never verified. please verify your account.', 422);
                }
                //$user = Auth::user();
                $response['access_token'] = $getUser->createToken('Api Token')->accessToken;
                $response['user'] = $getUser;
                $response['permissions'] = $getUser->getAllPermissions();
                return RestResponse::success($response, 'Access token successfully retrieved.');
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        } catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function logout()
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perUserAuth)){
                Auth::user()->token()->delete();
                return RestResponse::success([], 'Token successfully removed.');
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function forgotPasswordLinkEmail(Request $request)
    {
        try {
            if($request['permission'] == $this->perUserAuth){
                $validate = Validator::make($request->all(), [
                    'email' => 'required',
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }

                $checkUser = User::where('email', $request->email)->first();
                if (!empty($checkUser)) {
                    //Delete Old token
                    PasswordVerificationEmail::where('user_id', $checkUser->id)->delete();

                    $email_token = Str::random(50);
                    PasswordVerificationEmail::create([
                        'user_id' => $checkUser->id,
                        'token' => $email_token
                    ]);
                    $resetPasswordLink = config('constant.FRONTEND_URL') . '/reset-password?token=' . $email_token;
                    $data['reset_pwd_link'] = $resetPasswordLink;
                    $data['user_name'] = $checkUser['first_name'];
                    $toUserEmail = $checkUser->email;

                    Mail::to($toUserEmail)->send(new SendPasswordVerificationLink($data));

                    return RestResponse::success([], 'You will receive a link to reset your password.');
                } else {
                    return RestResponse::warning('No such email found.', 422);
                }
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        } catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function getResetPwdUserDetails(Request $request)
    {
        try {
            if($request['permission'] == $this->perUserAuth){
                $validate = Validator::make($request->all(), [
                    'token' => 'required',
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }
                $token = PasswordVerificationEmail::where('token', $request->token)->first();

                if (!empty($token) && $token->user()->exists()) {
                    $user = $token->user;
                    $response['reset_token'] = Crypt::encryptString($user->email);
                    $response['token'] = $request->token;
                    $response['email'] = $user->email;

                    return RestResponse::Success($response, 'Password reset details successfully retrieved.');
                }
                return RestResponse::warning('This link is expired.');
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        } catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            if($request['permission'] == $this->perUserAuth){
                $validate = Validator::make($request->all(), [
                    'email' => 'required',
                    'password' => 'required',
                    'password_confirmation' => 'required',
                    'reset_token' => 'required',
                    'token' => 'required',
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }
                $reset_token = Crypt::decryptString($request->reset_token);
                $token = PasswordVerificationEmail::where('token', $request->token)->first();
                if (!empty($token) && $token->user()->exists()) {
                    $user = $token->user;
                    if ($reset_token != $user->email) {
                        return RestResponse::warning(config('constant.SOMETHING_WENT_WRONG_ERROR'));
                    }
                    $token->user->update([
                        'password' => Hash::make($request->password)
                    ]);
                    $token->delete();
                    return RestResponse::success([], 'New password has been set successfully.');
                }
                return RestResponse::warning(config('constant.SOMETHING_WENT_WRONG_ERROR'));
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        } catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function profileResetPassword(Request $request){
        try{
            if(Auth::user()->hasPermissionTo($this->perUserAuth)){
                $validate = Validator::make($request->all(), [
                    'password' => 'required',
                    'password_confirmation' => 'required',
                    'user_id' => 'required'
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }
                $getUser = User::where('id',$request['user_id'])->first();
                if(empty($getUser)){
                    return RestResponse::warning('User not found.');
                }
                $getUser['password'] = Hash::make($request->password);
                $getUser->save();
                return RestResponse::success([], 'Password has been reset successfully.');
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
