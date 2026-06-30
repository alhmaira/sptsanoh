<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>Supplier Performance Yearly</title>


<style>

*{
    box-sizing:border-box;
}


@page{

    size:A4 portrait;

    margin:7mm;

}



body{

    font-family:"Helvetica Neue", Arial, sans-serif;

    font-size:7.5px;

    color:#000;

    margin:0;

}



/* ================= SHEET ================= */


.sheet{

    width:100%;

}



.sheet-border{

    border:1px solid #000;

    padding: 3px;

}



/* ================= HEADER ================= */


.header-table,
.approval-table{

    width:100%;

    border-collapse:collapse;

}

.note-bar {

    color: red;

    font-weight: bold;

    font-size: 9px;

    text-align:center;

    margin:5px 0;

}


.header-table td{

    border:none;

    vertical-align:middle;

}



.approval-table th,
.approval-table td{

    border:1px solid #000;

    padding:1px;

    text-align:center;

    font-size:5.5px;

}





/* ================= TITLE ================= */


.title-block{

    text-align:center;

    margin:4px 0;

}



.main-title{

    font-size:13px;

    font-weight:bold;

    text-decoration:underline;

}



.sub-title{

    font-size:8px;

    font-weight:bold;

}





/* ================= INFO ================= */



.info{

    font-size:7px;

    margin-bottom:4px;

}



/* ================= YEARLY TABLE ================= */


.performance-table{

    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
    margin-top:5px;

}



.performance-table th,
.performance-table td{

    border:1px solid #333;

    text-align:center;

    vertical-align:middle;

    padding:3px 2px;

    font-size:6.8px;

    line-height:1.1;

    color:#111;

}



/* HEADER MONTH */

.performance-table th{

    background:#f2f2f2;

    font-size:7.5px;

    font-weight:bold;

    height:18px;

}



/* SUBJECT */

.performance-table th:first-child,
.performance-table td:first-child{

    width:15%;

    font-weight:bold;

}



/* STAT POINT */

.performance-table th:nth-child(2),
.performance-table td:nth-child(2){

    width:8%;

}



/* MONTH */

.performance-table th:nth-child(n+3),
.performance-table td:nth-child(n+3){

    width:6.4%;

}



/* QUALITY DELIVERY BAR */

.performance-table .section{


    background:#d9d9d9;

    font-size:7.5px !important;

    font-weight:bold;

    text-align:left !important;

    padding:4px !important;


}



/* ROW HEIGHT */

.performance-table tr{

    height:20px;

}



/* TOTAL */

.performance-table .total-row td{


    font-size:7.5px;


    font-weight:bold;


    height:22px;


    background:#fafafa;


}


.performance-table b{

    font-size:7.5px;

}
/* ================= SECTION ================= */


.section{


    background:#d9d9d9;

    font-size:6px !important;

    font-weight:bold;

    text-align:left !important;


}





/* ================= TOTAL ================= */


.total-row{

    font-weight:bold;

}




/* ================= SIGNATURE ================= */


.signature img{


    height:25px;


}





/* ================= CRITERIA ================= */


.criteria-table,
.grade-table{


    width:100%;

    border-collapse:collapse;


}

/* ================= LEGEND GRADE ================= */

.grade-table{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
}


.grade-table th,
.grade-table td{

    border:1px solid #000;

    text-align:center;

    padding:0 !important;

    font-size:6.5px !important;

    line-height:7px !important;

    height:7px !important;

}


.grade-table th{

    background:#00a8d8;

    font-weight:bold;

}



/* ================= CRITERIA ================= */


.criteria-wrapper{

    width:100%;

    border-collapse:collapse;

    table-layout:fixed;

}



.criteria-wrapper > tr > td{

    width:50%;

    vertical-align:top;

    border:none;

    padding:0 2px !important;

}



.criteria-table{

    width:100%;

    border-collapse:collapse;

    table-layout:fixed;

}



.criteria-table th,
.criteria-table td{

    border:1px solid #000;

    padding:0 2px !important;

    font-size:6px !important;

    line-height:6px !important;

    height:6px !important;

    text-align:center;

}



.criteria-table th{

    background:#00a8d8;

    font-weight:bold;

}


.criteria-table .left-text{

    text-align:left !important;

}





/* ================= DELIVERY CRITERIA ================= */


.delivery-table{

    width:100%;

    border-collapse:collapse;

    table-layout:fixed;

}



.delivery-table th,
.delivery-table td{

    border:1px solid #000;

    padding:0 1px !important;

    font-size:6px !important;

    line-height:6px !important;

    height:6px !important;

    text-align:center;

    vertical-align:middle;

}



.delivery-table th{

    background:#ffc000;

    font-weight:bold;

}


/* ukuran kolom */

.delivery-table th:nth-child(1),
.delivery-table td:nth-child(1){

    width:5%;

}


.delivery-table th:nth-child(2),
.delivery-table td:nth-child(2){

    width:22%;

}



.delivery-table th:nth-child(n+3),
.delivery-table td:nth-child(n+3){

    width:12.16%;

}




/* jangan gepeng */

.criteria-wrapper table{

    margin:0;

}


.grade-table,
.criteria-table,
.delivery-table{

    page-break-inside:avoid;

}



.criteria-table td,
.criteria-table th,
.grade-table td,
.grade-table th{


    border:1px solid #000;


    font-size:5px;


    padding:1px;


}
/* ================= FIX SCORE CHART ================= */

.score-chart-wrapper{

    width:100%;
    border-collapse:separate;
    border-spacing:8px 0;
    margin-top:8px;

}


.score-chart-wrapper > tr > td{

    width:50% !important;
    border:1px solid #000 !important;

    padding:4px !important;

    vertical-align:top;

}



/* title */

.chart-title{

    text-align:center;

    font-size:7px;

    font-weight:bold;

    margin-bottom:4px;

}




.chart-half{

    width:50%;
    border:1px solid #000;
    padding:5px;

}

.chart-title{

    text-align:center;
    font-size:7px;
    font-weight:bold;
    margin-bottom:5px;

}



.chart-area{

    position:relative;

    height:100px;

    border-left:1px solid #000;

    border-bottom:1px solid #000;

    margin-left:25px;

}



.grid{

    position:absolute;

    left:0;

    width:100%;

    border-top:1px dotted #ccc;

}



.chart-point{

    position:absolute;

    width:4px;

    height:4px;

    background:#000;

    border-radius:50%;

}

.score-label{

    position:absolute;

    left:-25px;

    font-size:5px;

}

.s100{top:0%;}
.s90{top:10%;}
.s80{top:20%;}
.s70{top:30%;}
.s60{top:40%;}
.s50{top:50%;}
.s40{top:60%;}
.s30{top:70%;}
.s20{top:80%;}
.s10{top:90%;}
.s0{top:100%;}

.chart-line{

    position:absolute;

    height:1px;

    background:#000;

    transform-origin:left center;

}



.month-label{

    width:100%;

    table-layout:fixed;

}


.month-label td{

    font-size:5px;

    text-align:center;

    padding-top:3px;

}

.line-chart{

    width:100%;

    height:110px;

    border-collapse:collapse;

    border:none !important;

}





.line-chart > tr > td{

    border:none !important;

    padding:0 !important;

}




.axis{

    width:20px;

    font-size:5px;

    line-height:20px;

    text-align:left;

    vertical-align:top;

}





.graph{

    width:100%;

    height:80px;

    table-layout:fixed;

    border-left:1px solid #000;

    border-bottom:1px solid #000;

}



.graph td{

    border-bottom:1px dotted #ccc !important;

}





.point-cell{

    vertical-align:top;

    text-align:center;

}





.point{

    width:4px;

    height:4px;

    background:#000;

    margin-left:auto;

    margin-right:auto;

}





.month-row{

    width:100%;

    table-layout:fixed;

}



.month-row td{

    border:none !important;

    text-align:center;

    font-size:5px;

    padding-top:3px !important;

}






</style>


</head>


<body>

@php
function getSignatureImage($signature)
{
    if(!$signature){
        return null;
    }

    $filename = pathinfo($signature, PATHINFO_FILENAME);

    $files = glob(storage_path('app/public/signatures/'.$filename.'.*'));

    if(count($files) == 0){
        return null;
    }

    $path = $files[0];

    $type = pathinfo($path, PATHINFO_EXTENSION);

    $data = file_get_contents($path);

    return 'data:image/'.$type.';base64,'.base64_encode($data);
}
@endphp


<div class="sheet">
    <div class="sheet-border">

<div class="sheet">


<table class="top-info">

<tr>



<td align="right">
    PSQS 62000000
</td>


</tr>

</table>

<table style="width:100%; border-collapse:collapse;">

<tr>

<!-- COMPANY -->
<td style="width:36%; vertical-align:top; border:none; padding-right:10px;">

    <table class="header-table">
        <tr>

            <td style="width:65px; text-align:center; border:none;">
                <img
                    src="{{ public_path('images/sanohlogo.png') }}"
                    style="width:80px;">
            </td>

            <td style="border:none; padding-left:6px;">
                <b style="font-size:12px;">PT. SANOH INDONESIA</b><br> 
                <span style="font-size:8px;">
                    Purchasing Division<br>
                    Quality Engineering Division
                </span>
            </td>

        </tr>
    </table>

</td>

<!-- APPROVAL -->
<td style="width:64%; vertical-align:top; border:none; padding-left:8px;">

    <table class="approval-table">

<tr>
    <th>GM</th>
    <th colspan="2">PURCHASING</th>
    <th colspan="2">PPIC</th>
    <th colspan="2">QC</th>
</tr>


<tr>
    <td>APPROVED</td>

    <td>CHECKED</td>
    <td>PREPARED</td>

    <td>CHECKED</td>
    <td>PREPARED</td>

    <td>CHECKED</td>
    <td>PREPARED</td>
</tr>


<!-- SPACE TANDA TANGAN -->
<tr style="height:60px;">


    <!-- GM -->
    <td style="text-align:center;">
        @if($gm && $gm->signature)

<img src="{{ getSignatureImage($gm->signature) }}" height="40">

@endif
            </td>


    <!-- PURCH CHECKED -->
    <td style="text-align:center;">
       @if($purchChecked && $purchChecked->signature)

<img src="{{ getSignatureImage($purchChecked->signature) }}" height="40">

@endif
    </td>


    <!-- PURCH PREPARED -->
    <td style="text-align:center;">
        @if($purchPrepared && $purchPrepared->signature)

<img src="{{ getSignatureImage($purchPrepared->signature) }}" height="40">

@endif
    </td>


    <!-- PPIC CHECKED -->
    <td style="text-align:center;">
        @if($ppicChecked && $ppicChecked->signature)

<img src="{{ getSignatureImage($ppicChecked->signature) }}" height="40">

@endif
    </td>


    <!-- PPIC PREPARED -->
    <td style="text-align:center;">
        @if($ppicPrepared && $ppicPrepared->signature)

<img src="{{ getSignatureImage($ppicPrepared->signature) }}" height="40">

@endif
    </td>


    <!-- QC CHECKED -->
    <td style="text-align:center;">
        @if($qcChecked && $qcChecked->signature)

<img src="{{ getSignatureImage($qcChecked->signature) }}" height="40">

@endif
    </td>


    <!-- QC PREPARED -->
    <td style="text-align:center;">
        @if($qcPrepared && $qcPrepared->signature)

<img src="{{ getSignatureImage($qcPrepared->signature) }}" height="40">

@endif
    </td>

</tr>



<!-- NAMA AKUN -->
<tr>

<td>
    {{ $gm->name ?? '-' }}
</td>


<td>
    {{ $purchChecked->name ?? '-' }}
</td>


<td>
    {{ $purchPrepared->name ?? '-' }}
</td>


<td>
    {{ $ppicChecked->name ?? '-' }}
</td>


<td>
    {{ $ppicPrepared->name ?? '-' }}
</td>


<td>
    {{ $qcChecked->name ?? '-' }}
</td>


<td>
    {{ $qcPrepared->name ?? '-' }}
</td>
</tr>


</table>


</td>


</tr>

</table>


<div class="title-block">

<div class="main-title">
SUPPLIER PERFORMANCE
</div>


<div class="sub-title">

{{ $year }}

</div>


</div>



<div style="margin:4px 0 6px 0; font-size:9px;">
    <b>SUPPLIER NAME</b>
    <span style="display:inline-block; width:8px; text-align:center;">:</span>
    <b>{{ $supplier }}</b>
</div>



<div class="note-bar">

NOTE : Untuk Supplier Rank C & D Wajib membuat ACTION PLAN

</div>



@php

$qualityScores = [];

foreach($report as $month=>$monthData){

    if($monthData && $monthData->qc_score !== null){

        $qualityScores[] = $monthData->qc_score;

    }

}


$averageQuality = count($qualityScores) > 0
    ? round(array_sum($qualityScores) / count($qualityScores))
    : 0;


@endphp

<table class="performance-table">


<tr>


<th>
SUBJECT
</th>

<th>
</th>

@foreach($report as $month=>$monthData)


<th>
{{ strtoupper(substr($month,0,3)) }}
</th>


@endforeach


</tr>


<tr>
<td class="section" colspan="14">
QUALITY
</td>
</tr>


{{-- LINE STOP --}}
<tr>

<td class="subject" rowspan="2">
LINE STOP
</td>


<td>
STAT
</td>


@foreach($report as $month=>$monthData)

<td>
{{ 
$monthData 
? ($monthData->lineStop == 40 ? 'YES':'NO')
: '-'
}}
</td>

@endforeach


</tr>



<tr>

<td>
POINT
</td>


@foreach($report as $month=>$monthData)

<td>
<b>
{{ $monthData->lineStop ?? '-' }}
</b>
</td>

@endforeach


</tr>  



{{-- PPM --}}
<tr>

<td class="subject" rowspan="4">
PPM
</td>


<td>
NG
</td>


@foreach($report as $month=>$monthData)

<td>
{{ $monthData->ng ?? '-' }}
</td>

@endforeach


</tr>



<tr>

<td>
SUPPLY
</td>


@foreach($report as $month=>$monthData)

<td>
{{ $monthData->supply ?? '-' }}
</td>

@endforeach


</tr>



<tr>

<td>
PPM
</td>


@foreach($report as $month=>$monthData)

<td>
{{ $monthData->ppm ?? '-' }}
</td>

@endforeach


</tr>



<tr>

<td>
POINT
</td>


@foreach($report as $month=>$monthData)

<td>
<b>
{{ $monthData->ppmScore ?? '-' }}
</b>
</td>

@endforeach


</tr>




{{-- PROBLEM RANK --}}
<tr>

<td class="subject" rowspan="2">
PROBLEM RANK
</td>


<td>
RANK
</td>


@foreach($report as $month=>$monthData)

<td>

@if(($monthData->rank_score ?? 0)==25)
A
@elseif(($monthData->rank_score ?? 0)==10)
B
@elseif(($monthData->rank_score ?? 0)==5)
C
@else
-
@endif

</td>

@endforeach


</tr>


<tr>

<td>
POINT
</td>


@foreach($report as $month=>$monthData)

<td>
<b>
{{ $monthData->rank_score ?? '-' }}
</b>
</td>

@endforeach


</tr>

{{-- FPPK --}}
<tr>

<td class="subject" rowspan="2">
FPPK REPLY
</td>


<td>
STAT
</td>


@foreach($report as $month=>$monthData)

<td>

@if($monthData)

@switch($monthData->fppk)

@case(0)
NO PROBLEM
@break

@case(10)
DELAY
@break

@case(20)
NO REPLY
@break

@default
-

@endswitch

@else
-

@endif

</td>


@endforeach


</tr>



<tr>

<td>
POINT
</td>


@foreach($report as $month=>$monthData)

<td>
<b>
{{ $monthData->fppk ?? '-' }}
</b>
</td>

@endforeach


</tr>



<tr class="total-row">

<td colspan="2">
TOTAL POINT (100 - POINT)
</td>


@foreach($report as $month=>$monthData)

<td>
{{ $monthData->qc_score ?? '-' }}
</td>

@endforeach

</tr>




{{-- GRADE / RANK --}}
<tr class="total-row">


<td colspan="2">
GRADE / RANK
</td>



@foreach($report as $month=>$monthData)

<td>


@if($monthData && $monthData->qc_score !== null)


    @if($monthData->qc_score == 100)

        A

    @elseif($monthData->qc_score >= 80)

        B

    @elseif($monthData->qc_score >= 50)

        C

    @else

        D

    @endif


@else

-

@endif


</td>

@endforeach
</tr>


{{-- ===================== DELIVERY ===================== --}}

<tr>
<td class="section" colspan="14">
DELIVERY
</td>
</tr>


{{-- FULFILLMENT --}}
<tr>

<td class="subject" rowspan="2">
FULFILLMENT
</td>


<td>
%
</td>


@foreach($report as $month=>$monthData)

<td>
{{ $monthData->fulfillment ?? '-' }}
</td>

@endforeach


</tr>



<tr>

<td>
INDEX
</td>


@foreach($report as $month=>$monthData)

<td>
<b>

{{ $monthData->qty_index ?? '-' }}

</b>
</td>

@endforeach


</tr>




{{-- OTD --}}
<tr>

<td class="subject" rowspan="2">
ON TIME DELIVERY
</td>


<td>
DAY
</td>


@foreach($report as $month=>$monthData)

<td>

@if($monthData)

@switch($monthData->otd)

@case(0)
NO DELAY
@break

@case(2)
DELAY 1 DAY
@break

@case(4)
DELAY 2 DAYS
@break

@case(6)
DELAY 3 DAYS
@break

@case(10)
DELAY >3 DAYS
@break

@default
-

@endswitch

@else
-

@endif

</td>

@endforeach


</tr>



<tr>

<td>
INDEX
</td>


@foreach($report as $month=>$monthData)

<td>
<b>
{{ $monthData->otd_index ?? '-' }}
</b>
</td>

@endforeach


</tr>





{{-- DELIVERY METHOD --}}
<tr>

<td class="subject" rowspan="2">
DELIVERY METHOD
</td>


<td>
METHOD
</td>


@foreach($report as $month=>$monthData)

<td>

@if($monthData)

{{ $monthData->del_method == 4 ? 'ABNORMAL':'NORMAL' }}

@else
-

@endif

</td>

@endforeach


</tr>



<tr>

<td>
INDEX
</td>


@foreach($report as $month=>$monthData)

<td>
<b>
{{ $monthData->method_index ?? '-' }}
</b>
</td>

@endforeach


</tr>





{{-- PREMIUM --}}
<tr>

<td class="subject" rowspan="2">
PREMIUM FREIGHT
</td>


<td>
IDR/1000
</td>


@foreach($report as $month=>$monthData)

<td>

{{ $monthData->premium ?? '-' }}

</td>

@endforeach


</tr>



<tr>

<td>
INDEX
</td>


@foreach($report as $month=>$monthData)

<td>

<b>

@if($monthData)

{{ $monthData->prem_index ?? '-' }}

@else

-

@endif

</b>

</td>

@endforeach


</tr>





{{-- DPS --}}
<tr>

<td class="subject" rowspan="2">
DELIVERY PROBLEM SHEET (DPS) REPLY
</td>


<td>
REPLY
</td>


@foreach($report as $month=>$monthData)

<td>

@if($monthData)

@switch($monthData->dps)

@case(0)
NO PROBLEM
@break

@case(10)
DELAY
@break

@case(20)
NO REPLY
@break

@default
-

@endswitch

@else
-

@endif

</td>

@endforeach


</tr>



<tr>

<td>
INDEX
</td>


@foreach($report as $month=>$monthData)

<td>
<b>
{{ $monthData->dps_index ?? '-' }}
</b>
</td>

@endforeach


</tr>





{{-- TOTAL INDEX --}}
<tr class="total-row">

<td colspan="2">
TOTAL INDEX
</td>


@foreach($report as $month=>$monthData)

<td>
{{ $monthData->delivery_total_index ?? '-' }}
</td>

@endforeach


</tr>





{{-- SCORE TOTAL --}}
<tr class="total-row">

<td colspan="2">
SCORE TOTAL (100 - TOTAL INDEX)
</td>


@foreach($report as $month=>$monthData)

<td>

{{ $monthData->delivery_total_score ?? '-' }}

</td>


@endforeach


</tr>





{{-- GRADE --}}
<tr class="total-row">

<td colspan="2">
GRADE / RANK
</td>


@foreach($report as $month=>$monthData)

<td>

@if($monthData && $monthData->delivery_score !== null)

@php
$score = $monthData->delivery_score;
@endphp


@if($score == 100)

A

@elseif($score >= 80 && $score <= 99)

B

@elseif($score >= 60 && $score <= 79)

C

@elseif($score < 60)

D

@endif


@else

-

@endif


</td>


@endforeach


</tr>
</div>

{{-- ================= SCORE CHART ================= --}}

@php

$months = [
    'JAN','FEB','MAR','APR','MAY','JUN',
    'JUL','AUG','SEP','OCT','NOV','DEC'
];


$qualityChart = [];
$deliveryChart = [];


foreach($months as $month){


    $data = null;


    foreach($report as $key=>$item){

        if(strtoupper(substr($key,0,3)) == $month){

            $data = $item;
            break;

        }

    }



    $qualityChart[] = [

        'month'=>$month,

        'score'=> intval($data->qc_score ?? 0)

    ];



    $deliveryChart[] = [

        'month'=>$month,

        'score'=> intval($data->delivery_score ?? 0)

    ];


}


@endphp



<table class="score-chart-wrapper">

<tr>


<td class="chart-half">


<div class="chart-title">
QUALITY ACHIEVEMENT
</div>



<div class="chart-area">


@for($i=0;$i<=5;$i++)

<div class="grid"
style="
top:{{$i*20}}%;
">
</div>


@endfor

<div class="score-label s100">100</div>
<div class="score-label s80">80</div>
<div class="score-label s60">60</div>
<div class="score-label s40">40</div>
<div class="score-label s20">20</div>
<div class="score-label s0">0</div>

@foreach($qualityChart as $index=>$c)


@php

$x = ($index / 11) * 100;

$y = 100 - $c['score'];

@endphp



<div class="chart-point"

style="
left:{{$x}}%;
top:{{$y}}%;
">

</div>


@endforeach



</div>



<table class="month-label">

<tr>

@foreach($qualityChart as $c)

<td>
{{$c['month']}}
</td>

@endforeach


</tr>

</table>



</td>






<td class="chart-half">


<div class="chart-title">
DELIVERY ACHIEVEMENT
</div>



<div class="chart-area">


@for($i=0;$i<=5;$i++)

<div class="grid"
style="
top:{{$i*20}}%;
">
</div>


@endfor

<div class="score-label s100">100</div>
<div class="score-label s80">80</div>
<div class="score-label s60">60</div>
<div class="score-label s40">40</div>
<div class="score-label s20">20</div>
<div class="score-label s0">0</div>

@foreach($deliveryChart as $index=>$c)


@php

$x = ($index / 11) * 100;

$y = 100 - $c['score'];

@endphp



<div class="chart-point"

style="
left:{{$x}}%;
top:{{$y}}%;
">

</div>


@endforeach



</div>


<table class="month-label">

<tr>

@foreach($deliveryChart as $c)

<td>
{{$c['month']}}
</td>

@endforeach


</tr>

</table>



</td>


</tr>


</table>

{{-- ===================== LEGEND / GRADE TABLES ===================== --}}

<table style="width:100%; border-collapse:collapse; margin-top:3px;">
<tr>

<td style="width:50%; vertical-align:top; border:none; padding:0 2px 0 0;">


<table class="grade-table">

<tr>
<th colspan="2">
QUALITY GRADE
</th>
</tr>

<tr>
<th>GRADE</th>
<th>SCORE</th>
</tr>

<tr>
<td>A</td>
<td>100</td>
</tr>

<tr>
<td>B</td>
<td>80 ~ 99</td>
</tr>

<tr>
<td>C</td>
<td>50 ~ 79</td>
</tr>

<tr>
<td>D</td>
<td>&lt; 50</td>
</tr>

</table>


</td>



<td style="width:50%; vertical-align:top; border:none; padding:0 0 0 2px;">


<table class="grade-table">


<tr>
<th colspan="2">
DELIVERY GRADE
</th>
</tr>


<tr>
<th>GRADE</th>
<th>SCORE</th>
</tr>


<tr>
<td>A</td>
<td>100%</td>
</tr>


<tr>
<td>B</td>
<td>80% ~ 99%</td>
</tr>


<tr>
<td>C</td>
<td>60% ~ 79%</td>
</tr>


<tr>
<td>D</td>
<td>&lt; 60%</td>
</tr>


</table>


</td>

</tr>

</table>



{{-- ===================== CRITERIA SCORE ===================== --}}


<table class="criteria-wrapper">

<tr>


<!-- LEFT CRITERIA -->
<td style="width:50%; vertical-align:top; border:none;">


<table class="criteria-table">


<tr>
<th>KRITERIA</th>
<th>BOBOT</th>
<th>PRESENTASE</th>

<th>KRITERIA</th>
<th>BOBOT</th>
<th>PRESENTASE</th>
</tr>


<tr>

<td colspan="2">
<b>LINE STOP</b>
</td>

<td rowspan="3">
40%
</td>


<td colspan="2">
<b>RANK</b>
</td>

<td rowspan="4">
25%
</td>

</tr>


<tr>

<td class="left-text">
a.) YA
</td>

<td>
40 Point
</td>


<td class="left-text">
a.) A
</td>

<td>
25 Point
</td>

</tr>


<tr>

<td class="left-text">
b.) TIDAK
</td>

<td>
0 Point
</td>


<td class="left-text">
b.) B
</td>

<td>
10 Point
</td>

</tr>


<tr>

<td colspan="2">
<b>PPM</b>
</td>

<td rowspan="5">
15%
</td>


<td class="left-text">
c.) C
</td>

<td>
5 Point
</td>

</tr>


<tr>

<td class="left-text">
a.) ZERO PPM
</td>

<td>
0 Point
</td>


<td colspan="2">
<b>FPPK REPLY</b>
</td>

<td rowspan="4">
20%
</td>


</tr>


<tr>

<td class="left-text">
b.) 1 ~ 20 PPM (Target)
</td>

<td>
5 Point
</td>


<td class="left-text">
a.) On Time
</td>

<td>
0 Point
</td>

</tr>


<tr>

<td class="left-text">
c.) 21 ~ 200 PPM
</td>

<td>
10 Point
</td>


<td class="left-text">
b.) Delay
</td>

<td>
10 Point
</td>

</tr>


<tr>

<td class="left-text">
d.) > 200 PPM
</td>

<td>
15 Point
</td>


<td class="left-text">
c.) No Reply
</td>

<td>
20 Point
</td>

</tr>


</table>


</td>





<!-- RIGHT DELIVERY CRITERIA -->
<td style="width:50%; vertical-align:top; border:none;">


<table class="criteria-table delivery-table">


<tr>

<th rowspan="2">NO</th>

<th rowspan="2">VARIABEL</th>

<th colspan="6">GRADE</th>

</tr>


<tr>

<th></th>
<th>95% - 100%</th>
<th>85 - 94%</th>
<th>75 - 84%</th>
<th>65 - 74%</th>
<th>&lt; 64%</th>

</tr>



<!-- QUANTITY -->

<tr>

<td rowspan="2">1</td>

<td rowspan="2">
Quantity (Kesesuaian dengan DN)
</td>


<td>%</td>
<td>95% - 100%</td>
<td>85 - 94%</td>
<td>75 - 84%</td>
<td>65 - 74%</td>
<td>&lt; 64%</td>

</tr>


<tr>

<td>Index</td>

<td>0</td>

<td>2</td>

<td>4</td>

<td>6</td>

<td>8</td>

</tr>





<!-- OTD -->

<tr>

<td rowspan="2">2</td>

<td rowspan="2">
On-Time Delivery
</td>


<td>Day</td>

<td>No Delay</td>

<td>Delay 1 day</td>

<td>Delay 2 days</td>

<td>Delay 3 days</td>

<td>Delay > 3 days</td>

</tr>


<tr>

<td>Index</td>

<td>0</td>

<td>2</td>

<td>4</td>

<td>6</td>

<td>10</td>

</tr>






<!-- METHOD -->

<tr>

<td rowspan="2">3</td>

<td rowspan="2">
Delivery method
</td>


<td>Method</td>

<td>Normal</td>

<td>Abnormal</td>

<td></td>

<td></td>

<td></td>

</tr>


<tr>

<td>Index</td>

<td>0</td>

<td>4</td>

<td></td>

<td></td>

<td></td>

</tr>






<!-- PREMIUM -->

<tr>

<td rowspan="2">4</td>

<td rowspan="2">
Premium Freight/Month
</td>


<td>Rp</td>

<td>0</td>

<td>0 - Rp.500 rb</td>

<td>500 rb - Rp.1jt</td>

<td>1jt - Rp.3jt</td>

<td>>3jt</td>

</tr>


<tr>

<td>Index</td>

<td>0</td>

<td>2</td>

<td>4</td>

<td>6</td>

<td>8</td>

</tr>







<!-- DPS -->

<tr>

<td rowspan="2">5</td>

<td rowspan="2">
Delivery Problem Sheet(DPS) Reply
</td>


<td>Reply</td>

<td>ON TIME</td>

<td>DELAY</td>

<td>NO REPLY</td>

<td></td>
<td></td>

</tr>



<tr>

<td>Index</td>

<td>0</td>

<td>10</td>

<td>20</td>

<td></td>
<td></td>

</tr>



</table>

</body>

</html>