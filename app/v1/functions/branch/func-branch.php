<?php

  
// branch deatils
function getBranchDeatilsById($id=null) {
    
    $selSql = "SELECT `branch_id`, `company_id`, `branch_code`, `branch_name`, `branch_gstin`, `con_business`, `build_no`, `flat_no`, `street_name`, `pincode`, `location`, `city`, `district`, `state`, `branch_is_primary`, `branch_created_at`, `branch_created_by`, `branch_updated_at`, `branch_updated_by`, `branch_profile`, `branch_status` FROM `".ERP_BRANCHES."` WHERE `branch_id` = '".$id."'";

    return queryGet($selSql, true);
}

function getAllDataBranch($id){
    $sql = "SELECT * FROM `".ERP_BRANCHES."` as branch, `tbl_branch_admin_details` as admin ,`".ERP_COMPANIES."` as company WHERE branch.branch_id=admin.fldAdminBranchId AND branch.company_id=company.company_id AND admin.fldAdminRole=1 AND branch.branch_id =$id";
    $returnData = queryGet($sql);
    return $returnData;

}

function saveBranchSettings($POST){

    // $isValidate = validate($POST, [ 
    //     "name" => "required",
    //     "gst" => "required",
    //     "const"=>"required",
    //     "build"=>"required",
    //     "flat" => "required",
    //     "street" => "required",
    //     "locality"=>"required",
    //     "city"=>"required",

    //     "district" => "required",
    //     "state"=>"required",
    //     "pin"=>"required",
   
    // ]); 

    // if ($isValidate["status"] != "success") {
    //     $returnData['status'] = "warning";
    //     $returnData['message'] = "Invalid form inputes";
    //     $returnData['errors'] = $isValidate["errors"];
    //     return $returnData;
    // }
    $branch_name =$POST['name'];
    $gst=$POST['gst'];
    $const=$POST['const'];
    $building =$POST['build'];
    $flat =$POST['flat'];
    $street=$POST['street'];
    $locality=$POST['locality'];
    $city =$POST['city'];
    $district =$POST['district'];
    $state =$POST['state'];
    $pin =$POST['pin'];
    $id = $POST['id'];
    $gstUsername = $POST['gstUsername'];
    $eInvocieUsername = $POST['eInvocieUsername'];
    $eInvociePassword = $POST['eInvociePassword'];

    $ins = "UPDATE `".ERP_BRANCHES."` SET 
    `branch_name`='".$branch_name."',
    `branch_gstin`='".$gst."',
    `con_business`='".$const."',
    `build_no`='".$building."',
    `flat_no`='".$flat."',
    `street_name`='".$street."',
    `pincode`='".$pin."',
    `location`='".$locality."',
    `city`='".$city."',
    `branch_gstin_username`='".$gstUsername."',
    `branch_einvoice_username`='".$eInvocieUsername."',
    `branch_einvoice_password`='".$eInvociePassword."',
    `district`='".$district."',
    `state`='".$state."'
    WHERE `branch_id`='".$id."'  
    ";

    $returnData = queryUpdate($ins);
    return $returnData;

    //console($POST);
}

 


?>