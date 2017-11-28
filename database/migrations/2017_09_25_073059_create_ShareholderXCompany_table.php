<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShareholderXCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('ShareholderXCompany', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('companyId');
          $table->integer('shareholderId');
          $table->text('description')->nullable();
          $table->double('participation')->nullable();
          $table->date('beginDate')->nullable();
          $table->date('endDate')->nullable();
          $table->timestamps();
          $table->foreign('companyId')->references('id')->on('InvestedCompany');
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
        Schema::dropIfExists('ShareholderXCompany');
    }
}
