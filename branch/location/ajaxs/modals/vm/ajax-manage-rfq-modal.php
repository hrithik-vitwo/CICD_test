<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-branch-pr-controller.php");

$headerData = array('Content-Type: application/json');
$dbObj = new Database();
$BranchPrObj = new BranchPr();

// function fetchQuantity($itemId,$prId){
//     $sql="SELECT rfqItems.itemQuantity as itemQty FROM `erp_branch_purchase_request_items` as rfqItems WHERE rfqItems.itemId='".$itemId."' AND rfqItems.prId='".$prId."';";
//     return queryGet($sql)['data']['itemQty'];
// }

function fetchQuantity($rfqId){
    $sql = "SELECT rfq.ItemId AS itemId, rfq.deliverySceduleId AS deliverySceduleId, inv.itemName AS itemName, inv.itemCode AS itemCode, invUom.uomId AS uom, prds.qty AS qty FROM erp_rfq_items AS rfq JOIN erp_purchase_register_item_delivery_schedule AS prds ON rfq.deliverySceduleId = prds.pr_delivery_id JOIN erp_inventory_items AS inv ON rfq.itemId = inv.itemId JOIN erp_inventory_mstr_uom AS invUom ON inv.baseUnitMeasure = invUom.uomId WHERE rfq.rfqId = $rfqId";

    $data = queryGet($sql , true);

    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act']=='modalData') {
    $rfqId = $_GET['rfqId'];
    $cond = "rfq.rfqId=$rfqId";
    $sql_list = "SELECT * FROM `erp_rfq_list` AS rfq LEFT JOIN `erp_branch_purchase_request` AS pr ON rfq.prId = pr.purchaseRequestId WHERE $cond AND rfq.company_id = '".$company_id."' AND rfq.branch_id = '".$branch_id."' AND rfq.location_id = '".$location_id."' ORDER BY rfq.rfqId;";
    $sqlMainQryObj = $dbObj->queryGet($sql_list);
    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $dynamic_data = [];

    if ($num_list > 0) {
        $dynamic_data = [];

        // $itemDetails = $BranchPrObj->fetchBranchRFQItemswithQty($rfqId);
        $itemDetails = fetchQuantity($rfqId);
        // console($itemDetails);

        // exit();
        $rfqVendor = $BranchPrObj->fetchRFQVendor($rfqId)['data'];
        $items=[];

        foreach ($itemDetails['data'] as $oneItem) {

            // console($oneItem);
            // $itemQty=fetchQuantity($oneItem['itemId'],$oneItem['prId']);
            // console($resu);
                        
            $items[] = [
                "itemId" => $oneItem['itemId'],
                "itemCode" => $oneItem['itemCode'],
                "itemName" => $oneItem['itemName'],
                "itemQuantity" => $oneItem['qty'],
                "uom" => getUomDetail($oneItem['uom'])['data']['uomName'],
                "qty" => $oneItem['qty'],
            ];
        }
        
        $dynamic_data = [
            "dataObj" => $data,
            "items" => $items,
            "rfqVendor" => $rfqVendor,
            "created_by" => getCreatedByUser($data['created_by']),
            "created_at" => formatDateORDateTime($data['created_at']),
            "updated_by" => getCreatedByUser($data['updated_by']),
            "updated_at" => formatDateORDateTime($data['updated_at']),
        ];

        $res = [
            "status" => true,
            "msg" => "Success",
            "data" => $dynamic_data,
            "sql_list" => $sql_list,
            // "frqItems" => fetchQuantity(241),    
        ];
    } else {
        $res = [
            "status" => false,
            "msg" => "Error!",
            "sql" => $sqlMainQryObj
        ];
    }

    echo json_encode($res);
}else if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'rfqDel') {
    $id = $_GET['id'];
    $sql="UPDATE `erp_rfq_list` SET status='deleted' WHERE rfqId=$id";
    $res =$dbObj->queryUpdate($sql);
    echo json_encode($res);
}
?>