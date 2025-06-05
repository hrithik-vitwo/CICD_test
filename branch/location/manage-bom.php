<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-bom-controller.php");
require_once("../../app/v1/functions/branch/func-goods-controller.php");

$goodsBomController = new GoodsBomController();
$goodsController = new GoodsController();

function getRmSfgItems()
{
    global $location_id;
    $sql = 'SELECT
                items.itemId,
                items.itemName,
                items.itemCode,
                items.parentGlId,
                itemTypes.type,
                itemUom.uomName,
                COALESCE(summary.movingWeightedPrice,0.00) AS movingWeightedPrice,
                COALESCE(itemBom.cogm,0.00) AS itemBomPrice
            FROM
                `erp_inventory_stocks_summary` AS summary
            INNER JOIN `erp_inventory_items` AS items
            ON
                summary.`itemId` = items.`itemId`
            INNER JOIN `erp_inventory_mstr_good_types` AS itemTypes
            ON
                items.`goodsType` = itemTypes.`goodTypeId`
            LEFT JOIN `erp_inventory_mstr_uom` AS itemUom
            ON
                items.`baseUnitMeasure` = itemUom.`uomId`
            LEFT JOIN `erp_bom` AS itemBom
            ON
                items.itemId = itemBom.itemId AND summary.`location_id` = itemBom.`locationId`
            WHERE
                summary.`location_id` = ' . $location_id . ' AND(
                    itemTypes.`type` = "RM" OR itemTypes.`type` = "SFG"
                )';

    return queryGet($sql, true);
}

function getGoodActivities()
{
    global $location_id;
    global $branch_id;
    global $company_id;
    $sql = 'SELECT
                `CostCenter_id`,
                `CostCenter_code`,
                `CostCenter_desc`,
                `labour_hour_rate`,
                `machine_hour_rate`,
                `gl_code`,
                `parent_id`,
                `type`
            FROM
                `erp_cost_center`
            WHERE
                `CostCenter_status` = "active" AND `company_id` = ' . $company_id . '
            ORDER BY
                `CostCenter_id`
            DESC';

    return queryGet($sql, true);
}
function getWorkCenter()
{

    global $location_id;
    global $branch_id;
    global $company_id;
    $sql = 'SELECT
                `work_center_id`,
                `work_center_code`,
                `work_center_description`,
                `wc_lhr`,
                `wc_mhr`,
                `work_center_name`
            FROM
                `erp_work_center`
            WHERE
                `status` = "active" AND `company_id` = ' . $company_id . '
            ORDER BY
                `work_center_id`
            DESC';

    return queryGet($sql, true);
}

$coaObj = getAllChartOfAccounts_list_by_p($company_id, 4);

if (isset($_POST["addCOGSFormSubmitBtn"])) {
    // console($_POST);
    $createCogsObj = $goodsBomController->createBomCOGS($_POST);
    swalToast($createCogsObj["status"], $createCogsObj["message"]);
}

if (isset($_POST["releaseBom"])) {
    $bomId = base64_decode($_POST["releaseBom"]);
    $updateCurrentBomItemPriceObj = $goodsBomController->updateCurrentBomItemPrice($bomId);
    // console($updateCurrentBomItemPriceObj);
    swalToast($updateCurrentBomItemPriceObj["status"], $updateCurrentBomItemPriceObj["message"]);
}


?>
<!-- <link rel="stylesheet" href="../../public/assets/sales-order.css"> -->
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
    .bom-modal .modal-dialog {
        max-width: 100%;
        width: 50%;
    }

    .bom-modal .modal-header {
        height: auto;
    }

    .bom-modal .modal-body {
        width: 100%;
        top: -30px;
    }

    .bom-modal .modal-body .card .card-body {
        padding: 15px 0 0px;
    }

    .bom-modal .modal-body .card .card-body table {
        margin-bottom: 20px;
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">

            <!-- Create Bom -->
            <?php if (isset($_GET["create"]) && $_GET["create"] != "") : ?>
                <!-- All massages, logics, consoles  -->
                <div class="row m-0 p-0 messages justify-content-end">
                    <?php
                    $itemId = base64_decode($_GET["create"]);
                    $goodsDetailsObj = $goodsController->getGoodsDeatils($itemId);
                    // console($goodsDetailsObj);
                    $goodTitle = $goodsDetailsObj["data"]["itemName"] ?? "";
                    $goodCode = $goodsDetailsObj["data"]["itemCode"] ?? "";
                    $rmGoodsObj = $goodsController->getAllRMGoods();
                    ?>
                </div>
                <!-- /.All massages, logics, consoles -->
                <?php
                if (isset($_POST["createBomSubmitBtn"])) {
                    // console($_POST);
                    $createBomObj = $goodsBomController->createBom($_POST);
                    if ($createBomObj["status"] == "success") {
                        swalAlert($createBomObj["status"], ucfirst($createBomObj["status"]), $createBomObj["message"], LOCATION_URL . "manage-bom.php");
                    } else {
                        swalAlert($createBomObj["status"], ucfirst($createBomObj["status"]), $createBomObj["message"]);
                    }

                    //console($createBomObj);
                } else {
                    if (!$goodsBomController->isBomCreated($itemId)) { ?>
                        <!-- BOM Form -->
                        <div class="card p-0 bom-form-card">
                            <form action="" method="post" id="billOfMeterialForm">
                                <div class="card-header p-2 h5 text-light">Create Bill Of Meterial</div>
                                <div class="card-body p-2">
                                    <div>
                                        <?php
                                        // console($coaObj["data"]);
                                        ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <span>Prepared </span>
                                            <input type="hidden" name="preparedBy" value="<?= $_SESSION["logedBranchAdminInfo"]["adminId"] ?? ""; ?>">
                                            <input type="hidden" name="itemId" value="<?= $itemId ?>">
                                            <input type="text" value="<?= $_SESSION["logedBranchAdminInfo"]["adminName"] ?? ""; ?>" placeholder="Created by" class="form-control" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <span>Prepared Date</span>
                                            <input type="date" value="<?= date("Y-m-d"); ?>" name="preparedDate" class="form-control" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <span>Good Title</span>
                                            <input type="text" value="<?= $goodTitle ?>" placeholder="Good title" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
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
                                                    <div class="p-1 text-center" style="width: 5%;"><i class="fa fa-action"></i></div>
                                                </div>
                                                <div class="goodItemsDiv">

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
                                                    <div class="p-1 text-center" style="width: 5%;"><i class="fa fa-action"></i></div>
                                                </div>
                                                <div class="goodActivitiesDiv">

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
                                                    <div class="p-1 text-center" style="width: 5%;"><i class="fa fa-action"></i></div>
                                                </div>
                                                <div class="goodOthersDiv">

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
                <?php
                    } else {
                        swalAlert("warning", "Bom Already Created!", "Bill of material of this item already exist. Click okay to view BOM.", LOCATION_URL . "manage-bom.php?view=" . $_GET["create"]);
                    }
                }
                ?>

                <script>
                    function getTime() {
                        return (new Date()).getTime();
                    }
                    $(document).ready(function() {
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
                                        <input step="0.01" type="number" step="any" name="goodItemRate[]" id="goodItemRate_${rowNo}" placeholder="Item Rate" class="form-control m-0 goodItemRate">
                                    </div>
                                    <div class="border-right p-1" style="width: 10%;">
                                        <input step="0.01" type="number" step="any" name="goodItemAmount[]" id="goodItemAmount_${rowNo}" placeholder="Item Amount" class="form-control m-0 goodItemAmount">
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
                                    <input step="0.01" type="number" step="any" name="goodActivityAmount[]" id="goodActivityAmount_${rowNo}" placeholder="Total Amount" class="form-control m-0 goodActivityAmount" readonly>
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
                                    <input step="0.01" type="number" step="any" name="goodOthersAmount[]" id="goodOthersAmount_${rowNo}" placeholder="Other Amount" class="form-control m-0">
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
                        addGoodItemNewRow();
                        var goodItemRowNo = 0;
                        $(document).on("click", ".addGoodItemsDivItemBtn", function() {
                            addGoodItemNewRow(goodItemRowNo += 1);
                        });

                        // adding good activity or cost center to bom list
                        addGoodActivityNewRow();
                        var goodActivityRowNo = 0;
                        $(document).on("click", ".addGoodActivitiesDivItemBtn", function() {
                            addGoodActivityNewRow(goodActivityRowNo += 1);
                        });

                        // adding other good items to bom list
                        addGoodOtherItemsNewRow();
                        var goodOtherItemsRowNo = 0;
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

                <!-- Create Bom -->
            <?php elseif (isset($_GET["editBom"]) && $_GET["editBom"] != "") : ?>
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
                                                                <input step="0.01" type="number" step="any" name="goodItemRate[]" id="goodItemRate_<?= $rowNoGoods ?>" placeholder="Item Rate" class="form-control m-0 goodItemRate" value="<?= $bomOneItem["itemRate"] ?>">
                                                            </div>
                                                            <div class="border-right p-1" style="width: 10%;">
                                                                <input step="0.01" type="number" step="any" name="goodItemAmount[]" id="goodItemAmount_<?= $rowNoGoods ?>" placeholder="Item Amount" class="form-control m-0 goodItemAmount" value="<?= $bomOneItem["amount"] ?>">
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
                                                                <input step="0.01" type="number" value="<?= $bomOneItem["amount"] ?>" step="any" name="goodActivityAmount[]" id="goodActivityAmount_<?= $rowNoActivity ?>" placeholder="Total Amount" class="form-control m-0 goodActivityAmount" readonly>
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
                                                                <input step="0.01" type="number" value="<?= $bomOneItem["amount"] ?>" step="any" name="goodOthersAmount[]" id="goodOthersAmount_<?= $rowNoOthers ?>" placeholder="Other Amount" class="form-control m-0">
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
                                        <input step="0.01" type="number" step="any" name="goodItemRate[]" id="goodItemRate_${rowNo}" placeholder="Item Rate" class="form-control m-0 goodItemRate">
                                    </div>
                                    <div class="border-right p-1" style="width: 10%;">
                                        <input step="0.01" type="number" step="any" name="goodItemAmount[]" id="goodItemAmount_${rowNo}" placeholder="Item Amount" class="form-control m-0 goodItemAmount">
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
                                    <input step="0.01" type="number" step="any" name="goodActivityAmount[]" id="goodActivityAmount_${rowNo}" placeholder="Total Amount" class="form-control m-0 goodActivityAmount" readonly>
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
                                    <input step="0.01" type="number" step="any" name="goodOthersAmount[]" id="goodOthersAmount_${rowNo}" placeholder="Other Amount" class="form-control m-0">
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

                <!-- View Bom -->
            <?php elseif (isset($_GET["view"]) && $_GET["view"] != "") : ?>

                <?php
                $itemId = base64_decode($_GET["view"]);
                if (!$goodsBomController->isBomCreated($itemId)) {
                    swalAlert("warning", "BOM Not Created", "Bill of material is not created yet, Please click OK to create BOM.", LOCATION_URL . "manage-bom.php?create=" . $_GET["view"]);
                } else {
                    // $bomDetailsObj=$goodsBomController->getBomAndItemDetails($itemId);
                    // console($bomDetailsObj);

                    $bomAndAllItemsObj = $goodsBomController->getBomAndAllItems($itemId);
                    $bomDetails = $bomAndAllItemsObj["data"]["bomDetails"] ?? [];
                    $bomItemsList = $bomAndAllItemsObj["data"]["bomItemDetails"] ?? [];
                    // console($bomItemsList);
                ?>
                    <div class="col-12 mt-2 p-0">
                        <div class="p-0 pt-1 my-2">
                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                    <h3 class="card-title">View BOM</h3>
                                    <span>[<?= $bomDetails["itemCode"] ?>] <?= $bomDetails["itemName"] ?></span>
                                    <div style="display: inline-flex;">
                                        <form action="" method="post">
                                            <input type="hidden" name="releaseBom" value="<?= base64_encode($bomDetails["bomId"]) ?>">
                                            <button type="submit" name="releaseBomFrmSbmit" class="btn btn-sm btn-primary">Update Price</button>
                                        </form>
                                        <a href="manage-bom.php?editBom=<?= base64_encode($bomDetails["bomId"]) ?>" class="btn btn-sm btn-primary ml-2">Change Items</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="">
                            <div class="card">
                                <div class="card-body">
                                    <p class="text-left m-0 pl-3 pb-2 font-bold">Items</p>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th class="borderNone">Item Code</th>
                                                <th class="borderNone">Item Title</th>
                                                <th class="borderNone">Consumption</th>
                                                <th class="borderNone">Extra</th>
                                                <th class="borderNone">UOM</th>
                                                <th class="borderNone">Item Rate</th>
                                                <th class="borderNone">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($bomItemsList as $bomOneItem) {
                                                if ($bomOneItem["bomItemType"] == "goods") {
                                                    // goods items list
                                            ?>
                                                    <tr>
                                                        <td><?= $bomOneItem["itemCode"] ?></td>
                                                        <td><?= $bomOneItem["itemName"] ?></td>
                                                        <td><?= $bomOneItem["itemConsumption"] ?></td>
                                                        <td><?= $bomOneItem["itemExtraPurchage"] ?></td>
                                                        <td><?= $bomOneItem["itemUOM"] ?></td>
                                                        <td><?= $bomOneItem["itemRate"] ?></td>
                                                        <td><?= $bomOneItem["amount"] ?></td>
                                                    </tr>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    <p class="text-left m-0 pl-3 pb-2 font-bold">Activities</p>
                                    <table class="table mb-3">
                                        <thead>
                                            <tr>
                                                <th class="borderNone">Activity Code</th>
                                                <th class="borderNone">Activity Name</th>
                                                <th class="borderNone">Gl Code</th>
                                                <th class="borderNone">Consumption</th>
                                                <th class="borderNone">LHR</th>
                                                <th class="borderNone">MHR</th>
                                                <th class="borderNone">Amount</th>
                                                <th class="borderNone">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($bomItemsList as $bomOneItem) {
                                                if ($bomOneItem["bomItemType"] == "activities") {
                                                    // goods activity items list
                                            ?>
                                                    <tr>
                                                        <td><?= $bomOneItem["CostCenter_code"] ?></td>
                                                        <td><?= $bomOneItem["CostCenter_desc"] ?></td>
                                                        <td><?= $bomOneItem["itemGl"] ?></td>
                                                        <td><?= $bomOneItem["activityConsumption"] ?></td>
                                                        <td><?= $bomOneItem["activityLhr"] ?></td>
                                                        <td><?= $bomOneItem["activityMhr"] ?></td>
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
                                    <p class="text-left m-0 pl-3 pb-2 font-bold">Others</p>
                                    <table class="table mb-3">
                                        <thead>
                                            <tr>
                                                <th class="borderNone">Others Item</th>
                                                <th class="borderNone">Gl Code</th>
                                                <th class="borderNone">Amount</th>
                                                <th class="borderNone">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($bomItemsList as $bomOneItem) {
                                                if ($bomOneItem["bomItemType"] == "others") {
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

                                <script>
                                    $(document).ready(function() {
                                        function addOtherAddonsFormItem(rowNo = 0) {
                                            $("#otherAddonsForm").append(`
                                            <tr id="otherAddonItemTr_${rowNo}">
                                                <td>
                                                    <input class="form-control mt-2 mb-2" type="text" name="bomOtherAddonItemName[]" id="bomOtherAddonItemName_${rowNo}" placeholder="Item Name" required />
                                                </td>
                                                <td>
                                                    <select name="bomOtherAddonItemGl[]" id="bomOtherAddonItemGl_${rowNo}" class="form-control bomOtherAddonItemGlDropDown" required>
                                                        <option value="" data-row=""> -- Select Gl Code -- </option>
                                                        <?php
                                                        foreach ($coaObj["data"] as $itemObj) {
                                                            echo '<option value="' . $itemObj["id"] . '">' . $itemObj["gl_code"] . ' - ' . $itemObj["gl_label"] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input step="0.01" class="form-control mt-2 mb-2" type="number" name="bomOtherAddonItemPrice[]" id="bomOtherAddonItemPrice_${rowNo}" placeholder="Item Price" required />
                                                </td>
                                                <td>
                                                    <input class="form-control mt-2 mb-2" type="text" name="bomOtherAddonItemRemarks[]" id="bomOtherAddonItemRemarks_${rowNo}" placeholder="Item remarks" />
                                                </td>
                                                <td>
                                                    ${rowNo==0?`<i class="fa fa-plus bg-success rounded p-1 mt-2 addOtherAddonItemBtn" style="cursor: pointer;"></i>`:`<i class="fa fa-minus bg-danger rounded p-1 mt-2 removeOtherAddonItemBtn" style="cursor: pointer;"></i>`}
                                                </td>
                                            </tr>`);

                                            $(`#bomOtherAddonItemGl_${rowNo}`).select2();
                                        }

                                        addOtherAddonsFormItem(rowNo = 0);
                                        // adding other addon items to bom list
                                        var otherAddonItemsRowNo = 0;
                                        $(document).on("click", ".addOtherAddonItemBtn", function() {
                                            addOtherAddonsFormItem(otherAddonItemsRowNo += 1);
                                        });

                                        // removing bom good items, activity and others from bom list
                                        $(document).on("click", ".removeOtherAddonItemBtn", function() {
                                            let elm = $(this).parent().parent().remove();
                                        });
                                    });
                                </script>
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
                <?php
                }
                ?>



                <!-- Bom List -->
            <?php else : ?>

                <div class="row p-0 m-0">
                    <?php
                    $itemIdForBom = 0;
                    if (isset($_GET["item"]) && $_GET["item"] != "") {
                        $itemIdForBom = base64_decode($_GET["item"]);
                    }
                    $bomListObj = $goodsBomController->getAllBoms($itemIdForBom);
                    ?>
                    <div class="col-12 mt-2 p-0">
                        <div class="p-0 pt-1 my-2">
                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                    <h3 class="card-title">Manage BOM</h3>
                                    <span>
                                        <?php ($itemIdForBom > 0) ? '<a href="' . LOCATION_URL . 'goods.php" class="btn btn-sm btn-primary">Go Back</a>' : ""; ?>
                                        <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary"><i class="fa fa-plus" style="margin-right: 0;"></i> Create</a>
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
                                        <th class="borderNone">Item Code</th>
                                        <th class="borderNone">Item Name</th>
                                        <th class="borderNone">Item Type</th>
                                        <th class="borderNone">Prepared Date</th>
                                        <th class="borderNone">COGM</th>
                                        <th class="borderNone">COGS</th>
                                        <th class="borderNone">MSP</th>
                                        <th class="borderNone">Progress Status</th>
                                        <th class="borderNone">Status</th>
                                        <th class="borderNone">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // console($bomListObj);
                                    if ($bomListObj["status"] == "success") {
                                        $sl = 0;
                                        foreach ($bomListObj["data"] as $oneBomRow) {
                                            $sl++;
                                            // $goodsDetailsObj = $goodsController->getGoodsDeatils($oneBomRow["itemId"]);
                                            $goodTitle = $oneBomRow["itemName"];
                                            $goodCode = $oneBomRow["itemCode"];
                                    ?>
                                            <tr>
                                                <td><?= $oneBomRow["itemCode"] ?></td>
                                                <td><?= $oneBomRow["itemName"] ?></td>
                                                <td><?= $oneBomRow["itemType"] ?></td>
                                                <td><?= $oneBomRow["preparedDate"] ?></td>
                                                <td><?= $oneBomRow["cogm"] ?></td>
                                                <td><?= $oneBomRow["cogs"] ?></td>
                                                <td><?= $oneBomRow["msp"] ?></td>
                                                <td><?= $oneBomRow["bomProgressStatus"] ?></td>
                                                <td><?= ucfirst($oneBomRow["bomStatus"]) ?></td>
                                                <td>
                                                    <a style="cursor: pointer" class="btn btn-sm" href="manage-bom.php?view=<?= base64_encode($oneBomRow["itemId"]) ?>"><i class="fa fa-eye po-list-icon"></i></a>
                                                    <!-- <a style="cursor: pointer" class="btn btn-sm" data-toggle="modal" data-target="#bomItemModal_<?= $sl ?>"><i class="fa fa-eye po-list-icon"></i></a>
                                                    <div class="modal fade right customer-modal bom-modal" id="bomItemModal_<?= $sl ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-right" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header" style="height: 110px;">
                                                                    <h5 class="modal-title text-white mt-3 text-right" id="exampleModalLongTitle"><?= ucfirst($goodTitle) ?></h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>

                                                                <div class="modal-body p-3 pb-0">
                                                                    <div class="">
                                                                        <?php
                                                                        $bomItemsObj = $goodsBomController->getBomItemsAndDetails($oneBomRow["bomId"]);
                                                                        ?>
                                                                        <div class="card">
                                                                            <div class="card-body">
                                                                                <p class="text-left m-0 pl-3 pb-2 font-bold">Items</p>
                                                                                <table class="table">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th class="borderNone">Item Id</th>
                                                                                            <th class="borderNone">Consumption</th>
                                                                                            <th class="borderNone">Extra</th>
                                                                                            <th class="borderNone">UOM</th>
                                                                                            <th class="borderNone">Item Rate</th>
                                                                                            <th class="borderNone">Amount</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <?php
                                                                                        foreach ($bomItemsObj["data"] as $bomOneItem) {
                                                                                            if ($bomOneItem["bomItemType"] == "goods") {
                                                                                                // goods items list
                                                                                        ?>
                                                                                                <tr>
                                                                                                    <td><?= $bomOneItem["itemId"] ?></td>
                                                                                                    <td><?= $bomOneItem["itemConsumption"] ?></td>
                                                                                                    <td><?= $bomOneItem["itemExtraPurchage"] ?></td>
                                                                                                    <td><?= $bomOneItem["itemUOM"] ?></td>
                                                                                                    <td><?= $bomOneItem["itemRate"] ?></td>
                                                                                                    <td><?= $bomOneItem["amount"] ?></td>
                                                                                                </tr>
                                                                                        <?php
                                                                                            }
                                                                                        }
                                                                                        ?>
                                                                                    </tbody>
                                                                                </table>
                                                                                <p class="text-left m-0 pl-3 pb-2 font-bold">Activities</p>
                                                                                <table class="table mb-3">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th class="borderNone">Activity Id</th>
                                                                                            <th class="borderNone">Consumption</th>
                                                                                            <th class="borderNone">LHR</th>
                                                                                            <th class="borderNone">MHR</th>
                                                                                            <th class="borderNone">Amount</th>
                                                                                            <th class="borderNone">Remarks</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <?php
                                                                                        foreach ($bomItemsObj["data"] as $bomOneItem) {
                                                                                            if ($bomOneItem["bomItemType"] == "activities") {
                                                                                                // goods activity items list
                                                                                        ?>
                                                                                                <tr>
                                                                                                    <td><?= $bomOneItem["activityId"] ?></td>
                                                                                                    <td><?= $bomOneItem["activityConsumption"] ?></td>
                                                                                                    <td><?= $bomOneItem["activityLhr"] ?></td>
                                                                                                    <td><?= $bomOneItem["activityMhr"] ?></td>
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
                                                                                <p class="text-left m-0 pl-3 pb-2 font-bold">Others</p>
                                                                                <table class="table mb-3">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th class="borderNone">Others Item</th>
                                                                                            <th class="borderNone">Amount</th>
                                                                                            <th class="borderNone">Remarks</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <?php
                                                                                        foreach ($bomItemsObj["data"] as $bomOneItem) {
                                                                                            if ($bomOneItem["bomItemType"] == "others") {
                                                                                                // goods other item list
                                                                                        ?>
                                                                                                <tr>
                                                                                                    <td><?= $bomOneItem["othersItem"] ?></td>
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

                                                                        <?php

                                                                        if ($oneBomRow["bomProgressStatus"] == "COGM") {
                                                                        ?>

                                                                            <div class="card">
                                                                                <div class="card-body">
                                                                                    <p class="text-left m-0 pl-3 pb-2 font-bold">Other Addons</p>
                                                                                    <form action="" method="post">
                                                                                        <input type="hidden" name="bomId" value="<?= $oneBomRow["bomId"] ?>">
                                                                                        <table class="table mb-3">
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th class="borderNone">Others</th>
                                                                                                    <th class="borderNone">Amount</th>
                                                                                                    <th class="borderNone">Remarks</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td><input class="form-control mt-2 mb-2" type="text" name="bomOtherAddonItemName[]" placeholder="Item Name" required /></td>
                                                                                                    <td><input class="form-control mt-2 mb-2" type="number" name="bomOtherAddonItemPrice[]" placeholder="Item Price" required /></td>
                                                                                                    <td><input class="form-control mt-2 mb-2" type="text" name="bomOtherAddonItemRemarks[]" placeholder="Item remarks" /></td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                        <button type="submit" name="addCOGSFormSubmitBtn" class="btn btn-sm btn-primary text-xs mb-4 mr-3 float-right" value="Create COGM">Create COGM</button>
                                                                                    </form>

                                                                                </div>
                                                                            </div>


                                                                        <?php
                                                                        } elseif ($oneBomRow["bomProgressStatus"] == "COGS") {
                                                                        ?>
                                                                            <div class="card">
                                                                                <div class="card-body">
                                                                                    <p class="text-left m-0 pl-3 pb-2 font-bold">COGS Items </p>
                                                                                    <table class="table mb-3">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th class="borderNone">Name</th>
                                                                                                <th class="borderNone">Amount</th>
                                                                                                <th class="borderNone">Remarks</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            <?php
                                                                                            foreach ($bomItemsObj["data"] as $bomOneItem) {
                                                                                                if ($bomOneItem["bomItemType"] == "othersCogs") {
                                                                                                    // goods other item list
                                                                                            ?>
                                                                                                    <tr>
                                                                                                        <td><?= $bomOneItem["othersItem"] ?></td>
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
                                                                                                <td><input type="number" class="form-control mt-2 mb-2" name="bomMargin" placeholder="Bom margings" /></td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        <?php
                                                                        } ?>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                    <button type="button" class="btn btn-primary">Save changes</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->
                                                </td>

                                            </tr>

                                    <?php }
                                    }
                                    ?>
                                </tbody>

                            </table>

                        </div>
                    </div>

                </div>
            <?php endif; ?>
        </div>
    </section>
</div>


<?php
require_once("../common/footer.php");
?>