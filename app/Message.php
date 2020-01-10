<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Message
 * @package App
 * @property int $id
 * @property int $room_id
 */
class Message extends Model
{
    use SoftDeletes;

    public function room()
    {
        return $this->belongsTo('App\Room');
    }

    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }
}
