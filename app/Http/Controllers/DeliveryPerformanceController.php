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

            $response = Http::timeout(30)->get(
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



            // ambil supplier yang sudah pernah input delivery
            $existingSupplier = Delivery::where('del_month',$month)
                ->where('del_year',$year)
                ->pluck('supplierSearch')
                ->toArray();



            $suppliers = collect($json['data'] ?? [])

            ->filter(function($item) use ($existingSupplier){

                // buang supplier yang sudah ada
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


                    'on_time_deliveries' =>
                        $item['on_time_deliveries'] ?? 0,

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

        $response = Http::timeout(30)->get(
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