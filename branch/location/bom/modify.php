<!-- All massages, logics, consoles  -->
<div class="row m-0 p-0 messages justify-content-center">
    <?php
    $bomId = base64_decode($_GET["editBom"]);
    $bomDetailsObj = $goodsBomController->getBomAndAllItemsByBomId($bomId);
    // console($bomDetailsObj);
    $bomData = $bomDetailsObj["data"]["bomDetails"] ?? [];
    $bomItemsList = $bomDetailsObj["data"]["bomItemDetails"] ?? [];
    // console($bomData);
    // console($bomItemsList);

    $itemId = $bomData["itemId"];
    $goodTitle = $bomData["itemName"] ?? "";
    $goodCode = $bomData["itemCode"] ?? "";
    $rmGoodsObj = $goodsController->getAllRMGoods();

    ?>
</div>
<!-- /.All massages, logics, consoles -->
<?php
if (isset($_POST["editBomSubmitBtn"])) {
    // console($_POST);
    $createBomObj = $goodsBomController->createBom($_POST);
    if ($createBomObj["status"] == "success") {
        swalAlert($createBomObj["status"], ucfirst($createBomObj["status"]), $createBomObj["message"], "manage-bom.php?view=" . base64_encode($itemId));
    } else {
        swalAlert($createBomObj["status"], ucfirst($createBomObj["status"]), $createBomObj["message"]);
    }
    // console($createBomObj);
} else {
    if ($bomDetailsObj["status"] == "success") { ?>
        <!-- BOM Form -->
        <div class="card p-0 bom-form-card">
            <form action="" method="post" id="billOfMeterialForm">
                <div class="card-header p-2 h5 text-light">Edit Bill Of Meterial</div>
                <div class="card-body p-2">
                    <div>
                        <?php
                        // console($coaObj["data"]);
                        ?>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <span>Changed By</span>
                            <input type="hidden" name="preparedBy" value="<?= $_SESSION["logedBranchAdminInfo"]["adminId"] ?? ""; ?>">
                            <input type="hidden" name="itemId" value="<?= $itemId ?>">
                            <input type="text" value="<?= $_SESSION["logedBranchAdminInfo"]["adminName"] ?? ""; ?>" placeholder="Created by" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <span>Changed Date</span>
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
                                <div class="row m-0 p-0 bg-secondary"><small class="ml-2" style="font-size: 0.7em;">BOM Items</small></div>
                                <div class="row m-0 border-top border-bottom justify-content-between font-weight-bold text-light" style="background-color: #003060;">
                                    <div class="border-right p-1 justify-content-between" style="width: 15%;">Item Title
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small>
                                    </div>
                                    <div class="border-right p-1 justify-content-between" style="width: 10%;">Item Code
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small>
                                    </div>
                                    <div class="border-right p-1 justify-content-between" style="width: 5%;">Type
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Item details"></i></small>
                                    </div>
                                    <div class="border-right p-1" style="width: 10%;">Consumption
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="No of items"></i></small>
                                    </div>
                                    <div class="border-right p-1" style="width: 10%;">Extra(%)
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Extra purchages"></i></small>
                                    </div>
                                    <div class="border-right p-1" style="width: 10%;">UOM
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Unit of measurement e.g kg, pc, pkt, etc."></i></small>
                                    </div>
                                    <div class="border-right p-1" style="width: 10%;">Rate
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="One item rate(price)"></i></small>
                                    </div>
                                    <div class="border-right p-1" style="width: 10%;">Amount
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total amount"></i></small>
                                    </div>
                                    <div class="border-right p-1" style="width: 15%;">Remark
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small>
                                    </div>
                                    <div class="p-1 text-center" style="width: 5%;"><i class="fa fa-plus bg-success rounded p-1 mt-2 addGoodItemsDivItemBtn" style="cursor: pointer;"></i></div>
                                </div>
                                <div class="goodItemsDiv">
                                    <?php
                                    $rowNoGoods = 0;
                                    foreach ($bomItemsList as $bomOneItem) {
                                        if ($bomOneItem["bomItemType"] != "goods") continue;
                                        $rowNoGoods++;
                                    ?>
                                        <div class="row m-0 border-top border-bottom justify-content-between" id="goodItemsDivRow_<?= $rowNoGoods ?>">
                                            <div class="border-right p-1" style="width: 15%;">
                                                <input type="hidden" name="goodItemGl[]" id="goodItemGl_<?= $rowNoGoods ?>" value="<?= $bomOneItem["itemGl"] ?>" class="form-control">
                                                <select name="goodItemId[]" id="goodItemId_<?= $rowNoGoods ?>" class="form-control rmSfgItemsDropDown select2" required>
                                                    <option value="" data-row=""> -- Select Item -- </option>
                                                    <?php
                                                    foreach (getRmSfgItems()["data"] as $key => $itemObj) {
                                                        if ($itemObj["itemId"] == $itemId) continue;
                                                    ?>
                                                        <option value="<?= $itemObj["itemId"] ?>" data-row="<?= base64_encode(json_encode($itemObj, true)) ?>" <?= $itemObj["itemId"] == $bomOneItem["itemId"] ? "selected" : "" ?>>
                                                            <?= $itemObj["itemName"] . ' - ' . $itemObj["itemCode"] . ' [' . $itemObj["type"] . ']' ?>
                                                        </option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 10%;">
                                                <span id="goodItemCode_<?= $rowNoGoods ?>"><?= $bomOneItem["itemCode"] ?></span>
                                            </div>
                                            <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 5%;">
                                                <span id="goodItemType_<?= $rowNoGoods ?>"><?= $bomOneItem["bomItemType"] ?></span>
                                            </div>
                                            <div class="border-right p-1" style="width: 10%;">
                                                <input type="text" name="goodItemConsumption[]" id="goodItemConsumption_<?= $rowNoGoods ?>" placeholder="Item Consumption" class="form-control m-0 goodItemConsumtion" value="<?= $bomOneItem["itemConsumption"] ?>">
                                            </div>
                                            <div class="border-right p-1 d-flex" style="width: 10%;">
                                                <input type="text" name="goodItemExtraPurchage[]" id="goodItemExtraPurchage_<?= $rowNoGoods ?>" placeholder="Extra Purchase" class="form-control m-0 goodItemExtraPurchage" value="<?= $bomOneItem["itemExtraPurchage"] ?>"><span class="text-muted mt-1 ml-1">%</span>
                                            </div>
                                            <div class="border-right p-1" style="width: 10%;">
                                                <input type="text" name="goodItemUOM[]" id="goodItemUOM_<?= $rowNoGoods ?>" placeholder="Item UOM" class="form-control m-0 goodItemUOM" readonly value="<?= $bomOneItem["itemUOM"] ?>">
                                            </div>
                                            <div class="border-right p-1" style="width: 10%;">
                                                <input step="any" type="number" step="any" name="goodItemRate[]" id="goodItemRate_<?= $rowNoGoods ?>" placeholder="Item Rate" class="form-control m-0 goodItemRate" value="<?= $bomOneItem["itemRate"] ?>">
                                            </div>
                                            <div class="border-right p-1" style="width: 10%;">
                                                <input step="any" type="number" step="any" name="goodItemAmount[]" id="goodItemAmount_<?= $rowNoGoods ?>" placeholder="Item Amount" class="form-control m-0 goodItemAmount" value="<?= $bomOneItem["amount"] ?>">
                                            </div>
                                            <div class="border-right p-1" style="width: 15%; height: 100%;">
                                                <textarea name="goodItemRemark[]" id="goodItemRemark_<?= $rowNoGoods ?>" placeholder="Item Remark" rows="1" class="form-control m-0 p-2 goodItemRemark"><?= $bomOneItem["remarks"] ?></textarea>
                                            </div>
                                            <div class="text-center" style="width: 5%;">
                                                <i class="fa fa-minus bg-danger rounded p-1 mt-2 removeGoodItemsDivItemBtn" style="cursor: pointer;"></i>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="row m-0 p-0 bg-secondary"><small class="ml-2" style="font-size: 0.7em;">BOM Activities</small></div>
                                <div class="row m-0 border-top border-bottom justify-content-between font-weight-bold text-light" style="background-color: #003060;">
                                    <div class="border-right p-1 justify-content-between" style="width: 15%;">Cost Center Name
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Cost Center Name"></i></small>
                                    </div>
                                    <div class="border-right p-1 justify-content-between" style="width: 15%;">Cost Center Code
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Cost Center Code"></i></small>
                                    </div>
                                    <div class="border-right p-1 justify-content-between" style="width: 10%;">Select Gl
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Select Gl code"></i></small>
                                    </div>
                                    <div class="border-right p-1 justify-content-between" style="width: 10%;"><small>Consumption(Hr)</small>
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Consumption in hour"></i></small>
                                    </div>
                                    <div class="border-right p-1" style="width: 10%;">LHR
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Labour hour rate"></i></small>
                                    </div>
                                    <div class="border-right p-1" style="width: 10%;">MHR
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Machine hour rate"></i></small>
                                    </div>
                                    <div class="border-right p-1" style="width: 10%;">Total Cost
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total Cost"></i></small>
                                    </div>
                                    <div class="border-right p-1" style="width: 15%;">Remark
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small>
                                    </div>
                                    <div class="p-1 text-center" style="width: 5%;">
                                        <i class="fa fa-plus bg-success rounded p-1 mt-2 addGoodActivitiesDivItemBtn" style="cursor: pointer;"></i>
                                    </div>
                                </div>
                                <div class="goodActivitiesDiv">
                                    <?php
                                    $rowNoActivity  = 0;
                                    foreach ($bomItemsList as $bomOneItem) {
                                        if ($bomOneItem["bomItemType"] != "activities") continue;
                                        $rowNoActivity++;
                                    ?>
                                        <div class="row m-0 border-top border-bottom justify-content-between">
                                            <div class="border-right p-1" style="width: 15%;">
                                                <select name="goodActivityId[]" id="goodActivityId_<?= $rowNoActivity ?>" class="form-control goodActivityDropDown select2" required>
                                                    <option value="" data-row=""> -- Select Activity -- </option>
                                                    <?php
                                                    foreach (getGoodActivities()["data"] as $key => $itemObj) {
                                                    ?>
                                                        <option value="<?= $itemObj["CostCenter_id"] ?>" data-row="<?= base64_encode(json_encode($itemObj, true)) ?>" <?= $bomOneItem["activityId"] == $itemObj["CostCenter_id"] ? "selected" : "" ?>><?= $itemObj["CostCenter_code"] . ' - ' . $itemObj["CostCenter_desc"] ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 15%;">
                                                <span id="goodActivityCode_<?= $rowNoActivity ?>"><?= $bomOneItem["CostCenter_code"] ?></span>
                                            </div>
                                            <div class="border-right p-1" style="width: 10%;">
                                                <select name="goodActivityItemGl[]" id="goodActivityItemGl_<?= $rowNoActivity ?>" class="form-control goodActivityItemGlDropDown select2" required>
                                                    <option value="" data-row=""> -- Select Gl Code -- </option>
                                                    <?php
                                                    foreach ($coaObj["data"] as $itemObj) {
                                                    ?>
                                                        <option value="<?= $itemObj["id"] ?>" <?= $itemObj["id"] == $bomOneItem["itemGl"] ? "selected" : "" ?>><?= $itemObj["gl_code"] . ' - ' . $itemObj["gl_label"] ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="border-right p-1" style="width: 10%;">
                                                <input type="text" value="<?= $bomOneItem["activityConsumption"] ?>" name="goodActivityConsumption[]" id="goodActivityConsumption_<?= $rowNoActivity ?>" placeholder="Activity Consumption" class="form-control m-0 goodActivityConsumption">
                                            </div>
                                            <div class="border-right p-1" style="width: 10%;">
                                                <input type="text" value="<?= $bomOneItem["activityLhr"] ?>" name="goodActivityLhr[]" id="goodActivityLhr_<?= $rowNoActivity ?>" placeholder="Activity LHR" class="form-control m-0 goodActivityLhr">
                                            </div>
                                            <div class="border-right p-1" style="width: 10%;">
                                                <input type="text" value="<?= $bomOneItem["activityMhr"] ?>" name="goodActivityMhr[]" id="goodActivityMhr_<?= $rowNoActivity ?>" placeholder="Activity MHR" class="form-control m-0 goodActivityMhr">
                                            </div>
                                            <div class="border-right p-1" style="width: 10%;">
                                                <input step="any" type="number" value="<?= $bomOneItem["amount"] ?>" step="any" name="goodActivityAmount[]" id="goodActivityAmount_<?= $rowNoActivity ?>" placeholder="Total Amount" class="form-control m-0 goodActivityAmount" readonly>
                                            </div>
                                            <div class="border-right p-1" style="width: 15%; height: 100%;">
                                                <textarea name="goodActivityRemark[]" placeholder="Activity Remark" rows="1" class="form-control m-0 p-2"><?= $bomOneItem["remarks"] ?></textarea>
                                            </div>
                                            <div class="text-center" style="width: 5%;">
                                                <i class="fa fa-minus bg-danger rounded p-1 mt-2 removeGoodActivitiesDivItemBtn" style="cursor: pointer;"></i>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="row m-0 p-0 bg-secondary"><small class="ml-2" style="font-size: 0.7em;">Others</small></div>
                                <div class="row m-0 border-top border-bottom justify-content-between font-weight-bold text-light" style="background-color: #003060;">
                                    <div class="border-right p-1 justify-content-between" style="width: 50%;">Other Details
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Other details"></i></small>
                                    </div>
                                    <div class="border-right p-1" style="width: 20%;">Select GL
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Select GL for this other cost"></i></small>
                                    </div>
                                    <div class="border-right p-1" style="width: 10%;">Amount
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Total Cost"></i></small>
                                    </div>
                                    <div class="border-right p-1" style="width: 15%;">Remark
                                        <small class="bg-secondary px-1 rounded"><i class="fa fa-info" data-toggle="tooltip" data-placement="top" title="Write any remarks for future refarance"></i></small>
                                    </div>
                                    <div class="p-1 text-center" style="width: 5%;"><i class="fa fa-plus bg-success rounded p-1 mt-2 addGoodOthersDivItemBtn" style="cursor: pointer;"></i></div>
                                </div>
                                <div class="goodOthersDiv">
                                    <?php
                                    $rowNoOthers  = 0;
                                    foreach ($bomItemsList as $bomOneItem) {
                                        if ($bomOneItem["bomItemType"] != "others") continue;
                                        $rowNoOthers++;
                                    ?>
                                        <div class="row m-0 border-top border-bottom justify-content-between">
                                            <div class="border-right p-1" style="width: 50%;">
                                                <input type="text" value="<?= $bomOneItem["othersItem"] ?>" name="goodOthersItem[]" id="goodOthersItem_<?= $rowNoOthers ?>" placeholder="Enter Other Details" class="form-control m-0">
                                            </div>
                                            <div class="border-right p-1" style="width: 20%;">
                                                <select name="goodOthersItemGl[]" id="goodOthersItemGl_<?= $rowNoOthers ?>" class="form-control goodOthersItemGlDropDown select2" required>
                                                    <option value="" data-row=""> -- Select Gl Code -- </option>
                                                    <?php
                                                    foreach ($coaObj["data"] as $itemObj) {
                                                    ?>
                                                        <option value="<?= $itemObj["id"] ?>" <?= $itemObj["id"] == $bomOneItem["itemGl"] ? "selected" : "" ?>><?= $itemObj["gl_code"] . ' - ' . $itemObj["gl_label"] ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="border-right p-1" style="width: 10%;">
                                                <input step="any" type="number" value="<?= $bomOneItem["amount"] ?>" step="any" name="goodOthersAmount[]" id="goodOthersAmount_<?= $rowNoOthers ?>" placeholder="Other Amount" class="form-control m-0">
                                            </div>
                                            <div class="border-right p-1" style="width: 15%; height: 100%;">
                                                <textarea name="goodOthersRemark[]" placeholder="Other Remark" rows="1" class="form-control m-0 p-2"><?= $bomOneItem["remarks"] ?></textarea>
                                            </div>
                                            <div class="text-center" style="width: 5%;">
                                                <i class="fa fa-minus bg-danger rounded p-1 mt-2 removeGoodOthersDivItemBtn" style="cursor: pointer;"></i>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer m-0 p-0 text-right">
                            <button type="submit" value="Save" name="editBomSubmitBtn" class="btn btn-primary text-light my-3">Save Bom</button>
                            <a href="<?= basename($_SERVER['PHP_SELF']); ?>"><button class="btn btn-danger">Back</button></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.BOM Form -->
<?php
    } else {
        swalAlert("warning", "Bom Not found!", "Bill of material of this item not exist.", $_SERVER["HTTP_REFERER"]);
    }
}
?>

<script>
    function getTime() {
        return (new Date()).getTime();
    }
    $(document).ready(function() {

        $(".select2").select2();


        $(".clickTheFormSaveBtn").click(function() {
            console.log("Clicked the save button");
            $("#billOfMeterialForm").submit();
        });

        function calculateItemCost(rowNo = null) {
            let rate = $(`#goodItemRate_${rowNo}`).val();
            let qty = $(`#goodItemConsumption_${rowNo}`).val();
            let extra = $(`#goodItemExtraPurchage_${rowNo}`).val();
            let totalQty = parseFloat(qty) + parseFloat(qty * extra / 100);
            let itemAmount = rate * totalQty;
            $(`#goodItemAmount_${rowNo}`).val(itemAmount);
        }

        function calculateActivityCost(rowNo = null) {
            console.log("Calculating activity cost");
            let lhrVal = parseFloat($(`#goodActivityLhr_${rowNo}`).val()) > 0 ? parseFloat($(`#goodActivityLhr_${rowNo}`).val()) : 0;
            let mhrVal = parseFloat($(`#goodActivityMhr_${rowNo}`).val()) > 0 ? parseFloat($(`#goodActivityMhr_${rowNo}`).val()) : 0;
            let lhrMhr = lhrVal + mhrVal;
            $(`#goodActivityLhrMhr_${rowNo}`).val(lhrMhr);
            let activityConsumption = $(`#goodActivityConsumption_${rowNo}`).val();
            let activityCost = lhrMhr * activityConsumption;
            $(`#goodActivityAmount_${rowNo}`).val(activityCost);
        }

        $(document).on("keyup", ".goodItemConsumtion", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateItemCost(rowNo);
        });
        $(document).on("keyup", ".goodItemExtraPurchage", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateItemCost(rowNo);
        });
        $(document).on("keyup", ".goodItemRate", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateItemCost(rowNo);
        });

        $(document).on("keyup", ".goodActivityConsumption", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateActivityCost(rowNo);
        });
        $(document).on("keyup", ".goodActivityLhr", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateActivityCost(rowNo);
        });
        $(document).on("keyup", ".goodActivityMhr", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateActivityCost(rowNo);
        });

        $(document).on("change", ".rmSfgItemsDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));
            // console.log(selectVal);
            // console.log(rowData);
            console.log(rowDataObj);
            $(`#goodItemGl_${rowNo}`).val(rowDataObj["parentGlId"]);
            $(`#goodItemCode_${rowNo}`).html(rowDataObj["itemCode"]);
            $(`#goodItemType_${rowNo}`).html(rowDataObj["type"]);
            $(`#goodItemConsumption_${rowNo}`).val(1);
            $(`#goodItemExtraPurchage_${rowNo}`).val(0);
            $(`#goodItemUOM_${rowNo}`).val(rowDataObj["uomName"]);
            if (rowDataObj["type"] == "SFG") {
                $(`#goodItemRate_${rowNo}`).val(rowDataObj["itemBomPrice"]);
                $(`#goodItemAmount_${rowNo}`).val(rowDataObj["itemBomPrice"]);
            } else {
                $(`#goodItemRate_${rowNo}`).val(rowDataObj["movingWeightedPrice"]);
                $(`#goodItemAmount_${rowNo}`).val(rowDataObj["movingWeightedPrice"]);
            }
        });

        $(document).on("change", ".goodActivityDropDown", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let selectVal = $(this).val();
            let rowData = $(this).find(':selected').data('row');
            let rowDataObj = JSON.parse(atob(rowData));

            let lhr = parseFloat(rowDataObj["labour_hour_rate"]);
            let mhr = parseFloat(rowDataObj["machine_hour_rate"]);

            $(`#goodActivityCode_${rowNo}`).html(rowDataObj["CostCenter_code"]);
            $(`#goodActivityConsumption_${rowNo}`).val(1);
            $(`#goodActivityLhr_${rowNo}`).val(lhr);
            $(`#goodActivityMhr_${rowNo}`).val(mhr);
            $(`#goodActivityLhrMhr_${rowNo}`).val(lhr + mhr);
            $(`#goodActivityAmount_${rowNo}`).val(lhr + mhr);
        });

        function addGoodItemNewRow(rowNo = 0) {
            $(".goodItemsDiv").append(`
                                <div class="row m-0 border-top border-bottom justify-content-between" id="goodItemsDivRow_${rowNo}">
                                    <div class="border-right p-1" style="width: 15%;">
                                        <input type="hidden" name="goodItemGl[]" id="goodItemGl_${rowNo}" value="0" class="form-control">
                                        <select name="goodItemId[]" id="goodItemId_${rowNo}" class="form-control rmSfgItemsDropDown" required>
                                            <option value="" data-row=""> -- Select Item -- </option>
                                            <?php
                                            foreach (getRmSfgItems()["data"] as $key => $itemObj) {
                                                if ($itemObj["itemId"] == $itemId) continue;
                                                echo '<option value="' . $itemObj["itemId"] . '"  data-row="' . base64_encode(json_encode($itemObj, true)) . '"  >' . $itemObj["itemName"] . ' - ' . $itemObj["itemCode"] . ' [' . $itemObj["type"] . ']</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 10%;">
                                        <span id="goodItemCode_${rowNo}"></span>
                                    </div>
                                    <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 5%;">
                                        <span id="goodItemType_${rowNo}"></span>
                                    </div>
                                    <div class="border-right p-1" style="width: 10%;">
                                        <input type="text" name="goodItemConsumption[]" id="goodItemConsumption_${rowNo}" placeholder="Item Consumption" class="form-control m-0 goodItemConsumtion">
                                    </div>
                                    <div class="border-right p-1 d-flex" style="width: 10%;">
                                        <input type="text" name="goodItemExtraPurchage[]" id="goodItemExtraPurchage_${rowNo}" placeholder="Extra Purchase" class="form-control m-0 goodItemExtraPurchage"><span class="text-muted mt-1 ml-1">%</span>
                                    </div>
                                    <div class="border-right p-1" style="width: 10%;">
                                        <input type="text" name="goodItemUOM[]" id="goodItemUOM_${rowNo}" placeholder="Item UOM" class="form-control m-0 goodItemUOM" readonly>
                                    </div>
                                    <div class="border-right p-1" style="width: 10%;">
                                        <input step="any" type="number" step="any" name="goodItemRate[]" id="goodItemRate_${rowNo}" placeholder="Item Rate" class="form-control m-0 goodItemRate">
                                    </div>
                                    <div class="border-right p-1" style="width: 10%;">
                                        <input step="any" type="number" step="any" name="goodItemAmount[]" id="goodItemAmount_${rowNo}" placeholder="Item Amount" class="form-control m-0 goodItemAmount">
                                    </div>
                                    <div class="border-right p-1" style="width: 15%; height: 100%;">
                                        <textarea name="goodItemRemark[]" id="goodItemRemark_${rowNo}" placeholder="Item Remark" rows="1" class="form-control m-0 p-2 goodItemRemark"></textarea>
                                    </div>
                                    <div class="text-center" style="width: 5%;">
                                        ${rowNo==0?`<i class="fa fa-plus bg-success rounded p-1 mt-2 addGoodItemsDivItemBtn" style="cursor: pointer;"></i>`:`<i class="fa fa-minus bg-danger rounded p-1 mt-2 removeGoodItemsDivItemBtn" style="cursor: pointer;"></i>`}
                                    </div>
                                </div>`);

            $(`#goodItemId_${rowNo}`).select2();
        }

        function addGoodActivityNewRow(rowNo = 0) {
            $(".goodActivitiesDiv").append(`
                            <div class="row m-0 border-top border-bottom justify-content-between">
                                <div class="border-right p-1" style="width: 15%;">
                                    <select name="goodActivityId[]" id="goodActivityId_${rowNo}" class="form-control goodActivityDropDown" required>
                                        <option value="" data-row=""> -- Select Activity -- </option>
                                        <?php
                                        foreach (getGoodActivities()["data"] as $key => $itemObj) {
                                            echo '<option value="' . $itemObj["CostCenter_id"] . '"  data-row="' . base64_encode(json_encode($itemObj, true)) . '"  >' . $itemObj["CostCenter_code"] . ' - ' . $itemObj["CostCenter_desc"] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="border-right p-1 row m-0 align-items-center justify-content-center" style="width: 15%;">
                                    <span id="goodActivityCode_${rowNo}"></span>
                                </div>
                                <div class="border-right p-1" style="width: 10%;">
                                    <select name="goodActivityItemGl[]" id="goodActivityItemGl_${rowNo}" class="form-control goodActivityItemGlDropDown" required>
                                        <option value="" data-row=""> -- Select Gl Code -- </option>
                                        <?php
                                        foreach ($coaObj["data"] as $itemObj) {
                                            echo '<option value="' . $itemObj["id"] . '">' . $itemObj["gl_code"] . ' - ' . $itemObj["gl_label"] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="border-right p-1" style="width: 10%;">
                                    <input type="text" name="goodActivityConsumption[]" id="goodActivityConsumption_${rowNo}" placeholder="Activity Consumption" class="form-control m-0 goodActivityConsumption">
                                </div>
                                <div class="border-right p-1" style="width: 10%;">
                                    <input type="text" name="goodActivityLhr[]" id="goodActivityLhr_${rowNo}" placeholder="Activity LHR" class="form-control m-0 goodActivityLhr">
                                </div>
                                <div class="border-right p-1" style="width: 10%;">
                                    <input type="text" name="goodActivityMhr[]" id="goodActivityMhr_${rowNo}" placeholder="Activity MHR" class="form-control m-0 goodActivityMhr">
                                </div>
                                <div class="border-right p-1" style="width: 10%;">
                                    <input step="any" type="number" step="any" name="goodActivityAmount[]" id="goodActivityAmount_${rowNo}" placeholder="Total Amount" class="form-control m-0 goodActivityAmount" readonly>
                                </div>
                                <div class="border-right p-1" style="width: 15%; height: 100%;">
                                    <textarea name="goodActivityRemark[]" placeholder="Activity Remark" rows="1" class="form-control m-0 p-2"></textarea>
                                </div>
                                <div class="text-center" style="width: 5%;">
                                    ${rowNo==0?`<i class="fa fa-plus bg-success rounded p-1 mt-2 addGoodActivitiesDivItemBtn" style="cursor: pointer;"></i>`:`<i class="fa fa-minus bg-danger rounded p-1 mt-2 removeGoodActivitiesDivItemBtn" style="cursor: pointer;"></i>`}
                                </div>
                            </div>`);

            $(`#goodActivityId_${rowNo}`).select2();
            $(`#goodActivityItemGl_${rowNo}`).select2();
        }

        function addGoodOtherItemsNewRow(rowNo = 0) {
            $(".goodOthersDiv").append(`
                            <div class="row m-0 border-top border-bottom justify-content-between">
                                <div class="border-right p-1" style="width: 50%;">
                                    <input type="text" name="goodOthersItem[]" id="goodOthersItem_${rowNo}" placeholder="Enter Other Details" class="form-control m-0">
                                </div>
                                <div class="border-right p-1" style="width: 20%;">
                                    <select name="goodOthersItemGl[]" id="goodOthersItemGl_${rowNo}" class="form-control goodOthersItemGlDropDown" required>
                                        <option value="" data-row=""> -- Select Gl Code -- </option>
                                        <?php
                                        foreach ($coaObj["data"] as $itemObj) {
                                            echo '<option value="' . $itemObj["id"] . '">' . $itemObj["gl_code"] . ' - ' . $itemObj["gl_label"] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="border-right p-1" style="width: 10%;">
                                    <input step="any" type="number" step="any" name="goodOthersAmount[]" id="goodOthersAmount_${rowNo}" placeholder="Other Amount" class="form-control m-0">
                                </div>
                                <div class="border-right p-1" style="width: 15%; height: 100%;">
                                    <textarea name="goodOthersRemark[]" placeholder="Other Remark" rows="1" class="form-control m-0 p-2"></textarea>
                                </div>
                                <div class="text-center" style="width: 5%;">
                                    ${rowNo==0?`<i class="fa fa-plus bg-success rounded p-1 mt-2 addGoodOthersDivItemBtn" style="cursor: pointer;"></i>`:`<i class="fa fa-minus bg-danger rounded p-1 mt-2 removeGoodOthersDivItemBtn" style="cursor: pointer;"></i>`}
                                </div>
                            </div>`);

            $(`#goodOthersItemGl_${rowNo}`).select2();
        }

        // adding good items to bom list
        // addGoodItemNewRow();
        var goodItemRowNo = <?= $rowNoGoods ?? 0 ?>;
        $(document).on("click", ".addGoodItemsDivItemBtn", function() {
            addGoodItemNewRow(goodItemRowNo += 1);
        });

        // adding good activity or cost center to bom list
        // addGoodActivityNewRow();
        var goodActivityRowNo = <?= $rowNoActivity ?? 0 ?>;
        $(document).on("click", ".addGoodActivitiesDivItemBtn", function() {
            addGoodActivityNewRow(goodActivityRowNo += 1);
        });

        // adding other good items to bom list
        // addGoodOtherItemsNewRow();
        var goodOtherItemsRowNo = <?= $rowNoOthers ?>;
        $(document).on("click", ".addGoodOthersDivItemBtn", function() {
            addGoodOtherItemsNewRow(goodOtherItemsRowNo += 1);
        });



        // removing bom good items, activity and others from bom list
        $(document).on("click", ".removeGoodItemsDivItemBtn", function() {
            let elm = $(this).parent().parent().remove();
        });
        $(document).on("click", ".removeGoodActivitiesDivItemBtn", function() {
            let elm = $(this).parent().parent().remove();
        });
        $(document).on("click", ".removeGoodOthersDivItemBtn", function() {
            let elm = $(this).parent().parent().remove();
        });

    });
</script>