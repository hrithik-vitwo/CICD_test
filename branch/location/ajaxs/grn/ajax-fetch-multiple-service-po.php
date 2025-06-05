<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");
$BranchPoObj = new BranchPo();
if(isset($_GET["vendor_id"]) && $_GET["vendor_id"] != "" && isset($_GET["type"]) && $_GET["type"] != "" && isset($_GET["currency"]) && $_GET["currency"] != "")
{
    global $company_id;
    global $branch_id;
    global $location_id;
    $vendor_id = $_GET["vendor_id"];
    $type = $_GET["type"];
    $currency = $_GET["currency"];

    
        $poDetailsObj = queryGet('SELECT * FROM `erp_branch_purchase_order` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `po_status`="9" AND `use_type` = "servicep" AND `currency`="'.$currency.'"', true);
    
    $poDetails = $poDetailsObj["data"] ?? [];

    $result = "";
    $poItemSl = 1;
    foreach ($poDetails as $poDetail) {
   

    $result .= "
        <tr>

        <td>
            <input type='checkbox' id='selectPOCheck_".$poItemSl."' class='po-input_checkbox ajaxPoTableCheckBox' value='".$poDetail["po_number"]."' data-type ='service'>
        </td>
        <td>".$poDetail["po_number"]."</td>
        <td>".$poDetail["po_date"]."</td>
        <td>".$poDetail["ref_no"]."</td>
        <td>".$BranchPoObj->fetchVendorDetails($poDetail['vendor_id'])['data'][0]['trade_name']."</td>
        <td>".$BranchPoObj->fetchVendorDetails($poDetail['vendor_id'])['data'][0]['vendor_code']."</td>
        <td>".$poDetail["use_type"]."</td>
        <td>₹".$poDetail["totalAmount"]."</td>
        </tr>";
        $poItemSl++;
    }

    echo json_encode($result);
}

?>