<?php
require_once("../../../app/v1/connection-branch-admin.php");

require_once("../../common/header.php");

require_once("../../common/navbar.php");
require_once("../../common/sidebar.php");
require_once("../../common/pagination.php");
require_once("../../../app/v1/functions/company/func-branches.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-customers-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");

require_once("../../../app/v1/functions/common/templates/template-invoice.controller-taxComponents.php");

require_once("../../../app/v1/functions/common/templates/template-sales-order-controller-taxComponents.php");
require_once("../../../app/v1/functions/common/templates/template-quotation-tax.controller.php");
require_once("../../../app/v1/functions/common/templates/template-debitnote-tax.controller.php");
require_once("../../../app/v1/functions/common/templates/template-creditnote.controller-taxComponents.php");
require_once("../../../app/v1/functions/common/templates/template-manage-payment.php");
require_once("../../../app/v1/functions/common/templates/template-collect-payment.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order-pgi-controller.php");
require_once("../../../app/v1/functions/common/templates/template-so-delivery.controller.php");
require_once("../../../app/v1/functions/common/templates/template-manage-journal.php");
// require_once("../../../app/v1/functions/common/templates/template-quotation.controller.php");


require_once("../../../app/v1/functions/common/templates/template-purchase-order-tax.php");

require_once("../../../app/v1/functions/common/templates/template-item-master-controller.php");

require_once("../../../app/v1/functions/branch/func-branch-pr-controller.php");

require_once("../../../app/v1/functions/common/templates/template-manage-pr.php");

require_once("../../../app/v1/functions/branch/func-brunch-po-controller.php");

?>
<style>
    nav.main-header.navbar.navbar-expand.navbar-white.navbar-light,
    aside {
        display: none;
    }

    .wrapper {
        min-height: auto !important;
    }

    @media print {
        .sidebar-mini.sidebar-collapse .content-wrapper {
            margin-left: 0 !important;
        }

        .page-break {
            page-break-after: always;
        }
    }

    .nav-box {
        display: none;
    }

    table.classic-view td p {
        margin: 5px 0;
        font-size: 12px;
        white-space: normal;
    }
</style>

<div class="container mt-5">
    <?php
    if (isset($_GET['invoice_id'])) {
        $_GET['type'] = $_GET['type'] ?? 'customer';
        $invoice_id = base64_decode($_GET['invoice_id']);
        $templateInvoiceControllerObj = new TemplateInvoiceController();
        if (isset($_GET['printChkbox'])) {


            if (isset($_GET['type']) && $_GET['type'] == 'company') {
                if (isset($_GET['template_id'])) {

                    $templateInvoiceControllerObj->printInvoicetax($invoice_id, $_GET['template_id'], 'printChkbox');
                } else {
                    $templateInvoiceControllerObj->printInvoicetax($invoice_id, 'printChkbox');
                }
            } elseif (isset($_GET['type']) && $_GET['type'] == 'customer') {

                if (isset($_GET['template_id'])) {
                    $templateInvoiceControllerObj->printCustomerInvoice($invoice_id, $_GET['template_id'], 'printChkbox');
                } else {
                    $templateInvoiceControllerObj->printCustomerInvoice($invoice_id, 'printChkbox');
                }
            }
        } else {


            if (isset($_GET['type']) && $_GET['type'] == 'company') {
                if (isset($_GET['template_id'])) {
                    $templateInvoiceControllerObj->printInvoicetax($invoice_id, $_GET['template_id']);
                } else {
                    $templateInvoiceControllerObj->printInvoicetax($invoice_id);
                }
            } elseif (isset($_GET['type']) && $_GET['type'] == 'customer') {

                if (isset($_GET['template_id'])) {
                    $templateInvoiceControllerObj->printCustomerInvoice($invoice_id, $_GET['template_id']);
                } else {
                    $templateInvoiceControllerObj->printCustomerInvoice($invoice_id);
                }
            }
        }
    } elseif (isset($_GET['so_id'])) {
        $so_id = base64_decode($_GET['so_id']);
        $templateSalesOrderControllerObj = new TemplateSalesOrderControllerTaxComponents();
        $templateSalesOrderControllerObj->printSalesOrder($so_id);
    } elseif (isset($_GET['pgiId'])) {
        $pgiId = base64_decode($_GET['pgiId']);
        $templateSalesOrderPgiController = new TemplateSalesOrderPgiController();
        $templateSalesOrderPgiController->printSalesOrderPgi($pgiId);
    } elseif (isset($_GET['dr_note_id'])) {
        $dr_note_id = base64_decode($_GET['dr_note_id']);
        $templatedebitnoteControllerObj = new TemplateDebitNoteTaxController();
        $templatedebitnoteControllerObj->printDebitNotes($dr_note_id);
    } elseif (isset($_GET['cr_note_id'])) {
        $cr_note_id = base64_decode($_GET['cr_note_id']);
        $templatedebitnoteControllerObj = new TemplateCreditNoteTaxController();
        $templatedebitnoteControllerObj->printCreditNoteTax($cr_note_id);
    } elseif (isset($_GET['pay_id'])) {
        $pay_id = base64_decode($_GET['pay_id']);
        $templatepayment = new TemplatePayment();
        $templatepayment->printManagePayment($pay_id);
    } elseif (isset($_GET['payment_id'])) {
        $pay_id = base64_decode($_GET['payment_id']);
        $templatepayment = new TemplateCollectPaymentController();
        $templatepayment->printcollectpayment($pay_id);
    } elseif (isset($_GET['poId'])) {
        $poId = ($_GET['poId']);
        $templatePoTaxObj = new TemplatePoControllerTax();
        $templatePoTaxObj->printPoItems($poId);
    } elseif (isset($_GET['goods'])) {
        $itemId = base64_decode($_GET['goods']);
        $tempItemObj = new TemplateItemController();
        $tempItemObj->printItemPreview($itemId);
    } elseif (isset($_GET['pr_id'])) {
        $pr_id = base64_decode($_GET['pr_id']);
        $tempPrObj = new TemplatePr();
        $tempPrObj->printManagePr($pr_id);
    } elseif (isset($_GET['proformaInv'])) {
        $proformaInv = base64_decode($_GET['proformaInv']);
        $templateSalesOrderControllerObj = new TemplateSalesOrderControllerTaxComponents();
        $templateSalesOrderControllerObj->printSalesOrderProformaTaxComponents($proformaInv);
    } elseif (isset($_GET['journalId'])) {
        $id = base64_decode($_GET['journalId']);
        $templateJournal = new TemplateJournal();
        $templateJournal->printManageJouranl($id);
    } elseif (isset($_GET['delv_id'])) {
        $delvId = base64_decode($_GET['delv_id']);
        $tempdelvObj = new TemplateSalesOrderControllerTaxComponents();
        $tempdelvObj->printDelivery($delvId);
    } elseif (isset($_GET['quotationId'])) {
        $quotationId = base64_decode($_GET['quotationId']);
        $templateQuotationControllerTaxObj = new TemplateQuotationTaxController();
        $templateQuotationControllerTaxObj->printQuotation($quotationId, $company_id, $branch_id, $location_id);
    } elseif (isset($_GET['quotation_id'])) { // quotation approval 
        $BranchSoObj = new BranchSo();
        $quotation_id = base64_decode($_GET['quotation_id']);
        $templateQuotationControllerTaxObj = new TemplateQuotationTaxController();
        $templateQuotationControllerTaxObj->printQuotation($quotation_id, $company_id, $branch_id, $location_id, true);

        $quotationDetailsObj = $BranchSoObj->getQuotations($quotation_id);
        $quotationDetails = $quotationDetailsObj['data'];
        $company_id = $quotationDetails['company_id'];
        $branch_id = $quotationDetails['branch_id'];
        $location_id = $quotationDetails['location_id'];
        $customerId = $quotationDetails['customer_id'];
        $custSql = queryGet("SELECT `trade_name` FROM `erp_customer` WHERE `customer_id`=" . $customerId . "")['data'];
        $custName = $custSql['trade_name'];
        $posting_date = $quotationDetails['posting_date'];
        $totalItems = $quotationDetails['totalItems'];
        $cgst = $quotationDetails['cgst'];
        $sgst = $quotationDetails['sgst'];
        $igst = $quotationDetails['igst'];
        $allTotalAmt = $quotationDetails['totalAmount'];
        $totalDiscount = $quotationDetails['totalCashDiscount'];
        $currentTime = date("Y-m-d H:i:s");
        $quotationNo = $quotationDetails['quotation_no'];

        // console($quotationDetails);
        $approvalStatus = $quotationDetails['approvalStatus'];

        // accept quotation 
        if (isset($_POST['acceptBtn'])) {
            $updQuot = "UPDATE `" . ERP_BRANCH_QUOTATIONS . "` SET `approvalStatus`=16 WHERE `quotation_id`=$quotation_id ";
            $quotationUpdateResp = queryUpdate($updQuot);

            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrailDelv = array();
            $auditTrail['basicDetail']['trail_type'] = 'APPROVED';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_QUOTATIONS;
            $auditTrail['basicDetail']['column_name'] = 'quotation_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $quotation_id;  // primary key
            $auditTrail['basicDetail']['party_type'] = 'customer';
            $auditTrail['basicDetail']['party_id'] = $customerId;
            $auditTrail['basicDetail']['document_number'] = $quotationNo;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Quotation Accepted By Customer';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Edit';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($updQuot);
            $auditTrail['basicDetail']['company_id'] = $company_id;
            $auditTrail['basicDetail']['branch_id'] = $branch_id;
            $auditTrail['basicDetail']['location_id'] = $location_id;
            $auditTrail['basicDetail']['updated_by'] = $custName;
            $auditTrail['basicDetail']['created_by'] = $custName;
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['Quotation Details']['Posting Date'] = formatDateWeb($posting_date);
            $auditTrail['action_data']['Quotation Details']['Total Items'] = decimalQuantityPreview($totalItems);
            $auditTrail['action_data']['Quotation Details']['Cgst'] = decimalValuePreview($cgst);
            $auditTrail['action_data']['Quotation Details']['Sgst'] = decimalValuePreview($sgst);
            $auditTrail['action_data']['Quotation Details']['Igst'] = decimalValuePreview($igst);
            $auditTrail['action_data']['Quotation Details']['Total Discount'] = decimalValuePreview($totalDiscount);
            $auditTrail['action_data']['Quotation Details']['Total Amount'] = decimalValuePreview($allTotalAmt);

            $auditTrail['action_data']['Accepted Details']['Accepted By'] = $custName;
            $auditTrail['action_data']['Accepted Details']['Accepted At'] = formatDateORDateTime($currentTime);

            $auditTrailreturn = generateAuditTrailByMail($auditTrail);
            if ($quotationUpdateResp['status'] == "success") {
                swalAlert2($quotationUpdateResp["status"], $quotationDetails['quotation_no'], 'Quotation has been accepted successfully. Thank you!');
            } else {
                swalAlert2($quotationUpdateResp["status"], 'warning', $quotationUpdateResp["message"]);
            }
        }

        // reject quotation 
        if (isset($_POST['rejectBtn'])) {
            $updQuot = "UPDATE `" . ERP_BRANCH_QUOTATIONS . "` SET `approvalStatus`=17 WHERE `quotation_id`=$quotation_id ";
            $quotationUpdateResp = queryUpdate($updQuot);

            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrailDelv = array();
            $auditTrail['basicDetail']['trail_type'] = 'REJECT';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_QUOTATIONS;
            $auditTrail['basicDetail']['column_name'] = 'quotation_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $quotation_id;  // primary key
            $auditTrail['basicDetail']['party_type'] = 'customer';
            $auditTrail['basicDetail']['party_id'] = $customerId;
            $auditTrail['basicDetail']['document_number'] = $quotationNo;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Quotation Reject By Customer';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Edit';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($updQuot);
            $auditTrail['basicDetail']['company_id'] = $company_id;
            $auditTrail['basicDetail']['branch_id'] = $branch_id;
            $auditTrail['basicDetail']['location_id'] = $location_id;
            $auditTrail['basicDetail']['updated_by'] = $custName;
            $auditTrail['basicDetail']['created_by'] = $custName;
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['Quotation Details']['Posting Date'] = formatDateWeb($posting_date);
            $auditTrail['action_data']['Quotation Details']['Total Items'] = decimalQuantityPreview($totalItems);
            $auditTrail['action_data']['Quotation Details']['Cgst'] = decimalValuePreview($cgst);
            $auditTrail['action_data']['Quotation Details']['Sgst'] = decimalValuePreview($sgst);
            $auditTrail['action_data']['Quotation Details']['Igst'] = decimalValuePreview($igst);
            $auditTrail['action_data']['Quotation Details']['Total Discount'] = decimalValuePreview($totalDiscount);
            $auditTrail['action_data']['Quotation Details']['Total Amount'] = decimalValuePreview($allTotalAmt);

            $auditTrail['action_data']['Accepted Details']['Accepted By'] = $custName;
            $auditTrail['action_data']['Accepted Details']['Accepted At'] = formatDateORDateTime($currentTime);

            $auditTrailreturn = generateAuditTrailByMail($auditTrail);

            if ($quotationUpdateResp['status'] == "success") {
                swalAlert2($quotationUpdateResp["status"], $quotationDetails['quotation_no'], 'Quotation has been rejected successfully.');
            } else {
                swalAlert2($quotationUpdateResp["status"], 'warning', $quotationUpdateResp["message"]);
            }
        }
    ?>
        <form action="" method="POST">
            <div class="row btns-group">
                <?php if ($approvalStatus == 16 || $approvalStatus == 17) {
                    if ($approvalStatus == 16) { ?>
                        <div class="col-2 text-left">
                            <button type="button" onclick="return alert('You are already submitted.')" class="btn btn-success mt-4">Accepted <i class="fa fa-check-circle"></i></button>
                        </div>
                    <?php } else { ?>
                        <div class="col-2 text-left">
                            <button type="button" onclick="return alert('You are already rejected.')" class="btn btn-danger mt-4">
                                Rejected <i class="fa fa-times-circle"></i>
                            </button>
                        </div>
                    <?php }
                    ?>

                <?php } else { ?>
                    <div class="col-2">
                        <button type="submit" name="acceptBtn" class="btn btn-success mt-4 acceptBtn" onclick="return confirm('Are you sure you want to accept Quotation <?= $quotationDetails['quotation_no'] ?> ?')">Accept</button>
                    </div>
                    <div class="col-2 text-left">
                        <button type="submit" name="rejectBtn" class="btn btn-danger mt-4 cancelBtn" onclick="return confirm('Are you sure you want to reject Quotation <?= $quotationDetails['quotation_no'] ?> ?')">Reject</button>
                    </div>
                <?php } ?>
            </div>
        </form>
    <?php
    }
    ?>
</div>


<?php
include("../../common/footer.php");
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<?php
if (!isset($_GET['quotation_id'])) {
?>
    <script>
        $(document).ready(function() {
            window.print();
        })
    </script>
<?php } ?>