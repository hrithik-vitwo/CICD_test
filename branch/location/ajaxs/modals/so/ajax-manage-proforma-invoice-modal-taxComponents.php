<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../../app/v1/functions/common/templates/template-sales-order-controller-taxComponents.php");
$headerData = array('Content-Type: application/json');

$BranchSoObj = new BranchSo();
$ItemsObj = new ItemsController();
$templateSalesOrderControllerObj = new TemplateSalesOrderControllerTaxComponents();

if ($_SERVER["REQUEST_METHOD"] == "GET" &&  $_GET["act"] == "modalData") {
    $proforma_invoices_id = $_GET['proforma_invoice_id'];
    $sql_list = "SELECT proformaInv.*, cust.trade_name, cust.customer_code, cust.customer_gstin,cust.customer_pan, cust.customer_currency, cust.customer_status, custAddress.customer_address_building_no, custAddress.customer_address_flat_no, custAddress.customer_address_street_name, custAddress.customer_address_pin_code, custAddress.customer_address_location, custAddress.customer_address_district, custAddress.customer_address_state, kam.kamName, func.functionalities_name, custInvoiceLog.complianceInvoiceType, cust.customer_authorised_person_email, cust.customer_authorised_person_phone FROM `erp_proforma_invoices` AS proformaInv LEFT JOIN erp_customer AS cust ON proformaInv.customer_id = cust.customer_id LEFT JOIN `erp_customer_invoice_logs` AS custInvoiceLog ON proformaInv.invoice_no = custInvoiceLog.ref_no LEFT JOIN `erp_customer_address` AS custAddress ON proformaInv.customer_id = custAddress.customer_address_id LEFT JOIN `erp_kam` AS kam ON proformaInv.kamId = kam.kamId LEFT JOIN erp_company_functionalities AS func ON proformaInv.profit_center = func.functionalities_id WHERE proformaInv.proforma_invoice_id=$proforma_invoices_id AND proformaInv.company_id=$company_id AND proformaInv.branch_id=$branch_id  AND proformaInv.location_id=$location_id";
    $sqlMainQryObj = queryGet($sql_list);

    // console($sqlMainQryObj);
    // exit();

    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];
    $countrycode = $_SESSION["logedBranchAdminInfo"]["companyCountry"];
    $taxtype_sql=queryGet("SELECT * FROM `erp_tax_rulebook` WHERE `country_id` =".$countrycode."");
    $taxName=$taxtype_sql['data'];
    $dynamic_data = [];

    if ($num_list > 0) {
        $dynamic_data = [];

        // Invoice Specific Information
         // fetch company details
         
         //  $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
         //  $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
         //  $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
         //  $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
         //  $companyData = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);
         
         $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
         $companyCurrency = $companyDetailsObj['company_currency'];
         $companyCurrencyName = $BranchSoObj->fetchCurrencyIcon($companyCurrency)['data']['currency_name'];
 
         $exchange=[];

        //  echo $companyCurrencyName;
        //  echo "ok";
        //  echo $data['currency_name'];

         if ($data['currency_name'] != $companyCurrencyName) {
             $exchangeRate = $data['all_total_amt'] * $data['conversion_rate'];
             $exchange['exchangeNum']=number_format($exchangeRate,2);
             $exchange['exchangeWord']=number_to_words_indian_rupees($exchangeRate);
             $exchange['currencyName']=$data['currency_name'];
 
         }


        // Invoice Specific Information End

        // foreach ($sqldata as $data) {
        $customerAddress = $data['customer_address_building_no'] . ', ' . $data['customer_address_flat_no'] . ', ' . $data['customer_address_street_name'] . ', ' . $data['customer_address_pin_code'] . ', ' . $data['customer_address_location'] . ', ' . $data['customer_address_district'] . ', ' . $data['customer_address_state'];
        $itemDetails = $BranchSoObj->fetchProformaInvoiceItems($proforma_invoices_id)['data'];        

        $customerDetails[$data['so_id']] = $BranchSoObj->fetchCustomerDetails($data['customer_id'])['data'][0];
        $items = [];
        $allSubTotal = 0;
        $totalDis = 0;

        foreach ($itemDetails as $oneItem) {
            // console($oneItem);
            // $deliverySchedule = $BranchSoObj->fetchBranchSoItemsDeliverySchedule2($oneItem['so_item_id'])['data'];
            // $deliveryOBj = [];

            // foreach ($deliverySchedule as $dSchedule) {
            //     $uomName = getUomDetail($oneItem['uom'])['data']['uomName'];
            //     $deliveryOBj[] = [
            //         'delqnty' => $dSchedule['qty'],
            //         'delstatus' => $dSchedule['deliveryStatus'],
            //         'deldate' => $dSchedule['delivery_date'],
            //         'uomName' => $uomName
            //     ];
            // }

            $gstAmount = 0;
            $subTotal = $oneItem['unitPrice'] * $oneItem['qty'];
            $discount=$oneItem['itemTotalDiscount']+$oneItem['cashDiscountAmount'];
            $taxAbleAmount = $subTotal - $discount;
            if ($oneItem['tax'] == 0) {
                $itemTotalAmount = $taxAbleAmount;
            } else {
                $gstAmount = ($taxAbleAmount * $oneItem['tax']) / 100;
                $itemTotalAmount = $taxAbleAmount + $gstAmount;
            }

            $allSubTotal += $subTotal;

            //                 // exit();

            $stockQuery = queryGet("SELECT sum(itemQty) as stock FROM `erp_inventory_stocks_log` WHERE itemId='" . $oneItem['so_item_id'] . "' GROUP by itemId;");
            if ($stockQuery['numRows'] > 0) {
                $stock = $stockQuery['data']['stock'];
            } else {
                $stock = 0;
            }

            $currencyQuery = queryGet("SELECT currency_name FROM `erp_currency_type` WHERE currency_id='" . $data['customer_currency'] . "'");
            if ($currencyQuery['numRows'] > 0) {
                $curName = $currencyQuery['data']['currency_name'];
            } else {
                $curName = "N/A";
            }

            $items[] = [
                "itemCode" => $oneItem['itemCode'],
                "itemName" => $oneItem['itemName'],
                "qty" => $oneItem['qty'],
                "unitPrice" => number_format($oneItem['unitPrice'], 2),
                "subTotal" => number_format($subTotal, 2),
                "total_discount" => $discount,
                "totalTax" => $oneItem['totalTax'],
                "tax" => $oneItem['tax'],
                "uomName" => $uomName,
                "hsnCode" => $oneItem['hsnCode'],
                "stock" => $stock,
                "taxAbleAmount" => $taxAbleAmount,
                "gstAmount" => $gstAmount,
                "itemTotalAmount" => $itemTotalAmount,
                // "currency" => $curName,
                "currency" => $data['currency_name'],
                "taxAbleAmount" => $taxAbleAmount,
                "delivery_obj" => $deliveryOBj
            ];
        }


        // $classic_view=  $templateSalesOrderControllerObj->printSalesOrder($data['so_id']);

        $dynamic_data = [
           "gstName"=>$taxName['tax_name'],
            "dataObj" => $data,
            "customer_address" => getCustomerPrimaryAddressById($data['customer_id']),
            "placeOfsupply" => getStateDetail($data['placeOfSupply'])['data']['gstStateName'],
            "currecy_name_words" => number_to_words_indian_rupees($data['all_total_amt']),
            "so_IdBase" => base64_encode($data['so_id']),
            "ccode" => $data['invoice_no'],
            "created_by" => getCreatedByUser($data['created_by']),
            "created_at" => formatDateORDateTime($data['created_at']),
            "updated_by" => getCreatedByUser($data['updated_by']),
            "updated_at" => formatDateORDateTime($data['updated_at']),
            "bashProformaId" =>base64_encode($data['proforma_invoice_id']),
            "companyCurrencyName"=>$companyCurrencyName,
            "allSubTotal" => $allSubTotal,
            "item_details" => $items,
            "exchange" => $exchange
            // "navbar" => $navbar

        ];
        // }

        $res = [
            "status" => true,
            "msg" => "Success",
            "data" => $dynamic_data
        ];
    } else {
        $res = [
            "status" => false,
            "msg" => "Error!",
            "sql" => $sqlMainQryObj
        ];
    }

    echo json_encode($res);
} else if ($_SERVER["REQUEST_METHOD"] == "GET" &&  $_GET["act"] == "classicView") {
    $Id = $_GET["proforma_invoice_id"];
    $templateSalesOrderControllerObj->printSalesOrderProformaTaxComponents($Id);
}
