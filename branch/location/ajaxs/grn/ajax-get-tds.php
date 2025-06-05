<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");

function getSlabPercentage($amount, $slabArray) {
    $slab = array_reduce($slabArray, function($carry, $item) use ($amount) {
        $lowerLimit = $item[0];
        $upperLimit = $item[1];
        $percentage = $item[2];

        if ($amount >= $lowerLimit && ($upperLimit === null || $amount < $upperLimit)) {
            return $percentage;
        }
        
        return $carry;
    }, 0);

    return $slab;
}



if(isset($_GET["tds"]) && $_GET["tds"] != "" && isset($_GET["base"]) && $_GET["base"] != "" )
{
    $tds_id = $_GET["tds"];
    $baseAmt = $_GET["base"];
    $getTds = queryGet("SELECT `TDSRate`,`slab_serialized` FROM `erp_tds_details` WHERE `id`='" . $tds_id . "'");
    $tds = $getTds["data"]["TDSRate"];
    $slab = unserialize($getTds["data"]["slab_serialized"]);

    $percentage = getSlabPercentage($baseAmt, $slab);

    echo json_encode($percentage);
}


?>