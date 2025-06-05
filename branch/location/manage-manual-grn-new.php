<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");

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
        padding: 40px 0;
    }

    table.grn-table tr td {
        padding: 18px 13px !important;
    }

    table.grn-table tr td input,
    table.grn-table tr td select {
        height: 30px;
    }

    table.grn-table tr td select {
        width: 132px;
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
        top: -28px;
        left: -97px;
        margin: 10px 0;
        color: orange !important;
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

    .row.grn-vendor-details .display-flex .form-input.flex-block {
        flex: 0 0 40%;
    }

    .grn-form-input-text .func-note {
        font-size: 0.65rem;
        font-weight: 500;
        color: #000;
        font-style: italic;
        white-space: normal;
    }

    .row.grn-vendor-details .display-flex select {
        font-size: 9px !important;
        background: none;
        border: 0;
        max-width: 100%;
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

    .total-amount-grn-table .card td {
        padding: 10px 15px;
    }

    .is-manual-grn-srn button.po-add-btn {
        font-size: 0.6rem !important;
    }

    .is-manual-grn-srn .grn-form-input-text {
        flex-direction: column;
    }

    .left-div {
        flex: 0 0 40%;
    }

    .left-div .title-head,
    .left-div .title-head p {
        font-size: 0.7rem;
    }

    .right-div p.note {
        font-size: 0.65rem;
        font-weight: 500;
        color: #000;
    }

    .border-label {
        position: relative;
        top: 15px;
        left: 1rem;
    }

    .border-label h6 {
        padding: 2px 4px;
        background: #dbe5ee;
    }

    .multiplePolist {
        overflow: auto;
        border-radius: 7px;
    }

    .select-po-btn {
        font-size: 0.75rem;
        border: 1px solid #fff !important;
        color: #fff;
    }

    .select-po-btn:disabled {
        font-size: 0.75rem;
        border: 1px solid #ccc !important;
        color: #ccc;
    }

    .is-manual-grn-srn .manual-grn-plus-modal .form-input input {
        width: 100%;
    }

    div.chips_input div.inner {
        width: 100%;
        height: 5rem;
        overflow: auto;
        background-color: #ffffff;
        border-radius: 0.3rem;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding: 0.2rem 0.5rem;
        box-sizing: border-box;
        position: relative;
    }

    .display-flex.grn-form-input-text {
        position: relative;
    }

    div.chips_input div.inner input {
        border: none;
        outline: none;
        font-size: 0.7rem;
        position: relative;
        background: transparent;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1;
        color: var(--secondary);
        border: 1px solid #ccc;
    }

    span.chip {
        padding: 0.25rem 0.5rem;
        box-sizing: border-box;
        background: #ffffff;
        border: 1px solid #ccc;
        border-radius: 0.3rem;
        color: #121212;
        font-size: 0.7rem;
        position: relative;
        display: flex;
        justify-content: space-between;
        gap: 0.5rem;
        align-items: center;
        height: 20px;
    }

    span.chip button {
        border: none;
        background: transparent;
        color: transparent;
        position: relative;
        top: 0;
        right: 0;
    }

    span.chip button i {
        cursor: pointer;
        color: #000;
    }

    span.limit {
        float: right;
    }

    .customInvoicePoNumberMain {
        background: #fff;
        border-radius: 5px;
    }

    .item-list {
        font-size: 0.7rem;
        align-items: center;
        gap: 5px;
        padding: 0.6rem 1rem !important;
        height: auto;
        max-height: 14rem;
        overflow-y: auto;
        margin-bottom: 0;
    }

    .item-list li {
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 3px 5px;
        display: flex;
        justify-content: space-between;
        gap: 4px;
        align-items: center;
        margin-bottom: 7px;
    }

    button.btn.btn-danger.delete-button {
        padding: 0px 6px;
    }

    .add-po-number-area input {
        border-radius: 0 0 0 6px;
    }

    .add-po-number-area button {
        border-radius: 0 0 6px 0;
        height: 32px;
    }

    .is-manual-grn-srn .grn-table .po-input_checkbox {
        width: auto;
        height: 15px;
    }

    .grn-form-input-text .display-flex.grn-form-input-text button {
        position: relative;
    }

    .is-manual-grn-srn .grn-form-input-text .display-flex.grn-form-input-text {
        justify-content: space-between;
    }

    .cost-center-modal .modal-dialog {
        max-width: 90%;
    }

    .inner-section {
        overflow: auto;
    }

    .grn-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #ffffff59;
        backdrop-filter: blur(2px);
        display: grid;
        place-items: center;
        z-index: 999999;
    }

    .modal.discountViewModal .modal-dialog {
        max-width: 50%;
    }

    .modal.discountViewModal .modal-dialog .modal-cog-right .form-input select {
        width: 100%;
    }

    .modal.discountViewModal .modal-body .check-box input[type="checkbox"] {
        width: 12px;
        height: 12px;
    }

    .modal.discountViewModal .modal-body .check-box {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    @media (max-width: 575px) {
        #grnInvoicePreviewIfram {
            display: block;
        }

        div.grn-table {
            padding: 50px 0;
        }

        span.error {
            left: 440px;
        }
    }
</style>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<div class="content-wrapper pl-3 pr-3 mb-5 is-manual-grn-srn is-po-grn-srn" style="height: auto !important;">
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

    <!-- <script> 
    // const hiddenInputValue = document.getElementById('hiddenInputPO').value;
    
    // document.getElementById('invoicePoNumber').value = hiddenInputValue;

        // var chipAddition =
        //     '<button aria-label="remove this chip"><i class="fa fa-times"></i></button>';

        // $(document).ready(function() {
        //     updateLimiter();

        //     $("div.chips_input > div.inner").click(function() {
        //         $(this).find("input").focus();
        //     });

        //     $("div.chips_input > div.inner > input").keydown(function(e) {
        //         if (e.which == 13 || e.which == 9 || e.which == 188) {
        //             e.preventDefault();

        //             var value = $(this).val();

        //             if (e.which == 188) {
        //                 value = value.substring(0, value.length - 1);
        //             }

        //             var matches = 0;
        //             $("div.chips_input > div.inner > span").each(function() {
        //                 var other = $(this)
        //                     .html()
        //                     .substring(0, $(this).html().length - chipAddition.length);
        //                 // console.log(other, escapeHtml(value));
        //                 if (
        //                     other.replaceAll(" ", "") == escapeHtml(value.replaceAll(" ", ""))
        //                 ) {
        //                     matches++;
        //                 }
        //             });

        //             if (matches == 0 && value.replace(/\s/g, "").length > 0) {
        //                 if (
        //                     $("div.chips_input").attr("data-limit") !== "undefined" &&
        //                     $("div.chips_input").attr("data-limit") !== false &&
        //                     $("div.chips_input > div.inner > span").length !=
        //                     $("div.chips_input").attr("data-limit")
        //                 ) {
        //                     makeChip($(this).val());
        //                 }
        //             }
        //             $(this).val("");
        //         } else if (e.which == 8 && $(this).val().length == 0) {
        //             $("div.chips_input > div.inner > span:last-of-type").remove();
        //             updateLimiter();
        //         }
        //     });

        //     $(document).on(
        //         "click",
        //         "div.chips_input > div.inner > span > button",
        //         function() {
        //             $(this).parent().remove();
        //             updateLimiter();
        //         }
        //     );
        // });

        // function makeChip(string) {
        //     $("div.chips_input > div.inner > input").before(
        //         '<span class="chip">' + escapeHtml(string) + chipAddition + "</span>"
        //     );
        //     updateLimiter();
        // }

        // function getChips() {
        //     var result = "";
        //     $("div.chips_input > div.inner > span").each(function() {
        //         result = result + "" + $(this).html() + ", ";
        //     });
        //     var result = result.substring(0, result.length - 2); // crop comma
        //     return result;
        // }

        // function updateLimiter() {
        //     inverted = true;
        //     var max = $("div.chips_input").attr("data-limit");
        //     var cur = $("div.chips_input > div.inner > span").length;

        //     if (max !== undefined && max !== false) {
        //         if (inverted) {
        //             cur = max - cur;
        //         }

        //         $("div.chips_input > label > span.limit").html("(" + cur + "/" + max + ")");

        //         // color it when invalid
        //         if ((cur == max && inverted == false) || (cur == 0 && inverted == true)) {
        //             $("div.chips_input > label > span.limit").css("color", "var(--invalid)");
        //         } else {
        //             $("div.chips_input > label > span.limit").css("color", "inherit");
        //         }
        //     }
        // }

        // function escapeHtml(unsafe) {
        //     return unsafe
        //         .replace(/&/g, "&amp;")
        //         .replace(/</g, "&lt;")
        //         .replace(/>/g, "&gt;")
        //         .replace(/"/g, "&quot;")
        //         .replace(/'/g, "&#039;");
        // }
    </script>-->






    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var fileInput = document.getElementById('fileInput');
            var previewContainer = document.getElementById('previewContainer');
            var previewBtn = document.getElementById('iframePreview');
            var previewModalContainer = document.getElementById('previewModalContainer');


            fileInput.addEventListener('change', function() {
                var file = fileInput.files[0];

                previewContainer.innerHTML = '';

                if (file.type.includes('pdf')) {
                    // For PDF files
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var pdfObject = document.createElement('object');
                        pdfObject.data = e.target.result;
                        pdfObject.type = 'application/pdf';
                        pdfObject.width = '100%';
                        pdfObject.height = '500px';
                        previewContainer.appendChild(pdfObject);
                    };
                    reader.readAsDataURL(file);
                } else if (file.type.includes('image')) {
                    var imgElement = document.createElement('img');
                    imgElement.src = URL.createObjectURL(file);
                    imgElement.style.maxWidth = '100%';
                    imgElement.style.height = 'auto';
                    previewContainer.appendChild(imgElement);
                } else {
                    previewContainer.innerHTML = `<span class="unsupprt-error">Unsupported file type</span>`;
                }
            });

            function copyPreviewContentToModal() {
                previewModalContainer.innerHTML = previewContainer.innerHTML;
            }

            previewBtn.addEventListener('click', function() {
                copyPreviewContentToModal();
            });
        });
    </script>

    <?php
    $grnObj = new GrnController();
   
    if (isset($_POST["vendorCode"]) && $_POST["vendorCode"] != "") {
       
       
        // exit();
        // console($_POST);
        $createGrnObj = $grnObj->createManualGrn($_POST);

        
        if ($createGrnObj["status"] == "success") {
            swalAlert($createGrnObj["status"], ucfirst($createGrnObj["status"]), $createGrnObj["message"], BASE_URL . "branch/location/manage-grn-new.php");
        } else {
            swalAlert($createGrnObj["status"], ucfirst($createGrnObj["status"]), $createGrnObj["message"], BASE_URL . "branch/location/manage-grn-new.php");
        }


    }

    if (isset($_GET["view"]) && $_GET["view"] != "" && isset($_GET["type"]) && $_GET["type"] == "grn") {
        require_once("components/grn/manual-grn-view-po-new.php");
    } elseif (isset($_GET["view"]) && $_GET["view"] != "" && isset($_GET["type"]) && $_GET["type"] == "srn") {
        require_once("components/grn/manual-srn-view-po-new.php");
    }
    // elseif(isset($_GET["view"]) && $_GET["view"] == "nopo" && isset($_GET["type"]) && $_GET["type"] == "grn")
    // {
    //     require_once("components/grn/manual-grn-view.php");
    // }
    // elseif (isset($_GET["view"]) && $_GET["view"] == "nopo" && isset($_GET["type"]) && $_GET["type"] == "srn") {
    //     require_once("components/grn/manual-srn-view.php");
    // }
    elseif (isset($_GET["posting"])) {
        require_once("components/grn/posted-grn.php");
    } else {
        require_once("components/grn/pending-grn2.php");
    }
    require_once("../common/footer.php");
    ?>