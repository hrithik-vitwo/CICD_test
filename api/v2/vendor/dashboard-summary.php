<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $type = isset($_GET['type']) ? htmlspecialchars(trim($_GET['type'])) : null;

    $cond = '';
    function conditions($cond, $createdAt = null)
    {
        if (isset($_GET['fromDate']) && $_GET['fromDate'] != '') {
            $cond .= " AND $createdAt between '" . $_GET['fromDate'] . " 00:00:00' AND '" . $_GET['toDate'] . " 23:59:59'";
        }

        return $cond;
    }

    if (!$type) {
        sendApiResponse([
            "status" => "error",
            "message" => "Invalid input",
            "data" => []
        ], 400);
        exit;
    }

    $authVendor = authVendorApiRequest();
    $vendor_id = $authVendor['vendor_id'];
    $company_id = $authVendor['company_id'];
    $branch_id = $authVendor['branch_id'];
    $location_id = $authVendor['location_id'];

    try {
        // Get total received
        if ($_GET['type'] == 'totalReceivedAmount') {
            $totalReceivedObj = queryGet("SELECT SUM(`collect_payment`) AS 'totalReceivedAmount', COUNT(*) AS 'totalReceivedCount' FROM `erp_grn_payments` WHERE `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id AND `vendor_id`=$vendor_id" . conditions($cond, "documentDate") . "");
            $totalReceivedAmount = number_format($totalReceivedObj['data']["totalReceivedAmount"], 2);
            $totalReceivedCount = $totalReceivedObj["data"]["totalReceivedCount"];

            sendApiResponse([
                "totalReceivedAmount" => $totalReceivedAmount ?? 0,
                "totalReceivedCount" => $totalReceivedCount ?? 0
            ], 200);
        }

        // Get total invoice count and amount
        if ($_GET['type'] == 'invoiceCountAndAmount') {
            $totalInvoiceCountObj = queryGet("SELECT 
            COUNT(*) AS 'totalInvoiceCount', 
            SUM(`grnTotalAmount`) AS 'totalInvoiceAmount',
            SUM(CASE WHEN `paymentStatus` = 15 THEN `grnTotalAmount` ELSE 0 END) AS 'totalPayableAmount',
            SUM(CASE WHEN `paymentStatus` = 2 THEN `grnTotalAmount` ELSE 0 END) AS 'partialPaidAmount'
            FROM `erp_grninvoice` WHERE `companyId`=$company_id AND `branchId`=$branch_id AND `locationId`=$location_id AND `vendorId`=$vendor_id" . conditions($cond, "vendorDocumentDate"));

            $totalInvoiceCount = intval($totalInvoiceCountObj['data']["totalInvoiceCount"]) ?? 0;
            $totalInvoiceAmount = number_format($totalInvoiceCountObj['data']["totalInvoiceAmount"], 2) ?? 0;
            $totalPayableAmount = number_format($totalInvoiceCountObj['data']["totalPayableAmount"], 2) ?? 0;
            $partialPaidAmount = number_format($totalInvoiceCountObj['data']["partialPaidAmount"], 2) ?? 0;

            sendApiResponse([
                "totalInvoiceCount" => $totalInvoiceCount ?? 0,
                "totalInvoiceAmount" => $totalInvoiceAmount ?? 0,
                "totalPayableAmount" => $totalPayableAmount ?? 0,
                "partialPaidAmount" => $partialPaidAmount ?? 0
            ], 200);
        }

        // get overdue invoice count and amount
        if ($_GET['type'] == 'overdueInvoiceCountAndAmount') {
            $overdueInvoiceCountObj = queryGet("SELECT COUNT(*) AS 'overdueInvoiceCount', SUM(`grnTotalAmount`) AS 'totalOverdueAmount' FROM `erp_grninvoice` WHERE `companyId`=$company_id AND `branchId`=$branch_id AND `locationId`=$location_id AND `vendorId`=$vendor_id AND `dueDate` < grnCreatedAt AND `paymentStatus` = 15 " . conditions($cond, "vendorDocumentDate"));
            $totalOverdueAmount = number_format($overdueInvoiceCountObj['data']["totalOverdueAmount"], 2) ?? 0;
            $overdueInvoiceCount = intval($overdueInvoiceCountObj['data']["overdueInvoiceCount"]) ?? 0;

            sendApiResponse([
                "overdueInvoiceCount" => $overdueInvoiceCount ?? 0,
                "totalOverdueAmount" => $totalOverdueAmount ?? 0
            ], 200);
        }

        // Get total due invoice count and amount
        if ($_GET['type'] == 'dueInvoiceCountAndAmount') {
            $dueInvoiceObj = queryGet("SELECT COUNT(*) AS 'dueInvoiceCount', SUM(`dueAmt`) AS 'totalDueAmount' FROM `erp_grninvoice` WHERE `companyId`=$company_id AND `branchId`=$branch_id AND `locationId`=$location_id AND `vendorId`=$vendor_id AND `dueDate` >= grnCreatedAt AND `paymentStatus` IN(2,15,18) " . conditions($cond, "vendorDocumentDate"));
            $totalDueAmount = number_format($dueInvoiceObj["data"]["totalDueAmount"], 2) ?? 0;
            $dueInvoiceCount = intval($dueInvoiceObj["data"]["dueInvoiceCount"]) ?? 0;

            sendApiResponse([
                "dueInvoiceCount" => $dueInvoiceCount ?? 0,
                "totalDueAmount" => $totalDueAmount ?? 0
            ], 200);
        }

        // // Get vendor quotation amount
        // if ($_GET['type'] == 'vendorQuotationAmount') {
        //     $totalQuotationObj = queryGet("SELECT `erp_v_id` FROM `erp_vendor_response` WHERE vendor_id = $vendor_id" . conditions($cond, "created_at"), true);

        //     $totalQuotationAmount = 0;            
        //     foreach ($totalQuotationObj['data'] as $value) {
        //         $totalQuotationItemObj = queryGet("SELECT total AS totalAmount FROM `erp_vendor_item` WHERE `erp_v_id`=" . $value['erp_v_id'], true);

        //         // Ensure data exists and sum all totalAmount values
        //         if (!empty($totalQuotationItemObj["data"])) {
        //             foreach ($totalQuotationItemObj["data"] as $item) {
        //                 $totalQuotationAmount += $item['totalAmount'] ?? 0;
        //             }
        //         }
        //     }

        //     sendApiResponse([
        //         "totalQuotationAmount" => number_format($totalQuotationAmount, 2) ?? 0,
        //         "totalQuotationCount" => count($totalQuotationObj["data"])
        //     ], 200);
        // }

        if ($_GET['type'] == 'vendorQuotationAmount') {
            $totalQuotationObj = queryGet("SELECT `erp_v_id` FROM `erp_vendor_response` WHERE vendor_id = $vendor_id" . conditions($cond, "created_at"), true);

            $totalQuotationAmount = 0;
            $totalQuotationCount = 0; // Initialize count

            foreach ($totalQuotationObj['data'] as $value) {
                $totalQuotationItemObj = queryGet("SELECT total AS totalAmount FROM `erp_vendor_item` WHERE `erp_v_id`=" . $value['erp_v_id'], true);

                // Ensure data exists and sum all totalAmount values
                if (!empty($totalQuotationItemObj["data"])) {
                    foreach ($totalQuotationItemObj["data"] as $item) {
                        $totalQuotationAmount += $item['totalAmount'] ?? 0;
                        $totalQuotationCount++; // Increment count for each item
                    }
                }
            }

            sendApiResponse([
                "totalQuotationAmount" => number_format($totalQuotationAmount, 2),
                "totalQuotationCount" => $totalQuotationCount // Corrected count
            ], 200);
        }

        if ($_GET['type'] === 'purchaseOrderAmount') {
            $query = "
                SELECT 
                    SUM(`totalAmount`) AS totalPurchaseAmount,
                    COUNT(*) AS totalPurchaseCount,
                    SUM(CASE WHEN `po_status` = 9 THEN `totalAmount` ELSE 0 END) AS totalPurchaseOpenAmount,
                    COUNT(CASE WHEN `po_status` = 9 THEN 1 ELSE NULL END) AS totalPurchaseOpenCount
                FROM `erp_branch_purchase_order`
                WHERE company_id = $company_id 
                  AND branch_id = $branch_id 
                  AND location_id = $location_id 
                  AND vendor_id = $vendor_id 
                  " . conditions($cond, "po_date");

            $result = queryGet($query)["data"];

            sendApiResponse([
                "totalPurchaseOrderAmount" => number_format($result["totalPurchaseAmount"] ?? 0, 2),
                "totalPurchaseOrderCount" => $result["totalPurchaseCount"] ?? 0,
                "totalPurchaseOrderOpenAmount" => number_format($result["totalPurchaseOpenAmount"] ?? 0, 2),
                "totalPurchaseOrderOpenCount" => $result["totalPurchaseOpenCount"] ?? 0
            ], 200);
        }


        sendApiResponse([
            "status" => "warning",
            "message" => "Invalid type parameter",
            "data" => []
        ], 400);
    } catch (Exception $e) {
        sendApiResponse([
            "status" => "error",
            "message" => "An error occurred: " . $e->getMessage(),
            "data" => []
        ], 500);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
