<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EwordResult extends Model
{
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
