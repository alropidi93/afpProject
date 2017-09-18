<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Afp extends Model
{
    //

  //use SoftDeletes;
  protected $table = 'Afp';
  protected $primaryKey = 'id';
  protected $fillable = ['name', 'ruc', 'description', 'economicGroupId'];
  public function EconomicGroup(){
    return $this->belongsTo('App\EconomicGroup', 'economicGroupId');
  }


}
