<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EconomicGroup extends Model
{
    //
    protected $table = 'EconomicGroup';
    protected $primaryKey = 'id';
    protected $fillable = ['name','description'];

    public function afps()
    {
        return $this->belongsToMany('App\Afp','AfpXEconomicGroup',
                             'economicGroupId','afpId')->using('App\AfpXEconomicGroup')->withPivot(
                               'beginDate', 'endDate','description','active');
    }

    public function investedCompanies()
    {
        return $this->belongsToMany('App/InvestedCompany','InvestedCompanyXEconomicGroup',
                             'economicGroupId','companyId')->using('App\InvestedCompanyXEconomicGroup')->withPivot(
                               'beginDate', 'endDate','description','active');
    }

}
