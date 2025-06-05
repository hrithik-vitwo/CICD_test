<style>
    .manage-production-modal .modal-body span.error {
        position: relative;
        display: block !important;
    }
</style>

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
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>branch/location/" class="text-dark"><i class="fas fa-home po-list-icon"></i>Home</a></li>
                <li class="breadcrumb-item active"><a href="<?= BASE_URL ?>branch/location/manage-production-order.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Production Order</a></li>
                <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-list po-list-icon"></i> List</a></li>
                <li class="back-button">
                    <a href="<?= BASE_URL ?>branch/location/manage-production-order.php">
                        <i class="fa fa-reply po-list-icon"></i>
                    </a>
                </li>
            </ol>

            <div class="filter-list">
              <a href="manage-production-order.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>All</a>
              <a href="manage-production-order.php?open" class="btn"><i class="fa fa-list mr-2"></i>Open</a>
              <a href="manage-production-order.php?released" class="btn"><i class="fa fa-clock mr-2"></i>Released</a>
              <a href="manage-production-order.php?closed" class="btn"><i class="fa fa-lock-open mr-2"></i>Closed</a>
            </div>
            

            <div class="row p-0 m-0">
                <?php
                $filteredOrderStatus = isset($_GET["orderStatus"]) ? $_GET["orderStatus"] : "all";
                $filteredGoodType = isset($_GET["goodType"]) ? $_GET["goodType"] : "all";
                $stockControllerObj = new StockController();
                $productionOrderlistObj = $productionOrderController->getProductionOrderList($filteredOrderStatus, $filteredGoodType);
                // console($productionOrderlistObj);
                //NEW BOM Controller
                $bomControllerObj = new BomController();
                ?>
                <div class="col-12 mt-2 p-0">
                    <div class="card card-tabs" style="border-radius: 20px;">
                        <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                            <div class="card-body">
                                <div class="row filter-serach-row">
                                    <div class="col-md-3">
                                        <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        <span>
                                            <span id="multipleMrpRunSpan"></span>
                                            <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary"><i class="fa fa-plus" style="margin-right: 0;"></i></a>
                                        </span>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="d-flex gap-2">
                                            <label class="text-nowrap">Production Type</label>
                                            <select name="" id="productionOrderListFilter_orderStatus" class="form-control productionOrderListFilter">
                                                <option value="all" <?= (isset($_GET["orderStatus"]) && $_GET["orderStatus"] == "all") ? "selected" : "" ?>>All</option>
                                                <option value="open" <?= (isset($_GET["orderStatus"]) && $_GET["orderStatus"] == "open") ? "selected" : "" ?>>Open</option>
                                                <option value="released" <?= (isset($_GET["orderStatus"]) && $_GET["orderStatus"] == "released") ? "selected" : "" ?>>Released</option>
                                            </select>
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
                                    </div>
                                    <div class="col-md-4">
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
                                                <div class="modal-body"></div>
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

                        <div class="row p-0 m-0" style="overflow: auto;">
                            <table class="table defaultDataTable table-hover">
                                <thead>
                                    <tr class="alert-light">
                                        <th>Sl</th>
                                        <th><input type="checkbox" name="selectAllMrpCheckBox"></th>
                                        <th>Production Order</th>
                                        <th>Item Type</th>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>Ref/SO</th>
                                        <th>Quantity</th>
                                        <th>Remain Qty</th>
                                        <th>Require Date</th>
                                        <th>Created Date</th>
                                        <th>Created By</th>
                                        <th>Release Status</th>
                                        <th>MRP Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($productionOrderlistObj["data"]) == 0) {
                                        echo '<tr class="alert-light"><td colspan="15" class="text-center">Prouduction order not found!</td</tr>';
                                    }
                                    $sl = 0;
                                    foreach ($productionOrderlistObj["data"] as $listRow) {
                                        $statusOpenVal = 9;
                                        $statusReleaseVal = 13;
                                        $statusCloseVal = 10;
                                        $releaseStatusName = ($listRow["status"] == $statusOpenVal) ? "Open" : (($listRow["status"] == $statusReleaseVal) ? "Release" : "Close");
                                        $releaseStatusCssClass =  ($listRow["status"] == $statusOpenVal) ? "warning" : (($listRow["status"] == $statusReleaseVal) ? "success" : "danger");

                                        $prodItemStockDetails = $stockControllerObj->getStockDeatils($listRow["itemId"]);
                                    ?>
                                        <tr>
                                            <td><?= $sl += 1 ?></td>
                                            <td>
                                                <?php
                                                if ($releaseStatusName == "Open") {
                                                    echo '<input class="multipleMrpCheckBox" type="checkbox" name="multipleMrp[]" value="' . $listRow["so_por_id"] . '">';
                                                }
                                                ?>
                                            </td>
                                            <td><?= $listRow["porCode"] ?></td>
                                            <td><?= $listRow["goodTypeName"] ?></td>
                                            <td><?= $listRow["itemCode"] ?></td>
                                            <td>
                                                <p class="pre-wrap"><?= $listRow["itemName"] ?></p>
                                            </td>
                                            <td><?= $listRow["refNo"] ?></td>
                                            <td><?= $listRow["qty"] ?></td>
                                            <td><?= $listRow["remainQty"] ?></td>
                                            <td><?= $listRow["expectedDate"] ?></td>
                                            <td><?= $listRow["created_at"] ?></td>
                                            <td><?= getCreatedByUser($listRow["created_by"]) ?></td>
                                            <td><span class="badge badge-<?= $releaseStatusCssClass ?> p-1"><?= ucfirst($releaseStatusName) ?></span></td>
                                            <td class="text-center"><?= ($listRow["mrp_status"] == "Not Created") ? "<i class='fas fa-clock po-list-icon mx-auto'></i>" : "<i class='fas fa-check text-success mx-auto'></i>" ?></td>
                                            <td>
                                                <a style="cursor:pointer" href="" class="btn btn-sm" data-toggle="modal" data-target="#productionOrderDetailsModal_<?= $listRow["so_por_id"] ?>"><i class="fa fa-eye po-list-icon"></i></a>
                                                <!-- Modal -->
                                                <div class="modal fade right manage-production-modal customer-modal" id="productionOrderDetailsModal_<?= $listRow["so_por_id"] ?>" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-right" role="document">
                                                        <form action="?consumption-preview" method="post" class="h-100">
                                                            <input type="hidden" name="soProdId" value="<?= $listRow["so_por_id"] ?>">
                                                            <input type="hidden" name="soProdCode" value="<?= $listRow["porCode"] ?>">
                                                            <input type="hidden" name="soProdCreatedDate" value="<?= explode(" ", $listRow["created_at"])[0] ?>">
                                                            <input type="hidden" name="itemCode" value="<?= $listRow["itemCode"] ?>">
                                                            <input type="hidden" name="itemId" value="<?= $listRow["itemId"] ?>">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title text-light" id="exampleModalLongTitle"><?= ucfirst($listRow["itemName"]) ?></h5>
                                                                    <div class="text-left">
                                                                        <i class="text-muted">Item Code:</i> <?= ucfirst($listRow["itemCode"]) ?><br>
                                                                        <?php
                                                                        if ($listRow["goodsType"] == 2) {
                                                                            echo '<i class="text-muted">SFG WH Open:</i> ' . $prodItemStockDetails["sfgStockOpen"];
                                                                        } else {
                                                                            echo '<i class="text-muted">FG WH Open:</i> ' . $prodItemStockDetails["rmWhOpen"];
                                                                        }
                                                                        ?>
                                                                        <p class="pre-wrap"><i class="text-muted">Description:</i> <?= ucfirst($listRow["itemDesc"]) ?></p>
                                                                        <i class="text-muted">MRP Status:</i> <?= $listRow["mrp_status"] ?> <?= strtolower($listRow["mrp_status"]) == "created" ? ' <a href="" style="cursor:pointer" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>' : "" ?>
                                                                        <?php
                                                                        if ($listRow["mrp_status"] == "Not Created") {
                                                                            // console($goodsBomController->isBomCreated($listRow["itemId"]));
                                                                            if (!$goodsBomController->isBomCreated($listRow["itemId"])) {
                                                                                echo "<br>BOM not created, please create a BOM to run MRP";
                                                                            } else {
                                                                        ?>
                                                                                <a href="manage-production-order.php?run-mrp=<?= base64_encode($listRow["so_por_id"]) ?>" class="btn btn-sm btn-warning text-light">RUN MRP</a>
                                                                        <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                        <i class="text-muted">Status:</i> <?= ucfirst($releaseStatusName) ?>
                                                                    </div>
                                                                    <div class="text-left">

                                                                    </div>

                                                                    <div class="display-flex-space-between mt-4 mb-3">
                                                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                            <li class="nav-item">
                                                                                <a class="nav-link active" id="home-tab<?= str_replace('/', '-', $listRow["porCode"]) ?>" data-toggle="tab" href="#home<?= str_replace('/', '-', $listRow["porCode"]) ?>">Info</a>
                                                                            </li>

                                                                            <!-- -------------------Audit History Button Start------------------------- -->
                                                                            <li class="nav-item">
                                                                                <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $listRow["porCode"]) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $listRow["porCode"]) ?>" href="#history<?= str_replace('/', '-', $listRow["porCode"]) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $listRow["porCode"]) ?>" aria-selected="false"><i class="fa fa-history mr-2"></i> Trail</a>
                                                                            </li>
                                                                            <!-- -------------------Audit History Button End------------------------- -->

                                                                            <li class="nav-item">
                                                                                <a class="nav-link treeTable" id="treeTable<?= str_replace('/', '-', $listRow["porCode"]) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $listRow["porCode"]) ?>" href="#treeTable<?= str_replace('/', '-', $listRow["porCode"]) ?>" role="tab" aria-controls="treeTable<?= str_replace('/', '-', $listRow["porCode"]) ?>" aria-selected="false"><i class="fa fa-history mr-2"></i> Tree Table </a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                                if ($listRow["mrp_status"] == "Created") {
                                                                ?>
                                                                    <div class="modal-body">
                                                                        <div class="tab-content" id="myTabContent">
                                                                            <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $listRow["porCode"]) ?>" role="tabpanel" aria-labelledby="home-tab">
                                                                                <div class="text-left">
                                                                                    <div class="row p-0 m-0">
                                                                                        <div class="col-md-3">Declare Date: <input type="date" name="productionDeclareDate" value="<?= date("Y-m-d") ?>" class="productionDeclareDate form-control" required></div>
                                                                                        <div class="col-md-3">Declare Quantity: <input type="number" min="1" max="<?= $listRow["remainQty"] ?>" id="productionQuantity_<?= $listRow["so_por_id"] ?>" name="productionQuantity" value="<?= $listRow["remainQty"] ?>" placeholder="eg. <?= $listRow["remainQty"] ?>" class="productionQuantity form-control" required></div>
                                                                                        <div class="col-md-3">Remain Quantity: <input type="number" value="<?= ($listRow["remainQty"]) ?>" class="form-control" id="remainingQty_<?= $listRow["so_por_id"] ?>" disabled></div>
                                                                                        <div class="col-md-3">Order Quantity: <input type="number" value="<?= ($listRow["qty"]) ?>" class="form-control" disabled></div>
                                                                                    </div>
                                                                                    <!-- <p>Product Id : <?= $listRow["itemId"] ?></p> -->
                                                                                    <hr>
                                                                                    <?php
                                                                                    $bomDetailObj = $bomControllerObj->getBomDetails($listRow["itemId"]);
                                                                                    // console($bomDetailObj);
                                                                                    ?>
                                                                                    <p class="text-left m-0">Bill Of Material</p>
                                                                                    <div class="card">
                                                                                        <div class="card-body p-2" style="overflow-x : auto">
                                                                                            <div class="">
                                                                                                <div class="card">
                                                                                                    <div class="card-body">
                                                                                                        <p class="text-left m-0 pl-3 pb-2 font-bold">Items</p>
                                                                                                        <table class="table">
                                                                                                            <thead>
                                                                                                                <tr>
                                                                                                                    <th class="borderNone">Item Code</th>
                                                                                                                    <th class="borderNone">Item Title</th>
                                                                                                                    <th class="borderNone">Consumption/Unit</th>
                                                                                                                    <th class="borderNone">Total Consumption</th>
                                                                                                                    <th class="borderNone">Available Stock</th>
                                                                                                                    <th class="borderNone">UOM</th>
                                                                                                                    <th class="borderNone">Method</th>
                                                                                                                </tr>
                                                                                                            </thead>
                                                                                                            <tbody>
                                                                                                                <?php
                                                                                                                foreach ($bomDetailObj["data"]["bom_material_data"] ?? [] as $bomOneItem) {
                                                                                                                    // rmProdOpen
                                                                                                                    $stockLogObj = itemQtyStockChecking($bomOneItem["item_id"], "'rmProdOpen'", ($bomOneItem["item_sell_type"] == "FIFO" ? "ASC" : "DESC"));
                                                                                                                    // console($stockLogObj);
                                                                                                                    $itemAvailableStocks = $stockLogObj['sumOfBatches'];
                                                                                                                ?>
                                                                                                                    <tr>
                                                                                                                        <td><?= $bomOneItem["itemCode"] ?? "" ?></td>
                                                                                                                        <td><?= $bomOneItem["itemName"] ?? "" ?></td>
                                                                                                                        <td><span class="totalConsumptionPerUnit"><?= $bomOneItem["totalConsumption"] ?></span> (<?= $bomOneItem["consumption"] . " + " . $bomOneItem["extra"] ?>%)</td>
                                                                                                                        <td><span class="totalConsumption"><?= $bomOneItem["totalConsumption"] * $listRow["remainQty"] ?></span></td>
                                                                                                                        <td><span class="totalAvailableStock"><?= $itemAvailableStocks ?></span></td>
                                                                                                                        <td><?= $bomOneItem["uom"] ?></td>
                                                                                                                        <td><?= $bomOneItem["item_sell_type"] ?></td>
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
                                                                                                                    <th class="borderNone">Cost Center</th>
                                                                                                                    <th class="borderNone">Code</th>
                                                                                                                    <th class="borderNone">Head Name</th>
                                                                                                                    <th class="borderNone">Consumption</th>
                                                                                                                    <th class="borderNone">Extra(%)</th>
                                                                                                                    <th class="borderNone">UOM</th>
                                                                                                                </tr>
                                                                                                            </thead>
                                                                                                            <tbody>
                                                                                                                <?php
                                                                                                                $sl = 0;
                                                                                                                foreach ($bomDetailObj["data"]["bom_hd_data"] ?? [] as $bomOneItem) {
                                                                                                                ?>
                                                                                                                    <tr>
                                                                                                                        <td><?= $sl += 1 ?></td>
                                                                                                                        <td><?= $bomOneItem["CostCenter_desc"] ?></td>
                                                                                                                        <td><?= $bomOneItem["CostCenter_code"] ?></td>
                                                                                                                        <td><?= strtoupper($bomOneItem["head_type"]) ?></td>
                                                                                                                        <td><?= $bomOneItem["consumption"] ?></td>
                                                                                                                        <td><?= $bomOneItem["extra"] ?></td>
                                                                                                                        <td><?= $bomOneItem["uom"] ?></td>
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
                                                                                                                </tr>
                                                                                                            </thead>
                                                                                                            <tbody>
                                                                                                                <?php
                                                                                                                $sl = 0;
                                                                                                                foreach ($bomDetailObj["data"]["bom_other_head_data"] ?? [] as $bomOneItem) {
                                                                                                                ?>
                                                                                                                    <tr>
                                                                                                                        <td><?= $sl += 1 ?></td>
                                                                                                                        <td><?= $bomOneItem["CostCenter_desc"] ?></td>
                                                                                                                        <td><?= $bomOneItem["CostCenter_code"] ?></td>
                                                                                                                        <td><?= ucfirst($bomOneItem["head_name"] ?? "") ?></td>
                                                                                                                        <td><?= $bomOneItem["consumption"] ?></td>
                                                                                                                        <td><?= $bomOneItem["extra"] ?></td>
                                                                                                                        <td><?= $bomOneItem["uom"] ?></td>
                                                                                                                    </tr>
                                                                                                                <?php
                                                                                                                }
                                                                                                                ?>
                                                                                                            </tbody>
                                                                                                        </table>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <?php
                                                                                                if ($bomDetails["bomProgressStatus"] == "COGM") {
                                                                                                ?>
                                                                                                    <div class="card">
                                                                                                        <div class="card-body">
                                                                                                            <p class="text-left m-0 pl-3 pb-2 font-bold">Other Addons</p>
                                                                                                            <form action="" method="post">
                                                                                                                <input type="hidden" name="bomId" value="<?= $bomDetails["bomId"] ?>">
                                                                                                                <table class="table mb-3">
                                                                                                                    <thead>
                                                                                                                        <tr>
                                                                                                                            <th class="borderNone">Others</th>
                                                                                                                            <th class="borderNone">Select Gl</th>
                                                                                                                            <th class="borderNone">Amount</th>
                                                                                                                            <th class="borderNone">Remarks</th>
                                                                                                                            <th class="borderNone">Action</th>
                                                                                                                        </tr>
                                                                                                                    </thead>
                                                                                                                    <tbody id="otherAddonsForm">
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                                <button type="submit" name="addCOGSFormSubmitBtn" class="btn btn-sm btn-primary text-xs mb-4 mr-3 float-right" value="Create COGS">Create COGS</button>
                                                                                                            </form>

                                                                                                        </div>
                                                                                                    </div>
                                                                                                <?php
                                                                                                } elseif ($bomDetails["bomProgressStatus"] == "COGS") {
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
                                                                                                                    foreach ($bomItemsList as $bomOneItem) {
                                                                                                                        if ($bomOneItem["bomItemType"] == "othersCogs") {
                                                                                                                            // goods other item list
                                                                                                                    ?>
                                                                                                                            <tr>
                                                                                                                                <td><?= $bomOneItem["othersItem"] ?></td>
                                                                                                                                <td><?= $bomOneItem["itemGl"] ?></td>
                                                                                                                                <td><?= $bomOneItem["amount"] ?></td>
                                                                                                                                <td><?= $bomOneItem["remarks"] ?></td>
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
                                                                                                                        <th class="bomMargin">Margin Amount</th>
                                                                                                                    </tr>
                                                                                                                </thead>
                                                                                                                <tbody>
                                                                                                                    <tr>
                                                                                                                        <td><input type="text" class="form-control mt-2 mb-2" name="bomDiscount" placeholder="Bom Discount" /></td>
                                                                                                                        <td><input step="0.01" type="number" class="form-control mt-2 mb-2" name="bomMargin" placeholder="Bom margings" /></td>
                                                                                                                    </tr>
                                                                                                                </tbody>
                                                                                                            </table>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                <?php
                                                                                                } ?>

                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="card-footer p-2">
                                                                                            <button id="consumptionPostingCancel_<?= $listRow["so_por_id"] ?>" type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                                            <button id="consumptionPosting_<?= $listRow["so_por_id"] ?>" type="submit" name="consumptionPosting" class="btn btn-primary">Post</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                            <div class="tab-pane fade" id="history<?= str_replace('/', '-', $listRow["porCode"]) ?>" role="tabpanel" aria-labelledby="history-tab">

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
                                                                        <div class="modal-footer">
                                                                        </div>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                        </div>
                                                                    <?php
                                                                }
                                                                    ?>
                                                                    </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<script>
    $(document).ready(function() {
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
            prodQuantity = prodQuantity > 0 ? prodQuantity : 0;
            // console.log("prodId",prodId);
            // console.log("prodQuantity",prodQuantity);
            $(`.tdConsumptionRate_${prodId}`).each(function() {
                let tdRowNum = ($(this).attr("id")).split("_")[2];
                let totalConsumptionRate = parseFloat($(this).html());
                totalConsumptionRate = totalConsumptionRate > 0 ? totalConsumptionRate : 0;

                let requiredConsumption = totalConsumptionRate * prodQuantity;
                $(`#tdRequireConsumption_${prodId}_${tdRowNum}`).html(requiredConsumption.toFixed(2));
                $(`#inputRequireConsumption_${prodId}_${tdRowNum}`).val(requiredConsumption.toFixed(2));
                //console.log($(this).html());
                //console.log("tdRowNum: tdConsumptionRate_4_",tdRowNum);                
            });
        });
        $(document).on("change", ".availableQuantity", function() {
            let randomRowNum = ($(this).attr("id")).split("_")[1];
            let storageLocationName = $(this).find(':selected').data('storagelocation');
            $(`#availableQuantityLocationName_${randomRowNum}`).val(storageLocationName);
            console.log(storageLocationName);
        });
    });
</script>
<!-- <script src="<?= BASE_URL; ?>public/validations/prodOrderListValidation.js"></script> -->