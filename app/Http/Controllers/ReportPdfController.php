<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SupplierPerformance;
use App\Models\User;
use App\Models\Qc;
use App\Models\Delivery;


class ReportPdfController extends Controller
{

    public function summary(Request $request)
    {


        $query = DB::table('approval_histories as h')

            ->join('qc as q',
                'h.doc_number',
                '=',
                'q.docNumber'
            )

            ->join('delivery as d',
                'h.doc_number',
                '=',
                'd.docNumber'
            )

            ->where('h.role_name','General Manager')

            ->where('h.department','Production')

            ->where('h.action','APPROVED');




        if($request->filled('supplier')){

            $query->where(
                'q.supplier',
                $request->supplier
            );

        }



        if($request->filled('month')){

            $query->where(
                'q.del_month',
                $request->month
            );

        }



        if($request->filled('year')){

            $query->where(
                'q.del_year',
                $request->year
            );

        }

    $period = "ALL TIME";


if($request->filled('month') && $request->filled('year')){


    $period = strtoupper(
        date(
            'F',
            mktime(
                0,
                0,
                0,
                $request->month,
                1
            )
        )
    )
    . ' '
    . $request->year;


}
elseif($request->filled('year')){


    $period = $request->year;


}


        $data = $query

            ->select(

                'q.docNumber as doc_number',

                'q.supplier',

                'q.del_month',

                'q.del_year',

                'q.total_score as qc_score',

                'd.total_score as delivery_score'

            )

            ->distinct()

            ->get();





        $data->transform(function($row){


            $row->final_score = round(

                ($row->qc_score + $row->delivery_score) / 2,

                2

            );


            return $row;


        });

        // RANK DELIVERY

$deliveryRank = $data
    ->sortByDesc('delivery_score')
    ->values();


foreach($deliveryRank as $index => $item){

    $item->delivery_rank = $index + 1;

}

// RANK QUALITY

$qualityRank = $data
    ->sortByDesc('qc_score')
    ->values();


foreach($qualityRank as $index => $item){

    $item->quality_rank = $index + 1;

};

       // BEST SUPPLIER ON PERIOD

$bestDelivery = $data
    ->sortByDesc('delivery_score')
    ->take(3)
    ->values();


$bestQuality = $data
    ->sortByDesc('qc_score')
    ->take(3)
    ->values();

$worstDelivery = $data
    ->sortBy('delivery_score')
    ->values()
    ->take(3)
    ->map(function($item){

        return $item;

    });


$worstQuality = $data
    ->sortBy('qc_score')
    ->values()
    ->take(3)
    ->map(function($item){

        return $item;

    });


$managerPurch = User::where('role','Manager')
    ->where('department','Purchasing')
    ->first();


$leaderPurch = User::where('role','Leader')
    ->where('department','Purchasing')
    ->first();


        $pdf = Pdf::loadView(
    'summary-pdf',
    compact(
        'data',
        'managerPurch',
        'leaderPurch',
        'period',
        'bestDelivery',
        'bestQuality',
        'worstDelivery',
        'worstQuality'
    )

);



        $pdf->setPaper(
    'A4',
    'portrait'
);



        return $pdf->download(
        'Resume_Supplier_Performance_'.$period.'.pdf'
);


    }

    public function yearly(Request $request)
{

    $supplier = $request->supplier;

    $year = $request->year;


    $months = [
        1=>'JAN',
        2=>'FEB',
        3=>'MAR',
        4=>'APR',
        5=>'MAY',
        6=>'JUN',
        7=>'JUL',
        8=>'AUG',
        9=>'SEP',
        10=>'OCT',
        11=>'NOV',
        12=>'DEC'
    ];


    $report = [];


    foreach($months as $num=>$name){


$data = DB::table('approval_histories as h')


->join(
    'qc as q',
    'h.doc_number',
    '=',
    'q.docNumber'
)


->leftJoin(
    'delivery as d',
    'q.docNumber',
    '=',
    'd.docNumber'
)



->where(
    'h.role_name',
    'General Manager'
)


->where(
    'h.department',
    'Production'
)


->where(
    'h.action',
    'APPROVED'
)


->where(
    'q.supplier',
    $supplier
)


->where(
    'q.del_month',
    sprintf('%02d',$num)
)


->where(
    'q.del_year',
    $year
)



->select(

    // QUALITY
    'q.supplier',
    'q.lineStop',
    'q.ng',
    'q.supply',
    'q.ppm',
    'q.ppmScore',
    'q.rank_score',
    'q.fppk',
    'q.total_score as qc_score',


    // DELIVERY
    'd.fulfillment',
    'd.otd',
    'd.del_method',
    'd.premium',
    'd.dps',
    'd.total_score as delivery_score'

)

->first();

if($data){


    // ================= DELIVERY INDEX =================


    // FULFILLMENT INDEX
    $fulfillmentNum = floatval($data->fulfillment ?? 0);


    $data->qty_index =
        $fulfillmentNum >= 95 ? 0 :
        ($fulfillmentNum >= 85 ? 2 :
        ($fulfillmentNum >= 75 ? 4 :
        ($fulfillmentNum >= 65 ? 6 : 8)));



    // OTD INDEX
    // 0 No Delay
    // 2 Delay 1 day
    // 4 Delay 2 days
    // 6 Delay 3 days
    // 10 Delay >3 days

    $data->otd_index = $data->otd ?? 0;



    // DELIVERY METHOD INDEX
    // Normal = 0
    // Abnormal = 4

    $data->method_index =
        $data->del_method ?? 0;



    // PREMIUM FREIGHT INDEX

    $premium = floatval($data->premium ?? 0);


    $data->prem_index =
        $premium == 0 ? 0 :
        ($premium <= 500000 ? 2 :
        ($premium <= 1000000 ? 4 :
        ($premium <= 3000000 ? 6 : 8)));



    // DPS INDEX
    // No Problem = 0
    // On Time = 0
    // Delay = 10
    // No Reply = 20

    $data->dps_index =
        $data->dps ?? 0;



    // ================= TOTAL INDEX =================

    $data->delivery_total_index =
        $data->qty_index +
        $data->otd_index +
        $data->method_index +
        $data->prem_index +
        $data->dps_index;



    // ================= TOTAL SCORE =================

    $data->delivery_total_score =
        100 - $data->delivery_total_index;

}


$report[$name] = $data;

}

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
    'yearly-pdf',
    [

        'report'=>$report,

        'supplier'=>$supplier,

        'year'=>$year,


        'gm'=>$gm,
        'purchChecked'=>$purchChecked,
        'purchPrepared'=>$purchPrepared,
        'ppicChecked'=>$ppicChecked,
        'ppicPrepared'=>$ppicPrepared,
        'qcChecked'=>$qcChecked,
        'qcPrepared'=>$qcPrepared,

    ]
);




    $pdf->setPaper(
        'A4',
        'portrait'
    );



    return $pdf->download(
        'Supplier_Performance_'.$supplier.'_'.$year.'.pdf'
    );


}
}