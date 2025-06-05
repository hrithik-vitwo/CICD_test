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
            <div class="row p-0 m-0">
                <?php
                $filteredOrderStatus = 'open';
                $filteredGoodType = isset($_GET["goodType"]) ? $_GET["goodType"] : "all";
                $stockControllerObj = new StockController();
                $productionOrderlistObj = $productionOrderController->getProductionOrderList($filteredOrderStatus, $filteredGoodType);
                // console($productionOrderlistObj);
                //NEW BOM Controller
                $bomControllerObj = new BomController();
                ?>
                <div class="col-12 mt-2 p-0">
                    <div class="p-0 pt-1 my-2">
                        <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                            <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                <h3 class="card-title">Manage Production Order</h3>
                                <!-- <h3 class="card-title">Manage Production Order (Stage: TEST)</h3> -->
                                <div class="filter-list">
                                    <a href="manage-production-order.php" class="btn"><i class="fa fa-stream mr-2"></i>All</a>
                                    <a href="manage-production-order.php?open" class="btn active"><i class="fa fa-list mr-2 active"></i>Open</a>
                                    <a href="manage-production-order.php?released" class="btn"><i class="fa fa-clock mr-2"></i>Released</a>
                                    <a href="manage-production-order.php?closed" class="btn"><i class="fa fa-lock-open mr-2"></i>Closed</a>
                                </div>

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
                                <span>
                                    <span id="multipleMrpRunSpan"></span>
                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary"><i class="fa fa-plus" style="margin-right: 0;"></i></a>
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div class="card card-tabs" style="border-radius: 20px;">
                        <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                            <div class="card-body overflow-hidden">
                                <div class="row filter-serach-row">
                                    <div class="col-lg-1 col-md-1 col-sm-12">
                                        <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-lg-11 col-md-11 col-sm-12">
                                        <div class="row table-header-item">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
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
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 px-0">
                                        <div class="tab-content" id="custom-tabs-two-tabContent">
                                            <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
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
                                                            <th>Validity Period</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
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

                                            <?php

                                            if ($listRow['validityperiod'] != '') {
                                                $date1 = new DateTime($listRow['validityperiod']);
                                                $date2 = new DateTime(date('Y-m-d'));

                                                $interval = $date1->diff($date2);
                                                $countdays = $interval->format('%a');
                                                $day = "";
                                                if ($countdays > 1) {
                                                    $day = "days";
                                                } else {
                                                    $day = "day";
                                                }


                                                if ($listRow['validityperiod'] < date('Y-m-d')) {
                                                    echo "expired";
                                                } else {
                                                echo $countdays . " " . $day." Remaining";

                                                }
                                            } else {
                                                echo '-';
                                            }

                                            ?>

                                        </td>

                                                                <td>
                                                                    <a style="cursor:pointer" href="" class="btn btn-sm productionOrderDetailsModalBtn" id="productionOrderDetailsModalBtn_<?= $listRow["so_por_id"] ?>" data-production-order-id="<?= $listRow["so_por_id"] ?>" data-item-id="<?= $listRow["itemId"] ?>" data-mrp-status="<?= $listRow["mrp_status"] ?>" data-remain-qty="<?= $listRow["remainQty"] ?>" data-toggle="modal" data-target="#productionOrderDetailsModal_<?= $listRow["so_por_id"] ?>"><i class="fa fa-eye po-list-icon"></i></a>
                                                                    <!-- Modal -->
                                                                    <div class="modal fade right manage-production-modal production-status-view-modal customer-modal" id="productionOrderDetailsModal_<?= $listRow["so_por_id"] ?>" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-right" role="document">
                                                                            <form action="?consumption-preview" method="post" class="h-100">
                                                                                <input type="hidden" name="soProdId" value="<?= $listRow["so_por_id"] ?>">
                                                                                <input type="hidden" name="soProdCode" value="<?= $listRow["porCode"] ?>">
                                                                                <input type="hidden" name="soProdCreatedDate" value="<?= explode(" ", $listRow["created_at"])[0] ?>">
                                                                                <input type="hidden" name="itemCode" value="<?= $listRow["itemCode"] ?>">
                                                                                <input type="hidden" name="itemId" value="<?= $listRow["itemId"] ?>">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">

                                                                                    <div class="details-input">
                                                                                            <label for="">Name : </label>
                                                                                            <p class="item-details item-name" title="<?= ucfirst($listRow["itemName"]) ?>"><?= ucfirst($listRow["itemName"]) ?></p>
                                                                                        </div>
                                                                                        <div class="details-input">
                                                                                            <label for="">Code : </label>
                                                                                            <p class="item-details item-code"><?= ucfirst($listRow["itemCode"]) ?></p>
                                                                                        </div>
                                                                                        <div class="details-input">
                                                                                            <?php
                                                                                            if ($listRow["goodsType"] == 2) {
                                                                                                echo '<label for="">Current Stock : </label>';
                                                                                                echo '<p>'.$prodItemStockDetails["sfgStockOpen"].'</p>';
                                                                                            } else {
                                                                                                echo '<label for="">Current Stock : </label>';
                                                                                                echo '<p>'.$prodItemStockDetails["rmWhOpen"].'</p>';
                                                                                            }
                                                                                            ?>
                                                                                        </div>
                                                                                        <div class="details-input">
                                                                                            <label for="">Description : </label>
                                                                                            <p class="item-details item-desc" title="<?= ucfirst($listRow["itemDesc"]) ?>"><?= ucfirst($listRow["itemDesc"]) ?></p>
                                                                                        </div>

                                                                                        <div class="details-input">
                                                                                            <label for="">MRP Status : </label>
                                                                                            <p class="item-details item-mrp"><?= $listRow["mrp_status"] ?> <?= strtolower($listRow["mrp_status"]) == "created" ? ' <a href="" style="cursor:pointer" class="btn btn-sm mrp-btn"><ion-icon name="eye-outline"></ion-icon></a>' : "" ?></p>
                                                                                            <?php
                                                                                            if ($listRow["mrp_status"] == "Not Created") {
                                                                                                // console($goodsBomController->isBomCreated($listRow["itemId"]));
                                                                                                if (!$goodsBomController->isBomCreated($listRow["itemId"])) {
                                                                                                    echo "<br>BOM not created, please create a BOM to run MRP";
                                                                                                } else {
                                                                                            ?>
                                                                                                    <a href="manage-production-order.php?run-mrp=<?= base64_encode($listRow["so_por_id"]) ?>" class="btn btn-sm mrp-btn btn-warning text-light">RUN MRP</a>
                                                                                            <?php
                                                                                                }
                                                                                            }
                                                                                            ?>
                                                                                        </div>


                                                                                        <div class="details-input">
                                                                                            <label for="">Status : </label>
                                                                                            <p class="item-details item-mrp"><?= ucfirst($releaseStatusName) ?></p>
                                                                                        </div>


                                                                                        <!-- <h5 class="modal-title text-light" id="exampleModalLongTitle"><?= ucfirst($listRow["itemName"]) ?></h5>
                                                                                        <div class="text-left">
                                                                                            <i class="text-muted">Item Code:</i> <?= ucfirst($listRow["itemCode"]) ?><br>
                                                                                            <?php
                                                                                            if ($listRow["goodsType"] == 2) {
                                                                                                echo '<i class="text-muted">Current Stock:</i> ' . $prodItemStockDetails["sfgStockOpen"];
                                                                                            } else {
                                                                                                echo '<i class="text-muted">Current Stock:</i> ' . $prodItemStockDetails["rmWhOpen"];
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
                                                                                        </div>
                                                                                        <div class="text-left">

                                                                                        </div> -->

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
                                                                                                            <div class="col-md-3">Declare Date: <input type="date" name="productionDeclareDate" value="<?= date("Y-m-d") ?>" class="productionDeclareDate form-control" id="productionDeclareDate_<?= $listRow["so_por_id"] ?>" required></div>
                                                                                                            <div class="col-md-3">Declare Quantity: <input type="number" min="1" max="<?= $listRow["remainQty"] ?>" id="productionQuantity_<?= $listRow["so_por_id"] ?>" name="productionQuantity" value="<?= $listRow["remainQty"] ?>" placeholder="eg. <?= $listRow["remainQty"] ?>" class="productionQuantity form-control" required> <small class="text-danger" id="productionQuantityWarningText_<?= $listRow["so_por_id"] ?>"></small></div>
                                                                                                            <div class="col-md-3">Remain Quantity: <input type="number" value="<?= ($listRow["remainQty"]) ?>" class="form-control" id="remainingQty_<?= $listRow["so_por_id"] ?>" disabled></div>
                                                                                                            <div class="col-md-3">Order Quantity: <input type="number" value="<?= ($listRow["qty"]) ?>" class="form-control" disabled></div>
                                                                                                        </div>
                                                                                                        <hr>
                                                                                                        <div id="productionOrderMrpDetailsDiv_<?= $listRow["so_por_id"] ?>">
                                                                                                            <!-- Data will be coming from the api -->
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
                                                        if ($sl == 0) {
                                                            echo '<tr class="alert-light"><td colspan="15" class="text-center">Prouduction order not found!</td</tr>';
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
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
                $(this).find(".totalConsumption").html((totalConsumptionPerUnit * prodQuantity).toFixed(2));
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
                        // console.log(response);
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
            let prodId = ($(this).attr("id")).split("_")[1];
            getProductionOrderModalBomHtml(prodId);
        });

        $(document).on("change", ".productionDeclareDate", function() {
            let prodId = ($(this).attr("id")).split("_")[1];
            getProductionOrderModalBomHtml(prodId);
        });


    });
</script>