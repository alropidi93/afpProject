<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFunctionaryXAfpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('FunctionaryXAfp', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('afpId');
            $table->integer('functionaryId');
            $table->string('position',150)->nullable();
            $table->date('beginDate')->nullable();
            $table->date('endDate')->nullable();
            $table->text('description')->nullable();
            $table->boolean('active')->nullable();
            $table->timestamps();

            $table->foreign('functionaryId')->references('id')->on('Functionary');
            $table->foreign('afpId')->references('id')->on('Afp');




        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('FunctionaryXAfp');
    }
}
