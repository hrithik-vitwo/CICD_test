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
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<script src="<?= BASE_URL ?>public/assets/simple-tree-table/dist/jquery-simple-tree-table.js"></script>



<?php
require_once("bom/controller/bom.controller.php");
require_once("bom/controller/mrp.controller.php");
include_once("bom/controller/consumption.controller.php");

$productionOrderController = new ProductionOrderController();
$goodsBomController = new GoodsBomController();
$accountingControllerObj = new Accounting();

if (isset($_POST['addNewProduction'])) {
    
    $productionOrder = $productionOrderController->createProduction($_POST);
    swalAlert($productionOrder["status"], ucfirst($productionOrder["status"]), $productionOrder["message"], BASE_URL . "branch/location/manage-production-order.php");
}
?>
<style>
    .manage-production-modal .modal-body span.error {
        position: relative;
        display: block !important;
    }
</style>



<?php

if (isset($_GET["consumption-preview"])) {
    // console($_POST);
    // console($_GET);
    // exit();

    require_once("components/production/production-order-barcode-and-consumption.php");
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
        <section class="content">
            <div class="container-fluid">
                <div class="row p-0 m-0">
                    <?php
                    $filteredOrderStatus = isset($_GET["orderStatus"]) ? $_GET["orderStatus"] : "all";
                    $filteredGoodType = isset($_GET["goodType"]) ? $_GET["goodType"] : "all";
                    $stockControllerObj = new StockController();
                    $productionOrderlistObj = $productionOrderController->getSubProductionOrderList($filteredGoodType);
                    // console($productionOrderlistObj); 
                    //NEW BOM Controller
                    $bomControllerObj = new BomController();

                    if (isset($_POST["reverseProdDeclaration"])) {
                        $declarationId = $_POST["prodDeclarationId"];
                        // $created_by = $this->created_by;
                        // $updated_by = $this->updated_by;
                        // $company_id = $thi->company_id;
                        // $branch_id = $thi->branch_id;
                        // $location_id = $thi->location_id;

                        // $reversePostingObj = new ReversePosting();
                        // $resultObj = $reversePostingObj->reverseProdDeclaration($prodDeclarationId);
                        // console($resultObj);
                    }
                    ?>
                    <div class="col-12 mt-2 p-0">
                        <div class="p-0 pt-1 my-2">
                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                    <h3 class="card-title">Manage Production Declaration</h3>
                                    <div class="d-flex gap-2">
                                        <label class="text-nowrap">Goods Type</label>
                                        <select name="" id="productionOrderListFilter_goodType" class="form-control productionOrderListFilter">
                                            <option value="all" <?= (isset($_GET["goodType"]) && $_GET["goodType"] == "all") ? "selected" : "" ?>>All</option>
                                            <option value="sfg" <?= (isset($_GET["goodType"]) && $_GET["goodType"] == "sfg") ? "selected" : "" ?>>Semi-Finished Good</option>
                                            <option value="fg" <?= (isset($_GET["goodType"]) && $_GET["goodType"] == "fg") ? "selected" : "" ?>>Finished Good</option>
                                        </select>
                                        <script>
                                            $(document).ready(function() {
                                                $(document).on("change", ".productionOrderListFilter", function() {
                                                    let filterName = ($(this).attr("id")).split("_")[1];
                                                    let filterValue = $(this).val();
                                                    let orderStatus = `<?= isset($_GET["orderStatus"]) ? $_GET["orderStatus"] : "all" ?>`;
                                                    let goodType = `<?= isset($_GET["goodType"]) ? $_GET["goodType"] : "all" ?>`;
                                                    if (filterName == "orderStatus") {
                                                        window.location.href = `?filter&orderStatus=${filterValue}`;
                                                    } else {
                                                        window.location.href = `?filter&orderStatus=${orderStatus}&goodType=${filterValue}`;
                                                    }
                                                    console.log(filterName, filterValue);
                                                });
                                            });
                                        </script>
                                    </div>
                                    <span>
                                        <span id="multipleMrpRunSpan"></span>
                                        <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary"><i class="fa fa-plus" style="margin-right: 0;"></i></a>
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="card card-tabs" style="border-radius: 20px;">
                            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                                <div class="card-body">
                                    <div class="row filter-serach-row">
                                        <div class="col-lg-2 col-md-2 col-sm-12">
                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-lg-10 col-md-10 col-sm-12">
                                            <div class="section serach-input-section">
                                                <input type="text" id="myInput" placeholder="" class="field form-control" />
                                                <div class="icons-container">
                                                    <div class="icon-search">
                                                        <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                                    </div>
                                                    <div class="icon-close">
                                                        <i class="fa fa-search po-list-icon" onclick="javascript:alert('Hello World!')" id="myBtn"></i>
                                                        <script>
                                                            var input = document.getElementById("myInput");
                                                            input.addEventListener("keypress", function(event) {
                                                                if (event.key === "Enter") {
                                                                    event.preventDefault();
                                                                    document.getElementById("myBtn").click();
                                                                }
                                                            });
                                                        </script>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Filter Vendors</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync fa-spin"></i>Reset</a>
                                                        <a type="button" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>Search</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <table class="table defaultDataTable table-hover">
                                <thead>
                                    <tr class="alert-light">
                                        <th>Sl</th>
                                        <th>Sub Production Order</th>
                                        <th>Item Type</th>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>Ref/SO</th>
                                        <th>MRP Code</th>
                                        <th>Quantity</th>
                                        <th>Remain Qty</th>
                                        <th>Require Date</th>
                                        <th>Work Center</th>
                                        <th>Table</th>
                                        <th>Created Date</th>
                                        <th>Created By</th>
                                        <th>Release Status</th>
                                        <th>MRP Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Array
                                    // (
                                    //     [sub_prod_id] => 32
                                    //     [prod_id] => 87
                                    //     [company_id] => 1
                                    //     [branch_id] => 1
                                    //     [location_id] => 1
                                    //     [subProdCode] => PR1704887341759/2
                                    //     [prodCode] => PR1704887341759
                                    //     [itemId] => 310
                                    //     [itemCode] => 22000060
                                    //     [prodQty] => 20.00
                                    //     [remainQty] => 20.00
                                    //     [expectedDate] => 2024-01-31
                                    //     [mrp_status] => Created
                                    //     [wc_id] => 1
                                    //     [table_id] => 1
                                    //     [created_at] => 2024-01-19 15:56:21
                                    //     [created_by] => 2|location
                                    //     [updated_at] => 2024-01-19 15:56:21
                                    //     [updated_by] => 2|location
                                    //     [status] => 13
                                    //     [table_name] => Table 1
                                    //     [work_center_name] => work center 1
                                    //     [itemName] => DO NOT TOUCH THIS ITEM
                                    //     [itemDesc] => DO NOT TOUCH THIS ITEM dESC
                                    //     [goodsType] => 3
                                    //     [itemOpenStocks] => 0
                                    //     [itemBlockStocks] => 0
                                    //     [goodTypeName] => Finished Good
                                    //     [goodTypeShortName] => FG
                                    // )
                                    $masterCheckStockLocations = [
                                        1 => "rmWhOpen",
                                        2 => "sfgStockOpen",
                                        "other" => "fgWhOpen"
                                    ];
                                    $sl = 0;

                                    foreach ($productionOrderlistObj["data"] as $listRow) {
                                        $statusOpenVal = 9;
                                        $statusReleaseVal = 13;
                                        $statusCloseVal = 10;

                                        $releaseStatusName = ($listRow["status"] == $statusOpenVal) ? "Open" : (($listRow["status"] == $statusReleaseVal) ? "Release" : "Close");
                                        $releaseStatusCssClass =  ($listRow["status"] == $statusOpenVal) ? "warning" : (($listRow["status"] == $statusReleaseVal) ? "success" : "danger");

                                        $stockLocation = $masterCheckStockLocations[$listRow["goodsType"]] ?? $masterCheckStockLocations["other"];


                                        $itemQtyStockCheckingObj = itemQtyTotalStockChecking($listRow["itemId"], "'$stockLocation'");
                                        $itemStockQty = $itemQtyStockCheckingObj["data"]["itemQty"] ?? 0;
                                        // console($listRow);
                                    ?>
                                        <tr>
                                            <td><?= $sl += 1 ?></td>

                                            <td><?= $listRow["subProdCode"] ?></td>
                                            <td><?= $listRow["goodTypeName"] ?></td>
                                            <td><?= $listRow["itemCode"] ?></td>
                                            <td>
                                                <p class="pre-wrap"><?= $listRow["itemName"] ?></p>
                                            </td>
                                            <td><?= $listRow["prodCode"] ?></td>
                                            <td><?= $listRow["mrp_code"] ?></td>
                                            <td><?= $listRow["prodQty"] ?></td>
                                            <td><?= $listRow["remainQty"] ?></td>
                                            <td><?= $listRow["expectedDate"] ?></td>
                                            <td><?= $listRow["work_center_name"] ?></td>
                                            <td><?= $listRow["table_name"] ?></td>
                                            <td><?= $listRow["created_at"] ?></td>
                                            <td><?= getCreatedByUser($listRow["created_by"]) ?></td>
                                            <td><span class="badge badge-<?= $releaseStatusCssClass ?> p-1"><?= ucfirst($releaseStatusName) ?></span></td>
                                            <td class="text-center"><?= ($listRow["mrp_status"] == "Not Created") ? "<i class='fas fa-clock po-list-icon mx-auto'></i>" : "<i class='fas fa-check text-success mx-auto'></i>" ?></td>
                                            <td>
                                                <a style="cursor:pointer" href="" class="btn btn-sm productionOrderDetailsModalBtn" id="productionOrderDetailsModalBtn_<?= $listRow["sub_prod_id"] ?>" data-production-order-id="<?= $listRow["sub_prod_id"] ?>" data-item-id="<?= $listRow["itemId"] ?>" data-mrp-status="<?= $listRow["mrp_status"] ?>" data-remain-qty="<?= $listRow["remainQty"] ?>" data-toggle="modal" data-target="#productionOrderDetailsModal_<?= $listRow["sub_prod_id"] ?>"><i class="fa fa-eye po-list-icon"></i></a>
                                                <!-- Modal -->
                                                <div class="modal fade right manage-production-modal customer-modal" id="productionOrderDetailsModal_<?= $listRow["sub_prod_id"] ?>" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-right" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title text-light" id="exampleModalLongTitle"><?= ucfirst($listRow["itemName"]) ?></h5>
                                                                <div class="text-left">
                                                                    <p class="text-muted">Item Code: <span class="text-light"><?= ucfirst($listRow["itemCode"]) ?></span></p>
                                                                    <p class="text-muted">Current Stock: <span class="text-light"><?= number_format($itemStockQty, 2) ?></span></p>
                                                                    <p class="text-muted pre-wrap">Description: <span class="text-light"><?= ucfirst($listRow["itemDesc"]) ?></span></p>
                                                                    <p class="text-muted">Status: <span class="text-light"><?= ucfirst($releaseStatusName) ?></span></p>
                                                                    <?php
                                                                    if ($listRow["mrp_status"] == "Not Created") {
                                                                        // console($goodsBomController->isBomCreated($listRow["itemId"]));
                                                                        if (!$goodsBomController->isBomCreated($listRow["itemId"])) {
                                                                            echo "<p>BOM not created, please create a BOM to run MRP</p>";
                                                                        } else {
                                                                    ?>
                                                                            <p><a href="manage-production-order.php?run-mrp=<?= base64_encode($listRow["sub_prod_id"]) ?>" class="btn btn-sm btn-warning text-light">RUN MRP</a></p>
                                                                        <?php
                                                                        }
                                                                    } else {
                                                                        ?>
                                                                        <div class="d-flex">
                                                                            <p class="text-muted">MRP Status: <span class="text-light"><?= $listRow["mrp_status"] ?></span><?= strtolower($listRow["mrp_status"]) == "created" ? ' <a href="" style="cursor:pointer" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>' : "" ?></p>
                                                                        </div>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <div class="display-flex-space-between mt-2 mb-3">
                                                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                        <li class="nav-item">
                                                                            <a class="nav-link active" id="home-tab<?= str_replace('/', '-', $listRow["subProdCode"]) ?>" data-toggle="tab" href="#home<?= str_replace('/', '-', $listRow["subProdCode"]) ?>">Info</a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                            <a class="nav-link" id="declaration-list-tab<?= str_replace('/', '-', $listRow["subProdCode"]) ?>" data-toggle="tab" href="#declaration-list<?= str_replace('/', '-', $listRow["subProdCode"]) ?>">Declarations</a>
                                                                        </li>
                                                                        <!-- -------------------Audit History Button Start------------------------- -->
                                                                        <li class="nav-item">
                                                                            <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $listRow["sub_prod_id"]) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $listRow["subProdCode"]) ?>" href="#history<?= str_replace('/', '-', $listRow["sub_prod_id"]) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $listRow["sub_prod_id"]) ?>" aria-selected="false"><i class="fa fa-history mr-2"></i> Trail</a>
                                                                        </li>
                                                                        <!-- -------------------Audit History Button End------------------------- -->
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="tab-content" id="myTabContent">
                                                                    <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $listRow["subProdCode"]) ?>" role="tabpanel" aria-labelledby="home-tab">
                                                                        <div class="text-left">
                                                                            <?php
                                                                            if ($listRow["mrp_status"] == "Created" && $listRow["status"] != 10) {
                                                                            ?>
                                                                                <form action="?consumption-preview" method="post" class="h-100">
                                                                                    <input type="hidden" name="soProdId" value="<?= $listRow["prod_id"] ?>">
                                                                                    <input type="hidden" name="soSubProdId" value="<?= $listRow["sub_prod_id"] ?>">
                                                                                    <input type="hidden" name="soSubProdCode" value="<?= $listRow["subProdCode"] ?>">
                                                                                    <input type="hidden" name="soProdCode" value="<?= $listRow["prodCode"] ?>">
                                                                                    <input type="hidden" name="soProdCreatedDate" value="<?= explode(" ", $listRow["created_at"])[0] ?>">
                                                                                    <input type="hidden" name="itemCode" value="<?= $listRow["itemCode"] ?>">
                                                                                    <input type="hidden" name="itemId" value="<?= $listRow["itemId"] ?>">
                                                                                    <input type="hidden" name="itemUom" value="<?= $listRow["itemUom"] ?>">
                                                                                    <div class="row p-0 m-0">
                                                                                    <div class="col-md-2"><label for="" class="d-flex gap-1"><input type="checkbox" class="batchCheckBox" value="1" name="activeBatch" id="productionDeclareCheck_<?= $listRow["sub_prod_id"] ?>">Batch Number </label> <input type="text" name="productionDeclareBatch" value="PRODXXXXXXXXX" class="productionDeclareBatch form-control" id="productionDeclareBatch_<?= $listRow["sub_prod_id"] ?>" readonly></div>
                                                                                        <div class="col-md-2"><label for="">Declare Date</label> <input type="date" name="productionDeclareDate" value="<?= date("Y-m-d") ?>" class="productionDeclareDate form-control" id="productionDeclareDate_<?= $listRow["sub_prod_id"] ?>" required></div>
                                                                                        <div class="col-md-2"><label for="">Declare Quantity</label> <input type="number" step="any" min="1" max="<?= $listRow["remainQty"] ?>" id="productionQuantity_<?= $listRow["sub_prod_id"] ?>" name="productionQuantity" value="<?= $listRow["remainQty"] ?>" placeholder="eg. <?= $listRow["remainQty"] ?>" class="productionQuantity form-control" required> <small class="text-danger" id="productionQuantityWarningText_<?= $listRow["sub_prod_id"] ?>"></small></div>
                                                                                        <div class="col-md-2">
                                                                                            <label for=""> Dest. Storage Loc.</label>
                                                                                            <select class="form-control" name="productionDeclareLocation" required>
                                                                                                <option value="">Select Location</option>
                                                                                                <!-- <option value="auto" selected>Auto</option> -->
                                                                                                <?php
                                                                                                $storageLocationObj = queryGet("SELECT DISTINCT `storageLocationTypeSlug`, `storage_location_name` FROM `erp_storage_location` WHERE `location_id`=$location_id", true);
                                                                                                foreach ($storageLocationObj["data"] as $row) {
                                                                                                    $isSelected = $row["storageLocationTypeSlug"] == "rmProdOpen" ? "selected" : "";
                                                                                                ?>
                                                                                                    <option value="<?= $row["storageLocationTypeSlug"] ?>" <?= $isSelected ?>><?= $row["storage_location_name"] ?></option>
                                                                                                <?php
                                                                                                }
                                                                                                ?>
                                                                                            </select>
                                                                                        </div>
                                                                                        <div class="col-md-2"><label for="">Remain Quantity</label> <input type="number" value="<?= ($listRow["remainQty"]) ?>" class="form-control" id="remainingQty_<?= $listRow["sub_prod_id"] ?>" disabled></div>
                                                                                        <div class="col-md-2"><label for="">Order Quantity</label> <input type="number" value="<?= ($listRow["prodQty"]) ?>" class="form-control" disabled></div>
                                                                                    </div>
                                                                                    <hr>
                                                                                    <div id="productionOrderMrpDetailsDiv_<?= $listRow["sub_prod_id"] ?>">
                                                                                        <!-- Data will be coming from the api -->
                                                                                    </div>
                                                                                </form>
                                                                            <?php
                                                                            }
                                                                            if ($releaseStatusName == "Close") {
                                                                                echo "<p class='text-center'>This production declaration is closed!</p>";
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="tab-pane fade" id="declaration-list<?= str_replace('/', '-', $listRow["subProdCode"]) ?>" role="tabpanel" aria-labelledby="declaration-list-tab">
                                                                        <div class="text-left">
                                                                            <?php
                                                                            $declarationsListObj = queryGet("SELECT * FROM `erp_production_declarations` WHERE sub_prod_id=" . $listRow["sub_prod_id"] . " AND location_id=$location_id ORDER BY id DESC", true);
                                                                            ?>
                                                                            <table class="table defaultDataTable table-hover">
                                                                                <thead>
                                                                                    <tr class="alert-light">
                                                                                        <th>Sl</th>
                                                                                        <th>Decl. Code</th>
                                                                                        <th>Prod. Code</th>
                                                                                        <th>Sub. Prod. Code</th>
                                                                                        <th>Item Qty.</th>
                                                                                        <th>Date</th>
                                                                                        <th>Status</th>
                                                                                        <th>Action</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <?php
                                                                                    foreach ($declarationsListObj["data"] as  $declarationKey => $declaration) {
                                                                                    ?>
                                                                                        <tr>
                                                                                            <td><?= $declarationKey + 1 ?></td>
                                                                                            <td><?= $declaration["code"] ?></td>
                                                                                            <td><?= $declaration["prod_code"] ?></td>
                                                                                            <td><?= $declaration["sub_prod_code"] ?></td>
                                                                                            <td><?= $declaration["quantity"] ?></td>
                                                                                            <td><?= $declaration["created_at"] ?></td>
                                                                                            <td class="listStatus"><?= ucfirst($declaration["status"]) ?></td>
                                                                                            <td>
                                                                                                <?php
                                                                                                if ((($listRow["remainQty"] != $listRow["prodQty"]) || $listRow["remainQty"] == 0) && $declaration["status"] == "active") { ?>
                                                                                                    <a style="cursor:pointer" data-id="<?= $declaration["id"] ?>" class="btn btn-sm reverseProdDeclaration waves-effect waves-light" title="Reverse Now">
                                                                                                            <i class="far fa-undo po-list-icon"></i>
                                                                                                        </a>
                                                                                                    <!-- <form action="" method="post">
                                                                                                        <input type="hidden" name="prodDeclarationId" value="<?= $declaration["id"] ?>">
                                                                                                        <input type="submit" name="reverseProdDeclaration" value="Reverse">
                                                                                                    </form> -->
                                                                                                <?php
                                                                                                } ?>
                                                                                            </td>
                                                                                        </tr>
                                                                                    <?php
                                                                                    }
                                                                                    ?>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                    <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                    <div class="tab-pane fade" id="history<?= str_replace('/', '-', $listRow["sub_prod_id"]) ?>" role="tabpanel" aria-labelledby="history-tab">

                                                                        <div class="audit-head-section mb-3 mt-3 ">
                                                                            <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($listRow['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($listRow['created_at']) ?></p>
                                                                            <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($listRow['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($listRow['updated_at']) ?></p>
                                                                        </div>
                                                                        <hr>
                                                                        <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $listRow["porCode"]) ?>">

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
                                                </div>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    if ($sl == 0) {
                                        echo '<tr class="alert-light"><td colspan="15" class="text-center">Prouduction order not found!</td</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php
}
require_once("../common/footer.php");
?>
<script>
    $(document).ready(function() {

        
        $('.batchCheckBox').click(function(e) {
            var $this = $(this); // Store the reference to $(this) for later use
            let id = ($(this).attr("id")).split("_")[1];

            if ($(this).is(':checked')) {
                $(`#productionDeclareBatch_${id}`).removeAttr('readOnly');
                $(`#productionDeclareBatch_${id}`).val("");
            } else {
                $(`#productionDeclareBatch_${id}`).prop("readOnly", true);
                $(`#productionDeclareBatch_${id}`).val("PRODXXXXXXXXX");
            }
        });

        $('.reverseProdDeclaration').click(function(e) {
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
                            dep_slug: 'reverseProdDeclaration'
                        },
                        url: 'ajaxs/ajax-reverse-post.php',
                        beforeSend: function() {
                            $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                        },
                        success: function(response) {
                            // console.log(response);
                            var responseObj = JSON.parse(response);
                            console.log(responseObj);
                            if (responseObj.status == 'success') {
                                $this.parent().parent().find('.listStatus').html('Reverse');
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
                                title: ' ' + responseObj.message
                            }).then(function() {
                                // location.reload();
                            });
                        }
                    });
                }
            });
        });

        $(document).on('click', ".multipleMrpCheckBox", function() {
            let total = $('input[name="multipleMrp[]"]:checked').length;
            if (total > 0) {
                let productionOrderIds = [];
                $('input[name="multipleMrp[]"]:checked').each(function() {
                    productionOrderIds.push(parseInt($(this).val()));
                });
                let productionOrderIdsStr = productionOrderIds.join(",");
                $("#multipleMrpRunSpan").html(`<a href="manage-production-order.php?run-mrp=${btoa(productionOrderIdsStr)}" id="multipleMrpRunBtn" class="btn btn-sm btn-primary">Run Multiple MRP</a>`);
            } else {
                $("#multipleMrpRunSpan").html('');
            }
        });

        $(document).on("keyup", ".productionQuantity", function() {
            let prodId = ($(this).attr("id")).split("_")[1];
            let prodQuantity = parseFloat($(this).val());
            let remainingQty = parseFloat($(`#remainingQty_${prodId}`).val());
            prodQuantity = prodQuantity > 0 ? prodQuantity : 0;
            if (prodQuantity > remainingQty) {
                prodQuantity = 0;
                $(this).val(prodQuantity);
                $(`#productionQuantityWarningText_${prodId}`).html("Declare qty can't be greater than remaining");
            } else {
                $(`#productionQuantityWarningText_${prodId}`).html("");
            }
            $(`.productionOrderBomItemTrList_${prodId}`).each(function() {
                let totalConsumptionPerUnit = parseFloat($(this).find(".totalConsumptionPerUnit").html());
                let totalConsumption = parseFloat($(this).find(".totalConsumption").html());
                let totalAvailableStock = parseFloat($(this).find(".totalAvailableStock").html());
                //$(this).find(".totalConsumption").html((totalConsumptionPerUnit * prodQuantity).toFixed(2));
                $(this).find(".totalConsumption").html((totalConsumptionPerUnit * prodQuantity));
            });
        });

        $(document).on("change", ".availableQuantity", function() {
            let randomRowNum = ($(this).attr("id")).split("_")[1];
            let storageLocationName = $(this).find(':selected').data('storagelocation');
            $(`#availableQuantityLocationName_${randomRowNum}`).val(storageLocationName);
            console.log(storageLocationName);
        });

        function getProductionOrderModalBomHtml(prodId = 0) {
            let productionOrderId = parseInt($(`#productionOrderDetailsModalBtn_${prodId}`).data("production-order-id"));
            let productionItemId = parseInt($(`#productionOrderDetailsModalBtn_${prodId}`).data("item-id"));
            let productionOrderMrpStatus = $(`#productionOrderDetailsModalBtn_${prodId}`).data("mrp-status");
            let productionOrderRemainQty = parseFloat($(`#productionOrderDetailsModalBtn_${prodId}`).data("remain-qty"));
            let productionOrderDeclareQty = parseFloat($(`#productionQuantity_${prodId}`).val());
            let productionOrderDeclareDate = $(`#productionDeclareDate_${prodId}`).val();

            if (productionOrderMrpStatus == "Created") {
                $.ajax({
                    type: "GET",
                    url: `<?= BASE_URL ?>branch/location/ajaxs/production/ajax-production-order-bom-item-and-stocks.php`,
                    data: {
                        productionOrderId,
                        productionItemId,
                        productionOrderMrpStatus,
                        productionOrderRemainQty,
                        productionOrderDeclareQty,
                        productionOrderDeclareDate
                    },
                    beforeSend: function() {
                        $(`#productionOrderMrpDetailsDiv_${productionOrderId}`).html("Getting Production Order MRP details...");
                    },
                    success: function(response) {
                        $(`#productionOrderMrpDetailsDiv_${productionOrderId}`).html(response);
                        console.log(response);
                    },
                    error: function(jqXHR, textStatus, errorTh) {
                        $(`#productionOrderMrpDetailsDiv_${productionOrderId}`).html("Something went wrong, please try again!");
                        console.log("Something went wrong, please try again!", textStatus, jqXHR.status, errorTh);
                    },
                    complete: function(jqXHR, textStatus, errorTh) {
                        console.log("Completed the production order mrp details api call", textStatus, jqXHR.status);
                    }
                });
            }
        }

        $(document).on("click", ".productionOrderDetailsModalBtn", function() {
            //alert(1);
            let prodId = ($(this).attr("id")).split("_")[1];
            //  alert(prodId);
            getProductionOrderModalBomHtml(prodId);
        });

        $(document).on("change", ".productionDeclareDate", function() {
            let prodId = ($(this).attr("id")).split("_")[1];
            getProductionOrderModalBomHtml(prodId);
        });

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
                }
            });
        });

    });
</script>