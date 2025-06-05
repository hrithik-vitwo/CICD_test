
<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
$headerData = array('Content-Type: application/json');
$response = [];

$gl = $_GET['gl'];
$response['makingVal'] = '';
// $type = $_GET['type'];
// $glMapp = get_object_vars(json_decode(getAllfetchAccountingMappingArray($company_id)));

// if ($glMapp['status'] == 'success') {
//     $array = get_object_vars($glMapp['data']);
//     // console($array);
//     $key = array_search($gl, $array);
//     $rand = rand(0000, 9999);

//     // Output the result
//     if ($key == 'vendor_gl') {
//         // echo 'vendor_gl';
//         $sql = "SELECT * FROM " . ERP_VENDOR_DETAILS . " WHERE company_id =$company_id AND vendor_status='active'";
//         $qry = queryGet($sql, true);
//         if ($qry['status'] == 'success') {
//             $response['makingVal'] = '
//                 <option value="">--Select--</option>';
//             foreach ($qry['data'] as $data) {
//                 $response['makingVal'] .= '<option value="' . $data['vendor_code'] . '|' . $data['trade_name'] . '">' . $data['vendor_code'] . '|' . $data['trade_name'] . '</option>';
//             }
            
//         }
//     } else if ($key == 'customer_gl') {
//         // echo 'customer_gl';
//         $sql = "SELECT * FROM " . ERP_CUSTOMER . " WHERE company_id =$company_id AND customer_status='active'";
//         $qry = queryGet($sql, true);
//         if ($qry['status'] == 'success') {
//             $response['makingVal'] = '
//                 <option value="">--Select--</option>';
//             foreach ($qry['data'] as $data) {
//                 $response['makingVal'] .= '<option value="' . $data['customer_code'] . '|' . $data['trade_name'] . '">' . $data['customer_code'] . '|' . $data['trade_name'] . '</option>';
//             }
//         }
//     } else if ($key == 'bank_gl' || $key == 'cash_gl') {
//         // echo 'bankcash';
//         $sql = "SELECT * FROM " . ERP_ACC_BANK_CASH_ACCOUNTS . " WHERE company_id =$company_id AND parent_gl =$gl AND status='active'";
//         $qry = queryGet($sql, true);
//         if ($qry['status'] == 'success') {
//             $response['makingVal'] = '
//                 <option value="">--Select--</option>';
//             foreach ($qry['data'] as $data) {
//                 $response['makingVal'] .= '<option value="' . $data['acc_code'] . '|' . $data['bank_name'] . '">' . $data['acc_code'] . '|' . $data['bank_name'] . '</option>';
//             }
//         }
//     } else if ($key == 'itemsRM_gl' || $key == 'itemsSFG_gl' || $key == 'itemsFG_gl') {
//         // echo 'item';
//         $sql = "SELECT itemCode,itemName FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` as stock LEFT JOIN `" . ERP_INVENTORY_ITEMS . "` as goods ON stock.itemId=goods.itemId WHERE  stock.status='active' AND stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.parentGlId=$gl AND goods.itemId != '' ORDER BY stock.stockSummaryId desc";
//         $qry = queryGet($sql, true);
//         if ($qry['status'] == 'success') {
//             $response['makingVal'] = '
//                 <option value="">--Select--</option>';
//             foreach ($qry['data'] as $data) {
//                 $response['makingVal'] .= '<option value="' . $data['itemCode'] . '|' . $data['itemName'] . '">' . $data['itemCode'] . '|' . $data['itemName'] . '</option>';
//             }
//         }
//     } else {
//         $sql = "SELECT itemCode,itemName FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` as stock LEFT JOIN `" . ERP_INVENTORY_ITEMS . "` as goods ON stock.itemId=goods.itemId WHERE  stock.status='active' AND stock.company_id=$company_id AND stock.branch_id=$branch_id AND stock.location_id=$location_id AND goods.parentGlId=$gl AND goods.itemId != '' ORDER BY stock.stockSummaryId desc";
//         $qry = queryGet($sql, true);
//         if ($qry['status'] == 'success') {
//             $response['makingVal'] = '
//                 <option value="">--Select--</option>';
//             foreach ($qry['data'] as $data) {
//                 $response['makingVal'] .= '<option value="' . $data['itemCode'] . '|' . $data['itemName'] . '">' . $data['itemCode'] . '|' . $data['itemName'] . '</option>';
//             }
//         }
//     }
// }



$subchartOfAcc = queryGet("SELECT customer_code AS code, trade_name AS name, parentGlId, 'Customer' AS type
FROM erp_customer WHERE `parentGlId` = $gl AND company_id =$company_id
UNION ALL
SELECT vendor_code AS code, trade_name AS name, parentGlId, 'Vendor' AS type
FROM erp_vendor_details WHERE `parentGlId` = $gl AND company_id =$company_id
UNION ALL
SELECT itemCode AS code, itemName AS name, parentGlId, 'Item' AS type
FROM erp_inventory_items WHERE `parentGlId` = $gl AND company_id =$company_id
UNION ALL
SELECT acc_code AS code, bank_name AS name, parent_gl AS parentGlId, 'Bank' AS type
FROM erp_acc_bank_cash_accounts WHERE `parent_gl` = $gl AND company_id =$company_id
UNION ALL
SELECT sl_code AS code, sl_name AS name, parentGlId, 'SubGL' AS type
FROM erp_extra_sub_ledger WHERE `parentGlId` = $gl AND company_id =$company_id", true);








if ($subchartOfAcc['status'] == 'success') {
$numrows = $subchartOfAcc['numRows'];
$list = '<option value="">Select Sub Ledger</option>';
foreach ($subchartOfAcc['data'] as $subchart) {

 $list .= '<option value="' . $subchart['code']  . '|' . $subchart['name'] . '" data-parent="' . $subchart['parentGlId'] . '">' . $subchart['name'] . '&nbsp;||&nbsp;' . $subchart['code'] . '</option>';
}
}

$headerData['makingVal'] = $list;
$headerData['numRows'] = $numrows;

echo json_encode($headerData);

//echo $warning;
