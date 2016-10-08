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

    public static function by_date($with_wav, $is_test) {
        $results = self::selectRaw('DATE(created_at) as created_date, count(*) as count')
            ->where('with_wav', $with_wav)
            ->where('is_test', $is_test)
            ->groupBy('created_date')
            ->orderBy('created_at', 'desc')
            ->get();
        return $results;
    }
}