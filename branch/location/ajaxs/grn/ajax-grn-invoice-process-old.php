<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-bills-controller.php");
require_once("../../../../app/v1/functions/branch/func-goods-controller.php");



function getItemCode($branchId, $vendorId, $vendorItemTitle)
{

    return "";
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
                        "message" => "Invoice process failed, try again!",
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


console($processInvoiceObj);


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
    $vendorSuggestionObj = [];
    if ($vendorObj["status"] == "success") {
        $vendorCode = $vendorObj["data"]["vendor_code"];
    } else {
        $vendorSuggestionObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id`='" . $branchDetails["company_id"] . "' AND `trade_name` LIKE '%" . $vendorName . "%'", true);
    }
    //console("Vendor Code:" . $vendorCode);
    //console($vendorSuggestionObj);

    //$rmItemsObj = queryGet("SELECT * FROM " . ERP_BRANCHES . " WHERE `branch_id`=" . $loginBranchId);
?>
    <form action="" method="POST" id="addNewGRNForm">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <ul class="list-group">
                            <li class="list-group-item text-center h6 p-2 m-0 font-weight-bold text-muted" style="background:#f4f4f4">Vendor Info</li>
                        </ul>
                    </div>
                    <div class="col-md-12 my-2">
                        <div class="row" id="customerInfo">
                            <div class="col-md-6 col-sm-12">
                                <ul class="list-group">
                                    <li class="list-group-item"><strong>GRN Id: </strong>&nbsp;<?= $grnNo ?></li>
                                    <li class="list-group-item"><strong>Document No: </strong>&nbsp;<?= $documentNo ?></li>
                                    <li class="list-group-item"><strong>Document Date: </strong>&nbsp;<?= $documentDate ?></li>
                                    <li class="list-group-item">
                                        <span class="has-float-label">
                                            <input type="date" name="invoicePostingDate" value="<?= date("Y-m-d"); ?>" class="form-control" style="height: 35px;" />
                                            <label for="">Posting Date</label>
                                        </span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="has-float-label">
                                            <input type="date" name="invoiceDueDate" value="<?= $dueDate ?>" class="form-control" style="height: 35px;" />
                                            <label for="">Due Date</label>
                                        </span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="has-float-label">
                                            <input type="number" name="invoiceDueDays" class="form-control" style="height: 35px;" />
                                            <label for="">Due Days</label>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 col-sm-12 mt-xs-2">
                                <ul class="list-group">
                                    <input type="hidden" id="invoiceVendorCodeInput" value="<?= $vendorCode ?>" class="form-control" />
                                    <li class="list-group-item"><strong>Vendor Code: </strong>&nbsp;<span id="invoiceVendorCodeSpan"><?= $vendorCode ?></span></li>
                                    <li class="list-group-item"><strong>Vendor Name: </strong>&nbsp;<?= $vendorName ?></li>
                                    <li class="list-group-item"><strong>Vendor GSTIN: </strong>&nbsp;<?= $vendorGstin ?></li>
                                    <li class="list-group-item"><strong>Vendor GST Status: </strong>&nbsp;Active</li>
                                    <li class="list-group-item"><strong>Vendor Status: </strong>&nbsp;Active</li>
                                    <li class="list-group-item"><strong>Vendor State: </strong>&nbsp;<?= $vendorGstinStateName ?>(<?= $vendorGstinStateCode ?>)</li>
                                    <li class="list-group-item"><strong>Customer State: </strong>&nbsp;<?= $customerGstinStateName ?>(<?= $customerGstinStateCode ?>)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row m-0 p-0">
                    <div class="card card-tabs p-0">
                        <div class="card-header p-1">
                            <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link text-secondary active" id="uploaded-invoice-preview-div-tab" data-toggle="pill" href="#uploaded-invoice-preview-div" role="tab" aria-controls="uploaded-invoice-preview-div" aria-selected="true">Uploaded Bill</a>
                                </li>
                                <?php
                                if ($vendorCode == "") {
                                ?>
                                    <li class="nav-item">
                                        <a class="nav-link text-secondary" id="vendor-quick-registration-div-tab" data-toggle="pill" href="#vendor-quick-registration-div" role="tab" aria-controls="vendor-quick-registration-div" aria-selected="false">Quick Register</a>
                                    </li>
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
                                <div class="tab-pane fade show active" id="uploaded-invoice-preview-div" role="tabpanel" aria-labelledby="uploaded-invoice-preview-div-tab">
                                    <iframe src='bills/<?= $invoiceFile ?>' id="grnInvoicePreviewIfram" width="100%" height="200">
                                        <p>This browser does not support PDF!</p>
                                    </iframe>
                                </div>
                                <?php
                                if ($vendorCode == "") {
                                ?>
                                    <div class="tab-pane fade" id="vendor-quick-registration-div" role="tabpanel" aria-labelledby="vendor-quick-registration-div-tab">
                                        <span class="text-bold" style="color: #ff0000;">Vendor not found!</span>
                                        <small class="text-danger">Please do quick add or go back and add vendor before continuing the GRN.</small>
                                        <ul class="list-group">
                                            <li class="list-group-item"><strong>Vendor Name: </strong>&nbsp;<?= $vendorName ?></li>
                                            <li class="list-group-item"><strong>Vendor GSTIN: </strong>&nbsp;<?= $vendorGstin ?></li>
                                            <li class="list-group-item"><strong>Vendor Address: </strong>&nbsp;<?= $vendorAddress ?></li>
                                        </ul>
                                        <div class="row m-0 p-0">
                                            <span class="btn btn-sm form-control col-md-6 mt-2 btn-primary ml-auto mr-auto" data-toggle="modal" data-target="#dialogForVendorQuickAdd">Quick Add</span>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="tab-pane fade" id="invoice-suggestions-div" role="tabpanel" aria-labelledby="invoice-suggestions-div-tab">
                                    <?php
                                    if (isset($vendorSuggestionObj["status"]) && $vendorSuggestionObj["status"] == "success") {
                                        //console($vendorSuggestionObj["data"]);
                                        echo "Suggestions not available!";
                                    } else {
                                        echo "Suggestions not available!";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <ul class="list-group">
                    <li class="list-group-item text-center h6 p-2 m-0 font-weight-bold text-muted" style="background:#f4f4f4">Items Info</li>
                </ul>
            </div>
        </div>
        <div class="row m-0 p-0" style="overflow-y: auto;">
            <table class="table-sales-order">
                <thead>
                    <tr>
                        <th>Sl No.</th>
                        <th>Item Name</th>
                        <th>Item Code</th>
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
                        $sl += 1;

                        $oneItemData = $oneItemObj["value"];

                        $itemName = $oneItemData["Description"]["content"] ?? "";
                        $itemQty = $oneItemData["Quantity"]["value"] ?? "0";
                        $itemTax = $oneItemData["Tax"]["value"]["amount"] ?? "0";
                        $itemUnitPrice = $oneItemData["UnitPrice"]["content"] ?? "0";
                        $itemTotalPrice = $oneItemData["Amount"]["content"] ?? "0";

                        $internalItemCode = "";
                        $internalItemHsn = "";

                        if($itemName=="" || strtolower($itemName)=="cgst" || strtolower($itemName)=="sgst"){
                            continue;
                        }

                        ?>
                        <tr id="grnItemRowTr_<?= $sl ?>">

                            <input type="hidden" id="internalItemCode_<?= $sl ?>" name="grnItemCode[]" value="<?= $internalItemCode ?>" />
                            <input type="hidden" id="internalItemHsn_<?= $sl ?>" name="grnItemHsn[]" value="<?= $internalItemHsn ?>" />
                            <input type="hidden" name="grnItemName[]" value="<?= $oneItemData["itemName"] ?>" />
                            <input type="hidden" name="grnItemQty[]" value="<?= $oneItemData["itemQty"] ?>" />
                            <input type="hidden" name="grnItemTax[]" value="<?= $oneItemData["itemTax"] ?>" />
                            <input type="hidden" name="grnItemUnitPrice[]" value="<?= $oneItemData["itemUnitPrice"] ?>" />
                            <input type="hidden" name="grnItemTotalPrice[]" value="<?= $oneItemData["itemTotalPrice"] ?>" />

                            <td><?= $sl ?></td>
                            <td><span id="grnItemNameTdSpan_<?= $sl ?>"><?= $itemName ?></span></th>
                            <td>
                                <span class="grnItemCodeTdSpan" id="grnItemCodeTdSpan_<?= $sl ?>">
                                    <?php
                                    if ($internalItemCode == "") {
                                        echo '<a class="btn btn-xs btn-secondary openModalMapInvoiceItemCode" data-itemrownum="' . $sl . '" data-toggle="modal" data-target="#mapInvoiceItemCode">Map Code</a>';
                                    } else {
                                        echo $internalItemCode;
                                    }
                                    ?>
                                </span>
                            </td>
                            <td><span class="grnItemHSNTdSpan" id="grnItemHSNTdSpan_<?= $sl ?>"><?= $internalItemHsn ?></span></td>
                            <td><span id="grnItemInvoiceQtyTdSpan_<?= $sl ?>"><?= $itemQty ?></span></td>
                            <td><input type="number" name="grnItemReceivedQty[]" value="<?= $itemQty ?>" id="grnItemReceivedQtyTdInput_<?= $sl ?>" class="form-control" required></td>
                            <td><span id="grnItemInvoiceUnitPriceTdSpan_<?= $sl ?>"><?= $itemUnitPrice ?></span></td>
                            <td><span id="grnItemInvoiceTotalPriceTdSpan_<?= $sl ?>"><?= $itemTotalPrice ?></span></td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr class="itemTotals">
                        <td colspan="7" class="text-right">Sub Total</td>
                        <td><?= $invoiceSubTotal ?></td>
                    </tr>

                    <?php
                    if ($vendorGstinStateCode == $customerGstinStateCode) {
                        $totalCGST = $totalSGST = $invoiceTaxTotal/2;
                        $totalIGST = $invoiceTaxTotal;
                    ?>
                        <input type="hidden" name="totalInvoiceCGST" value="<?= $totalCGST ?>">
                        <input type="hidden" name="totalInvoiceSGST" value="<?= $totalSGST ?>">
                        <input type="hidden" name="totalInvoiceIGST" value="<?= $totalIGST ?>">
                        <input type="hidden" name="totalInvoiceSubTotal" value="<?= $invoiceSubTotal ?>">
                        <input type="hidden" name="totalInvoiceTotal" value="<?= $invoiceTotal ?>">
                        <tr class="itemTotals">
                            <td colspan="7" class="text-right">Total CGST</td>
                            <td><?= $totalCGST ?></td>
                        </tr>
                        <tr class="itemTotals">
                            <td colspan="7" class="text-right">Total SGST</td>
                            <td><?= $totalSGST ?></td>
                        </tr>
                    <?php
                    } else {
                    ?>
                        <tr class="itemTotals">
                            <td colspan="7" class="text-right">Total IGST</td>
                            <td><?= $invoiceTaxTotal ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr class="itemTotals">
                        <td colspan="7" class="text-right">Total Amount</td>
                        <td><?= $invoiceTotal ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button type="submit" name="addNewGrnFormSubmitBtn" id="addNewGrnFormSubmitBtn" class="btn btn-primary float-right m-3">Submit GRN</button>
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
                        <small class="text-muted mt-2">Item Description</small>
                        <textarea name="modalItemDescription" id="modalItemDescription" cols="1" rows="3" class="form-control" readonly>swcjnjnjnjn j  nkkk k knkk</textarea>
                        <small class="text-muted mt-3">Select Item Code</small>
                        <select class="form-control" name="modalItemCode" id="modalItemCodeDropDown" required>
                            <?php
                            $goodsController = new GoodsController();
                            $rmGoodsObj = $goodsController->getAllRMGoods();
                            if ($rmGoodsObj["status"] == "success") {
                                echo '<option value="" data-hsncode="">Select Item</option>';
                                foreach ($rmGoodsObj["data"] as $oneRmGoods) {
                            ?>
                                    <option value="<?= $oneRmGoods["itemCode"]; ?>" data-hsncode="<?= $oneRmGoods["hsnCode"]; ?>"><?= $oneRmGoods["itemCode"]; ?> | <?= $oneRmGoods["itemName"]; ?> | <?= $oneRmGoods["itemDesc"]; ?></option>
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
            console.log("maping item code");

            let itemSlNo = $("#modalItemSlNo").val();
            let itemCode = $("#modalItemCodeDropDown").val();
            let itemHSN = $("#modalItemCodeDropDown").find(':selected').data("hsncode");

            $(`#grnItemCodeTdSpan_${itemSlNo}`).html(itemCode);
            $(`#internalItemCode_${itemSlNo}`).val(itemCode);
            $(`#grnItemHSNTdSpan_${itemSlNo}`).html(itemHSN);
            $(`#internalItemHsn_${itemSlNo}`).val(itemHSN);

            $("#mapInvoiceItemCodeModalCloseBtn").click();

            console.log("itemSlNo:", itemSlNo, ", itemCode:", itemCode, ", itemHSN:", itemHSN);
        });

    });
</script>