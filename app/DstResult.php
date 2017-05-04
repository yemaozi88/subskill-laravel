<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DstResult extends Model
{
    public function user() {
        return $this->belongsTo('App\QuizUser', 'user_id');
    }

    protected $fillable = [
        'user_id',
        'quiz_set_name',
        'correct_num',
        'question_num',
        'user_digit_list',
        'correct_digit_list',
        'order_list',
    ];
}