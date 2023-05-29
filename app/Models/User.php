<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'id','first_name','last_name','email','password','phone','company_name','address','area',
        'zipcode','city','state','country','profile_photo','is_active','is_verified','role_id','created_at','updated_at'
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

   /* public function roles()
    {
        return $this->belongsToMany(Role::class, RoleUser::class, 'user_id', 'role_id');
    }*/
    public function role()
    {
        return $this->hasOne(Role::class,'id','role_id');
    }
}
