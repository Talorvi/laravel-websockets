<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;

    public function messages()
    {
        return $this->hasMany('App\Message');
    }

    public function owner()
    {
        return $this->belongsTo('App\User', 'owner_id');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_room');
    }
}
