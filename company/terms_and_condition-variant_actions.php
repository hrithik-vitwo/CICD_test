<?php
require_once("../app/v1/connection-company-admin.php");
// administratorAuth();
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
require_once("common/pagination.php");
require_once("../app/v1/functions/branch/func-cost-center.php");
require_once("../app/v1/functions/company/func-company-cash-accounts.php");
// require_once("../public/ckeditor/ckeditor");
global $company_id;
global $created_by;
global $updated_by;

// $dbObj = new Database();

if (isset($_POST["submitTandC"])) {

    $tc_variant = $_POST['varient_name'];
    $tc_slug = $_POST['tcslug'];
    $tc_text = addslashes(serialize($_POST['editor1']));


    $checkslagSql = queryGet("SELECT * FROM `erp_terms_and_condition_format` WHERE tc_slug='" . $tc_slug . "'");

    if ($checkslagSql['numRows'] > 0) {
        $updateSql = queryUpdate("UPDATE `erp_terms_and_condition_format` SET `status` = 'inactive', `updated_by` = '" . $updated_by . "' WHERE `tc_slug` = '" . $tc_slug . "'");
    }
    $insert = queryInsert("INSERT INTO `erp_terms_and_condition_format` SET 
                                `company_id`='" . $company_id . "',
                                `tc_variant`='" . $tc_variant . "',
                                `tc_slug`='" . $tc_slug . "',
                                `tc_text`='" . $tc_text . "',
                                `created_by`='" . $created_by . "',
                                `updated_by`='" . $updated_by . "'");


    if ($insert["status"] == "success") {
        swalToast($insert["status"], $insert["message"]);
    } else {
        swalToast($insert["status"], $insert["message"]);
    }
}

?>

<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/sales-order.css">
<script src="../public/ckeditor/ckeditor5-build-classic/ckeditor.js"></script>
<style>
    .select2-results .btn-row a.add-btn {
        display: none;
    }

    .tick-icon {
        align-self: center;
    }

    span.select2-container.select2-container--default,
    span.select2-container.select2-container--default.select2-container--open {
        z-index: 9999;
        width: 100% !important;
    }


    .previewDiv {
        margin: 50px 18px;
        box-shadow: 0 0 5px #e6e6e6;
    }

    .div002 {
        padding: 0px 10px;
        border: 1px solid #d1d1d1;
        border-radius: 5px;
    }

    .invoice-format-card .card-header:after {
        display: none;
    }

    .sidebar-mini.sidebar-collapse .is-terms-condition .main-footer {
        margin-left: 0;
    }

    .ck.ck-powered-by {
        display: none !important;
    }
</style>

<?php
if(isset($_GET['create'])){
?>
<div class="content-wrapper is-terms-condition">
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="exampleModalContent modal-content card">
                <div class="modal-header card-header py-2 px-3">
                    <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
                    <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div id="notesModalBody" class="modal-body card-body">
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                <input type="hidden" name="createdata" value="createdata">
                <div class="row brances-create">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card terms-and-condition-card">
                            <div class="card-header p-3" style="display: flex;align-items: center;justify-content: space-between;">
                                <h4>Create Terms & Condition</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 mx-12">
                                        <div class="form-input">
                                            <label for="">Policy Title</label>
                                            <input type="text" id="variant_name" name="varient_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 mx-12">
                                        <div class="form-input">
                                            <label for="">Select Slug <span class="text-danger">*</span></label>
                                            <select name="tcslug" class="form-control" id="prefixDividerDropDown">
                                                <option value="so">Sales Order</option>
                                                <option value="po">Purchase Order</option>
                                                <option value="invoice">Invoice</option>
                                                <option value="quotation">Quotation</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 mx-12">
                                        <div class="form-input my-3">
                                            <label for="">Policy Body</label>
                                            <textarea type="textarea" id="editor1" name="editor1" class="form-control" rows="5" cols="15"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="btn-section">
                                    <button type="submit" id="submitInvFormatBtn" class="btn btn-primary save-close-btn float-right add_data mt-0" name="submitTandC" value="Submit Number">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
<?php
}

else{
    ?>


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
                <h3 class="card-title">Manage Terms And Conditions</h3>
                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
              </li>
            </ul>
            <div class="card card-tabs">
              <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">

                <div class="card-body">

                  <div class="row filter-serach-row">

                    <div class="col-lg-2 col-md-2 col-sm-12">

                      <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                    </div>

                    <div class="col-lg-10 col-md-10 col-sm-12">

                      <div class="row table-header-item">

                        <div class="col-lg-11 col-md-11 col-sm-11">

                          <div class="section serach-input-section">



                            <input type="text" id="myInput" placeholder="" class="field form-control" />

                            <div class="icons-container">

                              <div class="icon-search">

                                <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>

                              </div>

                              <div class="icon-close">

                                <i class="fa fa-search po-list-icon" onclick="javascript:alert('Hello World!')" id="myBtn"></i>

                                <script>
                                  var input = document.getElementById("myInput");

                                  input.addEventListener("keypress", function(event) {

                                    if (event.key === "Enter") {

                                      event.preventDefault();

                                      document.getElementById("myBtn").click();

                                    }

                                  });
                                </script>

                              </div>

                            </div>

                          </div>

                        </div>

                        <div class="col-lg-1 col-md-1 col-sm-1">

                          <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary relative-add-btn" ><i class="fa fa-plus"></i></a>

                        </div>

                      </div>



                    </div>

                  </div>

                </div>

              </form>

              


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
                    $cond .= " AND (`tc_text` like '%" . $_REQUEST['keyword'] . "%' OR `tc_variant` like '%" . $_REQUEST['keyword'] . "%')";
                  }

                  $sql_list = "SELECT * FROM `erp_terms_and_condition_format` WHERE 1 " . $cond . " " . $sts . " AND company_id='" . $company_id . "' ORDER BY tc_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                  $qry_list = mysqli_query($dbCon, $sql_list);
                  $num_list = mysqli_num_rows($qry_list);




                  $countShow = "SELECT count(*) FROM `" . ERP_COMPANY_FUNCTIONALITIES . "` WHERE 1 " . $cond . " AND company_id='" . $company_id . "' " . $sts . " ";
                  $countQry = mysqli_query($dbCon, $countShow);
                  $rowCount = mysqli_fetch_array($countQry);
                  $count = $rowCount[0];
                  $cnt = $GLOBALS['start'] + 1;
                  $settingsTable = getTableSettings(TBL_COMPANY_ADMIN_TABLESETTINGS, "ERP_COMPANY_FUNCTIONALITIES", $company_id);
                  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                  $settingsCheckbox = unserialize($settingsCh);
                  if ($num_list > 0) {
                  ?>
                    <table id="mytable" class="table defaultDataTable table-hover text-nowrap">
                      <thead>
                        <tr>
                          <th>#</th>
                          <?php if (in_array(1, $settingsCheckbox)) { ?>
                            <th>Policy Title</th>
                          <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                            <th>Policy Description</th>
                          <?php }
                          if (in_array(3, $settingsCheckbox)) { ?>
                            <th>Slug</th>
                          <?php  }
                          if (in_array(4, $settingsCheckbox)) { ?>
                            <th>Created By</th>
                          <?php  }
                          if (in_array(5, $settingsCheckbox)) { ?>
                            <th>Modified By</th>
                          <?php  }
                         ?>
                          <th>Status</th>
                         
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        while ($row = mysqli_fetch_assoc($qry_list)) {
                        ?>
                          <tr>
                            <td><?= $cnt++ ?></td>
                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                              <td><?= $row['tc_variant'] ?></td>
                            <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                              <td><?= substr(stripcslashes(unserialize($row['tc_text'])),0,90) ?></td>
                            <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                              <td><?= $row['tc_slug'] ?></td>
                            <?php }
                            if (in_array(4, $settingsCheckbox)) { ?>
                              <td><?= getCreatedByUser($row['created_by']); ?></td>
                            <?php }
                            if (in_array(5, $settingsCheckbox)) { ?>
                              <td><?= getCreatedByUser($row['updated_by']) ?></td>
                            <?php }
                            ?>
                            <td>
                              <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['tc_id'] ?>">
                                <input type="hidden" name="changeStatus" value="active_inactive">
                                <button <?php if ($row['status'] == "draft") { ?> type="button" style="cursor: inherit; border:none" <?php } else { ?>type="submit" onclick="return confirm('Are you sure change Status?')" style="cursor: pointer; border:none" <?php } ?> class="p-0 m-0 ml-2" data-toggle="tooltip" data-placement="top" title="<?php echo $row['status'] ?>">
                                  <?php if ($row['status'] == "active") { ?>
                                    <span class="status"><?php echo ucfirst($row['status']); ?></span>
                                  <?php } else if ($row['status'] == "inactive") { ?>
                                    <span class="status-danger"><?php echo ucfirst($row['status']); ?></span>
                                  <?php } else if ($row['status'] == "draft") { ?>
                                    <span class="status-warning"><?php echo ucfirst($row['status']); ?></span>

                                  <?php } ?>

                                </button>
                              </form>
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

                          </td>
                        </tr>
                      </thead>
                    </table>
                </div>
              <?php } ?>
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
                      <input type="hidden" name="tablename" value="<?= TBL_COMPANY_ADMIN_TABLESETTINGS; ?>" />
                      <input type="hidden" name="pageTableName" value="ERP_COMPANY_FUNCTIONALITIES" />
                      <div class="modal-body">
                        <div id="dropdownframe"></div>
                        <div id="main2">
                          <table>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                               Policy Title</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                               Policy Description </td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                Slug</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox10" value="4" />
                                Created By</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox10" value="5" />
                                Modified By</td>
                            </tr>
                            <tr>
                         
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

              <!---------------------------------Table Model End--------------------------------->

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
}

    include("common/footer.php");
    ?>

    <script>
        ClassicEditor
            .create(document.querySelector('#editor1'))
            .then(editor => {
                console.log('Editor was initialized', editor);
            })
            .catch(error => {
                console.error(error);
            });
    </script>