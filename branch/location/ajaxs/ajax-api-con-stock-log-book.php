<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-goods-controller.php");
$headerData = array('Content-Type: application/json');
$dbObj = new Database();
$goodsObj = new GoodsController();
$cmpId = 11;

$REMARK_TYPE = "SNTZ25032025";




function itemIdByItemCode($itemCode)
{
    global $dbObj;
    global $cmpId;
    $sql = "SELECT i.itemId as itemId FROM `erp_inventory_items` as i WHERE i.itemCode= '$itemCode' AND i.company_id=$cmpId;";
    return $dbObj->queryGet($sql)['data']['itemId'] ?? 0;
}

function insertLog($inData = [])
{
    global $dbObj;
    global $cmpId;

    $prev_log     = isset($inData['prev_log']) ? json_encode($inData['prev_log']) : '';
    $updated_log  = isset($inData['updated_log']) ? json_encode($inData['updated_log']) : '';
    $reason       = isset($inData['reason']) ? json_encode($inData['reason']) : '';

    $log_sheet_id = isset($inData['log_sheet_id']) ? $inData['log_sheet_id'] : 0;
    $stock_log_id = isset($inData['stock_log_id']) ? addslashes($inData['stock_log_id']) : '';
    $journal_id   = isset($inData['journal_id']) ? addslashes($inData['journal_id']) : '';

    $status       = isset($inData['status']) ? addslashes($inData['status']) : '';
    $failedMessage = isset($inData['msg']) ? addslashes($inData['msg']) : '';

    $sql = "INSERT INTO erp_stock_log_data_sanitization_log (
                prev_log, updated_log, status, failed_message, reason,
                company_id, stock_log_id, journal_id, log_sheet_id
            ) VALUES (
                '" . addslashes($prev_log) . "',
                '" . addslashes($updated_log) . "',
                '$status',
                '$failedMessage',
                '" . addslashes($reason) . "',
                $cmpId,
                '$stock_log_id',
                '$journal_id',
                $log_sheet_id
            );";

    $res = $dbObj->queryInsert($sql);

    if (isset($res['status']) && $res['status'] === "success") {
        return [
            'status' => "success",
            'msg' => "Log generated successfully",
            'insertId' => $res['insertedId'],
            'sql' => $sql,
            'givenArray' => $inData
        ];
    } else {
        return [
            'status' => "error",
            'msg' => "Insert failed",
            'sql' => $sql,
            'givenArray' => $inData
        ];
    }
}

function sheetStatus($logSheetId, $status)
{
    global $dbObj;
    $res = [];
    $sql = "UPDATE `erp_inventory_temp_sanitization_stock_log` SET `update_status`='$status' WHERE  `temp_sheet_id`=$logSheetId;";
    $res = $dbObj->queryUpdate($sql);
    if ($res['status'] == "success") {
        return ['status' => "success", 'msg' => "Sheet Updated successfully", 'sql' => $sql];
    } else {
        return ['status' => "error", 'msg' => "Sheet Update failed", 'sql' => $sql];
    }
}

function findParentGlByItemId($itemId)
{
    global $dbObj;
    global $cmpId;

    $sql = "SELECT i.parentGlId as parentGl FROM `erp_inventory_items` as i WHERE i.itemId= '$itemId' AND i.company_id=$cmpId;";
    $res = $dbObj->queryGet($sql, true);

    if ($res['status'] == "success" && $res['numRows'] == 1) {
        return ['status' => "success", 'msg' => "Item parent Gl Found", 'parentGl' => $res['data'][0]['parentGl']];
    } else if ($res['status'] == "success" && $res['numRows'] > 1) {
        return ['status' => "warning", 'msg' => "Multiple Item parent Gl Found", 'sql' => $sql, 'data' => $res['data']];
    } else {
        return ['status' => "error", 'msg' => "Item parent Gl Not Found", 'sql' => $sql];
    }
}

function findItemTypeByItemId($itemId)
{
    global $dbObj;
    global $cmpId;

    $sql = "SELECT i.goodsType as itemTypeId FROM `erp_inventory_items` as i WHERE i.itemId= '$itemId' AND i.company_id=$cmpId;";
    $res = $dbObj->queryGet($sql, true);

    if ($res['status'] == "success" && $res['numRows'] == 1) {
        $itemTypeId = $res['data'][0]['itemTypeId'] ?? 0;

        $itemType = "";
        if ($itemTypeId == 1) {
            $itemType = "RM";
        } else if ($itemTypeId == 2) {
            $itemType = "SFG";
        } elseif ($itemTypeId == 3) {
            $itemType = "FG";
        } elseif ($itemTypeId == 9) {
            $itemType = "ASSET";
        } elseif ($itemTypeId == 5) {
            $itemType = "ServiceS";
        } elseif ($itemTypeId == 7) {
            $itemType = "ServiceP";
        }

        return ['status' => "success", 'msg' => "Item Type Found", 'itemTypeId' => $itemTypeId, 'itemType' => $itemType];
    } else if ($res['status'] == "success" && $res['numRows'] > 1) {
        return ['status' => "warning", 'msg' => "Multiple Item Id Found", 'sql' => $sql, 'data' => $res['data']];
    } else {
        return ['status' => "error", 'msg' => "Item Not Found", 'sql' => $sql];
    }
}

function getUomIdByName($uomName)
{
    global $dbObj;
    global $cmpId;

    $sql = "SELECT u.uomId FROM `erp_inventory_mstr_uom` as u WHERE u.uomName= '$uomName' AND u.companyId=$cmpId;";
    $res = $dbObj->queryGet($sql);

    if ($res['status'] = "success" && $res['numRows'] > 0) {
        return ['status' => "success", 'msg' => "Data Found", 'uomId' => $res['data']['uomId']];
    } else {
        return ['status' => "error", 'msg' => "UOM Not Found", 'sql' => $sql];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    if ($_POST['act'] == "logDataBookAdd") {

        $FG_WH_OPEN = "189|fgWhOpen";
        $RM_WH_OPEN = "183|rmWhOpen";
        $RM_PROD_OPEN = "185|rmProdOpen";

        $logSheetId = $_POST['logSheetId'] ?? 0;
        if ($logSheetId == "" || $logSheetId == 0) {
            echo json_encode(["status" => "error", "msg" => "Sheet ID Is  Required"]);
            exit();
        }
        if ($company_id != 11) {
            return ['status' => "error", 'msg' => "Company not found"];
        }

        $sql = "SELECT * FROM erp_inventory_temp_sanitization_stock_log AS slog WHERE  slog.mvt_type  IN ('CONSUMPTION(BOOK-PHYSICAL)') AND ( slog.document_no IS NUll OR slog.document_no ='') AND slog.update_status IN ('not update') AND slog.temp_sheet_id=$logSheetId;";
        $resSql = $dbObj->queryGet($sql, true);

        if ($resSql['status'] == "success" && $resSql['numRows'] > 0) {

            echo "<br>";
            echo "--------------------  API RUN START $logSheetId  -------------------------------";
            echo "<br>";

            $data = $resSql['data'][0];
            console($data);


            $itemCode = $data['item_code'] ?? '';
            $calQty = $data['calQty'] ?? 0;
            $calRate = $data['calrate'] ?? 0;
            $calMap = $data['calMap'] ?? 0;
            $calUom = $data['caluom'] ?? '';

            if ($calQty == 0 || $calRate == 0 || $calMap == 0 || $calUom == '' || $itemCode == '') {
                echo " Item Details Missing | $itemCode | $calQty | $calRate | $calMap | $calUom | <br>";
                echo  json_encode(['status' => "error", 'msg' => "Item Details Missing First"]);

                exit();
            }

            $itemName = $data['item_name'];
            $postingDate = $data['posting_date'];

            $itemId = itemIdByItemCode($itemCode) ?? 0;

            $itemParentGlOBj = findParentGlByItemId($itemId);
            $itemParentGlId = $itemParentGlOBj['parentGl'] ?? 0;
            $itemTypeObj = findItemTypeByItemId($itemId);
            $itemTypeId = $itemTypeObj['itemTypeId'] ?? 0;
            $itemGivenUomObj = getUomIdByName($calUom);
            $itemGivenUom = $itemGivenUomObj['uomId'] ?? 0;
            $BATCH_NO = $data['batch_no'] ?? '';

            if ($itemId == 0 || $itemParentGlId == 0 || $itemTypeId == 0 || $itemGivenUom == 0 || $BATCH_NO == '') {
                echo "<br> Item Details Missing | $itemCode | $itemId | $itemParentGlId | $itemTypeId | $itemGivenUom | <br>";
                echo json_encode(['status' => "error", 'msg' => "Item Details Missing"]);
                exit();
            }

            $itemType = $itemTypeObj['itemType'] ?? '';
            $randCode = $itemId . rand(00, 99);
          
            if ($company_id == 11) {
                if ($itemType == "RM") {
                    $storageLocation = "183|rmWhOpen";
                } else if ($itemType == "FG") {
                    $storageLocation = "189|fgWhOpen";
                } else {
                    $storageLocation = "185|rmProdOpen";
                }
            }

            $storageLocation = $data['storage_location'];
            

            $storageLocationId = "";
            
            $location = strtolower($storageLocation);
         

            if ($location == strtolower("FG WH OPEN")) {
                $storageLocationId = $FG_WH_OPEN;
            } else if ($location == strtolower("RM WH OPEN")) {

                $storageLocationId = $RM_WH_OPEN;
            } else if ($location == strtolower("RM PRODUCTION Open")) {
                $storageLocationId = $RM_PROD_OPEN;
            }


            if ($storageLocation == "" || $storageLocationId == "") {
                echo json_encode(['status' => "error", 'msg' => "Storage Location Missing"]);
                exit();
            }

            $stockArray = array(
                'movemenrtypesDropdown' => 'book_to_physical',
                'destinationStorageLocation' => 0,
                'creationDate' => "$postingDate",
                'listItem' => array(
                    $randCode => array(
                        'itemId' => $itemId,
                        'parentGlId' => $itemParentGlId,
                        'goodsType' => "$itemTypeId",
                        'itemCode' => "$itemCode",
                        'itemName' => "$itemName",
                        'itemSellType' => 'CUSTOM',
                        'itemreleasetype' => 'CUSTOM',
                        'manualbatchselection' => array(
                            'storageLocation' => "$storageLocationId",
                            'batchNumber' => "$BATCH_NO",
                            'qty' => $calQty,
                            'givenMap' => $calMap,
                            'bornDate' => ''
                        ),
                        'sign' => '+',
                        'qty' => $calQty,
                        'itemMap' => $calMap,
                        'uom' => $itemGivenUom
                    )
                ),
                'addNewInvoiceFormSubmitBtn' => ''
            );

            echo "GIVEN ARRAY <br>";
            console($stockArray);
            $addNewObj = $goodsObj->direct_consumption_snt($stockArray);
             $updateSheetStatus= $dbObj->queryUpdate("UPDATE erp_inventory_temp_sanitization_stock_log AS lg SET lg.update_status= 'BOOK TO PHYSICAL ACC DONE' WHERE lg.temp_sheet_id = $logSheetId");
             console($updateSheetStatus);
            if (isset($addNewObj['accImpact']) && $addNewObj['accImpact'] == 'success') {
                sheetStatus('BOOK TO PHYSICAL DONE', $logSheetId);
            }

            try {
                $res = insertLog(['prev_log' => $data, 'updated_log' => $addNewObj, 'reason' => $stockArray, 'log_sheet_id' => $logSheetId]);

                if ($res['status'] != "success") {
                    console($res);
                }
            } catch (Exception $e) {
                console($e);
            }

            echo "Result <br>";
            console($addNewObj);


            echo "<br>";
            echo "<br>";
            echo "------------------ API RUN END $logSheetId --------------------------";
            echo "<br>";
            echo "<br>";
        } else {
            echo json_encode(["status" => "error", "msg" => "Error!", "sql" => $sql]);
        }
    }
} else {
    echo json_encode(["status" => "error", "msg" => "Error!"]);
}
