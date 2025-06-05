<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
// require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once(BASE_DIR."app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../app/v1/functions/branch/func-discount-controller.php");

$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_GET['act'] === "tc") {

 $tc_id = $_GET['value'];
         //   echo "SELECT * FROM `erp_terms_and_condition_format` WHERE tc_slug='invoice' AND tc_id=$tc_id";
            $qry = queryGet("SELECT * FROM `erp_terms_and_condition_format` WHERE tc_slug='invoice' AND tc_id=$tc_id")['data'];
        
            $responseData['termscond'] = stripcslashes(unserialize($qry['tc_text']));
            $responseData['termHead'] = $qry['tc_variant'];


    echo json_encode($responseData);

       

}


?>