<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-customers-controller.php");

$headerData = array('Content-Type: application/json');
$responseData = [];

$CustomersObj = new CustomersController();

if (isset($_GET['act'])) {
    $action = $_GET['act'];

    if ($action === "customerAddress") {
        if (isset($_GET['customerId'])) {
            $customerId = $_GET['customerId'];
            $customerDetails = $CustomersObj->getDataCustomerAddressDetailsByPrimary($customerId)['data'][0];

            if ($customerDetails) {
                $data = "{$customerDetails['customer_address_recipient_name']}, {$customerDetails['customer_address_building_no']}, {$customerDetails['customer_address_flat_no']}, {$customerDetails['customer_address_street_name']}, {$customerDetails['customer_address_pin_code']}, {$customerDetails['customer_address_location']}, {$customerDetails['customer_address_city']}, {$customerDetails['customer_address_district']}, {$customerDetails['customer_address_state']},<span class=' bg-success'> {$customerDetails['customer_address_state_code']}</span>";
                $responseData['status'] = "success";
                $responseData['message'] = "Data Found";
                $responseData['data'] = $data;
            } else {
                $responseData['status'] = "warning";
                $responseData['message'] = "No customer address details found.";
            }
        } else {
            $responseData['status'] = "warning";
            $responseData['message'] = "Invalid customer ID.";
        }
    } elseif ($action === "shipAddressRadio") {
        if (isset($_GET['addressKey'])) {
            $addressKey = $_GET['addressKey'];
            $addressDetails = $CustomersObj->getDataCustomerAddressDetailsById($addressKey)['data'][0];

            if ($addressDetails) {
                $data = "{$addressDetails['customer_address_recipient_name']}, {$addressDetails['customer_address_building_no']}, {$addressDetails['customer_address_flat_no']}, {$addressDetails['customer_address_street_name']}, {$addressDetails['customer_address_pin_code']}, {$addressDetails['customer_address_location']}, {$addressDetails['customer_address_city']}, {$addressDetails['customer_address_district']}, {$addressDetails['customer_address_state']},<span class='stateCodeSpan'>{$addressDetails['customer_address_state_code']}</span>";
                $responseData['status'] = "success";
                $responseData['message'] = "Data Found";
                $responseData['data'] = $data;
            } else {
                $responseData['status'] = "warning";
                $responseData['message'] = "No address details found.";
            }
        } else {
            $responseData['status'] = "warning";
            $responseData['message'] = "Invalid address key.";
        }
    } elseif ($action === "shipAddressSave") {
        if (isset($_GET['customerId'], $_GET['billingNo'], $_GET['flatNo'], $_GET['streetName'], $_GET['location'], $_GET['city'], $_GET['pinCode'], $_GET['district'], $_GET['state'])) {
            $customerId = $_GET['customerId'];
            $billingNo = $_GET['billingNo'];
            $recipientName = $_GET['recipientName'];
            $flatNo = $_GET['flatNo'];
            $streetName = $_GET['streetName'];
            $location = $_GET['location'];
            $city = $_GET['city'];
            $pinCode = $_GET['pinCode'];
            $district = $_GET['district'];
            $state = $_GET['state'];
            $stateCode = $_GET['stateCode'];

            $ins = "INSERT INTO `erp_customer_address` 
                        SET 
                            `customer_id`='$customerId',
                            `customer_address_primary_flag`='0',
                            `customer_address_recipient_name`='$recipientName',
                            `customer_address_building_no`='$billingNo',
                            `customer_address_flat_no`='$flatNo',
                            `customer_address_street_name`='$streetName',
                            `customer_address_pin_code`='$pinCode',
                            `customer_address_location`='$location',
                            `customer_address_city`='$city',
                            `customer_address_state_code`='$stateCode',
                            `customer_address_district`='$district',
                            `customer_address_state`='$state'";

            if ($dbCon->query($ins)) {
                $lastId = $dbCon->insert_id;
                $sql = "SELECT * FROM `erp_customer_address` WHERE `customer_address_id`='$lastId'";
                $res = $dbCon->query($sql);
                $row = $res->fetch_assoc();

                if ($row) {
                    $data = "{$row['customer_address_recipient_name']}, {$row['customer_address_building_no']}, {$row['customer_address_flat_no']}, {$row['customer_address_street_name']}, {$row['customer_address_pin_code']}, {$row['customer_address_location']}, {$row['customer_address_city']}, {$row['customer_address_district']}, {$row['customer_address_state']}, <span class='stateCodeSpan bg-danger'>{$row['customer_address_state_code']} </span>";
                    $responseData['status'] = "success";
                    $responseData['message'] = "Data Found";
                    $responseData['data'] = $data;
                    $responseData['lastInsertedId'] = $lastId;
                } else {
                    $responseData['status'] = "warning";
                    $responseData['message'] = "Error retrieving saved address details.";
                }
            } else {
                $responseData['status'] = "warning";
                $responseData['message'] = "Error saving address";
            }
        } else {
            $responseData['status'] = "warning";
            $responseData['message'] = "Incomplete parameters for saving address.";
        }
    } else {
        $responseData['status'] = "warning";
        $responseData['message'] = "Invalid action.";
    }
} else {
    $responseData['status'] = "warning";
    $responseData['message'] = "No action specified.";
}

echo json_encode($responseData);
