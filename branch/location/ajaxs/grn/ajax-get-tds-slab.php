<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");

if(isset($_GET["tds"]) && $_GET["tds"] != "")
{
    $tds_id = $_GET["tds"];
    $getTds = queryGet("SELECT `slab_serialized` FROM `erp_tds_details` WHERE `id`='" . $tds_id . "'");
    $tds = unserialize($getTds["data"]["slab_serialized"]);

    echo json_encode($tds);
}


?>