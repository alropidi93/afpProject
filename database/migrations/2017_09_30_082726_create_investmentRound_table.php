<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvestmentRoundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('InvestmentRound', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('companyId');
            $table->integer('foundId');
            $table->integer('currencyId');
            $table->integer('financialinstrumentId');
            $table->integer('month');
            $table->integer('year');
            $table->double('mount')->nullable();
            $table->double('mountPercent')->nullable();

            $table->foreign('companyId')->references('id')->on('InvestedCompany');
            $table->foreign('foundId')->references('id')->on('Found');
            $table->foreign('currencyId')->references('id')->on('Currency');
            $table->foreign('financialinstrumentId')->references('id')->on('FinancialInstrument');




        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('InvestmentRound');
    }
}
