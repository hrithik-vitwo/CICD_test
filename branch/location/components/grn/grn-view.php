<style>
    .grn-srn-view .row.grn-create .card {
        min-height: auto;
        height: 350px;
        margin-bottom: 0;
    }
</style>


<?php

$grnObj = new GrnController();

$accountingControllerObj = new Accounting();

?>

<style>
    .corner-ribbon {
        position: absolute;
        top: 11px;
        left: -37px;
        z-index: 999;
        transform: rotate(-42deg);
        background: rgba(255, 3, 3, 0.45);
        color: #fff;
        padding: 5px 39px;
        font-size: 15px;
        font-weight: bold;
        text-align: left;
        letter-spacing: 1.2px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        pointer-events: none;
        text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
        text-wrap: nowrap;
    }

    .corner-ribbon::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        border-width: 6px 6px 0 0;
        border-style: solid;
        border-color: rgba(0, 0, 0, 0.15) transparent transparent transparent;
    }

    .corner-ribbon::after {
        content: "";
        position: absolute;
        bottom: 0;
        right: 0;
        border-width: 0 0 6px 6px;
        border-style: solid;
        border-color: transparent transparent rgba(255, 255, 255, 0.2) transparent;
    }
</style>
<div class="grn-srn-view is-grn-srn-view">
    <section class="content">

        <div id="revdtatus" style="display: none;" class="corner-ribbon">
            Reverse
        </div>

        <div class="container-fluid">

            <?php
            if (isset($_POST["ivPostingFormSubmitBtn"])) {


                $ivPostingData = json_decode(base64_decode($_POST["ivPostingGrnData"]), true);

                // echo '<br>---------------------ivPostingData-------------------';
                // console($ivPostingData);


                $grnPostingJournalId = $ivPostingData["grnDetails"]["grnPostingJournalId"];
                $grnId = $ivPostingData["grnDetails"]["grnId"];



                // $grIrItemsObj = queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $grnPostingJournalId, true);

                // $grIrItems = [];

                // foreach ($grIrItemsObj["data"] as $grIrItem) {

                //     $grIrItems[] = $grIrItem["credit_amount"];
                // }

                // echo '<br>---------------------grIrItems-------------------';
                // console($_POST['grnItemList']);
                // exit;


                $vendorDetailsObj = queryGet('SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=' . $_POST["vendorId"]);

                $vendorParentGlId = $vendorDetailsObj["data"]["parentGlId"] ?? 0;

                $tdsDetails = [];

                $tcsDetails = ["0" => $_POST['totalInvoiceTCS']];

                $postingDate = $_POST['invoicePostingDate'] ?? date("Y-m-d");
                $extra_remark = $POST['extra_remark'] ?? '';


                $symbol = $_POST["adjust_symbol"];
                $roundOffValue = $_POST["roundOffGL"];
                if ($symbol == "add") {
                    $roundOffGL = $roundOffValue;
                } else {
                    $roundOffGL = $roundOffValue * -1;
                }


                $ivPostingInputData = [

                    "BasicDetails" => [

                        "documentNo" => $_POST["documentNo"], // Invoice Doc Number

                        "documentDate" => $_POST["documentDate"], // Invoice number

                        "postingDate" => $postingDate, // current date

                        "grnJournalId" => $grnPostingJournalId,

                        "reference" => $_POST["grnCode"], // grn code

                        "remarks" => "Invoice Posting - " . $_POST["grnCode"] . " " . $extra_remark,

                        "journalEntryReference" => "Purchase"

                    ],

                    "vendorDetails" => [

                        "vendorId" => $_POST["vendorId"],

                        "vendorName" => $_POST["vendorName"],

                        "vendorCode" => $_POST["vendorCode"],

                        "parentGlId" => $vendorParentGlId

                    ],

                    "grIrItems" => $_POST['grnItemList'],

                    "taxDetails" => [

                        "cgst" => $_POST["totalInvoiceCGST"],

                        "sgst" => $_POST["totalInvoiceSGST"],

                        "igst" => $_POST["totalInvoiceIGST"]

                    ],
                    "tcsDetails" => $tcsDetails,
                    "tdsDetails" => $tdsDetails,
                    "roundOffValue" => $roundOffGL

                ];

                $createInvObj = $grnObj->createInvoice($_POST);


                if ($createInvObj["status"] == "success") {
                    $ivPostingObj = $accountingControllerObj->grnIvAccountingPosting($ivPostingInputData, "grniv", $createInvObj['grnIVId']);

                    $queryObj = queryUpdate('UPDATE `erp_grninvoice` SET `ivPostingJournalId`=' . $ivPostingObj["journalId"] . ' WHERE `grnIVId`=' . $createInvObj['grnIVId']);


                    swalAlert($createInvObj["status"], ucfirst($createInvObj["status"]), $createInvObj["message"], BASE_URL . "branch/location/manage-vendor-invoice.php");
                } else {
                    swalAlert($createInvObj["status"], ucfirst($createInvObj["status"]), $createInvObj["message"]);
                }

                // console($createInvObj);
                // console($createInvObj);

                // exit();

            }

            $getGrnDetailsObj = $grnObj->getGrnDetails($_GET["view"]);

            if ($getGrnDetailsObj["numRows"] == 1) {

                //console($getGrnDetailsObj);

                $grnDetails = $getGrnDetailsObj["data"];
                $grnInvoice = queryGet('SELECT * FROM `erp_grninvoice` WHERE (`companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' AND `grnId`=' . $grnDetails['grnId'] . ') ORDER BY `grnIvId` DESC LIMIT 1');
                $grnItemDetailsObj = $grnObj->getGrnItemDetails($_GET["view"]);

                global $branch_id;

                $branchDeailsObj = queryGet("SELECT * FROM " . ERP_BRANCHES . " WHERE `branch_id`=" . $branch_id);
                if ($branchDeailsObj["status"] == "success") {
                    $branchDeails = $branchDeailsObj["data"];
                    $loginBranchGstin = $branchDeails["branch_gstin"];
                } else {
                    return [
                        "status" => "warning",
                        "message" => "Branch not found!",
                        "file" => $filename
                    ];
                }
            }

            //console($grnItemDetailsObj);

            ?>

            <form action="" method="POST" id="addNewGRNForm">

                <input type="hidden" name="ivPostingGrnId" value="<?= $grnDetails["grnId"] ?>">

                <div class="row">

                    <div class="col-lg-6 col-md-6 col-sm-12 col-12">

                        <input type="hidden" name="grnCode" value="<?= $grnDetails["grnCode"] ?? "" ?>">
                        <input type="hidden" name="documentNo" value="<?= $grnDetails["vendorDocumentNo"] ?? "" ?>">
                        <input type="hidden" name="documentDate" value="<?= $grnDetails["vendorDocumentDate"] ?? "" ?>">
                        <input type="hidden" name="vendorDocumentFile" value="<?= $grnDetails["vendorDocumentFile"] ?? "" ?>">
                        <input type="hidden" name="vendorGstinStateName" value="<?= $grnDetails["vendorGstinStateName"] ?? "" . '(' . substr($grnDetails["vendorGstin"], 0) . ')'; ?>">
                        <input type="hidden" name="locationGstinStateName" value="<?= $grnDetails["locationGstinStateName"] ?? "" . '(' . substr($loginBranchGstin, 0) . ')' ?>">
                        <input type="hidden" name="invoicePostingDate" value="<?= $grnDetails["postingDate"] ?>" required>
                        <input type="hidden" name="invoiceDueDate" value="<?= $grnDetails["dueDate"] ?>" required>
                        <input type="hidden" name="invoicePoNumber" value="<?= $grnDetails["grnPoNumber"] ?>">
                        <input type="hidden" name="grnType" value="grn">
                        <input type="hidden" name="vendorCode" id="invoiceVendorCodeInput" value="<?= $grnDetails["vendorCode"] ?>" class="form-control" />
                        <input type="hidden" name="vendorId" id="invoiceVendorIdInput" value="<?= $grnDetails["vendorId"] ?>" class="form-control" />
                        <input type="hidden" name="vendorName" value="<?= $grnDetails["vendorName"] ?>" class="form-control" />
                        <input type="hidden" name="vendorGstin" value="<?= $grnDetails["vendorGstin"] ?>" class="form-control" />
                        <input type="hidden" name="totalInvoiceSubTotal" value="<?= $grnDetails["grnSubTotal"] ?>">
                        <input type="hidden" name="totalInvoiceTotal" id="totalInvoiceTotal" value="<?= $grnDetails["grnTotalAmount"] ?>">
                        <input type="hidden" name="grnApprovedStatus" value="<?= $grnDetails["grnApprovedStatus"] ?>">
                        <input type="hidden" name="id" value="<?= $grnDetails["grnId"] ?>">
                        <input type="hidden" name="roundvalue" id="roundvalue" value="<?= $grnDetails["roundvalue"] ?>">
                        <?php
                        $roundvalue = $grnDetails["roundvalue"];
                        ?>


                        <div class="line-border-area">
                            <h6>Document Info</h6>
                            <div class="doc-details">
                                <div class="form-input">
                                    <label for="">GRN No.</label>
                                    <p><?= $grnDetails["grnCode"] ?></p>
                                </div>
                                <div class="form-input">
                                    <label for="">Document No.</label>
                                    <p><?= $grnDetails["vendorDocumentNo"] ?></p>
                                </div>
                                <div class="form-input">
                                    <label for="">Document Date.</label>
                                    <?php
                                    $originalvendorDocumentDate = $grnDetails["vendorDocumentDate"];
                                    $vendorDocumentDatetimestamp = strtotime($originalvendorDocumentDate);

                                    ?>
                                    <p><?= date("d-m-Y", $vendorDocumentDatetimestamp) ?></p>
                                </div>
                                <div class="form-input">
                                    <label for="">Posting Date.</label>
                                    <?php
                                    $check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
                                    $check_var_data = $check_var_sql['data'];
                                    // console($_SESSION);
                                    // // console($check_var_sql);
                                    // console($check_var_sql);
                                    $max = $check_var_data['month_end'];
                                    $min = $check_var_data['month_start'];
                                    ?>

                                    &nbsp;
                                    <?php if ($min < $originalvendorDocumentDate) {
                                        // $min = $originalvendorDocumentDate;
                                    } ?>
                                    <p><input type="date" id="invoicePostingDateId" name="invoicePostingDate" class="form-control" value="<?= $grnDetails["postingDate"] ?>" min="<?= $min ?>" max="<?= $max ?>" required></p>
                                    <p class="text-danger text-xs" id="postdatelabel"></p>

                                </div>
                                <div class="form-input">
                                    <label for="">Due Date</label>
                                    <?php
                                    $originaldueDate = $grnDetails["dueDate"];
                                    $dueDatetimestamp = strtotime($originaldueDate);

                                    ?>

                                    <p><?= date('d-m-Y', $dueDatetimestamp) ?></p>
                                </div>

                                <?php

                                if ($grnDetails["grnPoNumber"] != "") {

                                ?>
                                    <div class="form-input">
                                        <label for="">PO No.</label>
                                        <p><?= $grnDetails["grnPoNumber"] ?></p>
                                    </div>

                                <?php
                                }
                                ?>

                            </div>
                            <div class="form-input">
                                <label for="">Remark</label>
                                <textarea name="extra_remark" id="extra_remark" class="form-control" rows="2"></textarea>
                            </div>
                        </div>


                        <div class="line-border-area">
                            <h6>Vendor Info</h6>
                            <div class="doc-details">
                                <div class="form-input">
                                    <label for="">Code</label>
                                    <p id="invoiceVendorCodeSpan"><?= $grnDetails["vendorCode"] ?? "" ?></p>
                                </div>
                                <div class="form-input">
                                    <label for="">Name</label>
                                    <p><?= $grnDetails["vendorName"] ?? "" ?></p>
                                </div>
                                <div class="form-input">
                                    <label for="">GSTIN</label>
                                    <p><?= $grnDetails["vendorGstin"] ?? "" ?></p>
                                </div>
                                <div class="form-input">
                                    <label for="">State</label>
                                    <p><?= $grnDetails["vendorGstinStateName"] ?? "" ?></p>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                        <div class="line-border-area">
                            <h6>Upload Bill Status</h6>
                            <?php
                            if (isset($grnDetails["vendorDocumentFile"]) && $grnDetails["vendorDocumentFile"] != "" && $grnDetails["vendorDocumentFile"] != NULL) {
                                // if(file_exists(COMP_STORAGE_URL ."/grn-invoice/".$grnDetails["vendorDocumentFile"])){

                            ?>

                                <iframe src='<?= COMP_STORAGE_URL ?>/grn-invoice/<?= $grnDetails["vendorDocumentFile"] ?? "" ?>' id="grnInvoicePreviewIfram" width="100%" height="220">

                                    <p>This browser does not support PDF!</p>

                                </iframe>

                                <div class="preview-btn-space">

                                    <button type="button" class="btn btn-primary preview-btn" id="iframePreview" data-toggle="modal" data-target="#exampleModalCenter">

                                        Preview

                                    </button>

                                </div>

                            <?php

                                // }else{

                                //     echo "Bill file not found";

                                // }
                            } else { ?>


                                <div class='bill-block'>
                                    <img src='<?= BASE_URL ?>public/assets/gif/no-transaction.gif' width='130' alt='no-data-found'>
                                    <p class='font-bold'> No Bill Found </p>
                                </div>
                            <?php
                            }

                            ?>

                            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

                                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5 class="modal-title" id="exampleModalLongTitle">Invoice Preview</h5>

                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                                                <span aria-hidden="true">×</span>

                                            </button>

                                        </div>

                                        <div class="modal-body" style="height: 600px;">

                                            <div id="iframeHolder" class="iframeholder"></div>

                                        </div>

                                    </div>

                                </div>

                            </div>


                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="line-border-area grn-pending">
                            <div class="item-list">
                                <table class="grn-table">
                                    <thead>
                                        <tr>
                                            <th>Sl No.</th>
                                            <th>Item Name</th>
                                            <th>Internal Code</th>
                                            <th>Item HSN</th>
                                            <th>St. Loc.</th>
                                            <th>Derived Qty</th>
                                            <th>Invoice Qty</th>
                                            <th>Received Qty</th>
                                            <th>Unit Price</th>
                                            <th>Basic Amount</th>
                                            <th>CGST</th>
                                            <th>SGST</th>
                                            <th>IGST</th>
                                            <th>TDS</th>
                                            <!-- <th>Total Amount</th> -->
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTable">

                                        <?php



                                        $formEncodedData = base64_encode(json_encode([

                                            "grnDetails" => $grnDetails,

                                            "grnItemDetails" => $grnItemDetailsObj["data"]

                                        ]));


                                        // console($grnItemDetailsObj["data"]);

                                        echo '<input type="hidden" name="ivPostingGrnData" value="' . $formEncodedData . '" />';







                                        $sl = 0;
                                        $total_tds = 0;

                                        foreach ($grnItemDetailsObj["data"] as $oneItemDetails) {

                                            $sl += 1;

                                            $basic_amount = $oneItemDetails["unitPrice"] * $oneItemDetails["receivedQty"];
                                            $tds = $oneItemDetails["tds"] ?? 0;
                                            $tds_value = $basic_amount * $tds / 100;
                                            $total_tds = $tds_value;
                                            if ($getGrnDetailsObj["data"]["iv_status"] == 1) {
                                                $total_tds = $grnInvoice['data']["grnTotalTds"];
                                            }
                                            $total_amount = $basic_amount + $oneItemDetails["cgst"] + $oneItemDetails["sgst"] + $oneItemDetails["igst"] - $tds_value;

                                        ?>

                                            <tr id="grnItemRowTr_<?= $sl ?>">

                                                <input type="hidden" id="internalItemId_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemId]" value="<?= $oneItemDetails["goodId"] ?>" />
                                                <input type="hidden" id="internalItemCode_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemCode]" value="<?= $oneItemDetails["goodCode"] ?>" />
                                                <input type="hidden" id="internalItemHsn_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemHsn]" value="<?= $oneItemDetails["goodHsn"] ?>" />
                                                <input type="hidden" name="grnItemList[<?= $sl ?>][itemName]" value="<?= $oneItemDetails["goodName"] ?>" />
                                                <input type="hidden" name="grnItemList[<?= $sl ?>][itemQty]" value="<?= $oneItemDetails["itemStocksQty"] ?>" />
                                                <input type="hidden" class="ItemInvoiceTotalPrice" name="grnItemList[<?= $sl ?>][baseAmount]" id="grnItemBaseInput_<?= $sl ?>" value="<?= $oneItemDetails["unitPrice"] * $oneItemDetails["receivedQty"] ?>" />
                                                <input type="hidden" name="grnItemList[<?= $sl ?>][itemUnitPrice]" value="<?= $oneItemDetails["unitPrice"] ?>" />
                                                <input type="hidden" name="grnItemList[<?= $sl ?>][itemTotalPrice]" value="<?= $oneItemDetails["totalAmount"] ?>" />
                                                <input type="hidden" name="grnItemList[<?= $sl ?>][itemStockQty]" value="<?= $oneItemDetails["itemStocksQty"] ?>" class="form-control text-xs w-50" \>
                                                <input type="hidden" name="grnItemList[<?= $sl ?>][itemReceivedQty]" value="<?= $oneItemDetails["receivedQty"] ?>" id="grnItemReceivedQtyTdInput_<?= $sl ?>" class="form-control text-xs">
                                                <input type="hidden" name="grnItemList[<?= $sl ?>][itemStorageLocationId]" value="<?= $oneItemDetails["itemStorageLocation"] ?>" />
                                                <input type="hidden" name="grnItemList[<?= $sl ?>][itemTotalTDS]" id="grnItemTDSValue_<?= $sl ?>" value="<?= $tds_value ?>" class="ItemTotalTds" />
                                                <input type="hidden" name="grnItemList[<?= $sl ?>][itemUOM]" id="grnItemUom_<?= $sl ?>" value="<?= $oneItemDetails["itemUOM"] ?>" />





                                                <td><?= $sl ?></td>

                                                <td>
                                                    <p class="pre-normal good-name"><?= ucfirst($oneItemDetails["goodName"]) ?></p>
                                                </td>

                                                <td><?= $oneItemDetails["goodCode"] ?></td>

                                                <td><?= $oneItemDetails["goodHsn"] ?></td>

                                                <td><?= $oneItemDetails["storage_location_code"] ?></td>

                                                <td><?= inputQuantity($oneItemDetails["itemStocksQty"]) . " " . $oneItemDetails["itemUOM"] ?></td>

                                                <td><?= inputQuantity($oneItemDetails["goodQty"]) . " " . $oneItemDetails["itemUOM"] ?></td>

                                                <td><?= inputQuantity($oneItemDetails["receivedQty"]) . " " . $oneItemDetails["itemUOM"] ?></td>

                                                <td class="text-right"><?= inputValue($oneItemDetails["unitPrice"]) ?></td>

                                                <td class="text-right" id="grnItemBaseUnit_<?= $sl ?>"><?= inputValue(($oneItemDetails["unitPrice"] * $oneItemDetails["receivedQty"])) ?></td>

                                                <td class="text-right">

                                                    <div class="form-input">
                                                        <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemCGST]" value="<?= inputValue($oneItemDetails["cgst"]) ?>" id="grnItemUnitCgstInput_<?= $sl ?>" class="form-control text-xs w-auto itemCgst inputAmountClass">
                                                    </div>
                                                </td>

                                                <td class="text-right">
                                                    <div class="form-input">
                                                        <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemSGST]" value="<?= inputValue($oneItemDetails["sgst"]) ?>" id="grnItemUnitSgstInput_<?= $sl ?>" class="form-control text-xs w-auto itemSgst inputAmountClass">
                                                    </div>
                                                </td>

                                                <td class="text-right">
                                                    <div class="form-input">
                                                        <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemIGST]" value="<?= inputValue($oneItemDetails["igst"]) ?>" id="grnItemUnitIgstInput_<?= $sl ?>" class="form-control text-xs w-auto itemIgst inputAmountClass">
                                                    </div>
                                                </td>

                                                <td class="text-right">
                                                    <div class="form-input d-flex gap-2">

                                                        <?php
                                                        if ($oneItemDetails["goodstype"] == "service") {
                                                        ?>
                                                            <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemTDS]" value="<?= inputValue($oneItemDetails["tds"]) ?>" id="grnItemUnitTdsInput_<?= $sl ?>" class="form-control text-xs w-auto itemTds inputAmountClass">
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemTDS]" value="<?= inputValue($oneItemDetails["tds"]) ?>" id="grnItemUnitTdsInput_<?= $sl ?>" class="form-control text-xs w-auto itemTds inputAmountClass">
                                                        <?php
                                                        }
                                                        ?>
                                                        <p class="text-xs">%</p>
                                                    </div>
                                                </td>

                                                <span style="display: none" class="text-right" id="grnItemUnitTDTotal_<?= $sl ?>"><?= inputValue($total_amount) ?></span>

                                            </tr>

                                        <?php

                                        }

                                        ?>

                                    </tbody>

                                </table>

                            </div>


                            <div class="item-total-card">

                                <table class="total-calculate-table">

                                    <tbody>

                                        <tr class="itemTotals">

                                            <td style="background: none;">Sub Total</td>

                                            <td class="text-right" style="background: none;"><?= inputValue($grnDetails["grnSubTotal"]) ?></td>



                                        </tr>

                                        <tr class="itemTotals">

                                            <td style="background: none;">Total CGST</td>

                                            <td class="text-right" style="background: none;">
                                                <input type="number" step="any" name="totalInvoiceCGST" value="<?= inputValue($grnDetails["grnTotalCgst"]) ?>" id="totalInvoiceCGST" class="form-control text-xs itemUnitPrice" readonly>
                                            </td>

                                        </tr>

                                        <tr class="itemTotals">

                                            <td style="background: none;">Total SGST</td>

                                            <td class="text-right" style="background: none;">
                                                <input type="number" step="any" name="totalInvoiceSGST" value="<?= inputValue($grnDetails["grnTotalSgst"]) ?? 0 ?>" id="totalInvoiceSGST" class="form-control text-xs itemUnitPrice" readonly>
                                            </td>

                                        </tr>

                                        <tr class="itemTotals">

                                            <td style="background: none;">Total IGST</td>

                                            <td class="text-right" style="background: none;">
                                                <input type="number" step="any" name="totalInvoiceIGST" value="<?= inputValue($grnDetails["grnTotalIgst"]) ?? 0 ?>" id="totalInvoiceIGST" class="form-control text-xs itemUnitPrice" readonly>
                                            </td>

                                        </tr>

                                        <tr class="itemTotals">

                                            <td style="background: none;">Total TDS</td>

                                            <td class="text-right" style="background: none;">
                                                <input type="number" step="any" name="totalInvoiceTDS" value="<?= inputValue($total_tds) ?>" id="totalInvoiceTDS" class="form-control text-xs itemUnitPrice" readonly>
                                            </td>

                                        </tr>

                                        <tr class="itemTotals">

                                            <td style="background: none;">TCS</td>
                                            <?php if ($getGrnDetailsObj["data"]["iv_status"] == 0 && $getGrnDetailsObj["data"]["grnStatus"] == "active") {
                                            ?>
                                                <td class="text-right" style="background: none;">
                                                    <input type="number" step="any" name="totalInvoiceTCS" value="<?= inputValue(0) ?>" id="totalInvoiceTCS" class="form-control text-xs itemTCS inputAmountClass">
                                                </td>
                                            <?php } else { ?>
                                                <td class="text-right" style="background: none;">
                                                    <input type="number" step="any" name="totalInvoiceTCS" readonly value="<?= inputValue(($grnInvoice['data']["grnTotalTcs"])) ?? 0 ?>" id="totalInvoiceTCS" class="form-control text-xs itemTCS inputAmountClass ">
                                                </td>
                                            <?php } ?>

                                        </tr>

                                        <tr class="itemTotals">

                                            <td class="font-bold" style="background: none; border: 0;">Total Amount</td>
                                            <?php
                                            if ($getGrnDetailsObj["data"]["iv_status"] == 0 && $getGrnDetailsObj["data"]["grnStatus"] == "active") {
                                            ?>
                                                <td class="text-right font-bold" id="tdGrandTotal" style="background: none; border: 0;s"><?= inputValue($grnDetails["grnTotalAmount"] - $grnDetails["roundvalue"]) ?? 0 ?></td>
                                            <?php } else { ?>
                                                <td class="text-right font-bold" id="tdGrandTotal" style="background: none; border: 0;s"><?= inputValue($grnInvoice['data']["grnSubTotal"] + $grnInvoice['data']["grnTotalCgst"] + $grnInvoice['data']["grnTotalSgst"] + $grnInvoice['data']["grnTotalIgst"] - $grnInvoice['data']["grnTotalTds"] + $grnInvoice['data']["grnTotalTcs"]) ?? 0 ?></td>


                                            <?php } ?>
                                        </tr>


                                        <tr class="itemTotals">

                                            <td class="font-bold" style="background: none; border: 0;">Round - Off</td>

                                            <td class="text-right font-bold" id="" style="background: none; border: 0;">
                                                <div class="adjust-currency d-flex gap-3 justify-content-end">
                                                    <?php
                                                    if ($getGrnDetailsObj["data"]["iv_status"] == 0 && $getGrnDetailsObj["data"]["grnStatus"] == "active") {
                                                    ?>
                                                        <select id="round_sign" name="adjust_symbol" class="form-control text-center">
                                                            <option value="add">+</option>
                                                            <option value="sub">-</option>
                                                        </select>

                                                        <input type="number" step="any" name="roundOffGL" id="round_value" value="0.00" class="form-control text-center">

                                                    <?php
                                                    } else {
                                                    ?>
                                                        <input type="number" step="any" name="roundOffGL" id="round_value" disabled readonly value="<?= inputValue(($grnInvoice['data']["roundoff"])) ?? 0 ?>" class="form-control text-center">
                                                    <?php
                                                    }
                                                    ?>
                                                </div>

                                            </td>


                                        </tr>
                                        <?php
                                        if ($getGrnDetailsObj["data"]["iv_status"] == 0 && $getGrnDetailsObj["data"]["grnStatus"] == "active") {
                                        ?>
                                            <tr class="itemTotals">

                                                <td class="font-bold" style="background: none; border: 0;">Total Round - Off</td>

                                                <td class="text-right font-bold" id="" style="background: none; border: 0;">
                                                    <span id="final_roundoff_span"><?= $roundvalue ?></span>
                                                    <input type="hidden" name="final_roundoff" id="final_roundoff" value="<?= $roundvalue ?>">

                                                </td>


                                            </tr>
                                        <?php
                                        }
                                        ?>

                                        <tr class="itemTotals">
                                            <td class="font-bold" style="background: none; border: 0;">Adjusted Amount</td>
                                            <?php
                                            if ($getGrnDetailsObj["data"]["iv_status"] == 0 && $getGrnDetailsObj["data"]["grnStatus"] == "active") {
                                            ?>
                                                <td class="text-right text-success font-bold" id="tdAdjustedTotal" style="background: none; border: 0;"><?= inputValue($grnDetails["grnTotalAmount"]) ?? 0 ?>
                                                <?php } else { ?>
                                                <td class="text-right text-success font-bold" id="tdAdjustedTotal" style="background: none; border: 0;"><?= inputValue($grnInvoice['data']["grnTotalAmount"]) ?></td>

                                            <?php } ?>
                                        </tr>
                                        <input type="hidden" id="tdAdjustedTotalval" name="tdAdjustedTotalval" value="0.0"></td>

                                        <?php
                                        $check = "SELECT `use_type` 
                                                FROM `erp_branch_purchase_order`
                                                WHERE 
                                                    `po_number` = '" . $grnDetails['grnPoNumber'] . "' AND 
                                                    `company_id` = '" . $grnDetails['companyId'] . "' AND 
                                                    `branch_id` = '" . $grnDetails['branchId'] . "' AND 
                                                    `location_id` = '" . $grnDetails['locationId'] . "'";
                                        $res = queryGet($check);
                                        if ($getGrnDetailsObj["data"]["iv_status"] == 0 && $getGrnDetailsObj["data"]["grnStatus"] == "active") {


                                            if ($res['data']['use_type'] == "asset") {
                                        ?>

                                                <tr class="itemTotals">
                                                    <td class="font-bold" style="background: none; border: 0; display: flex; align-items: center;">
                                                        <input type="checkbox" name="itc" id="itc" style="margin-right: 8px;">
                                                        Don't want to claim ITC?
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else {

                                            if ($res['data']['use_type'] == "asset") {
                                            ?>

                                                <tr class="itemTotals">
                                                    <td class="font-bold" style="background: none; border: 0; display: flex; align-items: center;">
                                                        <input type="checkbox" readonly <?php if ($grnDetails['itc'] == 1) echo "checked"; ?> name="itc" id="itc" style="margin-right: 8px;">
                                                        Don't want to claim ITC?
                                                    </td>
                                                </tr>

                                        <?php
                                            }
                                        } ?>

                                    </tbody>

                                </table>



                            </div>

                        </div>

                        <?php

                        if ($getGrnDetailsObj["data"]["iv_status"] == 0 && $getGrnDetailsObj["data"]["grnStatus"] == "active") {

                        ?>

                            <div class="row p-0 m-0">

                                <p class="text-right">

                                    <!-- <input type="text" name="ivPostingDate" id=""> <input type="text" name="invoicePostingDate" id=""> -->

                                    <button type="submit" name="ivPostingFormSubmitBtn" class="btn btn-primary mt-3 mb-5">IV Posting</button>

                                </p>

                            </div>

                        <?php

                        }

                        ?>

                    </div>

                </div>

            </form>

        </div>

    </section>

</div>


<?php if ($getGrnDetailsObj["data"]["iv_status"] != 1 && $getGrnDetailsObj["data"]["grnStatus"] == "active") { ?>
    <script>
        $(document).ready(function() {
            calculateGrandTotalAmount();

            function updateAdjustedTotal() {
                let total_value = helperAmount($("#totalInvoiceTotal").val()) || 0; // Default to 0 if empty
                let roundValue = helperAmount($("#round_value").val()) || 0; // Default to 0 if empty
                let sign = $('#round_sign').val();
                let newValue = (sign === "add") ? roundValue : (-1) * (roundValue);
                let roundvaluee = helperAmount($("#roundvalue").val());
                let final_value = (sign === "add") ?
                    total_value + roundValue :
                    total_value - roundValue;

                final_value = final_value + roundvaluee;

                $("#final_roundoff").val(inputValue(newValue + roundvaluee));
                $("#final_roundoff_span").html(inputValue(newValue + roundvaluee));
                $("#tdAdjustedTotal").html(inputValue(final_value)); // Update adjusted total
                $("#tdAdjustedTotalval").val(inputValue(final_value)); // Update adjusted total

            }

            $(document).on("keyup", "#round_value", function() {
                updateAdjustedTotal();
            });
            $(document).on("change", "#round_sign", function() {
                updateAdjustedTotal();
            });

            $(document).on("keyup", ".itemCgst", function() {
                let rowNo = ($(this).attr("id")).split("_")[1];
                calculateOneItemAmounts(rowNo);
            });
            $(document).on("keyup", ".itemSgst", function() {
                let rowNo = ($(this).attr("id")).split("_")[1];
                calculateOneItemAmounts(rowNo);
            });

            $(document).on("keyup", ".itemIgst", function() {
                let rowNo = ($(this).attr("id")).split("_")[1];
                calculateOneItemAmounts(rowNo);
            });

            $(document).on("keyup", ".itemTds", function() {
                let rowNo = ($(this).attr("id")).split("_")[1];
                calculateOneItemAmounts(rowNo);
            });


            function calculateOneItemAmounts(rowNo) {
                let basicPrice = (helperAmount($(`#grnItemBaseInput_${rowNo}`).val()) > 0) ? helperAmount($(`#grnItemBaseInput_${rowNo}`).val()) : 0;
                let cgst = (helperAmount($(`#grnItemUnitCgstInput_${rowNo}`).val()) > 0) ? helperAmount($(`#grnItemUnitCgstInput_${rowNo}`).val()) : 0;
                let sgst = (helperAmount($(`#grnItemUnitSgstInput_${rowNo}`).val()) > 0) ? helperAmount($(`#grnItemUnitSgstInput_${rowNo}`).val()) : 0;
                let igst = (helperAmount($(`#grnItemUnitIgstInput_${rowNo}`).val()) > 0) ? helperAmount($(`#grnItemUnitIgstInput_${rowNo}`).val()) : 0;
                let tds = (helperAmount($(`#grnItemUnitTdsInput_${rowNo}`).val()) > 0) ? helperAmount($(`#grnItemUnitTdsInput_${rowNo}`).val()) : 0;

                let tds_value = basicPrice * (tds / 100);

                let totalItemPrice = basicPrice + cgst + sgst + igst - tds_value;

                console.log(totalItemPrice, cgst, sgst, igst, tds_value);

                $(`#grnItemUnitTDTotal_${rowNo}`).html(inputValue(totalItemPrice))
                $(`#grnItemTDSValue_${rowNo}`).val(inputValue(tds_value));



                calculateGrandTotalAmount();
            }

            function calculateGrandTotalAmount() {
                let totalAmount = 0;
                let grandSubTotalAmt = 0;
                let TotalCGSt = 0;
                let TotalSGSt = 0;
                let TotalIGSt = 0;
                let TotalTds = 0;


                $(".ItemInvoiceTotalPrice").each(function() {
                    grandSubTotalAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
                });

                $(".itemCgst").each(function() {
                    TotalCGSt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
                });

                $(".itemSgst").each(function() {
                    TotalSGSt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
                });

                $(".itemIgst").each(function() {
                    TotalIGSt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
                });

                $(".ItemTotalTds").each(function() {
                    TotalTds += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
                });

                $(`#totalInvoiceCGST`).val(inputValue(TotalCGSt));
                $(`#totalInvoiceSGST`).val(inputValue(TotalSGSt));
                $(`#totalInvoiceIGST`).val(inputValue(TotalIGSt));
                $(`#totalInvoiceTDS`).val(inputValue(TotalTds));

                let ToTalcgst = (helperAmount($(`#totalInvoiceCGST`).val()) > 0) ? helperAmount($(`#totalInvoiceCGST`).val()) : 0;
                let ToTalsgst = (helperAmount($(`#totalInvoiceSGST`).val()) > 0) ? helperAmount($(`#totalInvoiceSGST`).val()) : 0;
                let ToTaligst = (helperAmount($(`#totalInvoiceIGST`).val()) > 0) ? helperAmount($(`#totalInvoiceIGST`).val()) : 0;
                let ToTaltcs = (helperAmount($(`#totalInvoiceTCS`).val()) > 0) ? helperAmount($(`#totalInvoiceTCS`).val()) : 0;
                let ToTalinvTds = (helperAmount($(`#totalInvoiceTDS`).val()) > 0) ? helperAmount($(`#totalInvoiceTDS`).val()) : 0;

                totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst + ToTaltcs - ToTalinvTds;


                $("#totalInvoiceTotal").val(inputValue(totalAmount));
                $("#tdGrandTotal").html(inputValue(totalAmount));
                updateAdjustedTotal();
            }

            $(document).on("keyup", "#totalInvoiceCGST", function() {
                let grandSubTotalAmt = 0;
                $(".ItemInvoiceTotalPrice").each(function() {
                    grandSubTotalAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
                });
                let ToTalcgst = (helperAmount($(`#totalInvoiceCGST`).val()) > 0) ? helperAmount($(`#totalInvoiceCGST`).val()) : 0;
                let ToTalsgst = (helperAmount($(`#totalInvoiceSGST`).val()) > 0) ? helperAmount($(`#totalInvoiceSGST`).val()) : 0;
                let ToTaligst = (helperAmount($(`#totalInvoiceIGST`).val()) > 0) ? helperAmount($(`#totalInvoiceIGST`).val()) : 0;
                let ToTaltcs = (helperAmount($(`#totalInvoiceTCS`).val()) > 0) ? helperAmount($(`#totalInvoiceTCS`).val()) : 0;
                let ToTalinvTds = (helperAmount($(`#totalInvoiceTDS`).val()) > 0) ? helperAmount($(`#totalInvoiceTDS`).val()) : 0;

                let totalAmount = 0;

                totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst + ToTaltcs - ToTalinvTds;

                $("#totalInvoiceTotal").val(inputValue(totalAmount));
                $("#tdGrandTotal").html(inputValue(totalAmount));
                updateAdjustedTotal();
            });

            $(document).on("keyup", "#totalInvoiceSGST", function() {
                let grandSubTotalAmt = 0;
                $(".ItemInvoiceTotalPrice").each(function() {
                    grandSubTotalAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
                });
                let ToTalcgst = (helperAmount($(`#totalInvoiceCGST`).val()) > 0) ? helperAmount($(`#totalInvoiceCGST`).val()) : 0;
                let ToTalsgst = (helperAmount($(`#totalInvoiceSGST`).val()) > 0) ? helperAmount($(`#totalInvoiceSGST`).val()) : 0;
                let ToTaligst = (helperAmount($(`#totalInvoiceIGST`).val()) > 0) ? helperAmount($(`#totalInvoiceIGST`).val()) : 0;
                let ToTaltcs = (helperAmount($(`#totalInvoiceTCS`).val()) > 0) ? helperAmount($(`#totalInvoiceTCS`).val()) : 0;
                let ToTalinvTds = (helperAmount($(`#totalInvoiceTDS`).val()) > 0) ? helperAmount($(`#totalInvoiceTDS`).val()) : 0;

                let totalAmount = 0;

                totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst + ToTaltcs - ToTalinvTds;

                $("#totalInvoiceTotal").val(inputValue(totalAmount));
                $("#tdGrandTotal").html(inputValue(totalAmount));
                updateAdjustedTotal();
            });

            $(document).on("keyup", "#totalInvoiceIGST", function() {
                let grandSubTotalAmt = 0;
                $(".ItemInvoiceTotalPrice").each(function() {
                    grandSubTotalAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
                });
                let ToTalcgst = (helperAmount($(`#totalInvoiceCGST`).val()) > 0) ? helperAmount($(`#totalInvoiceCGST`).val()) : 0;
                let ToTalsgst = (helperAmount($(`#totalInvoiceSGST`).val()) > 0) ? helperAmount($(`#totalInvoiceSGST`).val()) : 0;
                let ToTaligst = (helperAmount($(`#totalInvoiceIGST`).val()) > 0) ? helperAmount($(`#totalInvoiceIGST`).val()) : 0;
                let ToTaltcs = (helperAmount($(`#totalInvoiceTCS`).val()) > 0) ? helperAmount($(`#totalInvoiceTCS`).val()) : 0;
                let ToTalinvTds = (helperAmount($(`#totalInvoiceTDS`).val()) > 0) ? helperAmount($(`#totalInvoiceTDS`).val()) : 0;

                let totalAmount = 0;

                totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst + ToTaltcs - ToTalinvTds;

                $("#totalInvoiceTotal").val(inputValue(totalAmount));
                $("#tdGrandTotal").html(inputValue(totalAmount));
                updateAdjustedTotal()

            });


            $(document).on("keyup", "#totalInvoiceTCS", function() {
                console.log("Hello");

                let grandSubTotalAmt = 0;
                $(".ItemInvoiceTotalPrice").each(function() {
                    grandSubTotalAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
                });
                let ToTalcgst = (helperAmount($(`#totalInvoiceCGST`).val()) > 0) ? helperAmount($(`#totalInvoiceCGST`).val()) : 0;
                let ToTalsgst = (helperAmount($(`#totalInvoiceSGST`).val()) > 0) ? helperAmount($(`#totalInvoiceSGST`).val()) : 0;
                let ToTaligst = (helperAmount($(`#totalInvoiceIGST`).val()) > 0) ? helperAmount($(`#totalInvoiceIGST`).val()) : 0;
                let ToTaltcs = (helperAmount($(`#totalInvoiceTCS`).val()) > 0) ? helperAmount($(`#totalInvoiceTCS`).val()) : 0;
                let ToTalinvTds = (helperAmount($(`#totalInvoiceTDS`).val()) > 0) ? helperAmount($(`#totalInvoiceTDS`).val()) : 0;

                let totalAmount = 0;

                totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst + ToTaltcs - ToTalinvTds;

                console.log(totalAmount);


                $("#totalInvoiceTotal").val(inputValue(totalAmount));
                $("#tdGrandTotal").html(inputValue(totalAmount))
                // updateAdjustedTotal()
                calculateGrandTotalAmount();

            });

        });
    </script>
<?php } ?>
<script>
    $(function() {

        $('#iframePreview').click(function() {
            if (!$('#iframe').length) {
                var src = $('iframe#grnInvoicePreviewIfram').attr('src');
                $('#iframeHolder').html(
                    '<iframe src="' + src + '" id="grnInvoicePreviewIfram" width="100%" height="100%">' +
                    '<p>This browser does not support PDFs!</p>' +
                    '</iframe>'
                );
            }
        });

    });
</script>
<?php if ($_GET['status'] == "reverse") { ?>
    <script>
        $(document).ready(function() {
            $("#revdtatus").show();
        });
    </script>
<?php } ?>

<script>
    $(document).ready(function() {
        var iv_status = <?php echo $getGrnDetailsObj["data"]["iv_status"]; ?>;

        if (iv_status == 1) {
            $('.inneraction input').prop('disabled', true); // Only disable inputs inside elements with class 'inneraction'
        }
    });
</script>