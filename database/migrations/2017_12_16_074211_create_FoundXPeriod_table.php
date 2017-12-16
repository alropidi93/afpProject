<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoundXPeriodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('FoundXPeriod', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('foundId');
            $table->integer('periodId');
            $table->double('operaciontransito')->nullable();

            $table->foreign('foundId')->references('id')->on('Found');
            $table->foreign('periodId')->references('id')->on('Period');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('FoundXPeriod');
    }
}
