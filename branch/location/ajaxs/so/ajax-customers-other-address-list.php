<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-customers-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$CustomersObj = new CustomersController();
if ($_GET['act'] === "otherAddressList") {
    $customerId = $_GET['id'];
    $customerDetails = $CustomersObj->getDataCustomerAddressDetails($customerId)['data'];
    // console($customerDetails);

    if(!empty($customerDetails)) {

        $addressesArr = [];
        // $fullAddressObj = [];

        foreach ($customerDetails as $oneAddress) {
            $checked = ($oneAddress['customer_address_primary_flag'] == 1) ? "checked" : "";

            $fullAddressObj = [
                'recipient_name' => $oneAddress['customer_address_recipient_name'] ?? '',
                'building_no'    => $oneAddress['customer_address_building_no'] ?? '',
                'flat_no'        => $oneAddress['customer_address_flat_no'] ?? '',
                'street_name'    => $oneAddress['customer_address_street_name'] ?? '',
                'pincode'        => $oneAddress['customer_address_pin_code'] ?? '',
                'location'       => $oneAddress['customer_address_location'] ?? '',
                'district'       => $oneAddress['customer_address_district'] ?? '',
                'city'           => $oneAddress['customer_address_city'] ?? '',
                'state'          => $oneAddress['customer_address_state'] ?? '',
                'state_code'     => $oneAddress['customer_address_state_code'] ?? '',
            ];

            $addressForFull = $fullAddressObj;
            
            $addressArr[] = [
            "full_address" => str_replace('"', "'", implode(", ", $addressForFull)),
            "customer_address_city" => $oneAddress['customer_address_city'] ?? '',
            "customer_address_id" => $oneAddress['customer_address_id'] ?? '',
            "customer_address_state_code" => $oneAddress['customer_address_state_code'] ?? '',
            "checked" => $checked
            ];
            
        }
        // console($addressArr);
        $responseData["status"] = "success";
        $responseData["message"] = "Fetched Address details";
        $responseData["data"] = $addressArr;
    } else {
        $responseData["status"] = "warning";
        $responseData["message"] = "No other address found!";
    }

    echo json_encode($responseData);
}