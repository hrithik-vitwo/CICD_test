<!-- All massages, logics, consoles  -->
<div class="row m-0 p-0 messages justify-content-end">
    <?php
    $itemId = base64_decode($_GET["create"]);
    $goodsDetailsObj = $goodsController->getGoodsDeatils($itemId);
    $goodTitle = $goodsDetailsObj["data"]["itemName"] ?? "";
    $goodCode = $goodsDetailsObj["data"]["itemCode"] ?? "";
    $rmGoodsObj = $goodsController->getAllRMGoods();
    ?>
</div>
<!-- /.All massages, logics, consoles -->
<?php
// if (isset($_POST["createBomSubmitBtn"])) {
//     // console($_POST);
//     $createBomObj = $goodsBomController->createBom($_POST);
//     if ($createBomObj["status"] == "success") {
//         swalAlert($createBomObj["status"], ucfirst($createBomObj["status"]), $createBomObj["message"], LOCATION_URL . "manage-bom.php");
//     } else {
//         swalAlert($createBomObj["status"], ucfirst($createBomObj["status"]), $createBomObj["message"]);
//     }
//     //console($createBomObj);
// }
?>

<!-- BOM Form -->
<div class="card p-0 bom-form-card">
    <form action="" method="post" id="billOfMeterialForm">
        <div class="card-header p-2 h5 text-light">Create Bill Of Meterial</div>
        <div class="card-body p-2">
            <div>
                <?php
                // console($goodsDetailsObj);
                console($_SESSION["logedBranchAdminInfo"]);
                console($_POST);
                ?>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <span>Prepared By</span>
                    <input type="hidden" name="preparedBy" value="<?= $_SESSION["logedBranchAdminInfo"]["adminId"] ?? ""; ?>">
                    <input type="hidden" name="itemId" value="<?= $itemId ?>">
                    <input type="text" value="<?= $_SESSION["logedBranchAdminInfo"]["adminName"] ?? ""; ?>" placeholder="Created by" class="form-control" readonly>
                </div>
                <div class="col-md-6">
                    <span>Prepared Date</span>
                    <input type="date" value="<?= date("Y-m-d"); ?>" name="preparedDate" class="form-control" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <span>Good Title</span>
                    <input type="text" value="<?= $goodTitle ?>" placeholder="Good title" class="form-control" readonly>
                </div>
                <div class="col-md-6">
                    <span>Good Code</span>
                    <input type="text" value="<?= $goodCode ?>" placeholder="Good Code" class="form-control" readonly>
                </div>
            </div>
            <hr>
            <div class="card">
                <div class="card-body p-1">
                    <div class="customTable border rounded" style="font-size: 0.7em;">
                        <div class="row m-0 p-0">Materials</div>
                        <div class="row mx-2 mt-1 border-top border-bottom justify-content-between font-weight-bold text-light" style="background-color: #003060;">
                            <div class="border-right p-1 justify-content-between" style="width: 15%;">Item Title
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small>
                            </div>
                            <div class="border-right p-1 justify-content-between" style="width: 8%;">Item Code
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small>
                            </div>
                            <div class="border-right p-1 justify-content-between" style="width: 15%;">Type
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small>
                            </div>
                            <div class="border-right p-1" style="width: 10%;">UOM
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Unit of measurement e.g kg, pc, pkt, etc."></i></small>
                            </div>

                            <div class="border-right p-1" style="width: 10%;">Consumption
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="No of items"></i></small>
                            </div>
                            <div class="border-right p-1" style="width: 6%;">Extra(%)
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Extra purchages"></i></small>
                            </div>

                            <div class="border-right p-1" style="width: 10%;">Rate
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="One item rate(price)"></i></small>
                            </div>
                            <div class="border-right p-1" style="width: 10%;">Amount
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total amount"></i></small>
                            </div>
                            <div class="border-right p-1" style="width: 16%;">Remark
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small>
                            </div>
                        </div>
                        <div class="bomMaterialDiv mx-2">
                        </div>
                        <div class="row m-0 p-0 mt-3">Activity</div>
                        <div class="row mx-2 mt-1 p-0">Hourly Deployment</div>
                        <div class="row mx-2 border-top border-bottom justify-content-between font-weight-bold text-light" style="background-color: #003060;">
                            <div class="border-right p-1 text-left" style="width: 15%;">Cost Center Name
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Cost Center Name"></i></small>
                            </div>
                            <div class="border-right p-1 text-left" style="width: 6%;">Code
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small>
                            </div>
                            <div class="border-right py-1 text-left" style="width: 15%;">Hourly Deployment Type
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Hourly deployment type(LHR or MHR)"></i></small>
                            </div>
                            <div class="border-right p-1" style="width: 6%;">UOM
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Unit of measurement e.g LHR, MHR."></i></small>
                            </div>
                            <div class="border-right p-1 text-left" style="width: 10%;">Consumption
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Consumption in hour"></i></small>
                            </div>
                            <div class="border-right p-1" style="width: 6%;">Extra(%)
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Extra purchages"></i></small>
                            </div>
                            <div class="border-right p-1" style="width: 10%;">Rate
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="1 UOM Rate or price"></i></small>
                            </div>
                            <div class="border-right p-1" style="width: 10%;">Amount
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total Cost"></i></small>
                            </div>
                            <div class="border-right p-1" style="width: 10%;">Remark
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small>
                            </div>
                            <div class="p-1 text-center" style="width: 4%;"><i class="fa fa-action"></i></div>
                        </div>
                        <div class="bomHourlyDeploymentDiv mx-2 p-0">

                        </div>
                        <div class="row mx-2 mt-1 p-0">Other Head</div>
                        <div class="row mx-2 mt-1 border-top border-bottom justify-content-between font-weight-bold text-light" style="background-color: #003060;">
                            <div class="border-right p-1 text-left" style="width: 15%;">Cost Center Name
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Cost Center Name"></i></small>
                            </div>
                            <div class="border-right p-1 text-left" style="width: 8%;">Code
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small>
                            </div>
                            <div class="border-right py-1 text-left" style="width: 18%;">Other Head
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Hourly deployment type(LHR or MHR)"></i></small>
                            </div>
                            <!-- <div class="border-right p-1" style="width: 10%;">UOM
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Unit of measurement e.g LHR, MHR."></i></small>
                            </div> -->
                            <div class="border-right p-1 text-left" style="width: 10%;">Consumption
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Consumption in hour"></i></small>
                            </div>
                            <div class="border-right p-1" style="width: 6%;">Extra(%)
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Extra purchages"></i></small>
                            </div>
                            <div class="border-right p-1" style="width: 10%;">Rate
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="1 UOM Rate or price"></i></small>
                            </div>
                            <div class="border-right p-1" style="width: 10%;">Amount
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total Cost"></i></small>
                            </div>
                            <div class="border-right p-1" style="width: 15%;">Remark
                                <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small>
                            </div>
                            <div class="p-1 text-center" style="width: 4%;"><i class="fa fa-action"></i></div>
                        </div>
                    </div>
                    <div class="bomOtherHeadDiv mx-2 p-0">

                    </div>
                </div>
            </div>
            <div class="card-footer m-0 p-0 text-right">
                <button type="submit" value="Save" name="createBomSubmitBtn" class="btn btn-primary text-light my-3">Save Bom</button>
                <a href="<?= basename($_SERVER['PHP_SELF']); ?>"><button class="btn btn-danger">Back</button></a>
            </div>
        </div>
</div>
</form>
</div>
<!-- /.BOM Form -->

<script>
    function getTime() {
        return (new Date()).getTime();
    }

    $(document).ready(function() {

        $(".clickTheFormSaveBtn").click(function() {
            console.log("Clicked the save button");
            $("#billOfMeterialForm").submit();
        });

        //======================================================================================== [START MATERIAL] ============================================================================================
        function addBomItemMaterialNewRow(rowNo = 0) {
            $(".bomMaterialDiv").append(`
                <div class="row m-0 border-top border-bottom justify-content-between" id="bomMaterialsDivRow_${rowNo}">
                    <div class="border-right p-1" style="width: 15%;">
                        <input type="hidden" name="bomMaterialGl[]" id="bomMaterialGl_${rowNo}" value="0" class="form-control">
                        <select name="bomMaterialId[]" id="bomMaterialId_${rowNo}" class="form-control rmSfgItemsDropDown" required>
                            <option value="" data-row=""> -- Select Item -- </option>
                            <?php
                            foreach (getRmSfgItems()["data"] as $key => $itemObj) {
                                if ($itemObj["itemId"] == $itemId) continue;
                                echo '<option value="' . $itemObj["itemId"] . '"  data-row="' . base64_encode(json_encode($itemObj, true)) . '"  >' . $itemObj["itemName"] . ' - ' . $itemObj["itemCode"] . ' [' . $itemObj["type"] . ']</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 8%;">
                        <input type="text" name="bomMaterialCode[]" id="bomMaterialCode_${rowNo}" placeholder="Item Code" class="form-control m-0 bomMaterialCode" readonly>
                    </div>
                    <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 15%;">
                        <input type="text" name="bomMaterialType[]" id="bomMaterialType_${rowNo}" placeholder="Item Type" class="form-control m-0 bomMaterialType" readonly>
                    </div>
                    <div class="border-right p-1" style="width: 10%;">
                        <input type="text" name="bomMaterialUom[]" id="bomMaterialUom_${rowNo}" placeholder="Item UOM" class="form-control m-0 bomMaterialUom" readonly>
                    </div>
                    <div class="border-right p-1" style="width: 10%;">
                        <input type="number" step="0.01" name="bomMaterialConsumption[]" id="bomMaterialConsumption_${rowNo}" placeholder="0.00" class="form-control m-0 bomMaterialConsumtion bomMaterialRowInput text-right">
                    </div>
                    <div class="border-right p-1 d-flex" style="width: 6%;">
                        <input type="number" step="0.01" name="bomMaterialExtraPurchage[]" id="bomMaterialExtraPurchage_${rowNo}" placeholder="0.00" class="form-control m-0 bomMaterialExtraPurchage bomMaterialRowInput text-right"><span class="text-muted mt-1 ml-1">%</span>
                    </div>
                    <div class="border-right p-1" style="width: 10%;">
                        <input type="number" step="0.01" name="bomMaterialRate[]" id="bomMaterialRate_${rowNo}" placeholder="0.00" class="form-control m-0 bomMaterialRate text-right" readonly>
                    </div>
                    <div class="border-right p-1" style="width: 10%;">
                        <input type="number" step="0.01" name="bomMaterialAmount[]" id="bomMaterialAmount_${rowNo}" placeholder="0.00" class="form-control m-0 bomMaterialAmount text-right" readonly>
                    </div>
                    <div class="border-right p-1 d-flex" style="width: 16%; height: 100%;">
                        <textarea name="bomMaterialRemark[]" id="bomMaterialRemark_${rowNo}" placeholder="Item Remark" rows="1" class="form-control m-0 p-2 bomMaterialRemark"></textarea>
                        ${rowNo==0?`<i class="fa fa-plus bg-success rounded p-1 ml-1 addbomMaterialsDivItemBtn" style="cursor: pointer;"></i>`:`<i class="fa fa-minus bg-danger rounded p-1 ml-1 removebomMaterialsDivItemBtn" style="cursor: pointer;"></i>`}
                    </div>
                </div>`);

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
            $(`#bomMaterialConsumption_${rowNo}`).val(1);
            $(`#bomMaterialExtraPurchage_${rowNo}`).val(0);
            $(`#bomMaterialUom_${rowNo}`).val(rowDataObj["uomName"]);
            if (rowDataObj["type"] == "SFG") {
                $(`#bomMaterialRate_${rowNo}`).val(rowDataObj["itemBomPrice"]);
                $(`#bomMaterialAmount_${rowNo}`).val(rowDataObj["itemBomPrice"]);
            } else {
                $(`#bomMaterialRate_${rowNo}`).val(rowDataObj["movingWeightedPrice"]);
                $(`#bomMaterialAmount_${rowNo}`).val(rowDataObj["movingWeightedPrice"]);
            }
        });

        function calculateBomMaterialOneRowCost(rowNo = null) {
            let rate = $(`#bomMaterialRate_${rowNo}`).val();
            let qty = $(`#bomMaterialConsumption_${rowNo}`).val();
            let extra = $(`#bomMaterialExtraPurchage_${rowNo}`).val();
            let totalQty = parseFloat(qty) + parseFloat(qty * extra / 100);
            let itemAmount = rate * totalQty;
            $(`#bomMaterialAmount_${rowNo}`).val(itemAmount);
        }
        $(document).on("keyup", ".bomMaterialRowInput", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateBomMaterialOneRowCost(rowNo);
        });

        // adding/removing good items to bom list
        addBomItemMaterialNewRow();
        var bomMaterialRowNo = 0;
        $(document).on("click", ".addbomMaterialsDivItemBtn", function() {
            addBomItemMaterialNewRow(bomMaterialRowNo += 1);
        });
        $(document).on("click", ".removebomMaterialsDivItemBtn", function() {
            let elm = $(this).parent().parent().remove();
        });
        //======================================================================================== [END MATERIAL] ============================================================================================


        //================================================================================== [START HOURLY DEPLOYEMENT] ======================================================================================
        function addBomItemHourlyDeploymentNewRow(rowNo = 0) {
            $(".bomHourlyDeploymentDiv").append(`
                <div class="row m-0 border-top border-bottom justify-content-between">
                    <div class="border-right p-1" style="width: 15%;">
                        <select name="bomHdCostCenterId[]" id="bomHdCostCenterId_${rowNo}" class="form-control bomHdCostCenterDropDown" required>
                            <option value="" data-row=""> -- Select Cost Center -- </option>
                            <?php
                            foreach (getGoodActivities()["data"] as $key => $itemObj) {
                                echo '<option value="' . $itemObj["CostCenter_id"] . '"  data-row="' . base64_encode(json_encode($itemObj, true)) . '"  >' . $itemObj["CostCenter_code"] . ' - ' . $itemObj["CostCenter_desc"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="border-right p-1" style="width: 8%;">
                        <input type="text" name="bomHdCostCenterCode[]" id="bomHdCostCenterCode_${rowNo}" placeholder="Item Code" class="form-control m-0 bomHdCostCenterCode" readonly>
                    </div>
                    <div class="border-right p-1" style="width: 18%;">
                        <select name="bomHdItemHdType[]" id="bomHdItemHdType_${rowNo}" class="form-control bomHdItemHdTypeDropDown" required>
                            <option value="" data-row=""> -- Select HD Type -- </option>
                            <option value="lhr">LHR</option>
                            <option value="mhr">MHR</option>
                        </select>
                    </div>
                    <!--<div class="border-right p-1" style="width: 10%;">
                        <input type="text" name="bomHdUom[]" id="bomHdUom_${rowNo}" placeholder="Item UOM" class="form-control m-0 bomHdUom" readonly>
                    </div> -->
                    <div class="border-right p-1" style="width: 10%;">
                        <input type="number" step="0.01" name="bomHdConsumption[]" id="bomHdConsumption_${rowNo}" placeholder="0.00" class="form-control m-0 bomHdConsumption bomHdRowInput text-right">
                    </div>
                    <div class="border-right p-1 d-flex" style="width: 6%;">
                        <input type="number" step="0.01" name="bomHdExtraPurchage[]" id="bomHdExtraPurchage_${rowNo}" placeholder="0.00" class="form-control m-0 bomHdExtraPurchage bomHdRowInput text-right"><span class="text-muted mt-1 ml-1">%</span>
                    </div>
                    <div class="border-right p-1" style="width: 10%;">
                        <input type="number" step="0.01" name="bomHdRate[]" id="bomHdRate_${rowNo}" placeholder="0.00" class="form-control m-0 bomHdRate text-right" readonly>
                    </div>
                    <div class="border-right p-1" style="width: 10%;">
                        <input type="number" step="0.01" name="bomHdAmount[]" id="bomHdAmount_${rowNo}" placeholder="0.00" class="form-control m-0 bomHdAmount text-right" readonly>
                    </div>
                    <div class="border-right p-1" style="width: 15%; height: 100%;">
                        <textarea name="bomHdRemark[]" placeholder="Activity Remark" rows="1" class="form-control m-0 p-2"></textarea>
                    </div>
                    <div class="text-center" style="width: 4%;">
                        ${rowNo==0?`<i class="fa fa-plus bg-success rounded p-1 mt-2 addBomHdDivItemBtn" style="cursor: pointer;"></i>`:`<i class="fa fa-minus bg-danger rounded p-1 mt-2 removeBomHdDivItemBtn" style="cursor: pointer;"></i>`}
                    </div>
                </div>`);

            $(`#bomHdCostCenterId_${rowNo}`).select2();
        }
        $(document).on("change", ".bomHdCostCenterDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));

            $(`#bomHdCostCenterCode_${rowNo}`).val(rowDataObj["CostCenter_code"]);
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
                amount = parseFloat(rowDataObj["labour_hour_rate"]);
            } else if (selectVal == "mhr") {
                amount = parseFloat(rowDataObj["machine_hour_rate"]);
            }
            $(`#bomHdConsumption_${rowNo}`).val(1);
            $(`#bomHdExtraPurchage_${rowNo}`).val(0);
            $(`#bomHdRate_${rowNo}`).val(amount);
            $(`#bomHdAmount_${rowNo}`).val(amount);
        });

        function calculateHourlyDeploymentCost(rowNo = null) {
            console.log("Calculating HourlyDeployment cost");
            let bomHdRate = parseFloat($(`#bomHdRate_${rowNo}`).val()) > 0 ? parseFloat($(`#bomHdRate_${rowNo}`).val()) : 0;
            let bomHdConsumption = parseFloat($(`#bomHdConsumption_${rowNo}`).val()) > 0 ? parseFloat($(`#bomHdConsumption_${rowNo}`).val()) : 0;
            let bomHdExtraPurchage = parseFloat($(`#bomHdExtraPurchage_${rowNo}`).val()) > 0 ? parseFloat($(`#bomHdExtraPurchage_${rowNo}`).val()) : 0;
            let totalQty = bomHdConsumption + (bomHdConsumption * bomHdExtraPurchage / 100);
            let amount = bomHdRate * totalQty;
            $(`#bomHdAmount_${rowNo}`).val(amount);
        }
        $(document).on("keyup", ".bomHdRowInput", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateHourlyDeploymentCost(rowNo);
        });

        // adding/removing good activity or cost center to bom list
        addBomItemHourlyDeploymentNewRow();
        var bomHdRowNo = 0;
        $(document).on("click", ".addBomHdDivItemBtn", function() {
            addBomItemHourlyDeploymentNewRow(bomHdRowNo += 1);
        });
        $(document).on("click", ".removeBomHdDivItemBtn", function() {
            let elm = $(this).parent().parent().remove();
        });
        //================================================================================== [END HOURLY DEPLOYEMENT] ======================================================================================

        //================================================================================== [START OTHER HEAD] ============================================================================================

        function addBomItemOtherHeadNewRow(rowNo = 0) {
            $(".bomOtherHeadDiv").append(`
                <div class="row m-0 border-top border-bottom justify-content-between">
                    <div class="border-right p-1" style="width: 15%;">
                        <select name="bomOtherHeadCostCenterId[]" id="bomOtherHeadCostCenterId_${rowNo}" class="form-control bomOtherHeadCostCenterDropDown" required>
                            <option value="" data-row=""> -- Select Cost Center -- </option>
                            <?php
                            foreach (getGoodActivities()["data"] as $key => $itemObj) {
                                echo '<option value="' . $itemObj["CostCenter_id"] . '"  data-row="' . base64_encode(json_encode($itemObj, true)) . '"  >' . $itemObj["CostCenter_code"] . ' - ' . $itemObj["CostCenter_desc"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="border-right p-1" style="width: 8%;">
                        <input type="text" name="bomOtherHeadCostCenterCode[]" id="bomOtherHeadCostCenterCode_${rowNo}" placeholder="Item Code" class="form-control m-0 bomOtherHeadCostCenterCode" readonly>
                    </div>
                    <div class="border-right p-1" style="width: 18%;">
                        <select name="bomOtherHead[]" id="bomOtherHead_${rowNo}" class="form-control bomOtherHeadDropDown" required>
                            <option value="" data-row=""> -- Select Other Head -- </option>
                            <option value="1" data-row="">Marketing Cost</option>
                        </select>
                    </div>
                    <!--<div class="border-right p-1" style="width: 10%;">
                        <input type="text" name="bomOtherHeadUom[]" id="bomOtherHeadUom_${rowNo}" placeholder="Item UOM" class="form-control m-0 bomOtherHeadUom" readonly>
                    </div> -->
                    <div class="border-right p-1" style="width: 10%;">
                        <input type="number" step="0.01" name="bomOtherHeadConsumption[]" id="bomOtherHeadConsumption_${rowNo}" placeholder="0.00" class="form-control m-0 bomOtherHeadConsumption text-right">
                    </div>
                    <div class="border-right p-1 d-flex" style="width: 6%;">
                        <input type="number" step="0.01" name="bomOtherHeadExtraPurchage[]" id="bomOtherHeadExtraPurchage_${rowNo}" placeholder="0.00" class="form-control m-0 bomOtherHeadExtraPurchage text-right"><span class="text-muted mt-1 ml-1">%</span>
                    </div>
                    <div class="border-right p-1" style="width: 10%;">
                        <input type="number" step="0.01" name="bomOtherHeadRate[]" id="bomOtherHeadRate_${rowNo}" placeholder="0.00" class="form-control m-0 bomOtherHeadRate text-right" readonly>
                    </div>
                    
                    <div class="border-right p-1" style="width: 10%;">
                        <input type="number" step="0.01" name="bomOtherHeadAmount[]" id="bomOtherHeadAmount_${rowNo}" placeholder="0.00" class="form-control m-0 bomOtherHeadAmount text-right" readonly>
                    </div>
                    <div class="border-right p-1" style="width: 15%; height: 100%;">
                        <textarea name="bomOtherHeadRemark[]" placeholder="Activity Remark" rows="1" class="form-control m-0 p-2"></textarea>
                    </div>
                    <div class="text-center" style="width: 4%;">
                        ${rowNo==0?`<i class="fa fa-plus bg-success rounded p-1 mt-2 addBomOtherHeadDivItemBtn" style="cursor: pointer;"></i>`:`<i class="fa fa-minus bg-danger rounded p-1 mt-2 removeBomOtherHeadDivItemBtn" style="cursor: pointer;"></i>`}
                    </div>
                </div>`);
            $(`#bomOtherHeadCostCenterId_${rowNo}`).select2();
        }
        $(document).on("change", ".bomOtherHeadCostCenterDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));
            $(`#bomOtherHeadCostCenterCode_${rowNo}`).val(rowDataObj["CostCenter_code"]);
        });

        $(document).on("change", ".bomOtherHeadDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let amount=0;
            try {
                let rowData = $(this).find(':selected').data('row');
                let rowDataObj = JSON.parse(atob(rowData));
                let bomOtherRate = parseFloat($(`#bomOtherHeadRate_${rowNo}`).val()) > 0 ? parseFloat($(`#bomOtherHeadRate_${rowNo}`).val()) : 0;
                let bomOtherConsumption = parseFloat($(`#bomOtherHeadConsumption_${rowNo}`).val()) > 0 ? parseFloat($(`#bomOtherHeadConsumption_${rowNo}`).val()) : 0;
                let bomOtherExtraPurchage = parseFloat($(`#bomOtherHeadExtraPurchage_${rowNo}`).val()) > 0 ? parseFloat($(`#bomOtherHeadExtraPurchage_${rowNo}`).val()) : 0;
                let totalQty = bomOtherConsumption + (bomOtherConsumption * bomOtherExtraPurchage / 100);
                amount = bomOtherRate * totalQty;
            } catch (e) {

            }
            $(`#bomOtherHeadConsumption_${rowNo}`).val(1);
            $(`#bomOtherHeadExtraPurchage_${rowNo}`).val(0);
            $(`#bomOtherHeadAmount_${rowNo}`).val(amount);
        });


        // adding/removing other good items to bom list
        addBomItemOtherHeadNewRow();
        var bomOtherHeadRowNo = 0;
        $(document).on("click", ".addBomOtherHeadDivItemBtn", function() {
            addBomItemOtherHeadNewRow(bomOtherHeadRowNo += 1);
        });
        $(document).on("click", ".removeBomOtherHeadDivItemBtn", function() {
            let elm = $(this).parent().parent().remove();
        });
        //================================================================================== [END OTHER HEAD] ============================================================================================
    });
</script>