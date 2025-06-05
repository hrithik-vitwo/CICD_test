<?php
require_once "func-journal.php";

class BranchSo extends Accounting
{

    private function getInventoryItemParentGl($itemId)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        $pGlIdObj = queryGet('SELECT `parentGlId` FROM `erp_inventory_items` WHERE `company_id` =' . $company_id . ' AND `itemId` =' . $itemId);
        if ($pGlIdObj["numRows"] == 1) {
            return $pGlIdObj["data"]["parentGlId"];
        } else {
            return 0;
        }
    }
    // fetch item summery
    function fetchItemSummary()
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;

        $sql = "SELECT summary.*,items.*,hsn.taxPercentage
                    FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` as summary
                    INNER JOIN  `" . ERP_INVENTORY_ITEMS . "` as items ON summary.itemId=items.itemId
                    RIGHT JOIN `" . ERP_HSN_CODE . "` as hsn ON items.hsnCode = hsn.hsnCode
                    WHERE summary.company_id='$company_id' and items.goodsType=3
        ";
        if ($res = $dbCon->query($sql)) {
            $returnData['status'] = "success";
            $returnData['data'] = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['message'] = $res->num_rows . "data found successfull";
            $returnData['sql'] = $sql;
        } else {
            $returnData['status'] = "warning";
            $returnData['data'] = [];
            $returnData['message'] = "Somthing went wrong!";
        }
        return $returnData;
    }

    // fetch item filter by material
    function fetchItemSummaryMaterials()
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;

        $sql = "SELECT summary.*,items.*,hsn.taxPercentage
        FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` as summary
        INNER JOIN  `" . ERP_INVENTORY_ITEMS . "` as items ON summary.itemId=items.itemId
        RIGHT JOIN `" . ERP_HSN_CODE . "` as hsn ON items.hsnCode = hsn.hsnCode
        WHERE summary.company_id='$company_id' and items.goodsType IN(3,4)
        ";

        if ($res = $dbCon->query($sql)) {
            $returnData['status'] = "success";
            $returnData['data'] = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['message'] = $res->num_rows . "data found successfull";
            $returnData['sql'] = $sql;
        } else {
            $returnData['sql'] = $sql;
            $returnData['status'] = "warning";
            $returnData['data'] = [];
            $returnData['message'] = "Somthing went wrong!";
        }
        return $returnData;
    }
    // fetch item filter by material
    function fetchItemSummaryServices()
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;

        $sql = "SELECT summary.*,items.*,hsn.taxPercentage
        FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` as summary
        INNER JOIN  `" . ERP_INVENTORY_ITEMS . "` as items ON summary.itemId=items.itemId
        RIGHT JOIN `" . ERP_HSN_CODE . "` as hsn ON items.hsnCode = hsn.hsnCode
        WHERE summary.company_id='$company_id' and items.goodsType=5
        ";

        if ($res = $dbCon->query($sql)) {
            $returnData['status'] = "success";
            $returnData['data'] = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['message'] = $res->num_rows . "data found successfull";
            $returnData['sql'] = $sql;
        } else {
            $returnData['sql'] = $sql;
            $returnData['status'] = "warning";
            $returnData['data'] = [];
            $returnData['message'] = "Somthing went wrong!";
        }
        return $returnData;
    }

    function fetchItemSummaryDetails($itemId)
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;

        $sql = "SELECT summary.*,items.*,hsn.taxPercentage, hsn.hsnDescription
                    FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` as summary
                    INNER JOIN  `" . ERP_INVENTORY_ITEMS . "` as items ON summary.itemId=items.itemId
                    RIGHT JOIN `" . ERP_HSN_CODE . "` as hsn ON items.hsnCode = hsn.hsnCode
                    WHERE summary.company_id='$company_id' AND summary.branch_id='$branch_id' AND summary.location_id='$location_id' AND summary.itemId='$itemId'
        ";
        if ($res = $dbCon->query($sql)) {
            $returnData['status'] = "success";
            $returnData['data'] = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['message'] = $res->num_rows . "data found successfull";
        } else {
            $returnData['status'] = "warning";
            $returnData['data'] = [];
            $returnData['message'] = "Somthing went wrong!";
        }
        return $returnData;
    }

    function addBranchSo($POST)
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        // console($_POST);

        $isValidate = validate($POST, [
            "customerId" => "required",
            "deliveryDate" => "required",
            "profitCenter" => "required",
            "customerPO" => "required",
            "kamId" => "required"
        ], [
            "customerId" => "Select a customer",
            "deliveryDate" => "Enter delivery date",
            "profitCenter" => "Choose a profit center",
            "customerPO" => "Enter customer PO",
            "kamId" => "Select a KAM"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        $customerId = $POST['customerId'];
        $soDate = $POST['soDate'];
        $deliveryDate = $POST['deliveryDate'];
        $shippingAddress = $POST['shippingAddress'];
        $billingAddress = $POST['billingAddress'];
        $profitCenter = $POST['profitCenter'];
        $creditPeriod = $POST['creditPeriod'];
        $kamId = $POST['kamId'];
        $goodsType = $POST['goodsType'];
        $customerPO = $POST['customerPO'];
        $approvalStatus = $POST['approvalStatus'];
        $serviceDescription = $POST['otherCostDetails'] ?? 'hello null';

        // ***************
        $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE `status`!='deleted' ORDER BY so_id DESC LIMIT 1";
        $lastSoNo = queryGet($sql);
        // console($lastSoNo);
        if (isset($lastSoNo['data'])) {
            $lastSoNo = $lastSoNo['data']['so_number'] ?? 0;
        } else {
            $lastSoNo = '';
        }
        $returnSoNo = getSoSerialNumber($lastSoNo);
        // ***************

        $ins = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER . "`
                 SET
                   `so_number`='$returnSoNo',
                   `customer_id`='$customerId',
                   `company_id`='$company_id',
                   `branch_id`='$branch_id',
                   `location_id`='$location_id',
                   `so_date`='$soDate',
                   `delivery_date`='$deliveryDate',
                   `billingAddress`='$billingAddress',
                   `shippingAddress`='$shippingAddress',
                   `profit_center`='$profitCenter',
                   `credit_period`='$creditPeriod',
                   `kamId`='$kamId',
                   `goodsType`='$goodsType',
                   `approvalStatus`='$approvalStatus',
                   `customer_po_no`='$customerPO',
                   `created_by`='$created_by',
                   `updated_by`='$updated_by',
                   `soStatus`='open'
      ";
        if ($dbCon->query($ins)) {
            $returnData['status'] = "success";
            $returnData['message'] = "inserted success!";
            $returnData['lastID'] = $dbCon->insert_id;
            $returnData['other'] = $serviceDescription;

            foreach ($serviceDescription as $oneCost) {
                if ($oneCost['services'] != null && $oneCost['amount'] != null) {
                    $description = $oneCost['services'];
                    $amount = $oneCost['amount'];
                    $insert = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_OTHER_COST . "`
                                    SET
                                    `company_id`='$company_id',
                                    `branch_id`='$branch_id',
                                    `location_id`='$location_id',
                                    `so_number`='$returnSoNo',
                                    `description`='$description',
                                    `amount`='$amount',
                                    `created_at`='" . date("Y-m-d H:i:s") . "',
                                    `updated_at`='" . date("Y-m-d H:i:s") . "',
                                    `created_by`='$created_by',
                                    `updated_by`='$updated_by',
                                    `status`='active'
                    ";
                    $dbCon->query($insert);
                    $returnData['sql'] = $insert;
                }
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "somthing went wrong! ";
        }

        return $returnData;
    }

    function addBranchSoItems($POST, $id)
    {
        $returnData = [];
        global $dbCon;

        $isValidate = validate($POST, [
            "listItem" => "required"
        ], [
            "listItem" => "Select at least one item"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE so_id='$id'";
        $getSoNumber =  queryGet($sql)['data']['so_number'];

        $lastId = $id;
        $totalDiscount = 0;
        $totalAmount = 0;
        $listItem = $POST['listItem'] ?? '';
        $totalItems = count($listItem);
        $i = 1;
        if ($totalItems != 0) {
            foreach ($listItem as $item) {
                $tolerance = $item['tolerance'] ?? 0;
                if ($item['tolerance'] != "") {
                    $tolerance = $item['tolerance'];
                } else {
                    $tolerance = 0;
                }
                $countI = $i++;
                $ins = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_ITEMS . "`
                SET
                  `so_id`='$lastId',
                  `lineNo`='$countI',
                  `inventory_item_id`='" . $item['itemId'] . "',
                  `goodsType`='" . $item['goodsType'] . "',
                  `itemCode`='" . $item['itemCode'] . "',
                  `itemName`='" . $item['itemName'] . "',
                  `itemDesc`='" . $item['itemDesc'] . "',
                  `hsnCode`='" . $item['hsnCode'] . "',
                  `unitPrice`='" . $item['unitPrice'] . "',
                  `totalDiscount`='" . $item['totalDiscount'] . "',
                  `itemTotalDiscount`='" . $item['itemTotalDiscount1'] . "',
                  `tax`='" . $item['tax'] . "',
                  `totalTax`='" . $item['itemTotalTax1'] . "',
                  `totalPrice`='" . $item['totalPrice'] . "',
                  `tolerance`='" . $tolerance . "',
                  `qty`='" . $item['qty'] . "',
                  `uom`='" . $item['uom'] . "'
            ";
                if ($dbCon->query($ins)) {
                    $returnData['itemLastID'] = $dbCon->insert_id;
                    $tot = (($item['unitPrice'] * $item['qty']) - $item['itemTotalDiscount1']) + $item['itemTotalTax1'];
                    $dis = ($tot * $item['totalDiscount']) / 100;
                    $totalDiscount = $totalDiscount + $dis;
                    $totalAmount = $totalAmount + $tot;
                    // console($item['deliverySchedule']);
                    foreach ($item['deliverySchedule'] as $delItem) {
                        $insDeli = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` 
                                                SET 
                                                `so_item_id`='" . $returnData['itemLastID'] . "',
                                                `delivery_date`='" . $delItem['multiDeliveryDate'] . "',
                                                `deliveryStatus`='open',
                                                `qty`='" . $delItem['quantity'] . "'
                        ";
                        if ($dbCon->query($insDeli)) {
                            $returnData['status'] = "success";
                            $returnData['message'] = "Order Created Successfully";
                            $returnData['soNumber'] = $getSoNumber;
                            // redirect($_SERVER['PHP_SELF']);
                        } else {
                            $returnData['status'] = "warning";
                            $returnData['message'] = "somthing went wrong! 2";
                        }
                    }
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "somthing went wrong!";
                }
            }
            $updateDeli = "UPDATE `" . ERP_BRANCH_SALES_ORDER . "` 
                            SET 
                                `totalItems`='" . $totalItems . "',
                                `totalDiscount`='" . $totalDiscount . "',
                                `totalAmount`='" . $totalAmount . "' WHERE so_id=" . $lastId . "";
            $dbCon->query($updateDeli);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "select a item";
        }

        return $returnData;
    }

    function fetchBranchSoListing()
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE status='active' ORDER BY so_id DESC";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    function fetchBranchSoPendingListing()
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' AND approvalStatus=14 AND status='active' ORDER BY so_id DESC";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    function fetchBranchSoExceptionalListing()
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' AND approvalStatus=12 AND status='active' ORDER BY so_id DESC";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // service list
    function fetchServicesList()
    {
        global $company_id;

        // $ins = "SELECT * FROM `" . ERP_SERVICES . "` WHERE companyId='" . $company_id . "' AND `status`!='deleted' ORDER BY serviceId DESC";
        $ins = "SELECT summary.*,items.*,hsn.taxPercentage
        FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` as summary
        INNER JOIN  `" . ERP_INVENTORY_ITEMS . "` as items ON summary.itemId=items.itemId
        RIGHT JOIN `" . ERP_HSN_CODE . "` as hsn ON items.hsnCode = hsn.hsnCode
        WHERE summary.company_id='$company_id' and items.goodsType=5";
        return queryGet($ins, true);
    }

    // service details
    function getServiceDetails($serviceId)
    {
        global $company_id;

        $selectSql = "SELECT * FROM `" . ERP_SERVICES . "` WHERE serviceId='" . $serviceId . "' AND companyId = '" . $company_id . "'  AND `status`!='deleted'";
        return queryGet($selectSql);
    }

    function fetchBranchSoApprovedListing()
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' AND approvalStatus=9 AND status='active' ORDER BY so_id DESC";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    function fetchAllSoDeliverySchedule()
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT sales_order.so_id as so_id, sales_order.so_number as so_number, sales_order.delivery_date as delivery_date, sales_order.customer_id as customer_id, sales_order.billingAddress as billing_address, sales_order.shippingAddress as shipping_address, sales_order.so_date as so_date, sales_order.credit_period as credit_period, items.so_item_id as so_item_id, items.itemCode as itemCode, items.qty as total_quantity, items.uom as uom, items.tax as tax, items.totalDiscount as total_discount, items.totalPrice as item_total_price, delivery.so_delivery_id, delivery.delivery_date, delivery.deliveryStatus, delivery.qty as delivery_qty FROM erp_branch_sales_order_items as items, erp_branch_sales_order as sales_order, erp_branch_sales_order_delivery_schedule as delivery WHERE sales_order.so_id = items.so_id AND items.so_item_id = delivery.so_item_id ORDER BY items.so_item_id DESC";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }
    function fetchBranchSoItems($soId)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` WHERE `so_id`='$soId' AND status='active'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    function fetchBranchSoItemsDeliverySchedule($soItemId)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` WHERE `so_item_id`='$soItemId' AND status='active' AND deliveryStatus!='production'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch by id
    function fetchBranchSoItemsDeliveryScheduleById($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` WHERE `so_delivery_id`='$id' AND status='active'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch by id
    function fetchBranchSoItemsDeliverySchedule2($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` WHERE `so_item_id`='$id' AND status='active'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    function fetchCustomerList()
    {
        $returnData = [];
        global $dbCon;
        global $company_id;

        $ins = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE company_id=$company_id AND customer_status='active'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    function fetchCustomerDetails($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$id'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    function fetchCustomerAddressDetails($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_CUSTOMER_ADDRESS . "` WHERE `customer_id`='$id'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    function fetchSoDetailsById($soId)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE `so_number`='$soId'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // add branch SO delivery 
    function branchSoDeliveryCreate($POST)
    {
        // console($POST);
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        $soNumber = $POST['soNumber'];
        $soId = $POST['soId'] ?? 0;
        $customerId = $POST['customerId'];
        $deliveryCreationDate = $POST['deliveryCreationDate'];
        $soDeliveryPostingDate = $POST['soDeliveryPostingDate'];
        $so_number = $POST['so_number'];
        $customer_shipping_address = $POST['customer_shipping_address'];
        $customer_billing_address = $POST['customer_billing_address'];
        $profitCenter = $POST['profitCenter'];
        $customerPO = $POST['customerPO'];

        // ***************
        // $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "` WHERE `status`='active' ORDER BY so_delivery_id DESC LIMIT 1";
        // $lastDeliveryNo = queryGet($sql);
        // // console($lastSoNo);
        // if (isset($lastSoNo['data'])) {
        //     $lastSoNo = $lastSoNo['data']['delivery_no'] ?? 0;
        // } else {
        //     $lastSoNo = '';
        // }
        // $serialDeliveryNo = getSODelevarySerialNumber($SOSerialNumber, $lastDeliveryNo);

        $deliveryNo = time() . rand(100, 999);

        // check item quantity
        $itemList = $POST["listItem"];
        // console($itemList);
        $noOfItemsWhoDontHaveStocks = 0;
        foreach ($itemList as $key => $oneItem) {
            $oneItemCode = $oneItem["itemCode"];
            $inventoryItemId = $oneItem["inventoryItemId"];
            $oneItemStockObj = queryGet('SELECT `fgWhOpen`,`fgWhReserve` FROM `' . ERP_INVENTORY_STOCKS_SUMMARY . '` WHERE `itemId`="' . $inventoryItemId . '"');
            $oneItemOpenStock = $oneItemStockObj["data"]["fgWhOpen"] ?? 0;
            $itemList[$key]["availableOpenStocks"] = $oneItemOpenStock;
            $itemList[$key]["availableBlockStocks"] = $oneItemStockObj["data"]["fgWhReserve"] ?? 0;
            if ($oneItemOpenStock <= 0) {
                $noOfItemsWhoDontHaveStocks++;
            }
        }

        if (count($itemList) == $noOfItemsWhoDontHaveStocks) {
            //items don't have stocks so we need to create only production order, nothing else.
            // print_r('production imran59059');
            $productionOrderFailedNo = 0;
            $prOrderObjFailedNo = 0;
            foreach ($itemList as $oneItem) {

                $itemId = $oneItem["itemId"];
                $itemCode = $oneItem["itemCode"];
                $itemQty = $oneItem["qty"];
                $itemName = $oneItem["itemName"];
                $uom = $oneItem["uom"];
                $unitPrice = $oneItem["unitPrice"];
                $itemDiscount = $oneItem["itemDiscount"];
                $itemDeliveryDateId = $oneItem["itemDeliveryDateId"];
                $itemOpenStocks = $oneItem["availableOpenStocks"];
                if ($oneItem['qty'] != "") {
                    if ($oneItem['goodsType'] == 3) {
                        //only production order insert
                        $proCode = "PRO" . date("Ym") . rand(100, 999);

                        $sql = "INSERT INTO `" . ERP_PRODUCTION_ORDER . "`
                            SET 
                            `porCode`='$proCode',
                            `refNo`='$so_number',
                            `itemCode`='" . $itemCode . "',
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='" . $location_id . "',
                            `created_by`='$created_by',
                            `updated_by`='" . $updated_by . "',
                            `qty`='$itemQty'";
                        $productionOrderObj = queryInsert($sql);
                        return [
                            "status" => "success",
                            "message" => "Production Order Generated Successfully"
                        ];
                    } elseif ($oneItem['goodsType'] == 4) {
                        $prCode = "PR" . date("Ym") . rand(100, 999);
                        $pr_date = date('Y-m-d');
                        $sql = "INSERT INTO `" . ERP_BRANCH_PURCHASE_REQUEST . "` 
                                    SET
                                        `prCode`='$prCode',
                                        `company_id`='$company_id',
                                        `branch_id`='$branch_id',
                                        `location_id`='$location_id',
                                        `expectedDate`='$soDeliveryPostingDate',
                                        `pr_date`='$pr_date',
                                        `pr_type`='material',
                                        `refNo`='$so_number',
                                        `pr_status`='active',
                                        `status`='9',
                                        `created_by`='$created_by',
                                        `updated_by`='$updated_by' ";
                        $prOrderObj = queryInsert($sql);
                        $lastID = $prOrderObj['insertedId'];
                        $sqlItem = "INSERT `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "` 
                                        SET 
                                            `company_id`='$company_id',
                                            `branch_id`='$branch_id',
                                            `location_id`='$location_id',
                                            `prId`='$lastID',
                                            `itemId`='$itemId',
                                            `itemCode`='$itemCode',
                                            `itemName`='$itemName',
                                            `itemQuantity`='$itemQty',
                                            `uom`='$uom',
                                            `itemPrice`='$unitPrice',
                                            `itemDiscount`='$itemDiscount'";
                        $prOrderItemObj = queryInsert($sqlItem);
                        return [
                            "status" => "success",
                            "message" => "Purchase Request Created Successfully"
                        ];
                    }
                }

                // if ($productionOrderObj["status"] != "success") {
                //     $productionOrderFailedNo++;
                // }
                // if ($prOrderObj["status"] != "success") {
                //     $prOrderObjFailedNo++;
                // }
            }

            // if ($productionOrderFailedNo == 0) {
            //     return [
            //         "status" => "success",
            //         "message" => "Production Order Generated Successfully"
            //     ];
            // } else {
            //     return [
            //         "status" => "warning",
            //         "message" => "Production Order Generated Failed"
            //     ];
            // }
        } else {
            //so delivery creation here
            $sql = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "`
                            SET
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `delivery_no`='$deliveryNo',
                            `so_number`='$soNumber',
                            `customer_shipping_address`='$customer_shipping_address',
                            `customer_billing_address`='$customer_billing_address',
                            `so_id`='$soId',
                            `customer_id`='$customerId',
                            `delivery_date`='$deliveryCreationDate',
                            `so_delivery_posting_date`='$soDeliveryPostingDate',
                            `profit_center`='$profitCenter',
                            `deliveryStatus`='open',
                            `customer_po_no`='$customerPO'";

            $soDeliveryCreationObj = queryInsert($sql);
            $deliveryLastId = $soDeliveryCreationObj['insertedId'];

            $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "` WHERE so_delivery_id='$deliveryLastId'";
            $getDeliveryNumber =  queryGet($sql)['data']['delivery_no'];


            if ($soDeliveryCreationObj["status"] == "success") {
                $itemTotalDiscount = 0;
                $itemTotalPrice = 0;
                $totalprice = 0;
                // $acc=array();

                foreach ($itemList as $oneItem) {
                    $totalItem = count($itemList);

                    $itemId = $oneItem["itemId"];
                    $inventoryItemId = $oneItem["inventoryItemId"];
                    $itemLineNo = $oneItem["lineNo"];
                    $itemDeliveryDateId = $oneItem["itemDeliveryDateId"];
                    $itemCode = $oneItem["itemCode"];
                    $itemDesc = $oneItem["itemDesc"];
                    $itemName = $oneItem["itemName"];
                    $hsnCode = $oneItem["hsnCode"];
                    $tax = $oneItem["tax"];
                    $totalTax = $oneItem["totalTax"];
                    $tolerance = $oneItem["tolerance"] ?? 0;
                    $totalDiscount = $oneItem["totalDiscount"];
                    $itemTotalDiscount = $oneItem["itemTotalDiscount"];
                    $unitPrice = $oneItem["unitPrice"];
                    $itemTotalPrice = $oneItem["itemTotalPrice"];
                    $itemQty = $oneItem["qty"];
                    $itemUom = $oneItem["uom"];
                    $itemOpenStocks = $oneItem["availableOpenStocks"];
                    $itemBlockStocks = $oneItem["availableBlockStocks"];

                    // $itemTotalDiscount += $oneItem["itemTotalDiscount"];
                    $totalprice += $itemTotalPrice;
                    if ($oneItem['qty'] != "") {
                        if ($itemOpenStocks == 0) {
                            //only production order insert 
                            $prCode = "PR" . date("Ym") . rand(100, 999);
                            $sql = "INSERT INTO `" . ERP_PRODUCTION_ORDER . "`
                                    SET 
                                    `porCode`='$prCode',
                                    `refNo`='$so_number',
                                    `itemCode`='" . $itemCode . "',
                                    `company_id`='$company_id',
                                    `branch_id`='$branch_id',
                                    `location_id`='" . $location_id . "',
                                    `created_by`='$created_by',
                                    `updated_by`='" . $updated_by . "',
                                    `qty`='$itemQty'";

                            $productionOrderObj = queryInsert($sql);
                        } elseif ($itemOpenStocks >= $itemQty) {
                            //delivery items creation and update the stocks
                            $sql = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_DELIVERY_ITEMS . "`
                                            SET
                                            `so_delivery_id`='" . $soDeliveryCreationObj["insertedId"] . "',
                                            `lineNo`='" . $itemLineNo . "',
                                            `inventory_item_id`='" . $inventoryItemId . "',
                                            `itemCode`='" . $itemCode . "',
                                            `itemDesc`='" . $itemDesc . "',
                                            `itemName`='" . $itemName . "',
                                            `delivery_date`='" . $itemDeliveryDateId . "',
                                            `hsnCode`='" . $hsnCode . "',
                                            `tax`='" . $tax . "',
                                            `totalTax`='" . $totalTax . "',
                                            `tolerance`='" . $tolerance . "',
                                            `totalDiscount`='" . $totalDiscount . "',
                                            `totalDiscountAmt`='" . $itemTotalDiscount . "',
                                            `unitPrice`='" . $unitPrice . "',
                                            `totalPrice`='" . $itemTotalPrice . "',
                                            `qty`='" . $itemQty . "',
                                            `uom`='" . $itemUom . "'
                                ";
                            $deliveryItemsCreationsObj = queryInsert($sql);

                            if ($deliveryItemsCreationsObj["status"] == "success") {
                                queryUpdate("UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` 
                                    SET 
                                        `remainingQty`='0',
                                        `deliveryStatus`='production' 
                                    WHERE 
                                        so_delivery_id='" . $itemDeliveryDateId . "'");

                                $sql = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "` 
                                        SET
                                            `totalItems`='" . $totalItem . "',
                                            `totalDiscount`='" . $itemTotalDiscount . "',
                                            `totalAmount`='" . $itemTotalPrice . "'
                            WHERE `so_delivery_id`='" . $deliveryLastId . "'";
                                $updateDeliveryObj = queryUpdate($sql);
                                $upd = "UPDATE `" . ERP_INVENTORY_STOCKS_SUMMARY . "` SET `fgWhOpen`= `fgWhOpen`-" . $itemQty . " , `fgWhReserve`='" . ($itemBlockStocks + $itemQty) . "' WHERE itemId='" . $inventoryItemId . "'";
                                $updateItemStocksObj = queryUpdate($upd);

                                $itemQtyMin = '-' . $itemQty;
                                // echo "imran59059";
                                $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                    SET 
                                                        companyId = '" . $company_id . "',
                                                        branchId = '" . $branch_id . "',
                                                        locationId = '" . $location_id . "',
                                                        storageLocationId = 7,
                                                        itemId = '" . $inventoryItemId . "',
                                                        itemQty = '" . $itemQtyMin . "',
                                                        itemUom = '" . $itemUom . "',
                                                        itemPrice = '" . $unitPrice . "',
                                                        logRef = '" . $soDeliveryCreationObj['insertedId'] . "',
                                                        createdBy = '" . $created_by . "',
                                                        updatedBy = '" . $updated_by . "'
                            ";
                                $dbCon->query($insStockSummary1);
                                $insStockSummary2 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                    SET 
                                                        companyId = '" . $company_id . "',
                                                        branchId = '" . $branch_id . "',
                                                        locationId = '" . $location_id . "',
                                                        storageLocationId = 8,
                                                        itemId = '" . $inventoryItemId . "',
                                                        itemQty = '" . $itemQty . "',
                                                        itemUom = '" . $itemUom . "',
                                                        itemPrice = '" . $unitPrice . "',
                                                        logRef = '" . $soDeliveryCreationObj['insertedId'] . "',
                                                        createdBy = '" . $created_by . "',
                                                        updatedBy = '" . $updated_by . "'
                            ";
                                $dbCon->query($insStockSummary2);
                            }
                        } else {
                            //delivery items creation and update the stocks and also production order insert
                            $deliveryItemCreationSql = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_DELIVERY_ITEMS . "`
                                                        SET
                                                        `so_delivery_id`='" . $soDeliveryCreationObj["insertedId"] . "',
                                                        `lineNo`='" . $itemLineNo . "',
                                                        `inventory_item_id`='" . $inventoryItemId . "',
                                                        `itemCode`='" . $itemCode . "',
                                                        `itemDesc`='" . $itemDesc . "',
                                                        `itemName`='" . $itemName . "',
                                                        `delivery_date`='" . $itemDeliveryDateId . "',
                                                        `hsnCode`='" . $hsnCode . "',
                                                        `tax`='" . $tax . "',
                                                        `totalTax`='" . $totalTax . "',
                                                        `tolerance`='" . $tolerance . "',
                                                        `totalDiscount`='" . $totalDiscount . "',
                                                        `totalDiscountAmt`='" . $itemTotalDiscount . "',
                                                        `unitPrice`='" . $unitPrice . "',
                                                        `totalPrice`='" . $itemTotalPrice . "',
                                                        `qty`='" . $itemOpenStocks . "',
                                                        `uom`='" . $itemUom . "'
                        ";
                            $deliveryItemCreationSqlObj = queryInsert($deliveryItemCreationSql);

                            if ($deliveryItemCreationSqlObj["status"] == "success") {
                                $prCode = "PR" . date("Ym") . rand(100, 999);
                                $sql = "INSERT INTO `" . ERP_PRODUCTION_ORDER . "`
                                        SET 
                                        `porCode`='$prCode',
                                        `refNo`='$so_number',
                                        `itemCode`='" . $itemCode . "',
                                        `company_id`='$company_id',
                                        `branch_id`='$branch_id',
                                        `location_id`='" . $location_id . "',
                                        `created_by`='$created_by',
                                        `updated_by`='" . $updated_by . "',
                                        `qty`='" . ($itemQty - $itemOpenStocks) . "'";

                                $productionOrderObj = queryInsert($sql);



                                if ($productionOrderObj["status"] == "success") {

                                    queryUpdate("UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` 
                                    SET 
                                        `remainingQty`='" . ($itemQty - $itemOpenStocks) . "',
                                        `deliveryStatus`='pending' 
                                    WHERE 
                                        so_delivery_id='" . $itemDeliveryDateId . "'");

                                    $sql = "UPDATE `" . ERP_INVENTORY_STOCKS_SUMMARY . "` SET `fgWhOpen`='0', `fgWhReserve`='" . ($itemBlockStocks + $itemOpenStocks) . "' WHERE itemId='" . $inventoryItemId . "'";
                                    queryUpdate($sql);
                                }
                            }
                        }
                    }
                }
                return [
                    "status" => "success",
                    "message" => "Delivery Created Successfully",
                    "deliveryNo" => $getDeliveryNumber
                ];
            } else {
                return [
                    "status" => "warning",
                    "message" => "Delivery creation failed, try again!"
                ];
            }
        }
    }

    // fetch branch so delivery listing 
    function fetchBranchSoDeliveryListing()
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "` WHERE company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' AND status='active' ORDER BY so_delivery_id DESC";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch branch so delivery listing 
    function fetchBranchSoDeliveryById($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "` WHERE so_delivery_id='$id' AND status='active'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch branch so delivery listing 
    function fetchBranchSoDeliveryItems($soId)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY_ITEMS . "` WHERE `so_delivery_id`='$soId' AND status='active'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch COMPANY FUNCTIONALITIES 
    function fetchFunctionality()
    {
        $returnData = [];
        global $dbCon;
        global $company_id;

        $ins = "SELECT * FROM `" . ERP_COMPANY_FUNCTIONALITIES . "` WHERE company_id='" . $company_id . "'  AND functionalities_status='active'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch COMPANY FUNCTIONALITIES by ID
    function fetchFunctionalityById($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_COMPANY_FUNCTIONALITIES . "` WHERE functionalities_id='$id' AND functionalities_status='active'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_assoc();
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch COMPANY Details
    function fetchCompanyDetailsById($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_COMPANIES . "` WHERE company_id='$id'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_assoc();
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch COMPANY Details
    function fetchCompanyDetails()
    {
        global $company_id;
        $company = queryGet("SELECT * FROM `" . ERP_COMPANIES . "` WHERE company_id='$company_id'");
        return $company;
    }

    // fetch COMPANY Details
    function fetchBranchDetailsById($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCHES . "` WHERE branch_id='$id'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_assoc();
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch COMPANY Details
    function fetchBranchAdminDetailsById($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE fldAdminKey='$id'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_assoc();
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch COMPANY Details
    function fetchBranchLocalionDetailsById($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE othersLocation_id='$id'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_assoc();
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    function insertBranchPgi($POST)
    {
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        $returnData = [];

        $deliveryNo = $POST['deliveryNo'];
        $lastQuery = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "` WHERE delivery_no='$deliveryNo' AND `status`!='deleted' ORDER BY so_delivery_pgi_id DESC LIMIT 1";
        $last = queryGet($lastQuery);

        $lastRow = $last['data'] ?? "";
        $lastsl = $lastRow['rfqCode'] ?? null;
        $pgiNo = getSODelevaryPGISerialNumber($deliveryNo, $lastsl);

        $soNumber = $POST['soNumber'];
        $pgiDate = $POST['pgiDate'];
        $customerId = $POST['customerId'];
        $customer_billing_address = $POST['customer_billing_address'];
        $customer_shipping_address = $POST['customer_shipping_address'];
        $profitCenter = $POST['profitCenter'];
        $customerPO = $POST['customerPO'];


        $deliSche = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "` 
                            SET 
                                `company_id` = '" . $company_id . "',
                                `branch_id` = '" . $branch_id . "',
                                `location_id` = '" . $location_id . "',
                                `pgi_no`='$pgiNo',
                                `delivery_no`='$deliveryNo',
                                `so_number`='$soNumber',
                                `customer_id`='$customerId',
                                `customer_billing_address`='$customer_billing_address',
                                `customer_shipping_address`='$customer_shipping_address',
                                `pgiDate`='$pgiDate',
                                `profit_center`='$profitCenter',
                                `pgiStatus`='open',
                                `invoiceStatus`='9',
                                `customer_po_no`='$customerPO',
                                `created_by` = '" . $created_by . "',
                                `updated_by` = '" . $updated_by . "'
        ";
        if ($dbCon->query($deliSche)) {
            $returnData['lastID'] = $dbCon->insert_id;

            // $invNo = "INV" . rand(0000, 9999);
            // $invInsert = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
            //                 SET 
            //                     `invoice_no`='$invNo',
            //                     `pgi_no`='" . $returnData['lastID'] . "',
            //                     `delivery_no`='$deliveryNo',
            //                     `so_number`='$soNumber',
            //                     `customer_id`='$customerId',
            //                     `delivery_date`='$pgiDate',
            //                     `invoiceStatus`='open',
            //                     `profit_center`='$profitCenter',
            //                     `customer_po_no`='$customerPO'
            // ";
            // if ($dbCon->query($invInsert)) {
            //     $returnData['success'] = "true";
            //     $returnData['message'] = "inserted success!";
            //     $returnData['lastInvNo'] = $dbCon->insert_id;
            // } else {
            //     $returnData['success'] = "false";
            //     $returnData['message'] = "somthing went wrong! 1";
            // }
            $returnData['status'] = "success";
            $returnData['message'] = "Inserted Successfull";
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "somthing went wrong! 2";
        }
        return $returnData;
    }


    // add branch so delivery pgi items
    function insertBranchPgiItems($POST, $id)
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        global $updated_by;
        global $created_by;

        $lastId = $id;
        $listItem = $POST['listItem'];
        $itemTotalDiscount = 0;
        $itemTotalPrice = 0;
        $totalItems = count($listItem);

        $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "` WHERE so_delivery_pgi_id='$id'";
        $getPgiNumber =  queryGet($sql)['data']['pgi_no'];

        foreach ($listItem as $key => $item) {

            $itemTotalDiscount += $item["itemTotalDiscount"];
            $itemTotalPrice    += $item["itemTotalPrice"];

            $ins = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_DELIVERY_ITEMS_PGI . "`
                SET
                  `inventory_item_id`='" . $item['inventoryItemId'] . "',
                  `so_delivery_pgi_id`='$lastId',
                  `lineNo`='" . $item['lineNo'] . "',
                  `itemCode`='" . $item['itemCode'] . "',
                  `itemName`='" . $item['itemName'] . "',
                  `itemDesc`='" . $item['itemDesc'] . "',
                  `delivery_date`='" . $item['deliveryDate'] . "',
                  `qty`='" . $item['itemQty'] . "',
                  `enterQty`='" . $item['enterQty'] . "',
                  `uom`='" . $item['uom'] . "',
                  `hsnCode`='" . $item['hsnCode'] . "',
                  `tax`='" . $item['tax'] . "',
                  `totalTax`='" . $item['totalTax'] . "',
                  `tolerance`='" . $item['tolerance'] . "',
                  `totalDiscount`='" . $item['totalDiscount'] . "',
                  `totalDiscountAmt`='" . $item['itemTotalDiscount'] . "',
                  `unitPrice`='" . $item['unitPrice'] . "',
                  `totalPrice`='" . $item['itemTotalPrice'] . "'
      ";
            //   console($ins);
            if ($res = $dbCon->query($ins)) {
                $lastID1 = $dbCon->insert_id;

                // ############################################################
                $oneItemStockObj = queryGet('SELECT `fgWhOpen`,`fgWhReserve`,`fgMktOpen` FROM `' . ERP_INVENTORY_STOCKS_SUMMARY . '` WHERE `itemId`="' . $item['inventoryItemId'] . '"');
                $fgWhOpen = $listItem[$key]["fgWhOpen"] = $oneItemStockObj["data"]["fgWhOpen"] ?? 0;
                $fgWhReserve = $listItem[$key]["fgWhReserve"] = $oneItemStockObj["data"]["fgWhReserve"] ?? 0;
                $fgMktOpen = $listItem[$key]["fgMktOpen"] = $oneItemStockObj["data"]["fgMktOpen"] ?? 0;
                // ##########################
                // console('imran5050');
                // console($listItem);

                $deliItem = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_ITEMS . "`
                                SET
                                `deliveryStatus`='pgi' 
                            WHERE `so_delivery_item_id`='" . $item['so_delivery_item_id'] . "'";
                $dbCon->query($deliItem);

                $upd = "UPDATE `" . ERP_INVENTORY_STOCKS_SUMMARY . "` SET `fgWhReserve`= `fgWhReserve`-" . $item['itemQty'] . " , `fgMktOpen`='" . ($fgMktOpen + $item['itemQty']) . "' WHERE itemId='" . $item['inventoryItemId'] . "'";
                $updateItemStocksObj = queryUpdate($upd);

                // log maintant
                $itemQtyMin = '-' . $item['itemQty'];
                $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                    SET 
                                                        companyId = '" . $company_id . "',
                                                        branchId = '" . $branch_id . "',
                                                        locationId = '" . $location_id . "',
                                                        storageLocationId = 8,
                                                        itemId = '" . $item['inventoryItemId'] . "',
                                                        itemQty = '" . $itemQtyMin . "',
                                                        itemUom = '" . $item['uom'] . "',
                                                        itemPrice = '" . $item['unitPrice'] . "',
                                                        logRef = '" . $lastID1 . "',
                                                        createdBy = '" . $created_by . "',
                                                        updatedBy = '" . $updated_by . "'
                            ";
                $dbCon->query($insStockSummary1);
                $insStockSummary2 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                    SET 
                                                        companyId = '" . $company_id . "',
                                                        branchId = '" . $branch_id . "',
                                                        locationId = '" . $location_id . "',
                                                        storageLocationId = 9,
                                                        itemId = '" . $item['inventoryItemId'] . "',
                                                        itemQty = '" . $item['itemQty'] . "',
                                                        itemUom = '" . $item['uom'] . "',
                                                        itemPrice = '" . $item['unitPrice'] . "',
                                                        logRef = '" . $lastID1 . "',
                                                        createdBy = '" . $created_by . "',
                                                        updatedBy = '" . $updated_by . "'
                            ";
                $dbCon->query($insStockSummary2);

                // $ins = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICE_ITEMS . "`
                //             SET
                //             `so_invoice_id`='$invNo',
                //             `lineNo`='" . $item['lineNo'] . "',
                //             `itemCode`='" . $item['itemCode'] . "',
                //             `itemName`='" . $item['itemName'] . "',
                //             `delivery_date`='" . $item['deliveryDate'] . "',
                //             `qty`='" . $item['itemQty'] . "',
                //             `enterQty`='" . $item['enterQty'] . "',
                //             `uom`='" . $item['uom'] . "'
                // ";
                // if ($res = $dbCon->query($ins)) {
                //     $returnData['success'] = "true";
                //     $returnData['message'] = "insert successfull 2!";
                // } else {
                //     $returnData['success'] = "false";
                //     $returnData['message'] = "somthing went wrong! 3";
                // }
                $returnData['status'] = "success";
                $returnData['message'] = "PGI Created Successfully";
                $returnData['pgiNo'] = $getPgiNumber;
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "somthing went wrong! 4";
            }
        }

        $updateDeli = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "` 
                        SET 
                            `totalItems`='" . $totalItems . "',
                            `totalDiscount`='" . $itemTotalDiscount . "',
                            `totalAmount`='" . $itemTotalPrice . "'
                        WHERE so_delivery_pgi_id=" . $lastId . "";
        $dbCon->query($updateDeli);

        // $inv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
        //                 SET 
        //                     `totalItems`='" . $totalItems . "' WHERE so_invoice_id=" . $invNo . "";
        // $dbCon->query($inv);

        return $returnData;
    }

    // insert collect payment
    function insertCollectPayment($POST, $FILES)
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        // console($POST);
        // exit;
        $collectPayment = $POST['paymentDetails']['collectPayment'];
        $customerId = $POST['paymentDetails']['customerId'];
        $totalDueAmt = $POST['paymentDetails']['totalDueAmt'];
        $totalInvAmt = $POST['paymentDetails']['totalInvAmt'];
        $remaningAmt = $POST['paymentDetails']['remaningAmt'];
        $bankId = $POST['paymentDetails']['bankId'] ?? 0;
        $advancedPayAmt = $POST['paymentDetails']['advancedPayAmt'];
        $paymentCollectType = "";
        if ($POST['paymentDetails']['paymentCollectType'] == "collect") {
            $paymentCollectType = "collect";
        } elseif ($POST['paymentDetails']['paymentCollectType'] == "adjust") {
            $paymentCollectType = "adjust";
        }
        $documentDate = $POST['paymentDetails']['documentDate'];
        $tranactionId = $POST['paymentDetails']['tnxDocNo'];
        $postingDate = $POST['paymentDetails']['postingDate'];


        $payment_advice = date('dmY') . rand(1111, 9999) . '_' . $POST['paymentDetails']['paymentAdviceImg'];
        /*if(!empty($POST['paymentDetails']['paymentAdviceImg'])){
        $paymentAdviceImg=uploadFile( $POST['paymentDetails']['paymentAdviceImg'], "../../../public/storage/invoices/payment-advice/",["jpg","png","ico","jpeg"]);
            if($paymentAdviceImg["status"]=="success"){
                $payment_advice=$paymentAdviceImg["data"];
            }else{
                $payment_advice='';
            }
         } */
        // if($logoObj["status"]=="success"){
        // console('payment advice image******************', $paymentAdviceImg);
        // console($paymentAdviceImg);
        // console('payment advice image******************', $paymentAdviceImg);

        echo $ins = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_PAYMENTS . "` 
                    SET
                        `customer_id`='$customerId',
                        `collect_payment`='$collectPayment',
                        `company_id`='$company_id',
                        `branch_id`='$branch_id',
                        `location_id`='$location_id',
                        `bank_id`='$bankId',
                        `payment_advice`='$payment_advice',
                        `paymentCollectType`='$paymentCollectType',
                        `documentDate`='$documentDate',
                        `transactionId`='$tranactionId',
                        `postingDate`='$postingDate',
                        `remarks`='$customerId',
                        `created_by`='$created_by',
                        `updated_by`='$updated_by'
        ";
        // console($ins);
        if ($dbCon->query($ins)) {
            $paymentId = $dbCon->insert_id;

            $paymentInvItems = $POST['paymentInvoiceDetails'];
            //All Details of payments
            $remaining_amt = $remaningAmt ?? 0;
            $enter_amt = $collectPayment ?? 0;
            $adv_amt = $advancedPayAmt;
            if (!isset($adv_amt) || $adv_amt == "") {
                $adv_amt = 0;
            }

            $total_amt = 0;
            $invoiceConcadinate = '';
            foreach ($paymentInvItems as $one) {
                $invoiceId = $one['invoiceId'] ?? 0;
                $invoiceConcadinate .= $one['invoiceNo'];
                $invAmt = $one['invAmt'];
                $recAmt = $one['recAmt'];
                $dueAmt = $one['dueAmt'];
                // $calDueAmt = $dueAmt - $recAmt;
                if (isset($recAmt) && $recAmt != null) {
                    $total_amt += $recAmt;
                    $calPartialPaidAmt = ($dueAmt - $recAmt);
                    $insItem = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` 
                        SET
                            `payment_id`='$paymentId',
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `customer_id`='$customerId',
                            `invoice_id`='$invoiceId',
                            `payment_type`='pay',
                            `payment_amt`='$recAmt',
                            `remarks`='$invoiceId' ,
                            `created_by`='$created_by',
                            `updated_by`='$updated_by'
                        ";

                    if ($dbCon->query($insItem)) {
                        $returnData['status'] = "success";
                        $returnData['message'] = "Inserted Successfull";
                        if ($recAmt < $dueAmt) {
                            // update invoice items
                            $upd = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                        SET
                            `invoiceStatus`='2',
                            `due_amount`='$calPartialPaidAmt' WHERE `so_invoice_id`='$invoiceId'";
                            $dbCon->query($upd);
                            // console($returnData['ss'] = $upd);
                        } else if ($recAmt == $dueAmt) {
                            // update invoice items
                            $upd = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                        SET
                            `invoiceStatus`='4',
                            `due_amount`='0' WHERE `so_invoice_id`='$invoiceId'";
                            $dbCon->query($upd);
                            // console($returnData['ss'] = $upd);
                        } else {
                            // update invoice items
                            $upd = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                        SET
                            `invoiceStatus`='1',
                            `due_amount`='5' WHERE `so_invoice_id`='$invoiceId'";
                            $dbCon->query($upd);
                            // console($returnData['ss'] = $upd);
                        }
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "Somthing went wrong";
                    }
                } else {
                    continue;
                }
            }
            //-----------------------------Collection ACC Start----------------
            $collectionInputData = [
                "BasicDetails" => [
                    "documentNo" => $tranactionId, // Invoice Doc Number
                    "documentDate" => $documentDate, // Invoice number
                    "postingDate" =>  $postingDate, // current date
                    "reference" => $tranactionId, // T code
                    "remarks" => "Payment collection for - " . $invoiceConcadinate,
                    "journalEntryReference" => "Collection"
                ],
                "paymentDetails" => $POST['paymentDetails'],
                "customerDetails" => $this->fetchCustomerDetails($customerId)['data'][0],
                "paymentInvItems" => $paymentInvItems
            ];
            //console($ivPostingInputData);
            $collectionObj = $this->collectionAccountingPosting($collectionInputData, "Collection", $paymentId);
            //console($collectionObj);
            if ($collectionObj['status'] == 'success') {
                $JournalId = $collectionObj['journalId'];
                $sqlcollection = "UPDATE `" . ERP_BRANCH_SALES_ORDER_PAYMENTS . "`
                                SET
                                    `journal_id`=$JournalId 
                                WHERE `payment_id`='$paymentId'  ";
                queryUpdate($sqlcollection);
            }
            //-----------------------------Collection ACC END ----------------


            // console("print total amt ******************");
            // console($total_amt);
            // console("print total amt ******************");
            if ($adv_amt > 0) {
                if ($enter_amt > 0) {
                    if ($total_amt < $adv_amt) {
                        $total_amt = $total_amt * -1;
                        $insItem = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` 
                                            SET
                                                `payment_id`='$paymentId',
                                                `company_id`='$company_id',
                                                `branch_id`='$branch_id',
                                                `location_id`='$location_id',
                                                `customer_id`='$customerId',
                                                `invoice_id`='0',
                                                `payment_type`='advanced',
                                                `payment_amt`='$total_amt',
                                                `remarks`='',
                                                `created_by`='$created_by',
                                                `updated_by`='$updated_by'
                                                ";
                        $dbCon->query($insItem);

                        $insItem2 = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` 
                                SET
                                    `payment_id`='$paymentId',
                                    `company_id`='$company_id',
                                    `branch_id`='$branch_id',
                                    `location_id`='$location_id',
                                    `customer_id`='$customerId',
                                    `invoice_id`='0',
                                    `payment_type`='advanced',
                                    `payment_amt`='$enter_amt',
                                    `remarks`='',
                                    `created_by`='$created_by',
                                    `updated_by`='$updated_by'
                ";
                        $dbCon->query($insItem2);
                    } elseif ($total_amt >= $adv_amt) {
                        $adv_amt = $adv_amt * -1;
                        $insItem = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` 
                                SET
                                    `payment_id`='$paymentId',
                                    `company_id`='$company_id',
                                    `branch_id`='$branch_id',
                                    `location_id`='$location_id',
                                    `customer_id`='$customerId',
                                    `invoice_id`='$invoiceId',
                                    `payment_type`='advanced',
                                    `payment_amt`='$adv_amt',
                                    `remarks`='',
                                    `created_by`='$created_by',
                                    `updated_by`='$updated_by'
                ";
                        $dbCon->query($insItem);

                        $remaining = $enter_amt - $total_amt;
                        $insItem2 = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` 
                SET
                    `payment_id`='$paymentId',
                    `company_id`='$company_id',
                    `branch_id`='$branch_id',
                    `location_id`='$location_id',
                    `customer_id`='$customerId',
                    `invoice_id`='$invoiceId',
                    `payment_type`='advanced',
                    `payment_amt`='$remaining',
                    `remarks`='',
                    `created_by`='$created_by',
                    `updated_by`='$updated_by'
                ";
                        $dbCon->query($insItem2);
                    }
                } else {
                    $total_amt = $total_amt * -1;
                    $insItem = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` 
                SET
                    `payment_id`='$paymentId',
                    `company_id`='$company_id',
                    `branch_id`='$branch_id',
                    `location_id`='$location_id',
                    `customer_id`='$customerId',
                    `invoice_id`='$invoiceId',
                    `payment_type`='advanced',
                    `payment_amt`='$total_amt',
                    `remarks`='',
                    `created_by`='$created_by',
                    `updated_by`='$updated_by'
                    ";
                    $dbCon->query($insItem);
                }
            } else {
                $total_amt = $total_amt * -1;
                $insItem = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` 
            SET
                `payment_id`='$paymentId',
                `company_id`='$company_id',
                `branch_id`='$branch_id',
                `location_id`='$location_id',
                `customer_id`='$customerId',
                `invoice_id`='0',
                `payment_type`='advanced',
                `payment_amt`='$total_amt',
                `remarks`='',
                `created_by`='$created_by',
                `updated_by`='$updated_by'
                ";
                $dbCon->query($insItem);

                $insItem2 = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` 
                            SET
                                `payment_id`='$paymentId',
                                `company_id`='$company_id',
                                `branch_id`='$branch_id',
                                `location_id`='$location_id',
                                `customer_id`='$customerId',
                                `invoice_id`='0',
                                `payment_type`='advanced',
                                `payment_amt`='$enter_amt',
                                `remarks`='',
                                `created_by`='$created_by',
                                `updated_by`='$updated_by'
            ";
                $dbCon->query($insItem2);
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong 11";
        }
        return $returnData;
    }
    // fetch totalAdvanceAmt
    function fetchCustomerPayments()
    {
        $returnData = [];
        global $dbCon;
        $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_PAYMENTS . "` WHERE `status`!='delected' ORDER BY payment_id DESC;";
        if ($res = $dbCon->query($sql)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }
        return $returnData;
    }

    // fetch  imranali59059 20230112
    function fetchCustomerPaymentLogDetails($paymentId)
    {
        $returnData = [];
        global $dbCon;
        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` WHERE payment_id='$paymentId' AND  status!='deleted'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }
        return $returnData;
    }

    // fetch totalAdvanceAmt
    function fetchAdvanceAmt($id)
    {
        $returnData = [];
        global $dbCon;

        $sql = "SELECT SUM(`payment_amt`) AS `totalAdvanceAmt` FROM `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` WHERE `customer_id`='$id' AND `payment_type`='advanced'";
        if ($res = $dbCon->query($sql)) {
            $row = $res->fetch_assoc();
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch totalAdvanceAmt
    function fetchOneAdvanceAmt($paymentId)
    {
        $returnData = [];
        global $dbCon;

        $sql = "SELECT SUM(`payment_amt`) AS `totalAdvanceAmt` FROM `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` WHERE `payment_id`='$paymentId' AND `payment_type`='advanced'";
        if ($res = $dbCon->query($sql)) {
            $row = $res->fetch_assoc();
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch totalAdvanceAmt
    function fetchAllPaymentLogByCustomerId($customerId)
    {
        $returnData = [];
        global $dbCon;

        $sql = "SELECT log.*,payment.documentDate,payment.transactionId FROM (SELECT payment_id, sum(payment_amt) as advancedAmt FROM `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` WHERE customer_id='$customerId' and payment_type = 'advanced' GROUP BY payment_id) as log INNER JOIN `" . ERP_BRANCH_SALES_ORDER_PAYMENTS . "` as payment ON log.payment_id = payment.payment_id";
        if ($res = $dbCon->query($sql)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // add invoice 
    function insertBranchInvoice($POST, $body)
    {
        global $dbCon;
        $returnData = [];

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        $invNo = $POST['invoiceDetails']['invNo'];
        $pgi_id = $POST['invoiceDetails']['pgiId'];
        $pgi_no = $POST['invoiceDetails']['pgiNo'];
        $creditPeriod = $POST['invoiceDetails']['creditPeriod'];
        $delivery_no = $POST['invoiceDetails']['delivery_no'];
        $so_number = $POST['invoiceDetails']['so_number'];
        $customer_id = $POST['invoiceDetails']['customer_id'];
        $invoice_date = $POST['invoiceDetails']['invoiceDate'];
        $poNumber = $POST['invoiceDetails']['poNumber'];
        $poDate = $POST['invoiceDetails']['poDate'];
        $kamId = $POST['invoiceDetails']['kamId'];
        $profit_center = $POST['invoiceDetails']['profit_center'];
        $subTotal = $POST['invoiceDetails']['subTotal'];
        $totalTaxAmt = $POST['invoiceDetails']['totalTaxAmt'];
        $cgst = $POST['invoiceDetails']['cgst'] ?? 0;
        $sgst = $POST['invoiceDetails']['sgst'] ?? 0;
        $igst = $POST['invoiceDetails']['igst'] ?? 0;
        $tcs = $POST['invoiceDetails']['tcs'] ?? 0;
        $totalDiscount = $POST['invoiceDetails']['totalDiscount'];
        $allTotalAmt = $POST['invoiceDetails']['allTotalAmt'];
        $totalItems = $POST['invoiceDetails']['totalItems'];
        $customer_billing_address = $POST['invoiceDetails']['customer_billing_address'];
        $customer_shipping_address = $POST['invoiceDetails']['customer_shipping_address'];

        $branchGstin = $POST['branchGstin'];

        $company_logo = $POST['companyDetails']['company_logo'];
        $address = $POST['companyDetails']['address'];
        $signature = $POST['companyDetails']['signature'];
        $footerNote = $POST['companyDetails']['footerNote'];

        $customerName = $POST['customerDetails']['name'];
        $customerGstin = $POST['customerDetails']['gstin'];
        $customerPhone = $POST['customerDetails']['phone'];
        $customerEmail = $POST['customerDetails']['email'];
        $customerAddress = $POST['customerDetails']['address'];

        // fetch customer details
        $customerDetailsObj = queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customer_id'")['data'];

        $customer_name = $customerDetailsObj['customer_name'];
        $customer_authorised_person_email = $customerDetailsObj['customer_authorised_person_email'];

        $customerDetailsSerialize = serialize($customerDetailsObj);
        $customerCode = $customerDetailsObj["data"]["customer_code"] ?? 0;
        $customerParentGlId = $customerDetailsObj["data"]["parentGlId"] ?? 0;

        // fetch company details
        $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
        $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
        $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
        $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
        $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
        $arrMarge = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);
        $companySerialize = serialize($arrMarge);

        $company_name = $companyDetailsObj['company_name'];

        $invInsert = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                        SET 
                            `invoice_no`='$invNo',
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `customer_id`='$customer_id',
                            `pgi_id`='" . $pgi_id . "',
                            `pgi_no`='" . $pgi_no . "',
                            `credit_period`='$creditPeriod',
                            `delivery_no`='$delivery_no',
                            `so_number`='$so_number',
                            `invoice_date`='$invoice_date',
                            `po_number`='$poNumber',
                            `po_date`='$poDate',
                            `totalItems`='$totalItems',
                            `sub_total_amt`='$subTotal',
                            `totalDiscount`='$totalDiscount',
                            `cgst`='$cgst',
                            `sgst`='$sgst',
                            `kamId`='$kamId',
                            `profit_center`='$profit_center',
                            `igst`='$igst',
                            `total_tax_amt`='$totalTaxAmt',
                            `all_total_amt`='$allTotalAmt',
                            `due_amount`='$allTotalAmt',
                            `customerDetails`='$customerDetailsSerialize',
                            `companyDetails`='$companySerialize',
                            `company_gstin`='$branchGstin',
                            `customer_gstin`='$customerGstin',
                            `customer_billing_address`='$customer_billing_address',
                            `customer_shipping_address`='$customer_shipping_address',
                            `created_by`='$created_by',
                            `updated_by`='$updated_by',
                            `type`='pgi',
                            `invoiceStatus`='1'
        ";
        // console($invInsert);
        if ($dbCon->query($invInsert)) {
            $returnData['lastID'] = $dbCon->insert_id;
            $listItem = $POST['listItem'];
            $invId = $returnData['lastID'];

            $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE so_invoice_id='$invId'";
            $getInvNumber =  queryGet($sql)['data']['invoice_no'];

            $encodeInv_id = base64_encode($invId);

            // update delivery pgi table
            $upd = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "` 
                        SET
                            invoiceStatus=1 WHERE so_delivery_pgi_id='" . $pgi_id . "' ";
            $dbCon->query($upd);
            $flug = 0;

            foreach ($listItem as $itemKey => $item) {
                $lineNo = $item['lineNo'];
                $inventory_item_id = $item['inventory_item_id'];
                $itemCode = $item['itemCode'];
                $itemName = $item['itemName'];
                $itemDesc = $item['itemDesc'];
                $hsnCode = $item['hsnCode'];
                $tax = $item['tax'];
                $totalTax = $item['totalTax'];
                $tolerance = $item['tolerance'] ?? 0;
                if (!empty(trim($tolerance))) {
                    $tolerance = $tolerance;
                } else {
                    $tolerance = 0;
                }
                $totalDiscount = $item['totalDiscount'];
                $totalDiscountAmt = $item['totalDiscountAmt'];
                $unitPrice = $item['unitPrice'];
                $qty = $item['qty'];
                $basePrice = ($qty * $unitPrice);
                $uom = $item['uom'];
                $totalPrice = $item['totalPrice'];
                $delivery_date = $item['delivery_date'];
                $enterQty = $item['enterQty'];
                $listItem[$itemKey]["parentGlId"] = $this->getInventoryItemParentGl($item["inventory_item_id"]);

                $invItem = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICE_ITEMS . "`
                            SET
                            `so_invoice_id`='$invId',
                            `inventory_item_id`='" . $inventory_item_id . "',
                            `lineNo`='" . $lineNo . "',
                            `itemCode`='" . $itemCode . "',
                            `itemName`='" . $itemName . "',
                            `itemDesc`='" . $itemDesc . "',
                            `delivery_date`='" . $delivery_date . "',
                            `qty`='" . $qty . "',
                            `uom`='" . $uom . "',
                            `enterQty`='" . $enterQty . "',
                            `tolerance`='" . $tolerance . "',
                            `unitPrice`='" . $unitPrice . "',
                            `basePrice`='" . $basePrice . "',
                            `hsnCode`='" . $hsnCode . "',
                            `tax`='" . $tax . "',
                            `totalTax`='" . $totalTax . "',
                            `totalDiscount`='" . $totalDiscount . "',
                            `totalDiscountAmt`='" . $totalDiscountAmt . "',
                            `createdBy`='" . $created_by . "',
                            `updatedBy`='" . $updated_by . "',
                            `totalPrice`='" . $totalPrice . "'
                ";
                // console($invItem);
                if ($dbCon->query($invItem)) {
                    $return['status'] = "success";
                    $return['message'] = "Invoice Created Successfully";
                    $return['invoiceNo'] = $getInvNumber;

                    $upd = "UPDATE `" . ERP_INVENTORY_STOCKS_SUMMARY . "` SET `fgMktOpen`= `fgMktOpen`-" . $qty . " WHERE itemId='" . $item['inventory_item_id'] . "'";
                    $updateItemStocksObj = queryUpdate($upd);
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "somthing went wrong! 3";
                    $flug++;
                }
            }
            if ($flug == 0) {

                // calculate days to date
                $duedate = date("Y-m-d", strtotime("+" . $creditPeriod . " days"));

                $to = $customer_authorised_person_email;
                $sub = 'Invoice ' . $invNo . ' for Your Recent Purchase';
                $msg = '<div>
                <div><strong>Dear ' . $customer_name . ',</strong></div>
                <p>
                    I hope this email finds you well. I am writing to inform you that an invoice for your recent purchase with <b>' . $company_name . '</b> has been generated and is now ready for payment.
                </p>
                <strong>
                    Invoice details:
                </strong>
                <div style="display:grid">
                    <span>
                        Invoice Number: ' . $invNo . '
                    </span>
                    <span>
                        Amount Due: ' . $allTotalAmt . '
                    </span>
                    <span>
                        Due Date: <strong>' . $duedate . '</strong>
                    </span>
                </div>
                <p>
                    To make a payment, please follow the instructions included in the attached invoice. If you have any questions or need assistance with the payment process, please do not hesitate to contact our finance team.
                </p>
                <p>
                    Thank you for your business and for choosing <b>' . $company_name . '</b>. We appreciate your continued support and look forward to serving you again in the future.
                </p>
                <div style="display:grid">
                    Best regards for, <span><b>' . $company_name . '</b></span>
                </div>
                <p>
                <a href="' . BASE_URL . 'branch/location/branch-so-invoice-mobile-view.php?inv_id=' . $encodeInv_id . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View Invoice</a>
                <a href="#" style="background: #17a664;padding: 8px;color: white;text-decoration: none;border-radius: 5px; margin-left:10px"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/money.png" /> Pay Now</a>
                </p>
                </div>';
                $mail =  SendMailByMySMTPmailTemplate($to, $sub, $msg);
                if ($mail == true) {
                    $sql = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
                                    SET
                                        `mailStatus`='1' WHERE `so_invoice_id`='$invId'
                        ";
                    if ($dbCon->query($sql)) {
                        $ins = "INSERT INTO `" . ERP_INVOICE_MAIL_LOG . "` 
                                        SET
                                            `company_id`='$company_id',
                                            `branch_id`='$branch_id',
                                            `location_id`='$location_id',
                                            `so_invoice_id`='$invId',
                                            `mailStatus`='1',
                                            `created_by`='$created_by',
                                            `updated_by`='$updated_by'";
                        $dbCon->query($ins);
                    }
                }

                $itemQtyMin = '-' . $qty;
                $insStockSummary2 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                        SET 
                            companyId = '" . $company_id . "',
                            branchId = '" . $branch_id . "',
                            locationId = '" . $location_id . "',
                            storageLocationId = 9,
                            itemId = '" . $item['inventory_item_id'] . "',
                            itemQty = '" . $itemQtyMin . "',
                            itemUom = '" . $uom . "',
                            itemPrice = '" . $unitPrice . "',
                            logRef = '" . $pgi_no . "',
                            createdBy = '" . $created_by . "',
                            updatedBy = '" . $updated_by . "'
                ";
                $dbCon->query($insStockSummary2);

                $flug2 = 0;
                //************************START ACCOUNTING ******************** */

                //-----------------------------PGI ACC Start ----------------
                $PGIInputData = [
                    "BasicDetails" => [
                        "documentNo" => $pgi_no, // Invoice Doc Number
                        "documentDate" => $invoice_date, // Invoice number
                        "postingDate" => date("Y-m-d"), // current date
                        "reference" => $invNo, // grn code
                        "remarks" => "PGI Creation - " . $invNo,
                        "journalEntryReference" => "Sales"
                    ],
                    "FGItems" => $listItem
                ];
                //console($ivPostingInputData);
                $ivPostingObj = $this->sopgiAccountingPosting($PGIInputData, "PGI", $pgi_id);
                //console($ivPostingObj);
                if ($ivPostingObj['status'] == 'success') {
                    $pgiJournalId = $ivPostingObj['journalId'];
                    $sqlpgi = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "`
                                    SET
                                        `journal_id`=$pgiJournalId 
                                    WHERE `so_delivery_pgi_id`='$pgi_id'  ";

                    queryUpdate($sqlpgi);
                    //-----------------------------PGI ACC END ----------------

                    //-----------------------------Invoicing ACC Start ----------------
                    $InvoicingInputData = [
                        "BasicDetails" => [
                            "documentNo" => $pgi_no, // Invoice Doc Number
                            "documentDate" => $invoice_date, // Invoice number
                            "postingDate" => date("Y-m-d"), // current date
                            "grnJournalId" => $pgiJournalId,
                            "reference" => $invNo, // grn code
                            "remarks" => "SO Invoicing - " . $invNo,
                            "journalEntryReference" => "Sales"
                        ],
                        "customerDetails" => [
                            "customerId" => $customer_id,
                            "customerName" => $customerName,
                            "customerCode" => $customerCode,
                            "customerGlId" => $customerParentGlId
                        ],
                        "FGItems" => $listItem,
                        "taxDetails" => [
                            "cgst" => $cgst,
                            "sgst" => $sgst,
                            "igst" => $igst,
                            "TCS" => $tcs
                        ]
                    ];
                    //console($ivPostingInputData);
                    $SOivPostingObj = $this->soIvAccountingPosting($InvoicingInputData, "SOInvoicing", $invId);
                    //console($SOivPostingObj);

                    if ($ivPostingObj['status'] == 'success') {
                        $ivJournalId = $SOivPostingObj['journalId'];
                        $sqliv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
                                    SET
                                        `journal_id`=$ivJournalId 
                                        WHERE `so_invoice_id`='$invId'";
                        queryUpdate($sqliv);
                    } else {
                        $flug2++;
                    }

                    //-----------------------------Invoicing ACC END ----------------

                } else {
                    $flug2++;
                }
                if ($flug2 == 0) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Invoice Created Successfully";
                    $returnData['invoiceNo'] = $getInvNumber;
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Invoice successfully sent, (Warning Account Entry failed!)";
                }
                //************************END ACCOUNTING ******************** */
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong!";
        }
        return $returnData;
    }

    // add invoice 
    function insertBranchDirectInvoice($POST)
    {
        global $dbCon;
        $returnData = [];

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        $invNo = "INV" . date("Ymd") . rand(1000, 9999);
        $customerId = $POST['customerId'];
        $billingAddress = $POST['billingAddress'];
        $shippingAddress = $POST['shippingAddress'];
        $creditPeriod = $POST['creditPeriod'];
        $invoice_date = $POST['invoiceDate'];
        $profitCenter = $POST['profitCenter'];
        $kamId = $POST['kamId'];

        $subTotal = $POST['grandSubTotalAmtInp'];
        $totalDiscount = $POST['grandTotalDiscountAmtInp'];
        $totalTaxAmt = $POST['grandTaxAmtInp'];
        $allTotalAmt = $POST['grandTotalAmtInp'];

        $totalItems = $POST['invoiceDetails']['totalItems'];
        $company_logo = $POST['companyDetails']['company_logo'];
        $gstin = $POST['companyDetails']['gstin'];
        $address = $POST['companyDetails']['address'];
        $signature = $POST['companyDetails']['signature'];
        $footerNote = $POST['companyDetails']['footerNote'];

        $customerName = $POST['customerDetails']['name'];
        $customerGstin = $POST['customerDetails']['gstin'];
        $customerPhone = $POST['customerDetails']['phone'];
        $customerEmail = $POST['customerDetails']['email'];
        $customerAddress = $POST['customerDetails']['address'];

        // fetch customer details
        $customerDetailsObj = queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customerId'")['data'];
        $customer_authorised_person_email = $customerDetailsObj['customer_authorised_person_email'];
        $customer_name = $customerDetailsObj['customer_name'];

        $customerDetailsSerialize = serialize($customerDetailsObj);
        $customerCode = $customerDetailsObj["data"]["customer_code"] ?? 0;
        $customerParentGlId = $customerDetailsObj["data"]["parentGlId"] ?? 0;

        // fetch company details
        $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
        $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
        $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
        $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
        $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
        $arrMarge = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);
        $companySerialize = serialize($arrMarge);

        $company_name = $companyDetailsObj['company_name'];

        $customer_Gst = $customerDetailsObj['customer_gstin'];
        $branch_Gst = $branchDetailsObj['branch_gstin'];

        $companyGstCode = substr($branch_Gst, 0, 2);
        $customerGstCode = substr($customer_Gst, 0, 2);
        $conditionGST = $companyGstCode == $customerGstCode;


        $gstAmt = 0;
        if ($conditionGST) {
            $gstAmt = $totalTaxAmt / 2;
            $invInsert = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                         SET 
                             `invoice_no`='$invNo',
                             `company_id`='$company_id',
                             `branch_id`='$branch_id',
                             `location_id`='$location_id',
                             `type`='direct',
                             `credit_period`='$creditPeriod',
                             `customer_id`='$customerId',
                             `kamId`='$kamId',
                             `invoice_date`='$invoice_date',
                             `sub_total_amt`='$subTotal',
                             `profit_center`='$profitCenter',
                             `totalDiscount`='$totalDiscount',
                             `total_tax_amt`='$totalTaxAmt',
                             `all_total_amt`='$allTotalAmt',
                             `due_amount`='$allTotalAmt',
                             `cgst`='$gstAmt',
                             `sgst`='$gstAmt',
                             `customerDetails`='$customerDetailsSerialize',
                             `customer_gstin`='" . $customer_Gst . "',
                             `company_gstin`='" . $branch_Gst . "',
                             `companyDetails`='$companySerialize',
                             `customer_billing_address`='$billingAddress',
                             `customer_shipping_address`='$shippingAddress',
                             `created_by`='$created_by',
                             `updated_by`='$updated_by',
                             `invoiceStatus`='1'
         ";
        } else {
            $invInsert = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
            SET 
                `invoice_no`='$invNo',
                `company_id`='$company_id',
                `branch_id`='$branch_id',
                `location_id`='$location_id',
                `type`='direct',
                `credit_period`='$creditPeriod',
                `customer_id`='$customerId',
                `kamId`='$kamId',
                `invoice_date`='$invoice_date',
                `sub_total_amt`='$subTotal',
                `profit_center`='$profitCenter',
                `totalDiscount`='$totalDiscount',
                `total_tax_amt`='$totalTaxAmt',
                `all_total_amt`='$allTotalAmt',
                `due_amount`='$allTotalAmt',
                `igst`='$totalTaxAmt',
                `customerDetails`='$customerDetailsSerialize',
                `customer_gstin`='" . $customer_Gst . "',
                `company_gstin`='" . $branch_Gst . "',
                `companyDetails`='$companySerialize',
                `customer_billing_address`='$billingAddress',
                `customer_shipping_address`='$shippingAddress',
                `created_by`='$created_by',
                `updated_by`='$updated_by',
                `invoiceStatus`='1'
";
        }
        // console($invInsert);
        if ($dbCon->query($invInsert)) {
            $returnData['lastID'] = $dbCon->insert_id;
            $inv_id = $dbCon->insert_id;
            $encodeInv_id = base64_encode($inv_id);

            $listItem = $POST['listItem'];
            $invId = $returnData['lastID'];
            $flug = 0;

            foreach ($listItem as $item) {
                $lineNo = $item['lineNo'];
                $itemCode = $item['itemCode'];
                $itemName = $item['itemName'];
                $itemDesc = $item['itemDesc'];
                $hsnCode = $item['hsnCode'];
                $tax = $item['tax'];
                $totalTax = $item['itemTotalTax1'];
                $tolerance = $item['tolerance'];
                $totalDiscount = $item['totalDiscount'];
                $totalDiscountAmt = $item['itemTotalDiscount1'];
                $unitPrice = $item['unitPrice'];
                $baseAmount = $item['baseAmount'];
                $qty = $item['qty'];
                $uom = $item['uom'];
                $totalPrice = $item['totalPrice'];
                $delivery_date = $item['delivery_date'];
                $enterQty = $item['enterQty'];

                $invItem = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICE_ITEMS . "`
                             SET
                             `so_invoice_id`='$invId',
                             `lineNo`='" . $lineNo . "',
                             `itemCode`='" . $itemCode . "',
                             `itemName`='" . $itemName . "',
                             `itemDesc`='" . $itemDesc . "',
                             `delivery_date`='" . $delivery_date . "',
                             `qty`='" . $qty . "',
                             `uom`='" . $uom . "',
                             `unitPrice`='" . $unitPrice . "',
                             `hsnCode`='" . $hsnCode . "',
                             `basePrice`='" . $baseAmount . "',
                             `tax`='" . $tax . "',
                             `totalTax`='" . $totalTax . "',
                             `totalDiscount`='" . $totalDiscount . "',
                             `totalDiscountAmt`='" . $totalDiscountAmt . "',
                             `createdBy`='" . $created_by . "',
                             `updatedBy`='" . $updated_by . "',
                             `totalPrice`='" . $totalPrice . "'
                 ";
                // console($invItem);
                if ($dbCon->query($invItem)) {
                    $return['status'] = "success";
                    $return['message'] = "Item Insert Success!";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "somthing went wrong! 3";
                    $flug++;
                }
            }
            if ($flug == 0) {
                // calculate days to date
                $duedate = date("Y-m-d", strtotime("+" . $creditPeriod . " days"));

                $to = $customer_authorised_person_email;
                $sub = 'Invoice ' . $invNo . ' for Your Recent Purchase';
                $msg = '<div>
                <div><strong>Dear ' . $customer_name . ',</strong></div>
                <p>
                    I hope this email finds you well. I am writing to inform you that an invoice for your recent purchase with <b>' . $company_name . '</b> has been generated and is now ready for payment.
                </p>
                <strong>
                    Invoice details:
                </strong>
                <div style="display:grid">
                    <span>
                        Invoice Number: ' . $invNo . '
                    </span>
                    <span>
                        Amount Due: ' . $allTotalAmt . '
                    </span>
                    <span>
                        Due Date: <strong>' . $duedate . '</strong>
                    </span>
                </div>
                <p>
                    To make a payment, please follow the instructions included in the attached invoice. If you have any questions or need assistance with the payment process, please do not hesitate to contact our finance team.
                </p>
                <p>
                    Thank you for your business and for choosing <b>' . $company_name . '</b>. We appreciate your continued support and look forward to serving you again in the future.
                </p>
                <div style="display:grid">
                    Best regards for, <span><b>' . $company_name . '</b></span>
                </div>
                
                <p>
                <a href="' . BASE_URL . 'branch/location/branch-so-invoice-mobile-view.php?inv_id=' . $encodeInv_id . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View Invoice</a>
                <a href="#" style="background: #17a664;padding: 8px;color: white;text-decoration: none;border-radius: 5px; margin-left:10px"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/money.png" /> Pay Now</a>
                </p>
                </div>';
                $mail =  SendMailByMySMTPmailTemplate($to, $sub, $msg);
                if ($mail == true) {
                    $sql = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
                                     SET
                                         `mailStatus`='1' WHERE `so_invoice_id`='$invId'
                         ";
                    if ($dbCon->query($sql)) {
                        $ins = "INSERT INTO `" . ERP_INVOICE_MAIL_LOG . "` 
                                         SET
                                             `company_id`='$company_id',
                                             `branch_id`='$branch_id',
                                             `location_id`='$location_id',
                                             `so_invoice_id`='$invId',
                                             `mailStatus`='1',
                                             `created_by`='$location_id',
                                             `updated_by`='$location_id' 
                             ";
                        $dbCon->query($ins);
                    }
                }
                $returnData['status'] = "success";
                $returnData['message'] = "You are successfully send!";
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "somthing went wrong! 3";
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "somthing went wrong! 2";
        }
        return $returnData;
    }

    // add invoice 
    function insertServiceInvoice($POST)
    {
        global $dbCon;
        $returnData = [];

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        $branchGst = queryGet("SELECT branch_gstin FROM `" . ERP_BRANCHES . "` WHERE branch_id='$branch_id'")['data']['branch_gstin'];

        $invNo = "INV" . date("Ymd") . rand(1000, 9999);
        $customerId = $POST['customerId'];
        $billingAddress = $POST['billingAddress'];
        $shippingAddress = $POST['shippingAddress'];
        $creditPeriod = $POST['creditPeriod'];
        $invoice_date = $POST['invoiceDate'];
        $profitCenter = $POST['profitCenter'];
        $kamId = $POST['kamId'];

        $subTotal = $POST['grandSubTotalAmtInp'];
        $totalDiscount = $POST['grandTotalDiscountAmtInp'];
        $totalTaxAmt = $POST['grandTaxAmtInp'];
        $allTotalAmt = $POST['grandTotalAmtInp'];

        $totalItems = $POST['invoiceDetails']['totalItems'];
        $company_logo = $POST['companyDetails']['company_logo'];
        $gstin = $POST['companyDetails']['gstin'];
        $address = $POST['companyDetails']['address'];
        $signature = $POST['companyDetails']['signature'];
        $footerNote = $POST['companyDetails']['footerNote'];

        $customerName = $POST['customerDetails']['name'];
        $customerGstin = $POST['customerGstin'];
        $customerPhone = $POST['customerDetails']['phone'];
        $customerEmail = $POST['customerDetails']['email'];
        $customerAddress = $POST['customerDetails']['address'];

        // fetch customer details
        $customerDetailsObj = queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customerId'")['data'];

        $customer_name = $customerDetailsObj['customer_name'];
        $customer_authorised_person_email = $customerDetailsObj['customer_authorised_person_email'];

        $customerDetailsSerialize = serialize($customerDetailsObj);
        $customerCode = $customerDetailsObj["data"]["customer_code"] ?? 0;
        $customerParentGlId = $customerDetailsObj["data"]["parentGlId"] ?? 0;

        // fetch company details
        $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
        $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
        $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
        $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
        $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
        $arrMarge = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);
        $companySerialize = serialize($arrMarge);

        $company_name = $companyDetailsObj['company_name'];
        $customer_Gst = $customerDetailsObj['customer_gstin'];
        $branch_Gst = $branchDetailsObj['branch_gstin'];

        $companyGstCode = substr($branch_Gst, 0, 2);
        $customerGstCode = substr($customer_Gst, 0, 2);
        $conditionGST = $companyGstCode == $customerGstCode;

        $gstAmt = 0;
        if ($conditionGST) {
            $gstAmt = $totalTaxAmt / 2;
            $invInsert = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                         SET 
                             `invoice_no`='$invNo',
                             `company_id`='$company_id',
                             `branch_id`='$branch_id',
                             `location_id`='$location_id',
                             `type`='service',
                             `credit_period`='$creditPeriod',
                             `customer_id`='$customerId',
                             `invoice_date`='$invoice_date',
                             `sub_total_amt`='$subTotal',
                             `totalDiscount`='$totalDiscount',
                             `total_tax_amt`='$totalTaxAmt',
                             `cgst`='$gstAmt',
                             `sgst`='$gstAmt',
                             `all_total_amt`='$allTotalAmt',
                             `due_amount`='$allTotalAmt',
                             `customerDetails`='$customerDetailsSerialize',
                             `customer_gstin`='" . $customer_Gst . "',
                             `company_gstin`='" . $branch_Gst . "',
                             `companyDetails`='$companySerialize',
                             `customer_billing_address`='$billingAddress',
                             `customer_shipping_address`='$shippingAddress',
                             `created_by`='$created_by',
                             `updated_by`='$updated_by',
                             `invoiceStatus`='1'
         ";
        } else {
            $invInsert = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                         SET 
                             `invoice_no`='$invNo',
                             `company_id`='$company_id',
                             `branch_id`='$branch_id',
                             `location_id`='$location_id',
                             `type`='service',
                             `credit_period`='$creditPeriod',
                             `customer_id`='$customerId',
                             `invoice_date`='$invoice_date',
                             `sub_total_amt`='$subTotal',
                             `totalDiscount`='$totalDiscount',
                             `total_tax_amt`='$totalTaxAmt',
                             `igst`='$totalTaxAmt',
                             `all_total_amt`='$allTotalAmt',
                             `due_amount`='$allTotalAmt',
                             `customerDetails`='$customerDetailsSerialize',
                             `customer_gstin`='" . $customer_Gst . "',
                             `company_gstin`='" . $branch_Gst . "',
                             `companyDetails`='$companySerialize',
                             `customer_billing_address`='$billingAddress',
                             `customer_shipping_address`='$shippingAddress',
                             `created_by`='$created_by',
                             `updated_by`='$updated_by',
                             `invoiceStatus`='1'
         ";
        }
        // console($invInsert);
        if ($dbCon->query($invInsert)) {
            $returnData['lastID'] = $dbCon->insert_id;
            $inv_id = $dbCon->insert_id;
            $encodeInv_id = base64_encode($inv_id);
            $listItem = $POST['listItem'];
            $invId = $returnData['lastID'];
            $flug = 0;

            foreach ($listItem as $item) {
                // $serviceId = $item['serviceId'];
                // $lineNo = $item['lineNo'];
                // $serviceCode = $item['serviceCode'];
                // $serviceName = $item['serviceName'];
                // $serviceDesc = $item['serviceDesc'];
                // $hsnCode = $item['hsnCode'];
                // $tax = $item['tax'];
                // $totalTax = $item['itemTotalTax1'];
                // $totalDiscount = $item['totalDiscount'];
                // $totalDiscountAmt = $item['itemTotalDiscount1'];
                // $unitPrice = $item['unitPrice'];
                // $qty = $item['qty'];
                // $baseAmount = $item['baseAmount'];
                // $uom = $item['uom'];
                // $totalPrice = $item['totalPrice'];
                // $enterQty = $item['enterQty'];

                $lineNo = $item['lineNo'];
                $itemCode = $item['itemCode'];
                $itemName = $item['itemName'];
                $itemDesc = $item['itemDesc'];
                $hsnCode = $item['hsnCode'];
                $tax = $item['tax'];
                $totalTax = $item['itemTotalTax1'];
                $tolerance = $item['tolerance'];
                $totalDiscount = $item['totalDiscount'];
                $totalDiscountAmt = $item['itemTotalDiscount1'];
                $unitPrice = $item['unitPrice'];
                $baseAmount = $item['baseAmount'];
                $qty = $item['qty'];
                $uom = $item['uom'];
                $totalPrice = $item['totalPrice'];
                $delivery_date = $item['delivery_date'];
                $enterQty = $item['enterQty'];

                // `so_invoice_id`='$invId',
                //              `inventory_item_id`='$serviceId',
                //              `lineNo`='" . $lineNo . "',
                //              `itemCode`='" . $serviceCode . "',
                //              `itemName`='" . $serviceName . "',
                //              `itemDesc`='" . $serviceDesc . "',
                //              `qty`='" . $qty . "',
                //              `unitPrice`='" . $unitPrice . "',
                //              `basePrice`='" . $baseAmount . "',
                //              `hsnCode`='" . $hsnCode . "',
                //              `tax`='" . $tax . "',
                //              `totalTax`='" . $totalTax . "',
                //              `totalDiscount`='" . $totalDiscount . "',
                //              `totalDiscountAmt`='" . $totalDiscountAmt . "',
                //              `createdBy`='" . $created_by . "',
                //              `updatedBy`='" . $updated_by . "',
                //              `totalPrice`='" . $totalPrice . "'
                $invItem = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICE_ITEMS . "`
                             SET
                             `so_invoice_id`='$invId',
                             `lineNo`='" . $lineNo . "',
                             `itemCode`='" . $itemCode . "',
                             `itemName`='" . $itemName . "',
                             `itemDesc`='" . $itemDesc . "',
                             `delivery_date`='" . $delivery_date . "',
                             `qty`='" . $qty . "',
                             `uom`='" . $uom . "',
                             `unitPrice`='" . $unitPrice . "',
                             `hsnCode`='" . $hsnCode . "',
                             `basePrice`='" . $baseAmount . "',
                             `tax`='" . $tax . "',
                             `totalTax`='" . $totalTax . "',
                             `totalDiscount`='" . $totalDiscount . "',
                             `totalDiscountAmt`='" . $totalDiscountAmt . "',
                             `createdBy`='" . $created_by . "',
                             `updatedBy`='" . $updated_by . "',
                             `totalPrice`='" . $totalPrice . "'
                 ";
                // console($invItem);
                if ($dbCon->query($invItem)) {
                    $return['status'] = "success";
                    $return['message'] = "Item Insert Success!";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "somthing went wrong! 3";
                    $flug++;
                }
            }
            if ($flug == 0) {

                // calculate days to date
                $duedate = date("Y-m-d", strtotime("+" . $creditPeriod . " days"));

                $to = $customer_authorised_person_email;
                $sub = 'Invoice ' . $invNo . ' for Your Recent Purchase'; ?>
                <?php
                $msg = '<div>
                <div><strong>Dear ' . $customer_name . ',</strong></div>
                <p>
                    I hope this email finds you well. I am writing to inform you that an invoice for your recent purchase with <b>' . $company_name . '</b> has been generated and is now ready for payment.
                </p>
                <strong>
                    Invoice details:
                </strong>
                <div style="display:grid">
                    <span>
                        Invoice Number: ' . $invNo . '
                    </span>
                    <span>
                        Amount Due: ' . $allTotalAmt . '
                    </span>
                    <span>
                        Due Date: <strong>' . $duedate . '</strong>
                    </span>
                </div>
                <p>
                    To make a payment, please follow the instructions included in the attached invoice. If you have any questions or need assistance with the payment process, please do not hesitate to contact our finance team.
                </p>
                <p>
                    Thank you for your business and for choosing <b>' . $company_name . '</b>. We appreciate your continued support and look forward to serving you again in the future.
                </p>
                <div style="display:grid">
                    Best regards for, <span><b>' . $company_name . '</b></span>
                </div>
                
                <p>
                <a href="' . BASE_URL . 'branch/location/branch-so-invoice-mobile-view.php?inv_id=' . $encodeInv_id . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View Invoice</a>
                <a href="#" style="background: #17a664;padding: 8px;color: white;text-decoration: none;border-radius: 5px; margin-left:10px"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/money.png" /> Pay Now</a>
                </p>
                </div>';

                $mail =  SendMailByMySMTPmailTemplate($to, $sub, $msg);
                if ($mail == true) {
                    $sql = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
                                     SET
                                         `mailStatus`='1' WHERE `so_invoice_id`='$invId'
                         ";
                    if ($dbCon->query($sql)) {
                        $ins = "INSERT INTO `" . ERP_INVOICE_MAIL_LOG . "` 
                                         SET
                                             `company_id`='$company_id',
                                             `branch_id`='$branch_id',
                                             `location_id`='$location_id',
                                             `so_invoice_id`='$invId',
                                             `mailStatus`='1',
                                             `created_by`='$location_id',
                                             `updated_by`='$location_id' 
                             ";
                        $dbCon->query($ins);
                    }
                }
                $returnData['status'] = "success";
                $returnData['message'] = "You are successfully save & send.";
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "somthing went wrong! 01";
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "somthing went wrong! 03";
        }
        return $returnData;
    }

    // fetch branch so delivery pgi listing 
    function fetchBranchSoDeliveryPgiListing()
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "` WHERE status='active'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }
        return $returnData;
    }

    // fetch branch so delivery pgi listing 
    function fetchCurrencyIcon($id)
    {
        $currency = queryGet("SELECT currency_icon FROM `" . ERP_CURRENCY_TYPE . "` WHERE currency_id='$id'");
        return $currency;
    }

    // fetch branch so delivery pgi details by id 
    function fetchBranchSoDeliveryPgiById($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "` WHERE so_delivery_pgi_id='$id' AND status='active'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch branch so delivery pgi items listing 
    function fetchBranchSoDeliveryItemsPgi($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY_ITEMS_PGI . "` WHERE `so_delivery_pgi_id`='$id' AND status='active'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch branch so delivery pgi items listing 
    function fetchHsnDetails($hsnCode)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_HSN_CODE . "` WHERE `hsnCode`='$hsnCode'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch branch so delivery pgi listing 
    function fetchBranchSoInvoice()
    {
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        $returnData = [];

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' AND status='active' ORDER BY so_invoice_id DESC";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch branch so delivery pgi details by id 
    function fetchBranchSoInvoiceById($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE so_invoice_id='$id' AND status != 'deleted'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch branch so delivery pgi details by id 
    function fetchBranchSoInvoiceBycustomerId($customerId)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE customer_id='$customerId' AND invoiceStatus != 'cancelled'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch branch so delivery pgi items listing 
    function fetchBranchSoInvoiceItems($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICE_ITEMS . "` WHERE `so_invoice_id`='$id' AND status='active'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch branch so delivery pgi items listing 
    function fetchBranchSoItemPriceDetails($itemCode)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_INVENTORY_ITEM_PRICE . "` WHERE `ItemCode`='$itemCode'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['success'] = "true";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch KAM data  
    function fetchKamDetails()
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;

        $ins = "SELECT * FROM `" . ERP_KAM . "` WHERE company_id='" . $company_id . "'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['status'] = "success";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    // fetch stock summary details by itemCode  
    function fetchStocksSummaryDetails($itemId)
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;

        // $ins = "SELECT * FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` WHERE company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "'";
        $ins = "SELECT * FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` WHERE itemId='$itemId'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['status'] = "success";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    function fetchCompanyBankDetails()
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;

        $ins = "SELECT * FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['status'] = "success";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }
}
