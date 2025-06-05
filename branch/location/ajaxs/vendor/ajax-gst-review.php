<?php

$gstin = isset($_POST["gstin"]) && $_POST["gstin"] != "" ? $_POST["gstin"] : (isset($_GET["gstin"]) && $_GET["gstin"] != "" ? $_GET["gstin"] : "");

$currentYear = date('Y');
$currentMonth = date('n');

// Check if a financial year is sent via POST
if (isset($_POST["financial_year"]) && $_POST["financial_year"] != "") {
    $fy = $_POST["financial_year"];
} else {
    // Default calculation for the financial year
    if ($currentMonth >= 4) {
        $fyStart = $currentYear;
        $fyEnd = substr($currentYear + 1, -2);
    } else {
        $fyStart = $currentYear - 1;
        $fyEnd = substr($currentYear, -2);
    }
    $fy = "$fyStart-$fyEnd";
}

if ($gstin != "") {

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.mastergst.com/public/rettrack?gstin='.$gstin.'&fy='.$fy.'&email=developer%40vitwo.in',
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

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo json_encode([
            "status" => "warning",
            "message" => $err,
            "data" => []
        ]);
    } else {
        $returns = json_decode($response, true);

        unset($returns['header']['client_id']);
        unset($returns['header']['client_secret']);

        $returnData = [];

        if (isset($returns['data']['EFiledlist']) && !empty($returns['data']['EFiledlist'])) {
            $returnData = $returns['data'];
            echo json_encode([
                "status" => "success",
                "message" => "Data fetched successfully",
                "fy" => $fy,
                "data" => $returnData
            ]);
        } else {
            echo json_encode([
                "status" => "warning",
                "message" => "No data found for the provided GSTIN",
                "data" => [],
                "fy" => $fy
            ]);
        }
    }
} else {
    echo json_encode([
        "status" => "warning",
        "message" => "Please provide gstin",
        "data" => [],
        "fy" => $fy
    ]);
}
