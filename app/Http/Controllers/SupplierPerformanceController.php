<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierPerformance;
use App\Models\Delivery;
use App\Models\QC;
use App\Models\User;

use Barryvdh\DomPDF\Facade\Pdf;


class SupplierPerformanceController extends Controller
{


    public function show($id)
    {


        $data = SupplierPerformance::findOrFail($id);



        // ================= QUALITY =================

        $qualityPoint =
            ($data->lineStop ?? 0)
            +
            ($data->ppmScore ?? 0)
            +
            ($data->rank_score ?? 0)
            +
            ($data->fppk ?? 0);



        $qualityTotal = 100 - $qualityPoint;



        if($qualityTotal == 100){

            $qualityGrade = "A";

        }elseif($qualityTotal >= 80){

            $qualityGrade = "B";

        }elseif($qualityTotal >= 50){

            $qualityGrade = "C";

        }else{

            $qualityGrade = "D";

        }

        // ================= DELIVERY =================


        $qtyIdx = 0;

        if($data->fulfillment < 95)
            $qtyIdx = 2;

        if($data->fulfillment < 85)
            $qtyIdx = 4;

        if($data->fulfillment < 75)
            $qtyIdx = 6;

        if($data->fulfillment < 65)
            $qtyIdx = 8;




        $otdIdx = $data->otd ?? 0;


        $methodIdx = $data->del_method ?? 0;


        $premIdx = 0;


        if($data->premium > 500000)
            $premIdx = 2;

        if($data->premium > 1000000)
            $premIdx = 4;

        if($data->premium > 3000000)
            $premIdx = 8;



        $dpsIdx = $data->dps ?? 0;



        $totalIndex =
            $qtyIdx
            +
            $otdIdx
            +
            $methodIdx
            +
            $premIdx
            +
            $dpsIdx;



        $deliveryTotal =
            100 - $totalIndex;




        if($deliveryTotal >=95){

            $deliveryGrade="A";

        }elseif($deliveryTotal>=80){

            $deliveryGrade="B";

        }elseif($deliveryTotal>=60){

            $deliveryGrade="C";

        }else{

            $deliveryGrade="D";

        }





        return view(
    'Supplier-performance',
    [
        'data' => $data,

        'qualityTotal' => $qualityTotal,
        'qualityGrade' => $qualityGrade,

        'qtyIdx' => $qtyIdx,
        'otdIdx' => $otdIdx,
        'methodIdx' => $methodIdx,
        'premIdx' => $premIdx,
        'dpsIdx' => $dpsIdx,

        'deliveryTotal' => $deliveryTotal,
        'deliveryGrade' => $deliveryGrade,
    ]
);



    }

public function print($docNumber)
{


    $data = Delivery::where(
        'docNumber',
        $docNumber
    )->firstOrFail();


    // ================= AMBIL DATA QUALITY (TABEL qc) =================
    // Dicocokkan berdasarkan supplier + periode (del_month/del_year)
    // karena Quality dan Delivery tersimpan di tabel/record terpisah.

    $qcData = QC::where('supplier', $data->supplier ?? $data->supplier_name ?? $data->supplierSearch)
        ->where('del_month', $data->del_month)
        ->where('del_year', $data->del_year)
        ->first();


    if ($qcData) {

        $qualityTotal = $qcData->total_score ?? 0;

        if ($qualityTotal == 100) {
            $qualityGrade = "A";
        } elseif ($qualityTotal >= 80) {
            $qualityGrade = "B";
        } elseif ($qualityTotal >= 50) {
            $qualityGrade = "C";
        } else {
            $qualityGrade = "D";
        }

    } else {

        // Tidak ada record quality yang matching untuk supplier+periode ini
        $qualityTotal = 0;
        $qualityGrade = "D";

    }

    // ================= AMBIL USER SIGNATURE =================

$gm = User::where('role','General Manager')
          ->first();


$purchChecked = User::where('department','Purchasing')
                    ->where('role','Manager')
                    ->first();


$purchPrepared = User::where('department','Purchasing')
                     ->where('role','Leader')
                     ->first();


$ppicChecked = User::where('department','PPIC')
                   ->where('role','Manager')
                   ->first();

$ppicPrepared = User::where('department','PPIC')
                   ->where('role','Supervisor')
                   ->first();


$qcChecked = User::where('department','Quality Control')
                 ->where('role','Manager')
                 ->first();

$qcPrepared = User::where('department','Quality Control')
                 ->where('role','Supervisor')
                 ->first();

    $pdf = Pdf::loadView(
        'Supplier-performance',
        [

            'data'=>$data,

            'qcData'=>$qcData,


            'qualityTotal'=>$qualityTotal,

            'qualityGrade'=>$qualityGrade,


            'deliveryTotal'=>$data->total_score ?? 0,

            'deliveryGrade'=>$data->performance_grade ?? 'A',


            'qtyIdx'=>0,

            'otdIdx'=>$data->otd ?? 0,

            'methodIdx'=>$data->del_method ?? 0,

            'premIdx'=>0,

            'dpsIdx'=>$data->dps ?? 0,

               // USER SIGNATURE
            'gm'=>$gm,
            'purchChecked'=>$purchChecked,
            'purchPrepared'=>$purchPrepared,
            'ppicChecked'=>$ppicChecked,
            'ppicPrepared'=>$ppicPrepared,
            'qcChecked'=>$qcChecked,
            'qcPrepared'=>$qcPrepared,


        ]
    );



    return $pdf->download(
        'supplier-performance-'.$docNumber.'.pdf'
    );


}

}