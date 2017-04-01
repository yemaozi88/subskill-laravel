<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRstResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rst_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('quiz_set_name');
            $table->string('last_word_list', 500);
            $table->string('judgement_list', 300);
            $table->integer('correct_num');
            $table->integer('question_num');
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
        Schema::dropIfExists('rst_results');
    }
}
