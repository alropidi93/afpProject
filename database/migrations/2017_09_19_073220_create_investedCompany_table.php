<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvestedCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('InvestedCompany', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',150)->unique();
            $table->string('ruc',20)->nullable();
            $table->text('description')->nullable();
            $table->string('scope',20)->nullable();
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
        Schema::dropIfExists('InvestedCompany');
    }
}
