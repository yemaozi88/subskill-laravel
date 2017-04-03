<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDstResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dst_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('quiz_set_name');
            $table->string('user_digit_list', 500);
            $table->string('correct_digit_list', 500);
            $table->string('order_list', 300);
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
        Schema::dropIfExists('dst_results');
    }
}
