<?php
include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/branch/func-cost-center.php");
include("../app/v1/functions/company/func-company-cash-accounts.php");

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}
// ########################################################################
// ########################################################################
if (isset($_POST['insertBankCashAccountBtn'])) {
  // console($_POST);
  $addBankCash = insertBankCashAccount($_POST);
  // console($addBankCash);
  if ($addBankCash['status'] == "success") {
    swalAlert($addBankCash["status"], 'SUCCESS', $addBankCash["message"]);
  } else {
    swalAlert($addBankCash["status"], $addBankCash["message"]);
  }
}

// update BankCashAccountBtn
if (isset($_POST['updateBankCashAccount'])) {
  // console($_POST);
  $updateBankCash = updateBankCashAccount($_POST);

  if ($updateBankCash['status'] == "success") {
    swalToast($updateBankCash["status"], $updateBankCash["message"]);
  } else {
    swalToast($updateBankCash["status"], $updateBankCash["message"]);
  }
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



function uploadBankStatement($INPUTS)
{
  global $company_id;
  global $created_by;
  global $updated_by;
  $statement_data = json_decode(base64_decode($INPUTS["statement_data"]), true)["payload"] ?? [];
  $statement_file = $INPUTS["statement_file"];
  $statement_bank_id = $INPUTS["statement_bank_id"];
  $column_names = $INPUTS["column_names"];
  $insertErr = 0;
  foreach ($statement_data as $oneRow) {
    $key = -1;
    $set_data = [];
    foreach ($oneRow as $column => $value) {
      $key++;
      if (!empty($column_names[$key])) {
        if ($column_names[$key] == "tnx_date") {
          $set_data[] = $column_names[$key] . '="' . date_format(date_create($value), "Y-m-d") . '"';
        } elseif (in_array($column_names[$key], ["withdrawal_amt", "deposit_amt", "balance_amt"])) {
          $set_data[] = $column_names[$key] . '=' . floatval(str_replace(",", "", $value));
        } else {
          $set_data[] = $column_names[$key] . '="' . $value . '"';
        }
      }
    }
    // echo implode(",",$set_data)."<br>";
    $insertData = implode(", ", $set_data);
    $prevCondition = implode(" AND ", $set_data);
    if (!empty($prevCondition)) {
      $prevObj = queryGet('SELECT * FROM `erp_bank_statements` WHERE `company_id`=' . $company_id . ' AND ' . $prevCondition);
      // console($prevObj);
      if ($prevObj["status"] != "success") {
        $insObj = queryInsert('INSERT INTO `erp_bank_statements` SET `company_id`=' . $company_id . ', `bank_id`=' . $statement_bank_id . ', `created_by`="' . $created_by . '", `updated_by`="' . $updated_by . '", ' . $insertData);
        if ($insObj["status"] != "success") {
          $insertErr++;
        }
      }
    }
  }

  if ($insertErr == 0) {
    return [
      "status" => "success",
      "message" => "Statement successfully saved",
    ];
  } else {
    return [
      "status" => "error",
      "message" => "Statement not saved, please try again",
    ];
  }
}

if (isset($_POST["submitOcrStatementBtn"])) {
  $uploadObj = uploadBankStatement($_POST);
  swalToast($uploadObj["status"], $uploadObj["message"]);
}


?>

<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/sales-order.css">
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

  .bank-cash-acc-add-modal .modal-body {
    height: 250px;
    overflow: auto;
  }

  .bank-cash-acc-add-modal .modal-footer {
    background: #b2c2d1;
    padding-top: 10px;
  }

  .satatement-modal .modal-dialog {
    width: 100%;
    max-width: 1000px;
  }

  .satatement-modal .modal-dialog .modal-body {
    width: 100%;
  }
</style>

<?php
if (isset($_GET['create'])) {
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
            <ul class="nav nav-tabs border-bottom-0" id="custom-tabs-two-tab" role="tablist">
              <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                <h3 class="card-title mb-3">Manage Accounts</h3>
                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" data-toggle="modal" data-target="#addBankCashAcc" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
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
                          <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" data-toggle="modal" data-target="#addBankCashAcc" class="btn btn-sm btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
              <div class="modal fade add-modal bank-cash-acc-add-modal" id="addBankCashAcc" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                    <div class="modal-content card">
                      <div class="modal-header card-header pt-2 pb-2 px-3">
                        <h4 class="text-xs text-white mb-0">Create Cash Account</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="form-input mb-3">
                              <select name="parentGL" class="form-control select2 mapping-hidden-btn" id="parentGL" autofocus>
                                <option value="blank">Parent GL</option>
                                <?php foreach (getAllCOA()['data'] as $one) { ?>
                                  <option value="<?= $one['id'] ?>_<?= $one['gl_label'] ?>"><?= $one['gl_label'] ?>(<?= $one['gl_code'] ?>)</option>
                                <?php } ?>
                              </select>
                            </div>
                            <div class="form-input mb-3">
                              <select name="addAccountType" id="accountType" class="form-control select2 mapping-hidden-btn" autofocus>
                                <option value="">Account Type</option>
                              </select>
                            </div>
                            <div class="form-input mb-3">
                              <input type="radio" name="paymentType" value="0"><span class="text-xs"> Payment </span>
                              <input type="radio" name="paymentType" value="1"><span class="text-xs"> Receive </span>
                              <input type="radio" name="paymentType" value="2"><span class="text-xs"> Both </span>
                            </div>

                            <div class="row" style="display: <?php if ($bankDetails['type_of_account'] == "cash") {
                                                                echo "block";
                                                              } else {
                                                                echo "none";
                                                              } ?>;" id="cashDiv">
                              <div class="col-md-6">
                                <div class="form-input mb-3">
                                  <input type="text" class="form-control" id="addCashAccount" value="<?= $bankDetails['cash_account'] ?>" name="addCashAccount" placeholder="Enter cash account">
                                </div>
                              </div>

                              <div class="col-md-6" style="display:none;">
                                <div class="form-input mb-3">
                                  <input type="number" class="form-control" id="opening_balance_c" value="0" name="opening_balance_c" placeholder="Enter opening balance">
                                </div>
                              </div>
                            </div>
                            <div class="row" style="display: <?php if ($bankDetails['type_of_account'] == "bank") {
                                                                echo "block";
                                                              } else {
                                                                echo "none";
                                                              } ?>;" id="addBankDiv">
                              <div class="col-md-6">
                                <div class="form-input">
                                  <label for="">Account No.</label>
                                  <input type="text" class="form-control" value="<?= $bankDetails['account_no'] ?>" name="accountNo" placeholder="Enter Account No.">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-input">
                                  <label for="">IFSC Code</label>
                                  <div class="d-flex">
                                    <input type="text" class="form-control IFSClass" value="<?= $bankDetails['ifsc_code'] ?>" name="ifscCode" placeholder="Enter IFSC code" id="fetchBankDetailsFromIFSC">
                                    <span class="tick-icon"></span>
                                  </div>
                                  <span class="text-xs" id="ifscCodeMsg"></span>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-input mb-3">
                                  <label for="">Bank Name</label>
                                  <input type="text" class="form-control" value="<?= $bankDetails['bank_name'] ?>" id="bankName" name="bankName" placeholder="Enter bank name">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-input mb-3">
                                  <label for="">Bank Address</label>
                                  <input type="text" class="form-control" value="<?= $bankDetails['bank_address'] ?>" id="bankAddress" name="bankAddress" placeholder="Enter Bank Address">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-input mb-3">
                                  <label for="">Account Holder Name</label>
                                  <input type="text" class="form-control" value="<?= $bankDetails['account_holder_name'] ?>" id="accountHolderName" name="accountHolderName" placeholder="Enter Account holder name">
                                </div>
                              </div>

                              <div class="col-md-6" style="display:none;">
                                <div class="form-input mb-3">
                                  <label for="">Opening Balance</label>
                                  <input type="number" class="form-control" id="opening_balance" value="0" name="opening_balance" placeholder="Enter opening balance" step="1">
                                </div>
                              </div>
                            </div>

                            <div class="form-input mb-3"> <label for="">This Account For:</label> <br>
                              <div class="row">
                                <?php
                                $location = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `company_id`=$company_id AND `othersLocation_status`!='deleted' ", true);


                                foreach ($location['data'] as $location) {
                                ?>
                                  <div class="col-6">
                                    <input type="checkbox" name="accForLocation[]" value="<?= $location['othersLocation_id'] ?>"><span class="text-xs"> <?= $location['othersLocation_name'] . "(" . $location['othersLocation_code'] . ")" ?></span>

                                  </div>
                                <?php
                                }
                                ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <!-- <input type="hidden" name="insertBankCashAccountBtn" value="insertBankCashAccountBtn"> -->
                        <button type="button" name="insertBankCashAccountBtn" class="btn btn-primary submitCashAcc" id="submitCashAcc"> Submit</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

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

                  $sql_list = "SELECT * FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id=" . $company_id . " ORDER BY id DESC";
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
                            <th>Bank Name</th>
                          <?php }
                          if (in_array(3, $settingsCheckbox)) { ?>
                            <th>Account Type</th>
                          <?php }
                          if (in_array(4, $settingsCheckbox)) { ?>
                            <th>Created By</th>
                          <?php  }
                          if (in_array(5, $settingsCheckbox)) { ?>
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
                              <td><?= $row['bank_name'] ?></td>
                            <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                              <td><?= $row['type_of_account'] ?></td>
                            <?php }
                            if (in_array(4, $settingsCheckbox)) { ?>
                              <td><?= $row['created_by'] ?></td>
                            <?php }
                            if (in_array(5, $settingsCheckbox)) { ?>
                              <td><?= formatDateTime($row['created_at']); ?></td>
                            <?php } ?>
                            <td>
                              <?php if ($row['status'] == "active") { ?>
                                <span class="status"><?php echo ucfirst($row['status']); ?></span>
                              <?php } else if ($row['status'] == "inactive") { ?>
                                <span class="status-danger"><?php echo ucfirst($row['status']); ?></span>
                              <?php } else if ($row['status'] == "deleted") { ?>
                                <span class="status-warning"><?php echo ucfirst($row['status']); ?></span>
                              <?php } ?>
                            </td>
                            <td>
                              <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" class="">
                                <input type="hidden" name="cashAccountId" value="<?= $row['id'] ?>">
                                <button title="View" type="button" name="viewBtn" style="cursor: pointer;" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $row['id'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></button>
                                <!-- <a title="Edit" data-toggle="modal" data-target="#editBankCashAcc_<?= $row['id'] ?>" name="editBtn" style="cursor: pointer; border: none;"><i class="fa fa-edit po-list-icon"></i></a> -->
                                <button title="Delete" type="submit" onclick="return confirm('Are you sure to delete?')" name="deleteBtn" class="btn btn-sm" style="cursor: pointer; border:none"><i class="fa fa-trash po-list-icon"></i></button>
                                <?php
                                if ($row['type_of_account'] == "bank" && $row['status'] != "deleted") {
                                ?>
                                  <button title="Upload Bank Statement" type="button" name="bankStatementUploadModalBtn" style="cursor: pointer;" data-toggle="modal" data-target="#bankStatementUploadModal<?= $row['id'] ?>" class="btn btn-sm bankStatementUploadModalBtn">Upload Statement</button>
                                <?php
                                }
                                ?>
                              </form>
                            </td>
                          </tr>
                          <!-- start modal -->
                          <div class="modal fade right bank-cash-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $row['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                              <!--Content-->
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 class="text-light"><?= $getCOADetails['gl_label'] ?></h4>
                                  <strong><?= $getCOADetails['gl_code'] ?></strong>
                                  <div class="display-flex-space-between mt-4 mb-3">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                      <li class="nav-item">
                                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= str_replace('/', '-', $row['acc_code']) ?>">Info</a>
                                      </li>
                                      <!-- -------------------Audit History Button Start------------------------- -->
                                      <li class="nav-item">
                                        <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $row['acc_code']) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $row['acc_code']) ?>" href="#history<?= str_replace('/', '-', $row['acc_code']) ?>" role=" tab" aria-controls="history<?= str_replace('/', '-', $row['acc_code']) ?>" aria-selected="false"><i class="fa fa-history mr-2"></i> Trail</a>
                                      </li>
                                      <li>

                                        <a class="editFormModal editFormModal_<?= $row['id'] ?>" data-attr="<?= $row['id'] ?>">
                                          <ion-icon name="create-outline"></ion-icon>
                                        </a>
                                      </li>
                                      <!-- -------------------Audit History Button End------------------------- -->
                                    </ul>
                                  </div>
                                </div>
                                <div class="modal-body">

                                  <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $row['acc_code']) ?>" role="tabpanel" aria-labelledby="home-tab">
                                      <p>Parent GL: <span><?= $getCOADetails['gl_label'] ?></span></p>
                                      <p>Account Type: <strong class="text-success"><?= ucfirst($row['type_of_account']) ?></strong></p>
                                      <p>Bank Name: <strong><?= $row['bank_name'] ?></strong></p>
                                      <?php if ($row['ifsc_code'] != "" && $row['account_no'] != "" && $row['account_holder_name'] != "" && $row['bank_address'] != "") { ?>
                                        <p>IFSC Code: <strong><?= $row['ifsc_code'] ?></strong></p>
                                        <p>Account No: <strong><?= $row['account_no'] ?></strong></p>
                                        <p>Account Holder Name: <strong><?= $row['account_holder_name'] ?></strong></p>
                                        <p>Bank Address: <strong><?= $row['bank_address'] ?></strong></p>
                                      <?php } ?>
                                      <div id="edit-form" class="edit-form_<?= $row['id'] ?>" style="display:none;">
                                        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="edit_frm" name="edit_frm">
                                          <input type="hidden" value="updateBankCashAccount" name="updateBankCashAccount">
                                          <input type="hidden" value="<?= $row['id'] ?>" name="cashAccountId">
                                          <div class="modal-content card">
                                            <div class="modal-header card-header pt-2 pb-2 px-3">
                                              <h4 class="text-xs text-white mb-0">Create Cash Account</h4>
                                            </div>
                                            <div class="modal-body">
                                              <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                  <div class="form-input mb-3">
                                                    <select name="parentGL" class="form-control select2 mapping-hidden-btn" id="parentGL" autofocus disabled>


                                                      <option><?= $getCOADetails['gl_label']  ?></option>

                                                    </select>
                                                  </div>
                                                  <div class="form-input mb-3">
                                                    <select name="accountType" id="accountType" class="form-control select2 mapping-hidden-btn" autofocus disabled>
                                                      <option value="<?= $row['type_of_account'] ?>"><?= $row['type_of_account'] ?></option>
                                                    </select>
                                                  </div>

                                                  <div class="form-input mb-3">
                                                    <input type="radio" name="paymentType" value="0" <?php if ($row['flag'] == 0) {
                                                                                                        echo "checked";
                                                                                                      } ?>><span class="text-xs"> Payment </span>
                                                    <input type="radio" name="paymentType" value="1" <?php if ($row['flag'] == 1) {
                                                                                                        echo "checked";
                                                                                                      } ?>><span class="text-xs"> Receive </span>
                                                    <input type="radio" name="paymentType" value="2" <?php if ($row['flag'] == 2) {
                                                                                                        echo "checked";
                                                                                                      } ?>><span class="text-xs"> Both </span>
                                                  </div>

                                                  <div class="row" style="display: <?php if ($row['type_of_account'] == "cash") {
                                                                                      echo "block";
                                                                                    } else {
                                                                                      echo "none";
                                                                                    } ?>;" id="cashDiv">
                                                    <div class="col-md-6">
                                                      <div class="form-input mb-3">
                                                        <input type="text" class="form-control" id="addCashAccount" value="<?= $row['bank_name'] ?>" name="cashAccount" placeholder="Enter cash account">
                                                      </div>
                                                    </div>

                                                    <div class="col-md-6" style="display:none;">
                                                      <div class="form-input mb-3">
                                                        <input type="number" class="form-control" id="opening_balance_c" value="<?= $row['opening_balance'] ?>" name="opening_balance_c" placeholder="Enter opening balance">
                                                      </div>
                                                    </div>
                                                  </div>
                                                  <div class="row" style="display: <?php if ($row['type_of_account'] == "bank") {
                                                                                      echo "block";
                                                                                    } else {
                                                                                      echo "none";
                                                                                    } ?>;" id="addBankDiv">
                                                    <div class="col-md-6">
                                                      <div class="form-input">
                                                        <label for="">Account No.</label>
                                                        <input type="text" class="form-control" value="<?= $row['account_no'] ?>" name="accountNo" placeholder="Enter Account No.">
                                                      </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                      <div class="form-input">
                                                        <label for="">IFSC Code</label>
                                                        <div class="d-flex">
                                                          <input type="text" class="form-control IFSClass" value="<?= $row['ifsc_code'] ?>" name="ifscCode" placeholder="Enter IFSC code" id="fetchBankDetailsFromIFSC">
                                                          <span class="tick-icon"></span>
                                                        </div>
                                                        <span class="text-xs" id="ifscCodeMsg"></span>
                                                      </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                      <div class="form-input mb-3">
                                                        <label for="">Bank Name</label>
                                                        <input type="text" class="form-control" value="<?= $row['bank_name'] ?>" id="bankName" name="bankName" placeholder="Enter bank name">
                                                      </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                      <div class="form-input mb-3">
                                                        <label for="">Bank Address</label>
                                                        <input type="text" class="form-control" value="<?= $row['bank_address'] ?>" id="bankAddress" name="bankAddress" placeholder="Enter Bank Address">
                                                      </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                      <div class="form-input mb-3">
                                                        <label for="">Account Holder Name</label>
                                                        <input type="text" class="form-control" value="<?= $row['account_holder_name'] ?>" id="accountHolderName" name="accountHolderName" placeholder="Enter Account holder name">
                                                      </div>
                                                    </div>

                                                    <div class="col-md-6" style="display:none;">
                                                      <div class="form-input mb-3">
                                                        <label for="">Opening Balance</label>
                                                        <input type="number" class="form-control" id="opening_balance" value="<?= $row['opening_balance'] ?>" name="opening_balance" placeholder="Enter opening balance" step="1">
                                                      </div>
                                                    </div>
                                                  </div>

                                                  <div class="form-input mb-3"> <label for="">This Account For:</label> <br>
                                                    <div class="row">
                                                      <?php
                                                      $func = $row['accForLocation'];
                                                      $func_array = explode(",", $func);
                                                      $location = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `company_id`=$company_id AND `othersLocation_status`!='deleted' ", true);


                                                      foreach ($location['data'] as $location) {
                                                      ?>
                                                        <div class="col-6">
                                                          <input type="checkbox" name="accForLocation[]" value="<?= $location['othersLocation_id'] ?>" <?php if (in_array($location['othersLocation_id'], $func_array)) {
                                                                                                                                                          echo "checked";
                                                                                                                                                        } ?>><span class="text-xs"> <?= $location['othersLocation_name'] . "(" . $location['othersLocation_code'] . ")" ?></span>

                                                        </div>
                                                      <?php
                                                      }
                                                      ?>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="modal-footer">
                                              <!-- <input type="hidden" name="insertBankCashAccountBtn" value="insertBankCashAccountBtn"> -->
                                              <button type="submit" name="updateBankCashAccountBtn" class="btn btn-primary updateBankCashAccountBtn" id=""> Submit</button>
                                            </div>
                                          </div>
                                        </form>
                                      </div>
                                    </div>
                                    <!-- -------------------Audit History Tab Body Start------------------------- -->
                                    <div class="tab-pane fade" id="history<?= str_replace('/', '-', $row['acc_code']) ?>" role="tabpanel" aria-labelledby="history-tab">

                                      <div class="audit-head-section mb-3 mt-3 ">
                                        <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($row['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['created_at']) ?></p>
                                        <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($row['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['updated_at']) ?></p>
                                      </div>
                                      <hr>
                                      <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $row['acc_code']) ?>">

                                        <ol class="timeline">

                                          <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                            <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                            <div class="new-comment font-bold">
                                              <p>Loading...
                                              <ul class="ml-3 pl-0">
                                                <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                              </ul>
                                              </p>
                                            </div>
                                          </li>
                                          <p class="mt-0 mb-5 ml-5">Loading...</p>

                                          <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                            <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                            <div class="new-comment font-bold">
                                              <p>Loading...
                                              <ul class="ml-3 pl-0">
                                                <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                              </ul>
                                              </p>
                                            </div>
                                          </li>
                                          <p class="mt-0 mb-5 ml-5">Loading...</p>
                                        </ol>
                                      </div>
                                    </div>
                                    <!-- -------------------Audit History Tab Body End------------------------- -->
                                  </div>


                                </div>
                              </div>
                            </div>
                          </div>

                          <div class="modal fade add-modal bank-cash-acc-edit-modal" id="editBankCashAcc_<?= $row['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" name="add_frm">
                                <div class="modal-content card">
                                  <div class="modal-header card-header pt-2 pb-2 px-3">
                                    <h4 class="text-xs text-white mb-0">Edit Account</h4>
                                  </div>
                                  <div class="modal-body">
                                    <div class="row">
                                      <div class="col-lg-12 col-md-12 col-sm-12">

                                        <div class="form-input mb-3">
                                          <select name="parentGL" class="select2 form-control editParentGL" id="parentGL_<?= $row['id'] ?>">
                                            <option value="">Parent GL</option>
                                            <?php foreach (getAllCOA()['data'] as $one) { ?>
                                              <option <?php if ($bankDetails['parent_gl'] == $one['id']) {
                                                        echo "selected";
                                                      } ?> value="<?= $one['id'] ?>_<?= $one['gl_label'] ?>"><?= $one['gl_label'] ?>(<?= $one['gl_code'] ?>)</option>
                                            <?php } ?>
                                          </select>
                                        </div>

                                        <div class="form-input mb-3">
                                          <select name="accountType" id="accountType_<?= $row['id'] ?>" class="select4 form-control form-control-border borderColor " onchange="accountTypeEdit(<?= $row['id'] ?>)">
                                            <option value="">Account Type</option>
                                            <option <?php if ($row['type_of_account'] == "cash") {
                                                      echo "selected";
                                                    } ?> value="cash">Cash</option>
                                            <option <?php if ($row['type_of_account'] == "bank") {
                                                      echo "selected";
                                                    } ?> value="bank">Bank</option>
                                          </select>
                                        </div>


                                        <div class="row" style="display: <?php if ($row['type_of_account'] == "cash") {
                                                                            echo "block";
                                                                          } else {
                                                                            echo "none";
                                                                          } ?>;" id="editCashDiv<?= $row['id'] ?>">
                                          <div class="col-md-12">
                                            <div class="form-input mb-3">
                                              <input type="text" class="form-control" id="cashAccount_<?= $row['id'] ?>" value="<?= $row['cash_account'] ?>" name="cashAccount" placeholder="Enter cash account">
                                            </div>
                                          </div>
                                        </div>
                                        <div class="row" style="display: <?php if ($row['type_of_account'] == "bank") {
                                                                            echo "block";
                                                                          } else {
                                                                            echo "none";
                                                                          } ?>;" id="editBankDiv_<?= $row['id'] ?>">
                                          <div class="col-md-6">
                                            <div class="form-input">
                                              <label for="">Account No.</label>
                                              <input type="text" class="form-control" value="<?= $bankDetails['account_no'] ?>" name="accountNo" placeholder="Enter Account No.">
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="form-input">
                                              <label for="">IFSC Code</label>
                                              <div class="d-flex">
                                                <input type="text" class="form-control IFSClass" value="<?= $bankDetails['ifsc_code'] ?>" name="ifscCode" placeholder="Enter IFSC code" id="fetchBankDetailsFromIFSC">
                                                <span class="tick-icon"></span>
                                              </div>
                                              <span class="text-xs" id="ifscCodeMsg"></span>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="form-input mb-3">
                                              <label for="">Bank Name</label>
                                              <input type="text" class="form-control" value="<?= $bankDetails['bank_name'] ?>" id="bankName" name="bankName" placeholder="Enter bank name">
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="form-input mb-3">
                                              <label for="">Bank Address</label>
                                              <input type="text" class="form-control" value="<?= $bankDetails['bank_address'] ?>" id="bankAddress" name="bankAddress" placeholder="Enter Bank Address">
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="form-input mb-3">
                                              <label for="">Account Holder Name</label>
                                              <input type="text" class="form-control" value="<?= $bankDetails['account_holder_name'] ?>" id="accountHolderName" name="accountHolderName" placeholder="Enter Account holder name">
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button name="updateBankCashAccountBtn" type="submit" class="btn btn-primary updateBankCashAccountBtn" value="update_post">Update</button>
                                  </div>
                                </div>

                              </form>
                            </div>
                          </div>

                          <?php
                          if ($row['type_of_account'] == "bank" && $row['status'] != "deleted") {
                          ?>
                            <div class="modal fade satatement-modal" id="bankStatementUploadModal<?= $row['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content card">
                                  <div class="modal-header card-header pt-2 pb-2 px-3">
                                    <h4 class="text-xs text-white mb-0">Bank Statement Upload</h4>
                                  </div>
                                  <div class="modal-body">
                                    <div class="row col-6 ml-auto mr-auto uploadStatementFormDiv" id="uploadStatementFormDiv_<?= $row['id'] ?>">
                                      <form action="" method="post" class="statementFileUploadForm" id="statementFileUploadForm_<?= $row['id'] ?>" enctype="multipart/form-data">
                                        <input name="bank_id" type="hidden" value="<?= $row['id'] ?>">
                                        <input name="statementFileInput" type="file" class="form-control statementFileInput" accept=".jpg, .pdf, .png, .jpeg" id="statementFileInput_<?= $row['id'] ?>" required>
                                        <button name="submitStatementFileBtn" class="form-control btn btn-primary submitStatementFileBtn text-light mt-1" id="submitStatementFileBtn_<?= $row['id'] ?>">Upload Statement</button>
                                      </form>
                                    </div>
                                    <div class="row uploadStatementResponseDiv mt-2" id="uploadStatementResponseDiv_<?= $row['id'] ?>" style="overflow: auto; max-height:70vh;">
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                  </div>
                                </div>
                              </div>
                            </div>
                          <?php
                          }
                          ?>

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
                                Bank Name</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                Account Type</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                Created By</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox10" value="4" />
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
  // START: for bank statement upload

  $(document).on("click", ".submitStatementFileBtn", function(e) {
    e.preventDefault();
    let formKey = ($(this).attr("id")).split("_")[1];
    var formData = new FormData();
    formData.append('uploadFile', "submitStatementFileBtn");
    formData.append('bank_id', formKey);
    formData.append('file', $(`#statementFileInput_${formKey}`)[0].files[0]);
    $.ajax({
      url: '<?= BASE_URL ?>company/ajaxs/ajax-bank-statement-upload.php',
      type: 'POST',
      data: formData,
      async: true,
      cache: false,
      contentType: false,
      enctype: 'multipart/form-data',
      processData: false,
      beforeSend: function() {
        $(`#submitStatementFileBtn_${formKey}`).html("Uploading statement, please wait...");
        $(`#submitStatementFileBtn_${formKey}`).prop('disabled', true);
        console.log("Uploading statement.....");
      },
      success: function(response) {
        console.log(response);
        $(`#submitStatementFileBtn_${formKey}`).html("Successfully uploaded");
        $(`#uploadStatementResponseDiv_${formKey}`).html(response);
      },
      complete: function(xhr, textStatus) {
        if (xhr.status != 200) {
          $(`#submitStatementFileBtn_${formKey}`).html("Upload Again");
          $(`#uploadStatementResponseDiv_${formKey}`).html(`<p class="text-center text-warning">Something went wrong, Please try again!</p>`);
        }
        $(`#submitStatementFileBtn_${formKey}`).prop('disabled', false);
        console.log("The request is completed with status code ", xhr.status);
      }
    });
  });



  // END  : for bank statement upload




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


  function accountTypeEdit(id) {
    var accountType = $("#accountType" + id).val();
    // alert(id);
    // alert(accountType);
    // console.log(accountType);     

    if (accountType === "cash") {
      $("#cashDiv" + id).show();
      $("#addBankDiv" + id).hide();
    } else if (accountType === "bank") {
      $("#addBankDiv" + id).show();
      $("#cashDiv" + id).hide();
    } else {
      $("#addBankDiv" + id).hide();
      $("#cashDiv" + id).hide();
    }
  };

  $(document).ready(function() {
    // add bank/cash account ➕➕➕➕➕➕➕➕➕➕➕➕➕➕➕➕➕➕➕
    // add bank/cash account ➕➕➕➕➕➕➕➕➕➕➕➕➕➕➕➕➕➕➕
    $("#parentGL").on("change", function() {
      let parentGLlabel = ($(this).val()).split("_")[1];

      let keyword = 'Bank' || 'bank';

      if (parentGLlabel.indexOf(keyword) !== -1) {
        $("#addBankDiv").show();
        $("#cashDiv").hide();
        $("#accountType").html("<option value='bank'>Bank</option>");
        $(".submitCashAcc").attr("id", "submitBankAcc");
      } else {
        $("#cashDiv").show();
        $("#addBankDiv").hide();
        $("#accountType").html("<option value='cash'>Cash</option>");
        $(".submitCashAcc").attr("id", "submitCashAcc");
      }
    });

    // $("#accountType").on("change", function() {
    //   let accountType = $(this).val();
    //   console.log(accountType);

    //   if (accountType === "cash") {
    //     $("#cashDiv").show();
    //     $("#addBankDiv").hide();
    //   } else if (accountType === "bank") {
    //     $("#addBankDiv").show();
    //     $("#cashDiv").hide();
    //   } else {
    //     $("#addBankDiv").hide();
    //     $("#cashDiv").hide();
    //   }
    // });

    $("#opening_balance").on("keyup keydown input", function() {
      // Get input value
      var inputValue = $(this).val();

      // Check if input value is a decimal number
      if (inputValue.indexOf(".") !== -1) {
        // If input value is a decimal number, round down to the nearest whole number
        $(this).val(Math.floor(inputValue));
      }
    });

    $("#fetchBankDetailsFromIFSC").on("keyup blur", function() {
      let ifsc = $(this).val();

      $.ajax({
        url: `https://ifsc.razorpay.com/${ifsc}`,
        method: "GET",
        success: function(response) {
          $(".IFSClass").addClass(`border border-success`);
          $(".tick-icon").text(`✅`);
          $(".IFSClass").removeClass(`border-danger`);
          $("#ifscCodeMsg").html(`<span class="text-success">ifsc code is valid!</span>`);
          $("#bankName").val(response.BANK);
          $("#bankAddress").val(response.ADDRESS);
          $(".submitCashAcc").prop('disabled', false);
        },
        error: function(xhr, status, error) {
          $(".IFSClass").addClass(`border border-danger`);
          $(".tick-icon").text(`❌`);
          $(".IFSClass").removeClass(`border-success`);
          $("#ifscCodeMsg").html(`<span class="text-danger">ifsc code is not valid!</span>`);
          $("#bankName").val('');
          $(".submitCashAcc").prop('disabled', true);
        }
      });
    });

    $(document).on("click", "#submitBankAcc", function() {
      let parentGL = $("#parentGL").val();
      let accountType = $("#accountType").val();
      let IFSC = $("#fetchBankDetailsFromIFSC").val();
      let bankName = $("#bankName").val();
      let bankAddress = $("#bankAddress").val();
      let accountHolderName = $("#accountHolderName").val();

      if (parentGL != "" && accountType != "" && IFSC != "" && bankName != "" && bankAddress != "" && accountHolderName != "") {
        if (confirm("Are you sure to submitted?")) {
          $("#submitBankAcc").attr("type", "submit");
        }
      } else {
        alert('Field cant blank');
        return false;
      }
    });

    $(document).on("click", "#submitCashAcc", function() {
      let parentGL = $("#parentGL").val();
      let accountType = $("#accountType").val();
      let cashAcc = $("#addCashAccount").val();

      if (parentGL != "" && accountType != "" && cashAcc != "") {
        if (confirm("Are you sure to submitted?")) {
          $("#submitCashAcc").attr("type", "submit");
        }
      } else {
        alert('All fields are required');
        return false;
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
<!-- <script>
  $(document).on('select2:open', () => {
    document.querySelector('.select2-search__field').focus();
  });
</script> -->
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
  $(".editFormModal").click(function() {

    var attr = $(this).data('attr');

    $(".edit-form_" + attr).show();
  });
</script>
<style>
  .dataTable thead {
    top: 0px !important;
  }
</style>