@extends('layouts.app')

@section('title', 'SPT Dashboard')

@section('page_title', 'Performance Dashboard')

@push('head')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
@endpush

@section('content')

<!-- TOPBAR -->
<div class="topbar">

    <div class="topbar-left">

        <!-- SUPPLIER -->
        <div class="filter-box">
            <div class="dropdown">

                <input
                    type="text"
                    id="supplierSearch"
                    placeholder="All Suppliers"
                    onclick="toggleSupplierDropdown()"
                    onkeyup="filterSuppliers()">

                <div id="supplierDropdown"
                    class="dropdown-list">
                </div>

            </div>
        </div>

        <!-- MONTH -->
        <div class="filter-box">
            <select id="monthFilter">
                <option value="ALL">All Month</option>
                <option value="0">January</option>
                <option value="1">February</option>
                <option value="2">March</option>
                <option value="3">April</option>
                <option value="4">May</option>
                <option value="5">June</option>
                <option value="6">July</option>
                <option value="7">August</option>
                <option value="8">September</option>
                <option value="9">October</option>
                <option value="10">November</option>
                <option value="11">December</option>
            </select>
        </div>

        <!-- YEAR -->
        <div class="filter-box">
            <select id="yearFilter">
                <option value="ALL">All Year</option>
            </select>
        </div>

        <!-- GENERATE REPORT (icon only) -->
        <button type="button"
                id="btnGenerateReport"
                onclick="generateDashboardReport()"
                title="Generate Report"
                style="width:38px; height:38px; border:1px solid #d1d5db; background:#fff; color:#1a3a8f; border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:15px;">
            <i class="fa-solid fa-print"></i>
        </button>

    </div>

    <div class="topbar-right">
        <p id="date"></p>
    </div>

</div>

<!-- PERFORMANCE -->
<div class="chart-section">
    <div class="chart-box">
        <h3>Supplier Performance</h3>
        <div class="chart-container tall">
            <canvas id="barChart"></canvas>
        </div>
    </div>
</div>

<!-- TREND -->
<div class="chart-row">
    <div class="chart-box">
        <h3>QC Score Per Supplier</h3>
        <div class="chart-container">
            <canvas id="qcChart"></canvas>
        </div>
    </div>

    <div class="chart-box">
        <h3>Delivery Score Per Supplier</h3>
        <div class="chart-container">
            <canvas id="delivChart"></canvas>
        </div>
    </div>
</div>

<!-- TOP -->
<div class="section">
    <div class="list-box">
        <h3>Top 5 Best Suppliers</h3>
        <div id="bestList"></div>
    </div>

    <div class="list-box">
        <h3>Top 5 Worst Suppliers</h3>
        <div id="worstList"></div>
    </div>
</div>

<!-- ASK AI (floating icon, bottom-right) -->
<button id="aiFloatBtn"
        onclick="toggleAIPanel()"
        title="Ask AI"
        style="position:fixed; bottom:24px; right:24px; width:54px; height:54px; border-radius:50%;
               background:#7c3aed; color:#fff; border:none; box-shadow:0 4px 14px rgba(124,58,237,0.45);
               font-size:20px; cursor:pointer; z-index:1000;">
    <i class="fa-solid fa-wand-magic-sparkles"></i>
</button>

<div id="aiPanel"
     style="display:none; flex-direction:column; position:fixed; bottom:90px; right:24px; width:340px;
            max-height:440px; background:#fff; border:1px solid #e5d4ff; border-radius:12px;
            box-shadow:0 10px 30px rgba(0,0,0,0.18); z-index:1000; overflow:hidden;">

    <div style="padding:12px 14px; background:#7c3aed; color:#fff; display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:13px; font-weight:600;">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
            Ask AI
        </span>
        <button onclick="toggleAIPanel()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:16px; line-height:1;">
            ×
        </button>
    </div>

    <div id="aiChatBody"
         style="flex:1; padding:12px 14px; overflow-y:auto; font-size:13px; line-height:1.6; color:#3b0764; max-height:300px;">
       <div style="color:#9061c7;">
        Ask anything about this system — for example<br>
        - which supplier need attention?<br>
        - how many documents are pending approval?<br>
        - list QC problems<br>
        - generate pdf supplier ranking for &lt;supplier name&gt;".
    </div>
    </div>

    <div style="padding:10px; border-top:1px solid #eee; display:flex; gap:8px;">
        <input type="text"
               id="aiQuestionInput"
               placeholder="Type your question..."
               style="flex:1; padding:8px 10px; border:1px solid #ddd; border-radius:6px; font-size:13px;"
               onkeydown="if(event.key==='Enter') askAI()">
        <button onclick="askAI()"
                id="btnAskAI"
                style="width:38px; border:none; background:#7c3aed; color:#fff; border-radius:6px; cursor:pointer;">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </div>

</div>

@endsection

@push('scripts')
<script>

/* ======================
THRESHOLD LINE
====================== */
const thresholdLine = {
    id:'thresholdLine',
    afterDraw(chart,args,pluginOptions){
        const { ctx, chartArea:{left,right}, scales:{y} } = chart;
        if(!y) return;
        ctx.save();
        ctx.beginPath();
        ctx.moveTo(left, y.getPixelForValue(pluginOptions.value));
        ctx.lineTo(right, y.getPixelForValue(pluginOptions.value));
        ctx.lineWidth = 1;
        ctx.strokeStyle = 'red';
        ctx.shadowColor = 'rgba(255,0,0,0.45)';
        ctx.shadowBlur = 6;
        ctx.stroke();
        ctx.restore();
    }
};

/* ======================
DATE
====================== */
document.getElementById("date").innerText =
new Date().toLocaleDateString('en-US',{
    weekday:'long', year:'numeric', month:'long', day:'numeric'
});

/* ======================
DATA FROM DATABASE
====================== */
const qc = @json($qcData);
const delivery = @json($deliveryData);

/* ======================
SUPPLIER SET
====================== */
let supplierSet = new Set([
    ...qc.map(d=>d.supplier).filter(Boolean),
    ...delivery.map(d=>d.supplierSearch).filter(Boolean)
]);
let suppliers = [...supplierSet];

function buildSupplierDropdown(data){

    const dropdown =
        document.getElementById("supplierDropdown");

    dropdown.innerHTML = "";

    // All Supplier
    const all = document.createElement("div");
    all.textContent = "All Suppliers";

    all.onclick = () => {

        selectedSupplier = "ALL";

        document.getElementById("supplierSearch").value = "";

        dropdown.style.display = "none";

        renderAll();

    };

    dropdown.appendChild(all);

    data.forEach(name=>{

        const div =
            document.createElement("div");

        div.textContent = name;

        div.onclick = ()=>{

            selectedSupplier = name;

            document.getElementById("supplierSearch").value = name;

            dropdown.style.display = "none";

            renderAll();

        };

        dropdown.appendChild(div);

    });

}

function filterSuppliers(){

    const keyword =
        document.getElementById("supplierSearch")
        .value
        .toLowerCase();

    const filtered =
        suppliers.filter(s=>
            s.toLowerCase().includes(keyword)
        );

    buildSupplierDropdown(filtered);

    document.getElementById("supplierDropdown")
        .style.display = "block";
}

function toggleSupplierDropdown(){

    const dropdown =
        document.getElementById("supplierDropdown");

    dropdown.style.display =
        dropdown.style.display==="block"
        ? "none"
        : "block";
}

document.addEventListener("click",function(e){

    const dropdown =
        document.querySelector(".dropdown");

    if(
        dropdown &&
        !dropdown.contains(e.target)
    ){

        document.getElementById(
            "supplierDropdown"
        ).style.display="none";

    }

});

/* ======================
SUPPLIER DROPDOWN
====================== */
let selectedSupplier = "ALL";

buildSupplierDropdown(suppliers);

/* ======================
YEAR DROPDOWN
====================== */
const yearFilter = document.getElementById("yearFilter");
let yearSet = new Set();
[...qc, ...delivery].forEach(item => {
    if(item.del_year) yearSet.add(Number(item.del_year));
});
[...yearSet].sort((a,b)=>b-a).forEach(year=>{
    let opt = document.createElement("option");
    opt.value = year;
    opt.textContent = year;
    yearFilter.appendChild(opt);
});

/* ======================
FILTER DATA
====================== */
function filterData(data){
    let supplier = selectedSupplier;
    let month = document.getElementById("monthFilter").value;
    let year = document.getElementById("yearFilter").value;

    return data.filter(item => {
        let itemSupplier = item.supplier || item.supplierSearch || "";
        let supplierMatch = supplier === "ALL" || itemSupplier === supplier;

        let itemMonth = item.del_month ? Number(item.del_month) - 1 : null;
        let monthMatch = month === "ALL" || itemMonth === Number(month);

        let itemYear = item.del_year ? Number(item.del_year) : null;
        let yearMatch = year === "ALL" || itemYear === Number(year);

        return supplierMatch && monthMatch && yearMatch;
    });
}

/* ======================
SUPPLIER PERFORMANCE
====================== */
function getSupplierPerformance(name){
    let qcFiltered = filterData(qc).filter(d => d.supplier === name);
    let delFiltered = filterData(delivery).filter(d => d.supplierSearch === name);

    let qcAvg = qcFiltered.length
        ? qcFiltered.reduce((s,x) => s + (Number(x.total_score)||0), 0) / qcFiltered.length
        : 0;

    let delAvg = delFiltered.length
        ? delFiltered.reduce((s,x) => s + (Number(x.total_score)||0), 0) / delFiltered.length
        : 0;

    return ((qcAvg + delAvg) / 2).toFixed(1);
}

/* ======================
BEST WORST
====================== */
let currentRanking = [];

function renderRankingList(){
    let ranking = suppliers.map(name=>{
        let score = getSupplierPerformance(name);
        let grade = score >= 80 ? "A" : score >= 60 ? "B" : score >= 40 ? "C" : "D";
        return { name, score, grade };
    });

    currentRanking = ranking;

    let sorted = [...ranking].sort((a,b)=> b.score - a.score);
    renderList(sorted.slice(0,5), "bestList");
    renderList(sorted.slice(-5).reverse(), "worstList");
}

function renderList(data,id){
    let el = document.getElementById(id);
    el.innerHTML = "";
    data.forEach((item,i)=>{
        el.innerHTML += `
        <div class="item">
            <span>${i+1}. ${item.name}</span>
            <span><b>${item.score}</b> <small>(${item.grade})</small></span>
        </div>`;
    });
}

/* ======================
BAR CHART
====================== */
let barChart;
function renderBarChart(){
    let filteredSuppliers = suppliers.filter(name=>{
        let q = filterData(qc).filter(d=>d.supplier===name);
        let d = filterData(delivery).filter(d=>d.supplierSearch===name);
        return q.length || d.length;
    });

    if(barChart) barChart.destroy();
    barChart = new Chart(document.getElementById("barChart"), {
        type:'bar',
        data:{
            labels: filteredSuppliers,
            datasets:[{
                data: filteredSuppliers.map(s=> getSupplierPerformance(s)),
                backgroundColor:'#2d5fde',
                borderRadius:12,
                maxBarThickness:240,
                categoryPercentage:0.95,
                barPercentage:0.95
            }]
        },
        options:{
            responsive:true,
            maintainAspectRatio:false,
            plugins:{ legend:{ display:false } },
            scales:{
                x:{ grid:{ display:false } },
                y:{ beginAtZero:true, max:100, ticks:{ stepSize:10 } }
            }
        }
    });
}

/* ======================
TREND CHART
====================== */
/* ======================
TREND CHART (PER BULAN)
====================== */
let qcChart;
let delivChart;

const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

function renderCharts(){
    let qcFiltered = filterData(qc);
    let deliveryFiltered = filterData(delivery);

    // Group QC by bulan
    let qcMonthMap = {};
    qcFiltered.forEach(item => {
        if(item.del_month == null) return;
        let key = Number(item.del_month) - 1;
        if(!qcMonthMap[key]) qcMonthMap[key] = [];
        qcMonthMap[key].push(Number(item.total_score) || 0);
    });

    // Group Delivery by bulan
    let delivMonthMap = {};
    deliveryFiltered.forEach(item => {
        if(item.del_month == null) return;
        let key = Number(item.del_month) - 1;
        if(!delivMonthMap[key]) delivMonthMap[key] = [];
        delivMonthMap[key].push(Number(item.total_score) || 0);
    });

    // Paksa semua 12 bulan, 0 kalau tidak ada data
    let allMonths = [0,1,2,3,4,5,6,7,8,9,10,11];

    let qcData = allMonths.map(k =>
        qcMonthMap[k]
            ? (qcMonthMap[k].reduce((a,b)=>a+b,0) / qcMonthMap[k].length).toFixed(1)
            : 0
    );

    let delivData = allMonths.map(k =>
        delivMonthMap[k]
            ? (delivMonthMap[k].reduce((a,b)=>a+b,0) / delivMonthMap[k].length).toFixed(1)
            : 0
    );

    if(qcChart) qcChart.destroy();
    if(delivChart) delivChart.destroy();

   qcChart = new Chart(document.getElementById("qcChart"), {
    type:'line',
    data:{ 
        labels: monthNames, 
        datasets:[{ 
            data: qcData, 
            borderColor:'#f59e0b',
            backgroundColor:'rgba(245,158,11,0.12)',
            borderWidth: 2.5,
            pointBackgroundColor:'#f59e0b',
            pointRadius: 5,
            pointHoverRadius: 7,
            fill: true,
            tension: 0.4
        }] 
    },
    options:{
        responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false }, thresholdLine:{ value:20 } },
        scales:{ y:{ beginAtZero:true, max:100, ticks:{ stepSize:10 } } }
    },
    plugins:[thresholdLine]
});

delivChart = new Chart(document.getElementById("delivChart"), {
    type:'line',
    data:{ 
        labels: monthNames, 
        datasets:[{ 
            data: delivData, 
            borderColor:'#f59e0b',
            backgroundColor:'rgba(245,158,11,0.12)',
            borderWidth: 2.5,
            pointBackgroundColor:'#f59e0b',
            pointRadius: 5,
            pointHoverRadius: 7,
            fill: true,
            tension: 0.4
        }] 
    },
    options:{
        responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false }, thresholdLine:{ value:95 } },
        scales:{ y:{ beginAtZero:true, max:100, ticks:{ stepSize:10 } } }
    },
    plugins:[thresholdLine]
});
}
/* ======================
RENDER ALL
====================== */
function renderAll(){
    renderBarChart();
    renderCharts();
    renderRankingList();
}

/* ======================
EVENT
====================== */
document.getElementById("monthFilter").addEventListener("change", renderAll);
document.getElementById("yearFilter").addEventListener("change", renderAll);

/* ======================
INIT
====================== */
renderAll();

/* ======================================================
   GENERATE REPORT (PDF) — data Dashboard yang sedang tampil
====================================================== */
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

function getDashboardPeriodLabel(){
    const month = document.getElementById("monthFilter").value;
    const year  = document.getElementById("yearFilter").value;

    const monthLabel = month === "ALL" ? "" : monthNames[Number(month)];
    const yearLabel   = year === "ALL" ? "All Year" : year;

    if(month === "ALL"){
        return yearLabel;
    }

    return `${monthLabel} ${yearLabel}`;
}

function generateDashboardReport(){
    getLogoBase64(function(logoData, logoW, logoH){
        _buildDashboardReport(logoData, logoW, logoH);
    });
}

function _buildDashboardReport(logoData, logoW, logoH){

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation:"landscape", unit:"mm", format:"a4" });

    const pageW   = 297;
    const brand   = [26, 58, 143];   // matches the #1a3a8f accent used on-screen
    const black   = [30, 30, 30];
    const gray    = [110, 110, 110];
    const cardBg  = [245, 247, 252];
    const dateStr = new Date().toLocaleDateString("en-US",{day:"2-digit",month:"long",year:"numeric"});

    const subtitleText =
        `Supplier: ${selectedSupplier === "ALL" ? "All Suppliers" : selectedSupplier}   |   Period: ${getDashboardPeriodLabel()}`;

    /* ── Reusable header, redrawn on every page ── */
    function drawHeader(subtitle){
        const headerH = 24;

        const logoMaxH = 14, logoMaxW = 34;
        if(logoData){
            let lw = logoMaxW, lh = logoMaxH;
            const aspect = logoW / logoH;
            if(aspect > logoMaxW / logoMaxH){ lh = lw / aspect; }
            else { lw = lh * aspect; }
            doc.addImage(logoData, "PNG", 10, (headerH - lh) / 2, lw, lh);
        }

        doc.setTextColor(...black);
        doc.setFont("helvetica","bold");
        doc.setFontSize(15);
        doc.text("SUPPLIER PERFORMANCE DASHBOARD REPORT", pageW / 2, 11, {align:"center"});

        doc.setFont("helvetica","normal");
        doc.setFontSize(9.5);
        doc.setTextColor(...gray);
        doc.text(subtitle, pageW / 2, 17.5, {align:"center"});

        doc.setFontSize(9);
        doc.text(`Date : ${dateStr}`, pageW - 10, 9, {align:"right"});

        doc.setDrawColor(...brand);
        doc.setLineWidth(0.8);
        doc.line(10, headerH, pageW - 10, headerH);

        return headerH;
    }

    function drawCardLabel(text, x, y){
        doc.setFont("helvetica","bold");
        doc.setFontSize(11);
        doc.setTextColor(...brand);
        doc.text(text, x, y);
    }

    /* ============================================================
       PAGE 1 — CHARTS, each in its own light card for breathing room
    ============================================================ */
    let cursorY = drawHeader(subtitleText) + 10;

    if(barChart){
        const chartImg = barChart.toBase64Image();
        const cardH = 72;

        doc.setFillColor(...cardBg);
        doc.roundedRect(10, cursorY - 6, pageW - 20, cardH, 3, 3, "F");

        drawCardLabel("Supplier Performance", 15, cursorY);
        doc.addImage(chartImg, "PNG", 13, cursorY + 4, pageW - 26, cardH - 12);

        cursorY += cardH + 8;
    }

    if(qcChart && delivChart){
        const qcImg    = qcChart.toBase64Image();
        const delivImg = delivChart.toBase64Image();
        const cardH    = 62;
        const gap      = 6;
        const colW     = (pageW - 20 - gap) / 2;

        doc.setFillColor(...cardBg);
        doc.roundedRect(10, cursorY - 6, colW, cardH, 3, 3, "F");
        doc.roundedRect(10 + colW + gap, cursorY - 6, colW, cardH, 3, 3, "F");

        drawCardLabel("QC Score Trend", 15, cursorY);
        drawCardLabel("Delivery Score Trend", 15 + colW + gap, cursorY);

        doc.addImage(qcImg,    "PNG", 13,              cursorY + 4, colW - 6, cardH - 12);
        doc.addImage(delivImg, "PNG", 13 + colW + gap,  cursorY + 4, colW - 6, cardH - 12);

        cursorY += cardH + 8;
    }

    doc.setFontSize(8);
    doc.setTextColor(...gray);
    doc.setFont("helvetica","italic");
    doc.text("Top 5 best & worst suppliers on the next page.", 10, cursorY);

    /* ============================================================
       PAGE 2 — TOP 5 BEST / TOP 5 WORST, two compact tables
    ============================================================ */
    doc.addPage();
    cursorY = drawHeader("Top 5 Best & Worst Suppliers") + 12;

    const sortedRanking = [...currentRanking].sort((a,b)=> b.score - a.score);
    const best5  = sortedRanking.slice(0, 5);
    const worst5 = sortedRanking.slice(-5).reverse();

    const gradeColors = {
        A: [22, 163, 74],
        B: [37, 99, 235],
        C: [217, 119, 6],
        D: [220, 38, 38],
    };

    const tableW   = 116;
    const gap      = 10;
    const startX   = (pageW - (tableW * 2 + gap)) / 2;
    const headBg   = [230, 235, 247];

    const commonTableOptions = {
        theme: "grid",
        styles: { lineColor: [222,225,232], lineWidth: 0.15, fontSize: 9, cellPadding: 3, textColor: black },
        headStyles: { fillColor: headBg, textColor: brand, fontStyle:"bold", halign:"center", fontSize: 9.5 },
        bodyStyles: { halign:"center" },
        tableWidth: tableW,
        columnStyles: {
            0:{ cellWidth: 15 },
            1:{ halign:"left", cellWidth: 62 },
            2:{ cellWidth: 22, fontStyle:"bold" },
            3:{ cellWidth: 17, fontStyle:"bold" },
        },
        didParseCell(data){
            if(data.section === "body" && data.column.index === 3 && gradeColors[data.cell.raw]){
                data.cell.styles.textColor = gradeColors[data.cell.raw];
            }
        },
    };

    doc.setFont("helvetica","bold");
    doc.setFontSize(10.5);
    doc.setTextColor(...brand);
    doc.text("Top 5 Best Suppliers", startX, cursorY - 3);
    doc.text("Top 5 Worst Suppliers", startX + tableW + gap, cursorY - 3);

    doc.autoTable({
        ...commonTableOptions,
        startY: cursorY,
        margin: { left: startX },
        head: [["Rank", "Supplier", "Score", "Grade"]],
        body: best5.map((r, i) => [i + 1, r.name, r.score, r.grade]),
    });

    doc.autoTable({
        ...commonTableOptions,
        startY: cursorY,
        margin: { left: startX + tableW + gap },
        head: [["Rank", "Supplier", "Score", "Grade"]],
        body: worst5.map((r, i) => [i + 1, r.name, r.score, r.grade]),
    });

    /* ── FOOTER (all pages) ── */
    const pageCount = doc.internal.getNumberOfPages();
    for(let i = 1; i <= pageCount; i++){
        doc.setPage(i);
        const footerY = 200;
        doc.setDrawColor(...gray);
        doc.setLineWidth(0.2);
        doc.line(10, footerY-3, pageW-10, footerY-3);
        doc.setFontSize(7.5);
        doc.setFont("helvetica","normal");
        doc.setTextColor(...gray);
        doc.text("PT SANOH INDONESIA", 10, footerY+1);
        doc.text(`Page ${i} of ${pageCount}`, pageW-10, footerY+1, {align:"right"});
    }

    const filename = `Dashboard_Report_${getDashboardPeriodLabel().replace(/ /g,"_")}.pdf`;
    doc.save(filename);
}

/* ======================================================
   ASK AI — kotak tanya-jawab bebas soal data yang tampil
   + bisa trigger generate/export report (Dashboard / Ranking / Report)
====================================================== */

// Sesuaikan 2 URL ini kalau nama route di project kamu berbeda.
const RANKING_URL     = "/ranking";
const REPORT_PAGE_URL = "/report";

function toggleAIPanel(){
    const panel = document.getElementById("aiPanel");
    const isOpen = panel.style.display === "flex";
    panel.style.display = isOpen ? "none" : "flex";

    if(!isOpen){
        document.getElementById("aiQuestionInput").focus();
    }
}

function appendAIMessage(role, text){
    const body = document.getElementById("aiChatBody");

    const bubble = document.createElement("div");
    bubble.style.marginTop = "10px";

    if(role === "user"){
        bubble.style.textAlign = "right";
        bubble.innerHTML = `<span style="display:inline-block; background:#ede9fe; color:#3b0764; padding:6px 10px; border-radius:8px; max-width:85%;">${text}</span>`;
    } else {
        bubble.innerHTML = `<span style="display:inline-block; background:#f5f3ff; color:#3b0764; padding:6px 10px; border-radius:8px; max-width:90%; white-space:pre-line;">${text}</span>`;
    }

    body.appendChild(bubble);
    body.scrollTop = body.scrollHeight;
}

/**
 * Executes the `action` object returned by the backend, if any.
 * - target "dashboard" -> runs the existing PDF generator right here.
 * - target "ranking"   -> redirects to Supplier Ranking with query params
 *                         that auto-trigger PDF/Excel export on load.
 * - target "report"    -> redirects to Print Report with query params
 *                         that auto-trigger the report generation on load.
 */
function handleAIAction(action){
    if(!action || action.type !== "export"){
        return;
    }

    if(action.target === "dashboard"){
        appendAIMessage("ai", "Generating the Dashboard PDF report now...");
        generateDashboardReport();
        return;
    }

    if(action.target === "ranking"){
        const params = new URLSearchParams();
        params.set("ai_export", action.format || "pdf");
        if(action.supplier){
            params.set("ai_supplier", action.supplier);
        }

        appendAIMessage("ai", `Opening Supplier Ranking to generate the ${(action.format || "pdf").toUpperCase()} file...`);

        setTimeout(() => {
            window.location.href = `${RANKING_URL}?${params.toString()}`;
        }, 600);
        return;
    }

    if(action.target === "report"){
        const params = new URLSearchParams();
        params.set("ai_export", "1");
        if(action.supplier){
            params.set("ai_supplier", action.supplier);
        }

        appendAIMessage("ai", "Opening Print Report to generate the file...");

        setTimeout(() => {
            window.location.href = `${REPORT_PAGE_URL}?${params.toString()}`;
        }, 600);
        return;
    }
}

async function askAI(){

    const input   = document.getElementById("aiQuestionInput");
    const question = input.value.trim();

    if(!question){
        return;
    }

    appendAIMessage("user", question);
    input.value = "";

    const askBtn = document.getElementById("btnAskAI");
    askBtn.disabled = true;

    appendAIMessage("ai", "Thinking...");
    const body = document.getElementById("aiChatBody");
    const thinkingBubble = body.lastElementChild;

    const sortedRanking = [...currentRanking].sort((a,b)=> b.score - a.score);

    const summary = {
        supplier: selectedSupplier === "ALL" ? "All Suppliers" : selectedSupplier,
        period:   getDashboardPeriodLabel(),
        totalSuppliers: sortedRanking.length,
        best:  sortedRanking.slice(0, 5),
        worst: sortedRanking.slice(-5).reverse(),
        averageScore: sortedRanking.length
            ? (sortedRanking.reduce((s, r) => s + Number(r.score), 0) / sortedRanking.length).toFixed(1)
            : 0,
        allSuppliers: sortedRanking,
    };

    try{

        const response = await fetch('/dashboard/ai-ask', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ summary, question })
        });

        const result = await response.json();

        thinkingBubble.remove();
        appendAIMessage("ai", result.answer || "No answer available for this question.");

        handleAIAction(result.action);

    }catch(error){

        console.error(error);
        thinkingBubble.remove();
        appendAIMessage("ai", "Failed to get an answer. Please try again.");

    }finally{

        askBtn.disabled = false;
    }
}

</script>
@endpush