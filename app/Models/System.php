<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    //
    protected $fillable = ['user_id', 'name', 'description', 'url', 'developed_at'];

// Add this to get the single most recent update
    public function latestUpdate()
    {
        return $this->hasOne(SystemUpdate::class)->latestOfMany();
    }

    public function updates() { return $this->hasMany(SystemUpdate::class); }
    public function user() { return $this->belongsTo(User::class); }
}
