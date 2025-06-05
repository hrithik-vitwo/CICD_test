<?php

// require_once("../../app/v1/functions/branch/func-compliance-controller.php");

class ComplianceGSTR3b
{
    
    private $company_id;
    private $branch_id;
    private $location_id;
    private $created_by;
    private $updated_by;
    private $compOpeningDate;
    private $branch_gstin;
    private $branch_gstin_code;
    protected $api_client_id;
    protected $api_client_secret;
    protected $api_client_email;
    protected $branch_gstin_statecode;
    protected $branch_gstin_username;
    protected $branch_ip_address;
    private $periodGstr1 = null;


    private $authGstinPortalObj;
    private $dbObj;

    function __construct($authObj = null, $returnPeriod = null)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        global $branch_gstin;
        global $compOpeningDate;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->location_id = $location_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->compOpeningDate = $compOpeningDate;
        $this->branch_gstin = $branch_gstin;
        $this->branch_gstin_code = substr($branch_gstin, 0, 2);
        $this->api_client_id = "GSPfc3af0aa-1ae5-45be-8f6f-34d0baa63594";
        $this->api_client_secret = "GSP6e50f5dd-7787-4c7d-a576-a5f8d9ffe5f6";
        $this->api_client_email = "developer@vitwo.in";
        $branchDetailsObj = queryGet('SELECT `branch_gstin`, `branch_gstin_username` FROM `erp_branches` WHERE `branch_status`="active" AND `branch_id` =' . $this->branch_id);
        if ($branchDetailsObj["status"] == "success") {
            $this->branch_gstin = $branchDetailsObj["data"]["branch_gstin"];
            $this->branch_gstin_statecode = substr($this->branch_gstin, 0, 2);
            $this->branch_gstin_username = $branchDetailsObj["data"]["branch_gstin_username"];
        }

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
    function setUpcomingGstr3bFillingDate()
    {
        $dbObj = new Database(true);
        $dbObj->setSuccessMsg("Gstr3b file date saved success");
        $dbObj->setErrorMsg("Gstr3b file date saved failed!");
        $gstr1MonthlyArr = [
            "01" => "02",
            "02" => "03",
            "03" => "04",
            "04" => "05",
            "05" => "06",
            "06" => "07",
            "07" => "08",
            "08" => "09",
            "09" => "10",
            "10" => "11",
            "11" => "12",
            "12" => "01"
        ];

        $getLastGstr2bFileDaySqlObj = $dbObj->queryGet("SELECT gstr3b_return_period FROM `erp_compliance_gstr3b` WHERE `company_id`=" . $this->company_id . " AND `branch_id`=" . $this->branch_id . " ORDER BY `id` DESC limit 1 ");
        if ($getLastGstr2bFileDaySqlObj["status"] == "success") {
            $getLastGstr2bFileDay = $getLastGstr2bFileDaySqlObj['data']['gstr3b_return_period'];
        } else {
            $getLastGstr2bFileDay = $this->compOpeningDate;
        }
        // console($getLastGstr2bFileDay);

        $gst2bFileFreqDateObj = queryGet("SELECT branch_gstin_file_r3b_day,branch_gstin_file_frequency FROM `erp_branches` WHERE company_id= $this->company_id AND branch_id= $this->branch_id");
        $branch_gstin_file_frequency = $gst2bFileFreqDateObj['data']['branch_gstin_file_frequency'];

        $months = [];
        if ($branch_gstin_file_frequency == "monthly") {
            $start = DateTime::createFromFormat('d-m-Y', date('d-m-Y', strtotime('01-'.substr($getLastGstr2bFileDay,0,2).'-'.substr($getLastGstr2bFileDay,2))));
            $end = DateTime::createFromFormat('d-m-Y', date('d-m-Y'));
            if (!$start || !$end) {
                return [];
            }

            $start->modify('first day of next month');
            while ($start <= $end) {
                $months[] = [
                    "period" => $start->format('mY'),
                    "date" => $start->format('Y-m-d')
                ];
                $start->modify('first day of next month');
            }
        }

        foreach ($months as $month) {
            $insertSql = $dbObj->queryInsert("INSERT INTO `erp_compliance_gstr3b` SET `company_id`=$this->company_id,`branch_id`=$this->branch_id,`gstr3b_return_period`='" . $month['period'] . "',`created_at`='" . $month['date'] . "',`created_by`='Auto',`updated_by`='Auto',`status`='active'");
        }
        return $dbObj->queryFinish();
        // return $getLastGstr2bFileDay;
    }

    function getGstr3bSummary($returnPeriod = null)
    {
        $authObj = $this->authGstinPortalObj;
        if ($authObj["status"] == "success") {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mastergst.com/gstr3b/retsum?gstin=' . $this->branch_gstin . '& retperiod=' . $returnPeriod . '& email=' . $this->api_client_email,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'gst_username: ' . $this->branch_gstin_username,
                    'state_cd: ' . $this->branch_gstin_statecode,
                    'ip_address: ' . $this->branch_ip_address,
                    'txn: ' . $authObj["data"]["authTnxId"],
                    'client_id: ' . $this->api_client_id,
                    'client_secret: ' . $this->api_client_secret,
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
    function getGSTR2bData($returnPeriod = null)
    {
        $resultObj =  $this->dbObj->queryGet("SELECT * FROM `erp_compliance_gstr2b_documents` WHERE `company_id`=$this->company_id AND `branch_id`=$this->branch_id AND `return_period`='$returnPeriod' AND `status`='reconciled'", true);
        if ($resultObj["status"] == "success") {
            return [
                "status" => "success",
                "message" => "success",
                "data" => $resultObj['data'] ?? []
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "warning",
                "data" => [],
                "sql" => $resultObj
            ];
        }
    }

    // Step : 1
    function saveGstr3bData($jsonData = null)
    {
        $authObj = $this->authGstinPortalObj;
        if ($authObj["status"] == "success") {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mastergst.com/gstr3b/retsave?email=developer%40vitwo.in',
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
                    "response" => $responseObj,
                    "jsonData"=>$jsonData
                ];
            }
        } else {
            return $authObj;
        }
    }

    function saveGstr3bITCData($jsonData = null)
    {
        $authObj = $this->authGstinPortalObj;
        if ($authObj["status"] == "success") {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mastergst.com/gstr3b/retoffset?email=developer%40vitwo.in',
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
}
