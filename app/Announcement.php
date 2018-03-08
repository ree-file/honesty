<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    //
    protected $table = "announcement";

    public function suppliers(){
      return $this->belongsTo("App\Supplier",'supplier_id');
    }
}
