<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = ['supplier_id','user_id','order_code','order_pay','order_payway','order_status'];

    protected $table = "order";

    public function user()
    {
      return $this->belongsTo("App\User");
    }
    public function supplier()
    {
      return $this->belongsTo("App\Supplier","supplier_id","id");
    }
    public function ordergoods()
    {
      return $this->hasOne('App\OrderGoods');
    }
}
