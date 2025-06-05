<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $company_code = $_POST['company_code'];

    $authVendor = authVendorApiRequest();
    $vendor_id = $authVendor['vendor_id'];
    $company_id = $authVendor['company_id'];
    $branch_id = $authVendor['branch_id'];
    $location_id = $authVendor['location_id'];
    $phone = $authVendor['phone'];

    $po_sql = queryGet("SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `company_id`=$company_id AND `vendor_id`=$vendor_id ORDER BY `po_id` DESC  limit 5 ", true);

    $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
    $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
    $companyDetails = array_merge($companyDetailsObj, $companyAdminDetailsObj);
    $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")["data"];

    if ($po_sql['status'] == "success") {

        $po_data = $po_sql["data"];
        $po_array = [];
        foreach ($po_data as $data) {
            // console($data);
            $po_id = $data["po_id"];
            $po_items = queryGet("SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` as po_item, `" . ERP_INVENTORY_ITEMS . "` as item WHERE po_item.inventory_item_id=item.itemId AND `po_id`=$po_id", true);
            $po_item_data = $po_items["data"];

            $data["phone"] = $phone;
            $data["downloadUrl"] = BASE_URL . "vendor/po-download.php?po_id=" . $po_id;
            $data["shippingAddresss"] = $locationDetailsObj;
            $data["companyDetails"] = $companyDetails;
                      
            $po_array[] = array("po" => $data, "po_item" => $po_item_data);
        }
    }

    $sql_list = "SELECT * FROM `erp_grninvoice`   WHERE 1 " . $cond . "  AND `vendorId`='" . $vendor_id . "'  AND `companyId`='" . $company_id . "' AND `branchId` = '$branch_id' AND `locationId` = '$location_id' ORDER BY `grnIvId` desc limit 5 ";
    $iv_sql = queryGet($sql_list, true);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];

        $iv_array = [];
        foreach ($iv_data as $data) {

            $ivid = $data["grnIvId"];
            $invoice_file = $data["vendorDocumentFile"];
            $invoice =  BASE_URL . "branch/bills/" . $invoice_file;
            $grnIvItemSql = "SELECT * FROM `erp_grninvoice_goods` as iv_item, `" . ERP_INVENTORY_ITEMS . "` as item WHERE iv_item.goodId=item.itemId AND `grnIvId`=$ivid";
            $iv_items = queryGet($grnIvItemSql, true);
            $iv_item_data = $iv_items["data"];

            $data["downloadUrl"] = BASE_URL . "vendor/invoice-download.php?po_id=" . $ivid;
            $data["shippingAddresss"] = $locationDetailsObj;
            $data["companyDetails"] = $companyDetails;

            $iv_array[] = array("iv" => $data, "iv_item" => $iv_item_data, "invoice" => $invoice);
        }
    }

    sendApiResponse([
        "status" => "success",
        "message" => "Response Found",
        "data" => [
            "latest_po" => $po_array,
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
