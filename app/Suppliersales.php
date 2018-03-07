<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Suppliersales extends Model
{
    //
    protected $fillable = ['supplier_id','goods_id','added','leave'];
    protected $table = "suppliersale";
    public function supplier()
    {
      return $this->belongsTo("App\Supplier");
    }

    public function goods()
    {
      return $this->belongsTo("App\Goods");
    }
}
