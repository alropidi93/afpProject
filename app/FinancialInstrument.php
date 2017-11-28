<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinancialInstrument extends Model
{
  protected $table = 'FinancialInstrument';
  protected $primaryKey = 'id';
  protected $fillable = ['name','description'];


  public function investmentRounds()
  {
      return $this->hasMany('App\InvestmentRound','financialinstrumentId','id');

  }
}
