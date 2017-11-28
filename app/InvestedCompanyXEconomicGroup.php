<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class InvestedCompanyXEconomicGroup extends Pivot
{
    //
    protected $table = 'InvestedCompanyXEconomicGroup';
    protected $primaryKey = 'id';
    protected $fillable = ['companyId','economicGroupId','beginDate',
                          'endDate','description','active'];
}
