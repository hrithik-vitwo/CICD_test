<?php
include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/branch/func-cost-center.php");
include("../app/v1/functions/company/func-company-cash-accounts.php");

$company_id = $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"];
if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusCostCenter($_POST, "CostCenter_id", "CostCenter_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"]);
}


if (isset($_POST["createdata"])) {
  $addNewObj = createDataCostCenter($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
  if ($addNewObj["status"] == "success") {
    swalToast($addNewObj["status"], $addNewObj["message"], $_SERVER['PHP_SELF']);
  } else {
    swalToast($addNewObj["status"], $addNewObj["message"]);
  }
}


if (isset($_POST["editdata"])) {
  $editDataObj = updateDataCostCenter($_POST);
  if ($editDataObj["status"] == "success") {
    swalToast($editDataObj["status"], $editDataObj["message"], $_SERVER['PHP_SELF']);
  } else {
    swalToast($editDataObj["status"], $editDataObj["message"]);
  }
}


if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}


$sqqql = "SELECT CostCenter_code FROM `" . ERP_COST_CENTER . "` WHERE `company_id`='" . $company_id . "' AND `CostCenter_status`!='deleted' ORDER BY CostCenter_id DESC LIMIT 1";
$CostCenter_code = queryGet($sqqql);
if (isset($CostCenter_code['data'])) {
  $CostCenter_Lastcode = $CostCenter_code['data']['CostCenter_code'];
} else {
  $CostCenter_Lastcode = '';
}


// ########################################################################
// ########################################################################
if (isset($_POST['insertBankCashAccountBtn'])) {
  // console($_POST);
  $addBankCash = insertBankCashAccount($_POST);
  // console($addBankCash);
  if ($addBankCash['status'] == "success") {
    swalToast($addBankCash["status"], $addBankCash["message"]);
  } else {
    swalToast($addBankCash["status"], $addBankCash["message"]);
  }
}


// update BankCashAccountBtn
if (isset($_POST['updateBankCashAccountBtn'])) {
  console($_POST);
  $updateBankCash = updateBankCashAccount($_POST);
  console($updateBankCash);
  // if ($updateBankCash['status'] == "success") {
  //   swalToast($updateBankCash["status"], $updateBankCash["message"], $_SERVER['PHP_SELF']);
  // } else {
  //   swalToast($updateBankCash["status"], $updateBankCash["message"]);
  // }
}


if (isset($_POST['deleteBtn'])) {
  // console($_POST);
  $deleteBankCash = deleteBankCashAccount($_POST);
  // console($deleteBankCash);
  if ($deleteBankCash['status'] == "success") {
    swalToast($deleteBankCash["status"], $deleteBankCash["message"]);
  } else {
    swalToast($deleteBankCash["status"], $deleteBankCash["message"]);
  }
}


?>
<link rel="stylesheet" href="../public/assets/listing.css">
<?php
if (isset($_GET['create'])) {
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <form action="" method="POST">
      <div class="content-header mb-2 p-0  border-bottom">
        <?php if (isset($msg)) { ?>
          <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
            <?= $msg ?>
          </div>
        <?php } ?>
        <div class="container-fluid">
          <div class="row pt-2 pb-2">
            <div class="col-md-6">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-light">Manage Cash Account</a></li>
                <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Add Cash Account</a></li>
              </ol>
            </div>
            <div class="col-md-6" style="display: flex;">
              <button name="insertBankCashAccountBtn" class="btn btn-primary btnstyle gradientBtn ml-2 insertBankCashAccountBtn"><i class="fa fa-plus fontSize"></i>Submit</button>
            </div>
          </div>
        </div>
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">

            <div class="col-md-6">
              <div class="input-group">
                <select name="parentGL" class="select4 form-control form-control-border borderColor">
                  <option value="">Parent GL</option>
                  <?php foreach (getAllCOA()['data'] as $one) { ?>
                    <option value="<?= $one['id'] ?>"><?= $one['gl_label'] ?>(<?= $one['gl_code'] ?>)</option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <select name="accountType" id="accountType" class="select4 form-control form-control-border borderColor">
                  <option value="">Account Type</option>
                  <option value="cash">Cash</option>
                  <option value="bank">Bank</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row" style="display: none;" id="cashDiv">
            <div class="col-md-12">
              <div class="input-group">
                <input type="text" class="form-control" name="cashAccount" placeholder="Enter cash account">
              </div>
            </div>
          </div>
          <div class="row" style="display: none;" id="bankDiv">
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" class="form-control" name="bankName" placeholder="Enter bank name">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" class="form-control" name="ifscCode" placeholder="Enter IFSC code">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" class="form-control" name="accountNo" placeholder="Enter Account No.">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" class="form-control" name="accountHolderName" placeholder="Enter Account holder name">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" class="form-control" name="bankAddress" placeholder="Enter Bank Address">
              </div>
            </div>
          </div>
        </div>
      </section>
    </form>
    <!-- /.content -->
  </div>
<?php
} else if (isset($_GET['edit'])) {
  $id = $_GET['edit'];
  // console(getBankCashAccountDetails($id)['data']);
  $bankDetails = getBankCashAccountDetails($id)['data'];
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <form action="" method="POST">
      <input type="hidden" name="cashAccountId" value="<?= $bankDetails['id'] ?>">
      <div class="content-header mb-2 p-0  border-bottom">
        <?php if (isset($msg)) { ?>
          <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
            <?= $msg ?>
          </div>
        <?php } ?>
        <div class="container-fluid">
          <div class="row pt-2 pb-2">
            <div class="col-md-6">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-light">Manage Cash Account</a></li>
                <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Add Cash Account</a></li>
              </ol>
            </div>
            <div class="col-md-6" style="display: flex;">
              <button name="updateBankCashAccountBtn" class="btn btn-primary btnstyle gradientBtn ml-2 updateBankCashAccountBtn"><i class="fa fa-plus fontSize"></i>Update</button>
            </div>
          </div>
        </div>
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-6">
              <div class="input-group">
                <select name="parentGL" class="select4 form-control form-control-border borderColor">
                  <option value="">Parent GL</option>
                  <?php foreach (getAllCOA()['data'] as $one) { ?>
                    <option <?php if ($bankDetails['parent_gl'] == $one['id']) {
                              echo "selected";
                            } ?> value="<?= $one['id'] ?>"><?= $one['gl_label'] ?>(<?= $one['gl_code'] ?>)</option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <select name="accountType" id="accountType" class="select4 form-control form-control-border borderColor">
                  <option value="">Account Type</option>
                  <option <?php if ($bankDetails['type_of_account'] == "cash") {
                            echo "selected";
                          } ?> value="cash">Cash</option>
                  <option <?php if ($bankDetails['type_of_account'] == "bank") {
                            echo "selected";
                          } ?> value="bank">Bank</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row" style="display: <?php if ($bankDetails['type_of_account'] == "cash") {
                                              echo "block";
                                            } else {
                                              echo "none";
                                            } ?>;" id="cashDiv">
            <div class="col-md-12">
              <div class="input-group">
                <input type="text" class="form-control" value="<?= $bankDetails['bank_name'] ?>" name="cashAccount" placeholder="Enter cash account">
              </div>
            </div>
          </div>
          <div class="row" style="display: <?php if ($bankDetails['type_of_account'] == "bank") {
                                              echo "block";
                                            } else {
                                              echo "none";
                                            } ?>;" id="bankDiv">
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" class="form-control" value="<?= $bankDetails['bank_name'] ?>" name="bankName" placeholder="Enter bank name">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" class="form-control" value="<?= $bankDetails['ifsc_code'] ?>" name="ifscCode" placeholder="Enter IFSC code">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" class="form-control" value="<?= $bankDetails['account_no'] ?>" name="accountNo" placeholder="Enter Account No.">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" class="form-control" value="<?= $bankDetails['account_holder_name'] ?>" name="accountHolderName" placeholder="Enter Account holder name">
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" class="form-control" value="<?= $bankDetails['bank_address'] ?>" name="bankAddress" placeholder="Enter Bank Address">
              </div>
            </div>
          </div>
        </div>
      </section>
    </form>
    <!-- /.content -->
  </div>
<?php
} else {
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
            <div class="card card-tabs">
              <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                  <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                    <h3 class="card-title text-light">Manage Cash Account</h3>
                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary btnstyle m-2"><i class="fa fa-plus"></i> Add New</a>
                  </li>
                </ul>
              </div>
              <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                <div class="card-body">
                  <div class="row filter-serach-row">
                    <div class="col-lg-2 col-md-2 col-sm-12">
                      <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog" aria-hidden="true"></i></a>
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
                                <button type="submit" class="btn btn-primary btnstyle">Search</button>
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-2">
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger btnstyle">Reset</a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <button type="button" class="collapsible btn-search-collpase" id="btnSearchCollpase">
                          <i class="fa fa-search"></i>
                        </button>
                      </div>

                    </div>
                  </div>

              </form>
              <div class="tab-content" id="custom-tabs-two-tabContent">
                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                  <?php
                  $cond = '';
                  $con = '';
                  $sts = " AND `CostCenter_status` !='deleted'";
                  if (isset($_REQUEST['CostCenter_status']) && $_REQUEST['CostCenter_status'] != '') {
                    $sts = ' AND CostCenter_status="' . $_REQUEST['CostCenter_status'] . '"';
                  }

                  if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                    $cond .= " AND CostCenter_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                  }

                  if (isset($_SESSION["logedCompanyAdminInfo"]["fldAdminBranchId"]) && $_SESSION["logedCompanyAdminInfo"]["fldAdminBranchId"] != '') {
                    $con = " AND branch_id ='0' AND location_id ='0'";
                  }

                  if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                    $cond .= " AND (`CostCenter_code` like '%" . $_REQUEST['keyword'] . "%' OR `CostCenter_desc` like '%" . $_REQUEST['keyword'] . "%')";
                  }

                  $sql_list = "SELECT * FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` ORDER BY id DESC";
                  $qry_list = mysqli_query($dbCon, $sql_list);
                  $num_list = mysqli_num_rows($qry_list);


                  $countShow = "SELECT count(*) FROM `" . ERP_COST_CENTER . "` WHERE 1 " . $cond . $con  . " AND company_id='" . $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"] . "' AND branch_id ='0' AND location_id ='0' " . $sts . " ";
                  $countQry = mysqli_query($dbCon, $countShow);
                  $rowCount = mysqli_fetch_array($countQry);
                  $count = $rowCount[0];
                  $cnt = $GLOBALS['start'] + 1;
                  $settingsTable = getTableSettings(TBL_COMPANY_ADMIN_TABLESETTINGS, "ERP_COST_CENTER", $_SESSION["logedCompanyAdminInfo"]["adminId"]);
                  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                  $settingsCheckbox = unserialize($settingsCh);
                  if ($num_list > 0) {
                  ?>
                    <table id="mytable" class="table defaultDataTable table-hover text-nowrap">
                      <thead>
                        <tr>
                          <th>#</th>
                          <?php if (in_array(1, $settingsCheckbox)) { ?>
                            <th>Parent GL</th>
                          <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                            <th>Account Type</th>
                          <?php }
                          if (in_array(3, $settingsCheckbox)) { ?>
                            <th>Created By</th>
                          <?php  }
                          if (in_array(4, $settingsCheckbox)) { ?>
                            <th>Created At</th>
                          <?php  } ?>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        while ($row = mysqli_fetch_assoc($qry_list)) {
                          $getCOADetails = getCOADetails($row['parent_gl'])['data'];
                        ?>
                          <tr>
                            <td><?= $cnt++ ?></td>
                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                              <td>
                                <p><strong><?= $getCOADetails['gl_label'] ?></strong></p><small>(<?= $getCOADetails['gl_code'] ?>)</small>
                              </td>
                            <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                              <td><?= $row['type_of_account'] ?></td>
                            <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                              <td><?= $row['created_by'] ?></td>
                            <?php }
                            if (in_array(4, $settingsCheckbox)) { ?>
                              <td><?= formatDateTime($row['created_at']); ?></td>
                            <?php } ?>
                            <td>
                              <?php if ($row['status'] == "active") { ?>
                                <span class="font-weight-bold badge text-success"><?php echo ucfirst($row['status']); ?></span>
                              <?php } else if ($row['status'] == "inactive") { ?>
                                <span class="font-weight-bold badge text-danger"><?php echo ucfirst($row['status']); ?></span>
                              <?php } else if ($row['status'] == "deleted") { ?>
                                <span class="font-weight-bold badge text-warning"><?php echo ucfirst($row['status']); ?></span>
                              <?php } ?>
                            </td>
                            <td>
                              <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                                <input type="hidden" name="cashAccountId" value="<?= $row['id'] ?>">
                                <button title="View" type="button" name="viewBtn" style="cursor: pointer;" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $row['id'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></button>
                                <a href="<?php $_SERVER['PHP_SELF']; ?>?edit=<?= $row['id'] ?>" title="Edit" name="editBtn" onclick="return confirm('Are you sure to edit?')" style="cursor: pointer; border:none"><i class='fa fa-edit '></i></a>
                                <button title="Delete" type="submit" name="deleteBtn" onclick="return confirm('Are you sure to delete?')" style="cursor: pointer; border:none"><i class='fa fa-trash '></i></button>
                              </form>
                            </td>
                          </tr>
                          <!-- start modal -->
                          <div class="modal fade right customer-modal" id="fluidModalRightSuccessDemo_<?= $row['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                              <!--Content-->
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 class="text-light"><?= $getCOADetails['gl_label'] ?></h4><strong><?= $getCOADetails['gl_code'] ?></strong>
                                </div>
                                <div class="modal-body">
                                  <p>Parent GL: <span><?= $getCOADetails['gl_label'] ?></span></p>
                                  <p>Account Type: <strong class="text-success"><?= ucfirst($row['type_of_account']) ?></strong></p>
                                  <p>Bank Name: <strong><?= $row['bank_name'] ?></strong></p>
                                  <?php if ($row['ifsc_code'] != "" && $row['account_no'] != "" && $row['account_holder_name'] != "" && $row['bank_address'] != "") { ?>
                                    <p>IFSC Code: <strong><?= $row['ifsc_code'] ?></strong></p>
                                    <p>Account No: <strong><?= $row['account_no'] ?></strong></p>
                                    <p>Account Holder Name: <strong><?= $row['account_holder_name'] ?></strong></p>
                                    <p>Bank Address: <strong><?= $row['bank_address'] ?></strong></p>
                                  <?php } ?>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- end modal -->

                        <?php  } ?>
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
                      <input type="hidden" name="pageTableName" value="ERP_COST_CENTER" />
                      <div class="modal-body">
                        <div id="dropdownframe"></div>
                        <div id="main2">
                          <table>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                Parent GL</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                Account Type</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                Created By</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox10" value="4" />
                                Created At</td>
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
  $('.m-input').on('keyup', function() {
    $(this).parent().children('.error').hide()
  });

  $(".add_data").click(function() {
    var data = this.value;
    $("#createdata").val(data);
    let flag = 1;
    var Ragex = "/[0-9]{4}/";
    if ($("#CostCenter_code").val() == "") {
      $(".CostCenter_code").show();
      $(".CostCenter_code").html("Credit Period is requried.");
      flag++;
    } else {
      $(".CostCenter_code").hide();
      $(".CostCenter_code").html("");
    }
    if ($("#CostCenter_desc").val() == "") {
      $(".CostCenter_desc").show();
      $(".CostCenter_desc").html("Description is requried.");
      flag++;
    } else {
      $(".CostCenter_desc").hide();
      $(".CostCenter_desc").html("");
    }

    if (flag != 1) {
      return false;
    } else {
      $("#add_frm").submit();
    }


  });
  $(".edit_data").click(function() {
    var data = this.value;
    $("#editdata").val(data);
    let flag = 1;
    var Ragex = "/[0-9]{4}/";
    if ($("#CostCenter_code").val() == "") {
      $(".CostCenter_code").show();
      $(".CostCenter_code").html("Credit Period is requried.");
      flag++;
    } else {
      $(".CostCenter_code").hide();
      $(".CostCenter_code").html("");
    }
    if ($("#CostCenter_desc").val() == "") {
      $(".CostCenter_desc").show();
      $(".CostCenter_desc").html("Description is requried.");
      flag++;
    } else {
      $(".CostCenter_desc").hide();
      $(".CostCenter_desc").html("");
    }

    if (flag != 1) {
      return false;
    } else {
      $("#edit_frm").submit();
    }

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

    $("#accountType").on("change", function() {
      let accountType = $(this).val();
      console.log(accountType);

      if (accountType === "cash") {
        $("#cashDiv").show();
        $("#bankDiv").hide();
      } else if (accountType === "bank") {
        $("#bankDiv").show();
        $("#cashDiv").hide();
      } else {
        $("#bankDiv").hide();
        $("#cashDiv").hide();
      }
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