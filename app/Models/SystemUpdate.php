<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemUpdate extends Model
{
    //
    protected $fillable = ['user_id', 'system_id', 'version', 'title', 'description', 'type'];

    public function system() { return $this->belongsTo(System::class); }
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
}
