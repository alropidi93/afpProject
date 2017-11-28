<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Afp extends Model
{
    //

  //use SoftDeletes;
  protected $table = 'Afp';
  protected $primaryKey = 'id';
  protected $fillable = ['name', 'ruc', 'description'];
  protected $appends = ['begin_date','final_date'];
  public function getBeginDateAttribute()
  {
    
      return $this->year *100+ $this->month;
  }
  public function getFinalDateAttribute()
  {
      return $this->year *100+ $this->month;
  }

  public function economicGroups()
   {
       return $this->belongsToMany('App\EconomicGroup','AfpXEconomicGroup',
                            'afpId','economicGroupId')->using('App\AfpXEconomicGroup')->withPivot(
                              'beginDate', 'endDate','description','active');
   }


   public function functionaries()
    {
        return $this->belongsToMany('App\Functionary','FunctionaryXAfp',
                             'afpId','functionaryId')->using('App\FunctionaryXAfp')->withPivot(
                                 'beginDate', 'endDate','description','active','position','typePosition');
    }

    public function shareholders()
     {
         return $this->belongsToMany('App\Shareholder','ShareholderXAfp',
                              'afpId','shareholderId')->using('App\ShareholderXAfp')->withPivot(
                                'description','participation','beginDate', 'endDate','serie');
     }

    public function founds()
    {
        return $this->hasMany('App\Found','afpId','id');

    }

}
