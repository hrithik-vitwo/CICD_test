<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../../app/v1/functions/common/templates/template-sales-order.controller.php");
// require_once("../../../../../app/v1/functions/common/templates/template-debitnote.controller.php");
require_once("../../../../../app/v1/functions/common/templates/template-debitnote-tax.controller.php");
$headerData = array('Content-Type: application/json');
$branchSoObj = new BranchSo();
$tempObj = new TemplateDebitNoteTaxController();

if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'modaldata') {

    $dr_note_id = $_GET['dr_note_id'];

    $check_e_inv = queryGet("SELECT count(`id`) AS e_inv_count FROM `erp_e_invoices` WHERE `invoice_id` = $dr_note_id");
    $sql = "SELECT dr.*,eInv.`ack_no`, eInv.`ack_date`, eInv.`irn`, eInv.`signed_qr_code` FROM `erp_debit_note` as dr LEFT JOIN `erp_e_invoices` as eInv ON dr.dr_note_id = eInv.invoice_id WHERE dr.dr_note_id =$dr_note_id";
    // console(queryGet($sql));
    $oneList = queryGet($sql)['data'];

    $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
    $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
    $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
    $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
    $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
    $companyData = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);

    $currencyDetails = $branchSoObj->fetchCurrencyIcon($companyData['company_currency'])['data'];
    $companyCurrencyName = $currencyDetails['currency_name'];

    $itemDetailsObj = queryGet("SELECT * FROM `debit_note_item` AS dr_item, `erp_inventory_items` AS item  WHERE item.itemId=dr_item.item_id AND `debit_note_id` = '" . $dr_note_id . "'", true);

    $itemDetails = $itemDetailsObj['data'];

    $contactDetails = queryGet("SELECT `contact_details` FROM `erp_debit_note` WHERE `dr_note_id`='" . $dr_note_id . "'")['data']['contact_details'];


    $bill_id = $oneList['debitNoteReference'];
    $debitor_type = $oneList['debitor_type'];
    if ($debitor_type == 'customer') {
        $customerDetailsObj = queryGet("SELECT * FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`=" . $oneList['party_id'] . "");
        $customerData = $customerDetailsObj['data'];
        // console($customerDetailsObj);
        $branchGstin = substr($companyData['branch_gstin'], 0, 2);
        $customerGstin = substr($customerData['customer_gstin'], 0, 2);
        $conditionGST = $branchGstin == $customerGstin;

        $iv = queryGet("SELECT * FROM `erp_branch_sales_order_invoices` WHERE `so_invoice_id`=$bill_id");
        // console($iv);
        $ref = $iv['data']['invoice_no'];
        $iv_date = explode(" ", $iv['data']['created_at'], 1);

        $source_address_sql = queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_address_id`= '" . $oneList['billing_address'] . "' ")['data'];
        // console($source_address_sql);
        // exit();

        $source_address = $source_address_sql['customer_address_building_no'] . ' , ' . $source_address_sql['customer_address_flat_no'] . ' , ' . $source_address_sql['customer_address_street_name'] . ' , ' . $source_address_sql['customer_address_pin_code'] . ' , ' . $source_address_sql['customer_address_location'] . ' , ' . $source_address_sql['customer_address_city'] . ' , ' . $source_address_sql['customer_address_district'] . ' , ' . $source_address_sql['customer_address_country'] . ' , ' . $source_address_sql['customer_address_state'];
        // console($iv_date);

        $destination_address_sql = queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_address_id`= '" . $oneList['shipping_address'] . "' ")['data'];

        $destination_address = $destination_address_sql['customer_address_building_no'] . ' , ' . $destination_address_sql['customer_address_flat_no'] . ' , ' . $destination_address_sql['customer_address_street_name'] . ' , ' . $destination_address_sql['customer_address_pin_code'] . ' , ' . $destination_address_sql['customer_address_location'] . ' , ' . $destination_address_sql['customer_address_city'] . ' , ' . $destination_address_sql['customer_address_district'] . ' , ' . $destination_address_sql['customer_address_country'] . ' , ' . $destination_address_sql['customer_address_state'];
    } else {
        $customerDetailsObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `vendor_id`=" . $oneList['party_id'] . "");
        $customerData = $customerDetailsObj['data'];
        // console($customerDetailsObj);
        $branchGstin = substr($companyData['branch_gstin'], 0, 2);
        $customerGstin = substr($customerData['vendor_gstin'], 0, 2);
        $conditionGST = $branchGstin == $customerGstin;

        $iv = queryGet("SELECT * FROM `erp_grninvoice` WHERE `grnIvId`=$bill_id");
        // console($iv);
        $ref = $iv['data']['invoice_ number'];
        $iv_date = explode(" ", $iv['data']['created_at'], 1);

        // console($iv_date);
        $source_address_sql = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id`= '" . $oneList['billing_address'] . "' ");
        // console($source_address_sql);

        $source_address = $source_address_sql['data']['othersLocation_name'] . ', ' . $source_address_sql['data']['othersLocation_building_no'] . ', ' . $source_address_sql['data']['othersLocation_flat_no'] . ', ' . $source_address_sql['data']['othersLocation_street_name'] . ', ' . $source_address_sql['data']['othersLocation_pin_code'] . ', ' . $source_address_sql['data']['othersLocation_location'] . ', ' . $source_address_sql['data']['othersLocation_city'] . ', ' . $source_address_sql['data']['othersLocation_district'] . ', ' . $source_address_sql['data']['othersLocation_state'];

        $destination_address_sql = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id`= '" . $oneList['shipping_address'] . "' ");
        // console($source_address_sql);

        $destination_address = $destination_address_sql['data']['othersLocation_name'] . ', ' . $destination_address_sql['data']['othersLocation_building_no'] . ', ' . $destination_address_sql['data']['othersLocation_flat_no'] . ', ' . $destination_address_sql['data']['othersLocation_street_name'] . ', ' . $destination_address_sql['data']['othersLocation_pin_code'] . ', ' . $destination_address_sql['data']['othersLocation_location'] . ', ' . $destination_address_sql['data']['othersLocation_city'] . ', ' . $destination_address_sql['data']['othersLocation_district'] . ', ' . $destination_address_sql['data']['othersLocation_state'];
    }

    $branchGstin = substr($companyData['branch_gstin'], 0, 2);
    $customerGstin = substr($customerData['customer_gstin'], 0, 2);
    $conditionGST = $branchGstin == $customerGstin;
    $totalTaxAmt = 0;
    $subTotalAmt = 0;
    $allSubTotalAmt = 0;
    $totalDiscountAmt = 0;
    $totalAmt = 0;
    $totaligst = 0;
    $totalcgst = 0;
    $totalsgst = 0;
   
    $items = [];
    $sl = 0;
    foreach ($itemDetails as $item) {
        $uom = queryGet("SELECT `uomName` FROM `erp_inventory_mstr_uom` WHERE uomID='" . $item['baseUnitMeasure'] . "'");
        $uomName = $uom['data']['uomName'];

        $totalTaxAmt += $item['item_tax'];
        $allSubTotalAmt += $item['unitPrice'] * $item['qty'];
        $totalDiscountAmt += $item['itemTotalDiscount'];
        $subTotalAmt += ($item['item_qty'] * $item['item_rate']);
        $totalAmt += $item['item_amount'];
        $taxbleAmount = $item['item_qty'] * $item['item_rate']- $item['discount_amount'];
        $gstamt = ($taxbleAmount * $item['item_tax']) / 100;

        $total_discount+= $item['discount_amount'];
        $items[] = [
            "slNo" => $sl,
            "itemName" => $item['itemName'],
            "itemCode" => $item['itemCode'],
            "hsnCode" => $item['hsnCode'],
            "item_qty" => $item['item_qty'],
            "uomName" => $uomName ?? "-",
            "item_rate" => ($item['item_rate']),
            "item_dis"=> $item['discount_amount'],
            "taxbleAmount" => $taxbleAmount,
            "item_tax" => $item['item_tax'],
            "item_amount" => $item['item_amount'],
            "gstamt" => $gstamt
            // "uomSql" => $uom['sql']

        ];
    }

    $partydetails = [
        "debitor_type" => $debitor_type,
        "customerData" => $customerData,
        "destination_address" => $destination_address,
        "source_address" => $source_address
    ];

    $dynamic_data = [
        "items" => $items,
        'crNoteobj' => $oneList,
        "ref" => $ref,
        "companyCurrency" => getSingleCurrencyType($company_currency),
        "partydetails" => $partydetails,
        "subTotalAmt" => $subTotalAmt,
        "total_discount"=> $total_discount,
        "created_by" => getCreatedByUser($oneList['created_by']),
        "created_at" => formatDateORDateTime($oneList['created_at']),
        "updated_by" => getCreatedByUser($oneList['updated_by']),
        "updated_at" => formatDateORDateTime($oneList['updated_at']),
        "e_inv_count" => $check_e_inv['data']['e_inv_count'],
        "irn" => $oneList['irn'],
        "taxComponents" => $oneList['taxComponents'],
        "countryCode" => $_SESSION['logedBranchAdminInfo']['companyCountry'],
        "taxName" => getTaxName($_SESSION['logedBranchAdminInfo']['companyCountry'])['data'],
        "country_labels" => json_decode(getLebels($_SESSION['logedBranchAdminInfo']['companyCountry'])['data'])

    ];

    $res = [
        "status" => true,
        "msg" => "Success",
        "data" => $dynamic_data
    ];

    echo json_encode($res);

} else if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'classicView') {
    $tempObj->printDebitNotes($_GET['dr_note_id']);
}
