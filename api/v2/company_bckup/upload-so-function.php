<?php
    include("../../../app/v1/functions/common/func-common.php");
    require_once("api-common-func.php");


    class SOUpload
    {
    function uploadSO($POST_DATA = [],$company_id,$branch_id,$location_id,$user_id,$declaration = 0)
    {
        $created_by = $user_id."|location";
        $updated_by = $user_id."|location";
        $returnData = [];
        $i = 0;
        $flag = [];
        $error_flag = 0;

        foreach($POST_DATA as $POST)
        {

            //Item
            $itemName = $POST["itemName"];
            $itemCode = $POST["itemCode"];

            $itemQuery = queryGet("SELECT * FROM `erp_inventory_items` WHERE `company_id` = '" . $company_id . "' AND `branch`='".$branch_id."' AND `location_id`='".$location_id."' AND `itemCode`='".$itemCode."' AND `itemName`='".$itemName."'",false);

            if($itemQuery["numRows"] == 0)
            {
                $returnData['status'] = "warning";
                $returnData['message'] = "Item not found";
                $flag[] = array("status"=>"warning","message"=>"Item not found at line ".$i);
                $error_flag++;
                $i++;
                continue;
            }
            else
            {
            //Customer
            $customerName = $POST["customerName"];
            $customerCode = $POST["customerCode"];

            $customerQuery = queryGet("SELECT * FROM `erp_customer` WHERE `company_id` = '" . $company_id . "' AND `company_branch_id`='".$branch_id."' AND `location_id`='".$location_id."' AND `trade_name`='".$customerName."' AND `customer_code`='".$customerCode."'",false);

            if($customerQuery["numRows"] == 0)
            {
                $returnData['status'] = "warning";
                $returnData['message'] = "Customer not exists";
                $flag[] = array("status"=>"warning","message"=>"Customer not exists at line ".$i);
                $error_flag++;
                $i++;
                continue;
            }
            else
            {
                //KAM
                $kamCode = $POST["kamCode"];

                $kamQuery = queryGet("SELECT * FROM `erp_kam` WHERE `company_id` = '" . $company_id . "' AND `kamCode`='".$kamCode."'",false);

                if($kamQuery["numRows"] == 0)
                {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "KAM not exists";
                    $flag[] = array("status"=>"warning","message"=>"KAM not exists at line ".$i);
                    $error_flag++;
                    $i++;
                    continue;
                }
                else
                {
                $customerId = $customerQuery["data"]['customer_id'];
                $soDate = $POST['soDate'];
                $postingTime = $POST['postingTime'];
                $deliveryDate = $POST['deliveryDate'];
                $shippingAddress = $POST['shippingAddress'];
                $billingAddress = $POST['billingAddress'];
                $profitCenter = $POST['profitCenter'];
                $creditPeriod = $POST['creditPeriod'];
                $shipToLastInsertedId = 0;
                $kamId = $kamQuery["data"]['kamId'];
                $placeOfSupply = $POST['placeOfSupply'];
                $compInvoiceType = $POST['compInvoiceType'];
        
                $repeatEvery = $POST['repeatEvery'];
                $startOn = $POST['startOn'];
                $endOn = $POST['endOn'];
                // $fobCheckbox = $POST['fobCheckbox'];
        
                $goodsType = $POST['goodsType'];
                $customerPO = $POST['customerPO'];
                $approvalStatus = $POST['approvalStatus'] ?? 0;
                $serviceDescription = $POST['otherCostDetails'] ?? '';
                $quotationId = base64_decode($POST['quotationId']) ?? 0;
        
                $totalItems = 1;
                $uomType = "material";


            if (isset($POST["uom"]) && $POST["uom"] != '') {

                $uom = strtolower($POST["uom"]);

                $check_myArray = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `companyId` =  ".$company_id." AND LOWER(`uomName`) = '" . $uom . "'");

                if($check_myArray["numRows"] == 0)
                {
                    //Insert
                    $insuom = "INSERT INTO `erp_inventory_mstr_uom` 
                                    SET
                                        `companyId`='" . $company_id . "',
                                        `uomName`='".$POST['uom']."',
                                        `uomDesc`='".$POST['uom']."',
                                        `uomType`='".$uomType."',
                                        `uomCreatedBy`='".$created_by."',
                                        `uomUpdatedBy`='".$created_by."'";
                    //exit();

                    $insertItemUom = queryInsert($insuom);

                    $uomId = $insertItemUom['insertedId'];
                }
                else
                {
                    //get ID
                    $uomId = $check_myArray["data"]["uomId"];
                }


            } else {
                $uomId = 0;
            }

            $POST["funcArea"] = $profitCenter;

            if (isset($POST["funcArea"]) && $POST["funcArea"] != '') {

                $funcArea = strtolower($POST["funcArea"]);

                $check_myArray = queryGet("SELECT * FROM `erp_company_functionalities` WHERE `company_id` =  ".$company_id." AND LOWER(`functionalities_name`) LIKE '%" . $funcArea . "%'");

                if($check_myArray["numRows"] == 0)
                {
                    //Insert
                    $insfuncarea = "INSERT INTO `erp_company_functionalities` 
                                    SET
                                        `company_id`='" . $company_id . "',
                                        `functionalities_name`='".$POST['funcArea']."',
                                        `functionalities_desc`='".$POST['funcArea']."',
                                        `functionalities_created_by`='".$created_by."',
                                        `functionalities_updated_by`='".$created_by."'";
                    //exit();

                    $insertItemGroup = queryInsert($insfuncarea);

                    $functionalAreas = $insertItemGroup['insertedId'];
                }
                else
                {
                    //get ID
                    $functionalAreas = $check_myArray["data"]["functionalities_id"];
                }



            } else {
                $funcArea = 0;
            }

            if (isset($POST['currency']) && $POST["currency"] != '') {
                $currency = strtolower($POST["currency"]);
                $check_currency = queryGet("SELECT * FROM `erp_currency_type` WHERE  LOWER(`currency_name`) LIKE '%" . $currency . "%'",false);

                $currencyId = $check_currency["data"]["currency_id"];
                $currencyName = $check_currency["data"]["currency_name"];

            } else {
                $currency = 0;
            }
    
    
            if (isset($POST['curr_rate']) && $POST["curr_rate"] != '') {
                $conversion = $POST["curr_rate"] ?? 0;
            } else {
                $conversion = 0;
            }

            $lastQuery = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id='$company_id' AND branch_id='$branch_id' AND location_id='$location_id' ORDER BY so_id DESC LIMIT 1";
            $last = queryGet($lastQuery);
            $lastRow = $last['data'] ?? "";
            $lastSONo = $lastRow['so_number'] ?? "";
            $returnSoNo = getPoSerialNumber($lastSONo);

            $so_number = $POST["so_number"];

            $so_check_query = queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE `company_id`='".$company_id."' AND `branch_id`='".$branch_id."' AND `location_id`='".$location_id."' AND `so_number`='".$so_number."'",false);

            if($so_check_query["numRows"] == 0)
            {
                //Insert to SO table and SO item table
                 $insSO = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER . "`
                 SET
                   `so_number`='$returnSoNo',
                   `ref_no`='$so_number',
                   `customer_id`='$customerId',
                   `company_id`='$company_id',
                   `branch_id`='$branch_id',
                   `location_id`='$location_id',
                   `so_date`='$soDate',
                   `soPostingTime`='$postingTime',
                   `delivery_date`='$deliveryDate',
                   `billingAddress`='$billingAddress',
                   `shippingAddress`='$shippingAddress',
                   `profit_center`='$functionalAreas',
                   `credit_period`='$creditPeriod',
                   `kamId`='$kamId',
                   `shipToLastInsertedId`='$shipToLastInsertedId',
                   `conversion_rate`='$conversion',
                   `currency_id`='$currencyId',
                   `currency_name`='$currencyName',
                   `goodsType`='$goodsType',
                   `approvalStatus`='$approvalStatus',
                   `customer_po_no`='$customerPO',
                   `created_by`='$created_by',
                   `updated_by`='$updated_by',
                   `soStatus`='open' ";
        $sqlSOList = queryInsert($insSO);


            if ($sqlSOList['status'] == "success") 
            {
                $last_po_id =  $sqlSOList['insertedId'];
                $lastId = $sqlSOList['insertedId'];

                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_SALES_ORDER;
            $auditTrail['basicDetail']['column_name'] = 'so_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $lastId;  // primary key
            $auditTrail['basicDetail']['document_number'] = $returnSoNo;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Seles Order Creation';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($sqlSOList);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';


            $auditTrail['action_data']['Sales Order Detail']['so_number'] = $returnSoNo;
            $auditTrail['action_data']['Sales Order Detail']['customer_id'] = $customerId; //
            $auditTrail['action_data']['Sales Order Detail']['so_date'] = $soDate;
            $auditTrail['action_data']['Sales Order Detail']['soPostingTime'] = $postingTime;
            $auditTrail['action_data']['Sales Order Detail']['delivery_date'] = $deliveryDate;
            $auditTrail['action_data']['Sales Order Detail']['billingAddress'] = $billingAddress;
            $auditTrail['action_data']['Sales Order Detail']['shippingAddress'] = $shippingAddress;
            $auditTrail['action_data']['Sales Order Detail']['profit_center'] = $functionalAreas; //
            $auditTrail['action_data']['Sales Order Detail']['credit_period'] = $creditPeriod;
            $auditTrail['action_data']['Sales Order Detail']['kamId'] = $kamId; //
            $auditTrail['action_data']['Sales Order Detail']['conversion_rate'] = $conversion;
            $auditTrail['action_data']['Sales Order Detail']['currency_id'] = $currencyId; //
            $auditTrail['action_data']['Sales Order Detail']['currency_name'] = $currencyName;
            $auditTrail['action_data']['Sales Order Detail']['goodsType'] = $goodsType;
            $auditTrail['action_data']['Sales Order Detail']['approvalStatus'] = $approvalStatus;
            $auditTrail['action_data']['Sales Order Detail']['customer_po_no'] = $customerPO;
            $auditTrail['action_data']['Sales Order Detail']['soStatus'] = 'open';
            $auditTrail['action_data']['Sales Order Detail']['created_by'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['Sales Order Detail']['updated_by'] = getCreatedByUser($updated_by);

             // insert to subscription table
            $subscribSql = "INSERT INTO `" . ERP_BRANCH_SUBSCRIPTION_INVOICE . "` 
                    SET 
                        `so_id`='$lastId',
                        `repeat_every`='$repeatEvery',
                        `start_on`='$startOn',
                        `next_trigger_date`='$startOn',
                        `end_on`='$endOn'
            ";
            $subscribInsert = queryInsert($subscribSql);
            // insert items
            $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` 
                        WHERE 
                            company_id='$company_id' 
                            AND branch_id='$branch_id' 
                            AND location_id='$location_id' 
                            AND so_id='$lastId'
            ";
            $getSoNumber =  queryGet($sql)['data']['so_number'];



            $itemId = $itemQuery["data"]["itemId"];
            $unitPrice = $POST["unitPrice"];
            $quantity = $POST["qty"];
            $itemDescription = $itemQuery["data"]["itemDesc"];
            $remaining_quantity = $POST["remainingQty"];
            $hsnCode = $itemQuery["data"]["hsnCode"];
            $goodsType = $itemQuery["data"]["goodsType"];
            $totalPrice =  $unitPrice * $quantity;
            $tolerance = $POST["tolerance"];   
            $totalDiscount = $POST["totalDiscount"];
            $itemTotalDiscount1 = (($totalDiscount / $totalPrice) * 100);
            $tax = $POST["tax"];
            $itemTotalTax1 = (($tax / $totalPrice) * 100);

            $totalAmount = $totalPrice + $itemTotalTax1 - $itemTotalDiscount1;

            $countI = 1;

                $insItems = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_ITEMS . "`
                            SET
                            `so_id`='$lastId',
                            `lineNo`='$countI',
                            `inventory_item_id`='" . $itemId . "',
                            `goodsType`='" . $goodsType . "',
                            `itemCode`='" . $itemCode . "',
                            `itemName`='" . $itemName . "',
                            `itemDesc`='" . $itemDescription . "',
                            `hsnCode`='" . $hsnCode . "',
                            `unitPrice`='" . $unitPrice . "',
                            `totalDiscount`='" . $totalDiscount . "',
                            `itemTotalDiscount`='" . $itemTotalDiscount1 . "',
                            `tax`='" . $tax . "',
                            `totalTax`='" . $itemTotalTax1 . "',
                            `totalPrice`='" . $totalPrice . "',
                            `tolerance`='" . $tolerance . "',
                            `qty`='" . $quantity . "',
                            `uom`='" . $uomId . "'
                ";
                $sqlItemList = queryInsert($insItems);

                if ($sqlItemList['status'] == 'success') {


                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['lineNo'] = $countI;
                    // $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['goodsType']=0;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['itemCode'] = $itemCode;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['itemName'] = $itemName;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['itemDesc'] = $itemDescription;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['hsnCode'] = $hsnCode;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['unitPrice'] = $unitPrice;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['totalDiscount'] = $totalDiscount;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['itemTotalDiscount'] = $itemTotalDiscount1;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['tax'] = $tax;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['totalTax'] = $itemTotalTax1;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['totalPrice'] = $totalPrice;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['tolerance'] = $tolerance;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['qty'] = $quantity;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['uom'] = $uomId;

                    // update sales order
                        $updateDeli = "UPDATE `" . ERP_BRANCH_SALES_ORDER . "` 
                        SET 
                            `totalItems`='" . $totalItems . "',
                            `totalDiscount`='" . $totalDiscount . "',
                            `totalAmount`='" . $totalAmount . "' WHERE so_id=" . $lastId . "
                    ";
                    queryUpdate($updateDeli);

                    // update quotations
                    // $updateQuoat = "UPDATE `" . ERP_BRANCH_QUOTATIONS . "` 
                    //     SET 
                    //         `approvalStatus`=10 WHERE quotation_id=" . $quotationId . "";
                    // queryUpdate($updateQuoat);

                    // select from ERP_CUSTOMER_INVOICE_LOGS
            $selectInvLog = "SELECT * FROM `" . ERP_CUSTOMER_INVOICE_LOGS . "` WHERE customer_id=$customerId";
            $selectInvLogData = queryGet($selectInvLog);
            if ($selectInvLogData['numRows'] > 0) {
                // update customer log
                $updateInvLog = "UPDATE `" . ERP_CUSTOMER_INVOICE_LOGS . "`
                                SET
                                    `company_id`=$company_id,
                                    `branch_id`=$branch_id,
                                    `location_id`=$location_id,
                                    `customer_id`=$customerId,
                                    `ref_no`='$getSoNumber',
                                    `profit_center`='$functionalAreas',
                                    `credit_period`='$creditPeriod',
                                    `kamId`='$kamId',
                                    `placeOfSupply`='$placeOfSupply',
                                    `complianceInvoiceType`='$compInvoiceType',
                                    `conversion_rate`='$conversion',
                                    `currency_id`='$currencyId',
                                    `currency_name`='$currencyName',
                                    `billingAddress`='$billingAddress',
                                    `shippingAddress`='$shippingAddress',
                                    `created_by`='$created_by',
                                    `updated_by`='$updated_by' WHERE customer_id=$customerId";
                $updateInvoiceLog = queryInsert($updateInvLog);
            } else {
                // insert customer logs
                $insInvLog = "INSERT INTO `" . ERP_CUSTOMER_INVOICE_LOGS . "`
                            SET
                                `company_id`=$company_id,
                                `branch_id`=$branch_id,
                                `location_id`=$location_id,
                                `customer_id`=$customerId,
                                `ref_no`='$getSoNumber',
                                `profit_center`='$functionalAreas',
                                `credit_period`='$creditPeriod',
                                `kamId`='$kamId',
                                `placeOfSupply`='$placeOfSupply',
                                `complianceInvoiceType`='$compInvoiceType',
                                `conversion_rate`='$conversion',
                                `currency_id`='$currencyId',
                                `currency_name`='$currencyName',
                                `billingAddress`='$billingAddress',
                                `shippingAddress`='$shippingAddress',
                                `created_by`='$created_by',
                                `updated_by`='$updated_by'";
                $invoiceLog = queryInsert($insInvLog);

                $auditTrailreturn = generateAuditTrail($auditTrail);

                    // $lastItemId = $insItemConn['insertedId'];

                    $returnData['status'] = "success";
                    $returnData['message'] = "Data submitted successfully";
                    $flag[] = array("status"=>"warning","message"=>"Data submitted successfully at line ".$i);
            }



                }
                else
                {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Data saved failed try again";
                    $flag[] = array("status"=>"warning","message"=>"Data saved failed try again at line ".$i);
                    $error_flag++;
                    $i++;
                    continue;
                }

            }
            else
            {
                $returnData['status'] = "warning";
                $returnData['message'] = "Data saved failed try again";
                $flag[] = array("status"=>"warning","message"=>"Data saved failed try again at line ".$i);
                $error_flag++;
                $i++;
                continue;
            }

            }
            else
            {
                //Insert to SO item Table
                $last_so_id =  $so_check_query["data"]["so_id"];
                $lastId = $so_check_query["data"]["so_id"];
                $insert = "";

            // insert items
            $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` 
                        WHERE 
                            company_id='$company_id' 
                            AND branch_id='$branch_id' 
                            AND location_id='$location_id' 
                            AND so_id='$lastId'
            ";
            $getSoNumber =  queryGet($sql)['data']['so_number'];



            $itemId = $itemQuery["data"]["itemId"];
            $unitPrice = $POST["unitPrice"];
            $quantity = $POST["qty"];
            $itemDescription = $itemQuery["data"]["itemDesc"];
            $remaining_quantity = $POST["remainingQty"];
            $hsnCode = $itemQuery["data"]["hsnCode"];
            $goodsType = $itemQuery["data"]["goodsType"];
            $totalPrice =  $unitPrice * $quantity;
            $tolerance = $POST["tolerance"];   
            $totalDiscount = $POST["totalDiscount"];
            $itemTotalDiscount1 = (($totalDiscount / $totalPrice) * 100);
            $tax = $POST["tax"];
            $itemTotalTax1 = (($tax / $totalPrice) * 100);

            $totalAmount = $totalPrice + $itemTotalTax1 - $itemTotalDiscount1;

            $countI = 1;

                $insItems = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_ITEMS . "`
                            SET
                            `so_id`='$lastId',
                            `lineNo`='$countI',
                            `inventory_item_id`='" . $itemId . "',
                            `goodsType`='" . $goodsType . "',
                            `itemCode`='" . $itemCode . "',
                            `itemName`='" . $itemName . "',
                            `itemDesc`='" . $itemDescription . "',
                            `hsnCode`='" . $hsnCode . "',
                            `unitPrice`='" . $unitPrice . "',
                            `totalDiscount`='" . $totalDiscount . "',
                            `itemTotalDiscount`='" . $itemTotalDiscount1 . "',
                            `tax`='" . $tax . "',
                            `totalTax`='" . $itemTotalTax1 . "',
                            `totalPrice`='" . $totalPrice . "',
                            `tolerance`='" . $tolerance . "',
                            `qty`='" . $quantity . "',
                            `uom`='" . $uomId . "'
                ";
                $sqlItemList = queryInsert($insItems);

                if ($sqlItemList['status'] == 'success') {


                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['lineNo'] = $countI;
                    // $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['goodsType']=0;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['itemCode'] = $itemCode;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['itemName'] = $itemName;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['itemDesc'] = $itemDescription;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['hsnCode'] = $hsnCode;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['unitPrice'] = $unitPrice;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['totalDiscount'] = $totalDiscount;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['itemTotalDiscount'] = $itemTotalDiscount1;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['tax'] = $tax;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['totalTax'] = $itemTotalTax1;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['totalPrice'] = $totalPrice;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['tolerance'] = $tolerance;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['qty'] = $quantity;
                    $auditTrail['action_data']['Sales Order Item Detail'][$itemCode]['uom'] = $uomId;

                    // update sales order
                        $updateDeli = "UPDATE `" . ERP_BRANCH_SALES_ORDER . "` 
                        SET 
                            `totalItems`='" . $totalItems . "',
                            `totalDiscount`='" . $totalDiscount . "',
                            `totalAmount`='" . $totalAmount . "' WHERE so_id=" . $lastId . "
                    ";
                    queryUpdate($updateDeli);

                    // update quotations
                    // $updateQuoat = "UPDATE `" . ERP_BRANCH_QUOTATIONS . "` 
                    //     SET 
                    //         `approvalStatus`=10 WHERE quotation_id=" . $quotationId . "";
                    // queryUpdate($updateQuoat);

                    // select from ERP_CUSTOMER_INVOICE_LOGS
            $selectInvLog = "SELECT * FROM `" . ERP_CUSTOMER_INVOICE_LOGS . "` WHERE customer_id=$customerId";
            $selectInvLogData = queryGet($selectInvLog);
            if ($selectInvLogData['numRows'] > 0) {
                // update customer log
                $updateInvLog = "UPDATE `" . ERP_CUSTOMER_INVOICE_LOGS . "`
                                SET
                                    `company_id`=$company_id,
                                    `branch_id`=$branch_id,
                                    `location_id`=$location_id,
                                    `customer_id`=$customerId,
                                    `ref_no`='$getSoNumber',
                                    `profit_center`='$functionalAreas',
                                    `credit_period`='$creditPeriod',
                                    `kamId`='$kamId',
                                    `placeOfSupply`='$placeOfSupply',
                                    `complianceInvoiceType`='$compInvoiceType',
                                    `conversion_rate`='$conversion',
                                    `currency_id`='$currencyId',
                                    `currency_name`='$currencyName',
                                    `billingAddress`='$billingAddress',
                                    `shippingAddress`='$shippingAddress',
                                    `created_by`='$created_by',
                                    `updated_by`='$updated_by' WHERE customer_id=$customerId";
                $updateInvoiceLog = queryInsert($updateInvLog);
            } else {
                // insert customer logs
                $insInvLog = "INSERT INTO `" . ERP_CUSTOMER_INVOICE_LOGS . "`
                            SET
                                `company_id`=$company_id,
                                `branch_id`=$branch_id,
                                `location_id`=$location_id,
                                `customer_id`=$customerId,
                                `ref_no`='$getSoNumber',
                                `profit_center`='$functionalAreas',
                                `credit_period`='$creditPeriod',
                                `kamId`='$kamId',
                                `placeOfSupply`='$placeOfSupply',
                                `complianceInvoiceType`='$compInvoiceType',
                                `conversion_rate`='$conversion',
                                `currency_id`='$currencyId',
                                `currency_name`='$currencyName',
                                `billingAddress`='$billingAddress',
                                `shippingAddress`='$shippingAddress',
                                `created_by`='$created_by',
                                `updated_by`='$updated_by'";
                $invoiceLog = queryInsert($insInvLog);

                $auditTrailreturn = generateAuditTrail($auditTrail);

                    // $lastItemId = $insItemConn['insertedId'];

                    $returnData['status'] = "success";
                    $returnData['message'] = "Data submitted successfully";
                    $flag[] = array("status"=>"warning","message"=>"Data submitted successfully at line ".$i);
            }



                }
                else
                {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Data saved failed try again";
                    $flag[] = array("status"=>"warning","message"=>"Data saved failed try again at line ".$i);
                    $i++;
                    $error_flag++;
                    continue;
                }


            }
        }

        }

        }

                
            $i++;
        }

        $total_array = array("flag"=>$flag,"error_flag"=>$error_flag);

        if($declaration == 0)
        {
            $declaration_value = 'unlock';
        }
        else
        {
            $declaration_value = 'lock';
        }

        $insvalidation = "INSERT INTO `erp_migration_validation`
                        SET 
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `user_id`='$user_id',
                            `migration_type`='OpenSo',
                            `declaration`='$declaration_value',
                            `created_by`='$created_by',
                            `updated_by`='$created_by' 
                            ";
                            queryInsert($insvalidation);

        return $total_array;
    }
    }


?>