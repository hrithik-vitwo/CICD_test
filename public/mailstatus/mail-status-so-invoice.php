<?php
include("../../app/v1/connection-branch-admin.php");

if (isset($_GET['invId']) && isset($_GET['mailstatus']) && $_GET['invId'] != "" && $_GET['mailstatus'] != "") {
    $invId = $_GET['invId'];
    $mailStatus = $_GET['mailstatus'];
    $sql = "UPDATE `".ERP_BRANCH_SALES_ORDER_INVOICES."`
                SET
                    `mailStatus`='$mailStatus' WHERE `so_invoice_id`='$invId' AND `company_id`=$company_id AND `branch_id`=$branch_id AND  `location_id`='$location_id' ";


    if ($dbCon->query($sql)) {
         $ins = "INSERT INTO `".ERP_INVOICE_MAIL_LOG."` 
                    SET
                        `company_id`=$company_id,
                        `branch_id`=$branch_id,
                        `location_id`=$location_id,
                        `so_invoice_id`='$invId',
                        `mailStatus`='$mailStatus',
                        `created_by`=$created_by,
                        `updated_by`='$updated_by' 
        ";
        $dbCon->query($ins);
    }
}
