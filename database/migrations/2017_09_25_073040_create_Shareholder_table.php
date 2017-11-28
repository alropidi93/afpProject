<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShareholderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('Shareholder', function (Blueprint $table) {
          $table->increments('id');
          $table->string('documentId',20)->nullable();
          $table->string('name',50);
          $table->string('address',100)->nullable();
          $table->string('nationality',20)->nullable();
          $table->text('description')->nullable();
          $table->integer('age')->nullable();
          $table->text('biography')->nullable();
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
        Schema::dropIfExists('Shareholder');
    }
}
