<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

require_once("../../app/v1/functions/branch/func-discount-controller.php");


//console($_SESSION);
//console($_SESSION['logedBranchAdminInfo']['fldAdminBranchId']);
//console(date("Y-m-d H:i:s"));
$discountController = new CustomerDiscountGroupController();

// if (isset($_POST["changeStatus"])) {
//   $newStatusObj = ChangeStatus($_POST, "fldAdminKey", "fldAdminStatus");
//   swalToast($newStatusObj["status"], $newStatusObj["message"]);
// }


// if (isset($_POST["create"])) {
//   $addNewObj = createData($_POST + $_FILES);
//   swalToast($addNewObj["status"], $addNewObj["message"]);
// }

// if (isset($_POST["edit"])) { 
//   $editDataObj = updateData($_POST);

//   swalToast($editDataObj["status"], $editDataObj["message"]);
// }

if (isset($_POST["createCoupon"])) {



  $addNewObj = $discountController->createCoupon($_POST);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}

if (isset($_POST["editCoupon"])) {

  //console($_SESSION);
  $addNewObj = $discountController->editCoupon($_POST);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}


// if (isset($_POST["editgoodsdata"])) {
//   $addNewObj = $warehouseController->editGoods($_POST);
//   swalToast($addNewObj["status"], $addNewObj["message"]);
// }

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["itemId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
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
          <div class="p-0 pt-1 my-2">
            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
              <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                <h3 class="card-title">Manage Coupon</h3>
                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary relative-add-btn" data-toggle="modal" data-target="#funcAddForm"><i class="fa fa-plus"></i></a>
              </li>
            </ul>
          </div>
          <div class="card card-tabs" style="border-radius: 20px;">
            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
              <div class="card-body">
                <div class="row filter-serach-row">
                  <div class="col-lg-2 col-md-2 col-sm-12">
                    <!-- <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a> -->
                  </div>
                  <div class="col-lg-10 col-md-10 col-sm-12">
                    <div class="section serach-input-section">

                      <div class="collapsible-content">
                        <div class="filter-col">

                          <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-2">
                              <div class="input-group-manage-vendor">
                                <select name="vendor_status_s" id="vendor_status_s" class="form-control">
                                  <option value="">--- Status --</option>
                                  <option value="active" <?php if (isset($_REQUEST['vendor_status_s']) && 'active' == $_REQUEST['vendor_status_s']) {
                                                            echo 'selected';
                                                          } ?>>Active</option>
                                  <option value="inactive" <?php if (isset($_REQUEST['vendor_status_s']) && 'inactive' == $_REQUEST['vendor_status_s']) {
                                                              echo 'selected';
                                                            } ?>>Inactive</option>
                                  <option value="draft" <?php if (isset($_REQUEST['vendor_status_s']) && 'draft' == $_REQUEST['vendor_status_s']) {
                                                          echo 'selected';
                                                        } ?>>Draft</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2">
                              <div class="input-group-manage-vendor"> <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                                echo $_REQUEST['form_date_s'];
                                                                                                                                                              } ?>" />
                              </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2">
                              <div class="input-group-manage-vendor"> <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                                echo $_REQUEST['form_date_s'];
                                                                                                                                                              } ?>" />
                              </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2">
                              <div class="input-group-manage-vendor">
                                <input type="text" name="keyword" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php if (isset($_REQUEST['keyword'])) {
                                                                                                                                                      echo $_REQUEST['keyword'];
                                                                                                                                                    } ?>">
                              </div>
                            </div>


                            <div class="col-lg-2 col-md-2 col-sm-2">
                              <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2">
                              <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger">Reset</a>
                            </div>
                          </div>






                        </div>
                      </div>
                      <button type="button" class="collapsible btn-search-collpase" id="btnSearchCollpase">
                        <i class="fa fa-search po-list-icon"></i>
                      </button>
                    </div>

                  </div>
                </div>

            </form>





            <div class="modal fade add-modal func-add-modal" id="funcAddForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                  <input type="hidden" name="createCoupon" id="createCoupon" value="">
                  <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">

                  <div class="modal-content card">
                    <div class="modal-header card-header pt-2 pb-2 px-3">
                      <h4 class="text-xs text-white mb-0">Create Coupon</h4>
                      <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                    </button> -->
                    </div>
                    <div class="modal-body">
                      <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="form-input mb-3">
                            <label>Coupon Code* </label>
                            <input type="text" class="form-control" id="coupon_code" name="coupon_code" data-attr="create" required>
                            <span class="error coupon_code"></span>
                          </div>
                          <div class="form-input">
                            <label>Coupon Serial No*</label>
                            <input type="text" name="coupon_serial" class="form-control" id="coupon_serial" value="" required>
                            <span class="error coupon_serial_error"></span>
                          </div>

                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary add_data coupon_add_btn" value="add_post">Submit</button>

                    </div>
                  </div>

                </form>
              </div>
            </div>







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

                $sql_list = queryGet("SELECT * FROM `erp_discount_coupon`  WHERE 1 AND`company_id`=$company_id AND `branch_id`=$branch_id AND`location_id`=$location_id  ORDER BY discount_coupon_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ", true);

                //  console($sql_list);


                //AND  layer.'warehouse_id'=warehouse.'warehouse_id' 
                //as sl ,".ERP_WAREHOUSE." as warehouse
                $countShow = "SELECT COUNT(*) FROM `erp_discount_coupon` WHERE `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id  ";
                $countQry = mysqli_query($dbCon, $countShow);
                $rowCount = mysqli_fetch_array($countQry);
                $count = $rowCount[0];
                $cnt = $GLOBALS['start'] + 1;
                $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_COUPON", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                $settingsCheckbox = unserialize($settingsCh);
                if ($sql_list['numRows'] > 0) { ?>
                  <table class="table defaultDataTable table-hover text-nowrap p-0 m-0">
                    <thead>
                      <tr class="alert-light">
                        <th>#</th>
                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                          <th>Coupon Serial</th>
                        <?php }
                        if (in_array(2, $settingsCheckbox)) { ?>
                          <th>Coupon Code</th>
                        <?php }
                        if (in_array(3, $settingsCheckbox)) { ?>
                          <th>Status</th>
                        <?php  }
                        if (in_array(4, $settingsCheckbox)) { ?>
                          <th>Created By</th>
                        <?php }

                        if (in_array(5, $settingsCheckbox)) { ?>
                          <th>Created At</th>
                        <?php }

                        ?>

                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $customerModalHtml = "";
                      foreach ($sql_list['data'] as $row) {

                      ?>
                        <tr>
                          <td><?= $cnt++ ?></td>
                          <?php if (in_array(1, $settingsCheckbox)) { ?>
                            <td><?= $row['discount_coupon_serial'] ?></td>
                          <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                            <td><?= $row['discount_coupon_code'] ?></td>
                          <?php }
                          if (in_array(3, $settingsCheckbox)) { ?>
                            <td><?= $row['status'] ?></td> <?php }
                                                          if (in_array(4, $settingsCheckbox)) { ?>
                            <td><?= getCreatedByUser($row['created_by']) ?></td>
                          <?php }

                                                          if (in_array(5, $settingsCheckbox)) { ?>
                            <td><?= formatDateORDateTime($row['created_at']) ?></td>
                          <?php }

                          ?>

                          <td>


                            <a href="<?= basename($_SERVER['PHP_SELF']) . "?edit=" . $row['discount_coupon_id']; ?>" style="cursor: pointer;" class="btn btn-sm" data-toggle="modal" data-target="#editFunctionality_<?= $row['discount_coupon_id'] ?>" title="Edit Branch"><i class="fa fa-edit po-list-icon"></i></a>


                            <div class="modal fade edit-modal func-edit-modal" id="editFunctionality_<?= $row['discount_coupon_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="edit_frm" name="edit_frm">
                                  <input type="hidden" name="editCoupon" id="editCoupon" value="">
                                  <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">
                                  <input type="hidden" name="id" id="id" value="<?= $row['discount_coupon_id'] ?>">
                                  <input type="hidden" name="coupon_serial_hidden" id="coupon_serial_hidden" value="<?= $row['discount_coupon_serial'] ?>">

                                  <div class="modal-content card">
                                    <div class="modal-header card-header pt-2 pb-2 px-3">
                                      <h4 class="text-xs text-white mb-0">Create Coupon</h4>

                                    </div>
                                    <div class="modal-body">
                                      <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                          <div class="form-input mb-3">
                                            <label>Coupon Code* </label>
                                            <input type="text" class="form-control" id="coupon_code" name="coupon_code" required value="<?= $row['discount_coupon_code'] ?>">
                                            <span class="error coupon_code"></span>
                                          </div>
                                          <div class="form-input">
                                            <label>Coupon Serial No*</label>
                                            <input type="text" name="coupon_serial" class="form-control" id="" value="<?= $row['discount_coupon_serial'] ?>" data-attr="edit" required>
                                            <span class="error coupon_serial"></span>
                                          </div>

                                        </div>
                                      </div>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="submit" class="btn btn-primary edit_data" value="edit_post">Submit</button>

                                    </div>
                                  </div>

                                </form>
                              </div>
                            </div>



                          </td>
                        </tr>

                      <?php } ?>

                    </tbody>

                  </table>



                  <?php
                  if ($count > 0 && $count > $GLOBALS['show']) {
                  ?>
                    <div class="pagination align-right">
                      <?php pagination($count, "frm_opts"); ?>
                    </div>

                    <!-- End .pagination -->

                  <?php  } ?>

                <?php } else { ?>
                  <table class="table defaultDataTable table-hover text-nowrap">
                    <thead>
                      <tr>
                        <td>

                        </td>
                      </tr>
                    </thead>
                  </table>
              </div>
            <?php } ?>
            </div>
            <?= $customerModalHtml ?>


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

require_once("../common/footer.php");
?>
<script>
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



    $("#coupon_serial").keyup(function() {

      // alert(sl);
      var attr = $(this).data('attr');

      if (attr == 'edit') {
        var sl = $('#coupon_serial_hidden').val();
      } else {
        var sl = $(this).val();
      }


      $.ajax({
        type: "POST",
        url: `ajaxs/discount/ajax-coupon-serial.php`,
        data: {
          sl
        },

        beforeSend: function() {
          //$("#warehouseDropDown").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          alert(response);
          if (response > 0) {
            $(".coupon_serial_error").html('Duplicate Serial Code');
            $(".coupon_add_btn").prop("disabled", true);

          } else {
            $(".coupon_serial_error").html('');
            $(".coupon_add_btn").prop("disabled", false);

          }
        }
      });


    });


    // $('#warehouseDropDown')
    //   .select2()
    //   .on('select2:open', () => {
    //     //$(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewGoodTypesFormModal">Add New</a></div>`);
    //   });

    // $("#warehouseDropDown").change(function() {
    //   let dataAttrVal = $("#warehouseDropDown").find(':selected').data('goodtype');
    //   if (dataAttrVal == "RM") {
    //     $("#bomCheckBoxDiv").html("");
    //   } else if (dataAttrVal == "SFG") {
    //     $("#bomCheckBoxDiv").html(`<input type="checkbox" name="bomRequired"style="width: auto; margin-bottom: 0;" checked>Required BOM`);

    //   } else {
    //     $("#bomCheckBoxDiv").html(`<input type="checkbox" name="bomRequired"style="width: auto; margin-bottom: 0;">Required BOM`);
    //   }
    // });

    //**************************************************************
    $('#goodGroupDropDown')
      .select2()
      .on('select2:open', () => {
        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewGoodGroupFormModal">Add New</a></div>`);
      });

    $('#purchaseGroupDropDown')
      .select2()
      .on('select2:open', () => {
        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewPurchaseGroupFormModal">Add New</a></div>`);
      });

    $('#warehouseDropDown')
      .select2()
      .on('select2:open', () => {
        // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
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
    // function loadWarehouse() {
    //   $.ajax({
    //     type: "GET",
    //     url: `ajaxs/warehouse/ajax-warehouse.php`,
    //     beforeSend: function() {
    //       $("#warehouseDropDown").html(`<option value="">Loding...</option>`);
    //     },
    //     success: function(response) {
    //       $("#warehouseDropDown").html(response);
    //     }
    //   });
    // }



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


    $(".add_data").click(function() {
      var data = this.value;
      $("#createCoupon").val(data);
      //confirm('Are you sure to Submit?')
      $("#SubmitForm").submit();
    });


    $(".edit_data").click(function() {
      var data = this.value;
      $("#editStorageLocation").val(data);
      //confirm('Are you sure to Submit?')
      $("#Edit_data").submit();
    });


    //volume calculation
    function calculate_volume() {
      let height = $("#height").val();
      let width = $("#width").val();
      let length = $("#length").val();
      let res = height * length * width;
      let resm = res * 0.000001;
      console.log(res);
      $("#volcm").val(res);
      $("#volm").val(resm);


    }

    // $(document).on("keyup", ".calculate_volume", function(){
    //  calculate_volume();
    // });

    $("#height").keyup(function() {
      calculate_volume();
    });
    $("#width").keyup(function() {
      calculate_volume();
    });
    $("#length").keyup(function() {
      calculate_volume();
    });


    $("#buomDrop").change(function() {
      let res = $(this).val();
      $("#buom").val(res);
      console.log("buomDrop", res);
    });

    $("#iuomDrop").change(function() {
      let rel = $(this).val();
      $("#ioum").val(rel);
      console.log("iuomDrop", rel);
    });



  });
</script>