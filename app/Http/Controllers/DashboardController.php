<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $qcData = DB::table('qc')
            ->select('supplier', 'total_score', 'del_month', 'del_year')
            ->whereNotNull('total_score')
            ->get();

        $deliveryData = DB::table('delivery')
            ->select('supplierSearch', 'total_score', 'del_month', 'del_year')
            ->whereNotNull('total_score')
            ->get();

        return view('dashboard', compact('qcData', 'deliveryData'));
    }
}