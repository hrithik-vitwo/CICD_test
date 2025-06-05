<?php
//*************************************/INSERT/******************************************//
function createDataChartOfAccounts($POST = [])
{
    global $dbCon;
    $returnData = [];
    $isValidate = validate($POST, [
        "p_id" => "required",
        "p_gl_code" => "required",
        "gl_code" => "required",
        "gl_label" => "required"
    ], [
        "p_id" => "Enter name",
        "p_gl_code" => "Enter Parent GL",
        "gl_code" => "Enter valid GL",
        "gl_label" => "Enter GL Label"
    ]);

    if ($isValidate["status"] == "success") {
        if($POST["createdata"]=='add_post'){
        $customer_status = 'active';
        }else{
        $customer_status = 'draft';
        }

        $company_id = $POST["company_id"];
        $p_id = $POST["p_id"];
        $p_gl_code = $POST["p_gl_code"];
        $gl_code = $POST["gl_code"];
        $gl_label = $POST["gl_label"];
        $remark = $POST["remark"];

        //$adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]);

        $sql = "SELECT * FROM `".ERP_ACC_CHART_OF_ACCOUNTS."` WHERE `company_id`='" . $company_id . "' AND `p_id`='" . $p_id . "' AND `p_gl_code`='" . $p_gl_code . "' AND `gl_code`='" . $gl_code . "' AND `status`!='deleted'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 0) {

                $ins = "INSERT INTO `".ERP_ACC_CHART_OF_ACCOUNTS."` 
                            SET
                                `company_id`='" . $company_id . "',
                                `p_id`='" . $p_id . "',
                                `p_gl_code`='" . $p_gl_code . "',
                                `gl_code`='" . $gl_code . "',
                                `gl_label`='" . $gl_label . "',
                                `remark`='" . $remark . "',
                                `status`='" . $customer_status . "'";

                if (mysqli_query($dbCon, $ins)) {
                    $last_id = mysqli_insert_id($dbCon);
                    $returnData['status'] = "success";
                    $returnData['message'] = "Data added success";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Data added failed";
                }
          } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Admin already exist";
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
function updateDataChartOfAccounts($POST)
{
    global $dbCon;
    $returnData = [];
    $isValidate = validate($POST, [
        "adminKey" => "required",
        "adminName" => "required",
        "adminEmail" => "required|email",
        "adminPhone" => "required|min:10|max:10",
        "adminPassword" => "required|min:8",
        "adminRole" => "required",
    ], [
        "adminKey" => "Invalid admin",
        "adminName" => "Enter name",
        "adminEmail" => "Enter valid email",
        "adminPhone" => "Enter valid phone",
        "adminPassword" => "Enter password(min:8 character)",
        "adminRole" => "Select a role",
    ]);

    if ($isValidate["status"] == "success") {

        $adminKey = $POST["adminKey"];
        $adminName = $POST["adminName"];
        $adminEmail = $POST["adminEmail"];
        $adminPhone = $POST["adminPhone"];
        $adminPassword = $POST["adminPassword"];
        $adminRole = $POST["adminRole"];

        $sql = "SELECT * FROM `".ERP_ACC_CHART_OF_ACCOUNTS."` WHERE `fldAdminKey`='" . $adminKey . "'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $ins = "UPDATE `".ERP_ACC_CHART_OF_ACCOUNTS."` 
                            SET
                                `fldAdminName`='" . $adminName . "',
                                `fldAdminEmail`='" . $adminEmail . "',
                                `fldAdminPhone`='" . $adminPhone . "',
                                `fldAdminPassword`='" . $adminPassword . "',
                                `fldAdminRole`='" . $adminRole . "' WHERE `fldAdminKey`='" . $adminKey . "'";

                if (mysqli_query($dbCon, $ins)) {
                    $returnData['status'] = "success";
                    $returnData['message'] = "Admin modified success";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Admin modified failed";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Admin not exist";
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
function getAllDataChartOfAccountsgroup($company_id)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `".ERP_ACC_CHART_OF_ACCOUNTS."` WHERE company_id=$company_id AND p_id=0  AND `status`!='deleted'";
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
function getAllDataChartOfAccounts($company_id,$p_id)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `".ERP_ACC_CHART_OF_ACCOUNTS."` WHERE company_id=$company_id AND p_id=$p_id  AND `status`!='deleted'";
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
function getAllChartOfAccounts_list($company_id)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `".ERP_ACC_CHART_OF_ACCOUNTS."` WHERE company_id=$company_id AND `status`!='deleted' ORDER BY p_gl_code";
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
function getChartOfAccountsDataDetails($key = null)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `".ERP_ACC_CHART_OF_ACCOUNTS."` WHERE `status`!='deleted' AND `id`=" . $key . "";
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

function getChartOfAccountsDataDetails_byparent($pkey = null, $company_id=null)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `".ERP_ACC_CHART_OF_ACCOUNTS."` WHERE `status`!='deleted' AND `p_id`=" . $pkey . " AND `company_id`=" . $company_id . " order by `id`  DESC LIMIT 1";
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


//*************************************/UPDATE STATUS/******************************************//
function ChangeStatusChartOfAccounts($data = [], $tableKeyField = "", $tableStatusField = "status")
{
    global $dbCon;
	$tableName=ERP_ACC_CHART_OF_ACCOUNTS;
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



  function cateSubcatTreenew($parent_id, $sub_mark = '',$gl_account_length){
    global $dbCon;
    $query = $dbCon->query("SELECT * FROM erp_acc_chart_of_accounts WHERE p_id = $parent_id ORDER BY id ASC");
      if($query->num_rows > 0){
          while($row = $query->fetch_assoc()){
             echo '<li>'.$sub_mark.$row['gl_label'].'['.get_full_gl_code($gl_account_length,$row['p_gl_code'],$row['gl_code']).'] </li>';
              cateSubcatTreenew($row['id'], $sub_mark.'--',$gl_account_length);
          }
      }
  }

  function createGlTree($p_id=0){
    $queryObj = queryGet("SELECT * FROM `erp_acc_chart_of_accounts` WHERE `p_id`=0 AND `status`!='deleted'", true);
    if($queryObj["status"]== "success"){
      foreach($queryObj["data"] as $oneGlRow){
        
      }
    }
  }

  
  function length_calculater($pdata,$ac_length,$length_bkup,$lastrow_personal_glcode){
    $length_bkup = explode('-', $length_bkup);
    $select_possition = '';
    if ($pdata != NULL) {
      $pdata_length = strlen($pdata);
      $bksum = 0;
      foreach ($length_bkup as $key => $bkdta) {
        $bksum = $bksum + $bkdta;
        if ($pdata_length == $bksum) {
          if(isset($length_bkup[$key + 1])){
            $select_possition = $length_bkup[$key + 1];
          } else{
            $new_personal_glcode_status['status'] = 'warning';
            $new_personal_glcode_status['message'] = 'Not Possible to create a child G/L code';
            return $new_personal_glcode_status;
          }
        }
      }
      $length = $select_possition;
    } else {
      $length = 1;
    }
    $ength_wise_start_end=length_wise_start_end($length);
    $start = $ength_wise_start_end['start'];
    $end = $ength_wise_start_end['end'];
    
    //created Step last gl code from db by parent data
    //$lastrow_personal_glcode = 1000;
    if (!empty($lastrow_personal_glcode)) {
      $new_personal_glcode = $lastrow_personal_glcode + 1;
    } else {
      $new_personal_glcode = $start;
    }
    if ($new_personal_glcode > $end) {
      $new_personal_glcode_status['status'] = 'warning';
      $new_personal_glcode_status['message'] = 'Not Possible to create This G/L code';
      $new_personal_glcode_status['new_personal_glcode'] = $new_personal_glcode;
      $new_personal_glcode_status['parent_full_gl_code'] = $pdata;
      $new_personal_glcode_status['personal_full_gl_code'] ='';
    } else {
      $fullgl=get_full_gl_code($ac_length,$pdata,$new_personal_glcode);
      $new_personal_glcode_status['status'] = 'success';
      $new_personal_glcode_status['message'] = 'Available This G/L code';
      $new_personal_glcode_status['new_personal_glcode'] = $new_personal_glcode;
      $new_personal_glcode_status['parent_full_gl_code'] = $pdata;
      $new_personal_glcode_status['personal_full_gl_code'] = $fullgl;
    }
   // echo $new_personal_glcode_status['message'];
    //echo $new_personal_glcode_status['new_personal_glcode'];
    //echo $new_personal_glcode_status['personal_full_gl_code'];
    //echo $fullgl=get_full_gl_code($ac_length,$pdata,$new_personal_glcode);
   return $new_personal_glcode_status;
   }
  

   function get_full_gl_code($ac_length,$pdata,$new_personal_glcode){
    if($pdata!=0){
     $full_account_Number = $pdata . $new_personal_glcode;            
    }else if($pdata==''){
     $full_account_Number = $new_personal_glcode;                 
    }else{
     $full_account_Number = $new_personal_glcode;                 
    }
    $full_account_Number = ltrim($full_account_Number, "0"); 
    $full_account_Number_count = strlen($full_account_Number);
    $nnn = $ac_length - $full_account_Number_count;
    $zero_view = zeroview($nnn);
    $full_account_Number_preview = $full_account_Number . $zero_view;
    return $full_account_Number_preview;
    }
  
    function length_wise_start_end($length){
      $start = '';
      $end = '';
      if ($length == 1) {
        $start = 1;
        $end = 9;
      } else if ($length == 2) {
        $start = 10;
        $end = 99;
      } else if ($length == 3) {
        $start = 100;
        $end = 999;
      } else if ($length == 4) {
        $start = 1000;
        $end = 9999;
      } else if ($length == 5) {
        $start = 10000;
        $end = 99999;
      } else if ($length == 6) {
        $start = 100000;
        $end = 999999;
      } else if ($length == 7) {
        $start = 1000000;
        $end = 9999999;
      } else if ($length == 8) {
        $start = 10000000;
        $end = 99999999;
      } else if ($length == 9) {
        $start = 100000000;
        $end = 999999999;
      } else if ($length == 10) {
        $start = 1000000000;
        $end = 9999999999;
      }else{
        $start = 1;
        $end = 9;
        for($i = 1; $i < $length; $i++){ 
          $start .= '0';
          $end .= '9';
        }
        
      }
      $data['start']=$start;
      $data['end']=$end;
  
      return $data;
    }
   //$hhhh= length_wise_start_end(9);
   //echo '<br>'.$hhhh['start'] . '<br> ' . $hhhh['end'] ;
  
    function zeroview($num)
    {
      if ($num == 0) {
        $d = '';
      } else if ($num == 1) {
        $d = '0';
      } else if ($num == 2) {
        $d = '00';
      } else if ($num == 3) {
        $d = '000';
      } else if ($num == 4) {
        $d = '0000';
      } else if ($num == 5) {
        $d = "00000";
      } else if ($num == 6) {
        $d = '000000';
      } else if ($num == 7) {
        $d = '0000000';
      } else if ($num == 8) {
        $d = '00000000';
      } else if ($num == 9) {
        $d = '000000000';
      } else if ($num == 10) {
        $d = '0000000000';
      } else if ($num == 11) {
        $d = '00000000000';
      } else if ($num == 12) {
        $d = '000000000000';
      } else if ($num == 13) {
        $d = '0000000000000';
      } else if ($num == 14) {
        $d = '00000000000000';
      } else if ($num == 15) {
        $d = '000000000000000';
      } else if ($num == 16) {
        $d = '0000000000000000';
      } else if ($num == 17) {
        $d = '00000000000000000';
      } else if ($num == 18) {
        $d = '000000000000000000';
      } else if ($num == 19) {
        $d = '0000000000000000000';
      } else if ($num == 20) {
        $d = '00000000000000000000';
      } else if ($num == 21) {
        $d = '000000000000000000000';
      } else if ($num == 22) {
        $d = '0000000000000000000000';
      } else if ($num == 23) {
        $d = '00000000000000000000000';
      } else if ($num == 24) {
        $d = '000000000000000000000000';
      } else if ($num == 25) {
        $d = '0000000000000000000000000';
      } else if ($num == 26) {
        $d = '00000000000000000000000000';
      } else if ($num == 27) {
        $d = '000000000000000000000000000';
      } else if ($num == 28) {
        $d = '0000000000000000000000000000';
      } else if ($num == 29) {
        $d = '00000000000000000000000000000';
      } else if ($num == 30) {
        $d = '000000000000000000000000000000';
      } else if ($num == 31) {
        $d = '0000000000000000000000000000000';
      } else if ($num == 32) {
        $d = '00000000000000000000000000000000';
      } else if ($num == 33) {
        $d = '000000000000000000000000000000000';
      } else if ($num == 34) {
        $d = '0000000000000000000000000000000000';
      } else if ($num == 35) {
        $d = '00000000000000000000000000000000000';
      } else if ($num == 36) {
        $d = '000000000000000000000000000000000000';
      } else if ($num == 37) {
        $d = '0000000000000000000000000000000000000';
      } else if ($num == 38) {
        $d = '00000000000000000000000000000000000000';
      } else if ($num == 39) {
        $d = '000000000000000000000000000000000000000';
      } else if ($num == 40) {
        $d = '0000000000000000000000000000000000000000';
      }else{
        $d='';
        for($i = 0; $i < $num; $i++){ 
          $d.='0';
        }
      }
      return $d;
    }
//*************************************/END/******************************************//