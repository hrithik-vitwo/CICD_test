<?php
require_once("api-common-func.php");



// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;

    $authVendor = authVendorApiRequest();
    $vendor_id = $authVendor['vendor_id'];
    $company_id = $authVendor['company_id'];
    $branch_id = $authVendor['branch_id'];
    $location_id = $authVendor['location_id'];
    $phone = $authVendor['phone'];

    $pageNo = $_POST['pageNo'];
    $show = $_POST['limit'];
    $start = $pageNo * $show;
    $end = $show;

    $cond = '';

    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND po_date between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (po_number like '%" . $_POST['keyword'] . "%' OR delivery_date like '%" . $_POST['keyword'] . "%' OR ref_no like '%" . $_POST['keyword'] . "%')";
    }

    $po_sql = queryGet("SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE 1 " . $cond . " AND `company_id`=$company_id AND `vendor_id`=$vendor_id ORDER BY po_id DESC LIMIT " . $start . "," . $end . " ", true);
    
    $poCounts = queryGet("SELECT 
        COUNT(CASE WHEN `po_status` = '17' THEN 1 END) AS rejected, 
        COUNT(CASE WHEN `po_status` = '14' THEN 1 END) AS pending, 
        COUNT(CASE WHEN `po_status` = '9' THEN 1 END) AS openStatus,
        COUNT(CASE WHEN `po_status` = '10' THEN 1 END) AS closed
     FROM 
     `erp_branch_purchase_order` WHERE 1 ".$cond." AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id AND `vendor_id`=$vendor_id");

    $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
    $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
    $companyDetails = array_merge($companyDetailsObj, $companyAdminDetailsObj);

    $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")["data"];

    if ($po_sql['status'] == "success") {

        $po_data = $po_sql["data"];
        
        $data_array = [];
        // $data_array[] = array("counts" => $poCounts["data"]);

        foreach ($po_data as $data) {
            $po_id = $data["po_id"];
            $po_items = queryGet("SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` as po_item, `" . ERP_INVENTORY_ITEMS . "` as item WHERE po_item.inventory_item_id=item.itemId AND `po_id`=$po_id", true);
            $po_item_data = $po_items["data"];

            $data["subTotalPrice"] = ((float) $po_item_data['unitPrice']) * ((float) $po_item_data['qty']);
            $data["phone"] = $phone;
            $data["downloadLink"] = BASE_URL . "vendor/po-download.php?po_id=" . $po_id;
            $data["shippingAddress"] = $locationDetailsObj;
            $data["companyDetails"] = $companyDetails;

            $data_array[] = array("po" => $data, "po_item" => $po_item_data);
        }

        sendApiResponse([
            "status" => "success",
            "totalNoOfPo" => $po_sql['numRows'],
            "message" => "Purchase order fetched successfully",
            "counts" => $poCounts["data"],
            "data" => $data_array

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No purchase order found",
            "counts" => $poCounts["data"],
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