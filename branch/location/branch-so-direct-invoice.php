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

$so_id = base64_decode($_GET['so_id']);

// fetch company currency
$company = $BranchSoObj->fetchCompanyDetails()['data'];
$currencyIcon = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data']['currency_icon'];

// fetch so details
$soDetails = $BranchSoObj->fetchSoDetailsBySoId($so_id)['data'][0];

// fetch so item details
$soItemDetails = $BranchSoObj->fetchBranchSoItems($so_id)['data'];

// company details
$companyDetails = $BranchSoObj->fetchCompanyDetailsById($company_id)['data'];
$bankDetails = $BranchSoObj->fetchCompanyBankDetails()['data'][0];
$branchAdminDetails = $BranchSoObj->fetchBranchAdminDetailsById($branch_id)['data'];
$branchDetails = $BranchSoObj->fetchBranchDetailsById($branch_id)['data'];
$locationDetails = $BranchSoObj->fetchBranchLocalionDetailsById($location_id)['data'];

// customer details
$customerDetails = $BranchSoObj->fetchCustomerDetails($soDetails['customer_id'])['data'][0];

// console("sod$soite details*******************");
// console($soDetails);

// ##############################################################################
// ##############################################################################
if (isset($_POST['addNewinvoiveSaveFormSubmitBtn'])) {
  $addBranchSoInvoice = $BranchSoObj->insertBranchInvoiceFromSo($_POST, $body);
  // console($addBranchSoInvoice);
  if ($addBranchSoInvoice['status'] == "success") {
    swalAlert($addBranchSoInvoice["status"], $addBranchSoInvoice['invoiceNo'], $addBranchSoInvoice["message"], "manage-invoices.php");
    // swalAlert($addBranchSoInvoice["status"], $addBranchSoInvoice['invoiceNo'], $addBranchSoInvoice["message"]);
  } else {
    swalAlert($addBranchSoInvoice["status"], 'Warning', $addBranchSoInvoice["message"]);
  }
}

$fetchSql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE company_id='$company_id' AND branch_id='$branch_id' AND location_id='$location_id' ORDER BY so_invoice_id DESC LIMIT 0,1";
$fetchRow = [];
if ($res = $dbCon->query($fetchSql)) {
  $fetchRow = $res->fetch_assoc();
}

$lastIVcode = $fetchRow['invoice_no'] ?? "";
// $lastIVcode = 'INV99999999';
$invoiceGenCode = getSoInvoiceSerialNumber($lastIVcode);

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

  .content-wrapper {
    background: #fff;
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

  @media print {
    * {
      overflow: visible !important;
    }

    .page {
      page-break-after: always;
      border: 1px solid #fff !important;
    }
  }


  @media (max-width: 768px) {
    .invoice-template {
      font-size: 10px;
    }

    .invoice-template p {
      margin-bottom: 0;
    }

    table.tax-invoice-table tr th,
    table.tax-invoice-table tr td {
      font-size: 10px;
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
      font-size: 6px !important;
      padding: 3px !important;
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
                <p class="font-bold text-center">Tax Invoice</p>
              </div>
            </div>
            <div class="row mb-3" style="display:none;align-items: flex-end; justify-content: space-between;">
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
            <input type="hidden" name="branchGstin" value="<?= $branchDetails['branch_gstin'] ?>">
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-parent">
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-child border-bottom-0">
                    <p class="font-bold"> <?= $branchDetails['branch_name'] ?> </p>
                    <p><?= $locationDetails['othersLocation_building_no'] ?></p>
                    <p>Plot No.<?= $locationDetails['othersLocation_flat_no'] ?>, <?= $locationDetails['othersLocation_street_name'] ?>,</p>
                    <p><?= $locationDetails['othersLocation_location'] ?>, <?= $locationDetails['othersLocation_city'] ?>, <?= $locationDetails['othersLocation_city'] ?> <?= $locationDetails['othersLocation_pin_code'] ?></p>
                    <p>GSTIN/UIN: <?= $branchDetails['branch_gstin'] ?></p>
                    <p>State Name : <?= fetchStateNameByGstin($branchDetails['branch_gstin']) ?>, Code : <?= substr($branchDetails['branch_gstin'], 0, 2); ?></p>
                    <p>E-Mail : <?= $branchAdminDetails['fldAdminEmail'] ?></p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-child border-bottom-0">
                    <p>Buyer (Bill to)</p>
                    <p class="font-bold"> <?= $customerDetails['trade_name'] ?></p>
                    <p><?= $soDetails['billingAddress'] ?></p>
                    <p>GSTIN/UIN : <?= $customerDetails['customer_gstin'] ?></p>
                    <p>State Name : <?= fetchStateNameByGstin($customerDetails['customer_gstin']) ?>, Code : <?= substr($customerDetails['customer_gstin'], 0, 2); ?></p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-child border-bottom-0">
                    <p>Consignee (Ship to)</p>
                    <p class="font-bold"> <?= $customerDetails['trade_name'] ?></p>
                    <p><?= $soDetails['shippingAddress'] ?></p>
                    <p>State Name : <?= fetchStateNameByGstin($customerDetails['customer_gstin']) ?>, Code : <?= substr($customerDetails['customer_gstin'], 0, 2); ?></p>
                    <p>Place of Supply : <?= fetchStateNameByGstin($customerDetails['customer_gstin']) ?></p>
                  </div>
                </div>
              </div>
              <input type="hidden" name="invoiceDetails[soId]" value="<?= $soDetails['so_id'] ?>">
              <input type="hidden" name="invoiceDetails[so_number]" value="<?= $soDetails['so_number'] ?>">
              <input type="hidden" name="invoiceDetails[customer_id]" value="<?= $soDetails['customer_id'] ?>">
              <input type="hidden" name="invoiceDetails[totalItems]" value="<?= $soDetails['totalItems'] ?>">
              <input type="hidden" name="invoiceDetails[customer_billing_address]" value="<?= $soDetails['billingAddress'] ?>">
              <input type="hidden" name="invoiceDetails[customer_shipping_address]" value="<?= $soDetails['shippingAddress'] ?>">
              <input type="hidden" name="invoiceDetails[invNo]" value="<?= $invoiceGenCode ?>">
              <input type="hidden" name="invoiceDetails[customerPoNumber]" value="<?= $soDetails['customer_po_no'] ?>">
              <input type="hidden" name="invoiceDetails[creditPeriod]" value="<?= $soDetails['credit_period'] ?>">
              <input type="hidden" name="invoiceDetails[deliveryPostingDate]" value="<?= $soDetails['delivery_date'] ?>">
              <input type="hidden" name="invoiceDetails[kamId]" value="<?= $soDetails['kamId'] ?>">
              <input type="hidden" name="invoiceDetails[profit_center]" value="<?= $soDetails['profit_center'] ?>">

              <input type="hidden" name="customerDetails[name]" value="<?= $customerDetails['trade_name'] ?>">
              <input type="hidden" name="customerDetails[phone]" value="<?= $customerDetails['customer_authorised_person_phone'] ?>">
              <input type="hidden" name="customerDetails[email]" value="<?= $customerDetails['customer_authorised_person_email'] ?>">
              <input type="hidden" name="customerDetails[gstin]" value="<?= $customerDetails['customer_gstin'] ?>">
              <input type="hidden" name="customerDetails[address]" value="<?= $customerAddressDetails[1]['customer_address_building_no'] ?? null ?>, <?= $customerAddressDetails[1]['customer_address_flat_no'] ?? null ?>, <?= $customerAddressDetails[1]['customer_address_street_name'] ?? null ?>, <?= $customerAddressDetails[1]['customer_address_pin_code'] ?? null ?>, <?= $customerAddressDetails[1]['customer_address_location'] ?? null ?>, <?= $customerAddressDetails[1]['customer_address_city'] ?? null ?>, <?= $customerAddressDetails[1]['customer_address_district'] ?? null ?>, <?= $customerAddressDetails[1]['customer_address_state'] ?? null ?>">

              <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-parent" style="border-right: 1px solid #000;">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0 border-bottom-0 border-left-0">
                    <p>Invoice No.</p>
                    <p class="font-bold"><?= $invoiceGenCode ?></p>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0 border-right-0">
                    <p>Date</p>
                    <p class="font-bold">
                      <input type="hidden" name="invoiceDetails[invoiceDate]" value="<?= date('Y-m-d') ?>">
                      <?php $invDate = date_create(date("F d,Y"));
                      echo date_format($invDate, "d M, Y"); ?>
                    </p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0 border-left-0 border-right-0">
                    <p>Terms of Payment</p>
                    <?php if ($soDetails['credit_period'] != "") { ?>
                      <p class="font-bold"><?= $soDetails['credit_period'] ?> days</p>
                    <?php } ?>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0 border-right-0">
                    <p>Dispatch Doc No.</p>
                    <?php if ($pgiDetails['pgi_no'] != "") { ?>
                      <p class="font-bold"><?= $pgiDetails['pgi_no'] ?></p>
                    <?php } ?>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0 border-left-0">
                    <p>Buyer’s Order No.</p>
                    <?php if ($soDetails['customer_po_no'] != "") { ?>
                      <p class="font-bold"><?= $soDetails['customer_po_no'] ?></p>
                    <?php } ?>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0">
                    <p>Dated</p>
                    <?php if ($soDetails['delivery_date'] != "") { ?>
                      <p class="font-bold">
                        <?php
                        $date = date_create($soDetails['delivery_date']);
                        echo date_format($date, 'd-m-Y');
                        ?></p>
                    <?php } ?>
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
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>UOM</th>
                    <th>Sub Total</th>
                    <th>Discount</th>
                    <th>Tax</th>
                    <th>Total Amount</th>
                  </tr>
                  <?php
                  $i = 1;
                  $totalTaxAmt = 0;
                  $subTotalAmt = 0;
                  $allSubTotalAmt = 0;
                  $totalAmt = 0;
                  foreach ($soItemDetails as $key => $item) {
                    $totalTaxAmt += $item['totalTax'];
                    $allSubTotalAmt += $item['unitPrice'] * $item['qty'];
                    $subTotalAmt = $item['unitPrice'] * $item['qty'];
                    $totalAmt += $item['totalPrice'];
                  ?>
                    <input type="hidden" name="listItem[<?= $key ?>][so_delivery_item_pgi_id]" value="<?= $item['so_delivery_item_pgi_id'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][lineNo]" value="<?= $item['lineNo'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][so_id]" value="<?= $item['so_id'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][inventory_item_id]" value="<?= $item['inventory_item_id'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][itemCode]" value="<?= $item['itemCode'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][itemName]" value="<?= $item['itemName'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][itemDesc]" value="<?= $item['itemDesc'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][hsnCode]" value="<?= $item['hsnCode'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][tax]" value="<?= $item['tax'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][totalTax]" value="<?= $item['totalTax'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][tolerance]" value="<?= $item['tolerance'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][totalDiscount]" value="<?= $item['totalDiscount'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][totalDiscountAmt]" value="<?= $item['itemTotalDiscount'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][unitPrice]" value="<?= $item['unitPrice'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][qty]" value="<?= $item['qty'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][uom]" value="<?= $item['uom'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][totalPrice]" value="<?= $item['totalPrice'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][delivery_date]" value="<?= $item['delivery_date'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][enterQty]" value="<?= $item['enterQty'] ?>">
                    <input type="hidden" name="listItem[<?= $key ?>][deliveryStatus]" value="<?= $item['deliveryStatus'] ?>">
                    <tr class="border-top-none">
                      <td class="border-bottom-0"><?= $i++ ?></td>
                      <td class="border-bottom-0">
                        <p class="font-bold">
                          <?= $item['itemName'] ?>
                        </p>
                        <p class="font-italic"><?= $item['itemCode'] ?></p>
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
                      <td class="border-bottom-0"><?= number_format($item['unitPrice'], 2) ?></td>
                      <td class="border-bottom-0"><?= $item['uom'] ?></td>
                      <td class="border-bottom-0"><?= number_format($subTotalAmt, 2) ?></td>
                      <td class="border-bottom-0">
                        <p><?= number_format($item['itemTotalDiscount'], 2) ?></p>
                        <p class="font-italic font-bold text-xs">(%<?= $item['totalDiscount'] ?>)</p>
                      </td>
                      <td class="border-bottom-0">
                        <p><?= number_format($item['totalTax'], 2) ?></p>
                        <p class="font-italic font-bold text-xs">(%<?= $item['tax'] ?>)</p>
                      </td>
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
                    <td class="border-top-0"><?= number_format($allSubTotalAmt, 2) ?></td>
                    <td></td>
                    <td></td>
                    <td>
                      <p class="font-bold text-right">
                        <span class="rupee-symbol"><?= $currencyIcon ?></span><?= number_format($totalAmt, 2) ?>
                      </p>
                    </td>
                  </tr>
                  <tr class="border-top-none">
                    <td></td>
                    <td class="border-top-0">
                      <div class="text-right">SUB TOTAL</div>
                      <div class="text-right">DISCOUNT</div>
                      <?php
                      $branchGstin = substr($branchDetails['branch_gstin'], 0, 2);
                      $customerGstin = substr($customerDetails['customer_gstin'], 0, 2);
                      $conditionGST = $branchGstin == $customerGstin;
                      if ($conditionGST || $customerGstin =="") {
                      ?>
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
                    <td class="border-top-0"></td>
                    <td class="border-top-0">
                      <p class="text-right">
                        <input type="hidden" name="invoiceDetails[totalTaxAmt]" value="<?= $totalTaxAmt ?>">
                        <input type="hidden" name="invoiceDetails[subTotal]" value="<?= $allSubTotalAmt ?>">
                        <?= number_format($allSubTotalAmt, 2) ?>
                      </p>
                      <p class="text-right">
                        <input type="hidden" name="invoiceDetails[totalDiscount]" value="<?= $soDetails['totalDiscount'] ?>">
                        <?= number_format($soDetails['totalDiscount'], 2) ?>
                      </p>
                      <?php
                      if ($conditionGST || $customerGstin =="") {
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
                  </tr>
                </tbody>
                <tbody>
                  <tr>
                    <td></td>
                    <td>
                      <p class="text-right font-bold ">Total</p>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                      <p class="font-bold text-right">
                        <input type="hidden" name="invoiceDetails[allTotalAmt]" value="<?= $totalAmt ?>">
                        <span class="rupee-symbol"><?= $currencyIcon ?></span><?= number_format($totalAmt, 2) ?>
                      </p>
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
                <p class="font-bold"><?= number_to_words_indian_rupees($totalAmt); ?> ONLY</p>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-top-0 border-left-0 border-bottom-0">
                <p class="font-italic text-right">E. & O.E</p>
                <select name="bankId" class="form-control" id="bankId" required>
                  <option value="<?=$bankDetails['id']?>">Select Bank</option>
                  <?php
                  $bankList = $BranchSoObj->fetchCompanyBank()['data'];
                  foreach ($bankList as $bank) { ?>
                    <option value="<?= $bank['id'] ?>"><?php if ($bank['bank_name']) {
                                                          echo '🏦' . $bank['bank_name'];
                                                        } elseif ($bank['cash_account']) {
                                                          echo '💰' . $bank['cash_account'];
                                                        } ?></option>
                  <?php } ?>
                </select>
                <p>Company’s Bank Details</p>
                <div id="bankDetails">
                  <div class="d-flex">
                    <p>Bank Name :</p>
                    <p class="font-bold" id="bankName"><?= $bankDetails['bank_name'] ?></p>
                  </div>
                  <div class="d-flex">
                    <p>A/c No. :</p>
                    <p class="font-bold" id="accountNo"><?= $bankDetails['account_no'] ?></p>
                  </div>
                  <div class="d-flex">
                    <p>Branch & IFS Code :</p>
                    <p class="font-bold" id="ifscCode"><?= $bankDetails['ifsc_code'] ?></p>
                  </div>
                </div>
              </div>
            </div>
            <input type="hidden" name="companyGstin" value="<?= $companyDetails['company_footer'] ?>">
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-top-0 border-right-0">
                <p>Remarks:</p>
                <p>
                  <?= $companyDetails['company_footer'] ?>
                </p>
                <div class="d-flex">
                  <p>Company’s PAN : </p>
                  <p class="font-bold"><?= $companyDetails['company_pan'] ?></p>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child ">
                <p class="text-center font-bold">
                  for <?= $branchDetails['branch_name'] ?>
                </p>
                <p class="text-center sign-img">
                  <img src="../../public/storage/<?= $companyDetails['signature'] ?>" alt="">
                </p>
              </div>
            </div>
          </div>
          <!-- <button onclick="window.print()">Print this page</button> -->
        </div>
        <button type="submit" name="addNewinvoiveSaveFormSubmitBtn" class="btn btn-success mt-2 float-right" onclick="return confirm('Are you sure to save?')">Save</button>
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

    $("#bankId").on("change", function() {
      let bankId = $(this).val();

      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-bank.php`,
        beforeSend: function() {
          $("#customerDropDown").html(`<option value="">Loding...</option>`);
        },
        data: {
          bankId
        },
        success: function(response) {
          $("#bankDetails").html(response);
        }
      });
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