<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../app/v1/functions/admin/func-company.php");
$headerData = array('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $gl = $_POST['selectedOption'];

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
    FROM erp_extra_sub_ledger WHERE `parentGlId` = $gl AND company_id =$company_id ", true);





  if ($subchartOfAcc['status'] == 'success') {
    $numrows = $subchartOfAcc['numRows'];
    $list = '<option value=' . '"0"' . '>Select Sub Ledger</option>';
    foreach ($subchartOfAcc['data'] as $subchart) {

      $list .= '<option value="' . $subchart['code'] . '" data-parent="' . $subchart['parentGlId'] . '">' . $subchart['name'] . '&nbsp;||&nbsp;' . $subchart['code'] . '</option>';
    }
  }

  $headerData['list'] = $list;
  $headerData['numRows'] = $numrows;

  echo json_encode($headerData);
}
