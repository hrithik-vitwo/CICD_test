<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-goods-controller.php");
$headerData = array('Content-Type: application/json');
$dbObj = new Database();
$goodsObj = new GoodsController();
$cmpId = 11;

$REMARK_TYPE = "SNTZ25032025-b2p-Adj";
$STOCK_DIFF = 241;
$PRICE_DIFF = 242;

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

// old function

function getMovementType($str)
{
    $accMap = [
        "GRN" => "grn",
        "REV-GRN" => "grn",
        "CONSUMPTION(BOOK-PHYSICAL)" => "stockDifferenceBookToPhysical",
        "INVOICE" => "PGI",
        "REV-INVOICE" => "PGI",
        "MAT-MAT-IN" => "stockDifferenceMaterialToMaterial",
        "MAT-MAT-OUT" => "stockDifferenceMaterialToMaterial",
        "PROD-IN" => "FGSFGDeclaration",
        "PROD-OUT" => "ProductiondeclarationInventoryissuance",
        "REV-PROD-IN" => "FGSFGDeclaration",
        "REV-PROD-OUT" => "ProductiondeclarationInventoryissuance",
        "CN" => "CustomerCN",
        "CNMANUAL" => "CustomerCN",
        "DN" => "VendorDN",
        "REV-CN" => "CustomerCN",
        "MIGRATION" => "not required",
        "STRGE-LOC" => "not required"
    ];

    if (isset($accMap[$str])) {
        return $accMap[$str];
    } else {
        return "not found";
    }
}
function updateStockLogById($stockLogId, $updatedLogArr = [])
{
    global $dbObj;
    global $cmpId;
    global $REMARK_TYPE;

    if ($stockLogId == 0) {
        return ['status' => "error", 'msg' => "Stock Log Not Found"];
    }
    $setParts = [];


    if (isset($updatedLogArr['itemPrice']) && isset($updatedLogArr['itemCalPrice'])) {
        if ((float)$updatedLogArr['itemPrice'] != (float)$updatedLogArr['itemCalPrice']) {
            $setParts[] = "itemPrice = {$updatedLogArr['itemCalPrice']}";
        }
    }



    if (empty($setParts)) {
        return ['status' => "error", 'msg' => "No fields to update", 'sql' => "", 'givenArray' => $updatedLogArr];
    } else {
        $setParts[] = "remarks = '$REMARK_TYPE'";
    }

    $setClause = implode(", ", $setParts);
    $sql = "UPDATE erp_inventory_stocks_log SET $setClause WHERE stockLogId = '$stockLogId' AND companyId = $cmpId;";
    $res['status'] = "warning";
    $res = $dbObj->queryUpdate($sql);

    if ($res['status'] == "success") {
        return ['status' => "success", 'msg' => "Stock Log updated", 'sql' => $sql, 'givenArray' => $updatedLogArr];
    } else {
        return ['status' => "error", 'msg' => "Stock Log Not Updated", 'sql' => $sql, 'givenArray' => $updatedLogArr];
    }
}

function findStockLogDetails($stockLogId)
{
    global $dbObj;
    global $cmpId;
    global $REMARK_TYPE;

    $sql = "SELECT l.* FROM erp_inventory_stocks_log AS l WHERE l.stockLogId=$stockLogId AND l.companyId=$cmpId AND ( l.remarks IS NULL OR l.remarks NOT IN ('$REMARK_TYPE'))";
    $res = $dbObj->queryGet($sql, true);
    if ($res['status'] == "success" && $res['numRows'] > 0) {
        return ['status' => "success", 'msg' => "Stock Log Data Found", "data" => $res['data'][0]];
    } else {
        return ['status' => "error", 'msg' => "Stock Log Data Not Found", 'sql' => $sql];
    }
}

function addCreditDebitByJournalId($journalId, $newJournalArray = [])
{
    global $dbObj;
    global $REMARK_TYPE;
    global $STOCK_DIFF;

    $creditSql = "SELECT * FROM `erp_acc_credit` WHERE journal_id=$journalId AND credit_status='active';";
    $debitSql = "SELECT * FROM `erp_acc_debit` WHERE journal_id=$journalId AND debit_status='active' ;";

    $creditObj = $dbObj->queryGet($creditSql, true);
    $debitObj = $dbObj->queryGet($debitSql, true);

    if ($creditObj['status'] != "success" || $debitObj['status'] != "success" || $creditObj['numRows'] == 0 || $debitObj['numRows'] == 0) {
        return ['status' => "error", 'msg' => "Accounting Document Credit OR Debit  Not Found", 'sql' => ['creditSql' => $creditSql, 'debitSql' => $debitSql]];
    }

    $isValuePositive = $newJournalArray['value'] > 0;

    $creditInsSql = "";
    $debitInsSql = "";

    $accValue = abs((float)inputValue($newJournalArray['value'])) ?? 0;
    if ($accValue == 0) {
        return ['status' => "error", 'msg' => "Invalid value"];
    }

    /*     
        Difference  Value Positive meanse first its is 1000 value but needed 20000 

        Formula:-  value=  (CalQty* Calrate)-(oldQty*oldRate)   
        
        100 - 50 = +50

        105 - 100 = -5 
    
    */


    // stock Transfer Book to physical need to check
    $itemParentGl = findParentGlByItemId($newJournalArray['itemId'])['parentGl'];
    if ($isValuePositive) {
        $debitGlId = $STOCK_DIFF;
        $creditGlId = $itemParentGl;
    } else {
        $debitGlId = $itemParentGl;
        $creditGlId = $STOCK_DIFF;
    }




    $creditInsSql = "INSERT INTO erp_acc_credit (journal_id, glId, credit_amount, credit_remark,credit_created_by, credit_updated_by) VALUES (" . $journalId . ", " . $creditGlId . ", " . $accValue . ", '" . $REMARK_TYPE . "','AUTO','AUTO')";

    $debitInsSql = "INSERT INTO erp_acc_debit (journal_id, glId, debit_amount, debit_remark,debit_created_by, debit_updated_by) VALUES (" . $journalId . ", " . $debitGlId . ", " . $accValue . ", '" . $REMARK_TYPE . "','AUTO','AUTO')";



    $creditInObj = [];
    $debitInObj = [];

    if ($creditInsSql != "") {
        $creditInObj = $dbObj->queryInsert($creditInsSql, true);
    }
    if ($debitInsSql != "") {
        $debitInObj = $dbObj->queryInsert($debitInsSql, true);
    }


    if ($creditInObj['status'] == "success" && $debitInObj['status'] == "success") {
        return ['status' => "success", 'msg' => "Accounting Data Adjusted Successfully", 'data' => ['creditId' => $creditInObj['insertedId'], 'debitId' => $debitInObj['insertedId'], 'journalId' => $journalId], 'givenArray' => $newJournalArray, 'sql' => ['creditInSql' => $creditInsSql, 'debitInSql' => $debitInsSql]];
    } else {
        return ['status' => "error", 'msg' => "Accounting Data Not Adjusted", 'sql' => ['creditInSql' => $creditInsSql, 'debitInSql' => $debitInsSql], 'givenArray' => $newJournalArray];
    }
}


function findTransferId($time)
{
    global $cmpId;
    global $dbObj;
    $sql = "SELECT s.transfer_id, s.documentNo  FROM erp_stocktransfer AS s   WHERE s.company_id = $cmpId  AND s.created_at = '$time'";
    $res = $dbObj->queryGet($sql, true);

    if ($res['status'] == "success" && $res['numRows'] == 1) {
        return ['status' => "success", 'msg' => "Transfer Id Found", 'transferId' => $res['data'][0]['transfer_id'], 'documentNo' => $res['data'][0]['documentNo']];
    } else {
        return ['status' => "error", 'msg' => "Transfer Id Not Found", 'sql' => $sql];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST['act'] == "logDataBookAdd") {

        $FG_WH_OPEN = "189|fgWhOpen";
        $RM_WH_OPEN = "183|rmWhOpen";
        $RM_PROD_OPEN = "185|rmProdOpen";


        console($_POST);

        $documentNo = $_POST['documentNo'] ?? "";
        $jStatus = $_POST['jStatus'] ?? "";
        if ($documentNo == "" || $jStatus == "") {
            echo json_encode(["status" => "error", "msg" => "Document No AND Journal Status  Required"]);
            exit();
        }


        $sql = "SELECT s.temp_sheet_id AS sheetId, s.item_code AS itemCode,l.itemId AS itemId, s.item_name AS itemName,l.stockLogId,l.postingDate,j.id AS JournalEntryId, l.refNumber AS DocumentNo, s.update_status AS updateStaus, s.qty, s.calQty, s.rate, s.calrate,s.calMap,s.storage_location, s.uom, s.caluom, l.createdAt, j.journal_created_at, CASE WHEN s.qty != s.calQty THEN 'DIFFERENT' ELSE 'SAME' END AS QtyDiff, CASE WHEN s.rate != s.calrate THEN 'DIFFERENT' ELSE 'SAME' END AS RateDiff, CASE WHEN s.uom != s.caluom THEN 'DIFFERENT' ELSE 'SAME' END AS UomDiff FROM erp_inventory_temp_sanitization_stock_log AS s LEFT JOIN erp_inventory_stocks_log AS l ON l.stockLogId = s.log_id LEFT JOIN erp_acc_journal AS j ON ( (j.documentNo = l.refNumber OR j.refarenceCode = l.refNumber) AND j.parent_slug = 'stockDifferenceBookToPhysical' AND j.company_id = $cmpId AND DATE_FORMAT(j.journal_created_at, '%Y-%m-%d %H:%i') = DATE_FORMAT(l.createdAt, '%Y-%m-%d %H:%i') AND j.journal_status = 'active' ) WHERE s.mvt_type IN ('CONSUMPTION(BOOK-PHYSICAL)') AND s.document_no IS NOT NULL AND s.document_no = '$documentNo' ORDER BY s.temp_sheet_id DESC;";
        $resSql = $dbObj->queryGet($sql, true);

        if ($resSql['status'] == "success" && $resSql['numRows'] > 0) {

            echo "<br>";
            echo "--------------------  API RUN START $documentNo  -------------------------------";
            echo "<br>";


            if ($jStatus == "created" || $jStatus == "Created") {

                echo "<br>";
                echo "--------------------   JOURNAL ENTRY ALREADY CREATED $documentNo  -------------------------------";
                echo "<br>";

                foreach ($resSql['data'] as $key => $data) {
                    console($data);
                    if ($data['RateDiff'] == 'DIFFERENT' && $data['JournalEntryId'] != null) {

                        $stockLogId = $data['stockLogId'];
                        $journalId = $data['JournalEntryId'];
                        $sheetId = $data['sheetId'];



                        $stockImpact = 1;
                        $resStockImpact = updateStockLogById($stockLogId, ['itemPrice' => $data['rate'], 'itemCalPrice' => $data['calrate']]);
                        console($resStockImpact);

                        if ($resStockImpact['status'] == 'success') {
                            $stockImpact = 1;
                        }

                        if ($stockImpact == 1) {
                            $stockDetail = findStockLogDetails($stockLogId);

                            if ($stockDetail['status'] != 'suceess') {
                                echo "<br> ---------- Stock Detail not Found ----------<br>";
                                continue;
                            }

                            $itemId = $stockDetail['data']['itemId'];
                            $calQty = $data['calQty'] ?? 0;
                            $calRate = $data['calrate'] ?? 0;
                            $itemOldStockQty = $data['qty'];
                            $itemOldStockRate = $data['rate'];


                            echo " <br >calQty: $calQty, calRate: $calRate, itemOldStockQty: $itemOldStockQty, itemOldStockRate: $itemOldStockRate <br>";
                            if ($calQty == 0 || $calRate == 0) {
                                echo "<br> ---------- Cal Qty or Cal Rate not Found ----------<br>";
                                continue;
                            }

                            $diffValue = (abs($calQty) * abs($calRate)) - (abs($itemOldStockQty) * abs($itemOldStockRate));

                            $newJournalArray = [
                                'value' => $diffValue,
                                'itemId' => $itemId,
                            ];

                            $resJournal = addCreditDebitByJournalId($journalId, $newJournalArray);
                            console($resJournal);

                            if ($resJournal['status'] == 'success') {
                                echo "<br> ---------- Journal Entry Updated ----------<br>";
                                sheetStatus($sheetId, 'B2P Adjusted Succesfully');
                            }
                        }
                    }
                }
            } else {

                echo "<br>";
                echo "--------------------  NEW JOURNAL ENTRY REQUIRED $documentNo  -------------------------------";
                echo "<br>";

                $postingDate = $resSql['data'][0]['postingDate'];

                $trasnferId = rand(111, 999);
                $resTrasn = findTransferId($resSql['data'][0]['createdAt']);
                if ($resTrasn['status'] == 'success') {
                    $trasnferId = $resTrasn['transferId'];
                }

                $stockArray = array(
                    'movemenrtypesDropdown' => 'book_to_physical',
                    'destinationStorageLocation' => 0,
                    'creationDate' => "$postingDate",
                    'transfer_id' => $trasnferId,
                    'documentNo' => $documentNo,
                    'listItem' => array(),
                    'addNewInvoiceFormSubmitBtn' => ''
                );

                foreach ($resSql['data'] as $key => $data) {

                    $stockLogId = $data['stockLogId'];
                    $journalId = $data['JournalEntryId'];
                    $sheetId = $data['sheetId'];

                    $stockDetail = findStockLogDetails($stockLogId);

                    if ($stockDetail['status'] == 'error') {
                        echo "<br> ---------- Stock Detail not Found ----------<br>";
                        continue;
                    }

                    $itemId = $data['itemId'] ?? 0;

                    $itemCode = $data['itemCode'] ?? '';
                    $calQty = $data['calQty'] ?? '';
                    $calRate = $data['calrate'] ?? '';
                    $calMap = $data['calMap'] ?? '';
                    $calUom = $data['caluom'] ?? '';

                    if ($calQty == '' || $calRate == '' || $calMap == '' || $calUom == '' || $itemCode == '') {
                        echo " Item Details Missing | $itemCode | new qty $calQty | new rate $calRate |map  $calMap | uom  $calUom | <br>";
                        echo  json_encode(['status' => "error", 'msg' => "Item Details Missing First"]);
                        exit();
                    }


                    $itemName = $data['itemName'] ?? '';
                    $itemParentGlOBj = findParentGlByItemId($itemId);
                    $itemParentGlId = $itemParentGlOBj['parentGl'] ?? 0;
                    $itemTypeObj = findItemTypeByItemId($itemId);
                    $itemTypeId = $itemTypeObj['itemTypeId'] ?? 0;
                    $itemGivenUomObj = getUomIdByName($calUom);
                    $itemGivenUom = $itemGivenUomObj['uomId'] ?? 0;
                    $BATCH_NO = $stockDetail['data']['logRef'] ?? '';

                    if ($itemId == 0 || $itemParentGlId == 0 || $itemTypeId == 0 || $itemGivenUom == 0 || $BATCH_NO == '') {
                        echo "<br> Item Details Missing | $itemCode | $itemId | $itemParentGlId | $itemTypeId | $itemGivenUom | <br>";
                        echo json_encode(['status' => "error", 'msg' => "Item Details Missing"]);
                        exit();
                    }

                    $itemType = $itemTypeObj['itemType'] ?? '';
                    $randCode = $itemId . rand(00, 99);

                    if ($itemType == "RM") {
                        $storageLocation = "183|rmWhOpen";
                    } else if ($itemType == "FG") {
                        $storageLocation = "189|fgWhOpen";
                    } else {
                        $storageLocation = "185|rmProdOpen";
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

                    $sign = ($calQty > 0) ? '+' : '-';

                    $stockArray['listItem'][$randCode] = array(
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
                        'sign' => "$sign",
                        'qty' => $calQty,
                        'itemMap' => $calMap,
                        'uom' => $itemGivenUom
                    );
                }

                echo "GIVEN ARRAY <br>";
                console($stockArray);

                $addNewObj = $goodsObj->direct_consumption_snt($stockArray);
                console($addNewObj);

                sheetStatus($sheetId, "B2P Journal Added");
            }


            echo "<br>";
            echo "------------------ API RUN END $documentNo --------------------------";
            echo "<br>";
        } else {
            echo json_encode(["status" => "error", "msg" => "Error!", "sql" => $sql]);
        }
    }
} else {
    echo json_encode(["status" => "error", "msg" => "Error!"]);
}
