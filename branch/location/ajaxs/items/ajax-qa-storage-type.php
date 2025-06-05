<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-goods-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
$goodsObj = new GoodsController();
$responseData['qa_storage'] = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $type = $_GET['type'];
    $sql = queryGet("SELECT * FROM `erp_storage_location` WHERE `location_id` = $location_id AND `storage_location_material_type` = '" . $type . "' AND `storage_location_type` = 'QA'", true);
    if ($sql['numRows'] > 0) {
        $responseData['qa_storage'] .=  '<label for="">QA Storage Location</label>
    <select id="qa_storage" name="qa_storage" class="select2 form-control">';
        foreach ($sql['data'] as $data) {


            //echo '<option>'. $data['storage_location_name'] .'</option>';storage_location_id

            $responseData['qa_storage'] .=   '<option value="' . $data["storage_location_id"] . '" >' . $data["storage_location_name"] . '</option>';
        }

        $responseData['qa_storage'] .=  '</select>';

        $responseData['numRows'] = $sql['numRows'];
    }
     else{
        $responseData['qa_storage'] .= '<p>You Have no QA storage Location</p>';
        $responseData['numRows'] = $sql['numRows'];
       
    }


}

echo json_encode($responseData);
?>

