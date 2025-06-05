<?php

include_once("../../../../../app/v1/functions/company/func-ChartOfAccounts.php");
include_once("../../../../../app/v1/functions/branch/func-grn-controller.php");
include_once("../../../../../app/v1/connection-branch-admin.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET['type'] == 'grn') {
        require_once("../../../components/grn/grn-view-new.php");
    } else {
        require_once("../../../components/grn/srn-view-new.php");
    }
}
