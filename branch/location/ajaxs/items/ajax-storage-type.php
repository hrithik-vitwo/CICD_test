<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-goods-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
$goodsObj = new GoodsController();


if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $type = $_GET['type'];
    $sql = queryGet("SELECT * FROM `erp_storage_location` WHERE `location_id` = $location_id AND `storage_location_material_type` = '".$type."' AND `storage_location_type` != 'QA'",true);

   // console($sql);
   foreach($sql['data'] as $data){
   

//echo '<option>'. $data['storage_location_name'] .'</option>';storage_location_id

echo '<option value="'.$data["storage_location_id"].'" >'.$data["storage_location_name"].'</option>';

   }

}


?>