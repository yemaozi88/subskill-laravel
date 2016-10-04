<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEwordResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eword_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('session_id')->unsigned()->index();
            $table->integer('quiz_index');
            $table->integer('user_answer');
            $table->boolean('is_correct');
            $table->integer('play_count');
            $table->float('elapsed_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eword_results');
    }
}
