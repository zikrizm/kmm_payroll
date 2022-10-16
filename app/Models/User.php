<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    protected $appens = [
        'statusbox',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

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

    /**
     * Get the business that owns the user.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Creates a new user based on the input provided.
     *
     * @return object
     */
    public static function create_user($details)
    {
        $user = User::create([
            'name' => $details['name'],
            'username' => $details['username'],
            'email' => $details['email'],
            'is_default' => $details['is_default'],
            'password' => Hash::make($details['password']),
        ]);

        return $user;
    }

    public function getStatusboxAttribute()
    {
        $is_active = $this->status == 'active';
        return '<div class="flex items-center gap-1 rounded-xl px-2.5 py-0.5 w-max ' . (($is_active) ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700') .'"'.'>
            <span class="w-1.5 h-1.5 rounded-full ' . (($is_active) ? 'bg-green-700' : 'bg-red-700') . ' block"></span>
            <p class="text-xs font-normal flex items-center gap-1 capitalize text-green-600">
                ' . $this->status . '
            </p>
        </div>';
    }
}
