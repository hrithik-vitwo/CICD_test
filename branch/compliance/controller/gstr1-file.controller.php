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
    private $dbObj;

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

        $this->dbObj = new Database();

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

    function updateFilingStatus($status, $arn=null){
        if($arn==null) {
            $this->dbObj->queryGet("UPDATE `erp_compliance_gstr1` SET `gstr1_return_file_status`=$status WHERE `gstr1_return_period`='$this->periodGstr1' AND `company_id`=$this->company_id AND `branch_id`=$this->branch_id");
        }else{
            $this->dbObj->queryGet("UPDATE `erp_compliance_gstr1` SET `gstr1_return_file_status`=$status, `gstr1_return_file_arn`='$arn' WHERE `gstr1_return_period`='$this->periodGstr1' AND `company_id`=$this->company_id AND `branch_id`=$this->branch_id");
        }
    }

    function addLogOfRequest($apiPayload = [], $apiResponse = [], $logType = "")
    {
         $this->dbObj->queryInsert("INSERT INTO `erp_compliance_gstr1_log` SET `return_id`=$this->periodGstr1,`log_type`='$logType',`api_payload`='" . json_encode($apiPayload, true) . "',`api_response`='" . json_encode($apiResponse, true) . "',`created_by`='$this->created_by'");
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
            $responseObj = json_decode($response, true);

            $this->addLogOfRequest($jsonData, $responseObj, "save");
            if ($responseObj["status_cd"] == 1) {
                $this->updateFilingStatus(3);
                return [
                    "status" => "success",
                    "message" => $responseObj["status_desc"],
                    "response" => $responseObj
                ];
            } else {
                return [
                    "status" => "warning",
                    "message" => $responseObj["error"]["message"],
                    "response" => $responseObj
                ];
            }
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
            $responseObj = json_decode($response, true);

            $this->addLogOfRequest($payloadArr, $responseObj, "reset");
            if ($responseObj["status_cd"] == 1) {
                $this->updateFilingStatus(4);
                return [
                    "status" => "success",
                    "message" => $responseObj["status_desc"],
                    "response" => $responseObj
                ];
            } else {
                return [
                    "status" => "warning",
                    "message" => $responseObj["error"]["message"],
                    "response" => $responseObj
                ];
            }
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
            $responseObj = json_decode($response, true);
            $this->addLogOfRequest(["retperiod" => $this->periodGstr1], $responseObj, "proceed");
            if ($responseObj["status_cd"] == 1) {
                $this->updateFilingStatus(5);
                return [
                    "status" => "success",
                    "message" => $responseObj["status_desc"],
                    "response" => $responseObj
                ];
            } else {
                return [
                    "status" => "warning",
                    "message" => $responseObj["error"]["message"],
                    "response" => $responseObj
                ];
            }
        } else {
            return $authObj;
        }
    }

    // Step	4:Get return status.

    // call getReturnStatus(reference_id);

    //Step 5: Generate Return Summary
    function getGstr1Summary()
    {
        $authObj = $this->authGstinPortalObj;
        if ($authObj["status"] == "success") {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mastergst.com/gstr1/retsum?gstin=' . $this->branch_gstin . '&retperiod=' . $this->periodGstr1 . '&email=developer%40vitwo.in',
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
            $responseObj = json_decode($response, true);
            $this->addLogOfRequest(["retperiod" => $this->periodGstr1], $responseObj, "summary");
            if ($responseObj["status_cd"] == 1) {
                return [
                    "status" => "success",
                    "message" => $responseObj["status_desc"],
                    "response" => $responseObj
                ];
            } else {
                return [
                    "status" => "warning",
                    "message" => $responseObj["error"]["message"],
                    "response" => $responseObj
                ];
            }
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
            $responseObj = json_decode($response, true);

            $this->addLogOfRequest(["retperiod"=>$this->periodGstr1, "pan"=> $pan, "form_type"=>"R1"], $responseObj, "EVC");
            if ($responseObj["status_cd"] == 1) {
                $this->updateFilingStatus(6);
                return [
                    "status" => "success",
                    "message" => $responseObj["status_desc"],
                    "response" => $responseObj
                ];
            } else {
                return [
                    "status" => "warning",
                    "message" => $responseObj["error"]["message"],
                    "response" => $responseObj
                ];
            }
        } else {
            return $authObj;
        }
    }

    // Step7: File GSTR1

    function fileGstr1($pan = null, $otp = null)
    {
        $authObj = $this->authGstinPortalObj;
        $checksumObj = $this->getGstr1Summary();
        $checksumData = $checksumObj["response"]["data"] ?? [];
        $payload = json_encode($checksumData, true);
        $url = 'https://api.mastergst.com/gstr1/retevcfile?email=developer%40vitwo.in&pan=' . $pan . '&evcotp=' . $otp;
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
            // return json_decode($response, true) + ["URL" => $url];
            $responseObj = json_decode($response, true);
            $this->addLogOfRequest($checksumData, $responseObj, "FILING");
            if ($responseObj["status_cd"] == 1) {
                $this->updateFilingStatus(7, $responseObj["data"]["ack_num"] ?? "");
                return [
                    "status" => "success",
                    "message" => $responseObj["status_desc"],
                    "response" => $responseObj,
                    "URL" => $url
                ];
            } else {
                return [
                    "status" => "warning",
                    "message" => $responseObj["error"]["message"],
                    "response" => $responseObj,
                    "URL" => $url,
                    "payload" => json_decode($payload, true)
                ];
            }
        } else {
            return $authObj;
        }
    }
}
