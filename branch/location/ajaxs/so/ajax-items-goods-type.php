<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();

if ($_GET['act'] === "goodsType") {
    $goodsType = $_GET['goodsType'];
?>
    <option value="">Select One</option>
<?php
    if ($goodsType == "material") {
        // $getAllMaterialItems = $BranchSoObj->fetchItemSummaryMaterials();
        $getAllMaterialItems = $BranchSoObj->fetchItemSummaryMaterialsServicesBoth();
        // console($getAllMaterialItems);
        if ($getAllMaterialItems["status"] == "success") {
            $itemSummary = $getAllMaterialItems['data'];
            foreach ($itemSummary as $item) {
                if ($item['bomStatus'] == 1) {
                    $option = '<option value="' . $item["itemId"] . '" disabled title="BOM is not created">' . $item['itemName'] . '<small>(' . $item['itemCode'] . ')</small></option>';
                } else {
                    $option = '<option value="' . $item["itemId"] . '">' . $item['itemName'] . '<small>(' . $item['itemCode'] . ')</small></option>';
                }
                echo $option;
            }
        } else {
            echo '<option value="">Items Type</option>';
        }
    } else if ($goodsType == "service") {
        $getAllServiceItems = $BranchSoObj->fetchItemSummaryServices();
        // console($getAllMaterialItems);

        if ($getAllServiceItems["status"] == "success") {
            $itemSummary = $getAllServiceItems['data'];
            foreach ($itemSummary as $item) {
                if ($item['bomStatus'] == "1") {
                    $option = '<option value="' . $item["itemId"] . '" disabled title="BOM is not created">' . $item['itemName'] . '<small>(' . $item['itemCode'] . ')</small></option>';
                } else {
                    $option = '<option value="' . $item["itemId"] . '">' . $item['itemName'] . '<small>(' . $item['itemCode'] . ')</small></option>';
                }
                echo $option;
            }
        } else {
            echo '<option value="">Somthing Went Wrong!</option>';
        }
    } else if ($goodsType == "project") {
        $getAllServiceItems = $BranchSoObj->fetchItemSummaryServiceProjects();
        // console($getAllMaterialItems);

        if ($getAllServiceItems["status"] == "success") {
            $itemSummary = $getAllServiceItems['data'];
            foreach ($itemSummary as $item) {
                if ($item['bomStatus'] == "1") {
                    $option = '<option value="' . $item["itemId"] . '" disabled title="BOM is not created">' . $item['itemName'] . '<small>(' . $item['itemCode'] . ')</small></option>';
                } else {
                    $option = '<option value="' . $item["itemId"] . '">' . $item['itemName'] . '<small>(' . $item['itemCode'] . ')</small></option>';
                }
                echo $option;
            }
        } else {
            echo '<option value="">Somthing Went Wrong!</option>';
        }
    } else if ($goodsType == "both") {
        $getAllMaterialItems = $BranchSoObj->fetchItemSummaryMaterialsServicesBoth();
        // console($getAllMaterialItems);

        if ($getAllMaterialItems["status"] == "success") {
            $itemSummary = $getAllMaterialItems['data'];
            foreach ($itemSummary as $item) {
                if ($item['bomStatus'] == "1") {
                    $option = '<option value="' . $item["itemId"] . '" disabled title="BOM is not created">' . $item['itemName'] . '<small>(' . $item['itemCode'] . ')</small></option>';
                } else {
                    $option = '<option value="' . $item["itemId"] . '">' . $item['itemName'] . '<small>(' . $item['itemCode'] . ')</small></option>';
                }
                echo $option;
            }
        } else {
            echo '<option value="">Items Type</option>';
        }
    } else {
        echo '<option value="">Please Select Goods</option>';
    }
}
?>