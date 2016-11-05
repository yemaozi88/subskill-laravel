<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class EwordSession extends Model
{
    protected $table = 'eword_sessions';

    protected $fillable = ['with_wav', 'is_test', 'quiz_set', 'user_id'];

    public function user() {
        return $this->belongsTo('App\QuizUser', 'user_id');
    }

    public static function by_date($with_wav, $is_test, $group_name) {
        $results = self::selectRaw('DATE(created_at) as created_date, count(*) as count')
            ->where('with_wav', $with_wav)
            ->where('is_test', $is_test)
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