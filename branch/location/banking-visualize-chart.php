<?php
include("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
include("../common/header.php");
include("../common/navbar.php");
include("../common/sidebar.php");
require_once("../common/pagination.php");
include("../../app/v1/functions/company/func-branches.php");
include("../../app/v1/functions/branch/func-branch-pr-controller.php");
require_once("../../app/v1/functions/branch/bankReconciliationStatement.controller.php");
global $company_currency;
$companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
$companyCurrencyData = $companyCurrencyObj["data"];
$currency_name=$companyCurrencyData['currency_name'];

function uploadBankStatement($INPUTS)
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
    $statement_data = json_decode($INPUTS["statement_data"], true) ?? [];
    $statement_file = $INPUTS["statement_file"];
    $statement_bank_id = $INPUTS["statement_bank_id"];
    $column_names = $INPUTS["column_names"];
    $insertErr = 0;
    $tnxType = "all";
    $brsObj = new BankReconciliationStatement($statement_bank_id, $tnxType);

    foreach ($statement_data as $oneRow) {
        $key = -1;
        $set_data = [];
        $tnx_id = $oneRow['Tran Id'];

        // set data  oneRow array convert into insert sql string
        foreach ($oneRow as $column => $value) {
            $key++;

            if (!empty($column_names[$key])) {
                if ($column_names[$key] == "tnx_date") {
                    // Format transaction date
                    $formattedDate = date('Y-m-d', strtotime(str_replace('/', '-', $value)));
                    $set_data[] = $column_names[$key] . '="' . $formattedDate . '"';
                } else if ($column_names[$key] == "Tran Id" || $column_names[$key] == "utr_number") {
                    $value = str_replace(' ', '', $value);
                    $set_data[] = $column_names[$key] . '="' . $value . '"';
                } elseif (in_array($column_names[$key], ["withdrawal_amt", "deposit_amt", "balance_amt"])) {
                    // Handle amounts
                    $amount = floatval(str_replace(",", "", $value));
                    $set_data[] = $column_names[$key] . '=' . $amount;

                    if ($column_names[$key] == "withdrawal_amt" && $amount > 0) {
                        // Process withdrawal
                        $result = $brsObj->matchTransasctionVendor($tnx_id, $statement_bank_id, $amount);
                        if ($result['reconStatus'] == "reconciled") {
                            $set_data[] = 'reconciled_location_id=' . $location_id;
                        }
                        $set_data[] = 'remaining_amt=' . $result['remainingAmt'];
                        $set_data[] = 'reconciled_status="' . $result['reconStatus'] . '"';
                    }

                    if ($column_names[$key] == "deposit_amt" && $amount > 0) {
                        // Process deposit
                        $result = $brsObj->matchTransasctionCustomer($tnx_id, $statement_bank_id, $amount);
                        if ($result['reconStatus'] == "reconciled") {
                            $set_data[] = 'reconciled_location_id=' . $location_id;
                        }
                        $set_data[] = 'remaining_amt=' . $result['remainingAmt'];
                        $set_data[] = 'reconciled_status="' . $result['reconStatus'] . '"';
                    }
                } else {
                    // Handle general columns
                    $set_data[] = $column_names[$key] . '="' . $value . '"';
                }
            }
        }


        $particulars = $oneRow;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://dqncsgydrb.execute-api.ap-south-1.amazonaws.com/default/robertabankstatementver2',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(array(
                "inputs" => array(
                    "question" => "object?",
                    "context" => $particulars
                )
            )),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $responseData = json_decode($response, true);

        if ($responseData["success"] == "true") {

            $insertData = implode(", ", $set_data);
            $prevCondition = implode(" AND ", $set_data);
            if (!empty($prevCondition)) {
                $prevSql = 'SELECT * FROM `erp_bank_statements` WHERE `company_id`=' . $company_id . ' AND ' . $prevCondition;
                $prevObj = queryGet($prevSql);
                if ($prevObj["status"] != "success") {
                    $insertSql = 'INSERT INTO `erp_bank_statements` SET `company_id`=' . $company_id . ', `bank_id`=' . $statement_bank_id . ', `particular_ocr`="", `created_by`="' . $created_by . '", `updated_by`="' . $updated_by . '", ' . $insertData;
                    $insObj = queryInsert($insertSql);
                    if ($insObj["status"] != "success") {
                        $insertErr++;
                    }
                }
            }
        } else {
            $insertData = implode(", ", $set_data);
            $prevCondition = implode(" AND ", $set_data);

            if (!empty($prevCondition)) {
                $prevSql = 'SELECT * FROM `erp_bank_statements` WHERE `company_id`=' . $company_id . ' AND ' . $prevCondition;
                $prevObj = queryGet($prevSql);
                if ($prevObj["status"] != "success") {
                    $insertSql = 'INSERT INTO `erp_bank_statements` SET `company_id`=' . $company_id . ', `bank_id`=' . $statement_bank_id . ', `particular_ocr`="", `created_by`="' . $created_by . '", `updated_by`="' . $updated_by . '", ' . $insertData;
                    $insObj = queryInsert($insertSql);
                    if ($insObj["status"] != "success") {
                        $insertErr++;
                    }
                }
            }
        }
    }

    if ($insertErr == 0) {
        return [
            "status" => "success",
            "message" => "Statement successfully saved",
            "insObj" => $insObj
        ];
    } else {
        return [
            "status" => "error",
            "message" => "Statement not saved, please try again",
            "insObj" => $insObj
        ];
    }
}

if (isset($_POST["submitOcrStatementBtn"])) {
    $uploadObj = uploadBankStatement($_POST);
    swalToast($uploadObj["status"], $uploadObj["message"]);
    try {
        $unserializedData = unserialize($_POST["statement_data"]);
        // Process and display the unserialized data
        var_dump($unserializedData);
    } catch (Throwable $e) {
        // Handle any exceptions or errors that occur during unserialization
        echo "Error during unserialization: " . $e->getMessage();
    }
}
?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/banking.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<style>
    .cursor_pointer:hover {
        cursor: pointer;
    }

    #chartdiv {
        width: 100%;
        height: 500px;
    }

    .upload-button {
        padding: 8px;
    }

    .upload-button input.statement-file-input {
        width: 100%;
        opacity: 0;
    }
</style>


<div class="content-wrapper">
    <section class="content banking-overview">
        <div class="container-fluid">
            <div class="head">
                <h2 class="text-lg font-bold">Banking Overview</h2>
            </div>
            <?php


            //Action On Bank Statement Refresh Form Submit
            if (isset($_POST["bankStatementRefreshSubmitBtn"])) {
                $dbObj = new Database(true);
                $bankId = $_POST["bankId"] ?? 0;
                $lastStatementObj = $dbObj->queryGet("SELECT * FROM `erp_bank_statements` WHERE `company_id`=$company_id AND `bank_id`=$bankId ORDER BY `id` DESC LIMIT 1");
                if ($lastStatementObj["status"] == "success") {
                    $lastStatementDate = $lastStatementObj["data"]["tnx_date"];
                    $statementFormDate = date("d-m-Y", strtotime("$lastStatementDate -1 day"));
                    $statementToDate = date("d-m-Y");
                } else {
                    $statementFormDate = date("01-m-Y");
                    $statementToDate = date("d-m-Y");
                }
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://one.vitwo.ai/api/icici/api-icici-bank-statement.php?formDate=$statementFormDate&toDate=$statementToDate",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                $statementResponseObj = json_decode($response, true);

                // $statementResponseObj = [
                //     "CORP_ID" => "551590232",
                //     "USER_ID" => "USER1",
                //     "AGGR_ID" => "OTOE0627",
                //     "URN" => "SR235530139",
                //     "ACCOUNTNO" => "035505500681",
                //     "Record" => [
                //         [
                //             "CHEQUENO" => "",
                //             "TXNDATE" => "01-04-2024 16:07:32",
                //             "REMARKS" => "INF/INFT/035816696311/NB/XYZ",
                //             "AMOUNT" => "1,000.00",
                //             "BALANCE" => "-2,11,436.01",
                //             "VALUEDATE" => "01-04-2024",
                //             "TYPE" => "DR",
                //             "TRANSACTIONID" => "S11239244"
                //         ]
                //     ],
                //     "RESPONSE" => "SUCCESS"
                // ];
                $brsObj = new BankReconciliationStatement($bankId, $tnxType);

                $lastRecordDateTime = "";
                foreach ($statementResponseObj["Record"] as $record) {
                    $TRANSACTIONID = $record["TRANSACTIONID"];
                    $TXNDATE = $record["TXNDATE"];
                    $VALUEDATE = date("Y-m-d", strtotime($record["VALUEDATE"]));
                    $REMARKS = $record["REMARKS"];
                    $AMOUNT = floatval(str_replace(",", "", $record["AMOUNT"]));
                    $BALANCE = floatval(str_replace(",", "", $record["BALANCE"]));
                    $depositAmt = 0;
                    $withdrawalAmt = 0;
                    $remainingAmt = 0;
                    $recQuery = "";
                    if ($record["TYPE"] == "DR") {
                        $withdrawalAmt = $AMOUNT;
                        $response = $brsObj->matchTransasctionVendor($TRANSACTIONID, $bankId, $withdrawalAmt);
                        $remainingAmt = $response["remainingAmt"];
                        $recStatus = $response["reconStatus"];
                        
                        if ($result['reconStatus'] == "reconciled") {
                            $recQuery="`reconciled_status`='" . $recStatus . "',`reconciled_location_id`= $location_id";
                        }else{
                            $recQuery="`reconciled_status`='" . $recStatus . "'";
                        }
                    } else if ($record["TYPE"] == "CR") {
                        $depositAmt = $AMOUNT;
                        $response = $brsObj->matchTransasctionCustomer($TRANSACTIONID, $bankId, $depositAmt);
                        $remainingAmt = $response["remainingAmt"];
                        $recStatus = $response["reconStatus"];
                        
                        if ($result['reconStatus'] == "reconciled") {
                            $recQuery="`reconciled_status`='" . $recStatus . "',`reconciled_location_id`= $location_id";
                        }else{
                            $recQuery="`reconciled_status`='" . $recStatus . "'";
                        }
                    }

                    $prevObj = $dbObj->queryGet("SELECT * FROM `erp_bank_statements` WHERE `company_id`=$company_id AND `utr_number`='$TRANSACTIONID' AND `bank_id`=$bankId");
                    if ($prevObj["status"] != "success") {
                        $insObj = $dbObj->queryInsert("INSERT INTO `erp_bank_statements` SET `company_id`=$company_id,`bank_id`=$bankId,`tnx_date`='$VALUEDATE',`particular`='$REMARKS',`utr_number`='$TRANSACTIONID',`withdrawal_amt`=$withdrawalAmt,`deposit_amt`=$depositAmt,`remaining_amt`=$remainingAmt,$recQuery,`balance_amt`=$BALANCE,`upload_type`='auto', `created_by`='$created_by',`updated_by`='$updated_by'");
                    }
                    $lastRecordDateTime = $TXNDATE;
                }

                $resultObj = $dbObj->queryFinish();
                if ($resultObj["status"] == "success") {
                    if (count($resultObj["data"]) > 0) {
                        swalAlert("success", "Success!", "Statement is updated and last record time: " . $lastRecordDateTime);
                    } else {
                        swalAlert("success", "Success!", "Statement is alredy updated!");
                    }
                } else {
                    swalAlert("warning", "Warning!", "Statement update failed!");
                }
                console($resultObj);
            }
            $brsObj = new BankReconciliationStatement($bankId, $tnxType);

            $bankList = $brsObj->getBankList();
            // console($bankList);
            ?>
            <div id="focus">
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <div class="card card-tabs rounded-5 account-details bg-light mt-4">
                            <div class="card-header bg-transparent justify-content-between border-bottom rounded-0">
                                <div id="change_bank" class="form-control w-auto border-0 bg-transparent">
                                    <p class="text-sm">All Banks</p>
                                </div>
                                <div class="balance-blocks">
                                    <div class="box green">
                                        <div class="icon">
                                            <img src="../../public/assets/img/bank-balance.png" alt="">
                                        </div>
                                        <div class="desc">
                                            <p>Amount as per banks</p>
                                            <p id="bank_amount" class="font-bold"><?=$currency_name?> 0.00</p>
                                        </div>
                                    </div>
                                    <div class="box pink">
                                        <div class="icon">
                                            <img src="../../public/assets/img/card-balance.png" alt="">
                                        </div>
                                        <div class="desc">
                                            <p>Amount as per books</p>
                                            <p class="font-bold" id="book_amount"><?=$currency_name?> 0.00</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="right-blocks">
                                    <div class="add-bank-btn">
                                        <a href="add-new-bank.php" class="btn btn-primary btn-sm">Add bank</a>
                                    </div>
                                    <?php
                                    if (date("m/d/Y") > date("04/01/Y")) {
                                        $date_picker = date("04/01/Y") . " - " . date("m/d/Y");
                                    } else {
                                        $year = date("Y", strtotime("-1 year"));
                                        $date_picker =  date("04/01/" . $year) . " - " . date("04/01/Y");
                                    }
                                    ?>
                                    <div class="date-picker-section">
                                        <input type="text" id="changeDurationDropdownId" class="form-control" name="daterange" value="<?= $date_picker ?>" />
                                        <script>
                                            var root;
                                            $(function() {

                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body bg-transparent" style="overflow: hidden;">
                                <div class="row p-4">
                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div id="chartdiv" class="chartContainer"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        function getMonthsAndDatesInRange($startDate, $endDate)
                        {
                            $start = new DateTime($startDate);
                            $end = new DateTime($endDate);
                            $monthsAndDates = array();
                            while ($start <= $end) {
                                $month = $start->format('m');
                                $date = $start->format('d');
                                $year = $start->format('Y');
                                $month_in_words = $start->format('M');
                                $proper_start_date = new DateTime($year . "-" . $month . "-" . $date);
                                $proper_start_date->modify('last day of this month');
                                // Determine the target date for each month
                                if ($month == $end->format('m') && $year == $end->format('Y')) {
                                    $last_date = $end->format('d');
                                } else {
                                    $last_date = $proper_start_date->format('d');
                                }
                                $monthsAndDates[] = array("month_in_words" => $month_in_words, "first_date" => array('month' => $month, 'date' => $date, 'year' => $year), "last_date" => array('month' => $proper_start_date->format('m'), 'date' => $last_date, 'year' => $proper_start_date->format('Y')));
                                $start->modify('first day of next month');
                            }
                            return $monthsAndDates;
                        }

                        $startDate = date("Y-04-01");
                        $endDate = date("Y-m-d");

                        $monthsAndDatesInRange = getMonthsAndDatesInRange($startDate, $endDate);
                        $graph_date_wise = [];

                        foreach ($monthsAndDatesInRange as $item) {
                            $month = $item["last_date"]['month'];
                            $date = $item["last_date"]['date'];
                            $year = $item["last_date"]['year'];
                            $month_in_words = $item["month_in_words"] . "," . $year;

                            $start_date = $item["first_date"]['year'] . "-" . $item["first_date"]['month'] . "-" . $item["first_date"]['date'];
                            $end_date = $item["last_date"]['year'] . "-" . $item["last_date"]['month'] . "-" . $item["last_date"]['date'];

                            $bank_amount = $brsObj->getBankAmountgraph($start_date, $end_date)["data"]["amount"] ?? 0;
                            $book_amount = $brsObj->getBooksAmountgraph($start_date, $end_date);

                            $graph_date_wise[] = array("month" => $month_in_words, "book" => $book_amount, "bank" => $bank_amount == "" ? 0 : $bank_amount);
                        }
                        // console($graph_date_wise);
                        ?>
                        <div class="card-body">
                            <table class="table list-table active-accounts">
                                <thead>
                                    <tr>
                                        <th>Account Details</th>
                                        <th>Reports</th>
                                        <th>Uncategorized</th>
                                        <th class="text-right">Amount as per banks</th>
                                        <th class="text-right">Amount as per books</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_data">
                                    <?php
                                    $bank_sum = 0;
                                    $book_sum = 0;
                                    $graph = [];
                                    foreach ($brsObj->getBankList()["data"] as $key => $listItem) {
                                        $bank_id = $listItem["id"];
                                        $bank_amt = $brsObj->getBankAmount($listItem["id"])["data"]["balance_amt"];
                                        $bank_amt_count = $brsObj->getBankAmount($listItem["id"])["numRows"];
                                        $book_amt = $brsObj->getBooksAmount($listItem["parent_gl"], $listItem["acc_code"]);
                                        $pgl = $listItem["parent_gl"];
                                        $subgl = $listItem["acc_code"];
                                    ?>
                                        <tr>
                                            <td>
                                                <a href="<?= LOCATION_URL ?>banking-transaction.php?act=all&bank=<?= base64_encode(base64_encode(base64_encode($bank_id))) ?>">
                                                    <?= $listItem["bank_name"] ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#focus" class="report_link" data-pgl=<?= $pgl ?> data-subgl="<?= $subgl ?>" data-bankid="<?= $bank_id ?>" data-bank="<?= $bank_amt_count == 0 ? 0.00 : $bank_amt ?>" data-book="<?= $book_amt == "" ? 0.00 : $book_amt ?>" data-bankname="<?= $listItem["bank_name"] . " (" . $listItem["account_no"] . ")" ?>">
                                                    <i class="fa fa-chart-line cursor_pointer"></i>
                                                </a>
                                            </td>
                                            <?php
                                            $count =  $brsObj->getUncategorizedCount($listItem["id"])["numRows"];

                                            if ($count != 0) {
                                            ?>
                                                <td><span class="text-danger"><?= $count ?> transactions</span></td>
                                            <?php
                                            } else {
                                            ?>
                                                <td></td>
                                            <?php
                                            }
                                            ?>
                                            <td class="text-right">

                                                <?php

                                                if ($bank_amt_count == 0) {
                                                    $bank_sum += 0;
                                                    $bank_amt_graph = 0;
                                                    echo "0.00";
                                                } else {
                                                    $bank_sum += $bank_amt;
                                                    $bank_amt_graph = $bank_amt;
                                                    echo $bank_amt;
                                                }
                                                ?>

                                            </td>
                                            <td class="text-right">
                                                <?php
                                                if ($book_amt == "") {
                                                    $book_sum += 0;
                                                    $book_amt_graph = 0;
                                                    echo "0.00";
                                                } else {
                                                    $book_sum += $book_amt;
                                                    $book_amt_graph = $book_amt;
                                                    echo $book_amt;
                                                }
                                                ?>
                                            </td>
                                            <td>

                                                <?php
                                                if ($listItem["isIciciCibEnabled"] == 1) {
                                                ?>
                                                    <!-- Refresh Bank Statement button -->
                                                    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#getBankStatementByApi<?= $bank_id ?>">
                                                        <ion-icon name="cloud-download-outline"></ion-icon>
                                                    </button>
                                                    <div class="modal fade" id="getBankStatementByApi<?= $bank_id ?>" tabindex="-1" aria-labelledby="getBankStatementByApiLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form action="" method="post">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="getBankStatementByApiLabel">Refresh Bank Statement</h5>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Are you sure to refresh the Bank statement?</p>
                                                                        <input type="hidden" name="bankId" value="<?= $bank_id ?>">
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal" aria-label="Close">Close</button>
                                                                        <button type="submit" name="bankStatementRefreshSubmitBtn" class="btn btn-success">Confirm</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- / Refresh Bank Statement button -->
                                                <?php
                                                }
                                                ?>
                                                <!-- Upload Bank Statement button -->
                                                <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#bankStatement<?= $bank_id ?>">
                                                    <ion-icon name="cloud-upload-outline"></ion-icon>
                                                </button>
                                                <div class="modal fade bankstatement-modal" id="bankStatement<?= $bank_id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Upload Bank Statement</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="" method="post" id="statement-upload-form_<?= $bank_id ?>" class="statement-upload-form" enctype="multipart/form-data">
                                                                    <input type="hidden" name="uploadFile" value="submitStatementFileBtn">
                                                                    <input type="hidden" name="bank_id" value="<?= $bank_id ?>">
                                                                    <div class="upload-section">
                                                                        <div class="wrapper">
                                                                            <div class="upload-wrapper">
                                                                                <div class="upload drop-area">
                                                                                    <div class="upload-button">
                                                                                        <div class="upload-info">
                                                                                            <ion-icon name="attach-outline"></ion-icon>
                                                                                            <span class="upload-filename inactive drop-text" id="upload_filename_<?= $bank_id ?>">No file selected</span>
                                                                                        </div>
                                                                                        <input type="file" name="file" id="fileInput_<?= $bank_id ?>" class="form-control statement-file-input">
                                                                                        <button id="uploadButton_<?= $bank_id ?>" type="submit" class="btn btn-primary statement-upload-btn">Upload File</button>
                                                                                    </div>
                                                                                    <div class="upload-hint">Uploading...</div>
                                                                                    <div class="upload-progress"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="upload-vit-animation">
                                                                                <img width="150" src="../../public/assets/img/VitNew 1.png" alt="">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                <div class="bank-statement-list uploadStatementResponseDiv" id="uploadStatementResponseDiv_<?= $bank_id ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- / Upload Bank Statement button -->
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
include("../common/footer.php");
?>
<script src="<?= BASE_URL ?>public/assets/core.js"></script>
<script src="<?= BASE_URL ?>public/assets/charts.js"></script>
<script src="<?= BASE_URL ?>public/assets/animated.js"></script>
<script src="<?= BASE_URL ?>public/assets/forceDirected.js"></script>
<script src="<?= BASE_URL ?>public/assets/sunburst.js"></script>

<script>
    $(document).ready(function() {
        $(document).on("change", ".statement-file-input", function(event) {
            let bank_id = ($(this).attr("id")).split("_")[1];
            $(`#upload_filename_${bank_id}`).html(event.target.files[0].name);
        });

        $(document).on("submit", ".statement-upload-form", function(event) {
            let bank_id = ($(this).attr("id")).split("_")[1];
            event.preventDefault();
            // var formData = $(this).serialize();
            var formData = new FormData(this);
            formData.append('uploadFile', "submitStatementFileBtn");
            formData.append('bank_id', bank_id);
            formData.append('file', $(`#fileInput_${bank_id}`)[0].files[0]);

            $.ajax({
                url: 'ajaxs/reconciliation/ajax-bank-statement-upload.php',
                type: 'POST',
                data: formData,
                async: true,
                cache: false,
                contentType: false,
                enctype: 'multipart/form-data',
                processData: false,
                beforeSend: function() {
                    $(`#uploadButton_${bank_id}`).html("Uploading statement, please wait...");
                    $(`#uploadButton_${bank_id}`).prop('disabled', true);
                    console.log("Uploading statement.....");
                },
                success: function(response) {
                    console.log(response);
                    $(`#uploadButton_${bank_id}`).html("Successfully uploaded");
                    $(`#uploadStatementResponseDiv_${bank_id}`).html(response);
                },
                complete: function(xhr, textStatus) {
                    if (xhr.status != 200) {
                        $(`#uploadButton_${bank_id}`).html("Upload Again");
                        $(`#uploadStatementResponseDiv_${bank_id}`).html(`<p class="text-center text-warning">Something went wrong, Please try again!</p>`);
                    }
                    $(`#uploadButton_${bank_id}`).prop('disabled', false);
                    console.log("The request is completed with status code ", xhr.status);
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Get references to the file input and the filename display element
        var fileInput = $('#fileInput');
        var filenameDisplay = $('#upload_filename_<?= $bank_id ?>');

        // Add an event listener to the file input to update the filename display
        fileInput.on('change', function() {
            if (fileInput[0].files.length > 0) {
                // Get the selected file's name and display it
                var filename = fileInput[0].files[0].name;
                filenameDisplay.text(filename);
            } else {
                // If no file is selected, show "No file selected"
                filenameDisplay.text('No file selected');
            }
        });
    });
</script>


<script>
    let currency_name = "<?php echo addslashes($currency_name); ?>";
    $(document).ready(function() {
        var bank_sum = <?= json_encode($bank_sum) ?>;
        var book_sum = <?= json_encode($book_sum) ?>;
        var data = <?= json_encode($graph_date_wise) ?>;
        var start_date = <?= json_encode($startDate) ?>;
        var end_date = <?= json_encode($endDate) ?>;
        var chart;

        $("#book_amount").html(currency_name +' '+ decimalAmount(book_sum));
        $("#bank_amount").html(currency_name +' '+ decimalAmount(bank_sum));

        $(document).on("click", ".report_link", function() {
            // console.log(data);

            var bankid = $(this).data('bankid');
            var pgl = $(this).data('pgl');
            var subgl = $(this).data('subgl');

            $("#book_amount").html(currency_name +' '+ decimalAmount($(this).data('book')));
            $("#bank_amount").html(currency_name +' '+ decimalAmount($(this).data('bank')));

            $("#change_bank").html($(this).data('bankname'));

            $.ajax({
                type: "GET",
                url: `ajaxs/reconciliation/ajax-filter-date-graph-each.php?startdate=` + start_date + `&enddate=` + end_date + `&bankid=` + bankid + `&pgl=` + pgl + `&subgl=` + subgl,
                beforeSend: function() {},
                success: function(response) {
                    let responseObj = JSON.parse(response);
                    root.dispose();
                    // data = responseObj["graph"];
                    creategraph(responseObj);
                }
            });
        });


        $('input[name="daterange"]').daterangepicker({
            opens: 'left',
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            const currentDate = new Date();
            $.ajax({
                type: "GET",
                url: `ajaxs/reconciliation/ajax-filter-date.php?startdate=` + start.format('YYYY-MM-DD') + `&enddate=` + end.format('YYYY-MM-DD'),
                beforeSend: function() {},
                success: function(response) {
                    let responseObj = JSON.parse(response);
                    $("#book_amount").html(currency_name +' '+ responseObj["book_sum"].toFixed(2));
                    $("#bank_amount").html(currency_name +' '+ responseObj["bank_sum"].toFixed(2));
                    $("#tbody_data").html(responseObj["table"]);
                    $("#fromdatetodate").html(responseObj["dates"]);

                    $.ajax({
                        type: "GET",
                        url: `ajaxs/reconciliation/ajax-filter-date-graph.php?startdate=` + start.format('YYYY-MM-DD') + `&enddate=` + end.format('YYYY-MM-DD'),
                        beforeSend: function() {},
                        success: function(response) {
                            let responseObj = JSON.parse(response);
                            start_date = start.format('YYYY-MM-DD');
                            end_date = end.format('YYYY-MM-DD');
                            root.dispose();
                            creategraph(responseObj);
                        }
                    });
                }
            });
        });
        creategraph(data);

        function creategraph(data) {
            root = am5.Root.new("chartdiv");
            root.setThemes([
                am5themes_Animated.new(root)
            ]);
            chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: false,
                panY: false,
                wheelX: "panX",
                wheelY: "zoomX",
                layout: root.verticalLayout
            }));
            var legend = chart.children.push(am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50
            }))
            var yAxis = chart.yAxes.push(am5xy.CategoryAxis.new(root, {
                categoryField: "month",
                renderer: am5xy.AxisRendererY.new(root, {
                    inversed: true,
                    cellStartLocation: 0.1,
                    cellEndLocation: 0.9
                })
            }));
            yAxis.data.setAll(data);
            var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererX.new(root, {
                    strokeOpacity: 0.1
                }),
                min: 0
            }));

            function createSeries(field, name) {
                var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueXField: field,
                    categoryYField: "month",
                    sequencedInterpolation: true,
                    tooltip: am5.Tooltip.new(root, {
                        pointerOrientation: "horizontal",
                        labelText: "[bold]{name}[/]\n{categoryY}: {valueX}"
                    })
                }));
                series.columns.template.setAll({
                    height: am5.p100,
                    strokeOpacity: 0
                });
                series.bullets.push(function() {
                    return am5.Bullet.new(root, {
                        locationX: 1,
                        locationY: 0.5,
                        sprite: am5.Label.new(root, {
                            centerY: am5.p50,
                            text: "{valueX}",
                            populateText: true
                        })
                    });
                });
                series.bullets.push(function() {
                    return am5.Bullet.new(root, {
                        locationX: 1,
                        locationY: 0.5,
                        sprite: am5.Label.new(root, {
                            centerX: am5.p100,
                            centerY: am5.p50,
                            text: "{name}",
                            fill: am5.color(0xffffff),
                            populateText: true
                        })
                    });
                });

                series.data.setAll(data);
                series.appear();
                return series;
            }

            createSeries("book", "Books");
            createSeries("bank", "Banks");

            var legend = chart.children.push(am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50
            }));

            legend.data.setAll(chart.series.values);
            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "zoomY"
            }));
            cursor.lineY.set("forceHidden", true);
            cursor.lineX.set("forceHidden", true);
            chart.appear(1000, 100);
        }
    });
</script>