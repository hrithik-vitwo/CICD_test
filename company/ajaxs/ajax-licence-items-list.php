<?php
require_once("../../app/v1/connection-company-admin.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
// $_POST['selectedValue']=2;
if (isset($_POST['selectedValue'])) {
    $selectedValue = $_POST['selectedValue'];

    $randCode = $getVariant['data']['packageVariantId'] . rand(00, 99);

    $sqlVariant = "SELECT `" . ERP_PACKAGE_VARIANT . "`.*,`" . ERP_PACKAGE_MANAGEMENT . "`.packageTitle,`" . ERP_PACKAGE_MANAGEMENT . "`.packageDuration,`" . ERP_PACKAGE_MANAGEMENT . "`.packageDescription,`" . ERP_PACKAGE_MANAGEMENT . "`.packageBasePrice FROM `" . ERP_PACKAGE_VARIANT . "`,`" . ERP_PACKAGE_MANAGEMENT . "` WHERE `" . ERP_PACKAGE_VARIANT . "`.`packageId`=`" . ERP_PACKAGE_MANAGEMENT . "`.`packageId` AND `" . ERP_PACKAGE_VARIANT . "`.packageVariantId=" . $selectedValue . "";
    
    $getVariant = queryGet($sqlVariant);
    $itemTotalPrice=($getVariant['data']['packageDuration'] / 30) * ($getVariant['data']['variantPrice']);
    // console($getVariant);
    // exit;


?>
    <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getVariant['data']['packageVariantId'] ?>">
        <input type="hidden" name="listItem[<?= $randCode ?>][packageId]" value="<?= $getVariant['data']['packageId'] ?>">
        <input type="hidden" name="listItem[<?= $randCode ?>][packageVariantId]" value="<?= $getVariant['data']['packageVariantId'] ?>">
        <input type="hidden" name="listItem[<?= $randCode ?>][packageTitle]" value="<?= $getVariant['data']['packageTitle'] ?>">
        <input type="hidden" name="listItem[<?= $randCode ?>][packageDuration]" value="<?= $getVariant['data']['packageDuration'] ?>">
        <input type="hidden" name="listItem[<?= $randCode ?>][recharge_type]" value="package"><!--package/addon-->
        <td>
            <input type="hidden" name="listItem[<?= $randCode ?>][packageTitle]" value="<?= $getVariant['data']['packageTitle'] ?>">
            <?= $getVariant['data']['packageTitle'] ?>
        </td>
        <td>
            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][variantTitle]" value="<?= $getVariant['data']['variantTitle'] ?>">
            <?= $getVariant['data']['variantTitle'] ?>
        </td>
        <td>
            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][packageDescription]" value="<?= $getVariant['data']['packageDescription'] ?>">
            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][ocr_limit]" value="<?= $getVariant['data']['OCR'] ?>">
            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][transaction_limit]" value="<?= $getVariant['data']['transaction'] ?>">
            <?= $getVariant['data']['packageDescription'] ?> <i class="fa fa-info-circle" style="cursor: pointer;" title="OCR Limit : <?= $getVariant['data']['OCR']; ?> || Transaction : <?= $getVariant['data']['transaction']; ?>"></i>
        </td>
        <td class="inp-td">
            <div class="d-flex">
                <span class="rupee-symbol currency-symbol pr-1">#</span>
                <input type="number" name="listItem[<?= $randCode ?>][qty]" value="1" class="inp-design full-width itemQty" id="itemQty_<?= $randCode ?>">
            </div>
        </td>
        <td class="inp-td">
            <div class="d-flex">
                <span style="border:none !important" class="rupee-symbol currency-symbol pr-1">&#x20B9;</span>
                <input type="text" name="listItem[<?= $randCode ?>][unitPrice]" value="<?= $itemTotalPrice; ?>" class="inp-design full-width-center itemUnitPrice" style="border:none !important" id="itemUnitPrice_<?= $randCode ?>" readonly>
            </div>
        </td>
        
        <td>
            <input type="hidden" name="listItem[<?= $randCode ?>][totalPrice]" value="<?= $itemTotalPrice ?>" class="form-control full-width-center itemTotalPrice" id="itemTotalPrice_<?= $randCode ?>" readonly>
            <div class="text-success font-weight-bold">
                <span class="rupee-symbol"><?= $currencyIcon ?></span>
                <span class="itemTotalPrice1" id="itemTotalPrice1_<?= $randCode ?>"> <?= $itemTotalPrice ?></span>
            </div>
        </td>
        <td class="action-flex-btn">

            <button type="button" class="btn btn-danger delItemBtn">
                <i class="fa fa-minus" id="delItemBtn_<?= $getVariant['data']['packageVariantId'] ?>" class="delItemBtn mx-1 fa fa-minus"></i>
            </button>

        </td>
    </tr>






<?php
} else {
    echo "Something wrong, try again!";
}
?>