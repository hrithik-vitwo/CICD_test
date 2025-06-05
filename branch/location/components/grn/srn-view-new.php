<style>
    .grn-srn-view .row.grn-create .card {
        min-height: auto;
        height: 350px;
        margin-bottom: 0;
    }
</style>


<?php
global $companyCountry;
$grnObj = new GrnController();

$accountingControllerObj = new Accounting();

$countrycode = $_SESSION["logedBranchAdminInfo"]["companyCountry"];

$country_fields = json_decode(getLebels($countrycode)['data']);

$businessTaxID = $country_fields->fields->businessTaxID ?? null;

$lable = (getLebels($companyCountry)['data']);
$lable = json_decode($lable, true);
$tdslable = ($lable['source_taxation']);
$tcslable = $lable['transaction_taxation'];
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

                $grnPostingJournalId = $ivPostingData["grnDetails"]["grnPostingJournalId"];
                $grnId = $ivPostingData["grnDetails"]["grnId"];

                // $grIrItemsObj = queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $grnPostingJournalId, true);

                // $grIrItems = [];

                // foreach ($grIrItemsObj["data"] as $grIrItem) {

                //     $grIrItems[] = $grIrItem["credit_amount"];
                // }

                // echo '<br>---------------------grIrItems-------------------';
                // console($_POST);
                // exit;



                $vendorDetailsObj = queryGet('SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=' . $ivPostingData["grnDetails"]["vendorId"]);

                $vendorParentGlId = $vendorDetailsObj["data"]["parentGlId"] ?? 0;

                $tdsDetails = ["0" => $_POST['totalInvoiceTDS']];

                $tcsDetails = ["0" => $_POST['totalInvoiceTCS']];

                $symbol = $_POST["adjust_symbol"];
                $roundOffValue = $_POST["roundOffGL"];
                if ($symbol == "add") {
                    $roundOffGL = $roundOffValue;
                } else {
                    $roundOffGL = $roundOffValue * -1;
                }


                $postingDate = $_POST['invoicePostingDate'] ?? date("Y-m-d");
                $extra_remark = $POST['extra_remark'] ?? '';

                $ivPostingInputData = [

                    "BasicDetails" => [

                        "documentNo" => $ivPostingData["grnDetails"]["vendorDocumentNo"], // Invoice Doc Number

                        "documentDate" => $ivPostingData["grnDetails"]["vendorDocumentDate"], // Invoice number

                        "postingDate" => $postingDate, // current date

                        "grnJournalId" => $grnPostingJournalId,

                        "reference" => $ivPostingData["grnDetails"]["grnCode"], // grn code

                        "remarks" => "Invoice Posting - " . $ivPostingData["grnDetails"]["grnCode"] . " " . $extra_remark,

                        "journalEntryReference" => "Purchase"

                    ],

                    "vendorDetails" => [

                        "vendorId" => $ivPostingData["grnDetails"]["vendorId"],

                        "vendorName" => $ivPostingData["grnDetails"]["vendorName"],

                        "vendorCode" => $ivPostingData["grnDetails"]["vendorCode"],

                        "parentGlId" => $vendorParentGlId

                    ],

                    "grIrItems" => $_POST['grnItemList'],

                    "taxDetails" => [

                        "cgst" => $ivPostingData["grnDetails"]["grnTotalCgst"],

                        "sgst" => $ivPostingData["grnDetails"]["grnTotalSgst"],

                        "igst" => $ivPostingData["grnDetails"]["grnTotalIgst"],

                        "rcm" => $_POST["rcm"] ?? 0

                    ],

                    "tcsDetails" => $tcsDetails,
                    "tdsDetails" => $tdsDetails,
                    "roundOffValue" => $roundOffGL

                ];
                $createInvObj = $grnObj->createInvoice($_POST);


                if ($createInvObj["status"] == "success") {
                    //console($ivPostingInputData);


                    $ivPostingObj = $accountingControllerObj->srnIvAccountingPosting($ivPostingInputData, "srniv", $createInvObj['grnIVId']);
                    $queryObj = queryUpdate('UPDATE `erp_grninvoice` SET `ivPostingJournalId`=' . $ivPostingObj["journalId"] . ' WHERE `grnIVId`=' . $createInvObj['grnIVId']);


                    swalAlert($createInvObj["status"], ucfirst($createInvObj["status"]), $createInvObj["message"], BASE_URL . "branch/location/manage-vendor-invoice.php");
                } else {
                    swalAlert($createInvObj["status"], ucfirst($createInvObj["status"]), $createInvObj["message"]);
                }
                // console($ivPostingObj);
                // echo '----------------------------------------------------------------';
                // console($_POST);
                // exit;

                // console($createInvObj);

                // exit();

            }







            $getGrnDetailsObj = $grnObj->getGrnDetails($_GET["view"]);

            if ($getGrnDetailsObj["numRows"] == 1) {

                // console($getGrnDetailsObj);

                $grnDetails = $getGrnDetailsObj["data"];
                // $grnInvoice = queryGet('SELECT * FROM `erp_grninvoice` WHERE (`companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' AND `grnId`=' . $grnDetails['grnId'] . ')')['data'];
                $grnInvoice = queryGet('SELECT * FROM `erp_grninvoice` WHERE (`companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' AND `grnId`=' . $grnDetails['grnId'] . ') ORDER BY `grnIvId` DESC LIMIT 1')['data'];



                $grnItemDetailsObj = $grnObj->getSrnItemDetails($_GET["view"]);

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
                            <input type="hidden" name="invoiceDueDate" value="<?= $grnDetails["dueDate"] ?>" required>
                            <input type="hidden" name="invoicePoNumber" value="<?= $grnDetails["grnPoNumber"] ?>">
                            <input type="hidden" name="grnType" value="srn">
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
                                        <p class="font-bold"><?= $grnDetails["grnCode"] ?></p>
                                    </div>
                                    <div class="form-input">
                                        <label for="">Document No</label>
                                        <p class="font-bold"><?= $grnDetails["vendorDocumentNo"] ?></p>
                                    </div>
                                    <div class="form-input">
                                        <label for="">Document Date</label>
                                        <?php
                                        $originalvendorDocumentDate = $grnDetails["vendorDocumentDate"];
                                        $vendorDocumentDatetimestamp = strtotime($originalvendorDocumentDate);

                                        ?>
                                        <p class="font-bold"><?= date("d-m-Y", $vendorDocumentDatetimestamp) ?></p>
                                    </div>
                                    <?php if ($getGrnDetailsObj["data"]["iv_status"] == 0 && $getGrnDetailsObj["data"]["grnStatus"] == "active") {
                                    ?>
                                        <div class="form-input">
                                            <label for="">Posting Date</label>
                                            <?php
                                            $check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
                                            $check_var_data = $check_var_sql['data'];
                                            // console($_SESSION);
                                            // // console($check_var_sql);
                                            // console($check_var_sql);
                                            $max = $check_var_data['month_end'];
                                            $min = $check_var_data['month_start'];
                                            ?>

                                            <p><input type="date" class="form-control" id="invoicePostingDateId" name="invoicePostingDate" value="<?= $grnDetails["postingDate"] ?>" min="<?= $min ?>" $max="<?= $max ?>" required></p>
                                            <p class="text-danger text-xs" id="postdatelabel"></p>
                                        </div>
                                    <?php } else { ?>
                                        <div class="form-input">
                                            <label for="">Posting Date</label>

                                            <p><input type="date" class="form-control" id="invoicePostingDateId" name="invoicePostingDate" value="<?= $grnInvoice["postingDate"] ?>" required></p>
                                            <p class="text-danger text-xs" id="postdatelabel"></p>
                                        </div>
                                    <?php } ?>
                                    <div class="form-input">
                                        <label for="">Due Date</label>
                                        <?php
                                        $originaldueDate = $grnDetails["dueDate"];
                                        $dueDatetimestamp = strtotime($originaldueDate);

                                        ?>

                                        <p class="font-weight"><?= date('d-m-Y', $dueDatetimestamp) ?></p>
                                    </div>

                                    <?php

                                    if ($grnDetails["grnPoNumber"] != "") {

                                    ?>
                                        <div class="form-input">
                                            <label for="">PO Number</label>
                                            <p class="font-bold"><?= $grnDetails["grnPoNumber"] ?></p>
                                        </div>
                                    <?php

                                    }

                                    ?>

                                </div>
                                <div class="form-input">
                                    <label for="">Remarks</label>
                                    <textarea name="extra_remark" id="extra_remark" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="line-border-area">
                                <h6>Vendor Info</h6>
                                <div class="doc-details">
                                    <div class="form-input">
                                        <label for="">Code</label>
                                        <p class="font-bold" id="invoiceVendorCodeSpan"><?= $grnDetails["vendorCode"] ?? "" ?></p>
                                    </div>
                                    <div class="form-input">
                                        <label for="">Name</label>
                                        <p class="font-bold"><?= $grnDetails["vendorName"] ?? "" ?></p>
                                    </div>
                                    <div class="form-input">
                                        <?php if ($businessTaxID != null) { ?>
                                            <label for=""><?= $businessTaxID ?></label>
                                            <p><?= $grnDetails["vendorGstin"] ?? "" ?></p>
                                        <?php } ?>
                                    </div>
                                    <div class="form-input">
                                        <label for="">State</label>
                                        <p class="font-bold"><?= $grnDetails["vendorGstinStateName"] ?? "" ?></p>
                                    </div>
                                </div>
                                <div class="form-input mt-3" id="rcm">
                                    <div class="d-flex gap-1">
                                        <input type="checkbox" name="rcm" value="1" id="rcmBtn">
                                        <p class="text-xs">Would you like to enable Reverse Charge Mechanism ?</p>
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
                                                <th>Service Name</th>
                                                <th>Service Code</th>
                                                <th>Service HSN</th>
                                                <th>Cost Center</th>
                                                <th>Invoice Qty</th>
                                                <th>Received Qty</th>
                                                <th>Unit Price</th>
                                                <th>Basic Amount</th>
                                                <?php if ($companyCountry == 103) { ?>
                                                    <th>CGST</th>
                                                    <th>SGST</th>
                                                    <th>IGST</th>
                                                <?php } else {
                                                    // $getItemTaxRule = getItemTaxRule($companyCountry, $vendorGstinStateCode, $customerGstinStateCode);
                                                    $taxComponents = json_decode($grnDetails['taxComponents'], true);
                                                ?>
                                                    <input type="hidden" name="" id="taxComponents" value='<?= json_encode($taxComponents, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                                    <?php
                                                    foreach ($taxComponents as $t) {
                                                        echo "<th>{$t['gstType']}</th>";
                                                    }
                                                    ?>

                                                <?php } ?>
                                                <th><?= $tdslable ?></th>
                                                <!-- <th>Total Amount</th> -->
                                            </tr>
                                        </thead>
                                        <tbody id="itemsTable">

                                            <?php



                                            $formEncodedData = base64_encode(json_encode([

                                                "grnDetails" => $grnDetails,

                                                "grnItemDetails" => $grnItemDetailsObj["data"]

                                            ]));



                                            echo '<input type="hidden" name="ivPostingGrnData" value="' . $formEncodedData . '" />';







                                            $sl = 0;
                                            $total_tds = 0;
                                            $flag = 0;

                                            foreach ($grnItemDetailsObj["data"] as $oneItemDetails) {

                                                $sl += 1;

                                                $basic_amount = $oneItemDetails["unitPrice"] * $oneItemDetails["receivedQty"];
                                                $tds = $oneItemDetails["tds"] ?? 0;
                                                $tds_value = $basic_amount * $tds / 100;
                                                $total_tds += $tds_value;

                                                $total_amount = $basic_amount + $oneItemDetails["cgst"] + $oneItemDetails["sgst"] + $oneItemDetails["igst"] - $tds_value;


                                                $rcmItemQuery = queryGet('SELECT `rcm_enabled` FROM `erp_inventory_items` WHERE `itemId`=' . $oneItemDetails["goodId"]);

                                                if ($rcmItemQuery["data"]["rcm_enabled"] == 1) {
                                                    $flag = 1;
                                                }

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

                                                    <td><?= $oneItemDetails["CostCenter_code"] ?></td>

                                                    <td><?= inputQuantity($oneItemDetails["goodQty"]) . " " . $oneItemDetails["itemUOM"] ?></td>

                                                    <td><?= inputQuantity($oneItemDetails["receivedQty"]) . " " . $oneItemDetails["itemUOM"] ?></td>

                                                    <td class="text-right"><?= inputValue($oneItemDetails["unitPrice"]) ?></td>

                                                    <td class="text-right" id="grnItemBaseUnit_<?= $sl ?>"><?= inputValue(($oneItemDetails["unitPrice"] * $oneItemDetails["receivedQty"])) ?></td>

                                                    <?php if ($companyCountry == 103) { ?>
                                                        <td class="text-right">

                                                            <div class="form-input">
                                                                <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemCGST]" value="<?= inputValue($oneItemDetails["cgst"]) ?>" id="grnItemUnitCgstInput_<?= $sl ?>" class="form-control text-xs w-auto itemCgst inputAmountClass">
                                                            </div>
                                                        </td>

                                                        <td class="text-right">
                                                            <div class="form-input">
                                                                <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemSGST]" value="<?= inputValue($oneItemDetails["sgst"]) ?>" id="grnItemUnitSgstInput_<?= $sl ?>" class="form-control text-xs w-auto itemSgst inputAmountClass ">
                                                            </div>
                                                        </td>

                                                        <td class="text-right">
                                                            <div class="form-input">
                                                                <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemIGST]" value="<?= inputValue($oneItemDetails["igst"]) ?>" id="grnItemUnitIgstInput_<?= $sl ?>" class="form-control text-xs w-auto itemIgst inputAmountClass">
                                                            </div>
                                                        </td>
                                                        <?php } else {

                                                        $taxComponents1 = json_decode($oneItemDetails['taxComponents'], true);
                                                        foreach ($taxComponents1 as $t) { ?>
                                                            <td class="text-right">
                                                                <div class="form-input">
                                                                    <input type="number" step="any" name="grnItemList[<?= $sl ?>][item<?= (($t['gstType'])) ?>]" value="<?= inputValue($t["taxAmount"]) ?>" id="grnItemUnit<?= ucfirst(strtolower($t['gstType'])) ?>Input_<?= $sl ?>" class="form-control text-xs w-auto dynamic item<?= ucfirst(strtolower($t['gstType'])) ?>">
                                                                </div>
                                                            </td>
                                                    <?php
                                                        }
                                                    } ?>
                                                    <input type="hidden" name="grnItemList[<?= $sl ?>][itemtax]" id="grnItemUnitTaxInput_<?= $sl ?>" value=<?= ($oneItemDetails["taxComponents"]) ?>>

                                                    <td class="text-right">
                                                        <div class="form-input d-flex gap-2">
                                                            <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemTDS]" value="<?= inputValue($oneItemDetails["tds"]) ?>" id="grnItemUnitTdsInput_<?= $sl ?>" class="form-control text-xs w-auto itemTds inputAmountClass">
                                                            <p class="text-xs">%</p>
                                                        </div>
                                                    </td>

                                                    <span style="display: none" class="text-right" id="grnItemUnitTDTotal_<?= $sl ?>"><?= ($total_amount) ?></span>

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
                                            <?php if ($companyCountry == 103) { ?>
                                                <tr class="itemTotals">

                                                    <td style="background: none;">Total CGST</td>

                                                    <td class="text-right" style="background: none;">
                                                        <input type="number" step="any" name="totalInvoiceCGST" value="<?= inputValue($grnDetails["grnTotalCgst"]) ?>" id="totalInvoiceCGST" class="form-control text-xs itemUnitPrice" readonly>
                                                    </td>

                                                </tr>

                                                <tr class="itemTotals">

                                                    <td style="background: none;">Total SGST</td>

                                                    <td class="text-right" style="background: none;">
                                                        <input type="number" step="any" name="totalInvoiceSGST" value="<?= inputValue($grnDetails["grnTotalSgst"]) ?>" id="totalInvoiceSGST" class="form-control text-xs itemUnitPrice" readonly>
                                                    </td>

                                                </tr>
                                                <tr class="itemTotals">

                                                    <td style="background: none;">Total IGST</td>

                                                    <td class="text-right" style="background: none;">
                                                        <input type="number" step="any" name="totalInvoiceIGST" value="<?= inputValue($grnDetails["grnTotalIgst"]) ?? 0 ?>" id="totalInvoiceIGST" class="form-control text-xs itemUnitPrice" readonly>
                                                    </td>

                                                </tr>
                                                <?php } else {
                                                $grngst = json_decode($grnDetails['taxComponents'], true);
                                                foreach ($grngst as $t) {

                                                ?>

                                                    <tr class="itemTotals">

                                                        <td style="background: none;">Total <?= $t['gstType']; ?></td>

                                                        <td class="text-right" style="background: none;">
                                                            <input type="number" step="any" name="totalInvoice<?= $t['gstType']; ?>" value="<?= inputValue($t["taxAmount"]) ?? 0 ?>" id="totalInvoice<?= $t['gstType']; ?>" class="form-control text-xs itemUnitPrice" readonly>
                                                        </td>

                                                    </tr>
                                            <?php }
                                            } ?>
                                            <input type="hidden" step="any" name="totalInvoiceGrnd" id="totalInvoiceGrnd" class="form-control text-xs itemUnitPrice" readonly>


                                            <tr class="itemTotals">

                                                <td style="background: none;">Total <?= $tdslable ?></td>

                                                <td class="text-right" style="background: none;">
                                                    <?php if ($getGrnDetailsObj["data"]["iv_status"] == 0 && $getGrnDetailsObj["data"]["grnStatus"] == "active") {
                                                    ?>
                                                        <input type="number" step="any" name="totalInvoiceTDS" value="<?= inputValue($total_tds) ?>" id="totalInvoiceTDS" class="form-control text-xs itemUnitPrice" readonly>
                                                    <?php } else { ?>
                                                        <input type="number" step="any" name="totalInvoiceTDS" value="<?= inputValue(($grnInvoice["grnTotalTds"])) ?? 0 ?>" id=" totalInvoiceTDS" class="form-control text-xs itemUnitPrice" readonly>

                                                    <?php } ?>
                                                </td>

                                            </tr>

                                            <tr class="itemTotals">
                                                <td style="background: none;">Total <?= $tcslable ?></td>
                                                <?php if ($getGrnDetailsObj["data"]["iv_status"] == 0 && $getGrnDetailsObj["data"]["grnStatus"] == "active") {
                                                ?>
                                                    <td class="text-right" style="background: none;">
                                                        <input type="number" step="any" name="totalInvoiceTCS" value="0" id="totalInvoiceTCS" class="form-control text-xs itemTCS inputAmountClass">
                                                    </td>
                                                <?php } else { ?>
                                                    <td class="text-right" style="background: none;">
                                                        <input type="number" step="any" name="totalInvoiceTCS" readonly value="<?= inputValue(($grnInvoice["grnTotalTcs"])) ?? 0 ?>" id="totalInvoiceTCS" class="form-control text-xs itemTCS inputAmountClass">
                                                    </td>
                                                <?php } ?>

                                            </tr>

                                            <tr class="itemTotals">

                                                <td class="font-bold" style="background: none; border: 0;">Total Amount</td>
                                                <?php
                                                if ($getGrnDetailsObj["data"]["iv_status"] == 0 && $getGrnDetailsObj["data"]["grnStatus"] == "active") {
                                                ?>
                                                    <td class="text-right font-bold" id="tdGrandTotal" style="background: none; border: 0;s"><?= inputValue($grnDetails["grnTotalAmount"]) ?? 0 ?></td>
                                                <?php } else {
                                                    if ($grnInvoice["roundoff"] > 0) {
                                                        $r = $grnInvoice["roundoff"] * -1;
                                                    } else {
                                                        $r = $grnInvoice["roundoff"] * -1;
                                                    } ?>
                                                    <td class="text-right font-bold" id="tdGrandTotal" style="background: none; border: 0;s"><?= inputValue($grnInvoice["grnTotalAmount"] + $r) ?? 0 ?></td>


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

                                                            <input type="number" step="any" name="roundOffGL" id="round_value" value="0.00" class="form-control text-center inputAmountClass">

                                                        <?php
                                                        } else {
                                                        ?>
                                                            <input type="number" step="any" name="roundOffGL" id="round_value" disabled readonly value="<?= inputValue(($grnInvoice["roundoff"])) ?? 0 ?>" class="form-control text-center inputAmountClass">
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
                                                    <td class="text-right text-success font-bold" id="tdAdjustedTotal" style="background: none; border: 0;"><?= inputValue($grnInvoice["grnTotalAmount"]) ?></td>

                                                <?php } ?>
                                            </tr>
                                            <input type="hidden" id="tdAdjustedTotalval" name="tdAdjustedTotalval" value="0.0"></td>


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

                                        <button type="submit" name="ivPostingFormSubmitBtnSRN" class="btn btn-primary mt-3 mb-5">IV Posting</button>

                                    </p>

                                </div>

                            <?php

                            }

                            ?>

                        </div>

                    </div>

                </form>

            <?php

            } else {

                console($getGrnDetailsObj);
            }

            ?>
        </div>

    </section>

</div>




<?php if ($getGrnDetailsObj["data"]["iv_status"] != 1 && $getGrnDetailsObj["data"]["grnStatus"] == "active") { ?>
    <script>
        $(document).ready(function() {
            updateAdjustedTotal();

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

            $(document).on("change", "#round_sign", function() {
                updateAdjustedTotal();
            });
            $(document).on("keyup", "#round_value", function() {
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
            $(document).on("keyup", ".dynamic", function() {
                let rowNo = ($(this).attr("id")).split("_")[1];
                calculateOneItemAmounts(rowNo);
            });


            function calculateOneItemAmounts(rowNo) {
                let basicPrice = (helperAmount($(`#grnItemBaseInput_${rowNo}`).val()) > 0) ? helperAmount($(`#grnItemBaseInput_${rowNo}`).val()) : 0;
                var gst = 0;
                var taxArray = [];
                <?php if ($companyCountry == 103) { ?>
                    let cgst = (helperAmount($(`#grnItemUnitCgstInput_${rowNo}`).val()) > 0) ? helperAmount($(`#grnItemUnitCgstInput_${rowNo}`).val()) : 0;
                    let sgst = (helperAmount($(`#grnItemUnitSgstInput_${rowNo}`).val()) > 0) ? helperAmount($(`#grnItemUnitSgstInput_${rowNo}`).val()) : 0;
                    let igst = (helperAmount($(`#grnItemUnitIgstInput_${rowNo}`).val()) > 0) ? helperAmount($(`#grnItemUnitIgstInput_${rowNo}`).val()) : 0;
                    gst = cgst + sgst + igst;
                    taxArray.push({
                        gstType: "CGST",
                        taxPercentage: "50",
                        taxAmount: inputValue(cgst)
                    });
                    taxArray.push({
                        gstType: "CGST",
                        taxPercentage: "50",
                        taxAmount: inputValue(sgst)
                    });
                    taxArray.push({
                        gstType: "IGST",
                        taxPercentage: "100",
                        taxAmount: inputValue(igst)
                    });
                <?php } else { ?>

                    let jsonData = $("#taxComponents").val();
                    let taxComponents = JSON.parse(jsonData);
                    taxComponents.forEach(function(item, index) {
                        var gstType = item.gstType;
                        gstType = gstType.charAt(0).toUpperCase() + gstType.slice(1).toLowerCase();

                        let igst = (helperAmount($(`#grnItemUnit${gstType}Input_${rowNo}`).val()) > 0) ? helperAmount($(`#grnItemUnit${gstType}Input_${rowNo}`).val()) : 0;
                        taxArray.push({
                            gstType: item.gstType,
                            taxPercentage: item.taxPercentage,
                            taxAmount: inputValue(igst)
                        });

                        gst += igst;

                    });

                <?php  } ?>
                document.getElementById(`grnItemUnitTaxInput_${rowNo}`).value = JSON.stringify(taxArray);
                let tds = (helperAmount($(`#grnItemUnitTdsInput_${rowNo}`).val()) > 0) ? helperAmount($(`#grnItemUnitTdsInput_${rowNo}`).val()) : 0;

                let tds_value = basicPrice * (tds / 100);

                let totalItemPrice = basicPrice + gst - tds_value;

                console.log(totalItemPrice, gst, tds_value);

                $(`#grnItemUnitTDTotal_${rowNo}`).html(inputValue(totalItemPrice));
                $(`#grnItemTDSValue_${rowNo}`).val(inputValue(tds_value));



                calculateGrandTotalAmount();
            }

            calculateGrandTotalAmount();

            function calculateGrandTotalAmount() {
                let totalAmount = 0;
                let grandSubTotalAmt = 0;
                let TotalCGSt = 0;
                let TotalSGSt = 0;
                let TotalIGSt = 0;
                let TotalTds = 0;
                var taxArray2 = [];
                var grndgst = 0;


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

                taxArray2.push({
                    gstType: "CGST",
                    taxPercentage: "50",
                    taxAmount: inputValue(TotalCGSt)
                });
                taxArray2.push({
                    gstType: "SGST",
                    taxPercentage: "50",
                    taxAmount: inputValue(TotalSGSt)
                });
                taxArray2.push({
                    gstType: "IGST",
                    taxPercentage: "100",
                    taxAmount: inputValue(TotalIGSt)
                });
                <?php if ($companyCountry != 103) { ?>
                    let jsonData = $("#taxComponents").val();
                    let taxComponents = JSON.parse(jsonData);
                    taxComponents.forEach(function(item, index) {
                        var gstType = item.gstType;
                        var nameg = item.gstType;

                        var totalgst = 0;

                        gstType = gstType.charAt(0).toUpperCase() + gstType.slice(1).toLowerCase();

                        $(`.item${gstType}`).each(function() {
                            totalgst += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
                        });
                        grndgst += totalgst;
                        $(`#totalInvoice${nameg}`).val(inputValue(totalgst))
                        taxArray2.push({
                            gstType: item.gstType,
                            taxPercentage: item.taxPercentage,
                            taxAmount: inputValue(totalgst)
                        });


                    });

                <?php } ?>
                document.getElementById("totalInvoiceGrnd").value = JSON.stringify(taxArray2);

                $(`#totalInvoiceCGST`).val(TotalCGSt);
                $(`#totalInvoiceSGST`).val(TotalSGSt);
                $(`#totalInvoiceIGST`).val(TotalIGSt);
                $(`#totalInvoiceTDS`).val(TotalTds);

                let ToTalcgst = (helperAmount($(`#totalInvoiceCGST`).val()) > 0) ? helperAmount($(`#totalInvoiceCGST`).val()) : 0;
                let ToTalsgst = (helperAmount($(`#totalInvoiceSGST`).val()) > 0) ? helperAmount($(`#totalInvoiceSGST`).val()) : 0;
                let ToTaligst = (helperAmount($(`#totalInvoiceIGST`).val()) > 0) ? helperAmount($(`#totalInvoiceIGST`).val()) : 0;
                let ToTaltcs = (helperAmount($(`#totalInvoiceTCS`).val()) > 0) ? helperAmount($(`#totalInvoiceTCS`).val()) : 0;
                let ToTalinvTds = (helperAmount($(`#totalInvoiceTDS`).val()) > 0) ? helperAmount($(`#totalInvoiceTDS`).val()) : 0;

                // totalAmount = grandSubTotalAmt + grndgst + ToTalcgst + ToTalsgst + ToTaligst + ToTaltcs - ToTalinvTds;


                // rcm checking js
                let rcmValue = $("#rcmBtn").is(":checked") ? 1 : 0;
                if (rcmValue == 1) {
                    totalAmount = grandSubTotalAmt + ToTaltcs - ToTalinvTds;

                } else {
                    // totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst + ToTaltcs - ToTalinvTds;
                    totalAmount = grandSubTotalAmt + grndgst + ToTalcgst + ToTalsgst + ToTaligst + ToTaltcs - ToTalinvTds;


                }
                $("#totalInvoiceTotal").val(inputValue(totalAmount));
                $("#tdGrandTotal").html(inputValue(totalAmount));
                updateAdjustedTotal();
            }
            // for rcm checking js
            $(document).on("change", "#rcmBtn", function() {
                calculateGrandTotalAmount();
            });


            $(document).on("keyup", "#totalInvoiceCGST", function() {
                // let grandSubTotalAmt = 0;
                // $(".ItemInvoiceTotalPrice").each(function() {
                //     grandSubTotalAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
                // });
                // let ToTalcgst = (helperAmount($(`#totalInvoiceCGST`).val()) > 0) ? helperAmount($(`#totalInvoiceCGST`).val()) : 0;
                // let ToTalsgst = (helperAmount($(`#totalInvoiceSGST`).val()) > 0) ? helperAmount($(`#totalInvoiceSGST`).val()) : 0;
                // let ToTaligst = (helperAmount($(`#totalInvoiceIGST`).val()) > 0) ? helperAmount($(`#totalInvoiceIGST`).val()) : 0;
                // let ToTaltcs = (helperAmount($(`#totalInvoiceTCS`).val()) > 0) ? helperAmount($(`#totalInvoiceTCS`).val()) : 0;
                // let ToTalinvTds = (helperAmount($(`#totalInvoiceTDS`).val()) > 0) ? helperAmount($(`#totalInvoiceTDS`).val()) : 0;

                // let totalAmount = 0;

                // totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst + ToTaltcs - ToTalinvTds;

                // $("#totalInvoiceTotal").val(totalAmount.toFixed(2));
                // $("#tdGrandTotal").html(totalAmount.toFixed(2));
                // updateAdjustedTotal();
                calculateGrandTotalAmount();
            });

            $(document).on("keyup", "#totalInvoiceSGST", function() {
                // let grandSubTotalAmt = 0;
                // $(".ItemInvoiceTotalPrice").each(function() {
                //     grandSubTotalAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
                // });
                // let ToTalcgst = (helperAmount($(`#totalInvoiceCGST`).val()) > 0) ? helperAmount($(`#totalInvoiceCGST`).val()) : 0;
                // let ToTalsgst = (helperAmount($(`#totalInvoiceSGST`).val()) > 0) ? helperAmount($(`#totalInvoiceSGST`).val()) : 0;
                // let ToTaligst = (helperAmount($(`#totalInvoiceIGST`).val()) > 0) ? helperAmount($(`#totalInvoiceIGST`).val()) : 0;
                // let ToTaltcs = (helperAmount($(`#totalInvoiceTCS`).val()) > 0) ? helperAmount($(`#totalInvoiceTCS`).val()) : 0;
                // let ToTalinvTds = (helperAmount($(`#totalInvoiceTDS`).val()) > 0) ? helperAmount($(`#totalInvoiceTDS`).val()) : 0;

                // let totalAmount = 0;

                // totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst + ToTaltcs - ToTalinvTds;

                // $("#totalInvoiceTotal").val(totalAmount.toFixed(2));
                // $("#tdGrandTotal").html(totalAmount.toFixed(2));
                // updateAdjustedTotal();
                calculateGrandTotalAmount();
            });

            $(document).on("keyup", "#totalInvoiceIGST", function() {
                // let grandSubTotalAmt = 0;
                // $(".ItemInvoiceTotalPrice").each(function() {
                //     grandSubTotalAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
                // });
                // let ToTalcgst = (helperAmount($(`#totalInvoiceCGST`).val()) > 0) ? helperAmount($(`#totalInvoiceCGST`).val()) : 0;
                // let ToTalsgst = (helperAmount($(`#totalInvoiceSGST`).val()) > 0) ? helperAmount($(`#totalInvoiceSGST`).val()) : 0;
                // let ToTaligst = (helperAmount($(`#totalInvoiceIGST`).val()) > 0) ? helperAmount($(`#totalInvoiceIGST`).val()) : 0;
                // let ToTaltcs = (helperAmount($(`#totalInvoiceTCS`).val()) > 0) ? helperAmount($(`#totalInvoiceTCS`).val()) : 0;
                // let ToTalinvTds = (helperAmount($(`#totalInvoiceTDS`).val()) > 0) ? helperAmount($(`#totalInvoiceTDS`).val()) : 0;

                // let totalAmount = 0;

                // totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst + ToTaltcs - ToTalinvTds;

                // $("#totalInvoiceTotal").val(totalAmount.toFixed(2));
                // $("#tdGrandTotal").html(totalAmount.toFixed(2));
                // updateAdjustedTotal()
                calculateGrandTotalAmount();

            });


            $(document).on("keyup", "#totalInvoiceTCS", function() {
                // console.log("Hello");

                // let grandSubTotalAmt = 0;
                // $(".ItemInvoiceTotalPrice").each(function() {
                //     grandSubTotalAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
                // });
                // let ToTalcgst = (helperAmount($(`#totalInvoiceCGST`).val()) > 0) ? helperAmount($(`#totalInvoiceCGST`).val()) : 0;
                // let ToTalsgst = (helperAmount($(`#totalInvoiceSGST`).val()) > 0) ? helperAmount($(`#totalInvoiceSGST`).val()) : 0;
                // let ToTaligst = (helperAmount($(`#totalInvoiceIGST`).val()) > 0) ? helperAmount($(`#totalInvoiceIGST`).val()) : 0;
                // let ToTaltcs = (helperAmount($(`#totalInvoiceTCS`).val()) > 0) ? helperAmount($(`#totalInvoiceTCS`).val()) : 0;
                // let ToTalinvTds = (helperAmount($(`#totalInvoiceTDS`).val()) > 0) ? helperAmount($(`#totalInvoiceTDS`).val()) : 0;

                // let totalAmount = 0;

                // totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst + ToTaltcs - ToTalinvTds;

                // console.log(totalAmount);

                // updateAdjustedTotal();
                // $("#totalInvoiceTotal").val(totalAmount.toFixed(2));
                // $("#tdGrandTotal").html(totalAmount.toFixed(2));
                calculateGrandTotalAmount();



            });

        });
    </script>
<?php } ?>
<script>
    $(function() {

        $('#iframePreview').click(function() {

            if (!$('#iframe').length) {

                $('#iframeHolder').html('<iframe src="<?= COMP_STORAGE_URL ?>/grn-invoice/<?= $grnDetails["vendorDocumentFile"]  ?? "" ?>" id="grnInvoicePreviewIfram" width="100%" height="100%" <p>This browser does not support PDF!</p></iframe>');

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