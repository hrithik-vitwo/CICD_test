<?php

require_once("../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');

if ($_GET['act'] === "deletePartyOrder") {
  $partyorderval = $_GET['partyOrderVal'];
  $upd = "UPDATE erp_party_order set status = 'deleted' WHERE order_code='".$partyorderval."';";
  $updateObj = queryUpdate($upd);
  echo json_encode($updateObj);

}