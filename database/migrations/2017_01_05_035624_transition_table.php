<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransitionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbltransition', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('state_id');
            $table->string('input')->nullable();
            $table->string('callflow_id');
            $table->text('twilml')->nullable();
            $table->text('path')->nullable();
            $table->text('action')->nullable();
            $table->tinyInteger('new_state')->nullable();
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
        Schema::drop('tbltransition');
    }
}
