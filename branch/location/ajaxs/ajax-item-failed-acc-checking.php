<?php
require_once("../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if ($_GET['act'] == 'checkItem') {
        $itemId = $_GET['itemId'] ?? 0;
        $res = [];
        if ($itemId != 0) {
            $res = checkItemImpactById($itemId);
        } else {
            $res = ['status' => 'error', 'message' => "Invalid Item Id"];
        }
        echo json_encode($res);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
