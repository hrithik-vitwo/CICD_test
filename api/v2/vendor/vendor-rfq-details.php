<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $authVendor = authVendorApiRequest();
    $vendor_id = $authVendor['vendor_id'];
    $company_id = $authVendor['company_id'];
    $branch_id = $authVendor['branch_id'];
    $location_id = $authVendor['location_id'];
    $vendor_email = $authVendor['email'];

    $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
    $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];

    $companyDetails = array_merge($companyDetailsObj, $companyAdminDetailsObj);

    // $query = "SELECT * FROM erp_rfq_vendor_list  WHERE erp_rfq_vendor_list.rfqVendorId = '$id' AND erp_rfq_vendor_list.vendor_email = '$email' AND erp_rfq_vendor_list.vendor_type = '$type' AND erp_rfq_vendor_list.status = 'active'";
    $query = "SELECT * FROM `erp_rfq_vendor_list` WHERE vendorId = '$vendor_id' AND vendor_email = '$vendor_email' AND status = 'active'";
    $datasets = queryGet($query, true);

    $dataAyy = [];

    foreach ($datasets['data'] as $data) {
        // $rfqVendorId = $data['rfqVendorId'];
        $rfqListId = $data['rfqItemListId'];

        $prQuery = "SELECT * FROM erp_rfq_list  WHERE rfqId = '$rfqListId' AND `created_at` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
        $pr_db = queryGet($prQuery, true);

        // 🚨 Check if RFQ data is empty, then skip
        if (empty($pr_db['data'])) {
            continue;  // Skip this iteration if no RFQs are found
        }

        $items = [];
        foreach ($pr_db['data'] as $key => $pr) {
            $pr_id = $pr["prId"];
            $closing_date = $pr['closing_date'];

            $date1 = date_create($closing_date);
            $date2 = date_create("now");

            $diff = date_diff($date2, $date1);
            $days_left = (int)$diff->format("%r%a");

            $validity = "";
            $sts = "open";

            // Determine validity status correctly
            if ($days_left > 1) {
                $validity = "$days_left days left";
            } elseif ($days_left == 1) {
                $validity = "1 day left";
            } elseif ($days_left == 0) {
                $validity = "Expires today";
            } else {
                $validity = "Expired " . abs($days_left) . " days ago";
                $sts = "expired";
            }

            // Store result
            $pr_db['data'][$key]['expiryDetails'] = ["status" => $sts, "message" => $validity];

            $itemcode = "SELECT * FROM erp_rfq_items LEFT JOIN erp_branch_purchase_request_items ON erp_branch_purchase_request_items.itemId = erp_rfq_items.itemId LEFT JOIN erp_inventory_items ON erp_inventory_items.itemId = erp_branch_purchase_request_items.itemId WHERE erp_rfq_items.rfqId = '$rfqListId' AND erp_branch_purchase_request_items.prId = '$pr_id'";
            $itemset = queryGet($itemcode, true);
            $items = $itemset;

            foreach ($itemset['data'] as $key => $it) {
                $uom = $it['uom'];
                $uomSql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$uom", false);
                $uomDetails = $uomSql['data'];

                $items['data'][$key]['gst'] = "0";
                $items['data'][$key]['deliveryMode'] = ["FOR", "FOB", "CIF"];
                $items['data'][$key]['uomDetails'] = ["uomName" => $uomDetails['uomName'], "description" => $uomDetails['uomDesc']];
            }
        }

        // 🚨 Skip adding RFQs if there is no data
        if (!empty($pr_db['data'])) {
            $dataArr[] = [
                "rfq" => $pr_db['data'][0],
                "items" => $items['data'],
                "companyDetails" => $companyDetails
            ];
        }
    }

    if ($datasets['status'] == "success") {

        sendApiResponse([
            "status" => "success",
            "message" => count($dataArr) . " RFQ fetched successfully",
            // "prQuery" => $datasets,
            "data" => $dataArr

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
