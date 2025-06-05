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
                            `vendor_business_country`='" . $oneAddress['vendor_business_country'] ?? 'India' . "',
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
    global $companyNameNav;
    global $companyCodeNav;
    global $created_by;
    global $companyCountry;
    $componentsjsn = json_decode(getLebels($companyCountry)['data'], true);
    $businessTaxID = $componentsjsn['fields']['businessTaxID'];
    $returnData = [];
    // console($POST);
    // exit();
    $isValidate = validate($POST, [
        "legal_name" => "required",
        "vendor_authorised_person_name" => "required",
        "vendor_authorised_person_email" => "required|email",
        "vendor_authorised_person_phone" => "required|min:10|max:15",
        "adminPassword" => "required|min:4",

        "vendor_pan" => "required",
        "trade_name" => "required",
        "con_business" => "required",
        "city" => "required",
        "district" => "required",
        "location" => "required",
        "build_no" => "required",
        "street_name" => "required",
        "pincode" => "required|min:4|max:10",
        "vendor_authorised_person_designation" => "required"

    ], [
        "legal_name" => "Enter legal name",
        "vendor_authorised_person_name" => "Enter name",
        "vendor_authorised_person_email" => "Enter valid email",
        "vendor_authorised_person_phone" => "Enter valid phone",
        "adminPassword" => "Enter password(min:4 character)",

        "trade_name" => "Enter trade name",
        "con_business" => "Enter constitution of business",
        "city" => "Enter city",
        "district" => "Enter district",
        "location" => "Enter location",
        "build_no" => "Enter building number",
        "street_name" => "Enter street name",
        "pincode" => "Enter valid pincode",
        "vendor_authorised_person_designation" => "Enter designation"
    ]);


    if ($isValidate["status"] == "success") {

        $accMapp = getAllfetchAccountingMappingTbl($company_id);
        if ($accMapp["status"] == "success") {
            $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['vendor_gl']);
            $parentGlId = $paccdetails['data']['id'];
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
                $mail_send_status = '1';
            } else {
                $vendor_status = 'draft';
                $mail_send_status = '0';
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
            $vendor_gstin = $POST["vendor_gstin"] ?? '';
            $trade_name = $POST["trade_name"];
            $legal_name = $POST['legal_name'] ?? '';
            $constitution_of_business = $POST["con_business"];

            $vendor_authorised_person_name = $POST["vendor_authorised_person_name"];
            $vendor_authorised_person_designation = $POST["vendor_authorised_person_designation"];
            $vendor_authorised_person_phone = $POST["vendor_authorised_person_phone"];
            $vendor_authorised_alt_phone = $POST["vendor_authorised_alt_phone"];
            $vendor_authorised_person_email = $POST["vendor_authorised_person_email"];
            $vendor_authorised_alt_email = $POST["vendor_authorised_alt_email"];

            // other address
            $state = $POST["state"];
            $country = $POST["countries"] ?? 'India';
            $city = $POST["city"];
            $district = $POST["district"];
            $location = $POST["location"];
            $build_no = $POST["build_no"];
            $flat_no = $POST["flat_no"];
            $street_name = $POST["street_name"];
            $pincode = $POST["pincode"];

            $state_code_query = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateName` = '" . $state . "'  ORDER BY `gstCodeId` DESC LIMIT 1");

            if ($state_code_query['status'] == 'success' && $state_code_query["numRows"] > 0) {
                $state_code = $state_code_query["data"]["gstStateCode"];
            } else {
                $state_code = NULL;
            }

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

            $sql = "SELECT vendor_code FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id` = '" . $POST["company_id"] . "' AND `vendor_code`='" . $vendor_code . "'";
            //exit;
            if ($res = mysqli_query($dbCon, $sql)) {
                if (mysqli_num_rows($res) == 0) {
                    // console($POST);
                    $otp = rand(1000, 9999);
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
                                `legal_name`='" . $legal_name . "',
                                `constitution_of_business`='" . $constitution_of_business . "',
                                `vendor_authorised_person_name`='" . $vendor_authorised_person_name . "',
                                `vendor_authorised_person_designation`='" . $vendor_authorised_person_designation . "',
                                `vendor_authorised_person_phone`='" . $vendor_authorised_person_phone . "',
                                `vendor_authorised_alt_phone`='" . $vendor_authorised_alt_phone . "',
                                `vendor_authorised_person_email`='" . $vendor_authorised_person_email . "',
                                `vendor_authorised_alt_email`='" . $vendor_authorised_alt_email . "',
                                `vendor_visible_to_all`='" . $vendor_visible_to_all . "',
                                `vendor_credit_period` = $credit_period,
                                `mailValidationOtp` = '" . $otp . "',
                                `vendor_created_by`='" . $created_by . "',
                                `vendor_updated_by`='" . $created_by . "',
                                `vendor_status`='" . $vendor_status . "',
                                `mail_send_status`='" . $mail_send_status . "'";

                    if (mysqli_query($dbCon, $ins)) {
                        $vendorId = mysqli_insert_id($dbCon);


                        ///---------------------------------Audit Log Start---------------------
                        $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                        $auditTrail = array();
                        $auditTrail['basicDetail']['trail_type'] = 'ADD';   //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'	 //	Add/Update/Deleted
                        $auditTrail['basicDetail']['table_name'] = ERP_VENDOR_DETAILS;
                        $auditTrail['basicDetail']['column_name'] = 'vendor_id';  //Primary Key column
                        $auditTrail['basicDetail']['document_id'] = $vendorId;   // Primary Key
                        $auditTrail['basicDetail']['party_type'] = 'vendor';
                        $auditTrail['basicDetail']['party_id'] = $vendorId;
                        $auditTrail['basicDetail']['document_number'] = $vendor_code;
                        $auditTrail['basicDetail']['action_code'] = $action_code;
                        $auditTrail['basicDetail']['action_referance'] = '';
                        $auditTrail['basicDetail']['action_title'] = 'New Vendor Add';   // Action comment
                        $auditTrail['basicDetail']['action_name'] = 'Add';   //	Add/Update/Deleted
                        $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                        $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                        $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                        $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($ins);
                        $auditTrail['basicDetail']['others'] = '';
                        $auditTrail['basicDetail']['remark'] = '';

                        // $auditTrail['action_data']['Vendor Detail']['parentGlId'] = $parentGlId;
                        $auditTrail['action_data']['Vendor Detail']['code'] = $vendor_code;
                        $auditTrail['action_data']['Vendor Detail'][$componentsjsn['fields']['taxNumber']] = $vendor_pan;
                        $auditTrail['action_data']['Vendor Detail'][$businessTaxID] = $vendor_gstin;
                        $auditTrail['action_data']['Vendor Detail']['trade_name'] = $trade_name;
                        $auditTrail['action_data']['Vendor Detail']['constitution_of_business'] = $constitution_of_business;
                        $auditTrail['action_data']['Vendor Detail']['person_name'] = $vendor_authorised_person_name;
                        $auditTrail['action_data']['Vendor Detail']['person_designation'] = $vendor_authorised_person_designation;
                        $auditTrail['action_data']['Vendor Detail']['person_phone'] = $vendor_authorised_person_phone;
                        $auditTrail['action_data']['Vendor Detail']['alt_phone'] = $vendor_authorised_alt_phone;
                        $auditTrail['action_data']['Vendor Detail']['person_email'] = $vendor_authorised_person_email;
                        $auditTrail['action_data']['Vendor Detail']['alt_email'] = $vendor_authorised_alt_email;
                        $auditTrail['action_data']['Vendor Detail']['visible_to_all'] = $vendor_visible_to_all;
                        $auditTrail['action_data']['Vendor Detail']['Created By'] = getCreatedByUser($created_by);
                        $auditTrail['action_data']['Vendor Detail']['Updated By'] = getCreatedByUser($created_by);
                        $auditTrail['action_data']['Vendor Detail']['Vendor Status'] = $vendor_status;

                        $admin["fldAdminVendorId"] = $vendorId;
                        $admin["vendorCode"] = $vendor_code;
                        // $data = [
                        //     "date" => date('Y-m-d'),
                        //     "gl" => $parentGlId,
                        //     "subgl" => $vendor_code,
                        //     "closing_qty" => 0,
                        //     "closing_val" => $opening_balance
                        // ];
                        // addOpeningBalanceForGlSubGl($data);

                        // insert to admin details
                        // addNewAdministratorUserGlobal($admin);
                        $insAdmin = "INSERT INTO `" . $admin['tablename'] . "`
                        SET
                            `fldAdminName`='" . $admin['adminName'] . "',
                            `fldAdminEmail`='" . $admin['adminEmail'] . "',
                            `fldAdminPassword`='" . $admin['adminPassword'] . "',
                            `fldAdminPhone`='" . $admin['adminPhone'] . "', 
                            `vendorCode`='" . $vendor_code . "',
                            `fldAdminCompanyId`='" . $company_id . "',
                            `fldAdminVendorId`='" . $vendorId . "',
                            `fldAdminRole`=1";
                        queryInsert($insAdmin);

                        // insert to ERP_VENDOR_BUSINESS_PLACES from basic details
                        $ins_bussiness = "INSERT INTO `" . ERP_VENDOR_BUSINESS_PLACES . "`
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
                            `state_code`='$state_code',
                            `vendor_business_country`='$country',
                            `vendor_business_created_by`='$created_by',
                            `vendor_business_updated_by`='$created_by' 
                            ";
                        queryInsert($ins_bussiness);

                        // $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['primary_flag'] = '1';
                        $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['building_no'] = $build_no;
                        $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['flat_no'] = $flat_no;
                        $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['street_name'] = $street_name;
                        $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['pin_code'] = $pincode;
                        $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['location'] = $location;
                        $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['city'] = $city;
                        $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['district'] = $district;
                        $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['state'] = $state;

                        // insert to ERP_VENDOR_BUSINESS_PLACES from other addresses
                        // foreach ($POST['vendorOtherAddress'] as $oneAddress) {

                        //     // console($oneAddress["vendor_business_legal_name"]);
                        //     //console($key);
                        //     $insadd = "INSERT INTO `" . ERP_VENDOR_BUSINESS_PLACES . "`
                        //                      SET 
                        //                         `vendor_id`='$vendorId',
                        //                         `vendor_business_primary_flag`='0',
                        //                         `vendor_business_flat_no`='" . $oneAddress['vendor_business_flat_no'] . "',
                        //                         `vendor_business_pin_code`='" . $oneAddress['vendor_business_pin_code'] . "',
                        //                         `vendor_business_district`='" . $oneAddress['vendor_business_district'] . "',
                        //                         `vendor_business_location`='" . $oneAddress['vendor_business_location'] . "',
                        //                         `vendor_business_building_no`='" . $oneAddress['vendor_business_building_no'] . "',
                        //                         `vendor_business_street_name`='" . $oneAddress['vendor_business_street_name'] . "',
                        //                         `vendor_business_city`='" . $oneAddress['vendor_business_city'] . "',
                        //                         `vendor_business_state`='" . $oneAddress['vendor_business_state'] . "',
                        //                         `vendor_business_created_by`='$created_by',
                        //                         `vendor_business_updated_by`='$created_by'";
                        //     queryInsert($insadd);
                        //     $addAndPin = $oneAddress['vendor_business_district'] . ' (' . $oneAddress['vendor_business_pin_code'] . ')';
                        //     $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['primary_flag'] = '0';
                        //     $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['building_no'] = $oneAddress['vendor_business_building_no'];
                        //     $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['flat_no'] = $oneAddress['vendor_business_flat_no'];
                        //     $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['street_name'] = $oneAddress['vendor_business_street_name'];
                        //     $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['pin_code'] = $oneAddress['vendor_business_pin_code'];
                        //     $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['location'] = $oneAddress['vendor_business_location'];
                        //     $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['city'] = $oneAddress['vendor_business_city'];
                        //     $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['district'] = $oneAddress['vendor_business_district'];
                        //     $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['state'] = $oneAddress['vendor_business_state'];
                        // }

                        foreach ($POST['vendorOtherAddress'] as $oneAddress) {

                            $insadd = "INSERT INTO `" . ERP_VENDOR_BUSINESS_PLACES . "`
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
                            queryInsert($insadd);

                            // Check if any meaningful address field is not empty
                            if (
                                !empty($oneAddress['vendor_business_flat_no']) ||
                                !empty($oneAddress['vendor_business_pin_code']) ||
                                !empty($oneAddress['vendor_business_district']) ||
                                !empty($oneAddress['vendor_business_location']) ||
                                !empty($oneAddress['vendor_business_building_no']) ||
                                !empty($oneAddress['vendor_business_street_name']) ||
                                !empty($oneAddress['vendor_business_city']) ||
                                !empty($oneAddress['vendor_business_state'])
                            ) {
                                $addAndPin = $oneAddress['vendor_business_district'] . ' (' . $oneAddress['vendor_business_pin_code'] . ')';
                                $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin] = [
                                    // 'primary_flag' => '0',
                                    'building_no' => $oneAddress['vendor_business_building_no'],
                                    'flat_no' => $oneAddress['vendor_business_flat_no'],
                                    'street_name' => $oneAddress['vendor_business_street_name'],
                                    'pin_code' => $oneAddress['vendor_business_pin_code'],
                                    'location' => $oneAddress['vendor_business_location'],
                                    'city' => $oneAddress['vendor_business_city'],
                                    'district' => $oneAddress['vendor_business_district'],
                                    'state' => $oneAddress['vendor_business_state'],
                                ];
                            }
                        }


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


                        $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['currency'] = getSingleCurrencyType($currency);
                        $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['credit_period'] = $credit_period;
                        $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_name'] = $vendor_bank_name;
                        $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['account_holder'] = $account_holder;
                        $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_account_no'] = $vendor_bank_account_no;
                        $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_' . $componentsjsn['fields']['BankIdCode']] = $vendor_bank_ifsc;
                        $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_branch'] = $vendor_bank_branch;
                        $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_address'] = $vendor_bank_address;

                        $mailStatus = 'Not Sent';
                        if ($vendor_status == 'active') {
                            $encode_vendor_id = base64_encode($vendorId);
                            $encode_company_id = base64_encode($company_id);
                            $sub = "Welcome to $companyNameNav Partnership!";

                            $msg = "Dear $trade_name,<br>			
                        We are thrilled to welcome you as a new vendor partner with $companyNameNav. We are confident that our partnership will bring about great results for both our companies.<br>    
                        To ensure a smooth onboarding process, please find below some important information that will be helpful to you:<br><br>
                        <b>Our team:</b> Our procurement team is available to assist you with any questions or concerns you may have. Please do not hesitate to reach out to us for any support.<br>			
                        <b>Vendor Portal:</b> You will receive separate emails with your login information for $companyNameNav's vendor portal. This is where you can manage your invoices, purchase orders, and other important information.<br>			
                        <b>Communication channels:</b> To ensure seamless communication, we have set up dedicated email addresses and phone numbers for our vendor partners.<br>			
                        <b>Expectations and guidelines:</b> We have outlined our expectations and guidelines for our vendor partners in a detailed document, which we will send to you shortly.<br>	
                        If there is anything else we can do to help, please do not hesitate to contact us. We are here to support you and ensure that our partnership is a successful one.<br>			
                        <br> 
                        <b>Company Code: </b>" . $companyCodeNav . "<br>
                        <b>Vendor Code: </b>" . $vendor_code . "<br>          
                        Thank you for choosing  $companyNameNav, and we look forward to working with you.<br>
                        To validate your mail, <a href='" . BASE_URL . "branch/location/mailVerification_vendor.php?id=$encode_vendor_id&c_id=$encode_company_id'>Click Here</a><br><br>
                        Your OTP for partner app mail validation is $otp<br><br>
                    
                        Best regards,  $companyNameNav";

                            $mail =  SendMailByMySMTPmailTemplate($vendor_authorised_person_email, $sub, $msg, null, $vendor_code, 'customerAdd', $vendorId, $vendor_code);

                            $mailStatus = $mail ? 'Success' : 'Not Sent';


                            global $current_userName;
                            $whatsapparray = [];
                            $whatsapparray['templatename'] = 'vendor_onboard_msg';
                            $whatsapparray['to'] = $vendor_authorised_person_phone;
                            $whatsapparray['vendorname'] = $trade_name;
                            $whatsapparray['companyname'] = $companyNameNav;
                            $whatsapparray['companyCodeNav'] = $companyCodeNav;
                            $whatsapparray['vendor_code'] = $vendor_code;
                            $whatsapparray['password'] = $POST["adminPassword"];
                            $whatsapparray['quickcontact'] = null;
                            $whatsapparray['current_userName'] = $current_userName;
                            $whatsapparray['user_designation'] = 'Admin';

                            SendMessageByWhatsappTemplate($whatsapparray);
                        }

                        $auditTrail['action_data']['Mail Sent Status']['Send Status'] = $mailStatus;
                        $mail_verification_status = 'Not Verified';
                        $auditTrail['action_data']['Mail Verification Status']['Verification Status'] = $mail_verification_status;






                        // $auditTrail['action_data']['Mail-Send']['send-status'] = 'success';

                        $auditTrailreturn = generateAuditTrail($auditTrail);

                        ///---------------------------------Audit Log End---------------------

                        $returnData['status'] = "success";
                        $returnData['message'] = "Vendor added success";
                        $returnData['insAdmin'] = $insAdmin;
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
    global $created_by;
    global $updated_by;
    global $companyCountry;
    global $companyNameNav;
    $componentsjsn = json_decode(getLebels($companyCountry)['data'], true);
    $businessTaxID = $componentsjsn['fields']['businessTaxID'];
    $returnData = [];
    // console($POST);
    // exit();

    $vendor_id = $POST['vendor_id'];
    $vendor_code = $POST["vendor_code"];
    $vendor_pan = $POST["vendor_pan"];
    $vendor_gstin = $POST["vendor_gstin"];
    $trade_name = $POST["trade_name"];

    // POC details
    $vendor_authorised_person_name = $POST["vendor_authorised_person_name"];
    $vendor_authorised_person_designation = $POST["vendor_authorised_person_designation"];
    $vendor_authorised_person_phone = $POST["vendor_authorised_person_phone"];
    $vendor_authorised_alt_phone = $POST["vendor_authorised_alt_phone"];
    $vendor_authorised_person_email = $POST["vendor_authorised_person_email"];
    $vendor_authorised_alt_email = $POST["vendor_authorised_alt_email"];
    $billToCheckbox= $POST['billToCheckbox'];
    $adminPassword=$POST['adminPassword'];
    // other address

    $state = $POST["state"];

    $state_code_query = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateName` = '" . $state . "'  ORDER BY `gstCodeId` DESC LIMIT 1");

    if ($state_code_query['status'] == 'success' && $state_code_query["numRows"] > 0) {
        $state_code = $state_code_query["data"]["gstStateCode"];
    } else {
        $state_code = NULL;
    }

    $country = $POST["countries"] ?? 'India';
    $city = $POST["city"];
    $district = $POST["district"];
    $location = $POST["location"];
    $build_no = $POST["build_no"];
    $flat_no = $POST["flat_no"];
    $street_name = $POST["street_name"];
    $pincode = $POST["pincode"];

    $opening_balance = $POST["opening_balance"];
    $currency = $POST["currency"];
    // echo 'okkkkkk';
    $credit_period = $POST["credit_period"];
    $vendor_bank_cancelled_cheque = $POST["vendor_bank_cancelled_cheque"];
    $vendor_bank_ifsc = $POST["vendor_bank_ifsc"];
    $vendor_bank_name = $POST["vendor_bank_name"];
    $vendor_bank_branch = $POST["vendor_bank_branch"];
    $vendor_bank_address = $POST["vendor_bank_address"];
    $vendor_bank_account_no = $POST["vendor_bank_account_no"];
    $vendor_visible_to_all = $POST['vendor_visible_to_all'];
    $account_holder = $POST['account_holder'];

    // exit();

    $upd = queryUpdate("UPDATE `" . ERP_VENDOR_DETAILS . "` 
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
                `vendor_visible_to_all`='" . $vendor_visible_to_all . "',
                `vendor_authorised_alt_phone`='$vendor_authorised_alt_phone' WHERE vendor_id='$vendor_id'");



    $vendor_status_sql = "SELECT vendor_status FROM erp_vendor_details WHERE vendor_id = $vendor_id";
    $mail_valid_sql =  "SELECT isMailValid , mail_send_status  FROM `erp_vendor_details` WHERE `vendor_id` = $vendor_id";
    $mail_valid_data = queryGet($mail_valid_sql)['data'];
    $vendor_status_data = queryGet($vendor_status_sql)['data']['vendor_status'];
    $mail_validity = $mail_valid_data['isMailValid'];
    $mail_send_msg = $mail_valid_data['mail_send_status'];
    $vendor_details = queryGet("SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=$vendor_id")['data'];

    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
    $auditTrail = array();
    $auditTrail['basicDetail']['trail_type'] = 'EDIT';   //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'	 //	Add/Update/Deleted
    $auditTrail['basicDetail']['table_name'] = ERP_VENDOR_DETAILS;
    $auditTrail['basicDetail']['column_name'] = 'vendor_id';  //Primary Key column
    $auditTrail['basicDetail']['document_id'] = $vendor_id;   // Primary Key
    $auditTrail['basicDetail']['party_type'] = 'vendor';
    $auditTrail['basicDetail']['party_id'] = $vendor_id;
    $auditTrail['basicDetail']['document_number'] = $vendor_code;
    $auditTrail['basicDetail']['action_code'] = $action_code;
    $auditTrail['basicDetail']['action_referance'] = '';
    $auditTrail['basicDetail']['action_title'] = 'Vendor Edit';   // Action comment
    $auditTrail['basicDetail']['action_name'] = 'Edit';   //	Add/Update/Deleted
    $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
    $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
    $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
    $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($upd['query']);
    $auditTrail['basicDetail']['others'] = '';
    $auditTrail['basicDetail']['remark'] = '';

    $auditTrail['action_data']['Vendor Detail']['code'] = $vendor_code;
    $auditTrail['action_data']['Vendor Detail'][$componentsjsn['fields']['taxNumber']] = $vendor_pan;
    $auditTrail['action_data']['Vendor Detail'][$businessTaxID] = $vendor_gstin;
    $auditTrail['action_data']['Vendor Detail']['trade_name'] = $trade_name;
    $auditTrail['action_data']['Vendor Detail']['authorised_person_name'] = $vendor_authorised_person_name;
    $auditTrail['action_data']['Vendor Detail']['authorised_person_designation'] = $vendor_authorised_person_designation;
    $auditTrail['action_data']['Vendor Detail']['authorised_person_email'] = $vendor_authorised_person_email;
    $auditTrail['action_data']['Vendor Detail']['authorised_alt_email'] = $vendor_authorised_alt_email;
    $auditTrail['action_data']['Vendor Detail']['authorised_person_phone'] = $vendor_authorised_person_phone;
    $auditTrail['action_data']['Vendor Detail']['authorised_alt_phone'] = $vendor_authorised_alt_phone;
    $auditTrail['action_data']['Vendor Detail']['Created By'] = getCreatedByUser($vendor_details['vendor_created_by']);
    $auditTrail['action_data']['Vendor Detail']['Updated By'] = getCreatedByUser($updated_by);

    $auditTrail['action_data']['Vendor Detail']['Vendor Status'] = $vendor_status_data;
    $auditTrail['action_data']['Mail Sent Status']['Send Status'] = ($mail_send_msg === '1') ? 'Success' : 'Not Sent';
    $auditTrail['action_data']['Mail Verification Status']['Verification Status'] = ($mail_validity === 'yes') ? 'Mail Verfied' : 'Not Verified';
    if($billToCheckbox!='on'){
        $sqlupdate="UPDATE `tbl_vendor_admin_details` SET `fldAdminPassword` = '$adminPassword' WHERE  `fldAdminVendorId` = $vendor_id";
        $returnData_update_password =  queryUpdate($sqlupdate);
                            $sub = "Password Changed";
                            $msg = "Dear $trade_name,<br>
                            Your password has been changed.<br>
                            <b>New password is : </b> $adminPassword <br>
                            <b>Vendor Code :</b> $vendor_code <br>
                            Best regards,  $companyNameNav";

        $mail = SendMailByMySMTPmailTemplate($vendor_authorised_person_email, $sub, $msg, null, $vendor_code, 'passwordchanged', $vendor_id, $vendor_code);
                        
    }

    if ($upd['status'] == 'success') {

        $check_account = queryGet("SELECT * FROM `erp_vendor_bank_details` WHERE `vendor_id`=$vendor_id");
        // console($check_account);
        if ($check_account['numRows'] == 0) {
            $accountUpd = queryInsert("INSERT INTO `erp_vendor_bank_details` 
        SET 
          `opening_balance`='$opening_balance',
          `currency`='$currency',
          `credit_period`='$credit_period',
          `vendor_bank_cancelled_cheque`='demo_check',
          `vendor_bank_ifsc`='$vendor_bank_ifsc',
          `vendor_bank_name`='$vendor_bank_name',
          `vendor_bank_branch`='$vendor_bank_branch',
          `vendor_bank_address`='$vendor_bank_address',
          `account_holder`='$account_holder',
           `vendor_bank_account_no`='$vendor_bank_account_no', 
           `vendor_id` =$vendor_id,
           `vendor_bank_created_by`='" . $created_by . "',
           `vendor_bank_updated_by`='" . $updated_by . "',
           `vendor_bank_active_flag`=1");


            $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['currency'] = getSingleCurrencyType($currency);
            $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['credit_period'] = $credit_period;
            $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_name'] = $vendor_bank_name;
            $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['account_holder'] = $account_holder;
            $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_account_no'] = $vendor_bank_account_no;
            $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_' . $componentsjsn['fields']['BankIdCode']] = $vendor_bank_ifsc;
            $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_branch'] = $vendor_bank_branch;
            $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_address'] = $vendor_bank_address;
        } else {


            $accountUpd = queryUpdate("UPDATE `erp_vendor_bank_details` 
                   SET 
                     `opening_balance`='$opening_balance',
                     `currency`='$currency',
                     `credit_period`='$credit_period',
                     `vendor_bank_cancelled_cheque`='demo_check',
                     `vendor_bank_ifsc`='$vendor_bank_ifsc',
                     `vendor_bank_name`='$vendor_bank_name',
                     `vendor_bank_branch`='$vendor_bank_branch',
                     `vendor_bank_address`='$vendor_bank_address',
                     `account_holder`='$account_holder',
                      `vendor_bank_account_no`='$vendor_bank_account_no' WHERE vendor_id='$vendor_id'
    ");


            $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['currency'] = getSingleCurrencyType($currency);
            $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['credit_period'] = $credit_period;
            $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_name'] = $vendor_bank_name;
            $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['account_holder'] = $account_holder;
            $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_account_no'] = $vendor_bank_account_no;
            $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_' . $componentsjsn['fields']['BankIdCode']] = $vendor_bank_ifsc;
            $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_branch'] = $vendor_bank_branch;
            $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_address'] = $vendor_bank_address;
        }

        if ($accountUpd['status'] == 'success') {

            $check_primary = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=$vendor_id AND `vendor_business_primary_flag`=1");
            //  console($check_primary);
            if ($check_primary['numRows'] == 0) {
                $businessUpd =  queryInsert("INSERT INTO `erp_vendor_bussiness_places` 
                    SET 
                    `vendor_business_building_no`='$build_no',
                    `vendor_business_flat_no`='$flat_no',
                    `vendor_business_street_name`='$street_name',
                    `vendor_business_pin_code`='$pincode',
                    `vendor_business_location`='$location',
                    `vendor_business_city`='$city',
                    `vendor_business_district`='$district',
                    `vendor_business_state`='$state',
                    `state_code`='$state_code',
                    `vendor_business_country`='$country',
                    `vendor_id`='$vendor_id',
                    `vendor_business_active_flag`=1,
                    `vendor_business_primary_flag`=1");


                $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['building_no'] = $build_no;
                $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['flat_no'] = $flat_no;
                $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['street_name'] = $street_name;
                $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['pin_code'] = $pincode;
                $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['location'] = $location;
                $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['city'] = $city;
                $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['district'] = $district;
                $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['state'] = $state;
            } else {

                $businessUpd = queryUpdate("UPDATE `erp_vendor_bussiness_places`
                    SET 
                    `vendor_business_building_no`='$build_no',
                    `vendor_business_flat_no`='$flat_no',
                    `vendor_business_street_name`='$street_name',
                    `vendor_business_pin_code`='$pincode',
                    `vendor_business_location`='$location',
                    `vendor_business_city`='$city',
                    `vendor_business_district`='$district',
                    `vendor_business_state`='$state',
                    `state_code`='$state_code',
                    `vendor_business_country`='$country'
                    WHERE vendor_id='$vendor_id' AND vendor_business_id='" . $check_primary['data']['vendor_business_id'] . "'
                    ");


                $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['building_no'] = $build_no;
                $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['flat_no'] = $flat_no;
                $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['street_name'] = $street_name;
                $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['pin_code'] = $pincode;
                $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['location'] = $location;
                $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['city'] = $city;
                $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['district'] = $district;
                $auditTrail['action_data']['Vendor Bussiness Places'][$district . ' (' . $pincode . ')']['state'] = $state;
            }


            if ($businessUpd['status'] == 'success') {
                if ($POST['other_b_places'] == "update") {

                    foreach ($POST['vendorOtherAddress'] as $other_business) {
                        $b_id = $other_business['b_id'];
                        $flat = $other_business['vendor_business_flat_no'];
                        $pin = $other_business['vendor_business_pin_code'];
                        $b_district = $other_business['vendor_business_district'];
                        $b_location = $other_business['vendor_business_location'];
                        $build = $other_business['vendor_business_building_no'];
                        $b_city = $other_business['vendor_business_street_name'];
                        $b_state = $other_business['vendor_business_city'];
                        $street = $other_business['vendor_business_state'];





                        $otherbusinessUpd = queryUpdate("UPDATE `erp_vendor_bussiness_places`
                        SET 
                            `vendor_business_building_no`='$build',
                            `vendor_business_flat_no`='$flat',
                            `vendor_business_street_name`='$street',
                            `vendor_business_pin_code`='$pin',
                            `vendor_business_location`='$b_location',
                            `vendor_business_city`='$b_city',
                            `vendor_business_district`='$b_district',
                            `vendor_business_state`='$b_state'
                            WHERE vendor_business_id='$b_id'
                            
                        ");

                        if (
                            !empty($build) ||
                            !empty($flat) ||
                            !empty($street) ||
                            !empty($pin) ||
                            !empty($b_location) ||
                            !empty($b_city) ||
                            !empty($b_district) ||
                            !empty($b_state)
                        ) {
                            $key = $b_district . ' (' . $pin . ')';
                            $auditTrail['action_data']['Vendor Bussiness Places'][$key]['building_no'] = $build;
                            $auditTrail['action_data']['Vendor Bussiness Places'][$key]['flat_no'] = $flat;
                            $auditTrail['action_data']['Vendor Bussiness Places'][$key]['street_name'] = $street;
                            $auditTrail['action_data']['Vendor Bussiness Places'][$key]['pin_code'] = $pin;
                            $auditTrail['action_data']['Vendor Bussiness Places'][$key]['location'] = $b_location;
                            $auditTrail['action_data']['Vendor Bussiness Places'][$key]['city'] = $b_city;
                            $auditTrail['action_data']['Vendor Bussiness Places'][$key]['district'] = $b_district;
                            $auditTrail['action_data']['Vendor Bussiness Places'][$key]['state'] = $b_state;
                        }
                    }
                } elseif ($POST['other_b_places'] == "new") {

                    $flat = $POST['vendor_business_flat_no'];
                    $pin = $POST['vendor_business_pin_code'];
                    $b_district = $POST['vendor_business_district'];
                    $b_location = $POST['vendor_business_location'];
                    $build = $POST['vendor_business_building_no'];
                    $b_city = $POST['vendor_business_street_name'];
                    $b_state = $POST['vendor_business_city'];
                    $street = $POST['vendor_business_state'];

                    $otherbusinessUpd = queryInsert("INSERT INTO `erp_vendor_bussiness_places`
                SET 
                  `vendor_business_building_no`='$build',
                  `vendor_business_flat_no`='$flat',
                  `vendor_business_street_name`='$street',
                  `vendor_business_pin_code`='$pin',
                  `vendor_business_location`='$b_location',
                  `vendor_business_city`='$b_city',
                  `vendor_business_district`='$b_district',
                  `vendor_business_state`='$b_state',
                  `vendor_id`=$vendor_id,
                  `vendor_business_active_flag`=0,
                  `vendor_business_primary_flag`=0
                
                ");
                    $b_id = $otherbusinessUpd['insertedId'];

                    $auditTrail['action_data']['Vendor Bussiness Places'][$b_district . ' (' . $pin . ')']['building_no'] = $build;
                    $auditTrail['action_data']['Vendor Bussiness Places'][$b_district . ' (' . $pin . ')']['flat_no'] = $flat;
                    $auditTrail['action_data']['Vendor Bussiness Places'][$b_district . ' (' . $pin . ')']['street_name'] = $street;
                    $auditTrail['action_data']['Vendor Bussiness Places'][$b_district . ' (' . $pin . ')']['pin_code'] = $pin;
                    $auditTrail['action_data']['Vendor Bussiness Places'][$b_district . ' (' . $pin . ')']['location'] = $b_location;
                    $auditTrail['action_data']['Vendor Bussiness Places'][$b_district . ' (' . $pin . ')']['city'] = $b_city;
                    $auditTrail['action_data']['Vendor Bussiness Places'][$b_district . ' (' . $pin . ')']['district'] = $b_district;
                    $auditTrail['action_data']['Vendor Bussiness Places'][$b_district . ' (' . $pin . ')']['state'] = $b_state;
                }

                if ($otherbusinessUpd['status'] == 'success') {
                    //echo 1;

                    $insAdmin = queryUpdate("UPDATE `tbl_vendor_admin_details`
                        SET
                            `fldAdminName`='" . $POST['vendor_authorised_person_name'] . "',
                            `fldAdminEmail`='" . $POST['vendor_authorised_person_email'] . "',
                            `fldAdminPassword`='" . $POST['adminPassword'] . "',
                            `fldAdminPhone`='" . $POST['vendor_authorised_person_phone'] . "' 
                             WHERE
                            `fldAdminVendorId`='" . $vendor_id . "' AND
                            `fldAdminRole`=1");
                    //  queryInsert($insAdmin);

                    //   console($insAdmin);
                    //   exit();


                    if ($insAdmin['status'] == 'success') {
                        $returnData['status'] = 'success';
                        $returnData['message'] = 'vendor updated successfully.';
                    } else {


                        $returnData['status'] = 'warning';
                        $returnData['message'] = 'vendor poc update failed.';
                    }
                } else {
                    //  echo 0;
                    $returnData['status'] = 'warning';
                    $returnData['message'] = 'vendor business places update failed.';
                }
            } else {
                $returnData['status'] = 'warning';
                $returnData['message'] = 'vendor primary business place update failed.';
            }
        } else {
            $returnData['status'] = 'warning';
            $returnData['message'] = 'vendor account update failed.';
        }
    } else {
        $returnData['status'] = 'warning';
        $returnData['message'] = 'vendor update failed.';
    }
    //echo 5;
    $auditTrailreturn = generateAuditTrail($auditTrail);

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
    global $current_userName;
    global $companyNameNav;
    global $companyCodeNav;
    global $company_id;
    global $current_userName;
    global $companyCountry;
    $componentsjsn = json_decode(getLebels($companyCountry)['data'], true);
    $businessTaxID = $componentsjsn['fields']['businessTaxID'];
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
            $newStatus = "deleted"; // default status
            if ($data["changeStatus"] == "active_inactive") {
                $newStatus = ($prevData[$tableStatusField] == "active") ? "inactive" : "active";
            } elseif ($data["changeStatus"] == "guest_to_active" && $prevData[$tableStatusField] == "guest") {
                $newStatus = "active";
            } elseif ($data["changeStatus"] == "draft_to_active" && $prevData[$tableStatusField] == "draft") {
                $newStatus = "active";
            }

            $mail_verify_send_flag = 'Not Sent';

            if ($prevData[$tableStatusField] == 'draft' && $newStatus == 'active') {
                // For draft to active, send mail first.
                $vendor_sql = "SELECT erp_vendor_details.*, tbl_vendor_admin_details.fldAdminPassword 
                               FROM erp_vendor_details 
                               LEFT JOIN tbl_vendor_admin_details 
                               ON erp_vendor_details.vendor_id = tbl_vendor_admin_details.fldAdminVendorId 
                               WHERE erp_vendor_details.vendor_id = $id";
                $vendor_data = queryGet($vendor_sql)['data'];

                $trade_name = $vendor_data['trade_name'];
                $vendor_code = $vendor_data['vendor_code'];
                $vendor_authorised_person_email = $vendor_data['vendor_authorised_person_email'];
                $otp  = $vendor_data['mailValidationOtp'];
                $vendor_authorised_person_phone = $vendor_data['vendor_authorised_person_phone'];
                $adminPassword = $vendor_data['fldAdminPassword'];

                $sub = "Welcome to $companyNameNav Partnership!";
                $msg = "Dear $trade_name,<br>			
                        We are thrilled to welcome you as a new vendor partner with $companyNameNav. We are confident that our partnership will bring about great results for both our companies.<br>    
                        To ensure a smooth onboarding process, please find below some important information that will be helpful to you:<br><br>
                        <b>Our team:</b> Our procurement team is available to assist you with any questions or concerns you may have. Please do not hesitate to reach out to us for any support.<br>			
                        <b>Vendor Portal:</b> You will receive separate emails with your login information for $companyNameNav's vendor portal. This is where you can manage your invoices, purchase orders, and other important information.<br>			
                        <b>Communication channels:</b> To ensure seamless communication, we have set up dedicated email addresses and phone numbers for our vendor partners.<br>			
                        <b>Expectations and guidelines:</b> We have outlined our expectations and guidelines for our vendor partners in a detailed document, which we will send to you shortly.<br>
                        If there is anything else we can do to help, please do not hesitate to contact us. We are here to support you and ensure that our partnership is a successful one.<br>			
                        <br> 
                        <b>Company Code: </b>" . $companyCodeNav . "<br>
                        <b>Vendor Code: </b>" . $vendor_code . "<br>          
                        Thank you for choosing $companyNameNav, and we look forward to working with you.<br>
                        To validate your mail, <a href='" . BASE_URL . "branch/location/mailVerification_vendor.php?id=$id&company_id=$company_id'>Click Here</a><br><br>
                        Your OTP for partner app mail validation is $otp<br><br>
                        Best regards,  $companyNameNav";

                $mail = SendMailByMySMTPmailTemplate(
                    $vendor_authorised_person_email,
                    $sub,
                    $msg,
                    null,
                    $vendor_code,
                    'customerAdd',
                    $id,
                    $vendor_code
                );

                if ($mail) {
                    $mail_verify_send_flag = 'Success';
                    // Only update if mail sent successfully.
                    $changeStatusSql = "UPDATE `" . $tableName . "` 
                                        SET `" . $tableStatusField . "` = '" . $newStatus . "', 
                                            `mail_send_status` = '1' 
                                        WHERE `" . $tableKeyField . "` = " . $id;
                    if (mysqli_query($dbCon, $changeStatusSql)) {
                        $whatsapparray = [];
                        $whatsapparray['templatename'] = 'vendor_onboard_msg';
                        $whatsapparray['to'] = $vendor_authorised_person_phone;
                        $whatsapparray['vendorname'] = $trade_name;
                        $whatsapparray['companyname'] = $companyNameNav;
                        $whatsapparray['companyCodeNav'] = $companyCodeNav;
                        $whatsapparray['vendor_code'] = $vendor_code;
                        $whatsapparray['password'] = $adminPassword;
                        $whatsapparray['quickcontact'] = null;
                        $whatsapparray['current_userName'] = $current_userName;
                        $whatsapparray['user_designation'] = 'Admin';

                        $whatsappReturn = SendMessageByWhatsappTemplate($whatsapparray);

                        $returnData["status"] = "success";
                        $returnData["message"] = "Status has been changed to " . strtoupper($newStatus);
                    } else {
                        $returnData["status"] = "error";
                        $returnData["message"] = "Update failed after sending mail.";
                    }
                } else {
                    $returnData["status"] = "error";
                    $returnData["message"] = "Mail sending failed.";
                }
            } else {
                // For any other status change, update directly.
                $changeStatusSql = "UPDATE `" . $tableName . "` 
                                    SET `" . $tableStatusField . "` = '" . $newStatus . "' 
                                    WHERE `" . $tableKeyField . "` = " . $id;
                if (mysqli_query($dbCon, $changeStatusSql)) {
                    $returnData["status"] = "success";
                    $returnData["message"] = "Status has been changed to " . strtoupper($newStatus);
                } else {
                    $returnData["status"] = "error";
                    $returnData["message"] = "Something went wrong, Try again...!";
                }
            }
            $returnData["changeStatusSql"] = $changeStatusSql;

            $vendor_code_sql = "SELECT vendor_code FROM erp_vendor_details WHERE vendor_id = $id";
            $vendor_code_data = queryGet($vendor_code_sql)['data']['vendor_code'];


            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'EDIT';   //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'	 //	Add/Update/Deleted
            $auditTrail['basicDetail']['table_name'] = ERP_VENDOR_DETAILS;
            $auditTrail['basicDetail']['column_name'] = 'vendor_id';  //Primary Key column
            $auditTrail['basicDetail']['document_id'] = $id;   // Primary Key
            $auditTrail['basicDetail']['party_type'] = 'vendor';
            $auditTrail['basicDetail']['party_id'] = $id;
            $auditTrail['basicDetail']['document_number'] = $vendor_code_data;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Vendor Status Change';   // Action comment
            $auditTrail['basicDetail']['action_name'] = 'Edit';   //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($changeStatusSql);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $vendor_data_sql = "SELECT * FROM erp_vendor_details WHERE vendor_id = $id";
            $vendor_data = queryGet($vendor_data_sql)['data'];


            $vendor_code = $vendor_data['vendor_code'];
            $vendor_pan = $vendor_data['vendor_pan'];
            $vendor_gstin = $vendor_data['vendor_gstin'];
            $trade_name = $vendor_data['trade_name'];
            $vendor_authorised_person_name = $vendor_data['vendor_authorised_person_name'];
            $vendor_authorised_person_designation = $vendor_data['vendor_authorised_person_designation'];
            $vendor_authorised_person_email = $vendor_data['vendor_authorised_person_email'];
            $vendor_authorised_alt_email = $vendor_data['vendor_authorised_alt_email'];
            $vendor_authorised_person_phone = $vendor_data['vendor_authorised_person_phone'];
            $vendor_authorised_alt_phone = $vendor_data['vendor_authorised_alt_phone'];
            $created_by = $vendor_data['vendor_created_by'];
            $updated_by = $vendor_data['vendor_created_by'];
            $vendor_status = $vendor_data['vendor_status'];
            $mail_send_msg = $vendor_data['mail_send_status'];
            $mail_validity = $vendor_data['isMailValid'];



            $auditTrail['action_data']['Vendor Detail']['code'] = $vendor_code;
            $auditTrail['action_data']['Vendor Detail'][$componentsjsn['fields']['taxNumber']] = $vendor_pan;
            $auditTrail['action_data']['Vendor Detail'][$businessTaxID] = $vendor_gstin;
            $auditTrail['action_data']['Vendor Detail']['trade_name'] = $trade_name;
            $auditTrail['action_data']['Vendor Detail']['authorised_person_name'] = $vendor_authorised_person_name;
            $auditTrail['action_data']['Vendor Detail']['authorised_person_designation'] = $vendor_authorised_person_designation;
            $auditTrail['action_data']['Vendor Detail']['authorised_person_email'] = $vendor_authorised_person_email;
            $auditTrail['action_data']['Vendor Detail']['authorised_alt_email'] = $vendor_authorised_alt_email;
            $auditTrail['action_data']['Vendor Detail']['authorised_person_phone'] = $vendor_authorised_person_phone;
            $auditTrail['action_data']['Vendor Detail']['authorised_alt_phone'] = $vendor_authorised_alt_phone;
            $auditTrail['action_data']['Vendor Detail']['Created By'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['Vendor Detail']['Updated By'] = getCreatedByUser($updated_by);
            $auditTrail['action_data']['Vendor Detail']['Vendor Status'] = $vendor_status;
            $auditTrail['action_data']['Mail Sent Status']['Send Status'] = ($mail_send_msg == '1') ? 'Success' : 'Not Sent';;
            $auditTrail['action_data']['Mail Verification Status']['Verification Status'] = ($mail_validity === 'yes') ? 'Mail Verfied' : 'Not Verified';


            generateAuditTrail($auditTrail);
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