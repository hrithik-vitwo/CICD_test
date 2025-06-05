<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();
if ($_POST['act'] === "itemStockCheck") {
    $asondate = $_POST['invoicedate'];
    $rowData = json_decode($_POST['rowData']);
    $returnarray = [];
    foreach ($rowData as $key => $data) {
        $type = $_POST['type'];

        if ($type == "?pgi_to_invoice") {
            // $stock = $BranchSoObj->itemQtyStockCheck($data, "'fgMktOpen'", "DESC", "", $asondate);
            
            $stock = $BranchSoObj->itemQtyStockCheckWithAcc($data, "'fgMktOpen'", "DESC", "", $asondate);
        } else {
            
            // $stock = $BranchSoObj->itemQtyStockCheck($data, "'rmWhOpen', 'fgWhOpen'", "DESC", '', $asondate);
            // // console($stock);
            $stock = $BranchSoObj->itemQtyStockCheckWithAcc($data, "'rmWhOpen', 'fgWhOpen'", "DESC", '', $asondate);
            
        }
        // $stock = $BranchSoObj->itemQtyTotalStockCheck($data, "'rmWhOpen', 'fgWhOpen'", $asondate);
        // $getStockqty = $stock['data'][0]['itemQty'] ?? 0;
        $getStockqty = $stock['sumOfBatches'];
        $returnarray[$key] = $getStockqty;
        $responseData['stock'] = $stock;
    }
    $responseData['status'] = "success";
    $responseData['data'] = $returnarray;
    $responseData['stock'] = $stock;
    // print_r('$rowData');
    // print_r($rowData);
} elseif ($_POST['act'] === "approvalTab") {
    $responseData['status'] = "warning";
    $responseData['message'] = 'Something wrong';
} else {
    $responseData['status'] = "warning";
    $responseData['message'] = 'Something wrong, try again!';
}
echo json_encode($responseData);
