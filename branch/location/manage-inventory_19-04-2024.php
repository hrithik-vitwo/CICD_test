<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../common/footer.php");

require_once("../../app/v1/functions/branch/func-inventory-controller.php");
require_once("../../app/v1/functions/branch/func-goods-controller.php");



if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST,  $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
  }
  
$goodsController = new GoodsController();
if (isset($_POST['createData'])) {

    //console($_POST);
    // exit();
    $addNewObj = $goodsController->transfer_stock($_POST);

    // console($addNewObj);
    // exit();

    swalAlert($addNewObj["status"], ucfirst($addNewObj["status"]), $addNewObj["message"], BASE_URL . "branch/location/manage-inventory.php");
}

?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<?php


require_once("components/inventory/inventory-h.php");

require_once("../common/footer.php");
?>