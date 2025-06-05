<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-bills-controller.php");
$headerData = array('Content-Type: application/json');

// queryGet
if (isset($_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]) && $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"] != "") {

    $loginBranchId = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];
    $loginBranchGstin = "";


    if (isset($_FILES["billFile"])) {
        $billFileUploadObj = uploadFile($_FILES["billFile"], "../../bills/", ["pdf", ".jpeg", "jpg", "png"]);
        if ($billFileUploadObj["status"] == "success") {

            $billControllerObj = new BillController();
            $uploadedBillObj = $billControllerObj->readVendorBills("../../bills/" . $billFileUploadObj["data"]);

            if ($uploadedBillObj["status"] == "success") {

                $branchDeailsObj = queryGet("SELECT * FROM " . ERP_BRANCHES . " WHERE `branch_id`=" . $loginBranchId);
                if ($branchDeailsObj["status"] == "success") {
                    $branchDeails = $branchDeailsObj["data"];
                    $loginBranchGstin = $branchDeails["branch_gstin"];
                }

                $billData = $uploadedBillObj["data"];

                $billSubTotal = (isset($billData["SubTotal"]["value"]["amount"])) ? $billData["SubTotal"]["value"]["amount"] : 0;
                $billTotalTax = (isset($billData["TotalTax"]["value"]["amount"])) ? $billData["TotalTax"]["value"]["amount"] : 0;
                $billGrandTotal = (isset($billData["InvoiceTotal"]["value"]["amount"])) ? $billData["InvoiceTotal"]["value"]["amount"] : 0;

                $billCustomerName = (isset($billData["CustomerName"]["value"])) ? $billData["CustomerName"]["value"] : "";
                $billVendorName = (isset($billData["VendorName"]["value"])) ? $billData["VendorName"]["value"] : "";
                $billVendorAddress = (isset($billData["VendorAddress"]["content"])) ? $billData["VendorAddress"]["content"] : "";

                $billInvoiceDate = (isset($billData["InvoiceDate"]["value"])) ? $billData["InvoiceDate"]["value"] : "";

                $billInvoiceNumber = (isset($billData["InvoiceId"]["value"])) ? $billData["InvoiceId"]["value"] : "";

                $billVendorGSTIN = (isset($billData["VendorTaxId"]["value"])) ? $billData["VendorTaxId"]["value"] : "";

                if ($billTotalTax <= 0) {
                    $billTotalTax = $billGrandTotal - $billSubTotal;
                }

                $billOrderNumber = "";
                $billStateCode = "";
                $billDueDate = "";
                $billStateName = "";
                $vendorId = "";

                $billTotalIGST = 0;
                $billTotalCGST = 0;
                $billTotalSGST = 0;


                $VendorCustomerGSTINs = $billData["gstin_data"];

                $billVendorGSTIN = $VendorCustomerGSTINs[0];
                $billCustomerGSTIN = $VendorCustomerGSTINs[1];
                if ($VendorCustomerGSTINs[0] == $loginBranchGstin) {
                    $billCustomerGSTIN = $VendorCustomerGSTINs[0];
                    $billVendorGSTIN = $VendorCustomerGSTINs[1];
                }
                if (substr($billCustomerGSTIN, 0, 2) == substr($billVendorGSTIN, 0, 2)) {
                    $billTotalCGST = $billTotalSGST = $billTotalTax / 2;
                } else {
                    $billTotalIGST = $billTotalTax;
                }

                if($billVendorGSTIN!="") {
                    $billStateCode = substr($billVendorGSTIN, 0, 2);
                }


                $vendorDetails = [];
                $suggestVendorDetails = [];
                

                if($billVendorName!="" || $billVendorGSTIN!=""){
                    if($billVendorGSTIN!=""){
                        $selectSql = "SELECT * FROM `".ERP_VENDOR_DETAILS."` WHERE `vendor_gstin` = '".$billVendorGSTIN."'";
                    }else{
                        $selectSql = "SELECT * FROM `".ERP_VENDOR_DETAILS."` WHERE `vendor_name` = '".$billVendorName."'";
                    }
                    $selectSql.= " AND `company_branch_id`='".$loginBranchId."'";

                    $queryObj = queryGet($selectSql);
                    if($queryObj["status"] == "success"){
                        $vendorDetails = $queryObj["data"];
                        $vendorId = $vendorDetails["vendor_id"];
                    }else{
                        $vendorDetails = [];
                        $suggestVendorDetails = [];
                        //Find suggest vendor details below
                    }   
                }
                
                
                ?>



                <input type="hidden" name="vendorId" id="vendorIdInput" value="<?= $vendorId ?>" />
                <input type="hidden" name="billVendorGSTIN" value="<?= $billVendorGSTIN ?>" />
                <div class="row">
                    <div class="col-md-6 mt-1 d-flex"></div>
                    <div class="col-md-6 mb-3 mt-1 d-flex align-items-center" style="justify-content:flex-end;"></div>
                    <div class="col-md-8">
                        <div class="card ">
                            <div class="card-header" style="border-top: solid darkblue;">
                                <h4 class="card-title w-100">
                                    <a class="d-block w-100 text-dark">
                                        New Bill
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="collapse show" data-parent="#accordion">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <input type="hidden" name="vendorId" id="vendorId" value="<?= $vendorId ?>" class="form-control itemTotalPriceInput">
                                                <select id="" name="vendor" class="form-control form-control-border borderColor" required>
                                                    <option value="">Select Vendor</option>
                                                    <option value="<?= $billVendorName ?>" selected><?= $billVendorName ?></option>
                                                </select>
                                                <small><? $billVendorAddress ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form__group">
                                                <input type="text" class="form__field" name="billDate" placeholder="Bill Date" value="<?= $billInvoiceDate ?>" autocomplete="off" required>
                                                <label for="" class="form__label">Bill Date</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form__group">
                                                <input type="text" name="vendorBillNumber" value="<?= $billInvoiceNumber ?>" class="form__field" id="exampleInputBorderWidth2" placeholder="Vendor Bill No" required>
                                                <label for="exampleInputBorderWidth2" class="form__label">Vendor Bill No.</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form__group">
                                                <input type="text" name="billRefNumber" value="" class="form__field" id="exampleInputBorderWidth2" placeholder="Bill Number">
                                                <label for="exampleInputBorderWidth2" class="form__label">Bill Ref Number</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form__group">
                                                <input type="date" name="billDueDate" value="<?= $billDueDate ?>" class="form__field active" id="exampleInputBorderWidth2" placeholder="">
                                                <label for="exampleInputBorderWidth2" class="form__label">Due Date</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form__group">
                                                <input type="text" name="billOrderNumber" value="<?= $billOrderNumber ?>" class="form__field active" id="exampleInputBorderWidth2">
                                                <label for="exampleInputBorderWidth2" class="form__label">Order Number</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form__group">
                                                <input type="text" name="billStateCode" value="<?= $billStateCode ?>" class="form__field active" id="exampleInputBorderWidth2">
                                                <label for="exampleInputBorderWidth2" class="form__label">State Code</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form__group">
                                                <input type="text" name="billStateName" value="<?= $billStateName ?>" class="form__field active" id="exampleInputBorderWidth2">
                                                <label for="exampleInputBorderWidth2" class="form__label">State Name</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <select id="" name="vendorGstin" class="form-control form-control-border borderColor">
                                                    <option value="">GSTIN</option>
                                                    <option value="<?= $billVendorGSTIN ?>" selected><?= $billVendorGSTIN ?></option>
                                                </select>
                                                <label class="form__label">Vendor GSTIN</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group">

                                                <select name="billToAddress" class=" form-control form-control-border borderColor">
                                                    <option value="">Bill To Address</option>
                                                    <option value="<?= $billCustomerName ?>" selected><?= $billCustomerName ?></option>
                                                </select>
                                                <label class="form__label">Bill To Address</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form__group">
                                                <input type="text" name="billNote" class="form__field active" id="exampleInputBorderWidth2">
                                                <label for="exampleInputBorderWidth2" class="form__label">Notes</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div class="card card-primary card-outline card-tabs">
                            <div class="card-header p-0 pt-1 border-bottom-0">
                                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true">Suggestions</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false">Uploaded Bill</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body fontSize">
                                <div class="tab-content tab-col" id="custom-tabs-three-tabContent">
                                    <div class="tab-pane fade show active" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                                        <?php
                                        if (count($vendorDetails) < 1 && count($suggestVendorDetails) < 1) {
                                        ?>
                                            <div class="card" style="border: 1px solid #d1d1d199">
                                                <div class="card-header">
                                                    <h3 class="card-title text-danger">Vendor not found! Click below to quick register!</h3>
                                                </div>
                                                <div class="card-body p-0">
                                                    <table class="table table-sm p-0">
                                                        <tbody>
                                                            <tr>
                                                                <td>Vendor Name:</td>
                                                                <td id="quickRegVendorName"><?= $billVendorName ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Vendor GSTIN:</td>
                                                                <td id="quickRegVendorGstin"><?= $billVendorGSTIN ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Vendor Address:</td>
                                                                <td id="quickRegVendorAddress"><?= $billVendorAddress ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <a id="btnVendorQuickAdd" class="btn btn-sm btn-primary" style="float:right;">Quick Add Vendor</a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php
                                        } else if(count($vendorDetails)>0) {
                                            ?>
                                            <div class="card" style="border: 1px solid #d1d1d199">
                                                <div class="card-header">
                                                    <h3 class="card-title text-danger">Vendor Details ✅</h3>
                                                </div>
                                                <div class="card-body p-0">
                                                    <table class="table table-sm p-0">
                                                        <tbody>
                                                            <tr>
                                                                <td>Vendor Trade Name:</td>
                                                                <td><?= $vendorDetails["trade_name"] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Vendor Pan:</td>
                                                                <td><?= $vendorDetails["vendor_pan"] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Vendor GSTIN:</td>
                                                                <td><?= $vendorDetails["vendor_gstin"] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Vendor Status:</td>
                                                                <td><?= $vendorDetails["vendor_status"] ?></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <?php
                                        } else{
                                        ?>
                                            Suggestions yet not available!

                                        <?php
                                        }
                                        //console($vendorDetails);
                                        //console($suggestVendorDetails);

                                        ?>
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab">
                                        <div id="uploadedBillPreviewDiv">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- <div class="w-100 mt-3">
                        <button type="submit" name="addInventoryItem" class="btn-primary mb-3 btnstyle btn btn-block btn-sm">
                          <i class="fa fa-plus fontSize"></i>
                          Add New
                        </button>
                      </div> -->
                    </div>
                </div>
                <div class="card ">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table form-table customFields">

                                <tr>
                                    <th scope="col" width="10%">Items</th>
                                    <th scope="col" width="15%">Hsn</th>
                                    <th scope="col" width="35%">Description</th>
                                    <th scope="col" width="10%">Quantity</th>
                                    <th scope="col" width="10%">Price</th>
                                    <th scope="col" width="10%">Amount</th>
                                    <th scope="col" width="10%"> <a href="javascript:void(0);" class="addCF btn btn-info"><i class="fa fa-plus"></i></a></th>
                                </tr>

                                <?php
                                if (isset($billData['Items']) && count($billData['Items']) > 0) {
                                    foreach ($billData['Items']['value'] as $oneItemObj) {
                                        $oneItem = $oneItemObj['value'];

                                        $oneItemName = (isset($oneItem['Description']['value'])) ? $oneItem['Description']['value'] : "";
                                        $oneItemHSN = "";
                                        $oneItemDescription = (isset($oneItem['Description']['value'])) ? $oneItem['Description']['value'] : "";
                                        $oneItemQuantity = (isset($oneItem['Quantity']['value'])) ? $oneItem['Quantity']['value'] : 0;
                                        $oneItemUnitPrice = (isset($oneItem['UnitPrice']['value']['amount'])) ? $oneItem['UnitPrice']['value']['amount'] : 0;
                                        $oneItemTotalPrice = (isset($oneItem['Amount']['value']['amount'])) ? $oneItem['Amount']['value']['amount'] : 0;

                                ?>
                                        <tr>
                                            <td width="10%">
                                                <input type="text" name="itemName[]" class="form-control" placeholder="Name" value="<?= $oneItemName ?>" readonly="readonly">
                                            </td>
                                            <td width="15%">
                                                <input type="text" name="itemHSN[]" class="form-control" placeholder="HSN" value="<?= $oneItemHSN ?>">
                                            </td>
                                            <td width="35%">
                                                <input type="text" name="itemDescription[]" class="form-control" placeholder="Description" value="<?= $oneItemDescription ?>">
                                            </td>
                                            <td width="10%">
                                                <input type="number" name="itemQuantity[]" class="form-control itemQuantityInput" min="1" value="<?= $oneItemQuantity ?>">
                                            </td>
                                            <td width="10%">
                                                <input type="number" name="itemUnitPrice[]" class="form-control itemUnitPriceInput" value="<?= $oneItemUnitPrice ?>">
                                            </td>
                                            <td width="10%" class="pt-3">
                                                <input type="hidden" name="itemTotalPrice[]" class="form-control itemTotalPriceInput" value="<?= $oneItemTotalPrice ?>">
                                                <span class="itemTotalPriceSpan"><?= number_format($oneItemTotalPrice, 2) ?></span>
                                            </td>
                                            <td width="10%">
                                                <button class="btn btn-danger remove"><i class="fa fa-times" aria-hidden="true"></i></button>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td width="10%">
                                            <input type="text" name="itemName[]" class="form-control" placeholder="Name" value="">
                                        </td>
                                        <td width="15%">
                                            <input type="text" name="itemHSN[]" class="form-control" placeholder="HSN">
                                        </td>
                                        <td width="35%">
                                            <input type="text" name="itemDescription[]" class="form-control" placeholder="Description">
                                        </td>
                                        <td width="10%">
                                            <input type="number" name="itemQuantity[]" class="form-control itemQuantityInput" min="1" value="1">
                                        </td>
                                        <td width="10%">
                                            <input type="number" name="itemUnitPrice[]" class="form-control itemUnitPriceInput" value="0.00">
                                        </td>
                                        <td width="10%" class="pt-3">
                                            <input type="hidden" name="itemTotalPrice[]" class="form-control itemTotalPriceInput">
                                            <span class="itemTotalPriceSpan">0.00</span>
                                        </td>
                                        <td width="10%">
                                            <button class="btn btn-danger remove"><i class="fa fa-times" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                <?php
                                }

                                ?>

                            </table>
                        </div>

                    </div>

                    <div class="row pl-3 pr-3">
                        <div class="col-md-6"></div>
                        <div class="col-md-6" style="text-align:right;">
                            <table style="width:100%;">
                                <tr>
                                    <td style="text-align:right;">
                                        <small>Subtotal</small>
                                    <td>
                                    <td style="text-align:right;"><i class="fa fa-inr" aria-hidden="true"></i>
                                        <input type="hidden" name="billSubTotal" id="billSubTotalInput" value="<?= $billSubTotal; ?>">
                                        <span id="billSubTotalSpan"><?= number_format($billSubTotal, 2); ?></span>
                                    <td>
                                </tr>


                                <?php
                                if ($billTotalIGST > 0) {
                                ?>
                                    <tr>
                                        <td style="text-align:right;">
                                            <small>IGST</small>
                                        <td>
                                        <td style="text-align:right;">
                                            <input type="hidden" name="billTotalIGST" id="billTotalIGSTinput" value="<?php echo $billTotalIGST; ?>" />
                                            <i class="fa fa-inr" aria-hidden="true"></i><span id="totalIgstSpan"><?php echo number_format($billTotalIGST, 2); ?></span>
                                        <td>
                                    </tr>
                                <?php
                                } else {
                                ?>
                                    <tr>
                                        <td style="text-align:right;">
                                            <small>CGST</small>
                                        <td>
                                        <td style="text-align:right;">
                                            <input type="hidden" name="billTotalCGST" id="billTotalCGSTinput" value="<?php echo $billTotalCGST; ?>" />
                                            <i class="fa fa-inr" aria-hidden="true"></i><span id="totalCgstSpan"><?php echo number_format($billTotalCGST, 2); ?></span>
                                        <td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:right;">
                                            <small>SGST</small>
                                        <td>
                                        <td style="text-align:right;">
                                            <input type="hidden" name="billTotalSGST" id="billTotalSGSTinput" value="<?php echo $billTotalSGST; ?>" />
                                            <i class="fa fa-inr" aria-hidden="true"></i><span id="totalSgstSpan"><?php echo number_format($billTotalSGST, 2); ?></span>
                                        <td>
                                    </tr>
                                <?php
                                }
                                ?>

                                <tr>
                                    <td style="text-align:right;">
                                        <small>Total GST</small>
                                    <td>
                                    <td style="text-align:right;">
                                        <input type="hidden" name="billTotalTax" value="<?php echo $billTotalTax; ?>" />
                                        <i class="fa fa-inr" aria-hidden="true"></i><span id="totalGstSpan"><?php echo number_format($billTotalTax, 2); ?>
                                    <td>
                                </tr>
                                <tr>
                                    <td style="text-align:right;">
                                        <small>Add Discount</small>
                                    <td>
                                    <td style="text-align:right;"><i class="fa fa-inr" aria-hidden="true"></i> 0.00
                                    <td>
                                </tr>
                                <tr>
                                    <td class="d-flex align-items-center" style="text-align:right;justify-content:flex-end;">
                                        <P class="mb-0 mr-2">Grand Total</p>
                                        <select name="billCurrency" class="form-control form-control-border borderColor" style="width:100px">
                                            <option value="">Indian Rupe</option>

                                        </select>
                                    <td>
                                    <td style="text-align:right;">
                                        <input type="hidden" name="billGrandTotal" id="billGrandTotalInput" value="<?= $billSubTotal ?>">
                                        <span id="billGrandTotalSpan"><?= number_format($billGrandTotal, 2) ?></span>
                                    <td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-12 mb-4 mt-4 d-flex" style="justify-content:flex-end;">
                            <input type="submit" name="saveBillFormBtn" value="Save" class="btn btn-primary btnstyle">
                            <input type="submit" name="draftBillFormBtn" value="Draft" class="btn-danger ml-2 btn btnstyle">
                        </div>
                    </div>
                </div>

<?php

            } else {
                echo "error";
            }
        } else {
            echo "file_error";
        }
    } else {
        echo "file_not_found";
    }
} else {
    echo "Please do login first";
}
