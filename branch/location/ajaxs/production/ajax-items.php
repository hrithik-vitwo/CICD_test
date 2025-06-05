<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //GET REQUEST
    $itemId = $_GET['itemId'];
    $item = queryGet("SELECT i.*, u.uomName FROM erp_inventory_items AS i JOIN erp_inventory_mstr_uom AS u ON i.baseUnitMeasure = u.uomId WHERE i.itemId = $itemId AND i.location_id = $location_id;");
    //console($item)
    $responseData['code'] = $item['data']['itemCode'];
    $responseData['desc'] = $item['data']['itemDesc'];
    $responseData['uom'] = $item['data']['uomName'];
    echo json_encode($responseData);

} else {
    echo "Something wrong, try again!";
}