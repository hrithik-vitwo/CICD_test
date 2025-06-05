<?php 
// API CODE

use React\Dns\Query\Query;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization,  X-Requested-With");
require_once("../../app/v1/connection-branch-admin.php");

function sendApiResponse($responseArray = [], $statusCode = 200){
    http_response_code($statusCode);
    echo json_encode($responseArray, true);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $lat = $_GET["lat"] ?? "";
    $lng = $_GET["lng"] ?? "";
    $device = $_GET["device"] ?? "";
    $insertObj = queryInsert('INSERT INTO `claimz_tracking` SET `device`="'.$device.'",`lat`="'.$lat.'",`lng`="'.$lng.'"');
    sendApiResponse($insertObj, 200);

}else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
?>