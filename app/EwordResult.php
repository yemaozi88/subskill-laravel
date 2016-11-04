<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EwordResult extends Model
{
    public function session() {
        return $this->belongsTo('App\EwordSession', 'session_id');
    }

    protected $table = 'eword_results';
    protected $fillable = [
        "session_id",
        "play_count",
        "user_answer",
        "is_correct",
        "quiz_index",
        "elapsed_time",
    ];
}
