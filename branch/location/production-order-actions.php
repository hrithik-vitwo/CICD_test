<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");

require_once("../../app/v1/functions/branch/func-bom-controller.php");
require_once("../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../app/v1/functions/branch/func-production-order-controller.php");
require_once("../../app/v1/functions/branch/func-stock-controller.php");


require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");

?>

<style>
    .card.pr-creation-card {
        height: auto;
    }

    .production-order-info-body {
        height: 160px !important;
    }

    .production-status-view-modal .modal-header .details-input {
        display: flex;
        align-items: center;
        gap: 7px;
        position: relative;
        margin-bottom: 3px;
    }

    .production-status-view-modal .modal-header .details-input p {
        position: absolute;
        left: 87px;
        font-size: 0.8rem;
        top: 0;
        white-space: nowrap;
        max-width: 550px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .production-status-view-modal .modal-header .details-input .mrp-btn {
        position: relative;
        left: 94px;
        top: -4px;
    }

    .production-status-view-modal .modal-header .details-input p.item-details.item-mrp {
        display: flex;
    }

    .production-status-view-modal .modal-header .details-input p.item-details.item-mrp .mrp-btn {
        position: relative;
        left: 0;
        top: -4px;
    }

    .production-status-view-modal .modal-header .details-input p.item-details.item-mrp .mrp-btn ion-icon {
        color: #fff;
        font-size: 1.2rem;
    }

    .btn-group-toggle label.btn.btn-secondary.waves-effect.waves-light.active {
        background: #5f5f5f;
    }

    .production-status-view-modal .modal-header .nav-link.treeTable:hover i,  .production-status-view-modal .modal-header .nav-link.treeTable.active i{
        color: #003060;
    }

    .production-status-view-modal .modal-header .nav-link.treeTable i {
        font-weight: 600 !important;
        color: #fff;
    }

    .production-status-view-modal .modal-header .nav-link {
        gap: 0 !important;
    }
</style>


<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<script src="<?= BASE_URL ?>public/assets/simple-tree-table/dist/jquery-simple-tree-table.js"></script>


<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>


<?php
require_once("bom/controller/bom.controller.php");
require_once("bom/controller/mrp.controller.php");
require_once("bom/controller/mrprelease.controller.php");
include_once("bom/controller/consumption.controller.php");
include_once("bom/controller/consumptionbackflash.controller.php");

$productionOrderController = new ProductionOrderController();
$goodsBomController = new GoodsBomController();
$accountingControllerObj = new Accounting();



if (isset($_GET["consumption-backflash"]) && isset($_POST["soProdId"])) {
    $consumptionBackFlashControllerObj = new ConsumptionBackFlashController();
    $backFlashObj = $consumptionBackFlashControllerObj->confirmConsumption($_POST);
    swalAlert($backFlashObj["status"], ucfirst($backFlashObj["status"]), $backFlashObj["message"], BASE_URL . "branch/location/manage-production-order.php");
    // console($backFlashObj);
}

if (isset($_POST["submitProductionOrderMrpReleaseFrm"]) && $_POST["productionOrderId"] > 0) {
    // console($_POST);
    $mrpReleaseControllerObj = new MrpReleaseController();
    $releaseOrderObj = $mrpReleaseControllerObj->releaseOrder($_POST["productionOrderId"], $_POST);
    // console($releaseOrderObj);
    swalAlert($releaseOrderObj["status"], ucfirst($releaseOrderObj["status"]), $releaseOrderObj["message"], BASE_URL . "branch/location/manage-production-order.php");
}

if (isset($_POST['addNewProduction'])) {
    // console($_POST);
    // exit();
    $productionOrder = $productionOrderController->createProduction($_POST);
    // console($productionOrder);
    swalAlert($productionOrder["status"], ucfirst($productionOrder["status"]), $productionOrder["message"], BASE_URL . "branch/location/manage-production-order.php");
}

if (isset($_GET["run-mrp"])) {
    // require_once("components/production/production-order-mrp-preview.php");
    require_once("bom/mrp.php");
}elseif (isset($_GET["run-multi-mrp"])) {
    // require_once("components/production/production-order-mrp-preview.php");
    require_once("bom/mrp-multi.php");
    
}elseif (isset($_GET["consumption-preview"])) {
    require_once("components/production/production-order-barcode-and-consumption.php");
} elseif (isset($_GET['open'])) {
    require_once("components/production/production-order-open.php");
} elseif (isset($_GET['released'])) {
    require_once("components/production/production-order-released.php");
} elseif (isset($_GET['closed'])) {
    require_once("components/production/production-order-closed.php");
} elseif (isset($_GET["create"])) {
?>

    <div class="content-wrapper is-production-order">
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
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Production Order List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Production Order</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>
                <form action="" method="POST" id="addNewProductionForm">
                    <input type="hidden" name="addNewProduction">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card pr-creation-card so-creation-card">
                                <div class="card-header">
                                    <div class="row others-info-head">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="head">
                                                <i class="fa fa-pen"></i>
                                                <h4>Info</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body others-info production-order-info-body">
                                    <div class="row others-info-form-view">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="row others-info-form-view">

                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="form-input">
                                                        <label>Expected Date<span class="text-danger">*</span></label>
                                                        <input type="date" name="expDate" class="form-control" id="expDate" value="<?= $today ?>" />
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-3 col-md-4 col-sm-12">
                                                    <div class="form-input">
                                                        <label>Validity Period</label>
                                                        <input type="date" class="form-control" id="dateInputvalid" name="validitydate" min="<?php echo date('Y-m-d'); ?>"  required>
                                                    </div>
                                                </div>       
                                                        
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="form-input">
                                                        <label>Reference Number</label>
                                                        <input id="refNo" type="text" name="refNo" class="form-control" placeholder="Ref. No."/>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="form-input">
                                                        <label>Note</label>
                                                        <textarea name="description" class="form-control" placeholder="Note"><?= $row['description'] ?></textarea>
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
                            <div class="card item-select-table" style="overflow-x: auto;">
                                <table class="table table-sales-order">
                                    <thead>
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Item Code</th>
                                            <th>Description</th>
                                            <th>Quantity</th>
                                            <th>UOM</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTable">
                                        <tr>
                                            <td width="50%">
                                                <select name="item_id" class="form-control itemDrop" id="itemDrop">
                                                    <option>Select Item</option>
                                                    <?php
                                                    $item = queryGet("SELECT * FROM `erp_inventory_items` WHERE `company_id`=$company_id AND (`goodsType`= 3 OR `goodsType` = 2)", true);
                                                    foreach ($item['data'] as $data) {
                                                        $bomInfoObj = queryGet("SELECT * FROM `erp_bom` WHERE `locationId`=$location_id AND `itemId`=".$data['itemId']." AND `bomStatus`='active'");
                                                        // console($bomInfoObj);
                                                        $optionIsDisable = $bomInfoObj["status"]!="success" ? "disabled" : "";
                                                        $optionIsDisableInfo = $optionIsDisable!="" ? " [Item incomplete, Bom is not created]" : "";

                                                    ?>
                                                        <option value="<?= $data['itemId'] ?>" <?= $optionIsDisable ?>>
                                                            <?= $data['itemName'] . "[" . $data['itemCode'] . ']'.$optionIsDisableInfo ?>
                                                        </option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td id="itemCode" width="10%"></td>
                                            <input type="hidden" name="itemCode" id="itemCodeHidden">
                                            <td width="30%">
                                                <p class="pre-wrap" id="itemDesc"></p>
                                            </td>
                                            <td width="10%">
                                                <input type="number" placeholder="<?= decimalQuantityPreview(0)?>" step="any" name="item_qty" class="form-control inputQuantityClass" min="0.00001">
                                            </td>
                                            <td><p class="pre-wrap" id="itemUOM"></p></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="addNewProductionFormSubmitBtn" id="prbtn" class="btn btn-xs btn-primary items-search-btn float-right">Submit</button>
                </form>
            </div>
        </section>
    </div>
<?php
} else {
    ?>
  <script>
    let url = `<?= BRANCH_URL ?>location/manage-production-order.php`;
    window.location.href = url;
  </script>
<?php
}
require_once("../common/footer.php");
?>
<script>
    $('#itemDrop')
        .select2()
        .on('select2:open', () => {});

    $("#itemDrop").on('change', function() {
        var itemId = $(this).val();
        $.ajax({
            type: "GET",
            url: `ajaxs/production/ajax-items.php`,
            data: {
                itemId
            },
            beforeSend: function() {

            },
            success: function(response) {
                var obj = jQuery.parseJSON(response);
                $("#itemCode").html(obj['code']);
                $("#itemCodeHidden").val(obj['code']);
                $("#itemDesc").html(obj['desc']);
                $("#itemUOM").html(obj['uom']);
            }
        });
    });
</script>