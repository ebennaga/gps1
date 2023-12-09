<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    public $table = 'distributors';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'distributor_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributor_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    // public function distirbutor_user()
    // {
    //     // return $this->hasMany();
    // }

    public function roles ()
    {
        return $this->belongsToMany(Role::class);
    }

    public function managers()
    {
        return $this->belongsToMany(Manager::class);
    }
}
