<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function type() {
        return $this->belongsTo('App\Models\ArticleType');
    }

    public function articleAuthor() {
        return $this->belongsTo('App\Models\User', 'author');
    }

    public function collects() {
        return $this->belongsToMany('App\Models\User', 'collects', 'article_id', 'user_id');
    }

    public function comments() {
        return $this->hasMany('App\Models\Comment', 'article_id');
    }
}
