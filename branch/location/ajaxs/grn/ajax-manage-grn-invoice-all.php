<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../pagination/common-pagination.php");
require_once("../../../common/exportexcel-new.php");
require_once("../../../../app/v1/functions/branch/func-discount-controller.php");
$headerData = array('Content-Type: application/json');

$CustomerDiscountControllerObj = new CustomerDiscountGroupController();

if ($_POST['act'] == 'grnInvoiceAll') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? $_POST['limit'] : 25;

        $page_no = isset($_POST['pageNo']) ? (int) $_POST['pageNo'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $formObj = $_POST['formDatas'];
        $cond = "";
        // $cond = "AND DATE(so_date) BETWEEN '" . $previousDate . "' AND '" . $currentDate . "'";


        $implodeFrom = implode('', array_map(function ($slag, $data) {
            global $decimalValue;
            $conds = "";
            if ($slag === 'updated_at' || $slag === 'created_at' || $slag === 'po_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } else if ($slag === 'created_by' || $slag === 'updated_by') {
                if ($data['operatorName'] === 'LIKE' || $data['operatorName'] === 'NOT LIKE') {
                    $opr = $data['operatorName'] === 'LIKE' ? 'IN' : 'NOT IN';
                    $resultList = getAdminUserIdByName($data['value']);
                    $conds .= $slag . " $opr  " . " (" . $resultList . ")";
                }
            } else if($slag === 'totalAmount'){
                $cleanedValue = str_replace(',', '', $data['value']);
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $cleanedValue;
            }else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        // console($implodeFrom);
        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }



        // $sts = " AND `so`.status !='deleted'";


        $sql_list = 'SELECT erp_branch_purchase_order.* , erp_vendor_details.trade_name, erp_vendor_details.vendor_code,  erp_vendor_details.vendor_id AS vendor_id_details FROM `erp_branch_purchase_order` LEFT JOIN `erp_vendor_details` ON erp_vendor_details.vendor_id = erp_branch_purchase_order.vendor_id WHERE erp_branch_purchase_order.`company_id`=' . $company_id . ' AND erp_branch_purchase_order.`branch_id`=' . $branch_id . ' AND erp_branch_purchase_order.`location_id`=' . $location_id . ' AND erp_branch_purchase_order.`po_status`="9" '.$cond.' ORDER BY erp_branch_purchase_order.po_id DESC ';

        $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);

        $dynamic_data = [];
        $num_list = $sqlMainQryObj['numRows'];
        $sql_data = $sqlMainQryObj['data'];
        $output = "";
        $limitText = "";
        $sl = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        if ($num_list > 0) {
            foreach ($sql_data as $data) {
                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "po_number"    => $data['po_number'] ?? "-",
                    "po_date"      => formatDateWeb($data['po_date']) ?? "-",
                    "ref_no"       => $data['ref_no'] ?? "-",
                    "trade_name"   => $data['trade_name'] ?? "-",
                    "vendor_code"  => $data['vendor_code'] ?? "-",
                    "use_type"     => $data['use_type'] ?? "-",
                    "totalAmount"  => decimalValuePreview($data['totalAmount']) ?? "-",
                    "vendor_id" => $data['vendor_id'] ?? "-",
                    "po_id" => $data['po_id'] ?? "-",
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



            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "sqlMain" => $sql_data,
                "the_query" => $sqlMainQryObj['sql'],
                "sqlRowCount" => $sqlRowCount

            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sql_list,
                "sqlMain" => $sqlMainQryObj,
                // "conds" => console($cond)
            ];
        }

        echo json_encode($res);
    }
}

if ($_POST['act'] == 'alldata') {
    $formObj = $_POST['formDatas'];
    $cond = "";

    $implodeFrom = implode('', array_map(function ($slag, $data) {
        global $decimalValue;
        $conds = "";
        if ($slag === 'updated_at' || $slag === 'created_at' || $slag === 'po_date') {
            if ($data['operatorName'] === 'BETWEEN') {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
            } else {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
            }
        } else if ($slag === 'created_by' || $slag === 'updated_by') {
            if ($data['operatorName'] === 'LIKE' || $data['operatorName'] === 'NOT LIKE') {
                $opr = $data['operatorName'] === 'LIKE' ? 'IN' : 'NOT IN';
                $resultList = getAdminUserIdByName($data['value']);
                $conds .= $slag . " $opr  " . " (" . $resultList . ")";
            }
        } else if($slag === 'totalAmount'){
            $cleanedValue = str_replace(',', '', $data['value']);
            $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $cleanedValue;
        }else {
            $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
        }

        return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    // console($implodeFrom);
    if (!empty($implodeFrom)) {
        $cond .= $implodeFrom;
    }



    // $sts = " AND `so`.status !='deleted'";


    $sql_list = 'SELECT erp_branch_purchase_order.* , erp_vendor_details.trade_name, erp_vendor_details.vendor_code,  erp_vendor_details.vendor_id AS vendor_id_details FROM `erp_branch_purchase_order` LEFT JOIN `erp_vendor_details` ON erp_vendor_details.vendor_id = erp_branch_purchase_order.vendor_id WHERE erp_branch_purchase_order.`company_id`=' . $company_id . ' AND erp_branch_purchase_order.`branch_id`=' . $branch_id . ' AND erp_branch_purchase_order.`location_id`=' . $location_id . ' AND erp_branch_purchase_order.`po_status`="9" '.$cond.' ORDER BY erp_branch_purchase_order.po_id DESC ';

    $dynamic_data_all = [];
    $sqlMainQryObjall = queryGet($sql_list, true);
    $sql_data_all = $sqlMainQryObjall['data'];
    $num_list =  $sqlMainQryObjall['numRows'];
    if ($num_list > 0) {
        foreach ($sql_data_all as $data) {

            $dynamic_data_all[] = [
                "sl_no" => $sl++,
                "po_number"    => $data['po_number'] ?? "-",
                "po_date"      => formatDateWeb($data['po_date']) ?? "-",
                "ref_no"       => $data['ref_no'] ?? "-",
                "trade_name"   => $data['trade_name'] ?? "-",
                "vendor_code"  => $data['vendor_code'] ?? "-",
                "use_type"     => $data['use_type'] ?? "-",
                "totalAmount"  => decimalValuePreview($data['totalAmount']) ?? "-",
            ];
        }
        $dynamic_data_all = json_encode($dynamic_data_all);
        $exportToExcelAll = exportToExcelAll($dynamic_data_all, $_POST['coloum'], $_POST['sql_data_checkbox']);
        $res = [
            "status" => true,
            "msg" => "CSV all generated",
            'csvContentall' => $exportToExcelAll,
            "sql" => $sql_list,
            "data_all" => $dynamic_data_all
        ];
        }
         else {
        $res = [
          "status" => false,
          "msg" => "Error!",
          "sql" => $sql_list
        ];
      }
      echo json_encode($res);
}
