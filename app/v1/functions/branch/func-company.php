<?php
//*************************************/INSERT/******************************************//
function createDataCompany($POST)
{
    global $dbCon;
    $returnData = [];
    console($POST);
    $isValidate = validate($POST, [
        "adminName" => "required",
        "adminEmail" => "required|email",
        "adminPhone" => "required|min:10|max:15",
        "adminPassword" => "required|min:4"
    ], [
        "adminName" => "Enter name",
        "adminEmail" => "Enter valid email",
        "adminPhone" => "Enter valid phone",
        "adminPassword" => "Enter password(min:4 character)" 
    ]);

    if ($isValidate["status"] == "success") {
        $admin = array();
        $admin["adminName"] = $POST["adminName"];
        $admin["adminEmail"] = $POST["adminEmail"]; 
        $admin["adminPhone"] = $POST["adminPhone"];
        $admin["adminPassword"] = $POST["adminPassword"];
        $admin["tablename"] = 'tbl_company_admin_details';
        $admin["adminPassword"] = $POST["adminPassword"];

        if ($POST["createdata"] == 'add_post') {
            $company_status = 'active';
        } else {
            $company_status = 'draft';
        }

        $logo = "";
        $logo = uploadFile($POST["logo"], "../public/storage/logo/",["jpg","jpeg","png"]);
        if($logo['status']=='success'){
         $logo=$logo['data'];
        }else{
         $logo='';
        }
   
     
        $favicon = "";
        $favicon = uploadFile($POST["favicon"], "../public/storage/logo/",["jpg","jpeg","png","ico"]);
        if($favicon['status']=='success'){
         $favicon=$favicon['data'];
        }else{
         $favicon='';
        }

        $company_name = $POST["company_name"];
        $company_gstin = $POST["company_gstin"];
        $company_cin = $POST["company_cin"];
        $company_llpin = $POST["company_llpin"];
        $company_tan = $POST["company_tan"];
        $company_const_of_business = $POST["company_const_of_business"];
        $company_gstin_status = $POST["company_gstin_status"];
        $company_currency = $POST["company_currency"];
        $company_language = $POST["company_language"];
        $company_logo = $logo;
        $company_favicon = $favicon;

        $city=$POST['city'];
        $state=$POST['state'];
        $pin=$POST['pin'];
        $street=$POST['street'];
        $flat=$POST['flat'];
        $building=$POST['building'];
        $location=$POST['location'];
        $district =$POST['district'];

        $company_code = getCompanySerialNumber(ERP_COMPANIES, 'company_code');

        //$adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]);

        $sql = "SELECT * FROM `" . $admin["tablename"] . "` WHERE `fldAdminEmail`='" . $admin["adminEmail"] . "' AND `fldAdminStatus`!='deleted'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 0) {

                $ins = "INSERT INTO `" . ERP_COMPANIES . "` 
                            SET
                                `company_name`='" . $company_name . "',
                                `company_code`='" . $company_code['data'] . "',
                                `company_gstin`='" . $company_gstin . "',
                                `company_cin`='" . $company_cin . "',
                                `company_llpin`='" . $company_llpin . "',
                                `company_tan`='" . $company_tan . "',
                                `company_const_of_business`='" . $company_const_of_business . "',
                                `company_gstin_status`='" . $company_gstin_status . "',
                                `company_currency`='" . $company_currency . "',
                                `company_language`='" . $company_language . "',
                                `company_status`='" . $company_status . "',
                                `company_logo`='" . $company_logo . "',
                                
                                `company_favicon`='" . $company_favicon . "',
                                `company_building`='" . $building . "',
                                `company_flat_no`='" . $flat . "',
                                `company_state`='" . $state . "',
                                `company_district`='" . $district . "',
                                `company_location`='" . $location . "',
                                `company_pin`='" . $pin . "',
                                `company_street`='" . $street . "',
                                `company_city`='" . $city . "'

                                
                                ";

                if (mysqli_query($dbCon, $ins)) {
                    $last_id = mysqli_insert_id($dbCon);
                    $admin["fldAdminCompanyId"] = $last_id;
                    addNewAdministratorUserGlobal($admin);
                    $returnData['status'] = "success";
                    $returnData['message'] = "Admin added success";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Admin added failed";
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
function updateDataCompany($POST)
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

        $sql = "SELECT * FROM `" . ERP_COMPANIES . "` WHERE `fldAdminKey`='" . $adminKey . "'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $ins = "UPDATE `" . ERP_COMPANIES . "` 
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
function getAllDataCompany($company_id)
{
    global $dbCon;
    $returnData = [];
  $sql = "SELECT * FROM `" . ERP_COMPANIES . "` as comp, `" . ERP_LANGUAGE . "` as lang , `" . ERP_CURRENCY_TYPE . "` as curr, `tbl_company_admin_details` as det WHERE lang.language_id=comp.company_language AND comp.company_status!='deleted' AND comp.company_id=" . $company_id . " AND curr.currency_id=comp.company_currency AND det.fldAdminCompanyId=comp.company_id AND det.fldAdminRole=1";
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
function getCompanyDataDetails($key = null)
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `" . ERP_COMPANIES . "` WHERE `company_status`!='deleted' AND `company_id`=" . $key . "";
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

//get all language
function getAllLanguages()
{
    global $dbCon;
    $returnData = [];

    $sql = "SELECT * FROM `" . ERP_LANGUAGE . "` ";
    $returnData = queryGet($sql, true);
    return $returnData;
}
//end lang


//get all currency
function getAllCurrency()
{
    global $dbCon;
    $returnData = [];

    $sql = "SELECT * FROM `" . ERP_CURRENCY_TYPE . "` ";
    $returnData = queryGet($sql, true);
    return $returnData;
}
//end currency


//*************************************/UPDATE STATUS/******************************************//
function ChangeStatusCompany($data = [], $tableKeyField = "", $tableStatusField = "status")
{
    global $dbCon;
    $tableName = ERP_COMPANIES;
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


function saveCompanyDetails($POST){
 //console($POST['logo']);
    // $isValidate = validate($POST, [
    //     "name" => "required",
    //     "email" => "required",
    //     "phone" => "required",
    //     "code" => "required",
    //     "gst" => "required",
    //     "cin" => "required",
    //     "llpin" => "required",
    //     "tan" => "required",
    //     "const" => "required",
    //     "gst_status" => "required",
    //     "gl_length" => "required",
    //     "gl_brkup" => "required",
    //     "lang" => "required",
    //     "currency" => "required",
    //     "address" => "required",
    //     "footer" => "required",
  


    // ]);

    // if ($isValidate["status"] != "success") {
    //     $returnData['status'] = "warning";
    //     $returnData['message'] = "Invalid form inputes";
    //     $returnData['errors'] = $isValidate["errors"];
    //     return $returnData;
    // }

    $name = $POST["name"];
    $email = $POST["email"];
    $phone = $POST["phone"];
    $code = $POST["code"];
    $gst = $POST["gst"];
    $cin = $POST["cin"];
    $llpin = $POST["llpin"];
    $tan = $POST["tan"];
    $gst_status = $POST["gst_status"];
    $gl_length = $POST["gl_length"];
    $gl_brkup = $POST["gl_brkup"];
    $lang = $POST["lang"];
    $currency = $POST["currency"];
    $address = $POST["address"];
    $footer = $POST["footer"];
  //  $logo=$POST["logo"];
   // $favicon= $POST["favicon"];
    $const= $POST["const"];
    $company_id = $POST['company_id'];
   $logo = "";
   $logo = uploadFile($POST["logo"], "../public/storage/logo/",["jpg","jpeg","png"]);
   if($logo['status']=='success'){
    $logo=$logo['data'];
   }else{
    $logo='';
   }
echo $logo;

   $favicon = "";
   $favicon = uploadFile($POST["favicon"], "../public/storage/logo/",["jpg","jpeg","png","ico"]);
   if($favicon['status']=='success'){
    $favicon=$favicon['data'];
   }else{
    $favicon='';
   }
echo $favicon;

   $ins = "UPDATE`" . ERP_COMPANIES . "` 
    SET
    `company_code`='" . $code . "',
    `company_name`='" .$name. "',
    `company_gstin`='" . $gst . "',
    `company_cin`='" .  $cin  . "',
    `company_llpin`='" . $llpin . "',
    `company_tan`='" . $tan . "',
    `company_const_of_business`='" . $const . "',
    `company_gstin_status`= '".$gst_status."', 
    `company_currency`='" . $currency . "',
    `company_language`='" .  $lang  . "',
    `gl_account_length`='" . $gl_length . "',
    `gl_length_bkup`='" . $gl_brkup . "',
   `company_logo` = '".$logo."',
    `company_favicon`='".$favicon."',
    `company_address`='".$address."',
    `company_footer`='".$footer."'
    WHERE `company_id`='".$company_id."'";
    
    $insertQuery = queryInsert($ins);
    $returnData = $insertQuery;

    if($returnData["status"] == "success"){


        $insert = "UPDATE `tbl_company_admin_details`  
                    SET
                        `fldAdminPhone`='" . $phone . "',
                        `fldAdminEmail`='".$email."'
                    WHERE `fldAdminCompanyId`='".$company_id."' AND `fldAdminRole`=1";

                    $update = queryInsert($insert);
                    $returnData = $update;
                    return $returnData;


    }else{
       return $returnData;
    }
   
}