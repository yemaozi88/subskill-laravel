<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizUser extends Model
{
    protected $table = 'quiz_users';

    public function eword_sessions() {
        return $this->hasMany('App\EwordSession', 'user_id');
    }
}
