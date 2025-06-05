<?php
require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
 global $companyCountry;

$itemsql = "SELECT
    summary.*,
    items.*,
    hsn.taxPercentage AS taxPercentage
FROM `" . ERP_INVENTORY_ITEMS . "` AS items
LEFT JOIN `" . ERP_INVENTORY_STOCKS_SUMMARY . "` AS summary ON items.itemId = summary.itemId
LEFT JOIN `" . ERP_HSN_CODE . "` AS hsn ON items.hsnCode = hsn.hsnCode
WHERE items.goodsType IN (1,2,3,4)
    AND items.status = 'active'
    AND (summary.company_id = $company_id OR summary.company_id IS NULL)
    AND (summary.status = 'active' OR summary.status IS NULL)
    AND items.hsnCode IN (SELECT hsnCode FROM `erp_hsn_code`)
    AND hsn.country_id = $companyCountry
    AND items.company_id = $company_id
    AND summary.movingWeightedPrice > 0
    AND summary.bomStatus IN (0,2)
";
$getAllMaterialItems = queryGet($itemsql, true);

if ($_GET['act'] === "goodsType") {
    $goodsType = $_GET['goodsType'];
?>
    <option value="">Select One</option>
<?php
    if ($goodsType == "material") {
        if ($getAllMaterialItems["status"] == "success") {
            $itemSummary = $getAllMaterialItems['data'];
            foreach ($itemSummary as $item) {
                $option = '<option value="' . $item["itemId"] . '">' . $item['itemName'] . '<small>(' . $item['itemCode'] . ')</small></option>';
                echo $option;
            }
        } else {
            echo '<option value="">Items Type</option>';
        }
    } else {

        if ($getAllMaterialItems["status"] == "success") {
            $itemSummary = $getAllMaterialItems['data'];
            foreach ($itemSummary as $item) {
                $option = '<option value="' . $item["itemId"] . '">' . $item['itemName'] . '<small>(' . $item['itemCode'] . ')</small></option>';

                echo $option;
            }
        } else {
            echo '<option value="">Items Type</option>';
        }
    }
}
?>