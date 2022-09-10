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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'surname',
        'first_name',
        'last_name',
        'email',
        'username',
        'status',
        'password',
        'is_default',
        'photo',
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
            'surname' => $details['surname'],
            'first_name' => $details['first_name'],
            'last_name' => $details['last_name'],
            'username' => $details['username'],
            'email' => $details['email'],
            'password' => Hash::make($details['password']),
        ]);

        return $user;
    }

}
