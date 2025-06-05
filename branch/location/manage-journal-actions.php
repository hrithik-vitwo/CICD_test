<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");


$company_data = getCompanyDataDetails($company_id);
$gl_account_length = $company_data['data']['gl_account_length'];

// date checker
$check_var_sql = queryGet("SELECT * FROM `" . ERP_MONTH_VARIANT . "` WHERE `month_variant_id`=$admin_variant");
$check_var_data = $check_var_sql['data'];

$max = $check_var_data['month_end'];
$min = $check_var_data['month_start'];
$check_func = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id`=$location_id");
$funcs = $check_func['data']['companyFunctionalities'];
$func_ex = explode(",", $funcs);
$func_area_list = '';
foreach ($func_ex as $func) {
    $func_area = queryGet("SELECT * FROM `erp_company_functionalities` WHERE `functionalities_id`=$func", true);

    $func_area_list .= '<option value="' . $func_area['data'][0]['functionalities_id'] . '">' . $func_area['data'][0]['functionalities_name'] . '';
}
if (isset($_POST["createdata"])) {

    $addNewJournal = createDataJournal($_POST);
    // console($addNewObj);
    // exit();
    // alert($addNewJournal);
    // //swalAlert($addNewObj["status"],$addNewObj["message"]);

    swalAlert($addNewJournal["status"], ucfirst($addNewJournal["status"]), $addNewJournal["message"],  BASE_URL . "branch/location/manage-journal.php");
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
        top: 0px;
        left: 18px;
        margin: 15px 0;
        float: right;
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

<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="public/assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css"> -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper is-journal">
    <!-- Main content -->
    <section class="content">
        <?php if ($_GET['show'] == 'create') { ?>
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= LOCATION_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Journal List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Journal</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>

                <form action="" method="POST" id="addNewJournalForm">

                    <div class="card pgi-body-card">
                        <div class="card-header">
                            <div class="head p-2">
                                <h4>Create new journal</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="pgi-body">
                                <div class="row function_row_main">
                                    <?php
                                    $chartOfAcc = getAllChartOfAccountsByconditionForMapping($company_id, true);
                                    if ($chartOfAcc['status'] == 'success') {
                                        $list = '';
                                        foreach ($chartOfAcc['data'] as $chart) {
                                            $list .= '<option value="' . $chart['id'] . '">' . $chart['gl_label'] . '&nbsp;||&nbsp;' . $chart['gl_code'] . '</option>';
                                        }
                                    }
                                    $function_id = rand(0000, 9999);
                                    $cost_center_sql = queryGet("SELECT * FROM `erp_cost_center` WHERE `company_id` = $company_id", true);
                                    if ($cost_center_sql['status'] == 'success') {
                                        $cost_center_list = '';
                                        foreach ($cost_center_sql['data'] as $cost_center) {
                                            $cost_center_list .= '<option value="' . $cost_center['CostCenter_id'] . '">' . $cost_center['CostCenter_code'] . '&nbsp;||&nbsp;' . $cost_center['CostCenter_desc'] . '</option>';
                                        }
                                    }
                                    ?>
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="form-input function-mapp-main">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <label for="">Document Date </label>
                                                    <input type="date" id="documentDate" name="documentDate" class="form-control" value="" required>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <label for="">Document Number </label>
                                                    <input type="text" id="documentNo" name="documentNo" class="form-control" value="" required>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <label for="">Posting Date </label>
                                                    <!-- value="<?= date('Y-m-d'); ?>" -->
                                                    <input type="date" id="postingDate" name="postingDate" class="form-control" min="<?= $min ?>" max="<?= $max ?>" required>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <label for="">Reference Code </label>
                                                    <input type="text" id="refarenceCode" name="refarenceCode" class="form-control" value="" required>
                                                </div>
                                                <div class="col-lg-4 col-md-16 col-sm-12">
                                                    <label for="">For : </label>
                                                    <div class="sub-content">
                                                        <span>
                                                            <input type="radio" name="journalEntryReference" class="reff-type-radio select_payment_entry" value="Payment" data-target="div-option-pay">
                                                            <label> Payment</label>
                                                        </span>
                                                        <span>
                                                            <input type="radio" name="journalEntryReference" class="reff-type-radio select_payment_entry" value="Collection" data-target="div-option-collect">
                                                            <label> Collection</label>
                                                        </span>
                                                        <span>
                                                            <input type="radio" name="journalEntryReference" class="reff-type-radio select_payment_entry" value="Purchase" data-target="div-option-purchase">
                                                            <label> Purchase</label>
                                                        </span>
                                                        <span>
                                                            <input type="radio" name="journalEntryReference" class="reff-type-radio select_payment_entry" value="Production" data-target="div-option-production">
                                                            <label> Production</label>
                                                        </span>
                                                        <span>
                                                            <input type="radio" name="journalEntryReference" class="reff-type-radio select_payment_entry" value="Sales" data-target="div-option-sales">
                                                            <label>Sales</label>
                                                        </span>
                                                        <span>
                                                            <input type="radio" name="journalEntryReference" class="reff-type-radio select_payment_entry" value="Other" data-target="div-option-other" checked>
                                                            <label>Other</label>
                                                        </span>
                                                    </div>
                                                </div>



                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <label for="">Remark </label>
                                                    <textarea name="remark" id="remark" class="form-control" rows="4" required></textarea>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <hr>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-3"> <label>Particular</label></div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 cost_center"> <label>Cost Center</label></div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 func_area"> <label>Functional Area</label></div>
                                                <div class="col-lg-2 col-md-2 col-sm-2"> <label>Debit Amount</label></div>
                                                <div class="col-lg-2 col-md-2 col-sm-2"> <label>Credit Amount</label></div>

                                                <div class="col-lg-1 col-md-1 col-sm-1"> <label></label></div>
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <hr>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 d-inline-flex">
                                                    <label for="" style="color: red;">Debit </label> &nbsp;&nbsp;
                                                </div>
                                                <div class="col-12 debit-main">
                                                    <div class="row debitHtml">
                                                        <?php $Debit = rand(0000, 9999); ?>
                                                        <div class="col-lg-3 col-md-3 col-sm-3" style="display: inline-flex;">
                                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                                <select id="debit_<?= $Debit; ?>" name="journal[debit][<?= $Debit; ?>][gl]" class="form-control select2 selectDebitSub" required>
                                                                    <option value="">Select Debit G/L</option>
                                                                    <?php echo $list; ?>
                                                                </select>
                                                            </div>
                                                            <div class=" col-lg-6 col-md-6 col-sm-6 debit_<?= $Debit; ?>-psub" style="display: none;">
                                                                <select class="debit_<?= $Debit; ?>-sub form-control debitsl_<?= $Debit ?> select2" name="journal[debit][<?= $Debit; ?>][subgl]">
                                                                    <option value="">-- Select Sub G/L --</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 cost_center_debit_otr_<?= $Debit; ?>"> </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 cost_center_debit_<?= $Debit; ?>" style="display: none;">
                                                            <select id="cost_center_dr" name="journal[debit][<?= $Debit; ?>][cost_center]" class="form-control select2 selectCostCenetr">
                                                                <option value="">Select Cost Ceneter</option>
                                                                <?php
                                                                echo $cost_center_list;
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 cost_center_debit_otr_<?= $Debit; ?>"> </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 cost_center_debit_<?= $Debit; ?>" style="display: none;">
                                                            <select id="func_area_dr" name="journal[debit][<?= $Debit; ?>][functional_area]" class="form-control select2 selectCostCenetr">
                                                                <option value="">Select Functional</option>
                                                                <?php
                                                                echo $func_area_list;
                                                                ?>
                                                            </select>
                                                        </div>

                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                            <input step="0.01" type="number" id="dr_<?= rand(0000, 9999); ?>" name="journal[debit][<?= $Debit; ?>][amount]" class="form-control dr-amount inputAmountClass" value="" placeholder="Enter Amount" required>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2"></div>
                                                        <div class="col-lg-1 col-md-1 col-sm-1"><button value="<?= $function_id; ?>" type="button" class=" btn btn-primary add-debit">
                                                                <i class="fa fa-plus"></i>
                                                            </button></div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-12 col-md-12 col-sm-12 d-inline-flex">
                                                    <label for="" style="color: green;">Credit </label> &nbsp;&nbsp;
                                                </div>
                                                <div class="col-12 credit-main">
                                                    <div class="row">
                                                        <?php $Credit = rand(0000, 9999); ?>
                                                        <div class="col-lg-3 col-md-3 col-sm-3" style="display: inline-flex;">
                                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                                <select id="credit_<?= $Credit; ?>" name="journal[credit][<?= $Credit; ?>][gl]" class="form-control selectCreditSub select2" required>
                                                                    <option value="">Select Credit G/L</option>
                                                                    <?php echo $list; ?>
                                                                </select>
                                                            </div>
                                                            <div class=" col-lg-6 col-md-6 col-sm-6 credit_<?= $Credit; ?>-psub" style="display: none;">
                                                                <select class="credit_<?= $Credit; ?>-sub form-control select2 creditsl_<?= $Credit ?> " name="journal[credit][<?= $Credit; ?>][subgl]">
                                                                    <option value="">-- Select Sub G/L --</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 cost_center_credit_otr_<?= $Credit; ?>"> </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 cost_center_credit_<?= $Credit; ?>" style="display: none;">
                                                            <select id="cost_center" name="journal[credit][<?= $Credit; ?>][cost_center]" class="form-control select2 selectCostCenetr">
                                                                <option value="">Select Cost Ceneter</option>
                                                                <?php
                                                                echo $cost_center_list;
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 cost_center_credit_otr_<?= $Credit; ?>"> </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 cost_center_credit_<?= $Credit; ?>" style="display: none;">
                                                            <select id="func_area_cr" name="journal[credit][<?= $Credit; ?>][functional_area]" class="form-control select2 selectCostCenetr">
                                                                <option value="">Select Functional </option>
                                                                <?php
                                                                echo $func_area_list;
                                                                ?>
                                                            </select>
                                                        </div>

                                                        <div class="col-lg-2 col-md-2 col-sm-2"></div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                            <input step="0.01" type="number" id="cr_<?= rand(0000, 9999); ?>" name="journal[credit][<?= $Credit; ?>][amount]" class="form-control cr-amount inputAmountClass" value="" placeholder="Enter Amount" required>
                                                        </div>
                                                        <div class="col-lg-1 col-md-1 col-sm-1"><button type="button" value="<?= $function_id; ?>" class="btn btn-primary add-credit">
                                                                <i class="fa fa-plus"></i>
                                                            </button></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <hr>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-3"> <label>Total Amount</label></div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 cost_total"> <label></label></div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 funtional_total"> <label></label></div>
                                                <div class="col-lg-2 col-md-2 col-sm-2"> <label class="debit-total"><?= inputValue(0) ?></label></div>
                                                <div class="col-lg-2 col-md-2 col-sm-2"> <label class="credit-total"><?= inputValue(0) ?></label></div>
                                                <div class="col-lg-1 col-md-1 col-sm-1"> <label></label></div>
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <button type="submit" name="createdata" id="createdata" class="btn btn-primary save-close-btn float-right waves-effect waves-light createdata">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        <?php  } else { ?>
            <script>
                let url = `<?= BRANCH_URL ?>location/manage-journal.php`;
                window.location.href = url;
            </script>
        <?php } ?>
    </section>
    <!-- /.content -->
</div>

<?php
require_once("../common/footer.php");
?>


<!-- CHANGES -->
<script>
    // $('.createdata').change(function() {
    //     alert('ok');


    // });


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

        $("#dataTable tfoot th").each(function() {
            var title = $(this).text();
            $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
        });

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




    $('.reverseJournal').click(function(e) {
        e.preventDefault(); // Prevent default click behavior

        var dep_keys = $(this).data('id');
        var $this = $(this); // Store the reference to $(this) for later use

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: 'You want to reverse this?',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Reverse'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    data: {
                        dep_keys: dep_keys,
                        dep_slug: 'reverseJOURNAL'
                    },
                    url: 'ajaxs/ajax-reverse-post.php',
                    beforeSend: function() {
                        $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                    },
                    success: function(response) {
                        var responseObj = JSON.parse(response);
                        console.log(responseObj);

                        if (responseObj.status == 'success') {
                            // $this.parent().parent().find('.reverseStatus').html('reverse');
                            $this.html('Reversed');
                        } else {
                            $this.html('<i class="far fa-undo po-list-icon"></i>');
                        }

                        let Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000
                        });
                        Toast.fire({
                            icon: responseObj.status,
                            title: '&nbsp;' + responseObj.message
                        }).then(function() {
                            // location.reload();
                        });
                    }
                });
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('.select2')
            .select2()
            .on('select2:open', () => {
                $(".select2-results:not(:has(a))");
            });
        //**************************************************************
        $('.select4')
            .select4()
            .on('select4:open', () => {
                $(".select4-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal4">
Add New
</a></div>`);
            });
        $('#cost_center_cr').select2({
            allowClear: true
        });
        $('#cost_center_dr').select2({
            allowClear: true
        });
        $('#func_area_dr').select2({
            allowClear: true
        });
        $('#func_area_cr').select2({
            allowClear: true
        });
    });



    $('#addNewJournalForm').on('submit', function() {
        let dtotal = 0;
        $(".dr-amount").each(function() {
            let velu = parseFloat($(this).val());
            if (velu > 0) {
                dtotal += parseFloat(velu);
            }
        });
        let ctotal = 0;
        $(".cr-amount").each(function() {
            let velu = parseFloat($(this).val());
            if (velu > 0) {
                ctotal += parseFloat(velu);
            }
        });

        ctotal = (ctotal).toFixed(2);
        dtotal = helperAmount(dtotal);
        console.log(dtotal, '---------------', ctotal);
        if (dtotal != ctotal) {
            if (dtotal != ctotal) {
                let Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                Toast.fire({
                    icon: `warning`,
                    title: `&nbsp;Debit and credit mismatch!`
                });
                return false;
            }
            return false;
        }

        for (elem of $(".selectDebitSub").get()) {
            let element = elem.getAttribute("id").split("_")[1];
            // alert(element);
            let sub_ledger = $(`.debitsl_${element}`).val();
            //  alert(sub_ledger);
            //     alert(ok);
            if (sub_ledger != null && sub_ledger == 0) {

                let Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                Toast.fire({
                    icon: `warning`,
                    title: `&nbsp;Select Sub Ledger!`
                });
                return false;


            }

        }

        for (elem of $(".selectCreditSub").get()) {
            let element = elem.getAttribute("id").split("_")[1];
            //   alert(element);
            let sub_ledger = $(`.creditsl_${element}`).val();
            // alert(sub_ledger);
            //     alert(ok);
            if (sub_ledger != null && sub_ledger == 0) {

                let Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                Toast.fire({
                    icon: `warning`,
                    title: `&nbsp;Select Sub Ledger!`
                });
                return false;


            }

        }

        return true;

    });

    // Subgl Finding Methods
    $(document).on("change", '.selectDebitSub', function() {
        let valllAc = $(this).val();
        var elementId = $(this).attr('id');
        // alert(elementId);
        $(`.${elementId}-psub`).hide();
        $(`.${elementId}-sub`).html("");
        // $(`.cost_center`).hide();
        // $(`.cost_center_credit`).hide();
        // $(`.cost_center_debit`).hide();
        // $(`.cost_center_credit_otr`).hide();
        // $(`.cost_center_debit_otr`).hide();
        let Id = elementId.split("_")[1]
        let selectedText = $(this).find(":selected").text();
        let secondValue = selectedText.split("||")[1]?.trim();
        let secondValueAsInt = parseInt(secondValue, 10);
        let selectedTextcr = $('.selectCreditSub').find(":selected").text();
        let secondValuecr = selectedTextcr.split("||")[1]?.trim();
        let secondValueAsIntcr = parseInt(secondValuecr, 10);
        if (secondValueAsInt >= 30000) {
            if (secondValueAsIntcr >= 30000) {
                $(`.cost_center_debit_otr_${Id}`).hide();
                $(`.cost_center_debit_${Id}`).show();
            } else {
                // alert(elementId);
                $(`.cost_center_debit_otr_${Id}`).hide();
                $(`.cost_center_debit_${Id}`).show();
            }

        } else {
            if (secondValueAsIntcr && secondValueAsIntcr >= 30000) {
                $(`.cost_center_debit_otr_${Id}`).show();
                $(`.cost_center_debit_${Id}`).hide();
            } else {
                $(`.cost_center_debit_otr_${Id}`).show();
                $(`.cost_center_debit_${Id}`).hide();
            }
        }
        //  alert(secondValueAsIntcr);
        // let mappArray = '<?= getAllfetchAccountingMappingArray($company_id) ?>';
        // console.log(mappArray);
        // let mappfinalArray = jQuery.parseJSON(mappArray);
        // console.log(mappfinalArray['data']);
        // if (mappfinalArray['status'] == 'success') {
        //   if ($.inArray(valllAc, Object.values(mappfinalArray['data'])) !== -1) {
        //     console.log(valllAc + "exists in the array.");
        $.ajax({
            type: "GET",
            url: `<?= LOCATION_URL ?>ajaxs/ajax-subgl-list.php?gl=${valllAc}&type=debit`,
            beforeSend: function() {
                // $(`.${elementId}-sub`).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
            },
            success: function(response) {
                // console.log(response);
                if (response !== '') {
                    $(`.${elementId}-psub`).show();
                    $(`.${elementId}-sub`).html(response);
                } else {
                    $(`.${elementId}-psub`).hide();
                    $(`.${elementId}-sub`).html("");
                }
            }
        });

        //   } else {
        //     //console.log(valllAc + " does not exist in the array.");
        //     $(`.${elementId}-psub`).hide();
        //     $(`.${elementId}-sub`).html("");
        //   }
        // } else {
        //   // console.log(valllAc + " does not exist in the array.");
        //   $(`.${elementId}-psub`).hide();
        //   $(`.${elementId}-sub`).html("");
        // }

    });

    $(document).on("change", '.selectCreditSub', function() {
        let valllAc = $(this).val();
        var elementId = $(this).attr('id');
        // alert(elementId);
        $(`.${elementId}-psub`).hide();
        $(`.${elementId}-sub`).html("");
        // $(`.cost_center`).hide();
        // $(`.cost_center_credit`).hide();
        // $(`.cost_center_debit`).hide();
        // $(`.cost_center_credit_otr`).hide();
        // $(`.cost_center_debit_otr`).hide();
        let Id = elementId.split("_")[1]
        let selectedText = $(this).find(":selected").text();
        let secondValue = selectedText.split("||")[1]?.trim();
        let secondValueAsInt = parseInt(secondValue, 10);
        let selectedTextdr = $('.selectDebitSub').find(":selected").text();
        let secondValuedr = selectedTextdr.split("||")[1]?.trim();
        let secondValueAsIntdr = parseInt(secondValuedr, 10);
        if (secondValueAsInt >= 30000) {
            if (secondValueAsIntdr >= 30000) {
                $(`.cost_center_credit_otr_${Id}`).hide();
                $(`.cost_center_credit_${Id}`).show();
            } else {
                $(`.cost_center_credit_otr_${Id}`).hide();
                $(`.cost_center_credit_${Id}`).show();
            }

        } else {
            if (secondValueAsIntdr && secondValueAsIntdr >= 30000) {
                $(`.cost_center_credit_otr_${Id}`).show();
                $(`.cost_center_credit_${Id}`).hide();
            } else {
                $(`.cost_center_credit_otr_${Id}`).show();
                $(`.cost_center_credit_${Id}`).hide();
            }
        }
        //  alert(valllAc);
        // let mappArray = '<?= getAllfetchAccountingMappingArray($company_id) ?>';
        // let mappfinalArray = jQuery.parseJSON(mappArray);
        // //  console.log(mappfinalArray['data']);
        // if (mappfinalArray['status'] == 'success') {
        //   if ($.inArray(valllAc, Object.values(mappfinalArray['data'])) !== -1) {
        //     //console.log(valllAc + "exists in the array.");
        $.ajax({
            type: "GET",
            url: `<?= LOCATION_URL ?>ajaxs/ajax-subgl-list.php?gl=${valllAc}&type=credit`,
            beforeSend: function() {
                // $(`.${elementId}-sub`).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
            },
            success: function(response) {
                // console.log(response);
                if (response !== '') {
                    $(`.${elementId}-psub`).show();
                    $(`.${elementId}-sub`).html(response);
                } else {
                    $(`.${elementId}-psub`).hide();
                    $(`.${elementId}-sub`).html("");
                }
            }
        });

        //   } else {
        //     //console.log(valllAc + " does not exist in the array.");
        //     $(`.${elementId}-psub`).hide();
        //     $(`.${elementId}-sub`).html("");
        //   }
        // } else {
        //   // console.log(valllAc + " does not exist in the array.");
        //   $(`.${elementId}-psub`).hide();
        //   $(`.${elementId}-sub`).html("");
        // }

    });

    //////////////********************//////////////////


    $(document).on("keyup keydown paste", '.cr-amount', function() {
        let valllAc = $(this).val();
        calculateCrAmount();
    });

    function calculateCrAmount() {
        let sum = 0;
        $(".cr-amount").each(function() {
            let velu = parseFloat($(this).val());
            if (velu > 0) {
                sum += parseFloat(velu);
            }
        });
        sum = inputValue(sum);
        $('.credit-total').html(sum);
    }

    $(document).on("keyup keydown paste", '.dr-amount', function() {
        let valllAc = $(this).val();
        calculateDrAmount();
    });

    function calculateDrAmount() {
        let sum = 0;
        $(".dr-amount").each(function() {
            let velu = parseFloat($(this).val());
            if (velu > 0) {
                sum += parseFloat(velu);
            }
        });
        sum = inputValue(sum);
        $('.debit-total').html(sum);
    }

    $(document).on("click", ".add-debit", function() {
        let function_id = $(this).val();
        let rand_no = Math.ceil(Math.random() * 100000);
        var bullet_point_html = `<div class="row d_${rand_no}"><div class="col-lg-3 col-md-3 col-sm-3" style="display: inline-flex;">
                          <div class="col-lg-6 col-md-6 col-sm-6">
                          <select id="debit_${rand_no}" name="journal[debit][${rand_no}][gl]" class="form-control selectDebitSub select2" required>
                          <option value="">Select Debit G/L</option>
                           <?= $list; ?>
                          </select>
                          </div>
                          <div class=" col-lg-6 col-md-6 col-sm-6 debit_${rand_no}-psub" style="display: none;" >
                            <select class="debit_${rand_no}-sub form-control debitsl_${rand_no}  select2" name="journal[debit][${rand_no}][subgl]">
                              <option value="">-- Select Sub G/L --</option>
                            </select>
                          </div>
                          
                          </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 cost_center_debit_otr_${rand_no}"> </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 cost_center_debit_${rand_no}" style="display: none;">
                                                  
                                                    <select id="cost_center" name="journal[debit][${rand_no}][cost_center]" class="form-control select2 selectCostCenetr">
                                                                <option value="">Select Cost Cenetr</option>
                                                                <?php
                                                                echo $cost_center_list;
                                                                ?>
                                                            </select>
                                                </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 cost_center_debit_otr_${rand_no}"> </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 cost_center_debit_${rand_no}" style="display: none;">
                                                            <select id="cost_center" name="journal[credit][[${rand_no}][cost_center]" class="form-control select2 selectCostCenetr">
                                                                <option value="">Select Functional </option>
                                                                <?php
                                                                echo $func_area_list;
                                                                ?>
                                                            </select>
                                                        </div>

                          <div class="col-lg-2 col-md-2 col-sm-2">
                          <input step="0.01" type="number" id="dr_${rand_no}" name="journal[debit][${rand_no}][amount]" class="form-control dr-amount inputAmountClass" value="" placeholder="Enter Amount" required>                                    
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2"></div>
                          <div class="col-lg-1 col-md-1 col-sm-1">
                          <button type="button" class="btn btn-danger delete_new_bullet_point">
                            <i class="fa fa-minus"></i>
                          </button>
                        </div></div>`;

        $('.debit-main').append(bullet_point_html);
        $(`#debit_${rand_no}`).select2();
        $(`.debit_${rand_no}-sub`).select2();
    });

    $(document).on("click", ".add-credit", function() {
        let function_id = $(this).val();
        let rand_no = Math.ceil(Math.random() * 100000);
        var bullet_point_html = `<div class="row"><div class="col-lg-3 col-md-3 col-sm-3" style="display: inline-flex;">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                          <select id="credit_${rand_no}" name="journal[credit][${rand_no}][gl]" class="form-control selectCreditSub select2"  required>
                          <option value="">Select Credit G/L</option>
                           <?= $list; ?>
                          </select>
                          </div>
                            <div class=" col-lg-6 col-md-6 col-sm-6 credit_${rand_no}-psub" style="display: none;" >
                              <select class="credit_${rand_no}-sub form-control creditsl_${rand_no} select2" name="journal[credit][${rand_no}][subgl]">
                                <option value="">-- Select Sub G/L --</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 cost_center_credit_otr_${rand_no}"> </div>
                          <div class="col-lg-2 col-md-2 col-sm-2 cost_center_credit_${rand_no}" style="display: none;">
                                                  
                                                    <select id="cost_center" name="journal[credit][${rand_no}][cost_center]" class="form-control select2 selectCostCenetr">
                                                                <option value="">Select Cost Cenetr</option>
                                                                <?php
                                                                echo $cost_center_list;
                                                                ?>
                                                            </select>
                                                </div>
                             <div class="col-lg-2 col-md-2 col-sm-2 cost_center_credit_otr_${rand_no}"> </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 cost_center_credit_${rand_no}" style="display: none;">
                                                            <select id="cost_center" name="journal[credit][${rand_no}][cost_center]" class="form-control select2 selectCostCenetr">
                                                                <option value="">Select Functional </option>
                                                                <?php
                                                                echo $func_area_list;
                                                                ?>
                                                            </select>
                                                        </div>
                          <div class="col-lg-2 col-md-2 col-sm-2"></div>
                          <div class="col-lg-2 col-md-2 col-sm-2">
                          <input step="0.01" type="number" id="cr_${rand_no}" name="journal[credit][${rand_no}][amount]" class="form-control cr-amount inputAmountClass" value="" placeholder="Enter Amount" required>    
                          </div>
                          <div class="col-lg-1 col-md-1 col-sm-1">
                          <button type="button" class="btn btn-danger delete_new_bullet_point">
                            <i class="fa fa-minus"></i>
                          </button>
                        </div></div>`;
        $('.credit-main').append(bullet_point_html);

        $(`#credit_${rand_no}`).select2();
        $(`.credit_${rand_no}-sub`).select2();
    });

    $(document).on("click", ".delete_new_bullet_point", function() {
        $(this).parent().parent().remove();
        calculateDrAmount();
        calculateCrAmount();
    });

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