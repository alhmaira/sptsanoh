@extends('layouts.app')

@section('title', 'SPT - Approval Workflow')

@section('page_title', 'Approval Workflow')

@push('head')
<link rel="stylesheet" href="{{ asset('css/approval.css') }}">
<link rel="stylesheet" href="{{ asset('css/delivery.css') }}">
<style>
#editDeliveryModal .popup-box{
    width:1100px;
    max-width:95vw;
    max-height:90vh;
    overflow-y:auto;
    text-align:left;
    padding:24px 28px;
}

#editDeliveryModal input[readonly]{
    background:#f8fafc;
    color:#555;
    cursor:not-allowed;
}

#editDeliveryModal select:disabled{
    background:#f8fafc;
    color:#555;
}

#editDeliveryModal .btn-save:first-child{
    width:180px;
}

#editDeliveryModal .btn-save:last-child{
    flex:1;
}

#editDeliveryModal .modal-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

#editDeliveryModal .modal-header .section-title{
    margin:0;
    font-size:18px;
    font-weight:700;
    color:#1e2a45;
}

#editDeliveryModal .modal-close-btn{
    width:34px;
    height:34px;

    display:flex;
    justify-content:center;
    align-items:center;

    border:none;
    border-radius:50%;

    background:transparent;
    color:#6b7280;

    cursor:pointer;

    font-size:25px;

    transition:.2s;
}

#editDeliveryModal .modal-close-btn:hover{
    background:#f3f4f6;
    color:#111827;
}
</style>
@endpush

@section('content')

<div id="mainContent">

    <div class="filter-box">

        <input
            type="text"
            id="searchDoc"
            class="search-input"
            placeholder="Search Doc No..."
            onkeyup="searchDocument(this.value)">

        <select onchange="setFilterStatus(this.value)">
            <option value="ALL">All Status</option>
            <option value="PROGRESS">Progress</option>
            <option value="WAITING">Waiting</option>
            <option value="APPROVED">Approved</option>
        </select>

        <select id="filterSupplier" onchange="setFilterSupplier(this.value)">
            <option value="ALL">All Supplier</option>
        </select>

    </div>

    <table class="history-table">

        <thead>
            <tr>
                <th>Doc No</th>
                <th>Supplier</th>
                <th>Period</th>
                <th>Approved By</th>
                <th>
                    Submitted On
                    <i class="fa-solid fa-sort sort-icon" onclick="toggleSortDate()"></i>
                </th>
                <th>Department</th>
                <th>Status</th>
                <th>Detail</th>
            </tr>
        </thead>

        <tbody id="purchase-list"></tbody>

    </table>

    <div class="table-footer">
        <div class="show-entries">
            Show
            <select onchange="changeRowsPerPage(this.value)">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            entries
        </div>
        <div id="pagination" class="pagination"></div>
    </div>

</div>

<div id="detailCard" class="panel-card detail-card" style="display:none;">
    <div id="detail-section"></div>
</div>

{{-- Confirm Modal --}}
<div id="confirmModal" class="popup">
    <div class="popup-box">
        <p id="confirmText"></p>
        <div style="display:flex; gap:10px; justify-content:center;">
            <button onclick="closeConfirm()">Cancel</button>
            <button id="yesBtn">Yes</button>
        </div>
    </div>
</div>

{{-- Success Popup --}}
<div id="successPopup" class="popup">
    <div class="popup-box">
        <p id="successText"></p>
        <button onclick="closeSuccess()">OK</button>
    </div>
</div>

{{-- Failed Popup --}}
<div id="failedPopup" class="popup" style="display:none;">
    <div class="popup-box">
        <p id="failedText"></p>
        <button onclick="closeFailedPopup()">OK</button>
    </div>
</div>

{{-- Edit Delivery Modal --}}
<div id="editDeliveryModal" class="popup" style="display:none;">

    <div class="popup-box form-card" style="width:1100px;max-width:95vw;text-align:left;">

    <div class="modal-header">
        <div class="section-title">
            Edit Delivery Data
        </div>

        <button
            type="button"
            class="modal-close-btn"
            onclick="closeEditDelivery()">
            ×</button>
    </div>

        <div class="grid2">

            {{-- DOC NUMBER --}}
            <div class="field">
                <label>Document Number</label>
                <input type="text"
                       id="edDocNumber"
                       readonly>
            </div>

            {{-- SUPPLIER --}}
            <div class="field">
                <label>Supplier</label>
                <input type="text"
                       id="edSupplier"
                       readonly>
            </div>

            {{-- PERIOD --}}
            <div class="field">
                <label>Period</label>
                <input type="text"
                       id="edPeriod"
                       readonly>
            </div>

            {{-- OTD --}}
            <div class="field">
                <label>On-Time Delivery</label>

                <input type="text"
                       id="edOtdFrozen"
                       readonly>
            </div>

            {{-- QTY ORDER --}}
            <div class="field">
                <label>Quantity Ordered</label>

                <input type="text"
                       id="edQtyOrd"
                       readonly>
            </div>

            {{-- QTY RECEIVED --}}
            <div class="field">
                <label>Quantity Received</label>

                <input type="text"
                       id="edQtyRec"
                       readonly>
            </div>

            {{-- FULFILLMENT --}}
            <div class="field">
                <label>Order Fulfillment (%)</label>

                <input type="text"
                       id="edFulfillment"
                       readonly>
            </div>

            {{-- DELIVERY METHOD --}}
            <div class="field">
                <label>Delivery Method</label>

                <select id="edDelMethod"
                        onchange="recalcTotalScore()">

                    <option value="0">Normal</option>
                    <option value="4">Abnormal</option>

                </select>
            </div>

            {{-- PREMIUM --}}
            <div class="field">

                <label>Premium Freight (Rp)</label>

                <input type="number"
                       id="edPremium"
                       min="0"
                       oninput="recalcTotalScore()">

            </div>

            {{-- DPS --}}
            <div class="field">

                <label>DPS Reply</label>

                <select id="edDps"
                        onchange="recalcTotalScore()">

                    <option value="0">No Problem</option>
                    <option value="5">On Time</option>
                    <option value="10">Delay</option>
                    <option value="20">No Reply</option>

                </select>

            </div>

        </div>

        <div class="section-divider"></div>

        <div class="section-title">
            Total Score
        </div>

        <div class="total-box">

            <div
                class="total-num"
                id="edTotalScore">

                —

            </div>

        </div>

        <button
            class="btn-save"
            type="button"
            onclick="submitEditDelivery()">

            Save Data

        </button>

        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>

/* =========================
   USER
========================= */
const currentUser = {
    name:       "{{ Auth::user()->name }}",
    role:       "{{ Auth::user()->role }}",
    department: "{{ Auth::user()->department }}"
};

const canEditQC       = @json($canEditQC);
const canEditDelivery = @json($canEditDelivery);
const editPerms       = @json($editPerms);

/* =========================
   APPROVAL FLOW
========================= */
const approvalFlow = [
    { role: "Supervisor",      dept: "Quality Control" },
    { role: "Manager",         dept: "Quality Control" },
    { role: "Supervisor",      dept: "PPIC" },
    { role: "Manager",         dept: "PPIC" },
    { role: "Leader",          dept: "Purchasing" },
    { role: "Manager",         dept: "Purchasing" },
    { role: "General Manager", dept: "Production" }
];

/* =========================
   STATE
========================= */
let currentSearchDoc      = "";
let currentFilterStatus   = "ALL";
let currentFilterSupplier = "ALL";
let currentSortDate       = "DESC";
let selectedDoc           = null;
let currentPage           = 1;
let rowsPerPage           = 10;
let editDeliveryDocNumber = null;
let editDeliveryId        = null;

/* =========================
   DATA
========================= */
let qcRaw       = @json($qcData);
let deliveryRaw = @json($deliveryData);
let approvalRaw = @json($approvalData);
let historyRaw  = @json($historyData);

/* =========================
   NORMALIZE
========================= */
function normalizeQC(d) {
    return { ...d, docNumber: d.docNumber || "-", supplier: d.supplier || "-" };
}
function normalizeDEL(d) {
    return { ...d, docNumber: d.docNumber || "-" };
}

qcRaw       = qcRaw.map(normalizeQC);
deliveryRaw = deliveryRaw.map(normalizeDEL);

/* =========================
   JOIN
========================= */
const qcData = qcRaw.map(q => {
    const delivery = deliveryRaw.find(d => String(d.docNumber) === String(q.docNumber));
    return {
        ...q,
        delivery: delivery || {},
        period:   formatPeriod(q.del_month, q.del_year)
    };
});

/* =========================
   SUPPLIER FILTER
========================= */
const supplierSelect  = document.getElementById("filterSupplier");
const uniqueSuppliers = [...new Set(qcData.map(d => d.supplier))];

uniqueSuppliers.forEach(s => {
    const opt       = document.createElement("option");
    opt.value       = s;
    opt.textContent = s;
    supplierSelect.appendChild(opt);
});

/* =========================
   WORKFLOW
========================= */
function getWF(doc) {
    const wf      = approvalRaw.find(a => String(a.doc_number) === String(doc));
    const history = historyRaw.filter(h => String(h.doc_number) === String(doc));
    return {
        step:               wf?.current_step || 0,
        status:             wf?.status || "WAITING",
        current_department: wf?.current_department || "-",
        history:            history
    };
}

function getCurrentUserStep() {
    return approvalFlow.findIndex(s =>
        s.role === currentUser.role &&
        s.dept === currentUser.department
    );
}

function getUserStatus(doc) {
    const wf       = getWF(doc);
    const userStep = getCurrentUserStep();

    if (wf.status === "APPROVED") return "APPROVED";

    const alreadyApproved = wf.history.some(h => h.user_name === currentUser.name);
    if (alreadyApproved) return "APPROVED";

    if (wf.step === userStep) return "WAITING";

    return "PROGRESS";
}

function getStatus(doc) {
    return getWF(doc).status;
}

function getStatusLabel(doc) {
    const wf = getWF(doc);
    if (wf.status === "APPROVED") return "Completed";
    const step = approvalFlow[wf.step];
    return `${step.role} - ${step.dept}`;
}

function hasCurrentUserApproved(doc) {
    const wf = getWF(doc);
    return wf.history.some(h => h.user_name === currentUser.name);
}

function canEditQCForDoc(doc) {
    if (!canEditQC) return false;
    const wf = getWF(doc);
    if (wf.status === 'APPROVED') return false;
    if (hasCurrentUserApproved(doc)) return false;
    return true;
}

function canEditDeliveryForDoc(doc) {
    if (!canEditDelivery) return false;
    const wf = getWF(doc);
    if (wf.status === 'APPROVED') return false;
    if (hasCurrentUserApproved(doc)) return false;
    return true;
}

/* =========================
   APPROVE
========================= */
async function setStatus(doc, action) {
    try {
        const response = await fetch("/approval/update", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ doc_number: doc, action: action })
        });

        const text   = await response.text();
        const result = JSON.parse(text);

        if (result.success) {
            approvalRaw = result.approvals;
            historyRaw  = result.histories;
            closeConfirm();
            renderTable();
            showDetail(doc);
            showSuccess(`Document ${doc} ${action}`);
        } else {
            showFailed(result.message || "Failed to update approval.");
        }

    } catch (err) {
        console.error(err);
        showFailed("Failed to update approval. Please try again.");
    }
}

/* =========================
   FILTER
========================= */
function searchDocument(v)    { currentSearchDoc = v.toLowerCase(); currentPage = 1; renderTable(); }
function setFilterStatus(v)   { currentFilterStatus = v;   currentPage = 1; renderTable(); }
function setFilterSupplier(v) { currentFilterSupplier = v; currentPage = 1; renderTable(); }
function toggleSortDate()     { currentSortDate = currentSortDate === "DESC" ? "ASC" : "DESC"; renderTable(); }
function changeRowsPerPage(v) { rowsPerPage = parseInt(v); currentPage = 1; renderTable(); }
function changePage(page)     { currentPage = page; renderTable(); }

/* =========================
   UTIL
========================= */
function formatPeriod(month, year) {
    if (!month || !year) return "-";
    const monthNames = ["January","February","March","April","May","June",
                        "July","August","September","October","November","December"];
    return `${monthNames[month - 1]} ${year}`;
}

function formatDate(date) {
    if (!date) return "-";
    const d = new Date(date);
    return `${String(d.getMonth()+1).padStart(2,'0')}/${String(d.getDate()).padStart(2,'0')}/${d.getFullYear()} `
         + `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}:${String(d.getSeconds()).padStart(2,'0')}`;
}

function getUserBadge(status) {
    if (status === "APPROVED") return `<span class="status-badge completed"></span>`;
    if (status === "PROGRESS") return `<span class="status-badge in-progress"></span>`;
    return `<span class="status-badge waiting"></span>`;
}

/* =========================
   TABLE
========================= */
function renderTable() {
    const tbody = document.getElementById("purchase-list");
    tbody.innerHTML = "";

    let filtered = qcData.filter(d => {
        const userStatus = getUserStatus(d.docNumber);
        return (
            (currentFilterStatus === "ALL"      || userStatus === currentFilterStatus)
            && (currentFilterSupplier === "ALL" || d.supplier === currentFilterSupplier)
            && (!currentSearchDoc || String(d.docNumber).toLowerCase().includes(currentSearchDoc))
        );
    });

    filtered.sort((a, b) => {
        const aDate = new Date(a.created_at).getTime();
        const bDate = new Date(b.created_at).getTime();
        return currentSortDate === "DESC" ? bDate - aDate : aDate - bDate;
    });

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8">No Data</td></tr>`;
        document.getElementById("pagination").innerHTML = "";
        return;
    }

    const totalPages = Math.ceil(filtered.length / rowsPerPage);
    if (currentPage > totalPages) currentPage = 1;

    const start         = (currentPage - 1) * rowsPerPage;
    const paginatedData = filtered.slice(start, start + rowsPerPage);

    paginatedData.forEach(d => {
        const wf         = getWF(d.docNumber);
        const userStatus = getUserStatus(d.docNumber);
        const row        = document.createElement("tr");

        row.innerHTML = `
            <td>${d.docNumber}</td>
            <td>${d.supplier}</td>
            <td>${d.period}</td>
            <td>${wf.history.length ? wf.history.map(h => h.user_name).join(", ") : "-"}</td>
            <td>${formatDate(d.created_at)}</td>
            <td>${getStatusLabel(d.docNumber)}</td>
            <td>${getUserBadge(userStatus)}</td>
            <td>
                <button class="detail-btn eye-btn" onclick="selectRow('${d.docNumber}')">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </td>
        `;

        tbody.appendChild(row);
    });

    renderPagination(totalPages);
}

/* =========================
   PAGINATION
========================= */
function renderPagination(totalPages) {
    let html = `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>&lt;</button>`;

    for (let i = 1; i <= totalPages; i++) {
        html += `<button class="${i === currentPage ? 'active-page' : ''}" onclick="changePage(${i})">${i}</button>`;
    }

    html += `<button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>&gt;</button>`;

    document.getElementById("pagination").innerHTML = html;
}

/* =========================
   DETAIL
========================= */
function selectRow(doc) {
    selectedDoc = doc;
    document.getElementById("mainContent").style.display = "none";
    document.getElementById("detailCard").style.display  = "block";
    showDetail(doc);
    window.scrollTo({ top: 0, behavior: "smooth" });
}

function getNotes(problems){

    if(!problems){
        return "Nothing Problem";
    }


    let data = problems;


    if(typeof problems === "string"){

        try{
            data = JSON.parse(problems);

        }catch(e){

            return "Nothing Problem";
        }

    }


    if(!Array.isArray(data) || data.length === 0){

        return "Nothing Problem";
    }



    if(data.length === 1 && data[0].note){

        return data[0].note;

    }



    return data.map((p,index)=>`

        <div class="problem-note">

            <b>Problem ${index+1}</b><br>

            Part No : ${p.partNo ?? '-'} <br>

            Part Name : ${p.partName ?? '-'} <br>

            ${p.delivery !== undefined 
                ? `Delivery : ${p.delivery}<br>` 
                : ''
            }

            ${p.ng !== undefined 
                ? `NG : ${p.ng}<br>` 
                : ''
            }

            Problem : ${p.problem ?? p.remark ?? '-'}

        </div>

    `).join("<hr>");

}

function showDetail(doc) {
    const container = document.getElementById("detail-section");
    const data      = qcData.find(x => String(x.docNumber) === String(doc));
    if (!data) return;

    const wf          = getWF(doc);
    const currentStep = approvalFlow[wf.step];
    const userStep    = getCurrentUserStep();

    const canApprove        = wf.step === userStep && wf.status !== "APPROVED";

    /* ---- DELIVERY HELPERS ---- */
    function getOTDText(value) {
        switch (String(value)) {
            case "0":  return "No Delay";
            case "2":  return "Delay 1 day";
            case "4":  return "Delay 2 days";
            case "6":  return "Delay 3 days";
            case "10": return "Delay > 3 days";
            default:   return "-";
        }
    }

    function getMethodText(value) {
        switch (String(value)) {
            case "0": return "Normal";
            case "4": return "Abnormal";
            default:  return "-";
        }
    }

    function getDPSText(value) {
        switch (String(value)) {
            case "0":  return "No Problem";
            case "5":  return "On Time";
            case "10": return "Delay";
            case "20": return "No Reply";
            default:   return "-";
        }
    }

    function calculateQtyIndex(fulfillment) {
        if (!fulfillment) return 0;
        const value = parseFloat(String(fulfillment).replace('%', ''));
        if (value >= 95) return 0;
        if (value >= 85) return 2;
        if (value >= 75) return 4;
        if (value >= 65) return 6;
        return 8;
    }

    function calculatePremiumIndex(premium) {
        premium = parseFloat(premium) || 0;
        if (premium > 3000000) return 8;
        if (premium > 1000000) return 6;
        if (premium > 500000)  return 4;
        if (premium > 0)       return 2;
        return 0;
    }

    function getRankLabel(score) {
        score = parseInt(score);
        if (score >= 40) return "Critical";
        if (score >= 30) return "Major";
        if (score >= 20) return "Minor";
        return "Good";
    }

    function getFppkLabel(score) {
        return parseInt(score) > 0 ? "NG" : "OK";
    }

    const del = data.delivery || {};

    /* ---- Build action buttons ---- */
    let actionButtons = '';

    if (canApprove) {
        actionButtons += `<button class="btn-approve" onclick="openConfirm('${doc}','APPROVED')">Approve</button>`;
    }

    container.innerHTML = `
    <div class="detail-header-top">
        <button onclick="backToList()" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>

        <div class="doc-title">${data.docNumber}</div>
        <div class="header-space"></div>
    </div>

    <div class="detail-subinfo">
        <div class="info-line">
            <span class="info-label">Supplier</span>
            <span class="info-value">${data.supplier || '-'}</span>
        </div>
        <div class="info-line">
            <span class="info-label">Period</span>
            <span class="info-value">${data.period}</span>
        </div>
    </div>

    <table class="detail-table">

        <!-- ================= QUALITY ================= -->
        <tr>
            <th colspan="3">QUALITY</th>
        </tr>

        <tr>
            <td rowspan="2">Line Stop</td>
            <td>Status</td>
            <td>${data.lineStop == 40 ? "YES" : "NO"}</td>
        </tr>
        <tr>
            <td>Index</td>
            <td><b>${data.lineStop ?? 0}</b></td>
        </tr>

        <tr>
            <td rowspan="4">PPM</td>
            <td>NG</td>
            <td>${data.ng ?? 0}</td>
        </tr>
        <tr>
            <td>Supply</td>
            <td>${data.supply ?? 0}</td>
        </tr>
        <tr>
            <td>PPM</td>
            <td>${data.ppm ?? 0}</td>
        </tr>
        <tr>
            <td>Index</td>
            <td><b>${data.ppmScore ?? 0}</b></td>
        </tr>

        <tr>
            <td rowspan="2">Problem Rank</td>
            <td>Rank</td>
            <td>${getRankLabel(data.rank_score)}</td>
        </tr>
        <tr>
            <td>Index</td>
            <td><b>${data.rank_score ?? 0}</b></td>
        </tr>

        <tr>
            <td rowspan="2">FPPK</td>
            <td>Status</td>
            <td>${getFppkLabel(data.fppk)}</td>
        </tr>
        <tr>
            <td>Index</td>
            <td><b>${data.fppk ?? 0}</b></td>
        </tr>

        <tr class="total-row">
    <td>Total Score</td>
    <td></td>
    <td><b>${parseInt(data.total_score ?? 0)}</b></td>
</tr>

<tr>
    <td colspan="3">

        <div class="notes-card">

            <div class="notes-title">
                Notes :
            </div>


            <div class="notes-content">

                ${getNotes(data.qualityProblems)}

            </div>

        </div>

    </td>
</tr>

        <!-- ================= DELIVERY ================= -->
        <tr>
            <th colspan="3">DELIVERY</th>
        </tr>

        <tr>
            <td rowspan="2">Fulfillment</td>
            <td>%</td>
            <td>${del.fulfillment ?? '-'}</td>
        </tr>
        <tr>
            <td>Index</td>
            <td><b>${calculateQtyIndex(del.fulfillment)}</b></td>
        </tr>

        <tr>
            <td rowspan="2">On Time Delivery</td>
            <td>Day</td>
            <td>${getOTDText(del.otd)}</td>
        </tr>
        <tr>
            <td>Index</td>
            <td><b>${del.otd ?? 0}</b></td>
        </tr>

        <tr>
            <td rowspan="2">Delivery Method</td>
            <td>Method</td>
            <td>${getMethodText(del.del_method)}</td>
        </tr>
        <tr>
            <td>Index</td>
            <td><b>${del.del_method ?? 0}</b></td>
        </tr>

        <tr>
            <td rowspan="2">Premium Freight</td>
            <td>IDR</td>
            <td>${del.premium ?? 0}</td>
        </tr>
        <tr>
            <td>Index</td>
            <td><b>${calculatePremiumIndex(del.premium)}</b></td>
        </tr>

        <tr>
            <td rowspan="2">DPS Reply</td>
            <td>Reply</td>
            <td>${getDPSText(del.dps)}</td>
        </tr>
        <tr>
            <td>Index</td>
            <td><b>${del.dps ?? 0}</b></td>
        </tr>

        <tr class="total-row">
    <td>Total Delivery</td>
    <td></td>
    <td><b>${parseInt(del.total_score || 0)}</b></td>
</tr>


<tr>
    <td colspan="3">


        <div class="notes-card">


            <div class="notes-title">

                Notes :

            </div>


            <div class="notes-content">


                ${getNotes(del.problems)}


            </div>


        </div>


    </td>
</tr>

    </table>

    ${actionButtons ? `<div class="action-box">${actionButtons}</div>` : ''}

    <div class="status-info">
        Current Status: <b>${getStatus(doc)}</b><br>
        Waiting approval from:
        <b>${currentStep?.role || "-"}</b> - ${currentStep?.dept || "-"}
    </div>
    `;
}

/* =========================
   SCORE HELPERS (global scope)
========================= */
function calcPremiumIndex(val) {
    val = parseFloat(val) || 0;
    if (val > 3000000) return 8;
    if (val > 1000000) return 6;
    if (val > 500000)  return 4;
    if (val > 0)       return 2;
    return 0;
}

/* =========================
   EDIT DELIVERY MODAL
========================= */
/*
 * openEditDelivery — hitung sQty dari fulfillment, simpan sQty + otd ke dataset.
 * Tidak butuh apiScore atau totalScoreDB sama sekali.
 * Fungsi ini tidak lagi dipanggil dari tombol manapun di halaman Approval
 * (tombol Edit Delivery sudah dihapus), dibiarkan tetap ada untuk jaga-jaga.
 */
function openEditDelivery(docNumber, deliveryId, supplier, period, qtyOrd, qtyRec, fulfillment, otd, delMethod, premium, dps) {
    editDeliveryDocNumber = docNumber;
    editDeliveryId        = deliveryId;

    document.getElementById('edDocNumber').value   = docNumber;
    document.getElementById('edSupplier').value    = supplier   || '-';
    document.getElementById('edPeriod').value      = period     || '-';
    document.getElementById('edQtyOrd').value      = qtyOrd     ?? '-';
    document.getElementById('edQtyRec').value      = qtyRec     ?? '-';
    document.getElementById('edFulfillment').value = fulfillment || '-';

    /* OTD dari API — tampilkan label, simpan nilai di data attribute */
    const otdLabels = { '0':'No Delay (0)', '2':'Delay 1 day (2)', '4':'Delay 2 days (4)', '6':'Delay 3 days (6)', '10':'Delay > 3 days (10)' };
    const otdEl = document.getElementById('edOtdFrozen');
    otdEl.textContent   = otdLabels[String(otd)] || '-';
    otdEl.dataset.value = String(otd);

    /* Hitung sQty dari fulfillment — sama persis logika delivery input */
    const fulfillmentNum = parseFloat(String(fulfillment).replace('%', '')) || 0;
    let sQty = 0;
    if      (fulfillmentNum >= 95) sQty = 0;
    else if (fulfillmentNum >= 85) sQty = 2;
    else if (fulfillmentNum >= 75) sQty = 4;
    else if (fulfillmentNum >= 65) sQty = 6;
    else                           sQty = 8;

    /* Simpan sQty + otd ke dataset — ini komponen API yang tidak bisa diubah */
    const modal = document.getElementById('editDeliveryModal');
    modal.dataset.sQty = String(sQty);
    modal.dataset.otd  = String(otd);

    document.getElementById('edDelMethod').value = String(delMethod ?? 0);
    document.getElementById('edPremium').value   = premium ?? 0;
    document.getElementById('edDps').value       = String(dps ?? 0);

    recalcTotalScore();

    modal.style.display = 'flex';
}

/*
 * recalcTotalScore — sama persis dengan formula di delivery input:
 *   total = 100 - (sQty + otd + method + sPrem + dps)
 *
 * sQty dan otd dari API (read-only), disimpan di modal.dataset.
 * method, sPrem, dps dari input user (manual).
 * Jika penalty bertambah → total BERKURANG. Jika penalty berkurang → total NAIK.
 */
function recalcTotalScore() {
    const modal = document.getElementById('editDeliveryModal');

    const sQty  = parseInt(modal.dataset.sQty  || '0');
    const otd   = parseInt(modal.dataset.otd   || '0');

    const delMethod = parseInt(document.getElementById('edDelMethod').value  || '0');
    const premium   = parseFloat(document.getElementById('edPremium').value) || 0;
    const dps       = parseInt(document.getElementById('edDps').value        || '0');

    const sPrem = calcPremiumIndex(premium);

    const total = 100 - (sQty + otd + delMethod + sPrem + dps);

    document.getElementById('edTotalScore').textContent = total < 0 ? 0 : total;
}

function closeEditDelivery() {
    document.getElementById('editDeliveryModal').style.display = 'none';
    editDeliveryDocNumber = null;
    editDeliveryId        = null;
}

async function submitEditDelivery() {
    if (!editDeliveryId) {
        showFailed('Delivery record not found.');
        return;
    }

    const otd        = document.getElementById('edOtdFrozen').dataset.value || '0';
    const delMethod  = document.getElementById('edDelMethod').value;
    const premium    = document.getElementById('edPremium').value;
    const dps        = document.getElementById('edDps').value;
    const totalScore = parseInt(document.getElementById('edTotalScore').textContent) || 0;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        const formData = new FormData();
        formData.append('_method',     'PUT');
        formData.append('_token',      csrfToken);
        formData.append('otd',         otd);
        formData.append('del_method',  delMethod);
        formData.append('premium',     premium);
        formData.append('dps',         dps);
        formData.append('total_score', totalScore);

        const response = await fetch(`/delivery/update/${editDeliveryId}`, {
            method: 'POST',
            body:   formData
        });

        if (response.ok || response.redirected) {
            const idx = deliveryRaw.findIndex(d => String(d.docNumber) === String(editDeliveryDocNumber));
            if (idx !== -1) {
                deliveryRaw[idx].otd         = otd;
                deliveryRaw[idx].del_method  = delMethod;
                deliveryRaw[idx].premium     = premium;
                deliveryRaw[idx].dps         = dps;
                deliveryRaw[idx].total_score = totalScore;
            }

            const qcIdx = qcData.findIndex(q => String(q.docNumber) === String(editDeliveryDocNumber));
            if (qcIdx !== -1) {
                qcData[qcIdx].delivery.otd         = otd;
                qcData[qcIdx].delivery.del_method  = delMethod;
                qcData[qcIdx].delivery.premium     = premium;
                qcData[qcIdx].delivery.dps         = dps;
                qcData[qcIdx].delivery.total_score = totalScore;
            }

            const docForRefresh = editDeliveryDocNumber;
            closeEditDelivery();
            showDetail(docForRefresh);
            showSuccess('Delivery data updated successfully!');
        } else {
            const text = await response.text();
            showFailed('Failed to update delivery. Please try again.');
            console.error(text);
        }

    } catch (err) {
        console.error(err);
        showFailed('Failed to update delivery. Please try again.');
    }
}

/* =========================
   BACK
========================= */
function backToList() {
    selectedDoc = null;
    document.getElementById("detailCard").style.display  = "none";
    document.getElementById("mainContent").style.display = "block";
    document.getElementById("detail-section").innerHTML  = "";
}

/* =========================
   CONFIRM
========================= */
let confirmDoc    = null;
let confirmStatus = null;

function openConfirm(doc, status) {
    confirmDoc    = doc;
    confirmStatus = status;
    document.getElementById("confirmText").innerText = `Confirm ${status}?`;
    document.getElementById("confirmModal").style.display = "flex";
}

function closeConfirm() {
    document.getElementById("confirmModal").style.display = "none";
}

document.getElementById("yesBtn").onclick = () => {
    setStatus(confirmDoc, confirmStatus);
};

/* =========================
   SUCCESS / FAILED
========================= */
function showSuccess(msg) {
    document.getElementById("successText").innerText = msg;
    document.getElementById("successPopup").style.display = "flex";
}

function closeSuccess() {
    document.getElementById("successPopup").style.display = "none";
}

function showFailed(msg) {
    document.getElementById("failedText").innerText = msg;
    document.getElementById("failedPopup").style.display = "flex";
}

function closeFailedPopup() {
    document.getElementById("failedPopup").style.display = "none";
}

/* =========================
   INIT
========================= */
renderTable();

</script>
@endpush