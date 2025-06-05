<?php
//*************************************/INSERT/******************************************//

function createVendorOtherBusinessAddr($vendorId, $ADDRESSES = [])
{
    global $dbCon;
    global $created_by;
    $returnData = [];

    $noOfAddresses = count($ADDRESSES);
    $noOfSuccessAdded = 0;
    // console($ADDRESSES);

    foreach ($ADDRESSES as $oneAddress) {

        // console($oneAddress["vendor_business_legal_name"]);
        //console($key);
        $ins = "INSERT INTO `" . ERP_VENDOR_BUSINESS_PLACES . "`
                         SET 
                            `vendor_id`='$vendorId',
                            `vendor_business_primary_flag`='0',
                            `vendor_business_flat_no`='" . $oneAddress['vendor_business_flat_no'] . "',
                            `vendor_business_pin_code`='" . $oneAddress['vendor_business_pin_code'] . "',
                            `vendor_business_district`='" . $oneAddress['vendor_business_district'] . "',
                            `vendor_business_location`='" . $oneAddress['vendor_business_location'] . "',
                            `vendor_business_building_no`='" . $oneAddress['vendor_business_building_no'] . "',
                            `vendor_business_street_name`='" . $oneAddress['vendor_business_street_name'] . "',
                            `vendor_business_city`='" . $oneAddress['vendor_business_city'] . "',
                            `vendor_business_state`='" . $oneAddress['vendor_business_state'] . "',
                            `vendor_business_created_by`='$created_by',
                            `vendor_business_updated_by`='$created_by'";

        //   console($ins);
        mysqli_query($dbCon, $ins);

        //$noOfSuccessAdded++;
    }
}


function createDataVendor($POST = [])
{
    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];
    $isValidate = validate($POST, [
        "vendor_authorised_person_name" => "required",
        "vendor_authorised_person_email" => "required|email",
        "vendor_authorised_person_phone" => "required|min:10|max:15",
        "adminPassword" => "required|min:4"
    ], [
        "vendor_authorised_person_name" => "Enter name",
        "vendor_authorised_person_email" => "Enter valid email",
        "vendor_authorised_person_phone" => "Enter valid phone",
        "adminPassword" => "Enter password(min:4 character)"
    ]);

    if ($isValidate["status"] == "success") {

        $accMapp = getAllfetchAccountingMappingTbl($company_id);
        if ($accMapp["status"] == "success") {
            $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['vendor_gl']);
            $parentGlId= $paccdetails['data']['id'];
            $erp_v_id = $POST["erp_v_id"];
            $admin = array();
            $admin["adminName"] = $POST["vendor_authorised_person_name"];
            $admin["adminEmail"] = $POST["vendor_authorised_person_email"];
            $admin["adminPhone"] = $POST["vendor_authorised_person_phone"];
            $admin["adminPassword"] = $POST["adminPassword"];
            $admin["tablename"] = 'tbl_vendor_admin_details';
            $admin["adminPassword"] = $POST["adminPassword"];
            $admin["fldAdminCompanyId"] = $POST["company_id"];

            if ($POST["createdata"] == 'add_post') {
                $vendor_status = 'active';
            } else {
                $vendor_status = 'draft';
            }

            $lastlQuery = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id` = '" . $POST["company_id"] . "'  ORDER BY `vendor_id` DESC LIMIT 1";
            $resultLast = queryGet($lastlQuery);
            $rowLast = $resultLast["data"];
            $lastsl = $rowLast['vendor_code'];

            /*$company_id = $POST["company_id"];
            $company_branch_id = $POST["company_branch_id"];
            $company_location_id = $POST["company_location_id"];*/
            $vendor_code = getVendorSerialNumber($lastsl);
            $vendor_pan = $POST["vendor_pan"];
            $vendor_gstin = $POST["vendor_gstin"];
            $trade_name = $POST["trade_name"];
            $constitution_of_business = $POST["con_business"];

            $vendor_authorised_person_name = $POST["vendor_authorised_person_name"];
            $vendor_authorised_person_designation = $POST["vendor_authorised_person_designation"];
            $vendor_authorised_person_phone = $POST["vendor_authorised_person_phone"];
            $vendor_authorised_alt_phone = $POST["vendor_authorised_alt_phone"];
            $vendor_authorised_person_email = $POST["vendor_authorised_person_email"];
            $vendor_authorised_alt_email = $POST["vendor_authorised_alt_email"];

            // other address
            $state = $POST["state"];
            $city = $POST["city"];
            $district = $POST["district"];
            $location = $POST["location"];
            $build_no = $POST["build_no"];
            $flat_no = $POST["flat_no"];
            $street_name = $POST["street_name"];
            $pincode = $POST["pincode"];

            // accounting
            $opening_balance = $POST["opening_balance"];
            $currency = $POST["currency"];
            $credit_period = $POST["credit_period"];
            $vendor_bank_cancelled_cheque = $POST["vendor_bank_cancelled_cheque"];
            $vendor_bank_ifsc = $POST["vendor_bank_ifsc"];
            $vendor_bank_name = $POST["vendor_bank_name"];
            $account_holder = $POST["account_holder"];
            $vendor_bank_branch = $POST["vendor_bank_branch"];
            $vendor_bank_address = $POST["vendor_bank_address"];
            $vendor_bank_account_no = $POST["vendor_bank_account_no"];

            // $vendor_picture = $POST["vendor_picture"];
            $vendor_visible_to_all = $POST["vendor_visible_to_all"];
            //$adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]);

            $sql = "SELECT * FROM `" . $admin["tablename"] . "` WHERE `vendorCode`='" . $vendor_code . "' AND `fldAdminStatus`!='deleted'";
            if ($res = mysqli_query($dbCon, $sql)) {
                if (mysqli_num_rows($res) == 0) {
                    // console($POST);
                    $ins = "INSERT INTO `" . ERP_VENDOR_DETAILS . "` 
                            SET
                                `company_id`='" . $company_id . "',
                                `company_branch_id`='" . $branch_id . "',
                                `location_id`='" . $location_id . "',
                                `parentGlId`='" . $parentGlId . "',
                                `vendor_code`='" . $vendor_code . "',
                                `vendor_pan`='" . $vendor_pan . "',
                                `vendor_gstin`='" . $vendor_gstin . "',
                                `trade_name`='" . $trade_name . "',
                                `vendor_credit_period`='".$credit_period."',
                                `constitution_of_business`='" . $constitution_of_business . "',
                                `vendor_authorised_person_name`='" . $vendor_authorised_person_name . "',
                                `vendor_authorised_person_designation`='" . $vendor_authorised_person_designation . "',
                                `vendor_authorised_person_phone`='" . $vendor_authorised_person_phone . "',
                                `vendor_authorised_alt_phone`='" . $vendor_authorised_alt_phone . "',
                                `vendor_authorised_person_email`='" . $vendor_authorised_person_email . "',
                                `vendor_authorised_alt_email`='" . $vendor_authorised_alt_email . "',
                                `vendor_visible_to_all`='" . $vendor_visible_to_all . "',
                                `vendor_created_by`='" . $created_by . "',
                                `vendor_updated_by`='" . $created_by . "',
                                `vendor_status`='" . $vendor_status . "'";

                    if (mysqli_query($dbCon, $ins)) {
                        $vendorId = mysqli_insert_id($dbCon);
                        $admin["fldAdminVendorId"] = $vendorId;
                        $admin["vendorCode"] = $vendor_code;

                        // insert to admin details
                        addNewAdministratorUserGlobal($admin);

                        // insert to ERP_VENDOR_BUSINESS_PLACES from basic details
                        $ins = "INSERT INTO `" . ERP_VENDOR_BUSINESS_PLACES . "`
                         SET 
                            `vendor_id`='$vendorId',
                            `vendor_business_primary_flag`='1',
                            `vendor_business_building_no`='$build_no',
                            `vendor_business_flat_no`='$flat_no',
                            `vendor_business_street_name`='$street_name',
                            `vendor_business_pin_code`='$pincode',
                            `vendor_business_location`='$location',
                            `vendor_business_city`='$city',
                            `vendor_business_district`='$district',
                            `vendor_business_state`='$state',
                            `vendor_business_created_by`='$created_by',
                            `vendor_business_updated_by`='$created_by' 
                            ";
                        mysqli_query($dbCon, $ins);

                        // insert to ERP_VENDOR_BUSINESS_PLACES from other addresses
                        createVendorOtherBusinessAddr($vendorId, $POST['vendorOtherAddress']);

                        /*$paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['vendor_gl']);
                        $accounts['p_id'] = $paccdetails['data']['id'];
                        $accounts['personal_glcode_lvl'] = $paccdetails['data']['lvl'];
                        $accounts['typeAcc'] = $paccdetails['data']['typeAcc'];
                        $accounts['gl_code'] = $vendor_code;
                        $accounts['company_id'] = $company_id;
                        $accounts['gl_label'] = $trade_name;
                        $accounts['glSt'] = 'last';
                        $accounts['created_by'] = $created_by;
                        $accounts['updated_by'] = $created_by;
                        //createDataChartOfAccounts($accounts);*/

                        // insert to ERP_VENDOR_BUSINESS_PLACES from accounting
                        $insAcc = "INSERT INTO `" . ERP_VENDOR_BANK_DETAILS . "` 
                            SET
                                `vendor_id`='$vendorId',
                                `opening_balance`='$opening_balance',
                                `currency`='$currency',
                                `credit_period`='$credit_period',
                                `vendor_bank_name`='$vendor_bank_name',
                                `account_holder`='$account_holder',
                                `vendor_bank_account_no`='$vendor_bank_account_no',
                                `vendor_bank_ifsc`='$vendor_bank_ifsc',
                                `vendor_bank_branch`='$vendor_bank_branch',
                                `vendor_bank_address`='$vendor_bank_address',
                                `vendor_bank_cancelled_cheque`='$vendor_bank_cancelled_cheque'";
                        mysqli_query($dbCon, $insAcc);

                        $update_response = "UPDATE erp_vendor_response SET `vendor_id` = '$vendorId', `vendor_code` = '$vendor_code' WHERE `erp_v_id` = '$erp_v_id'";
                        mysqli_query($dbCon, $update_response);


                        $sub = "You are successfully added as a vendor";
                        $msg = "Hi, " . $vendor_authorised_person_name . ",<br> 
                            Your Login Credentials are:<br>
                            <b>Url: </b>" . BASE_URL . "vendor/<br>
                            <b>Vendor Code: </b>" . $vendor_code . "<br>
                            <b>Password: </b>" . $POST["adminPassword"] . "<br>
                            ";
                        SendMailByMySMTPmailTemplate($vendor_authorised_person_email, $sub, $msg);

                        $returnData['status'] = "success";
                        $returnData['message'] = "Vendor added success";
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "Vendor added failed";
                    }
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Vendor already exist";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Somthing went wrong";
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Setup Your Accounts first!";
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
}

//*************************************/UPDATE/******************************************//
function updateDataVendor($POST)
{
    global $dbCon;
    $returnData = [];
    //console($POST);
    // exit();

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
    $vendor_id = $POST['vendor_id'];

    $vendor_id = $_POST["vendor_id"];
    $vendor_code = $_POST["vendor_code"];
    $vendor_pan = $_POST["vendor_pan"];
    $vendor_gstin = $_POST["vendor_gstin"];
    $trade_name = $_POST["trade_name"];

    // POC details
    $vendor_authorised_person_name = $_POST["vendor_authorised_person_name"];
    $vendor_authorised_person_designation = $_POST["vendor_authorised_person_designation"];
    $vendor_authorised_person_phone = $_POST["vendor_authorised_person_phone"];
    $vendor_authorised_alt_phone = $_POST["vendor_authorised_alt_phone"];
    $vendor_authorised_person_email = $_POST["vendor_authorised_person_email"];
    $vendor_authorised_alt_email = $_POST["vendor_authorised_alt_email"];

    // other address
    $state = $_POST["state"];
    $city = $_POST["city"];
    $district = $_POST["district"];
    $location = $_POST["location"];
    $build_no = $_POST["build_no"];
    $flat_no = $_POST["flat_no"];
    $street_name = $_POST["street_name"];
    $pincode = $_POST["pincode"];

    $opening_balance = $_POST["opening_balance"];
    $currency = $_POST["currency"];
    $credit_period = $_POST["credit_period"];
    $vendor_bank_cancelled_cheque = $_POST["vendor_bank_cancelled_cheque"];
    $vendor_bank_ifsc = $_POST["vendor_bank_ifsc"];
    $vendor_bank_name = $_POST["vendor_bank_name"];
    $vendor_bank_branch = $_POST["vendor_bank_branch"];
    $vendor_bank_address = $_POST["vendor_bank_address"];
    $vendor_bank_account_no = $_POST["vendor_bank_account_no"];

    $upd = "UPDATE `erp_vendor_details` 
              SET 
                `vendor_code`='$vendor_code',
                `vendor_pan`='$vendor_pan',
                `vendor_gstin`='$vendor_gstin',
                `trade_name`='$trade_name',
                `vendor_authorised_person_name`='$vendor_authorised_person_name',
                `vendor_authorised_person_designation`='$vendor_authorised_person_designation',
                `vendor_authorised_person_email`='$vendor_authorised_person_email',
                `vendor_authorised_alt_email`='$vendor_authorised_alt_email',
                `vendor_authorised_person_phone`='$vendor_authorised_person_phone',
                `vendor_authorised_alt_phone`='$vendor_authorised_alt_phone' WHERE vendor_id='$vendor_id'";
    $returnData =  queryUpdate($upd);

    $accountUpd = "UPDATE `erp_vendor_bank_details` 
                   SET 
                     `opening_balance`='$opening_balance',
                     `currency`='$currency',
                     `credit_period`='$credit_period',
                     `vendor_bank_cancelled_cheque`='demo_check',
                     `vendor_bank_ifsc`='$vendor_bank_ifsc',
                     `vendor_bank_name`='$vendor_bank_name',
                     `vendor_bank_branch`='$vendor_bank_branch',
                     `vendor_bank_address`='$vendor_bank_address',
                      `vendor_bank_account_no`='$vendor_bank_account_no' WHERE vendor_id='$vendor_id'
    ";
    $returnData =  queryUpdate($accountUpd);
    $businessUpd = "UPDATE `erp_vendor_bussiness_places`
SET 
  `vendor_business_building_no`='$build_no',
  `vendor_business_flat_no`='$flat_no',
  `vendor_business_street_name`='$street_name',
  `vendor_business_pin_code`='$pincode',
  `vendor_business_location`='$location',
  `vendor_business_city`='$city',
  `vendor_business_district`='$district',
  `vendor_business_state`='$state'
   WHERE vendor_id='$vendor_id'
";
    $returnData =  queryUpdate($businessUpd);



    return $returnData;
}

//*************************************/SELECT ALL/******************************************//
function getAllDataVendor()
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `status`!='deleted'";
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
    $sql = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `status`!='deleted' AND `fldRoleKey`=" . $key . "";
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
function ChangeStatusVendor($data = [], $tableKeyField = "", $tableStatusField = "status")
{
    global $dbCon;
    $tableName = ERP_VENDOR_DETAILS;
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