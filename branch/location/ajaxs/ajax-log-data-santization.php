<?php
require_once("../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');

$dbObj = new Database();

// debit and credit fetching
function fetchDebitAndCreditByJournalId($journalId)
{
    global $dbObj;

    $debitSql = "SELECT * FROM erp_acc_debit WHERE journal_id=$journalId";
    $debitObj = $dbObj->queryGet($debitSql, true);

    $creditSql = "SELECT * FROM erp_acc_credit WHERE journal_id=$journalId";
    $creditObj = $dbObj->queryGet($creditSql, true);

    if ($creditObj['status'] == "success" && $debitObj['status'] == "success" && $creditObj['numRows'] > 0 && $debitObj['numRows'] > 0) {
        return ['status' => "success", 'data' => ['credit' => $creditObj['data'], 'debit' => $debitObj['data']]];
    } else {
        return ['status' => "error", 'data' => []];
    }
}

// log data fetching
function fetchFromStockLog($itemId, $invDate, $invNo)
{
    global $dbObj;
    global $company_id;
    $sql = "SELECT lg.* FROM `erp_inventory_stocks_log` as lg WHERE lg.companyId=$company_id AND lg.refActivityName='INVOICE' AND DATE(lg.postingDate) = '" . $invDate . "' AND lg.itemId=$itemId AND lg.refNumber='" . $invNo . "' ORDER BY `stockLogId` DESC;";

    $resSql = $dbObj->queryGet($sql, true);

    if ($resSql['status'] == "success") {
        if ($resSql['numRows'] == 1) {
            return ['status' => 'success', 'msg' => 'Data Found Successfully', 'data' => $resSql['data'][0], 'sql' => $sql];
        } else {
            return ['status' => 'warning', 'msg' => 'Something Went Wrong', 'data' => $resSql['data'], 'sql' => $sql];
        }
    } else {
        return ['status' => 'error', 'msg' => 'data not found', 'data' => [], 'sql' => $sql];
    }
}

// main used function
function fetchCreditSideDataByItemCode($itemCode, $journalId)
{
    global $dbObj;
    $sql = "SELECT * FROM erp_acc_credit WHERE journal_id=$journalId AND subGlCode='" . $itemCode . "' ";
    $res = $dbObj->queryGet($sql, true);
    if ($res['status'] == "success" && $res['numRows'] > 0) {
        return ['status' => 'success', 'msg' => 'Data Found Successfully', 'data' => $res['data'][0], 'sql' => $sql];
    } else {
        return ['status' => 'error', 'msg' => 'Data Not Found', 'data' => [], 'sql' => $sql];
    }
}


// check multi item at same inv
function checkMultiItem($invId, $itemId)
{
    global $dbObj;
    global $company_id;
    global $branch_id;
    global $location_id;
    $returnData = [];
    $sql = "SELECT COUNT(inv_item.inventory_item_id) AS item_count FROM erp_branch_sales_order_invoices AS inv LEFT JOIN erp_branch_sales_order_invoice_items AS inv_item ON inv.so_invoice_id = inv_item.so_invoice_id WHERE inv.company_id = $company_id and inv.branch_id =$branch_id and inv.location_id = $location_id and inv.so_invoice_id = $invId and inv_item.inventory_item_id =$itemId GROUP BY inv.so_invoice_id, inv_item.inventory_item_id;";
    $res = $dbObj->queryGet($sql, true);
    if ($res['status'] == "success" && $res['numRows'] > 0) {
        if ($res['data'][0]['item_count'] > 1) {
            $returnData = ['status' => 'warning', 'msg' => 'same item multiple times'];
        } else if ($res['data'][0]['item_count'] == 1) {
            $returnData = ['status' => 'success', 'msg' => 'Data Found Successfully'];
        } else {
            $returnData = ['status' => 'error', 'msg' => 'something went wrong', 'sql' => $sql];
        }
    } else {
        $returnData = ['status' => 'error', 'msg' => 'something went wrong', 'sql' => $sql];
    }
    return $returnData;
}

// function to insert query into database

function insertData($prevItemPrice, $newItemPrice, $status, $reason, $stockLogId)
{
    global $company_id;
    global $dbObj;

    $sql = "INSERT INTO `erp_stock_log_data_sanitization_log` (`prev_log_itemprice`, `updated_log_itemprice`, `status`, `reason`, `company_id`, `stock_log_id`) VALUES ('" . $prevItemPrice . "', '" . $newItemPrice . "', '" . $status . "', '" . $reason . "', $company_id, '$stockLogId');";
    $res = $dbObj->queryInsert($sql);
    return $res;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST['act'] == "logData") {
        // inv sql
        $totalInvCount=0;
        $sql = "SELECT inv.* FROM erp_branch_sales_order_invoices as inv WHERE inv.company_id=$company_id ORDER BY inv.so_invoice_id DESC";
        $resSql = $dbObj->queryGet($sql, true);

        $count = 0;
        if ($resSql['status'] == "success") {
            if ($resSql['numRows'] > 0) {
                $data = $resSql['data'];
                foreach ($data as $inv) {
                    $totalInvCount++;

                    // main logic start here

                    $invId = $inv['so_invoice_id'];
                    $invNo = $inv['invoice_no'];

                    $invJournalId = $inv['journal_id'];
                    $pgiJounralId = $inv['pgi_journal_id'];

                    $invDate = $inv['invoice_date'];
                    // item sql part
                    $itemSql = "SELECT item.* FROM `erp_branch_sales_order_invoice_items` as item WHERE item.so_invoice_id=$invId";

                    $itemResSql = $dbObj->queryGet($itemSql, true);

                    if ($itemResSql['status'] == "success") {
                        if ($itemResSql['numRows'] > 0) {
                            $itemData = $itemResSql['data'];

                            foreach ($itemData as $oneItem) {
                                $itemId = $oneItem['inventory_item_id'];
                                $itemCode = $oneItem['itemCode'];
                                $itemQty = $oneItem['qty'];
                                $creditSide = fetchCreditSideDataByItemCode($itemCode, $pgiJounralId);
                                $dataFetchLog = fetchFromStockLog($itemId, $invDate, $invNo);

                                if ($dataFetchLog['status'] == "success") {
                                    $logData = $dataFetchLog['data'];
                                    $itemPriceByLog = $logData['itemPrice'];

                                    $stocklogId = $logData['stockLogId'];

                                    if ($creditSide['status'] == "success") {
                                        $creditSideData = $creditSide['data'];

                                        $itemPriceByCredit = round(($creditSideData['credit_amount'] / $itemQty), 2);

                                        echo "\n <br>";
                                        echo "Invoice  NO $invNo";
                                        echo "\n <br>";
                                        echo "Invoice  Id $invId";
                                        echo "\n <br>";

                                        echo "\n <br>";
                                        echo "item code $itemCode";
                                        echo "\n <br>";

                                        echo "credit side item price by credit $itemPriceByCredit";
                                        echo "\n <br>";

                                        echo "item price by log $itemPriceByLog";
                                        echo "\n <br>";


                                        if (($itemPriceByCredit == round($itemPriceByLog, 2))) {
                                            echo "Everything is fine here.";
                                            echo "\n <br>";
                                            echo "\n <br>";
                                            echo "\n <br>";
                                        } else {

                                            if (abs($logData['itemQty']) == abs($itemQty)) {


                                                // last check
                                                $checkMultiItem = checkMultiItem($invId, $itemId);
                                                if ($checkMultiItem['status'] == "warning") {
                                                    insertData($itemPriceByLog, $itemPriceByCredit, "notupdated", "Dupilcate inv id exists", $stocklogId);
                                                    continue;
                                                } else if ($checkMultiItem['status'] == "error") {
                                                    continue;
                                                }

                                                echo "update logic will have to start for $stocklogId";
                                                echo "\n <br>";


                                                $sql = "UPDATE `erp_inventory_stocks_log` as lg SET lg.itemPrice=$itemPriceByCredit WHERE lg.stockLogId=$stocklogId AND lg.companyId=$company_id";

                                                echo "\n <br>";
                                                console($sql);
                                                echo "\n <br>";


                                                $updateRes = $dbObj->queryUpdate($sql);
                                                if ($updateRes['status'] == "success") {
                                                    echo "updating logic will have to start for $stocklogId";
                                                    echo "\n <br>";

                                                    $count = $count + 1;
                                                    insertData($itemPriceByLog, $itemPriceByCredit, "updated", "Value Updated ", $stocklogId);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        
        if ($count > 0) {
            echo json_encode(['status' => "success", "msg" => "Data sanitized successfully", "count" => $count,"totalInvoices" =>$totalInvCount,'sql'=>$sql]);
        } else {
            echo json_encode(['status' => "warning", "msg" => "No Data sanitized", "count" => $count,"totalInvoices" =>$totalInvCount,'sql'=>$sql]);
        }
        
    }
} else {
    echo json_encode(["status" => "error", "msg" => "Error!"]);
}
