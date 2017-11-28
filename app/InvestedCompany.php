<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvestedCompany extends Model
{
  protected $table = 'InvestedCompany';
  protected $primaryKey = 'id';
  protected $fillable = ['name', 'ruc', 'description', 'scope','companyTypeId'];
  public function economicGroups()
   {
       return $this->belongsToMany('App\EconomicGroup','InvestedCompanyXEconomicGroup',
                            'companyId','economicGroupId')->using('App\InvestedCompanyXEconomicGroup')->withPivot(
                              'beginDate', 'endDate','description','active');
   }

   public function functionaries()
   {
       return $this->belongsToMany('App\Functionary','FunctionaryXCompany',
                            'companyId','functionaryId')->using('App\FunctionaryXCompany')->withPivot(
                                'beginDate', 'endDate','description','active','position','typePosition');
   }

   public function shareholders()
    {
        return $this->belongsToMany('App\InvestedCompany','ShareholderXCompany',
                             'shareholderId','companyId')->using('App\ShareholderXCompany')->withPivot(
                               'description','participation','beginDate', 'endDate','serie');
    }

    public function investmentRounds()
    {
        return $this->hasMany('App\InvestmentRound','companyId','id');

    }

    public function companyType()
    {
        return $this->belongsTo('App\CompanyType','companyTypeId','id');
    }

}
