<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-goods-controller.php");

$headerData = array('Content-Type: application/json');
$responseData = [];
$companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $typeId = $_GET['typeId'];
  
 function fetchItems($parent_id = null)
 {
   
   global $company_id;
   global $typeId;
   $sql = queryGet("SELECT * FROM `erp_inventory_mstr_good_groups` WHERE  `goodType`=".$typeId." AND `companyId` = $company_id AND groupParentId " . ($parent_id === null ? "= 0" : "= $parent_id"), true);
   $items = [];

   if ($sql['numRows'] > 0) {
     foreach ($sql['data'] as $row) {
       $item = [
         'id' => $row['goodGroupId'],
         'title' => $row['goodGroupName']
       ];

       // Recursively fetch subitems
       $subitems = fetchItems($row['goodGroupId']);
       if (!empty($subitems)) {
         $item['subs'] = $subitems;
       }

       $items[] = $item;
     }
   }
   // console($items);
   return $items;
 }

 $fetchItems = fetchItems();
   // http_response_code(200);
  echo json_encode($fetchItems, true);
  
}
