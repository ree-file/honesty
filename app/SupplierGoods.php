<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierGoods extends Model
{
    //
    protected $table = "supplier_goods";
    protected $fillable = ['supplier_id','goods_id','supplier_num','shipments'];
    public function supplier()
    {
      return $this->belongsTo("App\Supplier");
    }

    public function goods()
    {
      return $this->belongsTo("App\Goods");
    }
}
