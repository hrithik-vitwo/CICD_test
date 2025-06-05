<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("pagination/common-pagination.php");
require_once("../../common/exportexcel-new.php");
// require_once("../../common/exportexcel.php");

$headerData = array('Content-Type: application/json');
session_start();
$_SESSION['columnMapping'] = $_POST['columnMapping'];
if (isset($_SESSION['columnMapping'])) {
    $columnMapping = $_SESSION['columnMapping'];
}

$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

$GoodsController = new GoodsController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if ($_POST['act'] == 'stocktransferlist') {
            $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? $_POST['limit'] : 25;

            $page_no = isset($_POST['pageNo']) ? (int) $_POST['pageNo'] : 1;
            $page_no = max(1, $page_no);
    
            $offset = ($page_no - 1) * $limit_per_Page;
            $maxPagesl = $page_no * $limit_per_Page;
            $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
            $formObj = $_POST['formDatas'];
            $cond = "";
    
            $implodeFrom = implode('', array_map(function ($slag, $data) {
                $conds = "";
                global $decimalValue;
                if ($slag === 'so.posting_date') {
                    if ($data['operatorName'] === 'BETWEEN') {
                        $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                    } else {
                        $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                    }
                } elseif ($slag === 'so.totalAmount') {
                    $cleanedValue = str_replace(',', '', $data['value']);
                
                        $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
                        $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
                } else if ($slag === 'created_by') {
                    $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
                }else if($slag === 'stat.label' &&  $data['value']=='Expired'){
                    $conds .= $slag . " " . $data['operatorName'] . " '%approved%' AND validityperiod< '".date('Y-m-d')."'";
                } else {
                    $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
                }
    
    
                return !empty($data['value']) ? " AND " . $conds : "";
            }, array_keys($formObj), $formObj));
    
            if (!empty($implodeFrom)) {
                $cond .= $implodeFrom;
            }
    
            $sts = " AND `status` !='deleted'";
    
            $sql_list = "SELECT * FROM `erp_stocktransfer` WHERE 1 " . $cond . " AND company_id = '" . $company_id . "' AND branch_id = '" . $branch_id . "' AND location_id = '" . $location_id . "' ORDER BY transfer_id DESC";
    
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

                $type = $data['destination_type'];
                $movementTypeValID = "";
                if ($type == "storage_location") {
                    $storageArr = $GoodsController->getStoragelocationValue($data['destinationsl']);
                    if (!empty($storageArr)) {
                        $storageArrValue = [
                            $storageArr['warehouse_code'] ?? '-',
                            $storageArr['storage_location_code'] ?? '-',
                            $storageArr['storage_location_name'] ?? '-'
                        ];
                        $storageArrValueSent = implode(" | ", $storageArrValue);
                        $movementTypeValID = $storageArrValueSent;
                    } else {
                        $movementTypeValID = "-";
                    }
                }
                else if ($type == "production_order") {
                    $productionArrValue = queryGet("SELECT po.porCode, po.itemId, po.itemCode, items.itemName FROM erp_production_order AS po LEFT JOIN ERP_INVENTORY_ITEMS AS items ON po.itemId = items.itemId WHERE po.so_por_id = '" . $data['destinationsl'] . "' AND po.company_id = '" . $company_id . "' AND po.branch_id = '" . $branch_id . "' AND po.location_id = '" . $location_id . "'" , false);

                    if (!empty($productionArrValue['data'])) {
                        $productionData = $productionArrValue['data']; // Assuming the first record is needed
                        $productionArrValueSent = implode(" | ", [
                            $productionData['porCode'] ?? '-',
                            $productionData['itemCode'] ?? '-',
                            $productionData['itemName'] ?? '-'
                        ]);
                        $movementTypeValID = $productionArrValueSent;
                    } else {
                        $movementTypeValID = "-";
                    }
                }
                else if ($type == "cost_center")
                {
                    $costCenterArr = queryGet("SELECT CostCenter_code , CostCenter_desc FROM erp_cost_center WHERE CostCenter_id = '" . $data['destinationsl'] . "' AND company_id = '" . $company_id . "' AND branch_id = '" . $branch_id . "' AND location_id = '" . $location_id . "'", false);
                    if (!empty($costCenterArr['data'])) {
                        $costCenterArrValueSent = implode(" | ", [
                            $costCenterArr['data']['CostCenter_code'] ?? '-',
                            $costCenterArr['data']['CostCenter_desc'] ?? '-',
                        ]);
                        $movementTypeValID = $costCenterArrValueSent;
                    } else {
                        $movementTypeValID = "-";
                    }
                }
                else{
                    $movementTypeValID = "-";
                }


                    $dynamic_data[] = [
                        "sl_no" => $sl++,
                        "transfer_id" => $data['transfer_id'],
                        "documentNo" => $data['documentNo'],
                        "destinationsl" => $movementTypeValID,
                        "destination_type" => $data['destination_type'],
                        "remarks" => $data['remarks'] ?? "-",
                        "created_by" => getCreatedByUser($data['created_by']),
                        "productionArrValue" => $productionArrValue
                    ];
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
                    // "csvContent" => $csvContent,
                    // "csvContentBypagination" => $csvContentBypagination,
                    // "sqlMain" => $sqlMainQryObj
    
                ];
            } else {
                $res = [
                    "status" => false,
                    "msg" => "Error!",
                    "sqlMain" => $sqlMainQryObj
                ];
            }
    
            echo json_encode($res);
            // console($costCenterArr);
        }

        if ($_POST['act'] == 'alldata') {


            $formObj = $_POST['formDatas'];
            $cond = "";

            $implodeFrom = implode('', array_map(function ($slag, $data) {
                $conds = "";
                global $decimalValue;
                if ($slag === 'so.posting_date') {
                    if ($data['operatorName'] === 'BETWEEN') {
                        $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                    } else {
                        $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                    }
                } elseif ($slag === 'so.totalAmount') {
                    $cleanedValue = str_replace(',', '', $data['value']);
                
                        $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
                        $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
                } else if ($slag === 'created_by') {
                    $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
                }else if($slag === 'stat.label' &&  $data['value']=='Expired'){
                    $conds .= $slag . " " . $data['operatorName'] . " '%approved%' AND validityperiod< '".date('Y-m-d')."'";
                } else {
                    $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
                }


                return !empty($data['value']) ? " AND " . $conds : "";
            }, array_keys($formObj), $formObj));

            if (!empty($implodeFrom)) {
                $cond .= $implodeFrom;
            }

            $sts = " AND `status` !='deleted'";

            $sql_list = "SELECT * FROM `erp_stocktransfer` WHERE 1 " . $cond . " AND company_id = '" . $company_id . "' AND branch_id = '" . $branch_id . "' AND location_id = '" . $location_id . "' ORDER BY transfer_id DESC";

            $dynamic_data_all = [];
            $sqlMainQryObjall = queryGet($sql_list, true);
            $sql_data_all = $sqlMainQryObjall['data'];
            $num_list = $sqlMainQryObjall['numRows'];
            if ($num_list > 0) {
                foreach ($sql_data_all as $data) {

                if($type == "storage_location") {
                    $storageArr = $GoodsController->getStoragelocationValue($data['dest_storage_location']);
                    if (!empty($storageArr)) {
                        $storageArrValue = [
                            $storageArr['warehouse_code'] ?? '-',
                            $storageArr['storage_location_code'] ?? '-',
                            $storageArr['storage_location_name'] ?? '-'
                        ];
                        $storageArrValueSent = implode(" | ", $storageArrValue);
                        $movementTypeValID = $storageArrValueSent;
                    } else {
                        $storageArrValueSent = "-";
                        $movementTypeValID = $storageArrValueSent;
                    }
                }

                    

                    $dynamic_data_all[] = [
                        "sl_no" => $sl++,
                        "documentNo" => $data['documentNo'],
                        "destinationsl" => $storageArrValueSent,
                        "destination_type" => $data['destination_type'],
                        "remarks" => $data['remarks'],
                        "created_by" => getCreatedByUser($data['created_by']),
                    ];
                }
                $dynamic_data_all = json_encode($dynamic_data_all);
                $exportToExcelAll = exportToExcelAll($dynamic_data_all, $_POST['coloum'], $_POST['sql_data_checkbox']);
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

       
    

 
