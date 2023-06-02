<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendPasswordVerificationLink;
use App\Models\CompanySettings;
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
    public function login(Request $request)
    {
        try {
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

            $getUser = User::where('email', '=', $credentials['email'])->first();
            if ($getUser['is_verified'] == 0){
                return RestResponse::warning('An Account has already been registered, but was never verified. Please verify your account.', 422);
            }
            $user = Auth::user();
            $response['access_token'] = $user->createToken('Api Token')->accessToken;
            $response['user'] = $user;
            return RestResponse::success($response, 'Access token successfully retrieved.');
        } catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function logout()
    {
        try{
            Auth::user()->token()->delete();
            return RestResponse::success([], 'Token successfully removed.');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function forgotPasswordLinkEmail(Request $request)
    {
        try {
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
                $toUserEmail = $checkUser->email;

                Mail::to($toUserEmail)->send(new SendPasswordVerificationLink($resetPasswordLink));

                return RestResponse::success([], 'You will receive a link to reset your password to your email.');
            } else {
                return RestResponse::warning('No such email found.', 422);
            }
        } catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function getResetPwdUserDetails(Request $request)
    {
        try {
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

        } catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
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
                return RestResponse::success([], 'New password has been set successfully to User.');
            }
            return RestResponse::warning(config('constant.SOMETHING_WENT_WRONG_ERROR'));
        } catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function profileResetPassword(Request $request){
        try{
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
            return RestResponse::success([], 'Password has been reset successfully to User.');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function getCompanySettings(Request $request){
        try{
            $companyId= $request->query('company_id');
            if(!empty($companyId)){
                $getCompanySetting = CompanySettings::where('id',$companyId)->first();
                if(empty($getCompanySetting)){
                    return RestResponse::warning('Company settings not found.');
                }
                return RestResponse::success($getCompanySetting, 'Company settings retrieve successfully.');
            }else{
                return RestResponse::warning('Company id is required.');
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function updateCompanySettings(Request $request)
    {
        try{
            $validate = Validator::make($request->all(), [
                'user_id' => 'required',
                'company_id' => 'required',
                'company_name' => 'required',
                'address_line1' => 'required',
                'area' => 'required',
                'zipcode' => 'required',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
                'currency' => 'required',
            ]);
            if ($validate->fails()) {
                return RestResponse::validationError($validate->errors());
            }

            $getCompanySetting = CompanySettings::where('id',$request['company_id'])->first();
            if(empty($getCompanySetting)){
                return RestResponse::warning('User company setting not found.');
            }

            $getCompanySetting['company_name'] = $request['company_name'];
            $getCompanySetting['address_line1'] = $request['address_line1'];
            $getCompanySetting['area'] = $request['area'];
            $getCompanySetting['zipcode'] = $request['zipcode'];
            $getCompanySetting['city'] = $request['city'];
            $getCompanySetting['state'] = $request['state'];
            $getCompanySetting['country'] = $request['country'];
            $getCompanySetting['currency'] = $request['currency'];
            if(array_key_exists('company_logo',$request->all()) && !empty($request['company_logo'])){
                $logoUrl = $request->file('company_logo');
                if (!empty($logoUrl)) {
                    if (File::size($logoUrl) > 2097152) {
                        return RestResponse::warning('Company logo upto 2 Mb max.', 422);
                    }
                    $extension = $logoUrl->getClientOriginalExtension();
                    $imageName = time() . '-' . rand(0, 100) . '.' . $extension;
                    $s3 = Storage::disk('s3');
                    $filePath = 'company_logo/' . $imageName;
                    $s3->put($filePath, file_get_contents($logoUrl),'public');
                    if ($getCompanySetting->company_logo != "") {
                        $s3->delete('company_logo/' . $getCompanySetting->company_logo);
                    }
                    $getCompanySetting['company_logo'] = $imageName;
                }else {
                    return RestResponse::warning('Whoops something went wrong.');
                }
            }
            if(array_key_exists('company_favicon',$request->all()) && !empty($request['company_favicon'])){
                $faviconUrl = $request->file('company_favicon');
                if (!empty($faviconUrl)) {
                    if (File::size($faviconUrl) > 2097152) {
                        return RestResponse::warning('Company favicon upto 2 Mb max.', 422);
                    }
                    $extension = $faviconUrl->getClientOriginalExtension();
                    $imageName = time() . '-' . rand(0, 100) . '.' . $extension;
                    $s3 = Storage::disk('s3');
                    $filePath = 'company_favicon/' . $imageName;
                    $s3->put($filePath, file_get_contents($faviconUrl),'public');
                    if ($getCompanySetting->company_favicon != "") {
                        $s3->delete('company_favicon/' . $getCompanySetting->company_favicon);
                    }
                    $getCompanySetting['company_favicon'] = $imageName;
                }else {
                    return RestResponse::warning('Whoops something went wrong.');
                }
            }
            $getCompanySetting->save();
            return RestResponse::success([], 'Company settings updated successfully.');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
