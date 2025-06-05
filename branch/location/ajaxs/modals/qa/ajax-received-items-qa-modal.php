<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if ($_GET['act'] == 'modalHeader') {
        $stock_id = $_GET['stocklogid'];

        $sql = "SELECT stocklog.logRef, stocklog.stockLogId, stocklog.itemQty, stocklog.bornDate, grn.grnPoNumber, grn.vendorDocumentNo, grn.vendorCode, grn.vendorName, item.itemCode , item.createdBy , item.updatedBy , item.createdAt , item.updatedAt
        FROM `erp_inventory_stocks_log` AS stocklog 
        LEFT JOIN `erp_storage_location` AS stloc ON stloc.`storage_location_id` = stocklog.`storageLocationId` 
        LEFT JOIN `erp_grn` AS grn ON grn.`grnCode` = stocklog.`logRef` 
        LEFT JOIN `erp_inventory_items` AS item ON item.`itemId` = stocklog.`itemId` 
        WHERE stocklog.stockLogId = '$stock_id' 
        ORDER BY stocklog.stockLogId DESC";

        $get_last_updated_qty = queryGet("SELECT * FROM `erp_qa_summary` WHERE `companyId` = '$company_id' AND `branchId`='$branch_id' AND `locationId`='$location_id' AND `stock_log_id`='$stock_id'", false);



        $sql_run = queryGet($sql, true);
        // console($sql_run);
        $num_list = $sql_run['numRows'];
        $sqlData = $sql_run['data'];

        if ($num_list > 0) {
            $dynamic_arr = [];

            foreach ($sqlData as $data) {

                if ($get_last_updated_qty["numRows"] == 0) {
                    $remaining_qty = $data["itemQty"] ?? 0;
                    $status = 0;
                } else {
                    $remaining_qty = $data["itemQty"] - (($get_last_updated_qty["data"]["passed"] ?? 0) + ($get_last_updated_qty["data"]["rejected"] ?? 0));
                    $status = $get_last_updated_qty["data"]["status"];
                }
                $dynamic_arr[] = [
                    "vendorCode" => $data["vendorCode"],
                    "vendorName" => $data["vendorName"],
                    "invNumber" => $data["vendorDocumentNo"],
                    "itemQty" => inputQuantity($data["itemQty"]),
                    "passedQty" => inputQuantity($get_last_updated_qty["data"]["passed"]),
                    "rejectedQty" => inputQuantity($get_last_updated_qty["data"]["rejected"]),
                    "remainingQty" => inputQuantity($remaining_qty),
                    "receivedQty" => inputQuantity($data["itemQty"]),
                    "stocklogid" => $data["stockLogId"], 
                    "createdBy" => getCreatedByUser($data["createdBy"]),
                    "updatedBy" => getCreatedByUser($data["updatedBy"]),
                    "createdAt" => formatDateWeb($data["createdAt"]),
                    "updatedAt" => formatDateWeb($data["updatedAt"]),
                ];
                // console($data);
                // echo "hiii"; 
            }


            $response = [
                "data" => $dynamic_arr,
                "status" => true,
                "message" => "Data Fetched Successfully",
            ];
        } else {
            $response = [
                "data" => $num_list,
                "status" => false,
                "message" => "Data Not Fetched",
                "sql" => $sql
            ];
        }

        echo json_encode($response);
        // console($dynamic_arr);
    }
    if ($_GET['act'] == 'relativeHistoryModal') {
        $qa_log_Id = $_GET['qa_log_Id'];
    
        $sql_query_log = queryGet("SELECT qa_file,qa_log_Id FROM `erp_qa_log` 
                                   WHERE `companyId` = '$company_id' 
                                   AND `branchId` = '$branch_id' 
                                   AND `locationId` = '$location_id' 
                                   AND `qa_log_Id` = '$qa_log_Id'", true);
    
        $num_list = $sql_query_log['numRows'];
        $sqlData = $sql_query_log['data'];
    
        if ($num_list > 0) {
            $dynamic_arr = [];
    
            foreach ($sqlData as $one_data) {
                $id = $one_data['qa_log_Id'];
    
                // Now fetch links
                $get_link = queryGet("SELECT * FROM `erp_qa_link` WHERE `companyId` = '$company_id' AND `branchId`='$branch_id' AND `locationId`='$location_id' AND `qa_log_Id`='$id'", true);
                $links = [];
    
                if ($get_link['numRows'] > 0) {
                    foreach ($get_link['data'] as $link_row) {
                        $links[] = $link_row['link'];
                    }
                }
    
                $dynamic_arr[] = [
                    "qa_file" => $one_data['qa_file'],
                    "links"   => $links
                ];
            }
    
            $response = [
                "data" => $dynamic_arr,
                "status" => true,
                "message" => "Data fetched Successfully"
            ];
        } else {
            $response = [
                "data" => [],
                "status" => false,
                "message" => "Data not fetched"
            ];
        }
    
        echo json_encode($response);
    }

    if ($_GET['act'] == 'formStatusradio') {
        $stock_id = $_GET['stocklogid'];
        $get_last_updated_qty = queryGet("SELECT * FROM `erp_qa_summary` WHERE `companyId` = '$company_id' AND `branchId`='$branch_id' AND `locationId`='$location_id' AND `stock_log_id`='$stock_id'", false);
        $data = $get_last_updated_qty['data'];

        $dynamic_arr = [];

        if ($get_last_updated_qty['numRows'] > 0) {
            $dynamic_arr = [
                "form_status" => $data['status']
            ];

            $response = [
                "status" => true,
                "data" => $dynamic_arr,
                "message" => "Data Fetched Successfully"
            ];
        } else {
            $response = [
                "status" => false,
                "data" => [],
                "message" => "Data not Fetched"
            ];
        }

        echo json_encode($response);
    }

    if ($_GET['act'] == 'rejectedlistSpecificationModal') {
        $stockLogid = $_GET['stockLogid'];

        $specifications_list_sql = "SELECT 
        item.`itemCode`,
        item.`itemName`,
        item.`availabilityCheck`,
        item.`status`,
        item.`itemDesc`,
        item.`netWeight`,
        item.`grossWeight`,
        item.`volume`,
        item.`volumeCubeCm`,
        item.`height`,
        item.`width`,
        item.`length`
        FROM
        `erp_inventory_stocks_log` AS stocklog
        LEFT JOIN `erp_inventory_items` AS item
        ON item.`itemId` = stocklog.`itemId`
        WHERE
        stocklog.`stockLogId` = '$stockLogid'";

        $rejectedlistSpecification_run = queryGet($specifications_list_sql,true);

        $num_list = $rejectedlistSpecification_run['numRows'];

        $rejectedlistSpecification_data = $rejectedlistSpecification_run['data'];

        if($num_list > 0)
        {
            $dynamic_listSpecification = [];
            $onlyItemDetails = [];

            foreach($rejectedlistSpecification_data as $data)
            {
                $dynamic_listSpecification[] = [
                    "itemDesc" => $data['itemDesc'] ?? "-",
                    "netWeight" => inputQuantity($data['netWeight']) ?? "-",
                    "grossWeight" => inputQuantity($data['grossWeight']) ?? "-",
                    "volume" => inputQuantity($data['volume']) ?? "-",
                    "volumeCubeCm" => inputQuantity($data['volumeCubeCm']) ?? "-",
                    "height" => inputQuantity($data['height']) ?? "-",
                    "width" => inputQuantity($data['width']) ?? "-",
                    "length" => inputQuantity($data['length']) ?? "-"
                ];

                $onlyItemDetails[] = [
                    "itemCode" => $data['itemCode'] ?? "-",
                    "itemName" => $data['itemName'] ?? "-",
                    "availabilityCheck" => $data['availabilityCheck'] ?? "-",
                    "status" => $data['status'] ?? "-",
                ];
            }

            $response = [
                "status" => true,
                "data" => [
                    "dynamic_listSpecification" => $dynamic_listSpecification,
                    "onlyItemDetails" => $onlyItemDetails
                ],
                "message" => "Data Fetched Successfully",
            ];
        }
        else
        {
            $response = [
                "status" => false,
                "data" => $num_list,
                "message" => "Data Not Fetched "
            ];
        }

        echo json_encode($response);

    }
}

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    if ($_POST['act'] == 'relativeHistorytable') {
        $stock_id = $_POST['stocklogid'];
        $sql = "SELECT qa_log_Id ,doc_no , passed FROM `erp_qa_log` WHERE `companyId` = '$company_id' AND `branchId`='$branch_id' AND `locationId`='$location_id' AND `stock_log_id`='$stock_id'";

        $sql_run = queryGet($sql, true);

        $sqlData = $sql_run['data'];

        if ($num_list > 0) {
            $dynamic_arr = [];
            $sl_no = 1;

            foreach ($sqlData as $data) {
                $dynamic_arr = [
                    'sl_no'       => $sl_no++,
                    'doc_no'      => $data["doc_no"],
                    'passed'      => inputQuantity($data["passed"]),
                    'rejected'    => inputQuantity($data["rejected"]),
                    'status'      => ($data["status"] == "0") ? "ToDo" : (($data["status"] == "1") ? "InProgress" : "Done"),
                    'Created_at' => formatDateORDateTime($data["qaCreatedAt"]),
                    'created_by' => getCreatedByUser($data["qaCreatedBy"]),
                    "qa_log_Id" => $data['qa_log_Id']
                ];
            }
            $response = [
                "data" => $dynamic_arr,
                "status" => true,
                "message" => "Data fetched successfully.",
            ];
        } else {
            $response = [
                "data" => [],
                "status" => false,
                "message" => "No data found.",
                "sql" => $sql
            ];
        }

        echo json_encode($response);
    }
}
