<?php
require_once("api-common-func.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
$branchObj = new BranchSo();

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'] ?? 0;
    $company_id = $authCustomer['company_id'] ?? 0;
    $branch_id = $authCustomer['branch_id'] ?? 0;
    $location_id = $authCustomer['location_id'] ?? 0;
    
    define("BASE_URL", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/");
    define("BUCKET_URL", BASE_URL);
    define("COMP_STORAGE_URL", BUCKET_URL."uploads/$company_id");

    $pageNo = $_POST['pageNo'];
    $show = $_POST['limit'];
    $start = $pageNo * $show;
    $end = $show;

    $return = [];

    $cond = '';

    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND `createdAt` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (`items`.`itemCode` like '%" . $_POST['keyword'] . "%' OR `items`.`itemName` like '%" . $_POST['keyword'] . "%' OR `items`.`hsnCode` like '%" . $_POST['keyword'] . "%' OR `items`.`itemDesc` like '%" . $_POST['keyword'] . "%')";
    }

    $sql_list = "SELECT summary.*,items.*,hsn.taxPercentage, hsn.hsnDescription FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` as summary INNER JOIN  `" . ERP_INVENTORY_ITEMS . "` as items ON summary.itemId=items.itemId RIGHT JOIN `" . ERP_HSN_CODE . "` as hsn ON items.hsnCode = hsn.hsnCode WHERE summary.company_id='$company_id' AND summary.branch_id='$branch_id' AND summary.location_id='$location_id' AND summary.itemId=items.itemId AND items.goodsType IN (3,4,5) $cond ORDER BY summary.itemId DESC LIMIT $start, $end";
    $iv_sql = queryGet($sql_list, true);

    sendApiResponse([
        "status" => "success",
        "message" => "Data fetched successfully",
        "count" => $iv_sql["numRows"],
        "data" => $iv_sql["data"]
    ]);
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
