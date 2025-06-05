<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-po-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../../app/v1/functions/common/templates/template-item-master-controller.php");

$headerData = array('Content-Type: application/json');
$dbObj = new Database();
$BranchPoObj = new BranchPo();
$ItemsObj = new ItemsController();
$tempObj = new TemplateItemController();
$goodsController = new GoodsController();

function getHsnDesc($hsnCode)
{
    $sql = "SELECT hsnDescription as hsnDesc  FROM `erp_hsn_code` WHERE hsnCode=$hsnCode";
    $res = queryGet($sql);

    if ($res['status'] == "success") {
        return $res['data']['hsnDesc'];
    } else {
        return null;
    }
}
function getImagesByItemId($itemId, $company_id, $branch_id, $location_id)
{
    $sql = queryGet("SELECT * FROM `erp_inventory_item_images` WHERE item_id=$itemId AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id ;", true);
    $imageArray = $sql['data'];
    $imageNamesArray = [];
    foreach ($imageArray as $image) {
        $imageNamesArray[] = $image['image_name'];
    }
    return $imageNamesArray;
}
function getUomName($uomId)
{
    $sql = "SELECT uomName ,uomDesc FROM `erp_inventory_mstr_uom` WHERE uomId=$uomId";
    $res = queryGet($sql);
    if ($res['status'] == "success") {
        $data = $res['data'];
        $result =  $data['uomName'] . " || " . $data['uomDesc'];
        return $result;
    } else {
        return null;
    }
}
function getStorageName($storagId, $location_id)
{
    $sql = "SELECT loc.storage_location_name as storage_location_name FROM `erp_storage_location` as loc WHERE loc.location_id=$location_id AND loc.storage_location_id=$storagId;";
    $res = queryGet($sql);
    $data = $res['data'];
    if ($res['status'] == "success") {
        return $data['storage_location_name'];
    }
}
function getClassificationDetail($depId){
    global $company_id;
    $sql="SELECT * FROM `erp_depreciation_table` WHERE `company_id`=$company_id and `depreciation_id`=$depId";
    $res=queryGet($sql);
    if ($res['status'] == "success") {
        return $res['data'];
    }else{
        return null;
    }
}
function getGlCodeAsset($glId){
    global $company_id;
    $typeAcc=1;
    $sql="SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND typeAcc=$typeAcc AND glStType='account'  AND `status`!='deleted' ORDER BY gl_code";
    $res=queryGet($sql);
    if($res['status'] == "success"){
        $data = $res['data'];
        $result =  $data['gl_code'] . " | " . $data['gl_label'];
        return $result;
    }else{
        return null;
    } 
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if ($_GET['act'] == 'duplicate') {
        $equip=$_GET['value'];
        $res=queryGet("SELECT `equip_no` from erp_equip_details WHERE `equip_no`=$equip AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id ");
        $numRows = $res['numRows'];
        if($numRows>0)
        {
            echo "exists";
        }

    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if ($_GET['act'] == 'modalData') {
        $itemId = $_GET['itemId'];
        $cond = "goods.company_id=$company_id  AND goods.itemId=$itemId AND goods.status!='deleted'";
        $sql_list = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` as goods WHERE " . $cond . "  AND goods.`goodsType`=9  ORDER BY itemId desc";
        $sqlMainQryObj = $dbObj->queryGet($sql_list);
        $data = $sqlMainQryObj['data'];
        $num_list = $sqlMainQryObj['numRows'];

        $dynamic_data = [];

        if ($num_list > 0) {


            $type = "service";
            $gldetails = getChartOfAccountsDataDetails($data['parentGlId'])['data'];

            // classification
            $goodTypeId = $data['goodsType'];
            $type_sql = $dbObj->queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
            $goodGroupId = $data['goodsGroup'];
            $group_sql = $dbObj->queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
            $group_name = $group_sql['data']['goodGroupName'];
            $type_name = $type_sql['data']['goodTypeName'] ? $type_sql['data']['goodTypeName'] : '-';

            $classification = [
                "glName" => $gldetails['gl_label'],
                "glCode" => $gldetails['gl_code'],
                "typeName" => $type_name,
                "groupName" => $group_name
            ];

            $type = "asset";

            $item_id = $data['itemId'];
            $storage_sql = $dbObj->queryGet("SELECT * FROM `" . ERP_INVENTORY_STORAGE . "` WHERE `item_id`=$item_id AND `location_id`=$location_id");
            $storage_data = $storage_sql['data'];

            $storageDetails = [
                "storageControl" => $storage_data['storage_control'] ?? "",
                "maxStoragePeriod" => $storage_data['maxStoragePeriod'],
                "maxStoragePeriodTimeUnit" => $storage_data['maxStoragePeriodTimeUnit'],
                "minRemainSelfLife" => $storage_data['minRemainSelfLife'],
                "minRemainSelfLifeTimeUnit" => $storage_data['minRemainSelfLifeTimeUnit'],
            ];

            // moving weighted price
            $summary_sql = $dbObj->queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE itemId=$itemId AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id ");
            $summary_data = $summary_sql['data'];

            // images 
            $imageRes = getImagesByItemId($itemId, $company_id, $branch_id, $location_id);

            // item specification
            $specification_sql = $dbObj->queryGet("SELECT specification,specification_detail FROM `erp_item_specification` WHERE item_id=$itemId AND branch_id=$branch_id AND company_id=$company_id AND location_id=$location_id;", true);
            $specification_data = $specification_sql['data'];

            $dynamic_data = [
                "type" => $type,
                "dataObj" => $data,
                "companyCurrency" => getSingleCurrencyType($company_currency),
                "created_by" => getCreatedByUser($data['createdBy']),
                "created_at" => formatDateORDateTime($data['createdAt']),
                "updated_by" => getCreatedByUser($data['updatedBy']),
                "updated_at" => formatDateORDateTime($data['updatedAt']),
                "classification" => $classification,
                "storageDetails" => $storageDetails,
                "hsnDesc" => getHsnDesc($data['hsnCode']),
                "movWeightPrice" => decimalValuePreview($summary_data['movingWeightedPrice']),
                "techSpecification" => $specification_data,
                "baseUnitMeasure" => getUomName($data['baseUnitMeasure']),
                "issueUnitMeasure" => getUomName($data['issueUnitMeasure']),
                "itemPrice" => decimalValuePreview($summary_data['itemPrice']),
                "itemMaxDiscount" => decimalQuantityPreview($summary_data['itemMaxDiscount']),
                "summaryData" => $summary_data,
                "images" => $imageRes,
                "defaultStorageLocationName" => getStorageName($summary_data['default_storage_location'], $location_id),
                "qaStorageLocationName" => ($summary_data['qa_storage_location'] > 0) ? getStorageName($summary_data['qa_storage_location'], $location_id) : '',
                "assetClass"=>getClassificationDetail($data['asset_classes'])['asset_class'],
                "assetGlCode"=>getGlCodeAsset($data['asset_gl_code']),
            ];


            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "sql_list" => $sql_list
            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sql_list
            ];
        }

        echo json_encode($res);
    }
    if ($_GET['act'] == 'classicView') {
        $itemId = $_GET['itemId'];
        $tempObj->printItemPreview($itemId);
    }
    if ($_GET['act'] == 'putToUse') {
        $itemId = $_GET['itemId'];
        $cond = "goods.company_id=$company_id  AND goods.itemId=$itemId AND goods.status!='deleted'";
        $sql_list = "SELECT `itemId`,`itemCode`,`itemName`,`baseUnitMeasure`,`dep_key` FROM `" . ERP_INVENTORY_ITEMS . "` as goods WHERE " . $cond . "  AND goods.`goodsType`=9  ORDER BY itemId DESC";
        $sqlMainQryObj = $dbObj->queryGet($sql_list);
        $data = $sqlMainQryObj['data'];
        $numRows = $sqlMainQryObj['numRows'];

        if ($numRows > 0) {
            $checkAsset = $dbObj->queryGet("SELECT * FROM `erp_asset_use` WHERE `asset_id`=$itemId AND `company_id`=$company_id AND `location_id`=$location_id");
             $checkAsset = $dbObj->queryGet("
                SELECT 
                    `logRef`, 
                    SUM(`itemQty`) AS total_itemQty, 
                    MIN(
                        CASE 
                            WHEN `refActivityName` = 'GRN' THEN DATE(`postingDate`)
                            ELSE NULL 
                        END
                    ) AS first_created_date
                FROM 
                    `erp_inventory_stocks_log` 
                WHERE 
                    `itemId` = $itemId AND 
                    `companyId` = $company_id AND 
                    `locationId` = $location_id AND
                    `branchId` =$branch_id
                GROUP BY 
                    `logRef` 
                HAVING 
                    total_itemQty > 0 
                ORDER BY 
                    first_created_date
            ", true);
            // $checkAsset = $dbObj->queryGet("
            //     SELECT 
            //         `logRef`, 
            //         SUM(`itemQty`) AS total_itemQty, 
            //         MIN(
            //             CASE 
            //                 WHEN `refActivityName` = 'GRN' THEN DATE(`postingDate`)
            //                 ELSE NULL 
            //             END
            //         ) AS first_created_date,
            //         CASE 
            //             WHEN `refActivityName` = 'GRN' THEN `itemPrice`
            //             ELSE NULL 
            //         END AS grn_itemPrice,
            //         CASE 
            //             WHEN `refActivityName` = 'GRN' THEN `storageLocationId`
            //             ELSE NULL 
            //         END AS storageLocationId,
            //         CASE 
            //             WHEN `refActivityName` = 'GRN' THEN `stockLogId`
            //             ELSE NULL 
            //         END AS stockLogId,
            //         CASE 
            //             WHEN `refActivityName` = 'GRN' THEN `storageType`
            //             ELSE NULL 
            //         END AS storageType
            //     FROM 
            //         `erp_inventory_stocks_log` 
            //     WHERE 
            //         `itemId` = $itemId AND 
            //         `companyId` = $company_id AND 
            //         `locationId` = $location_id AND
            //         `branchId` = $branch_id
            //     GROUP BY 
            //         `logRef` 
            //     HAVING 
            //         total_itemQty > 0 
            //     ORDER BY 
            //         first_created_date
            // ", true);




// With ITC And IV Posting
    $checkAsset = $dbObj->queryGet("
        SELECT 
            ils.`logRef`, 
            ils.`itemPrice`,
            SUM(ils.`itemQty`) AS total_itemQty, 
            MIN(
                CASE 
                    WHEN ils.`refActivityName` = 'GRN' AND grn.iv_status = 1 THEN DATE(ils.`postingDate`)
                    ELSE NULL 
                END
            ) AS first_created_date,
            CASE 
                WHEN ils.`refActivityName` = 'GRN' AND grn.iv_status = 1 THEN
                    CASE 
                        WHEN grn.itc = 1 THEN (ils.`itemPrice` + (grn.`grnTotalIgst` / ils.`itemQty`))
                        ELSE ils.`itemPrice`
                    END
                ELSE NULL 
            END AS grn_itemPrice,
            CASE 
                WHEN ils.`refActivityName` = 'GRN' AND grn.iv_status = 1 THEN ils.`storageLocationId`
                ELSE NULL 
            END AS storageLocationId,
            CASE 
                WHEN ils.`refActivityName` = 'GRN' AND grn.iv_status = 1 THEN ils.`stockLogId`
                ELSE NULL 
            END AS stockLogId,
            CASE 
                WHEN ils.`refActivityName` = 'GRN' AND grn.iv_status = 1 THEN ils.`storageType`
                ELSE NULL 
            END AS storageType,
            grn.`grnCode`
            FROM 
                `erp_inventory_stocks_log` ils
            LEFT JOIN 
                `erp_grn` grn ON ils.`refNumber` = grn.`grnCode`
            WHERE 
                ils.`itemId` = $itemId AND 
                ils.`companyId` = $company_id AND 
                ils.`locationId` = $location_id AND
                ils.`branchId` = $branch_id
            GROUP BY 
                ils.`logRef`
            HAVING 
                total_itemQty > 0 AND
                MAX(grn.iv_status) = 1 -- Ensure only logRefs with iv_status = 1 are returned
            ORDER BY 
                first_created_date
                ", true);





            $company_detaisl = queryGet("SELECT `depreciation_type` FROM `erp_companies` WHERE `company_id`='$company_id'");
            $method = strtolower($company_detaisl['data']['depreciation_type']);

            $dep_key = $data['dep_key'];
            $dep_percentage_sql = $dbObj->queryGet("
                SELECT `wdv`,`slm` 
                FROM `erp_depreciation_table` 
                WHERE `desp_key` = '" . $dep_key . "' 
                AND `company_id` = '" . $company_id . "'
            ");
            $dep_percentage = $dep_percentage_sql['data'][$method];
           
            $dynamic_data = [
                "itemId" => $data['itemId'],
                "itemCode" => $data['itemCode'],
                "itemName" => $data['itemName'],
                "itemUom" => $data['baseUnitMeasure'],
                "depPercentage" => decimalQuantityPreview($dep_percentage)
            ];

            $res = ["status" => true, "msg" => "Data Found Successfully", "data" => $dynamic_data, "batchlist" => $checkAsset];
        } else {
            $res = ["status" => false, "msg" => "Error!"];
        }
        echo json_encode($res);
    }
    if ($_GET['act'] == 'uomList') {
        $uomList = $goodsController->fetchUom();
        echo json_encode($uomList);
    }
    if ($_GET['act'] == 'costCenterList') {
        $funcList = $BranchPoObj->fetchFunctionality();
        echo json_encode($funcList);
    }
    if ($_GET['act'] == 'depHistory') {
        $assetId = $_GET['asset_use_id'];
        $sql = "SELECT * FROM `erp_asset_depreciation` WHERE asset_use_id=$assetId AND company_id = $company_id AND `branch_id`=$branch_id AND `location_id`=$location_id ORDER BY depreciation_code DESC";
        $sqlObject = $dbObj->queryGet($sql, true);
        if ($sqlObject['numRows'] > 0) {
            $res = ["status" => "success", "message" => "Data found", "data" => $sqlObject['data']];
        } else {
            $res = ["status" => "warning", "message" => "Data not found", "sql" => $sql];
        }
        echo json_encode($res);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['createLocationItem']) {
        $addNewObj = $goodsController->createGoodsLocation($_POST);
        echo json_encode($addNewObj);
    }
    if ($_POST['add_dep']) {
        $add_depreciation = $goodsController->createManualDepreciation($_POST);
        echo json_encode($add_depreciation);
    }
    if ($_POST['puttouse']) {
        // console(json_encode(["res"=>"error occured"]));
        // console($_POST);
        $addNewObj = $goodsController->asset_use($_POST);
        // console($addNewObj);
        // echo json_encode($addNewObj);


    }
}
