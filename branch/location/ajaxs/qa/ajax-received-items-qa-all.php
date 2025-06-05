<?php

require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../pagination/common-pagination.php");
require_once("../../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
if ($_POST['act'] == 'QA_AllList') {
        $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? $_POST['limit'] : 25;

        $page_no = isset($_POST['pageNo']) ? (int) $_POST['pageNo'] : 1;
        $page_no = max(1, $page_no);
        $typeGoods = isset($_POST['typeGoods']) ? trim($_POST['typeGoods']) : '';
        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $formObj = $_POST['formDatas'];
        $cond = "";
        // $cond = "AND DATE(so_date) BETWEEN '" . $previousDate . "' AND '" . $currentDate . "'";

        if ($typeGoods ==  'RM') {
            $itemCond = "AND item.`goodsType` IN('1')";
        } else if ($typeGoods == 'FG') {
            $itemCond = "AND item.`goodsType` IN ('3','4') ";
        } else if ($typeGoods == 'SFG') {
            $itemCond = "AND item.`goodsType` IN ('2')";
        } else {
            $itemCond = '';
        }

        $_SESSION['itemCond'] = $itemCond;

        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            global $decimalQuantity;
            global $decimalValue;
            // Handle date fields correctly
            if (in_array($slag, ['updated_at', 'created_at', 'valid_from', 'valid_upto', 'stocklog.bornDate'])) {
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

        $sql_list = "SELECT stocklog.`stockLogId`, stocklog.`storageLocationId`, stocklog.`storageType`, SUM(stocklog.itemQty) AS itemQty, stocklog.`remainingQty`, stocklog.`logRef`, stocklog.`bornDate`, item.`itemName`,item.itemCode, uom.uomName, grn.`grnPoNumber`, grn.`vendorDocumentNo`, grn.`vendorName`, grn.`vendorCode` FROM `erp_inventory_stocks_log` AS stocklog LEFT JOIN `erp_storage_location` AS stloc ON stloc.`storage_location_id` = stocklog.`storageLocationId` LEFT JOIN `erp_grn` AS grn ON grn.`grnCode` = stocklog.`logRef` LEFT JOIN `erp_inventory_items` AS item ON item.`itemId` = stocklog.`itemId` LEFT JOIN `erp_inventory_mstr_uom` AS uom ON uom.`uomId` = item.`baseUnitMeasure` WHERE 1 $cond AND stocklog.`companyId` = '$company_id' AND stocklog.`branchId` = '$branch_id' AND stocklog.`locationId` = '$location_id' AND( stocklog.`refActivityName` = 'GRN' OR stocklog.`refActivityName` = 'PRODUCTION') AND stocklog.`storageType` = 'QaLocation' $itemCond GROUP BY item.`itemId`, stocklog.`logRef`, stocklog.`storageLocationId` ORDER BY stocklog.stockLogId DESC";

        $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);

        $dynamic_data = [];
        $num_list = $sqlMainQryObj['numRows'];
        $sql_data = $sqlMainQryObj['data'];
        $output = "";
        $limitText = "";
        $sl = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        if ($num_list > 0) {
            foreach ($sql_data as $one) {
                $stock_id = $one["stockLogId"];
                $get_last_updated_qty = queryGet("SELECT * FROM `erp_qa_summary` WHERE `companyId` = '$company_id' AND `branchId`='$branch_id' AND `locationId`='$location_id' AND `stock_log_id`='$stock_id'", false);

                $received_qty = $one["itemQty"];

                if ($get_last_updated_qty["numRows"] == 0) {
                    $remaining_qty = $received_qty;
                    $status = 0;
                    $last_passed = 0;
                    $last_rejected = 0;
                } else {
                    $last_passed = $get_last_updated_qty["data"]["passed"] ?? 0;
                    $last_rejected = $get_last_updated_qty["data"]["rejected"] ?? 0;
                    $remaining_qty = $received_qty - ($last_passed + $last_rejected);
                    $status = $get_last_updated_qty["data"]["status"];
                }

                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "item.itemName" => $one['itemName'],
                    "item.itemCode" => $one['itemCode'],
                    "stocklog.logRef" => $one["logRef"] ?? "",
                    "received_qty" => decimalQuantityPreview($received_qty) . " " . ($one["uomName"] ?? ""),
                    "passed_qty" => decimalQuantityPreview($last_passed) . " " . ($one["uomName"] ?? ""),
                    "rejected_qty" => decimalQuantityPreview($last_rejected) . " " . ($one["uomName"] ?? ""),
                    "remaining_qty" => decimalQuantityPreview($remaining_qty) . " " . ($one["uomName"] ?? ""),
                    "stocklog.bornDate" => formatDateORDateTime($one["bornDate"] ?? ""),
                    "grn.grnPoNumber" => $one["grnPoNumber"] ?? "",
                    "grn.vendorDocumentNo" => $one["vendorDocumentNo"] ?? "",
                    "grn.vendorName" => $one["vendorName"] ?? "",
                    "grn.vendorCode" => $one["vendorCode"] ?? "",
                    "stocklog.stockLogId" => $stock_id
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
    if ($_POST['act'] == 'alldata') {

        $formObj = $_POST['formDatas'];
        $cond = "";


        $itemCond = isset($_SESSION['itemCond']) ? $_SESSION['itemCond'] : '';


        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            global $decimalQuantity;
            global $decimalValue;
            // Handle date fields correctly
            if (in_array($slag, ['updated_at', 'created_at', 'valid_from', 'valid_upto', 'stocklog.bornDate'])) {
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

        $sql_list = "SELECT stocklog.`stockLogId`, stocklog.`storageLocationId`, stocklog.`storageType`, SUM(stocklog.itemQty) AS itemQty, stocklog.`remainingQty`, stocklog.`logRef`, stocklog.`bornDate`, item.`itemName`,item.itemCode, uom.uomName, grn.`grnPoNumber`, grn.`vendorDocumentNo`, grn.`vendorName`, grn.`vendorCode` FROM `erp_inventory_stocks_log` AS stocklog LEFT JOIN `erp_storage_location` AS stloc ON stloc.`storage_location_id` = stocklog.`storageLocationId` LEFT JOIN `erp_grn` AS grn ON grn.`grnCode` = stocklog.`logRef` LEFT JOIN `erp_inventory_items` AS item ON item.`itemId` = stocklog.`itemId` LEFT JOIN `erp_inventory_mstr_uom` AS uom ON uom.`uomId` = item.`baseUnitMeasure` WHERE 1 $cond AND stocklog.`companyId` = '$company_id' AND stocklog.`branchId` = '$branch_id' AND stocklog.`locationId` = '$location_id' AND( stocklog.`refActivityName` = 'GRN' OR stocklog.`refActivityName` = 'PRODUCTION') AND stocklog.`storageType` = 'QaLocation' $itemCond GROUP BY item.`itemId`, stocklog.`logRef`, stocklog.`storageLocationId` ORDER BY stocklog.stockLogId DESC";

        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {
            $sl = 1;
            foreach ($sql_data_all as $one) {

                $stock_id = $one["stockLogId"];
                $get_last_updated_qty = queryGet("SELECT * FROM `erp_qa_summary` WHERE `companyId` = '$company_id' AND `branchId`='$branch_id' AND `locationId`='$location_id' AND `stock_log_id`='$stock_id'", false);

                $received_qty = $one["itemQty"];

                if ($get_last_updated_qty["numRows"] == 0) {
                    $remaining_qty = $received_qty;
                    $status = 0;
                    $last_passed = 0;
                    $last_rejected = 0;
                } else {
                    $last_passed = $get_last_updated_qty["data"]["passed"] ?? 0;
                    $last_rejected = $get_last_updated_qty["data"]["rejected"] ?? 0;
                    $remaining_qty = $received_qty - ($last_passed + $last_rejected);
                    $status = $get_last_updated_qty["data"]["status"];
                }

                $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "item.itemName" => $one['itemName'],
                    "item.itemCode" => $one['itemCode'],
                    "stocklog.logRef" => $one["logRef"] ?? "",
                    "received_qty" => decimalQuantityPreview($received_qty) . " " . ($one["uomName"] ?? ""),
                    "passed_qty" => decimalQuantityPreview($last_passed) . " " . ($one["uomName"] ?? ""),
                    "rejected_qty" => decimalQuantityPreview($last_rejected) . " " . ($one["uomName"] ?? ""),
                    "remaining_qty" => decimalQuantityPreview($remaining_qty) . " " . ($one["uomName"] ?? ""),
                    "stocklog.bornDate" => formatDateORDateTime($one["bornDate"] ?? ""),
                    "grn.grnPoNumber" => $one["grnPoNumber"] ?? "",
                    "grn.vendorDocumentNo" => $one["vendorDocumentNo"] ?? "",
                    "grn.vendorName" => $one["vendorName"] ?? "",
                    "grn.vendorCode" => $one["vendorCode"] ?? "",
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
