<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../../app/v1/functions/common/templates/template-invoice.controller.php");
require_once("../../../../../app/v1/functions/branch/func-reverse-posting.php");
$headerData = array('Content-Type: application/json');

// Invoice template obj 
$templateInvoiceControllerObj = new TemplateInvoiceController();
// Database object for all db related functions
$dbObj = new Database();
$reversePostingObj = new ReversePosting();

// modal data by inv  id
if ($_SERVER["REQUEST_METHOD"] == "GET" &&  $_GET['act'] == "modalData") {

    $invoiceId = $_GET['soInvId'];
    $sql_list = "SELECT salesInvoice.*, eInv.`ack_no`, eInv.`ack_date`, eInv.`irn`, eInv.`signed_qr_code`,cust.customer_code, cust.trade_name, cust.customer_picture , cust.customer_gstin,cust.customer_pan,cust.customer_currency,cust.customer_status, cust.customer_authorised_person_email, cust.customer_authorised_person_phone ,func.functionalities_name,kam.kamName FROM `erp_branch_sales_order_invoices` as salesInvoice LEFT JOIN `erp_kam` as kam On salesInvoice.kamId = kam.kamId LEFT JOIN erp_company_functionalities as func ON salesInvoice.profit_center = func.functionalities_id LEFT JOIN `erp_e_invoices` as eInv ON salesInvoice.so_invoice_id = eInv.invoice_id LEFT JOIN erp_customer as cust ON cust.customer_id=salesInvoice.customer_id WHERE salesInvoice.so_invoice_id='$invoiceId' AND salesInvoice.company_id = '$company_id' AND salesInvoice.branch_id = '$branch_id' AND salesInvoice.location_id = '$location_id' AND salesInvoice.`status` != 'deleted' ORDER BY salesInvoice.invoice_date DESC , salesInvoice.so_invoice_id DESC , salesInvoice.invoice_no ASC";
    $invQuery = $dbObj->queryGet($sql_list);

    if ($invQuery['numRows'] > 0) {
        $data = $invQuery['data'];
        $so_id=$data['so_id'];
        $so_number=$dbObj->queryGet("SELECT * FROM `erp_branch_sales_order` WHERE `so_id`='$so_id'AND company_id = '$company_id' AND branch_id = '$branch_id' AND location_id = '$location_id' ")['data'];
        
        // console($invQuery);
        // sql for fetching item detail of this inv 
        $itemSql = "SELECT * FROM `erp_branch_sales_order_invoice_items` WHERE so_invoice_id=$invoiceId";
        $itemQuery = $dbObj->queryGet($itemSql, true);
        // console($itemQuery['data']);
        $itemDetails = $itemQuery['data'];

        // exit();

        $items = [];
        $allSubTotal = 0;
        // item loop starts

        $curName = "";
        $currencyQuery = queryGet("SELECT currency_name FROM `erp_currency_type` WHERE currency_id='" . $company_currency . "'");
        if ($currencyQuery['numRows'] > 0) {
            $curName = $currencyQuery['data']['currency_name'];
        } else {
            $curName = "N/A";
        }
        
        foreach ($itemDetails as $oneItem) {
   
            // item level gst calculation for each item and subtotal for all
            $gstAmount = 0;
            $subTotal = $oneItem['itemTargetPrice'] * $oneItem['qty'];
            // $discount=$oneItem['itemTotalDiscount']+$oneItem['cashDiscountAmount'];
            $discount = $oneItem['totalDiscountAmt'] + $oneItem['cashDiscountAmount'];
            $taxAbleAmount = $subTotal - $discount;
            if ($oneItem['tax'] == 0) {
                $itemTotalAmount = $taxAbleAmount;
            } else {
                $gstAmount = $oneItem['totalTax'];
                $itemTotalAmount = $taxAbleAmount + $gstAmount;
            }
            $allSubTotal += $subTotal;
            $items[] = [
                "itemCode" => $oneItem['itemCode'],
                "itemName" => $oneItem['itemName'],
                "qty" => $oneItem['qty'],
                "rate" => $oneItem['itemTargetPrice'],
                "subTotal" => $subTotal,
                "total_discount" => $discount,
                "totalTax" => $oneItem['totalTax'],
                "tax" => $oneItem['tax'],
                "hsnCode" => $oneItem['hsnCode'],
                "taxAbleAmount" => $taxAbleAmount,
                "gstAmount" => $gstAmount,
                "itemTotalAmount" => $itemTotalAmount
            ];
        }
        $ewbSql="SELECT * FROM `erp_e_way_bills` as ewayBill  WHERE ewayBill.irn='".$data['irn']."' AND ewayBill.company_id=$company_id AND ewayBill.branch_id=$branch_id AND ewayBill.location_id=$location_id";
        $ewbData=queryGet($ewbSql)['data'];
        $ewbNo=$ewbData['ewb_no'];

        // main data response 
        $dynamic_data = [
            "dataObj" => $data,
            "itemDetail" => $items,
            "currecy_name_words" => number_to_words_indian_rupees($data['all_total_amt']),
            "created_by" => getCreatedByUser($data['created_by']),
            "created_at" => formatDateORDateTime($data['created_at']),
            "updated_by" => getCreatedByUser($data['updated_by']),
            "updated_at" => formatDateORDateTime($data['updated_at']),
            "placeOfsupply" => getStateDetail($data['placeOfSupply'])['data']['gstStateName'],
            "allSubTotal" => $allSubTotal,
            "compCurrencyId" => $company_currency,
            "companyCurrency" => $curName,
            "so_number" => $so_number['so_number'],
            "irn" => $data['irn'],
            "ewbNo"=>$ewbData['ewb_no'],
        ];
        $res = [
            "status" => true,
            "msg" => "success",
            "sql" => $sql_list,
            "data" => $dynamic_data
        ];
    } else {
        $res = [
            "status" => false,
            "msg" => "Error!",
            "sql" => $sql_list
        ];
    }
    echo json_encode($res);
} else if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == "classicView") {

    $templateId = $_GET['templateId'];
    $invoiceId = $_GET['invoiceId'];
    $invoiceType = $_GET['invoiceType'];

    // $invoiceId=$_GET['']
    if ($invoiceType === "company") {
        $templateInvoiceControllerObj->printInvoice($invoiceId, $templateId);
    } else {
        $templateInvoiceControllerObj->printCustomerInvoice($invoiceId, $templateId);
    }
} else if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == "acceptInv") {
    $invId = $_GET['soInvId'];
    $sql_list = "SELECT salesInvoice.*, eInv.`ack_no`, eInv.`ack_date`, eInv.`irn`, eInv.`signed_qr_code`,cust.customer_code, cust.trade_name, cust.customer_picture , cust.customer_gstin,cust.customer_pan,cust.customer_currency,cust.customer_status, cust.customer_authorised_person_email, cust.customer_authorised_person_phone ,func.functionalities_name,kam.kamName FROM `erp_branch_sales_order_invoices` as salesInvoice LEFT JOIN `erp_kam` as kam On salesInvoice.kamId = kam.kamId LEFT JOIN erp_company_functionalities as func ON salesInvoice.profit_center = func.functionalities_id LEFT JOIN `erp_e_invoices` as eInv ON salesInvoice.so_invoice_id = eInv.invoice_id LEFT JOIN erp_customer as cust ON cust.customer_id=salesInvoice.customer_id WHERE salesInvoice.so_invoice_id='$invId' AND salesInvoice.company_id = '$company_id' AND salesInvoice.branch_id = '$branch_id' AND salesInvoice.location_id = '$location_id' AND salesInvoice.`status` != 'deleted' ORDER BY salesInvoice.invoice_date DESC , salesInvoice.so_invoice_id DESC , salesInvoice.invoice_no ASC";
    $invQuery = $dbObj->queryGet($sql_list);
    $res = [];
    if ($invQuery['numRows'] > 0) {
        $data = $invQuery['data'];
        $kamId = $data['kamId'];
        $kam_details =  queryGet("SELECT * FROM `erp_kam` WHERE `kamId` = $kamId");
        $kam_email = $kam_details['data']['email'];
        $invoice_date = $data['invoice_date'];
        $creditPeriod = $data['credit_period'];
        $encodeInv_id = base64_encode($invId);
        $customer_authorised_person_email = $data['customer_authorised_person_email'];
        $customer_authorised_person_phone = $data['customer_authorised_person_phone'];
        $invNo = $data['invoice_no'];
        $customerName = $data['trade_name'];
        $customerCode = $data['customer_code'];
        $allTotalAmt = $data['all_total_amt'];
        $duedate = date("Y-m-d", strtotime($invoice_date . ' + ' . $creditPeriod . " days"));
        $invoicelink = BASE_URL . 'branch/location/classic-view/invoice-preview-print.php?invoice_id=' . $encodeInv_id;
        $invoicelinkWhatsapp = 'classic-view/invoice-preview-print.php?invoice_id=' . $encodeInv_id;
        $to = $customer_authorised_person_email;
        $sub = 'Invoice ' . $invNo . ' for Your Recent Purchase';
        $imgSrc = BASE_URL . 'public/mailstatus/mail-status-so-invoice.php?invId=' . $invId . '&mailstatus=2';

        $msg = '
                    <img src="'.$imgSrc.'" style="height:1px; width:1px">
                    <div>
                    <div><strong>Dear ' . $customerName . ',</strong></div>
                    <p>
                        I hope this email finds you well. I am writing to inform you that an invoice for your recent purchase with <b>' . $companyNameNav . '</b> has been generated and is now ready for payment.
                    </p>
                    <strong>
                        Invoice details:
                    </strong>
                    <div style="display:grid">
                        <span>
                            Invoice Number: ' . $invNo . '
                        </span>
                        <span>
                            Amount Due: ' . $allTotalAmt . '
                        </span>
                        <span>
                            Due Date: <strong>' . $duedate . '</strong>
                        </span>
                    </div>
                    <p>
                        To make a payment, please follow the instructions included in the attached invoice. If you have any questions or need assistance with the payment process, please do not hesitate to contact our finance team.
                    </p>
                    <p>
                        Thank you for your business and for choosing <b>' . $companyNameNav . '</b>. We appreciate your continued support and look forward to serving you again in the future.
                    </p>
                    <div style="display:grid">
                        Best regards , <span><b>' . $companyNameNav . '</b></span>
                    </div>
                    
                    <p>
                    <a href="' . $invoicelink . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View Invoice</a>
                    
                    </p>
                    <p>
                    We would love to answer any questions you might have about us, so please check out the FAQ section in our website, or contact us on ' . $kam_email . '
                    </p>
                    </div>';


                    // console($msg);
                    // echo json_encode($msg);
                    // exit();
        $mail =  SendMailByMySMTPmailTemplate($to, $sub, $msg, null, $customerCode, 'salesInvoice', $invId, $invNo);
        global $current_userName;
        $whatsapparray = [];
        $whatsapparray['templatename'] = 'invoice_sent_msg';
        $whatsapparray['to'] = $customer_authorised_person_phone;
        $whatsapparray['customername'] = $customer_name;
        $whatsapparray['invoiceno'] = $invNo;
        $whatsapparray['invoicelink'] = $invoicelinkWhatsapp;
        $whatsapparray['quickcontact'] = null;
        $whatsapparray['current_userName'] = $current_userName;
        $whatsappreturn = SendMessageByWhatsappTemplate($whatsapparray);

        if ($mail == true) {
            $sql = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
                         SET
                             `mailStatus`='1',`invoiceStatus`='1' WHERE `so_invoice_id`='$invId'
             ";
            $upDated = $dbObj->queryUpdate($sql);
            if ($upDated['status'] == "success") {
                $res = [
                    "status" => "success",
                    "message" => "Invoice Accepted successfully"
                ];
                $ins = "INSERT INTO `" . ERP_INVOICE_MAIL_LOG . "` 
                             SET
                                 `company_id`='$company_id',
                                 `branch_id`='$branch_id',
                                 `location_id`='$location_id',
                                 `so_invoice_id`='$invId',
                                 `mailStatus`='1',
                                 `created_by`='$location_id',
                                 `updated_by`='$location_id' 
                 ";
                $dbObj->queryInsert($ins);
            }
        } else {
            $res = [
                "status" => "error",
                "message" => "failed to accept invoice"
            ];
        }
    } else {
        $res = [
            "status" => "warning",
            "message" => "Invoice Not Found"
        ];
    }
    echo json_encode($res);
}
else if($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act']="rejectInv"){
    $invId=$_GET['soInvId'];
    $invId=base64_decode($invId);
    $revRes=$reversePostingObj->reverseInvoice($invId);
    $sql = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
            SET
                 `invoiceStatus`='17' WHERE `so_invoice_id`='$invId' ";
    if($revRes['status'] == "success"){
        $res=["status"=>"warning","message"=>"Invoice Reversed Successfully"];
        $upDated = $dbObj->queryUpdate($sql);
        if($upDated['status']=="success"){
        $res=["status"=>"success","message"=>"Invoice Rejected Successfully"];
        }
    }else{
        $res=["status"=>"error", "message"=>"Invoice Rejected Failed"];
    }
}
