<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    //
    protected $table="log";
    protected $fillable = ['supplier_id','goods_id','num','created_at','updated_at'];
    public function supplier()
    {
      return $this->belongsTo('App\Supplier');
    }
    public function goods()
    {
      return $this->belongsTo('App\Goods');
    }
}
