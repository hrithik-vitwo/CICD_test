<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/common/func-common.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");

require_once("../../app/v1/functions/branch/func-grn-controller.php");
include_once("../../app/v1/connection-branch-admin.php");
include("../../app/v1/functions/branch/func-bills-controller.php");
include("../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../app/v1/functions/branch/func-brunch-po-controller.php");

global $isPoEnabled;
$BranchPoObj = new BranchPo();
?>
<style>
    .content-wrapper {
        height: auto !important;
    }

    .row.grn-create .card ul {
        padding-left: 0;
    }

    .quick-registration-vendor {
        overflow: auto;
    }

    div.grn-table {
        margin: 40px 0 10px;
    }

    table.grn-table tr td {
        padding: 5px 15px !important;
    }

    table.grn-table tr td input,
    table.grn-table tr td select {
        height: 30px;
        width: auto !important;
    }

    table.grn-table tr td select {
        width: auto;
    }

    .derived-qty-info {
        display: inline-block;
    }

    .derived-qty-info p {

        white-space: pre-line;
        top: -53px !important;

    }

    .derived-qty-info::before {
        content: '!' !important;
    }

    span.error {
        position: relative;
        display: block;
        text-align: center;
        top: -7px;
        left: 25px;
        margin: 5px 0;
        display: flex !important;
        align-items: center;
        gap: 7px;
        justify-content: center;
        font-size: 10px !important;
    }

    span.calculate-error {
        left: 0;
    }

    table.grn-table tr.span-error-tr td {
        background-color: transparent !important;
        height: 0;
        padding: 0 !important;
    }

    .invoice-iframe .modal-dialog {
        max-width: 705px;
    }

    .modal-open {
        overflow: auto !important;
    }

    .row.grn-vendor-details .display-flex select {
        font-size: 9px !important;
        background: none;
        border: 0;
        max-width: 120px;
        cursor: pointer;
        height: auto;
        background-color: #0000001a;
        margin: 6px 0px;
    }

    .total-amount-grn-table .card {
        max-width: 500px;
        margin-left: auto;
        border-radius: 7px;
        margin-bottom: 0;
    }

    .grn-table.pending-grn-view table tr.span-error-tr td.bg-transparent {
        background: #fff !important;
    }

    .total-amount-grn-table .card td {
        padding: 10px 15px;
    }

    .select-type {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .select-type div {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .select-type div label {
        margin-bottom: 0;
    }

    form#mapInvoiceItemCodeForm .select2-container {
        width: 100% !important;
    }

    form#changeInvoiceItemCodeForm .select2-container {
        width: 100% !important;
    }

    .filter-list a.active {
        background: #003060;
        color: #fff;
    }

    div#DataTables_Table_0_length {
        display: none;
    }

    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row:nth-child(3) {
        align-items: center;
        display: flex !important;
        margin: 1rem 0;
    }

    .quick-registration-vendor {
        height: 56vh;
    }

    div.dataTables_wrapper div.dataTables_filter label {
        left: -20rem;
        display: flex;
        align-items: center;
        justify-self: end;
        gap: 1rem;
        position: absolute;
        top: -42px;
    }


    .vitwo-alpha-global .modal.discountViewModal .modal-dialog {
        max-width: 50%;
    }

    .vitwo-alpha-global .modal.discountViewModal .modal-dialog .modal-cog-right .form-input select,
    .vitwo-alpha-global .modal.discountViewModal .modal-dialog .modal-cog-right .form-input input {
        width: 100% !important;
    }

    .vitwo-alpha-global .modal.discountViewModal .modal-body .check-box input[type="checkbox"] {
        width: 12px;
        height: 12px;
    }

    .vitwo-alpha-global .modal.discountViewModal .modal-body .check-box {
        display: flex;
        align-items: center;
        gap: 5px;
    }


    @media (max-width: 575px) {
        #grnInvoicePreviewIfram {
            display: block;
        }

        div.grn-table {
            margin: 50px 0;
        }

        span.error {
            left: 440px;
        }
    }
</style>

<style>
    .storageSelect select {
        max-width: 200px;
    }
</style>

<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<div class="content-wrapper is-pending-grn vitwo-alpha-global" style="height: auto !important;">
    <!-- Modal -->
    <div class="modal fade" id="examplePendingGrnModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="examplePendingGrnModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card">
                <div class="modal-header card-header py-2 px-3">
                    <h4 class="modal-title font-monospace text-md text-white" id="examplePendingGrnModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
                    <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div id="notesModalBody" class="modal-body card-body">
                </div>
            </div>
        </div>
    </div>

    <?php
    $grnObj = new GrnController();
    //console($_POST);

    if (isset($_POST["vendorCode"]) && $_POST["vendorCode"] != "") {
        $createGrnObj = $grnObj->createGrn($_POST);

        if ($createGrnObj["status"] == "success") {
            swalAlert($createGrnObj["status"], ucfirst($createGrnObj["status"]), $createGrnObj["message"], BASE_URL . "branch/location/manage-grn.php");
        } else {
            swalAlert($createGrnObj["status"], ucfirst($createGrnObj["status"]), $createGrnObj["message"]);
        }

        //console("Hello grn process");
        //console($createGrnObj);

    }

    if (isset($_POST["add-table-settings"])) {
        $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
        swalToast($editDataObj["status"], $editDataObj["message"]);
    }

    if (isset($_GET["view"]) && $_GET["view"] != "" && isset($_GET["type"]) && $_GET["type"] == "grn" && isset($_GET["posted"]) && $_GET["posted"] = 1) {
        require_once("components/grn/posted-pending-grn.php");
    } elseif (isset($_GET["view"]) && $_GET["view"] != "" && isset($_GET["type"]) && $_GET["type"] == "srn" && isset($_GET["posted"]) && $_GET["posted"] = 1) {
        require_once("components/grn/posted-pending-srn.php");
    } elseif (isset($_GET["view"]) && $_GET["view"] != "" && isset($_GET["type"]) && $_GET["type"] == "grn") {
        if ($isPoEnabled == 0) {
            require_once("components/grn/pending-grn-view.php");
        } else {
            require_once("components/grn/pending-grn-view-po-enabled.php");
        }
    } elseif (isset($_GET["view"]) && $_GET["view"] != "" && isset($_GET["type"]) && $_GET["type"] == "srn") {

        if ($isPoEnabled == 0) {
            require_once("components/grn/pending-srn-view.php");
        } else {
            require_once("components/grn/pending-srn-view-po-enabled.php");
        }
    } elseif (isset($_GET["posting"])) {
        require_once("components/grn/posted-grn.php");
    } else {
        require_once("components/grn/pending-grn2.php");
    }
    require_once("../common/footer.php");
    ?>