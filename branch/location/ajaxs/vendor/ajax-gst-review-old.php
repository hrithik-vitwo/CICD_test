<?php

$gstin = (isset($_GET["gstin"]) && $_GET["gstin"] != "") ? $_GET["gstin"] : "";

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
        echo json_encode([
                "status" => "warning",
                "message" => $err,
                "data" => $returnData
            ]);
    } else {
        $returns = json_decode($response,true);
        
        $returnData = [];
        for ($i = 1; $i < 7; $i++) {
            $monthName = date('F', strtotime("-$i month"));
            $gstr1 = array_filter($returns["data"]["returns"], function ($var) use ($monthName){
                        return ($var['rtntype'] == 'GSTR1' && $var["taxp"] == $monthName);
                    });
            $gstr3b = array_filter($returns["data"]["returns"], function ($var)  use ($monthName) {
                        return ($var['rtntype'] == 'GSTR3B' && $var["taxp"] == $monthName);
                    });
            $returnData[] = [ 
                                "month" => $monthName,
                                "gstr1" => reset($gstr1) ,
                                "gstr3b" => reset($gstr3b)
                            ];
            //$returnData[]["gstr3b"] = reset($gstr3b);
        }
        
        $returns["status"] = "success";
        $returns["message"] = "Data fetched success";
        $returns["data"]["returns"] = $returnData;
        echo json_encode($returns);
            
        // echo json_encode([
        //         "status" => "success",
        //         "message" => "Data fetched success",
        //         "data" => $returnData
        //     ]);
    }
}else{
    echo json_encode([
                "status" => "warning",
                "message" => "Please provide gstin",
                "data" => []
            ]);
}
