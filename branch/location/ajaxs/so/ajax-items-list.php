<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();
if ($_GET['act'] === "listItem") {
    $itemId = $_GET['itemId'];
    $getItemObj = $ItemsObj->getItemById($itemId);
    $getItemSummaryObj = $BranchSoObj->fetchItemSummaryDetails($itemId)['data'][0];
    $randCode = $getItemObj['data']['itemId'] . rand(00, 99);


    $company = $BranchSoObj->fetchCompanyDetails()['data'];
    $currencyIcon = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data']['currency_icon'];

    // print_r($getItemObj);

    $itemPriceObj = $BranchSoObj->fetchBranchSoItemPriceDetails($getItemObj['data']['itemCode'])['data'][0];

    // $itemUnitPrice = $itemPriceObj['ItemPrice'] ?? 0;
    $itemUnitPrice = $getItemSummaryObj['itemPrice'] ?? 0;
    $itemBasePrice = $itemUnitPrice * 1;
    // $itemMaxDiscount = $itemPriceObj['ItemMaxDiscount'] ?? 0;
    $itemMaxDiscount = $getItemSummaryObj['itemMaxDiscount'] ?? 0;

    $hsnInfo = $BranchSoObj->fetchHsnDetails($getItemObj['data']['hsnCode'])['data'][0];
    $itemTaxPercentage = $hsnInfo['taxPercentage'];

    $itemTotalTax = ($itemUnitPrice * $itemTaxPercentage) / 100;
    $itemTotalPrice = $itemUnitPrice + $itemTotalTax;

?>
    <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemId]" value="<?= $getItemObj['data']['itemId'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][goodsType]" value="<?= $getItemObj['data']['goodsType'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemDesc]" value="<?= $getItemObj['data']['itemDesc'] ?>">
        <input type="hidden" name="listItem[<?= $randCode ?>][baseAmount]" value="<?= $itemBasePrice ?>" class="form-control full-width-center itemBaseAmountInp" id="itemBaseAmountInp_<?= $randCode ?>">
        <td>
            <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemCode]" value="<?= $getItemObj['data']['itemCode'] ?>">
            <?= $getItemObj['data']['itemCode'] ?>
        </td>
        <td>
            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
            <?= $getItemObj['data']['itemName'] ?>
        </td>
        <td>
            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][hsnCode]" value="<?= $getItemObj['data']['hsnCode'] ?>">
            <?= $getItemObj['data']['hsnCode'] ?> <i class="fa fa-info-circle" style="cursor: pointer;" title="<?= $hsnInfo['hsnDescription'] ?>"></i>
        </td>
        <td class="inp-td">
            <div class="d-flex">
                <span class="rupee-symbol currency-symbol pr-1">#</span>
                <input step="any" type="number" name="listItem[<?= $randCode ?>][qty]" value="1" class="inp-design full-width itemQty" id="itemQty_<?= $randCode ?>">
                <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>
            </div>
            <input type="hidden" name="listItem[<?= $randCode ?>][uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>">
        </td>
        <td class="inp-td">
            <div class="d-flex">
                <span style="border:none !important" class="rupee-symbol currency-symbol pr-1"><?= $currencyIcon ?> </span>
                <input type="text" name="listItem[<?= $randCode ?>][unitPrice]" value="<?= $getItemSummaryObj['itemPrice'] ?>" class="inp-design full-width-center itemUnitPrice" style="border:none !important" id="itemUnitPrice_<?= $randCode ?>">
            </div>
        </td>
        <td class="inp-td" style="max-width: 20px !important;">
            <div class="d-flex">
                <span class="rupee-symbol currency-symbol pr-1">%</span>
                <input type="text" value="0" name="listItem[<?= $randCode ?>][totalDiscount]" class="inp-design full-width-center itemDiscount" id="itemDiscount_<?= $randCode ?>" readonly>
                <small class="maxLimitStyle" style="display: none;">Max <br><?php if ($itemMaxDiscount == 0) { ?> <span class="text-danger">0</span> <?php } else { ?> <strong class="itemMaxDiscount" id="itemMaxDiscount_<?= $randCode ?>"><?= $itemMaxDiscount ?></strong> <?php } ?>%</small>
            </div>
            <div style="font-size: .5em; display: none;" class="mt-2 text-dark itemSpecialDiscount" id="itemSpecialDiscount_<?= $randCode ?>"></div>
        </td>
        <td class="inp-td" style="min-width: 20px !important;max-width: 100px !important;">
            <div class="d-flex">
                <span class="rupee-symbol currency-symbol pr-1"><?= $currencyIcon ?> </span>
                <input type="text" name="listItem[<?= $randCode ?>][itemTotalDiscount1]" class="inp-design full-width-center itemTotalDiscount1" id="itemTotalDiscount1_<?= $randCode ?>" value="0" readonly>
            </div>
            <span class="itemTotalDiscount" style="display: none;" id="itemTotalDiscount_<?= $randCode ?>">0</span>
        </td>
        <td>
            <input class="form-control itemTax" id="itemTax_<?= $randCode ?>" type="hidden" name="listItem[<?= $randCode ?>][tax]" value="<?= $itemTaxPercentage ?>">
            <?= $hsnInfo['taxPercentage'] ?>%
        </td>
        <td>
            <input type="hidden" name="listItem[<?= $randCode ?>][itemTotalTax1]" value="<?= $itemTotalTax ?>" class="form-control full-width-center itemTotalTax1" id="itemTotalTax1_<?= $randCode ?>" readonly>
            <span class="rupee-symbol"><?= $currencyIcon ?></span><span class="itemTotalTax" id="itemTotalTax_<?= $randCode ?>"> <?= $itemTotalTax ?></span>
        </td>
        <td>
            <input type="hidden" name="listItem[<?= $randCode ?>][totalPrice]" value="<?= $itemTotalPrice ?>" class="form-control full-width-center itemTotalPrice" id="itemTotalPrice_<?= $randCode ?>" readonly>
            <div class="text-success font-weight-bold">
                <span class="rupee-symbol"><?= $currencyIcon ?></span>
                <span class="itemTotalPrice1" id="itemTotalPrice1_<?= $randCode ?>"> <?= $itemTotalPrice ?></span>
            </div>
        </td>
        <td class="action-flex-btn">
            <button type="button" class="btn-view btn btn-primary" data-toggle="modal" data-target="#deliveryScheduleModal_<?= $randCode ?>">
                <i id="statusItemBtn_<?= $getItemObj['data']['itemId'] ?>" class="statusItemBtn fa fa-cog"></i>
            </button>

            <button type="button" class="btn btn-danger delItemBtn">
                <i class="fa fa-minus" id="delItemBtn_<?= $getItemObj['data']['itemId'] ?>" class="delItemBtn mx-1 fa fa-minus"></i>
            </button>

            <div class="modal modal-left left-item-modal fade" id="deliveryScheduleModal_<?= $randCode ?>" tabindex="-1" role="dialog" aria-labelledby="left_modal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= $getItemObj['data']['itemName'] ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h6 class="modal-title">Remaining Qty: <span class="mainQty" id="mainQty_<?= $randCode ?>">1</span> <span class="error" id="mainQtymsg_<?= $randCode ?>"></span></h6>
                            <div class="row">
                                <input class="form-control full-width randClass" id="randClass_<?= $randCode ?>" type="hidden" value="<?= $randCode ?>">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="form-input">
                                        <label>Tolerance (%)</label>
                                        <input type="text" name="listItem[<?= $randCode ?>][tolerance]" class="form-control" id="location" placeholder="Tolerance (%)" value="">
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="modal-add-row modal-add-row_<?= $randCode ?>">
                                        <div class="row modal-cog-right">
                                            <div class="col-lg-5 col-md-5 col-sm-5">
                                                <div class="form-input">
                                                    <label>Delivery Date</label>
                                                    <input type="date" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][multiDeliveryDate]" class="form-control multiDeliveryDate" id="multiDeliveryDate_<?= $randCode ?>" placeholder="delivery date" value="<?= $_GET['deliveryDate'] ?>">

                                                </div>
                                            </div>
                                            <div class="col-lg-5 col-md-5 col-sm-5">
                                                <div class="form-input">
                                                    <label>Quantity</label>
                                                    <input type="text" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][quantity]" class="form-control multiQuantity" data-itemid="<?= $randCode ?>" id="multiQuantity_<?= $randCode ?>" placeholder="quantity" value="1">

                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2">
                                                <a style="cursor: pointer" class="btn btn-primary addQtyBtn" id="addQtyBtn_<?= $randCode ?>" onclick='addMultiQty(<?= $randCode ?>)'>
                                                    <i class="fa fa-plus"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer modal-footer-fixed">
                            <button type="button" class="btn btn-primary saveClose" style="display: none;" id="saveClose_<?= $randCode ?>" data-dismiss="modal">Save & Close</button>
                            <button type="button" class="text-danger saveCloseLoading" style="display: none;" id="saveCloseLoading_<?= $randCode ?>"> Please make sure all the QTY<small class="text-info">(Bal-<span id="setAvlQty_<?= $randCode ?>"></span>)</small> is scheduled to enable save button.</button>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>






<?php
} elseif ($_GET['itemId'] === "ss") {
    $price = 20;
    $qty = $_GET['id'];
    echo $qty * $price;
} elseif ($_GET['act'] === "approvalTab") {
    global $updated_by;

    $soId = $_GET['soId'];
    $soDetails = queryGet("SELECT * FROM `erp_branch_sales_order` WHERE so_id=$soId")['data'];
    $customerId = $soDetails['customer_id'];
    $SONumber = $soDetails['so_number'];
    $soDate=$soDetails['so_date'];
    $postingTime=$soDetails['soPostingTime'];
    $deliveryDate=$soDetails['delivery_date'];
    $billingAddress=$soDetails['billingAddress'];
    $creditPeriod=$soDetails['credit_period'];
    $shippingAddress=$soDetails['shippingAddress'];
    $curr_rate=$soDetails['conversion_rate'];
    $goodsType=$soDetails['goodsType'];
    $currencyName=$soDetails['currency_name'];
    $customerPO=$soDetails['customer_po_no'];
    $soStatus=$soDetails['soStatus'];
    $createdby=$soDetails['created_by'];
    $updatedby=$soDetails['updated_by'];
    $currentTime = date("Y-m-d H:i:s");

    $upd = "UPDATE `" . ERP_BRANCH_SALES_ORDER . "` SET approvalStatus=9 WHERE so_id='" . $soId . "'";
    if ($dbCon->query($upd)) {
        $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
        $auditTrail['basicDetail']['trail_type'] = 'APPROVED';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
        $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_SALES_ORDER;
        $auditTrail['basicDetail']['column_name'] = 'so_id'; // Primary key column
        $auditTrail['basicDetail']['document_id'] = $soId;  // primary key
        $auditTrail['basicDetail']['party_type'] = 'customer';
        $auditTrail['basicDetail']['party_id'] = $customerId;
        $auditTrail['basicDetail']['document_number'] = $SONumber;
        $auditTrail['basicDetail']['action_code'] = $action_code;
        $auditTrail['basicDetail']['action_referance'] = '';
        $auditTrail['basicDetail']['action_title'] = 'Seles Order Approved';  //Action comment
        $auditTrail['basicDetail']['action_name'] = 'Updated';     //	Add/Update/Deleted
        $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
        $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
        $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
        $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($upd);
        $auditTrail['basicDetail']['others'] = '';
        $auditTrail['basicDetail']['remark'] = '';

        $auditTrail['action_data']['Sales Order Detail']['So_number'] = $SONumber;
        $auditTrail['action_data']['Sales Order Detail']['So_date'] = formatDateWeb($soDate);
        $auditTrail['action_data']['Sales Order Detail']['SoPostingTime'] = ($postingTime);
        $auditTrail['action_data']['Sales Order Detail']['Delivery_date'] = formatDateWeb($deliveryDate);
        $auditTrail['action_data']['Sales Order Detail']['Billing Address'] = $billingAddress;
        $auditTrail['action_data']['Sales Order Detail']['Shipping Address'] = $shippingAddress;
        $auditTrail['action_data']['Sales Order Detail']['Credit_period'] = $creditPeriod;
        $auditTrail['action_data']['Sales Order Detail']['Conversion_rate'] = decimalValuePreview($curr_rate);
        $auditTrail['action_data']['Sales Order Detail']['Currency_name'] = $currencyName;
        $auditTrail['action_data']['Sales Order Detail']['GoodsType'] = $goodsType;
        $auditTrail['action_data']['Sales Order Detail']['Customer Order No'] = $customerPO;
        $auditTrail['action_data']['Sales Order Detail']['SoStatus'] = 'open';
        $auditTrail['action_data']['Sales Order Detail']['Created_by'] = getCreatedByUser($createdby);
        $auditTrail['action_data']['Sales Order Detail']['Updated_by'] = getCreatedByUser($updatedby);


        $auditTrail['action_data']['Approve Details']['Approved By'] = getCreatedByUser($updated_by);
        $auditTrail['action_data']['Approve Details']['Approved At'] = formatDateTime($currentTime);

        $auditTrailreturn = generateAuditTrail($auditTrail);
        echo "success";
    } else {
        echo "error";
    }
} elseif ($_GET['act'] === "rejectTab") {
    $soId = $_GET['soId'];
    global $updated_by;
    $soDetails = queryGet("SELECT * FROM `erp_branch_sales_order` WHERE so_id=$soId")['data'];
    $customerId = $soDetails['customer_id'];
    $SONumber = $soDetails['so_number'];
    $soDate=$soDetails['so_date'];
    $postingTime=$soDetails['soPostingTime'];
    $deliveryDate=$soDetails['delivery_date'];
    $billingAddress=$soDetails['billingAddress'];
    $curr_rate=$soDetails['soPostingTime'];
    $creditPeriod=$soDetails['soPostingTime'];
    $shippingAddress=$soDetails['shippingAddress'];
    $curr_rate=$soDetails['conversion_rate'];
    $goodsType=$soDetails['goodsType'];
    $currencyName=$soDetails['currency_name'];
    $customerPO=$soDetails['customer_po_no'];
    $soStatus=$soDetails['soStatus'];
    $createdby=$soDetails['created_by'];
    $updatedby=$soDetails['updated_by'];
    $currentTime = date("Y-m-d H:i:s");
    $upd = "UPDATE `" . ERP_BRANCH_SALES_ORDER . "` SET approvalStatus=17 WHERE so_id='" . $soId . "'";
    // exit();
    if ($dbCon->query($upd)) {
        $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
        $auditTrail['basicDetail']['trail_type'] = 'REJECT'; //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
        $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_SALES_ORDER;
        $auditTrail['basicDetail']['column_name'] = 'so_id'; // Primary key column
        $auditTrail['basicDetail']['document_id'] = $soId;  // primary key
        $auditTrail['basicDetail']['party_type'] = 'customer';
        $auditTrail['basicDetail']['party_id'] = $customerId;
        $auditTrail['basicDetail']['document_number'] = $SONumber;
        $auditTrail['basicDetail']['action_code'] = $action_code;
        $auditTrail['basicDetail']['action_referance'] = '';
        $auditTrail['basicDetail']['action_title'] = 'Seles Order Rejected';  //Action comment
        $auditTrail['basicDetail']['action_name'] = 'Updated';     //	Add/Update/Deleted
        $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
        $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
        $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
        $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($upd);
        $auditTrail['basicDetail']['others'] = '';
        $auditTrail['basicDetail']['remark'] = '';

        $auditTrail['action_data']['Sales Order Detail']['So_number'] = $SONumber;
        $auditTrail['action_data']['Sales Order Detail']['So_date'] = formatDateWeb($soDate);
        $auditTrail['action_data']['Sales Order Detail']['SoPostingTime'] = formatDateWeb($postingTime);
        $auditTrail['action_data']['Sales Order Detail']['Delivery_date'] = formatDateWeb($deliveryDate);
        $auditTrail['action_data']['Sales Order Detail']['Billing Address'] = $billingAddress;
        $auditTrail['action_data']['Sales Order Detail']['Shipping Address'] = $shippingAddress;
        $auditTrail['action_data']['Sales Order Detail']['Credit_period'] = $creditPeriod;
        $auditTrail['action_data']['Sales Order Detail']['Conversion_rate'] = decimalValuePreview($curr_rate);
        $auditTrail['action_data']['Sales Order Detail']['Currency_name'] = $currencyName;
        $auditTrail['action_data']['Sales Order Detail']['GoodsType'] = $goodsType;
        $auditTrail['action_data']['Sales Order Detail']['Customer Order No'] = $customerPO;
        $auditTrail['action_data']['Sales Order Detail']['SoStatus'] = 'open';
        $auditTrail['action_data']['Sales Order Detail']['Created_by'] = getCreatedByUser($createdby);
        $auditTrail['action_data']['Sales Order Detail']['Updated_by'] = getCreatedByUser($updatedby);


        $auditTrail['action_data']['Reject Details']['Rejected By'] = getCreatedByUser($updated_by);
        $auditTrail['action_data']['Reject Details']['Rejected At'] = formatDateTime($currentTime);

        $auditTrailreturn = generateAuditTrail($auditTrail);
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "Something wrong, try again!";
}
?>