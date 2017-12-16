<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvestmentRound extends Model
{
  protected $table = 'InvestmentRound';
  protected $primaryKey = 'id';
  protected $fillable = ['companyId', 'foundId', 'currencyId',
  'financialinstrumentId', 'month','year','mount','mountPercent','orden_periodo','quantityinstrument'];

 protected $appends = ['year_month'];

 /*
 Accessor for the total price
  */
 public function getYearMonthAttribute()
 {
     return $this->year *100+ $this->month;
 }



  public function afp()
  {
      return $this->belongsTo('App\Afp','afpId','id');
  }

  public function financialInstrument()
  {
      return $this->belongsTo('App\FinancialInstrument','financialinstrumentId','id');
  }

  public function found()
  {
      return $this->belongsTo('App\Found','foundId','id');
  }

  public function currency()
  {
      return $this->belongsTo('App\Currency','currencyId','id');
  }



}
