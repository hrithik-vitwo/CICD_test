<?php

use PhpOffice\PhpSpreadsheet\Worksheet\Row;

include_once("../../../../app/v1/connection-branch-admin.php");
include_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../app/v1/functions/branch/func-grn-controller.php");

$responseData = [];
$soObj = new BranchSo();
$grnObj = new GrnController();

function checkVendorPaymentData(array $finalData): bool {
    if (!isset($finalData['paymentDetails']) || !is_array($finalData['paymentDetails'])) {
        error_log('Error: paymentDetails is missing or not an array.');
        return false;
    }

    $paymentDetails = $finalData['paymentDetails'];
    $requiredFields = ['paymentCollectType', 'vendorId', 'collectPayment', 'bankId', 'documentDate', 'postingDate', 'tnxDocNo'];

    foreach ($requiredFields as $field) {
        if (empty($paymentDetails[$field])) {
            error_log("Error: Missing required field '$field' in paymentDetails.");
            return false;
        }
    }

    if (!isset($finalData['paymentInvoiceDetails']) || !is_array($finalData['paymentInvoiceDetails']) || count($finalData['paymentInvoiceDetails']) === 0) {
        error_log('Error: paymentInvoiceDetails is missing or empty.');
        return false;
    }

    foreach ($finalData['paymentInvoiceDetails'] as $index => $invoice) {
        $requiredInvoiceFields = ['grnIvId', 'grnCode', 'invAmt', 'dueAmt', 'recAmt'];

        foreach ($requiredInvoiceFields as $field) {
            if (!isset($invoice[$field])) {
                error_log("Error: Missing field '$field' in invoice at index $index.");
                return false;
            }
        }

        if (!is_numeric($invoice['recAmt']) || $invoice['recAmt'] <= 0) {
            error_log("Error: Invalid recAmt in invoice at index $index.");
            return false;
        }

        if (!is_numeric($invoice['dueAmt']) || $invoice['recAmt'] > $invoice['dueAmt']) {
            error_log("Error: recAmt cannot be greater than dueAmt in invoice at index $index.");
            return false;
        }
    }

    return true;
}
function checkCustomerPaymentData($finalData) {
    if (!isset($finalData['paymentDetails'])) {
        error_log('Error: paymentDetails is missing.');
        return false;
    }

    $paymentDetails = $finalData['paymentDetails'];

    $requiredFields = [
        'paymentCollectType',
        'customerId',
        'collectPayment',
        'bankId',
        'documentDate',
        'postingDate',
        'tnxDocNo'
    ];

    foreach ($requiredFields as $field) {
        if (empty($paymentDetails[$field])) {
            error_log("Error: Missing required field '$field' in paymentDetails.");
            return false;
        }
    }

    $customerId = $paymentDetails['customerId'];

    if (!isset($finalData['paymentInvDetails'][$customerId])) {
        error_log("Error: paymentInvDetails for customerId '$customerId' is missing.");
        return false;
    }

    $invoices = $finalData['paymentInvDetails'][$customerId];

    if (!is_array($invoices) || count($invoices) === 0) {
        error_log("Error: No invoices found for customerId '$customerId'.");
        return false;
    }

    $invoiceRequiredFields = [
        'invoiceId',
        'invoiceNo',
        'invAmt',
        'dueAmt',
        'customer_id',
        'invoiceStatus',
        'recAmt'
    ];

    foreach ($invoices as $index => $invoice) {
        foreach ($invoiceRequiredFields as $field) {
            if (!isset($invoice[$field]) || $invoice[$field] === '') {
                error_log("Error: Missing required field '$field' in invoice at index $index.");
                return false;
            }
        }

        if ($invoice['recAmt'] > $invoice['dueAmt']) {
            error_log("Error: recAmt cannot be greater than dueAmt in invoice at index $index.");
            return false;
        }
    }

    error_log("Success: finalData is valid and complete.");
    return true;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Match Function for non Accounting customer 
    if ($_POST['act'] == "customer") {
        // console($_POST['idarray']);
        global $created_by;
        global $updated_by;


        if (checkCustomerPaymentData($_POST['idarray'])) {
            $addCollectPayment = $soObj->insertCollectPayment($_POST['idarray'], $_FILES);
            if ($addCollectPayment['status'] == "success") {
                $statement_id = $_POST['statement_id'];
                $get_statement = queryGet('SELECT * FROM `erp_bank_statements` WHERE `id`=' . $statement_id);
                $statement_amount = $get_statement["data"]["remaining_amt"];
                $total_due_amt = $_POST['collectPaymentt'];
                  queryUpdate('UPDATE `erp_branch_sales_order_payments` SET  `collect_through`= "BRS", `reconciled_amount`="'.$total_due_amt. '",`reconciled_statement_id`="'.$statement_id.'", `updated_by`="' . $updated_by . '" WHERE `payment_id`=' . $addCollectPayment['paymentId']);

                if ($total_due_amt == $statement_amount) {


                    $current_datetime = date('Y-m-d H:i:s');
                    $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `remaining_amt`="0.00", `reconciled_status`="reconciled" ,`reconciled_at`="' . $current_datetime . '", reconciled_by="' . $updated_by . '",reconciled_location_id="' . $location_id . '", `updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);

                    if ($update_ststement_amount["status"] == "success") {
                        $returnData['status'] = "success";
                        $returnData['message'] = "Reconciled Successfuly";
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "something went wrong 1";
                    }
                } elseif ($total_due_amt < $statement_amount) {

                    $amt = $statement_amount - $total_due_amt;
                    $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `remaining_amt`= "' . $amt . '", `reconciled_status`="pending", `updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);

                    if ($update_ststement_amount["status"] == "success") {
                        $returnData['status'] = "success";
                        $returnData['message'] = "Partialy Reconciled Successfuly";
                        $returnData['query'] = $update_due_amt;
                        $returnData['query1'] = $update_ststement_amount;
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "something went wrong 2";
                        $returnData['query'] = $update_ststement_amount;
                    }
                } else {
                    // return
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Invoice amount cannot be greater than statement amount";
                    $returnData["data"] = [
                        "statement_amount" => $statement_amount,
                        "total_due_amt" => $total_due_amt,
                        "id_array" => $id_array,
                        "statement_id" => $statement_id
                    ];
                }
                // swalToast($addCollectPayment["status"], $addCollectPayment["message"], LOCATION_URL . "collect-payment.php");
            } else {
                // swalToast($addCollectPayment["status"], $addCollectPayment["message"]);
                $returnData['status'] = "warning";
                $returnData['message'] = "Something went wrong col..!";
                $returnData['query'] = $update_ststement_amount;
            }
        } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Validation failed. Please check the input data. !";
                
        }
        echo json_encode($returnData, true);
        // exit;
        // global $created_by;
        // global $updated_by;
        // $returnData = [];

        // $id_array = $_POST['idarray'];
        // $statement_id = $_POST['statement_id'];
        // $get_statement = queryGet('SELECT * FROM `erp_bank_statements` WHERE `id`=' . $statement_id);
        // $statement_amount = $get_statement["data"]["remaining_amt"];
        // // --------------- ACCOUNTING STARTED ------------------------

        // if (isset($_POST['flag']) && $_POST['flag'] == "accounting") {
        //     $listDetail = $_POST['listDetail'];
        //     $withdrawalAmt = $listDetail['withdrawal_amt'];
        //     $depositAmt = $listDetail['deposit_amt'];

        //     // statement data validation
        //     if($withdrawalAmt<=0 && $depositAmt<=0){
        //         echo json_encode(["status" => "error", "message" => "Statement Data is Not Valid"]);
        //         exit();
        //     }

        //     // accounting type decide
        //     $accountingType = "";
        //     if ($depositAmt > 0 && $withdrawalAmt <= 0) {
        //         $accountingType = "collection";
        //     } else if ($withdrawalAmt > 0 && $depositAmt <= 0) {
        //         $accountingType = "payment";
        //     }

        //     $accountingArr = [];
        //     $bankId = $listDetail['bank_id'];
        //     $id = $id_array[0]['id'];
        //     $sql = "SELECT * FROM `erp_branch_sales_order_invoices` WHERE `so_invoice_id`=$id";
        //     $res = queryGet($sql)['data'];
        //     $customerId = $res['customer_id'];
        //     $customerSql = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE company_id='$company_id' AND company_branch_id='$branch_id' AND location_id='$location_id' AND `customer_id`='$customerId'";
        //     $customerDetail = queryGet($customerSql)['data'];
        //     $paymentItemArray = [];
        //     $invNoStr = "";
        //     foreach ($id_array as $row) {
        //         $ivData = queryGet('SELECT * FROM `erp_branch_sales_order_invoices` WHERE `so_invoice_id`=' . $row['id'])['data'];
        //         if (!empty($invNoStr)) {
        //             $invNoStr .= " | ";
        //         }
        //         $invNoStr .= $ivData['invoice_no'];

        //         $paymentItemArray[] = [
        //             "invoice_id" => $id,
        //             "invoiceNo" => $ivData['invoice_no'],
        //             "roundoff" => "0",
        //             "writeoff" => "0",
        //             "financial_charge" => "0",
        //             "forex" => "0",
        //             "tds" => "0",
        //             "recAmt" => $row['value']
        //         ];
        //     }

        //     //payment detail array 
        //     $paymentDetail = [];

        //     // Array change for collection as well as payment
        //     if ($accountingType == "payment") {
        //         $remarks = "Payment for - $invNoStr";
        //         $journalEntryReference = "Payment/Expenses";

        //         // payment details array
        //         $paymentDetail = [
        //             $customerId => [
        //                 "vendorId" => $customerId,
        //                 "vendorParentGl" => $customerDetail['parentGlId'],
        //                 "vendor_code" => $customerDetail['customer_code'],
        //                 "vendor_name" => $customerDetail['trade_name'],
        //                 "paymentId" => $listDetail['utr_number'],
        //                 "paymentCode" => $listDetail['utr_number'],
        //                 "bankId" => $bankId,
        //                 "paymentItems" => $paymentItemArray
        //             ]
        //         ];
        //     } else if ($accountingType == "collection") {
        //         $remarks = "Payment collection for - $invNoStr";
        //         $journalEntryReference = "Collection";

        //         // payment details array
        //         $paymentDetail = [
        //             $customerId => [
        //                 "customerId" => $customerId,
        //                 "customer_parentGlId" => $customerDetail['parentGlId'],
        //                 "customer_code" => $customerDetail['customer_code'],
        //                 "customer_name" => $customerDetail['trade_name'],
        //                 "paymentId" => $listDetail['utr_number'],
        //                 "bankId" => $bankId,
        //                 "paymentItems" => $paymentItemArray
        //             ]
        //         ];
        //     }

        //     // these are same for collection and vendor Array
        //     $basicDetail = [
        //         "documentNo" => $listDetail['utr_number'],
        //         "documentDate" => $listDetail['tnx_date'],
        //         "postingDate" => $listDetail['tnx_date'],
        //         "reference" => $listDetail['particular'],
        //         "remarks" => $remarks,
        //         "journalEntryReference" => $journalEntryReference,
        //     ];

        //     // main collection accounting array
        //     $accountingArr = [
        //         "BasicDetails" => $basicDetail,
        //         "paymentDetails" => $paymentDetail
        //     ];

        //     //  main accounting function call
        //     if ($accountingType == "collection") {
        //         $collectionObj = $soObj->multicollectionAccountingPosting($accountingArr, "Collection", $listDetail['utr_number']);
        //     } else if ($accountingType == "payment") {
        //         $collectionObj = $grnObj->multipaymentAccountingPosting($accountingArr, "Payment", $listDetail['utr_number']);
        //     }
        //     // console($collectionObj);
        //     if ($collectionObj['status'] != "success") {
        //         echo json_encode($collectionObj);
        //         exit();
        //     }
        // }

        // // --------------- ACCOUNTING ENDED ------------------------

        // $statement_amount = $get_statement["data"]["remaining_amt"];
        // $total_due_amt = 0;
        // foreach ($id_array as $row) {
        //     $get_inv_data = queryGet('SELECT * FROM `erp_branch_sales_order_invoices` WHERE `so_invoice_id`=' . $row['id']);

        //     $due_amount = $get_inv_data["data"]["due_amount"];
        //     $givenValue = $row["value"];

        //     if ($total_due_amt <= $statement_amount) {
        //         //Update due amount
        //         // $update_due_amt = queryUpdate('UPDATE `erp_branch_sales_order_invoices` SET  `due_amount`= "0.00", `invoiceStatus`="4" ,`updated_by`="' . $updated_by . '" WHERE `so_invoice_id`=' . $id);
        //         $total_due_amt += $givenValue;
        //     } else {
        //         continue;
        //     }
        // }

        // if ($total_due_amt == $statement_amount) {

        //     foreach ($id_array as $row) {
        //         $val = $row['dueAmount'] - $row['value'];

        //         if ($val <= 0) {
        //             $update_due_amt = queryUpdate('UPDATE `erp_branch_sales_order_invoices` SET `due_amount` = "0.00", `invoiceStatus` = "4", `updated_by` = "' . $updated_by . '" WHERE `so_invoice_id` = ' . $row['id']);
        //         } else {
        //             $update_due_amt = queryUpdate('UPDATE `erp_branch_sales_order_invoices` SET `due_amount` = "' . $val . '", `invoiceStatus` = "2", `updated_by` = "' . $updated_by . '" WHERE `so_invoice_id` = ' . $row['id']);
        //         }
        //     }
        //     $current_datetime = date('Y-m-d H:i:s');
        //     //update 0 and reconcilled
        //     // $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `withdrawal_amt`= "0.00", `reconciled_status`="reconciled" ,`updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);
        //     // $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `deposit_amt`= "0.00", `remaining_amt`="0.00", `reconciled_status`="reconciled" ,`reconciled_at`="'.$current_datetime.'", reconciled_by="'.$updated_by.'",reconciled_location_id="'.$location_id.'", `updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);
        //     $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `remaining_amt`="0.00", `reconciled_status`="reconciled" ,`reconciled_at`="'.$current_datetime.'", reconciled_by="'.$updated_by.'",reconciled_location_id="'.$location_id.'", `updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);

        //     if ($update_ststement_amount["status"] == "success") {
        //         $returnData['status'] = "success";
        //         $returnData['message'] = "Reconciled Successfuly";
        //     } else {
        //         $returnData['status'] = "warning";
        //         $returnData['message'] = "something went wrong 1";
        //     }
        // } elseif ($total_due_amt < $statement_amount) {

        //     foreach ($id_array as $row) {
        //         $val = $row['dueAmount'] - $row['value'];

        //         if ($val <= 0) {
        //             $update_due_amt = queryUpdate('UPDATE `erp_branch_sales_order_invoices` SET `due_amount` = "0.00", `invoiceStatus` = "4", `updated_by` = "' . $updated_by . '" WHERE `so_invoice_id` = ' . $row['id']);
        //         } else {
        //             $update_due_amt = queryUpdate('UPDATE `erp_branch_sales_order_invoices` SET `due_amount` = "' . $val . '", `invoiceStatus` = "2", `updated_by` = "' . $updated_by . '" WHERE `so_invoice_id` = ' . $row['id']);
        //         }
        //     }

        //     //minus and partially reconciled
        //     $amt = $statement_amount - $total_due_amt;

        //     // $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `withdrawal_amt`= "' . $amt . '", `reconciled_status`="pending" ,`updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);
        //     $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `remaining_amt`= "' . $amt . '", `reconciled_status`="pending", `updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);

        //     if ($update_ststement_amount["status"] == "success") {
        //         $returnData['status'] = "success";
        //         $returnData['message'] = "Partialy Reconciled Successfuly";
        //         $returnData['query'] = $update_due_amt;
        //         $returnData['query1'] = $update_ststement_amount;
        //     } else {
        //         $returnData['status'] = "warning";
        //         $returnData['message'] = "something went wrong 2";
        //         $returnData['query'] = $update_ststement_amount;
        //     }
        // } else {
        //     // return
        //     $returnData['status'] = "warning";
        //     $returnData['message'] = "Invoice amount cannot be greater than statement amount";
        //     $returnData["data"] = [
        //         "statement_amount" => $statement_amount,
        //         "total_due_amt" => $total_due_amt,
        //         "id_array" => $id_array,
        //         "statement_id" => $statement_id
        //     ];
        // }
        // echo json_encode($returnData, true);
    }

    // non Accounted vendor function
    if ($_POST['act'] == "vendor") {
        global $created_by;
        global $updated_by;
        $returnData = [];

        
        $id_array = $_POST['idarray'];
        $statement_id = $_POST['statement_id'];


        if (checkVendorPaymentData($id_array)) {
            $addCollectPayment = $grnObj->insertVendorPayment($_POST['idarray'], $_FILES);
            if ($addCollectPayment['status'] == "success") {

                $statement_id = $_POST['statement_id'];
                $get_statement = queryGet('SELECT * FROM `erp_bank_statements` WHERE `id`=' . $statement_id);
                $statement_amount = $get_statement["data"]["remaining_amt"];
                $total_due_amt = $_POST['collectPaymentt'];

                queryUpdate('UPDATE `erp_grn_payments` SET  `payment_through`= "BRS", `reconciled_amount`="'.$total_due_amt. '",`reconciled_statement_id`="'.$statement_id.'", `updated_by`="' . $updated_by . '" WHERE `payment_id`=' . $addCollectPayment['paymentid']);

                if ($total_due_amt == $statement_amount) {


                    $current_datetime = date('Y-m-d H:i:s');
                    $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `remaining_amt`="0.00", `reconciled_status`="reconciled" ,`reconciled_at`="' . $current_datetime . '", reconciled_by="' . $updated_by . '",reconciled_location_id="' . $location_id . '", `updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);

                    if ($update_ststement_amount["status"] == "success") {
                        $returnData['status'] = "success";
                        $returnData['message'] = "Reconciled Successfuly";
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "something went wrong 1";
                    }
                } elseif ($total_due_amt < $statement_amount) {

                    $amt = $statement_amount - $total_due_amt;
                    $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `remaining_amt`= "' . $amt . '", `reconciled_status`="pending", `updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);

                    if ($update_ststement_amount["status"] == "success") {
                        $returnData['status'] = "success";
                        $returnData['message'] = "Partialy Reconciled Successfuly";
                        $returnData['query'] = $update_due_amt;
                        $returnData['query1'] = $update_ststement_amount;
                    } else {
                        $returnData['status'] = "warning";
                        $returnData['message'] = "something went wrong 2";
                        $returnData['query'] = $update_ststement_amount;
                    }
                } else {
                    // return
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Invoice amount cannot be greater than statement amount";
                    $returnData["data"] = [
                        "statement_amount" => $statement_amount,
                        "total_due_amt" => $total_due_amt,
                        "id_array" => $id_array,
                        "statement_id" => $statement_id
                    ];
                }
                // swalToast($addCollectPayment["status"], $addCollectPayment["message"], LOCATION_URL . "collect-payment.php");
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Something went wrong..!";
            }
        }else{
                $returnData['status'] = "warning";
                $returnData['message'] = "Validation failed. Please check the input data.";
        }

        // echo json_encode($returnData, true);
        // // exit;
        // // ----------------------- ACCOUNTING STARTED -----------------------

        // if (isset($_POST['flag']) && $_POST['flag'] == "accounting") {
        //     $listDetail = $_POST['listDetail'];            
        //     $withdrawalAmt = $listDetail['withdrawal_amt'];
        //     $depositAmt = $listDetail['deposit_amt'];

        //     // statement data validation
        //     if($withdrawalAmt<=0 && $depositAmt<=0){
        //         echo json_encode(["status" => "error", "message" => "Statement Data is Not Valid"]);
        //         exit();
        //     }

        //     // accounting type decide
        //     $accountingType = "";
        //     if ($depositAmt > 0 && $withdrawalAmt <= 0) {
        //         $accountingType = "collection";
        //     } else if ($withdrawalAmt > 0 && $depositAmt <= 0) {
        //         $accountingType = "payment";
        //     }

        //     $accountingArr = [];
        //     $bankId = $listDetail['bank_id'];
        //     $id = $id_array[0]['id'];

        //     $sql = "SELECT * FROM `erp_grninvoice` WHERE `grnivid`=$id";
        //     $res = queryGet($sql)['data'];
        //     $vendorId = $res['vendorId'];
        //     $vendorSql = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE company_id='$company_id' AND company_branch_id='$branch_id' AND location_id='$location_id' AND `vendor_id`='$vendorId'";
        //     $vendorDetail = queryGet($vendorSql)['data'];

        //     // payment item array ready
        //     $paymentItemArray = [];
        //     $grnIvCode = "";
        //     foreach ($id_array as $row) {
        //         $ivData = queryGet('SELECT * FROM `erp_grninvoice` WHERE `grnivid`=' . $row['id'])['data'];
        //         if (!empty($grnIvCode)) {
        //             $grnIvCode .= " | ";
        //         }
        //         $grnIvCode .= $ivData['grnIvCode'];

        //         $paymentItemArray[] = [
        //             "grnId" => $id,
        //             "grnCode" => $ivData['grnIvCode'],
        //             "roundoff" => "0",
        //             "writeoff" => "0",
        //             "financial_charge" => "0",
        //             "forex" => "0",
        //             "tds" => "0",
        //             "recAmt" => $row['value']
        //         ];
        //     }


        //     //payment detail array 
        //     $paymentDetail = [];

        //     // Array change for collection as well as payment
        //     if ($accountingType == "payment") {
        //         $remarks = "Payment for - $grnIvCode";
        //         $journalEntryReference = "Payment/Expenses";

        //         // payment details array
        //         $paymentDetail = [
        //             $vendorId => [
        //                 "vendorId" => $vendorId,
        //                 "vendorParentGl" => $vendorDetail['parentGlId'],
        //                 "vendor_code" => $vendorDetail['vendor_code'],
        //                 "vendor_name" => $vendorDetail['trade_name'],
        //                 "paymentId" => $statement_id,
        //                 "paymentCode" => $listDetail['utr_number'],
        //                 "bankId" => $bankId,
        //                 "paymentItems" => $paymentItemArray
        //             ]
        //         ];
        //     } else if ($accountingType == "collection") {
        //         $remarks = "Payment collection for - $grnIvCode";
        //         $journalEntryReference = "Collection";

        //         // payment details array
        //         $paymentDetail = [
        //             $vendorId => [
        //                 "customerId" => $vendorId,
        //                 "customer_parentGlId" => $vendorDetail['parentGlId'],
        //                 "customer_code" => $vendorDetail['vendor_code'],
        //                 "customer_name" => $vendorDetail['trade_name'],
        //                 "paymentId" => $statement_id,
        //                 "bankId" => $bankId,
        //                 "paymentItems" => $paymentItemArray
        //             ]
        //         ];
        //     }

        //     // these are same for collection and vendor Array
        //     $basicDetail = [
        //         "documentNo" => $listDetail['utr_number'],
        //         "documentDate" => $listDetail['tnx_date'],
        //         "postingDate" => $listDetail['tnx_date'],
        //         "reference" => $listDetail['particular'],
        //         "remarks" => $remarks,
        //         "journalEntryReference" => $journalEntryReference,
        //     ];

        //     // Main array Build
        //     $accountingArr = [
        //         "BasicDetails" => $basicDetail,
        //         "paymentDetails" => $paymentDetail
        //     ];


        //     //  main accounting function call
        //     if ($accountingType == "collection") {
        //         $paymentObj = $soObj->multicollectionAccountingPosting($accountingArr, "Collection", $listDetail['utr_number']);
        //     } else if ($accountingType == "payment") {
        //         $paymentObj = $grnObj->multipaymentAccountingPosting($accountingArr, "Payment", $listDetail['utr_number']);
        //     }

        //     if ($paymentObj['status'] != "success") {
        //         echo json_encode(["status" => "warning","message"=>"Accounting Failed"]);
        //         exit();
        //     }

        // }

        // // ----------------------- ACCOUNTING ENDED -----------------------

        // // Fetch statement 
        // $get_statement = queryGet('SELECT * FROM `erp_bank_statements` WHERE `id`=' . $statement_id);

        // $statement_amount = $get_statement["data"]["remaining_amt"];
        // $total_due_amt = 0;
        // foreach ($id_array as $row) {
        //     $get_inv_data = queryGet('SELECT * FROM `erp_grninvoice` WHERE `grnivid`=' . $row['id']);

        //     $due_amount = $get_inv_data["data"]["dueAmt"];
        //     $givenValue = $row["value"];


        //     if ($total_due_amt <= $statement_amount) {
        //         //Update due amount
        //         // $update_due_amt = queryUpdate('UPDATE `erp_branch_sales_order_invoices` SET  `due_amount`= "0.00", `invoiceStatus`="4" ,`updated_by`="' . $updated_by . '" WHERE `so_invoice_id`=' . $id);
        //         $total_due_amt += $givenValue;
        //     } else {
        //         continue;
        //     }
        // }

        // if ($total_due_amt == $statement_amount) {

        //     foreach ($id_array as $row) {

        //         // Calculate the difference between dueAmount and value
        //         $val = $row['dueAmount'] - $row['value'];

        //         // Check if the value is zero or less, and update accordingly
        //         if ($val <= 0) {
        //             // Set due_amount to 0.00 and paymentStatus to 4
        //             $update_due_amt = queryUpdate('UPDATE `erp_grninvoice` SET  `dueAmt`= "0.00", `paymentStatus`="4" ,`grnUpdatedBy`="' . $updated_by . '" WHERE `grnivid`=' . $row['id']);
        //         } else {
        //             // Update due_amount to the remaining value and set payment status to 2
        //             $update_due_amt = queryUpdate('UPDATE `erp_grninvoice` SET  `dueAmt`= "' . $val . '", `paymentStatus`="2" ,`grnUpdatedBy`="' . $updated_by . '" WHERE `grnivid`=' . $row['id']);
        //         }
        //     }
        //     //update 0 and reconcilled
        //     // $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `withdrawal_amt`= "0.00", `reconciled_status`="reconciled" ,`updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);
        //     $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `deposit_amt`= "0.00", `remaining_amt`="0.00", `reconciled_status`="reconciled" ,`updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);

        //     if ($update_ststement_amount["status"] == "success") {
        //         $returnData['status'] = "success";
        //         $returnData['message'] = "Reconciled Successfuly";
        //     } else {
        //         $returnData['status'] = "warning";
        //         $returnData['message'] = "something went wrong 1";
        //     }
        // } elseif ($total_due_amt < $statement_amount) {

        //     foreach ($id_array as $row) {

        //         $val = $row['dueAmount'] - $row['value'];
        //         if ($val <= 0) {
        //             $update_due_amt = queryUpdate('UPDATE `erp_grninvoice` SET  `dueAmt`= "0.00", `paymentStatus`="4" ,`grnUpdatedBy`="' . $updated_by . '" WHERE `grnivid`=' . $row['id']);
        //         } else {
        //             $update_due_amt =queryUpdate("UPDATE `erp_grninvoice` SET `dueAmt` = '" . $val . "', `paymentStatus` = '2', `grnUpdatedBy` = '" . $updated_by . "' WHERE `grnivid` = " . $row['id']);
        //         }
        //     }

        //     $amt = $statement_amount - $total_due_amt;

        //     // $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `withdrawal_amt`= "' . $amt . '", `reconciled_status`="pending" ,`updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);
        //     $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `remaining_amt`= "' . $amt . '", `reconciled_status`="pending" ,`updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);

        //     if ($update_ststement_amount["status"] == "success") {
        //         $returnData['status'] = "success";
        //         $returnData['message'] = "Partialy Reconciled Successfuly";
        //         $returnData['query'] = $update_due_amt;
        //         $returnData['query2'] = $update_ststement_amount;
        //     } else {
        //         $returnData['status'] = "warning";
        //         $returnData['message'] = "something went wrong 2";
        //         $returnData['query1'] = $update_due_amt;
        //         $returnData['query'] = $update_ststement_amount;
        //     }
        // } else {
        //     // return
        //     $returnData['status'] = "warning";
        //     $returnData['message'] = "Invoice amount cannot be greater than statement amount";
        //     $returnData["data"] = [
        //         "statement_amount" => $statement_amount,
        //         "total_due_amt" => $total_due_amt,
        //         "id_array" => $id_array,
        //         "statement_id" => $statement_id
        //     ];
        // }
        echo json_encode($returnData, true);
    }

    if ($_POST['act'] == "customerAcc") {

       global $created_by;
        global $updated_by;
        $returnData = [];

        $id_array = $_POST['idarray'];
        $statement_id = $_POST['statement_id'];
        $totalreconciled_amt=$_POST['totalrecon'];

        $get_statement = queryGet('SELECT * FROM `erp_bank_statements` WHERE `id`=' . $statement_id);
        $statement_amount = $get_statement["data"]["remaining_amt"];
        $total_due_amt = 0;
        foreach ($id_array as $row) {
            $get_inv_data = queryGet("SELECT * FROM `erp_branch_sales_order_payments` WHERE `payment_id` = " . (int)$row['payment_id']);


            $totalcollectAmt = $get_inv_data["data"]["collect_payment"];
            $total_rec_amt=$get_inv_data["data"]["reconciled_amount"];
            $total_rec_amt+=$row['enter_amt'];



            $rr = queryUpdate("UPDATE `erp_branch_sales_order_payments` 
            SET 
                `reconciled_amount` = $total_rec_amt, 
                `reconciled_statement_id` = $statement_id, 
                `updated_by` = '$updated_by' 
            WHERE `payment_id` = " . (int)$row['payment_id']);

            $givenValue = $row["enter_amt"];

            if ($total_due_amt <= $statement_amount) {
                $total_due_amt += $givenValue;
            } else {
                continue;
            }
        }

        if ($total_due_amt == $statement_amount) {

            $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `deposit_amt`= "0.00", `remaining_amt`="0.00", `reconciled_status`="reconciled" ,`updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);

            if ($update_ststement_amount["status"] == "success") {
                $returnData['status'] = "success";
                $returnData['message'] = "Reconciled Successfuly";
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "something went wrong 1";
            }
        } elseif ($total_due_amt < $statement_amount) {

           
            $amt = $statement_amount - $total_due_amt;

            $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `remaining_amt`= "' . $amt . '", `reconciled_status`="pending" ,`updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);

            if ($update_ststement_amount["status"] == "success") {
                $returnData['status'] = "success";
                $returnData['message'] = "Partialy Reconciled Successfuly";
                $returnData['query'] = $update_due_amt;
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "something went wrong 2";
                $returnData['query'] = $update_ststement_amount;
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invoice amount cannot be greater than statement amount";
            $returnData["data"] = [
                "statement_amount" => $statement_amount,
                "total_due_amt" => $total_due_amt,
                "id_array" => $id_array,
                "statement_id" => $statement_id
            ];
        }
        echo json_encode($returnData, true);
    }



    if ($_POST['act'] == "vendorpaymentReconciled") {

        global $created_by;
        global $updated_by;
        $returnData = [];

        $id_array = $_POST['idarray'];
        $statement_id = $_POST['statement_id'];
        $totalreconciled_amt=$_POST['totalrecon'];

        $get_statement = queryGet('SELECT * FROM `erp_bank_statements` WHERE `id`=' . $statement_id);
        $statement_amount = $get_statement["data"]["remaining_amt"];
        $total_due_amt = 0;
        foreach ($id_array as $row) {
            $get_inv_data = queryGet("SELECT * FROM `erp_grn_payments` WHERE `payment_id` = " . (int)$row['payment_id']);


            $totalcollectAmt = $get_inv_data["data"]["collect_payment"];
            $total_rec_amt=$get_inv_data["data"]["reconciled_amount"];
            $total_rec_amt+=$row['enter_amt'];



            $rr = queryUpdate("UPDATE `erp_grn_payments` 
            SET 
                `reconciled_amount` = $total_rec_amt, 
                `reconciled_statement_id` = $statement_id, 
                `updated_by` = '$updated_by' 
            WHERE `payment_id` = " . (int)$row['payment_id']);

            $givenValue = $row["enter_amt"];

            if ($total_due_amt <= $statement_amount) {
                $total_due_amt += $givenValue;
            } else {
                continue;
            }
        }

        if ($total_due_amt == $statement_amount) {

            $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `deposit_amt`= "0.00", `remaining_amt`="0.00", `reconciled_status`="reconciled" ,`updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);

            if ($update_ststement_amount["status"] == "success") {
                $returnData['status'] = "success";
                $returnData['message'] = "Reconciled Successfuly";
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "something went wrong 1";
            }
        } elseif ($total_due_amt < $statement_amount) {

           
            $amt = $statement_amount - $total_due_amt;

            $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `remaining_amt`= "' . $amt . '", `reconciled_status`="pending" ,`updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);

            if ($update_ststement_amount["status"] == "success") {
                $returnData['status'] = "success";
                $returnData['message'] = "Partialy Reconciled Successfuly";
                $returnData['query'] = $update_due_amt;
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "something went wrong 2";
                $returnData['query'] = $update_ststement_amount;
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invoice amount cannot be greater than statement amount";
            $returnData["data"] = [
                "statement_amount" => $statement_amount,
                "total_due_amt" => $total_due_amt,
                "id_array" => $id_array,
                "statement_id" => $statement_id
            ];
        }
        echo json_encode($returnData, true);
    }
}
