<?php
include("../../app/v1/connection-branch-admin.php");
// administratorAuth();
include("../common/header.php");
include("../common/navbar.php");
include("../common/sidebar.php");
require_once("../common/pagination.php");
include("../../app/v1/functions/branch/func-territory-controller.php");
$territoryController = new TerritoryController();
// echo 'byeeeeeeeeeeeeeeeeeeeeeeeeeeeeee';
// console($companyCountry);
// echo 'okayyyyyyyyyyyyyyyyyyyyyyyyy';
// exit();
if (isset($_POST["createdata"])) {
    $addObj = $territoryController->addTerritory($_POST);
    swalToast($addObj["status"], $addObj["message"]);
    // swalToast($addObj["status"], $addObj["message"],basename($_SERVER['PHP_SELF']));

}


if (isset($_POST["editdata"])) {
    $editObj = $territoryController->editTerritory($_POST);
    swalToast($editObj["status"], $editObj["message"]);
}

$keywd = '';
if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
    $keywd = $_REQUEST['keyword'];
} else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
    $keywd = $_REQUEST['keyword2'];
}

?>
<link rel="stylesheet" href="../../public/assets/listing.css">


<style>
    .is-teritory .check-dropdown.dropdown {
        position: relative;
        font-size: 14px;
        color: #333;
    }

    .is-teritory .check-dropdown.dropdown .dropdown-list {
        padding: 12px;
        background: #fff;
        position: relative;
        top: 0;
        left: 0;
        right: 0;
        box-shadow: 0 1px 2px 1px rgba(0, 0, 0, 0.15);
        transform-origin: 50% 0;
        transform: scale(1, 0);
        transition: transform 0.15s ease-in-out 0.15s;
        max-height: 66vh;
        overflow-y: scroll;
        z-index: 9;
    }

    .is-teritory .check-dropdown.dropdown .dropdown-option {
        display: block;
        padding: 8px 12px;
        opacity: 0;
        transition: opacity 0.15s ease-in-out;
    }

    .check-dropdown.dropdown .dropdown-label {
        display: block;
        height: 30px;
        background: #fff;
        border: 1px solid #ccc;
        padding: 6px 12px;
        line-height: 1;
        cursor: pointer;
    }

    .is-teritory .check-dropdown.dropdown .dropdown-label:before {
        content: ">";
        float: right;
    }

    .is-teritory .check-dropdown.dropdown.on .dropdown-list {
        transform: scale(1, 1);
        transition-delay: 0s;
    }

    .is-teritory .check-dropdown.dropdown.on .dropdown-list .dropdown-option {
        opacity: 1;
        transition-delay: 0.2s;
    }

    .is-teritory .check-dropdown.dropdown.on .dropdown-label:before {
        content: "<";
    }

    .is-teritory .check-dropdown.dropdown [type=checkbox] {
        position: relative;
        top: -1px;
        margin-right: 4px;
    }

    .modal.teritory-modal .modal-dialog {
        transform: translateY(0) !important;
    }

    .modal.teritory-modal .modal-body {
        height: auto;
        min-height: 300px;
        overflow: auto;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper is-teritory">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">

                    <ul class="nav nav-tabs mb-3 border-bottom-0" id="custom-tabs-two-tab" role="tablist">
                        <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                            <h3 class="card-title">Manage Territory</h3>
                            <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-primary float-add-btn" data-toggle="modal" data-target="#funcAddForm"><i class="fa fa-plus"></i></a>
                        </li>
                    </ul>
                    <div class="card card-tabs">
                        <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" onsubmit="return srch_frm();">

                            <div class="card-body">

                                <div class="row filter-serach-row">

                                    <div class="col-lg-2 col-md-2 col-sm-12">


                                    </div>

                                    <div class="col-lg-10 col-md-10 col-sm-12">

                                        <div class="row table-header-item">

                                            <div class="col-lg-11 col-md-11 col-sm-11">

                                                <div class="section serach-input-section">



                                                    <input type="text" id="myInput" name="keyword" value="<?= $keywd ?>" placeholder="" class="field form-control" />

                                                    <div class="icons-container">

                                                        <div class="icon-search">

                                                            <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>

                                                        </div>

                                                        <div class="icon-close">

                                                            <i class="fa fa-search po-list-icon" id="myBtn"></i>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-lg-1 col-md-1 col-sm-1">

                                                <!-- <a class="btn btn-sm btn-primary relative-add-btn" data-toggle="modal" data-target="#funcAddForm"><i class="fa fa-plus"></i></a> -->
                                                <button type="button" class="btn btn-sm btn-primary relative-add-btn create-btn" data-toggle="modal" data-target="#funcAddForm"><i class="fa fa-plus"></i></button>
                                            </div>

                                        </div>



                                    </div>

                                </div>

                            </div>

                        </form>

                        <div class="modal fade add-modal teritory-modal func-add-modal" id="funcAddForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                                    <input type="hidden" name="createdata" id="createdata" value="">
                                    <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">

                                    <div class="modal-content card">
                                        <div class="modal-header card-header pt-2 pb-2 px-3">
                                            <h4 class="text-xs text-white mb-0">Create Territory</h4>
                                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                    </button> -->
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="form-input mb-3">
                                                        <label>Territory Name* </label>
                                                        <input type="text" class="form-control" id="territoryname" name="territoryname" required>
                                                        <span class="error name"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="form-input">
                                                        <div class="dropdown check-dropdown" data-control="checkbox-dropdown">
                                                            <label class="dropdown-label">Select State</label>




                                                            <div class="dropdown-list">
                                                                <a href="#" data-toggle="check-all" class="dropdown-option">Check All</a>

                                                                <div class="statenamesadd">
                                                                    <?php
                                                                    $stateQuery = queryGet("SELECT gstStateCode,gstStateName FROM `erp_gst_state_code` WHERE `country_id` = $companyCountry", true);
                                                                    foreach ($stateQuery['data'] as $data) {
                                                                    ?>

                                                                        <label class="dropdown-option">
                                                                            <input type="checkbox" name="stateCode[]" value="<?= $data['gstStateCode'] ?>">
                                                                            <?php echo $data['gstStateName'] . '-' . $data['gstStateCode']; ?>
                                                                        </label>

                                                                    <?php } ?>

                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary add_data" value="add_post">Submit</button>

                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>




                        <!-- edit modal start  -->





                        <div class="modal fade add-modal teritory-modal func-add-modal" id="editFunctionality" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                                    <input type="hidden" name="editdata" id="editdata" value="">
                                    <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">
                                    <input type="hidden" name="territoryid" id="territoryid" value="">

                                    <div class="modal-content card">
                                        <div class="modal-header card-header pt-2 pb-2 px-3">
                                            <h4 class="text-xs text-white mb-0">Edit Territory</h4>
                                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                    </button> -->
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="form-input mb-3">
                                                        <label>Territory Name* </label>
                                                        <input type="text" class="form-control" id="territorynameedit" name="territoryname" required>
                                                        <span class="error name"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="form-input">
                                                        <div class="dropdown check-dropdown" data-control="checkbox-dropdown">
                                                            <label class="dropdown-label">Select State</label>




                                                            <div class="dropdown-list">
                                                                <a href="#" data-toggle="check-all" class="dropdown-option">Check All</a>
                                                                <div class="statenamesedit"></div>


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary add_data" value="add_post">Update</button>

                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                        <!-- edit modal end -->


                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                <?php
                                $cond = '';
                                $sts = " AND `status` !='deleted'";
                                if (isset($_REQUEST['status']) && $_REQUEST['status'] != '') {
                                    $sts = ' AND status="' . $_REQUEST['status'] . '"';
                                }

                                if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                    $cond .= " AND created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                }

                                if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {

                                    $cond .= " AND (`territory_name` like '%" . $_REQUEST['keyword'] . "%'  OR `state_codes` like '%" . $_REQUEST['keyword'] . "%')";
                                }

                                $sql_list = queryGet("SELECT * FROM `erp_mrp_territory`  WHERE 1 " . $cond . "  AND company_id='" . $company_id . "' AND location_id = '" . $location_id . "' ORDER BY `created_at` DESC", true);
                                $num_list = $sql_list['numRows'];


                                // console($sql_list);

                                $countShow = "SELECT count(*) FROM `erp_mrp_territory` WHERE 1 " . $cond . " AND company_id='" . $company_id . "'  AND location_id = '" . $location_id . "'";
                                $countQry = mysqli_query($dbCon, $countShow);
                                $rowCount = mysqli_fetch_array($countQry);
                                $count = $rowCount[0];
                                $cnt = $GLOBALS['start'] + 1;
                                // $settingsTable = getTableSettings(TBL_COMPANY_ADMIN_TABLESETTINGS, "ERP_MRP_TERITARY", $company_id);
                                $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_MRP_TERITARY", $_SESSION["logedBranchAdminInfo"]["adminId"]);

                                $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                $settingsCheckbox = unserialize($settingsCh);
                                if ($num_list > 0) {
                                ?>
                                    <table id="mytable" class="table defaultDataTable table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                    <th>Territory Name</th>
                                                <?php }

                                                if (in_array(2, $settingsCheckbox)) { ?>
                                                    <th>State Code</th>
                                                <?php  }
                                                if (in_array(3, $settingsCheckbox)) { ?>
                                                    <th>Created By</th>
                                                <?php  }
                                                if (in_array(4, $settingsCheckbox)) { ?>
                                                    <th>Created At</th>
                                                <?php  }
                                                if (in_array(5, $settingsCheckbox)) { ?>
                                                    <th>Modified By</th>
                                                <?php  }
                                                if (in_array(6, $settingsCheckbox)) { ?>
                                                    <th>Modified At</th>
                                                <?php } ?>

                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($sql_list['data'] as $row) {
                                            ?>
                                                <tr>
                                                    <td><?= $cnt++ ?></td>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <td><?= $row['territory_name'] ?></td>
                                                    <?php }

                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <td>
                                                            <p class="pre-normal"><?php

                                                                                    $stateCodes = unserialize($row['state_codes']);
                                                                                    // Using a for loop
                                                                                    $array_length = count($stateCodes);


                                                                                    for ($i = 0; $i < $array_length; $i++) {
                                                                                        $sqlSname = queryGet("SELECT gstStateName FROM `erp_gst_state_code` WHERE gstStateCode='" . $stateCodes[$i] . "';");

                                                                                        $comma = ', ';
                                                                                        if ($array_length <= 1 or $i == $array_length - 1) {
                                                                                            $comma = '';
                                                                                        }

                                                                                        echo $sqlSname['data']['gstStateName'] . $comma;
                                                                                    }

                                                                                    ?></p>
                                                        </td>
                                                    <?php }

                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <td><?= getCreatedByUser($row['created_by']) ?></td>
                                                    <?php }
                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <td><?= formatDateTime($row['created_at']); ?></td>
                                                    <?php }
                                                    if (in_array(5, $settingsCheckbox)) { ?>
                                                        <td><?= getCreatedByUser($row['update_by']) ?></td>
                                                    <?php }
                                                    if (in_array(6, $settingsCheckbox)) { ?>
                                                        <td><?= formatDateTime($row['updated_at']); ?></td>
                                                    <?php } ?>

                                                    <td>
                                                        <button class="btn btn-sm btn-edit" data-tid="<?= $row['territory_id'] ?>" data-tname="<?= $row['territory_name'] ?>"><i class="fa fa-edit po-list-icon"></i></button>
                                                    </td>
                                                </tr>
                                            <?php  } ?>
                                        <tfoot>
                                            <tr>
                                                <td colspan="8">
                                                    <!-- Start .pagination -->

                                                    <?php
                                                    if ($count > 0 && $count > $GLOBALS['show']) {
                                                    ?>
                                                        <div class="pagination align-right">
                                                            <?php pagination($count, "frm_opts"); ?>
                                                        </div>

                                                        <!-- End .pagination -->

                                                    <?php  } ?>

                                                    <!-- End .pagination -->
                                                </td>
                                            </tr>
                                        </tfoot>
                                        </tbody>

                                    </table>
                                <?php } else { ?>
                                    <table id="mytable" class="table defaultDataTable table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <td>
                                                    No Data Found
                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                            </div>
                        <?php } ?>







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
                                                    echo  $_REQUEST['pageNo'];
                                                } ?>">
</form>
<!-- End Pegination from------->

<?php
include("../common/footer.php");
?>

<script>
    // $(document).ready(function() {
    //     (function($) {
    //         var CheckboxDropdown = function(el) {
    //             var _this = this;
    //             this.isOpen = false;
    //             this.areAllChecked = false;
    //             this.$el = $(el);
    //             this.$label = this.$el.find(".dropdown-label");
    //             this.$checkAll = this.$el.find('[data-toggle="check-all"]').first();
    //             this.$inputs = this.$el.find('[type="checkbox"]');

    //             this.onCheckBox();

    //             this.$label.on("click", function(e) {
    //                 e.preventDefault();
    //                 _this.toggleOpen();
    //             });

    //             this.$checkAll.on("click", function(e) {
    //                 e.preventDefault();
    //                 _this.onCheckAll();
    //             });

    //             this.$inputs.on("change", function(e) {
    //                 _this.onCheckBox();
    //             });
    //         };

    //         CheckboxDropdown.prototype.onCheckBox = function() {
    //             this.updateStatus();
    //         };

    //         CheckboxDropdown.prototype.updateStatus = function() {
    //             var checked = this.$el.find(":checked");

    //             this.areAllChecked = false;
    //             this.$checkAll.html("Check All");

    //             if (checked.length <= 0) {
    //                 this.$label.html("Select State");
    //             } else if (checked.length === 1) {
    //                 this.$label.html(checked.parent("label").text());
    //             } else if (checked.length === this.$inputs.length) {
    //                 this.$label.html("All Selected");
    //                 this.areAllChecked = true;
    //                 this.$checkAll.html("Uncheck All");
    //             } else {
    //                 this.$label.html(checked.length + " Selected");
    //             }
    //         };

    //         CheckboxDropdown.prototype.onCheckAll = function(checkAll) {
    //             if (!this.areAllChecked || checkAll) {
    //                 this.areAllChecked = true;
    //                 this.$checkAll.html("Uncheck All");
    //                 this.$inputs.prop("checked", true);
    //             } else {
    //                 this.areAllChecked = false;
    //                 this.$checkAll.html("Check All");
    //                 this.$inputs.prop("checked", false);
    //             }

    //             this.updateStatus();
    //         };

    //         CheckboxDropdown.prototype.toggleOpen = function(forceOpen) {
    //             var _this = this;

    //             if (!this.isOpen || forceOpen) {
    //                 this.isOpen = true;
    //                 this.$el.addClass("on");
    //                 $(document).on("click", function(e) {
    //                     if (!$(e.target).closest("[data-control]").length) {
    //                         _this.toggleOpen();
    //                     }
    //                 });
    //             } else {
    //                 this.isOpen = false;
    //                 this.$el.removeClass("on");
    //                 $(document).off("click");
    //             }
    //         };

    //         var checkboxesDropdowns = document.querySelectorAll(
    //             '[data-control="checkbox-dropdown"]'
    //         );
    //         for (var i = 0, length = checkboxesDropdowns.length; i < length; i++) {
    //             new CheckboxDropdown(checkboxesDropdowns[i]);
    //         }
    //     })(jQuery);
    // })

    // Define a function to initialize the CheckboxDropdown
    
        function initializeCheckboxDropdown() {
            var CheckboxDropdown = function(el) {
                var _this = this;
                this.isOpen = false;
                this.areAllChecked = false;
                this.$el = $(el);
                this.$label = this.$el.find(".dropdown-label");
                this.$checkAll = this.$el.find('[data-toggle="check-all"]').first();
                this.$inputs = this.$el.find('[type="checkbox"]');

                this.onCheckBox();

                this.$label.on("click", function(e) {
                    e.preventDefault();
                    _this.toggleOpen();
                });

                this.$checkAll.on("click", function(e) {
                    e.preventDefault();
                    _this.onCheckAll();
                });

                this.$inputs.on("change", function(e) {
                    _this.onCheckBox();
                });
            };

            CheckboxDropdown.prototype.onCheckBox = function() {
                this.updateStatus();
            };

            CheckboxDropdown.prototype.updateStatus = function() {
                var checked = this.$el.find(":checked");

                this.areAllChecked = false;
                this.$checkAll.html("Check All");

                if (checked.length <= 0) {
                    this.$label.html("Select State");
                } else if (checked.length === 1) {
                    this.$label.html(checked.parent("label").text());
                } else if (checked.length === this.$inputs.length) {
                    this.$label.html("All Selected");
                    this.areAllChecked = true;
                    this.$checkAll.html("Uncheck All");
                } else {
                    this.$label.html(checked.length + " Selected");
                }
            };

            CheckboxDropdown.prototype.onCheckAll = function(checkAll) {
                if (!this.areAllChecked || checkAll) {
                    this.areAllChecked = true;
                    this.$checkAll.html("Uncheck All");
                    this.$inputs.prop("checked", true);
                } else {
                    this.areAllChecked = false;
                    this.$checkAll.html("Check All");
                    this.$inputs.prop("checked", false);
                }

                this.updateStatus();
            };

            CheckboxDropdown.prototype.toggleOpen = function(forceOpen) {
                var _this = this;

                if (!this.isOpen || forceOpen) {
                    this.isOpen = true;
                    this.$el.addClass("on");
                    $(document).on("click", function(e) {
                        if (!$(e.target).closest("[data-control]").length) {
                            _this.toggleOpen();
                        }
                    });
                } else {
                    this.isOpen = false;
                    this.$el.removeClass("on");
                    $(document).off("click");
                }
            };

            // Apply CheckboxDropdown to elements with the attribute 'data-control="checkbox-dropdown"'
            var checkboxesDropdowns = document.querySelectorAll(
                '[data-control="checkbox-dropdown"]'
            );
            for (var i = 0, length = checkboxesDropdowns.length; i < length; i++) {
                new CheckboxDropdown(checkboxesDropdowns[i]);
            }
        }
 


    // Call the function to initialize CheckboxDropdown initially
    initializeCheckboxDropdown();

   
</script>

<script>
    var input = document.getElementById("myInput");
    input.addEventListener("keypress", function(event) {
        // console.log(event.key)

        if (event.key === "Enter") {
            event.preventDefault();
            // alert("clicked")
            document.getElementById("myBtn").click();
        }
    });
    var form = document.getElementById("search");

    document.getElementById("myBtn").addEventListener("click", function() {
        form.submit();
    });
</script>

<script>
    $('.btn-edit').on('click', function() {
        let tid = $(this).data('tid');
        let tname = $(this).data('tname');

        $('#territorynameedit').val(tname);
        $('#territoryid').val(tid);



        $.ajax({
            type: "GET",
            url: `ajaxs/territory/ajax-old-territoryname-edit.php`,
            data: {
                act: "oldterritoryname",
                territoryid: tid
            },
            beforeSend: function() {},
            success: function(response) {
                //alert(1);
                // console.log(response);
                $('.statenamesedit').html(response);
            initializeCheckboxDropdown();
            }
        });







        $("#editFunctionality").modal('show');

    });
    $('.create-btn').on('click', function() {


        $("#funcAddForm").modal('show');

    });






    $('.m-input').on('keyup', function() {
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
        $.each($("input[name='settingsCheckbox[]']:checked"), function() {
            favorite.push($(this).val());
        });
        var check = favorite.length;
        if (check < 5) {
            alert("Please Check Atlast 5");
            return false;
        }

    }


    $(document).on("click", "#btnSearchCollpase", function() {
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