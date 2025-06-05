<?php

require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
//require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-po-controller.php");


// console($_GET['selectItemPr']);
// console($_SESSION);
$variant = $_SESSION['visitBranchAdminInfo']['flAdminVariant'];
$check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
// console($check_var_sql);
$check_var_data = $check_var_sql['data'];

$max = $check_var_data['month_end'];
$min = $check_var_data['month_start'];

$today = date("Y-m-d");
if (isset($_POST["changeStatus"])) {
    $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
    swalToast($newStatusObj["status"], $newStatusObj["message"],);
}

$BranchPoObj = new BranchPo();

require_once("../../app/v1/functions/branch/func-items-controller.php");
$ItemsObj = new ItemsController();
if (isset($_POST['createData'])) {
    // console($_POST);
    $addBranchPo = $BranchPoObj->addBranchPo($_POST + $_FILES, $branch_id, $company_id, $location_id);
    // console($addBranchPo);
    // exit;

    swalAlert($addBranchPo["status"], ucfirst($addBranchPo["status"]), $addBranchPo["message"], BASE_URL . "branch/location/manage-purchases-orders.php");
}

if (isset($_POST["visit"])) {
    $newStatusObj = VisitBranches($_POST);
    redirect(BRANCH_URL);
}

//$sql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE company_branch_id=".$branch_id." AND company_id=".$company_id." `vendor_status`!='deleted'";
$sql = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `vendor_status`='active' AND `company_id`=$company_id AND `company_branch_id` = $branch_id ";
$get = queryGet($sql, true);
$datas = $get['data'];

$vendrSelect = ' ';
foreach ($datas as $data) {
    $vendrSelect .= '<option value="' . $data['vendor_id'] . '">' . $data['trade_name'] . '</option>';
}
// if (isset($_POST["createdata"])) {
//     $addNewObj = createDataBranches($_POST);
//     if ($addNewObj["status"] == "success") {
//         $branchId = base64_encode($addNewObj['branchId']);
//         redirect($_SERVER['PHP_SELF'] . "?branchLocation=" . $branchId);
//         swalToast($addNewObj["status"], $addNewObj["message"]);
//         // console($addNewObj);
//     } else {
//         swalToast($addNewObj["status"], $addNewObj["message"]);
//     }
// }

if (isset($_POST["editdata"])) {
    $editDataObj = updateDataBranches($_POST);

    swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["editNewPOFormSubmitBtn"])) {
    // console($_POST);
    $editBranchPo = $BranchPoObj->editBranchPo($_POST);

    swalToast($editBranchPo["status"], $editBranchPo["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩



if (isset($_GET["close-po"])) {
    $po_id = $_GET['close-po'];
    $update = queryUpdate("UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`=10 WHERE `po_id`=$po_id");
    swalToast($update["status"], $update["message"]);
}

?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
    .purchase-order-modal .modal-dialog .modal-content .modal-body {
        width: 100%;
    }

    .purchase-order-modal .modal-dialog .modal-content .modal-body .container {
        overflow: auto;
    }

    .purchase-order-modal .modal-header {

        height: 263px;

    }

    .card.po-creation-card .card-body {
        min-height: 100%;
        height: 300px;
    }

    .card.po-vendor-details-view .card-body {
        height: auto !important;
    }

    .card.other-cost-info .card-body {
        height: auto;
    }

    span.error.po__incoTerms {
        bottom: -15px;
    }

    .tooltip-label-btn {
        position: relative;
        left: -7px;
        top: -29px;
        float: right;
    }

    .currency-section {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .static-currency::before,
    .dynamic-currency::before {
        bottom: 21px !important;
    }

    .so-card-body .static-currency input,
    .so-card-body .dynamic-currency input,
    .dynamic-currency select {
        height: auto !important;
    }

    .printable-view .h3-title {
        visibility: hidden;
    }

    .is-purchase-order .customer-name-code h2 {
        font-size: 1.3rem;
        margin-bottom: 10px;
    }

    .purchase-order-currancy .static-currency::before,
    .purchase-order-currancy .dynamic-currency::before {
        bottom: 29px !important;
    }



    @media print {
        body {
            visibility: hidden;
        }


        .printable-view {
            visibility: visible !important;
        }

        .printable-view .h3-title {
            visibility: visible;
            text-align: center;
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

    }
</style>

<?php
//console($_SESSION);
if (isset($_POST['rfq_po'])) {

    // console($_POST);
    $id = $_POST["erp_v_id"];
    $query = "SELECT * FROM erp_vendor_response WHERE erp_v_id = '$id'";
    $dataset = queryGet($query, false);
    $data = $dataset["data"];
    $rfq_code = $data["rfq_code"];

    // console($_POST);
    // echo implode(',', array_keys($_POST['items']));

?>

    <div class="content-wrapper is-purchase-order">
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
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Purchase Order List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Purchase Order</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>

                <form action="" method="POST" id="submitPoForm" name="submitPoForm" onsubmit="return validationfunction()">
                <input type="hidden" name="poOrigin" value="rfq">

                    <input type="hidden" name="createData" id="createData" value="">
                    <div class="row po-form-creation">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="card so-creation-card po-creation-card  po-creation-card ">
                                        <div class="card-header">
                                            <div class="row customer-info-head">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="head">
                                                        <i class="fa fa-user"></i>
                                                        <h4>Vendor Info</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body others-info vendor-info so-card-body">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="row info-form-view">
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <div class="form-inline input-box customer-select mb-3">
                                                                <label for="">Vendor Name</label>
                                                                <select name="vendorId" id="" class="form-control selct-vendor-dropdown w-100">
                                                                    <option value="<?= $data["vendor_id"] ?>"><?= $data["vendor_name"] ?></option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <div class="customer-info-text po-customer-info-text" id="vendorInfo">
                                                                <div class="card po-vendor-details-view">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-lg-4 col-md-4 col-sm-12 display-flex customer-code"><i class="fa fa-check"></i>&nbsp;<p>Code :&nbsp;</p>
                                                                                <p> <?= $data['vendor_code'] ?></p>
                                                                                <div class="divider"></div>
                                                                            </div>
                                                                            <div class="col-lg-4 col-md-4 col-sm-12 display-flex customer-gstin"><i class="fa fa-check"></i>&nbsp;<p>GSTIN :&nbsp;</p>
                                                                                <p> <?= $data['vendor_gst'] ?></p>
                                                                                <div class="divider"></div>
                                                                            </div>
                                                                            <div class="col-lg-4 col-md-4 col-sm-12 display-flex customer-status"><i class="fa fa-check"></i>&nbsp;<p>Status :&nbsp;</p>
                                                                                <p class="status"> active</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <?php
                                                        $location = "SELECT * FROM  `erp_branch_otherslocation` WHERE `othersLocation_id`='" . $location_id . "' ";
                                                        $locConn = queryGet($location);
                                                        $locData = $locConn['data'];
                                                        // console($locData['othersLocation_building_no']);
                                                        $otherLocation = "SELECT * FROM  `erp_branch_otherslocation` WHERE `company_id`='" . $company_id . "' ";
                                                        $otherLocConn = queryGet($otherLocation, true);
                                                        $otherLocData = $otherLocConn['data'];

                                                        ?>

                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                                <div class="row address-section">
                                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                                        <div class="address-to bill-to">
                                                                            <h5>Bill to</h5>
                                                                            <hr class="mt-0 mb-2">
                                                                            <p>
                                                                                <?=
                                                                                $locData['othersLocation_building_no'] . "," . $locData['othersLocation_flat_no'] . "," . $locData['othersLocation_street_name'] . "," . $locData['othersLocation_pin_code'] . "," .  $locData['othersLocation_location'] . "," . $locData['othersLocation_district'] . "," .  $locData['othersLocation_city'] . "," .  $locData['othersLocation_state']
                                                                                ?>
                                                                            </p>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                                        <div class="address-to ship-to">
                                                                            <div class="row">
                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                    <h5>Ship to</h5>
                                                                                </div>
                                                                                <div class="col-lg-8 col-md-8 col-sm-8">
                                                                                    <h5 class="display-inline">
                                                                                        <div class="checkbox-label">
                                                                                            <input type="checkbox" id="addresscheckbox" name="addresscheckbox" value="1" title="checked here for same as Bill To adress" checked>
                                                                                            <p>Same as Bill to</p>
                                                                                        </div>
                                                                                        <button type="button" class="btn btn-primary change-address-modal text-xs btn-sm" data-toggle="modal" data-target="#address-change">Change</button>
                                                                                    </h5>
                                                                                </div>
                                                                            </div>



                                                                            <hr class="mt-0 mb-2">
                                                                            <p id="shipToAddressDiv">
                                                                                <?=
                                                                                $locData['othersLocation_building_no'] . "," . $locData['othersLocation_flat_no'] . "," . $locData['othersLocation_street_name'] . "," . $locData['othersLocation_pin_code'] . "," .  $locData['othersLocation_location'] . "," . $locData['othersLocation_district'] . "," .  $locData['othersLocation_city'] . "," .  $locData['othersLocation_state']
                                                                                ?>
                                                                            </p>
                                                                            <input type="hidden" name="shipToInput" id="shipToInput" value="<?= $locData['othersLocation_id'] ?>">
                                                                            <input type="hidden" name="shipToState" id="shipToState" value="<?= $locData['state_code'] ?>">



                                                                        </div>

                                                                        <!----------Address modal-------->

                                                                        <div class="modal fade address-change-modal" id="address-change" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                                            <div class="modal-dialog" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header card-header">
                                                                                        <div class="head">
                                                                                            <i class="fa fa-map-marker-alt"></i>
                                                                                            <h4>Change Address</h4>
                                                                                        </div>
                                                                                        <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                                                                            <li class="nav-item" role="presentation">
                                                                                                <button class="btn btn-primary address-btn otheraddressbtn nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#savedAddress" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Other Address</button>

                                                                                            </li>
                                                                                            <li class="nav-item" role="presentation">
                                                                                                <button class="btn btn-primary address-btn newaddress nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#newAddress" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">New Address</button>
                                                                                            </li>
                                                                                        </ul>
                                                                                    </div>

                                                                                    <div class="modal-body" style="height:15rem;">

                                                                                        <div class="tab-content " id="pills-tabContent">
                                                                                            <div class="tab-pane otherAddress-tab-pen fade show active" id="savedAddress" role="tabpanel" aria-labelledby="pills-home-tab">
                                                                                                <?php
                                                                                                foreach ($otherLocData as $data) {
                                                                                                ?>

                                                                                                    <div class="address-to bill-to">
                                                                                                        <input type="radio" class="address-check" name="shipToAddress" value="<?= $data['othersLocation_id'] ?>">
                                                                                                        <h5 id="shipToAddressHeadText_<?= $data['othersLocation_id'] ?>"><?= $data['othersLocation_name'] ?></h5>
                                                                                                        <hr class="mt-0 mb-2">
                                                                                                        <p id="shipToAddressBodyText_<?= $data['othersLocation_id'] ?>">

                                                                                                            <?=
                                                                                                            $data['othersLocation_building_no'] . "," . $data['othersLocation_flat_no'] . "," . $data['othersLocation_street_name'] . "," . $data['othersLocation_pin_code'] . "," .  $data['othersLocation_location'] . "," . $data['othersLocation_district'] . "," .  $data['othersLocation_city'] . "," .  $data['othersLocation_state']
                                                                                                            ?>

                                                                                                        </p>
                                                                                                        <p id="shipToStateCode_<?= $data['othersLocation_id'] ?>">
                                                                                                            <?= $data['state_code'] ?>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                <?php
                                                                                                }
                                                                                                ?>
                                                                                                <div class="modal-footer">
                                                                                                    <button type="button" id="closeBtn" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                                    <button type="button" class="btn btn-primary" id="shipToAddressSaveBtn" data-dismiss="modal">Save changes</button>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="tab-pane newAddress-tab-pen fade" id="newAddress" role="tabpanel" aria-labelledby="pills-profile-tab">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                                                                                                        <label for="">Building Number</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                                                                                                        <label for="">Flat Number</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                                                                                                        <label for="">Street Name</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                                                                                        <label for="">Location</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">City</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">Pin Code</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">District</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">State</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12"></div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12"></div>
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="card so-creation-card po-creation-card  po-creation-card">
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
                                <div class="card-body others-info">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="row info-form-view">

                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <label for="date">Delivery Date</label>
                                                    <input type="date" id="deliveryDate" name="deliveryDate" min="<?= $today ?>" class="form-control" value="<?= $_POST["date"] ?>" />
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <label for="date">PO Creation Date</label>
                                                    <input type="date" name="podatecreation" id="podatecreation" class="form-control" min="<?= $min ?>" max="<?= $max ?>">
                                                    <p id="podatelabel"></p>
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-input">
                                                        <label for="date">Validity Period</label>
                                                        <input type="date" class="form-control" id="dateInputvalid" name="validitydate" min="<?php echo date('Y-m-d'); ?>" required>
                                                        <p id="validitylabel"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row info-form-view">
                                                <div class="col-lg-3 col-md-3 col-sm-12 form-inline">
                                                    <label for="date">Use Types</label>
                                                    <select onclick="craateUserJsObject.ShowUseTypes();" name="usetypesDropdown" id="usetypesDropdown" class="form-control typesDropdown">
                                                        <option value="">Select</option>
                                                        <option value="material" selected>Material</option>
                                                        <option value="servicep">Service Purchase</option>
                                                        <option value="asset">Asset</option>
                                                    </select>

                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-12" id="parent_div" style="display:none;">
                                                    <label for="date">Select Parent PO</label>
                                                    <select id="parent" class="form-control parent" name="parent_po">
                                                        <option value="">Select</option>
                                                        <?php

                                                        $get_po = queryGet("SELECT * FROM `erp_branch_purchase_order` WHERE `location_id` = $location_id", true);
                                                        foreach ($get_po['data'] as $parent) {

                                                        ?>
                                                            <option value="<?= $parent['po_id'] ?>"><?= $parent['po_number'] ?></option>
                                                        <?php
                                                        }
                                                        ?>

                                                    </select>

                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-12 cost-center-col">
                                                    <div class="cost-center" style="display: none;">
                                                        <label for="">Cost Center</label>
                                                        <select name="costCenter" class="form-control">
                                                            <option value="">Cost Center</option>
                                                            <?php
                                                            $funcList = $BranchPoObj->fetchFunctionality()['data'];
                                                            foreach ($funcList as $func) {
                                                            ?>
                                                                <option value="<?= $func['CostCenter_id'] ?>">
                                                                    <?= $func['CostCenter_code'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-12 cost-center-col">
                                                    <label for="">PO Type</label>
                                                    <select name="potypes" id="potypes" onclick="craateUserJsObject.ShowPoTypes();" class="form-control typesDropdown">
                                                        <option value="">Select PO Type</option>
                                                        <option id="domestic" value="domestic">Domestic</option>
                                                        <option id="international" value="international">International
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-12 radio-condition" id="incoTerms">
                                                    <div class="radio-types radio-types-fob-cif" style="display: none;">
                                                        <label for="" class="inco-terms">Inco Terms</label>
                                                        <div class="form-input-radio form-input-fob">

                                                            <input type="radio" value="FOB" name="domestic">
                                                            <div class="tooltip-label">
                                                                <label for="">FOB</label>
                                                                <div class="help-tip fob-tooltip">
                                                                    <p>Free On Board or Freight on Board</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-input-radio form-input-cif">
                                                            <input type="radio" value="CIF" name="domestic">
                                                            <div class="tooltip-label">
                                                                <label for="">CIF</label>
                                                                <div class="help-tip cif-tooltip">
                                                                    <p>Cost, insurance, and freight is an international
                                                                        shipping agreement</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="radio-types radio-types-ex-for" style="display: none;">
                                                        <div class="form-input-radio form-input-ex-work">
                                                            <label for="" class="inco-terms">Inco Terms</label>
                                                            <input type="radio" value="exwork" name="domestic">
                                                            <div class="tooltip-label">
                                                                <label for="">Ex Work</label>
                                                                <div class="help-tip ex-work-tooltip">
                                                                    <p>An domestic trade term that describes when a
                                                                        seller makes a product available at a designated
                                                                        location</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-input-radio form-input-for">
                                                            <input type="radio" value="FOR" name="domestic">
                                                            <div class="tooltip-label">
                                                                <label for="">FOR</label>
                                                                <div class="help-tip for-tooltip">
                                                                    <p>F.O.R. stands for “Free on Road” means the goods which is being sent from source station to its destination includes transportation and all other transit expenses. All applicable taxes and duties on goods remains extra.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row info-form-view">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="tooltip-label">
                                                        <label for="">Reference</label>
                                                        <div class="help-tip">
                                                            <p>Vendor PO</p>
                                                        </div>
                                                    </div>
                                                    <input type="text" name="refNo" class="form-control" value="<?= $rfq_code ?>" />
                                                </div>


                                                <?php

                                                $check_func = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id`=$location_id");
                                                $funcs = $check_func['data']['companyFunctionalities'];
                                                $func_ex = explode(",", $funcs);



                                                ?>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="func-area">
                                                        <label for="">Functional Area</label>
                                                        <select name="funcArea" class="form-control">
                                                            <option value="">Functional Area</option>
                                                            <?php

                                                            foreach ($func_ex as $func) {
                                                                $func_area = queryGet("SELECT * FROM `erp_company_functionalities` WHERE `functionalities_id`=$func", true);
                                                                //console($func_area);
                                                            ?>

                                                                <option value="<?= $func_area['data'][0]['functionalities_id'] ?>"><?= $func_area['data'][0]['functionalities_name'] ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>


                                                <!-- 
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-input">
                                                        <label for="" class="label-hidden">Label</label>
                                                        <div class="static-currency">
                                                            <input type="text" class="form-control" value="1" readonly="">
                                                            <input type="text" class="form-control text-right" value="INR" readonly="">
                                                        </div>
                                                    </div>
                                                </div> -->

                                                <!-- <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="dynamic-currency border-0">
                                                        <div class="form-input">
                                                            <label for="">Currency Rate</label>
                                                            <input type="number" class="form-control" id="curr_rate" name="curr_rate" value="1">
                                                        </div>
                                                        <div class="form-input">
                                                            <label for="">Customer Currency</label>
                                                            <select id="" name="currency" class="form-control">
                                                                <?php
                                                                $curr = queryGet("SELECT * FROM `erp_currency_type`", true);
                                                                foreach ($curr['data'] as $data) {
                                                                ?>
                                                                    <option value="<?= $data['currency_id'] ?>"><?= $data['currency_name'] ?></option>
                                                                <?php
                                                                }
                                                                ?>

                                                            </select>
                                                        </div>

                                                    </div>
                                                </div> -->
                                                <?php
                                                $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
                                                $companyCurrencyData = $companyCurrencyObj["data"];

                                                $comp_currency = $companyCurrencyData["currency_name"];
                                                ?>

                                                <div class="currency-conversion-section mt-3">
                                                    <div class="static-currency">
                                                        <input type="text" class="form-control" value="1" readonly>
                                                        <input type="text" class="form-control text-right" value="<?= $comp_currency ?>" readonly>
                                                    </div>
                                                    <div class="dynamic-currency">
                                                        <input type="text" name="curr_rate" id="currency_conversion_rate" value="1" class="form-control">
                                                        <select id="selectCurrency" name="currency" class="form-control text-right">
                                                            <?php

                                                            $curr = queryGet("SELECT * FROM `erp_currency_type`", true);
                                                            foreach ($curr['data'] as $data) {
                                                            ?>
                                                                <option value="<?= $data['currency_id'] ?>" data-currname="<?= $data['currency_name'] ?>" <?php if ($comp_currency == $data['currency_name']) {
                                                                                                                                                                echo "selected";
                                                                                                                                                            } ?>><?= $data['currency_name'] ?></option>
                                                            <?php
                                                            }
                                                            ?>

                                                        </select>
                                                    </div>
                                                    <div class="display-flex grn-form-input-text mt-3">
                                                        <p class="label-bold text-italic" style="white-space: pre-line;">Vendor Currency</p>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>





                        <div class="row">
                            <!-- <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row others-info-head">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="head">
                                        <i class="fa fa-info"></i>
                                        <h4>Items Info</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card items-select-table">
                                    <div class="head-item-table">
                                        <div class="advanced-serach">
                                            <form action="" method="POST">



                                        </div>
                                    </div>

                                    <table class="table tabel-hover table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Item Code</th>
                                                <th>Item Name</th>
                                                <th>Qty</th>
                                                <th>Unit Price</th>
                                                <th>Base Amount</th>
                                                <th>GST</th>
                                                <th>GST Amount</th>
                                                <th>Total Price</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsTable">
                                            <?php
                                            // $pr_ite_sql = "SELECT *  FROM `erp_branch_purchase_request_items` WHERE `prId`='" . $id . "' ";
                                            // $pr = queryGet($pr_ite_sql, true);
                                            // $pr_data = $pr['data'];
                                            // // console($pr);
                                            // foreach ($pr_data as $data) {
                                            // console($data['itemId']);
                                            // $qty = $data['itemQuantity'];
                                            // $itemId = $data['itemId'];
                                            // $getItemObj = $ItemsObj->getItemById($itemId);
                                            // // console($getItemObj);
                                            // $itemCode = $getItemObj['data']['itemCode'];
                                            // $lastPricesql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` WHERE `itemCode`=$itemCode ORDER BY po_item_id DESC LIMIT 1";
                                            // $last = queryGet($lastPricesql);
                                            // $lastRow = $last['data'] ?? "";
                                            // $lastPrice = $lastRow['unitPrice'] ?? "";

                                            $total_value = 0;

                                            foreach ($_POST["items"] as $key => $value) {
                                                //   console($value);
                                                if ($value['item_qty'] > 0) {
                                                    $item_query = "SELECT * FROM erp_inventory_items WHERE itemId=$key AND  status='active' AND company_id=$company_id";
                                                    $itemdata = queryGet($item_query);
                                                    // console($itemdata);
                                                    $itemId = $itemdata['data']['itemId'];
                                                    // $randCode = $getItemObj['data']['itemId'] . rand(00, 99);

                                                    $basePrice = $value['item_qty'] * $value['price'];

                                                    $randCode = $key . rand(00, 99);
                                                    $hsn = $itemdata['data']['hsnCode'];
                                                    $gstPercentage = queryGet("SELECT * FROM `erp_hsn_code` WHERE `hsnCode` = '" . $hsn . "'");
                                                    //  console($gstPercentage);


                                                    $gstAmount = ($gstPercentage['data']['taxPercentage'] / 100) * $basePrice;
                                                    $totalAmount = $basePrice + $gstAmount;
                                            ?>
                                                    <input type="hidden" name="listItem[<?= $randCode ?>][pr_id]" value="<?= $data['prId'] ?>">
                                                    <tr class="rowDel itemRow" id="delItemRowBtn_<?= $key ?>">
                                                        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemId]" value="<?= $key ?>">
                                                        <td>
                                                            <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemCode]" value="<?= $itemdata["data"]["itemCode"] ?>" readonly>
                                                            <?= $itemdata["data"]["itemCode"] ?>
                                                        </td>
                                                        <td>
                                                            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][itemName]" value="<?= $itemdata["data"]["itemName"] ?>" readonly>
                                                            <?= $itemdata["data"]["itemName"] ?>
                                                        </td>
                                                        <td>
                                                            <div class="flex-display">
                                                                <input type="number" step="any" name="listItem[<?= $randCode ?>][qty]" value="<?= $value['item_qty'] ?>" class="form-control full-width itemQty" id="itemQty_<?= $randCode ?>" readonly>
                                                                <?= $ItemsObj->getBaseUnitMeasureById($itemdata['data']['baseUnitMeasure'])['data']['uomName'] ?>
                                                                <input type="hidden" name="listItem[<?= $randCode ?>][uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($itemdata['data']['baseUnitMeasure'])['data']['uomName'] ?>">
                                                            </div>

                                                        </td>
                                                        <td>
                                                            <input type="text" name="listItem[<?= $randCode ?>][unitPrice]" id="" value="<?= $value['price'] ?>" class="form-control full-width-center itemUnitPrice" readonly>
                                                            <input type="hidden" name="listItem[<?= $randCode ?>][unitPriceHidden]" value="<?= $value['price'] ?>" id="ItemUnitPriceTdInputhidden_<?= $randCode ?>" class="form-control text-xs itemUnitPricehidden">


                                                        </td>
                                                        <!-- <td class="flex-display">
                                                            <input type="number" name="listItem[<?= $randCode ?>][totalDiscount]" value="0.00" class="form-control full-width-center itemDiscount">%
                                                        </td> -->
                                                        <td>
                                                            <input type="number" name="listItem[<?= $randCode ?>][basePrice]" value="<?= $basePrice ?>" class="form-control full-width-center itemBasePrice" id="itemBasePrice_<?= $randCode ?>" readonly>
                                                            <div class="d-flex gap-2 my-1">
                                                                <?= $comp_currency ?> <p id="local_base_price_<?= $randCode ?>">0.00</p>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <input type="number" name="listItem[<?= $randCode ?>][gst]" value="<?= $gstPercentage['data']['taxPercentage'] ?>" class="form-control full-width-center gst" id="gst_<?= $randCode ?>" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="listItem[<?= $randCode ?>][gstAmount]" value="<?= $gstAmount ?>" class="form-control full-width-center gstAmount" id="gstAmount_<?= $randCode ?>" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="listItem[<?= $randCode ?>][totalPrice]" id="" value="<?= $totalAmount ?>" class="form-control full-width-center itemTotalPrice" readonly>
                                                        </td>
                                                        <td class="action-flex-btn">

                                                            <button type="button" class="btn-view btn btn-primary" data-toggle="modal" data-target="#deliveryScheduleModal_<?= $randCode ?>">
                                                                <i class="statusItemBtn fa fa-cog" id="statusItemBtn_<?= $itemId ?>"></i>
                                                            </button>

                                                            <button type="button" class="btn btn-danger delItemBtn" id="delItemBtn_<?= $itemId ?>">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                        </td>



                                                        <div class="modal modal-left left-item-modal fade" id="deliveryScheduleModal_<?= $randCode ?>" tabindex="-1" role="dialog" aria-labelledby="left_modal">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Delivery Shedule <?= $randCode ?></h5>
                                                                        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button> -->
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <!-- <h6 class="modal-title">Total Qty: <span class="mainQty" id="mainQty_<?= $randCode ?>">1</span></h6> -->
                                                                        <div class="row">


                                                                            <div class="col-lg-12 col-md-12 col-sm-12  modal-add-row modal-add-row-delivery_<?= $randCode ?>">

                                                                                <div class="row">
                                                                                    <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
                                                                                        <div class="form-input">
                                                                                            <label>Delivery date</label>
                                                                                            <input type="date" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][multiDeliveryDate]" class="form-control" id="delivery-date" placeholder="delivery date" value="">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                                                                        <div class="form-input">
                                                                                            <label>Quantity</label>
                                                                                            <input type="number" step="any" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][quantity]" class="form-control multiQuantity" id="multiQuantity_<?= $randCode ?>" placeholder="quantity" value="">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                                                                        <div class="add-btn-plus">
                                                                                            <a style="cursor: pointer" class="btn btn-primary waves-effect waves-light" onclick='addDeliveryQty(<?= $randCode ?>)'>
                                                                                                <i class="fa fa-plus"></i>
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer modal-footer-fixed">
                                                                        <button type="submit" class="btn btn-primary save-close-btn btn-xs float-right waves-effect waves-light" data-dismiss="modal">Save & Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </td>
                                                    </tr>







                                            <?php  }
                                                $total_value += $value['item_qty'] * $value['price'];
                                            }
                                            ?>
                                        </tbody>
                                        <!-- <tbody class="total-calculate">
                                            <tr>
                                                <td colspan="4" class="text-right" style="border: none;"> </td>
                                                <td colspan="0" class="text-right" style="border: none;"><b>Total Amount</b></td>
                                                <input type="hidden" name="totalAmt" id="grandTotalAmountInput" value="<?= $total_value ?>">
                                                <td colspan="2" style="border: none; background: none; " id="grandTotalAmount"><b><?= $total_value ?></b></th>
                                            </tr>

                                        </tbody> -->

                                        <tbody class="total-calculate purchase-order-item-list">
                                            <tr>
                                                <td colspan="6" class="text-right p-2" style="border: none; background: none;"> </td>
                                                <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Sub Total</td>
                                                <input type="hidden" name="subTotal" id="subTotalAmountInput" value="0.00">
                                                <td class="text-right pr-2" style="border: none; background: none;">
                                                    <small class="text-large font-weight-bold text-success">
                                                        <span class="rupee-symbol">INR </span><span id="subTotalAmount">0.00</span>
                                                    </small>
                                                    <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                        (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span id="">0.00</span>)
                                                    </small> -->
                                                </td>
                                            </tr>


                                            <tr class="p-2 igstTr" style="display:none">
                                                <td colspan="6" class="text-right p-2" style="border: none; background: none;"> </td>
                                                <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">IGST</td>
                                                <input type="hidden" name="igstInput" id="igst" value="0.00">
                                                <td class="text-right pr-2" style="border: none; background: none;">
                                                    <small class="text-large font-weight-bold text-success">
                                                        <span class="rupee-symbol">INR </span><span id="igst_span">0.00</span>
                                                    </small>
                                                    <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                        (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span id="">0.00</span>)
                                                    </small> -->
                                                </td>
                                            </tr>
                                            <tr class="p-2 cgstTr" style="display:none">
                                                <td colspan="6" class="text-right p-2" style="border: none; background: none;"> </td>
                                                <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">CGST</td>
                                                <input type="hidden" name="cgstInput" id="cgst" value="0.00">
                                                <td class="text-right pr-2" style="border: none; background: none;">
                                                    <small class="text-large font-weight-bold text-success">
                                                        <span class="rupee-symbol">INR </span><span id="cgst_span">0.00</span>
                                                    </small>
                                                    <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                        (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span class="">0</span>)
                                                    </small> -->
                                                </td>
                                            </tr>
                                            <tr class="p-2 sgstTr" style="display:none">
                                                <td colspan="6" class="text-right p-2" style="border: none; background: none;"> </td>
                                                <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">SGST</td>
                                                <input type="hidden" name="sgstInput" id="sgst" value="0.00">
                                                <td class="text-right pr-2" style="border: none; background: none;">
                                                    <small class="text-large font-weight-bold text-success">
                                                        <span class="rupee-symbol">INR </span><span id="sgst_span">0.00</span>
                                                    </small>
                                                    <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                        (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span class="">0</span>)
                                                    </small> -->
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td colspan="2">

                                                </td>
                                            </tr>
                                            <tr class="p-2">
                                                <td colspan="6" class="text-right p-2" style="border: none; background: none;"> </td>
                                                <td colspan="0" class="text-left p-2 font-weight-bold totalCal" style="border-top: 3px double !important; background: none;padding: 0px !important;">Total Amount</td>
                                                <input type="hidden" name="totalAmt" id="grandTotalAmountInput" value="0.00">
                                                <td class="font-weight-bold text-right pr-2" style="border-top: 3px double !important; background: none;">
                                                    <small class="text-large font-weight-bold text-success">
                                                        <span class="rupee-symbol">INR </span><span id="grandTotalAmount">0.00</span>
                                                    </small>
                                                    <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                        (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span id="">0.00</span>)
                                                    </small>
                                                </td>
                                            </tr>
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card so-creation-card po-creation-card  po-creation-card po-others-info">
                                    <div class="card-header">
                                        <div class="row others-info-head">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="head">
                                                    <i class="fa fa-info"></i>
                                                    <h4>Others Cost infos</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body others-accordion-info" id="cost">

                                        <div class="row accordion-other-cost">
                                            <div class="col">
                                                <div class="tabs">
                                                    <div class="tab">
                                                        <input type="checkbox" id="chck1" style="display: none;">
                                                        <label class="tab-label" for="chck1">Freight & Other Cost</label>
                                                        <div class="tab-content">
                                                            <div class="row othe-cost-infor modal-add-row_537">
                                                                <div class="row othe-cost-infor">
                                                                    <div class="col-lg-3 col-md-12 col-sm-12">
                                                                        <div class="form-input">
                                                                            <label for="">Service Select</label>
                                                                            <select class="form-control" id="" name="FreightCost[l1][service_purchase_id]">
                                                                                <option value="">Select Service</option>

                                                                                <?php
                                                                                $service_select = queryGet("SELECT * FROM `erp_inventory_items` WHERE `goodsType`=7 AND `location_id`=$location_id", true);
                                                                                //console($service_select);
                                                                                foreach ($service_select['data'] as $service) {
                                                                                ?>

                                                                                    <option value="<?= $service['itemId'] ?>">[<?= $service['itemCode'] ?>] <?= $service['itemName'] ?></option>

                                                                                <?php

                                                                                }


                                                                                ?>

                                                                            </select>
                                                                        </div>
                                                                    </div>



                                                                    <div class="col-lg-3 col-md-12 col-sm-12">
                                                                        <div class="form-input">
                                                                            <label for="">Vendor Select</label>
                                                                            <select class="form-control" name="FreightCost[l1][service_vendor]">
                                                                                <option value="">Select Vendor</option>
                                                                                <?php echo $vendrSelect;     ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-3 col-md-12 col-sm-12">
                                                                        <div class="form-input">
                                                                            <label for="">Service Description</label>
                                                                            <input type="text" class="form-control" placeholder="service description" name="FreightCost[l1][service_desc]">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-3 col-md-12 col-sm-12">
                                                                        <div class="form-input">
                                                                            <label for="">Amount</label>
                                                                            <input type="number" class="form-control amount" id="amount" placeholder="amount" name="FreightCost[l1][service_amount]">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg col-md-6 col-sm-6">
                                                                        <div class="add-btn-plus">
                                                                            <a style="cursor: pointer" class="btn btn-primary" onclick="addMultiQtyf(537)">
                                                                                <i class="fa fa-plus"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- 
                                                <div class="tab">
                                                    <input type="checkbox" id="chck2" style="display: none;">
                                                    <label class="tab-label" for="chck2">Others Cost</label>
                                                    <div class="tab-content">



                                                        <div class="row othe-cost-infor modal-add-row_538">
                                                            <div class="row othe-cost-infor">


                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Select</label>
                                                                        <select class="form-control otherServiceDropDown" id="otherServiceDropDown" name="OthersCost[13][service_purchase_id]">
                                                                            <option value="">Select Service</option>

                                                                            <?php
                                                                            $service_select = queryGet("SELECT * FROM `erp_inventory_items` WHERE `goodsType`=7 AND `location_id`=$location_id", true);

                                                                            foreach ($service_select['data'] as $service) {
                                                                            ?>

                                                                                <option value="<?= $service['itemId'] ?>">[<?= $service['itemCode'] ?>] <?= $service['itemName'] ?></option>

                                                                            <?php

                                                                            }


                                                                            ?>

                                                                        </select>
                                                                    </div>
                                                                </div>




                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Vendor Name</label>
                                                                        <input type="text" class="form-control" placeholder="vendor name" name="OthersCost[13][service_vendor]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="description" name="OthersCost[13][service_desc]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Amount</label>
                                                                        <input type="number" class="form-control other_amount" placeholder="amount" id="other_amount" name="OthersCost[13][service_amount]">
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="add-btn-plus">
                                                                        <a style="cursor: pointer" class="btn btn-primary" onclick="addMultiQty(538)">
                                                                            <i class="fa fa-plus"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                                </div>
                                            </div>
                                        </div>


                                    </div>


                                </div>

                                <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-primary save-close-btn btn-xs float-right add_data" id="pobtn" value="add_post">Save & Close</button>
                                <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-danger save-close-btn btn-xs float-right add_data" id="podbtn" value="add_draft">Save as Draft</button>
                            </div>
                        </div>
                    </div>
            </div>

            </form>
            <!-- modal -->
            <div class="modal" id="addNewItemsFormModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header py-1" style="background-color: #003060; color:white;">
                            <h4 class="modal-title">Add New Items</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <!-- <form action="" method="post" id="addNewItemsForm"> -->
                            <div class="col-md-12 mb-3">
                                <div class="input-group">
                                    <input type="text" name="itemName" class="m-input" required>
                                    <label>Item Name</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="text" name="itemDesc" class="m-input" required>
                                    <label>Item Description</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group btn-col">
                                    <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                                </div>
                            </div>
                            <!-- </form> -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- modal end -->
    </div>
    </section>
    </div>



<?php
} elseif (isset($_GET['po-creation'])) { ?>
    <div class="content-wrapper is-purchase-order">
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
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Purchase Order List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Purchase Order</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>

                <form action="" method="POST" id="submitPoForm" name="submitPoForm" onsubmit="return validationfunction()" enctype="multipart/form-data">
                <input type="hidden" name="poOrigin" value="default">
                    <input type="hidden" name="createData" id="createData" value="">
                    <div class="row po-form-creation">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="card so-creation-card po-creation-card purchase-order-card">
                                        <div class="card-header">
                                            <div class="row customer-info-head">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="head">
                                                        <i class="fa fa-user"></i>
                                                        <h4>Vendor Info <span class="text-danger">*</span></h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body others-info vendor-info so-card-body">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="row info-form-view">
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <div class="input-box customer-select">

                                                                <select name="vendorId" id="vendorDropdown" class="selct-vendor-dropdown">
                                                                    <option value="">Select Vendor</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <div class="customer-info-text po-customer-info-text pt-3" id="vendorInfo">

                                                            </div>

                                                        </div>
                                                        <?php
                                                        $location = "SELECT * FROM  `erp_branch_otherslocation` WHERE `othersLocation_id`='" . $location_id . "' ";
                                                        $locConn = queryGet($location);
                                                        $locData = $locConn['data'];
                                                        // console($locData['othersLocation_building_no']);
                                                        $otherLocation = "SELECT * FROM  `erp_branch_otherslocation` WHERE `company_id`='" . $company_id . "' ";
                                                        $otherLocConn = queryGet($otherLocation, true);
                                                        $otherLocData = $otherLocConn['data'];

                                                        ?>

                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                                <div class="row address-section">
                                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                                        <div class="address-to bill-to">
                                                                            <h5>Bill to</h5>
                                                                            <hr class="mt-0 mb-2">
                                                                            <p>
                                                                                <?=
                                                                                $locData['othersLocation_building_no'] . "," . $locData['othersLocation_flat_no'] . "," . $locData['othersLocation_street_name'] . "," . $locData['othersLocation_pin_code'] . "," .  $locData['othersLocation_location'] . "," . $locData['othersLocation_district'] . "," .  $locData['othersLocation_city'] . "," .  $locData['othersLocation_state']
                                                                                ?>
                                                                            </p>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                                        <div class="address-to ship-to">
                                                                            <div class="row">
                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                    <h5>Ship to</h5>
                                                                                </div>
                                                                                <div class="col-lg-8 col-md-8 col-sm-8">
                                                                                    <h5 class="display-inline">
                                                                                        <div class="checkbox-label">
                                                                                            <input type="checkbox" id="addresscheckbox" title="checked here for same as Bill To adress" data-toggle="modal" data-target="" checked>
                                                                                            <p>Same as Bill to</p>
                                                                                        </div>
                                                                                        <button type="button" class="btn btn-primary change-address-modal text-xs btn-sm" data-toggle="modal" data-target="#address-change">Change</button>
                                                                                    </h5>
                                                                                </div>
                                                                            </div>



                                                                            <hr class="mt-0 mb-2">
                                                                            <p id="shipToAddressDiv">


                                                                                <?=
                                                                                $locData['othersLocation_building_no'] . "," . $locData['othersLocation_flat_no'] . "," . $locData['othersLocation_street_name'] . "," . $locData['othersLocation_pin_code'] . "," .  $locData['othersLocation_location'] . "," . $locData['othersLocation_district'] . "," .  $locData['othersLocation_city'] . "," .  $locData['othersLocation_state'] . "," . $locData['state_code']
                                                                                ?>




                                                                            </p>

                                                                            <input type="hidden" name="shipToInput" id="shipToInput" value="<?= $locData['othersLocation_id'] ?>">
                                                                            <input type="hidden" name="shipToState" id="shipToState" value="<?= $locData['state_code'] ?>">

                                                                        </div>

                                                                        <!----------Address modal-------->

                                                                        <div class="modal fade address-change-modal" id="address-change" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable mt-0" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header card-header">
                                                                                        <div class="head">
                                                                                            <i class="fa fa-map-marker-alt"></i>
                                                                                            <h4>Change Address</h4>
                                                                                        </div>
                                                                                        <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                                                                            <li class="nav-item" role="presentation">
                                                                                                <button class="btn btn-primary address-btn otheraddressbtn nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#savedAddress" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Other Address</button>
                                                                                            </li>
                                                                                            <li class="nav-item" role="presentation">
                                                                                                <button class="btn btn-primary address-btn newaddress nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#newAddress" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">New Address</button>
                                                                                            </li>
                                                                                        </ul>
                                                                                    </div>

                                                                                    <div class="modal-body" style="height:15rem;">

                                                                                        <div class="tab-content " id="pills-tabContent">
                                                                                            <div class="tab-pane otherAddress-tab-pen fade show active" id="savedAddress" role="tabpanel" aria-labelledby="pills-home-tab">
                                                                                                <?php
                                                                                                foreach ($otherLocData as $data) {
                                                                                                ?>

                                                                                                    <div class="address-to bill-to">
                                                                                                        <input type="radio" class="address-check" name="shipToAddress" value="<?= $data['othersLocation_id'] ?>">
                                                                                                        <h5 id="shipToAddressHeadText_<?= $data['othersLocation_id'] ?>"><?= $data['othersLocation_name'] ?></h5>
                                                                                                        <hr class="mt-0 mb-2">
                                                                                                        <p id="shipToAddressBodyText_<?= $data['othersLocation_id'] ?>">

                                                                                                            <?=
                                                                                                            $data['othersLocation_building_no'] . "," . $data['othersLocation_flat_no'] . "," . $data['othersLocation_street_name'] . "," . $data['othersLocation_pin_code'] . "," .  $data['othersLocation_location'] . "," . $data['othersLocation_district'] . "," .  $data['othersLocation_city'] . "," .  $data['othersLocation_state'] . "," .  $data['state_code']
                                                                                                            ?>

                                                                                                        </p>
                                                                                                        <p id="shipToStateCode_<?= $data['othersLocation_id'] ?>">
                                                                                                            <?= $data['state_code'] ?>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                <?php
                                                                                                }
                                                                                                ?>
                                                                                                <div class="modal-footer px-0">
                                                                                                    <button type="button" id="closeBtn" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                                    <button type="button" class="btn btn-primary" id="shipToAddressSaveBtn" data-dismiss="modal">Save changes</button>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="tab-pane newAddress-tab-pen fade" id="newAddress" role="tabpanel" aria-labelledby="pills-profile-tab">

                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for=""> Location Name</label>
                                                                                                        <input type="text" name="loc_name" id="loc_name" class="form-control">
                                                                                                    </div>

                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">Building Number</label>
                                                                                                        <input type="text" name="buildingName" id="buildingName" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">Flat Number</label>
                                                                                                        <input type="text" name="flatNumber" id="flatNumber" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">Street Name</label>
                                                                                                        <input type="text" name="streetName" id="streetName" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                                                                                        <label for="">Location</label>
                                                                                                        <input type="text" name="newLocation" id="newLocation" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">City</label>
                                                                                                        <input type="text" name="newCity" id="newCity" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">Pin Code</label>
                                                                                                        <input type="text" name="newPinCode" id="newPinCode" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">District</label>
                                                                                                        <input type="text" name="newDistrict" id="newDistrict" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">State</label>
                                                                                                        <input type="text" name="newState" id="newState" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">Latitude</label>
                                                                                                        <input type="text" name="lat" id="lat" class="form-control">

                                                                                                    </div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">Longitude</label>
                                                                                                        <input type="text" name="lng" id="lng" class="form-control">

                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="modal-footer px-0">

                                                                                                    <button type="button" class="btn btn-primary" id="addNewAddressBtn">Save</button>
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="card so-creation-card po-creation-card purchase-order-card">
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




                                            <div class="row info-form-view" style="row-gap: 5px;">
                                                <div class="col-lg-12 col-md-12 col-sm-12 dotted-border-area">
                                                    <div class="row align-items-end">
                                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                                            <div class="form-input">
                                                                <div class="tooltip-label">
                                                                    <label for="">Create PO without PR</label>
                                                                    <div class="help-tip">
                                                                        <p>Vendor PO</p>
                                                                    </div>
                                                                </div>
                                                                <input type="text" name="refNo" class="form-control" placeholder="Reference Number" />
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                            <label for="" class="font-bold">Or</label>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                                            <div class="form-input">
                                                                <label for="">Create PO with PR</label>
                                                                <button type="button" class="btn btn-primary change-address-modal text-xs btn-sm" data-toggle="modal" data-target="#select-pr">Select PR</button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <p class="note-text font-bold font-italic pt-2">Note: Required Data will be fetched from the PR </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 col-md-4 col-sm-12">
                                                    <label for="date">Delivery Date</label>
                                                    <input type="date" id="deliveryDate" name="deliveryDate" min="<?= $today ?>" class="form-control" />
                                                </div>
                                                <div class="col-lg-3 col-md-4 col-sm-12">
                                                    <label for="date">PO Creation Date</label>
                                                    <input type="date" name="podatecreation" id="podatecreation" class="form-control" min="<?= $min ?>" max="<?= $max ?>">
                                                    <p id="podatelabel"></p>
                                                </div>
                                                <div class="col-lg-3 col-md-4 col-sm-12">
                                                    <div class="form-input">
                                                        <label for="date">Validity Period</label>
                                                        <input type="date" class="form-control" id="dateInputvalid" name="validitydate" min="<?php echo date('Y-m-d'); ?>" value="<?= $row['validityperiod'] ?>" required>
                                                        <p id="validitylabel"></p>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-12">
                                                    <label for="date">Use Types</label>
                                                    <select onclick="craateUserJsObject.ShowUseTypes();" name="usetypesDropdown" id="usetypesDropdown" class="form-control typesDropdown">
                                                        <option value="">Select</option>
                                                        <option value="material">Material</option>
                                                        <option value="servicep">Service Purchase</option>
                                                        <option value="asset">Asset</option>
                                                    </select>

                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-12" id="parent_div" style="display:none;">
                                                    <label for="date">Select Parent PO</label>
                                                    <select id="parent" class="form-control parent" name="parent_po">
                                                        <option value="">Select</option>
                                                        <?php

                                                        $get_po = queryGet("SELECT * FROM `erp_branch_purchase_order` WHERE `location_id` = $location_id", true);
                                                        foreach ($get_po['data'] as $parent) {

                                                        ?>
                                                            <option value="<?= $parent['po_id'] ?>"><?= $parent['po_number'] ?></option>
                                                        <?php
                                                        }
                                                        ?>

                                                    </select>

                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-12 cost-center-col">
                                                    <div class="cost-center" style="display: none;">
                                                        <label for="">Cost Center</label>
                                                        <select name="costCenter" class="form-control">
                                                            <option value="">Cost Center</option>
                                                            <?php
                                                            $funcList = $BranchPoObj->fetchFunctionality()['data'];
                                                            foreach ($funcList as $func) {
                                                            ?>
                                                                <option value="<?= $func['CostCenter_id'] ?>">
                                                                    <?= $func['CostCenter_code'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-12 cost-center-col">
                                                    <label for="">PO Type</label>
                                                    <select name="potypes" id="potypes" onclick="craateUserJsObject.ShowPoTypes();" class="form-control typesDropdown">
                                                        <option value="">Select PO Type</option>
                                                        <option id="domestic" value="domestic">Domestic</option>
                                                        <option id="international" value="international">International
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-12 radio-condition" id="incoTerms">
                                                    <div class="radio-types radio-types-fob-cif" style="display: none;">
                                                        <label for="" class="inco-terms">Inco Terms</label>
                                                        <div class="form-input-radio form-input-fob">

                                                            <input type="radio" value="FOB" name="domestic" selected>
                                                            <div class="tooltip-label">
                                                                <label for="">FOB</label>
                                                                <div class="help-tip fob-tooltip">
                                                                    <p>Free On Board or Freight on Board</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-input-radio form-input-cif">
                                                            <input type="radio" value="CIF" name="domestic">
                                                            <div class="tooltip-label">
                                                                <label for="">CIF</label>
                                                                <div class="help-tip cif-tooltip">
                                                                    <p>Cost, insurance, and freight is an international
                                                                        shipping agreement</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="radio-types radio-types-ex-for" style="display: none;">
                                                        <div class="form-input-radio form-input-ex-work">
                                                            <label for="" class="inco-terms">Inco Terms</label>
                                                            <input type="radio" value="exwork" name="domestic">
                                                            <div class="tooltip-label">
                                                                <label for="">Ex Work</label>
                                                                <div class="help-tip ex-work-tooltip">
                                                                    <p>An domestic trade term that describes when a
                                                                        seller makes a product available at a designated
                                                                        location</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-input-radio form-input-for">
                                                            <input type="radio" value="FOR" name="domestic">
                                                            <div class="tooltip-label">
                                                                <label for="">FOR</label>
                                                                <div class="help-tip for-tooltip">
                                                                    <p>F.O.R. stands for “Free on Road” means the goods which is being sent from source station to its destination includes transportation and all other transit expenses. All applicable taxes and duties on goods remains extra.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="tooltip-label">
                                                        <label for="">Reference</label>
                                                        <div class="help-tip">
                                                            <p>Vendor PO</p>
                                                        </div>
                                                    </div>
                                                    <input type="text" name="refNo" class="form-control" placeholder="Reference Number" />
                                                    <div class="tooltip-label-btn">
                                                        <button type="button" class="btn btn-primary change-address-modal text-xs btn-sm" data-toggle="modal" data-target="#select-pr">Select PR</button>
                                                    </div>
                                                </div> -->

                                                <?php

                                                $check_func = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id`=$location_id");
                                                $funcs = $check_func['data']['companyFunctionalities'];
                                                $func_ex = explode(",", $funcs);



                                                ?>
                                                <div class="col-lg-3 col-md-3 col-sm-12">
                                                    <div class="func-area">
                                                        <label for="">Functional Area</label>
                                                        <select name="funcArea" class="form-control">
                                                            <option value="">Functional Area</option>
                                                            <?php

                                                            foreach ($func_ex as $func) {
                                                                $func_area = queryGet("SELECT * FROM `erp_company_functionalities` WHERE `functionalities_id`=$func", true);
                                                                //console($func_area);

                                                            ?>

                                                                <option value="<?= $func_area['data'][0]['functionalities_id'] ?>"><?= $func_area['data'][0]['functionalities_name'] ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-12">
                                                    <div class="func-area">
                                                        <label for="">Attachment</label>
                                                        <input type="file" name="attachment" class="form-control" />
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <!-- <div class="dynamic-currency-conversion">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-input">
                                                                    <label for="">Currency Rate</label>
                                                                    <input type="number" class="form-control" id="curr_rate" name="curr_rate" value="1">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-input">
                                                                    <label for="">Vendor Currency</label>
                                                                    <select id="customer_currency" name="currency" class="form-control">
                                                                        <?php
                                                                        $curr = queryGet("SELECT * FROM `erp_currency_type`", true);
                                                                        foreach ($curr['data'] as $data) {
                                                                        ?>
                                                                            <option value="<?= $data['currency_id'] ?>" data-attr="<?= $data['currency_name'] ?>"><?= $data['currency_name'] ?></option>
                                                                        <?php
                                                                        }
                                                                        ?>

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <?php
                                                    $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
                                                    $companyCurrencyData = $companyCurrencyObj["data"];

                                                    $comp_currency = $companyCurrencyData["currency_name"];
                                                    ?>

                                                    <div class="currency-conversion-section mt-3">
                                                        <div class="static-currency">
                                                            <input type="text" class="form-control" value="1" readonly>
                                                            <input type="text" class="form-control text-right" value="<?= $comp_currency ?>" readonly>
                                                        </div>
                                                        <div class="dynamic-currency">
                                                            <input type="text" name="curr_rate" id="currency_conversion_rate" value="1" class="form-control">
                                                            <select id="selectCurrency" name="currency" class="form-control text-right">
                                                                <?php

                                                                $curr = queryGet("SELECT * FROM `erp_currency_type`", true);
                                                                foreach ($curr['data'] as $data) {
                                                                ?>
                                                                    <option value="<?= $data['currency_id'] ?>" data-currname="<?= $data['currency_name'] ?>" <?php if ($comp_currency == $data['currency_name']) {
                                                                                                                                                                    echo "selected";
                                                                                                                                                                } ?>><?= $data['currency_name'] ?></option>
                                                                <?php
                                                                }
                                                                ?>

                                                            </select>
                                                        </div>
                                                        <div class="display-flex grn-form-input-text mt-3">
                                                            <p class="label-bold text-italic" style="white-space: pre-line;">Vendor Currency</p>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="display-flex" style="justify-content: flex-start;">
                                                        <p class="note-text font-bold text-italic pt-2" style="white-space: pre-line;"><span class="mr-2">*</span>Vendor Currency is <b id="vendor-currency">INR</b></p>
                                                    </div> -->

                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>





                    <div class="row">
                        <!-- <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row others-info-head">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="head">
                                        <i class="fa fa-info"></i>
                                        <h4>Items Info</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card items-select-table">
                                <div class="head-item-table">
                                    <div class="advanced-serach">
                                        <form action="" method="POST">
                                            <div class="hamburger quickadd-hamburger">
                                                <div class="wrapper-action">
                                                    <i class="fa fa-plus"></i>
                                                </div>
                                            </div>
                                            <div class="nav-action quick-add-input" id="quick-add-input">
                                                <div class="form-inline">
                                                    <label for=""><span class="text-danger">*</span>Quick Add </label>
                                                    <select id="itemsDropDown" class="form-control">
                                                        <option value="">Items</option>

                                                    </select>
                                                </div>
                                            </div>


                                    </div>

                                    <!---Currency section--->
                                    <!-- <div class="currency-section">
                                        <div class="form-input">
                                            <label for="">Currency Conversion</label>
                                            <select id="" name="currency" class="form-control">
                                                <?php
                                                $curr = queryGet("SELECT * FROM `erp_currency_type`", true);
                                                foreach ($curr['data'] as $data) {
                                                ?>
                                                    <option value="<?= $data['currency_id'] ?>"><?= $data['currency_name'] ?></option>
                                                <?php
                                                }
                                                ?>

                                            </select>
                                        </div>
                                        <div class="form-input">
                                            <label for="">Currency Conversion Rate</label>
                                            <input type="number" class="form-control" id="curr_rate" name="curr_rate" value="1">
                                        </div>
                                    </div> -->


                                </div>




                                <table class="table tabel-hover table-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Qty</th>
                                            <th>Unit Price</th>
                                            <th class="gsthead">Base Amount</th>
                                            <th class="gsthead">GST</th>
                                            <th class="gsthead">GST Amount</th>
                                            <!-- Info -->
                                            <th>Total Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTable">

                                    </tbody>
                                    <!-- <tbody class="total-calculate">
                                        <tr>
                                            <td class="text-right" style="border: none; padding-left: 15px !important"> </td>
                                            <td style="border: none;"><b>Total Amount</b></td>
                                            <td></td>
                                            <td></td>
                                            <input type="hidden" name="totalAmt" id="grandTotalAmountInput" value="0.00">
                                            <td style="border: none; background: none; padding-left: 15px !important" id="grandTotalAmount"><b>0.00</b>
                                                <p id="grandtotalTxt"></p>
                                                </th>
                                        </tr>

                                    </tbody> -->
                                    <tbody class="total-calculate purchase-order-item-list">
                                        <tr>
                                            <td colspan="6" class="text-right p-2 colspanCng " style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Sub Total</td>
                                            <input type="hidden" name="subTotal" id="subTotalAmountInput" value="0.00">
                                            <td class="text-right pr-2" style="border: none; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol" id="subTotalCurrency">INR </span><span id="subTotalAmount">0.00</span>
                                                </small>
                                                <!-- <small class="text-large font-weight-bold text-success">
                                                    <span class="" id=""><?= $comp_currency ?> </span><span id="subTotalAmount">0.00</span>
                                                </small> -->
                                                <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span id="">0.00</span>)
                                                </small> -->
                                            </td>
                                        </tr>


                                        <tr class="p-2 igstTr " style="display:none" id="igstCol">
                                            <td colspan="6" class="text-right p-2 colspanCng" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">IGST</td>
                                            <input type="hidden" name="igstInput" id="igst" value="0.00">
                                            <td class="text-right pr-2" style="border: none; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol">INR </span><span id="igst_span">0.00</span>
                                                </small>
                                                <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span id="">0.00</span>)
                                                </small> -->
                                            </td>
                                        </tr>
                                        <tr class="p-2 cgstTr" style="display:none">
                                            <td colspan="6" class="text-right p-2 colspanCng" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">CGST</td>
                                            <input type="hidden" name="cgstInput" id="cgst" value="0.00">
                                            <td class="text-right pr-2" style="border: none; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol">INR </span><span id="cgst_span">0.00</span>
                                                </small>
                                                <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span class="">0</span>)
                                                </small> -->
                                            </td>
                                        </tr>
                                        <tr class="p-2 sgstTr" style="display:none">
                                            <td colspan="6" class="text-right p-2 colspanCng" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">SGST</td>
                                            <input type="hidden" name="sgstInput" id="sgst" value="0.00">
                                            <td class="text-right pr-2" style="border: none; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol">INR </span><span id="sgst_span">0.00</span>
                                                </small>
                                                <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span class="">0</span>)
                                                </small> -->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="colspanCng"></td>
                                            <td colspan="2">

                                            </td>
                                        </tr>
                                        <tr class="p-2">
                                            <td colspan="6" class="text-right p-2 colspanCng" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 font-weight-bold totalCal" style="border-top: 3px double !important; background: none;padding: 0px !important;">Total Amount</td>
                                            <input type="hidden" name="totalAmt" id="grandTotalAmountInput" value="0.00">
                                            <td class="font-weight-bold text-right pr-2" style="border-top: 3px double !important; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol">INR </span><span id="grandTotalAmount">0.00</span>
                                                </small>
                                                <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span id="">0.00</span>)
                                                </small>
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card other-cost-info so-creation-card po-creation-card">
                                <div class="card-header">
                                    <div class="row others-info-head">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="head">
                                                <i class="fa fa-info"></i>
                                                <h4>Others Cost info</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body others-accordion-info" id="cost">

                                    <div class="row accordion-other-cost">
                                        <div class="col">
                                            <div class="tabs">
                                                <div class="tab">
                                                    <input type="checkbox" id="chck1" style="display: none;">
                                                    <label class="tab-label" for="chck1">Freight & Other Cost</label>
                                                    <div class="tab-content">
                                                        <div class="row othe-cost-infor modal-add-row_537">
                                                            <div class="row othe-cost-infor">
                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Select</label>
                                                                        <select class="form-control" id="" name="FreightCost[l1][service_purchase_id]">
                                                                            <option value="">Select Service</option>

                                                                            <?php
                                                                            $service_select = queryGet("SELECT * FROM `erp_inventory_items` WHERE `goodsType`=7 AND `location_id`=$location_id", true);
                                                                            //console($service_select);
                                                                            foreach ($service_select['data'] as $service) {
                                                                            ?>

                                                                                <option value="<?= $service['itemId'] ?>">[<?= $service['itemCode'] ?>] <?= $service['itemName'] ?></option>

                                                                            <?php

                                                                            }


                                                                            ?>

                                                                        </select>
                                                                    </div>
                                                                </div>



                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Vendor Select</label>
                                                                        <select class="form-control" name="FreightCost[l1][service_vendor]">
                                                                            <option value="">Select Vendor</option>
                                                                            <?php echo $vendrSelect;     ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="service description" name="FreightCost[l1][service_desc]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Amount</label>
                                                                        <input type="number" class="form-control amount" id="amount" placeholder="amount" name="FreightCost[l1][service_amount]">
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="add-btn-plus">
                                                                        <a style="cursor: pointer" class="btn btn-primary" onclick="addMultiQtyf(537)">
                                                                            <i class="fa fa-plus"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- <div class="tab">
                                                    <input type="checkbox" id="chck2" style="display: none;">
                                                    <label class="tab-label" for="chck2">Others Cost</label>
                                                    <div class="tab-content">



                                                        <div class="row othe-cost-infor modal-add-row_538">
                                                            <div class="row othe-cost-infor">


                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Select</label>
                                                                        <select class="form-control otherServiceDropDown" id="otherServiceDropDown" name="OthersCost[13][service_purchase_id]">
                                                                            <option value="">Select Service</option>

                                                                            <?php
                                                                            $service_select = queryGet("SELECT * FROM `erp_inventory_items` WHERE `goodsType`=7 AND `location_id`=$location_id", true);

                                                                            foreach ($service_select['data'] as $service) {
                                                                            ?>

                                                                                <option value="<?= $service['itemId'] ?>">[<?= $service['itemCode'] ?>] <?= $service['itemName'] ?></option>

                                                                            <?php

                                                                            }


                                                                            ?>

                                                                        </select>
                                                                    </div>
                                                                </div>




                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Vendor Name</label>
                                                                        <input type="text" class="form-control" placeholder="vendor name" name="OthersCost[13][service_vendor]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="description" name="OthersCost[13][service_desc]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Amount</label>
                                                                        <input type="number" class="form-control other_amount" placeholder="amount" id="other_amount" name="OthersCost[13][service_amount]">
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="add-btn-plus">
                                                                        <a style="cursor: pointer" class="btn btn-primary" onclick="addMultiQty(538)">
                                                                            <i class="fa fa-plus"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>

                                </div>


                            </div>
                            <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-primary save-close-btn btn-xs float-right add_data" id="pobtn" value="add_post">Save & Close</button>
                            <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-danger save-close-btn btn-xs float-right add_data" id="podbtn" value="add_draft">Save as Draft</button>
                        </div>
                    </div>
            </div>


            </form>



            <div class="modal select-pr-modal" id="select-pr">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header py-1" style="background-color: #003060; color:white;">
                            <h5 class="modal-title" style="color:white;">PR</h5>
                            <button type="button" id="mapInvoiceItemCodeModalCloseBtn" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <?php
                        $pr_sql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_REQUEST . "` WHERE 1  AND company_id='" . $company_id . "' AND  `pr_status`=9 ORDER BY `purchaseRequestId` DESC LIMIT 10 ";
                        $pr_get = queryGet($pr_sql, true);
                        $pr_data = $pr_get['data'];
                        ?>
                        <form id="pr_form">
                            <div class="modal-body">
                                <table class="table-sales-order table defaultDataTable grn-table">
                                    <thead>
                                        <tr>
                                            <th>Select</th>
                                            <th>PR Number</th>
                                            <th>Required Date</th>
                                            <th>Reference Number</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pr_data as $onePrList) {
                                            $rand = rand(10, 1000);

                                        ?>
                                            <tr>

                                                <td><input type="radio" name="pr-po-creation" value="<?= $onePrList['purchaseRequestId'] ?>" id="prId" class="form prId"></td>
                                                <td><?= $onePrList['prCode'] ?></td>
                                                <td><?= formatDateORDateTime($onePrList['expectedDate']) ?></td>
                                                <td><?= $onePrList['refNo'] ?></td>
                                                <td><?php
                                                    if ($onePrList['pr_status'] == 10) {
                                                        echo "Closed";
                                                    } else if ($onePrList['pr_status'] == 9) {
                                                        echo "Open";
                                                    }


                                                    ?></td>
                                            </tr>



                                        <?php
                                        }
                                        ?>






                                    </tbody>
                                </table>
                                <button id="pr_form" class="btn btn-primary float-right mt-3">Select PR</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- end pr modal --->
            <!-- modal -->
            <div class="modal" id="addNewItemsFormModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header py-1" style="background-color: #003060; color:white;">
                            <h4 class="modal-title">Add New Items</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <!-- <form action="" method="post" id="addNewItemsForm"> -->
                            <div class="col-md-12 mb-3">
                                <div class="input-group">
                                    <input type="text" name="itemName" class="m-input" required>
                                    <label>Item Name</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="text" name="itemDesc" class="m-input" required>
                                    <label>Item Description</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group btn-col">
                                    <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                                </div>
                            </div>
                            <!-- </form> -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- modal end -->
    </div>
    </section>
    </div>
<?php } elseif (isset($_GET['pr-po-creation'])) {
    //echo $_GET['pr-po-creation'];

    $id = $_GET['pr-po-creation'];
    $sql = "SELECT * FROM `erp_branch_purchase_request` WHERE `purchaseRequestId`='" . $id . "'";
    $sqlGet = queryGet($sql);
    $sqlData = $sqlGet['data'];
    // console($sqlData);

?>
    <div class="content-wrapper is-purchase-order">
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
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Purchase Order List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Purchase Order</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>

                <form action="" method="POST" id="submitPoForm" name="submitPoForm" onsubmit="return validationfunction()">
                <input type="hidden" name="poOrigin" value="pr">

                    <input type="hidden" name="createData" id="createData" value="">
                    <div class="row po-form-creation">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="card so-creation-card po-creation-card  po-creation-card ">
                                        <div class="card-header">
                                            <div class="row customer-info-head">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="head">
                                                        <i class="fa fa-user"></i>
                                                        <h4>Vendor Info</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body others-info vendor-info so-card-body">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="row info-form-view">
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <div class="input-box customer-select">
                                                                <!-- <label for="">Select Vendor</label>
                                                                &nbsp; &nbsp; -->
                                                                <select name="vendorId" id="vendorDropdown" class="selct-vendor-dropdown">
                                                                    <option value="">Select Vendor</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <div class="customer-info-text po-customer-info-text pt-3" id="vendorInfo">

                                                            </div>

                                                        </div>
                                                        <?php
                                                        $location = "SELECT * FROM  `erp_branch_otherslocation` WHERE `othersLocation_id`='" . $location_id . "' ";
                                                        $locConn = queryGet($location);
                                                        $locData = $locConn['data'];
                                                        // console($locData['othersLocation_building_no']);
                                                        $otherLocation = "SELECT * FROM  `erp_branch_otherslocation` WHERE `company_id`='" . $company_id . "' ";
                                                        $otherLocConn = queryGet($otherLocation, true);
                                                        $otherLocData = $otherLocConn['data'];

                                                        ?>

                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                                <div class="row address-section">
                                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                                        <div class="address-to bill-to">
                                                                            <h5>Bill to</h5>
                                                                            <hr class="mt-0 mb-2">
                                                                            <p>
                                                                                <?=
                                                                                $locData['othersLocation_building_no'] . "," . $locData['othersLocation_flat_no'] . "," . $locData['othersLocation_street_name'] . "," . $locData['othersLocation_pin_code'] . "," .  $locData['othersLocation_location'] . "," . $locData['othersLocation_district'] . "," .  $locData['othersLocation_city'] . "," .  $locData['othersLocation_state']
                                                                                ?>
                                                                            </p>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                                        <div class="address-to ship-to">
                                                                            <div class="row">
                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                    <h5>Ship to</h5>
                                                                                </div>
                                                                                <div class="col-lg-8 col-md-8 col-sm-8">
                                                                                    <h5 class="display-inline">
                                                                                        <div class="checkbox-label">
                                                                                            <input type="checkbox" id="addresscheckbox" name="addresscheckbox" value="1" title="checked here for same as Bill To adress" checked>
                                                                                            <p>Same as Bill to</p>
                                                                                        </div>
                                                                                        <button type="button" class="btn btn-primary change-address-modal text-xs btn-sm" data-toggle="modal" data-target="#address-change">Change</button>
                                                                                    </h5>
                                                                                </div>
                                                                            </div>



                                                                            <hr class="mt-0 mb-2">
                                                                            <p id="shipToAddressDiv">
                                                                                <?=
                                                                                $locData['othersLocation_building_no'] . "," . $locData['othersLocation_flat_no'] . "," . $locData['othersLocation_street_name'] . "," . $locData['othersLocation_pin_code'] . "," .  $locData['othersLocation_location'] . "," . $locData['othersLocation_district'] . "," .  $locData['othersLocation_city'] . "," .  $locData['othersLocation_state']
                                                                                ?>
                                                                            </p>
                                                                            <input type="hidden" name="shipToInput" id="shipToInput" value="<?= $locData['othersLocation_id'] ?>">
                                                                            <input type="hidden" name="shipToState" id="shipToState" value="<?= $locData['state_code'] ?>">




                                                                        </div>

                                                                        <!----------Address modal-------->

                                                                        <div class="modal fade address-change-modal" id="address-change" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                                            <div class="modal-dialog" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header card-header">
                                                                                        <div class="head">
                                                                                            <i class="fa fa-map-marker-alt"></i>
                                                                                            <h4>Change Address</h4>
                                                                                        </div>
                                                                                        <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                                                                            <li class="nav-item" role="presentation">
                                                                                                <button class="btn btn-primary address-btn otheraddressbtn nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#savedAddress" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Other Address</button>

                                                                                            </li>
                                                                                            <li class="nav-item" role="presentation">
                                                                                                <button class="btn btn-primary address-btn newaddress nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#newAddress" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">New Address</button>
                                                                                            </li>
                                                                                        </ul>
                                                                                    </div>

                                                                                    <div class="modal-body" style="height:15rem;">

                                                                                        <div class="tab-content " id="pills-tabContent">
                                                                                            <div class="tab-pane otherAddress   -tab-pen fade show active" id="savedAddress" role="tabpanel" aria-labelledby="pills-home-tab">
                                                                                                <?php
                                                                                                foreach ($otherLocData as $data) {
                                                                                                ?>

                                                                                                    <div class="address-to bill-to">
                                                                                                        <input type="radio" class="address-check" name="shipToAddress" value="<?= $data['othersLocation_id'] ?>">
                                                                                                        <h5 id="shipToAddressHeadText_<?= $data['othersLocation_id'] ?>"><?= $data['othersLocation_name'] ?></h5>
                                                                                                        <hr class="mt-0 mb-2">
                                                                                                        <p id="shipToAddressBodyText_<?= $data['othersLocation_id'] ?>">

                                                                                                            <?=
                                                                                                            $data['othersLocation_building_no'] . "," . $data['othersLocation_flat_no'] . "," . $data['othersLocation_street_name'] . "," . $data['othersLocation_pin_code'] . "," .  $data['othersLocation_location'] . "," . $data['othersLocation_district'] . "," .  $data['othersLocation_city'] . "," .  $data['othersLocation_state']
                                                                                                            ?>
                                                                                                        </p>
                                                                                                        <p id="shipToStateCode_<?= $data['othersLocation_id'] ?>">
                                                                                                            <?= $data['state_code'] ?>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                <?php
                                                                                                }
                                                                                                ?>

                                                                                            </div>
                                                                                            <div class="tab-pane newAddress-tab-pen fade" id="newAddress" role="tabpanel" aria-labelledby="pills-profile-tab">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                                                                                                        <label for="">Building Number</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                                                                                                        <label for="">Flat Number</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                                                                                                        <label for="">Street Name</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                                                                                                        <label for="">Location</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>

                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                                                                                                        <label for="">City</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                                                                                                        <label for="">Pin Code</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>

                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                                                                                                        <label for="">District</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                                                                                                        <label for="">State</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>

                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12"></div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12"></div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" id="closeBtn" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                        <button type="button" class="btn btn-primary" id="shipToAddressSaveBtn" data-dismiss="modal">Save changes</button>

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
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="card so-creation-card po-creation-card  po-creation-card">
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
                                <div class="card-body others-info">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="row info-form-view" style="row-gap: 17px;">

                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <label for="date">Delivery Date</label>
                                                    <input type="date" id="deliveryDate" name="deliveryDate" min="<?= $today ?>" class="form-control" value="<?= $sqlData['expectedDate'] ?>" />
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <label for="date">PO Creation Date</label>
                                                    <input type="date" name="podatecreation" id="podatecreation" class="form-control" min="<?= $min ?>" max="<?= $max ?>">
                                                    <p id="podatelabel"></p>
                                                </div>


                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-input">
                                                        <label for="date">Validity Period</label>
                                                        <input type="date" class="form-control" id="dateInputvalid" name="validitydate" min="<?php echo date('Y-m-d'); ?>" required>
                                                        <p id="validitylabel"></p>
                                                    </div>
                                                </div>






                                                <?php
                                                $useType = $sqlData['pr_type'];
                                                if ($useType == "servicep" || $useType == "service") {
                                                    $useType_val = "servicep";
                                                    $useType = 'Service Purchase';
                                                } else {
                                                    $useType_val = $useType;
                                                }

                                                ?>
                                                <div class="col-lg-3 col-md-3 col-sm-12">
                                                    <label for="date">Use Types</label>
                                                    <select name="usetypesDropdown" id="usetypesDropdown" class="form-control typesDropdown">
                                                        <option value="<?= $useType_val ?>"><?= $useType ?></option>

                                                    </select>

                                                </div>
                                                <?php
                                                if ($useType ==  "servicep" || $useType == "service") {

                                                ?>
                                                    <div class="col-lg-3 col-md-3 col-sm-12">
                                                        <label for="date">Select Parent PO</label>
                                                        <select id="parent" class="form-control parent" name="parent_po">
                                                            <option value="">Select</option>
                                                            <?php

                                                            $get_po = queryGet("SELECT * FROM `erp_branch_purchase_order` WHERE `location_id` = $location_id", true);
                                                            foreach ($get_po['data'] as $parent) {

                                                            ?>
                                                                <option value="<?= $parent['po_id'] ?>"><?= $parent['po_number'] ?></option>
                                                            <?php
                                                            }
                                                            ?>

                                                        </select>

                                                    </div>
                                                <?php
                                                }
                                                ?>

                                                <div class="col-lg-3 col-md-3 col-sm-12 cost-center-col">
                                                    <div class="cost-center" style="display: none;">
                                                        <label for="">Cost Center</label>
                                                        <select name="costCenter" class="form-control">
                                                            <option value="">Cost Center</option>
                                                            <?php
                                                            $funcList = $BranchPoObj->fetchFunctionality()['data'];
                                                            foreach ($funcList as $func) {
                                                            ?>
                                                                <option value="<?= $func['CostCenter_id'] ?>">
                                                                    <?= $func['CostCenter_code'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-12 cost-center-col">
                                                    <label for="">PO Type</label>
                                                    <select name="potypes" id="potypesprpo" onclick="craateUserJsObject.ShowPoTypes();" class="form-control typesDropdown">
                                                        <option value="">Select PO Type</option>
                                                        <option id="domestic" value="domestic">Domestic</option>
                                                        <option id="international" value="international">International
                                                        </option>
                                                    </select>
                                                </div>
                                                <?php

                                                if ($sqlData['pr_type'] == "servicep" || $sqlData['pr_type'] == "service") {
                                                    //  echo 0;
                                                } else {
                                                    //  echo 1;

                                                ?>
                                                    <div class="col-lg-3 col-md-3 col-sm-12 radio-condition" id="incoTerms">
                                                        <div class="radio-types radio-types-fob-cif" style="display: none;">
                                                            <label for="" class="inco-terms">Inco Terms</label>
                                                            <div class="form-input-radio form-input-fob">

                                                                <input type="radio" value="FOB" name="domestic">
                                                                <div class="tooltip-label">
                                                                    <label for="">FOB</label>
                                                                    <div class="help-tip fob-tooltip">
                                                                        <p>Free On Board or Freight on Board</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-input-radio form-input-cif">
                                                                <input type="radio" value="CIF" name="domestic">
                                                                <div class="tooltip-label">
                                                                    <label for="">CIF</label>
                                                                    <div class="help-tip cif-tooltip">
                                                                        <p>Cost, insurance, and freight is an international
                                                                            shipping agreement</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="radio-types radio-types-ex-for" style="display: none;">
                                                            <div class="form-input-radio form-input-ex-work">
                                                                <label for="" class="inco-terms">Inco Terms</label>
                                                                <input type="radio" value="exwork" name="domestic">
                                                                <div class="tooltip-label">
                                                                    <label for="">Ex Work</label>
                                                                    <div class="help-tip ex-work-tooltip">
                                                                        <p>An domestic trade term that describes when a
                                                                            seller makes a product available at a designated
                                                                            location</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-input-radio form-input-for">
                                                                <input type="radio" value="FOR" name="domestic">
                                                                <div class="tooltip-label">
                                                                    <label for="">FOR</label>
                                                                    <div class="help-tip for-tooltip">
                                                                        <p>F.O.R. stands for “Free on Road” means the goods which is being sent from source station to its destination includes transportation and all other transit expenses. All applicable taxes and duties on goods remains extra.</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                <?php

                                                }

                                                ?>

                                                <div class="col-lg-3 col-md-3 col-sm-12">
                                                    <div class="tooltip-label">
                                                        <label for="">Reference</label>
                                                        <div class="help-tip">
                                                            <p>Vendor PO</p>
                                                        </div>
                                                    </div>
                                                    <input type="text" name="refNo" class="form-control" value="<?= $sqlData['prCode'] ?>" />
                                                </div>

                                                <?php

                                                $check_func = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id`=$location_id");
                                                $funcs = $check_func['data']['companyFunctionalities'];
                                                $func_ex = explode(",", $funcs);



                                                ?>
                                                <div class="col-lg-3 col-md-3 col-sm-12">
                                                    <div class="func-area">
                                                        <label for="">Functional Area</label>
                                                        <select name="funcArea" class="form-control">
                                                            <option value="">Functional Area</option>
                                                            <?php

                                                            foreach ($func_ex as $func) {
                                                                $func_area = queryGet("SELECT * FROM `erp_company_functionalities` WHERE `functionalities_id`=$func", true);
                                                                //console($func_area);

                                                            ?>

                                                                <option value="<?= $func_area['data'][0]['functionalities_id'] ?>"><?= $func_area['data'][0]['functionalities_name'] ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>


                                                <!-- <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-input">
                                                        <label for="" class="label-hidden">Label</label>
                                                        <div class="static-currency">
                                                            <input type="text" class="form-control" value="1" readonly="">
                                                            <input type="text" class="form-control text-right" value="INR" readonly="">
                                                        </div>
                                                    </div>
                                                </div> -->

                                                <?php
                                                $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
                                                $companyCurrencyData = $companyCurrencyObj["data"];

                                                $comp_currency = $companyCurrencyData["currency_name"];
                                                ?>

                                                <div class="currency-conversion-section mt-3 purchase-order-currancy">
                                                    <div class="static-currency">
                                                        <input type="text" class="form-control" value="1" readonly>
                                                        <input type="text" class="form-control text-right" value="<?= $comp_currency ?>" readonly>
                                                    </div>
                                                    <div class="dynamic-currency">
                                                        <input type="text" name="curr_rate" id="currency_conversion_rate" value="1" class="form-control">
                                                        <select id="selectCurrency" name="currency" class="form-control text-right">
                                                            <?php

                                                            $curr = queryGet("SELECT * FROM `erp_currency_type`", true);
                                                            foreach ($curr['data'] as $data) {
                                                            ?>
                                                                <option value="<?= $data['currency_id'] ?>" data-currname="<?= $data['currency_name'] ?>" <?php if ($comp_currency == $data['currency_name']) {
                                                                                                                                                                echo "selected";
                                                                                                                                                            } ?>><?= $data['currency_name'] ?></option>
                                                            <?php
                                                            }
                                                            ?>

                                                        </select>
                                                    </div>
                                                    <div class="d-flex justify-content-start grn-form-input-text mb-2">
                                                        <p class="label-bold text-xs text-italic" style="white-space: pre-line;">Vendor Currency</p>
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
                        <!-- <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row others-info-head">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="head">
                                        <i class="fa fa-info"></i>
                                        <h4>Items Info</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card items-select-table">
                                <div class="head-item-table">
                                    <div class="advanced-serach">
                                        <form action="" method="POST">
                                            <input type="hidden" name="pr_id" value="<?= $id ?>">



                                    </div>
                                </div>

                                <table class="table tabel-hover table-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th class="prnumbercol">PR Number</th>
                                            <th>Qty</th>
                                            <th>Unit Price</th>
                                            <th class="gsthead">Base Amount</th>
                                            <th class="gsthead">GST</th>
                                            <th class="gsthead">GST Amount</th>
                                            <th width="30%">Total Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTable">
                                        <?php
                                        $pr_ite_sql = "SELECT *  FROM `erp_branch_purchase_request_items` WHERE `prId`='" . $id . "' AND `remainingQty` > 0 ";

                                        $pr = queryGet($pr_ite_sql, true);
                                        $pr_data = $pr['data'];
                                        // console($pr);

                                        foreach ($pr_data as $data) {
                                            //   console($data);
                                            $qty = $data['itemQuantity'];
                                            $remaining_qty = $data['remainingQty'];
                                            $pr_item_id = $data['prItemId'];
                                            $itemId = $data['itemId'];
                                            $getItemObj = $ItemsObj->getItemById($itemId);
                                            //console($getItemObj);
                                            $itemCode = $getItemObj['data']['itemCode'];
                                            $lastPricesql = "SELECT * FROM `erp_branch_purchase_order_items`as po_item JOIN `erp_branch_purchase_order` as po ON po_item.`po_id`=po.po_id WHERE `location_id`=$location_id AND `itemCode`=$itemCode ORDER BY po_item.`po_item_id` DESC LIMIT 1";
                                            $last = queryGet($lastPricesql);
                                            $lastRow = $last['data'] ?? "";
                                            $lastPrice = $lastRow['unitPrice'] ?? "";
                                            $basePrice = $remaining_qty * $lastPrice;


                                            $item_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `itemId` = $itemId");
                                            //console($item_sql);

                                            $item_name = $item_sql['data']['itemName'];
                                            $item_code = $item_sql['data']['itemCode'];


                                            $randCode = $getItemObj['data']['itemId'] . rand(00, 99);

                                            $hsn = $getItemObj['data']['hsnCode'];
                                            $gstPercentage = queryGet("SELECT * FROM `erp_hsn_code` WHERE `hsnCode` = '" . $hsn . "'");
                                            //   console($gstPercentage);

                                            // console($randCode);
                                            $gstAmount = ($gstPercentage['data']['taxPercentage'] / 100) * $basePrice;
                                            $totalAmount = $basePrice + $gstAmount;

                                        ?>
                                            <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>">
                                                <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemId]" value="<?= $getItemObj['data']['itemId'] ?>">

                                                <td>
                                                    <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][pritemId]" value="<?= $pr_item_id ?>">

                                                    <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemCode]" value="<?= $getItemObj['data']['itemCode'] ?>">
                                                    <?= $item_code ?>
                                                </td>
                                                <td>
                                                    <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
                                                    <p class="pre-normal"><?= $item_name ?></p>
                                                </td>
                                                <td>
                                                    <?= $sqlData['prCode'] ?>
                                                    <input type="hidden" name="listItem[<?= $randCode ?>][pr_id]" value="<?= $data['prId'] ?>">
                                                </td>
                                                <td>
                                                    <div class="flex-display">
                                                        <input type="number" step="any" name="listItem[<?= $randCode ?>][qty]" value="<?= $remaining_qty ?>" class="form-control full-width itemQty" min="1" data-id="<?= $randCode ?>" id="itemQty_<?= $randCode ?>">

                                                        <input type="hidden" name="listItem[<?= $randCode ?>][remQty]" value="<?= $remaining_qty ?>" class="form-control full-width remqty_<?= $randCode ?>" min="1" id="remqty">


                                                        <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>
                                                        <input type="hidden" name="listItem[<?= $randCode ?>][uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>">
                                                    </div>
                                                    <p id="qty_error_<?= $randCode ?>"></p>
                                                </td>
                                                <td>
                                                    <input type="number" name="listItem[<?= $randCode ?>][unitPrice]" id="itemUnitPrice_<?= $randCode ?>" value="<?= $lastPrice ?>" class="form-control full-width-center itemUnitPrice" data-attr="<?= $randCode ?>">
                                                    <div class="d-flex gap-2 my-1">
                                                        <?= $comp_currency ?> <p id="local_unit_price_<?= $randCode ?>">0.00</p>
                                                    </div>
                                                    <input type="hidden" name="listItem[<?= $randCode ?>][unitPriceHidden]" value="<?= $lastPrice ?>" id="ItemUnitPriceTdInputhidden_<?= $randCode ?>" class="form-control text-xs itemUnitPricehidden">


                                                </td>
                                                <!-- <td class="flex-display">
                                                                        <input type="number" name="listItem[<?= $randCode ?>][totalDiscount]" value="0.00" class="form-control full-width-center itemDiscount">%
                                                </td> -->
                                                <td>
                                                    <input type="number" name="listItem[<?= $randCode ?>][basePrice]" value="<?= $basePrice ?>" class="gstTD form-control full-width-center itemBasePrice" id="itemBasePrice_<?= $randCode ?>" data-attr="<?= $randCode ?>" readonly>
                                                    <div class="d-flex gap-2 my-1">
                                                        <?= $comp_currency ?> <p id="local_base_price_<?= $randCode ?>">0.00</p>
                                                    </div>
                                                </td>

                                                <td>
                                                    <input type="number" name="listItem[<?= $randCode ?>][gst]" value="<?= $gstPercentage['data']['taxPercentage'] ?>" class="gstTD form-control full-width-center gst" id="gst_<?= $randCode ?>" data-attr="<?= $randCode ?>" readonly>


                                                    <input type="number" style="display:none" name="listItem[<?= $randCode ?>][gstbackup]" value="<?= $gstPercentage['data']['taxPercentage'] ?>" class="form-control full-width-center gst" id="gstbackup_<?= $randCode ?>" readonly>

                                                </td>
                                                <td>
                                                    <input type="number" name="listItem[<?= $randCode ?>][gstAmount]" value="<?= $gstAmount ?>" class="gstTD form-control full-width-center gstAmount" id="gstAmount_<?= $randCode ?>" data-attr="<?= $randCode ?>" readonly>
                                                    <div class="d-flex gap-2 my-1">
                                                        <?= $comp_currency ?> <p id="local_gst_amount_<?= $randCode ?>">0.00</p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" name="listItem[<?= $randCode ?>][totalPrice]" id="itemTotalPrice_<?= $randCode ?>" value="<?= $totalAmount ?>" class="form-control full-width-center itemTotalPrice" data-attr="<?= $randCode ?>" readonly>
                                                    <div class="d-flex gap-2 my-1">
                                                        <?= $comp_currency ?> <p id="local_total_price_<?= $randCode ?>">0.00</p>
                                                    </div>
                                                </td>

                                                <td class="action-flex-btn">

                                                    <button type="button" class="btn-view btn btn-primary" data-toggle="modal" data-target="#deliveryScheduleModal_<?= $randCode ?>">
                                                        <i class="statusItemBtn fa fa-cog" id="statusItemBtn_<?= $getItemObj['data']['itemId'] ?>"></i>
                                                    </button>


                                                    <button type="button" class="btn btn-danger delItemBtn" id="delItemBtn_<?= $itemId  ?>">
                                                        <i class="fa fa-minus"></i>
                                                    </button>
                                                </td>



                                                <div class="modal modal-left left-item-modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" id="deliveryScheduleModal_<?= $randCode ?>" tabindex="-1" role="dialog" aria-labelledby="left_modal" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title text-white">Delivery Shedule</h5>

                                                            </div>
                                                            <div class="modal-body">
                                                                <!-- <h6 class="modal-title">Total Qty: <span class="mainQty" id="mainQty_<?= $randCode ?>">1</span></h6> -->
                                                                <div class="row">


                                                                    <div class="col-lg-12 col-md-12 col-sm-12  modal-add-row modal-add-row-delivery_<?= $randCode ?>">
                                                                        <?php

                                                                        $delivery_schedule = queryGet("SELECT * FROM `erp_purchase_register_item_delivery_schedule` WHERE `pr_item_id`='" . $pr_item_id . "'", true);
                                                                        // console($delivery_schedule);
                                                                        $iteration_count = count($delivery_schedule_data);
                                                                        $iteration_number = 1;
                                                                        foreach ($delivery_schedule['data'] as $delivery_schedule_data) {
                                                                            

                                                                           



                                                                        ?>

                                                                            <div class="row">
                                                                                <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
                                                                                    <div class="form-input">
                                                                                        <label>Delivery date</label>
                                                                                        <input type="date" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][multiDeliveryDate]" class="form-control delDate delDate_<?= $randCode ?>" data-attr="<?= $randCode ?>" id="delivery-date" placeholder="delivery date" value="<?= $delivery_schedule_data['delivery_date'] ?>">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                                                                    <div class="form-input">
                                                                                        <label>Quantity</label>
                                                                                        <input type="text" step="any" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][quantity]" class="form-control multiQuantity multiQty_<?= $randCode ?>" data-attr="<?= $randCode ?>" id="multiQuantity_<?= $randCode ?>" placeholder="quantity" value="<?= $delivery_schedule_data['remaining_qty'] ?>">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                                                                    <?php
                                                                                   // echo $iteration_number;
                                                                                    if($iteration_number == 1) {
                                                                                        $iteration_number++;
                                                                                    ?>
                                                                                        <div class="add-btn-plus">
                                                                                            <a style="cursor: pointer" class="btn btn-primary waves-effect waves-light" onclick='addDeliveryQty(<?= $randCode ?>)'>
                                                                                                <i class="fa fa-plus"></i>
                                                                                            </a>
                                                                                        </div>
                                                                                    <?php
                                                                                    } else {
                                                                                    ?>
                                                                                        <div class="add-btn-minus">
                                                                                            <a style="cursor: pointer" class="btn btn-primary waves-effect waves-light" onclick=''>
                                                                                                <i class="fa fa-minus"></i>
                                                                                            </a>
                                                                                        </div>

                                                                                    <?php
                                                                                    }
                                                                                    ?>
                                                                                </div>
                                                                            </div>

                                                                        <?php
                                                                        }
                                                                        ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer modal-footer-fixed">
                                                                <button type="submit" id="finalBtn_<?= $randCode ?>" class="btn btn-primary save-close-btn btn-xs float-right waves-effect waves-light" data-dismiss="modal" aria-label="Close">Save & Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </td>
                                            </tr>







                                        <?php  } ?>
                                    </tbody>
                                    <!-- <tbody class="total-calculate">
                                        <tr>
                                            <td style="border: none;"> </td>
                                            <td style="border: none; padding-left: 15px !important;"><b>Total Amount</b></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <input type="hidden" name="totalAmt" id="grandTotalAmountInput" value="0.00">
                                            <td style="border: none; background: none; padding-left: 15px !important; white-space: pre-wrap;" id="grandTotalAmount">0.00</td>
                                        </tr>
                                    </tbody> -->
                                    <tbody class="total-calculate">
                                        <tr>
                                            <td colspan="7" class="text-right p-2 colspanCng" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Sub Total</td>
                                            <input type="hidden" name="subTotal" id="subTotalAmountInput" value="0.00">
                                            <td class="text-right pr-2" style="border: none; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol">INR </span><span id="subTotalAmount">0.00</span>
                                                </small>
                                                <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span id="">0.00</span>)
                                                </small> -->
                                            </td>
                                        </tr>


                                        <tr class="p-2 igstTr" style="display:none" id="igstCol">
                                            <td colspan="7" class="text-right p-2 colspanCng" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">IGST</td>
                                            <input type="hidden" name="igstInput" id="igst" value="0.00">
                                            <td class="text-right pr-2" style="border: none; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol">INR </span><span id="igst_span">0.00</span>
                                                </small>
                                                <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span id="">0.00</span>)
                                                </small> -->
                                            </td>
                                        </tr>
                                        <tr class="p-2 cgstTr" style="display:none">
                                            <td colspan="7" class="text-right p-2 colspanCng" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">CGST</td>
                                            <input type="hidden" name="cgstInput" id="cgst" value="0.00">
                                            <td class="text-right pr-2" style="border: none; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol">INR </span><span id="cgst_span">0.00</span>
                                                </small>
                                                <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span class="">0</span>)
                                                </small> -->
                                            </td>
                                        </tr>
                                        <tr class="p-2 sgstTr" style="display:none">
                                            <td colspan="7" class="text-right p-2 colspanCng" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">SGST</td>
                                            <input type="hidden" name="sgstInput" id="sgst" value="0.00">
                                            <td class="text-right pr-2" style="border: none; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol">INR </span><span id="sgst_span">0.00</span>
                                                </small>
                                                <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span class="">0</span>)
                                                </small> -->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="colspanCng"></td>
                                            <td colspan="2">

                                            </td>
                                        </tr>
                                        <tr class="p-2">
                                            <td colspan="7" class="text-right p-2 colspanCng" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 font-weight-bold totalCal" style="border-top: 3px double !important; background: none;padding: 0px !important;">Total Amount</td>
                                            <input type="hidden" name="totalAmt" id="grandTotalAmountInput" value="0.00">
                                            <td class="font-weight-bold text-right pr-2" style="border-top: 3px double !important; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol">INR </span><span id="grandTotalAmount">0.00</span>
                                                </small>
                                                <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span id="">0.00</span>)
                                                </small>
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card other-cost-info so-creation-card po-creation-card">
                                <div class="card-header">
                                    <div class="row others-info-head">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="head">
                                                <i class="fa fa-info"></i>
                                                <h4>Others Cost info</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body others-accordion-info" id="cost">

                                    <div class="row accordion-other-cost">
                                        <div class="col">
                                            <div class="tabs">
                                                <div class="tab">
                                                    <input type="checkbox" id="chck1" style="display: none;">
                                                    <label class="tab-label" for="chck1">Freight & Other Cost</label>
                                                    <div class="tab-content">
                                                        <div class="row othe-cost-infor modal-add-row_537">
                                                            <div class="row othe-cost-infor">
                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Select</label>
                                                                        <select class="form-control" id="" name="FreightCost[l1][service_purchase_id]">
                                                                            <option value="">Select Service</option>

                                                                            <?php
                                                                            $service_select = queryGet("SELECT * FROM `erp_inventory_items` WHERE `goodsType`=7 AND `location_id`=$location_id", true);
                                                                            //console($service_select);
                                                                            foreach ($service_select['data'] as $service) {
                                                                            ?>

                                                                                <option value="<?= $service['itemId'] ?>">[<?= $service['itemCode'] ?>] <?= $service['itemName'] ?></option>

                                                                            <?php

                                                                            }


                                                                            ?>

                                                                        </select>
                                                                    </div>
                                                                </div>



                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Vendor Select</label>
                                                                        <select class="form-control" name="FreightCost[l1][service_vendor]">
                                                                            <option value="">Select Vendor</option>
                                                                            <?php echo $vendrSelect;     ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="service description" name="FreightCost[l1][service_desc]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Amount</label>
                                                                        <input type="number" class="form-control amount" id="amount" placeholder="amount" name="FreightCost[l1][service_amount]">
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="add-btn-plus">
                                                                        <a style="cursor: pointer" class="btn btn-primary" onclick="addMultiQtyf(537)">
                                                                            <i class="fa fa-plus"></i>
                                                                        </a>
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

                            <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-primary save-close-btn btn-xs float-right add_data" id="pobtn" value="add_post">Save & Close</button>
                            <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-danger save-close-btn btn-xs float-right add_data" id="podbtn" value="add_draft">Save as Draft</button>

                        </div>
                    </div>
            </div>
    </div>

    </form>
    <!-- modal -->
    <div class="modal" id="addNewItemsFormModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-1" style="background-color: #003060; color:white;">
                    <h4 class="modal-title">Add New Items</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <!-- <form action="" method="post" id="addNewItemsForm"> -->
                    <div class="col-md-12 mb-3">
                        <div class="input-group">
                            <input type="text" name="itemName" class="m-input" required>
                            <label>Item Name</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" name="itemDesc" class="m-input" required>
                            <label>Item Description</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="input-group btn-col">
                            <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                        </div>
                    </div>
                    <!-- </form> -->
                </div>
            </div>
        </div>
    </div>
    <!-- modal end -->
    </div>
    </section>
    </div>

<?php
} elseif (isset($_GET['edit'])) {

    $id = $_GET['edit'];
    $edit_sql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `po_id`=$id";
    $sqlGet = queryGet($edit_sql);
    $sqlData = $sqlGet['data'];
    $poNumber = $sqlData['po_number'];
    //  console($sqlData);
    $vendor_id = $sqlData['vendor_id'];
    $vendorsql = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `vendor_id`= $vendor_id ");
    $vendorData = $vendorsql['data'];
    $ship_to_id = $sqlData['ship_address'];
    //console($ship_to_id);

?>

    <div class="content-wrapper is-purchase-order">
        <section class="content">
            <div class="container-fluid">


                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Purchase Order List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Edit Purchase Order</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>

                <form action="" method="POST" id="" name="">

                    <input type="hidden" name="po_id" id="" value="<?= $id ?>">
                    <div class="row po-form-creation">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="card so-creation-card po-creation-card  po-creation-card ">
                                        <div class="card-header">
                                            <div class="row customer-info-head">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="head">
                                                        <i class="fa fa-user"></i>
                                                        <h4>Vendor Info</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        $vendor_bussiness = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id` = '" . $vendorData['vendor_id'] . "' AND `vendor_business_primary_flag` = 1");

                                        ?>
                                        <div class="card-body others-info vendor-info so-card-body">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="row info-form-view">
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <div class="form-inline input-box customer-select mt-2">
                                                                <h2 class="text-xs font-bold"> Vendor : &nbsp;<?= $vendorData['trade_name'] ?></h2>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <div class="customer-info-text po-customer-info-text" id="vendorInfo">
                                                                <div class="card po-vendor-details-view">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <input type="hidden" name="vendorId" value="<?= $vendor_id; ?>">
                                                                            <div class="col-lg-4 col-md-4 col-sm-12 display-flex customer-code"><i class="fa fa-check"></i>&nbsp;<p>Code :&nbsp;</p>
                                                                                <p> <?= $vendorData['vendor_code'] ?></p>
                                                                                <div class="divider"></div>
                                                                            </div>
                                                                            <div class="col-lg-4 col-md-4 col-sm-12 display-flex customer-gstin"><i class="fa fa-check"></i>&nbsp;<p>GSTIN :&nbsp;</p>
                                                                                <p> <?= $vendorData['vendor_gstin'] ?></p>
                                                                                <div class="divider"></div>
                                                                                <input type="hidden" name="vendor_state" id="vendor_state_code" value="<?= $vendor_bussiness['data']['state_code']  ?>">
                                                                            </div>
                                                                            <div class="col-lg-4 col-md-4 col-sm-12 display-flex customer-status"><i class="fa fa-check"></i>&nbsp;<p>Status :&nbsp;</p>
                                                                                <p class="status"> active</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <?php
                                                        $location = "SELECT * FROM  `erp_branch_otherslocation` WHERE `othersLocation_id`='" . $location_id . "' ";
                                                        $locConn = queryGet($location);
                                                        $locData = $locConn['data'];
                                                        // console($locData['othersLocation_building_no']);
                                                        $otherLocation = "SELECT * FROM  `erp_branch_otherslocation` WHERE `company_id`='" . $company_id . "' ";
                                                        $otherLocConn = queryGet($otherLocation, true);
                                                        $otherLocData = $otherLocConn['data'];
                                                        ?>

                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                                <div class="row address-section">
                                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                                        <div class="address-to bill-to">
                                                                            <h5>Bill to</h5>
                                                                            <hr class="mt-0 mb-2">
                                                                            <p>
                                                                                <?=
                                                                                $locData['othersLocation_building_no'] . "," . $locData['othersLocation_flat_no'] . "," . $locData['othersLocation_street_name'] . "," . $locData['othersLocation_pin_code'] . "," .  $locData['othersLocation_location'] . "," . $locData['othersLocation_district'] . "," .  $locData['othersLocation_city'] . "," .  $locData['othersLocation_state']
                                                                                ?>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                                        <div class="address-to ship-to">
                                                                            <div class="row">
                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                    <h5>Ship to</h5>
                                                                                </div>
                                                                                <div class="col-lg-8 col-md-8 col-sm-8">
                                                                                    <h5 class="display-inline">
                                                                                        <div class="checkbox-label">
                                                                                            <input type="checkbox" id="addresscheckbox" name="addresscheckbox" value="1" title="checked here for same as Bill To adress">
                                                                                            <p>Same as Bill to</p>
                                                                                        </div>
                                                                                        <button type="button" class="btn btn-primary change-address-modal text-xs btn-sm" data-toggle="modal" data-target="#address-change">Change</button>
                                                                                    </h5>
                                                                                </div>
                                                                            </div>
                                                                            <?php
                                                                            $ship_sql = "SELECT * FROM  `erp_branch_otherslocation` WHERE `othersLocation_id`='" . $ship_to_id . "' ";
                                                                            $ship_get = queryGet($ship_sql);
                                                                            $ship_data = $ship_get['data'];


                                                                            ?>


                                                                            <hr class="mt-0 mb-2">
                                                                            <p id="shipToAddressDiv">
                                                                                <?=
                                                                                $ship_data['othersLocation_building_no'] . "," . $ship_data['othersLocation_flat_no'] . "," . $ship_data['othersLocation_street_name'] . "," . $ship_data['othersLocation_pin_code'] . "," .  $ship_data['othersLocation_location'] . "," . $ship_data['othersLocation_district'] . "," .  $ship_data['othersLocation_city'] . "," .  $ship_data['othersLocation_state']
                                                                                ?>
                                                                            </p>
                                                                            <input type="hidden" name="shipToInput" id="shipToInput" value="<?= $locData['othersLocation_id'] ?>">
                                                                            <input type="hidden" name="shipToState" id="shipToState" value="<?= $locData['state_code'] ?>">




                                                                        </div>

                                                                        <!----------Address modal-------->

                                                                        <div class="modal fade address-change-modal" id="address-change" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable mt-0" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header card-header">
                                                                                        <div class="head">
                                                                                            <i class="fa fa-map-marker-alt"></i>
                                                                                            <h4>Change Address</h4>
                                                                                        </div>
                                                                                        <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                                                                            <li class="nav-item" role="presentation">
                                                                                                <button class="btn btn-primary address-btn otheraddressbtn nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#savedAddress" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Other Address</button>
                                                                                            </li>
                                                                                            <li class="nav-item" role="presentation">
                                                                                                <button class="btn btn-primary address-btn newaddress nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#newAddress" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">New Address</button>
                                                                                            </li>
                                                                                        </ul>
                                                                                    </div>

                                                                                    <div class="modal-body" style="height:15rem;">

                                                                                        <div class="tab-content " id="pills-tabContent">
                                                                                            <div class="tab-pane otherAddress-tab-pen fade show active" id="savedAddress" role="tabpanel" aria-labelledby="pills-home-tab">
                                                                                                <?php
                                                                                                foreach ($otherLocData as $data) {
                                                                                                ?>

                                                                                                    <div class="address-to bill-to">
                                                                                                        <input type="radio" class="address-check" name="shipToAddress" value="<?= $data['othersLocation_id'] ?>">
                                                                                                        <h5 id="shipToAddressHeadText_<?= $data['othersLocation_id'] ?>"><?= $data['othersLocation_name'] ?></h5>
                                                                                                        <hr class="mt-0 mb-2">
                                                                                                        <p id="shipToAddressBodyText_<?= $data['othersLocation_id'] ?>">

                                                                                                            <?=
                                                                                                            $data['othersLocation_building_no'] . "," . $data['othersLocation_flat_no'] . "," . $data['othersLocation_street_name'] . "," . $data['othersLocation_pin_code'] . "," .  $data['othersLocation_location'] . "," . $data['othersLocation_district'] . "," .  $data['othersLocation_city'] . "," .  $data['othersLocation_state'] . "," .  $data['state_code']
                                                                                                            ?>

                                                                                                        </p>
                                                                                                        <p id="shipToStateCode_<?= $data['othersLocation_id'] ?>">
                                                                                                            <?= $data['state_code'] ?>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                <?php
                                                                                                }
                                                                                                ?>
                                                                                                <div class="modal-footer px-0">
                                                                                                    <button type="button" id="closeBtn" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                                    <button type="button" class="btn btn-primary" id="shipToAddressSaveBtn" data-dismiss="modal">Save changes</button>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="tab-pane newAddress-tab-pen fade" id="newAddress" role="tabpanel" aria-labelledby="pills-profile-tab">

                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for=""> Location Name</label>
                                                                                                        <input type="text" name="loc_name" id="loc_name" class="form-control">
                                                                                                    </div>

                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">Building Number</label>
                                                                                                        <input type="text" name="buildingName" id="buildingName" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">Flat Number</label>
                                                                                                        <input type="text" name="flatNumber" id="flatNumber" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">Street Name</label>
                                                                                                        <input type="text" name="streetName" id="streetName" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                                                                                        <label for="">Location</label>
                                                                                                        <input type="text" name="newLocation" id="newLocation" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">City</label>
                                                                                                        <input type="text" name="newCity" id="newCity" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">Pin Code</label>
                                                                                                        <input type="text" name="newPinCode" id="newPinCode" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">District</label>
                                                                                                        <input type="text" name="newDistrict" id="newDistrict" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">State</label>
                                                                                                        <input type="text" name="newState" id="newState" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">Latitude</label>
                                                                                                        <input type="text" name="lat" id="lat" class="form-control">

                                                                                                    </div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">Longitude</label>
                                                                                                        <input type="text" name="lng" id="lng" class="form-control">

                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="modal-footer px-0">

                                                                                                    <button type="button" class="btn btn-primary" id="addNewAddressBtn">Save</button>
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="card so-creation-card po-creation-card  po-creation-card">
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
                                <div class="card-body others-info">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="row info-form-view">

                                                <div class="col-lg-3 col-md-4 col-sm-12">
                                                    <label for="date">Delivery Date</label>
                                                    <input type="date" id="deliveryDate" name="deliveryDate" min="<?= $today ?>" class="form-control" value="<?= $sqlData['delivery_date'] ?>" />
                                                </div>
                                                <div class="col-lg-3 col-md-4 col-sm-12">
                                                    <label for="date">PO Creation Date</label>
                                                    <input type="date" name="podatecreation" id="podatecreation" class="form-control" value="<?= $sqlData['po_date'] ?>" min="<?= $min ?>" max="<?= $max ?>">
                                                    <p id="podatelabel"></p>
                                                </div>

                                                <div class="col-lg-3 col-md-4 col-sm-12">
                                                    <div class="form-input">
                                                        <label for="date">Validity Period</label>
                                                        <input type="date" class="form-control" id="dateInputvalid" name="validitydate" min="<?php echo date('Y-m-d'); ?>" value="<?= $sqlData['validityperiod'] ?>" required>
                                                        <p id="validitylabel"></p>
                                                    </div>
                                                </div>


                                            </div>

                                            <div class="row info-form-view">
                                                <div class="col-lg-6 col-md-6 col-sm-12 form-inline">
                                                    <label for="date">Use Types</label>
                                                    <select name="usetypesDropdown" id="usetypesDropdown" class="form-control typesDropdown" disabled>
                                                        <option value="material" <?php if ($sqlData['use_type'] == "material") {
                                                                                        echo "selected";
                                                                                    }  ?>>Raw Material</option>


                                                        <option value="servicep" <?php if ($sqlData['use_type'] == "servicep") {
                                                                                        echo "selected";
                                                                                    }  ?>>Service Purchase</option>


                                                        <option value="asset" <?php if ($sqlData['use_type'] == "asset") {
                                                                                    echo "selected";
                                                                                }  ?>>Asset</option>
                                                    </select>
                                                </div>
                                                <?php
                                                if ($sqlData['use_type'] == 'services' || $sqlData['use_type'] == 'servicep') {
                                                ?>
                                                    <div class="col-lg-3 col-md-3 col-sm-12">
                                                        <label for="date">Select Parent PO</label>
                                                        <select id="parent" class="form-control parent" name="parent_po">
                                                            <option value="">Select</option>
                                                            <?php

                                                            $get_po = queryGet("SELECT * FROM `erp_branch_purchase_order` WHERE `location_id` = $location_id", true);
                                                            foreach ($get_po['data'] as $parent) {

                                                            ?>
                                                                <option value="<?= $parent['po_id'] ?>"><?= $parent['po_number'] ?></option>
                                                            <?php
                                                            }
                                                            ?>

                                                        </select>

                                                    </div>
                                                <?php
                                                }
                                                if ($sqlData['use_type'] == "consumable") {
                                                ?>
                                                    <div class="col-lg-3 col-md-3 col-sm-12 cost-center-col">
                                                        <div class="cost-center" style="display: none;">
                                                            <label for="">Cost Center</label>
                                                            <select name="costCenter" class="form-control">
                                                                <option value="">Cost Center</option>
                                                                <?php
                                                                $funcList = $BranchPoObj->fetchFunctionality()['data'];
                                                                foreach ($funcList as $func) {
                                                                ?>
                                                                    <option value="<?= $func['CostCenter_id'] ?>">
                                                                        <?= $func['CostCenter_code'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                                <div class="col-lg-3 col-md-3 col-sm-12 cost-center-col">
                                                    <label for="">PO Type</label>
                                                    <select name="potypes" id="potypes" onclick="craateUserJsObject.ShowPoTypes();" class="form-control typesDropdown" disabled>
                                                        <option value="">Select PO Type</option>
                                                        <option id="domestic" value="domestic" <?php if ($sqlData['po_type'] == "domestic") {
                                                                                                    echo "selected";
                                                                                                }  ?>>Domestic</option>
                                                        <option id="international" value="international" <?php if ($sqlData['po_type'] == "international") {
                                                                                                                echo "selected";
                                                                                                            }  ?>>International
                                                        </option>
                                                    </select>
                                                </div>
                                                <?php
                                                if ($sqlData['use_type'] == 'services' || $sqlData['use_type'] == 'servicep') {
                                                } else {
                                                    //  echo "ok";
                                                ?>
                                                    <div class="col-lg-3 col-md-3 col-sm-12 radio-condition" id="incoTerms">
                                                        <?php
                                                        if ($sqlData['po_type'] == "international") {
                                                        ?>
                                                            <div class="radio-types radio-types-fob-cif">
                                                                <label for="" class="inco-terms">Inco Terms</label>
                                                                <div class="form-input-radio form-input-fob">

                                                                    <input type="radio" value="FOB" name="domestic" <?php if ($sqlData['inco_type'] == 'FOB') {
                                                                                                                        echo 'checked';
                                                                                                                    } ?> disabled>
                                                                    <div class="tooltip-label">
                                                                        <label for="">FOB</label>
                                                                        <div class="help-tip fob-tooltip">
                                                                            <p>Free On Board or Freight on Board</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-input-radio form-input-cif">
                                                                    <input type="radio" value="CIF" name="domestic" <?php if ($sqlData['inco_type'] == 'CIF') {
                                                                                                                        echo 'checked';
                                                                                                                    } ?> disabled>
                                                                    <div class="tooltip-label">
                                                                        <label for="">CIF</label>
                                                                        <div class="help-tip cif-tooltip">
                                                                            <p>Cost, insurance, and freight is an international
                                                                                shipping agreement</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <div class="radio-types radio-types-ex-for">
                                                                <div class="form-input-radio form-input-ex-work">
                                                                    <label for="" class="inco-terms">Inco Terms</label>
                                                                    <input type="radio" value="exwork" name="domestic" <?php if ($sqlData['inco_type'] == 'exwork') {
                                                                                                                            echo 'checked';
                                                                                                                        } ?> disabled>
                                                                    <div class="tooltip-label">
                                                                        <label for="">Ex Work</label>
                                                                        <div class="help-tip ex-work-tooltip">
                                                                            <p>An domestic trade term that describes when a
                                                                                seller makes a product available at a designated
                                                                                location</p>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="form-input-radio form-input-for">
                                                                    <input type="radio" value="FOR" name="domestic" <?php if ($sqlData['inco_type'] == 'FOR') {
                                                                                                                        echo 'checked';
                                                                                                                    } ?> disabled>
                                                                    <div class="tooltip-label">
                                                                        <label for="">FOR</label>
                                                                        <div class="help-tip for-tooltip">
                                                                            <p>F.O.R. stands for “Free on Road” means the goods which is being sent from source station to its destination includes transportation and all other transit expenses. All applicable taxes and duties on goods remains extra.</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="row info-form-view">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="tooltip-label">
                                                        <label for="">Reference</label>
                                                        <div class="help-tip">
                                                            <p>Vendor PO</p>
                                                        </div>
                                                    </div>
                                                    <input type="text" name="refNo" class="form-control" value="<?= $sqlData['ref_no'] ?>" />
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="func-area">
                                                        <label for="">Attachment</label>
                                                        <input type="file" name="attachment" class="form-control" />
                                                    </div>
                                                </div>
                                            </div>

                                            <?php
                                            $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
                                            $companyCurrencyData = $companyCurrencyObj["data"];

                                            $comp_currency = $companyCurrencyData["currency_name"];
                                            ?>

                                            <div class="currency-conversion-section mt-3">
                                                <div class="static-currency">
                                                    <input type="text" class="form-control" value="1" readonly>
                                                    <input type="text" class="form-control text-right" value="<?= $comp_currency ?>" readonly>
                                                </div>
                                                <div class="dynamic-currency">
                                                    <input type="text" name="curr_rate" id="currency_conversion_rate" value="<?= $sqlData['conversion_rate'] ?>" class="form-control">
                                                    <select id="selectCurrency" name="currency" class="form-control text-right">
                                                        <?php

                                                        $curr = queryGet("SELECT * FROM `erp_currency_type`", true);
                                                        foreach ($curr['data'] as $data) {
                                                        ?>
                                                            <option value="<?= $data['currency_id'] ?>" data-currname="<?= $data['currency_name'] ?>" <?php if ($data['currency_id'] == $sqlData['currency']) {
                                                                                                                                                            echo 'selected';
                                                                                                                                                        }  ?>><?= $data['currency_name'] ?></option>
                                                        <?php
                                                        }
                                                        ?>

                                                    </select>
                                                </div>
                                                <div class="display-flex grn-form-input-text mt-3">
                                                    <p class="label-bold text-italic" style="white-space: pre-line;">Vendor Currency</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>





                    <div class="row">
                        <!-- <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row others-info-head">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="head">
                                        <i class="fa fa-info"></i>
                                        <h4>Items Info</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card items-select-table">
                                <div class="head-item-table">
                                    <div class="advanced-serach">
                                        <form action="" method="POST">
                                            <div class="hamburger quickadd-hamburger">
                                                <div class="wrapper-action">
                                                    <i class="fa fa-plus"></i>
                                                </div>
                                            </div>
                                            <div class="nav-action quick-add-input" id="quick-add-input">
                                                <div class="form-inline">
                                                    <label for=""><span class="text-danger">*</span>Quick Add </label>
                                                    <select id="edititemsDropDown" class="form-control">
                                                        <option value="">Items</option>
                                                        <?php

                                                        if ($sqlData['use_type'] == "material") {
                                                            $selectSql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `" . ERP_INVENTORY_ITEMS . "` as goods ON stock.itemId=goods.itemId WHERE  stock.company_id=$company_id AND (goods.goodsType=1 OR goods.goodsType=4)  AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId desc", true);
                                                        } elseif ($sqlData['use_type'] == "servicep") {
                                                            $selectSql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `" . ERP_INVENTORY_ITEMS . "` as goods ON stock.itemId=goods.itemId WHERE goods.goodsType=7  AND stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId desc", true);
                                                        } elseif ($sqlData['use_type'] == "asset") {
                                                            $selectSql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `" . ERP_INVENTORY_ITEMS . "` as goods ON stock.itemId=goods.itemId WHERE goods.goodsType=9  AND stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId desc", true);
                                                        } else {
                                                            $selectSql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `" . ERP_INVENTORY_ITEMS . "` as goods ON stock.itemId=goods.itemId WHERE (goods.goodsType=1  OR goods.goodsType=4  OR goods.goodsType=5 OR goods.goodsType=9)  AND stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId desc", true);
                                                        }

                                                        foreach ($selectSql['data'] as $data) {

                                                        ?>
                                                            <option value="<?= $data['itemId'] ?>"><?= $data['itemName'] . "[" . $data['itemCode'] . "]" ?></option>
                                                        <?php
                                                        }
                                                        ?>




                                                    </select>
                                                </div>
                                            </div>


                                    </div>
                                </div>

                                <table class="table tabel-hover table-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Qty</th>
                                            <th>Remaining Qty</th>
                                            <th>Srn/Grn Qty</th>
                                            <th>Unit Price</th>
                                            <th>Base Price</th>
                                            <th>GST</th>
                                            <th>GST Amount</th>
                                            <th>Total Price</th>
                                            <th>Action</th>

                                        </tr>
                                    </thead>
                                    <tbody id="itemsTable">
                                        <?php
                                        $po_ite_sql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` WHERE `po_id`='" . $id . "' ";
                                        $po = queryGet($po_ite_sql, true);
                                        $po_data = $po['data'];
                                        // console($po);
                                        foreach ($po_data as $data) {
                                            // console($data);
                                            $qty = $data['qty'];
                                            $amount = $data['total_price'];
                                            $unit_price = $data['unitPrice'];
                                            $basePrice = $qty * $unit_price;


                                            $itemId = $data['inventory_item_id'];
                                            $getItemObj = $ItemsObj->getItemById($itemId);
                                            // console($data['po_item_id']);
                                            // console($getItemObj);
                                            $itemCode = $getItemObj['data']['itemCode'];
                                            $curr = $sqlData['currency'];




                                            $randCode = $getItemObj['data']['itemId'] . rand(00, 99);

                                            $select_grn = queryGet("SELECT SUM(grn_item.`receivedQty`) AS  sum_item FROM `erp_grn_goods` AS grn_item LEFT JOIN `erp_grn` AS grn ON grn.grnId=grn_item.grnId  WHERE grn.`grnPoNumber` = '" . $poNumber . "' AND `goodId` = $itemId", true);
                                            //  console($select_grn);

                                            // $grn_qty = $select_grn

                                            //     $hsn = $getItemObj['data']['hsnCode'];
                                            //     $gstPercentage = queryGet("SELECT * FROM `erp_hsn_code` WHERE `hsnCode` = '" . $hsn . "'");
                                            //  //   console($gstPercentage);

                                            //     // console($randCode);
                                            //     $gstAmount = ($gstPercentage['data']['taxPercentage'] / 100) * $basePrice;
                                            //     $totalAmount = $basePrice + $gstAmount;
                                            $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
                                            // console($companyCurrencyObj);
                                            $companyCurrencyData = $companyCurrencyObj["data"];

                                            $comp_currency = $companyCurrencyData["currency_name"];
                                            $vendor_curr_sql = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`= $curr ");

                                            $vendor_curr = $vendor_curr_sql['data']['currency_name'];


                                        ?>

                                            <!-- <input type ="hidden" id="" name="" value=""> -->
                                            <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>">
                                                <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][update_itemId]" value="<?= $getItemObj['data']['itemId'] ?>">
                                                <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][update_poitemId]" value="<?= $data['po_item_id'] ?>">
                                                <td>
                                                    <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][update_itemCode]" value="<?= $getItemObj['data']['itemCode'] ?>">
                                                    <?= $getItemObj['data']['itemCode'] ?>
                                                </td>
                                                <td class="pre-normal">
                                                    <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][update_itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
                                                    <?= $getItemObj['data']['itemName'] ?>
                                                </td>
                                                <td>
                                                    <div class="flex-display">
                                                        <input type="number" step="any" name="listItem[<?= $randCode ?>][update_qty]" value="<?= $data['qty'] ?>" class="form-control full-width updateitemQty" id="updateitemQty_<?= $randCode ?>">

                                                        <input type="hidden" id="actualTotalQty_<?= $randCode ?>" value="<?= $data['qty'] ?>" readonly>
                                                        <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>
                                                        <input type="hidden" name="listItem[<?= $randCode ?>][update_uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>">

                                                    </div>
                                                    <p id="issueItemQty_<?= $randCode ?>" class="error"></p>
                                                </td>
                                                <td>
                                                    <div class="flex-display">
                                                        <input type="number" step="any" name="listItem[<?= $randCode ?>][update_remQty]" value="<?= $data['remainingQty'] ?>" class="form-control full-width updateitemRemQty" id="updateitemRemQty_<?= $randCode ?>" readonly>

                                                        <input type="hidden" value="<?= $data['remainingQty'] ?>" id="updateitemRemQtyHidden_<?= $randCode ?>" readonly>
                                                        <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>
                                                        <input type="hidden" name="listItem[<?= $randCode ?>][update_uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>">

                                                    </div>
                                                    <p id="issueItemRemQty_<?= $randCode ?>" class="error"></p>
                                                </td>
                                                <td>
                                                    <div class="flex-display">
                                                        <input type="number" step="any" name="listItem[<?= $randCode ?>][update_srnQty]" value="<?= $select_grn['data'][0]['sum_item'] ?>" class="form-control full-width updateitemSrnQty" id="updateitemSrnQty_<?= $randCode ?>" readonly>
                                                        <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>
                                                        <input type="hidden" name="listItem[<?= $randCode ?>][update_uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <!-- <input type="number" name="listItem[<?= $randCode ?>][update_unitPrice]" id="itemUnitPrice_<?= $randCode ?>"  class="form-control full-width-center itemUnitPrice" step="any" value="<?= $data['qty'] ?>"> -->
                                                    <?= $vendor_curr ?>
                                                    <input type="number" step="any" name="listItem[<?= $randCode ?>][update_unitPrice]" value="<?= $data['unitPrice'] * $sqlData['conversion_rate'] ?>" class="form-control full-width updateitemUnitPrice" id="updateitemUnitPrice_<?= $randCode ?>" data-attr="<?= $randCode ?>">
                                                    <div class="d-flex gap-2 my-1">
                                                        <?= $comp_currency ?> <p id="local_unit_price_<?= $randCode ?>"></p><?= $data['unitPrice'] ?>
                                                    </div>
                                                    <input type="hidden" name="listItem[<?= $randCode ?>][update_unitPriceHidden]" value="" id="ItemUnitPriceTdInputhidden_<?= $randCode ?>" class="form-control text-xs itemUnitPricehidden">

                                                </td>
                                                <!-- <td class="flex-display">
            <input type="number" name="listItem[<?= $randCode ?>][totalDiscount]" value="0.00" class="form-control full-width-center itemDiscount">%
        </td> -->

                                                <td>
                                                    <?= $vendor_curr ?>
                                                    <input type="number" name="listItem[<?= $randCode ?>][update_basePrice]" value="<?= $basePrice * $sqlData['conversion_rate'] ?>" class="form-control full-width-center updateitemBasePrice" id="updateitemBasePrice_<?= $randCode ?>" data-attr="<?= $randCode ?>" readonly>
                                                    <div class="d-flex gap-2 my-1">
                                                        <?= $comp_currency ?> <p id="local_base_price_<?= $randCode ?>"><?= $basePrice ?></p>
                                                    </div>
                                                </td>

                                                <td>
                                                    <input type="number" name="listItem[<?= $randCode ?>][update_gst]" value="<?= $data['gst'] ?>" class="form-control full-width-center updategst" id="updategst_<?= $randCode ?>" data-attr="<?= $randCode ?>" readonly>

                                                </td>
                                                <td>
                                                    <?= $vendor_curr ?>
                                                    <input type="number" name="listItem[<?= $randCode ?>][update_gstAmount]" value="<?= $data['gstAmount'] * $sqlData['conversion_rate'] ?>" class="form-control full-width-center updategstAmount" id="updategstAmount_<?= $randCode ?>" data-attr="<?= $randCode ?>" readonly>
                                                    <div class="d-flex gap-2 my-1">
                                                        <?= $comp_currency ?> <p id="local_gst_amount_<?= $randCode ?>"><?= $data['gstAmount']  ?></p>
                                                    </div>
                                                </td>

                                                <td>
                                                    <?= $vendor_curr ?>
                                                    <input type="number" name="listItem[<?= $randCode ?>][update_totalPrice]" id="updateitemTotalPrice_<?= $randCode ?>" value="<?= $data['total_price'] * $sqlData['conversion_rate'] ?>" class="form-control full-width-center updateitemTotalPrice" data-attr="<?= $randCode ?>" step="any" readonly>
                                                    <div class="d-flex gap-2 my-1">
                                                        <?= $comp_currency ?> <p id="local_total_price_<?= $randCode ?>"><?= $data['total_price'] ?></p>
                                                    </div>

                                                </td>
                                                <td>
                                                    <button type="button" class="btn-view btn btn-primary" data-toggle="modal" data-target="#deliveryScheduleModal_<?= $randCode ?>">
                                                        <i class="statusItemBtn fa fa-cog" id="statusItemBtn_<?= $getItemObj['data']['itemId'] ?>"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger delItemBtn" id="delItemBtn_<?= $itemId  ?>">
                                                        <i class="fa fa-minus"></i>
                                                    </button>



                                                    <div class="modal modal-left left-item-modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" id="deliveryScheduleModal_<?= $randCode ?>" tabindex="-1" role="dialog" aria-labelledby="left_modal" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title text-white">Delivery Shedule</h5>

                                                                </div>
                                                                <div class="modal-body">
                                                                    <!-- <h6 class="modal-title">Total Qty: <span class="mainQty" id="mainQty_<?= $randCode ?>">1</span></h6> -->
                                                                    <div class="row">
                                                                        <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                                                            <div class="add-btn-plus">
                                                                                <a style="cursor: pointer" class="btn btn-primary waves-effect waves-light" onclick='addDeliveryQtyUpdate(<?= $randCode ?>)'>
                                                                                    <i class="fa fa-plus"></i>
                                                                                </a>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-12 col-md-12 col-sm-12  modal-add-row modal-add-row-delivery_<?= $randCode ?>">
                                                                            <?php
                                                                            $delivery_schedules = queryGet("SELECT * FROM `erp_branch_purchase_order_delivery_schedule` WHERE `po_item_id` = '" . $data['po_item_id'] . "' ", true);
                                                                            $rand_counts = 1;
                                                                            foreach ($delivery_schedules['data'] as $delivery_schedule) {
                                                                                $rand_count = $rand_counts++;

                                                                            ?>

                                                                                <div class="row">
                                                                                    <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
                                                                                        <div class="form-input">
                                                                                            <label>Delivery date</label>
                                                                                            <input type="date" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $rand_count ?>][multiDeliveryDate]" class="form-control delDate delDate_<?= $rand_count ?>" data-attr="<?= $rand_count ?>" id="delivery-date" placeholder="delivery date" value="<?= $delivery_schedule['delivery_date'] ?>">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                                                                        <div class="form-input">
                                                                                            <label>Quantity</label>
                                                                                            <input type="text" step="any" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $rand_count ?>][quantity]" class="form-control updatemultiQuantity updatemultiQty_<?= $rand_count ?>" data-attr="<?= $rand_count ?>" id="multiQuantity_<?= $randCode ?>" placeholder="quantity" value="<?= $delivery_schedule['qty'] ?>">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                                                                        <div class="add-btn-minus">
                                                                                            <a style="cursor: pointer" class="btn btn-danger update_qty_minus" data-attr="<?= $rand_count ?>">
                                                                                                <i class="fa fa-minus"></i>
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            <?php
                                                                            }

                                                                            ?>
                                                                            <!-- <div class="row">
                                                                            <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
                                                                                <div class="form-input">
                                                                                    <label>Delivery date</label>
                                                                                    <input type="date" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][multiDeliveryDate]" class="form-control delDate delDate_<?= $randCode ?>" data-attr="<?= $randCode ?>" id="delivery-date" placeholder="delivery date" value="<?= $_GET['deliveryDate'] ?>">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                                                                <div class="form-input">
                                                                                    <label>Quantity</label>
                                                                                    <input type="text" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][quantity]" class="form-control updatemultiQuantity updatemultiQty_<?= $randCode ?>" data-attr="<?= $randCode ?>" id="multiQuantity_<?= $randCode ?>" placeholder="quantity" value="0">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                                                                <div class="add-btn-plus">
                                                                                    <a style="cursor: pointer" class="btn btn-primary waves-effect waves-light" onclick='addDeliveryQtyUpdate(<?= $randCode ?>)'>
                                                                                        <i class="fa fa-plus"></i>
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div> -->
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer modal-footer-fixed">
                                                                    <button type="submit" id="finalBtn_<?= $randCode ?>" class="btn btn-primary save-close-btn btn-xs float-right waves-effect waves-light" data-dismiss="modal" aria-label="Close">Save & Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>



                                                </td>

                                            </tr>







                                        <?php  } ?>
                                    </tbody>

                                    <!-- <tbody class="total-calculate">
                                        <tr>
                                            <td class="text-right" style="border: none;"> </td>
                                            <td style="border: none;"><b>Total Amount</b></td>
                                            <td></td>
                                            <td></td>
                                            <input type="hidden" name="totalAmt" id="update_grandTotalAmountInput" value="<?= $sqlData['totalAmount'] ?>">
                                            <td colspan="2" style="border: none; background: none; " id="update_grandTotalAmount"><b>₹<?= $sqlData['totalAmount'] ?></b></th>
                                        </tr>

                                    </tbody> -->
                                    <tbody class="total-calculate">
                                        <tr>
                                            <td colspan="7" class="text-right p-2" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Sub Total</td>
                                            <input type="hidden" name="update_subTotal" id="update_subTotalAmountInput" value="0.00">
                                            <td class="text-right pr-2" style="border: none; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?php echo $vendor_curr; ?> </span><span id="update_subTotalAmount">0.00</span>
                                                </small>
                                                <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic"><?php echo $vendor_curr; ?></span><span id="">0.00</span>)
                                                </small> -->
                                            </td>
                                        </tr>


                                        <tr class="p-2 igstTr" style="display:none">
                                            <td colspan="7" class="text-right p-2" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">IGST</td>
                                            <input type="hidden" name="update_igstInput" id="update_igst" value="<?= $sqlData['total_igst'] ?>">
                                            <td class="text-right pr-2" style="border: none; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?php echo $vendor_curr; ?> </span><span id="update_igst_span"><?= $sqlData['total_igst'] ?></span>
                                                </small>
                                                <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic"><?php echo $vendor_curr; ?></span><span id=""><?= $sqlData['total_igst'] ?></span>)
                                                </small> -->
                                            </td>
                                        </tr>
                                        <tr class="p-2 cgstTr" style="display:none">
                                            <td colspan="7" class="text-right p-2" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">CGST</td>
                                            <input type="hidden" name="update_cgstInput" id="update_cgst" value="<?= $sqlData['total_cgst'] ?>">
                                            <td class="text-right pr-2" style="border: none; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?php echo $vendor_curr; ?> </span><span id="update_cgst_span"><?= $sqlData['total_cgst'] ?></span>
                                                </small>
                                                <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic"><?php echo $vendor_curr; ?></span><span class=""><?= $sqlData['total_cgst'] ?></span>)
                                                </small> -->
                                            </td>
                                        </tr>
                                        <tr class="p-2 sgstTr" style="display:none">
                                            <td colspan="7" class="text-right p-2" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">SGST</td>
                                            <input type="hidden" name="update_sgstInput" id="update_sgst" value="<?= $sqlData['total_sgst'] ?>">
                                            <td class="text-right pr-2" style="border: none; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?php echo $vendor_curr; ?> </span><span id="update_sgst_span"><?= $sqlData['total_sgst'] ?></span>
                                                </small>
                                                <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic"><?php echo $vendor_curr; ?></span><span class=""><?= $sqlData['total_sgst'] ?></span>)
                                                </small> -->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7"></td>
                                            <td colspan="2">

                                            </td>
                                        </tr>
                                        <tr class="p-2">
                                            <td colspan="7" class="text-right p-2" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 font-weight-bold totalCal" style="border-top: 3px double !important; background: none;padding: 0px !important;">Total Amount</td>
                                            <input type="hidden" name="update_totalAmt" id="update_grandTotalAmountInput" value="0.00">
                                            <td class="font-weight-bold text-right pr-2" style="border-top: 3px double !important; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?php echo $vendor_curr; ?> </span><span id="update_grandTotalAmount">0.00</span>
                                                </small>
                                                <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic"><?php echo $vendor_curr; ?></span><span id="">0.00</span>)
                                                </small>
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="btns">
                        <button type="submit" name="editNewPOFormSubmitBtn" class="btn btn-xs btn-primary items-search-btn float-right editNewPOFormSubmitBtn" id="editNewPOFormSubmitBtn">Update</button>

                    </div>


            </div>

            </form>
            <!-- modal -->
            <div class="modal" id="addNewItemsFormModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header py-1" style="background-color: #003060; color:white;">
                            <h4 class="modal-title">Add New Items</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <!-- <form action="" method="post" id="addNewItemsForm"> -->
                            <div class="col-md-12 mb-3">
                                <div class="input-group">
                                    <input type="text" name="itemName" class="m-input" required>
                                    <label>Item Name</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="text" name="itemDesc" class="m-input" required>
                                    <label>Item Description</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group btn-col">
                                    <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                                </div>
                            </div>
                            <!-- </form> -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- modal end -->
    </div>
    </section>
    </div>












<?php
} elseif (isset($_GET['selectItemPr'])) {
    $items = $_GET['selectItemPr'];
    // console($items);
    $array = [];
    foreach ($items as $item_data) {
        $pr_item = queryGet("SELECT *  FROM `erp_branch_purchase_request_items` WHERE `prItemId`=$item_data");

        $array[] = array('pr_item' => $pr_item);
    }
    $prcodes = "";
    $prcode = [];
    foreach ($array as $pr) {
        // console($pr['pr_item']['data'][0]['prId']);
        $pr_id = $pr['pr_item']['data']['prId'];
        $pr_sql = queryGet("SELECT * FROM `erp_branch_purchase_request` WHERE `purchaseRequestId`= $pr_id GROUP BY `prCode`");
        // console($pr_sql);
        $pr_code = $pr_sql['data']['prCode'];
        $pr_type = $pr_sql['data']['pr_type'];
        // $prcode .=  $pr_code.",";
        $prcode[] = $pr_code;
    }
    $unique = array_unique($prcode);
    foreach ($unique as $code) {
        $prcodes .= $code . ",";
    }
    $ref_no = substr($prcodes, 0, strlen($prcodes) - 1);

    // $ref_no = array(substr($prcode,0,strlen($prcode)-1));

    // console(array_unique($ref_no));
    // echo implode("",$prcode['prcode']);

    // echo $pr_type;

?>
    <div class="content-wrapper is-purchase-order">
        <section class="content">
            <div class="container-fluid">


                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Purchase Order List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Purchase Order</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>

                <form action="" method="POST" id="submitPoForm" name="submitPoForm">
                <input type="hidden" name="poOrigin" value="pr">

                    <input type="hidden" name="createData" id="createData" value="">
                    <div class="row po-form-creation">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="card so-creation-card po-creation-card  po-creation-card ">
                                        <div class="card-header">
                                            <div class="row customer-info-head">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="head">
                                                        <i class="fa fa-user"></i>
                                                        <h4>Vendor Info</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body others-info vendor-info so-card-body">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="row info-form-view">
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <div class="form-inline input-box customer-select">
                                                                <!-- <label for="">Select Vendor</label>
                                                                &nbsp; &nbsp; -->
                                                                <select name="vendorId" id="vendorDropdown" class="selct-vendor-dropdown">
                                                                    <option value="">Select Vendor</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <div class="customer-info-text po-customer-info-text" id="vendorInfo">

                                                            </div>

                                                        </div>
                                                        <?php
                                                        $location = "SELECT * FROM  `erp_branch_otherslocation` WHERE `othersLocation_id`='" . $location_id . "' ";
                                                        $locConn = queryGet($location);
                                                        $locData = $locConn['data'];
                                                        // console($locData['othersLocation_building_no']);
                                                        $otherLocation = "SELECT * FROM  `erp_branch_otherslocation` WHERE `company_id`='" . $company_id . "' ";
                                                        $otherLocConn = queryGet($otherLocation, true);
                                                        $otherLocData = $otherLocConn['data'];

                                                        ?>

                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                                <div class="row address-section">
                                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                                        <div class="address-to bill-to">
                                                                            <h5>Bill to</h5>
                                                                            <hr class="mt-0 mb-2">
                                                                            <p>
                                                                                <?=
                                                                                $locData['othersLocation_building_no'] . "," . $locData['othersLocation_flat_no'] . "," . $locData['othersLocation_street_name'] . "," . $locData['othersLocation_pin_code'] . "," .  $locData['othersLocation_location'] . "," . $locData['othersLocation_district'] . "," .  $locData['othersLocation_city'] . "," .  $locData['othersLocation_state']
                                                                                ?>
                                                                            </p>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                                        <div class="address-to ship-to">
                                                                            <div class="row">
                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                    <h5>Ship to</h5>
                                                                                </div>
                                                                                <div class="col-lg-8 col-md-8 col-sm-8">
                                                                                    <h5 class="display-inline">
                                                                                        <div class="checkbox-label">
                                                                                            <input type="checkbox" id="addresscheckbox" name="addresscheckbox" value="1" title="checked here for same as Bill To adress" checked>
                                                                                            <p>Same as Bill to</p>
                                                                                        </div>
                                                                                        <button type="button" class="btn btn-primary change-address-modal text-xs btn-sm" data-toggle="modal" data-target="#address-change">Change</button>
                                                                                    </h5>
                                                                                </div>
                                                                            </div>



                                                                            <hr class="mt-0 mb-2">
                                                                            <p id="shipToAddressDiv">
                                                                                <?=
                                                                                $locData['othersLocation_building_no'] . "," . $locData['othersLocation_flat_no'] . "," . $locData['othersLocation_street_name'] . "," . $locData['othersLocation_pin_code'] . "," .  $locData['othersLocation_location'] . "," . $locData['othersLocation_district'] . "," .  $locData['othersLocation_city'] . "," .  $locData['othersLocation_state']
                                                                                ?>
                                                                            </p>
                                                                            <input type="hidden" name="shipToInput" id="shipToInput" value="<?= $locData['othersLocation_id'] ?>">
                                                                            <input type="hidden" name="shipToState" id="shipToState" value="<?= $locData['state_code'] ?>">




                                                                        </div>

                                                                        <!----------Address modal-------->

                                                                        <div class="modal fade address-change-modal" id="address-change" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                                            <div class="modal-dialog" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header card-header">
                                                                                        <div class="head">
                                                                                            <i class="fa fa-map-marker-alt"></i>
                                                                                            <h4>Change Address</h4>
                                                                                        </div>
                                                                                        <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                                                                            <li class="nav-item" role="presentation">
                                                                                                <button class="btn btn-primary address-btn otheraddressbtn nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#savedAddress" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Other Address</button>

                                                                                            </li>
                                                                                            <li class="nav-item" role="presentation">
                                                                                                <button class="btn btn-primary address-btn newaddress nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#newAddress" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">New Address</button>
                                                                                            </li>
                                                                                        </ul>
                                                                                    </div>

                                                                                    <div class="modal-body" style="height:15rem;">

                                                                                        <div class="tab-content " id="pills-tabContent">
                                                                                            <div class="tab-pane otherAddress-tab-pen fade show active" id="savedAddress" role="tabpanel" aria-labelledby="pills-home-tab">
                                                                                                <?php
                                                                                                foreach ($otherLocData as $data) {
                                                                                                ?>

                                                                                                    <div class="address-to bill-to">
                                                                                                        <input type="radio" class="address-check" name="shipToAddress" value="<?= $data['othersLocation_id'] ?>">
                                                                                                        <h5 id="shipToAddressHeadText_<?= $data['othersLocation_id'] ?>"><?= $data['othersLocation_name'] ?></h5>
                                                                                                        <hr class="mt-0 mb-2">
                                                                                                        <p id="shipToAddressBodyText_<?= $data['othersLocation_id'] ?>">

                                                                                                            <?=
                                                                                                            $data['othersLocation_building_no'] . "," . $data['othersLocation_flat_no'] . "," . $data['othersLocation_street_name'] . "," . $data['othersLocation_pin_code'] . "," .  $data['othersLocation_location'] . "," . $data['othersLocation_district'] . "," .  $data['othersLocation_city'] . "," .  $data['othersLocation_state']
                                                                                                            ?>
                                                                                                        </p>
                                                                                                        <p id="shipToStateCode_<?= $data['othersLocation_id'] ?>">
                                                                                                            <?= $data['state_code'] ?>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                <?php
                                                                                                }
                                                                                                ?>
                                                                                                <div class="modal-footer">
                                                                                                    <button type="button" id="closeBtn" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                                    <button type="button" class="btn btn-primary" id="shipToAddressSaveBtn" data-dismiss="mo    dal">Save changes</button>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="tab-pane newAddress-tab-pen fade" id="newAddress" role="tabpanel" aria-labelledby="pills-profile-tab">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                                                                                                        <label for="">Building Number</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                                                                                                        <label for="">Flat Number</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                                                                                                        <label for="">Street Name</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                                                                                        <label for="">Location</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">City</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">Pin Code</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">District</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                                        <label for="">State</label>
                                                                                                        <input type="text" class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12"></div>
                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12"></div>
                                                                                                </div>
                                                                                                <div class="modal-footer">
                                                                                                    <button type="button" id="closeBtn" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                                    <button type="button" class="btn btn-primary" id="shipToAddressSaveBtn" data-dismiss="modal">Save changes</button>
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="card so-creation-card po-creation-card  po-creation-card">
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
                                <div class="card-body others-info">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="row info-form-view">

                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <label for="date">Delivery Date</label>
                                                    <input type="date" id="deliveryDate" name="deliveryDate" min="<?= $today ?>" class="form-control" value="<?= $sqlData['expectedDate'] ?>" />
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <label for="date">PO Creation Date</label>
                                                    <input type="date" name="podatecreation" id="podatecreation" class="form-control" min="<?= $min ?>" max="<?= $max ?>">
                                                    <p id="podatelabel"></p>
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-input">
                                                        <label for="date">Validity Period</label>
                                                        <input type="date" class="form-control" id="dateInputvalid" name="validitydate" min="<?php echo date('Y-m-d'); ?>" required>
                                                        <p id="validitylabel"></p>
                                                    </div>
                                                </div>


                                            </div>
                                            <div class="row info-form-view">

                                                <?php

                                                $useType = $pr_type;
                                                if ($useType == "servicep" || $useType == "service") {
                                                    $useType_val = "servicep";
                                                    $useType = 'Service Purchase';
                                                } else {
                                                    $useType_val = $useType;
                                                }

                                                ?>
                                                <div class="col-lg-3 col-md-3 col-sm-12">
                                                    <label for="date">Use Types</label>
                                                    <select name="usetypesDropdown" id="usetypesDropdown" class="form-control typesDropdown">
                                                        <option value="<?= $useType_val ?>"><?= $useType ?></option>

                                                    </select>

                                                </div>
                                                <?php
                                                if ($useType ==  "servicep" || $useType == "service") {

                                                ?>
                                                    <div class="col-lg-3 col-md-3 col-sm-12">
                                                        <label for="date">Select Parent PO</label>
                                                        <select id="parent" class="form-control parent" name="parent_po">
                                                            <option value="">Select</option>
                                                            <?php

                                                            $get_po = queryGet("SELECT * FROM `erp_branch_purchase_order` WHERE `location_id` = $location_id", true);
                                                            foreach ($get_po['data'] as $parent) {

                                                            ?>
                                                                <option value="<?= $parent['po_id'] ?>"><?= $parent['po_number'] ?></option>
                                                            <?php
                                                            }
                                                            ?>

                                                        </select>

                                                    </div>
                                                <?php
                                                }
                                                ?>


                                                <div class="col-lg-3 col-md-3 col-sm-12 cost-center-col">
                                                    <div class="cost-center" style="display: none;">
                                                        <label for="">Cost Center</label>
                                                        <select name="costCenter" class="form-control">
                                                            <option value="">Cost Center</option>
                                                            <?php
                                                            $funcList = $BranchPoObj->fetchFunctionality()['data'];
                                                            foreach ($funcList as $func) {
                                                            ?>
                                                                <option value="<?= $func['CostCenter_id'] ?>">
                                                                    <?= $func['CostCenter_code'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-12 cost-center-col">
                                                    <label for="">PO Type</label>
                                                    <select name="potypes" id="potypes" onclick="craateUserJsObject.ShowPoTypes();" class="form-control typesDropdown">
                                                        <option value="">Select PO Type</option>
                                                        <option id="domestic" value="domestic">Domestic</option>
                                                        <option id="international" value="international">International
                                                        </option>
                                                    </select>
                                                </div>
                                                <?php

                                                if ($pr_type == "servicep" || $pr_type == 'service') {
                                                } else {


                                                ?>
                                                    <div class="col-lg-3 col-md-3 col-sm-12 radio-condition" id="incoTerms">
                                                        <div class="radio-types radio-types-fob-cif" style="display: none;">
                                                            <label for="" class="inco-terms">Inco Terms</label>
                                                            <div class="form-input-radio form-input-fob">

                                                                <input type="radio" value="FOB" name="domestic">
                                                                <div class="tooltip-label">
                                                                    <label for="">FOB</label>
                                                                    <div class="help-tip fob-tooltip">
                                                                        <p>Free On Board or Freight on Board</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-input-radio form-input-cif">
                                                                <input type="radio" value="CIF" name="domestic">
                                                                <div class="tooltip-label">
                                                                    <label for="">CIF</label>
                                                                    <div class="help-tip cif-tooltip">
                                                                        <p>Cost, insurance, and freight is an international
                                                                            shipping agreement</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="radio-types radio-types-ex-for" style="display: none;">
                                                            <div class="form-input-radio form-input-ex-work">
                                                                <label for="" class="inco-terms">Inco Terms</label>
                                                                <input type="radio" value="exwork" name="domestic">
                                                                <div class="tooltip-label">
                                                                    <label for="">Ex Work</label>
                                                                    <div class="help-tip ex-work-tooltip">
                                                                        <p>An domestic trade term that describes when a
                                                                            seller makes a product available at a designated
                                                                            location</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-input-radio form-input-for">
                                                                <input type="radio" value="FOR" name="domestic">
                                                                <div class="tooltip-label">
                                                                    <label for="">FOR</label>
                                                                    <div class="help-tip for-tooltip">
                                                                        <p>F.O.R. stands for “Free on Road” means the goods which is being sent from source station to its destination includes transportation and all other transit expenses. All applicable taxes and duties on goods remains extra.</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="row info-form-view">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="tooltip-label">
                                                        <label for="">Reference</label>
                                                        <div class="help-tip">
                                                            <p>Vendor PO</p>
                                                        </div>
                                                    </div>
                                                    <input type="text" name="refNo" class="form-control" value="<?= $ref_no ?>" />
                                                </div>


                                                <?php

                                                $check_func = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id`=$location_id");
                                                $funcs = $check_func['data']['companyFunctionalities'];
                                                $func_ex = explode(",", $funcs);



                                                ?>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="func-area">
                                                        <label for="">Functional Area</label>
                                                        <select name="funcArea" class="form-control">
                                                            <option value="">Functional Area</option>
                                                            <?php

                                                            foreach ($func_ex as $func) {
                                                                $func_area = queryGet("SELECT * FROM `erp_company_functionalities` WHERE `functionalities_id`=$func", true);
                                                                //console($func_area);

                                                            ?>

                                                                <option value="<?= $func_area['data'][0]['functionalities_id'] ?>"><?= $func_area['data'][0]['functionalities_name'] ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <?php
                                                $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
                                                $companyCurrencyData = $companyCurrencyObj["data"];

                                                $comp_currency = $companyCurrencyData["currency_name"];
                                                ?>

                                                <div class="currency-conversion-section mt-3">
                                                    <div class="static-currency">
                                                        <input type="text" class="form-control" value="1" readonly>
                                                        <input type="text" class="form-control text-right" value="<?= $comp_currency ?>" readonly>
                                                    </div>
                                                    <div class="dynamic-currency">
                                                        <input type="text" name="curr_rate" id="currency_conversion_rate" value="1" class="form-control">
                                                        <select id="selectCurrency" name="currency" class="form-control text-right">
                                                            <?php

                                                            $curr = queryGet("SELECT * FROM `erp_currency_type`", true);
                                                            foreach ($curr['data'] as $data) {
                                                            ?>
                                                                <option value="<?= $data['currency_id'] ?>" data-currname="<?= $data['currency_name'] ?>" <?php if ($comp_currency == $data['currency_name']) {
                                                                                                                                                                echo "selected";
                                                                                                                                                            } ?>><?= $data['currency_name'] ?></option>
                                                            <?php
                                                            }
                                                            ?>

                                                        </select>
                                                    </div>
                                                    <div class="display-flex grn-form-input-text mt-3">
                                                        <p class="label-bold text-italic" style="white-space: pre-line;">Vendor Currency</p>
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
                        <!-- <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row others-info-head">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="head">
                                        <i class="fa fa-info"></i>
                                        <h4>Items Info</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card items-select-table">
                                <div class="head-item-table">
                                    <div class="advanced-serach">
                                        <form action="" method="POST">



                                        </form>
                                    </div>
                                </div>

                                <table class="table tabel-hover table-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>PR Number</th>
                                            <th>Qty</th>
                                            <th>Unit Price</th>
                                            <th>Base Amount</th>
                                            <th>GST</th>
                                            <th>GST Amount</th>
                                            <th>Total Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTable">
                                        <?php
                                        foreach ($items as $data) {



                                            $pr_ite_sql = "SELECT *  FROM `erp_branch_purchase_request_items` WHERE `prItemId`='" . $data . "' ";

                                            $pr = queryGet($pr_ite_sql, true);
                                            $pr_data = $pr['data'];

                                            // console($pr_data);
                                            foreach ($pr_data as $data) {
                                                $pr_id = $data['prId'];
                                                // console($data['itemId']);
                                                $qty = $data['itemQuantity'];
                                                $remaining_qty = $data['remainingQty'];
                                                $pr_item_id = $data['prItemId'];
                                                $itemId = $data['itemId'];
                                                $getItemObj = $ItemsObj->getItemById($itemId);
                                                // console($getItemObj);
                                                $itemCode = $getItemObj['data']['itemCode'];
                                                $lastPricesql = "SELECT * FROM `erp_branch_purchase_order_items`as po_item JOIN `erp_branch_purchase_order` as po ON po_item.`po_id`=po.po_id WHERE `location_id`=$location_id AND `itemCode`=$itemCode ORDER BY po_item.`po_item_id` DESC LIMIT 1";
                                                $last = queryGet($lastPricesql);
                                                $lastRow = $last['data'] ?? "";
                                                $lastPrice = $lastRow['unitPrice'] ?? "";


                                                $PrCodesql = queryGet("SELECT * FROM `" . ERP_BRANCH_PURCHASE_REQUEST . "` WHERE `purchaseRequestId`=$pr_id");
                                                $prCode = $PrCodesql['data']['prCode'];


                                                $basePrice = $lastPrice * $remaining_qty;

                                                $randCode = $getItemObj['data']['itemId'] . rand(00, 99);

                                                $hsn = $getItemObj['data']['hsnCode'];
                                                $gstPercentage = queryGet("SELECT * FROM `erp_hsn_code` WHERE `hsnCode` = '" . $hsn . "'");


                                                $gstAmount = ($gstPercentage['data']['taxPercentage'] / 100) * $basePrice ?? 0;
                                                $totalAmount = $basePrice + $gstAmount;

                                        ?>

                                                <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>">
                                                    <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemId]" value="<?= $getItemObj['data']['itemId'] ?>">

                                                    <td>
                                                        <?= $getItemObj['data']['itemCode'] ?>
                                                        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][pritemId]" value="<?= $pr_item_id ?>">
                                                        <input class="form-control full-width" type="hidden" id="remqty_<?= $randCode ?>" name="listItem[<?= $randCode ?>][remQty]" value="<?= $remaining_qty ?>">
                                                        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemCode]" value="<?= $getItemObj['data']['itemCode'] ?>">
                                                        <input type="hidden" id="random" <?= $getItemObj['data']['itemCode'] ?>>
                                                    </td>
                                                    <td>
                                                        <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
                                                        <?= $getItemObj['data']['itemName'] ?>
                                                    </td>
                                                    <td>
                                                        <?= $prCode ?>
                                                        <input type="hidden" name="listItem[<?= $randCode ?>][pr_id]" value="<?= $data['prId'] ?>">
                                                    </td>
                                                    <td>
                                                        <div class="flex-display">
                                                            <input type="number" step="any" name="listItem[<?= $randCode ?>][qty]" value="<?= $remaining_qty ?>" class="form-control full-width itemQty" data-val="<?= $randCode ?>" min="1" id="itemQty_<?= $randCode ?>">
                                                            <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>
                                                            <input type="hidden" name="listItem[<?= $randCode ?>][uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>">
                                                        </div>
                                                        <p id="qty_error_<?= $randCode ?>"></p>
                                                    </td>
                                                    <td>

                                                        <input type="text" name="listItem[<?= $randCode ?>][unitPrice]" id="itemUnitPrice_<?= $randCode ?>" value="<?= $lastPrice ?>" class="form-control full-width-center itemUnitPrice">

                                                        <input type="hidden" name="listItem[<?= $randCode ?>][unitPriceHidden]" value="<?= $lastPrice ?>" id="ItemUnitPriceTdInputhidden_<?= $randCode ?>" class="form-control text-xs itemUnitPricehidden">


                                                    </td>

                                                    <td>
                                                        <input type="number" name="listItem[<?= $randCode ?>][basePrice]" value="<?= $basePrice ?>" class="form-control full-width-center itemBasePrice" id="itemBasePrice_<?= $randCode ?>" readonly>
                                                        <div class="d-flex gap-2 my-1">
                                                            <?= $comp_currency ?> <p id="local_base_price_<?= $randCode ?>">0.00</p>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <input type="number" name="listItem[<?= $randCode ?>][gst]" value="<?= $gstPercentage['data']['taxPercentage'] ?? 0 ?>" class="form-control full-width-center gst" id="gst_<?= $randCode ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="listItem[<?= $randCode ?>][gstAmount]" value="<?= $gstAmount  ?>" class="form-control full-width-center gstAmount" id="gstAmount_<?= $randCode ?>" readonly>
                                                    </td>

                                                    <td>
                                                        <input type="text" name="listItem[<?= $randCode ?>][totalPrice]" id="itemTotalPrice_<?= $randCode ?>" value="<?= $totalAmount  ?>" class="form-control full-width-center itemTotalPrice" readonly>
                                                    </td>
                                                    <td class="action-flex-btn">

                                                        <button type="button" class="btn-view btn btn-primary" data-toggle="modal" data-target="#deliveryScheduleModal_<?= $randCode ?>">
                                                            <i class="statusItemBtn fa fa-cog" id="statusItemBtn_<?= $getItemObj['data']['itemId'] ?>"></i>
                                                        </button>

                                                        <button class="btn btn-danger">
                                                            <i class="fa fa-minus" id="delItemBtn_<?= $getItemObj['data']['itemId'] ?>"></i>
                                                        </button>



                                                        <div class="modal modal-left left-item-modal fade" id="deliveryScheduleModal_<?= $randCode ?>" tabindex="-1" role="dialog" aria-labelledby="left_modal">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Delivery Shedule <?= $randCode ?></h5>

                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-lg-12 col-md-12 col-sm-12  modal-add-row modal-add-row-delivery_<?= $randCode ?>">

                                                                                <div class="row">
                                                                                    <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
                                                                                        <div class="form-input">
                                                                                            <label>Delivery date</label>
                                                                                            <input type="date" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][multiDeliveryDate]" class="form-control" id="delivery-date" placeholder="delivery date" value="">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                                                                        <div class="form-input">
                                                                                            <label>Quantity</label>
                                                                                            <input type="text" step="any" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][quantity]" class="form-control multiQuantity" id="multiQuantity_<?= $randCode ?>" placeholder="quantity" value="">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                                                                        <div class="add-btn-plus">
                                                                                            <a style="cursor: pointer" class="btn btn-primary waves-effect waves-light" onclick='addDeliveryQty(<?= $randCode ?>)'>
                                                                                                <i class="fa fa-plus"></i>
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer modal-footer-fixed">
                                                                        <button type="submit" class="btn btn-primary save-close-btn btn-xs float-right waves-effect waves-light" data-dismiss="modal">Save & Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>







                                        <?php  }
                                        } ?>
                                    </tbody>
                                    <!-- <tbody class="total-calculate">
                                        <tr>
                                            <td style="border: none;"> </td>
                                            <td style="border: none; padding-left: 15px !important;"><b>Total Amount</b></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <input type="hidden" name="totalAmt" id="grandTotalAmountInput" value="0.00">
                                            <td style="border: none; background: none; padding-left: 15px !important;" id="grandTotalAmount"><b>0.00</b></th>
                                        </tr>

                                    </tbody> -->

                                    <tbody class="total-calculate">
                                        <tr>
                                            <td colspan="7" class="text-right p-2" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Sub Total</td>
                                            <input type="hidden" name="subTotal" id="subTotalAmountInput" value="0.00">
                                            <td class="text-right pr-2" style="border: none; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol">INR </span><span id="subTotalAmount">0.00</span>
                                                </small>
                                                <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span id="">0.00</span>)
                                                </small> -->
                                            </td>
                                        </tr>


                                        <tr class="p-2 igstTr" style="display:none">
                                            <td colspan="7" class="text-right p-2" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">IGST</td>
                                            <input type="hidden" name="igstInput" id="igst" value="0.00">
                                            <td class="text-right pr-2" style="border: none; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol">INR </span><span id="igst_span">0.00</span>
                                                </small>
                                                <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span id="">0.00</span>)
                                                </small> -->
                                            </td>
                                        </tr>
                                        <tr class="p-2 cgstTr" style="display:none">
                                            <td colspan="7" class="text-right p-2" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">CGST</td>
                                            <input type="hidden" name="cgstInput" id="cgst" value="0.00">
                                            <td class="text-right pr-2" style="border: none; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol">INR </span><span id="cgst_span">0.00</span>
                                                </small>
                                                <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span class="">0</span>)
                                                </small> -->
                                            </td>
                                        </tr>
                                        <tr class="p-2 sgstTr" style="display:none">
                                            <td colspan="7" class="text-right p-2" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">SGST</td>
                                            <input type="hidden" name="sgstInput" id="sgst" value="0.00">
                                            <td class="text-right pr-2" style="border: none; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol">INR </span><span id="sgst_span">0.00</span>
                                                </small>
                                                <!-- <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span class="">0</span>)
                                                </small> -->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7"></td>
                                            <td colspan="2">

                                            </td>
                                        </tr>
                                        <tr class="p-2">
                                            <td colspan="7" class="text-right p-2" style="border: none; background: none;"> </td>
                                            <td colspan="0" class="text-left p-2 font-weight-bold totalCal" style="border-top: 3px double !important; background: none;padding: 0px !important;">Total Amount</td>
                                            <input type="hidden" name="totalAmt" id="grandTotalAmountInput" value="0.00">
                                            <td class="font-weight-bold text-right pr-2" style="border-top: 3px double !important; background: none;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol">INR </span><span id="grandTotalAmount">0.00</span>
                                                </small>
                                                <small class="text-small font-weight-bold text-primary convertedDiv" style="display: none;">
                                                    (<span class="rupee-symbol currency-symbol-dynamic">INR</span><span id="">0.00</span>)
                                                </small>
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card so-creation-card po-creation-card  po-creation-card po-others-info">
                                <div class="card-header">
                                    <div class="row others-info-head">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="head">
                                                <i class="fa fa-info"></i>
                                                <h4>Others Cost infos</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body others-accordion-info" id="cost">

                                    <div class="row accordion-other-cost">
                                        <div class="col">
                                            <div class="tabs">
                                                <div class="tab">
                                                    <input type="checkbox" id="chck1" style="display: none;">
                                                    <label class="tab-label" for="chck1">Freight & Other Cost</label>
                                                    <div class="tab-content">
                                                        <div class="row othe-cost-infor modal-add-row_537">
                                                            <div class="row othe-cost-infor">
                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Select</label>
                                                                        <select class="form-control serviceDropDown" id="serviceDropDown" name="FreightCost[l1][service_purchase]">
                                                                            <option value="">Select Service</option>

                                                                            <?php
                                                                            $service_select = queryGet("SELECT * FROM `erp_inventory_items` WHERE `goodsType`=7 AND `location_id`=$location_id", true);
                                                                            //console($service_select);
                                                                            foreach ($service_select['data'] as $service) {
                                                                            ?>

                                                                                <option value="<?= $service['itemId'] ?>">[<?= $service['itemCode'] ?>] <?= $service['itemName'] ?></option>

                                                                            <?php

                                                                            }


                                                                            ?>

                                                                        </select>
                                                                    </div>
                                                                </div>



                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Vendor Select</label>
                                                                        <select class="form-control" name="FreightCost[l1][txt]">
                                                                            <option value="">Select Vendor</option>
                                                                            <?php echo $vendrSelect;     ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="service description" name="FreightCost[l1][service]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Amount</label>
                                                                        <input type="number" class="form-control amount" id="amount" placeholder="amount" name="FreightCost[l1][amount]">
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="add-btn-plus">
                                                                        <a style="cursor: pointer" class="btn btn-primary" onclick="addMultiQtyf(537)">
                                                                            <i class="fa fa-plus"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>



                                            </div>
                                        </div>
                                    </div>

                                    <!-- <button class="accordion" type="button">1.Know about this accordian</button>
                  <div class="panel">
                    <div class="row">
                      <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="form-input">
                          <label for="">Transportation #1</label>
                          <input type="text" name="FreightCost[l1][txt]" class="form-control" id="delivery-date" placeholder="L1" value="">
                        </div>
                      </div>
                      <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="form-input">
                          <label for="">GST</label>
                          <input type="text" name="gst" class="form-control" id="other-cost-gst" placeholder="L1" value="">
                        </div>
                      </div>
                      <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="form-input">
                          <label for="">Base Amount</label>
                          <input type="number" name="baseamount" class="form-control" id="baseamount" placeholder="L1" value="">
                        </div>
                      </div>
                      <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="form-input">
                          <label for="">Total Amount</label>
                          <input type="number" name="totalamount" class="form-control" id="totalAmount" placeholder="L1" value="">
                        </div>
                      </div>
                    </div>

                  </div>

                  <button class="accordion" type="button">2.using javascript</button>
                  <div class="panel">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                  </div> -->
                                </div>

                                <!-- <div class="accordion-item other-info-cost-accordion-accordion">
                  <h2 class="accordion-header" id="flush-other-info">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-otherInfo" aria-expanded="false" aria-controls="flush-collapseOne">
                      Other Cost Info
                    </button>
                  </h2>
                  <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="row others-info-form-view">
                        <div class="col-lg-6 col-md-6 col-sm-12">

                        </div>
                      </div>
                      <div class="row others-info-form-view" id="level">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="accordion accordion-flush other-item-info-accordion" id="accordionFlushExample">
                              <div class="accordion-item freist-accordion">
                                <h2 class="accordion-header" id="flush-freight">
                                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOneFreight" aria-expanded="false" aria-controls="flush-collapseOne">
                                    Freight Cost
                                  </button>
                                </h2>
                                <div id="flush-collapseOneFreight" class="accordion-collapse collapse" aria-labelledby="flush-freight" data-bs-parent="#accordionFlushExample">
                                  <div class="accordion-body">
                                    <div class="card">
                                      <div class="card-body">

                                        <div class="row freight Cost">
                                          <div class="col-lg-6 col-md-6 col-sm-12">
                                            <label for="">Transportation #1</label>
                                            <input type="text" name="FreightCost[l1][txt]" class="form-control" id="delivery-date" placeholder="L1" value="">
                                          </div>
                                          <div class="col-lg-6 col-md-6 col-sm-12">
                                            <label for="">Amount #1</label>
                                            <input type="number" name="FreightCost[l1][amount]" class="form-control multiQuantity" id="multiQuantity_538" placeholder="Amount" value="">
                                          </div>
                                        </div>
                                        <div class="row freight Cost">
                                          <div class="col-lg-6 col-md-6 col-sm-12">
                                            <label for="">Transportation 21</label>
                                            <input type="text" name="FreightCost[l2][txt]" class="form-control" id="delivery-date" placeholder="L2" value="">
                                          </div>
                                          <div class="col-lg-6 col-md-6 col-sm-12">
                                            <label for="">Amount #2</label>
                                            <input type="number" name="FreightCost[l2][amount]" class="form-control multiQuantity" id="multiQuantity_538" placeholder="Amount" value="">

                                          </div>
                                        </div>
                                        <div class="row freight Cost">
                                          <div class="col-lg-6 col-md-6 col-sm-12">
                                            <label for="">Transportation #3</label>

                                            <input type="text" name="FreightCost[l3][txt]" class="form-control" id="delivery-date" placeholder="L4" value="">
                                          </div>
                                          <div class="col-lg-6 col-md-6 col-sm-12">
                                            <label for="">Amount #3</label>
                                            <input type="number" name="FreightCost[l3][amount]" class="form-control multiQuantity" id="multiQuantity_538" placeholder="Amount" value="">
                                          </div>
                                        </div>
                                        <div class="row freight Cost">
                                          <div class="col-lg-6 col-md-6 col-sm-12">
                                            <label for="">Transportation #4</label>
                                            <input type="text" name="FreightCost[L4][txt]" class="form-control" id="delivery-date" placeholder="L4" value="">
                                          </div>
                                          <div class="col-lg-6 col-md-6 col-sm-12">
                                            <label for="">Amount #4</label>
                                            <input type="number" name="FreightCost[L4][amount]" class="form-control multiQuantity" id="multiQuantity_538" placeholder="Amount" value="">
                                          </div>
                                        </div>





                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>



                              <div id="flush-otherInfo" class="accordion-collapse collapse" aria-labelledby="flush-other-info" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                  <div class="card">
                                    <div class="card-body">
                                      <div class="row othe-cost-infor modal-add-row_538">
                                        <div class="col-lg-5 col-md-5 col-sm-11">
                                          <input type="text" name="OthersCost[1][txt]" class="form-control" id="delivery-date" placeholder="Others" value="">
                                          <label for="">other #1</label>
                                        </div>
                                        <div class="col-lg-5 col-md-5 col-sm-11">
                                          <input type="number" name="OthersCost[1][amount]" class="form-control multiQuantity" id="multiQuantity_538" placeholder="Amount" value="">
                                          <label for="">amount #1</label>
                                        </div>
                                        <div class="col-lg-1 col-md-1 col-sm-1">
                                          <a style="cursor: pointer" class="btn btn-success" onclick="addMultiQty(538)">
                                            <i class="fa fa-plus"></i>
                                          </a>
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
                </div> -->
                            </div>

                            <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-primary save-close-btn btn-xs float-right add_data" id="pobtn" value="add_post">Save & Close</button>
                            <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-danger save-close-btn btn-xs float-right add_data" id="podbtn" value="add_draft">Save as Draft</button>
                        </div>
                    </div>
            </div>
    </div>

    </form>
    <!-- modal -->
    <div class="modal" id="addNewItemsFormModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-1" style="background-color: #003060; color:white;">
                    <h4 class="modal-title">Add New Items</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <!-- <form action="" method="post" id="addNewItemsForm"> -->
                    <div class="col-md-12 mb-3">
                        <div class="input-group">
                            <input type="text" name="itemName" class="m-input" required>
                            <label>Item Name</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" name="itemDesc" class="m-input" required>
                            <label>Item Description</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="input-group btn-col">
                            <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                        </div>
                    </div>
                    <!-- </form> -->
                </div>
            </div>
        </div>
    </div>
    <!-- modal end -->
    </div>
    </section>
    </div>

<?php
} else {

?>
    <div class="content-wrapper is-purchase-order">
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
                                    <h3 class="card-title">Manage Purchase order</h3>
                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?po-creation" class="btn btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
                                </li>
                            </ul>
                            <a href="manage-pr.php" class="btn float-right"><button type="button" class="btn btn-primary">View Purchase Requests</button></a>


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
                                                <div class="col-lg-11 col-md-11 col-sm-11">
                                                    <div class="filter-search">
                                                        <div class="filter-list">
                                                            <a href="manage-purchases-orders.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>All</a>
                                                            <a href="po-items.php" class="btn"><i class="fa fa-list mr-2"></i>Item Order List</a>
                                                            <a href="pending-po.php" class="btn"><i class="fa fa-clock mr-2"></i>Pending PO</a>
                                                            <a href="pending-po.php?open" class="btn"><i class="fa fa-lock-open mr-2"></i>Open PO</a>
                                                            <a href="pending-po.php?closed" class="btn"><i class="fa fa-lock mr-2"></i>Closed PO</a>
                                                            <a href="pending-po.php?service" class="btn"><i class="fa fa-male mr-2"></i>Service PO</a>
                                                        </div>
                                                        <div class="dropdown filter-dropdown" id="filterDropdown">

                                                            <button type="button" class="dropbtn" id="dropBtn">
                                                                <i class="fas fa-filter po-list-icon"></i>
                                                            </button>

                                                            <div class="dropdown-content">
                                                                <a href="manage-purchases-orders.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>All</a>
                                                                <a href="po-items.php" class="btn"><i class="fa fa-list mr-2"></i>Item Order List</a>
                                                                <a href="pending-po.php" class="btn"><i class="fa fa-clock mr-2"></i>Pending PO</a>
                                                                <a href="pending-po.php?open" class="btn"><i class="fa fa-lock-open mr-2"></i>Open PO</a>
                                                                <a href="pending-po.php?closed" class="btn"><i class="fa fa-lock mr-2"></i>Closed PO</a>
                                                                <a href="pending-po.php?service" class="btn"><i class="fa fa-male mr-2"></i>Service PO</a>
                                                            </div>
                                                        </div>
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
                                                <div class="col-lg-1 col-md-1 col-sm-1">
                                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?po-creation" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
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
                                                                <input type="text" name="keyword2" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php /*if (isset($_REQUEST['keyword2'])) {
                                                                                                                                                      echo $_REQUEST['keyword2'];
                                                                                                                                                    } */ ?>">
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                <select name="status_s" id="status_s" class="fld form-control" style="appearance: auto;">
                                                                    <option value=""> Status </option>
                                                                    <option value="6" <?php if (isset($_REQUEST['status_s']) && '6' == $_REQUEST['status_s']) {
                                                                                            echo 'selected';
                                                                                        } ?>>Active
                                                                    </option>
                                                                    <option value="7" <?php if (isset($_REQUEST['status_s']) && '7' == $_REQUEST['status_s']) {
                                                                                            echo 'selected';
                                                                                        } ?>>Inactive
                                                                    </option>
                                                                    <option value="8" <?php if (isset($_REQUEST['status_s']) && '8' == $_REQUEST['status_s']) {
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
                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                    <?php
                                    $cond = '';

                                    $sts = " AND `status`!='deleted'";
                                    if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                        $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                    }

                                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                        $cond .= " AND delivery_date between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                    }


                                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                        $cond .= " AND `po_number` like '%" . $_REQUEST['keyword2'] . "%' OR `po_date` like '%" . $_REQUEST['keyword2'] . "%' OR `ref_no` like '%" . $_REQUEST['keyword2'] . "%' ";
                                    } else {
                                        if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                            $cond .= " AND `po_number` like '%" . $_REQUEST['keyword'] . "%'  OR `po_date` like '%" . $_REQUEST['keyword'] . "%' OR  `ref_no` like '%" . $_REQUEST['keyword'] . "%'";
                                        }
                                    }

                                    $sql_list = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE 1 " . $cond . "  AND`branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=$company_id " . $sts . "  ORDER BY po_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                                    $qry_list = queryGet($sql_list, true);
                                    $num_list = $qry_list['numRows'];
                                    $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE 1 " . $cond . " AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=$company_id " . $sts . " ";
                                    $countQry = mysqli_query($dbCon, $countShow);
                                    $rowCount = mysqli_fetch_array($countQry);
                                    $count = $rowCount[0];
                                    $cnt = $GLOBALS['start'] + 1;
                                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_PURCHASE_ORDER", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                    $settingsCheckbox = unserialize($settingsCh);
                                    if ($num_list > 0) {
                                    ?>
                                        <table class="table defaultDataTable table-hover">
                                            <thead>
                                                <tr class="alert-light">
                                                    <th>#</th>
                                                    <th>Vendor Code</th>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <th>Vendor Icon</th>
                                                    <?php }
                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <th>Vendor Name</th>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <th>Reference Number</th>
                                                    <?php }
                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <th>PO Date</th>
                                                    <?php  }
                                                    if (in_array(5, $settingsCheckbox)) { ?>
                                                        <th>PO Number</th>
                                                    <?php }
                                                    if (in_array(6, $settingsCheckbox)) { ?>
                                                        <th>Total Item</th>
                                                    <?php  }
                                                    if (in_array(7, $settingsCheckbox)) { ?>
                                                        <th>Total Amount</th>
                                                    <?php }
                                                    if (in_array(8, $settingsCheckbox)) { ?>
                                                        <th>Delivery Date</th>
                                                    <?php }



                                                    if (in_array(9, $settingsCheckbox)) { ?>
                                                        <th>Use type</th>
                                                    <?php }
                                                    if (in_array(10, $settingsCheckbox)) { ?>
                                                        <th>Inco Type</th>
                                                    <?php }
                                                    if (in_array(11, $settingsCheckbox)) { ?>
                                                        <th>Ship Address</th>
                                                    <?php }

                                                    if (in_array(12, $settingsCheckbox)) { ?>
                                                        <th>Bill Address</th>
                                                    <?php }


                                                    if (in_array(13, $settingsCheckbox)) { ?>
                                                        <th>Status</th>
                                                    <?php }




                                                    if (in_array(14, $settingsCheckbox)) { ?>
                                                        <th>Validity Period</th>
                                                    <?php } ?>




                                                    <th>Action</th>
                                                </tr>
                                            </thead>



                                            <tbody>
                                                <?php
                                                $poList = $qry_list['data'];

                                                foreach ($poList as $onePoList) {
                                                    // console($onePoList['po_number']);
                                                    $trade_name =  $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0]['trade_name'];
                                                    $cost_sql = queryGet("SELECT * FROM `erp_cost_center` WHERE `CostCenter_id` = '" . $onePoList['cost_center'] . "'");
                                                    $cost_center = $cost_sql['data']['CostCenter_code'];

                                                    $ship_location_sql = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id` = '" . $onePoList['ship_address'] . "'");
                                                    //  console($ship_location_sql);
                                                    $ship_location = $ship_location_sql['data']['othersLocation_name'] . "," . $ship_location_sql['data']['othersLocation_building_no'] . "," . $ship_location_sql['data']['othersLocation_flat_no'] . "," . $ship_location_sql['data']['othersLocation_street_name'] . "," . $ship_location_sql['data']['othersLocation_pin_code'] . "," . $ship_location_sql['data']['othersLocation_location'] . "," . $ship_location_sql['data']['othersLocation_city'] . "," . $ship_location_sql['data']['othersLocation_district'] . "," . $ship_location_sql['data']['othersLocation_state'];

                                                    $bill_location_sql = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id` = '" . $onePoList['bill_address'] . "'");
                                                    //  console($ship_location_sql);
                                                    $bill_location = $bill_location_sql['data']['othersLocation_name'] . "," . $bill_location_sql['data']['othersLocation_building_no'] . "," . $bill_location_sql['data']['othersLocation_flat_no'] . "," . $bill_location_sql['data']['othersLocation_street_name'] . "," . $bill_location_sql['data']['othersLocation_pin_code'] . "," . $bill_location_sql['data']['othersLocation_location'] . "," . $bill_location_sql['data']['othersLocation_city'] . "," . $bill_location_sql['data']['othersLocation_district'] . "," . $bill_location_sql['data']['othersLocation_state'];

                                                    $check_cur = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`='" . $onePoList['currency'] . "'");
                                                    // console($check_cur);


                                                    $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
                                                    $companyCurrencyData = $companyCurrencyObj["data"];

                                                    $comp_currency = $companyCurrencyData["currency_name"];



                                                ?>
                                                    <tr>
                                                        <td><?= $cnt++ ?></td>
                                                        <td>
                                                            <?= $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0]['vendor_code'] ?>
                                                        </td>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <td>
                                                                <div class="img-fluid avatar rounded-circle d-flex justify-content-center align-items-center" style="border: 1px solid grey;"> <?php echo ucfirst(substr($trade_name, 0, 1)) ?> </div>
                                                            </td>

                                                        <?php }
                                                        if (in_array(2, $settingsCheckbox)) { ?>
                                                            <td><?= $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0]['trade_name'] ?></td>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['ref_no'] ?></td>
                                                        <?php }
                                                        if (in_array(4, $settingsCheckbox)) { ?>
                                                            <td><?= formatDateORDateTime($onePoList['po_date'], false);    ?></td>
                                                        <?php }
                                                        if (in_array(5, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['po_number'] ?></td>
                                                        <?php }
                                                        if (in_array(6, $settingsCheckbox)) { ?>
                                                            <td>
                                                                <?= $onePoList['totalItems'] ?>
                                                            </td>
                                                        <?php }

                                                        if (in_array(7, $settingsCheckbox)) { ?>
                                                            <td>
                                                                <?php echo $comp_currency;
                                                                echo $onePoList['totalAmount']; ?>
                                                                <br>
                                                                <?php
                                                                if ($check_cur['data']['currency_name'] != $comp_currency) {
                                                                    echo $check_cur['data']['currency_name'];
                                                                    echo $onePoList['totalAmount'] * $onePoList['conversion_rate'];
                                                                }
                                                                ?>

                                                            </td>
                                                        <?php }
                                                        if (in_array(8, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['delivery_date'] ?></td>
                                                        <?php }


                                                        if (in_array(9, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['use_type'] ?></td>
                                                        <?php }
                                                        if (in_array(10, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['inco_type'] ?></td>
                                                        <?php }
                                                        if (in_array(11, $settingsCheckbox)) { ?>
                                                            <td><?= $ship_location ?></td>
                                                        <?php }


                                                        if (in_array(12, $settingsCheckbox)) { ?>
                                                            <td><?= $bill_location ?></td>
                                                        <?php }

                                                        if (in_array(13, $settingsCheckbox)) { ?>
                                                            <td>
                                                                <?php
                                                                if ($onePoList['po_status'] == 14) {
                                                                    echo "Pending";
                                                                } else if ($onePoList['po_status'] == 9) {
                                                                    echo "Open";
                                                                } else if ($onePoList['po_status'] == 10) {
                                                                    echo "Closed";
                                                                } else if ($onePoList['po_status'] == 17) {
                                                                    echo "Rejected";
                                                                } else {
                                                                }
                                                                ?>


                                                            </td>

                                                        <?php }

                                                        if (in_array(14, $settingsCheckbox)) { ?>
                                                            <td>
                                                                <?php

                                                                if ($onePoList['validityperiod'] != '') {
                                                                    $date1 = new DateTime($onePoList['validityperiod']);
                                                                    $date2 = new DateTime(date('Y-m-d'));

                                                                    $interval = $date1->diff($date2);
                                                                    $countdays = $interval->format('%a');
                                                                    $day = "";
                                                                    if ($countdays > 1) {
                                                                        $day = "days";
                                                                    } else {
                                                                        $day = "day";
                                                                    }


                                                                    if ($onePoList['validityperiod'] < date('Y-m-d')) {
                                                                        echo "expired";
                                                                    } else {
                                                                        echo $countdays . " " . $day . " Remaining";
                                                                    }
                                                                } else {
                                                                    echo '-';
                                                                }
                                                                // echo "Difference in days: " . $interval->format('%a days');




                                                                ?>
                                                            </td>


                                                        <?php } ?>





                                                        <td>
                                                            <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>

                                                            <div class="modal fade right purchase-order-modal customer-modal classic-view-modal" id="fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                                <div class="modal-dialog modal-full-height w-100 modal-right modal-notify modal-success" role="document">
                                                                    <!--Content-->
                                                                    <div class="modal-content">
                                                                        <!--Header-->
                                                                        <div class="modal-header">

                                                                            <div class="customer-head-info">
                                                                                <div class="customer-name-code">
                                                                                    <h2><?php echo $comp_currency;
                                                                                        echo $onePoList['totalAmount']; ?>
                                                                                        <br>
                                                                                        <?php
                                                                                        if ($check_cur['data']['currency_name'] != $comp_currency) {
                                                                                            echo $check_cur['data']['currency_name'];
                                                                                            echo $onePoList['totalAmount'] * $onePoList['conversion_rate'];
                                                                                        }
                                                                                        ?>

                                                                                    </h2>
                                                                                    <p class="heading lead"><?= $onePoList['po_number'] ?></p>
                                                                                    <p>REF :&nbsp;<?= $onePoList['ref_no'] ?></p>

                                                                                </div>
                                                                                <?php
                                                                                $vendorDetails = $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0];
                                                                                ?>
                                                                                <div class="customer-image">
                                                                                    <div class="name-item-count">
                                                                                        <h5 title="<?= $vendorDetails['trade_name'] ?>"><?= $vendorDetails['trade_name'] ?></h5>
                                                                                        <span>
                                                                                            <div class="round-item-count"><?= $onePoList['totalItems'] ?></div> Items
                                                                                        </span>
                                                                                    </div>
                                                                                    <i class="fa fa-user"></i>
                                                                                </div>
                                                                            </div>

                                                                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= $onePoList['po_number'] ?>" role="tab" aria-controls="home" aria-selected="true">Info</a>
                                                                                </li>
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile<?= $onePoList['po_number'] ?>" role="tab" aria-controls="profile" aria-selected="false">Vendor Details</a>
                                                                                </li>
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link" id="preview-tab" data-toggle="tab" href="#preview<?= $onePoList['po_number'] ?>" role="tab" aria-controls="preview" aria-selected="false">Preview</a>
                                                                                </li>
                                                                                <?php
                                                                                if ($onePoList['po_status'] == 9) {
                                                                                ?>
                                                                                    <?php
                                                                                    // if ($onePoList['use_type'] == "service" || $onePoList['use_type'] == "servicep") {
                                                                                    ?>
                                                                                    <!-- <li class="nav-item">
                                                                                            <a class="nav-link" id="" data-toggle="" href="manage-manual-grn.php?view=<?= $onePoList['po_number'] ?>&type=srn"> SRN</a>
                                                                                        </li> -->
                                                                                    <?php
                                                                                    // } else {
                                                                                    ?>
                                                                                    <li class="nav-item">
                                                                                        <a class="nav-link" id="" data-toggle="" href="manage-manual-grn.php?view=<?= $onePoList['po_number'] ?>&type=grn"> GRN</a>
                                                                                    </li>
                                                                                <?php
                                                                                    // }
                                                                                }
                                                                                ?>
                                                                                <!-- -------------------Audit History Button Start------------------------- -->
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link auditTrail" id="history-tab<?= $onePoList['po_number'] ?>" data-toggle="tab" data-ccode="<?= $onePoList['po_number'] ?>" href="#history<?= $onePoList['po_number'] ?>" role="tab" aria-controls="history<?= $onePoList['po_number'] ?>" aria-selected="false">Trail</a>
                                                                                </li>
                                                                                <!-- -------------------Audit History Button End------------------------- -->
                                                                            </ul>
                                                                        </div>
                                                                        <div class="modal-body">

                                                                            <div class="tab-content" id="myTabContent">

                                                                                <div class="tab-pane fade show active" id="home<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="home-tab">
                                                                                    <?php
                                                                                    $itemDetails = $BranchPoObj->fetchBranchPoItems($onePoList['po_id'])['data'];
                                                                                    // console($itemDetails);


                                                                                    foreach ($itemDetails as $oneItem) {
                                                                                        // console($oneItem);
                                                                                    ?>
                                                                                        <form action="" method="POST">

                                                                                            <div class="hamburger">
                                                                                                <div class="wrapper-action">
                                                                                                    <i class="fa fa-cog fa-2x"></i>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="nav-action" id="thumb">
                                                                                                <a title="Notify Me" href="" name="vendorEditBtn">
                                                                                                    <i class="fa fa-bell"></i>
                                                                                                </a>
                                                                                            </div>
                                                                                            <?php if ($onePoList['po_status'] == 10) {
                                                                                            } else {
                                                                                            ?>
                                                                                                <div class="nav-action" id="create">
                                                                                                    <a href="manage-purchases-orders.php?edit=<?= $onePoList['po_id'] ?>" title="Edit" name="vendorEditBtn">
                                                                                                        <i class="fa fa-edit"></i>
                                                                                                    </a>
                                                                                                </div>
                                                                                            <?php
                                                                                            }

                                                                                            ?>
                                                                                            <div class="nav-action trash" id="share">
                                                                                                <a title="Delete" href="" name="vendorEditBtn">
                                                                                                    <i class="fa fa-trash"></i>
                                                                                                </a>
                                                                                            </div>

                                                                                        </form>


                                                                                        <div class="item-detail-section">
                                                                                            <h6>Items Details</h6>

                                                                                            <div class="card">
                                                                                                <div class="card-body">

                                                                                                    <div class="row">

                                                                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                                            <div class="left-section">
                                                                                                                <div class="icon-img">
                                                                                                                    <i class="fa fa-box"></i>
                                                                                                                </div>
                                                                                                                <div class="code-des">
                                                                                                                    <h4><?= $oneItem['itemCode'] ?></h4>
                                                                                                                    <p><?= $oneItem['itemName'] ?></p>
                                                                                                                    <p><?= $oneItem['unitPrice'] ?></p>

                                                                                                                    <p>
                                                                                                                        <h10>Quantity- <?= $oneItem['qty'] . "    " . $oneItem['uom'] ?></h10>
                                                                                                                    </p>
                                                                                                                    <?php
                                                                                                                    //   console($oneItem);
                                                                                                                    ?>
                                                                                                                    <p>

                                                                                                                        <h10>Remaining Quantity- <?php if ($oneItem['remainingQty'] != "") {
                                                                                                                                                        echo $oneItem['remainingQty'] . "  " . $oneItem['uom'];
                                                                                                                                                    } else {
                                                                                                                                                        echo 0;
                                                                                                                                                    }
                                                                                                                                                    ?></h10>

                                                                                                                    </p>
                                                                                                                    <p>


                                                                                                                        <h10>Unit Price-<?php echo $comp_currency;
                                                                                                                                        echo  $oneItem['unitPrice']; ?><br>
                                                                                                                            <?php
                                                                                                                            if ($check_cur['data']['currency_name'] != $comp_currency) {
                                                                                                                                echo $check_cur['data']['currency_name'];
                                                                                                                                echo $oneItem['unitPrice'] * $onePoList['conversion_rate'];
                                                                                                                            }
                                                                                                                            ?></h10>

                                                                                                                    </p>


                                                                                                                    <p>
                                                                                                                        <h10>Total Price- <?php echo $comp_currency;
                                                                                                                                            echo $oneItem['total_price']; ?><br>
                                                                                                                            <?php
                                                                                                                            if ($check_cur['data']['currency_name'] != $comp_currency) {
                                                                                                                                echo $check_cur['data']['currency_name'];
                                                                                                                                echo $oneItem['total_price'] * $onePoList['conversion_rate'];
                                                                                                                            }
                                                                                                                            ?></h10>
                                                                                                                    </p>



                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <hr>
                                                                                                    <?php
                                                                                                    $deliverySchedule = $BranchPoObj->fetchBranchPoItemsDeliverySchedule($oneItem['po_item_id'])['data'];

                                                                                                    foreach ($deliverySchedule as $dSchedule) {

                                                                                                    ?>

                                                                                                        <div class="row">
                                                                                                            <div class="col-lg-8 col-md-8 col-sm-8">
                                                                                                                <div class="left-section">
                                                                                                                    <div class="icon-img">
                                                                                                                        <i class="fa fa-clock"></i>
                                                                                                                    </div>
                                                                                                                    <div class="date-time-parent">
                                                                                                                        <div class="date-time">
                                                                                                                            <div class="code-des">
                                                                                                                                <h4>
                                                                                                                                    <?php
                                                                                                                                    // $timestamp = $dSchedule['delivery_date'];
                                                                                                                                    // $dt1 = date_format($timestamp, "d");
                                                                                                                                    echo $dSchedule['delivery_date'];
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
                                                                                                                    <div class="dropdown">
                                                                                                                        <button class="btn btn-secondary dropdown-toggle date-time-item" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                                                            <?= $dSchedule['qty'] ?> <?= $oneItem['uom'] ?>
                                                                                                                        </button>
                                                                                                                    </div>
                                                                                                                </div>

                                                                                                            </div>
                                                                                                        </div>
                                                                                                    <?php } ?>
                                                                                                </div>
                                                                                            </div>

                                                                                        </div>
                                                                                    <?php } ?>
                                                                                </div>

                                                                                <div class="tab-pane fade" id="profile<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="profile-tab">
                                                                                    <div class="row">
                                                                                        <div class="col-md-12">
                                                                                            <div class="accordion accordion-flush customer-details-sells-order" id="accordionFlushCustDetails">
                                                                                                <div class="accordion-item customer-details">
                                                                                                    <h2 class="accordion-header" id="flush-headingOne">
                                                                                                        <button class="accordion-button active" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOnePo" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                            <span>Vendor Details</span>
                                                                                                        </button>
                                                                                                    </h2>
                                                                                                    <div id="flush-collapseOnePo" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                        <div class="accordion-body cust-detsils-body">

                                                                                                            <div class="card">
                                                                                                                <div class="card-body">
                                                                                                                    <div class="row">
                                                                                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                            <?php
                                                                                                                            $vendorDetails = $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0];
                                                                                                                            ?>
                                                                                                                            <div class="icon">
                                                                                                                                <i class="fa fa-hashtag"></i>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                            <span>Vendor Code</span>
                                                                                                                        </div>
                                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                            <p>
                                                                                                                                <?= $vendorDetails['vendor_code'] ?>
                                                                                                                            </p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <hr>
                                                                                                                    <div class="row">
                                                                                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                            <div class="icon">
                                                                                                                                <i class="fa fa-user"></i>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                            <span>Vendor Name</span>
                                                                                                                        </div>
                                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                            <p>
                                                                                                                                <?= $vendorDetails['trade_name'] ?>
                                                                                                                            </p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <hr>
                                                                                                                    <div class="row">
                                                                                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                            <div class="icon">
                                                                                                                                <i class="fa fa-file"></i>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                            <span>GST</span>
                                                                                                                        </div>
                                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                            <p>
                                                                                                                                <?= $vendorDetails['vendor_gstin'] ?>
                                                                                                                            </p>
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

                                                                                <div class="tab-pane fade" id="preview<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="preview-tab">
                                                                                    <div class="card classic-view bg-transparent">
                                                                                        <div class="card-body classic-view-so-table" style="overflow: auto;">
                                                                                            <!-- <button type="button" class="btn btn-primary classic-view-btn float-right" id="printButton">Print Table</button> -->
                                                                                            <button type="button" class="btn btn-primary classic-view-btn float-right" onclick="window.print(); return false;">Print</button>
                                                                                            <div class="printable-view">
                                                                                                <h3 class="h3-title font-bold text-sm mb-4">Purchase Order</h3>
                                                                                                <?php
                                                                                                $vendor_id = $onePoList['vendor_id'];
                                                                                                $companyData = $BranchPoObj->fetchCompanyDetailsById($company_id)['data'];
                                                                                                $vendorDetails = $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0];
                                                                                                $vendor_address = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id` = $vendor_id AND `vendor_business_primary_flag` = 1");
                                                                                                //  console($vendor_address);

                                                                                                if ($vendor_address['numRows'] > 0) {
                                                                                                    $v_address = $vendor_address['data'];
                                                                                                } else {
                                                                                                    $vendor_address_first = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id` = $vendor_id AND `vendor_business_active_flag`=0  ORDER BY `vendor_business_id` ASC");
                                                                                                    // console($vendor_address_first);
                                                                                                    $v_address = $vendor_address_first['data'];
                                                                                                }



                                                                                                //    console($v_address);

                                                                                                //console($vendorDetails);



                                                                                                ?>
                                                                                                <table class="classic-view">
                                                                                                    <tbody>
                                                                                                        <tr>
                                                                                                            <td colspan="4" class="border-right">
                                                                                                                <p class="font-bold"><?= $vendorDetails['trade_name'] ?></p>
                                                                                                                <?php if ($v_address != null) { ?>
                                                                                                                    <p><?= $v_address['vendor_business_flat_no'] ?>, <?= $v_address['vendor_business_building'] ?></p>
                                                                                                                    <p><?= $v_address['vendor_business_district'] ?>,<?= $v_address['vendor_business_location'] ?>,<?= $v_address['vendor_business_pin'] ?></p>
                                                                                                                    <p><?= $v_address['vendor_city'] ?></p>
                                                                                                                    <p>State Name :<?= $vendorDetails['vendor_business_state'] ?></p>
                                                                                                                <?php
                                                                                                                }
                                                                                                                ?>

                                                                                                                <p>GSTIN/UIN: <?= $vendorDetails['vendor_gstin'] ?></p>
                                                                                                                <p>Company’s PAN: <?= $vendorDetails['vendor_pan'] ?></p>


                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <p>Purchase Order Number</p>
                                                                                                                <p class="font-bold"><?= $onePoList['po_number'] ?></p>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <p>Dated</p>
                                                                                                                <p class="font-bold"><?= formatDateORDateTime($onePoList['po_date']) ?></p>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td colspan="4" class="border-right">
                                                                                                                <p>Bill To Address</p>
                                                                                                                <p class="font-bold"><?= $companyData['company_name']  ?></p>
                                                                                                                <p class="font-bold"><?= $bill_location_sql['data']['othersLocation_name'] ?></p>
                                                                                                                <p><?= $bill_location_sql['data']['othersLocation_building_no'] ?> ,<?= $bill_location_sql['data']['othersLocation_flat_no']  ?>,<?= $bill_location_sql['data']['othersLocation_street_name'] ?>, <?= $bill_location_sql['data']['othersLocation_pin_code'] ?>, <?= $bill_location_sql['data']['othersLocation_location'] ?>, <?= $bill_location_sql['data']['othersLocation_city'] ?>, <?= $bill_location_sql['data']['othersLocation_district'] ?></p>
                                                                                                                <p>State Name : <?= $bill_location_sql['data']['othersLocation_state'] ?></p>
                                                                                                                <p>Place of Supply : <?= $bill_location_sql['data']['othersLocation_state'] ?></p>
                                                                                                            </td>
                                                                                                            <td colspan="3">
                                                                                                                <p>Ship To Address</p>
                                                                                                                <p class="font-bold"><?= $companyData['company_name']  ?></p>
                                                                                                                <p class="font-bold"><?= $ship_location_sql['data']['othersLocation_name'] ?></p>
                                                                                                                <p><?= $ship_location_sql['data']['othersLocation_building_no'] ?> ,<?= $ship_location_sql['data']['othersLocation_flat_no']  ?>,<?= $ship_location_sql['data']['othersLocation_street_name'] ?>, <?= $ship_location_sql['data']['othersLocation_pin_code'] ?>, <?= $ship_location_sql['data']['othersLocation_location'] ?>, <?= $ship_location_sql['data']['othersLocation_city'] ?>, <?= $ship_location_sql['data']['othersLocation_district'] ?></p>
                                                                                                                <p>State Name : <?= $ship_location_sql['data']['othersLocation_state'] ?></p>
                                                                                                                <p>Place of Supply : <?= $ship_location_sql['data']['othersLocation_state'] ?></p>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    </tbody>
                                                                                                    <tbody>
                                                                                                        <tr>
                                                                                                            <th>Sl No.</th>
                                                                                                            <th>Particulars</th>
                                                                                                            <th>Quantity</th>
                                                                                                            <th>UOM</th>
                                                                                                            <th>Rate</th>
                                                                                                            <th>Base Amount</th>
                                                                                                            <th>GST</th>
                                                                                                            <th>GST Amount</th>
                                                                                                            <th>Currency</th>
                                                                                                            <th>Total Amount</th>
                                                                                                        </tr>
                                                                                                        <?php
                                                                                                        $cnt = 1;
                                                                                                        foreach ($itemDetails as $oneItems) {
                                                                                                            // console($oneItems);

                                                                                                        ?>
                                                                                                            <tr>
                                                                                                                <td class="text-center"><?= $cnt++ ?></td>
                                                                                                                <td class="text-center">
                                                                                                                    <p class="font-bold"><?= $oneItems['itemName'] ?></p>
                                                                                                                    <p class="text-italic"><?= $oneItems['itemCode'] ?></p>
                                                                                                                </td>

                                                                                                                <td class="text-center"><?= $oneItems['qty'] ?></td>
                                                                                                                <td class="text-center"><?= $oneItems['uom'] ?></td>
                                                                                                                <td class="text-right"><?= $oneItems['unitPrice'] * $onePoList['conversion_rate'] ?></td>
                                                                                                                <td class="text-right"><?= ($oneItems['total_price'] - $oneItems['gstAmount']) * $onePoList['conversion_rate'] ?></td>
                                                                                                                <td class="text-right"><?= $oneItems['gst'] ?></td>
                                                                                                                <td class="text-right"><?= $oneItems['gstAmount'] * $onePoList['conversion_rate'] ?></td>
                                                                                                                <td class="text-right"><?= $check_cur['data']['currency_name'] ?></td>


                                                                                                                <td class="text-right"><?= $oneItems['total_price'] * $onePoList['conversion_rate'] ?></td>
                                                                                                            </tr>
                                                                                                        <?php

                                                                                                        }

                                                                                                        ?>
                                                                                                        <tr>
                                                                                                            <td colspan="10" class="text-right font-bold">

                                                                                                                <?php
                                                                                                                if ($check_cur['data']['currency_name'] != $comp_currency) {
                                                                                                                    echo $check_cur['data']['currency_name'];
                                                                                                                    echo $onePoList['totalAmount'] * $onePoList['conversion_rate'];
                                                                                                                }
                                                                                                                ?>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td colspan="2">
                                                                                                                <p> Total Amount (in words)</p>
                                                                                                                <p class="font-bold"><?= number_to_words_indian_rupees($onePoList['totalAmount']); ?></p>
                                                                                                            </td>
                                                                                                            <td colspan="3">
                                                                                                                <?php echo "* All values are in " . $check_cur['data']['currency_name'];
                                                                                                                ?>
                                                                                                            </td>
                                                                                                            <td colspan="5" class="text-right">E. & O.E</td>
                                                                                                        </tr>
                                                                                                        <!-- <tr>
                                                                                                            <td colspan="5"></td>
                                                                                                            <td colspan="5">
                                                                                                                <p class="font-bold">Company’s Bank Details</p>
                                                                                                                <p>Bank Name :</p>
                                                                                                                <p>A/c No. :</p>
                                                                                                                <p>Branch & IFS Code :</p>
                                                                                                            </td>
                                                                                                        </tr> -->
                                                                                                        <tr>
                                                                                                            <td colspan="5">
                                                                                                                <p>Remarks:</p>
                                                                                                                <p>Created By: <b><?php
                                                                                                                                    echo getCreatedByUser($onePoList['created_by']);
                                                                                                                                    ?></b></p>
                                                                                                            </td>
                                                                                                            <td colspan="5" class="text-right border">
                                                                                                                <p class="text-center font-bold"> for <?= $companyData['company_name'] ?></p>
                                                                                                                <p class="text-center sign-img">
                                                                                                                    <img width="60" src="../../public/storage/signature/bs-signature-demo_4.png" alt="signature">
                                                                                                                </p>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    </tbody>
                                                                                                </table>

                                                                                            <?php

                                                                                        }

                                                                                            ?>
                                                                                            </div>

                                                                                        </div>
                                                                                    </div>

                                                                                </div>

                                                                                <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                                <div class="tab-pane fade" id="history<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                                    <div class="audit-head-section mb-3 mt-3 ">
                                                                                        <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($onePoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePoList['created_at']) ?></p>
                                                                                        <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($onePoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePoList['updated_at']) ?></p>
                                                                                    </div>
                                                                                    <hr>
                                                                                    <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $onePoList['po_number'] ?>">

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
                                                                </div>
                                                                <!--/.Content-->
                                                            </div>
                                                        </td>
                                                    </tr>




                                                    <!-- right modal start here  -->


                                </div>

                            <?php }
                                    //  console($onePoList['po_number']); 
                            ?>
                            <!-- right modal end here  -->

                            </tbody>
                            <tbody>
                                <tr>
                                    <td colspan="9">
                                        <!-- Start .pagination -->

                                        <?php
                                        if ($count > 0 && $count > $GLOBALS['show']) {
                                        ?>
                                            <div class="pagination align-right">
                                                <?php pagination($count, "frm_opts"); ?>
                                            </div>

                                            <!-- End .pagination -->



                                            <!-- End .pagination -->
                                    </td>
                                </tr>

                                <!-- For Pegination------->
                                <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
                                    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                                                    echo  $_REQUEST['pageNo'];
                                                                                } ?>">
                                </form>
                                <!-- End Pegination from------->
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
                                        <input type="hidden" name="pageTableName" value="ERP_BRANCH_PURCHASE_ORDER" />
                                        <div class="modal-body">
                                            <div id="dropdownframe"></div>
                                            <div id="main2">
                                                <table>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                            Vendor Icon</td>
                                                    </tr>

                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                            Vendor Name</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                            Reference Number</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                                            PO Date</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                                            PO Number</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                                            Total Items</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />
                                                            Total Amount</td>
                                                    </tr>

                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox8" value="8" />
                                                            Delivery Date</td>
                                                    </tr>



                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(9, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox9" value="9" />
                                                            Use type</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(10, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox10" value="10" />
                                                            Inco Terms</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(11, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox11" value="11" />
                                                            Ship Address</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(12, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox12" value="12" />
                                                            Bill Address</td>
                                                    </tr>






                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(13, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox13" value="13" />
                                                            Status</td>
                                                    </tr>


                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(14, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox13" value="14" />
                                                            validity period</td>
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
    </div>
    </section>
    </div>
    <!-- End Pegination from------->


<?php
}
require_once("../common/footer.php");
?>

<!-- script for po type  -->
<script>
    $(document).on("change", "#dateInputvalid", function() {
        let del_date = $("#deliveryDate").val();
        let val_date = $(this).val();
        if (del_date > val_date) {
            $("#validitylabel").html(`<p class="text-danger text-xs" id="validitylabel">Delivery Date should not be greater than Validity date</p>`);
            document.getElementById("pobtn").disabled = true;
            document.getElementById("podbtn").disabled = true;
        } else {
            $("#validitylabel").html(`<p class="text-danger text-xs" id="validitylabel"></p>`);
            document.getElementById("pobtn").disabled = false;
            document.getElementById("podbtn").disabled = false;
        }



    });


    $(document).on("change", "#podatecreation", function() {

        // alert(1);


        let del_date = $("#deliveryDate").val();
        //alert(del_date);
        let poDate = $(this).val();
        if (del_date < poDate) {
            $("#podatelabel").html(`<p class="text-danger text-xs" id="podatelabel">PO creation Date should not be greater than delivery date</p>`);
            document.getElementById("pobtn").disabled = true;
            document.getElementById("podbtn").disabled = true;
        } else {
            $("#podatelabel").html(`<p class="text-danger text-xs" id="podatelabel"></p>`);
            document.getElementById("pobtn").disabled = false;
            document.getElementById("podbtn").disabled = false;
        }

    });

    $(document).on("change", "#deliveryDate", function() {




        let poDate = $("#podatecreation").val();


        let del_date = $(this).val();
        if (del_date < poDate) {
            $("#podatelabel").html(`<p class="text-danger text-xs" id="podatelabel">PO creation Date should not be greater than delivery date</p>`);
            document.getElementById("pobtn").disabled = true;
            document.getElementById("podbtn").disabled = true;
        } else {
            $("#podatelabel").html(`<p class="text-danger text-xs" id="podatelabel"></p>`);
            document.getElementById("pobtn").disabled = false;
            document.getElementById("podbtn").disabled = false;
        }

        let val_date = $("#dateInputvalid").val();
        if (del_date > val_date) {
            $("#validitylabel").html(`<p class="text-danger text-xs" id="validitylabel">Delivery Date should not be greater than Validity date</p>`);
            document.getElementById("pobtn").disabled = true;
            document.getElementById("podbtn").disabled = true;
        } else {
            $("#validitylabel").html(`<p class="text-danger text-xs" id="validitylabel"></p>`);
            document.getElementById("pobtn").disabled = false;
            document.getElementById("podbtn").disabled = false;
        }


    });


    function check_date() {


        let date = $("#podatecreation").val();

        let max = '<?php echo $max; ?>';
        let min = '<?php echo $min; ?>';



        if (date < min) {


            $("#podatelabel").html(`<p class="text-danger text-xs" id="podatelabel">Invalid PO creation Date</p>`);
            document.getElementById("pobtn").disabled = true;
            document.getElementById("podbtn").disabled = true;

        } else if (date > max) {
            $("#podatelabel").html(`<p class="text-danger text-xs" id="podatelabel">Invalid PO creation Date</p>`);
            document.getElementById("pobtn").disabled = true;
            document.getElementById("podbtn").disabled = true;
        } else {
            $("#podatelabel").html("");
            document.getElementById("pobtn").disabled = false;
            document.getElementById("podbtn").disabled = false;

        }



    }


    $(document).on("change", "#selectCurrency", function() {
        // alert(0);
        var selected_currency = $("#selectCurrency").find(':selected').data("currname");
        var company_currency = <?= json_encode($comp_currency)  ?>;

        $.ajax({
            url: "ajaxs/ajax-currency-convert.php?company_currency=" + company_currency + "&selected_currency=" + selected_currency,
            type: "GET",
            beforeSend: function() {
                $(`#currency_conversion_rate`).val("Loading....");
            },
            success: function(responseData) {
                var responseObj = JSON.parse(responseData);
                console.log(responseObj);
                $(`#currency_conversion_rate`).val(responseObj);
                // currency_conversion();
                calculateAllItemsGrandAmount();
                $('.rupee-symbol').html(selected_currency);

                // for (elem of $(".itemUnitPricehidden")) {
                // let rowNo = ($(elem).attr("id")).split("_")[1];
                // let newVal = ($(elem).val()) * responseObj;
                // // $(`#grnItemUnitPriceTdInput_${rowNo}`).val(newVal);
                // $(`#currency_conversion_rate`).val(responseObj);
                // $(`#spanInvoiceCurrencyName_${rowNo}`).html(selected_currency);

                // $(elem).val(newVal);
                // calculateOneItemAmounts(rowNo);
                //   };
            }
        });

    });


    function hidegst() {
        let ptype = $("#potypes").val();
        if (ptype == 'international') {
            let col = 4;
            if ($("th").hasClass("prnumbercol")) {
                console.log("pr exist");
                col = 5;
            }
            $(".colspanCng").attr('colspan', col);
            $(".gsthead").hide();
            $(".gstTD").hide();
            $("#igstCol").hide();

        } else {
            let col = 7
            if ($("th").hasClass("prnumbercol")) {
                console.log("pr exist");
                col = 8;
            }
            $(".colspanCng").attr('colspan', col);
            $(".gsthead").show();
            // $(".gstTD").show();
            /// $("#igstCol").show();

        }
    }

    $("#potypes").on("change", function() {
        // hidegst();
        $(".itemUnitPrice").each(function() {
            let rowNum = ($(this).attr("id")).split("_")[1];
            calculateOneUpdateItemRowAmountPoCreation(rowNum);

        });
        calculateAllItemsGrandAmount();


    })


    $("#potypesprpo").on("change", function() {
        // hidegst();
        $(".itemUnitPrice").each(function() {
            let rowNum = ($(this).attr("id")).split("_")[1];
            calculateOneUpdateItemRowAmountPrPoCreation(rowNum);

        });
        calculateAllItemsGrandAmount();


    })
</script>







<script>
    $(document).ready(function() {
        $("#dropBtn").on("click", function(e) {
            e.stopPropagation(); // Stop the event from propagating to the document
            console.log("clickedddd");
            $("#filterDropdown .dropdown-content").addClass("active");
            $("#filterDropdown").addClass("active");
        });

        $(document).on("click", function() {
            $("#filterDropdown .dropdown-content").removeClass("active");
            $("#filterDropdown").removeClass("active");
        });

        // Close the dropdown when clicking inside it
        $("#filterDropdown .dropdown-content").on("click", function(e) {
            e.stopPropagation(); // Prevent the event from reaching the document
        });

        // $(window).resize(function() {
        //     if ($(window).width() > 768) {
        //         $("#filterDropdown .dropdown-content").hide();
        //     }
        // });
    });
</script>
<script>
    $(document).on("click", ".add-btn-minus", function() {
        $(this).parent().parent().remove();
    });


    var modal = document.getElementById("address-change");

    var checkbox = document.getElementById("addresscheckbox");

    var body = document.body;

    var closeBtn = document.getElementById("closeBtn")


    checkbox.addEventListener("change", function() {
        if (!checkbox.checked) {
            body.classList.add("modal-open");
            modal.classList.add("show");
            modal.style.display = "block";


        } else {
            body.classList.remove("modal-open");
            modal.style.display = "none";

        }
    });



    $(document).on("click", "#shipToAddressSaveBtn", function() {
        //alert(1);
        document.getElementById("addresscheckbox").checked = false;
        // alert(1);
        console.log("clickinggggggggg");
        let radioBtnVal = $('input[name="shipToAddress"]:checked').val();
        //  alert(radioBtnVal);
        let addressHead = ($(`#shipToAddressHeadText_${radioBtnVal}`).html()).trim();
        let stateCode = ($(`#shipToStateCode_${radioBtnVal}`).html()).trim();
        let addressBody = ($(`#shipToAddressBodyText_${radioBtnVal}`).html()).trim();


        //  alert(addressBody);
        $("#shipToAddressDiv").html(addressBody);
        $("#shipToInput").val(radioBtnVal);
        $("#shipToState").val(stateCode);
        $('#address-change').toggle();


    });

    $(document).on("click", "#closeBtn", function() {
        // alert(1);
        modal.style.display = "none";
    });












    function addMultiQtyf(id) {
        let addressRandNo = Math.ceil(Math.random() * 100000);
        $(`.modal-add-row_${id}`).append(`  <div class="row othe-cost-infor">

                                                                     <div class="col-lg-3 col-md-12 col-sm-12">
                                                                        <div class="form-input">
                                                                            <label for="">Service Select</label>
                                                                            <select class="form-control serviceDropDown" id="serviceDropDown" name="FreightCost[${addressRandNo}][service_purchase_id]">
                                                                                <option value="">Select Service</option>

                                             <?php
                                                $service_select = queryGet("SELECT * FROM `erp_inventory_items` WHERE `goodsType`=7 AND `location_id`=$location_id", true);
                                                //console($service_select);
                                                foreach ($service_select['data'] as $service) {
                                                ?>

                                 <option value="<?= $service['itemId'] ?>">[<?= $service['itemCode'] ?>] <?= $service['itemName'] ?></option>

                                 <?php

                                                }


                                    ?>

                                                                            </select>
                                                                        </div>
                                                                    </div>


                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Vendor Select</label>
                                                                        <select class="form-control" name="FreightCost[${addressRandNo}][service_vendor]">
                                                                        <option value="">Select Vendor</option>
                                                                           <?php echo $vendrSelect; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="service description" name="FreightCost[${addressRandNo}][service_desc]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Amount</label>
                                                                        <input type="number" class="form-control amount" placeholder="amount" name="FreightCost[${addressRandNo}][service_amount]">
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="add-btn-minus">
                                                                        <a style="cursor: pointer" class="btn btn-danger">
                                                                            <i class="fa fa-minus"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>`);
    }


    function addMultiQty(id) {
        let addressRandNo = Math.ceil(Math.random() * 100000);
        $(`.modal-add-row_${id}`).append(`  <div class="row othe-cost-infor">
        <div class="col-lg-3 col-md-12 col-sm-12">
                                                                        <div class="form-input">
                                                                            <label for="">Service Select</label>
                                                                            <select class="form-control otherServiceDropDown" id="otherServiceDropDown" name="OthersCost[${addressRandNo}][service_purchase_id]">
                                                                                <option value="">Select Service</option>

                                             <?php
                                                $service_select = queryGet("SELECT * FROM `erp_inventory_items` WHERE `goodsType`=7 AND `location_id`=$location_id", true);
                                                //console($service_select);
                                                foreach ($service_select['data'] as $other_service) {
                                                ?>

                                 <option value="<?= $other_service['itemId'] ?>">[<?= $other_service['itemCode'] ?>] <?= $other_service['itemName'] ?></option>

                                 <?php

                                                }


                                    ?>

                                                                            </select>
                                                                        </div>
                                                                    </div>


                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Vendor Name</label>
                                                                        <input type="text" class="form-control" placeholder="vendor name" name="OthersCost[${addressRandNo}][service_vendor]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="description" name="OthersCost[${addressRandNo}][service_desc]">
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-3 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Amount</label>
                                                                        <input type="number" class="form-control other_amount" placeholder="amount" name="OthersCost[${addressRandNo}][service_amount]">
                                                                    </div>
                                                                </div>



                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="add-btn-minus">
                                                                        <a style="cursor: pointer" class="btn btn-danger">
                                                                            <i class="fa fa-minus"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            </div>`);
    }

    // function addDeliveryQty(id) {
    //     let addressRandNo = Math.ceil(Math.random() * 100000);
    //     $(`.modal-add-row-delivery_${id}`).append(`
    //                                       <div class="row">
    //                                     <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
    //                                     <div class="form-input">
    //                                         <label>Delivery date</label>
    //                                         <input type="date" name="listItem[${addressRandNo}][deliverySchedule][${addressRandNo}][multiDeliveryDate]" class="form-control" id="delivery-date" placeholder="delivery date" value="">
    //                                     </div>
    //                                 </div>
    //                                 <div class="col-lg-5 col-md-5 col-sm-5 col-12">
    //                                     <div class="form-input">
    //                                         <label>Quantity</label>
    //                                         <input type="text" name="listItem[${addressRandNo}][deliverySchedule][${addressRandNo}][quantity]" class="form-control multiQuantity" id="multiQuantity_${addressRandNo}" placeholder="quantity" value="">
    //                                     </div>
    //                                 </div>
    //                                 <div class="col-lg-2 col-md-2 col-sm-2 col-12">
    //                                 <div class="add-btn-minus">
    //                                         <a style="cursor: pointer" class="btn btn-danger" onclick="rm(538)">
    //                                           <i class="fa fa-minus"></i>
    //                                         </a>
    //                                         </div>
    //                                 </div>
    //                             </div>`);
    // }

    function addDeliveryQty(randCode) {
        let addressRandNo = Math.ceil(Math.random() * 100000);
        $(`.modal-add-row-delivery_${randCode}`).append(`
                                          <div class="row">
                                        <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
                                        <div class="form-input">
                                            <label>Delivery date</label>
                                            <input type="date" name="listItem[${randCode}][deliverySchedule][${addressRandNo}][multiDeliveryDate]" class="form-control" id="delivery-date" placeholder="delivery date" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                        <div class="form-input">
                                            <label>Quantity</label>
                                            <input type="text" data-attr="${randCode}" name="listItem[${randCode}][deliverySchedule][${addressRandNo}][quantity]" class="form-control multiQuantity multiQty_${randCode}" id="multiQuantity_${addressRandNo}" placeholder="quantity" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                    <div class="add-btn-minus">
                                            <a style="cursor: pointer" class="btn btn-danger qty_minus" data-attr="${randCode}">
                                              <i class="fa fa-minus"></i>
                                            </a>
                                            </div>
                                    </div>
                                </div>`);
    }

    function addDeliveryQtyUpdate(randCode) {
        let addressRandNo = Math.ceil(Math.random() * 100000);
        $(`.modal-add-row-delivery_${randCode}`).append(`
                                          <div class="row">
                                        <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
                                        <div class="form-input">
                                            <label>Delivery date</label>
                                            <input type="date" name="listItem[${randCode}][deliverySchedule][${addressRandNo}][multiDeliveryDate]" class="form-control" id="delivery-date" placeholder="delivery date" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                        <div class="form-input">
                                            <label>Quantity</label>
                                            <input type="text" data-attr="${randCode}" name="listItem[${randCode}][deliverySchedule][${addressRandNo}][quantity]" class="form-control updatemultiQuantity updatemultiQty_${randCode}" id="multiQuantity_${addressRandNo}" placeholder="quantity" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                    <div class="add-btn-minus">
                                            <a style="cursor: pointer" class="btn btn-danger update_qty_minus" data-attr="${randCode}">
                                              <i class="fa fa-minus"></i>
                                            </a>
                                            </div>
                                    </div>
                                </div>`);
    }




    // function loadItems() {
    //     $.ajax({
    //         type: "GET",
    //         url: `ajaxs/po/ajax-items.php`,
    //         beforeSend: function() {
    //             $("#itemsDropDown").html(`<option value="">Loding...</option>`);
    //         },
    //         success: function(response) {
    //             $("#itemsDropDown").html(response);
    //         }
    //     });
    // }
    // loadItems();
    $("#usetypesDropdown").on("change", function() {
        let type = $(this).val();

        if (type == "servicep") {
            $("#incoTerms").hide();
            $("#parent_div").show();
        } else {

            $("#incoTerms").show();
            $("#parent_div").hide();
        }

        //  console.log(type);
        if (type != "") {
            $.ajax({
                type: "GET",
                url: `ajaxs/po/ajax-items.php`,
                data: {

                    type
                },
                beforeSend: function() {
                    $("#itemsDropDown").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    console.log(response);
                    $("#itemsDropDown").html(response);
                }
            });
        } else {
            $("#itemsDropDown").html('');
        }
    });

    // vendors ********************************
    function loadVendors() {
        $.ajax({
            type: "GET",
            url: `ajaxs/po/ajax-vendors.php`,
            beforeSend: function() {
                $("#vendorDropdown").html(`<option value="">Loding...</option>`);
            },
            success: function(response) {
                $("#vendorDropdown").html(response);
            }
        });
    }
    loadVendors();
</script>
<script>
    $(document).ready(function() {

        $(".add_data").click(function() {
            var data = this.value;
            $("#creatData").val(data);
            //confirm('Are you sure to Submit?')
            $("#submitPoForm").submit();
        });
    });
    $(document).ready(function() {
        $('#itemsDropDown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });

        $('#parent')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });


        $('#edititemsDropDown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });
        $('#vendorDropdown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });
        $('.serviceDropDown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });


        $('#otherServiceDropDown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });


        // get vendor details by id
        $("#vendorDropdown").on("change", function() {
            let vendorId = $(this).val();
            if (vendorId != "") {
                $.ajax({
                    type: "GET",
                    url: `ajaxs/po/ajax-vendors-list.php`,
                    data: {
                        act: "vendorlist",
                        vendorId
                    },
                    beforeSend: function() {
                        $("#vendorInfo").html(`<option value="">Loding...</option>`);
                    },
                    success: function(response) {
                        // console.log(response);
                        $("#vendorInfo").html(response);
                        calculateAllItemsGrandAmount();
                    }
                });

                $.ajax({
                    type: "GET",
                    url: `ajaxs/po/ajax-vendors-prev.php`,
                    data: {
                        act: "vendorprev",
                        vendorId
                    },
                    beforeSend: function() {
                        //  $("#vendorInfo").html(`<option value="">Loding...</option>`);
                    },
                    success: function(response) {
                        //  console.log(response);
                        // alert(response);
                        var obj = JSON.parse(response);
                        //   console.log(obj);
                        // $("#vendorInfo").html(response);
                        // calculateAllItemsGrandAmount();
                        if (obj['country'] == 'India') {
                            // alert(1);
                            $('#potypes').val('domestic');
                            $('.radio-types-ex-for').show();
                            $('.radio-types-fob-cif').hide();
                        } else {
                            //   alert(2);
                            $('#potypes').val('international');
                            $('.radio-types-fob-cif').show();
                            $('.radio-types-ex-for').hide();
                        }

                    }
                });


            } else {
                $("#vendorInfo").html('');
            }

        });

        $(document).ready(function() {
            $('input[type="radio"]').click(function() {
                var inputValue = $(this).attr("value");
                var targetBox = $("." + inputValue);
                $(".box").not(targetBox).hide();
                $(targetBox).show();
            });
        });
        // **************************************

        // get item details by id
        $("#itemsDropDown").on("change", function() {
            let itemId = $(this).val();
            let deliveryDate = $("[name='deliveryDate']").val();
            let ptype = $("#potypes").val();



            $.ajax({
                type: "GET",
                url: `ajaxs/po/ajax-items-list.php`,
                data: {
                    act: "listItem",
                    itemId,
                    deliveryDate,
                    ptype
                },
                beforeSend: function() {
                    //  $("#itemsTable").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    // console.log(response);

                    $("#itemsTable").append(response);
                    calculateAllItemsGrandAmount();
                    //    currency_conversion();
                },


            });






        });

        $(document).on('change', '.serviceDropDown', function() {
            // alert(1);
            let itemId = $(this).val();

            // alert(itemId);
            var this_id = $(this);

            // $(this).parent().parent().parent().find('.amount').val(999);


            $.ajax({
                type: "GET",
                url: `ajaxs/po/ajax-service-details.php`,
                data: {
                    act: "service",
                    itemId,
                    // deliveryDate
                },
                beforeSend: function() {
                    //  $("#itemsTable").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    //   alert(response);

                    var obj = JSON.parse(response);

                    var price = obj['price'];
                    // alert(price);

                    this_id.parent().parent().parent().find('.amount').val(obj['price']);
                    this_id.parent().parent().parent().find('.gst').val(obj['percentage']);
                    this_id.parent().parent().parent().find('.total').val(obj['total']);

                    //  $("#amount").val(obj['price']);
                    //  $("#gst").val(obj['percentage']);
                    //  $("#total").val(obj['total']);






                }
            });
        });



        $(document).on('change', '.otherServiceDropDown', function() {
            // alert(1);
            let itemId = $(this).val();
            //    alert(itemId);

            var this_id = $(this);




            $.ajax({
                type: "GET",
                url: `ajaxs/po/ajax-service-details.php`,
                data: {
                    act: "service",
                    itemId,
                    // deliveryDate
                },
                beforeSend: function() {
                    //  $("#itemsTable").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    //   alert(response);

                    var obj = JSON.parse(response);

                    this_id.parent().parent().parent().find('.other_amount').val(obj['price']);
                    this_id.parent().parent().parent().find('.other_gst').val(obj['percentage']);
                    this_id.parent().parent().parent().find('.other_total').val(obj['total']);






                }
            });
        });




        $("#edititemsDropDown").on("change", function() {
            let itemId = $(this).val();
            let deliveryDate = $("[name='deliveryDate']").val();

            $.ajax({
                type: "GET",
                url: `ajaxs/po/ajax-edititems-list.php`,
                data: {
                    act: "listItem",
                    itemId,
                    deliveryDate
                },
                beforeSend: function() {
                    //  $("#itemsTable").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    //console.log(response);

                    $("#itemsTable").append(response);
                    calculateUpdateAllItemsGrandAmount()
                    //   currency_conversion();
                }
            });
        });

        $(document).on("click", ".delItemBtn", function() {
            // let id = ($(this).attr("id")).split("_")[1];
            // $(`#delItemRowBtn_${id}`).remove();
            $(this).parent().parent().remove();
            calculateAllItemsGrandAmount();

            calculateUpdateAllItemsGrandAmount();
        });

        $(document).on('submit', '#addNewItemForm', function(event) {
            event.preventDefault();
            let formData = $("#addNewItemsForm").serialize();
            $.ajax({
                type: "POST",
                url: `ajaxs/po/ajax-items.php`,
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
                url: `ajaxs/po/ajax-items-list.php`,
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
</script>


<script>
    var potypes = jQuery('#potypes');
    var select = this.value;
    potypes.change(function() {
        if ($(this).val() == 'international') {
            $('.radio-types-fob-cif').show();
        } else $('.radio-types-fob-cif').hide();
    });
</script>
<script>
    var potypes = jQuery('#potypes');
    var select = this.value;
    potypes.change(function() {
        if ($(this).val() == 'domestic') {
            $('.radio-types-ex-for').show();
        } else $('.radio-types-ex-for').hide();
    });

    var potypesprpo = jQuery('#potypesprpo');
    var selectprPo = this.value;
    potypesprpo.change(function() {
        if ($(this).val() == 'domestic') {
            //   alert(1);
            $('.radio-types-ex-for').show();
            $('.radio-types-fob-cif').hide();
        } else {
            //alert(0);
            $('.radio-types-ex-for').hide();
            $('.radio-types-fob-cif').show();
        }

    });
</script>

<script>
    var usetypesDropdown = jQuery('#usetypesDropdown');
    var select = this.value;
    usetypesDropdown.change(function() {
        if ($(this).val() == 'consumable') {
            $('.cost-center').show();
        } else $('.cost-center').hide();
    });

    function convertNumberToWords(amount) {
        var words = new Array();
        words[0] = '';
        words[1] = 'One';
        words[2] = 'Two';
        words[3] = 'Three';
        words[4] = 'Four';
        words[5] = 'Five';
        words[6] = 'Six';
        words[7] = 'Seven';
        words[8] = 'Eight';
        words[9] = 'Nine';
        words[10] = 'Ten';
        words[11] = 'Eleven';
        words[12] = 'Twelve';
        words[13] = 'Thirteen';
        words[14] = 'Fourteen';
        words[15] = 'Fifteen';
        words[16] = 'Sixteen';
        words[17] = 'Seventeen';
        words[18] = 'Eighteen';
        words[19] = 'Nineteen';
        words[20] = 'Twenty';
        words[30] = 'Thirty';
        words[40] = 'Forty';
        words[50] = 'Fifty';
        words[60] = 'Sixty';
        words[70] = 'Seventy';
        words[80] = 'Eighty';
        words[90] = 'Ninety';

        var result = '';
        var rupees = parseInt(amount);
        var paisa = parseInt(amount * 100) % 100;

        if (rupees === 0) {
            result = ' Zero ';
        } else {
            result = convertToWords(rupees);
        }

        if (paisa > 0) {
            result += ' and ' + convertToWords(paisa);
        }

        return result;

        function convertToWords(number) {
            var num = parseInt(number);
            if (num === 0) {
                return '';
            }
            if (num < 20) {
                return words[num];
            }
            if (num < 100) {
                return words[num - num % 10] + ' ' + words[num % 10];
            }
            if (num < 1000) {
                return words[Math.floor(num / 100)] + ' Hundred ' + convertToWords(num % 100);
            }
            if (num < 100000) {
                return convertToWords(Math.floor(num / 1000)) + ' Thousand ' + convertToWords(num % 1000);
            }
            if (num < 10000000) {
                return convertToWords(Math.floor(num / 100000)) + ' Lakh ' + convertToWords(num % 100000);
            }
            return convertToWords(Math.floor(num / 10000000)) + ' Crore ' + convertToWords(num % 10000000);
        }
    }

    /********************************************** */
    function calculateAllItemsGrandAmount() {
        //  alert('ok');
        let grandTotal = 0;
        let subTotal = 0;
        let totalGst = 0;
        let num = '';
        let numSub = 0;
        let gstNum = 0;

        $(".itemTotalPrice").each(function() {
            let itemTotalPrice = parseFloat($(this).val());
            grandTotal += itemTotalPrice > 0 ? itemTotalPrice : 0;
            num = convertNumberToWords(grandTotal);
            console.log(num);


            var attr = $(this).data('attr');
            // alert(attr);
            var currency_conversion_rate = $("#currency_conversion_rate").val();
            // alert(currency_conversion_rate);
            var local_itemTotalPrice = itemTotalPrice / currency_conversion_rate;
            //alert(local_itemTotalPrice);
            $(`#local_total_price_${attr}`).html(parseFloat(local_itemTotalPrice).toFixed(2));

        });

        $(".itemUnitPrice").each(function() {
            var unit_price = $(this).val();
            var attr = $(this).data('attr');
            // alert(attr);
            var currency_conversion_rate = $("#currency_conversion_rate").val();
            // alert(currency_conversion_rate);
            var local_unit_price = unit_price / currency_conversion_rate;
            // alert(local_unit_price);
            $(`#local_unit_price_${attr}`).html(parseFloat(local_unit_price).toFixed(2));

        });
        // $(".itemBasePrice").each(function(){


        // });

        // $(".gst").each(function() {
        //     var gst = $(this).val();
        //     var attr = $(this).data('attr');
        //     // alert(attr);
        //     var currency_conversion_rate = $("#currency_conversion_rate").val();
        //     // alert(currency_conversion_rate);
        //     var local_gst = gst / currency_conversion_rate;
        //     //alert(local_gst);
        //     $(`#local_gst_${attr}`).html(local_gst);

        // });



        $(".gstAmount").each(function() {
            var gstAmount = $(this).val();
            var attr = $(this).data('attr');
            // alert(attr);
            var currency_conversion_rate = $("#currency_conversion_rate").val();
            // alert(currency_conversion_rate);
            var local_gstAmount = gstAmount / currency_conversion_rate;
            //alert(local_gstAmount);
            $(`#local_gst_amount_${attr}`).html(parseFloat(local_gstAmount).toFixed(2));

        });




        $(".itemBasePrice").each(function() {

            var base = $(this).val();
            //alert(base);
            var attr = $(this).data('attr');
            // alert(attr);
            var currency_conversion_rate = $("#currency_conversion_rate").val();
            //alert(currency_conversion_rate);
            var local_base = base / currency_conversion_rate;
            //  alert(local_base);
            $(`#local_base_price_${attr}`).html(parseFloat(local_base).toFixed(2));


            let itemBasePrice = parseFloat($(this).val());
            subTotal += itemBasePrice > 0 ? itemBasePrice : 0;
            numSub = convertNumberToWords(subTotal);
            //    console.log(num);
        });

        let ptype = $("#potypes").val();
        if (ptype == "international") {
            grandTotal = subTotal;
            num = numSub;
        }


        $(".gstAmount").each(function() {
            let gst = parseFloat($(this).val());

            if (ptype == "international") {
                gst = 0;
            }
            totalGst += gst > 0 ? gst : 0;
            gstNum = convertNumberToWords(totalGst);
        });

        let loc_state = $('#shipToState').val();
        // alert(loc_state);
        let vendor_state = $('#vendor_state_code').val();
        //  alert(vendor_state);

        if (loc_state == vendor_state) {
            //  alert(1);
            let sgst = totalGst / 2;
            let cgst = totalGst / 2;
            $(".igstTr").hide();
            $("#sgst_span").html(sgst.toFixed(2));
            $("#sgst").val(sgst.toFixed(2));
            $(".sgstTr").show();
            $("#cgst_span").html(cgst.toFixed(2));
            $(".cgstTr").show();
            $("#cgst").val(cgst.toFixed(2));
        } else if (loc_state != vendor_state) {
            let igst = totalGst;
            $(".sgstTr").hide();
            $(".cgstTr").hide();
            $("#igst_span").html(igst.toFixed(2));
            $("#igst").val(igst.toFixed(2));
            let ptype = $("#potypes").val();
            if (ptype != 'international') {
                $(".igstTr").show();
            }

        } else {
            let igst = totalGst;
            $(".sgstTr").hide();
            $(".cgstTr").hide();
            $("#igst_span").html(igst.toFixed(2));
            $("#igst").val(igst.toFixed(2));

            $(".igstTr").hide();


        }

        $("#grandTotalAmount").html(grandTotal.toFixed(2) + "<p class='rupee-word'>" + num + "</p>");
        $("#grandTotalAmountInput").val(grandTotal.toFixed(2));
        $("#subTotalAmount").html(subTotal.toFixed(2));
        $("#subTotalAmountInput").val(subTotal.toFixed(2));
    }
    calculateAllItemsGrandAmount();

    function calculateOneItemRowAmount(rowNum) {
        let qty = parseFloat($(`#itemQty_${rowNum}`).val());
        qty = qty > 0 ? qty : 1;

        let unitPrice = parseFloat($(`#itemUnitPrice_${rowNum}`).val());

        unitPrice = unitPrice > 0 ? unitPrice : 0;

        let basePrice = unitPrice * qty;


        let gst = 0;
        let gstAmount = 0;
        let totalPrice = 0;

        let ptype = $("#potypes").val();
        if (ptype == "international") {
            totalPrice = basePrice;
            gstAmount = 0;
            gst = 0;
        } else {
            gst = parseFloat($(`#gstbackup_${rowNum}`).val());
            gstAmount = (gst / 100) * basePrice;
            totalPrice = basePrice + gstAmount;

        }


        // alert(unitPrice);
        // alert(gst);
        // alert(gstAmount);
        $(`#gst_${rowNum}`).val(gst);
        $(`#gstAmount_${rowNum}`).val(gstAmount.toFixed(2));
        $(`#itemBasePrice_${rowNum}`).val(basePrice.toFixed(2));
        $(`#itemTotalPrice_${rowNum}`).val(totalPrice.toFixed(2));


        calculateAllItemsGrandAmount();
    }

    $(document).on("keyup", ".itemQty", function() {

        let rowNum = ($(this).attr("id")).split("_")[1];
        calculateOneItemRowAmount(rowNum);
    });
    $(document).on("keyup", ".itemUnitPrice", function() {
        let rowNum = ($(this).attr("id")).split("_")[1];
        calculateOneItemRowAmount(rowNum);
    });

    //update cal//


    function calculateUpdateAllItemsGrandAmount() {
        let subTotal = 0;
        let totalGst = 0;
        let grandTotal = 0;
        let num = 0;
        let numSub = 0;
        let gstNum = 0;

        // console.log(num);
        $(".updateitemTotalPrice").each(function() {
            let itemTotalPrice = parseFloat($(this).val());
            grandTotal += itemTotalPrice > 0 ? itemTotalPrice : 0;
            num = convertNumberToWords(grandTotal);
            console.log(itemTotalPrice);
            var attr = $(this).data('attr');
            // alert(attr);
            var currency_conversion_rate = $("#currency_conversion_rate").val();
            // alert(currency_conversion_rate);
            var local_itemTotalPrice = itemTotalPrice / currency_conversion_rate;
            //alert(local_itemTotalPrice);
            $(`#local_total_price_${attr}`).html(parseFloat(local_itemTotalPrice).toFixed(2));
        });

        $(".updateitemBasePrice").each(function() {
            let itemBasePrice = parseFloat($(this).val());


            subTotal += itemBasePrice > 0 ? itemBasePrice : 0;
            numSub = convertNumberToWords(subTotal);
            //    console.log(num);
            var base = $(this).val();
            //alert(base);
            var attr = $(this).data('attr');
            // alert(attr);
            var currency_conversion_rate = $("#currency_conversion_rate").val();
            //alert(currency_conversion_rate);
            var local_base = base / currency_conversion_rate;
            //  alert(local_base);
            $(`#local_base_price_${attr}`).html(parseFloat(local_base).toFixed(2));
        });


        $(".updategstAmount").each(function() {
            let gst = parseFloat($(this).val());


            totalGst += gst > 0 ? gst : 0;
            gstNum = convertNumberToWords(totalGst);

            var gstAmount = $(this).val();
            var attr = $(this).data('attr');
            // alert(attr);
            var currency_conversion_rate = $("#currency_conversion_rate").val();
            // alert(currency_conversion_rate);
            var local_gstAmount = gstAmount / currency_conversion_rate;
            //alert(local_gstAmount);
            $(`#local_gst_amount_${attr}`).html(parseFloat(local_gstAmount).toFixed(2));

            //    console.log(num);


        });
        $(".updateitemUnitPrice").each(function() {
            var unit_price = $(this).val();
            var attr = $(this).data('attr');
            // alert(attr);
            var currency_conversion_rate = $("#currency_conversion_rate").val();
            // alert(currency_conversion_rate);
            var local_unit_price = unit_price / currency_conversion_rate;
            // alert(local_unit_price);
            $(`#local_unit_price_${attr}`).html(parseFloat(local_unit_price).toFixed(2));

        });



        let loc_state = $('#shipToState').val();
        //alert(loc_state);
        let vendor_state = $('#vendor_state_code').val();
        // alert(vendor_state);

        if (loc_state == vendor_state) {
            // alert(1);
            let sgst = totalGst / 2;
            let cgst = totalGst / 2;
            $(".igstTr").hide();
            $("#update_sgst_span").html(sgst.toFixed(2));
            $("#update_sgst").val(sgst.toFixed(2));
            $(".sgstTr").show();
            $("#update_cgst_span").html(cgst.toFixed(2));
            $(".cgstTr").show();
            $("#update_cgst").val(cgst.toFixed(2));
        } else if (loc_state != vendor_state) {
            let igst = totalGst;
            $(".sgstTr").hide();
            $(".cgstTr").hide();
            $("#update_igst_span").html(igst.toFixed(2));
            $("#update_igst").val(igst.toFixed(2));
            let ptype = $("#potypes").val();
            if (ptype != 'international') {
                $(".igstTr").show();
            }

        } else {
            let igst = totalGst;
            $(".sgstTr").hide();
            $(".cgstTr").hide();


            $(".igstTr").hide();


        }

        $("#update_grandTotalAmount").html(grandTotal.toFixed(2) + "(" + num + ")");

        $("#update_grandTotalAmountInput").val(grandTotal.toFixed(2));

        $("#update_subTotalAmount").html(subTotal.toFixed(2));

        $("#update_subTotalAmountInput").val(subTotal.toFixed(2));


    }
    calculateUpdateAllItemsGrandAmount();

    function calculateOneUpdateItemRowAmountPoCreation(rowNum) {
        console.log(rowNum);
        let qty = parseFloat($(`#itemQty_${rowNum}`).val());
        console.log(qty);
        qty = qty > 0 ? qty : 1;
        let unitPrice = parseFloat($(`#itemUnitPrice_${rowNum}`).val());
        unitPrice = unitPrice > 0 ? unitPrice : 0;
        let basePrice = unitPrice * qty;


        let gst = 0;
        let gstAmount = 0;
        let totalPrice = 0;

        let ptype = $("#potypes").val();
        if (ptype == "international") {
            totalPrice = basePrice;
            gst = 0;
            gstAmount = 0;
        } else {
            gst = parseFloat($(`#gstbackup_${rowNum}`).val());
            gstAmount = (gst / 100) * basePrice;
            totalPrice = basePrice + gstAmount;

        }
        // let ptype = $("#potypes").val();
        // alert(unitPrice);
        // alert(gst);
        // alert(gstAmount);
        $(`#gst_${rowNum}`).val(gst);
        $(`#gstAmount_${rowNum}`).val(gstAmount.toFixed(2));
        $(`#itemBasePrice_${rowNum}`).val(basePrice.toFixed(2));
        $(`#itemTotalPrice_${rowNum}`).val(totalPrice.toFixed(2));
        calculateUpdateAllItemsGrandAmount();
    }

    function calculateOneUpdateItemRowAmountPrPoCreation(rowNum) {
        console.log(rowNum);
        let qty = parseFloat($(`#itemQty_${rowNum}`).val());
        console.log(qty);
        qty = qty > 0 ? qty : 1;
        let unitPrice = parseFloat($(`#itemUnitPrice_${rowNum}`).val());
        unitPrice = unitPrice > 0 ? unitPrice : 0;
        let basePrice = unitPrice * qty;


        let gst = 0;
        let gstAmount = 0;
        let totalPrice = 0;

        let ptype = $("#potypesprpo").val();
        if (ptype == "international") {
            totalPrice = basePrice;
            gst = 0;
            gstAmount = 0;
        } else {
            gst = parseFloat($(`#gstbackup_${rowNum}`).val());
            gstAmount = (gst / 100) * basePrice;
            totalPrice = basePrice + gstAmount;

        }
        // let ptype = $("#potypes").val();
        // alert(unitPrice);
        // alert(gst);
        // alert(gstAmount);
        $(`#gst_${rowNum}`).val(gst);
        $(`#gstAmount_${rowNum}`).val(gstAmount.toFixed(2));
        $(`#itemBasePrice_${rowNum}`).val(basePrice.toFixed(2));
        $(`#itemTotalPrice_${rowNum}`).val(totalPrice.toFixed(2));
        calculateUpdateAllItemsGrandAmount();
    }

    function calculateOneUpdateItemRowAmount(rowNum) {
        let qty = parseFloat($(`#updateitemQty_${rowNum}`).val());
        console.log(qty);
        qty = qty > 0 ? qty : 1;
        let unitPrice = parseFloat($(`#updateitemUnitPrice_${rowNum}`).val());
        unitPrice = unitPrice > 0 ? unitPrice : 0;
        let basePrice = unitPrice * qty;


        let gst = parseFloat($(`#updategst_${rowNum}`).val());
        let gstAmount = (gst / 100) * basePrice;
        let totalPrice = basePrice + gstAmount;
        // alert(unitPrice);
        // alert(gst);
        // alert(gstAmount);

        $(`#updategstAmount_${rowNum}`).val(gstAmount.toFixed(2));
        $(`#updateitemBasePrice_${rowNum}`).val(basePrice.toFixed(2));
        $(`#updateitemTotalPrice_${rowNum}`).val(totalPrice.toFixed(2));
        calculateUpdateAllItemsGrandAmount();
    }

    $(document).on("keyup", ".updateitemUnitPrice", function() {
        // alert(1);
        let rowNum = ($(this).attr("id")).split("_")[1];


        calculateOneUpdateItemRowAmount(rowNum);
    });

    $(document).on("keyup", ".updateitemQty", function() {
        //  alert(1);
        let updateitemQty = $(this).val();

        let rowNum = ($(this).attr("id")).split("_")[1];
        let actualTotalQty = $("#actualTotalQty_" + rowNum).val();
        let grnQty = $("#updateitemSrnQty_" + rowNum).val();
        let remQty = $("#updateitemRemQtyHidden_" + rowNum).val();
        // alert(actualTotalQty); 
        //    alert(remQty);
        let reminder = actualTotalQty - updateitemQty;
        //    alert(reminder);
        let newRemQty = remQty - (reminder);
        //    alert(newRemQty);
        $("#updateitemRemQty_" + rowNum).val(newRemQty);
        if (newRemQty < 0) {
            // alert('noooooooooooo');
            $("#issueItemRemQty_" + rowNum).html('Remaining Qty Can not Be Lesser Than Zero');
            document.getElementById("editNewPOFormSubmitBtn").disabled = true;
        } else {
            $("#issueItemRemQty_" + rowNum).html('');
            document.getElementById("editNewPOFormSubmitBtn").disabled = false;
        }
        //    alert(grnQty);
        //     alert(updateitemQty);

        if (Number(grnQty) > Number(updateitemQty)) {


            $("#issueItemQty_" + rowNum).html('Qty Can not Be Lesser Than GRN/SRN Qty');
            // document.getElementById("editNewPOFormSubmitBtn").disabled = true;
        } else {

            $("#issueItemQty_" + rowNum).html('');
            // document.getElementById("editNewPOFormSubmitBtn").disabled = false;
        }


        calculateOneUpdateItemRowAmount(rowNum);
    });






    $(document).on("keyup", ".itemQty", function() {


        let attr = $(this).data("id");

        let qty = $("#itemQty_" + attr).val();
        let limit = $(".remqty_" + attr).val();

        
         //alert(qty);
         //  alert(limit);
         // for (elem of $(`#multiQuantity_`+attr)) {
         //    alert(this.val());
         //     }
        //alert(val);

        if (Number(limit) < Number(qty)) {
            // alert(1);
            $("#qty_error_" + attr).html(`<p id="qty_error" > limit exceeded </p>`);
            document.getElementById("pobtn").disabled = true;
            document.getElementById("podbtn").disabled = true;

        } else {
            // alert(2);
            $("#qty_error_" + attr).html("");
            document.getElementById("pobtn").disabled = false;
            document.getElementById("podbtn").disabled = false;

        }

    });



    // function currency_conversion() {
    //     // console.log("hello");
    //     for (elem of $(".itemUnitPricehidden")) {
    //         let rowNo = ($(elem).attr("id")).split("_")[1];
    //         // console.log(rowNo);
    //         $elem_val = $(elem).val();
    //         if ($elem_val == 0) {
    //             $val = $(`#itemUnitPrice_${rowNo}`).val();
    //             $(elem).val($val);

    //         }

    //         let newVal = $("#curr_rate").val() * $(elem).val();

    //         $(`#itemUnitPrice_${rowNo}`).val(newVal);

    //         calculateOneItemRowAmount(rowNo);
    //     };
    // }

    // $(document).on("keyup", "#curr_rate", function() {
    //     currency_conversion();
    // });

    // $(document).on("keydown", "#curr_rate", function() {
    //     currency_conversion();
    // });
    // currency_conversion();


    $(document).on('keyup', '.other_amount', function() {
        // alert(1);

        var gst = $(this).parent().parent().parent().find('.other_gst').val();
        //alert(gst);
        var amount = $(this).val();

        var gst_amount = (amount * gst) / 100;

        var total_amount = Number(amount) + Number(gst_amount);

        $(this).parent().parent().parent().find('.other_total').val(total_amount);

    });



    $(document).on('keyup', '.amount', function() {
        // alert(1);

        var gst = $(this).parent().parent().parent().find('.gst').val();
        //alert(gst);
        var amount = $(this).val();

        var gst_amount = (amount * gst) / 100;

        var total_amount = Number(amount) + Number(gst_amount);

        $(this).parent().parent().parent().find('.total').val(total_amount);

    });

    // $(document).ready(function() {
    // // Intercept the form submission event
    // $('#addNewAddressBtn').click(function() {
    //     alert(1);
    // });
    // });
    $(document).on("click", "#addNewAddressBtn", function() {
        //  alert(1)
        var buildingName = $("#buildingName").val();
        var flatNumber = $("#flatNumber").val();
        var streetName = $("#streetName").val();
        var newLocation = $("#newLocation").val();
        var newCity = $("#newCity").val();
        var newPinCode = $("#newPinCode").val();
        var newDistrict = $("#newDistrict").val();
        var newState = $("#newState").val();
        var loc_name = $("#loc_name").val();
        var lat = $("#lat").val();
        var lng = $("#lng").val();



        // alert(data);
        // console.log(data);

        $.ajax({
            type: 'POST',
            url: 'ajaxs/po/ajax-new-address.php',
            data: {
                buildingName: buildingName,
                flatNumber: flatNumber,
                streetName: streetName,
                flatNumber: flatNumber,
                newLocation: newLocation,
                newCity: newCity,
                newPinCode: newPinCode,
                newDistrict: newDistrict,
                newState: newState,
                lat: lat,
                lng: lng,
                loc_name: loc_name
            },
            before: function() {

            },
            success: function(response) {
                $("#buildingName").val('');
                $("#flatNumber").val('');
                $("#streetName").val('');
                $("#newLocation").val('');
                $("#newCity").val('');
                $("#newPinCode").val('');
                $("#newDistrict").val('');
                $("#newState").val('');
                $("#loc_name").val('');
                $("#lat").val('');
                $("#lng").val('');
                //alert(response);
                console.log(response);
                var obj = JSON.parse(response);
                var addressBody = obj['data'];
                var radioBtnVal = obj['lastInsertedId'];

                $("#shipToAddressDiv").html(addressBody);
                $("#shipToInput").val(radioBtnVal);
                $('#address-change').toggle();
            },
            error: function(error) {
                // Handle any errors
                console.error('Error:', error);
            }


        });
    });
</script>
<script>
    const customerCurrencySelect = document.getElementById("customer_currency");
    const vendorCurrencyText = document.getElementById("vendor-currency");

    customerCurrencySelect.addEventListener('change', function() {


        // Get the selected currency name
        const selectedCurrency = customerCurrencySelect.options[customerCurrencySelect.selectedIndex].getAttribute('data-attr');
        //  alert(selectedCurrency);
        $('.rupee-symbol').html(selectedCurrency);

        // Update the INR text with the selected currency
        vendorCurrencyText.textContent = selectedCurrency;



    });
</script>


<script src="<?= BASE_URL; ?>public/validations/poValidation.js"></script>