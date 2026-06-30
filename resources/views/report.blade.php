@extends('layouts.app')

@section('title', 'Summary Report')
@section('page_title', 'Print Report')

@push('head')
<link rel="stylesheet" href="{{ asset('css/report.css') }}">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@endpush

@section('content')

<div class="filter-box">

    <div class="dropdown">
        <input
            type="text"
            id="supplierSearch"
            placeholder="Search Supplier..."
            autocomplete="off">

        <div
            id="supplierDropdown"
            class="dropdown-list">
        </div>
    </div>

    <select id="month">
        <option value="">All Month</option>

        @foreach(range(1,12) as $m)
            <option value="{{ sprintf('%02d',$m) }}">
                {{ date('M', mktime(0,0,0,$m,1)) }}
            </option>
        @endforeach

    </select>

    <select id="year"></select>

</div>


<div
    id="reportArea"
    class="report-box">
</div>


<div
    id="pdfReport"
    style="display:none">
</div>

<div class="action-bar" style="margin-top:15px;">
    <button
        type="button"
        id="printSummary"
        class="print-btn">
        <i class="fas fa-print"></i>
        Print Report
    </button>
</div>

@endsection

@push('scripts')

<script>
    const REPORT_URL = "{{ route('report.data') }}";
    const DETAIL_URL = "{{ url('/report/detail') }}";

    function initYear() {

        const year = document.getElementById("year");
        year.innerHTML = "";

        const current = new Date().getFullYear();

        for (let i = current - 3; i <= current + 2; i++) {

            year.innerHTML += `
                <option value="${i}" ${i === current ? "selected" : ""}>
                    ${i}
                </option>
            `;
        }
    }
</script>

<script src="{{ asset('js/report.js') }}"></script>

@endpush