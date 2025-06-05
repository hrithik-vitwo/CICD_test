<?php
include_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");

global $company_id;

if(isset($_GET["doc"]) && $_GET["doc"] != "" && isset($_GET["vendor"]) && $_GET["vendor"] != "" )
{
    $doc_no = $_GET["doc"];
    $vendor_id = $_GET["vendor"];
    $check_doc_no_query = queryGet("SELECT * FROM `erp_grn` WHERE `companyId`='" . $company_id . "' AND `vendorDocumentNo` = '" . $doc_no . "' AND `vendorId`='" . $vendor_id . "' AND `grnStatus`='active'");
    // $flag = "true";
    if($check_doc_no_query["numRows"] != 0)
    {
        $flag = "true";
    }
    else
    {
        $flag = "false";
    }

    echo json_encode($flag);
}


?>