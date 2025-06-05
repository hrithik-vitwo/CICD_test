<?php
require_once("../../app/v1/connection-branch-admin.php");

$headerData = array('Content-Type: application/json');
$postData = array(
    "username" => "rbajoria@vitwo.in",
    "password" => "Vitwo@123",
    "client_id" => "ifYTepjBvEWpzUCKji",
    "client_secret" => "0Z6ebVPQ5NplrfZ98BI1mF56",
    "grant_type" => "password"
);

$url_name = "https://commonapi.mastersindia.co/oauth/access_token";
$curl = curl_init();
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_URL, $url_name);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($curl, CURLOPT_HTTPHEADER, $headerData);

$result = curl_exec($curl);

try {
    $resultData = json_decode($result, true);
    if (isset($resultData["access_token"]) && !empty($resultData["access_token"])) {
        curl_close($curl);

        $curlGstHeaderData = array('Content-Type: application/json', 'Authorization: Bearer '.$resultData["access_token"], 'client_id: ifYTepjBvEWpzUCKji');
        
        if(isset($_GET["gstin"]) && !empty($_GET["gstin"])){
            $url_name = "https://commonapi.mastersindia.co/commonapis/searchgstin?gstin=".$_GET["gstin"];
            $curlGst = curl_init();
            curl_setopt($curlGst, CURLOPT_URL, $url_name);
            curl_setopt($curlGst, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlGst, CURLOPT_HTTPHEADER, $curlGstHeaderData);

            $resultGst = curl_exec($curlGst);
            try{
                $resultGstData = json_decode($resultGst, true);
                // $responseData["status"]="success";
                // $responseData["message"]="Fetched success";
                // $responseData["data"]=$resultGstData;
                if(isset($resultGstData["error"]) && $resultGstData["error"]!="false"){
                    $responseData["status"]="success";
                    $responseData["message"]="Fetched success";
                    $responseData["data"]=$resultGstData["data"];
                    curl_close($curlGst);
                }else{
                    $responseData["status"]="warning";
                    $responseData["message"]="Something went wrong try again!";
                    curl_close($curlGst);
                }
            }catch(Exception $ee){
                $responseData["status"]="error";
                $responseData["message"]="Something went wrong try again!";
                curl_close($curlGst);
            }
        }else{
            $responseData["status"]="warning";
            $responseData["message"]="Please provide valid gstin number!";
        }

    } else {
        $responseData["status"]="error";
        $responseData["message"]="Something went wrong try again with valid credentials!";
        curl_close($curl);
    }
} catch (Exception $e) {
    $responseData["status"]="error";
    $responseData["message"]="Something went wrong try again to auth!";
    curl_close($curl);
}

//console($responseData);
echo json_encode($responseData);