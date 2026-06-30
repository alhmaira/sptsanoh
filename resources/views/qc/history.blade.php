@extends('layouts.app')

@section('title', 'QC History')
@section('page_title', 'Quality Control History')

@push('head')
<link rel="stylesheet" href="{{ asset('css/qchistory.css') }}">
@endpush

@section('content')

<div id="mainContent">

    <!-- FILTER -->
    <div class="filter-box">

        <div class="dropdown">
            <input type="text"
                id="supplierSearch"
                placeholder="Search Supplier..."
                onkeyup="filterSupplier()"
                onclick="toggleDropdown()">
            <div id="dropdownList" class="dropdown-list"></div>
        </div>

        <select id="monthFilter" onchange="applyFilter()">
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

        <select id="yearFilter" onchange="applyFilter()"></select>

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
<div id="detailCard" class="panel-card detail-card" style="display:none;">
    <div id="qc-detail"></div>
</div>

@endsection

<!-- POPUP BLOCKED -->
<div id="blockedPopup" class="popup" style="display:none;">
    <div class="popup-box">
        <p>⚠️ Cannot edit - document is already in approval process.</p>
        <button onclick="closeBlockedPopup()">OK</button>
    </div>
</div>

@push('scripts')
<script>

let selectedSupplier = "";
let qcData           = @json($qcData);
const approvals      = @json($approvals);
const userRole = "{{ auth()->user()->role }}";
const userDept = "{{ auth()->user()->department }}";
let currentPage      = 1;
let rowsPerPage      = 10;
let filteredData     = [];

/* =========================
   SORT
========================= */
qcData.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

/* =========================
   ELEMENT
========================= */
const mainContent     = document.getElementById("mainContent");
const detailCard      = document.getElementById("detailCard");
const detailContainer = document.getElementById("qc-detail");

/* =========================
   YEAR
========================= */
const yearFilter = document.getElementById("yearFilter");
const now        = new Date().getFullYear();

for (let i = now - 3; i <= now + 2; i++) {
    let opt       = document.createElement("option");
    opt.value     = i;
    opt.textContent = i;
    if (i === now) opt.selected = true;
    yearFilter.appendChild(opt);
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
   DROPDOWN
========================= */
function toggleDropdown() {
    const list = document.getElementById("dropdownList");
    list.style.display = list.style.display === "block" ? "none" : "block";
}

function buildDropdown(data){

    const list = document.getElementById("dropdownList");
    list.innerHTML = "";

    const all = document.createElement("div");
    all.className = "option";
    all.textContent = "All Supplier";

    all.onclick = () => {
        selectedSupplier = "";
        document.getElementById("supplierSearch").value = "";
        list.style.display = "none";
        applyFilter();
    };

    list.appendChild(all);

    const supplierList = [...new Set(data.map(d => d.supplier))];

    supplierList.forEach(s => {

        const div = document.createElement("div");
        div.className = "option";
        div.textContent = s;

        div.onclick = () => {

            selectedSupplier = (s || "").toLowerCase();
            document.getElementById("supplierSearch").value = s;

            list.style.display = "none";

            applyFilter();
        };

        list.appendChild(div);
    });
}

function filterSupplier(){

    const input = document.getElementById("supplierSearch").value.toLowerCase();


    const filtered = qcData.filter(d =>
        (d.supplier || "").toLowerCase().includes(input)
    );

    buildDropdown(filtered);
}

document.addEventListener("click", function(e) {
    const dropdown = document.querySelector(".dropdown");
    if (dropdown && !dropdown.contains(e.target)) {
        document.getElementById("dropdownList").style.display = "none";
    }
});

/* =========================
   EDIT
========================= */
function openEdit(id) {
    window.location.href = `/qc/edit/${id}`;
}

function showEditBlocked() {
    document.getElementById("blockedPopup").style.display = "flex";
}

function closeBlockedPopup() {
    document.getElementById("blockedPopup").style.display = "none";
}

/* =========================
   TABLE
========================= */
function renderSupplierList(filtered) {
    filteredData = filtered;

    const container = document.getElementById("supplier-list");
    container.innerHTML = "";

    const start         = (currentPage - 1) * rowsPerPage;
    const paginatedData = filtered.slice(start, start + rowsPerPage);

    if (paginatedData.length === 0) {
        container.innerHTML = `<tr><td colspan="9">No QC data found</td></tr>`;
        renderPagination(0);
        return;
    }

    paginatedData.forEach(d => {
        const approval = approvals[d.docNumber];
        const approvalStarted = !!approvals[d.docNumber];

        let canEdit = false;

        if (!approvalStarted) {

            // sebelum approval
            canEdit =
                (userDept === "Quality Control" &&
                ["Staff","Supervisor","Manager"].includes(userRole))
                ||
                (userDept === "IT" &&
                userRole === "Admin");

        } else {

            // sesudah approval dimulai
            canEdit =
                userDept === "Quality Control" &&
                ["Supervisor","Manager"].includes(userRole);

        }
        const row       = document.createElement("tr");

        row.innerHTML = `
            <td>${d.docNumber}</td>
            <td>${formatDate(d.created_at)}</td>
            <td>${d.supplier || "-"}</td>
            <td>${formatPeriod(d.del_month, d.del_year)}</td>
            <td>${d.created_by || "-"}</td>
            <td>${d.updated_by || "-"}</td>
            <td><b>${d.total_score || 0}</b></td>
            <td>${getStatus(d)}</td>
            <td class="action-cell">
                <button class="table-view-btn"
                    onclick="viewQC('${d.docNumber}')">
                    <i class="fa-solid fa-eye"></i>
                </button>

                <button
                    class="table-edit-btn ${!canEdit ? 'disabled' : ''}"
                    onclick="${canEdit ? `openEdit(${d.id})` : 'showEditBlocked()'}"
                    title="${canEdit ? 'Edit' : 'Not Allowed'}">

                    <i class="fa-solid fa-pen"></i>

                </button>
            </td>
        `;
        container.appendChild(row);
    });

    const totalPages = Math.ceil(filtered.length / rowsPerPage);
    renderPagination(totalPages);
}

/* =========================
   PAGINATION
========================= */
function renderPagination(totalPages) {
    let html = `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}><</button>`;

    for (let i = 1; i <= totalPages; i++) {
        html += `<button class="${i === currentPage ? 'active-page' : ''}" onclick="changePage(${i})">${i}</button>`;
    }

    html += `<button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>></button>`;

    document.getElementById("pagination").innerHTML = html;
}

function changePage(page) {

    const totalPages = Math.ceil(filteredData.length / rowsPerPage);

    if (page < 1 || page > totalPages) return;

    currentPage = page;

    renderSupplierList(filteredData); 
}

function changeRowsPerPage(value) {
    rowsPerPage = parseInt(value);
    currentPage = 1;
    applyFilter();
}

/* =========================
   VIEW DETAIL
========================= */
function viewQC(docNumber) {
    const d = qcData.find(x => x.docNumber == docNumber);
    if (!d) return;

    mainContent.style.display = "none";
    detailCard.style.display  = "block";

    detailContainer.innerHTML = `
    <div class="table-card">

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
                <span class="info-value">${d.supplier || '-'}</span>
            </div>
            <div class="info-line">
                <span class="info-label">Period</span>
                <span class="info-value">${formatPeriod(d.del_month, d.del_year)}</span>
            </div>
        </div>

        <table class="detail-table">
            <tr>
                <th colspan="3">QUALITY</th>
            </tr>

            <tr>
                <td rowspan="2" class="main-label">Line Stop</td>
                <td>Status</td>
                <td> ${d.lineStop == 40 ? "YES" : "NO"} </td>
            </tr>
            <tr>
                <td>Index</td>
                <td>${d.lineStop ?? 0}</td>
            </tr>

            <tr>
                <td rowspan="4" class="main-label">PPM</td>
                <td>NG</td>
                <td>${d.ng ?? 0}</td>
            </tr>
            <tr>
                <td>Supply</td>
                <td>${d.supply ?? 0}</td>
            </tr>
            <tr>
                <td>PPM</td>
                <td>${d.ppm ?? 0}</td>
            </tr>
            <tr>
                <td>Index</td>
                <td>${d.ppmScore ?? 0}</td>
            </tr>

            <tr>
                <td rowspan="2" class="main-label">Problem Rank</td>
                <td>Rank</td>
                <td>${getRankLabel(d.rank_score)}</td>
            </tr>
            <tr>
                <td>Index</td>
                <td>${d.rank_score ?? 0}</td>
            </tr>

            <tr>
                <td rowspan="2" class="main-label">FPPK</td>
                <td>Status</td>
                <td>${getFppkLabel(d.fppk)}</td>
            </tr>
            <tr>
                <td>Index</td>
                <td>${d.fppk ?? 0}</td>
            </tr>

            <tr class="total-row">
                <td>Total Score</td>
                <td></td>
                <td><b>${d.total_score ?? 0}</b></td>
            </tr>
        </table>

    </div>
    
    <div class="notes-card">

    <div class="notes-title">
        Notes :
    </div>

    <div class="notes-content">

        ${getQualityNotes(d.qualityProblems)}

    </div>

</div>
`;

    window.scrollTo({ top: 0, behavior: "smooth" });
}

/* =========================
   BACK
========================= */
function backToList() {
    detailCard.style.display  = "none";
    detailContainer.innerHTML = "";
    mainContent.style.display = "block";
}

/* =========================
   FILTER
========================= */
function applyFilter() {

    const month = document.getElementById("monthFilter").value;
    const year  = document.getElementById("yearFilter").value;

    const filtered = qcData.filter(d => {

        const m = String(d.del_month).padStart(2, '0');
        const y = String(d.del_year);

        const supplier = (d.supplier || "").toLowerCase();

        const matchSupplier =
            !selectedSupplier ||
            supplier.includes(selectedSupplier);

        const matchMonth = !month || m === month;
        const matchYear  = !year || y === year;

        return matchSupplier && matchMonth && matchYear;
    });

    filteredData = filtered;

    renderSupplierList(filtered);
}

/* =========================
   UTIL
========================= */
function formatDate(date) {
    if (!date) return "-";
    const d = new Date(date);
    return `${String(d.getMonth()+1).padStart(2,'0')}/${String(d.getDate()).padStart(2,'0')}/${d.getFullYear()}
            ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}:${String(d.getSeconds()).padStart(2,'0')}`;
}

function formatPeriod(month, year) {
    const months = ["January","February","March","April","May","June",
                    "July","August","September","October","November","December"];
    return `${months[Number(month)-1]} ${year}`;
}

function getRankLabel(val) {
    if (val == 25) return "A";
    if (val == 10) return "B";
    if (val == 5)  return "C";
    return "No Problem";
}

function getFppkLabel(val) {
    if (val == 0)  return "No Problem";
    if (val == 10) return "Delay";
    if (val == 20) return "No Reply";
    return "-";
}

function getQualityNotes(problems){


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



    if(
        data.length === 1 &&
        data[0].note
    ){

        return data[0].note;

    }



    return data.map((p,index)=>`


        <div class="problem-note">


            <b>Problem ${index+1}</b><br>


            Part No :
            ${p.partNo ?? '-'} <br>


            Part Name :
            ${p.partName ?? '-'} <br>


            Delivery :
            ${p.delivery ?? '-'} <br>


            NG :
            ${p.ng ?? '-'} <br>


            Problem :
            ${p.problem ?? '-'}


        </div>


    `).join("<hr>");

}

/* =========================
   INIT
========================= */
buildDropdown(qcData);
applyFilter();

</script>
@endpush