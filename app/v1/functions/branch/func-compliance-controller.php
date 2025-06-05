<?php

class AuthGstinPortal
{

    protected $company_id;
    protected $branch_id;
    protected $created_by;
    protected $updated_by;

    protected $branch_gstin;
    protected $branch_gstin_statecode;
    protected $branch_gstin_username;
    protected $branch_ip_address;

    protected $api_client_id;
    protected $api_client_secret;
    protected $api_client_email;


    function __construct()
    {
        global $company_id, $branch_id, $created_by, $updated_by;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;

        $this->api_client_id = "GSPfc3af0aa-1ae5-45be-8f6f-34d0baa63594";
        $this->api_client_secret = "GSP6e50f5dd-7787-4c7d-a576-a5f8d9ffe5f6";
        $this->api_client_email = "developer@vitwo.in";

        $this->branch_ip_address = $_SERVER['REMOTE_ADDR'] ?? "";

        // $this->branch_gstin = "19AADCM4805F1ZL";
        // $this->branch_gstin = "19AABCZ0038M1Z2";
        // $this->branch_gstin_statecode = "19";
        // $this->branch_gstin_username = "medilink_98";
        // $this->branch_gstin_username = "LIVOTECH";
        // $this->branch_gstin_username = "medilink_9888";

        $branchDetailsObj = queryGet('SELECT `branch_gstin`, `branch_gstin_username` FROM `erp_branches` WHERE `branch_status`="active" AND `branch_id` =' . $this->branch_id);
        if ($branchDetailsObj["status"] == "success") {
            $this->branch_gstin = $branchDetailsObj["data"]["branch_gstin"];
            $this->branch_gstin_statecode = substr($this->branch_gstin, 0, 2);
            $this->branch_gstin_username = $branchDetailsObj["data"]["branch_gstin_username"];
        }
    }

    function checkAuth()
    {
        $isNeedOtp = true;
        $prevAuthSql = 'SELECT *, NOW() AS now, TIMESTAMPDIFF(SECOND, `createdAt`, NOW()) AS authTimeDifference FROM `erp_compliance_auth` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' ORDER BY `authId` DESC LIMIT 1';
        $prevAuthObj = queryGet($prevAuthSql);
        if ($prevAuthObj["status"] == "success") {
            if ($prevAuthObj["data"]["authTimeDifference"] < 21600) {  // 21600 seconds = 6 hours
                $isNeedOtp = false;
            }
        }

        if ($isNeedOtp) {
            return [
                "status" => "warning",
                "message" => "Not authorized to access, please connect the server",
                "data" => []
            ];
        } else {
            return [
                "status" => "success",
                "message" => "Already Authenticated",
                "data" => $prevAuthObj["data"]
            ];
        }
    }

    function sendOtp()
    {

        unset($_SESSION["masterGstAuthOtpTnx"]);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.mastergst.com/authentication/otprequest?email=' . $this->api_client_email,
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
                'client_id: ' . $this->api_client_id,
                'client_secret: ' . $this->api_client_secret,
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $responseObj = json_decode($response, true);
        curl_close($curl);
        if ($responseObj["status_cd"] == 1) {

            $_SESSION["masterGstAuthOtpTnx"] = $responseObj["header"]["txn"];

            return [
                "status" => "success",
                "message" => "Otp has been sent successfully",
                "data" => []
            ];
        } else {
            return [
                "status" => "warning",
                "message" => $responseObj["error"]["message"] ?? "Invalid credentials or API access not allowed",
                "data" => []
            ];
        }
        // echo $response;
    }

    function verifyOtp($authOtp = null)
    {

        $authTnx = $_SESSION["masterGstAuthOtpTnx"] ?? "";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.mastergst.com/authentication/authtoken?email=' . $this->api_client_email . '&otp=' . $authOtp,
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
                'txn: ' . $authTnx,
                'client_id: ' . $this->api_client_id,
                'client_secret: ' . $this->api_client_secret,
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $responseObj = json_decode($response, true);
        curl_close($curl);
        if ($responseObj["status_cd"] == 1) {

            $newAuthDetailSql = 'INSERT INTO `erp_compliance_auth` SET `company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`gstin`="' . $this->branch_gstin . '", `gstinStateCode`="' . $this->branch_gstin_statecode . '",`gstinUsername`="' . $this->branch_gstin_username . '",`ipAddress`="' . $this->branch_ip_address . '",`authTnxId`="' . $authTnx . '",`createdBy`="' . $this->created_by . '"';

            $newAuthDetailsObj = queryInsert($newAuthDetailSql);

            if ($newAuthDetailsObj["status"] == "success") {
                return [
                    "status" => "success",
                    "message" => "Otp has been verified successfully",
                    "data" => $responseObj
                ];
            } else {
                return [
                    "status" => "warning",
                    "message" => "Something went wrong, try again",
                    "data" => $responseObj
                ];
            }
        } else {
            return [
                "status" => "warning",
                "message" => "Invalid OTP, please try again with valid OTP",
                "data" => $responseObj
            ];
        }
    }

    // function refreshToken()
    // {
    //     $ch = curl_init();

    //     curl_setopt($ch, CURLOPT_URL, 'https://api.mastergst.com/authentication/refreshtoken?email=developer%40vitwo.in');
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


    //     $headers = array();
    //     $headers[] = 'Accept: application/json';
    //     $headers[] = 'Gst_username: medilink_98';
    //     $headers[] = 'State_cd: 19';
    //     $headers[] = 'Ip_address: 122.160.53.68';
    //     $headers[] = 'Txn: 8f41ff87a7594691ab5b10c1441828f5';
    //     $headers[] = 'Client_id: GSPfc3af0aa-1ae5-45be-8f6f-34d0baa63594';
    //     $headers[] = 'Client_secret: GSP6e50f5dd-7787-4c7d-a576-a5f8d9ffe5f6';
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //     $response = curl_exec($ch);
    //     $responseObj = json_decode($response, true);
    //     curl_close($ch);
    //     if ($responseObj["status_cd"] == 1) {

    //         $_SESSION["masterGstAuthOtpTnx"] = $responseObj["header"]["txn"];

    //         return [
    //             "status" => "success",
    //             "message" => "Otp has been sent successfully",
    //             "data" => []
    //         ];

    //     } else {
    //         return [
    //             "status" => "warning",
    //             "message" => $responseObj["error"]["message"] ?? "Invalid credentials or API access not allowed",
    //             "data" => []
    //         ];
    //     }
    // }
}


class ComplianceGstr2b extends AuthGstinPortal
{


    function getGstr2bData($mmyyyy = null)
    {
        $mmyyyy = ($mmyyyy == null) ? date('mY', strtotime("last month")) : $mmyyyy;

        $sql = 'SELECT * FROM `erp_branch_gstr2b_portal_invoices` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `filingPeriod`=' . $mmyyyy;

        $dataObj = queryGet($sql, true);

        if ($dataObj["status"] == "success") {
            return [
                "status" => "success",
                "message" => "GSTR2B data fetched successfully.",
                "data" => $dataObj["data"]
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "GSTR2B data not found.",
                "data" => []
            ];
        }
    }

    function fetchGstr2bData($mmyyyy = null)
    {
        // $mmyyyy = "012023";
        $mmyyyy = ($mmyyyy == null) ? date('mY', strtotime("last month")) : $mmyyyy;

        $gstr2bDbDataObj = $this->getGstr2bData($mmyyyy);
        if ($gstr2bDbDataObj["status"] == "success") {
            return $gstr2bDbDataObj;
            exit();
        }

        $checkAuthObj = $this->checkAuth();
        if ($checkAuthObj["status"] == "success") {

            $gstinStateCode = $checkAuthObj["data"]["gstinStateCode"] ?? "";
            $gstin = $checkAuthObj["data"]["gstin"] ?? "";
            $gstinUsername = $checkAuthObj["data"]["gstinUsername"] ?? "";
            $authTnxId = $checkAuthObj["data"]["authTnxId"] ?? "";

            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://api.mastergst.com/gstr2b/all?email=' . $this->api_client_email . '&gstin=' . $gstin . '&rtnprd=' . $mmyyyy,
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
                        'ip_address: ' . $this->branch_ip_address,
                        'txn: ' . $authTnxId,
                        'client_id: ' . $this->api_client_id,
                        'client_secret: ' . $this->api_client_secret,
                        'Accept: application/json'
                    )
                )
            );
            $response = curl_exec($curl);
            $responseObj = json_decode($response, true);

            // return $responseObj;
            // exit();

            if ($responseObj["status_cd"] == 1) {


                $gstr2bDocData = $responseObj["data"]["data"]["docdata"] ?? [];

                $noErrorsGstr2bDataSaving = 0;
                $noErrorsGstr2bDataSavingSql = [];

                foreach ($gstr2bDocData["b2b"] as $oneVendor) {
                    foreach ($oneVendor["inv"] as $oneInv) {
                        $invTotalTax = 0;
                        $invCgstAmount = 0;
                        $invSgstAmount = 0;
                        $invIgstAmount = 0;
                        $invCessAmount = 0;
                        foreach ($oneInv["items"] as $oneItem) {
                            $invTotalTax += ($oneItem["igst"] + $oneItem["cgst"] + $oneItem["sgst"] + $oneItem["cess"]);
                        }

                        $insertSql = 'INSERT INTO `erp_branch_gstr2b_portal_invoices` 
                            SET 
                            `company_id`=' . $this->company_id . ',
                            `branch_id`=' . $this->branch_id . ',
                            `filingPeriod`="' . $mmyyyy . '",
                            `filingDate`= STR_TO_DATE("' . $oneVendor["supfildt"] . '", "%d-%m-%Y"),
                            `invDate`= STR_TO_DATE("' . $oneInv["dt"] . '", "%d-%m-%Y"),
                            `itcAvl`="' . $oneInv["itcavl"] . '",
                            `invType`="' . $oneInv["typ"] . '",
                            `revCharge`="' . $oneInv["rev"] . '",
                            `vendorGstin`="' . $oneVendor["ctin"] . '",
                            `vendorName`="' . $oneVendor["trdnm"] . '",
                            `invoiceNo`="' . $oneInv["inum"] . '",
                            `invAmount`=' . $oneInv["val"] . ',
                            `taxAmount`=' . $invTotalTax . ',
                            `cgstAmount`=' . $invCgstAmount . ',
                            `sgstAmount`=' . $invSgstAmount . ',
                            `igstAmount`=' . $invIgstAmount . ',
                            `cessAmount`=' . $invCessAmount . ',
                            `portalData`="",
                            `createdBy`="' . $this->created_by . '",
                            `updatedBy`="' . $this->updated_by . '"';

                        $insertObj = queryInsert($insertSql);

                        if ($insertObj["status"] != "success") {
                            $noErrorsGstr2bDataSaving++;
                            $noErrorsGstr2bDataSavingSql[] = $insertSql;
                        }
                    }
                }

                if ($noErrorsGstr2bDataSaving == 0) {
                    return $this->getGstr2bData($mmyyyy);
                } else {
                    $devPrevDataSql = 'DELETE FROM `erp_branch_gstr2b_portal_invoices` 
                                WHERE 
                                `company_id`=' . $this->company_id . ',
                                `branch_id`=' . $this->branch_id . ',
                                `filingPeriod`="' . $mmyyyy . '"';

                    queryDelete($devPrevDataSql);

                    return [
                        "status" => "warning",
                        "message" => "All data not inserted successfully",
                        "error" => $noErrorsGstr2bDataSaving,
                        "error_sql" => $noErrorsGstr2bDataSavingSql
                    ];
                }
            } else {
                return [
                    "status" => "warning",
                    "message" => $responseObj["error"]["message"] ?? "Invalid credentials or API access not allowed",
                    "data" => $responseObj
                ];
            }
        } else {
            return [
                "status" => "warning",
                "message" => $checkAuthObj["message"],
                "data" => []
            ];
        }
    }
}
