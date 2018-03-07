<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplierfavorable extends Model
{
    //
    protected $table = "supplierfavorable";

    public function supplier()
    {
      return $this->belongsTo("App\Supplier");
    }
}
