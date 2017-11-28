<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShareholderXAFPTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('ShareholderXAfp', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('afpId');
          $table->integer('shareholderId');
          $table->text('description')->nullable();
          $table->double('participation')->nullable();
          $table->date('beginDate')->nullable();
          $table->date('endDate')->nullable();
          $table->timestamps();

          $table->foreign('afpId')->references('id')->on('Afp');
          $table->foreign('shareholderId')->references('id')->on('Shareholder');




      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ShareholderXAfp');
    }
}
