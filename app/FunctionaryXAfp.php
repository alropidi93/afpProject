<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class FunctionaryXAfp extends Pivot
{
    //
    protected $table = 'FunctionaryXAfp';
    protected $primaryKey = 'id';
    protected $fillable = ['afpId','functionaryId','beginDate',
                          'endDate','description','active','position','typePosition'];


}
