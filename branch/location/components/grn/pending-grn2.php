<?php

if (isset($_POST["changeStatus"])) {

  $id = $_POST["id"];
  $update = queryUpdate('UPDATE `erp_grn_multiple` SET `grn_active_status`="deactive" WHERE `grn_mul_id`="' . $id . '"');
}

?>



<style>
  div.DataTables_Table_0_length {
    display: none;
  }
</style>
<section class="content">
  <div class="container-fluid">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= LOCATION_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
      <li class="breadcrumb-item active">
        <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Pending GRN</a>
        <a href="<?php echo LOCATION_URL; ?>manage-grn-invoice.php" class="btn btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
      </li>
      <li class="back-button">
        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
          <i class="fa fa-reply po-list-icon"></i>
        </a>
      </li>
    </ol>
  </div>
  <div class="container-fluid">
    <div class="row p-0 m-0">
      <div class="col-12 mt-2 p-0">
        <div class="filter-list">
          <!-- <a href="<?= LOCATION_URL; ?>manage-pending-grn.php?all" class="btn"><i class="fa fa-stream mr-2"></i>All List</a> -->
          <a href="<?= LOCATION_URL; ?>manage-pending-grn.php?pending" class="btn active"><i class="fa fa-stream mr-2 active"></i>Pending List</a>
          <a href="<?= LOCATION_URL; ?>manage-pending-grn.php?posting" class="btn"><i class="fa fa-list mr-2"></i>Posted List</a>
        </div>
        
        <?php
            $keywd = '';
            if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
              $keywd = $_REQUEST['keyword'];
            } else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
              $keywd = $_REQUEST['keyword2'];
            } ?>

        <div class="card card-tabs" style="border-radius: 20px;">
          <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
            <div class="card-body">
              <div class="row filter-serach-row">
                <div class="col-lg-2 col-md-2 col-sm-12">
                  <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                </div>
                <div class="col-lg-10 col-md-10 col-sm-12">
                  <div class="row table-header-item">
                    <div class="col-lg-11 col-md-11 col-sm-11">
                      <div class="section serach-input-section">
                        <input type="text" name="keyword" id="myInput" placeholder="" class="field form-control" value="<?php echo $keywd; ?>">
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
                      <a href="<?php echo LOCATION_URL; ?>manage-grn-invoice.php" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                    </div>
                  </div>

                </div>

                <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Filter Pending GRN</h5>

                          </div>
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <input type="text" name="keyword2" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php /*if (isset($_REQUEST['keyword2'])) {
                               echo $_REQUEST['keyword2'];
                             } */ ?>">
                              </div>
                              <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <select name="status_s" id="status_s" class="fld form-control" style="appearance: auto;">
                                  <option value=""> Status </option>
                                  <option value="active" <?php if (isset($_REQUEST['status_s']) && 'active' == $_REQUEST['status_s']) {
                                                            echo 'selected';
                                                          } ?>>Active
                                  </option>
                                  <option value="inactive" <?php if (isset($_REQUEST['status_s']) && 'inactive' == $_REQUEST['status_s']) {
                                                              echo 'selected';
                                                            } ?>>Deactive
                                  </option>
                                </select>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-6 col-md-6 col-sm-6">
                                <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                          echo $_REQUEST['form_date_s'];
                                                                                                                        } ?>" />
                              </div>
                              <div class="col-lg-6 col-md-6 col-sm-6">
                                <input class="fld form-control" type="date" name="to_date_s" id="to_date_s" value="<?php if (isset($_REQUEST['to_date_s'])) {
                                                                                                                      echo $_REQUEST['to_date_s'];
                                                                                                                    } ?>" />
                              </div>
                            </div>

                          </div>
                          <div class="modal-footer">
                            <!-- <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync "></i>Reset</a>-->
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                              Search</button>
                          </div>
                        </div>
                      </div>
                    </div>


              </div>
            </div>
          </form>

          <div>
            <?php
            $cond = '';

            $sts = " AND `grn_active_status` !='deactive'";
            if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
              $sts = ' AND grn_active_status="' . $_REQUEST['status_s'] . '"';
            }

            if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
              $cond .= " AND created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
            }

            if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
              $cond .= " AND (`inv_no` like '%" . $_REQUEST['keyword2'] . "%' OR `vendor_name` like '%" . $_REQUEST['keyword2'] . "%' OR `vendor_code` like '%" . $_REQUEST['keyword2'] . "%' OR `total_amt` like '%" . $_REQUEST['keyword2'] . "%' OR `gst_no` like '%" . $_REQUEST['keyword2'] . "%' OR `po_no` like '%" . $_REQUEST['keyword2'] . "%' OR `inv_no` like '%" . $_REQUEST['keyword2'] . "%' OR `created_at` like '%" . $_REQUEST['keyword2'] . "%')";
            } else {
              if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                $cond .= " AND (`inv_no` like '%" . $_REQUEST['keyword'] . "%'  OR `vendor_name` like '%" . $_REQUEST['keyword'] . "%' OR `vendor_code` like '%" . $_REQUEST['keyword'] . "%' OR `total_amt` like '%" . $_REQUEST['keyword'] . "%' OR `gst_no` like '%" . $_REQUEST['keyword'] . "%' OR `po_no` like '%" . $_REQUEST['keyword'] . "%' OR `inv_no` like '%" . $_REQUEST['keyword'] . "%' OR `created_at` like '%" . $_REQUEST['keyword'] . "%')";
              }
            }

            // $grnListObj = $grnObj->getPendingGrnList();
            $grnListObj = queryGet("SELECT * FROM `erp_grn_multiple` WHERE `company_id`='" . $company_id . "' AND `branch_id`='" . $branch_id . "' AND `location_id`='" . $location_id . "' AND status = '0' ".$cond. $sts . " ORDER BY grn_mul_id DESC limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ", true);
            // console($grnListObj);

            $countShow = "SELECT count(*) FROM `erp_grn_multiple` WHERE `company_id`='" . $company_id . "' AND `branch_id`='" . $branch_id . "' AND `location_id`='" . $location_id . "' AND status = '0' ".$cond. $sts ."ORDER BY grn_mul_id DESC";
            // console($countShow);
            $countQry = mysqli_query($dbCon, $countShow);
            $rowCount = mysqli_fetch_array($countQry);
            $count = $rowCount[0];
            // console($count);
            $cnt = $GLOBALS['start'] + 1;

            $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_PENDING_DETAILS", $_SESSION["logedBranchAdminInfo"]["adminId"]);
            $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
            $settingsCheckbox = unserialize($settingsCh);
            ?>
          </div>
          <table id="dataTable" class="table defaultDataTable table-hover">
            <thead>
              <tr>
                <?php if (in_array(1, $settingsCheckbox)) { ?>
                  <th class="borderNone">Invoice Number</th>
                <?php }
                if (in_array(2, $settingsCheckbox)) { ?>
                  <th class="borderNone">PO No</th>
                <?php }
                if (in_array(3, $settingsCheckbox)) { ?>
                  <th class="borderNone">Vendor Name</th>
                <?php }
                if (in_array(4, $settingsCheckbox)) { ?>
                  <th class="borderNone">Vendor Code</th>
                <?php }
                if (in_array(5, $settingsCheckbox)) { ?>
                  <th class="borderNone">GST No</th>
                <?php }
                if (in_array(6, $settingsCheckbox)) { ?>
                  <th class="borderNone">Total Amount</th>
                <?php } ?>
                <th class="borderNone">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php

              if ($grnListObj["status"] == "success") {
                foreach ($grnListObj["data"] as $oneGrnRow) { 

                  $vendorId = $oneGrnRow["vendor_id"];

                  if($vendorId == "" || $vendorId == NULL)
                    {
                        $vendorCode = $oneGrnRow["vendor_code"];
                        $vendorName = $oneGrnRow["vendor_name"] ?? "";
                    }
                    else
                    {
                        $ven_details = queryGet("SELECT * FROM `erp_vendor_details` WHERE `company_id` = '" . $company_id . "' AND `company_branch_id`='" . $branch_id . "' AND `location_id`='" . $location_id . "' AND `vendor_id`='" . $vendorId . "'", false);
                        $vendorCode = $ven_details["data"]["vendor_code"];
                        $vendorName = $ven_details["data"]["trade_name"] ?? "";
                    }
                  $documentNo = $oneGrnRow["inv_no"];
                  $checkGrnExist = queryGet('SELECT `grnId` FROM `erp_grn` WHERE `vendorDocumentNo`="' . $documentNo . '" AND `vendorCode` ="' . $vendorCode . '" AND grnStatus="active"');
                  if ($checkGrnExist["numRows"] > 0) {
                    continue;
                  }
                  ?>
                  <tr>
                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                      <td><?= $oneGrnRow["inv_no"] ?></td>
                    <?php }
                    if (in_array(2, $settingsCheckbox)) { ?>
                      <td><?= $oneGrnRow["po_no"] ?></td>
                    <?php }
                    if (in_array(3, $settingsCheckbox)) { ?>
                      <td><?= $vendorName ?></td>
                    <?php }
                    if (in_array(4, $settingsCheckbox)) { ?>
                      <td><?= $vendorCode ?></td>
                    <?php }
                    if (in_array(5, $settingsCheckbox)) { ?>
                      <td><?= $oneGrnRow["gst_no"] ?></td>
                    <?php }
                    if (in_array(6, $settingsCheckbox)) { ?>
                      <td><?= $oneGrnRow["total_amt"] ?></td>
                    <?php } ?>
                    <td>
                      <?php
                      
                      if ($checkGrnExist["numRows"] > 0) {
                        echo "Posted";
                      } else {
                      ?>
                        <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $oneGrnRow['grn_mul_id'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                      <?php
                      }
                      ?>
                    </td>
                  </tr>

                  <div class="modal fade right customer-modal" id="fluidModalRightSuccessDemo_<?= $oneGrnRow['grn_mul_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                      <!--Content-->
                      <div class="modal-content">
                        <!--Header-->
                        <div class="modal-header">

                          <div class="customer-head-info">
                            <div class="customer-name-code">
                              <h2 style="font-size: 22px;"><span style="font-family: 'Font Awesome 5 Free';"></span><?= $oneGrnRow['vendor_name'] ?></h2>
                            </div>
                          </div>

                          <div class="display-flex-space-between mt-4 mb-3">

                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                              <li class="nav-item">
                                <a class="nav-link active" href="?type=grn&view=<?= $oneGrnRow["grn_mul_id"] ?>" aria-selected="true">GRN</a>
                              </li>
                              <li class="nav-item">
                                <a class="nav-link active" href="?type=srn&view=<?= $oneGrnRow["grn_mul_id"] ?>" aria-selected="false">SRN</a>
                              </li>

                            </ul>

                            <div class="action-btns display-flex-gap" id="">

                              <!-- <a href="?delete=<?= $oneGrnRow["grn_mul_id"] ?>"><i title="Delete" style="font-size: 1.2em" class="fa fa-trash po-list-icon"></i></a> -->

                              <form action="" method="POST" class="btn btn-sm">
                                <input type="hidden" name="id" value="<?php echo $oneGrnRow['grn_mul_id'] ?>">
                                <input type="hidden" name="changeStatus" value="delete">
                                <button title="Delete Cost Center" type="submit" onclick="return confirm('Are you sure to delete?')" class="btn btn-sm" style="cursor: pointer; border:none"><i class="fa fa-trash po-list-icon"></i></button>
                              </form>

                            </div>

                          </div>

                        </div>
                        <div class="modal-body">
                          <div class="tab-content pt-0" id="myTabContent">
                            <div class="tab-pane fade show active" id="" role="tabpanel" aria-labelledby="home-tab">

                              <form action="" method="POST">
                                <div class="hamburger">
                                  <div class="wrapper-action">
                                    <i class="fa fa-bell fa-2x"></i>
                                  </div>
                                </div>
                                <div class="nav-action" id="settings">

                                  <a title="Mail the customer" href="#" name="vendorEditBtn">
                                    <i class="fa fa-envelope"></i>
                                  </a>
                                </div>
                                <div class="nav-action" id="thumb">
                                  <a title="Chat the customer" href="#" name="vendorEditBtn">
                                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                                  </a>
                                </div>
                                <div class="nav-action" id="create">
                                  <a title="Call the customer" href="#" name="vendorEditBtn">
                                    <i class="fa fa-phone"></i>
                                  </a>
                                </div>
                              </form>


                              <!-- action btn  -->


                              <iframe src='<?= COMP_STORAGE_URL ?>/grn-invoice/<?= $oneGrnRow["uploaded_file_name"] ?>#view=fitH' id="grnInvoicePreviewIfram" width="100%" height="500"></iframe>





                              <div class="item-detail-section">
                                <!-- <h6>Items Details</h6> -->



                              </div>
                            </div>


                          </div>
                        </div>
                      </div>
                      <!--/.Content-->
                    </div>
                  </div>

                  <!-- right modal end here  -->

              <?php }
              }
              ?>

            </tbody>
            <tbody>
                        <tr>
                          <td colspan="9">
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
                      </tbody>
          </table>

          <div class="modal" id="myModal2">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">Table Column Settings</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                  <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                  <input type="hidden" name="pageTableName" value="ERP_PENDING_DETAILS" />
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
                          <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                            Invoice Number</td>
                        </tr>
                        <tr>
                          <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                            PO No </td>
                        </tr>
                        <tr>
                          <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                            Vendor Name</td>
                        </tr>
                        <tr>
                          <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                            Vendor Code</td>
                        </tr>
                        <tr>
                          <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                            GST No</td>
                        </tr>
                        <tr>
                          <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                            Total Amount</td>
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
</section>

<!-- For Pegination------->
<form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                echo  $_REQUEST['pageNo'];
                                              } ?>">
  </form>
  <!-- End Pegination from------->

<script>
  //check all
  $(document).ready(function() {
    $(".grand-checkbox").on("click", function() {

      // Check or uncheck all checkboxes within the table based on the grand checkbox state
      $(".colomnTable tr td input[type='checkbox']").prop("checked", this.checked);

    });
  });
</script>
<script>
  var table = $("#dataTable").DataTable({
    "ordering": false
  })

  function table_settings() {
    var favorite = [];
    $.each($("input[name='settingsCheckbox[]']:checked"), function() {
      favorite.push($(this).val());
    });
    var check = favorite.length;
    if (check < 5) {
      alert("Please Check Atleast 5");
      return false;
    }

  }
</script>