<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bank;


class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::all();
        return response()->json([
            'status' => 'success',
            'message' => 'return all banks successfully',
            'data' => $banks,
            'action' => 'index'
        ]);
    }
}
