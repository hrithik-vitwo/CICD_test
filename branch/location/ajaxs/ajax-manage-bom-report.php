<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("pagination/common-pagination.php");

require_once("../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');
$goods = '';
$approve = '';
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
            } else if ($slag === "totalAmount") {
                $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
            } elseif ($slag === "bi1.consumption") {
                $conds .= $slag . " " . $data['operatorName'] . " " . $data['value'];
            } elseif ($slag === "b.bomStatus") {
                $conds .= $slag . " " . $data['operatorName'] . " '" . $data['value'] . "'";
            } elseif ($slag == "i2.itemCode") {
                $conds .= "i2.itemCode LIKE '%" . $data['value'] . "%'";
            } elseif ($slag == "i1.goodsType") {
                if ($data['value'] == 'finished good') {
                    $conds .= "i1.goodsType = 3";
                } else {
                    $conds .= "i1.goodsType != 3";
                }
            } elseif ($slag == "i2.itemName") {
                $conds .= "i2.itemName LIKE '%" . $data['value'] . "%'";
            } elseif ($slag == "i2.goodsType") {
                if ($data['value'] == 'semi-finished good') {
                    $conds .= "i2.goodsType = 2";
                } elseif ($data['value'] == 'raw material') {
                    $conds .= "i2.goodsType = 1";
                } else {
                    $conds .= "i2.goodsType != 1 AND i2.goodsType != 2 ";
                }
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));


        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        // $sql_list = "SELECT b.bomId, i1.itemCode AS final_good_code, i1.itemName AS final_good_name, CASE WHEN i1.goodsType = 3 THEN 'finished good' ELSE 'semi-finished good' END AS final_good_type, i2.itemCode AS required_item_code, i2.itemName AS required_item_name, CASE WHEN i2.goodsType = 1 THEN 'raw material' when i2.goodsType = 2 THEN 'semi-finished good' ELSE 'finished good' END AS required_item_type, bi1.consumption AS required_item_qty, b.bomStatus FROM erp_bom b JOIN erp_bom_item_material bi1 ON bi1.bom_id = b.bomId JOIN erp_inventory_items i1 ON b.itemId = i1.itemId JOIN erp_inventory_items i2 ON bi1.item_id = i2.itemId WHERE 1 " . $cond . " AND i1.goodsType IN (3, 2) AND i2.goodsType IN (1, 2, 3) AND b.companyId = $company_id AND i1.company_id = $branch_id AND i2.company_id = $company_id ORDER BY b.bomId, i1.itemId, i2.itemId";
        $sql_list = "SELECT b.bomId, i1.itemCode AS final_good_code, i1.itemName AS final_good_name, uom1.uomName as final_good_uom, CASE WHEN i1.goodsType = 3 THEN 'finished good' ELSE 'semi-finished good' END AS final_good_type, i2.itemCode AS required_item_code, i2.itemName AS required_item_name, uom2.uomName as required_item_uom, CASE WHEN i2.goodsType = 1 THEN 'raw material' WHEN i2.goodsType = 2 THEN 'semi-finished good' ELSE 'finished good' END AS required_item_type, bi1.consumption AS required_item_qty, b.bomStatus FROM erp_bom b JOIN erp_bom_item_material bi1 ON bi1.bom_id = b.bomId JOIN erp_inventory_items i1 ON b.itemId = i1.itemId JOIN erp_inventory_items i2 ON bi1.item_id = i2.itemId join erp_inventory_mstr_uom as uom1 on i1.baseUnitMeasure = uom1.uomId join erp_inventory_mstr_uom as uom2 on i2.baseUnitMeasure = uom2.uomId WHERE 1 " . $cond . " AND  i1.goodsType IN(3, 2) AND i2.goodsType IN(1, 2, 3) AND b.companyId = $company_id AND i1.company_id = $company_id AND i2.company_id = $company_id ORDER BY b.bomId, i1.itemId, i2.itemId";

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
                $dynamic_data[] = [
                "sl_no" => $sl,
                "bomId" => $data['bomId'],
                "i1.itemCode" => $data['final_good_code'],
                "i1.itemName" => $data['final_good_name'],
                "uom1.uomName" => $data['final_good_uom'],
                "i1.goodsType" => $data['final_good_type'],
                "i2.itemCode" => $data['required_item_code'],
                "i2.itemName" => $data['required_item_name'],
                "uom2.uomName" => $data['required_item_uom'],
                "i2.goodsType" => $data['required_item_type'],
                "bi1.consumption" => decimalQuantityPreview($data['required_item_qty']),
                "b.bomStatus" => $data['bomStatus'],
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
                "limit_per_Page" => $limit_per_Page,
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

if ($_POST['act'] == 'alldata') {
    $fromDate = $_POST['fromDate'];
    $toDate = $_POST['toDate'];
    $formObj = $_POST['formDatas'];
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
        } else if ($slag === "totalAmount") {
            $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
        } elseif ($slag === "bi1.consumption") {
            $conds .= $slag . " " . $data['operatorName'] . " " . $data['value'];
        } elseif ($slag === "b.bomStatus") {
            $conds .= $slag . " " . $data['operatorName'] . " '" . $data['value'] . "'";
        } elseif ($slag == "i2.itemCode") {
            $conds .= "i2.itemCode LIKE '%" . $data['value'] . "%'";
        } elseif ($slag == "i1.goodsType") {
            if ($data['value'] == 'finished good') {
                $conds .= "i1.goodsType = 3";
            } else {
                $conds .= "i1.goodsType != 3";
            }
        } elseif ($slag == "i2.itemName") {
            $conds .= "i2.itemName LIKE '%" . $data['value'] . "%'";
        } elseif ($slag == "i2.goodsType") {
            if ($data['value'] == 'semi-finished good') {
                $conds .= "i2.goodsType = 2";
            } elseif ($data['value'] == 'raw material') {
                $conds .= "i2.goodsType = 1";
            } else {
                $conds .= "i2.goodsType != 1 AND i2.goodsType != 2 ";
            }
        } else {
            $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
        }

        return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));


    if (!empty($implodeFrom)) {
        $cond .= $implodeFrom;
    }
    // $sql_list = "SELECT b.bomId, i1.itemCode AS final_good_code, i1.itemName AS final_good_name, CASE WHEN i1.goodsType = 3 THEN 'finished good' ELSE 'semi-finished good' END AS final_good_type, i2.itemCode AS required_item_code, i2.itemName AS required_item_name, CASE WHEN i2.goodsType = 1 THEN 'raw material' when i2.goodsType = 2 THEN 'semi-finished good' ELSE 'finished good' END AS required_item_type, bi1.consumption AS required_item_qty, b.bomStatus FROM erp_bom b JOIN erp_bom_item_material bi1 ON bi1.bom_id = b.bomId JOIN erp_inventory_items i1 ON b.itemId = i1.itemId JOIN erp_inventory_items i2 ON bi1.item_id = i2.itemId WHERE 1 " . $cond . " AND i1.goodsType IN (3, 2) AND i2.goodsType IN (1, 2, 3) AND b.companyId = $company_id AND i1.company_id = $branch_id AND i2.company_id = $company_id ORDER BY b.bomId, i1.itemId, i2.itemId";
    $sql_list = "SELECT b.bomId, i1.itemCode AS final_good_code, i1.itemName AS final_good_name, uom1.uomName as final_good_uom, CASE WHEN i1.goodsType = 3 THEN 'finished good' ELSE 'semi-finished good' END AS final_good_type, i2.itemCode AS required_item_code, i2.itemName AS required_item_name, uom2.uomName as required_item_uom, CASE WHEN i2.goodsType = 1 THEN 'raw material' WHEN i2.goodsType = 2 THEN 'semi-finished good' ELSE 'finished good' END AS required_item_type, bi1.consumption AS required_item_qty, b.bomStatus FROM erp_bom b JOIN erp_bom_item_material bi1 ON bi1.bom_id = b.bomId JOIN erp_inventory_items i1 ON b.itemId = i1.itemId JOIN erp_inventory_items i2 ON bi1.item_id = i2.itemId join erp_inventory_mstr_uom as uom1 on i1.baseUnitMeasure = uom1.uomId join erp_inventory_mstr_uom as uom2 on i2.baseUnitMeasure = uom2.uomId WHERE 1 " . $cond . " AND i1.goodsType IN(3, 2) AND i2.goodsType IN(1, 2, 3) AND b.companyId = $company_id AND i1.company_id = $company_id AND i2.company_id = $company_id ORDER BY b.bomId, i1.itemId, i2.itemId";
    $dynamic_data_all = [];
    $sqlMainQryObjall = queryGet($sql_list, true);
    $sql_data_all = $sqlMainQryObjall['data'];
    $num_list =  $sqlMainQryObjall['numRows'];
    if ($num_list > 0) {
        foreach ($sql_data_all as $data) {
            $dynamic_data_all[] = [
               "sl_no" => $sl,
                "bomId" => $data['bomId'],
                "i1.itemCode" => $data['final_good_code'],
                "i1.itemName" => $data['final_good_name'],
                "uom1.uomName" => $data['final_good_uom'],
                "i1.goodsType" => $data['final_good_type'],
                "i2.itemCode" => $data['required_item_code'],
                "i2.itemName" => $data['required_item_name'],
                "uom2.uomName" => $data['required_item_uom'],
                "i2.goodsType" => $data['required_item_type'],
                "bi1.consumption" => decimalQuantityPreview($data['required_item_qty']),
                "b.bomStatus" => $data['bomStatus'],
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
