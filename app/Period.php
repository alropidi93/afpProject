<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
  protected $table = 'Period';
  protected $primaryKey = 'id';
  protected $fillable = ['month','year'];

  public function founds()
  {
    return $this->belongsToMany('App\Found','FoundXPeriod',
                         'periodId','foundId')->using('App\FoundXPeriod')->withPivot(
                             'operaciontransito');

  }
}
