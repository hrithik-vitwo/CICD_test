<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../../app/v1/functions/branch/func-brunch-po-controller.php");
//administratorLocationAuth();
require_once("../common/header.php");
// require_once("../common/navbar.php");
// require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-customers-controller.php");
require_once("../../app/v1/functions/vendor/func-vendor.php");
$BranchPoObj = new BranchPo();
$po_id = base64_decode($_GET['po_id']);
$poDetails = $BranchPoObj->fetchBranchPoById($po_id)['data'][0];
// console($poDetails);
$location_id=$poDetails['location_id'];
$branch_id=$poDetails['branch_id'];
$poitems = $BranchPoObj->fetchBranchPoItems($po_id)['data'];

$company_id = $poDetails['company_id'];
$get_country = queryGet("SELECT `company_country` FROM `erp_companies` WHERE company_id = $company_id");
$countrycode=$get_country['data']['company_country'];
 $bill_address = $poDetails['bill_address'];
$ship_address = $poDetails['ship_address'];
$companyData = $BranchPoObj->fetchCompanyDetailsById($company_id)['data'];
// console($companyData);
$branchData = $BranchPoObj->fetchBranchDetailsById($branch_id)['data'];
$bill_address_data = $BranchPoObj->fetchLocationDetailsById($bill_address)['data'];
$ship_address_data = $BranchPoObj->fetchLocationDetailsById($ship_address)['data'];
$company_bank_details = $BranchPoObj->fetchCompanyBankId($company_id)['data'];

$companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];

// console($bill_address_data);

//  exit();



// console('invoice *********');
// console($invoiceDetails);
// console('invoice item*********');
// console($invoiceItemDetails);
// console('company data*********');
// console($companyData);
// console('customer data*********');
// console($customerData);
// console('company bank data*********');
// console($invoiceDetails);
// console('invoiceItemDetailsGroupByHSN');
// console($invoiceItemDetailsGroupByHSN);
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
    padding: 5px 15px;
    background: none !important;
    color: #000 !important;
    border: 1px solid #000 !important;
    font-weight: 500 !important;
    font-size: 13px;
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
                <p class="font-bold text-center">PURCHASE ORDER</p>
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
            <input type="hidden" name="branchGstin" value="<?= $companyData['branch_gstin'] ?>">
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-parent">
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-child border-bottom-0 ">
                    <?php $addressCompany=getCompanyAddress($company_id,$branch_id,$location_id,$countrycode);
                     ?>
                    <p class="font-bold"> <?= $companyData['company_name'] ?> </p>
                    <div><?= $addressCompany?></div>
                    <?php 
                    if($get_country['data']['company_country'] == 103)
                          {
                    ?>
                    <p>GSTIN/UIN: <?= $branchData['branch_gstin'] ?></p>
                 
                    <p>Company’s PAN: <?= $companyData['company_pan'] ?></p>

                    <p>State Name : <?= fetchStateNameByGstin($branchData['branch_gstin']) ?>, Code : <?= substr($branchData['branch_gstin'], 0, 2); ?></p>

                    <?php
                          }
                          ?>

                  
                    <p>E-Mail : <?= $companyAdminDetailsObj['companyEmail'] ?></p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-child border-bottom-0">
                    <p> Bill to Address</p>
                    <p class="font-bold"> <?= $bill_address_data['othersLocation_name'] ?></p>
                    <p><?= $bill_address_data['othersLocation_building_no'] .",".  $bill_address_data['othersLocation_flat_no'] .",".  $bill_address_data['othersLocation_street_name'] .",".  $bill_address_data['othersLocation_pin_code'] .",".  $bill_address_data['othersLocation_location'] .",".  $bill_address_data['othersLocation_city'] .",".  $bill_address_data['othersLocation_district'] .",". $bill_address_data['othersLocation_state'] ?></p>
                   <?php if($get_country['data']['company_country'] == 103)
                          {
                    ?>
                    <p>State Name :  <?= fetchStateNameByGstin($branchData['branch_gstin']) ?>, Code : <?= substr($branchData['branch_gstin'], 0, 2); ?></p>
                    <?php
                          }
                          ?>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-child border-bottom-0">
                    <p>Ship to Address</p>
                    <p class="font-bold"> <?= $ship_address_data['othersLocation_name'] ?></p>
                    <p><?= $ship_address_data['othersLocation_building_no'] .",".  $ship_address_data['othersLocation_flat_no'] .",".  $ship_address_data['othersLocation_street_name'] .",".  $ship_address_data['othersLocation_pin_code'] .",".  $ship_address_data['othersLocation_location'] .",".  $ship_address_data['othersLocation_city'] .",".  $ship_address_data['othersLocation_district'] .",". $ship_address_data['othersLocation_state'] ?></p>
                    <?php if($get_country['data']['company_country'] == 103)
                          {
                    ?>
                   
                    <p>State Name : <?= fetchStateNameByGstin($branchData['branch_gstin']) ?>, Code : <?= substr($branchData['branch_gstin'], 0, 2); ?></p>

                     <?php
                          }
                          ?>
                  
                  </div>
                </div>
              </div>

              <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-parent" style="border-right: 1px solid #000;">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0 border-right-0 border-left-0">
                    <p>PO No.</p>
                    <p class="font-bold"><?= $poDetails['po_number'] ?></p>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0">
                    <p>PO Date</p>
                    <p class="font-bold">
                      <input type="hidden" name="poDetails[poDate]" value="<?= $poDetails['po_date'] ?>">
                      <?php $po_date = $poDetails['po_date'];

                      $poDate = date_create($poDetails['po_date']);
                      echo date_format($poDate, 'd-m-Y');
                      ?>
                    </p>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0 border-right-0 border-left-0">
                    <p>Reference No.</p>
                    <p class="font-bold"><?= $poDetails['ref_no'] ?></p>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0">
                    <p>Delivery Date</p>
                    <p class="font-bold">
                      <input type="hidden" name="poDetails[delDate]" value="<?= $poDetails['delivery_date'] ?>">
                      <?php $delivery_date = $poDetails['delivery_date'];

                      $delDate = date_create($poDetails['delivery_date']);
                      echo date_format($delDate, 'd-m-Y');
                      ?>
                    </p>
                  </div>
                </div>
                <div class="row">
                  <?php 
                  $vendorid=$poDetails['vendor_id'];
                  $address=getVendorAddressById($vendorid);
                                    
                  ?>
                  <div class="col-lg-12 col-md-12 col-sm-12 col-child border-bottom-0">
                    <p class="font-bold">Vendor Address:</p>
                    <div><?=$address?></div>
                                      
                  </div>
                </div>
               
               
              </div>
            </div>
          
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
                 
                    <th class="text-bold" rowspan="2">Total Amount</th>
                  </tr>
                  </tbody>
                  <tbody>
                  <?php
                  $i = 1;
                  
                  foreach ($poitems as $item) {
                    $po_item_details = $BranchPoObj->fetchPoItemDetails($item['inventory_item_id'])['data'];
                 
                   
                  ?>
                   
                
                  <tr>
                    <td><?= $i++ ?></td>
                    <td><?= $po_item_details['itemName'] ."(".$po_item_details['itemCode']   .")"?></td>
                    <td><?= $po_item_details['hsnCode'] ?></td>
                    <td><?= decimalQuantityPreview($item['qty']) ?></td>
                    <td><?= decimalValuePreview($item['unitPrice']) ?></td>
                    <td><?= $item['uom'] ?></td>
                    <td><?= decimalValuePreview($item['total_price']) ?></td>
                  </tr>
                  <?php } ?>
                
                </tbody>
              </table>

            </div>
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-top-0 border-right-0 border-bottom-0">
                <p>
                  Amount Chargeable (in words)
                </p>
                <p class="font-bold"><?= number_to_words_indian_rupees($poDetails['totalAmount']); ?> ONLY</p>
              </div>
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
                  <p>Branch & IFS/BSB Code :</p>
                  <p class="font-bold"><?= $company_bank_details['ifsc_code'] ?></p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-top-0 border-right-0">
                <p>Remarks:</p>
                <p>
                  <!-- <?= $companyData['company_footer'] ?> -->
                </p>
                <p>Created By: <strong><?=getCreatedByUser($poDetails['created_by']);?></strong></p>
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
            <?php
           $checkvedorTAndC = queryGet("SELECT `vendor_terms_and_cond` FROM `erp_company_enabilities` WHERE `company_id`=" . $company_id . "")['data']['vendor_terms_and_cond'];
            //  console($checkvedorTAndC);
           if($checkvedorTAndC==1){
                      $qry = queryGet("SELECT tc_text FROM `erp_applied_terms_and_conditions` WHERE slug='po' AND slug_id=" . $po_id . "")['data']['tc_text'];
                      $termscond = stripcslashes(unserialize($qry));
                     
                      echo ($termscond);
                    }
          ?>
          </div>

          
          <!-- <button onclick="window.print()">Print this page</button> -->
        </div>
        <!-- <button type="submit" name="addNewinvoiveSaveFormSubmitBtn" class="btn btn-success mt-2 float-right">Save</button> -->
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