<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("../../../app/v1/functions/branch/func-brunch-po-controller.php");
require_once("pagination/common-pagination.php");
// require_once("../../common/exportexcel.php");
require_once("../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');

// print_r($_POST);
$BranchPoObj = new BranchPo();
session_start();
$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'managePoall') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $_SESSION['columnMapping'] = $_POST['columnMapping'];
        if (isset($_SESSION['columnMapping'])) {
            $columnMapping = $_SESSION['columnMapping'];
        }

        $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? $_POST['limit'] : 25;

        $page_no = isset($_POST['pageNo']) ? (int)$_POST['pageNo'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $formObj = $_POST['formDatas'];
        $cond = "";


        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            if ($slag === 'delivery_date' || $slag === 'po_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif ($slag === "so.totalAmount" || $slag === 'so.totalItems') {
                $cleanedValue = str_replace(',', '', $data['value']);
                $conds .= $slag . " " . $data['operatorName'] . " " . $cleanedValue . "";
            } else if ($slag === 'so.created_by') {


                $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sts = " AND `status` !='deleted'";

        $sql_list = "SELECT so.*, stat.label, vendor.vendor_code FROM `erp_branch_purchase_order` AS so LEFT JOIN `erp_status_master` AS stat ON so.po_status = stat.status_id LEFT JOIN `erp_vendor_details` as vendor ON vendor.vendor_id=so.vendor_id WHERE 1 " . $cond . " AND`branch_id`=$branch_id AND so.location_id=$location_id AND so.company_id=$company_id " . $sts . " ORDER BY po_id desc";

        $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);

        $dynamic_data = [];
        $num_list = $sqlMainQryObj['numRows'];
        $sql_data = $sqlMainQryObj['data'];
        $output = "";
        $limitText = "";
        $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        if ($num_list > 0) {
            foreach ($sql_data as $data) {
                $trade_name =  $BranchPoObj->fetchVendorDetails($data['vendor_id'])['data'][0]['trade_name'];
                $cost_sql = queryGet("SELECT * FROM `erp_cost_center` WHERE `CostCenter_id` = '" . $data['cost_center'] . "'");
                $cost_center = $cost_sql['data']['CostCenter_code'];

                $ship_location_sql = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id` = '" . $data['ship_address'] . "'");
                //  console($ship_location_sql);
                $ship_location = $ship_location_sql['data']['othersLocation_name'] . "," . $ship_location_sql['data']['othersLocation_building_no'] . "," . $ship_location_sql['data']['othersLocation_flat_no'] . "," . $ship_location_sql['data']['othersLocation_street_name'] . "," . $ship_location_sql['data']['othersLocation_pin_code'] . "," . $ship_location_sql['data']['othersLocation_location'] . "," . $ship_location_sql['data']['othersLocation_city'] . "," . $ship_location_sql['data']['othersLocation_district'] . "," . $ship_location_sql['data']['othersLocation_state'];

                $bill_location_sql = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id` = '" . $data['bill_address'] . "'");
                //  console($ship_location_sql);
                $bill_location = $bill_location_sql['data']['othersLocation_name'] . "," . $bill_location_sql['data']['othersLocation_building_no'] . "," . $bill_location_sql['data']['othersLocation_flat_no'] . "," . $bill_location_sql['data']['othersLocation_street_name'] . "," . $bill_location_sql['data']['othersLocation_pin_code'] . "," . $bill_location_sql['data']['othersLocation_location'] . "," . $bill_location_sql['data']['othersLocation_city'] . "," . $bill_location_sql['data']['othersLocation_district'] . "," . $bill_location_sql['data']['othersLocation_state'];

                $check_cur = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`='" . $data['currency'] . "'");

                if ($data['po_status'] == 14) {
                    $status = "Pending";
                } else if ($data['po_status'] == 9) {
                    $status = "Open";
                } else if ($data['po_status'] == 10) {
                    $status = "Closed";
                }
                $totalAmt = $data['totalAmount'] * $data['conversion_rate'];
                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "poId" => $data['po_id'],
                    "vendor.vendor_code" => $data['vendor_code'],
                    "vendorIcon" => ucfirst(substr($trade_name, 0, 1)),
                    "vendor.trade_name" => $BranchPoObj->fetchVendorDetails($data['vendor_id'])['data'][0]['trade_name'],
                    "ref_no" => $data['ref_no'],
                    "po_date" => $data['po_date'],
                    "po_number" => $data['po_number'],
                    "so.totalItems" => $data['totalItems'],
                    "so.totalAmount" => $check_cur['data']['currency_name'] . " " . decimalValuePreview($totalAmt),
                    "delivery_date" => $data['delivery_date'],
                    "use_type" => $data['use_type'],
                    "inco_type" => $data['inco_type'],
                    "shipLoc" => $ship_location,
                    "bill_location" => $bill_location,
                    "so.created_by" => getCreatedByUser($data['created_by']),
                    "stat.label" => $data['label'],
                    "poStatus" => $data['po_status']
                ];
                $sl++;
            }
            $output .= "</table>";
            $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";
            $queryset = queryGet($sqlRowCount);
            $totalRows = $queryset['data']['row_count'];
            $total_page = ceil($totalRows / $limit_per_Page);
            $output .= pagiNation($page_no, $total_page);

            $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';



            // $csvContent = exportToExcelAll($sql_list, json_encode($columnMapping));
            // $csvContentBypagination = exportToExcelByPagin($sql_Mainqry, json_encode($columnMapping));
            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "pagination" => $output,
                "limitTxt" => $limitText,
                // "sqlMain" => $sqlMainQryObj,
                // "csvContent" => $csvContent,
                // "csvContentBypagination" => $csvContentBypagination
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
if ($_POST['act'] == 'alldata') {
    $formObj = $_POST['formDatas'];
    $cond = "";


    $implodeFrom = implode('', array_map(function ($slag, $data) {
        $conds = "";
        if ($slag === 'delivery_date' || $slag === 'po_date') {
            if ($data['operatorName'] === 'BETWEEN') {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
            } else {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
            }
        } elseif ($slag === "so.totalAmount" || $slag === 'so.totalItems') {
            $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
        } else if ($slag === 'so.created_by') {


            $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
        } else {
            $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
        }

        return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
        $cond .= $implodeFrom;
    }
    $sts = " AND `status` !='deleted'";

    $sql_list = "SELECT so.*, stat.label, vendor.vendor_code FROM `erp_branch_purchase_order` AS so LEFT JOIN `erp_status_master` AS stat ON so.po_status = stat.status_id LEFT JOIN `erp_vendor_details` as vendor ON vendor.vendor_id=so.vendor_id WHERE 1 " . $cond . " AND`branch_id`=$branch_id AND so.location_id=$location_id AND so.company_id=$company_id " . $sts . " ORDER BY po_id desc";

    $dynamic_data_all = [];
    $sqlMainQryObjall = queryGet($sql_list, true);
    $sql_data_all = $sqlMainQryObjall['data'];
    $num_list =  $sqlMainQryObjall['numRows'];
    if ($num_list > 0) {
        foreach ($sql_data_all as $data) {
            $trade_name =  $BranchPoObj->fetchVendorDetails($data['vendor_id'])['data'][0]['trade_name'];
            $cost_sql = queryGet("SELECT * FROM `erp_cost_center` WHERE `CostCenter_id` = '" . $data['cost_center'] . "'");
            $cost_center = $cost_sql['data']['CostCenter_code'];

            $ship_location_sql = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id` = '" . $data['ship_address'] . "'");
            //  console($ship_location_sql);
            $ship_location = $ship_location_sql['data']['othersLocation_name'] . "," . $ship_location_sql['data']['othersLocation_building_no'] . "," . $ship_location_sql['data']['othersLocation_flat_no'] . "," . $ship_location_sql['data']['othersLocation_street_name'] . "," . $ship_location_sql['data']['othersLocation_pin_code'] . "," . $ship_location_sql['data']['othersLocation_location'] . "," . $ship_location_sql['data']['othersLocation_city'] . "," . $ship_location_sql['data']['othersLocation_district'] . "," . $ship_location_sql['data']['othersLocation_state'];

            $bill_location_sql = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id` = '" . $data['bill_address'] . "'");
            //  console($ship_location_sql);
            $bill_location = $bill_location_sql['data']['othersLocation_name'] . "," . $bill_location_sql['data']['othersLocation_building_no'] . "," . $bill_location_sql['data']['othersLocation_flat_no'] . "," . $bill_location_sql['data']['othersLocation_street_name'] . "," . $bill_location_sql['data']['othersLocation_pin_code'] . "," . $bill_location_sql['data']['othersLocation_location'] . "," . $bill_location_sql['data']['othersLocation_city'] . "," . $bill_location_sql['data']['othersLocation_district'] . "," . $bill_location_sql['data']['othersLocation_state'];

            $check_cur = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`='" . $data['currency'] . "'");

            if ($data['po_status'] == 14) {
                $status = "Pending";
            } else if ($data['po_status'] == 9) {
                $status = "Open";
            } else if ($data['po_status'] == 10) {
                $status = "Closed";
            }
            $totalAmt = $data['totalAmount'] * $data['conversion_rate'];
            $sl=1;
            $dynamic_data_all[] = [
                "sl_no" => $sl,
                "poId" => $data['po_id'],
                "vendor.vendor_code" => $data['vendor_code'],
                "vendorIcon" => ucfirst(substr($trade_name, 0, 1)),
                "vendor.trade_name" => $BranchPoObj->fetchVendorDetails($data['vendor_id'])['data'][0]['trade_name'],
                "ref_no" => $data['ref_no'],
                "po_date" => $data['po_date'],
                "po_number" => $data['po_number'],
                "so.totalItems" => $data['totalItems'],
                "so.totalAmount" => $check_cur['data']['currency_name'] . " " . decimalValuePreview($totalAmt),
                "delivery_date" => $data['delivery_date'],
                "use_type" => $data['use_type'],
                "inco_type" => $data['inco_type'],
                "shipLoc" => $ship_location,
                "bill_location" => $bill_location,
                "so.created_by" => getCreatedByUser($data['created_by']),
                "stat.label" => $data['label'],
                "poStatus" => $data['po_status']
            ];
            $sl++;
        }
        $dynamic_data_all = json_encode($dynamic_data_all);
        $exportToExcelAll = exportToExcelAll($dynamic_data_all, $_POST['coloum'], $_POST['sql_data_checkbox']);
        $res = [
            "status" => true,
            "msg" => "alldataSuccess",
            "all_data" => $dynamic_data_all,
            "sql" => $sql_list,
        ];
    } else {
        $res = [
            "status" => false,
            "msg" => "Error!",
            "sql" => $sql_list
        ];
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'CSV allgenerated',
        'csvContentall' => $exportToExcelAll // Encoding CSV content to handle safely in JSON
    ]);
}
