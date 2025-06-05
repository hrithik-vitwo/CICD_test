<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$location_name = $_POST['loc_name'];
$buildingName = $_POST['buildingName'];
$flatNumber = $_POST['flatNumber'];
$streetName = $_POST['streetName'];
$newLocation = $_POST['newLocation']; 
$newCity = $_POST['newCity'];
$newPinCode = $_POST['newPinCode'];
$newDistrict = $_POST['newDistrict'];
$newState = $_POST['newState']; 
$lat = $_POST['lat'];
$lng = $_POST['lng'];
$othersLocation_status = 'active';


$sql = "SELECT othersLocation_code FROM `" . ERP_BRANCH_OTHERSLOCATION . "` ORDER BY othersLocation_id DESC LIMIT 1";
$lastSoNo = queryGet($sql);
// console($lastSoNo);
if (isset($lastSoNo['data'])) {
    $lastSoNo = $lastSoNo['data']['othersLocation_code'] ?? 0;
} else {
    $lastSoNo = '';
}
$othersLocation_code = getLocationSerialNumber($lastSoNo);

$ins = queryInsert("INSERT INTO `" . ERP_BRANCH_OTHERSLOCATION . "` 
SET
    `company_id`='" . $company_id . "',
    `branch_id`='" . $branch_id . "',
    `othersLocation_name`='" . $location_name . "',
    `othersLocation_code`='" . $othersLocation_code . "',
    `othersLocation_building_no`='" . $buildingName . "',
    `othersLocation_flat_no`='" . $flatNumber . "',
    `othersLocation_street_name`='" . $streetName . "',
    `othersLocation_pin_code`='" . $newPinCode . "',
    `othersLocation_location`='" . $newLocation . "',
    `othersLocation_city`='" . $newCity . "',
    `othersLocation_district`='" . $newDistrict . "',
    `othersLocation_state`='" . $newState . "',
    `othersLocation_lat`='" . $lat . "',
    `othersLocation_lng`='" . $lng . "',
    `othersLocation_status`='" . $othersLocation_status . "'");

    //console($ins);
    $lastId = $ins['insertedId'];


    $data = "{$buildingName},{$flatNumber},{$streetName},{$newPinCode},{$newLocation}, {$newCity}, {$newDistrict},{$newState}";
    $responseData['status'] = "success";
    $responseData['message'] = "Data Found";
    $responseData['data'] = $data;
    $responseData['lastInsertedId'] = $lastId;
    

    echo json_encode($responseData);




}

?>