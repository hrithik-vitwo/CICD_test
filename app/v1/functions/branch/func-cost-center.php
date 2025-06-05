<?php
//*************************************/INSERT/******************************************//
function createDataCostCenter($POST = [],$admin_id)
{
    global $dbCon;
    global $created_by;
    $returnData = [];
    $isValidate = validate($POST, [
        "CostCenter_code" => "required",
        "CostCenter_desc" => "required"
    ], [
        "CostCenter_code" => "Enter Credit Terms",
        "CostCenter_desc" => "Enter Credit Terms",
    ]);

    if ($isValidate["status"] == "success") {
        $CostCenter_status = 'active';        

        $CostCenter_code = $POST["CostCenter_code"];
        $CostCenter_created_by = $admin_id;
        $CostCenter_desc=$POST["CostCenter_desc"];
        $company_id=$POST["fldAdminCompanyId"];
        $branch_id=$POST["fldAdminBranchId"]??0;
        $location_id=$POST["location"];
        if($location_id != 0 || $location_id != ""){
            $branch= queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `company_id`=$company_id AND `othersLocation_id`=$location_id ");
            $branch_id = $branch['data']['branch_id'];
        }
        else{
            $branch_id = 0; 
        }
      
        // $lhr = $POST['lhr'];
        // $mhr = $POST['mhr'];
        $gl = $POST['gl'];


        //$adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]);

       $sql = "SELECT * FROM `".ERP_COST_CENTER."` WHERE `CostCenter_code`='" .$CostCenter_code. "' AND company_id=".$company_id." AND `CostCenter_status`!='deleted'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 0) {

                  $ins = "INSERT INTO `".ERP_COST_CENTER."` 
                            SET
                                `company_id`='" . $company_id . "',
                                `branch_id`='" . $branch_id . "',
                                `location_id`='" . $location_id . "',
                                `CostCenter_code`='" . $CostCenter_code . "',
                                `CostCenter_desc`='" . $CostCenter_desc . "',
                              
                                `CostCenter_updated_by`='" . $created_by . "',
                                `CostCenter_created_by`= '".$created_by."',
                                `CostCenter_status`='" . $CostCenter_status . "'";
                              

                if (mysqli_query($dbCon, $ins)) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Information added success";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Information added failed";
                }
            } else {
                $sqqql="SELECT CostCenter_code FROM `".ERP_COST_CENTER."` WHERE `company_id`='" .$company_id. "' AND `CostCenter_status`!='deleted' ORDER BY CostCenter_id DESC LIMIT 1";
                $CostCenter_code = queryGet($sqqql );
                if(isset($CostCenter_code['data'])){
                $CostCenter_code=$CostCenter_code['data']['CostCenter_code'];
                }else{
                $CostCenter_code='';
                }
                $ins = "INSERT INTO `".ERP_COST_CENTER."` 
                    SET
                        `company_id`='" . $company_id . "',
                        `branch_id`='" . $branch_id . "',
                        `location_id`='" . $location_id . "',
                        `CostCenter_code`='" . getCostCenterSerialNumber($CostCenter_code) . "',
                        `CostCenter_desc`='" . $CostCenter_desc . "',
                        `CostCenter_created_by`='" . $created_by . "',
                        `CostCenter_updated_by`='" . $created_by . "',
                        `CostCenter_status`='" . $CostCenter_status . "'";

                if (mysqli_query($dbCon, $ins)) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Information added success";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Information added failed";
                }
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
function updateDataCostCenter($POST)
{
    global $dbCon;
    global $created_by;
    $returnData = [];
    $isValidate = validate($POST, [
        "CostCenter_desc" => "required",
    ], [
        "CostCenter_desc" => "Required",
    ]);

    if ($isValidate["status"] == "success") {
        if($POST["editdata"]=='add_post'){
        $CostCenter_status = 'active';
        }else{
        $CostCenter_status = 'draft';

        }
        $CostCenter_desc = $POST["CostCenter_desc"];
        $CostCenter_id = $POST["CostCenter_id"];
      
        $location = $POST['location'];
        $gl = $POST['gl'];



         $sql = "SELECT * FROM `".ERP_COST_CENTER."` WHERE `CostCenter_id`='" . $CostCenter_id . "'";
       // exit();
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $ins = "UPDATE `".ERP_COST_CENTER."` 
                            SET
                                `CostCenter_desc`='" . $CostCenter_desc . "',
                                `CostCenter_updated_by`='".$created_by."',
                                `location_id` = '".$location."'
                            WHERE `CostCenter_id`='" . $CostCenter_id . "'";

                          //  exit();

                if (mysqli_query($dbCon, $ins)) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Information modified success";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Information modified failed";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Information not exist";
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

//*************************************/SELECT ALL/******************************************//
function getAllDataCostCenter($fldAdminCompanyId)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `".ERP_COST_CENTER."` WHERE `status`!='deleted' AND fldAdminCompanyId='".$fldAdminCompanyId."'";
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
    $sql = "SELECT * FROM `".ERP_COST_CENTER."` WHERE `status`!='deleted' AND `fldRoleKey`=" . $key . "";
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
//*************************************************Visit CostCenter*********************************************** */

function VisitCostCenter($POST){
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
function ChangeStatusCostCenter($data = [], $tableKeyField = "", $tableStatusField = "status")
{
    global $dbCon;
	$tableName=ERP_COST_CENTER;
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

function createDataWorkInternal($POST){
    //console($POST);
    global $dbCon;
    global $created_by;
    global $company_id;
    $returnData = [];
    $isValidate = validate($POST, [
        
        "desc" => "required",
        "type"=>"required",
        "parent"=>"required"
      
    ]);
    $type = $POST['type'];
    $desc = $POST['desc'];
    $parent = $POST['parent'];
    $parent_sql = queryGet("SELECT * FROM `erp_cost_center` WHERE `CostCenter_id`=$parent");
    $location_id = $parent_sql['data']['location_id'];
    $branch_id = $parent_sql['data']['branch_id'];
    $lhr = $parent_sql['data']['labour_hour_rate'];
    $mhr = $parent_sql['data']['machine_hour_rate'];
    $gl = $parent_sql['data']['gl_code'];
    $status = 'active';     

    if ($isValidate["status"] == "success") {
    

        if($type == "work center"){
            $code = "W001";
            

        }else if($type == "internal order"){
            $code = "I001";

        }

      $ins = "INSERT INTO `".ERP_COST_CENTER."` 
        SET
            `company_id`='" . $company_id . "',
            `branch_id`='" . $branch_id . "',
            `location_id`='" . $location_id . "',
            `CostCenter_code`='" . $code . "',
            `CostCenter_desc`='" . $desc . "',
            `parent_id`= $parent,
            `type`='".$type."',
            `machine_hour_rate`='" . $mhr . "',
            `labour_hour_rate`='" . $lhr . "',
            `gl_code`='" . $gl . "',
            `CostCenter_updated_by`='" . $created_by . "',
            `CostCenter_created_by`= '".$created_by."',
            `CostCenter_status`='" . $status . "'";
           // exit();
            if (mysqli_query($dbCon, $ins)) {
                $returnData['status'] = "success";
                $returnData['message'] = "Information added success";
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Information added failed";
            }
            
    }
    else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;

}

function ChangeStatusfunctionalities($POST){

    

}

?>

//*************************************/END/******************************************//