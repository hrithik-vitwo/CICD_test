<?php
require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_POST['act'] == 'getVerientExamplecopy') {

    // ///WrongStart
    if(isset($_POST['vid']) && !empty($_POST['vid'])){
        $status='success';
        $iv_varient_id=$_POST['vid'];
    }else{
        $iv_varient_map = queryGet("SELECT * FROM `erp_branch_func_cn_varient_map` WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND functional_area_id=".$_POST['functionalArea']." AND status=1");
        $status=$iv_varient_map['status'];
        $iv_varient_id=$iv_varient_map['data']['iv_varient_id'];
    }

    // //WrongEnd

    // if ($status == 'success') {
    //     $iv_varient = queryGet("SELECT * FROM `erp_iv_varient` WHERE company_id=$company_id AND id=".$iv_varient_id." AND status='active'");
    //     $responseData = [
    //         "status" => "success",
    //         "message" => "Record found",
    //         "id" => $iv_varient['data']['id'],
    //         "iv_number_example" => $iv_varient['data']['iv_number_example']
    //     ];

    // } else {
    //     $iv_varient = queryGet("SELECT * FROM `erp_iv_varient` WHERE company_id=$company_id AND flag_default=0 AND status='active'");
    //     $responseData = [
    //         "status" => "success",
    //         "message" => "Record found",
    //         "id" => $iv_varient['data']['id'],
    //         "iv_number_example" => $iv_varient['data']['iv_number_example']
    //     ];

    // }

    $responseData=getCNNumberByVerientView($iv_varient_id);

}elseif($_POST['act'] == 'getInvoiceNumberByVerient') {   

    
// $responseData=getCNNumberByVerient($_POST['vid']);

    
}else{
    $responseData = [
        "status" => "warning",
        "message" => "Something went wrong"
    ];
}

echo json_encode($responseData);
