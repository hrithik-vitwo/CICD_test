<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/company/func-branches.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../app/v1/functions/branch/func-customers-controller.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/common/templates/template-invoice.controller.php");
if ($_POST['act'] == "invoiceTemplate") {
    $templateInvoiceControllerObj = new TemplateInvoiceController();
    $templateId = $_POST['templateId'];
    $invoiceId = $_POST['invoiceId'];
    $templateInvoiceControllerObj->printInvoice($invoiceId, $templateId);    
} else {
    echo "Something wrong, try again!";
}
?>