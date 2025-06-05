<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");
$BranchPoObj = new BranchPo();
if(isset($_GET["vendor_id"]) && $_GET["vendor_id"] != "")
{
    global $company_id;
    global $branch_id;
    global $location_id;
    $vendor_id = $_GET["vendor_id"];
    $poDetailsObj = queryGet('SELECT * FROM `erp_branch_purchase_order` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `po_status`="9" AND `vendor_id`="'.$vendor_id.'"', true);
    $poDetails = $poDetailsObj["data"] ?? [];

    $result = "";
    $poItemSl = 1;
    foreach ($poDetails as $poDetail) {
   

    $result .= "
        <tr>
            <td>".$poItemSl."</td>
            <td>".$BranchPoObj->fetchVendorDetails($poDetail['vendor_id'])['data'][0]['trade_name']."</td>
            <td>".$poDetail["po_number"]."</td>
            <td>".$poDetail["totalItems"]."</td>
            <td>
                <a style='cursor:pointer' data-toggle='modal' data-target='#po_items' class='btn btn-sm btnModal' data-code='".$poDetail["po_number"]."' data-id='".$poDetail["po_id"]."'><i class='fa fa-eye po-list-icon'></i></a>
            </td>
        </tr>";
        $poItemSl++;
    }

    echo json_encode($result);
}

?>