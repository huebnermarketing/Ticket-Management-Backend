<?php

namespace App\Traits;
use App\Repositories\User\UserRepositoryInterface;
use Spatie\Permission\Models\Role;

trait RolePermissionTrait
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserRole($id)
    {
       return Role::find($id);
    }

    public function assignRoleToUser($userId,$roleId){
        $getRole = $this->getUserRole($roleId);
        info('$getRole---');
        info($getRole);
        $getUser = $this->userRepository->findUser($userId);
        info('$getUser---');
        info($getUser);
        return $getUser->assignRole($getRole['name']);
    }
}
