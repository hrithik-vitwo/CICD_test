<?php
    include("../../../app/v1/functions/common/func-common.php");
    require_once("api-common-func.php");


    class CustomersController
    {
    function createCustomer($POST_DATA = [],$company_id,$branch_id,$location_id,$user_id,$declaration = 0)
    {
        $created_by = $user_id."|company";
        $updated_by = $user_id."|company";
        $returnData = [];
        $i = 0;
        $flag = [];
        $error_flag = 0;

        $companies_name = queryGet("SELECT * FROM `erp_companies` WHERE `company_id` = '" . $company_id . "'",false);

        $companyCodeNav = $companies_name["data"]["company_code"];
        $companyNameNav = $companies_name["data"]["company_name"];

        foreach($POST_DATA as $POST)
        {
            // global $dbCon;
            // global $companyCodeNav;
            // global $companyNameNav;
            
            $customer_pan = $POST["customer_pan"]??'';
            $trade_name = addslashes($POST["trade_name"]) == "" ? addslashes($POST["legal_name"]) : addslashes($POST["trade_name"]);
            $legal_name = addslashes($POST["legal_name"]) == "" ? addslashes($POST["trade_name"]) : addslashes($POST["legal_name"]);
            // if($customer_pan == "" || $customer_pan == NULL)
            // {
            //     $flag[] = array("status"=>"warning","message"=>"Customer added failed at line ".$i);
            //     continue;
            // }

            $customer_pan_checking = queryGet("SELECT * FROM `erp_customer` WHERE `company_id` = '" . $company_id . "' AND `trade_name`='".$trade_name."'",false);

            if($customer_pan_checking["numRows"] != 0)
            {
                $flag[] = array("status"=>"warning","message"=>"Customer already added at line ".$i);
                $error_flag++;
                $i++;
                continue;
            }

            $returnData = [];
            $POST["company_id"] = $company_id;
            $POST["company_branch_id"] = $branch_id;
            
                $accMapp = getAllfetchAccountingMappingTbl($company_id);
                // console($accMapp);
                if ($accMapp["status"] == "success") {
                    $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['customer_gl'],$company_id);
                    $parentGlId= $paccdetails['data']['id'];
                    $admin = array();
                    $admin["adminName"] = $POST["customer_authorised_person_name"];
                    $admin["adminEmail"] = $POST["customer_authorised_person_email"];
                    $admin["adminPhone"] = $POST["customer_authorised_person_phone"];
                    $admin["adminPassword"] = $POST["adminPassword"];
                    $admin["tablename"] = 'tbl_customer_admin_details';
                    $admin["adminPassword"] = $POST["adminPassword"];
                    $admin["fldAdminCompanyId"] = $POST["company_id"];
                    $admin["fldAdminBranchId"] = $POST["company_branch_id"];

                // if (isset($POST["createdata"]) && $POST["createdata"] == 'add_post') {
                        $customer_status = 'active';
                /* } else {
                        $customer_status = 'draft';
                    }*/
                    $lastlQuery = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE `company_id` = '" . $POST["company_id"] . "'  ORDER BY `customer_id` DESC LIMIT 1";
                    $resultLast = queryGet($lastlQuery);
                    $rowLast = $resultLast["data"];
                    $lastsl = $rowLast['customer_code'];
                    //
                    $company_id = $POST["company_id"];
                    $company_branch_id = $POST["company_branch_id"];
                    $customer_code = getCustomerSerialNumber($lastsl);
                    $customer_gstin = $POST["customer_gstin"]??'';
                   
                    $constitution_of_business = $POST["con_business"];

                    $customer_authorised_person_name = $POST["customer_authorised_person_name"];
                    $customer_authorised_person_designation = $POST["customer_authorised_person_designation"];
                    $customer_authorised_person_phone = $POST["customer_authorised_person_phone"];
                    $customer_authorised_alt_phone = $POST["customer_authorised_alt_phone"];
                    $customer_authorised_person_email = $POST["customer_authorised_person_email"];
                    $customer_authorised_alt_email = $POST["customer_authorised_alt_email"];

                    // other address
                    $state = $POST["state"] ?? "";
                    $city = $POST["city"] ?? "";
                    $district = $POST["district"] ?? "";
                    $location = $POST["location"] ?? "";
                    $build_no = $POST["build_no"] ?? "";
                    $flat_no = $POST["flat_no"] ?? "";
                    $street_name = $POST["street_name"] ?? "";
                    $pincode = $POST["pincode"] ?? "";

                    // accounting
                    $opening_balance = $POST["opening_balance"] ?? 0;
                    $currency = $POST["currency"] ?? "";
                    $credit_period = $POST["credit_period"] ?? 1;

                    // $customer_picture = $POST["customer_picture"];
                    $customer_visible_to_all = $POST["customer_visible_to_all"];
                    //$adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]); 

                    $sql = queryGet("SELECT * FROM `" . ERP_CUSTOMER . "` WHERE company_id=$company_id AND `customer_code`='" . $customer_code . "' ");

                        if ($sql["numRows"] == 0) {
                            // console($POST);
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
                                        `customer_status`='" . $customer_status . "',
                                        `mail_send_status`='0'";
                            $customerreturn=queryInsert($insCustomer);
                            if ($customerreturn['status'] == 'success') {
                                
                                $customerId = $customerreturn['insertedId'];
                                $admin["customer_id"] = $customerId;
                                $admin["customer_code"] = $customer_code;
                                $adminRole=1;

                                
                                ///---------------------------------Audit Log Start---------------------
                                $action_code=time().rand(11,99).rand(11,99).rand(11,9999);
                                $auditTrail = array();
                                $auditTrail['basicDetail']['trail_type']='ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                                $auditTrail['basicDetail']['table_name']=ERP_CUSTOMER;
                                $auditTrail['basicDetail']['column_name']='customer_id'; // Primary key column
                                $auditTrail['basicDetail']['document_id']=$customerId;  // primary key
                                $auditTrail['basicDetail']['document_number']=$customer_code;
                                $auditTrail['basicDetail']['action_code']=$action_code;
                                $auditTrail['basicDetail']['action_referance']='';
                                $auditTrail['basicDetail']['action_title']='New Customer added';  //Action comment
                                $auditTrail['basicDetail']['action_name']='Add';	 //	Add/Update/Deleted
                                $auditTrail['basicDetail']['action_type']='Non-Monitory'; //Monitory/Non-Monitory
                                $auditTrail['basicDetail']['action_url']=BASE_URL.$_SERVER['REQUEST_URI'];
                                $auditTrail['basicDetail']['action_previous_url']=$_SERVER['HTTP_REFERER'];
                                $auditTrail['basicDetail']['action_sqlQuery']=base64_encode($insCustomer);                        
                                $auditTrail['basicDetail']['others']='';           
                                $auditTrail['basicDetail']['remark']='';

                                $auditTrail['action_data']['Customer Detail']['customer_code']=$customer_code;
                                $auditTrail['action_data']['Customer Detail']['customer_pan']=$customer_pan;
                                $auditTrail['action_data']['Customer Detail']['customer_gstin']=$customer_gstin;
                                $auditTrail['action_data']['Customer Detail']['trade_name']=$trade_name;
                                $auditTrail['action_data']['Customer Detail']['customer_currency']=$currency;
                                $auditTrail['action_data']['Customer Detail']['customer_credit_period']=$credit_period;
                                $auditTrail['action_data']['Customer Detail']['constitution_of_business']=$constitution_of_business;
                                $auditTrail['action_data']['Customer Detail']['customer_authorised_person_name']=$customer_authorised_person_name;
                                $auditTrail['action_data']['Customer Detail']['customer_authorised_person_designation']=$customer_authorised_person_designation;
                                $auditTrail['action_data']['Customer Detail']['customer_authorised_person_phone']=$customer_authorised_person_phone;
                                $auditTrail['action_data']['Customer Detail']['customer_authorised_alt_phone']=$customer_authorised_alt_phone;
                                $auditTrail['action_data']['Customer Detail']['customer_authorised_person_email']=$customer_authorised_person_email;
                                $auditTrail['action_data']['Customer Detail']['customer_authorised_alt_email']=$customer_authorised_alt_email;
                                $auditTrail['action_data']['Customer Detail']['customer_visible_to_all']=$customer_visible_to_all;
                                $auditTrail['action_data']['Customer Detail']['customer_created_by']=$created_by;
                                $auditTrail['action_data']['Customer Detail']['customer_updated_by']=$created_by;
                                $auditTrail['action_data']['Customer Detail']['customer_status']=$customer_status;

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
                                    `customer_address_state`='$state',
                                    `customer_address_created_by`='$created_by',
                                    `customer_address_updated_by`='$created_by' 
                                    ";
                                    queryInsert($insCustomerAdd);


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

                                // $sub = "Welcome to $companyNameNav";
                                // $msg ="Dear $customer_authorised_person_name,<br>
                                //     We are delighted to welcome you on board as a valued client of $companyNameNav. We are committed to providing you with the best possible service and support, and we look forward to working with you.<br>
                                //     To ensure a smooth onboarding process, please find below some important information that will be helpful to you:<br>
                                //     <b>Our team:</b> Our team is available to assist you with any questions or concerns you may have. Please do not hesitate to reach out to us for any support.<br>
                                //     <b>Login information:</b><br>
                                //     <b>Please click the link to download the App: </b>" . BASE_URL.'api/vitwoai-partner.apk'. "<br>
                                //     <b>Company Code: </b>" . $companyCodeNav . "<br>
                                //     <b>Customer Code: </b>" . $customer_code . "<br>
                                //     <b>Password: </b>" . $POST["adminPassword"] . "<br>
                                //     Resources: We have a range of resources available to help you make the most of our services, including user guides, tutorials, and FAQs.<br>
                                //     Upcoming events: We regularly host webinars, workshops, and other events to help you stay up-to-date with the latest developments in our services.<br>
                                //     If there is anything else we can do to help, please do not hesitate to contact us. We are here to support you and ensure that your experience with $companyNameNav is a positive one.<br>
                                //     Thank you for choosing $companyNameNav, and we look forward to working with you.<br><br>
                                //     Best regards, $companyNameNav";
                                // $mail =  SendMailByMySMTPmailTemplate($customer_authorised_person_email, $sub, $msg);

                                
                                                    
                                //$auditTrail['action_data']['po items'][$code]['name']=$itemName;
                                                    
                                $auditTrail['action_data']['Customer Address'][$district.' ('.$pincode.')']['customer_address_primary_flag']=1;
                                $auditTrail['action_data']['Customer Address'][$district.' ('.$pincode.')']['customer_address_building_no']=$build_no;
                                $auditTrail['action_data']['Customer Address'][$district.' ('.$pincode.')']['customer_address_flat_no']=$flat_no;
                                $auditTrail['action_data']['Customer Address'][$district.' ('.$pincode.')']['customer_address_street_name']=$street_name;
                                $auditTrail['action_data']['Customer Address'][$district.' ('.$pincode.')']['customer_address_pin_code']=$pincode;
                                $auditTrail['action_data']['Customer Address'][$district.' ('.$pincode.')']['customer_address_location']=$location;
                                $auditTrail['action_data']['Customer Address'][$district.' ('.$pincode.')']['customer_address_city']=$city;
                                $auditTrail['action_data']['Customer Address'][$district.' ('.$pincode.')']['customer_address_district']=$district;
                                $auditTrail['action_data']['Customer Address'][$district.' ('.$pincode.')']['customer_address_state']=$state;
                                $auditTrail['action_data']['Customer Address'][$district.' ('.$pincode.')']['customer_address_created_by']=$created_by;
                                $auditTrail['action_data']['Customer Address'][$district.' ('.$pincode.')']['customer_address_updated_by']=$created_by;

                            
                                $auditTrail['action_data']['Mail-Send']['send-status'] = 'success';

                                $auditTrailreturn=generateAuditTrail($auditTrail);
                                
                                ///---------------------------------Audit Log Start---------------------

                                $returnData['status'] = "success";
                                $returnData['message'] = "Customer added successfully";
                                $flag[] = array("status"=>"success","message"=>"Customer added successfully at line ".$i);
                                // $returnData['insCustomer'] = $insCustomer;
                                // $returnData['auditTrail'] = $auditTrail;
                                // $returnData['auditTrailreturn'] = $auditTrailreturn;
                            } else {
                                $returnData['status'] = "warning";
                                $returnData['message'] = "Customer added failed";
                                $flag[] = array("status"=>"warning","message"=>"Customer added failed at line ".$i);
                                $error_flag++;
                            }
                        } else {
                            $returnData['status'] = "warning";
                            $returnData['message'] = "Customer already exist";
                            $flag[] = array("status"=>"warning","message"=>"Customer already exist at line ".$i);
                            $error_flag++;
                        }
                    
                } else {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Setup Your Accounts first!";
                    $flag[] = array("status"=>"warning","message"=>"Setup Your Accounts first! at line ".$i);
                    $error_flag++;
                }
                $i++;
        }

        $total_array = array("flag"=>$flag,"error_flag"=>$error_flag);

        if($declaration == 0)
        {
            $declaration_value = 'unlock';
        }
        else
        {
            $declaration_value = 'lock';
        }

        $insvalidation = "INSERT INTO `erp_migration_validation`
                                SET 
                                    `company_id`='$company_id',
                                    `branch_id`='$branch_id',
                                    `location_id`='$location_id',
                                    `user_id`='$user_id',
                                    `migration_type`='customer',
                                    `declaration`='$declaration_value',
                                    `created_by`='$created_by',
                                    `updated_by`='$created_by' 
                                    ";
                                    queryInsert($insvalidation);

        return $total_array;
    }
    }


?>