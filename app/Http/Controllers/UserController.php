<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function checkuser(Request $request)
    {
      return $this->success($request);
    }
}
