<?php
require_once("bom.controller.php");

require_once (BASE_DIR."app/v1/functions/branch/func-branch-failed-accounting-controller.php");
class ConsumptionController extends Accounting
{
    private $company_id, $branch_id, $location_id, $created_by, $updated_by;
    private $mrpPreviewDataPool = [];
    private $mrpPreviewStoragePool = [];
    private $failedAccController;
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
        $this->failedAccController = new FailedAccController();
    }

    private function validateConsumption($itemId, $itemQty, $declearDate)
    {
        $bomControllerObj = new BomController();
        // $bomDetailObj = $bomControllerObj->getBomDetails($itemId);
        $bomDetailObj = $bomControllerObj->getBomDetailsByItemId($itemId);
        $isStockAvailable = true;
        foreach ($bomDetailObj["data"]["bom_material_data"] as $bomOneItem) {
            $totalRequiredConsumption = $bomOneItem["totalConsumption"] * $itemQty;
            $stockLogObj = itemQtyStockCheckWithAcc($bomOneItem["item_id"], "'rmProdOpen'", ($bomOneItem["item_sell_type"] == "FIFO" ? "ASC" : "DESC"), null, $declearDate);
            $itemAvailableStocks = $stockLogObj['sumOfBatches'];
            if ($itemAvailableStocks < $totalRequiredConsumption) {
                $isStockAvailable = false;
            }
        }
        return $isStockAvailable;
    }
    private function validateItemAccounting($itemId)
    {
        $bomControllerObj = new BomController();
        $bomDetailObj = $bomControllerObj->getBomDetailsByItemId($itemId);

        $isAccOk = true;
        $resArray = [];

        foreach ($bomDetailObj["data"]["bom_material_data"] as $bomOneItem) {
            $check = checkItemImpactById($bomOneItem['item_id']);
            if ($check['status'] != "success") {
                $resArray[] = [
                    'itemCode' => $bomOneItem['itemCode'],
                    'message' => $check["message"]
                ];
                $isAccOk = false;
            }
        }

        return [
            'status' => $isAccOk ? 'success' : 'failed',
            'issues' => $resArray
        ];
    }


    public function previewConsumption($POST)
    {
        $dbObj = new Database();
        $declearItemId = $POST["itemId"] ?? 0;
        $declearItemCode = $POST["itemCode"] ?? "";
        $declearItemQty = $POST["productionQuantity"] ?? 0;
        $declearDate = $POST["productionDeclareDate"];
        $isStockAvailable = $this->validateConsumption($declearItemId, $declearItemQty, $declearDate);
        if (!$isStockAvailable) {
            return [
                "status" => "warning",
                "message" => "Stock is not enough to consume",
                "data" => []
            ];
        } else {
            $isItemAccountCheck = $this->validateItemAccounting($declearItemId);

            // if ($isItemAccountCheck['status']=="success") {
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
            // }
            // else{
            //     $issues = $isItemAccountCheck['issues']; // assuming this is an array of itemCode/message

            //     $formattedMessages = array_map(function ($item) {
            //         return "Item Code: {$item['itemCode']}\nMessage: {$item['message']}";
            //     }, $issues);

            //     $finalMessage = implode("\n\n", $formattedMessages);

            //     return [
            //         "status" => "warning",
            //         "message" => "<pre>{$finalMessage}</pre>",
            //     ];
            // }
        }
    }

    public function confirmConsumption($POST)
    {
        $bomControllerObj = new BomController();

        $soProdId = $POST["soProdId"] ?? 0;
        $soProdCode = $POST["soProdCode"] ?? "";
        $soSubProdId = $POST["soSubProdId"] ?? 0;
        $soSubProdCode = $POST["soSubProdCode"] ?? "";
        $declearItemId = $POST["itemId"] ?? 0;
        $itemsQtyUpdate1 = [];
        $itemsQtyUpdate2 = [];

        // item impact checking at falied accounting
        $resItemImpact=checkItemImpactById($declearItemId);
        if($resItemImpact["status"]!="success"){
            return $resItemImpact;
        }
        
        $declearItemCode = $POST["itemCode"] ?? "";
        $declearItemQty = $POST["productionQuantity"] ?? 0;
        $declearItemUom = $POST["itemUom"] ?? 0;
        $declearItemPrice = $POST["itemRate"] ?? 0;
        $productionDeclareDate = $POST["productionDeclareDate"] ?? date("Y-m-d");
        $declearItemMfgDate = $productionDeclareDate;
        $remaingQty=$POST["remainQty"];

        $bomDetailObj = $bomControllerObj->getBomDetailsByItemId($declearItemId)['data'];
        $bom_material_data=$bomDetailObj['bom_material_data'];
       



        $productionDeclareLocation = $POST["productionDeclareLocation"] ?? "auto";
        $productionDeclareBatch = $POST['productionDeclareBatch'];

        $PROD_DECLARE_CODE = ($productionDeclareBatch == "PRODXXXXXXXXX") ? "PROD" . time() : $POST['productionDeclareBatch'];
        $PROD_DECLARE_REF = $PROD_DECLARE_CODE;

        $previewDataObj = $this->previewConsumption($POST);

        // return $previewDataObj;

        $bomControllerObj = new BomController();
        // $bomDetailObj = $bomControllerObj->getBomDetails($declearItemId);
        $bomDetailObj = $bomControllerObj->getBomDetailsByItemId($declearItemId);

        if (!empty($bomDetailObj["data"]["bom_data"])) {
            if ($previewDataObj["status"] == "success") {
                $dbObj = new Database(true);
                $dbObj->setSuccessMsg("Production declaration successfully processed");
                $dbObj->setErrorMsg("Production declaration processing failed");


                $productionDeclarationObj = $dbObj->queryInsert("INSERT INTO `erp_production_declarations` SET `company_id`='$this->company_id',`branch_id`='$this->branch_id',`location_id`='$this->location_id', `code`='$PROD_DECLARE_CODE',`prod_id`='$soProdId',`prod_code`='$soProdCode',`sub_prod_id`='$soSubProdId',`sub_prod_code`='$soSubProdCode',`quantity`='$declearItemQty', `created_by`='$this->created_by',`updated_by`='$this->updated_by'");
                $productionDeclarationId = $productionDeclarationObj["insertedId"];
                
                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrailprod = array();
                $auditTrailprod['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrailprod['basicDetail']['table_name'] = 'erp_production_declarations';
                $auditTrailprod['basicDetail']['column_name'] = 'id'; // Primary key column
                $auditTrailprod['basicDetail']['document_id'] = $productionDeclarationId;  // primary key
                $auditTrailprod['basicDetail']['document_number'] = $soSubProdCode;
                $auditTrailprod['basicDetail']['party_id'] = 0;
                $auditTrailprod['basicDetail']['action_code'] = $action_code;
                $auditTrailprod['basicDetail']['action_referance'] = '';
                $auditTrailprod['basicDetail']['action_title'] = 'Production Declaration';  //Action comment
                $auditTrailprod['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrailprod['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrailprod['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrailprod['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrailprod['basicDetail']['action_sqlQuery'] = base64_encode("INSERT INTO `erp_production_declarations` SET `company_id`='$this->company_id',`branch_id`='$this->branch_id',`location_id`='$this->location_id', `code`='$PROD_DECLARE_CODE',`prod_id`='$soProdId',`prod_code`='$soProdCode',`sub_prod_id`='$soSubProdId',`sub_prod_code`='$soSubProdCode',`quantity`='$declearItemQty', `created_by`='$this->created_by',`updated_by`='$this->updated_by'");
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';

                
                $auditTrailprod['action_data']['Production Declaration Details']['Declaration Date'] = formatDateWeb($productionDeclareDate);
                $auditTrailprod['action_data']['Production Declaration Details']['itemCode'] = $declearItemCode;
                $auditTrailprod['action_data']['Production Declaration Details']['Remaining Quantity'] = decimalQuantityPreview($remaingQty);
                $auditTrailprod['action_data']['Production Declaration Details']['production Quantity'] = decimalQuantityPreview($declearItemQty);
                $auditTrailprod['action_data']['Production Declaration Details']['created_by'] = getCreatedByUser($this->created_by);
                $auditTrailprod['action_data']['Production Declaration Details']['updated_by'] = getCreatedByUser($this->created_by);

                foreach($bom_material_data as $bomItems){
                    $stockLogObj = itemQtyStockChecking($bomItems["item_id"], "'rmProdOpen'", ($bomItems["item_sell_type"] == "FIFO" ? "ASC" : "DESC"),null, $productionDeclareDate);
                    $itemAvailableStocks = $stockLogObj['sumOfBatches'];
                    $auditTrailprod['action_data']['Production Bom Details'][$bomItems['itemCode']]['Item Name'] = $bomItems['itemName'];
                    $auditTrailprod['action_data']['Production Bom Details'][$bomItems['itemCode']]['Consumption/Unit'] = decimalQuantityPreview($bomItems["totalConsumption"])."(". decimalQuantityPreview($bomItems["consumption"]) . " + " . decimalQuantityPreview($bomItems["extra"]) ."%)";
                    $auditTrailprod['action_data']['Production Bom Details'][$bomItems['itemCode']]['Total Consumption'] = decimalQuantityPreview($bomItems['consumption']*$declearItemQty);
                    $auditTrailprod['action_data']['Production Bom Details'][$bomItems['itemCode']]['Item Available Stocks'] =decimalQuantityPreview( $itemAvailableStocks);
                    $auditTrailprod['action_data']['Production Bom Details'][$bomItems['itemCode']]['Storage Location Name'] = $stockLogObj['data'][0]['storage_location_name'];
                    $auditTrailprod['action_data']['Production Bom Details'][$bomItems['itemCode']]['UOM'] = $bomItems["uom"];
                    $auditTrailprod['action_data']['Production Bom Details'][$bomItems['itemCode']]['Method'] = $bomItems["item_sell_type"];

                }


                $finalProductDetails = [];
                $finalProductDetails['parentGlId'] = $bomDetailObj["data"]["bom_data"]['parentGlId'];
                $finalProductDetails['itemCode'] = $bomDetailObj["data"]["bom_data"]['itemCode'];
                $finalProductDetails['itemName'] = $bomDetailObj["data"]["bom_data"]['itemName'];
                $finalProductDetails['cogm_m'] = $bomDetailObj["data"]["bom_data"]['cogm_m'] * $declearItemQty;
                $finalProductDetails['cogm_a'] = $bomDetailObj["data"]["bom_data"]['cogm_a'] * $declearItemQty;

                // $declearItemPrice = $declearItemPrice <= 0 ? $finalProductDetails['cogm_m'] + $finalProductDetails['cogm_a'] : 0;
                $declearItemPrice = $declearItemPrice <= 0 ? $bomDetailObj["data"]["bom_data"]['cogm_m'] + $bomDetailObj["data"]["bom_data"]['cogm_a'] : 0;

                $consumpProductData = [];
                // Consumable items
                foreach ($bomDetailObj["data"]["bom_material_data"] as $keyss => $bomOneItem) {

                    //console($bomDetailObj["data"]["bom_material_data"] );



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
                            $consumpProductData[$keyss]["stockLogTransferQty"] =decimalQuantityPreview($stockLogTransferQty);
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
                            parentId='".$productionDeclarationId."',
                            storageLocationId = '" . $stockLogRow['storage_location_id'] . "',
                            storageType ='" . $stockLogRow['storageLocationTypeSlug'] . "',
                            itemId = '" . $bomOneItem["item_id"] . "',
                            itemQty = '" . $usedQuantity * -1 . "',
                            itemUom = '" . $bomOneItem['baseUnitMeasure'] . "',
                            itemPrice = '" . $bomOneItem['movingWeightedPrice'] . "',
                            refActivityName='PROD-OUT',
                            logRef = '" . $stockLogRow['logRef'] . "',
                            refNumber='" . $PROD_DECLARE_REF . "',
                            bornDate='" . $stockLogRow['bornDate'] . "',
                            postingDate ='" . $productionDeclareDate . "',
                            createdBy = '" . $this->created_by . "',
                            updatedBy = '" . $this->updated_by . "'";
                        $dbObj->queryInsert($minusStockSql);
                        $itemsQtyUpdate1[] = [
                            'itemId' => $bomOneItem["item_id"],
                            'qty' => $usedQuantity,
                            'type' => "prodin",
                            "id" => $PROD_DECLARE_REF,
                        ];
                    }
                }


                $storageLocationTypeSlug = $productionDeclareLocation == "auto" ? "fgWhOpen" : $productionDeclareLocation;

                $reserveStorageObj = $dbObj->queryGet("SELECT `storage_location_id`, `warehouse_id`, `storageLocationTypeSlug` FROM `erp_storage_location` WHERE `company_id`=$this->company_id AND `branch_id`=$this->branch_id AND `location_id`=$this->location_id AND `storageLocationTypeSlug`='$storageLocationTypeSlug' AND `status`='active'")['data'];
                // console($reserveStorageObj);
                $plusStockSql = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                        SET
                            companyId = '" . $this->company_id . "',
                            branchId = '" . $this->branch_id . "',
                            locationId = '" . $this->location_id . "',
                            parentId='".$productionDeclarationId."',
                            storageLocationId = '" . $reserveStorageObj['storage_location_id'] . "',
                            refActivityName='PROD-IN',
                            logRef = '" . $PROD_DECLARE_CODE . "',
                            refNumber='" . $PROD_DECLARE_REF . "',
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

                $itemsQtyUpdate2[] = [
                    'itemId' => $declearItemId,
                    'qty' => $declearItemQty,
                    'type' => "prodin",
                    "id" => $PROD_DECLARE_REF,
                ];
                // Barcode items
                foreach ($previewDataObj["data"] as $oneBarcode) {
                    $barCodeSql = 'INSERT INTO `erp_inventory_stocks_fg_barcodes` SET `company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`itemId`=' . $declearItemId . ',`itemCode`="' . $declearItemCode . '",`itemLotNumber`="' . $oneBarcode["lotNumber"] . '",`itemSlno`=' . $oneBarcode["declearQtySl"] . ',`itemBarcode`="' . $oneBarcode["barcode"] . '",`prod_declare_id` = "' . $productionDeclarationId . '",`mfgDate`="' . $declearItemMfgDate . '",`qcDate`="' . date("Y-m-d") . '"';
                    $barCodeInsObj =  $dbObj->queryInsert($barCodeSql);
                }

                $dbObj->queryUpdate("UPDATE `erp_production_order_sub`
            SET
              `remainQty`=`remainQty`-$declearItemQty,
              `updated_by`='$this->updated_by',
              `status` = CASE WHEN `remainQty`=0 THEN 10 ELSE `status` END
            WHERE 1
                AND `sub_prod_id`=$soSubProdId
                AND `company_id`=$this->company_id
                AND `branch_id`=$this->branch_id
                AND `location_id`=$this->location_id
            ");

                $dbObj->queryUpdate("UPDATE `erp_production_order`
                SET
                `remainQty`=`remainQty`-$declearItemQty,
                `updated_by`='$this->updated_by',
                `status` = CASE WHEN `remainQty`=0 THEN 10 ELSE `status` END
                WHERE 1
                    AND `so_por_id`=$soProdId
                    AND `company_id`=$this->company_id
                    AND `branch_id`=$this->branch_id
                    AND `location_id`=$this->location_id
            ");

                $getproductiondate = queryGet("SELECT DATE(`created_at`) as production_order_date from `erp_production_order` WHERE `so_por_id`=$soProdId
                    AND `company_id`=$this->company_id
                    AND `branch_id`=$this->branch_id
                    AND `location_id`=$this->location_id")['data'];




                // UPDATE `erp_production_order_sub` SET `sub_prod_id`='[value-1]',`prod_id`='[value-2]',`company_id`='[value-3]',`branch_id`='[value-4]',`location_id`='[value-5]',`subProdCode`='[value-6]',`prodCode`='[value-7]',`itemId`='[value-8]',`itemCode`='[value-9]',`prodQty`='[value-10]',`remainQty`='[value-11]',`expectedDate`='[value-12]',`mrp_status`='[value-13]',`wc_id`='[value-14]',`table_id`='[value-15]',`created_at`='[value-16]',`created_by`='[value-17]',`updated_at`='[value-18]',`updated_by`='[value-19]',`status`='[value-20]' WHERE 1

                $mainobj = $dbObj->queryFinish();
                // console($mainobj);

                if ($mainobj['status'] != 'success') {
                    return $mainobj;
                } else {
                    //********************************ACC Start********************************/           

                    //Accounting Information

                    // $consumptionInputData = [
                    //     "BasicDetails" => [
                    //         "documentNo" => $PROD_DECLARE_CODE,
                    //         "documentDate" => $productionDeclareDate,
                    //         "postingDate" =>  date("Y-m-d"),
                    //         "reference" => '',
                    //         "remarks" => "Production declaration for - " . $declearItemCode,
                    //         "journalEntryReference" => "Production"
                    //     ],
                    //     "finalProductData" => $finalProductDetails,
                    //     "consumpProductData" => $consumpProductData
                    // ];
                    $consumptionInputData = [
                        "BasicDetails" => [
                            "documentNo" => $PROD_DECLARE_CODE,
                            "documentDate" => $getproductiondate['production_order_date'],
                            "postingDate" => $productionDeclareDate,
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
                    $prodJournalId = $respproductionDeclaration["journalId"];
                    // console($respproductionDeclaration);

                    //**************************Production Declaration Accounting End****************************** */


                    //**************************FG/SFG Declaration Accounting Start****************************** */
                    $respfgsfgDeclaration = $this->FGSFGDeclarationAccountingPosting($consumptionInputData, 'FGSFGDeclaration', 0);
                    $fgsfgJournalId = $respfgsfgDeclaration["journalId"];
                    // console($respfgsfgDeclaration);

                    //**************************FG/SFG Declaration Accounting End****************************** */

                    $updateObj = $dbObj->queryUpdate("UPDATE `erp_production_declarations` SET `prod_declaration_journal_id`='$prodJournalId',`fgsfg_declaration_journal_id`='$fgsfgJournalId' WHERE `id`='$productionDeclarationId'");

                    //********************************ACC End******************************************/
                    if ($respproductionDeclaration['status'] == 'success' && $respfgsfgDeclaration['status'] == 'success') {
                        stockQtyImpact($itemsQtyUpdate1);
                        stockQtyImpact($itemsQtyUpdate2);
                        $auditTrailprod['action_data']['Production Declaration Details']['Accounting'] = "Success";
                        $auditTrailreturn = generateAuditTrail($auditTrailprod);
                        
                        return $auditTrailreturn;
                    } else {
                        $logAccFailedResponce = $this->failedAccController->logAccountingFailure($productionDeclarationId, "production");
                        stockQtyImpact($itemsQtyUpdate1, "failed");
                        stockQtyImpact($itemsQtyUpdate2, "failed");
                        $auditTrailprod['action_data']['Production Declaration Details']['Accounting'] = "Failed";
                        $auditTrailreturn = generateAuditTrail($auditTrailprod);
                        $returnData['status'] = "success";
                        $returnData['message'] = "Accounting entry failed!";
                        $returnData["POST"] = $POST;
                        $returnData["bomDetailObj"] = $bomDetailObj;
                        $returnData["consumptionInputData"] = $consumptionInputData;
                        $returnData["respproductionDeclaration"] = $respproductionDeclaration;
                        $returnData["respfgsfgDeclaration"] = $respfgsfgDeclaration;
                        return $returnData;
                    }
                }
            } else {
                return $previewDataObj;
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "There is no BOM found, Please check your BOM settings!";
            return $returnData;
        }
    }
}
