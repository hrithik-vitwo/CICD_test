<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../pagination/common-pagination.php");
require_once("../../../common/exportexcel-new.php");
// require_once("../../../common/exportexcel.php");
$headerData = array('Content-Type: application/json');

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

            if ($slag === 'created_at' || $slag === 'updated_at' || $slag === 'posting_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif (
                $slag === "totalPrice" || $slag === "total_qty" || $slag === 'mrp' || $slag === 'rate' || $slag === 'gross_amount' ||
                $slag === 'trade_disc' || $slag === 'trade_disc_amt' || $slag === 'base_amount' ||
                $slag === 'cash_disc' || $slag === 'cash_disc_amt' || $slag === 'taxable_value' ||
                $slag === 'cgst' || $slag === 'sgst' || $slag === 'igst' ||
                $slag === 'net_sales_value' || $slag === 'round_off' || $slag === 'net_receivable'
            ) {
                $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
            } else if ($slag === 'so.created_by' || $slag === 'created_by'||$slag==='updated_by') {
                $resultList = getAdminUserIdByName($data['value']);
                if($data['operatorName']=='LIKE'){
                    $conds .= $slag . " IN  " . " (" . $resultList . ")";
                }else{
                    $conds .= $slag . " NOT IN  " . " (" . $resultList . ")";
                }
                
               
            } else if ($slag === 'updated_by') {

                $resultList = getAdminUserIdByName($data['value']);
                $conds .= $slag . " IN  " . " (" . $resultList . ")";
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));


        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        // $sts = " AND `status` !='deleted'";
        $sql_list = "SELECT
                    document_no,
                    reference_no,
                    posting_date,
                    TYPE,
                    sales_person_name,
                    customer_code,
                    customer_name,
                    gstin,
                    item_code,
                    hsnCode,
                    item_name,
                    item_group,
                    total_qty,
                    uom,
                    mrp,
                    rate,
                    gross_amount,
                    trade_disc,
                    trade_disc_amt,
                    base_amount,
                    cash_disc,
                    cash_disc_amt,
                    taxable_value,
                    cgst,
                    sgst,
                    igst,
                    net_sales_value,
                    round_off,
                    net_receivable,
                    mobile,
                    customer_address_city,
                    customer_address_state,
                    created_by,
                    created_at,
                    updated_by,
                    updated_at,
                    tax
                FROM
                    (
                    SELECT
                		invoices.taxComponents as tax,
                        invoices.invoice_no AS document_no,
                        COALESCE(pgi.pgi_no, so.so_number) AS reference_no,
                        invoices.invoice_date AS posting_date,
                        'INV' AS TYPE,
                        kam.kamName AS sales_person_name,
                        customer.customer_code AS customer_code,
                        customer.trade_name AS customer_name,
                        invoices.customer_gstin AS gstin,
                        items.itemCode AS item_code,
                        items.hsnCode AS hsnCode,
                        items.itemName AS item_name,
                        groups.goodGroupName AS item_group,
                        COALESCE(items.qty, 0) AS total_qty,
                        UOM.uomName AS uom,
                        COALESCE(items.unitPrice, 0) AS mrp,
                        COALESCE(items.itemTargetPrice, 0) AS rate,
                        COALESCE(
                            items.qty * items.itemTargetPrice,
                            0
                        ) AS gross_amount,
                        COALESCE(items.totalDiscount, 0) AS trade_disc,
                        COALESCE(items.totalDiscountAmt, 0) AS trade_disc_amt,
                        COALESCE(
                            (
                                items.qty * items.itemTargetPrice
                            ) - items.totalDiscountAmt,
                            0
                        ) AS base_amount,
                        COALESCE(items.cashDiscount, 0) AS cash_disc,
                        COALESCE(items.cashDiscountAmount, 0) AS cash_disc_amt,
                        COALESCE(
                            (
                                items.qty * items.itemTargetPrice
                            ) - items.totalDiscountAmt - items.cashDiscountAmount,
                            0
                        ) AS taxable_value,
                        CASE WHEN invoices.igst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax / 2) ELSE 0
                END AS cgst,
                CASE WHEN invoices.igst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax / 2) ELSE 0
                END AS sgst,
                CASE WHEN invoices.cgst = 0 AND invoices.sgst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax) ELSE 0
                END AS igst,
                COALESCE(
                    (
                        items.qty * items.itemTargetPrice
                    ) - items.totalDiscountAmt - items.cashDiscountAmount,
                    0
                ) + CASE WHEN invoices.igst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax / 2) ELSE 0
                END + CASE WHEN invoices.igst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax / 2) ELSE 0
                END + CASE WHEN invoices.cgst = 0 AND invoices.sgst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax) ELSE 0
                END AS net_sales_value,
                (
                    COALESCE(debit.debit_amount, 0) - COALESCE(credit.credit_amount, 0)
                ) AS round_off,
                (
                    COALESCE(
                        (
                            items.qty * items.itemTargetPrice
                        ) - items.totalDiscountAmt - items.cashDiscountAmount,
                        0
                    ) + CASE WHEN invoices.igst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax / 2) ELSE 0
                END + CASE WHEN invoices.igst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax / 2) ELSE 0
                END + CASE WHEN invoices.cgst = 0 AND invoices.sgst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax) ELSE 0
                END
                ) -(
                    COALESCE(debit.debit_amount, 0) - COALESCE(credit.credit_amount, 0)
                ) AS net_receivable,
                customer.customer_authorised_person_phone AS mobile,
                (
                    SELECT
                        address.customer_address_city
                    FROM
                        erp_customer_address AS address
                    WHERE
                        address.customer_id = customer.customer_id
                    LIMIT 1
                ) AS customer_address_city,
                (
                    SELECT
                        address.customer_address_state
                    FROM
                        erp_customer_address AS address
                    WHERE
                        address.customer_id = customer.customer_id
                    LIMIT 1
                ) AS customer_address_state,
                invoices.created_by AS created_by,
                invoices.created_at AS created_at,
                invoices.updated_by updated_by,
                invoices.updated_at updated_at
                FROM
                    erp_customer AS customer
                LEFT JOIN erp_branch_sales_order_invoices AS invoices
                ON
                    invoices.customer_id = customer.customer_id
                LEFT JOIN erp_kam AS kam
                ON
                    invoices.kamId = kam.kamId
                LEFT JOIN erp_branch_sales_order_invoice_items AS items
                ON
                    invoices.so_invoice_id = items.so_invoice_id
                LEFT JOIN erp_inventory_items AS inventory
                ON
                    items.inventory_item_id = inventory.itemId
                LEFT JOIN erp_inventory_mstr_good_groups AS groups
                ON
                    inventory.goodsGroup = groups.goodGroupId
                LEFT JOIN erp_inventory_mstr_uom AS UOM
                ON
                    UOM.uomId = items.uom
                LEFT JOIN erp_branch_sales_order AS so
                ON
                    so.so_id = invoices.so_id
                LEFT JOIN erp_branch_sales_order_delivery_pgi AS pgi
                ON
                    pgi.so_delivery_pgi_id = invoices.pgi_id
                LEFT JOIN erp_acc_journal AS journal
                ON
                    journal.id = invoices.journal_id
                LEFT JOIN erp_acc_credit AS credit
                ON
                    credit.journal_id = journal.id AND credit.glId = 250
                LEFT JOIN erp_acc_debit AS debit
                ON
                    debit.journal_id = journal.id AND debit.glId = 250
                WHERE
                    invoices.company_id = $company_id  AND invoices.branch_id = $branch_id  AND invoices.location_id = $location_id AND invoices.status =               'active'
                UNION ALL
                SELECT
                	cn.taxComponents as tax,
                    cn.credit_note_no AS document_no,
                    invoices.invoice_no AS reference_no,
                    cn.postingDate AS posting_date,
                    'CN' AS TYPE,
                    kam.kamName AS sales_person_name,
                    customer.customer_code AS customer_code,
                    customer.trade_name AS customer_name,
                    invoices.customer_gstin AS gstin,
                    items.itemCode AS item_code,
                    items.hsnCode AS hsnCode,
                    items.itemName AS item_name,
                    groups.goodGroupName AS item_group,
                    COALESCE(-1 * cn_item.item_qty, 0) AS total_qty,
                    UOM.uomName AS uom,
                    0 AS mrp,
                    COALESCE(-1 * cn_item.item_rate, 0) AS rate,
                    COALESCE(
                        -1 * cn_item.item_qty * cn_item.item_rate,
                        0
                    ) AS gross_amount,
                    0 AS trade_disc,
                    0 AS trade_disc_amt,
                    COALESCE(
                        -1 * cn_item.item_qty * cn_item.item_rate,
                        0
                    ) AS base_amount,
                    0 AS cash_disc,
                    0 AS cash_disc_amt,
                    COALESCE(
                        -1 * cn_item.item_qty * cn_item.item_rate,
                        0
                    ) AS taxable_value,
                    COALESCE(-1 *(cn_item.cgst),
                    0) AS cgst,
                    COALESCE(-1 *(cn_item.sgst),
                    0) AS sgst,
                    COALESCE(-1 *(cn_item.igst),
                    0) AS igst,
                    COALESCE(-1 * cn_item.item_amount, 0) AS net_sales_value,
                    0 AS round_off,
                    COALESCE(-1 * cn_item.item_amount, 0) AS net_receivable,
                    customer.customer_authorised_person_phone AS mobile,
                    (
                    SELECT
                        address.customer_address_city
                    FROM
                        erp_customer_address AS address
                    WHERE
                        address.customer_id = customer.customer_id
                    LIMIT 1
                ) AS customer_address_city,
                (
                    SELECT
                        address.customer_address_state
                    FROM
                        erp_customer_address AS address
                    WHERE
                        address.customer_id = customer.customer_id
                    LIMIT 1
                ) AS customer_address_state,
                cn.created_by AS created_by,
                cn.created_at AS created_at,
                cn.updated_by updated_by,
                cn.updated_at updated_at
                FROM
                    erp_customer AS customer
                LEFT JOIN erp_branch_sales_order_invoices AS invoices
                ON
                    invoices.customer_id = customer.customer_id
                LEFT JOIN erp_kam AS kam
                ON
                    invoices.kamId = kam.kamId
                LEFT JOIN erp_branch_sales_order_invoice_items AS items
                ON
                    invoices.so_invoice_id = items.so_invoice_id
                LEFT JOIN erp_inventory_items AS inventory
                ON
                    items.inventory_item_id = inventory.itemId
                LEFT JOIN erp_inventory_mstr_good_groups AS groups
                ON
                    inventory.goodsGroup = groups.goodGroupId
                LEFT JOIN erp_inventory_mstr_uom AS UOM
                ON
                    UOM.uomId = items.uom
                LEFT JOIN erp_credit_note AS cn
                ON
                    cn.creditNoteReference = invoices.so_invoice_id AND cn.creditors_type = 'customer'
                LEFT JOIN credit_note_item AS cn_item
                ON
                    cn_item.credit_note_id = cn.cr_note_id AND cn_item.item_id = inventory.itemId AND cn_item.invoice_id = invoices.so_invoice_id
                LEFT JOIN erp_branch_sales_order AS so
                ON
                    so.so_id = invoices.so_id
                LEFT JOIN erp_branch_sales_order_delivery_pgi AS pgi
                ON
                    pgi.so_delivery_pgi_id = invoices.pgi_id
                WHERE
                    cn.company_id = $company_id  AND cn.branch_id = $branch_id  AND cn.location_id = $location_id AND cn.status = 'active'
                UNION ALL
                SELECT
                	dn.taxComponents as tax,
                    dn.debit_note_no AS document_no,
                    invoices.invoice_no AS reference_no,
                    dn.postingDate AS posting_date,
                    'DN' AS TYPE,
                    kam.kamName AS sales_person_name,
                    customer.customer_code AS customer_code,
                    customer.trade_name AS customer_name,
                    invoices.customer_gstin AS gstin,
                    items.itemCode AS item_code,
                    items.hsnCode AS hsnCode,
                    items.itemName AS item_name,
                    groups.goodGroupName AS item_group,
                    COALESCE(dn_item.item_qty, 0) AS total_qty,
                    UOM.uomName AS uom,
                    0 AS mrp,
                    COALESCE(dn_item.item_rate, 0) AS rate,
                    COALESCE(
                        dn_item.item_qty * dn_item.item_rate,
                        0
                    ) AS gross_amount,
                    COALESCE(items.totalDiscount, 0) AS trade_disc,
                    COALESCE(items.totalDiscountAmt, 0) AS trade_disc_amt,
                    COALESCE(
                        dn_item.item_qty * dn_item.item_rate,
                        0
                    ) AS base_amount,
                    COALESCE(items.cashDiscount, 0) AS cash_disc,
                    COALESCE(items.cashDiscountAmount, 0) AS cash_disc_amt,
                    COALESCE(
                        dn_item.item_qty * dn_item.item_rate,
                        0
                    ) AS taxable_value,
                    COALESCE((dn_item.cgst),
                    0) AS cgst,
                    COALESCE((dn_item.sgst),
                    0) AS sgst,
                    COALESCE((dn_item.igst),
                    0) AS igst,
                    COALESCE(dn_item.item_amount, 0) AS net_sales_value,
                    0 AS round_off,
                    COALESCE(dn_item.item_amount, 0) AS net_receivable,
                    customer.customer_authorised_person_phone AS mobile,
                    (
                    SELECT
                        address.customer_address_city
                    FROM
                        erp_customer_address AS address
                    WHERE
                        address.customer_id = customer.customer_id
                    LIMIT 1
                ) AS customer_address_city,
                (
                    SELECT
                        address.customer_address_state
                    FROM
                        erp_customer_address AS address
                    WHERE
                        address.customer_id = customer.customer_id
                    LIMIT 1
                ) AS customer_address_state,
                dn.created_by AS created_by,
                dn.created_at AS created_at,
                dn.updated_by updated_by,
                dn.updated_at updated_at
                FROM
                    erp_customer AS customer
                LEFT JOIN erp_branch_sales_order_invoices AS invoices
                ON
                    invoices.customer_id = customer.customer_id
                LEFT JOIN erp_kam AS kam
                ON
                    invoices.kamId = kam.kamId
                LEFT JOIN erp_branch_sales_order_invoice_items AS items
                ON
                    invoices.so_invoice_id = items.so_invoice_id
                LEFT JOIN erp_inventory_items AS inventory
                ON
                    items.inventory_item_id = inventory.itemId
                LEFT JOIN erp_inventory_mstr_good_groups AS groups
                ON
                    inventory.goodsGroup = groups.goodGroupId
                LEFT JOIN erp_inventory_mstr_uom AS UOM
                ON
                    UOM.uomId = items.uom
                LEFT JOIN erp_debit_note AS dn
                ON
                    dn.debitNoteReference = invoices.so_invoice_id AND dn.debitor_type = 'customer'
                LEFT JOIN debit_note_item AS dn_item
                ON
                    dn_item.debit_note_id = dn.dr_note_id AND dn_item.item_id = inventory.itemId AND dn_item.invoice_id = invoices.so_invoice_id
                LEFT JOIN erp_branch_sales_order AS so
                ON
                    so.so_id = invoices.so_id
                LEFT JOIN erp_branch_sales_order_delivery_pgi AS pgi
                ON
                    pgi.so_delivery_pgi_id = invoices.pgi_id
                WHERE
                    dn.company_id = $company_id  AND dn.branch_id = $branch_id  AND dn.location_id = $location_id AND dn.status = 'active'
                ) AS subquery
                WHERE
                    1
                $cond
                ORDER BY
                    posting_date
                DESC
    ";

        // $sql_Mainqry = $sql_list . "  ORDER BY so.so_id DESC LIMIT " . $offset . "," . $limit_per_Page . ";";
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
                    "document_no" => $data['document_no'],
                    "reference_no" => $data['reference_no'],
                    "posting_date" => formatDate($data['posting_date']),
                    "type" => $data['type']??"-",
                    "sales_person_name" => $data['sales_person_name'],
                    "customer_code" => $data['customer_code'],
                    "customer_name" => $data['customer_name'],
                    "gstin" => $data['gstin'],
                    "item_code" => $data['item_code'],
                    "hsnCode" => $data['hsnCode'],
                    "item_name" => $data['item_name'],
                    "item_group" => $data['item_group'],
                    "total_qty" => $data['total_qty'],
                    "uom" => $data['uom'],
                    "mrp" => $data['mrp'],
                    "rate" => $data['rate'],
                    "gross_amount" => $data['gross_amount'],
                    "trade_disc" => $data['trade_disc'],
                    "trade_disc_amt" => $data['trade_disc_amt'],
                    "base_amount" => $data['base_amount'],
                    "cash_disc" => $data['cash_disc'],
                    "cash_disc_amt" => $data['cash_disc_amt'],
                    "taxable_value" => $data['taxable_value'],
                    "cgst" => $data['cgst'],
                    "sgst" => $data['sgst'],
                    "igst" => $data['igst'],
                    "tax" => $data['tax'],
                    "net_sales_value" => $data['net_sales_value'],
                    "round_off" => $data['round_off'],
                    "net_receivable" => $data['net_receivable'],
                    "mobile" => $data['mobile'],
                    "customer_address_city" => $data['customer_address_city'],
                    "customer_address_state" => $data['customer_address_state'],
                    "created_by" => getCreatedByUser($data['created_by']),
                    "created_at" => formatDateWeb($data['created_at']),
                    "updated_by" => getCreatedByUser($data['updated_by']),
                    "updated_at" => formatDateWeb($data['updated_at']),
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
                // "sql" => $sql_list


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

            if ($slag === 'created_at' || $slag === 'updated_at' || $slag === 'posting_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif (
                $slag === "totalPrice" || $slag === "total_qty" || $slag === 'mrp' || $slag === 'rate' || $slag === 'gross_amount' ||
                $slag === 'trade_disc' || $slag === 'trade_disc_amt' || $slag === 'base_amount' ||
                $slag === 'cash_disc' || $slag === 'cash_disc_amt' || $slag === 'taxable_value' ||
                $slag === 'cgst' || $slag === 'sgst' || $slag === 'igst' ||
                $slag === 'net_sales_value' || $slag === 'round_off' || $slag === 'net_receivable'
            ) {
                $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
            } else if ($slag === 'so.created_by' || $slag === 'created_by') {

                $resultList = getAdminUserIdByName($data['value']);
                $conds .= $slag . " IN  " . " (" . $resultList . ")";
            } else if ($slag === 'updated_by') {

                $resultList = getAdminUserIdByName($data['value']);
                $conds .= $slag . " IN  " . " (" . $resultList . ")";
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));


        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        // $sts = " AND `status` !='deleted'";
        $sql_list = "SELECT
                    document_no,
                    reference_no,
                    posting_date,
                    TYPE,
                    sales_person_name,
                    customer_code,
                    customer_name,
                    gstin,
                    item_code,
                    hsnCode,
                    item_name,
                    item_group,
                    total_qty,
                    uom,
                    mrp,
                    rate,
                    gross_amount,
                    trade_disc,
                    trade_disc_amt,
                    base_amount,
                    cash_disc,
                    cash_disc_amt,
                    taxable_value,
                    cgst,
                    sgst,
                    igst,
                    net_sales_value,
                    round_off,
                    net_receivable,
                    mobile,
                    customer_address_city,
                    customer_address_state,
                    created_by,
                    created_at,
                    updated_by,
                    updated_at,
                    tax
                FROM
                    (
                    SELECT
                		invoices.taxComponents as tax,
                        invoices.invoice_no AS document_no,
                        COALESCE(pgi.pgi_no, so.so_number) AS reference_no,
                        invoices.invoice_date AS posting_date,
                        'INV' AS TYPE,
                        kam.kamName AS sales_person_name,
                        customer.customer_code AS customer_code,
                        customer.trade_name AS customer_name,
                        invoices.customer_gstin AS gstin,
                        items.itemCode AS item_code,
                        items.hsnCode AS hsnCode,
                        items.itemName AS item_name,
                        groups.goodGroupName AS item_group,
                        COALESCE(items.qty, 0) AS total_qty,
                        UOM.uomName AS uom,
                        COALESCE(items.unitPrice, 0) AS mrp,
                        COALESCE(items.itemTargetPrice, 0) AS rate,
                        COALESCE(
                            items.qty * items.itemTargetPrice,
                            0
                        ) AS gross_amount,
                        COALESCE(items.totalDiscount, 0) AS trade_disc,
                        COALESCE(items.totalDiscountAmt, 0) AS trade_disc_amt,
                        COALESCE(
                            (
                                items.qty * items.itemTargetPrice
                            ) - items.totalDiscountAmt,
                            0
                        ) AS base_amount,
                        COALESCE(items.cashDiscount, 0) AS cash_disc,
                        COALESCE(items.cashDiscountAmount, 0) AS cash_disc_amt,
                        COALESCE(
                            (
                                items.qty * items.itemTargetPrice
                            ) - items.totalDiscountAmt - items.cashDiscountAmount,
                            0
                        ) AS taxable_value,
                        CASE WHEN invoices.igst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax / 2) ELSE 0
                END AS cgst,
                CASE WHEN invoices.igst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax / 2) ELSE 0
                END AS sgst,
                CASE WHEN invoices.cgst = 0 AND invoices.sgst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax) ELSE 0
                END AS igst,
                COALESCE(
                    (
                        items.qty * items.itemTargetPrice
                    ) - items.totalDiscountAmt - items.cashDiscountAmount,
                    0
                ) + CASE WHEN invoices.igst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax / 2) ELSE 0
                END + CASE WHEN invoices.igst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax / 2) ELSE 0
                END + CASE WHEN invoices.cgst = 0 AND invoices.sgst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax) ELSE 0
                END AS net_sales_value,
                (
                    COALESCE(debit.debit_amount, 0) - COALESCE(credit.credit_amount, 0)
                ) AS round_off,
                (
                    COALESCE(
                        (
                            items.qty * items.itemTargetPrice
                        ) - items.totalDiscountAmt - items.cashDiscountAmount,
                        0
                    ) + CASE WHEN invoices.igst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax / 2) ELSE 0
                END + CASE WHEN invoices.igst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax / 2) ELSE 0
                END + CASE WHEN invoices.cgst = 0 AND invoices.sgst = 0 AND invoices.so_invoice_id = items.so_invoice_id THEN(items.totalTax) ELSE 0
                END
                ) -(
                    COALESCE(debit.debit_amount, 0) - COALESCE(credit.credit_amount, 0)
                ) AS net_receivable,
                customer.customer_authorised_person_phone AS mobile,
                (
                    SELECT
                        address.customer_address_city
                    FROM
                        erp_customer_address AS address
                    WHERE
                        address.customer_id = customer.customer_id
                    LIMIT 1
                ) AS customer_address_city,
                (
                    SELECT
                        address.customer_address_state
                    FROM
                        erp_customer_address AS address
                    WHERE
                        address.customer_id = customer.customer_id
                    LIMIT 1
                ) AS customer_address_state,
                invoices.created_by AS created_by,
                invoices.created_at AS created_at,
                invoices.updated_by updated_by,
                invoices.updated_at updated_at
                FROM
                    erp_customer AS customer
                LEFT JOIN erp_branch_sales_order_invoices AS invoices
                ON
                    invoices.customer_id = customer.customer_id
                LEFT JOIN erp_kam AS kam
                ON
                    invoices.kamId = kam.kamId
                LEFT JOIN erp_branch_sales_order_invoice_items AS items
                ON
                    invoices.so_invoice_id = items.so_invoice_id
                LEFT JOIN erp_inventory_items AS inventory
                ON
                    items.inventory_item_id = inventory.itemId
                LEFT JOIN erp_inventory_mstr_good_groups AS groups
                ON
                    inventory.goodsGroup = groups.goodGroupId
                LEFT JOIN erp_inventory_mstr_uom AS UOM
                ON
                    UOM.uomId = items.uom
                LEFT JOIN erp_branch_sales_order AS so
                ON
                    so.so_id = invoices.so_id
                LEFT JOIN erp_branch_sales_order_delivery_pgi AS pgi
                ON
                    pgi.so_delivery_pgi_id = invoices.pgi_id
                LEFT JOIN erp_acc_journal AS journal
                ON
                    journal.id = invoices.journal_id
                LEFT JOIN erp_acc_credit AS credit
                ON
                    credit.journal_id = journal.id AND credit.glId = 250
                LEFT JOIN erp_acc_debit AS debit
                ON
                    debit.journal_id = journal.id AND debit.glId = 250
                WHERE
                    invoices.company_id = $company_id  AND invoices.branch_id = $branch_id  AND invoices.location_id = $location_id AND invoices.status =               'active'
                UNION ALL
                SELECT
                	cn.taxComponents as tax,
                    cn.credit_note_no AS document_no,
                    invoices.invoice_no AS reference_no,
                    cn.postingDate AS posting_date,
                    'CN' AS TYPE,
                    kam.kamName AS sales_person_name,
                    customer.customer_code AS customer_code,
                    customer.trade_name AS customer_name,
                    invoices.customer_gstin AS gstin,
                    items.itemCode AS item_code,
                    items.hsnCode AS hsnCode,
                    items.itemName AS item_name,
                    groups.goodGroupName AS item_group,
                    COALESCE(-1 * cn_item.item_qty, 0) AS total_qty,
                    UOM.uomName AS uom,
                    0 AS mrp,
                    COALESCE(-1 * cn_item.item_rate, 0) AS rate,
                    COALESCE(
                        -1 * cn_item.item_qty * cn_item.item_rate,
                        0
                    ) AS gross_amount,
                    0 AS trade_disc,
                    0 AS trade_disc_amt,
                    COALESCE(
                        -1 * cn_item.item_qty * cn_item.item_rate,
                        0
                    ) AS base_amount,
                    0 AS cash_disc,
                    0 AS cash_disc_amt,
                    COALESCE(
                        -1 * cn_item.item_qty * cn_item.item_rate,
                        0
                    ) AS taxable_value,
                    COALESCE(-1 *(cn_item.cgst),
                    0) AS cgst,
                    COALESCE(-1 *(cn_item.sgst),
                    0) AS sgst,
                    COALESCE(-1 *(cn_item.igst),
                    0) AS igst,
                    COALESCE(-1 * cn_item.item_amount, 0) AS net_sales_value,
                    0 AS round_off,
                    COALESCE(-1 * cn_item.item_amount, 0) AS net_receivable,
                    customer.customer_authorised_person_phone AS mobile,
                    (
                    SELECT
                        address.customer_address_city
                    FROM
                        erp_customer_address AS address
                    WHERE
                        address.customer_id = customer.customer_id
                    LIMIT 1
                ) AS customer_address_city,
                (
                    SELECT
                        address.customer_address_state
                    FROM
                        erp_customer_address AS address
                    WHERE
                        address.customer_id = customer.customer_id
                    LIMIT 1
                ) AS customer_address_state,
                cn.created_by AS created_by,
                cn.created_at AS created_at,
                cn.updated_by updated_by,
                cn.updated_at updated_at
                FROM
                    erp_customer AS customer
                LEFT JOIN erp_branch_sales_order_invoices AS invoices
                ON
                    invoices.customer_id = customer.customer_id
                LEFT JOIN erp_kam AS kam
                ON
                    invoices.kamId = kam.kamId
                LEFT JOIN erp_branch_sales_order_invoice_items AS items
                ON
                    invoices.so_invoice_id = items.so_invoice_id
                LEFT JOIN erp_inventory_items AS inventory
                ON
                    items.inventory_item_id = inventory.itemId
                LEFT JOIN erp_inventory_mstr_good_groups AS groups
                ON
                    inventory.goodsGroup = groups.goodGroupId
                LEFT JOIN erp_inventory_mstr_uom AS UOM
                ON
                    UOM.uomId = items.uom
                LEFT JOIN erp_credit_note AS cn
                ON
                    cn.creditNoteReference = invoices.so_invoice_id AND cn.creditors_type = 'customer'
                LEFT JOIN credit_note_item AS cn_item
                ON
                    cn_item.credit_note_id = cn.cr_note_id AND cn_item.item_id = inventory.itemId AND cn_item.invoice_id = invoices.so_invoice_id
                LEFT JOIN erp_branch_sales_order AS so
                ON
                    so.so_id = invoices.so_id
                LEFT JOIN erp_branch_sales_order_delivery_pgi AS pgi
                ON
                    pgi.so_delivery_pgi_id = invoices.pgi_id
                WHERE
                    cn.company_id = $company_id  AND cn.branch_id = $branch_id  AND cn.location_id = $location_id AND cn.status = 'active'
                UNION ALL
                SELECT
                	dn.taxComponents as tax,
                    dn.debit_note_no AS document_no,
                    invoices.invoice_no AS reference_no,
                    dn.postingDate AS posting_date,
                    'DN' AS TYPE,
                    kam.kamName AS sales_person_name,
                    customer.customer_code AS customer_code,
                    customer.trade_name AS customer_name,
                    invoices.customer_gstin AS gstin,
                    items.itemCode AS item_code,
                    items.hsnCode AS hsnCode,
                    items.itemName AS item_name,
                    groups.goodGroupName AS item_group,
                    COALESCE(dn_item.item_qty, 0) AS total_qty,
                    UOM.uomName AS uom,
                    0 AS mrp,
                    COALESCE(dn_item.item_rate, 0) AS rate,
                    COALESCE(
                        dn_item.item_qty * dn_item.item_rate,
                        0
                    ) AS gross_amount,
                    COALESCE(items.totalDiscount, 0) AS trade_disc,
                    COALESCE(items.totalDiscountAmt, 0) AS trade_disc_amt,
                    COALESCE(
                        dn_item.item_qty * dn_item.item_rate,
                        0
                    ) AS base_amount,
                    COALESCE(items.cashDiscount, 0) AS cash_disc,
                    COALESCE(items.cashDiscountAmount, 0) AS cash_disc_amt,
                    COALESCE(
                        dn_item.item_qty * dn_item.item_rate,
                        0
                    ) AS taxable_value,
                    COALESCE((dn_item.cgst),
                    0) AS cgst,
                    COALESCE((dn_item.sgst),
                    0) AS sgst,
                    COALESCE((dn_item.igst),
                    0) AS igst,
                    COALESCE(dn_item.item_amount, 0) AS net_sales_value,
                    0 AS round_off,
                    COALESCE(dn_item.item_amount, 0) AS net_receivable,
                    customer.customer_authorised_person_phone AS mobile,
                    (
                    SELECT
                        address.customer_address_city
                    FROM
                        erp_customer_address AS address
                    WHERE
                        address.customer_id = customer.customer_id
                    LIMIT 1
                ) AS customer_address_city,
                (
                    SELECT
                        address.customer_address_state
                    FROM
                        erp_customer_address AS address
                    WHERE
                        address.customer_id = customer.customer_id
                    LIMIT 1
                ) AS customer_address_state,
                dn.created_by AS created_by,
                dn.created_at AS created_at,
                dn.updated_by updated_by,
                dn.updated_at updated_at
                FROM
                    erp_customer AS customer
                LEFT JOIN erp_branch_sales_order_invoices AS invoices
                ON
                    invoices.customer_id = customer.customer_id
                LEFT JOIN erp_kam AS kam
                ON
                    invoices.kamId = kam.kamId
                LEFT JOIN erp_branch_sales_order_invoice_items AS items
                ON
                    invoices.so_invoice_id = items.so_invoice_id
                LEFT JOIN erp_inventory_items AS inventory
                ON
                    items.inventory_item_id = inventory.itemId
                LEFT JOIN erp_inventory_mstr_good_groups AS groups
                ON
                    inventory.goodsGroup = groups.goodGroupId
                LEFT JOIN erp_inventory_mstr_uom AS UOM
                ON
                    UOM.uomId = items.uom
                LEFT JOIN erp_debit_note AS dn
                ON
                    dn.debitNoteReference = invoices.so_invoice_id AND dn.debitor_type = 'customer'
                LEFT JOIN debit_note_item AS dn_item
                ON
                    dn_item.debit_note_id = dn.dr_note_id AND dn_item.item_id = inventory.itemId AND dn_item.invoice_id = invoices.so_invoice_id
                LEFT JOIN erp_branch_sales_order AS so
                ON
                    so.so_id = invoices.so_id
                LEFT JOIN erp_branch_sales_order_delivery_pgi AS pgi
                ON
                    pgi.so_delivery_pgi_id = invoices.pgi_id
                WHERE
                    dn.company_id = $company_id  AND dn.branch_id = $branch_id  AND dn.location_id = $location_id AND dn.status = 'active'
                ) AS subquery
                WHERE
                    1
                $cond
                ORDER BY
                    posting_date
                DESC
    ";
        $sqlMainQryObj = queryGet($sql_list, true);

        $dynamic_data_all = [];
        $num_list = $sqlMainQryObj['numRows'];
        $sql_data = $sqlMainQryObj['data'];
        $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        if ($num_list > 0) {
            foreach ($sql_data as $data) {

                $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "document_no" => $data['document_no'],
                    "reference_no" => $data['reference_no'],
                    "posting_date" => formatDate($data['posting_date']),
                    "type" => $data['type']??"-",
                    "sales_person_name" => $data['sales_person_name'],
                    "customer_code" => $data['customer_code'],
                    "customer_name" => $data['customer_name'],
                    "gstin" => $data['gstin'],
                    "item_code" => $data['item_code'],
                    "hsnCode" => $data['hsnCode'],
                    "item_name" => $data['item_name'],
                    "item_group" => $data['item_group'],
                    "total_qty" => $data['total_qty'],
                    "uom" => $data['uom'],
                    "mrp" => $data['mrp'],
                    "rate" => $data['rate'],
                    "gross_amount" => $data['gross_amount'],
                    "trade_disc" => $data['trade_disc'],
                    "trade_disc_amt" => $data['trade_disc_amt'],
                    "base_amount" => $data['base_amount'],
                    "cash_disc" => $data['cash_disc'],
                    "cash_disc_amt" => $data['cash_disc_amt'],
                    "taxable_value" => $data['taxable_value'],
                    "cgst" => $data['cgst'],
                    "sgst" => $data['sgst'],
                    "igst" => $data['igst'],
                    "tax" => $data['tax'],
                    "net_sales_value" => $data['net_sales_value'],
                    "round_off" => $data['round_off'],
                    "net_receivable" => $data['net_receivable'],
                    "mobile" => $data['mobile'],
                    "customer_address_city" => $data['customer_address_city'],
                    "customer_address_state" => $data['customer_address_state'],
                    "created_by" => getCreatedByUser($data['created_by']),
                    "created_at" => formatDateWeb($data['created_at']),
                    "updated_by" => getCreatedByUser($data['updated_by']),
                    "updated_at" => formatDateWeb($data['updated_at']),
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
}