<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLstResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lst_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('quiz_set_name');
            $table->string('last_word_list', 200);
            $table->string('judgement_list', 100);
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
        Schema::dropIfExists('lst_results');
    }
}
