<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAfpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('Afp', function (Blueprint $table) {
          $table->increments('id');
          $table->string('name',150)->unique();
          $table->text('description')->nullable();
          $table->string('ruc',20)->unique()->nullable();




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
        //
          Schema::dropIfExists('Afp');
    }
}
