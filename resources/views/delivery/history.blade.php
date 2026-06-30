@extends('layouts.app')

@section('title', 'Delivery History')

@section('page_title', 'Delivery History')

@push('head')
<link rel="stylesheet" href="{{ asset('css/delivhistory.css') }}">
@endpush

@section('content')

<div id="mainContent">

    <!-- FILTER -->
    <div class="filter-box">

        <div class="dropdown" id="supplierWrapper">
            <input
                type="text"
                id="supplierSearch"
                placeholder="Search Supplier..."
                autocomplete="off"
            >
            <div id="dropdownList" class="dropdown-list"></div>
        </div>

        <input type="hidden" id="supplierFilter" value="">

        <select id="monthFilter">
            <option value="">All Month</option>
            <option value="01">January</option>
            <option value="02">February</option>
            <option value="03">March</option>
            <option value="04">April</option>
            <option value="05">May</option>
            <option value="06">June</option>
            <option value="07">July</option>
            <option value="08">August</option>
            <option value="09">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>

        <select id="yearFilter"></select>

    </div>

    <!-- TABLE -->
    <table class="history-table">

        <thead>
            <tr>
                <th>Doc No</th>
                <th>Creation Date</th>
                <th>Supplier</th>
                <th>Period</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Total Point</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody id="supplier-list"></tbody>

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

<!-- DETAIL -->
<div class="panel-card detail-card" id="detailCard" style="display:none;">
    <div id="detail-table"></div>
</div>

<!-- BLOCKED POPUP -->
<div id="blockedPopup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
     background:rgba(0,0,0,0.4); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:#fff; padding:30px; border-radius:10px; text-align:center; max-width:380px;">
        <p style="font-weight:600; margin-bottom:16px;">
            Cannot edit — document is already in approval process.
        </p>
        <button onclick="closeBlockedPopup()"
            style="padding:8px 24px; background:#1a3c6e; color:#fff; border:none; border-radius:6px; cursor:pointer;">
            OK
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>

let currentPage = 1;
let rowsPerPage = 10;
let selectedSupplier = "";

const data      = @json($deliveries);
const approvals = @json($approvals);
const userRole  = "{{ auth()->user()->role }}";
const canEdit   = userRole === 'Admin' || userRole === 'Staff';

const tableBody   = document.getElementById("supplier-list");
const detailCard  = document.getElementById("detailCard");
const detailTable = document.getElementById("detail-table");
const mainContent = document.getElementById("mainContent");
let supplierList = [];
/* =========================
   YEAR FILTER
========================= */
const yearFilter  = document.getElementById("yearFilter");
const currentYear = new Date().getFullYear();

for (let y = currentYear - 3; y <= currentYear + 2; y++) {
    const option       = document.createElement("option");
    option.value       = y;
    option.textContent = y;
    if (y == currentYear) option.selected = true;
    yearFilter.appendChild(option);
}

/* =========================
   FORMAT DATE
========================= */
function formatDate(date) {
    if (!date) return "-";
    const d = new Date(date);
    return `${String(d.getMonth()+1).padStart(2,'0')}/${String(d.getDate()).padStart(2,'0')}/${d.getFullYear()}
            ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}:${String(d.getSeconds()).padStart(2,'0')}`;
}

function formatPeriod(month, year) {
    const months = [
        "January","February","March","April","May","June",
        "July","August","September","October","November","December"
    ];
    return `${months[Number(month)-1]} ${year}`;
}

/* =========================
   STATUS (TEXT ONLY)
========================= */
function getStatus(d) {
    const approval = approvals[d.docNumber];

    if (!approval) {
        return 'New';
    }

    const status = (approval.status ?? '').toUpperCase();

    if (status === 'APPROVED') return 'Approved';
    if (status === 'REJECTED') return 'Not Approved';

    return 'In Review';
}

/* =========================
   POPUP
========================= */
function showEditBlocked() {
    document.getElementById("blockedPopup").style.display = "flex";
}

function closeBlockedPopup() {
    document.getElementById("blockedPopup").style.display = "none";
}

/* =========================
   OPEN EDIT
========================= */
function openEdit(id) {
    const d = data.find(x => x.id == id);
    if (!d) return;
    window.location.href = `/delivery/edit/${d.id}`;
}

/* =========================
   SEARCHABLE SUPPLIER
========================= */

function buildSupplierList() {

    supplierList = [
    ...new Set(
        data.map(d => d.supplierSearch).filter(Boolean)
    )
];
}

function renderSupplierDropdown(keyword = "") {

    const list = document.getElementById("dropdownList");

    keyword = keyword.toLowerCase();

    const suppliers = [
        { value:"", label:"All Supplier" },
        ...supplierList.map(s => ({
            value:s,
            label:s
        }))
    ].filter(item =>
        item.label.toLowerCase().includes(keyword)
    );

    if(!suppliers.length){

        list.innerHTML =
            `<div class="dropdown-empty">No supplier found</div>`;

        return;
    }

    list.innerHTML = suppliers.map(item=>`

        <div
            class="dropdown-item"
            data-value="${item.value}">
            ${item.label}
        </div>

    `).join("");

    list.querySelectorAll(".dropdown-item").forEach(item=>{

        item.onclick = () => {
    const value = (item.dataset.value || "").trim();
    const label = (item.textContent || "").trim();

    selectedSupplier = value.trim();

    document.getElementById("supplierFilter").value = selectedSupplier;
    document.getElementById("supplierSearch").value = label;

    document.getElementById("dropdownList").style.display = "none";

    applyFilter();
};

    });

}

function openSupplierDropdown(){

    renderSupplierDropdown(
        document.getElementById("supplierSearch").value==="All Supplier"
        ? ""
        : document.getElementById("supplierSearch").value
    );

    document.getElementById("dropdownList").style.display="block";

}

function closeSupplierDropdown(){

    document.getElementById("dropdownList").style.display="none";

    const hidden =
        document.getElementById("supplierFilter").value;

    document.getElementById("supplierSearch").value =
        hidden || "All Supplier";

}

function initSupplierDropdown(){

    const input=document.getElementById("supplierSearch");

    input.value="All Supplier";

    input.addEventListener("click",openSupplierDropdown);

    input.addEventListener("input",()=>{

        renderSupplierDropdown(input.value);

        document.getElementById("dropdownList").style.display="block";

    });

    document.addEventListener("click",e=>{

        if(!document.getElementById("supplierWrapper").contains(e.target)){

            closeSupplierDropdown();

        }

    });

}

/* =========================
   FILTER
========================= */
function applyFilter() {
    const supplier = (selectedSupplier || "").toLowerCase();
    const month    = document.getElementById("monthFilter").value;
    const year     = document.getElementById("yearFilter").value;

    const filtered = data.filter(d => {
        return (
            (!supplier || (d.supplierSearch || "").toLowerCase() === supplier)
            && (!month  || d.del_month == month)
            && (!year   || d.del_year == year)
        );
    });

    renderTable(filtered);
}

/* =========================
   RENDER TABLE
========================= */
function renderTable(filtered) {
    tableBody.innerHTML = "";

    const totalPages    = Math.ceil(filtered.length / rowsPerPage);
    if (currentPage > totalPages) currentPage = 1;

    const start         = (currentPage - 1) * rowsPerPage;
    const paginatedData = filtered.slice(start, start + rowsPerPage);

    if (filtered.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="9">No Data Found</td></tr>`;
        return;
    }

    paginatedData.forEach(d => {
        const row        = document.createElement("tr");
        const inApproval = !!approvals[d.docNumber];

        row.innerHTML = `
            <td>${d.docNumber ?? '-'}</td>
            <td>${formatDate(d.created_at)}</td>
            <td>${d.supplierSearch ?? '-'}</td>
            <td>${formatPeriod(d.del_month, d.del_year)}</td>
            <td>${d.createdBy ?? '-'}</td>
            <td>${d.updatedBy ?? '-'}</td>
            <td><b>${parseInt(d.total_score ?? 0)}</b></td>
            <td>${getStatus(d)}</td>
            <td class="action-cell">
                <button class="table-view-btn" onclick="viewDetail(${d.id})">
                    <i class="fa-solid fa-eye"></i>
                </button>
                ${canEdit
                    ? `<button
                            class="table-edit-btn ${inApproval ? 'disabled' : ''}"
                            onclick="${inApproval ? 'showEditBlocked()' : `openEdit(${d.id})`}"
                            title="${inApproval ? 'Already in Approval' : 'Edit'}">
                        <i class="fa-solid fa-pen"></i>
                       </button>`
                    : ''
                }
            </td>
        `;

        tableBody.appendChild(row);
    });

    renderPagination(totalPages);
}

/* =========================
   PAGINATION
========================= */
function renderPagination(totalPages) {
    let html = `<button onclick="changePage(currentPage-1)" ${currentPage===1 ? 'disabled' : ''}>&lt;</button>`;

    for (let i = 1; i <= totalPages; i++) {
        html += `<button class="${i===currentPage ? 'active-page' : ''}" onclick="changePage(${i})">${i}</button>`;
    }

    html += `<button onclick="changePage(currentPage+1)" ${currentPage===totalPages ? 'disabled' : ''}>&gt;</button>`;

    document.getElementById("pagination").innerHTML = html;
}

function changePage(page) {
    const supplier   = document.getElementById("supplierFilter").value.toLowerCase();
    const month      = document.getElementById("monthFilter").value;
    const year       = document.getElementById("yearFilter").value;
    const filtered   = data.filter(d =>
        (!supplier || (d.supplierSearch || "").toLowerCase() === supplier)
        && (!month  || d.del_month == month)
        && (!year   || d.del_year  == year)
    );
    const totalPages = Math.ceil(filtered.length / rowsPerPage);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    renderTable(filtered);
}

function changeRowsPerPage(value) {
    rowsPerPage = parseInt(value);
    currentPage = 1;
    buildSupplierList();
    initSupplierDropdown();
    applyFilter();
}

/* =========================
   HELPER DETAIL
========================= */
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

function getProblemNotes(problems){

    if(!problems){
        return "Nothing Problem";
    }


    let data = problems;


    // kalau dari database bentuk JSON string
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


    // NO PROBLEM
    if(data.length === 1 && data[0].note){

        return data[0].note;

    }


    // YES PROBLEM
    return data.map((p,index)=>`

        <div class="problem-note">

            <b>Problem ${index+1}</b><br>

            Part No : ${p.partNo ?? '-'} <br>

            Part Name : ${p.partName ?? '-'} <br>

            Remark : ${p.remark ?? '-'}

        </div>

    `).join("<hr>");

}

/* =========================
   VIEW DETAIL
========================= */
function viewDetail(id) {
    const d = data.find(x => x.id == id);
    if (!d) return;

    mainContent.style.display = "none";
    detailCard.style.display  = "block";

    detailTable.innerHTML = `
    <div class="detail-header-top">
        <button onclick="backToList()" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>

        <div class="doc-title">${d.docNumber}</div>

        <div class="header-space"></div>
    </div>

    <div class="detail-subinfo">
        <div class="info-line">
            <span class="info-label">Supplier</span>
            <span class="info-value">${d.supplierSearch ?? '-'}</span>
        </div>
        <div class="info-line">
            <span class="info-label">Period</span>
            <span class="info-value">${formatPeriod(d.del_month, d.del_year)}</span>
        </div>
    </div>

    <div class="section-title-table">DELIVERY</div>

    <table class="detail-table">

        <tr>
            <td rowspan="2" class="main-label">Fulfillment</td>
            <td>%</td>
            <td>${d.fulfillment}</td>
        </tr>
        <tr>
            <td>Index</td>
            <td>${calculateQtyIndex(d.fulfillment)}</td>
        </tr>

        <tr>
            <td rowspan="2" class="main-label">On Time Delivery</td>
            <td>Day</td>
            <td>${getOTDText(d.otd)}</td>
        </tr>
        <tr>
            <td>Index</td>
            <td>${d.otd ?? 0}</td>
        </tr>

        <tr>
            <td rowspan="2" class="main-label">Delivery Method</td>
            <td>Method</td>
            <td>${getMethodText(d.del_method)}</td>
        </tr>
        <tr>
            <td>Index</td>
            <td>${d.del_method ?? 0}</td>
        </tr>

        <tr>
            <td rowspan="2" class="main-label">Premium Freight</td>
            <td>Amount (IDR)</td>
            <td>${Number(d.premium ?? 0).toLocaleString('id-ID')}</td>
        </tr>
        <tr>
            <td>Index</td>
            <td>${calculatePremiumIndex(d.premium)}</td>
        </tr>

        <tr>
            <td rowspan="2" class="main-label">DPS Reply</td>
            <td>Reply</td>
            <td>${getDPSText(d.dps)}</td>
        </tr>
        <tr>
            <td>Index</td>
            <td>${d.dps ?? 0}</td>
        </tr>

        <tr class="total-row">
            <td>Total Score</td>
            <td></td>
            <td><b>${parseInt(d.total_score ?? 0)}</b></td>
        </tr>

    </table>

    <div class="notes-card">

    <div class="notes-title">
        Notes : 
    </div>

    <div class="notes-content">

        ${getProblemNotes(d.problems)}

    </div>

</div>

    `;

    window.scrollTo({ top: 0, behavior: "smooth" });
}

/* =========================
   CLOSE DETAIL
========================= */
function backToList() {
    detailCard.style.display  = "none";
    detailTable.innerHTML     = "";
    mainContent.style.display = "block";
}

/* =========================
   EVENTS
========================= */
document.getElementById("supplierSearch").addEventListener("input", applyFilter);
document.getElementById("monthFilter").addEventListener("change", applyFilter);
document.getElementById("yearFilter").addEventListener("change", applyFilter);


buildSupplierList();
initSupplierDropdown();
applyFilter();

</script>

@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session("success") }}',
        confirmButtonText: 'OK',
        confirmButtonColor: '#2563eb'
    });
});
</script>
@endif

@endpush