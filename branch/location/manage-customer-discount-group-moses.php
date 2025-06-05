<?php
include("../../app/v1/connection-branch-admin.php");
// administratorAuth();
include("../common/header.php");
include("../common/navbar.php");
include("../common/sidebar.php");
require_once("../common/pagination.php");
include("../../app/v1/functions/branch/func-discount-controller.php");
$groupController = new CustomerDiscountGroupController();

if (isset($_POST["visit"])) {

    $newStatusObj = Visitfunctionalities($_POST);
    swalToast($newStatusObj["status"], $newStatusObj["message"], COMPANY_URL);
}


// if (isset($_POST["createdata"])) {

//     // console($_POST);
//     // exit();

//     $addNewObj = $groupController->create_customer_discount_group($_POST, $company_id, $created_by);

//     if ($addNewObj["status"] == "success") {
//         swalToast($addNewObj["status"], $addNewObj["message"], $_SERVER['PHP_SELF']);
//     } else {
//         swalToast($addNewObj["status"], $addNewObj["message"]);
//     }
// }

// if (isset($_POST["editdata"])) {

//     $editDataObj = $groupController->edit_customer_discount_group($_POST, $company_id, $created_by);
//     swalToast($editDataObj["status"], $editDataObj["message"]);
// }

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

$pageName = basename($_SERVER['PHP_SELF'], '.php');

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_' . $currentDateTime;
$newFileNameDownloadall = $fileNameWithoutExtension . 'download_all_' . $currentDateTime;

if (!isset($_COOKIE["cookieCusDiscountGroup"])) {
    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookieCusDiscountGroup", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    } else {
        for ($i = 0; $i < 5; $i++) {
            $isChecked = ($i < 5) ? 'checked' : '';
        }
    }
}




$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Customer Discount Group Name',
        'slag' => 'customer_discount_group',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Created By',
        'slag' => 'created_by',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Created At',
        'slag' => 'created_at',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Modified By',
        'slag' => 'updated_by',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Modified At',
        'slag' => 'updated_at',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'date'
    ]
];

?>
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper report-wrapper vitwo-alpha-global">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <?php ?>
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
                                    <li class="pt-2 pt-md-0 px-3 d-flex justify-content-between align-items-center header-space"
                                        style="width:100%">
                                        <div class="left-block">
                                            <div class="label-select">
                                                <h3 class="card-title mb-0">Manage Customer Discount Group
                                                </h3>
                                            </div>
                                        </div>
                                        

                                        <div class="right-block">

                                            <button class="btn btn-sm fillscreen-btn" onclick="openFullscreen()">
                                                <ion-icon name="expand-outline"></ion-icon>
                                            </button>
                                            <button type="button" id="revealList" class="page-list">
                                                <ion-icon name="funnel-outline"></ion-icon>
                                            </button>
                                            <div id="modal-container">
                                                <div class="modal-background">
                                                    <div class="modal">
                                                        <button class="btn-close-modal" is="closeFilterModal">
                                                            <ion-icon name="close-outline"></ion-icon>
                                                        </button>
                                                        <h5>Filter Pages</h5>
                                                       
                                                        <h5>Search and Export</h5>
                                                        <div class="filter-action filter-mobile-search mobile-page">
                                                            <a type="button" class="btn add-col setting-menu"
                                                                data-toggle="modal" data-target="#myModal1"> <ion-icon
                                                                    name="settings-outline"></ion-icon></a>
                                                            <div class="filter-search">
                                                                <div class="icon-search" data-toggle="modal"
                                                                    data-target="#btnSearchCollpase_modal">
                                                                    <ion-icon name="filter-outline"></ion-icon>
                                                                    Advance Filter
                                                                </div>
                                                            </div>
                                                            <div class="exportgroup mobile-page mobile-export">
                                                                <button class="exceltype btn btn-primary btn-export"
                                                                    type="button">
                                                                    <ion-icon name="download-outline"></ion-icon>
                                                                </button>
                                                                <ul class="export-options">
                                                                    <li>
                                                                        <button class="ion-paginationlistCusDisGroup">
                                                                            <ion-icon name="list-outline"
                                                                                class="ion-paginationlistCusDisGroup md hydrated"
                                                                                id="exportAllBtn" role="img"
                                                                                aria-label="list outline"></ion-icon>Export
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button class="ion-fulllistCusDisGroup ">
                                                                            <ion-icon name="list-outline"
                                                                                class="ion-fulllistCusDisGroup md hydrated"
                                                                                role="img"
                                                                                aria-label="list outline"></ion-icon>Download
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <a href=""
                                                                class="btn btn-create mobile-page mobile-create addCusdiscountMrpbtn"
                                                                data-toggle="modal" data-target="#funcAddForm"
                                                                type="button">
                                                                <ion-icon name="add-outline"></ion-icon>
                                                                Create
                                                            </a>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <!---------------------- Search END -->
                            </div>



                            <div class="card card-tabs mobile-transform-card mb-0" style="border-radius: 20px;">
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-two-tabContent">
                                        <div class="tab-pane dataTableTemplate dataTable_stock fade show active"
                                            id="listTabPan" role="tabpanel" aria-labelledby="listTab"
                                            style="background: #fff; border-radius: 20px;">
                                            <div class="length-row mobile-legth-row">
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
                                            <div class="filter-action">
                                                <a type="button" class="btn add-col setting-menu" data-toggle="modal"
                                                    data-target="#myModal1"> <ion-icon
                                                        name="settings-outline"></ion-icon> Manage Column</a>
                                                <div class="length-row">
                                                    <span>Show</span>
                                                    <select name="" id="cusDisGroupLimit" class="custom-select">
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
                                                    <div class="icon-search" data-toggle="modal"
                                                        data-target="#btnSearchCollpase_modal">
                                                        <p>Advance Search</p>
                                                        <ion-icon name="filter-outline"></ion-icon>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="exportgroup">
                                                <button class="exceltype btn btn-primary btn-export" type="button">
                                                    <ion-icon name="download-outline"></ion-icon>
                                                    Export
                                                </button>
                                                <ul class="export-options">
                                                    <li>
                                                        <button class="ion-paginationlistCusDisGroup">
                                                            <ion-icon name="list-outline"
                                                                class="ion-paginationlistCusDisGroup md hydrated"
                                                                role="img" aria-label="list outline"></ion-icon>Export
                                                        </button>
                                                    </li>
                                                    <li>

                                                        <button class="ion-fulllistCusDisGroup">
                                                            <ion-icon name="list-outline"
                                                                class="ion-fulllistCusDisGroup md hydrated" role="img"
                                                                aria-label="list outline"></ion-icon>Download
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <a href="" class="btn btn-create mobile-page mobile-create addMrpbtn"
                                                data-toggle="modal" data-target="#funcAddForm" type="button">
                                                <ion-icon name="add-outline"></ion-icon>
                                                Create
                                            </a>


                                            <table id="dataTable_detailed_view"
                                                class="table table-hover table-nowrap stock-new-table transactional-book-table">

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
                                                            <h4 class="modal-title text-sm">Detailed View Column
                                                                Settings</h4>
                                                            <button type="button" class="close"
                                                                data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form name="table_settings_detailed_view" method="POST"
                                                            action="<?php $_SERVER['PHP_SELF']; ?>">
                                                            <div class="modal-body" style="max-height: 450px;">
                                                                <!-- <h4 class="modal-title">Detailed View Column Settings</h4> -->
                                                                <input type="hidden" id="tablename" name="tablename"
                                                                    value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                                                <input type="hidden" id="pageTableName"
                                                                    name="pageTableName"
                                                                    value="ERP_TEST_<?= $pageName ?>" />
                                                                <div class="modal-body">
                                                                    <div id="dropdownframe"></div>
                                                                    <div id="main2">
                                                                        <div class="checkAlltd d-flex gap-2 mb-3 pl-2">
                                                                            <input type="checkbox"
                                                                                class="grand-checkbox" value="" />
                                                                            <p class="text-xs font-bold">Check All</p>
                                                                        </div>

                                                                        <table class="colomnTable">
                                                                            <?php
                                                                            $cookieTableStockReport = json_decode($_COOKIE["cookieCusDiscountGroup"], true) ?? [];

                                                                            foreach ($columnMapping as $index => $column) {

                                                                                ?>
                                                                                <tr>
                                                                                    <td valign="top">

                                                                                        <input type="checkbox"
                                                                                            class="settingsCheckbox_detailed"
                                                                                            name="settingsCheckbox[]"
                                                                                            id="settingsCheckbox_detailed_view[]"
                                                                                            value='<?= $column['slag'] ?>'>
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
                                                                <button type="submit" id="check-box-submt"
                                                                    name="check-box-submit" data-dismiss="modal"
                                                                    class="btn btn-primary">Save</button>
                                                                <button type="button" class="btn btn-danger"
                                                                    data-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!---------------------------------Table Model End--------------------------------->

                                            <div class="modal " id="btnSearchCollpase_modal" tabindex="-1" role="dialog"
                                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-sm" id="exampleModalLongTitle">
                                                                Advanced Filter</h5>
                                                        </div>
                                                        <form id="myForm" method="post" action="">
                                                            <div class="modal-body">

                                                                <table>
                                                                    <tbody>
                                                                        <?php
                                                                        $operators = ["CONTAINS", "NOT CONTAINS", "<", ">", ">=", "<=", "=", "!=", "BETWEEN"];

                                                                        foreach ($columnMapping as $columnIndex => $column) {
                                                                            if ($columnIndex === 0) {
                                                                                continue;
                                                                            } ?>
                                                                        <tr>
                                                                            <td>
                                                                                <div
                                                                                    class="icon-filter d-flex align-items-center gap-2">
                                                                                    <?= $column['icon'] ?>
                                                                                    <p
                                                                                        id="columnName_<?= $columnIndex ?>">
                                                                                        <?= $column['name'] ?>
                                                                                    </p>
                                                                                    <input type="hidden"
                                                                                        id="columnSlag_<?= $columnIndex ?>"
                                                                                        value="<?= $column['slag'] ?>">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <select
                                                                                    class="form-control selectOperator"
                                                                                    id="selectOperator_<?= $columnIndex ?>"
                                                                                    name="operator[]" val="">
                                                                                    <?php
                                                                                    if (($column['dataType'] === 'date')) {
                                                                                        $operator = array_slice($operators, -3, 3);
                                                                                        foreach ($operator as $oper) {
                                                                                            ?>
                                                                                    <option value="<?= $oper ?>">
                                                                                        <?= $oper ?>
                                                                                    </option>
                                                                                    <?php
                                                                                        }
                                                                                    } elseif ($column['dataType'] === 'number') {
                                                                                        $operator = array_slice($operators, 2, 6);
                                                                                        foreach ($operator as $oper) {
                                                                                            ?>
                                                                                    <option value="<?= $oper ?>">
                                                                                        <?= $oper ?>
                                                                                    </option>
                                                                                    <?php

                                                                                        }
                                                                                    } else {
                                                                                        $operator = array_slice($operators, 0, 2);
                                                                                        foreach ($operator as $oper) {
                                                                                            if ($oper === 'CONTAINS') {
                                                                                                ?>
                                                                                    <option value="LIKE">
                                                                                        <?= $oper ?>
                                                                                    </option>
                                                                                    <?php
                                                                                            } else { ?>

                                                                                    <option value="NOT LIKE">
                                                                                        <?= $oper ?>
                                                                                    </option>

                                                                                    <?php
                                                                                            }
                                                                                        }
                                                                                    } ?>
                                                                                </select>
                                                                            </td>
                                                                            <td id="td_<?= $columnIndex ?>">
                                                                                <input
                                                                                    type="<?= ($column['dataType'] === 'date') ? 'date' : 'input' ?>"
                                                                                    data-operator-val="" name="value[]"
                                                                                    class="fld form-control m-input"
                                                                                    id="value_<?= $columnIndex ?>"
                                                                                    placeholder="Enter Keyword"
                                                                                    value="">
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" id="serach_reset"
                                                                    class="btn btn-primary">Reset</button>
                                                                <button type="submit" id="serach_submit"
                                                                    class="btn btn-primary"
                                                                    data-dismiss="modal">Search</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade add-modal func-add-modal" id="funcAddForm"
                                                tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <form action="" id="add_frm" name="add_frm">
                                                        <input type="hidden" name="createdata" id="createdata" value="">
                                                        <input type="hidden" name="fldAdminCompanyId"
                                                            id="fldAdminCompanyId"
                                                            value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">

                                                        <div class="modal-content card">
                                                            <div class="modal-header card-header pt-2 pb-2 px-3">
                                                                <h4 class="text-xs text-white mb-0">Create Customer Discount
                                                                    Group</h4>
                                                                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                    </button> -->
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                                        <div class="form-input mb-3">
                                                                            <label>Customer Discount Group Name* </label>
                                                                            <input type="text" class="form-control"
                                                                                id="addcusdisGrpname" name="name"
                                                                                required>
                                                                            <span class="error name"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" id="add_CusDispgrpName"
                                                                    class="btn btn-primary add_data"
                                                                    value="add_post">Submit</button>

                                                            </div>
                                                        </div>

                                                    </form>
                                                </div>
                                            </div>




                                            <!-- edit modal start  -->

                                            <div class="modal fade add-modal func-add-modal" id="editFunctionality"
                                                tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <form action="" method="POST" id="add_frm" name="add_frm">
                                                        <input type="hidden" name="editdata" id="editdata" value="">
                                                        <input type="hidden" name="id" id="editcusDisgrpid" value="">

                                                        <div class="modal-content card">
                                                            <div class="modal-header card-header pt-2 pb-2 px-3">
                                                                <h4 class="text-xs text-white mb-0">Edit Customer Discount
                                                                    Group</h4>

                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                                        <div class="form-input mb-3">
                                                                            <label>Group Name* </label>
                                                                            <input type="text" class="form-control"
                                                                                id="editcusdisGrpname"
                                                                                name="CustomerGroupName" value="" required>
                                                                            <span class="error name"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                                                                <button type="submit" id="update_CusDispgrpName"
                                                                    data-dismiss="modal"
                                                                    class="btn btn-primary update_data"
                                                                    value="update_post">Update</button>
                                                                <!-- <button class="btn btn-primary btnstyle gradientBtn ml-2 add_data" value="add_post"><i class="fa fa-plus fontSize"></i> Final Submit</button> -->

                                                            </div>
                                                        </div>

                                                    </form>
                                                </div>
                                            </div>
                                            <!-- edit modal end -->

                                            <!-- Global View start-->

                                            

                                            <!-- Global View end -->

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
</div>
<!-- /.row -->
</div>
</section>
<!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->
<!-- For Pegination------->
<form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
        echo $_REQUEST['pageNo'];
    } ?>">
</form>
<!-- End Pegination from------->

<?php
require_once("../common/footer2.php");
?>
<script>
    var input = document.getElementById("myInput");
    input.addEventListener("keypress", function (event) {
        // console.log(event.key)

        if (event.key === "Enter") {
            event.preventDefault();
            // alert("clicked")
            document.getElementById("myBtn").click();
        }
    });
    var form = document.getElementById("search");

    document.getElementById("myBtn").addEventListener("click", function () {
        form.submit();
    });
</script>

<script>
    // $('.btn-edit').on('click', function () {
    //     let gid = $(this).data('gid');

    //     let gname = $(this).data('gname');

    //     $('#editmrpGroupName').val(gname);
    //     $('#editGroupId').val(gid);


    //     $("#editFunctionality").modal('show');

    // });






    $('.m-input').on('keyup', function () {
        $(this).parent().children('.error').hide()
    });
    /*
      $(".add_data").click(function() {
        var data = this.value;
        $("#createdata").val(data);
        let flag = 1;
        var Ragex = "/[0-9]{4}/";
        if ($("#functionalities_name").val() == "") {
          $(".functionalities_name").show();
          $(".functionalities_name").html("functionalities name is requried.");
          flag++;
        } else {
          $(".functionalities_name").hide();
          $(".functionalities_name").html("");
        }
        if ($("#functionalities_desc").val() == "") {
          $(".functionalities_desc").show();
          $(".functionalities_desc").html("Description is requried.");
          flag++;
        } else {
          $(".functionalities_desc").hide();
          $(".functionalities_desc").html("");
        }
        if (flag == 1) {
          $("#add_frm").submit();
        }


      });
      $(".edit_data").click(function() {
        var data = this.value;
        $("#editdata").val(data);
        alert(data);
        //$( "#edit_frm" ).submit();
      });
    */

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
        $.each($("input[name='settingsCheckbox[]']:checked"), function () {
            favorite.push($(this).val());
        });
        var check = favorite.length;
        if (check < 5) {
            alert("Please Check Atlast 5");
            return false;
        }

    }


    $(document).on("click", "#btnSearchCollpase", function () {
        sec = document.getElementById("btnSearchCollpase").parentElement;
        coll = sec.getElementsByClassName("collapsible-content")[0];

        if (sec.style.width != '100%') {
            sec.style.width = '100%';
        } else {
            sec.style.width = 'auto';
        }

        if (coll.style.height != 'auto') {
            coll.style.height = 'auto';
        } else {
            coll.style.height = '0px';
        }

        $(this).children().toggleClass("fa-search fa-times");

    });

    $(document).ready(function () {

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
        el.addEventListener("blur", function () {
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




<!-----------mobile filter list------------>


<script>
    $(document).ready(function () {
        $("button.page-list").click(function () {
            var buttonId = $(this).attr("id");
            $("#modal-container").removeAttr("class").addClass(buttonId);
            $(".mobile-transform-card").addClass("modal-active");
        });

        $(".btn-close-modal").click(function () {
            $("#modal-container").toggleClass("out");
            $(".mobile-transform-card").removeClass("modal-active");
        });
    })
</script>


<!-- modal view responsive more tabs -->

<script>
    $(document).ready(function () {
        // Adjust tabs based on window size
        adjustTabs();

        // Listen for window resize event
        $(window).resize(function () {
            adjustTabs();
        });
    });

    function adjustTabs() {
        var navTabs = $("#nav-tab");
        var moreDropdown = $("#more-dropdown");

        // Reset nav tabs
        navTabs.children().show();
        moreDropdown.empty();

        // Check if tabs overflow the container
        var visibleTabs = 7; // Number of visible tabs
        if ($(window).width() < 576) { // Adjust for mobile devices
            visibleTabs = 3; // Display only one tab on mobile
        } else if ($(window).width() > 576) {
            visibleTabs = 7;
        } else {
            visibleTabs = 7;
        }


        var hiddenTabs = navTabs.children(":gt(" + (visibleTabs) + ")");

        hiddenTabs.hide().appendTo(moreDropdown);

        // If there are hidden tabs, show the "More" dropdown
        if (hiddenTabs.length > 0) {
            moreDropdown.show();
        } else {
            moreDropdown.hide();
        }
    }
</script>

<script>

    $(document).ready(function () {
        var indexValues = [];
        var dataTable;
        let columnMapping = <?php echo json_encode($columnMapping); ?>
        // let dataPaginate;

        function initializeDataTable() {
            dataTable = $("#dataTable_detailed_view").DataTable({
                dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"billList_wrapper"t><ip>',
                "lengthMenu": [10, 25, 50, 100, 200, 250],
                "ordering": false,
                info: false,
                "initComplete": function (settings, json) {
                    $('#dataTable_detailed_view_filter input[type="search"]').attr('placeholder', 'Search....');
                },

                buttons: [{
                    extend: 'collection',
                    text: '<ion-icon name="download-outline"></ion-icon> Export',
                    buttons: [{
                        extend: 'csv',
                        text: '<ion-icon name="document-outline" class="ion-csv"></ion-icon> CSV'
                    }]
                }],
                // select: true,
                "bPaginate": false,
            });

        }
        $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

        initializeDataTable();

        var allData;
        var dataPaginate;


        function full_datatable() {
            let fromDate = "<?= $fromDate ?>"; // For Date Filter
            let toDate = "<?= $toDate ?>"; // For Date Filter        
            let comid = <?= $company_id ?>;
            let locId = <?= $location_id ?>;
            let bId = <?= $branch_id ?>;

            $.ajax({
                type: "POST",
                url: "ajaxs/customer/ajax-customer-discount-group-all.php",
                dataType: 'json',
                data: {
                    act: 'alldata',
                },
                beforeSend: function () {

                },
                success: function (response) {
                    // all_data = response.all_data;
                    allData = response.all_data;


                },
            });
        };
        full_datatable();

        function fill_datatable(formDatas = '', pageNo = '', limit = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var columnMapping = <?php echo json_encode($columnMapping); ?>;
            var checkboxSettings = Cookies.get('cookieCusDiscountGroup');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/customer/ajax-customer-discount-group-all.php",
                dataType: 'json',
                data: {
                    act: 'discountGroupTable',
                    comid: comid,
                    locId: locId,
                    bId: bId,
                    formDatas: formDatas,
                    pageNo: pageNo,
                    limit: limit
                },
                beforeSend: function () {
                    $("#detailed_tbody").html(`<td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>`);
                },
                success: function (response) {
                    // console.log(response);
                    // alert(response)

                    if (response.status) {
                        var responseObj = response.data;
                        dataPaginate = responseObj;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(-1).visible(true);
                        $.each(responseObj, function (index, value) {
                            //  $('#item_id').val(value.itemId);

                            dataTable.row.add([
                                `<p>${value.sl_no}</p>`,
                                `<p>${value.customer_discount_group}</p>`,
                                `<p>${value.created_by}</p>`,
                                `<p>${formatDate(value.created_at)}</p>`,
                                `<p>${value.updated_by}</p>`,
                                `<p>${formatDate(value.updated_at)}</p>`,

                                ` <div class="dropout">
                                     <button class="more">
                                          <span></span>
                                          <span></span>
                                          <span></span>
                                     </button>
                                     <ul>
                                     <li>
                                         <button data-toggle="modal" class="editCusdiscountMrpbtn" data-cusdisgrpname="${value.customer_discount_group}" data-cusdisgrpid="${value.customer_discount_group_id}" 
                                         data-target="#editFunctionality"><ion-icon name="create-outline" class="ion-edit"></ion-icon>Edit</button>
                                     </li>
                                    
                                     </ul>
                                   
                                 </div>`,
                            ]).draw(false);
                        });

                        $('#yourDataTable_paginate').html(response.pagination);
                        $('#limitText').html(response.limitTxt);

                        if (checkboxSettings) {
                            var checkedColumns = JSON.parse(checkboxSettings);

                            $(".settingsCheckbox_detailed").each(function (index) {
                                var columnVal = $(this).val();
                                if (checkedColumns.includes(columnVal)) {
                                    $(this).prop("checked", true);
                                    dataTable.column(index).visible(true);

                                } else {
                                    notVisibleColArr.push(index);
                                }
                            });
                            // console.log("notVisibleColArr index:", notVisibleColArr);
                            if (notVisibleColArr.length > 0) {
                                notVisibleColArr.forEach(function (index) {
                                    dataTable.column(index).visible(false);
                                });
                            }


                        } else {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function (index) {
                                if ($(this).prop("checked")) {
                                    dataTable.column(index).visible(true);

                                }
                            });
                        }
                    } else {

                        $("#detailed_tbody").html(`<td colspan=17 class='else-td not-found-td text-center'><img src="../../../public/assets/gif/no-transaction.gif" width="150" alt=""><p>No Data Found</p></td>`);
                        $('#yourDataTable_paginate').remove();
                        $('#limitText').remove();
                    }
                }
            });
        }

        fill_datatable();

        $(document).on('click', '.editCusdiscountMrpbtn', function (e) {
            var cusdisgrpname = $(this).data('cusdisgrpname');
            var cusdisgrpid = $(this).data('cusdisgrpid');
            $("#editFunctionality").modal('show');
            $("#editcusdisGrpname").val(cusdisgrpname);
            $("#editcusDisgrpid").val(cusdisgrpid);
        })


        $(document).on('click', "#update_CusDispgrpName", function (e) {
            e.preventDefault();
            let cusdisgrpname = $("#editcusdisGrpname").val();
            let cusdisgrpid = $("#editcusDisgrpid").val();
            // check confirmation
            // alert(cusdisgrpname + cusdisgrpid)
            Swal.fire({
                icon: 'warning',
                title: `Are you confirmed to edit the MRP Group Name (${cusdisgrpname})?`,
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm',
            }).then((result) => {
                if (result.isConfirmed) {
                    // send request to server
                    $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            act: 'updateCusDisGrp',
                            cusdisgrpname: cusdisgrpname,
                            cusdisgrpid: cusdisgrpid
                        },
                        url: 'ajaxs/customer/ajax-customer-discount-group-all.php',
                        beforeSend: function () {
                        },
                        success: function (response) {
                            // handel response from server
                            console.log(response);
                            // $("#approveQuotation").html(`${response.status}`);

                            // swal toast to show the response
                            let Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 1000
                            });
                            Toast.fire({
                                // show response to user
                                icon: response.status,
                                title: '&nbsp;' + response.message
                            }).then(function () {
                                $("#editFunctionality").hide();
                                fill_datatable();
                            });
                        }
                    });
                }
            });

        })

        $(document).on('click', '.addCusdiscountMrpbtn', function (e) {
            $("#funcAddForm").modal('show');
        })


        $(document).on('click', "#add_CusDispgrpName", function (e) {
            e.preventDefault();
            let cusdisGrpname = $("#addcusdisGrpname").val();
            // check confirmation
            Swal.fire({
                icon: 'warning',
                title: `Are you confirmed to add the MRP Group Name (${cusdisGrpname})?`,
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm',
            }).then((result) => {
                if (result.isConfirmed) {
                    // send request to server
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            act: 'addCusDisGrp',
                            cusdisGrpname: cusdisGrpname,
                        },
                        url: 'ajaxs/customer/ajax-customer-discount-group-all.php',
                        beforeSend: function () {
                        },
                        success: function (response) {
                            // handel response from server
                            console.log(response);
                            // $("#approveQuotation").html(`${response.status}`);

                            // swal toast to show the response
                            let Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 1000
                            });
                            Toast.fire({
                                // show response to user
                                icon: response.status,
                                title: '&nbsp;' + response.message
                            }).then(function () {
                                $("#funcAddForm").hide();
                                fill_datatable();
                            });
                        }
                    });
                }
            });

        })




        $(document).on("click", ".ion-paginationlistCusDisGroup", function (e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(dataPaginate),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieCusDiscountGroup')
                },
                // beforeSend:function(){
                //     console.log(sql_data_checkbox);
                // },

                success: function (response) {
                    var blob = new Blob([response.csvContentpage], {
                        type: 'text/csv'
                    });

                    var url = URL.createObjectURL(blob);
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = '<?= $newFileName ?>';
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);


                }
            })

        });
        $(document).on("click", ".ion-fulllistCusDisGroup", function (e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'fullliststock',
                    data: JSON.stringify(allData),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieCusDiscountGroup')
                },

                beforeSend: function () {
                },
                success: function (response) {
                    var blob = new Blob([response.csvContentall], {
                        type: 'text/csv'
                    });

                    var url = URL.createObjectURL(blob);
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = '<?= $newFileNameDownloadall ?>';
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);


                }
            })

        });



        //    ----- page length limit-----
        let formInputs = {};
        $(document).on("change", ".custom-select", function (e) {
            var maxlimit = $(this).val();
            fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit);
        });

        //    ------------ pagination-------------

        $(document).on("click", "#pagination a", function (e) {
            e.preventDefault();
            var page_id = $(this).attr('id');
            var limitDisplay = $("#cusDisGroupLimit").val();
            //    console.log(limitDisplay);
            fill_datatable(formDatas = formInputs, pageNo = page_id, limit = limitDisplay);

        });

        //<--------------advance search------------------------------->
        $(document).ready(function () {
            $(document).on("click", "#serach_submit", function (event) {
                event.preventDefault();
                let values;
                $(".selectOperator").each(function () {
                    let columnIndex = ($(this).attr("id")).split("_")[1];
                    let columnSlag = $(`#columnSlag_${columnIndex}`).val();
                    let operatorName = $(`#selectOperator_${columnIndex}`).val();
                    let value = $(`#value_${columnIndex}`).val() ?? "";
                    let value2 = $(`#value2_${columnIndex}`).val() ?? "";
                    // let value3 = $(`#value3_${columnIndex}`).val() ?? "";
                    let value4 = $(`#value4_${columnIndex}`).val() ?? "";

                    if (columnSlag === 'created_at') {
                        values = value4;
                    } else if (columnSlag === 'updated_at') {
                        values = value2;
                    }

                    if ((columnSlag === 'updated_at' || columnSlag === 'created_at') && operatorName == "BETWEEN") {
                        formInputs[columnSlag] = {
                            operatorName,
                            value: {
                                fromDate: value,
                                toDate: values
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
                // console.log("FormInputs:", formInputs);

                fill_datatable(formDatas = formInputs);
            });



        });


        // -------------checkbox----------------------

        $(document).ready(function () {
            var columnMapping = <?php echo json_encode($columnMapping); ?>;

            var indexValues = [];

            function toggleColumnVisibility(columnIndex, checkbox) {
                var column = dataTable.column(columnIndex);
                column.visible(checkbox.checked);

            }

            $("input[name='settingsCheckbox[]']").change(function () {
                var columnVal = $(this).val();
                // console.log(columnVal);

                var index = columnMapping.findIndex(function (column) {
                    return column.slag === columnVal;
                });
                // console.log(index);
                toggleColumnVisibility(index, this);
            });

            $(".grand-checkbox").on("click", function () {
                $(".colomnTable tr td input[type='checkbox']").prop("checked", this.checked);
                $("input[name='settingsCheckbox[]']").each(function () {
                    var columnVal = $(this).val();
                    // console.log(columnVal);
                    var index = columnMapping.findIndex(function (column) {
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

    $(document).ready(function () {
        $(document).on("click", "#check-box-submt", function (event) {
            // console.log("Hiiiii");
            event.preventDefault();
            // $("#myModal1").modal().hide();
            $('#btnSearchCollpase_modal').modal('hide');
            var tablename = $("#tablename").val();
            var pageTableName = $("#pageTableName").val();
            var settingsCheckbox = [];
            var fromData = {};
            $(".settingsCheckbox_detailed").each(function () {
                if ($(this).prop('checked')) {
                    var chkBox = $(this).val();
                    settingsCheckbox.push(chkBox);
                    fromData = {
                        tablename,
                        pageTableName,
                        settingsCheckbox
                    };
                }
            });

            // console.log(fromData);
            if (settingsCheckbox.length < 5) {
                alert("Please select at least 5");
            } else {
                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-save-cookies.php",
                    dataType: 'json',
                    data: {
                        act: 'mrpCustomerDiscountGroup',
                        fromData: fromData
                    },
                    success: function (response) {
                        // console.log(response);
                        Swal.fire({
                            icon: response.status,
                            title: response.message,
                            timer: 1000,
                            showConfirmButton: false,
                        })
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            }
        });



    });
</script>

<!-- -----fromDate todate input add--- -->
<script>
    $(document).ready(function () {
        $(document).on("change", ".selectOperator", function () {
            let columnIndex = parseInt(($(this).attr("id")).split("_")[1]);
            let operatorName = $(this).val();
            let columnName = $(`#columnName_${columnIndex}`).html().trim();
            let inputContainer = $(`#td_${columnIndex}`);
            let inputId;
            if (columnName === 'Created At') {
                inputId = "value4_" + columnIndex;
            } else if (columnName === 'Modified At') {
                inputId = "value2_" + columnIndex;
            }

            if ((columnName === 'Created At' || columnName === 'Modified At') && operatorName === 'BETWEEN') {
                inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
            } else {
                $(`#${inputId}`).remove();
            }
            // console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
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

</script>