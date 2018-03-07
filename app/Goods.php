<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    //
    protected $table = "goods";

    public function suppliergoods()
    {
      return $this->hasMany('App\SupplierGoods');
    }

    public function suppliersales()
    {
      return $this->hasMany('App\Suppliersales');
    }

    public function goodscategory()
    {
      return $this->belongsTo('App\Goodscategory',"category_id");
    }

    public function suppliers()
    {
      return $this->belongsToMany("App\Supplier","supplier_goods");
    }
}
