<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-items-controller.php");
require_once("boq/controller/boq.controller.php");

// date checker
$check_var_sql = queryGet("SELECT * FROM `" . ERP_MONTH_VARIANT . "` WHERE `month_variant_id`=$admin_variant");
$check_var_data = $check_var_sql['data'];

$max = $check_var_data['month_end'];
$min = $check_var_data['month_start'];

$quotation_to_so = isset($_GET['quotation_to_so']);

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$BranchSoObj = new BranchSo();
$ItemsObj = new ItemsController();
$boqControllerObj = new BoqController();

global $company_id;

// fetch company details
$companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
$companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
$branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
$companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
$locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
$companyData = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);

// console('$companyBankSerialize');
// console($companyData);

if (isset($_POST['addNewSOFormSubmitBtn'])) {

    $addBranchSo = $BranchSoObj->addBranchSo($_POST);
    // console($addBranchSo);
    if ($addBranchSo['status'] == "success") {
        swalAlert($addBranchSo["status"], $addBranchSo['soNumber'], $addBranchSo["message"], $_SERVER['PHP_SELF']);
    } else {
        swalToast($addBranchSo["status"], 'Warning', $addBranchSo["message"]);
    }
}

$datetime = '2022-08-30 17:24:19'; // Example DateTime variable
$date = '2022-08-30 '; // Example Date variable

//Call this function For Display Date Or DateTime in Proper Format  
// for false (30-08-2022 17:24:19) & for true (Aug 30, 2022 05:24 PM)
formatDateORDateTime($datetime, false);

if (isset($_POST['jobOrderApprovalSubmitBtn'])) {

    $jobOrderCompletionConfirmationObj = $BranchSoObj->jobOrderCompletionConfirmation($_POST);

    // console($jobOrderCompletionConfirmationObj);

    // if ($jobOrderCompletionConfirmationObj['status'] == "success") {
    //     // swalAlert($jobOrderCompletionConfirmationObj["status"], "KJGHFTYF55552000", $jobOrderCompletionConfirmationObj["message"], $_SERVER['PHP_SELF']);
    //     swalAlert($jobOrderCompletionConfirmationObj["status"], $jobOrderCompletionConfirmationObj["message"], $_SERVER['PHP_SELF']);
    // } else {
    //     swalAlert($jobOrderCompletionConfirmationObj["status"], $jobOrderCompletionConfirmationObj["message"]);
    // }
}

?>

<style>
    .customer-modal .nav.nav-tabs li.nav-item a.nav-link {
        font-size: 12px;
    }

    .display-flex-gap {
        gap: 0 !important;
    }

    .card-body.others-info.vendor-info.so-card-body {
        height: 330px;
    }

    .fob-section div {
        align-items: center;
        gap: 3px;
    }

    .so-delivery-create-btn {
        display: flex;
        align-items: center;
        gap: 5px;
        max-width: 250px;
        margin-left: auto;
    }

    .deliveryCreationBtn {
        text-decoration: none;
        padding: 8px 5px;
        border-radius: 5px;
    }

    .customer-modal .modal-header {
        height: 250px !important;
    }

    .filter-list a.active {
        background-color: #003060;
        color: #fff;
    }

    .display-flex-space-between p {
        width: 77%;
        text-align: left;
    }

    .dFlex {
        display: flex;
        justify-content: space-between;
        align-items: center !important;
        padding: 10px 0;
    }

    .code-des p:nth-child(2) {
        margin: 0 !important;
    }

    .left-section {
        align-items: center !important;
    }

    /* 
    .classic-view-so-table .row .col {
        padding: 10px 15px;
        background: #003060;
        color: #fff;
        border-right: 1px solid #fff;
        font-weight: 500;
        font-size: 12px;
        text-align: left;
        white-space: nowrap;
    } */



    .create-delivery-btn-sales {
        justify-content: end;
    }

    .so_number-item {
        align-items: center;
        justify-content: space-between;
    }

    .item-count {
        display: flex;
        align-items: center;
    }

    .customer-modal .modal-header {
        height: 285px !important;
    }

    .icon-user-text {
        width: 100%;
    }

    .icon-user-img i {
        border: 1px solid #fff;
        padding: 15px 10px;
        border-radius: 7px;
    }

    .so-header {
        gap: 10px;
        display: flex;
        align-items: center;
    }


    @media print {
        @page {
            size: A4;
        }
    }


    /* 
    @media print {

        body {
            visibility: hidden;
            height: 100vh !important;
        }
        
        .printable-view {
            visibility: visible !important;
            page-break-before: always;
        }

        .classic-view-modal .modal-dialog {
            max-width: 100% !important;
        }

        .classic-view-modal .modal-dialog .modal-header {
            height: 0 !important;
        }

        .classic-view-modal table.classic-view th {
            font-size: 12px !important;
            padding: 5px 10px !important;
        }

        table.classic-view td p {
            font-size: 12px !important;
        }

    } */


    @media (max-width: 575px) {

        .filter-serach-row {
            align-items: center;
            padding-top: 9px;
            margin-bottom: 0 !important;
        }

        .customer-modal .nav.nav-tabs li.nav-item a.nav-link {
            padding: 7px;
        }

        .card-body.others-info.vendor-info.so-card-body {
            height: auto !important;
        }

        .customer-modal .modal-header {
            height: 285px !important;
        }

        .customer-modal .nav.nav-tabs {
            top: 0 !important;
        }

    }
</style>


<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<?php
if (isset($_GET['customer-so-creation'])) { ?>
    <div class="content-wrapper">
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content card">
                    <div class="modal-header card-header py-2 px-3">
                        <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
                        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div id="notesModalBody" class="modal-body card-body">
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="itemModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="itemModalContent modal-content card">
                    <div class="modal-header card-header py-2 px-3">
                        <h4 class="modal-title font-monospace text-md text-white" id="itemModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
                        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div id="itemModalBody" class="modal-body card-body">
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Sales Order List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Sales Order</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>
            </div>
            <form action="" method="POST" id="addNewSOForm">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="card so-creation-card po-creation-card ">
                            <div class="card-header">
                                <div class="row customer-info-head">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="head">
                                            <i class="fa fa-user"></i>
                                            <h4>Customer Info</h4>
                                            <input type="hidden" class="customerIdInp" value="0">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body others-info vendor-info so-card-body">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="row customer-info-form-view">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="input-box customer-select">
                                                    <span class="text-danger">*</span>
                                                    <select name="customerId" id="customerDropDown" class="form-control" required>
                                                        <option value="">Select Customer</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="customer-info-text" id="customerInfo">
                                                    <div class="watermark">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="card so-creation-card po-creation-card ">
                            <div class="card-header">
                                <div class="row others-info-head">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="head">
                                            <i class="fa fa-info"></i>
                                            <h4>Others Info</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body others-info vendor-info so-card-body">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="row others-info-form-view mb-2" style="row-gap: 17px;">
                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                <label>Posting Date: <span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="date" value="<?= date("Y-m-d") ?>" name="soDate" id="soDate" class="form-control" min="<?= $min ?>" max="<?= $max ?>" required />
                                                    <span class="input-group-addon soDateMsg"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                <label>Posting Time: <span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="time" name="postingTime" id="postingTime" value="<?= date("H:i") ?>" class="form-control" required />
                                                    <span class="input-group-addon postingTimeMsg"></span>
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                <label>Delivery Date: <span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="date" value="<?= date("Y-m-d") ?>" name="deliveryDate" id="deliveryDate" class="form-control" required />
                                                    <span class="input-group-addon deliveryDateMsg"></span>
                                                </div>
                                            </div>
                                            <input type="hidden" value="14" name="approvalStatus" id="approvalStatus">
                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                <div class="form-input">
                                                    <label for=""> Customer Order Number <span class="text-danger">*</span></label>
                                                    <input type="text" name="customerPO" class="form-control" placeholder="Customer Order Number" required />
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                <div class="form-input">
                                                    <label for="">Credit Period (Days)<span class="text-danger">*</span></label>
                                                    <input type="text" name="creditPeriod" class="form-control" id="inputCreditPeriod" placeholder="Credit Period " required />
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                <div class="form-input">
                                                    <label for="">Sales Person <span class="text-danger">*</span></label>
                                                    <select name="kamId" class="form-control" id="kamDropDown" required>
                                                        <option value="">Sales Person</option>
                                                        <?php
                                                        $funcList = $BranchSoObj->fetchKamDetails()['data'];
                                                        foreach ($funcList as $func) {
                                                        ?>
                                                            <option value="<?= $func['kamId'] ?>"><?= $func['kamName'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                <div class="form-input">
                                                    <label for="">Order For <span class="text-danger">*</span></label>
                                                    <select name="goodsType" class="form-control" id="goodsType" required>
                                                        <option value="">Select One</option>
                                                        <option value="both">Both</option>
                                                        <option value="material">Goods</option>
                                                        <option value="service">Services</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                <div class="form-input">
                                                    <label for="">Profile Center <span class="text-danger">*</span></label>
                                                    <select name="profitCenter" class="selct-vendor-dropdown" id="profitCenterDropDown" required>
                                                        <option value="">Profit Center</option>
                                                        <?php
                                                        $funcList = $BranchSoObj->fetchFunctionality()['data'];
                                                        foreach ($funcList as $func) {
                                                        ?>
                                                            <option value="<?= $func['functionalities_id'] ?>"><?= $func['functionalities_name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card items-select-table">
                            <div class="head-item-table">
                                <div class="advanced-serach">
                                    <div class="hamburger quickadd-hamburger">
                                        <div class="wrapper-action">
                                            <i class="fa fa-plus"></i>
                                        </div>
                                    </div>
                                    <div class="nav-action quick-add-input" id="quick-add-input">
                                        <div class="form-inline">
                                            <label for=""><span class="text-danger">*</span>Quick Add </label>
                                            <select id="itemsDropDown" class="form-control">
                                                <option value="">Goods Type</option>
                                                <option value="hello">hello</option>
                                                <option value="hello1">hello1</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <a class="btn btn-primary items-search-btn" data-bs-toggle="modal" data-bs-target="#exampleModal"> <i class="fa fa-search mr-2"></i>Advance Search</a> -->
                            <small class="py-2 px-1 rounded alert-dark specialDiscount" id="specialDiscount" style="display: none;">Special Discount</small>
                            <table class="table table-sales-order mt-0">
                                <thead>
                                    <tr>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>HSN Code</th>
                                        <th>Stock</th>
                                        <th>Qty</th>
                                        <th>Unit Price</th>
                                        <th>Base Amount</th>
                                        <th>Tax</th>
                                        <th>Total Tax</th>
                                        <th>Total Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <span id="spanItemsTable"></span>
                                <tbody id="itemsTable"></tbody>
                                <tbody>
                                    <tr>
                                        <td colspan="9" class="text-right p-2" style="border: none; background: none;"> </td>
                                        <td colspan="0" class="text-left p-2 font-weight-bold totalCal" style="border: none; background: none;">Sub <sup class="text-primary">[TOTAL]</sup></td>
                                        <input type="hidden" name="subTotal" value="0">
                                        <td id="grandSubTotalAmt" class="p-2" style="border: none; background: none;">0.00</th>
                                    </tr>
                                    <tr>
                                        <td colspan="9" class="text-right p-2" style="border: none; background: none;"> </td>
                                        <td colspan="0" class="text-left p-2 font-weight-bold totalCal" style="border: none; background: none;">TOTAL <sup class="text-danger">[DISCOUNT]</sup></td>
                                        <input type="hidden" name="totalDiscount" value="0">
                                        <td id="grandTotalDiscount" class="p-2" style="border: none; background: none;">0.00</td>
                                    </tr>
                                    <tr class="p-2">
                                        <td colspan="9" class="text-right p-2" style="border: none; background: none;"> </td>
                                        <td colspan="0" class="text-left p-2 font-weight-bold totalCal" style="border: none; background: none;">TOTAL <sup class="text-info">[TAX]</sup></td>
                                        <input type="hidden" name="taxAmount" value="0">
                                        <td id="grandTaxAmt" class="p-2" style="border: none; background: none;">0.00</td>
                                    </tr>
                                    <tr class="p-2">
                                        <td colspan="9" class="text-right p-2" style="border: none; background: none;"> </td>
                                        <td colspan="0" class="text-left p-2 font-weight-bold totalCal" style="border: none; background: none;">TOTAL <sup class="text-success">[VALUE]</sup></td>
                                        <input type="hidden" name="totalAmt" value="0">
                                        <td id="grandTotalAmt" class="p-2" style="border: none; background: none;">0.00</th>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="modal fade items-filter-modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Advanced Filter Search</h5>
                                        </div>
                                        <div class="modal-body">

                                            <div class="accordion-item filter-serch-accodion">
                                                <h2 class="accordion-header" id="flush-headingOne">
                                                    <button class="accordion-button collapsed btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                                        Advanced Search Filter
                                                    </button>
                                                    </button>
                                                </h2>
                                                <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                                <div class="card filter-search-card">
                                                                    <div class="card-body">
                                                                        <div class="serch-input">
                                                                            <input type="text" class="form-control" placeholder="search">
                                                                            <select name="" id="" class="form-control form-select filter-select">
                                                                                <option value="">search</option>
                                                                                <option value="">search</option>
                                                                                <option value="">search</option>
                                                                            </select>
                                                                            <input type="text" class="form-control" placeholder="search">
                                                                            <select name="" id="" class="form-control form-select filter-select">
                                                                                <option value="">search</option>
                                                                                <option value="">search</option>
                                                                                <option value="">search</option>
                                                                            </select>
                                                                            <input type="text" class="form-control" placeholder="search">
                                                                            <select name="" id="" class="form-control form-select filter-select">
                                                                                <option value="">search</option>
                                                                                <option value="">search</option>
                                                                                <option value="">search</option>
                                                                            </select>
                                                                        </div>
                                                                        <button class="btn btn-primary items-search-btn"><i class="fa fa-search mr-2"></i>Search</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row fob-section">
                            <div class="d-flex">
                                <input type="checkbox" id="fob">
                                <label for="fob" class="mb-0"> FOB
                                </label>
                            </div>
                        </div>
                        <div class="card p-3 items-select-table modal-add-row_537" id="otherCostCard" style="display: none;">
                            <div class="row othe-cost-infor">
                                <div class="col-lg-5 col-md-12 col-sm-12">
                                    <div class="form-input">
                                        <label for="">Service Description</label>
                                        <textarea class="form-control" placeholder="Description" name="otherCostDetails[12345][services]"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-12 col-sm-12">
                                    <div class="form-input">
                                        <label for="">Amount</label>
                                        <input step="0.01" type="number" class="form-control" placeholder="Amount" name="otherCostDetails[12345][amount]">
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6 col-sm-6">
                                    <div class="add-btn-plus">
                                        <a style="cursor: pointer" class="btn btn-primary" onclick="addOtherCost(537)">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <button type="submit" name="addNewSOFormSubmitBtn" id="soCreationBtn" class="btn btn-primary items-search-btn float-right">Submit</button>
                    </div>
                </div>
            </form>
        </section>
    </div>
<?php } else { ?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <div class="p-0 pt-1 my-2">
                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                    <h3 class="card-title">Manage Sales Order</h3>
                                    <a href="direct-create-invoice.php?sales_order_creation" class="btn btn-sm btn-primary btnstyle m-2 float-add-btn" style="line-height: 32px;"><i class="fa fa-plus"></i></a>
                                </li>
                            </ul>
                        </div>
                        <?php
                        $keywd = '';
                        if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
                            $keywd = $_REQUEST['keyword'];
                        } else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
                            $keywd = $_REQUEST['keyword2'];
                        } ?>
                        <div class="card card-tabs" style="border-radius: 20px;">
                            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return srch_frm();">
                                <div class="card-body">
                                    <div class="row filter-serach-row">
                                        <div class="col-lg-1 col-md-1 col-sm-12">
                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-lg-11 col-md-11 col-sm-12">
                                            <div class="row table-header-item">
                                                <div class="col-lg-11 col-md-11 col-sm-12">
                                                    <div class="filter-search">
                                                        <?php require_once('salesorder-filter-list.php'); ?>
                                                        <div class="section serach-input-section">
                                                            <input type="text" name="keyword" id="myInput" placeholder="" class="field form-control" value="<?php echo $keywd; ?>">
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
                                                <!-- <div class="col-lg-1 col-md-1 col-sm-1">
                          <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?customer-so-creation" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                        </div> -->
                                                <div class="col-lg-1 col-md-1 col-sm-1">
                                                    <a href="direct-create-invoice.php?sales_order_creation" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Filter Pending Sales Order</h5>

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
                                                        <!-- <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync "></i>Reset</a>-->
                                                        <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                                                            Search</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                            </form>
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
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                        <?php
                                        $cond = '';

                                        $sts = " AND `status` !='deleted'";
                                        if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                            $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                        }

                                        if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                            $cond .= " AND created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                        }

                                        if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                            $cond .= " AND `so_number` like '%" . $_REQUEST['keyword2'] . "%' OR `so_date` like '%" . $_REQUEST['keyword2'] . "%' OR `customer_po_no` like '%" . $_REQUEST['keyword2'] . "%'";
                                        } else {
                                            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                                $cond .= " AND `so_number` like '%" . $_REQUEST['keyword'] . "%'  OR `so_date` like '%" . $_REQUEST['keyword'] . "%' OR `customer_po_no` like '%" . $_REQUEST['keyword'] . "%'";
                                            }
                                        }

                                        $sql_list = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "'  AND branch_id='" . $branch_id . "'  AND location_id='" . $location_id . "' AND goodsType='project' AND approvalStatus=9 AND jobOrderApprovalStatus IN (14, 9, 11) " . $sts . "  ORDER BY so_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                                        $qry_list = mysqli_query($dbCon, $sql_list);
                                        $num_list = mysqli_num_rows($qry_list);

                                        $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "'  AND branch_id='" . $branch_id . "'  AND location_id='" . $location_id . "' AND goodsType='project' AND approvalStatus=9 AND jobOrderApprovalStatus IN (14, 9, 11) " . $sts . " ";
                                        $countQry = mysqli_query($dbCon, $countShow);
                                        $rowCount = mysqli_fetch_array($countQry);

                                        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE status='active' ORDER BY so_id DESC";
                                        $soListing = queryGet($ins);

                                        $count = $rowCount[0];
                                        $cnt = $GLOBALS['start'] + 1;
                                        $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_SALES_ORDER", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                        $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                        $settingsCheckbox = unserialize($settingsCh);
                                        $settingsCheckboxCount = count($settingsCheckbox);

                                        if ($num_list > 0) {
                                            $ss = 0;
                                        ?>
                                            <table class="table defaultDataTable table-hover tableDataBody">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <?php $ss++;
                                                        if (in_array($ss, $settingsCheckbox)) { ?>
                                                            <th <?= $ss; ?>>SO Number</th>
                                                        <?php }
                                                        $ss++;
                                                        if (in_array($ss, $settingsCheckbox)) { ?>
                                                            <th <?= $ss; ?>>Customer PO</th>
                                                        <?php }
                                                        $ss++;
                                                        if (in_array($ss, $settingsCheckbox)) { ?>
                                                            <th <?= $ss; ?>>Delivery Date</th>
                                                        <?php }
                                                        $ss++;
                                                        if (in_array($ss, $settingsCheckbox)) { ?>
                                                            <th <?= $ss; ?>>Customer Name</th>
                                                        <?php }
                                                        $ss++;
                                                        if (in_array($ss, $settingsCheckbox)) { ?>
                                                            <th <?= $ss; ?>>Type</th>
                                                        <?php }
                                                        $ss++;
                                                        if (in_array($ss, $settingsCheckbox)) { ?>
                                                            <th <?= $ss; ?>>JO Status</th>
                                                        <?php }
                                                        $ss++;
                                                        if (in_array($ss, $settingsCheckbox)) { ?>
                                                            <th <?= $ss; ?>>Status</th>
                                                            <?php
                                                            // }
                                                            // if (in_array($ss, $settingsCheckbox)) { 
                                                            ?>
                                                            <!--   <th >Status</th> -->
                                                        <?php }
                                                        $ss++;
                                                        if (in_array($ss, $settingsCheckbox)) { ?>
                                                            <th <?= $ss; ?>>Total Items</th>
                                                        <?php } ?>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="tableBody">
                                                    <?php
                                                    // console($BranchSoObj->fetchBranchSoListing()['data']);
                                                    foreach ($qry_list as $soKey => $oneSoList) {
                                                        $goodsType = "";
                                                        if ($oneSoList['goodsType'] == "material") {
                                                            $goodsType = '<span style="border-radius: 10px 3px;padding: 0px 5px;background: antiquewhite;box-shadow: 0 0 5px #b9b9b9;font-style: italic;">MATERIAL</span>';
                                                        } elseif ($oneSoList['goodsType'] == "service") {
                                                            $goodsType = '<span style="border-radius: 10px 3px;padding: 0px 5px;background: #d7f8fa;box-shadow: 0 0 5px #b9b9b9;font-style: italic;">SERVICE</span>';
                                                        } elseif ($oneSoList['goodsType'] == "both") {
                                                            $goodsType = '<span style="border-radius: 10px 3px;padding: 0px 5px;background: #d7d7fa;box-shadow: 0 0 5px #b9b9b9;font-style: italic;">BOTH</span>';
                                                        } elseif ($oneSoList['goodsType'] == "project") {
                                                            $goodsType = '<span style="border-radius: 10px 3px;padding: 0px 5px;background: #d7fad9;box-shadow: 0 0 5px #b9b9b9;font-style: italic;">PROJECT</span>';
                                                        }

                                                        $jobOrderApprovalStatus = '';
                                                        if ($oneSoList['jobOrderApprovalStatus'] == 14) {
                                                            $jobOrderApprovalStatus = '<div class="status-warning">PENDING</div>';
                                                        } else if ($oneSoList['jobOrderApprovalStatus'] == 11) {
                                                            $jobOrderApprovalStatus = '<div class="status">APPROVED</div>';
                                                        } else if ($oneSoList['jobOrderApprovalStatus'] == 9) {
                                                            $jobOrderApprovalStatus = '<div class="status-secondary">OPEN</div>';
                                                        }

                                                        $customerDetails = $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0];
                                                        $customerName = $customerDetails['trade_name'];
                                                        // console('$customerDetails');
                                                        // console($customerDetails);

                                                        if (fetchStatusMasterByCode($oneSoList['approvalStatus'])['data']['label'] == "open") {
                                                            $approvalStatus = '<div class="status">OPEN</div>';
                                                        } elseif (fetchStatusMasterByCode($oneSoList['approvalStatus'])['data']['label'] == "pending") {
                                                            $approvalStatus = '<div class="status-warning">PENDING</div>';
                                                        } elseif (fetchStatusMasterByCode($oneSoList['approvalStatus'])['data']['label'] == "exceptional") {
                                                            $approvalStatus = '<div class="status-warning">EXCEPTIONAL</div>';
                                                        } elseif (fetchStatusMasterByCode($oneSoList['approvalStatus'])['data']['label'] == "closed") {
                                                            $approvalStatus = '<div class="status-secondary">CLOSED</div>';
                                                        }
                                                        $sd = 0;
                                                    ?>
                                                        <tr class="tableOneRow">
                                                            <td><?= $cnt++ ?></td>
                                                            <?php $sd++;
                                                            if (in_array($sd, $settingsCheckbox)) { ?>
                                                                <td><?= $oneSoList['so_number'] ?></td>
                                                            <?php }
                                                            $sd++;
                                                            if (in_array($sd, $settingsCheckbox)) { ?>
                                                                <td><?= $oneSoList['customer_po_no'] ?></td>
                                                            <?php }
                                                            $sd++;
                                                            if (in_array($sd, $settingsCheckbox)) { ?>
                                                                <td><?= $oneSoList['delivery_date'] ?></td>
                                                            <?php }
                                                            $sd++;
                                                            if (in_array($sd, $settingsCheckbox)) { ?>
                                                                <td><?= $customerName ?></td>
                                                            <?php }
                                                            $sd++;
                                                            if (in_array($sd, $settingsCheckbox)) { ?>
                                                                <td><?= $goodsType ?></td>
                                                            <?php }
                                                            $sd++;
                                                            if (in_array($sd, $settingsCheckbox)) { ?>
                                                                <td><?= $jobOrderApprovalStatus ?></td>
                                                            <?php }
                                                            $sd++;
                                                            if (in_array($sd, $settingsCheckbox)) { ?>
                                                                <td><?= $approvalStatus ?></td>
                                                                <?php
                                                                // } $sd++;
                                                                // if (in_array($sd, $settingsCheckbox)) {
                                                                ?>
                                                                <!-- <td><?= $oneSoList['soStatus'] ?></td> -->
                                                            <?php }
                                                            $sd++;
                                                            if (in_array($sd, $settingsCheckbox)) { ?>
                                                                <td><?= $oneSoList['totalItems'] ?></td>
                                                            <?php } ?>
                                                            <td>
                                                                <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $oneSoList['so_number'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                                                <form action="" method="POST">
                                                                    <input type="hidden" name="soDetails[soId]" value="<?= $oneSoList['so_id'] ?>">
                                                                    <input type="hidden" name="soDetails[so_number]" value="<?= $oneSoList['so_number'] ?>">
                                                                    <div class="modal fade right customer-modal classic-view-modal" id="fluidModalRightSuccessDemo_<?= $oneSoList['so_number'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                                        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                                            <!--Content-->
                                                                            <div class="modal-content">
                                                                                <!--Header-->
                                                                                <div class="modal-header">

                                                                                    <div class="so-header">

                                                                                        <div class="icon-user-img">

                                                                                            <i class="fa fa-user"></i>

                                                                                        </div>

                                                                                        <div class="icon-user-text">

                                                                                            <p class="text-sm text-white mt-3"><?= $customerDetails['trade_name'] ?></p>

                                                                                            <div class="d-flex so_number-item mt-1 mb-2">

                                                                                                <p class="heading lead text-xs"><?= $oneSoList['so_number'] ?></p>

                                                                                                <div class="item-count text-xs">
                                                                                                    <p class="round-item-count text-xs"><?= $oneSoList['totalItems'] ?></p>
                                                                                                    <p>Items</p>
                                                                                                </div>

                                                                                            </div>

                                                                                        </div>

                                                                                    </div>
                                                                                    <?php $grandTotalAmt = 0;
                                                                                    $itemDetails = $BranchSoObj->fetchBranchSoItems($oneSoList['so_id'])['data'];
                                                                                    foreach ($itemDetails as $itemKey => $oneItem) {
                                                                                        $boqDetailObj = $boqControllerObj->getBoqDetails($oneItem['inventory_item_id']);

                                                                                        $baseUnitMeasure = $ItemsObj->getBaseUnitMeasureById($oneItem['uom']);
                                                                                        $uomName = $baseUnitMeasure['data']['uomName'];

                                                                                        $subTotalAmt = ($oneItem['unitPrice'] * $oneItem['completion_value']);
                                                                                        $taxAmount = ($subTotalAmt * $oneItem['tax']) / 100;
                                                                                        $totalDiscount = ($subTotalAmt * $oneItem['totalDiscount']) / 100;
                                                                                        $totalAmt = $subTotalAmt - $totalDiscount + $taxAmount;
                                                                                        $grandTotalAmt += $totalAmt;
                                                                                    }
                                                                                    ?>
                                                                                    <div class="customer-head-info mb-0 mt-2">
                                                                                        <div class="customer-name-code">
                                                                                            <h2 class="text-lg mb-0"><small><?= $oneSoList['currency_name'] ?></small><?php echo number_format($grandTotalAmt, 2); ?></h2>
                                                                                            <p class="text-xs">(<?= number_to_words_indian_rupees($grandTotalAmt) ?>)</p>
                                                                                            <!-- <p class="heading lead"><?= $oneSoList['so_number'] ?></p>
                                                                    <p>Cust CO/REF :&nbsp;<?= $oneSoList['customer_po_no'] ?></p> -->
                                                                                        </div>
                                                                                        <!-- <div class="customer-image">
                                                                    <div class="name-item-count">
                                                                        <h5 style="font-size: .8rem;"><?= $customerDetails['trade_name'] ?></h5>
                                                                        <span>
                                                                        <div class="round-item-count"><?= $oneSoList['totalItems'] ?></div> Items
                                                                        </span>
                                                                    </div>
                                                                    <i class="fa fa-user"></i>
                                                                    </div> -->
                                                                                    </div>

                                                                                    <div class="display-flex-space-between mt-2 mb-2">

                                                                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                                            <li class="nav-item">
                                                                                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= $oneSoList['so_number'] ?>" role="tab" aria-controls="home" aria-selected="true">Item Info</a>
                                                                                            </li>
                                                                                            <!-- <li class="nav-item">
                                                                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile<?= $oneSoList['so_number'] ?>" role="tab" aria-controls="profile" aria-selected="false">Customer Info</a>
                                                                        </li> -->
                                                                                            <!-- <li class="nav-item">
                                                                            <a class="nav-link" target="_blank" href="branch-so-invoice-view.php?so_id=<?= base64_encode($oneSoList['so_id']) ?>">Classic View</a>
                                                                        </li> -->
                                                                                            <li class="nav-item">
                                                                                                <a class="nav-link" id="classic-view-tab" data-toggle="tab" href="#classic-view<?= $oneSoList['so_number'] ?>" role="tab" aria-controls="classic-view" aria-selected="false">Classic View</a>
                                                                                            </li>

                                                                                            <!-- -------------------Audit History Button Start------------------------- -->
                                                                                            <li class="nav-item">
                                                                                                <a class="nav-link auditTrail" id="history-tab<?= $oneSoList['so_id'] ?>" data-toggle="tab" data-ccode="<?= $oneSoList['so_number'] ?>" href="#history<?= $oneSoList['so_id'] ?>" role="tab" aria-controls="history<?= $oneSoList['so_id'] ?>" aria-selected="false"><i class="fa fa-history mr-2"></i>Trail</a>
                                                                                            </li>
                                                                                            <!-- -------------------Audit History Button End------------------------- -->
                                                                                        </ul>


                                                                                    </div>

                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <div class="tab-content pt-0" id="myTabContent">
                                                                                        <div class="tab-pane fade show active" id="home<?= $oneSoList['so_number'] ?>" role="tabpanel" aria-labelledby="home-tab">

                                                                                            <!-- <form action="" method="POST"> -->
                                                                                            <div class="hamburger">
                                                                                                <div class="wrapper-action">
                                                                                                    <i class="fa fa-bell fa-2x"></i>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="nav-action" id="settings">

                                                                                                <a title="Mail the customer" href="#" name="vendorEditBtn">
                                                                                                    <i class="fa fa-envelope"></i>
                                                                                                </a>
                                                                                            </div>
                                                                                            <div class="nav-action" id="thumb">
                                                                                                <a title="Chat the customer" href="#" name="vendorEditBtn">
                                                                                                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                                                                                                </a>
                                                                                            </div>
                                                                                            <div class="nav-action" id="create">
                                                                                                <a title="Call the customer" href="#" name="vendorEditBtn">
                                                                                                    <i class="fa fa-phone"></i>
                                                                                                </a>
                                                                                            </div>
                                                                                            <!-- </form> -->
                                                                                            <!-- <form action="" method="POST">
                                                                    <div class="hamburger">
                                                                    <div class="wrapper-action">
                                                                        <i class="fa fa-cog fa-2x"></i>
                                                                    </div>
                                                                    </div>
                                                                    <div class="nav-action" id="settings">
                                                                    <?php if ($oneSoList['approvalStatus'] == 9) { ?>
                                                                        <a title="Delivery Creation" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($oneSoList['so_number']) ?>" name="vendorEditBtn">
                                                                        <i class="fa fa-box"></i>
                                                                        </a>
                                                                    <?php } else { ?>
                                                                        <a title="Can't access 'Delivery Creation'" href="#" name="vendorEditBtn">
                                                                        <i class="fa fa-box"></i>
                                                                        </a>
                                                                    <?php } ?>
                                                                    </div>

                                                                    <div class="nav-action" id="thumb">
                                                                    <a title="Notify Me" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($oneSoList['so_number']) ?>" name="vendorEditBtn">
                                                                        <i class="fa fa-bell"></i>
                                                                    </a>
                                                                    </div>
                                                                    <div class="nav-action" id="create">
                                                                    <a title="Edit" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($oneSoList['so_number']) ?>" name="vendorEditBtn">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                    </div>
                                                                    <div class="nav-action trash" id="share">
                                                                    <a title="Delete" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($oneSoList['so_number']) ?>" name="vendorEditBtn">
                                                                        <i class="fa fa-trash"></i>
                                                                    </a>
                                                                    </div>
                                                                    </form> -->


                                                                                            <!-- action btn  -->
                                                                                            <div class="action-btns display-flex-gap create-delivery-btn-sales" id="action-navbar">
                                                                                                <!-- <a href="#" class="btn btn-sm" title="Delete SO"><i class="fa fa-trash po-list-icon"></i></a> -->
                                                                                                <!-- action btn  -->
                                                                                                <?php
                                                                                                if ($oneSoList['approvalStatus'] == 9 || $oneSoList['approvalStatus'] == 11) {
                                                                                                    if ($oneSoList['goodsType'] == 'material' || $oneSoList['goodsType'] == 'both') {
                                                                                                ?>
                                                                                                        <div class="d-flex btnHideShow<?= $oneSoList['so_id']; ?>">
                                                                                                            <a href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($oneSoList['so_number']) ?>" class="btn-primary text-xs text-light deliveryCreationBtn pl-2 pr-2"><i class="fa fa-plus mr-2"></i>Create Delivery</a>
                                                                                                            <a title="Create Invoice" href="direct-create-invoice.php?so_to_invoice=<?= base64_encode($oneSoList['so_id']) ?>" class="btn-primary text-xs text-light deliveryCreationBtn pl-2 pr-2 ml-2"><i class="fa fa-plus mr-2"></i>Create Invoice</a>
                                                                                                        </div>
                                                                                                    <?php
                                                                                                    } else if ($oneSoList['goodsType'] == 'project') { ?>
                                                                                                        <button type="submit" name="jobOrderApprovalSubmitBtn" class="btn btn-success approvalTab float-right" onclick="return confirm('Are you sure?')" style="cursor: pointer; margin-top: 0px;">
                                                                                                            <i class="fa fa-check mr-2" style="border-radius: 50%; background: #fff; padding: 5px; color: #198754;"></i>
                                                                                                            Job Done
                                                                                                        </button>
                                                                                                        <!-- <a class="btn btn-success approvalTab float-right" style="cursor: pointer; margin-top: 0px;" id="approvalTab_<?= $soKey ?>_<?= $oneSoList['so_id'] ?>">
                                                                                                    <i class="fa fa-check mr-2" style="border-radius: 50%; background: #fff; padding: 5px; color: #198754;"></i>
                                                                                                    Approve
                                                                                                </a> -->
                                                                                                        <!-- <a title="Approval button" href="direct-create-invoice.php?joborder_to_invoice=<?= base64_encode($oneSoList['so_id']) ?>" class="btn-primary text-xs text-light deliveryCreationBtn pl-2 pr-2 ml-2"><i class="fa fa-plus mr-2"></i>
                                                                                                    <i class="fa fa-check mr-2" style="border-radius: 50%; background: #fff; padding: 5px; color: #198754;"></i>
                                                                                                    Approve
                                                                                                </a> -->
                                                                                                    <?php } else {
                                                                                                    ?>
                                                                                                        <a title="Create Invoice" href="direct-create-invoice.php?so_to_invoice=<?= base64_encode($oneSoList['so_id']) ?>" class="btn-primary text-xs text-light deliveryCreationBtn pl-2 pr-2 ml-2"><i class="fa fa-plus mr-2"></i>Create Invoice</a>
                                                                                                    <?php
                                                                                                    }
                                                                                                } else if ($oneSoList['approvalStatus'] == 12 || $oneSoList['approvalStatus'] == 14) { ?>
                                                                                                    <a title="Delivery Creation" href="#" class="btn-warning text-xs text-dark deliveryCreationBtn pl-2 pr-2"><i class="fa fa-clock mr-2"></i> Pending For Approval</a>
                                                                                                <?php } else if ($oneSoList['approvalStatus'] == 10) { ?>
                                                                                                    <a title="Delivery Creation" href="#" class="btn-secondary text-xs text-light deliveryCreationBtn pl-2 pr-2"><i class="fa fa-check-circle mr-2"></i> Delivery Closed</a>
                                                                                                <?php } ?>
                                                                                                <!-- <a href="#" class="btn btn-sm" title="Edit SO"><i class="fa fa-edit po-list-icon"></i></a> -->

                                                                                            </div>
                                                                                            <?php
                                                                                            $customerDetails = $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0];
                                                                                            // console($customerDetails);
                                                                                            $customerAddressDetails = $BranchSoObj->fetchCustomerAddressDetails($customerDetails['customer_id'])['data'][0];
                                                                                            ?>
                                                                                            <div class="item-detail-section">
                                                                                                <!-- <h6>Items Details</h6> -->
                                                                                                <?php
                                                                                                // $itemDetails = $BranchSoObj->fetchBranchSoItems($oneSoList['so_id'])['data'];
                                                                                                // console($itemDetails);
                                                                                                $flagForBtn = 0;
                                                                                                foreach ($itemDetails as $itemKey => $oneItem) {
                                                                                                    $boqDetailObj = $boqControllerObj->getBoqDetails($oneItem['inventory_item_id']);

                                                                                                    $baseUnitMeasure = $ItemsObj->getBaseUnitMeasureById($oneItem['uom']);
                                                                                                    $uomName = $baseUnitMeasure['data']['uomName'];

                                                                                                    $deliveryScheduleObj = $BranchSoObj->fetchBranchSoItemsDeliverySchedule($oneItem['so_item_id']);
                                                                                                    $deliverySchedule = $deliveryScheduleObj['data'];
                                                                                                    if (count($deliverySchedule) > 0) {
                                                                                                        $flagForBtn++;
                                                                                                    }
                                                                                                    $subTotalAmt = ($oneItem['unitPrice'] * $oneItem['completion_value']);
                                                                                                    $taxAmount = ($subTotalAmt * $oneItem['tax']) / 100;
                                                                                                    $totalDiscount = ($subTotalAmt * $oneItem['totalDiscount']) / 100;
                                                                                                    $totalAmt = $subTotalAmt - $totalDiscount + $taxAmount;
                                                                                                ?>
                                                                                                    <input type="hidden" name="modalListItem[<?= $itemKey ?>][so_item_id]" class="soItemId" id="soItemId__<?= $soKey ?>_<?= $itemKey ?>" value="<?= $oneItem['so_item_id'] ?>">
                                                                                                    <input type="hidden" name="modalListItem[<?= $itemKey ?>][inventory_item_id]" class="fetchItemCode" id="fetchItemCode__<?= $soKey ?>_<?= $itemKey ?>" value="<?= $oneItem['inventory_item_id'] ?>">
                                                                                                    <input type="hidden" name="modalListItem[<?= $itemKey ?>][itemCode]" class="fetchItemCode" id="fetchItemCode__<?= $soKey ?>_<?= $itemKey ?>" value="<?= $oneItem['itemCode'] ?>">
                                                                                                    <input type="hidden" name="modalListItem[<?= $itemKey ?>][invStatus]" class="invStatus" id="invStatus__<?= $soKey ?>_<?= $itemKey ?>" value="<?= $oneItem['invStatus'] ?>">

                                                                                                    <div class="card">
                                                                                                        <div class="card-body">
                                                                                                            <div class="row">
                                                                                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                                                    <div class="dFlex">
                                                                                                                        <div class="left-section">
                                                                                                                            <div class="icon-img">
                                                                                                                                <i class="fa fa-box"></i>
                                                                                                                            </div>
                                                                                                                            <div class="code-des">
                                                                                                                                <h4><?= $oneItem['itemCode'] ?></h4>
                                                                                                                                <p><?= $oneItem['itemName'] ?></p>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <div class="text-xs">
                                                                                                                            <input step="any" type="hidden" name="modalListItem[<?= $itemKey ?>][itemQty]" class="form-control text-right itemQty" id="itemQty_<?= $soKey ?>_<?= $itemKey ?>" value="<?= $oneItem['qty'] ?>">
                                                                                                                            <input step="any" type="hidden" name="modalListItem[<?= $itemKey ?>][completion_value]" class="form-control text-right completion_value" id="completion_value_<?= $soKey ?>_<?= $itemKey ?>" value="<?= $oneItem['completion_value'] ?>">
                                                                                                                            <input step="any" type="hidden" name="modalListItem[<?= $itemKey ?>][remainingQtyHidden]" class="form-control text-right remainingQtyHidden" id="remainingQtyHidden_<?= $soKey ?>_<?= $itemKey ?>" value="<?= $oneItem['remainingQty'] ?>">
                                                                                                                            <input step="any" type="number" name="modalListItem[<?= $itemKey ?>][completionPercentage]" class="form-control text-right completionPercentage" id="completionPercentage_<?= $soKey ?>_<?= $itemKey ?>" placeholder="<?php if ($oneItem['remainingQty'] == 0) {
                                                                                                                                                                                                                                                                                                                                                        echo "Order Closed";
                                                                                                                                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                                                                                                                                        echo "Enter completion value";
                                                                                                                                                                                                                                                                                                                                                    } ?>" <?php if ($oneItem['remainingQty'] == 0) {
                                                                                                                                                                                                                                                                                                                                                                echo "disabled";
                                                                                                                                                                                                                                                                                                                                                            } ?>>
                                                                                                                            <span style="display: none;" class="text-danger completionPercentageMsg" id="completionPercentageMsg_<?= $soKey ?>_<?= $itemKey ?>">Please enter correct value</span>
                                                                                                                            <p class="text-right">Qty <span class="completion_value" id="completion_value_<?= $soKey ?>_<?= $itemKey ?>"><?= $oneItem['completion_value'] ?></span> <?= $uomName ?></p>
                                                                                                                            <p class="text-right">Remaining <span class="remainingQtySpan" id="remainingQtySpan_<?= $soKey ?>_<?= $itemKey ?>"><?= $oneItem['remainingQty'] ?></span> of <span class="itemQtyCard" id="itemQtyCard_<?= $soKey ?>_<?= $itemKey ?>"><?= $oneItem['qty'] ?></span> <?= $uomName ?></p>
                                                                                                                            <p class="float-right"><span class="rupee-symbol"><small><?= $oneSoList['currency_name'] ?></small></span><?= number_format($oneItem['unitPrice'], 2) ?></p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                                                    <div class="right-section">
                                                                                                                        <!-- <p><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $oneItem['unitPrice'] * $oneItem['qty'] ?></p> -->
                                                                                                                        <div class="dFlex p-0">
                                                                                                                            <span class="mr-3 text-xs font-bold mb-2">Sub Total</span>
                                                                                                                            <span class="rupee-symbol text-xs font-bold mb-2"><small><?= $oneSoList['currency_name'] ?></small><?= number_format($subTotalAmt, 2) ?>
                                                                                                                        </div>
                                                                                                                        <?php if ($oneItem['totalDiscount'] > 0) { ?>
                                                                                                                            <div class="discount dFlex p-0">
                                                                                                                                <span class="mr-3 text-xs font-bold mb-2">Total Discount </span>
                                                                                                                                <div>
                                                                                                                                    <span class="rupee-symbol text-xs font-bold mb-2"><small><?= $oneSoList['currency_name'] ?></small><?= $totalDiscount ?></span>
                                                                                                                                    <small class="text-xs">(-<?= $oneItem['totalDiscount'] ?>%)</small>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        <?php } ?>
                                                                                                                        <div class="dFlex p-0">
                                                                                                                            <span class="mr-3 text-xs font-bold mb-2">Total Tax </span>
                                                                                                                            <div>
                                                                                                                                <span class="rupee-symbol text-xs font-bold mb-2"><small><?= $oneSoList['currency_name'] ?></small><?= $taxAmount ?></span>
                                                                                                                                <span class="text-xs">(<?= $oneItem['tax'] ?>%)</span>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <div style="border-top: 1px solid;" class="font-weight-bold dFlex">
                                                                                                                            <span class="text-xs font-bold">Total Amount</span>
                                                                                                                            <span class="rupee-symbol text-xs font-bold mb-2"><small><?= $oneSoList['currency_name'] ?></small><?= number_format($totalAmt, 2) ?>
                                                                                                                        </div>
                                                                                                                        <!-- <div class="discount">
                                                                            <p><?= $oneItem['itemTotalDiscount'] ?></p>
                                                                            (-<?= $oneItem['totalDiscount'] ?>%)
                                                                        </div> -->
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <hr>
                                                                                                            <?php
                                                                                                            $deliverySchedule = $BranchSoObj->fetchBranchSoItemsDeliverySchedule2($oneItem['so_item_id'])['data'];
                                                                                                            // console($deliverySchedule);

                                                                                                            foreach ($deliverySchedule as $dSchedule) {
                                                                                                            ?>
                                                                                                                <div class="row">
                                                                                                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                                                                                                        <div class="left-section">
                                                                                                                            <div class="icon-img">
                                                                                                                                <i class="fa fa-clock"></i>
                                                                                                                            </div>
                                                                                                                            <div class="date-time-parent">
                                                                                                                                <div class="date-time mb-0">
                                                                                                                                    <div class="code-des">
                                                                                                                                        <h4>
                                                                                                                                            <?php
                                                                                                                                            // $timestamp = $dSchedule['delivery_date'];
                                                                                                                                            // $dt1 = date_format($timestamp, "d");
                                                                                                                                            echo $dSchedule['delivery_date'];
                                                                                                                                            ?>
                                                                                                                                            <small class="text-secondary text-capitalize">(<?= $dSchedule['deliveryStatus'] ?>)</small>
                                                                                                                                            <?php
                                                                                                                                            // $date=date_create($dSchedule['delivery_date']);
                                                                                                                                            // echo date_format($date,"Y/F/d");
                                                                                                                                            ?>
                                                                                                                                        </h4>
                                                                                                                                    </div>
                                                                                                                                    <p>
                                                                                                                                        <?php
                                                                                                                                        // echo $timestamp = $dSchedule['delivery_date'];
                                                                                                                                        // $dt2 = date("Y", strtotime($timestamp));
                                                                                                                                        // echo $dt2;
                                                                                                                                        ?>
                                                                                                                                    </p>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                        <div class="right-section unit">
                                                                                                                            <div class="dropdown text-right">
                                                                                                                                <button class="btn btn-secondary dropdown-toggle date-time-item" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                                                                    <?= $dSchedule['qty'] ?> <?= $uomName ?>
                                                                                                                                </button>
                                                                                                                            </div>
                                                                                                                        </div>

                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            <?php } ?>
                                                                                                            <!-- start boq details -->
                                                                                                            <div class="col-12 mt-2 p-0" style="border: 1px solid #dfdfdf;padding: 0px 10px !important;box-shadow: 0 0 15px #cbcbcb;border-radius: 5px; margin-top: 25px !important;">
                                                                                                                <div class="p-0 pt-1 my-2">
                                                                                                                    <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                                                                                                        <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                                                                                                            <h3 class="card-title">View BOQ</h3>
                                                                                                                            <span><?= $boqDetailObj["data"]["boq_data"]["itemCode"] ?? "" ?> - <?= $boqDetailObj["data"]["boq_data"]["itemName"] ?? "" ?></span>
                                                                                                                            <!-- <div style="display: inline-flex;">
                                                                                                                                    <form action="" method="post">
                                                                                                                                    <input type="hidden" name="releaseboq" value="<?= base64_encode($boqDetails["boqId"]) ?>">
                                                                                                                                    <button type="submit" name="releaseboqFrmSbmit" class="btn btn-sm btn-primary">Update Price</button>
                                                                                                                                    </form>
                                                                                                                                    <a href="?editboq=<?= $_GET["view"] ?? '' ?>" class="btn btn-sm btn-primary ml-2">Change Items</a>
                                                                                                                                    </div> -->
                                                                                                                        </li>
                                                                                                                    </ul>
                                                                                                                </div>
                                                                                                                <div class="">
                                                                                                                    <div class="card">
                                                                                                                        <div class="card-body">
                                                                                                                            <p class="text-left m-0 pl-3 pb-2 font-bold">Service Items</p>
                                                                                                                            <table class="table">
                                                                                                                                <thead>
                                                                                                                                    <tr>
                                                                                                                                        <th class="borderNone">Item Code</th>
                                                                                                                                        <th class="borderNone">Item Title</th>
                                                                                                                                        <th class="borderNone">Consumption</th>
                                                                                                                                        <th class="borderNone">Extra(%)</th>
                                                                                                                                        <th class="borderNone">UOM</th>
                                                                                                                                        <th class="borderNone">Item Rate</th>
                                                                                                                                        <th class="borderNone">Amount</th>
                                                                                                                                        <th class="borderNone">Remarks</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>
                                                                                                                                    <?php
                                                                                                                                    foreach ($boqDetailObj["data"]["boq_service_data"] ?? [] as $boqOneItem) {
                                                                                                                                    ?>
                                                                                                                                        <tr>
                                                                                                                                            <td><?= $boqOneItem["itemCode"] ?? "" ?></td>
                                                                                                                                            <td><?= $boqOneItem["itemName"] ?? "" ?></td>
                                                                                                                                            <td><?= $boqOneItem["consumption"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["extra"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["uom"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["rate"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["amount"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["remarks"] ?></td>
                                                                                                                                        </tr>
                                                                                                                                    <?php
                                                                                                                                    }
                                                                                                                                    ?>
                                                                                                                                </tbody>
                                                                                                                            </table>
                                                                                                                        </div>
                                                                                                                    </div>

                                                                                                                    <div class="card">
                                                                                                                        <div class="card-body">
                                                                                                                            <p class="text-left m-0 pl-3 pb-2 font-bold">Goods Items</p>
                                                                                                                            <table class="table">
                                                                                                                                <thead>
                                                                                                                                    <tr>
                                                                                                                                        <th class="borderNone">Item Code</th>
                                                                                                                                        <th class="borderNone">Item Title</th>
                                                                                                                                        <th class="borderNone">Consumption</th>
                                                                                                                                        <th class="borderNone">Extra(%)</th>
                                                                                                                                        <th class="borderNone">UOM</th>
                                                                                                                                        <th class="borderNone">Item Rate</th>
                                                                                                                                        <th class="borderNone">Amount</th>
                                                                                                                                        <th class="borderNone">Remarks</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>
                                                                                                                                    <?php
                                                                                                                                    foreach ($boqDetailObj["data"]["boq_material_data"] ?? [] as $boqOneItem) {
                                                                                                                                    ?>
                                                                                                                                        <tr>
                                                                                                                                            <td><?= $boqOneItem["itemCode"] ?? "" ?></td>
                                                                                                                                            <td><?= $boqOneItem["itemName"] ?? "" ?></td>
                                                                                                                                            <td><?= $boqOneItem["consumption"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["extra"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["uom"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["rate"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["amount"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["remarks"] ?></td>
                                                                                                                                        </tr>
                                                                                                                                    <?php
                                                                                                                                    }
                                                                                                                                    ?>
                                                                                                                                </tbody>
                                                                                                                            </table>
                                                                                                                        </div>
                                                                                                                    </div>

                                                                                                                    <div class="card">
                                                                                                                        <div class="card-body">
                                                                                                                            <p class="text-left m-0 pl-3 pb-2 font-bold">Activities</p>
                                                                                                                            <p class="text-left m-0 pl-3 pb-2 font-bold">Hourly Deployment</p>
                                                                                                                            <table class="table mb-3">
                                                                                                                                <thead>
                                                                                                                                    <tr>
                                                                                                                                        <th class="borderNone">#</th>
                                                                                                                                        <th class="borderNone">Cost center</th>
                                                                                                                                        <th class="borderNone">Code</th>
                                                                                                                                        <th class="borderNone">Head Name</th>
                                                                                                                                        <th class="borderNone">Consumption</th>
                                                                                                                                        <th class="borderNone">Extra(%)</th>
                                                                                                                                        <th class="borderNone">UOM</th>
                                                                                                                                        <th class="borderNone">Rate</th>
                                                                                                                                        <th class="borderNone">Amount</th>
                                                                                                                                        <th class="borderNone">Remarks</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>
                                                                                                                                    <?php
                                                                                                                                    $sl = 0;
                                                                                                                                    foreach ($boqDetailObj["data"]["boq_hd_data"] ?? [] as $boqOneItem) {
                                                                                                                                    ?>
                                                                                                                                        <tr>
                                                                                                                                            <td><?= $sl += 1 ?></td>
                                                                                                                                            <td><?= $boqOneItem["CostCenter_desc"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["CostCenter_code"] ?></td>
                                                                                                                                            <td><?= strtoupper($boqOneItem["head_type"]) ?></td>
                                                                                                                                            <td><?= $boqOneItem["consumption"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["extra"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["uom"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["rate"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["amount"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["remarks"] ?></td>
                                                                                                                                        </tr>
                                                                                                                                    <?php
                                                                                                                                    }
                                                                                                                                    ?>
                                                                                                                                </tbody>
                                                                                                                            </table>

                                                                                                                            <p class="text-left m-0 pl-3 pb-2 font-bold">Other Heads</p>
                                                                                                                            <table class="table mb-3">
                                                                                                                                <thead>
                                                                                                                                    <tr>
                                                                                                                                        <th class="borderNone">#</th>
                                                                                                                                        <th class="borderNone">Cost center</th>
                                                                                                                                        <th class="borderNone">Code</th>
                                                                                                                                        <th class="borderNone">Other Head</th>
                                                                                                                                        <th class="borderNone">Consumption</th>
                                                                                                                                        <th class="borderNone">Extra(%)</th>
                                                                                                                                        <th class="borderNone">UOM</th>
                                                                                                                                        <th class="borderNone">Rate</th>
                                                                                                                                        <th class="borderNone">Amount</th>
                                                                                                                                        <th class="borderNone">Remarks</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>
                                                                                                                                    <?php
                                                                                                                                    $sl = 0;
                                                                                                                                    foreach ($boqDetailObj["data"]["boq_other_head_data"] ?? [] as $boqOneItem) {
                                                                                                                                    ?>
                                                                                                                                        <tr>
                                                                                                                                            <td><?= $sl += 1 ?></td>
                                                                                                                                            <td><?= $boqOneItem["CostCenter_desc"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["CostCenter_code"] ?></td>
                                                                                                                                            <td><?= ucfirst($boqOneItem["head_name"] ?? "") ?></td>
                                                                                                                                            <td><?= $boqOneItem["consumption"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["extra"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["uom"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["rate"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["amount"] ?></td>
                                                                                                                                            <td><?= $boqOneItem["remarks"] ?></td>
                                                                                                                                        </tr>
                                                                                                                                    <?php
                                                                                                                                    }
                                                                                                                                    ?>
                                                                                                                                </tbody>
                                                                                                                            </table>
                                                                                                                        </div>


                                                                                                                    </div>

                                                                                                                    <?php
                                                                                                                    if ($boqDetails["boqProgressStatus"] == "COGM") {
                                                                                                                    ?>

                                                                                                                        <script>
                                                                                                                            $(document).ready(function() {
                                                                                                                                function addOtherAddonsFormItem(rowNo = 0) {
                                                                                                                                    $("#otherAddonsForm").append(`
                                                                    <tr id="otherAddonItemTr_${rowNo}">
                                                                        <td>
                                                                            <input class="form-control mt-2 mb-2" type="text" name="boqOtherAddonItemName[]" id="boqOtherAddonItemName_${rowNo}" placeholder="Item Name" required />
                                                                        </td>
                                                                        <td>
                                                                            <select name="boqOtherAddonItemGl[]" id="boqOtherAddonItemGl_${rowNo}" class="form-control boqOtherAddonItemGlDropDown" required>
                                                                                <option value="" data-row=""> -- Select Gl Code -- </option>
                                                                                <?php
                                                                                                                        foreach ($coaObj["data"] as $itemObj) {
                                                                                                                            echo '<option value="' . $itemObj["id"] . '">' . $itemObj["gl_code"] . ' - ' . $itemObj["gl_label"] . '</option>';
                                                                                                                        }
                                                                                ?>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input step="0.01" class="form-control mt-2 mb-2" type="number" name="boqOtherAddonItemPrice[]" id="boqOtherAddonItemPrice_${rowNo}" placeholder="Item Price" required />
                                                                        </td>
                                                                        <td>
                                                                            <input class="form-control mt-2 mb-2" type="text" name="boqOtherAddonItemRemarks[]" id="boqOtherAddonItemRemarks_${rowNo}" placeholder="Item remarks" />
                                                                        </td>
                                                                        <td>
                                                                            ${rowNo==0?`<i class="fa fa-plus bg-success rounded p-1 mt-2 addOtherAddonItemBtn" style="cursor: pointer;"></i>`:`<i class="fa fa-minus bg-danger rounded p-1 mt-2 removeOtherAddonItemBtn" style="cursor: pointer;"></i>`}
                                                                        </td>
                                                                    </tr>`);

                                                                                                                                    $(`#boqOtherAddonItemGl_${rowNo}`).select2();
                                                                                                                                }

                                                                                                                                addOtherAddonsFormItem(rowNo = 0);
                                                                                                                                // adding other addon items to boq list
                                                                                                                                var otherAddonItemsRowNo = 0;
                                                                                                                                $(document).on("click", ".addOtherAddonItemBtn", function() {
                                                                                                                                    addOtherAddonsFormItem(otherAddonItemsRowNo += 1);
                                                                                                                                });

                                                                                                                                // removing boq good items, activity and others from boq list
                                                                                                                                $(document).on("click", ".removeOtherAddonItemBtn", function() {
                                                                                                                                    let elm = $(this).parent().parent().remove();
                                                                                                                                });
                                                                                                                            });
                                                                                                                        </script>
                                                                                                                    <?php
                                                                                                                    } elseif ($boqDetails["boqProgressStatus"] == "COGS") {
                                                                                                                    ?>
                                                                                                                        <div class="card">
                                                                                                                            <div class="card-body">
                                                                                                                                <p class="text-left m-0 pl-3 pb-2 font-bold">COGS Items </p>
                                                                                                                                <table class="table mb-3">
                                                                                                                                    <thead>
                                                                                                                                        <tr>
                                                                                                                                            <th class="borderNone">Name</th>
                                                                                                                                            <th class="borderNone">Gl Code</th>
                                                                                                                                            <th class="borderNone">Amount</th>
                                                                                                                                            <th class="borderNone">Remarks</th>
                                                                                                                                        </tr>
                                                                                                                                    </thead>
                                                                                                                                    <tbody>
                                                                                                                                        <?php
                                                                                                                                        foreach ($boqItemsList as $boqOneItem) {
                                                                                                                                            if ($boqOneItem["boqItemType"] == "othersCogs") {
                                                                                                                                                // goods other item list
                                                                                                                                        ?>
                                                                                                                                                <tr>
                                                                                                                                                    <td><?= $boqOneItem["othersItem"] ?></td>
                                                                                                                                                    <td><?= $boqOneItem["itemGl"] ?></td>
                                                                                                                                                    <td><?= $boqOneItem["amount"] ?></td>
                                                                                                                                                    <td><?= $boqOneItem["remarks"] ?></td>
                                                                                                                                                </tr>
                                                                                                                                        <?php
                                                                                                                                            }
                                                                                                                                        }
                                                                                                                                        ?>
                                                                                                                                    </tbody>
                                                                                                                                </table>
                                                                                                                            </div>
                                                                                                                        </div>

                                                                                                                        <div class="card">
                                                                                                                            <div class="card-body">
                                                                                                                                <p class="text-left pl-3 pb-2 font-bold">Discount & Margins</p>
                                                                                                                                <table class="table">
                                                                                                                                    <thead>
                                                                                                                                        <tr>
                                                                                                                                            <th class="borderNone">Discount</th>
                                                                                                                                            <th class="boqMargin">Margin Amount</th>
                                                                                                                                        </tr>
                                                                                                                                    </thead>
                                                                                                                                    <tbody>
                                                                                                                                        <tr>
                                                                                                                                            <td><input type="text" class="form-control mt-2 mb-2" name="boqDiscount" placeholder="boq Discount" /></td>
                                                                                                                                            <td><input step="0.01" type="number" class="form-control mt-2 mb-2" name="boqMargin" placeholder="boq margings" /></td>
                                                                                                                                        </tr>
                                                                                                                                    </tbody>
                                                                                                                                </table>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    <?php
                                                                                                                    } ?>

                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <!-- end boq details -->
                                                                                                        </div>
                                                                                                    </div>
                                                                                                <?php } ?>
                                                                                                <script>
                                                                                                    <?php
                                                                                                    if ($flagForBtn >= 1) {
                                                                                                    ?>
                                                                                                        $(".btnHideShow<?= $oneSoList['so_id'] ?>").show();
                                                                                                    <?php
                                                                                                    } else {
                                                                                                    ?>
                                                                                                        $(".btnHideShow<?= $oneSoList['so_id'] ?>").html('<a href="#" class="bg-success text-xs text-light deliveryCreationBtn"><i class="fa fa-check-circle"></i> Delivery Created</a>');
                                                                                                    <?php
                                                                                                        $updSql = "UPDATE `" . ERP_BRANCH_SALES_ORDER . "` SET approvalStatus=10 WHERE so_id='" . $oneSoList['so_id'] . "'";
                                                                                                        $dbCon->query($updSql);
                                                                                                    }
                                                                                                    ?>
                                                                                                </script>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="tab-pane fade" id="profile<?= $oneSoList['so_number'] ?>" role="tabpanel" aria-labelledby="profile-tab">

                                                                                            <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                                <div class="accordion-item">
                                                                                                    <h2 class="accordion-header" id="flush-headingOne">
                                                                                                        <button class="accordion-button btn btn-primary collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                            Customer Details
                                                                                                        </button>
                                                                                                    </h2>
                                                                                                    <div id="basicDetails" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                        <div class="accordion-body p-0">
                                                                                                            <div class="card">
                                                                                                                <div class="card-body p-3">
                                                                                                                    <div class="display-flex-space-between">
                                                                                                                        <p class="font-bold text-xs text-left">Code :</p>
                                                                                                                        <p class="font-bold text-xs text-left"><?= $customerDetails['customer_code'] ?></p>
                                                                                                                    </div>
                                                                                                                    <div class="display-flex-space-between">
                                                                                                                        <p class="font-bold text-xs text-left">GST :</p>
                                                                                                                        <p class="font-bold text-xs text-left"><?= $customerDetails['customer_gstin'] ?></p>
                                                                                                                    </div>
                                                                                                                    <div class="display-flex-space-between">
                                                                                                                        <p class="font-bold text-xs text-left">Pan :</p>
                                                                                                                        <p class="font-bold text-xs text-left"> <?= $customerDetails['customer_pan'] ?></p>
                                                                                                                    </div>
                                                                                                                    <div class="display-flex-space-between">
                                                                                                                        <p class="font-bold text-xs text-left">Address :</p>
                                                                                                                        <p class="font-bold text-xs text-left w-75"><?= $customerAddressDetails['customer_address_building_no'] . ', ' . $customerAddressDetails['customer_address_flat_no'] . ', ' . $customerAddressDetails['customer_address_street_name'] . ', ' . $customerAddressDetails['customer_address_pin_code'] . ', ' . $customerAddressDetails['customer_address_location'] . ', ' . $customerAddressDetails['customer_address_city'] . ', ' . $customerAddressDetails['customer_address_district'] . ', ' . $customerAddressDetails['customer_address_state'] ?></p>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="tab-pane fade" id="classic-view<?= $oneSoList['so_number'] ?>" role="tabpanel" aria-labelledby="classic-view-tab">
                                                                                            <div class="card classic-view bg-transparent">
                                                                                                <div class="card-body classic-view-so-table" style="overflow: auto;">
                                                                                                    <!-- <button type="button" class="btn btn-primary classic-view-btn float-right" id="printButton">Print Table</button> -->
                                                                                                    <button type="button" class="btn btn-primary classic-view-btn float-right" onclick="window.print();">Print</button>
                                                                                                    <div class="printable-view">
                                                                                                        <table class="classic-view table-bordered">
                                                                                                            <tbody>
                                                                                                                <tr>
                                                                                                                    <td colspan="5" class="border-right">
                                                                                                                        <p class="font-bold"><?= $companyData['company_name'] ?></p>
                                                                                                                        <p><?= $companyData['location_building_no'] ?>, <?= $companyData['location_flat_no'] ?></p>
                                                                                                                        <p><?= $companyData['location'] ?>, <?= $companyData['location_street_name'] ?>, <?= $companyData['location_pin_code'] ?></p>
                                                                                                                        <p><?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?></p>
                                                                                                                        <p><?= $companyData['location_state'] ?></p>
                                                                                                                        <p>GSTIN/UIN: <?= $companyData['branch_gstin'] ?></p>
                                                                                                                        <p>Company’s PAN: <?= $companyData['company_pan'] ?></p>
                                                                                                                        <p>State Name : <?= $companyData['location_state'] ?></p>
                                                                                                                        <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                                                                                                                    </td>
                                                                                                                    <td colspan="3">
                                                                                                                        <p>Sales Order Number</p>
                                                                                                                        <p class="font-bold"><?= $oneSoList['so_number'] ?></p>
                                                                                                                    </td>
                                                                                                                    <td colspan="3">
                                                                                                                        <p>Dated</p>
                                                                                                                        <p class="font-bold"><?= $oneSoList['delivery_date'] ?></p>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td colspan="5" class="border-right">
                                                                                                                        <p>Buyer (Bill to)</p>
                                                                                                                        <p class="font-bold"><?= $customerName ?></p>
                                                                                                                        <p><?= $oneSoList['billingAddress'] ?></p>
                                                                                                                        <p>GSTIN/UIN : <?= $customerDetails['customer_gstin'] ?></p>
                                                                                                                        <!-- <p>State Name : Maharashtra, Code : 27</p> -->
                                                                                                                    </td>
                                                                                                                    <td colspan="5" class="border-right">
                                                                                                                        <p>Consignee (Ship to)</p>
                                                                                                                        <p class="font-bold"><?= $customerName ?></p>
                                                                                                                        <p><?= $oneSoList['shippingAddress'] ?></p>
                                                                                                                        <!-- <p>State Name : Maharashtra, Code : 27</p> -->
                                                                                                                        <!-- <p>Place of Supply : Maharashtra</p> -->
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                            </tbody>
                                                                                                            <tbody>
                                                                                                                <tr>
                                                                                                                    <th rowspan="2">Sl No.</th>
                                                                                                                    <th rowspan="2">Particulars</th>
                                                                                                                    <th rowspan="2">HSN/SAC </th>
                                                                                                                    <th rowspan="2">Quantity</th>
                                                                                                                    <th rowspan="2">Rate</th>
                                                                                                                    <th rowspan="2">UOM</th>
                                                                                                                    <th rowspan="2">Discount</th>
                                                                                                                    <th colspan="2">IGST</th>
                                                                                                                    <th rowspan="2">Total Amount</th>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <th>Rate</th>
                                                                                                                    <th>Amount</th>
                                                                                                                </tr>
                                                                                                                <?php
                                                                                                                $itemDetails = $BranchSoObj->fetchBranchSoItems($oneSoList['so_id'])['data'];
                                                                                                                // console($itemDetails);
                                                                                                                $flagForBtn = 0;
                                                                                                                $grandTotalInvAmount = 0;
                                                                                                                foreach ($itemDetails as $oneItem) {
                                                                                                                    $baseUnitMeasure = $ItemsObj->getBaseUnitMeasureById($oneItem['uom']);
                                                                                                                    $uomName = $baseUnitMeasure['data']['uomName'];

                                                                                                                    $deliveryScheduleObj = $BranchSoObj->fetchBranchSoItemsDeliverySchedule($oneItem['so_item_id']);
                                                                                                                    $deliverySchedule = $deliveryScheduleObj['data'];
                                                                                                                    if (count($deliverySchedule) > 0) {
                                                                                                                        $flagForBtn++;
                                                                                                                    }
                                                                                                                    $subTotalAmt = ($oneItem['unitPrice'] * $oneItem['completion_value']);
                                                                                                                    $itemTotalDiscount = ($subTotalAmt * $oneItem['totalDiscount']) / 100;
                                                                                                                    $itemTotalTax = ($subTotalAmt * $oneItem['tax']) / 100;
                                                                                                                    $itemTotalAmt = $subTotalAmt - $itemTotalDiscount + $itemTotalTax; 
                                                                                                                    $grandTotalInvAmount += $itemTotalAmt;
                                                                                                                ?>
                                                                                                                    <tr>
                                                                                                                        <td class="text-center">
                                                                                                                            <p><?= ++$i ?></p>
                                                                                                                        </td>
                                                                                                                        <td class="text-center">
                                                                                                                            <p class="font-bold"><?= $oneItem['itemName'] ?></p>
                                                                                                                            <p class="text-italic"><?= $oneItem['itemCode'] ?></p>
                                                                                                                        </td>
                                                                                                                        <td class="text-center">
                                                                                                                            <p><?= $oneItem['hsnCode'] ?></p>
                                                                                                                        </td>
                                                                                                                        <td class="text-center">
                                                                                                                            <p><?= $oneItem['completion_value'] ?></p>
                                                                                                                        </td>
                                                                                                                        <td class="text-right">
                                                                                                                            <p><?= number_format($oneItem['unitPrice'], 2) ?></p>
                                                                                                                        </td>
                                                                                                                        <td class="text-center">
                                                                                                                            <p><?= $uomName ?></p>
                                                                                                                        </td>
                                                                                                                        <td class="text-right">
                                                                                                                            <p><?= $itemTotalDiscount ?></p>
                                                                                                                            <p class="font-bold text-italic">(-<?= $oneItem['totalDiscount'] ?>%)</p>
                                                                                                                        </td>
                                                                                                                        <td class="text-center">
                                                                                                                            <p><?= $oneItem['tax'] ?>%</p>
                                                                                                                        </td>
                                                                                                                        <td class="text-right">
                                                                                                                            <p><?= $itemTotalTax ?></p>
                                                                                                                        </td>
                                                                                                                        <td class="text-right">
                                                                                                                            <p><?= $itemTotalAmt ?></p>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                <?php } ?>
                                                                                                                <tr>
                                                                                                                    <td colspan="10" class="text-right font-bold">
                                                                                                                        <p><?= $grandTotalInvAmount ?></p>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td colspan="5">
                                                                                                                        <p>Amount Chargeable (in words)</p>
                                                                                                                        <p class="font-bold"><?= number_to_words_indian_rupees($grandTotalInvAmount); ?> ONLY</p>
                                                                                                                    </td>
                                                                                                                    <td colspan="5" class="text-right">E. & O.E</td>
                                                                                                                </tr>

                                                                                                                <tr>
                                                                                                                    <td colspan="5">
                                                                                                                        <p>Remarks:</p>
                                                                                                                        <p>Created By: <b><?= $companyData['company_footer'] ?></b></p>
                                                                                                                    </td>
                                                                                                                    <td colspan="5" class="text-right border">
                                                                                                                        <p class="text-center font-bold"> for <?= $companyData['company_name'] ?></p>
                                                                                                                        <p class="text-center sign-img">
                                                                                                                            <!-- <img width="60" src="../../public/storage/signature/<?= $companyData['signature'] ?>" alt="signature"> -->
                                                                                                                            <img width="160" src="<?= COMP_STORAGE_URL ?>/profile/<?= $companyData['signature'] ?>" alt="signature">
                                                                                                                        </p>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                            </tbody>
                                                                                                        </table>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>

                                                                                        </div>




                                                                                        <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                                        <div class="tab-pane fade" id="history<?= $oneSoList['so_id'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                                            <div class="audit-head-section mb-3 mt-3 ">
                                                                                                <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($oneSoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['created_at']) ?></p>
                                                                                                <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($oneSoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['updated_at']) ?></p>
                                                                                            </div>
                                                                                            <hr>
                                                                                            <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $oneSoList['so_number'] ?>">

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
                                                                                        </div>
                                                                                        <!-- -------------------Audit History Tab Body End------------------------- -->
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <!--/.Content-->
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </td>
                                                        </tr>

                                                        <!-- right modal end here  -->
                                                    <?php } ?>
                                                </tbody>

                                                <tbody>
                                                    <tr>
                                                        <td colspan="<?= $settingsCheckboxCount + 2; ?>">
                                                            <!-- Start .pagination -->

                                                            <?php
                                                            if ($count > 0 && $count > $GLOBALS['show']) {
                                                            ?>
                                                                <div class="pagination align-right">
                                                                    <?php pagination($count, "frm_opts"); ?>
                                                                </div>

                                                                <!-- End .pagination -->

                                                            <?php } ?>

                                                            <!-- End .pagination -->
                                                        </td>
                                                    </tr>
                                                </tbody>

                                            </table>
                                        <?php } else { ?>
                                            <table class="table defaultDataTable table-hover text-nowrap">
                                                <thead>
                                                    <tr>
                                                        <td>

                                                        </td>
                                                    </tr>
                                                </thead>
                                            </table>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        </div>
                        <!---------------------------------Table settings Model Start--------------------------------->
                        <div class="modal" id="myModal2">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Table Column Settings</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                                        <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                        <input type="hidden" name="pageTableName" value="ERP_BRANCH_SALES_ORDER" />
                                        <div class="modal-body">
                                            <div id="dropdownframe"></div>
                                            <div id="main2">
                                                <?php $sm = 0; ?>
                                                <table>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php $sm++;
                                                                                                                        echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?= $sm; ?>" />
                                                            SO Number</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php $sm++;
                                                                                                                        echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="<?= $sm; ?>" />
                                                            Customer PO Number</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php $sm++;
                                                                                                                        echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?= $sm; ?>" />
                                                            Delivery Date</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php $sm++;
                                                                                                                        echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?= $sm; ?>" />
                                                            Customer Name</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php $sm++;
                                                                                                                        echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?= $sm; ?>" />
                                                            Status</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php $sm++;
                                                                                                                        echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?= $sm; ?>" />
                                                            JO Status</td>
                                                    </tr>
                                                    <!-- <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php // $sm++; echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); 
                                                                                            ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?= $sm; ?>" />
                                Status</td>
                            </tr> -->
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php $sm++;
                                                                                                                        echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?= $sm; ?>" />
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

                        <!---------------------------------Table Model End--------------------------------->


                    </div>
                </div>
            </div>
    </div>
    </div>
    </section>
    </div> <!-- For Pegination------->
    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                        echo $_REQUEST['pageNo'];
                                                    } ?>">
    </form>
    <!-- End Pegination from------->
<?php } ?>

<?php require_once("../common/footer.php"); ?>

<script>
    $(document).on("click", ".dlt-popup", function() {
        $(this).parent().parent().remove();
    });

    function rm() {
        // $(event.target).closest("tr").remove();
        $(this).parent().parent().parent().remove();
    }

    function addOtherCost(id) {
        let addressRandNo = Math.ceil(Math.random() * 100000);
        $(`.modal-add-row_${id}`).append(`<div class="row othe-cost-infor">
        <div class="col-lg-5 col-md-12 col-sm-12">
            <div class="form-input">
                <label for="">Service Description</label>
                <textarea class="form-control" placeholder="Description" name="otherCostDetails[${addressRandNo}][services]"></textarea>
            </div>
        </div>
        <div class="col-lg-5 col-md-12 col-sm-12">
            <div class="form-input">
                <label for="">Amount</label>
                <input step="0.01" type="number" class="form-control" placeholder="Amount" name="otherCostDetails[${addressRandNo}][amount]">
            </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6">
            <div class="add-btn-minus">
                <a style="cursor: pointer" class="btn btn-danger">
                    <i class="fa fa-minus"></i>
                </a>
            </div>
        </div>
    </div>`);
    }
    $(document).on("click", ".add-btn-minus", function() {
        $(this).parent().parent().remove();
    });

    // function addMultiQty(id) {
    //     let addressRandNo = Math.ceil(Math.random() * 100000);
    //     //$(`.modal-add-row_${id}`).append(`<tr><td><span class='has-float-label'><input type='date' name='listItem[${id}][deliverySchedule][${addressRandNo}][multiDeliveryDate]' class='form-control' placeholder='delivery date' required><label>Delivery date</label></span></td><td><span class='has-float-label'><input type='text' name='listItem[${id}][deliverySchedule][${addressRandNo}][quantity]' class='form-control multiQuantity' data-itemid="${id}" id='multiQuantity_${addressRandNo}' placeholder='quantity' required><label>quantity</label></span></td><td><a class='btn btn-danger' onclick='rm()'><i class='fa fa-minus'></i></a></td></tr>`);
    //     $(`.modal-add-row_${id}`).append(`
    //   <div class="modal-add-row">
    //     <div class="row modal-cog-right">
    //       <div class="col-lg-5 col-md-5 col-sm-5">
    //           <div class="form-input">
    //               <label>Delivery date</label>
    //               <input type="date" name="listItem[${id}][deliverySchedule][${addressRandNo}][multiDeliveryDate]" class="form-control multiDeliveryDate" id="multiDeliveryDate_${id}" placeholder="delivery date" value="<?= $_GET['deliveryDate'] ?>">

    //           </div>
    //       </div>
    //       <div class="col-lg-5 col-md-5 col-sm-5">
    //           <div class="form-input">
    //               <label>Quantity</label>
    //               <input type="text" name="listItem[${id}][deliverySchedule][${addressRandNo}][quantity]" class="form-control multiQuantity" data-itemid="${id}" id="multiQuantity_${id}" placeholder="quantity" value="0">

    //           </div>
    //       </div>
    //       <div class="col-lg-2 col-md-2 col-sm-2 dlt-popup">
    //           <a style="cursor: pointer" class="btn btn-danger">
    //               <i class="fa fa-minus"></i>
    //           </a>
    //       </div>
    //     </div>
    //   </div>`);
    // }
</script>
<script>
    $(document).ready(function() {

        // start date checker
        function so_check_date() {
            let date = $("#soDate").val();
            let max = '<?php echo $max; ?>';
            let min = '<?php echo $min; ?>';

            if (date < min) {
                $(".soDateMsg").html(`<p class="text-danger text-xs" id="podatelabel">Invalid SO creation Date</p>`);
                document.getElementById("soCreationBtn").disabled = true;
            } else if (date > max) {
                $(".soDateMsg").html(`<p class="text-danger text-xs" id="podatelabel">Invalid SO creation Date</p>`);
                document.getElementById("soCreationBtn").disabled = true;
            } else {
                $(".soDateMsg").html("");
                document.getElementById("soCreationBtn").disabled = false;
            }
        }
        $("#soDate ").on("keyup", function() {
            so_check_date();
        });

        function delivery_check_date() {
            let deliveryDate = $("#deliveryDate").val();
            let soDate = $("#soDate").val();

            if (deliveryDate < soDate) {
                $(".deliveryDateMsg").html(`<p class="text-danger text-xs" id="podatelabel">Invalid Delivery creation Date</p>`);
                document.getElementById("soCreationBtn").disabled = true;
            } else {
                $(".deliveryDateMsg").html("");
                document.getElementById("soCreationBtn").disabled = false;
            }
        }
        $("#deliveryDate ").on("keyup blur click", function() {
            delivery_check_date();
        });
        // end date checker



        loadItems();

        loadCustomers();
        // **************************************
        function loadItems() {
            // alert();
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

        $(".approvalTab").on("click", function() {
            // let soRowKey = ($(this).attr("id")).split("_")[1];
            // let soId = ($(this).attr("id")).split("_")[2];
            // let completionPercentage = $(`.completionPercentage`).val();
            // console.log('soId, completionPercentage');
            // console.log(soId, completionPercentage);
            // if (confirm("Are you sure?")) {
            //     $.ajax({
            //         type: "GET",
            //         url: `ajaxs/so/ajax-items-list.php`,
            //         data: {
            //             act: "jobOrderApprovalTab",
            //             soId
            //         },
            //         beforeSend: function() {
            //             $(".approvalTab").html(`<option value="">Processing...</option>`);
            //         },
            //         success: function(response) {
            //             console.log(response);
            //             if (response === 'success') {
            //                 window.location.href = "";
            //             } else {
            //                 $(".approvalTab").html(response);
            //             }
            //         }
            //     });
            // }
        });

        $(`.approvalTab`).prop("disabled", true);
        $(".completionPercentage").on("keyup", function() {
            let soKey = ($(this).attr("id")).split("_")[1];
            let itemKey = ($(this).attr("id")).split("_")[2];
            let enterValue = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            let itemQtyCard = (parseFloat($(`#itemQtyCard_${soKey}_${itemKey}`).text()) > 0) ? parseFloat($(`#itemQtyCard_${soKey}_${itemKey}`).text()) : 0;
            let remQtySpan = (parseFloat($(`#remainingQtySpan_${soKey}_${itemKey}`).text()) > 0) ? parseFloat($(`#remainingQtySpan_${soKey}_${itemKey}`).text()) : 0;
            let remQtyCal = remQtySpan - enterValue;

            if (remQtySpan >= enterValue && enterValue != "") {
                $(`#completionPercentageMsg_${soKey}_${itemKey}`).hide();
                $(`#remainingQtyHidden_${soKey}_${itemKey}`).val(remQtyCal);
                $(`.approvalTab`).prop("disabled", false);
            } else {
                $(`#completionPercentageMsg_${soKey}_${itemKey}`).show();
                $(`#remainingQtyHidden_${soKey}_${itemKey}`).val(remQtySpan);
                $(`.approvalTab`).prop("disabled", true);
            }
        });

        $("#goodsType").on("change", function() {
            let goodsType = $(this).val();

            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-items-goods-type.php`,
                data: {
                    act: "goodsType",
                    goodsType
                },
                beforeSend: function() {
                    $("#itemsDropDown").html(`Loding...`);
                },
                success: function(response) {
                    console.log(response);
                    $("#itemsDropDown").html(response);
                }
            });
        });
        // get customer details by id
        $("#customerDropDown").on("change", function() {
            let customerId = $(this).val();

            if (customerId > 0) {
                $(document).on("click", ".billToCheckbox", function() {
                    if ($('input.billToCheckbox').is(':checked')) {
                        // $(".shipTo").html(`checked ${customerId}`);
                        $.ajax({
                            type: "GET",
                            url: `ajaxs/so/ajax-customers-address.php`,
                            data: {
                                act: "customerAddress",
                                customerId
                            },
                            beforeSend: function() {
                                $("#shipTo").html(`Loding...`);
                            },
                            success: function(response) {
                                console.log(response);
                                $("#shipTo").html(response);
                            }
                        });
                    } else {
                        $(".changeAddress").click();
                        // $("#shipTo").html(`unchecked ${customerId}`);
                    }
                });

                $(".customerIdInp").val(customerId);
                $.ajax({
                    type: "GET",
                    url: `ajaxs/so/ajax-customers-list.php`,
                    data: {
                        act: "listItem",
                        customerId
                    },
                    beforeSend: function() {
                        $("#customerInfo").html(`<option value="">Loding...</option>`);
                    },
                    success: function(response) {
                        console.log(response);
                        $("#customerInfo").html(response);
                        let creditPeriod = $("#spanCreditPeriod").text();
                        $("#inputCreditPeriod").val(creditPeriod);
                    }
                });
            }
        });

        $(document).on("click", "#pills-home-tab", function() {
            $("#saveChanges").html('<button type="button" class="btn btn-primary go">Go</button>');
        });
        $(document).on("click", "#pills-profile-tab", function() {
            $("#saveChanges").html('<button type="button" class="btn btn-primary" id="save">Save</button>');
        });

        // 👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀
        $(document).on('click', '.go', function() {
            let the_value = $('input[name=radioBtn]:radio:checked').val();

            console.log(the_value);
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-customers-address.php`,
                data: {
                    act: "shipAddressRadio",
                    addressKey: the_value
                },
                beforeSend: function() {
                    $(`.go`).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                },
                success: function(response) {
                    console.log(response);
                    $(".address-change-modal").hide();
                    $(".modal-backdrop").hide();
                    $("#shipTo").html(response);
                    $("#shippingAddressInp").val(response);
                    $('input.billToCheckbox').prop('checked', false);
                    $(".go").html('<button type="button" class="btn btn-primary go">Go</button>');
                }
            });
        });

        // submit address form
        $(document).on('click', '#save', function() {
            let customerId = $('.customerIdInp').val();
            let billingNo = $("#billingNo").val();
            let flatNo = $("#flatNo").val();
            let streetName = $("#streetName").val();
            let location = $("#location").val();
            let city = $("#city").val();
            let pinCode = $("#pinCode").val();
            let district = $("#district").val();
            let state = $("#state").val();

            if (billingNo != '' && flatNo != '' && streetName != '' && location != '' && city != '' && pinCode != '' && district != '' && state != '') {
                $.ajax({
                    type: "GET",
                    url: `ajaxs/so/ajax-customers-address.php`,
                    data: {
                        act: "shipAddressSave",
                        customerId,
                        billingNo,
                        flatNo,
                        streetName,
                        location,
                        city,
                        pinCode,
                        district,
                        state
                    },
                    beforeSend: function() {
                        $(`#save`).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                    },
                    success: function(response) {
                        // console.log(response);
                        $(".address-change-modal").hide();
                        $(".modal-backdrop").hide();
                        $("#shipTo").html(response);
                        $('input.billToCheckbox').prop('checked', false);
                        $("#save").html('<button type="button" class="btn btn-primary">Update</button>');
                    }
                });
            } else {
                alert(`All field are required`);
            }
        });
        // 👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀

        // get item details by id
        $("#itemsDropDown").on("change", function() {
            let itemId = $(this).val();
            if (itemId > 0) {
                let deliveryDate = $('#deliveryDate').val();
                $.ajax({
                    type: "GET",
                    url: `ajaxs/so/ajax-items-list-direct.php`,
                    data: {
                        act: "listItem",
                        type: "sales-order",
                        itemId,
                        deliveryDate
                    },
                    beforeSend: function() {
                        //  $(`#spanItemsTable`).html(`Loding...`);
                    },
                    success: function(response) {
                        console.log(response);
                        $("#itemsTable").append(response);
                        calculateGrandTotalAmount();
                    }
                });
            }
        });
        $(document).on("click", ".delItemBtn", function() {
            // let id = ($(this).attr("id")).split("_")[1];
            // $(`#delItemRowBtn_${id}`).remove();
            $(this).parent().parent().remove();
            calculateGrandTotalAmount();
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
                    $(".totalPrice").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    console.log(response);
                    $(".totalPrice").html(response);
                }
            });
        });

        $("#fob").on("click", function() {
            // alert();
            if ($('#fob').is(':checked')) {
                $("#otherCostCard").show();
            } else {
                $("#otherCostCard").hide();
            }
        });

        // 🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴
        // auto calculation 
        function calculateGrandTotalAmount() {
            let totalAmount = 0;
            let totalTaxAmount = 0;
            let totalDiscountAmount = 0;
            let itemBaseAmountInp = 0;
            $(".itemTotalPrice").each(function() {
                totalAmount += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            $(".itemTotalTax").each(function() {
                totalTaxAmount += (parseFloat($(this).html()) > 0) ? parseFloat($(this).html()) : 0;
            });
            $(".itemTotalDiscount").each(function() {
                totalDiscountAmount += (parseFloat($(this).html()) > 0) ? parseFloat($(this).html()) : 0;
            });
            $(".itemBaseAmountInp").each(function() {
                itemBaseAmountInp += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            // console.log("Grand = ", totalAmount, totalTaxAmount, totalDiscountAmount);
            // let grandSubTotalAmt = totalAmount - totalTaxAmount - totalDiscountAmount;
            // let grandSubTotalAmt = totalAmount - totalTaxAmount;
            $("#grandSubTotalAmt").html(itemBaseAmountInp.toFixed(2));
            $("#grandTotalDiscount").html(totalDiscountAmount.toFixed(2));
            $("#grandTaxAmt").html(totalTaxAmount.toFixed(2));
            $("#grandTotalAmt").html(totalAmount.toFixed(2));
        }

        function calculateOneItemAmounts(rowNo) {
            let itemQty = (parseFloat($(`#itemQty_${rowNo}`).val()) > 0) ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
            let itemUnitPrice = (parseFloat($(`#itemUnitPrice_${rowNo}`).val()) > 0) ? parseFloat($(`#itemUnitPrice_${rowNo}`).val()) : 0;
            let itemDiscount = (parseFloat($(`#itemDiscount_${rowNo}`).val())) ? parseFloat($(`#itemDiscount_${rowNo}`).val()) : 0;
            let itemTax = (parseFloat($(`#itemTax_${rowNo}`).val())) ? parseFloat($(`#itemTax_${rowNo}`).val()) : 0;

            $(`#multiQuantity_${rowNo}`).val(itemQty);

            let basicPrice = itemUnitPrice * itemQty;
            let totalDiscount = basicPrice * itemDiscount / 100;
            let priceWithDiscount = basicPrice - totalDiscount;
            let totalTax = priceWithDiscount * itemTax / 100;
            let totalItemPrice = priceWithDiscount + totalTax;

            console.log(itemQty, itemUnitPrice, itemDiscount, itemTax);

            $(`#itemBaseAmountInp_${rowNo}`).val(basicPrice.toFixed(2));
            $(`#itemTotalDiscount_${rowNo}`).html(totalDiscount.toFixed(2));
            $(`#itemTotalDiscount1_${rowNo}`).val(totalDiscount.toFixed(2));
            $(`#itemTotalTax_${rowNo}`).html(totalTax.toFixed(2));
            $(`#itemTotalTax1_${rowNo}`).val(totalTax.toFixed(2));
            $(`#itemTotalPrice_${rowNo}`).val(totalItemPrice.toFixed(2));
            $(`#itemTotalPrice1_${rowNo}`).html(totalItemPrice.toFixed(2));
            $(`#mainQty_${rowNo}`).html(itemQty);
            calculateGrandTotalAmount();
        }

        // #######################################################
        function calculateQuantity(rowNo, itemId, thisVal) {
            // console.log("code", rowNo);
            let itemQty = (parseFloat($(`#itemQty_${itemId}`).val()) > 0) ? parseFloat($(`#itemQty_${itemId}`).val()) : 0;
            let totalQty = 0;
            // console.log("calculateQuantity() ========== Row:", rowNo);
            // console.log("Total qty", itemQty);
            $(".multiQuantity").each(function() {
                if ($(this).data("itemid") == itemId) {
                    totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                    // console.log('Qtys":', $(this).val());
                }
            });

            let avlQty = itemQty - totalQty;

            // console.log("Avl qty:", avlQty);

            if (avlQty < 0) {
                let totalQty = 0;
                $(`#multiQuantity_${rowNo}`).val('');
                $(".multiQuantity").each(function() {
                    if ($(this).data("itemid") == itemId) {
                        totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                        // console.log('Qtys":', $(this).val());
                    }
                });
                let avlQty = itemQty - totalQty;

                $(`#mainQtymsg_${itemId}`).show();
                $(`#mainQtymsg_${itemId}`).html("[Error! Delivery QTY should equal to order QTY.]");
                $(`#mainQty_${itemId}`).html(avlQty);
            } else {
                let totalQty = 0;
                $(".multiQuantity").each(function() {
                    if ($(this).data("itemid") == itemId) {
                        totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                        // console.log('Qtys":', $(this).val());
                    }
                });

                let avlQty = itemQty - totalQty;

                $(`#mainQtymsg_${itemId}`).hide();
                $(`#mainQty_${itemId}`).html(avlQty);
            }
            if (avlQty == 0) {
                $(`#saveClose_${itemId}`).show();
                $(`#saveCloseLoading_${itemId}`).hide();
            } else {
                $(`#saveClose_${itemId}`).hide();
                $(`#saveCloseLoading_${itemId}`).show();
                $(`#setAvlQty_${itemId}`).html(avlQty);
            }
        }

        function itemMaxDiscount(rowNo, keyValue = 0) {
            let itemMaxDis = $(`#itemMaxDiscount_${rowNo}`).html();
            console.log('this is max discount', itemMaxDis);
            console.log('this is key value', keyValue);
            if (parseFloat(keyValue) > parseFloat(itemMaxDis)) {
                console.log('max discount is over');
                $(`#itemSpecialDiscount_${rowNo}`).text(`Special Discount`);
                $(`#itemSpecialDiscount_${rowNo}`).show();
                // $(`#specialDiscount`).show();
            } else {
                $(`#itemSpecialDiscount_${rowNo}`).hide();
                // $(`#specialDiscount`).hide();
            }
        }

        $(document).on("keyup blur click", ".itemQty", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let itemVal = $(`#itemQty_${rowNo}`).val();
            if (itemVal <= 0) {
                // let itemVal = $(`#itemQty_${rowNo}`).val(1);
                document.getElementById("soCreationBtn").disabled = true;
            } else {
                document.getElementById("soCreationBtn").disabled = false;
            }
            calculateOneItemAmounts(rowNo);
        });

        function checkSpecialDiscount() {
            let isSpecialDiscountApplied = false;

            $(".itemDiscount").each(function() {
                let rowNum = ($(this).attr("id")).split("_")[1];
                let discountPercentage = parseFloat($(this).val());
                discountPercentage = discountPercentage > 0 ? discountPercentage : 0;
                let maxDiscountPercentage = parseFloat($(`#itemMaxDiscount_${rowNum}`).html());
                maxDiscountPercentage = maxDiscountPercentage > 0 ? maxDiscountPercentage : 0;
                if (discountPercentage > maxDiscountPercentage) {
                    isSpecialDiscountApplied = true;
                }
            });

            if (isSpecialDiscountApplied) {
                $(`#approvalStatus`).val(`12`);
                console.log('max');
            } else {
                $(`#approvalStatus`).val(`14`);
                console.log('ok');
            }
        }


        $(document).on("keyup", ".itemDiscount", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let keyValue = $(this).val();
            calculateOneItemAmounts(rowNo);
            itemMaxDiscount(rowNo, keyValue);
            checkSpecialDiscount();
            // $(`#itemTotalDiscount1_${rowNo}`).attr('disabled', 'disabled');
        });

        // #######################################################
        $(document).on("blur", ".itemTotalDiscount1", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let itemDiscountAmt = ($(this).val());

            let itemQty = (parseFloat($(`#itemQty_${rowNo}`).val()) > 0) ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
            let itemUnitPrice = (parseFloat($(`#itemUnitPrice_${rowNo}`).val()) > 0) ? parseFloat($(`#itemUnitPrice_${rowNo}`).val()) : 0;

            let totalAmt = itemQty * itemUnitPrice;
            let discountPercentage = itemDiscountAmt * 100 / totalAmt;

            $(`#itemDiscount_${rowNo}`).val(discountPercentage.toFixed(2));

            let itemDiscount = (parseFloat($(`#itemDiscount_${rowNo}`).val())) ? parseFloat($(`#itemDiscount_${rowNo}`).val()) : 0;

            console.log('total', itemQty, itemUnitPrice, discountPercentage);
            calculateOneItemAmounts(rowNo);
            itemMaxDiscount(rowNo, itemDiscount);
            checkSpecialDiscount();

            // $(`#itemDiscount_${rowNo}`).attr('disabled', 'disabled');
            // discountCalculate(rowNo, thisVal);
        });

        // #######################################################
        $(document).on("keyup blur click change", ".multiQuantity", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let itemid = ($(this).data("itemid"));
            let thisVal = ($(this).val());
            calculateQuantity(rowNo, itemid, thisVal);
        });

        // allItemsBtn
        $("#allItemsBtn").on('click', function() {
            window.location.href = "";
        })

        // itemWiseSearch
        $("#itemWiseSearch").on('click', function() {
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-so-list.php`,
                data: {
                    act: "itemWiseSearch"
                },
                beforeSend: function() {
                    $(".tableDataBody").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    console.log(response);
                    $(".tableDataBody").html(response);
                }
            });
        })

        $(function() {
            $("#datepicker").datepicker({
                autoclose: true,
                todayHighlight: true
            }).datepicker('update', new Date());
        });

    });

    $(document).on("click", "#btnSearchCollpase", function() {
        sec = document.getElementById("btnSearchCollpase").parentElement;
        coll = sec.getElementsByClassName("collapsible-content")[0];

        if (sec.style.width != '100%') {
            sec.style.width = '100%';
        } else {
            sec.style.width = 'auto';
        }

        if (coll.style.height != 'auto') {
            coll.style.height = 'auto';
        } else {
            coll.style.height = '0px';
        }

        $(this).children().toggleClass("fa-search fa-times");
    });


    $('.hamburger').click(function() {
        $('.hamburger').toggleClass('show');
        $('#overlay').toggleClass('show');
        $('.nav-action').toggleClass('show');
    });



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
    $('#profitCenterDropDown')
        .select2()
        .on('select2:open', () => {
            // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
        });
    $('#kamDropDown')
        .select2()
        .on('select2:open', () => {
            // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
        });
</script>

<script src="<?= BASE_URL; ?>public/validations/soValidation.js"></script>