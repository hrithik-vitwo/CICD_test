<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-customers-controller.php");


// console($_SESSION);

if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"],);
}

if (isset($_POST["visit"])) {
  $newStatusObj = VisitBranches($_POST);
  redirect(BRANCH_URL);
}

if (isset($_POST["createdata"])) {
  $addNewObj = createDataBranches($_POST);
  if ($addNewObj["status"] == "success") {
    $branchId = base64_encode($addNewObj['branchId']);
    redirect($_SERVER['PHP_SELF'] . "?branchLocation=" . $branchId);
    swalToast($addNewObj["status"], $addNewObj["message"]);
    // console($addNewObj);
  } else {
    swalToast($addNewObj["status"], $addNewObj["message"]);
  }
}

if (isset($_POST["editdata"])) {
  $editDataObj = updateDataBranches($_POST);

  swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$BranchSoObj = new BranchSo();
$customerDetails = new CustomersController();

if (isset($_POST['addNewPgiFormSubmitBtn'])) {
  // console($_POST);
  $addBranchSoDeliveryPgi = $BranchSoObj->insertBranchPgi($_POST);
  // console($addBranchSoDeliveryPgi);
  if ($addBranchSoDeliveryPgi['success'] == "true") {
    $addBranchSoDeliveryPgiItems = $BranchSoObj->insertBranchPgiItems($_POST, $addBranchSoDeliveryPgi['lastID']);
    if ($addBranchSoDeliveryPgiItems['success'] == "true") {
      swalToast($addBranchSoDeliveryPgiItems["success"], $addBranchSoDeliveryPgiItems["message"]);
    } else {
      swalToast($addBranchSoDeliveryPgiItems["success"], $addBranchSoDeliveryPgiItems["message"]);
    }
  } else {
    // console($addBranchSoDeliveryPgi);
    swalToast($addBranchSoDeliveryPgi["success"], $addBranchSoDeliveryPgi["message"]);
  }
}

// console($singleSoDetails);
?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
  .dropdown-content {
    display: none;
  }

  table.tax-invoice-table tr th,
  table.tax-invoice-table tr td {
    padding: 10px 15px;
    background: none !important;
    color: #000 !important;
    border: 1px solid #000 !important;
    font-weight: 600 !important;
    font-size: 15px;
    text-align: left;
    white-space: nowrap;
  }

  .invoice-template {
    font-size: 15px;
  }

  .col-child {
    border: 1px solid #000;
    padding: 10px;
  }

  div.invoice-template table tr.border-top-none td {
    border-top: 0 !important;
    border-bottom: 0 !important;
  }

  @media print {
    * {
      overflow: visible !important;
    }

    .page {
      page-break-after: always;
      border: 1px solid #fff !important;
    }
  }

  /* 
  table.invoices-table tr td {
    border: 1px solid #000;
    border-top: 1px solid #000 !important;
    border-collapse: collapse;
    background: none;
  }

  table.invoices-table tbody:nth-child(2) tr td {
    height: 250px;
    vertical-align: baseline;
  } */
</style>

<?php
if (isset($_GET['create-pgi'])) {
?>
  <h1>Hello</h1>
<?php } else { ?>


  <div class="content-wrapper">
    <section class="content">
      <div class="container-fluid">

        <div class="invoice-template">
          <div class="row">
            <div class="col-lg-12 border-0 mb-3">
              <p class="font-bold text-center">Tax Invoice</p>
            </div>
          </div>
          <div class="row mb-3" style="align-items: flex-end; justify-content: space-between;">
            <div class="col-lg-8 col-md-8 col-sm-8 col-8 border-0 col-child">
              <div class="d-flex">
                <p>IRN :</p>
                <p class="font-bold"> &nbsp; de49ce41e485accfa26b5e9deb2bf848318c538e00d0af-
                  f2e928a736605bff77</p>
              </div>
              <div class="d-flex">
                <p>Ack No. :</p>
                <p class="font-bold">&nbsp;122214880803052</p>
              </div>
              <div class="d-flex">
                <p>Ack Date :</p>
                <p class="font-bold">&nbsp;24-Nov-22</p>
              </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-4 border-0 col-child">
              <div class="qr-code-img text-center">
                <p class="text-center">e-Invoice</p>
                <img src="../../public/assets/img/qr-code.png" alt="" width="170px" height="170px">
              </div>
            </div>

          </div>
          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-parent">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-child border-right-0 border-bottom-0">
                  <p class="font-bold"> Babaji Shivram 4PL Solutions Private Limited</p>
                  <p>Formerly Known As Cross Trade Ecom Pvt Ltd</p>
                  <p>Plot No.2, CTS No. 5/7, 6 Saki Vihar Road,</p>
                  <p>Behind Excom House, Sakinaka,</p>
                  <p>Andheri East, Mumbai, Maharashtra 400072</p>
                  <p>GSTIN/UIN: 27AAGCC4935R1ZZ</p>
                  <p>State Name : Maharashtra, Code : 27</p>
                  <p>E-Mail : accounts@mypacco.com</p>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-child border-right-0 border-bottom-0">
                  <p>Buyer (Bill to)</p>
                  <p class="font-bold"> FLP Trading Private Limited</p>
                  <p>Ground Floor, Sharda Plaza,Punjabipura, Delhi Road</p>
                  <p>MEERUT,250002</p>
                  <p>UTTAR PRADESH,India</p>
                  <p>GSTIN/UIN : 09AADCF3408R1Z7</p>
                  <p>State Name : Uttar Pradesh, Code : 09</p>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-child border-right-0 border-bottom-0">
                  <p>Consignee (Ship to)</p>
                  <p class="font-bold"> FLP Trading Private Limited</p>
                  <p>Ground Floor, Sharda Plaza,Punjabipura, Delhi Road</p>
                  <p>MEERUT,250002</p>
                  <p>UTTAR PRADESH,India</p>
                  <p>GSTIN/UIN : 09AADCF3408R1Z7</p>
                  <p>State Name : Uttar Pradesh, Code : 09</p>
                  <p>Place of Supply : Uttar Pradesh</p>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-parent">
              <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0 border-bottom-0">
                  <p>Invoice No.</p>
                  <p class="font-bold">MP/NOV22/000529</p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0">
                  <p>Dated</p>
                  <p class="font-bold">30-Nov-22</p>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0 border-bottom-0">
                  <p>Delivery Note</p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0">
                  <p>
                    Mode/Terms of Payment
                  </p>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0 border-bottom-0">
                  <p>Reference No. & Date.</p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0">
                  <p>
                    Other References
                  </p>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0 border-bottom-0">
                  <p>Buyer’s Order No.</p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0">
                  <p>
                    Dated
                  </p>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0 border-bottom-0">
                  <p>Dispatch Doc No.</p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0">
                  <p>
                    Delivery Note Date
                  </p>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0 border-bottom-0">
                  <p>Dispatched through</p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0">
                  <p>
                    Destination
                  </p>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0 border-bottom-0">
                  <p>Vessel/Flight No.</p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0">
                  <p>
                    Place of receipt by shipper:
                  </p>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0 border-bottom-0">
                  <p>City/Port of Loading</p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0">
                  <p>
                    City/Port of Discharge
                  </p>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 col-child border-bottom-0" style="min-height: 210px; height: 100%;">
                  <p>Terms of Delivery</p>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <table class="tax-invoice-table" style="overflow: auto;">
              <tbody>
                <tr>
                  <th>Sl No.</th>
                  <th>Particulars</th>
                  <th>HSN/SAC</th>
                  <th>GST Rate</th>
                  <th>Quantity</th>
                  <th>Rate</th>
                  <th>per (Uom)</th>
                  <th>Amount</th>
                </tr>
                <tr class="border-top-none">
                  <td class="border-bottom-0">1</td>
                  <td class="border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-bottom-0"></td>
                  <td class="border-bottom-0"></td>
                  <td class="border-bottom-0"></td>
                  <td class="border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">2</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">3</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td class="border-top-0 border-bottom-0">4</td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold">
                      Freight Income - B2C
                    </p>
                    <p class="font-italic">As Per Annexure</p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      996812
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0">
                    <p>
                      18 %
                    </p>
                  </td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0"></td>
                  <td class="border-top-0 border-bottom-0">
                    <p class="font-bold text-right">3,35,320.34</p>
                  </td>
                </tr>
                <tr class="border-top-none">
                  <td></td>
                  <td class="border-top-0">
                    <p class="text-right font-bold">IGST</p>
                    <p class="text-right font-bold">CGST</p>
                    <p class="text-right font-bold">SGST</p>
                  </td>
                  <td class="border-top-0">
                  </td>
                  <td class="border-top-0">

                  </td>
                  <td class="border-top-0"></td>
                  <td class="border-top-0"></td>
                  <td class="border-top-0"></td>
                  <td class="border-top-0">
                    <p class="font-bold text-right">60,357.66</p>
                    <p class="font-bold text-right">60,357.66</p>
                    <p class="font-bold text-right">60,357.66</p>
                  </td>
                </tr>
              </tbody>
              <tbody>
                <tr>
                  <td></td>
                  <td>
                    <p class="text-right">Total</p>
                  </td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>
                    <p class="font-bold text-right">3,95,678.00</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-top-0 border-right-0 border-bottom-0">
              <p>
                Amount Chargeable (in words)
              </p>
              <p class="font-bold">INR Three Lakh Ninety Five Thousand Six
                Hundred Seventy Eight Only
              </p>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-top-0 border-left-0 border-bottom-0">
              <p class="font-italic text-right">E. & O.E</p>
              <p>Company’s Bank Details</p>
              <div class="d-flex">
                <p>Bank Name :</p>
                <p class="font-bold">Yes Bank A/c No. 007863300001231</p>
              </div>
              <div class="d-flex">
                <p>A/c No. :</p>
                <p class="font-bold">007863300001231</p>
              </div>
              <div class="d-flex">
                <p>Branch & IFS Code :</p>
                <p class="font-bold">Andheri East & YESB0000078</p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-top-0 border-right-0">
              <p>Remarks:</p>
              <p>Being invoice raised to FLP against freight charges for
                the month of Nov’22.
              </p>
              <div class="d-flex">
                <p>Company’s PAN : </p>
                <p class="font-bold">AAGCC4935R</p>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child ">
              <p class="text-center font-bold">
                for Babaji Shivram 4PL Solutions Private Limited
              </p>
            </div>
          </div>
        </div>
        <!-- <button onclick="window.print()">Print this page</button> -->
      </div>
  </div>
  </div>
  </section>
  </div>
  <!-- End Pegination from------->
<?php } ?>

<?php
require_once("../common/footer.php");
?>
<script>
  function rm() {
    $(event.target).closest("tr").remove();
  }

  function addMultiQty(id) {
    let addressRandNo = Math.ceil(Math.random() * 100000);
    $(`.modal-add-row_${id}`).append(`<tr><td><span class='has-float-label'><input type='date' name='listItem[${id}][deliverySchedule][${addressRandNo}][multiDeliveryDate]' class='form-control' placeholder='delivery date'><label>Delivery date</label></span></td><td><span class='has-float-label'><input type='text' name='listItem[${id}][deliverySchedule][${addressRandNo}][quantity]' class='form-control' placeholder='quantity'><label>quantity</label></span></td><td><a class='btn btn-danger' onclick='rm()'><i class='fa fa-minus'></i></a></td></tr>`);
  }
</script>
<script>
  $(document).ready(function() {
    $('#itemsDropDown')
      .select2()
      .on('select2:open', () => {
        // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
      });
    $('#customerDropDown')
      .select2()
      .on('select2:open', () => {
        // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
      });
    // customers ********************************
    function loadCustomers() {
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-customers.php`,
        beforeSend: function() {
          $("#customerDropDown").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          $("#customerDropDown").html(response);
        }
      });
    }
    loadCustomers();
    // get customer details by id
    $("#customerDropDown").on("change", function() {
      let itemId = $(this).val();

      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-customers-list.php`,
        data: {
          act: "listItem",
          itemId
        },
        beforeSend: function() {
          $("#customerInfo").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          // console.log(response);
          $("#customerInfo").html(response);
        }
      });
    });
    // **************************************
    function loadItems() {
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-items.php`,
        beforeSend: function() {
          $("#itemsDropDown").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          $("#itemsDropDown").html(response);
        }
      });
    }
    loadItems();

    // get item details by id
    $("#itemsDropDown").on("change", function() {
      let itemId = $(this).val();

      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-items-list.php`,
        data: {
          act: "listItem",
          itemId
        },
        beforeSend: function() {
          //  $("#itemsTable").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          console.log(response);
          $("#itemsTable").append(response);
        }
      });
    });
    $(document).on("click", ".delItemBtn", function() {
      // let id = ($(this).attr("id")).split("_")[1];
      // $(`#delItemRowBtn_${id}`).remove();
      $(this).parent().parent().remove();
    })

    $(document).on('submit', '#addNewItemForm', function(event) {
      event.preventDefault();
      let formData = $("#addNewItemsForm").serialize();
      $.ajax({
        type: "POST",
        url: `ajaxs/so/ajax-items.php`,
        data: formData,
        beforeSend: function() {
          $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
          $("#addNewItemsFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');
        },
        success: function(response) {
          $("#goodTypeDropDown").html(response);
          $('#addNewItemsForm').trigger("reset");
          $("#addNewItemsFormModal").modal('toggle');
          $("#addNewItemsFormSubmitBtn").html("Submit");
          $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
        }
      });
    });

    $(document).on("keyup change", ".qty", function() {
      let id = $(this).val();
      var sls = $(this).attr("sls");
      alert(sls);
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-items-list.php`,
        data: {
          act: "totalPrice",
          itemId: "ss",
          id
        },
        beforeSend: function() {
          $(".totalPrice").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          console.log(response);
          $(".totalPrice").html(response);
        }
      });
    })

    $(".deliveryScheduleQty").on("change", function() {
      let qtyVal3 = ($(this).attr("id")).split("_")[1];
      let qtyVal = $(this).find(":selected").data("quantity");
      // let qtyVal2 = $(this).find(":selected").data("deliverydate");
      // let qtyVal = $(this).find(":selected").children("span");
      // $( "#myselect option:selected" ).text();
      console.log(qtyVal);
      $(`#itemQty_${qtyVal3}`).val(qtyVal);
    })

  })
</script>
<script>
  $('.hamburger').click(function() {
    $('.hamburger').toggleClass('show');
    $('#overlay').toggleClass('show');
    $('.nav-action').toggleClass('show');
  });
</script>