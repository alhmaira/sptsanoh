/* ===============================
   GLOBAL VARIABLE
================================*/
console.log("REPORT JS LOADED");
let suppliers = [];
let reports = [];
let selectedSupplier = "";

/* ===============================
   INITIALIZE
================================*/

document.addEventListener("DOMContentLoaded", async () => {

    initYear();

    await loadSupplier();
    await loadReport();

    bindEvents();

});


/* ===============================
   LOAD DATA
================================*/

async function loadSupplier(){

    const res = await fetch("/api/suppliers");

    const text = await res.text();
    console.log("RAW RESPONSE:", text);

    const data = JSON.parse(text);
    console.log("PARSED:", data);

    suppliers = data;

    buildSupplierDropdown(suppliers);
}

/* ===============================
   FILTER
================================*/

function bindEvents(){

    document
    .getElementById("month")
    .addEventListener("change", loadReport);


    document
    .getElementById("year")
    .addEventListener("change", loadReport);


    document
    .getElementById("supplierSearch")
    .addEventListener("input", filterSuppliers);


    document
    .getElementById("supplierSearch")
    .addEventListener("focus", ()=>{

        document
        .getElementById("supplierDropdown")
        .style.display="block";

    });

}

function renderSupplierDropdown(){}


/* ===============================
   TABLE
================================*/
function renderTable(){

    let html = `
    <table class="report-table">
        <thead>
            <tr>
                <th>Doc Number</th>
                <th>Supplier</th>
                <th>Period</th>
                <th>QC</th>
                <th>Delivery</th>
                <th>Final</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
    `;

    if(reports.length===0){

        html += `
        <tr>
            <td colspan="7" style="text-align:center">
                No Approved Data
            </td>
        </tr>`;

    }else{

        reports.forEach(row=>{

            html += `
            <tr>

                <td>${row.doc_number}</td>

                <td>${row.supplier}</td>

                <td>${formatPeriod(row.del_month, row.del_year)}</td>

                <td>${Number(row.qc_score).toFixed(2)}</td>

                <td>${Number(row.delivery_score).toFixed(2)}</td>

                <td>${Number(row.final_score).toFixed(2)}</td>

                <td>
                    <button class="print-icon-btn"
                        title="Print"
                        onclick="downloadDetailPDF('${row.doc_number}')">
                        <i class="fa-solid fa-print"></i>
                    </button>
                </td>

            </tr>`;
        });

    }

    html += `
        </tbody>
    </table>`;

    document.getElementById("reportArea").innerHTML = html;

}

/* ===============================
   SUPPLIER DROPDOWN
================================*/

function buildSupplierDropdown(data){

    const dropdown = document.getElementById("supplierDropdown");

    dropdown.innerHTML = "";

    // ===== ALL SUPPLIER =====
    const all = document.createElement("div");
    all.className = "option";
    all.innerHTML = "All Supplier";

    all.onclick = () => {

        selectedSupplier = "";

        document.getElementById("supplierSearch").value = "";

        dropdown.style.display = "none";

        loadReport();
    };

    dropdown.appendChild(all);

    // ===== LIST SUPPLIER =====
    data.forEach(item => {

        const name =
            item.supplier_name ??
            item.bp_name ??
            item.name ??
            "-";

        const div = document.createElement("div");

        div.className = "option";
        div.innerHTML = name;

        div.onclick = () => {

            selectedSupplier = name;

            document.getElementById("supplierSearch").value = name;

            dropdown.style.display = "none";

            loadReport();
        };

        dropdown.appendChild(div);
    });
}

function filterSuppliers(){

    const keyword =
        document
        .getElementById("supplierSearch")
        .value
        .toLowerCase();

    if(keyword === ""){
        selectedSupplier = "";
        buildSupplierDropdown(suppliers);
        loadReport();
        return;
    }


    const filtered = suppliers.filter(item => {

        const name =
            item.supplier_name ??
            item.bp_name ??
            item.name ??
            "";

        return name.toLowerCase().includes(keyword);

    });

    buildSupplierDropdown(filtered);
}

function initYear(){

    const year = document.getElementById("year");

    year.innerHTML = "";

    const now = new Date().getFullYear();

    for(let i=now-3;i<=now+2;i++){

        const option=document.createElement("option");

        option.value=i;
        option.text=i;

        if(i===now){
            option.selected=true;
        }

        year.appendChild(option);
    }

}

/* ===============================
   PRINT SUMMARY
================================*/

async function printSummary(){}

function buildSummaryHTML(){}


/* ===============================
   PRINT DETAIL
================================*/

async function printDetail(doc_number){}

function buildDetailHTML(qc, delivery, signatures){

return `

${buildQCTable(qc)}

${buildDeliveryTable(delivery)}

${buildFinalTable(qc, delivery)}

${buildApprovalTable(signatures)}

`;

}

function buildQCTable(qc){

return `
<h3 class="section-title">QC Detail</h3>

<table class="pdf-table">

<thead>
<tr>
<th width="8%">No</th>
<th width="42%">Category</th>
<th width="25%">Type</th>
<th width="25%">Value</th>
</tr>
</thead>

<tbody>

<tr>
<td>1</td>
<td rowspan="2">Line Stop</td>
<td>Status</td>
<td>${qc.lineStop > 0 ? "YES" : "NO"}</td>
</tr>

<tr>
<td>2</td>
<td>Point</td>
<td>${qc.rank_score}</td>
</tr>

<tr>
<td>3</td>
<td rowspan="4">PPM</td>
<td>NG</td>
<td>${qc.ng}</td>
</tr>

<tr>
<td>4</td>
<td>Supply</td>
<td>${qc.supply}</td>
</tr>

<tr>
<td>5</td>
<td>PPM</td>
<td>${qc.ppm}</td>
</tr>

<tr>
<td>6</td>
<td>Point</td>
<td>${qc.ppmScore}</td>
</tr>

<tr>
<td>7</td>
<td rowspan="2">Problem Rank</td>
<td>Rank</td>
<td>${qc.rank_score}</td>
</tr>

<tr>
<td>8</td>
<td>Point</td>
<td>${qc.rank_score}</td>
</tr>

<tr>
<td>9</td>
<td rowspan="2">FPPK</td>
<td>Status</td>
<td>${qc.fppk > 0 ? "YES":"NO"}</td>
</tr>

<tr>
<td>10</td>
<td>Point</td>
<td>${qc.fppk}</td>
</tr>

<tr class="total-row">

<td colspan="3">
<b>Total Score</b>
</td>

<td>
<b>${qc.total_score}</b>
</td>

</tr>

</tbody>

</table>
`;

}
function buildDeliveryTable(del){

return `
<h3 class="section-title">Delivery Detail</h3>

<table class="pdf-table">

<thead>

<tr>

<th>No</th>
<th>Category</th>
<th>Type</th>
<th>Value</th>

</tr>

</thead>

<tbody>

<tr>
<td>1</td>
<td rowspan="2">Fulfillment</td>
<td>Index</td>
<td>${del.fulfillment}</td>
</tr>

<tr>
<td>2</td>
<td>Point</td>
<td>${calculateQtyIndex(del.fulfillment)}</td>
</tr>

<tr>
<td>3</td>
<td rowspan="2">On Time Delivery</td>
<td>Day</td>
<td>${getOTDText(del.otd)}</td>
</tr>

<tr>
<td>4</td>
<td>Index</td>
<td>${del.otd}</td>
</tr>

<tr>
<td>5</td>
<td rowspan="2">Delivery Method</td>
<td>Method</td>
<td>${getMethodText(del.del_method)}</td>
</tr>

<tr>
<td>6</td>
<td>Index</td>
<td>${del.del_method}</td>
</tr>

<tr>
<td>7</td>
<td rowspan="2">Premium Freight</td>
<td>IDR</td>
<td>${del.premium}</td>
</tr>

<tr>
<td>8</td>
<td>Index</td>
<td>${calculatePremiumIndex(del.premium)}</td>
</tr>

<tr>
<td>9</td>
<td rowspan="2">DPS Reply</td>
<td>Reply</td>
<td>${getDPSText(del.dps)}</td>
</tr>

<tr>
<td>10</td>
<td>Index</td>
<td>${del.dps}</td>
</tr>

<tr class="total-row">

<td colspan="3">
<b>Total Score</b>
</td>

<td>
<b>${del.total_score}</b>
</td>

</tr>

</tbody>

</table>
`;

}

function buildFinalTable(qc,delivery){

const finalScore=((Number(qc.total_score)+Number(delivery.total_score))/2).toFixed(2);

return `

<h3 class="section-title">
Final Result
</h3>

<table class="pdf-table">

<thead>

<tr>

<th>QC Total</th>
<th>Delivery Total</th>
<th>Final Score</th>
<th>Grade</th>

</tr>

</thead>

<tbody>

<tr>

<td>${qc.total_score}</td>

<td>${delivery.total_score}</td>

<td>${finalScore}</td>

<td>${getGrade(finalScore)}</td>

</tr>

</tbody>

</table>

`;

}

function buildApprovalTable(signatures){

let header="";
let image="";
let name="";

signatures.forEach(item=>{

header+=`
<th>
${item.role_name}<br>
${item.department}
</th>
`;

image+=`
<td>

${
item.signature
?
`<img src="/storage/${item.signature}" class="signature-img">`
:
"-"
}

</td>
`;

name+=`
<td>${item.user_name}</td>
`;

});

return `

<h3 class="section-title">

Approval Signatures

</h3>

<table class="pdf-table">

<thead>

<tr>

${header}

</tr>

</thead>

<tbody>

<tr>

${image}

</tr>

<tr>

${name}

</tr>

</tbody>

</table>

`;

}

/* ===============================
   PDF
================================*/

function generatePDF(docNumber){

    const element =
        document.getElementById("pdfReport");

    element.style.display="block";

    html2pdf()

        .set({

            margin:0.3,

            filename:`Detail_${docNumber}.pdf`,

            image:{
                type:"jpeg",
                quality:1
            },

            html2canvas:{
                scale:2,
                useCORS:true
            },

            jsPDF:{
                unit:"mm",
                format:"a4",
                orientation:"portrait"
            }

        })

        .from(element)

        .save()

        .then(()=>{

            element.style.display="none";

        });

}

async function downloadDetailPDF(docNo){

    window.location.href =
    `/report/detail/pdf/${docNo}`;

}

document.addEventListener("DOMContentLoaded",()=>{


document
.getElementById("printSummary")
.addEventListener("click",()=>{


    let supplier =
    document.getElementById("supplierSearch").value;


    let month =
    document.getElementById("month").value;


    let year =
    document.getElementById("year").value;



    console.log({
        supplier,
        month,
        year
    });



    if(
        supplier !== "" &&
        month === ""
    ){

        let url =
        `/report/yearly-pdf?supplier=${encodeURIComponent(supplier)}&year=${year}`;


        console.log("YEARLY URL",url);


        window.location.href = url;


    }else{


        let url =
        `/report/pdf-summary?supplier=${encodeURIComponent(supplier)}&month=${month}&year=${year}`;


        console.log("OLD URL",url);


        window.location.href = url;

    }



});


});

/* ===============================
   HELPER
================================*/

function getGrade(){}

function formatPeriod(month, year) {
    if (!month || !year) return "-";

    const date = new Date(year, month - 1);

    return date.toLocaleString("en-US", {
        month: "long",
        year: "numeric"
    });
}

function getOTDText(){}

function getMethodText(){}

function getDPSText(){}

function calculateQtyIndex(){}

function calculatePremiumIndex(){}

async function loadReport(){

    console.log("LOAD REPORT START");

    const supplier = selectedSupplier;
    const month = document.getElementById("month").value;
    const year = document.getElementById("year").value;

    const params = new URLSearchParams({
        supplier,
        month,
        year
    });

    console.log(params.toString());

    const response = await fetch(`${REPORT_URL}?${params}`);

    console.log(response.status);

    reports = await response.json();

    console.log(reports);

    renderTable();

}