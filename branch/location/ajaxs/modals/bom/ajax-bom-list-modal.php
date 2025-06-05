<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../bom/controller/bom.controller.php");
$headerData = array('Content-Type: application/json');



// console($_SESSION);
$bomControllerObj = new BomController();
$bomDetailObj = $bomControllerObj->getBomDetails($itemId);


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $itemId = $_GET['bomid'];
    $bomControllerObj = new BomController();
    $bomDetailObj = $bomControllerObj->getBomDetails($itemId);
    if ($_GET['act'] == "modalData") {
        $itemObj = [];
        $activites = [];
        $overheads = [];

        $itemCode = $bomDetailObj["data"]["bom_data"]["itemCode"];
        $itemName = $bomDetailObj["data"]["bom_data"]["itemName"];
        $preparedBy = ucfirst($bomDetailObj["data"]["bom_data"]["preparedBy"]);
        $preparedDate = formatDateORDateTime($bomDetailObj["data"]["bom_data"]["preparedDate"]);

        foreach ($bomDetailObj["data"]["bom_material_data"] ?? [] as $bomOneItem) {
            $itemObj[] = [
                "itemCode" => $bomOneItem["itemCode"] ?? "-",
                "itemName" => $bomOneItem["itemName"] ?? "-",
                "consumption" => decimalQuantityPreview($bomOneItem["consumption"]),
                "extra" => decimalValuePreview($bomOneItem["extra"]),
                "uom" => $bomOneItem["uom"] ?? "-",
                "rate" => decimalValuePreview($bomOneItem["rate"]),
                "amount" => decimalValuePreview($bomOneItem["amount"]),
                "remarks" => $bomOneItem["remarks"] ?? "-"
            ];
        }

        foreach ($bomDetailObj["data"]["bom_hd_data"] ?? [] as $bomOneItem) {
            $hourlydeploy[] = [
                "sl" => $sl_no += 1,
                "work_center_description" => $bomOneItem["work_center_description"] ?? "-",
                "work_center_code" => $bomOneItem["work_center_code"] ?? "-",
                "head_type" => strtoupper($bomOneItem["head_type"] ?? "-"),
                "consumption" => decimalQuantityPreview($bomOneItem["consumption"]),
                "extra" => decimalValuePreview($bomOneItem["extra"]),
                "uom" => $bomOneItem["uom"] ?? "-",
                "rate" => decimalValuePreview($bomOneItem["rate"]),
                "amount" => decimalValuePreview($bomOneItem["amount"]),
                "remarks" => $bomOneItem["remarks"] ?? "-"
            ];
        }

        foreach ($bomDetailObj["data"]["bom_other_head_data"] ?? [] as $bomOneItem) {
            $overheads[] = [
                "sl" => $sl += 1,
                "work_center_description" => $bomOneItem["work_center_description"] ?? "-",
                "work_center_code" => $bomOneItem["work_center_code"] ?? "-",
                "head_name" => ucfirst($bomOneItem["head_name"] ?? "-"),
                "consumption" => decimalQuantityPreview($bomOneItem["consumption"]),
                "extra" => decimalValuePreview($bomOneItem["extra"]),
                "uom" => $bomOneItem["uom"] ?? "-",
                "rate" => decimalValuePreview($bomOneItem["rate"]),
                "amount" => decimalValuePreview($bomOneItem["amount"]),
                "remarks" => $bomOneItem["remarks"] ?? "-"
            ];
        }

        // console($bomDetailObj);

        $allData = [
            "itemObj" => $itemObj,
            "hourlydeploy" => $hourlydeploy,
            "overheads" => $overheads,
            "itemCode" => $itemCode, 
            "itemName" => $itemName, 
            "preparedBy" => $preparedBy,
            "preparedDate" => $preparedDate,
            "grandMaterialCost" => decimalValuePreview($bomDetailObj["data"]["bom_data"]["cogm_m"]),
            "grandActivityCost" => decimalValuePreview($bomDetailObj["data"]["bom_data"]["cogm_a"]),            
            "grandTotalCost" => decimalValuePreview($bomDetailObj["data"]["bom_data"]["cogm"]),
            "companyCurrency" => getSingleCurrencyType($company_currency)
        ];

        // if (empty($data["itemObj"]) || empty($data["activites"]) || empty($data["overheads"])) {
        //     $res = [
        //         "status" => false,
        //         "msg" => "failed to fetch"
        //     ];
        // } 
        // else {
            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $allData
            ];
        // }

        echo json_encode($res);
    }
}
