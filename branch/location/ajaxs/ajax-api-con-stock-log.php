<?php
require_once("../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$dbObj = new Database();

$cmpId = 11;
$bId = 11;
$lId = 12;
$REMARK_TYPE = "SNTZ25032025";
$NEW_REMARK_TYPE = "SNTZ25032025-NEW";

// Global GL ID LIST
$RM_GL_ID = 82;
$SFG_GL_ID = 83;
$FG_GL_ID = 84;

$GRIR = 230;

$CONSUMPTION_RM = 183;
$CONSUMPTION_SFG = 184;

$COGS = 181; // GOODS SOLD
$COGM = 182; // FG
$COGM_A = 246; // SFG

$STOCK_DIFF = 241;
$PRICE_DIFF = 242;

function updateStockLogById($stockLogId, $updatedLogArr = [])
{
    global $dbObj;
    global $cmpId;
    global $REMARK_TYPE;

    if ($stockLogId == 0) {
        return ['status' => "error", 'msg' => "Stock Log Not Found"];
    }
    $setParts = [];
    if (isset($updatedLogArr['itemQty']) && isset($updatedLogArr['itemCalQty'])) {
        if ((float)$updatedLogArr['itemQty'] != (float)$updatedLogArr['itemCalQty']) {
            $setParts[] = "itemQty = {$updatedLogArr['itemCalQty']}";
        }
    }

    if (isset($updatedLogArr['itemPrice']) && isset($updatedLogArr['itemCalPrice'])) {
        if ((float)$updatedLogArr['itemPrice'] != (float)$updatedLogArr['itemCalPrice']) {
            $setParts[] = "itemPrice = {$updatedLogArr['itemCalPrice']}";
        }
    }

    if (isset($updatedLogArr['itemUom']) && isset($updatedLogArr['itemCalUomName'])) {

        $itemUomId = $updatedLogArr['itemUom'];
        $itemCalUomName = $updatedLogArr['itemCalUomName'];
        $itemCalUomId = 0;
        $itemUomRes = getUomIdByName($itemCalUomName);
        if ($itemUomRes['status'] == "success") {
            $itemCalUomId = $itemUomRes['data']['uomId'] ?? 0;
            if (($itemUomId != $itemCalUomId) && $itemCalUomId != 0) {
                $setParts[] = "itemUom = {$itemCalUomId}";
            }
        } else {
            $failedMessage = $itemUomRes['msg'];
            $reason = $itemUomRes['sql'];
            insertLog(['prev_log' => '', 'updated_log' => '', 'status' => 'UOM FETCH FAILED', 'msg' => $failedMessage, 'reason' => $reason, 'stock_log_id' => $stockLogId, 'journal_id' => '', 'log_sheet_id' => '']);
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
    // $res = $dbObj->queryUpdate($sql);

    if ($res['status'] == "success") {
        return ['status' => "success", 'msg' => "Stock Log updated", 'sql' => $sql, 'givenArray' => $updatedLogArr];
    } else {
        return ['status' => "error", 'msg' => "Stock Log Not Updated", 'sql' => $sql, 'givenArray' => $updatedLogArr];
    }
}

function itemIdByItemCode($itemCode)
{
    global $dbObj;
    global $cmpId;
    $sql = "SELECT i.itemId as itemId FROM `erp_inventory_items` as i WHERE i.itemCode= '$itemCode' AND i.company_id=$cmpId;";
    return $dbObj->queryGet($sql)['data']['itemId'] ?? 0;
}

function findStockLogDetails($stockLogId)
{
    global $dbObj;
    global $cmpId;

    $sql = "SELECT l.* FROM erp_inventory_stocks_log AS l WHERE l.stockLogId=$stockLogId AND l.companyId=$cmpId;";
    $res = $dbObj->queryGet($sql, true);
    if ($res['status'] == "success" && $res['numRows'] > 0) {
        return ['status' => "success", 'msg' => "Stock Log Data Found", "data" => $res['data'][0]];
    } else {
        return ['status' => "error", 'msg' => "Stock Log Data Not Found", 'sql' => $sql];
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

        return ['status' => "success", 'msg' => "Item Type Found", 'itemTypeId' => $res['data'][0]['goodsType'], 'itemType' => $itemType];
    } else if ($res['status'] == "success" && $res['numRows'] > 1) {
        return ['status' => "warning", 'msg' => "Multiple Item Id Found", 'sql' => $sql, 'data' => $res['data']];
    } else {
        return ['status' => "error", 'msg' => "Item Not Found", 'sql' => $sql];
    }
}

function findJournalIdByDocumentNo($docNumber, $jounralMvtType, $stockLogCreatedAt, $isRevAccountingType)
{
    global $dbObj;
    global $cmpId;

    $sql = "";
    if ($isRevAccountingType == "success") {
        $sql = "SELECT j.id FROM `erp_acc_journal` as j WHERE j.documentNo= '$docNumber' AND j.parent_slug='$jounralMvtType' AND j.company_id=$cmpId  
          AND DATE_FORMAT(j.journal_created_at, '%Y-%m-%d %H:%i') = DATE_FORMAT('$stockLogCreatedAt', '%Y-%m-%d %H:%i') AND  j.remark LIKE '%REV%' AND j.journal_status='active' ";
    } else {
        $sql = "SELECT j.id FROM `erp_acc_journal` as j WHERE (j.documentNo= '$docNumber' OR j.refarenceCode= '$docNumber') AND j.parent_slug='$jounralMvtType' AND j.company_id=$cmpId AND  DATE_FORMAT(j.journal_created_at, '%Y-%m-%d %H:%i') = DATE_FORMAT('$stockLogCreatedAt', '%Y-%m-%d %H:%i') AND j.journal_status='active' ";
    }

    $res = $dbObj->queryGet($sql, true);
    if ($res['status'] = "success" && $res['numRows'] == 1) {
        return ['status' => "success", 'msg' => "Accounting Document Found With Created At", 'journalId' => $res['data'][0]['id'], 'sql' => $sql];
    } else {

        // Now Check Is trtigerd By failed accounting

        $givenArray = ["docNumber" => $docNumber, "journalMvtType" => $jounralMvtType, "stockLogCreatedAt" => $stockLogCreatedAt, "isRevType" => $isRevAccountingType];

        $failedSql = "";
        if ($isRevAccountingType == "success") {
            $failedSql = "SELECT j.id FROM `erp_acc_journal` as j WHERE j.documentNo= '$docNumber' AND j.parent_slug='$jounralMvtType' AND j.company_id=$cmpId AND j.remark LIKE '%REV%' AND j.journal_status='active' ;";
        } else {
            $failedSql = "SELECT j.id FROM `erp_acc_journal` as j WHERE (j.documentNo= '$docNumber' OR j.refarenceCode= '$docNumber') AND j.parent_slug='$jounralMvtType' AND j.company_id=$cmpId  AND j.journal_status='active' ;";
        }

        $failedRes = $dbObj->queryGet($failedSql, true);
        if ($failedRes['status'] = "success" && $failedRes['numRows'] == 1) {
            return ['status' => "success", 'msg' => "Accounting Document But With out Creadted At", 'journalId' => $failedRes['data'][0]['id'], 'sql' => $failedSql];
        } else if ($failedRes['status'] = "success" && $failedRes['numRows'] > 1) {
            return ['status' => "warning", 'msg' => "Multiple Journal Id ", 'sql' => $failedSql, 'data' => $failedRes['data'], 'givenArray' => $givenArray];
        } else {
            return ['status' => "error", 'msg' => "Accounting Document Not Found", 'sql' => $failedSql, 'givenArray' => $givenArray];
        }
    }
}

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

function removeStockLogImpact($dataArr = [])
{
    global $dbObj;
    global $REMOVE_REMARK_TYPE;

    $stockLogId = $dataArr['stockLogId'];
    $res = [];

    $newQty = $dataArr['itemQty'];
    if ($newQty == 0) {
        return ['status' => "error", 'msg' => "Stock log Already Zero", 'givenArray' => $dataArr];
    }

    if ($newQty > 0) {
        $newQty = abs($newQty) * -1;
    } else {
        $newQty = abs($newQty);
    }

    $sql = 'INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $dataArr['companyId'] . ',`branchId`=' . $dataArr['branchId'] . ',`locationId`=' . $dataArr['locationId'] . ',`storageLocationId`=' . $dataArr['storageLocationId'] . ',`storageType`="' . $dataArr['storageType'] . '",`itemId`=' . $dataArr['itemId'] . ',`itemQty`=' . $newQty . ',`itemUom`=' . $dataArr['itemUom'] . ',`itemPrice`=' . $dataArr['itemPrice'] . ',`refActivityName`="' . $dataArr['refActivityName'] . '",`logRef`="' . $dataArr['logRef'] . '",`refNumber`="' . $dataArr['refNumber'] . '",`remarks`="' . $REMOVE_REMARK_TYPE . '",`bornDate`="' . $dataArr['bornDate'] . '",`postingDate`="' . $dataArr['postingDate'] . '", `createdBy`="AUTO", `updatedBy`="AUTO" ';
    // $res = $dbObj->queryInsert($sql);

    if ($res['status'] == "success") {
        return ['status' => "success", 'msg' => "Stock log impact removed", 'givenArray' => $dataArr];
    } else {
        return ['status' => "error", 'msg' => "Stock log delete failed", 'sql' => $sql, 'givenArray' => $dataArr];
    }
}
function removeJournalImpact($journalId, $itemCode)
{
    global $dbObj;
    global $REMOVE_REMARK_TYPE;


    // Try to find subGL in CREDIT
    $creditSql = "SELECT * FROM `erp_acc_credit` WHERE journal_id = $journalId AND subGlCode LIKE '%$itemCode%' AND credit_status='active' ;";
    $creditObj = $dbObj->queryGet($creditSql, true);

    $isSubglOnCreditSide = false;
    $subglRow = null;
    $oppRow = null;

    if ($creditObj['status'] === 'success' && $creditObj['numRows'] > 0) {
        // SubGL is on credit side
        $isSubglOnCreditSide = true;
        $subglRow = $creditObj['data'][0];

        // Get opposite from debit table
        $debitSql = "SELECT * FROM `erp_acc_debit` WHERE journal_id = $journalId AND debit_status='active' ;";
        $debitObj = $dbObj->queryGet($debitSql, true);

        if ($debitObj['status'] !== 'success' || $debitObj['numRows'] == 0) {
            return ['status' => 'error', 'msg' => 'Opposite GL not found in debit table'];
        }

        $oppRow = $debitObj['data'][0];
    } else {
        // Try to find subGL in DEBIT
        $debitSql = "SELECT * FROM `erp_acc_debit` WHERE journal_id = $journalId AND subGlCode LIKE '%$itemCode%' AND debit_status='active';";
        $debitObj = $dbObj->queryGet($debitSql, true);

        if ($debitObj['status'] !== 'success' || $debitObj['numRows'] == 0) {
            return ['status' => 'error', 'msg' => 'SubGL not found on either side'];
        }

        $isSubglOnCreditSide = false;


        $subglRow = $debitObj['data'][0];

        // Get opposite from credit table
        $creditSql = "SELECT * FROM `erp_acc_credit` WHERE journal_id = $journalId AND credit_status='active';";
        $creditObj = $dbObj->queryGet($creditSql, true);

        if ($creditObj['status'] !== 'success' || $creditObj['numRows'] == 0) {
            return ['status' => 'error', 'msg' => 'Opposite GL not found in credit table'];
        }

        $oppRow = $creditObj['data'][0];
    }

    // Now we know if subGL is on credit or debit and we have both rows

    if ($isSubglOnCreditSide) {
        // SubGL is CREDIT → insert reversal as DEBIT
        $debitInsSql = "
            INSERT INTO `erp_acc_debit` (
                `journal_id`, `glId`, `subGlCode`, `subGlName`, `debit_amount`,
                `debit_remark`, `debit_created_by`, `debit_updated_by`
            ) VALUES (
                '$journalId',
                '{$subglRow["glId"]}',
                '{$subglRow["subGlCode"]}',
                '" . addslashes($subglRow["subGlName"]) . "',
                '{$subglRow["credit_amount"]}',
                '$REMOVE_REMARK_TYPE',
                'AUTO',
                'AUTO'
            );";

        $creditInsSql = "
            INSERT INTO `erp_acc_credit` (
                `journal_id`, `glId`, `subGlCode`, `subGlName`, `credit_amount`,
                `credit_remark`, `credit_created_by`, `credit_updated_by`
            ) VALUES (
                '$journalId',
                '{$oppRow["glId"]}',
                '{$oppRow["subGlCode"]}',
                '" . addslashes($oppRow["subGlName"]) . "',
                '{$oppRow["debit_amount"]}',
                '$REMOVE_REMARK_TYPE',
                'AUTO',
                'AUTO'
            );";
    } else {
        // SubGL is DEBIT → insert reversal as CREDIT
        $creditInsSql = "
            INSERT INTO `erp_acc_credit` (
                `journal_id`, `glId`, `subGlCode`, `subGlName`, `credit_amount`,
                `credit_remark`, `credit_created_by`, `credit_updated_by`
            ) VALUES (
                '$journalId',
                '{$subglRow["glId"]}',
                '{$subglRow["subGlCode"]}',
                '" . addslashes($subglRow["subGlName"]) . "',
                '{$subglRow["debit_amount"]}',
                '$REMOVE_REMARK_TYPE',
                'AUTO',
                'AUTO'
            );";

        $debitInsSql = "
            INSERT INTO `erp_acc_debit` (
                `journal_id`, `glId`, `subGlCode`, `subGlName`, `debit_amount`,
                `debit_remark`, `debit_created_by`, `debit_updated_by`
            ) VALUES (
                '$journalId',
                '{$oppRow["glId"]}',
                '{$oppRow["subGlCode"]}',
                '" . addslashes($oppRow["subGlName"]) . "',
                '{$oppRow["credit_amount"]}',
                '$REMOVE_REMARK_TYPE',
                'AUTO',
                'AUTO'
            );";
    }

    $creditInObj = '';
    $debitInObj = '';
    // Execute
    // $creditInObj = $dbObj->queryInsert($creditInsSql, true);
    // $debitInObj = $dbObj->queryInsert($debitInsSql, true);


    if ($creditInObj['status'] == "success" && $debitInObj['status'] === "success") {
        return [
            'status' => "success",
            'msg' => "Accounting Impact Removed",
            'data' => [
                'creditId' => $creditInObj['insertedId'],
                'debitId' => $debitInObj['insertedId'],
                'journalId' => $journalId,
                'subglFoundOn' => $isSubglOnCreditSide ? 'credit' : 'debit'
            ],
            'sql' => ['creditSql' => $creditInsSql ?? '', 'debitSql' => $debitInsSql ?? '']
        ];
    } else {
        return [
            'status' => "error",
            'msg' => "Accounting Impact Not Removed",
            'sql' => ['creditSql' => $creditInsSql ?? '', 'debitSql' => $debitInsSql ?? '']
        ];
    }
}

// Main journal function
function addCreditDebitByJournalId($journalId, $newJournalArray = [], $isRevAccountingType = "warning")
{
    global $dbObj;
    global $REMARK_TYPE;
    global $NEW_REMARK_TYPE;

    global $RM_GL_ID;
    global $FG_GL_ID;
    global $SFG_GL_ID;

    global $GRIR;
    global $COGS;

    global $COGM;
    global $COGM_A;

    global $CONSUMPTION_RM;
    global $CONSUMPTION_SFG;

    global $STOCK_DIFF;

    $creditSql = "SELECT * FROM `erp_acc_credit` WHERE journal_id=$journalId AND credit_status='active';";
    $debitSql = "SELECT * FROM `erp_acc_debit` WHERE journal_id=$journalId AND debit_status='active' ;";

    $creditObj = $dbObj->queryGet($creditSql, true);
    $debitObj = $dbObj->queryGet($debitSql, true);

    $mvtType = getMovementType($newJournalArray['mvtType']);
    // $totalValue = abs((float)inputValue($newJournalArray['totalValue'])) ?? 0;



    // if ($creditObj['status'] != "success" || $debitObj['status'] != "success" || $creditObj['numRows'] == 0 || $debitObj['numRows'] == 0) {

    //     // new logic for credit an debit insertion


    //     // return ['status' => "error", 'msg' => "Accounting Document Credit OR Debit  Not Found", 'sql' => ['creditSql' => $creditSql, 'debitSql' => $debitSql]];
    // }


    // $isValuePositive = $newJournalArray['value'] > 0;

    $creditInsSql = "";
    $debitInsSql = "";

    $accValue = abs((float)inputValue($newJournalArray['value'])) ?? 0;
    if ($accValue == 0) {
        return ['status' => "error", 'msg' => "Invalid value"];
    }

    /*     
        Difference  Value Positive meanse first its is 1000 value but needed 20000 
        Formula:-  value=  (CalQty* Calrate)-(oldQty*oldRate)   
        
        100 - 50  =  +50

        105 - 100 =  -5 
    
    */
    $CrSubGlCode = '';
    $CrSubGlName = '';
    $DrSubGlCode = '';
    $DrSubGlName = '';

    $creditGlId = '';
    $debitGlId = '';

    $itemCode = $newJournalArray['itemCode'];
    $itemName = $newJournalArray['itemName'];
    //  PGI ACCOUNTING
    if ($mvtType == 'PGI') {



        if ($isRevAccountingType == "success") {
            // Reverse Accounting
            $debitPgiSql = "SELECT debit_amount FROM `erp_acc_debit` WHERE journal_id=$journalId AND subGlCode = '$itemCode' AND debit_status='active';";
            $debitPgiObj = $dbObj->queryGet($debitPgiSql, true);
            $numRows = $debitPgiObj['numRows'];
            if ($debitPgiObj['status'] != "success" || $debitPgiObj['numRows'] == 0 || $debitPgiObj['numRows'] > 1) {
                // insert credit & debit both amount side
                $creditrevInsSql5 = "INSERT INTO erp_acc_credit (journal_id, glId,subGlCode,subGlName, credit_amount, credit_remark,credit_created_by, credit_updated_by) VALUES (" . $journalId . ", " . $COGS . ", '', ''," . $accValue . ", '" . $NEW_REMARK_TYPE . "','AUTO','AUTO')";

                $debitrevInsSql5 = "INSERT INTO erp_acc_debit (journal_id, glId,subGlCode,subGlName, debit_amount, debit_remark,debit_created_by, debit_updated_by)  
                VALUES (" . $journalId . ", " . $FG_GL_ID . ",'" . $itemCode . "', '" . $itemName . "'," . $accValue . ", '" . $NEW_REMARK_TYPE . "','AUTO','AUTO')";

                $queryrevObj = $dbObj->queryInsert($creditrevInsSql5);
                $queryrevObj2 = $dbObj->queryInsert($debitrevInsSql5);

                console($queryrevObj);
                console($queryrevObj2);
            } else {
                if ($numRows == 1) {
                    $oldDebitAmount = (float)$debitPgiObj['data'][0]['debit_amount'];
                    if ($accValue != $oldDebitAmount) {
                        $accValue = $oldDebitAmount - $accValue;

                        if ($accValue > $oldDebitAmount) {
                            //debit side
                            $debitGlId = $FG_GL_ID;
                            $creditGlId = $COGS;

                            $DrSubGlCode = $itemCode;
                            $DrSubGlName = $itemName;
                        } else {
                            //credit side
                            $debitGlId = $COGS;
                            $creditGlId = $FG_GL_ID;

                            $CrSubGlCode = $itemCode;
                            $CrSubGlName = $itemName;
                        }
                    }
                } else {
                    echo "<br> Same Item Found More Than One  <br>";
                }
            }
        } else {
            // Forward Accounting
            $creditPgiSql = "SELECT credit_amount FROM `erp_acc_credit` WHERE journal_id=$journalId AND subGlCode = '$itemCode'  AND credit_status='active' ;";

            $creditPgiObj = $dbObj->queryGet($creditPgiSql, true);
            $numRows = $creditPgiObj['numRows'];
            if ($creditPgiObj['status'] != "success" || $creditPgiObj['numRows'] == 0) {
                // insert the credit amount side

                $creditInsSql2 = "INSERT INTO erp_acc_credit (journal_id, glId,subGlCode,subGlName, credit_amount, credit_remark,credit_created_by, credit_updated_by) VALUES (" . $journalId . ", " . $FG_GL_ID . ",'" . $itemCode . "', '" . $itemName . "'," . $accValue . ", '" . $NEW_REMARK_TYPE . "','AUTO','AUTO')";

                $debitInsSql2 = "INSERT INTO erp_acc_debit (journal_id, glId,subGlCode,subGlName, debit_amount, debit_remark,debit_created_by, debit_updated_by) VALUES (" . $journalId . ", " . $COGS . ", '', ''," . $accValue . ", '" . $NEW_REMARK_TYPE . "','AUTO','AUTO')";

                $queryObj = $dbObj->queryInsert($creditInsSql2);
                $queryObj2 = $dbObj->queryInsert($debitInsSql2);

                console($queryObj);
                console($queryObj2);
            } else {
                if ($numRows == 1) {
                    // old 58 , but actual what is 7 (58-51=7)
                    $oldCreditAmount = (float)$creditPgiObj['data'][0]['credit_amount'];
                    if ($accValue != $oldCreditAmount) {
                        $accValue = $oldCreditAmount - $accValue;

                        if ($accValue > $oldCreditAmount) {
                            // credit side
                            $debitGlId = $COGS;
                            $creditGlId = $FG_GL_ID;

                            $CrSubGlCode = $itemCode;
                            $CrSubGlName = $itemName;
                        } else {
                            $debitGlId = $FG_GL_ID;
                            $creditGlId = $COGS;

                            $DrSubGlCode = $itemCode;
                            $DrSubGlName = $itemName;
                        }
                    }
                } else {
                    echo "<br> Same Item Found More Than One  <br>";
                }
            }
        }
    }
    // $itemAssetType = findItemTypeByItemId($newJournalArray['itemId'])['itemType'];

    // GRN || DN      
    // if ($mvtType == 'grn' && $itemAssetType != "ASSET") {
    //     if ($isRevAccountingType=="success") {
    //         if ($isValuePositive) {
    //             $debitGlId = $GRIR;
    //             $creditGlId = $RM_GL_ID;
    //         } else {
    //             $debitGlId = $RM_GL_ID;
    //             $creditGlId = $GRIR;
    //         }
    //     } else {
    //         if ($isValuePositive) {
    //             $debitGlId = $RM_GL_ID;
    //             $creditGlId = $GRIR;
    //         } else {
    //             $debitGlId = $GRIR;
    //             $creditGlId = $RM_GL_ID;
    //         }
    //     }
    // }


    // ASSET GRN
    // if ($mvtType == 'grn' && $itemAssetType == "ASSET") {
    //     $itemParentGl = findParentGlByItemId($newJournalArray['itemId'])['parentGl'];
    //     if ($isRevAccountingType=="success") {
    //         if ($isValuePositive) {
    //             $debitGlId = $GRIR;
    //             $creditGlId = $itemParentGl;
    //         } else {
    //             $debitGlId = $itemParentGl;
    //             $creditGlId = $GRIR;
    //         }
    //     } else {
    //         if ($isValuePositive) {
    //             $debitGlId = $itemParentGl;
    //             $creditGlId = $GRIR;
    //         } else {
    //             $debitGlId = $GRIR;
    //             $creditGlId = $itemParentGl;
    //         }
    //     }
    // }

    // PROD IN FG / SFG
    // if ($mvtType == 'FGSFGDeclaration') {
    //     $itemType = findItemTypeByItemId($newJournalArray['itemId'])['itemType'];
    //     if ($itemType == 'FG') {

    //         if ($isRevAccountingType=="success") {
    //             // Reverse Accounting of FG FOR PROD IN
    //             if ($isValuePositive) {
    //                 $debitGlId = $COGM;
    //                 $creditGlId = $FG_GL_ID;
    //             } else {
    //                 $debitGlId = $FG_GL_ID;
    //                 $creditGlId = $COGM;
    //             }
    //         } else {
    //             // Forward Accounting of FG FOR PROD IN
    //             if ($isValuePositive) {
    //                 $debitGlId = $FG_GL_ID;
    //                 $creditGlId = $COGM;
    //             } else {
    //                 $debitGlId = $COGM;
    //                 $creditGlId = $FG_GL_ID;
    //             }
    //         }
    //     } else if ($itemType == 'SFG') {

    //         if ($isRevAccountingType=="success") {
    //             // Reverse Accounting of SFG FOR PROD IN
    //             if ($isValuePositive) {
    //                 $creditGlId = $COGM_A;
    //                 $debitGlId = $SFG_GL_ID;
    //             } else {
    //                 $debitGlId = $SFG_GL_ID;
    //                 $creditGlId = $COGM_A;
    //             }
    //         } else {
    //             // Forward Accounting of SFG FOR PROD IN
    //             if ($isValuePositive) {
    //                 $debitGlId = $SFG_GL_ID;
    //                 $creditGlId = $COGM_A;
    //             } else {
    //                 $creditGlId = $COGM_A;
    //                 $debitGlId = $SFG_GL_ID;
    //             }
    //         }
    //     }
    // }

    // PROD OUT FG / SFG / RM
    if ($mvtType == 'ProductiondeclarationInventoryissuance') {
        $itemType = findItemTypeByItemId($newJournalArray['itemId'])['itemType'];

        if ($itemType == 'FG') {
            if ($isRevAccountingType == "success") {
                // Reverse Accounting of FG Prod Out




                $debitPgiSql = "SELECT debit_amount FROM `erp_acc_debit` WHERE journal_id=$journalId AND subGlCode = '$itemCode' AND debit_status='active';";
                $debitPgiObj = $dbObj->queryGet($debitPgiSql, true);
                $numRows = $debitPgiObj['numRows'];
                if ($debitPgiObj['status'] != "success" || $debitPgiObj['numRows'] == 0 || $debitPgiObj['numRows'] > 1) {
                    // insert credit & debit both amount side

                    $creditrevInsSql4 = "INSERT INTO erp_acc_credit (journal_id, glId,subGlCode,subGlName, credit_amount, credit_remark,credit_created_by, credit_updated_by) VALUES (" . $journalId . ", " . $CONSUMPTION_SFG . ", '', ''," . $accValue . ", '" . $NEW_REMARK_TYPE . "','AUTO','AUTO')";

                    $debitrevInsSql4 = "INSERT INTO erp_acc_debit (journal_id, glId,subGlCode,subGlName, debit_amount, debit_remark,debit_created_by, debit_updated_by) VALUES (" . $journalId . ", " . $FG_GL_ID . ",'" . $itemCode . "', '" . $itemName . "'," . $accValue . ", '" . $NEW_REMARK_TYPE . "','AUTO','AUTO')";

                    $queryrevObj = $dbObj->queryInsert($creditrevInsSql4);
                    $queryrevObj2 = $dbObj->queryInsert($debitrevInsSql4);

                    console($queryrevObj);
                    console($queryrevObj2);
                } else {
                    if ($numRows == 1) {
                        $oldDebitAmount = (float)$debitPgiObj['data'][0]['debit_amount'];
                        if ($accValue != $oldDebitAmount) {
                            $accValue = $oldDebitAmount - $accValue;



                            if ($accValue > $oldDebitAmount) {
                                //debit side
                                $debitGlId = $FG_GL_ID;
                                $creditGlId = $CONSUMPTION_SFG;

                                $DrSubGlCode = $itemCode;
                                $DrSubGlName = $itemName;
                            } else {
                                //credit side
                                $debitGlId = $CONSUMPTION_SFG;
                                $creditGlId = $FG_GL_ID;

                                $CrSubGlCode = $itemCode;
                                $CrSubGlName = $itemName;
                            }
                        }
                    } else {
                        echo "<br> Same Item Found More Than One  <br>";
                    }
                }
            } else {
                // Forward Accounting of FG Prod Out


                $creditPgiSql = "SELECT credit_amount FROM `erp_acc_credit` WHERE journal_id=$journalId AND subGlCode = '$itemCode'  AND credit_status='active' ;";

                $creditPgiObj = $dbObj->queryGet($creditPgiSql, true);
                $numRows = $creditPgiObj['numRows'];
                if ($creditPgiObj['status'] != "success" || $creditPgiObj['numRows'] == 0) {
                    // insert the credit amount side

                    $creditInsSql3 = "INSERT INTO erp_acc_credit (journal_id, glId,subGlCode,subGlName, credit_amount, credit_remark,credit_created_by, credit_updated_by) VALUES (" . $journalId . ", " . $FG_GL_ID . ",'" . $itemCode . "', '" . $itemName . "'," . $accValue . ", '" . $NEW_REMARK_TYPE . "','AUTO','AUTO')";

                    $debitInsSql3 = "INSERT INTO erp_acc_debit (journal_id, glId,subGlCode,subGlName, debit_amount, debit_remark,debit_created_by, debit_updated_by) VALUES (" . $journalId . ", " . $CONSUMPTION_SFG . ", '', ''," . $accValue . ", '" . $NEW_REMARK_TYPE . "','AUTO','AUTO')";

                    $queryObj = $dbObj->queryInsert($creditInsSql3);
                    $queryObj2 = $dbObj->queryInsert($debitInsSql3);

                    console($queryObj);
                    console($queryObj2);
                } else {
                    if ($numRows == 1) {
                        // old 58 , but actual what is 7 (58-51=7)
                        $oldCreditAmount = (float)$creditPgiObj['data'][0]['credit_amount'];
                        if ($accValue != $oldCreditAmount) {
                            $accValue = $oldCreditAmount - $accValue;

                            if ($accValue > $oldCreditAmount) {
                                // credit side

                                $debitGlId = $CONSUMPTION_SFG;
                                $creditGlId = $FG_GL_ID;

                                $CrSubGlCode = $itemCode;
                                $CrSubGlName = $itemName;
                            } else {

                                $debitGlId = $FG_GL_ID;
                                $creditGlId = $CONSUMPTION_SFG;

                                $DrSubGlCode = $itemCode;
                                $DrSubGlName = $itemName;
                            }
                        }
                    } else {
                        echo "<br> Same Item Found More Than One  <br>";
                    }
                }
            }
        } else if ($itemType == 'SFG') {
            if ($isRevAccountingType == "success") {
                // Reverse Accounting of FG Prod Out


                $debitPgiSql = "SELECT debit_amount FROM `erp_acc_debit` WHERE journal_id=$journalId AND subGlCode = '$itemCode' AND debit_status='active';";
                $debitPgiObj = $dbObj->queryGet($debitPgiSql, true);
                $numRows = $debitPgiObj['numRows'];
                if ($debitPgiObj['status'] != "success" || $debitPgiObj['numRows'] == 0 || $debitPgiObj['numRows'] > 1) {
                    // insert credit & debit both amount side

                    $creditrevInsSql4 = "INSERT INTO erp_acc_credit (journal_id, glId,subGlCode,subGlName, credit_amount, credit_remark,credit_created_by, credit_updated_by) VALUES (" . $journalId . ", " . $CONSUMPTION_SFG . ", '', ''," . $accValue . ", '" . $NEW_REMARK_TYPE . "','AUTO','AUTO')";

                    $debitrevInsSql4 = "INSERT INTO erp_acc_debit (journal_id, glId,subGlCode,subGlName, debit_amount, debit_remark,debit_created_by, debit_updated_by) VALUES (" . $journalId . ", " . $SFG_GL_ID . ",'" . $itemCode . "', '" . $itemName . "'," . $accValue . ", '" . $NEW_REMARK_TYPE . "','AUTO','AUTO')";

                    $queryrevObj = $dbObj->queryInsert($creditrevInsSql4);
                    $queryrevObj2 = $dbObj->queryInsert($debitrevInsSql4);

                    console($queryrevObj);
                    console($queryrevObj2);
                } else {
                    if ($numRows == 1) {
                        $oldDebitAmount = (float)$debitPgiObj['data'][0]['debit_amount'];
                        if ($accValue != $oldDebitAmount) {
                            $accValue = $oldDebitAmount - $accValue;



                            if ($accValue > $oldDebitAmount) {
                                //debit side
                                $debitGlId = $SFG_GL_ID;
                                $creditGlId = $CONSUMPTION_SFG;

                                $DrSubGlCode = $itemCode;
                                $DrSubGlName = $itemName;
                            } else {
                                //credit side
                                $debitGlId = $CONSUMPTION_SFG;
                                $creditGlId = $SFG_GL_ID;

                                $CrSubGlCode = $itemCode;
                                $CrSubGlName = $itemName;
                            }
                        }
                    } else {
                        echo "<br> Same Item Found More Than One  <br>";
                    }
                }
            } else {
                // Forward Accounting of FG Prod Out


                $creditPgiSql = "SELECT credit_amount FROM `erp_acc_credit` WHERE journal_id=$journalId AND subGlCode = '$itemCode'  AND credit_status='active' ;";

                $creditPgiObj = $dbObj->queryGet($creditPgiSql, true);
                $numRows = $creditPgiObj['numRows'];
                if ($creditPgiObj['status'] != "success" || $creditPgiObj['numRows'] == 0) {
                    // insert the credit amount side

                    $creditInsSql3 = "INSERT INTO erp_acc_credit (journal_id, glId,subGlCode,subGlName, credit_amount, credit_remark,credit_created_by, credit_updated_by) VALUES (" . $journalId . ", " . $SFG_GL_ID . ",'" . $itemCode . "', '" . $itemName . "'," . $accValue . ", '" . $NEW_REMARK_TYPE . "','AUTO','AUTO')";

                    $debitInsSql3 = "INSERT INTO erp_acc_debit (journal_id, glId,subGlCode,subGlName, debit_amount, debit_remark,debit_created_by, debit_updated_by) VALUES (" . $journalId . ", " . $CONSUMPTION_SFG . ", '', ''," . $accValue . ", '" . $NEW_REMARK_TYPE . "','AUTO','AUTO')";

                    $queryObj = $dbObj->queryInsert($creditInsSql3);
                    $queryObj2 = $dbObj->queryInsert($debitInsSql3);

                    console($queryObj);
                    console($queryObj2);
                } else {
                    if ($numRows == 1) {
                        // old 58 , but actual what is 7 (58-51=7)
                        $oldCreditAmount = (float)$creditPgiObj['data'][0]['credit_amount'];
                        if ($accValue != $oldCreditAmount) {
                            $accValue = $oldCreditAmount - $accValue;

                            if ($accValue > $oldCreditAmount) {
                                // credit side

                                $debitGlId = $CONSUMPTION_SFG;
                                $creditGlId = $SFG_GL_ID;

                                $CrSubGlCode = $itemCode;
                                $CrSubGlName = $itemName;
                            } else {

                                $debitGlId = $SFG_GL_ID;
                                $creditGlId = $CONSUMPTION_SFG;

                                $DrSubGlCode = $itemCode;
                                $DrSubGlName = $itemName;
                            }
                        }
                    } else {
                        echo "<br> Same Item Found More Than One  <br>";
                    }
                }
            }
        } else if ($itemType == 'RM') {
            if ($isRevAccountingType == "success") {
                $debitPgiSql = "SELECT debit_amount FROM `erp_acc_debit` WHERE journal_id=$journalId AND subGlCode = '$itemCode' AND debit_status='active';";
                $debitPgiObj = $dbObj->queryGet($debitPgiSql, true);
                $numRows = $debitPgiObj['numRows'];
                if ($debitPgiObj['status'] != "success" || $debitPgiObj['numRows'] == 0 || $debitPgiObj['numRows'] > 1) {
                    // insert credit & debit both amount side

                    $creditrevInsSql4 = "INSERT INTO erp_acc_credit (journal_id, glId,subGlCode,subGlName, credit_amount, credit_remark,credit_created_by, credit_updated_by) VALUES (" . $journalId . ", " . $CONSUMPTION_RM . ", '', ''," . $accValue . ", '" . $NEW_REMARK_TYPE . "','AUTO','AUTO')";

                    $debitrevInsSql4 = "INSERT INTO erp_acc_debit (journal_id, glId,subGlCode,subGlName, debit_amount, debit_remark,debit_created_by, debit_updated_by) VALUES (" . $journalId . ", " . $RM_GL_ID . ",'" . $itemCode . "', '" . $itemName . "'," . $accValue . ", '" . $NEW_REMARK_TYPE . "','AUTO','AUTO')";

                    $queryrevObj = $dbObj->queryInsert($creditrevInsSql4);
                    $queryrevObj2 = $dbObj->queryInsert($debitrevInsSql4);

                    console($queryrevObj);
                    console($queryrevObj2);
                } else {
                    if ($numRows == 1) {
                        $oldDebitAmount = (float)$debitPgiObj['data'][0]['debit_amount'];
                        if ($accValue != $oldDebitAmount) {
                            $accValue = $oldDebitAmount - $accValue;
                            if ($accValue > $oldDebitAmount) {
                                //debit side
                                $debitGlId = $RM_GL_ID;
                                $creditGlId = $CONSUMPTION_RM;

                                $DrSubGlCode = $itemCode;
                                $DrSubGlName = $itemName;
                            } else {
                                //credit side
                                $debitGlId = $CONSUMPTION_RM;
                                $creditGlId = $RM_GL_ID;

                                $CrSubGlCode = $itemCode;
                                $CrSubGlName = $itemName;
                            }
                        }
                    } else {
                        echo "<br> Same Item Found More Than One  <br>";
                    }
                }
            } else {
                // Forward Accounting of FG Prod Out


                $creditPgiSql = "SELECT credit_amount FROM `erp_acc_credit` WHERE journal_id=$journalId AND subGlCode = '$itemCode'  AND credit_status='active' ;";

                $creditPgiObj = $dbObj->queryGet($creditPgiSql, true);
                $numRows = $creditPgiObj['numRows'];
                if ($creditPgiObj['status'] != "success" || $creditPgiObj['numRows'] == 0) {
                    // insert the credit amount side

                    $creditInsSql3 = "INSERT INTO erp_acc_credit (journal_id, glId,subGlCode,subGlName, credit_amount, credit_remark,credit_created_by, credit_updated_by) VALUES (" . $journalId . ", " . $RM_GL_ID . ",'" . $itemCode . "', '" . $itemName . "'," . $accValue . ", '" . $NEW_REMARK_TYPE . "','AUTO','AUTO')";

                    $debitInsSql3 = "INSERT INTO erp_acc_debit (journal_id, glId,subGlCode,subGlName, debit_amount, debit_remark,debit_created_by, debit_updated_by) VALUES (" . $journalId . ", " . $CONSUMPTION_RM . ", '', ''," . $accValue . ", '" . $NEW_REMARK_TYPE . "','AUTO','AUTO')";

                    $queryObj = $dbObj->queryInsert($creditInsSql3);
                    $queryObj2 = $dbObj->queryInsert($debitInsSql3);

                    console($queryObj);
                    console($queryObj2);
                } else {
                    if ($numRows == 1) {
                        // old 58 , but actual what is 7 (58-51=7)
                        $oldCreditAmount = (float)$creditPgiObj['data'][0]['credit_amount'];
                        if ($accValue != $oldCreditAmount) {
                            $accValue = $oldCreditAmount - $accValue;

                            if ($accValue > $oldCreditAmount) {
                                // credit side

                                $debitGlId = $CONSUMPTION_RM;
                                $creditGlId = $RM_GL_ID;

                                $CrSubGlCode = $itemCode;
                                $CrSubGlName = $itemName;
                            } else {

                                $debitGlId = $RM_GL_ID;
                                $creditGlId = $CONSUMPTION_RM;

                                $DrSubGlCode = $itemCode;
                                $DrSubGlName = $itemName;
                            }
                        }
                    } else {
                        echo "<br> Same Item Found More Than One  <br>";
                    }
                }
            }
        }
    }

    // stock Transfer Book to physical need to check
    // if ($mvtType == 'stockDifferenceBookToPhysical') {
    //     $itemParentGl = findParentGlByItemId($newJournalArray['itemId'])['parentGl'];
    //     if ($isValuePositive) {
    //         $debitGlId = $STOCK_DIFF;
    //         $creditGlId = $itemParentGl;
    //     } else {
    //         $debitGlId = $itemParentGl;
    //         $creditGlId = $STOCK_DIFF;
    //     }
    // }


    // stock Transfer Material to Material IN OR OUT
    // if ($mvtType == 'stockDifferenceMaterialToMaterial') {

    //     $itemParentGl = findParentGlByItemId($newJournalArray['itemId'])['parentGl'];
    //     $isMatIn = $newJournalArray['mvtType'] !== "MAT-MAT-OUT";

    //     if ($isMatIn) {
    //         $debitInsSql = "INSERT INTO erp_acc_debit (journal_id, glId, debit_amount, debit_remark,debit_created_by, debit_updated_by) VALUES (" . $journalId . ", " . $itemParentGl . ", " . $accValue . ", '" . $REMARK_TYPE . "','AUTO','AUTO')";
    //     } else {
    //         $creditInsSql = "INSERT INTO erp_acc_credit (journal_id, glId, credit_amount, credit_remark,credit_created_by, credit_updated_by) VALUES (" . $journalId . ", " . $itemParentGl . ", " . $accValue . ", '" . $REMARK_TYPE . "','AUTO','AUTO')";
    //     }
    // }

    // if ($mvtType != "stockDifferenceMaterialToMaterial") {

    $accValue = abs($accValue);

    $creditInsSql = "";
    $debitInsSql = "";

    if ($creditGlId != '' && $debitGlId != '' && $accValue > 0) {

        $creditInsSql = "INSERT INTO erp_acc_credit (journal_id, glId,subGlCode,subGlName, credit_amount, credit_remark,credit_created_by, credit_updated_by) VALUES (" . $journalId . ", " . $creditGlId . ",'" . $CrSubGlCode . "', '" . $CrSubGlName . "'," . $accValue . ", '" . $REMARK_TYPE . "','AUTO','AUTO')";

        $debitInsSql = "INSERT INTO erp_acc_debit (journal_id, glId,subGlCode,subGlName, debit_amount, debit_remark,debit_created_by, debit_updated_by) VALUES (" . $journalId . ", " . $debitGlId . ", '" . $DrSubGlCode . "', '" . $DrSubGlName . "'," . $accValue . ", '" . $REMARK_TYPE . "','AUTO','AUTO')";
    }
    // }


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
    } else if (($creditInObj['status'] == "success" || $debitInObj['status'] == "success") && $mvtType == "stockDifferenceMaterialToMaterial") {
        return ['status' => "success", 'msg' => "MAT To MAT Accounting Data Adjusted Successfully", 'type' => 'MAT', 'data' => ['creditId' => $creditInObj['insertedId'], 'debitId' => $debitInObj['insertedId'], 'journalId' => $journalId], 'givenArray' => $newJournalArray, 'sql' => ['creditInSql' => $creditInsSql, 'debitInSql' => $debitInsSql]];
    } else {
        return ['status' => "error", 'msg' => "Accounting Data Not Adjusted", 'sql' => ['creditInSql' => $creditInsSql, 'debitInSql' => $debitInsSql], 'givenArray' => $newJournalArray];
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST['act'] == "logData") {

        $itemCode = $_POST['itemCode'] ?? 0;
        if ($itemCode == "" || $itemCode == 0) {
            echo json_encode(["status" => "error", "msg" => "Item Code Required"]);
            exit();
        }

        $sql = "SELECT slog.* FROM erp_inventory_temp_sanitization_stock_log AS slog WHERE slog.item_code=$itemCode AND slog.mvt_type NOT IN ('CONSUMPTION(BOOK-PHYSICAL)') AND slog.update_status IN ('not update')  ORDER BY slog.temp_sheet_id  DESC";

        $resSql = $dbObj->queryGet($sql, true);
        if ($resSql['status'] == "success" && $resSql['numRows'] > 0) {

            echo "<br>";
            echo "----------------------------  API RUN START $itemCode  -----------------------------";
            echo "<br>";
            echo "Total no of lines in Sheet for this item code $itemCode  : " . $resSql['numRows'];
            echo "<br>";

            // Main activity loop
            foreach ($resSql['data'] as $data) {

                console($data);

                $stockLogId = $data['log_id'] ?? 0;
                $log_sheet_id = $data['temp_sheet_id'] ?? 0;

                $itemName = $data['item_name'] ?? '';

                if ($stockLogId == 0) {
                    echo "<br>";
                    echo "Stock Log Not Found Id ";
                    echo "<br>";
                    continue;
                }

                $journalId = 0;
                $reason = "";
                $updateStatus = "";
                $updateStockLogResponse = "";

                $SHEET_CURRENT_STATUS = $data['update_status'] ?? '';

                $calQty = $data['calQty'];
                $calUom = $data['calUom'];
                $calRate = $data['calrate'];
                $calMap = $data['calMap'];

                if ($calQty == "TRUE" || $calQty == '-') {
                    $calQty = 0;
                }

                if ($calRate == "TRUE" || $calRate == '-') {
                    $calRate = 0;
                }

                if ($calMap == "TRUE" || $calMap == '-') {
                    $calMap = 0;
                }

                if ($calMap == 0 || $calRate == 0 || $calQty == 0) {
                    echo "<br>";
                    echo "Unusual to update this line";
                    echo "<br>";
                }

                $resStockLog = findStockLogDetails($stockLogId);

                echo "<br>";
                echo "Stock LOg Object";
                console($resStockLog);
                echo "<br>";

                if ($resStockLog['status'] == "success") {
                    $resStockLogData = $resStockLog['data'];

                    // it will be used in sant log
                    $updateStockLogResponse = $resStockLogData;

                    $itemId = $resStockLogData['itemId'];
                    $itemOldStockQty = (float)$resStockLogData['itemQty'];
                    $itemOldStockRate = (float)$resStockLogData['itemPrice'];

                    $calQty = (float)$calQty;
                    $calMap = (float)$calMap;

                    $itemOldUomId = $resStockLogData['itemUom'];

                    $stockLogDocNumber = $resStockLogData['refNumber'];
                    $stockLogMvtType = $resStockLogData['refActivityName'];
                    $stockLogCreatedAt = $resStockLogData['createdAt'];

                    $jounralMvtType = getMovementType($stockLogMvtType);

                    $parts = explode('-', $stockLogMvtType);
                    $isRevAccountingType = "warning";
                    if ($parts[0] == 'REV') {
                        $isRevAccountingType = "success";
                    }

                    console("stockLogMvtType");
                    console($stockLogMvtType);
                    console($parts);
                    echo "is rev ";
                    console($isRevAccountingType);



                    echo "<br>";
                    echo "Document Checking";
                    console($stockLogDocNumber);

                    echo "<br>";
                    echo "MVT Type From Stock LOG";
                    console($stockLogMvtType);

                    echo "<br>";
                    echo "Mct Type Journal";
                    console($jounralMvtType);

                    echo "isRevAccountingType";
                    console($isRevAccountingType);

                    if ($jounralMvtType != "not found") {
                        if ($jounralMvtType != "not required") {
                            // if accounting required
                            $resJournal = findJournalIdByDocumentNo($stockLogDocNumber, $jounralMvtType, $stockLogCreatedAt, $isRevAccountingType);

                            echo "<br>";
                            echo "Journal Finding Object";
                            console($resJournal);

                            if ($resJournal['status'] == "success") {
                                // accounting document found
                                echo "<br>";
                                echo "Journal Id Found";

                                $journalId = $resJournal['journalId'];
                                if ($calQty != 0) {
                                    // Stock Log Update part
                                    $stockLogImpact = 0;
                                    // if ($SHEET_CURRENT_STATUS != 'STOCK LOG UPDATE DONE') {

                                    //     $resStockLogUpdate = updateStockLogById($stockLogId, ['itemQty' => $itemOldStockQty, 'itemPrice' => $itemOldStockRate, 'itemUom' => $itemOldUomId, 'itemCalQty' => $calQty, 'itemCalPrice' => $calRate, 'itemCalUomName' => $calUom]);

                                    //     echo "<br>";
                                    //     echo "Stock Log Data";
                                    //     console($resStockLogData);
                                    //     echo "<br>";


                                    //     if ($resStockLogUpdate['status'] == "success") {
                                    //         $stockLogImpact = 1;
                                    //         $failedMessage = '';
                                    //         $reason = '';
                                    //         $updateStatus = "STOCK LOG UPDATE DONE";
                                    //         sheetStatus($log_sheet_id, $updateStatus);
                                    //     } else {

                                    //         $failedMessage = $resStockLogUpdate['msg'];
                                    //         $reson = $resStockLogUpdate['sql'];
                                    //         $updateStatus = "STOCK LOG UPDATE FAILED";

                                    //         if ($failedMessage == "No fields to update") {
                                    //             $updateStatus = "STOCK LOG UPDATE NOT REQUIRED";
                                    //             $reson = $resStockLogUpdate['givenArray'];
                                    //         }

                                    //         sheetStatus($log_sheet_id, $updateStatus);

                                    //         $updateStockLogResponse = '';

                                    //         try {
                                    //             $insRes = insertLog(['prev_log' => $data, 'updated_log' => $updateStockLogResponse, 'status' => $updateStatus, 'msg' => $failedMessage, 'reason' => $reason, 'stock_log_id' => $stockLogId, 'journal_id' => $journalId, 'log_sheet_id' => $log_sheet_id]);

                                    //             if ($insRes['status'] != "success") {
                                    //                 console($res);
                                    //             }
                                    //         } catch (Exception $e) {
                                    //             console($e);
                                    //         }

                                    //         echo "<br>";
                                    //         echo $updateStatus;
                                    //         echo "<br>";
                                    //     }
                                    // }

                                    // Journal Update part
                                    if ($SHEET_CURRENT_STATUS != 'ACCOUNTING DONE') {

                                        // $diffValue = 0;
                                        $diffValue = abs($calQty) * ($calMap);
                                        // if ($stockLogMvtType == "INVOICE" || $stockLogMvtType == "REV-INVOICE"||) {
                                        // } else {
                                        //     $diffValue = (abs($calQty) * abs($calRate)) - (abs($itemOldStockQty) * abs($itemOldStockRate));
                                        // }
                                        // $totalValue = abs($calQty) * abs($calRate);

                                        $newJournalArray = [
                                            'value' => $diffValue,
                                            'mvtType' => $data['mvt_type'],
                                            'itemId' => $itemId,
                                            'itemCode' => $itemCode,
                                            'itemName' => $itemName,
                                        ];

                                        echo "<br>";
                                        echo "Given Array For Accounting";
                                        console($newJournalArray);
                                        $resJournalSides = [];
                                        if (in_array($data['mvt_type'], ['PROD-OUT', 'REV-PROD-OUT', 'INVOICE', 'REV-INVOICE'])) {

                                            $resJournalSides = addCreditDebitByJournalId($journalId, $newJournalArray, $isRevAccountingType);
                                        }

                                        echo "<br>";
                                        echo "Journal Impact";
                                        console($resJournalSides);
                                        echo "<br>";

                                        if ($resJournalSides['status'] == "success") {
                                            $updateStatus = "ACCOUNTING DONE";
                                            sheetStatus($log_sheet_id, $updateStatus);

                                            $failedMessage = $resJournalSides['msg'];
                                            $reason = $resJournalSides['sql'];

                                            try {
                                                $insRes = insertLog(['prev_log' => $data, 'updated_log' => $updateStockLogResponse, 'status' => $updateStatus, 'msg' => $failedMessage, 'reason' => $reason, 'stock_log_id' => $stockLogId, 'journal_id' => $journalId, 'log_sheet_id' => $log_sheet_id]);
                                                if ($insRes['status'] != "success") {
                                                    console($res);
                                                }
                                            } catch (Exception $e) {
                                                console($e);
                                            }
                                        } else {
                                            $updateStatus = "ACCOUNTING Not Adjusted";
                                            sheetStatus($log_sheet_id, $updateStatus);

                                            $failedMessage = $resJournalSides['msg'];
                                            $reason = $resJournalSides['sql'];

                                            try {
                                                $res = insertLog(['prev_log' => $data, 'updated_log' => $updateStockLogResponse, 'status' => $updateStatus, 'msg' => $failedMessage, 'reason' => $reason, 'stock_log_id' => $stockLogId, 'journal_id' => $journalId, 'log_sheet_id' => $log_sheet_id]);
                                                if ($res['status'] != "success") {
                                                    console($res);
                                                }
                                            } catch (Exception $e) {
                                                console($e);
                                            }

                                            echo "<br>";
                                            echo $updateStatus;
                                            echo "<br>";
                                        }
                                    }
                                }
                                // else {

                                //     $updateStatus = "NEED TO REMOVE STOCK LOG AND ACCOUTING IMPACT";
                                //     sheetStatus($log_sheet_id, $updateStatus);

                                //     // REMOVE STOCK IMPACT
                                //     $stockParentRemoveImpact = 0;
                                //     if ($SHEET_CURRENT_STATUS != 'STOCK LOG IMPACT REMOVED' && $calQty == 0) {

                                //         $resStockImp = removeStockLogImpact($resStockLogData);

                                //         echo "<br>";
                                //         echo "Stock Impact OBject";
                                //         console($resStockImp);
                                //         echo "<br>";

                                //         if ($resStockImp['status'] == "success") {
                                //             $stockParentRemoveImpact = 1;
                                //             $updateStatus = "STOCK LOG IMPACT REMOVED";
                                //             sheetStatus($log_sheet_id, $updateStatus);
                                //             $updateStockLogResponse = $resStockImp['data'];
                                //         } else {
                                //             $updateStatus = "STOCK LOG IMPACT NOT REMOVED";
                                //             sheetStatus($log_sheet_id, $updateStatus);

                                //             $failedMessage = $resStockImp['msg'];
                                //             $reason = $resStockImp['sql'];

                                //             try {
                                //                 $resLog = insertLog(['prev_log' => $data, 'updated_log' => $updateStockLogResponse, 'status' => $updateStatus, 'msg' => $failedMessage, 'reason' => $reason, 'stock_log_id' => $stockLogId, 'journal_id' => $journalId, 'log_sheet_id' => $log_sheet_id]);
                                //                 if ($res['status'] != "success") {
                                //                     console($res);
                                //                 }
                                //             } catch (Exception $e) {
                                //                 console($e);
                                //             }
                                //             echo "<br>";
                                //             echo $updateStatus;
                                //             echo "<br>";
                                //         }
                                //     }

                                //     // // REMOVE ACCOUNTING IMPACT
                                //     if ($SHEET_CURRENT_STATUS != 'ACCOUNTING IMPACT REMOVED' && $stockParentRemoveImpact == 1) {

                                //         $resJImp = removeJournalImpact($journalId, $itemCode);

                                //         echo "<br>";
                                //         echo "Journal Impact Removal OBject";
                                //         console($resJImp);

                                //         if ($resJImp['status'] == "success") {
                                //             $updateStatus = "ACCOUNTING IMPACT REMOVED";
                                //             sheetStatus($log_sheet_id, $updateStatus);

                                //             $failedMessage = $resJImp['msg'];
                                //             $reason = $resJImp['sql'];

                                //             try {
                                //                 $resLog = insertLog(['prev_log' => $data, 'updated_log' => $updateStockLogResponse, 'status' => $updateStatus, 'msg' => $failedMessage, 'reason' => $reason, 'stock_log_id' => $stockLogId, 'journal_id' => $journalId, 'log_sheet_id' => $log_sheet_id]);

                                //                 if ($res['status'] != "success") {
                                //                     console($res);
                                //                 }
                                //             } catch (Exception $e) {
                                //                 console($e);
                                //             }
                                //         } else if ($resJImp['status'] == "warning") {

                                //             $updateStatus = "ACCOUNTING IMPACT ALREADY REMOVED";
                                //             sheetStatus($log_sheet_id, $updateStatus);

                                //             $failedMessage = $resJImp['msg'];
                                //             $reason = $resJImp['sql'];

                                //             try {
                                //                 $resLog = insertLog(['prev_log' => $data, 'updated_log' => $updateStockLogResponse, 'status' => $updateStatus, 'msg' => $failedMessage, 'reason' => $reason, 'stock_log_id' => $stockLogId, 'journal_id' => $journalId, 'log_sheet_id' => $log_sheet_id]);

                                //                 if ($res['status'] != "success") {
                                //                     console($res);
                                //                 }
                                //             } catch (Exception $e) {
                                //                 console($e);
                                //             }
                                //         } else {
                                //             $updateStatus = "ACCOUNTING IMPACT NOT REMOVED";
                                //             sheetStatus($log_sheet_id, $updateStatus);

                                //             $failedMessage = $resJImp['msg'];
                                //             $reason = $resJImp['sql'];

                                //             try {
                                //                 $resLog = insertLog(['prev_log' => $data, 'updated_log' => $updateStockLogResponse, 'status' => $updateStatus, 'msg' => $failedMessage, 'reason' => $reason, 'stock_log_id' => $stockLogId, 'journal_id' => $journalId, 'log_sheet_id' => $log_sheet_id]);

                                //                 if ($res['status'] != "success") {
                                //                     console($res);
                                //                 }
                                //             } catch (Exception $e) {
                                //                 console($e);
                                //             }

                                //             echo "<br>";
                                //             echo $updateStatus;
                                //             echo "<br>";
                                //         }
                                //     }
                                // }
                            }
                            // else {

                            //     // if accounting document not found 
                            //     $updateStatus = "STOCK LOG AND ACCOUNTING PENDING";
                            //     $isRev = (stripos($data['mvt_type'], 'rev') !== false);
                            //     if ($isRev) {
                            //         $updateStatus = "STOCK LOG AND REV ACCOUNTING PENDING";
                            //     }

                            //     sheetStatus($log_sheet_id, $updateStatus);

                            //     $failedMessage = $resJournal['msg'];
                            //     $reason = $resJournal['sql'];

                            //     if (isset($resJournal['givenArray'])) {
                            //         $updateStockLogResponse = $resJournal['givenArray'];
                            //     }
                            //     try {
                            //         $res = insertLog(['prev_log' => $data, 'updated_log' => $updateStockLogResponse, 'status' => $updateStatus, 'msg' => $failedMessage, 'reason' => $reason, 'stock_log_id' => $stockLogId, 'journal_id' => $journalId, 'log_sheet_id' => $log_sheet_id]);

                            //         if ($res['status'] != "success") {
                            //             console($res);
                            //         }
                            //     } catch (Exception $e) {
                            //         console($e);
                            //     }

                            //     echo "<br>";
                            //     echo $updateStatus;
                            //     echo "<br>";
                            // }
                        }
                        //  else {
                        //     // IF acc Impact Not required
                        //     if ($calQty != 0) {
                        //         // Stock Log Update part
                        //         if ($SHEET_CURRENT_STATUS != 'STOCK LOG UPDATE DONE') {
                        //             $resStockLogUpdate = updateStockLogById($stockLogId, ['itemQty' => $itemOldStockQty, 'itemPrice' => $itemOldStockRate, 'itemUom' => $itemOldUomId, 'itemCalQty' => $calQty, 'itemCalPrice' => $calRate, 'itemCalUomName' => $calUom]);

                        //             echo "<br>";
                        //             echo "Update Stock Log Stock Object";
                        //             console($resStockLogUpdate);
                        //             echo "<br>";

                        //             if ($resStockLogUpdate['status'] == "success") {
                        //                 $updateStatus = "STOCK LOG UPDATE DONE ACCOUNTING NOT NEED";
                        //                 sheetStatus($log_sheet_id, $updateStatus);
                        //             } else {
                        //                 $updateStatus = "STOCK LOG UPDATE FAILED ACCOUNTING NOT NEED";
                        //                 $failedMessage = $resStockLogUpdate['msg'];
                        //                 $reason = '';
                        //                 if ($failedMessage == "No fields to update") {
                        //                     $updateStatus = "STOCK LOG UPDATE NOT REQUIRED ACCOUNTING NOT NEED";
                        //                     $reason = $resStockLogUpdate['givenArray'];
                        //                 } else {
                        //                     $reason = $resStockLogUpdate['sql'];
                        //                 }
                        //                 sheetStatus($log_sheet_id, $updateStatus);
                        //                 try {
                        //                     $res = insertLog(['prev_log' => $data, 'updated_log' => $updateStockLogResponse, 'status' => $updateStatus, 'msg' => $failedMessage, 'reason' => $reason, 'stock_log_id' => $stockLogId, 'journal_id' => $journalId, 'log_sheet_id' => $log_sheet_id]);

                        //                     if ($res['status'] != "success") {
                        //                         console($res);
                        //                     }
                        //                 } catch (Exception $e) {
                        //                     console($e);
                        //                 }

                        //                 echo "<br>";
                        //                 echo $updateStatus;
                        //                 echo "<br>";
                        //             }
                        //         }
                        //     } else if ($calQty == 0) {
                        //         if ($SHEET_CURRENT_STATUS != 'STOCK LOG IMPACT REMOVED ACCOUNTING NOT NEED') {
                        //             $resStockImp = removeStockLogImpact($resStockLogData);

                        //             echo "<br>";
                        //             echo "Remove Stock Object";
                        //             console($resStockImp);
                        //             echo "<br>";

                        //             if ($resStockImp['status'] == "success") {
                        //                 $updateStatus = "STOCK LOG IMPACT REMOVED ACCOUNTING NOT NEED";
                        //                 sheetStatus($log_sheet_id, $updateStatus);
                        //             } else {
                        //                 $updateStatus = "STOCK LOG NEED TO REMOVED ACCOUNTING NOT NEED";
                        //                 sheetStatus($log_sheet_id, $updateStatus);

                        //                 $failedMessage = $resStockImp['msg'];
                        //                 $reason = $resStockImp['sql'];

                        //                 try {
                        //                     $res = insertLog(['prev_log' => $data, 'updated_log' => $updateStockLogResponse, 'status' => $updateStatus, 'msg' => $failedMessage, 'reason' => $reason, 'stock_log_id' => $stockLogId, 'journal_id' => $journalId, 'log_sheet_id' => $log_sheet_id]);

                        //                     if ($res['status'] != "success") {
                        //                         console($res);
                        //                     }
                        //                 } catch (Exception $e) {
                        //                     console($e);
                        //                 }
                        //                 echo "<br>";
                        //                 echo $updateStatus;
                        //                 echo "<br>";
                        //             }
                        //         }
                        //     }
                        // }
                    }
                    // else {
                    //     $updateStatus = "ACCOUNTING TYPE NOT FOUND";
                    //     sheetStatus($log_sheet_id, $updateStatus);
                    //     $failedMessage = "Check Accounting Type";
                    //     $reason = $data['mvt_type'];
                    //     try {
                    //         $res = insertLog(['prev_log' => $data, 'updated_log' => $updateStockLogResponse, 'status' => $updateStatus, 'msg' => $failedMessage, 'reason' => $reason, 'stock_log_id' => $stockLogId, 'journal_id' => $journalId, 'log_sheet_id' => $log_sheet_id]);

                    //         if ($res['status'] != "success") {
                    //             console($res);
                    //         }
                    //     } catch (Exception $e) {
                    //         console($e);
                    //     }

                    //     echo "<br>";
                    //     echo $updateStatus;
                    //     echo "<br>";
                    // }
                }
                // else {
                //     // if stock log not found
                //     $updateStatus = "STOCK LOG NOT FOUND";
                //     sheetStatus($log_sheet_id, $updateStatus);

                //     $failedMessage = $resStockLog['msg'];
                //     $reason = $resStockLog['sql'];
                //     try {
                //         $res = insertLog(['prev_log' => $data, 'updated_log' => $updateStockLogResponse, 'status' => $updateStatus, 'msg' => $failedMessage, 'reason' => $reason, 'stock_log_id' => $stockLogId, 'journal_id' => $journalId, 'log_sheet_id' => $log_sheet_id]);

                //         if ($res['status'] != "success") {
                //             console($res);
                //         }
                //     } catch (Exception $e) {
                //         console($e);
                //     }

                //     echo "<br>";
                //     echo $updateStatus;
                //     echo "<br>";
                // }
                sheetStatus($log_sheet_id, "API ALready Run");
            }

            echo "<br>";

            echo "-------------------- API RUN END $itemCode -------------------------------";
            echo "<br>";
        } else {
            echo json_encode(["status" => "error", "msg" => "Error!", "sql" => $sql]);
        }
    }
} else {
    echo json_encode(["status" => "error", "msg" => "Error!"]);
}
