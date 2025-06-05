<?php
    require_once("config.php");
    require_once("tables.php");
    require_once("lib/validator/autoload.php");
    require_once("functions/common/func-common.php");
    require_once("functions/customer/func-customer-administrator.php");
    require_once("functions/customer/func-customer-admin.php");

    if(isset($_SESSION["logedCustomerAdminInfo"])){
        $company_id = $_SESSION["logedCustomerAdminInfo"]["company_id"]??'';
        $customer_id = $_SESSION["logedCustomerAdminInfo"]["customer_id"]??'';
        $created_by=$_SESSION["logedCustomerAdminInfo"]["adminId"].'|'.$_SESSION["logedCustomerAdminInfo"]["adminType"];
        $updated_by=$_SESSION["logedCustomerAdminInfo"]["adminId"].'|'.$_SESSION["logedCustomerAdminInfo"]["adminType"];
    }
?>