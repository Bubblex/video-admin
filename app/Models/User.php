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
}
