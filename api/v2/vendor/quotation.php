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

    $pageNo = $_POST['pageNo'];
    $show = $_POST['limit'];
    $start = $pageNo * $show;
    $end = $show;

    $cond = '';

    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND rfq.created_at between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (rfq.rfqCode like '%" . $_POST['keyword'] . "%' OR rfq.prCode like '%" . $_POST['keyword'] . "%' OR pr.refNo like '%" . $_POST['keyword'] . "%')";
    }

    // $sql_list = "SELECT * FROM `" . ERP_RFQ_VENDOR_LIST . "` AS rfqvendor LEFT JOIN `" . ERP_RFQ_LIST . "` AS rfq ON rfqvendor.rfqItemListId = rfq.rfqId LEFT JOIN `" . ERP_BRANCH_PURCHASE_REQUEST . "` as pr ON rfq.prId = pr.purchaseRequestId  WHERE 1 " . $cond . "  AND rfqvendor.vendorId='" . $vendor_id . "'  AND rfq.company_id='" . $company_id . "' AND rfq.branch_id = '$branch_id' AND rfq.location_id = '$location_id' ORDER BY rfq.rfqId desc limit " . $start . "," . $end . " ";
    $sql_list = "SELECT * FROM `" . ERP_RFQ_VENDOR_LIST . "` AS rfqvendor LEFT JOIN `" . ERP_RFQ_LIST . "` AS rfq ON rfqvendor.rfqItemListId = rfq.rfqId WHERE 1 " . $cond . "  AND rfqvendor.vendorId='" . $vendor_id . "'  AND rfq.company_id='" . $company_id . "' AND rfq.branch_id = '$branch_id' AND rfq.location_id = '$location_id' ORDER BY rfq.rfqId desc limit " . $start . "," . $end . " ";
    $rfq_sql = queryGet($sql_list, true);
    
    if ($rfq_sql['status'] == "success") {
        $rfq_data = $rfq_sql["data"];
        $data_array = [];
        foreach ($rfq_data as $data) {
            // console($data);
            $rfqId = $data["rfqId"];
            $rfqItemSql="SELECT * FROM `" . ERP_RFQ_LIST_ITEM . "` as rfq_item, `" . ERP_INVENTORY_ITEMS . "` as item WHERE rfq_item.ItemId=item.itemId AND rfq_item.`rfqId`=$rfqId";
            $rfq_items = queryGet($rfqItemSql, true);
            $rfq_item_data = $rfq_items["data"];

            $data_array[] = array("rfq" => $data, "rfq_item" => $rfq_item_data);
        } 
        
        sendApiResponse([
            "status" => "success",
            "message" => "Quotation fetched successfully",
            "count" => $rfq_sql["numRows"],
            "data" => $data_array

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No quotation found",
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