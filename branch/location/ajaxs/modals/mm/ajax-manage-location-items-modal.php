<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-po-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../../app/v1/functions/branch/func-goods-controller.php");
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
    $data = $res['data'];
    if ($res['status'] == "success") {
        $result =  $data['uomName'] . " || " . $data['uomDesc'];
        return $result;
    } else {
        return null;
    }
}
function getStorageName($storagId,$company_id,$branch_id, $location_id)
{
    $sql = "SELECT loc.storage_location_name as storage_location_name FROM `erp_storage_location` as loc WHERE loc.company_id=$company_id AND loc.branch_id=$branch_id AND loc.location_id=$location_id  AND loc.storage_location_id=$storagId;";
    $res = queryGet($sql);
    $data = $res['data'];
    return $res;
    if ($res['status'] == "success") {
        return $data['storage_location_name'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'modalData') {

    $itemId = $_GET['itemId'];
    $cond = "goods.company_id=$company_id AND goods.branch=$branch_id AND goods.location_id=$location_id AND goods.itemId=$itemId AND goods.status!='deleted'";
    $sql_list = "SELECT goods.*, UOM.uomName, groups.goodGroupName, stock.movingWeightedPrice, stock.priceType AS valuation_class, stock.itemPrice AS target_price, type.goodTypeName, stock.bomStatus AS bomStatus, stock.status FROM erp_inventory_stocks_summary as stock LEFT JOIN erp_inventory_items as goods ON stock.itemId=goods.itemId LEFT JOIN erp_inventory_mstr_uom AS UOM ON goods.baseUnitMeasure = UOM.uomId LEFT JOIN erp_inventory_mstr_good_groups AS groups ON goods.goodsGroup = groups.goodGroupId LEFT JOIN erp_inventory_mstr_good_types AS type ON goods.goodsType = type.goodTypeId LEFT JOIN erp_status_master AS status_mstr ON stock.bomStatus = status_mstr.status_id WHERE  " . $cond . " AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId DESC";
    $sqlMainQryObj = $dbObj->queryGet($sql_list);
    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $dynamic_data = [];

    if ($num_list > 0) {

        // Good group tree  start
        $goodObj=$goodsController->findGoodGrpDetailById($data['goodsGroup']);

        $tree[] = [
            "goodGroupName" => $goodObj['goodGroupName'],
            "goodGroupId" => $data['goodsGroup'],
        ];

        if ($goodObj['groupParentId']!=0) {
            $parentId = $goodObj['groupParentId'];
            $resData = $goodsController->findGoodGrpDetailById($parentId);
            while ($resData) {
                $tree[] = [
                    "goodGroupName" => $resData['goodGroupName'],
                    "goodGroupId" => $resData['goodGroupId']
                ];

                if ($resData['groupParentId']==0) {
                    break;
                }

                $resData = $goodsController->findGoodGrpDetailById($resData['groupParentId']);
                $parentId = $resData['groupParentId'];
            }
        }
        // good group tree end

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

        if ($data['goodsType'] == 5 || $data['goodsType'] == 7 || $data['goodsType'] == 10) {

            $serviceDetails = [
                "itemName" => $data['itemName'],
                "itemDesc" => $data['itemDesc'],
                "hsnCode" => $data['hsnCode'],
                "glName" => $gldetails['gl_label'],
                "glCode" => $gldetails['gl_code'],
            ];
            // moving weighted price
            $summary_sql = $dbObj->queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE itemId=$itemId AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id ");
            $summary_data = $summary_sql['data'];



            $dynamic_data = [
                "type" => $type,
                "dataObj" => $data,
                "companyCurrency" => getSingleCurrencyType($company_currency),
                "created_by" => getCreatedByUser($data['createdBy']),
                "created_at" => formatDateORDateTime($data['createdAt']),
                "updated_by" => getCreatedByUser($data['updatedBy']),
                "updated_at" => formatDateORDateTime($data['updatedAt']),
                "serviceDetails" => $serviceDetails,
                "hsnDesc" => getHsnDesc($data['hsnCode']),
                "serviceUnit" => getUomName($data['baseUnitMeasure']),
                "summaryData" => $summary_data,
                "classification" => $classification,
                "tree" => $tree
            ];
        } else if ($data['goodsType'] != 5 || $data['goodsType'] != 7 || $data['goodsType'] != 10) {
            $type = "other";

            $item_id = $data['itemId'];
            $storage_sql = $dbObj->queryGet("SELECT * FROM `" . ERP_INVENTORY_STORAGE . "` WHERE `item_id`=$item_id AND `location_id`=$location_id");
            $storage_data = $storage_sql['data'];
            // console($storage_sql);

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
            // console($summary_data);
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
                "movWeightPrice" => $summary_data['movingWeightedPrice'],
                "techSpecification" => $specification_data,
                "baseUnitMeasure" => getUomName($data['baseUnitMeasure']),
                "issueUnitMeasure" => getUomName($data['issueUnitMeasure']),
                "itemPrice" => $summary_data['itemPrice'],
                "itemMaxDiscount" => $summary_data['itemMaxDiscount'],
                "summaryData" => $summary_data,
                "images" => $imageRes,
                "defaultStorageLocationName" =>($summary_data['default_storage_location'] > 0) ?  getStorageName($summary_data['default_storage_location'], $company_id,$branch_id, $location_id):'',
                "qaStorageLocationName" => ($summary_data['qa_storage_location'] > 0) ? getStorageName($summary_data['qa_storage_location'], $company_id,$branch_id, $location_id) : '',
                "tree" => $tree
            ];
        }


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
            "sql" => $sqlMainQryObj
        ];
    }

    echo json_encode($res);
} else if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'classicView') {
    $itemId = $_GET['itemId'];
    $tempObj->printItemPreview($itemId);
}
else if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'stChange') {
    $itemId = $_GET['itemId'];
    $sql="UPDATE `erp_inventory_items` SET status='inactive' WHERE itemId=$itemId";
    $res =$dbObj->queryUpdate($sql);
    echo json_encode($res);
}
else if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'goodDel') {
    $id = $_GET['id'];
    $sql="UPDATE `erp_inventory_items` SET status='deleted' WHERE itemId=$id";
    $res =$dbObj->queryUpdate($sql);
    echo json_encode($res);
}