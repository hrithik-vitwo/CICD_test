<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-po-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../../app/v1/functions/common/templates/template-purchase-order-tax.php");

$headerData = array('Content-Type: application/json');
$dbObj = new Database();
$BranchPoObj = new BranchPo();
$ItemsObj = new ItemsController();
$templatePoTaxObj = new TemplatePoControllerTax();


if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'modaldata') {
    $poId = $_GET['po_id'];
    $poIdBash = base64_encode($poId);
    $cond = "po.company_id=$company_id AND po.branch_id=$branch_id AND po.location_id=$location_id AND po.po_id=$poId AND po.status!='deleted'";
    $sql_list = "SELECT po.*, ven.vendor_code, ven.vendor_pan, ven.vendor_currency, ven.vendor_gstin, ven.trade_name AS vendor_name, ven.vendor_authorised_person_email AS vendor_email, ven.vendor_id, ven.vendor_authorised_person_phone AS vendor_phone, addr.* FROM `erp_branch_purchase_order` AS po JOIN `erp_vendor_details` AS ven ON ven.vendor_id = po.vendor_id LEFT JOIN `erp_vendor_bussiness_places` AS addr ON addr.vendor_id = ven.vendor_id AND addr.vendor_business_primary_flag = 1 WHERE $cond";

    $sqlMainQryObj = $dbObj->queryGet($sql_list);

    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $dynamic_data = [];

    if ($num_list > 0) {
        $dynamic_data = [];

        $itemDetails = $BranchPoObj->fetchBranchPoItems($poId)['data'];

        $items = [];
        $allSubTotal = 0;
        $totalDis = 0;

        $termsandcondqry = queryGet("SELECT * FROM `erp_applied_terms_and_conditions` WHERE slug='po' AND slug_id=" . $poId . "")['numRows'];
        $enabilitiesObj = queryGet("SELECT `company_terms_and_cond`,`print_terms_and_cond` FROM `erp_company_enabilities` WHERE `company_id`=" . $company_id . "")['data'];
        $checkcompantTC = $enabilitiesObj['company_terms_and_cond'];
        $printChkTC= $enabilitiesObj['print_terms_and_cond'];
        $showChkbox = 0;
        if ($termsandcondqry > 0 && $checkcompantTC == 1) {
            $showChkbox = 1;
        }

        foreach ($itemDetails as $oneItem) {
            // console($oneItem);

            $gstAmount = 0;
            $itemTotalAmount = 0;
            $subTotal = $oneItem['unitPrice'] * $oneItem['qty'];
            // $taxAbleAmount = $subTotal - $oneItem['itemTotalDiscount'];
            if ($oneItem['gst'] == 0) {
                $itemTotalAmount = $subTotal;
            } else {
                $gstAmount = ($subTotal * $oneItem['gst']) / 100;
                $itemTotalAmount = $subTotal + $gstAmount;
            }

            $allSubTotal += $subTotal;

            $items[] = [
                "itemCode" => $oneItem['itemCode'],
                "itemName" => $oneItem['itemName'],
                "qty" => decimalQuantityPreview($oneItem['qty']),
                "remainingQty" => decimalQuantityPreview($oneItem['remainingQty']),
                "unitPrice" => decimalValuePreview($oneItem['unitPrice']),
                "subTotal" => decimalValuePreview($subTotal),
                "tax" => decimalQuantityPreview($oneItem['gst']),
                "uomName" => $oneItem['uom'],
                "gstAmount" => decimalValuePreview($gstAmount),
                "itemTotalAmount" => decimalValuePreview($itemTotalAmount)
            ];
        }


        $addressSql = $dbObj->queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE othersLocation_id='" . $data['bill_address'] . "'")['data'];

        $billAddress = $addressSql['othersLocation_building_no'] . ', ' . $addressSql['othersLocation_flat_no'] . ', ' . $addressSql['othersLocation_street_name'] . ', ' . $addressSql['othersLocation_pin_code'] . ', ' . $addressSql['othersLocation_location'] . ', ' . $addressSql['othersLocation_district'] . ', ' . $addressSql['othersLocation_city'] . ' ,' . $addressSql['othersLocation_state'];

        $addressSql = $dbObj->queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE othersLocation_id='" . $data['ship_address'] . "'")['data'];
        $shipAddresss = $addressSql['othersLocation_building_no'] . ', ' . $addressSql['othersLocation_flat_no'] . ', ' . $addressSql['othersLocation_street_name'] . ', ' . $addressSql['othersLocation_pin_code'] . ', ' . $addressSql['othersLocation_location'] . ', ' . $addressSql['othersLocation_district'] . ', ' . $addressSql['othersLocation_city'] . ' ,' . $addressSql['othersLocation_state'];

        $vendorCur = $dbObj->queryGet("SELECT currency_name FROM `erp_currency_type` WHERE currency_id='" . $data['currency'] . "'")['data']['currency_name'];

        $parentPo = $dbObj->queryGet("SELECT * FROM `erp_branch_purchase_order` WHERE `po_id`='" . $data['parent_id'] . "' ")['data'];

        $functionalAreaName = $dbObj->queryGet("SELECT functionalities_name FROM `erp_company_functionalities` WHERE functionalities_id='" . $data['functional_area'] . "'")['data']['functionalities_name'];

        // navBar Button 
        $btn = '';
        if ($data['po_status'] == 9) {
              if($data['use_type'] == 'servicep'){
                $btn .= '<a class="nav-link btn btn-primary text-white py-2 px-3" id="" data-toggle="" href="manage-manual-grn-new.php?view=' . base64_encode($data['po_number']) . '&type=srn">SRN</a>';
            $btn .= '<a class="nav-link btn btn-danger text-white py-2 px-3" id="" data-toggle="" href="manage-purchases-orders-tax.php?close-po=' . $poIdBash . '"> Close PO</a>';

              }
              else{
                $btn .= '<a class="nav-link btn btn-primary text-white py-2 px-3" id="" data-toggle="" href="manage-manual-grn-new.php?view=' . base64_encode($data['po_number']) . '&type=grn">GRN</a>';
            $btn .= '<a class="nav-link btn btn-danger text-white py-2 px-3" id="" data-toggle="" href="manage-purchases-orders-tax.php?close-po=' . $poIdBash . '"> Close PO</a>';

              }
            
        } else if ($data['po_status'] == 14) {
            $btn .= '<a class="nav-link approve-po btn btn-danger text-white float-right p-2" id="" href="manage-purchases-orders-tax.php?reject=' . $poIdBash . '" role="" aria-controls="profile" aria-selected="false">Reject PO</a>';
            $btn .= '<a class="nav-link approve-po btn btn-success text-white float-right p-2" id="" href="manage-purchases-orders-tax.php?approve=' . $poIdBash . '" role="" aria-controls="profile" aria-selected="false">Approve PO</a>';
        }
        $navBtn = '<div class="action-btns display-flex-gap create-delivery-btn-sales gap-2" id="action-navbar">' . $btn . '</div>';

        


        $dynamic_data = [
            "dataObj" => $data,
            "companyCurrency" => getSingleCurrencyType($company_currency),
            "vendorCur" => $vendorCur,
            "parentPoNo" => $parentPo['po_number'],
            "functionalAreaName" => $functionalAreaName ?? "-",
            "billAddress" => $billAddress,
            "shipAddress" => $shipAddresss,
            "currecy_name_words" => number_to_words_indian_rupees($data['totalAmount']),
            "currecy_name_wordsVendorCur" => number_to_words_indian_rupees($data['totalAmount'] * $data['conversion_rate']),
            "created_by" => getCreatedByUser($data['created_by']),
            "created_at" => formatDateORDateTime($data['created_at']),
            "updated_by" => getCreatedByUser($data['updated_by']),
            "updated_at" => formatDateORDateTime($data['updated_at']),
            "items" => $items,
            "allSubTotal" => decimalValuePreview($allSubTotal),
            "navBtn" => $navBtn,
            "countryCode" => $_SESSION['logedBranchAdminInfo']['companyCountry'],
            "taxName" => getTaxName($_SESSION['logedBranchAdminInfo']['companyCountry'])['data'],
            "taxComponents" => $data['taxComponents'],
            "country_labels" => json_decode(getLebels($_SESSION['logedBranchAdminInfo']['companyCountry'])['data'])
        ];

        $res = [
            "status" => true,
            "msg" => "Success",
            "data" => $dynamic_data,
            "showChkbox" => $showChkbox,
            "printChkTC"=>$printChkTC,
            "sql" => $sqlMainQryObj['query'],
            
        ];
    } else {
        $res = [
            "status" => false,
            "msg" => "Error!",
            "sql" => $sqlMainQryObj
        ];
    }

    echo json_encode($res);
} else if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'deletepo') {
    $poId = $_GET['po_id'];
    $sql = "UPDATE `erp_branch_purchase_order` SET STATUS = 'deleted' WHERE po_id = '" . $poId . "';";
    $querry = $dbObj->queryUpdate($sql);
    echo json_encode($querry);
} else if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'classicview') {
    $poId = $_GET['po_id'];
    $templatePoTaxObj->printPoItems($poId);
}
