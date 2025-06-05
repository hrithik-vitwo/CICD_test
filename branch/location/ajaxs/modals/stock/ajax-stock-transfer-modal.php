<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../../app/v1/functions/branch/func-goods-controller.php");
$headerData = array('Content-Type: application/json');

$itemsController = new ItemsController();
$goodsController = new GoodsController();

if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'modalData') {

    $transfer_id = $_GET['transfer_id'];

    $sql = "SELECT 
    esi.createdBy, 
    esi.itemCode, 
    esi.itemName, 
    esi.item_id,
    esi.qty, 
    esi.uom, 
    esi.dest_item, 
    esi.dest_qty, 
    esi.dest_uom, 
    esi.dest_storage_location, 
    esi.status, 
    es.documentNo,
    es.postingDate,
    es.destination_type
FROM 
    erp_stocktransfer_item esi
LEFT JOIN 
    erp_stocktransfer es 
    ON esi.transfer_id = es.transfer_id
WHERE 
    esi.transfer_id = $transfer_id AND es.company_id = $company_id and es.branch_id = $branch_id and es.location_id = $location_id";



    $sqlMainQryObj = queryGet($sql , true);
    $sql_data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];
    // console($sqlMainQryObj);
    $dynamic_data = [];
    if ($num_list > 0) {
        foreach ($sql_data as $data) {
            // console($data);
        $uomVal = getUomDetail($data['dest_uom'])['data']['uomName'];
        $storageArr =$goodsController->getStoragelocationValue($data['dest_storage_location']);
        if (!empty($storageArr)) {
            $storageArrValue = [
                $storageArr['warehouse_code'] ?? '-',
                $storageArr['storage_location_code'] ?? '-',
                $storageArr['storage_location_name'] ?? '-'
            ];
            $storageArrValueSent = implode(" | ", $storageArrValue);
        } else {
            $storageArrValueSent = "-";
        }   

        $dest_item = [];

        // $dest_item = $ItemsController->getItemById($data['dest_item'])['data'];
        $dest_item = $itemsController->getItemById($data['dest_item'])['data'];
        $current_price = fetchCurrentMwp($data['item_id']);

            $dynamic_data[] = array(
                'documentNo' => $data['documentNo'],
                'postingDate' => formatDateWeb($data['postingDate']),
                'destination_type' => $data['destination_type'],
                'itemCode' => $data['itemCode'],
                'itemName' => $data['itemName'],
                'qty' => decimalQuantityPreview($data['qty']),
                'uom' => getUomDetail($data['uom'])['data']['uomName'],
                'status' => $data['status'],
                "current_price" => decimalValuePreview($current_price),
                "dest_item" => $dest_item['itemName'] ?? "-",
                "dest_itemCode" => $dest_item['itemCode'] ?? "-",
                "dest_qty" => decimalQuantityPreview($data['dest_qty']) ?? "-",
                "dest_uom" => $uomVal ?? "-",
                // "dest_storage_location" => getStoragelocationValue($data['dest_storage_location'])['']
                "dest_storage_location" => $storageArrValueSent,
                'createdBy' => getCreatedByUser($data['createdBy']),
            );
        
        $response = array(
            'status' => true,
            'data' => $dynamic_data,
            'mesage' => 'Data found',
            // "storage_location" => getStoragelocationValue(2)
        );
    }
    }
    else{
        $response = array(
            'status' => false,
            'message' => 'No data found',
            "sql" => $sql,
        );
    }

    echo json_encode($response);
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'stockActivity') {

    $docNo = $_GET['docNo'];
    $sql = "SELECT
    loc.othersLocation_name AS location,
    l.refNumber AS document_no,
    items.itemCode,
    items.itemName,
    grp.goodGroupName AS itemGroup,
    str_loc.storage_location_name AS storage_location,
    l.logRef,
    UOM.uomName AS uom,
    l.refActivityName AS movement_type,
    l.itemQty AS qty,
    l.postingDate AS postingDate,
    l.itemPrice * l.itemQty AS VALUE,
    l.itemPrice AS rate
FROM
    erp_inventory_stocks_log AS l
LEFT JOIN erp_inventory_items AS items
    ON l.itemId = items.itemId
LEFT JOIN erp_inventory_mstr_uom AS UOM
    ON l.itemUom = UOM.uomId
LEFT JOIN erp_storage_location AS str_loc
    ON l.storageLocationId = str_loc.storage_location_id
LEFT JOIN erp_branch_otherslocation AS loc 
    ON l.locationId = loc.othersLocation_id
LEFT JOIN erp_inventory_mstr_good_groups AS grp
    ON items.goodsGroup = grp.goodGroupId
WHERE
    l.refNumber = '$docNo'
    AND l.companyId = $company_id
    AND l.branchId = $branch_id
    AND l.locationId = $location_id
ORDER BY
    l.stockLogId DESC";


    $sqlMainQryObj = queryGet($sql , true);
    $sql_data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];
    // console($sqlMainQryObj);
    // console($sql_data);
    $dynamic_data = [];
    if ($num_list > 0) {
        foreach ($sql_data as $data) {
            // console($data);
            $dynamic_data[] = array(
            "location" => $data['location'],
            "postingdate" => formatDateWeb($data['postingDate']),
            "document_no" => $data['document_no'],
            "itemGroup" => $data['itemGroup'],
            "itemCode" =>  $data['itemCode'],
            "itemName" => $data['itemName'],
            "storage_location" =>  $data['storage_location'],
            "party_code" => $data['party_code']??"-",
            "party_name"=>$data['party_name']??"-",
            "batchNo"=>$data['logRef'],
            "uom"=>$data['uom'],
            "movement_type"=>$data['movement_type'],
            "qty"=>decimalQuantityPreview($data['qty'])??"-",
            "VALUE"=>decimalValuePreview($data['VALUE'])??"-",
            "rate"=>decimalValuePreview($data['rate'])??"-",
            "currency"=>getSingleCurrencyType($company_currency)
            );
        
        $response = array(
            'status' => true,
            'data' => $dynamic_data,
            'mesage' => 'Data found',
        );
    }
    }
    else{
        $response = array(
            'status' => false,
            'message' => 'No data found',
            "sql" => $sql,
        );
    }

    echo json_encode($response);
}