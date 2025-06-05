<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("pagination/common-pagination.php");

require_once("../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');
session_start();

if ($_POST['act'] == 'tdata') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $_SESSION['columnMapping'] = $_POST['columnMapping'];
        if (isset($_SESSION['columnMapping'])) {
            $columnMapping = $_SESSION['columnMapping'];
        }
        $flag = 0;
        $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? $_POST['limit'] : 25;

        $page_no = isset($_POST['pageNo']) ? (int)$_POST['pageNo'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $formObj = $_POST['formDatas'];
        $cond = "";
        $implodeFrom = implode('', array_map(function ($slag, $data) use (&$flag) {
            $conds = "";
            global $decimalValue;
            if ($slag === 'grniv.vendorDocumentDate' || $slag === 'grn.grnCreatedAt' || $slag === 'grn.po_date' || $slag === 'grniv.postingDate' || $slag === 'grniv.dueDate') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif ($slag === "grniv.grnTotalAmount" || $slag === "grniv.grnSubTotal" || $slag === "grniv.grnTotalTds"  || $slag === 'grniv.grnTotalTcs' || $slag === 'grniv.dueAmt') {

                // Single value case
                $cleanedValue = str_replace(',', '', $data['value']);

                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');

                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            } elseif ($slag === "grniv.payementMode") {
                $cleanedValue = str_replace(',', '', $data['value']);

                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');

                $conds .= "TRUNCATE(grniv.grnTotalAmount - grniv.dueAmt, " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            } else if ($slag === 'grniv.grnCreatedBy' || $slag === 'created_by') {
                if (in_array($data['operatorName'], ['LIKE', 'NOT LIKE'])) {
                    $opr = ($data['operatorName'] === 'LIKE') ? 'LIKE' : 'NOT LIKE';
                    $resultList = getAdminUserIdByName($data['value']);
                    // $new_slag = 'varient.' . $slag;
        
                    if (strpos($resultList, ',') !== false) {
                        $resultList = (!empty($resultList)) ? $resultList : '0';
                        $opr = ($opr === 'LIKE') ? 'IN' : 'NOT IN';
                        $conds .= $slag . " $opr (" . $resultList . ")";
                    } else {
                        $resultList = (!empty($resultList)) ? $resultList : '0';
                        $conds .= $slag . " $opr '%" . $resultList . "%'";
                    }
                }
            } else if ($slag === "grniv.paymentStatus") {
                if (strcasecmp($data['value'], "Not Yet Due") === 0) {
                    $conds .= " (grniv.paymentStatus LIKE '%15%' AND grniv.dueDate > CURDATE()) ";
                } else if ($data['value'] == 'Reversed') {
                    if ($data['operatorName'] == 'LIKE') {
                        $conds .= "grniv.`grnStatus` = 'reverse'";
                        $flag = 1;
                    }else{
                      $conds .= "grniv.`grnStatus` != 'reverse'";
                        $flag = 1;  
                    }
                } else {
                    $lable = (strcasecmp($data['value'], "Not Yet Due") === 0) ? "payable" : $data['value'];
                    $statusObj = queryGet("SELECT status_id FROM `erp_status_master` WHERE label='" . $lable . "'");

                    if ($statusObj['numRows'] > 0) {
                        $statusId = $statusObj['data']['status_id'];
                        $conds .= $slag . " " . $data['operatorName'] . " '%" . $statusId . "%'";
                    }
                }
            } elseif ($slag === "duePercentage") {
                $conds .= "(grniv.dueAmt / grniv.grnTotalAmount) * 100 " . $data['operatorName'] . " " . $data['value'];
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return $data['value'] !== '' ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));


        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }
        $sts = '';
        if ($flag == 0) {
            $sts = " AND grniv.`grnStatus`!='deleted'";
        } else {
            $sts = '';
        }


        $sql_list = "SELECT grniv.*, grn.`grnCreatedAt` AS grnDate, grn.`po_date` AS poDate FROM `" . ERP_GRNINVOICE . "` AS grniv LEFT JOIN `erp_grn` AS grn ON grn.`grnId` = grniv.`grnId` WHERE 1 " . $cond . " AND grniv.`companyId`='$company_id' AND grniv.`branchId`='$branch_id' AND grniv.`locationId`='$location_id' " . $sts . " ORDER BY grniv.`postingDate` DESC";

        $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);

        $dynamic_data = [];
        $num_list = $sqlMainQryObj['numRows'];
        $sql_data = $sqlMainQryObj['data'];
        $output = "";
        $limitText = "";
        $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        $tcs = $data['grnTotalTcs'] ?? 0;

        if ($num_list > 0) {
            foreach ($sql_data as $data) {

                $tax_sum = 0.00;
                if ($companyCountry == 103) {
                    $tax_sum = $data['grnTotalCgst'] + $data['grnTotalSgst'] + $data['grnTotalIgst'];
                } else {
                    $gstd = json_decode($data['taxComponents'], true);

                    // Calculate the sum of all tax amounts

                    foreach ($gstd as $item) {
                        $tax_sum += (float)$item['taxAmount'];
                    }
                }
                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "grniv.vendorCode" => $data['vendorCode'],
                    "grniv.vendorName" => $data['vendorName'],
                    "grniv.vendorDocumentNo" => $data['vendorDocumentNo'],
                    "grniv.vendorDocumentDate" => formatDateWeb($data['vendorDocumentDate']),
                    "grniv.grnCode" => $data['grnCode'],
                    "grn.grnCreatedAt" => formatDateWeb($data['grnDate']),
                    "grniv.grnPoNumber" => $data['grnPoNumber'],
                    "grn.po_date" => formatDateWeb($data['poDate']),
                    "grniv.grnIvCode" => $data['grnIvCode'],
                    "grniv.postingDate" => formatDateWeb($data['postingDate']),
                    "grniv.dueDate" => formatDateWeb($data['dueDate']),
                    "grniv.grnSubTotal" => decimalValuePreview($data['grnSubTotal']),
                    "gst" => decimalValuePreview($tax_sum),
                    "grniv.grnTotalTds" => decimalValuePreview($data['grnTotalTds']),
                    "grniv.grnTotalTcs" => decimalValuePreview($data['grnTotalTcs']),
                    "grniv.grnTotalAmount" => decimalValuePreview($data['grnTotalAmount']),
                    "grniv.payementMode" => decimalValuePreview($data['grnTotalAmount'] -  $data['dueAmt']),
                    "grniv.dueAmt" => decimalValuePreview($data['dueAmt']),
                    "grniv.paymentStatus" => fetchStatusMasterByCode($data['paymentStatus'])['data']['label'],
                    "grnIvId" => $data['grnIvId'],
                    "grnId" => $data['grnId'],
                    "grnStatus" => $data['grnStatus'],
                    "grniv.grnCreatedBy" => getCreatedByUser($data['grnCreatedBy'])
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
                "sql" => $sqlMainQryObj['sql']
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
            global $decimalValue;

            if ($slag === 'grniv.vendorDocumentDate' || $slag === 'grn.grnCreatedAt' || $slag === 'grn.po_date' || $slag === 'grniv.postingDate' || $slag === 'grniv.dueDate') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif ($slag === "grniv.grnTotalAmount" || $slag === "grniv.grnSubTotal" || $slag === "grniv.grnTotalTds"  || $slag === 'grniv.grnTotalTcs' || $slag === 'grniv.dueAmt') {

                // Single value case
                $cleanedValue = str_replace(',', '', $data['value']);

                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');

                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            } elseif ($slag === "grniv.payementMode") {
                $cleanedValue = str_replace(',', '', $data['value']);

                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');

                $conds .= "TRUNCATE(grniv.grnTotalAmount - grniv.dueAmt, " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            } else if ($slag === 'grniv.grnCreatedBy' || $slag === 'created_by') {
                $resultList = getAdminUserIdByName($data['value']);
                $conds .= $slag . " IN  " . " (" . $resultList . ")";
            } else if ($slag === "grniv.paymentStatus") {
                $lable = (strcasecmp($data['value'], "Not Yet Due") === 0) ? "payable" : $data['value'];
                $statusObj = queryGet("SELECT status_id FROM `erp_status_master` WHERE label='" . $lable . "'");
                if ($statusObj['numRows'] > 0) {
                    $statusId = $statusObj['data']['status_id'];
                    $conds .= $slag . " " . $data['operatorName'] . " '%" . $statusId . "%'";
                }
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));


        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sts = " AND grniv.`grnStatus`!='deleted'";

        $sql_list = "SELECT grniv.*, grn.`grnCreatedAt` AS grnDate, grn.`po_date` AS poDate FROM `" . ERP_GRNINVOICE . "` AS grniv LEFT JOIN `erp_grn` AS grn ON grn.`grnId` = grniv.`grnId` WHERE 1 " . $cond . " AND grniv.`companyId`='$company_id' AND grniv.`branchId`='$branch_id' AND grniv.`locationId`='$location_id' " . $sts . " ORDER BY grniv.`postingDate` DESC";

        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {
            $sl = 1;
            foreach ($sql_data_all as $data) {
                $tax_sum = 0.00;
                if ($companyCountry == 103) {
                    $tax_sum = $data['grnTotalCgst'] + $data['grnTotalSgst'] + $data['grnTotalIgst'];
                } else {
                    $gstd = json_decode($data['taxComponents'], true);

                    // Calculate the sum of all tax amounts

                    foreach ($gstd as $item) {
                        $tax_sum += (float)$item['taxAmount'];
                    }
                }
                $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "grniv.vendorCode" => $data['vendorCode'],
                    "grniv.vendorName" => $data['vendorName'],
                    "grniv.vendorDocumentNo" => $data['vendorDocumentNo'],
                    "grniv.vendorDocumentDate" => formatDateWeb($data['vendorDocumentDate']),
                    "grniv.grnCode" => $data['grnCode'],
                    "grn.grnCreatedAt" => formatDateWeb($data['grnDate']),
                    "grniv.grnPoNumber" => $data['grnPoNumber'],
                    "grn.po_date" => formatDateWeb($data['poDate']),
                    "grniv.grnIvCode" => $data['grnIvCode'],
                    "grniv.postingDate" => formatDateWeb($data['postingDate']),
                    "grniv.dueDate" => formatDateWeb($data['dueDate']),
                    "grniv.grnSubTotal" => decimalValuePreview($data['grnSubTotal']),
                    "gst" => decimalValuePreview($tax_sum),
                    "grniv.grnTotalTds" => decimalValuePreview($data['grnTotalTds']),
                    "grniv.grnTotalTcs" => decimalValuePreview($data['grnTotalTcs']),
                    "grniv.grnTotalAmount" => decimalValuePreview($data['grnTotalAmount']),
                    "grniv.payementMode" => decimalValuePreview($data['grnTotalAmount'] -  $data['dueAmt']),
                    "grniv.dueAmt" => decimalValuePreview($data['dueAmt']),
                    "grniv.paymentStatus" => fetchStatusMasterByCode($data['paymentStatus'])['data']['label'],
                    "grnIvId" => $data['grnIvId'],
                    "grnId" => $data['grnId'],
                    "grnStatus" => $data['grnStatus'],
                    "grniv.grnCreatedBy" => getCreatedByUser($data['grnCreatedBy'])
                ];
                $sl++;
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
