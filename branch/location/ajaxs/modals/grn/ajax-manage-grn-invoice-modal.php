<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-po-controller.php");
require_once("../../../../../app/v1/functions/branch/func-goods-controller.php");

$dbObj = new Database();
$headerData = array('Content-Type: application/json');
$BranchPoObj = new BranchPo();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($_GET['act'] == "modalData") {
        $po_number = $_GET['po_number'];
        $vendor_id = $_GET['vendor_id'];
        $po_id = $_GET['po_id'];

        // $cond = "AND erp_branch_purchase_order.po_number = " . "'$po_number'";

        // $sql_list = 'SELECT * FROM `erp_branch_purchase_order` 
        //             LEFT JOIN `erp_vendor_details` 
        //             ON erp_vendor_details.vendor_id = erp_branch_purchase_order.vendor_id 
        //             WHERE erp_branch_purchase_order.`company_id` = ' . $company_id . ' 
        //             AND erp_branch_purchase_order.`branch_id` = ' . $branch_id . ' 
        //             AND erp_branch_purchase_order.`location_id` = ' . $location_id . ' 
        //             AND erp_branch_purchase_order.`po_status` = "9" ' . $cond . '';

        
        $sql_list="SELECT po.* FROM `erp_branch_purchase_order` AS po LEFT JOIN `erp_vendor_details` AS v ON v.vendor_id = po.vendor_id  WHERE po.`company_id` = $company_id AND po.`branch_id` = $branch_id AND po.`location_id` = $location_id AND po.`po_status` = '9' AND po.status = 'active' AND po.po_id = '$po_id'";


        $sqlObject = $dbObj->queryGet($sql_list);
        $num_list = $sqlObject['numRows'];
        if ($num_list > 0) {
            $po_data = $sqlObject['data'];
            $vendorDetails = $BranchPoObj->fetchVendorDetails($vendor_id)['data'][0];
            $itemDetails = $BranchPoObj->fetchBranchPoItems($po_id)['data'];

            foreach ($itemDetails as $oneItem) {
                if ($oneItem['remainingQty'] != "") {
                    $remainingQty = $oneItem['remainingQty'] . " " . $oneItem['uom'];
                } else {
                    $remainingQty = "0 " . $oneItem['uom'];
                }
            
                // Fetch delivery schedule
                $deliveryScheduleObj = $BranchPoObj->fetchBranchPoItemsDeliverySchedule($oneItem['po_item_id']);
                $deliverySchedule = $deliveryScheduleObj['data'];
            
                // Loop through delivery schedules (handling multiple dates)
                foreach ($deliverySchedule as $schedule) {
                    $itemdata[] = [
                        "itemCode" => $oneItem['itemCode'],
                        "itemName" => $oneItem['itemName'],
                        "unitPrice" => decimalValuePreview($oneItem['unitPrice']),
                        "qty" => decimalQuantityPreview($oneItem['qty']),
                        "uom" => $oneItem['uom'],
                        "remainingQty" => decimalQuantityPreview($remainingQty),
                        "total_price" => decimalValuePreview($oneItem['total_price']),
                        "delivery_date" => formatDateWeb($schedule['delivery_date']) // Ensure correct assignment
                    ];
                }
            }

            $data = [
                "totalAmount" => decimalValuePreview($po_data['totalAmount']),
                "po_number" => $po_data['po_number'],
                "ref_no" => $po_data['ref_no'],
                "created_by" => getCreatedByUser($po_data["created_by"]),
                "updated_by" => getCreatedByUser($po_data["updated_by"]),
                "created_at" => formatDateORDateTime($po_data["created_at"]),
                "updated_at"=> formatDateORDateTime($po_data["updated_at"]),
                "trade_name" => $vendorDetails['trade_name'],
                "vendor_code" => $vendorDetails['vendor_code'],
                "vendor_gstin" => $vendorDetails['vendor_gstin'],
                "itemdata" => $itemdata
            ];


            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $data,
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
