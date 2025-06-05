<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("../../../boq/controller/boq.controller.php");
$headerData = array('Content-Type: application/json');

$BranchSoObj = new BranchSo();
$templateSalesOrderControllerObj = new TemplateSalesOrderController();
$ItemsObj = new ItemsController();
$boqControllerObj = new BoqController();
$dbObj = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if ($_GET['act'] == "modalData") {
        $soId = $_GET['soId'];
        $cond = "AND so.so_id ='" . $soId . "'";
        $sql_list = "SELECT so.*, cust.trade_name, cust.customer_code, cust.customer_gstin,cust.customer_pan,cust.customer_currency, cust.customer_status, custInvoiceLog.placeOfSupply, custAddress.customer_address_building_no, custAddress.customer_address_flat_no, custAddress.customer_address_street_name, custAddress.customer_address_pin_code, custAddress.customer_address_location, custAddress.customer_address_district, custAddress.customer_address_state, kam.kamName,func.functionalities_name , custInvoiceLog.complianceInvoiceType,cust.customer_authorised_person_email, cust.customer_authorised_person_phone FROM `erp_branch_sales_order` AS so LEFT JOIN erp_customer AS cust ON so.customer_id = cust.customer_id LEFT JOIN `erp_customer_invoice_logs` AS custInvoiceLog ON so.so_number = custInvoiceLog.ref_no LEFT JOIN `erp_customer_address` as custAddress ON so.customer_id = custAddress.customer_address_id LEFT JOIN `erp_kam` as kam On so.kamId = kam.kamId LEFT JOIN erp_company_functionalities as func ON so.profit_center = func.functionalities_id WHERE 1 " . $cond . " AND so.company_id='" . $company_id . "' AND so.branch_id='" . $branch_id . "' AND so.location_id='" . $location_id . "' AND goodsType='project' AND approvalStatus='9' AND jobOrderApprovalStatus=10 AND so.status !='deleted' ORDER BY so.so_id DESC";
        $sqlMainQryObj = $dbObj->queryGet($sql_list);
        $data = $sqlMainQryObj['data'];
        $num_list = $sqlMainQryObj['numRows'];
        $dynamic_data = [];
        if ($num_list > 0) {
            $dynamic_data = [];
            $itemDetails[$data['so_id']] = $BranchSoObj->fetchBranchSoItems($data['so_id'])['data'];
            $items = [];
            $allSubTotal = 0;
            $totalDis = 0;
            $grandTotalAmt = 0;
            $boq = [];
            foreach ($itemDetails[$data['so_id']] as $itemKey => $oneItem) {
                $deliverySchedule = $BranchSoObj->fetchBranchSoItemsDeliverySchedule2($oneItem['so_item_id'])['data'];
                $deliveryOBj = [];
                $boqDetailObj = $boqControllerObj->getBoqDetails($oneItem['inventory_item_id']);
                $boq[] = [
                    "itemCode" => $oneItem['itemCode'],
                    "so_item_id" => $oneItem['so_item_id'],
                    "inventory_item_id" => $oneItem['inventory_item_id'],
                    "invStatus" => $oneItem['invStatus'],
                    "itemName" => $oneItem['itemName'],
                    "qty" => $oneItem['qty'],
                    "remainingQty" => $oneItem['remainingQty'],
                    "itemRemarks" => $oneItem['itemRemarks'],
                    "completion_value" => $oneItem['completion_value'],
                    "boqDetail" => $boqDetailObj['data']
                ];

                $baseUnitMeasure = $ItemsObj->getBaseUnitMeasureById($oneItem['uom']);
                $uomName = $baseUnitMeasure['data']['uomName'];
                $subTotalAmt = ($oneItem['unitPrice'] * $oneItem['completion_value']);
                $taxAmount = ($subTotalAmt * $oneItem['tax']) / 100;
                $totalDiscount = ($subTotalAmt * $oneItem['totalDiscount']) / 100;
                $totalAmt = $subTotalAmt - $totalDiscount + $taxAmount;
                $grandTotalAmt += $totalAmt;

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
                $taxAbleAmount = $subTotal - $oneItem['itemTotalDiscount'];
                if ($oneItem['tax'] == 0) {
                    $itemTotalAmount = $taxAbleAmount;
                } else {
                    $gstAmount = ($taxAbleAmount * $oneItem['tax']) / 100;
                    $itemTotalAmount = $taxAbleAmount + $gstAmount;
                }

                $allSubTotal += $subTotal;
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
                    "unitPrice" => $oneItem['unitPrice'],
                    "subTotal" => $subTotal,
                    "total_discount" => $oneItem['itemTotalDiscount'],
                    "totalTax" => $oneItem['totalTax'],
                    "tax" => $oneItem['tax'],
                    "uomName" => $uomName,
                    "hsnCode" => $oneItem['hsnCode'],
                    "itemreamrk" => $oneItem['itemRemarks'],
                    "completion_value" => $oneItem['completion_value'],
                    "remainingQty" => $oneItem['remainingQty'],
                    "stock" => $stock,
                    "taxAbleAmount" => $taxAbleAmount,
                    "gstAmount" => $gstAmount,
                    "itemTotalAmount" => $itemTotalAmount,
                    "currency" => $curName,
                    "delivery_obj" => $deliveryOBj
                ];
            }

            $customerAddress = $data['customer_address_building_no'] . ', ' . $data['customer_address_flat_no'] . ', ' . $data['customer_address_street_name'] . ', ' . $data['customer_address_pin_code'] . ', ' . $data['customer_address_location'] . ', ' . $data['customer_address_district'] . ', ' . $data['customer_address_state'];

            $dynamic_data = [
                "dataObj" => $data,
                "placeOfsupply" => getStateDetail($data['placeOfSupply'])['data']['gstStateName'],
                "currecy_name_words" => number_to_words_indian_rupees($data['totalAmount']),
                "created_by" => getCreatedByUser($data['created_by']),
                "created_at" => formatDateORDateTime($data['created_at']),
                "updated_by" => getCreatedByUser($data['updated_by']),
                "updated_at" => formatDateORDateTime($data['updated_at']),
                "customer_address" => $customerAddress,
                "itemDetails" => $items,
                "boqDetailObj" => $boq,
            ];
            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "sql_list" => $sql_list
            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql_list" => $sql_list
            ];
        }
        echo json_encode($res);
    }
}