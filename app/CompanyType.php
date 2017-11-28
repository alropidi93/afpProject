<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyType extends Model
{
  protected $table = 'CompanyType';
  protected $primaryKey = 'id';
  protected $fillable = ['name','description'];

  public function companies()
  {
      return $this->hasMany('App\InvestmentRound','companyTypeId','id');

  }
}
