<?php
include("../../../app/v1/functions/common/func-common.php");
require_once("api-common-func.php");
class VendorController
{
    function createVendor($POST_DATA = [],$company_id,$branch_id,$location_id,$user_id,$declaration = 0)
    {
        $created_by = $user_id;
        $updated_by = $user_id;
        $returnData = [];
        $i = 0;
        $flag = [];
        $error_flag = 0;

        $companies_name = queryGet("SELECT * FROM `erp_companies` WHERE `company_id` = '" . $company_id . "'",false);

        // $companyCodeNav = $companies_name["data"]["company_code"];
        // $companyNameNav = $companies_name["data"]["company_name"];

        foreach($POST_DATA as $POST)
        {

            $vendor_pan = $POST["vendor_pan"]??'';
            $trade_name = addslashes($POST["trade_name"]);
            // if($vendor_pan == "" || $vendor_pan == NULL)
            // {
            //     $flag[] = array("status"=>"warning","message"=>"Vendor added failed at line ".$i);
            //     continue;
            // }

            $vendor_pan_checking = queryGet("SELECT * FROM `erp_vendor_details` WHERE `company_id` = '" . $company_id . "' AND `trade_name`='".$trade_name."'",false);

            if($vendor_pan_checking["numRows"] != 0)
            {
                $flag[] = array("status"=>"warning","message"=>"Vendor already added at line ".$i);
                $error_flag++;
                $i++;
                continue;
            }
        
            //console($POST);
            $returnData = [];
            $POST["company_id"] = $company_id;
            $POST["company_branch_id"] = $branch_id;

                $accMapp = getAllfetchAccountingMappingTbl($company_id);
                if ($accMapp["status"] == "success") {
                    $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['vendor_gl'],$company_id);
                    $parentGlId = $paccdetails['data']['id'];
                    $admin = array();
                    $admin["adminName"] = $POST["vendor_authorised_person_name"];
                    $admin["adminEmail"] = $POST["vendor_authorised_person_email"];
                    $admin["adminPhone"] = $POST["vendor_authorised_person_phone"];
                    $admin["adminPassword"] = $POST["adminPassword"];
                    $admin["tablename"] = 'tbl_vendor_admin_details';
                    $admin["adminPassword"] = $POST["adminPassword"];
                    $admin["fldAdminCompanyId"] = $POST["company_id"];

                    // if ($POST["createdata"] == 'add_post') {
                        $vendor_status = 'active';
                    // } else {
                    //     $vendor_status = 'draft';
                    // }

                    $lastlQuery = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id` = '" . $POST["company_id"] . "'  ORDER BY `vendor_id` DESC LIMIT 1";
                    $resultLast = queryGet($lastlQuery);
                    $rowLast = $resultLast["data"];
                    $lastsl = $rowLast['vendor_code'];

                    /*$company_id = $POST["company_id"];
                    $company_branch_id = $POST["company_branch_id"];
                    $company_location_id = $POST["company_location_id"];*/
                    $vendor_code = getVendorSerialNumber($lastsl);
                    $vendor_gstin = $POST["vendor_gstin"] ?? '';
                    $constitution_of_business = $POST["con_business"];

                    $vendor_authorised_person_name = $POST["vendor_authorised_person_name"];
                    $vendor_authorised_person_designation = $POST["vendor_authorised_person_designation"];
                    $vendor_authorised_person_phone = $POST["vendor_authorised_person_phone"];
                    $vendor_authorised_alt_phone = $POST["vendor_authorised_alt_phone"];
                    $vendor_authorised_person_email = $POST["vendor_authorised_person_email"];
                    $vendor_authorised_alt_email = $POST["vendor_authorised_alt_email"];

                    // other address
                    $state = $POST["state"] ?? "";
                    $city = $POST["city"] ?? "";
                    $district = $POST["district"] ?? "";
                    $location = addslashes($POST["location"]);
                    $build_no = $POST["build_no"] ?? "";
                    $flat_no = $POST["flat_no"] ?? "";
                    $street_name = $POST["street_name"] ?? "";
                    $pincode = $POST["pincode"] ?? "";

                    // accounting
                    $opening_balance = $POST["opening_balance"] ?? "";
                    $currency = $POST["currency"] ?? "";
                    $credit_period = $POST["credit_period"] ?? 1;
                    $vendor_bank_cancelled_cheque = $POST["vendor_bank_cancelled_cheque"] ?? "";
                    $vendor_bank_ifsc = $POST["vendor_bank_ifsc"] ?? "";
                    $vendor_bank_name = $POST["vendor_bank_name"] ?? "";
                    $account_holder = $POST["account_holder"] ?? "";
                    $vendor_bank_branch = $POST["vendor_bank_branch"] ?? "";
                    $vendor_bank_address = addslashes($POST["vendor_bank_address"]);
                    $vendor_bank_account_no = $POST["vendor_bank_account_no"] ?? "";

                    // $vendor_picture = $POST["vendor_picture"];
                    $vendor_visible_to_all = $POST["vendor_visible_to_all"];
                    //$adminAvatar = uploadFile($POST["adminAvatar"], "../public/storage/avatar/",["jpg","jpeg","png"]);

                    $sql = queryGet("SELECT vendor_code FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id` = '" . $POST["company_id"] . "' AND `vendor_code`='" . $vendor_code . "'");
                    //exit;
                        if ($sql["numRows"] == 0) {
                            // console($POST);
                            $ins = "INSERT INTO `" . ERP_VENDOR_DETAILS . "` 
                                    SET
                                        `company_id`='" . $company_id . "',
                                        `company_branch_id`='" . $branch_id . "',
                                        `location_id`='" . $location_id . "',
                                        `parentGlId`='" . $parentGlId . "',
                                        `vendor_credit_period`='".$credit_period."',
                                        `vendor_code`='" . $vendor_code . "',
                                        `vendor_pan`='" . $vendor_pan . "',
                                        `vendor_gstin`='" . $vendor_gstin . "',
                                        `trade_name`='" . $trade_name . "',
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
                                        `vendor_status`='" . $vendor_status . "',
                                        `mail_send_status`='0'";

                               $insert_ins = queryInsert($ins);         

                            if ($insert_ins['status'] == "success") {
                                $vendorId = $insert_ins['insertedId'];

                                
                                ///---------------------------------Audit Log Start---------------------
                                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                                $auditTrail = array();
                                $auditTrail['basicDetail']['trail_type'] = 'ADD';   //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'	 //	Add/Update/Deleted
                                $auditTrail['basicDetail']['table_name'] = ERP_VENDOR_DETAILS;
                                $auditTrail['basicDetail']['column_name'] = 'vendor_id';  //Primary Key column
                                $auditTrail['basicDetail']['document_id'] = $vendorId;   // Primary Key
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

                                $auditTrail['action_data']['Vendor Detail']['parentGlId'] = $parentGlId;
                                $auditTrail['action_data']['Vendor Detail']['code'] = $vendor_code;
                                $auditTrail['action_data']['Vendor Detail']['pan'] = $vendor_pan;
                                $auditTrail['action_data']['Vendor Detail']['gstin'] = $vendor_gstin;
                                $auditTrail['action_data']['Vendor Detail']['trade_name'] = $trade_name;
                                $auditTrail['action_data']['Vendor Detail']['constitution_of_business'] = $constitution_of_business;
                                $auditTrail['action_data']['Vendor Detail']['person_name'] = $vendor_authorised_person_name;
                                $auditTrail['action_data']['Vendor Detail']['person_designation'] = $vendor_authorised_person_designation;
                                $auditTrail['action_data']['Vendor Detail']['person_phone'] = $vendor_authorised_person_phone;
                                $auditTrail['action_data']['Vendor Detail']['alt_phone'] = $vendor_authorised_alt_phone;
                                $auditTrail['action_data']['Vendor Detail']['person_email'] = $vendor_authorised_person_email;
                                $auditTrail['action_data']['Vendor Detail']['alt_email'] = $vendor_authorised_alt_email;
                                $auditTrail['action_data']['Vendor Detail']['visible_to_all'] = $vendor_visible_to_all;

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
                                    `vendor_business_created_by`='$created_by',
                                    `vendor_business_updated_by`='$created_by' 
                                    ";
                                queryInsert($ins_bussiness);
                                
                                $auditTrail['action_data']['Vendor Bussiness Places'][$district.' ('.$pincode.')']['primary_flag'] = '1';
                                $auditTrail['action_data']['Vendor Bussiness Places'][$district.' ('.$pincode.')']['building_no'] = $build_no;
                                $auditTrail['action_data']['Vendor Bussiness Places'][$district.' ('.$pincode.')']['flat_no'] = $flat_no;
                                $auditTrail['action_data']['Vendor Bussiness Places'][$district.' ('.$pincode.')']['street_name'] = $street_name;
                                $auditTrail['action_data']['Vendor Bussiness Places'][$district.' ('.$pincode.')']['pin_code'] = $pincode;
                                $auditTrail['action_data']['Vendor Bussiness Places'][$district.' ('.$pincode.')']['location'] = $location;
                                $auditTrail['action_data']['Vendor Bussiness Places'][$district.' ('.$pincode.')']['city'] = $city;
                                $auditTrail['action_data']['Vendor Bussiness Places'][$district.' ('.$pincode.')']['district'] = $district;
                                $auditTrail['action_data']['Vendor Bussiness Places'][$district.' ('.$pincode.')']['state'] = $state;

                                // insert to ERP_VENDOR_BUSINESS_PLACES from other addresses
                                foreach ($POST['vendorOtherAddress'] as $oneAddress) {

                                    // console($oneAddress["vendor_business_legal_name"]);
                                    //console($key);
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
                                    $addAndPin=$oneAddress['vendor_business_district'].' ('.$oneAddress['vendor_business_pin_code'].')';
                                    $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['primary_flag'] = '0';
                                    $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['building_no'] = $oneAddress['vendor_business_building_no'];
                                    $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['flat_no'] = $oneAddress['vendor_business_flat_no'];
                                    $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['street_name'] = $oneAddress['vendor_business_street_name'];
                                    $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['pin_code'] = $oneAddress['vendor_business_pin_code'];
                                    $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['location'] = $oneAddress['vendor_business_location'];
                                    $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['city'] = $oneAddress['vendor_business_city'];
                                    $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['district'] = $oneAddress['vendor_business_district'];
                                    $auditTrail['action_data']['Vendor Bussiness Places'][$addAndPin]['state'] = $oneAddress['vendor_business_state'];
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
                                     
                                        queryInsert($insAcc);

                                
                                $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['currency'] = $currency;
                                $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['credit_period'] = $credit_period;
                                $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_name'] = $vendor_bank_name;
                                $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['account_holder'] = $account_holder;
                                $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_account_no'] = $vendor_bank_account_no;
                                $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_ifsc'] = $vendor_bank_ifsc;
                                $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_branch'] = $vendor_bank_branch;
                                $auditTrail['action_data']['Vendor Bank Detail'][$vendor_bank_account_no]['vendor_bank_address'] = $vendor_bank_address;

                                // $sub = "Welcome to $companyNameNav Partnership!";

                                // $msg = "Dear $trade_name,<br>			
                                // We are thrilled to welcome you as a new vendor partner with $companyNameNav. We are confident that our partnership will bring about great results for both our companies.<br>    
                                // To ensure a smooth onboarding process, please find below some important information that will be helpful to you:<br><br>
                                // <b>Our team:</b> Our procurement team is available to assist you with any questions or concerns you may have. Please do not hesitate to reach out to us for any support.<br>			
                                // <b>Vendor Portal:</b> You will receive separate emails with your login information for $companyNameNav\'s vendor portal. This is where you can manage your invoices, purchase orders, and other important information.<br>			
                                // <b>Communication channels:</b> To ensure seamless communication, we have set up dedicated email addresses and phone numbers for our vendor partners.<br>			
                                // <b>Expectations and guidelines:</b> We have outlined our expectations and guidelines for our vendor partners in a detailed document, which we will send to you shortly.<br>	
                                // If there is anything else we can do to help, please do not hesitate to contact us. We are here to support you and ensure that our partnership is a successful one.<br>			
                                // <br> 
                                // Your Login Credentials are:<br>
                                // <b>Please click the link to download the App: </b>" . BASE_URL.'api/vitwoai-partner.apk'. "<br>
                                // <b>Company Code: </b>" . $companyCodeNav . "<br>
                                // <b>Vendor Code: </b>" . $vendor_code . "<br>
                                // <b>Password: </b>" . $POST["adminPassword"] . "<br>           
                                // Thank you for choosing  $companyNameNav, and we look forward to working with you.<br>		
                                // Best regards,  $companyNameNav";
                                // SendMailByMySMTPmailTemplate($vendor_authorised_person_email, $sub, $msg);

                                // $auditTrail['action_data']['Mail-Send']['send-status'] = 'success';

                                $auditTrailreturn = generateAuditTrail($auditTrail);

                                ///---------------------------------Audit Log End---------------------

                                $returnData['status'] = "success";
                                $returnData['message'] = "Vendor added success";
                                $returnData['insAdmin'] = $insAdmin;
                                $flag[] = array("status"=>"success","message"=>"Vendor added successfully at line ".$i);
                            } else {
                                $returnData['status'] = "warning";
                                $returnData['message'] = "Vendor added failed";
                                $flag[] = array("status"=>"warning","insert_ins"=>$insert_ins,"message"=>"Vendor added failed at line ".$i);
                                $error_flag++;
                            }
                        } else {
                            $returnData['status'] = "warning";
                            $returnData['message'] = "Vendor already exist";
                            $flag[] = array("status"=>"warning","message"=>"Vendor already exist at line ".$i);
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
                                    `migration_type`='vendor',
                                    `declaration`='$declaration_value',
                                    `created_by`='$created_by',
                                    `updated_by`='$created_by'
                                    ";
                                    queryInsert($insvalidation);
        return $total_array;
    }
}
