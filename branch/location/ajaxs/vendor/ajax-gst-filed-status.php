<?php

$gstin = (isset($_GET["gstin"]) && $_GET["gstin"] != "") ? $_GET["gstin"] : "";
$response = null;
if ($gstin != "") {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://gst-return-status.p.rapidapi.com/free/gstin/" . $gstin,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "X-RapidAPI-Host: gst-return-status.p.rapidapi.com",
            "X-RapidAPI-Key: 5eba86a19fmsh5990778eda39a1cp1bc388jsnde4bc60fc2b7"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo $response;
    }
}
?>