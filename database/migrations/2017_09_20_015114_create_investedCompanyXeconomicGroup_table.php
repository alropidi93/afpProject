<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvestedCompanyXeconomicGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('InvestedCompanyXEconomicGroup', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('companyId');
            $table->integer('economicGroupId');
            $table->date('beginDate')->nullable();;
            $table->date('endDate')->nullable();;
            $table->text('description')->nullable();;
            $table->boolean('active')->nullable();;
            $table->timestamps();

            $table->foreign('economicGroupId')->references('id')->on('EconomicGroup');
            $table->foreign('companyId')->references('id')->on('InvestedCompany');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('InvestedCompanyXEconomicGroup');
    }
}
