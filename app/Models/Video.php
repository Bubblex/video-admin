<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function videoAuthor() {
        return $this->belongsTo('App\Models\User', 'author');
    }

    public function collects() {
        return $this->belongsToMany('App\Models\User', 'collects', 'video_id', 'user_id');
    }
}
