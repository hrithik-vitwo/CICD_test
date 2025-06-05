<?php
header("content-type: application/json");
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-compliance-controller.php");
require_once("../../controller/gstr1-file.controller.php");
require_once("../../controller/gstr1-json-repositary-controller.php");
$response = [];
$queryParams = json_decode(base64_decode($_GET['action']));
$authGstinPortalObj = new AuthGstinPortal();
$checkAuthObj = $authGstinPortalObj->checkAuth();
$period = $queryParams->period;
$api_client_id = "GSPfc3af0aa-1ae5-45be-8f6f-34d0baa63594";
$api_client_secret = "GSP6e50f5dd-7787-4c7d-a576-a5f8d9ffe5f6";
$api_client_email = "developer@vitwo.in";
$branch_ip_address = $_SERVER['REMOTE_ADDR'];
$company_id;


if ($checkAuthObj["status"] == "success") {

    $complianceGSTR2bFileObj = new ComplianceGstr2b();
    $jsonObj = $complianceGSTR2bFileObj->fetchGstr2bData($queryParams->period);

    if ($jsonObj["data"] != []) {
        $resultObj = queryUpdate("UPDATE `erp_compliance_gstr2b` SET `apiData` = '" . $jsonObj["data"] . "',`status`='pulled' WHERE `company_id` = $company_id AND `branch_id` = $branch_id AND `gstr2b_return_period` = '$queryParams->period'");
    }
    if ($resultObj['status'] == 'success') {
        $response = [
            "status" => "success",
            "msg" => $resultObj['message'],
        ];
    };
    $mmyyyy = ($period == null) ? date('mY', strtotime("last month")) : $period;
    $gstinStateCode = $checkAuthObj["data"]["gstinStateCode"] ?? "";
    $gstin = $checkAuthObj["data"]["gstin"] ?? "";
    $gstinUsername = $checkAuthObj["data"]["gstinUsername"] ?? "";
    $authTnxId = $checkAuthObj["data"]["authTnxId"] ?? "";
    $curl = curl_init();
    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => 'https://api.mastergst.com/gstr2b/all?email=' . $api_client_email . '&gstin=' . $gstin . '&rtnprd=' . $mmyyyy,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'gst_username: ' . $gstinUsername,
                'state_cd: ' . $gstinStateCode,
                'ip_address: ' . $branch_ip_address,
                'txn: ' . $authTnxId,
                'client_id: ' . $api_client_id,
                'client_secret: ' . $api_client_secret,
                'Accept: application/json'
            )
        )
    );
    $response = curl_exec($curl);
    $responseObj = json_decode($response, true);
    //  $responseObj["status_cd"]=1;
    if ($responseObj["status_cd"] == 1) {
        //     function getGstr2bData($mmyyyy = null)
        // {
        //     global $company_id, $branch_id;
        //     $mmyyyy = ($mmyyyy == null) ? date('mY', strtotime("last month")) : $mmyyyy;

        //     $sql = 'SELECT * FROM `erp_compliance_gstr2b_documents` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `filingPeriod`=' . $mmyyyy;

        //     $dataObj = queryGet($sql, true);

        //     if ($dataObj["status"] == "success") {
        //         return [
        //             "status" => "success",
        //             "message" => "GSTR2B data fetched successfully.",
        //             "data" => $dataObj["data"]
        //         ];
        //     } else {
        //         return [
        //             "status" => "warning",
        //             "message" => "GSTR2B data not found.",
        //             "data" => []
        //         ];
        //     }
        // }

        $gstr2bDocData = $responseObj["data"]["data"]["docdata"] ?? [];
        $noErrorsGstr2bDataSaving = 0;
        $noErrorsGstr2bDataSavingSql = [];
        //   $gstr2bDocData=$gstr2bDocDataObj['data']["docdata"];
        $resultObj = queryUpdate("UPDATE `erp_compliance_gstr2b` SET `apiData` = '" . addslashes(json_encode($responseObj)) . "',`status`='pulled' WHERE `company_id` = $company_id AND `branch_id` = $branch_id AND `gstr2b_return_period` = '$period'");

        foreach ($gstr2bDocData["b2b"] as $oneVendor) {
            $invTotalTax = 0;
            $invCgstAmount = 0;
            $invSgstAmount = 0;
            $invIgstAmount = 0;
            $invCessAmount = 0;
            foreach ($oneVendor["inv"] as $oneInv) {

                // foreach ($oneInv["items"] as $oneItem) {
                $invTotalTax = ($oneInv["igst"] + $oneInv["cgst"] + $oneInv["sgst"] + $oneInv["cess"]);
                $invSgstAmount = $oneInv["sgst"];
                $invCgstAmount = $oneInv["cgst"];
                $invIgstAmount = $oneInv["igst"];
                $invCessAmount = $oneInv["cess"];


                // }
                $taxable_amount = $oneInv['val'] - $invTotalTax;

                $insertSql = 'INSERT INTO `erp_compliance_gstr2b_documents` 
                    SET 
                    `company_id`=' . $company_id . ',
                    `branch_id`=' . $branch_id . ',
                    `return_period`="' . $mmyyyy . '",
                    `doc_file_date`= STR_TO_DATE("' . $oneVendor["supfildt"] . '", "%d-%m-%Y"),
                    `doc_date`= STR_TO_DATE("' . $oneInv["dt"] . '", "%d-%m-%Y"),
                    `doc_type`="B2B",
                    `inv_type`="' . $oneInv["typ"] . '",
                    `rev_charge`="' . $oneInv["rev"] . '",
                    `itc_available`="' . $oneInv["itcavl"] . '",
                    `vendor_gstin`="' . $oneVendor["ctin"] . '",
                    `vendor_name`="' . $oneVendor["trdnm"] . '",
                    `inv_number`="' . $oneInv["inum"] . '",
                    `inv_amount`=' . $oneInv['val'] . ',
                    `taxable_amount`=' . $taxable_amount . ',
                    `tax_amount`=' . $invTotalTax . ',
                    `cgst_amount`=' . $invCgstAmount . ',
                    `sgst_amount`=' . $invSgstAmount . ',
                    `igst_amount`=' . $invIgstAmount . ',
                    `cess_amount`=' . $invCessAmount . ',
                    `created_by`="' . $created_by . '",
                    `updated_by`="' . $updated_by . '"';

                $insertObj = queryInsert($insertSql);
                $insertDocId = $insertObj["insertedId"];
                foreach ($oneInv["items"] as $oneItem) {
                    $insertItemSql = "INSERT INTO `erp_compliance_gstr2b_documents_item` SET `doc_id`=$insertDocId,`taxable_amount`=" . $oneItem["txval"] . ",`tax_rate`=" . $oneItem["rt"] . ",`cgst_amount`=" . $oneItem["cgst"] . ",`sgst_amount`=" . $oneItem["sgst"] . ",`igst_amount`=" . $oneItem["igst"] . ",`cess_amount`=" . $oneItem["cess"];
                    queryInsert($insertItemSql);
                }

                if ($insertObj["status"] != "success") {
                    $noErrorsGstr2bDataSaving++;
                    $noErrorsGstr2bDataSavingSql[] = $insertSql;
                }
            }
        }
        if ($noErrorsGstr2bDataSaving == 0) {
            // $jsonObj= getGstr2bData($mmyyyy);
            $response = [
                "status" => "success",
                "message" => "Data pulled successfully",
            ];
        } else {
            $response = [
                "status" => "warning",
                "message" => "All data not inserted successfully",
                "error" => $noErrorsGstr2bDataSaving,
                "error_sql" => $noErrorsGstr2bDataSavingSql
            ];
        }
    }else{
        $response = [
            "status" => "warning",
            "message" => $responseObj["error"]["message"],
            "data" => $responseObj
        ];
    }
} else {
    $response = [
        "status" => "warning",
        "message" => $responseObj["error"]["message"] ?? "Invalid credentials or API access not allowed",
        "data" => $responseObj
    ];
}
echo json_encode($response);
