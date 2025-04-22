<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, HasRoles;
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];
    public function isAdmin()
    {
        return $this->roles()->where("name", "admin")->exists();
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, "users_role");
    }
}
