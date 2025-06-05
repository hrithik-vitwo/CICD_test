<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../../app/v1/functions/common/templates/template-sales-order.controller.php");
$headerData = array('Content-Type: application/json');

$BranchSoObj = new BranchSo();
$templateSalesOrderControllerObj = new TemplateSalesOrderController();
$companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];

$companyCurrency = $companyDetailsObj['company_currency'];
$companyCurrencyName = $BranchSoObj->fetchCurrencyIcon($companyCurrency)['data']['currency_name'];


if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $so_no = $_GET['so_no'];
    $cond = "AND so.so_number ='" . $so_no . "'";

    $sql_list = "SELECT so.*, cust.trade_name, cust.customer_code, cust.customer_gstin,cust.customer_pan,cust.customer_currency, cust.customer_status, custInvoiceLog.placeOfSupply, custAddress.customer_address_building_no, custAddress.customer_address_flat_no, custAddress.customer_address_street_name, custAddress.customer_address_pin_code, custAddress.customer_address_location, custAddress.customer_address_district, custAddress.customer_address_state, kam.kamName,func.functionalities_name , custInvoiceLog.complianceInvoiceType,cust.customer_authorised_person_email,
    cust.customer_authorised_person_phone FROM `erp_branch_sales_order` AS so 
    LEFT JOIN erp_customer AS cust ON so.customer_id = cust.customer_id LEFT JOIN `erp_customer_invoice_logs` AS custInvoiceLog ON so.so_number = custInvoiceLog.ref_no LEFT JOIN `erp_customer_address` as custAddress ON so.customer_id = custAddress.customer_address_id LEFT JOIN `erp_kam` as kam On so.kamId = kam.kamId 
    LEFT JOIN erp_company_functionalities as func ON so.profit_center = func.functionalities_id WHERE 1 " . $cond . " AND approvalStatus=9  AND so.company_id='" . $company_id . "'  AND so.branch_id='" . $branch_id . "'   AND so.location_id='" . $location_id . "'  AND so.status !='deleted'  ORDER BY so.so_id DESC;";

    $sqlMainQryObj = queryGet($sql_list);
    // console($sqlMainQryObj);
    
    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $dynamic_data = [];

    if ($num_list > 0) {
        $dynamic_data = [];

        // foreach ($sqldata as $data) {
            $itemDetails[$data['so_id']] = $BranchSoObj->fetchBranchSoItems($data['so_id'])['data'];
            $customerDetails[$data['so_id']] = $BranchSoObj->fetchCustomerDetails($data['customer_id'])['data'][0];
            $customerAddressDetails = $BranchSoObj->fetchCustomerAddressDetails($data['customer_id'])['data'][0];

            $items = [];
            $allSubTotal=0;
            $totalDis=0;


            foreach ($itemDetails[$data['so_id']] as $oneItem) {
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
                
                $gstAmount=0;
                $subTotal = $oneItem['unitPrice'] * $oneItem['qty'];
                $discount=$oneItem['itemTotalDiscount']+$oneItem['cashDiscountAmount'];
                $taxAbleAmount = $subTotal - $discount;
                if($oneItem['tax']==0){
                    $itemTotalAmount = $taxAbleAmount;
                }else{
                 $gstAmount = ($taxAbleAmount*$oneItem['tax'])/100;
                $itemTotalAmount = $taxAbleAmount + $gstAmount;
                }


                $allSubTotal+=$subTotal;

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
                    "qty" => decimalQuantityPreview($oneItem['qty']),
                    "unitPrice" => decimalValuePreview($oneItem['unitPrice']),
                    "subTotal" => decimalValuePreview($subTotal),
                    "total_discount" => decimalValuePreview($discount),
                    "totalTax" => decimalValuePreview($oneItem['totalTax']),
                    "tax" => decimalQuantityPreview($oneItem['tax']),
                    "uomName" => $uomName,
                    "hsnCode" => $oneItem['hsnCode'],
                    "stock" => $stock,
                    "taxAbleAmount" => decimalValuePreview($taxAbleAmount),
                    "gstAmount" => decimalValuePreview($gstAmount),
                    "itemTotalAmount" => decimalValuePreview($itemTotalAmount),
                    "currency" => $data['currency_name'],
                    "delivery_obj" => $deliveryOBj
                ];
            }

            $navBtn = "";

            if ($data['approvalStatus'] == 9 || $data['approvalStatus'] == 11) {
                if ($data['goodsType'] == 'material' || $data['goodsType'] == 'both') {
                    $navBtn .= '<div class="d-flex btnHideShow ?>">
                                    <a href="delivery-actions.php?create-sales-order-delivery=' . base64_encode($data['so_id']) . '" class="btn btn-primary "><i class="fa fa-plus mr-2"></i>Create Delivery</a>
                                    <a title="Create Invoice" href="direct-create-invoice.php?so_to_invoice=' . base64_encode($data['so_id']) . '" class="btn btn-primary "><i class="fa fa-plus mr-2"></i>Create Invoice</a>
                                </div>';
                } else if ($data['goodsType'] == 'project') {
                    $navBtn .= '<a style="display: none;" title="Create Invoice" href="direct-create-invoice.php?joborder_to_invoice=' . base64_encode($data['so_id']) . '" class="btn btn-primary  disabled-link"><i class="fa fa-plus mr-2"></i>Create Invoice</a>';
                } else {
                    $navBtn .= '<a title="Create Invoice" href="direct-create-invoice.php?so_to_invoice=' . base64_encode($data['so_id']) . '" class="btn btn-primary "><i class="fa fa-plus"></i>Create Invoice</a>';
                }
            } else if ($data['approvalStatus'] == 12 || $data['approvalStatus'] == 14) {
                $navBtn .= '<button class="btn btn-warning "><i class="fa fa-clock"></i> Pending For Approval</button>';
            } else if ($data['approvalStatus'] == 10) {
                if ($data['goodsType'] != 'project') {
                    $navBtn .= '<button class="btn btn-secondary ">Delivery Closed</button>';
                }
            }
            if ($data['approvalStatus'] != 10) {
                $navBtn .=   '<button class="btn btn-danger closeSoBtn" id="closeSoBtn_' . $data['so_id'] . '_' . $data['so_number'] . '"  title="Close SO"><i class="fa fa-times mr-2"></i> Close SO</button>';
            }

            $navbar = '<div class="action-btns display-flex-gap create-delivery-btn-sales" id="action-navbar">' . $navBtn . '</div>';

            $customerAddress = $data['customer_address_building_no'] . ', ' . $data['customer_address_flat_no'] . ', ' . $data['customer_address_street_name'] . ', ' . $data['customer_address_pin_code'] . ', ' . $data['customer_address_location'] . ', ' . $data['customer_address_district'] . ', ' . $data['customer_address_state'];

            $dynamic_data = [
                "dataObj" => $data,
                "customer_address" => getCustomerPrimaryAddressById($data['customer_id']),
                "placeOfsupply" => getStateDetail($data['placeOfSupply'])['data']['gstStateName'],
                "currecy_name_words" => number_to_words_indian_rupees($data['totalAmount']),
                "so_IdBase" => base64_encode($data['so_id']),
                "item_details" => $items,
                "created_by" => getCreatedByUser($data['created_by']),
                "created_at" => formatDateORDateTime($data['created_at']),
                "updated_by" => getCreatedByUser($data['updated_by']),
                "updated_at" => formatDateORDateTime($data['updated_at']),
                "navbar" => $navbar
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
}
