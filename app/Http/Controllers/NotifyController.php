<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notify;
class NotifyController extends Controller
{
    //
    public function store(Request $request)
    {
      $notify = new Notify;
      $notify->content = $request->content;
      $notify->operator = $request->operator;
      $notify->delete = 0;
      $notify->save();
      return $this->success($notify->id);
    }
}
