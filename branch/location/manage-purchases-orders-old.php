<?php
include("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
include("../common/header.php");
include("../common/navbar.php");
include("../common/sidebar.php");
require_once("../common/pagination.php");
include("../../app/v1/functions/company/func-branches.php");
include("../../app/v1/functions/branch/func-brunch-po-controller.php");

$today = date("Y-m-d");
if (isset($_POST["changeStatus"])) {
    $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
    swalToast($newStatusObj["status"], $newStatusObj["message"],);
}

$BranchPoObj = new BranchPo();

include("../../app/v1/functions/branch/func-items-controller.php");
$ItemsObj = new ItemsController();
if (isset($_POST['createData'])) {
    //console($POST);
    $addBranchPo = $BranchPoObj->addBranchPo($_POST, $branch_id, $company_id, $location_id);
    //console($addBranchPo);

    swalToast($addBranchPo["status"], $addBranchPo["message"]);
}

if (isset($_POST["visit"])) {
    $newStatusObj = VisitBranches($_POST);
    redirect(BRANCH_URL);
}


//$sql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE company_branch_id=".$branch_id." AND company_id=".$company_id." `vendor_status`!='deleted'";
$sql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `vendor_status`!='deleted'";
$get = queryGet($sql, true);
$datas = $get['data'];
$vendrSelect = '';
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

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

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
                                    <div class="card so-creation-card po-creation-card">
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
                                                                <select name="vendorId" id="" class="selct-vendor-dropdown">
                                                                    <option value="<?= $data["vendor_id"] ?>"><?= $data["vendor_name"] ?></option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <div class="customer-info-text po-customer-info-text" id="">
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
                                                                                            <input type="checkbox" id="addresscheckbox" name="addresscheckbox" value="1" title="checked here for same as Bill To adress" data-toggle="modal" data-target="#address-change" checked>
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
                            <div class="card so-creation-card po-creation-card">
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
                                            <div class="row info-form-view">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">Delivery Date</label>
                                                    <input type="date" name="deliveryDate" class="form-control" value="<?= $_POST["date"] ?>" />
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">PO Creation Date</label>
                                                    <input type="date" name="podatecreation" class="form-control" value="<?= $today ?>">
                                                </div>
                                            </div>
                                            <div class="row info-form-view">
                                                <div class="col-lg-3 col-md-3 col-sm-12 form-inline">
                                                    <label for="date">Use Types</label>
                                                    <select onclick="craateUserJsObject.ShowUseTypes();" name="usetypesDropdown" id="usetypesDropdown" class="form-control typesDropdown">
                                                        <option value="rawmaterial">Raw Material</option>
                                                        <option value="consumable">Consumable</option>
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

                                                            <input type="radio" value="fob" name="domestic" selected>
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
                                                    <input type="text" name="refNo" class="form-control" placeholder="Reference Number" value="<?= $rfq_code ?>" />
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

                                    </tbody>
                                    <tbody class="total-calculate">

                                        <?php
                                        foreach ($_POST["items"] as $key => $value) {
                                            if ($value['item_qty'] > 0) {
                                                $item_query = "SELECT * FROM erp_inventory_items WHERE itemId=$key AND  status='active' AND company_id=$company_id";
                                                $itemdata = queryGet($item_query);
                                                $randCode = $key . rand(00, 99);
                                                // console($itemdata);
                                                         ?>
                                                <tr>
                                                    <td><?= $itemdata["data"]["itemCode"] ?><input type ="hidden" value ="<?= $itemdata["data"]["itemCode"] ?>" name="itemCode"></td>
                                                    <td><?= $itemdata["data"]["itemName"] ?><input type ="hidden" value ="<?= $itemdata["data"]["itemName"] ?>" name="itemName"></td>
                                                    <td><?= $value['item_qty'] ?><input type ="hidden" value ="<?= $value['item_qty'] ?>" name="qty"></td>
                                                    <td><?= $value['price'] ?> <input type ="hidden" value ="<?= $value['price'] ?>" name="unitPrice"></td>
                                                    
                                                    <td><?= $value['item_qty'] * $value['price'] ?></td>
                                                    <td class="action-flex-btn">

                                                        <button type="button" class="btn-view btn btn-primary" data-toggle="modal" data-target="#deliveryScheduleModal_<?= $randCode ?>">
                                                            <i class="statusItemBtn fa fa-cog" id="statusItemBtn_<?= $key ?>"></i>
                                                        </button>

                                                        <button class="btn btn-danger">
                                                            <i class="fa fa-minus" id="delItemBtn_<?= $key ?>"></i>
                                                        </button>



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
                                        <?php
                                            }
                                        }
                                        ?>

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
                                                                        <label for="">Vendor Select</label>
                                                                        <select class="form-control" name="FreightCost[l1][txt]">
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
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="service description" name="FreightCost[l1][service]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">GST</label>
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
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="description" name="OthersCost[13][service]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">GST</label>
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

                                </div>


                            </div>
                            <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Save & Close</button>
                            <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-danger save-close-btn btn-xs float-right add_data" value="add_draft">Save as Draft</button>
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
                                    <div class="card so-creation-card po-creation-card">
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
                                                            <div class="form-inline input-box customer-select">

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
                                                                                            <input type="checkbox" id="addresscheckbox" name="addresscheckbox" value="1" title="checked here for same as Bill To adress" data-toggle="modal" data-target="#address-change" checked>
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
                            <div class="card so-creation-card po-creation-card">
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
                                            <div class="row info-form-view">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">Delivery Date</label>
                                                    <input type="date" value="<?= date("Y-m-d") ?>" name="deliveryDate" id="deliveryDate" class="form-control" />
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">PO Creation Date</label>
                                                    <input type="date" name="podatecreation" class="form-control" value="<?= $today ?>">
                                                </div>
                                            </div>
                                            <div class="row info-form-view">
                                                <div class="col-lg-3 col-md-3 col-sm-12 form-inline">
                                                    <label for="date">Use Types</label>
                                                    <select onclick="craateUserJsObject.ShowUseTypes();" name="usetypesDropdown" id="usetypesDropdown" class="form-control typesDropdown">
                                                        <option value="rawmaterial">Raw Material</option>
                                                        <option value="consumable">Consumable</option>
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

                                                            <input type="radio" value="fob" name="domestic" selected>
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
                                                    <input type="text" name="refNo" class="form-control" placeholder="Reference Number" />
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
                                                        <option value="">Goods Type</option>
                                                        <option value="hello">hello</option>
                                                        <option value="hello1">hello1</option>
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
                                            <!-- Info -->
                                            <th>Total Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTable">

                                    </tbody>
                                    <tbody class="total-calculate">
                                        <tr>
                                            <td colspan="4" class="text-right" style="border: none;"> </td>
                                            <td colspan="0" class="text-right pr-3" style="border: none;">Total Amount</td>
                                            <input type="hidden" name="totalAmt" id="grandTotalAmountInput" value="0.00">
                                            <td colspan="2" class="text-right pr-3" style="border: none; background: none;" id="grandTotalAmount">0.00</th>
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
                                                                        <select class="form-control" name="FreightCost[l1][txt]">
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

                                </div>


                            </div>
                            <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Save & Close</button>
                            <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-danger save-close-btn btn-xs float-right add_data" value="add_draft">Save as Draft</button>
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
<?php } elseif (isset($_GET['pr-po-creation'])) {
    //echo $_GET['pr-po-creation'];

    $id = $_GET['pr-po-creation'];
    $sql = "SELECT * FROM `erp_branch_purchase_request` WHERE `purchaseRequestId`='" . $id . "'";
    $sqlGet = queryGet($sql);
    $sqlData = $sqlGet['data'];
    //  console($sqlData);

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
                                                                <label for="">Select Vendor</label>
                                                                &nbsp; &nbsp;
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
                                                    <input type="date" name="deliveryDate" class="form-control" />
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label for="date">PO Creation Date</label>
                                                    <input type="date" name="podatecreation" class="form-control" value="<?= $today ?>">
                                                </div>
                                            </div>
                                            <div class="row info-form-view">
                                                <div class="col-lg-3 col-md-3 col-sm-12 form-inline">
                                                    <label for="date">Use Types</label>
                                                    <select onclick="craateUserJsObject.ShowUseTypes();" name="usetypesDropdown" id="usetypesDropdown" class="form-control typesDropdown">
                                                        <option value="rawmaterial">Raw Material</option>
                                                        <option value="consumable">Consumable</option>
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
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="tooltip-label">
                                                        <label for="">Reference Number</label>
                                                        <div class="help-tip">
                                                            <p>Customer PO</p>
                                                        </div>
                                                    </div>
                                                    <input type="text" name="refNo" class="form-control" value="<?= $sqlData['prCode'] ?>" />
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
                                            <th>Total Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTable">
                                        <?php
                                        $pr_ite_sql = "SELECT *  FROM `erp_branch_purchase_request_items` WHERE `prId`='" . $id . "' ";
                                        $pr = queryGet($pr_ite_sql, true);
                                        $pr_data = $pr['data'];
                                        // console($pr);
                                        foreach ($pr_data as $data) {
                                            // console($data['itemId']);
                                            $qty = $data['itemQuantity'];
                                            $itemId = $data['itemId'];
                                            $getItemObj = $ItemsObj->getItemById($itemId);
                                            // console($getItemObj);
                                            $itemCode = $getItemObj['data']['itemCode'];
                                            $lastPricesql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` WHERE `itemCode`=$itemCode ORDER BY po_item_id DESC LIMIT 1";
                                            $last = queryGet($lastPricesql);
                                            $lastRow = $last['data'] ?? "";
                                            $lastPrice = $lastRow['unitPrice'] ?? "";


                                            $randCode = $getItemObj['data']['itemId'] . rand(00, 99);

                                        ?>
                                            <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>">
                                                <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemId]" value="<?= $getItemObj['data']['itemId'] ?>">
                                                <td>
                                                    <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemCode]" value="<?= $getItemObj['data']['itemCode'] ?>">
                                                    <?= $getItemObj['data']['itemCode'] ?>
                                                </td>
                                                <td>
                                                    <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
                                                    <?= $getItemObj['data']['itemName'] ?>
                                                </td>
                                                <td>
                                                    <div class="flex-display">
                                                        <input type="number" name="listItem[<?= $randCode ?>][qty]" value="<?= $qty ?>" class="form-control full-width itemQty" id="itemQty_<?= $randCode ?>"><?= $qty ?>
                                                        <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>
                                                        <input type="hidden" name="listItem[<?= $randCode ?>][uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" name="listItem[<?= $randCode ?>][unitPrice]" id="itemUnitPrice_<?= $randCode ?>" value="<?= $lastPrice ?>" class="form-control full-width-center itemUnitPrice">

                                                </td>
                                                <!-- <td class="flex-display">
            <input type="number" name="listItem[<?= $randCode ?>][totalDiscount]" value="0.00" class="form-control full-width-center itemDiscount">%
        </td> -->
                                                <td>
                                                    <input type="text" name="listItem[<?= $randCode ?>][totalPrice]" id="itemTotalPrice_<?= $randCode ?>" value="<?= $lastPrice ?>" class="form-control full-width-center itemTotalPrice" readonly>
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







                                        <?php  } ?>
                                    </tbody>
                                    <tbody class="total-calculate">
                                        <tr>
                                            <td colspan="4" class="text-right" style="border: none;"> </td>
                                            <td colspan="0" class="text-right" style="border: none;">Total Amount</td>
                                            <input type="hidden" name="totalAmt" id="grandTotalAmountInput" value="0.00">
                                            <td colspan="2" style="border: none; background: none; " id="grandTotalAmount">0.00</th>
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
                                                                        <select class="form-control" name="FreightCost[l1][txt]">
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

                            <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Save & Close</button>
                            <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-danger save-close-btn btn-xs float-right add_data" value="add_draft">Save as Draft</button>
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
} else { ?>
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
                                    <h3 class="card-title">Manage Purchase order</h3>
                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?po-creation" class="btn btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
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
                                        <div class="col-lg-2 col-md-2 col-sm-12">
                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-lg-10 col-md-10 col-sm-12">
                                            <div class="row table-header-item">
                                                <div class="col-lg-11 col-md-11 col-sm-11">
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
                                        $cond .= " AND `po_number` like '%" . $_REQUEST['keyword2'] . "%' OR `po_date` like '%" . $_REQUEST['keyword2'] . "%'";
                                    } else {
                                        if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                            $cond .= " AND `po_number` like '%" . $_REQUEST['keyword'] . "%'  OR `po_date` like '%" . $_REQUEST['keyword'] . "%'";
                                        }
                                    }

                                    $sql_list = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE 1 " . $cond . "  AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=$company_id " . $sts . "  ORDER BY po_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
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
                                                    <th>Icon</th>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <th>Vendor Name</th>
                                                    <?php }
                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <th>Reference Number</th>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <th>PO Date</th>
                                                    <?php  }
                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <th>PO Number</th>
                                                    <?php }
                                                    if (in_array(5, $settingsCheckbox)) { ?>
                                                        <th>Total Amount</th>
                                                    <?php  }
                                                    if (in_array(6, $settingsCheckbox)) { ?>
                                                        <th>Total Items</th>
                                                    <?php }
                                                    if (in_array(7, $settingsCheckbox)) { ?>
                                                        <th>Status</th>
                                                    <?php } ?>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>



                                            <tbody>
                                                <?php
                                                $poList = $qry_list['data'];

                                                foreach ($poList as $onePoList) {
                                                    // console($onePoList['po_number']);
                                                    $trade_name =  $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0]['trade_name']
                                                ?>
                                                    <tr>
                                                        <td><?= $cnt++ ?></td>
                                                        <td>
                                                            <div class="img-fluid avatar rounded-circle d-flex justify-content-center align-items-center" style="border: 1px solid grey;"><?php echo ucfirst(substr($trade_name, 0, 1)) ?></div>
                                                        </td>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <td><?= $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0]['trade_name'] ?></td>
                                                        <?php }
                                                        if (in_array(2, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['ref_no'] ?></td>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['po_date'] ?></td>
                                                        <?php }
                                                        if (in_array(4, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['po_number'] ?></td>
                                                        <?php }
                                                        if (in_array(5, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['totalAmount'] ?></td>
                                                        <?php }
                                                        if (in_array(6, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['totalItems'] ?></td>
                                                        <?php }
                                                        if (in_array(7, $settingsCheckbox)) { ?>
                                                            <td>
                                                                <p class="status">Open</p>
                                                            </td>

                                                        <?php } ?>
                                                        <td>
                                                            <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                                        </td>
                                                    </tr>




                                                    <!-- right modal start here  -->

                                                    <div class="modal fade right customer-modal" id="fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                            <!--Content-->
                                                            <div class="modal-content">
                                                                <!--Header-->
                                                                <div class="modal-header">

                                                                    <div class="customer-head-info">
                                                                        <div class="customer-name-code">
                                                                            <h2>₹<?= $onePoList['totalAmount'] ?></h2>
                                                                            <p class="heading lead"><?= $onePoList['po_number'] ?></p>
                                                                            <p>REF :&nbsp;<?= $onePoList['ref_no'] ?></p>
                                                                        </div>
                                                                        <?php
                                                                        $vendorDetails = $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0];
                                                                        ?>
                                                                        <div class="customer-image">
                                                                            <div class="name-item-count">
                                                                                <h5><?= $vendorDetails['trade_name'] ?></h5>
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
                                                                    </ul>
                                                                </div>
                                                                <div class="modal-body">

                                                                    <div class="tab-content" id="myTabContent">
                                                                        <div class="tab-pane fade show active" id="home<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="home-tab">
                                                                            <?php
                                                                            $itemDetails = $BranchPoObj->fetchBranchPoItems($onePoList['po_id'])['data'];
                                                                            foreach ($itemDetails as $oneItem) {
                                                                            ?>
                                                                                <form action="" method="POST">

                                                                                    <div class="hamburger">
                                                                                        <div class="wrapper-action">
                                                                                            <i class="fa fa-cog fa-2x"></i>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="nav-action" id="settings">
                                                                                        <a title="Delivery Creation" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($onePoList['po_number']) ?>" name="vendorEditBtn">
                                                                                            <i class="fa fa-box"></i>
                                                                                        </a>
                                                                                    </div>
                                                                                    <div class="nav-action" id="thumb">
                                                                                        <a title="Notify Me" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($onePoList['po_number']) ?>" name="vendorEditBtn">
                                                                                            <i class="fa fa-bell"></i>
                                                                                        </a>
                                                                                    </div>
                                                                                    <div class="nav-action" id="create">
                                                                                        <a title="Edit" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($onePoList['po_number']) ?>" name="vendorEditBtn">
                                                                                            <i class="fa fa-edit"></i>
                                                                                        </a>
                                                                                    </div>
                                                                                    <div class="nav-action trash" id="share">
                                                                                        <a title="Delete" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($onePoList['po_number']) ?>" name="vendorEditBtn">
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
                                                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOnePo" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                    <span>Vendor Details</span>
                                                                                                </button>
                                                                                            </h2>
                                                                                            <div id="flush-collapseOnePo" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
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
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--/.Content-->
                                                    </div>
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

                                        <?php  } ?>

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
                                                            SO Number</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                            Customer PO Number</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                            PO Date</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                                            Customer Name</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                                            Total Amount</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="6" />
                                                            Total Items</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="7" />
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
    </div>
    </section>
    </div>
    <!-- End Pegination from------->
<?php } ?>

<?php
include("../common/footer.php");
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
        console.log(addressBody);
        $("#shipToAddressDiv").html(addressBody);
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
                                            <input type="text" name="listItem[${randCode}][deliverySchedule][${addressRandNo}][quantity]" class="form-control multiQuantity" id="multiQuantity_${addressRandNo}" placeholder="quantity" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                    <div class="add-btn-minus">
                                            <a style="cursor: pointer" class="btn btn-danger" onclick="rm(538)">
                                              <i class="fa fa-minus"></i>
                                            </a>
                                            </div>
                                    </div>
                                </div>`);
    }


    function loadItems() {
        $.ajax({
            type: "GET",
            url: `ajaxs/po/ajax-items.php`,
            beforeSend: function() {
                $("#itemsDropDown").html(`<option value="">Loding...</option>`);
            },
            success: function(response) {
                $("#itemsDropDown").html(response);
            }
        });
    }
    loadItems();
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
        $('#vendorDropdown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });

            //calculate
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


            let deliveryDate = $('#deliveryDate').val();
            // console.log(deliveryDate);
            let itemId = $(this).val();
            $.ajax({
                type: "GET",
                url: `ajaxs/po/ajax-items-list.php`,
                data: {
                    act: "listItem",
                    itemId,
                    deliveryDate
                },
                beforeSend: function() {
                    //  $("#itemsTable").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    // console.log(response);
                    $("#itemsTable").append(response);
                    calculateAllItemsGrandAmount();
                }
            });

        });
        $(document).on("click", ".delItemBtn", function() {
            // let id = ($(this).attr("id")).split("_")[1];
            // $(`#delItemRowBtn_${id}`).remove();
            $(this).parent().parent().remove();
            calculateAllItemsGrandAmount();
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

    /********************************************** */
    function calculateAllItemsGrandAmount() {
        let grandTotal = 0;
        $(".itemTotalPrice").each(function() {
            let itemTotalPrice = parseFloat($(this).val());
            grandTotal += itemTotalPrice > 0 ? itemTotalPrice : 0;
        });
        $("#grandTotalAmount").html(grandTotal.toFixed(2));
        $("#grandTotalAmountInput").val(grandTotal.toFixed(2));
    }

    function calculateOneItemRowAmount(rowNum) {
        let qty = parseFloat($(`#itemQty_${rowNum}`).val());
        qty = qty > 0 ? qty : 0;
        let unitPrice = parseFloat($(`#itemUnitPrice_${rowNum}`).val());
        unitPrice = unitPrice > 0 ? unitPrice : 0;
        let totalPrice = unitPrice * qty;
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
</script>