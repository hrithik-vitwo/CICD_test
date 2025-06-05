<?php
include("app/v1/config.php");

if (isset($_GET['invId']) && isset($_GET['mailstatus']) && $_GET['invId'] != "" && $_GET['mailstatus'] != "") {
    $invId = $_GET['invId'];
    $mailStatus = $_GET['mailstatus'];
    $sql = "UPDATE `erp_branch_sales_order_invoices`
                SET
                    `mailStatus`='$mailStatus' WHERE `so_invoice_id`='$invId'
    ";
    if ($dbCon->query($sql)) {
        $ins = "INSERT INTO `erp_invoice_mail_log` 
                    SET
                        `company_id`='1',
                        `branch_id`='1',
                        `location_id`='1',
                        `so_invoice_id`='$invId',
                        `mailStatus`='$mailStatus',
                        `created_by`='1',
                        `updated_by`='1' 
        ";
        $dbCon->query($ins);
    }
}
