<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();

// check item quantity
$itemList = $_POST["listItem"];
// echo json_encode($_POST);
$return = [];
$html = '';


// $noOfItemsWhoDontHaveStocks = 0;
// foreach ($itemList as $key => $oneItem) {
//     if ($oneItem['sumOfBatches'] <= 0) {
//         $noOfItemsWhoDontHaveStocks++;
//     }
// }

// $totalItem = count($itemList);

$itemTotalDiscount = 0;
$itemTotalPrice = 0;
$totalprice = 0;
$itemTotalDiscountSum = 0;
$itemTotalPriceSum = 0;
$totalItems = 0;
if ($itemList) {
    foreach ($itemList as $oneItem) {

        $itemTotalPriceSum += $oneItem["itemTotalPrice"];
        $itemTotalDiscountSum += $oneItem["itemTotalDiscount"];

        $itemId = $oneItem["itemId"];
        $inventoryItemId = $oneItem["inventoryItemId"];
        $itemLineNo = $oneItem["lineNo"];
        $itemDeliveryDateId = $oneItem["itemDeliveryDateId"];
        $itemCode = $oneItem["itemCode"];
        $itemDesc = $oneItem["itemDesc"];
        $itemName = $oneItem["itemName"];
        $hsnCode = $oneItem["hsnCode"];
        $tax = $oneItem["tax"];
        $totalTax = $oneItem["totalTax"];
        $tolerance = $oneItem["tolerance"] ?? 0;
        $totalDiscount = $oneItem["totalDiscount"];
        $itemTotalDiscount = $oneItem["itemTotalDiscount"];
        $unitPrice = $oneItem["unitPrice"];
        $itemTotalPrice = $oneItem["itemTotalPrice"];
        $itemTotalQty = $oneItem["itemTotalQty"];
        $itemQty = $oneItem["qty"];
        $extraOrder = $oneItem["extraOrder"];
        $itemUom = $oneItem["uom"];

        // $itemTotalDiscount += $oneItem["itemTotalDiscount"];
        $totalprice += $itemTotalPrice;


        if ($oneItem['qty']>0 || $oneItem["extraOrder"]>0) {
            $html .= '<div class="row border my-2 p-2">';
            $html .= '<p class="space-between-class"><strong>Item Code:</strong> <i>' . $itemCode . '</i></p>';
            $html .= '<p class="space-between-class"><strong>Item Name:</strong> <i>' . $itemName . '</i></p>';
            $html .= '<p class="space-between-class"><strong>Total Qty:</strong> <i>' . decimalQuantityPreview($itemTotalQty) . '</i></p>';

            $html .= '<p class="space-between-class"><strong>Delivery Qty:</strong> <i>' . decimalQuantityPreview($itemQty) . '</i></p>';

            if ($oneItem['extraOrderType'] == 'purchase') {
                $html .= '<p class="space-between-class"><strong>Purchase Request Qty:</strong> <i>' . decimalQuantityPreview($extraOrder) . '</i></p>';
            }else{
                $html .= '<p class="space-between-class"><strong>Production Qty:</strong> <i>' . decimalQuantityPreview($extraOrder) . '</i></p>';
            }
            $html .= '</div>';
        } else {
            $html .= '<div class="row border my-2 p-2">';
            $html .= '<p class="space-between-class"><strong>Item Code:</strong> <i>' . $itemCode . '</i></p>';
            $html .= '<p class="space-between-class"><strong>Item Name:</strong> <i>' . $itemName . '</i></p>';
            $html .= '<p class="space-between-class"><strong>Total Qty:</strong> <i>' . $itemTotalQty . '</i></p>';
            $html .= '<p class="space-between-class error"><strong>Error: No qty selected!</strong> <i></i></p>';
            $html .= '</div>';
        }
    }
} else {
    $html .= '<div class="row border my-2 p-2">';
    $html .= '<p class="space-between-class error"><strong>Error</strong> <i></i></p>';
    $html .= '</div>';
}

echo $html;
