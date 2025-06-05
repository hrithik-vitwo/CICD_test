<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");
$BranchPoObj = new BranchPo();
if (isset($_GET["st"]) && $_GET["st"] != "") {
    global $company_id;
    global $branch_id;
    global $location_id;
    $st = $_GET["st"];

    $rackDetailsObj = queryGet("SELECT * FROM `erp_rack` WHERE storage_location_id = '".$st."'", true);
    $options = "";
    foreach($rackDetailsObj["data"] as $rackDetail)
    {
        $rack_id = $rackDetail["rack_id"];
        $layerDetailsObj = queryGet("SELECT * FROM `erp_layer` WHERE rack_id = '".$rack_id."'", true);
        foreach($layerDetailsObj["data"] as $layerDetail)
        {
            $layer_id = $layerDetail["layer_id"];
            $binDetailsObj = queryGet("SELECT * FROM `erp_storage_bin` WHERE layer_id = '".$layer_id."'", true);
            foreach($binDetailsObj["data"] as $binDetail)
            {
                $bin_id = $binDetail["bin_id"];
                $bin_name = $binDetail["bin_name"];
                $options .=  "<option value='".$bin_id."'>".$bin_name."</option>";
            }
        }
    }
    
    echo json_encode($options);

}

?>