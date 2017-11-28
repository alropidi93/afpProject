<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class FunctionaryXCompany extends Pivot
{
    //
    protected $table = 'FunctionaryXCompany';
    protected $primaryKey = 'id';
    protected $fillable = ['companyId','functionaryId','beginDate',
                          'endDate','description','active','position','typePosition'];
}
