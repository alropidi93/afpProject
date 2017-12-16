<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Found extends Model
{
    //
    protected $table = 'Found';
    protected $primaryKey = 'id';
    protected $fillable = ['name','description', 'state', 'afpId'];

    public function afp()
    {
        return $this->belongsTo('App\Afp','afpId','id');
    }

    public function investmentRounds()
    {
        return $this->hasMany('App\InvestmentRound','foundId','id');

    }

    public function periods()
    {
      return $this->belongsToMany('App\Period','FoundXPeriod',
                           'foundId','periodId')->using('App\FoundXPeriod')->withPivot(
                               'operaciontransito');

    }
}
