<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $company_code = $_POST['company_code'];

    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $company_id = $authCustomer['company_id'];
    $branch_id = $authCustomer['branch_id'];
    $location_id = $authCustomer['location_id'];

    $po_sql = queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE `company_id`=$company_id AND `customer_id`=$customer_id ORDER BY `so_id` DESC  limit 5 ", true);
    if ($po_sql['status'] == "success") {

        $po_data = $po_sql["data"];
        $po_array = [];
        foreach ($po_data as $data) {

            $so_id = $data["so_id"];

            $po_items = queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` as so_item, `" . ERP_INVENTORY_ITEMS . "` as item WHERE so_item.inventory_item_id=item.itemId AND `so_id`=$so_id", true);
            $po_item_data = $po_items["data"];

            $so_array[] = array("so" => $data, "so_item" => $po_item_data);
        }
    }

    $sql_list = "SELECT * FROM `erp_branch_sales_order_invoices`   WHERE 1  AND `customer_id`='" . $customer_id . "'  ORDER BY `so_invoice_id` desc limit 5 ";
    $iv_sql = queryGet($sql_list, true);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];

        $iv_array = [];
        foreach ($iv_data as $data) {

            $ivid = $data["so_invoice_id"];

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

            $invoice_file = $data["customerDocumentFile"];
            $invoice =  BASE_URL . "branch/bills/" . $invoice_file;
            $grnIvItemSql = "SELECT * FROM `erp_branch_sales_order_invoice_items` as iv_item, `" . ERP_INVENTORY_ITEMS . "` as item WHERE iv_item.inventory_item_id=item.itemId AND `so_invoice_id`=$ivid";
            $iv_items = queryGet($grnIvItemSql, true);
            $iv_item_data = $iv_items["data"];

            $iv_array[] = array("iv" => $data, "iv_item" => $iv_item_data, "invoice" => $invoice);
        }
    }

    sendApiResponse([
        "status" => "success",
        "message" => "Response Found",
        "auth" => $authCustomer,
        "data" => [
            "sql" => $grnIvItemSql,
            "latest_so" => $so_array,
            "latest_invoice" => $iv_array,
        ]
    ], 200);
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
