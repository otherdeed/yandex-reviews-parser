<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'login',
        'password',
        'url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'url' => 'string',
    ];
    
    public function getJWTIdentifier()
    {
        return $this->getKey(); 
    }
    public function getJWTCustomClaims(): array
    {
        return [
            'login' => $this->login,
        ];
    }
}