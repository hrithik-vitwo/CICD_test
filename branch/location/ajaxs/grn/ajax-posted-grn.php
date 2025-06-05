<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");
$BranchPoObj = new BranchPo();
    global $company_id;
    global $branch_id;
    global $location_id;

    
    $grnListQuery = queryGet('SELECT * FROM `erp_grn` WHERE `companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' AND `grnType`="grn" AND `iv_status`="0" AND `grnStatus` = "active" ORDER BY `grnId` DESC', true);
    
    $grnDetails = $grnListQuery["data"] ?? [];

    $result = "";
    $grnItemSl = 1;
    foreach ($grnDetails as $grnDetail) {
   

    $result .= "
        <tr>

        <td>";

        $result .= "<input type='checkbox' id='selectPOCheck_".$grnItemSl."' class='po-input_checkbox ajaxPoTableCheckBox' value='".$grnDetail["grnCode"]."' data-type ='material' data-origin='grn'>";
            
       $result .= "</td>
        <td>".$grnDetail["grnCode"]."</td>
        <td>".$grnDetail["vendorDocumentNo"]."</td>
        <td>".$grnDetail["grnPoNumber"]."</td>
        <td>".$BranchPoObj->fetchVendorDetails($grnDetail['vendorId'])['data'][0]['trade_name']."</td>
        <td>".$BranchPoObj->fetchVendorDetails($grnDetail['vendorId'])['data'][0]['vendor_code']."</td>
        <td>".$grnDetail["vendorGstin"]."</td>
        <td>₹".decimalValuePreview($grnDetail["grnTotalAmount"])."</td>
        </tr>";
        $grnItemSl++;
    }

    echo json_encode($result);


?>