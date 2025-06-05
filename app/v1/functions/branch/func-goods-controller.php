<?php
require_once "func-journal.php";
class GoodsController extends Accounting
{

    function createGoodTypes($INPUTS)
    {
        global $dbCon;
        $returnData = [];
        $isValidate = validate($INPUTS, [
            "goodTypeName" => "required",
            "goodTypeDesc" => "required",
            "type" => "required"
        ], [
            "goodTypeName" => "Enter good type name",
            "goodTypeDesc" => "Enter good type  desc"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }




        $companyId = $INPUTS["companyId"];
        $goodTypeName = $INPUTS["goodTypeName"];
        $goodTypeDesc = $INPUTS["goodTypeDesc"];
        $type = $INPUTS["type"];

        $goodTypeCreatedBy = 1;

        $createSql = "INSERT INTO `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` SET `companyId`='" . $companyId . "',`goodTypeName`='" . $goodTypeName . "',`goodTypeDesc`='" . $goodTypeDesc . "',`goodTypeCreatedBy`='" . $goodTypeCreatedBy . "',`goodTypeUpdatedBy`='" . $goodTypeCreatedBy . "' ,`type`='" . $type . "'";

        if (mysqli_query($dbCon, $createSql)) {
            $returnData["status"] = "success";
            $returnData["message"] = "Good type created success.";
        } else {
            $returnData["status"] = "warning";
            $returnData["message"] = "Good type created failed, try again!";
        }
        return $returnData;
    }

    function getAllGoodTypes()
    {
        global $dbCon;
        $returnData = [];
        $selectSql = "SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeStatus`='active'";

        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = mysqli_num_rows($res) . " records found.";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }


    function createGoodGroup($INPUTS)
    {
        global $dbCon;
        $returnData = [];
        global $company_id;
        global $created_by;
        $isValidate = validate($INPUTS, [
            "goodGroupName" => "required",
            "goodGroupDesc" => "required",
            "goodType_id" => "required"
        ], [
            "goodGroupName" => "Enter good group name",
            "goodGroupDesc" => "Enter good group desc",
            "goodType_id" => "Item Type Id missing"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        //  $companyId = $INPUTS["companyId"];
        $goodGroupName = $INPUTS["goodGroupName"];
        $goodGroupDesc = $INPUTS["goodGroupDesc"];
        $goodType_id = $INPUTS["goodType_id"];


        $sql = "INSERT INTO `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` SET `companyId`=$company_id,`goodGroupName`='" . $goodGroupName . "',`goodGroupDesc`='" . $goodGroupDesc . "',`goodGroupCreatedBy`='" . $created_by . "',`goodGroupUpdatedBy`='" . $created_by . "',`goodType`=$goodType_id";
        $returnData = queryInsert($sql);

        return $returnData;
    }

    function getAllGoodGroups($type = null)
    {
        global $dbCon;
        global $company_id;
        $returnData = [];
        $cond = '';
        if (!empty($type)) {
            $cond .= "AND `goodType`='" . $type . "'";
        }
        $selectSql = "SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE 1 " . $cond . " AND `companyId`=$company_id ";

        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = mysqli_num_rows($res) . " records found.";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }



    function getAllServiceGroup()
    {
        global $dbCon;
        global $company_id;
        $returnData = [];
        $selectSql = "SELECT * FROM `erp_service_groups` WHERE `serviceGroupStatus`='active' AND `companyId`=$company_id";

        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = mysqli_num_rows($res) . " records found.";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }

    function getAllPurchaseGroups()
    {
        global $dbCon;
        $returnData = [];
        global $company_id;
        $selectSql = "SELECT * FROM `" . ERP_INVENTORY_MASTR_PURCHASE_GROUPS . "` WHERE `companyId`=$company_id";

        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = mysqli_num_rows($res) . " records found.";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }


    function createPurchaseGroup($INPUTS)
    {
        global $dbCon;
        $returnData = [];
        $isValidate = validate($INPUTS, [
            "purchaseGroupName" => "required",
            "purchaseGroupDesc" => "required"
        ], [
            "purchaseGroupName" => "Enter purchase group name",
            "purchaseGroupDesc" => "Enter purchase group desc"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        $companyId = $INPUTS["companyId"];
        $purchaseGroupName = $INPUTS["purchaseGroupName"];
        $purchaseGroupDesc = $INPUTS["purchaseGroupDesc"];

        $purchaseGroupCreatedBy = 1;

        $createSql = "INSERT INTO `" . ERP_INVENTORY_MASTR_PURCHASE_GROUPS . "` SET `companyId`='" . $companyId . "',`purchaseGroupName`='" . $purchaseGroupName . "',`purchaseGroupDesc`='" . $purchaseGroupDesc . "',`purchaseGroupCreatedBy`='" . $purchaseGroupCreatedBy . "',`purchaseGroupUpdatedBy`='" . $purchaseGroupCreatedBy . "'";

        if (mysqli_query($dbCon, $createSql)) {
            $returnData["status"] = "success";
            $returnData["message"] = "Purchase group created successfully";
        } else {
            $returnData["status"] = "warning";
            $returnData["message"] = "Purchase group created failed, try again!";
        }
        return $returnData;
    }
    function createGoods($POST = [])
    {




        // console($POST);
        // exit();
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        $returnData = [];

        $myArray = $POST['goodsGroup'];

        $goodsGroup = null;

        for ($i = count($myArray) - 1; $i >= 0; $i--) {
            if ($myArray[$i] != null) {
                $goodsGroup = $myArray[$i];
                break;
            }
        }

        $discountGroupArray = json_encode($POST['discount_group']);

        // console(json_encode($discountGroupArray));



        // $discountGroup = null;

        // for ($i = count($discountGroupArray) - 1; $i >= 0; $i--) {
        //     if ($discountGroupArray[$i] != null) {
        //         $discountGroup = $discountGroupArray[$i];
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
                $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['itemsFG_gl']);
                $parentGlId = $paccdetails['data']['id'];
            } else if ($POST['goodsType'] == 1) {
                $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['itemsRM_gl']);
                $parentGlId = $paccdetails['data']['id'];
            } else if ($POST['goodsType'] == 2) {
                $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['itemsSFG_gl']);
                $parentGlId = $paccdetails['data']['id'];
            } else {
                $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['itemsRM_gl']);
                $parentGlId = $paccdetails['data']['id'];
            }
            $admin = array();
            //$admin["goodsType"] = $POST["goodsType"];
            $admin["goodsGroup"] = $goodsGroup;
            //    $admin['discountGroup'] = $discountGroup;
            $admin["purchaseGroup"] = $POST["purchaseGroup"] ?? 0;
            $admin["itemName"] = $POST["itemName"] ?? 0;
            $admin["netWeight"] = $POST["netWeight"] ?? 0;
            $admin["availabilityCheck"] = $POST["availabilityCheck"] ?? 0;
            $admin["grossWeight"] = $POST["grossWeight"] ?? 0;
            $admin["volume"] = $POST["volume"] ?? 0;
            $admin["height"] = $POST["height"] ?? 0;
            $admin["width"] = $POST["width"] ?? 0;
            $admin["length"] = $POST["length"] ?? 0;

            $admin["issueUnitMeasure"] = $POST["issueUnit"] ?? 0;
            $admin["itemDesc"] = $POST["itemDesc"] ?? 0;
            $admin["storageControl"] = $POST["storageControl"] ?? 0;
            $admin["maxStoragePeriod"] = $POST["maxStoragePeriod"] ?? 0;
            $admin["maxStoragePeriodTimeUnit"] = $POST["maxTime"] ?? 0;
            $admin["minRemainSelfLife"] = $POST["minRemainSelfLife"] ?? 0;
            $admin["minRemainSelfLifeTimeUnit"] = $POST["minTime"] ?? 0;
            $admin["purchasingValueKey"] = $POST["purchasingValueKey"] ?? 0;
            $admin['uomRel'] = $POST["rel"] > 0 ? $POST["rel"] : 0;
            $admin['volumeCubeCm'] = $POST["volumeCubeCm"] > 0 ? $POST["volumeCubeCm"] : 0;
            $admin['hsn'] = $POST['hsn'];
            $admin['weight_unit'] = $POST['grossWeight'] ?? 0;
            $admin['measure_unit'] = $POST['measure_unit'] ?? 0;
            $admin["serviceName"] = $POST["serviceName"] ?? 0;
            $admin["serviceDesc"] = $POST["serviceDesc"] ?? 0;
            $admin["serviceUnit"] = $POST["serviceUnit"] ?? 0;
            $admin["glCode"] = $POST["glCode"] ?? 0;
            $admin["serviceGroup"] = $POST["serviceGroup"] ?? 0;
            $admin["stock_date"] = $POST["stock_date"] ?? 0;
            $default_storage = $POST['default_storage'] ?? 0;
            $qa_storage = $POST['qa_storage'] ?? 0;
            if (isset($POST['qaEnable']) && $POST['qaEnable'] != '') {
                $qaEnable = $POST['qaEnable'];
            } else {
                $qaEnable = 0;
            }
            // echo $qaEnable;
            // exit();
            $costCenter = '-';
            if (isset($POST["costCenter"]) && $POST["costCenter"] != '') {
                $admin["costCenter"] = $POST["costCenter"] ?? 0;

                if ($admin['costCenter'] != 0) {

                    $costCenter = queryGet("SELECT CostCenter_code FROM `" . ERP_COST_CENTER . "` WHERE CostCenter_status = 'active' AND branch_id = $branch_id AND company_id = $company_id AND CostCenter_id = " . $admin["costCenter"])['data']['CostCenter_code'];
                }
            } else {
                $admin["costCenter"] = 0;
            }
            $purchaseGroup = '-';
            if (isset($POST["purchaseGroup"]) && $POST["purchaseGroup"] != '') {
                $admin["purchaseGroup"] = $POST["purchaseGroup"] ?? 0;
                if ($admin['purchaseGroup'] != 0) {
                    $purchaseGroup = queryGet("SELECT purchaseGroupName FROM `" . ERP_INVENTORY_MASTR_PURCHASE_GROUPS . "` WHERE companyId = $company_id AND purchaseGroupId = " . $admin["purchaseGroup"])['data']['purchaseGroupName'];;
                }
            } else {
                $admin["purchaseGroup"] = 0;
            }


            $issueUnitMeasure = '-';

            if (isset($POST["issueUnit"]) && $POST["issueUnit"] != '') {
                $admin["issueUnitMeasure"] = $POST["issueUnit"] ?? 0;
                if ($admin['issueUnitMeasure'] != 0) {
                    $issueUnitMeasure = getUomDetail($admin['issueUnitMeasure'])['data']['uomName'];
                }
            } else {
                $admin["issueUnitMeasure"] = 0;
            }

            $baseUnitMeasure = '-';
            if (isset($POST["baseUnitMeasure"]) && $POST["baseUnitMeasure"] != '') {
                $admin["baseUnitMeasure"] = $POST["baseUnitMeasure"] ?? 0;
                if ($admin['baseUnitMeasure'] != 0) {
                    $baseUnitMeasure = getUomDetail($admin['baseUnitMeasure'])['data']['uomName'];
                }
            } else {
                $admin["baseUnitMeasure"] = 0;
            }


            $serviceUnit = '-';
            if (isset($POST["serviceUnit"]) && $POST["serviceUnit"] != '') {
                $admin["serviceUnit"] = $POST["serviceUnit"] ?? 0;
                if ($admin['serviceUnit'] != 0) {
                    $serviceUnit = getUomDetail($admin['serviceUnit'])['data']['uomName'];
                }
            } else {
                $admin["serviceUnit"] = 0;
            }
            //$admin["costCenter"] = $POST["costCenter"] ?? 0;

            if (isset($POST["tds"]) && $POST["tds"] != '') {
                $admin["tds"] = $POST["tds"] ?? 0;
            } else {
                $admin["tds"] = 0;
            }

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

            $status = $POST["creategoodsdata"] == "add_draft" ? "draft" : "active";

            $goodTypeId = $POST["goodsType"];
            //  exit();








            $checkFg = "SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE  `goodTypeId` = " . $POST['goodsType'] . "";
            $resultType = queryGet($checkFg);
            $row = $resultType["data"];
            $goodType = $row['type'];
            // echo $goodType;
            //    exit();

            if ($goodType == "FG") {
                $admin["baseUnitMeasure"] = $POST["baseUnitMeasure"] ?? 0;
                $bomRequired = $POST['bomRequired_radio'];
                $stock_col = "fgWhOpen";
                if ($bomRequired == 1) {
                    $price_type = "S";
                    $admin["goodsType"] = $POST["goodsType"];
                    $goodTypeId = '3';
                } elseif ($bomRequired == 0) {
                    $admin["goodsType"] = 4;
                    $price_type = "V";
                    $goodTypeId = '4';
                } else {
                    $price_type = "";
                }


                $name = $admin["itemName"];
                $desc = $admin["itemDesc"];
                $gl = $parentGlId;
                //  $goodTypeId = '3,4';
            } elseif ($goodType == "SFG") {
                $admin["baseUnitMeasure"] = $POST["baseUnitMeasure"] ?? 0;
                $admin["goodsType"] = $POST["goodsType"];
                $bomRequired = 1;
                $price_type = "S";
                $stock_col = "sfgStockOpen";
                $goodTypeId = 2;

                $name = $admin["itemName"];
                $desc = $admin["itemDesc"];
                $gl = $parentGlId;
            } elseif ($goodType == "RM") {
                $admin["baseUnitMeasure"] = $POST["baseUnitMeasure"] ?? 0;
                $admin["goodsType"] = $POST["goodsType"];
                $bomRequired = 0;
                $price_type = "V";
                $stock_col = "rmWhOpen";

                $goodTypeId = 1;
                $name = $admin["itemName"];
                $desc = $admin["itemDesc"];
                $gl = $parentGlId;
            } elseif ($goodType == "ASSET") {
                $admin["baseUnitMeasure"] = $POST["baseUnitMeasure"] ?? 0;
                $admin["goodsType"] = $POST["goodsType"];
                $bomRequired = 0;
                $price_type = "V";
                $stock_col = "rmWhOpen";

                $goodTypeId = 9;
                $name = $admin["itemName"];
                $desc = $admin["itemDesc"];
                $gl =  $POST["glCodeAsset"];
            } elseif ($goodType == "SERVICES") {
                $admin["baseUnitMeasure"] = $POST["serviceUnit"] ?? 0;
                $name = $POST['serviceName'];
                $desc = $admin['serviceDesc'];
                $gl =  $admin["glCode"];


                //$admin["goodsType"] = $POST["goodsType"];
                $bomRequired =  $POST['boqRequired'] ? 1 : 0;

                $stock_col = " ";

                if ($bomRequired == 1) {
                    $price_type = "S";
                    $admin["goodsType"] = 10;
                    $goodTypeId = 10;

                    $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['billable_project_gl']);
                    $parentGlId = $paccdetails['data']['id'];
                } elseif ($bomRequired == 0) {
                    $goodTypeId = 5;
                    $admin["goodsType"] = 5;
                    $price_type = "V";
                } else {
                    $price_type = "";
                }


                $price_type = "S";
            } elseif ($goodType == "SERVICEP") {
                $admin["baseUnitMeasure"] = $POST["serviceUnit"] ?? 0;
                $name = $POST['serviceName'];
                $desc = $admin['serviceDesc'];
                $gl =  $admin["glCode"];
                $goodTypeId  = 7;
                $admin["goodsType"] = $POST["goodsType"];
                $bomRequired = 0;
                $price_type = "";
                $stock_col = "";
            }

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





            $duplicate = queryGet("SELECT * FROM `erp_inventory_items` WHERE `itemName` = '" .  $name . "' AND `company_id`=$company_id", true);
            //  console($duplicate);
            //  exit();

            if ($duplicate['numRows'] > 0) {

                $returnData['status'] = "warning";
                $returnData['message'] = "Duplicate Item Name";
                return $returnData;
            }



            if (isset($_POST['asset_classification'])) {
                $filtered_array = array_filter($_POST['asset_classification']);
                $asset_class_list =  implode(",", $filtered_array);
            }
            $dep_key = $_POST['dep_key'];



            $lastlQuery = "SELECT itemCode FROM `" . ERP_INVENTORY_ITEMS . "` WHERE  `itemCode` REGEXP '^[0-9]+$' AND `company_id`=$company_id AND `goodsType` IN($goodTypeId)  ORDER BY `itemId` DESC LIMIT 1";
            $resultLast = queryGet($lastlQuery);

            $rowLast = $resultLast["data"];

            $goodType;

            $lastsl = $rowLast['itemCode'];

            if ($goodTypeId == 4) {
                $goodCodetype = $goodType . "T";
            } else {
                $goodCodetype = $goodType;
            }


            $itemCode =  getItemSerialNumber($lastsl, $goodCodetype);

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
                                `goodsType`='" .  $goodTypeId . "',
                                `goodsGroup`='" . $admin["goodsGroup"] . "', 
                                `discountGroup` = '" . $discountGroupArray . "',
                                `purchaseGroup`='" . $admin["purchaseGroup"] . "',
                                `branch`='" . $branch_id . "',
                                `location_id`='" . $location_id . "',
                                `company_id`='" . $company_id . "',
                                `availabilityCheck`='" . $admin["availabilityCheck"] . "',
                                `baseUnitMeasure`='" . $admin["baseUnitMeasure"] . "',
                                `issueUnitMeasure`='" . $admin["issueUnitMeasure"] . "',
                                `tds`='" . $admin['tds'] . "',
                                `cost_center`='" . $admin['costCenter'] . "',
                                `isBomRequired`= $bomRequired,
                                `purchasingValueKey`='" . $admin["purchasingValueKey"] . "',
                                `uomRel` = '" . $admin['uomRel'] . "',
                                `status`='" . $status . "',
                                `hsnCode` = '" . $admin['hsn'] . "',
                                `volumeCubeCm` = '" . $admin['volumeCubeCm'] . "',
                                `weight_unit`='" . $POST['net_unit'] . "',
                                `measuring_unit`='" . $POST['measure_unit'] . "',
                                `service_unit`='" . $admin["serviceUnit"] . "',
                                `service_group`='" . $admin["serviceGroup"] . "',
                                `asset_classes`='" .  $asset_class_list . "',
                                `dep_key`='" . $dep_key . "',
                                `createdBy`='" . $created_by . "',
                                `updatedBy`='" . $updated_by . "'";
            // exit();

            $insertItem = queryInsert($insgood);

            if ($insertItem["status"] == "success") {
                $itemId = $insertItem['insertedId'];





                ///---------------------------------Audit Log Start---------------------
                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $goods_id = $admin["goodsType"];
                $goodsType_sql = queryGet("SELECT * FROM `erp_inventory_mstr_good_types` WHERE `goodTypeId`=$goods_id")['data'];
                $goodsGroup = $admin["goodsGroup"];
                $goodsGroup_sql = queryGet("SELECT * FROM `erp_inventory_mstr_good_groups` WHERE `goodGroupId`=$goodsGroup")['data'];
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_INVENTORY_ITEMS;
                $auditTrail['basicDetail']['column_name'] = 'itemId'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $itemId;  //     primary key
                $auditTrail['basicDetail']['document_number'] = $itemCode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['party_id'] = 0;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = 'New Item added';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insgood);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';


                // $auditTrail['action_data']['Basic Detail']['parentGlId'] = $gl;
                $auditTrail['action_data']['Basic Detail']['Item_Code'] =  $itemCode;
                $auditTrail['action_data']['Basic Detail']['Item_Name'] = addslashes($name);
                $auditTrail['action_data']['Basic Detail']['Item_Desc'] = addslashes($desc);
                $auditTrail['action_data']['Basic Detail']['Net_Weight'] = decimalQuantityPreview($admin["netWeight"]);
                $auditTrail['action_data']['Basic Detail']['Gross_Weight'] = decimalQuantityPreview($admin["grossWeight"]);
                $auditTrail['action_data']['Basic Detail']['Volume'] =  decimalQuantityPreview($admin["volume"]);
                $auditTrail['action_data']['Basic Detail']['Height'] = decimalQuantityPreview($admin["height"]);
                $auditTrail['action_data']['Basic Detail']['Width'] = decimalQuantityPreview($admin["width"]);
                $auditTrail['action_data']['Basic Detail']['Length'] = decimalQuantityPreview($admin["length"]);
                $auditTrail['action_data']['Basic Detail']['Goods_Type'] = $goodsType_sql['goodTypeName'];
                $auditTrail['action_data']['Basic Detail']['Goods_Group'] = $goodsGroup_sql['goodGroupName'];
                $auditTrail['action_data']['Basic Detail']['Purchase_Group'] = $purchaseGroup;
                $auditTrail['action_data']['Basic Detail']['Availability_Check'] = ($admin["availabilityCheck"] == 0) ? '-' : $admin["availabilityCheck"];
                $auditTrail['action_data']['Basic Detail']['Base Unit Measure'] = $baseUnitMeasure;
                $auditTrail['action_data']['Basic Detail']['Issue Unit Measure'] = $issueUnitMeasure;
                $auditTrail['action_data']['Basic Detail']['Tds'] = decimalValuePreview($admin['tds']);
                $auditTrail['action_data']['Basic Detail']['Cost_Center'] = $costCenter;
                $auditTrail['action_data']['Basic Detail']['Bom_Status'] = ($bomRequired == 1) ? 'Required' : 'Not required';
                $auditTrail['action_data']['Basic Detail']['Purchasing Value Key'] = $admin["purchasingValueKey"];
                $auditTrail['action_data']['Basic Detail']['Uom_Rel'] = $admin['uomRel'];
                $auditTrail['action_data']['Basic Detail']['Status'] = $status;
                $auditTrail['action_data']['Basic Detail']['Hsn_Code'] = $admin['hsn'];
                $auditTrail['action_data']['Basic Detail']['Volume Cube Cm'] = $admin['volumeCubeCm'];
                $auditTrail['action_data']['Basic Detail']['Weight_Unit'] = $POST['net_unit'];
                $auditTrail['action_data']['Basic Detail']['Measuring_Unit'] = $POST['measure_unit'];
                $auditTrail['action_data']['Basic Detail']['Service_Unit'] = $serviceUnit;
                // $auditTrail['action_data']['Basic Detail']['Service_Group'] = $admin["serviceGroup"];
                $auditTrail['action_data']['Basic Detail']['Asset_Classes'] =  $asset_class_list;
                $auditTrail['action_data']['Basic Detail']['Dep_Key'] = $dep_key;



                //insert storage


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

                $price["discount"] = $POST["discount"] ?? 0;

                //echo    $POST["price"]??0;
                if ($goodType == "SERVICES") {

                    //   echo 1;
                    $price["price"] = $POST["service_target_price"] ?? 0;
                    $insSummary = "INSERT INTO erp_inventory_stocks_summary  
                    SET 
                        `company_id`=$company_id,
                        `branch_id`=$branch_id,
                        `location_id`=$location_id,
                        `itemId`=$itemId,
                        `itemPrice`='" . $price["price"] . "',
                         `priceType`=  '" . $price_type . "',
                        `itemMaxDiscount`='" . $POST["discount"] . "', 
                        `discountGroup` = '" . $discountGroupArray . "',
                        `bomStatus` ='" .  $bomRequired . "',
                        `createdBy`='" . $created_by . "', 
                        `updatedBy`='" . $updated_by . "'";



                    $auditTrail['action_data']['Summary']['Item_Price'] = decimalValuePreview($price["price"]);
                    $auditTrail['action_data']['Summary']['Item Max Discount'] = decimalValuePreview($POST["discount"]);
                    // $auditTrail['action_data']['Summary']['movingWeightedPrice'] = $POST["service_rate"];
                    //  $auditTrail['action_data']['Summary']['stock_date'] = $POST["service_stock_date"];
                } else if ($goodType == "SERVICEP") {
                    $price["price"] = $POST["service_target_price"] ?? 0;
                    $insSummary = "INSERT INTO erp_inventory_stocks_summary  
                    SET 
                        `company_id`=$company_id,
                        `branch_id`=$branch_id,
                        `location_id`=$location_id,
                        `itemId`=$itemId,
                        `itemPrice`=0,
                        `itemMaxDiscount`='" . $POST["discount"] . "', 
                        `bomStatus` ='" .  $bomRequired . "',
                        `createdBy`='" . $created_by . "', 
                        `updatedBy`='" . $updated_by . "'";



                    $auditTrail['action_data']['Summary']['Item_Price'] = decimalValuePreview($price["price"]);
                    $auditTrail['action_data']['Summary']['Item Max Discount'] = decimalValuePreview($POST["discount"]);
                    // $auditTrail['action_data']['Summary']['movingWeightedPrice'] = $POST["service_rate"];
                    //  $auditTrail['action_data']['Summary']['stock_date'] = $POST["service_stock_date"];
                } else if ($goodType == "ASSET") {
                    //  echo 2;
                    $price["price"] = $POST["price"] ?? 0;
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
                        `stock_date`='" . $admin["stock_date"] . "',
                        `bomStatus` ='" .  $bomRequired . "',
                        `min_stock` =  $min_stock,
                        `discountGroup` = '" . $discountGroupArray . "',
                        `max_stock` = $max_stock,
                        `createdBy`='" . $created_by . "', 
                        `updatedBy`='" . $updated_by . "'";


                    $auditTrail['action_data']['Summary']['Item_Price'] = decimalValuePreview($price["price"]);
                    $auditTrail['action_data']['Summary']['Price_Type'] = $price_type;
                    $auditTrail['action_data']['Summary']['Item Max Discount'] = decimalValuePreview($POST["discount"]);
                    $auditTrail['action_data']['Summary']['Item Total Qty'] = decimalQuantityPreview($admin["opening_stock"]);
                    $auditTrail['action_data']['Summary']['Moving Weighted Price'] = decimalValuePreview($admin["rate"]);
                    $auditTrail['action_data']['Summary']['Stock_Date'] = formatDateWeb($admin["stock_date"]);

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


                    // $auditTrail['action_data']['Stock Log']['StorageLocationId'] = 1;
                    $auditTrail['action_data']['Stock Log']['Remaining_Qty'] = decimalQuantityPreview($admin["opening_stock"]);
                    $auditTrail['action_data']['Stock Log']['Item_Qty'] =  decimalQuantityPreview($admin["opening_stock"]);
                    $auditTrail['action_data']['Stock Log']['Item_Uom'] = $baseUnitMeasure;
                    $auditTrail['action_data']['Stock Log']['Item_Price'] = decimalValuePreview($admin["total"]);
                    $auditTrail['action_data']['Stock Log']['Log_Ref'] = 'item creation';
                    $auditTrail['action_data']['Stock Log']['Min_Stock'] = decimalQuantityPreview($min_stock);
                    $auditTrail['action_data']['Stock Log']['Max_Stock'] = decimalQuantityPreview($max_stock);
                } else {
                    $price["price"] = $POST["price"] ?? 0;
                    // echo 3;
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
                            `default_storage_location`=$default_storage,
                            `discountGroup` = '" . $discountGroupArray . "',
                            `quality_enabled`='" . $qaEnable . "',
                            `qa_storage_location` = '" . $qa_storage . "',
                            `createdBy`='" . $created_by . "', 
                            `updatedBy`='" . $updated_by . "'";




                    $auditTrail['action_data']['Summary']['Item_Price'] = decimalValuePreview($price["price"]);
                    $auditTrail['action_data']['Summary']['Price+_Type'] = $price_type;
                    $auditTrail['action_data']['Summary']['Item Max Discount'] = decimalValuePreview($POST["discount"]);
                    $auditTrail['action_data']['Summary']['Moving Weighted Price'] = decimalValuePreview($admin["rate"]);
                    $auditTrail['action_data']['Summary'][$stock_col] =  decimalQuantityPreview($admin["opening_stock"]);
                    $auditTrail['action_data']['Summary']['Item Total Qty'] = decimalQuantityPreview($admin["opening_stock"]);
                    $auditTrail['action_data']['Summary']['Stock_Date'] = formatDateWeb($admin["stock_date"]);
                    $auditTrail['action_data']['Summary']['Min_Stock'] =  decimalQuantityPreview($min_stock);
                    $auditTrail['action_data']['Summary']['Max_Stock'] = decimalQuantityPreview($max_stock);

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
                    //     `logRef`='item creation'
                    //     `createdBy`='" . $created_by . "',
                    //     `updatedBy`='" . $updated_by . "',
                    //     `min_stock` = $min_stock,
                    //     `max_stock` = $max_stock,
                    //     `status`=0 ");


                    $auditTrail['action_data']['Stock Log']['Storage_Type'] =  $stock_col;
                    // $auditTrail['action_data']['Stock Log']['storageLocationId'] = 1;
                    $auditTrail['action_data']['Stock Log']['Remaining_Qty'] = decimalQuantityPreview($admin["opening_stock"]);
                    $auditTrail['action_data']['Stock Log']['Item_Qty'] =  decimalQuantityPreview($admin["opening_stock"]);
                    $auditTrail['action_data']['Stock Log']['Item_Uom'] = $baseUnitMeasure;
                    $auditTrail['action_data']['Stock Log']['Item_Price'] = decimalValuePreview($admin["total"]);
                    $auditTrail['action_data']['Stock Log']['Log_Ref'] = 'item creation';
                    $auditTrail['action_data']['Stock Log']['Min_Stock'] = decimalQuantityPreview($min_stock);
                    $auditTrail['action_data']['Stock Log']['Max_Stock'] = decimalQuantityPreview($max_stock);
                }

                //   exit();

                $insertSummary = queryInsert($insSummary);
                //  console($insertSummary);
                //  exit();
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



                    // insert images
                    // console($POST);




                    $files = $POST['pic'];
                    // console($files);
                    foreach ($files["name"] as $key => $name) {

                        $name = $files["name"][$key];
                        $tmpName = $files["tmp_name"][$key];
                        $size = $files["size"][$key];

                        $allowed_types = ['jpg', 'png', 'jpeg'];
                        $maxsize = 2 * 1024 * 1024; // 10 MB


                        $fileUploaded = uploadFile(["name" => $name, "tmp_name" => $tmpName, "size" => $size], COMP_STORAGE_DIR . "/others/", $allowed_types, $maxsize, 0);
                        //  console($fileUploaded);
                        $image_name = $fileUploaded['data'];
                        // exit();



                        if ($fileUploaded['status'] == 'success') {

                            $insert_img = queryInsert("INSERT INTO `erp_inventory_item_images` SET 
                    `item_id`=$itemId,
                    `item_summary_id`= $itemSummaryId, 
                    `image_name`='" . $image_name . "',
                    `company_id`=$company_id,
                    `branch_id`=$branch_id,
                    `location_id`=$location_id,
                    `created_by`='" . $created_by . "',
                    `updated_by` ='" . $created_by . "' ");
                            //console($insert_spec);
                        }
                    }

                    //doc insert


                    $doc = $POST['doc'];
                    // console($doc);

                    foreach ($doc["name"] as $key => $name) {

                        $name = $doc["name"][$key];
                        $tmpName = $doc["tmp_name"][$key];
                        $size = $doc["size"][$key];

                        $allowed_types = ['pdf'];
                        $maxsize = 2 * 1024 * 1024; // 10 MB


                        $docUploaded = uploadFile(["name" => $name, "tmp_name" => $tmpName, "size" => $size], COMP_STORAGE_DIR . "/others/", $allowed_types, $maxsize, 0);
                        //   console($docUploaded);



                        $doc_name = $docUploaded['data'];

                        if ($docUploaded['status'] == 'success') {

                            $insert_doc = queryInsert("INSERT INTO `erp_inventory_item_doc` SET 
                    `item_id`=$itemId,
                    `item_summary_id`= $itemSummaryId, 
                    `doc_name`='" . $doc_name . "',
                    `company_id`=$company_id,
                    `branch_id`=$branch_id,
                    `location_id`=$location_id,
                    `created_by`='" . $created_by . "',
                    `updated_by` ='" . $created_by . "' ");
                            //  console($insert_doc);  

                        }
                    }


                    //   exit();

                    //specifications insert
                    $spec = $_POST['spec'];
                    foreach ($spec as $spec) {
                        //  console($spec);

                        $spec_name = $spec['spec_name'] ?? '';
                        $spec_desc = $spec['spec_detail'] ?? '';
                        $insert_spec = queryInsert("INSERT INTO `erp_item_specification` SET 
                        `item_id`=$itemId,
                        `item_summary_id`= $itemSummaryId, 
                        `specification`='" . $spec_name . "',
                        `specification_detail`='" . $spec_desc . "',
                        `company_id`=$company_id,
                        `branch_id`=$branch_id,
                        `location_id`=$location_id,
                        `created_by`='" . $created_by . "' ");

                        $auditTrail['action_data']['Specification']['specification'] = $spec_name;
                        $auditTrail['action_data']['Specification']['specification_detail'] = $spec_desc;
                    }
                    // exit();

                }



                $auditTrail['action_data']['Storage']['Storage_Control'] = $admin["storageControl"];
                $auditTrail['action_data']['Storage']['Max Storage Period'] = $admin["maxStoragePeriod"];
                $auditTrail['action_data']['Storage']['Max Storage Period Time Unit'] = $admin["maxStoragePeriodTimeUnit"];
                $auditTrail['action_data']['Storage']['Min Remain Self Life'] = $admin["minRemainSelfLife"];
                $auditTrail['action_data']['Storage']['Min Remain Self Life Time Unit'] = $admin["minRemainSelfLifeTimeUnit"];


                $auditTrailreturn = generateAuditTrail($auditTrail);



                $returnData['status'] = "success";
                $returnData['name'] = $name;
                $returnData['message'] = "Item Creation Successful! New Item Code is -" . $itemCode;
                $returnData['insgood'] = $insgood;
                $returnData['auditTrailreturn'] = $auditTrailreturn;
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Item Creation Failed!";
                $returnData['insgood'] = $insgood;
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Setup Your Accounts first!";
        }






        return $returnData;
    }
    function createGoodsLocation($POST)
    {
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        $returnData = [];
        $itemId = $POST['item_id'];

        $invItems = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `itemId`=$itemId AND `company_id`=$company_id  ";
        $resItems = queryGet($invItems)['data'];
        $bom_req = $resItems['isBomRequired'] ?? 0;

        $costCenter = '-';
        if ($resItems["costCenter"] && $resItems["costCenter"] != '') {
            if ($resItems["costCenter"] != 0) {
                $costCenter = queryGet("SELECT CostCenter_code FROM `" . ERP_COST_CENTER . "` WHERE CostCenter_status = 'active' AND branch_id = $branch_id AND company_id = $company_id AND CostCenter_id = " . $resItems["costCenter"])['data']['CostCenter_code'];
            }
        }


        $purchaseGroup = '-';
        if ($resItems["purchaseGroup"] && $resItems["purchaseGroup"] != '') {
            if ($resItems["purchaseGroup"] != 0) {
                $purchaseGroup = queryGet("SELECT purchaseGroupName FROM `" . ERP_INVENTORY_MASTR_PURCHASE_GROUPS . "` WHERE companyId = $company_id AND purchaseGroupId = " . $resItems["purchaseGroup"])['data']['purchaseGroupName'];;
            }
        }

        $issueUnitMeasure = '-';
        if ($resItems["issueUnitMeasure"] && $resItems["issueUnitMeasure"] != '') {
            if ($resItems["issueUnitMeasure"] != 0) {
                $issueUnitMeasure = getUomDetail($resItems['issueUnitMeasure'])['data']['uomName'];
            }
        }


        $baseUnitMeasure = '-';
        if ($resItems["baseUnitMeasure"] && $resItems["baseUnitMeasure"] != '') {
            if ($resItems["baseUnitMeasure"] != 0) {
                $baseUnitMeasure = getUomDetail($resItems['baseUnitMeasure'])['data']['uomName'];
            }
        }


        $serviceUnit = '-';
        if ($resItems["serviceUnit"] && $resItems["serviceUnit"] != '') {
            if ($resItems["serviceUnit"] != 0) {
                $serviceUnit = getUomDetail($resItems['serviceUnit'])['data']['uomName'];
            }
        }



        $admin["storageControl"] = $POST["storageControl"] ?? 0;
        $admin["maxStoragePeriod"] = $POST["maxStoragePeriod"] ?? 0;
        $admin["maxStoragePeriodTimeUnit"] = $POST["maxTime"] ?? 0;
        $admin["minRemainSelfLife"] = $POST["minRemainSelfLife"] ?? 0;
        $admin["minRemainSelfLifeTimeUnit"] = $POST["minTime"] ?? 0;

        //   $select_summary = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
        //  console($select_summary);
        //   $bom_req = $select_summary['data']['bomStatus'] ?? 0;
        //exit();



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
        // exit();


        $insertStorage = queryInsert($insert_storage);
        if ($insertStorage["status"] == "success") {

            $price = array();
            $price["price"] = $POST["price"];
            $price["discount"] = $POST["discount"];

            $insSummary = "INSERT INTO erp_inventory_stocks_summary 
                    SET 
                        `company_id`=$company_id,
                        `branch_id`=$branch_id,
                        `location_id`=$location_id,
                        `itemId`=$itemId,
                        `itemPrice`='" . $price["price"] . "',
                        `priceType`= 'target',
                        `bomStatus` = '" . $bom_req . "',
                        `itemMaxDiscount`='" . $POST["discount"] . "',
                        `createdBy`='" . $created_by . "',
                        `updatedBy`='" . $updated_by . "'";
            // exit();

            $ins = queryInsert($insSummary);



            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $goods_id = $resItems["goodsType"];
            $goodsType_sql = queryGet("SELECT * FROM `erp_inventory_mstr_good_types` WHERE `goodTypeId`=$goods_id")['data'];
            $goodsGroup = $resItems["goodsGroup"];
            $goodsGroup_sql = queryGet("SELECT * FROM `erp_inventory_mstr_good_groups` WHERE `goodGroupId`=$goodsGroup")['data'];
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_INVENTORY_ITEMS;
            $auditTrail['basicDetail']['column_name'] = 'itemId'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $itemId;  //     primary key
            $auditTrail['basicDetail']['document_number'] = $resItems['itemCode'];
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_code'] = 0;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'New Item added';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insSummary);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';


            $auditTrail['action_data']['Basic Detail']['Item_Code'] =  $resItems['itemCode'];
            $auditTrail['action_data']['Basic Detail']['Item_Name'] = $resItems['itemName'];
            $auditTrail['action_data']['Basic Detail']['Item_Desc'] = $resItems['itemDesc'];
            $auditTrail['action_data']['Basic Detail']['Net_Weight'] = decimalQuantityPreview($resItems["netWeight"]);
            $auditTrail['action_data']['Basic Detail']['Gross_Weight'] = decimalValuePreview($resItems["grossWeight"]);
            $auditTrail['action_data']['Basic Detail']['Volume'] =  decimalQuantityPreview($resItems["volume"]);
            $auditTrail['action_data']['Basic Detail']['Height'] = decimalQuantityPreview($resItems["height"]);
            $auditTrail['action_data']['Basic Detail']['Width'] = decimalQuantityPreview($resItems["width"]);
            $auditTrail['action_data']['Basic Detail']['Length'] = decimalQuantityPreview($resItems["length"]);
            $auditTrail['action_data']['Basic Detail']['Goods_Type'] = $goodsType_sql['goodTypeName'];
            $auditTrail['action_data']['Basic Detail']['Goods_Group'] = $goodsGroup_sql['goodGroupName'];
            $auditTrail['action_data']['Basic Detail']['Purchase_Group'] = $purchaseGroup;
            $auditTrail['action_data']['Basic Detail']['Availability_Check'] = ($resItems["availabilityCheck"] == 0) ? '-' : $resItems["availabilityCheck"];;
            $auditTrail['action_data']['Basic Detail']['Base Unit Measure'] = $baseUnitMeasure;
            $auditTrail['action_data']['Basic Detail']['Issue Unit Measure'] = $issueUnitMeasure;
            $auditTrail['action_data']['Basic Detail']['Tds'] = decimalValuePreview($resItems['tds']);
            $auditTrail['action_data']['Basic Detail']['Cost_Center'] = $costCenter;
            $auditTrail['action_data']['Basic Detail']['Bom_Status'] = ($bom_req == 1) ? 'Required' : 'Not required';
            $auditTrail['action_data']['Basic Detail']['Purchasing Value Key'] = $resItems["purchasingValueKey"];
            $auditTrail['action_data']['Basic Detail']['Uom_Rel'] = $resItems['uomRel'];
            $auditTrail['action_data']['Basic Detail']['Status'] = $resItems['status'];
            $auditTrail['action_data']['Basic Detail']['Hsn_Code'] = $resItems['hsn'];
            $auditTrail['action_data']['Basic Detail']['Volume Cube Cm'] = $resItems['volumeCubeCm'];
            $auditTrail['action_data']['Basic Detail']['Weight_Unit'] = $POST['net_unit'];
            $auditTrail['action_data']['Basic Detail']['Measuring_Unit'] = $POST['measure_unit'];
            $auditTrail['action_data']['Basic Detail']['Service_Unit'] = $serviceUnit;
            $auditTrail['action_data']['Basic Detail']['Service_Group'] = $resItems["serviceGroup"];



            $invItemsStorage = "SELECT * FROM `" . ERP_INVENTORY_STORAGE . "` WHERE `itemId`=$itemId AND `company_id`=$company_id  ";

            $auditTrail['action_data']['Storage']['Storage_Control'] = $invItemsStorage["storageControl"];
            $auditTrail['action_data']['Storage']['Max Storage Period'] = $invItemsStorage["maxStoragePeriod"];
            $auditTrail['action_data']['Storage']['Max Storage Period Time Unit'] = $invItemsStorage["maxStoragePeriodTimeUnit"];
            $auditTrail['action_data']['Storage']['Min Remain Self Life'] = $invItemsStorage["minRemainSelfLife"];
            $auditTrail['action_data']['Storage']['Min Remain Self Life Time Unit'] = $invItemsStorage["minRemainSelfLifeTimeUnit"];

            $auditTrail['action_data']['Summary']['Item_Price'] = decimalValuePreview($price["price"]);
            $auditTrail['action_data']['Summary']['Price_Type'] = 'target';
            $auditTrail['action_data']['Summary']['Item Max Discount'] = decimalValuePreview($price["discount"]);



            $auditTrailreturn = generateAuditTrail($auditTrail);


            return $ins;
        } else {
            return $insertStorage;
        }
    }


    function editGoods($POST = [])
    {
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        $returnData = [];

        // console($POST);
        // echo  $POST['hsn'];
        // exit();



        // $admin = array();
        // $admin["goodsType"] = $POST["goodsType"];
        // $admin["goodsGroup"] = $POST["goodsGroup"];
        // $admin["purchaseGroup"] = $POST["purchaseGroup"];
        // $admin["itemCode"] = $POST["itemCode"];
        // $admin["itemName"] = $POST["itemName"];
        // $admin["netWeight"] = $POST["netWeight"];
        // $admin["availabilityCheck"] = $POST["availabilityCheck"];
        // $admin["grossWeight"] = $POST["grossWeight"];
        // $admin["volume"] = $POST["volume"];
        // $admin["height"] = $POST["height"];
        // $admin["width"] = $POST["width"];
        // $admin["length"] = $POST["length"];
        // $admin["baseUnitMeasure"] = $POST["baseUnitMeasure"];
        // $admin["issueUnitMeasure"] = $POST["issueUnit"];
        // $admin["itemDesc"] = $POST["itemDesc"];
        // $admin["storageBin"] = $POST["storageBin"];
        // $admin["pickingArea"] = $POST["pickingArea"];
        // $admin["tempControl"] = $POST["tempControl"];
        // $admin["storageControl"] = $POST["storageControl"];
        // $admin["maxStoragePeriod"] = $POST["maxStoragePeriod"];
        // $admin["maxStoragePeriodTimeUnit"] = $POST["maxtimeUnit"];
        // $admin["minRemainSelfLife"] = $POST["minRemainSelfLife"];
        // $admin["minRemainSelfLifeTimeUnit"] = $POST["mintimeUnit"];
        // $admin["purchasingValueKey"] = $POST["purchasingValueKey"];

        // $admin["uomRel"] = $POST["rel"];
        // $admin["hsn"] = $POST["hsn"];
        // $admin["volumeCubeCm"] = $POST["volumeCubeCm"];

        // console($_POST);
        $admin = array();
        //$admin["goodsType"] = $POST["goodsType"];
        $admin["goodsGroup"] = $POST["goodsGroup"];
        $admin["purchaseGroup"] = $POST["purchaseGroup"];
        $admin["itemName"] = $POST["itemName"] ?? 0;
        $admin["netWeight"] = $POST["netWeight"] ?? 0;
        $admin["availabilityCheck"] = $POST["availabilityCheck"] ?? 0;
        $admin["grossWeight"] = $POST["grossWeight"] ?? 0;
        $admin["volume"] = $POST["volume"] ?? 0;
        $admin["height"] = $POST["height"] ?? 0;
        $admin["width"] = $POST["width"] ?? 0;
        $admin["length"] = $POST["length"] ?? 0;
        $admin["baseUnitMeasure"] = $POST["baseUnitMeasure"] ?? 0;
        $admin["issueUnitMeasure"] = $POST["issueUnit"] ?? 0;
        $admin["itemDesc"] = $POST["itemDesc"] ?? 0;
        $admin["storageControl"] = $POST["storageControl"] ?? 0;
        $admin["maxStoragePeriod"] = $POST["maxStoragePeriod"] ?? 0;
        $admin["maxStoragePeriodTimeUnit"] = $POST["maxTime"] ?? 0;
        $admin["minRemainSelfLife"] = $POST["minRemainSelfLife"] ?? 0;
        $admin["minRemainSelfLifeTimeUnit"] = $POST["minTime"] ?? 0;
        $admin["purchasingValueKey"] = $POST["purchasingValueKey"] ?? 0;
        $admin['uomRel'] = $POST["rel"] > 0 ? $POST["rel"] : 0;
        $admin['volumeCubeCm'] = $POST["volumeCubeCm"] > 0 ? $POST["volumeCubeCm"] : 0;
        $admin['hsn'] = $POST['hsn'];
        $admin['weight_unit'] = $POST['grossWeight'] ?? 0;
        $admin['measure_unit'] = $POST['measure_unit'] ?? 0;
        $admin["serviceName"] = $POST["serviceName"] ?? 0;
        $admin["serviceDesc"] = $POST["serviceDesc"] ?? 0;
        $admin["serviceUnit"] = $POST["serviceUnit"] ?? 0;
        $admin["glCode"] = $POST["glCode"] ?? 0;
        $admin["serviceGroup"] = $POST["serviceGroup"] ?? 0;
        // $admin["stock_date"] = $POST["stock_date"] ?? 0;
        $costCenter = '-';
        if (isset($POST["costCenter"]) && $POST["costCenter"] != '') {
            $admin["costCenter"] = $POST["costCenter"] ?? 0;

            if ($admin['costCenter'] != 0) {

                $costCenter = queryGet("SELECT CostCenter_code FROM `" . ERP_COST_CENTER . "` WHERE CostCenter_status = 'active' AND branch_id = $branch_id AND company_id = $company_id AND CostCenter_id = " . $admin["costCenter"])['data']['CostCenter_code'];
            }
        } else {
            $admin["costCenter"] = 0;
        }

        $purchaseGroup = '-';
        if (isset($POST["purchaseGroup"]) && $POST["purchaseGroup"] != '') {
            $admin["purchaseGroup"] = $POST["purchaseGroup"] ?? 0;
            if ($admin['purchaseGroup'] != 0) {
                $purchaseGroup = queryGet("SELECT purchaseGroupName FROM `" . ERP_INVENTORY_MASTR_PURCHASE_GROUPS . "` WHERE companyId = $company_id AND purchaseGroupId = " . $admin["purchaseGroup"])['data']['purchaseGroupName'];;
            }
        } else {
            $admin["purchaseGroup"] = 0;
        }


        $issueUnitMeasure = '-';

        if (isset($POST["issueUnit"]) && $POST["issueUnit"] != '') {
            $admin["issueUnitMeasure"] = $POST["issueUnit"] ?? 0;
            if ($admin['issueUnitMeasure'] != 0) {
                $issueUnitMeasure = getUomDetail($admin['issueUnitMeasure'])['data']['uomName'];
            }
        } else {
            $admin["issueUnitMeasure"] = 0;
        }

        $baseUnitMeasure = '-';
        if (isset($POST["baseUnitMeasure"]) && $POST["baseUnitMeasure"] != '') {
            $admin["baseUnitMeasure"] = $POST["baseUnitMeasure"] ?? 0;
            if ($admin['baseUnitMeasure'] != 0) {
                $baseUnitMeasure = getUomDetail($admin['baseUnitMeasure'])['data']['uomName'];
            }
        } else {
            $admin["baseUnitMeasure"] = 0;
        }


        $serviceUnit = '-';
        if (isset($POST["serviceUnit"]) && $POST["serviceUnit"] != '') {
            $admin["serviceUnit"] = $POST["serviceUnit"] ?? 0;
            if ($admin['serviceUnit'] != 0) {
                $serviceUnit = getUomDetail($admin['serviceUnit'])['data']['uomName'];
            }
        } else {
            $admin["serviceUnit"] = 0;
        }
        //$admin["costCenter"] = $POST["costCenter"] ?? 0;

        if (isset($POST["tds"]) && $POST["tds"] != '') {
            $admin["tds"] = $POST["tds"] ?? 0;
        } else {
            $admin["tds"] = 0;
        }

        // if ($POST["stock"] != "") {
        //     $admin["opening_stock"] =  $POST["stock"];
        // } else {
        //     $admin["opening_stock"] = 0;
        // }
        //$admin["opening_stock"] =  isset($POST["stock"]) ? $POST["stock"] : 0;
        // $POST["stock"] ?? 0;
        if ($POST["rate"] != "") {
            $admin["rate"] = $POST["rate"];
        } else {
            $admin["rate"] = 0;
        }
        //  $admin["rate"] = $POST["rate"] ?? 0;
        // if ($POST["total"] != "") {
        //     $admin["total"] = $POST["total"];
        // } else {
        //     $admin["total"] = 0;
        // }
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
        $min_stock;
        $max_stock;
        // exit();

        // $min_stock = $POST["min_stock"] ?? 0;
        // $max_stock = $POST["max_stock"] ?? 0;

        // echo 1;
        // echo  $admin["opening_stock"];
        // exit();
        $itemID = $_POST['id'];
        $goodTypeId = $POST["goodsType"];
        $checkFg = "SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE  `goodTypeId` = " . $POST['goodsType'] . "";
        $resultType = queryGet($checkFg);
        $row = $resultType["data"];
        $goodType = $row['type'];


        if ($goodType == "FG") {
            $bomRequired = $POST['bomRequired_radio'];
            $stock_col = "fgWhOpen";
            if ($bomRequired == 1) {
                $price_type = "S";
                $admin["goodsType"] = $POST["goodsType"];
            } elseif ($bomRequired == 0) {
                $admin["goodsType"] = 4;
                $price_type = "V";
            } else {
                $price_type = "";
            }


            $name = $admin["itemName"];
            $desc = $admin["itemDesc"];
            // $gl = $parentGlId;
            $goodTypeId = '3,4';
        } elseif ($goodType == "SFG") {
            $admin["goodsType"] = $POST["goodsType"];
            $bomRequired = 1;
            $price_type = "S";
            $stock_col = "sfgStockOpen";


            $name = $admin["itemName"];
            $desc = $admin["itemDesc"];
            // $gl = $parentGlId;
        } elseif ($goodType == "RM") {
            $admin["goodsType"] = $POST["goodsType"];
            $bomRequired = 0;
            $price_type = "V";
            $stock_col = "rmWhOpen";


            $name = $admin["itemName"];
            $desc = $admin["itemDesc"];
            // $gl = $parentGlId;
        } elseif ($goodType == "ASSET") {
            $admin["goodsType"] = $POST["goodsType"];
            $bomRequired = 0;
            $price_type = "V";
            $stock_col = "rmWhOpen";


            $name = $admin["itemName"];
            $desc = $admin["itemDesc"];
            // $gl =  $admin["glCode"];
        } elseif ($goodType == "SERVICES" || $goodType == "SERVICEP" || $goodType == "PROJECT") {
            $name = $POST['serviceName'];
            $desc = $admin['serviceDesc'];
            $gl =  $admin["glCode"];

            $admin["goodsType"] = $POST["goodsType"];
            $bomRequired = 0;
            $price_type = " ";
            $stock_col = " ";
        } else {
        }







        $status = 'active';


        // $company_id = $POST["company_id"];



        // $customer_code = getRandCodeNotInTable(ERP_CUSTOMER,'customer_code');

        //$adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]);
        $select_prev = queryGet("SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `itemID` = $itemID");
        $item_code = $select_prev['data']['itemCode'];

        $discountGroupArray = json_encode($POST['discount_group']);

        $insUpdate = "UPDATE `" . ERP_INVENTORY_ITEMS . "` 
                            SET
                          
                            `itemName`='" . addslashes($name) . "',
                            `itemDesc`='" . addslashes($desc) . "',
                            `netWeight`='" . $admin["netWeight"] . "',
                            `grossWeight`='" . $admin["grossWeight"] . "',
                            `volume`='" .  $admin["volume"]  . "',
                            `height`='" . $admin["height"] . "',
                            `width`='" . $admin["width"]  . "',

                            `length`='" . $admin["length"] . "',
                           `discountGroup` ='" . $discountGroupArray . "',
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
                            `service_unit`='" .  $admin["baseUnitMeasure"] . "',
                           
                            `updatedBy`='" . $updated_by . "' WHERE `itemID` = $itemID";
        //  exit();



        if (mysqli_query($dbCon, $insUpdate)) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data added success";

            $updateStorage = "UPDATE `" . ERP_INVENTORY_STORAGE . "` 
                SET
                `storageControl`='" . $admin["storageControl"] . "',
                `maxStoragePeriod`='" . $admin["maxStoragePeriod"] . "',
                `maxStoragePeriodTimeUnit`='" . $admin["maxStoragePeriodTimeUnit"] . "',
                `minRemainSelfLife`='" . $admin["minRemainSelfLife"] . "',
                `minRemainSelfLifeTimeUnit`='" . $admin["minRemainSelfLifeTimeUnit"] . "',
                `company_id`=$company_id,
                `branch_id`=$branch_id,
                `location_id`=$location_id
                WHERE  `item_id`=  $itemID";
            $update_storage = queryUpdate($updateStorage);


            $del_specifications = queryDelete("DELETE FROM `erp_item_specification` WHERE `item_id` = $itemID");
            //console($del_specifications);
            if ($del_specifications['status'] == 'success') {
                $spec = $POST['spec'];
                foreach ($spec as $spec) {

                    $spec_name = $spec['spec_name'];
                    $spec_desc = $spec['spec_detail'];
                    $insert_spec = queryInsert("INSERT INTO `erp_item_specification`
                        SET 
                          `item_id`=$itemID, 
                        `specification`='" . $spec_name . "',
                        `specification_detail`='" . $spec_desc . "',
                        `company_id`=$company_id,
                        `branch_id`=$branch_id,
                        `location_id`=$location_id,
                        `created_by`='" . $created_by . "' ");
                }
            }





            $price = array();
            $price["price"] = $POST["price"] ?? 0;
            $price["discount"] = $POST["discount"] ?? 0;

            if ($goodType == "SERVICEP" || $goodType == "SERVICES") {

                $insSummary = queryUpdate("UPDATE `erp_inventory_stocks_summary`
                SET 
                    `company_id`=$company_id,
                    `branch_id`=$branch_id,
                    `location_id`=$location_id,
                    
                    `itemPrice`='" . $price["price"] . "',
                    `itemMaxDiscount`='" . $POST["discount"] . "', 
                  
                    `stock_date`='" . $POST["service_stock_date"] . "', 
                    `updatedBy`='" . $updated_by . "' WHERE `itemId`=$itemID");

                $auditTrail['action_data']['Summary']['Item Price'] = decimalValuePreview($price["price"]);
                $auditTrail['action_data']['Summary']['Item Max Discount'] = decimalValuePreview($POST["discount"]);
                $auditTrail['action_data']['Summary']['Moving Weighted Price'] = decimalValuePreview($POST["service_rate"]);
                $auditTrail['action_data']['Summary']['Stock Date'] = formatDateWeb($POST["service_stock_date"]);
            } else if ($goodType == "ASSET") {

                $insSummary = queryUpdate("UPDATE `erp_inventory_stocks_summary`  
                SET 
                    `company_id`=$company_id,
                    `branch_id`=$branch_id,
                    `location_id`=$location_id,
                    `itemPrice`='" . $price["price"] . "', 
                    `itemMaxDiscount`='" . $POST["discount"] . "', 
                    `min_stock` =  $min_stock,
                    `max_stock` = $max_stock,
                    `movingWeightedPrice`= '" . $admin["rate"] . "',
                    `updatedBy`='" . $updated_by . "' WHERE  `itemId`=$itemID");


                $auditTrail['action_data']['Summary']['Item_Price'] = decimalValuePreview($price["price"]);
                $auditTrail['action_data']['Summary']['Price_Type'] = $price_type;
                $auditTrail['action_data']['Summary']['Item Max Discount'] = decimalValuePreview($POST["discount"]);
            } else {

                $insSummary = queryUpdate("UPDATE `erp_inventory_stocks_summary`  
                                            SET 
                                                `company_id`=$company_id,
                                                `branch_id`=$branch_id,
                                                `location_id`=$location_id,
                                                `itemPrice`='" . $price["price"]  . "', 
                                                `itemMaxDiscount`='" . $POST["discount"] . "',
                                                `movingWeightedPrice`= '" . $admin["rate"] . "',
                                                `min_stock` =  $min_stock,
                                                `max_stock` = $max_stock,
                                                `updatedBy`='" . $updated_by . "' WHERE  `itemId`=  $itemID");


                $auditTrail['action_data']['Summary']['Item_Price'] = decimalValuePreview($price["price"]);
                $auditTrail['action_data']['Summary']['Price_Type'] = $price_type;
                $auditTrail['action_data']['Summary']['Item Max Discount'] = decimalValuePreview($POST["discount"]);
                $auditTrail['action_data']['Summary']['Moving Weighted Price'] = decimalValuePreview($admin["rate"]);
                $auditTrail['action_data']['Summary']['Stock_Date'] = formatDateWeb($admin["stock_date"]);
                $auditTrail['action_data']['Summary']['Min_Stock'] = decimalQuantityPreview($min_stock);
                $auditTrail['action_data']['Summary']['Max_Stock'] = decimalQuantityPreview($max_stock);




                // $insert_log = queryUpdate("UPDATE `erp_inventory_stocks_log` SET `companyId`=$company_id,`branchId`=$branch_id,`locationId`=$location_id,`storageLocationId`=1,`storageType`='" . $stock_col . "',`itemQty`='" . $admin["opening_stock"] . "',`itemUom`='" . $admin["baseUnitMeasure"] . "',`itemPrice`='" . $admin["total"] . "',`logRef`='item creation',`updatedBy`='" . $updated_by . "', `min_stock` = $min_stock,`max_stock` = $max_stock,`status`=0 WHERE  `item_id`=  $itemID ");
            }

            $prevPics = $POST['prevPic'];
            $files = $POST['pic'];
            // console([$prevPics,$files]);
            //exit();
            $delete = queryDelete("DELETE FROM `erp_inventory_item_images` WHERE `company_id`=$company_id AND `branch_id`=$branch_id AND `item_id`= $itemID");
            foreach ($files["name"] as $key => $name) {
                $prevName = $prevPics[$key] ?? "";
                $name = $files["name"][$key] ?? "";
                $tmpName = $files["tmp_name"][$key];
                $size = $files["size"][$key];
                $allowed_types = ['jpg', 'png', 'jpeg'];
                $maxsize = 2 * 1024 * 1024; // 10 MB

                if ($files["name"][$key] != "") {

                    $fileUploaded = uploadFile(["name" => $name, "tmp_name" => $tmpName, "size" => $size], COMP_STORAGE_DIR . "/others/", $allowed_types, $maxsize, 0);
                    //  console($fileUploaded);
                    $image_name = $fileUploaded['data'];
                    // exit();
                    if ($fileUploaded['status'] == 'success') {
                        $insert_img = queryInsert("INSERT INTO `erp_inventory_item_images` SET 
                                                    `item_id`=$itemID,
                                                    `image_name`='" . $image_name . "',
                                                    `company_id`=$company_id,
                                                    `branch_id`=$branch_id,
                                                    `location_id`=$location_id,
                                                    `created_by`='" . $created_by . "',
                                                    `updated_by` ='" . $created_by . "' ");
                    }
                } else {
                    if ($prevName != "") {
                        $insert_img = queryInsert("INSERT INTO `erp_inventory_item_images` SET 
                                                    `item_id`=$itemID,
                                                    `image_name`='" . $prevName . "',
                                                    `company_id`=$company_id,
                                                    `branch_id`=$branch_id,
                                                    `location_id`=$location_id,
                                                    `created_by`='" . $created_by . "',
                                                    `updated_by` ='" . $created_by . "'");
                    }
                }
            }
            $auditTrail['action_data']['Stock_Log']['Item_Uom'] = $baseUnitMeasure;
            $auditTrail['action_data']['Stock_Log']['Log_Ref'] = 'item creation';
            $auditTrail['action_data']['Stock_Log']['Min_Stock'] = decimalQuantityPreview($min_stock);
            $auditTrail['action_data']['Stock_Log']['Max_Stock'] = decimalQuantityPreview($max_stock);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data added failed";
        }

        ///---------------------------------Audit Log Start---------------------
        $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
        $goods_id = $admin["goodsType"];
        $goodsType_sql = queryGet("SELECT * FROM `erp_inventory_mstr_good_types` WHERE `goodTypeId`=$goods_id")['data'];
        $goodsGroup = $admin["goodsGroup"];
        $goodsGroup_sql = queryGet("SELECT * FROM `erp_inventory_mstr_good_groups` WHERE `goodGroupId`=$goodsGroup")['data'];
        $auditTrail = array();
        $auditTrail['basicDetail']['trail_type'] = 'EDIT';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
        $auditTrail['basicDetail']['table_name'] = ERP_INVENTORY_ITEMS;
        $auditTrail['basicDetail']['column_name'] = 'itemId'; // Primary key column
        $auditTrail['basicDetail']['document_id'] = $itemID;  // primary key
        $auditTrail['basicDetail']['document_number'] = $item_code;
        $auditTrail['basicDetail']['action_code'] = $action_code;
        $auditTrail['basicDetail']['party_id'] = 0;
        $auditTrail['basicDetail']['action_referance'] = '';
        $auditTrail['basicDetail']['action_title'] = 'New Item updated';  //Action comment
        $auditTrail['basicDetail']['action_name'] = 'Update';     //	Add/Update/Deleted
        $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
        $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
        $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
        $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insUpdate);
        $auditTrail['basicDetail']['others'] = '';
        $auditTrail['basicDetail']['remark'] = '';

        // $auditTrail['action_data']['Basic Detail']['parentGlId'] = $gl;
        $auditTrail['action_data']['Basic Detail']['Item_Name'] = addslashes($admin["itemName"]);
        $auditTrail['action_data']['Basic Detail']['Item_Desc'] = addslashes($desc);
        $auditTrail['action_data']['Basic Detail']['Net_Weight'] = decimalQuantityPreview($admin["netWeight"]);
        $auditTrail['action_data']['Basic Detail']['Gross_Weight'] = decimalQuantityPreview($admin["grossWeight"]);
        $auditTrail['action_data']['Basic Detail']['Volume'] = decimalQuantityPreview($admin["volume"]);
        $auditTrail['action_data']['Basic Detail']['Height'] = decimalQuantityPreview($admin["height"]);
        $auditTrail['action_data']['Basic Detail']['Width'] = decimalQuantityPreview($admin["width"]);
        $auditTrail['action_data']['Basic Detail']['Length'] = decimalQuantityPreview($admin["length"]);
        $auditTrail['action_data']['Basic Detail']['Goods_Type'] = $goodsType_sql['goodTypeName'];
        $auditTrail['action_data']['Basic Detail']['Goods_Group'] = $goodsGroup_sql['goodGroupName'];
        $auditTrail['action_data']['Basic Detail']['Purchase_Group'] = $purchaseGroup;
        $auditTrail['action_data']['Basic Detail']['Availability_Check'] = ($admin["availabilityCheck"] == 0) ? '-' : $admin["availabilityCheck"];
        $auditTrail['action_data']['Basic Detail']['Base Unit Measure'] = $baseUnitMeasure;
        $auditTrail['action_data']['Basic Detail']['Issue Unit Measure'] = $issueUnitMeasure;
        $auditTrail['action_data']['Basic Detail']['Tds'] = decimalValuePreview($admin['tds']);
        $auditTrail['action_data']['Basic Detail']['Cost_Center'] = $costCenter;
        $auditTrail['action_data']['Basic Detail']['Bom_Status'] = ($bomRequired == 1) ? 'Required' : 'Not required';
        $auditTrail['action_data']['Basic Detail']['Purchasing Value Key'] = $admin["purchasingValueKey"];
        $auditTrail['action_data']['Basic Detail']['Uom_Rel'] = $admin['uomRel'];
        $auditTrail['action_data']['Basic Detail']['Status'] = $status;
        $auditTrail['action_data']['Basic Detail']['Hsn_Code'] = $admin['hsn'];
        $auditTrail['action_data']['Basic Detail']['Volume Cube Cm'] = $admin['volumeCubeCm'];
        $auditTrail['action_data']['Basic Detail']['Weight_Unit'] = $POST['net_unit'];
        $auditTrail['action_data']['Basic Detail']['Measuring_Unit'] = $POST['measure_unit'];
        $auditTrail['action_data']['Basic Detail']['Service_Unit'] = $serviceUnit;
        $auditTrail['action_data']['Basic Detail']['Service_Group'] = $admin["serviceGroup"];



        $auditTrail['action_data']['Storage']['Storage_Control'] = $admin["storageControl"];
        $auditTrail['action_data']['Storage']['Max_Storage_Period'] = $admin["maxStoragePeriod"];
        $auditTrail['action_data']['Storage']['Max Storage Period TimeUnit'] = $admin["maxStoragePeriodTimeUnit"];
        $auditTrail['action_data']['Storage']['Min Remain SelfLife'] = $admin["minRemainSelfLife"];
        $auditTrail['action_data']['Storage']['Min Remain SelfLife TimeUnit'] = $admin["minRemainSelfLifeTimeUnit"];


        $auditTrailreturn = generateAuditTrail($auditTrail);

        return $returnData;
    }

    function getAllRMGoods()
    {
        // $sql = "SELECT items.*, types.type FROM `erp_inventory_items` as items ,`erp_inventory_mstr_good_types` as types WHERE items.goodsType=types.goodTypeId AND types.type='RM'";
        // $sql = "SELECT * FROM `erp_inventory_stocks_summary` LEFT JOIN `erp_inventory_items` ON 'erp_inventory_stocks_summary.itemId' = 'erp_inventory_items.itemId',`erp_inventory_mstr_good_types` WHERE 'erp_inventory_items.goodsType' = 'erp_inventory_mstr_good_types.goodTypeId' AND 'erp_inventory_mstr_good_types.type' ='RM';";
        global $company_id;
        global $branch_id;
        global $location_id;
        // $sql = " SELECT * FROM `".ERP_INVENTORY_STOCKS_SUMMARY."` as stock LEFT JOIN `".ERP_INVENTORY_ITEMS."` as goods ON stock.itemId=goods.itemId LEFT JOIN `erp_inventory_mstr_uom` as uom ON uom.uomId=goods.baseUnitMeasure WHERE (goods.goodsType=1  OR goods.goodsType=4  OR goods.goodsType=5 OR goods.goodsType=9)  AND stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId desc";
        $sql = "SELECT * FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` as stock LEFT JOIN `" . ERP_INVENTORY_ITEMS . "` as goods ON stock.itemId=goods.itemId LEFT JOIN `erp_hsn_code` as hsn ON goods.hsnCode=hsn.hsnCode LEFT JOIN `erp_inventory_mstr_uom` as uom ON uom.uomId=goods.baseUnitMeasure  WHERE (goods.goodsType=1  OR goods.goodsType=4  OR goods.goodsType=5 OR goods.goodsType=9 OR goods.goodsType=7)  AND stock.company_id=" . $company_id . " AND stock.branch_id=" . $branch_id . " AND stock.location_id=" . $location_id . " AND goods.itemId != '' ORDER BY stock.stockSummaryId desc";
        return queryGet($sql, true);
    }

    function getAllGRNServices()
    {
        // $sql = "SELECT items.*, types.type FROM `erp_inventory_items` as items ,`erp_inventory_mstr_good_types` as types WHERE items.goodsType=types.goodTypeId AND types.type='RM'";
        // $sql = "SELECT * FROM `erp_inventory_stocks_summary` LEFT JOIN `erp_inventory_items` ON 'erp_inventory_stocks_summary.itemId' = 'erp_inventory_items.itemId',`erp_inventory_mstr_good_types` WHERE 'erp_inventory_items.goodsType' = 'erp_inventory_mstr_good_types.goodTypeId' AND 'erp_inventory_mstr_good_types.type' ='RM';";
        global $company_id;
        global $branch_id;
        global $location_id;
        $sql = " SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` as goods LEFT JOIN `erp_inventory_mstr_uom` as uom ON uom.uomId=goods.baseUnitMeasure LEFT JOIN `erp_tds_details` as tds ON tds.id=goods.tds LEFT JOIN `erp_hsn_code` as hsn ON goods.hsnCode=hsn.hsnCode WHERE goods.goodsType=7  AND goods.company_id=" . $company_id . " AND goods.branch=" . $branch_id . " AND goods.location_id=" . $location_id . " AND goods.itemId != '' ORDER BY goods.itemId desc";
        // $sql = " SELECT * FROM `".ERP_INVENTORY_STOCKS_SUMMARY."` as stock LEFT JOIN `".ERP_INVENTORY_ITEMS."` as goods ON stock.itemId=goods.itemId WHERE goods.goodsType=7  AND stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId desc";
        return queryGet($sql, true);
    }

    function fetchUom()
    {
        $sql = "SELECT * FROM `" . ERP_INVENTORY_MASTR_UOM . "` ";
        return queryGet($sql, true);
    }

    function getGoodsDeatils($goodId = null)
    {
        return queryGet('SELECT * FROM ' . ERP_INVENTORY_ITEMS . ' WHERE  `itemId`="' . $goodId . '"');
    }

    function getAllHsn()
    {
        global $dbCon;
        global $company_id;
        $returnData = [];
        if ($company_id = 19) {
            $selectSql = "SELECT * FROM " . ERP_HSN_CODE . " WHERE  isPublic='" . $company_id . "' LIMIT 500 ";
        } else {
            $selectSql = "SELECT * FROM " . ERP_HSN_CODE . " WHERE (isPublic=0 OR isPublic=19 OR isPublic='" . $company_id . "') LIMIT 500 ";
        }

        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = mysqli_num_rows($res) . " records found.";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }


    function getAllHsnPegination($POST)
    {
        global $dbCon;
        global $company_id;
        global $companyCountry;
        $returnData = [];

        $pageNo = $POST['pageNo'];
        // $show = $POST['limit']; 
        $show = 2000;
        $start = $pageNo * $show;
        $end = $show;

        $cond = '';

        if (isset($POST['keyword']) && $POST['keyword'] != '') {
            $cond .= " AND (hsnCode like '%" . $POST['keyword'] . "%' OR hsnDescription like '%" . $POST['keyword'] . "%' OR taxPercentage like '%" . $POST['keyword'] . "%')";
        }
        $selectSql = "SELECT * FROM " . ERP_HSN_CODE . " WHERE 1 AND country_id = $companyCountry " . $cond . " AND (isPublic=0 OR isPublic='" . $company_id . "') ORDER BY hsnCode  ASC LIMIT " . $start . "," . $end . " ";


        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = mysqli_num_rows($res) . " records found.";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }

    function post_stock($POST)
    {

        global $location_id;
        global $updated_by;
        global $created_by;
        global $company_id;
        global $branch_id;


        $item_id = $POST['item'];
        $storage_location = $POST['storageLocation'];
        $qty = $POST['quantity'];
        $mwp = $POST['price'];
        $item_sql = queryGet("SELECT *," . $storage_location . " as sl_quan FROM `erp_inventory_stocks_summary` WHERE `itemId`=$item_id AND `location_id`=$location_id");
        $item_data = $item_sql["data"];
        $final_sl_quan = $item_data['sl_quan'] + ($qty);
        $update = queryUpdate("UPDATE `erp_inventory_stocks_summary` SET `" . $storage_location . "`= $final_sl_quan, `movingWeightedPrice` = '" . $mwp . "' WHERE `itemId`=$item_id AND `location_id`=$location_id ");

        $log_insert_add = "INSERT INTO `erp_inventory_stocks_log` SET `itemId`=$item_id, `itemQty`='" . $qty . "',`storageType`='" . $storage_location . "',`itemPrice`='" . $mwp . "',`logRef`='stock post',`createdBy`='" . $created_by . "',`updatedBy`='" . $updated_by . "',`companyId`=$company_id,`locationId`=$location_id,`branchId`=$branch_id,`storageLocationId`=1 ";
        $returnData = $update;
        return $returnData;
    }


    function direct_consumption($POST)
    {
        //   console($POST);
        // exit();
        global $location_id;
        global $updated_by;
        global $created_by;
        global $company_id;
        global $branch_id;
        $returnData = [];
        $movement = $POST['movemenrtypesDropdown'];
        $destinationStorageLocation = $POST['destinationStorageLocation'];
        $explodessl = explode('|', $destinationStorageLocation);
        $destination_storage_location_id = $explodessl[0];
        $destination_storageLocationTypeSlug = $explodessl[1];

        $postingDate = $POST['creationDate'];
        $documentNo = 'ST' . time() . rand(1111, 9999);
        $reference = $documentNo;

        if (count($POST['listItem']) > 0) {
            $transfersql = "INSERT INTO `erp_stocktransfer`
                                SET
                                `company_id`='" . $company_id . "',
                                `branch_id`='" . $branch_id . "',
                                `location_id`='" . $location_id . "',
                                `documentNo`='" . $documentNo . "',
                                `destinationsl`='" . $destination_storage_location_id . "',
                                `destination_type`='" . $movement . "',
                                `postingDate`='" . $postingDate . "',
                                `created_by`='" . $created_by . "',
                                `updated_by`='" . $updated_by . "'";

            $transfersqlQry = queryInsert($transfersql);
            if ($transfersqlQry['status'] == 'success') {
                $transfer_id = $transfersqlQry['insertedId'];
                if ($movement == "storage_location") {
                    $listItem = $POST['listItem'];
                    $flug = 0;
                    $invTotalItems = 0;
                    foreach ($listItem as $item) {
                        $invTotalItems++;
                        $itemId = $item['itemId'];
                        $itemCode = $item['itemCode'];
                        $goodsType = $item['goodsType'];
                        $itemName = addslashes($item['itemName']);
                        $qty = $item['qty'] ?? 0;
                        $uom = $item['uom'];


                        $stockQty = $item['stockQty'];
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

                        if ($itemSellType != 'CUSTOM') {
                            $selStockLog = itemQtyStockChecking($itemId, "'rmWhOpen','rmProdOpen','sfgStockOpen', 'fgWhOpen','fgMktOpen'", $itemSellType, '', $postingDate);
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


                            $selStockLog = itemQtyStockChecking($itemId, "'rmWhOpen','rmProdOpen','sfgStockOpen', 'fgWhOpen','fgMktOpen'", 'ASC', "$keysString", $postingDate);
                            // console($selStockLog);
                            // console($filteredBatchSelection);
                            $itemOpenStocks = $selStockLog['sumOfBatches'];
                        }
                        $returnData['insStockreturn3'][] = $selStockLog;
                        if ($itemOpenStocks >= $qty) {
                            $invItem1 = "INSERT INTO `erp_stocktransfer_item`
                                SET
                                `transfer_id`='$transfer_id',
                                `item_id`='$itemId',
                                `itemCode`='" . $itemCode . "',
                                `itemName`='" . $itemName . "',
                                `qty`='" . $qty . "',
                                `uom`='" . $uom . "',
                                `createdBy`='" . $created_by . "',
                                `updatedBy`='" . $updated_by . "'";

                            $itemIns = queryInsert($invItem1);
                            if ($itemIns['status'] == 'success') {
                                $return['status'] = "success";
                                $return['message'] = "Item Insert Success!";

                                $auditTrail['action_data']['Item Details'][$itemCode]['so_Item_id'] = $transfer_id;
                                $auditTrail['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                                $auditTrail['action_data']['Item Details'][$itemCode]['itemName'] = $itemName;
                                $auditTrail['action_data']['Item Details'][$itemCode]['qty'] = $qty;
                                $auditTrail['action_data']['Item Details'][$itemCode]['uom'] = $uom;


                                foreach ($selStockLog['data'] as $stockLogKey => $logdata) {
                                    if ($itemSellType == 'CUSTOM') {

                                        // console($filteredBatchSelection);
                                        // echo '********************************';
                                        // $explodes = explode('_', $logdata['logRef']);
                                        // $logRef = $explodes[0];
                                        // $slocation = $explodes[1];
                                        $logRef = $logdata['logRef'];
                                        $keysval = $logdata['logRefConcat'];
                                        $usedQuantity = $filteredBatchSelection[$keysval];
                                        $bornDate = $logdata['bornDate'];
                                        $storage_location_id = $logdata['storage_location_id'];
                                        $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                                        $itemPrice = $logdata['itemPrice'];
                                    } else {
                                        if ($qty <= 0) {
                                            break;
                                        }

                                        $itemPrice = $logdata['itemPrice'];
                                        $quantity = $logdata['itemQty'];
                                        $usedQuantity = min($quantity, $qty);
                                        $qty -= $usedQuantity;
                                        // $explodes = explode('_', $logdata['logRef']);
                                        // $logRef = $explodes[0];

                                        $logRef = $logdata['logRef'];
                                        $bornDate = $logdata['bornDate'];
                                        $storage_location_id = $logdata['storage_location_id'];
                                        $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                                        $itemPrice = $logdata['itemPrice'];
                                    }

                                    // moving weighted price calculation
                                    $newMwp = fetchCurrentMwp($itemId);
                                    $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                SET 
                                                    companyId = '" . $company_id . "',
                                                    branchId = '" . $branch_id . "',
                                                    locationId = '" . $location_id . "',
                                                    parentId='" . $transfer_id . "',
                                                    storageLocationId = '" . $storage_location_id . "',
                                                    storageType ='" . $storageLocationTypeSlug . "',
                                                    itemId = '" . $itemId . "',
                                                    itemQty = '" . $usedQuantity * -1 . "',
                                                    itemUom = '" . $uom . "',
                                                    itemPrice = '" . $newMwp . "',
                                                    refActivityName='STRGE-LOC',
                                                    logRef = '" . $logRef . "',
                                                    refNumber='" . $documentNo . "',
                                                    bornDate='" . $bornDate . "',
                                                    postingDate='" . $postingDate . "',
                                                    createdBy = '" . $created_by . "',
                                                    updatedBy = '" . $updated_by . "'";

                                    $insStockreturn1 = queryInsert($insStockSummary1);

                                    $insStockSummary2 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                SET 
                                                    companyId = '" . $company_id . "',
                                                    branchId = '" . $branch_id . "',
                                                    locationId = '" . $location_id . "',
                                                    parentId='" . $transfer_id . "',
                                                    storageLocationId = '" . $destination_storage_location_id . "',
                                                    storageType ='" . $destination_storageLocationTypeSlug . "',
                                                    itemId = '" . $itemId . "',
                                                    itemQty = '" . $usedQuantity . "',
                                                    itemUom = '" . $uom . "',
                                                    itemPrice = '" . $newMwp . "',
                                                    refActivityName='STRGE-LOC',
                                                    logRef = '" . $logRef . "',
                                                    refNumber='" . $documentNo . "',
                                                    bornDate='" . $bornDate . "',
                                                    postingDate='" . $postingDate . "',
                                                    createdBy = '" . $created_by . "',
                                                    updatedBy = '" . $updated_by . "'";

                                    $insStockreturn2 = queryInsert($insStockSummary2);
                                    // console($insStockreturn1);

                                    $returnData['insStockreturn1'][] = $insStockreturn1;
                                    $returnData['insStockreturn2'][] = $insStockreturn2;
                                }
                            } else {
                                $returnData['status'] = "warning";
                                $returnData['message1'] = "somthing went wrong! 31";
                                $returnData['itemIns'] = $itemIns;
                                $flug++;
                            }
                        } else {
                            $returnData['status'] = "warning";
                            $returnData['message2'] = "Stock quantity issue - " . $item['itemCode'];
                            $flug++;
                        }
                    }
                    if ($flug == 0) {
                        $return["status"] = "success";
                        $return["message"] = "Stock Transfer Successfull";
                        $return["documentNo"] = $documentNo;
                        $return["returnData"] = $returnData;
                    } else {
                        $return["status"] = "warning";
                        $return["message"] = "Stock Transferred Failure!";
                        $return["returnData"] = $returnData;
                    }
                } elseif ($movement == "production_order") {
                    $listItem = $POST['listItem'];
                    $flug = 0;
                    $invTotalItems = 0;
                    $itemsQtyUpdate = [];
                    foreach ($listItem as $productionorderkey => $item) {
                        $invTotalItems++;
                        $itemId = $item['itemId'];
                        $itemCode = $item['itemCode'];
                        $goodsType = $item['goodsType'];
                        $itemName = addslashes($item['itemName']);
                        $qty = $item['qty'] ?? 0;
                        $uom = $item['uom'];

                        $stockQty = $item['stockQty'];
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

                        if ($itemSellType != 'CUSTOM') {
                            $selStockLog = itemQtyStockChecking($itemId, "'rmWhOpen','rmProdOpen','sfgStockOpen', 'fgWhOpen','fgMktOpen'", $itemSellType, '', $postingDate);
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


                            $selStockLog = itemQtyStockCheckWithAcc($itemId, "'rmWhOpen','rmProdOpen','sfgStockOpen', 'fgWhOpen','fgMktOpen'", 'ASC', "$keysString", $postingDate);
                            // console($selStockLog);
                            // console($filteredBatchSelection);
                            $itemOpenStocks = $selStockLog['sumOfBatches'];
                        }
                        $returnData['insStockreturn3'][] = $selStockLog;
                        if ($itemOpenStocks >= $qty) {
                            $invItem1 = "INSERT INTO `erp_stocktransfer_item`
                                SET
                                `transfer_id`='$transfer_id',
                                `item_id`='$itemId',
                                `itemCode`='" . $itemCode . "',
                                `itemName`='" . $itemName . "',
                                `qty`='" . $qty . "',
                                `uom`='" . $uom . "',
                                `createdBy`='" . $created_by . "',
                                `updatedBy`='" . $updated_by . "'";

                            $itemIns = queryInsert($invItem1);
                            if ($itemIns['status'] == 'success') {
                                $return['status'] = "success";
                                $return['message'] = "Item Insert Success!";

                                $auditTrail['action_data']['Item Details'][$itemCode]['so_Item_id'] = $transfer_id;
                                $auditTrail['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                                $auditTrail['action_data']['Item Details'][$itemCode]['itemName'] = $itemName;
                                $auditTrail['action_data']['Item Details'][$itemCode]['qty'] = $qty;
                                $auditTrail['action_data']['Item Details'][$itemCode]['uom'] = $uom;

                                $item_price_query = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId AND `location_id`=$location_id");

                                $itemPrice = $item_price_query["data"]["movingWeightedPrice"] ?? 0;

                                $listItem[$productionorderkey]["price"] = $itemPrice * $qty;

                                foreach ($selStockLog['data'] as $stockLogKey => $logdata) {
                                    if ($itemSellType == 'CUSTOM') {

                                        // console($filteredBatchSelection);
                                        // echo '********************************';
                                        // $explodes = explode('_', $logdata['logRef']);
                                        // $logRef = $explodes[0];
                                        // $slocation = $explodes[1];
                                        $logRef = $logdata['logRef'];
                                        $keysval = $logdata['logRefConcat'];
                                        $usedQuantity = $filteredBatchSelection[$keysval];
                                        $bornDate = $logdata['bornDate'];
                                        $storage_location_id = $logdata['storage_location_id'];
                                        $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                                        // $itemPrice = $logdata['itemPrice'];
                                    } else {
                                        if ($qty <= 0) {
                                            break;
                                        }

                                        // $itemPrice = $logdata['itemPrice'];
                                        $quantity = $logdata['itemQty'];
                                        $usedQuantity = min($quantity, $qty);
                                        $qty -= $usedQuantity;
                                        // $explodes = explode('_', $logdata['logRef']);
                                        // $logRef = $explodes[0];

                                        $logRef = $logdata['logRef'];
                                        $bornDate = $logdata['bornDate'];
                                        $storage_location_id = $logdata['storage_location_id'];
                                        $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                                        // $itemPrice = $logdata['itemPrice'];
                                    }

                                    $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                SET 
                                                    companyId = '" . $company_id . "',
                                                    branchId = '" . $branch_id . "',
                                                    locationId = '" . $location_id . "',
                                                    parentId='" . $transfer_id . "',
                                                    storageLocationId = '" . $storage_location_id . "',
                                                    storageType ='" . $storageLocationTypeSlug . "',
                                                    itemId = '" . $itemId . "',
                                                    itemQty = '" . $usedQuantity * -1 . "',
                                                    itemUom = '" . $uom . "',
                                                    itemPrice = '" . $itemPrice . "',
                                                    refActivityName='CONSUMPTION(PROD-ORDR)',
                                                    logRef = '" . $logRef . "',
                                                    refNumber='" . $documentNo . "',
                                                    bornDate='" . $bornDate . "',
                                                    postingDate='" . $postingDate . "',
                                                    createdBy = '" . $created_by . "',
                                                    updatedBy = '" . $updated_by . "'";

                                    $insStockreturn1 = queryInsert($insStockSummary1);
                                    $itemsQtyUpdate[] = [
                                        'itemId' => $itemId,
                                        'qty' => $usedQuantity
                                    ];
                                    $returnData['insStockreturn1'][] = $insStockreturn1;
                                }
                            } else {
                                $returnData['status'] = "warning";
                                $returnData['message1'] = "somthing went wrong! 31";
                                $returnData['itemIns'] = $itemIns;
                                $flug++;
                            }
                        } else {
                            $returnData['status'] = "warning";
                            $returnData['message2'] = "Stock quantity issue - " . $item['itemCode'];
                            $flug++;
                        }
                    }
                    if ($flug == 0) {

                        //-----------------------------stockPostingProductionOrder ACC Start ----------------
                        $dataInputData = [
                            "BasicDetails" => [
                                "documentNo" => $documentNo, // Invoice Doc Number
                                "documentDate" => $postingDate, // Invoice number
                                "postingDate" => $postingDate, // current date
                                "grnJournalId" => '',
                                "reference" => $documentNo, // grn code
                                "remarks" => "Stock Posting - Production Order - " . $documentNo,
                                "journalEntryReference" => "StockPosting"
                            ],
                            "FGItems" => $listItem
                        ];
                        //console($dataInputData);
                        $dataPostingObj = $this->stockPostingProductionOrderAccountingPosting($dataInputData, "stockPostingProductionOrder", $transfer_id);

                        if ($dataPostingObj['status'] == "success") {
                            stockQtyImpact($itemsQtyUpdate);
                        }

                        $return["status"] = "success";
                        $return["message"] = "Stock Transfer Successfull";
                        $return["documentNo"] = $documentNo;
                        $return["returnData"] = $returnData;
                        $return["accounting"] = $dataPostingObj;
                    } else {
                        $return["status"] = "warning";
                        $return["message"] = "Stock Transferred Failure!";
                        $return["returnData"] = $returnData;
                    }
                } elseif ($movement == "cost_center") {
                    $listItem = $POST['listItem'];
                    $flug = 0;
                    $invTotalItems = 0;
                    $itemsQtyUpdate = [];
                    foreach ($listItem as $costcenterkey => $item) {
                        $invTotalItems++;
                        $itemId = $item['itemId'];
                        $itemCode = $item['itemCode'];
                        $goodsType = $item['goodsType'];
                        $itemName = addslashes($item['itemName']);
                        $qty = $item['qty'] ?? 0;
                        $uom = $item['uom'];

                        $stockQty = $item['stockQty'];
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

                        if ($itemSellType != 'CUSTOM') {
                            $selStockLog = itemQtyStockCheckWithAcc($itemId, "'rmWhOpen','rmProdOpen','sfgStockOpen', 'fgWhOpen','fgMktOpen'", $itemSellType, '', $postingDate);
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


                            $selStockLog = itemQtyStockCheckWithAcc($itemId, "'rmWhOpen','rmProdOpen','sfgStockOpen', 'fgWhOpen','fgMktOpen'", 'ASC', "$keysString", $postingDate);
                            // console($selStockLog);
                            // console($filteredBatchSelection);
                            $itemOpenStocks = $selStockLog['sumOfBatches'];
                        }
                        $returnData['insStockreturn3'][] = $selStockLog;
                        if ($itemOpenStocks >= $qty) {
                            $invItem1 = "INSERT INTO `erp_stocktransfer_item`
                                SET
                                `transfer_id`='$transfer_id',
                                `item_id`='$itemId',
                                `itemCode`='" . $itemCode . "',
                                `itemName`='" . $itemName . "',
                                `qty`='" . $qty . "',
                                `uom`='" . $uom . "',
                                `createdBy`='" . $created_by . "',
                                `updatedBy`='" . $updated_by . "'";

                            $itemIns = queryInsert($invItem1);
                            if ($itemIns['status'] == 'success') {
                                $return['status'] = "success";
                                $return['message'] = "Item Insert Success!";

                                $auditTrail['action_data']['Item Details'][$itemCode]['so_Item_id'] = $transfer_id;
                                $auditTrail['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                                $auditTrail['action_data']['Item Details'][$itemCode]['itemName'] = $itemName;
                                $auditTrail['action_data']['Item Details'][$itemCode]['qty'] = $qty;
                                $auditTrail['action_data']['Item Details'][$itemCode]['uom'] = $uom;

                                $item_price_query = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId AND `location_id`=$location_id");

                                $itemPrice = $item_price_query["data"]["movingWeightedPrice"] ?? 0;

                                $listItem[$costcenterkey]["price"] = $itemPrice * $qty;

                                foreach ($selStockLog['data'] as $stockLogKey => $logdata) {
                                    if ($itemSellType == 'CUSTOM') {

                                        // console($filteredBatchSelection);
                                        // echo '********************************';
                                        // $explodes = explode('_', $logdata['logRef']);
                                        // $logRef = $explodes[0];
                                        // $slocation = $explodes[1];
                                        $logRef = $logdata['logRef'];
                                        $keysval = $logdata['logRefConcat'];
                                        $usedQuantity = $filteredBatchSelection[$keysval];
                                        $bornDate = $logdata['bornDate'];
                                        $storage_location_id = $logdata['storage_location_id'];
                                        $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                                        // $itemPrice = $logdata['itemPrice'];
                                    } else {
                                        if ($qty <= 0) {
                                            break;
                                        }

                                        // $itemPrice = $logdata['itemPrice'];
                                        $quantity = $logdata['itemQty'];
                                        $usedQuantity = min($quantity, $qty);
                                        $qty -= $usedQuantity;
                                        // $explodes = explode('_', $logdata['logRef']);
                                        // $logRef = $explodes[0];

                                        $logRef = $logdata['logRef'];
                                        $bornDate = $logdata['bornDate'];
                                        $storage_location_id = $logdata['storage_location_id'];
                                        $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                                        // $itemPrice = $logdata['itemPrice'];
                                    }

                                    $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                SET 
                                                    companyId = '" . $company_id . "',
                                                    branchId = '" . $branch_id . "',
                                                    locationId = '" . $location_id . "',
                                                    parentId='" . $transfer_id . "',
                                                    storageLocationId = '" . $storage_location_id . "',
                                                    storageType ='" . $storageLocationTypeSlug . "',
                                                    itemId = '" . $itemId . "',
                                                    itemQty = '" . $usedQuantity * -1 . "',
                                                    itemUom = '" . $uom . "',
                                                    itemPrice = '" . $itemPrice . "',
                                                    refActivityName='CONSUMPTION(COST-CENTER)',
                                                    logRef = '" . $logRef . "',
                                                    refNumber='" . $documentNo . "',
                                                    bornDate='" . $bornDate . "',
                                                    postingDate='" . $postingDate . "',
                                                    createdBy = '" . $created_by . "',
                                                    updatedBy = '" . $updated_by . "'";

                                    $insStockreturn1 = queryInsert($insStockSummary1);

                                    $itemsQtyUpdate[] = [
                                        'itemId' => $itemId,
                                        'qty' => $usedQuantity
                                    ];
                                    $returnData['insStockreturn1'][] = $insStockreturn1;
                                }
                            } else {
                                $returnData['status'] = "warning";
                                $returnData['message1'] = "somthing went wrong! 31";
                                $returnData['itemIns'] = $itemIns;
                                $flug++;
                            }
                        } else {
                            $returnData['status'] = "warning";
                            $returnData['message2'] = "Stock quantity issue - " . $item['itemCode'];
                            $flug++;
                        }
                    }
                    if ($flug == 0) {

                        $dataInputData = [
                            "BasicDetails" => [
                                "documentNo" => $documentNo, // Invoice Doc Number
                                "documentDate" => $postingDate, // Invoice number
                                "postingDate" => $postingDate, // current date
                                "grnJournalId" => '',
                                "reference" => $documentNo, // grn code
                                "remarks" => "Stock Posting - Cost Center - " . $documentNo,
                                "journalEntryReference" => "StockPosting"
                            ],
                            "FGItems" => $listItem
                        ];
                        //console($dataInputData);
                        $dataPostingObjCostCenter = $this->stockPostingCostcenterAccountingPosting($dataInputData, "stockPostingCostcenter", $transfer_id);

                        if ($dataPostingObjCostCenter['status'] == "success") {
                            stockQtyImpact($itemsQtyUpdate);
                        }
                        $return["status"] = "success";
                        $return["message"] = "Stock Transfer Successfull";
                        $return["documentNo"] = $documentNo;
                        $return["returnData"] = $returnData;
                        $return["accounting"] = $dataPostingObjCostCenter;
                    } else {
                        $return["status"] = "warning";
                        $return["message"] = "Stock Transferred Failure!";
                        $return["returnData"] = $returnData;
                    }
                } elseif ($movement == "material_to_material") {
                    // console($POST);
                    // // exit();
                    $listItem = $POST['listItem'];
                    $flug = 0;
                    $invTotalItems = 0;
                    $itemsQtyUpdate = [];
                    foreach ($listItem as $itemKey => $item) {
                        $invTotalItems++;
                        $itemId = $item['itemId'];
                        $itemCode = $item['itemCode'];
                        $goodsType = $item['goodsType'];
                        $itemName = addslashes($item['itemName']);
                        $qty = $item['qty'] ?? 0;
                        $uom = $item['uom'];
                        $destUom = $item['destination_uom'];
                        $destination_item_id = $item['destinationItems'];
                        $checkitem = checkItemImpactById($destination_item_id);

                        if ($checkitem['status'] != "success") {
                            continue;
                        }
                        $destination_item_qty = $item['destination_qty'];
                        $destination_st_location = $item['destinationStorageLocation'];
                        $destination_st_location_explode = explode('|', $destination_st_location);
                        $destination_st_location_id = $destination_st_location_explode[0];
                        $destination_st_location_slug = $destination_st_location_explode[1];

                        $destination_item_query = queryGet("SELECT parentGlId, itemCode, itemName, itemId, goodsType FROM `erp_inventory_items` WHERE `itemId`=$destination_item_id AND `location_id`=$location_id")["data"];

                        $listItem[$itemKey]["toItem"] = $destination_item_query;

                        $stockQty = $item['stockQty'];
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
                                $itemSellType = 'CUSTOM';
                                $batchselection = $item['batchselection'];
                            }
                        }

                        if ($itemSellType != 'CUSTOM') {
                            $selStockLog = itemQtyStockChecking($itemId, "'rmWhOpen','rmProdOpen','sfgStockOpen', 'fgWhOpen','fgMktOpen'", $itemSellType, '', $postingDate);
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


                            $selStockLog = itemQtyStockChecking($itemId, "'rmWhOpen','rmProdOpen','sfgStockOpen', 'fgWhOpen','fgMktOpen'", 'ASC', "$keysString", $postingDate);
                            // console($selStockLog);
                            // console($filteredBatchSelection);
                            $itemOpenStocks = $selStockLog['sumOfBatches'];
                        }

                        $returnData['insStockreturn3'][] = $selStockLog;

                        // exit();

                        if ($itemOpenStocks >= $qty) {
                            $invItem1 = "INSERT INTO `erp_stocktransfer_item`
                                SET
                                `transfer_id`='$transfer_id',
                                `item_id`='$itemId',
                                `itemCode`='" . $itemCode . "',
                                `itemName`='" . $itemName . "',
                                `qty`='" . $qty . "',
                                `uom`='" . $uom . "',
                                `dest_item`='" . $destination_item_id . "',
                                `dest_qty`='" . $destination_item_qty . "',
                                `dest_uom`='" . $destUom . "',
                                `dest_storage_location`='" . $destination_st_location_id . "',
                                `createdBy`='" . $created_by . "',
                                `updatedBy`='" . $updated_by . "'";

                            $itemIns = queryInsert($invItem1);

                            if ($itemIns['status'] == 'success') {
                                $return['status'] = "success";
                                $return['message'] = "Item Insert Success!";

                                $auditTrail['action_data']['Item Details'][$itemCode]['so_Item_id'] = $transfer_id;
                                $auditTrail['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                                $auditTrail['action_data']['Item Details'][$itemCode]['itemName'] = $itemName;
                                $auditTrail['action_data']['Item Details'][$itemCode]['qty'] = $qty;
                                $auditTrail['action_data']['Item Details'][$itemCode]['uom'] = $uom;


                                $from_item_price_query = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId AND `location_id`=$location_id");
                                $itemPrice = $from_item_price_query["data"]["movingWeightedPrice"];

                                $valuation_class_query = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$destination_item_id AND `location_id`=$location_id");

                                $to_item_quantity = queryGet("SELECT SUM(last_closing_quantity) AS total_closing_quantity
                            FROM
                                (
                                SELECT
                                    report.item_id,
                                    report.storage_id,
                                    SUM(report.total_closing_qty) AS last_closing_quantity
                                FROM
                                    erp_inventory_stocks_log_report AS report
                                INNER JOIN(
                                    SELECT item_id,
                                        storage_id,
                                        MAX(report_date) AS max_date
                                    FROM
                                        erp_inventory_stocks_log_report
                                    WHERE
                                        report_date <= '" . $postingDate . "' AND company_id = $company_id AND branch_id = $branch_id AND location_id = $location_id AND item_id=$destination_item_id
                                    GROUP BY
                                        item_id,
                                        storage_id
                                ) AS max_dates
                            ON
                                report.item_id = max_dates.item_id AND report.storage_id = max_dates.storage_id AND report.report_date = max_dates.max_date
                            GROUP BY
                                report.item_id,
                                report.storage_id
                            ) AS last_closing_quantities
                            LEFT JOIN erp_inventory_items AS item
                            ON
                                last_closing_quantities.item_id = item.itemId
                            GROUP BY
                                item_id;");

                                $class_valuation = $valuation_class_query["data"]["priceType"];


                                if ($class_valuation == "V") {

                                    $from_quantity = $qty;
                                    $from_price = $itemPrice ?? 0.00; //50


                                    $to_quantity = $to_item_quantity["data"]["total_closing_quantity"] ?? 0; //3000.00

                                    $destination_qty = $destination_item_qty ?? 0;

                                    $new_to_qty = (float)$destination_qty + $to_quantity;

                                    $total_value = (float)$from_quantity * $from_price;
                                    $per_item_price = (float)$total_value / $destination_qty;

                                    // $x = (float)(($to_quantity * $to_price) + ($destination_qty * $per_item_price));
                                    // $y = (float)($to_quantity + $new_to_qty);

                                    // $movingWeightedPrice1 = (float)($x / $y);
                                    // console("oldcal===".$movingWeightedPrice1);
                                    calculateNewMwp($destination_item_id, $destination_qty, $per_item_price, "mat");

                                    $movingWeightedPrice = $per_item_price;
                                    // $oneItemStocksQty = $qty; //500
                                    // $oneItemUnitPrice = $itemPrice ?? 0.00; //50

                                    // $prevTotalQty = $to_item_quantity ?? 0; //3000.00
                                    // $prevMovingWeightedPrice = $valuation_class_query["data"]["movingWeightedPrice"] ?? 0; //50.00
                                    // $prevTotalPrice = $prevTotalQty * $prevMovingWeightedPrice; //1,50,000

                                    // $itemNewTotalQty = (float)$prevTotalQty + $oneItemStocksQty; //3500
                                    // $itemNewTotalPrice = (float)$prevTotalPrice + ($oneItemStocksQty * $oneItemUnitPrice); //(150000 + (500 * 50)) //175000
                                    // $movingWeightedPrice = (float)($itemNewTotalPrice / $itemNewTotalQty) + 0; //(175000/3500) // 

                                    if (is_nan($movingWeightedPrice)) {
                                        $movingWeightedPrice = 0;
                                    }

                                    $price_diff = 0;
                                } else {

                                    // ------- for valuation class S ---------

                                    // $oneItemStocksQty = $qty; //500
                                    // $oneItemUnitPrice = $itemPrice ?? 0.00; //50

                                    // $prevTotalQty = $to_item_quantity ?? 0; //3000.00
                                    // $prevMovingWeightedPrice = $valuation_class_query["data"]["movingWeightedPrice"] ?? 0; //50.00
                                    // $prevTotalPrice = $prevTotalQty * $prevMovingWeightedPrice; //1,50,000

                                    // $itemNewTotalQty = (float)$prevTotalQty + $oneItemStocksQty; //3500
                                    // $itemNewTotalPrice = (float)$prevTotalPrice + ($oneItemStocksQty * $oneItemUnitPrice); //(150000 + (500 * 50)) //175000
                                    // $movingWeightedPrice = (float)($itemNewTotalPrice / $itemNewTotalQty) + 0; //(175000/3500) // 

                                    $from_quantity = $qty;
                                    $from_price = $itemPrice ?? 0.00; //50

                                    $to_quantity = $to_item_quantity["data"]["total_closing_quantity"] ?? 0; //3000.00
                                    $to_price = $valuation_class_query["data"]["movingWeightedPrice"] ?? 0; //50.00

                                    $destination_qty = $destination_item_qty ?? 0;

                                    $new_to_qty = (float)$destination_qty + $to_quantity;

                                    $total_value = (float)$from_quantity * $from_price;
                                    $per_item_price = (float)$total_value / $destination_qty;

                                    // $x = (float)(($to_quantity * $to_price) + ($destination_qty * $per_item_price));
                                    // $y = (float)($to_quantity + $destination_qty);

                                    // $movingWeightedPrice = (float)($x / $y);

                                    // if (is_nan($movingWeightedPrice)) {
                                    $movingWeightedPrice = $to_price;
                                    // }

                                    $itemsQtyUpdate[] = [
                                        'itemId' => $destination_item_id,
                                        'qty' => $destination_qty * -1,
                                    ];
                                    //price Difference
                                    $price_diff = ($from_quantity * $from_price) - ($destination_qty * $to_price);
                                }

                                //Update Summary


                                // if ($valuation_class_query["numRows"] > 0) {
                                //     $goodStockInserSql = 'UPDATE `erp_inventory_stocks_summary` SET `itemTotalQty`=' . $new_to_qty . ', `movingWeightedPrice`=' . $movingWeightedPrice . ' ,`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $destination_item_id;
                                //     $goodStockInsertObj = queryUpdate($goodStockInserSql);
                                // } else {
                                //     $goodStockInserSql = 'INSERT INTO `erp_inventory_stocks_summary` SET `itemTotalQty` = ' . $destination_qty . ', `movingWeightedPrice`=' . $movingWeightedPrice . ', `updatedBy`="' . $updated_by . '", `createdBy`="' . $created_by . '", `company_id`=' . $company_id . ', `branch_id`=' . $branch_id . ', `location_id`=' . $location_id . ', `itemId`=' . $destination_item_id;
                                //     $goodStockInsertObj = queryInsert($goodStockInserSql);
                                // }

                                $listItem[$itemKey]["price"] = $itemPrice * $qty;
                                $listItem[$itemKey]["price_diff"] = $price_diff;


                                foreach ($selStockLog['data'] as $stockLogKey => $logdata) {
                                    if ($itemSellType == 'CUSTOM') {


                                        $logRef = $logdata['logRef'];
                                        $keysval = $logdata['logRefConcat'];
                                        $usedQuantity = $filteredBatchSelection[$keysval];
                                        $bornDate = $logdata['bornDate'];
                                        $storage_location_id = $logdata['storage_location_id'];
                                        $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                                        // $itemPrice = $logdata['itemPrice'];
                                        if ($destination_qty != $qty) {
                                            $inqty = $destination_qty * ($usedQuantity / $qty); // 9*(1/3)=3
                                        } else {
                                            $inqty = $usedQuantity;
                                        }
                                    } else {
                                        if ($qty <= 0) {
                                            break;
                                        }

                                        // $itemPrice = $logdata['itemPrice'];
                                        $quantity = $logdata['itemQty'];
                                        $usedQuantity = min($quantity, $qty);
                                        $qty -= $usedQuantity;
                                        // $explodes = explode('_', $logdata['logRef']);
                                        // $logRef = $explodes[0];

                                        $logRef = $logdata['logRef'];
                                        $bornDate = $logdata['bornDate'];
                                        $storage_location_id = $logdata['storage_location_id'];
                                        $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                                        // $itemPrice = $logdata['itemPrice'];
                                    }

                                    $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                SET 
                                                    companyId = '" . $company_id . "',
                                                    branchId = '" . $branch_id . "',
                                                    locationId = '" . $location_id . "',
                                                    parentId='" . $transfer_id . "',
                                                    storageLocationId = '" . $storage_location_id . "',
                                                    storageType ='" . $storageLocationTypeSlug . "',
                                                    itemId = '" . $itemId . "',
                                                    itemQty = '" . $usedQuantity * -1 . "',
                                                    itemUom = '" . $uom . "',
                                                    itemPrice = '" . $itemPrice . "',
                                                    refActivityName='MAT-MAT-OUT',
                                                    logRef = '" . $logRef . "',
                                                    refNumber='" . $documentNo . "',
                                                    bornDate='" . $bornDate . "',
                                                    postingDate='" . $postingDate . "',
                                                    createdBy = '" . $created_by . "',
                                                    updatedBy = '" . $updated_by . "'";

                                    $insStockreturn1 = queryInsert($insStockSummary1);


                                    $itemsQtyUpdate[] = [
                                        'itemId' => $itemId,
                                        'qty' => $usedQuantity
                                    ];
                                    $insStockSummary2 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                SET 
                                                    companyId = '" . $company_id . "',
                                                    branchId = '" . $branch_id . "',
                                                    locationId = '" . $location_id . "',
                                                    parentId='" . $transfer_id . "',
                                                    storageLocationId = '" . $destination_st_location_id . "',
                                                    storageType ='" . $destination_st_location_slug . "',
                                                    itemId = '" . $destination_item_id . "',
                                                    itemQty = '" . $inqty . "',
                                                    itemUom = '" . $destUom . "',
                                                    itemPrice = '" . $movingWeightedPrice . "',
                                                    refActivityName='MAT-MAT-IN',
                                                    logRef = '" . $logRef . "',
                                                    refNumber='" . $documentNo . "',
                                                    bornDate='" . $bornDate . "',
                                                    postingDate='" . $postingDate . "',
                                                    createdBy = '" . $created_by . "',
                                                    updatedBy = '" . $updated_by . "'";

                                    $insStockreturn2 = queryInsert($insStockSummary2);
                                    // console($insStockreturn1);

                                    
                                    $returnData['insStockreturn1'][] = $insStockreturn1;
                                    $returnData['insStockreturn2'][] = $insStockreturn2;
                                }
                            } else {
                                $returnData['status'] = "warning";
                                $returnData['message1'] = "somthing went wrong! 31";
                                $returnData['itemIns'] = $itemIns;
                                $flug++;
                            }
                        } else {
                            $returnData['status'] = "warning";
                            $returnData['message2'] = "Stock quantity issue - " . $item['itemCode'];
                            $flug++;
                        }
                    }
                    if ($flug == 0) {

                        $dataInputData = [
                            "BasicDetails" => [
                                "documentNo" => $documentNo, // Invoice Doc Number
                                "documentDate" => $postingDate, // Invoice number
                                "postingDate" => $postingDate, // current date
                                "grnJournalId" => '',
                                "reference" => $documentNo, // grn code
                                "remarks" => "Stock Posting - Material To Material - " . $documentNo,
                                "journalEntryReference" => "StockPosting"
                            ],
                            "FGItems" => $listItem
                        ];


                        // console($dataInputData);
                        $dataPostingObjmaterial = $this->stockDifferenceMaterialToMaterialAccountingPosting($dataInputData, "stockDifferenceMaterialToMaterial", $transfer_id);
                        // console($dataPostingObjmaterial);

                        if ($dataPostingObjmaterial['status'] == "success") {
                            // console($itemsQtyUpdate);
                            $jounalId = $dataPostingObjmaterial['journalId'];
                            stockQtyImpact($itemsQtyUpdate);
                            $upSql = "UPDATE `erp_stocktransfer` SET `journal_id` = $jounalId WHERE `transfer_id` = $transfer_id";
                            $upSqlObj = queryUpdate($upSql);
                        }
                        // exit;


                        $return["status"] = "success";
                        $return["message"] = "Stock Transfer Successfull";
                        $return["documentNo"] = $documentNo;
                        $return["returnData"] = $returnData;
                        $return["accounting"] = $dataPostingObjmaterial;
                    } else {
                        $return["status"] = "warning";
                        $return["message"] = "Stock Transferred Failure!";
                        $return["returnData"] = $returnData;
                    }
                } elseif ($movement == "book_to_physical") {
                    $listItem = $POST['listItem'];
                    $flug = 0;
                    $invTotalItems = 0;
                    foreach ($listItem as $booktophykey => $item) {
                        $invTotalItems++;
                        $itemId = $item['itemId'];
                        $itemCode = $item['itemCode'];
                        $goodsType = $item['goodsType'];
                        $itemName = addslashes($item['itemName']);
                        $qty = $item['qty'] ?? 0;
                        $uom = $item['uom'];
                        $sign = $item['sign'];
                        $manualbatchselectionQty = $item['manualbatchselection']['qty'] ?? 0;
                        if ($manualbatchselectionQty > 0) {
                            $sign = "+";
                        }
                        $qty2 = $sign . $qty;
                        summeryDirectStockUpdateByItemId($itemId, $qty, $sign);
                        $manualbatchselectionSL = $item['manualbatchselection']['storageLocation'];

                        $stockQty = $item['stockQty'];
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

                        if ($itemSellType != 'CUSTOM') {
                            $selStockLog = itemQtyStockChecking($itemId, "'rmWhOpen','rmProdOpen','sfgStockOpen', 'fgWhOpen','fgMktOpen'", $itemSellType, '', $postingDate);
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
                                    $filteredBatchSelection[$keysval] = $sign . $value;
                                }
                            }

                            $keysString = "'" . implode("', '", array_keys($filteredBatchSelection)) . "'";


                            $selStockLog = itemQtyStockChecking($itemId, "'rmWhOpen','rmProdOpen','sfgStockOpen', 'fgWhOpen','fgMktOpen'", 'ASC', "$keysString", $postingDate);
                            // console($selStockLog);
                            // console($filteredBatchSelection);
                            $itemOpenStocks = $selStockLog['sumOfBatches'];
                        }
                        $returnData['insStockreturn3'][] = $selStockLog;
                        $invItem1 = "INSERT INTO `erp_stocktransfer_item`
                                SET
                                `transfer_id`='$transfer_id',
                                `item_id`='$itemId',
                                `itemCode`='" . $itemCode . "',
                                `itemName`='" . $itemName . "',
                                `qty`='" . $qty2 . "',
                                `uom`='" . $uom . "',
                                `createdBy`='" . $created_by . "',
                                `updatedBy`='" . $updated_by . "'";

                        $itemIns = queryInsert($invItem1);
                        if ($itemIns['status'] == 'success') {
                            $return['status'] = "success";
                            $return['message'] = "Item Insert Success!";

                            $auditTrail['action_data']['Item Details'][$itemCode]['so_Item_id'] = $transfer_id;
                            $auditTrail['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                            $auditTrail['action_data']['Item Details'][$itemCode]['itemName'] = $itemName;
                            $auditTrail['action_data']['Item Details'][$itemCode]['qty'] = $qty2;
                            $auditTrail['action_data']['Item Details'][$itemCode]['uom'] = $uom;

                            $item_price_query = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId AND `location_id`=$location_id");

                            $itemPrice = $item_price_query["data"]["movingWeightedPrice"] ?? 0;

                            $listItem[$booktophykey]["price"] = $itemPrice * $qty;

                            foreach ($selStockLog['data'] as $stockLogKey => $logdata) {
                                if ($itemSellType == 'CUSTOM') {

                                    // console($filteredBatchSelection);
                                    // echo '********************************';
                                    // $explodes = explode('_', $logdata['logRef']);
                                    // $logRef = $explodes[0];
                                    // $slocation = $explodes[1];
                                    $logRef = $logdata['logRef'];
                                    $keysval = $logdata['logRefConcat'];
                                    $usedQuantity = $filteredBatchSelection[$keysval];
                                    $bornDate = $logdata['bornDate'];
                                    $storage_location_id = $logdata['storage_location_id'];
                                    $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                                    // $itemPrice = $logdata['itemPrice'];
                                } else {
                                    // if ($qty <= 0) {
                                    //     break;
                                    // }

                                    // $itemPrice = $logdata['itemPrice'];
                                    // $quantity = $logdata['itemQty'];
                                    // $usedQuantity = min($quantity, $qty);
                                    // $qty -= $usedQuantity;
                                    // $explodes = explode('_', $logdata['logRef']);
                                    // $logRef = $explodes[0];
                                    $usedQuantity = $qty2;

                                    $logRef = $logdata['logRef'];
                                    $bornDate = $logdata['bornDate'];
                                    $storage_location_id = $logdata['storage_location_id'];
                                    $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                                    // $itemPrice = $logdata['itemPrice'];
                                }
                                $stockclobj = "1";

                                // if (isset($POST["accImpact"])) {
                                $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                        SET 
                                            companyId = '" . $company_id . "',
                                            branchId = '" . $branch_id . "',
                                            locationId = '" . $location_id . "',
                                            parentId='" . $transfer_id . "',
                                            storageLocationId = '" . $storage_location_id . "',
                                            storageType ='" . $storageLocationTypeSlug . "',
                                            itemId = '" . $itemId . "',
                                            itemQty = '" . $usedQuantity * $stockclobj . "',
                                            itemUom = '" . $uom . "',
                                            itemPrice = '" . $itemPrice . "',
                                            refActivityName='CONSUMPTION(BOOK-PHYSICAL)',
                                            logRef = '" . $logRef . "',
                                            refNumber='" . $documentNo . "',
                                            bornDate='" . $bornDate . "',
                                            postingDate='" . $postingDate . "',
                                            createdBy = '" . $created_by . "',
                                            updatedBy = '" . $updated_by . "'";

                                $insStockreturn1 = queryInsert($insStockSummary1);
                                // } else {
                                //     $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                //     SET 
                                //         companyId = '" . $company_id . "',
                                //         branchId = '" . $branch_id . "',
                                //         locationId = '" . $location_id . "',
                                //         storageLocationId = '" . $storage_location_id . "',
                                //         storageType ='" . $storageLocationTypeSlug . "',
                                //         itemId = '" . $itemId . "',
                                //         itemQty = '" . $usedQuantity * $stockclobj . "',
                                //         itemUom = '" . $uom . "',
                                //         itemPrice = '" . $itemPrice . "',
                                //         refActivityName='CONSUMPTION(BOOK-PHYSICAL)',
                                //         logRef = '" . $logRef . "',
                                //         refNumber='" . $documentNo . "',
                                //         bornDate='" . $bornDate . "',
                                //         postingDate='" . $postingDate . "',
                                //         createdBy = '" . $created_by . "',
                                //         updatedBy = '" . $updated_by . "'";

                                //     $insStockreturn1 = queryInsert($insStockSummary1);
                                // }


                                $returnData['insStockreturn1'][] = $insStockreturn1;
                            }
                            if ($manualbatchselectionQty > 0 && !empty($manualbatchselectionSL)) {
                                $manualBatchNumber = $item['manualbatchselection']['batchNumber'] ? $item['manualbatchselection']['batchNumber'] : "ST" . time();
                                $manualBatchDate = $item['manualbatchselection']['bornDate'] ? $item['manualbatchselection']['bornDate'] : date('Y-m-d H:i:s');

                                $btachData = queryGet("SELECT logRef,storageType, DATE_FORMAT(bornDate, '%Y-%m-%d') AS bornDate FROM erp_inventory_stocks_log WHERE logRef = '" . $manualBatchNumber . "' ORDER BY bornDate ASC LIMIT 1");

                                $explodessl = explode('|', $manualbatchselectionSL);
                                $slId = $explodessl[0];
                                $storageType = $explodessl[1];

                                if ($btachData['status'] == 'success') {
                                    $refNumber = $documentNo;
                                }

                                // if (isset($POST["accImpact"])) {
                                $insStockSummaryManual = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                    SET 
                                        companyId = '" . $company_id . "',
                                        branchId = '" . $branch_id . "',
                                        locationId = '" . $location_id . "',
                                        parentId='" . $transfer_id . "',
                                        storageLocationId = '" . $slId . "',
                                        storageType ='" . $storageType . "',
                                        itemId = '" . $itemId . "',
                                        itemQty = '" . $manualbatchselectionQty . "',
                                        itemUom = '" . $uom . "',
                                        itemPrice = '" . $itemPrice . "',
                                        refActivityName='CONSUMPTION(BOOK-PHYSICAL)',
                                        logRef = '" . $manualBatchNumber . "',
                                        refNumber='" . $documentNo . "',
                                        bornDate='" . $manualBatchDate . "',
                                        postingDate='" . $postingDate . "',
                                        createdBy = '" . $created_by . "',
                                        updatedBy = '" . $updated_by . "'";

                                $insStockreturnmanual = queryInsert($insStockSummaryManual);
                                // } else {
                                // $insStockSummaryManual = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                // SET 
                                //     companyId = '" . $company_id . "',
                                //     branchId = '" . $branch_id . "',
                                //     locationId = '" . $location_id . "',
                                //     storageLocationId = '" . $slId . "',
                                //     storageType ='" . $storageType . "',
                                //     itemId = '" . $itemId . "',
                                //     itemQty = '" . $manualbatchselectionQty . "',
                                //     itemUom = '" . $uom . "',
                                //     itemPrice = '" . $itemPrice . "',
                                //     refActivityName='CONSUMPTION(BOOK-PHYSICAL)',
                                //     logRef = '" . $manualBatchNumber . "',
                                //     refNumber='" . $documentNo . "',
                                //     bornDate='" . $manualBatchDate . "',
                                //     postingDate='" . $postingDate . "',
                                //     createdBy = '" . $created_by . "',
                                //     updatedBy = '" . $updated_by . "'";

                                // $insStockreturnmanual = queryInsert($insStockSummaryManual);
                                // }


                                $returnData['insStockreturn1'][] = $insStockreturnmanual;
                            }
                        } else {
                            $returnData['status'] = "warning";
                            $returnData['message1'] = "somthing went wrong! 31";
                            $returnData['itemIns'] = $itemIns;
                            $flug++;
                        }
                    }
                    if ($flug == 0) {
                        $accsttts = ' Without Accounting';
                        // if (isset($POST["accImpact"])) {
                        $dataInputData = [
                            "BasicDetails" => [
                                "documentNo" => $documentNo, // Invoice Doc Number
                                "documentDate" => $postingDate, // Invoice number
                                "postingDate" => $postingDate, // current date
                                "grnJournalId" => '',
                                "reference" => $documentNo, // grn code
                                "remarks" => "Stock Posting - Book To Physical - " . $documentNo,
                                "journalEntryReference" => "StockPosting"
                            ],
                            "FGItems" => $listItem
                        ];
                        $dataPostingObjPhysical = $this->stockDifferenceBookToPhysicalAccountingPosting($dataInputData, "stockDifferenceBookToPhysical", $transfer_id);
                        $accsttts = ' With Accounting';
                        $jounalId = $dataPostingObjPhysical["journalId"] ?? 0;

                        $upSql = "UPDATE `erp_stocktransfer` SET `journal_id` = $jounalId WHERE `transfer_id` = $transfer_id";
                        $upSqlObj = queryUpdate($upSql);
                        // need failed accounting

                        // }

                        $return["status"] = "success";
                        $return["message"] = "Stock Transfer Successfull," . $accsttts;
                        $return["documentNo"] = $documentNo;
                        $return["returnData"] = $returnData;
                        $return["accounting"] = $dataPostingObjPhysical;
                    } else {
                        $return["status"] = "warning";
                        $return["message"] = "Stock Transferred Failure!";
                        $return["returnData"] = $returnData;
                    }
                }
            } else {

                $return['status'] = "warning";
                $return['message'] = "Stock Transferred Failure!";
            }
        } else {

            $return['status'] = "warning";
            $return['message'] = "Item Not selected!";
        }
        return $return;
    }

    function direct_consumption_snt($POST)
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $updated_by = 'AUTO';
        $created_by = 'AUTO';

        $returnData = [];
        $movement = $POST['movemenrtypesDropdown'];
        $destinationStorageLocation = $POST['destinationStorageLocation'];
        $explodessl = explode('|', $destinationStorageLocation);
        $destination_storage_location_id = $explodessl[0];

        console($POST['creationDate']);
        $postingDate = date('Y-m-d H:i:s', strtotime($POST['creationDate']));
        console($postingDate);
        $documentNo = $POST['documentNo'];

        if (count($POST['listItem']) > 0) {
            $transfersql = "INSERT INTO `erp_stocktransfer`
                                SET
                                `company_id`='" . $company_id . "',
                                `branch_id`='" . $branch_id . "',
                                `location_id`='" . $location_id . "',
                                `documentNo`='" . $documentNo . "',
                                `destinationsl`='" . $destination_storage_location_id . "',
                                `destination_type`='" . $movement . "',
                                `created_by`='" . $created_by . "',
                                `updated_by`='" . $updated_by . "'";

            // $transfersqlQry = queryInsert($transfersql);
            $transfersqlQry['status'] = 'success';

            $transfer_id = $POST['transfer_id'];

            if ($transfersqlQry['status'] == 'success') {
                $transfer_id = $transfersqlQry['insertedId'];
                if ($movement == "book_to_physical") {
                    $listItem = $POST['listItem'];

                    foreach ($listItem as $booktophykey => $item) {
                        $qty = $item['qty'] ?? 0;
                        $itemMap = $item['itemMap'] ?? 0;
                        $listItem[$booktophykey]["price"] = $itemMap * $qty;
                    }

                    $flug = 0;

                    if ($flug == 0) {
                        $dataInputData = [
                            "BasicDetails" => [
                                "documentNo" => $documentNo, // Invoice Doc Number
                                "documentDate" => $postingDate, // Invoice number
                                "postingDate" => $postingDate, // current date
                                "grnJournalId" => '',
                                "reference" => $documentNo, // grn code
                                "remarks" => "Stock Posting - Book To Physical - " . $documentNo,
                                "journalEntryReference" => "StockPosting"
                            ],
                            "FGItems" => $listItem
                        ];
                        console($dataInputData);
                        $dataPostingObjPhysical = $this->stockDifferenceBookToPhysicalAccountingPosting($dataInputData, "stockDifferenceBookToPhysical", $transfer_id, 'AUTO');

                        console($dataPostingObjPhysical);

                        if ($dataPostingObjPhysical["status"] == "success") {
                            $accsttts = ' With Accounting';
                        } else {
                            $accsttts = ' Without Accounting';
                        }


                        $jounalId = $dataPostingObjPhysical["journalId"] ?? 0;

                        $upSql = "UPDATE `erp_stocktransfer` SET `journal_id` = $jounalId WHERE `transfer_id` = $transfer_id";
                        $upSqlObj = queryUpdate($upSql);

                        // }

                        $return["status"] = "success";
                        $return["message"] = "Stock Transfer Successfull," . $accsttts;
                        $return["documentNo"] = $documentNo;
                        $return["returnData"] = $returnData;
                        // $return["accounting"] = $dataPostingObjPhysical;
                    } else {
                        $return["status"] = "warning";
                        $return["message"] = "Stock Transferred Failure!";
                        $return["returnData"] = $returnData;
                    }
                }
            } else {

                $return['status'] = "warning";
                $return['message'] = "Stock Transferred Failure!";
            }
        } else {

            $return['status'] = "warning";
            $return['message'] = "Item Not selected!";
        }
        return $return;
    }

    function transfer_stock($POST)
    {
        //   console($POST);
        // exit();
        global $location_id;
        global $updated_by;
        global $created_by;
        global $company_id;
        global $branch_id;
        $returnData = [];
        $movement = $POST['movemenrtypesDropdown'];
        $destinationStorageLocation = $POST['destinationStorageLocation'];
        $explodessl = explode('|', $destinationStorageLocation);
        $destination_storage_location_id = $explodessl[0];
        $destination_storageLocationTypeSlug = $explodessl[1];

        $postingDate = $POST['creationDate'];
        $documentNo = 'ST' . time() . rand(1111, 9999);
        $reference = $documentNo;

        if (count($POST['listItem']) > 0) {
            $transfersql = "INSERT INTO `erp_stocktransfer`
                                SET
                                `company_id`='" . $company_id . "',
                                `branch_id`='" . $branch_id . "',
                                `location_id`='" . $location_id . "',
                                `documentNo`='" . $documentNo . "',
                                `destinationsl`='" . $destination_storage_location_id . "',
                                `created_by`='" . $created_by . "',
                                `updated_by`='" . $updated_by . "'";

            $transfersqlQry = queryInsert($transfersql);
            if ($transfersqlQry['status'] == 'success') {
                $transfer_id = $transfersqlQry['insertedId'];
                if ($movement == "storage_location") {
                    $listItem = $POST['listItem'];
                    $flug = 0;
                    $invTotalItems = 0;
                    foreach ($listItem as $item) {
                        $invTotalItems++;
                        $itemId = $item['itemId'];
                        $itemCode = $item['itemCode'];
                        $goodsType = $item['goodsType'];
                        $itemName = addslashes($item['itemName']);
                        $qty = $item['qty'] ?? 0;
                        $uom = $item['uom'];

                        $stockQty = $item['stockQty'];
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

                        if ($itemSellType != 'CUSTOM') {
                            $selStockLog = itemQtyStockChecking($itemId, "'rmWhOpen','rmProdOpen','sfgStockOpen', 'fgWhOpen','fgMktOpen'", $itemSellType, '', $postingDate);
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


                            $selStockLog = itemQtyStockChecking($itemId, "'rmWhOpen','rmProdOpen','sfgStockOpen', 'fgWhOpen','fgMktOpen'", 'ASC', "$keysString", $postingDate);
                            // console($selStockLog);
                            // console($filteredBatchSelection);
                            $itemOpenStocks = $selStockLog['sumOfBatches'];
                        }
                        $returnData['insStockreturn3'][] = $selStockLog;
                        if ($itemOpenStocks >= $qty) {
                            $invItem1 = "INSERT INTO `erp_stocktransfer_item`
                             SET
                             `transfer_id`='$transfer_id',
                             `item_id`='$itemId',
                             `itemCode`='" . $itemCode . "',
                             `itemName`='" . $itemName . "',
                             `qty`='" . $qty . "',
                             `uom`='" . $uom . "',
                             `createdBy`='" . $created_by . "',
                             `updatedBy`='" . $updated_by . "'";

                            $itemIns = queryInsert($invItem1);
                            if ($itemIns['status'] == 'success') {
                                $return['status'] = "success";
                                $return['message'] = "Item Insert Success!";

                                $auditTrail['action_data']['Item Details'][$itemCode]['so_Item_id'] = $transfer_id;
                                $auditTrail['action_data']['Item Details'][$itemCode]['itemCode'] = $itemCode;
                                $auditTrail['action_data']['Item Details'][$itemCode]['itemName'] = $itemName;
                                $auditTrail['action_data']['Item Details'][$itemCode]['qty'] = $qty;
                                $auditTrail['action_data']['Item Details'][$itemCode]['uom'] = $uom;


                                foreach ($selStockLog['data'] as $stockLogKey => $logdata) {
                                    if ($itemSellType == 'CUSTOM') {

                                        // console($filteredBatchSelection);
                                        // echo '********************************';
                                        // $explodes = explode('_', $logdata['logRef']);
                                        // $logRef = $explodes[0];
                                        // $slocation = $explodes[1];
                                        $logRef = $logdata['logRef'];
                                        $keysval = $logdata['logRefConcat'];
                                        $usedQuantity = $filteredBatchSelection[$keysval];
                                        $bornDate = $logdata['bornDate'];
                                        $storage_location_id = $logdata['storage_location_id'];
                                        $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                                        $itemPrice = $logdata['itemPrice'];
                                    } else {
                                        if ($qty <= 0) {
                                            break;
                                        }

                                        $itemPrice = $logdata['itemPrice'];
                                        $quantity = $logdata['itemQty'];
                                        $usedQuantity = min($quantity, $qty);
                                        $qty -= $usedQuantity;
                                        // $explodes = explode('_', $logdata['logRef']);
                                        // $logRef = $explodes[0];

                                        $logRef = $logdata['logRef'];
                                        $bornDate = $logdata['bornDate'];
                                        $storage_location_id = $logdata['storage_location_id'];
                                        $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
                                        $itemPrice = $logdata['itemPrice'];
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
                                                    itemPrice = '" . $itemPrice . "',
                                                    refActivityName='STOCK-MVMT',
                                                    logRef = '" . $logRef . "',
                                                    refNumber='" . $documentNo . "',
                                                    bornDate='" . $bornDate . "',
                                                    postingDate='" . $postingDate . "',
                                                    createdBy = '" . $created_by . "',
                                                    updatedBy = '" . $updated_by . "'";

                                    $insStockreturn1 = queryInsert($insStockSummary1);

                                    $insStockSummary2 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                                                SET 
                                                    companyId = '" . $company_id . "',
                                                    branchId = '" . $branch_id . "',
                                                    locationId = '" . $location_id . "',
                                                    storageLocationId = '" . $destination_storage_location_id . "',
                                                    storageType ='" . $destination_storageLocationTypeSlug . "',
                                                    itemId = '" . $itemId . "',
                                                    itemQty = '" . $usedQuantity . "',
                                                    itemUom = '" . $uom . "',
                                                    itemPrice = '" . $itemPrice . "',
                                                    refActivityName='STOCK-MVMT',
                                                    logRef = '" . $logRef . "',
                                                    refNumber='" . $documentNo . "',
                                                    bornDate='" . $bornDate . "',
                                                    postingDate='" . $postingDate . "',
                                                    createdBy = '" . $created_by . "',
                                                    updatedBy = '" . $updated_by . "'";

                                    $insStockreturn2 = queryInsert($insStockSummary2);
                                    // console($insStockreturn1);

                                    $returnData['insStockreturn1'][] = $insStockreturn1;
                                    $returnData['insStockreturn2'][] = $insStockreturn2;
                                }
                            } else {
                                $returnData['status'] = "warning";
                                $returnData['message1'] = "somthing went wrong! 31";
                                $returnData['itemIns'] = $itemIns;
                                $flug++;
                            }
                        } else {
                            $returnData['status'] = "warning";
                            $returnData['message2'] = "Stock quantity issue - " . $item['itemCode'];
                            $flug++;
                        }
                    }
                    if ($flug == 0) {
                        $return["status"] = "success";
                        $return["message"] = "Stock Transfer Successfull";
                        $return["documentNo"] = $documentNo;
                        $return["returnData"] = $returnData;
                    } else {
                        $return["status"] = "warning";
                        $return["message"] = "Stock Transferred Failure!";
                        $return["returnData"] = $returnData;
                    }
                } else {

                    $items = $POST['item'];
                    // $to_sl = $POST['item_sl'];
                    $fromItem = [];
                    $ded_price_total = 0;
                    $formItemName = '';

                    foreach ($items as $item) {
                        //console($item['']);
                        $itemid = $item['name'];
                        $qty = $item['quantity'];

                        $uom = $item['uom'];
                        $itemsl = $item['storagelocation'];
                        $sql = queryGet("SELECT *," . $itemsl . " as max FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemid AND `location_id`=$location_id ");
                        $from_total =  $sql["data"]["itemTotalQty"];
                        $new_from_total = $from_total - $qty;
                        $item_mwp = $sql["data"]["movingWeightedPrice"];
                        $sl_max = $sql["data"]["max"];

                        // if ($sl_max < $qty) {
                        //     $returnData['status'] = "warning";
                        //     $returnData['message'] = "Quanity greater than maximum limit";
                        //     $returnData['data'] = [];
                        //     return $returnData;
                        // } else {
                        $new_from_sl_qty = $sl_max - $qty;
                        // exit();
                        $to_item = $POST['item_name'];
                        $to_item_sl = $POST['item_sl'];
                        $sql_to = queryGet("SELECT *," . $to_item_sl . " as to_item_prev_qty FROM `erp_inventory_stocks_summary` WHERE `itemId`=$to_item AND `location_id`=$location_id ");
                        $to_total =  $sql_to["data"]["itemTotalQty"];
                        $new_to_total = $to_total + $qty;
                        $prev_price = $sql_to["data"]["movingWeightedPrice"];
                        $prev_qty = $sql_to["data"]["to_item_prev_qty"];
                        $new_to_qty = $qty + $prev_qty;
                        $new_mwp = (($prev_price * $prev_qty) + ($qty * $item_mwp)) / $new_to_qty;
                        $from_item = $item['name'];
                        $from_item_uom = $item['uom'];
                        $from_item_sl = $item['storageLocation'];
                        $from_item_quantity = $item['quantity'];
                        $itemsl;
                        $update_from = queryUpdate("UPDATE `erp_inventory_stocks_summary` SET `" . $itemsl . "`='$new_from_sl_qty',`updatedBy`='" . $updated_by . "',`itemTotalQty`='$new_from_total' WHERE `itemId`=$itemid AND `location_id`=$location_id ");

                        $update_to = queryUpdate("UPDATE `erp_inventory_stocks_summary` SET `" . $to_item_sl . "`='$new_to_qty',`movingWeightedPrice`='" . $new_mwp . "',`updatedBy`='" . $updated_by . "', `itemTotalQty`='$new_to_total' WHERE `itemId`=$to_item AND `location_id`=$location_id ");
                        //  exit();
                        $qty;
                        $ded_price = $item_mwp * $qty;
                        $ded_price_total = $ded_price_total + $ded_price;
                        $add_prie = $prev_price * $qty;
                        $log_insert_ded = queryInsert("INSERT INTO `erp_inventory_stocks_log` SET `itemId`=$itemid, `itemQty`='" . -$qty . "',`storageType`='" . $itemsl . "',`itemUom`=$uom,`itemPrice`='" . -$ded_price . "',`logRef`='stock transfer-" . $documentNo . "',`createdBy`='" . $created_by . "',`updatedBy`='" . $updated_by . "',`companyId`=$company_id,`locationId`=$location_id,`branchId`=$branch_id,`storageLocationId`=1 ");

                        $log_insert_add = queryInsert("INSERT INTO `erp_inventory_stocks_log` SET `itemId`=$to_item, `itemQty`='" . $qty . "',`storageType`='" . $to_item_sl . "',`itemUom`=$uom,`itemPrice`='" . $add_prie . "',`logRef`='stock transfer-" . $documentNo . "',`createdBy`='" . $created_by . "',`updatedBy`='" . $updated_by . "',`companyId`=$company_id,`locationId`=$location_id,`branchId`=$branch_id,`storageLocationId`=1 ");
                        //  exit();

                        // $returnData = $update_to;
                        // return $returnData;
                        $formitemsql = queryGet("SELECT * FROM `erp_inventory_items` WHERE `itemId`=$itemid AND `company_id`=$company_id ");
                        $formItemName .= $formitemsql['data']['itemName'] . ", ";
                        $fromItem[] = [
                            "itemName" => $formitemsql['data']['itemName'], // SubGl Name
                            "itemCode" => $formitemsql['data']['itemCode'], // Sub GL code
                            "parentGlId" => $formitemsql['data']['parentGlId'], // GL id
                            "price" => $ded_price
                        ];
                    }


                    //-----------------------------Stock Transfer ACC Start----------------

                    $toitemsql = queryGet("SELECT * FROM `erp_inventory_items` WHERE `itemId`=$to_item AND `company_id`=$company_id ");

                    $priceDiffrence = $ded_price_total - $add_prie;

                    $transferInputData = [
                        "BasicDetails" => [
                            "documentNo" => $documentNo, // Transfer Doc Number
                            "postingDate" => $postingDate, // Transfer number
                            "postingDate" =>  $postingDate, // current date
                            "reference" => $reference, // reference
                            "remarks" => "" . $qty . " Stock Transfer to - " . $toitemsql['data']['itemName'] . " form - " . $formItemName,
                            "journalEntryReference" => "StockTransfer",
                            "priceDiffrence" => $priceDiffrence, // priceDiffrence
                        ],
                        "fromItem" =>  $fromItem,
                        "toItem" => [
                            "itemName" => $toitemsql['data']['itemName'], // SubGl Name
                            "itemCode" => $toitemsql['data']['itemCode'], // Sub GL code
                            "parentGlId" => $toitemsql['data']['parentGlId'], // GL id
                            "price" => $add_prie
                        ]
                    ];
                    //console($ivPostingInputData);
                    $trnasResponce = $this->transferAccountingPosting($transferInputData, 'Stocktransportwithoutprofit', '0');

                    //-----------------------------Stock Transfer ACC End----------------

                    $returnData['status'] = "success";
                    $returnData['message'] = "Stock Transferred Successfully!";
                    $returnData['trnasResponce'] = $trnasResponce;
                    return $returnData;
                }
            } else {

                $return['status'] = "warning";
                $return['message'] = "Stock Transferred Failure!";
            }
        } else {

            $return['status'] = "warning";
            $return['message'] = "Item Not selected!";
        }
        return $return;
    }


    function getUOM()
    {

        global $dbCon;
        global $company_id;
        $returnData = [];
        $selectSql = "SELECT * FROM `erp_inventory_mstr_uom` WHERE (`companyId`=$company_id OR `companyId`=0) AND `uomStatus`='active'";

        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = mysqli_num_rows($res) . " records found.";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }
    function addUOM($POST)
    {
        global $dbCon;
        $returnData = [];
        global $company_id;
        global $created_by;
        // $isValidate = validate($POST, [
        //     "uomName" => "required",
        //     "uomDesc" => "required",

        // ], [
        //     "uomName" => "Enter UOM name",
        //     "uomDesc" => "Enter UOM desc",

        // ]);

        // if ($isValidate["status"] != "success") {
        //     $returnData['status'] = "warning";
        //     $returnData['message'] = "Invalid form inputes";
        //     $returnData['errors'] = $isValidate["errors"];
        //     return $returnData;
        // }

        //  $companyId = $INPUTS["companyId"];
        $uomName = $POST["uomName"];
        $uomDesc = $POST["uomDesc"];
        $uomType = $POST["uomType"];


        $returnData = queryInsert("INSERT INTO `erp_inventory_mstr_uom` SET `companyId`=$company_id,`uomName`='" . $uomName . "',`uomDesc`='" . $uomDesc . "',`uomCreatedBy`='" . $created_by . "',`uomUpdatedBy`='" . $created_by . "',`uomStatus`='active',`uomType`='" . $uomType . "'");
        // $returnData = queryInsert($sql);



        return $returnData;
    }

    function addHSN($POST)
    {

        global $dbCon;
        $returnData = [];
        global $company_id;
        global $created_by;
        $isValidate = validate($POST, [
            "hsnCode" => "required",
            "hsnDesc" => "required",
            "hsnRate" => "required",


        ], [
            "hsnCode" => "Enter UOM name",
            "hsnDesc" => "Enter UOM desc",
            "hsnRate" => "Enter HSN rate",


        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        //  $companyId = $INPUTS["companyId"];
        $hsnCode = $POST["hsnCode"];
        $hsnDesc = $POST["hsnDesc"];
        $hsnRate = $POST["hsnRate"];
        $hsnPublic  = $POST["hsnPublic"];


        $sql = "INSERT INTO `erp_hsn_code` SET `hsnCode`='" . $hsnCode . "',`hsnDescription`='" . $hsnDesc . "',`isPublic`=$hsnPublic,`taxPercentage`= '" . $hsnRate . "'";
        $returnData = queryInsert($sql);

        return $returnData;
    }

    function asset_use($POST)
    {
        global $dbCon, $company_id, $location_id, $branch_id, $created_by, $updated_by;

        $returnData = [];

        // Validate required fields
        $isValidate = validate($POST, [
            "assetCode" => "required",
            "rcvDate" => "required",
            "qty" => "required",
            "uom" => "required",
            "rate" => "required",
            "total" => "required",
        ]);

        if (!$isValidate) {
            return ["status" => false, "message" => "Validation failed", "errors" => $isValidate];
        }

        // Extract variables from POST data
        $asset_id = $POST['item_id'];
        $assetCode = $POST['assetCode'];
        $assetName = $POST['assetName'];
        $rcvDate = $POST['rcvDate'];
        $qty = $POST['qty'];
        $uom = $POST['uom'];
        $rate = $POST['rate'];
        $total = $POST['total'];
        $use_date = $POST['useDate'];
        $batchno = $POST['batchno'];
        $stockLogId = $POST['stockLogId'];
        $scrap = $POST['scrap'];
        $dep_percentage = $POST['dep_percentage'];
        $scrap_val = $POST['scrap_val'];
        $dep_schedule = $POST['dep_schedule'];
        $storagelocationid = $POST['storageLocationId'];
        $storageType = $POST['storageType'];
        $costcenter = $POST['costcenter'];
        $equip = $POST['equiplist'];
        $item_ac_price = $POST['price1'];



        $costCenterValue = '-';

        if ($costcenter != 0 && $costcenter != NULL) {

            $costCenterValue = queryGet("SELECT CostCenter_code FROM `" . ERP_COST_CENTER . "` WHERE CostCenter_status = 'active' AND branch_id = $branch_id AND company_id = $company_id AND CostCenter_id = " . $costcenter)['data']['CostCenter_code'];
        }
        // Generate a lot number
        $lot = rand(100, 1000);

        // Insert into `erp_asset_use` table
        $sql = "
        INSERT INTO `erp_asset_use` 
        SET 
            `lot_no` = '$lot',
            `rcv_date` = '$rcvDate',
            `asset_id` = $asset_id,
            `asset_code` = '$assetCode',
            `assetName` = '$assetName',
            `qty` = $qty,
            `uom` = $uom,
            `rate` = $rate,
            `total_value` = $total,
            `use_date` = '$use_date',
            `stock_log_id` = '$stockLogId',
            `batch_number`='$batchno',
            `cost_center_id`='$costcenter',
            `scrap_rate` = '$scrap',
            `scrap_value` = '$scrap_val',
            `depreciation_amount` = 0,
            `depreciated_asset_value` = '$total',
            `dep_percentage` = '$dep_percentage',
            `company_id` = $company_id,
            `location_id` = $location_id,
            `branch_id` = $branch_id,
            `depreciation_schedule` = '$dep_schedule',
            `created_by` = '$created_by',
            `updated_by` = '$updated_by'
    ";

        $returnData = queryInsert($sql);

        // If asset use insert was successful, proceed with stock log insertion
        if ($returnData['status'] === "success") {
            $invNo = $returnData['insertedId'];

            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';   //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'	 //	Add/Update/Deleted
            $auditTrail['basicDetail']['table_name'] = 'erp_asset_use';
            $auditTrail['basicDetail']['column_name'] = 'asset_id';  //Primary Key column
            $auditTrail['basicDetail']['document_id'] = $asset_id;   // Primary Key
            $auditTrail['basicDetail']['party_id'] = 0;
            $auditTrail['basicDetail']['document_number'] = $assetCode;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Asset Put to Use';   // Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';   //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($sql);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            foreach ($equip as $eq) {

                $sql2 = "
        INSERT INTO `erp_equip_details` 
        SET 
            `company_id` = $company_id,
            `location_id` = $location_id,
            `branch_id` = $branch_id,
            `batch_no`='$batchno',
            `equip_no`='$eq',
            `item_id`=$asset_id,
            `puttouse_id`=$invNo,
            `created_by` = '$created_by',
            `updated_by` = '$updated_by'
    ";
                $returnData2 = queryInsert($sql2);

                if (!empty($batchno) || !empty($eq)) {
                    $sl = 1;
                    $auditTrail['action_data']['Equip Details'][$sl] = [
                        'Batch_No' => $batchno,
                        'Equip_No' => $eq
                    ];
                    $sl++;
                }
            }


            $invoice_date = date('Y-m-d');

            // Insert stock log for the asset use
            $insStockSummary1 = "
            INSERT INTO `erp_inventory_stocks_log` 
            SET 
                `companyId` = '$company_id',
                `branchId` = '$branch_id',
                `locationId` = '$location_id',
                `parentId`='$invNo',
                `storageLocationId` = '$storagelocationid',
                `storageType` = '$storageType',
                `itemId` = '$asset_id',
                `itemQty` = " . ($qty * -1) . ",
                `itemUom` = '$uom',
                `itemPrice` = $item_ac_price,
                `refActivityName` = 'Put To Use',
                `logRef` = '$batchno',
                `refNumber` = '$invNo',
                `bornDate` = '$invoice_date',
                `postingDate` = '$invoice_date',
                `createdBy` = '$created_by',
                `updatedBy` = '$updated_by'
        ";

            $insStockreturn1 = queryInsert($insStockSummary1);

            // Check if stock log insertion was successful
            if ($insStockreturn1['status'] !== "success") {
                return ["status" => 'false', "message" => "Failed to log asset use in inventory"];
            } else {
                $auditTrail['action_data']['Basic Detail']['Lot_No'] = $lot;
                $auditTrail['action_data']['Basic Detail']['Receiving_Date'] = formatDateWeb($rcvDate);
                $auditTrail['action_data']['Basic Detail']['Asset_Code'] = $assetCode;
                $auditTrail['action_data']['Basic Detail']['Asset_Name'] = $assetName;
                $auditTrail['action_data']['Basic Detail']['Qty'] = decimalQuantityPreview($qty);
                $auditTrail['action_data']['Basic Detail']['UOM'] = getUomDetail($uom)['data']['uomName'];
                $auditTrail['action_data']['Basic Detail']['Rate'] = $rate;
                $auditTrail['action_data']['Basic Detail']['Total_Value'] = decimalValuePreview($total);
                $auditTrail['action_data']['Basic Detail']['Use_Date'] = formatDateWeb($use_date);
                $auditTrail['action_data']['Basic Detail']['Batch_Number'] = $batchno;
                $auditTrail['action_data']['Basic Detail']['Cost_Center'] = $costCenterValue;
                $auditTrail['action_data']['Basic Detail']['Scrap_Rate'] = decimalQuantityPreview($scrap) . '%';
                $auditTrail['action_data']['Basic Detail']['Scrap_Value'] = $scrap_val;
                $auditTrail['action_data']['Basic Detail']['Depreciated_Asset_Value'] = decimalValuePreview($total);
                $auditTrail['action_data']['Basic Detail']['Depreciation_Percentage'] = decimalQuantityPreview($dep_percentage);
                $auditTrail['action_data']['Basic Detail']['Depreciation_Schedule'] = $dep_schedule;
                $auditTrail['action_data']['Basic Detail']['Created_By'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Basic Detail']['Updated_By'] = getCreatedByUser($updated_by);

                $auditTrail['action_data']['Stock Summary']['Storage_Type'] = $storageType;
                $auditTrail['action_data']['Stock Summary']['Item_Qty'] = decimalQuantityPreview($qty);
                $auditTrail['action_data']['Stock Summary']['Item_Uom'] = getUomDetail($uom)['data']['uomName'];
                $auditTrail['action_data']['Stock Summary']['Item_Price'] = decimalValuePreview($item_ac_price);
                $auditTrail['action_data']['Stock Summary']['Ref_Activity_Name'] = 'Put To Use';
                $auditTrail['action_data']['Stock Summary']['Log_Ref'] = $batchno;
                $auditTrail['action_data']['Stock Summary']['Ref_Number'] = $invNo;
                $auditTrail['action_data']['Stock Summary']['Born_Date'] = formatDateWeb($invoice_date);
                $auditTrail['action_data']['Stock Summary']['Posting_Date'] = formatDateWeb($invoice_date);

                $auditTrailreturn = generateAuditTrail($auditTrail);
            }

            // If both insertions succeed, return success
            echo json_encode([
                "status" => 'Success',
                "message" => "Successfully Put To Use",
                "insertedId" => $returnData['insertedId'],
            ]);
            exit;
        }
        echo json_encode(["status" => 'false', "message" => 'Something Went Wrong!']);
        exit;

        // return $returnData;
    }

    function createManualDepreciation($POST)
    {
        global $dbCon;
        $returnData = [];
        global $company_id;
        global $location_id;
        global $branch_id;
        global $created_by;
        // console($POST);
        foreach ($POST['asset'] as $asset) {
            // console($asset);
            $buying_val = $asset['asset_total'];
            $asset_use_id = $asset['asset_use_id'];
            $dep_on_val = $asset['depreciation_on'];
            $asset_id = $asset['asset_id'];
            $dep_rate = $asset['depreciation_percentage'];
            $dep_val = $asset['depreciated_val'];
            $asset_new_val = $asset['after_dep_val'];
            $scrap = $asset['scrap'];
            // $tody = "2025-09-01";
            $method = $asset['method'];
            $depreciation_date = isset($asset['posting_date']) ? $asset['posting_date'] : null;
            $company_detaisl = queryGet("SELECT `depreciation_schedule` FROM `erp_companies` WHERE `company_id`='$company_id'");
            $dep_schedule = $company_detaisl['data']['depreciation_schedule'];
            if ($dep_schedule == "yearly") {
                if ($depreciation_date) {
                    $givenDate = new DateTime($depreciation_date);

                    // Determine the financial year
                    $year = (int) $givenDate->format('Y');
                    $month = (int) $givenDate->format('m');

                    if ($month >= 4) {
                        // If the month is April or later, move to the next financial year
                        $year += 1;
                    } else {
                        // If before April, move to the next financial year
                        $year = $year;
                    }

                    // Set date to March 31 of the next financial year
                    $givenDate->setDate($year, 3, 31);
                    $formattedDate = $givenDate->format('Y-m-d');
                }
            } else {
                if ($depreciation_date) {
                    $givenDate = new DateTime($depreciation_date);
                    $givenDate->modify('last day of this month'); // No need to store in another variable
                    $formattedDate = $givenDate->format('Y-m-d');
                }
            }

            $posting_date = $formattedDate;


            $assUsesql = queryGet("SELECT * FROM `erp_asset_use` WHERE company_id = $company_id AND `branch_id`=$branch_id AND `location_id`=$location_id AND `use_asset_id`= $asset_use_id");

            $assDepsql = queryGet("SELECT * FROM `erp_asset_depreciation` WHERE company_id = $company_id AND `branch_id`=$branch_id AND `location_id`=$location_id ORDER BY depreciation_code DESC LIMIT 1");

            $lstCode = $assDepsql['data']['depreciation_code'] ?? '';
            $depreciation_code = getDepreciationSerialNumber($lstCode);
            $update = queryUpdate("UPDATE `erp_asset_use` SET `depreciation_amount`= depreciation_amount+'" . $dep_val . "',`depreciated_asset_value`='" . $asset_new_val . "' WHERE `use_asset_id`= $asset_use_id");

            $insert = queryInsert("INSERT INTO `erp_asset_depreciation` SET `depreciation_code`='" . $depreciation_code . "',`method`='" . $method . "',`depreciation_date` = '" . $depreciation_date . "',`posting_date`='" . $posting_date . "',`asset_use_id`='" . $asset_use_id . "',`asset_id`=$asset_id,`asset_value`='" . $buying_val . "',`depreciation_on_value`='" . $dep_on_val . "',`depreciated_value`=$dep_val,`depreciation_value`=$asset_new_val,`company_id`=$company_id,`branch_id`=$branch_id,`location_id`=$location_id,`created_by`='" . $created_by . "',`updated_by` = '" . $created_by . "'");
            $query = "INSERT INTO `erp_asset_depreciation` 
            SET `depreciation_code` = '" . $depreciation_code . "',
                `posting_date` = '" . $posting_date . "',
                `depreciation_date` = '" . $depreciation_date . "',
                `asset_use_id` = " . $asset_use_id . ",
                `asset_id` = " . $asset_id . ",
                `asset_value` = " . $buying_val . ",
                `depreciation_on_value` = " . $dep_on_val . ",
                `depreciated_value` = " . $dep_val . ",
                `depreciation_value` = " . $asset_new_val . ",
                `company_id` = " . $company_id . ",
                `branch_id` = " . $branch_id . ",
                `location_id` = " . $location_id . ",
                `created_by` = " . $created_by . ",
                `updated_by` = " . $created_by . "";

            $depInputData = [
                "BasicDetails" => [
                    "documentNo" => $depreciation_code, // Transfer Doc Number
                    "documentDate" => date('Y-m-d'), // Transfer number
                    "postingDate" => $posting_date, // current date
                    "reference" => $depreciation_code, // reference
                    "remarks" => "Asset depreciation for - " . $assUsesql['data']['assetName'] . " (" . $assUsesql['data']['asset_code'] . ")",
                    "journalEntryReference" => "assetdepreciation"
                ],
                "asset" => [
                    "itemName" => $assUsesql['data']['assetName'], // SubGl Name
                    "itemCode" => $assUsesql['data']['asset_code'], // Sub GL code
                    "amount" => $dep_val
                ]
            ];

            // depreciation
            $trnasResponce = $this->depreciationAccountingPosting($depInputData, 'Depreciation', $insert['insertedId']);
            if ($trnasResponce['status'] == "success") {
                queryUpdate("UPDATE `erp_asset_depreciation` SET `journal_id`= '" . $trnasResponce['journalId'] . "' WHERE asset_depreciation_id=" . $insert['insertedId'] . "");
            }
        }


        if ($update['status'] == "success") {
            if ($insert['status'] == "success") {
                $returnData['status'] = "success";
                $returnData['message'] = "Depreciated Successfully";
                $returnData['trnasResponce'] = $trnasResponce;
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Depreciation Unsuccessful1";
                $returnData['trnasResponce'] = $trnasResponce;
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Depreciation Unsuccessful2";
            $returnData['trnasResponce'] = $trnasResponce;
        }

        return $returnData;
    }

    function depreciationReversel($POST)
    {
        global $dbCon;
        $returnData = [];
        global $company_id;
        global $location_id;
        global $branch_id;
        global $created_by;

        $assDepsql = queryGet("SELECT * FROM `erp_asset_depreciation` WHERE company_id = $company_id AND `branch_id`=$branch_id AND `location_id`=$location_id ORDER BY depreciation_code DESC LIMIT 1");

        if ($assDepsql['status'] == "success") {
            $returnData['status'] = "success";
            $returnData['message'] = "Depreciated Successfully";
            $returnData['assDepsql'] = $assDepsql;
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Depreciation Unsuccessful";
            $returnData['assDepsql'] = $assDepsql;
        }

        return $returnData;
    }

    function findGoodGrpDetailById($goodGroupId)
    {
        global $company_id;
        global $location_id;
        global $branch_id;
        global $created_by;
        $sql = "SELECT goodGroup.* FROM `erp_inventory_mstr_good_groups` AS goodGroup WHERE companyId = $company_id AND goodGroupId=$goodGroupId;";
        $res = queryGet($sql);

        if ($res['status'] == "success") {
            return $res['data'];
        } else {
            return null;
        }
    }

    function getStoragelocationValue($id)
    {
        global $company_id;
        $getStorageLocation = queryGet("SELECT loc.storage_location_id, loc.storage_location_code, loc.storage_location_name, loc.storage_location_type, loc.storageLocationTypeSlug, warh.warehouse_id, warh.warehouse_code, warh.warehouse_name FROM erp_storage_location AS loc LEFT JOIN erp_storage_warehouse AS warh ON loc.warehouse_id = warh.warehouse_id WHERE loc.company_id=$company_id AND loc.storage_location_id = $id", false);

        $getStorageLocationArr = [];
        if ($getStorageLocation['numRows'] > 0) {
            $getStorageLocationArr = $getStorageLocation;
        }

        return $getStorageLocationArr['data'];
    }


    function generateCurrentMwp($itemId)
    {
        global $company_id;
        $dbObj = new Database();

        $sql = "SELECT l.itemQty,l.itemPrice,l.refActivityName FROM erp_inventory_stocks_log AS l WHERE l.companyId=$company_id AND l.itemId=$itemId ORDER BY l.stockLogId ASC;";
        $resLog = $dbObj->queryGet($sql, true);
        $countOfLog = $resLog['numRows'];
        if ($resLog['status'] == "success" && $resLog['numRows'] > 0) {

            $totalQty = 0;
            $newMap = 0;

            foreach ($resLog['data'] as $log) {

                $mvtType = $log['refActivityName'];

                $qty = $log['itemQty'];
                $rate = $log['itemPrice'];
                
                // if($mvtType=="GRN"||$mvtType=="REV-GRN"){
                //     echo "<br>  Starting Movement Type $mvtType | Old Qty $totalQty | New Map $newMap  | Qty $qty | Rate $rate  <br>";
                // }

                $lastQty = $totalQty;
                $lastMap = $newMap;

                if($mvtType=="MIGRATION"){
                    $newMap = $rate;
                }

                if (in_array($mvtType, ['GRN', 'MAT-MAT-IN', 'REV-INVOICE', 'REV-PROD-OUT', 'CN', 'REV-DN', 'CNMANUAL'])) {
                    
                    if ($qty > 0) {
                        $newMap = ($lastQty + $qty) > 0 ? (($lastQty * $lastMap) + ($qty * $rate)) / ($lastQty + $qty) : 0;
                    }
                } elseif (in_array($mvtType, ['REV-GRN', 'REV-CN'])) {
                    
                    if ($qty < 0) {
                        $newMap = ($lastQty + $qty) > 0 ? (($lastQty * $lastMap) +($qty * $rate)) / ($lastQty + $qty) : 0;
                    }
                }
                $totalQty = max(0, $totalQty + (float)$qty);

                // echo "<br>  Movement Type $mvtType | LastQty $lastQty | LastMap $lastMap  <br>";
                // if($mvtType=="GRN"||$mvtType=="REV-GRN"){
                //     echo "<br>  After Movement Type $mvtType | Old Qty $totalQty | New Map $newMap  | Qty $qty | Rate $rate  <br>";     
                // }
            }

            // echo "<br>  Final Movement Type $mvtType | Final Qty $totalQty | Final Map $newMap  <br>";


            return ['status' => 'success', 'message' => "Item MAP and Total QTY Calculated", 'data' => ['finalMap' => inputValue($newMap), 'finalQty' => inputQuantity($totalQty), 'countOfLog' => $countOfLog]];
        } else {
            return ['status' => 'warning', 'message' => "Item Stock Log Not Found", 'sql' => $sql];
        }
    }

    function updateDirectMapToSummery($itemId, $mapValue)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        if ($itemId == "" || $itemId == 0) {
            return ["status" => "error", "message" => "Item ID Required"];
        }

        if($mapValue == "" || $mapValue == 0){
            return ["status" => "error", "message" => "MAP Value Required"];
        }

        $dbObj = new Database();

        $upRes['status'] = "error";
        $inStock['status'] = "error";

        $itemCode = $dbObj->queryGet("SELECT itemCode FROM `" . ERP_INVENTORY_ITEMS . "` WHERE itemId='" . $itemId . "' AND company_id = '" . $company_id . "'")['data']['itemCode'];

        $upSql = 'UPDATE `erp_inventory_stocks_summary` SET  `movingWeightedPrice`=' . $mapValue . ' ,`updatedBy`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $itemId;

        $upRes = $dbObj->queryUpdate($upSql);        

        $inSql = 'INSERT INTO `erp_inventory_stocks_moving_average` SET `companyId`=' . $company_id . ',`branchId`=' . $branch_id . ',`locationId`=' . $location_id . ',`itemId`=' . $itemId . ',`itemCode`="' . $itemCode . '",`movingAveragePrice`=' . $mapValue . ',`createdBy`="' . $created_by . '"';

        $inStock = $dbObj->queryInsert($inSql);

        if ($upRes['status'] == "success" && $inStock['status'] == "success") {
            return ["status" => "success", "message" => "Item MAP Updated"];
        } else {
            return ["status" => "error", "message" => "Item MAP Not Updated", "sql" => ['upSql' => $upSql, 'inStock' => $inSql]];
        }
    }
}
