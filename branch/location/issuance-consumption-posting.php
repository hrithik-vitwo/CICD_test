<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-po-controller.php");
require_once("../../app/v1/functions/branch/func-consumption.php");


// echo "varrrr"; 
// console($_GET['selectItemPr']);
$variant = $_SESSION['visitBranchAdminInfo']['flAdminVariant'];
$check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
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



if(isset($_POST['submitConsumption'])){

    //console($_POST);
    $consumption = consumption($_POST);
    swalToast($consumption["status"], $consumption["message"]);

}
?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
    .purchase-order-modal .modal-dialog {
        max-width: 100%;
    }

    .purchase-order-modal .modal-dialog .modal-content {
        max-width: 800px;
        margin-left: auto;
    }

    .purchase-order-modal .modal-dialog .modal-content .modal-body {
        width: 100%;
    }

    .purchase-order-modal .modal-dialog .modal-content .modal-body .container {
        overflow: auto;
    }

    .purchase-order-modal .modal-header {

        height: 233px;

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

    console($_POST);
    // echo implode(',', array_keys($_POST['items']));

?>

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
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Purchase Order List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Purchase Order</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>

                <form action="" method="POST" id="submitPoForm" name="submitPoForm" onsubmit="return validationfunction()">

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
                                                                <label for="">Vendor Name</label>
                                                                &nbsp; &nbsp;
                                                                <select name="vendorId" id="" class="form-control selct-vendor-dropdown">
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
                                                                                                        <label for="">Distric</label>
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
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                                            <div class="row info-form-view">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">Delivery Date</label>
                                                    <input type="date" name="deliveryDate" class="form-control" value="<?= $_POST["date"] ?>" />
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">PO Creation Date</label>
                                                    <input type="date" name="podatecreation" id="podatecreation" class="form-control" min="<?= $min ?>" max="<?= $max ?>">
                                                    <p id="podatelabel"></p>
                                                </div>
                                            </div>
                                            <div class="row info-form-view">
                                                <div class="col-lg-3 col-md-3 col-sm-12 form-inline">
                                                    <label for="date">Use Types</label>
                                                    <select onclick="craateUserJsObject.ShowUseTypes();" name="usetypesDropdown" id="usetypesDropdown" class="form-control typesDropdown">
                                                        <option value="">Select</option>
                                                        <option value="material">Material</option>
                                                        <option value="servicep">Service Purchase</option>
                                                        <option value="asset">Asset</option>
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
                                                <div class="col-lg-3 col-md-3 col-sm-12 radio-condition">
                                                    <div class="radio-types radio-types-fob-cif" style="display: none;">
                                                        <label for="" class="inco-terms">Inco Terms</label>
                                                        <div class="form-input-radio form-input-fob">

                                                            <input type="radio" value="fob" name="domestic">
                                                            <div class="tooltip-label">
                                                                <label for="">FOB</label>
                                                                <div class="help-tip fob-tooltip">
                                                                    <p>Free On Board or Freight on Board</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-input-radio form-input-cif">
                                                            <input type="radio" value="cif" name="domestic">
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
                                                                    <p>An international trade term that describes when a
                                                                        seller makes a product available at a designated
                                                                        location</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-input-radio form-input-for">
                                                            <input type="radio" value="for" name="domestic">
                                                            <div class="tooltip-label">
                                                                <label for="">FOR</label>
                                                                <div class="help-tip for-tooltip">
                                                                    <p>This is the inline help tip! It can contain all kinds
                                                                        of HTML. Style it as you please.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row info-form-view">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="tooltip-label">
                                                        <label for="">Reference Number</label>
                                                        <div class="help-tip">
                                                            <p>Customer PO</p>
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



                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="static-currency">
                                                        <input type="text" class="form-control" value="1" readonly="">
                                                        <input type="text" class="form-control text-right" value="INR" readonly="">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="dynamic-currency">
                                                        <input type="number" class="form-control" id="curr_rate" name="curr_rate" value="1">
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
                                                <div class="display-flex" style="justify-content: flex-end;">
                                                    <p class="label-bold text-italic" style="white-space: pre-line;"><span class="mr-2">*</span> Vendor Currency</p>
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
                                                // console($value);
                                                if ($value['item_qty'] > 0) {
                                                    $item_query = "SELECT * FROM erp_inventory_items WHERE itemId=$key AND  status='active' AND company_id=$company_id";
                                                    $itemdata = queryGet($item_query);
                                                    //  console($itemdata);
                                                    $itemId = $itemdata['data']['itemId'];
                                                    // $randCode = $getItemObj['data']['itemId'] . rand(00, 99);
                                                    $randCode = $key . rand(00, 99);
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
                                                                <input type="number" name="listItem[<?= $randCode ?>][qty]" value="<?= $value['item_qty'] ?>" class="form-control full-width itemQty" id="itemQty_<?= $randCode ?>" readonly>
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
                                                            <input type="text" name="listItem[<?= $randCode ?>][totalPrice]" id="" value="<?= $value['item_qty'] * $value['price'] ?>" class="form-control full-width-center itemTotalPrice" readonly>
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
                                                                                            <input type="number" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][quantity]" class="form-control multiQuantity" id="multiQuantity_<?= $randCode ?>" placeholder="quantity" value="">
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
                                        <tbody class="total-calculate">
                                            <tr>
                                                <td colspan="4" class="text-right" style="border: none;"> </td>
                                                <td colspan="0" class="text-right" style="border: none;">Total Amount</td>
                                                <input type="hidden" name="totalAmt" id="grandTotalAmountInput" value="<?= $total_value ?>">
                                                <td colspan="2" style="border: none; background: none; " id="grandTotalAmount"><?= $total_value ?></th>
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
                                                        <label class="tab-label" for="chck1">Freight Cost</label>
                                                        <div class="tab-content">
                                                            <div class="row othe-cost-infor modal-add-row_537">
                                                                <div class="row othe-cost-infor">
                                                                    <div class="col-lg-2 col-md-12 col-sm-12">
                                                                        <div class="form-input">
                                                                            <label for="">Service Description</label>
                                                                            <input type="text" class="form-control" placeholder="service description" name="FreightCost[l1][service]">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg-2 col-md-12 col-sm-12">
                                                                        <div class="form-input">
                                                                            <label for="">Vendor Select</label>
                                                                            <select class="form-control" id="vendorDropdown" name="FreightCost[l1][txt]">
                                                                                <option value="">Select Vendor</option>

                                                                                <?php echo $vendrSelect;     ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-12 col-sm-12">
                                                                        <div class="form-input">
                                                                            <label for="">Amount</label>
                                                                            <input type="number" class="form-control" placeholder="amount" name="FreightCost[l1][amount]">
                                                                        </div>
                                                                    </div>
                                                                    <!-- <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="service description" name="FreightCost[l1][service]">
                                                                    </div>
                                                                </div> -->
                                                                    <div class="col-lg-2 col-md-12 col-sm-12">
                                                                        <div class="form-input">
                                                                            <label for="">GST %</label>
                                                                            <input type="text" class="form-control" placeholder="gst" name="FreightCost[l1][gst]">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-12 col-sm-12">
                                                                        <div class="form-input">
                                                                            <label for="">Total Amount</label>
                                                                            <input type="text" class="form-control" placeholder="total amount" name="FreightCost[l1][total]">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg col-md-6 col-sm-6">
                                                                        <div class="form-check-rcm">
                                                                            <input type="checkbox" name="FreightCost[l1][rcm]" id="">
                                                                            <label for="">RCM</label>
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

                                                    <div class="tab">
                                                        <input type="checkbox" id="chck2" style="display: none;">
                                                        <label class="tab-label" for="chck2">Others Cost</label>
                                                        <div class="tab-content">
                                                            <div class="row othe-cost-infor modal-add-row_538">
                                                                <div class="row othe-cost-infor">
                                                                    <div class="col-lg-2 col-md-12 col-sm-12">
                                                                        <div class="form-input">
                                                                            <label for="">Service Description</label>
                                                                            <input type="text" class="form-control" placeholder="description" name="OthersCost[13][service]">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-12 col-sm-12">
                                                                        <div class="form-input">
                                                                            <label for="">Vendor Name</label>
                                                                            <input type="text" class="form-control" placeholder="vendor name" name="OthersCost[13][name]">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-12 col-sm-12">
                                                                        <div class="form-input">
                                                                            <label for="">Amount</label>
                                                                            <input type="number" class="form-control" placeholder="amount" name="OthersCost[13][amount]">
                                                                        </div>
                                                                    </div>
                                                                    <!-- <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="description" name="OthersCost[13][service]">
                                                                    </div>
                                                                </div> -->
                                                                    <div class="col-lg-2 col-md-12 col-sm-12">
                                                                        <div class="form-input">
                                                                            <label for="">GST %</label>
                                                                            <input type="text" class="form-control" placeholder="gst" name="OthersCost[13][gst]">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-md-12 col-sm-12">
                                                                        <div class="form-input">
                                                                            <label for="">Total Amount</label>
                                                                            <input type="text" class="form-control" placeholder="total amount" name="OthersCost[13][total]">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg col-md-6 col-sm-6">
                                                                        <div class="form-check-rcm">
                                                                            <input type="checkbox" name="OthersCost[13][rcm]" id="" value="1">
                                                                            <label for="">RCM</label>
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
} elseif (isset($_GET['create'])) { ?>
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
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Purchase Order List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Purchase Order</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>

                <form action="" method="POST" id="submitConsumption" name="submitConsumption" onsubmit="return validationfunction()">

                    <input type="hidden" name="submitConsumption" id="submitConsumption" value="">
                    <div class="row po-form-creation">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="card so-creation-card po-creation-card">
                                        <div class="card-header">
                                            <div class="row customer-info-head">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="head">
                                                        <i class="fa fa-user"></i>
                                                        <h4> Information <span class="text-danger">*</span></h4>
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
                                                            <div class="row info-form-view" style="row-gap: 5px;">
                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">Cost Center</label>
                                                    <select name="cost_center" id="" class="form-control selct">
                                                                   <?php
                                                                    $funcList = $BranchPoObj->fetchFunctionality()['data'];
                                                                    foreach ($funcList as $func) {
                                                                    ?>
                                                                        <option value="<?= $func['CostCenter_id'] ?>">
                                                                            <?= $func['CostCenter_code'] ?></option>
                                                                    <?php } ?>

                                                                </select>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">Posting Date</label>
                                                    <input type="date" name="post_date" id="post_date" class="form-control" min="<?= $min ?>" max="<?= $max ?>">
                                                    <p id="podatelabel"></p>
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
                                                        <?php 

                                                        $item = queryGet("SELECT * FROM `erp_inventory_stocks_log` as logs LEFT JOIN `erp_inventory_items` as item ON logs.itemId = item.itemId  WHERE logs.`locationId`=8 AND `goodsType`=1 ORDER BY `stockLogId` DESC",true);
                                                        foreach($item['data'] as $item){

                                                            ?>
                                                            <option value="<?= $item['stockLogId'] ?>"><?= $item['refNumber']."(".$item['itemName'] .")" ?></option>

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
                                            <th>Batch Number</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Qty</th>
                                            <th>Unit Price</th>
                                            <!-- Info -->
                                            <th>Total Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTable">

                                    </tbody>
                                   

                                </table>
                            </div>
                        </div>
                    </div>

                   
            </div>
            <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-primary save-close-btn btn-xs float-right add_data" id="pobtn" value="add_post">Save & Close</button>
                               
                           
           
            </form>
            <!-- modal pr ---->


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
                                                        echo "closed";
                                                    } else if ($onePrList['pr_status'] == 9) {
                                                        echo "open";
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
    //  console($sqlData);

?>
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
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Purchase Order List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Purchase Order</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>

                <form action="" method="POST" id="submitPoForm" name="submitPoForm" onsubmit="return validationfunction()">

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
                                                                                                        <label for="">Distric</label>
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
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                            <!-- <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
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
                                        <div class="card-body quickadd" style="gap: 10px;">
                                            <div class="row">
                                                <div class=" col-lg-6 col-md-6 col-sm-12">
                                                    <label for="">Quick Add</label>
                                                    <select id="itemsDropDown" class="form-control">
                                                        <option value="">Goods Type</option>
                                                        <option value="hello">hello</option>
                                                        <option value="hello1">hello1</option>
                                                    </select>
                                                </div>
                                                <div class=" col-lg-6 col-md-6 col-sm-12">
                                                    <a class="btn btn-primary advanced-search-btn btn-xs" data-bs-toggle="modal" data-bs-target="#exampleModal"> <i class="fa fa-search mr-2"></i>Advance Search</a>

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
                                                                                                <button class="btn btn-primary btn-xs"><i class="fa fa-search mr-2"></i>Search</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="card filter-add-item-card">
                                                                        <div class="card-header">
                                                                            <button class="btn btn-primary"><i class="fa fa-plus"></i> Add</button>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <table class="filter-add-item">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th><input type="checkbox"></th>
                                                                                        <th>Item Code</th>
                                                                                        <th>Item Code</th>
                                                                                        <th>Item Code</th>
                                                                                        <th>Item Code</th>
                                                                                        <th>Item Code</th>
                                                                                        <th>Item Code</th>
                                                                                        <th>Item Code</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td><input type="checkbox"></td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td><input type="checkbox"></td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td><input type="checkbox"></td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td><input type="checkbox"></td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td><input type="checkbox"></td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                    <tr>
                                                                                        <td><input type="checkbox"></td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                    <tr>
                                                                                        <td><input type="checkbox"></td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                    <tr>
                                                                                        <td><input type="checkbox"></td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                    <tr>
                                                                                        <td><input type="checkbox"></td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                        <td>12</td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
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
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">Delivery Date</label>
                                                    <input type="date" name="deliveryDate" class="form-control" value="<?= $sqlData['expectedDate'] ?>" />
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">PO Creation Date</label>
                                                    <input type="date" name="podatecreation" id="podatecreation" class="form-control" min="<?= $min ?>" max="<?= $max ?>">
                                                    <p id="podatelabel"></p>
                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-12">
                                                    <label for="date">Use Types</label>
                                                    <select onclick="craateUserJsObject.ShowUseTypes();" name="usetypesDropdown" id="usetypesDropdown" class="form-control typesDropdown">
                                                        <option value="">Select</option>
                                                        <option value="material" <?php if ($sqlData['pr_type'] == "material") {
                                                                                        echo "selected";
                                                                                    } ?>>Material</option>
                                                        <option value="servicep" <?php if ($sqlData['pr_type'] == "servicep") {
                                                                                        echo "selected";
                                                                                    } ?>>Service Purchase</option>
                                                        <option value="asset" <?php if ($sqlData['pr_type'] == "asset") {
                                                                                    echo "selected";
                                                                                } ?>>Asset</option>
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
                                                <div class="col-lg-3 col-md-3 col-sm-12 radio-condition">
                                                    <div class="radio-types radio-types-fob-cif" style="display: none;">
                                                        <label for="" class="inco-terms">Inco Terms</label>
                                                        <div class="form-input-radio form-input-fob">

                                                            <input type="radio" value="fob" name="domestic">
                                                            <div class="tooltip-label">
                                                                <label for="">FOB</label>
                                                                <div class="help-tip fob-tooltip">
                                                                    <p>Free On Board or Freight on Board</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-input-radio form-input-cif">
                                                            <input type="radio" value="cif" name="domestic">
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
                                                                    <p>An international trade term that describes when a
                                                                        seller makes a product available at a designated
                                                                        location</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-input-radio form-input-for">
                                                            <input type="radio" value="for" name="domestic">
                                                            <div class="tooltip-label">
                                                                <label for="">FOR</label>
                                                                <div class="help-tip for-tooltip">
                                                                    <p>This is the inline help tip! It can contain all kinds
                                                                        of HTML. Style it as you please.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="tooltip-label">
                                                        <label for="">Reference Number</label>
                                                        <div class="help-tip">
                                                            <p>Customer PO</p>
                                                        </div>
                                                    </div>
                                                    <input type="text" name="refNo" class="form-control" value="<?= $sqlData['prCode'] ?>" />
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


                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="static-currency">
                                                        <input type="text" class="form-control" value="1" readonly="">
                                                        <input type="text" class="form-control text-right" value="INR" readonly="">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="dynamic-currency">
                                                        <input type="number" class="form-control" id="curr_rate" name="curr_rate" value="1">
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
                                                <div class="display-flex" style="justify-content: flex-end;">
                                                    <p class="label-bold text-italic" style="white-space: pre-line;"><span class="mr-2">*</span> Vendor Currency</p>
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
                                            <th>PR Number</th>
                                            <th>Qty</th>
                                            <th>Unit Price</th>
                                            <th>Total Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTable">
                                        <?php
                                        $pr_ite_sql = "SELECT *  FROM `erp_branch_purchase_request_items` WHERE `prId`='" . $id . "' AND `remainingQty` > 0 ";

                                        $pr = queryGet($pr_ite_sql, true);
                                        $pr_data = $pr['data'];
                                        //console($pr);

                                        foreach ($pr_data as $data) {
                                            //   console($data);
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


                                            $item_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `itemId` = $itemId");
                                            // console($item_sql);

                                            $item_name = $item_sql['data']['itemName'];
                                            $item_code = $item_sql['data']['itemCode'];


                                            $randCode = $getItemObj['data']['itemId'] . rand(00, 99);

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
                                                    <?= $item_name ?>
                                                </td>
                                                <td>
                                                    <?= $sqlData['prCode'] ?>
                                                    <input type="hidden" name="listItem[<?= $randCode ?>][pr_id]" value="<?= $data['prId'] ?>">
                                                </td>
                                                <td>
                                                    <div class="flex-display">
                                                        <input type="number" name="listItem[<?= $randCode ?>][qty]" value="<?= $remaining_qty ?>" class="form-control full-width itemQty" min="1" data-id="<?= $randCode ?>" id="itemQty_<?= $randCode ?>">

                                                        <input type="hidden" name="listItem[<?= $randCode ?>][remQty]" value="<?= $remaining_qty ?>" class="form-control full-width remqty_<?= $randCode ?>" min="1" id="remqty">


                                                        <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>
                                                        <input type="hidden" name="listItem[<?= $randCode ?>][uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>">
                                                    </div>
                                                    <p id="qty_error_<?= $randCode ?>"></p>
                                                </td>
                                                <td>
                                                    <input type="number" name="listItem[<?= $randCode ?>][unitPrice]" id="itemUnitPrice_<?= $randCode ?>" value="<?= $lastPrice ?>" class="form-control full-width-center itemUnitPrice">

                                                    <input type="hidden" name="listItem[<?= $randCode ?>][unitPriceHidden]" value="<?= $lastPrice ?>" id="ItemUnitPriceTdInputhidden_<?= $randCode ?>" class="form-control text-xs itemUnitPricehidden">


                                                </td>
                                                <!-- <td class="flex-display">
            <input type="number" name="listItem[<?= $randCode ?>][totalDiscount]" value="0.00" class="form-control full-width-center itemDiscount">%
        </td> -->
                                                <td>
                                                    <input type="text" name="listItem[<?= $randCode ?>][totalPrice]" id="itemTotalPrice_<?= $randCode ?>" value="<?= $lastPrice * $remaining_qty ?>" class="form-control full-width-center itemTotalPrice" readonly>
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
                                                                                    <input type="date" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][multiDeliveryDate]" class="form-control delDate delDate_<?= $randCode ?>" data-attr="<?= $randCode ?>" id="delivery-date" placeholder="delivery date" value="<?= $_GET['deliveryDate'] ?>">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                                                                <div class="form-input">
                                                                                    <label>Quantity</label>
                                                                                    <input type="text" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][quantity]" class="form-control multiQuantity multiQty_<?= $randCode ?>" data-attr="<?= $randCode ?>" id="multiQuantity_<?= $randCode ?>" placeholder="quantity" value="1">
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
                                                                <button type="submit" id="finalBtn_<?= $randCode ?>" class="btn btn-primary save-close-btn btn-xs float-right waves-effect waves-light" data-dismiss="modal" aria-label="Close">Save & Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </td>
                                            </tr>







                                        <?php  } ?>
                                    </tbody>
                                    <tbody class="total-calculate">
                                        <tr>
                                            <td style="border: none;"> </td>
                                            <td style="border: none; padding-left: 15px !important;">Total Amount</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <input type="hidden" name="totalAmt" id="grandTotalAmountInput" value="0.00">
                                            <td style="border: none; background: none; padding-left: 15px !important;" id="grandTotalAmount">0.00</th>
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
                                                    <label class="tab-label" for="chck1">Freight Cost</label>
                                                    <div class="tab-content">
                                                        <div class="row othe-cost-infor modal-add-row_537">
                                                            <div class="row othe-cost-infor">
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="service description" name="FreightCost[l1][service]">
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Vendor Select</label>
                                                                        <select class="form-control" id="vendorDropdown" name="FreightCost[l1][txt]">
                                                                            <option value="">Select Vendor</option>

                                                                            <?php echo $vendrSelect;     ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Amount</label>
                                                                        <input type="number" class="form-control" placeholder="amount" name="FreightCost[l1][amount]">
                                                                    </div>
                                                                </div>
                                                                <!-- <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="service description" name="FreightCost[l1][service]">
                                                                    </div>
                                                                </div> -->
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">GST %</label>
                                                                        <input type="text" class="form-control" placeholder="gst" name="FreightCost[l1][gst]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Total Amount</label>
                                                                        <input type="text" class="form-control" placeholder="total amount" name="FreightCost[l1][total]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="form-check-rcm">
                                                                        <input type="checkbox" name="FreightCost[l1][rcm]" id="">
                                                                        <label for="">RCM</label>
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

                                                <div class="tab">
                                                    <input type="checkbox" id="chck2" style="display: none;">
                                                    <label class="tab-label" for="chck2">Others Cost</label>
                                                    <div class="tab-content">
                                                        <div class="row othe-cost-infor modal-add-row_538">
                                                            <div class="row othe-cost-infor">
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="description" name="OthersCost[13][service]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Vendor Name</label>
                                                                        <input type="text" class="form-control" placeholder="vendor name" name="OthersCost[13][name]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Amount</label>
                                                                        <input type="number" class="form-control" placeholder="amount" name="OthersCost[13][amount]">
                                                                    </div>
                                                                </div>
                                                                <!-- <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="description" name="OthersCost[13][service]">
                                                                    </div>
                                                                </div> -->
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">GST %</label>
                                                                        <input type="text" class="form-control" placeholder="gst" name="OthersCost[13][gst]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Total Amount</label>
                                                                        <input type="text" class="form-control" placeholder="total amount" name="OthersCost[13][total]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="form-check-rcm">
                                                                        <input type="checkbox" name="OthersCost[13][rcm]" id="" value="1">
                                                                        <label for="">RCM</label>
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
} elseif (isset($_GET['edit'])) {

    $id = $_GET['edit'];
    $edit_sql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `po_id`=$id";
    $sqlGet = queryGet($edit_sql);
    $sqlData = $sqlGet['data'];

  //  console($sqlData);
    $vendor_id = $sqlData['vendor_id'];
    $vendorsql = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `vendor_id`= $vendor_id ");
    $vendorData = $vendorsql['data'];
    $ship_to_id = $sqlData['ship_address'];
    //console($ship_to_id);

?>

    <div class="content-wrapper">
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
                                                                            <div class="col-lg-4 col-md-4 col-sm-12 display-flex customer-code"><i class="fa fa-check"></i>&nbsp;<p>Code :&nbsp;</p>
                                                                                <p> <?= $vendorData['vendor_code'] ?></p>
                                                                                <div class="divider"></div>
                                                                            </div>
                                                                            <div class="col-lg-4 col-md-4 col-sm-12 display-flex customer-gstin"><i class="fa fa-check"></i>&nbsp;<p>GSTIN :&nbsp;</p>
                                                                                <p> <?= $vendorData['vendor_gstin'] ?></p>
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
                                                                                                        <label for="">Distric</label>
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
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                                            <div class="row info-form-view">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">Delivery Date</label>
                                                    <input type="date" name="deliveryDate" class="form-control" value="<?= $sqlData['delivery_date'] ?>" />
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">PO Creation Date</label>
                                                    <input type="date" name="podatecreation" id="podatecreation" class="form-control" value="<?= $sqlData['po_date'] ?>" min="<?= $min ?>" max="<?= $max ?>">
                                                    <p id="podatelabel"></p>
                                                </div>
                                            </div>
                                            <div class="row info-form-view">
                                                <div class="col-lg-6 col-md-6 col-sm-12 form-inline">
                                                    <label for="date">Use Types</label>
                                                    <select onclick="craateUserJsObject.ShowUseTypes();" name="usetypesDropdown" id="usetypesDropdown" class="form-control typesDropdown" disabled>
                                                        <option value="material" <?php if ($sqlData['use_type'] == "rawmaterial") {
                                                                                        echo "selected";
                                                                                    }  ?>>Raw Material</option>


                                                        <option value="servicep">Service Purchase</option>


                                                        <option value="asset" <?php if ($sqlData['use_type'] == "asset") {
                                                                                    echo "selected";
                                                                                }  ?>>Asset</option>
                                                    </select>
                                                </div>
                                                <?php
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
                                                    <select name="potypes" id="potypes" onclick="craateUserJsObject.ShowPoTypes();" class="form-control typesDropdown">
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
                                                <div class="col-lg-3 col-md-3 col-sm-12 radio-condition">
                                                    <div class="radio-types radio-types-fob-cif" style="display: none;">
                                                        <label for="" class="inco-terms">Inco Terms</label>
                                                        <div class="form-input-radio form-input-fob">

                                                            <input type="radio" value="fob" name="domestic">
                                                            <div class="tooltip-label">
                                                                <label for="">FOB</label>
                                                                <div class="help-tip fob-tooltip">
                                                                    <p>Free On Board or Freight on Board</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-input-radio form-input-cif">
                                                            <input type="radio" value="cif" name="domestic">
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
                                                                    <p>An international trade term that describes when a
                                                                        seller makes a product available at a designated
                                                                        location</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-input-radio form-input-for">
                                                            <input type="radio" value="for" name="domestic">
                                                            <div class="tooltip-label">
                                                                <label for="">FOR</label>
                                                                <div class="help-tip for-tooltip">
                                                                    <p>This is the inline help tip! It can contain all kinds
                                                                        of HTML. Style it as you please.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row info-form-view">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="tooltip-label">
                                                        <label for="">Reference Number</label>
                                                        <div class="help-tip">
                                                            <p>Customer PO</p>
                                                        </div>
                                                    </div>
                                                    <input type="text" name="refNo" class="form-control" value="<?= $sqlData['ref_no'] ?>" />
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
                                            <th>Unit Price</th>
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
                                            //console($data);
                                            $qty = $data['qty'];
                                            $amount = $data['total_price'];

                                            $itemId = $data['inventory_item_id'];
                                            $getItemObj = $ItemsObj->getItemById($itemId);
                                            // console($data['po_item_id']);
                                            // console($getItemObj);
                                            $itemCode = $getItemObj['data']['itemCode'];



                                            $randCode = $getItemObj['data']['itemId'] . rand(00, 99);

                                        ?>
                                            <!-- <input type ="hidden" id="" name="" value=""> -->
                                            <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>">
                                                <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][update_itemId]" value="<?= $getItemObj['data']['itemId'] ?>">
                                                <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][update_poitemId]" value="<?= $data['po_item_id'] ?>">
                                                <td>
                                                    <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][update_itemCode]" value="<?= $getItemObj['data']['itemCode'] ?>">
                                                    <?= $getItemObj['data']['itemCode'] ?>
                                                </td>
                                                <td>
                                                    <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][update_itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
                                                    <?= $getItemObj['data']['itemName'] ?>
                                                </td>
                                                <td>
                                                    <div class="flex-display">
                                                        <input type="number" name="listItem[<?= $randCode ?>][update_qty]" value="<?= $data['qty'] ?>" class="form-control full-width updateitemQty" id="updateitemQty_<?= $randCode ?>">
                                                        <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>
                                                        <input type="hidden" name="listItem[<?= $randCode ?>][update_uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <!-- <input type="number" name="listItem[<?= $randCode ?>][update_unitPrice]" id="itemUnitPrice_<?= $randCode ?>"  class="form-control full-width-center itemUnitPrice" step="any" value="<?= $data['qty'] ?>"> -->
                                                    <input type="number" name="listItem[<?= $randCode ?>][update_unitPrice]" value="<?= $data['unitPrice'] ?>" class="form-control full-width updateitemUnitPrice" id="updateitemUnitPrice_<?= $randCode ?>">
                                                    <input type="hidden" name="istItem[<?= $randCode ?>][update_unitPriceHidden]" value="" id="ItemUnitPriceTdInputhidden_<?= $randCode ?>" class="form-control text-xs itemUnitPricehidden">

                                                </td>
                                                <!-- <td class="flex-display">
            <input type="number" name="listItem[<?= $randCode ?>][totalDiscount]" value="0.00" class="form-control full-width-center itemDiscount">%
        </td> -->
                                                <td>
                                                    <input type="number" name="listItem[<?= $randCode ?>][update_totalPrice]" id="updateitemTotalPrice_<?= $randCode ?>" value="<?= $data['total_price'] ?>" class="form-control full-width-center updateitemTotalPrice" step="any" readonly>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger delItemBtn" id="delItemBtn_<?= $itemId  ?>">
                                                        <i class="fa fa-minus"></i>
                                                    </button>
                                                </td>

                                            </tr>







                                        <?php  } ?>
                                    </tbody>

                                    <tbody class="total-calculate">
                                        <tr>
                                            <td class="text-right" style="border: none;"> </td>
                                            <td style="border: none;">Total Amount</td>
                                            <td></td>
                                            <td></td>
                                            <input type="hidden" name="totalAmt" id="update_grandTotalAmountInput" value="<?= $sqlData['totalAmount'] ?>">
                                            <td colspan="2" style="border: none; background: none; " id="update_grandTotalAmount">₹<?= $sqlData['totalAmount'] ?></th>
                                        </tr>

                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card so-creation-card po-creation-card  po-creation-card po-others-info">


                                <button type="submit" name="editNewPOFormSubmitBtn" class="btn btn-xs btn-primary items-search-btn float-right">Update</button>
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

    //echo $pr_type;

?>
    <div class="content-wrapper">
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
                                                                                                        <label for="">Distric</label>
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
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                                            <div class="row info-form-view">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">Delivery Date</label>
                                                    <input type="date" name="deliveryDate" class="form-control" value="<?= $sqlData['expectedDate'] ?>" />
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">PO Creation Date</label>
                                                    <input type="date" name="podatecreation" id="podatecreation" class="form-control" min="<?= $min ?>" max="<?= $max ?>">
                                                    <p id="podatelabel"></p>
                                                </div>
                                            </div>
                                            <div class="row info-form-view">
                                                <div class="col-lg-3 col-md-3 col-sm-12 form-inline">
                                                    <label for="date">Use Types</label>
                                                    <select onclick="craateUserJsObject.ShowUseTypes();" name="usetypesDropdown" id="usetypesDropdown" class="form-control typesDropdown">
                                                        <option value="">Select</option>
                                                        <option value="material" <?php if ($pr_type == "material") {
                                                                                        echo "selected";
                                                                                    } ?>>Material</option>
                                                        <option value="servicep" <?php if ($pr_type == "servicep") {
                                                                                        echo "selected";
                                                                                    } ?>>Service Purchase</option>
                                                        <option value="asset" <?php if ($asset == "material") {
                                                                                    echo "selected";
                                                                                } ?>>Asset</option>
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
                                                <div class="col-lg-3 col-md-3 col-sm-12 radio-condition">
                                                    <div class="radio-types radio-types-fob-cif" style="display: none;">
                                                        <label for="" class="inco-terms">Inco Terms</label>
                                                        <div class="form-input-radio form-input-fob">

                                                            <input type="radio" value="fob" name="domestic">
                                                            <div class="tooltip-label">
                                                                <label for="">FOB</label>
                                                                <div class="help-tip fob-tooltip">
                                                                    <p>Free On Board or Freight on Board</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-input-radio form-input-cif">
                                                            <input type="radio" value="cif" name="domestic">
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
                                                                    <p>An international trade term that describes when a
                                                                        seller makes a product available at a designated
                                                                        location</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-input-radio form-input-for">
                                                            <input type="radio" value="for" name="domestic">
                                                            <div class="tooltip-label">
                                                                <label for="">FOR</label>
                                                                <div class="help-tip for-tooltip">
                                                                    <p>This is the inline help tip! It can contain all kinds
                                                                        of HTML. Style it as you please.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row info-form-view">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="tooltip-label">
                                                        <label for="">Reference Number</label>
                                                        <div class="help-tip">
                                                            <p>Customer PO</p>
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

                                                <div class="col-lg-6 col-md-6 col-sm-6 mt-4">
                                                    <div class="static-currency">
                                                        <input type="text" class="form-control" value="1" readonly="">
                                                        <input type="text" class="form-control text-right" value="INR" readonly="">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-6 mt-4">
                                                    <div class="dynamic-currency">
                                                        <input type="number" class="form-control" id="curr_rate" name="curr_rate" value="1">
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
                                                <div class="display-flex" style="justify-content: flex-end;">
                                                    <p class="label-bold text-italic" style="white-space: pre-line;"><span class="mr-2">*</span> Vendor Currency</p>
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

                                                $randCode = $getItemObj['data']['itemId'] . rand(00, 99);

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
                                                            <input type="number" name="listItem[<?= $randCode ?>][qty]" value="<?= $remaining_qty ?>" class="form-control full-width itemQty" data-val="<?= $randCode ?>" min="1" id="itemQty_<?= $randCode ?>">
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
                                                        <input type="text" name="listItem[<?= $randCode ?>][totalPrice]" id="itemTotalPrice_<?= $randCode ?>" value="<?= $lastPrice * $remaining_qty  ?>" class="form-control full-width-center itemTotalPrice" readonly>
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
                                                                                            <input type="text" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][quantity]" class="form-control multiQuantity" id="multiQuantity_<?= $randCode ?>" placeholder="quantity" value="">
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
                                    <tbody class="total-calculate">
                                        <tr>
                                            <td style="border: none;"> </td>
                                            <td style="border: none; padding-left: 15px !important;">Total Amount</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <input type="hidden" name="totalAmt" id="grandTotalAmountInput" value="0.00">
                                            <td style="border: none; background: none; padding-left: 15px !important;" id="grandTotalAmount">0.00</th>
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
                                                    <label class="tab-label" for="chck1">Freight Cost</label>
                                                    <div class="tab-content">
                                                        <div class="row othe-cost-infor modal-add-row_537">
                                                            <div class="row othe-cost-infor">
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="service description" name="FreightCost[l1][service]">
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Vendor Select</label>
                                                                        <select class="form-control" id="vendorDropdown" name="FreightCost[l1][txt]">
                                                                            <option value="">Select Vendor</option>

                                                                            <?php echo $vendrSelect;     ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Amount</label>
                                                                        <input type="number" class="form-control" placeholder="amount" name="FreightCost[l1][amount]">
                                                                    </div>
                                                                </div>
                                                                <!-- <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="service description" name="FreightCost[l1][service]">
                                                                    </div>
                                                                </div> -->
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">GST %</label>
                                                                        <input type="text" class="form-control" placeholder="gst" name="FreightCost[l1][gst]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Total Amount</label>
                                                                        <input type="text" class="form-control" placeholder="total amount" name="FreightCost[l1][total]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="form-check-rcm">
                                                                        <input type="checkbox" name="FreightCost[l1][rcm]" id="">
                                                                        <label for="">RCM</label>
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

                                                <div class="tab">
                                                    <input type="checkbox" id="chck2" style="display: none;">
                                                    <label class="tab-label" for="chck2">Others Cost</label>
                                                    <div class="tab-content">
                                                        <div class="row othe-cost-infor modal-add-row_538">
                                                            <div class="row othe-cost-infor">
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="description" name="OthersCost[13][service]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Vendor Name</label>
                                                                        <input type="text" class="form-control" placeholder="vendor name" name="OthersCost[13][name]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Amount</label>
                                                                        <input type="number" class="form-control" placeholder="amount" name="OthersCost[13][amount]">
                                                                    </div>
                                                                </div>
                                                                <!-- <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="description" name="OthersCost[13][service]">
                                                                    </div>
                                                                </div> -->
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">GST %</label>
                                                                        <input type="text" class="form-control" placeholder="gst" name="OthersCost[13][gst]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Total Amount</label>
                                                                        <input type="text" class="form-control" placeholder="total amount" name="OthersCost[13][total]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="form-check-rcm">
                                                                        <input type="checkbox" name="OthersCost[13][rcm]" id="" value="1">
                                                                        <label for="">RCM</label>
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
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Purchase Order List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Purchase Order</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>

                <form action="" method="POST" id="submitConsumption" name="submitConsumption" onsubmit="return validationfunction()">

                    <input type="hidden" name="submitConsumption" id="submitConsumption" value="">
                    <div class="row po-form-creation">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="card so-creation-card po-creation-card">
                                        <div class="card-header">
                                            <div class="row customer-info-head">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="head">
                                                        <i class="fa fa-user"></i>
                                                        <h4> Information <span class="text-danger">*</span></h4>
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
                                                            <div class="row info-form-view" style="row-gap: 5px;">
                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">Cost Center</label>
                                                    <select name="cost_center" id="" class="form-control selct">
                                                                   <?php
                                                                    $funcList = $BranchPoObj->fetchFunctionality()['data'];
                                                                    foreach ($funcList as $func) {
                                                                    ?>
                                                                        <option value="<?= $func['CostCenter_id'] ?>">
                                                                            <?= $func['CostCenter_code'] ?></option>
                                                                    <?php } ?>

                                                                </select>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">Posting Date</label>
                                                    <input type="date" name="post_date" id="post_date" class="form-control" min="<?= $min ?>" max="<?= $max ?>">
                                                    <p id="podatelabel"></p>
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
                                                        <?php 

                                                        $item = queryGet("SELECT * FROM `erp_inventory_stocks_log` as logs LEFT JOIN `erp_inventory_items` as item ON logs.itemId = item.itemId  WHERE logs.`locationId`=8 AND `goodsType`=1 ORDER BY `stockLogId` DESC",true);
                                                        foreach($item['data'] as $item){

                                                            ?>
                                                            <option value="<?= $item['stockLogId'] ?>"><?= $item['refNumber']."(".$item['itemName'] .")" ?></option>

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
                                            <th>Batch Number</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Qty</th>
                                            <th>Unit Price</th>
                                            <!-- Info -->
                                            <th>Total Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTable">

                                    </tbody>
                                   

                                </table>
                            </div>
                        </div>
                    </div>

                   
            </div>
            <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-primary save-close-btn btn-xs float-right add_data" id="pobtn" value="add_post">Save & Close</button>
                               
                           
           
            </form>
            <!-- modal pr ---->


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
                                                        echo "closed";
                                                    } else if ($onePrList['pr_status'] == 9) {
                                                        echo "open";
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
<?php } ?>

<?php
require_once("../common/footer.php");
?>
<script>
    $(document).on("click", ".add-btn-minus", function() {
        $(this).parent().parent().remove();
    });


    $(document).on("click", "#shipToAddressSaveBtn", function() {
        document.getElementById("addresscheckbox").checked = false;

        console.log("clickinggggggggg");
        let radioBtnVal = $('input[name="shipToAddress"]:checked').val();
        let addressHead = ($(`#shipToAddressHeadText_${radioBtnVal}`).html()).trim();
        let addressBody = ($(`#shipToAddressBodyText_${radioBtnVal}`).html()).trim();
        console.log(radioBtnVal);
        $("#shipToAddressDiv").html(addressBody);
        $("#shipToInput").val(radioBtnVal);
    });

    // $(document).on("click","#addresscheckbox", function(){
    //   console.log("clickinggggggggg");
    //     let radioBtnVal = $('input[name="shipToAddress"]:checked').val();
    //     let addressHead = ($(`#shipToAddressHeadText_${radioBtnVal}`).html()).trim();
    //     let addressBody = ($(`#shipToAddressBodyText_${radioBtnVal}`).html()).trim();
    //     console.log(addressBody);
    //     $("#shipToAddressDiv").html(addressBody);
    // });



    function addMultiQtyf(id) {
        let addressRandNo = Math.ceil(Math.random() * 100000);
        $(`.modal-add-row_${id}`).append(`  <div class="row othe-cost-infor">
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Vendor Select</label>
                                                                        <select class="form-control" name="FreightCost[${addressRandNo}][txt]">
                                                                        <option value="">Select Vendor</option>
                                                                           <?php echo $vendrSelect; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Amount</label>
                                                                        <input type="number" class="form-control" placeholder="amount" name="FreightCost[${addressRandNo}][amount]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="service description" name="FreightCost[${addressRandNo}][service]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">GST</label>
                                                                        <input type="text" class="form-control" placeholder="gst" name="FreightCost[${addressRandNo}][gst]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Total Amount</label>
                                                                        <input type="text" class="form-control" placeholder="total amount" name="FreightCost[${addressRandNo}][total]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="form-check-rcm">
                                                                        <input type="checkbox" name="FreightCost[${addressRandNo}][rcm]" id="">
                                                                        <label for="">RCM</label>
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
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Vendor Name</label>
                                                                        <input type="text" class="form-control" placeholder="vendor name" name="OthersCost[${addressRandNo}][name]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Amount</label>
                                                                        <input type="number" class="form-control" placeholder="amount" name="OthersCost[${addressRandNo}][amount]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="description" name="OthersCost[${addressRandNo}][service]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">GST</label>
                                                                        <input type="text" class="form-control" placeholder="gst" name="OthersCost[${addressRandNo}][gst]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Total Amount</label>
                                                                        <input type="text" class="form-control" placeholder="total amount" name="OthersCost[${addressRandNo}][total]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="form-check-rcm">
                                                                        <input type="checkbox" name="OthersCost[${addressRandNo}][rcm]" id="" value="1">
                                                                        <label for="">RCM</label>
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
                        console.log(response);
                        $("#vendorInfo").html(response);
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
            let batchId = $(this).val();


            $.ajax({
                type: "GET",
                url: `ajaxs/consumption/ajax-items-list.php`,
                data: {
                    act: "listItem",
                    batchId
                },
                beforeSend: function() {
                    //  $("#itemsTable").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                     console.log(response);

                    $("#itemsTable").append(response);
                    calculateAllItemsGrandAmount();
                   
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
                    currency_conversion();
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
            result = 'Zero Rupees';
        } else {
            result = convertToWords(rupees) + ' Rupees';
        }

        if (paisa > 0) {
            result += ' and ' + convertToWords(paisa) + ' Paise';
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
    

    function calculateOneItemRowAmount(rowNum) {
        let qty = parseFloat($(`#itemQty_${rowNum}`).val());
        qty = qty > 0 ? qty : 1;

        let unitPrice = parseFloat($(`#itemUnitPrice_${rowNum}`).val());
        unitPrice = unitPrice > 0 ? unitPrice : 0;

        let totalPrice = unitPrice * qty;

        $(`#itemTotalPrice_${rowNum}`).val(totalPrice.toFixed(2));
       // calculateAllItemsGrandAmount();
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
        let grandTotal = 0;
        let num = 0;
        // console.log(num);
        $(".updateitemTotalPrice").each(function() {
            let itemTotalPrice = parseFloat($(this).val());
            grandTotal += itemTotalPrice > 0 ? itemTotalPrice : 0;
            num = convertNumberToWords(grandTotal);
            console.log(itemTotalPrice);
        });


        $("#update_grandTotalAmount").html(grandTotal.toFixed(2) + "(" + num + ")");

        $("#update_grandTotalAmountInput").val(grandTotal.toFixed(2));
    }
    calculateUpdateAllItemsGrandAmount();

    function calculateOneUpdateItemRowAmount(rowNum) {
        let qty = parseFloat($(`#updateitemQty_${rowNum}`).val());
        console.log(qty);
        qty = qty > 0 ? qty : 1;
        let unitPrice = parseFloat($(`#updateitemUnitPrice_${rowNum}`).val());
        unitPrice = unitPrice > 0 ? unitPrice : 0;
        let totalPrice = unitPrice * qty;
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
        let rowNum = ($(this).attr("id")).split("_")[1];
        calculateOneUpdateItemRowAmount(rowNum);
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
    $("#podatecreation ").keyup(function() {

        check_date();

    });


    $(document).on("keyup", ".itemQty", function() {


        let attr = $(this).data("id");

        let qty = $("#itemQty_" + attr).val();
        let limit = $(".remqty_" + attr).val();

        //alert(attr);
        // alert(qty);
        //  alert(limit);

        if (Number(limit) < Number(qty)) {
            // alert(1);
            $("#qty_error_" + attr).html(`<p id="qty_error"> limit exceeded </p>`);
            document.getElementById("pobtn").disabled = true;
            document.getElementById("podbtn").disabled = true;

        } else {
            // alert(2);
            $("#qty_error_" + attr).html("");
            document.getElementById("pobtn").disabled = false;
            document.getElementById("podbtn").disabled = false;

        }

    });



    function currency_conversion() {
        // console.log("hello");
        for (elem of $(".itemUnitPricehidden")) {
            let rowNo = ($(elem).attr("id")).split("_")[1];
            // console.log(rowNo);
            $elem_val = $(elem).val();
            if ($elem_val == 0) {
                $val = $(`#itemUnitPrice_${rowNo}`).val();
                $(elem).val($val);

            }

            let newVal = $("#curr_rate").val() * $(elem).val();

            $(`#itemUnitPrice_${rowNo}`).val(newVal);

            calculateOneItemRowAmount(rowNo);
        };
    }

    $(document).on("keyup", "#curr_rate", function() {
        currency_conversion();
    });

    $(document).on("keydown", "#curr_rate", function() {
        currency_conversion();
    });
    currency_conversion();
</script>

