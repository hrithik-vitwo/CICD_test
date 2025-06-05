<?php
    require_once("config.php");
    require_once("tables.php");
    require_once("lib/validator/autoload.php");
    require_once("functions/common/func-common.php");
    require_once("functions/service/func-administrator.php");
    //require_once("functions/admin/func-admin.php");

    if(isset($_SESSION["logedAdminInfo"])){
        $current_userName=$_SESSION["logedAdminInfo"]["adminName"];
        $created_by=$_SESSION["logedAdminInfo"]["adminId"].'|'.$_SESSION["logedAdminInfo"]["adminType"];
        $updated_by=$_SESSION["logedAdminInfo"]["adminId"].'|'.$_SESSION["logedAdminInfo"]["adminType"];
    }
