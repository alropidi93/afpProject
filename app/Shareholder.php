<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shareholder extends Model
{
  protected $table = 'Shareholder';
  protected $primaryKey = 'id';
  protected $fillable = ['documentId','name', 'address', 'nationality', 'description'];


  public function afps()
   {
       return $this->belongsToMany('App\Afp','ShareholderXAfp',
                            'shareholderId','afpId')->using('App\ShareholderXAfp')->withPivot(
                              'description','participation','beginDate', 'endDate','serie');


   }

   public function companies()
    {
        return $this->belongsToMany('App\InvestedCompany','ShareholderXCompany',
                             'shareholderId','companyId')->using('App\ShareholderXCompany')->withPivot(
                               'description','participation','beginDate','endDate','serie');
    }
}
