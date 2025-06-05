<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("./controller/gstr3b.controller.php");
require_once("../../app/v1/functions/branch/func-compliance-controller.php");
$authGstinPortalObj = new AuthGstinPortal();
$authObj = $authGstinPortalObj->checkAuth();

$gstr3bControllerObj = new ComplianceGSTR3b();
$gstr3bControllerObj->setUpcomingGstr3bFillingDate();

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
        <h4 class="text-lg font-bold mt-4 mb-4">GSTR-3B</h4>

        <div class="head-btn-section mb-3">
            <div class="filter-list gst-one-filter">
                <a href="gstr2b-graphical-view.php" class="btn"><i class="fas fa-chart-bar mr-2"></i>Graphical View</a>
                <a href="" class="btn active"><i class="fa fa-list mr-2"></i>Concised View</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <?php
                ?>
                <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                <table id="datatable" width="100" class="table table-hover defaultDataTable gst-consised-view">
                    <thead>
                        <tr>
                            <th>Sl.</th>
                            <th>Period</th>
                            <th>Taxable Amount</th>
                            <th>CGST</th>
                            <th>SGST</th>
                            <th>IGST</th>
                            <th>CESS</th>
                            <th>Updated at</th>
                            <th>Updated by</th>
                            <!-- <th>Approved</th> -->
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- erp_compliance_gstr3b table data -->
                        <?php
                        $gsr3bCompTableDatasql = "SELECT * FROM `erp_compliance_gstr3b` WHERE `company_id`=" . $company_id . " AND `branch_id`=" . $branch_id . " ORDER BY id DESC";
                        $gsr3bCompTableDatasqlObj = queryGet($gsr3bCompTableDatasql, true);

                        $gst3bFileFreqDateObj = queryGet("SELECT branch_gstin_file_r3b_day,branch_gstin_file_frequency FROM `erp_branches` WHERE company_id= $company_id AND branch_id= $branch_id");
                        $branch_gstin_file_frequency = $gst3bFileFreqDateObj['data']['branch_gstin_file_frequency'];
                        $slNo = 0;
                        foreach ($gsr3bCompTableDatasqlObj['data'] as $key => $gsr3bOnedata) {
                            $slNo += 1;
                            $statusObj = $statusArr[$gsr3bOnedata['gstr3b_return_file_status']];
                            $updated_by = $gsr3bOnedata['updated_by'] == "Auto" ? 'Auto' : getCreatedByUser($gsr3bOnedata['updated_by']);
                            $approved_by = $gsr3bOnedata['approved_by'] == null ? '-' : getCreatedByUser($gsr3bOnedata['approved_by']);
                            $isFiled = ($gsr3bOnedata['gstr3b_return_file_status'] >= 6) ? 1 : 0;
                            $getLastGstr3bFileDay = $gsr3bOnedata['gstr3b_return_period'];
                            $getLastGstr3bFileDate = date('d-m-Y', strtotime('01-' . substr($getLastGstr3bFileDay, 0, 2) . '-' . substr($getLastGstr3bFileDay, 2)));
                            $action = [
                                "period" => $getLastGstr3bFileDay,
                                "startDate" => date('01-m-Y', strtotime($getLastGstr3bFileDate)),
                                "endDate" => date('t-m-Y', strtotime($getLastGstr3bFileDate)),
                                "frequency" => $branch_gstin_file_frequency,
                                "isFiled" => $isFiled
                            ];
                            $actionButton = $gsr3bOnedata['status'] == 'active' ? "File" : $gsr3bOnedata['status'];
                        ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= getMonthFromDigits($gsr3bOnedata['gstr3b_return_period']) ?></td>
                                <td><?= $gsr3bOnedata['gstr3b_return_total_taxable'] ?></td>
                                <td><?= $gsr3bOnedata['gstr3b_return_total_cgst'] ?></td>
                                <td><?= $gsr3bOnedata['gstr3b_return_total_sgst'] ?></td>
                                <td><?= $gsr3bOnedata['gstr3b_return_total_igst'] ?></td>
                                <td><?= $gsr3bOnedata['gstr3b_return_total_cess'] ?></td>
                                <td><?= formatDateORDateTime($gsr3bOnedata['created_at']) ?></td>
                                <td><?= $updated_by ?></td>
                                <!-- <td><?= $approved_by ?></td> -->
                                <td>
                                    <span class=""><?= ucfirst($gsr3bOnedata['status']) ?></span>
                                </td>
                                <td>
                                    <a style="cursor:pointer" class="btn btn-sm status bg-primary actionBtn" id="reconStepBtn_<?= $slNo ?>" data-action='<?= (base64_encode(json_encode($action)))  ?>'><?=
                                                                                                                                                                                                        $actionButton ?></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>
        </div>
    </section>
</div>
<!-- Modal -->
<div class="modal fade" id="authModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Authentication Modal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Your content here -->
                <div id="componentViewDiv">
                    <!-- The elements will come here from ajax -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
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

    $(document).on('click', '.actionBtn', function() {
        let action = ($(this).data('action'));
        // $('#authModal').modal('show');
        $.ajax({
            type: "POST",
            url: `ajaxs/view/ajax-view-provider-gstr3b.php?action=${action}`,
            dataType: 'json',
            beforeSend: function() {
                $("#componentViewDiv").html("Loding...");
                console.log("Loading...");
            },
            success: function(response) {
                console.log(response);
             
                if (response.status == "authFailed") {
                    window.location.href = `gstr3b-filling.php?action=${action}`;
                } else if (response.status == "success") {
                    window.location.href = `gst-3B-summary.php?action=${action}`;
                }
            }
        })
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