<?php
require_once "func-journal.php";

// fetch all uom
function fetchUom($id)
{
    $uom = "SELECT * FROM `" . ERP_INVENTORY_MSTR_UOM . "` WHERE uomId=$id";
    return queryGet($uom, true);
}


class BranchSo extends Accounting
{
    //use Accounting, BoqController;

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

        // $sql = "SELECT summary.*,items.*,hsn.taxPercentage
        // FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` as summary
        // INNER JOIN  `" . ERP_INVENTORY_ITEMS . "` as items ON summary.itemId=items.itemId
        // RIGHT JOIN `" . ERP_HSN_CODE . "` as hsn ON items.hsnCode = hsn.hsnCode
        // WHERE summary.company_id='$company_id' AND items.goodsType IN(3,4) AND summary.status = 'active' AND items.status = 'active'
        // ";

        $sql = "SELECT
      summary.*,
      items.*,
      hsn.taxPercentage AS taxPercentage
      FROM  `" . ERP_INVENTORY_ITEMS . "` AS items
      LEFT JOIN `" . ERP_INVENTORY_STOCKS_SUMMARY . "` AS summary ON items.itemId = summary.itemId
      LEFT JOIN `" . ERP_HSN_CODE . "` AS hsn ON items.hsnCode = hsn.hsnCode
      WHERE items.goodsType IN (3, 4)
          AND items.status = 'active'
          AND (summary.company_id = $company_id OR summary.company_id IS NULL)
          AND (summary.status = 'active' OR summary.status IS NULL)
          AND items.hsnCode IN (SELECT hsnCode FROM `erp_hsn_code`);
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
    function fetchItemSummaryMaterialsServicesBoth()
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
        WHERE summary.company_id='$company_id' AND items.goodsType IN(3,4,5) AND summary.status = 'active' AND items.status = 'active'
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

    // fetch all items
    function fetchAllItemSummary()
    {
        global $company_id;

        $sql = "SELECT *
        FROM erp_inventory_mstr_good_groups AS grp
        LEFT JOIN erp_inventory_items AS items ON grp.goodGroupId = items.goodsGroup
        LEFT JOIN erp_inventory_stocks_summary AS summary ON items.itemId = summary.itemId
        LEFT JOIN erp_hsn_code AS hsn ON items.hsnCode = hsn.hsnCode
        WHERE grp.companyId = $company_id
          AND items.company_id = $company_id
          AND summary.status = 'active'
          AND items.goodsType IN (3, 4)
          AND items.status = 'active'
          AND grp.goodType IN (3, 4)
        ";
        return queryGet($sql, true);
    }

    // fetch all items
    function fetchAllItemSummarySearch($searchText)
    {
        global $company_id;

        $sql = "SELECT *
        FROM erp_inventory_mstr_good_groups AS grp
        LEFT JOIN erp_inventory_items AS items ON grp.goodGroupId = items.goodsGroup
        LEFT JOIN erp_inventory_stocks_summary AS summary ON items.itemId = summary.itemId
        LEFT JOIN erp_hsn_code AS hsn ON items.hsnCode = hsn.hsnCode
        WHERE grp.companyId = $company_id
          AND items.company_id = $company_id
          AND summary.status = 'active'
          AND items.goodsType IN (3, 4)
          AND items.status = 'active'
          AND grp.goodType IN (3, 4)
          AND items.itemName LIKE '%$searchText%'
        ";
        return queryGet($sql, true);
    }

    // fetch all items
    function fetchItemByBarcodescanner($itemId, $batch)
    {
        global $location_id;
        global $branch_id;
        global $company_id;

        $sql = "SELECT `itemId`,`logRef` FROM `erp_inventory_stocks_log` WHERE `companyId`=$company_id AND `branchId`=$branch_id AND `locationId`=$location_id AND `itemId` = $itemId AND `logRef` = '$batch' GROUP BY `logRef`";
        return queryGet($sql);
    }

    // fetch fetchAllItemsByGroupWise
    function fetchAllItemsByGroupWise($gid)
    {
        global $company_id;

        $sql = "SELECT *
        FROM erp_inventory_mstr_good_groups AS grp
        LEFT JOIN erp_inventory_items AS items ON grp.goodGroupId = items.goodsGroup
        LEFT JOIN erp_inventory_stocks_summary AS summary ON items.itemId = summary.itemId
        LEFT JOIN erp_hsn_code AS hsn ON items.hsnCode = hsn.hsnCode
        WHERE grp.companyId = $company_id
          AND items.company_id = $company_id
          AND items.goodsGroup = $gid
          AND summary.status = 'active'
          AND items.goodsType IN (3, 4)
          AND items.status = 'active'
          AND grp.goodType IN (3, 4)
        ";
        return queryGet($sql, true);
    }

    // fetch item filter by material
    function fetchItemServices()
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
        WHERE summary.company_id='$company_id' AND items.goodsType=7 AND summary.status = 'active' AND items.status = 'active'
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
        WHERE summary.company_id='$company_id' and items.goodsType=5 AND bomStatus=0 AND summary.status = 'active' AND items.status = 'active'
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
    function fetchItemSummaryServiceProjects()
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
        WHERE summary.company_id='$company_id' and items.goodsType=5 AND bomStatus!=0 AND summary.status = 'active' AND items.status = 'active'
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

    function addBranchSo($POST, $FILES = null)
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        $customerId = $POST['customerId'];
        $soDate = $POST['soDate'];
        $postingTime = $POST['postingTime'];
        $deliveryDate = $POST['deliveryDate'];
        $shippingAddress = cleanUpString(addslashes($POST['shippingAddress']));
        $billingAddress = cleanUpString(addslashes($POST['billingAddress']));
        $profitCenter = $POST['profitCenter'];
        $creditPeriod = $POST['creditPeriod'];
        $shipToLastInsertedId = $POST['shipToLastInsertedId'];
        $kamId = $POST['kamId'];
        $placeOfSupply = $POST['placeOfSupply'];
        $customerGstinCode = $POST['customerGstinCode'];
        $compInvoiceType = $POST['compInvoiceType'];

        $repeatEvery = $POST['repeatEvery'];
        $startOn = $POST['startOn'];
        $endOn = $POST['endOn'];
        $fobCheckbox = $POST['fobCheckbox'];

        $validitydate = $POST['validitydate'];
        if ($validitydate < date('Y-m-d')) {
            $returnData['status'] = "warning";
            $returnData['message'] = "Validation Date Wrong";
            return $returnData;
        }

        $remarks = addslashes($POST['extra_remark']);

        // Upload the attachment file to the specified directory and retrieve the file path.📂📂📂📂📂
        $attachmentObj = uploadFile($FILES['attachment'], COMP_STORAGE_DIR . "/others/");
        // uploadFile($file = [], $dir = "", $allowedExtensions = [], $maxSize = 0, $minSize = 0)

        $goodsType = $POST['goodsType'];
        $customerPO = $POST['customerPO'];
        $approvalStatus = $POST['approvalStatus'] ?? 0;
        $serviceDescription = $POST['otherCostDetails'] ?? '';
        $quotationId = base64_decode($POST['quotationId']) ?? 0;

        $curr_rate = $POST['curr_rate'];
        $currency = explode('≊', $POST['currency']);
        $currencyId = $currency[0];
        $currencyName = $currency[2];

        $totalDiscount = str_replace(',', '', $POST['grandTotalDiscountAmtInp']) ?? 0;
        $totalTaxAmt = str_replace(',', '', $POST['grandTaxAmtInp']) ?? 0;

        // ***************
        $customerDetailsObj = queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customerId'")['data'];

        $customer_name = $customerDetailsObj['customer_name'];
        $customer_authorised_person_email = $customerDetailsObj['customer_authorised_person_email'];
        $customer_authorised_person_phone = $customerDetailsObj['customer_authorised_person_phone'];

        $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id='$company_id' AND branch_id='$branch_id' AND location_id='$location_id' ORDER BY so_id DESC LIMIT 1";
        $lastSoNo = queryGet($sql);
        // console($lastSoNo);

        // $checkSql = "SELECT so_number FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id='$company_id' AND branch_id='$branch_id' AND location_id='$location_id'";
        // $CheckSoNo = queryGet($checkSql, false);
        // if (count($lastSoNo['data']['so_number']) > 0) {
        //     echo ""
        // }

        if (isset($lastSoNo['data'])) {
            $lastSoNo = $lastSoNo['data']['so_number'] ?? 0;
        } else {
            $lastSoNo = '';
        }
        $returnSoNo = 0;
        // $joNumber = "JO" . rand(00000000, 99999999);
        if ($goodsType == "project") {
            // $returnSoNo = $joNumber;
            $returnSoNo = getSoSerialNumber($lastSoNo);
        } else {
            $returnSoNo = getSoSerialNumber($lastSoNo);
        }

        $customerDetailsObj = queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,customer_code,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customerId'")['data'];
        $customer_Gst = $customerDetailsObj['customer_gstin'];

        $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];

        $branch_Gst = $branchDetailsObj['branch_gstin'];

        $companyGstCode = substr($branch_Gst, 0, 2);
        $customerGstCode = substr($customer_Gst, 0, 2);

        $cgst = 0;
        $sgst = 0;
        $igst = 0;
        $tcs = 0;

        $gstAmt = 0;
        if ($compInvoiceType == "R" || $compInvoiceType == "SEWP") {
            if ($customerGstinCode != "") {
                if ($companyGstCode == $customerGstCode) {
                    $gstAmt = str_replace(',', '', $totalTaxAmt / 2);
                    $cgst = str_replace(',', '', $gstAmt);
                    $sgst = str_replace(',', '', $gstAmt);
                } else {
                    $igst = str_replace(',', '', $totalTaxAmt);
                }
            } else {
                if ($companyGstCode == $placeOfSupply) {
                    $gstAmt = str_replace(',', '', $totalTaxAmt / 2);
                    $cgst = str_replace(',', '', $gstAmt);
                    $sgst = str_replace(',', '', $gstAmt);
                } else {
                    $igst = str_replace(',', '', $totalTaxAmt);
                }
            }
        }

        $insSO = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER . "`
                 SET
                   `so_number`='$returnSoNo',
                   `customer_id`='$customerId',
                   `company_id`='$company_id',
                   `branch_id`='$branch_id',
                   `location_id`='$location_id',
                   `so_date`='$soDate',
                   `soPostingTime`='$postingTime',
                   `delivery_date`='$deliveryDate',
                   `billingAddress`='$billingAddress',
                   `shippingAddress`='$shippingAddress',
                   `profit_center`='$profitCenter',
                   `credit_period`='$creditPeriod',
                   `kamId`='$kamId',
                   `shipToLastInsertedId`='$shipToLastInsertedId',
                   `conversion_rate`='$curr_rate',
                   `currency_id`='$currencyId',
                   `currency_name`='$currencyName',
                   `totalTax`='$totalTaxAmt',
                   `cgst`='$cgst',
                   `sgst`='$sgst',
                   `igst`='$igst',
                   `goodsType`='$goodsType',
                   `jobOrderApprovalStatus`='9',
                   `approvalStatus`='$approvalStatus',
                   `remarks`='$remarks',
                   `customer_po_no`='$customerPO',
                   `created_by`='$created_by',
                   `updated_by`='$updated_by',
                   `validityperiod`  = '$validitydate',
                   `soStatus`='open' ";
        $sqlSOList = queryInsert($insSO);
        if ($sqlSOList['status'] == "success") {
            $lastId = $sqlSOList['insertedId'];

            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_SALES_ORDER;
            $auditTrail['basicDetail']['column_name'] = 'so_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $lastId;  // primary key
            $auditTrail['basicDetail']['party_type'] = 'customer';
            $auditTrail['basicDetail']['party_id'] = $customerId;
            $auditTrail['basicDetail']['document_number'] = $returnSoNo;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Seles Order Creation';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insCustomer);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';


            $auditTrail['action_data']['Sales Order Detail']['so_number'] = $returnSoNo;
            $auditTrail['action_data']['Sales Order Detail']['customer_id'] = $customerId; //
            $auditTrail['action_data']['Sales Order Detail']['so_date'] = $soDate;
            $auditTrail['action_data']['Sales Order Detail']['soPostingTime'] = $postingTime;
            $auditTrail['action_data']['Sales Order Detail']['delivery_date'] = $deliveryDate;
            $auditTrail['action_data']['Sales Order Detail']['billingAddress'] = $billingAddress;
            $auditTrail['action_data']['Sales Order Detail']['shippingAddress'] = $shippingAddress;
            $auditTrail['action_data']['Sales Order Detail']['profit_center'] = $profitCenter; //
            $auditTrail['action_data']['Sales Order Detail']['credit_period'] = $creditPeriod;
            $auditTrail['action_data']['Sales Order Detail']['kamId'] = $kamId; //
            $auditTrail['action_data']['Sales Order Detail']['conversion_rate'] = $curr_rate;
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

            // insert attachment
            if ($attachmentObj['status'] == 'success') {
                $name = $attachmentObj['data'];
                $type = $FILES['attachment']['type'];
                $size = $FILES['attachment']['size'];
                $path = COMP_STORAGE_URL . '/others/' . $attachmentObj['data'];

                $insertAttachmentSql = "INSERT INTO `" . ERP_ATTACH_DOCUMENTS . "`
                        SET
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `file_name`='" . $name . "',
                            `file_path`='" . $path . "',
                            `file_type`='" . $type . "',
                            `file_size`='" . $size . "',
                            `refName`='so-creation',
                            `ref_no`='$lastId'
                ";
                $insertAttachment = queryInsert($insertAttachmentSql);
            }

            $totalDiscount = 0;
            $totalTax = 0;
            $totalAmount = 0;
            $listItem = $POST['listItem'] ?? '';
            $totalItems = count($listItem);
            $i = 1;

            foreach ($listItem as $item) {
                $tolerance = $item['tolerance'] ?? 0;
                if ($item['tolerance'] != "") {
                    $tolerance = $item['tolerance'];
                } else {
                    $tolerance = 0;
                }
                $itemName = addslashes($item['itemName']);
                $itemDesc = addslashes($item['itemDesc']);
                $itemRemarks = addslashes($item['itemRemarks']) ?? '';
                $unitPrice = str_replace(',', '', $item['unitPrice']) ?? 0;
                $totalDiscount = $item['totalDiscount'] ?? 0;
                $itemTotalDiscount1 = str_replace(',', '', $item['itemTotalDiscount1']) ?? 0;
                $tax = 0;
                $itemTotalTax1 = 0;
                if ($compInvoiceType == "R" || $compInvoiceType == "SEWP") {
                    $tax = $item['tax'] ?? 0;
                    $itemTotalTax1 = str_replace(',', '', $item['itemTotalTax1']) ?? 0;
                }
                $totalPrice = str_replace(',', '', $item['totalPrice']) ?? 0;
                $qty = $item['qty'] ?? 0;

                $countI = $i++;
                $insItems = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_ITEMS . "`
                            SET
                            `so_id`='$lastId',
                            `lineNo`='$countI',
                            `inventory_item_id`='" . $item['itemId'] . "',
                            `goodsType`='" . $item['goodsType'] . "',
                            `itemCode`='" . $item['itemCode'] . "',
                            `itemName`='" . $itemName . "',
                            `itemRemarks`='" . $itemRemarks . "',
                            `itemDesc`='" . $itemDesc . "',
                            `hsnCode`='" . $item['hsnCode'] . "',
                            `unitPrice`='" . $unitPrice . "',
                            `totalDiscount`='" . $totalDiscount . "',
                            `itemTotalDiscount`='" . $itemTotalDiscount1 . "',
                            `tax`='" . $tax . "',
                            `totalTax`='" . $itemTotalTax1 . "',
                            `totalPrice`='" . $totalPrice . "',
                            `tolerance`='" . $tolerance . "',
                            `qty`='" . $qty . "',
                            `remainingQty`='" . $qty . "',
                            `invStatus`='open',
                            `uom`='" . $item['uom'] . "'
                ";
                $sqlItemList = queryInsert($insItems);
                $totalDiscountAmount=0;
                if ($sqlItemList['status'] == 'success') {

                    $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']]['lineNo'] = $countI;
                    // $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']]['goodsType']=0;
                    $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']]['itemCode'] = $item['itemCode'];
                    $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']]['itemName'] = $item['itemName'];
                    $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']]['itemDesc'] = addslashes($item['itemDesc']);
                    $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']]['hsnCode'] = $item['hsnCode'];
                    $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']]['unitPrice'] = $item['unitPrice'];
                    $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']]['totalDiscount'] = $item['totalDiscount'];
                    $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']]['itemTotalDiscount'] = $item['itemTotalDiscount1'];
                    $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']]['tax'] = $item['tax'];
                    $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']]['totalTax'] = $item['itemTotalTax1'];
                    $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']]['totalPrice'] = $item['totalPrice'];
                    $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']]['tolerance'] = $tolerance;
                    $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']]['qty'] = $item['qty'];
                    $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']]['uom'] = $item['uom'];

                    $itemLastId = $sqlItemList['insertedId'];
                    $tot = (($item['unitPrice'] * $item['qty']) - $item['itemTotalDiscount1']) + $item['itemTotalTax1'];
                    // $dis = ($tot * $item['totalDiscount']) / 100;
                    $totalDiscountAmount += str_replace(',', '', $item['itemTotalDiscount1']);
                    $totalTax += str_replace(',', '', $item['itemTotalTax1']);
                    $totalAmount = str_replace(',', '', $totalAmount) + str_replace(',', '', $tot);

                    // create delivery schedule
                    foreach ($item['deliverySchedule'] as $delItem) {
                        $insDeli = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` 
                                    SET 
                                    `so_item_id`='" . $itemLastId . "',
                                    `delivery_date`='" . $delItem['multiDeliveryDate'] . "',
                                    `deliveryStatus`='open',
                                    `remainingQty`='" . $delItem['quantity'] . "',
                                    `qty`='" . $delItem['quantity'] . "'
                        ";
                        $scheduleList = queryInsert($insDeli);

                        $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']][$delItem['multiDeliveryDate']]['delivery_date'] = $delItem['multiDeliveryDate'];
                        $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']][$delItem['multiDeliveryDate']]['qty'] = $delItem['quantity'];
                        $auditTrail['action_data']['Sales Order Item Detail'][$item['itemCode']][$delItem['multiDeliveryDate']]['deliveryStatus'] = 'open';
                    }
                }
            }
            // update sales order
            $updateDeli = "UPDATE `" . ERP_BRANCH_SALES_ORDER . "` 
                SET 
                    `totalItems`='" . $totalItems . "',
                    `totalTax`='" . $totalTax . "',
                    `totalDiscount`='" . $totalDiscountAmount . "',
                    `totalAmount`='" . $totalAmount . "' WHERE so_id=" . $lastId . "
            ";
            queryUpdate($updateDeli);

            // update quotations
            $updateQuoat = "UPDATE `" . ERP_BRANCH_QUOTATIONS . "` 
                SET 
                    `approvalStatus`=10 WHERE quotation_id=" . $quotationId . "";
            queryUpdate($updateQuoat);

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
                                    `profit_center`='$profitCenter',
                                    `credit_period`='$creditPeriod',
                                    `kamId`='$kamId',
                                    `placeOfSupply`='$placeOfSupply',
                                    `customerOrderNo`='$customerPO',
                                    `complianceInvoiceType`='$compInvoiceType',
                                    `conversion_rate`='$curr_rate',
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
                                `profit_center`='$profitCenter',
                                `credit_period`='$creditPeriod',
                                `kamId`='$kamId',
                                `placeOfSupply`='$placeOfSupply',
                                `customerOrderNo`='$customerPO',
                                `complianceInvoiceType`='$compInvoiceType',
                                `conversion_rate`='$curr_rate',
                                `currency_id`='$currencyId',
                                `currency_name`='$currencyName',
                                `billingAddress`='$billingAddress',
                                `shippingAddress`='$shippingAddress',
                                `created_by`='$created_by',
                                `updated_by`='$updated_by'";
                $invoiceLog = queryInsert($insInvLog);
            }

            // insert other services
            $lastQuery = "SELECT * FROM " . ERP_BRANCH_PURCHASE_REQUEST . " WHERE `company_id` = '$company_id' AND `branch_id` = '$branch_id' AND `location_id` = '$location_id' ORDER BY `purchaseRequestId` DESC LIMIT 1";

            $last = queryGet($lastQuery);
            $lastRow = $last['data'] ?? "";
            $lastPrId = $lastRow['prCode'] ?? "";
            $prCode = getPRSerialNumber($lastPrId);

            if ($fobCheckbox == "checked") {
                $insert = "INSERT INTO `" . ERP_BRANCH_PURCHASE_REQUEST . "` 
                        SET 
                            `prCode`='$prCode',
                            `company_id`=$company_id,
                            `branch_id`=$branch_id,
                            `location_id`=$location_id,
                            `expectedDate`='$deliveryDate',
                            `pr_date`='$soDate',
                            `pr_type`='service',
                            `refNo`='$returnSoNo',
                            `pr_status`=9,
                            `created_by`='$created_by',
                            `updated_by`='$updated_by' 
                    ";
                $insertOthercost = queryInsert($insert);
                $othercostInsertedId = $insertOthercost['insertedId'];

                foreach ($serviceDescription as $oneCost) {
                    // if ($oneCost['services'] != null && $oneCost['qty'] != null) {

                    $services = '';
                    $serviceItemId = 0;
                    $serviceItemCode = '';
                    $serviceItemName = '';

                    if ($oneCost['services'] != "") {
                        $services = explode("_", $oneCost['services']);
                        $serviceItemId = $services[0];
                        $serviceItemCode = $services[1];
                        $serviceItemName = $services[2];
                        $service_unit = $services[3];
                    }

                    $serviceQty = $oneCost['qty'] ?? 0;

                    $insertItem = "INSERT INTO `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "` 
                                    SET 
                                        `company_id`=$company_id,
                                        `branch_id`=$branch_id,
                                        `location_id`=$location_id,
                                        `prId`='$othercostInsertedId',
                                        `itemId`='" . $serviceItemId . "',
                                        `itemCode`='$serviceItemCode',
                                        `itemName`='$serviceItemName',
                                        `uom`='$service_unit',
                                        `itemQuantity`='$serviceQty',
                                        `remainingQty`='$serviceQty'
                    ";
                    $insertOthercostItem = queryInsert($insertItem);

                    if ($insertOthercost['status'] == 'success') {
                        $auditTrail['action_data']['Other Cost'][$services]['services'] = $services;
                        $auditTrail['action_data']['Other Cost'][$services]['qty'] = $serviceQty;
                    }
                    // }
                }
            }
            if ($sqlItemList['status'] == "success") {

                $auditTrail['action_data']['Sales Order Detail']['totalItems'] = $totalItems;
                $auditTrail['action_data']['Sales Order Detail']['totalDiscount'] = $totalDiscount;
                $auditTrail['action_data']['Sales Order Detail']['totalAmount'] = $totalAmount;

                $auditTrailreturn = generateAuditTrail($auditTrail);

                ///---------------------------------Audit Log Start---------------------


                global $current_userName;
                global $companyNameNav;

                $whatsapparray = [];
                $whatsapparray['templatename'] = 'so_created_order_confirmation_msg';
                $whatsapparray['to'] = $customer_authorised_person_phone;
                $whatsapparray['customername'] = $customer_name;
                $whatsapparray['companyname'] = $companyNameNav;
                $whatsapparray['so_number'] = $returnSoNo;
                $whatsapparray['quickcontact'] = null;
                $whatsapparray['current_userName'] = $current_userName;

                SendMessageByWhatsappTemplate($whatsapparray);

                return [
                    "type" => "pos_salesorder",
                    "status" => "success",
                    "message" => "Order Created Successfully",
                    "ins" => $insSO,
                    "soNumber" => $getSoNumber,
                    "insertOthercost" => $insertOthercost,
                    "insertOthercostitem" => $insertOthercostItem,
                    "serviceDescription" => $serviceDescription,
                    "updateQuoat" => $updateQuoat,
                    "fobCheckbox" => $fobCheckbox,
                    "auditTrailreturn" => $auditTrailreturn
                ];
            } else {
                return [
                    "status" => "warning",
                    "message" => "Somthing went wrong 01",
                ];
            }
        } else {
            return [
                "status" => "warning",
                "message" => "Somthing went wrong 02",
                "sqlSOList" => $sqlSOList,
                "sql" => $insSO,
            ];
        }

        return $returnData;
    }

    function getBoqDetailsInSoControll($itemId)
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $boqObj = queryGet('SELECT boqs.*,items.`itemCode`,items.`itemName` FROM `erp_boq` AS boqs LEFT JOIN `erp_inventory_items` AS items ON boqs.`itemId`= items.`itemId` WHERE boqs.`companyId`=' . $company_id . ' AND boqs.`branchId`=' . $branch_id . ' AND boqs.`locationId`=' . $location_id . ' AND boqs.`itemId`=' . $itemId . ' AND boqs.`boqStatus` = "active"');
        if ($boqObj['status'] != "success") {
            return [
                "status" => $boqObj["status"],
                "message" => $boqObj["message"],
                "data" => [
                    "boq_data" => [],
                    "boq_service_data" => [],
                    "boq_material_data" => [],
                    "boq_hd_data" => [],
                    "boq_other_head_data" => [],
                ]
            ];
        } else {
            $boqId = $boqObj["data"]["boqId"];
            $boqServiceObj = queryGet('SELECT `boqItems`.*, FORMAT((`boqItems`.`consumption`+(`boqItems`.`consumption`*`boqItems`.`extra`/100)),2) AS totalConsumption, items.`itemCode`, items.`itemName`, items.`goodsType`, items.`item_sell_type` FROM `erp_boq_item` AS boqItems LEFT JOIN `erp_inventory_items` AS items ON `boqItems`.`item_id`= items.`itemId` WHERE `isService`=1 AND `boq_id`=' . $boqId, true);

            $boqMaterialObj = queryGet('SELECT `boqItems`.*, FORMAT((`boqItems`.`consumption`+(`boqItems`.`consumption`*`boqItems`.`extra`/100)),2) AS totalConsumption, items.`itemCode`, items.`itemName`, items.`goodsType`, items.`item_sell_type` FROM `erp_boq_item` AS boqItems LEFT JOIN `erp_inventory_items` AS items ON `boqItems`.`item_id`= items.`itemId` WHERE `isService`=0 AND `boq_id`=' . $boqId, true);

            $boqHdObj = queryGet('SELECT `boqItem`.*, `costCenter`.* FROM `erp_boq_item_other` AS boqItem JOIN `erp_cost_center` AS costCenter ON costCenter.CostCenter_id = boqItem.cost_center_id WHERE boqItem.`head_type` != "other" AND boqItem.`boq_id` = ' . $boqId, true);

            $boqOtherHeadObj = queryGet('SELECT `costCenter`.*, `boqItem`.* FROM  `erp_boq_item_other` AS boqItem JOIN `erp_cost_center` AS costCenter ON costCenter.CostCenter_id = boqItem.cost_center_id WHERE boqItem.`head_type`="other" AND boqItem.`boq_id`=' . $boqId, true);
            $result = [
                "status" => $boqObj["status"],
                "message" => $boqObj["message"],
                "data" => [
                    "boq_data" => $boqObj["data"],
                    "boq_service_data" => $boqServiceObj["data"],
                    "boq_material_data" => $boqMaterialObj["data"],
                    "boq_hd_data" => $boqHdObj['data'],
                    "boq_other_head_data" => $boqOtherHeadObj["data"],
                ]
            ];
            return $result;
        }
    }

    function jobOrderCompletionConfirmation($POST)
    {
        $returnData = [];
        global $company_id;
        global $branch_id;
        global $location_id;

        // return $POST;

        $so_id = $POST['soDetails']['soId'];
        $so_number = $POST['soDetails']['so_number'];
        $modalListItem = $POST['modalListItem'];
        $flag = 0;
        $boqControllerObj = new BoqController();
        foreach ($modalListItem as $keyssmain => $one) {
            $so_item_id = $one['so_item_id'];
            $itemCode = $one['itemCode'];
            $itemId = $one['inventory_item_id'];
            $itemQty = $one['itemQty'];
            $completionPercentage = $one['completionPercentage'];
            $remainingQtyHidden = $one['remainingQtyHidden'];
            $invStatus = $one['invStatus'];
            $remainingQtyHiddenPlus = $remainingQtyHidden - $one['completionPercentage'];
            $completionPercentagePlus = $one['completionPercentage'] + $one['completion_value'];


            $boqDetailObj = $boqControllerObj->getBoqDetails($itemId);

            // echo '----------------Boq'.$keyssmain;
            // console($boqDetailObj);

            $finalProductDetails = [];
            $finalProductDetails['parentGlId'] = $boqDetailObj["data"]["boq_data"]['parentGlId'];
            $finalProductDetails['itemCode'] = $boqDetailObj["data"]["boq_data"]['itemCode'];
            $finalProductDetails['itemName'] = $boqDetailObj["data"]["boq_data"]['itemName'];
            $finalProductDetails['cosp_m'] = $boqDetailObj["data"]["boq_data"]['cosp_m'] * $completionPercentage;
            $finalProductDetails['cosp_a'] = $boqDetailObj["data"]["boq_data"]['cosp_a'] * $completionPercentage;
            $finalProductDetails['cosp_i'] = $boqDetailObj["data"]["boq_data"]['cosp_i'] * $completionPercentage;

            $consumpProductData = [];

            foreach ($boqDetailObj["data"]["boq_material_data"] as $keyss => $materialOne) {
                $stockLogTransferQty = $materialOne['totalConsumption'] * $completionPercentage;

                if ($materialOne["priceType"] == "V") {
                    $consumpProductData[] = [
                        "type" => $materialOne['type'],
                        "stockLogTransferQty" => $stockLogTransferQty,
                        "parentGlId" => $materialOne['parentGlId'],
                        "itemCode" => $materialOne['itemCode'],
                        "itemName" => $materialOne['itemName'],
                        "unitprice" => $materialOne['movingWeightedPrice'],
                        "price" => $materialOne['movingWeightedPrice'] * $stockLogTransferQty,
                    ];
                } else {
                    $consumpSfgProductSql = "SELECT boq.`cogm` as cogmprice FROM `erp_boq` WHERE `locationId`=" . $location_id . " AND boqStatus` = 'active' AND `itemId`=" . $materialOne["item_id"] . " ORDER BY boqId DESC";

                    $consumpSfgProductObj = queryGet($consumpSfgProductSql);

                    if ($consumpSfgProductObj["status"] == "success") {
                        $consumpProductData[] = [
                            "type" => $materialOne['type'],
                            "stockLogTransferQty" => $stockLogTransferQty,
                            "parentGlId" => $materialOne['parentGlId'],
                            "itemCode" => $materialOne['itemCode'],
                            "itemName" => $materialOne['itemName'],
                            "unitprice" => $consumpSfgProductObj['data']['cogmprice'],
                            "price" => $consumpSfgProductObj['data']['cogmprice'] * $stockLogTransferQty,
                        ];
                    } else {
                        $consumpProductData[] = [
                            "type" => $materialOne['type'],
                            "stockLogTransferQty" => $stockLogTransferQty,
                            "parentGlId" => $materialOne['parentGlId'],
                            "itemCode" => $materialOne['itemCode'],
                            "itemName" => $materialOne['itemName'],
                            "unitprice" => $materialOne['movingWeightedPrice'],
                            "price" => $materialOne['movingWeightedPrice'] * $stockLogTransferQty,
                        ];
                    }
                }
            }

            foreach ($boqDetailObj["data"]["boq_service_data"] as $keyss => $searviceOne) {
                $stockLogTransferQty = $searviceOne['totalConsumption'] * $completionPercentage;

                if ($searviceOne["priceType"] == "V") {
                    $consumpProductData[] = [
                        "type" => $searviceOne['type'],
                        "stockLogTransferQty" => $stockLogTransferQty,
                        "parentGlId" => $searviceOne['parentGlId'],
                        "itemCode" => $searviceOne['itemCode'],
                        "itemName" => $searviceOne['itemName'],
                        "unitprice" => $searviceOne['movingWeightedPrice'],
                        "price" => $searviceOne['movingWeightedPrice'] * $stockLogTransferQty,
                    ];
                } else {
                    $consumpSfgProductSql = "SELECT boq.`cogm` as cogmprice FROM `erp_boq` WHERE `locationId`=" . $location_id . " AND boqStatus` = 'active' AND `itemId`=" . $searviceOne["item_id"] . " ORDER BY boqId DESC";

                    $consumpSfgProductObj = queryGet($consumpSfgProductSql);

                    if ($consumpSfgProductObj["status"] == "success") {
                        $consumpProductData[] = [
                            "type" => $searviceOne['type'],
                            "stockLogTransferQty" => $stockLogTransferQty,
                            "parentGlId" => $searviceOne['parentGlId'],
                            "itemCode" => $searviceOne['itemCode'],
                            "itemName" => $searviceOne['itemName'],
                            "unitprice" => $consumpSfgProductObj['data']['cogmprice'],
                            "price" => $consumpSfgProductObj['data']['cogmprice'] * $stockLogTransferQty,
                        ];
                    } else {
                        $consumpProductData[] = [
                            "type" => $searviceOne['type'],
                            "stockLogTransferQty" => $stockLogTransferQty,
                            "parentGlId" => $searviceOne['parentGlId'],
                            "itemCode" => $searviceOne['itemCode'],
                            "itemName" => $searviceOne['itemName'],
                            "unitprice" => $searviceOne['movingWeightedPrice'],
                            "price" => $searviceOne['movingWeightedPrice'] * $stockLogTransferQty,
                        ];
                    }
                }
            }

            //********************************ACC Start********************************/           

            //Accounting Information

            $consumptionInputData = [
                "BasicDetails" => [
                    "documentNo" => $so_number,
                    "documentDate" => date("Y-m-d"),
                    "postingDate" =>  date("Y-m-d"),
                    "reference" => '',
                    "remarks" => "Production declaration for - " . $itemCode,
                    "journalEntryReference" => "Production"
                ],
                "finalProductData" => $finalProductDetails,
                "consumpProductData" => $consumpProductData
            ];

            // echo "<br>Accounting Information</br>";
            // console($consumptionInputData);

            //**************************Production Declaration Accounting Start****************************** */
            $respproductionDeclaration = $this->productionDeclarationAccountingPostingProject($consumptionInputData, 'ProductiondeclarationProjectsissuance', 0);

            // console($respproductionDeclaration);

            //**************************Production Declaration Accounting End****************************** */


            //**************************FG/SFG Declaration Accounting Start****************************** */
            $respfgsfgDeclaration = $this->FGSFGDeclarationAccountingPostingProject($consumptionInputData, 'ProjectDeclaration', 0);

            // console($respfgsfgDeclaration);

            //**************************FG/SFG Declaration Accounting End****************************** */



            //********************************ACC End******************************************/

            if ($invStatus == "done") {
                $updateSalesItemsObj = queryUpdate("UPDATE `" . ERP_BRANCH_SALES_ORDER_ITEMS . "`
                SET
                    remainingQty=$remainingQtyHidden,
                    completion_value=$completionPercentage 
                WHERE so_item_id=$so_item_id AND so_id=$so_id AND inventory_item_id=$itemId");
            } else {
                $updateSalesItemsObj = queryUpdate("UPDATE `" . ERP_BRANCH_SALES_ORDER_ITEMS . "`
                SET
                    remainingQty=$remainingQtyHidden,
                    completion_value=$completionPercentagePlus 
                WHERE so_item_id=$so_item_id AND so_id=$so_id AND inventory_item_id=$itemId");
            }

            // checks if a variable is not NULL, not zero, and not a negative value
            if ($completionPercentage != "" && $completionPercentage > 0) {
                $invStatusUpdateSql = "UPDATE `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` SET `invStatus`='pending' WHERE so_item_id=$so_item_id AND `so_id`=$so_id AND `inventory_item_id`=$itemId";
                $invStatusUpdate = queryUpdate($invStatusUpdateSql);

                // make a logs for created job orders
                $insJobOrderSql = "INSERT INTO `" . ERP_BRANCH_JOB_ORDER_LOGS . "` 
                SET
                    `so_id`='$so_id',
                    `company_id`='$company_id',
                    `branch_id`='$branch_id',
                    `location_id`='$location_id',
                    `inventory_item_id`='$itemId',
                    `qty`='$itemQty',
                    `remainingQty`='$remainingQtyHidden',
                    `completion_value`='$completionPercentage'";
                $insJobOrderSqlObj = queryInsert($insJobOrderSql);
            }

            if ($remainingQtyHidden == 0) {
                $flag = 0;
            } else {
                $flag++;
            }
        }



        // return ($POST);

        if ($flag == 0) {
            $updateJobOrderApprovalStatusSql = "UPDATE `" . ERP_BRANCH_SALES_ORDER . "` 
                    SET
                        jobOrderApprovalStatus=14
                    WHERE so_id=$so_id AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id";
            $updateJobOrderApprovalStatusObj = queryUpdate($updateJobOrderApprovalStatusSql);
        } else {
            $updateJobOrderApprovalStatusSql = "UPDATE `" . ERP_BRANCH_SALES_ORDER . "` 
                    SET
                        jobOrderApprovalStatus=14
                    WHERE so_id=$so_id AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id";
            $updateJobOrderApprovalStatusObj = queryUpdate($updateJobOrderApprovalStatusSql);
        }

        if ($updateSalesItemsObj['status'] == "success") {
            return [
                "status" => "success",
                "message" => "Inserted Successfully"
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Somthing went wrong 01"
            ];
        }
        return $returnData;
    }

    // start fetch branch so listing
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

    // get services
    function getServices()
    {
        global $company_id;

        $selectSql = "SELECT * FROM `" . ERP_SERVICES . "` WHERE `status`='active'";
        return queryGet($selectSql, true);
    }

    // service details
    function getServiceDetails($serviceId)
    {
        global $company_id;

        $selectSql = "SELECT * FROM `" . ERP_SERVICES . "` WHERE serviceId='" . $serviceId . "' AND companyId = '" . $company_id . "'  AND `status`!='deleted'";
        return queryGet($selectSql);
    }

    // fetch items group
    function getItemsGroup()
    {
        global $company_id;
        $selectSql = "SELECT * FROM `erp_inventory_mstr_good_groups` WHERE `companyId`=$company_id AND `goodType` IN(3,4)";
        return queryGet($selectSql, true);
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

        $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` WHERE `so_id`='$soId' AND status='active'";
        if ($res = $dbCon->query($sql)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['status'] = "success";
            $returnData['message'] = "Data found!";
            $returnData['sql'] = $sql;
            $returnData['data'] = $row;
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }
    function fetchParyOrderItems($orderId)
    {
        $returnData = [];
        global $dbCon;

        $sql = "SELECT partyItem.*, inventory.*
                    FROM
                        `" . ERP_PARTY_ORDER_ITEM . "` as partyItem,
                         `" . ERP_INVENTORY_ITEMS . "` as inventory
                    WHERE
                        `order_id`='$orderId'
                        AND inventory.itemId = partyItem.item_id
        ";
        if ($res = $dbCon->query($sql)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['status'] = "success";
            $returnData['message'] = "Data found!";
            $returnData['sql'] = $sql;
            $returnData['data'] = $row;
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }

    function fetchProformaInvoiceItems($proformaId)
    {
        $returnData = [];
        global $dbCon;

        $sql = "SELECT proformaInvItem.*, inventory.*
                    FROM
                        `" . ERP_PROFORMA_INVOICE_ITEMS . "` as proformaInvItem,
                         `" . ERP_INVENTORY_ITEMS . "` as inventory
                    WHERE
                    proformaInvItem.proforma_invoice_id='$proformaId'
                        AND inventory.itemId = proformaInvItem.inventory_item_id
        ";
        if ($res = $dbCon->query($sql)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['status'] = "success";
            $returnData['message'] = "Data found!";
            $returnData['sql'] = $sql;
            $returnData['data'] = $row;
        } else {
            $returnData['status'] = "warning";
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
        global $company_id;
        global $branch_id;
        global $location_id;

        $ins = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE company_id='$company_id' AND company_branch_id='$branch_id' AND location_id='$location_id' AND `customer_id`='$id'";

        return queryGet($ins, true);
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
        global $company_id;
        global $branch_id;
        global $location_id;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND `so_id`='$soId'";
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

    function fetchSoDetailsBySoId($soId)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE `so_id`='$soId'";
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


    function convertToWHSLBatchArray($data)
    {
        return array_reduce($data, function ($carry, $item) {
            $warehouse_id = $item['warehouse_id'];
            $storage_location_id = $item['storage_location_id'];

            if (!array_key_exists($warehouse_id, $carry)) {
                $carry[$warehouse_id] = [
                    'warehouse_id' => $item['warehouse_id'],
                    'warehouse_code' => $item['warehouse_code'],
                    'warehouse_name' => $item['warehouse_name'],
                    'storage_locations' => []
                ];
            }

            if (!array_key_exists($storage_location_id, $carry[$warehouse_id]['storage_locations'])) {
                $carry[$warehouse_id]['storage_locations'][$storage_location_id] = [
                    'storage_location_id' => $item['storage_location_id'],
                    'storage_location_code' => $item['storage_location_code'],
                    'storage_location_name' => $item['storage_location_name'],
                    'storage_location_type' => $item['storage_location_type'],
                    'storageLocationTypeSlug' => $item['storageLocationTypeSlug'],
                    'batches' => []
                ];
            }

            $carry[$warehouse_id]['storage_locations'][$storage_location_id]['batches'][] = $item;

            return $carry;
        }, []);
    }


    // add branch SO delivery 
    function deliveryCreateItemQty($item_id)
    {

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        $today = date("Y-m-d");
        //$selStockLog = "SELECT loc.storage_location_id,loc.storage_location_code,loc.storage_location_name,loc.storage_location_type,loc.storageLocationTypeSlug,SUM(log.itemQty) as itemQty,log.itemUom,log.logRef,grn.postingDate FROM erp_inventory_stocks_log AS log LEFT JOIN erp_storage_location AS loc ON log.storageLocationId=loc.storage_location_id LEFT JOIN erp_grn AS grn ON log.logRef=grn.grnCode WHERE log.companyId=$company_id AND log.branchId=$branch_id AND log.locationId=$location_id AND log.itemId=$item_id AND grn.postingDate BETWEEN '2023-06-01' AND '" . $today . "' AND loc.storageLocationTypeSlug IN('rmWhOpen','rmWhReserve','fgWhOpen') GROUP BY loc.storage_location_id,loc.storage_location_code,loc.storage_location_name,loc.storage_location_type,loc.storageLocationTypeSlug,log.itemUom,log.logRef,grn.postingDate ORDER BY grn.postingDate ASC";
        $selStockLog = "SELECT
                warh.warehouse_id,
                warh.warehouse_code,
                warh.warehouse_name,
                loc.storage_location_id,
                loc.storage_location_code,
                loc.storage_location_name,
                loc.storage_location_type,
                loc.storageLocationTypeSlug,
                SUM(log.itemQty) AS itemQty,
                log.itemUom,
                log.logRef,
                grn.postingDate
            FROM
                erp_inventory_stocks_log AS log
            LEFT JOIN erp_storage_location AS loc
            ON
                log.storageLocationId = loc.storage_location_id
            LEFT JOIN erp_storage_warehouse AS warh
            ON
                warh.warehouse_id = loc.warehouse_id
            LEFT JOIN erp_grn AS grn
            ON
                log.logRef = grn.grnCode
            WHERE
            log.companyId=$company_id 
            AND log.branchId=$branch_id 
            AND log.locationId=$location_id 
            AND log.itemId=$item_id 
            AND loc.storageLocationTypeSlug IN('rmWhOpen', 'rmWhReserve', 'fgWhOpen')
            GROUP BY
                loc.storage_location_id,
                loc.storage_location_code,
                loc.storage_location_name,
                loc.storage_location_type,
                loc.storageLocationTypeSlug,
                log.itemUom,
                log.logRef,
                grn.postingDate
            ORDER BY
                grn.postingDate ASC";

        $getStockLog = queryGet($selStockLog, true);
        // return $getStockLog;

        $totquantities = array_column($getStockLog['data'], "itemQty");
        $itemOpenStocks = array_sum($totquantities);
        if ($itemOpenStocks == '') {
            $itemOpenStocks = '0';
        }
        $getStockLog['sumOfBatches'] = $itemOpenStocks;

        return $getStockLog;
    }

    function itemQtyTotalStockCheck($item_id, $stockLoc, $asondate = null)
    {

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        if (empty($asondate)) {
            $asondate = date("Y-m-d");
        }

        // $selStockLog = "SELECT COALESCE(SUM(itemQty), 0) AS itemQty
        // FROM erp_inventory_stocks_log
        // WHERE companyId=$company_id 
        // AND branchId=$branch_id 
        // AND locationId=$location_id 
        // AND storageType IN ($stockLoc)
        // AND itemId = $item_id
        // AND postingDate <= '$asondate' 
        // GROUP BY itemId";
        // $getStockLog = queryGet($selStockLog);
        $returnobj = $this->itemQtyStockCheck($item_id, $stockLoc, '', '', $asondate);
        $getStockLog['data']['itemQty'] = $returnobj['sumOfBatches'] ?? 0;

        return $getStockLog;
    }


    // add branch SO delivery 
    function itemQtyStockCheck($item_id, $stockLoc, $ordering = 'ASC', $refNumber = null, $asondate = null)
    {

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        if (empty($asondate)) {
            $asondate = date("Y-m-d");
        }
        $cond = '';
        // if (!empty($refNumber)) {
        //     $cond .= " AND log.refNumber IN ($refNumber)";
        // }

        if (!empty($refNumber)) {
            $cond .= " AND CONCAT(log.logRef, log.storageLocationId) IN ($refNumber)";
        }

        //$selStockLog = "SELECT loc.storage_location_id,loc.storage_location_code,loc.storage_location_name,loc.storage_location_type,loc.storageLocationTypeSlug,SUM(log.itemQty) as itemQty,log.itemUom,log.logRef,grn.postingDate FROM erp_inventory_stocks_log AS log LEFT JOIN erp_storage_location AS loc ON log.storageLocationId=loc.storage_location_id LEFT JOIN erp_grn AS grn ON log.logRef=grn.grnCode WHERE log.companyId=$company_id AND log.branchId=$branch_id AND log.locationId=$location_id AND log.itemId=$item_id AND grn.postingDate BETWEEN '2023-06-01' AND '" . $today . "' AND loc.storageLocationTypeSlug IN('rmWhOpen','rmWhReserve','fgWhOpen') GROUP BY loc.storage_location_id,loc.storage_location_code,loc.storage_location_name,loc.storage_location_type,loc.storageLocationTypeSlug,log.itemUom,log.logRef,grn.postingDate ORDER BY grn.postingDate ASC";
        $selStockLog = "SELECT
                warh.warehouse_id,
                warh.warehouse_code,
                warh.warehouse_name,
                loc.storage_location_id,
                loc.storage_location_code,
                loc.storage_location_name,
                loc.storage_location_type,
                loc.storageLocationTypeSlug,
                (SELECT SUM(itemQty) FROM erp_inventory_stocks_log WHERE storageLocationId=log.storageLocationId AND logRef= log.logRef AND itemId=log.itemId AND companyId=log.companyId AND branchId=log.branchId AND locationId=log.locationId) AS itemQty,
                log.logRef,
                log.bornDate,
                MAX(log.itemPrice) as itemPrice,
                CONCAT(log.logRef, log.storageLocationId) AS logRefConcat -- Concatenate logRef and storageLocationId
            FROM
                erp_inventory_stocks_log AS log
            LEFT JOIN erp_storage_location AS loc
            ON
                log.storageLocationId = loc.storage_location_id
            LEFT JOIN erp_storage_warehouse AS warh
            ON
                loc.warehouse_id=warh.warehouse_id             
            WHERE
            log.companyId=$company_id 
            AND log.branchId=$branch_id 
            AND log.locationId=$location_id 
            AND log.itemId=$item_id 
            AND loc.storageLocationTypeSlug IN($stockLoc)
            AND log.storageType IN($stockLoc)
            AND log.bornDate <= '$asondate' 
            $cond 
            GROUP BY
                loc.storage_location_id,
                loc.storage_location_code,
                loc.storage_location_name,
                loc.storage_location_type,
                loc.storageLocationTypeSlug,
                log.logRef,
                log.bornDate
            HAVING itemQty > 0
            ORDER BY
                log.bornDate $ordering";


        $getStockLog = queryGet($selStockLog, true);
        // return $getStockLog;

        $totquantities = array_column($getStockLog['data'], "itemQty");
        $itemOpenStocks = array_sum($totquantities);
        if ($itemOpenStocks == '') {
            $itemOpenStocks = '0';
        }
        $getStockLog['sumOfBatches'] = $itemOpenStocks;

        return $getStockLog;
    }

    // add branch SO delivery 
    function branchSoDeliveryCreate($POST)
    {
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
        $flug = 0;

        $deliveryNo = "D" . time() . rand(100, 999);
        $return = array();

        // check item quantity
        $itemList = $POST["listItem"];

        $noOfItemsWhoDontHaveStocks = 0;
        foreach ($itemList as $key => $oneItem) {
            if ($oneItem['sumOfBatches'] <= 0) {
                $noOfItemsWhoDontHaveStocks++;
            }
        }

        $totalItem = count($itemList);
        $delivery = 0;

        if ($totalItem > $noOfItemsWhoDontHaveStocks) {
            // so delivery creation here
            $sqldel = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "`
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

            $soDeliveryCreationObj = queryInsert($sqldel);
            if ($soDeliveryCreationObj["status"] != "success") {
                return [
                    "status" => "warning",
                    "message" => "Delivery creation failed, try again!",
                    "soDeliveryCreationObj" => $soDeliveryCreationObj
                ];
            }
            $delivery++;
            $deliveryLastId = $soDeliveryCreationObj['insertedId'];
            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrailDelv = array();
            $auditTrailDelv['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrailDelv['basicDetail']['table_name'] = ERP_BRANCH_SALES_ORDER_DELIVERY;
            $auditTrailDelv['basicDetail']['column_name'] = 'so_delivery_id'; // Primary key column
            $auditTrailDelv['basicDetail']['document_id'] = $deliveryLastId;  // primary key
            $auditTrailDelv['basicDetail']['party_type'] = 'customer';
            $auditTrailDelv['basicDetail']['party_id'] = $customerId;
            $auditTrailDelv['basicDetail']['document_number'] = $deliveryNo;
            $auditTrailDelv['basicDetail']['action_code'] = $action_code;
            $auditTrailDelv['basicDetail']['action_referance'] = $soNumber;
            $auditTrailDelv['basicDetail']['action_title'] = 'Delivery Creation';  //Action comment
            $auditTrailDelv['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrailDelv['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrailDelv['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrailDelv['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrailDelv['basicDetail']['action_sqlQuery'] = base64_encode($sqldel);
            $auditTrailDelv['basicDetail']['others'] = '';
            $auditTrailDelv['basicDetail']['remark'] = '';

            $auditTrailDelv['action_data']['Sales Order Delivery Details']['delivery_no'] = $deliveryNo;
            $auditTrailDelv['action_data']['Sales Order Delivery Details']['so_number'] = $soNumber;
            $auditTrailDelv['action_data']['Sales Order Delivery Details']['customer_shipping_address'] = $customer_shipping_address;
            $auditTrailDelv['action_data']['Sales Order Delivery Details']['customer_billing_address'] = $customer_billing_address;
            $auditTrailDelv['action_data']['Sales Order Delivery Details']['so_id'] = $soId;
            $auditTrailDelv['action_data']['Sales Order Delivery Details']['customer_id'] = $customerId;
            $auditTrailDelv['action_data']['Sales Order Delivery Details']['delivery_date'] = formatDateORDateTime($deliveryCreationDate);
            $auditTrailDelv['action_data']['Sales Order Delivery Details']['so_delivery_posting_date'] = formatDateORDateTime($soDeliveryPostingDate);
            $auditTrailDelv['action_data']['Sales Order Delivery Details']['profit_center'] = $profitCenter;
            $auditTrailDelv['action_data']['Sales Order Delivery Details']['deliveryStatus'] = 'open';
            $auditTrailDelv['action_data']['Sales Order Delivery Details']['customer_po_no'] = $customerPO;
        }

        $getDeliveryNumber =  $deliveryNo;
        $itemTotalDiscount = 0;
        $itemTotalPrice = 0;
        $totalprice = 0;
        $itemTotalDiscountSum = 0;
        $itemTotalPriceSum = 0;
        $totalItems = 0;
        $totalProduction = 0;
        $totalPR = 0;
        foreach ($itemList as $oneItem) {

            $itemTotalPriceSum += $oneItem["itemTotalPrice"];
            $itemTotalDiscountSum += $oneItem["itemTotalDiscount"];

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

            if (isset($oneItem['itemreleasetype'])) {
                if ($oneItem["itemreleasetype"] == 'FIFO') {
                    $itemSellType = 'ASC';
                } else if ($oneItem["itemreleasetype"] == 'LIFO') {
                    $itemSellType = 'DESC';
                } else if ($oneItem["itemreleasetype"] == 'CUSTOM') {
                    $itemSellType = 'CUSTOM';
                    $batchselection = $oneItem['batchselection'];
                }
            } else {
                if ($oneItem["itemSellType"] == 'FIFO') {
                    $itemSellType = 'ASC';
                } else if ($oneItem["itemSellType"] == 'LIFO') {
                    $itemSellType = 'DESC';
                } else if ($oneItem["itemSellType"] == 'CUSTOM') {
                    //$itemSellType = 'ASC';
                }
            }


            // $itemTotalDiscount += $oneItem["itemTotalDiscount"];
            $totalprice += $itemTotalPrice;

            if ($oneItem['extraOrderCBox'] == 'on' && $oneItem['extraOrder'] > 0) {
                $extraOrder = $oneItem['extraOrder'];
                if ($oneItem['extraOrderType'] == 'production') {
                    //only production order insert
                    $proCode = "PRO" . date("Ym") . rand(100, 999);

                    $sqlProd = "INSERT INTO `" . ERP_PRODUCTION_ORDER . "`
                    SET 
                    `porCode`='$proCode',
                    `refNo`='$so_number',
                    `expectedDate`='$soDeliveryPostingDate',
                    `itemId`='" . $inventoryItemId . "',
                    `itemCode`='" . $itemCode . "',
                    `company_id`='$company_id',
                    `branch_id`='$branch_id',
                    `location_id`='" . $location_id . "',
                    `created_by`='$created_by',
                    `updated_by`='" . $updated_by . "',
                    `remainQty`='" . $extraOrder . "',
                    `qty`='$extraOrder'";
                    $productionOrderObj = queryInsert($sqlProd);
                    if ($productionOrderObj['status'] == 'success') {
                        $totalProduction++;
                        ///---------------------------------Audit Log Start---------------------
                        $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                        $auditTrail = array();
                        $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                        $auditTrail['basicDetail']['table_name'] = ERP_PRODUCTION_ORDER;
                        $auditTrail['basicDetail']['column_name'] = 'so_por_id'; // Primary key column
                        $auditTrail['basicDetail']['document_id'] = $productionOrderObj['insertedId'];  // primary key
                        $auditTrail['basicDetail']['party_type'] = 'customer';
                        $auditTrail['basicDetail']['party_id'] = $customerId;
                        $auditTrail['basicDetail']['document_number'] = $proCode;
                        $auditTrail['basicDetail']['action_code'] = $action_code;
                        $auditTrail['basicDetail']['action_referance'] = $soNumber;
                        $auditTrail['basicDetail']['action_title'] = 'Production Order Creation';  //Action comment
                        $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                        $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                        $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                        $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                        $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($sqlProd);
                        $auditTrail['basicDetail']['others'] = '';
                        $auditTrail['basicDetail']['remark'] = '';

                        $auditTrail['action_data']['Production Order Details'][$itemCode]['refNo'] = $so_number;
                        $auditTrail['action_data']['Production Order Details'][$itemCode]['expectedDate'] = $soDeliveryPostingDate;
                        $auditTrail['action_data']['Production Order Details'][$itemCode]['itemCode'] = $itemCode;
                        $auditTrail['action_data']['Production Order Details'][$itemCode]['created_by'] = getCreatedByUser($created_by);
                        $auditTrail['action_data']['Production Order Details'][$itemCode]['updated_by'] = getCreatedByUser($updated_by);
                        $auditTrail['action_data']['Production Order Details'][$itemCode]['qty'] = $extraOrder;

                        $auditTrailreturn = generateAuditTrail($auditTrail);

                        $return[$itemCode]['message2'] = "Production Order Generated Successfully";
                    } else {

                        $return[$itemCode]['message2'] = "Production Order Generate Failed";
                    }
                } else {
                    // $prCode = "PR" . date("Ym") . rand(100, 999);
                    $lastQuery = "SELECT * FROM " . ERP_BRANCH_PURCHASE_REQUEST . " WHERE `company_id` = '$company_id' AND `branch_id` = '$branch_id' AND `location_id` = '$location_id' ORDER BY `purchaseRequestId` DESC LIMIT 1";

                    $last = queryGet($lastQuery);

                    $lastRow = $last['data'] ?? "";

                    $lastPrId = $lastRow['prCode'] ?? "";

                    $prCode = getPRSerialNumber($lastPrId);
                    $pr_date = date('Y-m-d');
                    $sqlpr = "INSERT INTO `" . ERP_BRANCH_PURCHASE_REQUEST . "` 
                            SET
                                `prCode`='$prCode',
                                `company_id`='$company_id',
                                `branch_id`='$branch_id',
                                `location_id`='$location_id',
                                `expectedDate`='$soDeliveryPostingDate',
                                `pr_date`='$pr_date',
                                `pr_type`='material',
                                `refNo`='$so_number',
                                `pr_status`=9,
                                `status`='active',
                                `created_by`='$created_by',
                                `updated_by`='$updated_by' ";
                    $prOrderObj = queryInsert($sqlpr);
                    $lastID = $prOrderObj['insertedId'];
                    $sqlprItem = "INSERT `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "` 
                                SET 
                                    `company_id`='$company_id',
                                    `branch_id`='$branch_id',
                                    `location_id`='$location_id',
                                    `prId`='$lastID',
                                    `itemId`='$inventoryItemId',
                                    `itemCode`='$itemCode',
                                    `itemName`='$itemName',
                                    `itemQuantity`='$extraOrder',
                                    `remainingQty`='$extraOrder',
                                    `uom`='$itemUom',
                                    `itemPrice`='$unitPrice',
                                    `itemDiscount`='$totalDiscount'";
                    $prOrderItemObj = queryInsert($sqlprItem);
                    if ($prOrderItemObj['status'] = 'success') {
                        $totalPR++;
                        ///---------------------------------Audit Log Start---------------------
                        $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                        $auditTrailPR = array();
                        $auditTrailPR['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                        $auditTrailPR['basicDetail']['table_name'] = ERP_BRANCH_PURCHASE_REQUEST;
                        $auditTrailPR['basicDetail']['column_name'] = 'purchaseRequestId'; // Primary key column
                        $auditTrailPR['basicDetail']['document_id'] = $prOrderItemObj['insertedId'];  // primary key
                        $auditTrailPR['basicDetail']['party_type'] = 'customer';
                        $auditTrailPR['basicDetail']['party_id'] = $customerId;
                        $auditTrailPR['basicDetail']['document_number'] = $prCode;
                        $auditTrailPR['basicDetail']['action_code'] = $action_code;
                        $auditTrailPR['basicDetail']['action_referance'] = $soNumber;
                        $auditTrailPR['basicDetail']['action_title'] = 'Purchase Request Creation';  //Action comment
                        $auditTrailPR['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                        $auditTrailPR['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                        $auditTrailPR['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                        $auditTrailPR['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                        $auditTrailPR['basicDetail']['action_sqlQuery'] = base64_encode($sqlpr);
                        $auditTrailPR['basicDetail']['others'] = '';
                        $auditTrailPR['basicDetail']['remark'] = '';

                        $auditTrailPR['action_data']['Purchase Request Details']['prCode'] = $prCode;
                        $auditTrailPR['action_data']['Purchase Request Details']['pr_type'] = 'material';
                        $auditTrailPR['action_data']['Purchase Request Details']['refNo'] = $so_number;
                        $auditTrailPR['action_data']['Purchase Request Details']['description'] = '';
                        $auditTrailPR['action_data']['Purchase Request Details']['expectedDate'] = formatDateORDateTime($soDeliveryPostingDate);
                        $auditTrailPR['action_data']['Purchase Request Details']['pr_date'] = formatDateORDateTime($pr_date);
                        $auditTrailPR['action_data']['Purchase Request Details']['created_by'] = getCreatedByUser($created_by);
                        $auditTrailPR['action_data']['Purchase Request Details']['updated_by'] = getCreatedByUser($updated_by);

                        $auditTrailPR['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                        $auditTrailPR['action_data']['Item Details'][$itemCode]['itemQuantity'] = $extraOrder;
                        $auditTrailPR['action_data']['Item Details'][$itemCode]['uom'] = $itemUom;
                        $auditTrailPR['action_data']['Item Details'][$itemCode]['itemPrice'] = $unitPrice;
                        $auditTrailPR['action_data']['Item Details'][$itemCode]['itemDiscount'] = $totalDiscount;

                        $auditTrailreturn = generateAuditTrail($auditTrailPR);

                        $return[$itemCode]["message3"] = "Purchase Request Created Successfully";
                    } else {
                        $return[$itemCode]["message3"] = "Purchase Request Creation failed!";
                    }
                }
            }

            if ($itemQty > 0) {
                // $selStockLog = $this->deliveryCreateItemQty($inventoryItemId);
                if ($itemSellType != 'CUSTOM') {
                    $selStockLog = $this->itemQtyStockCheck($inventoryItemId, "'rmWhOpen', 'rmWhReserve', 'fgWhOpen'", $itemSellType, '', $soDeliveryPostingDate);
                    $itemOpenStocks = $selStockLog['sumOfBatches'];
                } else {
                    $filteredBatchSelection = [];

                    foreach ($batchselection as $key => $value) {
                        $explodes = explode('_', $key);
                        $logRef = $explodes[0];
                        $slocation = $explodes[1];

                        $keysval = $logRef . $slocation;

                        if (!empty($value)) {
                            $filteredBatchSelection[$keysval] = $value;
                        }
                    }

                    $keysString = "'" . implode("', '", array_keys($filteredBatchSelection)) . "'";


                    $selStockLog = $this->itemQtyStockCheck($inventoryItemId, "'rmWhOpen', 'rmWhReserve', 'fgWhOpen'", 'ASC', "$keysString", $soDeliveryPostingDate);

                    $itemOpenStocks = $selStockLog['sumOfBatches'];
                }

                // console($selStockLog);

                if ($itemOpenStocks > 0) {
                    // echo "Open Stocks";
                    $totalItems++;
                    //delivery items creation and update the stocks

                    $remainingitemQty = min($itemOpenStocks,  $itemQty);

                    $sql = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_DELIVERY_ITEMS . "`
                                            SET
                                            `so_delivery_id`='" . $deliveryLastId . "',
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
                                            `qty`='" . $remainingitemQty . "',
                                            `uom`='" . $itemUom . "'
                                ";
                    $deliveryItemsCreationsObj = queryInsert($sql);

                    if ($deliveryItemsCreationsObj["status"] == "success") {

                        $auditTrailDelv['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                        $auditTrailDelv['action_data']['Item Details'][$itemCode]['itemQuantity'] = $remainingitemQty;
                        $auditTrailDelv['action_data']['Item Details'][$itemCode]['uom'] = $itemUom;
                        $auditTrailDelv['action_data']['Item Details'][$itemCode]['itemPrice'] = $unitPrice;
                        $auditTrailDelv['action_data']['Item Details'][$itemCode]['itemDiscount'] = $totalDiscount;
                        queryUpdate("UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` 
                                    SET 
                                        `remainingQty`=$remainingitemQty,
                                        `deliveryStatus`='Delivery Created' 
                                    WHERE 
                                        so_delivery_id='" . $itemDeliveryDateId . "'");

                        // echo "imran59059";


                        foreach ($selStockLog['data'] as $stockLogKey => $logdata) {
                            if ($itemSellType == 'CUSTOM') {
                                // $explodes = explode('_', $logdata['logRef']);
                                // $logRef = $explodes[0];
                                $logRef = $logdata['logRef'];
                                $keysval = $logdata['logRefConcat'];
                                $usedQuantity = $filteredBatchSelection[$keysval];
                                $bornDate = $logdata['bornDate'];
                                $storage_location_id = $logdata['storage_location_id'];
                                $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                            } else {
                                if ($itemQty <= 0) {
                                    break;
                                }

                                $quantity = $logdata['itemQty'];
                                $usedQuantity = min($quantity,  $itemQty);
                                $itemQty -= $usedQuantity;
                                // $explodes = explode('_', $logdata['logRef']);
                                // $logRef = $explodes[0];

                                $logRef = $logdata['logRef'];
                                $bornDate = $logdata['bornDate'];
                                $storage_location_id = $logdata['storage_location_id'];
                                $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                            }

                            $getWHDetail = queryGet("SELECT storage_location_id,warehouse_id,storageLocationTypeSlug FROM `erp_storage_location` WHERE storage_location_id=" . $logdata['storage_location_id'] . " ")['data'];

                            $getfgWHReserveId = queryGet("SELECT storage_location_id,warehouse_id,storageLocationTypeSlug FROM `erp_storage_location` WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND warehouse_id=" . $getWHDetail['warehouse_id'] . " AND storageLocationTypeSlug='fgWhReserve' ")['data'];

                            $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                    SET 
                                                        companyId = '" . $company_id . "',
                                                        branchId = '" . $branch_id . "',
                                                        locationId = '" . $location_id . "',
                                                        storageLocationId = '" . $storage_location_id . "',
                                                        storageType ='" . $storageLocationTypeSlug . "',
                                                        itemId = '" . $inventoryItemId . "',
                                                        itemQty = '" . $usedQuantity * -1 . "',
                                                        itemUom = '" . $itemUom . "',
                                                        itemPrice = '" . $unitPrice . "',
                                                        refActivityName='DELIVERY',
                                                        logRef = '" . $logRef . "',
                                                        refNumber='" . $getDeliveryNumber . "',
                                                        bornDate='" . $bornDate . "',
                                                        postingDate='" . $soDeliveryPostingDate . "',
                                                        createdBy = '" . $created_by . "',
                                                        updatedBy = '" . $updated_by . "'";

                            $insStockreturn1 = queryInsert($insStockSummary1);

                            $insStockSummary2 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                        SET 
                                                        companyId = '" . $company_id . "',
                                                        branchId = '" . $branch_id . "',
                                                        locationId = '" . $location_id . "',
                                                        storageLocationId = '" . $getfgWHReserveId['storage_location_id'] . "',
                                                        storageType ='" . $getfgWHReserveId['storageLocationTypeSlug'] . "',
                                                        refActivityName='DELIVERY',
                                                        logRef = '" . $logRef . "',
                                                        refNumber='" . $getDeliveryNumber . "',
                                                        bornDate='" . $bornDate . "',
                                                        postingDate='" . $soDeliveryPostingDate . "',
                                                        itemId = '" . $inventoryItemId . "',
                                                        itemQty = '" . $usedQuantity . "',
                                                        itemUom = '" . $itemUom . "',
                                                        itemPrice = '" . $unitPrice . "',
                                                        createdBy = '" . $created_by . "',
                                                        updatedBy = '" . $updated_by . "'";
                            $insStockreturn2 = queryInsert($insStockSummary2);
                        }

                        $return['insStockreturn1'][] = $insStockreturn1;
                        $return['insStockreturn2'][] = $insStockreturn2;
                    }
                } else {
                    $flug++;
                    $return[$itemCode]["messageNot"] = "Item quantity In Open-----" . $itemOpenStocks;
                }
            } else {
                $flug++;
                $return[$itemCode]["message3"] = "Item quantity not found";
            }
        }
        if ($totalItems > 0) {
            if ($delivery > 0) {
                // update delivery table
                $sqlDeliveryUpdate = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "` 
                        SET
                            `totalItems`='" . $totalItems . "',
                            `totalDiscount`='" . $itemTotalDiscountSum . "',
                            `totalAmount`='" . $itemTotalPriceSum . "'
                        WHERE `so_delivery_id`='" . $deliveryLastId . "'";
                queryUpdate($sqlDeliveryUpdate);

                $auditTrailDelv['action_data']['Sales Order Delivery Details']['totalItems'] = $totalItem;
                $auditTrailDelv['action_data']['Sales Order Delivery Details']['totalDiscount'] = $itemTotalDiscount;
                $auditTrailDelv['action_data']['Sales Order Delivery Details']['totalAmount'] = $itemTotalPrice;

                $auditTrailDelvreturn = generateAuditTrail($auditTrailDelv);
            }


            $return["status"] = "success";
            $extramsg = '';
            if ($totalPR > 0 && $totalProduction > 0) {
                $extramsg = "$totalProduction Production Order and $totalPR Purchase Request Auto Generated Successfully.";
            } else if ($totalPR <= 0 && $totalProduction > 0) {
                $extramsg = "$totalProduction Production Order Auto Generated Successfully.";
            } else if ($totalPR > 0 && $totalProduction <= 0) {
                $extramsg = "$totalPR Purchase Request Auto Generated Successfully.";
            }

            if ($delivery > 0 &&  !empty($extramsg)) {
                $return["message"] = "Delivery Created." . $extramsg;
                $return["deliveryNo"] = $getDeliveryNumber;
            } else if ($delivery == 0 &&  empty($extramsg)) {
                $return["status"] = "warning";
                $return["message"] = "Delivery not created for qty issue.";
            } else if ($delivery > 0 &&  empty($extramsg)) {
                $return["message"] = "Delivery Created Successfully.";
                $return["deliveryNo"] = $getDeliveryNumber;
            } else {
                $return["message"] = $extramsg;
            }
        } else {
            $return["status"] = "success";
            if ($totalPR > 0 && $totalProduction > 0) {
                $return["message"] = "$totalProduction Production Order and $totalPR Purchase Request Auto Generated Successfully.";
            } else if ($totalPR <= 0 && $totalProduction > 0) {
                $return["message"] = "$totalProduction Production Order Auto Generated Successfully.";
            } else if ($totalPR > 0 && $totalProduction <= 0) {
                $return["message"] = "$totalPR Purchase Request Auto Generated Successfully.";
            } else {
                $return["status"] = "warning";
                // $return["message"] = "Delivery Created Failure!";
                $return["message"] = "Delivery was not created due to insufficient stock availability. Please proceed with production.";
            }
        }
        return $return;
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
        global $company_id;
        global $companyFunctionalities;

        $ins = "SELECT * FROM `" . ERP_COMPANY_FUNCTIONALITIES . "` WHERE company_id='" . $company_id . "' AND functionalities_id IN($companyFunctionalities) AND functionalities_status='active'";

        return queryGet($ins, true);
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

    // fetch COMPANY Details
    function fetchCompanyDetails()
    {
        global $company_id;
        $company = queryGet("SELECT * FROM `" . ERP_COMPANIES . "` WHERE company_id='$company_id'");
        return $company;
    }

    // fetch warehouse
    function fetchWarehouseDetails($id)
    {
        $warehouseSql = "SELECT * FROM `erp_storage_warehouse` WHERE warehouse_id='" . $id . "'";
        return queryGet($warehouseSql);
    }

    // fetch warehouse
    function fetchBatchListByStorageLocation($storageLocationCode, $itemId)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        $storageLocationSql = "SELECT temp_table2.warehouse_name,temp_table2.storage_location_name,temp_table2.logRef,SUM(temp_table2.open_) AS open_stock,SUM(temp_table2.reserve) AS reserve_stock,temp_table2.itemUom,temp_table2.created_at FROM (SELECT
        temp_table1.warehouse_name,temp_table1.storage_location_name,temp_table1.logRef,
            CASE
            WHEN temp_table1.storage_location_storage_type='Open' THEN temp_table1.itemQty
            END AS open_,
            CASE
            WHEN temp_table1.storage_location_storage_type='Reserve' THEN temp_table1.itemQty
            END AS reserve,
            temp_table1.itemUom,
            temp_table1.created_at
        FROM
        (SELECT warehouse.warehouse_name,loc.storage_location_name,loc.storage_location_storage_type,log.logRef,log.itemQty,log.itemUom,loc.created_at FROM erp_inventory_stocks_log AS log LEFT JOIN erp_storage_location AS loc ON log.storageLocationId=loc.storage_location_id LEFT JOIN erp_storage_warehouse AS warehouse ON loc.warehouse_id=warehouse.warehouse_id WHERE log.companyId=$company_id AND log.branchId=$branch_id AND log.locationId=$location_id AND log.status='active' AND loc.storage_location_code='$storageLocationCode' AND log.createdAt BETWEEN '2023-06-01' AND '2023-06-30' AND log.itemId=$itemId) AS temp_table1) AS temp_table2 GROUP BY temp_table2.warehouse_name,temp_table2.storage_location_name,temp_table2.logRef,temp_table2.itemUom,temp_table2.created_at";
        return queryGet($storageLocationSql, true);
    }

    // fetch warehouse
    function fetchStatusMaster($id)
    {
        $statusMaster = "SELECT * FROM `" . ERP_STATUS_MASTER . "` WHERE status_id=$id";
        return queryGet($statusMaster, true);
    }

    // fetch location
    function fetchLocationListByWarehouse($id)
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $locationSql = "SELECT storage_location_code,storage_location_name,storage_location_type FROM erp_storage_location WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND warehouse_id=$id";
        return queryGet($locationSql, true);
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
                                `updated_by` = '" . $updated_by . "' ";

        $dpgiResponce = queryInsert($deliSche);
        if ($dpgiResponce['status'] == 'success') {
            $lastId = $dpgiResponce['insertedId'];
            $listItem = $POST['listItem'];
            $itemTotalDiscount = 0;
            $itemTotalPrice = 0;
            $totalItems = count($listItem);

            $getPgiNumber = $pgiNo;

            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrailPgi = array();
            $auditTrailPgi['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrailPgi['basicDetail']['table_name'] = ERP_BRANCH_SALES_ORDER_DELIVERY_PGI;
            $auditTrailPgi['basicDetail']['column_name'] = 'so_delivery_pgi_id'; // Primary key column
            $auditTrailPgi['basicDetail']['document_id'] = $lastId;  // primary key
            $auditTrailPgi['basicDetail']['party_type'] = 'customer';
            $auditTrailPgi['basicDetail']['party_id'] = $customerId;
            $auditTrailPgi['basicDetail']['document_number'] = $pgiNo;
            $auditTrailPgi['basicDetail']['action_code'] = $action_code;
            $auditTrailPgi['basicDetail']['action_referance'] = $soNumber;
            $auditTrailPgi['basicDetail']['action_title'] = 'PGI Creation';  //Action comment
            $auditTrailPgi['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrailPgi['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrailPgi['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrailPgi['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrailPgi['basicDetail']['action_sqlQuery'] = base64_encode($deliSche);
            $auditTrailPgi['basicDetail']['others'] = '';
            $auditTrailPgi['basicDetail']['remark'] = '';

            $auditTrailPgi['action_data']['Pgi Details']['pgi_no'] = $pgiNo;
            $auditTrailPgi['action_data']['Pgi Details']['delivery_no'] = $deliveryNo;
            $auditTrailPgi['action_data']['Pgi Details']['so_number'] = $soNumber;
            $auditTrailPgi['action_data']['Pgi Details']['customer_id'] = $customerId;
            $auditTrailPgi['action_data']['Pgi Details']['customer_billing_address'] = $customer_billing_address;
            $auditTrailPgi['action_data']['Pgi Details']['customer_shipping_address'] = $customer_shipping_address;
            $auditTrailPgi['action_data']['Pgi Details']['pgiDate'] = $pgiDate;
            $auditTrailPgi['action_data']['Pgi Details']['profit_center'] = $profitCenter;
            $auditTrailPgi['action_data']['Pgi Details']['pgiStatus'] = 'open';
            $auditTrailPgi['action_data']['Pgi Details']['invoiceStatus'] = 9;
            $auditTrailPgi['action_data']['Pgi Details']['customer_po_no'] = $customerPO;
            $auditTrailPgi['action_data']['Pgi Details']['created_by'] = getCreatedByUser($created_by);
            $auditTrailPgi['action_data']['Pgi Details']['updated_by'] = getCreatedByUser($updated_by);


            foreach ($listItem as $key => $item) {

                if ($item['enterQty'] > 0) {
                    // $selStockLog = $this->itemQtyStockCheck($item['inventoryItemId'], "'fgWhReserve'", ' ASC', $deliveryNo);
                    // $itemOpenStocks = $selStockLog['sumOfBatches'];
                    // console($item['enterQty'] . "<=" . $itemOpenStocks . "chack stock");
                    if ($item['enterQty'] <= $item['batchNo']) {
                        $enterQty = $item['enterQty'];
                        $itemTotalDiscount += $item["itemTotalDiscount"];
                        $itemTotalPrice    += $item["itemTotalPrice"];

                        $itemName = addslashes($item['itemName']);
                        $itemDesc = addslashes($item['itemDesc']);

                        $inspgiItm = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_DELIVERY_ITEMS_PGI . "`
                                        SET
                                        `inventory_item_id`='" . $item['inventoryItemId'] . "',
                                        `so_delivery_pgi_id`='$lastId',
                                        `lineNo`='" . $item['lineNo'] . "',
                                        `itemCode`='" . $item['itemCode'] . "',
                                        `itemName`='" . $itemName . "',
                                        `itemDesc`='" . $itemDesc . "',
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
                        $pgiitemresponse = queryInsert($inspgiItm);
                        if ($pgiitemresponse['status'] == 'success') {
                            $lastID1 = $pgiitemresponse['insertedId'];

                            $auditTrailPgi['action_data']['Item Details'][$item['itemCode']]['lineNo'] = $item['lineNo'];
                            $auditTrailPgi['action_data']['Item Details'][$item['itemCode']]['itemCode'] = $item['itemCode'];
                            $auditTrailPgi['action_data']['Item Details'][$item['itemCode']]['itemName'] = $item['itemName'];
                            $auditTrailPgi['action_data']['Item Details'][$item['itemCode']]['itemDesc'] = $item['itemDesc'];
                            $auditTrailPgi['action_data']['Item Details'][$item['itemCode']]['delivery_date'] = $item['deliveryDate'];
                            $auditTrailPgi['action_data']['Item Details'][$item['itemCode']]['qty'] = $item['itemQty'];
                            $auditTrailPgi['action_data']['Item Details'][$item['itemCode']]['enterQty'] = $item['enterQty'];
                            $auditTrailPgi['action_data']['Item Details'][$item['itemCode']]['uom'] = $item['uom'];
                            $auditTrailPgi['action_data']['Item Details'][$item['itemCode']]['hsnCode'] = $item['hsnCode'];
                            $auditTrailPgi['action_data']['Item Details'][$item['itemCode']]['tax'] = $item['tax'];
                            $auditTrailPgi['action_data']['Item Details'][$item['itemCode']]['totalTax'] = $item['totalTax'];
                            $auditTrailPgi['action_data']['Item Details'][$item['itemCode']]['tolerance'] = $item['tolerance'];
                            $auditTrailPgi['action_data']['Item Details'][$item['itemCode']]['totalDiscount'] = $item['totalDiscount'];
                            $auditTrailPgi['action_data']['Item Details'][$item['itemCode']]['totalDiscountAmt'] = $item['itemTotalDiscount'];
                            $auditTrailPgi['action_data']['Item Details'][$item['itemCode']]['unitPrice'] = $item['unitPrice'];
                            $auditTrailPgi['action_data']['Item Details'][$item['itemCode']]['totalPrice'] = $item['itemTotalPrice'];


                            queryUpdate("UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` 
                                        SET 
                                            `deliveryStatus`='pgi' 
                                        WHERE 
                                            so_delivery_id='" . $item['deliveryDate'] . "'");

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
                                    `deliveryStatus`='PGI Created' 
                                WHERE `so_delivery_item_id`='" . $item['so_delivery_item_id'] . "'";
                            $dbCon->query($deliItem);

                            $upd = "UPDATE `" . ERP_INVENTORY_STOCKS_SUMMARY . "` SET `fgWhReserve`= `fgWhReserve`-" . $item['itemQty'] . " , `fgMktOpen`='" . ($fgMktOpen + $item['itemQty']) . "' WHERE itemId='" . $item['inventoryItemId'] . "'";
                            $updateItemStocksObj = queryUpdate($upd);

                            $selStockLog = $this->itemQtyStockCheck($item['inventoryItemId'], "'fgWhReserve'", "DESC", "", $pgiDate);
                            // console($selStockLog);

                            foreach ($selStockLog['data'] as $stockLogKey => $logdata) {
                                if ($enterQty <= 0) {
                                    break;
                                }
                                $quantity = $logdata['itemQty'];
                                $usedQuantity = min($quantity, $enterQty);
                                $enterQty -= $usedQuantity;

                                $getWHDetail = queryGet("SELECT storage_location_id,warehouse_id,storageLocationTypeSlug FROM `erp_storage_location` WHERE storage_location_id=" . $logdata['storage_location_id'] . " ")['data'];

                                $getfgMktOpenId = queryGet("SELECT storage_location_id,warehouse_id,storageLocationTypeSlug FROM `erp_storage_location` WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND warehouse_id=" . $getWHDetail['warehouse_id'] . " AND storageLocationTypeSlug='fgMktOpen' ")['data'];

                                $logRef = $logdata['logRef'];
                                $bornDate = $logdata['bornDate'];

                                $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                    SET 
                                                        companyId = '" . $company_id . "',
                                                        branchId = '" . $branch_id . "',
                                                        locationId = '" . $location_id . "',
                                                        storageLocationId = '" . $getWHDetail['storage_location_id'] . "',
                                                        storageType ='" . $getWHDetail['storageLocationTypeSlug'] . "',
                                                        itemId = '" . $item['inventoryItemId'] . "',
                                                        itemQty = '" . $usedQuantity * -1 . "',
                                                        itemUom = '" . $item['uom'] . "',
                                                        itemPrice = '" . $item['unitPrice'] . "',
                                                        refActivityName='PGI',
                                                        logRef = '" . $logRef . "',
                                                        refNumber='" . $pgiNo . "',
                                                        bornDate='" . $bornDate . "',
                                                        postingDate='" . $pgiDate . "',
                                                        createdBy = '" . $created_by . "',
                                                        updatedBy = '" . $updated_by . "'";

                                $insStockreturn1 = queryInsert($insStockSummary1);

                                $insStockSummary2 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                        SET 
                                                        companyId = '" . $company_id . "',
                                                        branchId = '" . $branch_id . "',
                                                        locationId = '" . $location_id . "',
                                                        storageLocationId = '" . $getfgMktOpenId['storage_location_id'] . "',
                                                        refActivityName='PGI',
                                                        logRef = '" . $logRef . "',
                                                        refNumber='" . $pgiNo . "',
                                                        bornDate='" . $bornDate . "',
                                                        postingDate='" . $pgiDate . "',
                                                        storageType ='" . $getfgMktOpenId['storageLocationTypeSlug'] . "',
                                                        itemId = '" . $item['inventoryItemId'] . "',
                                                        itemQty = '" . $usedQuantity . "',
                                                        itemUom = '" . $item['uom'] . "',
                                                        itemPrice = '" . $item['unitPrice'] . "',
                                                        createdBy = '" . $created_by . "',
                                                        updatedBy = '" . $updated_by . "'";
                                $insStockreturn2 = queryInsert($insStockSummary2);


                                $return['insStockreturn1'][] = $insStockreturn1;
                                $return['insStockreturn2'][] = $insStockreturn2;
                            }


                            $returnData['status'] = "success";
                            $returnData['message'] = "PGI Created Successfully";
                            $returnData['pgiNo'] = $getPgiNumber;
                            $returnData['pgiList'] = $dpgiResponce;
                            $returnData['pgiListItem'] = $pgiitemresponse;
                        } else {
                            $returnData['status'] = "warning";
                            $returnData['message'] = " PGI not Created";
                        }
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "Quentity not available - " . $item['itemCode'];
                    }
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Quentity required - " . $item['itemCode'];
                }
            }


            $updateDeli = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "` 
                            SET 
                                `totalItems`='" . $totalItems . "',
                                `totalDiscount`='" . $itemTotalDiscount . "',
                                `totalAmount`='" . $itemTotalPrice . "'
                            WHERE so_delivery_pgi_id=" . $lastId . "";
            queryUpdate($updateDeli);


            $auditTrailPgi['action_data']['Pgi Details']['totalItems'] = $totalItems;
            $auditTrailPgi['action_data']['Pgi Details']['totalDiscount'] = $itemTotalDiscount;
            $auditTrailPgi['action_data']['Pgi Details']['totalAmount'] = $itemTotalPrice;

            $auditTrailreturn = generateAuditTrail($auditTrailPgi);

            // $returnData['status'] = "success";
            // $returnData['message'] = "PGI Created Successfully";
            // $returnData['pgiNo'] = $getPgiNumber;
            // $returnData['pgiList'] = $dpgiResponce;
            // $returnData['pgiListItem'] = $pgiitemresponse;
            // $returnData['deliSche'] = $deliSche;
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "somthing went wrong! 2";
        }
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
        // // exit;
        $collectPayment = $POST['paymentDetails']['collectPayment'];
        $customerId = $POST['paymentDetails']['customerId'];
        $totalDueAmt = $POST['paymentDetails']['totalDueAmt'];

        $customerDetailsObj = queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customerId'")['data'];

        $customer_name = $customerDetailsObj['customer_name'];
        $customer_authorised_person_email = $customerDetailsObj['customer_authorised_person_email'];
        $customer_authorised_person_phone = $customerDetailsObj['customer_authorised_person_phone'];


        $allTotalAmt = 0;
        $roundOffValue = 0;
        // echo $collectPayment . '--' . $roundOffValue;
        if (isset($POST['round_off_checkbox']) && !empty($POST['paymentDetails']['roundOffValue'])) {
            $collectPayment = $POST['paymentDetails']['adjustedCollectAmount'];
            $roundOffValue = $POST['paymentDetails']['roundOffValue'];
        } else {
            $collectPayment = $POST['paymentDetails']['collectPayment'];
        }
        // echo $collectPayment . '--' . $roundOffValue;
        $totalInvAmt = $POST['paymentDetails']['totalInvAmt'];
        $remaningAmt = $POST['paymentDetails']['remaningAmt'];
        $bankId = $POST['paymentDetails']['bankId'] ?? 0;
        $bankDetails = get_acc_bank_cash_accounts_details($bankId);
        $POST['paymentDetails']['bank'] = $bankDetails;
        $POST['paymentDetails']['accCode'] = $bankDetails['acc_code'];
        $POST['paymentDetails']['accName'] = $bankDetails['bank_name'];
        $advancedPayAmt = $POST['paymentDetails']['advancedPayAmt'];
        $paymentCollectType = "";
        if ($POST['paymentDetails']['paymentCollectType'] == "collect") {
            $paymentCollectType = "collect";
        } elseif ($POST['paymentDetails']['paymentCollectType'] == "adjust") {
            $paymentCollectType = "adjust";
        }
        $documentDate = $POST['paymentDetails']['documentDate'];
        $tranactionId = addslashes($POST['paymentDetails']['tnxDocNo']);
        $postingDate = $POST['paymentDetails']['postingDate'];

        $collectionCode = date('dmY') . rand(1111, 9999) . rand(1111, 9999);
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

        $insCollect = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_PAYMENTS . "` 
                    SET
                        `collectionCode`='$collectionCode',
                        `adjusted_amount`='$roundOffValue',
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
                        `remarks`='collection',
                        `created_by`='$created_by',
                        `updated_by`='$updated_by'
        ";
        $insCollectObj = queryInsert($insCollect);
        if ($insCollectObj['status'] = 'success') {
            $paymentId = $insCollectObj['insertedId'];

            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrailDelv = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_SALES_ORDER_PAYMENTS;
            $auditTrail['basicDetail']['column_name'] = 'payment_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $paymentId;  // primary key
            $auditTrail['basicDetail']['party_type'] = 'customer';
            $auditTrail['basicDetail']['party_id'] = $customerId;
            $auditTrail['basicDetail']['document_number'] = $collectionCode;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = $paymentCollectType;  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insCollect);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['Collection Details']['transactionId'] = $tranactionId;
            $auditTrail['action_data']['Collection Details']['amount'] = $collectPayment;
            $auditTrail['action_data']['Collection Details']['Account_Detail'] = $bankDetails['bank_name'] . ' (' . $bankDetails['acc_code'] . ')';
            $auditTrail['action_data']['Collection Details']['paymentCollectType'] = $paymentCollectType;
            $auditTrail['action_data']['Collection Details']['documentDate'] = formatDateORDateTime($documentDate);
            $auditTrail['action_data']['Collection Details']['postingDate'] = formatDateORDateTime($postingDate);
            $auditTrail['action_data']['Collection Details']['remarks'] = 'collection';
            $auditTrail['action_data']['Collection Details']['created_by'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['Collection Details']['updated_by'] = getCreatedByUser($updated_by);

            $paymentInvItems = $POST['paymentInvoiceDetails'];

            $paymentInvItems = array_filter($paymentInvItems, function ($item) {
                return $item['recAmt'] !== '' && $item['recAmt'] !== 0;
            });

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
                $invAmt = $one['invAmt'];
                $recAmt = $one['recAmt'];
                $dueAmt = $one['dueAmt'];
                // $calDueAmt = $dueAmt - $recAmt;
                if (isset($recAmt) && $recAmt != null) {
                    $invoiceConcadinate .= $one['invoiceNo'] . ' |';
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
                            `remarks`='Collection For- " . $one['invoiceNo'] . "',
                            `created_by`='$created_by',
                            `updated_by`='$updated_by'
                        ";
                    $querinvpaylog = queryInsert($insItem);
                    // console($querinvpaylog);

                    if ($querinvpaylog['status'] == 'success') {


                        $auditTrail['action_data']['Collection Log Details'][$collectionCode]['invoiceNo'] = $one['invoiceNo'];
                        $auditTrail['action_data']['Collection Log Details'][$collectionCode]['type'] = 'collect';
                        $auditTrail['action_data']['Collection Log Details'][$collectionCode]['amount'] = $recAmt;
                        $auditTrail['action_data']['Collection Log Details'][$collectionCode]['remarks'] = 'Collection For' . $one['invoiceNo'];


                        $returnData['status'] = "success";
                        $returnData['message'] = "Inserted Successfully";
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
                        }
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "Somthing went wrong";
                        $returnData['$insCollect'] = $insCollect;
                        $returnData['sql'] = $insItem;
                    }
                } else {
                    continue;
                }
            }
            //-----------------------------Collection ACC Start----------------

            $invoiceConcadinate = $invoiceConcadinate != '' ? $invoiceConcadinate : 'Advance';
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
                "paymentInvItems" => $paymentInvItems,
                "roundOffValue" => $roundOffValue
            ];
            //console($ivPostingInputData);
            $collectionObj = $this->collectionAccountingPosting($collectionInputData, "Collection", $paymentId);
            // console($collectionObj);
            if ($collectionObj['status'] == 'success') {

                $JournalId = $collectionObj['journalId'];
                $sqlcollection = "UPDATE `" . ERP_BRANCH_SALES_ORDER_PAYMENTS . "`
                                SET
                                    `journal_id`=$JournalId 
                                WHERE `payment_id`='$paymentId'  ";
                queryUpdate($sqlcollection);
            }
            //-----------------------------Collection ACC END ----------------

            $returnData['collectionObj'] = $collectionObj;

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
                                    `invoice_id`='0',
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
                                    `invoice_id`='0',
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
                                    `invoice_id`='0',
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


            global $current_userName;
            $whatsapparray = [];
            $whatsapparray['templatename'] = 'after_settlement_of_invoices_msg';
            $whatsapparray['to'] = $customer_authorised_person_phone;
            $whatsapparray['customername'] = $customer_name;
            $whatsapparray['quickcontact'] = null;
            $whatsapparray['current_userName'] = $current_userName;

            SendMessageByWhatsappTemplate($whatsapparray);


            $auditTrail['action_data']['Collection Log Details'][$collectionCode]['invoiceNo'] = 'NULL';
            $auditTrail['action_data']['Collection Log Details'][$collectionCode]['type'] = 'collect';
            $auditTrail['action_data']['Collection Log Details'][$collectionCode]['amount'] = $adv_amt;
            $auditTrail['action_data']['Collection Log Details'][$collectionCode]['remarks'] = 'Advance Collection';

            $auditTrailreturn = generateAuditTrail($auditTrail);
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
        global $company_id;
        global $branch_id;
        global $location_id;

        $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_PAYMENTS . "` WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND `status`!='deleted' ORDER BY payment_id DESC";
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
        global $company_id;
        global $branch_id;
        global $location_id;
        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND payment_id='$paymentId' AND  status!='deleted'";
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

    // add invoice (from SO)  
    function insertBranchInvoiceFromSo($POST)
    {
        global $dbCon;
        $returnData = [];

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        $IvNoByVerientresponse = getInvoiceNumberByVerient($POST['iv_varient']);
        // $invNo = $POST['invoiceDetails']['invNo'] ?? 0;
        $invNo = $IvNoByVerientresponse['iv_number'];
        $invoice_no_serialized = $IvNoByVerientresponse['iv_number_array'];
        $pgi_id = $POST['invoiceDetails']['pgiId'] ?? 0;
        $pgi_no = $POST['invoiceDetails']['pgiNo'] ?? 0;
        $creditPeriod = $POST['invoiceDetails']['creditPeriod'];
        $delivery_no = $POST['invoiceDetails']['delivery_no'];
        $so_number = $POST['invoiceDetails']['so_number'];
        $customer_id = $POST['invoiceDetails']['customer_id'] ?? 0;
        $invoice_date = $POST['invoiceDetails']['invoiceDate'];
        $poNumber = $POST['invoiceDetails']['poNumber'];
        $poDate = $POST['invoiceDetails']['poDate'];
        $kamId = $POST['invoiceDetails']['kamId'] ?? 0;
        $shipToLastInsertedId = $POST['shipToLastInsertedId'];
        $profit_center = $POST['invoiceDetails']['profit_center'];
        $subTotal = $POST['invoiceDetails']['subTotal'] ?? 0;
        $totalTaxAmt = $POST['invoiceDetails']['totalTaxAmt'] ?? 0;
        $cgst = $POST['invoiceDetails']['cgst'] ?? 0;
        $sgst = $POST['invoiceDetails']['sgst'] ?? 0;
        $igst = $POST['invoiceDetails']['igst'] ?? 0;
        $tcs = $POST['invoiceDetails']['tcs'] ?? 0;
        $totalDiscount = $POST['invoiceDetails']['totalDiscount'] ?? 0;
        $allTotalAmt = $POST['invoiceDetails']['allTotalAmt'] ?? 0;
        $totalItems = $POST['invoiceDetails']['totalItems'] ?? 0;
        $customer_billing_address = $POST['invoiceDetails']['customer_billing_address'];
        $customer_shipping_address = $POST['invoiceDetails']['customer_shipping_address'];
        $bankId = $POST['bankId'] ?? 0;

        $curr_rate = 1;
        if ($POST['curr_rate']) {
            $curr_rate = $POST['curr_rate'];
        }

        $currency = explode('≊', $POST['currency']);
        $currencyId = $currency[0] ?? 0;
        $currencyName = $currency[2];

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
        $customer_authorised_person_phone = $customerDetailsObj['customer_authorised_person_phone'];

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

        $companyBankAccDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='$bankId' ")['data'];
        $companyBankSerialize = serialize($companyBankAccDetailsObj);

        $company_name = $companyDetailsObj['company_name'];

        $invInsert = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                        SET 
                            `invoice_no`='$invNo',
                            `invoice_no_serialized`='$invoice_no_serialized',
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `customer_id`='$customer_id',
                            `credit_period`='$creditPeriod',
                            `delivery_no`='$delivery_no',
                            `so_number`='$so_number',
                            `invoice_date`='$invoice_date',
                            `po_number`='$poNumber',
                            `po_date`='$poDate',
                            `shipToLastInsertedId`='$shipToLastInsertedId',
                            `totalItems`='$totalItems',
                            `sub_total_amt`='$subTotal',
                            `conversion_rate`='$curr_rate',
                             `currency_id`='$currencyId',
                             `currency_name`='$currencyName',
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
                            `company_bank_details`='$companyBankSerialize',
                            `company_gstin`='$branchGstin',
                            `customer_gstin`='$customerGstin',
                            `customer_billing_address`='$customer_billing_address',
                            `customer_shipping_address`='$customer_shipping_address',
                            `created_by`='$created_by',
                            `updated_by`='$updated_by',
                            `type`='so',
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

            $StUpd = 'UPDATE `' . ERP_BRANCH_SALES_ORDER . '` SET `approvalStatus`= 10 WHERE so_number="' . $so_number . '"';
            $updateStatus = queryUpdate($StUpd);


            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_SALES_ORDER_INVOICES;
            $auditTrail['basicDetail']['column_name'] = 'so_invoice_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $invId;  // primary key
            $auditTrail['basicDetail']['party_type'] = 'customer';
            $auditTrail['basicDetail']['party_id'] = $customer_id;
            $auditTrail['basicDetail']['document_number'] = $invNo;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Invoice Creation ';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($invInsert);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['Invoice Details']['invoice_no'] = $invNo;
            $auditTrail['action_data']['Invoice Details']['credit_period'] = $creditPeriod;
            $auditTrail['action_data']['Invoice Details']['invoice_date'] = $invoice_date;
            $auditTrail['action_data']['Invoice Details']['totalItems'] = $totalItems;
            $auditTrail['action_data']['Invoice Details']['sub_total_amt'] = $subTotal;
            $auditTrail['action_data']['Invoice Details']['totalDiscount'] = $totalDiscount;
            $auditTrail['action_data']['Invoice Details']['cgst'] = $cgst;
            $auditTrail['action_data']['Invoice Details']['sgst'] = $sgst;
            $auditTrail['action_data']['Invoice Details']['igst'] = $igst;
            $auditTrail['action_data']['Invoice Details']['kamId'] = $kamId;
            $auditTrail['action_data']['Invoice Details']['total_tax_amt'] = $totalTaxAmt;
            $auditTrail['action_data']['Invoice Details']['all_total_amt'] = $allTotalAmt;
            $auditTrail['action_data']['Invoice Details']['due_amount'] = $allTotalAmt;
            $auditTrail['action_data']['Invoice Details']['customerDetails'] = $customerDetailsSerialize;
            $auditTrail['action_data']['Invoice Details']['companyDetails'] = $companySerialize;
            $auditTrail['action_data']['Invoice Details']['company_bank_details'] = $companyBankSerialize;
            $auditTrail['action_data']['Invoice Details']['company_gstin'] = $branchGstin;
            $auditTrail['action_data']['Invoice Details']['customer_gstin'] = $customerGstin;
            $auditTrail['action_data']['Invoice Details']['customer_billing_address'] = $customer_billing_address;
            $auditTrail['action_data']['Invoice Details']['customer_shipping_address'] = $customer_shipping_address;



            // update delivery pgi table
            // $upd = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "` 
            //             SET
            //                 invoiceStatus=1 WHERE so_delivery_pgi_id='" . $pgi_id . "' ";
            // $dbCon->query($upd);
            $flug = 0;

            foreach ($listItem as $itemKey => $item) {
                $lineNo = $item['lineNo'];
                $inventory_item_id = $item['inventory_item_id'] ?? 0;
                $itemCode = $item['itemCode'];
                $itemName = addslashes($item['itemName']);
                $itemDesc = addslashes($item['itemDesc']);
                $hsnCode = $item['hsnCode'];
                $tax = $item['tax'] ?? 0;
                $totalTax = $item['totalTax'] ?? 0;
                $tolerance = $item['tolerance'] ?? 0;
                if (!empty(trim($tolerance))) {
                    $tolerance = $tolerance;
                } else {
                    $tolerance = 0;
                }
                $totalDiscount = $item['totalDiscount'] ?? 0;
                $totalDiscountAmt = $item['totalDiscountAmt'] ?? 0;
                $goodsMainPrice = $item['goodsMainPrice'] ?? 0;
                $unitPrice = $item['unitPrice'] ?? 0;
                $qty = $item['qty'] ?? 0;
                $basePrice = ($qty * $unitPrice);
                $uom = $item['uom'];
                $totalPrice = $item['totalPrice'] ?? 0;
                $delivery_date = $item['delivery_date'] ?? 0;
                $enterQty = $item['enterQty'] ?? 0;
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
                            `tolerance`='" . $tolerance . "',
                            `goodsMainPrice`='" . $goodsMainPrice . "',
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
                    $auditTrail['action_data']['Item Details'][$itemCode]['so_Item_id'] = $invId;
                    $auditTrail['action_data']['Item Details'][$itemCode]['lineNo'] = $lineNo;
                    $auditTrail['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                    $auditTrail['action_data']['Item Details'][$itemCode]['itemName'] = $itemName;
                    $auditTrail['action_data']['Item Details'][$itemCode]['itemDesc'] = $itemDesc;
                    $auditTrail['action_data']['Item Details'][$itemCode]['delivery_date'] = $delivery_date;
                    $auditTrail['action_data']['Item Details'][$itemCode]['qty'] = $qty;
                    $auditTrail['action_data']['Item Details'][$itemCode]['uom'] = $uom;
                    $auditTrail['action_data']['Item Details'][$itemCode]['tolerance'] = $tolerance;
                    $auditTrail['action_data']['Item Details'][$itemCode]['unitPrice'] = $unitPrice;
                    $auditTrail['action_data']['Item Details'][$itemCode]['basePrice'] = $basePrice;
                    $auditTrail['action_data']['Item Details'][$itemCode]['hsnCode'] = $hsnCode;
                    $auditTrail['action_data']['Item Details'][$itemCode]['tax'] = $tax;
                    $auditTrail['action_data']['Item Details'][$itemCode]['totalTax'] = $totalTax;
                    $auditTrail['action_data']['Item Details'][$itemCode]['totalDiscount'] = $totalDiscount;
                    $auditTrail['action_data']['Item Details'][$itemCode]['totalDiscountAmt'] = $totalDiscountAmt;
                    $auditTrail['action_data']['Item Details'][$itemCode]['totalPrice'] = $totalPrice;
                    $return['status'] = "success";
                    $return['message'] = "Invoice Created Successfully";
                    $return['invoiceNo'] = $getInvNumber;

                    $upd = "UPDATE `" . ERP_INVENTORY_STOCKS_SUMMARY . "` SET `fgMktOpen`= `fgMktOpen`-" . $qty . " WHERE itemId='" . $item['inventory_item_id'] . "'";
                    $updateItemStocksObj = queryUpdate($upd);
                    ///---------------------------------Audit Log Start---------------------
                    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                    $auditTrailSummry = array();
                    $auditTrailSummry['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                    $auditTrailSummry['basicDetail']['table_name'] = ERP_INVENTORY_ITEMS;
                    $auditTrailSummry['basicDetail']['column_name'] = 'itemId'; // Primary key column
                    $auditTrailSummry['basicDetail']['document_id'] = $item['inventory_item_id'];  //     primary key
                    $auditTrailSummry['basicDetail']['document_number'] = $itemCode;
                    $auditTrailSummry['basicDetail']['action_code'] = $action_code;
                    $auditTrailSummry['basicDetail']['action_referance'] = $invNo;
                    $auditTrailSummry['basicDetail']['action_title'] = 'Item Stock added';  //Action comment
                    $auditTrailSummry['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                    $auditTrailSummry['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                    $auditTrailSummry['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                    $auditTrailSummry['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                    $auditTrailSummry['basicDetail']['action_sqlQuery'] = base64_encode($upd);
                    $auditTrailSummry['basicDetail']['others'] = '';
                    $auditTrailSummry['basicDetail']['remark'] = '';

                    $auditTrailSummry['action_data']['Summary']['fgMktOpen'] = $qty * -1;

                    $auditTrailreturn = generateAuditTrail($auditTrailSummry);
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "somthing went wrong! 3";
                    $flug++;
                }
            }
            if ($flug == 0) {

                // calculate days to date
                $duedate = date("Y-m-d", strtotime("+" . $creditPeriod . " days"));
                $invoicelink = BASE_URL . 'branch/location/branch-so-invoice-view.php?inv_id=' . $encodeInv_id;

                $invoicelinkWhatsapp = 'branch-so-invoice-view.php?inv_id=' . $encodeInv_id;

                $to = $customer_authorised_person_email;
                $sub = 'Invoice ' . $invNo . ' for Your Recent Purchase';
                $msg = '
                <img src="' . BASE_URL . 'public/mailstatus/mail-status-so-invoice.php?invId=' . $invId . '&mailstatus=2" style="height:1px; width:1px">
                <div>
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
                <a href="' . BASE_URL . 'branch/location/classic-view/invoice-preview-print.php?invoice_id=' . $encodeInv_id . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View Invoice</a>
                
                </p>
                </div>';
                $mail =  SendMailByMySMTPmailTemplate($to, $sub, $msg, null, $customerCode, 'salesInvoice', $invId, $invNo);


                global $current_userName;
                $whatsapparray = [];
                $whatsapparray['templatename'] = 'invoice_sent_msg';
                $whatsapparray['to'] = $customer_authorised_person_phone;
                $whatsapparray['customername'] = $customer_name;
                $whatsapparray['invoiceno'] = $invNo;
                $whatsapparray['invoicelink'] = $invoicelinkWhatsapp;
                $whatsapparray['quickcontact'] = null;
                $whatsapparray['current_userName'] = $current_userName;

                SendMessageByWhatsappTemplate($whatsapparray);

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

                $auditTrail['action_data']['Mail Details']['Status'] = 'Mail send Successfully';

                $auditTrailreturn = generateAuditTrail($auditTrail);


                $itemQtyMin = '-' . $qty;
                $insStockSummary2 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                        SET 
                            companyId = '" . $company_id . "',
                            branchId = '" . $branch_id . "',
                            locationId = '" . $location_id . "',
                            storageLocationId = 9,
                            storageType = 'FG-MKT',
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
                        "postingDate" => $invoice_date, // current date
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
                            "postingDate" => $invoice_date, // current date
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
                    // console($SOivPostingObj);

                    if ($ivPostingObj['status'] == 'success') {
                        $ivJournalId = $SOivPostingObj['journalId'];
                        $sqliv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
                                    SET
                                        `pgi_journal_id`=$pgiJournalId,
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
                    $returnData['journal_sql'] = $SOivPostingObj;
                    $returnData['journal_sql2'] = $ivPostingObj;
                }
                //************************END ACCOUNTING ******************** */
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong! 01";
            $returnData['sql'] = $invInsert;
        }
        return $returnData;
    }

    // add invoice (from SO)  
    function subscriptionInvoice()
    {
        global $dbCon;
        $returnData = [];

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;


        $todayDate = date("Y-m-d");
        $subscriptionInvoiceData = queryGet("SELECT * FROM `" . ERP_BRANCH_SUBSCRIPTION_INVOICE . "` WHERE next_trigger_date='$todayDate'", true);

        if ($subscriptionInvoiceData['status'] == "success") {
            foreach ($subscriptionInvoiceData['data'] as $one) {
                if ($todayDate <= $one['end_on'] || $one['end_on'] == "") {
                    $soDetailsObj = queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE so_id='" . $one['so_id'] . "'");
                    $soDetails = $soDetailsObj['data'];

                    $soItemDetailsObj = queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` WHERE so_id='" . $soDetails['so_id'] . "'", true);
                    $POST['listItem'] = $soItemDetailsObj['data'];

                    $POST['invoiceDetails']['invNo'] = "INV" . rand(0000, 9999);    //Ramen questions
                    $POST['invoiceDetails']['customer_id'] = $soDetails['customer_id'];
                    $POST['invoiceDetails']['creditPeriod'] = $soDetails['credit_period'];
                    $POST['invoiceDetails']['invoiceDate'] = $one['next_trigger_date'];
                    $POST['invoiceDetails']['so_number'] = $soDetails['so_number'];
                    $POST['invoiceDetails']['profit_center'] = $soDetails['profit_center'];
                    $POST['invoiceDetails']['kamId'] = $soDetails['kamId'];

                    $POST['curr_rate'] = $soDetails['conversion_rate'];
                    $POST['currency'] = $soDetails['currency_name'];

                    $POST['invoiceDetails']['totalItems'] = $soDetails['totalItems'] ?? 0;
                    $POST['invoiceDetails']['totalDiscount'] = $soDetails['totalDiscount'] ?? 0;
                    $POST['invoiceDetails']['subTotal'] = $soDetails['subTotal'] ?? 0;
                    $POST['invoiceDetails']['totalTaxAmt'] = $soDetails['totalTaxAmt'] ?? 0;
                    $POST['invoiceDetails']['cgst'] = $soDetails['cgst'] ?? 0;
                    $POST['invoiceDetails']['sgst'] = $soDetails['sgst'] ?? 0;
                    $POST['invoiceDetails']['igst'] = $soDetails['igst'] ?? 0;
                    $POST['invoiceDetails']['allTotalAmt'] = $soDetails['totalAmount'] ?? 0;
                    $POST['invoiceDetails']['customer_billing_address'] = $soDetails['billingAddress'];
                    $POST['invoiceDetails']['customer_shipping_address'] = $soDetails['shippingAddress'];

                    $invpostreturn = $this->insertBranchInvoiceFromSo($POST);
                    if ($invpostreturn['status'] == "success") {
                        $days = $one['repeat_every'];
                        $nextMonth = date('Y-m-d', strtotime("+$days days"));

                        $subs = "UPDATE `" . ERP_BRANCH_SUBSCRIPTION_INVOICE . "` SET `next_trigger_date`='$nextMonth' WHERE so_id='" . $soDetails['so_id'] . "'";
                        $data = queryUpdate($subs);
                        $returnData['message'] = "invoice sent successfully";
                        // return [
                        //     "status" => "success",
                        //     "message" => "success",
                        //     "sql" => $subs
                        // ];
                    } else {
                        $returnData['message'] = "somthing went wrong!";
                        // return [
                        //     "status" => "warning",
                        //     "message" => "warning",
                        //     "post" => $POST,
                        //     "resp" => $invpostreturn
                        // ];
                    }
                } else {
                    $returnData['message'] = "No subscribtion found in the record!";
                    // return [
                    //     "status" => "warning",
                    //     "message" => "No subscribtion found in the record!"
                    // ];
                }
            }
        } else {
            return [
                "status" => "warning",
                "message" => "No subscribtion date found in the record!",
                "sql" => $subscriptionInvoiceData,
            ];
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

        $IvNoByVerientresponse = getInvoiceNumberByVerient($POST['iv_varient']);
        // $invNo = $POST['invoiceDetails']['invNo'] ?? 0;
        $invNo = $IvNoByVerientresponse['iv_number'];
        $invoice_no_serialized = $IvNoByVerientresponse['iv_number_array'];

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
        $shipToLastInsertedId = $POST['shipToLastInsertedId'];
        $igst = $POST['invoiceDetails']['igst'] ?? 0;
        $tcs = $POST['invoiceDetails']['tcs'] ?? 0;
        $totalDiscount = $POST['invoiceDetails']['totalDiscount'] ?? 0;
        $allTotalAmt = $POST['invoiceDetails']['allTotalAmt'] ?? 0;
        $totalItems = $POST['invoiceDetails']['totalItems'] ?? 0;
        $customer_billing_address = $POST['invoiceDetails']['customer_billing_address'];
        $customer_shipping_address = $POST['invoiceDetails']['customer_shipping_address'];
        $bankId = $POST['bankId'];

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
        $customer_authorised_person_phone = $customerDetailsObj['customer_authorised_person_phone'];

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

        $companyBankAccDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='$bankId' ")['data'];
        $companyBankSerialize = serialize($companyBankAccDetailsObj);

        $company_name = $companyDetailsObj['company_name'];

        $invInsert = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                        SET 
                            `invoice_no`='$invNo',
                            `invoice_no_serialized`='$invoice_no_serialized',
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
                            `shipToLastInsertedId`='$shipToLastInsertedId',
                            `kamId`='$kamId',
                            `profit_center`='$profit_center',
                            `igst`='$igst',
                            `total_tax_amt`='$totalTaxAmt',
                            `all_total_amt`='$allTotalAmt',
                            `due_amount`='$allTotalAmt',
                            `customerDetails`='$customerDetailsSerialize',
                            `companyDetails`='$companySerialize',
                            `company_bank_details`='$companyBankSerialize',
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


            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_SALES_ORDER_INVOICES;
            $auditTrail['basicDetail']['column_name'] = 'so_invoice_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $invId;  // primary key
            $auditTrail['basicDetail']['party_type'] = 'customer';
            $auditTrail['basicDetail']['party_id'] = $customer_id;
            $auditTrail['basicDetail']['document_number'] = $invNo;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Invoice Creation ';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($invInsert);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';


            $auditTrail['action_data']['Invoice Details']['invoice_no'] = $invNo;
            $auditTrail['action_data']['Invoice Details']['credit_period'] = $creditPeriod;
            $auditTrail['action_data']['Invoice Details']['invoice_date'] = $invoice_date;
            $auditTrail['action_data']['Invoice Details']['totalItems'] = $totalItems;
            $auditTrail['action_data']['Invoice Details']['sub_total_amt'] = $subTotal;
            $auditTrail['action_data']['Invoice Details']['totalDiscount'] = $totalDiscount;
            $auditTrail['action_data']['Invoice Details']['cgst'] = $cgst;
            $auditTrail['action_data']['Invoice Details']['sgst'] = $sgst;
            $auditTrail['action_data']['Invoice Details']['igst'] = $igst;
            $auditTrail['action_data']['Invoice Details']['kamId'] = $kamId;
            $auditTrail['action_data']['Invoice Details']['total_tax_amt'] = $totalTaxAmt;
            $auditTrail['action_data']['Invoice Details']['all_total_amt'] = $allTotalAmt;
            $auditTrail['action_data']['Invoice Details']['due_amount'] = $allTotalAmt;
            $auditTrail['action_data']['Invoice Details']['customerDetails'] = $customerDetailsSerialize;
            $auditTrail['action_data']['Invoice Details']['companyDetails'] = $companySerialize;
            $auditTrail['action_data']['Invoice Details']['company_bank_details'] = $companyBankSerialize;
            $auditTrail['action_data']['Invoice Details']['company_gstin'] = $branchGstin;
            $auditTrail['action_data']['Invoice Details']['customer_gstin'] = $customerGstin;
            $auditTrail['action_data']['Invoice Details']['customer_billing_address'] = $customer_billing_address;
            $auditTrail['action_data']['Invoice Details']['customer_shipping_address'] = $customer_shipping_address;

            // update delivery pgi table
            $upd = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "` 
                        SET
                            invoiceStatus=1 WHERE so_delivery_pgi_id='" . $pgi_id . "' ";
            $dbCon->query($upd);

            $StUpd = 'UPDATE `' . ERP_BRANCH_SALES_ORDER . '` SET `approvalStatus`= 10 WHERE so_number="' . $so_number . '"';
            $updateStatus = queryUpdate($StUpd);

            $flug = 0;

            foreach ($listItem as $itemKey => $item) {
                $lineNo = $item['lineNo'];
                $inventory_item_id = $item['inventory_item_id'];
                $itemCode = $item['itemCode'];
                $itemName = addslashes($item['itemName']);
                $itemDesc = addslashes($item['itemDesc']);
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
                $goodsMainPrice = $item['goodsMainPrice'] ?? 0;
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
                            `goodsMainPrice`='" . $goodsMainPrice . "',
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

                    $auditTrail['action_data']['Item Details'][$itemCode]['so_Item_id'] = $invId;
                    $auditTrail['action_data']['Item Details'][$itemCode]['lineNo'] = $lineNo;
                    $auditTrail['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                    $auditTrail['action_data']['Item Details'][$itemCode]['itemName'] = $itemName;
                    $auditTrail['action_data']['Item Details'][$itemCode]['itemDesc'] = $itemDesc;
                    $auditTrail['action_data']['Item Details'][$itemCode]['delivery_date'] = $delivery_date;
                    $auditTrail['action_data']['Item Details'][$itemCode]['qty'] = $qty;
                    $auditTrail['action_data']['Item Details'][$itemCode]['uom'] = $uom;
                    $auditTrail['action_data']['Item Details'][$itemCode]['tolerance'] = $tolerance;
                    $auditTrail['action_data']['Item Details'][$itemCode]['unitPrice'] = $unitPrice;
                    $auditTrail['action_data']['Item Details'][$itemCode]['basePrice'] = $basePrice;
                    $auditTrail['action_data']['Item Details'][$itemCode]['hsnCode'] = $hsnCode;
                    $auditTrail['action_data']['Item Details'][$itemCode]['tax'] = $tax;
                    $auditTrail['action_data']['Item Details'][$itemCode]['totalTax'] = $totalTax;
                    $auditTrail['action_data']['Item Details'][$itemCode]['totalDiscount'] = $totalDiscount;
                    $auditTrail['action_data']['Item Details'][$itemCode]['totalDiscountAmt'] = $totalDiscountAmt;
                    $auditTrail['action_data']['Item Details'][$itemCode]['totalPrice'] = $totalPrice;

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
                $invoicelink = BASE_URL . 'branch/location/branch-so-invoice-view.php?inv_id=' . $encodeInv_id;

                $invoicelinkWhatsapp = 'branch-so-invoice-view.php?inv_id=' . $encodeInv_id;

                $to = $customer_authorised_person_email;
                $sub = 'Invoice ' . $invNo . ' for Your Recent Purchase';
                $msg = '
                <img src="' . BASE_URL . 'public/mailstatus/mail-status-so-invoice.php?invId=' . $invId . '&mailstatus=2" style="height:1px; width:1px">
                <div>
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
                <a href="' . BASE_URL . 'branch/location/classic-view/invoice-preview-print.php?invoice_id=' . $encodeInv_id . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View Invoice</a>
                
                </p>
                </div>';
                $mail =  SendMailByMySMTPmailTemplate($to, $sub, $msg, null, $customerCode, 'salesInvoice', $invId, $invNo);

                global $current_userName;
                $whatsapparray = [];
                $whatsapparray['templatename'] = 'invoice_sent_msg';
                $whatsapparray['to'] = $customer_authorised_person_phone;
                $whatsapparray['customername'] = $customer_name;
                $whatsapparray['invoiceno'] = $invNo;
                $whatsapparray['invoicelink'] = $invoicelinkWhatsapp;
                $whatsapparray['quickcontact'] = null;
                $whatsapparray['current_userName'] = $current_userName;

                SendMessageByWhatsappTemplate($whatsapparray);

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


                $auditTrail['action_data']['Mail Details']['Status'] = 'Mail send Successfully';

                $auditTrailreturn = generateAuditTrail($auditTrail);

                $itemQtyMin = '-' . $qty;
                $insStockSummary2 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                        SET 
                            companyId = '" . $company_id . "',
                            branchId = '" . $branch_id . "',
                            locationId = '" . $location_id . "',
                            storageLocationId = 9,
                            storageType = 'FG-MKT',
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
                        "postingDate" => $invoice_date, // current date
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
                            "postingDate" => $invoice_date, // current date
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
                                    `pgi_journal_id`=$pgiJournalId,
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
    // edit invoice
    function editBranchDirectInvoice($POST, $FILES)
    {
        $invoice_id = $POST['invoice_id'];
        $kamId = $POST['kamId'];
        $bankId = $POST['bankId'];
        $profitCenter = $POST['profitCenter'];
        $creditPeriod = $POST['creditPeriod'];
        $remarks = $POST['extra_remark'];
        $declaration_note = $POST['declaration_note'];

        // Upload the attachment file to the specified directory and retrieve the file path.📂📂📂📂📂
        $attachmentObj = uploadFile($FILES['attachment'], COMP_STORAGE_DIR . "/others/");

        $bankSql = "SELECT * FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id=$bankId";
        $bankDetails = queryGet($bankSql)['data'];

        $serializeBank = serialize($bankDetails);

        $sql = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                SET 
                    `credit_period`='$creditPeriod',
                    `kamId`='$kamId',
                    `profit_center`='$profitCenter',
                    `declaration_note`='$declaration_note',
                    `company_bank_details`='$serializeBank',
                    `remarks`='$remarks'
                WHERE `so_invoice_id`=$invoice_id";
        $invUpdate = queryUpdate($sql);

        // insert attachment
        if ($attachmentObj['status'] == 'success') {
            $name = $attachmentObj['data'];
            $type = $FILES['attachment']['type'];
            $size = $FILES['attachment']['size'];
            $path = COMP_STORAGE_URL . '/others/' . $attachmentObj['data'];

            $insertAttachmentSql = "UPDATE `" . ERP_ATTACH_DOCUMENTS . "`
                SET
                    `file_name`='" . $name . "',
                    `file_path`='" . $path . "',
                    `file_type`='" . $type . "',
                    `file_size`='" . $size . "',
                    `refName`='invoice-creation',
                WHERE `ref_no`='$inv_id'
            ";
            $insertAttachment = queryInsert($insertAttachmentSql);
        }
        return $invUpdate;
    }

    // add invoice 
    function insertBranchDirectInvoice($POST, $FILES = null)
    {
        global $dbCon;
        $returnData = [];

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;


        $customerId = $POST['customerId'] ?? 0;
        $billingAddress = cleanUpString(addslashes($POST['billingAddress']));
        $shippingAddress = cleanUpString(addslashes($POST['shippingAddress']));
        $creditPeriod = $POST['creditPeriod'];
        $invoice_date = $POST['invoiceDate'];
        $invoiceTime = $POST['invoiceTime'];
        $declaration_note = addslashes($POST['declaration_note']);
        $billing_address_id = $POST['billing_address_id'] ?? 0;
        $shipping_address_id = $POST['shipping_address_id'] ?? 0;
        $profitCenter = $POST['profitCenter'];
        $kamId = $POST['kamId'] ?? 0;
        $so_id = $POST['so_id'];
        $remarks = addslashes($POST['extra_remark']);
        $customerType = null;
        if (isset($POST['walkInCustomerCheckbox'])) {
            $customerType = "walkin";
            $customerId = 0;
        }

        $companyConfigId = 0;
        if (isset($POST['companyConfigId']) && $POST['companyConfigId'] != 0) {
            $companyConfigId = $POST['companyConfigId'];
        }

        $iv_varient = $POST['iv_varient'];
        $shipToLastInsertedId = $POST['shipToLastInsertedId'] ?? 0;
        $bankId = $POST['bankId'];
        $compInvoiceType = $POST['compInvoiceType'];
        $placeOfSupply = $POST['placeOfSupply'];
        $customerGstinCode = $POST['customerGstinCode'];
        $quotationId = base64_decode($POST['quotationId']) ?? 0;
        $pgi_to_invoice = base64_decode($POST['pgi_to_invoice']) ?? 0;
        $so_to_invoice = base64_decode($POST['so_to_invoice']) ?? 0;
        $ivType = $POST['ivType'];

        $curr_rate = $POST['curr_rate'];
        $currency = explode('≊', $POST['currency']);
        $currencyId = $currency[0];
        $currencyName = $currency[2];

        $subTotal = str_replace(',', '', $POST['grandSubTotalAmtInp']) ?? 0;
        $totalDiscount = str_replace(',', '', $POST['grandTotalDiscountAmtInp']) ?? 0;
        $totalTaxAmt = str_replace(',', '', $POST['grandTaxAmtInp']) ?? 0;

        // Upload the attachment file to the specified directory and retrieve the file path.📂📂📂📂📂
        $attachmentObj = uploadFile($FILES['attachment'], COMP_STORAGE_DIR . "/others/");
        // uploadFile($file = [], $dir = "", $allowedExtensions = [], $maxSize = 0, $minSize = 0)

        $allTotalAmt = 0;
        $roundOffValue = 0;
        if ($POST['round_off_checkbox'] == 1 || !empty($POST['roundOffValue'])) {
            // $allTotalAmt = $POST['adjustedTotalAmount'] ?? 0;
            $allTotalAmt = $POST['paymentDetails']['adjustedCollectAmount'] ?? 0;
            $roundOffValue = $POST['paymentDetails']['roundOffValue'] ?? 0;
        } else {
            $roundOffValue = 0;
            $allTotalAmt = str_replace(',', '', $POST['grandTotalAmtInp']) ?? 0;
        }

        $totalItems = $POST['invoiceDetails']['totalItems'];
        $company_logo = $POST['companyDetails']['company_logo'];
        $gstin = $POST['companyDetails']['gstin'];
        $address = $POST['companyDetails']['address'];
        $signature = $POST['companyDetails']['signature'];
        $footerNote = $POST['companyDetails']['footerNote'];

        $customerName = addslashes($POST['customerDetails']['name']);
        $customerGstin = $POST['customerDetails']['gstin'];
        $customerPhone = $POST['customerDetails']['phone'];
        $customerEmail = $POST['customerDetails']['email'];
        $customerAddress = $POST['customerDetails']['address'];

        $invNo = '';
        if (isset($POST['repostInvoiceNo']) && !empty($POST['repostInvoiceNo'])) {
            $invNo = $POST['repostInvoiceNo'];
        }

        // fetch customer details
        $customerDetailsObj = queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,customer_code,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customerId'")['data'];
        $customer_authorised_person_email = $customerDetailsObj['customer_authorised_person_email'];
        $customer_authorised_person_phone = $customerDetailsObj['customer_authorised_person_phone'];
        $customer_name = addslashes($customerDetailsObj['customer_name']);

        $customerDetailsSerialize = serialize($customerDetailsObj);
        $customerCode = $customerDetailsObj["customer_code"] ?? 0;
        $customerParentGlId = $customerDetailsObj["parentGlId"] ?? 0;
        $customerName = addslashes($customerDetailsObj['customer_name']);
        $customer_Gst = $customerDetailsObj['customer_gstin'];

        // fetch company details
        $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
        $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
        $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
        $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
        $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
        $arrMarge = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);
        $companySerialize = serialize($arrMarge);
        $companySerialize = str_replace(["\r", "\n"], '', $companySerialize);

        $companyBankAccDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='$bankId' ")['data'];
        $companyBankSerialize = serialize($companyBankAccDetailsObj);

        $branch_Gst = $branchDetailsObj['branch_gstin'];

        $companyGstCode = substr($branch_Gst, 0, 2);
        $customerGstCode = substr($customer_Gst, 0, 2);

        $cgst = 0;
        $sgst = 0;
        $igst = 0;
        $tcs = 0;

        $gstAmt = 0;
        if ($compInvoiceType == "R" || $compInvoiceType == "SEWP") {
            if ($customerGstinCode != "") {
                if ($companyGstCode == $customerGstCode) {
                    $gstAmt = str_replace(',', '', $totalTaxAmt / 2);
                    $cgst = str_replace(',', '', $gstAmt);
                    $sgst = str_replace(',', '', $gstAmt);
                } else {
                    $igst = str_replace(',', '', $totalTaxAmt);
                }
            } else {
                if ($companyGstCode == $placeOfSupply) {
                    $gstAmt = str_replace(',', '', $totalTaxAmt / 2);
                    $cgst = str_replace(',', '', $gstAmt);
                    $sgst = str_replace(',', '', $gstAmt);
                } else {
                    $igst = str_replace(',', '', $totalTaxAmt);
                }
            }
        }
        // console('$companyGstCode, $customerGstCode, $cgst, $sgst, $igst, $totalTaxAmt, $placeOfSupply');
        // console($companyGstCode, $customerGstCode, $cgst, $sgst, $igst, $totalTaxAmt, $placeOfSupply);

        $invInsert = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                         SET 
                             `invoice_no`='$invNo',
                             `invoice_no_serialized`='$invoice_no_serialized',
                             `company_id`='$company_id',
                             `branch_id`='$branch_id',
                             `location_id`='$location_id',
                             `type`='$ivType',
                             `credit_period`='$creditPeriod',
                             `customer_id`='$customerId',
                             `kamId`='$kamId',
                             `invoice_date`='$invoice_date',
                             `invoice_time`='$invoiceTime',
                             `sub_total_amt`='$subTotal',
                             `profit_center`='$profitCenter',
                             `totalDiscount`='$totalDiscount',
                             `all_total_amt`='$allTotalAmt',
                             `due_amount`='$allTotalAmt',
                             `adjusted_amount`='$roundOffValue',
                             `conversion_rate`='$curr_rate',
                             `currency_id`='$currencyId',
                             `currency_name`='$currencyName',
                             `total_tax_amt`='$totalTaxAmt',
                             `cgst`='$cgst',
                             `sgst`='$sgst',
                             `igst`='$igst',
                             `billing_address_id`='$billing_address_id',
                             `shipping_address_id`='$shipping_address_id',
                             `shipToLastInsertedId`='$shipToLastInsertedId',
                             `customerDetails`='$customerDetailsSerialize',
                             `customer_gstin`='" . $customer_Gst . "',
                             `company_gstin`='" . $branch_Gst . "',
                             `companyDetails`='$companySerialize',
                             `companyConfigId`='$companyConfigId',
                             `company_bank_details`='$companyBankSerialize',
                             `customer_billing_address`='$billingAddress',
                             `customer_shipping_address`='$shippingAddress',
                             `placeOfSupply`='$placeOfSupply',
                             `compInvoiceType`='$compInvoiceType',
                             `declaration_note`='$declaration_note',
                             `remarks`='$remarks',
                             `customerType`='$customerType',
                             `created_by`='$created_by',
                             `updated_by`='$updated_by',
                             `invoiceStatus`='1'
         ";

        // console($invInsert);
        if ($dbCon->query($invInsert)) {
            $returnData['lastID'] = $dbCon->insert_id;
            $inv_id = $dbCon->insert_id;
            $encodeInv_id = base64_encode($inv_id);

            if ($POST['walkInCustomerCheckbox'] == true) {
                if ($POST['walkInCustomerName'] != "" && $POST['walkInCustomerMobile'] != "") {
                    $walkInCustomerName = $POST['walkInCustomerName'] ?? '';
                    $walkInCustomerMobile = $POST['walkInCustomerMobile'] ?? '';

                    $insertWalkInCustomer = "INSERT INTO `" . ERP_WALK_IN_INVOICES . "`
                        SET
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `invoice_id`='$inv_id',
                            `customer_name`='$walkInCustomerName',
                            `customer_phone`='$walkInCustomerMobile'
                    ";

                    $insertWalkInCustomerObj = queryInsert($insertWalkInCustomer);
                }
            }

            if (!isset($POST['repostInvoiceNo'])) {



                // added to manual inv no 

                $invoiceNumberType = $_POST['invoiceNumberType'];
                $invoice_no_serialized = "";


                if ($invoiceNumberType == "manual") {
                    $invNo = $_POST['ivnumberManual'];
                    $invoice_no_serialized = "";
                } else {

                    $IvNoByVerientresponse = getInvoiceNumberByVerient($POST['iv_varient']);
                    $invNo = $IvNoByVerientresponse['iv_number'];
                    $invoice_no_serialized = $IvNoByVerientresponse['iv_number_array'];
                }

                $updateInv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                            SET 
                            `invoice_no`='$invNo',
                            `invoice_no_serialized`='$invoice_no_serialized'
                         WHERE so_invoice_id='$inv_id'";
                queryUpdate($updateInv);
            } else {
                $invNo = $POST['repostInvoiceNo'];
                $repostInvoiceId = $POST['repostInvoiceId'];
                $updateInv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                            SET
                            `invoice_no`='$invNo'
                         WHERE so_invoice_id='$inv_id'";
                queryUpdate($updateInv);

                $updateInv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                            SET  
                            `status`='reposted'
                         WHERE so_invoice_id='$repostInvoiceId'";
                queryUpdate($updateInv);
            }

            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_SALES_ORDER_INVOICES;
            $auditTrail['basicDetail']['column_name'] = 'so_invoice_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $inv_id;  // primary key
            $auditTrail['basicDetail']['party_type'] = 'customer';
            $auditTrail['basicDetail']['party_id'] = $customerId;
            $auditTrail['basicDetail']['document_number'] = $invNo;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Invoice Creation ';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($invInsert);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';


            $auditTrail['action_data']['Invoice Details']['invoice_no'] = $invNo;
            $auditTrail['action_data']['Invoice Details']['credit_period'] = $creditPeriod;
            $auditTrail['action_data']['Invoice Details']['invoice_date'] = $invoice_date;
            $auditTrail['action_data']['Invoice Details']['totalItems'] = $totalItems;
            $auditTrail['action_data']['Invoice Details']['sub_total_amt'] = $subTotal;
            $auditTrail['action_data']['Invoice Details']['totalDiscount'] = $totalDiscount;
            $auditTrail['action_data']['Invoice Details']['cgst'] = $cgst;
            $auditTrail['action_data']['Invoice Details']['sgst'] = $sgst;
            $auditTrail['action_data']['Invoice Details']['igst'] = $igst;
            $auditTrail['action_data']['Invoice Details']['kamId'] = $kamId;
            $auditTrail['action_data']['Invoice Details']['total_tax_amt'] = $totalTaxAmt;
            $auditTrail['action_data']['Invoice Details']['all_total_amt'] = $allTotalAmt;
            $auditTrail['action_data']['Invoice Details']['due_amount'] = $allTotalAmt;
            $auditTrail['action_data']['Invoice Details']['customerDetails'] = $customerDetailsSerialize;
            $auditTrail['action_data']['Invoice Details']['companyDetails'] = $companySerialize;
            $auditTrail['action_data']['Invoice Details']['company_bank_details'] = $companyBankSerialize;
            $auditTrail['action_data']['Invoice Details']['company_gstin'] = $branch_Gst;
            $auditTrail['action_data']['Invoice Details']['customer_gstin'] = $customer_Gst;
            $auditTrail['action_data']['Invoice Details']['customer_billing_address'] = $billingAddress;
            $auditTrail['action_data']['Invoice Details']['customer_shipping_address'] = $shippingAddress;

            // insert attachment
            if ($attachmentObj['status'] == 'success') {
                $name = $attachmentObj['data'];
                $type = $FILES['attachment']['type'];
                $size = $FILES['attachment']['size'];
                $path = COMP_STORAGE_URL . '/others/' . $attachmentObj['data'];

                $insertAttachmentSql = "INSERT INTO `" . ERP_ATTACH_DOCUMENTS . "`
                        SET
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `file_name`='" . $name . "',
                            `file_path`='" . $path . "',
                            `file_type`='" . $type . "',
                            `file_size`='" . $size . "',
                            `refName`='invoice-creation',
                            `ref_no`='$inv_id'
                ";
                $insertAttachment = queryInsert($insertAttachmentSql);
            }

            $listItem = $POST['listItem'];
            $invId = $returnData['lastID'];
            $flug = 0;

            $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE so_invoice_id='$invId'";
            $getInvNumber =  queryGet($sql)['data']['invoice_no'];
            $invTotalItems = 0;
            $pgiitem = [];
            foreach ($listItem as $item) {

                if ($item['goodsType'] != 5) {
                    $pgiitem[] = $item;
                }

                $invTotalItems++;
                $lineNo = $item['lineNo'];
                $itemId = $item['itemId'];
                $invStatus = $item['invStatus'];
                $itemCode = $item['itemCode'];
                $goodsType = $item['goodsType'];
                $itemName = addslashes($item['itemName']);
                $itemDesc = addslashes($item['itemDesc']);
                $itemRemarks = addslashes($item['itemRemarks']) ?? '';
                $hsnCode = $item['hsnCode'];
                $tax = 0;
                $totalTax = 0;
                if ($compInvoiceType == "R" || $compInvoiceType == "SEWP") {
                    $tax = $item['tax'];
                    $totalTax = str_replace(',', '', $item['itemTotalTax1']) ?? 0;
                }
                $tolerance = $item['tolerance'] ?? 0;
                $totalDiscount = $item['totalDiscount'] ?? 0;
                $totalDiscountAmt = str_replace(',', '', $item['itemTotalDiscount1']) ?? 0;
                $unitPrice = str_replace(',', '', $item['unitPrice']) ?? 0;
                $baseAmount = str_replace(',', '', $item['baseAmount']) ? str_replace(',', '', $item['baseAmount']) : 0;
                $qty = $item['qty'] ?? 0;
                $invoiceQty = $item['invoiceQty'] ?? 0;
                $remainingQty = $item['remainingQty'] ?? 0;
                $uom = $item['uom'];
                $totalPrice = str_replace(',', '', $item['totalPrice']) ?? 0;
                $delivery_date = $item['delivery_date'];
                $enterQty = $item['enterQty'];

                $stockQty = $item['stockQty'];
                $explodeStockQty = $stockQty;
                if (isset($item['itemreleasetype'])) {
                    if ($item["itemreleasetype"] == 'FIFO') {
                        $itemSellType = 'ASC';
                    } else if ($item["itemreleasetype"] == 'LIFO') {
                        $itemSellType = 'DESC';
                    } else if ($item["itemreleasetype"] == 'CUSTOM') {
                        $itemSellType = 'CUSTOM';
                        $batchselection = $item['batchselection'];
                    }
                } else {
                    if ($item["itemSellType"] == 'FIFO') {
                        $itemSellType = 'ASC';
                    } else if ($item["itemSellType"] == 'LIFO') {
                        $itemSellType = 'DESC';
                    } else if ($item["itemSellType"] == 'CUSTOM') {
                        //$itemSellType = 'ASC';
                    }
                }

                $pgiNo = $_POST['pgiCode'];

                if ($ivType == "pgi_to_invoice") {
                    $selStockLog = $this->itemQtyStockCheck($itemId, "'fgMktOpen'", "ASC", "", $invoice_date);
                    $itemOpenStocks = $selStockLog['sumOfBatches'];
                } else {
                    if ($itemSellType != 'CUSTOM') {
                        $selStockLog = $this->itemQtyStockCheck($itemId, "'rmWhOpen', 'fgWhOpen'", $itemSellType, '', $invoice_date);
                        $itemOpenStocks = $selStockLog['sumOfBatches'];
                    } else {
                        // echo $itemCode;

                        $filteredBatchSelection = [];

                        foreach ($batchselection as $key => $value) {
                            $explodes = explode('_', $key);
                            $logRef = $explodes[0];
                            $slocation = $explodes[1];

                            $keysval = $logRef . $slocation;

                            if (!empty($value)) {
                                $filteredBatchSelection[$keysval] = $value;
                            }
                        }

                        $keysString = "'" . implode("', '", array_keys($filteredBatchSelection)) . "'";


                        $selStockLog = $this->itemQtyStockCheck($itemId, "'rmWhOpen', 'fgWhOpen'", 'ASC', "$keysString", $invoice_date);
                        // console($selStockLog);
                        $itemOpenStocks = $selStockLog['sumOfBatches'];
                    }
                }
                if ($goodsType == "5") {
                    $invItem1 = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICE_ITEMS . "`
                    SET
                    `so_invoice_id`='$invId',
                    `inventory_item_id`='$itemId',
                    `lineNo`='" . $lineNo . "',
                    `itemCode`='" . $itemCode . "',
                    `itemName`='" . $itemName . "',
                    `itemRemarks`='" . $itemRemarks . "',
                    `itemDesc`='" . $itemDesc . "',
                    `delivery_date`='" . $delivery_date . "',
                    `qty`='" . $qty . "',
                    `invoiceQty`='" . $invoiceQty . "',
                    `remainingQty`='" . $remainingQty . "',
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
                    `totalPrice`='" . $totalPrice . "'";

                    $itemIns = queryInsert($invItem1);
                    if ($itemIns['status'] == 'success') {
                        $return['status'] = "success";
                        $return['message'] = "Item Insert Success!";

                        // $updateSalesItems = "UPDATE `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` SET remainingQty=$remainingQty WHERE so_id=$so_id AND inventory_item_id=$itemId";
                        // $updateSalesItemsObj = queryUpdate($updateSalesItems);

                        $auditTrail['action_data']['Item Details'][$itemCode]['so_Item_id'] = $invId;
                        $auditTrail['action_data']['Item Details'][$itemCode]['lineNo'] = $lineNo;
                        $auditTrail['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                        $auditTrail['action_data']['Item Details'][$itemCode]['itemName'] = $itemName;
                        $auditTrail['action_data']['Item Details'][$itemCode]['itemDesc'] = $itemDesc;
                        $auditTrail['action_data']['Item Details'][$itemCode]['delivery_date'] = $delivery_date;
                        $auditTrail['action_data']['Item Details'][$itemCode]['qty'] = $qty;
                        $auditTrail['action_data']['Item Details'][$itemCode]['uom'] = $uom;
                        $auditTrail['action_data']['Item Details'][$itemCode]['tolerance'] = $tolerance;
                        $auditTrail['action_data']['Item Details'][$itemCode]['unitPrice'] = $unitPrice;
                        $auditTrail['action_data']['Item Details'][$itemCode]['basePrice'] = $baseAmount;
                        $auditTrail['action_data']['Item Details'][$itemCode]['hsnCode'] = $hsnCode;
                        $auditTrail['action_data']['Item Details'][$itemCode]['tax'] = $tax;
                        $auditTrail['action_data']['Item Details'][$itemCode]['totalTax'] = $totalTax;
                        $auditTrail['action_data']['Item Details'][$itemCode]['totalDiscount'] = $totalDiscount;
                        $auditTrail['action_data']['Item Details'][$itemCode]['totalDiscountAmt'] = $totalDiscountAmt;
                        $auditTrail['action_data']['Item Details'][$itemCode]['totalPrice'] = $totalPrice;
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message1'] = "somthing went wrong! 31";
                        $returnData['invItem'] = $itemOpenStocks;
                        $flug++;
                    }
                } else {
                    $returnData['insStockreturn3'][] = $selStockLog;
                    if ($itemOpenStocks >= $qty) {
                        $invItem1 = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICE_ITEMS . "`
                             SET
                             `so_invoice_id`='$invId',
                             `inventory_item_id`='$itemId',
                             `lineNo`='" . $lineNo . "',
                             `itemCode`='" . $itemCode . "',
                             `itemName`='" . $itemName . "',
                             `itemDesc`='" . $itemDesc . "',
                             `itemRemarks`='" . $itemRemarks . "',
                             `delivery_date`='" . $delivery_date . "',
                             `qty`='" . $qty . "',
                             `invoiceQty`='" . $invoiceQty . "',
                             `remainingQty`='" . $remainingQty . "',
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
                             `totalPrice`='" . $totalPrice . "'";

                        $itemIns = queryInsert($invItem1);
                        if ($itemIns['status'] == 'success') {
                            $return['status'] = "success";
                            $return['message'] = "Item Insert Success!";

                            // $updateSalesItems = "UPDATE `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` SET remainingQty=$remainingQty WHERE so_id=$so_id AND inventory_item_id=$itemId";
                            // $updateSalesItemsObj = queryUpdate($updateSalesItems);

                            $auditTrail['action_data']['Item Details'][$itemCode]['so_Item_id'] = $invId;
                            $auditTrail['action_data']['Item Details'][$itemCode]['lineNo'] = $lineNo;
                            $auditTrail['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                            $auditTrail['action_data']['Item Details'][$itemCode]['itemName'] = $itemName;
                            $auditTrail['action_data']['Item Details'][$itemCode]['itemDesc'] = $itemDesc;
                            $auditTrail['action_data']['Item Details'][$itemCode]['delivery_date'] = $delivery_date;
                            $auditTrail['action_data']['Item Details'][$itemCode]['qty'] = $qty;
                            $auditTrail['action_data']['Item Details'][$itemCode]['uom'] = $uom;
                            $auditTrail['action_data']['Item Details'][$itemCode]['tolerance'] = $tolerance;
                            $auditTrail['action_data']['Item Details'][$itemCode]['unitPrice'] = $unitPrice;
                            $auditTrail['action_data']['Item Details'][$itemCode]['basePrice'] = $baseAmount;
                            $auditTrail['action_data']['Item Details'][$itemCode]['hsnCode'] = $hsnCode;
                            $auditTrail['action_data']['Item Details'][$itemCode]['tax'] = $tax;
                            $auditTrail['action_data']['Item Details'][$itemCode]['totalTax'] = $totalTax;
                            $auditTrail['action_data']['Item Details'][$itemCode]['totalDiscount'] = $totalDiscount;
                            $auditTrail['action_data']['Item Details'][$itemCode]['totalDiscountAmt'] = $totalDiscountAmt;
                            $auditTrail['action_data']['Item Details'][$itemCode]['totalPrice'] = $totalPrice;


                            if ($ivType == "pgi_to_invoice") {
                                foreach ($selStockLog['data'] as $stockLogKey => $logdata) {
                                    if ($qty <= 0) {
                                        break;
                                    }
                                    $quantity = $logdata['itemQty'];
                                    $usedQuantity = min($quantity, $qty);
                                    $qty -= $usedQuantity;

                                    $logRef = $logdata['logRef'];
                                    $bornDate = $logdata['bornDate'];

                                    $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                SET 
                                                    companyId = '" . $company_id . "',
                                                    branchId = '" . $branch_id . "',
                                                    locationId = '" . $location_id . "',
                                                    storageLocationId = '" . $logdata['storage_location_id'] . "',
                                                    storageType ='" . $logdata['storageLocationTypeSlug'] . "',
                                                    itemId = '" . $itemId . "',
                                                    itemQty = '" . $usedQuantity * -1 . "',
                                                    itemUom = '" . $uom . "',
                                                    itemPrice = '" . $unitPrice . "',
                                                    refActivityName='INVOICE',
                                                    logRef = '" . $logRef . "',
                                                    refNumber='" . $invNo . "',
                                                    bornDate='" . $bornDate . "',
                                                    postingDate='" . $invoice_date . "',
                                                    createdBy = '" . $created_by . "',
                                                    updatedBy = '" . $updated_by . "'";

                                    $insStockreturn1 = queryInsert($insStockSummary1);


                                    $return['insStockreturn1'][] = $insStockreturn1;
                                }
                            } else {
                                foreach ($selStockLog['data'] as $stockLogKey => $logdata) {
                                    if ($itemSellType == 'CUSTOM') {
                                        // $explodes = explode('_', $logdata['logRef']);
                                        // $logRef = $explodes[0];
                                        $logRef = $logdata['logRef'];
                                        $keysval = $logdata['logRefConcat'];
                                        $usedQuantity = $filteredBatchSelection[$keysval];
                                        $bornDate = $logdata['bornDate'];
                                        $storage_location_id = $logdata['storage_location_id'];
                                        $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                                    } else {
                                        if ($qty <= 0) {
                                            break;
                                        }

                                        $quantity = $logdata['itemQty'];
                                        $usedQuantity = min($quantity, $qty);
                                        $qty -= $usedQuantity;
                                        // $explodes = explode('_', $logdata['logRef']);
                                        // $logRef = $explodes[0];

                                        $logRef = $logdata['logRef'];
                                        $bornDate = $logdata['bornDate'];
                                        $storage_location_id = $logdata['storage_location_id'];
                                        $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                                    }

                                    $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                SET 
                                                    companyId = '" . $company_id . "',
                                                    branchId = '" . $branch_id . "',
                                                    locationId = '" . $location_id . "',
                                                    storageLocationId = '" . $storage_location_id . "',
                                                    storageType ='" . $storageLocationTypeSlug . "',
                                                    itemId = '" . $itemId . "',
                                                    itemQty = '" . $usedQuantity * -1 . "',
                                                    itemUom = '" . $uom . "',
                                                    itemPrice = '" . $unitPrice . "',
                                                    refActivityName='INVOICE',
                                                    logRef = '" . $logRef . "',
                                                    refNumber='" . $invNo . "',
                                                    bornDate='" . $bornDate . "',
                                                    postingDate='" . $invoice_date . "',
                                                    createdBy = '" . $created_by . "',
                                                    updatedBy = '" . $updated_by . "'";

                                    $insStockreturn1 = queryInsert($insStockSummary1);
                                    // console($insStockreturn1);

                                    $returnData['insStockreturn1'][] = $insStockreturn1;
                                    $returnData['insStockreturn2'][] = $selStockLog;
                                }
                            }
                        } else {
                            $returnData['status'] = "warning";
                            $returnData['message1'] = "somthing went wrong! 31";
                            $returnData['invItem'] = $itemOpenStocks;
                            $flug++;
                        }
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message2'] = "Stock quantity issue - " . $item['itemCode'];
                        $flug++;
                    }
                }
                if ($ivType == "project") {
                    $invStatusUpdate = queryUpdate("UPDATE `erp_branch_sales_order_items` SET `invStatus`='done' WHERE `so_id`=$so_id AND `inventory_item_id`=$itemId");
                }
            }

            $updInv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` SET totalItems='$invTotalItems' WHERE so_invoice_id='$invId'";
            $dbCon->query($updInv);

            // getNextSerializedCode 👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾
            $declarationCode = rand(0000, 9999);

            $declarationObj = queryGet("SELECT * FROM `" . ERP_DOCUMENT_DECLARATION . "` WHERE declarationType='$ivType'");

            if ($declarationObj['data'] > 0) {
                $updateDeclaration = "UPDATE `" . ERP_DOCUMENT_DECLARATION . "` 
                        SET descText='$declaration_note' WHERE declarationType='$ivType'";
                $updateDeclarationObj = queryUpdate($updateDeclaration);
            } else {
                $insertDeclaration = "INSERT INTO `" . ERP_DOCUMENT_DECLARATION . "` 
                        SET
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `code`='$declarationCode',
                            `declarationType`='$ivType',
                            `descText`='$declaration_note',
                            `created_by`='$created_by',
                            `updated_by`='$updated_by'";
                $insertDeclarationObj = queryInsert($insertDeclaration);
            }
            // 👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾

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
                                    `ref_no`='$getInvNumber',
                                    `profit_center`='$profitCenter',
                                    `credit_period`='$creditPeriod',
                                    `kamId`='$kamId',
                                    `bank`='$bankId',
                                    `invoiceNoFormate`='$iv_varient',
                                    `placeOfSupply`='$placeOfSupply',
                                    `customerOrderNo`='$customerPO',
                                    `complianceInvoiceType`='$compInvoiceType',
                                    `conversion_rate`='$curr_rate',
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
                                `ref_no`='$getInvNumber',
                                `profit_center`='$profitCenter',
                                `credit_period`='$creditPeriod',
                                `kamId`='$kamId',
                                `bank`='$bankId',
                                `invoiceNoFormate`='$iv_varient',
                                `placeOfSupply`='$placeOfSupply',
                                `customerOrderNo`='$customerPO',
                                `complianceInvoiceType`='$compInvoiceType',
                                `conversion_rate`='$curr_rate',
                                `currency_id`='$currencyId',
                                `currency_name`='$currencyName',
                                `billingAddress`='$billingAddress',
                                `shippingAddress`='$shippingAddress',
                                `created_by`='$created_by',
                                `updated_by`='$updated_by'";
                $invoiceLog = queryInsert($insInvLog);
            }

            if ($ivType == "pgi_to_invoice") {
                // update pgi
                $updatePgi = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "` 
                            SET 
                                `pgiStatus`='invoice' WHERE so_delivery_pgi_id=" . $pgi_to_invoice . "";
                queryUpdate($updatePgi);
            } elseif ($ivType == "quotation_to_invoice") {
                // update quotations
                $updateQuoat = "UPDATE `" . ERP_BRANCH_QUOTATIONS . "` 
                SET 
                `approvalStatus`=10 WHERE quotation_id=" . $quotationId . "";
                queryUpdate($updateQuoat);
            } elseif ($ivType == "so_to_invoice") {
                // update so
                $updateSo = "UPDATE `" . ERP_BRANCH_SALES_ORDER . "` 
                SET 
                `soStatus`=10 WHERE so_id=" . $so_to_invoice . "";
                queryUpdate($updateSo);
            }

            if ($flug == 0) {
                // calculate days to date
                $duedate = date("Y-m-d", strtotime("+" . $creditPeriod . " days"));
                $invoicelink = BASE_URL . 'branch/location/classic-view/invoice-preview-print.php?invoice_id=' . $encodeInv_id;
                $invoicelinkWhatsapp = 'classic-view/invoice-preview-print.php?invoice_id=' . $encodeInv_id;

                $to = $customer_authorised_person_email;
                $sub = 'Invoice ' . $invNo . ' for Your Recent Purchase';
                $msg = '
                <img src="' . BASE_URL . 'public/mailstatus/mail-status-so-invoice.php?invId=' . $invId . '&mailstatus=2" style="height:1px; width:1px">
                <div>
                <div><strong>Dear ' . $customerName . ',</strong></div>
                <p>
                    I hope this email finds you well. I am writing to inform you that an invoice for your recent purchase with <b>' . $customerName . '</b> has been generated and is now ready for payment.
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
                    Thank you for your business and for choosing <b>' . $customerName . '</b>. We appreciate your continued support and look forward to serving you again in the future.
                </p>
                <div style="display:grid">
                    Best regards for, <span><b>' . $customerName . '</b></span>
                </div>
                
                <p>
                <a href="' . $invoicelink . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View Invoice</a>
                
                </p>
                </div>';
                $mail =  SendMailByMySMTPmailTemplate($to, $sub, $msg, null, $customerCode, 'salesInvoice', $invId, $invNo);

                global $current_userName;
                $whatsapparray = [];
                $whatsapparray['templatename'] = 'invoice_sent_msg';
                $whatsapparray['to'] = $customer_authorised_person_phone;
                $whatsapparray['customername'] = $customer_name;
                $whatsapparray['invoiceno'] = $invNo;
                $whatsapparray['invoicelink'] = $invoicelinkWhatsapp;
                $whatsapparray['quickcontact'] = null;
                $whatsapparray['current_userName'] = $current_userName;

                $whatsappreturn = SendMessageByWhatsappTemplate($whatsapparray);

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
                $auditTrail['action_data']['Mail Details']['Status'] = 'Mail send Successfully';

                $auditTrailreturn = generateAuditTrail($auditTrail);

                $flug2 = 0;


                //************************START ACCOUNTING ******************** */
                $extra_remark = $POST['extra_remark'] ?? '';

                if (count($pgiitem) > 0) {
                    //-----------------------------PGI ACC Start ----------------
                    $PGIInputData = [
                        "BasicDetails" => [
                            "documentNo" => $invNo, // Invoice Doc Number
                            "documentDate" => $invoice_date, // Invoice number
                            "postingDate" => $invoice_date, // current date
                            "reference" => $invNo, // grn code
                            "remarks" => "PGI Creation - " . $invNo . " " . $extra_remark,
                            "journalEntryReference" => "Sales"
                        ],
                        "customerDetails" => [
                            "customerId" => $customerId,
                            "customerName" => $customerName,
                            "customerCode" => $customerCode,
                            "customerGlId" => $customerParentGlId
                        ],
                        "FGItems" => $pgiitem
                    ];
                    //console($ivPostingInputData);
                    $ivPostingObj = $this->sopgiAccountingPosting($PGIInputData, "PGI", 0);
                    //console($ivPostingObj); 

                    if ($ivPostingObj['status'] == 'success') {
                        $pgiJournalId = $ivPostingObj['journalId'];
                        $sqlpgi = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "`
                                    SET
                                        `journal_id`=$pgiJournalId 
                                    WHERE `so_delivery_pgi_id`='$invNo'  ";

                        queryUpdate($sqlpgi);

                        $sqliv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
                                SET
                                `pgi_journal_id`=$pgiJournalId
                                    WHERE `so_invoice_id`='$invId'";
                        queryUpdate($sqliv);
                    } else {
                        $flug2++;
                    }

                    //-----------------------------PGI ACC END ----------------
                }


                //-----------------------------Invoicing ACC Start ----------------
                $InvoicingInputData = [
                    "BasicDetails" => [
                        "documentNo" => $invNo, // Invoice Doc Number
                        "documentDate" => $invoice_date, // Invoice number
                        "postingDate" => $invoice_date, // current date
                        "grnJournalId" => '',
                        "reference" => $invNo, // grn code
                        "remarks" => "SO Invoicing - " . $invNo . " " . $extra_remark,
                        "journalEntryReference" => "Sales"
                    ],
                    "customerDetails" => [
                        "customerId" => $customerId,
                        "customerName" => $customerName,
                        "customerCode" => $customerCode,
                        "customerGlId" => $customerParentGlId
                    ],
                    "companyDetails" => $arrMarge,
                    "compInvoiceType" => $compInvoiceType,
                    "FGItems" => $listItem,
                    "taxDetails" => [
                        "cgst" => $cgst,
                        "sgst" => $sgst,
                        "igst" => $igst,
                        "TCS" => $tcs
                    ],
                    "roundOffValue" => $roundOffValue
                ];
                //console($ivPostingInputData);
                $SOivPostingObj = $this->soIvAccountingPosting($InvoicingInputData, "SOInvoicing", $invId);
                //console($SOivPostingObj);

                if ($SOivPostingObj['status'] == 'success') {
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

                if ($flug2 == 0) {
                    $returnData['type'] = "pos_invoice";
                    $returnData['status'] = "success";
                    $returnData['message'] = "Invoice Created Successfully";
                    $returnData['invoiceLog'] = $invoiceLog;
                    $returnData['insInvLog'] = $insInvLog;
                    $returnData['updateInvoiceLog'] = $updateInvoiceLog;
                    $returnData['updateInvLog'] = $updateInvLog;
                    $returnData['invoiceNo'] = $getInvNumber;
                    $returnData['updateQuoat'] = $updateQuoat;
                    $returnData['PGIInputData'] = $PGIInputData;
                    $returnData['InvoicingInputData'] = $InvoicingInputData;
                    $returnData['ivPostingObj'] = $ivPostingObj;
                    $returnData['SOivPostingObj'] = $SOivPostingObj;
                    $returnData['updateSalesItemsObj'] = $updateSalesItemsObj;
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Invoice successfully sent, (Warning Account Entry failed!)";
                }
                //************************END ACCOUNTING ******************** */
            } else {
                $returnData['status'] = "warning";
                $returnData['message_03'] = "somthing went wrong! 30";
                $returnData['message'] = "Out of stock";
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['invInsert'] = $invInsert;
            $returnData['message'] = "somthing went wrong! 2";
        }
        return $returnData;
    }

    // add invoice 
    function insertProformaInvoice($POST, $FILES)
    {
        global $dbCon;
        $returnData = [];

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        // $IvNoByVerientresponse = getInvoiceNumberByVerient($POST['iv_varient']);
        // $invNo = $IvNoByVerientresponse['iv_number'];

        $invNo = time() . rand(00, 99);

        $invoice_no_serialized = $IvNoByVerientresponse['iv_number_array'];

        $customerId = $POST['customerId'];
        $billingAddress = cleanUpString(addslashes($POST['billingAddress']));
        $shippingAddress = cleanUpString(addslashes($POST['shippingAddress']));
        $creditPeriod = $POST['creditPeriod'];
        $invoice_date = $POST['invoiceDate'];
        $invoiceTime = $POST['invoiceTime'];
        $declaration_note = addslashes($POST['declaration_note']);
        $billing_address_id = $POST['billing_address_id'] ?? 0;
        $shipping_address_id = $POST['shipping_address_id'] ?? 0;
        $profitCenter = $POST['profitCenter'];
        $kamId = $POST['kamId'];
        $so_id = $POST['so_id'];
        $remarks = addslashes($POST['extra_remark']);

        $validitydate = $POST['validitydate'];
        if ($validitydate < date('Y-m-d')) {

            $returnData['status'] = "warning";

            $returnData['message'] = "Validation Date Wrong";

            return $returnData;
        }

        $iv_varient = $POST['iv_varient'];
        $shipToLastInsertedId = $POST['shipToLastInsertedId'] ?? 0;
        $bankId = $POST['bankId'];
        $compInvoiceType = $POST['compInvoiceType'];
        $placeOfSupply = $POST['placeOfSupply'];
        $customerGstinCode = $POST['customerGstinCode'];
        $quotationId = base64_decode($POST['quotationId']) ?? 0;
        $pgi_to_invoice = base64_decode($POST['pgi_to_invoice']) ?? 0;
        $so_to_invoice = base64_decode($POST['so_to_invoice']) ?? 0;
        $ivType = $POST['ivType'];

        $curr_rate = $POST['curr_rate'];
        $currency = explode('≊', $POST['currency']);
        $currencyId = $currency[0];
        $currencyName = $currency[2];

        $subTotal = str_replace(',', '', $POST['grandSubTotalAmtInp']) ?? 0;
        $totalDiscount = str_replace(',', '', $POST['grandTotalDiscountAmtInp']) ?? 0;
        $totalTaxAmt = str_replace(',', '', $POST['grandTaxAmtInp']) ?? 0;

        // Upload the attachment file to the specified directory and retrieve the file path.📂📂📂📂📂
        $attachmentObj = uploadFile($FILES['attachment'], COMP_STORAGE_DIR . "/others/");
        // uploadFile($file = [], $dir = "", $allowedExtensions = [], $maxSize = 0, $minSize = 0)

        $allTotalAmt = 0;
        $roundOffValue = 0;
        if ($POST['round_off_checkbox'] == 1 || !empty($POST['roundOffValue'])) {
            // $allTotalAmt = $POST['adjustedTotalAmount'] ?? 0;
            $allTotalAmt = $POST['paymentDetails']['adjustedCollectAmount'] ?? 0;
            $roundOffValue = $POST['paymentDetails']['roundOffValue'] ?? 0;
        } else {
            $allTotalAmt = str_replace(',', '', $POST['grandTotalAmtInp']) ?? 0;
        }

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
        $customerDetailsObj = queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,customer_code,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customerId'")['data'];
        $customer_authorised_person_email = $customerDetailsObj['customer_authorised_person_email'];
        $customer_authorised_person_phone = $customerDetailsObj['customer_authorised_person_phone'];
        $customer_name = $customerDetailsObj['customer_name'];

        $customerDetailsSerialize = serialize($customerDetailsObj);
        $customerCode = $customerDetailsObj["customer_code"] ?? 0;
        $customerParentGlId = $customerDetailsObj["parentGlId"] ?? 0;
        $customerName = $customerDetailsObj['customer_name'];
        $customer_Gst = $customerDetailsObj['customer_gstin'];

        // fetch company details
        $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
        $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
        $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
        $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
        $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
        $arrMarge = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);
        $companySerialize = serialize($arrMarge);

        $companyBankAccDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='$bankId' ")['data'];
        $companyBankSerialize = serialize($companyBankAccDetailsObj);

        $branch_Gst = $branchDetailsObj['branch_gstin'];

        $companyGstCode = substr($branch_Gst, 0, 2);
        $customerGstCode = substr($customer_Gst, 0, 2);

        $cgst = 0;
        $sgst = 0;
        $igst = 0;
        $tcs = 0;

        $gstAmt = 0;
        if ($compInvoiceType == "R" || $compInvoiceType == "SEWP") {
            if ($customerGstinCode != "") {
                if ($companyGstCode == $customerGstCode) {
                    $gstAmt = str_replace(',', '', $totalTaxAmt / 2);
                    $cgst = str_replace(',', '', $gstAmt);
                    $sgst = str_replace(',', '', $gstAmt);
                } else {
                    $igst = str_replace(',', '', $totalTaxAmt);
                }
            } else {
                if ($companyGstCode == $placeOfSupply) {
                    $gstAmt = str_replace(',', '', $totalTaxAmt / 2);
                    $cgst = str_replace(',', '', $gstAmt);
                    $sgst = str_replace(',', '', $gstAmt);
                } else {
                    $igst = str_replace(',', '', $totalTaxAmt);
                }
            }
        }
        // console('$companyGstCode, $customerGstCode, $cgst, $sgst, $igst, $totalTaxAmt, $placeOfSupply');
        // console($companyGstCode, $customerGstCode, $cgst, $sgst, $igst, $totalTaxAmt, $placeOfSupply);
        $invInsert = "INSERT INTO `" . ERP_PROFORMA_INVOICES . "` 
                         SET 
                             `invoice_no`='$invNo',
                             `invoice_no_serialized`='$invoice_no_serialized',
                             `company_id`='$company_id',
                             `branch_id`='$branch_id',
                             `location_id`='$location_id',
                             `type`='$ivType',
                             `credit_period`='$creditPeriod',
                             `customer_id`='$customerId',
                             `kamId`='$kamId',
                             `invoice_date`='$invoice_date',
                             `invoice_time`='$invoiceTime',
                             `validityperiod`  = '$validitydate',
                             `sub_total_amt`='$subTotal',
                             `profit_center`='$profitCenter',
                             `totalDiscount`='$totalDiscount',
                             `all_total_amt`='$allTotalAmt',
                             `due_amount`='$allTotalAmt',
                             `adjusted_amount`='$roundOffValue',
                             `conversion_rate`='$curr_rate',
                             `currency_id`='$currencyId',
                             `currency_name`='$currencyName',
                             `total_tax_amt`='$totalTaxAmt',
                             `cgst`='$cgst',
                             `sgst`='$sgst',
                             `igst`='$igst',
                             `billing_address_id`='$billing_address_id',
                             `shipping_address_id`='$shipping_address_id',
                             `shipToLastInsertedId`='$shipToLastInsertedId',
                             `customerDetails`='$customerDetailsSerialize',
                             `customer_gstin`='" . $customer_Gst . "',
                             `company_gstin`='" . $branch_Gst . "',
                             `companyDetails`='$companySerialize',
                             `company_bank_details`='$companyBankSerialize',
                             `customer_billing_address`='$billingAddress',
                             `customer_shipping_address`='$shippingAddress',
                             `placeOfSupply`='$placeOfSupply',
                             `compInvoiceType`='$compInvoiceType',
                             `declaration_note`='$declaration_note',
                             `remarks`='$remarks',
                             `created_by`='$created_by',
                             `updated_by`='$updated_by',
                             `invoiceStatus`='1'
         ";

        if ($dbCon->query($invInsert)) {
            $returnData['lastID'] = $dbCon->insert_id;
            $inv_id = $dbCon->insert_id;
            $encodeInv_id = base64_encode($inv_id);

            // insert attachment
            if ($attachmentObj['status'] == 'success') {
                $name = $attachmentObj['data'];
                $type = $FILES['attachment']['type'];
                $size = $FILES['attachment']['size'];
                $path = COMP_STORAGE_URL . '/others/' . $attachmentObj['data'];

                $insertAttachmentSql = "INSERT INTO `" . ERP_ATTACH_DOCUMENTS . "`
                        SET
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `file_name`='" . $name . "',
                            `file_path`='" . $path . "',
                            `file_type`='" . $type . "',
                            `file_size`='" . $size . "',
                            `refName`='invoice-creation',
                            `ref_no`='$inv_id'
                ";
                $insertAttachment = queryInsert($insertAttachmentSql);
            }

            $listItem = $POST['listItem'];
            $invId = $returnData['lastID'];
            $flug = 0;

            $sql = "SELECT * FROM `" . ERP_PROFORMA_INVOICES . "` WHERE so_invoice_id='$invId'";
            $getInvNumber =  queryGet($sql)['data']['invoice_no'];
            $invTotalItems = 0;
            $pgiitem = [];
            foreach ($listItem as $item) {

                if ($item['goodsType'] != 5) {
                    $pgiitem[] = $item;
                }

                $invTotalItems++;
                $lineNo = $item['lineNo'];
                $itemId = $item['itemId'];
                $invStatus = $item['invStatus'];
                $itemCode = $item['itemCode'];
                $goodsType = $item['goodsType'];
                $itemName = addslashes($item['itemName']);
                $itemDesc = addslashes($item['itemDesc']);
                $itemRemarks = addslashes($item['itemRemarks']) ?? '';
                $hsnCode = $item['hsnCode'];
                $tax = 0;
                $totalTax = 0;
                if ($compInvoiceType == "R" || $compInvoiceType == "SEWP") {
                    $tax = $item['tax'];
                    $totalTax = str_replace(',', '', $item['itemTotalTax1']) ?? 0;
                }
                $tolerance = $item['tolerance'] ?? 0;
                $totalDiscount = $item['totalDiscount'] ?? 0;
                $totalDiscountAmt = str_replace(',', '', $item['itemTotalDiscount1']) ?? 0;
                $unitPrice = str_replace(',', '', $item['unitPrice']) ?? 0;
                $baseAmount = str_replace(',', '', $item['baseAmount']) ? str_replace(',', '', $item['baseAmount']) : 0;
                $qty = $item['qty'] ?? 0;
                $invoiceQty = $item['invoiceQty'] ?? 0;
                $remainingQty = $item['remainingQty'] ?? 0;
                $uom = $item['uom'];
                $totalPrice = str_replace(',', '', $item['totalPrice']) ?? 0;
                $delivery_date = $item['delivery_date'];
                $enterQty = $item['enterQty'];

                $stockQty = $item['stockQty'];
                $explodeStockQty = $stockQty;
                if (isset($item['itemreleasetype'])) {
                    if ($item["itemreleasetype"] == 'FIFO') {
                        $itemSellType = 'ASC';
                    } else if ($item["itemreleasetype"] == 'LIFO') {
                        $itemSellType = 'DESC';
                    } else if ($item["itemreleasetype"] == 'CUSTOM') {
                        $itemSellType = 'CUSTOM';
                        $batchselection = $item['batchselection'];
                    }
                } else {
                    if ($item["itemSellType"] == 'FIFO') {
                        $itemSellType = 'ASC';
                    } else if ($item["itemSellType"] == 'LIFO') {
                        $itemSellType = 'DESC';
                    } else if ($item["itemSellType"] == 'CUSTOM') {
                        //$itemSellType = 'ASC';
                    }
                }

                $pgiNo = $_POST['pgiCode'];

                if ($ivType == "pgi_to_invoice") {
                    $selStockLog = $this->itemQtyStockCheck($itemId, "'fgMktOpen'", "ASC", $pgiNo, $invoice_date);
                    $itemOpenStocks = $selStockLog['sumOfBatches'];
                } else {
                    if ($itemSellType != 'CUSTOM') {
                        $selStockLog = $this->itemQtyStockCheck($itemId, "'rmWhOpen', 'fgWhOpen'", $itemSellType, '', $invoice_date);
                        $itemOpenStocks = $selStockLog['sumOfBatches'];
                    } else {
                        // echo $itemCode;

                        $filteredBatchSelection = [];

                        foreach ($batchselection as $key => $value) {
                            $explodes = explode('_', $key);
                            $logRef = $explodes[0];
                            $slocation = $explodes[1];

                            $keysval = $logRef . $slocation;

                            if (!empty($value)) {
                                $filteredBatchSelection[$keysval] = $value;
                            }
                        }

                        $keysString = "'" . implode("', '", array_keys($filteredBatchSelection)) . "'";


                        $selStockLog = $this->itemQtyStockCheck($itemId, "'rmWhOpen', 'fgWhOpen'", 'ASC', "$keysString", $invoice_date);
                        // console($selStockLog);
                        $itemOpenStocks = $selStockLog['sumOfBatches'];
                    }
                }
                $invItem1 = "INSERT INTO `" . ERP_PROFORMA_INVOICE_ITEMS . "`
                    SET
                    `proforma_invoice_id`='$invId',
                    `inventory_item_id`='$itemId',
                    `lineNo`='" . $lineNo . "',
                    `itemCode`='" . $itemCode . "',
                    `itemName`='" . $itemName . "',
                    `itemRemarks`='" . $itemRemarks . "',
                    `itemDesc`='" . $itemDesc . "',
                    `qty`='" . $qty . "',
                    `invoiceQty`='" . $invoiceQty . "',
                    `remainingQty`='" . $remainingQty . "',
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
                    `totalPrice`='" . $totalPrice . "'";

                $itemIns = queryInsert($invItem1);
                if ($itemIns['status'] == 'success') {
                    $return['status'] = "success";
                    $return['message'] = "Item Insert Success!";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message1'] = "somthing went wrong! 31";
                    $returnData['invItem'] = $itemOpenStocks;
                    $flug++;
                }
            }

            $updInv = "UPDATE `" . ERP_PROFORMA_INVOICES . "` SET totalItems='$invTotalItems' WHERE proforma_invoice_id='$invId'";
            $dbCon->query($updInv);

            // getNextSerializedCode 👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾
            $declarationCode = rand(0000, 9999);

            $declarationObj = queryGet("SELECT * FROM `" . ERP_DOCUMENT_DECLARATION . "` WHERE declarationType='$ivType'");

            if ($declarationObj['data'] > 0) {
                $updateDeclaration = "UPDATE `" . ERP_DOCUMENT_DECLARATION . "` 
                        SET descText='$declaration_note' WHERE declarationType='$ivType'";
                $updateDeclarationObj = queryUpdate($updateDeclaration);
            } else {
                $insertDeclaration = "INSERT INTO `" . ERP_DOCUMENT_DECLARATION . "` 
                        SET
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `code`='$declarationCode',
                            `declarationType`='$ivType',
                            `descText`='$declaration_note',
                            `created_by`='$created_by',
                            `updated_by`='$updated_by'";
                $insertDeclarationObj = queryInsert($insertDeclaration);
            }
            // 👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾
            $returnData['type'] = "pos_invoice";
            $returnData['status'] = "success";
            $returnData['message'] = "Invoice Created Successfully";
            $returnData['invoiceLog'] = $invoiceLog;
            $returnData['insInvLog'] = $insInvLog;
            $returnData['updateInvoiceLog'] = $updateInvoiceLog;
            $returnData['updateInvLog'] = $updateInvLog;
            $returnData['invoiceNo'] = $getInvNumber;
            $returnData['updateQuoat'] = $updateQuoat;
            $returnData['PGIInputData'] = $PGIInputData;
            $returnData['InvoicingInputData'] = $InvoicingInputData;
            $returnData['ivPostingObj'] = $ivPostingObj;
            $returnData['SOivPostingObj'] = $SOivPostingObj;
            $returnData['updateSalesItemsObj'] = $updateSalesItemsObj;
            $returnData['itemIns'] = $itemIns;
        } else {
            $returnData['status'] = "warning";
            $returnData['invInsert'] = $invInsert;
            $returnData['message'] = "somthing went wrong! 2";
        }
        return $returnData;
    }

    // add invoice 
    function insertQuotation($POST, $FILES)
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
        $billingAddress = cleanUpString(addslashes($POST['billingAddress']));
        $shippingAddress = cleanUpString(addslashes($POST['shippingAddress']));
        $posting_date = $POST['postingDate'];
        $compInvoiceType = $POST['compInvoiceType'];

        $subTotal = str_replace(',', '', $POST['grandSubTotalAmtInp']);
        $totalDiscount = str_replace(',', '', $POST['grandTotalDiscountAmtInp']);
        $totalTaxAmt = str_replace(',', '', $POST['grandTaxAmtInp']);
        $allTotalAmt = str_replace(',', '', $POST['grandTotalAmtInp']);
        $goodsType = $POST['goodsType'];
        $currencyName = $POST['currencyName'];

        $curr_rate = $POST['curr_rate'];
        $currency = explode('≊', $POST['currency']);
        $currencyId = $currency[0];
        $currencyName = $currency[2];
        $placeOfSupply = $POST['placeOfSupply'];
        $customerGstinCode = $POST['customerGstinCode'];
        $remarks = addslashes($POST['extra_remark']);

        $customerName = $POST['customerDetails']['name'];

        $quotationNo = "QUOT" . date('dmY') . rand(1111, 9999);

        // fetch customer details
        $customerDetailsObj = queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customerId'")['data'];
        $customer_authorised_person_email = $customerDetailsObj['customer_authorised_person_email'];
        $customer_name = $customerDetailsObj['customer_name'];

        $customerDetailsSerialize = serialize($customerDetailsObj);
        $customerCode = $customerDetailsObj["data"]["customer_code"] ?? 0;
        $customerParentGlId = $customerDetailsObj["data"]["parentGlId"] ?? 0;

        $validitydate = $POST['validitydate'];
        if ($validitydate < date('Y-m-d')) {

            $returnData['status'] = "warning";

            $returnData['message'] = "Validation Date Wrong";

            return $returnData;
        }

        // fetch company details
        $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
        $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
        $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
        $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
        $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
        $arrMarge = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);
        $companySerialize = serialize($arrMarge);

        $companyBankAccDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='$bankId' ")['data'];
        $companyBankSerialize = serialize($companyBankAccDetailsObj);

        $company_name = $companyDetailsObj['company_name'];

        $customer_Gst = $customerDetailsObj['customer_gstin'];
        $branch_Gst = $branchDetailsObj['branch_gstin'];

        // insert files
        $attachmentObj = uploadFile($FILES['attachment'], COMP_STORAGE_DIR . "/others/");

        $listItem = $POST['listItem'];
        $totalItems = count($POST['listItem']);

        $companyGstCode = substr($branch_Gst, 0, 2);
        $customerGstCode = substr($customer_Gst, 0, 2);
        $conditionGST = $companyGstCode == $customerGstCode;

        $cgst = 0;
        $sgst = 0;
        $igst = 0;
        $tcs = 0;

        $gstAmt = 0;
        if ($compInvoiceType == "R" || $compInvoiceType == "SEWP") {
            if ($customerGstinCode != "") {
                if ($companyGstCode == $customerGstCode) {
                    $gstAmt = str_replace(',', '', $totalTaxAmt / 2);
                    $cgst = str_replace(',', '', $gstAmt);
                    $sgst = str_replace(',', '', $gstAmt);
                } else {
                    $igst = str_replace(',', '', $totalTaxAmt);
                }
            } else {
                if ($companyGstCode == $placeOfSupply) {
                    $gstAmt = str_replace(',', '', $totalTaxAmt / 2);
                    $cgst = str_replace(',', '', $gstAmt);
                    $sgst = str_replace(',', '', $gstAmt);
                } else {
                    $igst = str_replace(',', '', $totalTaxAmt);
                }
            }
        }

        $invInsert = "INSERT INTO `" . ERP_BRANCH_QUOTATIONS . "`
                                SET
                                    `quotation_no`='$quotationNo',
                                    `company_id`='$company_id',
                                    `branch_id`='$branch_id',
                                    `location_id`='$location_id',
                                    `customer_id`='$customerId',
                                    `validityperiod`  = '$validitydate',
                                    `posting_date`='$posting_date',
                                    `compInvoiceType`='$compInvoiceType',
                                    `conversion_rate`='$curr_rate',
                                    `currency_id`='$currencyId',
                                    `currency_name`='$currencyName',
                                    `totalItems`='$totalItems',
                                    `goodsType`='$goodsType',
                                    `placeOfSupply`='$placeOfSupply',
                                    `totalTax`='$totalTaxAmt',
                                    `cgst`='$cgst',
                                    `sgst`='$sgst',
                                    `igst`='$igst',
                                    `customer_billing_address`='$billingAddress',
                                    `customer_shipping_address`='$shippingAddress',
                                    `approvalStatus`='14',
                                    `totalDiscount`='$totalDiscount',
                                    `totalAmount`='$allTotalAmt',
                                    `remarks`='$remarks',
                                    `created_by`='$created_by',
                                    `updated_by`='$updated_by'
                                    ";

        // console($invInsert);
        $qutotationresponce = queryInsert($invInsert);
        if ($qutotationresponce['status'] == 'success') {
            $returnData['lastID'] = $qutotationresponce['insertedId'];
            $quote_id = $qutotationresponce['insertedId'];
            $encodeQuot_id = base64_encode($quote_id);

            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrailDelv = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_QUOTATIONS;
            $auditTrail['basicDetail']['column_name'] = 'quotation_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $quote_id;  // primary key
            $auditTrail['basicDetail']['party_type'] = 'customer';
            $auditTrail['basicDetail']['party_id'] = $customerId;
            $auditTrail['basicDetail']['document_number'] = $quotationNo;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Quotation Add';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($invInsert);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['Quotation Details']['posting_date'] = $posting_date;
            $auditTrail['action_data']['Quotation Details']['totalItems'] = $totalItems;
            $auditTrail['action_data']['Quotation Details']['cgst'] = $cgst;
            $auditTrail['action_data']['Quotation Details']['sgst'] = $sgst;
            $auditTrail['action_data']['Quotation Details']['igst'] = $igst;
            $auditTrail['action_data']['Quotation Details']['totalDiscount'] = $totalDiscount;
            $auditTrail['action_data']['Quotation Details']['totalAmount'] = $allTotalAmt;

            $quoteId = $returnData['lastID'];
            $flug = 0;

            $invTotalItems = 0;

            $sql = "SELECT * FROM `" . ERP_BRANCH_QUOTATIONS . "` WHERE quotation_id='$quote_id'";
            $getQuotationNumber =  queryGet($sql)['data']['quotation_no'];

            // insert attachment
            if ($attachmentObj['status'] == 'success') {
                $name = $attachmentObj['data'];
                $type = $FILES['attachment']['type'];
                $size = $FILES['attachment']['size'];
                $path = COMP_STORAGE_URL . '/others/' . $attachmentObj['data'];

                $insertAttachmentSql = "INSERT INTO `" . ERP_ATTACH_DOCUMENTS . "`
                        SET
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `file_name`='" . $name . "',
                            `file_path`='" . $path . "',
                            `file_type`='" . $type . "',
                            `file_size`='" . $size . "',
                            `refName`='quotation-creation',
                            `ref_no`='$quoteId'
                ";
                $insertAttachment = queryInsert($insertAttachmentSql);
            }

            $quotationTotalAmount = 0;

            foreach ($listItem as $item) {
                $invTotalItems++;
                $itemId = $item['itemId'];
                $lineNo = $item['lineNo'];
                $itemCode = $item['itemCode'];
                $itemName = addslashes($item['itemName']);
                $itemDesc = addslashes($item['itemDesc']);
                $itemRemarks = addslashes($item['itemRemarks']);
                $hsnCode = $item['hsnCode'];
                $tax = 0;
                $totalTax = 0;
                if ($compInvoiceType == "R" || $compInvoiceType == "SEWP") {
                    $tax = $item['tax'];
                    $totalTax = str_replace(',', '', $item['itemTotalTax1']);
                }
                $tolerance = $item['tolerance'];
                $totalDiscount = $item['totalDiscount'];
                $totalDiscountAmt = str_replace(',', '', $item['itemTotalDiscount1']);
                $unitPrice = str_replace(',', '', $item['unitPrice']) ?? 0;
                $qty = $item['qty'] ?? 0;
                $uom = $item['uom'];
                $totalPrice = str_replace(',', '', $item['totalPrice']) ?? 0;
                $quotationTotalAmount += str_replace(',', '', $item['totalPrice']) ?? 0;

                $stockQty = $item['stockQty'];

                $invItem = "INSERT INTO `" . ERP_BRANCH_QUOTATION_ITEMS . "` 
                                SET 
                                    `quotation_id`='$quoteId',
                                    `inventory_item_id`='$itemId',
                                    `itemCode`='$itemCode',
                                    `itemName`='$itemName',
                                    `itemRemarks`='" . $itemRemarks . "',
                                    `itemDesc`='$itemDesc',
                                    `hsnCode`='$hsnCode',
                                    `tax`='$tax',
                                    `totalTax`='$totalTax',
                                    `tolerance`='$tolerance',
                                    `totalDiscount`='$totalDiscount',
                                    `itemTotalDiscount`='$totalDiscountAmt',
                                    `unitPrice`='$unitPrice',
                                    `totalPrice`='$totalPrice',
                                    `qty`='$qty',
                                    `uom`='$uom',
                                    `created_by`='$created_by',
                                    `updated_by`='$updated_by'
                 ";
                $quotationItems = queryInsert($invItem);

                $auditTrail['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                $auditTrail['action_data']['Item Details'][$itemCode]['itemName'] = $itemName;
                $auditTrail['action_data']['Item Details'][$itemCode]['itemDesc'] = $itemDesc;
                $auditTrail['action_data']['Item Details'][$itemCode]['hsnCode'] = $hsnCode;
                $auditTrail['action_data']['Item Details'][$itemCode]['tax'] = $tax;
                $auditTrail['action_data']['Item Details'][$itemCode]['totalTax'] = $totalTax;
                $auditTrail['action_data']['Item Details'][$itemCode]['tolerance'] = $tolerance;
                $auditTrail['action_data']['Item Details'][$itemCode]['totalDiscount'] = $totalDiscount;
                $auditTrail['action_data']['Item Details'][$itemCode]['totalDiscountAmt'] = $totalDiscountAmt;
                $auditTrail['action_data']['Item Details'][$itemCode]['unitPrice'] = $unitPrice;
                $auditTrail['action_data']['Item Details'][$itemCode]['totalPrice'] = $totalPrice;
                $auditTrail['action_data']['Item Details'][$itemCode]['qty'] = $qty;
                $auditTrail['action_data']['Item Details'][$itemCode]['uom'] = $uom;
            }

            if ($quotationItems['status'] == "success") {

                // calculate days to date
                $duedate = date("Y-m-d", strtotime("+" . $creditPeriod . " days"));

                $to = $customer_authorised_person_email;
                $sub = 'Quotation ' . $quotationNo . ' - ' . $customer_name;
                $msg = '
                <div>
                <div><strong>Dear ' . $customer_name . ',</strong></div>
                <p>
                    I hope this email finds you well. Thank you for considering our services/products.
                    We appreciate the opportunity to provide you with a quotation. We have carefully reviewed your requirements, and we are confident that our offerings will meet and exceed your expectations.
                </p>
                <strong>
                    Quotation details:
                </strong>
                <div style="display:grid">
                    <span>
                        Quotation Number: ' . $quotationNo . '
                    </span>
                    <span>
                        Total Amount: <strong>' . $currencyName . number_format($quotationTotalAmount, 2) . '</strong>
                    </span>
                    <span>
                        Total Amount In Word: <strong>' . number_to_words_indian_rupees($quotationTotalAmount) . ' ONLY</strong>
                    </span>
                </div>
                <p>
                    If you have any specific customization or additional requirements, please let us know, and we will be happy to provide you with an updated quotation tailored to your needs.
                </p>
                <p>
                    Thank you for your business and for choosing <b>' . $company_name . '</b>. We appreciate your continued support and look forward to serving you again in the future.
                </p>
                <div style="display:grid">
                    Best regards for, <span><b>' . $company_name . '</b></span>
                </div>
                
                <p>
                <a href="' . BASE_URL . 'branch/location/classic-view/invoice-preview-print.php?quotation_id=' . $encodeQuot_id . '&company_id=' . $company_id . '&branch_id=' . $branch_id . '&location_id=' . $location_id . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View Quotation</a>
                </p>
                </div>';
                $mail =  SendMailByMySMTPmailTemplate($to, $sub, $msg, null, $customerCode, 'salesQuotation', $quoteId, $quotationNo);



                return [
                    "status" => "success",
                    "message" => "Quotation Created Successfully",
                    "quotationNo" => $getQuotationNumber
                ];
            } else {
                return [
                    "status" => "warning",
                    "message" => "Somthing went wrong",
                ];
            }

            $auditTrailreturn = generateAuditTrail($auditTrail);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "somthing went wrong! 2";
            $returnData['sqlInvInsert'] = $invInsert;
        }


        return $returnData;
    }

    function getQuotations($id)
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $selectSql = "SELECT * FROM `" . ERP_BRANCH_QUOTATIONS . "` WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND quotation_id=$id AND `status`='active'";
        return queryGet($selectSql, false);
    }

    function getPartyOrders($id)
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $selectSql = "SELECT * FROM `" . ERP_PARTY_ORDER . "` WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND id=$id";
        return queryGet($selectSql, false);
    }

    function getQuotationItems($id)
    {
        $selectSql = "SELECT * FROM `" . ERP_BRANCH_QUOTATION_ITEMS . "` WHERE quotation_id='" . $id . "' AND `status`='active'";
        return queryGet($selectSql, true);
    }

    function getPartyOrderItems($orderId)
    {
        global $company_id;
        $selectSql = "SELECT summary.*, items.`itemId` as inventory_item_id, items.`company_id`, items.`branch`, items.`location_id`, items.`parentGlId`, items.`itemCode`, items.`item_sell_type`, items.`itemName`, items.`itemDesc`, items.`netWeight`, items.`grossWeight`, items.`volume`, items.`volumeCubeCm`, items.`height`, items.`width`, items.`length`, items.`goodsType`, items.`goodsGroup`, items.`purchaseGroup`, items.`service_group`, items.`availabilityCheck`, items.`baseUnitMeasure`, items.`issueUnitMeasure`, items.`uomRel`, items.`service_unit`, items.`weight_unit`, items.`measuring_unit`, items.`purchasingValueKey`, items.`itemOpenStocks`, items.`itemBlockStocks`, items.`itemMovingAvgWeightedPrice`, items.`hsnCode`, items.`rcm_enabled`, items.`tds`, items.`cost_center`, items.`asset_classes`, items.`dep_key`, items.`isBomRequired`, hsn.taxPercentage, partyItem.`order_id`, partyItem.`item_id`, partyItem.`quantity` as qty, partyItem.`remarks`
        FROM `erp_inventory_stocks_summary` as summary
        INNER JOIN  `" . ERP_INVENTORY_ITEMS . "` as items ON summary.itemId = items.itemId
        RIGHT JOIN `" . ERP_HSN_CODE . "` as hsn ON items.hsnCode = hsn.hsnCode
        INNER JOIN `" . ERP_PARTY_ORDER_ITEM . "` as partyItem ON summary.itemId = partyItem.item_id
        WHERE summary.company_id = '1' AND partyItem.order_id = '$orderId'";
        return queryGet($selectSql, true);
    }

    // add invoice 
    function insertServiceInvoice($POST, $FILES)
    {
        global $dbCon;
        $returnData = [];

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        $branchGst = queryGet("SELECT branch_gstin FROM `" . ERP_BRANCHES . "` WHERE branch_id='$branch_id'")['data']['branch_gstin'];

        $IvNoByVerientresponse = getInvoiceNumberByVerient($POST['iv_varient']);
        $invNo = $IvNoByVerientresponse['iv_number'];
        $invoice_no_serialized = $IvNoByVerientresponse['iv_number_array'];

        $customerId = $POST['customerId'];
        $billingAddress = cleanUpString(addslashes($POST['billingAddress']));
        $shippingAddress = cleanUpString(addslashes($POST['shippingAddress']));
        $creditPeriod = $POST['creditPeriod'];
        $invoice_date = $POST['invoiceDate'];
        $profitCenter = $POST['profitCenter'];
        $shipToLastInsertedId = $POST['shipToLastInsertedId'];
        $kamId = $POST['kamId'];
        $bankId = $POST['bankId'];
        $remarks = addslashes($POST['extra_remark']);

        // Upload the attachment file to the specified directory and retrieve the file path.📂📂📂📂📂
        $attachmentObj = uploadFile($FILES['attachment'], COMP_STORAGE_DIR . "/others/");
        // uploadFile($file = [], $dir = "", $allowedExtensions = [], $maxSize = 0, $minSize = 0)

        $allTotalAmt = 0;
        $roundOffValue = 0;
        // if ($POST['round_off_checkbox'] == 1) {
        //     $allTotalAmt = $POST['adjustedTotalAmount'];
        //     $roundOffValue = $POST['roundOffValue'];
        // } else {
        //     $allTotalAmt = str_replace(',', '', $POST['grandTotalAmtInp']);
        // }
        if ($POST['round_off_checkbox'] == 1 || !empty($POST['roundOffValue'])) {
            // $allTotalAmt = $POST['adjustedTotalAmount'] ?? 0;
            $allTotalAmt = $POST['paymentDetails']['adjustedCollectAmount'] ?? 0;
            $roundOffValue = $POST['paymentDetails']['roundOffValue'] ?? 0;
        } else {
            $allTotalAmt = str_replace(',', '', $POST['grandTotalAmtInp']) ?? 0;
        }

        $declaration_note = $POST['declaration_note'];
        $placeOfSupply = $POST['placeOfSupply'];
        $customerGstinCode = $POST['customerGstinCode'];
        $compInvoiceType = $POST['compInvoiceType'];
        $iv_varient = $POST['iv_varient'];

        $subTotal = str_replace(',', '', $POST['grandSubTotalAmtInp']);
        $totalDiscount = str_replace(',', '', $POST['grandTotalDiscountAmtInp']);
        $totalTaxAmt = str_replace(',', '', $POST['grandTaxAmtInp']);

        $curr_rate = $POST['curr_rate'];
        $currency = explode('≊', $POST['currency']);
        $currencyId = $currency[0];
        $currencyName = $currency[2];

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
        $customer_authorised_person_phone = $customerDetailsObj['customer_authorised_person_phone'];

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

        $companyBankAccDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='$bankId' ")['data'];
        $companyBankSerialize = serialize($companyBankAccDetailsObj);
        // console($companyBankAccDetailsObj);

        $company_name = $companyDetailsObj['company_name'];
        $customer_Gst = $customerDetailsObj['customer_gstin'];
        $branch_Gst = $branchDetailsObj['branch_gstin'];

        $companyGstCode = substr($branch_Gst, 0, 2);
        $customerGstCode = substr($customer_Gst, 0, 2);
        $conditionGST = $companyGstCode == $customerGstCode;

        $cgst = 0;
        $sgst = 0;
        $igst = 0;
        $tcs = 0;

        $gstAmt = 0;
        if ($compInvoiceType == "R" || $compInvoiceType == "SEWP") {
            if ($customerGstinCode != "") {
                if ($companyGstCode == $customerGstCode) {
                    $gstAmt = str_replace(',', '', $totalTaxAmt / 2);
                    $cgst = str_replace(',', '', $gstAmt);
                    $sgst = str_replace(',', '', $gstAmt);
                } else {
                    $igst = str_replace(',', '', $totalTaxAmt);
                }
            } else {
                if ($companyGstCode == $placeOfSupply) {
                    $gstAmt = str_replace(',', '', $totalTaxAmt / 2);
                    $cgst = str_replace(',', '', $gstAmt);
                    $sgst = str_replace(',', '', $gstAmt);
                } else {
                    $igst = str_replace(',', '', $totalTaxAmt);
                }
            }
        }
        // insert sales order invoice
        $invInsert = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                         SET 
                             `invoice_no`='$invNo',
                             `invoice_no_serialized`='$invoice_no_serialized',
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
                             `cgst`='$cgst',
                             `sgst`='$sgst',
                             `igst`='$igst',
                             `shipToLastInsertedId`='$shipToLastInsertedId',
                             `placeOfSupply`='$placeOfSupply',
                             `compInvoiceType`='$compInvoiceType',
                             `all_total_amt`='$allTotalAmt',
                             `due_amount`='$allTotalAmt',
                             `adjusted_amount`='$roundOffValue',
                             `conversion_rate`='$curr_rate',
                             `currency_id`='$currencyId',
                             `currency_name`='$currencyName',
                             `customerDetails`='$customerDetailsSerialize',
                             `customer_gstin`='" . $customer_Gst . "',
                             `company_gstin`='" . $branch_Gst . "',
                             `companyDetails`='$companySerialize',
                             `company_bank_details`='$companyBankSerialize',
                             `customer_billing_address`='$billingAddress',
                             `customer_shipping_address`='$shippingAddress',
                             `declaration_note`='$declaration_note',
                             `remarks`='$remarks',
                             `created_by`='$created_by',
                             `updated_by`='$updated_by',
                             `invoiceStatus`='1'
         ";
        if ($dbCon->query($invInsert)) {
            $returnData['lastID'] = $dbCon->insert_id;
            $inv_id = $dbCon->insert_id;
            $encodeInv_id = base64_encode($inv_id);
            $listItem = $POST['listItem'];
            $invId = $returnData['lastID'];
            $flug = 0;

            // insert attachment
            if ($attachmentObj['status'] == 'success') {
                $name = $attachmentObj['data'];
                $type = $FILES['attachment']['type'];
                $size = $FILES['attachment']['size'];
                $path = COMP_STORAGE_URL . '/others/' . $attachmentObj['data'];

                $insertAttachmentSql = "INSERT INTO `" . ERP_ATTACH_DOCUMENTS . "`
                        SET
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `file_name`='" . $name . "',
                            `file_path`='" . $path . "',
                            `file_type`='" . $type . "',
                            `file_size`='" . $size . "',
                            `refName`='invoice-creation',
                            `ref_no`='$inv_id'
                ";
                $insertAttachment = queryInsert($insertAttachmentSql);
            }

            $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE so_invoice_id='$invId'";
            $getInvNumber =  queryGet($sql)['data']['invoice_no'];
            $invTotalItems = 0;
            foreach ($listItem as $item) {
                $invTotalItems++;

                $lineNo = $item['lineNo'];
                $itemCode = $item['itemCode'];
                $itemId = $item['itemId'];
                $itemName = addslashes($item['itemName']);
                $itemRemarks = addslashes($item['itemRemarks']) ?? '';
                $itemDesc = addslashes($item['itemDesc']);
                $hsnCode = $item['hsnCode'];
                $tax = 0;
                $totalTax = 0;
                if ($compInvoiceType == "R" || $compInvoiceType == "SEWP") {
                    $tax = $item['tax'];
                    $totalTax = str_replace(',', '', $item['itemTotalTax1']);
                }
                $tolerance = $item['tolerance'];
                $totalDiscount = $item['totalDiscount'];
                $totalDiscountAmt = str_replace(',', '', $item['itemTotalDiscount1']);
                $unitPrice = str_replace(',', '', $item['unitPrice']);
                $baseAmount = str_replace(',', '', $item['baseAmount']) ? str_replace(',', '', $item['baseAmount']) : 0;
                $qty = $item['qty'];
                $uom = $item['uom'];
                // $service_unit = $item['service_unit'];
                $service_unit = $item['uom'];
                $totalPrice = str_replace(',', '', $item['totalPrice']);
                $delivery_date = $item['delivery_date'];
                $enterQty = $item['enterQty'];

                $invItem = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICE_ITEMS . "`
                             SET
                             `so_invoice_id`='$invId',
                             `inventory_item_id`='$itemId',
                             `lineNo`='" . $lineNo . "',
                             `itemCode`='" . $itemCode . "',
                             `itemName`='" . $itemName . "',
                             `itemRemarks`='" . $itemRemarks . "',
                             `itemDesc`='" . $itemDesc . "',
                             `delivery_date`='" . $delivery_date . "',
                             `qty`='" . $qty . "',
                             `uom`='" . $service_unit . "',
                             `service_unit`='" . $service_unit . "',
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
                $itemSql = queryInsert($invItem);
            }
            if ($itemSql['status'] == "success") {
                $return['status'] = "success";
                $return['message'] = "Item Insert Success!";

                $updInv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` SET totalItems='$invTotalItems' WHERE so_invoice_id='$invId'";
                $dbCon->query($updInv);

                // getNextSerializedCode 👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾
                $declarationCode = rand(0000, 9999);

                $declarationObj = queryGet("SELECT * FROM `" . ERP_DOCUMENT_DECLARATION . "` WHERE declarationType='service'");

                if ($declarationObj['data'] > 0) {
                    $updateDeclaration = "UPDATE `" . ERP_DOCUMENT_DECLARATION . "` 
                        SET descText='$declaration_note' WHERE declarationType='service'";
                    $updateDeclarationObj = queryUpdate($updateDeclaration);
                } else {
                    $insertDeclaration = "INSERT INTO `" . ERP_DOCUMENT_DECLARATION . "` 
                        SET
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `code`='$declarationCode',
                            `declarationType`='service',
                            `descText`='$declaration_note',
                            `created_by`='$created_by',
                            `updated_by`='$updated_by'";
                    $insertDeclarationObj = queryInsert($insertDeclaration);
                }
                // 👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "somthing went wrong! 3";
                $flug++;
            }
            // select from ERP_CUSTOMER_INVOICE_LOGS ****************
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
                                    `ref_no`='$getInvNumber',
                                    `profit_center`='$profitCenter',
                                    `credit_period`='$creditPeriod',
                                    `kamId`='$kamId',
                                    `bank`='$bankId',
                                    `invoiceNoFormate`='$iv_varient',
                                    `placeOfSupply`='$placeOfSupply',
                                    `customerOrderNo`='$customerPO',
                                    `complianceInvoiceType`='$compInvoiceType',
                                    `conversion_rate`='$curr_rate',
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
                                `ref_no`='$getInvNumber',
                                `profit_center`='$profitCenter',
                                `credit_period`='$creditPeriod',
                                `kamId`='$kamId',
                                `bank`='$bankId',
                                `invoiceNoFormate`='$iv_varient',
                                `placeOfSupply`='$placeOfSupply',
                                `customerOrderNo`='$customerPO',
                                `complianceInvoiceType`='$compInvoiceType',
                                `conversion_rate`='$curr_rate',
                                `currency_id`='$currencyId',
                                `currency_name`='$currencyName',
                                `billingAddress`='$billingAddress',
                                `shippingAddress`='$shippingAddress',
                                `created_by`='$created_by',
                                `updated_by`='$updated_by'";
                $invoiceLog = queryInsert($insInvLog);
            }
            if ($flug == 0) {

                // calculate days to date
                $duedate = date("Y-m-d", strtotime("+" . $creditPeriod . " days"));

                $invoicelink = BASE_URL . 'branch/location/branch-so-invoice-view.php?inv_id=' . $encodeInv_id;

                $to = $customer_authorised_person_email;
                $sub = 'Invoice ' . $invNo . ' for Your Recent Purchase';

                $msg = '
                <img src="' . BASE_URL . 'public/mailstatus/mail-status-so-invoice.php?invId=' . $invId . '&mailstatus=2" style="height:1px; width:1px">
                <div>
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
                <a href="' . BASE_URL . 'branch/location/classic-view/invoice-preview-print.php?invoice_id=' . $encodeInv_id . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View Invoice</a>
                
                </p>
                </div>';

                $mail =  SendMailByMySMTPmailTemplate($to, $sub, $msg, null, $customerCode, 'salesInvoice', $invId, $invNo);

                global $current_userName;
                $whatsapparray = [];
                $whatsapparray['templatename'] = 'invoice_sent_msg';
                $whatsapparray['to'] = $customer_authorised_person_phone;
                $whatsapparray['customername'] = $customer_name;
                $whatsapparray['invoiceno'] = $invNo;
                $whatsapparray['invoicelink'] = $invoicelink;
                $whatsapparray['quickcontact'] = null;
                $whatsapparray['current_userName'] = $current_userName;

                SendMessageByWhatsappTemplate($whatsapparray);

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

                $flug2 = 0;
                //************************START ACCOUNTING ******************** */
                $pgiJournalId = '';
                //-----------------------------PGI ACC Start ----------------
                // $PGIInputData = [
                //     "BasicDetails" => [
                //         "documentNo" => $invNo, // Invoice Doc Number
                //         "documentDate" => $invoice_date, // Invoice number
                //         "postingDate" => $invoice_date, // current date
                //         "reference" => $invNo, // grn code
                //         "remarks" => "PGI Creation - " . $invNo,
                //         "journalEntryReference" => "Sales"
                //     ],
                //     "FGItems" => $listItem
                // ];
                // //console($ivPostingInputData);
                // $ivPostingObj = $this->sopgiAccountingPosting($PGIInputData, "PGI", 0);
                // //console($ivPostingObj);
                // if ($ivPostingObj['status'] == 'success') {
                //     $pgiJournalId = $ivPostingObj['journalId'];
                //     $sqlpgi = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "`
                //     SET
                //         `journal_id`=$pgiJournalId 
                //     WHERE `so_delivery_pgi_id`='$invNo'  ";

                //     queryUpdate($sqlpgi);
                //     //-----------------------------PGI ACC END ----------------
                // }
                //-----------------------------Invoicing ACC Start ----------------
                $InvoicingInputData = [
                    "BasicDetails" => [
                        "documentNo" => $invNo, // Invoice Doc Number
                        "documentDate" => $invoice_date, // Invoice number
                        "postingDate" => $invoice_date, // current date
                        "grnJournalId" => $pgiJournalId,
                        "reference" => $invNo, // grn code
                        "remarks" => "SO Invoicing - " . $invNo,
                        "journalEntryReference" => "Sales"
                    ],
                    "customerDetails" => [
                        "customerId" => $customerId,
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

                if ($SOivPostingObj['status'] == 'success') {
                    $ivJournalId = $SOivPostingObj['journalId'];
                    $sqliv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
                    SET
                        `pgi_journal_id`=$pgiJournalId,
                        `journal_id`=$ivJournalId 
                        WHERE `so_invoice_id`='$invId'";
                    queryUpdate($sqliv);
                } else {
                    $flug2++;
                }

                //-----------------------------Invoicing ACC END ----------------

                if ($flug2 == 0) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Invoice Created Successfully";
                    $returnData['listItem'] = $listItem;
                    $returnData['itemSql'] = $itemSql;
                    $returnData['invoiceNo'] = $getInvNumber;
                    $returnData['$updateInvoiceLog'] = $updateInvoiceLog;
                    $returnData['$invoiceLog'] = $invoiceLog;
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Invoice successfully sent, (Warning Account Entry failed!)";
                    $returnData['SOivPostingObj'] = $SOivPostingObj;
                }
                //************************END ACCOUNTING ******************** */
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "somthing went wrong! 01";
                $returnData['sql'] = $invItem;
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
        $currency = queryGet("SELECT * FROM `" . ERP_CURRENCY_TYPE . "` WHERE currency_id='$id'");
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
    function fetchBranchSoById($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE so_id='$id' AND status='active'";
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

    function fetchSalesOrderById($so_id)
    {
        return queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE so_id='" . $so_id . "' AND status='active'");
    }

    function fetchCompanyConfig($configId){
        return queryGet("SELECT * FROM `" . ERP_CONFIG_INVOICES . "` WHERE config_id='" . $configId . "'");
    }

    // erp_inventory_item_images 
    function inventoryItemImages($itemId){
        return queryGet("SELECT * FROM `" . ERP_INVENTORY_ITEM_IMAGES . "` WHERE item_id='" . $itemId . "'", true);
    }

    // erp_item_specification
    function itemSpecification($itemId){
        return queryGet("SELECT specification, specification_detail FROM `" . ERP_ITEM_SPECIFICATION . "` WHERE item_id='" . $itemId . "'", true);
    }

    function getSalesOrderItems($so_id)
    {
        return queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` WHERE so_id='" . $so_id . "'", true);
    }
    // fetch pgi
    function fetchPGIById($pgi_id)
    {
        return queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "` WHERE so_delivery_pgi_id='" . $pgi_id . "' AND status='active'");
    }

    function getPGIItems($pgi_id)
    {
        return queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY_ITEMS_PGI . "` WHERE so_delivery_pgi_id='" . $pgi_id . "'", true);
    }

    // fetch branch so delivery pgi details by id 
    function fetchBranchSoInvoiceById($id)
    {
        $returnData = [];
        global $dbCon;

        $invTbl = ERP_BRANCH_SALES_ORDER_INVOICES;
        // "SELECT `" . $invTbl . "`.*, `erp_e_invoices`.`ack_no`, `erp_e_invoices`.`ack_date`,`erp_e_invoices`.`irn`, `erp_e_invoices`.`signed_qr_code` FROM `" . $invTbl . "` LEFT JOIN `erp_e_invoices` ON `" . $invTbl . "`.so_invoice_id = `erp_e_invoices`.invoice_id WHERE `" . $invTbl . "`.company_id='" . $company_id . "' AND `" . $invTbl . "`.branch_id='" . $branch_id . "' AND `" . $invTbl . "`.location_id='" . $location_id . "' " . $cond . " " . $sts . " ORDER BY `" . $invTbl . "`.so_invoice_id DESC limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " "

        $ins = "SELECT `" . $invTbl . "`.*, `erp_e_invoices`.`ack_no`, `erp_e_invoices`.`ack_date`,`erp_e_invoices`.`irn`, `erp_e_invoices`.`signed_qr_code` FROM `" . $invTbl . "` LEFT JOIN `erp_e_invoices` ON `" . $invTbl . "`.so_invoice_id = `erp_e_invoices`.invoice_id WHERE `" . $invTbl . "`.so_invoice_id='$id' AND `" . $invTbl . "`.status != 'deleted'";
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

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE customer_id='$customerId' AND invoiceStatus != '4' AND status = 'active'";
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
    function fetchBranchSoInvoiceBycustomerIdAndInvoiceNo($customerId, $invoiceArray)
    {
        $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE customer_id='$customerId' AND invoice_no IN ($invoiceArray)";
        return queryGet($sql, true);
    }
    // fetchBranchSoInvoiceBycustomerIdForManageInvoice
    function fetchBranchSoInvoiceBycustomerIdForManageInvoice($customerId)
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $invTbl = ERP_BRANCH_SALES_ORDER_INVOICES;

        $sql_list = "SELECT `" . $invTbl . "`.*, `erp_e_invoices`.`ack_no`, `erp_e_invoices`.`ack_date`,`erp_e_invoices`.`irn`, `erp_e_invoices`.`signed_qr_code` FROM `" . $invTbl . "` LEFT JOIN `erp_e_invoices` ON `" . $invTbl . "`.so_invoice_id = `erp_e_invoices`.invoice_id WHERE `" . $invTbl . "`.customer_id='" . $customerId . "' AND `" . $invTbl . "`.company_id='" . $company_id . "' AND `" . $invTbl . "`.branch_id='" . $branch_id . "' AND `" . $invTbl . "`.location_id='" . $location_id . "' AND `" . $invTbl . "`.invoiceStatus!=4 AND `" . $invTbl . "`.status!='reverse'";
        return queryGet($sql_list, true);
    }

    // fetch Branch So Invoice By customer Id For Manage Invoice Due
    function fetchBranchSoInvoiceBycustomerIdForManageInvoiceDue($customerId)
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $invTbl = ERP_BRANCH_SALES_ORDER_INVOICES;

        $sql_list = "SELECT `" . $invTbl . "`.*, `erp_e_invoices`.`ack_no`, `erp_e_invoices`.`ack_date`,`erp_e_invoices`.`irn`, `erp_e_invoices`.`signed_qr_code` FROM `" . $invTbl . "` LEFT JOIN `erp_e_invoices` ON `" . $invTbl . "`.so_invoice_id = `erp_e_invoices`.invoice_id WHERE `" . $invTbl . "`.invoiceStatus!=4 AND `" . $invTbl . "`.customer_id='" . $customerId . "' AND `" . $invTbl . "`.company_id='" . $company_id . "' AND `" . $invTbl . "`.branch_id='" . $branch_id . "' AND `" . $invTbl . "`.location_id='" . $location_id . "' AND `" . $invTbl . "`.invoiceStatus!=4 AND `" . $invTbl . "`.status!='reverse'";
        return queryGet($sql_list, true);
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

    function fetchBranchSoInvoiceItemsGroupByHSN($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT hsnCode, SUM(basePrice) AS basePrice, tax, SUM(totalTax) AS totalTax FROM `" . ERP_BRANCH_SALES_ORDER_INVOICE_ITEMS . "` WHERE `so_invoice_id`='$id' AND status='active' GROUP BY hsnCode, tax";
        if ($res = $dbCon->query($ins)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['status'] = "success";
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found!";
            $returnData['sql'] = $ins;
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

    function totalInvoiceAmountDetails()
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $fetchSql = "SELECT
            SUM(CASE WHEN invoiceStatus IN (1, 2) THEN due_amount ELSE 0 END) AS total_outstanding_amount,
            SUM(CASE WHEN invoiceStatus IN (1, 2) AND invoice_date < DATE_SUB(CURDATE(), INTERVAL credit_period DAY) THEN due_amount ELSE 0 END) AS total_overdue_amount,
            SUM(CASE WHEN invoiceStatus IN (1, 2) AND credit_period <= 30 THEN due_amount ELSE 0 END) AS total_due_in_30_days,
            SUM(CASE WHEN invoiceStatus IN (1, 2) THEN due_amount ELSE 0 END) AS total_due_amount
        FROM
        `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE `company_id`='$company_id' AND `branch_id`='$branch_id' AND `location_id`='$location_id'";

        return queryGet($fetchSql);
    }

    function invoiceCron()
    {
        global $companyNameNav;
        // $sql = "SELECT
        //     `so_invoice_id`, `customer_id`, `invoice_date`, `credit_period`
        // FROM
        //     `erp_branch_sales_order_invoices`
        // WHERE
        //     `invoiceStatus` IN (1, 2)
        //     AND `invoice_date` = CURDATE() - INTERVAL `credit_period` DAY";

        $sql = "SELECT
            `so_invoice_id`, `customer_id`, `invoice_date`, `credit_period`,
            DATEDIFF(CURDATE(), `invoice_date`) AS days_overdue
        FROM
            `erp_branch_sales_order_invoices`
        WHERE
            `invoiceStatus` IN (1, 2)
            AND `invoice_date` < DATE_SUB(CURDATE(), INTERVAL `credit_period` DAY)";

        $result = queryGet($sql, true);
        if ($result['numRows'] > 0) {
            foreach ($result['data'] as $row) {
                $invoiceId = $row['so_invoice_id'];
                $customerId = $row['customer_id'];
                $encodeInv_id = base64_encode($invoiceId);

                // fetch invoice🧾 details by inv id
                $invSql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE so_invoice_id='$invoiceId'";
                $invDetailsObj = queryGet($invSql);
                $invDetails = $invDetailsObj['data'];
                $invNo = $invDetails['invoice_no'];
                $allTotalAmt = $invDetails['all_total_amt'];
                $creditPeriod = $invDetails['credit_period'];
                $duedate = date("Y-m-d", strtotime("+" . $creditPeriod . " days"));

                // fetch customer👨🏻‍🦰 details by customer id
                $customerDetailsObj = queryGet("SELECT
                parentGlId,
                customer_pan,
                customer_gstin,
                trade_name AS customer_name,
                constitution_of_business,
                customer_opening_balance,
                customer_currency,
                customer_website,
                customer_credit_period,
                customer_picture,
                customer_authorised_person_name,
                customer_authorised_person_email,
                customer_authorised_alt_email,
                customer_authorised_person_phone,
                customer_authorised_alt_phone,
                customer_authorised_person_designation,
                customer_profile,
                customer_status
            FROM
                `" . ERP_CUSTOMER . "`
            WHERE
                `customer_id` = '$customerId'");
                $customerDetails = $customerDetailsObj['data'];

                $customer_authorised_person_email = $customerDetails['customer_authorised_person_email'];
                $customer_authorised_person_phone = $customerDetails['customer_authorised_person_phone'];
                $customer_name = $customerDetailsObj['customer_name'];
                $customerCode = $customerDetailsObj['customer_code'];
                $invoicelink = BASE_URL . 'branch/location/branch-so-invoice-view.php?inv_id=' . $encodeInv_id;
                $invoicelinkWhatsapp = 'branch-so-invoice-view.php?inv_id=' . $encodeInv_id;


                global $current_userName;
                $whatsapparray = [];
                $whatsapparray['templatename'] = 'invoice_sent_msg';
                $whatsapparray['to'] = $customer_authorised_person_phone;
                $whatsapparray['customername'] = $customer_name;
                $whatsapparray['invoiceno'] = $invNo;
                $whatsapparray['invoicelink'] = $invoicelinkWhatsapp;
                $whatsapparray['quickcontact'] = null;
                $whatsapparray['current_userName'] = $current_userName;

                SendMessageByWhatsappTemplate($whatsapparray);


                $to = $customer_authorised_person_email;
                $sub = 'Invoice ' . $invNo . ' for Your Recent Purchase';
                $msg = '
                <img src="' . BASE_URL . 'public/mailstatus/mail-status-so-invoice.php?invId=' . $invoiceId . '&mailstatus=2" style="height:1px; width:1px">
                <div>
                <div><strong>Dear ' . $customer_name . ',</strong></div>
                <p>
                    I hope this email finds you well. I am writing to inform you that an invoice for your recent purchase with <b>' . $companyNameNav . '</b> has been generated and is now ready for payment.
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
                    Thank you for your business and for choosing <b>' . $companyNameNav . '</b>. We appreciate your continued support and look forward to serving you again in the future.
                </p>
                <div style="display:grid">
                    Best regards for, <span><b>' . $companyNameNav . '</b></span>
                </div>
                <p>
                <a href="' . BASE_URL . 'branch/location/classic-view/invoice-preview-print.php?invoice_id=' . $encodeInv_id . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View Invoice</a>
                
                </p>
                </div>';
                return  $mail =  SendMailByMySMTPmailTemplate($to, $sub, $msg, null, $customerCode, 'salesInvoice', $invoiceId, $invNo);
            }
        } else {
            return "No overdue invoices found.";
        }
        return $result;
    }

    function totalInvoiceAmountDetailsByCustomer($customerId)
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $fetchSql = "SELECT 
        SUM(CASE WHEN invoiceStatus IN (1, 2) THEN `due_amount` ELSE 0 END) AS total_outstanding_amount,
        SUM(CASE WHEN invoiceStatus IN (1, 2) AND invoice_date < DATE_SUB(CURDATE(), INTERVAL credit_period DAY) THEN `due_amount` ELSE 0 END) AS total_overdue_amount,
        SUM(CASE WHEN invoiceStatus IN (1, 2) AND invoice_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN `due_amount` ELSE 0 END) AS total_due_in_last_30_days,
        SUM(CASE WHEN invoiceStatus IN (1, 2) THEN `due_amount` ELSE 0 END) AS total_due_amount
      FROM 
      `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE `company_id`='$company_id' AND `branch_id`='$branch_id' AND `location_id`='$location_id' AND customer_id='$customerId'  AND status='active'";

        return queryGet($fetchSql);
    }

    // fetch attachments by quotation id
    function getQuotationAttachments($quotationId)
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $sql = "SELECT * FROM `" . ERP_ATTACH_DOCUMENTS . "` WHERE `company_id` = '$company_id' AND `branch_id` = '$branch_id' AND `location_id` = '$location_id' AND `ref_no` = '$quotationId' AND `refName` = 'quotation-creation'";
        return queryGet($sql);
    }

    // fetch attachments by invoice id
    function getInvoiceAttachments($invoiceId)
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $sql = "SELECT * FROM `" . ERP_ATTACH_DOCUMENTS . "` WHERE `company_id` = '$company_id' AND `branch_id` = '$branch_id' AND `location_id` = '$location_id' AND `ref_no` = '$invoiceId' AND `refName` = 'invoice-creation'";
        return queryGet($sql);
    }

    // fetch attachments by sales order id
    function getSalesOrderAttachments($salesOrderId)
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $sql = "SELECT * FROM `" . ERP_ATTACH_DOCUMENTS . "` WHERE `company_id` = '$company_id' AND `branch_id` = '$branch_id' AND `location_id` = '$location_id' AND `ref_no` = '$salesOrderId' AND `refName` = 'so-creation'";
        return queryGet($sql);
    }

    function totalInvoiceAmountDetailsByCustomerSelectedInvoice($customerId, $invoices)
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $fetchSql = "SELECT 
        SUM(CASE WHEN invoiceStatus IN (1, 2) THEN `due_amount` ELSE 0 END) AS total_outstanding_amount,
        SUM(CASE WHEN invoiceStatus IN (1, 2) AND invoice_date < DATE_SUB(CURDATE(), INTERVAL credit_period DAY) THEN `due_amount` ELSE 0 END) AS total_overdue_amount,
        SUM(CASE WHEN invoiceStatus IN (1, 2) AND invoice_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN `due_amount` ELSE 0 END) AS total_due_in_last_30_days,
        SUM(CASE WHEN invoiceStatus IN (1, 2) THEN `due_amount` ELSE 0 END) AS total_due_amount
      FROM 
      `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE `company_id`='$company_id' AND `branch_id`='$branch_id' AND `location_id`='$location_id' AND customer_id='$customerId' AND invoice_no IN ($invoices)";

        return queryGet($fetchSql);
    }

    // fetch invoice details
    function fetchInvoiceDetails($invoiceId)
    {
        $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE so_invoice_id = '$invoiceId'";
        return queryGet($sql);
    }

    // fetch invoice items 
    function fetchInvoiceItems($invoiceId)
    {
        $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICE_ITEMS . "` WHERE so_invoice_id = '$invoiceId'";
        return queryGet($sql, true);
    }

    function fetchCompanyBank()
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;

        $sql = "SELECT *, CONCAT(bank_name,' (', account_no,')') as bank_name FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND FIND_IN_SET('$location_id',accForLocation)>0 AND (flag=1 OR flag=2) AND status='active'";
        if ($res = $dbCon->query($sql)) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
            $returnData['status'] = "success";
            $returnData['sql'] = $sql;
            $returnData['message'] = "Data found!";
            $returnData['data'] = $row;
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found!";
            $returnData['data'] = [];
        }

        return $returnData;
    }
    function createDataCustomer($POST = [])
    {
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $companyCodeNav;
        global $companyNameNav;
        $returnData = [];

        $isValidate = validate($POST, [
            "trade_name" => "required"
            // "customer_authorised_person_email" => "required|email",
            // "customer_authorised_person_phone" => "required|min:10|max:15",
            // "adminPassword" => "required|min:4"
        ], [
            "trade_name" => "Enter Trade name"
            // "customer_authorised_person_email" => "Enter valid email",
            // "customer_authorised_person_phone" => "Enter valid phone",
            // "adminPassword" => "Enter password(min:4 character)"
        ]);
        //console($POST);
        if ($isValidate["status"] == "success") {

            $accMapp = getAllfetchAccountingMappingTbl($company_id);
            // console($accMapp);
            if ($accMapp["status"] == "success") {
                $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['customer_gl']);
                $parentGlId = $paccdetails['data']['id'];
                $admin = array();
                $admin["adminName"] = $POST["customer_authorised_person_name"];
                $admin["adminEmail"] = $POST["customer_authorised_person_email"];
                $admin["adminPhone"] = $POST["customer_authorised_person_phone"];
                $admin["adminPassword"] = $POST["adminPassword"];
                $admin["tablename"] = 'tbl_customer_admin_details';
                $admin["adminPassword"] = $POST["adminPassword"];
                $admin["fldAdminCompanyId"] = $POST["company_id"];
                $admin["fldAdminBranchId"] = $POST["company_branch_id"];

                // if (isset($POST["createdata"]) && $POST["createdata"] == 'add_post') {
                $customer_status = 'active';
                /* } else {
                $customer_status = 'draft';
            }*/
                $lastlQuery = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE `company_id` = '" . $POST["company_id"] . "'  ORDER BY `customer_id` DESC LIMIT 1";

                $resultLast = queryGet($lastlQuery);
                $rowLast = $resultLast["data"];
                $lastsl = $rowLast['customer_code'];
                //
                $company_id = $POST["company_id"];
                $company_branch_id = $POST["company_branch_id"];
                $customer_code = getCustomerSerialNumber($lastsl);
                $customer_pan = $POST["customer_pan"];
                $customer_gstin = $POST["customer_gstin"] ?? '';
                $trade_name = $POST["trade_name"];
                $constitution_of_business = $POST["con_business"];

                $customer_authorised_person_name = $POST["customer_authorised_person_name"];
                $customer_authorised_person_designation = $POST["customer_authorised_person_designation"];
                $customer_authorised_person_phone = $POST["customer_authorised_person_phone"];
                $customer_authorised_alt_phone = $POST["customer_authorised_alt_phone"];
                $customer_authorised_person_email = $POST["customer_authorised_person_email"];
                $customer_authorised_alt_email = $POST["customer_authorised_alt_email"];

                // other address
                $state = $POST["state"];
                $city = $POST["city"];
                $district = $POST["district"];
                $location = $POST["location"];
                $build_no = $POST["build_no"];
                $flat_no = $POST["flat_no"];
                $street_name = $POST["street_name"];
                $pincode = $POST["pincode"];

                // accounting
                $opening_balance = $POST["opening_balance"];
                $currency = $POST["currency"];
                $credit_period = $POST["credit_period"];

                // $customer_picture = $POST["customer_picture"];
                $customer_visible_to_all = $POST["customer_visible_to_all"];
                //$adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]); 

                $sql = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE company_id=$company_id AND `customer_code`='" . $customer_code . "' ";


                if ($res = mysqli_query($dbCon, $sql)) {
                    if (mysqli_num_rows($res) == 0) {
                        // console($POST);
                        $ins = "INSERT INTO `" . ERP_CUSTOMER . "` 
                            SET
                                `company_id`='" . $company_id . "',
                                `company_branch_id`='" . $branch_id . "',
                                `location_id`='" . $location_id . "',
                                `parentGlId`='" . $parentGlId . "',
                                `customer_code`='" . $customer_code . "',
                                `customer_pan`='" . $customer_pan . "',
                                `customer_gstin`='" . $customer_gstin . "',
                                `trade_name`='" . $trade_name . "',
                                `customer_opening_balance`='$opening_balance',
                                `customer_currency`='$currency',
                                `customer_credit_period`='$credit_period',
                                `constitution_of_business`='" . $constitution_of_business . "',
                                `customer_authorised_person_name`='" . $customer_authorised_person_name . "',
                                `customer_authorised_person_designation`='" . $customer_authorised_person_designation . "',
                                `customer_authorised_person_phone`='" . $customer_authorised_person_phone . "',
                                `customer_authorised_alt_phone`='" . $customer_authorised_alt_phone . "',
                                `customer_authorised_person_email`='" . $customer_authorised_person_email . "',
                                `customer_authorised_alt_email`='" . $customer_authorised_alt_email . "',
                                `customer_visible_to_all`='" . $customer_visible_to_all . "',
                                `customer_created_by`='" . $created_by . "',
                                `customer_updated_by`='" . $created_by . "',
                                `customer_status`='" . $customer_status . "'";

                        if (mysqli_query($dbCon, $ins)) {
                            $customerId = mysqli_insert_id($dbCon);
                            $admin["customer_id"] = $customerId;
                            $admin["customer_code"] = $customer_code;
                            $adminRole = 1;
                            // $data = [
                            //     "date" => date('Y-m-d'),
                            //     "gl" => $parentGlId,
                            //     "subgl" => $customer_code,
                            //     "closing_qty" => 0,
                            //     "closing_val" => $opening_balance
                            // ];
                            // addOpeningBalanceForGlSubGl($data);
                            // insert to admin details
                            //addNewAdministratorUserGlobal($admin);


                            ///---------------------------------Audit Log Start---------------------
                            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                            $auditTrail = array();
                            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                            $auditTrail['basicDetail']['table_name'] = ERP_CUSTOMER;
                            $auditTrail['basicDetail']['column_name'] = 'customer_id'; // Primary key column
                            $auditTrail['basicDetail']['document_id'] = $customerId;  // primary key
                            $auditTrail['basicDetail']['party_type'] = 'customer';
                            $auditTrail['basicDetail']['party_id'] = $customerId;
                            $auditTrail['basicDetail']['document_number'] = $customer_code;
                            $auditTrail['basicDetail']['action_code'] = $action_code;
                            $auditTrail['basicDetail']['action_referance'] = '';
                            $auditTrail['basicDetail']['action_title'] = 'New Customer added';  //Action comment
                            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insCustomer);
                            $auditTrail['basicDetail']['others'] = '';
                            $auditTrail['basicDetail']['remark'] = '';

                            $auditTrail['action_data']['Customer Detail']['customer_code'] = $customer_code;
                            $auditTrail['action_data']['Customer Detail']['customer_pan'] = $customer_pan;
                            $auditTrail['action_data']['Customer Detail']['customer_gstin'] = $customer_gstin;
                            $auditTrail['action_data']['Customer Detail']['trade_name'] = $trade_name;
                            $auditTrail['action_data']['Customer Detail']['customer_currency'] = $currency;
                            $auditTrail['action_data']['Customer Detail']['customer_credit_period'] = $credit_period;
                            $auditTrail['action_data']['Customer Detail']['constitution_of_business'] = $constitution_of_business;
                            $auditTrail['action_data']['Customer Detail']['customer_authorised_person_name'] = $customer_authorised_person_name;
                            $auditTrail['action_data']['Customer Detail']['customer_authorised_person_designation'] = $customer_authorised_person_designation;
                            $auditTrail['action_data']['Customer Detail']['customer_authorised_person_phone'] = $customer_authorised_person_phone;
                            $auditTrail['action_data']['Customer Detail']['customer_authorised_alt_phone'] = $customer_authorised_alt_phone;
                            $auditTrail['action_data']['Customer Detail']['customer_authorised_person_email'] = $customer_authorised_person_email;
                            $auditTrail['action_data']['Customer Detail']['customer_authorised_alt_email'] = $customer_authorised_alt_email;
                            $auditTrail['action_data']['Customer Detail']['customer_visible_to_all'] = $customer_visible_to_all;
                            $auditTrail['action_data']['Customer Detail']['customer_created_by'] = $created_by;
                            $auditTrail['action_data']['Customer Detail']['customer_updated_by'] = $created_by;
                            $auditTrail['action_data']['Customer Detail']['customer_status'] = $customer_status;


                            $ins = "INSERT INTO `" . $admin['tablename'] . "`
                                SET
                                    `fldAdminName`='" . $admin['adminName'] . "',
                                    `fldAdminEmail`='" . $admin['adminEmail'] . "',
                                    `fldAdminPassword`='" . $admin['adminPassword'] . "',
                                    `fldAdminPhone`='" . $admin['adminPhone'] . "', 
                                    `customer_code`='" . $customer_code . "',
                                    `company_id`='" . $company_id . "',
                                    `customer_id`='" . $customerId . "',
                                    `fldAdminRole`='" . $adminRole . "'";
                            queryInsert($ins);
                            // insert to ERP_CUSTOMER_ADDRESS from basic details
                            $ins = "INSERT INTO `" . ERP_CUSTOMER_ADDRESS . "`
                                SET 
                                    `customer_id`='$customerId',
                                    `customer_address_primary_flag`='1',
                                    `customer_address_building_no`='$build_no',
                                    `customer_address_flat_no`='$flat_no',
                                    `customer_address_street_name`='$street_name',
                                    `customer_address_pin_code`='$pincode',
                                    `customer_address_location`='$location',
                                    `customer_address_city`='$city',
                                    `customer_address_district`='$district',
                                    `customer_address_state`='$state',
                                    `customer_address_created_by`='$created_by',
                                    `customer_address_updated_by`='$created_by' 
                                    ";
                            mysqli_query($dbCon, $ins);


                            /* $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['customer_gl']);
                            $accounts['p_id'] = $paccdetails['data']['id'];
                            $accounts['personal_glcode_lvl'] = $paccdetails['data']['lvl'];
                            $accounts['typeAcc'] = $paccdetails['data']['typeAcc'];
                            $accounts['gl_code'] = $customer_code;
                            $accounts['company_id'] = $company_id;
                            $accounts['gl_label'] = $trade_name;
                            $accounts['glSt'] = 'last';
                            $accounts['created_by'] = $created_by;
                            $accounts['updated_by'] = $created_by;
                            //createDataChartOfAccounts($accounts);*/

                            $sub = "Welcome to $companyNameNav";
                            $msg = "Dear $customer_authorised_person_name,<br>
                            We are delighted to welcome you on board as a valued client of $companyNameNav. We are committed to providing you with the best possible service and support, and we look forward to working with you.<br>
                            To ensure a smooth onboarding process, please find below some important information that will be helpful to you:<br>
                            <b>Our team:</b> Our team is available to assist you with any questions or concerns you may have. Please do not hesitate to reach out to us for any support.<br>
                            <b>Login information:</b><br>
                            <b>Url: </b>" . BASE_URL . "customer/<br>
                            <b>Company Code: </b>" . $companyCodeNav . "<br>
                            <b>Customer Code: </b>" . $customer_code . "<br>
                            <b>Password: </b>" . $POST["adminPassword"] . "<br>
                            Resources: We have a range of resources available to help you make the most of our services, including user guides, tutorials, and FAQs.<br>
                            Upcoming events: We regularly host webinars, workshops, and other events to help you stay up-to-date with the latest developments in our services.<br>
                            If there is anything else we can do to help, please do not hesitate to contact us. We are here to support you and ensure that your experience with $companyNameNav is a positive one.<br>
                            Thank you for choosing $companyNameNav, and we look forward to working with you.<br><br>
                            Best regards, $companyNameNav";
                            $mail =  SendMailByMySMTPmailTemplate($customer_authorised_person_email, $sub, $msg, null, $customer_code, 'customerAdd', $customerId, $customer_code);


                            global $current_userName;
                            $whatsapparray = [];
                            $whatsapparray['templatename'] = 'customer_onboard_msg';
                            $whatsapparray['to'] = $customer_authorised_person_phone;
                            $whatsapparray['customername'] = $trade_name;
                            $whatsapparray['companyname'] = $companyNameNav;
                            $whatsapparray['companyCodeNav'] = $companyCodeNav;
                            $whatsapparray['customer_code'] = $customer_code;
                            $whatsapparray['password'] = $POST["adminPassword"];
                            $whatsapparray['quickcontact'] = null;
                            $whatsapparray['current_userName'] = $current_userName;
                            $whatsapparray['user_designation'] = 'Admin';

                            SendMessageByWhatsappTemplate($whatsapparray);



                            $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_primary_flag'] = 1;
                            $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_building_no'] = $build_no;
                            $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_flat_no'] = $flat_no;
                            $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_street_name'] = $street_name;
                            $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_pin_code'] = $pincode;
                            $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_location'] = $location;
                            $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_city'] = $city;
                            $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_district'] = $district;
                            $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_state'] = $state;
                            $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_created_by'] = $created_by;
                            $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_updated_by'] = $created_by;


                            $auditTrail['action_data']['Mail-Send']['send-status'] = 'success';

                            $auditTrailreturn = generateAuditTrail($auditTrail);

                            $returnData['status'] = "success";
                            $returnData['message'] = "Customer added successfully";
                        } else {
                            $returnData['status'] = "warning";
                            $returnData['message'] = "Customer added failed";
                        }
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "Customer already exist";
                    }
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Somthing went wrong";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Setup Your Accounts first!";
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
        }
        return $returnData;
    }
}
