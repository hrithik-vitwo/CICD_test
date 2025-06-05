<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div class="p-0 pt-1 my-2">
                        <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                            <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                <h3 class="card-title">Manage Production Order</h3>
                                <span>
                                    <span id="multipleMrpRunSpan"></span>
                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>" class="btn btn-sm btn-primary"><i class="fa fa-plus" style="margin-right: 0;"></i></a>
                                </span>
                            </li>
                        </ul>
                    </div>
                    <?php
                    if (isset($_POST["consumptionPosting"])) {
                    ?>
                        <div class="card" style="border-radius: 20px;">
                            <div class="p-0 m-0">
                                <?php
                                console($_POST);
                                $declearItemQty = $_POST["productionQuantity"] ?? 0;
                                $declearItemCode = $_POST["itemCode"] ?? "";
                                $declearItemId = $_POST["itemId"] ?? 0;

                                $consumptionLocation = $_POST["consumptionLocation"];
                                $availableQuantityNotEnough = false;
                                foreach ($consumptionLocation as $oneItem) {
                                    if ($oneItem["availableQuantity"] < $oneItem["requiredQuantity"]) {
                                        $availableQuantityNotEnough = true;
                                    }
                                }

                                $availableQuantityNotEnough = false; //for barcode design purposes
                                if ($availableQuantityNotEnough) {
                                    swalToast("warning", "Available Quantity not enough to consume!");
                                } else {
                                    //consumtion posting ***
                                    ?>
                                    <form action="" method="post">
                                        <input type="hidden" name="proDeclareData" value="<?= base64_encode(json_encode($_POST, true)) ?>">
                                        <?php

                                        $getLastProdDeclareSlNoObj = queryGet('SELECT `itemSlno` FROM `erp_inventory_stocks_fg_barcodes` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $declearItemId . ' ORDER BY `itemSlno` DESC LIMIT 1');
                                        if ($getLastProdDeclareSlNoObj["status"] == "success" && $getLastProdDeclareSlNoObj["data"]["itemSlno"] > 0) {
                                            $lastDeclearQtySl = ($getLastProdDeclareSlNoObj["data"]["itemSlno"] ?? 0) + 1;
                                        } else {
                                            $lastDeclearQtySl = 1;
                                        }

                                        for ($declearQtySl = $lastDeclearQtySl; $declearQtySl < $lastDeclearQtySl + $declearItemQty; $declearQtySl++) {
                                            $lotNumber = date("YmdHms");
                                            $uniqueBarCode = $declearItemCode . "/" . $lotNumber . "/" . $declearQtySl;
                                            ?>
                                            <input type="hidden" name="proDeclare[<?= $declearQtySl ?>][itemId]" value="<?= $declearItemId ?>">
                                            <input type="hidden" name="proDeclare[<?= $declearQtySl ?>][itemCode]" value="<?= $declearItemCode ?>">
                                            <input type="hidden" name="proDeclare[<?= $declearQtySl ?>][mfgDate]" value="<?= date("Y-m-d") ?>">
                                            <input type="hidden" name="proDeclare[<?= $declearQtySl ?>][itemSlno]" value="<?= $declearQtySl ?>">
                                            <input type="hidden" name="proDeclare[<?= $declearQtySl ?>][itemLotNumber]" value="<?= $lotNumber ?>">
                                            <input type="hidden" name="proDeclare[<?= $declearQtySl ?>][itemBarcode]" value="<?= $uniqueBarCode ?>">

                                            <div class="p-2 text-center" style="background-color: while; max-width:350px;">
                                                <svg id="barcode<?= $declearQtySl ?>" style="max-width:350px; min-width:350px;"></svg>
                                                <div class="d-flex justify-content-between px-2">
                                                    <small>Mfg</small>
                                                    <small><?= date("d-m-Y") ?></small>
                                                </div>
                                            </div>
                                            <script>
                                                $(document).ready(function() {
                                                    JsBarcode("#barcode<?= $declearQtySl ?>", "<?= $uniqueBarCode ?>", {
                                                        fontSize: 14,
                                                        fontOptions: "bold",
                                                        margin: 5,
                                                        height: 75,
                                                        width: 1
                                                    });
                                                });
                                            </script>
                                        <?php
                                        } ?>
                                        <div class="row p-0 m-0">
                                            <input type="submit" name="printAndConfirmDeclareProd" value="Print & Confirm Declare" class="btn btn-primary form-control"></input>
                                            <input type="submit" name="confirmDeclareProd" value="Print & Confirm Declare" class="btn btn-success form-control"></input>
                                        </div>
                                    </form>

                                <?php
                                }

                                //consumtion posting ***
                                ?>
                            </div>
                        </div>
                    <?php
                    } else {
                        if (isset($_POST["printAndConfirmDeclareProd"]) || isset($_POST["confirmDeclareProd"])) {
                            $prodDeclareData = json_decode(base64_decode($_POST["proDeclareData"]), true);
                            $prodDeclareBarCodes = $_POST["proDeclare"];

                            $soProdId = $prodDeclareData["soProdId"];
                            $soProdCode = $prodDeclareData["soProdCode"];
                            $soProdDate = $prodDeclareData["soProdCreatedDate"];
                            $prodItemId = $prodDeclareData["itemId"];
                            $prodItemCode = $prodDeclareData["itemCode"];
                            $prodItemQty = $prodDeclareData["productionQuantity"];

                            $consumpItemList = $prodDeclareData["consumptionLocation"];

                            $InsertedQueryDetails = [];


                            $barCodeInsErr = 0;
                            $updateFgStockErr = 0;
                            $updateRmAndSfgStockErr = 0;
                            $rmSfgForLogErr = 0;


                            foreach ($prodDeclareBarCodes as $oneProdBarCode) {
                                $barCodeSql = 'INSERT INTO `erp_inventory_stocks_fg_barcodes` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`itemId`=' . $oneProdBarCode["itemId"] . ',`itemCode`=' . $oneProdBarCode["itemCode"] . ',`itemLotNumber`="' . $oneProdBarCode["itemLotNumber"] . '",`itemSlno`=' . $oneProdBarCode["itemSlno"] . ',`itemBarcode`="' . $oneProdBarCode["itemBarcode"] . '",`mfgDate`="' . $oneProdBarCode["mfgDate"] . '",`qcDate`="' . date("Y-m-d") . '"';
                                $barCodeInsObj = queryInsert($barCodeSql);

                                if ($barCodeInsObj["status"] == "success") {
                                    $InsertedQueryDetails["erp_inventory_stocks_fg_barcodes"]["keyName"] = "barcodeId";
                                    $InsertedQueryDetails["erp_inventory_stocks_fg_barcodes"]["keyValues"][] = $barCodeInsObj["insertedId"];
                                } else {
                                    $barCodeInsErr++;
                                }
                            }

                            // update fgStock summary information
                            // $updateFGstockSql = 'UPDATE `erp_inventory_stocks_summary` SET `fgWhOpen`='[value-20]',`fgWhReserve`='[value-21]',`fgMktOpen`='[value-22]',`fgMktReserve`='[value-23]', `updatedBy`="'.$updated_by.'" WHERE `company_id`='.$company_id.' AND `branch_id`='.$branch_id.' AND `location_id`='.$location_id.' AND `itemId`='.$prodItemId.';
                            $updateFgStockSumSql = 'UPDATE `erp_inventory_stocks_summary` SET `fgWhReserve`=`fgWhReserve`+' . $prodItemQty . ', `updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $prodItemId;
                            $updateFgStockObj = queryUpdate($updateFgStockSumSql);

                            if ($updateFgStockObj["status"] != "success") {
                                $updateFgStockErr++;
                            }

                            // update all rm and sfg summary information
                            foreach ($consumpItemList as $oneConsumpItem) {
                                $updateRmAndSfgStockSumSql = 'UPDATE `erp_inventory_stocks_summary` SET `' . $oneConsumpItem["storageLocationSlug"] . '`=`' . $oneConsumpItem["storageLocationSlug"] . '`-' . $oneConsumpItem["requiredQuantity"] . ', `updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $oneConsumpItem["itemId"];
                                $updateRmAndSfgStockObj = queryUpdate($updateRmAndSfgStockSumSql);
                                if ($updateRmAndSfgStockObj["status"] != "success") {
                                    $updateRmAndSfgStockErr++;
                                }

                                // log generate for all rm and sfg for consumption
                                $logRefForConsumption = "CONSUMPTION" . $prodItemId;
                                $rmSfgForLogCreateSql = 'INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`storageLocationId`=1,`storageType`="' . $oneConsumpItem["storageLocationSlug"] . '",`itemId`=' . $oneConsumpItem["itemId"] . ',`itemQty`="-' . $oneConsumpItem["requiredQuantity"] . '",`itemUom`=0,`itemPrice`=0, `logRef`="' . $logRefForConsumption . '", `createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '", `status`=0';

                                $rmSfgForLogCreateObj = queryInsert($rmSfgForLogCreateSql);
                                if ($rmSfgForLogCreateObj["status"] != "success") {
                                    $rmSfgForLogErr++;
                                }

                                //console($rmSfgForLogCreateObj);

                            }

                            // UPDATE the production declaretion remaining qty
                            $updateRemainingQtyInProdOrdrSql = 'UPDATE `erp_production_order` SET `remainQty`=`remainQty`-' . $prodItemQty . ', `updated_by`="' . $updated_by . '" WHERE `so_por_id`=' . $soProdId . ' AND `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id;

                            $updateRemainingQtyProdOrderObj = queryUpdate($updateRemainingQtyInProdOrdrSql);



                            $finalProductDetailsSql = "SELECT Items.*,bom.cogm as price,goodsType.`type` FROM erp_inventory_items as Items LEFT JOIN erp_bom AS bom ON Items.itemId=bom.itemId LEFT JOIN `erp_inventory_mstr_good_types` AS goodsType ON Items.`goodsType`=goodsType.`goodTypeId` WHERE Items.location_id=" . $location_id . " AND Items.itemId=" . $prodItemId;
                            $finalProductDetailsObj = queryGet($finalProductDetailsSql);
                            $finalProductDetails = $finalProductDetailsObj["data"];


                            $consumpProductData = [];
                            foreach ($consumpItemList as $oneConsumpItem) {
                                if ($oneConsumpItem["materialType"] == "RM") {
                                    $consumpRmProductSql = "SELECT Items.*, stockSummary.`movingWeightedPrice` as price, 'RM' as type FROM `erp_inventory_items` as Items LEFT JOIN `erp_inventory_stocks_summary` AS stockSummary ON Items.`itemId`=stockSummary.`itemId` WHERE Items.`location_id`=" . $location_id . " AND Items.`itemId`=" . $oneConsumpItem["itemId"];
                                    $consumpRmProductObj = queryGet($consumpRmProductSql);
                                    $consumpProductData[] = $consumpRmProductObj["data"];
                                } else {
                                    $consumpSfgProductSql = "SELECT Items.*,bom.`cogm` as price, 'SFG' as type FROM `erp_inventory_items` as Items LEFT JOIN `erp_bom` AS bom ON Items.itemId=bom.`itemId` WHERE Items.`location_id`=" . $location_id . " AND bom.`locationId`=" . $location_id . " AND Items.`itemId`=" . $oneConsumpItem["itemId"];
                                    $consumpSfgProductObj = queryGet($consumpSfgProductSql);
                                    $consumpProductData[] = $consumpSfgProductObj["data"];
                                }
                            }

                            $consumptionInputData = [
                                "BasicDetails" => [
                                    "documentNo" => $soProdCode,
                                    "documentDate" => $soProdDate,
                                    "postingDate" =>  date("Y-m-d"),
                                    "reference" => '',
                                    "remarks" => "Production declaration for - " . $prodItemCode,
                                    "journalEntryReference" => "Production"
                                ],
                                "finalProductData" => $finalProductDetails,
                                "consumpProductData" => $consumpProductData
                            ];

                            echo "<br>Accounting Information</br>";
                            console($consumptionInputData);
                            
                            //**************************Production Declaration Accounting Start****************************** */
                            $respproductionDeclaration= $accountingControllerObj->productionDeclarationAccountingPosting($consumptionInputData,'ProductiondeclarationInventoryissuance',0);

                            console($respproductionDeclaration);

                            //**************************Production Declaration Accounting End****************************** */

                            
                            //**************************FG/SFG Declaration Accounting Start****************************** */
                            $respfgsfgDeclaration= $accountingControllerObj->FGSFGDeclarationAccountingPosting($consumptionInputData,'FGSFGDeclaration',0);
                    
                            console($respfgsfgDeclaration);

                            //**************************FG/SFG Declaration Accounting End****************************** */

                            console(["barCodeInsErr" => $barCodeInsErr, "updateFgStockErr" => $updateFgStockErr, "updateRmAndSfgStockErr" => $updateRmAndSfgStockErr, "rmSfgForLogErr" => $rmSfgForLogErr]);



                            console($InsertedQueryDetails);


                            console($prodDeclareData);
                            console($prodDeclareBarCodes);
                        } else {
                            redirect(LOCATION_URL . "manage-production-order.php");
                        }
                    }
                    ?>
                </div>
    </section>
</div>


<script>
    $(document).ready(function() {

    });
</script>