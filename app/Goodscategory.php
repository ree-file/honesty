<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Goodscategory extends Model
{
    //
    protected $table = "goodscategory";
    public function goods()
    {
        return $this->hasMany("App\Goods","category_id");
    }
}
