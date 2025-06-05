<?php

class BranchPr

{

    function addBranchPr($POST)

    {

        // console($POST['listItem']);
        // exit();

        $returnData = [];

        global $dbCon;

        global $company_id;

        global $branch_id;

        global $location_id;

        global $created_by;

        global $updated_by;

        global $admin_variant;




        $isValidate = validate($POST, [

            "expDate" => "required"



        ], [

            "expDate" => "Enter Expected Date"



        ]);



        if ($isValidate["status"] != "success") {

            $returnData['status'] = "warning";

            $returnData['message'] = "Invalid form inputes";

            $returnData['errors'] = $isValidate["errors"];

            return $returnData;
        }

        $validitydate = $POST['validitydate'];
    
        if ($validitydate<date('Y-m-d')) {

            $returnData['status'] = "warning";

            $returnData['message'] = "Validation Date Wrong";

            return $returnData;
        }




        // $company_id = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];

        //$branch_id = $_SESSION['logedBranchAdminInfo']['fldAdminBranchId'];

        // $customerId = $POST['customerId'];

        $expectedDate = $POST['expDate'];

        $today = date("Y-m-d");
        if($expectedDate < $today){

            $returnData = [
                "status" => "warning",
                "message" => "Required/Expected date can not be lesser than today's date",
                "numRows" => 0,
                "data" => []
            ];
            return $returnData;

        }

        $refNo = $POST['refNo'];

        $description = $POST['description'];

        $prDate = $POST['prDate'];

        $pr_type = $POST['pr_type'];

        $variant = $_SESSION['visitBranchAdminInfo']['flAdminVariant'];
        // $listItem = $_POST['listItem'];
        // $filteredDeliverySchedule = [];
        
        // foreach ($listItem as $item) {
        //     foreach ($item['deliverySchedule'] as $schedule) {
        //         if (!empty($schedule['multiDeliveryDate']) && !empty($schedule['quantity'])) {
        //             $filteredDeliverySchedule[] = $schedule;
        //         }
        //     }
        // }

       


        //$customerPO = $POST['customerPO'];
        $check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
        $check_var_data = $check_var_sql['data'];
        $max = $check_var_data['month_end'];
        $min = $check_var_data['month_start'];

        // console($variant);
        // exit();

        if ($prDate > $max) {

            $returnData = [
                "status" => "warning",
                "message" => "PR Date Invalid",
                "numRows" => 0,
                "data" => []
            ];
            return $returnData;
        } elseif ($prDate < $min) {

            $returnData = [
                "status" => "warning",
                "message" => "PO Date Invalid",
                "numRows" => 0,
                "data" => []
            ];
            return $returnData;
        } else {




            $created_by = $_SESSION['logedBranchAdminInfo']['adminId'] . "|" . $_SESSION['logedBranchAdminInfo']['adminType'];

            $lastQuery = "SELECT * FROM " . ERP_BRANCH_PURCHASE_REQUEST . " WHERE `company_id` = '$company_id' AND `branch_id` = '$branch_id' AND `location_id` = '$location_id' ORDER BY `purchaseRequestId` DESC LIMIT 1";

            $last = queryGet($lastQuery);
            $lastRow = $last['data'] ?? "";
            $lastPrId = $lastRow['prCode'] ?? "";
            $prCode = getPRSerialNumber($lastPrId);
            //exit();
            $created_at = date('Y-m-d H:i:s');


            $ins = "INSERT INTO `" . ERP_BRANCH_PURCHASE_REQUEST . "`

                 SET

                   `expectedDate`='$expectedDate',
                   `pr_date`  = '$prDate',

                   `description`='$description',

                   `prCode` = '$prCode',
                   `validityperiod`  = '$validitydate',

                   `branch_id` = '$branch_id',

                   `company_id` = '$company_id',

                   `location_id` = '$location_id',

                   `created_by` = '$created_by',

                   `updated_by` = '$updated_by',

                   `pr_type` =  '$pr_type', 

                   `refNo`='$refNo'";
            $insConn = queryInsert($ins);

            if ($insConn['status'] == "success") {

                $lastId = $dbCon->insert_id;

                $listItem = $POST['listItem'];

                foreach ($listItem as $item) {
                    if ($item['uom'] == "") {
                        $uom = 0;
                    } else {
                        $uom = $item['uom'];
                    }
                    $delItem = $item['deliverySchedule'];
                    $del_note =  json_encode($item['deliverySchedule']);
                    $insert = "INSERT INTO `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "`

                            SET

                            `prId`='" . $lastId . "',

                            `company_id`='" . $company_id . "',

                            `branch_id`='" . $branch_id . "',

                            `location_id`='" . $location_id . "',

                              `itemId`='" . $item['itemId'] . "',

                              `itemCode`='" . $item['itemCode'] . "',

                              `itemQuantity`='" . $item['qty'] . "',

                              `uom`='" . $uom . "',
                              `remainingQty`='" . $item['qty'] . "',

                              `itemNote` = '" . $item['note'] . "',

                              `itemName`='" . $item['itemName'] . "',

                              `delivery_schedule` = '".addslashes($del_note)."'

                  ";
                    //exit();

                    $insData = queryInsert($insert);
                    if($insData['status'] == 'success'){
                        $lastItemId = $insData['insertedId'];
                       // console( $filteredDeliverySchedule);
                       // exit();
                        foreach ($item['deliverySchedule'] as $delItem) {
                        if ($delItem['multiDeliveryDate'] == "") {
                            $date = $expectedDate;
                            $quantity = $item['qty'];
                        } else {
                            $date = $delItem['multiDeliveryDate'];
                            $quantity = $delItem['quantity'];
                        }
                        // $date = $delItem['multiDeliveryDate'];
                        // $quantity = $delItem['quantity'];

                        $insDeli = "INSERT INTO `erp_purchase_register_item_delivery_schedule` 
                        SET 
                        `pr_id`=$lastId ,
                        `pr_item_id`=$lastItemId,
                        `delivery_date`='" . $date . "',
                        `delivery_status`='open',
                        `qty`='" . $quantity . "',
                        `remaining_qty`='" . $quantity . "',
                        `created_by` = '$created_by',
                        `updated_by` = '$updated_by'
                        ";

                        $insDeliConn = queryInsert($insDeli);
                        // console($insDeli);
                        // exit();

                    }

                    }


                    else{
                        $returnData['status'] = "warning";
                    $returnData['message'] = "Error.";
                    }
                    // console($insData);
                    // $del_arr = [];
                    // foreach($item['deliverySchedule'] as $del){
                    //     $del_arr['date'] = $del['multiDeliveryDate'];
                    //     $del_arr['qty'] = $del['quantity'];
                    // }


                    $auditTrail['action_data']['Item Details'][$item['itemCode']]['Item_Code'] = $item['itemCode'];
                    $auditTrail['action_data']['Item Details'][$item['itemCode']]['Item_Quantity'] = decimalQuantityPreview($item['qty']);
                    $auditTrail['action_data']['Item Details'][$item['itemCode']]['UOM'] = getUomDetail($uom)['data']['uomName'];
                    $auditTrail['action_data']['Item Details'][$item['itemCode']]['Remaining_Qty'] = decimalQuantityPreview($item['qty']);
                    $auditTrail['action_data']['Item Details'][$item['itemCode']]['Item_Note'] = $item['note'];
                    $auditTrail['action_data']['Item Details'][$item['itemCode']]['Item_Name'] = $item['itemName'];




                    $returnData['status'] = "success";
                    $returnData['message'] = "PR creation Successful! New Item Code is -" . $prCode;
                }
             
         //     exit();

                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_PURCHASE_REQUEST;
                $auditTrail['basicDetail']['column_name'] = 'purchaseRequestId'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $lastId;  // primary key
                $auditTrail['basicDetail']['document_number'] = $prCode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['party_id'] = 0;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = 'New PR created';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';




                $auditTrail['action_data']['Purchase Request Details']['Expected_Date'] = formatDateORDateTime($expectedDate);
                $auditTrail['action_data']['Purchase Request Details']['PR_Date'] = formatDateORDateTime($prDate);
                $auditTrail['action_data']['Purchase Request Details']['Description'] = $description;
                $auditTrail['action_data']['Purchase Request Details']['PR_Code'] = $prCode;
                $auditTrail['action_data']['Purchase Request Details']['Created_By'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Purchase Request Details']['Updated_By'] = getCreatedByUser($updated_by);
                $auditTrail['action_data']['Purchase Request Details']['PR_Type'] = $pr_type;
                $auditTrail['action_data']['Purchase Request Details']['Ref_No'] = $refNo;




                $auditTrailreturn = generateAuditTrail($auditTrail);


                return $returnData;
            } else {

                return $returnData;
            }
        }
    }


    // function addBranchSoItems($POST, $id)

    // {

    //     $returnData = [];

    //     global $dbCon;

    //     $lastId = $id;

    //     $listItem = $POST['listItem'];

    //     $totalDiscount = 0;

    //     $totalItems = count($listItem);

    //     $totalAmount = 0;

    //     foreach ($listItem as $item) {

    //         $ins = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_ITEMS . "`

    //             SET

    //               `so_id`='$lastId',

    //               `inventory_item_id`='" . $item['itemId'] . "',

    //               `itemCode`='" . $item['itemCode'] . "',

    //               `itemName`='" . $item['itemName'] . "',

    //               `totalDiscount`='" . $item['totalDiscount'] . "',

    //               `unitPrice`='" . $item['unitPrice'] . "',

    //               `tolerance`='" . $item['tolerance'] . "',

    //               `qty`='" . $item['qty'] . "'

    //   ";

    //         if ($res = $dbCon->query($ins)) {

    //             $returnData['itemLastID'] = $dbCon->insert_id;

    //             $tot = $item['unitPrice'] * $item['qty'];

    //             $dis = ($tot * $item['totalDiscount']) / 100;

    //             $totalDiscount = $totalDiscount + $dis;

    //             $totalAmount = $totalAmount + $tot;

    //             // console($item['deliverySchedule']);

    //             foreach ($item['deliverySchedule'] as $delItem) {

    //                 $insDeli = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` 

    //                 SET 

    //                 `so_item_id`='" . $returnData['itemLastID'] . "',

    //                 `delivery_date`='" . $delItem['multiDeliveryDate'] . "',

    //                 `qty`='" . $delItem['quantity'] . "'";

    //                 if ($dbCon->query($insDeli)) {

    //                     $returnData['success'] = "true";

    //                     $returnData['message'] = "inserted success!";

    //                     // redirect($_SERVER['PHP_SELF']);

    //                 } else {

    //                     $returnData['success'] = "false";

    //                     $returnData['message'] = "somthing went wrong!";

    //                 }

    //             }

    //         } else {

    //             $returnData['success'] = "false";

    //             $returnData['message'] = "somthing went wrong!";

    //         }

    //     }



    //     $updateDeli = "UPDATE `" . ERP_BRANCH_SALES_ORDER . "` 

    //                     SET 

    //                         `totalItems`='" . $totalItems . "',

    //                         `totalDiscount`='" . $totalDiscount . "',

    //                         `totalAmount`='" . $totalAmount . "' WHERE so_id=" . $lastId . "";

    //     $dbCon->query($updateDeli);



    //     return $returnData;

    // }



    // function fetchBranchPrListing()

    // {

    //     $returnData = [];

    //     global $dbCon;



    //     $ins = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_REQUEST .  "` WHERE 1 " . $cond . " AND rfq.prCode=pr.prCode AND rfq.company_id='" .$company_id."' AND rfq.branch_id='" .$branch_id."' AND rfq.location_id='" .$location_id."' " . $sts . " ORDER BY rfq.rfqId DESC ";



    //     if ($res = $dbCon->query($ins)) {

    //         $row = $res->fetch_all(MYSQLI_ASSOC);

    //         $returnData['success'] = "true";

    //         $returnData['message'] = "Data found!";

    //         $returnData['data'] = $row;

    //     } else {

    //         $returnData['success'] = "false";

    //         $returnData['message'] = "Data not found!";

    //         $returnData['data'] = [];

    //     }



    //     return $returnData;

    // }



    function fetchBranchPrItems($id)

    {

        $returnData = [];

        global $dbCon;

        global $company_id;

        global $branch_id;

        global $location_id;



        $ins = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "` LEFT JOIN erp_inventory_mstr_uom ON erp_inventory_mstr_uom.uomId = erp_branch_purchase_request_items.uom WHERE erp_branch_purchase_request_items.prId ='$id' AND erp_branch_purchase_request_items.company_id = $company_id AND erp_branch_purchase_request_items.branch_id = $branch_id AND erp_branch_purchase_request_items.location_id = $location_id";

        //console($ins);

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



        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` WHERE `so_item_id`='$soItemId' AND status='active'";

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



    function fetchCustomerDetails($soId)

    {

        $returnData = [];

        global $dbCon;



        $ins = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$soId'";

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

    function addBranchRFQ($POST)
    {
        $returnData = [];
        if (empty($POST['itemId'])) {

            $returnData['status'] = "warning";

            $returnData['message'] = "No Item Selected";



            return $returnData;
        }


        global $dbCon;

        global $company_id;
        global $branch_id;
        global $location_id;



        $created_by = $_SESSION['logedBranchAdminInfo']['adminId'] . "|" . $_SESSION['logedBranchAdminInfo']['adminType'];

        $PRSerialNumber = $POST['prCode'];

        $PRID = $POST['prid'];

        //   die();

        $uid = uniqid();



        $lastQuery = "SELECT * FROM `" . ERP_RFQ_LIST . "` WHERE `branch_id` = $branch_id AND `company_id` = $company_id  AND prId=$PRID  ORDER BY `rfqId` DESC LIMIT 1";

        $last = queryGet($lastQuery);



        $lastRow = $last['data'] ?? "";

        $lastsl = $lastRow['rfqCode'] ?? "";

        $rfqCode = getRFQSerialNumber($PRSerialNumber, $lastsl);



        //exit();

        $created_at = date('Y-m-d H:i:s');

        $ins = "INSERT INTO `" . ERP_RFQ_LIST . "` SET 
        `rfqCode` = '$rfqCode' , 
        `prId` = '$PRID' , 
        `prCode` = '$PRSerialNumber' ,
        `created_at` = '$created_at', 
        `created_by` ='$created_by',
         `branch_id` ='$branch_id' , 
         `company_id` = '$company_id' ,
          `location_id` = '$location_id' , 
          `uid` = '$uid'";

        $insConn = queryInsert($ins);

        if ($insConn['status'] == "success") {



            $rfqselect = "SELECT * FROM `" . ERP_RFQ_LIST . "` WHERE `uid`= '" . $uid . "' AND `company_id` = '$company_id' AND `location_id` = '$location_id' AND `branch_id`='$branch_id'";

            $getrfqid = queryGet($rfqselect);

            $RFQID = $getrfqid['data']['rfqId'];



            $listItem = $POST['itemId'];

            foreach ($listItem as $item) {



                $itemCodeget = "SELECT * FROM `erp_inventory_items` WHERE itemId = '$item' AND company_id = '$company_id' AND location_id = '$location_id' AND branch = '$branch_id'";

                $item_code = queryGet($itemCodeget);

                $ItemCode = $item_code['data']['itemCode'];





                $insert = "INSERT INTO `erp_rfq_items` SET 
                `rfqId`='$RFQID',
                 `prId`='$PRID',
                  `ItemId`='$item',
                   `rfqCode`='$rfqCode', 
                   `prCode`='$PRSerialNumber', 
                   `itemCode`='$ItemCode' ,
                    `branch_id` ='$branch_id' ,
                     `company_id` = '$company_id' ,
                      `location_id` = '$location_id'";

                $returnData = queryInsert($insert);

                $auditTrail['action_data']['items'][$ItemCode]['rfqId'] = $RFQID;
                $auditTrail['action_data']['items'][$ItemCode]['prId'] = $PRID;
                $auditTrail['action_data']['items'][$ItemCode]['ItemId'] = $item;
                $auditTrail['action_data']['items'][$ItemCode]['rfqCode'] = $rfqCode;
                $auditTrail['action_data']['items'][$ItemCode]['prCode'] = $PRSerialNumber;
                $auditTrail['action_data']['items'][$ItemCode]['itemCode'] = $ItemCode;
                $auditTrail['action_data']['items'][$ItemCode]['branch_id'] = $branch_id;
                $auditTrail['action_data']['items'][$ItemCode]['company_id'] = $company_id;
                $auditTrail['action_data']['items'][$ItemCode]['location_id'] = $location_id;
            }




            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_RFQ_LIST;
            $auditTrail['basicDetail']['column_name'] = 'rfqId'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $RFQID;  // primary key
            $auditTrail['basicDetail']['document_number'] = $rfqCode;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['party_id'] = 0;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = ' RFQ Created';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';


            $auditTrail['action_data']['basic Detail']['rfqCode'] = $rfqCode;
            $auditTrail['action_data']['basic Detail']['prId'] = $PRID;
            $auditTrail['action_data']['basic Detail']['prCode'] = $PRSerialNumber;
            $auditTrail['action_data']['basic Detail']['created_at'] = $created_at;
            $auditTrail['action_data']['basic Detail']['created_by'] = $created_by;
            $auditTrail['action_data']['basic Detail']['uid'] = $uid;





            $auditTrailreturn = generateAuditTrail($auditTrail);
            return $returnData;
        } else {

            return $insConn;
        }

        return $insConn;
    }

    function addBranchAddtoRFQ($POST)
    {
        // console($POST);
        // exit();
        $returnData = [];
        if (empty($POST['itemObj'])) {

            $returnData['status'] = "warning";

            $returnData['message'] = "No Item Selected";



            return $returnData;
        }


        global $dbCon;

        global $company_id;
        global $branch_id;
        global $location_id;



        $created_by = $_SESSION['logedBranchAdminInfo']['adminId'] . "|" . $_SESSION['logedBranchAdminInfo']['adminType'];

        $PRSerialNumber = $POST['prid'];

        $PRID = $POST['prCode'];

        // echo $PRID;
        // exit();

        //   die();

        $uid = uniqid();



        $lastQuery = "SELECT * FROM `" . ERP_RFQ_LIST . "` WHERE `branch_id` = $branch_id AND `company_id` = $company_id  AND prId=$PRID  ORDER BY `rfqId` DESC LIMIT 1";

        $last = queryGet($lastQuery);



        $lastRow = $last['data'] ?? "";

        $lastsl = $lastRow['rfqCode'] ?? "";

        $rfqCode = getRFQSerialNumber($PRSerialNumber, $lastsl);



        //exit();

        $created_at = date('Y-m-d H:i:s');

        $ins = "INSERT INTO `" . ERP_RFQ_LIST . "` SET 
        `rfqCode` = '$rfqCode' , 
        `prId` = '$PRID' , 
        `prCode` = '$PRSerialNumber' ,
        `created_at` = '$created_at', 
        `created_by` ='$created_by',
         `branch_id` ='$branch_id' , 
         `company_id` = '$company_id' ,
          `location_id` = '$location_id' , 
          `uid` = '$uid'";

        $insConn = queryInsert($ins);

        if ($insConn['status'] == "success") {



            $rfqselect = "SELECT * FROM `" . ERP_RFQ_LIST . "` WHERE `uid`= '" . $uid . "' AND `company_id` = '$company_id' AND `location_id` = '$location_id' AND `branch_id`='$branch_id'";

            $getrfqid = queryGet($rfqselect);

            $RFQID = $getrfqid['data']['rfqId'];



            $listItem = $POST['itemObj'];


            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_RFQ_LIST;
            $auditTrail['basicDetail']['column_name'] = 'rfqId'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $RFQID;  // primary key
            $auditTrail['basicDetail']['document_number'] = $rfqCode;
            $auditTrail['basicDetail']['party_id'] = 0;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = ' RFQ Created';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($ins);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';
            $auditTrail['action_data']['basic Detail']['RFQ_Code'] = $rfqCode;
            // $auditTrail['action_data']['basic Detail']['prId'] = $PRID;
            $auditTrail['action_data']['basic Detail']['PR_Code'] = $PRSerialNumber;
            $auditTrail['action_data']['basic Detail']['Created_At'] = formatDateWeb($created_at);
            $auditTrail['action_data']['basic Detail']['Created_By'] = getCreatedByUser($created_by);
            // $auditTrail['action_data']['basic Detail']['uid'] = $uid;

            foreach ($listItem as $item) {


                $itemCodeget = "SELECT * FROM `erp_inventory_items` WHERE itemId = ".$item['itemId']." AND company_id = '$company_id' AND location_id = '$location_id' AND branch = '$branch_id'";

                $item_code = queryGet($itemCodeget);

                $ItemCode = $item_code['data']['itemCode'];


                $insert = "INSERT INTO `erp_rfq_items` SET 
                `rfqId`='$RFQID',
                 `prId`='$PRID',
                  `ItemId`=".$item['itemId'].",
                   `rfqCode`='$rfqCode', 
                   `prCode`='$PRSerialNumber', 
                   `itemCode`='$ItemCode' ,
                    `branch_id` ='$branch_id' ,
                     `company_id` = '$company_id' ,
                     `deliverySceduleId`=".$item['deliveryId'].",
                      `location_id` = '$location_id'";

                $returnData = queryInsert($insert);

                // $auditTrail['action_data']['items'][$ItemCode]['rfqId'] = $RFQID;
                // $auditTrail['action_data']['items'][$ItemCode]['prId'] = $PRID;
                // $auditTrail['action_data']['items'][$ItemCode]['ItemId'] = $item['itemId'];
                $auditTrail['action_data']['items'][$ItemCode]['RfQ_Code'] = $rfqCode;
                $auditTrail['action_data']['items'][$ItemCode]['PR_Code'] = $PRSerialNumber;
                $auditTrail['action_data']['items'][$ItemCode]['Item_Code'] = $ItemCode;
                // $auditTrail['action_data']['items'][$ItemCode]['branch_id'] = $branch_id;
                // $auditTrail['action_data']['items'][$ItemCode]['company_id'] = $company_id;
                // $auditTrail['action_data']['items'][$ItemCode]['deliverySceduleId'] = $item['deliveryId'];
                // $auditTrail['action_data']['items'][$ItemCode]['location_id'] = $location_id;
            }




            


            





            $auditTrailreturn = generateAuditTrail($auditTrail);
            return $returnData;
        } else {

            return $insConn;
        }

        // return $insConn;
    }



    function fetchBranchRFQListing()

    {

        $returnData = [];

        global $dbCon;

        global $company_id;

        global $branch_id;

        global $location_id;

        // $ins = "SELECT * FROM `".ERP_RFQ_LIST."` as rfq,`".ERP_BRANCH_PURCHASE_REQUEST."` as pr WHERE rfq.prCode=pr.prCode";

        $ins = "SELECT * FROM `" . ERP_RFQ_LIST . "` as rfq LEFT JOIN `" . ERP_BRANCH_PURCHASE_REQUEST . "` as pr ON rfq.prId = pr.purchaseRequestId WHERE rfq.branch_id= '$branch_id' AND rfq.company_id= '$company_id' AND rfq.location_id = '$location_id'";

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

        //console($returnData);

        return $returnData;
    }



    function fetchBranchRFQItems($id)

    {

        $returnData = [];

        global $dbCon;

        global $company_id;

        global $branch_id;

        global $location_id;



        $ins = "SELECT * FROM erp_rfq_items LEFT JOIN erp_inventory_items ON erp_inventory_items.itemId = erp_rfq_items.ItemId WHERE erp_rfq_items.rfqId = '" . $id . "' ";

        //console($ins);

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

        //console($returnData);

        return $returnData;
    }


    
    function fetchBranchRFQItemswithQty($id)

    {

        $returnData = [];

        global $dbCon;

        global $company_id;

        global $branch_id;

        global $location_id;



        // $sql = "SELECT * FROM `erp_rfq_items` as rfqItems 
        // LEFT JOIN `erp_branch_purchase_request_items` as prItems
        // on rfqItems.prId=prItems.prId
        //  WHERE rfqItems.rfqId = '" . $id . "' and rfqItems.company_id=$company_id ";

        // changed sql
        $sql="SELECT DISTINCT invItems.itemCode,invItems.itemName,invItems.itemId,rfqItems.rfqId ,rfqItems.prId FROM erp_rfq_items as rfqItems LEFT JOIN erp_inventory_items as invItems ON invItems.itemId = rfqItems.ItemId WHERE rfqItems.rfqId = '".$id."' AND rfqItems.company_id = '".$company_id."'";
        
        // $oldSql = "SELECT * FROM erp_rfq_items LEFT JOIN erp_inventory_items ON erp_inventory_items.itemId = erp_rfq_items.ItemId WHERE erp_rfq_items.rfqId = '" . $id . "' ";

        return queryGet($sql,true);
    }



    function fetchBranchVendor()
    {

        $returnData = [];

        global $dbCon;

        global $company_id;

        global $branch_id;

        global $location_id;

        $ins = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id` = '$company_id' AND `company_branch_id`='$branch_id' AND `location_id` = '$location_id' AND `vendor_status` = 'active'";

        //console($ins);

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

        //console($returnData);

        return $returnData;
    }

    function addVendorList($POST)
    {

        $returnData = [];

        global $dbCon;

        $rfqId = $POST['rfqId'];

        $rfqSql = "SELECT * FROM `" . ERP_RFQ_LIST . "` WHERE rfqId=$rfqId ";

        $rfq = queryGet($rfqSql);

        $rfqRow = $rfq['data'] ?? "";

        $rfqCode = $rfqRow['rfqCode'];

        $VendorId = $POST['vendorId'];

        $type = "existing";

        //console($rfqCode);



        foreach ($VendorId as $vendor) {



            $vendorSql = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE vendor_code=$vendor ";

            $vendorConn = queryGet($vendorSql);

            $vendorRow = $vendorConn['data'] ?? "";

            $vendor_id = $vendorRow['vendor_id'];

            $name = $vendorRow['trade_name'];

            $email = $vendorRow['vendor_authorised_person_email'];



            $insert = "INSERT INTO `erp_rfq_vendor_list`

                        SET

                        `rfqCode`='" . $rfqCode . "',

                          `vendorId`='" . $vendor_id . "',

                          `rfqItemListId`='" . $rfqId . "',

                          `vendorCode`='" . $vendor . "',

                          `vendor_type` = '" . $type . "',

                          `vendor_name` = '" . $name . "',

                          `vendor_email` ='" . $email . "'

              ";

            $returnData = queryInsert($insert);
        }

        return $returnData;
    }

    function fetchRFQVendor($id)
    {

        $returnData = [];

        global $dbCon;



        $ins = "SELECT * FROM `erp_rfq_vendor_list` WHERE rfqItemListId=$id ";

        //console($ins);

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

        //console($returnData);

        return $returnData;
    }

    function fetchexistingRFQVendor($id)
    {

        $returnData = [];

        global $dbCon;



        $ins = "SELECT * FROM `erp_rfq_vendor_list` WHERE rfqItemListId=$id AND vendor_type='existing' ";

        //console($ins);

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

        //console($returnData);

        return $returnData;
    }



    function deleteRfqVendor($GET, $id)
    {

        $returnData = [];

        global $dbCon;



        $del = "DELETE FROM `erp_rfq_vendor_list`  WHERE rfqVendorId=$id ";

        $returnData = queryDelete($del);

        return $returnData;
    }



    function addOtherVendorList($POST)
    {

        //console($POST);

        // exit();



        $returnData = [];

        global $dbCon;



        $rfqId = $POST['rfqId'];

        $rfqSql = "SELECT * FROM `" . ERP_RFQ_LIST . "` WHERE rfqId=$rfqId ";

        $rfq = queryGet($rfqSql);

        $rfqRow = $rfq['data'] ?? "";

        $rfqCode = $rfqRow['rfqCode'];

        $type = "others";

        //  console($rfqRow);

        // console($_POST['OthersVendor']);

        foreach ($_POST['OthersVendor'] as $others) {

            $name_other =  $others['name'];

            $email_other = $others['email'];

            //  console($name_other);

            //  console($email_other);

            // console($type);

            // console($rfqCode);

            // console($rfqId);

            $insert = "INSERT INTO `erp_rfq_vendor_list`

SET

`rfqCode`='" . $rfqCode . "',

  `rfqItemListId`='" . $rfqId . "',

  `vendor_type` = '" . $type . "',

  `vendor_name` = '" . $name_other . "',

  `vendor_email` ='" . $email_other . "'

";



            $returnData = queryInsert($insert);

            // $insOtherConn = queryInsert($insOther);

        }

        return $returnData;
    }



    function fetchBranchRFQSingle($id)
    {

        //  console($id);

        $returnData = [];

        global $dbCon;

        global $company_id;

        global $branch_id;

        global $location_id;

        // $ins = "SELECT * FROM `erp_rfq_list` as rfq  WHERE rfqId=$id ";

        $ins = "SELECT * FROM `" . ERP_RFQ_LIST . "` as rfq LEFT JOIN `" . ERP_BRANCH_PURCHASE_REQUEST . "` as pr ON rfq.prId = pr.purchaseRequestId WHERE rfq.rfqId = '$id'";

        //console($ins);

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

        //console($returnData);

        return $returnData;
    }

    function updatePR($POST)
    {

        $returnData = [];

        global $dbCon;

        global $company_id;

        global $branch_id;

        global $location_id;

        global $created_by;

        global $updated_by;

        // console($POST);
        // exit();


        $isValidate = validate($POST, [

            "expDate" => "required",



        ], [

            "expDate" => "Enter Expected Date",



        ]);



        if ($isValidate["status"] != "success") {

            $returnData['status'] = "warning";

            $returnData['message'] = "Invalid form inputes";

            $returnData['errors'] = $isValidate["errors"];

            return $returnData;
        }





        // $company_id = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];

        //$branch_id = $_SESSION['logedBranchAdminInfo']['fldAdminBranchId'];

        // $customerId = $POST['customerId'];

        $expectedDate = $POST['expDate'];

        $pr_date = $POST['prDate'];

        $refNo = $POST['refNo'];

        $description = $POST['description'];
        $pr_id = $POST['prId'];

        $pr_type = $POST['pr_type'];
        $select_pr = queryGet("SELECT * FROM `" . ERP_BRANCH_PURCHASE_REQUEST . "` WHERE `purchaseRequestId`=$pr_id AND `company_id` = $company_id");
        $pr_code = $select_pr['data']['prCode'];
        $pr_type_val = $select_pr['data']['pr_type'];
        $description_val = $select_pr['data']['description'] ?? "-";
        $created_by_val = $select_pr['data']['created_by'] ?? "-";

        $validitydate = $POST['validitydate'];
    
        if ($validitydate<date('Y-m-d')) {

            $returnData['status'] = "warning";

            $returnData['message'] = "Validation Date Wrong";

            return $returnData;
        }

        // $customerPO = $POST['customerPO'];
        // *************** //

        // $created_by = $_SESSION['logedBranchAdminInfo']['adminId']."|".$_SESSION['logedBranchAdminInfo']['adminType'];

        // $lastQuery = "SELECT * FROM ".ERP_BRANCH_PURCHASE_REQUEST." WHERE `company_id` = '$company_id' AND `branch_id` = '$branch_id' AND `location_id` = '$location_id' ORDER BY `purchaseRequestId` DESC LIMIT 1";

        // $last = queryGet($lastQuery);

        // $lastRow = $last['data'] ?? "";

        // $lastPrId = $lastRow['prCode'] ?? "";

        // $prCode = getPRSerialNumber($lastPrId);

        //exit();

        $created_at = date('Y-m-d H:i:s');

        $ins = "UPDATE `" . ERP_BRANCH_PURCHASE_REQUEST . "`

             SET

               `expectedDate`='$expectedDate',

               `description`='$description',

               `branch_id` = '$branch_id',

               `company_id` = '$company_id',
               `validityperiod`  = '$validitydate',
               `pr_date` = '$pr_date',

               `location_id` = '$location_id',

               

               `updated_by` = '$updated_by',

               `refNo`='$refNo' WHERE `purchaseRequestId`=$pr_id";


        $insConn = queryUpdate($ins);

        if ($insConn['status'] == "success") {

            $lastId = $dbCon->insert_id;

            $listItem = $POST['listItem'];

            foreach ($listItem as $item) {

                $pritemID = $item['pritemId'];
                $insert = "UPDATE `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "`

                        SET

                        `prId`='$pr_id',

                        `company_id`='$company_id',

                        `branch_id`='$branch_id',

                        `location_id`='$location_id',

                          `itemId`='" . $item['itemId'] . "',

                          `itemCode`='" . $item['itemCode'] . "',

                          `remainingQty`='" . $item['qty'] . "',

                          `uom`='" . $item['uom'] . "',

                          `itemName`='" . $item['note'] . "',

                          `itemName`='" . $item['itemName'] . "' WHERE `prItemId`=$pritemID

              ";



                $returnDataUpdate = queryUpdate($insert);

                $auditTrail['action_data']['Item Details'][$item['itemCode']]['prId'] = $lastId;
                $auditTrail['action_data']['Item Details'][$item['itemCode']]['itemId'] = $item['itemId'];
                $auditTrail['action_data']['Item Details'][$item['itemCode']]['itemCode'] = $item['itemCode'];
                $auditTrail['action_data']['Item Details'][$item['itemCode']]['itemQuantity'] = $item['qty'];
                $auditTrail['action_data']['Item Details'][$item['itemCode']]['uom'] = $item['uom'];
                $auditTrail['action_data']['Item Details'][$item['itemCode']]['itemName'] = $item['itemName'];
            }



            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'EDIT';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_PURCHASE_REQUEST;
            $auditTrail['basicDetail']['column_name'] = 'purchaseRequestId'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $pr_id;  // primary key
            $auditTrail['basicDetail']['document_number'] = $pr_code;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['party_id'] = 0;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = ' PR updated';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Update';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';


            $auditTrail['action_data']['Purchase Request Details']['Expected_Date'] = formatDateORDateTime($expectedDate);
            $auditTrail['action_data']['Purchase Request Details']['PR_Date'] = formatDateWeb($pr_date);
            $auditTrail['action_data']['Purchase Request Details']['Description'] = $description_val ?? "-";
            $auditTrail['action_data']['Purchase Request Details']['PR_Code'] = $pr_code;
            $auditTrail['action_data']['Purchase Request Details']['Created_By'] = getCreatedByUser($created_by_val);
            $auditTrail['action_data']['Purchase Request Details']['Updated_By'] = getCreatedByUser($updated_by);
            $auditTrail['action_data']['Purchase Request Details']['PR_Type'] = $pr_type_val;
            $auditTrail['action_data']['Purchase Request Details']['Ref_No'] = $refNo;




            $auditTrailreturn = generateAuditTrail($auditTrail);

            if($returnDataUpdate['status'] == 'success'){
                $returnData['status'] = 'Success';
                $returnData['message'] = 'Success';

            }
          else{
            $returnData['status'] = 'Warning';
            $returnData['message'] = 'Failed';
          }
        
        } else {

            $returnData['status'] = 'Warning';
            $returnData['message'] = 'Failed';
        }

        return $returnData;
    }
}
