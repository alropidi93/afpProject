<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFunctionaryXCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('FunctionaryXCompany', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('companyId');
            $table->integer('functionaryId');
            $table->string('position',150)->nullable();
            $table->date('beginDate')->nullable();
            $table->date('endDate')->nullable();
            $table->text('description')->nullable();
            $table->boolean('active')->nullable();
            $table->timestamps();



            $table->foreign('companyId')->references('id')->on('InvestedCompany');
            $table->foreign('functionaryId')->references('id')->on('Functionary');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('FunctionaryXCompany');
    }
}
