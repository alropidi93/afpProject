<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
class AfpXEconomicGroup extends Pivot
{
    //
    protected $table = 'AfpXEconomicGroup';
    protected $primaryKey = 'id';
    protected $fillable = ['afpId','economicGroupId','beginDate',
                          'endDate','description','active'];
}
