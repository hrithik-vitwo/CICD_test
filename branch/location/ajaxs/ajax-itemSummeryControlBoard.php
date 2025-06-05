<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-goods-controller.php");
$headerData = array('Content-Type: application/json');
$dbObj = new Database();
$goodsObj = new GoodsController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST['act'] == "summeryData") {
        $itemId = $_POST['id'] ?? 0;
        if ($itemId == "" || $itemId == 0) {
            echo json_encode(["status" => "error", "message" => "Item ID Required"]);
            exit();
        }
        $res = $goodsObj->generateCurrentMwp($itemId);
        echo json_encode($res);
    }

    if ($_POST['act'] == "summeryDataUpdate") {

        $itemId = $_POST['id'] ?? "";
        $itemNewMap = $_POST['newmap'] ?? "";

        if ($itemId == "") {
            echo json_encode(["status" => "error", "message" => "Item ID Required"]);
            exit();
        }
        if ($itemNewMap == "") {
            echo json_encode(["status" => "error", "message" => "Item New Map Required"]);
            exit();
        }
        
        $res = $goodsObj->updateDirectMapToSummery($itemId, $itemNewMap);

        echo json_encode($res);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Error!"]);
}
