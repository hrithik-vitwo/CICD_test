<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');

$dbObj = new Database();


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // $vendorId = $_GET['id'];
    if ($_GET['act'] == "modalData") {
        $mrp_id = $_GET['mrp_id'];
        $sql_list = "SELECT varient.*, varient.`created_by` AS created, varient.`created_at` AS `time`, variant.*, variant.`status` AS item_status, territory.*, customer_group.*, var_items.* FROM `erp_mrp_variant` AS varient LEFT JOIN `erp_mrp_territory` AS territory ON territory.territory_id = varient.territory LEFT JOIN `erp_customer_mrp_group` AS customer_group ON customer_group.customer_mrp_group_id = varient.customer_group LEFT JOIN `erp_mrp_variant_items` AS variant ON variant.mrp_id = varient.mrp_id LEFT JOIN `erp_inventory_items` AS var_items ON variant.item_id = var_items.itemId WHERE varient.`company_id` = $company_id AND varient.`branch_id` = $branch_id AND varient.`location_id` = $location_id AND variant.`mrp_id` = $mrp_id ORDER BY varient.mrp_id DESC";

        $sqlObject = $dbObj->queryGet($sql_list , true);
        $sql_data = $sqlObject['data'];
        // console($sql_data);
        $num_list = intval($sqlObject['numRows']);
        // console($num_list);
        $dynamic_data = [];
        if ($num_list > 0) {

            foreach ($sql_data as $data) {
                $dynamic_data[] = [
                    "mrp_variant" => $data['mrp_variant']?? "-" ,
                    "valid_from" => $data['valid_from'] ?? "-",
                    "valid_till" => $data['valid_till']?? "-" ,
                    "status" => $data['status'] ?? "-",
                    "itemName" => $data['itemName'] ?? "-",
                    "itemCode" => $data['itemCode'] ?? "-",
                    "cost" => $data['cost'] ?? "-",
                    "margin" => $data['margin'] ?? "-",
                    "mrp" => $data['mrp'] ?? "-",
                    "item_status" => $data['item_status'] ?? "-"
                ];
            }
            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "sql" => $sql_list
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
}