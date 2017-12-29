<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FoundXPeriod extends Model
{
  protected $table = 'FoundXPeriod';
  protected $primaryKey = 'id';
  protected $fillable = ['foundId','periodId','operaciontransito','accionestotalesnacionales',
                        'bonostotalesnacionales'];
}
