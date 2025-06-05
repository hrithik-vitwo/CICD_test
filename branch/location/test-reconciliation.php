<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/company/func-branches.php");
?>
<!-- <link rel="stylesheet" href="../../public/assets/sales-order.css"> -->
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
  .reconListItemCard {
    margin: 3px;
    padding: 0px;
    padding-bottom: 3px;
    line-height: 0.85;
    border: 1px solid rgb(97, 96, 96);
  }

  .reconListItemCard span {
    font-size: 1.5vh;
    color: rgb(104, 104, 104);
  }

  .reconListItemCard p {
    color: #868686;
    padding: 0px;
    margin: 0px;
    font-weight: bold;
  }

  .reconListItemCardRight {
    cursor: move;
  }

</style>

<div class="content-wrapper">
  <section>
    <div class="row p-0 m-0 bg-primary pt-3">
      <div class="col-4" style="height: 10vh;">
        <p class="p-0 m-0 text-center font-weight-bold">Total Amount</p>
        <p class="p-0 m-0 text-center font-weight-bold">2000.00</p>
      </div>
      <div class="col-4" style="height: 10vh;">
        <p class="p-0 m-0 text-center font-weight-bold">Total Due</p>
        <p class="p-0 m-0 text-center font-weight-bold">150.00</p>
      </div>
      <div class="col-4" style="height: 10vh;">
        <p class="p-0 m-0 text-center font-weight-bold">Total Advance</p>
        <p class="p-0 m-0 text-center font-weight-bold">4300.00</p>
      </div>
    </div>
  </section>
  <section>
    <div class="col-12 p-0 m-0" id="reconListDiv"></div>
  </section>
</div>
<?php require_once("../common/footer.php"); ?>




<script>
  let localDataList = [{
      "invoiceNo": "INV1234",
      "invoiceAmount": 100.00,
      "vendorGstin": "GSTIN123",
      "vendorName": "ABC Corp",
      "invoiceTax": 18.00,
      "isItcAvl": true
    },
    {
      "invoiceNo": "INV5678",
      "invoiceAmount": 200.50,
      "vendorGstin": "GSTIN456",
      "vendorName": "XYZ Inc",
      "invoiceTax": 36.09,
      "isItcAvl": false
    },
    {
      "invoiceNo": "INV9101",
      "invoiceAmount": 50.00,
      "vendorGstin": "GSTIN789",
      "vendorName": "PQR Co",
      "invoiceTax": 9.00,
      "isItcAvl": true
    },
    {
      "invoiceNo": "INV1121",
      "invoiceAmount": 300.00,
      "vendorGstin": "GSTIN234",
      "vendorName": "LMN Ltd",
      "invoiceTax": 54.00,
      "isItcAvl": true
    },
    {
      "invoiceNo": "INV3141",
      "invoiceAmount": 75.50,
      "vendorGstin": "GSTIN567",
      "vendorName": "DEF Group",
      "invoiceTax": 13.59,
      "isItcAvl": false
    },
    {
      "invoiceNo": "INV5161",
      "invoiceAmount": 150.00,
      "vendorGstin": "GSTIN891",
      "vendorName": "GHI Enterprises",
      "invoiceTax": 27.00,
      "isItcAvl": true
    },
    {
      "invoiceNo": "INV7181",
      "invoiceAmount": 250.75,
      "vendorGstin": "GSTIN234",
      "vendorName": "JKL Inc",
      "invoiceTax": 45.14,
      "isItcAvl": false
    },
    {
      "invoiceNo": "INV9201",
      "invoiceAmount": 125.00,
      "vendorGstin": "GSTIN567",
      "vendorName": "MNO Corp",
      "invoiceTax": 22.50,
      "isItcAvl": true
    },
    {
      "invoiceNo": "INV1221",
      "invoiceAmount": 175.25,
      "vendorGstin": "GSTIN891",
      "vendorName": "PQR Co",
      "invoiceTax": 31.55,
      "isItcAvl": false
    },
    {
      "invoiceNo": "INV3241",
      "invoiceAmount": 80.00,
      "vendorGstin": "GSTIN234",
      "vendorName": "ABC Corp",
      "invoiceTax": 14.40,
      "isItcAvl": true
    }
  ];

  let portalDataList = [{
      "invoiceNo": "INV9101",
      "invoiceAmount": 55.00,
      "vendorGstin": "GSTIN780",
      "vendorName": "PQR Co",
      "invoiceTax": 9.00,
      "isItcAvl": true
    },
    {
      "invoiceNo": "INV1121",
      "invoiceAmount": 300.00,
      "vendorGstin": "GSTIN234",
      "vendorName": "LMN Ltd",
      "invoiceTax": 54.00,
      "isItcAvl": true
    },
    {
      "invoiceNo": "INV3142",
      "invoiceAmount": 75.50,
      "vendorGstin": "GSTIN567",
      "vendorName": "DEF Group",
      "invoiceTax": 13.59,
      "isItcAvl": false
    },
    {
      "invoiceNo": "INV5161",
      "invoiceAmount": 150.00,
      "vendorGstin": "GSTIN891",
      "vendorName": "GHI Enterprises",
      "invoiceTax": 27.00,
      "isItcAvl": true
    },
    {
      "invoiceNo": "INV7181",
      "invoiceAmount": 250.75,
      "vendorGstin": "GSTIN234",
      "vendorName": "JKL Inc",
      "invoiceTax": 45.14,
      "isItcAvl": false
    },
    {
      "invoiceNo": "INV9201",
      "invoiceAmount": 125.00,
      "vendorGstin": "GSTIN567",
      "vendorName": "MNO Corp",
      "invoiceTax": 22.50,
      "isItcAvl": true
    }
  ];


  function updateTheLocalPortalList() {
    let maxRow = Math.max(localDataList.length, portalDataList.length);
    console.log(maxRow);
    for (let i = 0; i < maxRow; i++) {
      let localData = localDataList[i];
      let portalData = portalDataList[i];
      if (portalData) {
        console.log(portalData);
      }
      if (localData) {
        console.log(localData);
      }
      console.log("for", i, localData, portalData);
      $("#reconListDiv").append(`
        <div class="row p-0 m-1 reconListItemRow border matched-100" id="row-${i}">
            <div class="col-6 m-0 p-0 pr-1 reconListItemColLeft" id="rowLeft-${i}">
                ${localData ? (`
                <div class="row reconListItemCard reconListItemCardLeft" id="rowLeftDiv-${i}">
                    <div class="col-md-4 col-sm-6">
                        <span>${i} Invoice Number</span>
                        <p>${localData.invoiceNo}</p>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <span>GSTIN</span>
                        <p>${localData.vendorGstin}</p>
                    </div>
                    <div class="col-md-4 col-sm-6 d-none d-sm-block">
                        <span>Vendor name</span>
                        <p>${localData.vendorName}</p>
                    </div>
                    <div class="col-md-4 col-sm-6 d-none d-sm-block">
                        <span>Invoice Amount</span>
                        <p>${localData.invoiceAmount}</p>
                    </div>
                    <div class="col-md-4 col-sm-6 d-none d-sm-block">
                        <span>Tax Amount</span>
                        <p>${localData.invoiceTax}</p>
                    </div>
                    <div class="col-md-4 col-sm-6 d-none d-sm-block">
                        <span>ITC ?</span><br>
                        <p>${localData.isItcAvl}</p>
                    </div>
                </div>`) : (`
                
                `)}
                
            </div>
  
            <div class="col-6 m-0 p-0 reconListItemColRight" id="rowRight-${i}">
                ${portalData ? (`
                <div class="row reconListItemCard reconListItemCardRight" id="rowRightDiv-${i}" draggable="true">
                    <div class="col-md-4 col-sm-6">
                        <span>Invoice Number</span>
                        <p>${portalData.invoiceNo}</p>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <span>GSTIN</span>
                        <p>${portalData.vendorGstin}</p>
                    </div>
                    <div class="col-md-4 col-sm-6 d-none d-sm-block">
                        <span>Vendor name</span>
                        <p>${portalData.vendorName}</p>
                    </div>
                    <div class="col-md-4 col-sm-6 d-none d-sm-block">
                        <span>Invoice Amount</span>
                        <p>${portalData.invoiceAmount}</p>
                    </div>
                    <div class="col-md-4 col-sm-6 d-none d-sm-block">
                        <span>Tax Amount</span>
                        <p>${portalData.invoiceTax}</p>
                    </div>
                    <div class="col-md-4 col-sm-6 d-none d-sm-block">
                        <span>ITC ?</span><br>
                        <p>${portalData.isItcAvl}</p>
                    </div>
                </div>`) : (``)}
            </div>
        </div>`);
    }
  }



  $(document).ready(function() {
    updateTheLocalPortalList();
  });



  console.log("lets start the reconciliation");
</script>