<?php
require_once("../../../../app/v1/connection-branch-admin.php"); // Adjust path as needed
require_once("../../../common/exportexcel-new.php");
header('Content-Type: application/json');

// Check for POST request and action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['act']) && $_POST['act'] == 'trialBalanceDatatable') {
    $start_date = isset($_POST['from_date']) && !empty($_POST['from_date']) ? $_POST['from_date'] : date('Y-m-d', strtotime('-1 day'));
    $end_date = isset($_POST['to_date']) && !empty($_POST['to_date']) ? $_POST['to_date'] : date('Y-m-d');

    $_SESSION['dateArr']['from_date'] = $start_date;
    $_SESSION['dateArr']['to_date'] = $end_date;

    $glSummaryObj = new GlSummary("location");
    $glSummaryObj->setGlAccTypes([1, 2, 3, 4]);
    $glSummaryObj->setGlTreeView(false);
    $glSummaryData = $glSummaryObj->getSummaryTree($start_date, $end_date);



    $glSummaryObjNew = new GlSummary("location");
    $glSummaryObjNew->setGlAccTypes([3, 4]);
    $glSummaryObjNew->setGlTreeView(false);
    $glSummaryDataNew = $glSummaryObjNew->getSummaryTree($start_date, $end_date);
    $profitBeforeTax = ($glSummaryDataNew["grandTotal"]["grandTotalDebit"] ?? 0) - ($glSummaryDataNew["grandTotal"]["grandTotalCredit"] ?? 0);

    $grandTotalDebit = 0;
    $grandTotalCredit = 0;
    $grandTotalOpening = 0;
    $grandTotalClosing = 0;
    $trialBalanceData = [];

    foreach ($glSummaryData["data"] as $row) {
        if (in_array($row["typeAcc"], [3, 4])) {
            $opening_val = 0; // Revenue and expense accounts start with zero opening balance
            $closing_val = $row["debit_amount"] - $row["credit_amount"];
        } else {
            $opening_val = $row["opening_val"] ?? 0;
            $closing_val = $opening_val + ($row["debit_amount"] ?? 0) - ($row["credit_amount"] ?? 0);
        }

        // Accumulate grand totals
        $grandTotalDebit += $row["debit_amount"] ?? 0;
        $grandTotalCredit += $row["credit_amount"] ?? 0;
        $grandTotalOpening += $opening_val;
        $grandTotalClosing += $closing_val;

        // Add row to trial balance data
        $trialBalanceData[] = [
            "gl_code" => $row["gl_code"] ?? "",
            "gl_label" => $row["gl_label"] ?? "",
            "opening_val" => $opening_val,
            "debit_amount" => $row["debit_amount"] ?? 0,
            "credit_amount" => $row["credit_amount"] ?? 0,
            "closing_val" => $closing_val,
            "typeAcc" => $row["typeAcc"] ?? null
        ];
    }

    $totalAdjustedAmountOpening = (-1) * $grandTotalOpening; // Total adjustment including P&L
    $adjustedAmountOpening = $totalAdjustedAmountOpening - $profitBeforeTax; // Adjustment excluding P&L
    $totalAdjustedAmountClosing = (-1) * $grandTotalClosing; // Total adjustment including P&L
    $adjustedAmountClosing = $totalAdjustedAmountClosing - $profitBeforeTax; // Adjustment excluding P&L

    $trialBalanceData[] = [
        "gl_code" => "",
        "gl_label" => "Profit/Loss",
        "opening_val" => $profitBeforeTax,
        "debit_amount" => 0,
        "credit_amount" => 0,
        "closing_val" => $profitBeforeTax,
        "typeAcc" => null
    ];

    $trialBalanceData[] = [
        "gl_code" => "50000",
        "gl_label" => "Opening Balance Adjustment",
        "opening_val" => $adjustedAmountOpening,
        "debit_amount" => 0,
        "credit_amount" => 0,
        "closing_val" => $adjustedAmountClosing,
        "typeAcc" => null
    ];

    $trialBalanceData[] = [
        "gl_code" => "",
        "gl_label" => "Total",
        "opening_val" => 0, // Typically zeroed out in trial balance totals
        "debit_amount" => $grandTotalDebit,
        "credit_amount" => $grandTotalCredit,
        "closing_val" => 0, // Typically zeroed out in trial balance totals
        "typeAcc" => null
    ];


    if (empty($glSummaryData['data'])) {
        $res = [
            "status" => false,
            "msg" => "No GL summary data found",
            "data" => []
        ];
    } else {
        $res = [
            "status" => true,
            "msg" => "Trial balance data retrieved successfully",
            "data" => $trialBalanceData
        ];
    }

    // **Step 7: Return JSON Response**
    echo json_encode($res);
}
if ($_POST['act'] == 'alldata') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $start_date = $_SESSION['dateArr']['from_date'] ;
        $end_date = $_SESSION['dateArr']['to_date'];

        $glSummaryObj = new GlSummary("location");
        $glSummaryObj->setGlAccTypes([1, 2, 3, 4]);
        $glSummaryObj->setGlTreeView(false);
        $glSummaryData = $glSummaryObj->getSummaryTree($start_date, $end_date);

        

        $glSummaryObjNew = new GlSummary("location");
        $glSummaryObjNew->setGlAccTypes([3, 4]);
        $glSummaryObjNew->setGlTreeView(false);
        $glSummaryDataNew = $glSummaryObjNew->getSummaryTree($start_date, $end_date);
        $profitBeforeTax = ($glSummaryDataNew["grandTotal"]["grandTotalDebit"] ?? 0) - ($glSummaryDataNew["grandTotal"]["grandTotalCredit"] ?? 0);

        $grandTotalDebit = 0;
        $grandTotalCredit = 0;
        $grandTotalOpening = 0;
        $grandTotalClosing = 0;
        $trialBalanceData = [];

        foreach ($glSummaryData["data"] as $row) {
            if (in_array($row["typeAcc"], [3, 4])) {
                $opening_val = 0; // Revenue and expense accounts start with zero opening balance
                $closing_val = $row["debit_amount"] - $row["credit_amount"];
            } else {
                $opening_val = $row["opening_val"] ?? 0;
                $closing_val = $opening_val + ($row["debit_amount"] ?? 0) - ($row["credit_amount"] ?? 0);
            }

            // Accumulate grand totals
            $grandTotalDebit += $row["debit_amount"] ?? 0;
            $grandTotalCredit += $row["credit_amount"] ?? 0;
            $grandTotalOpening += $opening_val;
            $grandTotalClosing += $closing_val;

            // Add row to trial balance data
            $trialBalanceData[] = [
                "gl_code" => $row["gl_code"] ?? "",
                "gl_label" => $row["gl_label"] ?? "",
                "opening_val" => $opening_val,
                "debit_amount" => $row["debit_amount"] ?? 0,
                "credit_amount" => $row["credit_amount"] ?? 0,
                "closing_val" => $closing_val,
                "typeAcc" => $row["typeAcc"] ?? null
            ];
        }

        $totalAdjustedAmountOpening = (-1) * $grandTotalOpening; // Total adjustment including P&L
        $adjustedAmountOpening = $totalAdjustedAmountOpening - $profitBeforeTax; // Adjustment excluding P&L
        $totalAdjustedAmountClosing = (-1) * $grandTotalClosing; // Total adjustment including P&L
        $adjustedAmountClosing = $totalAdjustedAmountClosing - $profitBeforeTax; // Adjustment excluding P&L

        $trialBalanceData[] = [
            "gl_code" => "",
            "gl_label" => "Profit/Loss",
            "opening_val" => $profitBeforeTax,
            "debit_amount" => 0,
            "credit_amount" => 0,
            "closing_val" => $profitBeforeTax,
            "typeAcc" => null
        ];

        $trialBalanceData[] = [
            "gl_code" => "50000",
            "gl_label" => "Opening Balance Adjustment",
            "opening_val" => $adjustedAmountOpening,
            "debit_amount" => 0,
            "credit_amount" => 0,
            "closing_val" => $adjustedAmountClosing,
            "typeAcc" => null
        ];

        $trialBalanceData[] = [
            "gl_code" => "",
            "gl_label" => "Total",
            "opening_val" => 0, // Typically zeroed out in trial balance totals
            "debit_amount" => $grandTotalDebit,
            "credit_amount" => $grandTotalCredit,
            "closing_val" => 0, // Typically zeroed out in trial balance totals
            "typeAcc" => null
        ];


        $trialBalanceData=json_encode($trialBalanceData);
        $exportToExcelAll =exportToExcelAll($trialBalanceData,$_POST['coloum'],$_POST['sql_data_checkbox']);

        if(empty($glSummaryData['data'])) {
            $res = [
                "status" => false,
                "msg" => "No GL summary data found",
                "data" => []
            ];
        }
        else
        {
            $res = [
                "status" => true,
                "msg" => "CSV all generated",
                'csvContentall' => $exportToExcelAll,
                "sql" => $sql_list,
            ];
        }

        echo json_encode($res);
    }
}
