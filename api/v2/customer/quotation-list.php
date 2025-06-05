<?php
require_once("api-common-func.php");

// accepted = 16
// rejected = 17
// pending = 14
// approved = 11 (pending to customer end)
// rejected from customer = 19

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $company_id = $authCustomer['company_id'];
    $branch_id = $authCustomer['branch_id'];
    $location_id = $authCustomer['location_id'];

    $approvalStatus = $_POST['approvalStatus'] ?? 0;
    $pageNo = $_POST['pageNo'];
    $show = $_POST['limit'];
    $start = $pageNo * $show;
    $end = $show;

    define("BASE_URL", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/");
    define("BUCKET_URL", BASE_URL);
    define("COMP_STORAGE_URL", BUCKET_URL . "uploads/$company_id");

    $cond = '';

    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND `created_at` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    } 

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (`quotation_no` like '%" . $_POST['keyword'] . "%' OR `posting_date` like '%" . $_POST['keyword'] . "%')";
    }

    // quotation query
    if ($approvalStatus > 0 || $approvalStatus != null) {
        $sql_list = "SELECT * FROM `erp_branch_quotations`
            WHERE 
                `customer_id` = '" . $customer_id . "'
                AND `company_id` = $company_id
                AND `branch_id` = $branch_id
                AND `location_id` = $location_id
                AND approvalStatus='$approvalStatus' " . $cond . "
        ORDER BY `quotation_id` DESC LIMIT " . $start . "," . $end . " ";
    } else {
        $sql_list = "SELECT *
            FROM erp_branch_quotations AS q
            WHERE 
                `customer_id` = '" . $customer_id . "'
                AND `company_id` = $company_id
                AND `branch_id` = $branch_id
                AND `location_id` = $location_id
                " . $cond . "
                AND q.approvalStatus NOT IN(14, 17)
                AND (
                    q.approvalStatus != 11
                    OR q.validityPeriod >= CURDATE()
                )            
        ORDER BY `quotation_id` DESC LIMIT " . $start . "," . $end . " ";
    }

    $iv_sql = queryGet($sql_list, true);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];

        $data_array = [];
        foreach ($iv_data as $key => $data) {
            $sql_item_list = "SELECT 
                `erp_inventory_items`.*, 
                `erp_branch_quotation_items`.`qty`,
                true AS 'isClicked',
                `erp_inventory_stocks_summary`.`itemPrice`
            FROM 
                `erp_branch_quotation_items`
            LEFT JOIN 
                `erp_inventory_items` 
                ON `erp_branch_quotation_items`.`inventory_item_id` = `erp_inventory_items`.`itemId`
            LEFT JOIN 
                `erp_inventory_stocks_summary` 
                ON `erp_inventory_stocks_summary`.`itemId` = `erp_inventory_items`.`itemId`
            WHERE 
                `erp_branch_quotation_items`.`quotation_id` = '" . $data['quotation_id'] . "'
            ORDER BY 
                `erp_branch_quotation_items`.`quotation_item_id` DESC";
            $qry_item = queryGet($sql_item_list, true);
            $quotationId = $data['quotation_id'];
            $encodedQuotId = base64_encode($quotationId);
            $url = BASE_URL . "branch/location/classic-view/invoice-preview-print.php?quotationId=" . $encodedQuotId;
            $data_array[$key] = array("ordermain" => $data);
            $data_array[$key]['ordermain']['shareQuotation'] = $url;
            $data_array[$key]['ordermain']['items'] = $qry_item['data'];
        }

        sendApiResponse([
            "status" => "success",
            "message" => count($data_array) . " data found",
            "data" => $data_array
        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No not found",
            "data" => []

        ], 200);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
