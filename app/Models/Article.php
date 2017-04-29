<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public function type() {
        return $this->belongsTo('App\Models\ArticleType');
    }

    public function articleAuthor() {
        return $this->belongsTo('App\Models\User', 'author');
    }

    public function comments() {
        return $this->hasMany('App\Models\Comment', 'article_id');
    }
}
