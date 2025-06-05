<?php

$pincode = (isset($_GET["pincode"]) && $_GET["pincode"] != "") ? $_GET["pincode"] : "";

if ($pincode != "") {

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.postalpincode.in/pincode/' . $pincode . '',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    if ($err) {
        echo json_encode([
            "status" => "warning",
            "message" => $err,
            "data" => ""
        ]);
    } else {
        $returndata = json_decode($response, true);
    }
    if ($returndata[0]['Status'] == 'Success') {
        $returns["status"] = $returndata[0]['Status'];
        $returns["message"] = "Data fetched success";
        $returns["data"] = $returndata[0]['PostOffice'][0];

        echo json_encode($returns);
    } else {
        echo json_encode([
            "status" => "warning",
            "message" => "No data found",
            "data" => []
        ]);
    }
} else {
    echo json_encode([
        "status" => "warning",
        "message" => "Please provide pincode",
        "data" => []
    ]);
}
?>
