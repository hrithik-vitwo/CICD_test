<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-brunch-po-controller.php");
require_once("../../app/v1/functions/branch/func-grn-controller.php");

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

// date checker
$check_var_sql = queryGet("SELECT * FROM `" . ERP_MONTH_VARIANT . "` WHERE `month_variant_id`=$admin_variant");
$check_var_data = $check_var_sql['data'];

$max = $check_var_data['month_end'];
$min = $check_var_data['month_start'];

// add PGI form 
$BranchPoObj = new BranchPo();
$grnObj = new GrnController();

// imranali59059💰💰💰💰💰💰💰💰💰💰💰💰💰
if (isset($_POST['submitCollectPaymentBtn'])) {
    // console($_POST);
    // console($_FILES);
    $addCollectPayment = $grnObj->insertVendorPayment($_POST, $_FILES);
    // console($addCollectPayment);
    if ($addCollectPayment['status'] == "success") {
        swalToast($addCollectPayment["status"], $addCollectPayment["message"]);
    } else {
        swalToast($addCollectPayment["status"], $addCollectPayment["message"]);
    }
}


// $customerList = $grnObj->fetchCustomerList()['data'];
$vendorList = $grnObj->fetchAllVendor()['data'];
$fetchInvoiceByCustomer = $grnObj->fetchGRNByVendorId(14)['data'];
// $fetchInvoiceByCustomer = $grnObj->fetchBranchSoInvoiceBycustomerId(1)['data'];

// console($vendorList);
// console($fetchInvoiceByCustomer);
?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
    .text {
        font-size: 1.2em;
    }

    .textColor {
        color: #0090ff;
        font-weight: bold;
    }

    .verticalAlign {
        text-align: right;
        vertical-align: bottom !important;
    }

    .tableStriped {
        background-color: #f2f2f2 !important;
    }

    .customPadding {
        padding-top: 180px !important;
    }

    .borderWhite {
        border: #fff;
    }

    .borderBlue {
        border-bottom: 3px solid #0090ff;
    }

    a.btn.shadow.waves-effect.waves-light:hover {
        background: #1a3a84db;
        color: white;
    }

    /* ######################################### */
    /* // design input type file STYLE  */

    .image-input input {
        display: none;
    }

    .image-input label {
        display: block;
        border: 2px dashed #dcdcdc;
        padding: 40px;
        cursor: pointer;
    }

    .image-input label i {
        font-size: 125%;
        margin-right: 0.3rem;
    }

    .image-input label:hover i {
        animation: shake 0.35s;
    }

    .image-input img {
        max-width: 175px;
        display: none;
    }

    .image-input span {
        display: none;
        cursor: pointer;
    }

    /******new****/

    .image-input label {
        display: flex;
        align-items: center;
        margin-top: 1em;
        justify-content: center;
        background: #fff;
        box-shadow: 6px 4px 11px -3px #00000070;
        padding: 20px;
        border-radius: 7px;
        border: 2px dashed #dcdcdc;
    }

    img.image-preview {
        object-fit: contain;
        aspect-ratio: 6/3;
        margin: auto;
    }

    .card.collect-payment-card {
        height: max-content;
        min-height: 303px;
    }

    .inputTableRow {
        overflow: auto;
    }

    /*******settlement*******/

    .settlement-card {
        min-height: 90%;
    }

    .settlement-card .image-input {
        overflow: auto;
        height: auto;
        background: #FFF;
        padding: 10px;
        border-radius: 12px;
        margin-top: 15px;
        box-shadow: 0px 3px 9px -5px #000;
    }

    @media (max-width: 575px) {
        .card.collect-payment-card {
            height: max-content;
            min-height: auto;
        }

        .card.collect-payment-card select {
            margin-top: 2em;
        }
    }


    @keyframes shake {
        0% {
            transform: rotate(0deg);
        }

        25% {
            transform: rotate(10deg);
        }

        50% {
            transform: rotate(0deg);
        }

        75% {
            transform: rotate(-10deg);
        }

        100% {
            transform: rotate(0deg);
        }
    }
</style>
<?php
if (isset($_GET['vendor-payments'])) {
?>
    <div class="content-wrapper">
        <!-- Modal -->
        <div class="modal fade" id="exampleCollectionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleCollectionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content card">
                    <div class="modal-header card-header py-2 px-3">
                        <h4 class="modal-title font-monospace text-md text-white" id="exampleCollectionModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
                        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div id="notesModalBody" class="modal-body card-body">
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <form action="" method="POST">
                    <!--Header-->
                    <input type="hidden" name="paymentDetails[paymentCollectType]" value="collect">
                    <div class="row m-0 p-0 py-2 my-2">
                        <div class="col-6">
                            <h5><strong>Vendor Payment</strong></h5>
                        </div>
                        <div class="col-6">
                            <div class="float-right d-flex">
                                <div class="mx-2"><button class="btn btn-success" type="button" id="submitCollectPaymentBtn">POST</button></div>
                                <!-- <div class="mx-2 btn btn-danger " data-dismiss="modal" aria-label="Close">X</div> -->
                            </div>
                        </div>
                    </div>
                    <!-- Collect Payment Modal -->
                    <div class="modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Payment</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="totalPaidAmtDiv"><span style="font-family: 'Font Awesome 5 Free';" id="totalReceiveAmt">0</span> amount paid against invoice</div>
                                    <div class="totalCaptureAmtDiv"><span style="font-family: 'Font Awesome 5 Free';" id="totalCaptureAmt">0</span> amount captured as an advance</div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="submitCollectPaymentBtn" class="btn btn-primary">Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Body-->
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="card collect-payment-card">
                                <div class="card-header p-3">
                                    <h4>Info</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <select name="paymentDetails[vendorId]" class="form-control" id="customerSelect">
                                                <!-- <select name="paymentDetails[vendorId]" class="form-control" id="vendorDropDown"> -->
                                                <option value="">Select Vendor</option>
                                                <?php foreach ($vendorList as $customer) { ?>
                                                    <option value="<?= $customer['vendor_id'] ?>"><?= $customer['trade_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <label for="" class="label-hidden"></label>
                                            <input type="text" name="paymentDetails[collectPayment]" class="form-control collectTotalAmt px-3 mr-1" placeholder="Enter amount" aria-label="Username" aria-describedby="basic-addon1">
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <label for="" class="label-hidden"></label>
                                            <?php $fetchCOADetails = get_acc_bank_cash_accounts()['data']; ?>
                                            <select name="paymentDetails[bankId]" class="form-control mx-1">
                                                <option value="0">Select Bank</option>
                                                <?php
                                                foreach ($fetchCOADetails as $one) {
                                                    $account_no = "";
                                                    if ($one['account_no'] != "") {
                                                        $account_no = "(" . $one['account_no'] . ")";
                                                    }
                                                    if ($one['bank_name'] != "") {
                                                ?>
                                                        <option value="<?= $one['id'] ?>"><?= $one['bank_name'] ?><?= $account_no ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                            <div class="totalamount">
                                                <p class="text-xs"> Total Invoice Amount</p>
                                                <p class="text-xs font-bold rupee-symbol">₹ <span class="totalInvAmt">0</span></p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                            <div class="totaldueamount">
                                                <p class="text-xs">Due Amount</p>
                                                <p class="text-xs font-bold rupee-symbol">₹ <span class="totalDueAmt">0</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="card collect-payment-card">
                                <div class="card-header p-3">
                                    <h4>Info</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <p class="text-xs text-right">Remaining</p>
                                            <p class="text-xs text-right font-bold rupee-symbol">₹ <span class="remaningAmt">0</span></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="image-input">
                                                <input type="file" name="paymentDetails[paymentAdviceImg]" accept="image/*" id="imageInput">
                                                <label for="imageInput" class="image-button"><i class="fa fa-image po-list-icon mr-2"></i> Upload Payment Advice</label>
                                                <img src="" class="image-preview">
                                                <span class="change-image float-right mt-3"><button class=" btn btn-danger"><i class="fa fa-times mr-2"></i>Remove</button></span>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                            <div class="form-input totalamount">
                                                <label for="">Document Date</label>
                                                <input type="date" name="paymentDetails[documentDate]" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                            <div class="form-input totaldueamount">
                                                <label for="">Posting Date</label>
                                                <input type="date" name="paymentDetails[postingDate]" class="form-control"  min="<?= $min ?>" max="<?= $max ?>"  aria-label="Username" aria-describedby="basic-addon1">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                            <div class="form-input totaloverdue">
                                                <label for="">Transaction Id / Doc. No.</label>
                                                <input type="text" placeholder="Tnx. Id / Doc. No." name="paymentDetails[tnxDocNo]" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="text-xs text-danger float-right" style="display:none" id="greaterMsg">Can not greater collect amount.</span>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="inputTableRow"></div>
                        </div>
                    </div>
                </form>
            </div>
            <section>
    </div>
<?php
} else if (isset($_GET['adjust-payment'])) {
?>
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <form action="" method="POST">
                    <!--Header-->
                    <input type="hidden" name="paymentDetails[paymentCollectType]" value="adjust">
                    <div class="row m-0 p-0 py-2 my-2">
                        <div class="col-6">
                            <h5><strong>Settlement</strong></h5>
                        </div>
                        <div class="col-6">
                            <div class="float-right d-flex">
                                <!-- <div class="mx-2"><button class="btn btn-success" type="button" data-toggle="modal" data-target="#exampleModal" id="submitCollectPaymentBtn">POST</button></div> -->
                                <!-- <div class="mx-2 btn btn-danger " data-dismiss="modal" aria-label="Close">X</div> -->
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="card settlement-card">
                                <div class="card-header p-3">
                                    <h4>Info</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <select name="paymentDetails[vendorId]" class="form-control" id="customerSelect">
                                                <!-- <select name="paymentDetails[vendorId]" class="form-control" id="vendorDropDown"> -->
                                                <option value="">Select Vendor</option>
                                                <?php foreach ($vendorList as $customer) { ?>
                                                    <option value="<?= $customer['vendor_id'] ?>"><?= $customer['trade_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                            <div class="totalamount">
                                                <p class="text-xs"> Total Invoice Amount</p>
                                                <p class="text-xs font-bold rupee-symbol">₹ <span class="totalInvAmt">0</span></p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                            <div class="totaldueamount">
                                                <p class="text-xs">Current Due Amount</p>
                                                <p class="text-xs font-bold rupee-symbol">₹ <span class="totalDueAmt">0</span></p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                            <div class="totaloverdue">
                                                <p class="text-xs">Overdue Amount</p>
                                                <p class="text-xs font-bold rupee-symbol">₹ <span class="totalOverDueAmt">0</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="card settlement-card">
                                <div class="card-header p-3">
                                    <h4>Info</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row" style="display: none;">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <p class="text-xs text-right">Remaining</p>
                                            <p class="text-xs text-right font-bold rupee-symbol">₹ <span class="remaningAmt">0</span></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="image-input">
                                                <h6 class="text-sm">Advanced List</h6>
                                                <div class="advancedAmtList" style="max-height: 200px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="inputTableRow"></div>
                        </div>
                    </div>
                </form>
            </div>
            <section>
    </div>
<?php
} else {
?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <span style="float:right;" class="mb-2">
                            <a href="?vendor-payments" class="btn btn-primary waves-effect waves-light">Make Payment</a>
                            <a href="?adjust-payment" class="btn btn-primary waves-effect waves-light">Adjust Payment</a>
                        </span>
                        <?php
                        $paymentList = $grnObj->fetchAllPayments()['data'];
                        // console($soList);
                        ?>
                        <div class="card card-tabs" style="border-radius: 20px;">
                            <?php
                            $keywd = '';
                            if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
                                $keywd = $_REQUEST['keyword'];
                            } else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
                                $keywd = $_REQUEST['keyword2'];
                            } ?>
                            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="">
                                <div class="card-body">
                                    <div class="row filter-serach-row">
                                        <div class="col-lg-2 col-md-2 col-sm-12">
                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-lg-10 col-md-10 col-sm-12">
                                            <div class="row table-header-item">
                                                <div class="col-lg-11 col-md-11 col-sm-11">
                                                    <div class="section serach-input-section">
                                                        <input type="text" name="keyword" id="myInput" placeholder="Search here..." class="field form-control " value="<?php echo $keywd; ?>">
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
                                            </div>
                                        </div>

                                        <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Filter Purchase Order</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                <input type="text" name="keyword2" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="">
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
                                                        <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>




                            <script>
                                $(document).ready(function() {

                                    $(document).on("keydown", "#myInput", function(e) {
                                        if (e.key === "Enter") {
                                            // console.log("Enter key pressed!");           
                                            e.preventDefault();
                                            $("#myBtn").click();
                                        }
                                    });

                                    $("#myBtn").on("click", function() {
                                        $("#search").submit();
                                    });
                                });
                            </script>

                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                    <?php
                                    $cond = '';

                                    $sts = " AND pay.`status` !='deleted'";
                                    if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                        $sts = ' AND pay.status="' . $_REQUEST['status_s'] . '"';
                                    }

                                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                        $cond .= " AND pay.created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                    }

                                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                        $cond .= " AND pay.`transactionId` like '%" . $_REQUEST['keyword2'] . "%' OR pay.`paymentCollectType` like '%" . $_REQUEST['keyword2'] . "%' OR pay.`documentDate` like '%" . $_REQUEST['keyword2'] . "%'";
                                    } else {
                                        if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                            $cond .= " AND pay.`transactionId` like '%" . $_REQUEST['keyword'] . "%'  OR pay.`paymentCollectType` like '%" . $_REQUEST['keyword'] . "%' OR pay.`documentDate` like '%" . $_REQUEST['keyword'] . "%' OR pay.`collect_payment` like '%" . $_REQUEST['keyword'] . "%'";
                                        }
                                    }

                                    $sql_list = "SELECT pay.*, bank.bank_name, bank.account_no FROM erp_acc_bank_cash_accounts as bank LEFT JOIN " . ERP_GRN_PAYMENTS . " AS pay ON bank.id = pay.bank_id WHERE pay.company_id = '" . $company_id . "' AND pay.branch_id ='" . $branch_id . "' AND pay.location_id ='" . $location_id . "' " . $cond . " " . $sts . " ORDER BY pay.payment_id DESC limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . "";

                                    // $sql_list = "SELECT * FROM " . ERP_GRN_PAYMENTS . " WHERE company_id='" . $company_id . "' " . $cond . " " . $sts . " ORDER BY payment_id DESC limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . "";
                                    // $qry_list = mysqli_query($dbCon, $sql_list);
                                    $qry_list = queryGet($sql_list, true);

                                    // $num_list = mysqli_num_rows($qry_list);
                                    $num_list = $qry_list['numRows'];



                                    $countShow = "SELECT count(*) FROM `" . ERP_GRN_PAYMENTS . "` AS pay WHERE 1 " . $cond . " AND pay.company_id='" . $company_id . "' " . $sts . " ";
                                    $countQry = mysqli_query($dbCon, $countShow);
                                    $rowCount = mysqli_fetch_array($countQry);
                                    $count = $rowCount[0];
                                    $cnt = $GLOBALS['start'] + 1;
                                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_VENDOR_DETAILS", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                    $settingsCheckbox = unserialize($settingsCh);
                                    if ($num_list > 0) {
                                    ?>
                                        <table class="table defaultDataTable table-hover">
                                            <thead>
                                                <tr class="alert-light">
                                                    <th>#</th>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <th>Posting Date</th>
                                                    <?php }
                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <th>Transaction Id</th>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <th>Bank Name</th>
                                                    <?php  }
                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <th>Payment Amount</th>
                                                    <?php }
                                                    if (in_array(5, $settingsCheckbox)) { ?>
                                                        <th>Created At</th>
                                                    <?php }
                                                    if (in_array(6, $settingsCheckbox)) { ?>
                                                        <th>Status</th>
                                                    <?php } ?>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // console($qry_list['data'][0]);
                                                foreach ($qry_list['data'] as $oneSoList) {
                                                    // console($oneSoList);
                                                ?>
                                                    <tr>
                                                        <td><?= $cnt++ ?></td>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <td><?= $oneSoList['postingDate'] ?></td>
                                                        <?php }
                                                        if (in_array(2, $settingsCheckbox)) { ?>
                                                            <td><?= $oneSoList['transactionId'] ?></td>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>
                                                            <td><?= $oneSoList['bank_name'] ?>(<?= $oneSoList['account_no'] ?>)</td>
                                                        <?php }
                                                        if (in_array(4, $settingsCheckbox)) {
                                                        ?>
                                                            <td><?= $oneSoList['collect_payment'] ?></td>
                                                        <?php }
                                                        if (in_array(5, $settingsCheckbox)) { ?>
                                                            <td><?= $oneSoList['created_at'] ?></td>
                                                        <?php }
                                                        if (in_array(6, $settingsCheckbox)) { ?>
                                                            <td class="text-success font-weight-bold text-capitalize listStatus"><?= $oneSoList['status'] ?></td>
                                                        <?php } ?>
                                                        <td class="d-flex">
                                                            <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $oneSoList['payment_id'] ?>"><i class="fa fa-eye po-list-icon"></i></a>
                                                            <?php if ($oneSoList['status'] == 'active') { ?>
                                                                <a style="cursor:pointer" data-id="<?= $oneSoList['payment_id']; ?>" class="btn btn-sm reversePayment" title="Reverse Now">
                                                                    <i class="far fa-undo po-list-icon"></i>
                                                                </a>
                                                            <?php } ?>
                                                            <!-- right modal start here  -->
                                                            <div class="modal fade right customer-modal" id="fluidModalRightSuccessDemo_<?= $oneSoList['payment_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                                    <!--Content-->
                                                                    <div class="modal-content">
                                                                        <!--Header-->
                                                                        <div class="modal-header">
                                                                            <p class="heading lead">Transaction Id: <?= $oneSoList['transactionId'] ?></p>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true" class="white-text">×</span>
                                                                            </button>
                                                                        </div>
                                                                        <!--Body-->
                                                                        <div class="modal-body" style="padding: 0;">
                                                                            <div class="container my-3">
                                                                                <?php
                                                                                // echo "First Sql";
                                                                                $sql = "SELECT grnPay.paymentCode,grnPay.transactionId,grnPay.postingDate,grnPay.collect_payment,vDetail.vendor_code,vDetail.trade_name as `vendor_name`
                                                                                FROM `erp_grn_payments` as grnPay
                                                                                LEFT JOIN `erp_vendor_details` as vDetail ON grnPay.vendor_id=vDetail.vendor_id
                                                                                WHERE grnPay.company_id='" . $company_id . "' AND grnPay.branch_id='" . $branch_id . "' AND grnPay.location_id='" . $location_id . "' AND grnPay.payment_id='" . $oneSoList['payment_id'] . "' AND  grnPay.status!='deleted';
                                                                                ";
                                                                                $query = queryget($sql, false);
                                                                                $data = $query['data'];
                                                                                // console($data);

                                                                                ?>
                                                                                <div class="card shadow-sm p-2">
                                                                                    <p>Payment Code: <strong><?= $data['paymentCode'] ?></strong></p>
                                                                                    <p>Transaction Id: <strong><?= $data['transactionId'] ?></strong></p>
                                                                                    <p>Posting Date: <strong><?= $data['postingDate'] ?></strong></p>
                                                                                    <p>Collect Payment: <strong><?= $data['collect_payment'] ?></strong></p>
                                                                                    <p>Vendor Code: <strong><?= $data['vendor_code'] ?></strong></p>
                                                                                    <p>Vendor Name: <strong><?= $data['vendor_name'] ?></strong></p>
                                                                                </div>
                                                                                <?php

                                                                                // echo "Second Sql";

                                                                                $sql2 = "SELECT grninv.grnIvCode, grnlog.payment_amt FROM `erp_grn_payments_log` as grnlog 
                                                                        LEFT JOIN `erp_grninvoice` as grninv ON grnlog.grn_id=grninv.grnIvId
                                                                        WHERE grnlog.company_id='" . $company_id . "' AND grnlog.branch_id='" . $branch_id . "' AND grnlog.location_id='" . $location_id . "' AND grnlog.payment_id='" . $oneSoList['payment_id'] . "' AND  grnlog.status!='deleted';
                                                                        ";
                                                                                $query2 = queryget($sql2, true);
                                                                                // console($query2['data']);
                                                                                ?>
                                                                                <div>
                                                                                    <table class="table">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>IV Doc. No.</th>
                                                                                            
                                                                                                <th>Invoice Amt.</th>
                                                                                            </tr>

                                                                                        </thead>
                                                                                        <tbody>
                                                                                            <?php
                                                                                            foreach ($query2['data'] as $data) {
                                                                                            ?>
                                                                                                <tr>
                                                                                                    <td><?= $data['grnIvCode'] ?></td>
                                                                                                
                                                                                                    <td><?= $data['payment_amt'] ?></td>
                                                                                                </tr>
                                                                                            <?php
                                                                                            }

                                                                                            ?>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>

                                                                                <?php

                                                                                ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!--/.Content-->
                                                                </div>
                                                            </div>
                                                            <!-- right modal end here  -->
                                                        </td>
                                                    </tr>


                                                <?php } ?>
                                            </tbody>
                                            <tbody>
                                                <tr>
                                                    <td colspan="8">
                                                        <!-- Start .pagination -->

                                                        <?php
                                                        if ($count > 0 && $count > $GLOBALS['show']) {
                                                        ?>
                                                            <div class="pagination align-right">
                                                                <?php pagination($count, "frm_opts"); ?>
                                                            </div>

                                                            <!-- End .pagination -->

                                                        <?php  } ?>

                                                        <!-- End .pagination -->
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php
                                    } else {
                                    ?>
                                        <table class="table defaultDataTable table-hover text-nowrap">
                                            <thead>
                                                <tr>
                                                    <td>

                                                    </td>
                                                </tr>
                                            </thead>
                                        </table>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="modal" id="myModal2">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Table Column Settings</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                                            <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                            <input type="hidden" name="pageTableName" value="ERP_VENDOR_DETAILS" />
                                            <div class="modal-body">
                                                <div id="dropdownframe"></div>
                                                <div id="main2">
                                                    <table>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                                Posting Date.</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                                Transaction Id</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                Payment Type</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                                                Collect Payment</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                                                Created At</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="6" />
                                                                Status</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="add-table-settings" class="btn btn-success">Save</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <!-- For Pegination------->
    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                        echo  $_REQUEST['pageNo'];
                                                    } ?>">
    </form>
    <!-- End Pegination from------->
<?php
}
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


        $('.reversePayment').click(function(e) {
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
                            dep_slug: 'reversePayment'
                        },
                        url: 'ajaxs/ajax-reverse-post.php',
                        beforeSend: function() {
                            $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                        },
                        success: function(response) {
                            var responseObj = JSON.parse(response);
                            console.log(responseObj);

                            if (responseObj.status == 'success') {
                                $this.parent().parent().find('.listStatus').html('reverse');
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

        let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
        if (collectTotalAmt <= 0) {
            $("#submitCollectPaymentBtn").prop("disabled", true);
        } else {
            $("#submitCollectPaymentBtn").prop("disabled", false);
        }

        var staticRemain = 0;
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
        $('#customerSelect')
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
        });

        function calculateDueAmt() {
            let totalDueAmt = 0;
            let totalInvAmt = 0;
            $(".dueAmt").each(function() {
                totalDueAmt += (parseFloat($(this).text()) > 0) ? parseFloat($(this).text()) : 0;
            });
            $(".invAmt").each(function() {
                totalInvAmt += (parseFloat($(this).text()) > 0) ? parseFloat($(this).text()) : 0;
            });
            $(".totalDueAmt").html(totalDueAmt);
            $(".totalInvAmt").html(totalInvAmt);
            $(".totalDueAmtInp").val(totalDueAmt);
            $(".totalInvAmtInp").val(totalInvAmt);
        }

        // imranali59059👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰
        // select customer 
        $("#customerSelect").on("change", function() {
            let customerSelect = $(this).val();
            // console.log(advancedPayAmt);
            if (window.location.search === '?adjust-payment') {
                adjustPayment(customerSelect);
            } else {
                $.ajax({
                    type: "POST",
                    url: `ajaxs/grn/ajax-invoice-vendor-list.php`,
                    data: {
                        customerSelect
                    },
                    beforeSend: function() {
                        $(".inputTableRow").html(`<option value="">Loding...</option>`);
                    },
                    success: function(response) {
                        $(".inputTableRow").html(response);
                        calculateDueAmt();
                        let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
                        $(".remaningAmt").html(advancedPayAmt);
                        console.log('first', advancedPayAmt);
                        $(".collectTotalAmt").val("");
                    }
                });
            }
        });

        function adjustPayment(customerSelect) {
            $.ajax({
                type: "POST",
                url: `ajaxs/grn/ajax-invoice-vendor-advanced.php`,
                data: {
                    customerSelect
                },
                beforeSend: function() {
                    $(".advancedAmtList").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    $(".advancedAmtList").html(response);
                    // calculateDueAmt();
                    // let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
                    // $(".remaningAmt").html(advancedPayAmt);
                    console.log('first', response);
                }
            });

            $.ajax({
                type: "POST",
                url: `ajaxs/grn/ajax-invoice-vendor-list2.php`,
                data: {
                    customerSelect
                },
                beforeSend: function() {
                    $(".inputTableRow").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    $(".inputTableRow").html(response);
                    calculateDueAmt();
                    let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
                    $(".remaningAmt").html(advancedPayAmt);
                    console.log('first', advancedPayAmt);
                }
            });
        }

        // imranali59059💰💰💰💰💰💰💰💰💰💰💰💰💰
        // collect payment Amount 
        $(document).on("keyup", ".collectTotalAmt", function() {
            let thisAmt = $(this).val();
            let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
            let rem = (parseFloat(thisAmt) + parseFloat(advancedPayAmt)) ? (parseFloat(thisAmt) + parseFloat(advancedPayAmt)) : 0;
            let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
            staticRemain = rem;
            $(".remaningAmt").text(rem);

            if (collectTotalAmt <= 0) {
                $("#submitCollectPaymentBtn").prop("disabled", true);
            } else {
                $("#submitCollectPaymentBtn").prop("disabled", false);
            }
        })
        // received payment amount🪢🪢🪢🪢🪢🪢🪢🪢🪢🪢🪢🪢🪢 
        $(document).on("blur", ".receiveAmt", function() {
            let rowId = ($(this).attr("id")).split("_")[1];
            let recAmt = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            let invoiceAmt = $(`#invoiceAmt_${rowId}`).text();
            let dueAmt = (parseFloat($(`#dueAmt_${rowId}`).text()) > 0) ? parseFloat($(`#dueAmt_${rowId}`).text()) : 0;
            let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
            let remaningAmt = $(".remaningAmt").text();

            var totalDueAmt = 0;
            var totalRecAmt = 0;

            let duePercentage = ((parseFloat(dueAmt) - parseFloat(recAmt)) / parseFloat(invoiceAmt)) * 100;
            $(`#duePercentage_${rowId}`).text(`${Math.round(duePercentage,2)}%`);
            // $(`#duePercentage_${rowId}`).text(`${duePercentage.toFixed(2)}%`);

            $(".receiveAmt").each(function() {
                totalRecAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
            let rem = parseFloat(collectTotalAmt) + parseFloat(advancedPayAmt);
            staticRemain = rem;
            // let remaintTotalAmt = parseFloat(collectTotalAmt) - parseFloat(totalRecAmt);
            let remaintTotalAmt = parseFloat(staticRemain) - parseFloat(totalRecAmt);
            if (totalRecAmt > collectTotalAmt) {
                console.log('over');
                $(".remaningAmt").text(collectTotalAmt);
                $(".remaningAmtInp").val(collectTotalAmt);
                $("#submitCollectPaymentBtn").prop("disabled", true);
                $("#greaterMsg").show();
            } else {
                console.log('ok');
                $(".remaningAmt").text(remaintTotalAmt);
                $(".remaningAmtInp").val(remaintTotalAmt);
                $("#submitCollectPaymentBtn").prop("disabled", false);
                $("#greaterMsg").hide();
            }
            console.log('due amt', dueAmt, recAmt);
            if (recAmt <= dueAmt) {
                $(`#warningMsg_${rowId}`).hide();
            } else {
                $(`#warningMsg_${rowId}`).show();
            }
        });

        $("#submitCollectPaymentBtn").on("click", function() {
            let enterAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
            let totalRecAmt2 = 0;
            let advancedPayAmt2 = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
            $(".receiveAmt").each(function() {
                totalRecAmt2 += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            let totalCaptureAmt = (parseFloat(enterAmt) + parseFloat(advancedPayAmt2)) - (parseFloat(totalRecAmt2));
            console.log(totalRecAmt2, enterAmt);
            $("#totalReceiveAmt").text(`₹${totalRecAmt2}`);
            $("#totalCaptureAmt").text(`₹${totalCaptureAmt}`);

            // if (totalRecAmt2 === 0) {
            //   $(".totalPaidAmtDiv").hide();
            // }else{
            //   $(".totalPaidAmtDiv").show();
            // }

            if (totalCaptureAmt === 0) {
                $(".totalCaptureAmtDiv").hide();
            } else {
                $(".totalCaptureAmtDiv").show();
            }
        });

        // ******************************************************************
        $(document).on("click", ".paymentSettlement", function() {
            let inv_id = ($(this).attr("id")).split("_")[1];
            advancedAmtInpFunc(inv_id);
            console.log('inv_id');
            console.log(inv_id);
        });

        function advancedAmtInpFunc(inv_id) {
            var payment_id = "";
            $(document).on("keyup", `.inv-${inv_id}-advancedAmtInp`, function() {
                payment_id = ($(this).attr("id")).split("_")[1];
                let enterAdvAmt = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                let staticAdvancedAmtInp = (parseFloat($(`#inv-${inv_id}-staticAdvancedAmtInp_${payment_id}`).val()) > 0) ? parseFloat($(`#inv-${inv_id}-staticAdvancedAmtInp_${payment_id}`).val()) : 0;
                let sumAdv = (staticAdvancedAmtInp - enterAdvAmt);
                let dueAmtOnModalStatic = $(`.inv-${inv_id}-dueAmtOnModalStatic`).val();
                let totalEnterAdvAmt = 0;

                if (enterAdvAmt > staticAdvancedAmtInp) {
                    $(`#inv-${inv_id}-advancedAmtSpan_${payment_id}`).html(staticAdvancedAmtInp);
                    $(`#inv-${inv_id}-advancedAmtMsg_${payment_id}`).text(`Enter correct value`);
                    $(this).val('');
                } else {
                    $(`#inv-${inv_id}-advancedAmtSpan_${payment_id}`).html(sumAdv);
                }

                $(`.inv-${inv_id}-advancedAmtInp`).each(function() {
                    totalEnterAdvAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                });

                let itemDueAmt = $(`#dueAmt_${inv_id}`).html();
                let dueAmtOnModalCal = (itemDueAmt - totalEnterAdvAmt);

                if (dueAmtOnModalStatic < totalEnterAdvAmt) {
                    $(`#dueAmtAdvancedAmtMsg_${inv_id}`).text(`Enter correct value`);
                    $(`.inv-${inv_id}-dueAmtOnModal`).text(0);
                    $(`#invoiceAddBtn_${inv_id}`).attr('disabled', 'disabled');
                } else {
                    $(`#dueAmtAdvancedAmtMsg_${inv_id}`).text('');
                    $(`.inv-${inv_id}-dueAmtOnModal`).text(dueAmtOnModalCal.toFixed(2));
                    $(`#invoiceAddBtn_${inv_id}`).removeAttr("disabled");
                }
                $(`#receiveAmt_${inv_id}`).val(totalEnterAdvAmt);
                setTimeout(() => {
                    $(`#inv-${inv_id}-advancedAmtMsg_${payment_id}`).hide();
                }, 3000);
            });
        }


        // *********************************************************************
        $(document).on("click", `.invoiceAddBtn`, function() {
            let inv_id = $(this).val();
            // alert(inv_id);
            let customerId = $(`#vendorId_${inv_id}`).val();
            let payments = [];
            let paymentAmt = 0;
            let i = 0;
            $(`.inv-${inv_id}-advancedAmtInp`).each(function() {
                var paymentId = $(this).data('advancedid');
                paymentAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                let payAmt = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                payments[paymentId] = payAmt;
            });

            if (paymentAmt == 0) {
                $(`#dueAmtAdvancedAmtMsg_${inv_id}`).text(`Please enter amount`);
            } else {

                payments = JSON.stringify(payments);
                $.ajax({
                    type: "POST",
                    url: `ajaxs/grn/ajax-grn-invoice-settlement.php`,
                    data: {
                        payments,
                        inv_id,
                        paymentAmt,
                        customerId
                    },
                    beforeSend: function() {
                        $(`#invoiceAddBtn_${inv_id}`).html(`Posting...`);
                        $(`#invoiceAddBtn_${inv_id}`).attr('disabled', 'disabled');
                    },
                    success: function(response) {
                        let data = JSON.parse(response);
                        console.log(data);
                        $(`#postMsg_${inv_id}`).html(data.message);
                        $(`#invoiceAddBtn_${inv_id}`).html(`POST`);
                        adjustPayment(customerId);
                    }
                });
            }
            setTimeout(() => {
                $(`#postMsg_${inv_id}`).hide();
                $(`#dueAmtAdvancedAmtMsg_${inv_id}`).html('');
            }, 3000);
        });


        // imranali59059🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️
        // dynamically image upload and show 
        $('#pic').on("change", function(e) {
            let url = $(this).val();
            let img = $('.load_img');
            let tmppath = URL.createObjectURL(e.target.files[0]);
            img.attr('src', tmppath);
            $(".imageUrl").html(url);
        });

        // imranali59059📁📁📁📁📁📁📁📁📁📁📁📁📁
        // design input type file STYLE
        $('#imageInput').on('change', function() {
            $input = $(this);
            if ($input.val().length > 0) {
                fileReader = new FileReader();
                fileReader.onload = function(data) {
                    $('.image-preview').attr('src', data.target.result);
                }
                fileReader.readAsDataURL($input.prop('files')[0]);
                $('.image-button').css('display', 'none');
                $('.image-preview').css('display', 'block');
                $('.change-image').css('display', 'block');
            }
        });

        $('.change-image').on('click', function() {
            $control = $(this);
            $('#imageInput').val('');
            $preview = $('.image-preview');
            $preview.attr('src', '');
            $preview.css('display', 'none');
            $control.css('display', 'none');
            $('.image-button').css('display', 'block');
        });

        // enter btn hit to block submit form  
        $(document).ready(function() {
            $(window).keydown(function(event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });

        $('#vendorDropDown').select2({
            placeholder: 'Select Vendor',
            ajax: {
                url: 'ajaxs/so/ajax-vendor-list-select2.php',
                dataType: 'json',
                delay: 50,
                data: function(params) {
                    return {
                        searchTerm: params.term // search term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        }).on('select2:open', function(e) {
            var $results = $(e.target).data('select2').$dropdown.find('.select2-results');

            // Conditionally add the 'Add New' button based on the element
            if (!$results.find('a').length) {
                $results.append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewCustomerModal">Add New</a></div>`);
            }
        });
    })
</script>

<script src="<?= BASE_URL; ?>public/validations/paymentValidation.js"></script>