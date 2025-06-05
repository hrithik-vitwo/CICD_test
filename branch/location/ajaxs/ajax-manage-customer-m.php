<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("pagination/common-pagination.php");

// require_once("../../common/exportexcel.php");
require_once("../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');

// // print_r($_POST);
// $currentDate = date('Y-m-d');
// $timestampPreviousDay = strtotime($fromd . ' -1 day');
// $previousDate = date('Y-m-d', $timestampPreviousDay);
// file to be upload from local to git

$BranchSoObj = new BranchSo();
session_start();

if ($_POST['act'] == 'tdata') {
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

            if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif (strcasecmp($data['value'], 'Goods') === 0) {
                $data['value'] = 'material';
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            } elseif ($slag === "totalAmount") {
                $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
            } else if ($slag === 'so.created_by' || $slag === 'created_by') {

                $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
            }else if($slag === 'isMailValid'){
                if($data['value']=='Verified'){
                    $conds .= $slag . " " . $data['operatorName'] . " 'yes'";
                }else if($data['value']=='Not Verified'){
                    $conds .= $slag . " " . $data['operatorName'] . " 'no'";
                }
            }else if($slag === 'customer_status'){
                $conds .= $slag . " " . $data['operatorName'] . " '" . $data['value'] . "%'";
            } 
            else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));


        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sql_list = "SELECT cust.customer_id,cust.customer_code,cust.trade_name,cust.constitution_of_business,cust.customer_gstin,cust.customer_pan,cust.customer_authorised_person_name,cust.customer_authorised_person_email,cust.customer_authorised_person_phone,cust.customer_status,cust.isMailValid,disGroup.customer_discount_group, mrpGroup.customer_mrp_group FROM erp_customer AS cust LEFT JOIN erp_customer_discount_group AS disGroup ON cust.customer_discount_group = disGroup.customer_discount_group_id LEFT JOIN erp_customer_mrp_group AS mrpGroup ON cust.customer_mrp_group = mrpGroup.customer_mrp_group_id WHERE 1 " . $cond . "   AND cust.company_id = $company_id AND cust.company_branch_id = $branch_id AND cust.location_id = $location_id AND cust.customer_status != 'deleted' ORDER BY customer_id DESC";

        // $sql_list="SELECT cust.*, disGroup.customer_discount_group, mrpGroup.customer_mrp_group, COALESCE(primary_addr.customer_address_pin_code, fallback_addr.customer_address_pin_code) AS customer_address_pin_code, COALESCE(primary_addr.customer_address_street_name, fallback_addr.customer_address_street_name) AS customer_address_street_name, COALESCE(primary_addr.customer_address_building_no, fallback_addr.customer_address_building_no) AS customer_address_building_no FROM erp_customer AS cust LEFT JOIN erp_customer_discount_group AS disGroup ON cust.customer_discount_group = disGroup.customer_discount_group_id LEFT JOIN erp_customer_mrp_group AS mrpGroup ON cust.customer_mrp_group = mrpGroup.customer_mrp_group_id LEFT JOIN erp_customer_address AS primary_addr ON cust.customer_id = primary_addr.customer_id AND primary_addr.customer_address_primary_flag = 1 LEFT JOIN ( SELECT customer_id, customer_address_pin_code, customer_address_street_name, customer_address_building_no FROM erp_customer_address GROUP BY customer_id HAVING MIN(customer_address_id) ) AS fallback_addr ON cust.customer_id = fallback_addr.customer_id WHERE 1 AND $cond  cust.company_id = $company_id AND cust.company_branch_id = $branch_id AND cust.location_id = $location_id AND cust.customer_status != 'deleted' ORDER BY cust.customer_id DESC";

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
                $cusIcon;
                if ($data['customer_picture'] != "") {
                    $cusIcon = $data['customer_picture'];
                } else {
                    $cusIcon = ucfirst(substr($data['trade_name'], 0, 1));
                }


                $isMailStatus = ($data['isMailValid'] === 'yes') ? 'Verified' : 'Not Verified';
                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "customerId" => $data['customer_id'],
                    "customer_code" => $data['customer_code'],
                    // "cusIcon" => $cusIcon,
                    "trade_name" => $data['trade_name'],
                    "constitution_of_business" => $data['constitution_of_business'],
                    "customer_gstin" => !empty($data['customer_gstin']) ? $data['customer_gstin'] : "-",
                    "customer_pan" => $data['customer_pan'],
                    "customer_authorised_person_name" => $data['customer_authorised_person_name'],
                    "customer_authorised_person_email" => $data['customer_authorised_person_email'],
                    "customer_authorised_person_phone" => $data['customer_authorised_person_phone'],
                    "customer_status" => $data['customer_status'],
                    "customer_discount_group" => $data['customer_discount_group'],
                    "customer_mrp_group" => $data['customer_mrp_group'],
                    "isMailValid"=>$isMailStatus
                    // "orderVolume" => $getvol['numRows'],
                    // "receipt_amt" => $getorder['data'][0]['sentInvoiceAmount'],
                    // "status" => $formObj

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
                "limit_per_Page" => $limit_per_Page,
                // "csvContent" => $csvContent,
                // "csvContentBypagination" => $csvContentBypagination,
                "sql" => $sql_list,
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
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $formObj = $_POST['formDatas'];
        $cond = "";
        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";

            if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif (strcasecmp($data['value'], 'Goods') === 0) {
                $data['value'] = 'material';
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            } elseif ($slag === "totalAmount") {
                $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
            } else if ($slag === 'so.created_by' || $slag === 'created_by') {

                

                $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
            }else if($slag === 'isMailValid'){
                if($data['value']=='Verified'){
                    $conds .= $slag . " " . $data['operatorName'] . " 'yes'";
                }else if($data['value']=='Not Verified'){
                    $conds .= $slag . " " . $data['operatorName'] . " 'no'";
                }
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));


        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sql_list = "SELECT cust.customer_id,cust.customer_code,cust.trade_name,cust.constitution_of_business,cust.customer_gstin,cust.customer_pan,cust.customer_authorised_person_name,cust.customer_authorised_person_email,cust.customer_authorised_person_phone,cust.customer_status,cust.isMailValid,disGroup.customer_discount_group, mrpGroup.customer_mrp_group FROM erp_customer AS cust LEFT JOIN erp_customer_discount_group AS disGroup ON cust.customer_discount_group = disGroup.customer_discount_group_id LEFT JOIN erp_customer_mrp_group AS mrpGroup ON cust.customer_mrp_group = mrpGroup.customer_mrp_group_id WHERE 1 " . $cond . "   AND cust.company_id = $company_id AND cust.company_branch_id = $branch_id AND cust.location_id = $location_id AND cust.customer_status != 'deleted' ORDER BY customer_id DESC";

        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {
            $sl = 1;
            foreach ($sql_data_all as $data) {
                $isMailStatus = ($data['isMailValid'] === 'yes') ? 'Verified' : 'Not Verified';
                $dynamic_data_all[] = [
                   "sl_no" => $sl,
                    "customerId" => $data['customer_id'],
                    "customer_code" => $data['customer_code'],
                    // "cusIcon" => $cusIcon,
                    "trade_name" => $data['trade_name'],
                    "constitution_of_business" => $data['constitution_of_business'],
                    "customer_gstin" => !empty($data['customer_gstin']) ? $data['customer_gstin'] : "-",
                    "customer_pan" => $data['customer_pan'],
                    "customer_authorised_person_name" => $data['customer_authorised_person_name'],
                    "customer_authorised_person_email" => $data['customer_authorised_person_email'],
                    "customer_authorised_person_phone" => $data['customer_authorised_person_phone'],
                    "customer_status" => $data['customer_status'],
                    "customer_discount_group" => $data['customer_discount_group'],
                    "customer_mrp_group" => $data['customer_mrp_group'],
                    "isMailValid"=>$isMailStatus
                    // "orderVolume" => $getvol['numRows'],
                    // "receipt_amt" => $getorder['data'][0]['sentInvoiceAmount'],
                    // "status" => $formObj

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
            'csvContentall' => $exportToExcelAll, // Encoding CSV content to handle safely in JSON
            'res'=>$res
        ]);
    }
}
