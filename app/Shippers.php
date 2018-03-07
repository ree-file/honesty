<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shippers extends Model
{
    //
    protected $table = 'shippers';
    public function supplier()
    {
      return $this->hasOne('App\Supplier','id','charge_supplier');
    }
}
