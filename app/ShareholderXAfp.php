<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShareholderXAfp extends Model
{
  protected $table = 'ShareholderXAfp';
  protected $primaryKey = 'id';
  protected $fillable = ['afpId','shareholderId',
                        'description','participation','beginDate','endDate','serie'];
}
