<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OnlineChangesController extends Controller
{
    public function index(Request $request)
    {
        $onlineCharges = Payment::where('is_complete',1)->get();
        return view('admin.onlinecharges.index', compact('onlineCharges'));
    }
}
