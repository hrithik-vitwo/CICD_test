<?php
require_once("api-common-func.php");



// API CODE
if ($_SERVER["REQUEST_METHOD"] == "GET") {
  //  echo 1;

    $authVendor = authVendorApiRequest();
    $vendor_id = $authVendor['vendor_id'];
    $company_id = $authVendor['company_id'];

    $po_sql = queryGet("SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `company_id`=$company_id AND `vendor_id`=$vendor_id", true);
    // console($po_sql["data"]);
    // exit();
    if ($po_sql['status'] == "success") {


        $po_data = $po_sql["data"];
        $data_array = [];
        foreach ($po_data as $data) {
            // console($data);
            $po_id = $data["po_id"];
            $po_items = queryGet("SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` as po_item, `" . ERP_INVENTORY_ITEMS . "` as item WHERE po_item.inventory_item_id=item.itemId AND `po_id`=$po_id", true);
            $po_item_data = $po_items["data"];
         
            $data_array[] = array("po" => $data, "po_item" => $po_item_data);
        }
        // console($data_array);
        sendApiResponse([
            "status" => "success",
            "message" => "success",
            "data" => $data_array

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No purchase order found",
            "data" => []

        ], 400);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
//echo "ok";