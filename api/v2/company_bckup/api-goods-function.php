<?php
include("../../../app/v1/functions/common/func-common.php");
require_once("api-common-func.php");
class GoodsController
{
    function createGoods($POST_DATA = [],$company_id,$branch_id,$location_id,$user_id,$declaration = 0)
    {
        global $dbCon;
        $created_by = $user_id;
        $updated_by = $user_id;
        $returnData = [];
        $i = 0;
        $flag = [];
        $error_flag = 0;
        foreach($POST_DATA as $POST)
        {
            $itemName = $POST["itemName"];
            if($itemName == "" || $itemName == NULL)
            {
                $flag[] = array("status"=>"warning","message"=>"Item added failed at line ".$i);
                $i++;
                $error_flag++;
                continue;
            }

            $item_checking = queryGet("SELECT * FROM `erp_inventory_items` WHERE `company_id` = '" . $company_id . "' AND `itemName`='".$itemName."'",false);

            if($item_checking["numRows"] != 0)
            {
                $flag[] = array("status"=>"warning","message"=>"Item already added at line ".$i);
                $i++;
                $error_flag++;
                continue;
            }
        //console($POST);
        $myArray = strtolower(trim($POST['goodsGroup']));

        $check_myArray = queryGet("SELECT * FROM `erp_inventory_mstr_good_groups` WHERE `companyId` =  ".$company_id." AND LOWER(TRIM(`goodGroupName`)) LIKE '%" . $myArray . "%'");

        if($check_myArray["numRows"] == 0)
        {
            //Insert
            $insgoodgroup = "INSERT INTO `erp_inventory_mstr_good_groups` 
                            SET
                                `companyId`='" . $company_id . "',
                                `goodGroupName`='".$POST['goodsGroup']."',
                                `goodGroupDesc`='".$POST['goodsGroup']."',
                                `goodType`='".$POST['goodsType']."',
                                `goodGroupCreatedBy`='".$user_id."',
                                `goodGroupUpdatedBy`='".$user_id."'";
            //exit();

            $insertItemGroup = queryInsert($insgoodgroup);

            $goodsGroup = $insertItemGroup['insertedId'];
        }
        else
        {
            //get ID
            $goodsGroup = $check_myArray["data"]["goodGroupId"];
        }

        $purchase_group_array = strtolower(trim($POST['purchaseGroup']));

        $check_purchase_group = queryGet("SELECT * FROM `erp_inventory_mstr_purchase_groups` WHERE `companyId` =  ".$company_id." AND LOWER(TRIM(`purchaseGroupName`)) LIKE '%" . $purchase_group_array . "%'");

        if($check_purchase_group["numRows"] == 0)
        {
            //Insert
            $inspurchasegroup = "INSERT INTO `erp_inventory_mstr_purchase_groups` 
                            SET
                                `companyId`='" . $company_id . "',
                                `purchaseGroupName`='".$POST['purchaseGroup']."',
                                `purchaseGroupDesc`='".$POST['purchaseGroup']."',
                                `purchaseGroupCreatedBy`='".$user_id."',
                                `purchaseGroupUpdatedBy`='".$user_id."'";
            //exit();

            $insertPurchaseGroup = queryInsert($inspurchasegroup);

            $p_Group = $insertPurchaseGroup['insertedId'];
        }
        else
        {
            //get ID
            $p_Group = $check_purchase_group["data"]["purchaseGroupId"];
        }

        //TDS
        if (isset($POST["tds"]) && $POST["tds"] != '') {

            $tds_section = $POST["tds"];
            $tds_query = queryGet("SELECT `id` FROM `erp_tds_details` WHERE `section` = '" . $tds_section . "'");
            if($tds_query["numRows"] == 0)
            {
                $admin["tds"] = 1;
            }
            else
            {
                $admin["tds"] = $tds_query["data"]["id"];
            }

        } else {
            $admin["tds"] = 1;
        }



        


        // $goodsGroup = null;

        // for ($i = count($myArray) - 1; $i >= 0; $i--) {
        //     if ($myArray[$i] != null) {
        //         $goodsGroup = $myArray[$i];
        //         break;
        //     }
        // }


        //echo $goodsGroup;



        // $spec = $_POST['spec'];
        // foreach($spec as $spec){
        //     console($spec);

        // }
        // exit();
        //console($POST['serviceName']);

        // $isValidate = validate($POST, [
        //     "goodsType" => "required",
        //     "goodsGroup" => "required",
        //     "purchaseGroup" => "required",
        //     "availabilityCheck" => "required",
        //     "itemName" => "required",
        //     "baseUnitMeasure" => "required",
        //     "issueUnit" => "required",
        //     "netWeight" => "required",
        //     "grossWeight" => "required",
        //     "width" => "required",
        //     "length" => "required",
        //     "height" => "required",
        //     "volume" => "required"





        // ]);

        // if ($isValidate["status"] != "success") {
        //     $returnData['status'] = "warning";
        //     $returnData['message'] = "Invalid form inputes";
        //     $returnData['errors'] = $isValidate["errors"];
        //     return $returnData;
        // }

        $accMapp = getAllfetchAccountingMappingTbl($company_id);

        if ($accMapp["status"] == "success") {

            if ($POST['goodsType'] == 3) {
                $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['itemsFG_gl'],$company_id);
                $parentGlId = $paccdetails['data']['id'];
            } else if ($POST['goodsType'] == 1) {
                $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['itemsRM_gl'],$company_id);
                $parentGlId = $paccdetails['data']['id'];
                
            } else if ($POST['goodsType'] == 2) {
                $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['itemsSFG_gl'],$company_id);
                $parentGlId = $paccdetails['data']['id'];
            } else {
                $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['itemsRM_gl'],$company_id);
                $parentGlId = $paccdetails['data']['id'];
            }
            $admin = array();
            //$admin["goodsType"] = $POST["goodsType"];
            $admin["goodsGroup"] = $goodsGroup;
            $admin["purchaseGroup"] = $p_Group ?? 0;
            $admin["itemName"] = $POST["itemName"] ?? 0;
            $admin["netWeight"] = $POST["netWeight"] ?? 0;
            $admin["availabilityCheck"] = $POST["availabilityCheck"] ?? 0;
            $admin["grossWeight"] = $POST["grossWeight"] ?? 0;
            $admin["volume"] = $POST["volume"] ?? 0;
            $admin["height"] = $POST["height"] ?? 0;
            $admin["width"] = $POST["width"] ?? 0;
            $admin["length"] = $POST["length"] ?? 0;
            $admin["itemDesc"] = $POST["itemDesc"] ?? 0;
            $admin["storageControl"] = $POST["storageControl"] ?? 0;
            $admin["maxStoragePeriod"] = $POST["maxStoragePeriod"] ?? 0;
            $admin["maxStoragePeriodTimeUnit"] = $POST["maxTime"] ?? 0;
            $admin["minRemainSelfLife"] = $POST["minRemainSelfLife"] ?? 0;
            $admin["minRemainSelfLifeTimeUnit"] = $POST["minTime"] ?? 0;
            $admin["purchasingValueKey"] = $POST["purchasingValueKey"] ?? 0;
            $admin['uomRel'] = $POST["rel"] > 0 ? $POST["rel"] : 0;
            $admin['volumeCubeCm'] = $POST["volumeCubeCm"] > 0 ? $POST["rel"] : 0;
            $admin['hsn'] = $POST['hsn'] ?? 0;
            $admin['weight_unit'] = $POST['grossWeight'] ?? 0;
            $admin['measure_unit'] = $POST['measure_unit'] ?? 0;
            $admin['measure_unit'] = $POST['measure_unit'] ?? 0;
            $admin["serviceName"] = $POST["serviceName"] ?? 0;
            $admin["serviceDesc"] = $POST["serviceDesc"] ?? 0;
            $admin["serviceUnit"] = $POST["serviceUnit"] ?? 0;
            // $admin["glCode"] = $POST["glCode"] ?? 0;
            $admin["serviceGroup"] = $POST["serviceGroup"] ?? 0;
            $admin["stock_date"] = $POST["stock_date"] ?? 0;
            if (isset($POST["costCenter"]) && $POST["costCenter"] != '') {
                $admin["costCenter"] = $POST["costCenter"] ?? 0;
            } else {
                $admin["costCenter"] = 0;
            }
            //$admin["costCenter"] = $POST["costCenter"] ?? 0;

            if ($POST["stock"] != "") {
                $admin["opening_stock"] =  $POST["stock"];
            } else {
                $admin["opening_stock"] = 0;
            }
            //$admin["opening_stock"] =  isset($POST["stock"]) ? $POST["stock"] : 0;
            // $POST["stock"] ?? 0;
            if ($POST["rate"] != "") {
                $admin["rate"] = $POST["rate"];
            } else {
                $admin["rate"] = 0;
            }
            //  $admin["rate"] = $POST["rate"] ?? 0;
            if ($POST["total"] != "") {
                $admin["total"] = $POST["total"];
            } else {
                $admin["total"] = 0;
            }
            //  $admin["total"] = $POST["total"] ?? 0;
            if ($POST["min_stock"] != "") {
                $min_stock = $POST["min_stock"];
            } else {
                $min_stock = 0;
            }


            if ($POST["max_stock"] != "") {
                $max_stock = $POST["max_stock"];
            } else {
                $max_stock = 0;
            }

            // $min_stock = $POST["min_stock"] ?? 0;
            // $max_stock = $POST["max_stock"] ?? 0;

            // echo 1;
            // echo  $admin["opening_stock"];
            // exit();

            $status = "active";

            $goodTypeId = $POST["goodsType"];
            $checkFg = "SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE  `goodTypeId` = '" . $goodTypeId . "'";
            $resultType = queryGet($checkFg);
            $row = $resultType["data"];
            $goodType = $row['type'];


            if ($goodType == "FG") {
                $bomRequired = $POST['bomRequired_radio'] ?? 0;
                $stock_col = "fgWhOpen";
                if ($bomRequired == 1) {
                    $price_type = "S";
                    
                } elseif ($bomRequired == 0) {
                    // $admin["goodsType"] = 4;
                    $price_type = "V";
                } else {
                    $price_type = "";
                }

                $admin["goodsType"] = $POST["goodsType"];
                $name = $admin["itemName"];
                $desc = $admin["itemDesc"];
                $gl = $parentGlId;
                // $goodTypeId = '3,4';
                $uomType = "material";
            } elseif ($goodType == "SFG") {
                $admin["goodsType"] = $POST["goodsType"];
                $bomRequired = 1;
                $price_type = "S";
                $stock_col = "sfgStockOpen";


                $name = $admin["itemName"];
                $desc = $admin["itemDesc"];
                $gl = $parentGlId;
                $uomType = "material";
            } elseif ($goodType == "RM") {
                $admin["goodsType"] = $POST["goodsType"];
                $bomRequired = 0;
                $price_type = "V";
                $stock_col = "rmWhOpen";


                $name = $admin["itemName"];
                $desc = $admin["itemDesc"];
                $gl = $parentGlId;
                $uomType = "material";
            } elseif ($goodType == "ASSET") {
                $admin["goodsType"] = $POST["goodsType"];
                $bomRequired = 0;
                $price_type = "V";
                $stock_col = "rmWhOpen";


                $name = $admin["itemName"];
                $desc = $admin["itemDesc"];
                // $gl =  $POST["glCodeAsset"];
                
               $gl_value = getChartOfAccountsDataDetailsByCode($POST["glCodeAsset"],$company_id);
               $gl = $gl_value['data']["id"];
                $uomType = "material";
            } elseif ($goodType == "SERVICES" || $goodType == "SERVICEP") {
                $name = $POST['itemName'];
                $desc = $admin['itemDesc'];
                $gl =  $parentGlId;

                $admin["goodsType"] = $POST["goodsType"];
                $bomRequired = 0;
                $price_type = " ";
                $stock_col = " ";
                $uomType = "service";
            } else {
            }

            // $flag[] = array("status"=>"warning","message"=>"Setup Your Accounts first! at line ".$i,"gl"=>$gl);

            if (isset($POST["baseUnitMeasure"]) && $POST["baseUnitMeasure"] != '') {

                $uom = strtolower($POST["baseUnitMeasure"]);
    
                $check_myArray = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `companyId` =  ".$company_id." AND  LOWER(`uomName`) = '" . $uom . "'");
    
                if($check_myArray["numRows"] == 0)
                {
                    //Insert
                    $insuom = "INSERT INTO `erp_inventory_mstr_uom` 
                                    SET
                                        `companyId`='" . $company_id . "',
                                        `uomName`='".$POST['baseUnitMeasure']."',
                                        `uomDesc`='".$POST['baseUnitMeasure']."',
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


            if (isset($POST["issueUnit"]) && $POST["issueUnit"] != '') {

                $uom = strtolower($POST["issueUnit"]);
    
                $check_myArray = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `companyId` =  ".$company_id." AND LOWER(`uomName`) = '" . $uom . "'");
    
                if($check_myArray["numRows"] == 0)
                {
                    //Insert
                    $insuom = "INSERT INTO `erp_inventory_mstr_uom` 
                                    SET
                                        `companyId`='" . $company_id . "',
                                        `uomName`='".$POST['issueUnit']."',
                                        `uomDesc`='".$POST['issueUnit']."',
                                        `uomType`='".$uomType."',
                                        `uomCreatedBy`='".$created_by."',
                                        `uomUpdatedBy`='".$created_by."'";
                    //exit();
    
                    $insertItemUom = queryInsert($insuom);
    
                    $issue_unit_Id = $insertItemUom['insertedId'];
                }
                else
                {
                    //get ID
                    $issue_unit_Id = $check_myArray["data"]["uomId"];
                }
    
    
            } else {
                $issue_unit_Id = 0;
            }



            if (isset($POST["measure_unit"]) && $POST["measure_unit"] != '') {

                $uom = strtolower($POST["measure_unit"]);
    
                $check_myArray = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `companyId` =  ".$company_id." AND LOWER(`uomName`) = '" . $uom . "'");
    
                if($check_myArray["numRows"] == 0)
                {
                    //Insert
                    $insuom = "INSERT INTO `erp_inventory_mstr_uom` 
                                    SET
                                        `companyId`='" . $company_id . "',
                                        `uomName`='".$POST['measure_unit']."',
                                        `uomDesc`='".$POST['measure_unit']."',
                                        `uomType`='".$uomType."',
                                        `uomCreatedBy`='".$created_by."',
                                        `uomUpdatedBy`='".$created_by."'";
                    //exit();
    
                    $insertItemUom = queryInsert($insuom);
    
                    $measure_uomId = $insertItemUom['insertedId'];
                }
                else
                {
                    //get ID
                    $measure_uomId = $check_myArray["data"]["uomId"];
                }
    
    
            } else {
                $measure_uomId = 0;
            }

            $admin["baseUnitMeasure"] = $uomId ?? 0;
            $admin["issueUnitMeasure"] = $issue_unit_Id ?? 0;
            $admin["measure_unit"] = $measure_uomId ?? 0;

            // if ($goodType == "FG") {
            //     $bomRequired = $POST['bomRequired_radio'];
            //     $stock_col = "fgWhOpen";
            //     if ($bomRequired == 1) {
            //         $price_type = "S";
            //         $admin["goodsType"] = $POST["goodsType"];
            //     } elseif ($bomRequired == 0) {
            //         $admin["goodsType"] = 4;
            //         $price_type = "V";
            //     } else {
            //         $price_type = "";
            //     }
            // } else {
            //     $admin["goodsType"] = $POST["goodsType"];
            //     $bomRequired = isset($POST["bomRequired"]) ? 1 : 0;
            //     if ($goodType == "RM" || $goodType == "ASSET") {
            //         $price_type = "V";
            //         $stock_col = "rmWhOpen";
            //     } elseif ($goodType = "SFG") {
            //         $price_type = "S";
            //         $stock_col = "sfgStockOpen";
            //     } else {
            //         $price_type = "";
            //     }
            // }
            //console($goodType);
            // if ($goodType == "SERVICES") {

            //     $name = $POST['serviceName'];
            //     $desc = $admin['serviceDesc'];
            //     $gl =  $admin["glCode"];
            // }elseif($goodType == "SERVICEP"){
            //     $name = $POST['serviceName'];
            //     $desc = $admin['serviceDesc'];
            //     $gl =  $admin["glCode"];
            // }
            //  else {

            //     $name = $admin["itemName"];
            //     $desc = $admin["itemDesc"];
            //     $gl = $parentGlId;
            // }
            // if (isset($_POST['asset_classification'])) {
            //     $filtered_array = array_filter($_POST['asset_classification']);
            //     $asset_class_list =  implode(",", $filtered_array);
            // }
            $dep_key = $POST['dep_key'];



            $lastlQuery = "SELECT itemCode FROM `" . ERP_INVENTORY_ITEMS . "` WHERE  `itemCode` REGEXP '^[0-9]+$' AND `company_id`=$company_id AND `goodsType` IN($goodTypeId)  ORDER BY `itemId` DESC LIMIT 1";
            $resultLast = queryGet($lastlQuery);

            $rowLast = $resultLast["data"];

            $goodType;
            $lastsl = $rowLast['itemCode'];
            $itemCode =  getItemSerialNumber($lastsl, $goodType);

            $insgood = "INSERT INTO `" . ERP_INVENTORY_ITEMS . "` 
                            SET
                                `parentGlId`='" . $gl . "',
                                `itemCode`='" .  $itemCode . "',
                                `itemName`='" . addslashes($name) . "',
                                `itemDesc`='" . addslashes($desc) . "',
                                `netWeight`='" . $admin["netWeight"] . "',
                                `grossWeight`='" . $admin["grossWeight"] . "',
                                `volume`='" .  $admin["volume"]  . "',
                                `height`='" . $admin["height"] . "',
                                `width`='" . $admin["width"]  . "',

                                `length`='" . $admin["length"] . "',
                                `goodsType`='" . $admin["goodsType"] . "',
                                `goodsGroup`='" . $admin["goodsGroup"] . "', 
                                `purchaseGroup`='" . $admin["purchaseGroup"] . "',
                                `branch`='" . $branch_id . "',
                                `location_id`='" . $location_id . "',
                                `company_id`='" . $company_id . "',
                                `availabilityCheck`='" . $admin["availabilityCheck"] . "',
                                `baseUnitMeasure`='" . $admin["baseUnitMeasure"] . "',
                                `issueUnitMeasure`='" . $admin["issueUnitMeasure"] . "',
                                `tds`='" . $admin['tds'] . "',
                                `cost_center`='" . $admin['costCenter'] . "',
                                `purchasingValueKey`='" . $admin["purchasingValueKey"] . "',
                                `uomRel` = '" . $admin['uomRel'] . "',
                                `status`='" . $status . "',
                                `hsnCode` = '" . $admin['hsn'] . "',
                                `volumeCubeCm` = '" . $admin['volumeCubeCm'] . "',
                                `weight_unit`='" . $POST['net_unit'] . "',
                                `measuring_unit`='" . $POST['measure_unit'] . "',
                                `service_unit`='" . $admin["serviceUnit"] . "',
                                `service_group`='" . $admin["serviceGroup"] . "',
                                `asset_classes`='',
                                `dep_key`='" . $dep_key . "',
                                `createdBy`='" . $created_by . "',
                                `updatedBy`='" . $updated_by . "'";
            //exit();

            $insertItem = queryInsert($insgood);


            if ($insertItem["status"] == "success") {
                $itemId = $insertItem['insertedId'];


                ///---------------------------------Audit Log Start---------------------
                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_INVENTORY_ITEMS;
                $auditTrail['basicDetail']['column_name'] = 'itemId'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $itemId;  //     primary key
                $auditTrail['basicDetail']['document_number'] = $itemCode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = 'New Item added';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insgood);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';


                $auditTrail['action_data']['Basic Detail']['parentGlId'] = $gl;
                $auditTrail['action_data']['Basic Detail']['itemCode'] =  $itemCode;
                $auditTrail['action_data']['Basic Detail']['itemName'] = addslashes($name);
                $auditTrail['action_data']['Basic Detail']['itemDesc'] = addslashes($desc);
                $auditTrail['action_data']['Basic Detail']['netWeight'] = $admin["netWeight"];
                $auditTrail['action_data']['Basic Detail']['grossWeight'] = $admin["grossWeight"];
                $auditTrail['action_data']['Basic Detail']['volume'] =  $admin["volume"];
                $auditTrail['action_data']['Basic Detail']['height'] = $admin["height"];
                $auditTrail['action_data']['Basic Detail']['width'] = $admin["width"];
                $auditTrail['action_data']['Basic Detail']['length'] = $admin["length"];
                $auditTrail['action_data']['Basic Detail']['goodsType'] = $admin["goodsType"];
                $auditTrail['action_data']['Basic Detail']['goodsGroup'] = $admin["goodsGroup"];
                $auditTrail['action_data']['Basic Detail']['purchaseGroup'] = $admin["purchaseGroup"];
                $auditTrail['action_data']['Basic Detail']['availabilityCheck'] = $admin["availabilityCheck"];
                $auditTrail['action_data']['Basic Detail']['baseUnitMeasure'] = $admin["baseUnitMeasure"];
                $auditTrail['action_data']['Basic Detail']['issueUnitMeasure'] = $admin["issueUnitMeasure"];
                $auditTrail['action_data']['Basic Detail']['tds'] = $admin['tds'];
                $auditTrail['action_data']['Basic Detail']['cost_center'] = $admin['costCenter'];
                $auditTrail['action_data']['Basic Detail']['bomStatus'] = $bomRequired;
                $auditTrail['action_data']['Basic Detail']['purchasingValueKey'] = $admin["purchasingValueKey"];
                $auditTrail['action_data']['Basic Detail']['uomRel'] = $admin['uomRel'];
                $auditTrail['action_data']['Basic Detail']['status'] = $status;
                $auditTrail['action_data']['Basic Detail']['hsnCode'] = $admin['hsn'];
                $auditTrail['action_data']['Basic Detail']['volumeCubeCm'] = $admin['volumeCubeCm'];
                $auditTrail['action_data']['Basic Detail']['weight_unit'] = $POST['net_unit'];
                $auditTrail['action_data']['Basic Detail']['measuring_unit'] = $POST['measure_unit'];
                $auditTrail['action_data']['Basic Detail']['service_unit'] = $admin["serviceUnit"];
                $auditTrail['action_data']['Basic Detail']['service_group'] = $admin["serviceGroup"];
                $auditTrail['action_data']['Basic Detail']['asset_classes'] =  "";
                $auditTrail['action_data']['Basic Detail']['dep_key'] = $dep_key;


                $insert_storage = "INSERT INTO `" . ERP_INVENTORY_STORAGE . "` 
                                    SET
                                    `storageControl`='" . $admin["storageControl"] . "',
                                    `maxStoragePeriod`='" . $admin["maxStoragePeriod"] . "',
                                    `maxStoragePeriodTimeUnit`='" . $admin["maxStoragePeriodTimeUnit"] . "',
                                    `minRemainSelfLife`='" . $admin["minRemainSelfLife"] . "',
                                    `minRemainSelfLifeTimeUnit`='" . $admin["minRemainSelfLifeTimeUnit"] . "',
                                    `item_id`=  $itemId,
                                    `company_id`=$company_id,
                                    `branch_id`=$branch_id,
                                    `location_id`=$location_id,
                                    `created_by`='" . $created_by . "'";
                $insertStorage = queryInsert($insert_storage);


                $price = array();
                $price["price"] = $POST["price"] ?? 0;
                $price["discount"] = $POST["discount"] ?? 0;

                //echo    $POST["price"]??0;
                if ($goodType == "SERVICEP" || $goodType == "SERVICES") {

                    $insSummary = "INSERT INTO erp_inventory_stocks_summary  
                    SET 
                        `company_id`=$company_id,
                        `branch_id`=$branch_id,
                        `location_id`=$location_id,
                        `itemId`=$itemId,
                        `itemPrice`='" . $price["price"] . "',
                        `bomStatus` ='" .  $bomRequired . "',
                        `itemMaxDiscount`='" . $POST["discount"] . "', 
                        `movingWeightedPrice`= '" . $POST["rate"] . "',
                        `stock_date`='" . $POST["service_stock_date"] . "',
                        `createdBy`='" . $created_by . "', 
                        `updatedBy`='" . $updated_by . "'";



                    $auditTrail['action_data']['Summary']['itemPrice'] = $price["price"];
                    $auditTrail['action_data']['Summary']['itemMaxDiscount'] = $POST["discount"];
                    $auditTrail['action_data']['Summary']['movingWeightedPrice'] = $POST["rate"];
                    $auditTrail['action_data']['Summary']['stock_date'] = $POST["service_stock_date"];
                } else if ($goodType == "ASSET") {

                    $insSummary = "INSERT INTO erp_inventory_stocks_summary  
                    SET 
                        `company_id`=$company_id,
                        `branch_id`=$branch_id,
                        `location_id`=$location_id,
                        `itemId`=$itemId,
                        `itemPrice`='" . $price["price"] . "',  
                        `priceType`=  '" . $price_type . "',
                        `itemMaxDiscount`='" . $POST["discount"] . "', 
                        `itemTotalQty`= '" . $admin["opening_stock"] . "',
                        `movingWeightedPrice`= '" . $admin["rate"] . "',
                        `bomStatus` ='" .  $bomRequired . "',
                        `stock_date`='" . $admin["stock_date"] . "',
                        `createdBy`='" . $created_by . "', 
                        `updatedBy`='" . $updated_by . "'";


                    $auditTrail['action_data']['Summary']['itemPrice'] = $price["price"];
                    $auditTrail['action_data']['Summary']['priceType'] = $price_type;
                    $auditTrail['action_data']['Summary']['itemMaxDiscount'] = $POST["discount"];
                    $auditTrail['action_data']['Summary']['itemTotalQty'] = $admin["opening_stock"];
                    $auditTrail['action_data']['Summary']['movingWeightedPrice'] = $admin["rate"];
                    $auditTrail['action_data']['Summary']['stock_date'] = $admin["stock_date"];

                    // $insert_log = queryInsert("INSERT INTO `erp_inventory_stocks_log` SET
                    //  `companyId`=$company_id ,
                    //  `branchId`=$branch_id ,
                    //  `locationId`=$location_id ,
                    //  `storageLocationId`=1 ,
                    //  `itemId`=$itemId ,
                    //  `remainingQty`='" . $admin["opening_stock"] . "' ,
                    //  `itemQty`='" . $admin["opening_stock"] . "' ,
                    //  `itemUom`='" . $admin["baseUnitMeasure"] . "' ,
                    //  `itemPrice`='" . $admin["total"] . "' ,
                    //  `logRef`='item creation' ,
                    //  `createdBy`='" . $created_by . "' ,
                    //  `updatedBy`='" . $updated_by . "' ,
                    //  `min_stock` = $min_stock ,
                    //  `max_stock` = $max_stock ,
                    //  `status`=0 ");
                    // console($insert_log);
                    // exit();


                    $auditTrail['action_data']['Stock Log']['storageLocationId'] = 1;
                    $auditTrail['action_data']['Stock Log']['remainingQty'] = $admin["opening_stock"];
                    $auditTrail['action_data']['Stock Log']['itemQty'] =  $admin["opening_stock"];
                    $auditTrail['action_data']['Stock Log']['itemUom'] = $admin["baseUnitMeasure"];
                    $auditTrail['action_data']['Stock Log']['itemPrice'] = $admin["total"];
                    $auditTrail['action_data']['Stock Log']['logRef'] = 'item creation';
                    $auditTrail['action_data']['Stock Log']['min_stock'] = $min_stock;
                    $auditTrail['action_data']['Stock Log']['max_stock'] = $max_stock;
                } else {
                    $insSummary = "INSERT INTO erp_inventory_stocks_summary  
                        SET 
                            `company_id`=$company_id,
                            `branch_id`=$branch_id,
                            `location_id`=$location_id,
                            `itemId`=$itemId,
                            `itemPrice`='" . $price["price"] . "', 
                            `priceType`=  '" . $price_type . "',
                            `itemMaxDiscount`='" . $POST["discount"] . "', 
                            `movingWeightedPrice`= '" . $admin["rate"] . "',
                            `" . $stock_col . "` = '" . $admin["opening_stock"] . "',
                            `itemTotalQty`= '" . $admin["opening_stock"] . "',
                            `stock_date`='" . $admin["stock_date"] . "',
                            `bomStatus` ='" .  $bomRequired . "',
                            `min_stock` =  $min_stock,
                            `max_stock` = $max_stock,
                            `createdBy`='" . $created_by . "', 
                            `updatedBy`='" . $updated_by . "'";




                    $auditTrail['action_data']['Summary']['itemPrice'] = $price["price"];
                    $auditTrail['action_data']['Summary']['priceType'] = $price_type;
                    $auditTrail['action_data']['Summary']['itemMaxDiscount'] = $POST["discount"];
                    $auditTrail['action_data']['Summary']['movingWeightedPrice'] = $admin["rate"];
                    $auditTrail['action_data']['Summary'][$stock_col] =  $admin["opening_stock"];
                    $auditTrail['action_data']['Summary']['itemTotalQty'] = $admin["opening_stock"];
                    $auditTrail['action_data']['Summary']['stock_date'] = $admin["stock_date"];
                    $auditTrail['action_data']['Summary']['min_stock'] =  $min_stock;
                    $auditTrail['action_data']['Summary']['max_stock'] = $max_stock;

                    //exit();
                    // $insert_log = queryInsert("INSERT INTO `erp_inventory_stocks_log` SET 
                    //     `companyId`=$company_id,
                    //     `branchId`=$branch_id,
                    //     `locationId`=$location_id,
                    //     `storageLocationId`=1,
                    //     `storageType`='" . $stock_col . "',
                    //     `itemId`=$itemId,
                    //     `itemQty`='" . $admin["opening_stock"] . "',
                    //     `itemUom`='" . $admin["baseUnitMeasure"] . "',
                    //     `itemPrice`='" . $admin["total"] . "',
                    //     `logRef`='item creation',
                    //     `createdBy`='" . $created_by . "',
                    //     `updatedBy`='" . $updated_by . "',
                    //     `min_stock` = $min_stock,
                    //     `max_stock` = $max_stock,
                    //     `status`=0 ");


                    $auditTrail['action_data']['Stock Log']['storageType'] =  $stock_col;
                    $auditTrail['action_data']['Stock Log']['storageLocationId'] = 1;
                    $auditTrail['action_data']['Stock Log']['remainingQty'] = $admin["opening_stock"];
                    $auditTrail['action_data']['Stock Log']['itemQty'] =  $admin["opening_stock"];
                    $auditTrail['action_data']['Stock Log']['itemUom'] = $admin["baseUnitMeasure"];
                    $auditTrail['action_data']['Stock Log']['itemPrice'] = $admin["total"];
                    $auditTrail['action_data']['Stock Log']['logRef'] = 'item creation';
                    $auditTrail['action_data']['Stock Log']['min_stock'] = $min_stock;
                    $auditTrail['action_data']['Stock Log']['max_stock'] = $max_stock;
                }

                $insertSummary = queryInsert($insSummary);
                if ($insertSummary['status'] == "success") {
                    $itemSummaryId = $insertSummary['insertedId'];
                    // $data = [
                    //     "date" => date('Y-m-d'),
                    //     "gl" => $gl,
                    //     "subgl" => $itemCode,
                    //     "closing_qty" => $admin["opening_stock"],
                    //     "closing_val" => $admin["rate"]
                    // ];
                    // addOpeningBalanceForGlSubGl($data);
                    // $spec = $_POST['spec'];
                    // foreach ($spec as $spec) {
                    //     //  console($spec);

                    //     $spec_name = $spec['spec_name'];
                    //     $spec_desc = $spec['spec_detail'];
                    //     $insert_spec = queryInsert("INSERT INTO `erp_item_specification` SET 
                    //     `item_id`=$itemId,
                    //     `item_summary_id`= $itemSummaryId, 
                    //     `specification`='" . $spec_name . "',
                    //     `specification_detail`='" . $spec_desc . "',
                    //     `company_id`=$company_id,
                    //     `branch_id`=$branch_id,
                    //     `location_id`=$location_id,
                    //     `created_by`='" . $created_by . "' ");

                    //     $auditTrail['action_data']['Specification']['specification'] = $spec_name;
                    //     $auditTrail['action_data']['Specification']['specification_detail'] = $spec_desc;
                    // }
                    // exit();

                }



                $auditTrail['action_data']['Storage']['storageControl'] = $admin["storageControl"];
                $auditTrail['action_data']['Storage']['maxStoragePeriod'] = $admin["maxStoragePeriod"];
                $auditTrail['action_data']['Storage']['maxStoragePeriodTimeUnit'] = $admin["maxStoragePeriodTimeUnit"];
                $auditTrail['action_data']['Storage']['minRemainSelfLife'] = $admin["minRemainSelfLife"];
                $auditTrail['action_data']['Storage']['minRemainSelfLifeTimeUnit'] = $admin["minRemainSelfLifeTimeUnit"];


                $auditTrailreturn = generateAuditTrail($auditTrail);



                $returnData['status'] = "success";
                $returnData['name'] = $name;
                $returnData['message'] = "Item Creation Successful! New Item Code is -" . $itemCode;
                $returnData['insgood'] = $insgood;
                $returnData['auditTrailreturn'] = $auditTrailreturn;
                $flag[] = array("status"=>"success","message"=>"Item Creation Successful! at line ".$i);
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Item Creation Failed!";
                $returnData['insgood'] = $insgood;
                $flag[] = array("status"=>"warning","message"=>"Item Creation Failed! at line ".$i,"everything"=>$insertItem,"query"=>$insgood);
                $error_flag++;
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Setup Your Accounts first!";
            $flag[] = array("status"=>"warning","message"=>"Setup Your Accounts first! at line ".$i);
            $error_flag++;
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
                                    `migration_type`='item',
                                    `declaration`='$declaration_value',
                                    `created_by`='$created_by',
                                    `updated_by`='$created_by' 
                                    ";
                                    queryInsert($insvalidation);


        return $total_array;
    }
}
