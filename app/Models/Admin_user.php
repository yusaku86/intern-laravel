<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Admin_user extends Authenticatable
{
    protected $fillable = ['email', 'authority', 'password','google_id', 'twitter_id', 'github_id'];

    use HasFactory;
}
