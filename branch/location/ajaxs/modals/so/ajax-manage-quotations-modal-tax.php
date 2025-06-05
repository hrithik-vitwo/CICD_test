<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
// require_once("../../../../../app/v1/functions/common/templates/template-quotation.controller.php");
require_once("../../../../../app/v1/functions/common/templates/template-quotation-tax.controller.php");
$headerData = array('Content-Type: application/json');

$dbObj = new Database();

$BranchSoObj = new BranchSo();
$companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];

$companyCurrency = $companyDetailsObj['company_currency'];
$companyCurrencyName = $BranchSoObj->fetchCurrencyIcon($companyCurrency)['data']['currency_name'];


if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'modalData') {

    $quotation_id = $_GET['quotation_id'];
    $cond = "AND quot.quotation_id ='" . $quotation_id . "'";

    $sql_list = "SELECT quot.*, cust.trade_name, cust.customer_code, cust.customer_gstin, cust.customer_currency, cust.customer_status, cust.customer_pan, custAddress.customer_address_building_no, custAddress.customer_address_flat_no, custAddress.customer_address_street_name, custAddress.customer_address_pin_code, custAddress.customer_address_location, custAddress.customer_address_district, custAddress.customer_address_state, cust.customer_authorised_person_email, cust.customer_authorised_person_phone,(SELECT file_name FROM `erp_attach_documents` WHERE refName='quotation-creation' AND ref_no='" . $quotation_id . "') as fileName FROM `erp_branch_quotations` AS quot LEFT JOIN erp_customer AS cust ON quot.customer_id = cust.customer_id LEFT JOIN `erp_customer_address` AS custAddress ON quot.customer_id = custAddress.customer_address_id WHERE 1 " . $cond . " AND quot.company_id='" . $company_id . "' AND quot.branch_id='" . $branch_id . "' AND quot.location_id='" . $location_id . "' ORDER BY quot.quotation_no DESC";

    $sqlMainQryObj = queryGet($sql_list);
    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $dynamic_data = [];
    $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
    $companyCurrency = $companyDetailsObj['company_currency'];
    $companyCurrencyName = $BranchSoObj->fetchCurrencyIcon($companyCurrency)['data']['currency_name'];

    if ($num_list > 0) {
        $dynamic_data = [];

        $customerAddress = $data['customer_address_building_no'] . ', ' . $data['customer_address_flat_no'] . ', ' . $data['customer_address_street_name'] . ', ' . $data['customer_address_pin_code'] . ', ' . $data['customer_address_location'] . ', ' . $data['customer_address_district'] . ', ' . $data['customer_address_state'];
        $itemDetails[$data['quotation_id']] = $BranchSoObj->getQuotationItems($data['quotation_id'])['data'];
        $customerDetails[$data['quotation_id']] = $BranchSoObj->fetchCustomerDetails($data['customer_id'])['data'][0];
        $items = [];
        $allSubTotal = 0;
        $totalDis = 0;
        // console($itemDetails[$data['quotation_id']]);
        foreach ($itemDetails[$data['quotation_id']] as $oneItem) {
            $deliverySchedule = $BranchSoObj->fetchBranchSoItemsDeliverySchedule2($oneItem['so_item_id'])['data'];
            $deliveryOBj = [];

            foreach ($deliverySchedule as $dSchedule) {
                $uomName = getUomDetail($oneItem['uom'])['data']['uomName'];
                $deliveryOBj[] = [
                    'delqnty' => $dSchedule['qty'],
                    'delstatus' => $dSchedule['deliveryStatus'],
                    'deldate' => $dSchedule['delivery_date'], 
                    'uomName' => $uomName
                ];
            }

            $gstAmount = 0;
            $subTotal = $oneItem['unitPrice'] * $oneItem['qty'];
            $discount = $oneItem['itemTotalDiscount'] + $oneItem['cashDiscountAmount'];
            $taxAbleAmount = $subTotal - $discount;
            if ($oneItem['tax'] == 0) {
                $itemTotalAmount = $taxAbleAmount;
            } else {
                $gstAmount = $oneItem['totalTax'];
                $itemTotalAmount = $taxAbleAmount + $gstAmount;
            }

            $allSubTotal += $subTotal;

            $stockQuery = queryGet("SELECT sum(itemQty) as stock FROM `erp_inventory_stocks_log` WHERE itemId='" . $oneItem['so_item_id'] . "' GROUP by itemId;");
            if ($stockQuery['numRows'] > 0) {
                $stock = $stockQuery['data']['stock'];
            } else {
                $stock = 0;
            }

            // $currencyQuery = queryGet("SELECT currency_name FROM `erp_currency_type` WHERE currency_id='" . $data['currency_id'] . "'");
            // if ($currencyQuery['numRows'] > 0) {
            //     $curName = $currencyQuery['data']['currency_name'];
            // } else {
            //     $curName = "N/A";
            // }

            $items[] = [
                "itemCode" => $oneItem['itemCode'],
                "itemName" => $oneItem['itemName'],
                "qty" => $oneItem['qty'],
                "unitPrice" => $oneItem['unitPrice'],
                "subTotal" => $subTotal,
                "total_discount" => $discount,
                "totalTax" => $oneItem['totalTax'],
                "tax" => $oneItem['tax'],
                "uomName" => $uomName,
                "hsnCode" => $oneItem['hsnCode'],
                "stock" => $stock,
                "taxAbleAmount" => $taxAbleAmount,
                "gstAmount" => $gstAmount,
                "itemTotalAmount" => $itemTotalAmount,
                "currency" => $curName,
                "delivery_obj" => $deliveryOBj
            ];
        }

        $navBtn = '';
        if ($data['approvalStatus'] == 17) { // rejcted
            $navBtn .= '<a class="nav-link approve-po btn btn-danger text-white float-right p-2" id="rejectedQuot"  role="" aria-controls="profile" aria-selected="false">Rejected</a>';
        } else if ($data['approvalStatus'] == 14) { // pending
            $navBtn .= '<a class="nav-link approve-po btn btn-success text-white float-right p-2" id="approveQuotation" data-no=' . base64_encode($data['quotation_no']) . ' data-id=' . base64_encode($data['quotation_id']) . ' role="" aria-controls="profile" aria-selected="false">Approve Quotation</a>';
            $navBtn .= '<a class="nav-link approve-po btn btn-danger text-white float-right p-2" id="rejectQuotation" data-no=' . base64_encode($data['quotation_no']) . ' data-id=' . base64_encode($data['quotation_id']) . ' role="" aria-controls="profile" aria-selected="false">Reject Quotation</a>';
        } else if ($data['approvalStatus'] == 10) { // close
            $navBtn .= '<a href="#" class="btn btn-danger pgi-create-btn border" id="closedQuotation"  title="Close Quotation"><i class="fa fa-times mr-2"></i> Close Quotation</a>';
        } else if ($data['approvalStatus'] == 11) { // approved
            $navBtn .= '<a class="nav-link approve-po btn btn-warning text-white float-right p-2" id="approvedQuot"  role="" aria-controls="profile" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Awaiting Customer Response</a>';
        } else { // main actions
            $navBtn .= '<a href="invoice-creation.php?quotation=' . base64_encode($data['quotation_id']) . '" class="btn btn-primary pgi-create-btn border" title="Create Invoice"><i class="fa fa-box mr-2"></i> Create Invoice</a>';
            $navBtn .= '<a href="sales_order_creation.php?quotation_to_so=' . base64_encode($data['quotation_id']) . '" class="btn btn-primary pgi-create-btn border" title="Create SO"><i class="fa fa-box mr-2"></i> Create SO</a>';
        }

        $navBar = '<div class="action-btns display-flex-gap create-delivery-btn-sales" id="action-navbar">' . $navBtn . '</div>';

        $dynamic_data = [
            "dataObj" => $data,
            "customer_address" => getCustomerPrimaryAddressById($data['customer_id']),
            "placeOfsupply" => getStateDetail($data['placeOfSupply'])['data']['gstStateName'],
            "currecy_name_words" => number_to_words_indian_rupees($data['totalAmount']),
            "so_IdBase" => base64_encode($data['so_id']),
            "created_by" => getCreatedByUser($data['created_by']),
            "created_at" => formatDateORDateTime($data['created_at']),
            "updated_by" => getCreatedByUser($data['updated_by']),
            "updated_at" => formatDateORDateTime($data['updated_at']),
            "allSubTotal" => $allSubTotal,
            "companyCurrencyName" => $companyCurrencyName,
            "companyCurrency" => getSingleCurrencyType($company_currency),
            "item_details" => $items,
            "taxComponents" => $data['taxComponents'],
            "countryCode" => $_SESSION['logedBranchAdminInfo']['companyCountry'],
            "taxName" => getTaxName($_SESSION['logedBranchAdminInfo']['companyCountry'])['data'],
            "navbar" => $navBar,
            "country_labels" => json_decode(getLebels($_SESSION['logedBranchAdminInfo']['companyCountry'])['data'])
        ];

        $res = [
            "status" => true,
            "sql" => $sqlMainQryObj['sql'],
            "msg" => "Success",
            "data" => $dynamic_data
        ];
    } else {
        $res = [
            "status" => false,
            "msg" => "Error!",
            "sql" => $sqlMainQryObj['sql']
        ];
    }

    echo json_encode($res);
} else if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET["act"] == "classicView") {
    $id = $_GET["quotation_id"];

    $countryCode = $_SESSION['logedBranchAdminInfo']['companyCountry'];



    $templateQuotationControllerTaxObj = new TemplateQuotationTaxController();
    $templateQuotationControllerTaxObj->printQuotation($id, $company_id, $branch_id, $location_id);



} else if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET["act"] == "approveQuot") {

    // $quotation_id = $_GET["qId"];
    // $cond = "AND quot.quotation_id ='" . $quotation_id . "'";
    // $sql_list = "SELECT quot.*, cust.trade_name, cust.customer_code, cust.customer_gstin, cust.customer_currency, cust.customer_status, cust.customer_pan, custAddress.customer_address_building_no, custAddress.customer_address_flat_no, custAddress.customer_address_street_name, custAddress.customer_address_pin_code, custAddress.customer_address_location, custAddress.customer_address_district, custAddress.customer_address_state, cust.customer_authorised_person_email, cust.customer_authorised_person_phone,cust.customer_authorised_person_name FROM `erp_branch_quotations` AS quot LEFT JOIN erp_customer AS cust ON quot.customer_id = cust.customer_id LEFT JOIN `erp_customer_address` AS custAddress ON quot.customer_id = custAddress.customer_address_id WHERE 1 " . $cond . " AND quot.company_id='" . $company_id . "' AND quot.branch_id='" . $branch_id . "' AND quot.location_id='" . $location_id . "' ORDER BY quot.quotation_no DESC;";
    // $status = 11;
    // $updateSql = "UPDATE `erp_branch_quotations` SET `approvalStatus`=$status WHERE `quotation_id`=$quotation_id";
    // $update = queryUpdate($updateSql);

    // if ($update['status'] == 'success') {
    //     $sqlMainQryObj = $dbObj->queryGet($sql_list);
    //     $data = $sqlMainQryObj['data'];


    //     // $check_service = queryGet("SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `ref_no` = '" . $po_no . "'", true);
    //     // foreach ($check_service['data'] as $data) {
    //     //     $s_po_id = $data['po_id'];
    //     //     $update_service = queryUpdate("UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`=$status WHERE `po_id`=$s_po_id");
    //     // }

    //     //  change  this thing to something
    //     $encodeQuot_id = base64_encode($quotation_id);

    //     $totalAmount = $data['totalAmount'];
    //     $postingDate = $data['posting_date'];
    //     $quotationNo = $data['quotation_no'];
    //     $userName = $data['customer_authorised_person_name'];
    //     $customer_authorised_person_email = $data['customer_authorised_person_email'];
    //     $customer_name = $data['trade_name'];
    //     $customerCode = $data['customer_code'];
    //     $gst = $data['customer_gstin'];
    //     $currencyName = getSingleCurrencyType($data['currency_id']);
    //     $quotationTotalAmount = $data['totalAmount'];
    //     // $creditPeriod=$data['credit_period'];

    //     // $duedate = date("Y-m-d", strtotime("+" . $creditPeriod . " days"));

    //     $to = $customer_authorised_person_email;
    //     $sub = 'Quotation ' . $quotationNo . ' - ' . $customer_name;
    //     $msg = '
    //     <div>
    //     <div><strong>Dear ' . $customer_name . ',</strong></div>
    //     <p>
    //         I hope this email finds you well. Thank you for considering our services/products.
    //         We appreciate the opportunity to provide you with a quotation. We have carefully reviewed your requirements, and we are confident that our offerings will meet and exceed your expectations.
    //     </p>
    //     <strong>
    //         Quotation details:
    //     </strong>
    //     <div style="display:grid">
    //         <span>
    //             Quotation Number: ' . $quotationNo . '
    //         </span>
    //         <span>
    //             Total Amount: <strong>' . $currencyName . decimalValuePreview($quotationTotalAmount) . '</strong>
    //         </span>
    //         <span>
    //             Total Amount In Word: <strong>' . number_to_words_indian_rupees($quotationTotalAmount) . ' ONLY</strong>
    //         </span>
    //     </div>
    //     <p>
    //         If you have any specific customization or additional requirements, please let us know, and we will be happy to provide you with an updated quotation tailored to your needs.
    //     </p>
    //     <p>
    //         Thank you for your business and for choosing <b>' . $company_name . '</b>. We appreciate your continued support and look forward to serving you again in the future.
    //     </p>
    //     <div style="display:grid">
    //         Best regards for, <span><b>' . $company_name . '</b></span>
    //     </div>
        
    //     <p>
    //     <a href="' . BASE_URL . 'branch/location/classic-view/invoice-preview-print-taxcomponents.php?quotation_id=' . $encodeQuot_id . '&company_id=' . $company_id . '&branch_id=' . $branch_id . '&location_id=' . $location_id . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View Quotation</a>
    //     </p>
    //     </div>';
    //     $mail = SendMailByMySMTPmailTemplate($to, $sub, $msg, null, $customerCode, 'salesQuotation', $quotation_id, $quotationNo);


    //     $res = [];
    //     if ($mail == true) {
    //         $res = [
    //             "status" => "success",
    //             "message" => "Email sent successfully"
    //         ];
    //     } else {
    //         $res = [
    //             "status" => "error",
    //             "message" => "Email does not sent"
    //         ];
    //     }
    //     echo json_encode($res);
    // }

    $id = $_GET["qId"];
    $res=$BranchSoObj->approveQuotationById($id);
    echo json_encode($res);
} else if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET["act"] == "rejectQuot") {

    $quotation_id = $_GET["qId"];
    $status = 17;
    $updateSql = "UPDATE `erp_branch_quotations` SET `approvalStatus`=$status WHERE `quotation_id`=$quotation_id";
    $update = queryUpdate($updateSql);
    $res = [];
    if ($update['status'] == 'success') {
        $res = ["status" => "success", "message" => "Quotation Rejected successfully"];
    } else {
        $res = ["status" => "error", "message" => "Something went wrong"];
    }
    echo json_encode($res);
}