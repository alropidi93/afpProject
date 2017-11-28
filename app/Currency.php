<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
  protected $table = 'Currency';
  protected $primaryKey = 'id';
  protected $fillable = ['name','description'];

  public function investmentRounds()
  {
      return $this->hasMany('App\InvestmentRound','currencyId','id');

  }
}
