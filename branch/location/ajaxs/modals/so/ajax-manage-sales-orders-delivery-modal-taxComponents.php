<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../../app/v1/functions/common/templates/template-sales-order-controller-taxComponents.php");
// require_once("../../../../../app/v1/functions/common/templates/template-so-delivery.controller.php");
$headerData = array('Content-Type: application/json');
$BranchSoObj = new BranchSo();
$ItemsObj = new ItemsController();
$dbObj = new Database();
$templateSalesOrderdelvControllerObj = new TemplateSalesOrderControllerTaxComponents();

if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act']=="modalData") {
    $so_delivery_id = $_GET['so_delivery_id'];
    $sql_list = "SELECT delv.*, cust.trade_name, cust.customer_code, cust.customer_gstin,cust.customer_pan,cust.customer_currency, cust.customer_status, custInvoiceLog.placeOfSupply, custAddress.customer_address_building_no, custAddress.customer_address_flat_no, custAddress.customer_address_street_name, custAddress.customer_address_pin_code, custAddress.customer_address_location, custAddress.customer_address_district, custAddress.customer_address_state,func.functionalities_name , custInvoiceLog.complianceInvoiceType,cust.customer_authorised_person_email, cust.customer_authorised_person_phone, salesOrder.igst FROM `erp_branch_sales_order_delivery` AS delv LEFT JOIN erp_customer AS cust ON delv.customer_id = cust.customer_id LEFT JOIN `erp_customer_invoice_logs` AS custInvoiceLog ON delv.so_number = custInvoiceLog.ref_no LEFT JOIN `erp_customer_address` as custAddress ON delv.customer_id = custAddress.customer_address_id LEFT JOIN erp_company_functionalities as func ON delv.profit_center = func.functionalities_id LEFT JOIN `erp_branch_sales_order` as salesOrder ON delv.so_id=salesOrder.so_id WHERE 1 " . $cond . " AND delv.company_id='" . $company_id . "' AND delv.branch_id='" . $branch_id . "' AND delv.location_id='" . $location_id . "' AND delv.so_delivery_id =" . $so_delivery_id . " AND delv.status !='deleted' ORDER BY delv.so_id DESC;";

    $sqlMainQryObj = $dbObj->queryGet($sql_list);
    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];
    $dynamic_data = [];
    $deleveryNo=$data['delivery_no'];
    $checksql=$dbObj->queryGet("SELECT * FROM `erp_branch_sales_order_delivery_pgi` WHERE `delivery_no`='$deleveryNo'")['numRows'];
    if ($num_list > 0) {
        $dynamic_data = [];
        $customerAddress = $data['customer_address_building_no'] . ', ' . $data['customer_address_flat_no'] . ', ' . $data['customer_address_street_name'] . ', ' . $data['customer_address_pin_code'] . ', ' . $data['customer_address_location'] . ', ' . $data['customer_address_district'] . ', ' . $data['customer_address_state'];
        $itemDetails[$data['so_id']] = $BranchSoObj->fetchBranchSoItems($data['so_id'])['data'];
        $customerDetails[$data['so_id']] = $BranchSoObj->fetchCustomerDetails($data['customer_id'])['data'][0];
        $items = [];
        $allSubTotal = 0;
        $totalDis = 0;
        $totalTax = 0;
        $actionBTn = "";
        $itemDetail = queryGet("SELECT LOG.refNumber AS del_code, LOG.logRef AS batch, LOG.storageLocationId, strLoc.storage_location_name, warehouse.warehouse_name, LOG.storageType, items.itemCode, items.itemName, LOG.itemQty AS qty FROM erp_inventory_stocks_log AS LOG LEFT JOIN erp_branch_sales_order_delivery AS delivery ON LOG.refNumber = delivery.delivery_no LEFT JOIN erp_branch_sales_order_delivery_items AS items ON delivery.so_delivery_id = items.so_delivery_id LEFT JOIN `erp_storage_location` as strLoc ON LOG.storageLocationId=strLoc.storage_location_id LEFT JOIN erp_storage_warehouse AS warehouse ON warehouse.warehouse_id = strLoc.warehouse_id WHERE LOG.companyId = " . $company_id . " AND LOG.branchId = " . $branch_id . " AND LOG.locationId = " . $location_id . "  AND items.inventory_item_id =LOG.itemId AND LOG.refNumber='" . $data['delivery_no'] . "' AND LOG.itemQty>0 GROUP BY LOG.refNumber, LOG.logRef, LOG.storageLocationId, LOG.storageType, items.itemCode, items.itemName, strLoc.storage_location_name, warehouse.warehouse_name, LOG.itemQty", true);

        $companyDetailsObj = queryGet("SELECT company_currency FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
        $companyCurrency = $companyDetailsObj['company_currency'];
        // console($itemDetail);
        // <!-- action btn  -->
        if($checksql==0){
            $actionBTn .= ' <div class="action-btns display-flex-gap" id="action-navbar">
            <a href="pgi-actions.php?create-pgi=' . base64_encode($so_delivery_id) . '" class="btn btn-primary pgi-create-btn" title="Create PGI"><i class="fa fa-box"></i>Create PGI</a>
            </div>';
        }

        $dynamic_data = [
            "dataObj" => $data,
            "customer_address" => getCustomerPrimaryAddressById($data['customer_id']),
            "placeOfsupply" => getStateDetail($data['placeOfSupply'])['data']['gstStateName'],
            "currecy_name_words" => number_to_words_indian_rupees($data['totalAmount']),
            "so_IdBase" => base64_encode($data['so_id']),
            "item_details" => $itemDetail['data'],
            "created_by" => getCreatedByUser($data['created_by']),
            "created_at" => formatDateORDateTime($data['created_at']),
            "updated_by" => getCreatedByUser($data['updated_by']),
            "updated_at" => formatDateORDateTime($data['updated_at']),
            "currency" => getSingleCurrencyType($company_currency),
            "allSubTotal" => $allSubTotal,
            "totalTax" => $totalTax,
            "actionBTn" => $actionBTn
        ];

        $res = [
            "status" => true,
            "msg" => "Success",
            "data" => $dynamic_data,
            "numrows"=>$checksql
        ];
    } else {
        $res = [
            "status" => false,
            "msg" => "Error!",
            "sql" => $sql_list
        ];
    }
    echo json_encode($res);
}else if($_SERVER['REQUEST_METHOD'] =="GET"&& $_GET['act']="classicView"){
    
        $so_delivery_id = $_GET['so_delivery_id'];
        $so_id=queryGet("SELECT so_id FROM `erp_branch_sales_order_delivery` WHERE so_delivery_id=$so_delivery_id")['data']['so_id'];
        $templateSalesOrderdelvControllerObj->printDelivery($so_delivery_id);
        // echo $templateSalesOrderdelvControllerObj;   
    
}
