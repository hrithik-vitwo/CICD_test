<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// error_log("An error occurred", 3, "/var/log/php_errors.log");
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();

require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");

require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-customers-controller.php");

require_once("../../app/v1/functions/branch/func-items-controller.php");
require_once("../../app/v1/functions/common/templates/template-invoice.controller.php");

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$BranchSoObj = new BranchSo();
$ItemsObj = new ItemsController();
$customerDetailsObj = new CustomersController();
$templateInvoiceControllerObj = new TemplateInvoiceController();

$company = $BranchSoObj->fetchCompanyDetails()['data'];
$currencyDetails = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data'];
$currencyIcon = $currencyDetails['currency_icon'];
$currencyName = $currencyDetails['currency_name'];

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



// console($BranchSoObj->invoiceCron());

$companyDetails = $BranchSoObj->fetchCompanyDetailsById($company_id)['data'];
$branchDetails = $BranchSoObj->fetchBranchDetailsById($branch_id)['data'];
$branchAdminDetails = $BranchSoObj->fetchBranchAdminDetailsById($branch_id)['data'];
$locationDetails = $BranchSoObj->fetchBranchLocalionDetailsById($location_id)['data'];
$bankDetails = $BranchSoObj->fetchCompanyBankDetails()['data'];

$totalInvoiceAmountDetails = $BranchSoObj->totalInvoiceAmountDetails()['data'];
// console('totalInvoiceAmountDetails');
// console($totalInvoiceAmountDetails);
?>

<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<style>
    .dropdown-content {
        display: none;
    }

    table.invoices-table tr td {
        border: 1px solid #000;
        border-top: 1px solid #000 !important;
        border-collapse: collapse;
        background: none;
    }

    .invoice-modal .modal-dialog {
        max-width: 100%;
    }

    .invoice-modal .modal-header {
        height: 200px;
    }

    .invoice-modal .modal-header .nav.nav-tabs {
        margin-top: 40px !important;
    }

    .print-btn {
        display: flex;
        align-items: center;
        gap: 7px;
        width: 100px;
        padding: 7px;
        margin-left: auto;
    }

    .dropdown-item.small-text {
        font-size: 0.8rem;
    }

    .customer-modal.modal.fade.right .nav.nav-tabs li.nav-item a {
        margin-bottom: 20px !important;
    }

    @media print {

        /* Hide elements not needed in the print version */
        #pdfModal .modal-header,
        #pdfModal .modal-footer,
        body>div:not(#pdfModal .modal) {
            display: none !important;
        }

        /* Adjust the size and style of the content for printing */
        #pdfModal .modal-body {
            padding: 10px;
        }

        /* Add any additional styles or decorations you want for the printed content */
        #pdfModal .modal-content {
            border: 1px solid #000;
            border-radius: 5px;
            box-shadow: 5px 5px 10px #888888;
        }
    }

    @media (max-width: 575px) {
        .content-wrapper {
            padding-top: 115px !important;
        }

        .relative-add-btn {
            display: block;
            position: relative;
            top: -60px;
            right: -10px;
        }
    }



    ul#pills-tab li.nav-item a {
        color: #fff;
        padding: 8px 19px;
        font-size: 13px;
        box-shadow: rgb(0 0 0 / 38%) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, -1.91) 0px 8px 8px, rgba(0, 0, 0, 0.09) 0px 5px 16px;
    }

    ul#pills-tab li.nav-item a.active {
        background: #003060;
    }

    .col.btn-generatecls {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .col.btn-generatecls .btn {
        font-size: 0.6rem;
    }

    .generate-bill-modal .modal-body {
        overflow: auto;
        height: auto;
        max-height: 500px;
    }


    /* table.invoices-table tbody:nth-child(2) tr td {
    height: 250px;
    vertical-align: baseline;
  } */
</style>

<?php
if (isset($_GET['create-pgi'])) {
?>
<?php } else { ?>


    <div class="content-wrapper is-invoices">
        <section class="content">
            <div class="container-fluid">
                <h3 class="py-3 mb-4">All Invoices</h3>
                <div class="row mb-3">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="d-flex">
                            <?php
                            if (isset($_GET['payment-due'])) { ?>
                                <a href="manage-invoices.php" class="btn mr-2" style="background: #dbe5ee;"><i class="fa fa-stream"></i> Invoices List</a>
                                <a href="collect-payment.php" class="btn mr-2" style="background: #dbe5ee"><i class="fa fa-list"></i> Payment Received List</a>
                                <a href="manage-invoices.php?payment-due" class="btn" style="background: #003060; color: white;"><i class="fa fa-list"></i> Due List</a>
                            <?php } else { ?>
                                <a href="manage-invoices.php" class="btn mr-2" style="background: #003060; color: white;"><i class="fa fa-stream"></i> Invoices List</a>
                                <a href="collect-payment.php" class="btn mr-2" style="background: #dbe5ee"><i class="fa fa-list"></i> Payment Received List</a>
                                <a href="manage-invoices.php?payment-due" class="btn" style="background: #dbe5ee;"><i class="fa fa-list"></i> Due List</a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 d-flex justify-content-end create-btns">
                        <div class="btn-group mr-2">
                            <button type="button" class="btn dropdown-toggle btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-plus"></i> Create Invoice
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item small-text" href="direct-create-invoice.php">Goods Invoice</a></li>
                                <li><a class="dropdown-item small-text" href="direct-create-invoice.php?create_service_invoice">Service Invoice</a></li>
                                <li><a class="dropdown-item small-text" href="direct-create-invoice.php?proforma_invoice">Proforma Invoice</a></li>
                            </ul>
                        </div>
                        <div class="btn-group mr-2">
                            <button type="button" class="btn dropdown-toggle btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-plus"></i> Settlement
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item small-text" href="collectpaymentsettelment.php?collect-payment">Collect Payment</a></li>
                                <li><a class="dropdown-item small-text" href="collectpaymentsettelment.php?adjust-payment">Settlement</a></li>
                            </ul>
                        </div>
                        <!-- <a href="collect-payment.php?collect-payment" style="display: none;" class="btn btn-primary collectSelectedInvoiceBtn">
              <i class="fa fa-plus"></i> Collection
            </a> -->
                        <button style="display: none;" class="btn btn-primary collectSelectedInvoiceBtn">
                            <i class="fa fa-plus"></i> Collection
                        </button>
                    </div>
                </div>
                <div class="row pt-3 shadow-sm align-items-center p-2 mb-3 bg-body rounded m-0">
                    <div class="col-lg-6 col-md-6 col-sm-6 outsDiv">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 border-right pr-2 col">
                                <p class="text-xs text-secondary mb-2">Total Outstanding</p>
                                <p class="rupee-symbol font-bold"><?= $currencyName ?><span class="total_outstanding_amount1"><?= number_format($totalInvoiceAmountDetails['total_outstanding_amount'], 2) ?></span></p>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 border-right pr-2 col">
                                <p class="text-xs text-secondary mb-2">Overdue Invoice</p>
                                <p class="rupee-symbol font-bold"><?= $currencyName ?><span class="total_overdue_amount1"><?= number_format($totalInvoiceAmountDetails['total_overdue_amount'], 2) ?></span></p>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 border-right pr-2 col">
                                <p class="text-xs text-secondary mb-2">Due Amount in 30 days</p>
                                <p class="rupee-symbol font-bold"><?= $currencyName ?><span class="total_due_amount1"><?= number_format($totalInvoiceAmountDetails['total_due_in_30_days'], 2) ?></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 d-flex justify-content-end searchDiv">
                        <div class="col-lg-4 col-md-4 col-sm-4 border-right pr-2">
                            <select name="customerList" class="form-control select2" id="customerDropDown">
                            </select>
                        </div>
                        <?php
                        $keywd = '';
                        if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
                            $keywd = $_REQUEST['keyword'];
                        } else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
                            $keywd = $_REQUEST['keyword2'];
                        } ?>
                        <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                            <div class="card-body">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="row table-header-item">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="section serach-input-section">
                                                <input type="text" name="keyword" id="myInput" placeholder="Invoice search" class="field form-control" value="<?php echo $keywd; ?>">
                                                <div class="icons-container">
                                                    <div class="icon-search">
                                                        <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                                    </div>
                                                    <div class="icon-close">
                                                        <i class="fa fa-search po-list-icon" id="myBtn"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-lg-1 col-md-1 col-sm-1">
                          <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create-sales-order-delivery" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                        </div> -->
                                    </div>
                                </div>
                                <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">Filter Invoice</h5>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                        <input type="text" name="keyword2" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php /*if (isset($_REQUEST['keyword2'])) {
                                                                                                                                                      echo $_REQUEST['keyword2'];
                                                                                                                                                    } */ ?>">
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                        <select name="status_s" id="status_s" class="fld form-control" style="appearance: auto;">
                                                            <option value=""> Status </option>
                                                            <option value="active" <?php if (isset($_REQUEST['status_s']) && 'active' == $_REQUEST['status_s']) {
                                                                                        echo 'selected';
                                                                                    } ?>>Active
                                                            </option>
                                                            <option value="inactive" <?php if (isset($_REQUEST['status_s']) && 'inactive' == $_REQUEST['status_s']) {
                                                                                            echo 'selected';
                                                                                        } ?>>Inactive
                                                            </option>
                                                            <option value="draft" <?php if (isset($_REQUEST['status_s']) && 'draft' == $_REQUEST['status_s']) {
                                                                                        echo 'selected';
                                                                                    } ?>>Draft</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                    echo $_REQUEST['form_date_s'];
                                                                                                                                                } ?>" />
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <input class="fld form-control" type="date" name="to_date_s" id="to_date_s" value="<?php if (isset($_REQUEST['to_date_s'])) {
                                                                                                                                                echo $_REQUEST['to_date_s'];
                                                                                                                                            } ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync "></i>Reset</a>
                                                <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                                                    Search</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    var input = document.getElementById("myInput");
                    input.addEventListener("keypress", function(event) {
                        if (event.key === "Enter") {
                            event.preventDefault();
                            document.getElementById("myBtn").click();
                        }
                    });
                    var form = document.getElementById("search");
                    document.getElementById("myBtn").addEventListener("click", function() {
                        form.submit();
                    });
                </script>
                <div class="card list-view-div">
                    <div class="card-body">
                        <div class="row">
                            <!-- <div class="col col-1" style="width: 3%;">
                <input type="checkbox" class="mt-1">
              </div> -->
                            <div class="col col-1" style="width: 5%;">SL. No.</div>
                            <div class="col">Icon</div>
                            <div class="col">Invoice No.</div>
                            <div class="col text-right">Invoice Amount</div>
                            <div class="col">Invoice Date</div>
                            <div class="col">Due in (day/s)</div>
                            <div class="col">Status</div>
                            <div class="col">Action</div>
                            <div class="col">E-Invoice</div>
                        </div>
                        <hr />

                        <?php
                        $invTbl = ERP_BRANCH_SALES_ORDER_INVOICES;
                        $cond = '';

                        $sts = " AND `" . $invTbl . "`.`status` !='deleted'";
                        if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                            $sts = ' AND `' . $invTbl . '`.status="' . $_REQUEST['status_s'] . '"';
                        }

                        if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                            $cond .= " AND `" . $invTbl . "`.invoice_date between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                        }

                        if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                            $cond .= " AND `" . $invTbl . "`.`invoice_no` like '%" . $_REQUEST['keyword2'] . "%'";
                        } else {
                            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                $cond .= " AND `" . $invTbl . "`.`invoice_no` like '%" . $_REQUEST['keyword'] . "%'";
                            }
                        }

                        if (isset($_GET['payment-due'])) {
                            $sql_list = "SELECT `" . $invTbl . "`.*, `erp_e_invoices`.`ack_no`, `erp_e_invoices`.`ack_date`,`erp_e_invoices`.`irn`, `erp_e_invoices`.`signed_qr_code` FROM `" . $invTbl . "` LEFT JOIN `erp_e_invoices` ON `" . $invTbl . "`.so_invoice_id = `erp_e_invoices`.invoice_id WHERE `" . $invTbl . "`.invoiceStatus!=4 AND `" . $invTbl . "`.company_id='" . $company_id . "' AND `" . $invTbl . "`.branch_id='" . $branch_id . "' AND `" . $invTbl . "`.location_id='" . $location_id . "' " . $cond . " " . $sts . "ORDER BY `" . $invTbl . "`.invoice_date DESC,`" . $invTbl . "`.so_invoice_id DESC ,`" . $invTbl . "`.invoice_no ASC limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                        } else {
                            $sql_list = "SELECT `" . $invTbl . "`.*, `erp_e_invoices`.`ack_no`, `erp_e_invoices`.`ack_date`,`erp_e_invoices`.`irn`, `erp_e_invoices`.`signed_qr_code` FROM `" . $invTbl . "` LEFT JOIN `erp_e_invoices` ON `" . $invTbl . "`.so_invoice_id = `erp_e_invoices`.invoice_id WHERE `" . $invTbl . "`.company_id='" . $company_id . "' AND `" . $invTbl . "`.branch_id='" . $branch_id . "' AND `" . $invTbl . "`.location_id='" . $location_id . "' " . $cond . " " . $sts . "ORDER BY `" . $invTbl . "`.invoice_date DESC,`" . $invTbl . "`.so_invoice_id DESC ,`" . $invTbl . "`.invoice_no ASC limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                        }
                        // console($sql_list);

                        $qry_list = mysqli_query($dbCon, $sql_list);
                        $num_list = mysqli_num_rows($qry_list);

                        // console($sql_list);

                        $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' " . $cond . " " . $sts . " ";
                        $countQry = mysqli_query($dbCon, $countShow);
                        $rowCount = mysqli_fetch_array($countQry);
                        $count = $rowCount[0];
                        $cnt = $GLOBALS['start'] + 1;

                        $mobileView = '';
                        $increment = 1;
                        $cnt = $GLOBALS['start'] + 1;
                        foreach ($qry_list as $oneSoList) {

                            $customerDtls = $customerDetailsObj->getDataCustomerDetails($oneSoList['customer_id'])['data'][0];
                            $customerPic = $customerDtls['customer_picture'];
                            $customerName = $customerDtls['trade_name'];
                            $customerPicture = '';
                            $customer_name = mb_substr($customerName, 0, 1);

                            ($customerPic != '') ? ($customerPicture = '<img src="' . BASE_URL . 'public/storage/avatar/' . $customerPic . '" class="img-fluid avatar rounded-circle" alt="">') : ($customerPicture = '<div class="img-fluid avatar rounded-circle d-flex justify-content-center align-items-center" style="border: 1px solid grey;">' . $customer_name . '</div>');

                            $temDueDate = date_create($oneSoList["invoice_date"]);
                            $dateInShow = date_add($temDueDate, date_interval_create_from_date_string($oneSoList["credit_period"] . " days"));
                            $todayDate = new DateTime(date("Y-m-d"));
                            $oneInvDueDays = $todayDate->diff(new DateTime(date_format($temDueDate, "Y-m-d")))->format("%r%a");
                            $dueInDaysClass = ($oneInvDueDays >= 0) ? (($oneInvDueDays == 0) ? "status-info" : "status") : "status-danger";

                            $oneInvDueDays = ($oneInvDueDays >= 0) ? (($oneInvDueDays >= 1) ? (($oneInvDueDays == 1) ? "Due in 1 day" : "Due in " . $oneInvDueDays . " days") : "Due Today") : (($oneInvDueDays == -1) ? "Overdue by 1 day" : "Overdue by " . abs($oneInvDueDays) . " days");

                            if ($oneSoList['totalItems'] == 1) {
                                $label = "Item";
                            } elseif ($oneSoList['totalItems'] > 1) {
                                $label = "Items";
                            }

                            // console($oneSoList);
                        ?>
                            <div class="row">
                                <!-- <div class="col col-1" style="width: 3%;">
                  <input type="checkbox" class="mt-1">
                </div> -->
                                <div class="col col-1 text-xs" style="width: 5%;"><?= $cnt++ ?></div>
                                <!-- <div class="col icon-mobile" style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo1_<?= $oneSoList['so_invoice_id'] ?>"> -->
                                <div class="col icon-mobile">
                                    <?= $customerPicture ?>
                                    <p class="company-name mt-1"><?= $customerDtls['trade_name'] ?></p>
                                </div>
                                <div class="col invoice-num-mobile"><?= $oneSoList['invoice_no'] ?>
                                    <p class="item-count mt-1">[<?= $oneSoList['totalItems'] ?> <?= $label ?>]</p>
                                </div>
                                <div class="col amount-invoice-mobile"><span class="rupee-symbol"><?= $currencyName ?></span><?= decimalValuePreview($oneSoList['all_total_amt']) ?></div>
                                <div class="col delivery-date-mobile"><?= $oneSoList['invoice_date'] ?></div>
                                <div class="col delivery-date-mobile duedateCls">
                                    <?php
                                    if ($oneSoList['status'] == 'reverse') {
                                        echo '--';
                                    } else {
                                        if ($oneSoList['invoiceStatus'] != 4) { ?>
                                            <p class="<?= $dueInDaysClass ?> text-xs text-center"><?= $oneInvDueDays ?></p>
                                        <?php } else { ?>
                                            <p class="status-light text-xs text-center"><i class="fa fa-check-circle"></i> Received</p>
                                    <?php }
                                    } ?>
                                </div>
                                <div class="col status-mobile listStatus">
                                    <?php if ($oneSoList['status'] == 'reverse') {
                                        echo 'Reversed';
                                    } else { ?>
                                        <div class="status-custom text-xs text-center text-secondary listStatus">
                                            <?php if ($oneSoList['mailStatus'] == 1) {
                                                echo 'SENT <div class="round">
                        <ion-icon name="checkmark-sharp"></ion-icon>
                      </div>';
                                            } elseif ($oneSoList['mailStatus'] == 2) {
                                                echo '<span class="text-primary">VIEW</span> <div class="round text-primary">
                        <ion-icon name="checkmark-done-sharp"></ion-icon>
                      </div>';
                                            } ?>

                                            <!-- <div class="round">
                    <ion-icon name="checkmark-done-sharp"></ion-icon>bgghjhghjghjghjghjghjghj
                    </div> -->
                                            <p class="status-date"><?= $oneSoList['updated_at'] ?></p>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col action-mobile">
                                    <button type="button" class="btn text-secondary" style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo1_<?= $oneSoList['so_invoice_id'] ?>"><i class="fa fa-eye po-list-icon"></i></button>

                                    <?php
                                    if ($oneSoList['status'] != 'reposted') {
                                        if ($oneSoList['status'] == 'active') { ?>
                                            <a style="cursor:pointer" data-id="<?= $oneSoList['so_invoice_id']; ?>" class="btn btn-sm reverseInvoice" title="Reverse Now">
                                                <i class="far fa-undo po-list-icon"></i>
                                            </a>
                                        <?php } elseif ($oneSoList['status'] == 'reverse') { ?>
                                            <a href="direct-create-invoice.php?repost_invoice=<?= base64_encode($oneSoList['so_invoice_id']) ?>" style="cursor:pointer" data-id="<?= $oneSoList['so_invoice_id']; ?>" class="btn btn-sm rePostInvoice">
                                                <i class="far fa-retweet po-list-icon"></i>
                                            </a>
                                        <?php } ?>
                                        <a href="direct-create-invoice.php?edit_invoice=<?= base64_encode($oneSoList['so_invoice_id']) ?>" style="cursor:pointer" data-id="<?= $oneSoList['so_invoice_id']; ?>" class="btn btn-sm rePostInvoice">
                                            <i class="fa fa-edit po-list-icon"></i>
                                        </a>
                                    <?php } ?>
                                </div>
                                <div class="col einvoiceCls btn-generatecls">
                                    <?php
                                    if ($oneSoList['status'] != 'active') {
                                        echo '--';
                                    } else {
                                        if ($oneSoList["ack_no"] == "") {
                                    ?>
                                            <a class="btn btn-sm btn-primary generateEInvoice" id="generateEInvoice_<?= $oneSoList['so_invoice_id'] ?>" onclick="return confirm('Are you sure to generate E-invoice?')">Generate</a>
                                        <?php
                                        } else {
                                        ?>
                                            <a class="btn btn-sm btn-success">Generated</a>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateEBill_<?= $oneSoList['so_invoice_id'] ?>">Generate E-way Bill</button>

                                    <?php
                                        }
                                    }
                                    ?>

                                </div>
                            </div>
                            <hr />
                            <!-- manage internal modal start🎈🎈🎈🎈🎈🎈🎈 -->


                            <!------- form modal  -------->


                            <div class="modal fade generate-bill-modal" id="generateEBill_<?= $oneSoList['so_invoice_id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel_<?= $oneSoList['so_invoice_id'] ?>"> </h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="" method="post" id="generateEbillform" class="generateEbillform">
                                            <input type="hidden" name="so_inv_id" value="<?= $oneSoList['so_invoice_id'] ?>">
                                            <div class="modal-body">
                                                <div class="form-feild">
                                                    <div class="row">
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">IRN</label>
                                                                <input type="text" name="irn" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">Distance</label>
                                                                <input type="text" name="distance" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">Transport Mode</label>
                                                                <input type="text" name="transport_mode" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="row">
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">Transport Id</label>
                                                                <input type="text" name="transport_id" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">Transport Name</label>
                                                                <input type="text" name="transport_name" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">Transport Document Number</label>
                                                                <input type="text" name="transport_doc_no" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="row">
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">Transport Document Date</label>
                                                                <input type="date" name="transport_doc_date" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">Vehicle Number</label>
                                                                <input type="text" name="vehicle_number" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">Vehicle Type</label>
                                                                <input type="text" name="vehicle_type" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="row">
                                                        <div class="col-lg-6 col-md-6 col-12">
                                                            <div class="form-input">
                                                                <label for="">Address 1</label>
                                                                <input type="text" name="exp_addr1" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-md-6 col-12">
                                                            <div class="form-input">
                                                                <label for="">Address 2</label>
                                                                <input type="text" name="exp_addr2" class="form-control">
                                                            </div>
                                                        </div>


                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">Location</label>
                                                                <input type="text" name="exp_loc" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">Pin</label>
                                                                <input type="text" name="exp_pin" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">State Code</label>
                                                                <input type="text" name="exp_state_code" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="row">
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">Name</label>
                                                                <input type="text" name="disp_addr_name" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">Address 1</label>
                                                                <input type="text" name="disp_addr1" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">Address 2</label>
                                                                <input type="text" name="disp_addr2" class="form-control">
                                                            </div>
                                                        </div>


                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">Location</label>
                                                                <input type="text" name="disp_loc" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">Pin</label>
                                                                <input type="text" name="disp_pin" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-12">
                                                            <div class="form-input">
                                                                <label for="">State Code</label>
                                                                <input type="text" name="disp_state_code" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-sm btn-success submitForm">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>



                            <!------- end form modal  -------->


                            <!-- right modal start here  -->
                            <div class="modal fade right invoice-modal customer-modal classic-view-modal" id="fluidModalRightSuccessDemo1_<?= $oneSoList['so_invoice_id'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                    <!--Content-->
                                    <div class="modal-content">
                                        <!--Header-->
                                        <div class="modal-header">
                                            <p class="heading lead"><?= $oneSoList['invoice_no'] ?></p>
                                            <ul class="nav nav-tabs">
                                                <li class="nav-item"><a class="nav-link active" href="#preview<?= str_replace('/', '-', $oneSoList['invoice_no']) ?>" data-bs-toggle="tab">Preview</a></li>
                                                <!-- -------------------Audit History Button Start------------------------- -->
                                                <li class="nav-item">
                                                    <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $oneSoList['invoice_no']) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $oneSoList['invoice_no']) ?>" href="#history<?= str_replace('/', '-', $oneSoList['invoice_no']) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $oneSoList['invoice_no']) ?>" aria-selected="false">Trail</a>
                                                </li>
                                                <!-- -------------------Audit History Button End------------------------- -->
                                            </ul>
                                        </div>
                                        <!--Body-->
                                        <div class="modal-body">
                                            <?php
                                            $invoiceDetails = $BranchSoObj->fetchBranchSoInvoiceById($oneSoList['so_invoice_id'])['data'][0];
                                            $invoiceItemDetails = $BranchSoObj->fetchBranchSoInvoiceItems($oneSoList['so_invoice_id'])['data'];
                                            $customerDetails = $BranchSoObj->fetchCustomerDetails($invoiceDetails['customer_id'])['data'][0];
                                            $customerAddressDetails = $BranchSoObj->fetchCustomerAddressDetails($customerDetails['customer_id'])['data'];
                                            $companyData = unserialize($invoiceDetails['companyDetails']);
                                            $customerData = unserialize($invoiceDetails['customerDetails']);
                                            $encodeInvId = base64_encode($oneSoList['so_invoice_id']);

                                            $conversion_rate = 1;
                                            $conversion_currency_name = $invoiceDetails['currency_name'] ?? "";
                                            if ($invoiceDetails['conversion_rate'] != "") {
                                                $conversion_rate = $invoiceDetails['conversion_rate'];
                                            } else {
                                                $conversion_rate = 1;
                                            }

                                            $company_bank_details = unserialize($invoiceDetails['company_bank_details']);

                                            $invoiceItemDetailsGroupByHSN = $BranchSoObj->fetchBranchSoInvoiceItemsGroupByHSN($oneSoList['so_invoice_id'])['data'];
                                            ?>
                                            <!-- ************************************** -->
                                            <div style="display: flex; justify-content: space-between">
                                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                                    <li class="nav-item handleCompanyCopyTemplate" id="handleCompanyCopyTemplate_<?= $oneSoList['so_invoice_id'] ?>">
                                                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home<?= $oneSoList['so_invoice_id'] ?>" role="tab" aria-controls="pills-home" aria-selected="true">Company <sup><small>(<?= $currencyName ?>)</small></sup></a>
                                                    </li>
                                                    <?php if ($oneSoList['currency_name'] != $currencyName) { ?>
                                                        <li class="nav-item ml-2 handleCustomerCopyTemplate" id="handleCustomerCopyTemplate_<?= $oneSoList['so_invoice_id'] ?>">
                                                            <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile<?= $oneSoList['so_invoice_id'] ?>" role="tab" aria-controls="pills-profile" aria-selected="false">Customer <sup><small>(<?= $oneSoList['currency_name'] ?>)</small></sup></a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                                <div>
                                                    <select title="Select Template" class="form-control handleTemplates" id="handleTemplates_<?= $oneSoList['so_invoice_id'] ?>">
                                                        <option <?php if ($invoice_template_id == 0) {
                                                                    echo "selected";
                                                                } ?> value="0">Default Template</option>
                                                        <option <?php if ($invoice_template_id == 1) {
                                                                    echo "selected";
                                                                } ?> value="1">Template 2</option>
                                                                 <option <?php if ($invoice_template_id == 2) {
                                                                    echo "selected";
                                                                } ?> value="2">Template 3</option>
                                                    </select>
                                                    <input type="hidden" class="handleTemplateId" id="handleTemplateId_<?= $oneSoList['so_invoice_id'] ?>">
                                                    <input type="hidden" class="handleInvoiceType" id="handleInvoiceType_<?= $oneSoList['so_invoice_id'] ?>">
                                                </div>
                                            </div>
                                            <div class="tab-content" id="pills-tabContent">
                                                <div class="tab-pane fade show active" id="pills-home<?= $oneSoList['so_invoice_id'] ?>" role="tabpanel" aria-labelledby="pills-home-tab">
                                                    <div style="display: flex;justify-content: space-between;">
                                                        <p>Company Copy</p>
                                                        <span class="handlePrintBtn" id="handleCompanyPrintBtn_<?= $oneSoList['so_invoice_id'] ?>">
                                                            <a href="classic-view/invoice-preview-print.php?invoice_id=<?= base64_encode($oneSoList['so_invoice_id']) ?>&type=company&template_id=<?= $invoice_template_id ?>" class="btn btn-primary classic-view-btn float-right" target="_blank">Print</a>
                                                        </span>
                                                    </div>
                                                    <?php $templateInvoiceControllerObj->printInvoice($oneSoList['so_invoice_id'], $invoice_template_id); ?>
                                                </div>
                                                <div class="tab-pane fade" id="pills-profile<?= $oneSoList['so_invoice_id'] ?>" role="tabpanel" aria-labelledby="pills-profile-tab">
                                                    <div style="display: flex;justify-content: space-between;">
                                                        <p>Customer Copy</p>
                                                        <!-- <a href="classic-view/invoice-preview-print.php?invoice_id=<?= base64_encode($oneSoList['so_invoice_id']) ?>&type=customer" class="btn btn-primary classic-view-btn float-right" target="_blank">Print</a> -->
                                                        <span class="handlePrintBtn" id="handleCustomerPrintBtn_<?= $oneSoList['so_invoice_id'] ?>">
                                                            <a href="classic-view/invoice-preview-print.php?invoice_id=<?= base64_encode($oneSoList['so_invoice_id']) ?>&type=customer&template_id=<?= $invoice_template_id ?>" class="btn btn-primary classic-view-btn float-right" target="_blank">Print</a>
                                                        </span>
                                                    </div>
                                                    <?php $templateInvoiceControllerObj->printCustomerInvoice($oneSoList['so_invoice_id'], 1); ?>
                                                </div>
                                            </div>
                                            <!-- **************************************** -->
                                            <div class="tab-content" id="myTabContent">
                                                <!-- <div class="tab-pane show active" id="preview<?= str_replace('/', '-', $oneSoList['invoice_no']) ?>"> -->
                                                <!-- Nav pills -->
                                                <!-- <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link active template" id="template_<?= $oneSoList['so_invoice_id'] ?>" data-classic="0">Default Template</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link template" id="template_<?= $oneSoList['so_invoice_id'] ?>" data-classic="1">Classic Template</a>
                                                        </li>
                                                    </ul> -->
                                                <!-- <a href="classic-view/invoice-preview-print.php?invoice_id=<?= base64_encode($oneSoList['so_invoice_id']) ?>" class="btn btn-primary classic-view-btn float-right" target="_blank">Print</a>
                                                    
                                                    <div class="tab-content" id="pills-tabContent">
                                                        <div class="tab-pane fade show active invoiceTemplate" id="invoiceTemplate_<?= $oneSoList['so_invoice_id'] ?>" role="tabpanel">
                                                            <?php // $templateInvoiceControllerObj->printInvoice($oneSoList['so_invoice_id']); 
                                                            ?>
                                                        </div>
                                                    </div> -->
                                                <!-- </div> -->
                                                <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                <!-- <div class="tab-pane fade" id="history<?= str_replace('/', '-', $oneSoList['invoice_no']) ?>" role="tabpanel" aria-labelledby="history-tab">

                                                    <div class="audit-head-section mb-3 mt-3 ">
                                                        <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($oneSoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['created_at']) ?></p>
                                                        <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($oneSoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['updated_at']) ?></p>
                                                    </div>
                                                    <hr>
                                                    <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $oneSoList['invoice_no']) ?>">

                                                        <ol class="timeline">

                                                            <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                <div class="new-comment font-bold">
                                                                    <p>Loading...
                                                                    <ul class="ml-3 pl-0">
                                                                        <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                    </ul>
                                                                    </p>
                                                                </div>
                                                            </li>
                                                            <p class="mt-0 mb-5 ml-5">Loading...</p>

                                                            <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                <div class="new-comment font-bold">
                                                                    <p>Loading...
                                                                    <ul class="ml-3 pl-0">
                                                                        <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                    </ul>
                                                                    </p>
                                                                </div>
                                                            </li>
                                                            <p class="mt-0 mb-5 ml-5">Loading...</p>
                                                        </ol>
                                                    </div>
                                                </div> -->
                                                <!-- -------------------Audit History Tab Body End------------------------- -->
                                            </div>
                                        </div>
                                    </div>
                                    <!--/.Content-->
                                </div>
                            </div>
                            <!-- right modal end here  -->


                            <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 50%;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h6>test</h6>
                                            <button class="btn btn-primary classic-view-btn" onclick="printPDFModal()">Print</button>
                                        </div>
                                        <div class="modal-body" style="overflow: auto;">
                                            <div class="printable-view">
                                                <h3 class="h3-title text-center font-bold text-sm mb-4">Tax Invoice</h3>
                                                <table class="classic-view table-bordered">
                                                    <tbody>
                                                        <tr>
                                                            <td rowspan="3" colspan="5" class="border-right border-bottom">
                                                                <p class="font-bold"> <?= $companyData['company_name'] ?></p>
                                                                <!-- <p class="font-bold"> <?= $companyData['branch_name'] ?></p> -->
                                                                <p><?= $companyData['location_building_no'] ?></p>
                                                                <p>Flat No.<?= $companyData['location_flat_no'] ?>, <?= $companyData['location_street_name'] ?>,</p>
                                                                <p><?= $companyData['location'] ?>, <?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?> <?= $companyData['location_pin_code'] ?></p>
                                                                <p>GSTIN/UIN: <?= $companyData['branch_gstin'] ?></p>
                                                                <p>Company's PAN: <?= $companyData['company_pan'] ?></p>
                                                                <p>State Name : <?= fetchStateNameByGstin($companyData['branch_gstin']) ?>, Code : <?= substr($companyData['branch_gstin'], 0, 2); ?></p>
                                                                <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                                                            </td>
                                                            <td colspan="3" class="border-bottom">
                                                                <p>Invoice No.</p>
                                                                <p class="font-bold"><?= $invoiceDetails['invoice_no'] ?></p>
                                                            </td>
                                                            <td colspan="3" class="border-bottom">
                                                                <p>Dated</p>
                                                                <p class="font-bold"><?php $invDate = date_create($oneSoList['invoice_date']);
                                                                                        echo date_format($invDate, "F d,Y"); ?></p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="border-bottom">
                                                                <p>Mode/Terms of Payment</p>
                                                                <?php if ($invoiceDetails['credit_period'] != "") { ?>
                                                                    <p><?= $invoiceDetails['credit_period'] ?></p>
                                                                <?php } ?>
                                                            </td>
                                                            <td colspan="3" class="border-bottom">
                                                                <p>Dispatch Doc No.</p>
                                                                <?php if ($invoiceDetails['pgi_no'] != "") { ?>
                                                                    <p><?= $invoiceDetails['pgi_no'] ?></p>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="border-bottom">
                                                                <p>Buyer’s Order No.</p>
                                                                <?php if ($invoiceDetails['po_number'] != "") { ?>
                                                                    <p><?= $invoiceDetails['po_number'] ?></p>
                                                                <?php } ?>
                                                            </td>
                                                            <td colspan="3" class="border-bottom">
                                                                <p>Dated</p>
                                                                <?php if ($invoiceDetails['po_date'] != "") { ?>
                                                                    <p><?= $invoiceDetails['po_date'] ?></p>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5" class="border-right">
                                                                <p>Buyer (Bill to)</p>
                                                                <p class="font-bold"> <?= $customerData['customer_name'] ?></p>
                                                                <p style="white-space: pre-wrap;"><?= $invoiceDetails['customer_billing_address'] ?></p>
                                                                <p>GSTIN/UIN : <?= $customerData['customer_gstin'] ?></p>
                                                                <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                                            </td>
                                                            <td colspan="5">
                                                                <p>Consignee (Ship to)</p>
                                                                <p class="font-bold"> <?= $customerData['customer_name'] ?></p>
                                                                <p style="white-space: pre-wrap;"><?= $invoiceDetails['customer_shipping_address'] ?></p>
                                                                <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                                                <p>Place of Supply : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?></p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tbody>
                                                        <?php
                                                        $branchGstin = substr($companyData['branch_gstin'], 0, 2);
                                                        $customerGstin = substr($customerData['customer_gstin'], 0, 2);
                                                        $conditionGST = $branchGstin == $customerGstin;
                                                        ?>
                                                        <tr>
                                                            <th rowspan="2">Sl No.</th>
                                                            <th rowspan="2">Particulars</th>
                                                            <th rowspan="2">HSN/SAC</th>
                                                            <th rowspan="2">Quantity</th>
                                                            <th rowspan="2">Rate</th>
                                                            <th rowspan="2">UOM</th>
                                                            <!-- <th rowspan="2">Sub Total</th> -->
                                                            <th rowspan="2">Discount</th>
                                                            <?php
                                                            if ($conditionGST || $customerGstin == "") {
                                                            ?>
                                                                <th class="text-center text-bold border" colspan="2">CGST</th>
                                                                <th class="text-center text-bold border" colspan="2">SGST</th>
                                                            <?php } else { ?>
                                                                <th class="text-center text-bold border-bottom" colspan="2">IGST</th>
                                                            <?php } ?>
                                                            <th rowspan="2">Total Amount</th>
                                                        </tr>
                                                        <tr>
                                                            <?php if ($conditionGST || $customerGstin == "") { ?>
                                                                <th>Rate</th>
                                                                <th>Amount</th>
                                                                <th>Rate</th>
                                                                <th>Amount</th>
                                                            <?php } else { ?>
                                                                <th>Rate</th>
                                                                <th>Amount</th>
                                                            <?php } ?>
                                                        </tr>
                                                        <?php
                                                        $i = 1;
                                                        $totalTaxAmt = 0;
                                                        $subTotalAmt = 0;
                                                        $allSubTotalAmt = 0;
                                                        $totalDiscountAmt = 0;
                                                        $totalAmt = 0;
                                                        foreach ($invoiceItemDetails as $key => $item) {
                                                            $uomName = getUomDetail($item['uom'])['data']['uomName'];
                                                            // $uomObj = $ItemsObj->getBaseUnitMeasureById($item['uom']);
                                                            // $uomName = $uomObj['data']['uomName'];

                                                            $totalTaxAmt += $item['totalTax'];
                                                            $allSubTotalAmt += $item['unitPrice'] * $item['qty'];
                                                            $totalDiscountAmt += $item['totalDiscountAmt'];
                                                            $subTotalAmt = $item['unitPrice'] * $item['qty'];
                                                            $totalAmt += $item['totalPrice'];
                                                        ?>
                                                            <tr>
                                                                <td class="border-bottom"><?= $i++ ?></td>
                                                                <td class="border-bottom">
                                                                    <p class="font-bold"><?= $item['itemName'] ?></p>
                                                                    <p class="font-italic"><?= $item['itemCode'] ?></p>
                                                                    <p class="font-italic text-xs"><?= $item['itemRemarks'] ?></p>
                                                                </td>
                                                                <td class="border-bottom">
                                                                    <p><?= $item['hsnCode'] ?></p>
                                                                </td>
                                                                <td class="border-bottom">
                                                                    <p><?= $item['qty'] ?></p>
                                                                </td>
                                                                <td class="text-right border-bottom">
                                                                    <p><?= number_format($item['unitPrice'], 2) ?></p>
                                                                </td>
                                                                <td class="border-bottom">
                                                                    <p><?= $uomName ?></p>
                                                                </td>
                                                                <!-- <td class="border-bottom-0"><?= $subTotalAmt ?></td> -->
                                                                <td class="text-right border-bottom">
                                                                    <p><?= number_format($item['totalDiscountAmt'], 2) ?></p>
                                                                    <p class="text-xs font-italic font-bold">(%<?= $item['totalDiscount'] ?>)</p>
                                                                </td>
                                                                <?php
                                                                if ($conditionGST || $customerGstin == "") {
                                                                    $itemGstAmt = $item['totalTax'] / 2;
                                                                    $itemGstPer = $item['tax'] / 2;
                                                                ?>
                                                                    <td class="text-right border-bottom">
                                                                        <p class="text-xs font-italic font-bold">%<?= number_format($itemGstPer, 2) ?></p>
                                                                    </td>
                                                                    <td class="text-right border-bottom">
                                                                        <p class="text-xs font-italic font-bold"><span class="rupee-symbol"><?= $currencyName ?></span><?= number_format($itemGstAmt, 2) ?></p>
                                                                    </td>
                                                                    <td class="text-right border-bottom">
                                                                        <p class="text-xs font-italic font-bold">%<?= number_format($itemGstPer, 2) ?></p>
                                                                    </td>
                                                                    <td class="text-right border-bottom">
                                                                        <p class="text-xs font-italic font-bold"><span class="rupee-symbol"><?= $currencyName ?></span><?= number_format($itemGstAmt, 2) ?></p>
                                                                    </td>
                                                                <?php } else { ?>
                                                                    <td class="border-bottom">
                                                                        <p class="text-xs font-italic font-bold">%<?= $item['tax'] ?></p>
                                                                    </td>
                                                                    <td class="border-bottom">
                                                                        <p class="text-xs font-italic font-bold"><span class="rupee-symbol"><?= $currencyName ?></span><?= number_format($item['totalTax'], 2) ?></p>
                                                                    </td>
                                                                <?php } ?>
                                                                <td class="text-right border-bottom">
                                                                    <p><?= number_format($item['totalPrice'], 2) ?></p>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                        <tr>

                                                            <?php if ($conditionGST || $customerGstin == "") { ?>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                            <?php } else { ?>
                                                                <td></td>
                                                            <?php } ?>
                                                            <td></td>
                                                            <td colspan="8" class="text-right font-bold">
                                                                <p><span class="rupee-symbol"><?= $currencyName ?></span><?= number_format($totalAmt, 2) ?></p>
                                                            </td>
                                                        </tr>
                                                    </tbody>

                                                    <tbody>
                                                        <tr>
                                                            <th colspan="2" class="text-bold" rowspan="2">HSN / SAC</th>
                                                            <th colspan="3" class="text-bold" rowspan="2">Taxable Value</th>
                                                            <?php if ($conditionGST || $customerGstin == "") { ?>
                                                                <th colspan="2" class="text-bold text-center">Central Tax</th>
                                                                <th colspan="2" class="text-bold text-center">State Tax</th>
                                                            <?php } else { ?>
                                                                <th colspan="4" class="text-bold text-center border-bottom">IGST</th>
                                                            <?php } ?>
                                                            <th class="text-bold" rowspan="2">Total Tax Amount</th>
                                                        </tr>
                                                        <tr>
                                                            <?php if ($conditionGST || $customerGstin == "") { ?>
                                                                <th colspan="2" class="text-bold">Rate</th>
                                                                <th colspan="2" class="text-bold">Amount</th>
                                                                <th colspan="2" class="text-bold">Rate</th>
                                                                <th colspan="2" class="text-bold">Amount</th>
                                                            <?php } else { ?>
                                                                <th colspan="2" class="text-bold">Rate</th>
                                                                <th colspan="2" class="text-bold">Amount</th>
                                                            <?php } ?>
                                                        </tr>
                                                    </tbody>
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
                                                                <td colspan="2" class="border-bottom">
                                                                    <p><?= $item['hsnCode'] ?></p>
                                                                </td>
                                                                <td colspan="3" class="text-right border-bottom">
                                                                    <p><?= $item['basePrice'] ?></p>
                                                                </td>
                                                                <?php if ($conditionGST || $customerGstin == "") { ?>
                                                                    <td colspan="2" class="text-right border-bottom">
                                                                        <p><?= $itemGstPerHSN ?>%</p>
                                                                    </td>
                                                                    <td colspan="2" class="text-right border-bottom">
                                                                        <p><?= $itemGstAmtHSN ?></p>
                                                                    </td>
                                                                    <td colspan="2" class="text-right border-bottom">
                                                                        <p><?= $itemGstPerHSN ?>%</p>
                                                                    </td>
                                                                    <td colspan="2" class="text-right border-bottom">
                                                                        <p><?= $itemGstAmtHSN ?></p>
                                                                    </td>
                                                                <?php } else { ?>
                                                                    <td colspan="2" class="text-right border-bottom">
                                                                        <p><?= $item['tax'] ?>%</p>
                                                                    </td>
                                                                    <td colspan="2" class="text-right border-bottom">
                                                                        <p><?= $item['totalTax'] ?></p>
                                                                    </td>
                                                                <?php } ?>
                                                                <td colspan="2" class="text-right border-bottom">
                                                                    <p><?= $item['totalTax'] ?></p>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                        <tr>
                                                            <td class="text-bold border-bottom" colspan="2">Total</td>
                                                            <td class="text-right border-bottom font-bold" colspan="3">
                                                                <p><?= number_format($totalTaxableValue, 2) ?></p>
                                                            </td>
                                                            <?php if ($conditionGST || $customerGstin == "") { ?>
                                                                <td class="text-right border-bottom" colspan="2"></td>
                                                                <td class="text-right border-bottom" colspan="2">
                                                                    <p><?= $totalCgstSgstAmt ?></p>
                                                                </td>
                                                                <td class="text-right border-bottom" colspan="2"></td>
                                                                <td class="text-right border-bottom" colspan="2">
                                                                    <p><?= $totalCgstSgstAmt ?></p>
                                                                </td>
                                                            <?php } else { ?>
                                                                <td class="text-right border-bottom" colspan="2"></td>
                                                                <td class="text-right font-bold border-bottom" colspan="2">
                                                                    <p><?= $allTotalTaxAmt ?></p>
                                                                </td>
                                                            <?php } ?>
                                                            <td class="text-right font-bold border-bottom" colspan="2">
                                                                <p><?= $allTotalTaxAmt ?></p>
                                                            </td>
                                                        </tr>
                                                    </tbody>

                                                    <tbody>
                                                        <tr>
                                                            <td colspan="5" class="border-right border-bottom">
                                                                <p>Amount Chargeable (in words)</p>
                                                                <p class="font-bold"><?= number_to_words_indian_rupees($totalAmt); ?> ONLY</p>
                                                            </td>
                                                            <td colspan="5" class="border-bottom">
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
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5">
                                                                <p>Remarks: <?= $invoiceDetails['remarks'] ?></p>
                                                                <p>Declaration: <?= $invoiceDetails['declaration_note'] ?></p>
                                                                <p><?= $companyData['company_footer'] ?></p>
                                                                <p>Created By: <strong><?= getCreatedByUser($invoiceDetails['created_by']); ?></strong></p>
                                                            </td>
                                                            <td colspan="5" class="text-right border">
                                                                <p class="text-center font-bold">for <?= $companyData['company_name'] ?></p>
                                                                <p class="text-center sign-img">
                                                                    <img width="160" src="../../public/storage/signature/<?= $companyData['signature'] ?>" alt="">
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                function openPDFModal() {
                                    var pdfmodal = document.getElementById("pdfModal");
                                    $(pdfmodal).modal("toggle");
                                }
                            </script>
                            <script>
                                function printPDFModal() {
                                    // Adjust print settings
                                    var printSettings = {
                                        // Adjust page scaling
                                        scale: 0.8, // Adjust as needed
                                        // Adjust paper size
                                        paperSize: 'A4', // Adjust as needed
                                        // Adjust orientation
                                        orientation: 'landscape', // Adjust as needed
                                        // Adjust margins
                                        marginTop: 10,
                                        marginRight: 10,
                                        marginBottom: 10,
                                        marginLeft: 10
                                    };

                                    // Apply print settings
                                    var css = '';
                                    css += '@page { size: ' + printSettings.paperSize + ' ' + printSettings.orientation + '; margin: ' + printSettings.marginTop + 'mm ' + printSettings.marginRight + 'mm ' + printSettings.marginBottom + 'mm ' + printSettings.marginLeft + 'mm; }';
                                    css += 'html, body { width: 100%; height: 100%; margin: 0; padding: 0; }';

                                    var style = document.createElement('style');
                                    style.media = 'print';
                                    style.textContent = css;

                                    document.head.appendChild(style);

                                    // Print the document
                                    window.print();

                                    // Remove the added style element after printing
                                    style.remove();
                                }
                            </script>

                            <!-- mobile view area -->

                            <?php
                            $mailStatus = '';
                            if ($oneSoList['mailStatus'] == 1) {
                                $mailStatus = "SENT";
                            } elseif ($oneSoList['mailStatus'] == 2) {
                                $mailStatus = "VIEW";
                            }
                            $invDate = date_create($oneSoList['invoice_date']);
                            $invoiceDate = date_format($invDate, "F d,Y");
                            $poDate = date_create($oneSoList['po_date']);
                            $echoPoDate = date_format($poDate, "F d,Y");

                            $mobileView .= '<div class="row mb-2 mt-2 justify-content-center">
                                <div class="col col-2">
                                <div class="row mb-0">
                                    <div class="col col-12 icon-image sm-icon">
                                    ' . $customerPicture . '
                                    </div>
                                </div>
                                <div class="row mb-0">
                                    <div class="col col-12 text-center text-xs sm-customer">
                                    ' . $oneSoList['customer_name'] . '
                                    </div>
                                </div>
                                </div>

                                <div class="col-5">
                                <div class="row mb-0">
                                    <div class="col col-12 text-xs sm-inv-num">
                                    ' . $oneSoList['invoice_no'] . '
                                    <p class="item-count mt-1 text-xs">[' . $oneSoList['totalItems'] . ' item/s]</p>
                                    </div>

                                    <div class="col col-12 text-lg sm-total-amnt">
                                    ' . $oneSoList['all_total_amt'] . '
                                    </div>
                                    <div class="col col-12 text-xs">
                                    <p class="' . $dueInDaysClass . ' text-xs w-100 text-center">' . $oneInvDueDays . '</p>
                                    </div>
                                </div>
                                </div>
                                <div class="col-3">
                                <div class="row mb-0">
                                    <div class="col col-12">
                                    <div class="status-custom text-xs w-75 text-secondary">' . $mailStatus . '
                                        <div class="round">
                                        <ion-icon name="checkmark-sharp"></ion-icon>
                                        </div>
                                    </div>
                                    <p class="status-date">12 Dec, 22</p>
                                    </div>
                                    <!--
                                    <div class="col col-12">
                                    <div class="status-custom text-xs w-100 text-primary">viewed
                                        <div class="round">
                                        <ion-icon name="checkmark-done-sharp"></ion-icon>
                                        </div>
                                    </div>
                                    <p class="status-date">12 Dec, 22</p>
                                    </div>
                                    -->
                                </div>
                                </div>
                                <div class="col-2">
                                <div class="inv-mobile-action-btns">
                                <button type="button" class="btn text-secondary" style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo2_' . $oneSoList['so_invoice_id'] . '"><i class="fa fa-eye po-list-icon"></i></button>';
                            if ($oneSoList['status'] != 'reposted') {
                                if ($oneSoList['status'] == 'active') {
                                    $mobileView .= '<a style="cursor:pointer" data-id="' . $oneSoList['so_invoice_id'] . '" class="btn btn-sm reverseInvoice" title="Reverse Now">
                                                <i class="far fa-undo po-list-icon"></i>
                                            </a>';
                                } elseif ($oneSoList['status'] == 'reverse') {
                                    $mobileView .= '<a href="direct-create-invoice.php?repost_invoice=' . base64_encode($oneSoList['so_invoice_id']) . '" style="cursor:pointer" data-id="' . $oneSoList['so_invoice_id'] . '" class="btn btn-sm rePostInvoice">
                                                <i class="far fa-retweet po-list-icon"></i>
                                            </a>';
                                }
                                $mobileView .= '<a href="direct-create-invoice.php?edit_invoice=' . base64_encode($oneSoList['so_invoice_id']) . '" style="cursor:pointer" data-id="' . $oneSoList['so_invoice_id'] . '" class="btn btn-sm rePostInvoice">
                                            <i class="fa fa-edit po-list-icon"></i>
                                        </a>';
                            }

                            $mobileView .= '</div>
                                        </div>
                                        </div>
                                        <!-- right modal start here  -->
                                        <div class="modal fade right" id="fluidModalRightSuccessDemo2_' . $oneSoList['so_invoice_id'] . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                            <div style="max-width: 70%; min-width:50%" class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                            <!--Content-->
                                            <div class="modal-content">
                                                <!--Header-->
                                                <div class="modal-header " style="background: none; border:none; color:#424242">
                                                <p class="heading lead">' . $oneSoList['invoice_no'] . '</p>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true" class="white-text">×</span>
                                                </button>
                                                </div>
                                                <!--Body-->
                                                <div class="modal-body" style="padding: 0;">
                                                <ul class="nav nav-tabs">
                                                    <li class="nav-item"><a class="nav-link active" href="#preview' . $oneSoList['so_invoice_id'] . '" data-bs-toggle="tab">Preview</a></li>
                                                    <li class="nav-item"><a class="nav-link" href="#otherDetails' . $oneSoList['so_invoice_id'] . '" data-bs-toggle="tab">Other Details</a></li>
                                                </ul>
                                                <div class="tab-content">
                                                    <div class="col-md-12">
                                                    <div class="shadow-sm bg-light py-2 mx-2 my-2" id="action-navbar" style="text-align:right">
                                                        <form action="" method="POST">
                                                        <!-- <a href="branch-so-invoice-2.php?invoice-no=' . base64_encode($oneSoList['so_invoice_id']) . '" name="vendorEditBtn">
                                                                    <span class="text-info font-weight-bold shadow-sm px-2">INVOICE</span>
                                                                    </a> -->
                                                        <a href="#" name="vendorEditBtn">
                                                            <i title="Edit" style="font-size: 1.2em" class="fa fa-edit text-success mx-3"></i>
                                                        </a>
                                                        <i title="Delete" style="font-size: 1.2em" class="fa fa-trash text-danger mx-3"></i>
                                                        <i title="Toggle" style="font-size: 1.2em" class="fa fa-toggle-on text-primary mx-3"></i>
                                                        </form>
                                                    </div>
                                                    </div>
                                                    <div class="tab-pane show active" id="preview' . $oneSoList['so_invoice_id'] . '">
        
        
                                                    <!-- ################################## -->
                                                    <div class="container my-3">
                                                        <div class="row p-0 m-0 pb-2" style="border-bottom: 3px solid #0090ff;">
                                                        <div class="col-6 d-flex align-items-center">
                                                            <img width="220" src="../../public/storage/logo/' . $oneSoList['company_logo'] . '" alt="">
                                                        </div>
                                                        <div class="col-6 d-flex align-items-end flex-column">
                                                            <div>Original for Recipient</div>
                                                            <div>
                                                            <strong class="textColor">' . $oneSoList['invoice_no'] . '</strong>
                                                            </div>
                                                            <div>
                                                            <b>Date </b>
                                                            <span>' . $invoiceDate . '</span> </span>
                                                            </div>
                                                            <div>
                                                            <b>Due Date </b>
                                                            <span>' . $oneSoList['credit_period'] . '</span> </span>
                                                            </div>
                                                            <div>
                                                            <b>P.O. Number </b>
                                                            <span>' . $oneSoList['po_number'] . '</span> </span>
                                                            </div>
                                                            <div>
                                                            <b>P.O. Date </b>
                                                            <span>' . $echoPoDate . '</span> </span>
                                                            </div>
                                                        </div>
                                                        </div>
                                                        <div class="row p-0 m-0 py-3" style="border-bottom: 3px solid #0090ff;">
                                                        <div class="col-6">
                                                            <!-- <div>
                                                                        <strong class="ml-1 textColor">Sorina TEST 123</strong>
                                                                    </div> -->
                                                            <div>
                                                            <i class="textColor fa fa-briefcase"></i>
                                                            <span>' . $oneSoList['company_gstin'] . '</span>
                                                            </div>
                                                            <div>
                                                            <i class="textColor fa fa-phone"></i>
                                                            <span>7059746613</span>
                                                            </div>
                                                            <div>
                                                            <i class="textColor fa fa-envelope"></i>
                                                            <span>imranali59059@gmail.com</span>
                                                            </div>
                                                            <div>
                                                            <i class="textColor fa fa-globe"></i>
                                                            <span>www.imranali59059.com</span>
                                                            </div>
                                                            <div>
                                                            <i class="textColor fa fa-info"></i>
                                                            <span>
                                                                ' . $oneSoList['company_address'] . '
                                                            </span>
                                                            </div>
                                                        </div>
                                                        <!-- <div class="col-4 d-flex align-items-end flex-column">
                                                                    </div> -->
                                                        <div class="col-6 d-flex align-items-end flex-column">
                                                            <div>
                                                            <strong class="ml-1 textColor">' . $oneSoList['customer_name'] . '</strong>
                                                            </div>
                                                            <div>
                                                            <strong class="ml-1 textColor">' . $oneSoList['customer_gstin'] . '</strong>
                                                            </div>
                                                            <div>
                                                            <i class="textColor fa fa-phone"></i>
                                                            <span>' . $oneSoList['customer_phone'] . '</span>
                                                            </div>
                                                            <div>
                                                            <i class="textColor fa fa-envelope"></i>
                                                            <span>' . $oneSoList['customer_email'] . '</span>
                                                            </div>
                                                            <div>
                                                            <i class="textColor fa fa-info"></i>
                                                            <span>' . $oneSoList['customer_address'] . '</span>
                                                            </div>
                                                        </div>
        
                                                        </div>
                                                        <div class="row p-0 m-0">
                                                        <div class="col-md-12" style="overflow: auto;">
                                                            <div class="row">
                                                            <div class="col-6">
                                                                <div class="row">
                                                                <div class="col-1 font-weight-bold bg-light">NO</div>
                                                                <div class="col-5 font-weight-bold">PRODUCT NAME</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="row">
                                                                <div class="col-3 font-weight-bold bg-light">HSN CODE</div>
                                                                <div class="col-3 font-weight-bold">QTY</div>
                                                                <div class="col-3 font-weight-bold bg-light">UNIT PRICE</div>
                                                                <div class="col-3 font-weight-bold text-right">AMOUNT</div>
                                                                </div>
                                                            </div>
                                                            </div>
                                                            <!-- list items here -->
                                                            <?php
                                                            $i = 1;
                                                            foreach ($invoiceItemDetails as $item) {
                                                            ?>
                                                            <div class="row py-2">
                                                                <div class="col-6">
                                                                <div class="row">
                                                                    <div class="col-1 font-weight-bold bg-light"><?= $i++; ?></div>
                                                                    <div class="col-11">
                                                                    <strong>' . $item['itemName'] . '</strong>
                                                                    <div><small>' . $item['itemDesc'] . '</small></div>
                                                                    </div>
                                                                </div>
                                                                </div>
                                                                <div class="col-6">
                                                                <div class="row">
                                                                    <div class="col-3 font-weight-bold bg-light">' . $item['hsnCode'] . '</div>
                                                                    <div class="col-3">' . $item['qty'] . '/' . $uomName . '</div>
                                                                    <div class="col-3 font-weight-bold bg-light">' . $item['unitPrice'] . '</div>
                                                                    <div class="col-3 text-right">' . $item['totalPrice'] . '</div>
                                                                </div>
                                                                </div>
                                                            </div>
                                                            <?php } ?>
                                                            <!-- list items here -->
                                                        </div>
                                                        </div>
        
                                                        <div class="row p-0 m-0">
                                                        <div class="col-8">
                                                            <!-- <div>Total: Twenty Seven Thousand Four Hundred Tinety Rupees Only</div>
                                                                    <div><a href="#">Pay Now with PayPal </a></div> -->
                                                            <div>
                                                            <strong class="textColor">AUTHORIZED SIGNATORY</strong>
                                                            </div>
                                                            <img width="160" src="../../public/storage/' . $oneSoList['company_signature'] . '" alt="">
                                                        </div>
                                                        <div class="col-2 d-flex align-items-end flex-column textColor">
                                                            <div>SUB TOTAL</div>
                                                            <div>TOTAL TAX</div>
                                                            <div>TOTAL DISCOUNT</div>
                                                            <div>TOTAL AMOUNT</div>
                                                        </div>
                                                        <div class="col-2 d-flex align-items-end flex-column textColor">
                                                            <div class="">' . $oneSoList['sub_total_amt'] . '</div>
                                                            <div class="">' . $oneSoList['total_tax_amt'] . '</div>
                                                            <div class="">' . $oneSoList['totalDiscount'] . '</div>
                                                            <div class="">' . $oneSoList['all_total_amt'] . '</div>
                                                        </div>
                                                        <div class="col-12">
                                                            <strong class="textColor">NOTE:</strong>
                                                            <div class="text">' . $oneSoList['company_footer'] . '</div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    <!-- ################################## -->
                                                    </div>
                                                    <div class="tab-pane" id="otherDetails' . $oneSoList['so_invoice_id'] . '">
                                                    <div class="card p-5">
                                                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi ipsum ex soluta natus consequuntur voluptatem sed voluptate eum nulla. Molestias harum maxime ipsa? Error, ullam fugit possimus qa perspiciatis fugiat nisi dolore neque praesentium, quidem necessitatibus totam in explicabo, autem, nulla eum. Culpa, magni!
                                                    </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                            <!--/.Content-->
                                            </div>
                                        </div>
                                        <!-- right modal end here  -->
                                <hr class="m-3">
                            ';
                            ?>

                            <!-- manage internal modal end🎈🎈🎈🎈🎈🎈🎈 -->
                        <?php
                        }
                        ?>
                        <!-- End Pegination from------->
                        <?php
                        if ($count > 0 && $count > $GLOBALS['show']) {
                        ?>
                            <div class="pagination align-right">
                                <?php pagination($count, "frm_opts"); ?>
                            </div>

                            <!-- End .pagination -->

                        <?php  } ?>
                    </div>
                </div>

                <div class="card mobile-view-list">
                    <div class="card-body">
                        <?php echo $mobileView ?>
                    </div>
                </div>
            </div>
    </div>
    </div>
    </section>
    </div>

<?php } ?>

<!-- For Pegination------->
<form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                    echo  $_REQUEST['pageNo'];
                                                } ?>">
</form>
<!-- End Pegination from------->

<?php
require_once("../common/footer.php");
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

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
        $('#customerList')
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
                    $("#customerDropDown").html(`<div class="loader">
                        <i class="fas fa-spinner fa-spin"></i>
                </div>`);
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
            $('.collectSelectedInvoiceBtn').val(itemId);
            $(".collectSelectedInvoiceBtn").show();

            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-customers-list.php`,
                data: {
                    act: "listItem",
                    itemId
                },
                beforeSend: function() {
                    $("#customerInfo").html(`<div class="loader">
                        <i class="fas fa-spinner fa-spin"></i>
                </div>`);
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
                    $("#itemsDropDown").html(`<div class="loader">
                        <i class="fas fa-spinner fa-spin"></i>
                </div>`);
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
        });

        // collectSelectedInvoice payment
        $('.collectSelectedInvoiceBtn').on('click', function() {
            let customerId = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            const selectedInvoices = [];
            $('.invoiceCheckbox:checked').each(function() {
                selectedInvoices.push($(this).val());
            });

            if (selectedInvoices.length === 0) {
                alert('Please select at least one invoice to collect.');
            } else {
                // Redirect to the collect page with selected invoices
                const collectUrl = 'collectpaymentsettelment.php?collect-payment=';
                const queryParams = selectedInvoices.join(',');
                const redirectUrl = collectUrl + JSON.stringify(queryParams) + '&customerId=' + customerId;
                window.location.href = redirectUrl;
            }
        });

        $(".template").on("click", function() {
            let invoiceId = ($(this).attr("id")).split("_")[1];
            let templateId = $(this).data("classic");

            $.ajax({
                type: "POST",
                url: `ajaxs/templates/ajax-invoice-template.php`,
                data: {
                    act: 'invoiceTemplate',
                    templateId,
                    invoiceId
                },
                beforeSend: function() {
                    $(`#invoiceTemplate_${invoiceId}`).html(`<div class="loader">
                        <i class="fas fa-spinner fa-spin"></i>
                </div>`);
                },
                success: function(response) {
                    console.log('response');
                    console.log(response);
                    $(`#invoiceTemplate_${invoiceId}`).html(response);
                }
            });
        });

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
                    $(".totalPrice").html(`<div class="loader">
                        <i class="fas fa-spinner fa-spin"></i>
                </div>`);
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
        });

        $("#customerDropDown").on("change", function() {
            let customerSelect = $(this).val();
            let paymentDueUrl = window.location.search;

            if (window.location.search === '?adjust-payment') {
                adjustPayment(customerSelect);
            } else {
                $.ajax({
                    type: "POST",
                    url: `ajaxs/so/ajax-invoice-customer-details-list.php`,
                    data: {
                        customerSelect,
                        paymentDueUrl
                    },
                    beforeSend: function() {
                        $(".list-view-div").html(`<div class="loader">
                        <i class="fas fa-spinner fa-spin"></i>
                </div>`);
                    },
                    success: function(response) {
                        $(".list-view-div").html(response);
                        $(".mobile-view-list").html(response);

                        let total_overdue_amount = (parseFloat($(".total_overdue_amount").val()) > 0) ? parseFloat($(".total_overdue_amount").val()) : 0;
                        let total_due_amount = (parseFloat($(".total_due_amount").val()) > 0) ? parseFloat($(".total_due_amount").val()) : 0;
                        let total_outstanding_amount = (parseFloat($(".total_outstanding_amount").val()) > 0) ? parseFloat($(".total_outstanding_amount").val()) : 0;

                        $(".total_outstanding_amount1").text(total_outstanding_amount);
                        $(".total_due_amount1").text(total_due_amount);
                        $(".total_overdue_amount1").text(total_overdue_amount);

                        calculateDueAmt();
                        let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
                        $(".remaningAmt").html(advancedPayAmt);
                        console.log('first', advancedPayAmt);
                        let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
                        if (collectTotalAmt <= 0 || collectTotalAmt === "") {
                            $("#submitCollectPaymentBtn").prop("disabled", true);
                        } else {
                            $("#submitCollectPaymentBtn").prop("disabled", false);
                        }
                        $(".collectTotalAmt").val("");
                    }
                });
            }
        });
        $(document).on("click", ".invoiceCheckboxAll", function() {
            $(".invoiceCheckbox").prop("checked", this.checked);
        });

        // handle templates function
        function handleTemplates(invoiceId, templateId, invoiceType) {
            $.ajax({
                type: "POST",
                url: `ajaxs/so/ajax-invoice-templates.php`,
                data: {
                    act: 'handleTemplates',
                    invoiceId,
                    templateId,
                    invoiceType
                },
                beforeSend: function() {
                    $(".printable-view").html(`<div class="loader">
                        <i class="fas fa-spinner fa-spin"></i>
                </div>`);
                },
                success: function(response) {
                    console.log('response =>>', response);
                    $(".printable-view").html(response);
                }
            });
        }

        // handle Company Copy Template
        $(document).on("click", ".handleCompanyCopyTemplate", function() {
            let invoiceId = ($(this).attr("id")).split("_")[1];
            let invoiceIdEncoded = btoa(invoiceId);
            let templateId = parseFloat($(`#handleTemplateId_${invoiceId}`).val()) ? parseFloat($(`#handleTemplateId_${invoiceId}`).val()) : 0;
            $(`#handleInvoiceType_${invoiceId}`).val('company');

            $(`#handleCompanyPrintBtn_${invoiceId}`).html(`<a href="classic-view/invoice-preview-print.php?invoice_id=${invoiceIdEncoded}&type=company&template_id=${templateId}" class="btn btn-primary classic-view-btn float-right" target="_blank">Print</a>`)

            handleTemplates(invoiceId, templateId, invoiceType = 'company');
        })

        // handle Customer Copy Template
        $(document).on("click", ".handleCustomerCopyTemplate", function() {
            let invoiceId = ($(this).attr("id")).split("_")[1];
            let invoiceIdEncoded = btoa(invoiceId);
            let templateId = parseFloat($(`#handleTemplateId_${invoiceId}`).val()) ? parseFloat($(`#handleTemplateId_${invoiceId}`).val()) : 0;
            $(`#handleInvoiceType_${invoiceId}`).val('customer');

            $(`#handleCustomerPrintBtn_${invoiceId}`).html(`<a href="classic-view/invoice-preview-print.php?invoice_id=${invoiceIdEncoded}&type=customer&template_id=${templateId}" class="btn btn-primary classic-view-btn float-right" target="_blank">Print</a>`)

            handleTemplates(invoiceId, templateId, invoiceType = 'customer');
        })


        $(document).on("change", ".handleTemplates", function() {
            let invoiceId = ($(this).attr("id")).split("_")[1];
            let invoiceIdEncoded = btoa(invoiceId);
            let templateId = parseFloat($(this).val()) ? parseFloat($(this).val()) : 0;
            let invoiceType = $(`#handleInvoiceType_${invoiceId}`).val() ? $(`#handleInvoiceType_${invoiceId}`).val() : 'company';
            $(`#handleTemplateId_${invoiceId}`).val(templateId);

            $(`#handleCompanyPrintBtn_${invoiceId}`).html(`<a href="classic-view/invoice-preview-print.php?invoice_id=${invoiceIdEncoded}&type=company&template_id=${templateId}" class="btn btn-primary classic-view-btn float-right" target="_blank">Print</a>`)

            $(`#handleCustomerPrintBtn_${invoiceId}`).html(`<a href="classic-view/invoice-preview-print.php?invoice_id=${invoiceIdEncoded}&type=customer&template_id=${templateId}" class="btn btn-primary classic-view-btn float-right" target="_blank">Print</a>`)
            handleTemplates(invoiceId, templateId, invoiceType);
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


<script>
    $(document).ready(function() {
        $(document).on("click", ".generateEInvoice", function(e) {
            let btnId = $(this).attr("id");
            let invId = parseInt(btnId.split("_")[1]);
            $.ajax({
                url: '<?= BASE_URL ?>branch/ajaxs/compliance/ajax-create-e-invoice.php',
                type: 'POST',
                data: {
                    invoiceId: invId
                },
                beforeSend: function() {
                    $(`#${btnId}`).html("Generating...");
                },
                success: function(response, status, xhr) {
                    let responseData = JSON.parse(response);
                    console.log(responseData);
                    if (responseData["status"] == "success") {
                        $(`#${btnId}`).html("Generated");
                        $(`#${btnId}`).removeClass("btn-primary");
                        $(`#${btnId}`).addClass("btn-success");
                        Swal.fire({
                            icon: `success`,
                            title: `Success`,
                            text: `${responseData["message"]}`,
                        });
                    } else {
                        // $(`#${btnId}`).html("Try again");
                        $(`#${btnId}`).html("Generate");
                        // alert(`${responseData["message"]}`);
                        Swal.fire({
                            icon: `warning`,
                            title: `Opps!`,
                            text: `${responseData["message"]}`,
                        });
                    }
                },
                error: function(jqXhr, textStatus, errorMessage) {
                    Swal.fire({
                        icon: `warning`,
                        title: `Opps!`,
                        text: `${errorMessage}`,
                    });
                    // alert(`${errorMessage}`);
                    console.log(errorMessage);
                }
            });

        });
    });


    $('.reverseInvoice').click(function(e) {
        e.preventDefault(); // Prevent default click behavior

        var dep_keys = $(this).data('id');
        var $this = $(this); // Store the reference to $(this) for later use

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: 'You want to reverse this?',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Reverse'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    data: {
                        dep_keys: dep_keys,
                        dep_slug: 'reverseInvoice'
                    },
                    url: 'ajaxs/ajax-reverse-post.php',
                    beforeSend: function() {
                        $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                    },
                    success: function(response) {
                        var responseObj = JSON.parse(response);
                        console.log(responseObj);

                        if (responseObj.status == 'success') {
                            $this.parent().parent().find('.listStatus').html('Reverse');
                            $this.parent().parent().find('.einvoiceCls').html('--');
                            $this.parent().parent().find('.duedateCls').html('--');
                            $this.hide();
                        } else {
                            $this.html('<i class="far fa-undo po-list-icon"></i>');
                        }

                        let Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000
                        });
                        Toast.fire({
                            icon: responseObj.status,
                            title: '&nbsp;' + responseObj.message
                        }).then(function() {
                            // location.reload();
                        });
                    }
                });
            }
        });
    });
</script>

<script>
    // $(document).ready(function() {
    //   $(".invoicePrintBtn").click(function() {
    //     // Clone the invoice content and show it in a new window
    //     var printWindow = window.open('', '_blank');
    //     var printableDiv = $(".classic-view-so-table").clone();

    //     // Add the cloned content to the new window
    //     printWindow.document.open();
    //     printWindow.document.write('<html><head><title>Print Invoice</title>');
    //     printWindow.document.write('<link rel="stylesheet" type="text/css" href="../../../public/assets/listing.css">');
    //     printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">');
    //     printWindow.document.write('</head><body>');
    //     printWindow.document.write(printableDiv.html());
    //     printWindow.document.write('</body></html>');
    //     printWindow.document.close();

    //     // Print the new window
    //     printWindow.print();
    //   });
    // });

    $(document).ready(function() {
        $('.submitForm').on('click', function(e) {
            e.preventDefault();
            // console.log("ok");        
            var formData = new FormData($('#generateEbillform')[0]);
            // console.log("Value of irn:", formData.get("irn"));
            // formData.forEach(function(value, key){
            //     console.log(key + ": " + value);
            // });

            $.ajax({
                url: '../ajaxs/compliance/ajax-create-e-way-bill.php', // Replace this with your server URL
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // Handle success response here
                    console.log(response);
                    var obj = JSON.parse(response);
                    if (obj['status'] == 'success') {
                        swalToast(obj["status"], obj["message"]);

                    } else {
                        swalToast(obj["status"], obj["message"]);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle error response here
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>