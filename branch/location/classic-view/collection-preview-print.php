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
require_once("../../../app/v1/functions/common/templates/template-collection.controller.php");
require_once("../../../app/v1/functions/common/templates/template-invoice.controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("../../../app/v1/functions/common/templates/template-quotation.controller.php");
require_once("../../../app/v1/functions/common/templates/template-debitnote.controller.php");
require_once("../../../app/v1/functions/common/templates/template-creditnote.controller.php");
?>
<style>
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
</style>

<div class="container mt-5">
    <?php
    if (isset($_GET['invoice_id'])) {
       // console($_GET);
        $invoice_id = base64_decode($_GET['invoice_id']);
        $templateInvoiceControllerObj = new TemplateCollectionController();
        if (isset($_GET['type']) && $_GET['type'] == 'company') {
           // console($_GET);
            $templateInvoiceControllerObj->printInvoice($invoice_id);
        } elseif (isset($_GET['type']) && $_GET['type'] == 'customer') {
            $templateInvoiceControllerObj->printCustomerInvoice($invoice_id);
        }
    } elseif (isset($_GET['quotation_id'])) {
        $quotation_id = base64_decode($_GET['quotation_id']);
        $company_id = $_GET['company_id'];
        $branch_id = $_GET['branch_id'];
        $location_id = $_GET['location_id'];
        $templateQuotationControllerObj = new TemplateQuotationController();
        $quotationDetails = $templateQuotationControllerObj->printQuotation($quotation_id, $company_id, $branch_id, $location_id);
    } elseif (isset($_GET['so_id'])) {
        $so_id = base64_decode($_GET['so_id']);
        $templateSalesOrderControllerObj = new TemplateSalesOrderController();
        $templateSalesOrderControllerObj->printSalesOrder($so_id);
    } elseif (isset($_GET['dr_note_id'])) {
        $dr_note_id = base64_decode($_GET['dr_note_id']);
        $templatedebitnoteControllerObj = new TemplateDebitNoteController();
        $templatedebitnoteControllerObj->printDebitNotes($dr_note_id);
    
    } elseif (isset($_GET['cr_note_id'])) {
        $dr_note_id = base64_decode($_GET['cr_note_id']);
        $templatedebitnoteControllerObj = new TemplateCreditNoteController();
        $templatedebitnoteControllerObj->printCreditNotes($dr_note_id);
    }

    // quotation approval 
    if (isset($_GET['quotation_id'])) {
        $BranchSoObj = new BranchSo();
        $quotation_id = base64_decode($_GET['quotation_id']);
        $quotationDetailsObj = $BranchSoObj->getQuotations($quotation_id);
        $quotationDetails = $quotationDetailsObj['data'];

        // accept quotation 
        if (isset($_POST['acceptBtn'])) {
            $updQuot = "UPDATE `" . ERP_BRANCH_QUOTATIONS . "` SET `approvalStatus`=16 WHERE `quotation_id`=$quotation_id ";
            $quotationUpdateResp = queryUpdate($updQuot);

            if ($quotationUpdateResp['status'] == "success") {
                swalAlert($quotationUpdateResp["status"], $quotationDetails['quotation_no'], 'Quotation has been accepted successfully. Thank you!');
            } else {
                swalAlert($quotationUpdateResp["status"], 'warning', $quotationUpdateResp["message"]);
            }
        }

        // reject quotation 
        if (isset($_POST['rejectBtn'])) {
            $updQuot = "UPDATE `" . ERP_BRANCH_QUOTATIONS . "` SET `approvalStatus`=17 WHERE `quotation_id`=$quotation_id ";
            $quotationUpdateResp = queryUpdate($updQuot);

            if ($quotationUpdateResp['status'] == "success") {
                swalAlert($quotationUpdateResp["status"], $quotationDetails['quotation_no'], 'Quotation has been rejected successfully.');
            } else {
                swalAlert($quotationUpdateResp["status"], 'warning', $quotationUpdateResp["message"]);
            }
        }
        ?>
        <form action="" method="POST">
            <div class="row btns-group">
                <?php if ($quotationDetails['approvalStatus'] == 16) { ?>
                    <div class="col-2 text-left">
                        <button type="button" onclick="return alert('You are already submitted.')" class="btn btn-success mt-4 acceptBtn">Accepted <i class="fa fa-check-circle"></i></button>
                    </div>
                    <div class="col-2 text-left">
                        <button type="button" onclick="return alert('You are already submitted.')" class="btn btn-secondary mt-4 cancelBtn">Reject</button>
                    </div>
                <?php } else if ($quotationDetails['approvalStatus'] == 17) { ?>
                    <div class="col-2 text-left">
                        <button type="button" onclick="return alert('You are already submitted.')" class="btn btn-secondary mt-4 acceptBtn">Accept</button>
                    </div>
                    <div class="col-2 text-left">
                        <button type="button" onclick="return alert('You are already submitted.')" class="btn btn-danger mt-4 cancelBtn">Rejected <i class="fa fa-times-circle"></i></button>
                    </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<?php
require_once("../../common/footer.php");
?>
<script>
    $(document).ready(function(){
        window.print();
    })
</script>