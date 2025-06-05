<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/common/func-gl-summary-new.php");
// console($_POST);
?>

<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/listing.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<script src="<?= BASE_URL ?>public/assets/simple-tree-table/dist/jquery-simple-tree-table.js"></script>
<style>
    .circlePercent {
        position: relative;
        top: 26px;
        left: 26px;
        width: 96px;
        height: 96px;
        border-radius: 50%;
        background: orange;
    }

    .circlePercent:before,
    .circlePercent>.progressEnd {
        position: absolute;
        z-index: 3;
        top: 2px;
        left: 45px;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: white;
        -ms-transform-origin: 3px 46px;
        transform-origin: 3px 46px;
        content: "";
    }

    .circlePercent:after,
    .circlePercent>.progress {
        position: absolute;
        -ms-transform-origin: 48px 48px;
        transform-origin: 48px 48px;
        z-index: 0;
        top: 0;
        left: 0;
        width: 48px;
        height: 96px;
        border-radius: 48px 0 0 48px;
        background: orange;
        content: "";
    }

    .circlePercent.fiftyPlus:after {
        background: white;
        -ms-transform: rotate(180deg);
        transform: rotate(180deg);
    }

    .circlePercent>.progress.progress {
        background: white;
    }

    .circlePercent>.counter {
        position: absolute;
        box-sizing: border-box;
        z-index: 2;
        width: 100px;
        height: 100px;
        margin-top: -2px;
        margin-left: -2px;
        border-radius: 50%;
        border: 4px solid orange;
    }

    .circlePercent>.counter:before {
        position: absolute;
        z-index: 1;
        top: 50%;
        margin-top: -13px;
        width: 100%;
        height: 26px;
        font-size: 26px;
        line-height: 26px;
        font-family: sans-serif;
        text-align: center;
        color: white;
        content: attr(data-percent) "%";
    }

    .circlePercent>.counter:after {
        position: absolute;
        width: 80px;
        height: 80px;
        top: 6px;
        left: 6px;
        border-radius: 50%;
        background: orange;
        content: "";
    }

    .circlePercent>.counter[data-percent="100"] {
        background: white;
    }
</style>

<?php
if (isset($_GET["run-mrp"]) && $_GET["run-mrp"] != "") {


    $productionOrdersIdArr = explode(",", base64_decode($_GET["run-mrp"]));
    $pro_id = implode("", $productionOrdersIdArr);
?>
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid" style="overflow: auto;">
                <?php
                class MrpReleaseController
                {
                    private $company_id, $branch_id, $location_id, $created_by, $updated_by, $dbObj;
                    function __construct()
                    {
                        global $company_id;
                        global $branch_id;
                        global $location_id;
                        global $created_by;
                        global $updated_by;
                        $this->company_id = $company_id;
                        $this->branch_id = $branch_id;
                        $this->location_id = $location_id;
                        $this->created_by = $created_by;
                        $this->updated_by = $updated_by;
                        $this->dbObj = new Database(true);
                    }



                    function releaseOrder($productionOrderId, $POST)
                    {
                        $productionOrderObj =  $this->dbObj->queryGet('SELECT pOrder.*,items.itemId, items.itemName, items.itemCode, items.itemDesc, items.item_sell_type FROM `' . ERP_PRODUCTION_ORDER . '` AS pOrder,`' . ERP_INVENTORY_ITEMS . '` AS items WHERE pOrder.`itemCode`=items.`itemCode` AND pOrder.`location_id`="' . $this->location_id . '" AND pOrder.so_por_id=' . $productionOrderId);

                        $prodMainItemId = $productionOrderObj["data"]["itemId"] ?? 0;
                        $prodMainItemProductionCode = $productionOrderObj["data"]["porCode"] ?? "";

                        $prodIdList = [];
                        $prodIdList[$prodMainItemId]["prodId"] = $productionOrderId;
                        $prodIdList[$prodMainItemId]["prodCode"] = $prodMainItemProductionCode;

                        // for consumeableItems
                        foreach ($POST["consumeableItems"] as $itemKey => $subItems) {
                            if ($itemKey == 0) {
                                //only subproduction order will be genereated and production order status will be changed to released
                                foreach ($subItems as $subItemKey => $subItem) {
                                    $subProdCode = $prodMainItemProductionCode . "/" . ($subItemKey + 1);
                                    $subProdSql = 'INSERT INTO `erp_production_order_sub` SET `prod_id`=' . $productionOrderId . ',`company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`subProdCode`="' . $subProdCode . '",`prodCode`="' . $prodMainItemProductionCode . '", `itemId`=' . $subItem["itemId"] . ',`itemCode`="' . $subItem["itemCode"] . '",`prodQty`=' . $subItem["prodQty"] . ',`remainQty`=' . $subItem["prodQty"] . ',`expectedDate`="' . $subItem["expected_date"] . '",`mrp_status`="Created",`wc_id`=' . $subItem["work_center"] . ',`table_id`=' . $subItem["table_map"] . ',`created_by`="' . $this->created_by . '", `updated_by`="' . $this->updated_by . '",`status`=13';
                                    $insertObj = $this->dbObj->queryInsert($subProdSql);
                                }

                                $this->dbObj->queryUpdate('UPDATE `erp_production_order` SET `mrp_status`="Created", `status`=13, `updated_by`="' . $this->updated_by . '" WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `so_por_id`=' . $productionOrderId);
                            } else {
                                //Production order and sub production both will generate.
                                $newProductionOrderCode = "PR" . time() . rand(100, 999);
                                $subItemKey = -1;
                                foreach ($subItems as $subItem) {
                                    $subItemKey += 1;
                                    if ($subItemKey == 0) {
                                        //only production order will be genereated
                                        $insertObj = $this->dbObj->queryInsert('INSERT INTO `erp_production_order` SET `company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`porCode`="' . $newProductionOrderCode . '",`itemId`=' . $subItem["itemId"] . ',`itemCode`="' . $subItem["itemCode"] . '",`refNo`="' . $prodIdList[$subItem["parentId"]]["prodCode"] . '",`qty`=' . $subItem["prodQty"] . ',`remainQty`=' . $subItem["prodQty"] . ',`expectedDate`="' . $subItem["expected_date"] . '",`description`="' . $subItem["itemName"] . ", grand child of " . $prodMainItemProductionCode . '",`mrp_status`="Created", `created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '",`status`=13');
                                        $prodIdList[$subItem["itemId"]]["prodId"] = $insertObj["insertedId"];
                                        $prodIdList[$subItem["itemId"]]["prodCode"] = $newProductionOrderCode;
                                    }
                                    // Sub production order will generate
                                    $subProdCode = $newProductionOrderCode . "/" . ($subItemKey + 1);
                                    $subProdSql = 'INSERT INTO `erp_production_order_sub` SET `prod_id`=' . $prodIdList[$subItem["parentId"]]["prodId"] . ',`company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`subProdCode`="' . $subProdCode . '",`prodCode`="' . $newProductionOrderCode . '",`itemId`=' . $subItem["itemId"] . ',`itemCode`="' . $subItem["itemCode"] . '",`prodQty`=' . $subItem["prodQty"] . ',`remainQty`=' . $subItem["prodQty"] . ',`expectedDate`="' . $subItem["expected_date"] . '",`mrp_status`="Created",`wc_id`=' . $subItem["work_center"] . ',`table_id`=' . $subItem["table_map"] . ',`created_by`="' . $this->created_by . '", `updated_by`="' . $this->updated_by . '",`status`=13';
                                    $this->dbObj->queryInsert($subProdSql);
                                }
                            }
                        }

                        // for purchasableItems
                        if (count($POST["purchasableItems"]) > 0) {
                            $last = $this->dbObj->queryGet("SELECT * FROM " . ERP_BRANCH_PURCHASE_REQUEST . " WHERE `company_id` = '$this->company_id' AND `branch_id` = '$this->branch_id' AND `location_id` = '$this->location_id' ORDER BY `purchaseRequestId` DESC LIMIT 1");
                            $lastRow = $last['data'] ?? "";
                            $lastPrId = $lastRow['prCode'] ?? "";
                            $prCode = getPRSerialNumber($lastPrId);

                            $pr_date = date('Y-m-d');
                            $expectedDate = date('Y-m-d');

                            $purchaseRequestInsertObj = $this->dbObj->queryInsert('INSERT INTO `erp_branch_purchase_request` SET `prCode`="' . $prCode . '",`company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`expectedDate`="' . $expectedDate . '", `pr_date`="' . $pr_date . '", `pr_type`="material", `refNo`="' . $prodMainItemProductionCode . '",`pr_status`=9,`description`=" RM for ' . $prodMainItemProductionCode . '",`created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '"');

                            if ($purchaseRequestInsertObj["status"] == "success") {
                                $purchaseRequestId = $purchaseRequestInsertObj["insertedId"];
                                foreach ($POST["purchasableItems"] as $itemKey => $item) {
                                    // generate purchase order and change the stock quantity
                                    $itemUOM = $item["uomId"] ?? 0;
                                    $prItemInsertObj = $this->dbObj->queryInsert('INSERT INTO `erp_branch_purchase_request_items` SET `company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`prId`=' . $purchaseRequestId . ',`itemId`=' . $item["itemId"] . ',`itemCode`="' . $item["itemCode"] . '",`itemName`="' . $item["itemName"] . '",`itemQuantity`=' . $item["requiredQty"] . ', `remainingQty`=' . $item["requiredQty"] . ', `uom`=' . $itemUOM . ', `itemNote`="' . $item["purchaseNote"] . '"');
                                }
                            }
                        }

                        $resultObj = $this->dbObj->queryFinish();
                        return $resultObj;
                    }
                }

                if (isset($_POST["productionOrderId"]) && $_POST["productionOrderId"] > 0) {
                    // console($_POST);
                    $mrpReleaseControllerObj = new MrpReleaseController();
                    $releaseOrderObj = $mrpReleaseControllerObj->releaseOrder($_POST["productionOrderId"], $_POST);
                    // console($_POST);
                    // console($releaseOrderObj);

                    swalAlert($releaseOrderObj["status"], ucfirst($releaseOrderObj["status"]), $releaseOrderObj["message"], BASE_URL . "branch/location/manage-production-order.php");
                }

                ?>
                <form action="" method="post" id="mrpPreviewForm">
                    <div class="d-flex">
                        <span class="h5 font-weight-bold">MRP Preview</span>
                        <div class="d-flex ml-auto">
                            <div class="btn-group btn-group-toggle col-2 pr-0" data-toggle="buttons">
                                <label class="btn btn-secondary active waves-effect waves-light">
                                    <input type="radio" class="expand_collapse" id="collapser" name="expand_collapse" value="collapse" autocomplete="off">Collapse
                                </label>
                                <label class="btn btn-secondary waves-effect waves-light">
                                    <input type="radio" class="expand_collapse" id="expander" name="expand_collapse" value="expand" autocomplete="off" checked="">Expand
                                </label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="productionOrderId" value="<?= $pro_id ?? 0 ?>">
                    <table id="basic" class="table">
                        <thead>
                            <tr>
                                <th>Material Details</th>
                                <th>Item Code</th>
                                <th>Consumption</th>
                                <th>Total Qty</th>
                                <th>UOM</th>
                            </tr>
                        </thead>
                        <tbody id="treeTableBody">
                            <!-- <tr data-node-id="1">
                            <td>Kuytrdfg</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                        </tr>
                        <tr data-node-id="1.1" data-node-pid="1">
                            <td><a href="#">1.1</a></td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                        </tr>
                        <tr data-node-id="1.1.1" data-node-pid="1.1">
                            <td><a href="#">1.1.1</a></td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                        </tr>
                        <tr data-node-id="1.1.2" data-node-pid="1.1">
                            <td><a href="#">1.1.2</a></td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                        </tr>
                        <tr data-node-id="1.2" data-node-pid="1">
                            <td><a href="#">1.2</a></td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                            <td>text of 1</td>
                        </tr> -->
                        </tbody>
                    </table>
                    <span class="h5 font-weight-bold">Producable Items</span>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Consumption Rate</th>
                                <th>Available Qty</th>
                                <th>Required Qty</th>
                                <th>UOM</th>
                                <th>Prod Qty</th>
                                <th>Work Center</th>
                                <th>Table</th>
                                <th>Expected Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="consumeableItems">
                        </tbody>
                    </table>
                    <span class="h5 font-weight-bold">Purchasable Items</span>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Items Code</th>
                                <th>Item Name</th>
                                <th>Available Qty</th>
                                <th>Required Qty</th>
                                <th>UOM</th>
                                <th>Purchase Note</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="nonConsumeableItems">
                        </tbody>
                    </table>
                    <button class="btn btn-primary" type="submit" id="btnReleaseOrder">Release Order</button>
                </form>
            </div>
        </section>
    </div>
<?php
} else {
    echo "<p>MRP Preview generation failed or Bom items not found!</p>";
}

require_once("../common/footer.php");

$workCenterListObj = queryGet("SELECT * FROM `erp_work_center` WHERE `company_id` = $company_id", true);

?>
<script>
    $(document).ready(function() {
        const initTreeTable = () => {
            $('#basic').simpleTreeTable({
                expander: $('#expander'),
                collapser: $('#collapser'),
                store: 'session',
                storeKey: 'simple-tree-table-basic'
            });
        }
        initTreeTable();

        // ================================[      CUSTOM JS START     ]========================================
        console.log("Welcome to MRP");
        let TreeTableJSON = null;



        $.ajax({
            url: `bom/ajax/get-mrp-preview.php?production-order-id=<?= $pro_id ?>`,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Render the tree when the data is successfully fetched
                console.log("Calling rander function!");
                console.log(data);
                //console.log(data["productionOrder"]["remainQty"]);
                let productionOrderDetails = data.productionOrder;
                let treeTableBodyHtml = renderTreeTable(data["bomDetails"], parseFloat(data["productionOrder"]["remainQty"]), "0_0");
                $("#treeTableBody").html(`
                    <tr data-node-id="0_0">
                        <td><span class="pre-normal">${productionOrderDetails.itemName.toUpperCase()}</span></td>
                        <td><span class="pre-normal">${productionOrderDetails.itemCode}</span></td>
                        <td class="text-right">-</td>
                        <td class="text-right">${productionOrderDetails.remainQty}</td>
                        <td class="text-right">${productionOrderDetails.uom}</td>
                    </tr>${treeTableBodyHtml}`);
                initTreeTable();

                $(`#consumeableItems`).html(data.produceableItems.map((item, index) => {
                    return renderProduceableItemTableRow(item, index);
                }).join(""));
                $(`#nonConsumeableItems`).html(data.notProduceableItems.map((item, index) => {
                    return renderPurchasableItemTableRow(item, index);
                }).join(""));
            },
            error: function(error) {
                console.error('Error fetching data:', error);
            }
        });

        let splitOrderCounter = 0;

        function renderProduceableItemTableRow(item, index, isSplited = false) {
            splitOrderCounter += isSplited ? 1 : 0;
            let subIndex = isSplited ? splitOrderCounter : 0;
            let itemId = item.itemId ? item.itemId : item.item_id;
            let prodQty = isSplited ? 0 : item.requiredQty;
            let cellColorClass = index % 2 == 0 ? 'bg-light' : '';
            return `
                    <tr>
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][itemId]" value="${itemId}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][requiredQty]" value="${item.requiredQty}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][consumptionRate]" value="${item.consumptionRate}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][availableQty]" value="${item.availableQty}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][itemCode]" value="${item.itemCode}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][parentId]" value="${item.parentId}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][goodsType]" value="${item.goodsType}">
                        <input type="hidden" name="consumeableItems[${index}][${subIndex}][uomId]" value="${item.uomId ? item.uomId : 0}">
                        <td class="${cellColorClass}">${item.itemCode}</td>
                        <td class="${cellColorClass}">
                            <p class="pre-normal">${item.itemName}</p>
                            
                        </td>
                        <td class="${cellColorClass} text-right">${item.consumptionRate}</td>
                        <td class="${cellColorClass} text-right">${item.availableQty}</td>
                        <td class="${cellColorClass} text-right">${item.requiredQty}</td>
                        <td class="${cellColorClass} text-right">${item.uom}</td>
                        <td class="${cellColorClass}">
                            <input type="number" name="consumeableItems[${index}][${subIndex}][prodQty]" class="form-control inputProdQty" data-item_index="${index}" data-item_id="${itemId}" data-parent_id="${item.parentId}" data-required_qty="${item.requiredQty}" value="${prodQty}" required>
                            <span class="spanProdQtyInputMsg spanProdQtyInputMsg_${index}"></span>
                        </td>
                        <td class="${cellColorClass}">
                          <select id="wcDropdown" name="consumeableItems[${index}][${subIndex}][work_center]" class="form-control selct-wc-dropdown wcDropdown wcDropdown_${index} ?>" data-wc_index="${index}" data-wc_subindex="${subIndex}" required>
                            <option value="">Select Work Center</option>
                            <?php
                            foreach ($workCenterListObj['data'] as $wc) {
                            ?>
                                <option value="<?= $wc['work_center_id'] ?>"><?= $wc['work_center_code'] . '(' . $wc['work_center_name'] . ')' ?></option>
                              <?php
                            }
                                ?>
                          </select>
                        </td>
                        <td class="${cellColorClass}">
                          <select name="consumeableItems[${index}][${subIndex}][table_map]" class="form-control selct-Table-dropdown TableDropdown" id="tableDropdown_${index}_${subIndex}" required>
                            <option value="">Select Table</option>
                          </select>
                        </td>
                        <td class="${cellColorClass}">
                            <input type="date" name="consumeableItems[${index}][${subIndex}][expected_date]" class="form-control" required>
                        </td>
                        <td class="${cellColorClass}">
                            ${ isSplited ? (
                                `<span class="btn btn-sm btn-warning removeSplitOrderBtn" data-item_id="${itemId}" data-parent_id="${item.parentId}" id="removeSplitOrderBtn_${itemId}"> - </span>`
                            ):(
                                `<span class="btn btn-sm btn-success splitOrderBtn" data-item_id="${itemId}" data-parent_id="${item.parentId}" data-item_row_index="${index}" data-item_json_data="${btoa(JSON.stringify(item))}" id="splitOrderBtn_${itemId}"> + </span>`
                            )}
                        </td>
                    </tr>`;
        }

        function renderPurchasableItemTableRow(item, index) {
            return `<tr>
                        <input type="hidden" name="purchasableItems[${index}][itemId]" value="${item.item_id}">
                        <input type="hidden" name="purchasableItems[${index}][requiredQty]" value="${item.requiredQty}">
                        <input type="hidden" name="purchasableItems[${index}][consumptionRate]" value="${item.consumptionRate}">
                        <input type="hidden" name="purchasableItems[${index}][availableQty]" value="${item.availableQty}">
                        <input type="hidden" name="purchasableItems[${index}][itemCode]" value="${item.itemCode}">
                        <input type="hidden" name="purchasableItems[${index}][itemName]" value="${item.itemName}">
                        <input type="hidden" name="purchasableItems[${index}][parentId]" value="${item.parentId}">
                        <input type="hidden" name="purchasableItems[${index}][goodsType]" value="${item.goodsType}">
                        <input type="hidden" name="purchasableItems[${index}][uomId]" value="${item.uomId}">
                        <td>${item.itemCode}</td>
                        <td><p class="pre-normal">${item.itemName}</p></td>
                        <td class="text-right">${item.availableQty}</td>
                        <td class="text-right">${item.requiredQty}</td>
                        <td>${item.uom}</td>
                        <td><input type="text" name="purchasableItems[${index}][purchaseNote]" class="form-control" placeholder="Note..."></td>

                        <td class="action-flex-btn"> 
                         <button type="button" class="btn-view btn btn-primary" data-toggle="modal" data-target="#deliveryScheduleModal_${index}">
                            <i class="fa fa-cog statusItemBtn" id="statusItemBtn_[${index}][itemId] ?>"></i>
                         </button>


                         <div class="modal modal-left left-item-modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" id="deliveryScheduleModal_${index}" tabindex="-1" role="dialog" aria-labelledby="left_modal" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-white">Delivery Shedule</h5>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12  modal-add-row modal-add-row-delivery_${index}">
                                                    <div class="row">
                                                        <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
                                                            <div class="form-input">
                                                                <label>Delivery date</label>
                                                                <input type="date"
                                                                    name="listItem[${index}][deliverySchedule][${index}][multiDeliveryDate]"
                                                                    class="form-control delDate delDate_${index} ?>" data-attr="${index}"
                                                                    id="delivery-date" placeholder="delivery date" value="">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                                            <div class="form-input">
                                                                <label>Quantity</label>
                                                                <input type="text" name="listItem[${index}][deliverySchedule][${index}][quantity]"
                                                                    class="form-control multiQuantity multiQty_${index}" data-attr="${index}"
                                                                    id="multiQuantity_${index}" placeholder="quantity" value="1">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                                            <div class="add-btn-plus">
                                                                <a style="cursor: pointer" class="btn btn-primary waves-effect waves-light"
                                                                    onclick='addDeliveryQty(${index})'>
                                                                    <i class="fa fa-plus"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer modal-footer-fixed">
                                            <button type="submit" id="finalBtn_${index}"
                                                class="btn btn-primary save-close-btn btn-xs float-right waves-effect waves-light"
                                                data-dismiss="modal" aria-label="Close">Save & Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                       </td>
                    </tr>`;
        }



        function renderTreeTable(nodes, parentQty = 1, parentNodeId = null) {
            let html = '';
            nodes.forEach(function(node, index) {
                let nodeId = `${node.bom_id}_${node.item_id}`;
                html += `
                    <tr data-node-id="${nodeId}" ${ parentNodeId ? `data-node-pid="${parentNodeId}"` : ""}>
                        <td><span class="pre-normal">${node.itemName.toUpperCase()}</span></td>
                        <td><span class="pre-normal">${node.itemCode}</span></td>
                        <td class="text-right">${node.consumptionRate}</td>
                        <td class="text-right">${node.consumptionRate*parentQty}</td>
                        <td class="text-right">${node.uom}</td>
                    </tr>`;
                if (node.childrens) {
                    html += renderTreeTable(node.childrens, node.consumptionRate * parentQty, nodeId);
                }
            });
            return html;
        }

        $(document).on("click", ".splitOrderBtn", function() {
            let itemId = $(this).data("item_id");
            let parentId = $(this).data("parent_id");
            let itemIndex = $(this).data("item_row_index");
            let targetRow = $(this).closest("tr");
            let itemRawData = $(this).data("item_json_data");
            let itemData = JSON.parse(atob(itemRawData));
            targetRow.after(renderProduceableItemTableRow(itemData, itemIndex, true));
        });

        $(document).on("click", ".removeSplitOrderBtn", function() {
            let itemId = $(this).data("item_id");
            let parentId = $(this).data("parent_id");
            let targetRow = $(this).closest("tr");
            targetRow.remove();
        });

        $(document).on("change", ".wcDropdown", function() {
            let wc_index = $(this).data('wc_index');
            let wc_subindex = $(this).data('wc_subindex');
            let attr = $(this).data('attr');
            let wc_id = $(this).val();
            $.ajax({
                type: "GET",
                url: `bom/ajax/ajax-table.php`,
                data: {
                    wc_id
                },
                beforeSend: function() {
                    $(`#tableDropdown_${wc_index}_${wc_subindex}`).html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    $(`#tableDropdown_${wc_index}_${wc_subindex}`).html(response);
                }
            });
        });

        $(document).on('keyup', '.inputProdQty', function() {
            let item_id = $(this).data('item_id');
            let parent_id = $(this).data('parent_id');
            let item_index = $(this).data('item_index');
            let sum = 0;
            let required_qty = parseFloat($(this).data("required_qty"));
            $(`.spanProdQtyInputMsg_${item_index}`).html(``);
            $('.inputProdQty').each(function() {
                if ($(this).data('item_id') == item_id && $(this).data('parent_id') == parent_id) {
                    sum += parseFloat($(this).val()) || 0;
                }
            });

            $('.inputProdQty').each(function() {
                if ($(this).data('item_id') == item_id && $(this).data('parent_id') == parent_id) {
                    if (sum != required_qty) {
                        let rem = required_qty - sum;
                        $(`.spanProdQtyInputMsg_${item_index}`).html(`<span class="text-danger">Combined quantity is ${rem>0 ? 'lesser': 'greater'} than required quantity by ${Math.abs(rem)}</span>`);
                    }
                }
            });
            checkFullFormDataIsValid();
        });

        function checkFullFormDataIsValid() {
            let isValid = true;
            let prodQtyList = {};
            let requiredQtyList = {};
            $(".inputProdQty").each(function() {
                let item_id = $(this).data("item_id");
                let parent_id = $(this).data("parent_id");
                let required_qty = parseFloat($(this).data("required_qty"));
                let inputProdQty = parseFloat($(this).val());
                let uniqueItemId = `${item_id}_${parent_id}`;

                requiredQtyList[uniqueItemId] = required_qty;
                let newProdQty = prodQtyList[uniqueItemId] > 0 ? prodQtyList[uniqueItemId] + inputProdQty : inputProdQty;
                prodQtyList[uniqueItemId] = newProdQty;
                if (newProdQty == 0) {
                    isValid = false;
                }
            });

            Object.keys(requiredQtyList).forEach(function(key, index) {
                if (requiredQtyList[key] != prodQtyList[key]) {
                    isValid = false;
                }
            });

            if (!isValid) {
                $(`#btnReleaseOrder`).attr('disabled', true);
            } else {
                $(`#btnReleaseOrder`).attr('disabled', false);
            }
            return isValid;
        }


        $(document).on("submit", "#mrpPreviewForm", function(e) {
            if (!checkFullFormDataIsValid()) {
                e.preventDefault();
                $(`#btnReleaseOrder`).attr('disabled', true);
                alert("Invalid Produceable Quantity provided!");
            } else {
                $(`#btnReleaseOrder`).attr('disabled', true);
            }
        });

    });
</script>