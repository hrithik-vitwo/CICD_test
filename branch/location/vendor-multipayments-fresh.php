<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-brunch-po-controller.php");
require_once("../../app/v1/functions/branch/func-grn-controller.php");


$branchPoObj = new BranchPo();
$grnObj = new GrnController();
$cashAndAccountsObj = get_acc_bank_cash_accounts();

$vendorListObj = [];
if (isset($_GET['code'])) {
    $vendorIdList = json_decode(base64_decode($_GET["code"]));
    foreach ($vendorIdList as $vendorId) {
        $vendorListObj["data"][] = $grnObj->fetchVendorDetails($vendorId)['data'][0];
    }
}
if(count($vendorListObj["data"])>0){
    $vendorListObj["status"] = "success";
    $vendorListObj["message"] = "Vendor list genereted success";
}else{
    $vendorListObj["status"] = "warning";
    $vendorListObj["message"] = "Vendor list not found!";
}




?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
    .text {
        font-size: 1.2em;
    }

    .textColor {
        color: #0090ff;
        font-weight: bold;
    }

    .verticalAlign {
        text-align: right;
        vertical-align: bottom !important;
    }

    .tableStriped {
        background-color: #f2f2f2 !important;
    }

    .customPadding {
        padding-top: 180px !important;
    }

    .borderWhite {
        border: #fff;
    }

    .borderBlue {
        border-bottom: 3px solid #0090ff;
    }

    a.btn.shadow.waves-effect.waves-light:hover {
        background: #1a3a84db;
        color: white;
    }

    /* ######################################### */
    /* // design input type file STYLE  */

    .image-input input {
        display: none;
    }

    .image-input label {
        display: block;
        border: 2px dashed #dcdcdc;
        padding: 40px;
        cursor: pointer;
    }

    .image-input label i {
        font-size: 125%;
        margin-right: 0.3rem;
    }

    .image-input label:hover i {
        animation: shake 0.35s;
    }

    .image-input img {
        max-width: 175px;
        display: none;
    }

    .image-input span {
        display: none;
        cursor: pointer;
    }

    /******new****/

    .image-input label {
        display: flex;
        align-items: center;
        margin-top: 1em;
        justify-content: center;
        background: #fff;
        box-shadow: 6px 4px 11px -3px #00000070;
        padding: 20px;
        border-radius: 7px;
        border: 2px dashed #dcdcdc;
    }

    img.image-preview {
        object-fit: contain;
        aspect-ratio: 6/3;
        margin: auto;
    }

    .card.collect-payment-card {
        height: 323px;
        min-height: 100%;
    }

    .inputTableRow {
        overflow: auto;
    }

    /*******settlement*******/

    .settlement-card {
        min-height: 90%;
    }

    .settlement-card .image-input {
        overflow: auto;
        height: auto;
        background: #FFF;
        padding: 10px;
        border-radius: 12px;
        margin-top: 15px;
        box-shadow: 0px 3px 9px -5px #000;
    }

    @media (max-width: 575px) {
        .card.collect-payment-card {
            height: max-content;
            min-height: auto;
        }

        .card.collect-payment-card select {
            margin-top: 2em;
        }
    }


    @keyframes shake {
        0% {
            transform: rotate(0deg);
        }

        25% {
            transform: rotate(10deg);
        }

        50% {
            transform: rotate(0deg);
        }

        75% {
            transform: rotate(-10deg);
        }

        100% {
            transform: rotate(0deg);
        }
    }
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <form action="" method="POST">
                <!--Header-->
                <input type="hidden" name="paymentDetails[paymentCollectType]" value="collect">
                <div class="row m-0 p-0 py-2 my-2">
                    <div class="col-6">
                        <h5><strong>Vendors Payment</strong></h5>
                        <?= console($vendorListObj) ?>
                    </div>
                    <div class="col-6">
                        <div class="float-right d-flex">
                            <div class="mx-2"><button class="btn btn-success" type="button" id="submitCollectPaymentBtn">POST</button></div>
                            <!-- <div class="mx-2 btn btn-danger " data-dismiss="modal" aria-label="Close">X</div> -->
                        </div>
                    </div>
                </div>
                <!-- Collect Payment Modal -->
                <div class="modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Payment</h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="totalPaidAmtDiv"><span style="font-family: 'Font Awesome 5 Free';" id="totalReceiveAmt">0</span> amount paid against invoice</div>
                                <div class="totalCaptureAmtDiv"><span style="font-family: 'Font Awesome 5 Free';" id="totalCaptureAmt">0</span> amount captured as an advance</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="submitCollectPaymentBtn" class="btn btn-primary">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Body-->
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="card collect-payment-card">
                            <div class="card-header p-3">
                                <h4>Info</h4>
                            </div>
                            <div class="card-body">
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <label for="" class="">Payment Bank Details</label>
                                        <select name="paymentDetails[bankId]" class="form-control" id="paymentDetailsBankDropDown">
                                            <option value="0">Select Bank</option>
                                            <?php
                                            foreach ($cashAndAccountsObj["data"] as $row) {
                                                if ($row['bank_name'] != "") {
                                                    ?>
                                                    <option value="<?= $row['id'] ?>" data-is-icici-cib-enabled="<?= $row["isIciciCibEnabled"] ?>"><?= $row['bank_name'] ?><?= $row['account_no'] != "" ? "(" . $row['account_no'] . ")" : "" ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="" class="">Total Payment Amount</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">INR</span>
                                            </div>
                                            <input type="text" name="paymentDetails[collectPayment]" class="form-control border py-3 collectTotalAmt" placeholder="0.00" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="" class="">Total Due Amount</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">INR</span>
                                            </div>
                                            <input type="text" name="" class="form-control border py-3" placeholder="0.00" readonly>
                                        </div>
                                    </div>
                                    <div class="vendor-info">
                                        <label for="" class="">Vendor/s:</label>
                                        <div class="vendor-list" style="overflow-x: auto; max-height: 160px;">
                                            <?php
                                                console($vendorDetails);
                                            ?>
                                            <ol>
                                                <?php
                                                foreach ($vendors as $vendorId => $code) {
                                                    $vendorDetails = $grnObj->fetchVendorDetails($vendorId)['data'][0];
                                                ?>
                                                    <input type="hidden" name="vendorDetails[<?= $vendorId ?>][vendorId]" value="<?= $vendorId ?>">
                                                    <li><?= $vendorDetails['trade_name']; ?></li>
                                                <?php
                                                }
                                                ?>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="card collect-payment-card">
                            <div class="card-header p-3">
                                <h4>Info</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="image-input">
                                            <input type="file" name="paymentDetails[paymentAdviceImg]" accept="image/*" id="imageInput">
                                            <label for="imageInput" class="image-button"><i class="fa fa-image po-list-icon mr-2"></i> Upload Payment Advice</label>
                                            <img src="" class="image-preview">
                                            <span class="change-image float-right mt-3"><button class=" btn btn-danger"><i class="fa fa-times mr-2"></i>Remove</button></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-input totalamount">
                                            <label for="">Document Date</label>
                                            <input type="date" name="paymentDetails[documentDate]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-input totaldueamount">
                                            <label for="">Posting Date</label>
                                            <input type="date" name="paymentDetails[postingDate]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-input totaloverdue">
                                            <label for="">Transaction Id / Doc. No.</label>
                                            <input type="text" placeholder="Tnx. Id / Doc. No." name="paymentDetails[tnxDocNo]" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <span class="text-xs text-danger float-right" style="display:none" id="greaterMsg">Can't greater collect amount.</span>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="inputTableRow mt-3">
                            <table class="table">
                                <tr>
                                    <th>IV Doc. No.</th>
                                    <th>Vendor</th>
                                    <th>Status</th>
                                    <th>Due Dates</th>
                                    <th>Invoice Amt.</th>
                                    <th>Due Amt.</th>
                                    <th>Due Amt(USD).</th>
                                    <th>Pay. Amt.</th>
                                    <th>Due Amt</th>
                                    <th>Action</th>
                                </tr>
                                <?php

                                $paymentAmountDetails = [];

                                $vendors2 = array();
                                foreach ($secondDecodedStrings as $line) {
                                    $parts = explode('|', $line);
                                    $code = $parts[0];
                                    $vendorId = $parts[1];
                                    $vendorInvoiceFetch = queryGet("SELECT * FROM `erp_payment_initiate_request` WHERE code='$code' AND vendor_id=$vendorId", true);
                                    
                                    foreach ($vendorInvoiceFetch['data'] as $rowKey => $fetch) {
                                        $grnInvoiceObj = $grnObj->fetchGRNInvoiceById($fetch['invoice_id']);
                                        $grnInvoiceData = $grnInvoiceObj['data'][0];
                                        $grnInvoiceId = $grnInvoiceData['grnIvId'];

                                        $totalDueAmount += $grnInvoiceData['dueAmt'];
                                        $statusLabel = fetchStatusMasterByCode($grnInvoiceData['paymentStatus'])['data']['label'];
                                        $statusClass = "";
                                        if ($statusLabel == "paid") {
                                            $statusClass = "status";
                                        } elseif ($statusLabel == "partial paid") {
                                            $statusClass = "status-warning";
                                        } else {
                                            $statusClass = "status-danger";
                                        }

                                        $days = $grnInvoiceData['credit_period'];
                                        $date = date_create($grnInvoiceData['invoice_date']);
                                        date_add($date, date_interval_create_from_date_string($days . " days"));
                                        $creditPeriod = date_format($date, "d-m-Y");

                                        $due_amt = $grnInvoiceData['dueAmt'];
                                        $inv_amt = $grnInvoiceData['grnTotalAmount'];
                                        $duePercentage = ($due_amt / $inv_amt) * 100;

                                        // console($grnInvoiceData);

                                ?>
                                        <tr>
                                            <input type="hidden" name="paymentInvoiceDetails[<?= $rowKey ?>][grnIvId]" value="<?= $grnInvoiceId ?>">
                                            <input type="hidden" name="paymentInvoiceDetails[<?= $rowKey ?>][grnCode]" value="<?= $grnInvoiceData['grnIvCode'] ?>">
                                            <input type="hidden" name="paymentInvoiceDetails[<?= $rowKey ?>][vendorId]" value="<?= $grnInvoiceData['vendorId'] ?>">
                                            <input type="hidden" name="paymentInvoiceDetails[<?= $rowKey ?>][paymentStatus]" value="<?= $statusLabel ?>">
                                            <input type="hidden" name="paymentInvoiceDetails[<?= $rowKey ?>][creditPeriod]" value="<?= $grnInvoiceData['credit_period'] ?>">
                                            <input type="hidden" name="paymentInvoiceDetails[<?= $rowKey ?>][invAmt]" value="<?= $grnInvoiceData['grnTotalAmount'] ?>">
                                            <input type="hidden" name="paymentInvoiceDetails[<?= $rowKey ?>][dueAmt]" value="<?= $grnInvoiceData['dueAmt'] ?>">
                                            <td><?= $grnInvoiceData['grnIvCode'] ?? "<span class='text-danger'>Not Found!</span>"; ?></td>
                                            <td><?= $grnInvoiceData['vendorName'] ?></td>
                                            <td><span class="text-uppercase <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                                            <td><?= $creditPeriod ?></td>
                                            <td class="invAmt invoiceAmt" id="invoiceAmt_<?= $grnInvoiceId ?>"><?= $grnInvoiceData['grnTotalAmount'] ?></td>
                                            <td class="dueAmt" id="dueAmt_<?= $grnInvoiceId ?>"><?= $grnInvoiceData['dueAmt'] ?></td>
                                            <td class="dueAmt" id="dueAmtInInvoiceCurrency_<?= $grnInvoiceId ?>"><?= $grnInvoiceData['dueAmt'] ?></td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <?php if ($grnInvoiceData['dueAmt'] <= 0) { ?>
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">✅</span>
                                                        </div>
                                                        <input readonly type="number" class="form-control border py-3 receiveAmt" style="background-color: #c6e5d4 !important;" placeholder="No Due Payment">
                                                    <?php } else { ?>
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">USD</span>
                                                        </div>
                                                        <input type="number" step="0.01" name="paymentInvoiceDetails[<?= $rowKey ?>][recAmt]" class="form-control border py-3 receiveAmt" id="receiveAmt_<?= $grnInvoiceId ?>" value="<?= $grnInvoiceData['dueAmt'] ?>" placeholder="Enter amount">
                                                    <?php } ?>
                                                </div>
                                                <small style="display: none;" class="text-danger mt-n4 warningMsg" id="warningMsg_<?= $grnInvoiceId ?>">Amount Exceeded</small>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">USD</span>
                                                    </div>
                                                    <input type="number" class="form-control border py-3" placeholder="0.00" readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <a style="cursor:pointer" data-toggle="modal" data-target="#paymentInvoiceModal_<?= $rowKey ?>" class="btn btn-sm">
                                                    <i class="fa fa-cog po-list-icon"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <div class="modal fade right customer-modal classic-view-modal" id="paymentInvoiceModal_<?= $rowKey ?>" role="dialog" data-backdrop="true" aria-hidden="true">
                                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document" style="max-width: 30%;">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="height: 160px;">
                                                        <div class="text-light text-nowrap">
                                                            <p class="h4"><?= $grnInvoiceData['vendorName'] ?></p>
                                                            <p class="h6"><span class="text-muted">GRN IV CODE:</span> <?= $grnInvoiceData['grnIvCode'] ?></p>
                                                            <p class="h6"><span class="text-muted">GRN Total:</span> <?= number_format($grnInvoiceData['grnTotalAmount'], 2) ?> <span class="text-muted">Total Due:</span> <?= number_format($grnInvoiceData['dueAmt'], 2) ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="modal-body p-3">
                                                        <div class="card mb-3">
                                                            <div class="card-header py-1 text-light">Round Off</div>
                                                            <div class="card-body py-1">
                                                                <div class="d-flex gap-2 m-0 p-0">
                                                                    <div class="input-group input-group-sm w-50">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text">USD</span>
                                                                        </div>
                                                                        <select class="form-control">
                                                                            <option> + </option>
                                                                            <option> - </option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="input-group input-group-sm">
                                                                        <input type="number" class="form-control border py-3" placeholder="0.00">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card mb-3">
                                                            <div class="card-header py-1 text-light">Right Off</div>
                                                            <div class="card-body py-1">
                                                                <div class="d-flex gap-2 m-0 p-0">
                                                                    <div class="input-group input-group-sm w-50">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text">USD</span>
                                                                        </div>
                                                                        <select class="form-control">
                                                                            <option> + </option>
                                                                            <option> - </option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="input-group input-group-sm">
                                                                        <input type="number" class="form-control border py-3" placeholder="0.00">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card mb-3">
                                                            <div class="card-header py-1 text-light">Service Charge</div>
                                                            <div class="card-body py-1">
                                                                <div class="d-flex gap-2 m-0 p-0">
                                                                    <div class="input-group input-group-sm w-50">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text">USD</span>
                                                                        </div>
                                                                        <select class="form-control">
                                                                            <option> + </option>
                                                                            <option> - </option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="input-group input-group-sm">
                                                                        <input type="number" class="form-control border py-3" placeholder="0.00">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card mb-3">
                                                            <div class="card-header py-1 text-light">Forex Loss/Gain</div>
                                                            <div class="card-body py-1">
                                                                <div class="d-flex gap-2 m-0 p-0">
                                                                    <div class="input-group input-group-sm w-50">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text">USD</span>
                                                                        </div>
                                                                        <select class="form-control">
                                                                            <option> + </option>
                                                                            <option> - </option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="input-group input-group-sm">
                                                                        <input type="number" class="form-control border py-3" placeholder="0.00">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-primary">Appy changes</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
<?php
require_once("../common/footer.php");
?>
<script>
    $(document).ready(function() {

        $(document).on('change', '#paymentDetailsBankDropDown', function() {
            const bankId = parseInt($(this).val());
            const isIciciCibEnabled = parseInt($(this).find(':selected').data('is-icici-cib-enabled'));
            if (bankId > 0 && isIciciCibEnabled === 1) {
                console.log("Hello payment banks! ", bankId, isIciciCibEnabled);
            }
        });

        // $(".totalDueAmt").text("<?= $totalDueAmount ?>");
        // $(".collectTotalAmt").val("<?= $totalDueAmount ?>");
        // $(".remaningAmt").text("<?= $totalDueAmount ?>");

        // $(".receiveAmt").on("keyup", function() {
        //     let rowId = ($(this).attr("id")).split("_")[1];
        //     let inpVal = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
        //     let collectTotalAmt = 0;

        //     $(".receiveAmt").each(function() {
        //         collectTotalAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
        //     });

        //     $(".collectTotalAmt").val(collectTotalAmt);
        //     $(".remaningAmt").text(collectTotalAmt);
        // });

        // let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
        // if (collectTotalAmt <= 0) {
        //     $("#submitCollectPaymentBtn").prop("disabled", true);
        // } else {
        //     $("#submitCollectPaymentBtn").prop("disabled", false);
        // }


        // function calculateDueAmt() {
        //     let totalDueAmt = 0;
        //     let totalInvAmt = 0;
        //     $(".dueAmt").each(function() {
        //         totalDueAmt += (parseFloat($(this).text()) > 0) ? parseFloat($(this).text()) : 0;
        //     });
        //     $(".invAmt").each(function() {
        //         totalInvAmt += (parseFloat($(this).text()) > 0) ? parseFloat($(this).text()) : 0;
        //     });
        //     $(".totalDueAmt").html(totalDueAmt);
        //     $(".totalInvAmt").html(totalInvAmt);
        //     $(".totalDueAmtInp").val(totalDueAmt);
        //     $(".totalInvAmtInp").val(totalInvAmt);
        // }

        // // collect payment Amount 
        // $(document).on("keyup", ".collectTotalAmt", function() {
        //     let thisAmt = $(this).val();
        //     let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
        //     let rem = (parseFloat(thisAmt) + parseFloat(advancedPayAmt)) ? (parseFloat(thisAmt) + parseFloat(advancedPayAmt)) : 0;
        //     let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
        //     staticRemain = rem;
        //     $(".remaningAmt").text(rem);

        //     if (collectTotalAmt <= 0) {
        //         $("#submitCollectPaymentBtn").prop("disabled", true);
        //     } else {
        //         $("#submitCollectPaymentBtn").prop("disabled", false);
        //     }
        // })
        // // received payment amount
        // $(document).on("keyup", ".receiveAmt", function() {
        //     let rowId = ($(this).attr("id")).split("_")[1];
        //     let recAmt = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
        //     let invoiceAmt = $(`#invoiceAmt_${rowId}`).text();
        //     let dueAmt = (parseFloat($(`#dueAmt_${rowId}`).text()) > 0) ? parseFloat($(`#dueAmt_${rowId}`).text()) : 0;
        //     let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
        //     let remaningAmt = $(".remaningAmt").text();

        //     var totalDueAmt = 0;
        //     var totalRecAmt = 0;

        //     let duePercentage = ((parseFloat(dueAmt) - parseFloat(recAmt)) / parseFloat(invoiceAmt)) * 100;
        //     $(`#duePercentage_${rowId}`).text(`${Math.round(duePercentage,2)}%`);
        //     // $(`#duePercentage_${rowId}`).text(`${duePercentage.toFixed(2)}%`);

        //     $(".receiveAmt").each(function() {
        //         totalRecAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
        //     });

        //     let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
        //     let rem = parseFloat(collectTotalAmt) + parseFloat(advancedPayAmt);
        //     staticRemain = rem;
        //     // let remaintTotalAmt = parseFloat(collectTotalAmt) - parseFloat(totalRecAmt);
        //     let remaintTotalAmt = parseFloat(staticRemain) - parseFloat(totalRecAmt);
        //     if (totalRecAmt > collectTotalAmt) {
        //         console.log('over');
        //         $(".remaningAmt").text(collectTotalAmt);
        //         $(".remaningAmtInp").val(collectTotalAmt);
        //         $("#submitCollectPaymentBtn").prop("disabled", true);
        //         $("#greaterMsg").show();
        //     } else {
        //         console.log('ok');
        //         $(".remaningAmt").text(remaintTotalAmt);
        //         $(".remaningAmtInp").val(remaintTotalAmt);
        //         $("#submitCollectPaymentBtn").prop("disabled", false);
        //         $("#greaterMsg").hide();
        //     }
        //     console.log('due amt', dueAmt, recAmt);
        //     if (recAmt <= dueAmt) {
        //         $(`#warningMsg_${rowId}`).hide();
        //     } else {
        //         $(`#warningMsg_${rowId}`).show();
        //     }
        // });

        // $("#submitCollectPaymentBtn").on("click", function() {
        //     let enterAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
        //     let totalRecAmt2 = 0;
        //     let advancedPayAmt2 = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
        //     $(".receiveAmt").each(function() {
        //         totalRecAmt2 += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
        //     });
        //     let totalCaptureAmt = (parseFloat(enterAmt) + parseFloat(advancedPayAmt2)) - (parseFloat(totalRecAmt2));
        //     console.log(totalRecAmt2, enterAmt);
        //     $("#totalReceiveAmt").text(`₹${totalRecAmt2}`);
        //     $("#totalCaptureAmt").text(`₹${totalCaptureAmt}`);

        //     if (totalCaptureAmt === 0) {
        //         $(".totalCaptureAmtDiv").hide();
        //     } else {
        //         $(".totalCaptureAmtDiv").show();
        //     }
        // });
    })
</script>
<script src="<?= BASE_URL; ?>public/validations/paymentValidation.js"></script>