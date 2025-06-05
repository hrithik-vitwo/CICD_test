<?php
require_once("../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$dbObj = new Database();

// function to insert query into database
function updateDataByLogId($stockLogId, $newItemMap, $companyId)
{
    global $dbObj;
    $mainUpdateSql = "UPDATE `erp_inventory_stocks_log` SET `itemPrice`=$newItemMap WHERE `stockLogId`=$stockLogId AND companyId=$companyId;";
    $mainUpdateRes = $dbObj->queryUpdate($mainUpdateSql);
    $response = [];
    if ($mainUpdateRes['status'] == 'success') {
        $response = ["status" => "success", "message" => "Data Updated Successfully", "stockLogId" => $stockLogId];
    } else {
        $rollBackRes = $dbObj->queryRollBack();
        if ($rollBackRes['status'] == 'success') {
            $response = ["status" => "warning", "message" => "Failed to update,Rollback", "stockLogId" => $stockLogId, "mainUpdateSql" => $mainUpdateSql];
        } else {
            $response = ["status" => "error", "message" => "Something went Wrong..!", "mainUpdateSql" => $mainUpdateSql];
        }
    }
    return $response;
}

// update summery log

function updateSummeryByItemId($itemId, $newItemMap, $companyId)
{
    global $dbObj;

    $mainUpdateSql="UPDATE erp_inventory_stocks_summary AS sm SET sm.movingWeightedPrice=$newItemMap WHERE sm.itemId=$itemId AND sm.company_id=$companyId";
    $mainUpdateRes = $dbObj->queryUpdate($mainUpdateSql);
    $response = [];
    if ($mainUpdateRes['status'] == 'success') {
        $response = ["status" => "success", "message" => "Data Updated Successfully", "itemId" => $itemId,"sql" => $mainUpdateRes];
    } else {
        $rollBackRes = $dbObj->queryRollBack();
        if ($rollBackRes['status'] == 'success') {
            $response = ["status" => "warning", "message" => "Failed to update,Rollback", "itemId" => $itemId, "mainUpdateSql" => $mainUpdateSql];
        } else {
            $response = ["status" => "error", "message" => "Something went Wrong..!", "mainUpdateSql" => $mainUpdateSql];
        }
    }
    return $response;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST['act'] == "logData") {
        $totalRmCount = 0;
        $santizeCompanyId = 11;
        // $sql = "SELECT rmlg.*,rmlg.MAP AS itemRightPrice FROM erp_rm_data_log AS rmlg WHERE rmlg.company_id=$santizeCompanyId AND rmlg.status='not updated';";
        // $sql = "SELECT lg.* FROM erp_rm_new_data_log AS lg ";
        $sumSql="SELECT lg.itemCode, lg.createdAt, lg.MAP FROM ( SELECT itemCode, createdAt, MAP, ROW_NUMBER() OVER (PARTITION BY itemCode ORDER BY createdAt DESC) AS rn FROM erp_rm_new_data_log ) lg WHERE lg.rn = 1;";
        $resSql = $dbObj->queryGet($sumSql, true);
        // console($resSql);
        $count = $resSql['numRows'];
        $totalImpactCount = 0;
        if ($resSql['status'] == "success") {
            if ($resSql['numRows'] > 0) {
                $data = $resSql['data'];
                foreach ($data as $one) {
                    $itemCode = $one['itemCode'];
                    $itemRightMap = $one['MAP'];
                    $sqlItem="SELECT itm.itemId FROM erp_inventory_items AS itm WHERE itm.itemCode='".$itemCode."' AND itm.company_id=$santizeCompanyId";
                    $resItm= $dbObj->queryGet($sqlItem)['data'];
                    $itemId=$resItm['itemId'];
                    $floatValue = floatval(str_replace(',', '', $itemRightMap));
                    // echo "itemId: ".$itemId;
                    // echo "itemRightMap: ".$floatValue;
                    $response = updateSummeryByItemId($itemId, $floatValue, $santizeCompanyId);
                    if ($response['status'] == "success") {
                        $totalImpactCount = $totalImpactCount + 1;
                    }
                }
            }
        }

        if ($totalImpactCount > 0) {
            echo json_encode(['status' => "success", "msg" => "Data sanitized successfully", "count" => $count, "totalImpact" => $totalImpactCount, 'sql' => $sql]);
        } else {
            echo json_encode(['status' => "warning", "msg" => "No Data sanitized", "count" => $count, "totalImpact" => $totalImpactCount, 'sql' => $sql]);
        }
    }
} else {
    echo json_encode(["status" => "error", "msg" => "Error!"]);
}
