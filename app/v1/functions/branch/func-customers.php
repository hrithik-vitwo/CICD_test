<?php
//*************************************/INSERT/******************************************//

function createCustomerAddr($customerId, $ADDRESSES = [])
{
    global $dbCon;
    $returnData = [];

    $noOfAddresses = count($ADDRESSES);
    $noOfSuccessAdded = 0;
    // console($ADDRESSES);

    foreach ($ADDRESSES as $oneAddress) {

        // console($oneAddress["customer_address_legal_name"]);
        //console($key);
        $ins = "INSERT INTO `" . ERP_CUSTOMER_ADDRESS . "`
                         SET 
                            `customer_id`='$customerId',
                            `customer_address_primary_flag`='0',
                            `customer_address_flat_no`='" . $oneAddress['customer_address_flat_no'] . "',
                            `customer_address_pin_code`='" . $oneAddress['customer_address_pin_code'] . "',
                            `customer_address_district`='" . $oneAddress['customer_address_district'] . "',
                            `customer_address_location`='" . $oneAddress['customer_address_location'] . "',
                            `customer_address_building_no`='" . $oneAddress['customer_address_building_no'] . "',
                            `customer_address_street_name`='" . $oneAddress['customer_address_street_name'] . "',
                            `customer_address_city`='" . $oneAddress['customer_address_city'] . "',
                            `customer_address_country`='" . $oneAddress['country'] ?? 'India' . "',
                            `customer_address_state`='" . $oneAddress['customer_address_state'] . "'";

        //   console($ins);
        mysqli_query($dbCon, $ins);

        //$noOfSuccessAdded++;
    }
}


function createDataCustomer($POST = [])
{
    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $companyCodeNav;
    global $companyNameNav;
    global $companyCountry;
    $componentsjsn = json_decode(getLebels($companyCountry)['data'], true);
    $businessTaxID = $componentsjsn['fields']['businessTaxID'];
    $taxNumber = $componentsjsn['fields']['taxNumber'];
    $returnData = [];
    $isValidate = validate($POST, [
        "customer_authorised_person_name" => "required",
        "customer_authorised_person_email" => "required|email",
        "customer_authorised_person_phone" => "required|min:10|max:15",
        "adminPassword" => "required|min:4",
        "city" => "required",
        "district" => "required",
        "location" => "required",
        "build_no" => "required",
        "street_name" => "required",
        "pincode" => "required|min:4|max:10",
        "credit_period" => "required|numeric",
        "customer_authorised_person_designation" => "required"

    ], [
        "customer_authorised_person_name" => "Enter name",
        "customer_authorised_person_email" => "Enter valid email",
        "customer_authorised_person_phone" => "Enter valid phone",
        "adminPassword" => "Enter password(min:4 character)",
        "city" => "Enter city",
        "district" => "Enter district",
        "location" => "Enter location",
        "build_no" => "Enter building number",
        "street_name" => "Enter street name",
        "pincode" => "Enter valid pincode",
        "credit_period" => "Enter valid credit period",
        "customer_authorised_person_designation" => "Enter designation"
    ]);

    //console($POST);
    if ($isValidate["status"] == "success") {

        $accMapp = getAllfetchAccountingMappingTbl($company_id);
        // console($accMapp);
        if ($accMapp["status"] == "success") {
            $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['customer_gl']);
            $parentGlId = $paccdetails['data']['id'];
            $admin = array();
            $admin["adminName"] = $POST["customer_authorised_person_name"];
            $admin["adminEmail"] = $POST["customer_authorised_person_email"];
            $admin["adminPhone"] = $POST["customer_authorised_person_phone"];
            $admin["adminPassword"] = $POST["adminPassword"];
            $admin["tablename"] = 'tbl_customer_admin_details';
            $admin["adminPassword"] = $POST["adminPassword"];
            $admin["fldAdminCompanyId"] = $POST["company_id"];
            $admin["fldAdminBranchId"] = $POST["company_branch_id"];

            if (isset($POST["createdata"]) && $POST["createdata"] == 'add_post') {
                $customer_status = 'active';
                $mail_send_status = '1';
            } else {
                $customer_status = 'draft';
                $mail_send_status = '0';
            }
            $lastlQuery = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE `company_id` = '" . $POST["company_id"] . "'  ORDER BY `customer_id` DESC LIMIT 1";
            $resultLast = queryGet($lastlQuery);
            $rowLast = $resultLast["data"];
            //    console($resultLast);
            $lastsl = $rowLast['customer_code'];
            //   echo "ok";
            //
            $company_id = $POST["company_id"];
            $company_branch_id = $POST["company_branch_id"];
            $customer_code = getCustomerSerialNumber($lastsl);

            $customer_pan = $POST["customer_pan"];
            $customer_gstin = $POST["customer_gstin"] ?? '';
            $trade_name = $POST["trade_name"];
            $legal_name = $POST['legal_name'] ?? '';
            $constitution_of_business = $POST["con_business"];

            $customer_authorised_person_name = $POST["customer_authorised_person_name"];
            $customer_authorised_person_designation = $POST["customer_authorised_person_designation"];
            $customer_authorised_person_phone = $POST["customer_authorised_person_phone"];
            $customer_authorised_alt_phone = $POST["customer_authorised_alt_phone"];
            $customer_authorised_person_email = $POST["customer_authorised_person_email"];
            $customer_authorised_alt_email = $POST["customer_authorised_alt_email"];

            // other address
            $state = $POST["state"];
            if (empty($customer_gstin) || $customer_gstin == '') {


                //echo 1;
                $get_code =  queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateName` = '" . $state . "'");
                //   console($get_code);
                if ($get_code['numRows'] > 0) {
                    // console($get_code);
                    $state_code = $get_code['data']['gstStateCode'];
                } else {
                    $state_code = '';
                }
            } else {
                // echo 0;
                $state_code = substr($customer_gstin, 0, 2);
            }
            //  exit();


            $country = $POST["countries"] ?? 'India';
            $city = $POST["city"];
            $district = $POST["district"];
            $location = $POST["location"];
            $build_no = $POST["build_no"];
            $flat_no = $POST["flat_no"];
            $street_name = $POST["street_name"];
            $pincode = $POST["pincode"];
            // $discount_group = $POST['discount_group'] ?? 0;
            // $customer_mrp_group = $POST['customer_mrp_group'] ?? 0;
            $discount_group = !empty($POST['discount_group']) ? $POST['discount_group'] : 0;
            $customer_mrp_group = !empty($POST['customer_mrp_group']) ? $POST['customer_mrp_group'] : 0;


            // accounting
            $opening_balance = $POST["opening_balance"] ?? 0;
            $currency = $POST["currency"];
            $credit_period = $POST["credit_period"];

            // $customer_picture = $POST["customer_picture"];
            $customer_visible_to_all = $POST["customer_visible_to_all"];
            //$adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]); 

            $sql = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE company_id=$company_id AND `customer_code`='" . $customer_code . "' ";

            if ($res = mysqli_query($dbCon, $sql)) {

                if (mysqli_num_rows($res) == 0) {
                    // console($POST);
                    $otp = rand(1000, 9999);
                    $insCustomer = "INSERT INTO `" . ERP_CUSTOMER . "` 
                            SET
                                `company_id`='" . $company_id . "',
                                `company_branch_id`='" . $branch_id . "',
                                `location_id`='" . $location_id . "',
                                `parentGlId`='" . $parentGlId . "',
                                `customer_code`='" . $customer_code . "',
                                `customer_pan`='" . $customer_pan . "',
                                `customer_gstin`='" . $customer_gstin . "',
                                `trade_name`='" . $trade_name . "',
                                `legal_name`='" . $legal_name . "',
                                `customer_opening_balance`='$opening_balance',
                                `customer_currency`='$currency',
                                `customer_credit_period`='$credit_period',
                                `constitution_of_business`='" . $constitution_of_business . "',
                                `customer_authorised_person_name`='" . $customer_authorised_person_name . "',
                                `customer_authorised_person_designation`='" . $customer_authorised_person_designation . "',
                                `customer_authorised_person_phone`='" . $customer_authorised_person_phone . "',
                                `customer_authorised_alt_phone`='" . $customer_authorised_alt_phone . "',
                                `customer_authorised_person_email`='" . $customer_authorised_person_email . "',
                                `customer_authorised_alt_email`='" . $customer_authorised_alt_email . "',
                                `customer_visible_to_all`='" . $customer_visible_to_all . "',
                                `customer_created_by`='" . $created_by . "',
                                `customer_updated_by`='" . $created_by . "',
                                `customer_discount_group` = '" . $discount_group . "',
                                `customer_mrp_group` = '" . $customer_mrp_group . "',
                                `mailValidationOtp` = '" . $otp . "',
                                `customer_status`='" . $customer_status . "',
                                `mail_send_status` = '" . $mail_send_status . "'";

                    $customerreturn = queryInsert($insCustomer);
                    if ($customerreturn['status'] == 'success') {

                        $customerId = $customerreturn['insertedId'];
                        $admin["customer_id"] = $customerId;
                        $admin["customer_code"] = $customer_code;
                        $adminRole = 1;


                        ///---------------------------------Audit Log Start---------------------
                        $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                        $auditTrail = array();
                        $mrp_group=queryGet("SELECT * FROM `erp_customer_mrp_group` WHERE `customer_mrp_group_id`=$customer_mrp_group")['data'];
                        $discount_group_qry=queryGet("SELECT * FROM `erp_customer_discount_group` WHERE `customer_discount_group_id`=$discount_group")['data'];

                        $currencyName = getSingleCurrencyType($currency);
                        $created_byName = getCreatedByUser($created_by);
                        $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                        $auditTrail['basicDetail']['table_name'] = ERP_CUSTOMER;
                        $auditTrail['basicDetail']['column_name'] = 'customer_id'; // Primary key column
                        $auditTrail['basicDetail']['document_id'] = $customerId;  // primary key
                        $auditTrail['basicDetail']['party_type'] = 'customer';
                        $auditTrail['basicDetail']['party_id'] = $customerId;
                        $auditTrail['basicDetail']['document_number'] = $customer_code;
                        $auditTrail['basicDetail']['action_code'] = $action_code;
                        $auditTrail['basicDetail']['action_referance'] = '';
                        $auditTrail['basicDetail']['action_title'] = 'New Customer added';  //Action comment
                        $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                        $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                        $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                        $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                        $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insCustomer);
                        $auditTrail['basicDetail']['others'] = '';
                        $auditTrail['basicDetail']['remark'] = '';

                        $auditTrail['action_data']['Customer Detail']['customer_code'] = $customer_code;
                        $auditTrail['action_data']['Customer Detail']['customer_' . $taxNumber] = $customer_pan;
                        $auditTrail['action_data']['Customer Detail']['customer_' . $businessTaxID] = $customer_gstin;
                        $auditTrail['action_data']['Customer Detail']['trade_name'] = $trade_name;
                        $auditTrail['action_data']['Customer Detail']['customer_currency'] = $currencyName;
                        $auditTrail['action_data']['Customer Detail']['customer_credit_period'] = $credit_period;
                        $auditTrail['action_data']['Customer Detail']['constitution_of_business'] = $constitution_of_business;
                        $auditTrail['action_data']['Customer Detail']['customer_authorised_person_name'] = $customer_authorised_person_name;
                        $auditTrail['action_data']['Customer Detail']['customer_authorised_person_designation'] = $customer_authorised_person_designation;
                        $auditTrail['action_data']['Customer Detail']['customer_authorised_person_phone'] = $customer_authorised_person_phone;
                        $auditTrail['action_data']['Customer Detail']['customer_authorised_alt_phone'] = $customer_authorised_alt_phone;
                        $auditTrail['action_data']['Customer Detail']['customer_authorised_person_email'] = $customer_authorised_person_email;
                        $auditTrail['action_data']['Customer Detail']['customer_authorised_alt_email'] = $customer_authorised_alt_email;
                        $auditTrail['action_data']['Customer Detail']['customer_visible_to_all'] = $customer_visible_to_all;
                        $auditTrail['action_data']['Customer Detail']['customer_mrp_group'] = $mrp_group['customer_mrp_group'];
                        $auditTrail['action_data']['Customer Detail']['discount_group'] = $discount_group_qry['customer_discount_group'];
                        $auditTrail['action_data']['Customer Detail']['customer_created_by'] = $created_byName;
                        $auditTrail['action_data']['Customer Detail']['customer_updated_by'] = $created_byName;
                        $auditTrail['action_data']['Customer Detail']['customer_status'] = $customer_status;

                        // $data = [
                        //     "date" => date('Y-m-d'),
                        //     "gl" => $parentGlId,
                        //     "subgl" => $customer_code,
                        //     "closing_qty" => 0,
                        //     "closing_val" => $opening_balance
                        // ];
                        // addOpeningBalanceForGlSubGl($data);

                        // insert to admin details
                        //addNewAdministratorUserGlobal($admin);
                        $insCustomerAdmin = "INSERT INTO `" . $admin['tablename'] . "`
                        SET
                            `fldAdminName`='" . $admin['adminName'] . "',
                            `fldAdminEmail`='" . $admin['adminEmail'] . "',
                            `fldAdminPassword`='" . $admin['adminPassword'] . "',
                            `fldAdminPhone`='" . $admin['adminPhone'] . "', 
                            `customer_code`='" . $customer_code . "',
                            `company_id`='" . $company_id . "',
                            `customer_id`='" . $customerId . "',
                            `fldAdminRole`='" . $adminRole . "'";
                        queryInsert($insCustomerAdmin);
                        // insert to ERP_CUSTOMER_ADDRESS from basic details
                        $insCustomerAdd = "INSERT INTO `" . ERP_CUSTOMER_ADDRESS . "`
                         SET 
                            `customer_id`='$customerId',
                            `customer_address_primary_flag`='1',
                            `customer_address_building_no`='$build_no',
                            `customer_address_flat_no`='$flat_no',
                            `customer_address_street_name`='$street_name',
                            `customer_address_pin_code`='$pincode',
                            `customer_address_location`='$location',
                            `customer_address_city`='$city',
                            `customer_address_district`='$district',
                            `customer_address_country`='$country',
                            `customer_address_state`='$state',
                            `customer_address_state_code`=$state_code,
                            `customer_address_created_by`='$created_by',
                            `customer_address_updated_by`='$created_by' 
                            ";

                        mysqli_query($dbCon, $insCustomerAdd);
                        // exit();

                        /* $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['customer_gl']);
                        $accounts['p_id'] = $paccdetails['data']['id'];
                        $accounts['personal_glcode_lvl'] = $paccdetails['data']['lvl'];
                        $accounts['typeAcc'] = $paccdetails['data']['typeAcc'];
                        $accounts['gl_code'] = $customer_code;
                        $accounts['company_id'] = $company_id;
                        $accounts['gl_label'] = $trade_name;
                        $accounts['glSt'] = 'last';
                        $accounts['created_by'] = $created_by;
                        $accounts['updated_by'] = $created_by;
                        //createDataChartOfAccounts($accounts);*/
                        $mailstatus = 'not send';
                        if ($customer_status == "active") {
                            $encode_customer_id = base64_encode($customerId);
                            $encode_company_id = base64_encode($company_id);
                            $sub = "Welcome to $companyNameNav";
                            $msg = "Dear $trade_name,<br>			
                        We are thrilled to welcome you as a new customer partner with $companyNameNav. We are confident that our partnership will bring about great results for both our companies.<br>    
                        To ensure a smooth onboarding process, please find below some important information that will be helpful to you:<br><br>
                        <b>Our team:</b> Our procurement team is available to assist you with any questions or concerns you may have. Please do not hesitate to reach out to us for any support.<br>			
                        <b>Customer Portal:</b> You will receive separate emails with your login information for $companyNameNav's customer portal. This is where you can manage your invoices, purchase orders, and other important information.<br>			
                        <b>Communication channels:</b> To ensure seamless communication, we have set up dedicated email addresses and phone numbers for our customer partners.<br>			
                        <b>Expectations and guidelines:</b> We have outlined our expectations and guidelines for our customer partners in a detailed document, which we will send to you shortly.<br>	
                        If there is anything else we can do to help, please do not hesitate to contact us. We are here to support you and ensure that our partnership is a successful one.<br>			
                        <br> 
                        <b>Company Code: </b>" . $companyCodeNav . "<br>
                        <b>customer Code: </b>" . $customer_code . "<br>          
                        Thank you for choosing  $companyNameNav, and we look forward to working with you.<br>
                        To validate your mail, <a href='" . BASE_URL . "branch/location/mailVarification_customer.php?id=$encode_customer_id&c_id=$encode_company_id'>Click Here</a><br><br>
                        Your OTP for partner app mail validation is $otp<br><br>
                    
                        Best regards,  $companyNameNav";

                            $mail = SendMailByMySMTPmailTemplate($customer_authorised_person_email, $sub, $msg, null, $customer_code, 'customerAdd', $customerId, $customer_code);
                            $mailstatus = $mail ? 'success' : 'Not send';
                            global $current_userName;
                            $whatsapparray = [];
                            $whatsapparray['templatename'] = 'customer_onboard_msg';
                            $whatsapparray['to'] = $customer_authorised_person_phone;
                            $whatsapparray['customername'] = $trade_name;
                            $whatsapparray['companyname'] = $companyNameNav;
                            $whatsapparray['companyCodeNav'] = $companyCodeNav;
                            $whatsapparray['customer_code'] = $customer_code;
                            $whatsapparray['password'] = $POST["adminPassword"];
                            $whatsapparray['quickcontact'] = null;
                            $whatsapparray['current_userName'] = $current_userName;
                            $whatsapparray['user_designation'] = 'Admin';

                            $whatsreturn = SendMessageByWhatsappTemplate($whatsapparray);
                        }



                        //$auditTrail['action_data']['po items'][$code]['name']=$itemName;

                        // $auditTrail['action_data']['Customer Address'][$district.' ('.$pincode.')']['customer_address_primary_flag']=1;
                        $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_building_no'] = $build_no;
                        $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_flat_no'] = $flat_no;
                        $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_street_name'] = $street_name;
                        $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_pin_code'] = $pincode;
                        $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_location'] = $location;
                        $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_city'] = $city;
                        $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_district'] = $district;
                        $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_state'] = $state;
                        $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_created_by'] = $created_byName;
                        $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_updated_by'] = $created_byName;


                        $auditTrail['action_data']['Mail-Send']['send-status'] = $mailstatus;
                        $auditTrail['action_data']['Mail Verification']['Verification-status'] = 'Not Verified';

                        $auditTrailreturn = generateAuditTrail($auditTrail);

                        ///---------------------------------Audit Log Start---------------------

                        $returnData['status'] = "success";
                        $returnData['message'] = "Customer added successfully";
                        $returnData['whatsreturn'] = $whatsreturn;
                        $returnData['insCustomerAdd'] = $insCustomerAdd;
                        // $returnData['insCustomer'] = $insCustomer;
                        // $returnData['auditTrail'] = $auditTrail;
                        // $returnData['auditTrailreturn'] = $auditTrailreturn;
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "Customer added failed";
                        $returnData['sql'] = $insCustomer;
                    }
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Customer already exist";
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
function updateDataCustomer($POST)
{
    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
    global $companyCountry;
    global $companyNameNav;
    $returnData = [];
    $componentsjsn = json_decode(getLebels($companyCountry)['data'], true);
    $businessTaxID = $componentsjsn['fields']['businessTaxID'];
    $taxNumber = $componentsjsn['fields']['taxNumber'];
    //  console($POST);
    // exit();

    // POC details
    $customer_authorised_person_name = $_POST["customer_authorised_person_name"];
    $customer_authorised_person_designation = $_POST["customer_authorised_person_designation"];
    $customer_authorised_person_phone = $_POST["customer_authorised_person_phone"];
    $customer_authorised_alt_phone = $_POST["customer_authorised_alt_phone"];
    $customer_authorised_person_email = $_POST["customer_authorised_person_email"];
    $customer_authorised_alt_email = $_POST["customer_authorised_alt_email"];
    $customer_id = $_POST['customer_id'];
    $customer_code = $_POST['customer_code'];
    $customer_pan = $_POST['customer_pan'];
    $customer_gstin = $_POST['customer_gstin'] ?? ' ';
    $trade_name = $_POST['trade_name'];
    $legal_name = $_POST['legal_name'];
    $customer_visible_to_all = $_POST['customer_visible_to_all'];
    $constitution_of_business = $_POST['constitution_of_business'];
    // other address
    $state = $_POST["state"];
    $country = $_POST['countries'] ?? 'India';
    $city = $_POST["city"];
    $district = $_POST["district"];
    $location = $_POST["location"];
    $build_no = $_POST["build_no"];
    $flat_no = $_POST["flat_no"];
    $street_name = $_POST["street_name"];
    $pincode = $_POST["pincode"];

    $discount_group = $POST['discount_group'];
    $customer_mrp_group = $POST['customer_mrp_group'];
    $billToCheckbox= $POST['billToCheckbox'];
    $adminPassword=$POST['adminPassword'];


    // $address = $_POST['customerOtherAddress'];

    // accounting
    $opening_balance = $_POST["opening_balance"];
    $currency = $_POST["currency"];
    $credit_period = $_POST["credit_period"];


    if (empty($customer_gstin) || $customer_gstin == '') {
        //echo 1;
        $get_code =  queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateName` = '" . $state . "'");
        //  console($get_code);
        if ($get_code['numRows'] > 0) {
            //console($get_code);
            $state_code = $get_code['data']['gstStateCode'];
        } else {
            // echo 0;
            $state_code = 0;
        }
    } else {
        $state_code = substr($customer_gstin, 0, 2);
    }




    $upd = "UPDATE `erp_customer` 
                  SET 
                    `customer_code`=$customer_code,
                    `customer_pan`='" . $customer_pan . "',
                    `customer_gstin`='" . $customer_gstin . "',
                    `trade_name`='" . addslashes($trade_name) . "',
                    `legal_name` = '" . addslashes($legal_name) . "',
                    `customer_visible_to_all`='" . $customer_visible_to_all . "',
                    `constitution_of_business`='" . $constitution_of_business . "',
                    `customer_authorised_person_name`='" . addslashes($customer_authorised_person_name) . "',
                    `customer_authorised_person_designation`='" . addslashes($customer_authorised_person_designation) . "',
                    `customer_authorised_person_email`='" . addslashes($customer_authorised_person_email) . "',
                    `customer_authorised_alt_email`='" . addslashes($customer_authorised_alt_email) . "',
                    `customer_authorised_person_phone`=$customer_authorised_person_phone,
                    `customer_authorised_alt_phone`='$customer_authorised_alt_phone',
                    `customer_currency` = '" . $currency . "',
                    `customer_opening_balance`='" . $opening_balance . "',
                    `customer_credit_period`='$credit_period',
                    `customer_discount_group` = '" . $discount_group . "',
                    `customer_mrp_group` = '" . $customer_mrp_group . "',
                    `customer_updated_by`='" . $updated_by . "'
                     WHERE `customer_id`=$customer_id";


    $returnData_update =  queryUpdate($upd);
    if ($returnData_update["status"] == "success") {
        $check_data = queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_id` = $customer_id");
        //console($check_data);
        if ($check_data['numRows'] > 0) {
            //echo 0;
            // exit();
            $upd_address = "UPDATE `erp_customer_address` 
                SET
                `customer_address_state`='" . $state . "',
                `customer_address_state_code`=$state_code,
                 `customer_address_country`='" . $country . "',
                `customer_address_city`='" . $city . "',
                `customer_address_district`='" . addslashes($district) . "',
                `customer_address_location`='" . addslashes($location) . "',
                `customer_address_building_no`='" . addslashes($build_no) . "',
                `customer_address_flat_no`='" . addslashes($flat_no) . "',
                `customer_address_street_name`='" . addslashes($street_name) . "',
                `customer_address_pin_code`='" . $pincode . "'
                WHERE `customer_id`='" . $customer_id . "'";

            $returnData_update_address =  queryUpdate($upd_address);
        } else {
            //echo 1;

            $upd_address = "INSERT INTO `erp_customer_address` 
                SET
                `customer_id`='" . $customer_id . "',
                `customer_address_state`='" . $state . "',
                `customer_address_state_code`=$state_code,
                 `customer_address_country`='" . $country . "',
                `customer_address_city`='" . $city . "',
                `customer_address_district`='" . addslashes($district) . "',
                `customer_address_location`='" . addslashes($location) . "',
                `customer_address_building_no`='" . addslashes($build_no) . "',
                `customer_address_flat_no`='" . addslashes($flat_no) . "',
                `customer_address_street_name`='" . addslashes($street_name) . "',
                `customer_address_primary_flag` = 1,
                `customer_address_pin_code`='" . $pincode . "'";

            $returnData_update_address =  queryUpdate($upd_address);
        }
        //console($returnData_update_address);
    }
    if($billToCheckbox!='on'){
        $sqlupdate="UPDATE `tbl_customer_admin_details` SET `fldAdminPassword` = '$adminPassword' WHERE  `customer_id` = $customer_id";
        $returnData_update_password =  queryUpdate($sqlupdate);
                            $sub = "Password Changed";
                            $msg = "Dear $trade_name,<br>
                            Your password has been changed.<br>
                            <b>New password is : </b> $adminPassword <br>
                            <b>Customer Code :</b> $customer_code <br>
                            Best regards,  $companyNameNav";

        $mail = SendMailByMySMTPmailTemplate($customer_authorised_person_email, $sub, $msg, null, $customer_code, 'passwordchanged', $customer_id, $customer_code);
                        
    }

    // console($returnData_update);
    // exit();
    ///---------------------------------Audit Log Start---------------------
    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
    $auditTrail = array();
    $currencyName = getSingleCurrencyType($currency);
    $updated_byName = getCreatedByUser($updated_by);
    $sql = queryGet("SELECT * FROM `erp_customer` WHERE customer_id=" . $customer_id . "")["data"];
    $status=$sql["customer_status"];
    $created_byName = getCreatedByUser($sql["customer_created_by"]);
    $isMailValid = $sql["isMailValid"];
    $mail_send_status = $sql["mail_send_status"];

    $auditTrail['basicDetail']['trail_type'] = 'EDIT';   //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'	 //	Add/Update/Deleted
    $auditTrail['basicDetail']['table_name'] = ERP_CUSTOMER;
    $auditTrail['basicDetail']['column_name'] = 'customer_id';  //Primary Key column
    $auditTrail['basicDetail']['document_id'] = $customer_id;   // Primary Key
    $auditTrail['basicDetail']['party_type'] = 'customer';
    $auditTrail['basicDetail']['party_id'] = $customer_id;
    $auditTrail['basicDetail']['document_number'] = $customer_code;
    $auditTrail['basicDetail']['action_code'] = $action_code;
    $auditTrail['basicDetail']['action_referance'] = '';
    $auditTrail['basicDetail']['action_title'] = 'Customer Update';   // Action comment
    $auditTrail['basicDetail']['action_name'] = 'Update';   //	Add/Update/Deleted
    $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
    $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
    $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
    $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($upd_address);
    $auditTrail['basicDetail']['others'] = '';
    $auditTrail['basicDetail']['remark'] = '';

    $auditTrail['action_data']['Customer Detail']['customer_code'] = $customer_code;
    $auditTrail['action_data']['Customer Detail']['customer_' . $taxNumber] = $customer_pan;
    $auditTrail['action_data']['Customer Detail']['customer_' . $businessTaxID] = $customer_gstin;
    $auditTrail['action_data']['Customer Detail']['trade_name'] = $trade_name;
    $auditTrail['action_data']['Customer Detail']['customer_currency'] = $currencyName;
    $auditTrail['action_data']['Customer Detail']['customer_credit_period'] = $credit_period;
    $auditTrail['action_data']['Customer Detail']['constitution_of_business'] = $constitution_of_business;
    $auditTrail['action_data']['Customer Detail']['customer_authorised_person_name'] = $customer_authorised_person_name;
    $auditTrail['action_data']['Customer Detail']['customer_authorised_person_designation'] = $customer_authorised_person_designation;
    $auditTrail['action_data']['Customer Detail']['customer_authorised_person_phone'] = $customer_authorised_person_phone;
    $auditTrail['action_data']['Customer Detail']['customer_authorised_alt_phone'] = $customer_authorised_alt_phone;
    $auditTrail['action_data']['Customer Detail']['customer_authorised_person_email'] = $customer_authorised_person_email;
    $auditTrail['action_data']['Customer Detail']['customer_authorised_alt_email'] = $customer_authorised_alt_email;
    $auditTrail['action_data']['Customer Detail']['customer_visible_to_all'] = $customer_visible_to_all;
    $auditTrail['action_data']['Customer Detail']['customer_mrp_group'] = $customer_mrp_group;
    $auditTrail['action_data']['Customer Detail']['discount_group'] = $discount_group;
    $auditTrail['action_data']['Customer Detail']['customer_created_by'] = $created_byName;
    $auditTrail['action_data']['Customer Detail']['customer_updated_by'] = $updated_byName;
    $auditTrail['action_data']['Customer Detail']['customer_status'] = $status;

    $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_building_no'] = $build_no;
    $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_flat_no'] = $flat_no;
    $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_street_name'] = $street_name;
    $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_pin_code'] = $pincode;
    $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_location'] = $location;
    $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_city'] = $city;
    $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_district'] = $district;
    $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_state'] = $state;
    $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_created_by'] = $created_byName;
    $auditTrail['action_data']['Customer Address'][$district . ' (' . $pincode . ')']['customer_address_updated_by'] = $updated_byName;


    $auditTrail['action_data']['Mail Send']['send-status'] = $mail_send_status == 1 ? 'Success' : 'Not sent';
    $auditTrail['action_data']['Mail Verification']['Verification-status'] = $isMailValid == 'yes' ? 'Verified' : 'Not Verified';


    $auditTrailreturn = generateAuditTrail($auditTrail);

    ///---------------------------------Audit Log End---------------------

    if ($returnData_update_address['status'] == 'success') {
        $returnData['status'] = 'success';
        $returnData['message'] = 'Customer Updated Successfully';
    } else {
        $returnData['status'] = 'warning';
        $returnData['message'] = 'Something Went wrong';
        $returnData['returnData_update_address'] = $returnData_update_address;
    }
    return $returnData;
}

//*************************************/SELECT ALL/******************************************//
function getAllDataCustomer()
{
    global $dbCon;
    $returnData = [];
    $sql = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE `status`!='deleted'";
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
    $sql = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE `status`!='deleted' AND `fldRoleKey`=" . $key . "";
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
function ChangeStatusCustomer($data = [], $tableKeyField = "", $tableStatusField = "status")
{
    global $dbCon;
    global $current_userName;
    global $companyNameNav;
    global $companyCodeNav;
    global $company_id;
    global $updated_by;
    global $companyCountry;
    $componentsjsn = json_decode(getLebels($companyCountry)['data'], true);
    $businessTaxID = $componentsjsn['fields']['businessTaxID'];
    $taxNumber = $componentsjsn['fields']['taxNumber'];
    $tableName = ERP_CUSTOMER;
    $returnData["status"] = null;
    $returnData["message"] = null;
    if (!empty($data)) {
        $id = isset($data["id"]) ? $data["id"] : 0;
        $prevSql = "SELECT * FROM `" . $tableName . "` WHERE `" . $tableKeyField . "`='" . $id . "'";
        $prevExeQuery = mysqli_query($dbCon, $prevSql);
        $cust_details = queryGet($prevSql);
        $mail = $cust_details['data']['mail_send_status'];
        $mail_send_status=$mail== 1 ? 'Success' : 'Not sent';
        $prevNumRecords = mysqli_num_rows($prevExeQuery);
        if ($prevNumRecords > 0) {
            $prevData = mysqli_fetch_assoc($prevExeQuery);
            $newStatus = "deleted";
            if (isset($data["changeStatus"])) {
                if ($data["changeStatus"] == "active_inactive") {
                    $newStatus = ($prevData[$tableStatusField] == "active") ? "inactive" : "active";
                } elseif ($data["changeStatus"] == "draft_to_active" && $prevData[$tableStatusField] == "draft") {
                    $newStatus = "active";
                }
            }
            if ($prevData[$tableStatusField] == 'draft' && $newStatus == 'active') {

                $changeStatusSql = "UPDATE `" . $tableName . "` SET `" . $tableStatusField . "` = '" . $newStatus . "', `mail_send_status` = '1' WHERE `" . $tableKeyField . "` = " . $id;
            } else {
                $changeStatusSql = "UPDATE `" . $tableName . "` SET `" . $tableStatusField . "`='" . $newStatus . "' WHERE `" . $tableKeyField . "`=" . $id;
            }
            if (mysqli_query($dbCon, $changeStatusSql)) {

                if ($prevData[$tableStatusField] == 'draft' && $newStatus == 'active') {

                    $customer_sql = "SELECT erp_customer.*, tbl_customer_admin_details.fldAdminPassword 
                                        FROM erp_customer 
                                        LEFT JOIN tbl_customer_admin_details 
                                        ON erp_customer.customer_id = tbl_customer_admin_details.customer_id 
                                        WHERE erp_customer.customer_id = $id";;
                    $customer_data = queryGet($customer_sql)['data'];

                    $customer_authorised_person_phone = $customer_data['customer_authorised_person_phone'];
                    $trade_name = $customer_data['trade_name'];
                    $customer_code = $customer_data['customer_code'];
                    $otp = $customer_data['mailValidationOtp'];
                    $customer_authorised_person_email = $customer_data['customer_authorised_person_email'];
                    $adminPassword = $customer_data['fldAdminPassword'];

                    $whatsapparray = [];
                    $whatsapparray['templatename'] = 'customer_onboard_msg';
                    $whatsapparray['to'] = $customer_authorised_person_phone;
                    $whatsapparray['customername'] = $trade_name;
                    $whatsapparray['companyname'] = $companyNameNav;
                    $whatsapparray['companyCodeNav'] = $companyCodeNav;
                    $whatsapparray['customer_code'] = $customer_code;
                    $whatsapparray['password'] = $adminPassword;
                    $whatsapparray['quickcontact'] = null;
                    $whatsapparray['current_userName'] = $current_userName;
                    $whatsapparray['user_designation'] = 'Admin';

                    $whatsreturn = SendMessageByWhatsappTemplate($whatsapparray);

                    $sub = "Welcome to $companyNameNav";

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
                        <b>Vendor Code: </b>" . $customer_code . "<br>          
                        Thank you for choosing  $companyNameNav, and we look forward to working with you.<br>
                        To validate your mail, <a href='" . BASE_URL . "branch/location/mailVarification_customer.php?id=$id&company_id=$company_id'>Click Here</a><br><br>
                        Your OTP for partner app mail validation is $otp<br><br>
                    
                        Best regards,  $companyNameNav";

                    $mail = SendMailByMySMTPmailTemplate($customer_authorised_person_email, $sub, $msg, null, $customer_code, 'customerAdd', $id, $customer_code);
                    $mail_send_status = $mail ? 'success' : 'Not send';
                }

                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $customer_code = $cust_details["data"]["customer_code"];
                $customer_pan = $cust_details["data"]["customer_pan"];
                $customer_gstin = $cust_details["data"]["customer_gstin"];
                $trade_name = $cust_details["data"]["trade_name"];
                $currency = $cust_details["data"]["customer_currency"];
                $credit_period = $cust_details["data"]["customer_credit_period"];
                $constitution_of_business = $cust_details["data"]["constitution_of_business"];
                $customer_authorised_person_name = $cust_details["data"]["customer_authorised_person_name"];
                $customer_authorised_person_designation = $cust_details["data"]["customer_authorised_person_designation"];
                $customer_authorised_person_phone = $cust_details["data"]["customer_authorised_person_phone"];
                $customer_authorised_alt_phone = $cust_details["data"]["customer_authorised_alt_phone"];
                $customer_authorised_person_email = $cust_details["data"]["customer_authorised_person_email"];
                $customer_authorised_alt_email=$cust_details["data"]["customer_authorised_alt_email"];
                $customer_visible_to_all = $cust_details["data"]["customer_visible_to_all"];
                $created_by = $cust_details["data"]["customer_created_by"];
                $currencyName = getSingleCurrencyType($currency);
                $created_byName = getCreatedByUser($created_by);
                $updated_by=getCreatedByUser($updated_by);
                $isMailValid=$cust_details["data"]["isMailValid"];
                $discount_group=$cust_details["data"]["customer_discount_group"];
                $customer_mrp_group=$cust_details["data"]["customer_mrp_group"];

                $auditTrail['basicDetail']['trail_type'] = 'EDIT';   //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'	 //	Add/Update/Deleted
                $auditTrail['basicDetail']['table_name'] = ERP_CUSTOMER;
                $auditTrail['basicDetail']['column_name'] = 'customer_id';  //Primary Key column
                $auditTrail['basicDetail']['document_id'] = $id;   // Primary Key
                $auditTrail['basicDetail']['party_type'] = 'customer';
                $auditTrail['basicDetail']['party_id'] = $id;
                $auditTrail['basicDetail']['document_number'] = $customer_code;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = 'Customer Status Change';   // Action comment
                $auditTrail['basicDetail']['action_name'] = 'Update';   //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($changeStatusSql);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';

                $auditTrail['action_data']['Customer Detail']['customer_code'] = $customer_code;
                $auditTrail['action_data']['Customer Detail']['customer_' . $taxNumber] = $customer_pan;
                $auditTrail['action_data']['Customer Detail']['customer_' . $businessTaxID] = $customer_gstin;
                $auditTrail['action_data']['Customer Detail']['trade_name'] = $trade_name;
                $auditTrail['action_data']['Customer Detail']['customer_currency'] = $currencyName;
                $auditTrail['action_data']['Customer Detail']['customer_credit_period'] = $credit_period;
                $auditTrail['action_data']['Customer Detail']['constitution_of_business'] = $constitution_of_business;
                $auditTrail['action_data']['Customer Detail']['customer_authorised_person_name'] = $customer_authorised_person_name;
                $auditTrail['action_data']['Customer Detail']['customer_authorised_person_designation'] = $customer_authorised_person_designation;
                $auditTrail['action_data']['Customer Detail']['customer_authorised_person_phone'] = $customer_authorised_person_phone;
                $auditTrail['action_data']['Customer Detail']['customer_authorised_alt_phone'] = $customer_authorised_alt_phone;
                $auditTrail['action_data']['Customer Detail']['customer_authorised_person_email'] = $customer_authorised_person_email;
                $auditTrail['action_data']['Customer Detail']['customer_authorised_alt_email'] = $customer_authorised_alt_email;
                $auditTrail['action_data']['Customer Detail']['customer_visible_to_all'] = $customer_visible_to_all;
                $auditTrail['action_data']['Customer Detail']['customer_mrp_group'] = $customer_mrp_group;
                $auditTrail['action_data']['Customer Detail']['discount_group'] = $discount_group;
                $auditTrail['action_data']['Customer Detail']['customer_created_by'] = $created_byName;
                $auditTrail['action_data']['Customer Detail']['customer_updated_by'] = $updated_by;
                $auditTrail['action_data']['Customer Detail']['customer_status'] = $newStatus;

                $auditTrail['action_data']['Mail-Send']['send-status'] = $mail_send_status;
                $auditTrail['action_data']['Mail Verification']['Verification-status'] = $isMailValid == 'yes' ? 'Verified' : 'Not Verified';

                $auditTrailreturn = generateAuditTrail($auditTrail);
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
