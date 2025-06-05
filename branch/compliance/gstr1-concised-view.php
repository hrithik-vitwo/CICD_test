<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once(BASE_DIR . "app/v1/functions/branch/func-brunch-gstr1-controller.php");

$gstr1ControllerObj = new gstr1Compilance();

if (isset($_POST['addNewgstr1CompilanceSubmitBtn'])) {
    $gstr1obj = $gstr1ControllerObj->insertNewGstr1($_POST);
    // console($gstr1obj);
    swalAlert($gstr1obj["status"], ucfirst($gstr1obj["status"]), $gstr1obj["message"]);
}

$gstr1ControllerObj->setUpcomingGstr1FillingDate();


?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<style>
    section.gstr-1 {
        padding: 0px 20px;
    }

    .head-btn-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .gst-one-filter {
        left: 0;
        top: 0;
    }

    .gst-one-filter a.active {
        background-color: #003060;
        color: #fff;
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

    .dataTables_scrollBody::-webkit-scrollbar {
        background-color: transparent;
        width: 0px;
        height: 0px;
        cursor: pointer;
    }

    .dataTables_scrollBody:hover::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .dataTables_scrollBody:hover::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
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

    .gst-consised-view tr td {
        /* white-space: pre-line !important; */
        text-align: center !important;
    }

    .gst-consised-view tr th {
        text-align: center !important;
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

    .dataTables_scrollBody {
        max-height: 100% !important;
        height: auto !important;
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

    .daybook-tabs {
        flex-direction: row-reverse;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
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
    }

    .dataTables_scrollHeadInner {
        width: 100% !important;
    }

    table.defaultDataTable {
        width: 100% !important;
    }
</style>


<div class="content-wrapper">
    <section class="gstr-1">
        <h4 class="text-lg font-bold mt-4 mb-4">GSTR-1</h4>

        <div class="head-btn-section mb-3">
            <div class="filter-list gst-one-filter">
                <a href="gstr1-graphical-view.php" class="btn"><i class="fas fa-chart-bar mr-2"></i>Graphical View</a>
                <a href="" class="btn active"><i class="fa fa-list mr-2"></i>Concised View</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                <table id="datatable" width="100" class="table table-hover defaultDataTable gst-consised-view">
                    <thead>
                        <tr>
                            <th>Sl.</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th>ARN</th>
                            <th>Return Date</th>
                            <th>Taxable Amount</th>
                            <th>CGST</th>
                            <th>SGST</th>
                            <th>IGST</th>
                            <th>CESS</th>
                            <th>Updated at</th>
                            <th>Updated by</th>
                            <th>Approved</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- erp_compliance_gstr1 table data -->
                        <?php
                        $gsr1CompTableDatasql = "SELECT * FROM `erp_compliance_gstr1` WHERE `company_id`=" . $company_id . " AND `branch_id`=" . $branch_id . " ORDER BY id DESC";
                        $gsr1CompTableDatasqlObj = queryGet($gsr1CompTableDatasql, true);
                        // console($gsr1CompTableDatasqlObj);

                        $gst1FileFreqDateObj = queryGet("SELECT branch_gstin_file_r1_day,branch_gstin_file_frequency FROM `erp_branches` WHERE company_id= $company_id AND branch_id= $branch_id");
                        $branch_gstin_file_frequency = $gst1FileFreqDateObj['data']['branch_gstin_file_frequency'];

                        $statusArr = [
                            0 => [
                                'name' => 'Pending',
                                'style' => 'status'
                            ],
                            1 => [
                                'name' => 'Requested for approval',
                                'style' => 'status bg-warning'
                            ],
                            2 => [
                                'name' => 'Approved request',
                                'style' => 'status bg-warning'
                            ],
                            6 => [
                                'name' => 'Data saved',
                                'style' => 'status bg-warning'
                            ],
                            5 => [
                                'name' => 'Data reset',
                                'style' => 'status bg-warning'
                            ],
                            7 => [
                                'name' => 'Filed',
                                'style' => 'status bg-success'
                            ],
                            8 => [
                                'name' => 'Marked as filed',
                                'style' => 'status bg-secondary'
                            ],
                        ];

                        foreach ($gsr1CompTableDatasqlObj['data'] as $key => $gsr1Onedata) {
                            $statusObj = $statusArr[$gsr1Onedata['gstr1_return_file_status']];
                            $updated_by = $gsr1Onedata['updated_by'] == "Auto" ? 'Auto' : getCreatedByUser($gsr1Onedata['updated_by']);
                            $approved_by = $gsr1Onedata['approved_by'] == null ? '-' : getCreatedByUser($gsr1Onedata['approved_by']);
                            $isFiled = ($gsr1Onedata['gstr1_return_file_status'] >= 7) ? 1 : 0;
                            $getLastGstr1FileDay = $gsr1Onedata['gstr1_return_period'];
                            $getLastGstr1FileDate = date('d-m-Y', strtotime('01-' . substr($getLastGstr1FileDay, 0, 2) . '-' . substr($getLastGstr1FileDay, 2)));
                            $action = [
                                "period" => $getLastGstr1FileDay,
                                "startDate" => date('01-m-Y', strtotime($getLastGstr1FileDate)),
                                "endDate" => date('t-m-Y', strtotime($getLastGstr1FileDate)),
                                "frequency" => $branch_gstin_file_frequency,
                                "isFiled" => $isFiled
                            ];
                        ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= getMonthFromDigits($gsr1Onedata['gstr1_return_period']) ?></td>

                                <td>
                                    <span class="<?= $statusObj['style'] ?>"><?= $statusObj['name'] ?></span>
                                </td>
                                <td><?= $gsr1Onedata['gstr1_return_file_arn'] ?></td>
                                <td><?= $gsr1Onedata['gstr1_return_date'] ?></td>
                                <td><?= $gsr1Onedata['gstr1_return_total_taxable'] ?></td>
                                <td><?= $gsr1Onedata['gstr1_return_total_cgst'] ?></td>
                                <td><?= $gsr1Onedata['gstr1_return_total_sgst'] ?></td>
                                <td><?= $gsr1Onedata['gstr1_return_total_igst'] ?></td>
                                <td><?= $gsr1Onedata['gstr1_return_total_cess'] ?></td>
                                <td><?= formatDateORDateTime($gsr1Onedata['created_at']) ?></td>
                                <td><?= $updated_by ?></td>
                                <td><?= $approved_by ?></td>
                                <td>
                                    <a style="cursor:pointer" href="gstr1-preview.php?action=<?= (base64_encode(json_encode($action))) ?>" class="btn btn-sm status bg-primary">Preview</a>
                                    <?php
                                    if (in_array($gsr1Onedata['gstr1_return_file_status'], [0, 1, 2, 3, 4, 5, 6])) { ?>
                                        <a style="cursor:pointer" data-toggle="modal" data-target="#gstr1RowModal_<?= $gsr1Onedata['id'] ?>" class="btn btn-sm bg-success">Mark as File</a>
                                    <?php } ?>
                                </td>
                            </tr>

                            <div class="modal fade right audit-history-modal show" id="gstr1RowModal_<?= $gsr1Onedata['id'] ?>" aria-labelledby="innerModalLabel" aria-modal="true" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content auditTrailBodyContentLineDiv">
                                        <div class="modal-body p-0">
                                            <div class="free-space-bg">
                                                <ul class="nav nav-tabs pb-0" id="myTab" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="concise-tab" data-toggle="tab" href="#consize" role="tab" aria-controls="concise" aria-selected="true"><i class="fa fa-th-large mr-2" aria-hidden="true"></i>GSTR1 FORM</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <!-- form tab -->
                                            <div class="tab-content pt-0" id="myTabContent">
                                                <div class="tab-pane fade active show" id="consize" role="tabpanel" aria-labelledby="consize-tab">
                                                    <form method="POST" id="addNewgstr1Compilance" enctype="multipart/form-data">
                                                        <div class="formInput">
                                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                                <input type="hidden" class="form-control" name="gstr1Id" value="<?= $gsr1Onedata['id'] ?>">

                                                                <label>GSTR1 Return Date</label>
                                                                <input type="date" class="form-control" id="gstr1ReturnDate" name="gstr1ReturnDate">

                                                                <label>Total Taxable Amount</label>
                                                                <input type="number" class="form-control" id="totalTaxableAmount" name="totalTaxableAmount">

                                                                <label>Total CGST</label>
                                                                <input type="number" class="form-control" id="totalGstCgst" name="totalGstCgst">

                                                                <label>Total SGST</label>
                                                                <input type="number" class="form-control" id="totalGstSgst" name="totalGstSgst">

                                                                <label>Total IGST</label>
                                                                <input type="number" class="form-control" id="totalGstIgst" name="totalGstIgst">

                                                                <label>Total Cess</label>
                                                                <input type="number" class="form-control" id="totalCess" name="totalCess">

                                                                <label>ARN</label>
                                                                <input type="text" class="form-control" id="arn" name="arn">
                                                            </div>

                                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                                <button type="submit" name="addNewgstr1CompilanceSubmitBtn" id="addNewgstr1CompilanceSubmitBtn" class="btn btn-primary items-search-btn float-right">Submit</button>
                                                            </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                    </tbody>
                </table>

            </div>
        </div>

        <div class="modal" id="myModal2">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Table Column Settings</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                        <div class="modal-body" style="max-height: 450px;">
                            <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                            <input type="hidden" name="pageTableName" value="ERP_ACC_JOURNAL" />
                            <div class="modal-body">
                                <div id="dropdownframe"></div>
                                <div id="main2">
                                    <div class="checkAlltd d-flex gap-2 mb-2">
                                        <input type="checkbox" class="grand-checkbox" value="" />
                                        <p class="text-xs font-bold">Check All</p>
                                    </div>
                                    <?php $p = 1; ?>
                                    <table class="colomnTable">
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                GST-1</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                Period</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                Status</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                ARN</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="<?php echo $p; ?>" />
                                                Financial year</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?php echo $p; ?>" />
                                                Created at</td>
                                        </tr>

                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="<?php echo $p; ?>" />
                                                Created by</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                                Approved</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="<?php echo $p; ?>" />
                                                View</td>
                                        </tr>
                                    </table>
                                </div>
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

    </section>
</div>


<script>
    $(document).ready(function() {


        // DataTable
        var columnSl = 0;
        var table = $("#datatable").DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
            buttons: ['csv'],
            "lengthMenu": [
                [1000, 5000, 10000, -1],
                [1000, 5000, 10000, 'All'],
            ],
            "scrollY": 200,
            "scrollX": true,
            "ordering": false,
        });


    });
</script>



<?php
require_once("../common/footer.php");

function getMonthFromDigits($digits)
{

    $monthNumber = substr($digits, 0, 2);
    $year = substr($digits, 2, 4);

    // Define an array of months
    $months = [
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December'
    ];

    return isset($months[$monthNumber]) ? $months[$monthNumber] . " " . $year : 'Invalid month';
}

?>