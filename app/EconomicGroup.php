<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EconomicGroup extends Model
{
    //
    protected $table = 'EconomicGroup';
    protected $primaryKey = 'id';
    protected $fillable = ['name','description'];

    
}
