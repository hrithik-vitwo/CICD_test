<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../../app/v1/functions/common/templates/template-quotation.controller.php");
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
            $navBtn .= '<a href="direct-create-invoice.php?quotation=' . base64_encode($data['quotation_id']) . '" class="btn btn-primary pgi-create-btn border" title="Create Invoice"><i class="fa fa-box mr-2"></i> Create Invoice</a>';
            $navBtn .= '<a href="direct-create-invoice.php?quotation_to_so=' . base64_encode($data['quotation_id']) . '" class="btn btn-primary pgi-create-btn border" title="Create SO"><i class="fa fa-box mr-2"></i> Create SO</a>';
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
            "navbar" => $navBar
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
} else if ($_SERVER["REQUEST_METHOD"] == "GET" &&  $_GET["act"] == "classicView") {
    $id = $_GET["quotation_id"];
    $templateQuotationControllerObj = new TemplateQuotationController();
    $templateQuotationControllerObj->printQuotation($id, $company_id, $branch_id, $location_id);
} else if ($_SERVER["REQUEST_METHOD"] == "GET" &&  $_GET["act"] == "approveQuot") {
    $id = $_GET["qId"];
    $res=$BranchSoObj->approveQuotationById($id);
    echo json_encode($res);
} else if ($_SERVER["REQUEST_METHOD"] == "GET" &&  $_GET["act"] == "rejectQuot") {
    $id = $_GET["qId"];
    $res = $BranchSoObj->rejectQuotationById($id);
    echo json_encode($res);
}
