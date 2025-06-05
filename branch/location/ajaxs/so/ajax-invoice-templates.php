<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../app/v1/functions/common/templates/template-invoice.controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();
$templateInvoiceControllerObj = new TemplateInvoiceController();

if ($_POST['act'] === "handleTemplates") {
    $templateId = $_POST['templateId'];
    $invoiceId = $_POST['invoiceId'];
    $invoiceType = $_POST['invoiceType'];
?>
<?php
if($invoiceType === "company") {
    $templateInvoiceControllerObj->printInvoice($invoiceId, $templateId);
}else{
    $templateInvoiceControllerObj->printCustomerInvoice($invoiceId, $templateId);
}
?>
<?php
}
?>