<?php
$itemId = base64_decode($_GET["create"]);
// getWorkCenter();
//getGoodActivities();
$getWc = queryGet("SELECT * FROM `erp_work_center` WHERE `company_id` = $company_id",true);
// console($getWc);

?>

<style>
    .select-bom-modal .modal-body {
        height: 500px;
        overflow: auto;
    }
    .select-bom-modal {
        overflow: auto !important;
    }
</style>

<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>branch/location/bom.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>BOM</a></li>
    <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create</a></li>
    <li class="back-button">
        <a href="<?= $_SERVER["PHP_SELF"] ?>">
            <i class="fa fa-reply po-list-icon"></i>
        </a>
    </li>
</ol>
<div class="col-lg-4 col-md-4 col-sm-4">
    <div class="form-inline gap-2 pl-2 my-2">
        <label for="">COPY BOM</label>
        <button type="button" class="btn btn-primary change-address-modal text-xs btn-sm" data-toggle="modal" data-target="#select-pr">COPY BOM</button>
    </div>
</div>
<!-- All massages, logics, consoles  -->
<div class="modal select-bom-modal fade" id="select-pr" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header py-1">
                <h5 class="modal-title">BOM</h5>
                <button type="button" id="mapInvoiceItemCodeModalCloseBtn" class="close" data-dismiss="modal">&times;</button>
            </div>
            <?php
            $item_sql = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` as item, `erp_inventory_stocks_summary` as summary WHERE 1 AND item.itemId = summary.itemId  AND item.company_id='" . $company_id . "' AND  summary.`bomStatus`= 2 ORDER BY summary.`stockSummaryId` DESC LIMIT 10";


            $item_get = queryGet($item_sql, true);
           // echo $item_get['numRows'];
            $item_data = $item_get['data'];
            ?>
            <form id="pr_form">
                <div class="modal-body">
                    <table class="table-sales-order table defaultDataTable grn-table">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($item_data as $oneItemList) {
                               // console($item_data);
                                $rand = rand(10, 1000);
                            ?>
                                <tr>
                                    <td><input type="radio" name="copy" value="<?= base64_encode($oneItemList['itemId'] . "," . $itemId) ?>" id="itemId" class="form itemId"></td>
                                    <td><?= $oneItemList['itemCode'] ?></td>
                                    <td><?= $oneItemList['itemName']  ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button id="pr_form" class="btn btn-primary float-right mt-3">Select BOM Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="row m-0 p-0 messages justify-content-end">
    <?php

    $goodsDetailsObj = $goodsController->getGoodsDeatils($itemId);
    $goodTitle = $goodsDetailsObj["data"]["itemName"] ?? "";
    $goodCode = $goodsDetailsObj["data"]["itemCode"] ?? "";
    $expenseGlListObj = getAllChartOfAccounts_list_by_p($company_id, 4);

    // $getUomDetail= getUomDetail($uomId);
    $getUomListObj = getUomList('material');
    $getWcListObj = getWcList();
    //console($getWcListObj);


    include_once("controller/bom.controller.php");
    $bomControllerObj = new BomController();
    $goodMasterList = $bomControllerObj->getGoodMasterList();
    if (isset($_POST["createBomSubmitBtn"])) {
        // console($_POST);
        $createObj = $bomControllerObj->createBom($_POST);
        // console($createObj);
        if ($createObj["status"] == "success") {
            swalAlert($createObj["status"], ucfirst($createObj["status"]), $createObj["message"], LOCATION_URL . "bom.php");
        } else {
            swalAlert($createObj["status"], ucfirst($createObj["status"]), $createObj["message"]);
        }
    }
    ?>
</div>
<!-- /.All massages, logics, consoles -->

<div class="container-fluid">
    <div class="card p-0 boq-form-card bg-transparent boq-section">
        <h5 class="card-header p-2 text-sm text-light">Create Bill of Material</h5>
        <div class="card-body p-2">
            <form action="" method="post">
                <div class="row m-0 p-0">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Prepared By</label>
                                <input type="hidden" name="itemId" value="<?= $itemId ?>">
                                <input type="text" name="preparedBy" value="<?= $_SESSION["logedBranchAdminInfo"]["adminName"] ?? ""; ?>" placeholder="Created by" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Prepared Date</label>
                                <input type="date" value="<?= date("Y-m-d"); ?>" name="preparedDate" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Product Title</label>
                                <input type="text" name="itemTitle" value="<?= $goodTitle ?>" placeholder="Product title" class="form-control" readonly>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Product Code</label>
                                <input type="text" name="itemCode" value="<?= $goodCode ?>" placeholder="Product Code" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label>Work Center</label>
                                <select name="workCenter" id="workCenterDropDown" class="form-control" required>
                                    <option value="">Select Work Center</option>
                                    <?php
                                    foreach ($getWcListObj['data'] as $wc) {
                                    ?>
                                        <option value="<?= $wc['work_center_id'] ?>"><?= $wc['work_center_name'] . '(' . $wc['work_center_code'] . ')' ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="left-divider" style="border-left: 1px solid #00000033; padding-left: 1.2em;">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="text-muted">Total Material Cost</label>
                                    <p class="h4 font-weight-bold" id="grandMaterialCost"><?= decimalValuePreview(0)?></p>
                                    <input type="hidden" name="grandMaterialCost" value="<?= decimalValuePreview(0)?>" id="grandMaterialCostInput">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted">Total Hourly Deployment Cost</label>
                                    <p class="h4 font-weight-bold" id="grandHourlyDeploymentCost"><?= decimalValuePreview(0)?></p>
                                    <input type="hidden" name="grandHourlyDeploymentCost" value="<?= decimalValuePreview(0)?>" id="grandHourlyDeploymentCostInput">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label class="text-muted">Total Over Head Cost</label>
                                    <p class="h4 font-weight-bold" id="grandOtherHeadCost"><?= decimalValuePreview(0)?></p>
                                    <input type="hidden" name="grandOtherHeadCost" value="<?= decimalValuePreview(0)?>" id="grandOtherHeadCostInput">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted">Total Cost</label>
                                    <p class="h4 font-weight-bold" id="grandTotalBomCost"><?= decimalValuePreview(0)?></p>
                                    <input type="hidden" step="any" name="grandTotalBomCost" value="<?= decimalValuePreview(0)?>" id="grandTotalBomCostInput">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-sm font-bold py-3 pl-3 border-bottom">Materials</h5>
                        <hr>
                        <table>
                            <thead>
                                <th>Item Title <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small></th>
                                <th>Item Code <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small></th>
                                <th>Type <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small></th>
                                <th>UOM <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Unit of measurement e.g kg, pc, pkt, etc."></i></small></th>
                                <th>Consumption Quantity <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="No of Items"></i></small></th>
                                <th>Extra(%) <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Extra purchages"></i></small></th>
                                <th>Rate <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="One Item rate(price)"></i></small></th>
                                <th>Amount <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total amount"></i></small></th>
                                <th>Remarks <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small></th>
                                <th class="text-center"><i class="fa fa-plus bg-success rounded p-1 addBoqMaterialsDivItemBtn" style="cursor: pointer;"></i></th>
                            </thead>
                            <tbody id="bomMaterialDiv">
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body">
                        <div class="row pl-3 m-0 p-0 mt-2">Activity</div>
                        <h5 class="text-sm font-bold py-3 pl-3 border-bottom">Hourly Deployment</h5>
                        <hr>
                        <table>
                            <tbody>
                                <th>Work Center<small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Work Center Name"></i></small></th>
                                <th>Code <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Work center code"></i></small></th>
                                <th>Hourly Deployment Type <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Hourly deployment type(LHR or MHR)"></i></small></th>
                                <th>UOM <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Unit of measurement e.g LHR, MHR."></i></small></th>
                                <th>Hour <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Consumption"></i></small></th>
                                <th>Extra(%) <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Extra consumption"></i></small></th>
                                <th>Rate <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="One qty rate(price)"></i></small></th>
                                <th>Amount <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total amount"></i></small></th>
                                <th>Remarks <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small></th>
                                <th class="text-center"><i class="fa fa-plus bg-success rounded p-1 addBoqHdDivItemBtn" style="cursor: pointer;"></i></th>
                            </tbody>
                            <tbody id="bomHourlyDeploymentDiv">
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body">
                        <h5 class="text-sm font-bold py-3 pl-3 border-bottom">Over Head</h5>
                        <hr>
                        <table>
                            <tbody>
                                <th>Work Center<small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Work Center Name"></i></small></th>
                                <th>Code <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Work center code"></i></small></th>
                                <th>Over Head <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Over Head deatils"></i></small></th>
                                <th>UOM <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Unit of measurement e.g LHR, MHR."></i></small></th>
                                <th>Consumption Quantity <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Consumption"></i></small></th>
                                <th>Extra(%) <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Extra consumption"></i></small></th>
                                <th>Rate <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="One qty rate(price)"></i></small></th>
                                <th>Amount <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total amount"></i></small></th>
                                <th>Remarks <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small></th>
                                <th class="text-center"><i class="fa fa-plus bg-success rounded p-1 addBoqOtherHeadDivItemBtn" style="cursor: pointer;"></i></th>
                            </tbody>
                            <tbody id="bomOtherHeadDiv">
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer m-0 p-0 text-right">
                        <button type="submit" value="Save" name="createBomSubmitBtn" class="btn btn-primary text-light my-3">Save BOM</button>
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btn-danger">Back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Add New Over Head Modal -->
    <div class="modal fade new-over-modal" id="addBomOtherHeadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create New Activity Over Head</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" class="p-0 m-0" id="frmAddOtherExpenseHeadForm">
                    <div class="modal-body">
                        <input type="hidden" name="frmOtherHeadType" value="1">
                        <div class="form-input">
                            <label for="">Head Code</label>
                            <input type="text" name="frmOtherHeadCode" placeholder="Code" class="form-control" required>
                        </div>

                        <div class="form-input">
                            <label for="">Name</label>
                            <input type="text" name="frmOtherHeadName" placeholder="Name" class="form-control" required>
                        </div>

                        <div class="form-input connect-gl">
                            <label for="">Connect Gl <small>(Optional, it will use for analytics)</small></label>
                            <select name="frmOtherHeadGl" class="form-control" id="frmOtherHeadGlDropDown">
                                <option value="">Select Gl</option>
                                <?php
                                foreach ($expenseGlListObj["data"] as $row) {
                                ?>
                                    <option value="<?= $row["id"] ?>"><?= $row["gl_label"] ?> - <?= $row["gl_code"] ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <div class="form-input" style="width:48%">
                                <label for="">Rate</label>
                                <input type="number" step="any" name="frmOtherHeadRate" placeholder="Rate" class="form-control" required>
                            </div>
                            <div class="form-input">
                                <label for=""></label>
                                <p>/</p>
                            </div>
                            <div class="form-input" style="width:48%">
                                <label for="">UOM</label>
                                <select name="frmOtherHeadUom" id="frmOtherHeadUomDropDown" class="form-control" required>
                                    <option value="">Select UOM</option>
                                    <?php
                                    foreach ($getUomListObj["data"] as $row) {
                                    ?>
                                        <option value="<?= $row["uomName"] ?>"><?= $row["uomDesc"] ?> (<?= $row["uomName"] ?>)</option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END New Over Head Modal -->
</div>

<script>
    $(document).ready(function() {

        $(`#frmOtherHeadGlDropDown`).select2({
            dropdownParent: $(`#addBomOtherHeadModal`)
        });

        $(`#frmOtherHeadUomDropDown`).select2({
            dropdownParent: $(`#addBomOtherHeadModal`)
        });

        //=============================================== [UPDATE GRAND COST] =======================================================
        function updateGrandTotalCost() {
            let grandMaterialCost = 0;
            $(".bomMaterialAmount").each(function() {
                grandMaterialCost += parseFloat($(this).val()) > 0 ? parseFloat($(this).val()) : 0;
            })

            let grandHourlyDeploymentCost = 0;
            $(".bomHdAmount").each(function() {
                grandHourlyDeploymentCost += parseFloat($(this).val()) > 0 ? parseFloat($(this).val()) : 0;
            })

            let grandOtherHeadCost = 0;
            $(".bomOtherHeadAmount").each(function() {
                grandOtherHeadCost += parseFloat($(this).val()) > 0 ? parseFloat($(this).val()) : 0;
            })

            $("#grandMaterialCost").html(inputValue(grandMaterialCost));
            $("#grandHourlyDeploymentCost").html(inputValue(grandHourlyDeploymentCost));
            $("#grandOtherHeadCost").html(inputValue(grandOtherHeadCost));

            $("#grandMaterialCostInput").val(inputValue(grandMaterialCost));
            $("#grandHourlyDeploymentCostInput").val(inputValue(grandHourlyDeploymentCost));
            $("#grandOtherHeadCostInput").val(inputValue(grandOtherHeadCost));

            $("#grandTotalBomCost").html(inputValue(grandMaterialCost + grandHourlyDeploymentCost + grandOtherHeadCost));
            $("#grandTotalBomCostInput").val(inputValue(grandMaterialCost + grandHourlyDeploymentCost + grandOtherHeadCost));
        }
        //============================================= [END UPDATE GRAND COST] =====================================================


        //================================================ [START MATERIAL] =========================================================
        function addBoqItemMaterialNewRow(rowNo = 0) {
            $("#bomMaterialDiv").append(`<tr id="bomMaterialsDivRow_${rowNo}">
                                    <td class="p-1">
                                        <input type="hidden" name="bomMaterial[${rowNo}][ItemGl]" id="bomMaterialGl_${rowNo}" value="0" class="form-control">
                                        <select name="bomMaterial[${rowNo}][ItemId]" id="bomMaterialId_${rowNo}" class="form-control rmSfgItemsDropDown" required>
                                            <option value="" data-row=""> --Select Item-- </option>
                                            <?php
                                            foreach ($goodMasterList["data"] as $key => $itemObj) {
                                                if ($itemObj["itemId"] == $itemId) continue;
                                                if ($itemObj["bomStatus"] == 1) {
                                                    echo '<option value="' . $itemObj["itemId"] . '" data-bomstatus="' . $itemObj["bomStatus"] . '"  data-row="' . base64_encode(json_encode($itemObj, true)) . '"  disabled>' . $itemObj["itemName"] . ' - ' . $itemObj["itemCode"] . ' [' . $itemObj["type"] . '] (BOM not created)</option>';
                                                } else {
                                                    echo '<option value="' . $itemObj["itemId"] . '" data-bomstatus="' . $itemObj["bomStatus"] . '" data-row="' . base64_encode(json_encode($itemObj, true)) . '"  >' . $itemObj["itemName"] . ' - ' . $itemObj["itemCode"] . ' [' . $itemObj["type"] . ']</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td class="p-1">
                                        <input type = "text" name = "bomMaterial[${rowNo}][Code]" id = "bomMaterialCode_${rowNo}" placeholder = "Item Code" class="form-control m-0 bomMaterialCode" readonly >
                                    </td>
                                    <td class="p-1">
                                        <input type = "text" name = "bomMaterial[${rowNo}][Type]" id = "bomMaterialType_${rowNo}" placeholder = "Item Type" class="form-control m-0 bomMaterialType" readonly >
                                    </td>
                                    <td class="p-1">
                                        <input type = "text" name = "bomMaterial[${rowNo}][Uom]" id = "bomMaterialUom_${rowNo}" placeholder = "Item UOM" class="form-control m-0 bomMaterialUom" readonly >
                                    </td>
                                    <td class="p-1">
                                        <input type = "number" step = "any" name = "bomMaterial[${rowNo}][Consumption]" id = "bomMaterialConsumption_${rowNo}" placeholder = "<?= decimalQuantityPreview(0)?>" class = "form-control m-0 bomMaterialConsumtion bomMaterialRowInput text-right" >
                                    </td><?= decimalQuantityPreview(0)?>
                                    <td class="p-1 d-flex">
                                        <input type = "number" step = "any" name = "bomMaterial[${rowNo}][ExtraPurchage]" id = "bomMaterialExtraPurchage_${rowNo}" placeholder = "<?= decimalQuantityPreview(0)?>" class = "form-control m-0 bomMaterialExtraPurchage bomMaterialRowInput text-right" ><span class = "text-muted mt-1 ml-1">%</span>
                                    </td><?= decimalQuantityPreview(0)?>
                                    <td class="p-1">
                                        <input type = "number" step = "any" name = "bomMaterial[${rowNo}][Rate]" value="<?= decimalValuePreview(0)?>" id = "bomMaterialRate_${rowNo}" placeholder = "<?= decimalValuePreview(0)?>" class = "form-control m-0 bomMaterialRate text-right" readonly >
                                    </td>
                                    <td class="p-1">
                                        <input type = "number" step = "any" name = "bomMaterial[${rowNo}][Amount]" value="<?= decimalValuePreview(0)?>" id = "bomMaterialAmount_${rowNo}" placeholder = "<?= decimalValuePreview(0)?>" class = "form-control m-0 bomMaterialAmount text-right" readonly >
                                    </td>
                                    <td class="p-1">
                                        <input type="text" name="bomMaterial[${rowNo}][Remark]" id="bomMaterialRemark_${rowNo}" placeholder="Item Remark" class="form-control m-0 bomMaterialRemark">
                                    </td>
                                    <td class="p-1 text-center">
                                        <i class="fa fa-minus bg-danger rounded p-1 removeBoqMaterialsDivItemBtn" style="cursor: pointer;"></i >
                                    </td>
                                </tr>`);

            $(`#bomMaterialId_${rowNo}`).select2();
        }

        $(document).on("change", ".rmSfgItemsDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));
            // console.log(selectVal);
            // console.log(rowData);
            console.log(rowDataObj);
            $(`#bomMaterialGl_${rowNo}`).val(rowDataObj["parentGlId"]);
            $(`#bomMaterialCode_${rowNo}`).val(rowDataObj["itemCode"]);
            $(`#bomMaterialType_${rowNo}`).val(rowDataObj["type"]);
            $(`#bomMaterialConsumption_${rowNo}`).val(inputQuantity(1));
            $(`#bomMaterialExtraPurchage_${rowNo}`).val(inputQuantity(0));
            $(`#bomMaterialUom_${rowNo}`).val(rowDataObj["uomName"]);
            if (rowDataObj["type"] == "SFG") {
                $(`#bomMaterialRate_${rowNo}`).val(inputValue(rowDataObj["itemBomPrice"]));
                $(`#bomMaterialAmount_${rowNo}`).val(inputValue(rowDataObj["itemBomPrice"]));
            } else {
                $(`#bomMaterialRate_${rowNo}`).val(inputValue(rowDataObj["movingWeightedPrice"]));
                $(`#bomMaterialAmount_${rowNo}`).val(inputValue(rowDataObj["movingWeightedPrice"]));
            }
            updateGrandTotalCost();
        });

        function calculateBoqMaterialOneRowCost(rowNo = null) {
            let rate = $(`#bomMaterialRate_${rowNo}`).val();
            let qty = $(`#bomMaterialConsumption_${rowNo}`).val();
            let extra = $(`#bomMaterialExtraPurchage_${rowNo}`).val();
            let totalQty = parseFloat(qty) + parseFloat(qty * extra / 100);
            let itemAmount = rate * totalQty;
            $(`#bomMaterialAmount_${rowNo}`).val(itemAmount.toFixed(2));
        }

        $(document).on("keyup", ".bomMaterialRowInput", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateBoqMaterialOneRowCost(rowNo);
            updateGrandTotalCost();
        });

        addBoqItemMaterialNewRow();
        var bomMaterialRowNo = 0;
        $(document).on("click", ".addBoqMaterialsDivItemBtn", function() {
            addBoqItemMaterialNewRow(bomMaterialRowNo += 1);
            updateGrandTotalCost();
        });

        $(document).on("click", ".removeBoqMaterialsDivItemBtn", function() {
            let elm = $(this).parent().parent().remove();
            updateGrandTotalCost();
        });
        //====================================================================== [END MATERIAL] ====================================================================


        //===================================== [START HOURLY DEPLOYEMENT] ================================================
        function addBoqItemHourlyDeploymentNewRow(rowNo = 0) {
            $("#bomHourlyDeploymentDiv").append(`
                                    <tr id="bomHdDivRow_${rowNo}">
                                        <td class="p-1" style="width: 15%;">
                                            <div>
                                                <select name="bomHd[${rowNo}][CostCenterId]" id="bomHdCostCenterId_${rowNo}" class="form-control bomHdCostCenterDropDown" required>
                                                    <option value="" data-row=""> -- Select Work Center -- </option>
                                                    <?php
                                    foreach ($getWc['data'] as $key => $wc) {
                                    ?>
                                        <option value="<?= $wc['work_center_id'] ?>" data-row="<?php echo base64_encode(json_encode($wc, true)); ?>" ><?= $wc['work_center_name'] . '(' . $wc['work_center_code'] . ')' ?></option>
                                    <?php
                                    }
                                    ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="p-1" style="width: 5%;">
                                            <input type="text" name="bomHd[${rowNo}][CostCenterCode]" id="bomHdCostCenterCode_${rowNo}" placeholder="Item Code" class="form-control m-0 bomHdCostCenterCode" readonly>
                                        </td>
                                        <td class="p-1" style="width: 10%;">
                                            <select name="bomHd[${rowNo}][ItemHdType]" id="bomHdItemHdType_${rowNo}" class="form-control bomHdItemHdTypeDropDown" required>
                                                <option value="" data-row=""> -- Select HD Type -- </option>
                                                <option value="lhr">LHR</option>
                                                <option value="mhr">MHR</option>
                                            </select>
                                        </td>
                                        <td class="p-1" style="width: 5%;">
                                            <input type="text" name="bomHd[${rowNo}][Uom]" id="bomHdUom_${rowNo}" placeholder="Item UOM" value="" class="form-control m-0 bomHdUom" readonly>
                                        </td>
                                        <td class="p-1" style="width: 10%;">
                                            <input type="number" step="any" name="bomHd[${rowNo}][Consumption]" id="bomHdConsumption_${rowNo}" placeholder="<?= decimalQuantityPreview(0)?>" class="form-control m-0 bomHdConsumption bomHdRowInput text-right">
                                        </td>
                                        <td class="p-1 d-flex">
                                            <input type="number" step="any" name="bomHd[${rowNo}][ExtraPurchage]" id="bomHdExtraPurchage_${rowNo}" placeholder="<?= decimalQuantityPreview(0)?>" class="form-control m-0 bomHdExtraPurchage bomHdRowInput text-right"><span class="text-muted mt-1 ml-1">%</span>
                                        </td>
                                        <td class="p-1" style="width: 8%;">
                                            <input type="number" step="any" name="bomHd[${rowNo}][Rate]" value="<?= decimalValuePreview(0)?>" id="bomHdRate_${rowNo}" placeholder="<?= decimalValuePreview(0)?>" class="form-control m-0 bomHdRate text-right" readonly>
                                        </td>
                                        <td class="p-1" style="width: 10%;">
                                            <input type="number" step="any" name="bomHd[${rowNo}][Amount]" value="<?= decimalValuePreview(0)?>" id="bomHdAmount_${rowNo}" placeholder="<?= decimalValuePreview(0)?>" class="form-control m-0 bomHdAmount text-right" readonly>
                                        </td>
                                        <td class="p-1" style="width: 10%;">
                                            <input type="text" name="bomHd[${rowNo}][Remark]" placeholder="Activity Remark" class="form-control m-0">
                                        </td>
                                        <td class="p-1 text-center" style="width:4%;"><i class="fa fa-minus bg-danger rounded p-1 removeBoqHdDivItemBtn" style="cursor: pointer;"></i></td>
                                    </div>`);

            $(`#bomHdCostCenterId_${rowNo}`).select2();
        }
        $(document).on("change", ".bomHdCostCenterDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
          //  console.log(rowData);
            let rowDataObj = JSON.parse(atob(rowData));
            console.log(rowDataObj);

            $(`#bomHdCostCenterCode_${rowNo}`).val(rowDataObj["work_center_code"]);
            $(`#bomHdItemHdType_${rowNo}`).val($(`#bomHdItemHdType_${rowNo} option:first`).val());
            $(`#bomHdConsumption_${rowNo}`).val("");
            $(`#bomHdExtraPurchage_${rowNo}`).val("");
            $(`#bomHdRate_${rowNo}`).val("");
            $(`#bomHdAmount_${rowNo}`).val("");
        });

        $(document).on("change", ".bomHdItemHdTypeDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(`#bomHdCostCenterId_${rowNo}`).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));

            let amount = 0;
            if (selectVal == "lhr") {
                amount = parseFloat(rowDataObj["wc_lhr"]);
            } else if (selectVal == "mhr") {
                amount = parseFloat(rowDataObj["wc_mhr"]);
            }
            $(`#bomHdUom_${rowNo}`).val("hour");
            $(`#bomHdConsumption_${rowNo}`).val(1);
            $(`#bomHdExtraPurchage_${rowNo}`).val(inputQuantity(0));
            $(`#bomHdRate_${rowNo}`).val(inputValue(amount));
            $(`#bomHdAmount_${rowNo}`).val(inputValue(amount));

            updateGrandTotalCost();
        });

        function calculateHourlyDeploymentCost(rowNo = null) {
            console.log("Calculating HourlyDeployment cost");
            let bomHdRate = parseFloat($(`#bomHdRate_${rowNo}`).val()) > 0 ? parseFloat($(`#bomHdRate_${rowNo}`).val()) : 0;
            let bomHdConsumption = parseFloat($(`#bomHdConsumption_${rowNo}`).val()) > 0 ? parseFloat($(`#bomHdConsumption_${rowNo}`).val()) : 0;
            let bomHdExtraPurchage = parseFloat($(`#bomHdExtraPurchage_${rowNo}`).val()) > 0 ? parseFloat($(`#bomHdExtraPurchage_${rowNo}`).val()) : 0;
            let totalQty = bomHdConsumption + (bomHdConsumption * bomHdExtraPurchage / 100);
            let amount = bomHdRate * totalQty;
            $(`#bomHdAmount_${rowNo}`).val(amount.toFixed(2));
        }
        $(document).on("keyup", ".bomHdRowInput", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateHourlyDeploymentCost(rowNo);
            updateGrandTotalCost();
        });

        // adding/removing good activity or cost center to bom list
        // addBoqItemHourlyDeploymentNewRow();
        var bomHdRowNo = 0;
        $(document).on("click", ".addBoqHdDivItemBtn", function() {
            addBoqItemHourlyDeploymentNewRow(bomHdRowNo += 1);
            updateGrandTotalCost();
        });
        $(document).on("click", ".removeBoqHdDivItemBtn", function() {
            let elm = $(this).parent().parent().remove();
            updateGrandTotalCost();
        });
        //========================================== [END HOURLY DEPLOYEMENT] =============================================


        //===================================== [START OTHER HEAD] ==========================================================
        function addBoqItemOtherHeadNewRow(rowNo = 0) {
            $("#bomOtherHeadDiv").append(`
                                    <tr id="bomOtherHeadDivRow_${rowNo}">
                                        <td class="p-1" style="width: 15%;">
                                            <select name="bomOtherHead[${rowNo}][CostCenterId]" id="bomOtherHeadCostCenterId_${rowNo}" class="form-control bomOtherHeadCostCenterDropDown" required>
                                                <option value="" data-row=""> -- Select Work Center -- </option>
                                                <?php
                                    foreach($getWc['data'] as $key => $wc) {
                                    ?>
                                        <option value="<?= $wc['work_center_id'] ?>" data-row="<?php echo base64_encode(json_encode($wc, true)); ?>"><?= $wc['work_center_name'] . '(' . $wc['work_center_code'] . ')' ?></option>
                                    <?php
                                    }
                                    ?>
                                            </select>
                                        </td>
                                        <td class="p-1" style="width: 5%;">
                                            <input type="text" name="bomOtherHead[${rowNo}][CostCenterCode]" id="bomOtherHeadCostCenterCode_${rowNo}" placeholder="Item Code" class="form-control m-0 bomOtherHeadCostCenterCode" readonly>
                                        </td>
                                        <td class="p-1 bomOtherHeadDropDownTr" id="bomOtherHeadDropDownTr_${rowNo}" style="width: 10%;">
                                            <select name="bomOtherHead[${rowNo}][Head]" id="bomOtherHead_${rowNo}" class="form-control bomOtherHeadDropDown" required>
                                                <option value="" data-row="" disabled>Loding Head... </option>
                                            </select>
                                        </td>
                                        <td class="p-1" style="width: 5%;">
                                            <input type="text" name="bomOtherHead[${rowNo}][Uom]" id="bomOtherHeadUom_${rowNo}" placeholder="Item UOM" class="form-control m-0 bomOtherHeadUom" readonly>
                                        </td>
                                        <td class="p-1" style="width: 10%;">
                                            <input type="number" step="any" name="bomOtherHead[${rowNo}][Consumption]" id="bomOtherHeadConsumption_${rowNo}" placeholder="<?= decimalQuantityPreview(0)?>" class="form-control m-0 bomOtherHeadConsumption bomOtherHeadRowInput text-right">
                                        </td>
                                        <td class="p-1 d-flex">
                                            <input type="number" step="any" name="bomOtherHead[${rowNo}][ExtraPurchage]" id="bomOtherHeadExtraPurchage_${rowNo}" placeholder="<?= decimalQuantityPreview(0)?>" class="form-control m-0 bomOtherHeadExtraPurchage bomOtherHeadRowInput text-right"><span class="text-muted mt-1 ml-1">%</span>
                                        </td>
                                        <td class="p-1" style="width: 8%;">
                                            <input type="number" step="any" name="bomOtherHead[${rowNo}][Rate]" value="<?= decimalValuePreview(0)?>" id="bomOtherHeadRate_${rowNo}" placeholder="<?= decimalValuePreview(0)?>" class="form-control m-0 bomOtherHeadRate text-right" readonly>
                                        </td>
                                        <td class="p-1" style="width: 10%;">
                                            <input type="number" step="any" name="bomOtherHead[${rowNo}][Amount]" value="<?= decimalValuePreview(0)?>" id="bomOtherHeadAmount_${rowNo}" placeholder="<?= decimalValuePreview(0)?>" class="form-control m-0 bomOtherHeadAmount text-right" readonly>
                                        </td>
                                        <td class="p-1" style="width: 10%;">
                                            <input type="text" name="bomOtherHead[${rowNo}][Remark]" placeholder="Activity Remark" class="form-control m-0">
                                        </td>
                                        <td class="p-1 text-center" style="width: 4%;">
                                            <i class="fa fa-minus bg-danger rounded p-1 removeBoqOtherHeadDivItemBtn" style="cursor: pointer;"></i>
                                        </td>
                                    </tr>`);
            $(`#bomOtherHeadCostCenterId_${rowNo}`).select2();
            $(`#bomOtherHead_${rowNo}`)
                .select2()
                .on('select2:open', () => {
                    $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addBomOtherHeadModal">Add New</a></div>`);
                });

            $.ajax({
                type: "GET",
                url: "<?= BASE_URL ?>branch/location/bom/ajax/ajax-other-expense-head.php", // Specify the URL where you want to submit the form
                data: {
                    head_type: 1
                },
                success: function(response) {
                    // Handle the success response here
                    let responseData = JSON.parse(response);
                    let html = responseData.data.map((item, i) => {
                        return `<option value="${item.head_id}" data-row="${window.btoa(JSON.stringify(item))}">${item.head_name}</option>`;
                    }).join("");
                    $(`#bomOtherHead_${rowNo}`).html(`<option value="" data-row="">Select One Head...</option>${html}`);
                    // console.log(html);
                },
                error: function(xhr, status, error) {
                    // Handle errors here
                    console.error("Error:", error);
                },
                complete: function() {}
            });
        }
        $(document).on("change", ".bomOtherHeadCostCenterDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));
            $(`#bomOtherHeadCostCenterCode_${rowNo}`).val(rowDataObj["work_center_code"]);
        });

        $(document).on("change", ".bomOtherHeadDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));
            $(`#bomOtherHeadCostCenterCode_${rowNo}`).val(rowDataObj["head_code"]);
            $(`#bomOtherHeadUom_${rowNo}`).val(rowDataObj["head_uom"]);
            $(`#bomOtherHeadRate_${rowNo}`).val(inputValue(rowDataObj["head_rate"]))
            $(`#bomOtherHeadConsumption_${rowNo}`).val(inputQuantity(1));
            $(`#bomOtherHeadExtraPurchage_${rowNo}`).val(inputQuantity(0));
            $(`#bomOtherHeadAmount_${rowNo}`).val(inputValue(rowDataObj["head_rate"]));
            updateGrandTotalCost();
        });

        function calculateOtherHeadCost(rowNo = null) {
            console.log("Calculating Other head cost");
            let bomOtherHeadRate = parseFloat($(`#bomOtherHeadRate_${rowNo}`).val()) > 0 ? parseFloat($(`#bomOtherHeadRate_${rowNo}`).val()) : 0;
            let bomOtherHeadConsumption = parseFloat($(`#bomOtherHeadConsumption_${rowNo}`).val()) > 0 ? parseFloat($(`#bomOtherHeadConsumption_${rowNo}`).val()) : 0;
            let bomOtherHeadExtraPurchage = parseFloat($(`#bomOtherHeadExtraPurchage_${rowNo}`).val()) > 0 ? parseFloat($(`#bomOtherHeadExtraPurchage_${rowNo}`).val()) : 0;
            let totalQty = bomOtherHeadConsumption + (bomOtherHeadConsumption * bomOtherHeadExtraPurchage / 100);
            let amount = bomOtherHeadRate * totalQty;
            $(`#bomOtherHeadAmount_${rowNo}`).val(amount.toFixed(2));
        }

        $(document).on("keyup", ".bomOtherHeadRowInput", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOtherHeadCost(rowNo);
            updateGrandTotalCost();
        });

        // adding/removing other good items to bom list
        // addBoqItemOtherHeadNewRow();
        var bomOtherHeadRowNo = 0;
        $(document).on("click", ".addBoqOtherHeadDivItemBtn", function() {
            addBoqItemOtherHeadNewRow(bomOtherHeadRowNo += 1);
            updateGrandTotalCost();
        });
        $(document).on("click", ".removeBoqOtherHeadDivItemBtn", function() {
            let elm = $(this).parent().parent().remove();
            updateGrandTotalCost();
        });
        //============================================ [END OTHER HEAD] =============================================


        //============================================ [START ADD OTHER HEAD] =======================================

        $(document).on("submit", "#frmAddOtherExpenseHeadForm", function(event) {
            // Prevent the default form submission
            event.preventDefault();
            // Disable the submit button to prevent multiple submissions
            $("button[type=submit]").prop("disabled", true);
            // Serialize the form data
            var formData = $(this).serialize();
            // Perform an AJAX POST request to submit the form
            $.ajax({
                type: "POST",
                url: "<?= BASE_URL ?>branch/location/bom/ajax/ajax-other-expense-head.php", // Specify the URL where you want to submit the form
                data: formData,
                success: function(response) {
                    // Handle the success response here
                    let responseData = JSON.parse(response);
                    Swal.fire({
                        icon: responseData.status,
                        title: responseData.status.toUpperCase(),
                        text: responseData.message,
                    });

                    // console.log(responseData);
                    // Reset the form
                    $("#frmAddOtherExpenseHeadForm")[0].reset();
                },
                error: function(xhr, status, error) {
                    // Handle errors here
                    console.error("Error:", error);
                },
                complete: function() {
                    // Re-enable the submit button
                    $("button[type=submit]").prop("disabled", false);
                }
            });
        });

        //============================================= [END ADD OTHER HEAD] ========================================


    });
</script>