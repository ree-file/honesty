<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Shippers;

class LoginController extends Controller
{
    //
    public function index(Request $request)
    {
      $shipper = Shippers::with('supplier')->where('name',$request->name)->get();
      if ($shipper->isEmpty()) {
        return $this->success('false');
      }
      if ($shipper[0]['password'] == $request->password) {
        return $this->success(['supplier_id'=>$shipper[0]['charge_supplier'],
        'supplier_name'=>$shipper[0]['supplier']['supplier_name']]);
      }
      else {
        return $this->success('false');
      }
    }
}
