<?php

require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
// echo '<option value=""  data-attr = "vendor">'.$_REQUEST['listtype'].'</option>';
// console($_REQUEST);

$val = explode("|", $_REQUEST['listtype']);
$id = $val[0];

$option = $val[1];
$responseData = [];
$start = 0;
$limit = 30;
$cond = '';

if ($option == 'customer') {
    if (isset($_REQUEST['searchTerm']) && !empty($_REQUEST['searchTerm'])) {
        $searchTerm = $_REQUEST['searchTerm'];

        $cond .= " AND (`invoice_no` like '%" . $searchTerm . "%' OR `so_number` like '%" . $searchTerm . "%')";
        $limit = 50;
    }
    if (!empty($_REQUEST['page'])) {
        $start = $_REQUEST['page'] * $limit;
    }

    $custsql = "SELECT CONCAT(so_invoice_id,'|inv|',invoice_no,'|',compInvoiceType) as id, invoice_no as text FROM `erp_branch_sales_order_invoices` WHERE 1 " . $cond . " AND company_id='" . $company_id . "' AND (journal_id!=0 OR journal_id IS NOT NULL) AND  `status`='active' AND  `customer_id` = $id  LIMIT $start,$limit";
    $query = queryGet($custsql, true);
    if ($query['status'] = "success") {

        $returnData['status'] = "success";
        $returnData['message'] = "Data found";
        $returnData['data'] = $query['data'];
        $datalist = $query['data'];
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = [];

        $datalist = [];
    }
} else {

  
    if (isset($_REQUEST['searchTerm']) && !empty($_REQUEST['searchTerm'])) {
        $searchTerm = $_REQUEST['searchTerm'];

        $cond .= " AND (`grnIvCode` like '%" . $searchTerm . "%' OR `vendorDocumentNo` like '%" . $searchTerm . "%' OR `grnCode` like '%" . $searchTerm . "%')";
        $limit = 50;
    }
    if (!empty($_REQUEST['page'])) {
        $start = $_REQUEST['page'] * $limit;
    }

    $custsql = "SELECT CONCAT(grnIvId,'|grn|',grnIvCode) as id, CONCAT(grnIvCode,' || ',vendorDocumentNo) as text FROM `erp_grninvoice` WHERE 1 " . $cond . " AND companyId='" . $company_id . "' AND (ivPostingJournalId!=0 OR ivPostingJournalId IS NOT NULL) AND  `grnStatus`='active' AND `vendorId` = $id LIMIT $start,$limit";
    $query = queryGet($custsql, true);
    if ($query['status'] = "success") {

        $returnData['status'] = "success";
        $returnData['message'] = "Data found";
        $returnData['data'] = $query['data'];
        $datalist = $query['data'];
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = [];

        $datalist = [];
    }
}


echo json_encode($datalist);
