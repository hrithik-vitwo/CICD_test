<?php
//*************************************/INSERT/******************************************//
function createDataCompany($POST)
{
    global $dbCon;
    $returnData = [];
    $isValidate = validate($POST, [
        "adminName" => "required",
        "company_mrp_priority"=>"required",
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

        $favicon = "";


        $company_name = addslashes($POST["company_name"]);
        $company_pan = $POST["company_pan"];
        $company_cin = $POST["company_cin"];
        $company_llpin = $POST["company_llpin"];
        $company_tan = $POST["company_tan"];
        $company_const_of_business = $POST["company_const_of_business"];
        $company_currency = $POST["company_currency"];
        $company_language = $POST["company_language"];
        $company_mrp_priority= $POST["company_mrp_priority"];

        $city = addslashes($POST['city']);

        $country = $POST['country'];
        $state = addslashes($POST['state']);

      
        $pin = $POST['pin'];
        $street = addslashes($POST['street']);
        $flat = $POST['flat'];
        $building = addslashes($POST['building']);
        $location = addslashes($POST['location']);
        $district = addslashes($POST['district']);

        //$company_code = getCompanySerialNumber(ERP_COMPANIES, 'company_code');
        // ***************
        $sql = "SELECT company_code FROM `" . ERP_COMPANIES . "` ORDER BY company_id DESC LIMIT 1";
        $lastSoNo = queryGet($sql);
        // console($lastSoNo);
        if (isset($lastSoNo['data'])) {
            $lastSoNo = $lastSoNo['data']['company_code'] ?? 0;
        } else {
            $lastSoNo = '';
        }
        $company_code = getCompanySerialNumber($lastSoNo);
        // ***************

        //$adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]);

        $sql = "SELECT * FROM `" . $admin["tablename"] . "` WHERE `fldAdminEmail`='" . $admin["adminEmail"] . "' AND `fldAdminStatus`!='deleted'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) == 0) {

                $ins = "INSERT INTO `" . ERP_COMPANIES . "` 
                            SET
                                `company_name`='" . $company_name . "',
                                `company_code`='" . $company_code . "',
                                `company_pan`='" . $company_pan . "',
                                `company_cin`='" . $company_cin . "',
                                `company_llpin`='" . $company_llpin . "',
                                `company_tan`='" . $company_tan . "',
                                `company_const_of_business`='" . $company_const_of_business . "',
                                `company_currency`='" . $company_currency . "',
                                `company_language`='" . $company_language . "',
                                `company_status`='" . $company_status . "',
                                `company_logo`='" . $logo . "',
                                `gl_account_length`=10,
                                `gl_length_bkup`='1-2-2-2-3',
                                `mrpPriority`='".$company_mrp_priority."',
                                
                                `company_favicon`='" . $favicon . "',
                                `company_building`='" . $building . "',
                                `company_flat_no`='" . $flat . "',
                                `company_country`='".$country."',
                                `company_state`='" . $state . "',
                                `company_district`='" . $district . "',
                                `company_location`='" . $location . "',
                                `company_pin`='" . $pin . "',
                                `company_street`='" . $street . "',
                                `company_city`='" . $city . "' ";

                if (mysqli_query($dbCon, $ins)) {
                    $last_id = mysqli_insert_id($dbCon);

                    define("COMP_STORAGE_DIR", BUCKET_DIR . "uploads/$last_id");

                    $companyDirObj = createCompanyUploadDirs();
                    if ($companyDirObj["status"] == "success") {
                        $logo = uploadFile($POST["logo"], COMP_STORAGE_DIR . "/profile/", ["jpg", "jpeg", "png"]);
                        if ($logo['status'] == 'success') {
                            $logo = $logo['data'];
                        } else {
                            $logo = '';
                        }

                        $favicon = uploadFile($POST["favicon"], COMP_STORAGE_DIR . "/profile/", ["jpg", "jpeg", "png", "ico"]);
                        if ($favicon['status'] == 'success') {
                            $favicon = $favicon['data'];
                        } else {
                            $favicon = '';
                        }

                        $compUpdare = queryUpdate("UPDATE `" . ERP_COMPANIES . "` SET `company_logo`='" . $logo . "',`company_favicon`='" . $favicon . "'  WHERE company_id=$last_id");
                    }


                    $sql = "CREATE TABLE IF NOT EXISTS `erp_acc_coa_" . $last_id . "_table` (
                        `id` int(20) NOT NULL AUTO_INCREMENT,
                        `company_id` int(11) DEFAULT NULL,
                        `ordering` int(11) NOT NULL DEFAULT '0',
                        `lock_status` int(11) NOT NULL DEFAULT '0' COMMENT '0- unlock, 1- lock',
                        `txn_status` int(11) NOT NULL DEFAULT '0' COMMENT '0- untxn, 1- txn',
                        `glSt` varchar(5) DEFAULT NULL COMMENT 'last',
                        `glStType` enum('group','account') DEFAULT 'account' COMMENT 'group/account',
                        `typeAcc` varchar(25) DEFAULT NULL,
                        `p_id` int(11) DEFAULT NULL COMMENT 'MIS',
                        `sp_id` int(11) DEFAULT NULL COMMENT 'SCHEDULE-3',
                        `lvl` int(11) NOT NULL DEFAULT '0',
                        `gl_code` varchar(255) DEFAULT NULL,
                        `gl_label` varchar(255) DEFAULT NULL,
                        `remark` text,
                        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                        `created_by` varchar(255) DEFAULT NULL,
                        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        `updated_by` varchar(255) DEFAULT NULL,
                        `status` enum('active','inactive','deleted','draft') DEFAULT 'active',
                        PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                    mysqli_query($dbCon, $sql);

                    $sql = "CREATE TABLE `erp_audit_trail_" . $last_id . "_table` ( 
                        `id` bigint(20) NOT NULL AUTO_INCREMENT, 
                        `company_id` int(11) DEFAULT NULL, 
                        `branch_id` int(11) DEFAULT NULL, 
                        `location_id` int(11) DEFAULT NULL, 
                        `party_type` varchar(250) DEFAULT NULL, 
                        `party_id` bigint(20) DEFAULT NULL, 
                        `trail_type` varchar(255) DEFAULT NULL COMMENT '''ADD'',''EDIT'',''DELETE'',''ACCENTRY,''ACCREVERSE'',''MAILSEND'',''MAILSEEN'',''APPROVED''', 
                        `table_name` varchar(255) DEFAULT NULL, 
                        `column_name` varchar(255) DEFAULT NULL COMMENT 'table primary key fld', 
                        `document_id` bigint(20) DEFAULT NULL COMMENT 'primary key value', 
                        `document_number` varchar(250) DEFAULT NULL COMMENT 'Doc Code', 
                        `action_code` varchar(100) DEFAULT NULL COMMENT 'ADT8551', 
                        `action_referance` varchar(255) DEFAULT NULL, 
                        `action_title` varchar(255) DEFAULT NULL, 
                        `action_name` varchar(255) DEFAULT NULL COMMENT 'Add/Update/Deleted', 
                        `action_type` varchar(255) DEFAULT NULL COMMENT 'Monitory/Non-Monitory', 
                        `action_url` text, 
                        `action_previous_url` text, 
                        `action_sqlQuery` text, 
                        `action_data` text, 
                        `others` varchar(255) DEFAULT NULL, 
                        `remark` text, 
                        `created_at` datetime DEFAULT CURRENT_TIMESTAMP, 
                        `created_by` varchar(255) DEFAULT NULL, 
                        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                        `updated_by` varchar(255) DEFAULT NULL, 
                        `status` enum('active','inactive','deleted','draft') DEFAULT 'active',
                        PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                     mysqli_query($dbCon, $sql);

                    $instcosdata = "INSERT INTO `erp_acc_coa_" . $last_id . "_table` 
                        (`id`, `company_id`, `glSt`, `glStType`, `typeAcc`, `p_id`, `sp_id`, `lvl`, `gl_code`, `gl_label`) 
                        VALUES
                        (1, $last_id, NULL, 'group', '1', 0, 0, 0, '', 'Asset'),
                        (2, $last_id, NULL, 'group', '2', 0, 0, 0, '', 'Liabilities'),
                        (3, $last_id, NULL, 'group', '3', 0, 0, 0, '', 'Income'),
                        (4, $last_id, NULL, 'group', '4', 0, 0, 0, '', 'Expense')";
                    mysqli_query($dbCon, $instcosdata);

                    $ivvarientdata = "INSERT INTO `erp_iv_varient` (`id`, `company_id`, `flag_default`, `last_inv_no`, `title`, `verient_serialized`, `iv_number_example`, `seperator`, `reset_time`, `description`, `created_at`, `created_by`, `updated_at`, `updated_by`, `status`) 
                    VALUES (NULL, $last_id, '0', NULL, 'Default Variant', 'a:2:{s:6:\"prefix\";s:3:\"INV\";s:6:\"serial\";s:10:\"0000000001\";}', 'INV-0000000001', '-', 'never', '', '2023-04-17 09:43:23', '$last_id|company', '2023-04-17 09:43:23', '$last_id|company', '1')
                    ";
                    mysqli_query($dbCon, $ivvarientdata);
                    
                    
                    $cnvarientdata = "INSERT INTO `erp_cn_varient` (`id`, `company_id`, `flag_default`, `last_inv_no`, `title`, `verient_serialized`, `iv_number_example`, `seperator`, `reset_time`, `description`, `created_at`, `created_by`, `updated_at`, `updated_by`, `status`) 
                    VALUES (NULL, $last_id, '0', NULL, 'Default Variant', 'a:2:{s:6:\"prefix\";s:2:\"CN\";s:6:\"serial\";s:10:\"0000000001\";}', 'CN-0000000001', '-', 'never', '', '2023-04-17 09:43:23', '$last_id|company', '2023-04-17 09:43:23', '$last_id|company', '1')
                    ";
                    mysqli_query($dbCon, $cnvarientdata);
                    
                    $dnvarientdata = "INSERT INTO `erp_dn_varient` (`id`, `company_id`, `flag_default`, `last_inv_no`, `title`, `verient_serialized`, `iv_number_example`, `seperator`, `reset_time`, `description`, `created_at`, `created_by`, `updated_at`, `updated_by`, `status`) 
                    VALUES (NULL, $last_id, '0', NULL, 'Default Variant', 'a:2:{s:6:\"prefix\";s:2:\"DN\";s:6:\"serial\";s:10:\"0000000001\";}', 'DN-0000000001', '-', 'never', '', '2023-04-17 09:43:23', '$last_id|company', '2023-04-17 09:43:23', '$last_id|company', '1')
                    ";
                    mysqli_query($dbCon, $dnvarientdata);
                    



                    $admin["fldAdminCompanyId"] = $last_id;
                    addNewAdministratorUserGlobal($admin, 3);

                    $sub = "Welcome to $company_name!";
                    $msg = "Dear " . $POST["adminName"] . ",<br />
                    We are delighted to welcome you on board as a valued client of ViTWO.ai. We are committed to providing you with the best possible service and support, and we look forward to working with you.<br>
                    To ensure a smooth onboarding process, please find below some important information that will be helpful to you:<br><br>
                    
                    <b>Our team:</b> Our team is available to assist you with any questions or concerns you may have. Please do not hesitate to reach out to us for any support.<br>
                    <b>Login information:</b><br>	
                    <b>Url: </b>" . COMPANY_URL . "<br>
                    <b>User Name: </b>" . $POST["adminEmail"] . "<br>
                    <b>Password: </b>" . $POST["adminPassword"] . "<br>   
                    <b>Resources:</b> We have a range of resources available to help you make the most of our services, including user guides, tutorials, and FAQs.<br>		
                    <b>Upcoming events:</b> We regularly host webinars, workshops, and other events to help you stay up-to-date with the latest developments in our services.<br>
                    If there is anything else we can do to help, please do not hesitate to contact us. We are here to support you and ensure that your experience with $company_name is a positive one.<br>
                    Thank you for choosing $company_name, and we look forward to working with you.<br><br>
                    Best regards, ViTWO.ai";

                    SendMailByMySMTPmailTemplate($POST["adminEmail"], $sub, $msg);

                    global $current_userName; 

                    $whatsapparray = [];
                    $whatsapparray['templatename'] = 'company_onboard';
                    $whatsapparray['to'] = $POST["adminPhone"];
                    $whatsapparray['companyname'] = $company_name;
                    $whatsapparray['username'] = $POST["adminEmail"];
                    $whatsapparray['password'] = $POST["adminPassword"];
                    $whatsapparray['quickcontact'] = null;
                    $whatsapparray['current_userName'] = $current_userName;

                    SendMessageByWhatsappTemplate($whatsapparray);
                    

                    $returnData['status'] = "success";
                    $returnData['message'] = "Company added success";
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Company added failed";
                }
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Company already exist";
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
    $sql = "SELECT * FROM `" . ERP_COMPANIES . "` as comp, `" . ERP_LANGUAGE . "` as lang , `" . ERP_CURRENCY_TYPE . "` as curr, `tbl_company_admin_details` as det WHERE lang.language_id=comp.company_language AND comp.company_status!='deleted' AND comp.company_id=" . $company_id . " AND curr.currency_id=comp.company_currency AND det.fldAdminCompanyId=comp.company_id AND det.fldAdminRole=3";
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


function saveCompanyDetails($POST)
{

    // console($POST['po_enable']);
    // console($_POST);
    // exit();
 

    if(isset($POST['po_enable']) && !empty($POST['po_enable'])){
        $po_enable = 1;
    }
    else{
        $po_enable = 0;
    }
    if(isset($POST['qa_enable']) && !empty($POST['qa_enable'])){
        $qa_enable = 1;
    }
    else{
        $qa_enable = 0;
    }

    if(isset($POST['isEmailActive']) && !empty($POST['isEmailActive'])){
        $isEmailActive = 'yes';
    }
    else{
        $isEmailActive = 'no';
    }
    if(isset($POST['isWhatsappActive']) && !empty($POST['isWhatsappActive'])){
        $isWhatsappActive = 'yes';
    }
    else{
        $isWhatsappActive = 'no';
    }
    //     exit();
    $name = $POST["name"];
    $email = $POST["email"];
    $phone = $POST["phone"];
    $code = $POST["code"];
    $company_pan = $POST["company_pan"];
    $cin = $POST["cin"];
    $llpin = $POST["llpin"];
    $tan = $POST["tan"];
    $gl_length = $POST["gl_length"];
    $gl_brkup = $POST["gl_brkup"];
    $lang = $POST["lang"];
    $currency = $POST["currency"];
    $footer = $POST["footer"];
    $address = $POST['address'];
    $flat = $POST['flat'];
    $building = $POST['building'];
    $state = $POST['state'];
    $district = $POST['district'];
    $location = $POST['location'];
    $company_mrp_priority= $POST["company_mrp_priority"];
    $sales_invoice_declaration= $POST["sales_invoice_declaration"];
    $region=$POST["region"];
    $decimal_quantity=$POST["decimal_quantity"];
    $decimal_value=$POST["decimal_value"];

    $pin = $POST['pin'];
    $street = $POST['street'];
    $city = $POST['city'];
$date = $POST['date'];
    //  $logo=$POST["logo"];
    // $favicon= $POST["favicon"];
    $const = $POST["const"];
    $company_id = $POST['company_id'];
    if (isset($POST["logo"]) && !empty($POST["logo"])) {
        $logo = uploadFile($POST["logo"], COMP_STORAGE_DIR . "/profile/", ["jpg", "jpeg", "png"]);
        if ($logo['status'] == 'success') {
            $logo = $logo['data'];

            $insl = "UPDATE`" . ERP_COMPANIES . "` 
                        SET
                        `company_logo` = '" . $logo . "'
                        WHERE `company_id`='" . $company_id . "'";

            queryInsert($insl);
        }
    }

    if (isset($POST["favicon"]) && !empty($POST["favicon"])) {
        $favicon = uploadFile($POST["favicon"], COMP_STORAGE_DIR . "/profile/", ["jpg", "jpeg", "png", "ico"]);
        if ($favicon['status'] == 'success') {
            $favicon = $favicon['data'];

            $insf = "UPDATE`" . ERP_COMPANIES . "` 
                        SET
                        `company_favicon` = '" . $favicon . "'
                        WHERE `company_id`='" . $company_id . "'";

            queryInsert($insf);
        }
    }

    if (isset($POST["signature"]) && !empty($POST["signature"])) {
        $signature = uploadFile($POST["signature"], COMP_STORAGE_DIR . "/profile/", ["jpg", "jpeg", "png", "ico"]);
        if ($signature['status'] == 'success') {
            $signature = $signature['data'];

            $insSig = "UPDATE`" . ERP_COMPANIES . "` 
                        SET
                        `signature` = '" . $signature . "'
                        WHERE `company_id`='" . $company_id . "'";

            queryInsert($insSig);
        }
    }


    $ins = "UPDATE`" . ERP_COMPANIES . "`
    SET
    `company_code`='" . $code . "',
    `mrpPriority`='".$company_mrp_priority."',
    `sales_invoice_declaration`='".$sales_invoice_declaration."',
    `company_name`='" . $name . "',
    `company_pan`='" . $company_pan . "',
    `company_cin`='" .  $cin  . "',
    `company_llpin`='" . $llpin . "',
    `company_tan`='" . $tan . "',
    `company_const_of_business`='" . $const . "',
    `company_currency`='" . $currency . "',
    `gl_account_length`='" . $gl_length . "',
    `gl_length_bkup`='" . $gl_brkup . "',
    `company_footer`='" . $footer . "',
    `company_address`='" . $address . "',
    `company_building`='" . $building . "',
    `company_flat_no`='" . $flat . "',
    `company_state`='" . $state . "',
    `company_district`='" . $district . "',
    `company_location`='" . $location . "',
    `region`='".$region."',
    `company_pin`='" . $pin . "',
    `company_street`='" . $street . "',
    `company_city`='" . $city . "',
    `opening_date`='" . $date . "',
    `isPoEnabled`='".$po_enable."',
    `isQaEnabled`='".$qa_enable."',
    `isWhatsappActive`='".$isWhatsappActive."',
    `isEmailActive`='".$isEmailActive."',
    `decimal_quantity`='".$decimal_quantity."',
    `decimal_value`='".$decimal_value."'
    WHERE `company_id`='" . $company_id . "'";

    $insertQuery = queryInsert($ins);
    $returnData = $insertQuery;

    $check = queryGet("SELECT * FROM `tbl_company_admin_details` WHERE `fldAdminEmail`='" . $email . "' AND `fldAdminCompanyId`=$company_id");
    if ($check['numRows'] == 0) {
        $insert = "UPDATE `tbl_company_admin_details`  
                    SET
                        `fldAdminPhone`='" . $phone . "',
                        `fldAdminEmail`='" . $email . "'
                    WHERE `fldAdminCompanyId`='" . $company_id . "' AND `fldAdminRole`=1";

        $update = queryInsert($insert);
    }
    return $returnData;
}
