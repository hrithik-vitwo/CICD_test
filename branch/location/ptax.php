<?php
require_once("../../app/v1/connection-branch-admin.php");   
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
// Add Functions
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");
require_once("../../app/v1/functions/branch/func-goods-controller.php");
$pageName =  basename($_SERVER['PHP_SELF'], '.php');

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_' . $currentDateTime;
//administratorLocationAuth();
if (!isset($_SESSION["logedBranchAdminInfo"]["adminId"]) || !isset($_SESSION["logedBranchAdminInfo"]["adminRole"])) {
    echo "Session Timeout";
    exit;
}


$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Month and Year',
        'slag' => 'month_year',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    // [
    //     'name' => 'Total PTAX',
    //     'slag' => 'sum_ptax',
    //     'icon' => '<ion-icon name="location-outline"></ion-icon>',
    //     'dataType' => 'string'
    // ],
    [
        'name' => 'Total Paid',
        'slag' => 'amount',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Total Due',
        'slag' => 'due_amount',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Action',
        'slag' => 'acconting_status',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ]
];

?>



<!-- <link rel="stylesheet" href="../../../public/assets/new_listing.css"> -->
<!-- <link rel="stylesheet" href="../../../public/assets/sales-order.css"> -->
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">

<!-- Content Wrapper detailed-view -->
<div class="content-wrapper report-wrapper is-stock-new vitwo-alpha-global">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <?php
            // console($_COOKIE);

            ?>

            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 p-0">
                    <div class="card card-tabs reports-card">
                        <div class="card-body">
                            <div class="row filter-serach-row m-0">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="row table-header-item">
                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-0 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                                <!---------------------- Search START -->
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                    <li class="pt-2 pt-md-0 px-3 d-flex justify-content-between align-items-center header-space" style="width:100%">
                                        <div class="left-block">
                                            <div class="label-select">
                                                <h3 class="card-title mb-0"> Manage P-TAX </h3>
                                            </div>
                                        </div>

                                        <div class="right-block">
                                            <?php require_once("components/fa/payroll-tabs.php"); ?>
                                            <button class="btn btn-sm fillscreen-btn" onclick="openFullscreen()"><i class="fa fa-expand fa-2x"></i></button>
                                        </div>
                                    </li>
                                </ul>
                                <!---------------------- Search END -->
                            </div>

                            <div class="card card-tabs mb-0" style="border-radius: 20px;">
                                <div class="card-body">
                                    <!-- <div class="row filter-search">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="row table-header-item">

                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            
                                        </div>
                                    </div> -->
                                    <div class="tab-content" id="custom-tabs-two-tabContent">
                                        <div class="tab-pane dataTableTemplate dataTable_stock fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                                            <div class="filter-action">
                                                <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal1"> <ion-icon name="settings-outline"></ion-icon> Manage Column</a>
                                                <div class="length-row">
                                                    <span>Show</span>
                                                    <select name="" id="" class="custom-select" value="25">
                                                        <option value="10">10</option>
                                                        <option value="25" selected="selected">25</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                        <option value="200">200</option>
                                                        <option value="250">250</option>
                                                    </select>
                                                    <span>Entries</span>
                                                </div>
                                                <div class="filter-search">
                                                    <div class="icon-search" data-toggle="modal" data-target="#btnSearchCollpase_modal">
                                                        <p>Advance Search</p>
                                                        <ion-icon name="filter-outline"></ion-icon>
                                                    </div>
                                                </div>
                                            </div>

                                            <a href="manage-production-declaration.php?create" class="btn btn-create waves-effect waves-light" type="button">
                                                <ion-icon name="add-outline" role="img" class="md hydrated" aria-label="add outline"></ion-icon>
                                                Create
                                            </a>
                                            <table id="dataTable_detailed_view" class="table table-hover table-nowrap stock-new-table transactional-book-table">

                                                <thead>
                                                    <tr>
                                                        <?php
                                                        foreach ($columnMapping as $index => $column) {
                                                        ?>
                                                            <th data-value="<?= $index ?>"><?= $column['name'] ?></th>
                                                        <?php
                                                        }
                                                        ?>
                                                    </tr>
                                                </thead>
                                                <tbody id="detailed_tbody">
                                                </tbody>
                                            </table>
                                            <div class="row custom-table-footer">
                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div id="limitText" class="limit-text">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div id="yourDataTable_paginate">
                                                        <div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <!---------------------------------deialed View Table settings Model Start--------------------------------->
                                            <div class="modal manage-column-setting-modal" id="myModal1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title text-sm">Detailed View Column Settings</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form name="table_settings_detailed_view" method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
                                                            <div class="modal-body" style="max-height: 450px;">
                                                                <!-- <h4 class="modal-title">Detailed View Column Settings</h4> -->
                                                                <input type="hidden" id="tablename" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                                                <input type="hidden" id="pageTableName" name="pageTableName" value="ERP_TEST_<?= $pageName ?>" />
                                                                <div class="modal-body">
                                                                    <div id="dropdownframe"></div>
                                                                    <div id="main2">
                                                                        <div class="checkAlltd d-flex gap-2 mb-3 pl-2">
                                                                            <input type="checkbox" class="grand-checkbox" value="" />
                                                                            <p class="text-xs font-bold">Check All</p>
                                                                        </div>

                                                                        <table class="colomnTable">
                                                                            <?php

                                                                            foreach ($columnMapping as $index => $column) {

                                                                            ?>
                                                                                <tr>
                                                                                    <td valign="top" style="width: 165px">

                                                                                        <input type="checkbox" class="settingsCheckbox_detailed" name="settingsCheckbox[]" id="settingsCheckbox_detailed_view[]" value='<?= $column['slag'] ?>'>
                                                                                        <?= $column['name'] ?>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php
                                                                            }
                                                                            ?>

                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="submit" id="check-box-submt" name="check-box-submit" data-dismiss="modal" class="btn btn-primary">Save</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!---------------------------------Table Model End--------------------------------->

                                            <div class="modal " id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-sm" id="exampleModalLongTitle">Advanced Filter</h5>
                                                        </div>
                                                        <form id="myForm" method="post" action="">
                                                            <div class="modal-body">

                                                                <table>
                                                                    <tbody>
                                                                        <?php
                                                                        $operators = ["CONTAINS", "NOT CONTAINS", "<", ">", ">=", "<=", "=", "!=", "BETWEEN"];

                                                                        foreach ($columnMapping as $columnIndex  => $column) {
                                                                            if ($columnIndex === 0) {
                                                                                continue;
                                                                            } ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <div class="icon-filter d-flex align-items-center gap-2">
                                                                                        <?= $column['icon'] ?>
                                                                                        <p id="columnName_<?= $columnIndex ?>"><?= $column['name'] ?></p>
                                                                                        <input type="hidden" id="columnSlag_<?= $columnIndex ?>" value="<?= $column['slag'] ?>">
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <select class="form-control selectOperator" id="selectOperator_<?= $columnIndex ?>" name="operator[]" val="">
                                                                                        <?php
                                                                                        if (($column['dataType'] === 'date')) {
                                                                                            $operator = array_slice($operators, -3, 3);
                                                                                            foreach ($operator as $oper) {
                                                                                        ?>
                                                                                                <option value="<?= $oper ?>"><?= $oper ?></option>
                                                                                                <?php
                                                                                            }
                                                                                        }
                                                                                        elseif ($column['dataType'] === 'number') {
                                                                                            $operator = array_slice($operators, 2, 6);
                                                                                            foreach ($operator as $oper) {
                                                                                            ?>
                                                                                                <option value="<?= $oper ?>">
                                                                                                    <?= $oper ?>
                                                                                                </option>
                                                                                                <?php

                                                                                            }
                                                                                        }
                                                                                         else {
                                                                                            $operator = array_slice($operators, 0, 2);
                                                                                            foreach ($operator as $oper) {
                                                                                                if ($oper === 'CONTAINS') {
                                                                                                ?>
                                                                                                    <option value="LIKE"><?= $oper ?></option>
                                                                                                <?php
                                                                                                } else { ?>

                                                                                                    <option value="NOT LIKE"><?= $oper ?></option>

                                                                                        <?php
                                                                                                }
                                                                                            }
                                                                                        } ?>
                                                                                    </select>
                                                                                </td>
                                                                                <td id="td_<?= $columnIndex ?>">
                                                                                    <input type="<?= ($column['dataType'] === 'date') ? 'date' : 'input' ?>" data-operator-val="" name="value[]" class="fld form-control m-input" id="value_<?= $columnIndex ?>" placeholder="Enter Keyword" value="">
                                                                                </td>
                                                                            </tr>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" id="serach_submit" class="btn btn-primary" data-dismiss="modal">Search</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
    </section>
    <!-- /.content -->
</div>

</td>

</tr>


<!-----add form modal start --->
<div class="modal fade hsn-dropdown-modal" id="addToLocation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
    <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <form method="POST" action="">
                    <input type="hidden" name="createLocationItem" id="createLocationItem" value="">
                    <input type="hidden" id="item_id" name="item_id" value="">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">
                                <div class="card-header">
                                    <h4>Storage Details</h4>
                                </div>
                                <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">
                                    <div class="row">

                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                            <div class="row goods-info-form-view customer-info-form-view">

                                                <div class="col-lg-4 col-md-4 col-sm-4">

                                                    <div class="form-input">

                                                        <label for="">Storage Control</label>
                                                        <input type="text" name="storageControl" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4">

                                                    <div class="form-input">

                                                        <label for="">Max Storage Period</label>

                                                        <input type="text" name="maxStoragePeriod" class="form-control">

                                                    </div>

                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                    <div class="form-input">
                                                        <label class="label-hidden" for="">Min Time Unit</label>
                                                        <select id="minTime" name="minTime" class="select2 form-control">
                                                            <option value="">Min Time Unit</option>
                                                            <option value="Day">Day</option>
                                                            <option value="Month">Month</option>
                                                            <option value="Hours">Hours</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6">

                                                    <div class="form-input">

                                                        <label for="">Minimum Remain Self life</label>

                                                        <input type="text" name="minRemainSelfLife" class="form-control">

                                                    </div>

                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-input">
                                                        <label class="label-hidden" for="">Max Time Unit</label>
                                                        <select id="maxTime" name="maxTime" class="select2 form-control">
                                                            <option value="">Max Time Unit</option>
                                                            <option value="Day">Day</option>
                                                            <option value="Month">Month</option>
                                                            <option value="Hours">Hours</option>

                                                        </select>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                <div class="card-header">

                                    <h4>Pricing and Discount

                                        <span class="text-danger">*</span>

                                    </h4>

                                </div>

                                <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                    <div class="row">

                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                            <div class="row goods-info-form-view customer-info-form-view">

                                                <div class="col-lg-6 col-md-6 col-sm-6">

                                                    <div class="form-input">

                                                        <label for="">Target price</label>

                                                        <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" placeholder="price">

                                                    </div>

                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-6">

                                                    <div class="form-input">

                                                        <label for="">Max Discount</label>

                                                        <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Submit</button>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-body" style="height: 500px; overflow: auto;">
                <div class="card">

                </div>
            </div>
        </div>
    </div>


    <?php
    require_once("../common/footer2.php");
    ?>


    <script>
        const table = new DataTable('#example', {
            ajax: '../php/staff.php',
            columns: [{
                    data: null,
                    render: (data) => data.first_name + ' ' + data.last_name
                },
                {
                    data: 'position'
                },
                {
                    data: 'office'
                },
                {
                    data: 'extn'
                },
                {
                    data: 'start_date'
                },
                {
                    data: 'salary',
                    render: DataTable.render.number(null, null, 0, '$')
                }
            ],
            colReorder: true,

        });
    </script>

    <script>
        $(document).ready(function() {
            var indexValues = [];
            var dataTable;

            function initializeDataTable() {
                dataTable = $("#dataTable_detailed_view").DataTable({
                    dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
                    "lengthMenu": [10, 25, 50, 100, 200, 250],
                    "ordering": false,
                    info: false,
                    "initComplete": function(settings, json) {
                        $('#dataTable_detailed_view_filter input[type="search"]').attr('placeholder', 'Search....');
                    },

                    buttons: [{
                        extend: 'collection',
                        text: '<ion-icon name="download-outline"></ion-icon> Export',
                        buttons: [{
                                extend: 'copy',
                                text: '<ion-icon name="copy-outline" class="ion-copy"></ion-icon> Copy'
                            },
                            {
                                extend: 'excel',
                                text: '<ion-icon name="document-outline" class="ion-excel"></ion-icon> Excel'
                            },
                            {
                                extend: 'csv',
                                text: '<ion-icon name="document-outline" class="ion-csv"></ion-icon> CSV'
                            }
                        ]
                    }],
                    // select: true,
                    "bPaginate": false,
                });

            }

            initializeDataTable();

            function fill_datatable(formDatas = '', pageNo = '', limit = '') {
                var fdate = "<?php echo $f_date; ?>";
                var to_date = "<?php echo $to_date; ?>";
                var comid = <?php echo $company_id; ?>;
                var locId = <?php echo $location_id; ?>;
                var bId = <?php echo $branch_id; ?>;
                var columnMapping = <?php echo json_encode($columnMapping); ?>;
                // var checkboxSettings = Cookies.get('cookiesProdDeclare');
                var notVisibleColArr = [];

                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-ptax.php",
                    dataType: 'json',
                    data: {
                        act: 'ptax',
                        comid: comid,
                        locId: locId,
                        bId: bId,
                        formDatas: formDatas,
                        pageNo: pageNo,
                        limit: limit
                    },
                    beforeSend: function() {
                        $("#detailed_tbody").html(`<td colspan=16 class='text-center'>Data is loading....</td>`);
                    },
                    success: function(response) {

                        console.log(response);

                        if (response.status) {
                            var responseObj = response.data;
                            $('#yourDataTable_paginate').show();
                            $('#limitText').show();

                            dataTable.clear().draw();
                            dataTable.columns().visible(false);

                            $.each(responseObj, function(index, value) {
                                $('#item_id').val(value.itemId);

                                dataTable.row.add([

                                    value.sl_no,
                                    value.month_year,
                                    value.amount,
                                    value.due_amount,
                                    value.acconting_status,


                                    ` <div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>
                                    <li>
                                        <button data-toggle="modal" data-target="#editModal"><ion-icon name="create-outline" class="ion-edit"></ion-icon>Edit</button>
                                    </li>
                                    <li>
                                        <button data-toggle="modal" data-target="#viewModal"><ion-icon name="trash-outline" class="ion-delete"></ion-icon>Delete</button>
                                    </li>
                                    <li>
                                        <button data-toggle="modal" data-target="#viewModal"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                    </li>
                                    
                                    </ul>
                                   
                                </div>`,
                                ]).draw(false);
                            });

                            $('#yourDataTable_paginate').html(response.pagination);
                            $('#limitText').html(response.limitTxt);



                            var checkboxSettings = Cookies.get('cookiesPtax');
                            // console.log(checkboxSettings);
                            if (checkboxSettings) {
                                var checkedColumns = JSON.parse(checkboxSettings);

                                $(".settingsCheckbox_detailed").each(function(index) {
                                    var columnVal = $(this).val();
                                    if (checkedColumns.includes(columnVal)) {
                                        $(this).prop("checked", true);
                                        dataTable.column(index).visible(true);

                                    } else {
                                        notVisibleColArr.push(index);
                                    }
                                });
                                if (notVisibleColArr.length > 0) {
                                    notVisibleColArr.forEach(function(index) {
                                        dataTable.column(index).visible(false);
                                    });
                                }

                                // console.log('Cookie value:', checkboxSettings);

                            } else {
                                $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                                $(".settingsCheckbox_detailed").each(function(index) {
                                    if ($(this).prop("checked")) {
                                        dataTable.column(index).visible(true);

                                    }
                                });

                                console.log('Cookie is blank.');

                            }
                        } else {

                            $("#detailed_tbody").html(`<td colspan=16 class='text-center'>No data found</td>`);
                            $('#yourDataTable_paginate').remove();
                            $('#limitText').remove();
                        }
                    }
                });
            }

            fill_datatable();


            //    ----- page length limit-----\
            let formInputs = {};
            $(document).on("change", ".custom-select", function(e) {
                var maxlimit = $(this).val();
                fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit);

            });

            //    ------------ pagination-------------

            $(document).on("click", "#pagination a ", function(e) {
                e.preventDefault();
                var page_id = $(this).attr('id');
                var limitDisplay = $(".custom-select").val();

                fill_datatable(formDatas = formInputs, pageNo = page_id, limit = limitDisplay);

            });

            //<--------------advance search------------------------------->
            $(document).ready(function() {
                $(document).on("click", "#serach_submit", function(event) {
                    event.preventDefault();
                    let values;
                    $(".selectOperator").each(function() {
                        let columnIndex = ($(this).attr("id")).split("_")[1];
                        let columnSlag = $(`#columnSlag_${columnIndex}`).val();
                        let operatorName = $(`#selectOperator_${columnIndex}`).val();
                        let value = $(`#value_${columnIndex}`).val() ?? "";
                        let value2 = $(`#value2_${columnIndex}`).val() ?? "";


                        if ((columnSlag === 'expectedDate') && operatorName == "BETWEEN") {
                            formInputs[columnSlag] = {
                                operatorName,
                                value: {
                                    fromDate: value,
                                    toDate: value2
                                }
                            };
                        } else {
                            formInputs[columnSlag] = {
                                operatorName,
                                value
                            };
                        }
                    });

                    $('#btnSearchCollpase_modal').modal('hide');
                    console.log("FormInputs:", formInputs);

                    fill_datatable(formDatas = formInputs);

                });
            });

            // -------------checkbox----------------------

            $(document).ready(function() {
                var columnMapping = <?php echo json_encode($columnMapping); ?>;

                var indexValues = [];

                function toggleColumnVisibility(columnIndex, checkbox) {
                    var column = dataTable.column(columnIndex);
                    column.visible(checkbox.checked);

                }

                $("input[name='settingsCheckbox[]']").change(function() {
                    var columnVal = $(this).val();
                    console.log(columnVal);

                    var index = columnMapping.findIndex(function(column) {
                        return column.slag === columnVal;
                    });
                    console.log(index);
                    toggleColumnVisibility(index, this);
                });

                $(".grand-checkbox").on("click", function() {
                    $(".colomnTable tr td input[type='checkbox']").prop("checked", this.checked);
                    $("input[name='settingsCheckbox[]']").each(function() {
                        var columnVal = $(this).val();
                        // console.log(columnVal);
                        var index = columnMapping.findIndex(function(column) {
                            return column.slag === columnVal;
                        });
                        if ($(this).is(':checked')) {
                            indexValues.push(index);
                        } else {
                            var removeIndex = indexValues.indexOf(index);
                            if (removeIndex !== -1) {
                                indexValues.splice(removeIndex, 1);
                            }
                        }
                        toggleColumnVisibility(index, this);
                    });
                });

            });

        });

        //    -------------- save cookies--------------------

        $(document).ready(function() {
            $(document).on("click", "#check-box-submt", function(event) {
                // console.log("Hiiiii");
                event.preventDefault();
                // $("#myModal1").modal().hide();
                $('#btnSearchCollpase_modal').modal('hide');
                var tablename = $("#tablename").val();
                var pageTableName = $("#pageTableName").val();
                var settingsCheckbox = [];
                var formData = {};
                $(".settingsCheckbox_detailed").each(function() {
                    if ($(this).prop('checked')) {
                        var chkBox = $(this).val();
                        settingsCheckbox.push(chkBox);
                        formData = {
                            tablename,
                            pageTableName,
                            settingsCheckbox
                        };
                    }
                });

                console.log(formData);
                if (settingsCheckbox.length < 5) {
                    alert("Please select at least 5");
                } else {
                    $.ajax({
                        type: "POST",
                        url: "ajaxs/ajax-save-cookies.php",
                        data: {
                            act: 'ptax',
                            formData: formData
                        },
                        success: function(response) {
                            console.log(response);
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });

                }
            });
        });
    </script>
    <!-- -----fromDate todate input add--- -->
    <script>
        $(document).ready(function() {
            $(document).on("change", ".selectOperator", function() {
                let columnIndex = parseInt(($(this).attr("id")).split("_")[1]);
                let operatorName = $(this).val();
                let columnName = $(`#columnName_${columnIndex}`).html();
                let inputContainer = $(`#td_${columnIndex}`);
                let inputId;
                if (columnName === 'Require Date') {
                    inputId = "value2_" + columnIndex;
                }

                if ((columnName === 'Require Date') && operatorName === 'BETWEEN') {
                    inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
                } else {
                    $(`#${inputId}`).remove();
                }
                console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
            });

        });
    </script>

    <script>
        function openFullscreen() {
            var elem = document.getElementById("listTabPan")

            if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) {
                    /* Safari */
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) {
                    /* IE11 */
                    elem.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    /* Safari */
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    /* IE11 */
                    document.msExitFullscreen();
                }
            }
        }

        document.addEventListener('fullscreenchange', exitHandler);
        document.addEventListener('webkitfullscreenchange', exitHandler);
        document.addEventListener('MSFullscreenChange', exitHandler);

        function exitHandler() {
            if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
                $(".content-wrapper").removeClass("fullscreen-mode");
            } else {
                $(".content-wrapper").addClass("fullscreen-mode");
            }
        }
    </script>

    <script>
        document.querySelector('table.stock-new-table').onclick = ({
            target
        }) => {
            if (!target.classList.contains('more')) return
            document.querySelectorAll('.dropout.active').forEach(
                (d) => d !== target.parentElement && d.classList.remove('active')
            )
            target.parentElement.classList.toggle('active')
        }
    </script>