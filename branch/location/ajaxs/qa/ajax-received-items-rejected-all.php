<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../pagination/common-pagination.php");
require_once("../../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');



if ($_POST['act'] == 'QARejectedList') {
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
            global $decimalQuantity;
            global $decimalValue;
            // Handle date fields correctly
            if (in_array($slag, ['updated_at', 'created_at', 'valid_from', 'valid_upto' , 'stocklog.bornDate'])) {
                // $new_slag = 'varient.' . $slag;
                
                if ($data['operatorName'] === 'BETWEEN' && is_array($data['value'])) {
                    $conds .= " DATE($slag) BETWEEN '" . $data['value']['fromDate'] . "' AND '" . $data['value']['toDate'] . "' ";
                } else {
                    $conds .= " DATE($slag) " . $data['operatorName'] . " '" . $data['value'] . "' ";
                }
            }
            // Handle 'created_by' and 'updated_by' conditions
            else if ($slag === 'created_by' || $slag === 'updated_by') {
                if (in_array($data['operatorName'], ['LIKE', 'NOT LIKE'])) {
                    $opr = ($data['operatorName'] === 'LIKE') ? 'LIKE' : 'NOT LIKE';
                    $resultList = getAdminUserIdByName($data['value']);
                    $new_slag = 'varient.' . $slag;
        
                    if (strpos($resultList, ',') !== false) {
                        $opr = ($opr === 'LIKE') ? 'IN' : 'NOT IN';
                        $conds .= $new_slag . " $opr (" . $resultList . ")";
                    } else {
                        $conds .= $new_slag . " $opr '%" . $resultList . "%'";
                    }
                }
            }
        
            // Handle minimum_valueQuantity condition       
            // General fallback condition, EXCLUDING date fields
            else if (!in_array($slag, ['updated_at', 'created_at', 'valid_from', 'valid_upto'])) {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }
        
            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));
        
        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }
        



        // $sts = " AND `so`.status !='deleted'";


        $sql_list = "SELECT qa.`qa_log_Id`, item.itemName,item.itemCode, stocklog.logRef, stocklog.stockLogId, stocklog.bornDate, grn.grnPoNumber, grn.vendorDocumentNo, grn.vendorCode, grn.vendorName, uom.uomName FROM `erp_qa_log` AS qa LEFT JOIN `erp_inventory_stocks_log` AS stocklog ON stocklog.`stockLogId` = qa.`stock_log_id` LEFT JOIN `erp_storage_location` AS stloc ON stloc.`storage_location_id` = stocklog.`storageLocationId` LEFT JOIN `erp_grn` AS grn ON grn.`grnCode` = stocklog.`logRef` LEFT JOIN `erp_inventory_items` AS item ON item.`itemId` = stocklog.`itemId` LEFT JOIN `erp_inventory_mstr_uom` AS uom ON uom.`uomId` = item.`baseUnitMeasure` WHERE rejected > 0 $cond AND qa.`companyId` = '$company_id' AND qa.`branchId` = '$branch_id' AND qa.`locationId` = '$location_id' GROUP BY `stock_log_id` DESC";

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

                $stock_id = $data["stockLogId"];
                $get_last_updated_qty = queryGet("SELECT * FROM `erp_qa_summary` WHERE `companyId` = '$company_id' AND `branchId`='$branch_id' AND `locationId`='$location_id' AND `stock_log_id`='$stock_id'", false);
                $received_qty = $data["itemQty"];


                if ($get_last_updated_qty["numRows"] == 0) {
                    $remaining_qty = $data["itemQty"] ?? 0;
                    $status = 0;
                } else {
                    $remaining_qty = $data["itemQty"] - (($get_last_updated_qty["data"]["passed"] ?? 0) + ($get_last_updated_qty["data"]["rejected"] ?? 0));
                    $status = $get_last_updated_qty["data"]["status"];
                }

                // echo $data['minimumDetails'];


                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "item.itemName" => $data["itemName"],
                    "item.itemCode" => $data["itemCode"],
                    "stocklog.logRef" => $data["logRef"],
                    "stocklog.bornDate" => formatDateORDateTime($data["bornDate"]),
                    "failQty" => decimalQuantityPreview($get_last_updated_qty["data"]["rejected"]) . " " . $data["uomName"],
                    "grn.grnPoNumber" => $data["grnPoNumber"],
                    "grn.vendorDocumentNo" => $data["vendorDocumentNo"],
                    "grn.vendorName" => $data["vendorName"],
                    "grn.vendorCode" => $data["vendorCode"],
                    "qa_log_Id" => $data['qa_log_Id'],
                    "stocklog.stockLogId" =>$data['stockLogId']
                ];
                $sl++;
            }
            $output .= "</table>";
            $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";

            // $sqlRowCount = ""
            $queryset = queryGet($sqlRowCount);
            $totalRows = $queryset['data']['row_count'];
            $total_page = ceil($totalRows / $limit_per_Page);
            $output .= pagiNation($page_no, $total_page);

            $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';

            // console($sqlRowCount);

            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "sqlMain" => $sql_data,
                "the_query" => $sqlMainQryObj['sql'],
                "row_count" => $queryset['sql'],
                "formObj"=>$formObj

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
            global $decimalQuantity;
            global $decimalValue;
            // Handle date fields correctly
            if (in_array($slag, ['updated_at', 'created_at', 'valid_from', 'valid_upto' , 'stocklog.bornDate'])) {
                // $new_slag = 'varient.' . $slag;
                
                if ($data['operatorName'] === 'BETWEEN' && is_array($data['value'])) {
                    $conds .= " DATE($slag) BETWEEN '" . $data['value']['fromDate'] . "' AND '" . $data['value']['toDate'] . "' ";
                } else {
                    $conds .= " DATE($slag) " . $data['operatorName'] . " '" . $data['value'] . "' ";
                }
            }
            // Handle 'created_by' and 'updated_by' conditions
            else if ($slag === 'created_by' || $slag === 'updated_by') {
                if (in_array($data['operatorName'], ['LIKE', 'NOT LIKE'])) {
                    $opr = ($data['operatorName'] === 'LIKE') ? 'LIKE' : 'NOT LIKE';
                    $resultList = getAdminUserIdByName($data['value']);
                    $new_slag = 'varient.' . $slag;
        
                    if (strpos($resultList, ',') !== false) {
                        $opr = ($opr === 'LIKE') ? 'IN' : 'NOT IN';
                        $conds .= $new_slag . " $opr (" . $resultList . ")";
                    } else {
                        $conds .= $new_slag . " $opr '%" . $resultList . "%'";
                    }
                }
            }
        
            // Handle minimum_valueQuantity condition       
            // General fallback condition, EXCLUDING date fields
            else if (!in_array($slag, ['updated_at', 'created_at', 'valid_from', 'valid_upto'])) {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }
        
            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));
        
        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sql_list = "SELECT qa.`qa_log_Id`, item.itemName, stocklog.logRef, stocklog.stockLogId, stocklog.bornDate, grn.grnPoNumber, grn.vendorDocumentNo, grn.vendorCode, grn.vendorName, uom.uomName FROM `erp_qa_log` AS qa LEFT JOIN `erp_inventory_stocks_log` AS stocklog ON stocklog.`stockLogId` = qa.`stock_log_id` LEFT JOIN `erp_storage_location` AS stloc ON stloc.`storage_location_id` = stocklog.`storageLocationId` LEFT JOIN `erp_grn` AS grn ON grn.`grnCode` = stocklog.`logRef` LEFT JOIN `erp_inventory_items` AS item ON item.`itemId` = stocklog.`itemId` LEFT JOIN `erp_inventory_mstr_uom` AS uom ON uom.`uomId` = item.`baseUnitMeasure` WHERE rejected > 0 AND qa.`companyId` = '$company_id' AND qa.`branchId` = '$branch_id' AND qa.`locationId` = '$location_id' GROUP BY `stock_log_id` DESC";

        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {
            $sl = 1;
            foreach ($sql_data_all as $data) {

                
                $stock_id = $data["stockLogId"];
                $get_last_updated_qty = queryGet("SELECT * FROM `erp_qa_summary` WHERE `companyId` = '$company_id' AND `branchId`='$branch_id' AND `locationId`='$location_id' AND `stock_log_id`='$stock_id'", false);
                $received_qty = $data["itemQty"];



                $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "item.itemName" => $data["itemName"],
                    "item.itemCode" => $data["itemCode"],
                    "stocklog.logRef" => $data["logRef"],
                    "stocklog.bornDate" => formatDateORDateTime($data["bornDate"]),
                    "failQty" => decimalQuantityPreview($get_last_updated_qty["data"]["rejected"]) . " " . $data["uomName"],
                    "grn.grnPoNumber" => $data["grnPoNumber"],
                    "grn.vendorDocumentNo" => $data["vendorDocumentNo"],
                    "grn.vendorName" => $data["vendorName"],
                    "grn.vendorCode" => $data["vendorCode"]
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