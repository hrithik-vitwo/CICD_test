<?php
class CustomersController
{
    function getAllDataCustomer()
    {
        global $dbCon;
        $returnData = [];
        global $company_id;
        global $branch_id;
        global $location_id;

        $sql = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE company_id='" . $company_id . "' AND `customer_status`='active'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $returnData['status'] = "success";
                $returnData['message'] = "Data found";
                $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Data not found";
                $returnData['data'] = [];
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
            $returnData['data'] = [];
        }
        return $returnData;
    }

    // fetch customer details
    function getDataCustomerDetails($id)
    {
        global $dbCon;
        $returnData = [];
        $sql = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`=$id AND `customer_status`!='deleted'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $returnData['status'] = "success";
                $returnData['message'] = "Data found";
                $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Data not found";
                // $returnData['data'] = [];
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
            $returnData['data'] = [];
        }
        return $returnData;
    }

    // fetch customer log details
    function getCustomersInvoiceLogDetails($id)
    {
        $sql = "SELECT * FROM `" . ERP_CUSTOMER_INVOICE_LOGS . "` WHERE `customer_id`=$id";
        return queryGet($sql);
    }

    // fetch customers address details
    function getDataCustomerAddressDetails($id)
    {
        global $dbCon;
        $returnData = [];
        $sql = "SELECT * FROM `" . ERP_CUSTOMER_ADDRESS . "` WHERE `customer_id`=$id ORDER BY customer_address_id DESC";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $returnData['status'] = "success";
                $returnData['message'] = "Data found";
                $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Data not found";
                // $returnData['data'] = [];
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
            $returnData['data'] = [];
        }
        return $returnData;
    }
    function getDataCustomerAddressDetailsByPrimary($id)
    {
        global $dbCon;
        $returnData = [];
        $sql = "SELECT * FROM `" . ERP_CUSTOMER_ADDRESS . "` WHERE `customer_id`='$id' AND `customer_address_primary_flag`=1";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $returnData['status'] = "success";
                $returnData['message'] = "Data found";
                $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Data not found";
                // $returnData['data'] = [];
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
            $returnData['data'] = [];
        }
        return $returnData;
    }
    function getDataCustomerAddressDetailsById($id)
    {
        global $dbCon;
        $returnData = [];
        $sql = "SELECT * FROM `" . ERP_CUSTOMER_ADDRESS . "` WHERE `customer_address_id`='$id'";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $returnData['status'] = "success";
                $returnData['message'] = "Data found";
                $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Data not found";
                // $returnData['data'] = [];
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
            $returnData['data'] = [];
        }
        return $returnData;
    }

    function addCustomerMail($POST)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        $response = [];

        foreach ($POST['customerMail'] as $key => $data) {
            $customer_id = isset($data['customer_id']) ? $data['customer_id'] : 0;
            $shootingDays = isset($data['shootingDays']) ? $data['shootingDays'] : 0;
            $operator = isset($data['operator']) ? $data['operator'] : null;

            if (!empty($shootingDays)) {
                $insSql = "INSERT INTO `" . ERP_SETTINGS_EMAIL_CUSTOMER_INVOICE . "` 
                            SET
                                `company_id`='$company_id',
                                `branch_id`='$branch_id',
                                `location_id`='$location_id',
                                `customer_id`='$customer_id',
                                `days`='$shootingDays',
                                `operators`='$operator',
                                `created_by`='$created_by',
                                `updated_by`='$updated_by'
                ";
                $data = queryInsert($insSql);

                if ($data['status'] == "success") {
                    $response[] = [
                        "status" => "success",
                        "message" => "Created Successfully",
                        "customer_id" => $customer_id,
                        "shootingDays" => $shootingDays,
                        "operator" => $operator
                    ];
                } else {
                    $response[] = [
                        "status" => "warning",
                        "message" => "Something went wrong",
                        "customer_id" => $customer_id,
                        "shootingDays" => $shootingDays,
                        "operator" => $operator
                    ];
                }
            } else {
                $response[] = [
                    "status" => "warning",
                    "message" => "Missing shootingDays value",
                    "customer_id" => $customer_id,
                    "shootingDays" => $shootingDays,
                    "operator" => $operator
                ];
            }
        }

        return $response;
    }



    function addCustomer($customerName, $customerEmail, $customerPhone)
    {
        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        $returnData = [];

        // $customerName = $POST['customerName'];
        // $customerEmail = $POST['customerEmail'];
        // $customerPhone = $POST['customerPhone'];

        $customerCode = time() . rand(0000, 9999);

        $accMapp = getAllfetchAccountingMappingTbl($company_id);
        $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['customer_gl']);
        $parentGlId = $paccdetails['data']['id'];

        $lastlQuery = "SELECT customer_code FROM `" . ERP_CUSTOMER . "` WHERE `company_id` = '" . $company_id . "'  ORDER BY `customer_id` DESC LIMIT 1";
        $resultLast = queryGet($lastlQuery);
        $rowLast = $resultLast["data"];
        $lastsl = $rowLast['customer_code'];
        $customerCode = getCustomerSerialNumber($lastsl);

        $insertCustomer = "INSERT INTO `" . ERP_CUSTOMER . "`
                SET
                    `company_id`='$company_id',
                    `company_branch_id`='$branch_id',
                    `location_id`='$location_id',
                    `customer_code`='$customerCode',
                    `trade_name`='$customerName',
                    `parentGlId`='$parentGlId',
                    `customer_authorised_person_name`='$customerName',
                    `customer_authorised_person_email`='$customerEmail',
                    `customer_authorised_person_phone`='$customerPhone',
                    `customer_created_by`='" . $created_by . "',
                    `customer_updated_by`='" . $created_by . "',
                    `customer_status`='active'
        ";
        if (mysqli_query($dbCon, $insertCustomer)) {
            $returnData['status'] = "success";
            $returnData['message'] = "Inserted successfully";
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
        }
        return $returnData;
    }
}
