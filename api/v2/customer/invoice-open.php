<?php
require_once("api-common-func.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $company_id = $authCustomer['company_id'];
    $branch_id = $authCustomer['branch_id'];
    $location_id = $authCustomer['location_id'];

    $pageNo = $_POST['pageNo'];
    $show = $_POST['limit'];
    $start = $pageNo * $show;
    $end = $show;

    define("BASE_URL", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/");
    define("BUCKET_URL", BASE_URL);
    define("COMP_STORAGE_URL", BUCKET_URL . "uploads/$company_id");

    // https://one.vitwo.ai/q1/api/v2/customer/invoice-open.php
    // COMP_STORAGE_URL = https://one.vitwo.ai/q1/
    // https://one.vitwo.ai/q1/branch/location/classic-view/invoice-preview-print.php?invoice_id=NjEwMA==&type=company&template_id=0

    // $url = COMP_STORAGE_URL . "branch/location/classic-view/invoice-preview-print.php?invoice_id=NjEwMA==&type=company&template_id=0";

    $cond = '';

    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND CAST(`invoice_date` AS DATETIME) BETWEEN '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (`all_total_amt` like '%" . $_POST['keyword'] . "%' OR `invoice_date` like '%" . $_POST['keyword'] . "%')";
    }

    $sql_list = "SELECT * FROM `erp_branch_sales_order_invoices` WHERE `customer_id`='" . $customer_id . "'  AND `branch_id` = '$branch_id' AND `location_id` = '$location_id' AND `company_id`='" . $company_id . "' AND `invoiceStatus` != 4 " . $cond . " ORDER BY `so_invoice_id` desc limit " . $start . "," . $end . " ";
    $iv_sql = queryGet($sql_list, true);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];

        $data_array = [];
        foreach ($iv_data as $data) {

            $ivid = $data["so_invoice_id"];
            $encodedInvId = base64_encode($ivid);

            $url = BASE_URL . "branch/location/classic-view/invoice-preview-print.php?invoice_id=".$encodedInvId."&type=company&template_id=0";

            $data['shareInvoice'] = $url;
            // unserialize customer details
            $customerDetails = unserialize($data["customerDetails"]);
            unset($data['customerDetails']);
            $data['customerDetails'] = $customerDetails;

            // unserialize company bank details
            $company_bank_details = unserialize($data["company_bank_details"]);
            unset($data['company_bank_details']);
            $data['company_bank_details'] = $company_bank_details;

            // unserialize company details
            $companyDetails = unserialize($data["companyDetails"]);
            unset($data['companyDetails']);
            $data['companyDetails'] = $companyDetails;

            $grnIvItemSql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICE_ITEMS . "` AS iv_item, `" . ERP_INVENTORY_ITEMS . "` AS item WHERE iv_item.inventory_item_id=item.itemId AND iv_item.so_invoice_id=$ivid";
            $iv_items = queryGet($grnIvItemSql, true);
            $iv_item_data = $iv_items["data"];

            $data_array[] = array("iv" => $data, "iv_item" => $iv_item_data );
        }

        sendApiResponse([
            "status" => "success",
            "message" => "Invoice found",
            "count" => count($data_array),
            "data" => $data_array

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No Invoice found",
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
