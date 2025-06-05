<?php
require_once("../app/v1/connection-company-admin.php");
// administratorLocationAuth();
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
require_once("common/pagination.php");
require_once("../app/v1/functions/branch/func-customers.php");
require_once("../app/v1/functions/branch/func-journal.php");
require_once("../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../app/v1/functions/admin/func-company.php");

//  $erp_acc_coa = ERP_ACC_CHART_OF_ACCOUNTS;


// console($resultObj);

$coatype = 'p_id';
if (isset($_GET['coatype']) && !empty($_GET['coatype'])) {
    $coatype = $_GET['coatype'];
}


function getglTree($p_id = null, $coatype)
{
    global $company_id;
    if (empty($p_id)) {
        $sql = "SELECT customer.customer_code AS sugl_code,customer.trade_name AS subgl_name,coa.gl_code AS gl_code,coa.gl_label AS gl_name,coa.$coatype,coa.typeAcc  FROM erp_customer AS customer INNER JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON customer.parentGlId=coa.id WHERE customer.company_id=$company_id 
        UNION
        SELECT vendor.vendor_code AS subgl_code,vendor.trade_name AS subgl_name,coa.gl_code AS gl_code,coa.gl_label AS gl_name,coa.$coatype,coa.typeAcc  FROM erp_vendor_details AS vendor INNER JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON vendor.parentGlId=coa.id WHERE vendor.company_id=$company_id  
        UNION
        SELECT bank_cash.acc_code AS subgl_code,bank_cash.bank_name AS subgl_name,coa.gl_code AS gl_code,coa.gl_label AS gl_name,coa.$coatype,coa.typeAcc  FROM erp_acc_bank_cash_accounts AS bank_cash INNER JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON bank_cash.parent_gl=coa.id WHERE bank_cash.company_id=$company_id  
        UNION
        SELECT items.itemCode AS subgl_code,items.itemName AS subgl_name,coa.gl_code AS gl_code,coa.gl_label AS gl_name,coa.$coatype,coa.typeAcc  FROM erp_inventory_items AS items INNER JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON items.parentGlId=coa.id WHERE items.company_id=$company_id  
        ";
    } else {
        $sql = "SELECT id,gl_code,gl_label,typeAcc,$coatype FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE `id`=$p_id";
    }
    $queryObj = queryGet($sql, true);

    $tree = [];
    foreach ($queryObj["data"] as $row) {
        if ($row[$coatype] != 0) {
            $children = getglTree($row[$coatype], $coatype);
        }
        if ($p_id != null) {
            $tree[] = array(
                'gl_label' => $row['gl_label'],
                'gl_code' => $row['gl_code'],
                'glStType' => $row['glStType'],
                'typeAcc' => $row['typeAcc'],
                $coatype => $row[$coatype],
                'data' => $children
            );
        } else {
            // console($row);
            $tree[] = array(
                'subgl_label' => $row['subgl_name'],
                'subgl_code' => $row['sugl_code'],
                'gl_label' => $row['gl_name'],
                'gl_code' => $row['gl_code'],
                'glStType' => '',
                'typeAcc' => $row['typeAcc'],
                $coatype => $row[$coatype],
                'data' => $children
            );
        }
    }
    // $tree["groupTotal"] = $groupTotal;
    return $tree;
}
$glAccList = getglTree($p_id = null, $coatype);
//console($glAccList);
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
        top: -35px;
        left: -75px;
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

    .content-wrapper table.gl-view-table tr td {
        font-size: 10px;
        white-space: pre-line;
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


    select.form-control.view-dropdown {
        position: relative;
        top: 0;
        width: 145px;
        height: 30px;
        padding: 0 15px;
        font-size: 12px;
        right: 14px;
        background: #cfd8e1;
        border: 1px solid #00306030;
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

            </div>

            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div class="p-0 pt-1 my-2">
                        <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                            <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                <h3 class="card-title">
                                    GL View
                                </h3>
                                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
                            </li>
                        </ul>
                    </div>

                    <button class="btn btn-primary float-right my-3" onclick="exportTableToExcel()">Export to Excel</button>

                    <div class="card card-tabs">
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

                                <div class="daybook-filter-list filter-list">
                                    <?php $colmnsql = "SELECT COLUMN_NAME, COLUMN_COMMENT
                                                        FROM INFORMATION_SCHEMA.COLUMNS
                                                        WHERE TABLE_NAME = '" . ERP_ACC_CHART_OF_ACCOUNTS . "'
                                                        AND ORDINAL_POSITION > (
                                                            SELECT ORDINAL_POSITION 
                                                            FROM INFORMATION_SCHEMA.COLUMNS
                                                            WHERE TABLE_NAME = '" . ERP_ACC_CHART_OF_ACCOUNTS . "'
                                                            AND COLUMN_NAME = 'typeAcc'
                                                        )
                                                        AND ORDINAL_POSITION < (
                                                            SELECT ORDINAL_POSITION 
                                                            FROM INFORMATION_SCHEMA.COLUMNS
                                                            WHERE TABLE_NAME = '" . ERP_ACC_CHART_OF_ACCOUNTS . "'
                                                            AND COLUMN_NAME = 'lvl'
                                                        );
                                                        ";
                                    $colmnResponce = queryGet($colmnsql, true)['data'];
                                    // console($colmnResponce);
                                    foreach ($colmnResponce as $colmn) {
                                        $active = ' mr-2';
                                        if ($coatype == $colmn['COLUMN_NAME']) {
                                            $active = 'active';
                                        }
                                    ?>

                                        <a href="manage-chart-of-account.php?coatype=<?= $colmn['COLUMN_NAME']; ?>" class="btn <?= $active; ?> waves-effect waves-light">
                                            <i class="fa fa-list <?= $active; ?> mr-2"></i><?= $colmn['COLUMN_COMMENT']; ?>
                                        </a>
                                        <?php if ($coatype == $colmn['COLUMN_NAME']) { ?>
                                            <select class="form-control view-dropdown" name="coaDropdown" id="coaDropdown">
                                                <option value="manage-chart-of-account.php?coatype=<?= $colmn['COLUMN_NAME']; ?>">Tree View</option>
                                                <option value="sub-gl-view.php?coatype=<?= $colmn['COLUMN_NAME']; ?>" selected>Sub GL View</option>
                                                <option value="gl-view.php?coatype=<?= $colmn['COLUMN_NAME']; ?>">GL View</option>
                                            </select>
                                    <?php }
                                    } ?>
                                </div>


                                <table id="dataTable" class="table table-hover text-nowrap">
                                    <thead>
                                        <tr class="alert-light">
                                            <th class="border vertical-align">SubGL Code</th>
                                            <th class="border vertical-align">SubGL Name</th>
                                            <th class="border vertical-align">GL Code</th>
                                            <th class="border vertical-align">GL Name</th>
                                            <th class="border vertical-align">Type</th>
                                            <th class="border vertical-align">Group 1</th>
                                            <th class="border vertical-align">Group 2</th>
                                            <th class="border vertical-align">Group 3</th>
                                            <th class="text-center border">Group 4</th>
                                            <th class="border vertical-align">Group 5</th>
                                            <th class="border vertical-align">Group 6</th>
                                            <th class="text-center border">Group 7</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($glAccList as $data) {

                                        ?>
                                            <tr>
                                                <td><p><?= $data['subgl_code'] ?></p></td>
                                                <td><p class="pre-normal"><?= $data['subgl_label'] ?></p></td>
                                                <td><?= $data['gl_code'] ?></td>
                                                <td><p class="pre-nomal"><?= $data['gl_label'] ?></p></td>
                                                <td><p><?php if ($data['typeAcc'] == 1 || $data['typeAcc'] == 2) {
                                                        echo "Balance Sheet";
                                                    } else {
                                                        echo "Profit and Loss";
                                                    }  ?></p></td>
                                                <td><p class="pre-normal"><?php if (isset($data['data'][0])) {
                                                        echo $data['data'][0]['gl_label'];
                                                    } else {
                                                        echo "-";
                                                    } ?></p></td>
                                                <td><?php if (isset($data['data'][0]['data'][0])) {
                                                        echo $data['data'][0]['data'][0]['gl_label'];
                                                    } else {
                                                        echo "-";
                                                    } ?></td>
                                                <td><?php if (isset($data['data'][0]['data'][0]['data'][0])) {
                                                        echo $data['data'][0]['data'][0]['data'][0]['gl_label'];
                                                    } else {
                                                        echo "-";
                                                    } ?></td>
                                                <td><?php if (isset($data['data'][0]['data'][0]['data'][0]['data'][0])) {
                                                        echo $data['data'][0]['data'][0]['data'][0]['data'][0]['gl_label'];
                                                    } else {
                                                        echo "-";
                                                    } ?></td>

                                                <td><?php if (isset($data['data'][0]['data'][0]['data'][0]['data'][0])) {
                                                        echo $data['data'][0]['data'][0]['data'][0]['data'][0]['gl_label'];
                                                    } else {
                                                        echo "-";
                                                    } ?></td>
                                                <td><?php if (isset($data['data'][0]['data'][0]['data'][0]['data'][0]['data'][0])) {
                                                        echo $data['data'][0]['data'][0]['data'][0]['data'][0]['data'][0]['gl_label'];
                                                    } else {
                                                        echo "-";
                                                    } ?></td>
                                                <td><?php if (isset($data['data'][0]['data'][0]['data'][0]['data'][0]['data'][0]['data'][0])) {
                                                        echo $data['data'][0]['data'][0]['data'][0]['data'][0]['data'][0]['data'][0]['gl_label'];
                                                    } else {
                                                        echo "-";
                                                    } ?></td>
                                                <td><?php if (isset($data['data'][0]['data'][0]['data'][0]['data'][0]['data'][0]['data'][0]['data'][0])) {
                                                        echo $data['data'][0]['data'][0]['data'][0]['data'][0]['data'][0]['data'][0]['data'][0]['gl_label'];
                                                    } else {
                                                        echo "-";
                                                    } ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>

                                        <!-- <tr>
                                            <td></td>
                                            <td class="font-weight-bold">Total</td>
                                            <td class="text-right font-weight-bold"></td>
                                            <td class="text-right font-weight-bold"><?= number_format($grandTotalDebit, 2) ?></td>
                                            <td class="text-right font-weight-bold"><?= number_format($grandTotalCredit, 2) ?></td>
                                            <td class="text-right font-weight-bold"></td>
                                        </tr> -->
                                    </tbody>
                                </table>

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




<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.2/xlsx.full.min.js"></script>

<script>
    function exportTableToExcel() {
        const table = document.getElementById('dataTable');
        const ws = XLSX.utils.table_to_sheet(table);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Table Data');
        XLSX.writeFile(wb, 'subGL-data.xlsx');
    }
</script>

<script>
    $('#coaDropdown').on('change', function() {
        // Get the selected option value
        const selectedOption = $(this).val();

        // Redirect to the selected URL
        if (selectedOption) {
            window.location.href = selectedOption;
        }
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