<?php
class ProductionOrderController extends GoodsBomController
{
    function getProductionOrderList($orderStatus = "all", $materialType = "all")
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        $conditon = "";
        if ($orderStatus != "all") {
            if ($orderStatus == "open") {
                $conditon .= ' AND pOrder.Status=9';
            } elseif ($orderStatus == "released") {
                $conditon .= ' AND pOrder.Status=13';
            } elseif ($orderStatus == "closed") {
                $conditon .= ' AND pOrder.Status=10';
            } else {
                $conditon .= ' AND pOrder.Status=13';
            }
        }
        if ($materialType != "all") {
            $conditon .= ' AND goodTypes.type="' . strtoupper($materialType) . '"';
        }
        // $prodOrdList = 'SELECT pOrder.*,items.itemId, items.itemName, items.itemCode, items.itemDesc, items.itemOpenStocks, items.itemBlockStocks FROM `' . ERP_PRODUCTION_ORDER . '` AS pOrder,`' . ERP_INVENTORY_ITEMS . '` AS items WHERE pOrder.`itemId`=items.`itemId` AND pOrder.`location_id`="' . $location_id . '" ORDER BY pOrder.so_por_id DESC';

        $prodOrdList = 'SELECT pOrder.*,items.itemId, items.itemName, items.itemCode, items.itemDesc, items.goodsType, items.itemOpenStocks, items.itemBlockStocks, goodTypes.goodTypeName, goodTypes.type AS goodTypeShortName FROM `' . ERP_PRODUCTION_ORDER . '` AS pOrder LEFT JOIN `' . ERP_INVENTORY_ITEMS . '` AS items ON pOrder.`itemId`=items.`itemId` LEFT JOIN `erp_inventory_mstr_good_types` AS goodTypes ON items.goodsType=goodTypes.goodTypeId WHERE pOrder.`location_id`=' . $location_id . $conditon . ' ORDER BY pOrder.so_por_id DESC';

        return queryGet($prodOrdList, true);
    }

    function getSubProductionOrderList($materialType = "all"){
        global $company_id;
        global $branch_id;
        global $location_id;
        $conditon = "";
        if ($materialType != "all") {
            $conditon .= ' AND goodTypes.type="' . strtoupper($materialType) . '"';
        }

        $prodOrdList = 'SELECT pOrder.*,table_master.table_name,wc.work_center_name,items.itemId, items.itemName, items.itemCode, items.itemDesc, items.goodsType, items.itemOpenStocks, items.itemBlockStocks, goodTypes.goodTypeName, goodTypes.type AS goodTypeShortName, items.baseUnitMeasure as itemUom FROM `erp_production_order_sub` AS pOrder LEFT JOIN `erp_inventory_items` AS items ON pOrder.`itemId`=items.`itemId` LEFT JOIN `erp_inventory_mstr_good_types` AS goodTypes ON items.goodsType=goodTypes.goodTypeId LEFT JOIN `erp_table_master` AS table_master ON pOrder.table_id = table_master.table_id LEFT JOIN `erp_work_center` AS wc ON wc.work_center_id = pOrder.wc_id WHERE pOrder.`location_id`=' . $location_id . $conditon . ' order by sub_prod_id DESC';

        return queryGet($prodOrdList, true);

    }

    function getProductionOrderDetails($prodOrderIdList = [])
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $prodOrderIds = implode(",", $prodOrderIdList);

        return queryGet('SELECT pOrder.*,items.itemId, items.itemName, items.itemCode, items.itemDesc, items.baseUnitMeasure as itemUom, items.itemOpenStocks, items.itemBlockStocks FROM `' . ERP_PRODUCTION_ORDER . '` AS pOrder,`' . ERP_INVENTORY_ITEMS . '` AS items WHERE pOrder.`itemCode`=items.`itemCode` AND pOrder.`location_id`="' . $location_id . '" AND pOrder.so_por_id IN (' . $prodOrderIds . ')', true);
    }


    private $virtualStocks = [];
    // private $virtualStocks = [
    //     566 => [
    //         "itemOpenStocks"=>0,"itemReserveStocks"=>0,"itemTotalQty"=>0,"movingWeightedPrice"=>0
    //     ],
    //     556 => [
    //         "itemOpenStocks"=>0,"itemReserveStocks"=>0,"itemTotalQty"=>0,"movingWeightedPrice"=>0
    //     ],
    //     578 => [
    //         "itemOpenStocks"=>0,"itemReserveStocks"=>0,"itemTotalQty"=>0,"movingWeightedPrice"=>0
    //     ]
    // ];


    function getItemStockDetails($itemId)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        if (isset($this->virtualStocks[$itemId])) {
            return $this->virtualStocks[$itemId];
        } else {
            $queryObj = queryGet('SELECT `itemOpenStocks`,`itemReserveStocks`,`itemTotalQty`,`movingWeightedPrice` FROM `erp_inventory_stocks_summary` WHERE `company_id`=' . $company_id . ' AND `branch_id` = ' . $branch_id . ' AND `location_id`= ' . $location_id . ' AND `itemId`=' . $itemId);
            if ($queryObj["numRows"] == 1) {
                $this->virtualStocks[$itemId] = $queryObj["data"];
                return $queryObj["data"];
            } else {
                $this->virtualStocks[$itemId] = ["itemOpenStocks" => 0, "itemReserveStocks" => 0, "itemTotalQty" => 0, "movingWeightedPrice" => 0];
                return [
                    "itemOpenStocks" => 0, "itemReserveStocks" => 0, "itemTotalQty" => 0, "movingWeightedPrice" => 0
                ];
            }
        }
    }


    public $mrpItemsFinalResultList = [
        "status" => "success",
        "message" => "Data fetched successfully",
        "rmItemsList" => [],
        "sfgItemsList" => []
    ];

    private function addItemsToMrpFinalResultList($itemData = [])
    {
        if (isset($this->mrpItemsFinalResultList["rmItemsList"][$itemData["itemId"]])) {
            $this->mrpItemsFinalResultList["rmItemsList"][$itemData["itemId"]]["totalOpenStocks"] = $itemData;
        } else {
            $this->mrpItemsFinalResultList["rmItemsList"][$itemData["itemId"]] = $itemData;
        }
        return true;
    }

    function mrpItemsList($bomItemList = [], $productionOrderQty = 1)
    {
        foreach ($bomItemList as $oneItemList) {

            $consumptionQty = $oneItemList["itemConsumption"];
            $totalConsumptionQty = $productionOrderQty * $consumptionQty;

            $stocksDetails = $this->getItemStockDetails($oneItemList["itemId"]);

            $oneItemList["totalConsumptionQty"] = $totalConsumptionQty;
            $oneItemList["totalOpenStocks"] = $stocksDetails["itemOpenStocks"];
            $oneItemList["totalReserveStocks"] = $stocksDetails["itemReserveStocks"];

            if ($oneItemList["totalOpenStocks"] >= $oneItemList["totalConsumptionQty"]) {
                $oneItemList["totalExtraRequiredStocks"] = 0;
                $this->virtualStocks[$oneItemList["itemId"]]["totalOpenStocks"] = $this->virtualStocks[$oneItemList["itemId"]]["totalOpenStocks"] - $oneItemList["totalConsumptionQty"];
            } else {
                $oneItemList["totalExtraRequiredStocks"] = $oneItemList["totalConsumptionQty"] - $oneItemList["totalOpenStocks"];
                $this->virtualStocks[$oneItemList["itemId"]]["totalOpenStocks"] = 0;
            }



            if ($oneItemList["materialType"] == "RM") {
                //$this->mrpItemsFinalResultList["rmItemsList"][$oneItemList["itemId"]] = $oneItemList;
                $this->mrpItemsFinalResultList["rmItemsList"][] = $oneItemList;
            } else {

                //$this->mrpItemsFinalResultList["sfgItemsList"][$oneItemList["itemId"]] = $oneItemList;
                $this->mrpItemsFinalResultList["sfgItemsList"][] = $oneItemList;

                $bomDetailsAndItemsObj = $this->getBomAndItemDetails($oneItemList["itemId"]);
                if (count($bomDetailsAndItemsObj["data"]["bomItemDetails"]) > 0) {
                    $sfgBomItemList = $bomDetailsAndItemsObj["data"]["bomItemDetails"];
                    $this->mrpItemsList($sfgBomItemList, $productionOrderQty * $oneItemList["totalExtraRequiredStocks"]);
                } else {
                    $this->mrpItemsFinalResultList["status"] = "warning";
                    $this->mrpItemsFinalResultList["message"] = "Bom items not found for " . $oneItemList["itemCode"];
                }
            }
        }
        return $this->mrpItemsFinalResultList;
    }

    function runMrp($productionOrderIdArr = [])
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $prodOrderDetailsObj = $this->getProductionOrderDetails($productionOrderIdArr);


        if ($prodOrderDetailsObj["status"] != "success") {
            return [
                "status" => "warning",
                "message" => "Invalid production order id",
            ];
        }

        foreach ($prodOrderDetailsObj["data"] as $oneProdOrderDetails) {
            $bomDetailsAndItemsObj = $this->getBomAndItemDetails($oneProdOrderDetails["itemId"]);
            $this->mrpItemsList($bomDetailsAndItemsObj["data"]["bomItemDetails"], $oneProdOrderDetails["qty"]);
        }


        return $this->mrpItemsFinalResultList;
        // return [
        //     "status" => "success",
        //     "message" => "MRP run successfully",
        //     "prodOrderDetails" => $prodOrderDetailsObj["data"],
        //     //"bomDetailsAndItems" => $bomDetailsAndItemsObj["data"],
        //     "mrpData" => [
        //         "rmItemsList" => $itemListObj["rmItemsList"],
        //         "sfgItemsList" => $itemListObj["sfgItemsList"]
        //     ]
        // ];
    }

    function confirmMrp($data = [], $requiredDate = null)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        echo $requiredDate;


        exit();

        $mrpNumber = "MRP" . time() . rand(100, 999);
        $prodOrderDetails = $data["prodOrderDetails"] ?? [];
        $rmItemsList = $data["mrpData"]["rmItemsList"] ?? [];
        $sfgItemsList = $data["mrpData"]["sfgItemsList"] ?? [];
        $expectedDate = $prodOrderDetails["prodOrderDetails"] ?? "";
        foreach ($rmItemsList as $oneRmItem) {
            $stockRequired = $oneRmItem["totalExtraRequiredStocks"];
            if ($stockRequired > 0) {
                $prCode = "PR" . date("Ym") . rand(100, 999);
                $prInsertSql = 'INSERT INTO `' . ERP_BRANCH_PURCHASE_REQUEST . '` SET `prCode`="' . $prCode . '",`company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`expectedDate`="' . $requiredDate . '",`refNo`="' . $mrpNumber . '",`description`="",`created_by`="' . $created_by . '",`updated_by`="' . $updated_by . '"';
                $prInsertObj = queryInsert($prInsertSql);
                if ($prInsertObj["status"] = "success") {
                    $prId = $prInsertObj["insertedId"];
                    $prItemInsertSql = 'INSERT INTO `' . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . '` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`prId`=' . $prId . ',`itemId`=' . $oneRmItem["itemId"] . ',`itemCode`="' . $oneRmItem["itemCode"] . '",`itemName`="' . $oneRmItem["itemName"] . '",`itemQuantity`=' . $stockRequired . ',`uom`="",`itemPrice`="",`itemDiscount`="",`itemTotal`=""';
                    queryInsert($prItemInsertSql);
                    if ($oneRmItem["totalOpenStocks"] > 0) {
                        //stock reserve quantity

                        queryUpdate('UPDATE `erp_inventory_stocks_summary` SET `itemOpenStocks`=0,`itemReserveStocks`=`itemReserveStocks`+' . $oneRmItem["totalOpenStocks"] . ', `updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`itemId`=' . $oneRmItem["itemId"]);
                    }
                }
            } else {
                //stock reserve quantity
                queryUpdate('UPDATE `erp_inventory_stocks_summary` SET `itemOpenStocks`=`itemOpenStocks`-' . $oneRmItem["totalConsumptionQty"] . ',`itemReserveStocks`=`itemReserveStocks`+' . $oneRmItem["totalConsumptionQty"] . ', `updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`itemId`=' . $oneRmItem["itemId"]);
            }
        }

        foreach ($sfgItemsList as $oneSfgItem) {
            $prodCode = "PR" . date("Ymd");
            $stockRequired = $oneSfgItem["totalExtraRequiredStocks"];
            $expectedDate = $requiredDate;
            if ($stockRequired > 0) {
                $sql = 'INSERT INTO `erp_production_order` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`porCode`="' . $prodCode . '",`itemId`=' . $oneSfgItem["itemId"] . ',`itemCode`="' . $oneSfgItem["itemCode"] . '",`refNo`="' . $mrpNumber . '",`qty`=' . $stockRequired . ',`expectedDate`="' . $expectedDate . '",`mrp_status`="Created",`created_by`="' . $created_by . '",`updated_by`="' . $updated_by . '",`status`=13';
                if ($oneSfgItem["totalOpenStocks"] > 0) {
                    //stock reserve quantity
                    queryUpdate('UPDATE `erp_inventory_stocks_summary` SET `itemOpenStocks`=0,`itemReserveStocks`=`itemReserveStocks`+' . $oneSfgItem["totalOpenStocks"] . ', `updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`itemId`=' . $oneSfgItem["itemId"]);
                }
            } else {
                //stock reserve quantity
                queryUpdate('UPDATE `erp_inventory_stocks_summary` SET `itemOpenStocks`=`itemOpenStocks`-' . $oneSfgItem["totalConsumptionQty"] . ',`itemReserveStocks`=`itemReserveStocks`+' . $oneSfgItem["totalConsumptionQty"] . ', `updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`itemId`=' . $oneSfgItem["itemId"]);
            }

            // INSERT INTO `erp_production_order` SET `so_por_id`=[value-1],`company_id`=[value-2],`branch_id`=[value-3],`location_id`=[value-4],`porCode`=[value-5],`itemId`=[value-6],`itemCode`=[value-7],`refNo`=[value-8],`qty`=[value-9],`expectedDate`=[value-10],`description`=[value-11],`mrp_status`=[value-12],`created_at`=[value-13],`created_by`=[value-14],`updated_at`=[value-15],`updated_by`=[value-16],`status`=[value-17] WHERE 1
        }

        return [
            "status" => "success",
            "message" => "success"
        ];
    }


    function createProduction($POST)
    {

        // console($POST);
        // exit();

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;

        global $dbCon;
        $returnData = [];
        // echo 2;
        $isValidate = validate($POST, [
            "expDate" => "required",
            "itemCode" => "required",
            "validitydate" => "required",
            "item_id" => "required",
            "item_qty" => "required"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        $prodCode = "PR" . time() . rand(100, 999);
        // exit();
        $expDate = $POST['expDate'];
        $itemCode = $POST['itemCode'];
        $description = $POST['description'];
        $item_id = $POST['item_id'];
        $item_qty = $POST['item_qty'];
        $refNo = $POST['refNo'];
        $status = $POST['status'] ?? '9';
        $table = $POST['tableId'] ?? 0;
        $wcId = $POST['wcId'] ?? 0;
        $validitydate = $POST['validitydate'];
    
        if ($validitydate < date('Y-m-d')) {
            $returnData['status'] = "warning";
            $returnData['message'] = "Validation Date Wrong";
            return $returnData;
        }


        $mrp_status = $POST['mrp_status'] ?? 'Not Created';
        $prodsql = "INSERT INTO " . ERP_PRODUCTION_ORDER . " 
                SET 
                `porCode`= '" . $prodCode . "' ,
                `itemId`=$item_id,
                `itemCode`=$itemCode,
                `validityperiod`  = '$validitydate',
                `refNo`='" . $refNo . "',
                `qty`= $item_qty,
                `remainQty`= $item_qty,
                `expectedDate`='" . $expDate . "',
                `description`= '" . $description . "',
                `company_id` = $company_id ,
                `status` = $status,
                `wc_id` = $wcId,
                `mrp_status` = '".$mrp_status."',
                `table_id` = $table,
                `branch_id`=$branch_id,
                `location_id`=$location_id,
                `created_by`= '" . $created_by . "',
                `updated_by`='" . $created_by . "'";

              //  exit();

        $productionOrderObj = queryInsert($prodsql);
        // console($prodsql);
        // console($productionOrderObj);

        if ($productionOrderObj['status'] == "success") {
            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrailprod = array();
            $auditTrailprod['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrailprod['basicDetail']['table_name'] = ERP_PRODUCTION_ORDER;
            $auditTrailprod['basicDetail']['column_name'] = 'so_por_id'; // Primary key column
            $auditTrailprod['basicDetail']['document_id'] = $productionOrderObj['insertedId'];  // primary key
            $auditTrailprod['basicDetail']['document_number'] = $prodCode;
            $auditTrailprod['basicDetail']['action_code'] = $action_code;
            $auditTrailprod['basicDetail']['action_referance'] = '';
            $auditTrailprod['basicDetail']['party_id'] = 0;
            $auditTrailprod['basicDetail']['action_title'] = 'Production Order Creation';  //Action comment
            $auditTrailprod['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrailprod['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrailprod['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrailprod['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrailprod['basicDetail']['action_sqlQuery'] = base64_encode($prodsql);
            $auditTrailprod['basicDetail']['others'] = '';
            $auditTrailprod['basicDetail']['remark'] = '';

            $auditTrailprod['action_data']['Production Order Details'][$itemCode]['refNo'] = $refNo;
            $auditTrailprod['action_data']['Production Order Details'][$itemCode]['expectedDate'] = formatDateWeb($expDate);
            $auditTrailprod['action_data']['Production Order Details'][$itemCode]['itemCode'] = $itemCode;
            $auditTrailprod['action_data']['Production Order Details'][$itemCode]['qty'] = decimalQuantityPreview($item_qty);
            $auditTrailprod['action_data']['Production Order Details'][$itemCode]['remainQty'] = decimalQuantityPreview($item_qty);
            $auditTrailprod['action_data']['Production Order Details'][$itemCode]['created_by'] = getCreatedByUser($created_by);
            $auditTrailprod['action_data']['Production Order Details'][$itemCode]['updated_by'] = getCreatedByUser($created_by);

            $auditTrailreturn = generateAuditTrail($auditTrailprod);

            $returnData['status'] = "Success";
            $returnData['message'] = "Production Order Created Sucessfully";
        } else {

            $returnData['status'] = "Warning";
            $returnData['message'] = "Something Went Wrong";
        }
        return $returnData;
    }
}
