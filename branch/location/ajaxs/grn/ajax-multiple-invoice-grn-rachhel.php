<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");


function getStorageLocationListForGrn()
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
    return queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `storage_location_type`="RM-WH" AND `storage_location_material_type`="RM" AND `storage_location_storage_type`="Open" AND `status`="active"', true);
}

function getItemCodeAndHsn($vendorCode, $vendorItemTitle)
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;

    $vendorGoodsCodeObj = queryGet("SELECT `itemCode` FROM `" . ERP_VENDOR_ITEM_MAP . "` WHERE `branchId`='" . $branch_id . "' AND `vendorCode`='" . $vendorCode . "' AND `itemTitle`='" . strip_tags($vendorItemTitle) . "'");
    if ($vendorGoodsCodeObj["status"] == "success") {
        $itemCode = $vendorGoodsCodeObj["data"]["itemCode"];
        $goodsHsnObj = queryGet("SELECT `itemId`, `hsnCode` FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `branch`='" . $branch_id . "' AND `itemCode`='" . $itemCode . "'");
        if ($goodsHsnObj["status"] == "success") {
            return [
                "itemCode" => $itemCode,
                "itemHsn" => $goodsHsnObj["data"]["hsnCode"],
                "itemId" => $goodsHsnObj["data"]["itemId"]
            ];
        } else {
            return [
                "itemCode" => $vendorGoodsCodeObj["data"]["itemCode"],
                "itemHsn" => "",
                "itemId" => ""
            ];
        }
    } else {
        return [
            "itemCode" => "",
            "itemHsn" => "",
            "itemId" => ""
        ];
    }
}


if (!empty(array_filter($_FILES['grnInvoiceFilemultiple']['name']))) {

    $array = array();

    foreach ($_FILES['grnInvoiceFilemultiple']['tmp_name'] as $key => $value) {
        $file_tmpname = $_FILES['grnInvoiceFilemultiple']['tmp_name'][$key];
        $file_name = $_FILES['grnInvoiceFilemultiple']['name'][$key];
        $file_size = $_FILES['grnInvoiceFilemultiple']['size'][$key];

        $allowed_types = ['pdf', 'jpg', 'png', 'jpeg'];
        $maxsize = 2 * 1024 * 1024; // 10 MB

        $uploadedInvoiceObj = uploadFile(["name"=>$file_name, "tmp_name"=>$file_tmpname, "size"=>$file_size], BASE_DIR."branch/bills/",$allowed_types,$maxsize);

        if($uploadedInvoiceObj["status"]=="success"){
            $uploadedInvoiceName=$uploadedInvoiceObj["data"];
            $uploadedInvoiceUrl = BASE_URL."branch/bills/".$uploadedInvoiceName;

            $ocrInvoiceControllerObj = new OcrInvoiceController();

            $invoiceRawData=$ocrInvoiceControllerObj->readInvoice($uploadedInvoiceUrl);

            $invoiceProcessedData = $ocrInvoiceControllerObj->processInvoiceRawData($invoiceRawData["data"],$loginBranchGstin);

            echo json_encode(["row"=>$invoiceRawData, "clean"=>$invoiceProcessedData], true);

        }else{
            // console($uploadedInvoiceObj);
            $array[] = array("status" => "failed", "message" => $uploadedInvoiceObj, "file" => $file_name);
        }
    }
} else {
    echo "No files selected.";
}
