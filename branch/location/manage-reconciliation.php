<?php
require_once("../../app/v1/connection-branch-admin.php");
include_once("../../app/v1/functions/branch/func-compliance-controller.php");
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
//administratorAuth();
?>



<style>
    .content-wrapper {
        padding-top: 6em;
    }


    .filter-list a {
        background: #fff;
        box-shadow: 1px 2px 5px -1px #8e8e8e;
    }

    .filter-list {
        margin-bottom: 2em;
    }

    li.nav-item.complince a {
        background: #fff;
        color: #003060;
        z-index: 9;
        margin-bottom: 1em;
    }

    .reconColumn {
        background-color: #7b7b7b !important;
        color: white;
    }

    .reconColumn_green {
        background-color: #91fe91 !important;
        color: white;
    }

    table tr td {
        background: #ffffff !important;
        padding-left: 0px !important;
        padding-right: 0px !important;
        text-align: center !important;
        cursor: pointer;
    }

    table th {
        padding-left: 0px !important;
        padding-right: 0px !important;
        text-align: center !important;
    }

    .matchedRowColor-100 td {
        background-color: #d1f0cc !important;
        color: #064908;
    }

    .matchedRowColor-75 td {
        background-color: #b3d5f0 !important;
        color: #064908;
    }

    .matchedRowColor-50 td {
        background-color: #f0deb3 !important;
        color: #064908;
    }

    .matchedRowColor-25 td {
        background-color: #fdf0f0 !important;
        color: #064908;
    }

    table.dataTable>thead .sorting:before,
    table.dataTable>thead .sorting:after,
    table.dataTable>thead .sorting_asc:before,
    table.dataTable>thead .sorting_asc:after,
    table.dataTable>thead .sorting_desc:before,
    table.dataTable>thead .sorting_desc:after,
    table.dataTable>thead .sorting_asc_disabled:before,
    table.dataTable>thead .sorting_asc_disabled:after,
    table.dataTable>thead .sorting_desc_disabled:before,
    table.dataTable>thead .sorting_desc_disabled:after {
        display: block !important;
    }

    .dataTables_wrapper .row:nth-child(3) {
        display: flex !important;
    }

    div.dataTables_wrapper div.dataTables_filter {
        display: block !important;
    }

    div.dataTables_wrapper div.dataTables_filter label {
        font-size: 0;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 0;
        display: inline-block;
        width: auto;
        padding-left: 10px;
        border: 1px solid #E5E5E5;
        color: #1B2559;
        height: 25px;
        border-radius: 8px;
    }

    ul.pagination {
        border: 0;
    }

    /* .header-title .card-body {
        display: flex;
        justify-content: space-between;
    }
    .card-body::after, .card-footer::after, .card-header::after {
        display: none !important;
    } */
    .temp-recon-list-modal .modal-dialog {
        min-width: 75%;
    }

    .temp-recon-list-modal .modal-body {
        width: 100% !important;
    }

    /********otp start******/
    .title {
        max-width: 400px;
        margin: auto;
        text-align: center;
        font-family: "Poppins", sans-serif;
    }

    .title h3 {
        font-weight: bold;
    }

    .title p {
        font-size: 12px;
        color: #118a44;
    }

    .title p.msg {
        color: initial;
        text-align: initial;
        font-weight: bold;
    }

    .otp-input-fields {
        margin: auto;
        max-width: 400px;
        width: auto;
        display: flex;
        justify-content: center;
        gap: 10px;
        padding: 15px 10px;
    }

    .otp-input-fields input {
        height: 40px;
        width: 40px;
        background-color: transparent;
        border-radius: 4px;
        border: 1px solid #2f8f1f;
        text-align: center;
        outline: none;
        font-size: 16px;
        /* Firefox */
    }

    .otp-input-fields input::-webkit-outer-spin-button,
    .otp-input-fields input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .otp-input-fields input[type=number] {
        -moz-appearance: textfield;
    }

    .otp-input-fields input:focus {
        border-width: 2px;
        border-color: #287a1a;
        font-size: 20px;
    }

    .result {
        max-width: 400px;
        margin: auto;
        padding: 24px;
        text-align: center;
    }

    .result p {
        font-size: 24px;
        font-family: "Antonio", sans-serif;
        opacity: 1;
        transition: color 0.5s ease;
    }

    .result p._ok {
        color: green;
    }

    .result p._notok {
        color: red;
        border-radius: 3px;
    }

    .otp-section {
        margin-top: 39px;
        background: #ebebeb;
        padding: 10px;
        border-radius: 12px;
        box-shadow: 2px 7px 14px -3px #868686;
    }

    .otp-input-fields,
    .otp-input-fields-count-time {
        height: 160px;
        padding-top: 4em;
    }

    .second-step {
        display: none;
    }

    .otp-input-fields-count-time {
        display: none;
    }

    /* .connected-text {
        display: none;
    } */
    .robo-element {
        height: 50vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 25px;
    }

    .robo-element img {
        width: 200px;
        height: 200px;
        object-fit: contain;
    }

    .recon-table-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .recon-table-head .amount-section {
        display: flex;
        justify-content: space-between;
    }

    .recon-table-head .amount-section p {
        display: flex;
        flex-direction: column;
        border-right: 1px solid #999999;
        padding-right: 7em;
    }

    .recon-table-head {
        display: grid;
        grid-template-columns: 2fr 1fr;
        align-items: center;
    }

    .recon-table-head .btn-section {
        display: flex;
        justify-content: end;
        gap: 10px;
    }

    table#previewTable {
        border: 0;
    }

    table#previewTable tr td {
        background: #fff !important;
    }

    table#previewTable tr:nth-child(2n+1) td {
        background: #d4e3ff !important;
    }

    .action-container {
        display: flex;
        align-items: center;
        gap: 5px;
        justify-content: flex-end;
        margin: 10px 0 5px;
    }

    .action-container button {
        display: flex;
        align-items: center;
        gap: 5px;
    }



    /* .otp-input-fields-count-time {
        display: none;
    } */
    /********otp end******/
</style>

<link rel="stylesheet" href="../public/assets/listing.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Main content -->
    <?php

    if (isset($_GET['party_id']) && isset($_GET['type']) && isset($_GET['from_date']) && isset($_GET['to_date'])) {
        $party_type = $_GET['type'];
        $party_id = $_GET['party_id'];
        $currentDate = date("Y-m-d"); // Get the current date

        // Get the start date of the current month
        $from_date = $_GET['from_date'];

        // Get the end date of the current month
        $to_date = $_GET['to_date'];

        // console($_GET);

        $party_id = $_GET['party_id'];
        $_GET['type'];

        if ($_GET['type'] == 'vendor') {
            $vendor_sql = queryGet("SELECT * FROM `erp_vendor_details` WHERE `vendor_id` = $party_id");
            //console($vendor_sql);
            $party_code = $vendor_sql['data']['vendor_code'];
            $party_name = $vendor_sql['data']['trade_name'];

            $party_currency = $vendor_sql['data']['vendor_currency'];
            // $party_code = $_GET['party_code'];
            $statement_sql = queryGet("SELECT * FROM (
            SELECT journal.id AS id,journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS amount,SUM(debit.debit_amount) AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE  journal.reconcile_status = 0 AND journal.parent_slug='Payment' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
            UNION
            SELECT  journal.id AS id,journal.parent_slug AS type,journal.remark,journal.postingDate,SUM(credit.credit_amount) AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE  journal.reconcile_status = 0 AND journal.parent_slug='grniv' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
            UNION
            SELECT journal.id AS id, journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS amount,SUM(debit.debit_amount) AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE  journal.reconcile_status = 0 AND journal.parent_slug='journal' AND journalEntryReference='Payment' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND debit.subGlCode=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
            UNION
            SELECT  journal.id AS id,'grniv' AS type,journal.remark,journal.postingDate,SUM(credit.credit_amount) AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id  WHERE journal.reconcile_status = 0  AND journal.parent_slug='journal' AND journalEntryReference='Purchase' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND credit.subGlCode=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id) AS temp_table ORDER BY temp_table.postingDate desc", true);

            $data = $statement_sql['data'];
            //  console($statement_sql);
            $total_transaction_debit = 0;
            $total_transaction_credit = 0;
            $total_transaction = 0;
            foreach ($statement_sql['data'] as $transaction) {
                // console($transaction);

                $total_transaction_credit += $transaction['payment'];
                //   echo '<br>';

                $total_transaction_debit += $transaction['amount'];
                // echo '<br>';




            }

            $total_pending_transaction += $total_transaction_debit - $total_transaction_credit;

            $statement_sql_reconciled = queryGet("SELECT * FROM (
            SELECT journal.id AS id,journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS amount,SUM(debit.debit_amount) AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.reconcile_status != 0 AND journal.parent_slug='Payment' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
            UNION
            SELECT  journal.id AS id,journal.parent_slug AS type,journal.remark,journal.postingDate,SUM(credit.credit_amount) AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.reconcile_status != 0 AND  journal.parent_slug='grniv' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
            UNION
            SELECT journal.id AS id, journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS amount,SUM(debit.debit_amount) AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.reconcile_status != 0 AND journal.parent_slug='journal' AND journalEntryReference='Payment' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND debit.subGlCode=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
            UNION
            SELECT  journal.id AS id,'grniv' AS type,journal.remark,journal.postingDate,SUM(credit.credit_amount) AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.reconcile_status != 0 AND journal.parent_slug='journal' AND journalEntryReference='Purchase' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND credit.subGlCode=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id) AS temp_table ORDER BY temp_table.postingDate desc", true);

            $total_recon_credit = 0;
            $total_recon_debit = 0;
            $total_reonciled = 0;
            foreach ($statement_sql_reconciled['data'] as $recon_transaction) {

                $total_recon_credit += $recon_transaction['payment'];
                $total_recon_debit += $recon_transaction['amount'];;
            }
            $total_reonciled += $total_recon_debit - $total_recon_credit;
        } else {

            $customer_sql = queryGet("SELECT * FROM `erp_customer` WHERE `customer_id` = $party_id");
            // console($customer_sql);
            $party_code = $customer_sql['data']['customer_code'];
            // $party_code = $_GET['party_code'];
            $party_name = $customer_sql['data']['trade_name'];
            $party_currency = $customer_sql['data']['customer_currency'];

            $statement_sql = queryGet("SELECT * FROM (
            SELECT journal.id AS id,journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS amount,SUM(credit.credit_amount) AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.reconcile_status = 0 AND journal.parent_slug='Collection' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
            UNION
            SELECT journal.id AS id,journal.parent_slug AS type,journal.remark,journal.postingDate,SUM(debit.debit_amount) AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.reconcile_status = 0 AND journal.parent_slug='SOInvoicing' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
            UNION
            SELECT journal.id AS id,journal.journalEntryReference AS type,journal.remark AS remark,journal.postingDate,0 AS amount,SUM(credit.credit_amount) AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.reconcile_status = 0 AND journal.parent_slug='journal' AND journal.journalEntryReference='Collection' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND credit.subGlCode=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
            UNION
            SELECT journal.id AS id,'SOInvoicing' AS type,journal.remark AS remark,journal.postingDate,SUM(debit.debit_amount) AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.reconcile_status = 0 AND journal.parent_slug='journal' AND journal.journalEntryReference='Sales' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND debit.subGlCode=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id) AS temp_table
            ORDER BY temp_table.postingDate desc;", true);

            $data = $statement_sql['data'];
            //  console($statement_sql);
            $total_transaction_debit = 0;
            $total_transaction_credit = 0;
            $total_transaction = 0;
            foreach ($statement_sql['data'] as $transaction) {
                // console($transaction);

                $total_transaction_credit += $transaction['payment'];
                //   echo '<br>';

                $total_transaction_debit += $transaction['amount'];
                // echo '<br>';




            }

            $total_pending_transaction += $total_transaction_debit - $total_transaction_credit;

            $statement_sql_reconciled = queryGet("SELECT * FROM (
                SELECT journal.id AS id,journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS amount,SUM(credit.credit_amount) AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.reconcile_status != 0 AND journal.parent_slug='Collection' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
                UNION
                SELECT journal.id AS id,journal.parent_slug AS type,journal.remark,journal.postingDate,SUM(debit.debit_amount) AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.reconcile_status != 0 AND journal.parent_slug='SOInvoicing' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
                UNION
                SELECT journal.id AS id,journal.journalEntryReference AS type,journal.remark AS remark,journal.postingDate,0 AS amount,SUM(credit.credit_amount) AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.reconcile_status != 0 AND journal.parent_slug='journal' AND journal.journalEntryReference='Collection' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND credit.subGlCode=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
                UNION
                SELECT journal.id AS id,'SOInvoicing' AS type,journal.remark AS remark,journal.postingDate,SUM(debit.debit_amount) AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.reconcile_status != 0 AND journal.parent_slug='journal' AND journal.journalEntryReference='Sales' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND debit.subGlCode=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id) AS temp_table
                ORDER BY temp_table.postingDate desc;", true);
            // console($statement_sql_reconciled);

            $total_recon_credit = 0;
            $total_recon_debit = 0;
            $total_reonciled = 0;
            foreach ($statement_sql_reconciled['data'] as $recon_transaction) {

                $total_recon_credit += $recon_transaction['payment'];
                $total_recon_debit += $recon_transaction['amount'];;
            }
            $total_reonciled += $total_recon_debit - $total_recon_credit;
        }

        $currency_sql = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id` = $party_currency");
        // console($currency_sql);
        $currency = $currency_sql['data']['currency_name'];

        $locStatement_sql = queryGet("SELECT * FROM `erp_vendor_customer_reconciliation` as recon LEFT JOIN `erp_reconciliation` as files ON files.id=recon.recon_file_id WHERE files.type='" . $party_type . "' AND files.code=$party_id AND recon.recon_journal_id = 0 AND location_id=$location_id", true);

        // console($locStatement_sql);

        $partner_deb = 0;
        $partner_cred = 0;

        foreach ($locStatement_sql['data'] as $partner_sum) {
            $partner_deb +=  $partner_sum['invoice'];
            $partner_cred += $partner_sum['payments'];
        }

        $total_pending_partner = $partner_deb - $partner_cred;

    ?>
        <!-- <div class="filter-list">
            <a href="reconciliation.php?type=<?= $type ?>&v_id=<?= $party_id ?>" class="btn"><i class="fa fa-list mr-2"></i>Files</a>
            <a href="manage-reconciliation.php?type=<?= $type ?>&party_id=<?= $party_id ?>" class="btn"><i class="fa fa-clock mr-2"></i>Reconciliation</a>
        </div> -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h4 class="font-bold text-lg"><?php if ($party_type == 'vendor') {
                                                            echo "Vendor Name : ";
                                                        } else {
                                                            echo "Customer Name : ";
                                                        }
                                                        echo $party_name; ?></h4>
                        <hr width="250" class="my-2">
                        <p class="text-sm"><?= $from_date ?> To <?= $to_date ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card mt-5 rounded mb-2">
                            <div class="card-body">
                                <div class="recon-table-head">
                                    <div class="amount-section">
                                        <p class="text-sm">Total Transaction <span class="font-bold text-lg"><?= $currency ?> <?= round($total_pending_transaction + $total_reonciled, 2) ?></span> </p>
                                        <p class="text-sm">Reconciled Transaction <span class="font-bold text-lg"><?= $currency ?> <?= round($total_reonciled, 2) ?></span></p>
                                        <p class="text-sm">Unreconciled Transaction <span class="font-bold text-lg"><?= $currency ?> <?= round($total_pending_transaction, 2) ?></span></p>

                                        <p class="text-sm">Unreconciled Transaction by <?= $party_type ?> <span class="font-bold text-lg"><?= $currency ?> <?= round($total_pending_partner, 2) ?></span></p>

                                    </div>
                                    <div class="btn-section">
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reconFilesModal_<?= $party_id ?>"> <?php if ($party_type == 'vendor') {
                                                                                                                                                        echo "Vendor  ";
                                                                                                                                                    } else {
                                                                                                                                                        echo "Customer ";
                                                                                                                                                    }  ?>Files</button>
                                        <button class="btn btn-primary" id="matchTheTableRowBtn">Match</button>
                                        <button class="btn btn-primary" id="addMatchedRowToBusketBtn">Confirm</button>
                                    </div>
                                    <div class="modal fade right reconFilesModal" id="reconFilesModal_<?= $party_id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-white" id="exampleModalLabel">Modal title</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="tab-content" id="custom-tabs-two-tabContent">
                                                        <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                                            <?php
                                                            $cond = '';

                                                            $sts = " AND `status` !='deleted'";
                                                            if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                                                $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                                            }

                                                            if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                                                $cond .= " AND createdAt between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                                            }

                                                            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                                                $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword'] . "%' OR `itemName` like '%" . $_REQUEST['keyword'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword'] . "%')";
                                                            }

                                                            //$sql_list = "SELECT * FROM " . ERP_BIN . " as bin ,".ERP_WAREHOUSE." as warehouse ,".ERP_STORAGE_LOCATION." as sl WHERE 1 AND sl.storage_location_id=bin.storage_location_id  ORDER BY bin.bin_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                                                            $batch = queryGet("SELECT * FROM `erp_reconciliation` WHERE `type`='" . $party_type . "' AND `code`= $party_id AND `company_id` = $company_id AND `location_id` = $location_id limit 0,25 ", true);

                                                          //  console($batch);

                                                            ?>



                                                            <div class="action-container">

                                                                <button class="btn btn-primary"><ion-icon name="cloud-upload-outline"></ion-icon> Upload</button>

                                                                <button class="btn btn-primary" id="downloadBtn"><ion-icon name="cloud-download-outline"></ion-icon>Download</button>

                                                            </div>

                                                            <table class="table defaultDataTable table-hover text-nowrap">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Type</th>
                                                                        <th> Reconciliation Type</th>
                                                                        <th>Code</th>
                                                                        <th> File Name</th>
                                                                        <th>View</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                               


                                                                    <?php

                                                                    foreach ($batch['data'] as $datas) {
                                                                    ?>
                                                                        <tr style="cursor:pointer">
                                                                            <td><?= $cnt++ ?></td>

                                                                            <td><?= $datas['type'] ?>

                                                                            </td>


                                                                            <td><?= $datas['reconciliationType'] ?>

                                                                            </td>


                                                                            <td><?= $datas['code'] ?>

                                                                            </td>


                                                                            <td><?= $datas['files'] ?>
                                                                            </td>



                                                                            <td><a href="#" class="viewBtn" style="text-decoration: none;" data-attr="<?= $datas['id'] ?>">
                                                                                    <i class="fa fa-eye po-list-icon mx-auto"></i>
                                                                                </a></td>


                                                                            <div id="previewModal" class="modal add-stock-list-modal previewModal_<?= $datas['id'] ?>">
                                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h3 class="card-title text-white">Excel Preview</h3>
                                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <div class="excelData_<?= $datas['id'] ?>">

                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <button class="btn btn-primary insertButton_<?= $datas['id'] ?>" id="insertButton">Insert into Database</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <script>
                                                                                $(document).ready(function() {
                                                                                    var modal = $("#previewModal");
                                                                                    // Close modal when clicking close button
                                                                                    $(".btn-close").on("click", function() {
                                                                                        modal.toggle();
                                                                                    });
                                                                                });
                                                                            </script>


                                                                        </tr>




                                                                    <?php
                                                                    }

                                                                    ?>

                                                                </tbody>



                                                            </table>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="modal fade right temp-recon-list-modal customer-modal" id="tempReconListModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true">
                    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                        <!--Content-->
                        <div class="modal-content" id="tempReconListModalContent">
                            <!-- <div class="modal-header"></div>
                        <div class="modal-body"></div> -->
                        </div>
                    </div>
                    <!--/.Content-->
                </div>
                <h4 class="text-lg mt-3 mb-2 font-bold">Unreconciled</h4>
                <div class="row p-0 m-0">
                    <div class="col-lg-6 col-md-6 col-sm-6 px-0">
                        <table class="table gstr2aTable" id="gstr2aPortalTable">
                            <thead>
                                <th style="display:none;">id</th>
                                <th>Date</th>
                                <th> Transaction</th>
                                <th>Debit</th>
                                <th>Credit </th>
                                <th style="background-color: #011a3c!important; color:white">RECON</th>
                                <th style="background-color: #011a3c!important; color:white">MATCH</th>
                            </thead>


                            <?php
                            if ($party_type == 'vendor') {

                            ?>

                                <tr>
                                    <td colspan="5">INVOICE : </td>
                                                                    
                                </tr>

                                <tbody id="portalGstr2bTableBody">

                             


                                    <?php
                                       $balance = 0;
                                    foreach ($data as $rows) {

                                        // console($rows);
                                     
                                        if ($rows['type'] === 'grniv') {
                                        $date = $rows['postingDate'];

                                        $type = $rows['type'];
                                        $transaction = $rows['type'];
                                        $details = $rows['remark'];

                                       

                                            $amount = $rows['amount'];

                                            $balance = $balance + $amount;


                                       
                                    ?>
                                        <tr>
                                            <td class="statementId" style="display:none;"><?= $rows['id'] ?></td>
                                            <td class="statementDate"><?= $date ?></td>
                                            <td class="statementTransaction"><?= $transaction ?></td>
                                            <td class="statementDebit"><?php if ($type == 'grniv') {
                                                                            echo round($amount, 2);
                                                                        } else {
                                                                            echo 0;
                                                                        } ?></td>
                                            <td class="statementCredit"> <?php if ($type == 'Payment') {
                                                                                echo round($amount, 2);
                                                                            } else {
                                                                                echo 0;
                                                                            } ?></td>

                                            <?php

                                            $id = $rows['id'];
                                            $check_reconciled = queryGet("SELECT * FROM `erp_acc_journal` WHERE `id`= $id");
                                            // console($check_reconciled);
                                            if ($check_reconciled['data']['reconcile_status'] != 0) {
                                            ?>
                                                <td class="reconColumn_green">
                                                    <ion-icon name="checkmark-outline" size="small"></ion-icon>
                                                </td>
                                                <td class=" reconColumn_green">Reconciled</td>

                                            <?php
                                            } else {
                                            ?>
                                                <td class="reconColumn">
                                                    <input type="checkbox" name="" id="" class="reconCheckBox">
                                                </td>
                                                <td class="reconPercentageColumn reconColumn text-white">0%</td>



                                            <?php
                                            }
                                            ?>
                                        <tr>
                                        <?php
                                    }
                                }
                               ?>
                                    <tr>
                                        <td class="text-left" colspan="2">Total</td>
                                        <td><?= $balance ?></td>
                                    </tr>

                                <?php

                                $balance_payment = 0 ;
                                foreach ($data as $rows_payment) {

                                    if ($rows_payment['type'] === 'Payment') {
                                    $date = $rows_payment['postingDate'];

                                    $type = $rows_payment['type'];
                                    $transaction = $rows_payment['type'];
                                    $details = $rows_payment['remark'];

                                 
                                        $amount = $rows_payment['payment'];
                                        $balance_payment = $balance_payment + $amount;
                                   

                                    ?>

                                        <tr>
                                            <td class="statementId" style="display:none;"><?= $rows_payment['id'] ?></td>
                                            <td class="statementDate"><?= $date ?></td>
                                            <td class="statementTransaction"><?= $transaction ?></td>
                                            <td class="statementDebit"><?php if ($type == 'grniv') {
                                                                            echo round($amount, 2);
                                                                        } else {
                                                                            echo 0;
                                                                        } ?></td>
                                            <td class="statementCredit"> <?php if ($type == 'Payment') {
                                                                                echo round($amount, 2);
                                                                            } else {
                                                                                echo 0;
                                                                            } ?></td>

                                            <?php

                                            $id = $rows_payment['id'];
                                            $check_reconciled = queryGet("SELECT * FROM `erp_acc_journal` WHERE `id`= $id");
                                            // console($check_reconciled);
                                            if ($check_reconciled['data']['reconcile_status'] != 0) {
                                            ?>
                                                <td class="reconColumn_green">
                                                    <ion-icon name="checkmark-outline" size="small"></ion-icon>
                                                </td>
                                                <td class=" reconColumn_green">Reconcilled</td>

                                            <?php
                                            } else {
                                            ?>
                                                <td class="reconColumn">
                                                    <input type="checkbox" name="" id="" class="reconCheckBox">
                                                </td>
                                                <td class="reconPercentageColumn reconColumn text-white">0%</td>



                                            <?php
                                            }
                                            ?>
                                        <tr>
<?php
                                    }

                                }

                                        ?>

<tr>
                                        <td class="text-left" colspan="2">Total</td>
                                        <td><?= $balance_payment ?></td>
                                    </tr>
                                </tbody>

                            <?php
                            } else {

                            ?>
                                <!-- customer table -->


                                 <tr>
                                    <td colspan="5">INVOICE : </td>
                                                                    
                                </tr>

                                <tbody id="portalGstr2bTableBody">
                                    <?php
                                    
                                      
                                      $balance = 0;

                                    foreach ($data as $rows) {

                                        // console($rows);
                                        if ($rows['type'] === 'SOInvoicing') {
                                        $date = $rows['postingDate'];

                                        $type = $rows['type'];
                                        $transaction = $rows['type'];
                                        $details = $rows['remark'];

                                        

                                            $amount = $rows['amount'];

                                            $balance = $balance + $amount;
                                      
                                    ?>
                                        <tr>
                                            <td class="statementId" style="display:none;"><?= $rows['id'] ?></td>
                                            <td class="statementDate"><?= $date ?></td>
                                            <td class="statementTransaction"><?= $transaction ?></td>
                                            <td class="statementDebit"><?php if ($type == 'SOInvoicing') {
                                                                            echo round($amount, 2);
                                                                        } else {
                                                                            echo 0;
                                                                        } ?></td>
                                            <td class="statementCredit"> <?php if ($type == 'Collection') {
                                                                                echo round($amount, 2);
                                                                            } else {
                                                                                echo 0;
                                                                            } ?></td>

                                            <?php

                                            $id = $rows['id'];
                                            $check_reconciled = queryGet("SELECT * FROM `erp_acc_journal` WHERE `id`= $id");
                                            // console($check_reconciled);
                                            if ($check_reconciled['data']['reconcile_status'] != 0) {
                                            ?>
                                                <td class="reconColumn_green">
                                                    <ion-icon name="checkmark-outline" size="small"></ion-icon>
                                                </td>
                                                <td class=" reconColumn_green">Reconcilled</td>

                                            <?php
                                            } else {
                                            ?>
                                                <td class="reconColumn">
                                                    <input type="checkbox" name="" id="" class="reconCheckBox">
                                                </td>
                                                <td class="reconPercentageColumn reconColumn text-white">0%</td>
                                            <?php
                                            }
                                            ?>
                                        <tr>
                                        <?php
                                        }
                                    }
?>

<tr>
                                        <td class="text-left" colspan="2">Total</td>
                                        <td><?= $balance ?></td>
                                    </tr>

                                    <?php
                                    $balance_collection = 0;
                                    foreach ($data as $rows_collection) {
                                        if ($rows_collection['type'] === 'Collection') {
                                            $type = $rows_collection['type'];
                                            $transaction = $rows_collection['type'];
                                            $details = $rows_collection['remark'];
    
                                        
                                                $amount = $rows_collection['payment'];
                                                $balance_collection = $balance_collection - $amount;
                                            
                                        ?>
  <tr>
                                            <td class="statementId" style="display:none;"><?= $rows_collection['id'] ?></td>
                                            <td class="statementDate"><?= $date ?></td>
                                            <td class="statementTransaction"><?= $transaction ?></td>
                                            <td class="statementDebit"><?php if ($type == 'SOInvoicing') {
                                                                            echo round($amount, 2);
                                                                        } else {
                                                                            echo 0;
                                                                        } ?></td>
                                            <td class="statementCredit"> <?php if ($type == 'Collection') {
                                                                                echo round($amount, 2);
                                                                            } else {
                                                                                echo 0;
                                                                            } ?></td>

                                            <?php

                                            $id = $rows_collection['id'];
                                            $check_reconciled = queryGet("SELECT * FROM `erp_acc_journal` WHERE `id`= $id");
                                            // console($check_reconciled);
                                            if ($check_reconciled['data']['reconcile_status'] != 0) {
                                            ?>
                                                <td class="reconColumn_green">
                                                    <ion-icon name="checkmark-outline" size="small"></ion-icon>
                                                </td>
                                                <td class=" reconColumn_green">Reconcilled</td>

                                            <?php
                                            } else {
                                            ?>
                                                <td class="reconColumn">
                                                    <input type="checkbox" name="" id="" class="reconCheckBox">
                                                </td>
                                                <td class="reconPercentageColumn reconColumn text-white">0%</td>
                                            <?php
                                            }
                                            ?>
                                        <tr>


                                        <?php
                                        }
                                    }
                                        ?>

<tr>
                                        <td class="text-left" colspan="2">Total</td>
                                        <td><?= $balance_collection ?></td>
                                    </tr>
                                </tbody>



                            <?php
                            }
                            ?>
                        </table>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6 px-0">
                        <!-- <p class="text-center">Local Invoices</p> -->
                        <table class="table defaultdataTable gstr2aTable" id="gstr2aLocalTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction</th>
                                    <th>Debit</th>
                                    <th>Credit </th>
                                    <th><i class="fas fa-bars"></i></th>
                                </tr>
                            </thead>
                            <tr>
                                    <td colspan="5"> 
                                        <label for="" class="label-hidden">label</label>
                                    </td>
                                                                    
                                </tr>
                            <tbody id="localGstr2bTableBody">
                          
                                <?php



                                // echo $type;
                            // console($locStatement_sql);
                                if ($locStatement_sql["status"] == "success") {
                                    $rowNo = 0;
                                    $balance_payment = 0;
                                    foreach ($locStatement_sql["data"] as $data) {
                                       // console($data);                                        
                                        $balance_payment += $data['payments'] + $balance_payment;
                                        if($data['payments'] != 0){
                                   ?>
                                        <td class="partyStatementId" style="display:none;"><?= $data['id'] ?></td>
                                        <tr id="rightRow-<?= $rowNo += 1; ?>">
                                            <td class="localStatementDate"><?= $data['date'] ?></td>
                                            <td class="localStatementTransaction"><?= $data['transaction'] ?></td>
                                            <td class="localStatementDebit"><?= $data['invoice'] ?></td>
                                            <td class="localStatementCredit"><?= $data['payments'] ?></td>
                                            <td><i class="fa fa-sort"></i></td>
                                        </tr>

                                        
                                <?php
                                        }
                                    }

                                    ?>
                                      <td class="text-left" colspan="2">Total</td>
                                        <td><?= $balance_payment ?></td>
                                    </tr>

                                    <?php
                                            $balance_iv  = 0;
                                    foreach ($locStatement_sql["data"] as $data_iv) {
                                        $balance_iv += $balance_iv +$data_iv['invoice'];
                                        if($data_iv['invoice'] != 0){
                                            ?>
                                        <td class="partyStatementId" style="display:none;"><?= $data_iv['id'] ?></td>
                                        <tr id="rightRow-<?= $rowNo += 1; ?>">
                                            <td class="localStatementDate"><?= $data_iv['date'] ?></td>
                                            <td class="localStatementTransaction"><?= $data_iv['transaction'] ?></td>
                                            <td class="localStatementDebit"><?= $data_iv['invoice'] ?></td>
                                            <td class="localStatementCredit"><?= $data_iv['payments'] ?></td>
                                            <td><i class="fa fa-sort"></i></td>
                                        </tr>
                                            <?php
                                        }
                                    }


                                    ?>

<td class="text-left" colspan="2">Total</td>
                                        <td><?= $balance_iv ?></td>
                                    </tr>



                                <?php
  

                                }

                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>




















                <!-- reconcilled -->



                <h4 class="text-lg mt-3 mb-2 font-bold">Reconciled</h4>
                <div class="row p-0 m-0">
                    <div class="col-lg-6 col-md-6 col-sm-6 px-0">
                        <table class="table gstr2aTable" id="gstr2aPortalTable">
                            <thead>
                                <th style="display:none;">id</th>
                                <th>Date</th>
                                <th> Transaction</th>
                                <th>Debit</th>
                                <th>Credit </th>
                                <th style="background-color: #011a3c!important; color:white">RECON</th>
                                <th style="background-color: #011a3c!important; color:white">MATCH</th>
                            </thead>
                            <?php
                            
                            if ($party_type == 'vendor') {

                            ?>
                                <tbody id="">
                                    <?php
                                    foreach ($statement_sql_reconciled['data'] as $reconciled_row) {

                                        //  console($reconciled_row);

                                        $date = $reconciled_row['postingDate'];

                                        $type = $reconciled_row['type'];
                                        $transaction = $reconciled_row['type'];
                                        $details = $reconciled_row['remark'];

                                        if ($type === 'grniv') {

                                            $amount = $reconciled_row['amount'];

                                            $balance = $balance + $amount;
                                        } elseif ($type === 'Payment') {
                                            $amount = $reconciled_row['payment'];
                                            $balance = $balance - $amount;
                                        }
                                    ?>
                                        <tr>
                                            <td class="statementId" style="display:none;"><?= $reconciled_row['id'] ?></td>
                                            <td class="statementDate"><?= $date ?></td>
                                            <td class="statementTransaction"><?= $transaction ?></td>
                                            <td class="statementDebit"><?php if ($type == 'grniv') {
                                                                            echo round($amount, 2);
                                                                        } else {
                                                                            echo 0;
                                                                        } ?></td>
                                            <td class="statementCredit"> <?php if ($type == 'Payment') {
                                                                                echo round($amount, 2);
                                                                            } else {
                                                                                echo 0;
                                                                            } ?></td>


                                            <td class="reconColumn_green">
                                                <ion-icon name="checkmark-outline" size="small"></ion-icon>
                                            </td>
                                            <td class=" reconColumn_green text-black">Reconciled</td>




                                        <tr>
                                        <?php
                                    }
                                        ?>
                                </tbody>

                            <?php
                            } else {

                            ?>
                                <!-- customer table -->

                                <tbody id="">
                                    <?php
                                    foreach ($statement_sql_reconciled['data'] as $reconciled_row) {

                                        // console($reconciled_row);

                                        $date = $reconciled_row['postingDate'];

                                        $type = $reconciled_row['type'];
                                        $transaction = $reconciled_row['type'];
                                        $details = $reconciled_row['remark'];

                                        if ($type === 'SOInvoicing') {

                                            $amount = $reconciled_row['amount'];

                                            $balance = $balance + $amount;
                                        } elseif ($type === 'Collection') {
                                            $amount = $reconciled_row['payment'];
                                            $balance = $balance - $amount;
                                        }
                                    ?>
                                        <tr>
                                            <td class="statementId" style="display:none;"><?= $reconciled_row['id'] ?></td>
                                            <td class="statementDate"><?= $date ?></td>
                                            <td class="statementTransaction"><?= $transaction ?></td>
                                            <td class="statementDebit"><?php if ($type == 'SOInvoicing') {
                                                                            echo round($amount, 2);
                                                                        } else {
                                                                            echo 0;
                                                                        } ?></td>
                                            <td class="statementCredit"> <?php if ($type == 'Collection') {
                                                                                echo round($amount, 2);
                                                                            } else {
                                                                                echo 0;
                                                                            } ?></td>



                                            <td class="reconColumn">
                                                <input type="checkbox" name="" id="" class="reconCheckBox">
                                            </td>
                                            <td class="reconPercentageColumn reconColumn text-white">0%</td>




                                        <tr>
                                        <?php
                                    }
                                        ?>
                                </tbody>



                            <?php
                            }
                            ?>
                        </table>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6 px-0">
                        <!-- <p class="text-center">Local Invoices</p> -->
                        <table class="table defaultdataTable gstr2aTable" id="gstr2aLocalTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction</th>
                                    <th>Debit</th>
                                    <th>Credit </th>

                                </tr>
                            </thead>
                            <tbody id="">
                                <?php
                                $locStatement_sql_reconciled = queryGet("SELECT * FROM `erp_vendor_customer_reconciliation` as recon LEFT JOIN `erp_reconciliation` as files ON files.id=recon.recon_file_id WHERE files.type='" . $party_type . "' AND files.code=$party_id AND recon.recon_journal_id != 0 AND location_id=$location_id", true);
                                // echo $type;
                                // console($locStatement_sql);
                                if ($locStatement_sql_reconciled["status"] == "success") {
                                    $rowNo = 0;
                                    foreach ($locStatement_sql_reconciled["data"] as $data) {
                                ?>
                                        <tr id="rightRow-<?= $rowNo += 1; ?>">
                                            <td class="localStatementDate"><?= $data['date'] ?></td>
                                            <td class="localStatementTransaction"><?= $data['transaction'] ?></td>
                                            <td class="localStatementDebit"><?= $data['invoice'] ?></td>
                                            <td class="localStatementCredit"><?= $data['payments'] ?></td>

                                        </tr>
                                <?php
                                    }
                                }

                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>




            </div>
            <div class="row">

            </div>
        </section>

</div>
<!-- /.Content Wrapper. Contains page content -->



<?php

    }
    require_once("../common/footer.php");
?>
<script src="../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../public/assets/apexchart/chart-data.js"></script>
<script src="../public/assets/piechart/piecore.js"></script>
<script src="//www.amcharts.com/lib/4/charts.js"></script>
<script src="//www.amcharts.com/lib/4/themes/animated.js"></script>
<script src="../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../public/assets/apexchart/chart-data.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://www.amcharts.com/lib/3/amcharts.js?x"></script>
<script src="https://www.amcharts.com/lib/3/serial.js?x"></script>
<script src="https://www.amcharts.com/lib/3/themes/dark.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(document).ready(function() {
        // jQuery statements

        // $('#gstr2aLocalTable').DataTable({
        //     "searching": true,
        //     "paging": false,
        //     "info": false,
        //     "lengthChange": false,
        // });

        // $('#gstr2aPortalTable').DataTable({
        //     "searching": true,
        //     "paging": false,
        //     "info": false,
        //     "lengthChange": false,
        // });

        $('#localGstr2bTableBody').sortable({
            stop: function(event, ui) {
                calculateMatchedConditionsRows();
            }
        });

        $(document).on('click', "#addMatchedRowToBusketBtn", function() {

            addTempReconciliation();
        });

        $(document).on('click', "#matchTheTableRowBtn", function() {
            //  alert(1);
            autoMatchLocalAndPortalReconData();

        });

        function autoMatchLocalAndPortalReconData() {
            //alert("ok");
            let rowMatchedConditionsRatio = [];
            $('#portalGstr2bTableBody > tr').each(function(leftTrIndex, leftTr) {
                let leftThis = this;
                rowMatchedConditionsRatio[leftTrIndex] = rowMatchedConditionsRatio[leftTrIndex] ?? 0;
                let statementDate = $(this).find('.statementDate').text();
                let statementTransaction = $(this).find('.statementTransaction').text();
                let statementDebit = $(this).find('.statementDebit').text();
                let statementCredit = $(this).find('.statementCredit').text();
                // let portalInvoiceTaxAmt = $(this).find('.portalInvoiceTaxAmt').text();
                console.log("================ LEFT ROW ==============", leftTrIndex);
                console.log("statementDate:", statementDate);
                console.log("statementTransaction:", statementTransaction);
                console.log("statementDebit:", statementDebit);
                console.log("statementCredit:", statementCredit);
                //alert(1);

                $('#localGstr2bTableBody > tr').each(function(rightTrIndex, rightTr) {
                    let rightThis = this;
                    let localStatementDate = $(this).find('.localStatementDate').text();
                    let localStatementDebit = $(this).find('.localStatementDebit').text();
                    let localStatementTransaction = $(this).find('.localStatementTransaction').text();
                    let localStatementCredit = $(this).find('.localStatementCredit').text();
                    //  let localInvoiceTaxAmt = $(this).find('.localInvoiceTaxAmt').text();



                    let matchedConditions = 0;
                    if (statementTransaction == localStatementTransaction) {

                        matchedConditions += 25;
                      //  alert(1);
                    }
                    if (statementDate == localStatementDate) {

                        matchedConditions += 25;
                       // alert(1);
                    }
                    if (statementDebit == localStatementCredit) {
                        // alert(3);
                        matchedConditions += 25;
                        //alert(3);
                    }
                    if (statementCredit == localStatementDebit) {
                        // alert(4);
                        matchedConditions += 25;
                         //alert(4);
                    }

                    if (matchedConditions > rowMatchedConditionsRatio[leftTrIndex]) {

                        let tempRightTrData = $(`#localGstr2bTableBody tr:eq(${rightTrIndex})`).html();
                        let tempPrevRightTrData = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).html();

                        if (leftTrIndex > rightTrIndex) {
                            if (matchedConditions > rowMatchedConditionsRatio[rightTrIndex]) {
                                rowMatchedConditionsRatio[leftTrIndex] = matchedConditions;
                                $(leftThis).find('.reconPercentageColumn').html(`${matchedConditions}%`);
                                $(`#localGstr2bTableBody tr:eq(${rightTrIndex})`).html(tempPrevRightTrData);
                                $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).html(tempRightTrData);
                                // autoMatchLocalAndPortalReconData();
                            }
                        } else {
                            rowMatchedConditionsRatio[leftTrIndex] = matchedConditions;
                            $(leftThis).find('.reconPercentageColumn').html(`${matchedConditions}%`);
                            $(`#localGstr2bTableBody tr:eq(${rightTrIndex})`).html(tempPrevRightTrData);
                            $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).html(tempRightTrData);
                        }

                        console.log("========RIGHT ROW===========", rightTrIndex);
                        // console.log("localStatementDebit:", localStatementDebit);
                        // console.log("localStatementTransaction:", localStatementTransaction);
                        // console.log("localStatementCredit:", localStatementCredit);
                        // console.log("localInvoiceAmt:", localInvoiceAmt);
                        // console.log("localInvoiceTaxAmt:", localInvoiceTaxAmt);
                        // console.log("MATCHED PERCENTAGE::::", matchedConditions);

                    }
                });
            });
            $("#gstr2aPortalTable_filter input[type='search']").attr("disabled", "true");
            $("#gstr2aPortalTable th").click(function(event) {
                event.preventDefault();
            });
            $("#gstr2aLocalTable_filter input[type='search']").attr("disabled", "true");
            $("#gstr2aLocalTable th").click(function(event) {
                event.preventDefault();
            });
            calculateMatchedConditionsRows();
        }

        function calculateMatchedConditionsRows() {
            $(`#localGstr2bTableBody tr`).removeClass(`matchedRowColor-100 matchedRowColor-75 matchedRowColor-50 matchedRowColor-25`);
            $(`#portalGstr2bTableBody tr`).removeClass(`matchedRowColor-100 matchedRowColor-75 matchedRowColor-50 matchedRowColor-25`);

            $('#portalGstr2bTableBody > tr').each(function(leftTrIndex, leftTr) {
                let leftThis = this;
                let statementDate = $(this).find('.statementDate').text();
                let statementTransaction = $(this).find('.statementTransaction').text();
                let statementDebit = $(this).find('.statementDebit').text();
                let statementCredit = $(this).find('.statementCredit').text();
                // let portalInvoiceAmt = $(this).find('.portalInvoiceAmt').text();
                // let portalInvoiceTaxAmt = $(this).find('.portalInvoiceTaxAmt').text();
                // let reconPercentage = $(this).find('.reconPercentageColumn').text();

                let localStatementDate = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localStatementDate').text();
                let localStatementDebit = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localStatementDebit').text();
                let localStatementTransaction = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localStatementTransaction').text();
                let localStatementCredit = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localStatementCredit').text();
                // let localInvoiceAmt = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localInvoiceAmt').text();
                // let localInvoiceTaxAmt = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localInvoiceTaxAmt').text();

                let matchedConditions = 0;
                if (statementTransaction == localStatementTransaction) {

                    matchedConditions += 25;
                   // alert(matchedConditions);
                }
                if (statementDate == localStatementDate) {

                    matchedConditions += 25;
                   // alert(matchedConditions);
                }
                if (statementDebit == localStatementCredit) {
                    
                    matchedConditions += 25;
                     //   alert(matchedConditions);
                }
                if (statementCredit == localStatementDebit) {
                   
                    matchedConditions += 25;
                    // alert(matchedConditions);
                }

                $(leftThis).find('.reconPercentageColumn').html(`${matchedConditions}%`);
                $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).addClass(`matchedRowColor-${matchedConditions}`);
                $(leftTr).addClass(`matchedRowColor-${matchedConditions}`);
                if (matchedConditions == 100) {
                    $(leftThis).find('.reconCheckBox').prop('checked', true);
                } else {
                    $(leftThis).find('.reconCheckBox').prop('checked', false);
                }
                // console.log(matchedConditions);
            });
        }


        function addTempReconciliation() {

            let reconData = [];
            $('#portalGstr2bTableBody > tr').each(function(leftTrIndex, leftTr) {
                let leftThis = this;
                let isChecked = $(leftThis).find('.reconCheckBox').prop('checked');
                if (isChecked) {

                    let statementDate = $(this).find('.statementDate').text();
                    let statementTransaction = $(this).find('.statementTransaction').text();
                    let statementDebit = $(this).find('.statementDebit').text();
                    let statementCredit = $(this).find('.statementCredit').text();
                    let statementId = $(this).find('.statementId').text();

                    // let portalInvoiceTaxAmt = $(this).find('.portalInvoiceTaxAmt').text();

                    let reconPercentage = ($(this).find('.reconPercentageColumn').text()).slice(0, -1);

                    let localStatementDate = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localStatementDate').text();
                    let localStatementDebit = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localStatementDebit').text();
                    let localStatementTransaction = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localStatementTransaction').text();
                    let localStatementCredit = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localStatementCredit').text();
                    let partyStatementId = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.partyStatementId').text();
                    // let localInvoiceAmt = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localInvoiceAmt').text();
                    // let localInvoiceTaxAmt = $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).find('.localInvoiceTaxAmt').text();
                    //  alert(localStatementDate);
                    let matchedConditions = 0;
                    if (statementTransaction == localStatementTransaction) {

                        matchedConditions += 25;
                    }
                    if (statementDate == localStatementDate) {

                        matchedConditions += 25;
                    }
                    if (statementDebit == localStatementCredit) {
                        // alert(3);
                        matchedConditions += 25;
                    }
                    if (statementCredit == localStatementDebit) {
                        // alert(4);
                        matchedConditions += 25;
                    }
                    // alert(matchedConditions);
                    reconData[leftTrIndex] = {
                        statementDate,
                        statementTransaction,
                        statementDebit,
                        statementCredit,
                        statementId,
                        // portalInvoiceTaxAmt,

                        reconPercentage,

                        localStatementDate,
                        localStatementDebit,
                        localStatementTransaction,
                        localStatementCredit,
                        matchedConditions,
                        partyStatementId
                        // localInvoiceAmt,
                        //localInvoiceTaxAmt
                    };

                    console.log(reconData);
                }
            });
            if (reconData.length > 0) {
                console.log(reconData);
                $.ajax({
                    method: "post",
                    url: "ajaxs/reconciliation/ajax-reconcile-post.php",
                    data: {
                        reconData: reconData
                    },
                    beforeSend: function() {
                        console.log("beforeSend");
                    },
                    success: function(data) {
                        // alert(data);

                        //  let reconObj = JSON.parse(data);
                        //  let status = reconObj["status"];
                        //  let message = reconObj["message"];
                        //  let url = <?= PHP_SELF ?>

                        //  swalAlert(status, ucfirst(status),message,url);
                        location.reload();

                        // let reconTableIndex = reconObj["data"];
                        // let reconListCounter = reconObj["listCounter"];
                        // let reconListAmount = reconObj["listTotalTax"];
                        // $("#reconListCounterSpan").html(reconListCounter);
                        // $(".reconListAmountSpan").html(reconListAmount);
                        // reconTableIndex.forEach(function(reconTableIndex, index) {
                        //     $(`#portalGstr2bTableBody tr:eq(${reconTableIndex})`).remove();
                        //     $(`#localGstr2bTableBody tr:eq(${reconTableIndex})`).remove();
                        // });
                        console.log("response from ajax:");
                        console.log(data);
                        console.log(reconObj);
                    }
                });
            } else {
                alert("Please select atleast one invoice to reconciliation!");
            }
        }


        $(document).on("click", "#tempReconListModalBtn", function() {
            $.ajax({
                method: "get",
                url: "ajaxs/compliance/ajax-gstr2b-temp-reconciliation.php",
                beforeSend: function() {
                    console.log("beforeSend");
                },
                success: function(data) {
                    $("#tempReconListModalContent").html(data);
                    console.log(data);
                }
            });
        });

    });
</script>







<!-- compliance auth modal -->
<script>
    var otp_inputs = document.querySelectorAll(".otp__digit");
    var mykey = "0123456789".split("");
    otp_inputs.forEach((_) => {
        _.addEventListener("keyup", handle_next_input);
    });

    function handle_next_input(event) {
        let current = event.target;
        let index = parseInt(current.classList[1].split("__")[2]);
        current.value = event.key;

        if (event.keyCode == 8 && index > 1) {
            current.previousElementSibling.focus();
        }
        if (index < 6 && mykey.indexOf("" + event.key + "") != -1) {
            var next = current.nextElementSibling;
            next.focus();
        }
        var _finalKey = "";
        for (let {
                value
            }
            of otp_inputs) {
            _finalKey += value;
        }
        if (_finalKey.length == 6) {
            document.querySelector("#_otp").classList.replace("_notok", "_ok");
            document.querySelector("#_otp").innerText = _finalKey;
        } else {
            document.querySelector("#_otp").classList.replace("_ok", "_notok");
            document.querySelector("#_otp").innerText = _finalKey;
        }
    }
    $(document).ready(function() {
        $("#connectBtn").click(function() {

            $.ajax({
                method: "POST",
                url: "ajaxs/compliance/ajax-compliance-auth.php",
                data: {
                    authType: "sendOtp"
                },
                beforeSend: function() {
                    $("#connectBtn").html(`Processing...`);
                },
                success: function(data) {
                    let dataObj = JSON.parse(data);
                    if (dataObj["status"] == "success") {
                        $("#firstStep").hide();
                        $("#secondStep").show();
                    } else {
                        $("#connectBtn").html(`<button class="btn btn-primary connect-btn">Try again to Connect</button>`);
                        Swal.fire({
                            icon: `warning`,
                            title: `Warning`,
                            text: `${dataObj["message"]}`,
                        });
                        console.log(dataObj["message"]);
                    }
                    // console.log(dataObj);
                }
            });
        });

        $("#verifyBtn").click(function() {
            $("#invalidOtpSpan").html("");
            $("#otpRequiredSpan").html("");
            let userOtp = "";
            $('.otp-input-fields').children('input[type=text], select').each(function() {
                console.log(userOtp = `${userOtp}${$(this).val()}`)
            });
            if (userOtp.toString().length == 6) {
                $.ajax({
                    method: "POST",
                    url: "ajaxs/compliance/ajax-compliance-auth.php",
                    data: {
                        authType: "verifyOtp",
                        authOtp: userOtp
                    },
                    beforeSend: function() {
                        $("#verifyBtn").html(`Processing...`);
                    },
                    success: function(data) {
                        let dataObj = JSON.parse(data);
                        if (dataObj["status"] == "success") {
                            $("#otpInputFields").hide();
                            $("#verifyOTP").hide();
                            $("#otpCountTime").show();
                            $(".connected-text").show();

                            $("#verifyBtn").html("");
                            $("#robotOtpImage").attr("src", "<?= BASE_URL ?>public/assets/gif/green-bot.gif");
                            $(".connected-text").html("Great! Now I am ready to be executed.");
                        } else {
                            $("#invalidOtpSpan").html("Please enter valid OTP!");
                            $("#verifyBtn").html(`<button class="btn btn-primary verify-otp-btn" id="verifyOTP">Verify OTP</button>`);
                        }
                        // console.log(dataObj);
                    }
                });
            } else {
                $("#otpRequiredSpan").html("Please enter OTP");
            }
        });
    });
    let digitValidate = function(ele) {
        console.log(ele.value);
        ele.value = ele.value.replace(/[^0-9]/g, '');
    }

    let tabChange = function(val) {
        let ele = document.querySelectorAll('input');
        if (ele[val - 1].value != '') {
            ele[val].focus()
        } else if (ele[val - 1].value == '') {
            ele[val - 2].focus()
        }
        $("#otpRequiredSpan").html("");
    }
</script>
<!-- / end compliance auth modal -->


<script>
    $(document).ready(function() {
        $('.viewBtn').on('click', function() {
            var attr = $(this).data('attr');
            // alert(attr);
            // alert(1);
            $.ajax({
                url: `ajaxs/reconciliation/preview.php?type=reconcile&id=${attr}`,
                type: 'GET',

                success: function(response) {
                    // alert(response);
                    $('.excelData_' + attr).html(response);
                    $('.previewModal_' + attr).show();
                }
            });


            function submitForm() {
                //alert(1);
                var tableData = [];
                var table = document.getElementById("previewTable");
                var rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
                var id = $("#id").val();
                // alert(id);
                // exit();

                for (var i = 0; i < rows.length; i++) {
                    var rowData = [];
                    var cells = rows[i].getElementsByTagName("td");
                    for (var j = 0; j < cells.length; j++) {
                        rowData.push(cells[j].innerHTML);
                    }
                    tableData.push(rowData);
                }

                $.ajax({
                    url: "ajaxs/reconciliation/insert.php",
                    type: "POST",
                    data: {
                        tableData: JSON.stringify(tableData),
                        id: id
                    },
                    success: function(response) {
                        var returnData = JSON.parse(response);
                        //  alert(returnData);
                        console.log(response);
                        // Check the status and message
                        // if (returnData.status === "success" && returnData.message === "Stock Count Inserted") {
                        //     // Display a success alert using alert()
                        //     alert('Stock Count Inserted');
                        // }
                    }
                });
            }

            $('.insertButton_' + attr).click(function() {
                //alert(1);
                submitForm();

                // alert(attr);

            });

        });




        // $(document).on('click','.insertButton', function() {


        //     console.log("gytfdresedrftgyhujikol");

        //     //submitForm();
        // });


    });
</script>


<script>
    // Function to trigger the download
    function downloadExcel() {
        // Path to your demo Excel file
        var excelFilePath = '../../public/demo.xlsx';

        // Create a link element
        var link = document.createElement('a');
        link.href = excelFilePath;

        // Set the download attribute and file name
        link.download = 'demo.xlsx';

        // Programmatically click the link to trigger the download
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Attach the downloadExcel function to the button's click event
    var downloadButton = document.getElementById('downloadBtn');
    downloadButton.addEventListener('click', downloadExcel);
</script>