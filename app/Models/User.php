<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable , HasRoles, HasPermissions;

    protected $fillable = [
        'id','first_name','last_name','email','password','phone','company_name','address','area',
        'zipcode','city','state','country','profile_photo','is_active','is_verified','role_id','created_at','updated_at','timezone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getProfilePhotoAttribute(){
        $imagePath = null;
        if(!empty($this->attributes['profile_photo'])){
            $imagePath = config('app.image_path')."user_profile/".$this->attributes['profile_photo'];
        }
        return $imagePath;
    }

   /* public function roles()
    {
        return $this->belongsToMany(Role::class, RoleUser::class, 'user_id', 'role_id');
    }*/
    public function role()
    {
        return $this->hasOne(Role::class,'id','role_id');
    }
}
