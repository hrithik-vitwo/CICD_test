<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../pagination/common-pagination.php");
require_once("../../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');


if ($_POST['act'] == 'vendorpaymentAll') {
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
            $conds = "";
            global $decimalValue;
            
            // General condition handling
             if ($slag === "grniv.dueAmt" && is_numeric(str_replace(',', '', $data['value']))) {
                $cleanedValue = str_replace(',', '', $data['value']);
        
                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
        
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
              } 
              else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
              }           
           return $data['value'] !== '' ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));
        
        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }
        



        // $sts = " AND `so`.status !='deleted'";


        // $sql_list = "SELECT *, varient.`created_by` AS created, varient.`created_at` AS `time` 
        //                 FROM `erp_mrp_variant` AS varient  
        //                 LEFT JOIN `erp_mrp_territory` AS territory ON territory.territory_id = varient.territory 
        //                 LEFT JOIN `erp_customer_mrp_group` AS customer_group ON customer_group.customer_mrp_group_id = varient.customer_group 
        //                 WHERE 1 $cond AND varient.`company_id`=$company_id 
        //                 AND varient.`branch_id`=$branch_id 
        //                 AND varient.`location_id`=$location_id 
        //                 ORDER BY varient.mrp_id DESC  ";

        $sts = " AND grniv.`grnStatus`!='deleted'";

        $sql_list = "SELECT req.initiate_id AS request_id, req.code AS request_code, grniv.vendorId AS vendor_id, grniv.vendorCode, grniv.vendorName, grniv.vendorGstin, grniv.dueAmt, bank.vendor_bank_id AS bank_id, bank.vendor_bank_name AS bank_name, bank.vendor_bank_account_no AS bank_account_no FROM erp_payment_initiate_request AS req LEFT JOIN erp_grninvoice AS grniv ON req.invoice_id = grniv.grnIvId LEFT JOIN erp_vendor_bank_details AS bank ON grniv.vendorId = bank.vendor_id WHERE grniv.companyId = $company_id AND grniv.branchId = $branch_id AND grniv.locationId = $location_id AND grniv.paymentStatus != 4 " . $sts . " " . $cond . " GROUP BY req.initiate_id, req.code, grniv.vendorCode, grniv.vendorName ORDER BY request_id DESC";



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
                    "req.code" => $data['request_code'] ?? "-",
                    "grniv.vendorCode" => $data['vendorCode'] ?? "-",
                    "grniv.vendorName" => $data['vendorName'] ?? "-",
                    "grniv.vendorGstin" => $data['vendorGstin'] ?? "-",
                    "bank.vendor_bank_name" => $data['bank_name'] ?? "-",
                    "bank.vendor_bank_account_no" => $data['bank_account_no'] ?? "-",
                    "grniv.dueAmt" => decimalValuePreview($data['dueAmt']) ?? "-",
                    "vendor_id" => $data['vendor_id']
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
                "the_query" => $sqlMainQryObj['sql']

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
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $formObj = $_POST['formDatas'];
        $cond = "";

        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            global $decimalValue;
            
            // General condition handling
            if ($slag === "grniv.dueAmt") {
                $cleanedValue = str_replace(',', '', $data['value']);
        
                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
        
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
              } 
              else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
              }           
            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));
        
        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }
        


        $sts = " AND grniv.`grnStatus`!='deleted'";

        $sql_list = "SELECT req.initiate_id AS request_id, req.code AS request_code, grniv.vendorId AS vendor_id, grniv.vendorCode, grniv.vendorName, grniv.vendorGstin, grniv.dueAmt, bank.vendor_bank_id AS bank_id, bank.vendor_bank_name AS bank_name, bank.vendor_bank_account_no AS bank_account_no FROM erp_payment_initiate_request AS req LEFT JOIN erp_grninvoice AS grniv ON req.invoice_id = grniv.grnIvId LEFT JOIN erp_vendor_bank_details AS bank ON grniv.vendorId = bank.vendor_id WHERE grniv.companyId = $company_id AND grniv.branchId = $branch_id AND grniv.locationId = $location_id AND grniv.paymentStatus != 4 " . $sts . " " . $cond . " GROUP BY req.initiate_id, req.code, grniv.vendorCode, grniv.vendorName ORDER BY request_id DESC";

        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {
            $sl = 1;
            foreach ($sql_data_all as $data) {

                $dynamic_data_all[] =  [
                    "sl_no" => $sl,
                    "req.code" => $data['request_code'] ?? "-",
                    "grniv.vendorCode" => $data['vendorCode'] ?? "-",
                    "grniv.vendorName" => $data['vendorName'] ?? "-",
                    "grniv.vendorGstin" => $data['vendorGstin'] ?? "-",
                    "bank.vendor_bank_name" => $data['bank_name'] ?? "-",
                    "bank.vendor_bank_account_no" => $data['bank_account_no'] ?? "-",
                    "grniv.dueAmt" => decimalValuePreview($data['dueAmt']) ?? "-",
                    // "vendor_id" => $data['vendor_id']
                ];
                $sl++;
            }
            $dynamic_data_all=json_encode($dynamic_data_all);
            $exportToExcelAll =exportToExcelAll($dynamic_data_all,$_POST['coloum'],$_POST['sql_data_checkbox']);
            $res = [
                "status" => true,
                "msg" => "CSV all generated",
                'csvContentall' => $exportToExcelAll,
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