<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row p-0 m-0">
                <?php

                $productionOrderlistObj = $productionOrderController->getProductionOrderList();
                //console($productionOrderlistObj);

                ?>
                <div class="col-12 mt-2 p-0">
                    <div class="p-0 pt-1 my-2">
                        <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                            <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                <h3 class="card-title">Manage Production Order</h3>
                                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>" class="btn btn-sm btn-primary"><i class="fa fa-plus" style="margin-right: 0;"></i></a>
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
                                    <th class="borderNone">Sl</th>
                                    <th class="borderNone">Production Order</th>
                                    <th class="borderNone">Item</th>
                                    <!--item code and item name in tooltip -->
                                    <th class="borderNone">Ref/SO</th>
                                    <th class="borderNone">Quantity</th>
                                    <th class="borderNone">Require Date</th>
                                    <th class="borderNone">Created Date</th>
                                    <th class="borderNone">Created By</th>
                                    <th class="borderNone">Release Status</th>
                                    <th class="borderNone">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sl = 0;
                                foreach ($productionOrderlistObj["data"] as $listRow) {
                                    $releaseStatusCssClass =  ($listRow["status"] == "open") ? "warning" : (($listRow["status"] == "released") ? "success" : "danger");
                                ?>
                                    <tr>
                                        <td><?= $sl += 1 ?></td>
                                        <td><?= $listRow["porCode"] ?></td>
                                        <td><?= $listRow["itemCode"] ?></td>
                                        <td><?= $listRow["refNo"] ?></td>
                                        <td><?= $listRow["qty"] ?></td>
                                        <td><?= $listRow["expectedDate"] ?></td>
                                        <td><?= $listRow["created_at"] ?></td>
                                        <td><?= $listRow["created_by"] ?></td>
                                        <td><span class="badge badge-<?= $releaseStatusCssClass ?>"><?= ucfirst($listRow["status"]) ?></span></td>
                                        <td>

                                            <a style="cursor:pointer" href="" class="btn btn-sm" data-toggle="modal" data-target="#productionOrderDetailsModal_<?= $listRow["so_por_id"] ?>"><i class="fa fa-eye po-list-icon"></i></a>
                                            <!-- Modal -->
                                            <div class="modal fade right customer-modal" id="productionOrderDetailsModal_<?= $listRow["so_por_id"] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-right" role="document">

                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLongTitle"><?= ucfirst($listRow["itemName"]) ?></h5>
                                                            <div class="text-left">
                                                                Item Code: <?= ucfirst($listRow["itemCode"]) ?><br>
                                                                Open Stocks: <?= ucfirst($listRow["itemOpenStocks"]) ?><br>
                                                                Block Stocks: <?= ucfirst($listRow["itemBlockStocks"]) ?><br>
                                                                Description: <?= ucfirst($listRow["itemDesc"]) ?>
                                                            </div>
                                                        </div>

                                                        <div class="modal-body p-3 pb-0">
                                                            <div class="text-left">
                                                                Required Quantity: <?= ($listRow["qty"]) ?><br>
                                                                Production Quantity: <input type="number" min="1" id="productionQuantity_<?= $listRow["so_por_id"] ?>" name="productionQuantity" value="<?= $listRow["qty"] ?>" placeholder="eg. <?= $listRow["qty"] ?>" class="productionQuantity" required>
                                                                <hr>
                                                                <?php
                                                                // $bomItemsObj = $goodsBomController->getBomItemsAndDetails($oneBomRow["bomId"]);

                                                                $bomItemsObj = $goodsBomController->getBomAndItemDetails($listRow["itemId"]);
                                                                //console($bomItemsObj);
                                                                ?>
                                                                <p class="text-left m-0">Items</p>
                                                                <table class="table" id="bomItemTable_<?= $listRow["so_por_id"] ?>">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="borderNone">Item Code</th>
                                                                            <th class="borderNone">Consumption</th>
                                                                            <th class="borderNone">UOM</th>
                                                                            <th class="borderNone">Available</th>
                                                                            <th class="borderNone">Required</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        foreach ($bomItemsObj["data"]["bomItemDetails"] as $bomOneItem) {
                                                                            $consumption=$bomOneItem["itemConsumption"];
                                                                            $extraConsumtion = $bomOneItem["itemExtraPurchage"];
                                                                            $totalConsumption = $consumption + ($consumption*$extraConsumtion/100);
                                                                            $requiredConsumption = $totalConsumption*$listRow["qty"];

                                                                            $randomRowNum = time().rand(1000,9999);
                                                                            ?>
                                                                            <tr>
                                                                                <td><?= $bomOneItem["itemCode"] ?></td>
                                                                                <td class="tdConsumptionRate_<?= $listRow["so_por_id"] ?>" id="tdConsumptionRate_<?= $listRow["so_por_id"]."_".$randomRowNum ?>"><?= $totalConsumption ?></td>
                                                                                <td><?= $bomOneItem["itemUOM"] ?></td>
                                                                                <td><?= $bomOneItem["itemOpenStocks"] ?></td>
                                                                                <td class="tdRequireConsumption_<?= $listRow["so_por_id"] ?>" id="tdRequireConsumption_<?= $listRow["so_por_id"]."_".$randomRowNum ?>"><?= $requiredConsumption ?></td>
                                                                            </tr>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>

                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            <button type="button" class="btn btn-primary">Save changes</button>
                                                        </div>
                                                    </div>
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
    </section>
</div>


<script>
    $(document).ready(function(){
        $(document).on("keyup",".productionQuantity", function(){
            let prodId = ($(this).attr("id")).split("_")[1];
            let prodQuantity = parseFloat($(this).val());
            prodQuantity = prodQuantity>0 ? prodQuantity : 0;

            // console.log("prodId",prodId);
            // console.log("prodQuantity",prodQuantity);
            $(`.tdConsumptionRate_${prodId}`).each(function() {
                let tdRowNum = ($(this).attr("id")).split("_")[2];
                let totalConsumptionRate = parseFloat($(this).html());
                totalConsumptionRate = totalConsumptionRate>0 ? totalConsumptionRate : 0;

                let requiredConsumption = totalConsumptionRate*prodQuantity;
                $(`#tdRequireConsumption_${prodId}_${tdRowNum}`).html(requiredConsumption.toFixed(2));
                //console.log($(this).html());
                //console.log("tdRowNum: tdConsumptionRate_4_",tdRowNum);                
            });
        });
    });
</script>