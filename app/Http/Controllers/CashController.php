<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CashController extends Controller
{
    public function getView(Request $req)
    {
        return view('cashflow');
    }
}
