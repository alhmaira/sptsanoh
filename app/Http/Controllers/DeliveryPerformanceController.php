<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Delivery;

class DeliveryPerformanceController extends Controller
{

    public function index(Request $request)
    {
        return view('delivery-performance');
    }


    public function getSupplierByPeriod(Request $request)
    {
        $month = $request->month;
        $year  = $request->year;


        try {

            $response = Http::withoutVerifying()
            ->timeout(30)
            ->get(
                'https://be-ams.sanohindonesia.co.id/api/public/delivery-performance/',
                [
                    'month' => $month,
                    'year'  => $year
                ]
            );


            if (!$response->successful()) {

                return response()->json([
                    'success' => false,
                    'message' => 'API tidak dapat diakses'
                ]);

            }


            $json = $response->json();



            $existingSupplier = Delivery::where('del_month',$month)
                ->where('del_year',$year)
                ->pluck('supplierSearch')
                ->toArray();



            $suppliers = collect($json['data'] ?? [])

            ->filter(function($item) use ($existingSupplier){

                return !in_array(
                    $item['supplier_name'],
                    $existingSupplier
                );

            })


            ->map(function ($item) {

                return [

                    'supplier_name' =>
                        $item['supplier_name'] ?? '',


                    'qty_ordered' =>
                        $item['total_dn_qty'] ?? 0,


                    'qty_received' =>
                        $item['total_receipt_qty'] ?? 0,


                    'total_delay_days' =>
                        $item['total_delay_days'] ?? 0,

                ];

            })


            ->sortBy('supplier_name')
            ->values();



            return response()->json([

                'success'   => true,

                'suppliers' => $suppliers

            ]);



        } catch (\Exception $e) {


            return response()->json([

                'success' => false,

                'message' => $e->getMessage()

            ]);

        }
    }

    public function sync(Request $request)
{
    $month = $request->get('month');
    $year  = $request->get('year');

    try {

        $response = Http::withoutVerifying()
        ->timeout(30)
        ->get(
            'https://be-ams.sanohindonesia.co.id/api/public/delivery-performance/',
            [
                'month' => $month,
                'year'  => $year
            ]
        );


        if (!$response->successful()) {

            return response()->json([
                'success' => false,
                'message' => 'API tidak dapat diakses'
            ]);

        }


        $json = $response->json();


        return response()->json([

            'success' => true,

            'total' => count($json['data'] ?? []),

            'suppliers' => $json['data'] ?? []

        ]);


    } catch (\Exception $e) {


        return response()->json([

            'success' => false,

            'message' => $e->getMessage()

        ]);

    }
}


}