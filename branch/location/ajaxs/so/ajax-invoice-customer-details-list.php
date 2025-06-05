<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../app/v1/functions/branch/func-customers-controller.php");
require_once("../../../../app/v1/functions/common/templates/template-invoice.controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$customerSelect = $_POST['customerSelect'];

$BranchSoObj = new BranchSo();
$customerDetailsObj = new CustomersController();
$templateInvoiceControllerObj = new TemplateInvoiceController();

$company = $BranchSoObj->fetchCompanyDetails()['data'];
$currencyDetails = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data'];
$currencyIcon = $currencyDetails['currency_icon'];
$currencyName = $currencyDetails['currency_name'];

$fetchInvoiceByCustomer = "";

if (isset($_POST['paymentDueUrl']) && $_POST['paymentDueUrl'] == "?payment-due") {
    $paymentDueUrl = $_POST['paymentDueUrl'];
    $fetchInvoiceByCustomer = $BranchSoObj->fetchBranchSoInvoiceBycustomerIdForManageInvoiceDue($customerSelect)['data'];
} else {
    $fetchInvoiceByCustomer = $BranchSoObj->fetchBranchSoInvoiceBycustomerIdForManageInvoice($customerSelect)['data'];
}
$fetchAdvanceAmt = $BranchSoObj->fetchAdvanceAmt($customerSelect)['data']['totalAdvanceAmt'];

$fetchInvoiceAmtDetails = $BranchSoObj->totalInvoiceAmountDetailsByCustomer($customerSelect)['data'];

if ($fetchInvoiceByCustomer != NULL) {
?>

    <input type="hidden" value="<?= $fetchInvoiceAmtDetails['total_outstanding_amount'] ?? 0 ?>" class="total_outstanding_amount">
    <input type="hidden" value="<?= $fetchInvoiceAmtDetails['total_due_amount'] ?>" class="total_due_amount">
    <input type="hidden" value="<?= $fetchInvoiceAmtDetails['total_overdue_amount'] ?>" class="total_overdue_amount">

    <div class="card-body">
        <div class="row">
            <div class="col col-1" style="width: 3%;">
                <input type="checkbox" class="mt-1 invoiceCheckboxAll">
            </div>
            <div class="col col-1" style="width: 5%;">SL. No.</div>
            <div class="col">Icon</div>
            <div class="col">Invoice No.</div>
            <div class="col text-right">Invoice Amount</div>
            <div class="col">Invoice Date</div>
            <div class="col">Due in (day/s)</div>
            <div class="col">Status</div>
            <div class="col">Action</div>
            <div class="col">E-Invoice</div>
        </div>
        <hr />

        <?php
        $mobileView = '';
        $increment = 1;
        $cnt = 1;
        foreach ($fetchInvoiceByCustomer as $invoiceKey => $oneSoList) {

            $customerDtls = $customerDetailsObj->getDataCustomerDetails($oneSoList['customer_id'])['data'][0];
            $customerPic = $customerDtls['customer_picture'];
            $customerName = $customerDtls['trade_name'];
            $customerPicture = '';
            $customer_name = mb_substr($customerName, 0, 1);

            ($customerPic != '') ? ($customerPicture = '<img src="' . BASE_URL . 'public/storage/avatar/' . $customerPic . '" class="img-fluid avatar rounded-circle" alt="">') : ($customerPicture = '<div class="img-fluid avatar rounded-circle d-flex justify-content-center align-items-center" style="border: 1px solid grey;">' . $customer_name . '</div>');

            $temDueDate = date_create($oneSoList["invoice_date"]);
            $dateInShow = date_add($temDueDate, date_interval_create_from_date_string($oneSoList["credit_period"] . " days"));
            $todayDate = new DateTime(date("Y-m-d"));
            $oneInvDueDays = $todayDate->diff(new DateTime(date_format($temDueDate, "Y-m-d")))->format("%r%a");
            $dueInDaysClass = ($oneInvDueDays >= 0) ? (($oneInvDueDays == 0) ? "status-info" : "status") : "status-danger";

            $oneInvDueDays = ($oneInvDueDays >= 0) ? (($oneInvDueDays >= 1) ? (($oneInvDueDays == 1) ? "Due in 1 day" : "Due in " . $oneInvDueDays . " days") : "Due Today") : (($oneInvDueDays == -1) ? "Overdue by 1 day" : "Overdue by " . abs($oneInvDueDays) . " days");

            if ($oneSoList['totalItems'] == 1) {
                $label = "Item";
            } elseif ($oneSoList['totalItems'] > 1) {
                $label = "Items";
            }

            // console($oneSoList);
        ?>
            <div class="row">
                <div class="col col-1" style="width: 3%;">
                    <input type="checkbox" class="mt-1 invoiceCheckbox" id="invoiceCheckbox||<?= $invoiceKey ?>||<?= $oneSoList['customer_id'] ?>||<?= $oneSoList['invoice_no'] ?>" value="<?= $invoiceKey ?>||<?= $oneSoList['customer_id'] ?>||<?= $oneSoList['invoice_no'] ?>">
                </div>
                <div class="col col-1 text-xs" style="width: 5%;"><?= $cnt++ ?></div>
                <!-- <div class="col icon-mobile" style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo1_<?= $oneSoList['so_invoice_id'] ?>"> -->
                <div class="col icon-mobile">
                    <?= $customerPicture ?>
                    <p class="company-name mt-1"><?= $customerDtls['trade_name'] ?></p>
                </div>
                <div class="col invoice-num-mobile"><?= $oneSoList['invoice_no'] ?>
                    <p class="item-count mt-1">[<?= $oneSoList['totalItems'] ?> <?= $label ?>]</p>
                </div>
                <div class="col amount-invoice-mobile"><span class="rupee-symbol"><?= $currencyName ?></span><?= $oneSoList['all_total_amt'] ?></div>
                <div class="col delivery-date-mobile"><?= $oneSoList['invoice_date'] ?></div>
                <div class="col delivery-date-mobile duedateCls">
                    <?php
                    if ($oneSoList['status'] == 'reverse') {
                        echo '--';
                    } else {
                        if ($oneSoList['invoiceStatus'] != 4) { ?>
                            <p class="<?= $dueInDaysClass ?> text-xs text-center"><?= $oneInvDueDays ?></p>
                        <?php } else { ?>
                            <p class="status-light text-xs text-center"><i class="fa fa-check-circle"></i> Received</p>
                    <?php }
                    } ?>
                </div>
                <div class="col status-mobile listStatus">
                    <?php if ($oneSoList['status'] == 'reverse') {
                        echo 'Reversed';
                    } else { ?>
                        <div class="status-custom text-xs text-center text-secondary listStatus">
                            <?php if ($oneSoList['mailStatus'] == 1) {
                                echo 'SENT <div class="round">
                        <ion-icon name="checkmark-sharp"></ion-icon>
                      </div>';
                            } elseif ($oneSoList['mailStatus'] == 2) {
                                echo '<span class="text-primary">VIEW</span> <div class="round text-primary">
                        <ion-icon name="checkmark-done-sharp"></ion-icon>
                      </div>';
                            } ?>

                            <!-- <div class="round">
                    <ion-icon name="checkmark-done-sharp"></ion-icon>bgghjhghjghjghjghjghjghj
                    </div> -->
                            <p class="status-date"><?= $oneSoList['updated_at'] ?></p>
                        </div>
                    <?php } ?>
                </div>
                <div class="col action-mobile">
                    <button type="button" class="btn text-secondary" style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo1_<?= $oneSoList['so_invoice_id'] ?>"><i class="fa fa-eye po-list-icon"></i></button>
                    <!-- <div class="dropdown">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                      &#xFE19;
                    </a>
                    <ul class="dropdown-menu border-0 w-50" aria-labelledby="dropdownMenuLink">
                      <li><a class="text-sm" style="cursor:pointer; text-decoration: none;" data-toggle="modal" data-target="#fluidModalRightSuccessDemo1_<?= $oneSoList['so_invoice_id'] ?>">View</a></li>
                    </ul>
                  </div> -->
                    <?php if ($oneSoList['status'] == 'active') { ?>
                        <a style="cursor:pointer" data-id="<?= $oneSoList['so_invoice_id']; ?>" class="btn btn-sm reverseInvoice" title="Reverse Now">
                            <i class="far fa-undo po-list-icon"></i>
                        </a>
                    <?php } ?>
                </div>
                <div class="col einvoiceCls">
                    <?php
                    if ($oneSoList['status'] == 'reverse') {
                        echo '--';
                    } else {
                        if ($oneSoList["ack_no"] == "") {
                    ?>
                            <a class="btn btn-sm btn-primary generateEInvoice" id="generateEInvoice_<?= $oneSoList['so_invoice_id'] ?>">Generate</a>
                        <?php
                        } else {
                        ?>
                            <a class="btn btn-sm btn-success">Generated</a>
                    <?php
                        }
                    }
                    ?>

                </div>
            </div>
            <hr />
            <!-- manage internal modal start🎈🎈🎈🎈🎈🎈🎈 -->

            <!-- right modal start here  -->
            <div class="modal fade right invoice-modal customer-modal classic-view-modal" id="fluidModalRightSuccessDemo1_<?= $oneSoList['so_invoice_id'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                    <!--Content-->
                    <div class="modal-content">
                        <!--Header-->
                        <div class="modal-header">
                            <p class="heading lead"><?= $oneSoList['invoice_no'] ?></p>
                            <ul class="nav nav-tabs">
                                <li class="nav-item"><a class="nav-link active" href="#preview<?= str_replace('/', '-', $oneSoList['invoice_no']) ?>" data-bs-toggle="tab">Preview</a></li>
                                <!-- -------------------Audit History Button Start------------------------- -->
                                <li class="nav-item">
                                    <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $oneSoList['invoice_no']) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $oneSoList['invoice_no']) ?>" href="#history<?= str_replace('/', '-', $oneSoList['invoice_no']) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $oneSoList['invoice_no']) ?>" aria-selected="false">Trail</a>
                                </li>
                                <!-- -------------------Audit History Button End------------------------- -->
                            </ul>
                        </div>
                        <!--Body-->
                        <div class="modal-body">
                            <?php
                            $invoiceDetails = $BranchSoObj->fetchBranchSoInvoiceById($oneSoList['so_invoice_id'])['data'][0];
                            $invoiceItemDetails = $BranchSoObj->fetchBranchSoInvoiceItems($oneSoList['so_invoice_id'])['data'];
                            $customerDetails = $BranchSoObj->fetchCustomerDetails($invoiceDetails['customer_id'])['data'][0];
                            $customerAddressDetails = $BranchSoObj->fetchCustomerAddressDetails($customerDetails['customer_id'])['data'];
                            $companyData = unserialize($invoiceDetails['companyDetails']);
                            $customerData = unserialize($invoiceDetails['customerDetails']);
                            $encodeInvId = base64_encode($oneSoList['so_invoice_id']);

                            $conversion_rate = 1;
                            $conversion_currency_name = $invoiceDetails['currency_name'] ?? "";
                            if ($invoiceDetails['conversion_rate'] != "") {
                                $conversion_rate = $invoiceDetails['conversion_rate'];
                            } else {
                                $conversion_rate = 1;
                            }

                            $company_bank_details = unserialize($invoiceDetails['company_bank_details']);

                            $invoiceItemDetailsGroupByHSN = $BranchSoObj->fetchBranchSoInvoiceItemsGroupByHSN($oneSoList['so_invoice_id'])['data'];
                            ?>
                            <!-- ************************************** -->
                            <div style="display: flex; justify-content: space-between">
                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                    <li class="nav-item handleCompanyCopyTemplate" id="handleCompanyCopyTemplate_<?= $oneSoList['so_invoice_id'] ?>">
                                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home<?= $oneSoList['so_invoice_id'] ?>" role="tab" aria-controls="pills-home" aria-selected="true">Company <sup><small>(<?= $currencyName ?>)</small></sup></a>
                                    </li>
                                    <?php if ($oneSoList['currency_name'] != $currencyName) { ?>
                                        <li class="nav-item ml-2 handleCustomerCopyTemplate" id="handleCustomerCopyTemplate_<?= $oneSoList['so_invoice_id'] ?>">
                                            <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile<?= $oneSoList['so_invoice_id'] ?>" role="tab" aria-controls="pills-profile" aria-selected="false">Customer <sup><small>(<?= $oneSoList['currency_name'] ?>)</small></sup></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                                <div>
                                    <select title="Select Template" class="form-control handleTemplates" id="handleTemplates_<?= $oneSoList['so_invoice_id'] ?>">
                                        <option value="0">Default</option>
                                        <option value="1">Standard</option>
                                    </select>
                                    <input type="hidden" class="handleTemplateId" id="handleTemplateId_<?= $oneSoList['so_invoice_id'] ?>">
                                    <input type="hidden" class="handleInvoiceType" id="handleInvoiceType_<?= $oneSoList['so_invoice_id'] ?>">
                                </div>
                            </div>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-home<?= $oneSoList['so_invoice_id'] ?>" role="tabpanel" aria-labelledby="pills-home-tab">
                                    <div style="display: flex;justify-content: space-between;">
                                        <p>Company Copy</p>
                                        <span class="handlePrintBtn" id="handleCompanyPrintBtn_<?= $oneSoList['so_invoice_id'] ?>">
                                            <a href="classic-view/invoice-preview-print.php?invoice_id=<?= base64_encode($oneSoList['so_invoice_id']) ?>&type=company" class="btn btn-primary classic-view-btn float-right" target="_blank">Print</a>
                                        </span>
                                    </div>
                                    <?php $templateInvoiceControllerObj->printInvoice($oneSoList['so_invoice_id']); ?>
                                </div>
                                <div class="tab-pane fade" id="pills-profile<?= $oneSoList['so_invoice_id'] ?>" role="tabpanel" aria-labelledby="pills-profile-tab">
                                    <div style="display: flex;justify-content: space-between;">
                                        <p>Customer Copy</p>
                                        <!-- <a href="classic-view/invoice-preview-print.php?invoice_id=<?= base64_encode($oneSoList['so_invoice_id']) ?>&type=customer" class="btn btn-primary classic-view-btn float-right" target="_blank">Print</a> -->
                                        <span class="handlePrintBtn" id="handleCustomerPrintBtn_<?= $oneSoList['so_invoice_id'] ?>">
                                            <a href="classic-view/invoice-preview-print.php?invoice_id=<?= base64_encode($oneSoList['so_invoice_id']) ?>&type=company" class="btn btn-primary classic-view-btn float-right" target="_blank">Print</a>
                                        </span>
                                    </div>
                                    <?php $templateInvoiceControllerObj->printCustomerInvoice($oneSoList['so_invoice_id'], 1); ?>
                                </div>
                            </div>
                            <!-- **************************************** -->
                            <div class="tab-content" id="myTabContent">
                                <!-- <div class="tab-pane show active" id="preview<?= str_replace('/', '-', $oneSoList['invoice_no']) ?>"> -->
                                <!-- Nav pills -->
                                <!-- <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link active template" id="template_<?= $oneSoList['so_invoice_id'] ?>" data-classic="0">Default Template</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link template" id="template_<?= $oneSoList['so_invoice_id'] ?>" data-classic="1">Classic Template</a>
                                                        </li>
                                                    </ul> -->
                                <!-- <a href="classic-view/invoice-preview-print.php?invoice_id=<?= base64_encode($oneSoList['so_invoice_id']) ?>" class="btn btn-primary classic-view-btn float-right" target="_blank">Print</a>
                                                    
                                                    <div class="tab-content" id="pills-tabContent">
                                                        <div class="tab-pane fade show active invoiceTemplate" id="invoiceTemplate_<?= $oneSoList['so_invoice_id'] ?>" role="tabpanel">
                                                            <?php // $templateInvoiceControllerObj->printInvoice($oneSoList['so_invoice_id']); 
                                                            ?>
                                                        </div>
                                                    </div> -->
                                <!-- </div> -->
                                <!-- -------------------Audit History Tab Body Start------------------------- -->
                                <!-- <div class="tab-pane fade" id="history<?= str_replace('/', '-', $oneSoList['invoice_no']) ?>" role="tabpanel" aria-labelledby="history-tab">

                                                    <div class="audit-head-section mb-3 mt-3 ">
                                                        <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($oneSoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['created_at']) ?></p>
                                                        <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($oneSoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['updated_at']) ?></p>
                                                    </div>
                                                    <hr>
                                                    <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $oneSoList['invoice_no']) ?>">

                                                        <ol class="timeline">

                                                            <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                <div class="new-comment font-bold">
                                                                    <p>Loading...
                                                                    <ul class="ml-3 pl-0">
                                                                        <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                    </ul>
                                                                    </p>
                                                                </div>
                                                            </li>
                                                            <p class="mt-0 mb-5 ml-5">Loading...</p>

                                                            <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                <div class="new-comment font-bold">
                                                                    <p>Loading...
                                                                    <ul class="ml-3 pl-0">
                                                                        <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                    </ul>
                                                                    </p>
                                                                </div>
                                                            </li>
                                                            <p class="mt-0 mb-5 ml-5">Loading...</p>
                                                        </ol>
                                                    </div>
                                                </div> -->
                                <!-- -------------------Audit History Tab Body End------------------------- -->
                            </div>
                        </div>
                    </div>
                    <!--/.Content-->
                </div>
            </div>
            <!-- right modal end here  -->

            <!-- mobile view area -->

            <?php
            $mailStatus = '';
            if ($oneSoList['mailStatus'] == 1) {
                $mailStatus = "SENT";
            } elseif ($oneSoList['mailStatus'] == 2) {
                $mailStatus = "VIEW";
            }
            $invDate = date_create($oneSoList['invoice_date']);
            $invoiceDate = date_format($invDate, "F d,Y");
            $poDate = date_create($oneSoList['po_date']);
            $echoPoDate = date_format($poDate, "F d,Y");

            $mobileView .= '<div class="row mb-2 mt-2">
                <div class="col col-3">
                <div class="row mb-0">
                    <div class="col col-12 icon-image sm-icon">
                    ' . $customerPicture . '
                    </div>
                </div>
                <div class="row mb-0">
                    <div class="col col-12 text-center text-xs sm-customer">
                    ' . $oneSoList['customer_name'] . '
                    </div>
                </div>
                </div>

                <div class="col-5">
                <div class="row mb-0">
                    <div class="col col-12 text-xs sm-inv-num">
                    ' . $oneSoList['invoice_no'] . '
                    <p class="item-count mt-1 text-xs">[' . $oneSoList['totalItems'] . ' item/s]</p>
                    </div>

                    <div class="col col-12 text-lg sm-total-amnt">
                    ' . $oneSoList['all_total_amt'] . '
                    </div>
                    <div class="col col-12 text-xs">
                    <p class="' . $dueInDaysClass . ' text-xs w-100 text-center">' . $oneInvDueDays . '</p>
                    </div>
                </div>
                </div>
                <div class="col-3">
                <div class="row mb-0">
                    <div class="col col-12">
                    <div class="status-custom text-xs w-75 text-secondary">' . $mailStatus . '
                        <div class="round">
                        <ion-icon name="checkmark-sharp"></ion-icon>
                        </div>
                    </div>
                    <p class="status-date">12 Dec, 22</p>
                    </div>
                    <!--
                    <div class="col col-12">
                    <div class="status-custom text-xs w-100 text-primary">viewed
                        <div class="round">
                        <ion-icon name="checkmark-done-sharp"></ion-icon>
                        </div>
                    </div>
                    <p class="status-date">12 Dec, 22</p>
                    </div>
                    -->
                </div>
                </div>
                <div class="col-1">
                <div class="dropdown">
                    <a class="dropdown-toggle text-lg" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                    &#xFE19;
                    </a>

                    <ul class="dropdown-menu border-0 w-50 text-center" aria-labelledby="dropdownMenuLink">
                    <li><a href="' . BASE_URL . 'branch/location/branch-so-invoice-view.php?inv_id=' . $encodeInvId . '" class="text-sm" style="cursor:pointer; text-decoration: none;">View</a></li>
                    </ul>
                </div>
                </div>
                </div>
                <hr class="m-3">
                
                
                <!-- right modal start here  -->
                <div class="modal fade right" id="fluidModalRightSuccessDemo2_' . $oneSoList['so_invoice_id'] . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                    <div style="max-width: 70%; min-width:50%" class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                    <!--Content-->
                    <div class="modal-content">
                        <!--Header-->
                        <div class="modal-header " style="background: none; border:none; color:#424242">
                        <p class="heading lead">' . $oneSoList['invoice_no'] . '</p>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="white-text">×</span>
                        </button>
                        </div>
                        <!--Body-->
                        <div class="modal-body" style="padding: 0;">
                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a class="nav-link active" href="#preview' . $oneSoList['so_invoice_id'] . '" data-bs-toggle="tab">Preview</a></li>
                            <li class="nav-item"><a class="nav-link" href="#otherDetails' . $oneSoList['so_invoice_id'] . '" data-bs-toggle="tab">Other Details</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="col-md-12">
                            <div class="shadow-sm bg-light py-2 mx-2 my-2" id="action-navbar" style="text-align:right">
                                <form action="" method="POST">
                                <!-- <a href="branch-so-invoice-2.php?invoice-no=' . base64_encode($oneSoList['so_invoice_id']) . '" name="vendorEditBtn">
                                            <span class="text-info font-weight-bold shadow-sm px-2">INVOICE</span>
                                            </a> -->
                                <a href="#" name="vendorEditBtn">
                                    <i title="Edit" style="font-size: 1.2em" class="fa fa-edit text-success mx-3"></i>
                                </a>
                                <i title="Delete" style="font-size: 1.2em" class="fa fa-trash text-danger mx-3"></i>
                                <i title="Toggle" style="font-size: 1.2em" class="fa fa-toggle-on text-primary mx-3"></i>
                                </form>
                            </div>
                            </div>
                            <div class="tab-pane show active" id="preview' . $oneSoList['so_invoice_id'] . '">


                            <!-- ################################## -->
                            <div class="container my-3">
                                <div class="row p-0 m-0 pb-2" style="border-bottom: 3px solid #0090ff;">
                                <div class="col-6 d-flex align-items-center">
                                    <img width="220" src="../../public/storage/logo/' . $oneSoList['company_logo'] . '" alt="">
                                </div>
                                <div class="col-6 d-flex align-items-end flex-column">
                                    <div>Original for Recipient</div>
                                    <div>
                                    <strong class="textColor">' . $oneSoList['invoice_no'] . '</strong>
                                    </div>
                                    <div>
                                    <b>Date </b>
                                    <span>' . $invoiceDate . '</span> </span>
                                    </div>
                                    <div>
                                    <b>Due Date </b>
                                    <span>' . $oneSoList['credit_period'] . '</span> </span>
                                    </div>
                                    <div>
                                    <b>P.O. Number </b>
                                    <span>' . $oneSoList['po_number'] . '</span> </span>
                                    </div>
                                    <div>
                                    <b>P.O. Date </b>
                                    <span>' . $echoPoDate . '</span> </span>
                                    </div>
                                </div>
                                </div>
                                <div class="row p-0 m-0 py-3" style="border-bottom: 3px solid #0090ff;">
                                <div class="col-6">
                                    <!-- <div>
                                                <strong class="ml-1 textColor">Sorina TEST 123</strong>
                                            </div> -->
                                    <div>
                                    <i class="textColor fa fa-briefcase"></i>
                                    <span>' . $oneSoList['company_gstin'] . '</span>
                                    </div>
                                    <div>
                                    <i class="textColor fa fa-phone"></i>
                                    <span>7059746613</span>
                                    </div>
                                    <div>
                                    <i class="textColor fa fa-envelope"></i>
                                    <span>imranali59059@gmail.com</span>
                                    </div>
                                    <div>
                                    <i class="textColor fa fa-globe"></i>
                                    <span>www.imranali59059.com</span>
                                    </div>
                                    <div>
                                    <i class="textColor fa fa-info"></i>
                                    <span>
                                        ' . $oneSoList['company_address'] . '
                                    </span>
                                    </div>
                                </div>
                                <!-- <div class="col-4 d-flex align-items-end flex-column">
                                            </div> -->
                                <div class="col-6 d-flex align-items-end flex-column">
                                    <div>
                                    <strong class="ml-1 textColor">' . $oneSoList['customer_name'] . '</strong>
                                    </div>
                                    <div>
                                    <strong class="ml-1 textColor">' . $oneSoList['customer_gstin'] . '</strong>
                                    </div>
                                    <div>
                                    <i class="textColor fa fa-phone"></i>
                                    <span>' . $oneSoList['customer_phone'] . '</span>
                                    </div>
                                    <div>
                                    <i class="textColor fa fa-envelope"></i>
                                    <span>' . $oneSoList['customer_email'] . '</span>
                                    </div>
                                    <div>
                                    <i class="textColor fa fa-info"></i>
                                    <span>' . $oneSoList['customer_address'] . '</span>
                                    </div>
                                </div>

                                </div>
                                <div class="row p-0 m-0">
                                <div class="col-md-12" style="overflow: auto;">
                                    <div class="row">
                                    <div class="col-6">
                                        <div class="row">
                                        <div class="col-1 font-weight-bold bg-light">NO</div>
                                        <div class="col-5 font-weight-bold">PRODUCT NAME</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="row">
                                        <div class="col-3 font-weight-bold bg-light">HSN CODE</div>
                                        <div class="col-3 font-weight-bold">QTY</div>
                                        <div class="col-3 font-weight-bold bg-light">UNIT PRICE</div>
                                        <div class="col-3 font-weight-bold text-right">AMOUNT</div>
                                        </div>
                                    </div>
                                    </div>
                                    <!-- list items here -->
                                    <?php
                                    $i = 1;
                                    foreach ($invoiceItemDetails as $item) {
                                    ?>
                                    <div class="row py-2">
                                        <div class="col-6">
                                        <div class="row">
                                            <div class="col-1 font-weight-bold bg-light"><?= $i++; ?></div>
                                            <div class="col-11">
                                            <strong>' . $item['itemName'] . '</strong>
                                            <div><small>' . $item['itemDesc'] . '</small></div>
                                            </div>
                                        </div>
                                        </div>
                                        <div class="col-6">
                                        <div class="row">
                                            <div class="col-3 font-weight-bold bg-light">' . $item['hsnCode'] . '</div>
                                            <div class="col-3">' . $item['qty'] . '/' . $item['uom'] . '</div>
                                            <div class="col-3 font-weight-bold bg-light">' . $item['unitPrice'] . '</div>
                                            <div class="col-3 text-right">' . $item['totalPrice'] . '</div>
                                        </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <!-- list items here -->
                                </div>
                                </div>

                                <div class="row p-0 m-0">
                                <div class="col-8">
                                    <!-- <div>Total: Twenty Seven Thousand Four Hundred Tinety Rupees Only</div>
                                            <div><a href="#">Pay Now with PayPal </a></div> -->
                                    <div>
                                    <strong class="textColor">AUTHORIZED SIGNATORY</strong>
                                    </div>
                                    <img width="160" src="../../public/storage/' . $oneSoList['company_signature'] . '" alt="">
                                </div>
                                <div class="col-2 d-flex align-items-end flex-column textColor">
                                    <div>SUB TOTAL</div>
                                    <div>TOTAL TAX</div>
                                    <div>TOTAL DISCOUNT</div>
                                    <div>TOTAL AMOUNT</div>
                                </div>
                                <div class="col-2 d-flex align-items-end flex-column textColor">
                                    <div class="">' . $oneSoList['sub_total_amt'] . '</div>
                                    <div class="">' . $oneSoList['total_tax_amt'] . '</div>
                                    <div class="">' . $oneSoList['totalDiscount'] . '</div>
                                    <div class="">' . $oneSoList['all_total_amt'] . '</div>
                                </div>
                                <div class="col-12">
                                    <strong class="textColor">NOTE:</strong>
                                    <div class="text">' . $oneSoList['company_footer'] . '</div>
                                </div>
                                </div>
                            </div>
                            <!-- ################################## -->
                            </div>
                            <div class="tab-pane" id="otherDetails' . $oneSoList['so_invoice_id'] . '">
                            <div class="card p-5">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi ipsum ex soluta natus consequuntur voluptatem sed voluptate eum nulla. Molestias harum maxime ipsa? Error, ullam fugit possimus qa perspiciatis fugiat nisi dolore neque praesentium, quidem necessitatibus totam in explicabo, autem, nulla eum. Culpa, magni!
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    <!--/.Content-->
                    </div>
                </div>
                <!-- right modal end here  -->
            ';
            ?>

            <!-- manage internal modal end🎈🎈🎈🎈🎈🎈🎈 -->
        <?php
        }
        ?>
        <!-- End Pagination from------->
        <?php
        if ($count > 0 && $count > $GLOBALS['show']) {
        ?>
            <div class="pagination align-right">
                <?php pagination($count, "frm_opts"); ?>
            </div>

            <!-- End .pagination -->

        <?php  } ?>
    </div>
<?php } else { ?>
    <div id="noTinvoiceFound" class="text-center py-2 bg-white">
        <img src="../../public/assets/gif/no-transaction.gif" width="150" alt="">
        <p>No Invoice Found</p>
    </div>
<?php } ?>