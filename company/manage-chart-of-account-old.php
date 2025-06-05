<?php
include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
include("../app/v1/functions/company/func-ChartOfAccounts-old.php");
include("../app/v1/functions/admin/func-company.php");
$company_data = getCompanyDataDetails($company_id);
$queryGetNumRows = queryGetNumRows("SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id");
// console($queryGetNumRows);
if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusChartOfAccounts($_POST, "customer_id", "customer_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"]);
}


if (isset($_POST["createdata"])) {
  $addNewObj = createDataChartOfAccounts($_POST);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}

if (isset($_GET["import"]) && $_GET["import"] == "importDefault") {
  if ($queryGetNumRows['numRows'] == 4) {
    $importNewObj = importDefaltChartOfAccounts();
    // console($importNewObj);
    ///swalToast($addNewObj["status"], $addNewObj["message"]);
    redirect($_SERVER['PHP_SELF']);
    exit;
  }
}

if (isset($_POST["editdata"])) {
  $editDataObj = updateDataChartOfAccounts($_POST);
  // console($editDataObj);

  swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}
?>
<style>
  .btn-transparent {
    position: absolute;
    top: 23px;
    left: 9px;
    height: 35px;
    z-index: 9;
    width: 92%;
    background: transparent !important;
  }

  body.sidebar-mini.layout-fixed.sidebar-collapse p.gl-code {
    position: absolute;
    right: 32em;
    top: 5px;
    z-index: 999;
    width: 100px;
    transition-delay: 0.2s;
  }

  body.sidebar-mini.layout-fixed p.gl-code {
    position: absolute;
    right: 22em;
    top: 5px;
    z-index: 999;
    width: 100px;
    transition-delay: 0.2s;
  }

  .gl-filter-list.filter-list {
    display: flex;
    gap: 7px;
    justify-content: flex-end;
    position: relative;
    top: 53px;
    left: -20px;
  }

  .gl-filter-list.filter-list a.active {
    background-color: #003060;
    color: #fff;
  }

  .preview-default-coa-modal .modal-dialog {
    max-width: 85%;
    min-height: 80%;
  }

  .preview-default-coa-modal .modal-content {
    min-height: 80vh;
  }

  .preview-default-coa-modal .modal-content .modal-body {
    height: 400px;
  }

</style>

<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/sales-order.css">
<link rel="stylesheet" href="../public/assets/accordion.css">
<link rel="stylesheet" href="../public/assets/tree-table-2/tree-table-2.css">
<?php
if (isset($_GET['create']) && ($_GET['create'] == 'group' || $_GET['create'] == 'account')) {
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <?php if (isset($msg)) { ?>
        <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
          <?= $msg ?>
        </div>
      <?php } ?>
      <div class="container-fluid">

        <ol class="breadcrumb">

          <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

          <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Chart of Account</a></li>

          <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Add Chart of Account</a></li>

          <li class="back-button">

            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">

              <i class="fa fa-reply po-list-icon"></i>

            </a>

          </li>

        </ol>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">

          <div class="col-lg-6 col-md-12 col-sm-12">

            <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

              <div class="card-header">

                <h4>Add Group</h4>

              </div>

              <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">
                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_group_frm" name="add_group_frm">
                  <input type="hidden" name="createdata" id="createdata" value="group">
                  <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>">
                  <div class="row">

                    <div class="col-lg-12 col-md-12 col-sm-12">

                      <div class="row goods-info-form-view customer-info-form-view">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="form-input">

                            <label for="">Select Parent*</label>

                            <!-- <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#accountDropdown1"></button> -->

                            <select id="gp_id" name="p_id" class="form-control form-control-border borderColor" required>
                              <option value="">Select Parent*</option>
                              <?php
                              $listResult = getAllChartOfAccounts_listGroup($company_id);
                              if ($listResult["status"] == "success") {
                                foreach ($listResult["data"] as $listRow) {
                              ?>

                                  <option value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label']; ?>
                                  </option>
                              <?php }
                              } ?>
                            </select>
                            <input type="hidden" name="gl_code" id="ggl_code" value="">
                            <input type="hidden" name="personal_glcode_lvl" id="gpersonal_glcode_lvl" value="">
                            <input type="hidden" name="typeAcc" id="gpersonal_typeAcc" value="">
                            <input type="hidden" class="form-control" id="ggl_code_preview" name="gl_code_preview" readonly required>
                          </div>

                          <span class="error " id="gp_id_error"></span>

                          <!-- <div class="modal fade" id="accountDropdown1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content" style="max-height: 600px;">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Select Parent*</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body p-2">

                                  <?php echo createGlTreeAddform(); ?>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                  <button type="button" class="btn btn-primary">Save changes</button>
                                </div>
                              </div>
                            </div>
                          </div> -->

                        </div>


                        <div class="col-lg-6 col-md-6 col-sm-6">

                          <div class="form-input">

                            <label for="">Group Name</label>

                            <input type="text" class="form-control" id="gl_label" name="gl_label" required>
                            <span class="error"></span>

                          </div>

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">

                          <div class="form-input">

                            <label for="">Group Description</label>

                            <input type="text" class="form-control" id="remark" name="remark">

                            <span class="error"></span>

                          </div>

                        </div>
                        <div class="btn-section mt-2 mb-2 ml-auto">
                          <button class="btn btn-primary save-close-btn float-right add_data waves-effect waves-light" value="add_post">Create Group</button>
                        </div>



                      </div>

                    </div>

                  </div>
                </form>

              </div>

            </div>

          </div>

          <div class="col-lg-6 col-md-12 col-sm-12">

            <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

              <div class="card-header">

                <h4>Add Account</h4>

              </div>

              <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_account_frm" name="add_account_frm">
                  <input type="hidden" name="createdata" id="createdata" value="account">
                  <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>">
                  <div class="row">

                    <div class="col-lg-12 col-md-12 col-sm-12">

                      <div class="row goods-info-form-view customer-info-form-view">

                        <div class="col-lg-6 col-md-6 col-sm-12">

                          <div class="form-input">

                            <label for="">Select Parent*</label>

                            <!-- <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#accountDropdown"></button> -->

                            <select id="ap_id" name="p_id" class="form-control form-control-border borderColor mt-0" required>
                              <option value="">Select Parent*</option>
                              <?php
                              $coaSql = "SELECT coaTable.* FROM
                              " . ERP_ACC_CHART_OF_ACCOUNTS . " coaTable
                                WHERE
                                    coaTable.company_id = $company_id 
                                    AND coaTable.glStType = 'group'
                                    AND coaTable.id NOT IN (
                                        SELECT
                                          innerTable.`p_id`
                                        FROM
                                        " . ERP_ACC_CHART_OF_ACCOUNTS . " innerTable
                                        WHERE innerTable.glStType = 'group' AND innerTable.`p_id`=coaTable.`id` 
                                        ) 
                                  AND coaTable.`status` != 'deleted'
                                ORDER BY coaTable.gl_code";
                              $listResult = queryGet($coaSql, true);

                              if ($listResult["status"] == "success") {
                                foreach ($listResult["data"] as $listRow) {
                              ?>
                                  <option value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label']; ?>
                                  </option>
                              <?php }
                              } ?>
                            </select>

                            <!-- <div class="modal fade" id="accountDropdown" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content" style="max-height: 600px;">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Select Parent*</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body p-2">

                                    <?php echo createGlTreeAddform(); ?>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary">Save changes</button>
                                  </div>
                                </div>
                              </div>
                            </div> -->

                            <input type="hidden" name="gl_code" id="agl_code" value="">
                            <input type="hidden" name="personal_glcode_lvl" id="apersonal_glcode_lvl" value="">
                            <input type="hidden" name="typeAcc" id="apersonal_typeAcc" value="">
                          </div>

                          <span class="error " id="ap_id_error"></span>

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">

                          <div class="form-input">

                            <label for="">A/C Code</label>

                            <input type="text" class="form-control" id="agl_code_preview" name="gl_code_preview" readonly required>

                            <span class="error"></span>

                          </div>

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">

                          <div class="form-input">

                            <label for="">Account Name</label>

                            <input type="text" class="form-control" id="gl_label" name="gl_label" required>
                            <span class="error"></span>

                          </div>

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">

                          <div class="form-input">

                            <label for="">Account Description</label>

                            <input type="text" class="form-control" id="remark" name="remark">

                            <span class="error"></span>

                          </div>

                        </div>

                        <div class="btn-section mt-2 mb-2 ml-auto">
                          <button type="submit" class="btn btn-primary save-close-btn float-right add_data waves-effect waves-light" value="add_post">Create Account</button>
                        </div>

                      </div>

                    </div>

                  </div>

              </div>
              </form>

            </div>

          </div>

        </div>

      </div>
  </div>
  </section>
  <!-- /.content -->
  </div>

<?php
} else {

  $length_bkup = explode('-', $company_data['data']['gl_length_bkup']);
  $format = [];
  foreach ($length_bkup as $key => $bkdta) {
    $format[] = str_pad(0, $bkdta, 0, STR_PAD_LEFT);
  }
  $gl_format = implode('-', $format);
?>

  <style>
    .content-wrapper {
      padding: 30px 5px 10px 5px !important;
    }
  </style>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->


    <div class="gl-filter-list filter-list">
      <div class="btns-group d-flex justify-content-end">
        <?php
        if ($queryGetNumRows['numRows'] == 4) { ?>
          <a class="btn btn-primary text-white" data-toggle="modal" data-target="#importAcc" id="import-acc">Import Default <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
          <div class="modal fade zoom-in preview-default-coa-modal" id="importAcc" tabindex="-1" aria-labelledby="importAccLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Preview Default COA</h5>
                </div>
                <div class="modal-body">
                  <?php echo previewGlTreeDefult(); ?>
                </div>

                <div class="modal-footer">
                  <button onclick="window.location.href='<?php echo basename($_SERVER['PHP_SELF']) ?>?import=importDefault'" type="button" class="btn btn-success">Import Default</button>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
        <?php  } else { ?>
          <a href="manage-chart-of-account.php" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active"></i>Tree View</a>
          <a href="sub-gl-view.php" class="btn  waves-effect waves-light"><i class="fa fa-stream mr-2 "></i>Sub GL View</a>
          <a href="gl-view.php" class="btn waves-effect waves-light"><i class="fa fa-list mr-2"></i>GL View</a>
          <a class="btn btn-primary text-white" href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create=account" type="button" id="add-acc">Add Account <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
        <?php } ?>
      </div>
    </div>

    <section class="tree-table-2">
      <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
        <li class="pt-2 px-3 my-2 d-flex justify-content-between align-items-center" style="width:100%">
          <h3 class="card-title">Chart of Account (<?= $gl_format; ?>)</h3>

        </li>
      </ul>

      <!-- <div id="my-tree-view" class="tree-view">
        <div class="branch-node">
          <div class="branch-title"><i class="fa fa-plus mr-2"></i> Food</div>
          <ul class="branches">
            <div class="branch-node">
              <div class="branch-title"><i class="fa fa-plus mr-2"></i>Burgers</div>
              <ul class="branches">
                <li>Hamburger</li>
                <li>Cheeseburger</li>
                <li>Chili Cheeseburger</li>
                <li>Turkey Burger</li>
              </ul>
            </div>



            <div class="branch-node">
              <div class="branch-title"><i class="fa fa-plus mr-2"></i>Pizza</div>
              <ul class="branches">
                <li>Cheese</li>
                <li>Pepperoni</li>
                <li>Hawaiian</li>
                <li>Vegetarian</li>
              </ul>
            </div>

            <div class="branch-node">
              <div class="branch-title"><i class="fa fa-plus mr-2"></i>Soup</div>
              <ul class="branches">
                <li>Beef Stew</li>
                <li>Chicken Noodle</li>
                <li>Chicken and Dumplings</li>
                <li>Tomato</li>
              </ul>
            </div>
          </ul>
        </div>

        <div class="branch-node">
          <div class="branch-title"><i class="fa fa-plus mr-2"></i>Drinks</div>
          <ul class="branches">
            <div class="branch-node">
              <div class="branch-title">Soda</div>
              <ul class="branches">
                <li>Sprite</li>
                <li>Mountain Dew</li>
                <li>Dr. Pepper</li>
                <li>Rootbeer</li>
              </ul>
            </div>

            <div class="branch-node">
              <div class="branch-title"><i class="fa fa-plus mr-2"></i>Juice</div>
              <ul class="branches">
                <li>Apple Juice</li>
                <li>Orange Juice</li>
                <li>Grape Juice</li>
                <li>Cranberry Juice</li>
                <div class="branch-node">
                  <div class="branch-title"><i class="fa fa-plus mr-2"></i>Extra</div>
                  <ul class="branches">
                    <li>Strawberry Lemonade</li>
                    <li>Tropical Punch</li>
                    <li>Orange Mango</li>
                    <li>Strawberry Banana</li>
                  </ul>
                </div>
              </ul>
            </div>

            <li>Water</li>
          </ul>
        </div>

        <div class="branch-node">
          <div class="branch-title"><i class="fa fa-plus mr-2"></i>Desserts</div>
          <ul class="branches">
            <div class="branch-node">
              <div class="branch-title"><i class="fa fa-plus mr-2"></i>Pies</div>
              <ul class="branches">
                <li>Cherry Pie</li>
                <li>Blackberry Pie</li>
                <li>Pumpkin Pie</li>
                <li>Cheese Cake</li>
              </ul>
            </div>

            <div class="branch-node">
              <div class="branch-title"><i class="fa fa-plus mr-2"></i>Cakes</div>
              <ul class="branches">
                <li>Vanilla</li>
                <li>Chocolate</li>
                <li>Lemon Meringue</li>
                <li>Red Velvet</li>
              </ul>
            </div>

            <div class="branch-node">
              <div class="branch-title"><i class="fa fa-plus mr-2"></i>Cookies</div>
              <ul class="branches">
                <li>Chocolate Chip</li>
                <li>Oatmeal Raisin</li>
                <li>Peanut Butter</li>
                <li>Fudge</li>
              </ul>
            </div>
          </ul>
        </div>
      </div> -->

      <!-- <ul class="tree">
            <li>
                <input type="checkbox" checked="checked" id="c1" />
                <label class="tree_label" for="c1">Assests
                    <span class="action-btns">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="customSwitches">
                            <label class="custom-control-label" for="customSwitches"></label>
                        </div> <i class="fa fa-edit"></i>
                        <i class="fa fa-trash"></i>
                    </span>
                </label>
                <ul>
                    <li>
                        <input type="checkbox" checked="checked" id="c2" />
                        <label for="c2" class="tree_label">Child</label>
                        <ul>
                            <li><span class="tree_label">Sub-child-1</span></li>
                            <li><span class="tree_label">sub-child-28</span></li>
                        </ul>
                    </li>
                    <li>
                        <input type="checkbox" id="c3" />
                        <label for="c3" class="tree_label">Child-2</label>
                        <ul>
                            <li><span class="tree_label">Sub-child-1</span></li>
                            <li>
                                <input type="checkbox" id="c4" />
                                <label for="c4" class="tree_label">Sub-child-2</label>
                                <ul>
                                    <li><span class="tree_label">Sub Sub-child-1</span></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>

            <li>
                <input type="checkbox" id="c5" />
                <label class="tree_label" for="c5">Liability</label>
                <ul>
                    <li>
                        <input type="checkbox" id="c6" />
                        <label for="c6" class="tree_label">Level 1</label>
                        <ul>
                            <li><span class="tree_label">Level 2</span></li>
                        </ul>
                    </li>
                    <li>
                        <input type="checkbox" id="c7" />
                        <label for="c7" class="tree_label">Level 1</label>
                        <ul>
                            <li><span class="tree_label">Level 2</span></li>
                            <li>
                                <input type="checkbox" id="c8" />
                                <label for="c8" class="tree_label">Level 2</label>
                                <ul>
                                    <li><span class="tree_label">Level 3</span></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>

            <li>
                <input type="checkbox" id="c5" />
                <label class="tree_label" for="c5">Income</label>
                <ul>
                    <li>
                        <input type="checkbox" id="c6" />
                        <label for="c6" class="tree_label">Level 1</label>
                        <ul>
                            <li><span class="tree_label">Level 2</span></li>
                        </ul>
                    </li>
                    <li>
                        <input type="checkbox" id="c7" />
                        <label for="c7" class="tree_label">Level 1</label>
                        <ul>
                            <li><span class="tree_label">Level 2</span></li>
                            <li>
                                <input type="checkbox" id="c8" />
                                <label for="c8" class="tree_label">Level 2</label>
                                <ul>
                                    <li><span class="tree_label">Level 3</span></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>
                <input type="checkbox" id="c5" />
                <label class="tree_label" for="c5">Expenditure</label>
                <ul>
                    <li>
                        <input type="checkbox" id="c6" />
                        <label for="c6" class="tree_label">Level 1</label>
                        <ul>
                            <li><span class="tree_label">Level 2</span></li>
                        </ul>
                    </li>
                    <li>
                        <input type="checkbox" id="c7" />
                        <label for="c7" class="tree_label">Level 1</label>
                        <ul>
                            <li><span class="tree_label">Level 2</span></li>
                            <li>
                                <input type="checkbox" id="c8" />
                                <label for="c8" class="tree_label">Level 2</label>
                                <ul>
                                    <li><span class="tree_label">Level 3</span></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>

        </ul> -->
      <?php echo createGlTree(); ?>
    </section>

    <div class="modal fade right" id="GLedit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog w-50">
        <div class="modal-content">
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_group_frm" name="add_group_frm">
          <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Edit Form</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row ReturnsDataDiv" style="row-gap: 7px;">
              <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Loding...
            </div>
            <div class="row ReturnsDataDivoriginal" style="row-gap: 7px;">

            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
          </form>
        </div>
      </div>
    </div>



  </div>
<?php
}
include("common/footer.php");
?>



<script>
  $(document).ready(function() {
    $(document).on('click', '.edit-gst', function() {
      let glid = $(this).data('glid');
      console.log("Getting value is - ", glid);
      $.ajax({
        url: `ajaxs/ajax-get-gl-detail.php?glid=${glid}`,
        type: 'get',
        beforeSend: function() {          
          $(".ReturnsDataDiv").show();
          $(".ReturnsDataDivoriginal").hide();
          $(".ReturnsDataDivoriginal").html('');
          $(".modal-footer").hide();
        },
        success: function(response) {

          $(".ReturnsDataDiv").hide();
          $(".ReturnsDataDivoriginal").show();
          $(".modal-footer").show();
          $(".ReturnsDataDivoriginal").html(response);

        }
      });
    });
  });



  /* $(".add_data").click(function() {
    var data = this.value;
    $("#createdata").val(data);
    //confirm('Are you sure to Submit?')
    $("#add_frm").submit();
  });*/
  $(".edit_data").click(function() {
    var data = this.value;
    $("#editdata").val(data);
    alert(data);
    //$( "#edit_frm" ).submit();
  });


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


  $(document).ready(function() {


    $(document).on("change", "#gp_id", function() {
      let p_id = $(this).val();
      $("#ggl_code_preview").val('');
      $("#gp_id_error").hide();
      $("#gp_id_error").html('');
      //alert(p_id);
      $.ajax({
        url: 'ajaxs/ajax_gl_code.php',
        data: {
          p_id
        },
        type: 'POST',
        beforeSend: function() {
          // $('.vendor_bank_cancelled_cheque').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          //$(".vendor_bank_cancelled_cheque").toggleClass("disabled");
        },
        success: function(responseData) {
          responseObj = JSON.parse(responseData);
          console.log(responseObj);
          if (responseObj['status'] == 'success') {
            $("#ggl_code_preview").val(responseObj['personal_full_gl_code']);
            $("#ggl_code").val(responseObj['personal_full_gl_code']);
            $("#gpersonal_glcode_lvl").val(responseObj['personal_glcode_lvl']);
            $("#gpersonal_typeAcc").val(responseObj['personal_typeAcc']);
          } else {
            $("#gp_id_error").show();
            $("#gp_id_error").html(responseObj['message']);
          }
        }
      });

    });


    $(document).on("change", "#ap_id", function() {
      let p_id = $(this).val();
      $("#agl_code_preview").val('');
      $("#ap_id_error").hide();
      $("#ap_id_error").html('');
      //alert(p_id);
      $.ajax({
        url: 'ajaxs/ajax_gl_code.php',
        data: {
          p_id
        },
        type: 'POST',
        beforeSend: function() {
          // $('.vendor_bank_cancelled_cheque').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          //$(".vendor_bank_cancelled_cheque").toggleClass("disabled");
        },
        success: function(responseData) {
          responseObj = JSON.parse(responseData);
          console.log(responseObj);
          if (responseObj['status'] == 'success') {
            $("#agl_code_preview").val(responseObj['personal_full_gl_code']);
            $("#agl_code").val(responseObj['personal_full_gl_code']);
            $("#apersonal_glcode_lvl").val(responseObj['personal_glcode_lvl']);
            $("#apersonal_typeAcc").val(responseObj['personal_typeAcc']);
          } else {
            $("#ap_id_error").show();
            $("#ap_id_error").html(responseObj['message']);
          }
        }
      });

    });

    $(document).on("imput keyup", ".group_imput", function() {
      let label_val = $(this).val();
      let p_id = 0;
      $("#gl_code_preview").val('');
      $("#p_id_error").hide();
      $("#p_id_error").html('');
      //alert(p_id);
      $.ajax({
        url: 'ajaxs/ajax_gl_code.php',
        data: {
          p_id
        },
        type: 'POST',
        beforeSend: function() {
          // $('.vendor_bank_cancelled_cheque').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          //$(".vendor_bank_cancelled_cheque").toggleClass("disabled");
        },
        success: function(responseData) {
          responseObj = JSON.parse(responseData);
          console.log(responseObj);
          if (responseObj['status'] == 'success') {
            $("#gl_code_preview").val(responseObj['personal_full_gl_code']);
            $("#gl_code").val(responseObj['personal_full_gl_code']);
            $("#personal_glcode_lvl").val(responseObj['personal_glcode_lvl']);
            $("#personal_typeAcc").val(responseObj['personal_typeAcc']);
          } else {
            $("#p_id_error").show();
            $("#p_id_error").html(responseObj['message']);
          }
        }
      });

    });

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



<!---Nodetree start--->


<script>
  function expand() {
    $('.nested').toggle('caret-down');
  }

  // function collapse() {
  //    $('.nested').toggle('hide');
  // }

  carets = document.getElementsByClassName('caret');

  for (var i = 0; i < carets.length; i++) {
    carets[i].addEventListener('click', function() {
      this.classList.toggle('caret-down')
      parent = this.parentElement;
      parent.querySelector('.nested').classList.toggle('active')
    })
  }


  // createParent = document.getElementById('createParent')
  // backend = document.getElementById('backend')
  // createParent.addEventListener('click', function() {

  //     backend.innerHTML += ` <li>
  //     <input type="text" value='Parent'>
  //     <button class='createChild'><i class="fa fa-sitemap" aria-hidden="true"></i></button>
  //     <span class='closeIT'>X</span>
  // </li>`
  // })

  backend.addEventListener('click', function(e) {
    if (e.target.classList == 'closeIT') {
      // e.target.parentElement.remove();
    }

    // if (e.target.classList == 'createChild') {
    //     createChildBTN = e.target;
    //     li = createChildBTN.parentElement;
    //     count = prompt('Enter the Number of Row');
    //     var x = '';
    //     for (var i = 1; i <= count; i++) {
    //         x += ` <li>
    //         <input type="text" value='Child'>
    //         <button class='createGrandChild'><i class="fa fa-sitemap" aria-hidden="true"></i></button>
    //         <span class='closeIT'>X</span>
    //     </li>`
    //     }

    //     li.innerHTML += `<ul>${x}</ul>`;

    // }

    if (e.target.classList == 'createGrandChild') {
      createChildBTN = e.target;
      li = createChildBTN.parentElement;
      count = prompt('Enter the Number of Row');
      var x = '';
      for (var i = 1; i <= count; i++) {
        x += ` <li>
            <input type="text" value='Grand Child'>
            <button class='createGreatGrandChild'><i class="fa fa-sitemap" aria-hidden="true"></i></button>
            <span class='closeIT'>X</span>
        </li>`
      }

      li.innerHTML += `<ul>${x}</ul>`;

    }

    if (e.target.classList == 'createGreatGrandChild') {
      createChildBTN = e.target;
      li = createChildBTN.parentElement;
      count = prompt('Enter the Number of Row');
      var x = '';
      for (var i = 1; i <= count; i++) {
        x += ` <li>
            <input type="text" value='Great Grand Child'>
           
            <span class='closeIT'>X</span>
        </li>`
      }

      li.innerHTML += `<ul>${x}</ul>`;

    }



  });








  function expandAll() {
    $(".collapsible-header").addClass("active");
    $(".collapsible").collapsible({
      accordion: false
    });
  }

  function collapseAll() {
    $(".collapsible-header").removeClass(function() {
      return "active";
    });
    $(".collapsible").collapsible({
      accordion: true
    });
    $(".collapsible").collapsible({
      accordion: false
    });
  }
</script>


<script>
  function hasClass(element, className) {
    return element.classList.contains(className);
  }

  function treeView(element) {
    element.addEventListener("click", (e) => {
      let target = e.target;

      console.log(target);

      if (hasClass(target, "branch-node")) target.classList.toggle("open");
      else if (hasClass(target, "branch-title"))
        target.parentNode.classList.toggle("open");
    });
  }

  var myTreeView = document.getElementById("my-tree-view");

  treeView(myTreeView);
</script>

<script>
  $('.branch-title').click(function() {
    $(this).children("i").toggleClass("fa-plus mr-2");
    $(this).children("i").toggleClass("fa-minus mr-2");
  });
</script>

<!------Nodetree end------->