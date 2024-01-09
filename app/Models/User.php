<?php

namespace App\Models;

use App\Enums\UserType;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['profile', 'name', 'email', 'password', 'last_login_at', 'last_login_ip'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function files()
    {
        return $this->belongsToMany(File::class)->orderBy('created_at', 'desc');
    }

    public function settings($name = null)
    {
        if ($name) {
            $setting = $this->hasMany(Setting::class)
                ->where('name', '=', $name)
                ->first();

            return isset($setting) ? $setting->value : null;
        } else {
            return $this->hasMany(Setting::class);
        }
    }

    public function getLastLoginAtAttribute()
    {
        // return $this->attributes['last_login_at'] != null
        //     ? Carbon::parse($this->attributes['last_login_at'])->format('d/m/Y \à\s H:i:s')
        //     : null;

        return Carbon::parse($this->attributes['last_login_at'])->format('d/m/Y \à\s H:i:s') ?? null;
    }

    public function getRegistration()
    {
        // return $this->attributes['last_login_at'] != null
        //     ? Carbon::parse($this->attributes['last_login_at'])->format('d/m/Y \à\s H:i:s')
        //     : null;

        return Carbon::parse($this->attributes['created_at'])->format('d/m/Y \à\s H:i:s') ?? null;
    }

    public function getIdFromProfilePicture()
    {
        $image = $this->files()
            ->where('highlight', '=', 1)
            ->first();

        return isset($image) ? $image->id : null;
    }

    public function getProfileDescriptionAttribute()
    {
        return UserType::getDescription($this->profile);
    }

    public function getProfilePicture()
    {
        $image = $this->files()
            ->where('highlight', '=', 1)
            ->first();

        return isset($image) ? asset('storage/' . $image->name) : asset('assets/images/user-default.png');
    }

    public function getProfileAlias()
    {
        switch ($this->profile) {
            case 0:
                return 'Administrador';

            case 1:
                return 'Institutuion';

            case 2:
                return 'User';

            default:
                return 'Undefined';
        }
    }
}
