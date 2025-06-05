<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("controller/gstr2b.controller.php");
$queryParams = json_decode(base64_decode($_GET['action']));
$gstr2bControllerObj = new ComplianceGSTR2b();
$pulledDataObj = $gstr2bControllerObj->getPulledData($queryParams->period);
// $currentMonthITCObj = $pulledDataObj['data']['itcsumm']['itcavl']['nonrevsup']['b2b'];
// $totalITCamount = $currentMonthITCObj['igst'] + $currentMonthITCObj['cgst'] + $currentMonthITCObj['sgst'] + $currentMonthITCObj['cess'];
// $docdataobj = $pulledDataObj['data']['docdata']['b2b'];
// console($pulledDataObj);
$totalITCAmt = 0;
$leftITCAmt = 0;
$reconITCAmt = 0;

$totalITCAmountsql = queryGet("SELECT * FROM `erp_compliance_gstr2b_documents` WHERE `company_id`=$company_id AND `branch_id`=$branch_id AND `return_period`='$queryParams->period'", true);

$reconITCAmountsql = queryGet("SELECT * FROM `erp_compliance_gstr2b_documents` WHERE `company_id`=$company_id AND `branch_id`=$branch_id AND `return_period`='$queryParams->period' AND `status` NOT IN ('reconciled', 'reversed')", true);
// console($reconITCAmountsql);

foreach ($pulledDataObj['data'] as $onedata) {
    $leftITCAmt += $onedata['cgst_amount'] + $onedata['sgst_amount'] + $onedata['igst_amount'] + $onedata['cess_amount'];
}

foreach ($totalITCAmountsql['data'] as $onedata) {
    $totalITCAmt += $onedata['cgst_amount'] + $onedata['sgst_amount'] + $onedata['igst_amount'] + $onedata['cess_amount'];
}

foreach ($reconITCAmountsql['data'] as $onedata) {
    $reconITCAmt += $onedata['cgst_amount'] + $onedata['sgst_amount'] + $onedata['igst_amount'] + $onedata['cess_amount'];
}
$gstr2bRecondataObj = queryGet("SELECT portalVendorInvNo,localVendorInvNo FROM `erp_branch_gstr2b_reconciliation` WHERE `company_id`=$company_id AND `branch_id`=$branch_id;", true);
$gstr2bRecondataObjdata = $gstr2bRecondataObj['data'];
$startDateObject = DateTime::createFromFormat('d-m-Y', $queryParams->startDate);
$startDate = $startDateObject->format('Y-m-d');
$endDateObject = DateTime::createFromFormat('d-m-Y', $queryParams->endDate);
$endDate = $endDateObject->format('Y-m-d');

//administratorAuth();
?>
<style>
    .content-wrapper {
        padding-top: 6em;
    }


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
        background-color: #7b7b7b !important;
        color: white;
    }

    .reconColumn_green {
        background-color: #91fe91 !important;
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

    .matchedRowColor-3 td {
        background-color: #d1f0cc !important;
        color: #064908;
    }

    .matchedRowColor-2 td {
        background-color: #b3d5f0 !important;
        color: #064908;
    }

    .matchedRowColor-1 td {
        background-color: #f0deb3 !important;
        color: #064908;
    }

    /* .matchedRowColor-25 td {
        background-color: #fdf0f0 !important;
        color: #064908;
    } */

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

    .recon-table-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .recon-table-head .amount-section {
        display: flex;
        justify-content: space-between;
    }

    .recon-table-head .amount-section p {
        display: flex;
        flex-direction: column;
        border-right: 1px solid #999999;
        padding-right: 7em;
    }

    .recon-table-head {
        display: grid;
        grid-template-columns: 2fr 1fr;
        align-items: center;
    }

    .recon-table-head .btn-section {
        display: flex;
        justify-content: end;
        gap: 10px;
    }

    table#previewTable {
        border: 0;
    }

    table#previewTable tr td {
        background: #fff !important;
    }

    table#previewTable tr:nth-child(2n+1) td {
        background: #d4e3ff !important;
    }

    .action-container {
        display: flex;
        align-items: center;
        gap: 5px;
        justify-content: flex-end;
        margin: 10px 0 5px;
    }

    .action-container button {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .recon-table table tr th,
    .recon-table table tr td {
        padding: 10px 4px !important;
        font-size: 12px;
    }

    .recon-close {
        font-size: 22px !important;
        font-weight: 500;
        top: 0 !important;
        opacity: 1;
    }

    .recon-excel-close {
        top: 0 !important;
    }

    td.portalInvoiceNo {
        justify-content: space-between;
        transition: all 0.5s ease;
    }

    .action-button {
        display: flex;
    }

    .action-button button ion-icon {
        font-size: 1.2rem;
        pointer-events: none;
    }

    .action-button button {
        padding: 3px 6px;
    }

    .action-button button.reversalBtn {
        color: #d27700;
    }

    .action-button button.grnBtn {
        color: #2f8f1f;
    }




    /* .otp-input-fields-count-time {
        display: none;
    } */
    /********otp end******/
</style>
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<style>
    .reconciled {
        background-color: #f0f0f0;
        /* Gray background to show it's disabled */
        color: #999;
        /* Gray text color */
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper recon-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <h4 class="text-lg font-bold mt-4 mb-4">GSTR-2B Reconciliation <small>(<?= $queryParams->startDate ?> to <?= $queryParams->endDate ?>)</small></h4>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card mt-5 recon-tab-card rounded mb-2">
                        <div class="card-body">
                            <?php
                            // console($queryParams);
                            // console($pulledDataObj);
                            ?>
                            <div class="recon-table-head">
                                <div class="amount-section">

                                    <!-- <p class="text-sm">Carry Forwared<span class="font-bold text-lg"><?= $currency ?> <?= round($opening_balance, 2) ?></span> </p> -->

                                    <p class="text-sm">Available ITC (Current month)
                                        <span class="font-bold text-lg"><?= $currency ?> <?= decimalValuePreview($totalITCAmt) ?></span>
                                    </p>
                                    <p class="text-sm">Left to recon<span class="font-bold text-lg"><?= $currency ?> <?= decimalValuePreview($leftITCAmt, 2) ?></span></p>
                                    <p class="text-sm">Reconcile Amount<span class="font-bold text-lg"><?= $currency ?> <?= decimalValuePreview($reconITCAmt, 2) ?></span></p>
                                </div>
                                <div class="btn-section">
                                    <a class="btn btn-primary" href="gstr2b-reconciled-invloice-list.php" id="reconInvBtn">Reconciled Invoices</a>
                                    <a class="btn btn-primary" href="gstr2b-reversal-invloice-list.php" id="reconInvBtn">Permanent Reversed Invoices</a>
                                    <button class="btn btn-primary" id="matchTheTableRowBtn">Match</button>
                                    <button class="btn btn-primary" id="addMatchedRowToBusketBtn">Confirm</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="text-lg mt-3 mb-2 font-bold">Unreconciled(Portal Data)</h4>
            <div class="row p-0 m-0 recon-table">
                <div class="col-lg-6 col-md-6 col-sm-6 px-0" style="overflow: auto;">
                    <table class="table gstr2aTable" id="gstr2aPortalTable">
                        <thead>
                            <th>Sl No</th>
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
                            $totalITCAmount = 0;
                            $slno = 0;
                            foreach ($pulledDataObj['data'] as $onedata) {
                                $slno++;
                                $currentITCAmount = $onedata['cgst_amount'] + $onedata['sgst_amount'] + $onedata['igst_amount'] + $onedata['cess_amount'];
                                $totalITCAmount += $currentITCAmount;
                                $isReconciled = $onedata['status'] === 'reconciled';
                                $rowClass = $isReconciled ? 'reconciled' : '';
                            ?>
                                <tr class="<?= $rowClass ?>">
                                    <td><?= $slno ?></td>
                                    <td class="portalInvoiceItc"><?= $currentITCAmount ?></td> <!-- Display the current row's ITC amount -->
                                    <td class="portalInvoiceDate"><?= $onedata['doc_date'] ?></td>
                                    <td class="portalVendorGstin"><?= $onedata['vendor_gstin']  ?></td>
                                    <td class="portalVendorName"><?= $onedata['vendor_name'] ?></td>
                                    <td class="portalInvoiceNo d-flex gap-2">
                                        <a href="#" data-id="<?= $onedata['id'] ?>" class="reversalCheckBox"><?= $onedata['inv_number'] ?></a>
                                        <div class="action-button" class="reversalGrnBtnDiv" id="reversalGrnBtnDiv_<?= $onedata['id'] ?>" style="display: none;">
                                            <button class="reversalBtn btn btn-transparent" id="reversalBtn_<?= $onedata['id'] ?>" title="Reversal">
                                                <ion-icon name="refresh" title=""></ion-icon>
                                            </button>
                                            <button class="grnBtn btn btn-transparent" title="GRN">
                                                <ion-icon name="document-outline" title=""></ion-icon>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="portalInvoiceAmt text-right"><?= ($onedata['inv_amount']) ?></td>
                                    <td class="portalInvoiceTaxAmt text-right"><?= ($onedata['tax_amount']) ?></td>
                                    <td class="reconColumn">
                                        <input type="checkbox" name="" id="" data-id="<?= $onedata['id'] ?>" class="reconCheckBox">
                                    </td>
                                    <td class="reconPercentageColumn reconColumn text-white">0%</td>
                                </tr>
                            <?php
                            } ?>

                    </table>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6 px-0" style="overflow: auto;">
                    <!-- <p class="text-center">Local Invoices</p> -->
                    <table class="table defaultdataTable gstr2aTable" id="gstr2aLocalTable">
                        <thead>
                            <tr>
                                <th>ITC</th>
                                <th>Doc Date</th>
                                <th style="width: 10px;">GSTIN</th>
                                <th>VENDOR NAME</th>
                                <th>INVOICE NO</th>
                                <th>INV AMOUNT</th>
                                <th>TAX AMOUNT</th>
                                <th><i class="fas fa-bars"></i></th>
                            </tr>
                        </thead>
                        <tbody id="localGstr2bTableBody">
                            <?php
                            $localInvoiceObj = queryGet('SELECT `grnIvId`, `grnId`, `companyId`, `branchId`, `vendorId`, `vendorCode`, `vendorGstin`, `vendorName`, `vendorDocumentNo`, `vendorDocumentDate`, `postingDate`, `grnTotalCgst`, `grnTotalSgst`, `grnTotalIgst`, `grnTotalAmount`, `paymentStatus` FROM `erp_grninvoice` WHERE `companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `postingDate` BETWEEN "' . $startDate . '" AND "' . $endDate . '" ORDER BY postingDate DESC', true);
                            // console($localInvoiceObj);
                            if ($localInvoiceObj["status"] == "success") {
                                $rowNo = 0;
                                foreach ($localInvoiceObj["data"] as $oneLocInv) {
                                    // Prepare the document number for debugging
                                    $vendorDocumentNo = $oneLocInv['vendorDocumentNo'];
                                    // Log the document number
                                    // console("Checking document number: " . $vendorDocumentNo);

                                    // Check if the document exists
                                    $isExistSql = queryGet("SELECT gstr.recon_document FROM erp_compliance_gstr2b_documents AS gstr WHERE gstr.company_id=$company_id AND gstr.branch_id=$branch_id AND gstr.recon_document='$vendorDocumentNo'")['numRows'];

                                    // Log the existence result
                                    // console("Existence check result for document number $vendorDocumentNo: " . $isExistSql);

                                    // Render the row only if the document does not exist
                                    if ($isExistSql == 0) {
                            ?>
                                        <tr id="rightRow-<?= ++$rowNo; ?>">
                                            <td></td>
                                            <td class="localInvoiceDate"><?= $oneLocInv["postingDate"] ?></td>
                                            <td class="localVendorGstin"><?= $oneLocInv["vendorGstin"] ?></td>
                                            <td class="localVendorName"><?= substr($oneLocInv["vendorName"], 0, 15); ?></td>
                                            <td class="localInvoiceNo"><?= $vendorDocumentNo ?></td>
                                            <td class="localInvoiceAmt text-right"><?=decimalValuePreview($oneLocInv["grnTotalAmount"]) ?></td>
                                            <td class="localInvoiceTaxAmt text-right"><?=decimalValuePreview($oneLocInv["grnTotalCgst"] + $oneLocInv["grnTotalSgst"] + $oneLocInv["grnTotalIgst"]) ?></td>
                                            <td><i class="fa fa-sort"></i></td>
                                        </tr>
                            <?php
                                    }
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

</div>
<!-- /.Content Wrapper. Contains page content -->
<?php
require_once("../common/footer.php");
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
<script src="<?= BASE_URL ?>public/assets/bhootujs/best-pair.js"></script>
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
                console.log("portalVendorGstin:" + portalVendorGstin);
                console.log("portalVendorName:" + portalVendorName);
                console.log("portalInvoiceNo:" + portalInvoiceNo);
                console.log("portalInvoiceAmt:" + portalInvoiceAmt);
                console.log("portalInvoiceTaxAmt:" + portalInvoiceTaxAmt);

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
                    if (portalInvoiceTaxAmt == localInvoiceTaxAmt) {
                        matchedConditions += 25;
                    }
                    if (portalInvoiceNo == localInvoiceNo) {
                        matchedConditions += 25;
                    }
                    console.log("matchedConditions" + matchedConditions)
                    console.log("rowMatchedConditionsRatio[leftTrIndex]" + rowMatchedConditionsRatio[leftTrIndex])

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
                if (portalInvoiceTaxAmt == localInvoiceTaxAmt) {
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
                let $leftThis = $(this); // Use jQuery object for consistency
                let isChecked = $leftThis.find('.reconCheckBox').prop('checked');

                if (isChecked) {
                    // Extract portal invoice details
                    let portalInvoiceDate = $leftThis.find('.portalInvoiceDate').text().trim();
                    let portalVendorGstin = $leftThis.find('.portalVendorGstin').text().trim();
                    let portalVendorName = $leftThis.find('.portalVendorName').text().trim();
                    let portalInvoiceNo = $leftThis.find('.portalInvoiceNo').text().trim();
                    let portalInvoiceAmt = $leftThis.find('.portalInvoiceAmt').text().trim();
                    let portalInvoiceTaxAmt = $leftThis.find('.portalInvoiceTaxAmt').text().trim();
                    let invId = $leftThis.find('.reconCheckBox').data('id');

                    let reconPercentage = $leftThis.find('.reconPercentageColumn').text().trim().slice(0, -1);

                    // Extract local invoice details
                    let $localRow = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`);
                    let localInvoiceDate = $localRow.find('.localInvoiceDate').text().trim();
                    let localVendorGstin = $localRow.find('.localVendorGstin').text().trim();
                    let localVendorName = $localRow.find('.localVendorName').text().trim();
                    let localInvoiceNo = $localRow.find('.localInvoiceNo').text().trim();
                    let localInvoiceAmt = $localRow.find('.localInvoiceAmt').text().trim();
                    let localInvoiceTaxAmt = $localRow.find('.localInvoiceTaxAmt').text().trim();

                    // Matching conditions
                    let matchedConditions = 0;
                    if (portalVendorGstin === localVendorGstin) {
                        matchedConditions += 25;
                    }
                    if (portalInvoiceAmt === localInvoiceAmt) {
                        matchedConditions += 25;
                    }
                    if (portalInvoiceTaxAmt === localInvoiceTaxAmt) { // This comparison should be between portal and local tax amounts
                        matchedConditions += 25;
                    }
                    if (portalInvoiceNo === localInvoiceNo) {
                        matchedConditions += 25;
                    }

                    // Store reconciliation data
                    reconData.push({
                        invId,
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
                        localInvoiceTaxAmt,
                        matchedConditions
                    });
                }
            });

            if (reconData.length > 0) {
                console.log(reconData);

                Swal.fire({
                    icon: 'warning',
                    title: 'Are you sure?',
                    text: `Are you sure to reconcile ?`,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Confirm'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            method: "POST",
                            url: "../ajaxs/compliance/ajax-gstr2b-reconciliation.php",
                            data: {
                                reconData: reconData,
                                period:'<?= json_encode($queryParams->period) ?>'
                            },
                            beforeSend: function() {
                                console.log("beforeSend");
                            },
                            success: function(data) {
                                // After successful reconciliation, hide the rows
                                reconData.forEach(function(item) {
                                    // Find and hide the corresponding rows
                                    let $leftRow = $(`#portalGstr2bTableBody .reconCheckBox[data-id="${item.invId}"]`).closest('tr');
                                    let leftRowIndex = $leftRow.index(); // Get the row index

                                    // Hide the left row
                                    $leftRow.hide();

                                    // Hide the corresponding local row
                                    let $localRow = $(`#localGstr2bTableBody tr:eq(${leftRowIndex})`);
                                    $localRow.hide();
                                });
                                let reconObj = JSON.parse(data);
                                Swal.fire({
                                    icon: reconObj.status,
                                    title: reconObj.message,
                                    timer: 1000,
                                    showConfirmButton: false,
                                })
                                let reconTableIndex = reconObj["data"];
                                let reconListCounter = reconObj["listCounter"];
                                let reconListAmount = reconObj["listTotalTax"];

                                $("#reconListCounterSpan").html(reconListCounter);
                                $(".reconListAmountSpan").html(reconListAmount);

                                reconTableIndex.forEach(function(index) {
                                    $(`#portalGstr2bTableBody tr:eq(${index})`).remove();
                                    $(`#localGstr2bTableBody tr:eq(${index})`).remove();
                                });

                                console.log("response from ajax:", data, reconObj);
                            },
                            error: function(xhr, status, error) {
                                console.error("Error during AJAX request:", error);
                            }
                        });
                    }

                });
            } else {
                alert("Please select at least one invoice for reconciliation!");
            }
        }



        $(document).on("click", "#tempReconListModalBtn", function() {
            $.ajax({
                method: "get",
                url: "ajaxs/api/ajax-gstr2b-temp-reconciliation.php",
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

<script>
    $(document).ready(function() {
        $('.reversalCheckBox').on('click', function() {
            var id = $(this).data('id');
            $('#reversalGrnBtnDiv_' + id).toggle(this.checked);
        });
    });


    /* Permannet reversal button     */

    $('.reversalBtn').on('click', function() {
        let id = $(this).attr('id').split("_")[1];

        Swal.fire({
            // icon: 'warning',
            title: 'Are you sure?',
            text: `Are you sure to permanently reverse this invoice?`,
            input: 'text', // Add an input field for the user to type in
            inputPlaceholder: 'Enter reason for reversal', // Placeholder for the input field
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm',
            preConfirm: (inputValue) => {
                if (!inputValue) {
                    Swal.showValidationMessage('Please enter a reason for reversal'); // Ensure a reason is provided
                }
                return inputValue; // Return the input value if valid
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let reversalReason = result.value; // Get the input value (reason)

                $.ajax({
                    method: "POST",
                    url: "ajaxs/api/ajax-gstr2b-permanent-rev.php",
                    data: {
                        id,
                        reversalReason // Pass the reversal reason along with the ID
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        console.log("beforeSend");
                    },
                    success: function(response) {
                        console.log(response);

                        let data = response.data;
                        Swal.fire({
                            icon: response.status,
                            title: response.message,
                            timer: 1000,
                            showConfirmButton: false,
                        }).then(() => {
                            // If success, remove the table row
                            if (response.status === 'success') {
                                $(`#reversalGrnBtnDiv_${id}`).closest('tr').remove();
                            }
                        });
                    }
                });
            }
        });
    });
</script>
<!-- <script>
    $(document).on("click",".grnBtn",function(){
        window.location.href="../location/manage-grn-invoice.php"
    })
</script> -->