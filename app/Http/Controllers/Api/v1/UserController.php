<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Mail\UserCreateSendPassword;
use App\Models\RoleUser;
use App\Models\user;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Storage;
use RestResponse;
use Validator;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $filters = [
                'total_record' => $request->total_record,
                'order_by' => $request->order_by,
                'sort_value' => $request->sort_value
            ];
            $getAllUser = $this->userRepository->getUserWithRole($filters);
            if(empty($getAllUser)){
                return RestResponse::warning('User not found.');
            }
            return RestResponse::Success($getAllUser, 'Users retrieve successfully.');
        } catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $validate = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required',
                'phone' => 'required|unique:users|min:3|max:10',
                'role_id' => 'required',
            ]);
            if ($validate->fails()) {
                return RestResponse::validationError($validate->errors());
            }

            $createUser['first_name'] = $request['first_name'];
            $createUser['last_name'] = $request['last_name'];
            $createUser['email'] = $request['email'];
            $createUser['phone'] = $request['phone'];
            $createUser['password'] = app('hash')->make($request['password']);
            if(array_key_exists('profile_photo',$request->all())){
                if ($request->hasFile('profile_photo')) {
                    $photo = $request->file('profile_photo');
                    $fileName = $photo->getClientOriginalName();
                    $createUser['profile_photo'] = $fileName;
                    $filePath = 'profile_photo/' . $fileName;
                    Storage::disk('s3')->put($filePath, file_get_contents($photo));
                }
            }
            $createUser['is_active'] = 1;
            $createUser['is_verified'] = 1;
            $createUser['role_id'] = $request['role_id'];
            //$storeUser = User::create($createUser);
            $storeUser = $this->userRepository->storeUser($createUser);
            if(!$storeUser){
                return RestResponse::warning('User create failed.');
            }
            /*$assignRole = RoleUser::create([
               'role_id' => $request['role_id'],
               'user_id' => $storeUser['id']
            ]);*/
            DB::commit();
            $mailData['first_name'] = $request['first_name'];
            $mailData['last_name'] = $request['last_name'];
            $mailData['password'] = $request['password'];
            Mail::to($request['email'])->send(new UserCreateSendPassword($mailData));

            return RestResponse::Success('User created successfully.');
        }catch (\Exception $e) {
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            if(empty($id)){
                return RestResponse::warning('User id not found.Must pass in URL.');
            }
            $getUser = $this->userRepository->findUser($id);
            if(empty($getUser)){
                return RestResponse::warning('User not found.');
            }
            return RestResponse::Success($getUser,'User retrieve successfully.');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        try{
            DB::beginTransaction();
            info($request);
            info($id);
            $validate = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'phone' => 'required|min:3|max:10|unique:users,phone,'.$id,
                'role_id' => 'required',
                'is_active' => 'required',
            ]);
            if ($validate->fails()) {
                return RestResponse::validationError($validate->errors());
            }

            $updateUser = $this->userRepository->updateUser($request->all(),$id);

            //Update User Role
            $updateRole =
            DB::commit();
            return RestResponse::Success('User created successfully.');
        }catch (\Exception $e) {
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(user $user)
    {
        //
    }
}
