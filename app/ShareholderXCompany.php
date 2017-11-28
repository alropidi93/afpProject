<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShareholderXCompany extends Model
{
  protected $table = 'ShareholderXCompany';
  protected $primaryKey = 'id';
  protected $fillable = ['companyId','shareholderId',
                        'description','participation','beginDate','endDate','serie'];
}
