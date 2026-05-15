<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'admin';

    protected $fillable = ['name', 'email', 'password', 'last_login_at', 'last_login_ip'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'last_login_at' => 'datetime',
        'password'      => 'hashed',
    ];
}
