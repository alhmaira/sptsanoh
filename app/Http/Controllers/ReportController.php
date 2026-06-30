<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function index()
    {
        return view('report');
    }



    public function data(Request $request)
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




        return response()->json($data);

    }


}