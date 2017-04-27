<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //
    protected $fillable = ['account', 'nickname', 'password'];

    public function role() {
        return $this->belongsTo('App\Models\Role');
    }

    public function videos() {
        return $this->hasMany('App\Models\Video', 'author');
    }

    public function articles() {
        return $this->hasMany('App\Models\Article', 'author');
    }

    public function stars() {
        return $this->hasMany('App\Models\Follower', 'star');
    }

    public function followers() {
        return $this->hasMany('App\Models\Follower', 'follower');
    }

    public function userStars() {
        return $this->belongsToMany('App\Models\User', 'followers', 'follower', 'star')->withTimestamps();
    }
}
