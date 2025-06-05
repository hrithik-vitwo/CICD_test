<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../../app/v1/functions/common/templates/template-sales-order-pgi-controller.php");
$headerData = array('Content-Type: application/json');

$BranchSoObj = new BranchSo();
$ItemsObj = new ItemsController();
$templateSalesOrderPgiController = new TemplateSalesOrderPgiController();

if ($_SERVER["REQUEST_METHOD"] == "GET" &&  $_GET["act"] == "modalData") {


    $so_delivery_pgi_id = $_GET['so_delivery_pgi_id'];
    $sql_list = "SELECT pgi.*, cust.trade_name, cust.customer_code, cust.customer_gstin, cust.customer_currency, cust.customer_status, custInvoiceLog.placeOfSupply, custAddress.customer_address_building_no, custAddress.customer_address_flat_no, custAddress.customer_address_street_name, custAddress.customer_address_pin_code, custAddress.customer_address_location, custAddress.customer_address_district, custAddress.customer_address_state,cust.customer_authorised_person_email,cust.customer_authorised_person_phone ,(SELECT so.igst  FROM `erp_branch_sales_order` as so WHERE so_number=pgi.so_number AND so.company_id=$company_id) as igst,(SELECT so.currency_name  FROM `erp_branch_sales_order` as so WHERE so_number=pgi.so_number AND so.company_id=$company_id) as currency_name FROM `erp_branch_sales_order_delivery_pgi` AS pgi LEFT JOIN erp_customer AS cust ON pgi.customer_id = cust.customer_id LEFT JOIN `erp_customer_invoice_logs` AS custInvoiceLog ON pgi.so_number = custInvoiceLog.ref_no LEFT JOIN `erp_customer_address` AS custAddress ON pgi.customer_id = custAddress.customer_address_id WHERE pgi.so_delivery_pgi_id=$so_delivery_pgi_id AND pgi.company_id=$company_id AND pgi.branch_id=$branch_id  AND pgi.location_id=$location_id";

    $sqlMainQryObj = queryGet($sql_list);;
    // console($sqlMainQryObj);

    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $dynamic_data = [];
    if ($num_list > 0) {
        $dynamic_data = [];


        $itemDetails = $BranchSoObj->fetchBranchSoDeliveryItemsPgi($so_delivery_pgi_id);

        $items = [];
        $allSubTotal = 0;
        $totalDis = 0;

        $batchSql = "SELECT LOG.refNumber AS refNo, LOG.logRef AS batch, LOG.storageLocationId, LOG.storageType, warehouse.warehouse_name,strloc.storage_location_name,items.itemCode, items.itemName, LOG.itemQty AS qty FROM erp_inventory_stocks_log AS LOG LEFT JOIN erp_branch_sales_order_delivery_pgi AS pgi ON LOG.refNumber = pgi.pgi_no LEFT JOIN erp_branch_sales_order_delivery_items_pgi AS items ON pgi.so_delivery_pgi_id = items.so_delivery_pgi_id LEFT JOIN erp_storage_location AS strloc ON strloc.storage_location_id = LOG.storageLocationId LEFT JOIN erp_storage_warehouse AS warehouse ON warehouse.warehouse_id = strloc.warehouse_id WHERE LOG.companyId = $company_id AND LOG.branchId = $branch_id AND LOG.locationId = $location_id  AND items.inventory_item_id =LOG.itemId AND LOG.refNumber = '" . $data['pgi_no'] . "' AND LOG.itemQty > 0 GROUP BY LOG.refNumber, LOG.logRef, LOG.storageLocationId, LOG.storageType, items.itemCode, items.itemName, LOG.itemQty;";

        $batchQuery = queryGet($batchSql, true);

        $countryCode_sql = queryGet("SELECT * FROM `erp_companies` WHERE `company_id` = " . $company_id . "")['data'];
        $countryCode = $countryCode_sql['company_country'];

        $navBtn = "";
        if ($data['invoiceStatus'] == 9) {
            // Decide the invoice page based on company country
            // $invoicePage = ($companyCountry == 103) ? 'direct-create-invoice.php' : 'invoice-creation.php';
            $invoicePage='invoice-creation.php';
            $navBtn .= ' <a href="' . $invoicePage . '?pgi_to_invoice=' . base64_encode($data['so_delivery_pgi_id']) . '" name="vendorEditBtn" class="btn btn-primary float-right mb-3">
            <i class="fa fa-plus"></i>
            Create Invoice
            </a>';
        }  elseif ($data['invoiceStatus'] == 1) {
            $navBtn .= '  <a class="btn btn-success float-right mb-3">
            <i class="fa fa-check mr-2" style="border-radius: 50%; background: #fff; padding: 5px; color: #198754;"></i>
            <span>Invoice Created</span>
            </a>';
        } else {
            $navBtn .= '<a class="btn btn-danger float-right mb-3">
            <i class="fa fa-exclamation mr-2" style="border-radius: 50%; background: #fff; padding: 5px; color: #198754;"></i>
            <span>Not Found</span>
            </a>';
        }
        $navBar = '<div class="action-btns display-flex-gap create-delivery-btn-sales" id="action-navbar">' . $navBtn . '</div>';



        $customerAddress = $data['customer_address_building_no'] . ', ' . $data['customer_address_flat_no'] . ', ' . $data['customer_address_street_name'] . ', ' . $data['customer_address_pin_code'] . ', ' . $data['customer_address_location'] . ', ' . $data['customer_address_district'] . ', ' . $data['customer_address_state'];


        $soId = queryGet("SELECT so_id FROM `erp_branch_sales_order` WHERE so_number='" . $data['so_number'] . "'")['data']['so_id'];
        $dynamic_data = [
            "dataObj" => $data,
            "customer_address" => getCustomerPrimaryAddressById($data['customer_id']),
            "item_details" => $batchQuery['data'],
            "placeOfsupply" => getStateDetail($data['placeOfSupply'])['data']['gstStateName'],
            "currecy_name_words" => number_to_words_indian_rupees($data['totalAmount']),
            "ccode" => str_replace('/', '-', $data['pgi_no']),
            "createdBy" => getCreatedByUser($data['created_by']),
            "updatedBy" => getCreatedByUser($data['updated_by']),
            "createdAt" => formatDateORDateTime($data['created_at']),
            "updateAt" => formatDateORDateTime($data['updated_at']),
            "soId" => $soId,
            "so_IdBase" => base64_encode($soId),
            "allSubTotal" => $allSubTotal,
            "navBtn" => $navBar,
            "currency_name" => $data['currency_name'],
            "countryCode" => $_SESSION['logedBranchAdminInfo']['companyCountry'],
            "taxName" => getTaxName($_SESSION['logedBranchAdminInfo']['companyCountry'])['data'],
            "country_labels" => json_decode(getLebels($_SESSION['logedBranchAdminInfo']['companyCountry'])['data']),
            "sqlMainQryObj" => $sqlMainQryObj
        ];
        $res = [
            "status" => true,
            "msg" => "success",
            "data" => $dynamic_data
        ];
    } else {
        $res = [
            "status" => false,
            "msg" => "Error!",
            "sql" => $sql_list
        ];
    }
    echo json_encode($res);
} else if ($_SERVER["REQUEST_METHOD"] == "GET" &&  $_GET["act"] == "classicView") {
    $pgiId = $_GET["so_delivery_pgi_id"];
    $templateSalesOrderPgiController->printSalesOrderPgi($pgiId);
    // echo $templateSalesOrderPgiController;
}
