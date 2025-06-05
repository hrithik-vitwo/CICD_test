<?php
require_once("../app/v1/connection-company-admin.php");
administratorAuth();
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
require_once("common/pagination.php");
require_once("../app/v1/functions/branch/func-customers.php");
require_once("../app/v1/functions/branch/func-journal.php");
require_once("../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../app/v1/functions/admin/func-company.php");

class TrailBalance
{
    protected $company_id;
    protected $branch_id;
    protected $location_id;
    protected $created_by;
    protected $updated_by;
    function __construct()
    {
        global $company_id, $branch_id, $location_id, $created_by, $updated_by;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->location_id = $location_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }

    function getTrailBalanceDetailView($startDate = null, $endDate = null)
    {

        $erp_acc_coa = ERP_ACC_CHART_OF_ACCOUNTS;
        global $compOpeningDate;

        $oppeningDate = $compOpeningDate;
        $oppYerMonth = date("Y-m", strtotime($oppeningDate));
        $startYerMonth = date("Y-m", strtotime($startDate));

        if ($oppYerMonth != $startYerMonth) {

            // echo 'Ramen1-----------------------';
            $sql = 'SELECT log.gl_code,log.gl_label,SUM(log.debit_amount) AS debit_amount,SUM(log.credit_amount) AS credit_amount,opening.open_bal AS open_balance FROM

                (SELECT
                
                    coa.gl_code,
                
                    coa.gl_label,
                
                    SUM(
                
                        CASE WHEN table1.Type = "DR" THEN table1.Amount ELSE 0
                
                    END
                
                ) AS debit_amount,
                
                SUM(
                
                    CASE WHEN table1.Type = "CR" THEN table1.Amount ELSE 0
                
                END
                
                ) AS credit_amount
                
                FROM
                
                    (
                
                        (
                
                        SELECT
                
                            journal.postingDate AS postingDate,
                
                            debit.glId AS glId,
                
                            debit.debit_amount AS Amount,
                
                            "DR" AS TYPE
                
                        FROM
                
                            erp_acc_journal AS journal
                
                        INNER JOIN(
                
                            SELECT
                
                                journal_id,
                
                                glId,
                
                                SUM(debit_amount) AS debit_amount
                
                            FROM
                
                                erp_acc_debit
                
                            GROUP BY
                
                                journal_id,
                
                                glId
                
                        ) AS debit
                
                    ON
                
                        debit.journal_id = journal.id
                
                    WHERE
                
                        journal.journal_status = "active" AND journal.company_id = ' . $this->company_id . '
                
                    )
                
                UNION
                
                    (
                
                    SELECT
                
                        journal.postingDate AS postingDate,
                
                        credit.glId AS glId,
                
                        credit.credit_amount *(-1) AS Amount,
                
                        "CR" AS TYPE
                
                    FROM
                
                        erp_acc_journal AS journal
                
                    INNER JOIN(
                
                        SELECT
                
                            journal_id,
                
                            glId,
                
                            SUM(credit_amount) AS credit_amount
                
                        FROM
                
                            erp_acc_credit
                
                        GROUP BY
                
                            journal_id,
                
                            glId
                
                    ) AS credit
                
                ON
                
                    credit.journal_id = journal.id
                
                WHERE
                
                    journal.journal_status = "active" AND journal.company_id = ' . $this->company_id . '
                
                )
                
                    ) AS table1
                
                INNER JOIN `' . $erp_acc_coa . '` AS coa
                
                ON
                
                    table1.glId = coa.id
                
                WHERE
                
                    table1.postingDate BETWEEN "' . $startDate . '" AND "' . $endDate . '"
                
                GROUP BY
                
                    coa.gl_code,
                
                    coa.gl_label,
                
                    table1.Type
                
                ORDER BY
                
                    coa.gl_code,
                
                    coa.gl_label) AS log LEFT JOIN (SELECT table1.gl_code,SUM(table1.total_amount)AS open_bal FROM
                
                ((SELECT coa.gl_code,SUM(balance.closing_val) AS total_amount FROM erp_opening_closing_balance AS balance INNER JOIN `' . $erp_acc_coa . '` AS coa ON balance.gl=coa.id WHERE balance.company_id=' . $this->company_id . ' AND DATE_FORMAT(balance.date,"%m-%Y")=DATE_FORMAT(DATE_SUB("' . $startDate . '",INTERVAL 1 MONTH),"%m-%Y") GROUP BY coa.gl_code)
                
                UNION
                
                (SELECT coa.gl_code AS gl_code,SUM(table1.Amount) AS total_amount FROM
                
                ((SELECT
                
                        journal.postingDate AS postingDate,
                
                        debit.glId AS glId,
                
                        debit.debit_amount AS Amount,
                
                        "DR" AS Type
                
                    FROM
                
                        erp_acc_journal AS journal
                
                    INNER JOIN
                
                        (SELECT journal_id,glId,SUM(debit_amount) as debit_amount FROM erp_acc_debit GROUP BY journal_id,glId) AS debit
                
                    ON
                
                        debit.journal_id = journal.id
                
                    WHERE
                
                        journal.journal_status="active" AND
                
                        journal.company_id = ' . $this->company_id . ')
                
                    UNION
                
                    (SELECT
                
                        journal.postingDate AS postingDate,
                
                        credit.glId AS glId,
                
                        credit.credit_amount*(-1) AS Amount,
                
                        "CR" AS Type
                
                    FROM
                
                        erp_acc_journal AS journal
                
                    INNER JOIN
                
                        (SELECT journal_id,glId,SUM(credit_amount) as credit_amount FROM erp_acc_credit GROUP BY journal_id,glId) AS credit
                
                    ON
                
                        credit.journal_id = journal.id
                
                    WHERE
                
                        journal.journal_status="active" AND
                
                        journal.company_id = ' . $this->company_id . ')) AS table1 INNER JOIN `' . $erp_acc_coa . '` AS coa ON table1.glId = coa.id
                
                WHERE
                
                table1.postingDate BETWEEN DATE_SUB("' . $startDate . '",INTERVAL (DATE_FORMAT("' . $startDate . '","%d")-1) DAY) AND DATE_SUB("' . $startDate . '",INTERVAL 1 DAY)
                
                GROUP BY
                
                coa.gl_code,coa.gl_label
                
                ORDER BY
                
                coa.gl_code,coa.gl_label)) AS table1
                
                GROUP BY table1.gl_code) AS opening ON log.gl_code = opening.gl_code GROUP BY log.gl_code,log.gl_label,open_balance';
        } else {

            // echo 'Ramen2-----------------------';
            $sql = 'SELECT log.gl_code,log.gl_label,SUM(log.debit_amount) AS debit_amount,SUM(log.credit_amount) AS credit_amount,opening.open_bal AS open_balance FROM

                (SELECT

                    coa.gl_code,

                    coa.gl_label,

                    SUM(

                        CASE WHEN table1.Type = "DR" THEN table1.Amount ELSE 0

                    END

                ) AS debit_amount,

                SUM(

                    CASE WHEN table1.Type = "CR" THEN table1.Amount ELSE 0

                END

                ) AS credit_amount

                FROM

                    (

                        (

                        SELECT

                            journal.postingDate AS postingDate,

                            debit.glId AS glId,

                            debit.debit_amount AS Amount,

                            "DR" AS TYPE

                        FROM

                            erp_acc_journal AS journal

                        INNER JOIN(

                            SELECT

                                journal_id,

                                glId,

                                SUM(debit_amount) AS debit_amount

                            FROM

                                erp_acc_debit

                            GROUP BY

                                journal_id,

                                glId

                        ) AS debit

                    ON

                        debit.journal_id = journal.id

                    WHERE

                    journal.journal_status = "active" AND journal.company_id = ' . $this->company_id . '                 
                    )

                UNION

                    (

                    SELECT

                        journal.postingDate AS postingDate,

                        credit.glId AS glId,

                        credit.credit_amount *(-1) AS Amount,

                        "CR" AS TYPE

                    FROM

                        erp_acc_journal AS journal

                    INNER JOIN(

                        SELECT

                            journal_id,

                            glId,

                            SUM(credit_amount) AS credit_amount

                        FROM

                            erp_acc_credit

                        GROUP BY

                            journal_id,

                            glId

                    ) AS credit

                ON

                    credit.journal_id = journal.id

                WHERE

                journal.journal_status = "active" AND journal.company_id = ' . $this->company_id . '                    
                )

                    ) AS table1

                INNER JOIN `' . $erp_acc_coa . '` AS coa

                ON

                    table1.glId = coa.id

                WHERE

                    table1.postingDate BETWEEN "' . $startDate . '" AND "' . $endDate . '"

                GROUP BY

                    coa.gl_code,

                    coa.gl_label,

                    table1.Type

                ORDER BY

                    coa.gl_code,

                    coa.gl_label) AS log LEFT JOIN (SELECT table1.gl_code,SUM(table1.total_amount)AS open_bal FROM

                ((SELECT coa.gl_code,SUM(balance.closing_val) AS total_amount FROM erp_opening_closing_balance AS balance INNER JOIN `' . $erp_acc_coa . '` AS coa ON balance.gl=coa.id WHERE balance.company_id=' . $this->company_id . ' AND DATE_FORMAT(balance.date,"%m-%Y")=DATE_FORMAT("' . $startDate . '","%m-%Y") GROUP BY coa.gl_code)
                UNION

                (SELECT coa.gl_code AS gl_code,SUM(table1.Amount) AS total_amount FROM

                ((SELECT

                        journal.postingDate AS postingDate,

                        debit.glId AS glId,

                        debit.debit_amount AS Amount,

                        "DR" AS Type

                    FROM

                        erp_acc_journal AS journal

                    INNER JOIN

                        (SELECT journal_id,glId,SUM(debit_amount) as debit_amount FROM erp_acc_debit GROUP BY journal_id,glId) AS debit

                    ON

                        debit.journal_id = journal.id

                    WHERE

                        journal.journal_status="active" AND

                        journal.company_id = ' . $this->company_id . ')

                    UNION

                    (SELECT

                        journal.postingDate AS postingDate,

                        credit.glId AS glId,

                        credit.credit_amount*(-1) AS Amount,

                        "CR" AS Type

                    FROM

                        erp_acc_journal AS journal

                    INNER JOIN

                        (SELECT journal_id,glId,SUM(credit_amount) as credit_amount FROM erp_acc_credit GROUP BY journal_id,glId) AS credit

                    ON

                        credit.journal_id = journal.id

                    WHERE

                        journal.journal_status="active" AND

                        journal.company_id = ' . $this->company_id . ')) AS table1 INNER JOIN `' . $erp_acc_coa . '` AS coa ON table1.glId = coa.id

                WHERE

                table1.postingDate BETWEEN DATE_SUB("' . $startDate . '",INTERVAL (DATE_FORMAT("' . $startDate . '","%d")-1) DAY) AND DATE_SUB("' . $startDate . '",INTERVAL 1 DAY)

                GROUP BY

                coa.gl_code,coa.gl_label

                ORDER BY

                coa.gl_code,coa.gl_label)) AS table1

                GROUP BY table1.gl_code) AS opening ON log.gl_code = opening.gl_code GROUP BY log.gl_code,log.gl_label,open_balance';
        }
        // echo $sql;

        return $resultObj = queryGet($sql, true);
    }
}

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}
?>


<style>
    .content-wrapper table tr:nth-child(2n+1) td {
        background: #b5c5d3;
    }

    tfoot.individual-search tr th {
        padding: 5px !important;
        border-right: 1px solid #fff !important;
    }

    .vertical-align {
        vertical-align: middle;
    }

    /* .green-text {
    color: #14ca14 !important;
    font-weight: 600;
  }

  .red-text {
    color: red !important;
    font-weight: 600;
  } */

    .dataTables_scrollHeadInner tr th {
        position: sticky;
        top: -1px;
    }

    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row {
        display: flex !important;
        align-items: center;
        justify-content: end;
    }

    /* div.dataTables_wrapper {
    overflow: hidden;
  } */

    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row:nth-child(1),
    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row:nth-child(3) {
        padding: 10px 20px;
    }

    div.dataTables_wrapper div.dataTables_length select {
        width: 60% !important;
        appearance: none !important;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    .dataTables_scroll {
        position: relative;
        margin-bottom: 10px;
    }

    .dataTables_scroll::-webkit-scrollbar {
        visibility: hidden;
    }

    .dataTables_scrollBody tfoot th {
        background: none !important;
    }

    .dataTables_scrollHead {
        margin-bottom: 40px;
    }

    .dataTables_scrollBody {
        max-height: 75vh !important;
        height: 75% !important;
        overflow: scroll !important;
    }

    .dataTables_scrollFoot {
        position: absolute;
        top: 37px;
        height: 50px;
        overflow: scroll;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 10px;
    }

    div.dataTables_scrollFoot>.dataTables_scrollFootInner th {
        border: 0;
    }

    .dataTables_filter {
        padding-right: 0 !important;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        padding: 0;
        border: 0;
    }

    .dt-top-container {
        display: flex;
        align-items: center;
        padding: 0 20px;
        gap: 20px;
    }

    .transactional-book-table tr td {
        white-space: pre-line !important;
    }

    .dataTables_length {
        margin-left: 4em;
    }

    a.btn.add-col.setting-menu.waves-effect.waves-light {
        position: absolute !important;
        display: flex;
        justify-content: space-between;
        top: 10px !important;
    }

    div.dataTables_wrapper div.dataTables_length label {
        margin-bottom: 0;
    }

    div.dataTables_wrapper div.dataTables_info {
        padding-left: 20px;
        position: relative;
        top: 0;
    }

    .dataTables_paginate {
        position: relative;
        right: 20px;
        bottom: 20px;
        margin-top: -15px;
    }

    .dt-center-in-div {
        display: block;
        /* order: 3; */
        margin-left: auto;
    }

    .dt-buttons.btn-group.flex-wrap button {
        background-color: #003060 !important;
        border-color: #003060 !important;
        border-radius: 7px !important;
    }

    /* .setting-row .col .btn.setting-menu {
    position: absolute !important;
    right: 255px;
    top: 10px;
  } */

    .dt-buttons.btn-group.flex-wrap {
        gap: 10px;
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

    .dataTable thead tr th,
    .dataTable tfoot.individual-search tr th {
        padding-right: 30px !important;
        border-right: 0 !important;
    }

    select.fy-dropdown {
        position: absolute;
        max-width: 100px;
        top: 14px;
        left: 255px;
    }

    .daybook-filter-list.filter-list {
        display: flex;
        gap: 7px;
        justify-content: flex-end;
        position: relative;
        top: 16px;
        left: 19px;
        float: right;
        margin-bottom: 2em;
    }

    .daybook-filter-list.filter-list a.active {
        background-color: #003060;
        color: #fff;
    }

    .date-range-input {
        gap: 7px;
    }

    .date-range-input .form-input {
        width: 100%;
    }

    @media (max-width: 769px) {
        .dt-buttons.btn-group.flex-wrap {
            gap: 10px;
            position: absolute;
            top: -39px;
            right: 60px;
        }

        .dt-buttons.btn-group.flex-wrap button {
            max-width: 60px;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            margin-top: -10px;
        }


    }

    @media (max-width :575px) {
        .dataTables_scrollFoot {
            position: absolute;
            top: 28px;
        }

        .dt-top-container {
            display: flex;
            align-items: baseline;
            padding: 0 20px;
            gap: 20px;
            flex-direction: column-reverse;
            flex-wrap: nowrap;
        }

        .dataTables_length {
            margin-left: 0;
            margin-bottom: 1em;
        }

        select.fy-dropdown {
            position: absolute;
            max-width: 125px;
            top: 155px;
            left: 189px;
        }

        div.dataTables_wrapper div.dataTables_length select {
            width: 164px !important;
        }

        .dt-center-in-div {
            margin: 3px auto;
        }

        div.dataTables_filter {
            right: 0;
            margin-top: 0;
            position: relative;
            right: -43px;
        }

        .dt-buttons.btn-group.flex-wrap {
            gap: 10px;
            position: relative;
            top: 0;
            right: 0;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            margin-top: 40px;
        }

        .dataTables_length label {
            font-size: 0;
        }
    }

    @media (max-width: 376px) {
        div.dataTables_wrapper div.dataTables_filter {
            margin-top: 0;
            padding-left: 0 !important;
        }

        select.fy-dropdown {
            position: absolute;
            max-width: 109px;
            top: 144px;
            left: 189px;
        }

        div.dataTables_wrapper div.dataTables_filter input {
            max-width: 150px;
        }

        select.fy-dropdown {
            max-width: 100px;
        }

        /* div.dataTables_wrapper div.dataTables_length select {
      width: 164px !important;
    } */
    }
</style>

<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <?php
                $trailBalanceObj = new TrailBalance();
                $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);
                                                
                if (isset($_POST['from_date'])) {
                    $start_date = $_POST['from_date'];
                    $end_date = $_POST['to_date'];
                    //echo 1;


                } else {

                    $start = explode('-', $variant_sql['data'][0]['year_start']);
                    $end = explode('-', $variant_sql['data'][0]['year_end']);
                    $start_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                    $end_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
                    $_POST['from_date']=$start_date;
                    $_POST['to_date']=$end_date;
                    $_POST['drop_val']='fYDropdown';
                    $_POST['drop_id']=$variant_sql['data'][0]['year_variant_id'];

                }
                $trailBalanceDetailsObj = $trailBalanceObj->getTrailBalanceDetailView($start_date, $end_date);
                // console($trailBalanceDetailsObj);
                ?>
            </div>

            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">

                    <div class="card card-tabs" style="border-radius: 20px;">
                        <script>
                            var input = document.getElementById("myInput");
                            input.addEventListener("keypress", function(event) {
                                if (event.key === "Enter") {
                                    event.preventDefault();
                                    document.getElementById("myBtn").click();
                                }
                            });
                            var form = document.getElementById("search");
                            document.getElementById("myBtn").addEventListener("click", function() {
                                form.submit();
                            });
                        </script>
                        <div class="tab-content pt-0" id="custom-tabs-two-tabContent">

                            <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                <!---------------------- Search START -->
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                                    <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                                        <div class="label-select">
                                            <h3 class="card-title mb-0">Trail Balance (Detailed View)</h3>
                                        </div>

                                        <div class="fy-custom-section">
                                            <div class="fy-dropdown-section">
                                                <h6 class="text-xs font-bold">Financial Year</h6>
                                                <div class="dropdown-fyear">
                                                    <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                                                        <option value="">--Select FY--</option>
                                                        <?php
                                                        foreach ($variant_sql['data'] as $key => $data) {
                                                            $start = explode('-', $data['year_start']);
                                                            $end = explode('-', $data['year_end']);
                                                            $startDate = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                                                            $endDate = date('Y-m-t', strtotime("$end[0]-$end[1]"));

                                                        ?>
                                                            <option value="<?= $data['year_variant_id'] ?>" data-start="<?= $startDate ?>" data-end="<?= $endDate ?>" <?php if (($_POST['drop_val'] == 'fYDropdown' && $_POST['drop_id'] == $data['year_variant_id'])) {
                                                                                                                                                                            echo "selected";
                                                                                                                                                                        } ?>><?= $data['year_variant_name'] ?></option>
                                                        <?php
                                                        }
                                                        ?>

                                                        <option value="customrange" <?php if ($_POST['drop_id'] == '') {
                                                                                        echo "selected";
                                                                                    } ?>>
                                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                                                        </option>
                                                    </select>

                                                    <label class="mb-0" for="">OR</label>


                                                    <select name="quickDropdown" id="quickDropdown" class="form-control quick-dropdown">
                                                        <option value="">--Select One--</option>
                                                        <option value="0" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 0) {
                                                                                echo "selected";
                                                                            } ?>>Today Report</option>
                                                        <option value="6" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 6) {
                                                                                echo "selected";
                                                                            } ?>>Last 7 Days</option>
                                                        <option value="14" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 14) {
                                                                                echo "selected";
                                                                            } ?>>Last 15 Days</option>
                                                        <option value="29" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 29) {
                                                                                echo "selected";
                                                                            } ?>>Last 30 Days</option>
                                                        <option value="44" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 44) {
                                                                                echo "selected";
                                                                            } ?>>Last 45 Days</option>
                                                        <option value="59" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 59) {
                                                                                echo "selected";
                                                                            } ?>>Last 60 Days</option>
                                                    </select>
                                                </div>
                                                
                                                <h6 class="text-xs font-bold "><span class="finacialYearCla"></span></h6>
                                            </div>

                                            <div class="customrange-section">
                                                <h6 class="text-xs font-bold ">Custom Range</h6>
                                                <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                                                    <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="" />
                                                    <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange" />
                                                    <div class="date-range-input d-flex">
                                                        <div class="form-input">
                                                            <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $_POST['from_date']; ?>" required>
                                                        </div>
                                                        <div class="form-input">
                                                            <label class="mb-0" for="">To</label>
                                                            <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary float-right" id="rangeid" name="add_date_form">Apply</button>
                                                </form>
                                                <h6 class="text-xs font-bold "><span class="customRangeCla"></span></h6>
                                            </div>

                                            <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>

                                        </div>



                                    </li>

                                    <!-- <div class="daybook-filter-list filter-list">
                                        <a href="manage-daybook.php" class="btn  waves-effect waves-light"><i class="fa fa-stream mr-2 "></i>Transactional Day Book</a>
                                        <a href="<?php echo $_SERVER['PHP_SELF'];?>" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active"></i>Trial Balance(Detailed View)</a>
                                    </div> -->

                                    <!---------------------- Search END -->




                                </ul>
                                <table class="table defaultDataTable table-hover text-nowrap">
                                    <thead>
                                        <tr class="alert-light">
                                            <th rowspan="2" class="border vertical-align">GL Code</th>
                                            <th rowspan="2" class="border vertical-align">GL Name</th>
                                            <th rowspan="2" class="border vertical-align">Opening</th>
                                            <th rowspan="2" class="border vertical-align">Debit</th>
                                            <th rowspan="2" class="border vertical-align">Credit</th>
                                            <th rowspan="2" class="text-center border">Closing</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $grandTotalDebit = 0;
                                        $grandTotalCredit = 0;
                                        foreach ($trailBalanceDetailsObj["data"] as $row) {
                                            $grandTotalDebit += $row["debit_amount"];
                                            $grandTotalCredit += $row["credit_amount"];
                                        ?>
                                            <tr>
                                                <td><?= $row["gl_code"] ?></td>
                                                <td><?= $row["gl_label"] ?></td>
                                                <td class="text-right"><?= number_format($row["open_balance"], 2) ?></td>
                                                <td class="text-right"><?= number_format($row["debit_amount"], 2) ?></td>
                                                <td class="text-right"><?= number_format($row["credit_amount"], 2) ?></td>
                                                <td class="text-right"><?= number_format($row["open_balance"] + $row["debit_amount"] + $row["credit_amount"], 2) ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <td></td>
                                            <td class="font-weight-bold">Total</td>
                                            <td class="text-right font-weight-bold"></td>
                                            <td class="text-right font-weight-bold"><?= number_format($grandTotalDebit, 2) ?></td>
                                            <td class="text-right font-weight-bold"><?= number_format($grandTotalCredit, 2) ?></td>
                                            <td class="text-right font-weight-bold"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <?php
                                if ($count > 0 && $count > $GLOBALS['show']) {
                                ?>
                                    <div class="pagination align-right">
                                        <?php pagination($count, "frm_opts"); ?>
                                    </div>
                                    <!-- End .pagination -->
                                <?php
                                } ?>
                            </div>
                        </div>
                        <!---------------------------------Table settings Model Start--------------------------------->

                        <div class="modal" id="myModal2">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Table Column Settings</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                                        <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                        <input type="hidden" name="pageTableName" value="ERP_TRANCTIONALDAYBOOK_Trial_Balance_Detailed" />
                                        <div class="modal-body">
                                            <div id="dropdownframe"></div>
                                            <div id="main2">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="add-table-settings" class="btn btn-success">Save</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!---------------------------------Table Model End--------------------------------->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->
<?php
require_once("common/footer.php");
?>
<!-- CHANGES -->
<script>
    $(function() {
        $('input[name="daterange"]').daterangepicker({
            opens: 'left'
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
    });
    $('#fYDropdown').change(function() {
        var title = $(this).val();
        if (title == "customrange") {
            $("#drop_val").val('customrange');
            $("#from_date").val('');
            $("#to_date").val('');
            $("#from_date").focus();
        } else {
            let start = $(this).find(':selected').data('start');
            let end = $(this).find(':selected').data('end');
            //alert(start);
            $("#from_date").val(start);
            $("#to_date").val(end);
            $("#drop_val").val('fYDropdown');
            $("#drop_id").val(title);
            $('#date_form').submit();
        }
    });

    $('#quickDropdown').change(function() {
        var days = $(this).val();
        var today = new Date();
        var seven_days_ago = new Date(today.getTime() - (days * 24 * 60 * 60 * 1000));

        var end = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
        var start = seven_days_ago.getFullYear() + '-' + ('0' + (seven_days_ago.getMonth() + 1)).slice(-2) + '-' + ('0' + seven_days_ago.getDate()).slice(-2);

        // alert(start);
        // alert(end);
        $("#from_date").val(start);
        $("#to_date").val(end);
        $("#drop_val").val('quickDrop');
        $("#drop_id").val(days);

        $('#date_form').submit();
    });

    function compare_date() {
        let fromDate = $("#from_date").val();
        let toDate = $("#to_date").val();

        const date1 = new Date(fromDate);
        const date2 = new Date(toDate);
        const diffTime = Math.abs(date2 - date1);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (fromDate && toDate) {
            if (diffDays > 366) {
                document.getElementById("rangeid").disabled = true;
                $(".customRangeCla").html(`<p class="text-danger text-xs prdatelabel">Date Range can not be greater than 1 year</p>`);
            } else {
                $(".customRangeCla").html('');
                document.getElementById("rangeid").disabled = false;

                if (toDate < fromDate) {
                    $(".customRangeCla").html(`<p class="text-danger text-xs prdatelabel">From Date can not be greater than To Date</p>`);
                    document.getElementById("rangeid").disabled = true;

                } else {
                $(".customRangeCla").html('');
                    document.getElementById("rangeid").disabled = false;
                }
            }
        }
    }

    $("#to_date").keyup(function() {
        compare_date();
    });

    $("#from_date").change(function() {
        compare_date();
    });

    $("#to_date").change(function() {
        compare_date();
    });
</script>
<!-- CHANGES -->
<script>
    function srch_frm() {
        if ($('#form_date_s').val().trim() != '' && $('#to_date_s').val().trim() === '') { //$("#phone_r_err").css('display','block');
            //$("#phone_r_err").html("Your Phone Number");
            alert("Enter To Date");
            $('#to_date_s').focus();
            return false;
        }
        if ($('#to_date_s').val().trim() != '' && $('#form_date_s').val().trim() === '') { //$("#phone_r_err").css('display','block');
            //$("#phone_r_err").html("Your Phone Number");
            alert("Enter From Date");
            $('#form_date_s').focus();
            return false;
        }

    }

    function table_settings() {
        var favorite = [];
        $.each($("input[name='settingsCheckbox[]']:checked"), function() {
            favorite.push($(this).val());
        });
        var check = favorite.length;
        if (check < 5) {
            alert("Please Check Atlast 5");
            return false;
        }

    }
    $(document).ready(function() {
        $('.select2')
            .select2()
            .on('select2:open', () => {
                $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal3">
    Add New
  </a></div>`);
            });
        //**************************************************************
        $('.select4')
            .select4()
            .on('select4:open', () => {
                $(".select4-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal4">
    Add New
  </a></div>`);
            });
    });
</script>
<script>
    function leaveInput(el) {
        if (el.value.length > 0) {
            if (!el.classList.contains('active')) {
                el.classList.add('active');
            }
        } else {
            if (el.classList.contains('active')) {
                el.classList.remove('active');
            }
        }
    }
    var inputs = document.getElementsByClassName("m-input");
    for (var i = 0; i < inputs.length; i++) {
        var el = inputs[i];
        el.addEventListener("blur", function() {
            leaveInput(this);
        });
    }
    // *** autocomplite select *** //
    wow = new WOW({
        boxClass: 'wow', // default
        animateClass: 'animated', // default
        offset: 0, // default
        mobile: true, // default
        live: true // default
    })
    wow.init();
</script>


<script>
    $(document).ready(function() {


        // DataTable
        var columnSl = 0;
        var table = $("#dataTable").DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
            buttons: ['copy', 'csv', 'excel', 'print'],
            "lengthMenu": [
                [1000, 5000, 10000, -1],
                [1000, 5000, 10000, 'All'],
            ],
            "scrollY": 200,
            "scrollX": true,
            "ordering": false,
            initComplete: function() {
                this.api()
                    .columns()
                    .every(function() {
                        columnSl++;
                        console.log(`columnSl=${columnSl}`);
                        if (columnSl == 8 || columnSl == 10) {
                            //For Dropdown column search
                            /*var column = this;
                            var select = $('<select class="form-control p-0"><option value="">All</option></select>')
                              .appendTo($(column.footer()).empty())
                              .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                console.log(val);
                                column.search(val ? '^' + val + '$' : '', true, false).draw();
                              });

                            column
                              .data()
                              .unique()
                              .sort()
                              .each(function(d, j) {
                                select.append('<option value="' + d + '">' + d + '</option>');
                              });*/
                        }
                        if (columnSl == 4 || columnSl == 5) {
                            var column = this;
                            var select = $('<input type="text" class="form-control" placeholder="dd-mm-yyyy">')
                                .appendTo($(column.footer()).empty());
                        }
                    });
            },
        });
        // Apply the search
        columnSl2 = 0;
        table.columns().every(function() {
            columnSl2++;
            if (columnSl2 == 4 || columnSl2 == 5) {
                var that = this;
                $('input', this.footer()).on('keyup change', function() {
                    let searchVal = `${(this.value).split("-")[2]}-${(this.value).split("-")[1]}-${(this.value).split("-")[0]}`;
                    that.search(searchVal).draw();
                });
            } else {
                var that = this;
                $('input', this.footer()).on('keyup change', function() {
                    that.search(this.value).draw();
                });
            }
        });

    });
</script>
<script>
    var elem = document.getElementById("listTabPan");

    function openFullscreen() {
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
            /* Safari */
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            /* IE11 */
            elem.msRequestFullscreen();
        }
    }
</script>

<script>
    $(function() {
        $('input[name="daterange"]').daterangepicker({
                opens: 'left'
            },
            function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
    });
</script>

<script>
    $(function() {
        $('input[name="daterange"]').daterangepicker({
            opens: 'left'
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
    });
    $('#fYDropdown').change(function() {
        var title = $(this).val();
        if (title == "customrange") {
            $('.modal-title').html(title);
            $('.custom-range-modal').modal('show');
        }
    });
</script>