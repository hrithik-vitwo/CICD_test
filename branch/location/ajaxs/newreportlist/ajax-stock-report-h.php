<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../pagination/common-pagination.php");

// require_once("../../../common/exportexcel.php");
require_once("../../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');

$BranchSoObj = new BranchSo();
session_start();

if ($_POST['act'] == 'cdata') {
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
            global $decimalValue;
            global $decimalQuantity;
            $conds = "";

            if ($slag === 'postingDate' || $slag === 'due_date' || $slag === 'created_at' || $slag === 'updated_at' || $slag === "LOG.postingDate") {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif (strcasecmp($data['value'], 'Goods') === 0) {
                $data['value'] = 'material';
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            } elseif ($slag === "total_due" || $slag === "0-30_days_due" || $slag === "31-60_days_due" || $slag === "61-90_days_due" || $slag === "91-180_days_due" || $slag === "more_than_180_days_due") {
                $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
            } elseif ($slag === "created_by" || $slag === "updated_by") {

                $resultList = getAdminUserIdByName($data['value']);
                $conds .= $slag . " IN  " . " (" . $resultList . ")";
            } else if ($slag === "LOG.itemPrice") {
                $cleanedValue = str_replace(',', '', $data['value']);
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $cleanedValue;
            } else if ($slag === "LOG.itemQty") {
                $cleanedValue = str_replace(',', '', $data['value']);
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $cleanedValue;
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));


        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sql_list = "SELECT loc.othersLocation_name AS location, LOG.refNumber AS document_no, items.itemCode, items.itemName, grp.goodGroupName AS itemGroup, str_loc.storage_location_name AS storage_location, LOG.logRef, CASE WHEN LOG.refActivityName IN('GRN', 'REV-GRN') THEN grn.vendorCode WHEN LOG.refActivityName IN('INVOICE', 'REV-INVOICE') THEN ( SELECT customer.customer_code FROM erp_branch_sales_order_invoices inv LEFT JOIN erp_customer customer ON inv.customer_id = customer.customer_id WHERE inv.invoice_no = CASE WHEN LOG.refActivityName = 'REV-INVOICE' THEN SUBSTRING(LOG.refNumber, 4) ELSE LOG.refNumber END LIMIT 1 ) WHEN LOG.refActivityName IN ('DELIVERY', 'REV-DELIVERY') THEN ( SELECT customer.customer_code FROM erp_branch_sales_order_delivery del LEFT JOIN erp_customer customer ON del.customer_id = customer.customer_id WHERE del.delivery_no = CASE WHEN LOG.refActivityName = 'REV-DELIVERY' THEN SUBSTRING(LOG.refNumber, 4) ELSE LOG.refNumber END LIMIT 1 ) WHEN LOG.refActivityName IN ('PGI') THEN ( SELECT customer.customer_code FROM erp_branch_sales_order_delivery_pgi pgi LEFT JOIN erp_customer customer ON pgi.customer_id = customer.customer_id WHERE pgi.pgi_no = LOG.refNumber LIMIT 1 ) ELSE 'INTERNAL' END AS party_code, CASE WHEN LOG.refActivityName IN('GRN', 'REV-GRN') THEN grn.vendorName WHEN LOG.refActivityName IN('INVOICE', 'REV-INVOICE') THEN ( SELECT customer.trade_name FROM erp_branch_sales_order_invoices inv LEFT JOIN erp_customer customer ON inv.customer_id = customer.customer_id WHERE inv.invoice_no = CASE WHEN LOG.refActivityName = 'REV-INVOICE' THEN SUBSTRING(LOG.refNumber, 4) ELSE LOG.refNumber END LIMIT 1 ) WHEN LOG.refActivityName IN ('DELIVERY', 'REV-DELIVERY') THEN ( SELECT customer.trade_name FROM erp_branch_sales_order_delivery del LEFT JOIN erp_customer customer ON del.customer_id = customer.customer_id WHERE del.delivery_no = CASE WHEN LOG.refActivityName = 'REV-DELIVERY' THEN SUBSTRING(LOG.refNumber, 4) ELSE LOG.refNumber END LIMIT 1 ) WHEN LOG.refActivityName IN ('PGI') THEN ( SELECT customer.trade_name FROM erp_branch_sales_order_delivery_pgi pgi LEFT JOIN erp_customer customer ON pgi.customer_id = customer.customer_id WHERE pgi.pgi_no = LOG.refNumber LIMIT 1 ) ELSE 'INTERNAL' END AS party_name, LOG.postingDate AS DATE, UOM.uomName AS uom, LOG.refActivityName AS movement_type, LOG.itemQty AS qty, LOG.itemPrice AS rate, LOG.itemPrice * LOG.itemQty AS value,LOG.createdAt as createdAt FROM erp_inventory_stocks_log AS LOG LEFT JOIN erp_inventory_items AS items ON LOG.itemId = items.itemId LEFT JOIN erp_inventory_stocks_summary AS summary ON items.itemId = summary.itemId LEFT JOIN erp_inventory_mstr_good_groups AS grp ON items.goodsGroup = grp.goodGroupId LEFT JOIN erp_inventory_mstr_uom AS UOM ON LOG.itemUom = UOM.uomId LEFT JOIN erp_storage_location AS str_loc ON LOG.storageLocationId = str_loc.storage_location_id LEFT JOIN erp_branch_otherslocation AS loc ON LOG.locationId = loc.othersLocation_id LEFT JOIN erp_grn AS grn ON LOG.logRef = grn.grnCode WHERE 1 $cond AND LOG.companyId = $company_id AND LOG.branchId = $branch_id AND LOG.locationId = $location_id AND items.company_id = $company_id AND items.goodsType IN (1,2,3,4) ORDER BY LOG.postingdate DESC";

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
                $currency_sql = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id` = $company_currency");
                // $currency = $currency_sql['data']['currency_name'];


                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "loc.othersLocation_name" => $data['location'],
                    "LOG.refNumber" => $data['document_no'],
                    "grp.goodGroupName" => $data['itemGroup'],
                    "items.itemCode" => $data['itemCode'],
                    "items.itemName" => $data['itemName'],
                    "str_loc.storage_location_name" => $data['storage_location'],
                    "party_code" => $data['party_code'],
                    "party_name" => $data['party_name'],
                    "loLOG.logRefgRef" => $data['logRef'],
                    "UOM.uomName" => $data['uom'],
                    "movement_type" => $data['movement_type'],
                    "LOG.refActivityName" => $data['movement_type'],
                    "LOG.itemQty" => decimalQuantityPreview($data['qty']),
                    "LOG.itemPrice" => decimalValuePreview($data['rate']),
                    "value" => decimalValuePreview($data['value']),
                    "LOG.postingDate" => formatDateWeb($data['DATE']),
                    "fdate" => formatDateWeb($data['DATE']),
                    "LOG.createdAt" => formatDateWeb($data['createdAt'])
                    // "currency" => $currency
                ];
                $sl++;
            }
            $output .= "</table>";
            $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";
            $queryset = queryGet($sqlRowCount);
            $totalRows = $queryset['data']['row_count'];
            $total_page = ceil($totalRows / $limit_per_Page);

            $output .= pagiNation($page_no, $total_page);

            $limitText .= '<a class="active" id="limitText">Showing' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';

            // $csvContent = exportToExcelAll($sql_list, json_encode($columnMapping),1);
            // $csvContentBypagination = exportToExcelByPagin($sql_Mainqry, json_encode($columnMapping));

            $res = [
                "sql_data" => $sql_data,
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "limit_per_Page" => $limit_per_Page,
                // "csvContent" => $csvContent,
                // "csvContentBypagination" => $csvContentBypagination,
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
    $formObj = $_POST['formDatas'];
    $cond = "";
    $implodeFrom = implode('', array_map(function ($slag, $data) {
        global $decimalValue;
        global $decimalQuantity;
        $conds = "";

        if ($slag === 'postingDate' || $slag === 'due_date' || $slag === 'created_at' || $slag === 'updated_at' || $slag === "LOG.postingDate") {
            if ($data['operatorName'] === 'BETWEEN') {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
            } else {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
            }
        } elseif (strcasecmp($data['value'], 'Goods') === 0) {
            $data['value'] = 'material';
            $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
        } elseif ($slag === "total_due" || $slag === "0-30_days_due" || $slag === "31-60_days_due" || $slag === "61-90_days_due" || $slag === "91-180_days_due" || $slag === "more_than_180_days_due") {
            $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
        } elseif ($slag === "created_by" || $slag === "updated_by") {

            $resultList = getAdminUserIdByName($data['value']);
            $conds .= $slag . " IN  " . " (" . $resultList . ")";
        } else if ($slag === "LOG.itemPrice") {
            $cleanedValue = str_replace(',', '', $data['value']);
            $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $cleanedValue;
        } else if ($slag === "LOG.itemQty") {
            $cleanedValue = str_replace(',', '', $data['value']);
            $conds .= "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $cleanedValue;
        } else {
            $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
        }

        return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));


    if (!empty($implodeFrom)) {
        $cond .= $implodeFrom;
    }

    $sql_list = "SELECT loc.othersLocation_name AS location, LOG.refNumber AS document_no, items.itemCode, items.itemName, grp.goodGroupName AS itemGroup, str_loc.storage_location_name AS storage_location, LOG.logRef, CASE WHEN LOG.refActivityName IN('GRN', 'REV-GRN') THEN grn.vendorCode WHEN LOG.refActivityName IN('INVOICE', 'REV-INVOICE') THEN ( SELECT customer.customer_code FROM erp_branch_sales_order_invoices inv LEFT JOIN erp_customer customer ON inv.customer_id = customer.customer_id WHERE inv.invoice_no = CASE WHEN LOG.refActivityName = 'REV-INVOICE' THEN SUBSTRING(LOG.refNumber, 4) ELSE LOG.refNumber END LIMIT 1 ) WHEN LOG.refActivityName IN ('DELIVERY', 'REV-DELIVERY') THEN ( SELECT customer.customer_code FROM erp_branch_sales_order_delivery del LEFT JOIN erp_customer customer ON del.customer_id = customer.customer_id WHERE del.delivery_no = CASE WHEN LOG.refActivityName = 'REV-DELIVERY' THEN SUBSTRING(LOG.refNumber, 4) ELSE LOG.refNumber END LIMIT 1 ) WHEN LOG.refActivityName IN ('PGI') THEN ( SELECT customer.customer_code FROM erp_branch_sales_order_delivery_pgi pgi LEFT JOIN erp_customer customer ON pgi.customer_id = customer.customer_id WHERE pgi.pgi_no = LOG.refNumber LIMIT 1 ) ELSE 'INTERNAL' END AS party_code, CASE WHEN LOG.refActivityName IN('GRN', 'REV-GRN') THEN grn.vendorName WHEN LOG.refActivityName IN('INVOICE', 'REV-INVOICE') THEN ( SELECT customer.trade_name FROM erp_branch_sales_order_invoices inv LEFT JOIN erp_customer customer ON inv.customer_id = customer.customer_id WHERE inv.invoice_no = CASE WHEN LOG.refActivityName = 'REV-INVOICE' THEN SUBSTRING(LOG.refNumber, 4) ELSE LOG.refNumber END LIMIT 1 ) WHEN LOG.refActivityName IN ('DELIVERY', 'REV-DELIVERY') THEN ( SELECT customer.trade_name FROM erp_branch_sales_order_delivery del LEFT JOIN erp_customer customer ON del.customer_id = customer.customer_id WHERE del.delivery_no = CASE WHEN LOG.refActivityName = 'REV-DELIVERY' THEN SUBSTRING(LOG.refNumber, 4) ELSE LOG.refNumber END LIMIT 1 ) WHEN LOG.refActivityName IN ('PGI') THEN ( SELECT customer.trade_name FROM erp_branch_sales_order_delivery_pgi pgi LEFT JOIN erp_customer customer ON pgi.customer_id = customer.customer_id WHERE pgi.pgi_no = LOG.refNumber LIMIT 1 ) ELSE 'INTERNAL' END AS party_name, LOG.postingDate AS DATE, UOM.uomName AS uom, LOG.refActivityName AS movement_type, LOG.itemQty AS qty, LOG.itemPrice AS rate, LOG.itemPrice * LOG.itemQty AS value,LOG.createdAt as createdAt FROM erp_inventory_stocks_log AS LOG LEFT JOIN erp_inventory_items AS items ON LOG.itemId = items.itemId LEFT JOIN erp_inventory_stocks_summary AS summary ON items.itemId = summary.itemId LEFT JOIN erp_inventory_mstr_good_groups AS grp ON items.goodsGroup = grp.goodGroupId LEFT JOIN erp_inventory_mstr_uom AS UOM ON LOG.itemUom = UOM.uomId LEFT JOIN erp_storage_location AS str_loc ON LOG.storageLocationId = str_loc.storage_location_id LEFT JOIN erp_branch_otherslocation AS loc ON LOG.locationId = loc.othersLocation_id LEFT JOIN erp_grn AS grn ON LOG.logRef = grn.grnCode WHERE 1 $cond AND LOG.companyId = $company_id AND LOG.branchId = $branch_id AND LOG.locationId = $location_id AND items.company_id = $company_id ORDER BY LOG.postingdate DESC";
    $dynamic_data_all = [];
    $sqlMainQryObjall = queryGet($sql_list, true);
    $sql_data_all = $sqlMainQryObjall['data'];
    $num_list =  $sqlMainQryObjall['numRows'];
    if ($num_list > 0) {
        foreach ($sql_data_all as $data) {
            $dynamic_data_all[] = [
                "sl_no" => $sl,
                "loc.othersLocation_name" => $data['location'],
                "LOG.refNumber" => $data['document_no'],
                "grp.goodGroupName" => $data['itemGroup'],
                "items.itemCode" => $data['itemCode'],
                "items.itemName" => $data['itemName'],
                "str_loc.storage_location_name" => $data['storage_location'],
                "party_code" => $data['party_code'],
                "party_name" => $data['party_name'],
                "loLOG.logRefgRef" => $data['logRef'],
                "UOM.uomName" => $data['uom'],
                "movement_type" => $data['movement_type'],
                "LOG.refActivityName" => $data['movement_type'],
                "LOG.itemQty" => decimalQuantityPreview($data['qty']),
                "LOG.itemPrice" => decimalValuePreview($data['rate']),
                "value" => decimalValuePreview($data['value']),
                "LOG.postingDate" => formatDateWeb($data['DATE']),
                "fdate" => formatDateWeb($data['DATE']),
                "LOG.createdAt" => formatDateWeb($data['createdAt'])
            ];
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
