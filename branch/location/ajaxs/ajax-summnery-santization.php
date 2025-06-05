<?php
require_once("../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$dbObj = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST['act'] == "logData") {
        $santizeCompanyId = 11;
        $sql = "SELECT item.itemId, lg.MAP AS itemMapPrice, lg.`status` FROM erp_rm_data_log AS lg  LEFT JOIN erp_inventory_items as item ON item.itemCode=lg.itemCode  WHERE lg.company_id=$santizeCompanyId AND item.company_id=$santizeCompanyId AND lg.`status`='updated';";
        $resSql = $dbObj->queryGet($sql, true);
        $count = $resSql['numRows'];
        $sumCount = 0;
        if ($resSql['status'] == "success") {
            if ($resSql['numRows'] > 0) {
                $data = $resSql['data'];
                foreach ($data as $rm) {

                    $itemRightMap = $rm['itemMapPrice'];
                    $itemId = $rm['itemId'];

                    $summerySql = "SELECT s.* FROM erp_inventory_stocks_summary AS s WHERE s.company_id=$santizeCompanyId AND s.itemId=$itemId AND s.updatedAt < '2025-02-25 13:00:00'";
                    $sumObj = $dbObj->queryGet($summerySql);
                    if ($sumObj['status'] == 'success' && $sumObj['numRows'] > 0) {
                        $sumData = $sumObj['data'];
                        $stockSummaryId = $sumData['stockSummaryId'];
                        $movingWeightedPrice = $sumData['movingWeightedPrice'];
                        if ($movingWeightedPrice != $itemRightMap) {

                            echo "<br>";
                            echo "need to updated";
                            echo "stockSummaryId " . $stockSummaryId;
                            echo "<br>";

                            echo "<br>";
                            echo "itemRightMap " . $itemRightMap;
                            echo "<br>";


                            echo "<br>";
                            echo "movingWeightedPrice " . $movingWeightedPrice;
                            echo "<br>";

                            // $updateSql = "UPDATE `erp_inventory_stocks_summary` SET `itemPrice`=$itemRightMap WHERE  `stockSummaryId`=$stockSummaryId";
                            // $updateRes = $dbObj->queryUpdate($updateSql);
                            // if ($updateRes['status'] == 'success') {
                            //     console($updateSql);
                            //     echo "update successfully ";
                            //     echo "<br>";

                            //     $sumCount++;
                            // } else {
                            //     echo "Roll back successfully  canot be udpated";
                            //     echo "<br>";
                            //     $dbObj->queryRollBack();
                            // }

                        } else {
                            echo "<br>";
                            echo "stockSummaryId " . $stockSummaryId;
                            echo "<br>";

                            echo "<br>";
                            echo "itemRightMap " . $itemRightMap;
                            echo "<br>";


                            echo "<br>";
                            echo "movingWeightedPrice " . $movingWeightedPrice;
                            echo "<br>";
                        }
                    }
                }
            }
        }


        if ($sumCount > 0) {
            echo json_encode(['status' => "success", "msg" => "Data sanitized successfully", "count" => $count, "totalSummeryUpdate" => $sumCount, 'sql' => $sql]);
        } else {
            echo json_encode(['status' => "warning", "msg" => "No Data sanitized", "count" => $count, "totalSummeryUpdate" => $sumCount, 'sql' => $sql]);
        }
    }
} else {
    echo json_encode(["status" => "error", "msg" => "Error!"]);
}
