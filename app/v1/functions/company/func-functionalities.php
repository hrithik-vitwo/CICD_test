<?php
//*************************************/INSERT/******************************************//
function createDatafunctionalities($POST = [],$admin_id)
{
    global $dbCon;
    global $company_id;
    global $created_by;
    
    $returnData = [];
    $isValidate = validate($POST, [
        "functionalities_name" => "required"
    ], [
        "functionalities_name" => "Enter Credit Terms"
    ]);

    if ($isValidate["status"] == "success") {
        $functionalities_status = 'active';

        $functionalities_name = $POST["functionalities_name"];
        $functionalities_created_by = $created_by;
        $functionalities_desc=$POST["functionalities_desc"];

        //$adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]);

        $sql = "SELECT * FROM `".ERP_COMPANY_FUNCTIONALITIES."` WHERE `functionalities_name`='" .$functionalities_name. "' AND company_id=$company_id AND `functionalities_status`!='deleted'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 0) {

                $ins = "INSERT INTO `".ERP_COMPANY_FUNCTIONALITIES."` 
                            SET
                                `company_id`='" . $company_id . "',
                                `functionalities_name`='" . $functionalities_name . "',
                                `functionalities_desc`='" . $functionalities_desc . "',
                                `functionalities_created_by`='" . $functionalities_created_by . "',
                                `functionalities_updated_by`='" . $functionalities_created_by . "',
                                `functionalities_status`='" . $functionalities_status . "'";

                if (mysqli_query($dbCon, $ins)) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Information added success";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Information added failed";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Information already exist";
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
}

//*************************************/UPDATE/******************************************//
function updateDatafunctionalities($POST)
{
    global $dbCon;
    global $created_by;
    $returnData = [];
    // $isValidate = validate($POST, [
    //     "adminKey" => "required",
    //     "adminName" => "required",
    //     "adminEmail" => "required|email",
    //     "adminPhone" => "required|min:10|max:10",
    //     "adminPassword" => "required|min:8",
    //     "adminRole" => "required",
    // ], [
    //     "adminKey" => "Invalid admin",
    //     "adminName" => "Enter name",
    //     "adminEmail" => "Enter valid email",
    //     "adminPhone" => "Enter valid phone",
    //     "adminPassword" => "Enter password(min:8 character)",
    //     "adminRole" => "Select a role",
    // ]);

    // if ($isValidate["status"] == "success") {

        $functionalities_name = $POST["functionalities_name"];
        $functionalities_desc = $POST["functionalities_desc"];
        $id = $POST['id'];

        $update = queryUpdate("UPDATE `erp_company_functionalities` SET 
                            `functionalities_name`='$functionalities_name',
                            `functionalities_desc`='$functionalities_desc',
                            `functionalities_updated_by`='$created_by' WHERE `functionalities_id`=$id ");
                            if($update['status'] == 'success'){
                                $returnData['status'] = "success";
                                $returnData['message'] = "updated successfully";
                            } 
                            else{
                                $returnData['status'] = "warning";
                                $returnData['message'] = "something went wrong";   
                            }
        

        
    return $returnData;
}

//*************************************/SELECT ALL/******************************************//
function getAllDatafunctionalities($fldAdminCompanyId)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `".ERP_COMPANY_FUNCTIONALITIES."` WHERE `status`!='deleted' AND fldAdminCompanyId='".$fldAdminCompanyId."'";
    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found";
            $returnData['data'] = [];
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = [];
    }
    return $returnData;
}

//*************************************/SELECT SINGLE/******************************************//
function getDataDetails($key = null)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `".ERP_COMPANY_FUNCTIONALITIES."` WHERE `status`!='deleted' AND `fldRoleKey`=" . $key . "";
    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = mysqli_fetch_assoc($res);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found";
            $returnData['data'] = [];
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = [];
    }
    return $returnData;
}
//*************************************************Visit functionalities*********************************************** */

function Visitfunctionalities($POST){
    global $dbCon;
    $returnData=[];
    $sql="SELECT * FROM `tbl_branch_admin_details` WHERE `fldAdminCompanyId`='".$POST["fldAdminCompanyId"]."' AND `fldAdminBranchId`='".$POST["fldAdminBranchId"]."' AND `fldAdminStatus`='active' ORDER BY fldAdminKey ASC limit 1";
    if($result=mysqli_query($dbCon,$sql)){
        if(mysqli_num_rows($result)>0){
            $row=mysqli_fetch_assoc($result);
            $_SESSION["logedBranchAdminInfo"]["adminId"]=$row["fldAdminKey"];
            $_SESSION["logedBranchAdminInfo"]["adminName"]=$row["fldAdminName"];
            $_SESSION["logedBranchAdminInfo"]["adminEmail"]=$row["fldAdminEmail"];
            $_SESSION["logedBranchAdminInfo"]["adminPhone"]=$row["fldAdminPhone"];
            $_SESSION["logedBranchAdminInfo"]["adminRole"]=$row["fldAdminRole"];
            $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"]=$row["fldAdminCompanyId"];
            $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]=$row["fldAdminBranchId"];
            $_SESSION["logedBranchAdminInfo"]["adminType"]='branch';
            $returnData["status"]="success";
            $returnData["message"]="Login success";
            
        }else{
            $returnData["status"]="warning";
            $returnData["message"]="Invalid Credentials, Try again...!";
        }
    }else{
        $returnData["status"]="warning";
        $returnData["message"]="Something went wrong, Try again...!";
    }
    return $returnData;
}

//*************************************/UPDATE STATUS/******************************************//
function ChangeStatusfunctionalities($data = [], $tableKeyField = "", $tableStatusField = "status")
{
    global $dbCon;
	$tableName=ERP_COMPANY_FUNCTIONALITIES;
    $returnData["status"] = null;
    $returnData["message"] = null;
    if (!empty($data)) {
        $id = isset($data["id"]) ? $data["id"] : 0;
        $prevSql = "SELECT * FROM `" . $tableName . "` WHERE `" . $tableKeyField . "`='" . $id . "'";
        $prevExeQuery = mysqli_query($dbCon, $prevSql);
        $prevNumRecords = mysqli_num_rows($prevExeQuery);
        if ($prevNumRecords > 0) {
            $prevData = mysqli_fetch_assoc($prevExeQuery);
            $newStatus = "deleted";
            if ($data["changeStatus"] == "active_inactive") {
                $newStatus = ($prevData[$tableStatusField] == "active") ? "inactive" : "active";
            }
            $changeStatusSql = "UPDATE `" . $tableName . "` SET `" . $tableStatusField . "`='" . $newStatus . "' WHERE `" . $tableKeyField . "`=" . $id;
            if (mysqli_query($dbCon, $changeStatusSql)) {
                $returnData["status"] = "success";
                $returnData["message"] = "Status has been changed to " . strtoupper($newStatus);
            } else {
                $returnData["status"] = "error";
                $returnData["message"] = "Something went wrong, Try again...!";
            }
            $returnData["changeStatusSql"] = $changeStatusSql;
        } else {
            $returnData["status"] = "warning";
            $returnData["message"] = "Something went wrong, Try again...!";
        }
    } else {
        $returnData["status"] = "warning";
        $returnData["message"] = "Please provide all valid data...!";
    }
    return $returnData;
}

//*************************************/END/******************************************//


function create_work_center($POST){
    global $dbCon;
    global $created_by;
    global $company_id;
    $returnData = [];
    $isValidate = validate($POST, [
        
        "work_center_name" => "required",
        "work_center_desc"=>"required",
        "work_center_code"=>"required",
        "work_center_lhr" => "required",
        "work_center_mhr" => "required"
      
      
    ]);
    // console($POST);
    $name = $POST['work_center_name'];
    $code = $POST['work_center_code'];
    $desc = $POST['work_center_desc'];
    $parent =  empty($_POST['parent']) ? 0 : $_POST['parent'];
 
    $lhr = $POST['work_center_lhr'];
    $mhr = $POST['work_center_mhr'];
  
    $status = 'active';     
    if ($isValidate["status"] == "success") {
    

      $ins = queryInsert("INSERT INTO `erp_work_center` 
        SET
            `company_id`='" . $company_id . "',
            `work_center_code`='" . $code . "',
            `work_center_description`='" . $desc . "',
            `work_center_name` = '".$name."',
            `cost_center_id`='". $parent."',
            `updated_by`='" . $created_by . "',
            `created_by`= '".$created_by."',
            `wc_lhr` = '".$lhr."',
            `wc_mhr` = '".$mhr."',
        
            `status`='" . $status . "'");
           // exit();

        //    console($ins);
        //    exit();
            if ($ins['status'] == "success") {
                $returnData['status'] = "success";
                $returnData['message'] = "Work Center added successfully";
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "something went wrong";
            }
            
    }
    else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;


}



function edit_work_center($POST){
    global $dbCon;
    global $created_by;
    global $company_id;
    $returnData = [];
    $isValidate = validate($POST, [
        
        "work_center_name" => "required",
        "work_center_desc"=>"required",
        "work_center_code"=>"required",
        "work_center_lhr" => "required",
        "work_center_mhr" => "required"
      
    ]);
    $name = $POST['work_center_name'];
    $code = $POST['work_center_code'];
    $desc = $POST['work_center_desc'];
    $parent =  empty($_POST['parent']) ? 0 : $_POST['parent'];
    $lhr = $POST['work_center_lhr'];
    $mhr = $POST['work_center_mhr'];
    $wc_id = $POST['id'];
 
  
    //$status = 'active';     
    if ($isValidate["status"] == "success") {
    

      $ins = queryUpdate("UPDATE `erp_work_center` 
        SET
            `company_id`='" . $company_id . "',
            `work_center_code`='" . $code . "',
            `work_center_description`='" . $desc . "',
            `work_center_name` = '".$name."',
            `cost_center_id`= $parent,
            `wc_lhr` = '".$lhr."',
            `wc_mhr` = '".$mhr."',
            `updated_by`='" . $created_by . "' WHERE `work_center_id` = $wc_id");
           // exit();

        //    console($ins);
        //    exit();
            if ($ins['status'] == "success") {
                $returnData['status'] = "success";
                $returnData['message'] = "Work Center Updated successfully";
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "something went wrong";
            }
            
    }
    else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;


}








function create_table($POST){
    global $dbCon;
    global $created_by;
    global $company_id;
    $returnData = [];
    $isValidate = validate($POST, [
        
        "table_name" => "required",
        "table_desc"=>"required",
        "table_code"=>"required",
        "location" => "required"
      
    ]);
    $name = $POST['table_name'];
    $code = $POST['table_code'];
    $desc = $POST['table_desc'];
    $location = $POST['location'];
    
  
  
    $status = 'active';     
    if ($isValidate["status"] == "success") {
    

      $ins = queryInsert("INSERT INTO `erp_table_master` 
        SET
            `company_id`='" . $company_id . "',
            `table_code`='" . $code . "',
            `table_description`='" . $desc . "',
            `table_name` = '".$name."',
            `location_id` = $location,
            `updated_by`='" . $created_by . "',
            `created_by`= '".$created_by."',
            `status`='" . $status . "'");
           // exit();

        //    console($ins);
        //    exit();
            if ($ins['status'] == "success") {
                $returnData['status'] = "success";
                $returnData['message'] = "table added successfully";
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "something went wrong";
            }
            
    }
    else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;


}



function edit_table($POST){
    global $dbCon;
    global $created_by;
    global $company_id;
    $returnData = [];
    $isValidate = validate($POST, [
        
        "table_name" => "required",
        "table_desc"=>"required",
        "table_code"=>"required",
        "location" => "required"
      
    ]);
    $name = $POST['table_name'];
    $code = $POST['table_code'];
    $desc = $POST['table_desc'];
    $table_id = $POST['id'];
    $location = $POST['location'];
    //$status = 'active';     
    if ($isValidate["status"] == "success") {
    

      $ins = queryUpdate("UPDATE `erp_table_master` 
        SET
            `company_id`='" . $company_id . "',
            `table_code`='" . $code . "',
            `table_description`='" . $desc . "',
            `table_name` = '".$name."',
            `location_id` = $location,
            `updated_by`='" . $created_by . "' WHERE `table_id` = $table_id");
           // exit();

        //    console($ins);
        //    exit();
            if ($ins['status'] == "success") {
                $returnData['status'] = "success";
                $returnData['message'] = "Table Updated successfully";
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "something went wrong";
            }
            
    }
    else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;


}


function map_table($POST){

    global $dbCon;
    global $created_by;
    global $company_id;
    $returnData = [];
   // console($POST);

    $isValidate = validate($POST, [
        
        "wc" => "required",
        "table_id"=>"required",
        "kam"=>"required"
      
    ]);
    // console($isValidate);
    // exit();
    $wc = $POST['wc'];
    $table = $POST['table_id'];
    $kam = $POST['kam'];
  
  
    //$status = 'active';     
    if ($isValidate["status"] == "success") {
    

      $ins = queryInsert("INSERT INTO `erp_table_wc_mapping` 
        SET
            `company_id`='" . $company_id . "',
            `wc_id`='" . $wc . "',
            `table_id`='" . $table . "',
            `kam_id` = '".$kam."',
            `updated_by`='" . $created_by . "',
            `created_by`= '".$created_by."'");
            // exit();

        //    console($ins);
        //    exit();
            if ($ins['status'] == "success") {
                $returnData['status'] = "success";
                $returnData['message'] = "table mapped successfully";
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "something went wrong";
            }
            
    }
    else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;





}
?>