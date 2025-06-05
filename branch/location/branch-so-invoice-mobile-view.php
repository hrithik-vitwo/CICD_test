<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
//administratorLocationAuth();
require_once("../common/header.php");
// require_once("../common/navbar.php");
// require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-customers-controller.php");

// console($_SESSION);
$BranchSoObj = new BranchSo();


$company = $BranchSoObj->fetchCompanyDetails()['data'];
$currencyIcon = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data']['currency_icon'];

$inv_id = base64_decode($_GET['inv_id']);
$invoiceDetails = $BranchSoObj->fetchBranchSoInvoiceById($inv_id)['data'][0];
$invoiceItemDetails = $BranchSoObj->fetchBranchSoInvoiceItems($inv_id)['data'];

$companyData = unserialize($invoiceDetails['companyDetails']);
$customerData = unserialize($invoiceDetails['customerDetails']);

// console('invoice *********');
// console($invoiceDetails);
// console('invoice item*********');
// console($invoiceItemDetails);
// console('company data*********');
// console($companyData);
// console('customer data*********');
// console($customerData);

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
        padding-top: 1em;
        background: #fff;
    }

    .sign-img img {
        width: 140px;
        height: 40px;
        object-fit: contain;
        margin: 0 auto;
    }

    .btns-group {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 12px 0;
    }

    .back-btn {
        color: #fff;
        font-weight: 500;
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
            font-size: 8px;
            padding: 5px;
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
                                        <p><?= $invoiceDetails['customer_billing_address'] ?></p>
                                        <p>GSTIN/UIN : <?= $customerData['customer_gstin'] ?></p>
                                        <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-child border-bottom-0">
                                        <p>Consignee (Ship to)</p>
                                        <p class="font-bold"> <?= $customerData['trade_name'] ?></p>
                                        <p><?= $invoiceDetails['customer_shipping_address'] ?></p>
                                        <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                        <p>Place of Supply : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-parent" style="border-right: 1px solid #000;">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-bottom-0 border-right-0 border-left-0">
                                        <p>Invoice No.</p>
                                        <p class="font-bold"><?= $invoiceDetails['invoice_no'] ?></p>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-right-0">
                                        <p>Dated</p>
                                        <p class="font-bold">
                                            <input type="hidden" name="invoiceDetails[invoiceDate]" value="<?= date('Y-m-d') ?>">
                                            <?php
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
                                                $podate = date_create($invoiceDetails['po_date']);
                                                echo date_format($podate, 'd-m-Y');
                                                ?>
                                            </p>
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
                                    $totalDiscountAmt = 0;
                                    $totalAmt = 0;
                                    foreach ($invoiceItemDetails as $key => $item) {
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
                                                <p><?= number_format($item['totalDiscountAmt'], 2) ?></p>
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
                                            if ($conditionGST || $customerGstin == "") {
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
                                <p>Company’s Bank Details</p>
                                <div class="d-flex">
                                    <p>Bank Name :</p>
                                    <p class="font-bold"><?= $companyData['bank_name'] ?></p>
                                </div>
                                <div class="d-flex">
                                    <p>A/c No. :</p>
                                    <p class="font-bold"><?= $companyData['account_no'] ?></p>
                                </div>
                                <div class="d-flex">
                                    <p>Branch & IFS Code :</p>
                                    <p class="font-bold"><?= $companyData['ifsc_code'] ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child border-top-0 border-right-0">
                                <p>Remarks:</p>
                                <p>
                                    <?= $companyData['company_footer'] ?>
                                </p>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-6 col-child ">
                                <p class="text-center font-bold">
                                    for <?= $branchDetails['branch_name'] ?>
                                </p>
                                <p class="text-center sign-img">
                                    <img src="../../public/storage/<?= $companyData['signature'] ?>" alt="">
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="btns-group">
                        <button class="btn btn-primary back-btn mb-0" onclick="history.back()">Back</button>
                        <button class="btn btn-primary print-btn" onclick="print()">Print</button>
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