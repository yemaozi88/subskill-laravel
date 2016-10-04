<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EwordSession extends Model
{
    protected $table = 'eword_sessions';

    protected $fillable = ['with_wav', 'is_test', 'quiz_set', 'user_id'];

}
