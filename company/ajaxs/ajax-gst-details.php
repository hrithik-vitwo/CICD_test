<?php
// include_once("../../app/v1/connection-company-admin.php");
include_once("../../app/v1/connection-branch-admin.php");



    function isCustomerExist($GSTIN = null)
    {
        global $company_id;
        $check = queryGet("SELECT * FROM `" . ERP_CUSTOMER . "`  WHERE company_id=$company_id AND `customer_gstin`='" . $GSTIN . "'");

        if ($check['numRows'] >= 1) {

            return true;
        } else {
            return false;
            //exit(); 
        }
    }

if (isset($_GET["gstin"]) && !empty($_GET["gstin"])) {
    $curlGst = curl_init();
    curl_setopt_array($curlGst, array(
        CURLOPT_URL => 'https://api.mastergst.com/public/search?email=developer@vitwo.in&gstin=' . $_GET["gstin"],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'client_id: GSPfc3af0aa-1ae5-45be-8f6f-34d0baa63594',
            'client_secret: GSP6e50f5dd-7787-4c7d-a576-a5f8d9ffe5f6',
            'Accept: application/json'
        ),
    ));

    $gstin = trim($_GET["gstin"]);

    $resultGst = curl_exec($curlGst);
    
    $resultGstData = json_decode($resultGst, true);
    // console($resultGstData);
    // exit;

    try {
        if (isCustomerExist($gstin)) {
            $responseData = [
                "exists"  => true,
                "status"  => "warning",
                "message" => "Customer with this GSTIN already exists!"
            ];
            curl_close($curlGst);
        } elseif (isset($resultGstData["data"]) && count($resultGstData["data"]) > 0) {
            $gstDetails = $resultGstData["data"];
            if (isset($resultGstData["error"]) && $resultGstData["error"] = "false") {
                $responseData["status"] = "warning";
                $responseData["message"] = "Something went wrong try again1!";
                curl_close($curlGst);
            } else {
                $responseData["status"] = "success";
                $responseData["message"] = "Fetched success";
                $responseData["data"] = $resultGstData["data"];
                curl_close($curlGst);
            }
        } else {
            $responseData["status"] = "warning";
            $responseData["message"] = "Something went wrong try again2!";
            curl_close($curlGst);
        }
    } catch (Exception $ee) {
        $responseData["status"] = "error";
        $responseData["message"] = "Something went wrong try again3!";
        curl_close($curlGst);
    }
} else {
    $responseData["status"] = "warning";
    $responseData["message"] = "Please provide valid gstin number!";
}

 
//console($responseData);
echo json_encode($responseData);
