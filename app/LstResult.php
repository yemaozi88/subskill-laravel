<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LstResult extends Model
{
    public function user() {
        return $this->belongsTo('App\QuizUser', 'user_id');
    }
}
