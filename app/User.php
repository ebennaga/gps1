<?php
namespace App;

use Carbon\Carbon;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    public $table = 'users';

    public static $searchable = [
        'name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    const TIMEZONE_SELECT = [
        'UTC-12'        => 'UTC-12',
        'UTC-11'       => 'UTC-11',
        'UTC-10'        => 'UTC-10',
        'UTC-09'        => 'UTC-09',
        'UTC-08'       => 'UTC-08',
        'UTC-07'        => 'UTC-07',
        'UTC-06'        => 'UTC-06',
        'UTC-05'       => 'UTC-05',
        'UTC-04'        => 'UTC-04',
        'UTC-03'        => 'UTC-03',
        'UTC-02'       => 'UTC-02',
        'UTC-01'        => 'UTC-01',
        'UTC'        => 'UTC',
        'UTC+01'       => 'UTC+01',
        'UTC+02'        => 'UTC+02',
        'UTC+03'        => 'UTC+03',
        'UTC+04'       => 'UTC+04',
        'UTC+05'        => 'UTC+05',
        'UTC+06'        => 'UTC+06',
        'UTC+07'       => 'UTC+07',
        'UTC+08'        => 'UTC+08',
        'UTC+09'        => 'UTC+09',
        'UTC+10'       => 'UTC+10',
        'UTC+11'        => 'UTC+11',
        'UTC+12'        => 'UTC+12',
    ];

    protected $fillable = [
        'name',
        'username',
        'password',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'timezone',
    ];

    public function managerManagers()
    {
        return $this->hasMany(Manager::class, 'manager_id', 'id');
    }

    public function userManagers()
    {
        return $this->belongsToMany(Manager::class);
    }

    public function email ()
    {
        return $this->hasMany(EmailUser::class);
    }

    public function setPasswordAttribute ($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function roles ()
    {
        return $this->belongsToMany(Role::class);
    }

    public function terminals()
    {
        return $this->belongsToMany(Terminal::class);
    }

    public function ships()
    {
        return $this->belongsToMany(Ship::class);
    }

}
