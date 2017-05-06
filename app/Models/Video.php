<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    public function videoAuthor() {
        return $this->belongsTo('App\Models\User', 'author');
    }

    public function collects() {
        return $this->belongsToMany('App\Models\User', 'collects', 'video_id', 'user_id');
    }
}
