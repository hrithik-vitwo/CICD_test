<?php
// 0 0 1 * * php /home/devalpha/public_html/app/v1/cron/opening-stock-update.php
// 0 0 1 * * php /home/alpha/public_html/app/v1/cron/opening-stock-update.php
require_once dirname(__DIR__) . "/connection-branch-admin.php";
echo "<pre>";
echo "Working:<br/>";
if (date("d") == 12) {
    $curMonthFirstDate = "2023-10-01";
    // $curMonthFirstDate = date("Y-m-01");
    $curMonthLastDate = date("Y-m-t", strtotime($curMonthFirstDate));
    $prevMonthFirstDate = date("Y-m-d", strtotime($curMonthFirstDate . " -1 month"));
    $prevMonthLastDate = date("Y-m-t", strtotime($prevMonthFirstDate));

    // echo "<br>curMonthFirstDate :".$curMonthFirstDate;
    // echo "<br>curMonthLastDate :".$curMonthLastDate;
    // echo "<br>prevMonthFirstDate :".$prevMonthFirstDate;
    // echo "<br>prevMonthLastDate :".$prevMonthLastDate;
    $dbObj = new Database(true);
    $stockDataPoolTable = "erp_inventory_stocks_log";
    $stockOpeningClosingTable = "erp_opening_closing_stock";
    $checkIfExistObj = $dbObj->queryGet("SELECT `company_id` FROM `$stockOpeningClosingTable` WHERE `closing_qty`!='' AND `date`='$prevMonthFirstDate'", true);
    // print_r($checkIfExistObj);
    if($checkIfExistObj["status"] != "success") {
        $selectObj = $dbObj->queryGet('SELECT `companyId`,`branchId`,`locationId`, `storageLocationId`,`itemId`,`logRef` AS item_batch, SUM(`itemQty`) AS stock_available FROM `' . $stockDataPoolTable . '` WHERE DATE(`bornDate`) BETWEEN "' . $prevMonthFirstDate . '" AND "' . $prevMonthLastDate . '" GROUP BY `locationId`, `storageLocationId`,`itemId`,`logRef`, `companyId`,`branchId`', true);
        // print_r($selectObj);
        foreach ($selectObj["data"] as $prevMonthClosing) {
            $company_id = $prevMonthClosing["companyId"];
            $branch_id = $prevMonthClosing["branchId"];
            $location_id = $prevMonthClosing["locationId"];
            $item_id = $prevMonthClosing["itemId"];
            $item_storage = $prevMonthClosing["storageLocationId"];
            $item_batch = $prevMonthClosing["item_batch"];
            $stock_available = $prevMonthClosing["stock_available"] > 0 ? $prevMonthClosing["stock_available"] : 0;

            $closingSql = "UPDATE `$stockOpeningClosingTable` SET `closing_qty`=`opening_qty`+$stock_available, `updated_by`='Auto' WHERE `location_id`=$location_id AND `item_id`=$item_id AND `item_storage`=$item_storage AND `item_batch`='$item_batch' AND `date`='$prevMonthFirstDate'";
            $updateObj = $dbObj->queryUpdate($closingSql);
            if ($updateObj["affectedRows"] == 0) {
                $closingSql = "INSERT INTO `$stockOpeningClosingTable` SET `company_id`=$company_id,`branch_id`=$branch_id,`location_id`=$location_id,`created_by`='Auto', `updated_by`='Auto', `item_id`=$item_id,`item_storage`=$item_storage,`item_batch`='$item_batch', `date`='$prevMonthFirstDate', `closing_qty`=$stock_available";
                $updateObj = $dbObj->queryInsert($closingSql);
            }
            // print_r($updateObj);

            $openingSql = "INSERT INTO `$stockOpeningClosingTable` SET `company_id`=$company_id,`branch_id`=$branch_id,`location_id`=$location_id,`created_by`='Auto',`item_id`=$item_id,`item_storage`=$item_storage,`item_batch`='$item_batch', `date`='$curMonthFirstDate', `opening_qty`=$stock_available";
            $openingObj = $dbObj->queryInsert($openingSql);
            // print_r($openingObj);
        }

        $actionObj = $dbObj->queryFinish();
        $actionObj["message"] = $curMonthFirstDate . " => " .$actionObj["message"];
        print_r($actionObj);
    }else{
        print_r([
            "status" => "warning",
            "message" => "Already stock updated"
        ]);
    }
} else {
    print_r([
        "status" => "warning",
        "message" => "Today is not a valid day"
    ]);
}
echo "</pre>";
