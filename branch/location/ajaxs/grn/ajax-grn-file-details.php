<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");
$BranchPoObj = new BranchPo();
if (isset($_GET["po"]) && $_GET["po"] != "") {
    global $company_id;
    global $branch_id;
    global $location_id;
    $po = $_GET["po"];


    $poDetailsObj = queryGet("SELECT * FROM `erp_branch_purchase_order` LEFT JOIN `erp_vendor_details` ON erp_vendor_details.vendor_id = erp_branch_purchase_order.vendor_id WHERE erp_branch_purchase_order.po_number = '" . $po . "' AND erp_branch_purchase_order.company_id='" . $company_id . "' AND erp_branch_purchase_order.branch_id='" . $branch_id . "' AND erp_branch_purchase_order.location_id='" . $location_id . "'", false);
    $poDetails = $poDetailsObj["data"] ?? [];



    if ($poDetailsObj["numRows"] == 0) {
    } else {


        $vendorName = $poDetails["trade_name"];


?>
        <ul class="upload-info">
            <li>
                <div class="doc-preview dotted-border-area">
                    <label for="" class="float-label"><?= $vendorName ?></label>
                    <span class="label" data-js-label>No File Selected</span>
                    <button type="button" class="btn btn-transparent preview-btn" id="iframePreview" data-toggle="modal" data-target="#pdfModal">
                        <ion-icon name="eye-outline"></ion-icon>
                    </button>
                </div>
            </li>
        </ul>

        <!-- <div id="previewContainer" class="previewContainer"></div> -->

        <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Invoice Preview</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="height: 600px;">
                        <div id="previewModalContainer" class="previewModalContainer"></div>
                    </div>
                </div>
            </div>
        </div>

<?php

    }
}

?>