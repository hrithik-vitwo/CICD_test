<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-customers-controller.php");

// console($_SESSION);
$BranchSoObj = new BranchSo();

$company = $BranchSoObj->fetchCompanyDetails()['data'];
$currencyIcon = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data']['currency_icon'];

if (isset($_GET['inv_id'])) {
  $inv_id = base64_decode($_GET['inv_id']);
  $invoiceDetails = $BranchSoObj->fetchBranchSoInvoiceById($inv_id)['data'][0];
  $invoiceItemDetails = $BranchSoObj->fetchBranchSoInvoiceItems($inv_id)['data'];
  $invoiceItemDetailsGroupByHSN = $BranchSoObj->fetchBranchSoInvoiceItemsGroupByHSN($inv_id)['data'];
  $companyData = unserialize($invoiceDetails['companyDetails']);
  $customerData0 = unserialize($invoiceDetails['customerDetails']);
  $company_bank_details = unserialize($invoiceDetails['company_bank_details']);

  // fetch customer details
  $customerDetailsObj = queryGet("SELECT cust.parentGlId, cust.customer_pan, cust.customer_gstin, cust.trade_name AS customer_name, cust.constitution_of_business, cust.customer_opening_balance, cust.customer_currency, cust.customer_website, cust.customer_credit_period, cust.customer_picture, cust.customer_authorised_person_name, cust.customer_authorised_person_email, cust.customer_authorised_alt_email, cust.customer_authorised_person_phone, cust.customer_authorised_alt_phone, cust.customer_authorised_person_designation, cust.customer_profile, cust.customer_status, cadmin.* FROM erp_customer AS cust LEFT JOIN tbl_customer_admin_details AS cadmin ON cust.customer_id = cadmin.customer_id WHERE cust.customer_id='" . $invoiceDetails['customer_id'] . "'");

  $customerDetails = $customerDetailsObj['data'];
  $customerData = array_merge($customerData0, $customerDetails);

  $sqlCustomerAddress = "SELECT * FROM `erp_customer_address` WHERE customer_address_id='" . $invoiceDetails['shipToLastInsertedId'] . "'";
  $customerShipAddress = queryGet($sqlCustomerAddress)['data'];
}

if (isset($_GET['so_id'])) {
  $so_id = base64_decode($_GET['so_id']);
  $soDetails = $BranchSoObj->fetchBranchSoById($so_id)['data'][0];
  $soItemDetails = $BranchSoObj->fetchBranchSoItems($so_id)['data'];

  // fetch company details
  $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
  $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
  $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
  $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
  $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
  $arrMarge = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);

  $companyData = $arrMarge;
  $companyBankAccDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='$bankId' ")['data'];

  // fetch customer details
  $customerDetailsObj = queryGet("SELECT cust.parentGlId, cust.customer_pan, cust.customer_gstin, cust.trade_name AS customer_name, cust.constitution_of_business, cust.customer_opening_balance, cust.customer_currency, cust.customer_website, cust.customer_credit_period, cust.customer_picture, cust.customer_authorised_person_name, cust.customer_authorised_person_email, cust.customer_authorised_alt_email, cust.customer_authorised_person_phone, cust.customer_authorised_alt_phone, cust.customer_authorised_person_designation, cust.customer_profile, cust.customer_status, cadmin.* FROM erp_customer AS cust LEFT JOIN tbl_customer_admin_details AS cadmin ON cust.customer_id = cadmin.customer_id WHERE cust.customer_id='" . $soDetails['customer_id'] . "'");

  $customerData = $customerDetailsObj['data'];

  $sqlCustomerAddress = "SELECT * FROM `erp_customer_address` WHERE customer_address_id='" . $soDetails['shipToLastInsertedId'] . "'";
  $customerShipAddress = queryGet($sqlCustomerAddress)['data'];
}

// fetch quotation details 
if (isset($_GET['quotation_id'])) {
  $quotation_id = base64_decode($_GET['quotation_id']);

  // fetch quotation details 
  $quotationDetails = $BranchSoObj->getQuotations($quotation_id)['data'];
  $quotationItemDetails = $BranchSoObj->getQuotationItems($quotation_id)['data'];

  // fetch company details
  $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
  $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
  $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
  $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
  $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
  $arrMarge = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);

  $companyData = $arrMarge;
  $companyBankAccDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='$bankId' ")['data'];

  // fetch customer details
  $customerDetailsObj = queryGet("SELECT cust.parentGlId, cust.customer_pan, cust.customer_gstin, cust.trade_name AS customer_name, cust.constitution_of_business, cust.customer_opening_balance, cust.customer_currency, cust.customer_website, cust.customer_credit_period, cust.customer_picture, cust.customer_authorised_person_name, cust.customer_authorised_person_email, cust.customer_authorised_alt_email, cust.customer_authorised_person_phone, cust.customer_authorised_alt_phone, cust.customer_authorised_person_designation, cust.customer_profile, cust.customer_status, cadmin.* FROM erp_customer AS cust LEFT JOIN tbl_customer_admin_details AS cadmin ON cust.customer_id = cadmin.customer_id WHERE cust.customer_id='" . $soDetails['customer_id'] . "'");

  $customerData = $customerDetailsObj['data'];

  $sqlCustomerAddress = "SELECT * FROM `erp_customer_address` WHERE customer_address_id='" . $soDetails['shipToLastInsertedId'] . "'";
  $customerShipAddress = queryGet($sqlCustomerAddress)['data'];

  // accept quotation 
  if (isset($_POST['acceptBtn'])) {
    $updQuot = "UPDATE `" . ERP_BRANCH_QUOTATIONS . "` SET `approvalStatus`=16 WHERE `quotation_id`=$quotation_id ";
    $quotationUpdateResp = queryUpdate($updQuot);

    if ($quotationUpdateResp['status'] == "success") {
      swalAlert($quotationUpdateResp["status"], $quotationDetails['quotation_no'], 'Quotation has been accepted successfully. Thank you!', "branch-so-invoice-view.php?quotation_id=" . $_GET['quotation_id']);
    } else {
      swalAlert($quotationUpdateResp["status"], 'warning', $quotationUpdateResp["message"]);
    }
  }

  // reject quotation 
  if (isset($_POST['rejectBtn'])) {
    $updQuot = "UPDATE `" . ERP_BRANCH_QUOTATIONS . "` SET `approvalStatus`=17 WHERE `quotation_id`=$quotation_id ";
    $quotationUpdateResp = queryUpdate($updQuot);

    if ($quotationUpdateResp['status'] == "success") {
      swalAlert($quotationUpdateResp["status"], $quotationDetails['quotation_no'], 'Quotation has been rejected successfully.', "branch-so-invoice-view.php?quotation_id=" . $_GET['quotation_id']);
    } else {
      swalAlert($quotationUpdateResp["status"], 'warning', $quotationUpdateResp["message"]);
    }
  }
}

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
    font-size: 13px;
    text-align: left;
    white-space: nowrap;
  }

  table.tax-hsn-table tr th,
  table.tax-hsn-table tr td {
    padding: 5px 5px !important;
    background: none !important;
    color: #000 !important;
    border: 1px solid #000 !important;
    font-weight: 500 !important;
    font-size: 7px !important;
    text-align: left;
    white-space: nowrap;
  }

  .invoice-template {
    font-size: 15px;
    color: #000;
  }

  .col-child {
    border: 1px solid #000;
    padding: 10px;
  }

  div.invoice-template table tr.border-top-none td {
    border-top: 0 !important;
    border-bottom: 0 !important;
  }

  .text-size-em {
    font-size: 0.8em !important;
  }

  .sign-img img {
    width: 140px;
    height: 40px;
    object-fit: contain;
    margin: 0 auto;
  }

  table.tax-invoice-table tr th,
  table.tax-invoice-table tr td {
    font-weight: 100 !important;
  }

  table.tax-invoice-table tr th.text-bold {
    font-weight: bold !important;
  }

  table.tax-hsn-table tr th.text-bold {
    font-weight: bold !important;
  }

  table.tax-hsn-table tr td.text-bold {
    font-weight: bold !important;
  }

  .row.btns-group {
    justify-content: flex-end;
    gap: 10px;
  }

  .row.btns-group button {
    width: 100%;
  }

  @media print {
    * {
      overflow: visible !important;
    }

    .page {
      page-break-after: always;
      border: 1px solid #fff !important;
    }

    .btns-group {
      display: none;
    }

  }

  @media (max-width: 768px) {
    .invoice-template {
      font-size: 10px;
    }

    .invoice-template p {
      margin-bottom: 0;
      font-size: 5px !important;
    }

    table.tax-invoice-table tr th,
    table.tax-invoice-table tr td {
      padding: 5px;
      font-size: 8px;
    }

    .sign-img img {
      width: 100px;
      height: 40px;
      object-fit: contain;
      margin: 0 auto;
    }


  }

  @media (max-width: 575px) {
    .invoice-template {
      font-size: 6px !important;
    }

    .invoice-template .col-child {
      padding: 5px;
    }

    table.tax-invoice-table tr th,
    table.tax-invoice-table tr td {
      font-size: 5px !important;
      padding: 3px !important;
    }

    .row.btns-group button {
      width: auto;
      margin-left: -10px;
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
      <form action="" method="POST">
        <div class="container-fluid">

          <div class="invoice-template">
            <div class="row">
              <div class="col-lg-12 border-0 mb-3">
                <?php if ($_GET['quotation_id']) { ?>
                  <p class="font-bold text-center">Quotation Details</p>
                <?php } else if (isset($_GET['inv_id'])) { ?>
                  <p class="font-bold text-center">Tax Invoice</p>
                <?php } else if (isset($_GET['so_id'])) { ?>
                  <p class="font-bold text-center">Sales Order</p>
                <?php } ?>
              </div>
            </div>

            <?php
            if ($invoiceDetails["ack_no"] != "") {
            ?>
              <div class="row mb-3" style="align-items: flex-end; justify-content: space-between;">
                <div class="col-lg-8 col-md-8 col-sm-8 col-8 border-0 col-child">
                  <div class="d-flex">
                    <p>IRN :</p>
                    <p class="font-bold"> &nbsp; <?= $invoiceDetails["irn"] ?></p>
                  </div>
                  <div class="d-flex">
                    <p>Ack No. :</p>
                    <p class="font-bold">&nbsp;<?= $invoiceDetails["ack_no"] ?></p>
                  </div>
                  <div class="d-flex">
                    <p>Ack Date :</p>
                    <p class="font-bold">&nbsp;<?= $invoiceDetails["ack_date"] ?></p>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-4 border-0 col-child">
                  <div class="qr-code-img text-center">
                    <p class="text-center">e-Invoice</p>
                    <?php
                    // echo $invoiceDetails["signed_qr_code"];
                    ?>
                    <img src="../../public/assets/img/qr-code.png" alt="" width="170px" height="170px">
                  </div>
                </div>
              </div>
            <?php
            }
            ?>
            <input type="hidden" name="branchGstin" value="<?= $companyData['branch_gstin'] ?>">
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-parent">
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-child border-bottom-0 ">
                    <p class="font-bold"> <?= $companyData['company_name'] ?> </p>
                    <p><?= $companyData['location_building_no'] ?></p>
                    <p>Plat No.<?= $companyData['location_flat_no'] ?>, <?= $companyData['location_street_name'] ?>,</p>
                    <p><?= $companyData['location'] ?>, <?= $companyData['location_city'] ?>, <?= $companyData['location_city'] ?> <?= $companyData['location_pin_code'] ?></p>
                    <p>GSTIN/UIN: <?= $companyData['branch_gstin'] ?></p>
                    <p>Company’s PAN: <?= $companyData['company_pan'] ?></p>
                    <p>State Name : <?= fetchStateNameByGstin($companyData['branch_gstin']) ?>, Code : <?= substr($companyData['branch_gstin'], 0, 2); ?></p>
                    <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-child border-bottom-0">
                    <p>Buyer (Bill to)</p>
                    <p class="font-bold"> <?= $customerData['customer_name'] ?></p>
                    <?php if (isset($_GET['so_id'])) { ?>
                      <p><?= $soDetails['billingAddress'] ?></p>
                    <?php } else { ?>
                      <p><?= $invoiceDetails['customer_billing_address'] ?></p>
                    <?php } ?>
                    <p>GSTIN/UIN : <?= $customerData['customer_gstin'] ?></p>
                    <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-child border-bottom-0">
                    <p>Consignee (Ship to)</p>
                    <p class="font-bold"> <?php if ($customerShipAddress['customer_address_recipient_name'] != "") {
                                            echo $customerShipAddress['customer_address_recipient_name'];
                                          } else {
                                            echo $customerData['fldAdminName'];
                                          } ?></p>
                    <?php if (isset($_GET['so_id'])) { ?>
                      <p><?= $soDetails['shippingAddress'] ?></p>
                    <?php } else { ?>
                      <p><?= $invoiceDetails['customer_shipping_address'] ?></p>
                    <?php } ?>
                    <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                    <p>Place of Supply : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?></p>
                  </div>
                </div>
              </div>

              <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-parent" style="border-right: 1px solid #000;">
                <?php if (isset($_GET['quotation_id'])) { ?>
                  <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0 border-right-0 border-left-0">
                      <p>Quotation No.</p>
                      <p class="font-bold"><?= $quotationDetails['quotation_no'] ?></p>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0">
                      <p>Dated</p>
                      <p class="font-bold">
                        <input type="hidden" name="invoiceDetails[invoiceDate]" value="<?= date('Y-m-d') ?>">
                        <?php $invDate = $quotationDetails['posting_date'];

                        $invDate = date_create($quotationDetails['posting_date']);
                        echo date_format($invDate, 'd-m-Y');
                        ?>
                      </p>
                    </div>
                  </div>
                <?php } else if (isset($_GET['so_id'])) { ?>
                  <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0 border-right-0 border-left-0">
                      <p>Sales Orders No.</p>
                      <p class="font-bold"><?= $soDetails['so_number'] ?></p>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0">
                      <p>Dated</p>
                      <p class="font-bold">
                        <?php $invDate = $soDetails['so_date'];
                        $invDate = date_create($soDetails['so_date']);
                        echo date_format($invDate, 'd-m-Y');
                        ?>
                      </p>
                    </div>
                  </div>
                <?php } else if (isset($_GET['inv_id'])) { ?>
                  <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0 border-right-0 border-left-0">
                      <p>Invoice No.</p>
                      <p class="font-bold"><?= $invoiceDetails['invoice_no'] ?></p>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0">
                      <p>Dated</p>
                      <p class="font-bold">
                        <input type="hidden" name="invoiceDetails[invoiceDate]" value="<?= date('Y-m-d') ?>">
                        <?php $invDate = $invoiceDetails['invoice_date'];

                        $invDate = date_create($invoiceDetails['invoice_date']);
                        echo date_format($invDate, 'd-m-Y');
                        ?>
                      </p>
                    </div>
                  </div>
                  <div class="row">
                    <?php if ($invoiceDetails['credit_period'] != "") { ?>
                      <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-left-0">
                        <p>Terms of Payment</p>
                        <p class="font-bold"><?= $invoiceDetails['credit_period'] ?> days</p>
                      </div>
                    <?php } ?>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-top-0 border-left-0 border-bottom-0 border-right-0">
                      <p>Dispatch Doc No.</p>
                      <?php if ($invoiceDetails['pgi_no'] != "") { ?>
                        <p class="font-bold"><?= $invoiceDetails['pgi_no'] ?></p>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0 border-top-0 border-left-0">
                      <p>Buyer’s Order No.</p>
                      <?php if ($invoiceDetails['po_number'] != "") { ?>
                        <p class="font-bold"><?= $invoiceDetails['po_number'] ?></p>
                      <?php } ?>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0">
                      <p>Dated</p>
                      <?php if ($invoiceDetails['po_date'] != "") { ?>
                        <p class="font-bold">
                          <?php
                          $date = date_create($invoiceDetails['po_date']);
                          echo date_format($date, 'd-m-Y');
                          ?>
                        </p>
                      <?php } ?>
                    </div>
                  </div>
                <?php } ?>
                <hr>
                <strong>Customer Details</strong>
                <p>
                  <label>Email:</label>
                  <small>
                    <?= $customerData['fldAdminEmail'] ?>
                  </small>
                </p>
                <p>
                  <label>Phone:</label>
                  <small>
                    <?= $customerData['fldAdminPhone'] ?>
                  </small>
                </p>
              </div>
            </div>
            <?php
            $branchGstin = substr($companyData['branch_gstin'], 0, 2);
            $customerGstin = substr($customerData['customer_gstin'], 0, 2);
            $conditionGST = $branchGstin == $customerGstin;
            ?>
            <div class="row">
              <table class="tax-invoice-table" style="overflow: auto;">
                <tbody>
                  <tr>
                    <th class="text-bold" rowspan="2">Sl No.</th>
                    <th class="text-bold" rowspan="2">Particulars</th>
                    <th class="text-bold" rowspan="2">HSN/SAC</th>
                    <th class="text-bold" rowspan="2">Quantity</th>
                    <th class="text-bold" rowspan="2">Rate</th>
                    <th class="text-bold" rowspan="2">UOM</th>
                    <!-- <th rowspan="2">Sub Total</th> -->
                    <th class="text-bold" rowspan="2">Discount</th>
                    <?php
                    if ($conditionGST || $customerGstin == "") {
                    ?>
                      <th class="text-center text-bold" colspan="2">CGST</th>
                      <th class="text-center text-bold" colspan="2">SGST</th>
                    <?php } else { ?>
                      <th class="text-center text-bold" colspan="2">IGST</th>
                    <?php } ?>
                    <th class="text-bold" rowspan="2">Total Amount</th>
                  </tr>
                  <tr>
                    <?php if ($conditionGST || $customerGstin == "") { ?>
                      <th class="text-bold">Rate</th>
                      <th class="text-bold">Amount</th>
                      <th class="text-bold">Rate</th>
                      <th class="text-bold">Amount</th>
                    <?php } else { ?>
                      <th class="text-bold">Rate</th>
                      <th class="text-bold">Amount</th>
                    <?php } ?>
                  </tr>
                  <?php
                  $i = 1;
                  $totalTaxAmt = 0;
                  $totalDiscountAmt = 0;
                  $allSubTotalAmt = 0;
                  $itemDetails = "";

                  $foreachData = [];
                  if (isset($_GET['quotation_id'])) {
                    $foreachData = $quotationItemDetails;
                  } else if (isset($_GET['so_id'])) {
                    $foreachData = $soItemDetails;
                  } else if (isset($_GET['inv_id'])) {
                    $foreachData = $invoiceItemDetails;
                  }

                  foreach ($foreachData as $key => $item) {
                    $totalTaxAmt += $item['totalTax'];
                    $allSubTotalAmt += $item['unitPrice'] * $item['qty'];
                    $totalDiscountAmt += $item['totalDiscountAmt'];
                    $totalAmt += $item['totalPrice'];
                    $subTotalAmt = $item['unitPrice'] * $item['qty'];
                  ?>
                    <tr class="border-top-none">
                      <td class="border-bottom-0"><?= $i++ ?></td>
                      <td class="border-bottom-0">
                        <p class="font-bold">
                          <?= $item['itemName'] ?>
                        </p>
                        <p class="font-italic"><?= $item['itemCode'] ?></p>
                        <p class="font-italic text-xs"><?= $item['itemRemarks'] ?></p>
                      </td>
                      <td class="border-bottom-0">
                        <p>
                          <?= $item['hsnCode'] ?>
                        </p>
                      </td>
                      <td class="border-bottom-0">
                        <p>
                          <?= $item['qty'] ?>
                        </p>
                      </td>
                      <td class="border-bottom-0 text-right"><?= number_format($item['unitPrice'], 2) ?></td>
                      <td class="border-bottom-0"><?= $item['uom'] ?></td>
                      <!-- <td class="border-bottom-0"><?= $subTotalAmt ?></td> -->
                      <td class="border-bottom-0 text-right">
                        <p><?= number_format($item['totalDiscountAmt'], 2) ?></p>
                        <p class="text-xs font-italic font-bold">(%<?= $item['totalDiscount'] ?>)</p>
                      </td>
                      <?php
                      if ($conditionGST || $customerGstin == "") {
                        $itemGstAmt = $item['totalTax'] / 2;
                        $itemGstPer = $item['tax'] / 2;
                      ?>
                        <td class="border-bottom-0 text-right">
                          <p class="text-xs font-italic font-bold">%<?= $itemGstPer ?></p>
                        </td>
                        <td class="border-bottom-0 text-right">
                          <p class="text-xs font-italic font-bold"><span class="rupee-symbol"></span><?= $itemGstAmt ?></p>
                        </td>
                        <td class="border-bottom-0 text-right">
                          <p class="text-xs font-italic font-bold">%<?= $itemGstPer ?></p>
                        </td>
                        <td class="border-bottom-0 text-right">
                          <p class="text-xs font-italic font-bold"><span class="rupee-symbol"></span><?= $itemGstAmt ?></p>
                        </td>
                      <?php } else { ?>
                        <td class="border-bottom-0">
                          <p class="text-xs font-italic font-bold">%<?= $item['tax'] ?></p>
                        </td>
                        <td class="border-bottom-0">
                          <p class="text-xs font-italic font-bold"><span class="rupee-symbol"></span><?= $item['totalTax'] ?></p>
                        </td>
                      <?php } ?>
                      <td class="border-bottom-0 text-right">
                        <p><?= number_format($item['totalPrice'], 2) ?></p>
                      </td>
                    </tr>
                  <?php } ?>
                  <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <!-- <td class="border-top-0"><?= number_format($allSubTotalAmt, 2) ?></td> -->
                    <td></td>
                    <?php if ($conditionGST || $customerGstin == "") { ?>
                      <td></td>
                      <td></td>
                      <td></td>
                    <?php } else { ?>
                      <td></td>
                    <?php } ?>
                    <td></td>
                    <td>
                      <p class="font-bold text-right" style="display: flex;justify-content: space-between;">
                        <strong>Grand Total </strong>
                        <span class="rupee-symbol"></span><?= number_format($totalAmt, 2) ?>
                      </p>
                    </td>
                  </tr>

                  <!-- <tr class="border-top-none">
                    <td></td>
                    <td class="border-top-0">
                      <div class="text-right">SUB TOTAL</div>
                      <div class="text-right">DISCOUNT</div>
                      <?php if ($conditionGST || $customerGstin == "") { ?>
                        <div class="text-right">CGST</div>
                        <div class="text-right">SGST</div>
                      <?php } else { ?>
                        <div class="text-right">IGST</div>
                      <?php } ?>
                    </td>
                    <td class="border-top-0">
                    </td>
                    <td class="border-top-0">

                    </td>
                    <td class="border-top-0"></td>
                    <td class="border-top-0"></td>
                    <td class="border-top-0"></td>
                    <td class="border-top-0"></td>
                    <?php if ($conditionGST || $customerGstin == "") { ?>
                      <td></td>
                      <td></td>
                      <td></td>
                    <?php } else { ?>
                      <td></td>
                    <?php } ?>
                    <td class="border-top-0">
                      <p class="text-right">
                        <input type="hidden" name="invoiceDetails[totalTaxAmt]" value="<?= $totalTaxAmt ?>">
                        <input type="hidden" name="invoiceDetails[subTotal]" value="<?= $allSubTotalAmt ?>">
                        <?= number_format($allSubTotalAmt, 2) ?>
                      </p>
                      <p class="text-right">
                        <input type="hidden" name="invoiceDetails[totalDiscount]" value="<?= $totalDiscountAmt ?>">
                        <?= number_format($totalDiscountAmt, 2) ?>
                      </p>
                      <?php
                      if ($conditionGST || $customerGstin == "") {
                        $gstAmt = $totalTaxAmt / 2;
                      ?>
                        <div class="text-right">
                          <input type="hidden" name="invoiceDetails[cgst]" value="<?= $gstAmt ?>">
                          <?= number_format($gstAmt, 2) ?>
                        </div>
                        <div class="text-right">
                          <input type="hidden" name="invoiceDetails[sgst]" value="<?= $gstAmt ?>">
                          <?= number_format($gstAmt, 2) ?>
                        </div>
                      <?php } else { ?>
                        <div class="text-right">
                          <input type="hidden" name="invoiceDetails[igst]" value="<?= $totalTaxAmt ?>">
                          <?= number_format($totalTaxAmt, 2) ?>
                        </div>
                      <?php } ?>

                    </td>
                  </tr> -->
                </tbody>
                <tbody>
                  <!-- <tr>
                    <td></td>
                    <td>
                      <p class="text-right font-bold ">Total</p>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <?php if ($conditionGST || $customerGstin == "") { ?>
                      <td></td>
                      <td></td>
                      <td></td>
                    <?php } else { ?>
                      <td></td>
                    <?php } ?>
                    <td></td>
                    <td>
                      <p class="font-bold text-right">
                        <input type="hidden" name="invoiceDetails[allTotalAmt]" value="<?= $totalAmt ?>">
                        <span class="rupee-symbol"><?= $currencyIcon ?></span><?= number_format($totalAmt, 2) ?>
                      </p>
                    </td>
                  </tr> -->
                </tbody>
              </table>

              <?php if (!isset($_GET['so_id'])) { ?>
                <?php if (isset($_GET['inv_id'])) { ?>
                  <table class="tax-hsn-table mt-3">
                    <thead>
                      <tr>
                        <th style="font-size: 0.8em !important;" style="font-size: 0.8em !important;" class="text-bold" rowspan="2">HSN / SAC</th>
                        <th style="font-size: 0.8em !important;" class="text-size-em text-bold" rowspan="2">Taxable Value</th>
                        <?php if ($conditionGST || $customerGstin == "") { ?>
                          <th style="font-size: 0.8em !important;" colspan="2" class="text-size-em text-bold text-center">Central Tax</th>
                          <th style="font-size: 0.8em !important;" colspan="2" class="text-size-em text-bold text-center">State Tax</th>
                        <?php } else { ?>
                          <th style="font-size: 0.8em !important;" colspan="2" class="text-size-em text-bold text-center">IGST</th>
                        <?php } ?>
                        <th style="font-size: 0.8em !important;" class="text-size-em text-bold" rowspan="2">Total Tax Amount</th>
                      </tr>
                      <tr>
                        <?php if ($conditionGST || $customerGstin == "") { ?>
                          <th style="font-size: 0.8em !important;" class="text-size-em text-bold">Rate</th>
                          <th style="font-size: 0.8em !important;" class="text-size-em text-bold">Amount</th>
                          <th style="font-size: 0.8em !important;" class="text-size-em text-bold">Rate</th>
                          <th style="font-size: 0.8em !important;" class="text-size-em text-bold">Amount</th>
                        <?php } else { ?>
                          <th style="font-size: 0.8em !important;" class="text-size-em text-bold">Rate</th>
                          <th style="font-size: 0.8em !important;" class="text-size-em text-bold">Amount</th>
                        <?php } ?>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $totalTaxableValue = 0;
                      $totalCgstSgstAmt = 0;
                      $allTotalTaxAmt = 0;
                      foreach ($invoiceItemDetailsGroupByHSN as $key => $item) {
                        $itemGstPerHSN = $item['tax'] / 2;
                        $itemGstAmtHSN = $item['totalTax'] / 2;
                        $totalTaxableValue += $item['basePrice'];
                        $totalCgstSgstAmt += $itemGstAmtHSN;
                        $allTotalTaxAmt += $item['totalTax'];
                      ?>
                        <tr>
                          <td style="font-size: 0.8em !important;"><?= $item['hsnCode'] ?></td>
                          <td style="font-size: 0.8em !important;"><?= $item['basePrice'] ?></td>
                          <?php if ($conditionGST || $customerGstin == "") { ?>
                            <td style="font-size: 0.8em !important;" class="text-right"><?= $itemGstPerHSN ?>%</td>
                            <td style="font-size: 0.8em !important;" class="text-right"><?= $itemGstAmtHSN ?></td>
                            <td style="font-size: 0.8em !important;" class="text-right"><?= $itemGstPerHSN ?>%</td>
                            <td style="font-size: 0.8em !important;" class="text-right"><?= $itemGstAmtHSN ?></td>
                          <?php } else { ?>
                            <td style="font-size: 0.8em !important;" class="text-right"><?= $item['tax'] ?>%</td>
                            <td style="font-size: 0.8em !important;" class="text-right"><?= $item['totalTax'] ?></td>
                          <?php } ?>
                          <td style="font-size: 0.8em !important;" class="text-right"><?= $item['totalTax'] ?></td>
                        </tr>
                      <?php } ?>
                      <tr>
                        <td style="font-size: 0.8em !important;" class="text-bold">Total</td>
                        <td style="font-size: 0.8em !important;"><?= number_format($totalTaxableValue, 2) ?></td>
                        <?php if ($conditionGST || $customerGstin == "") { ?>
                          <td style="font-size: 0.8em !important;" class="text-right"></td>
                          <td style="font-size: 0.8em !important;" class="text-right"><?= $totalCgstSgstAmt ?></td>
                          <td style="font-size: 0.8em !important;" class="text-right"></td>
                          <td style="font-size: 0.8em !important;" class="text-right"><?= $totalCgstSgstAmt ?></td>
                        <?php } else { ?>
                          <td style="font-size: 0.8em !important;" class="text-right"></td>
                          <td style="font-size: 0.8em !important;" class="text-right"><?= $allTotalTaxAmt ?></td>
                        <?php } ?>
                        <td style="font-size: 0.8em !important;" class="text-right"><?= $allTotalTaxAmt ?></td>
                      </tr>
                    </tbody>
                  </table>
                <?php } ?>
              <?php } ?>
            </div>
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-top-0 border-right-0 border-bottom-0">
                <p>
                  Amount Chargeable (in words)
                </p>
                <p class="font-bold"><?= number_to_words_indian_rupees($totalAmt); ?> ONLY</p>
              </div>
              <?php if (!isset($quotation_id)) { ?>
                <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-top-0 border-left-0 border-bottom-0">
                  <p class="font-italic text-right">E. & O.E</p>
                  <p>Company’s Bank Details</p>
                  <div class="d-flex">
                    <p>Bank Name :</p>
                    <p class="font-bold"><?= $company_bank_details['bank_name'] ?></p>
                  </div>
                  <div class="d-flex">
                    <p>A/c No. :</p>
                    <p class="font-bold"><?= $company_bank_details['account_no'] ?></p>
                  </div>
                  <div class="d-flex">
                    <p>Branch & IFS Code :</p>
                    <p class="font-bold"><?= $company_bank_details['ifsc_code'] ?></p>
                  </div>
                </div>
              <?php } ?>
            </div>
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-top-0 border-right-0">
                <p>Remarks:</p>
                <p>
                  <?= $companyData['company_footer'] ?>
                </p>
                <?php if (isset($_GET['quotation_id'])) { ?>
                  <p>Created By: <strong><?= getCreatedByUser($quotationDetails['created_by']); ?></strong></p>
                <?php } else if (isset($_GET['inv_id']) || isset($_GET['so_id'])) { ?>
                  <p>Created By: <strong><?= getCreatedByUser($invoiceDetails['created_by']); ?></strong></p>
                <?php } ?>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child ">
                <p class="text-center font-bold">
                  for <?= $companyData['company_name'] ?>
                </p>
                <p class="text-center sign-img">
                  <img src="../../public/storage/signature/<?= $companyData['signature'] ?>" alt="signature">
                </p>
              </div>
            </div>
          </div>
          <!-- <button onclick="window.print()">Print this page</button> -->
        </div>
        <?php if (isset($_GET['quotation_id'])) { ?>
          <div class="row btns-group">
            <?php if ($quotationDetails['approvalStatus'] == 16) { ?>
              <div class="col-2 text-left">
                <button type="button" onclick="return alert('You are already submitted.')" class="btn btn-success mt-4 acceptBtn">Accepted <i class="fa fa-check-circle"></i></button>
              </div>
              <div class="col-2 text-left">
                <button type="button" onclick="return alert('You are already submitted.')" class="btn btn-secondary mt-4 cancelBtn">Reject</button>
              </div>
            <?php } else if ($quotationDetails['approvalStatus'] == 17) { ?>
              <div class="col-2 text-left">
                <button type="button" onclick="return alert('You are already submitted.')" class="btn btn-secondary mt-4 acceptBtn">Accept</button>
              </div>
              <div class="col-2 text-left">
                <button type="button" onclick="return alert('You are already submitted.')" class="btn btn-danger mt-4 cancelBtn">Rejected <i class="fa fa-times-circle"></i></button>
              </div>
            <?php } else { ?>
              <div class="col-2">
                <button type="submit" name="acceptBtn" class="btn btn-success mt-4 acceptBtn" onclick="return confirm('Are you sure you want to accept Quotation <?= $quotationDetails['quotation_no'] ?> ?')">Accept</button>
              </div>
              <div class="col-2 text-left">
                <button type="submit" name="rejectBtn" class="btn btn-danger mt-4 cancelBtn" onclick="return confirm('Are you sure you want to reject Quotation <?= $quotationDetails['quotation_no'] ?> ?')">Reject</button>
              </div>
            <?php } ?>

          </div>
        <?php } ?>
      </form>
  </div>
  </div>
  </section>
  </div>
  <!-- End Pegination from------->
<?php } ?>

<?php
require_once("../common/footer.php");
?>

<!-- <script>
  $(document).ready(function() {
    window.print();
    setTimeout("closePrintView()", 1000);
  });

  function closePrintView() {
    document.location.href = 'manage-invoices.php';
  }
</script> -->