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

    public function followers() {
        return $this->hasMany('App\Models\Follower', 'star');
    }

    public function stars() {
        return $this->hasMany('App\Models\Follower', 'follower');
    }

    public function userStars() {
        return $this->belongsToMany('App\Models\User', 'followers', 'follower', 'star')->withTimestamps();
    }

    public function userFollowers() {
        return $this->belongsToMany('App\Models\User', 'followers', 'star', 'follower')->withTimestamps();
    }

    public function userArticles() {
        return $this->hasMany('App\Models\Article', 'author');
    }

    public function userVideos() {
        return $this->hasMany('App\Models\Video', 'author');
    }

    public function collectArticles() {
        return $this->belongsToMany('App\Models\Article', 'collects', 'user_id', 'article_id')->withPivot('id');
    }

    public function collectVideos() {
        return $this->belongsToMany('App\Models\Video', 'collects', 'user_id', 'video_id')->withPivot('id');
    }

    public function messages() {
        return $this->hasMany('App\Models\Message');
    }
}
