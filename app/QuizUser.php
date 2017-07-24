<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizUser extends Model
{
    protected $table = 'quiz_users';
    protected $fillable = [
        'username',
        'group_name',
    ];

    public function eword_sessions() {
        return $this->hasMany('App\EwordSession', 'user_id');
    }

    public function lst_results() {
        return $this->hasMany('App\LstResult', 'user_id');
    }
}
