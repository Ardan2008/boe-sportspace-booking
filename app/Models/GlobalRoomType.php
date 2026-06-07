<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalRoomType extends Model
{
    protected $table = 'global_room_types';

    protected $fillable = ['name'];
}
