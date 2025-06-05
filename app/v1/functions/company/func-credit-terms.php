<?php
//*************************************/INSERT/******************************************//
function createDataCreditTerms($POST = [],$admin_id)
{
    global $dbCon;
    $returnData = [];
    $isValidate = validate($POST, [
        "credit_terms_name" => "required"
    ], [
        "adminName" => "Enter Credit Terms"
    ]);

    if ($isValidate["status"] == "success") {
        if($POST["createdata"]=='add_post'){
        $credit_terms_status = 'active';
        }else{
        $credit_terms_status = 'draft';

        }

        $credit_terms_name = $POST["credit_terms_name"];
        $credit_terms_created_by = $admin_id;
        $credit_terms_desc=$POST["credit_terms_desc"];
        $company_id=$POST["fldAdminCompanyId"];

        //$adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]);

        $sql = "SELECT * FROM `".ERP_CREDIT_TERMS."` WHERE `credit_terms_name`='" .$credit_terms_name. "' AND company_id=$company_id AND `credit_terms_status`!='deleted'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 0) {

                $ins = "INSERT INTO `".ERP_CREDIT_TERMS."` 
                            SET
                                `company_id`='" . $company_id . "',
                                `credit_terms_name`='" . $credit_terms_name . "',
                                `credit_terms_desc`='" . $credit_terms_desc . "',
                                `credit_terms_created_by`='" . $credit_terms_created_by . "',
                                `credit_terms_updated_by`='" . $credit_terms_created_by . "',
                                `credit_terms_status`='" . $credit_terms_status . "'";

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
function updateDataCreditTerms($POST)
{
    global $dbCon;
    global $created_by;
    $returnData = [];
    $isValidate = validate($POST, [
        "name" => "required",
        "desc" => "required"
    ], [
        "name" => "Enter Credit Terms",
        "desc" => "Enter Description"
        
      
    ]);

    if ($isValidate["status"] == "success") {

        $name = $POST["name"];
        $desc = $POST["desc"];
        $id = $POST["id"];
       
      

     
                $update = "UPDATE `".ERP_CREDIT_TERMS."` 
                            SET
                                `credit_terms_name`='" . $name . "',
                                `credit_terms_desc`='" . $desc . "',
                                `credit_terms_updated_by`='" . $created_by . "'
                                WHERE `credit_terms_id`='" . $id . "'";
                              //  exit();

                                $returnData = queryUpdate($update);

    }
    if($returnData['status'] == 'success'){
        $returnData['status'] = 'success';
        $returnData['message'] = 'updated successfully';

    }
    else{
        $returnData['status'] = 'warning';
        $returnData['message'] = 'something went wrong';
    }
    return $returnData;
}

//*************************************/SELECT ALL/******************************************//
function getAllDataCreditTerms($fldAdminCompanyId)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `".ERP_CREDIT_TERMS."` WHERE `status`!='deleted' AND fldAdminCompanyId='".$fldAdminCompanyId."'";
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
    $sql = "SELECT * FROM `".ERP_CREDIT_TERMS."` WHERE `status`!='deleted' AND `fldRoleKey`=" . $key . "";
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
//*************************************************Visit CreditTerms*********************************************** */

function VisitCreditTerms($POST){
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
function ChangeStatusCreditTerms($data = [], $tableKeyField = "", $tableStatusField = "status")
{
    global $dbCon;
	$tableName=ERP_CREDIT_TERMS;
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