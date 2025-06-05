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
        $this->dbObj->setSuccessMsg("Mrp successfully processed");
        $this->dbObj->setErrorMsg("Mrp processing failed");
    }

    function releaseOrder($productionOrderId, $POST)
    {

        // console($POST);
        // exit();

        $productionOrderObj =  $this->dbObj->queryGet('SELECT pOrder.*,items.itemId, items.itemName, items.itemCode, items.itemDesc, items.item_sell_type FROM `' . ERP_PRODUCTION_ORDER . '` AS pOrder,`' . ERP_INVENTORY_ITEMS . '` AS items WHERE pOrder.`itemCode`=items.`itemCode` AND pOrder.`location_id`="' . $this->location_id . '" AND pOrder.so_por_id=' . $productionOrderId);

        if ($productionOrderObj["data"]["mrp_status"] == "Created" || $productionOrderObj["data"]["status"] == 13) {
            return [
                "status" => "warning",
                "message" => "Production order already released, please try with another"
            ];
        }

        if (count($POST["consumeableItems"]) + count($POST["purchasableItems"]) == 0) {
            return [
                "status" => "warning",
                "message" => "Invalid MRP data, please try again!"
            ];
        }

        $prodMainItemId = $productionOrderObj["data"]["itemId"] ?? 0;
        $prodMainItemProductionCode = $productionOrderObj["data"]["porCode"] ?? "";
        $prodMainItemProdQty = $productionOrderObj["data"]["qty"];

        $prodIdList = [];
        $prodIdList[$prodMainItemId]["prodId"] = $productionOrderId;
        $prodIdList[$prodMainItemId]["prodCode"] = $prodMainItemProductionCode;

        $stockTransferList = [];

        $mrpCode = "MRP" . time();
        $mrp_table = queryInsert('INSERT INTO `erp_mrp` SET `company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ', `mrp_code`= "' . $mrpCode . '" , `created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '"');
        // console($mrp_table);
        // exit();

        // for consumeableItems
        foreach ($POST["consumeableItems"] as $itemKey => $subItems) {
            // echo $itemKey;
            // exit();
            if ($itemKey == 0) {
                //only subproduction order will be genereated and production order status will be changed to released
                foreach ($subItems as $subItemKey => $subItem) {
                    $subProdCode = $prodMainItemProductionCode . "/" . ($subItemKey + 1);
                    $subProdSql = 'INSERT INTO `erp_production_order_sub` SET `grand_prod_id`= ' . $productionOrderId . ', `prod_id`=' . $productionOrderId . ',`company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`subProdCode`="' . $subProdCode . '",`prodCode`="' . $prodMainItemProductionCode . '", `itemId`=' . $subItem["itemId"] . ',`itemCode`="' . $subItem["itemCode"] . '",`prodQty`=' . $subItem["prodQty"] . ',`remainQty`=' . $subItem["prodQty"] . ',`expectedDate`="' . $subItem["expected_date"] . '",`mrp_status`="Created", `mrp_code` = "'.$mrpCode.'", `wc_id`=' . $subItem["work_center"] . ',`table_id`=' . $subItem["table_map"] . ',`created_by`="' . $this->created_by . '", `updated_by`="' . $this->updated_by . '",`status`=13';
                    $insertObj = $this->dbObj->queryInsert($subProdSql);
                    // console($subProdSql);

                    $stockTransferList[] = [
                        "itemId" => $subItem["itemId"],
                        "fromStrorageLoc" => $subItem["goodsType"] == 3 ? "sfgWhOpen" : "fgWhOpen",
                        "toStrorageLoc" => "rmProdOpen",
                        "refNumber" => $prodMainItemProductionCode,
                        "quantity" => ($subItem["prodQty"] * $subItem["consumptionRate"]) - $subItem["requiredQty"]
                    ];
                    $sub_prod_id=$insertObj["insertedId"];
                    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                    $auditTrail = array();
                    $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                    $auditTrail['basicDetail']['table_name'] = 'erp_production_order_sub';
                    $auditTrail['basicDetail']['column_name'] = 'sub_prod_id'; // Primary key column
                    $auditTrail['basicDetail']['document_id'] = $sub_prod_id;  // primary key
                    $auditTrail['basicDetail']['document_number'] = $subProdCode;
                    $auditTrail['basicDetail']['party_id'] = 0;
                    $auditTrail['basicDetail']['action_code'] = $action_code;
                    $auditTrail['basicDetail']['action_referance'] = '';
                    $auditTrail['basicDetail']['action_title'] = 'Sub Production Add';  //Action comment
                    $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                    $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                    $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                    $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                    $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($subProdSql);
                    $auditTrail['basicDetail']['others'] = '';
                    $auditTrail['basicDetail']['remark'] = '';
                    $itemCode=$subItem["itemCode"];
                    $auditTrail['action_data']['Production Order Details']['expectedDate'] = formatDateWeb($subItem["expected_date"]);
                    $auditTrail['action_data']['Production Order Details']['itemCode'] = $itemCode;
                    $auditTrail['action_data']['Production Order Details']['prod qty'] = decimalQuantityPreview($subItem["prodQty"]);
                    $auditTrail['action_data']['Production Order Details']['remainQty'] = decimalQuantityPreview($subItem["prodQty"]);
                    $auditTrail['action_data']['Production Order Details']['created_by'] = getCreatedByUser($this->created_by);
                    $auditTrail['action_data']['Production Order Details']['updated_by'] = getCreatedByUser($this->updated_by);

                    $auditTrailreturn = generateAuditTrail($auditTrail);
                }

                $this->dbObj->queryUpdate('UPDATE `erp_production_order` SET `mrp_status`="Created", `mrp_code` = "'.$mrpCode.'", `status`=13, `updated_by`="' . $this->updated_by . '" WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `so_por_id`=' . $productionOrderId);

            } else {
                //Production order and sub production both will generate.
                $newProductionOrderCode = "PR" . time() . rand(100, 999);
                $newProductionOrderId = 0;
                $subItemKey = -1;
                foreach ($subItems as $subItem) {
                    $subItemKey += 1;
                    if ($subItemKey == 0) {
                        //only production order will be genereated
                        $subProdQty = $subItem["requiredQty"] ?? 0;
                        $refProdCode = $prodIdList[$subItem["parentId"]]["prodCode"];
                        $insertObj = $this->dbObj->queryInsert('INSERT INTO `erp_production_order` SET `company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`porCode`="' . $newProductionOrderCode . '",`itemId`=' . $subItem["itemId"] . ',`itemCode`="' . $subItem["itemCode"] . '",`refNo`="' . $refProdCode . '",`qty`=' . $subProdQty . ',`remainQty`=' . $subProdQty . ',`expectedDate`="' . $subItem["expected_date"] . '",`description`="' . $subItem["itemName"] . ", grand child of " . $prodMainItemProductionCode . '",`mrp_status`="Created", `mrp_code` = "'.$mrpCode.'", `created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '",`status`=13');

                        $newProductionOrderId = $insertObj["insertedId"];

                        $prodIdList[$subItem["itemId"]]["prodId"] = $newProductionOrderId;
                        $prodIdList[$subItem["itemId"]]["prodCode"] = $newProductionOrderCode;

                        $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                        $auditTrailprod = array();
                        $auditTrailprod['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN',                'APPROVED'
                        $auditTrailprod['basicDetail']['table_name'] = ERP_PRODUCTION_ORDER;
                        $auditTrailprod['basicDetail']['column_name'] = 'so_por_id'; // Primary key column
                        $auditTrailprod['basicDetail']['document_id'] = $insertObj['insertedId'];  // primary key
                        $auditTrailprod['basicDetail']['document_number'] = $newProductionOrderCode;
                        $auditTrailprod['basicDetail']['action_code'] = $action_code;
                        $auditTrailprod['basicDetail']['action_referance'] = '';
                        $auditTrailprod['basicDetail']['party_id'] = 0;
                        $auditTrailprod['basicDetail']['action_title'] = 'Production Order Creation';  //Action comment
                        $auditTrailprod['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                        $auditTrailprod['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                        $auditTrailprod['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                        $auditTrailprod['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                        $auditTrailprod['basicDetail']['action_sqlQuery'] = base64_encode($insertObj['query']);
                        $auditTrailprod['basicDetail']['others'] = '';
                        $auditTrailprod['basicDetail']['remark'] = '';
                        $itemCode=$subItem["itemCode"];
                        $auditTrailprod['action_data']['Production Order Details'][$itemCode]['refNo'] = $$refProdCode;
                        $auditTrailprod['action_data']['Production Order Details'][$itemCode]['expectedDate'] = $subItem["expected_date"];
                        $auditTrailprod['action_data']['Production Order Details'][$itemCode]['itemCode'] = $itemCode;
                        $auditTrailprod['action_data']['Production Order Details'][$itemCode]['qty'] = $subProdQty;
                        $auditTrailprod['action_data']['Production Order Details'][$itemCode]['remainQty'] = $subProdQty;
                        $auditTrailprod['action_data']['Production Order Details'][$itemCode]['created_by'] = getCreatedByUser($this->created_by);
                        $auditTrailprod['action_data']['Production Order Details'][$itemCode]['updated_by'] = getCreatedByUser($this->created_by);

                        $auditTrailreturn = generateAuditTrail($auditTrailprod);
                    }
                    // Sub production order will generate
                    $subProdCode = $newProductionOrderCode . "/" . ($subItemKey + 1);
                    $subProdSql = 'INSERT INTO `erp_production_order_sub` SET `grand_prod_id`= ' . $productionOrderId . ', `prod_id`=' . $newProductionOrderId . ',`company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`subProdCode`="' . $subProdCode . '",`prodCode`="' . $newProductionOrderCode . '",`itemId`=' . $subItem["itemId"] . ',`itemCode`="' . $subItem["itemCode"] . '",`prodQty`=' . $subItem["prodQty"] . ',`remainQty`=' . $subItem["prodQty"] . ',`expectedDate`="' . $subItem["expected_date"] . '",`mrp_status`="Created", `mrp_code`="' . $mrpCode . '", `wc_id`=' . $subItem["work_center"] . ',`table_id`=' . $subItem["table_map"] . ',`created_by`="' . $this->created_by . '", `updated_by`="' . $this->updated_by . '",`status`=13';
                    $this->dbObj->queryInsert($subProdSql);

                    $stockTransferList[] = [
                        "itemId" => $subItem["itemId"],
                        "fromStrorageLoc" => $subItem["goodsType"] == 3 ? "sfgWhOpen" : "fgWhOpen",
                        "toStrorageLoc" => "rmProdOpen",
                        "refNumber" => $prodMainItemProductionCode,
                        "quantity" => ($subItem["prodQty"] * $subItem["consumptionRate"]) - $subItem["requiredQty"]
                    ];
                    $sub_prod_id=$insertObj["insertedId"];
                    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                    $auditTrail = array();
                    $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                    $auditTrail['basicDetail']['table_name'] = 'erp_production_order_sub';
                    $auditTrail['basicDetail']['column_name'] = 'sub_prod_id'; // Primary key column
                    $auditTrail['basicDetail']['document_id'] = $sub_prod_id;  // primary key
                    $auditTrail['basicDetail']['document_number'] = $subProdCode;
                    $auditTrail['basicDetail']['party_id'] = 0;
                    $auditTrail['basicDetail']['action_code'] = $action_code;
                    $auditTrail['basicDetail']['action_referance'] = '';
                    $auditTrail['basicDetail']['action_title'] = 'Sub Production Add';  //Action comment
                    $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                    $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                    $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                    $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                    $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($subProdSql);
                    $auditTrail['basicDetail']['others'] = '';
                    $auditTrail['basicDetail']['remark'] = '';
                    $itemCode=$subItem["itemCode"];
                    $auditTrail['action_data']['Production Order Details']['expectedDate'] = $subItem["expected_date"];
                    $auditTrail['action_data']['Production Order Details']['itemCode'] = $itemCode;
                    $auditTrail['action_data']['Production Order Details']['prod qty'] = $subItem["prodQty"];
                    $auditTrail['action_data']['Production Order Details']['remainQty'] = $subItem["prodQty"];
                    $auditTrail['action_data']['Production Order Details']['created_by'] = getCreatedByUser($this->created_by);
                    $auditTrail['action_data']['Production Order Details']['updated_by'] = getCreatedByUser($this->updated_by);

                    $auditTrailreturn = generateAuditTrail($auditTrail);
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
            
            $isPurchaseRequestRequired = false;
            foreach ($POST["purchasableItems"] as $itemKey => $item) {
                if ($item["requiredQty"] > 0) {
                    $isPurchaseRequestRequired = true;
                }
            }
            //  $prodMainItemProductionCode
            if ($isPurchaseRequestRequired) {
                $purchaseRequestInsertObj = $this->dbObj->queryInsert('INSERT INTO `erp_branch_purchase_request` SET `prCode`="' . $prCode . '",`company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`pr_origin` = "mrp" ,`expectedDate`="' . $expectedDate . '", `pr_date`="' . $pr_date . '", `pr_type`="material", `refNo`="' . $mrpCode . '",`pr_status`=9,`description`=" RM for ' . $prodMainItemProductionCode . '",`created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '"');

                if ($purchaseRequestInsertObj["status"] == "success") {
                    $purchaseRequestId = $purchaseRequestInsertObj["insertedId"];

                    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_PURCHASE_REQUEST;
                $auditTrail['basicDetail']['column_name'] = 'purchaseRequestId'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $purchaseRequestId;  // primary key
                $auditTrail['basicDetail']['document_number'] = $prCode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['party_id'] = 0;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = 'New PR created';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($purchaseRequestInsertObj["query"]);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';




                $auditTrail['action_data']['Purchase Request Details']['expectedDate'] = formatDateORDateTime($expectedDate);
                $auditTrail['action_data']['Purchase Request Details']['pr_date'] = formatDateORDateTime($pr_date);
                $auditTrail['action_data']['Purchase Request Details']['description'] = 'RM for ' . $prodMainItemProductionCode . '';
                $auditTrail['action_data']['Purchase Request Details']['prCode'] = $prCode;
                $auditTrail['action_data']['Purchase Request Details']['created_by'] = getCreatedByUser($this->created_by);
                $auditTrail['action_data']['Purchase Request Details']['updated_by'] = getCreatedByUser($this->updated_by);
                $auditTrail['action_data']['Purchase Request Details']['pr_type'] = 'material';
                $auditTrail['action_data']['Purchase Request Details']['refNo'] = $mrpCode;
                

                


                    foreach ($POST["purchasableItems"] as $itemKey => $item) {
                        // generate purchase order and change the stock quantity
                        if ($item["requiredQty"] > 0) {
                            $itemUOM = $item["uomId"] ?? 0;
                            $prItemInsertObj = $this->dbObj->queryInsert('INSERT INTO `erp_branch_purchase_request_items` SET `company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`prId`=' . $purchaseRequestId . ',`itemId`=' . $item["itemId"] . ',`itemCode`="' . $item["itemCode"] . '",`itemName`="' . $item["itemName"] . '",`itemQuantity`=' . $item["requiredQty"] . ', `remainingQty`=' . $item["requiredQty"] . ', `uom`=' . $itemUOM . ', `itemNote`="' . $item["purchaseNote"] . '"');

                           // console($prItemInsertObj);

                            foreach ($item['deliverySchedule'] as $delItem) {
                              
                                if ($delItem['multiDeliveryDate'] == "") {
                                    $date = $expectedDate;
                                    $quantity = $item['requiredQty'];
                                } else {
                                    $date = $delItem['multiDeliveryDate'];
                                    $quantity = $delItem['quantity'];
                                }
                                $prItemId = $prItemInsertObj["insertedId"];
                                $prItemDelInsertObj = $this->dbObj->queryInsert('INSERT INTO `erp_purchase_register_item_delivery_schedule`   SET 
                                `pr_id`='.$purchaseRequestId.' ,
                                `pr_item_id`='.$prItemId.',
                                `delivery_date`="' . $date . '",
                                `delivery_status`="open",
                                `qty`=' . $quantity . ',
                                `remaining_qty`='. $quantity . ',
                                `created_by` = "' . $this->created_by . '",`updated_by`="' . $this->updated_by . '"');
                             //  console($prItemDelInsertObj);

                            }
                            $auditTrail['action_data']['Item Details'][$item['itemCode']]['prId'] = $purchaseRequestId;
                            $auditTrail['action_data']['Item Details'][$item['itemCode']]['itemId'] = $prItemId;
                            $auditTrail['action_data']['Item Details'][$item['itemCode']]['itemCode'] = $item['itemCode'];
                            $auditTrail['action_data']['Item Details'][$item['itemCode']]['itemQuantity'] = $item['qty'];
                            $auditTrail['action_data']['Item Details'][$item['itemCode']]['uom'] = $itemUOM;
                            $auditTrail['action_data']['Item Details'][$item['itemCode']]['remainingQty'] = $item['requiredQty'];
                            $auditTrail['action_data']['Item Details'][$item['itemCode']]['itemNote'] =  $item['purchaseNote'];
                            $auditTrail['action_data']['Item Details'][$item['itemCode']]['itemName'] = $item['itemName'];
                        }
                        $stockTransferList[] = [
                            "itemId" => $item["itemId"],
                            "fromStrorageLoc" => "rmWhOpen",
                            "toStrorageLoc" => "rmProdOpen",
                            "refNumber" => $prodMainItemProductionCode,
                            "quantity" => ($item["consumptionRate"] * $prodMainItemProdQty) - $item["requiredQty"]
                        ];
                    }
                    $auditTrailreturn = generateAuditTrail($auditTrail);
                }
            }
        }
        foreach ($stockTransferList as $oneItem) {
            $stockLogTransferedQty = 0;
            $stockLogObj = itemQtyStockChecking($oneItem["itemId"], "'" . $subItem["fromStrorageLoc"] . "'", ($oneItem["item_sell_type"] == "FIFO" ? "ASC" : "DESC"));
            $itemAvailableStocks = $stockLogObj['sumOfBatches'];
            foreach ($stockLogObj["data"] as $stockLogKey => $stockLogRow) {
                $resultTestObj["stockLogRow"][] = $stockLogRow;

                if ($stockLogTransferedQty == $oneItem["quantity"] || $oneItem["quantity"] < 1) {
                    break;
                }
                if ($stockLogRow['itemQty'] == 0) {
                    continue;
                }


                $usedQuantity = min($stockLogRow['quantity'], $oneItem["quantity"] - $stockLogTransferedQty);
                $stockLogTransferedQty += $usedQuantity;

                $reserveStorageSql = 'SELECT storage_location_id, warehouse_id, storageLocationTypeSlug FROM `erp_storage_location` WHERE company_id=' . $this->company_id . ' AND branch_id=' . $this->branch_id . ' AND location_id=' . $this->location_id . ' AND warehouse_id=' . $stockLogRow['warehouse_id'] . ' AND storageLocationTypeSlug="' . $oneItem["toStrorageLoc"] . '"';
                $reserveStorageObj = $this->dbObj->queryGet($reserveStorageSql)['data'];

                $minusStockSql = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                        SET 
                            companyId = '" . $this->company_id . "',
                            branchId = '" . $this->branch_id . "',
                            locationId = '" . $this->location_id . "',
                            storageLocationId = '" . $stockLogRow['storage_location_id'] . "',
                            storageType ='" . $stockLogRow['storageLocationTypeSlug'] . "',
                            itemId = '" . $oneItem["item_id"] . "',
                            itemQty = '" . $usedQuantity * -1 . "',
                            itemUom = '" . $stockLogRow["itemUom"] . "',
                            itemPrice = '" . $oneItem["rate"] . "',
                            refActivityName='MRP',
                            logRef = '" . $stockLogRow['logRef'] . "',
                            refNumber='" . $oneItem["refNumber"] . "',
                            bornDate='" . $stockLogRow['bornDate'] . "',
                            createdBy = '" . $this->created_by . "',
                            updatedBy = '" . $this->updated_by . "'";

                $plusStockSql = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                        SET
                            companyId = '" . $this->company_id . "',
                            branchId = '" . $this->branch_id . "',
                            locationId = '" . $this->location_id . "',
                            storageLocationId = '" . $reserveStorageObj['storage_location_id'] . "',
                            refActivityName='MRP',
                            logRef = '" . $stockLogRow['logRef'] . "',
                            refNumber='" . $oneItem["refNumber"] . "',
                            bornDate='" . $stockLogRow['bornDate'] . "',
                            storageType ='" . $reserveStorageObj['storageLocationTypeSlug'] . "',
                            itemId = '" . $oneItem["item_id"] . "',
                            itemQty = '" . $usedQuantity . "',
                            itemUom = '" . $stockLogRow["itemUom"] . "',
                            itemPrice = '" .  $oneItem["rate"] . "',
                            createdBy = '" . $this->created_by . "',
                            updatedBy = '" . $this->updated_by . "'";

                $this->dbObj->queryInsert($minusStockSql);
                $this->dbObj->queryInsert($plusStockSql);
            }
        }

      // exit();
        $resultObj = $this->dbObj->queryFinish();
        return $resultObj;
    }
}
