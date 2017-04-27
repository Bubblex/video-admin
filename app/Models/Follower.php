<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    //
    function userInfo() {
        return $this->belongsTo('App\Models\User');
    }
}
