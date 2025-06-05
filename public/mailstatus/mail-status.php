<?php
require_once("../../app/v1/config.php");
require_once("../../app/v1/functions/common/func-common.php");

if (isset($_GET['mail_id']) && isset($_GET['mailstatus']) && $_GET['mail_id'] != "" && $_GET['mailstatus'] != "") {
     $mail_id = $_GET['mail_id'];
    $mailStatus = $_GET['mailstatus'];
    
     $sql = "UPDATE `erp_globalmail_log`
                SET
                    `mailStatus`='$mailStatus' WHERE `mail_id`='$mail_id'";

    if ($dbCon->query($sql)) {
         $ins = "INSERT INTO `".ERP_INVOICE_MAIL_LOG."` 
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
