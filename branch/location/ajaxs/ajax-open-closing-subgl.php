<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
$headerData = array('Content-Type: application/json');
$response = [];

$gl = $_GET['gl'];
$type = $_GET['type'];
$glMapp = get_object_vars(json_decode(getAllfetchAccountingMappingArray($company_id)));
$response['status'] = 'warning';
$response['makingVal'] = '';

if ($glMapp['status'] == 'success') {
    $array = get_object_vars($glMapp['data']);
    // console($array);
    $key = array_search($gl, $array);
    $rand = rand(0000, 9999);
    $response['status'] = 'success';
    // Output the result
    if ($key == 'vendor_gl') {
        // echo 'vendor_gl';
        $sql = "SELECT * FROM " . ERP_VENDOR_DETAILS . " WHERE company_id =$company_id AND vendor_status='active'";
        $qry = queryGet($sql, true);
        if ($qry['status'] == 'success') {
            $response['makingVal'] = '<option value="">--Select--</option>';
            foreach ($qry['data'] as $data) {
                $response['makingVal'] .= '<option value="' . $data['vendor_code'] . '|' . $data['trade_name'] . '">' . $data['vendor_code'] . '|' . $data['trade_name'] . '</option>';
            }
        }
    } else if ($key == 'customer_gl') {
        // echo 'customer_gl';
        $sql = "SELECT * FROM " . ERP_CUSTOMER . " WHERE company_id =$company_id AND customer_status='active'";
        $qry = queryGet($sql, true);
        if ($qry['status'] == 'success') {
            $response['makingVal'] = '<option value="">--Select--</option>';
            foreach ($qry['data'] as $data) {
                $response['makingVal'] .= '<option value="' . $data['customer_code'] . '|' . $data['trade_name'] . '">' . $data['customer_code'] . '|' . $data['trade_name'] . '</option>';
            }
        }
    } else if ($key == 'bank_gl' || $key == 'cash_gl') {
        // echo 'bankcash';
        $sql = "SELECT * FROM " . ERP_ACC_BANK_CASH_ACCOUNTS . " WHERE company_id =$company_id AND parent_gl =$gl AND status='active'";
        $qry = queryGet($sql, true);
        if ($qry['status'] == 'success') {
            $response['makingVal'] = '<option value="">--Select--</option>';
            foreach ($qry['data'] as $data) {
                $response['makingVal'] .= '<option value="' . $data['acc_code'] . '|' . $data['bank_name'] . '">' . $data['acc_code'] . '|' . $data['bank_name'] . '</option>';
            }
        }
    } else if ($key == 'itemsRM_gl' || $key == 'itemsSFG_gl' || $key == 'itemsFG_gl') {
        // echo 'item';
        $sql = "SELECT itemCode,itemName FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` as stock LEFT JOIN `" . ERP_INVENTORY_ITEMS . "` as goods ON stock.itemId=goods.itemId WHERE  stock.status='active' AND stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.parentGlId=$gl AND goods.itemId != '' ORDER BY stock.stockSummaryId desc";
        $qry = queryGet($sql, true);
        if ($qry['status'] == 'success') {
            $response['makingVal'] = '<option value="">--Select--</option>';
            foreach ($qry['data'] as $data) {
                $response['makingVal'] .= '<option value="' . $data['itemCode'] . '|' . $data['itemName'] . '">' . $data['itemCode'] . '|' . $data['itemName'] . '</option>';
            }
        }
    } else {
        $sql = "SELECT itemCode,itemName FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` as stock LEFT JOIN `" . ERP_INVENTORY_ITEMS . "` as goods ON stock.itemId=goods.itemId WHERE  stock.status='active' AND stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.parentGlId=$gl AND goods.itemId != '' ORDER BY stock.stockSummaryId desc";
        $qry = queryGet($sql, true);
        if ($qry['status'] == 'success') {
            $response['makingVal'] = '<option value="">--Select--</option>';
            foreach ($qry['data'] as $data) {
                $response['makingVal'] .= '<option value="' . $data['itemCode'] . '|' . $data['itemName'] . '">' . $data['itemCode'] . '|' . $data['itemName'] . '</option>';
            }
        }
    }
}




echo json_encode($response);
//echo $warning;
