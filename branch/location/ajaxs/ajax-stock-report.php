<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("pagination/common-pagination.php");

require_once("../../common/exportexcel.php");
$headerData = array('Content-Type: application/json');

// $columnMapping = [
//     [
//         'name' => 'SL_NO',
//         'slag' => 'sl_no',
//         'icon' => '',
//         'dataType' => 'number'
//     ],
//     [
//         'name' => 'Location',
//         'slag' => 'loc.othersLocation_name',
//         'icon' => '<ion-icon name="location-outline"></ion-icon>',
//         'dataType' => 'string'
//     ],
//     [
//         'name' => 'Document No',
//         'slag' => 'LOG.refNumber',
//         'icon' => '<ion-icon name="document-outline"></ion-icon>',
//         'dataType' => 'string'
//     ],
//     [
//         'name' => 'Item Code',
//         'slag' => 'items.itemCode',
//         'icon' => '<ion-icon name="code-outline"></ion-icon>',
//         'dataType' => 'string'
//     ],
//     [
//         'name' => 'Item Group',
//         'slag' => 'grp.goodGroupName',
//         'icon' => '<ion-icon name="albums-outline"></ion-icon>',
//         'dataType' => 'string'
//     ],

//     [
//         'name' => 'Item Name',
//         'slag' => 'items.itemName',
//         'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
//         'dataType' => 'string'
//     ],
//     [
//         'name' => 'Storage Location',
//         'slag' => 'str_loc.storage_location_name',
//         'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
//         'dataType' => 'string'
//     ],
//     [
//         'name' => 'Party Code',
//         'slag' => 'grn.vendorCode__customer.customer_code',
//         'icon' => '<ion-icon name="code-outline"></ion-icon>',
//         'dataType' => 'string'
//     ],
//     [
//         'name' => 'Party Name',
//         'slag' => 'grn.vendorName__customer.trade_name',
//         'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
//         'dataType' => 'string'
//     ],
//     [
//         'name' => 'Batch No',
//         'slag' => 'LOG.logRef',
//         'icon' => '<ion-icon name="document-outline"></ion-icon>',
//         'dataType' => 'string'
//     ],
//     [
//         'name' => 'Date',
//         'slag' => 'LOG.postingDate',
//         'icon' => '<ion-icon name="calendar-outline"></ion-icon>',
//         'dataType' => 'date'
//     ],
//     [
//         'name' => 'UOM',
//         'slag' => 'LOG.itemQty',
//         'icon' => '<ion-icon name="keypad-outline"></ion-icon>',
//         'dataType' => 'number'
//     ],
//     [
//         'name' => 'Mvt Type ',
//         'slag' => 'LOG.refActivityName',
//         'icon' => '<ion-icon name="walk-outline"></ion-icon>',
//         'dataType' => 'string'
//     ],

//     [
//         'name' => 'Qty',
//         'slag' => 'UOM.uomName',
//         'icon' => '<ion-icon name="document-outline"></ion-icon>',
//         'dataType' => 'string'
//     ],
//     [
//         'name' => 'Rate',
//         'slag' => 'LOG.itemPrice',
//         'icon' => '<ion-icon name="wallet-outline"></ion-icon>',
//         'dataType' => 'number'
//     ],
//     [
//         'name' => 'Value',
//         'slag' => 'curr.currency_name',
//         'icon' => '<ion-icon name="wallet-outline"></ion-icon>',
//         'dataType' => 'string'
//     ],

// ];


$_SESSION['columnMapping'] = $_POST['columnMapping'];
if (isset($_SESSION['columnMapping'])) {
    $columnMapping = $_SESSION['columnMapping'];
}
// print_r($_POST);

$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'detailed_view') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? $_POST['limit'] : 25;

        $page_no = isset($_POST['pageNo']) ? (int)$_POST['pageNo'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $formObj = $_POST['formDatas'];
        // $con = "AND DATE(LOG.postingDate) BETWEEN '" . $previousDate . "' AND '" . $currentDate . "'";
        $con = "";

        $implodeFrom = implode('', array_map(function ($slag, $data) use (&$con) {
            $slag1 = explode("__", $slag)[0] ?? "";
            $slag2 = explode("__", $slag)[1] ?? "";
            $conds = "";
            if (!empty($slag1) && !empty($slag2)) {
                $conds .= "(" . $slag1 . " " . $data['operatorName'] . " '%" . $data['value'] . "%' OR " . $slag2 . " " . $data['operatorName'] . " '%" . $data['value'] . "%')";
            } else if ($slag === 'date') {
                $con = "";
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(LOG.postingDate) " . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(LOG.postingDate) " . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $con .= " " . $implodeFrom;
        }



        // $sql_list = "SELECT loc.othersLocation_name AS location, LOG.refNumber AS document_no, items.itemCode, items.itemName, grp.goodGroupName AS itemGroup, str_loc.storage_location_name AS storage_location, LOG.logRef, CASE WHEN LOG.refActivityName IN('GRN', 'REV-GRN') THEN grn.vendorCode WHEN LOG.refActivityName IN( 'INVOICE', 'REV-INVOICE', 'DELIVERY', 'REV-DELIVERY', 'PGI' ) THEN customer.customer_code ELSE 'INTERNAL' END AS party_code, CASE WHEN LOG.refActivityName IN('GRN', 'REV-GRN') THEN grn.vendorName WHEN LOG.refActivityName IN( 'INVOICE', 'REV-INVOICE', 'DELIVERY', 'REV-DELIVERY', 'PGI' ) THEN customer.trade_name ELSE 'INTERNAL' END AS party_name, DATE_FORMAT(DATE(LOG.postingDate), '%d-%b') AS date, UOM.uomName AS uom, LOG.refActivityName AS movement_type, LOG.itemQty AS qty, LOG.itemPrice AS rate, LOG.itemPrice * LOG.itemQty AS value FROM erp_inventory_stocks_log AS LOG LEFT JOIN erp_inventory_items AS items ON LOG.itemId = items.itemId LEFT JOIN erp_inventory_stocks_summary AS summary ON items.itemId = summary.itemId LEFT JOIN erp_inventory_mstr_uom AS UOM ON LOG.itemUom = UOM.uomId LEFT JOIN erp_branch_otherslocation AS loc ON LOG.locationId = loc.othersLocation_id LEFT JOIN erp_inventory_mstr_good_groups AS grp ON items.goodsGroup = grp.goodGroupId LEFT JOIN erp_storage_location AS str_loc ON LOG.storageLocationId = str_loc.storage_location_id LEFT JOIN erp_grn AS grn ON LOG.logRef = grn.grnCode LEFT JOIN erp_branch_sales_order_invoice_items AS inv_itm ON items.itemId = inv_itm.inventory_item_id LEFT JOIN erp_branch_sales_order_invoices AS inv ON inv_itm.so_invoice_id = inv.so_invoice_id LEFT JOIN erp_customer AS customer ON inv.customer_id = customer.customer_id WHERE 1 ".$con."AND LOG.companyId = $company_id AND LOG.branchId = $branch_id AND LOG.locationId = $location_id AND items.location_id=$location_id GROUP BY items.itemId, LOG.logRef, LOG.refNumber, str_loc.storage_location_name, grn.vendorCode, grn.vendorName, customer.customer_code, customer.trade_name, UOM.uomName, DATE, movement_type, LOG.itemQty, LOG.itemPrice, LOG.postingDate HAVING DATE IS NOT NULL";

        $sql_list = "SELECT loc.othersLocation_name AS location, LOG.refNumber AS document_no, items.itemCode, items.itemName, grp.goodGroupName AS itemGroup, str_loc.storage_location_name AS storage_location, LOG.logRef, CASE WHEN LOG.refActivityName IN('GRN', 'REV-GRN') THEN grn.vendorCode WHEN LOG.refActivityName IN('INVOICE', 'REV-INVOICE') THEN ( SELECT customer.customer_code FROM erp_branch_sales_order_invoices inv LEFT JOIN erp_customer customer ON inv.customer_id = customer.customer_id WHERE inv.invoice_no = CASE WHEN LOG.refActivityName = 'REV-INVOICE' THEN SUBSTRING(LOG.refNumber, 4) ELSE LOG.refNumber END LIMIT 1 ) WHEN LOG.refActivityName IN ('DELIVERY', 'REV-DELIVERY') THEN ( SELECT customer.customer_code FROM erp_branch_sales_order_delivery del LEFT JOIN erp_customer customer ON del.customer_id = customer.customer_id WHERE del.delivery_no = CASE WHEN LOG.refActivityName = 'REV-DELIVERY' THEN SUBSTRING(LOG.refNumber, 4) ELSE LOG.refNumber END LIMIT 1 ) WHEN LOG.refActivityName IN ('PGI') THEN ( SELECT customer.customer_code FROM erp_branch_sales_order_delivery_pgi pgi LEFT JOIN erp_customer customer ON pgi.customer_id = customer.customer_id WHERE pgi.pgi_no = LOG.refNumber LIMIT 1 ) ELSE 'INTERNAL' END AS party_code, CASE WHEN LOG.refActivityName IN('GRN', 'REV-GRN') THEN grn.vendorName WHEN LOG.refActivityName IN('INVOICE', 'REV-INVOICE') THEN ( SELECT customer.trade_name FROM erp_branch_sales_order_invoices inv LEFT JOIN erp_customer customer ON inv.customer_id = customer.customer_id WHERE inv.invoice_no = CASE WHEN LOG.refActivityName = 'REV-INVOICE' THEN SUBSTRING(LOG.refNumber, 4) ELSE LOG.refNumber END LIMIT 1 ) WHEN LOG.refActivityName IN ('DELIVERY', 'REV-DELIVERY') THEN ( SELECT customer.trade_name FROM erp_branch_sales_order_delivery del LEFT JOIN erp_customer customer ON del.customer_id = customer.customer_id WHERE del.delivery_no = CASE WHEN LOG.refActivityName = 'REV-DELIVERY' THEN SUBSTRING(LOG.refNumber, 4) ELSE LOG.refNumber END LIMIT 1 ) WHEN LOG.refActivityName IN ('PGI') THEN ( SELECT customer.trade_name FROM erp_branch_sales_order_delivery_pgi pgi LEFT JOIN erp_customer customer ON pgi.customer_id = customer.customer_id WHERE pgi.pgi_no = LOG.refNumber LIMIT 1 ) ELSE 'INTERNAL' END AS party_name, DATE_FORMAT(DATE(LOG.postingDate), '%d-%b') AS DATE, UOM.uomName AS uom, LOG.refActivityName AS movement_type, LOG.itemQty AS qty, LOG.itemPrice AS rate, LOG.itemPrice * LOG.itemQty AS value FROM erp_inventory_stocks_log AS LOG LEFT JOIN erp_inventory_items AS items ON LOG.itemId = items.itemId LEFT JOIN erp_inventory_mstr_good_groups AS grp ON items.goodsGroup = grp.goodGroupId LEFT JOIN erp_inventory_mstr_uom AS UOM ON LOG.itemUom = UOM.uomId LEFT JOIN erp_storage_location AS str_loc ON LOG.storageLocationId = str_loc.storage_location_id LEFT JOIN erp_branch_otherslocation AS loc ON LOG.locationId = loc.othersLocation_id LEFT JOIN erp_grn AS grn ON LOG.logRef = grn.grnCode WHERE 1 $con AND LOG.companyId = $company_id AND LOG.branchId = $branch_id AND LOG.locationId = $location_id";

        $dynamic_data = [];
        $sql_Mainqry = $sql_list . "  LIMIT " . $offset . "," . $limit_per_Page . ";";
        // console($sql_Mainqry);
        $sqlMainQryObj = queryGet($sql_Mainqry, true);
        // console($sqlMainQryObj);
        $num_list = $sqlMainQryObj['numRows'];
        $sql_data = $sqlMainQryObj['data'];
        $output = "";
        $limitText = "";
        $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $currency_sql = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id` = $company_currency");
        // $currency = $currency_sql['data']['currency_name'];

        if ($num_list > 0) {
            foreach ($sql_data as $data) {

                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "loc" => $data['location'],
                    "doc_no" => $data['document_no'],
                    "itemGrp" => $data['itemGroup'],
                    "itemcode" => $data['itemCode'],
                    "itemName" => $data['itemName'],
                    "storage_loc" => $data['storage_location'],
                    "party_code" => $data['party_code'],
                    "party_name" => $data['party_name'],
                    "logRef" => $data['logRef'],
                    "uom" => $data['uom'],
                    "movement_type" => $data['movement_type'],
                    "qty" => $data['qty'],
                    "value" => $data['value'],
                    "date" => $data['DATE'],
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


            $csvContent = exportToExcelAll($sql_list, json_encode($columnMapping), 1);
            $csvContentBypagination = exportToExcelByPagin($sql_Mainqry, json_encode($columnMapping));


            $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';

            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "pagination" => $output,
                "pagemno" => $page_no,
                "offset" => $offset,
                "formdata" => $formObj,
                "formVal" => $value,
                "location" => $location,
                "implodeData" => $implodeFrom,
                "limitTxt" => $limitText,
                "csvContent" => $csvContent,
                "csvContentBypagination" => $csvContentBypagination,
                "sql" => $sql_list,
                "array" => $columnMapping
            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "formdata" => $formObj,
                "sql" => $sql_list,
                "implodeData" => $implodeFrom,
                "fdate" => $f_Date

            ];
        }
        // console($res);
        echo json_encode($res);
    }
}
