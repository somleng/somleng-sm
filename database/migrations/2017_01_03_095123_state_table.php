<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblstate', function (Blueprint $table) {
            $table->increments('id');
            $table->string('state');
            $table->tinyInteger('state_type')->comment('1: initial state; 2: final state; 0: (default) normal state');
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
        Schema::drop('tblstate');
    }
}
