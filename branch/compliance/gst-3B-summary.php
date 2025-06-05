<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../../app/v1/functions/branch/func-compliance-controller.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("./controller/gstr3b.controller.php");
require_once("./controller/gstr1-json-repositary-controller.php");
require_once("./controller/gstr3b-json-repositary-controller.php");


session_start();
$gstr3bControllerObj = new ComplianceGSTR3b();
$queryParams = json_decode(base64_decode(($_GET['action'])));
$authGstinPortalObj = new AuthGstinPortal();
$authObj = $authGstinPortalObj->checkAuth();
$period = $queryParams->period;
$startDate = date("Y-m-d", strtotime($queryParams->startDate));
$endDate = date("Y-m-d", strtotime($queryParams->endDate));
// if (isset($_GET['sup_details'])) {
//     console($_GET['sup_details']);
// }

$gstr3bJsonRepoObj = new Gstr3bJsonRepository($period, $startDate, $endDate);
$jsonObj = $gstr3bJsonRepoObj->generate();
// $gstr3bSummary =$gstr3bControllerObj->getGstr3bSummary($queryParams->period);
$gstr1JsonRepoObj = new Gstr1JsonRepository($period, $startDate, $endDate);
$gstr1jsonObj = $gstr1JsonRepoObj->generate();

//----------gstr1 b2b data----------------------
$b2bTaxableAmount = 0;
foreach ($gstr1jsonObj['b2b'] as $invoiceItems) {
    foreach ($invoiceItems['inv'] as $rate => $rateWiseItem) {
        foreach ($rateWiseItem['itms'] as $oneItem) {
            // $b2bCGST += $oneItem['itm_det']["camt"];
            // $b2bSGST += $oneItem['itm_det']["samt"];
            // $b2bIGST += $oneItem['itm_det']["iamt"];
            // $b2bCESS += decimalValuePreview($oneItem['itm_det']["csamt"]);
            $b2bTaxableAmount += ($oneItem['itm_det']["txval"]);
        }
    }
}
$hsnSGST = 0;
$hsnCGST = 0;
$hsnIGST = 0;
$hsnCESS = 0;

foreach ($gstr1jsonObj['hsn']["data"] as $oneRow) {
    $hsnSGST += $oneRow["samt"];
    $hsnCGST += $oneRow["camt"];
    $hsnIGST += $oneRow["iamt"];
    $hsnCESS += $oneRow["csamt"];
    // $hsnTaxableAmount += $oneRow["txval"];
    // $hsnItemtotal_value = $oneRow["txval"] + $oneRow["iamt"] + $oneRow["camt"] + $oneRow["samt"] + $oneRow["csamt"];
}
$b2csIGST=0;
$b2csTaxableAmount=0;
foreach ($gstr1jsonObj["b2cs"] as $invoiceItems) {

    $b2csIGST += $invoiceItems["iamt"];
    $b2csTaxableAmount += $invoiceItems["txval"];
}
$_SESSION["gstr3bSummary"] = $gstr3bSummary;

$sup_details = $gstr3bSummary['data']['sup_details'];
$sup_details_total_igst = $sup_details['isup_rev']['iamt'] + $sup_details['osup_det']['iamt'] + $sup_details['osup_nil_exmp']['iamt'] + $sup_details['osup_nongst']['iamt'] + $sup_details['osup_zero']['iamt'];
$sup_details_total_cgst = $sup_details['isup_rev']['camt'] + $sup_details['osup_det']['camt'] + $sup_details['osup_nil_exmp']['camt'] + $sup_details['osup_nongst']['camt'] + $sup_details['osup_zero']['camt'];
$sup_details_total_sgst = $sup_details['isup_rev']['samt'] + $sup_details['osup_det']['samt'] + $sup_details['osup_nil_exmp']['samt'] + $sup_details['osup_nongst']['samt'] + $sup_details['osup_zero']['samt'];
$sup_details_total_cess = $sup_details['isup_rev']['csamt'] + $sup_details['osup_det']['csamt'] + $sup_details['osup_nil_exmp']['csamt'] + $sup_details['osup_nongst']['csamt'] + $sup_details['osup_zero']['csamt'];
$sup_details_total_taxvalue = $sup_details['isup_rev']['txval'] + $sup_details['osup_det']['txval'] + $sup_details['osup_nil_exmp']['txval'] + $sup_details['osup_nongst']['txval'] + $sup_details['osup_zero']['txval'];
$inter_sup_details = $gstr3bSummary['data']['inter_sup'];
$unreg_details_iamt = 0;
$unreg_details_txval = 0;
foreach ($inter_sup_details['unreg_details'] as $oneUnreg) {
    $unreg_details_iamt += $oneUnreg['iamt'];
    $unreg_details_txval += $oneUnreg['txval'];
}
$itc_elg_details = $gstr3bSummary['data']['itc_elg'];
$cgstAct_details = $gstr3bSummary['data']['eco_dtls'];
$cgstAct_igst = $cgstAct_details['eco_reg_sup']['iamt'] + $cgstAct_details['eco_sup']['iamt'];
$cgstAct_cgst = $cgstAct_details['eco_reg_sup']['camt'] + $cgstAct_details['eco_sup']['camt'];
$cgstAct_sgst = $cgstAct_details['eco_reg_sup']['samt'] + $cgstAct_details['eco_sup']['samt'];
$cgstAct_cess = $cgstAct_details['eco_reg_sup']['csamt'] + $cgstAct_details['eco_sup']['csamt'];

$nongst_inward_sup = $gstr3bSummary['data']['inward_sup'];
$nongst_inward_sup_interstate_sup = $nongst_inward_sup['isup_details'];
$intr_ltfee_details = $gstr3bSummary['data']['intr_ltfee'];
$intr_ltfee_details_igst = $intr_ltfee_details['intr_details']['iamt'] + $intr_ltfee_details['ltfee_details']['iamt'];
$intr_ltfee_details_cgst = $intr_ltfee_details['intr_details']['camt'] + $intr_ltfee_details['ltfee_details']['camt'];
$intr_ltfee_details_sgst = $intr_ltfee_details['intr_details']['samt'] + $intr_ltfee_details['ltfee_details']['samt'];
$intr_ltfee_details_cess = $intr_ltfee_details['intr_details']['csamt'] + $intr_ltfee_details['ltfee_details']['csamt'];

// console($gstr1jsonObj);


?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

<style>
    section.gstr-3B {
        padding: 0px 20px;
    }

    .head-btn-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .gstr-3B-filter {
        left: 0;
        top: 0;
    }

    .gstr-3B-filter a.active {
        background-color: #003060;
        color: #fff;
    }


    .daybook-filter-list.filter-list {
        display: flex;
        gap: 7px;
        justify-content: flex-end;
        position: relative;
        top: 0px;
        left: 18px;
        margin: 15px 0;
        float: right;
    }

    .daybook-filter-list.filter-list a.active {
        background-color: #003060;
        color: #fff;
    }

    .date-range-input {
        gap: 7px;
    }

    .date-range-input .form-input {
        width: 100%;
    }

    .daybook-tabs {
        flex-direction: row-reverse;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    ul.nav-preview {
        position: absolute;
        top: 15px;
        left: 100px;
        z-index: 9;
    }

    ul.nav-preview li .nav-link,
    ul.nav-preview li .nav-link:hover {
        display: flex;
        align-items: center;
        color: #000 !important;
    }

    ul.nav-preview li .nav-link.active {
        background-color: #fff;
        color: #000;
    }

    .nav-preview-content nav.details .nav-tabs {
        background: transparent;
        padding: 5px 3px 0px;
    }

    .nav-preview-content nav.details .nav-tabs button {
        font-weight: 500;
        color: #000;
        font-size: 13px;
    }

    .nav-preview-content nav.details .nav-tabs button.active {
        background: #a7a7a7;
        border: 0;
        color: #fff;
    }


    .date-picker {
        width: 260px;
        height: auto;
        max-height: 50px;
        background: white;
        position: relative;
        overflow: hidden;
        transition: all 0.3s 0s ease-in-out;
    }

    .date-picker .input {
        width: 100%;
        height: 50px;
        font-size: 0;
        cursor: pointer;
    }

    .date-picker .input .result,
    .date-picker .input button {
        display: inline-block;
        vertical-align: top;
    }

    .date-picker .input .result {
        width: calc(100% - 50px);
        height: 50px;
        line-height: 50px;
        font-size: 16px;
        padding: 0 10px;
        color: grey;
        box-sizing: border-box;
    }

    .date-picker .input button {
        width: 50px;
        height: 50px;
        background-color: #8392A7;
        color: white;
        line-height: 50px;
        border: 0;
        font-size: 18px;
        padding: 0;
    }

    .date-picker .input button:hover {
        background-color: #68768A;
    }

    .date-picker .input button:focus {
        outline: 0;
    }

    .date-picker .calendar {
        position: relative;
        width: 100%;
        background: #fff;
        border-radius: 0px;
        overflow: hidden;
    }

    .date-picker .ui-datepicker-inline {
        position: relative;
        width: 100%;
    }

    .date-picker .ui-datepicker-header {
        height: 100%;
        line-height: 50px;
        background: #8392A7;
        color: #fff;
        margin-bottom: 10px;
    }

    .date-picker .ui-datepicker-prev,
    .date-picker .ui-datepicker-next {
        width: 20px;
        height: 20px;
        text-indent: 9999px;
        border: 2px solid #fff;
        border-radius: 100%;
        cursor: pointer;
        overflow: hidden;
        margin-top: 12px;
    }

    .date-picker .ui-datepicker-prev {
        float: left;
        margin-left: 12px;
    }

    .date-picker .ui-datepicker-prev:after {
        transform: rotate(45deg);
        margin: -43px 0px 0px 8px;
    }

    .date-picker .ui-datepicker-next {
        float: right;
        margin-right: 12px;
    }

    .date-picker .ui-datepicker-next:after {
        transform: rotate(-135deg);
        margin: -43px 0px 0px 6px;
    }

    .date-picker .ui-datepicker-prev:after,
    .date-picker .ui-datepicker-next:after {
        content: "";
        position: absolute;
        display: block;
        width: 4px;
        height: 4px;
        border-left: 2px solid #fff;
        border-bottom: 2px solid #fff;
    }

    .date-picker .ui-datepicker-prev:hover,
    .date-picker .ui-datepicker-next:hover,
    .date-picker .ui-datepicker-prev:hover:after,
    .date-picker .ui-datepicker-next:hover:after {
        border-color: #68768A;
    }

    .date-picker .ui-datepicker-title {
        text-align: center;
    }

    .date-picker .ui-datepicker-calendar {
        width: 100%;
        text-align: center;
    }

    .date-picker .ui-datepicker-calendar thead tr th span {
        display: block;
        width: 100%;
        color: #8392A7;
        margin-bottom: 5px;
        font-size: 13px;
    }

    .date-picker .ui-state-default {
        display: block;
        text-decoration: none;
        color: #b5b5b5;
        line-height: 40px;
        font-size: 12px;
    }

    .date-picker .ui-state-default:hover {
        background: rgba(0, 0, 0, 0.02);
    }

    .date-picker .ui-state-highlight {
        color: #68768A;
    }

    .date-picker .ui-state-active {
        color: #68768A;
        background-color: rgba(131, 146, 167, 0.12);
        font-weight: 600;
    }

    .date-picker .ui-datepicker-unselectable .ui-state-default {
        color: #eee;
        border: 2px solid transparent;
    }

    .date-picker.open {
        max-height: 400px;
    }

    .date-picker.open .input button {
        background: #68768A;
    }



    #startDate {
        max-width: 200px;
    }

    .datepicker-bg {
        background-color: #003060;
        color: #fff;
    }

    table.summary-details-table {
        width: 100%;
    }

    table.summary-details-table tr:nth-child(even) td {
        background: #fff;
    }

    table.summary-details-table tr:nth-child(odd) td {
        background: #eee;
    }

    table.summary-details-table td {
        white-space: pre-wrap !important;
    }

    .summary-block .card {
        box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, rgba(0, 0, 0, 0.24) 0px 1px 2px;
        transition-duration: 0.2s;
        min-height: 260px;
    }

    .summary-block .card:hover {
        box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;
    }

    .summary-details {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .summary-details>div {
        flex-basis: calc(50% - 40px);
        margin: 0 0;
        padding: 10px 0;
    }
</style>


<div class="content-wrapper">
    <section class="gstr-3B">
        <h4 class="text-lg font-bold mt-4 mb-4">GSTR-3B(<?= $queryParams->startDate ?> To <?= $queryParams->endDate ?>)</h4>
        <div class="head-btn-section mb-3">
            <div class="filter-list gstr-3B-filter">
                <a href="./gst-3B-summary.php" class="btn active"><i class="fas fa-chart-bar mr-2"></i>Preview</a>
                <a class="btn active"><i class="fas fa-chart-bar mr-2"></i>Export To JSON</a>
            </div>
            <!-- <div class="input-group date" id="startDate">
                <span class="input-group-addon input-group-text datepicker-bg"><ion-icon name="calendar"></ion-icon>
                </span>
                <input type="text" class="form-control border px-5" name="startDate" placeholder="dd/mm/yyyy" />
            </div> -->
        </div>

        <div class="card bg-light">
            <div class="card-body p-0">
                <?php
                // console("b2bTaxableAmount");
                // console($gstr1jsonObj);
                ?>
                <!-- <a type="button" class="btn add-col setting-menu mt-3" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a> -->
                <!-- <ul class="nav nav-pills nav-preview mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active text-xs" id="pills-summary-tab" data-bs-toggle="pill" data-bs-target="#pills-summary" type="button" role="tab" aria-controls="pills-summary" aria-selected="true"><ion-icon name="document-text-outline" class="mr-2"></ion-icon>Summary</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-xs" id="pills-details-tab" data-bs-toggle="pill" data-bs-target="#pills-details" type="button" role="tab" aria-controls="pills-details" aria-selected="false"><ion-icon name="list-outline" class="mr-2"></ion-icon>Details</button>
                    </li>
                </ul> -->
                <div class="tab-content nav-preview-content pt-0" id="pills-tabContent">
                    <div class="tab-pane summary fade show active" id="pills-summary" role="tabpanel" aria-labelledby="pills-summary-tab">
                        <div class="card bg-light">
                            <div class="card-body border-top mt-3">
                                <p class="text-xs my-2"><span class="text-danger pr-1">*</span>Table 3.1(a), (b), (c) and (e) are auto-drafted based on values provided in gstr-3B. Whereas Table 3.1(d) is auto-drafted based on GSTR-2B</p>
                                <div class="row summary-block">

                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                        <div class="card my-3 bg-white rounded-3" id="taxOutwardReverseDiv">
                                            <div class="card-body">
                                                <label for="" class="py-1">3.1 Tax on outward and reverse charge inward supplies</label>
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">Integrated Tax</p>
                                                        <p class="text-xs my-2"><?= decimalValuePreview($hsnIGST) ?></p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">Central Tax</p>
                                                        <p class="text-xs my-2"><?= decimalValuePreview($hsnCGST) ?></p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">State Tax</p>
                                                        <p class="text-xs my-2"><?= decimalValuePreview($hsnSGST) ?></p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">CESS</p>
                                                        <p class="text-xs my-2"><?= decimalValuePreview($hsnCESS) ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                        <div class="card my-3 bg-white rounded-3" id="cgstActdiv">
                                            <div class="card-body">
                                                <label for="" class="py-1">3.1.1 Supplies notified under section 9(5) of the CGST Act, 2017</label>
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">Integrated Tax</p>
                                                        <p class="text-xs my-2"><?= $cgstAct_igst ?></p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">Central Tax</p>
                                                        <p class="text-xs my-2"><?= $cgstAct_cgst ?></p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">State Tax</p>
                                                        <p class="text-xs my-2"><?= $cgstAct_sgst ?></p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">CESS</p>
                                                        <p class="text-xs my-2"><?= $cgstAct_cess ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                        <div class="card my-3 bg-white rounded-3" id="interStateSupplyDiv">
                                            <div class="card-body">
                                                <label for="" class="py-1">3.2 Inter-state supplies</label>
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">Taxable Value</p>
                                                        <p class="text-xs my-2"><?= decimalValuePreview($b2csTaxableAmount) ?></p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">Integrated Tax</p>
                                                        <p class="text-xs my-2"><?= decimalValuePreview($b2csIGST) ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                        <div class="card my-3 bg-white rounded-3" id="eligibleitcdiv">
                                            <div class="card-body">
                                                <label for="" class="py-1">4. Eligible ITC</label>
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">Integrated Tax</p>
                                                        <p class="text-xs my-2"><?= decimalValuePreview($itc_elg_details['itc_net']['iamt']) ?></p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">Central Tax</p>
                                                        <p class="text-xs my-2"><?= decimalValuePreview($itc_elg_details['itc_net']['camt']) ?></p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">State Tax</p>
                                                        <p class="text-xs my-2"><?= decimalValuePreview($itc_elg_details['itc_net']['samt']) ?></p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">CESS</p>
                                                        <p class="text-xs my-2"><?= decimalValuePreview($itc_elg_details['itc_net']['csamt']) ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                        <div class="card my-3 bg-white rounded-3" id="nongstInward">
                                            <div class="card-body">
                                                <label for="" class="py-1">5. Exempt, nil and Non GST inward supllies</label>
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">Inter-state supplies</p>
                                                        <p class="text-xs my-2"></p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">Intra-state supplies</p>
                                                        <p class="text-xs my-2"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                        <div class="card my-3 bg-white rounded-3" id="lateFeediv">
                                            <div class="card-body">
                                                <label for="" class="py-1">5.1 Interest and Late fee for previous tax Period</label>
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">Integrated Tax</p>
                                                        <p class="text-xs my-2"><?= decimalValuePreview($intr_ltfee_details_igst) ?></p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">Central Tax</p>
                                                        <p class="text-xs my-2"><?= decimalValuePreview($intr_ltfee_details_cgst) ?></p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">State Tax</p>
                                                        <p class="text-xs my-2"><?= decimalValuePreview($intr_ltfee_details_sgst) ?></p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">CESS</p>
                                                        <p class="text-xs my-2"><?= decimalValuePreview($intr_ltfee_details_cess)  ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-3 my-3 mr-2">
                    <button type="button" class="btn btn-primary gap-2" id="savegstr3bBtn">SAVE GSTR3B</button>
                    <button type="button" class="btn btn-primary gap-2" id="paymentBtn">PROCEED TO PAYMENT</button>
                </div>
            </div>
        </div>
    </section>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
    $(document).ready(function() {

        $(function() {
            $('#startDate').datepicker({
                format: 'dd/mm/yyyy'
            });
        });
    });
</script>


<?php
require_once("../common/footer.php");
?>

<script>
    $(document).on("click", "#taxOutwardReverseDiv", function() {
        let action = '<?= json_encode($queryParams) ?>';
        window.location.href = `gst-3B-tax-outward.php?action=${btoa(action)}`;
    })
    $(document).on("click", "#eligibleitcdiv", function() {
        let action = '<?= json_encode($queryParams) ?>';
        window.location.href = `gst-3B-itc.php?action=${btoa(action)}`;
    })
    $(document).on("click", "#cgstActdiv", function() {
        let action = '<?= json_encode($queryParams) ?>';
        let data = '<?= json_encode($cgstAct_details) ?>';
        window.location.href = `gst-3B-cgst-act.php?action=${btoa(action)}&data=${btoa(data)}`;
    })
    $(document).on("click", "#nongstInward", function() {
        let action = '<?= json_encode($queryParams) ?>';
        window.location.href = `gst-3B-nongst-inward.php?action=${btoa(action)}`;
    })
    $(document).on("click", "#lateFeediv", function() {
        let action = '<?= json_encode($queryParams) ?>';
        window.location.href = `gst-3B-interest-latefee.php?action=${btoa(action)}`;
    })
    $(document).on("click", "#paymentBtn", function() {
        let action = '<?= json_encode($queryParams) ?>';
        window.location.href = `gst-3B-payments-tax.php?action=${btoa(action)}`;
    })
    $(document).on("click", "#interStateSupplyDiv", function() {
        let action = '<?= json_encode($queryParams) ?>';
        window.location.href = `gst-3B-inter-state-supply.php?action=${btoa(action)}`;
    })

    $(document).on("click", "#savegstr3bBtn", function() {
        let queryParams = '<?= base64_encode(json_encode($queryParams)) ?>';
        $.ajax({
            type: 'GET',
            url: `ajaxs/api/ajax-gstr3b-save-data.php`,
            data: {
                action: queryParams,
            },
            // dataType: "json",
            beforeSend: function() {
                console.log("Loading...");
            },
            success: function(response) {
                console.log(response);
                Swal.fire({
                    icon: response.status,
                    title: response.message,
                    timer: 1000,
                    showConfirmButton: false,
                })

            }
        })
    })
</script>

<script>
    $(document).ready(function() {
        var jsonData = JSON.parse(`<?= json_encode($jsonObj, JSON_PRETTY_PRINT) ?>`);
        console.log(jsonData);

        // Event listener for the button click
        $("#exportButton").on("click", function() {
            var jsonString = JSON.stringify(jsonData, null, 4); // Pretty format the JSON data
            var blob = new Blob([jsonString], {
                type: 'application/json'
            }); // Create a Blob object
            var link = document.createElement('a'); // Create a temporary link element

            // Create a URL for the Blob
            var url = URL.createObjectURL(blob);
            link.href = url;

            // Define the name of the exported file
            link.download = 'GSTR-3b Export <?= $queryParams ?>.json';

            // Trigger the download by simulating a click
            link.click();

            // Revoke the object URL after the download to free memory
            URL.revokeObjectURL(url);
        });
    });
</script>