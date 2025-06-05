<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");

if(isset($_GET["po_id"]))

{
    $poId = $_GET["po_id"] ?? 0;
    $poItemsListObj = queryGet('SELECT * FROM `erp_branch_purchase_order_items` WHERE `po_id`=' . $poId, true);
    $poItemsList = $poItemsListObj["data"];

    $value = "";

    foreach($poItemsList as $poItem)
    {
        $value .= "<tr><td>" . $poItem['itemCode']."</td><td>" . $poItem['itemName'] . "</td><td>" . $poItem['qty'] . "</td><td>" . $poItem['remainingQty'] . "</td><td>".$poItem['createdAt']."</td></tr>";
    }

    echo json_encode($value);

}

?>