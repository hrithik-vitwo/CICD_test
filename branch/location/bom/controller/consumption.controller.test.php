<?php
require_once("bom.controller.test.php");
class ConsumptionController extends Accounting
{
    private $company_id, $branch_id, $location_id, $created_by, $updated_by;
    private $mrpPreviewDataPool = [];
    private $mrpPreviewStoragePool = [];
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
    }

    private function validateConsumption($itemId, $itemQty)
    {
        $bomControllerObj = new BomController();
        $bomDetailObj = $bomControllerObj->getBomDetails($itemId);
        $isStockAvailable = true;
        foreach ($bomDetailObj["data"]["bom_material_data"] as $bomOneItem) {
            $totalRequiredConsumption = $bomOneItem["totalConsumption"] * $itemQty;
            $stockLogObj = itemQtyStockChecking($bomOneItem["item_id"], "'rmProdOpen'", ($bomOneItem["item_sell_type"] == "FIFO" ? "ASC" : "DESC"));
            $itemAvailableStocks = $stockLogObj['sumOfBatches'];
            if ($itemAvailableStocks < $totalRequiredConsumption) {
                $isStockAvailable = false;
            }
        }
        return $isStockAvailable;
    }

    public function previewConsumption($POST)
    {
        $dbObj = new Database();
        $declearItemId = $POST["itemId"] ?? 0;
        $declearItemCode = $POST["itemCode"] ?? "";
        $declearItemQty = $POST["productionQuantity"] ?? 0;
        $isStockAvailable = $this->validateConsumption($declearItemId, $declearItemQty);
        if (!$isStockAvailable) {
            return [
                "status" => "warning",
                "message" => "Stock is not enough to consume",
                "data" => []
            ];
        } else {
            $getLastProdDeclareSlNoObj = $dbObj->queryGet('SELECT `itemSlno` FROM `erp_inventory_stocks_fg_barcodes` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `itemId`=' . $declearItemId . ' ORDER BY `itemSlno` DESC LIMIT 1');
            if ($getLastProdDeclareSlNoObj["status"] == "success" && $getLastProdDeclareSlNoObj["data"]["itemSlno"] > 0) {
                $lastDeclearQtySl = ($getLastProdDeclareSlNoObj["data"]["itemSlno"] ?? 0) + 1;
            } else {
                $lastDeclearQtySl = 1;
            }
            $uniqueBarCodes = [];
            for ($declearQtySl = $lastDeclearQtySl; $declearQtySl < $lastDeclearQtySl + $declearItemQty; $declearQtySl++) {
                $lotNumber = date("YmdHms");
                $uniqueBarCodes[] = ["declearItemCode" => $declearItemCode, "lotNumber" => $lotNumber, "declearQtySl" => $declearQtySl, "barcode" => $declearItemCode . "/" . $lotNumber . "/" . $declearQtySl];
            }
            return [
                "status" => "success",
                "message" => "Successfully generated QR Codes",
                "data" => $uniqueBarCodes
            ];
        }
    }

    public function confirmConsumption($POST)
    {
        $soProdId = $POST["soProdId"] ?? 0;
        $declearItemId = $POST["itemId"] ?? 0;
        $declearItemCode = $POST["itemCode"] ?? "";
        $declearItemQty = $POST["productionQuantity"] ?? 0;
        $declearItemUom = $POST["itemUom"] ?? 0;
        $declearItemPrice = $POST["itemRate"] ?? 0;
        $productionDeclareDate = $POST["productionDeclareDate"] ?? date("Y-m-d");
        $declearItemMfgDate = $productionDeclareDate;

        $PROD_DECLARE_CODE = "PROD" . time();

        $previewDataObj = $this->previewConsumption($POST);
        // return $previewDataObj;
        if ($previewDataObj["status"] == "success") {
            $dbObj = new Database(true);
            $dbObj->setSuccessMsg("Production declaration successfully processed");
            $dbObj->setErrorMsg("Production declaration processing failed");
            $bomControllerObj = new BomController();

            $bomDetailObj = $bomControllerObj->getBomDetails($declearItemId);

            $finalProductDetails = [];
            $finalProductDetails['parentGlId'] = $bomDetailObj["data"]["bom_data"]['parentGlId'];
            $finalProductDetails['itemCode'] = $bomDetailObj["data"]["bom_data"]['itemCode'];
            $finalProductDetails['itemName'] = $bomDetailObj["data"]["bom_data"]['itemName'];
            $finalProductDetails['cogm_m'] = $bomDetailObj["data"]["bom_data"]['cogm_m'] * $declearItemQty;
            $finalProductDetails['cogm_a'] = $bomDetailObj["data"]["bom_data"]['cogm_a'] * $declearItemQty;

            $consumpProductData = [];
            // Consumable items
            foreach ($bomDetailObj["data"]["bom_material_data"] as $keyss => $bomOneItem) {

                $stockLogObj = itemQtyStockChecking($bomOneItem["item_id"], "'rmProdOpen'", ($bomOneItem["item_sell_type"] == "FIFO" ? "ASC" : "DESC"));
                $stockLogTransferQty = $bomOneItem["totalConsumption"] * $declearItemQty;


                if ($bomOneItem["priceType"] == "V") {
                    $consumpProductData[$keyss]["type"] = $bomOneItem['type'];
                    $consumpProductData[$keyss]["stockLogTransferQty"] = $stockLogTransferQty;
                    $consumpProductData[$keyss]["parentGlId"] = $bomOneItem['parentGlId'];
                    $consumpProductData[$keyss]["itemCode"] = $bomOneItem['itemCode'];
                    $consumpProductData[$keyss]["itemName"] = $bomOneItem['itemName'];
                    $consumpProductData[$keyss]["unitprice"] = $bomOneItem['movingWeightedPrice'];
                    $consumpProductData[$keyss]["price"] = $bomOneItem['movingWeightedPrice'] * $stockLogTransferQty;
                } else {
                    $consumpSfgProductSql = "SELECT bom.`cogm` as cogmprice FROM `erp_bom` WHERE `locationId`=" . $this->location_id . " AND bomStatus` = 'active' AND `itemId`=" . $bomOneItem["item_id"] . " ORDER BY bomId DESC";

                    $consumpSfgProductObj = $dbObj->queryGet($consumpSfgProductSql);

                    if ($consumpSfgProductObj["status"] == "success") {
                        $consumpProductData[$keyss]["type"] = $bomOneItem['type'];
                        $consumpProductData[$keyss]["stockLogTransferQty"] = $stockLogTransferQty;
                        $consumpProductData[$keyss]["parentGlId"] = $bomOneItem['parentGlId'];
                        $consumpProductData[$keyss]["itemCode"] = $bomOneItem['itemCode'];
                        $consumpProductData[$keyss]["itemName"] = $bomOneItem['itemName'];
                        $consumpProductData[$keyss]["unitprice"] = $consumpSfgProductObj['data']['cogmprice'];
                        $consumpProductData[$keyss]["price"] = $consumpSfgProductObj['data']['cogmprice'] * $stockLogTransferQty;
                    } else {
                        $consumpProductData[$keyss]["type"] = $bomOneItem['type'];
                        $consumpProductData[$keyss]["stockLogTransferQty"] = $stockLogTransferQty;
                        $consumpProductData[$keyss]["parentGlId"] = $bomOneItem['parentGlId'];
                        $consumpProductData[$keyss]["itemCode"] = $bomOneItem['itemCode'];
                        $consumpProductData[$keyss]["itemName"] = $bomOneItem['itemName'];
                        $consumpProductData[$keyss]["unitprice"] = $bomOneItem['movingWeightedPrice'];
                        $consumpProductData[$keyss]["price"] = $bomOneItem['movingWeightedPrice'] * $stockLogTransferQty;
                    }
                }


                $stockLogTransferedQty = 0;
                foreach ($stockLogObj["data"] as $stockLogKey => $stockLogRow) {
                    $resultTestObj["stockLogRow"][] = $stockLogRow;

                    if ($stockLogTransferedQty == $stockLogTransferQty) {
                        break;
                    }
                    if ($stockLogRow['itemQty'] == 0) {
                        continue;
                    }

                    $usedQuantity = min($stockLogRow['itemQty'], $stockLogTransferQty - $stockLogTransferedQty);
                    $stockLogTransferedQty += $usedQuantity;

                    $minusStockSql = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                        SET 
                            companyId = '" . $this->company_id . "',
                            branchId = '" . $this->branch_id . "',
                            locationId = '" . $this->location_id . "',
                            storageLocationId = '" . $stockLogRow['storage_location_id'] . "',
                            storageType ='" . $stockLogRow['storageLocationTypeSlug'] . "',
                            itemId = '" . $bomOneItem["item_id"] . "',
                            itemQty = '" . $usedQuantity * -1 . "',
                            itemUom = '" . $stockLogRow["itemUom"] . "',
                            itemPrice = '" . $bomOneItem["rate"] . "',
                            refActivityName='PROD',
                            logRef = '" . $stockLogRow['logRef'] . "',
                            refNumber='" . $PROD_DECLARE_CODE . "',
                            bornDate='" . $stockLogRow['bornDate'] . "',
                            postingDate ='" . $productionDeclareDate . "',
                            createdBy = '" . $this->created_by . "',
                            updatedBy = '" . $this->updated_by . "'";
                    $dbObj->queryInsert($minusStockSql);
                }
            }

            // console($bomDetailObj);
            // console($finalProductDetails);
            // return $consumpProductData;
            // exit;

            $reserveStorageSql = 'SELECT storage_location_id, warehouse_id, storageLocationTypeSlug FROM `erp_storage_location` WHERE company_id=' . $this->company_id . ' AND branch_id=' . $this->branch_id . ' AND location_id=' . $this->location_id . ' AND warehouse_id=' . $stockLogRow['warehouse_id'] . ' AND storageLocationTypeSlug="fgWhOpen"';
            $reserveStorageObj = $dbObj->queryGet($reserveStorageSql)['data'];

            $plusStockSql = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                        SET
                            companyId = '" . $this->company_id . "',
                            branchId = '" . $this->branch_id . "',
                            locationId = '" . $this->location_id . "',
                            storageLocationId = '" . $reserveStorageObj['storage_location_id'] . "',
                            refActivityName='PROD',
                            logRef = '" . $PROD_DECLARE_CODE . "',
                            refNumber='" . $PROD_DECLARE_CODE . "',
                            bornDate='" . $productionDeclareDate . "',
                            postingDate ='" . $productionDeclareDate . "',
                            storageType ='" . $reserveStorageObj['storageLocationTypeSlug'] . "',
                            itemId = '" . $declearItemId . "',
                            itemQty = '" . $declearItemQty . "',
                            itemUom = '" . $declearItemUom . "',
                            itemPrice = '" .  $declearItemPrice . "',
                            createdBy = '" . $this->created_by . "',
                            updatedBy = '" . $this->updated_by . "'";

            $dbObj->queryInsert($plusStockSql);
            
            if($_POST['confirmDeclareProd'] == 'Print & Confirm Declare'){
            // Barcode items
            foreach ($previewDataObj["data"] as $oneBarcode) {
                $barCodeSql = 'INSERT INTO `erp_inventory_stocks_fg_barcodes` SET `company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`itemId`=' . $declearItemId . ',`itemCode`="' . $declearItemCode . '",`itemLotNumber`="' . $oneBarcode["lotNumber"] . '",`itemSlno`=' . $oneBarcode["declearQtySl"] . ',`itemBarcode`="' . $oneBarcode["barcode"] . '",`mfgDate`="' . $declearItemMfgDate . '",`qcDate`="' . date("Y-m-d") . '"';
                $barCodeInsObj =  $dbObj->queryInsert($barCodeSql);
            }

        }
            $dbObj->queryUpdate('UPDATE `erp_production_order` SET `remainQty`=`remainQty`-' . $declearItemQty . ', `updated_by`="' . $this->updated_by . '" WHERE `so_por_id`=' . $soProdId . ' AND `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id);


            $mainobj = $dbObj->queryFinish();
            // console($mainobj);

            if ($mainobj['status'] != 'success') {
                return $mainobj;
            } else {
                //********************************ACC Start********************************/           

                //Accounting Information

                $consumptionInputData = [
                    "BasicDetails" => [
                        "documentNo" => $PROD_DECLARE_CODE,
                        "documentDate" => $productionDeclareDate,
                        "postingDate" =>  date("Y-m-d"),
                        "reference" => '',
                        "remarks" => "Production declaration for - " . $declearItemCode,
                        "journalEntryReference" => "Production"
                    ],
                    "finalProductData" => $finalProductDetails,
                    "consumpProductData" => $consumpProductData
                ];

                // echo "<br>Accounting Information</br>";
                // console($consumptionInputData);

                //**************************Production Declaration Accounting Start****************************** */
                $respproductionDeclaration = $this->productionDeclarationAccountingPosting($consumptionInputData, 'ProductiondeclarationInventoryissuance', 0);

                // console($respproductionDeclaration);

                //**************************Production Declaration Accounting End****************************** */


                //**************************FG/SFG Declaration Accounting Start****************************** */
                $respfgsfgDeclaration = $this->FGSFGDeclarationAccountingPosting($consumptionInputData, 'FGSFGDeclaration', 0);

                // console($respfgsfgDeclaration);

                //**************************FG/SFG Declaration Accounting End****************************** */



                //********************************ACC End******************************************/
                if($respproductionDeclaration['status'] =='success' && $respproductionDeclaration['status'] =='success'){
                    return $mainobj;
                }else{
                    $returnData['status'] = "success";
                    $returnData['message'] = "Accounting entry failed!";
                    $returnData["respproductionDeclaration"] = $respproductionDeclaration;
                    $returnData["respproductionDeclaration"] = $respproductionDeclaration;

                    return $returnData;
                }
            }
        } else {
            return $previewDataObj;
        }
    }
}
