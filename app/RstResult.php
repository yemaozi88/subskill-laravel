<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RstResult extends Model
{
    public function user() {
        return $this->belongsTo('App\QuizUser', 'user_id');
    }

    protected $fillable = [
        'user_id',
        'quiz_set_name',
        'correct_num',
        'question_num',
        'last_word_list',
        'judgement_list',
    ];
}
