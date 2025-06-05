<?php
    require_once("config.php");
    require_once("tables.php");
    require_once("lib/validator/autoload.php");
    require_once("functions/common/func-common.php");
    require_once("functions/vendor/func-vendor-administrator.php");
    require_once("functions/vendor/func-vendor-admin.php");

    if(isset($_SESSION["logedVendorAdminInfo"])){
        $vendor_id = $_SESSION["logedVendorAdminInfo"]["fldAdminVendorId"]??'';
        $company_id = $_SESSION["logedVendorAdminInfo"]["fldAdminCompanyId"]??'';
        $created_by=$_SESSION["logedVendorAdminInfo"]["adminId"].'|'.$_SESSION["logedVendorAdminInfo"]["adminType"];
        $updated_by=$_SESSION["logedVendorAdminInfo"]["adminId"].'|'.$_SESSION["logedVendorAdminInfo"]["adminType"];
    }
?>