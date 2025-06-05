<?php
class BranchPo
{
    function addBranchPo($POST, $branch_id, $company_id, $location_id)
    {
        $returnData = [];
        global $dbCon;
        global $admin_variant;
        global $company_id;
        global $location_id;
        global $branch_id;
        global $created_by;
        global $updated_by;
        //console($POST);
        //         $listItem = $POST['listItem'];
        // //          console($listItem);
        //          foreach ($listItem as $item) {
        //       if($item['deliveryScheduleId'] != ''){
        //     $deliveryScheduleId = $item['deliveryScheduleId'];
        //     $select_rem_qty = queryGet("SELECT * FROM `erp_purchase_register_item_delivery_schedule` WHERE `pr_delivery_id` = $deliveryScheduleId");
        //   //  console($select_rem_qty['data']['remaining_qty']);
        //     $prev_rem_qty = $select_rem_qty['data']['remaining_qty'];
        //     if($prev_rem_qty < $item['qty']){
        //         $remaining_qty = 0;
        //     }
        //     else{
        //         $remaining_qty = $prev_rem_qty - $item['qty'];
        //     }

        // $update_rem_qty = queryGet("UPDATE `erp_purchase_register_item_delivery_schedule` SET remaining_qty = '".$remaining_qty."'");

        // }
        //         }
        // console($update_pr_del_schedule);
        // exit();


        // if($_POST['addresscheckbox'] == 1 ){
        //     $shipTo = $location_id;

        // }
        // else{
        //     $shipTo = $POST['shipToAddress'];
        // }

        $shipTo = $_POST['shipToInput'];
        //   echo $shipTo;

        // exit();

        // echo $POST['pr_id'] ;
        // exit();
        // check if checkbox is set 



        $chkBoxsql = queryGet("SELECT tc_id ,tc_text FROM `erp_terms_and_condition_format` WHERE tc_slug='po' AND company_id=" . $company_id . " AND `status`='active' ")['data'];
        $tc_id = $chkBoxsql['tc_id'];
        $tc_text = addcslashes($chkBoxsql['tc_text'], '\\\'');


        $isValidate = validate($POST, [
            "vendorId" => "required",
            "deliveryDate" => "required",
            "podatecreation" => "required",
            "listItem" => "required",
            "validitydate" => "required"

        ]);
        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }


        $validitydate = $POST['validitydate'];

        if ($validitydate < date('Y-m-d')) {

            $returnData['status'] = "warning";

            $returnData['message'] = "Validation Date Wrong";

            return $returnData;
        }



        //console($POST);

        if (isset($POST['curr_rate']) && $POST["curr_rate"] != '') {
            $conversion = $POST["curr_rate"] ?? 0;
        } else {
            $conversion = 0;
        }
        $vendorId = $POST['vendorId'];
        $deliveryDate = $POST['deliveryDate'];
        $costCenter = $POST['costCenter'] ?? 0;
        $refNo = $POST['refNo'];
        $poDate = $POST['podatecreation'];
        $use_type = $POST['usetypesDropdown'];
        $poOrigin = $POST['poOrigin'];
        $remarks = $POST['extra_remark'];

        if ($use_type == "servicep") {
            $service_po = "yes";
        } else {
            $service_po = "no";
        }
        $po_type = $POST['potypes'];
        $inco = $POST['domestic'] ?? "";
        $pr_id = $POST['pr_id'] ?? 0;
        $po_status = 14;
        $parent_po = !empty($_POST['parent_po']) ? $_POST['parent_po'] : 0;
        // $currency = $POST['currency'];
        //$conversion = $POST['curr_rate'];

        // $funcArea = $POST['funcArea'];

        $companyCountry = $_SESSION["logedBranchAdminInfo"]["companyCountry"];
        if ($companyCountry == "103") {
            $taxData = json_decode($POST['gstdetails'], true);
            foreach ($taxData as $tax) {
                if ($tax['gstType'] === 'CGST') {
                    $cgst = $tax['taxAmount'];
                } elseif ($tax['gstType'] === 'SGST') {
                    $sgst = $tax['taxAmount'];
                } elseif ($tax['gstType'] === "IGST") {
                    $igst = $tax['taxAmount'];
                }
            }
            $igst = round($igst / $conversion, 2);
            $sgst = round($sgst / $conversion, 2);
            $cgst =  round($cgst / $conversion, 2);
            $total_gst = $igst + $sgst + $cgst;
            $taxComponents = $POST['gstdetails'];
        } else {
            $igst = 0;
            $sgst = 0;
            $cgst =  0;
            $total_gst = $POST['grandTaxAmtInp'];
            $taxComponents = $POST['gstdetails'];
        }
        $subtotal =  round($POST['subTotal'] / $conversion, 2);



        if (isset($POST["funcArea"]) && $POST["funcArea"] != '') {
            $funcArea = $POST["funcArea"] ?? 0;
        } else {
            $funcArea = 0;
        }

        if (isset($POST['currency']) && $POST["currency"] != '') {
            $currency = $POST["currency"] ?? 0;
        } else {
            $currency = 0;
        }


        // exit();
        $check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant ");
        $check_var_data = $check_var_sql['data'];
        $max = $check_var_data['month_end'];
        $min = $check_var_data['month_start'];


        if ($poDate > $max) {

            $returnData = [
                "status" => "warning",
                "message" => "PO Date Invalid",
                "numRows" => 0,
                "data" => []
            ];
            return $returnData;
        } elseif ($poDate < $min) {

            $returnData = [
                "status" => "warning",
                "message" => "PO Date Invalid",
                "numRows" => 0,
                "data" => []
            ];
            return $returnData;
        } else {

            // *************** //
            $lastQuery = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `company_id`=$company_id AND `po_number` NOT LIKE '%/%' ORDER BY `po_id` DESC LIMIT 1";
            $last = queryGet($lastQuery);
            $lastRow = $last['data'] ?? "";
            $lastPoNo = $lastRow['po_number'] ?? "";
            $returnPoNo = getPoSerialNumber($lastPoNo);
            // console($returnPoNo);
            // exit();
            //  $ship_to = $POST['shipToAddress'];


            //attachment details insert    
            $attachment = $POST['attachment'];
            $name = $attachment["name"];
            $tmpName = $attachment["tmp_name"];
            $size = $attachment["size"];

            $allowed_types = ['jpg', 'png', 'jpeg', 'pdf'];
            $maxsize = 2 * 1024 * 1024; // 10 MB


            $fileUploaded = uploadFile(["name" => $name, "tmp_name" => $tmpName, "size" => $size], COMP_STORAGE_DIR . "/others/", $allowed_types, $maxsize, 0);
            // console($fileUploaded);
            $attachment_name = $fileUploaded['data'];


            $insert = "INSERT INTO `erp_branch_purchase_order` 
                            SET `po_number`='$returnPoNo',
                             `vendor_id`='$vendorId',
                             `delivery_date`='$deliveryDate',
                             `cost_center`='$costCenter',
                             `ref_no`='$refNo',
                            `po_date`='$poDate',
                            `use_type`='$use_type',
                            `po_type`='$po_type',
                            `inco_type`='$inco',
                            `branch_id`=$branch_id,
                            `company_id`=$company_id,
                             `pr_id`=$pr_id,
                             `parent_id` = $parent_po,  
                             `validityperiod`  = '$validitydate',
                            `location_id`=$location_id,
                            `bill_address`=$location_id,
                            `ship_address`=$shipTo,
                            `service_po`='$service_po',
                            `po_status`= $po_status,
                            `functional_area`=$funcArea,
                            `currency`=$currency,
                            `total_gst` = '$total_gst',
                            `total_igst` = '$igst',
                            `total_sgst` ='$sgst',
                            `total_cgst` = '$cgst',
                            `taxComponents`='$taxComponents',
                            `subtotal` = '$subtotal',
                            `created_by`='" . $created_by . "',
                            `updated_by`='" . $updated_by . "',
                            `po_attachment` = '" . $attachment_name . "',
                            `poOrigin` = '" . $poOrigin . "',
                            `remarks` = '" . $remarks . "',
                            `conversion_rate`='" . $conversion . "'";

            $insConn = queryInsert($insert);
            // console($insConn);
            // exit();
            if ($insConn['status'] == "success") {
                $last_po_id =  $insConn['insertedId'];

                $insertTermsandCond = queryInsert("INSERT INTO `erp_applied_terms_and_conditions` SET `slug_id`='$last_po_id',`tc_id`='$tc_id',`slug`='po' ,`tc_text`='$tc_text', `created_by`='" . $created_by . "', `updated_by`='" . $updated_by . "'");

                $funcAreaValue = queryGet("SELECT * FROM `erp_company_functionalities` WHERE `functionalities_id`=$funcArea")['data']['functionalities_name'];

                // $costCenterValue = queryGet("SELECT CostCenter_code FROM `" . ERP_COST_CENTER . "` WHERE CostCenter_status='active' AND branch_id = $branch_id AND company_id = $company_id AND CostCenter_id = $costCenter")['data']['CostCenter_code'];
                $costCenterValue = '';
                if ($costCenter != '' || $costCenter != NULL || $costCenter != 0) {

                    $costCenterValue = queryGet("SELECT CostCenter_code FROM `" . ERP_COST_CENTER . "` WHERE CostCenter_status='active' AND branch_id = $branch_id AND company_id = $company_id AND CostCenter_id = $costCenter")['data']['CostCenter_code'];
                }


                $lastId = $insConn['insertedId'];

                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_PURCHASE_ORDER;
                $auditTrail['basicDetail']['column_name'] = 'po_id'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $lastId;  // primary key
                $auditTrail['basicDetail']['party_type'] = 'vendor';
                $auditTrail['basicDetail']['party_id'] = $vendorId;
                $auditTrail['basicDetail']['document_number'] = $returnPoNo;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = ' PO Created';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';
                $auditTrail['action_data']['Purchase Order Details']['po_number'] = $returnPoNo;
                $auditTrail['action_data']['Purchase Order Details']['delivery_date'] = formatDateORDateTime($deliveryDate);
                // $auditTrail['action_data']['Purchase Order Details']['cost_center'] = $costCenter;
                $auditTrail['action_data']['Purchase Order Details']['ref_no'] = $refNo;
                $auditTrail['action_data']['Purchase Order Details']['po_date'] = formatDateORDateTime($poDate);
                $auditTrail['action_data']['Purchase Order Details']['use_type'] = $use_type;
                $auditTrail['action_data']['Purchase Order Details']['po_type'] = $po_type;
                $auditTrail['action_data']['Purchase Order Details']['inco_type'] = $inco;
                // $auditTrail['action_data']['Purchase Order Details']['pr_id'] = $pr_id;
                // $auditTrail['action_data']['Purchase Order Details']['bill_address'] = $location_id;
                // $auditTrail['action_data']['Purchase Order Details']['ship_address'] = $shipTo;
                // $auditTrail['action_data']['Purchase Order Details']['service_po'] = 'no';
                // $auditTrail['action_data']['Purchase Order Details']['functional_area'] = $funcArea;
                // $auditTrail['action_data']['Purchase Order Details']['currency'] = $currency;
                $auditTrail['action_data']['Purchase Order Details']['conversion_rate'] = $conversion;
                $auditTrail['action_data']['Purchase Order Details']['Po_Number'] = $returnPoNo;
                $auditTrail['action_data']['Purchase Order Details']['Delivery_Date'] = formatDateORDateTime($deliveryDate);
                $auditTrail['action_data']['Purchase Order Details']['Cost_Center'] = $costCenterValue;
                $auditTrail['action_data']['Purchase Order Details']['Ref_No'] = $refNo;
                $auditTrail['action_data']['Purchase Order Details']['Po_Date'] = formatDateORDateTime($poDate);
                $auditTrail['action_data']['Purchase Order Details']['Use_Type'] = $use_type;
                $auditTrail['action_data']['Purchase Order Details']['Po_Type'] = $po_type;
                $auditTrail['action_data']['Purchase Order Details']['Inco_Type'] = $inco;
                // $auditTrail['action_data']['Purchase Order Details']['pr_id'] = $pr_id;
                // $auditTrail['action_data']['Purchase Order Details']['bill_address'] = $location_id;
                // $auditTrail['action_data']['Purchase Order Details']['ship_address'] = $shipTo;
                // $auditTrail['action_data']['Purchase Order Details']['Service_Po'] = 'no';
                $auditTrail['action_data']['Purchase Order Details']['Functional_Area'] = $funcAreaValue;
                $auditTrail['action_data']['Purchase Order Details']['Currency'] = getSingleCurrencyType($currency);
                $auditTrail['action_data']['Purchase Order Details']['Conversion_Rate'] = $conversion;
                $listItem = $POST['listItem'];
                $totalItems = count($listItem);
                //console($listItem);
                //  exit();
                $totalAmount = 0;
                $i = 1;
                $count = 1;
                if ($POST['FreightCost']['l1']['service_amount'] != "") {
                    foreach ($_POST['FreightCost'] as $freight) {

                        $amount =  $freight['service_amount'];
                        $txt = $freight['service_desc'];
                        $service = $freight['service_purchase_id'];
                        $gst = $freight['gst'] ?? "";
                        $total = $freight['service_amount'];
                        $rcm = $freight['rcm'] ?? "";
                        $type = "associate po";
                        $vendor = $freight['service_vendor'];

                        // $count = $i++;
                        $serv_po = $returnPoNo . "/" . $count++;
                        $insFrieght = "INSERT INTO `erp_branch_purchase_order` 
                                            SET  
                                             `delivery_date`='$deliveryDate',
                                             `cost_center`='$costCenter',
                                             `use_type`='$use_type',
                                            `inco_type`='$inco',
                                            `bill_address`=$location_id,
                                             `ship_address`=$shipTo,
                                            `ref_no`='$returnPoNo',
                                             `po_number`='" . $serv_po . "',
                                            `vendor_id`='$vendor',
                                            `po_type`='servicep',
                                             `service_name`='" . $txt . "',
                                            `service_amount`='" . $amount . "',
                                            `service_type`='" . $type . "',
                                            `service_description`='" . $service . "',
                                            `service_gst`='" . $gst . "',
                                            `service_total`='" . $total . "',
                                            `service_rcm`='" . $rcm . "',
                                            `parent_id`=$last_po_id,
                                            `service_po`='yes',
                                            `po_date`='$poDate',
                                            `po_status`= $po_status  ,
                                            `total_gst` = 0,
                                            `subtotal` = 0,
                                            `validityperiod` = '$validitydate',
                                            `currency`=$currency,
                                            `conversion_rate`='" . $conversion . "',
                                            `branch_id`='" . $branch_id . "',
                                            `company_id`='" . $company_id . "',
                                            `location_id`='" . $location_id . "'";
                        //   exit();

                        $insFrieghtConn = queryInsert($insFrieght);

                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Ref_No'] = $serv_po;
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Po_Number'] = $serv_po;
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Service_Name'] = $txt;
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Service_Amount'] = decimalValuePreview($amount);
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Service_Type'] = $type;
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Service_Description'] = $service;
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Service_Gst'] = $gst;
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Service_Total'] = decimalValuePreview($total);
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Service_Rcm'] = $rcm;
                        // $auditTrail['action_data']['Freight Cost Details'][$serv_po]['parent_id'] = $last_po_id;
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Service_Po'] = 'yes';
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Po_Date'] = formatDateWeb($poDate);
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Po_Status'] = $po_status;
                    }
                }
                if ($POST['OthersCost']['13']['service_amount'] != "") {
                    foreach ($_POST['OthersCost'] as $others) {
                        $amount_other =  $others['service_amount'];
                        $txt_other = $others['service_desc'];
                        $service = $others['service_purchase_id'];
                        $gst = $others['gst'] ?? "";
                        $total = $others['service_amount'];
                        $rcm = $others['rcm'] ?? "";
                        $type = "others";

                        $serv_po_other = $returnPoNo . "/" . $count++;
                        $insOther = "INSERT INTO `erp_branch_po_transport_detail` 
                                SET 
                                `poNumber`='" . $returnPoNo . "',
                                `vendor_name`='" . $txt_other . "',
                                `transportationAmount`='" . $amount_other . "',
                                `transportation_type`='" . $type . "',
                                `service_description`='" . $txt_other . "',
                                `gst_amount`='" . $gst . "',
                                `total_amount`='" . $total . "',
                                `rcm`='" . $rcm . "' ";
                        // echo $insOther;
                        $ins_serv =  queryInsert("INSERT INTO `erp_branch_purchase_order` 
                            SET `ref_no`='$returnPoNo',
                                `po_number`='" . $serv_po_other . "',
                                `service_name`='" . $txt_other . "',
                                `po_type`='servicep',
                                `service_amount`='" . $amount_other . "',
                                `service_type`='" . $type . "',
                                `service_description`='" . $service . "',
                                `service_gst`='" . $gst . "',
                                `service_total`='" . $total . "',
                                `service_po`='yes',
                                `service_rcm`='" . $rcm . "',
                                `po_date`='$poDate',
                                `po_status`= $po_status ,
                                `parent_id`=$last_po_id,
                                `currency`=$currency,
                                `conversion_rate`='" . $conversion . "',
                                `branch_id`='" . $branch_id . "',
                                `company_id`='" . $company_id . "',
                                `location_id`='" . $location_id . "',
                                `created_by`='" . $created_by . "',
                                `updated_by`='" . $updated_by . "'");

                        //    exit();
                        $insOtherConn = queryInsert($insOther);

                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Ref_no'] = $serv_po_other;
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Po_Number'] = $serv_po_other;
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Service_Name'] = $txt_other;
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Service_Amount'] = decimalValuePreview($amount_other);
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Service_Type'] = $type;
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Service_Description'] = $service;
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Service_Gst'] = $gst;
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Service_Total'] = decimalValuePreview($total);
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Service_Po'] = 'yes';
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Service_Rcm'] = $rcm;
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Po_Date'] = formatDateWeb($poDate);
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Po_Status'] = $po_status;
                        // $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['parent_id'] = $last_po_id;
                    }
                }


                foreach ($listItem as $item) {
                    //console($item);
                    $item_pr_id = $item['pr_id'];
                    $countI = $i++;
                    $totalPrice =   round($item['totalPrice'] / $conversion, 2);
                    $itemCode = $item['itemCode'];
                    if ($item['pritemId'] != "") {
                        $prItemId = $item['pritemId'];
                        $rem_qty_sql = queryGet("SELECT * FROM `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "` WHERE `prItemId`=$prItemId");
                        //   console($rem_qty_sql);
                        $total_remaining_qty = $rem_qty_sql['data']['remainingQty'];
                        $qty = $item['qty'];
                        $new_remaining = $total_remaining_qty - $qty;
                        $update_pr = "UPDATE `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "` SET `remainingQty`= $new_remaining WHERE `prItemId`=$prItemId";
                        //exit();
                        $update_pr = queryUpdate($update_pr);

                        $auditTrail['action_data']['Item Details'][$itemCode]['remainingQty'] = $new_remaining;
                    }

                    if ($item['uom'] == "") {
                        $uom = 0;
                    } else {
                        $uom = $item['uom'];
                    }
                    $gstAmount =  round($item['gstAmount'] / $conversion, 2);
                    $gst = $item['gst'];
                    $insItem = "INSERT INTO `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "`
                    SET
                        `po_id`='" . $lastId . "',
                        `lineNo`='" . $countI . "',
                        `inventory_item_id`='" . $item['itemId'] . "',
                        `itemCode`='" . $item['itemCode'] . "',
                        `itemName`='" . addslashes($item['itemName']) . "',
                        `unitPrice`='" .  round($item['unitPrice'] / $conversion, 2) . "',
                        `qty`='" . $item['qty'] . "',
                        `remainingQty`='" . $item['qty'] . "',
                        `total_price` = '" . $totalPrice . "',
                        `gst` = '" . $gst . "',
                        `gstAmount` = '" . $gstAmount . "',
                        `uom`='" . $uom . "'
                    ";
                    //exit();
                    $insItemConn = queryInsert($insItem);
                    //console($insItemConn);
                    // $auditTrail['action_data']['Item Details'][$itemCode]['line_No'] = $countI;
                    // $auditTrail['action_data']['Item Details'][$itemCode]['inventory_item_id'] = $item['itemId'];
                    $auditTrail['action_data']['Item Details'][$itemCode]['Item_Code'] = $item['itemCode'];
                    $auditTrail['action_data']['Item Details'][$itemCode]['Item_Name'] = $item['itemName'];
                    $auditTrail['action_data']['Item Details'][$itemCode]['Unit_Price'] = decimalValuePreview($item['unitPrice']);
                    $auditTrail['action_data']['Item Details'][$itemCode]['Qty'] = decimalQuantityPreview($item['qty']);
                    $auditTrail['action_data']['Item Details'][$itemCode]['UOM'] = $uom;
                    $auditTrail['action_data']['Item Details'][$itemCode]['Remaining_Qty'] = decimalQuantityPreview($item['qty']);
                    $auditTrail['action_data']['Item Details'][$itemCode]['Total_Price'] = decimalValuePreview($totalPrice);
                    // $auditTrail['action_data']['Item Details'][$itemCode]['total_transport_cost'] = $total_transport_cost;
                    $lastItemId = $insItemConn['insertedId'];
                    if ($insItemConn['status'] == "success") {
                        $returnData['itemLastID'] = $dbCon->insert_id;
                        $tot = $item['unitPrice'] * $item['qty'];
                        $totalAmount = $totalAmount + $tot;
                        // console($item['deliverySchedule']);
                        foreach ($item['deliverySchedule'] as $delItem) {
                            $rand = 'del' . rand(11111, 999999);
                            if ($delItem['multiDeliveryDate'] == "") {
                                $date = $deliveryDate;
                                $quantity = $item['qty'];
                            } else {
                                $date = $delItem['multiDeliveryDate'];
                                $quantity = $delItem['quantity'];
                            }
                            $insDeli = "INSERT INTO `" . ERP_BRANCH_PURCHASE_ORDER_DELIVERY_SCHEDULE . "` 
                                                        SET 
                                                        `po_item_id`=$lastItemId,
                                                        `delivery_date`='" . $date . "',
                                                        `deliveryStatus`='open',
                                                        `po_id` = $lastId,
                                                        `qty`='" . $quantity . "'";

                            $insDeliConn = queryInsert($insDeli);
                            if ($insDeliConn['status'] == "success") {

                                $returnData = $insDeliConn;

                                $auditTrail['action_data']['Item Details'][$itemCode]['Delivery Schedule Details'][$rand]['Delivery_Date'] = formatDateWeb($date);
                                $auditTrail['action_data']['Item Details'][$itemCode]['Delivery Schedule Details'][$rand]['Delivery_Status'] = 'open';
                                $auditTrail['action_data']['Item Details'][$itemCode]['Delivery Schedule Details'][$rand]['Qty'] = decimalQuantityPreview($quantity);
                            } else {
                                $returnData = $insDeliConn;
                            }
                        }

                        if ($item['deliveryScheduleId'] != '') {
                            $deliveryScheduleId = $item['deliveryScheduleId'];
                            $select_rem_qty = queryGet("SELECT * FROM `erp_purchase_register_item_delivery_schedule` WHERE `pr_delivery_id` = $deliveryScheduleId");
                            $prev_rem_qty = $select_rem_qty['data']['remaining_qty'];
                            if ($prev_rem_qty < $item['qty']) {
                                $remaining_qty = 0;
                            } else {
                                $remaining_qty = $prev_rem_qty - $item['qty'];
                            }

                            $update_rem_qty = queryGet("UPDATE `erp_purchase_register_item_delivery_schedule` SET remaining_qty = '" . $remaining_qty . "' WHERE `pr_delivery_id` = $deliveryScheduleId");
                        }



                        //$total_remaining_qty =  $item['qty'];
                        // $pr_del_sql = queryGet("SELECT * FROM `erp_purchase_register_item_delivery_schedule` WHERE `pr_item_id` = $prItemId ORDER BY `pr_delivery_id` ASC", true);
                        // foreach ($pr_del_sql['data'] as $pr_del) {
                        //     $pr_delivery_id = $pr_del['pr_delivery_id'];
                        //     $pr_del_qty = $pr_del['remaining_qty'];
                        //     if ($pr_del_qty > 0) {
                        //         if ($pr_del_qty < $total_remaining_qty) {

                        //             $pr_remaining_qty = $total_remaining_qty - $pr_del_qty;

                        //             $total_remaining_qty = $pr_remaining_qty;

                        //             $update_pr_del_schedule = "UPDATE `erp_purchase_register_item_delivery_schedule` SET `remaining_qty` =  $total_remaining_qty WHERE `pr_delivery_id` = $pr_delivery_id";
                        //         } else {
                        //             $update_pr_del_schedule = "UPDATE `erp_purchase_register_item_delivery_schedule` SET `remaining_qty` =  0 WHERE `pr_delivery_id` = $pr_delivery_id";
                        //         }
                        //     }
                        // }
                        //  console($pr_del_sql);
                    } else {
                        $returnData = $insConn;
                    }
                    if ($item_pr_id != 0 || $item_pr_id != "") {

                        $check_sql = queryGet("SELECT count(prItemId) FROM `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "` WHERE `prId`=$item_pr_id");
                        // console($check_sql);
                        $check = $check_sql['data']['count(prItemId)'];
                        $check_closed_item_sql = queryGet("SELECT count(prItemId) FROM `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "` WHERE `prId`=$item_pr_id AND `remainingQty`=0");
                        $check_closed_item =  $check_closed_item_sql['data']['count(prItemId)'];
                        if ($check == $check_closed_item) {
                            $update_pr = queryUpdate("UPDATE `" . ERP_BRANCH_PURCHASE_REQUEST . "` SET  `pr_status`=10 WHERE `purchaseRequestId`=$item_pr_id ");
                            // console($update_pr);
                            // $auditTrail['action_data']['Purchase Order Details']['pr_status'] = 10;
                        }
                    }
                }
                // exit();
                $total_quan_sql = queryGet("SELECT count(`po_item_id`) as total_items, SUM(`total_price`) as total_price FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` WHERE `po_id`=$lastId  ");
                $totalItems = $total_quan_sql['data']['total_items'];
                $totalPrice = $total_quan_sql['data']['total_price'];
                $poItem = "SELECT SUM(`unitPrice`) AS 'amount' , SUM(`qty`) AS 'qty' FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` WHERE `po_id`=$lastId  ";
                $poConn = queryGet($poItem);
                $poData = $poConn['data'] ?? "";
                $amount = $poData['amount'];
                $sumtransQuery = "SELECT SUM(`transportationAmount`) as amount FROM `erp_branch_po_transport_detail` WHERE `poNumber`='" . $returnPoNo . "'";
                $transQuery = queryGet($sumtransQuery);
                $transData = $transQuery['data'] ?? "";
                $total_transport_amount = $transData['amount'];
                $poItemQuery = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` WHERE `po_id`= '" . $lastId . "' ";
                $poQuery = queryGet($poItemQuery, true);
                $poItemData = $poQuery['data'];
                foreach ($poItemData as $item) {
                    $item_total_price = $item['total_price'];
                    $total_transport_cost =  ($total_transport_amount / $amount) * $item_total_price;
                    $insert_trans = "UPDATE `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "`  SET `total_transport_cost`='" . $total_transport_cost . "' WHERE `po_item_id`='" . $item['po_item_id'] . "'";
                    $updateItemiConn = queryInsert($insert_trans);
                    $returnData =  $updateItemiConn;
                }
                $updateDeli = "UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` 
                            SET 
                                `totalItems`='" . $totalItems . "',
                                `totalAmount`='" . $totalPrice . "' WHERE po_id=" . $lastId . "";
                $updateDeliConn = queryInsert($updateDeli);
                // $returnData =  $updateDeliConn;
                $auditTrail['action_data']['Purchase Order Details']['Total_Items'] = decimalQuantityPreview($totalItems);
                $auditTrail['action_data']['Purchase Order Details']['Total_Amount'] = decimalValuePreview($totalPrice);
                $auditTrailreturn = generateAuditTrail($auditTrail);
                $returnData['status'] = "success";
                $returnData['lastQuery'] = $lastQuery;
                $returnData['auditTrailreturn'] = $auditTrailreturn;
                $returnData['costcenterReturn'] = $costCenterValue;
                $returnData['message'] = "PO Creation Successful! PO Code is -" . $returnPoNo;
                return $returnData;
            } else {

                return $insConn;
            }
            return $returnData;
        }
    }

    function addBranchPo2($POST, $branch_id, $company_id, $location_id)
    {
        $returnData = [];
        global $dbCon;
        global $admin_variant;
        global $company_id;
        global $location_id;
        global $branch_id;
        global $created_by;
        global $updated_by;
        //console($POST);
        //         $listItem = $POST['listItem'];
        // //          console($listItem);
        //          foreach ($listItem as $item) {
        //       if($item['deliveryScheduleId'] != ''){
        //     $deliveryScheduleId = $item['deliveryScheduleId'];
        //     $select_rem_qty = queryGet("SELECT * FROM `erp_purchase_register_item_delivery_schedule` WHERE `pr_delivery_id` = $deliveryScheduleId");
        //   //  console($select_rem_qty['data']['remaining_qty']);
        //     $prev_rem_qty = $select_rem_qty['data']['remaining_qty'];
        //     if($prev_rem_qty < $item['qty']){
        //         $remaining_qty = 0;
        //     }
        //     else{
        //         $remaining_qty = $prev_rem_qty - $item['qty'];
        //     }

        // $update_rem_qty = queryGet("UPDATE `erp_purchase_register_item_delivery_schedule` SET remaining_qty = '".$remaining_qty."'");

        // }
        //         }
        // console($update_pr_del_schedule);
        // exit();


        // if($_POST['addresscheckbox'] == 1 ){
        //     $shipTo = $location_id;

        // }
        // else{
        //     $shipTo = $POST['shipToAddress'];
        // }

        $shipTo = $_POST['shipToInput'];
        //   echo $shipTo;

        // exit();

        // echo $POST['pr_id'] ;
        // exit();
        // check if checkbox is set 



        $chkBoxsql = queryGet("SELECT tc_id ,tc_text FROM `erp_terms_and_condition_format` WHERE tc_slug='po' AND company_id=" . $company_id . " AND `status`='active' ")['data'];
        $tc_id = $chkBoxsql['tc_id'];
        $tc_text = addcslashes($chkBoxsql['tc_text'], '\\\'');


        $isValidate = validate($POST, [
            "vendorId" => "required",
            "deliveryDate" => "required",
            "podatecreation" => "required",
            "listItem" => "required",
            "validitydate" => "required"

        ]);
        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }


        $validitydate = $POST['validitydate'];

        if ($validitydate < date('Y-m-d')) {

            $returnData['status'] = "warning";

            $returnData['message'] = "Validation Date Wrong";

            return $returnData;
        }
        $listItem = $_POST['listItem'];
        $filteredDeliverySchedule = [];
        
        foreach ($listItem as $item) {
            foreach ($item['deliverySchedule'] as $dsKey => $schedule) {
                if (!empty($schedule['multiDeliveryDate']) && !empty($schedule['quantity'])) {
                    $filteredDeliverySchedule[$dsKey] = $schedule;
                }
            }
        }



        //console($POST);

        if (isset($POST['curr_rate']) && $POST["curr_rate"] != '') {
            $conversion = $POST["curr_rate"] ?? 0;
        } else {
            $conversion = 0;
        }
        $vendorId = $POST['vendorId'];
        $deliveryDate = $POST['deliveryDate'];
        $costCenter = $POST['costCenter'] ?? 0;
        $refNo = $POST['refNo'];
        $poDate = $POST['podatecreation'];
        $use_type = $POST['usetypesDropdown'];
        $poOrigin = $POST['poOrigin'];
        $remarks = $POST['extra_remark'];

        if ($use_type == "servicep") {
            $service_po = "yes";
        } else {
            $service_po = "no";
        }
        $po_type = $POST['potypes'];
        $inco = $POST['domestic'] ?? "";
        $pr_id = $POST['pr_id'] ?? 0;
        $po_status = 14;
        $parent_po = !empty($_POST['parent_po']) ? $_POST['parent_po'] : 0;
        // $currency = $POST['currency'];
        //$conversion = $POST['curr_rate'];

        // $funcArea = $POST['funcArea'];

        $companyCountry = $_SESSION["logedBranchAdminInfo"]["companyCountry"];
        if ($companyCountry == "103") {
            $taxData = json_decode($POST['gstdetails'], true);
            foreach ($taxData as $tax) {
                if ($tax['gstType'] === 'CGST') {
                    $cgst = $tax['taxAmount'];
                } elseif ($tax['gstType'] === 'SGST') {
                    $sgst = $tax['taxAmount'];
                } elseif ($tax['gstType'] === "IGST") {
                    $igst = $tax['taxAmount'];
                }
            }
            $igst = round($igst / $conversion, 2);
            $sgst = round($sgst / $conversion, 2);
            $cgst =  round($cgst / $conversion, 2);
            $total_gst = $igst + $sgst + $cgst;
            $taxComponents = $POST['gstdetails'];
        } else {
            $igst = 0;
            $sgst = 0;
            $cgst =  0;
            $total_gst = $POST['grandTaxAmtInp'];
            $taxComponents = $POST['gstdetails'];
        }
        $subtotal =  round($POST['subTotal'] / $conversion, 2);


        if (isset($POST["funcArea"]) && $POST["funcArea"] != '') {
            $funcArea = $POST["funcArea"] ?? 0;
        } else {
            $funcArea = 0;
        }

        if (isset($POST['currency']) && $POST["currency"] != '') {
            $currency = $POST["currency"] ?? 0;
        } else {
            $currency = 0;
        }


        // exit();
        $check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant ");
        $check_var_data = $check_var_sql['data'];
        $max = $check_var_data['month_end'];
        $min = $check_var_data['month_start'];


        if ($poDate > $max) {

            $returnData = [
                "status" => "warning",
                "message" => "PO Date Invalid",
                "numRows" => 0,
                "data" => []
            ];
            return $returnData;
        } elseif ($poDate < $min) {

            $returnData = [
                "status" => "warning",
                "message" => "PO Date Invalid",
                "numRows" => 0,
                "data" => []
            ];
            return $returnData;
        } else {

            // *************** //
            $lastQuery = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `company_id`=$company_id AND `po_number` NOT LIKE '%/%' ORDER BY `po_id` DESC LIMIT 1";
            $last = queryGet($lastQuery);
            $lastRow = $last['data'] ?? "";
            $lastPoNo = $lastRow['po_number'] ?? "";
            $returnPoNo = getPoSerialNumber($lastPoNo);
            // console($returnPoNo);
            // exit();
            //  $ship_to = $POST['shipToAddress'];


            //attachment details insert    
            $attachment = $POST['attachment'];
            $name = $attachment["name"];
            $tmpName = $attachment["tmp_name"];
            $size = $attachment["size"];

            $allowed_types = ['jpg', 'png', 'jpeg', 'pdf'];
            $maxsize = 2 * 1024 * 1024; // 10 MB


            $fileUploaded = uploadFile(["name" => $name, "tmp_name" => $tmpName, "size" => $size], COMP_STORAGE_DIR . "/others/", $allowed_types, $maxsize, 0);
            // console($fileUploaded);
            $attachment_name = $fileUploaded['data'];


            $insert = "INSERT INTO `erp_branch_purchase_order` 
                            SET `po_number`='$returnPoNo',
                             `vendor_id`='$vendorId',
                             `delivery_date`='$deliveryDate',
                             `cost_center`='$costCenter',
                             `ref_no`='$refNo',
                            `po_date`='$poDate',
                            `use_type`='$use_type',
                            `po_type`='$po_type',
                            `inco_type`='$inco',
                            `branch_id`=$branch_id,
                            `company_id`=$company_id,
                             `pr_id`=$pr_id,
                             `parent_id` = $parent_po,  
                             `validityperiod`  = '$validitydate',
                            `location_id`=$location_id,
                            `bill_address`=$location_id,
                            `ship_address`=$shipTo,
                            `service_po`='$service_po',
                            `po_status`= $po_status,
                            `functional_area`=$funcArea,
                            `currency`=$currency,
                            `total_gst` = '$total_gst',
                            `total_igst` = '$igst',
                            `total_sgst` ='$sgst',
                            `total_cgst` = '$cgst',
                            `taxComponents`='$taxComponents',
                            `subtotal` = '$subtotal',
                            `created_by`='" . $created_by . "',
                            `updated_by`='" . $updated_by . "',
                            `po_attachment` = '" . $attachment_name . "',
                            `poOrigin` = '" . $poOrigin . "',
                            `remarks` = '" . $remarks . "',
                            `conversion_rate`='" . $conversion . "'";


            $insConn = queryInsert($insert);
            // console($insConn);
            // exit();
            if ($insConn['status'] == "success") {
                $last_po_id =  $insConn['insertedId'];

                $insertTermsandCond = queryInsert("INSERT INTO `erp_applied_terms_and_conditions` SET `slug_id`='$last_po_id',`tc_id`='$tc_id',`slug`='po' ,`tc_text`='$tc_text', `created_by`='" . $created_by . "', `updated_by`='" . $updated_by . "'");

                $funcAreaValue = queryGet("SELECT * FROM `erp_company_functionalities` WHERE `functionalities_id`=$funcArea")['data']['functionalities_name'];


                $costCenterValue = '';
                if ($costCenter != '' || $costCenter != NULL || $costCenter != 0) {

                    $costCenterValue = queryGet("SELECT CostCenter_code FROM `" . ERP_COST_CENTER . "` WHERE CostCenter_status='active' AND branch_id = $branch_id AND company_id = $company_id AND CostCenter_id = $costCenter")['data']['CostCenter_code'];
                }
                

                $lastId = $insConn['insertedId'];

                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_PURCHASE_ORDER;
                $auditTrail['basicDetail']['column_name'] = 'po_id'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $lastId;  // primary key
                $auditTrail['basicDetail']['party_type'] = 'vendor';
                $auditTrail['basicDetail']['party_id'] = $vendorId;
                $auditTrail['basicDetail']['document_number'] = $returnPoNo;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = ' PO Created';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';
                $auditTrail['action_data']['Purchase Order Details']['po_number'] = $returnPoNo;
                $auditTrail['action_data']['Purchase Order Details']['delivery_date'] = formatDateORDateTime($deliveryDate);
                // $auditTrail['action_data']['Purchase Order Details']['cost_center'] = $costCenter;
                $auditTrail['action_data']['Purchase Order Details']['ref_no'] = $refNo;
                $auditTrail['action_data']['Purchase Order Details']['po_date'] = formatDateORDateTime($poDate);
                $auditTrail['action_data']['Purchase Order Details']['use_type'] = $use_type;
                $auditTrail['action_data']['Purchase Order Details']['po_type'] = $po_type;
                $auditTrail['action_data']['Purchase Order Details']['inco_type'] = $inco;
                $auditTrail['action_data']['Purchase Order Details']['pr_id'] = $pr_id;
                $auditTrail['action_data']['Purchase Order Details']['bill_address'] = $location_id;
                $auditTrail['action_data']['Purchase Order Details']['ship_address'] = $shipTo;
                $auditTrail['action_data']['Purchase Order Details']['service_po'] = 'no';
                $auditTrail['action_data']['Purchase Order Details']['functional_area'] = $funcArea;
                $auditTrail['action_data']['Purchase Order Details']['currency'] = $currency;
                $auditTrail['action_data']['Purchase Order Details']['conversion_rate'] = $conversion;
                $auditTrail['action_data']['Purchase Order Details']['Po_Number'] = $returnPoNo;
                $auditTrail['action_data']['Purchase Order Details']['Delivery_Date'] = formatDateORDateTime($deliveryDate);
                $auditTrail['action_data']['Purchase Order Details']['Cost_Center'] = $costCenterValue;
                $auditTrail['action_data']['Purchase Order Details']['Ref_No'] = $refNo;
                $auditTrail['action_data']['Purchase Order Details']['Po_Date'] = formatDateORDateTime($poDate);
                $auditTrail['action_data']['Purchase Order Details']['Use_Type'] = $use_type;
                $auditTrail['action_data']['Purchase Order Details']['Po_Type'] = $po_type;
                $auditTrail['action_data']['Purchase Order Details']['Inco_Type'] = $inco;
                // $auditTrail['action_data']['Purchase Order Details']['pr_id'] = $pr_id;
                // $auditTrail['action_data']['Purchase Order Details']['bill_address'] = $location_id;
                // $auditTrail['action_data']['Purchase Order Details']['ship_address'] = $shipTo;
                $auditTrail['action_data']['Purchase Order Details']['Service_Po'] = 'no';
                $auditTrail['action_data']['Purchase Order Details']['Functional_Area'] = $funcAreaValue;
                $auditTrail['action_data']['Purchase Order Details']['Currency'] = getSingleCurrencyType($currency);
                $auditTrail['action_data']['Purchase Order Details']['Conversion_Rate'] = $conversion;
                $listItem = $POST['listItem'];
                $totalItems = count($listItem);
                //console($listItem);
                //  exit();
                $totalAmount = 0;
                $i = 1;
                $count = 1;
                if ($POST['FreightCost']['l1']['service_amount'] != "") {
                    foreach ($_POST['FreightCost'] as $freight) {

                        $amount =  $freight['service_amount'];
                        $txt = $freight['service_desc'];
                        $service = $freight['service_purchase_id'];
                        $gst = $freight['gst'] ?? "";
                        $total = $freight['service_amount'];
                        $rcm = $freight['rcm'] ?? "";
                        $type = "associate po";
                        $vendor = $freight['service_vendor'];

                        // $count = $i++;
                        $serv_po = $returnPoNo . "/" . $count++;
                        $insFrieght = "INSERT INTO `erp_branch_purchase_order` 
                                            SET  
                                             `delivery_date`='$deliveryDate',
                                             `cost_center`='$costCenter',
                                             `use_type`='$use_type',
                                            `inco_type`='$inco',
                                            `bill_address`=$location_id,
                                             `ship_address`=$shipTo,
                                            `ref_no`='$returnPoNo',
                                             `po_number`='" . $serv_po . "',
                                            `vendor_id`='$vendor',
                                            `po_type`='servicep',
                                             `service_name`='" . $txt . "',
                                            `service_amount`='" . $amount . "',
                                            `service_type`='" . $type . "',
                                            `service_description`='" . $service . "',
                                            `service_gst`='" . $gst . "',
                                            `service_total`='" . $total . "',
                                            `service_rcm`='" . $rcm . "',
                                            `parent_id`=$last_po_id,
                                            `service_po`='yes',
                                            `po_date`='$poDate',
                                            `po_status`= $po_status  ,
                                            `total_gst` = 0,
                                            `subtotal` = 0,
                                            `validityperiod` = '$validitydate',
                                            `currency`=$currency,
                                            `conversion_rate`='" . $conversion . "',
                                            `branch_id`='" . $branch_id . "',
                                            `company_id`='" . $company_id . "',
                                            `location_id`='" . $location_id . "'";
                        //   exit();

                        $insFrieghtConn = queryInsert($insFrieght);

                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Ref_No'] = $serv_po;
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Po_Number'] = $serv_po;
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Service_Name'] = $txt;
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Service_Amount'] = decimalValuePreview($amount);
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Service_Type'] = $type;
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Service_Description'] = $service;
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Service_Gst'] = $gst;
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Service_Total'] = decimalValuePreview($total);
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Service_Rcm'] = $rcm;
                        // $auditTrail['action_data']['Freight Cost Details'][$serv_po]['parent_id'] = $last_po_id;
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Service_Po'] = 'yes';
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Po_Date'] = formatDateORDateTime($poDate);
                        $auditTrail['action_data']['Freight Cost Details'][$serv_po]['Po_Status'] = $po_status;
                    }
                }
                if ($POST['OthersCost']['13']['service_amount'] != "") {
                    foreach ($_POST['OthersCost'] as $others) {
                        $amount_other =  $others['service_amount'];
                        $txt_other = $others['service_desc'];
                        $service = $others['service_purchase_id'];
                        $gst = $others['gst'] ?? "";
                        $total = $others['service_amount'];
                        $rcm = $others['rcm'] ?? "";
                        $type = "others";

                        $serv_po_other = $returnPoNo . "/" . $count++;
                        $insOther = "INSERT INTO `erp_branch_po_transport_detail` 
                                SET 
                                `poNumber`='" . $returnPoNo . "',
                                `vendor_name`='" . $txt_other . "',
                                `transportationAmount`='" . $amount_other . "',
                                `transportation_type`='" . $type . "',
                                `service_description`='" . $txt_other . "',
                                `gst_amount`='" . $gst . "',
                                `total_amount`='" . $total . "',
                                `rcm`='" . $rcm . "' ";
                        // echo $insOther;
                        $ins_serv =  queryInsert("INSERT INTO `erp_branch_purchase_order` 
                            SET `ref_no`='$returnPoNo',
                                `po_number`='" . $serv_po_other . "',
                                `service_name`='" . $txt_other . "',
                                `po_type`='servicep',
                                `service_amount`='" . $amount_other . "',
                                `service_type`='" . $type . "',
                                `service_description`='" . $service . "',
                                `service_gst`='" . $gst . "',
                                `service_total`='" . $total . "',
                                `service_po`='yes',
                                `service_rcm`='" . $rcm . "',
                                `po_date`='$poDate',
                                `po_status`= $po_status ,
                                `parent_id`=$last_po_id,
                                `currency`=$currency,
                                `conversion_rate`='" . $conversion . "',
                                `branch_id`='" . $branch_id . "',
                                `company_id`='" . $company_id . "',
                                `location_id`='" . $location_id . "',
                                `created_by`='" . $created_by . "',
                                `updated_by`='" . $updated_by . "'");

                        //    exit();
                        $insOtherConn = queryInsert($insOther);

                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Ref_No'] = $serv_po_other;
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Po_Number'] = $serv_po_other;
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Service_Name'] = $txt_other;
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Service_Amount'] = decimalValuePreview($amount_other);
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Service_Type'] = $type;
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Service_Description'] = $service;
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Service_Gst'] = $gst;
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Service_Total'] = decimalValuePreview($total);
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Service_Po'] = 'yes';
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Service_Rcm'] = $rcm;
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Po_Date'] = formatDateWeb($poDate);
                        $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['Po_Status'] = $po_status;
                        // $auditTrail['action_data']['Others Cost Details'][$serv_po_other]['parent_id'] = $last_po_id;
                    }
                }


                foreach ($listItem as $item) {
                    //console($item);
                    $item_pr_id = $item['pr_id'];
                    $countI = $i++;
                    $totalPrice =   round($item['totalPrice'] / $conversion, 2);
                    $itemCode = $item['itemCode'];
                    if ($item['pritemId'] != "") {
                        $prItemId = $item['pritemId'];
                        $rem_qty_sql = queryGet("SELECT * FROM `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "` WHERE `prItemId`=$prItemId");
                        //   console($rem_qty_sql);
                        $total_remaining_qty = $rem_qty_sql['data']['remainingQty'];
                        $qty = $item['qty'];
                        $new_remaining = $total_remaining_qty - $qty;
                        $update_pr = "UPDATE `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "` SET `remainingQty`= $new_remaining WHERE `prItemId`=$prItemId";
                        //exit();
                        $update_pr = queryUpdate($update_pr);

                        $auditTrail['action_data']['Item Details'][$itemCode]['remainingQty'] = $new_remaining;
                    }

                    if ($item['uom'] == "") {
                        $uom = 0;
                    } else {
                        $uom = $item['uom'];
                    }
                    $gstAmount =  round($item['gstAmount'] / $conversion, 2);
                    $gst = $item['gst'];
                    $insItem = "INSERT INTO `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "`
                    SET
                        `po_id`='" . $lastId . "',
                        `lineNo`='" . $countI . "',
                        `inventory_item_id`='" . $item['itemId'] . "',
                        `itemCode`='" . $item['itemCode'] . "',
                        `itemName`='" . addslashes($item['itemName']) . "',
                        `unitPrice`='" .  round($item['unitPrice'] / $conversion, 2) . "',
                        `qty`='" . $item['qty'] . "',
                        `remainingQty`='" . $item['qty'] . "',
                        `total_price` = '" . $totalPrice . "',
                        `gst` = '" . $gst . "',
                        `gstAmount` = '" . $gstAmount . "',
                        `uom`='" . $uom . "'
                    ";
                    //exit();
                    $insItemConn = queryInsert($insItem);
                    //console($insItemConn);
                    // $auditTrail['action_data']['Item Details'][$itemCode]['line_No'] = $countI;
                    // $auditTrail['action_data']['Item Details'][$itemCode]['inventory_item_id'] = $item['itemId'];
                    $auditTrail['action_data']['Item Details'][$itemCode]['Item_Code'] = $item['itemCode'];
                    $auditTrail['action_data']['Item Details'][$itemCode]['Item_Name'] = $item['itemName'];
                    $auditTrail['action_data']['Item Details'][$itemCode]['Unit_Price'] = decimalValuePreview($item['unitPrice']);
                    $auditTrail['action_data']['Item Details'][$itemCode]['Qty'] = decimalQuantityPreview($item['qty']);
                    $auditTrail['action_data']['Item Details'][$itemCode]['UOM'] = $uom;
                    $auditTrail['action_data']['Item Details'][$itemCode]['Remaining_Qty'] = decimalQuantityPreview($item['qty']);
                    $auditTrail['action_data']['Item Details'][$itemCode]['Total_Price'] = decimalValuePreview($totalPrice);
                    // $auditTrail['action_data']['Item Details'][$itemCode]['total_transport_cost'] = $total_transport_cost;
                    $lastItemId = $insItemConn['insertedId'];
                    if ($insItemConn['status'] == "success") {
                        $returnData['itemLastID'] = $dbCon->insert_id;
                        $tot = $item['unitPrice'] * $item['qty'];
                        $totalAmount = $totalAmount + $tot;
                        // console($item['deliverySchedule']);
                        foreach ($filteredDeliverySchedule as $delItem) {
                            $rand = 'del' . rand(11111, 999999);
                            if ($delItem['multiDeliveryDate'] == "") {
                                $date = $deliveryDate;
                                $quantity = $item['qty'];
                            } else {
                                $date = $delItem['multiDeliveryDate'];
                                $quantity = $delItem['quantity'];
                            }
                            $insDeli = "INSERT INTO `" . ERP_BRANCH_PURCHASE_ORDER_DELIVERY_SCHEDULE . "` 
                                                        SET 
                                                        `po_item_id`=$lastItemId,
                                                        `delivery_date`='" . $date . "',
                                                        `deliveryStatus`='open',
                                                        `po_id` = $lastId,
                                                        `qty`='" . $quantity . "'";

                            $insDeliConn = queryInsert($insDeli);
                            if ($insDeliConn['status'] == "success") {

                                $returnData = $insDeliConn;

                                $auditTrail['action_data']['Item Details'][$itemCode]['Delivery Schedule Details'][$rand]['Delivery_Date'] = formatDateWeb($date);
                                $auditTrail['action_data']['Item Details'][$itemCode]['Delivery Schedule Details'][$rand]['Delivery_Status'] = 'open';
                                $auditTrail['action_data']['Item Details'][$itemCode]['Delivery Schedule Details'][$rand]['Qty'] = decimalQuantityPreview($quantity);
                            } else {
                                $returnData = $insDeliConn;
                            }
                        }

                        if ($item['deliveryScheduleId'] != '') {
                            $deliveryScheduleId = $item['deliveryScheduleId'];
                            $select_rem_qty = queryGet("SELECT * FROM `erp_purchase_register_item_delivery_schedule` WHERE `pr_delivery_id` = $deliveryScheduleId");
                            $prev_rem_qty = $select_rem_qty['data']['remaining_qty'];
                            if ($prev_rem_qty < $item['qty']) {
                                $remaining_qty = 0;
                            } else {
                                $remaining_qty = $prev_rem_qty - $item['qty'];
                            }

                            $update_rem_qty = queryGet("UPDATE `erp_purchase_register_item_delivery_schedule` SET remaining_qty = '" . $remaining_qty . "' WHERE `pr_delivery_id` = $deliveryScheduleId");
                        }



                        //$total_remaining_qty =  $item['qty'];
                        // $pr_del_sql = queryGet("SELECT * FROM `erp_purchase_register_item_delivery_schedule` WHERE `pr_item_id` = $prItemId ORDER BY `pr_delivery_id` ASC", true);
                        // foreach ($pr_del_sql['data'] as $pr_del) {
                        //     $pr_delivery_id = $pr_del['pr_delivery_id'];
                        //     $pr_del_qty = $pr_del['remaining_qty'];
                        //     if ($pr_del_qty > 0) {
                        //         if ($pr_del_qty < $total_remaining_qty) {

                        //             $pr_remaining_qty = $total_remaining_qty - $pr_del_qty;

                        //             $total_remaining_qty = $pr_remaining_qty;

                        //             $update_pr_del_schedule = "UPDATE `erp_purchase_register_item_delivery_schedule` SET `remaining_qty` =  $total_remaining_qty WHERE `pr_delivery_id` = $pr_delivery_id";
                        //         } else {
                        //             $update_pr_del_schedule = "UPDATE `erp_purchase_register_item_delivery_schedule` SET `remaining_qty` =  0 WHERE `pr_delivery_id` = $pr_delivery_id";
                        //         }
                        //     }
                        // }
                        //  console($pr_del_sql);
                    } else {
                        $returnData = $insConn;
                    }
                    if ($item_pr_id != 0 || $item_pr_id != "") {

                        $check_sql = queryGet("SELECT count(prItemId) FROM `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "` WHERE `prId`=$item_pr_id");
                        // console($check_sql);
                        $check = $check_sql['data']['count(prItemId)'];
                        $check_closed_item_sql = queryGet("SELECT count(prItemId) FROM `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "` WHERE `prId`=$item_pr_id AND `remainingQty`=0");
                        $check_closed_item =  $check_closed_item_sql['data']['count(prItemId)'];
                        if ($check == $check_closed_item) {
                            $update_pr = queryUpdate("UPDATE `" . ERP_BRANCH_PURCHASE_REQUEST . "` SET  `pr_status`=10 WHERE `purchaseRequestId`=$item_pr_id ");
                            // console($update_pr);
                            // $auditTrail['action_data']['Purchase Order Details']['pr_status'] = 10;
                        }
                    }
                }
                // exit();
                $total_quan_sql = queryGet("SELECT count(`po_item_id`) as total_items, SUM(`total_price`) as total_price FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` WHERE `po_id`=$lastId  ");
                $totalItems = $total_quan_sql['data']['total_items'];
                $totalPrice = $total_quan_sql['data']['total_price'];
                $poItem = "SELECT SUM(`unitPrice`) AS 'amount' , SUM(`qty`) AS 'qty' FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` WHERE `po_id`=$lastId  ";
                $poConn = queryGet($poItem);
                $poData = $poConn['data'] ?? "";
                $amount = $poData['amount'];
                $sumtransQuery = "SELECT SUM(`transportationAmount`) as amount FROM `erp_branch_po_transport_detail` WHERE `poNumber`='" . $returnPoNo . "'";
                $transQuery = queryGet($sumtransQuery);
                $transData = $transQuery['data'] ?? "";
                $total_transport_amount = $transData['amount'];
                $poItemQuery = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` WHERE `po_id`= '" . $lastId . "' ";
                $poQuery = queryGet($poItemQuery, true);
                $poItemData = $poQuery['data'];
                foreach ($poItemData as $item) {
                    $item_total_price = $item['total_price'];
                    $total_transport_cost =  ($total_transport_amount / $amount) * $item_total_price;
                    $insert_trans = "UPDATE `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "`  SET `total_transport_cost`='" . $total_transport_cost . "' WHERE `po_item_id`='" . $item['po_item_id'] . "'";
                    $updateItemiConn = queryInsert($insert_trans);
                    $returnData =  $updateItemiConn;
                }
                $updateDeli = "UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` 
                            SET 
                                `totalItems`='" . $totalItems . "',
                                `totalAmount`='" . $totalPrice . "' WHERE po_id=" . $lastId . "";
                $updateDeliConn = queryInsert($updateDeli);
                // $returnData =  $updateDeliConn;
                $auditTrail['action_data']['Purchase Order Details']['Total_Items'] = decimalQuantityPreview($totalItems);
                $auditTrail['action_data']['Purchase Order Details']['Total_Amount'] = decimalValuePreview($totalPrice);
                $auditTrailreturn = generateAuditTrail($auditTrail);
                $returnData['status'] = "success";
                $returnData['lastQuery'] = $lastQuery;
                $returnData['auditTrailreturn'] = $auditTrailreturn;
                $returnData['message'] = "PO Creation Successful! PO Code is -" . $returnPoNo;
                return $returnData;
            } else {

                return $insConn;
            }
            return $returnData;
        }
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
    function fetchBranchPoListing($company_id, $branch_id, $location_id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `branch_id`='" . $branch_id . "' AND `location_id`='" . $location_id . "' AND `company_id`='" . $company_id . "' ";
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

    function fetchBranchPoListingByVendor($id)
    {


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
                  `uom`='" . $item['uom'] . "'";
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
                                        so_delivery_id='" . $item['deliveryDate2'] . "'";
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

    function uploadInvoice($POST)
    {

        //console($POST);
        $invoice = "";
        $invoice = uploadFile($POST["invoice"], "../public/storage/invoice/", ["jpg", "jpeg", "png"]);
        if ($invoice['status'] == 'success') {
            $invoice = $invoice['data'];
        } else {
            $invoice = '';
        }
        $id = $POST['id'];
        $status = "invoice uploaded";
        $ins = "UPDATE `erp_branch_purchase_order` SET `invoice`='$invoice', `invoice_status`='$status' WHERE `po_id`='" . $id . "' ";


        $returnData = queryUpdate($ins);
        return $returnData;
    }
    function approvePendingPO($po_id) {}

    function fetchCompanyDetails($Id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$Id'";

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

    function editBranchPo($POST)
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        global $updated_by;
        // console($_POST);
        // exit();
        // if($_POST['addresscheckbox'] = 1 ){
        //     $shipTo = $_POST['shipToInput'];
        // }
        // else{
        //     $shipTo = $location_id;
        // }



        //$vendorId = $POST['vendorId'];

        $validitydate = $POST['validitydate'];

        if ($validitydate < date('Y-m-d')) {

            $returnData['status'] = "warning";

            $returnData['message'] = "Validation Date Wrong";

            return $returnData;
        }


        $deliveryDate = $POST['deliveryDate'];
        $costCenter = $POST['costCenter'] ?? 0;
        $refNo = $POST['refNo'];
        $poDate = $POST['podatecreation'];
        $use_type = $POST['usetypesDropdown'];
        $po_type = $POST['potypes'];
        // $inco = $POST['domestic'] ?? "";
        $pr_id = $POST['pr_id'] ?? 0;
        $vendorId = $POST['vendorId'] ?? '';
        $remarks = $POST['extra_remark'];
        // $po_status = 13;
        // *************** //

        $igst = $POST['update_igstInput'];
        $sgst = $POST['update_sgstInput'];
        $cgst = $POST['update_cgstInput'];
        $total_gst = $igst + $sgst + $cgst;
        $subtotal = $POST['update_subTotal'];

        $shipTo = $POST['shipToInput'];
        //console($POST['shipToAddress']);
        $po_id = $POST['po_id'];

        $returnPoNo_sql = "SELECT po_number , inco_type FROM erp_branch_purchase_order WHERE po_id = $po_id";
        $returnPoNo = queryGet($returnPoNo_sql)['data']['po_number'];
        $inco = queryGet($returnPoNo_sql)['data']['inco_type'];


        $update = "UPDATE `erp_branch_purchase_order` 
                        SET 
                            `delivery_date`='" . $deliveryDate . "',
                            `cost_center`='" . $costCenter . "',
                            `ref_no`='" . $refNo . "',
                            `po_date`='" . $poDate . "',
                            `branch_id`=$branch_id,
                            `company_id`=$company_id,
                            `location_id`=$location_id,
                            `bill_address`=$location_id,
                            `ship_address`=$shipTo ,
                            `validityperiod`  = '$validitydate',
                            `total_gst` = '" . $total_gst . "',
                            `total_igst` = '" . $igst . "',
                            `total_cgst` = '" . $cgst . "',
                            `total_sgst` = '" . $sgst . "',
                            `subtotal` = '" . $subtotal . "',
                            `remarks` = '" . $remarks . "',
                            `updated_by`='" . $updated_by . "'
                        WHERE `po_id`=$po_id";
        // exit();

        $insConn = queryUpdate($update);
        //  console($insConn);

        if ($insConn['status'] == "success") {

            $lastId = $insConn['insertedId'];

            $listItem = $POST['listItem'];
            //  console($listItem);

            $totalItems = count($listItem);
            $totalAmount = 0;
            $i = 1;

            $total = $POST['update_totalAmt'];
            //   console($listItem);
            // exit();
            $delete_item = queryDelete("DELETE FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` WHERE  `po_id` = $po_id");
            $delete_delivery = queryDelete("DELETE FROM `" . ERP_BRANCH_PURCHASE_ORDER_DELIVERY_SCHEDULE . "` WHERE  `po_id` = $po_id");

            $costCenterValue = '';
            if ($costCenter != '' || $costCenter != NULL || $costCenter != 0) {

                $costCenterValue = queryGet("SELECT CostCenter_code FROM `" . ERP_COST_CENTER . "` WHERE CostCenter_status='active' AND branch_id = $branch_id AND company_id = $company_id AND CostCenter_id = $costCenter")['data']['CostCenter_code'];
            }

            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'EDIT';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_PURCHASE_ORDER;
            $auditTrail['basicDetail']['column_name'] = 'po_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $po_id;  // primary key
            $auditTrail['basicDetail']['party_type'] = 'vendor';
            $auditTrail['basicDetail']['party_id'] = $vendorId;
            $auditTrail['basicDetail']['document_number'] = $returnPoNo;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = ' PO Updated';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Edit';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($update);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            foreach ($listItem as $item) {

                $countI = $i++;
                $totalPrice =  $item['update_totalPrice'];
                //    console($item);
                //    exit();
                $itemCode = $item['itemCode'];

                $gstAmount = $item['update_gstAmount'];
                $gst = $item['update_gst'];

                // $totalPrice =  $item['update_totalAmt'];
                $itemCode = $item['update_itemCode'];
                $insItem = queryInsert("INSERT INTO `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "`
                    SET
                      `po_id`= $po_id,
                      `lineNo`='" . $countI . "',
                      `inventory_item_id`='" . $item['update_itemId'] . "',
                      `itemCode`='" . $item['update_itemCode'] . "',
                      `itemName`='" . $item['update_itemName'] . "',
                      `unitPrice`='" . $item['update_unitPrice'] . "',
                      `qty`='" . $item['update_qty'] . "',
                      `remainingQty` ='" . $item['update_qty'] . "',
                      `uom`='" . $item['update_uom'] . "',
                      `gst` = '" . $gst . "',
                      `gstAmount` = '" . $gstAmount . "',
                      `total_price` = '" . $totalPrice . "' ");

                //  console($insItem);

                // $auditTrail['action_data']['Item Details'][$itemCode]['Line_No'] = $countI;
                // $auditTrail['action_data']['Item Details'][$itemCode]['inventory_item_id'] = $item['itemId'];
                $auditTrail['action_data']['Item Details'][$itemCode]['Item_Code'] = $item['update_itemCode'];
                $auditTrail['action_data']['Item Details'][$itemCode]['Item_Name'] = $item['update_itemName'];
                $auditTrail['action_data']['Item Details'][$itemCode]['Unit_Price'] = decimalValuePreview($item['update_unitPrice']);
                $auditTrail['action_data']['Item Details'][$itemCode]['Qty'] = decimalQuantityPreview($item['update_qty']);
                $auditTrail['action_data']['Item Details'][$itemCode]['UOM'] = $item['update_uom'];
                $auditTrail['action_data']['Item Details'][$itemCode]['Total_Price'] = decimalValuePreview($totalPrice);


                $lastItemId = $insItem['insertedId'];

                if ($insItem['status'] == "success") {


                    $returnData['itemLastID'] = $dbCon->insert_id;
                    $tot = $item['unitPrice'] * $item['qty'];
                    $totalAmount = $totalAmount + $tot;
                    // console($item['deliverySchedule']);
                    foreach ($item['deliverySchedule'] as $delItem) {
                        //   console($delItem);
                        $rand = 'del' . rand(11111, 999999);
                        if ($delItem['multiDeliveryDate'] == "") {
                            $date = $deliveryDate;
                            $quantity = $item['update_qty'];
                        } else {
                            $date = $delItem['multiDeliveryDate'];
                            $quantity = $delItem['quantity'];
                        }
                        $insDeliConn = queryInsert("INSERT INTO `" . ERP_BRANCH_PURCHASE_ORDER_DELIVERY_SCHEDULE . "` 
                                                        SET 
                                                        `po_item_id`=$lastItemId,
                                                        `delivery_date`='" . $date . "',
                                                        `deliveryStatus`='open',
                                                        `po_id` = $po_id,
                                                        `qty`='" . $quantity . "'");



                        //  console($insDeliConn);

                        if ($insDeliConn['status'] == "success") {

                            $returnData = $insDeliConn;

                            $auditTrail['action_data']['Item Details'][$itemCode]['Delivery Schedule Details'][$rand]['Delivery_Date'] = formatDateWeb($date);
                            $auditTrail['action_data']['Item Details'][$itemCode]['Delivery Schedule Details'][$rand]['Delivery_Status'] = 'open';
                            $auditTrail['action_data']['Item Details'][$itemCode]['Delivery Schedule Details'][$rand]['Qty'] = decimalQuantityPreview($quantity);
                        } else {
                            $returnData = $insDeliConn;
                        }
                    }
                } else {
                    $returnData = $insConn;
                }
            }

            // exit();
            $updateDeli = "UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` 
            SET 
                `totalItems`='" . $totalItems . "',
                `totalAmount`='" . $total . "' WHERE po_id=$po_id";
            $updateDeliConn = queryUpdate($updateDeli);

            // $insItemConn = queryInsert($insItem);



            $auditTrail['action_data']['Purchase Order Details']['Po_Number'] = $returnPoNo;
            $auditTrail['action_data']['Purchase Order Details']['Delivery_Date'] = formatDateORDateTime($deliveryDate);
            $auditTrail['action_data']['Purchase Order Details']['Cost_Center'] = $costCenterValue;
            $auditTrail['action_data']['Purchase Order Details']['Ref_No'] = $refNo;
            $auditTrail['action_data']['Purchase Order Details']['Po_Date'] = formatDateORDateTime($poDate);
            $auditTrail['action_data']['Purchase Order Details']['Use_Type'] = $use_type;
            $auditTrail['action_data']['Purchase Order Details']['Po_Type'] = $po_type;
            $auditTrail['action_data']['Purchase Order Details']['Inco_Type'] = $inco;
            // $auditTrail['action_data']['Purchase Order Details']['pr_id'] = $pr_id;
            // $auditTrail['action_data']['Purchase Order Details']['bill_address'] = $location_id;
            // $auditTrail['action_data']['Purchase Order Details']['ship_address'] = $shipTo;
            $auditTrail['action_data']['Purchase Order Details']['Total_Items'] = decimalQuantityPreview($totalItems);
            $auditTrail['action_data']['Purchase Order Details']['Total_Amount'] = decimalValuePreview($total);

            $auditTrailreturn = generateAuditTrail($auditTrail);


            return $insItem;
        }
        return $returnData;
    }
    function editBranchPo2($POST)
    {
        $returnData = [];
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        global $updated_by;
        // console($_POST);
        // exit();
        // if($_POST['addresscheckbox'] = 1 ){
        //     $shipTo = $_POST['shipToInput'];
        // }
        // else{
        //     $shipTo = $location_id;
        // }



        //$vendorId = $POST['vendorId'];

        $validitydate = $POST['validitydate'];

        if ($validitydate < date('Y-m-d')) {

            $returnData['status'] = "warning";

            $returnData['message'] = "Validation Date Wrong";

            return $returnData;
        }


        $deliveryDate = $POST['deliveryDate'];
        $costCenter = $POST['costCenter'] ?? 0;
        $refNo = $POST['refNo'];
        $poDate = $POST['podatecreation'];
        $use_type = $POST['usetypesDropdown'];
        $po_type = $POST['potypes'];
        // $inco = $POST['domestic'] ?? "";
        $pr_id = $POST['pr_id'] ?? 0;
        $vendorId = $POST['vendorId'] ?? '';
        $remarks = $POST['extra_remark'];
        // $po_status = 13;
        // *************** //

        $igst = $POST['update_igstInput'];
        $sgst = $POST['update_sgstInput'];
        $cgst = $POST['update_cgstInput'];
        $total_gst = $igst + $sgst + $cgst;
        $subtotal = $POST['update_subTotal'];

        $companyCountry = $_SESSION["logedBranchAdminInfo"]["companyCountry"];
        if ($companyCountry == "103") {
            $taxData = json_decode($POST['gstdetails'], true);
            foreach ($taxData as $tax) {
                if ($tax['gstType'] === 'CGST') {
                    $cgst = $tax['taxAmount'];
                } elseif ($tax['gstType'] === 'SGST') {
                    $sgst = $tax['taxAmount'];
                } elseif ($tax['gstType'] === "IGST") {
                    $igst = $tax['taxAmount'];
                }
            }

            $total_gst = $igst + $sgst + $cgst;
            $taxComponents = $POST['gstdetails'];
        } else {
            $igst = 0;
            $sgst = 0;
            $cgst =  0;
            $total_gst = $POST['grandTaxAmtInp'];
            $taxComponents = $POST['gstdetails'];
        }

        $shipTo = $POST['shipToInput'];
        //console($POST['shipToAddress']);
        $po_id = $POST['po_id'];
        $update = "UPDATE `erp_branch_purchase_order` 
                        SET 
                            `delivery_date`='" . $deliveryDate . "',
                            `cost_center`='" . $costCenter . "',
                            `ref_no`='" . $refNo . "',
                            `po_date`='" . $poDate . "',
                            `branch_id`=$branch_id,
                            `company_id`=$company_id,
                            `location_id`=$location_id,
                            `bill_address`=$location_id,
                            `ship_address`=$shipTo ,
                            `taxComponents`=$taxComponents,
                            `validityperiod`  = '$validitydate',
                            `total_gst` = '" . $total_gst . "',
                            `total_igst` = '" . $igst . "',
                            `total_cgst` = '" . $cgst . "',
                            `total_sgst` = '" . $sgst . "',
                            `subtotal` = '" . $subtotal . "',
                            `remarks` = '" . $remarks . "',
                            `updated_by`='" . $updated_by . "'
                        WHERE `po_id`=$po_id";
        // exit();

        $insConn = queryUpdate($update);
        //  console($insConn);

        if ($insConn['status'] == "success") {

            $lastId = $insConn['insertedId'];

            $listItem = $POST['listItem'];
            //  console($listItem);

            $totalItems = count($listItem);
            $totalAmount = 0;
            $i = 1;

            $total = $POST['update_totalAmt'];
            //   console($listItem);
            // exit();
            $delete_item = queryDelete("DELETE FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` WHERE  `po_id` = $po_id");
            $delete_delivery = queryDelete("DELETE FROM `" . ERP_BRANCH_PURCHASE_ORDER_DELIVERY_SCHEDULE . "` WHERE  `po_id` = $po_id");


            $costCenterValue = '';
            if ($costCenter != '' || $costCenter != NULL || $costCenter == 0) {

                $costCenterValue = queryGet("SELECT CostCenter_code FROM `" . ERP_COST_CENTER . "` WHERE CostCenter_status='active' AND branch_id = $branch_id AND company_id = $company_id AND CostCenter_id = $costCenter")['data']['CostCenter_code'];
            }

            $returnPoNo_sql = "SELECT po_number , inco_type FROM erp_branch_purchase_order WHERE po_id = $po_id";
            $returnPoNo = queryGet($returnPoNo_sql)['data']['po_number'];
            $inco = queryGet($returnPoNo_sql)['data']['inco_type'];

            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'EDIT';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_PURCHASE_ORDER;
            $auditTrail['basicDetail']['column_name'] = 'po_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $po_id;  // primary key
            $auditTrail['basicDetail']['party_type'] = 'vendor';
            $auditTrail['basicDetail']['party_id'] = $vendorId;
            $auditTrail['basicDetail']['document_number'] = $returnPoNo;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = ' PO Updated';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Edit';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($update);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            foreach ($listItem as $item) {

                $countI = $i++;
                $totalPrice =  $item['update_totalPrice'];
                //    console($item);
                //    exit();
                $itemCode = $item['itemCode'];

                $gstAmount = $item['update_gstAmount'];
                $gst = $item['update_gst'];

                // $totalPrice =  $item['update_totalAmt'];
                $itemCode = $item['update_itemCode'];
                $insItem = queryInsert("INSERT INTO `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "`
                    SET
                      `po_id`= $po_id,
                      `lineNo`='" . $countI . "',
                      `inventory_item_id`='" . $item['update_itemId'] . "',
                      `itemCode`='" . $item['update_itemCode'] . "',
                      `itemName`='" . $item['update_itemName'] . "',
                      `unitPrice`='" . $item['update_unitPrice'] . "',
                      `qty`='" . $item['update_qty'] . "',
                      `remainingQty` ='" . $item['update_qty'] . "',
                      `uom`='" . $item['update_uom'] . "',
                      `gst` = '" . $gst . "',
                      `gstAmount` = '" . $gstAmount . "',
                      `total_price` = '" . $totalPrice . "' ");

                //  console($insItem);

                // $auditTrail['action_data']['Item Details'][$itemCode]['Line_No'] = $countI;
                // $auditTrail['action_data']['Item Details'][$itemCode]['inventory_item_id'] = $item['itemId'];
                $auditTrail['action_data']['Item Details'][$itemCode]['Item_Code'] = $item['update_itemCode'];
                $auditTrail['action_data']['Item Details'][$itemCode]['Item_Name'] = $item['update_itemName'];
                $auditTrail['action_data']['Item Details'][$itemCode]['Unit_Price'] = decimalValuePreview($item['update_unitPrice']);
                $auditTrail['action_data']['Item Details'][$itemCode]['Qty'] = decimalQuantityPreview($item['update_qty']);
                $auditTrail['action_data']['Item Details'][$itemCode]['UOM'] = $item['update_uom'];
                $auditTrail['action_data']['Item Details'][$itemCode]['Total_Price'] = decimalValuePreview($totalPrice);


                $lastItemId = $insItem['insertedId'];

                if ($insItem['status'] == "success") {


                    $returnData['itemLastID'] = $dbCon->insert_id;
                    $tot = $item['unitPrice'] * $item['qty'];
                    $totalAmount = $totalAmount + $tot;
                    // console($item['deliverySchedule']);
                    foreach ($item['deliverySchedule'] as $delItem) {
                        //   console($delItem);
                        $rand = 'del' . rand(11111, 999999);
                        if ($delItem['multiDeliveryDate'] == "") {
                            $date = $deliveryDate;
                            $quantity = $item['update_qty'];
                        } else {
                            $date = $delItem['multiDeliveryDate'];
                            $quantity = $delItem['quantity'];
                        }
                        $insDeliConn = queryInsert("INSERT INTO `" . ERP_BRANCH_PURCHASE_ORDER_DELIVERY_SCHEDULE . "` 
                                                        SET 
                                                        `po_item_id`=$lastItemId,
                                                        `delivery_date`='" . $date . "',
                                                        `deliveryStatus`='open',
                                                        `po_id` = $po_id,
                                                        `qty`='" . $quantity . "'");



                        //  console($insDeliConn);

                        if ($insDeliConn['status'] == "success") {

                            $returnData = $insDeliConn;

                            $auditTrail['action_data']['Item Details'][$itemCode]['Delivery Schedule Details'][$rand]['Delivery_Date'] = formatDateWeb($date);
                            $auditTrail['action_data']['Item Details'][$itemCode]['Delivery Schedule Details'][$rand]['Delivery_Status'] = 'open';
                            $auditTrail['action_data']['Item Details'][$itemCode]['Delivery Schedule Details'][$rand]['Qty'] = decimalQuantityPreview($quantity);
                        } else {
                            $returnData = $insDeliConn;
                        }
                    }
                } else {
                    $returnData = $insConn;
                }
            }

            // exit();
            $updateDeli = "UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` 
            SET 
                `totalItems`='" . $totalItems . "',
                `totalAmount`='" . $total . "' WHERE po_id=$po_id";
            $updateDeliConn = queryUpdate($updateDeli);

            // $insItemConn = queryInsert($insItem);


            $auditTrail['action_data']['Purchase Order Details']['Po_Number'] = $returnPoNo;
            $auditTrail['action_data']['Purchase Order Details']['Delivery_Date'] = formatDateORDateTime($deliveryDate);
            $auditTrail['action_data']['Purchase Order Details']['Cost_Center'] = $costCenterValue;
            $auditTrail['action_data']['Purchase Order Details']['Ref_No'] = $refNo;
            $auditTrail['action_data']['Purchase Order Details']['Po_Date'] = formatDateORDateTime($poDate);
            $auditTrail['action_data']['Purchase Order Details']['Use_Type'] = $use_type;
            $auditTrail['action_data']['Purchase Order Details']['Po_Type'] = $po_type;
            $auditTrail['action_data']['Purchase Order Details']['Inco_Type'] = $inco;
            // $auditTrail['action_data']['Purchase Order Details']['pr_id'] = $pr_id;
            // $auditTrail['action_data']['Purchase Order Details']['bill_address'] = $location_id;
            // $auditTrail['action_data']['Purchase Order Details']['ship_address'] = $shipTo;
            $auditTrail['action_data']['Purchase Order Details']['Total_Items'] = decimalQuantityPreview($totalItems);
            $auditTrail['action_data']['Purchase Order Details']['Total_Amount'] = decimalValuePreview($total);

            $auditTrailreturn = generateAuditTrail($auditTrail);


            return $insItem;
        }
        //  return $returnData;
    }

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

    function fetchBranchPoById($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE po_id='$id' AND status != 'deleted'";
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

    function fetchBranchDetailsById($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCHES . "` WHERE branch_id='$id'";
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
    function fetchLocationDetailsById($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE othersLocation_id ='$id'";
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
    function fetchCompanyBankId($id)
    {
        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `erp_acc_bank_cash_accounts` WHERE company_id='$id'";
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
    function fetchPoItemDetails($id)
    {

        $returnData = [];
        global $dbCon;

        $ins = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `itemId`='$id'";
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
}
