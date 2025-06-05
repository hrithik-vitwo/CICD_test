<?php
include("../../app/v1/connection-branch-admin.php");
// administratorAuth();
include("../common/header.php");
include("../common/navbar.php");
include("../common/sidebar.php");
require_once("../common/pagination.php");
include("../../app/v1/functions/branch/func-mrp-controller.php");
$groupController = new MRPController();


if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["createdata"])) {
    $addGroupObj = $groupController->addMrpGroup($_POST);
    swalToast($addGroupObj["status"], $addGroupObj["message"]);
}


if (isset($_POST["editdata"])) {
    $editGroupObj = $groupController->editMrpGroup($_POST);
    swalToast($editGroupObj["status"], $editGroupObj["message"]);
}

$keywd = '';
if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
    $keywd = $_REQUEST['keyword'];
} else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
    $keywd = $_REQUEST['keyword2'];
}

?>
<link rel="stylesheet" href="../../public/assets/listing.css">


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">

                    <ul class="nav nav-tabs mb-3 border-bottom-0" id="custom-tabs-two-tab" role="tablist">
                        <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                            <h3 class="card-title">Manage Customer MRP Group</h3>
                            <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-primary float-add-btn" data-toggle="modal" data-target="#funcAddForm"><i class="fa fa-plus"></i></a>
                        </li>
                    </ul>
                    <div class="card card-tabs">
                        <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" onsubmit="return srch_frm();">

                            <div class="card-body">

                                <div class="row filter-serach-row">

                                    <div class="col-lg-2 col-md-2 col-sm-12">

                                        <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

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

                                                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary relative-add-btn" data-toggle="modal" data-target="#funcAddForm"><i class="fa fa-plus"></i></a>

                                            </div>

                                        </div>



                                    </div>

                                </div>

                            </div>

                        </form>

                        <div class="modal fade add-modal func-add-modal" id="funcAddForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                                    <input type="hidden" name="createdata" id="createdata" value="">
                                    <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">

                                    <div class="modal-content card">
                                        <div class="modal-header card-header pt-2 pb-2 px-3">
                                            <h4 class="text-xs text-white mb-0">Create Customer MRP Group</h4>
                                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                    </button> -->
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="form-input mb-3">
                                                        <label>Customer MRP Group Name* </label>
                                                        <input type="text" class="form-control" id="mrpGroupName" name="mrpGroupName" required>
                                                        <span class="error name"></span>
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

                        <div class="modal fade add-modal func-add-modal" id="editFunctionality" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                                    <input type="hidden" name="editdata" id="editdata" value="">
                                    <input type="hidden" name="id" id="editGroupId" value="">

                                    <div class="modal-content card">
                                        <div class="modal-header card-header pt-2 pb-2 px-3">
                                            <h4 class="text-xs text-white mb-0">Edit Customer MRP Group</h4>

                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="form-input mb-3">
                                                        <label>Group Name* </label>
                                                        <input type="text" class="form-control" id="editmrpGroupName" name="mrpGroupName" value="" required>
                                                        <span class="error name"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                                            <button type="submit" class="btn btn-primary update_data" value="update_post">Update</button>
                                            <!-- <button class="btn btn-primary btnstyle gradientBtn ml-2 add_data" value="add_post"><i class="fa fa-plus fontSize"></i> Final Submit</button> -->

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

                                    $cond .= " AND (`customer_mrp_group` like '%" . $_REQUEST['keyword'] . "%' )";
                                }

                                $sql_list = queryGet("SELECT * FROM `erp_customer_mrp_group` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "' AND location_id = '" . $location_id . "' ORDER BY customer_mrp_group_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ", true);

                                $num_list = $sql_list['numRows'];


                                // console($sql_list);

                                $countShow = "SELECT count(*) FROM `erp_customer_mrp_group` WHERE 1 " . $cond . " AND company_id='" . $company_id . "'  AND location_id = '" . $location_id . "'";
                                $countQry = mysqli_query($dbCon, $countShow);
                                $rowCount = mysqli_fetch_array($countQry);
                                $count = $rowCount[0];
                                $cnt = $GLOBALS['start'] + 1;
                                $settingsTable = getTableSettings(TBL_COMPANY_ADMIN_TABLESETTINGS, "erp_customer_mrp_group", $company_id);
                                $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                $settingsCheckbox = unserialize($settingsCh);
                                if ($num_list > 0) {
                                ?>
                                    <table id="mytable" class="table defaultDataTable table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                    <th>Customer MRP Group Name</th>
                                                <?php }

                                                if (in_array(2, $settingsCheckbox)) { ?>
                                                    <th>Created By</th>
                                                <?php  }
                                                if (in_array(3, $settingsCheckbox)) { ?>
                                                    <th>Created At</th>
                                                <?php  }
                                                if (in_array(4, $settingsCheckbox)) { ?>
                                                    <th>Modified By</th>
                                                <?php  }
                                                if (in_array(5, $settingsCheckbox)) { ?>
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
                                                        <td><?= $row['customer_mrp_group'] ?></td>
                                                    <?php }

                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <td><?= getCreatedByUser($row['created_by']) ?></td>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <td><?= formatDateTime($row['created_at']); ?></td>
                                                    <?php }
                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <td><?= getCreatedByUser($row['updated_by']) ?></td>
                                                    <?php }
                                                    if (in_array(5, $settingsCheckbox)) { ?>
                                                        <td><?= formatDateTime($row['updated_at']); ?></td>
                                                    <?php } ?>

                                                    <td>
                                                        <button class="btn btn-sm btn-edit" data-gid="<?= $row['customer_mrp_group_id'] ?>" data-gname="<?= $row['customer_mrp_group'] ?>"><i class="fa fa-edit po-list-icon"></i></button>
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







                        <div class="modal" id="myModal2">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Table Column Settings</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                                        <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                        <input type="hidden" name="pageTableName" value="ERP_VENDOR_DETAILS" />
                                        <div class="modal-body">
                                            <div id="dropdownframe"></div>
                                            <div id="main2">
                                                <table>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                            Customer MRP Group Name</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                            Created By </td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                            Created At</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                                            Modified By</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                                            Modified At</td>
                                                    </tr>

                                                </table>
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
        let gid = $(this).data('gid');

        let gname = $(this).data('gname');

        $('#editmrpGroupName').val(gname);
        $('#editGroupId').val(gid);


        $("#editFunctionality").modal('show');

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