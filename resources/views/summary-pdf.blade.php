<!DOCTYPE html>
<html>

<head>

<style>

@page {
    size: A4;
    margin: 75px 25px 25px 25px;
}


body{
    font-family: "DejaVu Sans", sans-serif;
    font-size:10px;
    color:#222;
}


.header{

    width:100%;
    padding-bottom:10px;
    margin-bottom:20px;

}


.logo{

    width:120px;

}


.header-title{

    text-align:center;
    font-size:18px;
    font-weight:bold;
    letter-spacing:0.5px;

}


.period{

    font-size:12px;
    text-align:center;
    margin-top:5px;
    font-weight:normal;

}


.date{

    text-align:right;
    font-size:10px;

}


table{

    width:100%;
    border-collapse:collapse;

}


th,td{

    border:1px solid #555;
    padding:6px;
    text-align:center;
    font-size:9px;

}


th{

    font-weight:bold;
}


.supplier{

    text-align:left;
    font-weight:500;

}



.approval{

    margin-top:30px;
    text-align:center;

}

.approval-table{

    width:50%;
    margin:auto;

}


.approval-table td{

    height:80px;
    vertical-align:top;
    padding:4px;

}


.role{

    font-weight:bold;
    font-size:9px;
    margin-top:5px;

}


.signature{

    height:35px;

}

.signature img{

    width:80px !important;
    height:40px !important;

}



.line{

    width:150px;
    border-bottom:1px solid black;
    margin:auto;

}



.name{

    margin-top:8px;
    font-size:9px;
    font-weight:bold;

}


</style>

</head>


<body>



<!-- HEADER -->

<table class="header">

<tr>


<td style="border:none;width:25%">


<img 
src="{{ public_path('images/sanohlogo.png') }}"
class="logo">


</td>



<td style="border:none;width:50%">


<div class="header-title">

RESUME SUPPLIER PERFORMANCE

</div>


<div class="period">

{{ $period }}

</div>


</td>



<td style="border:none;width:25%" class="date">


Date :

{{ now()->format('d F Y') }}

</td>


</tr>


</table>






<table>


<tr>


<th rowspan="2">
NO
</th>


<th rowspan="2">
SUPPLIER
</th>



<th colspan="3">
DELIVERY PERFORMANCE
</th>


<th colspan="3">
QUALITY PERFORMANCE
</th>


<th rowspan="2">
AVERAGE SCORE
</th>



</tr>



<tr>


<th>
SCORE
</th>


<th>
LEVEL
</th>


<th>
RANK
</th>



<th>
SCORE
</th>


<th>
LEVEL
</th>


<th>
RANK
</th>



</tr>




@foreach($data as $i=>$d)


<tr>


<td>

{{ $i+1 }}

</td>



<td class="supplier">

{{ $d->supplier }}

</td>




<td>

{{ number_format($d->delivery_score,2) }}

</td>



<td>

{{ $d->delivery_score >=80 ? 'A':'B' }}

</td>



<td>

{{ $d->delivery_rank }}

</td>




<td>

{{ number_format($d->qc_score,2) }}

</td>




<td>

{{ $d->qc_score >=80 ? 'A':'B' }}

</td>



<td>

{{ $d->quality_rank }}

</td>




<td>


{{ number_format(
($d->delivery_score + $d->qc_score) / 2,
2
) }}


</td>



</tr>



@endforeach



</table>



<br>


<!-- BEST SUPPLIER TABLE -->

<table>


<tr>

<th rowspan="2" style="width:15%">
PERIOD
</th>


<th colspan="2">
DELIVERY PERFORMANCE
</th>


<th colspan="2">
QUALITY PERFORMANCE
</th>


</tr>



<tr>


<th>
RANK
</th>


<th>
SUPPLIER
</th>


<th>
RANK
</th>


<th>
SUPPLIER
</th>



</tr>




<tr>


<td rowspan="3">

THE BEST ON
<br>
{{ $period }}

</td>




<td>
1
</td>


<td class="supplier">

{{ $bestDelivery[0]->supplier ?? '-' }}

</td>



<td>
1
</td>


<td class="supplier">

{{ $bestQuality[0]->supplier ?? '-' }}

</td>



</tr>




<tr>



<td>
2
</td>


<td class="supplier">

{{ $bestDelivery[1]->supplier ?? '-' }}

</td>



<td>
2
</td>


<td class="supplier">

{{ $bestQuality[1]->supplier ?? '-' }}

</td>



</tr>





<tr>



<td>
3
</td>


<td class="supplier">

{{ $bestDelivery[2]->supplier ?? '-' }}

</td>



<td>
3
</td>


<td class="supplier">

{{ $bestQuality[2]->supplier ?? '-' }}

</td>



</tr>



</table>

<br>


<!-- WORST SUPPLIER TABLE -->

<table>


<tr>

<th rowspan="2" style="width:15%">
PERIOD
</th>


<th colspan="2">
DELIVERY PERFORMANCE
</th>


<th colspan="2">
QUALITY PERFORMANCE
</th>


</tr>



<tr>


<th>
RANK
</th>


<th>
SUPPLIER
</th>


<th>
RANK
</th>


<th>
SUPPLIER
</th>


</tr>



<tr>


<td rowspan="3">

THE WORST ON
<br>
{{ $period }}

</td>




<td>
1
</td>


<td class="supplier">

{{ $worstDelivery[0]->supplier ?? '-' }}

</td>




<td>
1
</td>


<td class="supplier">

{{ $worstQuality[0]->supplier ?? '-' }}

</td>



</tr>




<tr>


<td>
2
</td>


<td class="supplier">

{{ $worstDelivery[1]->supplier ?? '-' }}

</td>



<td>
2
</td>


<td class="supplier">

{{ $worstQuality[1]->supplier ?? '-' }}

</td>



</tr>





<tr>


<td>
3
</td>


<td class="supplier">

{{ $worstDelivery[2]->supplier ?? '-' }}

</td>



<td>
3
</td>


<td class="supplier">

{{ $worstQuality[2]->supplier ?? '-' }}

</td>



</tr>

</table>

<!-- APPROVAL -->

<div class="approval">


<p>
Approved By,
</p>



<table class="approval-table">


<tr>


<td>


<div class="role">

MANAGER PURCHASING

</div>



<div class="signature">


@if($managerPurch && $managerPurch->signature)

    @php
        $managerSign = storage_path('app/public/'.$managerPurch->signature);
    @endphp

    <img 
    src="data:image/png;base64,{{ base64_encode(file_get_contents($managerSign)) }}"
    style="width:120px;height:60px;object-fit:contain;">

@endif

</div>



<div class="line">

</div>



<div class="name">

{{ $managerPurch->name ?? '-' }}

</div>


</td>





<td>


<div class="role">

LEADER PURCHASING

</div>



<div class="signature">


@if($leaderPurch && $leaderPurch->signature)

    @php
        $leaderSign = storage_path('app/public/'.$leaderPurch->signature);
    @endphp

    <img 
    src="data:image/png;base64,{{ base64_encode(file_get_contents($leaderSign)) }}"
    style="width:120px;height:60px;object-fit:contain;">

@endif


</div>




<div class="line">

</div>



<div class="name">

{{ $leaderPurch->name ?? '-' }}

</div>



</td>



</tr>



</table>



</div>



</body>

</html>