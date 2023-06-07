<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Mail\UserCreateSendPassword;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use App\Traits\RolePermissionTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Storage;
use RestResponse;
use Validator;
use File;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use RolePermissionTrait;

    private $userRepository;
    private $perUserCRUD;
    private $perUserProfile;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->perUserCRUD = config('constant.PERMISSION_USER_CRUD');
        $this->perUserProfile = config('constant.PERMISSION_USER_PROFILE');
    }

    public function index(Request $request)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perUserCRUD)){
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
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        } catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function store(Request $request)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perUserCRUD)){
                DB::beginTransaction();
                $validate = Validator::make($request->all(), [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|email',
                    'password' => 'required|min:6|confirmed',
                    'password_confirmation' => 'required',
                    'phone' => 'required|unique:users|min:3|max:15',
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
                        if (File::size($photo) > 2097152) {
                            return RestResponse::warning('Profile Image upto 2 Mb max.', 422);
                        }
                        $ext = $photo->getClientOriginalExtension();
                        if (!in_array(strtolower($ext), array("png", "jpeg", "jpg", "gif", "svg"))) {
                            return RestResponse::warning('Profile Image must be a PNG, JPEG, GIF, SVG file.', 422);
                        }
                        //$fileName = $photo->getClientOriginalName();
                        $imageName = time() . '-' . rand(0, 100) . '.' . $photo->getClientOriginalExtension();
                        $filePath = 'user_profile/' . $imageName;
                        Storage::disk('s3')->put($filePath, file_get_contents($photo),'public');
                        $createUser['profile_photo'] = $imageName;
                    }
                }
                $createUser['is_active'] = 1;
                $createUser['is_verified'] = 1;
                $createUser['role_id'] = $request['role_id'];
                $storeUser = $this->userRepository->storeUser($createUser);
                if(!$storeUser){
                    return RestResponse::warning('User create failed.');
                }
                //Assign role to user.
                $this->assignRoleToUser($storeUser['id'],$request['role_id']);

                DB::commit();
                $mailData['first_name'] = $request['first_name'];
                $mailData['last_name'] = $request['last_name'];
                $mailData['password'] = $request['password'];
                Mail::to($request['email'])->send(new UserCreateSendPassword($mailData));

                return RestResponse::Success([],'User created successfully.');
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function show($id)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perUserCRUD)){
                if(empty($id)){
                    return RestResponse::warning('User id not found. Must pass in URL.');
                }
                $getUser = $this->userRepository->findUserWithRole($id);
                if(empty($getUser)){
                    return RestResponse::warning('User not found.');
                }
                return RestResponse::Success($getUser,'User retrieve successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function update(Request $request,$id)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perUserCRUD)){
                DB::beginTransaction();
                $validate = Validator::make($request->all(), [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|email',
                    'phone' => 'required|min:3|max:15|unique:users,phone,'.$id,
                    'role_id' => 'required',
                    'is_active' => 'required',
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }

                $user = $this->userRepository->findUser($id);
                if (empty($user)) {
                    return RestResponse::warning('Users not found.');
                }

                if ($request->file('profile_photo') != "") {
                    $images = $request->file('profile_photo');

                    if (!empty($images)) {
                        if (File::size($images) > 2097152) {
                            return RestResponse::warning('Profile Image upto 2 Mb max.', 422);
                        }
                        $ext = $images->getClientOriginalExtension();
                        if (!in_array(strtolower($ext), array("png", "jpeg", "jpg", "gif", "svg"))) {
                            return RestResponse::warning('Profile Image must be a PNG, JPEG, GIF, SVG file.', 422);
                        }
                    }

                    $imageName = time() . '-' . rand(0, 100) . '.' . $images->getClientOriginalExtension();
                    $s3 = Storage::disk('s3');
                    $filePath = 'user_profile/' . $imageName;
                    $s3->put($filePath, file_get_contents($images),'public');
                    $profileImage = $imageName;
                    if ($user->profile_photo != "") {
                        $s3->delete('user_profile/' . $user->profile_photo);
                    }
                    $updateData['profile_photo'] = $profileImage;
                }

                //remove existing user role.
                $this->removeExistingRole($user['id']);

                //assign new role
                $this->assignRoleToUser($user['id'],$request['role_id']);

                $updateData['first_name'] = $request['first_name'];
                $updateData['last_name'] = $request['last_name'];
                $updateData['phone'] = $request['phone'];
                $updateData['email'] = $request['email'];
                $updateData['role_id'] = $request['role_id'];
                $updateData['is_active'] = $request['is_active'];
                $updateUser = $this->userRepository->updateUser($updateData,$id);
                if(!$updateUser){
                    return RestResponse::warning('User update failed.');
                }
                DB::commit();
                return RestResponse::Success([],'User updated successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function destroy($id)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perUserCRUD)){
                $user = $this->userRepository->findUser($id);
                if (empty($user)) {
                    return RestResponse::warning('User not found.');
                }
                $user->delete();
                return RestResponse::Success([],'User deleted successfully.');
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function getRoles()
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perUserCRUD)){
                $roles = Role::all();
                if (count($roles) < 0) {
                    return RestResponse::warning('Roles not found.');
                }
                return RestResponse::Success($roles,'Roles retrieve successfully.');
            } else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function searchUser(Request $request){
        try{
            if(Auth::user()->hasPermissionTo($this->perUserCRUD)){
                $validate = Validator::make($request->all(), [
                    'search_key' => 'required'
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }
                $limit = isset($request->total_record) ? $request->total_record : config('constant.PAGINATION_RECORD');
                $searchUser = User::where(function ($qry) use($request){
                    $qry->orWhere('first_name', 'LIKE', '%' . $request->search_key . '%');
                    $qry->orWhere('last_name', 'LIKE', '%' . $request->search_key . '%');
                    $qry->orWhere('phone', 'LIKE', '%' . $request->search_key . '%');
                    $qry->orWhere('email', 'LIKE', '%' . $request->search_key . '%');
                })->paginate($limit);
                if(count($searchUser) < 0){
                    return RestResponse::warning('No any search result found.');
                }
                return RestResponse::Success($searchUser,'User search successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function updateUserProfile(Request $request, $userId)
    {
        try{
            if(Auth::user()->hasPermissionTo($this->perUserProfile)){
                $validate = Validator::make($request->all(), [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|email',
                    'phone' => 'required|min:3|max:15|unique:users,phone,'.$userId,
                    'address' => 'required',
                    'area' => 'required',
                    'city' => 'required',
                    'state' => 'required',
                    'country' => 'required'
                ]);
                if ($validate->fails()) {
                    return RestResponse::validationError($validate->errors());
                }
                $getUser = $this->userRepository->findUser($userId);
                if (empty($getUser)) {
                    return RestResponse::warning('User not found.');
                }
                $updateData = $request->except('profile_photo');
                if(array_key_exists('profile_photo',$request->all())){
                    $profilePhoto = $request->file('profile_photo');

                    if (!empty($profilePhoto)) {
                        if (File::size($profilePhoto) > 2097152) {
                            return RestResponse::warning('Profile Image upto 2 Mb max.', 422);
                        }
                        $ext = $profilePhoto->getClientOriginalExtension();
                        if (!in_array(strtolower($ext), array("png", "jpeg", "jpg", "gif", "svg"))) {
                            return RestResponse::warning('Profile Image must be a PNG, JPEG, GIF, SVG file.', 422);
                        }
                    }

                    $imageName = time() . '-' . rand(0, 100) . '.' . $profilePhoto->getClientOriginalExtension();
                    $s3 = Storage::disk('s3');
                    $filePath = 'user_profile/' . $imageName;
                    $s3->put($filePath, file_get_contents($profilePhoto),'public');
                    if ($getUser->profile_photo != "") {
                        $s3->delete('user_profile/' . $getUser->profile_photo);
                    }
                    $updateData['profile_photo'] = $imageName;
                }
                User::where('id',$userId)->update($updateData);
                return RestResponse::Success([],'User updated successfully.');
            }else {
                return RestResponse::warning(config('constant.USER_DONT_HAVE_PERMISSION'));
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
