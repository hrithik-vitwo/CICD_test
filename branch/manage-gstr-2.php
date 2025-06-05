<?php
require_once("../app/v1/connection-branch-admin.php");
include_once("../app/v1/functions/branch/func-compliance-controller.php");
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
// administratorAuth();
?>
<style>
    .filter-list a {
        background: #fff;
        box-shadow: 1px 2px 5px -1px #8e8e8e;
    }

    .filter-list {
        margin-bottom: 2em;
    }

    li.nav-item.complince a {
        background: #fff;
        color: #003060;
        z-index: 9;
        margin-bottom: 1em;
    }

    .reconColumn {
        background-color: #606470 !important;
        color: white;
    }

    table tr td {
        background: #ffffff !important;
        padding-left: 0px !important;
        padding-right: 0px !important;
        text-align: center !important;
        cursor: pointer;
    }

    table th {
        padding-left: 0px !important;
        padding-right: 0px !important;
        text-align: center !important;
    }

    .matchedRowColor-100 td {
        background-color: #d1f0cc !important;
        color: #064908;
    }

    .matchedRowColor-75 td {
        background-color: #b3d5f0 !important;
        color: #064908;
    }

    .matchedRowColor-50 td {
        background-color: #f0deb3 !important;
        color: #064908;
    }

    .matchedRowColor-25 td {
        background-color: #fdf0f0 !important;
        color: #064908;
    }

    table.dataTable>thead .sorting:before,
    table.dataTable>thead .sorting:after,
    table.dataTable>thead .sorting_asc:before,
    table.dataTable>thead .sorting_asc:after,
    table.dataTable>thead .sorting_desc:before,
    table.dataTable>thead .sorting_desc:after,
    table.dataTable>thead .sorting_asc_disabled:before,
    table.dataTable>thead .sorting_asc_disabled:after,
    table.dataTable>thead .sorting_desc_disabled:before,
    table.dataTable>thead .sorting_desc_disabled:after {
        display: block !important;
    }

    .dataTables_wrapper .row:nth-child(3) {
        display: flex !important;
    }

    div.dataTables_wrapper div.dataTables_filter {
        display: block !important;
    }

    div.dataTables_wrapper div.dataTables_filter label {
        font-size: 0;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 0;
        display: inline-block;
        width: auto;
        padding-left: 10px;
        border: 1px solid #E5E5E5;
        color: #1B2559;
        height: 25px;
        border-radius: 8px;
    }

    ul.pagination {
        border: 0;
    }

    /* .header-title .card-body {
        display: flex;
        justify-content: space-between;
    }
    .card-body::after, .card-footer::after, .card-header::after {
        display: none !important;
    } */
    .temp-recon-list-modal .modal-dialog {
        min-width: 75%;
    }

    .temp-recon-list-modal .modal-body {
        width: 100% !important;
    }

    /********otp start******/
    .title {
        max-width: 400px;
        margin: auto;
        text-align: center;
        font-family: "Poppins", sans-serif;
    }

    .title h3 {
        font-weight: bold;
    }

    .title p {
        font-size: 12px;
        color: #118a44;
    }

    .title p.msg {
        color: initial;
        text-align: initial;
        font-weight: bold;
    }

    .otp-input-fields {
        margin: auto;
        max-width: 400px;
        width: auto;
        display: flex;
        justify-content: center;
        gap: 10px;
        padding: 15px 10px;
    }

    .otp-input-fields input {
        height: 40px;
        width: 40px;
        background-color: transparent;
        border-radius: 4px;
        border: 1px solid #2f8f1f;
        text-align: center;
        outline: none;
        font-size: 16px;
        /* Firefox */
    }

    .otp-input-fields input::-webkit-outer-spin-button,
    .otp-input-fields input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .otp-input-fields input[type=number] {
        -moz-appearance: textfield;
    }

    .otp-input-fields input:focus {
        border-width: 2px;
        border-color: #287a1a;
        font-size: 20px;
    }

    .result {
        max-width: 400px;
        margin: auto;
        padding: 24px;
        text-align: center;
    }

    .result p {
        font-size: 24px;
        font-family: "Antonio", sans-serif;
        opacity: 1;
        transition: color 0.5s ease;
    }

    .result p._ok {
        color: green;
    }

    .result p._notok {
        color: red;
        border-radius: 3px;
    }

    .otp-section {
        margin-top: 39px;
        background: #ebebeb;
        padding: 10px;
        border-radius: 12px;
        box-shadow: 2px 7px 14px -3px #868686;
    }

    .otp-input-fields,
    .otp-input-fields-count-time {
        height: 160px;
        padding-top: 4em;
    }

    .second-step {
        display: none;
    }

    .otp-input-fields-count-time {
        display: none;
    }

    /* .connected-text {
        display: none;
    } */
    .robo-element {
        height: 50vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 25px;
    }

    .robo-element img {
        width: 200px;
        height: 200px;
        object-fit: contain;
    }

    /* .otp-input-fields-count-time {
        display: none;
    } */
    /********otp end******/
</style>

<link rel="stylesheet" href="../public/assets/listing.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" style="background-color: #ffffff !important;">
    <!-- Main content -->
    <?php

    // $authGstinPortalObj = new AuthGstinPortal();
    // $authCheckObj = $authGstinPortalObj->checkAuth();

    // console($authCheckObj);

    // if ($authCheckObj["status"] == "success") {
    //     $complianceGstr2bObj = new ComplianceGstr2b();
    //     // $getGstr2bDataObj = $complianceGstr2bObj->getGstr2bData();
    //     // $getGstr2bDataObj = $complianceGstr2bObj->fetchGstr2bData();
    //     $getGstr2bDataObj = $complianceGstr2bObj->fetchGstr2bData();
    //     // console($getGstr2bDataObj);
    // }
    $complianceGstr2bObj = new ComplianceGstr2b();
    // $getGstr2bDataObj = $complianceGstr2bObj->fetchGstr2bData();
    $getGstr2bDataObj = $complianceGstr2bObj->getGstr2bData("012023");
    // console($getGstr2bDataObj);

    $getGstr2bDataObj["status"] = "success";
    if (($getGstr2bDataObj["status"] ?? "") == "success") {

        $countPendingReconDataSql = 'SELECT * FROM `erp_branch_gstr2b_reconciliation` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `reconMonth`=' . date("m") . ' AND `reconYear`=' . date("Y") . ' AND `reconStatus`="pending"';
        $countPendingReconDataObj = queryGet($countPendingReconDataSql, true);

        ?>
        <section class="content">
            <div class="container-fluid my-4">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        GSTR-2B Reconciliation
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="div w-100 p-0 d-flex justify-content-between">
                                <div class="m-1 md-12">
                                    <p>Carry Forwared</p>
                                    <p style="font-size: 20px;"><i class="fas fa-rupee-sign"></i> 9,55,000.00</p>
                                </div>
                                <div style="background-color:#f7f8f9;margin-top: 20px;" class="p-1 md-12">
                                    <table>
                                        <tr>
                                            <td class="text-left" style="background-color: #f7f8f9!important;">
                                                <p>Available ITC (Current month)</p>
                                            </td>
                                            <td class="pl-2" style="background-color: #f7f8f9!important;">
                                                <p style="font-size: 20px;"><i class="fas fa-rupee-sign"></i> 9,55,000.00</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left" style="background-color: #f7f8f9!important;">
                                                <p>Left to recon</p>
                                            </td>
                                            <td class="pl-2" style="background-color: #f7f8f9!important;">
                                                <p style="font-size: 20px;"><i class="fas fa-rupee-sign"></i> 5,00,000.00</p>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="m-1 d-flex md-12">
                                    <div class="align-self-end">
                                        <button class="btn btn-sm btn-primary mr-3" id="matchTheTableRowBtn"><i class="fa fa-match text-light"></i> Match </button>
                                        <!-- <button class="btn btn-sm btn-primary mr-3" id="calculateMatchedTableRowBtn"><i class="fa fa-match text-light"></i> Calculate </button> -->
                                        <button class="btn btn-sm btn-primary mr-3" id="addMatchedRowToBusketBtn"><i class="fa fa-check text-light"></i> Add to List </button>
                                    </div>
                                    <div class="align-self-end fs-4 mx-2">
                                        <p>Reconcile Amount</p>
                                        <div><span><i class="fas fa-rupee-sign"></i><span class="reconListAmountSpan">0.00</span></span></div>
                                    </div>
                                    <div class="align-self-end">
                                        <!-- <a style="cursor:pointer" data-toggle="modal" data-target="#tempReconListModal" class="btn btn-sm waves-effect waves-light"><i class="fas fa-file po-list-icon"></i></a> -->
                                        <a style="cursor: pointer;" data-toggle="modal" id="tempReconListModalBtn" data-target="#tempReconListModal"><i class="fas fa-file" style="font-size:65px;"></i></a>
                                        <span class="badge badge-pill badge-info p-1" style="font-size: 10px!important;" id="reconListCounterSpan"><?= $countPendingReconDataObj["numRows"] ?? 0; ?></span>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade right temp-recon-list-modal customer-modal" id="tempReconListModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true">
                    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                        <!--Content-->
                        <div class="modal-content" id="tempReconListModalContent">
                            <!-- <div class="modal-header"></div>
                        <div class="modal-body"></div> -->
                        </div>
                    </div>
                    <!--/.Content-->
                </div>

                <div class="row p-0 m-0">
                    <div class="col-6 pr-0">
                        <p class="text-center">Portal Invoices</p>
                        <table class="table gstr2aTable" id="gstr2aPortalTable">
                            <thead>
                                <th>ITC</th>
                                <th>Date</th>
                                <th style="width: 10px;">GSTIN</th>
                                <th>VENDOR NAME</th>
                                <th>INVOICE NO</th>
                                <th>INV AMOUNT</th>
                                <th>TAX AMOUNT</th>
                                <th style="background-color: #011a3c!important; color:white">RECON</th>
                                <th style="background-color: #011a3c!important; color:white">MATCH</th>
                            </thead>
                            <tbody id="portalGstr2bTableBody">
                                <?php
                                foreach ($getGstr2bDataObj["data"] as $oneInv) {
                                ?>
                                    <tr>
                                        <td class="portalInvoiceItc"><?= $oneInv["itcAvl"] == "Y" ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>'; ?></td>
                                        <td class="portalInvoiceDate"><?= $oneInv["invDate"] ?></td>
                                        <td class="portalVendorGstin"><?= $oneInv["vendorGstin"] ?></td>
                                        <td class="portalVendorName"><?= substr($oneInv["vendorName"], 0, 15) ?></td>
                                        <td class="portalInvoiceNo"><?= $oneInv["invoiceNo"] ?></td>
                                        <td class="portalInvoiceAmt text-right"><?= $oneInv["invAmount"] ?></td>
                                        <td class="portalInvoiceTaxAmt text-right"><?= $oneInv["taxAmount"] ?></td>
                                        <td class="reconColumn">
                                            <input type="checkbox" name="" id="" class="reconCheckBox">
                                        </td>
                                        <td class="reconPercentageColumn reconColumn">0%</td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-6 pl-0">
                        <p class="text-center">Local Invoices</p>
                        <table class="table gstr2aTable" id="gstr2aLocalTable">
                            <thead>
                                <th>Date</th>
                                <th>GSTIN</th>
                                <th>VENDOR NAME</th>
                                <th>INVOICE NO</th>
                                <th>INV AMOUNT</th>
                                <th>TAX AMOUNT</th>
                                <th><i class="fas fa-bars"></i></th>
                            </thead>
                            <tbody id="localGstr2bTableBody">
                                <?php
                                $localInvoiceObj = queryGet('SELECT `grnIvId`, `grnId`, `companyId`, `branchId`, `vendorId`, `vendorCode`, `vendorGstin`, `vendorName`, `vendorDocumentNo`, `vendorDocumentDate`, `postingDate`, `grnTotalCgst`, `grnTotalSgst`, `grnTotalIgst`, `grnTotalAmount`, `paymentStatus` FROM `erp_grninvoice` WHERE `companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' ORDER BY `grnIvId` DESC', true);
                                if ($localInvoiceObj["status"] == "success") {
                                    $rowNo = 0;
                                    foreach ($localInvoiceObj["data"] as $oneLocInv) {
                                ?>
                                        <tr id="rightRow-<?= $rowNo += 1; ?>">
                                            <td class="localInvoiceDate"><?= $oneLocInv["vendorDocumentDate"] ?></td>
                                            <td class="localVendorGstin"><?= $oneLocInv["vendorGstin"] ?></td>
                                            <td class="localVendorName"><?= substr($oneLocInv["vendorName"], 0, 15); ?></td>
                                            <td class="localInvoiceNo"><?= $oneLocInv["vendorDocumentNo"] ?></td>
                                            <td class="localInvoiceAmt text-right"><?= $oneLocInv["grnTotalAmount"] ?></td>
                                            <td class="localInvoiceTaxAmt text-right"><?= $oneLocInv["grnTotalCgst"] + $oneLocInv["grnTotalSgst"] + $oneLocInv["grnTotalIgst"] ?></td>
                                            <td><i class="fa fa-sort"></i></td>
                                        </tr>
                                <?php
                                    }
                                }

                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="row">

            </div>
        </section>
    <?php
    } else {
        swalAlert($getGstr2bDataObj["status"], ucfirst($getGstr2bDataObj["status"]), $getGstr2bDataObj["message"]);
        // echo $getGstr2bDataObj["message"];
        ?>
        <section class="content">
            <div class="container-fluid my-4">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        GSTR-2B Reconciliation
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <div class="robo-element">
                        <!-- <img src="<?= BASE_URL ?>public/assets/gif/red-bot.gif" alt=""> -->
                        <img src="<?= BASE_URL ?>public/assets/img/grn-robot-static.png" alt="">
                        <p class="text-primary font-bold">Hi [user]! I am ViTWO, your reconcile assistant<br><br>Would you like to reconcile your vendor invoices with GST portal?<br><br>If yes, make sure the registered mobile is with you!</p>
                        <a style="cursor: pointer;" class="btn btn-primary compliance-auth" data-toggle="modal" id="gstPortalAuthorizeBtn" data-target="#tempReconListModal">Yes</a>
                        <a href="<?= BASE_URL ?>branch/" style="cursor: pointer;" class="btn btn-warning compliance-auth">No</a>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade right customer-modal" id="tempReconListModal" tabindex="-1" aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                <!--Content-->
                <div class="modal-content">
                    <!--Header-->

                    <div class="modal-body">

                        <div class="first-step" id="firstStep">
                            <div class="robo-element">
                                <img src="<?= BASE_URL ?>public/assets/gif/red-bot.gif" alt="robo-not-connected">
                            </div>
                            <p class="text-sm text-danger text-center font-bold">Sorry, I am not connected to GST server to operate.<br><br>Please allow me connect!</p>
                            <div class="connct-btn-section text-center mt-3 mb-2" id="connectBtn">
                                <button class="btn btn-primary connect-btn">Allow</button>
                            </div>
                        </div>

                        <div class="second-step" id="secondStep">
                            <div class="robo-element">
                                <img id="robotOtpImage" src="<?= BASE_URL ?>public/assets/gif/robot-otp.png" alt="robo-otp">
                            </div>
                            <p class="text-sm text-success text-center font-bold connected-text">OTP has been sent to your registered mobile number!</p>

                            <form action=" javascript: void(0)" class="otp-form" name="otp-form">
                                <div class="otp-section">
                                    <div id="otpInputFields">
                                        <div class="title mt-3">
                                            <p class="msg text-center">Please enter the correct OTP</p>
                                        </div>
                                        <div class="otp-input-fields bg-transparent">
                                            <input class="otp" type="text" oninput='digitValidate(this)' onkeyup='tabChange(1)' maxlength=1>
                                            <input class="otp" type="text" oninput='digitValidate(this)' onkeyup='tabChange(2)' maxlength=1>
                                            <input class="otp" type="text" oninput='digitValidate(this)' onkeyup='tabChange(3)' maxlength=1>
                                            <input class="otp" type="text" oninput='digitValidate(this)' onkeyup='tabChange(4)' maxlength=1>
                                            <input class="otp" type="text" oninput='digitValidate(this)' onkeyup='tabChange(5)' maxlength=1>
                                            <input class="otp" type="text" oninput='digitValidate(this)' onkeyup='tabChange(6)' maxlength=1>
                                        </div>
                                        <p class="text-center text-danger" id="otpRequiredSpan"></p>
                                        <p class="text-center text-danger" id="invalidOtpSpan"></p>
                                    </div>
                                    <div class="otp-input-fields-count-time" id="otpCountTime">
                                        <!-- <p class="text-center mt-3 mb-3">I am permitted to execute</p> -->
                                        <button class="btn btn-primary">Execute Me</button>
                                    </div>


                                    <div class="verify-btn-section text-center mt-2 mb-2" id="verifyBtn">
                                        <button class="btn btn-primary verify-otp-btn" id="verifyOTP">Verify OTP</button>
                                    </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php
    }
    ?>
</div>
<!-- /.Content Wrapper. Contains page content -->
<?php
require_once("common/footer.php");
?>
<script src="../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../public/assets/apexchart/chart-data.js"></script>
<script src="../public/assets/piechart/piecore.js"></script>
<script src="//www.amcharts.com/lib/4/charts.js"></script>
<script src="//www.amcharts.com/lib/4/themes/animated.js"></script>
<script src="../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../public/assets/apexchart/chart-data.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://www.amcharts.com/lib/3/amcharts.js?x"></script>
<script src="https://www.amcharts.com/lib/3/serial.js?x"></script>
<script src="https://www.amcharts.com/lib/3/themes/dark.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(document).ready(function() {
        // jQuery statements

        $('#gstr2aLocalTable').DataTable({
            "searching": true,
            "paging": false,
            "info": false,
            "lengthChange": false,
        });

        $('#gstr2aPortalTable').DataTable({
            "searching": true,
            "paging": false,
            "info": false,
            "lengthChange": false,
        });

        $('#localGstr2bTableBody').sortable({
            stop: function(event, ui) {
                calculateMatchedConditionsRows();
            }
        });

        $(document).on('click', "#addMatchedRowToBusketBtn", function() {
            addTempReconciliation();
        });

        $(document).on('click', "#matchTheTableRowBtn", function() {
            autoMatchLocalAndPortalReconData();
        });

        function autoMatchLocalAndPortalReconData() {
            let rowMatchedConditionsRatio = [];
            $('#portalGstr2bTableBody > tr').each(function(leftTrIndex, leftTr) {
                let leftThis = this;
                rowMatchedConditionsRatio[leftTrIndex] = rowMatchedConditionsRatio[leftTrIndex] ?? 0;
                let portalVendorGstin = $(this).find('.portalVendorGstin').text();
                let portalVendorName = $(this).find('.portalVendorName').text();
                let portalInvoiceNo = $(this).find('.portalInvoiceNo').text();
                let portalInvoiceAmt = $(this).find('.portalInvoiceAmt').text();
                let portalInvoiceTaxAmt = $(this).find('.portalInvoiceTaxAmt').text();
                console.log("================ LEFT ROW ==============", leftTrIndex);
                console.log("portalVendorGstin:", portalVendorGstin);
                console.log("portalVendorName:", portalVendorName);
                console.log("portalInvoiceNo:", portalInvoiceNo);
                console.log("portalInvoiceAmt:", portalInvoiceAmt);
                console.log("portalInvoiceTaxAmt:", portalInvoiceTaxAmt);

                $('#localGstr2bTableBody > tr').each(function(rightTrIndex, rightTr) {
                    let rightThis = this;
                    let localVendorGstin = $(this).find('.localVendorGstin').text();
                    let localVendorName = $(this).find('.localVendorName').text();
                    let localInvoiceNo = $(this).find('.localInvoiceNo').text();
                    let localInvoiceAmt = $(this).find('.localInvoiceAmt').text();
                    let localInvoiceTaxAmt = $(this).find('.localInvoiceTaxAmt').text();

                    let matchedConditions = 0;
                    if (portalVendorGstin == localVendorGstin) {
                        matchedConditions += 25;
                    }
                    if (portalInvoiceAmt == localInvoiceAmt) {
                        matchedConditions += 25;
                    }
                    if (localInvoiceTaxAmt == localInvoiceTaxAmt) {
                        matchedConditions += 25;
                    }
                    if (portalInvoiceNo == localInvoiceNo) {
                        matchedConditions += 25;
                    }

                    if (matchedConditions > rowMatchedConditionsRatio[leftTrIndex]) {

                        let tempRightTrData = $(`#localGstr2bTableBody tr:eq(${rightTrIndex})`).html();
                        let tempPrevRightTrData = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).html();

                        if (leftTrIndex > rightTrIndex) {
                            if (matchedConditions > rowMatchedConditionsRatio[rightTrIndex]) {
                                rowMatchedConditionsRatio[leftTrIndex] = matchedConditions;
                                $(leftThis).find('.reconPercentageColumn').html(`${matchedConditions}%`);
                                $(`#localGstr2bTableBody tr:eq(${rightTrIndex})`).html(tempPrevRightTrData);
                                $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).html(tempRightTrData);
                                // autoMatchLocalAndPortalReconData();
                            }
                        } else {
                            rowMatchedConditionsRatio[leftTrIndex] = matchedConditions;
                            $(leftThis).find('.reconPercentageColumn').html(`${matchedConditions}%`);
                            $(`#localGstr2bTableBody tr:eq(${rightTrIndex})`).html(tempPrevRightTrData);
                            $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).html(tempRightTrData);
                        }

                        console.log("========RIGHT ROW===========", rightTrIndex);
                        console.log("localVendorGstin:", localVendorGstin);
                        console.log("localVendorName:", localVendorName);
                        console.log("localInvoiceNo:", localInvoiceNo);
                        console.log("localInvoiceAmt:", localInvoiceAmt);
                        console.log("localInvoiceTaxAmt:", localInvoiceTaxAmt);
                        console.log("MATCHED PERCENTAGE::::", matchedConditions);

                    }
                });
            });
            $("#gstr2aPortalTable_filter input[type='search']").attr("disabled", "true");
            $("#gstr2aPortalTable th").click(function(event) {
                event.preventDefault();
            });
            $("#gstr2aLocalTable_filter input[type='search']").attr("disabled", "true");
            $("#gstr2aLocalTable th").click(function(event) {
                event.preventDefault();
            });
            calculateMatchedConditionsRows();
        }

        function calculateMatchedConditionsRows() {
            $(`#localGstr2bTableBody tr`).removeClass(`matchedRowColor-100 matchedRowColor-75 matchedRowColor-50 matchedRowColor-25`);
            $(`#portalGstr2bTableBody tr`).removeClass(`matchedRowColor-100 matchedRowColor-75 matchedRowColor-50 matchedRowColor-25`);

            $('#portalGstr2bTableBody > tr').each(function(leftTrIndex, leftTr) {
                let leftThis = this;
                let portalInvoiceDate = $(this).find('.portalInvoiceDate').text();
                let portalVendorGstin = $(this).find('.portalVendorGstin').text();
                let portalVendorName = $(this).find('.portalVendorName').text();
                let portalInvoiceNo = $(this).find('.portalInvoiceNo').text();
                let portalInvoiceAmt = $(this).find('.portalInvoiceAmt').text();
                let portalInvoiceTaxAmt = $(this).find('.portalInvoiceTaxAmt').text();
                let reconPercentage = $(this).find('.reconPercentageColumn').text();

                let localInvoiceDate = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localInvoiceDate').text();
                let localVendorGstin = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localVendorGstin').text();
                let localVendorName = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localVendorName').text();
                let localInvoiceNo = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localInvoiceNo').text();
                let localInvoiceAmt = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localInvoiceAmt').text();
                let localInvoiceTaxAmt = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localInvoiceTaxAmt').text();

                let matchedConditions = 0;
                if (portalVendorGstin == localVendorGstin) {
                    matchedConditions += 25;
                }
                if (portalInvoiceAmt == localInvoiceAmt) {
                    matchedConditions += 25;
                }
                if (localInvoiceTaxAmt == localInvoiceTaxAmt) {
                    matchedConditions += 25;
                }
                if (portalInvoiceNo == localInvoiceNo) {
                    matchedConditions += 25;
                }
                $(leftThis).find('.reconPercentageColumn').html(`${matchedConditions}%`);
                $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).addClass(`matchedRowColor-${matchedConditions}`);
                $(leftTr).addClass(`matchedRowColor-${matchedConditions}`);
                if (matchedConditions == 100) {
                    $(leftThis).find('.reconCheckBox').prop('checked', true);
                } else {
                    $(leftThis).find('.reconCheckBox').prop('checked', false);
                }
                // console.log(matchedConditions);
            });
        }


        function addTempReconciliation() {
            let reconData = [];
            $('#portalGstr2bTableBody > tr').each(function(leftTrIndex, leftTr) {
                let leftThis = this;
                let isChecked = $(leftThis).find('.reconCheckBox').prop('checked');
                if (isChecked) {
                    let portalInvoiceDate = $(this).find('.portalInvoiceDate').text();
                    let portalVendorGstin = $(this).find('.portalVendorGstin').text();
                    let portalVendorName = $(this).find('.portalVendorName').text();
                    let portalInvoiceNo = $(this).find('.portalInvoiceNo').text();
                    let portalInvoiceAmt = $(this).find('.portalInvoiceAmt').text();
                    let portalInvoiceTaxAmt = $(this).find('.portalInvoiceTaxAmt').text();

                    let reconPercentage = ($(this).find('.reconPercentageColumn').text()).slice(0, -1);

                    let localInvoiceDate = $(this).find('.localInvoiceDate').text();
                    let localVendorGstin = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localVendorGstin').text();
                    let localVendorName = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localVendorName').text();
                    let localInvoiceNo = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localInvoiceNo').text();
                    let localInvoiceAmt = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localInvoiceAmt').text();
                    let localInvoiceTaxAmt = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localInvoiceTaxAmt').text();

                    let matchedConditions = 0;
                    if (portalVendorGstin == localVendorGstin) {
                        matchedConditions += 25;
                    }
                    if (portalInvoiceAmt == localInvoiceAmt) {
                        matchedConditions += 25;
                    }
                    if (localInvoiceTaxAmt == localInvoiceTaxAmt) {
                        matchedConditions += 25;
                    }
                    if (portalInvoiceNo == localInvoiceNo) {
                        matchedConditions += 25;
                    }
                    reconData[leftTrIndex] = {
                        portalInvoiceDate,
                        portalVendorGstin,
                        portalVendorName,
                        portalInvoiceNo,
                        portalInvoiceAmt,
                        portalInvoiceTaxAmt,

                        reconPercentage,

                        localInvoiceDate,
                        localVendorGstin,
                        localVendorName,
                        localInvoiceNo,
                        localInvoiceAmt,
                        localInvoiceTaxAmt
                    };
                }
            });
            if (reconData.length > 0) {
                console.log(reconData);
                $.ajax({
                    method: "post",
                    url: "ajaxs/compliance/ajax-gstr2b-temp-reconciliation.php",
                    data: {
                        reconData: reconData
                    },
                    beforeSend: function() {
                        console.log("beforeSend");
                    },
                    success: function(data) {
                        let reconObj = JSON.parse(data);
                        let reconTableIndex = reconObj["data"];
                        let reconListCounter = reconObj["listCounter"];
                        let reconListAmount = reconObj["listTotalTax"];
                        $("#reconListCounterSpan").html(reconListCounter);
                        $(".reconListAmountSpan").html(reconListAmount);
                        reconTableIndex.forEach(function(reconTableIndex, index) {
                            $(`#portalGstr2bTableBody tr:eq(${reconTableIndex})`).remove();
                            $(`#localGstr2bTableBody tr:eq(${reconTableIndex})`).remove();
                        });
                        console.log("response from ajax:");
                        console.log(data);
                        console.log(reconObj);
                    }
                });
            } else {
                alert("Please select atleast one invoice to reconciliation!");
            }
        }


        $(document).on("click", "#tempReconListModalBtn", function() {
            $.ajax({
                method: "get",
                url: "ajaxs/compliance/ajax-gstr2b-temp-reconciliation.php",
                beforeSend: function() {
                    console.log("beforeSend");
                },
                success: function(data) {
                    $("#tempReconListModalContent").html(data);
                    console.log(data);
                }
            });
        });

    });
</script>







<!-- compliance auth modal -->
<script>
    var otp_inputs = document.querySelectorAll(".otp__digit");
    var mykey = "0123456789".split("");
    otp_inputs.forEach((_) => {
        _.addEventListener("keyup", handle_next_input);
    });

    function handle_next_input(event) {
        let current = event.target;
        let index = parseInt(current.classList[1].split("__")[2]);
        current.value = event.key;

        if (event.keyCode == 8 && index > 1) {
            current.previousElementSibling.focus();
        }
        if (index < 6 && mykey.indexOf("" + event.key + "") != -1) {
            var next = current.nextElementSibling;
            next.focus();
        }
        var _finalKey = "";
        for (let {
                value
            }
            of otp_inputs) {
            _finalKey += value;
        }
        if (_finalKey.length == 6) {
            document.querySelector("#_otp").classList.replace("_notok", "_ok");
            document.querySelector("#_otp").innerText = _finalKey;
        } else {
            document.querySelector("#_otp").classList.replace("_ok", "_notok");
            document.querySelector("#_otp").innerText = _finalKey;
        }
    }
    $(document).ready(function() {
        $("#connectBtn").click(function() {

            $.ajax({
                method: "POST",
                url: "ajaxs/compliance/ajax-compliance-auth.php",
                data: {
                    authType: "sendOtp"
                },
                beforeSend: function() {
                    $("#connectBtn").html(`Processing...`);
                },
                success: function(data) {
                    let dataObj = JSON.parse(data);
                    if (dataObj["status"] == "success") {
                        $("#firstStep").hide();
                        $("#secondStep").show();
                    } else {
                        $("#connectBtn").html(`<button class="btn btn-primary connect-btn">Try again to Connect</button>`);
                        Swal.fire({
                            icon: `warning`,
                            title: `Warning`,
                            text: `${dataObj["message"]}`,
                        });
                        console.log(dataObj["message"]);
                    }
                    // console.log(dataObj);
                }
            });
        });

        $("#verifyBtn").click(function() {
            $("#invalidOtpSpan").html("");
            $("#otpRequiredSpan").html("");
            let userOtp = "";
            $('.otp-input-fields').children('input[type=text], select').each(function() {
                console.log(userOtp = `${userOtp}${$(this).val()}`)
            });
            if (userOtp.toString().length == 6) {
                $.ajax({
                    method: "POST",
                    url: "ajaxs/compliance/ajax-compliance-auth.php",
                    data: {
                        authType: "verifyOtp",
                        authOtp: userOtp
                    },
                    beforeSend: function() {
                        $("#verifyBtn").html(`Processing...`);
                    },
                    success: function(data) {
                        let dataObj = JSON.parse(data);
                        if (dataObj["status"] == "success") {
                            $("#otpInputFields").hide();
                            $("#verifyOTP").hide();
                            $("#otpCountTime").show();
                            $(".connected-text").show();

                            $("#verifyBtn").html("");
                            $("#robotOtpImage").attr("src", "<?= BASE_URL ?>public/assets/gif/green-bot.gif");
                            $(".connected-text").html("Great! Now I am ready to be executed.");
                        } else {
                            $("#invalidOtpSpan").html("Please enter valid OTP!");
                            $("#verifyBtn").html(`<button class="btn btn-primary verify-otp-btn" id="verifyOTP">Verify OTP</button>`);
                        }
                        // console.log(dataObj);
                    }
                });
            } else {
                $("#otpRequiredSpan").html("Please enter OTP");
            }
        });
    });
    let digitValidate = function(ele) {
        console.log(ele.value);
        ele.value = ele.value.replace(/[^0-9]/g, '');
    }

    let tabChange = function(val) {
        let ele = document.querySelectorAll('input');
        if (ele[val - 1].value != '') {
            ele[val].focus()
        } else if (ele[val - 1].value == '') {
            ele[val - 2].focus()
        }
        $("#otpRequiredSpan").html("");
    }
</script>
<!-- / end compliance auth modal -->