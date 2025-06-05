<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");
$BranchPoObj = new BranchPo();
if(isset($_GET["vendor_id"]) && $_GET["vendor_id"] != "" && isset($_GET["currency"]) && $_GET["currency"] != "" && isset($_GET["except_po"]) && $_GET["except_po"] != "" && isset($_GET["exceptparentpo"]) && $_GET["exceptparentpo"] != "")
{
    global $company_id;
    global $branch_id;
    global $location_id;
    $vendor_id = $_GET["vendor_id"];
    $currency = $_GET["currency"];
    $except_po = $_GET["except_po"];
    $exceptparentpo = $_GET["exceptparentpo"];

    $parentPoQuery = queryGet('SELECT * FROM `erp_branch_purchase_order` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `po_status`="9"  AND `use_type` IN ("servicep","material","asset")  AND `currency`="'.$currency.'" AND `parent_id` = "'.$exceptparentpo.'" ORDER BY `po_id` DESC', true);

    $array = [];

    foreach($parentPoQuery["data"] as $parentPo)
    {
        array_push($array, $parentPo["po_id"]);
    }
    

    $cond = "";

    if(count($array) > 0)
    {
        $po_is_list_string = implode(",", $array);
        $cond .= "AND `po_id` NOT IN ('.$po_is_list_string.')"; 
    }
    
    $poDetailsObj = queryGet('SELECT * FROM `erp_branch_purchase_order` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `po_status`="9"  AND `use_type` IN ("servicep","material","asset")  AND `currency`="'.$currency.'" AND `po_number` != "'.$except_po.'" '.$cond.' ORDER BY `po_id` DESC', true);
    
    // echo json_encode($poDetailsObj);

    $poDetails = $poDetailsObj["data"] ?? [];


    $result = "";
    $poItemSl = 1;
    foreach ($poDetails as $poDetail) {
   
        $currency = $poDetail["currency"];
        $curr_name_query = queryGet("SELECT * FROM `erp_currency_type` WHERE currency_id = $currency", false);
        $curr_name = $curr_name_query["data"]["currency_name"];

    $result .= "
        <tr>

        <td>";
        $typeVal = "material";
        $type = $poDetail["use_type"];
        if($type == "servicep")
        {
            $typeVal = "service";
        }
        else
        {
            $typeVal = "material";
        }

        $result .= "<input type='checkbox' id='selectPOCheck_".$poItemSl."' class='po-input_checkbox ajaxPoTableCheckBox' value='".$poDetail["po_number"]."' data-type ='".$typeVal."' data-origin='po'>";
            
       $result .= "</td>
        <td>".$poDetail["po_number"]."</td>
        <td>".$poDetail["po_date"]."</td>
        <td>".$poDetail["ref_no"]."</td>
        <td>".$BranchPoObj->fetchVendorDetails($poDetail['vendor_id'])['data'][0]['trade_name']."</td>
        <td>".$BranchPoObj->fetchVendorDetails($poDetail['vendor_id'])['data'][0]['vendor_code']."</td>
        <td>".$poDetail["use_type"]."</td>
        <td>".$curr_name." ".decimalValuePreview($poDetail["totalAmount"])."</td>
        </tr>";
        $poItemSl++;
    }

    echo json_encode($result);
}

?>