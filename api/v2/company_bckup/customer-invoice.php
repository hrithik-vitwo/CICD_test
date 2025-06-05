<?php
require_once "api-goods-function.php";
require_once "../../../app/v1/functions/branch/func-opening-closing-balance-controller.php";

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $isValidate = validate($_POST, [
        "data" => "required",
        "company_id" => "required",
        "branch_id" => "required",
        "location_id" => "required",
        "user_id" => "required"

    ]);
    if ($isValidate["status"] != "success") {
        sendApiResponse([
            "status" => "error",
            "message" => "Invalid inputs"

        ], 200);
    }

    $data_set = json_decode($_POST['data'],true);

    $company_id = $_POST["company_id"];
    $branch_id = $_POST["branch_id"];
    $location_id = $_POST["location_id"];
    $user_id = $_POST["user_id"];
    $created_by = $_POST["user_id"]."|location";
    $updated_by = $_POST["user_id"]."|location";

    // print_r($data_set);

    $company_opening_query = queryGet('SELECT `opening_date` FROM `erp_companies` WHERE `company_id`=' . $company_id);

    $compOpeningDate = $company_opening_query["data"]["opening_date"];

    $array = [];
    $flag = [];
    $error_flag = 0;
    $i =1;
    $unique = rand(10000,100000).time();
    foreach($data_set as $key => $data)
    {
        $subglcode = $data["subgl"];
        $get_gl_query = queryGet('SELECT `customer_id`,`customer_code`,`parentGlId` FROM `erp_customer` WHERE `company_id`=' . $company_id . ' AND `company_branch_id`='.$branch_id.' AND `location_id`='.$location_id.' AND `customer_code`="'.$subglcode.'" AND `customer_status`= "active"');

        if($get_gl_query["numRows"] == 0)
        {
            $flag[] = array("status"=>"warning","message"=>"Customer not found at line ".$i);
            $error_flag++;
            $i++;
            continue;
        }

        $data["gl"] = $get_gl_query["data"]["parentGlId"];

        //Insert TO invoice table
        
        $invNo = $data["inv_no"];
        $invoice_no_serialized = NULL;
        $customer_id = $get_gl_query["data"]["customer_id"];
        $creditPeriod = $data["credit_period"]??1;
        $delivery_no = NULL;
        $so_number = NULL;
        $invoice_date = date("Y-m-d",strtotime($data["inv_date"]));
        $poNumber = NULL;
        $poDate = NULL;
        $totalItems = 0;
        $subTotal = $data["sub_total"]??0;
        $curr_rate = $data["conversion_rate"]??0;
        $currency_name = $data["currency_name"]??'INR';

        $curr_query = queryGet('SELECT `currency_id`,`currency_name` FROM `erp_currency_type` WHERE `currency_name`='.$currency_name);

        $currencyId = $curr_query["data"]["currency_id"];
        $totalDiscount = trim($data["discount"])==null?0:$data["discount"];
        $cgst =  trim($data["cgst"])==null?0:$data["cgst"];
        $sgst =  trim($data["sgst"])==null?0:$data["sgst"];
        $igst =  trim($data["igst"])==null?0:$data["igst"];
        $kamId = 0;
        $profit_center = "";
        $totalTaxAmt = $cgst + $sgst + $igst;
        $allTotalAmt = trim($data["total_amt"])==null?0:$data["total_amt"];
        $due_amount = trim($data["due_amt"])==null?0:$data["due_amt"];


        $customerDetailsObj = queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customer_id'")['data'];

        $customer_name = $customerDetailsObj['customer_name'];
        $customer_authorised_person_email = $customerDetailsObj['customer_authorised_person_email'];
        $customerGstin = $customerDetailsObj['customer_gstin'];

        $customerDetailsSerialize = serialize($customerDetailsObj);

        $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
        $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
        $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
        $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
        $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
        $arrMarge = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);
        $companySerialize = serialize($arrMarge);

        $companyBankAccDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='$bankId' ")['data'];
        $companyBankSerialize = serialize($companyBankAccDetailsObj);

        $branch  = queryGet("SELECT `branch_gstin` FROM `erp_branches` WHERE branch_id='$branch_id' ");
        $branchGstin = $branch["data"]["branch_gstin"];
        $customer_billing_address = $data["customer_billing_address"];
        $customer_shipping_address = $data["customer_shipping_address"];

        $current_date = date('Y-m-d');
        
        if(strtolower(str_replace(' ', '', $invNo)) == "onaccount")
        {
            
            $insert_payments = "INSERT INTO `erp_branch_sales_order_payments`
            SET 
                `collectionCode`='$unique',
                `customer_id`='$customer_id',
                `company_id`='$company_id',
                `branch_id`='$branch_id',
                `location_id`='$location_id',
                `bank_id`='0',
                `journal_id`='0',
                `collect_payment`='$allTotalAmt',
                `adjusted_amount`='0.00',
                `payment_advice`='$unique',
                `remarks`='migration',
                `documentDate`='$current_date',
                `transactionId`='',
                `postingDate`='$current_date',
                `paymentCollectType`='migration',
                `reconciled_statement_id`= 0,
                `created_by`='$created_by',
                `updated_by`='$created_by' 
                ";
            $query_insert_payments = queryInsert($insert_payments);

            if($query_insert_payments["status"] == "success")
            {
                $id = $query_insert_payments["insertedId"];
                $insert_payments_log = "INSERT INTO `erp_branch_sales_order_payments_log`
                SET 
                    `company_id`='$company_id',
                    `branch_id`='$branch_id',
                    `location_id`='$location_id',
                    `payment_id`='$id',
                    `customer_id`='$customer_id',
                    `invoice_id`='0',
                    `payment_type`='advanced',
                    `payment_amt`='$allTotalAmt',
                    `remarks`='migration',
                    `created_by`='$created_by',
                    `updated_by`='$created_by' 
                    ";
                $query_insert_payments_logs = queryInsert($insert_payments_log);

                if($query_insert_payments_logs["status"] == "success")
                {
                    $flag[] = array("status"=>"success","message"=>"Invoice Amount successfully Submitted at line ".$i);
                }
                else
                {
                    $flag[] = array("status"=>"warning","message"=>"Invoice Amount Not Submitted at line1.1 ".$i);
                    $error_flag++;
                }
            }
            else
            {
                $flag[] = array("status"=>"warning","message"=>"Invoice Amount Not Submitted at line1.2 ".$i);
                $error_flag++;
            }

        }
        else
        {

            $check_duplicate = queryGet("SELECT `invoice_no` FROM `".ERP_BRANCH_SALES_ORDER_INVOICES."` WHERE invoice_no='".$invNo."' AND customer_id='".$customer_id."'");

            if($check_duplicate["numRows"] != 0)
            {
                $flag[] = array("status"=>"warning","message"=>"Invoice Already Exists at line ".$i);
                $error_flag++;
                $i++;
                continue;
            }
            else
            {
            $invInsert = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                        SET 
                            `invoice_no`='$invNo',
                            `invoice_no_serialized`='$invoice_no_serialized',
                            `company_id`='$company_id',
                            `branch_id`='$branch_id',
                            `location_id`='$location_id',
                            `customer_id`='$customer_id',
                            `credit_period`='$creditPeriod',
                            `delivery_no`='$delivery_no',
                            `so_number`='$so_number',
                            `invoice_date`='$invoice_date',
                            `po_number`='$poNumber',
                            `po_date`='$poDate',
                            `totalItems`='$totalItems',
                            `sub_total_amt`='$subTotal',
                            `conversion_rate`='$curr_rate',
                             `currency_id`='$currencyId',
                             `currency_name`='$currencyName',
                            `totalDiscount`='$totalDiscount',
                            `cgst`='$cgst',
                            `sgst`='$sgst',
                            `kamId`='$kamId',
                            `profit_center`='$profit_center',
                            `igst`='$igst',
                            `total_tax_amt`='$totalTaxAmt',
                            `all_total_amt`='$allTotalAmt',
                            `due_amount`='$due_amount',
                            `customerDetails`='$customerDetailsSerialize',
                            `companyDetails`='$companySerialize',
                            `company_bank_details`='$companyBankSerialize',
                            `company_gstin`='$branchGstin',
                            `customer_gstin`='$customerGstin',
                            `customer_billing_address`='$customer_billing_address',
                            `customer_shipping_address`='$customer_shipping_address',
                            `created_by`='$created_by',
                            `updated_by`='$updated_by',
                            `type`='migration',
                            `invoiceStatus`='1'
        ";

        $insrt_inv = queryInsert($invInsert);

        if($insrt_inv['status'] == 'success')
        {
            $flag[] = array("status"=>"success","message"=>"Invoice Amount successfully Submitted at line ".$i);
        }
        else
        {
            $flag[] = array("status"=>"warning","message"=>"Invoice Amount Not Submitted at line2.1 ".$i,"arry"=>$insrt_inv);
            $error_flag++;
        }
    }

        }
        $i++;
    }


    if($error_flag > 0)
    {
        $grand_status = "warning";
        $grand_message = "Partially Success";
    }
    else
    {
        $grand_status = "success";
        $grand_message = "Success";
    }

    $total_array = array("status"=>$grand_status,"data"=>$flag,"message"=>$grand_message);


    // $openingClosingBalanceObj = new OpeningClosingBalance();
    // $resultObj = $openingClosingBalanceObj->saveOpeningBalance($array);
    // $resultObj["data"] = [];
    $declaration = 0;
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
                        `migration_type`='customerinv',
                        `declaration`='$declaration_value',
                        `created_by`='$created_by',
                        `updated_by`='$created_by' 
                        ";
                    queryInsert($insvalidation);
    sendApiResponse($total_array, 200);

}
else
{
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed"

    ], 405);
}
