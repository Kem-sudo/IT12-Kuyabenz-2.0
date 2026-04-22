<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            if (User::count() === 1) {
                $user->update(['role' => 'admin']);
            }
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public static function getAdminUser()
    {
        $admin = static::where('role', 'admin')->first();
        
        if (!$admin) {
            $admin = static::create([
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);
        }
        
        return $admin;
    }
}
