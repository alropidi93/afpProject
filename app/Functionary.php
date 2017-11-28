<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Functionary extends Model
{
  protected $table = 'Functionary';
  protected $primaryKey = 'id';
  protected $fillable = ['identityDocument','name','surname','secondSurname',
                      'birthday','age','biography'];

  public function afps()
  {
      return $this->belongsToMany('App\Afp','FunctionaryXAfp',
                           'functionaryId','afpId')->using('App\FunctionaryXAfp')->withPivot(
                               'beginDate', 'endDate','description','active','position','typePosition');
  }

  public function companies()
  {
      return $this->belongsToMany('App/InvestedCompany','FunctionaryXCompany',
                           'functionaryId','companyId')->using('App\FunctionaryXCompany')->withPivot(
                               'beginDate', 'endDate','description','active','position','typePosition');
  }
}
