<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authVendor = authVendorApiRequest();
    $vendor_id = $authVendor['vendor_id'];
    $company_id = $authVendor['company_id'];
    $branch_id = $authVendor['branch_id'];
    $location_id = $authVendor['location_id'];

    $pageNo = $_POST['pageNo'] ? $_POST['pageNo'] : 0;
    $show = $_POST['limit'] ? $_POST['limit'] : 25;
    $start = $pageNo * $show;
    $end = $show;

    $cond = '';

    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND `vendorDocumentDate` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (`vendorDocumentNo` like '%" . $_POST['keyword'] . "%' OR `grnTotalAmount` like '%" . $_POST['keyword'] . "%' OR `postingDate` like '%" . $_POST['keyword'] . "%')";
    }

    $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
    $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];

    $companyDetails = array_merge($companyDetailsObj, $companyAdminDetailsObj);

    $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")["data"];

    $invoiceCounts = queryGet("SELECT 
        COUNT(CASE WHEN `paymentStatus` = '15' THEN 1 END) AS payable, 
        COUNT(CASE WHEN `paymentStatus` = '2' THEN 1 END) AS partialPaid, 
        COUNT(CASE WHEN `paymentStatus` = '18' THEN 1 END) AS paymentInitiated, 
        COUNT(CASE WHEN `paymentStatus` = '4' THEN 1 END) AS paid
     FROM 
     `erp_grninvoice` WHERE 1 " . $cond . " AND `companyId`=$company_id AND `branchId`=$branch_id AND `locationId`=$location_id AND `vendorId`=$vendor_id");

    $sql_list = "SELECT * FROM `erp_grninvoice` WHERE 1 " . $cond . " AND `vendorId`='" . $vendor_id . "'  AND `companyId`='" . $company_id . "' AND `branchId` = '$branch_id' AND `locationId` = '$location_id' ORDER BY `grnIvId` desc limit " . $start . "," . $end . " ";
    $iv_sql = queryGet($sql_list, true);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];

        $data_array = [];

        foreach ($iv_data as $data) {

            $taxComponents = json_decode($data['taxComponents'], true);
            $totalGstAmount = $taxComponents[3]['taxAmount'];

            $ivid = $data["grnIvId"];
            $invoice_file = $data["vendorDocumentFile"];
            $invoice =  BASE_URL . "branch/bills/" . $invoice_file;
            $grnIvItemSql = "SELECT iv_item.grnIvCode,iv_item.grnCode,iv_item.goodName,iv_item.goodDesc,iv_item.goodId,iv_item.goodCode,iv_item.grnType,iv_item.goodHsn,iv_item.goodQty,iv_item.receivedQty,iv_item.unitPrice,iv_item.cgst,iv_item.sgst,iv_item.igst,iv_item.tds,iv_item.totalAmount,iv_item.itemStocksQty,iv_item.itemUOM,iv_item.itemStorageLocation,item.parentGlId,item.itemCode,item.item_sell_type,item.itemName,item.itemDesc,item.netWeight,item.grossWeight,item.volume,item.volumeCubeCm,item.height,item.width,item.length,item.goodsType,item.goodsGroup,item.purchaseGroup,item.service_group,item.availabilityCheck,item.discountGroup,item.baseUnitMeasure,item.issueUnitMeasure,item.uomRel,item.service_unit,item.weight_unit,item.measuring_unit,item.purchasingValueKey,item.itemOpenStocks,item.itemBlockStocks,item.itemMovingAvgWeightedPrice,item.hsnCode,item.rcm_enabled,item.cost_center,item.asset_classes,item.dep_key,item.isBomRequired,item.featured FROM `erp_grninvoice_goods` as iv_item, `" . ERP_INVENTORY_ITEMS . "` as item WHERE iv_item.goodId=item.itemId AND `grnIvId`=$ivid";
            $iv_items = queryGet($grnIvItemSql, true);
            $iv_item_data = $iv_items["data"];

            $data["totalGstAmount"] = $totalGstAmount ?? 0;
            $data["invoice"] = $invoice;
            $data['shippingAddress'] = $locationDetailsObj;
            $data['companyDetails'] = $companyDetails;
            $data_array[] = array("iv" => $data, "iv_item" => $iv_item_data);
        }

        sendApiResponse([
            "status" => "success",
            "totalNoOfInvoice" => $iv_sql['numRows'],
            "message" => "Invoice fetched successfully",
            "counts" => $invoiceCounts["data"],
            "data" => $data_array

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No Invoice found",
            "counts" => $invoiceCounts["data"],
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
