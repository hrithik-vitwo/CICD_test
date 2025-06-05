<?php
    include("../../../app/v1/functions/common/func-common.php");
    require_once("api-common-func.php");


    class POUpload
    {
    function uploadPO($POST_DATA = [],$company_id,$branch_id,$location_id,$user_id,$declaration = 0)
    {
        $created_by = $user_id."|company";
        $updated_by = $user_id."|company";
        $returnData = [];
        $i = 0;
        $flag = [];
        $error_flag = 0;

        foreach($POST_DATA as $POST)
        {

            // $storage_location_code = $POST[""];

            // $storage_location_query = queryGet("SELECT * FROM `erp_storage_location` WHERE `company_id` = '" . $company_id . "' AND `branch_id`='".$branch_id."' AND `location_id`='".$location_id."' AND `storage_location_code`='".$storage_location_code."'",false);

            // if($storage_location_query["numRows"] == 0)
            // {
            //     $returnData['status'] = "warning";
            //     $returnData['message'] = "Storage Location not exists";
            //     $flag[] = array("status"=>"warning","message"=>"Storage Location not exists at line ".$i);
            //     $i++;
            //     continue;
            // }
            // else
            // {
                
            // }

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
            //Vendor
            $vendorName = $POST["vendorName"];
            $vendorCode = $POST["vendorCode"];

            $vendorQuery = queryGet("SELECT * FROM `erp_vendor_details` WHERE `company_id` = '" . $company_id . "' AND `company_branch_id`='".$branch_id."' AND `location_id`='".$location_id."' AND `trade_name`='".$vendorName."' AND `vendor_code`='".$vendorCode."'",false);

            if($vendorQuery["numRows"] == 0)
            {
                $returnData['status'] = "warning";
                $returnData['message'] = "Vendor not exists";
                $flag[] = array("status"=>"warning","message"=>"Vendor not exists at line ".$i);
                $error_flag++;
                $i++;
                continue;
            }
            else
            {
                
            $vendorId = $vendorQuery['data']["vendor_id"];
            $deliveryDate = $POST['deliveryDate'];
            $costCenter = $POST['costCenter'] ?? 0;
            $poDate = $POST['podatecreation'];
            $use_type = $POST['usetypesDropdown'];
            if($use_type == "servicep"){
                $service_po = "yes";
                $uomType = "service";
            }
            else{
                $service_po = "no";  
                $uomType = "material";
            }
            $po_type = $POST['potypes'];
            $inco = $POST['domestic'] ?? "";
            $pr_id = $POST['pr_id'] ?? 0;
            $po_status = 9;

            if (isset($POST["shipToInput"]) && $POST["shipToInput"] != '') {

                $shipToCode = strtolower($_POST['shipToInput']);

                $check_myArray = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `company_id` =  ".$company_id." AND `branch_id`=".$branch_id." AND LOWER(`othersLocation_code`) LIKE '%" . $shipToCode . "%'");

                if($check_myArray["numRows"] == 0)
                {
                    //Insert
                    // $insuom = "INSERT INTO `erp_inventory_mstr_uom` 
                    //                 SET
                    //                     `companyId`='" . $company_id . "',
                    //                     `uomName`='".$POST['uom']."',
                    //                     `uomDesc`='".$POST['uom']."',
                    //                     `uomType`='".$uomType."',
                    //                     `uomCreatedBy`='".$created_by."',
                    //                     `uomUpdatedBy`='".$created_by."'";
                    // //exit();

                    // $insertItemUom = queryInsert($insuom);

                    $shipTo = 0;
                }
                else
                {
                    //get ID
                    $shipTo = $check_myArray["data"]["othersLocation_id"];
                }


            } else {
                $shipTo = 0;
            }


            if (isset($POST["uom"]) && $POST["uom"] != '') {

                $uom = $POST["uom"];

                $check_myArray = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `companyId` =  ".$company_id." AND `uomName` = '" . $uom . "'");

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
                $currency_value = strtolower($POST["currency"]);
                $check_currency = queryGet("SELECT * FROM `erp_currency_type` WHERE  LOWER(`currency_name`) LIKE '%" . $currency_value . "%'",false);

                $currencyId = $check_currency["data"]["currency_id"];
                $currencyName = $check_currency["data"]["currency_name"];
            } else {
                $currencyId = 0;
            }
    
    
            if (isset($POST['curr_rate']) && $POST["curr_rate"] != '') {
                $conversion = $POST["curr_rate"] ?? 0;
            } else {
                $conversion = 0;
            }

            $lastQuery = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `company_id`=$company_id ORDER BY `po_id` DESC LIMIT 1";
            $last = queryGet($lastQuery);
            $lastRow = $last['data'] ?? "";
            $lastPoNo = $lastRow['po_number'] ?? "";
            $returnPoNo = getPoSerialNumber($lastPoNo);

            $po_number = $POST["po_number"];

            $po_check_query = queryGet("SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `company_id`='".$company_id."' AND `branch_id`='".$branch_id."' AND `location_id`='".$location_id."' AND `po_number`='".$po_number."'",false);

            if($po_check_query["numRows"] == 0)
            {
                //Insert to PO table and PO item table
                 $insert = "INSERT INTO `erp_branch_purchase_order` 
                            SET `po_number`='$returnPoNo',
                             `vendor_id`='$vendorId',
                             `delivery_date`='$deliveryDate',
                             `cost_center`='$costCenter',
                             `ref_no`='$po_number',
                            `po_date`='$poDate',
                            `use_type`='$use_type',
                            `po_type`='$po_type',
                            `inco_type`='$inco',
                            `branch_id`=$branch_id,
                            `company_id`=$company_id,
                             `pr_id`=$pr_id,
                            `location_id`=$location_id,
                            `bill_address`=$location_id,
                            `ship_address`=$shipTo,
                            `service_po`='$service_po',
                            `po_status`= $po_status,
                            `functional_area`=$functionalAreas,
                            `currency`=$currencyId,
                            `conversion_rate`='" . $conversion . "'";

            $insConn = queryInsert($insert);


            if ($insConn['status'] == "success") 
            {
                $last_po_id =  $insConn['insertedId'];
                $lastId = $insConn['insertedId'];

                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_PURCHASE_ORDER;
                $auditTrail['basicDetail']['column_name'] = 'po_id'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $lastId;  // primary key
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
                $auditTrail['action_data']['Purchase Order Details']['cost_center'] = $costCenter;
                $auditTrail['action_data']['Purchase Order Details']['ref_no'] = $po_number;
                $auditTrail['action_data']['Purchase Order Details']['po_date'] = formatDateORDateTime($poDate);
                $auditTrail['action_data']['Purchase Order Details']['use_type'] = $use_type;
                $auditTrail['action_data']['Purchase Order Details']['po_type'] = $po_type;
                $auditTrail['action_data']['Purchase Order Details']['inco_type'] = $inco;
                $auditTrail['action_data']['Purchase Order Details']['pr_id'] = $pr_id;
                $auditTrail['action_data']['Purchase Order Details']['bill_address'] = $location_id;
                $auditTrail['action_data']['Purchase Order Details']['ship_address'] = $shipTo;
                $auditTrail['action_data']['Purchase Order Details']['service_po'] = 'no';
                $auditTrail['action_data']['Purchase Order Details']['functional_area'] = $functionalAreas;
                $auditTrail['action_data']['Purchase Order Details']['currency'] = $currencyId;
                $auditTrail['action_data']['Purchase Order Details']['conversion_rate'] = $conversion;

                $itemId = $itemQuery["data"]["itemId"];
                $unitPrice = $POST["unitPrice"];
                $quantity = $POST["qty"];
                $remaining_quantity = $POST["remainingQty"];
                $totalPrice =  $unitPrice * $quantity;    
                $countI = 1;

                // $listItem = $POST['listItem'];

                $insItem = "INSERT INTO `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "`
                
                    SET
                        `po_id`='" . $lastId . "',
                        `lineNo`='" . $countI . "',
                        `inventory_item_id`='" . $itemId . "',
                        `itemCode`='" . $itemCode . "',
                        `itemName`='" . $itemName . "',
                        `unitPrice`='" . $unitPrice . "',
                        `qty`='" . $quantity . "',
                        `remainingQty`='" . $remaining_quantity . "',
                        `total_price` = '" . $totalPrice . "',
                        `uom`='" . $uomId . "'
                    ";

                    $insItemConn = queryInsert($insItem);

                    $auditTrail['action_data']['Item Details'][$itemCode]['lineNo'] = $countI;
                    $auditTrail['action_data']['Item Details'][$itemCode]['inventory_item_id'] = $itemId;
                    $auditTrail['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                    $auditTrail['action_data']['Item Details'][$itemCode]['itemName'] = $itemName;
                    $auditTrail['action_data']['Item Details'][$itemCode]['unitPrice'] = $unitPrice;
                    $auditTrail['action_data']['Item Details'][$itemCode]['qty'] = $quantity;
                    $auditTrail['action_data']['Item Details'][$itemCode]['uom'] = $uomId;
                    $auditTrail['action_data']['Item Details'][$itemCode]['remainingQty'] = $quantity;
                    $auditTrail['action_data']['Item Details'][$itemCode]['total_price'] = $totalPrice;
                    // $auditTrail['action_data']['Item Details'][$itemCode]['total_transport_cost'] = $total_transport_cost;

                    $auditTrailreturn = generateAuditTrail($auditTrail);

                    $lastItemId = $insItemConn['insertedId'];

                    $returnData['status'] = "success";
                    $returnData['message'] = "Data submitted successfully";
                    $flag[] = array("status"=>"warning","message"=>"Data submitted successfully at line ".$i);

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
                //Insert to PO item Table
                $last_po_id =  $po_check_query["data"]["po_id"];
                $lastId = $po_check_query["data"]["po_id"];

                $itemId = $itemQuery["data"]["itemId"];
                $unitPrice = $POST["unitPrice"];
                $quantity = $POST["qty"];
                $remaining_quantity = $POST["remainingQty"];
                $totalPrice =  $unitPrice * $quantity;    
                $countI = 1;

                // $listItem = $POST['listItem'];

                $insItem = "INSERT INTO `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "`
                
                    SET
                        `po_id`='" . $lastId . "',
                        `lineNo`='" . $countI . "',
                        `inventory_item_id`='" . $itemId . "',
                        `itemCode`='" . $itemCode . "',
                        `itemName`='" . $itemName . "',
                        `unitPrice`='" . $unitPrice . "',
                        `qty`='" . $quantity . "',
                        `remainingQty`='" . $remaining_quantity . "',
                        `total_price` = '" . $totalPrice . "',
                        `uom`='" . $uomId . "'
                    ";

                    $insItemConn = queryInsert($insItem);

                    $auditTrail['action_data']['Item Details'][$itemCode]['lineNo'] = $countI;
                    $auditTrail['action_data']['Item Details'][$itemCode]['inventory_item_id'] = $itemId;
                    $auditTrail['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                    $auditTrail['action_data']['Item Details'][$itemCode]['itemName'] = $itemName;
                    $auditTrail['action_data']['Item Details'][$itemCode]['unitPrice'] = $unitPrice;
                    $auditTrail['action_data']['Item Details'][$itemCode]['qty'] = $quantity;
                    $auditTrail['action_data']['Item Details'][$itemCode]['uom'] = $uomId;
                    $auditTrail['action_data']['Item Details'][$itemCode]['remainingQty'] = $quantity;
                    $auditTrail['action_data']['Item Details'][$itemCode]['total_price'] = $totalPrice;
                    // $auditTrail['action_data']['Item Details'][$itemCode]['total_transport_cost'] = $total_transport_cost;

                    $auditTrailreturn = generateAuditTrail($auditTrail);

                    $lastItemId = $insItemConn['insertedId'];

                    $returnData['status'] = "success";
                    $returnData['message'] = "Data submitted successfully";
                    $flag[] = array("status"=>"success","message"=>"Data submitted successfully at line ".$i);
                    $error_flag++;

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
                            `migration_type`='OpenPo',
                            `declaration`='$declaration_value',
                            `created_by`='$created_by',
                            `updated_by`='$created_by' 
                            ";
                            queryInsert($insvalidation);

        return $total_array;
    }
    }


?>