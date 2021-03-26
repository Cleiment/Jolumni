<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    protected $table = "users";
    protected $primaryKey = "user_id";
    protected $keyType = "string";
    protected $guarded = [];
    protected $hidden = ['password', 'remember_token'];

    use HasApiTokens, HasFactory, Notifiable;
}
