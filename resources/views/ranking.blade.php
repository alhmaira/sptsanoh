@extends('layouts.app')

@section('title', 'Supplier Ranking')
@section('page_title', 'Supplier Ranking')

@push('head')
<link rel="stylesheet" href="{{ asset('css/ranking.css') }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<style>
/* ── Searchable Supplier Dropdown ── */
.supplier-search-wrapper {
    position: relative;
    min-width: 220px;
}

.supplier-search-input {
    width: 100%;
    padding: 6px 30px 6px 10px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 13px;
    background: white;
    cursor: pointer;
    box-sizing: border-box;
    height: 36px;
    outline: none;
    transition: border-color .2s;
}

.supplier-search-input:focus {
    border-color: #0B2C6A;
    box-shadow: 0 0 0 2px rgba(11,44,106,.12);
}

.supplier-search-arrow {
    position: absolute;
    right: 9px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #6b7280;
    font-size: 11px;
}

.supplier-dropdown-list {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    max-height: 240px;
    overflow-y: auto;
    z-index: 9999;
    display: none;
}

.supplier-dropdown-list.open {
    display: block;
}

.supplier-dropdown-item {
    padding: 8px 12px;
    font-size: 13px;
    cursor: pointer;
    color: #111827;
    transition: background .15s;
    border-radius: 4px;
    margin: 2px 4px;
}

.supplier-dropdown-item:hover,
.supplier-dropdown-item.highlighted {
    background: #EFF4FF;
    color: #0B2C6A;
}

.supplier-dropdown-item.selected {
    background: #0B2C6A;
    color: white;
    font-weight: 600;
}

.supplier-dropdown-empty {
    padding: 12px;
    text-align: center;
    color: #9ca3af;
    font-size: 13px;
}
</style>
@endpush

@section('content')

<div class="filter-rank">
    <div class="filter-group">
        <label>Period</label>
        <select id="periodType">
            <option value="all">All Time</option>
            <option value="yearly">Yearly</option>
            <option value="monthly">Monthly</option>
        </select>
    </div>

    <div class="filter-group" id="yearGroup" style="display:none;">
        <label>Year</label>
        <select id="yearFilter"></select>
    </div>

    <div class="filter-group" id="monthGroup" style="display:none;">
        <label>Month</label>
        <select id="monthFilter">
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>
    </div>

    <!-- ✅ SEARCHABLE SUPPLIER DROPDOWN -->
    <div class="filter-group">
        <label>Supplier</label>
        <div class="supplier-search-wrapper" id="supplierSearchWrapper">
            <input
                type="text"
                id="supplierSearchInput"
                class="supplier-search-input"
                placeholder="All Supplier"
                autocomplete="off"
            />
            <span class="supplier-search-arrow">▼</span>
            <div class="supplier-dropdown-list" id="supplierDropdownList"></div>
        </div>
        <!-- Hidden value untuk logic render -->
        <input type="hidden" id="supplierFilter" value="all" />
    </div>

    <div class="filter-group">
        <label>Sort</label>
        <select id="sortOrder">
            <option value="desc">Highest Score</option>
            <option value="asc">Lowest Score</option>
        </select>
    </div>

    <div class="export-group">
        <button class="btn-export btn-excel" onclick="exportExcel()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Excel
        </button>
        <button class="btn-export btn-pdf" onclick="exportPDF()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            PDF
        </button>
    </div>
</div>

<div id="periodLabel" class="period-label" style="display:none;"></div>

<!-- TABLE: ALL SUPPLIERS -->
<div id="allSuppliersView">
    <table id="rankingTable">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Supplier Name</th>
                <th>Delivery Score</th>
                <th>QC Score</th>
                <th>Total Score</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- TABLE: SINGLE SUPPLIER -->
<div id="singleSupplierView" style="display:none;">
    <div class="supplier-detail-header">
        <span id="supplierDetailName"></span>
        <span id="supplierDetailBadge" class="grade"></span>
    </div>
    <table id="supplierMonthTable">
        <thead>
            <tr>
                <th>Month</th>
                <th>Delivery Score</th>
                <th>QC Score</th>
                <th>Total Score</th>
                <th>Rank</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@endsection

@push('scripts')
<script>

const qcRaw       = @json($qcData);
const deliveryRaw = @json($deliveryData);

const MONTHS = [
    "January","February","March","April",
    "May","June","July","August",
    "September","October","November","December"
];

let globalQc         = [];
let globalDelivery   = [];
let lastRenderData   = [];
let allSuppliersList = []; // ✅ list semua supplier untuk dropdown

/* =========================
   HELPERS
========================= */
function getGrade(score){
    if(score === 100) return "A";
    if(score >= 80)   return "B";
    if(score >= 60)   return "C";
    return "D";
}

function getPeriodLabel(){
    const type  = document.getElementById("periodType").value;
    const year  = document.getElementById("yearFilter").value;
    const month = Number(document.getElementById("monthFilter").value);
    if(type === "yearly")  return `Year ${year}`;
    if(type === "monthly") return `${MONTHS[month-1]} ${year}`;
    return "All Time";
}

function filterByPeriod(data, type, year, month){
    if(type === "all") return data;
    return data.filter(d => {
        const y = Number(d.year);
        const m = Number(d.month);
        if(type === "yearly")  return y === Number(year);
        if(type === "monthly") return y === Number(year) && m === Number(month);
        return true;
    });
}

function loadYearFilter(qc, delivery){
    const years = [...new Set([
        ...qc.map(d => d.year),
        ...delivery.map(d => d.year)
    ])].filter(Boolean).map(Number).sort((a,b)=>b-a);

    const sel = document.getElementById("yearFilter");
    sel.innerHTML = "";
    years.forEach(y => {
        sel.innerHTML += `<option value="${y}">${y}</option>`;
    });
    const cur = new Date().getFullYear();
    if(years.includes(cur)) sel.value = cur;
}

function computeRankings(qc, delivery){
    const suppliers = [...new Set([
        ...qc.map(d => d.supplier),
        ...delivery.map(d => d.supplier)
    ])].filter(Boolean);

    return suppliers.map(s => {
        const dData = delivery.filter(d => d.supplier === s);
        const qData = qc.filter(d => d.supplier === s);

        const avgDel = dData.length
            ? dData.reduce((a,b) => a + Number(b.total||0), 0) / dData.length : 0;
        const avgQc  = qData.length
            ? qData.reduce((a,b) => a + Number(b.total_score||0), 0) / qData.length : 0;
        const score  = (avgDel + avgQc) / 2;

        return { supplier:s, avgDel, avgQc, score, grade:getGrade(score) };
    });
}

/* =========================
   SEARCHABLE SUPPLIER DROPDOWN
========================= */
function buildSupplierList(qc, delivery){
    allSuppliersList = [...new Set([
        ...qc.map(d => d.supplier),
        ...delivery.map(d => d.supplier)
    ])].filter(Boolean).sort();
}

function renderDropdownItems(filter = ""){
    const list = document.getElementById("supplierDropdownList");
    const q    = filter.toLowerCase().trim();

    const items = [
        { value:"all", label:"All Supplier" },
        ...allSuppliersList.map(s => ({ value:s, label:s }))
    ].filter(item => item.label.toLowerCase().includes(q));

    if(!items.length){
        list.innerHTML = `<div class="supplier-dropdown-empty">No supplier found</div>`;
        return;
    }

    const currentVal = document.getElementById("supplierFilter").value;
    list.innerHTML = items.map(item => `
        <div class="supplier-dropdown-item ${item.value === currentVal ? "selected" : ""}"
             data-value="${item.value}">
            ${item.label}
        </div>
    `).join("");

    // Click handler tiap item
    list.querySelectorAll(".supplier-dropdown-item").forEach(el => {
        el.addEventListener("click", () => {
            const val   = el.dataset.value;
            const label = val === "all" ? "All Supplier" : val;

            document.getElementById("supplierFilter").value  = val;
            document.getElementById("supplierSearchInput").value = label;
            closeDropdown();
            render();
        });
    });
}

function openDropdown(){
    const list  = document.getElementById("supplierDropdownList");
    const input = document.getElementById("supplierSearchInput");
    input.select();
    renderDropdownItems(input.value === "All Supplier" ? "" : input.value);
    list.classList.add("open");
}

function closeDropdown(){
    document.getElementById("supplierDropdownList").classList.remove("open");

    // Kalau input dikosongkan / tidak cocok, reset ke All Supplier
    const currentVal = document.getElementById("supplierFilter").value;
    const inputEl    = document.getElementById("supplierSearchInput");
    const expected   = currentVal === "all" ? "All Supplier" : currentVal;
    if(inputEl.value.trim() === "" || inputEl.value !== expected){
        inputEl.value = expected;
    }
}

function initSupplierDropdown(){
    const input  = document.getElementById("supplierSearchInput");
    const list   = document.getElementById("supplierDropdownList");
    const wrapper = document.getElementById("supplierSearchWrapper");

    // Buka saat klik input
    input.addEventListener("click", openDropdown);

    // Filter saat ketik
    input.addEventListener("input", () => {
        renderDropdownItems(input.value);
        list.classList.add("open");
    });

    // Keyboard navigation
    input.addEventListener("keydown", e => {
        const items = list.querySelectorAll(".supplier-dropdown-item");
        const highlighted = list.querySelector(".highlighted");
        let idx = [...items].indexOf(highlighted);

        if(e.key === "ArrowDown"){
            e.preventDefault();
            if(highlighted) highlighted.classList.remove("highlighted");
            const next = items[idx + 1] || items[0];
            if(next){ next.classList.add("highlighted"); next.scrollIntoView({block:"nearest"}); }
        } else if(e.key === "ArrowUp"){
            e.preventDefault();
            if(highlighted) highlighted.classList.remove("highlighted");
            const prev = items[idx - 1] || items[items.length - 1];
            if(prev){ prev.classList.add("highlighted"); prev.scrollIntoView({block:"nearest"}); }
        } else if(e.key === "Enter"){
            e.preventDefault();
            if(highlighted) highlighted.click();
            else if(items.length) items[0].click();
        } else if(e.key === "Escape"){
            closeDropdown();
            input.blur();
        }
    });

    // Tutup kalau klik di luar
    document.addEventListener("click", e => {
        if(!wrapper.contains(e.target)) closeDropdown();
    });
}

/* =========================
   RENDER: ALL SUPPLIERS
========================= */
function renderAllSuppliers(qc, delivery){
    document.getElementById("allSuppliersView").style.display = "";
    document.getElementById("singleSupplierView").style.display = "none";

    const tbody = document.querySelector("#rankingTable tbody");
    tbody.innerHTML = "";

    let result = computeRankings(qc, delivery);
    if(!result.length){
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:24px;color:#9ca3af;">No data for this period</td></tr>`;
        lastRenderData = [];
        return;
    }

    const order = document.getElementById("sortOrder").value;
    result.sort((a,b) => order==="asc" ? a.score-b.score : b.score-a.score);
    lastRenderData = result.map((r,i) => ({ rank:i+1, ...r }));

    result.forEach((r, i) => {
        let rc = i===0 ? "rank-1" : i===1 ? "rank-2" : i===2 ? "rank-3" : "";
        tbody.innerHTML += `
        <tr class="${rc}">
            <td class="rank-number ${rc}">${i+1}</td>
            <td class="supplier-name">${r.supplier}</td>
            <td>${r.avgDel.toFixed(1)}</td>
            <td>${r.avgQc.toFixed(1)}</td>
            <td><div class="score-box" style="font-weight:800;">${r.score.toFixed(1)}</div></td>
            <td><span class="grade grade-${r.grade}">${r.grade}</span></td>
        </tr>`;
    });
}

/* =========================
   RENDER: SINGLE SUPPLIER
========================= */
function renderSingleSupplier(supplier, qc, delivery){
    document.getElementById("allSuppliersView").style.display = "none";
    document.getElementById("singleSupplierView").style.display = "";

    const type  = document.getElementById("periodType").value;
    const year  = Number(document.getElementById("yearFilter").value);

    const tbody = document.querySelector("#supplierMonthTable tbody");
    tbody.innerHTML = "";
    lastRenderData = [];

    const sQcAll  = qc.filter(d => d.supplier === supplier);
    const sDelAll = delivery.filter(d => d.supplier === supplier);
    const hasMonthData = [...sQcAll, ...sDelAll].some(d => d.month > 0 && d.year > 0);

    /* ── CASE A: No month/year data ── */
    if(!hasMonthData){
        const sQc  = type === "all" ? sQcAll  : sQcAll.filter(d => type==="yearly" ? d.year===year : d.year===year && d.month===Number(document.getElementById("monthFilter").value));
        const sDel = type === "all" ? sDelAll : sDelAll.filter(d => type==="yearly" ? d.year===year : d.year===year && d.month===Number(document.getElementById("monthFilter").value));

        const useQc  = sQc.length  ? sQc  : sQcAll;
        const useDel = sDel.length ? sDel : sDelAll;

        if(!useQc.length && !useDel.length){
            tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:24px;color:#9ca3af;">No data available for this supplier</td></tr>`;
            renderSupplierHeader(supplier, 0);
            return;
        }

        const avgDel = useDel.length ? useDel.reduce((a,b)=>a+Number(b.total||0),0)/useDel.length : 0;
        const avgQc  = useQc.length  ? useQc.reduce((a,b) =>a+Number(b.total_score||0),0)/useQc.length : 0;
        const score  = (avgDel + avgQc) / 2;
        const grade  = getGrade(score);

        const rankings = computeRankings(qc, delivery).sort((a,b)=>b.score-a.score);
        const rank = rankings.findIndex(r=>r.supplier===supplier) + 1;
        const rankClass = rank===1?"rank-1":rank===2?"rank-2":rank===3?"rank-3":"";

        lastRenderData.push({ year:"—", month:"Overall", avgDel, avgQc, score, grade, rank });

        tbody.innerHTML += `
        <tr>
            <td class="supplier-name">Overall (No monthly breakdown)</td>
            <td>${avgDel.toFixed(1)}</td>
            <td>${avgQc.toFixed(1)}</td>
            <td><div class="score-box" style="font-weight:800;">${score.toFixed(1)}</div></td>
            <td class="rank-number ${rankClass}">${rank || "—"}</td>
        </tr>`;

        renderSupplierHeader(supplier, score);
        return;
    }

    /* ── CASE B: Has month/year data ── */
    const allYears = [...new Set([
        ...sQcAll.map(d=>d.year),
        ...sDelAll.map(d=>d.year)
    ])].filter(y=>y>0).map(Number).sort();

    let years = type === "all" ? allYears : [year];
    let grandTotal = 0, grandCount = 0;

    years.forEach(yr => {
        if(type === "all" && years.length > 1){
            tbody.innerHTML += `<tr class="year-separator"><td colspan="5">${yr}</td></tr>`;
        }

        MONTHS.forEach((monthName, mi) => {
            const m = mi + 1;
            if(type === "monthly" && m !== Number(document.getElementById("monthFilter").value)) return;

            const sQcM  = sQcAll.filter(d  => d.year===yr && d.month===m);
            const sDelM = sDelAll.filter(d => d.year===yr && d.month===m);
            if(!sQcM.length && !sDelM.length) return;

            const avgDel = sDelM.length ? sDelM.reduce((a,b)=>a+Number(b.total||0),0)/sDelM.length : 0;
            const avgQc  = sQcM.length  ? sQcM.reduce((a,b) =>a+Number(b.total_score||0),0)/sQcM.length : 0;
            const score  = (avgDel + avgQc) / 2;
            const grade  = getGrade(score);

            const overallRankings = computeRankings(globalQc, globalDelivery).sort((a,b)=>b.score-a.score);
            const rank = overallRankings.findIndex(r=>r.supplier===supplier) + 1;
            const rankClass = rank===1?"rank-1":rank===2?"rank-2":rank===3?"rank-3":"";

            lastRenderData.push({ year:yr, month:monthName, avgDel, avgQc, score, grade, rank });
            grandTotal += score; grandCount++;

            tbody.innerHTML += `
            <tr>
                <td class="supplier-name">${monthName} ${yr}</td>
                <td>${avgDel.toFixed(1)}</td>
                <td>${avgQc.toFixed(1)}</td>
                <td><div class="score-box" style="font-weight:800;">${score.toFixed(1)}</div></td>
                <td class="rank-number ${rankClass}">${rank || "—"}</td>
            </tr>`;
        });
    });

    if(!lastRenderData.length){
        tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:24px;color:#9ca3af;">No data for selected period</td></tr>`;
        renderSupplierHeader(supplier, 0);
        return;
    }

    const avg      = grandTotal / grandCount;
    const avgGrade = getGrade(avg);

    const finalRankings = computeRankings(globalQc, globalDelivery).sort((a,b)=>b.score-a.score);
    const avgRank = finalRankings.findIndex(r=>r.supplier===supplier) + 1;
    const avgRankClass = avgRank===1?"rank-1":avgRank===2?"rank-2":avgRank===3?"rank-3":"";

    tbody.innerHTML += `
    <tr class="summary-row">
        <td style="font-weight:800;text-align:left;padding-left:14px;">Average</td>
        <td>—</td>
        <td>—</td>
        <td><div class="score-box" style="font-weight:800;">${avg.toFixed(1)}</div></td>
        <td class="rank-number ${avgRankClass}">${avgRank || "—"}</td>
    </tr>`;

    renderSupplierHeader(supplier, avg);
}

function renderSupplierHeader(supplier, score){
    const grade = getGrade(score);
    document.getElementById("supplierDetailName").textContent = supplier;
    document.getElementById("supplierDetailBadge").textContent = grade;
    document.getElementById("supplierDetailBadge").className = `grade grade-${grade}`;
}

/* =========================
   MAIN RENDER
========================= */
function render(){
    const type     = document.getElementById("periodType").value;
    const year     = document.getElementById("yearFilter").value;
    const month    = document.getElementById("monthFilter").value;
    const supplier = document.getElementById("supplierFilter").value;

    const label = document.getElementById("periodLabel");
    if(type !== "all"){
        label.style.display = "";
        label.textContent   = "Showing: " + getPeriodLabel();
    } else {
        label.style.display = "none";
    }

    const qc  = filterByPeriod(globalQc,      type, year, month);
    const del = filterByPeriod(globalDelivery, type, year, month);

    if(supplier === "all"){
        renderAllSuppliers(qc, del);
    } else {
        renderSingleSupplier(supplier, globalQc, globalDelivery);
    }
}

/* =========================
   LOGO HELPER
========================= */
const LOGO_PATH = "{{ asset('images/sanohlogo.png') }}";

function getLogoBase64(callback){
    const img = new Image();
    img.crossOrigin = "anonymous";
    img.onload = function(){
        const canvas = document.createElement("canvas");
        canvas.width  = img.width;
        canvas.height = img.height;
        canvas.getContext("2d").drawImage(img, 0, 0);
        callback(canvas.toDataURL("image/png"), img.width, img.height);
    };
    img.onerror = function(){ callback(null, 0, 0); };
    img.src = LOGO_PATH;
}

/* =========================
   EXPORT: EXCEL  (PLAIN B&W STYLE, matches PDF layout, with logo image)
========================= */
async function exportExcel(){
    const supplier = document.getElementById("supplierFilter").value;
    const label    = getPeriodLabel();
    const dateStr  = new Date().toLocaleDateString("en-US",{day:"2-digit",month:"long",year:"numeric"});
    const yearNow  = new Date().getFullYear();

    const wb = new ExcelJS.Workbook();
    const ws = wb.addWorksheet("Ranking");

    const totalCols = 6;
    const lastColLetter = "F";

    // ── Column widths (match PDF proportions) ──
    ws.columns = supplier === "all"
        ? [{width:8},{width:40},{width:18},{width:14},{width:14},{width:10}]
        : [{width:20},{width:18},{width:14},{width:14},{width:10},{width:10}];

    // ── Row 1-3: reserved for header (logo / title / date) ──
    ws.addRow([]);                 // row 1 - logo sits here
    ws.addRow([]);                 // row 2 - title
    ws.addRow([]);                 // row 3 - year
    ws.addRow([]);                 // row 4 - blank spacer

    // Title (merged, centered) — row 2
    ws.mergeCells(`B2:${lastColLetter}2`);
    const titleCell = ws.getCell("B2");
    titleCell.value = "SUPPLIER RANKING PERFORMANCE";
    titleCell.font  = { bold:true, size:14, color:{ argb:"FF000000" } };
    titleCell.alignment = { horizontal:"center", vertical:"middle" };

    // Year — row 3
    ws.mergeCells(`B3:${lastColLetter}3`);
    const yearCell = ws.getCell("B3");
    yearCell.value = String(yearNow);
    yearCell.font  = { size:10, color:{ argb:"FF000000" } };
    yearCell.alignment = { horizontal:"center" };

    // Date — top right, row 1
    ws.mergeCells(`D1:${lastColLetter}1`);
    const dateCell = ws.getCell("D1");
    dateCell.value = `Date : ${dateStr}`;
    dateCell.font  = { size:9, color:{ argb:"FF6E6E6E" } };
    dateCell.alignment = { horizontal:"right" };

    ws.getRow(1).height = 22;
    ws.getRow(2).height = 20;
    ws.getRow(3).height = 16;

    // ── Embed logo image (top-left) ──
    try {
        const logoResp = await fetch(LOGO_PATH);
        const logoBlob = await logoResp.blob();
        const logoBuffer = await logoBlob.arrayBuffer();
        const ext = LOGO_PATH.toLowerCase().endsWith(".jpg") || LOGO_PATH.toLowerCase().endsWith(".jpeg") ? "jpeg" : "png";
        const imageId = wb.addImage({ buffer: logoBuffer, extension: ext });
        ws.addImage(imageId, { tl: { col: 0.1, row: 0.1 }, ext: { width: 110, height: 45 } });
    } catch(e){
        // If logo fails to load, continue without it (no crash)
        console.warn("Logo could not be embedded in Excel export:", e);
    }

    // ── Separator line below header (row 4 bottom border) ──
    for(let c = 1; c <= totalCols; c++){
        ws.getCell(4, c).border = { bottom: { style:"thin", color:{ argb:"FF000000" } } };
    }

    // ── Period / Supplier label ──
    if(supplier === "all"){
        const periodRow = ws.addRow([`Period: ${label}`]);
        periodRow.getCell(1).font = { bold:true, size:10, color:{ argb:"FF000000" } };
        ws.addRow([]); // spacer
    } else {
        const supRow = ws.addRow([`Supplier: ${supplier}`]);
        supRow.getCell(1).font = { bold:true, size:10, color:{ argb:"FF000000" } };
        const periodRow = ws.addRow([`Period: ${label}`]);
        periodRow.getCell(1).font = { bold:true, size:10, color:{ argb:"FF000000" } };
        ws.addRow([]); // spacer
    }

    const thinBorder = {
        top:{style:"thin",color:{argb:"FF000000"}},
        bottom:{style:"thin",color:{argb:"FF000000"}},
        left:{style:"thin",color:{argb:"FF000000"}},
        right:{style:"thin",color:{argb:"FF000000"}}
    };

    // ── Table header row ──
    const headerLabels = supplier === "all"
        ? ["Rank","Supplier Name","Delivery Score","QC Score","Total Score","Grade"]
        : ["Month","Delivery Score","QC Score","Total Score","Grade","Rank"];

    const headerRow = ws.addRow(headerLabels);
    headerRow.eachCell(cell => {
        cell.font = { bold:true, size:11, color:{ argb:"FF000000" } };
        cell.alignment = { horizontal:"center", vertical:"middle" };
        cell.border = thinBorder;
    });
    headerRow.height = 20;

    // ── Table body rows ──
    const boldTotalGrade = (row, totalCol, gradeCol) => {
        row.eachCell((cell, colNumber) => {
            cell.border = thinBorder;
            cell.alignment = { horizontal: colNumber === 2 && supplier === "all" ? "left" : (colNumber === 1 && supplier !== "all" ? "left" : "center"), vertical:"middle" };
            if(colNumber === totalCol || colNumber === gradeCol){
                cell.font = { bold:true, color:{ argb:"FF000000" } };
            } else {
                cell.font = { color:{ argb:"FF000000" } };
            }
        });
    };

    if(supplier === "all"){
        lastRenderData.forEach(r => {
            const row = ws.addRow([
                r.rank, r.supplier,
                Number(r.avgDel.toFixed(1)), Number(r.avgQc.toFixed(1)),
                Number(r.score.toFixed(1)), r.grade
            ]);
            boldTotalGrade(row, 5, 6); // Total Score = col5, Grade = col6
        });
    } else {
        lastRenderData.forEach(r => {
            const row = ws.addRow([
                `${r.month} ${r.year}`,
                Number(r.avgDel.toFixed(1)), Number(r.avgQc.toFixed(1)),
                Number(r.score.toFixed(1)), r.grade, r.rank
            ]);
            boldTotalGrade(row, 4, 5); // Total Score = col4, Grade = col5
        });

        if(lastRenderData.length){
            const avg      = lastRenderData.reduce((s,r)=>s+r.score,0)/lastRenderData.length;
            const avgGrade = getGrade(avg);
            const finalRankings = computeRankings(globalQc, globalDelivery).sort((a,b)=>b.score-a.score);
            const avgRank = finalRankings.findIndex(r=>r.supplier===supplier) + 1;
            const avgRow = ws.addRow(["Average","—","—", Number(avg.toFixed(1)), avgGrade, avgRank]);
            avgRow.eachCell((cell, colNumber) => {
                cell.border = thinBorder;
                cell.alignment = { horizontal: colNumber === 1 ? "left" : "center", vertical:"middle" };
                cell.font = { bold:true, color:{ argb:"FF000000" } };
            });
        }
    }

    // ── Footer ──
    ws.addRow([]);
    const footerRow = ws.addRow(["PT SANOH INDONESIA"]);
    footerRow.getCell(1).font = { size:8, color:{ argb:"FF6E6E6E" } };

    const filename = supplier === "all"
        ? `Supplier_Ranking_${label.replace(/ /g,"_")}.xlsx`
        : `Ranking_${supplier.replace(/ /g,"_")}_${label.replace(/ /g,"_")}.xlsx`;

    const buf = await wb.xlsx.writeBuffer();
    const blob = new Blob([buf], { type:"application/octet-stream" });
    saveAs(blob, filename);
}

/* =========================
   EXPORT: PDF  (PLAIN B&W STYLE)
========================= */
function exportPDF(){
    getLogoBase64(function(logoData, logoW, logoH){
        _buildPDF(logoData, logoW, logoH);
    });
}

function _buildPDF(logoData, logoW, logoH){
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation:"landscape", unit:"mm", format:"a4" });

    const supplier   = document.getElementById("supplierFilter").value;
    const label      = getPeriodLabel();
    const dateStr    = new Date().toLocaleDateString("en-US",{day:"2-digit",month:"long",year:"numeric"});
    const yearNow    = new Date().getFullYear();

    const black      = [0, 0, 0];
    const gray       = [110, 110, 110];
    const pageW      = 297;

    /* ── HEADER: logo left, title centered, date right (plain, no color blocks) ── */
    const headerH = 30;

    const logoMaxH = 18, logoMaxW = 42;
    if(logoData){
        let lw = logoMaxW, lh = logoMaxH;
        const aspect = logoW / logoH;
        if(aspect > logoMaxW / logoMaxH){ lh = lw / aspect; }
        else { lw = lh * aspect; }
        doc.addImage(logoData, "PNG", 10, (headerH - lh) / 2, lw, lh);
    }

    doc.setTextColor(...black);
    doc.setFont("helvetica","bold");
    doc.setFontSize(16);
    doc.text("SUPPLIER RANKING PERFORMANCE", pageW / 2, 14, {align:"center"});
    doc.setFont("helvetica","normal");
    doc.setFontSize(10);
    doc.text(String(yearNow), pageW / 2, 21, {align:"center"});

    doc.setFontSize(9);
    doc.setTextColor(...gray);
    doc.text(`Date : ${dateStr}`, pageW - 10, 12, {align:"right"});

    // simple separator line under header
    doc.setDrawColor(...black);
    doc.setLineWidth(0.4);
    doc.line(10, headerH, pageW - 10, headerH);

    const subY = headerH + 8;
    doc.setFont("helvetica","bold");
    doc.setFontSize(10);
    doc.setTextColor(...black);
    doc.text(supplier === "all" ? `Period: ${label}` : `Supplier: ${supplier}  |  Period: ${label}`, 10, subY);

    let tableColumns, tableRows;

    if(supplier === "all"){
        tableColumns = [
            { header:"Rank",           dataKey:"rank"     },
            { header:"Supplier Name",  dataKey:"supplier" },
            { header:"Delivery Score", dataKey:"del"      },
            { header:"QC Score",       dataKey:"qc"       },
            { header:"Total Score",    dataKey:"score"    },
            { header:"Grade",          dataKey:"grade"    },
        ];
        tableRows = lastRenderData.map(r => ({
            rank: r.rank, supplier: r.supplier,
            del: r.avgDel.toFixed(1), qc: r.avgQc.toFixed(1),
            score: r.score.toFixed(1), grade: r.grade
        }));
    } else {
        tableColumns = [
            { header:"Month",          dataKey:"month" },
            { header:"Delivery Score", dataKey:"del"   },
            { header:"QC Score",       dataKey:"qc"    },
            { header:"Total Score",    dataKey:"score" },
            { header:"Grade",          dataKey:"grade" },
            { header:"Rank",           dataKey:"rank"  },
        ];
        tableRows = lastRenderData.map(r => ({
            month: `${r.month} ${r.year}`,
            del: r.avgDel.toFixed(1), qc: r.avgQc.toFixed(1),
            score: r.score.toFixed(1), grade: r.grade, rank: r.rank
        }));
        if(lastRenderData.length){
            const avg      = lastRenderData.reduce((s,r)=>s+r.score,0)/lastRenderData.length;
            const avgGrade = getGrade(avg);
            const finalRankings = computeRankings(globalQc, globalDelivery).sort((a,b)=>b.score-a.score);
            const avgRank = finalRankings.findIndex(r=>r.supplier===supplier) + 1;
            tableRows.push({ month:"Average", del:"—", qc:"—", score:avg.toFixed(1), grade:avgGrade, rank:avgRank });
        }
    }

    doc.autoTable({
        startY: subY + 5,
        columns: tableColumns,
        body: tableRows,
        theme: "grid",
        styles: { lineColor: black, lineWidth: 0.2 },
        headStyles: { fillColor: [255,255,255], textColor: black, fontStyle:"bold", halign:"center", fontSize:9.5, cellPadding:4, lineColor: black, lineWidth: 0.3 },
        bodyStyles: { halign:"center", fontSize:9, cellPadding:3, textColor: black },
        alternateRowStyles: { fillColor: [255,255,255] },
        columnStyles: supplier === "all"
            ? { 1:{ halign:"left", cellWidth:90 } }
            : { 0:{ halign:"left", cellWidth:50 } },
        didParseCell(data){
            // Keep everything plain black & white, but bold Total Score and Grade columns
            if(data.column.dataKey === "score" || data.column.dataKey === "grade"){
                data.cell.styles.fontStyle = "bold";
            }
        },
        margin: { left:10, right:10 },
    });

    const pageCount = doc.internal.getNumberOfPages();
    for(let i = 1; i <= pageCount; i++){
        doc.setPage(i);
        const footerY = 200;
        doc.setDrawColor(...black);
        doc.setLineWidth(0.2);
        doc.line(10, footerY-3, pageW-10, footerY-3);
        doc.setFontSize(7.5);
        doc.setFont("helvetica","normal");
        doc.setTextColor(...gray);
        doc.text("PT SANOH INDONESIA", 10, footerY+1);
        doc.text(`Page ${i} of ${pageCount}`, pageW-10, footerY+1, {align:"right"});
    }

    const filename = supplier === "all"
        ? `Supplier_Ranking_${label.replace(/ /g,"_")}.pdf`
        : `Ranking_${supplier.replace(/ /g,"_")}_${label.replace(/ /g,"_")}.pdf`;
    doc.save(filename);
}

/* =========================
   INIT
========================= */
document.addEventListener("DOMContentLoaded", () => {

    globalQc = qcRaw.map(d => ({
        supplier    : d.supplier    || "",
        total_score : Number(d.total_score || 0),
        month       : Number(d.del_month   || 0),
        year        : Number(d.del_year    || 0),
    }));

    globalDelivery = deliveryRaw.map(d => ({
        supplier : d.supplierSearch || "",
        total    : Number(d.total_score || 0),
        month    : Number(d.del_month   || 0),
        year     : Number(d.del_year    || 0),
    }));

    loadYearFilter(globalQc, globalDelivery);

    // ✅ Build & init searchable dropdown (gantikan loadSupplierFilter lama)
    buildSupplierList(globalQc, globalDelivery);
    initSupplierDropdown();

    const curMonth = new Date().getMonth() + 1;
    document.getElementById("monthFilter").value = curMonth;

    render();

    ["periodType","yearFilter","monthFilter","sortOrder"]
        .forEach(id => {
            document.getElementById(id)
                .addEventListener("change", () => {
                    const type = document.getElementById("periodType").value;
                    document.getElementById("yearGroup").style.display  = type !== "all" ? "" : "none";
                    document.getElementById("monthGroup").style.display = type === "monthly" ? "" : "none";
                    render();
                });
        });
});

</script>
@endpush