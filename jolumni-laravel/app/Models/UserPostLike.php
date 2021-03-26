<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPostLike extends Model
{
    protected $table = "user_post_like";
    protected $guarded = [];
    
    use HasFactory;
}
