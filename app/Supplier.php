<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    //
    protected $table = "supplier";

    public function announcements()
    {
      return $this->hasMany("App\Announcement");
    }

    public function orders()
    {
      return $this->hasMany("App\Order");
    }


    public function suppliersales(){
      return $this->hasMany("App\Suppliersales");
    }

    public function supplierfavorables(){
      return $this->hasMany("App\Supplierfavorable");
    }

    public function goods()
    {
      return $this->belongsToMany("App\Goods",'supplier_goods')->withPivot(['goods_id',"discount","is_discount","shipments"]);
    }
  
}
