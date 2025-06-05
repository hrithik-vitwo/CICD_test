<?php
class BranchPo
{ 
    function addBranchPo($POST,$branch_id,$company_id,$location_id)
    {
        $returnData = [];
        global $dbCon;
     
//     console($POST['OthersCost']);
// exit();

// console($_POST['OthersCost']);
// console($_POST['FreightCost']);
//  foreach($_POST['OthersCost'] as $others){
//     console($others['txt']);
//     console($others['amount']);
//  }
//console($_POST['FreightCost']);
//  foreach($_POST['FreightCost'] as $frieght){
//    console($frieght['txt']);
//  }
//exit();
$isValidate = validate($POST, [	
    "vendorId" => "required",	
    "deliveryDate" => "required",	
    "podatecreation" => "required",	
    "listItem" => "required"	
  	
]);	
if ($isValidate["status"] != "success") {	
    $returnData['status'] = "warning";	
    $returnData['message'] = "Invalid form inputes";	
    $returnData['errors'] = $isValidate["errors"];	
    return $returnData;	
}
        $vendorId = $POST['vendorId'];
        $deliveryDate = $POST['deliveryDate']; 
        $costCenter = $POST['costCenter'] ?? 0;
        $refNo = $POST['refNo'];
        $poDate = $POST['podatecreation'];
        $use_type = $POST['usetypesDropdown'];
        $po_type = $POST['potypes'];
        $inco = $POST['domestic']?? "";  
        $pr_id = $POST['pr_id'] ?? 0;
        // ***************
        $lastQuery = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` ORDER BY `po_id` DESC LIMIT 1";
        $last = queryGet($lastQuery);
        $lastRow = $last['data'] ?? "";
        $lastPoNo = $lastRow['po_number'] ?? "";
        $returnPoNo = getPoSerialNumber($lastPoNo);  
        // ***************
// console($returnPoNo);
// exit();
      $ins = "INSERT INTO `erp_branch_purchase_order` SET `po_number`='$returnPoNo', `vendor_id`='$vendorId', `delivery_date`='$deliveryDate', `cost_center`='$costCenter', `ref_no`='$refNo',`po_date`='$poDate',`use_type`='$use_type',`po_type`='$po_type',`inco_type`='$inco',`branch_id`='$branch_id',`company_id`='$company_id', `pr_id`='$pr_id',`location_id`='$location_id',`bill_address`='$location_id',`ship_address`='$location_id' ";

     
      $insConn = queryInsert($ins);
     
      if($insConn['status'] == "success") {
         
        $lastId = $insConn['insertedId'];



       // $lastId = $id;
        $listItem = $POST['listItem'];

         //$totalDiscount = 0;

        $totalItems = count($listItem);
        $totalAmount = 0;
        $i = 1;
        if($POST['FreightCost']['l1']['amount'] != ""){
        foreach($_POST['FreightCost'] as $freight){
            $amount =  $freight['service_amount'];
            $txt = $freight['service_desc'];
            $service = $freight['service_purchase_id'];
           $gst = $freight['gst'] ?? "";
            $total = $freight['service_amount'];
             $rcm = $freight['rcm'] ?? "";
            $type = "freight";

            $count = $i++;
            $serv_po = $returnPoNo."/".$count;
            $insFrieght = "INSERT INTO `erp_service_po` SET `poNumber`='".$serv_po."', `service_name`='".$txt."',`service_amount`='".$amount."',`service_type`='".$type."',`service_description`='".$service."',`service_gst`='".$gst."',`service_total`='".$total."',`service_rcm`='".$rcm."',`branch_id`='".$branch_id."',`company_id`='".$company_id."',`location_id`='".$location_id."'";
           // echo $insFrieght;
            $insFrieghtConn = queryInsert($insFrieght);
        }

    }
    if($POST['OthersCost']['13']['amount'] != ""){
        foreach($_POST['OthersCost'] as $others){
            $amount_other =  $others['service_amount'];
                        $txt_other = $others['service_desc'];
                        $service = $others['service_purchase_id'];
                       $gst = $others['gst'] ?? "";
                        $total = $others['service_amount'];
                         $rcm = $others['rcm'] ?? "";
                        $type = "others";
            $insOther = "INSERT INTO `erp_branch_po_transport_detail` SET `poNumber`='".$returnPoNo."', `vendor_name`='".$txt_other."',`transportationAmount`='".$amount_other."',`transportation_type`='".$type."',`service_description`='".$service."',`gst_amount`='".$gst."',`total_amount`='".$total."',`rcm`='".$rcm."' ";
           // echo $insOther;
            $insOtherConn = queryInsert($insOther);
        }
        
    }


        foreach ($listItem as $item) {
        //  console($item);
        //     exit();
              $countI = $i++;
              $totalPrice =  $item['qty']*$item['unitPrice'];
            
           $insItem = "INSERT INTO `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "`
              
                  SET
                    `po_id`='" .$lastId. "',
                    `lineNo`='" .$countI. "',
                    `inventory_item_id`='" . $item['itemId'] . "',
                    `itemCode`='" . $item['itemCode'] . "',
                    `itemName`='" . $item['itemName'] . "',
                    `unitPrice`='" . $item['unitPrice'] . "',
                    `qty`='" . $item['qty'] . "',
                    `total_price` = '".$totalPrice."',
                    `uom`='" . $item['uom'] . "'
                ";
              
                  $insItemConn = queryInsert($insItem);
                  $lastItemId = $insItemConn['insertedId'];
                  
                  if($insItemConn['status'] == "success") {
                  $returnData['itemLastID'] = $dbCon->insert_id;
                  $tot = $item['unitPrice'] * $item['qty'];
                  $totalAmount = $totalAmount + $tot;
                  // console($item['deliverySchedule']);
                  foreach ($item['deliverySchedule'] as $delItem) {
                    if($delItem['multiDeliveryDate'] != ""){
                        $date = $deliveryDate;
                        $quantity = $item['qty'];
                    }
                    else{
                            $date=$delItem['multiDeliveryDate'];
                            $quantity = $delItem['quantity'];
                    }
                      $insDeli = "INSERT INTO `" . ERP_BRANCH_PURCHASE_ORDER_DELIVERY_SCHEDULE . "` 
                      SET 
                      `po_item_id`='" .$lastItemId. "',
                      `delivery_date`='" .$date . "',
                      `deliveryStatus`='pending',
                      `qty`='" . $quantity . "'";
  
                      $insDeliConn = queryInsert($insDeli);
               if($insDeliConn['status'] == "success") {
                  
                        $returnData = $insDeliConn;
                      } else {
                          $returnData = $insDeliConn;
                      }
                  } 
              } else {
                  $returnData = $insConn;
              }
          }



          $poItem = "SELECT SUM(`unitPrice`) AS 'amount' , SUM(`qty`) AS 'qty' FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` WHERE `po_id`=$lastId  ";
          $poConn = queryGet($poItem);
          $poData = $poConn['data'] ?? "";
          $amount = $poData['amount'];
          //$quantity = $poData['qty'];
  
          $sumtransQuery = "SELECT SUM(`transportationAmount`) as amount FROM `erp_branch_po_transport_detail` WHERE `poNumber`='".$returnPoNo."'";
          $transQuery = queryGet($sumtransQuery);
          $transData = $transQuery['data'] ?? "";
        //  console($transQuery);
         // exit();
          $total_transport_amount = $transData['amount'];
  
       $poItemQuery = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` WHERE `po_id`= '".$lastId."' ";
    //  die();
          $poQuery = queryGet($poItemQuery,true);
          $poItemData = $poQuery['data'];
       //   console($poItemData);
        
         //$poItemId = $poItemData['po_item_id'];
         //console($poItemId);
          foreach($poItemData as $item)
       {
        
              $item_total_price = $item['total_price'];
             
          $total_transport_cost =  ($total_transport_amount/$amount)*$item_total_price;
         $insert_trans = "UPDATE `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "`  SET `total_transport_cost`='" . $total_transport_cost . "' WHERE `po_item_id`='".$item['po_item_id']."'";
         $updateItemiConn = queryInsert($insert_trans);
         $returnData =  $updateItemiConn;
  
       }
  
          $updateDeli = "UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` 
                          SET 
                              `totalItems`='" . $totalItems . "',
                              `totalAmount`='" . $totalAmount . "' WHERE po_id=" . $lastId . "";
                              $updateDeliConn = queryInsert($updateDeli);
                              $returnData =  $updateDeliConn;
                              
  
          return $returnData;
         
         
         
        }
        else{
          return $insConn;
        }
        
  
          return $returnData;
    }

    // function addBranchPoItems($POST, $id)
    // {
    //     $returnData = [];
    //     global $dbCon;
    //     $lastId = $id;
    //     $listItem = $POST['listItem'];
    //     $totalDiscount = 0;
    //     $totalItems = count($listItem);
    //     $totalAmount = 0;
    //     $i = 1;
    //     foreach ($listItem as $item) {
    //         $countI = $i++;
    //         $ins = "INSERT INTO `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "`
    //             SET
    //               `so_id`='$lastId',
    //               `lineNo`='$countI',
    //               `inventory_item_id`='" . $item['itemId'] . "',
    //               `itemCode`='" . $item['itemCode'] . "',
    //               `itemName`='" . $item['itemName'] . "',
    //               `totalDiscount`='" . $item['totalDiscount'] . "',
    //               `unitPrice`='" . $item['unitPrice'] . "',
    //               `tolerance`='" . $item['tolerance'] . "',
    //               `qty`='" . $item['qty'] . "',
    //               `uom`='" . $item['uom'] . "'
    //           ";
    //             $insConn = queryInsert($ins);
    //             if($insConn['status'] == "success") {
    //             $returnData['itemLastID'] = $dbCon->insert_id;
    //             $tot = $item['unitPrice'] * $item['qty'];
    //             $dis = ($tot * $item['totalDiscount']) / 100;
    //             $totalDiscount = $totalDiscount + $dis;
    //             $totalAmount = $totalAmount + $tot;
    //             // console($item['deliverySchedule']);
    //             foreach ($item['deliverySchedule'] as $delItem) {
    //                 $insDeli = "INSERT INTO `" . ERP_BRANCH_PURCHASE_ORDER_DELIVERY_SCHEDULE . "` 
    //                 SET 
    //                 `so_item_id`='" . $returnData['itemLastID'] . "',
    //                 `delivery_date`='" . $delItem['multiDeliveryDate'] . "',
    //                 `deliveryStatus`='pending',
    //                 `qty`='" . $delItem['quantity'] . "'";
    //                 $insDeliConn = queryInsert($insDeli);
    //          if($insDeliConn['status'] == "success") {
                
    //                   $returnData = $insDeliConn;
    //                 } else {
    //                     $returnData = $insDeliConn;
    //                 }
    //             }
    //         } else {
    //             $returnData = $insConn;
    //         }
    //     }

    //     $updateDeli = "UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` 
    //                     SET 
    //                         `totalItems`='" . $totalItems . "',
    //                         `totalDiscount`='" . $totalDiscount . "',
    //                         `totalAmount`='" . $totalAmount . "' WHERE po_id=" . $lastId . "";
    //                         $updateDeliConn = queryInsert($updateDeli);
    //                         $returnData =  $updateDeliConn;
                            

    //     return $returnData;
    // }

    function fetchBranchPoListing($company_id,$branch_id,$location_id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `branch_id`='".$branch_id."' AND `location_id`='".$location_id."' AND `company_id`='".$company_id."' ";
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

    function fetchBranchPoListingByVendor($id){

        
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `vendor_id`=$id";
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

    function fetchBranchPoItems($soId)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` WHERE `po_id`='$soId' AND status='active'";
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

    function fetchBranchPoItemsDeliverySchedule($poItemId)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER_DELIVERY_SCHEDULE . "` WHERE `po_item_id`='$poItemId' AND status='active' AND deliveryStatus!='production'";
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

        $ins = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER_DELIVERY_SCHEDULE . "` WHERE `so_delivery_id`='$id' AND status='active'";
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

    function fetchVendorDetails($poId)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `vendor_id`='$poId'";

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
    function addBranchSoDelivery($POST)
    {
        $returnData = [];
        global $dbCon;

        // console($_POST);

        $customerId = $POST['customerId'];
        $deliveryDate = $POST['deliveryDate1'];
        $profitCenter = $POST['profitCenter'];
        $customerPO = $POST['customerPO'];
        $soNumber = $POST['soNumber'];

        // ***************
        $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "` WHERE `status`!='deleted' ORDER BY so_delivery_id DESC LIMIT 1";
        $lastSoNo = queryGet($sql);
        // console($lastSoNo);
        if (isset($lastSoNo['data'])) {
            $lastSoNo = $lastSoNo['data']['so_number'];
        } else {
            $lastSoNo = '';
        }
        $returnSoNo = getSoSerialNumber($lastSoNo);
        // ***************

        $ins = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "`
                 SET
                   `so_number`='$soNumber',
                   `customer_id`='$customerId',
                   `delivery_date`='$deliveryDate',
                   `profit_center`='$profitCenter',
                   `customer_po_no`='$customerPO'
      ";
        if ($dbCon->query($ins)) {
            $returnData['success'] = "true";
            $returnData['message'] = "inserted success!";
            $returnData['lastID'] = $dbCon->insert_id;
        } else {
            $returnData['success'] = "false";
            $returnData['message'] = "somthing went wrong!";
        }

        return $returnData;
    }

    // add branch so delivery items
    function addBranchSoDeliveryItems($POST, $id)
    {
        $returnData = [];
        global $dbCon;
        $lastId = $id;
        $listItem = $POST['listItem'];
        $totalDiscount = 0;
        $totalItems = count($listItem);
        $totalAmount = 0;
        foreach ($listItem as $item) {
            $ins = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_DELIVERY_ITEMS . "`
                SET
                  `so_delivery_id`='$lastId',
                  `lineNo`='" . $item['lineNo'] . "',
                  `inventory_item_id`='" . $item['itemId'] . "',
                  `itemCode`='" . $item['itemCode'] . "',
                  `itemName`='" . $item['itemName'] . "',
                  `delivery_date`='" . $item['deliveryDate2'] . "',
                  `qty`='" . $item['qty'] . "',
                  `uom`='" . $item['uom'] . "'
      ";
            if ($res = $dbCon->query($ins)) {
                $returnData['success'] = "true";
                $returnData['message'] = "insert successfull!";
                // $returnData['itemLastID'] = $dbCon->insert_id;
                // $tot = $item['unitPrice'] * $item['qty'];
                // $dis = ($tot * $item['totalDiscount']) / 100;
                // $totalDiscount = $totalDiscount + $dis;
                // $totalAmount = $totalAmount + $tot;
                // console($item['deliverySchedule']);

                // $updateDeli = "UPDATE `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` 
                //                     SET 
                //                         `remainingQty`='" . $item['qty'] . "',
                //                         `deliveryStatus`='production' 
                //                     WHERE 
                //                         lineNo='".$item['lineNo']."' AND so_item_id='".$item['itemId']."'
                // ";
                // $dbCon->query($updateDeli);

                // update delivery schedule 
                $deliSche = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` 
                                    SET 
                                        `remainingQty`='" . $item['qty'] . "',
                                        `deliveryStatus`='production' 
                                    WHERE 
                                        so_delivery_id='" . $item['deliveryDate2'] . "'
                ";
                $dbCon->query($deliSche);
            } else {
                $returnData['success'] = "false";
                $returnData['message'] = "somthing went wrong!";
            }
        }

        $updateDeli = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "` 
                        SET 
                            `totalItems`='" . $totalItems . "' WHERE so_delivery_id=" . $lastId . "";
        $dbCon->query($updateDeli);

        return $returnData;
    }

    // fetch branch so delivery listing 
    function fetchBranchSoDeliveryListing()
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "` WHERE status='active'";
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

        $company_id = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
        $branch_id = $_SESSION['logedBranchAdminInfo']['fldAdminBranchId'];
        $ins = "SELECT * FROM `" . ERP_COST_CENTER . "` WHERE CostCenter_status='active' AND branch_id = $branch_id AND company_id = $company_id";
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

    function updateBranchPgi($POST)
    {
        global $dbCon;
        
        $POST['soNumber'];
        $POST['itemDeliveryId'];

        $deliSche = "UPDATE `" . ERP_BRANCH_SALES_ORDER_DELIVERY_ITEMS . "` 
                            SET 
                                `remainingQty`='" . $POST['qty'] . "',
                                `deliveryStatus`='pgi' 
                            WHERE 
                                so_delivery_id='" . $POST['deliveryDate2'] . "'
        ";
        $dbCon->query($deliSche);
    }

    function uploadInvoice($POST){
 
    //console($POST);
        $invoice = "";
        $invoice = uploadFile($POST["invoice"], "../public/storage/invoice/",["jpg","jpeg","png"]);
        if($invoice['status']=='success'){
         $invoice=$invoice['data'];
        }else{
         $invoice='';
        }
        $id = $POST['id'];
        $status = "invoice uploaded";
        $ins = "UPDATE `erp_branch_purchase_order` SET `invoice`='$invoice', `invoice_status`='$status' WHERE `po_id`='".$id."' ";

     
        $returnData = queryUpdate($ins);
        return $returnData;


    }
}
