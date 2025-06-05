<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-bills-controller.php");
require_once("../../../../app/v1/functions/branch/func-goods-controller.php");



function getItemCodeAndHsn($vendorCode, $vendorItemTitle)
{
    $branchId = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];
    $vendorGoodsCodeObj = queryGet("SELECT `itemCode` FROM `" . ERP_VENDOR_ITEM_MAP . "` WHERE `branchId`='" . $branchId . "' AND `vendorCode`='" . $vendorCode . "' AND `itemTitle`='" . strip_tags($vendorItemTitle) . "'");
    if ($vendorGoodsCodeObj["status"] == "success") {
        $itemCode = $vendorGoodsCodeObj["data"]["itemCode"];
        $goodsHsnObj = queryGet("SELECT `hsnCode` FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `branch`='" . $branchId . "' AND `itemCode`='" . $itemCode . "'");
        if ($goodsHsnObj["status"] == "success") {
            return [
                "itemCode" => $itemCode,
                "itemHsn" => $goodsHsnObj["data"]["hsnCode"]
            ];
        } else {
            return [
                "itemCode" => $vendorGoodsCodeObj["data"]["itemCode"],
                "itemHsn" => ""
            ];
        }
    } else {
        return [
            "itemCode" => "",
            "itemHsn" => ""
        ];
    }
}

function processInvoice($POST)
{
    if (isset($_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]) && $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"] != "") {
        $loginBranchId = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];
        $loginBranchGstin = "";
        $branchDeails = [];
        $branchDeailsObj = queryGet("SELECT * FROM " . ERP_BRANCHES . " WHERE `branch_id`=" . $loginBranchId);
        if ($branchDeailsObj["status"] == "success") {
            $branchDeails = $branchDeailsObj["data"];
            $loginBranchGstin = $branchDeails["branch_gstin"];
        } else {
            return [
                "status" => "warning",
                "message" => "Branch not found!"
            ];
        }

        if (isset($POST["grnInvoiceFile"])) {
            $billFileUploadObj = uploadFile($POST["grnInvoiceFile"], "../../../bills/", ["pdf", ".jpeg", "jpg", "png"]);
            if ($billFileUploadObj["status"] == "success") {

                $billFileFullPath = "../../../bills/" . $billFileUploadObj["data"];

                $imagelink = file_get_contents($billFileFullPath);
                $encdata = base64_encode($imagelink);

                $billControllerObj = new BillController();
                $readInvoiceObj = $billControllerObj->readVendorBills($billFileFullPath);
                if ($readInvoiceObj["status"] == "success") {
                    return [
                        "status" => "success",
                        "message" => "Invoice successfully processed.",
                        "invoiceFile" => $billFileUploadObj["data"],
                        "invoiceData" => $readInvoiceObj["data"],
                        "branchDetails" => $branchDeails
                    ];
                } else {
                    return [
                        "status" => "warning",
                        "message" => "System unable to read invoice, process denied - [".$POST["grnInvoiceFile"]["name"]."]",
                        "responseData" => $readInvoiceObj
                    ];
                }
            } else {
                return [
                    "status" => "warning",
                    "message" => "Invoice upload failed, try again!"
                ];
            }
        } else {
            return [
                "status" => "warning",
                "message" => "Invoice not found!"
            ];
        }
    } else {
        return [
            "status" => "warning",
            "message" => "Please do login first!"
        ];
    }
}


$processInvoiceObj = processInvoice($_FILES);


// console($processInvoiceObj);


if ($processInvoiceObj["status"] == "success") {
    $invoiceFile = $processInvoiceObj["invoiceFile"];
    $branchDetails = $processInvoiceObj["branchDetails"];
    $invoiceData = $processInvoiceObj["invoiceData"];


    // console($invoiceFile);
    // console($branchDetails);
    // console($invoiceData);




    $gstin1 = $invoiceData["gstin_data"][0] ?? "";
    $gstin2 = $invoiceData["gstin_data"][1] ?? "";

    $grnNo = "GRN" . time() . rand(100, 999);

    $documentNo = $invoiceData["InvoiceId"]["value"] ?? "";
    $documentDate = $invoiceData["InvoiceDate"]["value"] ?? "";
    $dueDate = $invoiceData["DueDate"]["value"] ?? "";

    $invoiceTotal = $invoiceData["InvoiceTotal"]["value"]["amount"] ?? 0;
    $invoiceSubTotal = $invoiceData["SubTotal"]["value"]["amount"] ?? 0;
    $invoiceTaxTotal = $invoiceData["TotalTax"]["value"]["amount"] ?? 0;

    $customerName = $invoiceData["CustomerName"]["value"] ?? "";
    $customerGstin = $invoiceData["CustomerTaxId"]["value"] ?? $branchDetails["branch_gstin"];
    $vendorGstin = $invoiceData["VendorTaxId"]["value"] ?? "";

    if ($customerGstin != "" && $vendorGstin == "") {
        $vendorGstin = ($customerGstin == $gstin1) ? $gstin2 : $gstin1;
    }
    if ($vendorGstin != "" && $customerGstin == "") {
        $customerGstin = ($vendorGstin == $gstin1) ? $gstin2 : $gstin1;
    }

    $customerGstinStateCode = substr($customerGstin, 0, 2);
    $vendorGstinStateCode = substr($vendorGstin, 0, 2);

    $vendorName = $invoiceData["VendorName"]["value"] ?? "";
    $vendorAddress = $invoiceData["VendorAddress"]["content"] ?? "";
    $vendorAddressRecipient = $invoiceData["VendorAddressRecipient"]["value"] ?? "";

    $vendorGstinStateName = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode`=" . $vendorGstinStateCode)["data"]["gstStateName"] ?? "";
    $customerGstinStateName = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode`=" . $customerGstinStateCode)["data"]["gstStateName"] ?? "";

    $vendorPan = substr($vendorGstin, 2, 10);
    $vendorObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id`='" . $branchDetails["company_id"] . "' AND `vendor_pan` = '" . $vendorPan . "'");
    $vendorCode = "";
    $vendorId = "";
    $vendorSuggestionObj = [];
    if ($vendorObj["status"] == "success") {
        $vendorCode = $vendorObj["data"]["vendor_code"];
        $vendorId = $vendorObj["data"]["vendor_id"];
    } else {
        $vendorSuggestionObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id`='" . $branchDetails["company_id"] . "' AND `trade_name` LIKE '%" . $vendorName . "%'", true);
    }

    $totalCGST = $totalSGST = $totalIGST = 0;
    if ($vendorGstinStateCode == $customerGstinStateCode) {
        $totalCGST = $totalSGST = $invoiceTaxTotal / 2;
        $totalIGST = $invoiceTaxTotal;
    }

    $isPoEnabledCompany = false;


    $isGrnExist = false;
    if ($vendorCode != "" && $documentNo != "") {
        $checkGrnExist = queryGet('SELECT `grnId` FROM `erp_grn` WHERE `vendorDocumentNo`="' . $documentNo . '" AND `vendorCode` ="' . $vendorCode . '"');
        if ($checkGrnExist["numRows"] > 0) {
            $isGrnExist = true;
        }
    }


    if (!$isGrnExist) {
?>
        <form action="" method="POST" id="addNewGRNForm">
            <div class="row grn-create">
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="card">
                        <div class="card-header">
                            <div class="head">
                                <i class="fa fa-user"></i>
                                <h4>Doc info</h4>
                            </div>
                        </div>
                        <div class="card-body" id="customerInfo">

                            <div class="row grn-vendor-details">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <input type="hidden" name="grnCode" value="<?= $grnNo ?>">
                                    <input type="hidden" name="documentNo" value="<?= $documentNo ?>">
                                    <input type="hidden" name="documentDate" value="<?= $documentDate ?>">
                                    <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">GRN No :&nbsp;</p>
                                        <p> <?= $grnNo ?></p>
                                    </div>
                                    <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Document No :&nbsp;</p>
                                        <p><?= $documentNo ?></p>
                                    </div>
                                    <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Document Date :&nbsp;</p>
                                        <p><?= $documentDate ?></p>
                                    </div>
                                    <div class="display-flex grn-form-input-text">
                                        <i class="fa fa-check"></i>
                                        &nbsp;
                                        <p class="label-bold">Posting Date :</p>
                                        &nbsp;
                                        <input type="date" name="" value="<?= date("Y-m-d"); ?>" class="form-control">
                                    </div>
                                    <div class="display-flex grn-form-input-text">
                                        <i class="fa fa-check"></i>
                                        &nbsp;
                                        <p class="label-bold">Due Date :</p>
                                        &nbsp;
                                        <input type="date" name="invoiceDueDate" value="<?= $dueDate ?>" class="form-control">
                                    </div>
                                    <div class="display-flex grn-form-input-text">
                                        <i class="fa fa-check"></i>
                                        &nbsp;
                                        <p class="label-bold">Due Days :</p>
                                        &nbsp;
                                        <input type="date" name="" class="form-control">
                                    </div>
                                    <div class="display-flex grn-form-input-text">
                                        <i class="fa fa-check"></i>
                                        &nbsp;
                                        <p class="label-bold">PO Number :</p>
                                        &nbsp;
                                        <input type="date" name="" class="form-control">
                                    </div>

                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="card">
                        <div class="card-header">
                            <div class="head">
                                <i class="fa fa-user"></i>
                                <h4>Vendor info</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row grn-vendor-details">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <input type="hidden" name="vendorCode" id="invoiceVendorCodeInput" value="<?= $vendorCode ?>" class="form-control" />
                                    <input type="hidden" name="vendorName" value="<?= $vendorName ?>" class="form-control" />
                                    <input type="hidden" name="vendorGstin" value="<?= $vendorGstin ?>" class="form-control" />
                                    <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Code :&nbsp;</p>
                                        <p id="invoiceVendorCodeSpan"><?= $vendorCode ?></p>
                                    </div>
                                    <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Name :&nbsp;</p>
                                        <p><?= $vendorName ?></p>
                                    </div>
                                    <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">GSTIN :&nbsp;</p>
                                        <p> <?= $vendorGstin ?></p>
                                    </div>
                                    <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">GSTIN Status :&nbsp;</p>
                                        <p class="status">Active</p>
                                    </div>
                                    <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Status :&nbsp;</p>
                                        <p class="status">Active</p>
                                    </div>
                                    <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">State :&nbsp;</p>
                                        <p><?= $vendorGstinStateName ?>(<?= $vendorGstinStateCode ?>)</p>
                                    </div>
                                    <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Customer State :&nbsp;</p>
                                        <p><?= $customerGstinStateName ?>(<?= $customerGstinStateCode ?>)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="row">
                        <div class="card card-tabs">
                            <div class="card-header">
                                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link text-secondary active" id="uploaded-invoice-preview-div-tab" data-toggle="pill" href="#uploaded-invoice-preview-div" role="tab" aria-controls="uploaded-invoice-preview-div" aria-selected="true">Uploaded Bill</a>
                                    </li>
                                    <span class="divider-vertical">|</span>
                                    <?php
                                    if ($vendorCode == "") {
                                    ?>
                                        <li class="nav-item">
                                            <a class="nav-link text-secondary" id="vendor-quick-registration-div-tab" data-toggle="pill" href="#vendor-quick-registration-div" role="tab" aria-controls="vendor-quick-registration-div" aria-selected="false">Quick Register</a>
                                        </li>
                                        <span class="divider-vertical">|</span>
                                    <?php
                                    }
                                    ?>
                                    <li class="nav-item">
                                        <a class="nav-link text-secondary" id="invoice-suggestions-div-tab" data-toggle="pill" href="#invoice-suggestions-div" role="tab" aria-controls="invoice-suggestions-div" aria-selected="false">Suggestions</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content tab-col" id="custom-tabs-three-tabContent">
                                    <div class="tab-pane fade show active iframe-preview-btn" id="uploaded-invoice-preview-div" role="tabpanel" aria-labelledby="uploaded-invoice-preview-div-tab">
                                        <iframe src='../bills/<?= $invoiceFile ?>' id="grnInvoicePreviewIfram" width="100%" height="220">
                                            <p>This browser does not support PDF!</p>
                                        </iframe>
                                        <div class="preview-btn-space">
                                            <button type="button" class="btn btn-primary preview-btn" id="iframePreview" data-toggle="modal" data-target="#exampleModalCenter">
                                                Preview
                                            </button>
                                        </div>
                                        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Invoice Preview</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body" style="height: 600px;">
                                                        <div id="iframeHolder" class="iframeholder"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <?php
                                    if ($vendorCode == "") {
                                    ?>
                                        <div class="tab-pane fade quick-registration-vendor" id="vendor-quick-registration-div" role="tabpanel" aria-labelledby="vendor-quick-registration-div-tab">
                                            <div class="container">
                                                <div class="row grn-vendor-details">
                                                    <div class="display-flex alert-danger">
                                                        <p class="text-bold" style="color: #ff0000;">Vendor not found!</p>
                                                        <p><small class="text-danger">Please do quick add or go back and add vendor before continuing the GRN.</small></p>
                                                    </div>
                                                    <div class="display-flex">
                                                        <p>Vendor Name :</p>&nbsp;
                                                        <p><?= $vendorName ?></p>
                                                    </div>
                                                    <div class="display-flex">
                                                        <p>Vendor GSTIN :</p>&nbsp;
                                                        <p><?= $vendorGstin ?></p>
                                                    </div>
                                                    <div class="display-flex">
                                                        <p>Vendor Address :</p>&nbsp;
                                                        <p><?= $vendorAddress ?></p>
                                                    </div>
                                                    <div class="row">
                                                        <a class="btn btn-sm btn-primary quick-add-vendor" data-toggle="modal" data-target="#dialogForVendorQuickAdd">Quick Add</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <div class="tab-pane fade quick-registration-vendor" id="invoice-suggestions-div" role="tabpanel" aria-labelledby="invoice-suggestions-div-tab">
                                        <div class="container">
                                            <p>
                                                <?php
                                                if (isset($vendorSuggestionObj["status"]) && $vendorSuggestionObj["status"] == "success") {
                                                    //console($vendorSuggestionObj["data"]);
                                                    echo "Suggestions not available!";
                                                } else {
                                                    echo "Suggestions not available!";
                                                }
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grn-table">
                <table class="table-sales-order table defaultDataTable grn-table">
                    <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>Item Name</th>
                            <th>Internal Code</th>
                            <th>Item HSN</th>
                            <th>Invoice Qty</th>
                            <th width="10%">Received Qty</th>
                            <th>Unit Price</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTable">
                        <?php
                        $sl = 0;
                        foreach ($invoiceData["Items"]["value"] as $oneItemObj) {


                            $oneItemData = $oneItemObj["value"];

                            $itemName = $oneItemData["Description"]["content"] ?? "";
                            $itemQty = $oneItemData["Quantity"]["value"] ?? "0";
                            $itemTax = $oneItemData["Tax"]["value"]["amount"] ?? "0";
                            $itemUnitPrice = $oneItemData["UnitPrice"]["content"] ?? "0";
                            $itemTotalPrice = $oneItemData["Amount"]["content"] ?? "0";

                            $internalItemCode = "";
                            $internalItemHsn = "";
                            if ($vendorCode != "") {
                                $itemCodeAndHsnObj = getItemCodeAndHsn($vendorCode, $itemName);
                                $internalItemCode = $itemCodeAndHsnObj["itemCode"];
                                $internalItemHsn = $itemCodeAndHsnObj["itemHsn"];
                            }


                            if ($itemName == "" || strtolower($itemName) == "cgst" || strtolower($itemName) == "sgst") {
                                continue;
                            }

                            $sl += 1;

                        ?>

                            <tr id="grnItemRowTr_<?= $sl ?>">

                                <input type="hidden" id="internalItemCode_<?= $sl ?>" name="grnItemCode[]" value="<?= $internalItemCode ?>" />
                                <input type="hidden" id="internalItemHsn_<?= $sl ?>" name="grnItemHsn[]" value="<?= $internalItemHsn ?>" />
                                <input type="hidden" name="grnItemName[]" value="<?= $itemName ?>" />
                                <input type="hidden" name="grnItemQty[]" value="<?= $itemQty ?>" />
                                <input type="hidden" name="grnItemTax[]" value="<?= $itemTax ?>" />
                                <input type="hidden" name="grnItemUnitPrice[]" value="<?= $itemUnitPrice ?>" />
                                <input type="hidden" name="grnItemTotalPrice[]" value="<?= $itemTotalPrice ?>" />

                                <td><?= $sl ?></td>
                                <td style="width: 15%;" id="grnItemNameTdSpan_<?= $sl ?>"><?= $itemName ?></th>
                                <td class="grnItemCodeTdSpan" id="grnItemCodeTdSpan_<?= $sl ?>">
                                    <?php
                                    if ($internalItemCode == "") {
                                        echo '<a class="btn btn-sm btn-xs btn-secondary openModalMapInvoiceItemCode" data-itemrownum="' . $sl . '" data-toggle="modal" data-target="#mapInvoiceItemCode">Map Code</a>';
                                    } else {
                                        echo $internalItemCode;
                                        echo '<a class="btn btn-sm btn-xs btn-secondary openModalMapInvoiceItemCodeChange" data-itemrownum="' . $sl . '" data-toggle="modal" data-target="#mapInvoiceItemCodeChange">Change</a>';
                                    }
                                    ?>
                                </td>
                                <td class="grnItemHSNTdSpan" id="grnItemHSNTdSpan_<?= $sl ?>"><?= $internalItemHsn ?></td>
                                <td id="grnItemInvoiceQtyTdSpan_<?= $sl ?>"><?= $itemQty ?></td>
                                <td>
                                    <input type="number" name="grnItemReceivedQty[]" value="<?= $itemQty ?>" id="grnItemReceivedQtyTdInput_<?= $sl ?>" class="form-control" required>
                                </td>
                                <td class="text-right" id="grnItemInvoiceUnitPriceTdSpan_<?= $sl ?>"><?= $itemUnitPrice ?></td>
                                <td class="text-right" id="grnItemInvoiceTotalPriceTdSpan_<?= $sl ?>"><?= $itemTotalPrice ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr class="itemTotals">
                            <td colspan="7" class="text-right" style="background: none; border: 0;">Sub Total</td>
                            <td class="text-right" style="background: none; border: 0;"><?= $invoiceSubTotal ?></td>
                        </tr>

                        <?php
                        if ($vendorGstinStateCode == $customerGstinStateCode) {
                            $totalCGST = $totalSGST = $invoiceTaxTotal / 2;
                            $totalIGST = $invoiceTaxTotal;
                        ?>
                            <tr class="itemTotals">
                                <td colspan="7" class="text-right" style="background: none; border: 0;">Total CGST</td>
                                <td class="text-right" style="background: none; border: 0;"><?= $totalCGST ?></td>
                            </tr>
                            <tr class="itemTotals">
                                <td colspan="7" class="text-right" style="background: none; border: 0;">Total SGST</td>
                                <td class="text-right" style="background: none; border: 0;"><?= $totalSGST ?></td>
                            </tr>
                        <?php
                        } else {
                        ?>
                            <tr class="itemTotals">
                                <td colspan="7" class="text-right" style="background: none; border: 0;">Total IGST</td>
                                <td class="text-right" style="background: none; border: 0;"><?= $totalIGST ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr class="itemTotals">
                            <input type="hidden" name="totalInvoiceCGST" value="<?= $totalCGST ?>">
                            <input type="hidden" name="totalInvoiceSGST" value="<?= $totalSGST ?>">
                            <input type="hidden" name="totalInvoiceIGST" value="<?= $totalIGST ?>">
                            <input type="hidden" name="totalInvoiceSubTotal" value="<?= $invoiceSubTotal ?>">
                            <input type="hidden" name="totalInvoiceTotal" value="<?= $invoiceTotal ?>">
                            <td colspan="7" class="text-right" style="background: none; border: 0;">Total Amount</td>
                            <td class="text-right" style="background: none; border: 0;"><?= $invoiceTotal ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <input type="submit" value="Submit GRN" name="addNewGrnFormSubmitBtn" id="addNewGrnFormSubmitBtn" class="btn btn-primary float-right m-3" />
        </form>

        <!-- modal dialogForVendorQuickAdd -->
        <div class="modal" id="dialogForVendorQuickAdd">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header py-1" style="background-color: #003060; color:white;">
                        <h5 class="modal-title">Vendor Quick Add</h5>
                        <button type="button" id="dialogForVendorQuickAddCloseBtn" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <span class="text-muted">The vendro is being added with the basic details. Account, POC, Other details need to be added latter.</span>
                        <form action="" method="post" id="vendorQuickAddForm">
                            <input type="hidden" name="vendorName" value="<?= $vendorName ?>">
                            <input type="hidden" name="vendorGstin" value="<?= $vendorGstin ?>">
                            <input type="hidden" name="vendorPan" value="<?= $vendorPan ?>">
                            <span class="has-float-label mt-3">
                                <input type="number" name="creditPeriod" placeholder="E.g 30" class="form-control" required style="height: 35px;" />
                                <label for="">Credit Period (days)</label>
                            </span>
                            <div class="form-check ml-1">
                                <label class="form-check-label">
                                    <input type="checkbox" name="notifyConcernPerson" checked class="form-check-input" value=""> <i class="fas fa-envelope"></i> Notify conserned person!
                                </label>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <div class="input-group btn-col">
                                    <button type="submit" class="btn btn-primary btnstyle" id="vendorQuickAddFormSubmitBtn">Add Vendor</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal dialogForVendorQuickAdd end -->

        <!-- modal -->
        <div class="modal" id="mapInvoiceItemCode">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header py-1" style="background-color: #003060; color:white;">
                        <h5 class="modal-title">Map Item</h5>
                        <button type="button" id="mapInvoiceItemCodeModalCloseBtn" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" id="mapInvoiceItemCodeForm">
                            <input type="hidden" name="modalItemSlNo" id="modalItemSlNo" value="0">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mapInvoiceItemTypeRadioBtn" id="mapInvoiceItemTypeGoods" checked>
                                <label class="form-check-label" for="mapInvoiceItemTypeGoods">
                                    Goods
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mapInvoiceItemTypeRadioBtn" id="mapInvoiceItemTypeService">
                                <label class="form-check-label" for="mapInvoiceItemTypeService">
                                    Services
                                </label>
                            </div>
                            <small class="text-muted mt-2">Item Description</small>
                            <textarea name="modalItemDescription" id="modalItemDescription" cols="1" rows="3" class="form-control" readonly></textarea>
                            <small class="text-muted mt-3">Select Item Code</small>
                            <select class="form-control" name="modalItemCode" id="modalItemCodeDropDown" required>
                                <?php
                                $goodsController = new GoodsController();
                                $rmGoodsObj = $goodsController->getAllRMGoods();
                                if ($rmGoodsObj["status"] == "success") {
                                    echo '<option value="" data-hsncode="" data-itemtitle="">Select Item</option>';
                                    foreach ($rmGoodsObj["data"] as $oneRmGoods) {
                                ?>
                                        <option value="<?= $oneRmGoods["itemCode"]; ?>" data-hsncode="<?= $oneRmGoods["hsnCode"]; ?>" data-itemtitle="<?= $oneRmGoods["itemName"]; ?>"><?= $oneRmGoods["itemCode"]; ?> | <?= $oneRmGoods["itemName"]; ?> | <?= $oneRmGoods["itemDesc"]; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <button type="submit" name="mapItemCodeFormSubmitBtn" class="btn btn-primary btnstyle mt-2">Map Code</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal end -->
<?php
    } else {

        echo "This invoice is already processed, try another invoice!";
    }
    //console("Vendor Code:" . $vendorCode);
    //console($vendorSuggestionObj);

    //$rmItemsObj = queryGet("SELECT * FROM " . ERP_BRANCHES . " WHERE `branch_id`=" . $loginBranchId);
} else {
    echo $processInvoiceObj["message"];
}
?>
<script>
    $(document).ready(function() {
        console.log("hello there!");
        $("#modalItemCodeDropDown").select2({
            dropdownParent: $("#mapInvoiceItemCode")
        });

        //$("#modalItemCodeDropDown").select2();

        let vendorCode = `<?= $vendorCode ?>`;
        if (vendorCode == "") {
            $("#vendor-quick-registration-div-tab").click();
            $("#itemTotalPrice")
        }

        $("#vendorQuickAddForm").on('submit', (function(e) {
            e.preventDefault();
            $.ajax({
                url: "ajaxs/vendor/ajax-vendor-quick-register.php",
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $("#vendorQuickAddFormSubmitBtn").html("Processing...");
                    console.log("Adding...");
                },
                success: function(response) {
                    let responseObj = JSON.parse(response);
                    $("#vendorQuickAddFormSubmitBtn").html("Added Successfully");
                    console.log(responseObj);
                    if (responseObj["status"] == "success") {
                        $("#invoiceVendorCodeInput").val(responseObj["vendorCode"]);
                        $("#invoiceVendorCodeSpan").html(responseObj["vendorCode"]);
                        $("#dialogForVendorQuickAddCloseBtn").click();

                        $("#uploaded-invoice-preview-div-tab").click();
                        $("#vendor-quick-registration-div").remove();
                        $("#vendor-quick-registration-div-tab").remove();

                    }

                    let Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    Toast.fire({
                        icon: responseObj["status"],
                        title: `&nbsp;${responseObj["message"]}`
                    });

                },
                error: function(e) {
                    console.log("error: " + e.message);
                }
            });
        }));


        $(document).on("#invoiceVendorCodeInput", ".change", function() {
            let vendorCode = $(this).val();
            console.log("vendorCode: " + vendorCode);
        });

        $("#addNewGRNForm").submit(function(e) {
            e.preventDefault();
            let vendorCode = $("#invoiceVendorCodeInput").val();

            if (vendorCode == "") {
                let Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                Toast.fire({
                    icon: `warning`,
                    title: `&nbsp;Vendor Code not found, please do vendor quick registration before procced GRN!`
                });
            } else {
                let isAllItemCodesMapped = true;
                $(".grnItemHSNTdSpan").each(function() {
                    let hsnCodes = $(this).text();
                    if (hsnCodes == "") {
                        isAllItemCodesMapped = false;
                        return false;
                    }
                });

                if (!isAllItemCodesMapped) {
                    let Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    Toast.fire({
                        icon: `warning`,
                        title: `&nbsp;Please make sure all item codes have been mapped!`
                    });
                } else {
                    console.log("Verified all item codes, ready for submit the form!");
                    this.submit();
                }

            }

            console.log("vendorCode", vendorCode);
        });

        $(".openModalMapInvoiceItemCode").click(function() {
            let itemSlNo = $(this).data("itemrownum");
            let itemDescription = $(`#grnItemNameTdSpan_${itemSlNo}`).html();
            $("#modalItemDescription").val(itemDescription);
            $("#modalItemSlNo").val(itemSlNo);
            $('#modalItemCodeDropDown').prop('selectedIndex', 0);
        });

        $("#mapInvoiceItemCodeForm").submit(function(e) {
            e.preventDefault();

            let vendorCode = $("#invoiceVendorCodeInput").val();

            if (vendorCode != "") {

                console.log("maping item code");
                let itemSlNo = $("#modalItemSlNo").val();
                let itemCode = $("#modalItemCodeDropDown").val();
                let itemHSN = $("#modalItemCodeDropDown").find(':selected').data("hsncode");
                let itemTitle = $("#modalItemDescription").val();
                let itemType = "goods";


                $.ajax({
                    url: "ajaxs/vendor/ajax-map-vendor-item-to-internal-code.php",
                    type: "POST",
                    data: {
                        vendorCode,
                        itemTitle,
                        itemCode,
                        itemHSN,
                        itemType
                    },
                    beforeSend: function() {
                        console.log("Mapping...");
                    },
                    success: function(response) {
                        let responseObj = JSON.parse(response);
                        if (responseObj["status"] == "success") {
                            let mapData = responseObj["data"];
                            $(`#grnItemCodeTdSpan_${itemSlNo}`).html(mapData["itemCode"]);
                            $(`#internalItemCode_${itemSlNo}`).val(mapData["itemCode"]);
                            $(`#grnItemHSNTdSpan_${itemSlNo}`).html(mapData["itemHSN"]);
                            $(`#internalItemHsn_${itemSlNo}`).val(mapData["itemHSN"]);
                        }
                        console.log("Response::");
                        console.log(responseObj);
                        let Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        Toast.fire({
                            icon: `${responseObj["status"]}`,
                            title: `&nbsp;${responseObj["message"]}`
                        });
                    },
                    error: function(e) {
                        let Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        Toast.fire({
                            icon: `error`,
                            title: `&nbsp;Mapping failed, please try again!`
                        });
                        console.log("error: " + e.message);
                    }
                });


                $("#mapInvoiceItemCodeModalCloseBtn").click();

                console.log("itemSlNo:", itemSlNo, ", itemCode:", itemCode, ", itemHSN:", itemHSN);
            } else {
                let Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                Toast.fire({
                    icon: `warning`,
                    title: `&nbsp;Please make sure vendor is registered!`
                });
            }
            $("#modalItemCodeDropDown").val("");
            $('#mapInvoiceItemCodeForm')[0].reset();

        });


        $(function() {
            $('#iframePreview').click(function() {
                if (!$('#iframe').length) {
                    $('#iframeHolder').html('<iframe src="../bills/<?= $invoiceFile ?>" id="grnInvoicePreviewIfram" width="100%" height="100%" <p>This browser does not support PDF!</p></iframe>');
                }
            });
        });

    });
</script>