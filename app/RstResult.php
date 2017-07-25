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

    public static function by_date($quiz_set_name, $group_name) {
        $results = self::selectRaw('DATE(created_at) as created_date, count(*) as count')
            ->where('quiz_set_name', $quiz_set_name)
            ->whereIn('user_id', function($query) use ($group_name) {
                $query->select('id')
                    ->from('quiz_users')
                    ->where('group_name', $group_name);
            })
            ->groupBy('created_date')
            ->orderBy('created_at', 'desc')
            ->get();
        return $results;
    }
}
