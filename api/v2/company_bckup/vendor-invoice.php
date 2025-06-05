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
        $get_gl_query = queryGet('SELECT `vendor_id`,`vendor_code`,`parentGlId`,`vendor_gstin`,`trade_name` FROM `erp_vendor_details` WHERE `company_id`=' . $company_id . ' AND `company_branch_id`='.$branch_id.' AND `location_id`='.$location_id.' AND `vendor_code`="'.$subglcode.'" AND `vendor_status`= "active"');

        if($get_gl_query["numRows"] == 0)
        {
            $flag[] = array("status"=>"warning","message"=>"Vendor not found at line ".$i);
            $error_flag++;
            $i++;
            continue;
        }

        $data["gl"] = $get_gl_query["data"]["parentGlId"];
        $grnIvCode = "MRIG".time();
        $vendorId = $get_gl_query["data"]["vendor_id"];
        $vendorCode = $get_gl_query["data"]["vendor_code"];
        $vendorGstin = $get_gl_query["data"]["vendor_gstin"];
        $vendorName = $get_gl_query["data"]["trade_name"];
        $documentNo = $data["inv_no"];
        $documentDate = date("Y-m-d",strtotime($data["inv_date"]));
        $invoicePostingDate = date("Y-m-d",strtotime($data["inv_posting_date"]));
        $invoiceDueDate = date("Y-m-d",strtotime($data["inv_due_date"]));
        $invoiceDueDays = trim($data["inv_due_days"])==null? 1: $data["inv_due_days"];
        $totalInvoiceTotal = trim($data["inv_total"])==null? 0 : $data["inv_total"];
        $dueAmt = trim($data["dueAmt"])==null ? 0:$data["dueAmt"];
        $totalInvoiceSubTotal = trim($data["inv_sub_total"])==null? 0:$data["inv_sub_total"];
        $totalInvoiceCGST = trim($data["cgst"])==null ? 0 : $data["cgst"];
        $totalInvoiceSGST = trim($data["sgst"])==null ? 0 : $data["sgst"];
        $totalInvoiceIGST = trim($data["igst"])==null ? 0 : $data["igst"];
        $totalInvoiceTDS = trim($data["tds"])==null ? 0 : $data["tds"];
        $vendorDocumentFile = NULL;
        $grnApprovedStatus = "approved";

        $vendor_query = queryGet('SELECT `vendor_business_state` FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=' . $vendorId);
        $vendorGstinStateName = $vendor_query["data"]["vendor_business_state"];

        $location_query = queryGet('SELECT `othersLocation_state` FROM `erp_branch_otherslocation` WHERE `company_id`=' . $company_id . ' AND `branch_id`='.$branch_id);
        $locationGstinStateName = $location_query["data"]["othersLocation_state"];

        if($dueAmt == 0)
        {
            $paymentStatus = 4;
        }
        else
        {
            if($dueAmt == $totalInvoiceTotal)
            {
                $paymentStatus = 15;
            }
            else
            {
                $paymentStatus = 2;
            }
        }

        
        $current_date = date('Y-m-d');
        
        if(strtolower(str_replace(' ', '', $documentNo)) == "onaccount")
        {
            
            $insert_payments = "INSERT INTO `erp_grn_payments`
            SET 
                `paymentCode`='$unique',
                `vendor_id`='$vendorId',
                `company_id`='$company_id',
                `branch_id`='$branch_id',
                `location_id`='$location_id',
                `bank_id`='0',
                `journal_id`='0',
                `collect_payment`='$totalInvoiceTotal',
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
                $insert_payments_log = "INSERT INTO `erp_grn_payments_log`
                SET 
                    `company_id`='$company_id',
                    `branch_id`='$branch_id',
                    `location_id`='$location_id',
                    `payment_id`='$id',
                    `vendor_id`='$vendorId',
                    `grn_id`='0',
                    `payment_type`='advanced',
                    `payment_amt`='$totalInvoiceTotal',
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
                    $flag[] = array("status"=>"warning","message"=>"Invoice Amount Not Submitted at line ".$i);
                    $error_flag++;
                }
            }
            else
            {
                $flag[] = array("status"=>"warning","message"=>"Invoice Amount Not Submitted at line ".$i);
                $error_flag++;
            }

        }
        else
        {

            $check_duplicate = queryGet("SELECT `invoice_no` FROM `erp_grninvoice` WHERE vendorDocumentNo='".$documentNo."' AND vendorId='".$vendorId."'");

            if($check_duplicate["numRows"] != 0)
            {
                $flag[] = array("status"=>"warning","message"=>"Invoice Already Exists at line ".$i);
                $error_flag++;
                $i++;
                continue;
            }
            else
            {
            
            $invInsert = 'INSERT INTO `erp_grninvoice` SET 
                        `companyId`="' . $company_id . '",
                        `branchId`="' . $branch_id . '",
                        `locationId`="' . $location_id . '",
                        `functionalAreaId`="",
                        `grnId`="0",
                        `grnCode`="",
                        `grnPoNumber`="",
                        `grnIvCode`="' . $grnIvCode . '",
                        `grnType`="migration",
                        `vendorId`=' . $vendorId . ',
                        `vendorCode`="' . $vendorCode . '",
                        `vendorGstin`="' . $vendorGstin . '",
                        `vendorName`="' . $vendorName . '",
                        `vendorDocumentNo`="' . $documentNo . '",
                        `vendorDocumentDate`="' . $documentDate . '",
                        `postingDate`="' . $invoicePostingDate . '",
                        `dueDate`="' . $invoiceDueDate . '",
                        `dueDays`="' . $invoiceDueDays . '",
                        `paymentStatus`="'.$paymentStatus.'",
                        `dueAmt`="' . $dueAmt . '",
                        `grnSubTotal`="' . $totalInvoiceSubTotal . '",
                        `grnTotalCgst`="' . $totalInvoiceCGST . '",
                        `grnTotalSgst`="' . $totalInvoiceSGST . '",
                        `grnTotalIgst`="' . $totalInvoiceIGST . '",
                        `grnTotalTds`="' . $totalInvoiceTDS . '",
                        `grnTotalAmount`="' . $totalInvoiceTotal . '",
                        `rcm_enabled`="0",
                        `locationGstinStateName`="' . $locationGstinStateName . '",
                        `vendorGstinStateName`="' . $vendorGstinStateName . '",
                        `vendorDocumentFile`="' . $vendorDocumentFile . '",
                        `grnCreatedBy`="' . $created_by . '",
                        `grnUpdatedBy`="' . $updated_by . '",
                        `grnApprovedStatus`="' . $grnApprovedStatus . '"';

        $insrt_inv = queryInsert($invInsert);

        if($insrt_inv['status'] == 'success')
        {
            $flag[] = array("status"=>"success","message"=>"Invoice Amount successfully Submitted at line ".$i);
        }
        else
        {
            $flag[] = array("status"=>"warning","message"=>"Invoice Amount Not Submitted at line ".$i);
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
                        `migration_type`='vendorinv',
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
