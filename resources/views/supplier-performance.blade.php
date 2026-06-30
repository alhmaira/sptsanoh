<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>SUPPLIER PERFORMANCE</title>
</head>
<style>
        * { box-sizing:border-box; }

  @page {
    size: A4 portrait;
    margin: 10mm;
}


body {

    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    font-size: 9px;
    color:#000;

    padding:0;
    margin:0;

    line-height:1.25;

}

    .sheet {

    width:100%;

    margin:auto;

}


.sheet-border{
    border:1px solid #000;
    padding:6px;
    overflow:hidden;
}

    .header-table {
        width:100%;
        border-collapse:collapse;
        padding:3px;
    }
    .approval-table,
    .problem-table,
    .footer-table {
        width:100%;
        border-collapse:collapse;
        padding:3px;
    }
    
    .detail-table {
        width:100%;
        border-collapse:collapse;
        padding:3px;
        table-layout:fixed;
    }

    td, th {

    border:1px solid #000;

    padding:3px 4px;

    font-size:8.5px;

    line-height:1.2;

}

    .logo-cell {


    width:70px;

    font-size:16px;


}




.company-cell {
    font-size:10.5px;
    line-height:1.3;
}



.title-block {


    text-align:center;

    margin:5px 0;


}



.main-title {


    font-size:15px; 

    font-weight:bold;

    text-decoration:underline;


}



.sub-title {


    font-size:10px;

    font-weight:bold;


}

.info-table{
    width:auto !important;
    border-collapse:collapse;
}


.info-table td{
    border:none;
    padding:1px 2px;
    text-align:left;
    vertical-align:middle;
}

.note-bar {

    color: red;

    font-weight: bold;

    font-size: 9px;

    text-align:center;

    margin:5px 0;

}

.section-title-table {


    background:#d9d9d9;

    font-weight:bold;

    padding:2px 4px;

    margin-top:5px;

    font-size:10px;


}




.detail-table td {


    padding:2px 3px;
    width:auto;


}


.problem-header-bar {

    background:red;
    color:white;
    font-weight:bold;
    padding:2px 4px;
    font-size:9px;
    width:35%;
}

.problem-chart-wrapper{
    display:flex;
    gap:8px;
    margin-top:3px;
}

.left-column{
    width:55%;
}

.right-column{
    width:45%;
}

.chart-box{
    border:1px solid #000;
    margin-bottom:4px;
    padding:2px;
}

.footer-table td {


    padding:10px 2px;


}

.top-info {

    width:100%;

    border-collapse:collapse;

    margin-bottom:3px;

}


.top-info td {

    border:none;

    padding:0;

    font-size:9px;

}

.problem-chart-wrapper{
    width:100%;
    overflow:hidden;
    margin-top:5px;
}

.left-column{
    width:54%;
    float:left;
}

table{
    width:100%;
    border-collapse:collapse;
}

/* ================= GRADE TABLE ================= */

.grade-table{
    width:100%;
    height:10px;
    border-collapse:collapse;
}

.grade-table th,
.grade-table td{
    border:1px solid #000;
    text-align:center;
    padding:1px 2px !important;
    font-size:7px !important;
    line-height:1 !important;
}

td[style*="width:50%"]{
    border:none !important;
}

/* ================= CRITERIA TABLE ================= */

.criteria-wrapper{
    width:100%;
    border-collapse:collapse;
    margin-top:2px !important;
}


.criteria-wrapper td{
    width:50%;
    vertical-align:top;
    border:none;
    padding:0 !important;
    
}


.criteria-table{
    border-collapse:collapse;
    table-layout:fixed;
}


.criteria-table th,
.criteria-table td{

    border:1px solid #000;

    padding:1px 2px !important;

    font-size:7px !important;

    line-height:1 !important;

    height:auto !important;

    vertical-align:middle;

}


.criteria-table th{
    background:#00a8d8;
    font-weight:bold;
    text-align:center;
}


.criteria-table td{
    text-align:center;
}


.criteria-table .left-text{
    text-align:left !important;
}



.left-text{
    text-align:left !important;
}



/* ================= DELIVERY TABLE ================= */


.delivery-table{
    table-layout:fixed;
    border-collapse:collapse;
    word-wrap:break-word;
}



.delivery-table th,
.delivery-table td{
    border:1px solid #000;
    text-align:center;
    vertical-align:middle;
    padding:1px !important;
    font-size:6.5px !important;
    line-height:1 !important;
}



.delivery-table th{
    background:#ffc000;
    font-weight:bold;
}



/* ukuran kolom delivery */

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
    width:14.6%;
}



/* ================= SPACING PDF ================= */


.criteria-wrapper{
    margin-top:1px !important;
}


table[style*="margin-top:5px"]{
    margin-top:1px !important;
}

.header-table{
    width:100%;
    table-layout:auto !important;
    border-collapse:collapse;
}

.header-table td{
    border:none;
    vertical-align:middle;
}

/* ================= FORCE CRITERIA SMALL ================= */


/* area grade + criteria jangan kasih ruang */
.grade-table,
.criteria-wrapper,
.criteria-table,
.delivery-table{
    margin:0 !important;
    padding:0 !important;
}


/* GRADE */
.grade-table th,
.grade-table td{
    padding:0 !important;
    font-size:6px !important;
    line-height:7px !important;
    height:7px !important;
}


/* CRITERIA KIRI KANAN */
.criteria-wrapper td{
    padding:0 !important;
}


.criteria-table{
    width:100% !important;
    table-layout:fixed !important;
}


.criteria-table th,
.criteria-table td{

    padding:0 !important;
    font-size:5.5px !important;
    line-height:6px !important;
    height:6px !important;

}


/* header */
.criteria-table th{
    height:7px !important;
}


/* tulisan kiri */
.left-text{
    text-align:left !important;
}


/* Delivery criteria paling bawah */
.delivery-table th,
.delivery-table td{

    padding:0 !important;
    font-size:5px !important;
    line-height:6px !important;
    height:6px !important;

}



/* kecilkan jarak sebelum criteria */
.criteria-wrapper{
    margin-top:-2px !important;
}


/* paksa tidak pindah halaman */
.grade-table,
.criteria-wrapper,
.delivery-table{
    page-break-inside:avoid !important;
}

table{
    table-layout:fixed;
}

td,
th{
    overflow-wrap:break-word;
    word-break:break-word;
}

/* ================= FINAL TABLE SIZE ================= */

/* GRADE */
.grade-table th,
.grade-table td{
    padding:0 !important;
    font-size:6.5px !important;
    line-height:6px !important;
    height:6px !important;
}


/* CRITERIA */
.criteria-wrapper td{
    padding:0 !important;
}


.criteria-table{
    width:100% !important;
    table-layout:fixed !important;
}


.criteria-table th,
.criteria-table td{
    padding:0 2px !important;
    font-size:6.5px !important;
    line-height:6px !important;
    height:6px !important;
}


.criteria-table th{
    height:7px !important;
}


/* DELIVERY CRITERIA */
.delivery-table th,
.delivery-table td{
    padding:0 1px !important;
    font-size:6px !important;
    line-height:6px !important;
    height:6px !important;
}

</style>
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

@php

function parseProblemData($data)
{
    if(!$data){
        return [];
    }

    if(is_string($data)){

        $decoded = json_decode($data,true);

        if(json_last_error() === JSON_ERROR_NONE){
            return $decoded;
        }

    }

    return $data;
}

@endphp

<div class="sheet">
    <div class="sheet-border">

<div class="sheet">


<table class="top-info">

<tr>

<td>
    {{ $data->docNumber ?? '-' }}
</td>


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

{{ strtoupper(date('F', mktime(0,0,0,$data->del_month,1)).' '.$data->del_year) }}

</div>


</div>



<div style="margin:4px 0 6px 0; font-size:9px;">
    <b>SUPPLIER NAME</b>
    <span style="display:inline-block; width:8px; text-align:center;">:</span>
    <b>{{ $data->supplier_name ?? $data->supplierSearch }}</b>
</div>



<div class="note-bar">

NOTE : Untuk Supplier Rank C & D Wajib membuat ACTION PLAN

</div>

{{-- ===================== QUALITY ===================== --}}

<div class="section-title-table">
    QUALITY
</div>

<table class="detail-table">

    {{-- LINE STOP --}}
    <tr>
        <td rowspan="2">Line Stop</td>
        <td>Status</td>
        <td>{{ ($qcData->lineStop ?? 0) == 40 ? 'YES' : 'NO' }}</td>
    </tr>
    <tr>
        <td>Point</td>
        <td><b>{{ $qcData->lineStop ?? 0 }}</b></td>
    </tr>

    {{-- PPM --}}
    <tr>
        <td rowspan="4">PPM</td>
        <td>NG</td>
        <td>{{ $qcData->ng ?? 0 }}</td>
    </tr>
    <tr>
        <td>Supply</td>
        <td>{{ $qcData->supply ?? 0 }}</td>
    </tr>
    <tr>
        <td>PPM</td>
        <td>{{ $qcData->ppm ?? 0 }}</td>
    </tr>
    <tr>
        <td>Point</td>
        <td><b>{{ $qcData->ppmScore ?? 0 }}</b></td>
    </tr>

    {{-- PROBLEM RANK --}}
    <tr>
        <td rowspan="2">Problem Rank</td>
        <td>Rank</td>
        <td>
            @switch($qcData->rank_score ?? 0)
                @case(25) A @break
                @case(10) B @break
                @case(5)  C @break
                @default  -
            @endswitch
        </td>
    </tr>
    <tr>
        <td>Point</td>
        <td><b>{{ $qcData->rank_score ?? 0 }}</b></td>
    </tr>

    {{-- FPPK --}}
    <tr>
        <td rowspan="2">FPPK</td>
        <td>Status</td>
        <td>
            @switch($qcData->fppk ?? 0)
                @case(0) NO PROBLEM @break
                @case(10) DELAY @break
                @case(20) NO REPLY @break
                @default -
            @endswitch
        </td>
    </tr>
    <tr>
        <td>Point</td>
        <td><b>{{ $qcData->fppk ?? 0 }}</b></td>
    </tr>

    {{-- TOTAL --}}
    <tr class="total-row">
        <td>Total Score ( 100 - Total Index)</td>
        <td>Grade/Rank {{ $qualityGrade }}</td>
        <td><b>{{ $qualityTotal }}</b></td>
    </tr>

</table>

{{-- ===================== DELIVERY ===================== --}}

<div class="section-title-table">
    DELIVERY
</div>


<table class="detail-table">


<tr>

<td rowspan="2">
    Fulfillment
</td>


<td>
    %
</td>


<td>
    {{ $data->fulfillment ?? 0 }}
</td>

</tr>



<tr>

<td>
    Index
</td>


<td>
    {{ $qtyIdx }}
</td>

</tr>




<tr>

<td rowspan="2">
    On Time Delivery
</td>


<td>
    Day
</td>


<td>

@switch($data->otd)

@case(0)
No Delay
@break


@case(2)
Delay 1 day
@break


@case(4)
Delay 2 days
@break


@case(6)
Delay 3 days
@break


@case(10)
Delay > 3 days
@break


@default
-

@endswitch


</td>

</tr>



<tr>

<td>
Index
</td>


<td>
{{ $otdIdx }}
</td>


</tr>





<tr>

<td rowspan="2">
Delivery Method
</td>


<td>
Method
</td>


<td>

{{ $data->del_method == 4 ? 'ABNORMAL':'NORMAL' }}

</td>

</tr>



<tr>

<td>
Index
</td>


<td>
{{ $methodIdx }}
</td>


</tr>





<tr>

<td rowspan="2">
Premium Freight
</td>


<td>
IDR
</td>


<td>
{{ $data->premium ?? 0 }}
</td>


</tr>



<tr>

<td>
Index
</td>


<td>
{{ $premIdx }}
</td>

</tr>

<tr>

<td rowspan="2">
DPS Reply
</td>


<td>
Reply
</td>


<td>


@switch($data->dps)


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


</td>


</tr>



<tr>

<td>
Index
</td>

<td>
{{ $dpsIdx }}
</td>
</tr>

<tr class="total-row">
<td>
Total Score ( 100 - Total Index)
</td>

<td>
Grade/Rank {{ $deliveryGrade }}
</td>


<td>

<b>
{{ intval($deliveryTotal) }}
</b>

</td>


</tr>


</table>

{{-- ================= CUSTOMER DISRUPTION & NOTIFICATION ================= --}}

<div class="section-title-table">
    CUSTOMER DISRUPTION & NOTIFICATION
</div>


<table class="detail-table">


<tr>

<td>
Delivery (PPM)
</td>


<td>
PPM
</td>


<td>
{{ $data->disruption_ppm ?? 0 }}
</td>


</tr>



<tr>

<td>
Delivered Part
</td>
<td>
Qty
</td>
<td>
{{ $qcData->supply ?? 0 }}
</td>


</tr>




<tr>

<td>
Returned Part
</td>


<td>
Qty
</td>


<td>
{{ $data->returnedPart ?? 0 }}
</td>


</tr>




<tr class="total-row">


<td>
Grade / Rank
</td>


<td></td>


<td>

<b>

{{ ($data->returnedPart ?? 0) > 0 ? 'C' : 'A' }}

</b>

</td>


</tr>


</table>

{{-- ================= QUALITY PROBLEM ================= --}}

<div class="problem-header-bar">
    QUALITY PROBLEM
</div>

<table class="problem-table">


<tr>

<th>
Part No
</th>

<th>
Part Name
</th>

<th>
Delivery
</th>

<th>
NG
</th>

<th>
Problem
</th>

</tr>



@php
$qualityList = parseProblemData($data->qualityProblems ?? null);
@endphp


@if(count($qualityList) > 0)


@if(isset($qualityList[0]['note']))


<tr>

<td>
{{ $qualityList[0]['note'] }}
</td>

<td></td>
<td></td>
<td></td>
<td></td>

</tr>


@else


@foreach($qualityList as $p)


<tr>

<td>
{{ $p['partNo'] ?? '-' }}
</td>


<td>
{{ $p['partName'] ?? '-' }}
</td>


<td>
{{ $p['delivery'] ?? '-' }}
</td>


<td>
{{ $p['ng'] ?? '-' }}
</td>


<td>
{{ $p['problem'] ?? '-' }}
</td>


</tr>


@endforeach


@endif



@else


<tr>

<td>
Nothing Problem
</td>

<td></td>
<td></td>
<td></td>
<td></td>


</tr>


@endif


</table>





{{-- ================= DELIVERY PROBLEM ================= --}}

<div class="problem-header-bar">
    DELIVERY PROBLEM
</div>


<table class="problem-table">


<tr>

<th>
Part No
</th>


<th>
Part Name
</th>


<th colspan="3">
Remark
</th>


</tr>



@php
$deliveryList = parseProblemData($data->problems ?? null);
@endphp



@if(count($deliveryList) > 0)



@if(isset($deliveryList[0]['note']))



<tr>


<td>
{{ $deliveryList[0]['note'] }}
</td>

<td></td>

<td colspan="3"></td>


</tr>



@else



@foreach($deliveryList as $p)


<tr>


<td>
{{ $p['partNo'] ?? '-' }}
</td>


<td>
{{ $p['partName'] ?? '-' }}
</td>


<td colspan="3">
{{ $p['remark'] ?? '-' }}
</td>


</tr>



@endforeach



@endif



@else



<tr>


<td>
Nothing Problem
</td>

<td></td>

<td colspan="3"></td>


</tr>



@endif



</table>

{{-- ===================== LEGEND / GRADE TABLES ===================== --}}

<table style="width:100%; border-collapse:collapse; margin-top:2px;">
<tr>

<td style="width:50%; vertical-align:top; border:none;">

    <table class="grade-table" style="width:100%;">
        <tr>
            <th colspan="2">QUALITY GRADE</th>
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

<td style="width:50%; vertical-align:top; border:none;">

    <table class="grade-table" style="width:100%;">
        <tr>
            <th colspan="2">DELIVERY GRADE</th>
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

{{-- ===================== CRITERIA SCORE ===================== --}}

<table class="criteria-wrapper" style="width:100%; table-layout:fixed;">
<tr>

<td style="width:50%; vertical-align:top;">
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
    <td colspan="2"><b>LINE STOP</b></td>
    <td rowspan="3">40%</td>

    <td colspan="2"><b>RANK</b></td>
    <td rowspan="4">25%</td>
</tr>

<tr>
    <td class="left-text">a.) YA</td>
    <td>40 Point</td>

    <td class="left-text">a.) A</td>
    <td>25 Point</td>
</tr>

<tr>
    <td class="left-text">b.) TIDAK</td>
    <td>0 Point</td>

    <td class="left-text">b.) B</td>
    <td>10 Point</td>
</tr>

<tr>
    <td colspan="2"><b>PPM</b></td>
    <td rowspan="5">15%</td>

    <td class="left-text">c.) C</td>
    <td>5 Point</td>
</tr>

<tr>
    <td class="left-text">a.) ZERO PPM</td>
    <td>0 Point</td>

    <td colspan="2"><b>FPPK REPLY</b></td>
    <td rowspan="4">20%</td>
</tr>

<tr>
    <td class="left-text">b.) 1 ~ 20 PPM (Target)</td>
    <td>5 Point</td>

    <td class="left-text">a.) On Time</td>
    <td>0 Point</td>
</tr>

<tr>
    <td class="left-text">c.) 21 ~ 200 PPM</td>
    <td>10 Point</td>

    <td class="left-text">b.) Delay</td>
    <td>10 Point</td>
</tr>

<tr>
    <td class="left-text">d.) > 200 PPM</td>
    <td>15 Point</td>

    <td class="left-text">c.) No Reply</td>
    <td>20 Point</td>
</tr>


</table>


</td>

</tr>

</table>

<td style="width:50%; vertical-align:top;">
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

</div>
</div>