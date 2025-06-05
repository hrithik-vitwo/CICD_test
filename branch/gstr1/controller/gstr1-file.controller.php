<?php

class ComplianceGSTR1File
{
    private $periodGstr1 = null;
    private $periodStart = null;
    private $periodEnd = null;
    private $company_id;
    private $branch_id;
    private $location_id;
    private $created_by;
    private $updated_by;
    private $branch_gstin;
    private $branch_gstin_code;

    private $authGstinPortalObj;

    function __construct($returnPeriod = null, $authObj = null)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        global $branch_gstin;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->location_id = $location_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->branch_gstin = $branch_gstin;
        $this->branch_gstin_code = substr($branch_gstin, 0, 2);


        if ($returnPeriod == null) {
            $this->periodGstr1 =  date("mY", strtotime('first day of last month'));
        } else {
            $this->periodGstr1 = $returnPeriod;
        }


        if ($authObj == null) {
            $authGstinPortalObj = new AuthGstinPortal();
            $this->authGstinPortalObj = $authGstinPortalObj->checkAuth();
        } else {
            $this->authGstinPortalObj = $authObj;
        }
    }


    // Step : 1
    function saveGstr1Data($jsonData = null)
    {
        $authObj = $this->authGstinPortalObj;
        if ($authObj["status"] == "success") {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mastergst.com/gstr1/retsave?email=developer%40vitwo.in',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => $jsonData,
                CURLOPT_HTTPHEADER => array(
                    'gstin: ' . $this->branch_gstin,
                    'ret_period: ' . $this->periodGstr1,
                    'gst_username: ' . $authObj["data"]["gstinUsername"],
                    'state_cd: ' . $authObj["data"]["gstinStateCode"],
                    'ip_address: ' . $authObj["data"]["ipAddress"],
                    'txn: ' . $authObj["data"]["authTnxId"],
                    'client_id: GSPfc3af0aa-1ae5-45be-8f6f-34d0baa63594',
                    'client_secret: GSP6e50f5dd-7787-4c7d-a576-a5f8d9ffe5f6',
                    'Content-Type: application/json',
                    'Accept: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $authObj;
        }
    }
    // Step 1.1 (If mistake was made during step 1 then you can call this step, after this you can call step 1 again)
    function resetSavedGstr1Data()
    {
        $payloadArr = [
            "gstin" => $this->branch_gstin, "ret_period" => $this->periodGstr1
        ];

        $authObj = $this->authGstinPortalObj;
        if ($authObj["status"] == "success") {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mastergst.com/gstr1/reset?email=developer%40vitwo.in',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($payloadArr, true),
                CURLOPT_HTTPHEADER => array(
                    'gstin: ' . $this->branch_gstin,
                    'ret_period: ' . $this->periodGstr1,
                    'gst_username: ' . $authObj["data"]["gstinUsername"],
                    'state_cd: ' . $authObj["data"]["gstinStateCode"],
                    'ip_address: ' . $authObj["data"]["ipAddress"],
                    'txn: ' . $authObj["data"]["authTnxId"],
                    'client_id: GSPfc3af0aa-1ae5-45be-8f6f-34d0baa63594',
                    'client_secret: GSP6e50f5dd-7787-4c7d-a576-a5f8d9ffe5f6',
                    'Content-Type: application/json',
                    'Accept: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $authObj;
        }
    }

    // Step 2 To check return status (Pass the reference_id which is returned from Step 1)
    function getReturnStatus($ref_id = null)
    {
        $authObj = $this->authGstinPortalObj;
        if ($authObj["status"] == "success") {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mastergst.com/gstr/retstatus?gstin=' . $this->branch_gstin . '&returnperiod=' . $this->periodGstr1 . '&refid=' . $ref_id . '&email=developer%40vitwo.in',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'gst_username: ' . $authObj["data"]["gstinUsername"],
                    'state_cd: ' . $authObj["data"]["gstinStateCode"],
                    'ip_address: ' . $authObj["data"]["ipAddress"],
                    'txn: ' . $authObj["data"]["authTnxId"],
                    'client_id: GSPfc3af0aa-1ae5-45be-8f6f-34d0baa63594',
                    'client_secret: GSP6e50f5dd-7787-4c7d-a576-a5f8d9ffe5f6',
                    'Content-Type: application/json',
                    'Accept: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $authObj;
        }
    }

    // Step	3: New Proceed to file.
    function newProceedfile()
    {
        $authObj = $this->authGstinPortalObj;
        if ($authObj["status"] == "success") {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mastergst.com/all/newproceedfile?gstin=' . $this->branch_gstin . '&retperiod=' . $this->periodGstr1 . '&type=GSTR1&email=developer%40vitwo.in',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'gst_username: ' . $authObj["data"]["gstinUsername"],
                    'state_cd: ' . $authObj["data"]["gstinStateCode"],
                    'ip_address: ' . $authObj["data"]["ipAddress"],
                    'txn: ' . $authObj["data"]["authTnxId"],
                    'client_id: GSPfc3af0aa-1ae5-45be-8f6f-34d0baa63594',
                    'client_secret: GSP6e50f5dd-7787-4c7d-a576-a5f8d9ffe5f6',
                    'Content-Type: application/json',
                    'Accept: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $authObj;
        }
    }

    // Step	4:Get return status.

    // call getReturnStatus(reference_id);

    //Step 5: Generate	Return	Summary
    function getGstr1Summary()
    {
        $authObj = $this->authGstinPortalObj;
        if ($authObj["status"] == "success") {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mastergst.com/gstr1/retsum?gstin=' . $this->branch_gstin . '&retperiod=' . $this->periodGstr1 . '&email=developer%40vitwo.in&smrytyp=L',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'gst_username: ' . $authObj["data"]["gstinUsername"],
                    'state_cd: ' . $authObj["data"]["gstinStateCode"],
                    'ip_address: ' . $authObj["data"]["ipAddress"],
                    'txn: ' . $authObj["data"]["authTnxId"],
                    'client_id: GSPfc3af0aa-1ae5-45be-8f6f-34d0baa63594',
                    'client_secret: GSP6e50f5dd-7787-4c7d-a576-a5f8d9ffe5f6',
                    'Accept: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $authObj;
        }
    }

    // Step	6: Generate OTP for EVC (Pass the authorized person's pan for OTP authentication)
    function generateOtpForEvc($pan = null)
    {
        $authObj = $this->authGstinPortalObj;
        if ($authObj["status"] == "success") {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mastergst.com/authentication/otpforevc?email=developer%40vitwo.in&gstin=' . $this->branch_gstin . '&pan=' . $pan . '&form_type=R1',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'gst_username: ' . $authObj["data"]["gstinUsername"],
                    'state_cd: ' . $authObj["data"]["gstinStateCode"],
                    'ip_address: ' . $authObj["data"]["ipAddress"],
                    'txn: ' . $authObj["data"]["authTnxId"],
                    'client_id: GSPfc3af0aa-1ae5-45be-8f6f-34d0baa63594',
                    'client_secret: GSP6e50f5dd-7787-4c7d-a576-a5f8d9ffe5f6',
                    'Accept: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $authObj;
        }
    }

    // Step7: File GSTR1

    function fileGstr1($pan=null, $otp=null)
    {
        $authObj = $this->authGstinPortalObj;
        $checksumObj = $this->getGstr1Summary();
        $payload = json_encode($checksumObj["data"], true);
        $url = 'https://api.mastergst.com/gstr1/retevcfile?email=developer%40vitwo.in&pan='.$pan.'&evcotp='.$otp;
        if ($authObj["status"] == "success") {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    'gstin: ' . $this->branch_gstin,
                    'ret_period: ' . $this->periodGstr1,
                    'gst_username: ' . $authObj["data"]["gstinUsername"],
                    'state_cd: ' . $authObj["data"]["gstinStateCode"],
                    'ip_address: ' . $authObj["data"]["ipAddress"],
                    'txn: ' . $authObj["data"]["authTnxId"],
                    'client_id: GSPfc3af0aa-1ae5-45be-8f6f-34d0baa63594',
                    'client_secret: GSP6e50f5dd-7787-4c7d-a576-a5f8d9ffe5f6',
                    'Content-Type: application/json',
                    'Accept: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true)+["URL"=>$url];
        } else {
            return $authObj;
        }
    }




    // function submitGstr1Data()
    // {
    //     $authObj = $this->authGstinPortalObj;
    //     if ($authObj["status"] == "success") {

    //         $payloadArr = [
    //             "gstin" => $this->branch_gstin,
    //             "ret_period" => $this->periodGstr1,
    //         ];

    //         $curl = curl_init();
    //         curl_setopt_array($curl, array(
    //             CURLOPT_URL => 'https://api.mastergst.com/gstr1/retsubmit?email=developer%40vitwo.in',
    //             CURLOPT_RETURNTRANSFER => true,
    //             CURLOPT_ENCODING => '',
    //             CURLOPT_MAXREDIRS => 10,
    //             CURLOPT_TIMEOUT => 0,
    //             CURLOPT_FOLLOWLOCATION => true,
    //             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //             CURLOPT_CUSTOMREQUEST => 'POST',
    //             CURLOPT_POSTFIELDS => json_encode($payloadArr, true),
    //             CURLOPT_HTTPHEADER => array(
    //                 'gstin: ' . $this->branch_gstin,
    //                 'ret_period: ' . $this->periodGstr1,
    //                 'gst_username: ' . $authObj["data"]["gstinUsername"],
    //                 'state_cd: ' . $authObj["data"]["gstinStateCode"],
    //                 'ip_address: ' . $authObj["data"]["ipAddress"],
    //                 'txn: ' . $authObj["data"]["authTnxId"],
    //                 'client_id: GSPfc3af0aa-1ae5-45be-8f6f-34d0baa63594',
    //                 'client_secret: GSP6e50f5dd-7787-4c7d-a576-a5f8d9ffe5f6',
    //                 'Content-Type: application/json',
    //                 'Accept: application/json'
    //             ),
    //         ));

    //         $response = curl_exec($curl);
    //         curl_close($curl);
    //         return json_decode($response, true);
    //     } else {
    //         return $authObj;
    //     }
    // }
}
