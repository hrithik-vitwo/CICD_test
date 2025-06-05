<?php
class BranchPr
{
    function addBranchPr($POST)
    {
        $returnData = [];
        global $dbCon;
    	global $company_id;
		global $branch_id;
		global $location_id;
		global $created_by;
		global $updated_by;

        $isValidate = validate($POST, [ 
            "expDate" => "required",
            "description" => "required"
        ], [
            "expDate" => "Enter Expected Date",
            "description" => "Enter Description"
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
        $refNo = $POST['refNo'];
        $description = $POST['description'];
        $pr_type = $POST['pr_type'];
     
       // $customerPO = $POST['customerPO'];

        // *************** //
   // $created_by = $_SESSION['logedBranchAdminInfo']['adminId']."|".$_SESSION['logedBranchAdminInfo']['adminType'];
  $lastQuery = "SELECT * FROM ".ERP_BRANCH_PURCHASE_REQUEST." WHERE `company_id` = '$company_id' AND `branch_id` = '$branch_id' AND `location_id` = '$location_id' ORDER BY `purchaseRequestId` DESC LIMIT 1";
  $last = queryGet($lastQuery);
  $lastRow = $last['data'] ?? "";
  $lastPrId = $lastRow['prCode'] ?? "";
  $prCode = getPRSerialNumber($lastPrId);
  //exit();
    $created_at = date('Y-m-d H:i:s'); 
       echo  $ins = "INSERT INTO `" . ERP_BRANCH_PURCHASE_REQUEST . "`
                 SET
                   `expectedDate`='$expectedDate',
                   `description`='$description',
                   `prCode` = '$prCode',
                   `branch_id` = '$branch_id',
                   `company_id` = '$company_id',
                   `location_id` = '$location_id',
                   `created_by` = '$created_by',
                   `updated_by` = '$updated_by',
                   `pr_type` = $pr_type,
                   `refNo`='$refNo'";
        $insConn = queryInsert($ins);
        if($insConn['status'] == "success") {
            $lastId = $dbCon->insert_id;
            $listItem = $POST['listItem'];
            foreach ($listItem as $item) {
                        $insert = "INSERT INTO `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "`
                            SET
                            `prId`='" . $lastId . "',
                            `company_id`='".$company_id."',
                            `branch_id`='".$branch_id."',
                            `location_id`='".$location_id."',
                              `itemId`='" . $item['itemId'] . "',
                              `itemCode`='" . $item['itemCode'] . "',
                              `itemQuantity`='" . $item['qty'] . "',
                              `uom`='" . $item['uom'] . "',
                              `itemName`='" . $item['itemName'] . "'
                  ";
                  $returnData = queryInsert($insert);
            }
            return $returnData;
        }else{
            return $insConn;
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
     
        $ins = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "` as item, `" . ERP_INVENTORY_MASTR_UOM . "` as uom WHERE item.uom=uom.uomId AND  `prId`='$id'";
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
   function addBranchRFQ($POST){
   
   //console($POST);
   //exit();
 
   $returnData = [];
   global $dbCon;

    $company_id = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
    $branch_id = $_SESSION['logedBranchAdminInfo']['fldAdminBranchId'];
    // $location_id = $_SESSION['loggedBranchAdminInfo']['fldAdminLocationId'];
    global $location_id;
    $created_by = $_SESSION['logedBranchAdminInfo']['adminId']."|".$_SESSION['logedBranchAdminInfo']['adminType'];
  $PRSerialNumber = $POST['prCode'];
   $PRID = $POST['prid'];
//   die();
   $uid = uniqid();

    $lastQuery = "SELECT * FROM `".ERP_RFQ_LIST."` WHERE `branch_id` = $branch_id AND `company_id` = $company_id ORDER BY `rfqId` DESC LIMIT 1";
  $last = queryGet($lastQuery);

  $lastRow = $last['data'] ?? "";
  $lastsl = $lastRow['rfqCode'] ?? ""; 
   $rfqCode = getRFQSerialNumber($PRSerialNumber,$lastsl);

//exit();
    $created_at = date('Y-m-d H:i:s'); 
    $ins = "INSERT INTO `".ERP_RFQ_LIST."` SET `rfqCode` = '$rfqCode' , `prId` = '$PRID' , `prCode` = '$PRSerialNumber' ,`created_at` = '$created_at', `created_by` ='$created_by', `branch_id` ='$branch_id' , `company_id` = '$company_id' , `location_id` = '$location_id' , `uid` = '$uid'";
        $insConn = queryInsert($ins);
       if($insConn['status'] == "success") {

        $rfqselect = "SELECT * FROM `".ERP_RFQ_LIST."` WHERE `uid`= '".$uid."' AND `company_id` = '$company_id' AND `location_id` = '$location_id' AND `branch_id`='$branch_id'";
        $getrfqid = queryGet($rfqselect);
        $RFQID = $getrfqid['data']['rfqId'];

            $listItem = $POST['itemId'];
            foreach ($listItem as $item) {
             
                $itemCodeget = "SELECT * FROM `erp_inventory_items` WHERE itemId = '$item' AND company_id = '$company_id' AND location_id = '$location_id' AND branch = '$branch_id'";
                $item_code = queryGet($itemCodeget);
                $ItemCode = $item_code['data']['itemCode'];


                $insert = "INSERT INTO `erp_rfq_items` SET `rfqId`='$RFQID', `prId`='$PRID', `ItemId`='$item', `rfqCode`='$rfqCode', `prCode`='$PRSerialNumber', `itemCode`='$ItemCode' , `branch_id` ='$branch_id' , `company_id` = '$company_id' , `location_id` = '$location_id'";
     $returnData = queryInsert($insert);

            }
            
            return $returnData;
       }
        else{
          return $insConn;
        }
       return $insConn;

   } 

   function fetchBranchRFQListing()
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
		global $branch_id;
		global $location_id;
        // $ins = "SELECT * FROM `".ERP_RFQ_LIST."` as rfq,`".ERP_BRANCH_PURCHASE_REQUEST."` as pr WHERE rfq.prCode=pr.prCode";
        $ins = "SELECT * FROM `".ERP_RFQ_LIST."` as rfq LEFT JOIN `".ERP_BRANCH_PURCHASE_REQUEST."` as pr ON rfq.prId = pr.purchaseRequestId WHERE rfq.branch_id= '$branch_id' AND rfq.company_id= '$company_id' AND rfq.location_id = '$location_id'";
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
     
        $ins = "SELECT * FROM erp_rfq_items LEFT JOIN erp_inventory_items ON erp_inventory_items.itemCode = erp_rfq_items.itemCode WHERE erp_rfq_items.rfqCode = '".$id."'";
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

    function fetchBranchVendor(){
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
    function addVendorList($POST){
        $returnData = [];
        global $dbCon;
     $rfqId = $POST['rfqId'];
     $rfqSql = "SELECT * FROM `".ERP_RFQ_LIST."` WHERE rfqId=$rfqId ";
     $rfq = queryGet($rfqSql);
  $rfqRow = $rfq['data'] ?? "";
  $rfqCode = $rfqRow['rfqCode'];
$VendorId = $POST['vendorId'];
$type = "existing";
//console($rfqCode);
       
        foreach ($VendorId as $vendor) {

              $vendorSql = "SELECT * FROM `".ERP_VENDOR_DETAILS."` WHERE vendor_code=$vendor ";
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
                          `vendor_type` = '".$type."',
                          `vendor_name` = '".$name."',
                          `vendor_email` ='".$email."'
              ";
             $returnData = queryInsert($insert);
        }
        return $returnData;
    }
    function fetchRFQVendor($id){
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
    function fetchexistingRFQVendor($id){
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

function deleteRfqVendor($GET,$id){
    $returnData = [];
        global $dbCon;

        $del = "DELETE FROM `erp_rfq_vendor_list`  WHERE rfqVendorId=$id ";
        $returnData = queryDelete($del);
        return $returnData;


}

function addOtherVendorList($POST){
//console($POST);
// exit();

$returnData = [];
global $dbCon;

$rfqId = $POST['rfqId'];
$rfqSql = "SELECT * FROM `".ERP_RFQ_LIST."` WHERE rfqId=$rfqId ";
$rfq = queryGet($rfqSql);
$rfqRow = $rfq['data'] ?? "";
$rfqCode = $rfqRow['rfqCode'];
$type = "others";
//  console($rfqRow);
// console($_POST['OthersVendor']);
foreach($_POST['OthersVendor'] as $others){
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
  `vendor_type` = '".$type."',
  `vendor_name` = '".$name_other."',
  `vendor_email` ='".$email_other."'
";

$returnData = queryInsert($insert);
    // $insOtherConn = queryInsert($insOther);
}
        return $returnData;

}

function fetchBranchRFQSingle($id){
  //  console($id);
  $returnData = [];
global $dbCon;
global $company_id;
global $branch_id;
global $location_id;
// $ins = "SELECT * FROM `erp_rfq_list` as rfq  WHERE rfqId=$id ";
$ins = "SELECT * FROM `" . ERP_RFQ_LIST . "` as rfq LEFT JOIN `".ERP_BRANCH_PURCHASE_REQUEST."` as pr ON rfq.prId = pr.purchaseRequestId WHERE rfq.rfqId = '$id'";
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
}
 

