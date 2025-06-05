<?php
require_once "func-brunch-so-controller.php";
 require_once "func-journal.php";


 class SubscriptionController extends BranchSo
{

    function subscriptionInvoice()
    {
       //echo "alright";

      
       // global $dbCon;
        $returnData = [];

        


        $todayDate = date("Y-m-d");
        $subscriptionInvoiceData = queryGet("SELECT * FROM `" . ERP_BRANCH_SUBSCRIPTION_INVOICE . "` WHERE next_trigger_date='$todayDate'", true);
       //console($subscriptionInvoiceData);

        if ($subscriptionInvoiceData['status'] == "success") {
            foreach ($subscriptionInvoiceData['data'] as $one) {
                if ($todayDate <= $one['end_on'] || $one['end_on'] == "") {
                    $soDetailsObj = queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE so_id='" . $one['so_id'] . "'");
                   // console($soDetailsObj);
                    $soDetails = $soDetailsObj['data'];
                //    console($soDetails);

                    $soItemDetailsObj = queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` WHERE so_id='" . $soDetails['so_id'] . "'", true);
                    // console($soItemDetailsObj);
                    // console($soDetails['so_id']);
                    //exit();
                   //console($soItemDetailsObj['data']);
                    $POST['listItem'] = $soItemDetailsObj['data'];
                    $POST['iv_varient'] = $one['invoice_no_variant'];
                    $POST['invNo'] = "INV" . rand(0000, 9999);    //Ramen questions
                    $POST['customerId'] = $soDetails['customer_id'];
                    $POST['creditPeriod'] = $soDetails['credit_period'];
                    $POST['invoiceDate'] = $one['next_trigger_date'];
                    $POST['so_number'] = $soDetails['so_number'];
                    $POST['profit_center'] = $soDetails['profit_center'];
                    $POST['kamId'] = $soDetails['kamId'];
                    $POST['so_to_invoice'] = base64_encode($soDetails['so_id']) ;
                    $POST['curr_rate'] = $soDetails['conversion_rate'];
                    $POST['currency'] = $soDetails['currency_name'];

                    $POST['totalItems'] = $soDetails['totalItems'] ?? 0;
                    $POST['grandTotalDiscountAmtInp'] = $soDetails['totalDiscount'] ?? 0;
                    $POST['grandTotalCashDiscountAmtInp'] = $soDetails['totalCashDiscount'] ?? 0;
                    
                    $POST['grandTaxAmtInp'] = $soDetails['totalTax'] ?? 0;
                    $POST['cgst'] = $soDetails['cgst'] ?? 0;
                    $POST['sgst'] = $soDetails['sgst'] ?? 0;
                    $POST['igst'] = $soDetails['igst'] ?? 0;
                    $POST['grandTotalAmtInp'] = $soDetails['totalAmount'] ?? 0;
                    $POST['grandSubTotalAmtInp'] =$POST['grandTotalAmtInp'] - ($POST['grandTaxAmtInp']+$POST['grandTotalDiscountAmtInp']+$POST['grandTotalCashDiscountAmtInp'])  ?? 0;
                    $POST['customer_billing_address'] = $soDetails['billingAddress'];
                    $POST['customer_shipping_address'] = $soDetails['shippingAddress'];
                    $company_id = $soDetails['company_id'];
                     $branch_id = $soDetails['branch_id'];
                     $location_id = $soDetails['location_id'];

                  //  console($POST);
                  $company_details = [];
                  $company_details['company_id'] = $company_id;
                  $company_details['branch_id'] = $branch_id;
                  $company_details['location_id'] = $location_id;
                  $company_details['created_by'] = 'auto';
                  $company_details['updated_by'] = 'auto';


                    $invpostreturn = $this->insertBranchDirectInvoice($POST,null, $company_details);
                    if ($invpostreturn['status'] == "success") {
                        echo 'ok';
                        $days = $one['repeat_every'];
                        $nextMonth = date('Y-m-d', strtotime("+$days days"));

                        $subs = "UPDATE `" . ERP_BRANCH_SUBSCRIPTION_INVOICE . "` SET `next_trigger_date`='$nextMonth' WHERE so_id='" . $soDetails['so_id'] . "'";
                        $data = queryUpdate($subs);
                        $returnData['message'] = "invoice sent successfully";
                        // return [
                        //     "status" => "success",
                        //     "message" => "success",
                        //     "sql" => $subs
                        // ];
                    } else {
                       // echo 'not';
                        $returnData['message'] = "somthing went wrong!";
                        // return [
                        //     "status" => "warning",
                        //     "message" => "warning",
                        //     "post" => $POST,
                        //     "resp" => $invpostreturn
                        // ];
                    }
                } else {
                    $returnData['message'] = "No subscribtion found in the record!";
                    // return [
                    //     "status" => "warning",
                    //     "message" => "No subscribtion found in the record!"
                    // ];
                }
            }
        } else {
            return [
                "status" => "warning",
                "message" => "No subscribtion date found in the record!",
                "sql" => $subscriptionInvoiceData,
            ];
        }
        return $returnData;
    }
   
 }

?>