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
          <a href="<?= LOCATION_URL; ?>manage-pending-grn.php?pending" class="btn"><i class="fa fa-stream mr-2"></i>Pending List</a>
          <a href="<?= LOCATION_URL; ?>manage-pending-grn.php?posting" class="btn active"><i class="fa fa-list mr-2 active"></i>Posted List</a>
        </div>
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
                            <h5 class="modal-title" id="exampleModalLongTitle">Filter Posted GRN</h5>

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

            $sts = " AND `grnStatus` !='deleted'";
            if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
              $sts = ' AND grnStatus="' . $_REQUEST['status_s'] . '"';
            }

            if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
              $cond .= " AND grnCreatedAt between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
            }

            if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
              $cond .= " AND (`vendorDocumentNo` like '%" . $_REQUEST['keyword2'] . "%' OR `vendorName` like '%" . $_REQUEST['keyword2'] . "%' OR `vendorCode` like '%" . $_REQUEST['keyword2'] . "%' OR `grnTotalAmount` like '%" . $_REQUEST['keyword2'] . "%' OR `vendorGstin` like '%" . $_REQUEST['keyword2'] . "%' OR `grnPoNumber` like '%" . $_REQUEST['keyword2'] . "%' OR `vendorDocumentNo` like '%" . $_REQUEST['keyword2'] . "%' OR `grnCreatedAt` like '%" . $_REQUEST['keyword2'] . "%')";
            } else {
              if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                $cond .= " AND (`vendorDocumentNo` like '%" . $_REQUEST['keyword'] . "%'  OR `vendorName` like '%" . $_REQUEST['keyword'] . "%' OR `vendorCode` like '%" . $_REQUEST['keyword'] . "%' OR `grnTotalAmount` like '%" . $_REQUEST['keyword'] . "%' OR `vendorGstin` like '%" . $_REQUEST['keyword'] . "%' OR `grnPoNumber` like '%" . $_REQUEST['keyword'] . "%' OR `vendorDocumentNo` like '%" . $_REQUEST['keyword'] . "%' OR `grnCreatedAt` like '%" . $_REQUEST['keyword'] . "%')";
              }
            }


            $grnListObj = queryGet('SELECT * FROM `' . ERP_GRN . '` WHERE `companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' '.$cond. $sts . ' ORDER BY grnId DESC limit ' . $GLOBALS['start'] . ',' . $GLOBALS['show'] . ' ', true);
            
            $countShow = 'SELECT count(*) FROM `' . ERP_GRN . '` WHERE `companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' '.$cond. $sts . ' ORDER BY grnId DESC';
            // console($countShow);
            $countQry = mysqli_query($dbCon, $countShow);
            $rowCount = mysqli_fetch_array($countQry);
            $count = $rowCount[0];
            // console($count);
            $cnt = $GLOBALS['start'] + 1;


            $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_GRN_POSTED", $_SESSION["logedBranchAdminInfo"]["adminId"]);
            $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
            $settingsCheckbox = unserialize($settingsCh);
            ?>
          </div>
          <table class="table defaultDataTable table-hover">
            <thead>
              <tr class="alert-light">
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
                foreach ($grnListObj["data"] as $oneGrnRow) { ?>
                  <tr>
                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                      <td><?= $oneGrnRow["vendorDocumentNo"] ?></td>
                    <?php }
                    if (in_array(2, $settingsCheckbox)) { ?>
                      <td><?= $oneGrnRow["grnPoNumber"] ?></td>
                    <?php }
                    if (in_array(3, $settingsCheckbox)) { ?>
                      <td><?= $oneGrnRow["vendorName"] ?></td>
                    <?php }
                    if (in_array(4, $settingsCheckbox)) { ?>
                      <td><?= $oneGrnRow["vendorCode"] ?></td>
                    <?php }
                    if (in_array(5, $settingsCheckbox)) { ?>
                      <td><?= $oneGrnRow["vendorGstin"] ?></td>
                    <?php }
                    if (in_array(6, $settingsCheckbox)) { ?>
                      <td><?= $oneGrnRow["grnTotalAmount"] ?></td>
                    <?php } ?>
                    <td>
                      <?php
                      if ($oneGrnRow["grnType"] == "grn") {
                      ?>
                        <a style="cursor:pointer" href="?posted=1&type=grn&view=<?= $oneGrnRow["grnId"] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                      <?php
                      } else {
                      ?>
                        <a style="cursor:pointer" href="?posted=1&type=srn&view=<?= $oneGrnRow["grnId"] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                      <?php
                      }
                      ?>

                      <?php if ($oneGrnRow['grnStatus'] == 'active') { ?>
                        <a style="cursor:pointer" data-id="<?= $oneGrnRow['grnId']; ?>" class="btn btn-sm reverseGRN" title="Reverse Now">
                          <i class="far fa-undo po-list-icon"></i>
                        </a>
                      <?php }else if ($oneGrnRow['grnStatus'] == 'reverse'){ echo 'Reversed';}else{ echo '-';} ?>

                    </td>
                  </tr>



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
                  <input type="hidden" name="pageTableName" value="ERP_GRN_POSTED" />
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

  $('.reverseGRN').click(function(e) {
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
            dep_slug: 'reverseGRN'
          },
          url: 'ajaxs/ajax-reverse-post.php',
          beforeSend: function() {
            $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
          },
          success: function(response) {
            var responseObj = JSON.parse(response);
            console.log(responseObj);

            if (responseObj.status == 'success') {
              $this.parent().parent().find('.listStatus').html('Reverse');
              $this.hide();
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