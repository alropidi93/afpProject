<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFunctionaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Functionary', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('identityDocument')->unique()->nullable();
            $table->string('name',150);
            $table->string('surname',150)->nullable();
            $table->string('secondSurname',150)->nullable();
            $table->date('birthday')->nullable();
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
        Schema::dropIfExists('Functionary');
    }
}
