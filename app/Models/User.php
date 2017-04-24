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
}
