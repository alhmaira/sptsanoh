@extends('layouts.app')

@section('title', 'Edit QC')
@section('page_title', 'Edit QC')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/qchistory.css') }}">
    <link rel="stylesheet" href="{{ asset('css/manageuser.css') }}">
@endpush

@section('content')
<form method="POST" action="/qc/update/{{ $qc->id }}">
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-title">Edit QC Data</div>

        <div class="form-grid">
            <div class="input-group">
                <label>Doc Number</label>
                <input type="text" value="{{ $qc->docNumber }}" disabled>
            </div>

            <div class="input-group">
                <label>Supplier</label>
                <input type="text" value="{{ $qc->supplier }}" disabled>
            </div>
        </div>

        <div class="form-grid">
            <div class="input-group">
                <label>Line Stop</label>
                <select name="lineStop" id="lineStop" onchange="calcQC()">
                    <option value="0" {{ $qc->lineStop == '0' ? 'selected' : '' }}>No</option>
                    <option value="40" {{ $qc->lineStop == '40' ? 'selected' : '' }}>Yes</option>
                </select>
            </div>

            <div class="input-group">
                <label>NG</label>
                <input type="number" name="ng" id="ng" value="{{ $qc->ng }}" oninput="calcQC()">
            </div>
        </div>

        <div class="form-grid">
            <div class="input-group">
                <label>Supply</label>
                <input type="number" name="supply" id="supply" value="{{ $qc->supply }}" oninput="calcQC()">
            </div>

            <div class="input-group">
                <label>PPM</label>
                <input type="text" id="ppm" name="ppm" value="{{ $qc->ppm }}" readonly>
            </div>
        </div>

        <div class="form-grid">
            <div class="input-group">
                <label>PPM Score</label>
                <input type="text" id="ppmScore" name="ppmScore" value="{{ $qc->ppmScore }}" readonly>
            </div>

            <div class="input-group">
                <label>Problem Rank</label>
                <select name="rank_score" id="rank_score" onchange="calcQC()">
                    <option value="0" {{ $qc->rank_score == '0' ? 'selected' : '' }}>No Problem</option>
                    <option value="25" {{ $qc->rank_score == '25' ? 'selected' : '' }}>A</option>
                    <option value="10" {{ $qc->rank_score == '10' ? 'selected' : '' }}>B</option>
                    <option value="5" {{ $qc->rank_score == '5' ? 'selected' : '' }}>C</option>
                </select>
            </div>
        </div>

        <div class="form-grid">

            <div class="input-group">
                <label>FPPK</label>
                <select name="fppk" id="fppk" onchange="calcQC()">
                    <option value="0" {{ $qc->fppk == '0' ? 'selected' : '' }}>No Problem</option>
                    <option value="10" {{ $qc->fppk == '10' ? 'selected' : '' }}>Delay</option>
                    <option value="20" {{ $qc->fppk == '20' ? 'selected' : '' }}>No Reply</option>
                </select>
            </div>

            <div class="input-group">

                <label>Quality Problem</label>

                <div class="radio-group">

                    <label>
                        <input type="radio"
                            name="has_problem"
                            value="yes"
                            {{ $qc->has_problem == 'yes' ? 'checked' : '' }}
                            onchange="toggleQCProblem()">
                        Yes
                    </label>

                    <label>
                        <input type="radio"
                            name="has_problem"
                            value="no"
                            {{ $qc->has_problem != 'yes' ? 'checked' : '' }}
                            onchange="toggleNoQCProblem()">
                        No
                    </label>

                </div>

            </div>

            <div id="qc-problem-container" class="problem-container" style="display:none; grid-column:1/-1;">
                <div id="qc-problem-list"></div>

                <button type="button"
                        class="add-problem-btn"
                        onclick="addQCProblemRow()">
                    + Add Problem
                </button>
            </div>

        </div>

        <div class="form-grid">

            <div class="input-group" style="grid-column:1/-1;">
                <label>Total Score</label>
                <input type="text"
                    id="total_score"
                    name="total_score"
                    value="{{ $qc->total_score }}"
                    readonly>
            </div>

        </div>

        <div style="display:flex; gap:12px; margin-top:20px;">
            <button type="submit" class="add-btn">
                <i class="fa-solid fa-floppy-disk"></i>
                Save
            </button>

            <a href="/qc/history" class="add-btn" style="background:#6b7280; text-decoration:none;">
                Cancel
            </a>
        </div>

    </div>

</form>

@push('scripts')
<script>
// Data qualityProblems yang sudah tersimpan di database, dikirim dari controller (edit())
const existingQCProblems = @json($qc->qualityProblems ?? []);

function calcQC() {
    const lineStop = Number(document.getElementById("lineStop").value) || 0;
    const ng = Number(document.getElementById("ng").value) || 0;
    const supply = Number(document.getElementById("supply").value) || 0;
    const rankScore = Number(document.getElementById("rank_score").value) || 0;
    const fppk = Number(document.getElementById("fppk").value) || 0;

    let ppmScore = 0;

    if (supply > 0) {
        let ppm = (ng / supply) * 1000000;
        document.getElementById("ppm").value = Math.round(ppm);

        if (ppm === 0)       ppmScore = 0;
        else if (ppm <= 20)  ppmScore = 5;
        else if (ppm <= 200) ppmScore = 10;
        else                 ppmScore = 15;

        document.getElementById("ppmScore").value = ppmScore;
    }

    const total = 100 - (lineStop + ppmScore + rankScore + fppk);
    document.getElementById("total_score").value = Math.max(0, Math.min(100, total)).toFixed(1);
}

calcQC();

let qcProblemIndex = 0;

function toggleQCProblem(){

    document.getElementById("qc-problem-container").style.display = "block";

    if(document.getElementById("qc-problem-list").children.length === 0){
        addQCProblemRow();
    }

}

function toggleNoQCProblem(){

    document.getElementById("qc-problem-container").style.display = "none";
    document.getElementById("qc-problem-list").innerHTML = "";
    qcProblemIndex = 0;

}

// data (opsional) dipakai untuk prefill baris dari data lama (existingQCProblems)
function addQCProblemRow(data = {}){

    const partNo    = data.partNo    ?? '';
    const partName  = data.partName  ?? '';
    const delivery  = data.delivery  ?? '';
    const ng        = data.ng        ?? '';
    const problem   = data.problem   ?? '';

    const html=`
    <div class="problem-item">

        <div class="problem-row">

            <div class="problem-field">
                <label>Part No</label>
                <input type="text"
                       name="qualityProblems[${qcProblemIndex}][partNo]"
                       value="${partNo}">
            </div>

            <div class="problem-field">

                <div class="problem-header">

                    <label>Part Name</label>

                    <button type="button"
                            class="delete-problem-btn"
                            onclick="this.closest('.problem-item').remove()">
                        <i class="fa-solid fa-trash"></i>
                    </button>

                </div>

                <input type="text"
                    name="qualityProblems[${qcProblemIndex}][partName]"
                    value="${partName}">

            </div>

            <div class="problem-field">
                <label>Delivery</label>
                <input type="text"
                       name="qualityProblems[${qcProblemIndex}][delivery]"
                       value="${delivery}">
            </div>

            <div class="problem-field">
                <label>NG</label>
                <input type="text"
                       name="qualityProblems[${qcProblemIndex}][ng]"
                       value="${ng}">
            </div>

            <div class="problem-field" style="grid-column:1/-1;">
                <label>Problem</label>
                <textarea
                    name="qualityProblems[${qcProblemIndex}][problem]">${problem}</textarea>
            </div>

        </div>

    </div>`;

    document.getElementById("qc-problem-list")
        .insertAdjacentHTML("beforeend",html);

    qcProblemIndex++;

}

window.onload = function () {
    calcQC();
    if(document.querySelector('input[name="has_problem"]:checked')?.value === "yes"){

        document.getElementById("qc-problem-container").style.display = "block";

        if(Array.isArray(existingQCProblems) && existingQCProblems.length > 0){
            existingQCProblems.forEach(function(item){
                addQCProblemRow(item);
            });
        } else {
            addQCProblemRow();
        }

    }else{
        toggleNoQCProblem();
    }
}

</script>
@endpush

@endsection