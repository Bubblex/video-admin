<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public function type() {
        return $this->belongsTo('App\Models\ArticleType');
    }

    public function author() {
        return $this->belongsTo('App\Models\Users', 'user_id');
    }
}
