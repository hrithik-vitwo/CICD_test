<?php
include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/branch/func-cost-center.php");
include("../app/v1/functions/company/func-company-cash-accounts.php");

global $company_id;
global $created_by;
global $updated_by;

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}
// ########################################################################
// ########################################################################
if (isset($_POST['insertBankCashAccountBtn'])) {
  // console($_POST);
  $addBankCash = insertBankCashAccount($_POST);
  if ($addBankCash['status'] == "success") {
    swalAlert($addBankCash["status"], 'SUCCESS', $addBankCash["message"]);
  } else {
    swalAlert($addBankCash["status"], $addBankCash["message"]);
  }
}

if (isset($_POST["changeStatus"])) {

  $id = $_POST["id"];
  $update = queryUpdate("UPDATE `erp_cn_varient` SET status='deleted' WHERE `id`=$id");
}




if (isset($_POST["submitInvNumber"]) && isset($_POST["createdata"])) {
  // console($_POST);
  $varient_name = $_POST["varient_name"];
  $seperator = $_POST["prefixDivider"];
  $reset_time = $_POST["reset_time"] ?? 'never';
  $array = $_POST["prefix"];
  $iv_number_example = $_POST["iv_number_example"];
  $array_input = $_POST["prefixInput"];
  $data_array = [];
  foreach ($array as $key => $each) {
    $data_array[$each] = $array_input[$key];
  }
  $serialized_data = serialize($data_array);

  $insert = queryInsert("INSERT INTO `erp_cn_varient` SET 
    `company_id`='" . $company_id . "',
    `title`='" . $varient_name . "',
    `verient_serialized`='" . $serialized_data . "',
    `iv_number_example`='" . $iv_number_example . "',
    `seperator`='" . $seperator . "',
    `reset_time`='" . $reset_time . "',
    `description`='',
    `created_by`='" . $created_by . "',
    `updated_by`='" . $updated_by . "'");

  if ($insert["status"] == "success") {
    swalToast($insert["status"], $insert["message"]);
  } else {
    swalToast($insert["status"], $insert["message"]);
  }
} elseif (isset($_POST["submitInvNumber"]) && isset($_POST["updatedata"])) {
  $id = $_POST["updatedata"];
  $varient_name = $_POST["varient_name"];
  $seperator = $_POST["prefixDivider"];
  $iv_number_example = $_POST["iv_number_example"];
  $reset_time = $_POST["reset_time"] ?? 'never';
  $array = $_POST["prefix"];
  $array_input = $_POST["prefixInput"];
  $data_array = [];
  foreach ($array as $key => $each) {
    $data_array[$each] = $array_input[$key];
  }
  $serialized_data = serialize($data_array);

  //update
  $update = queryUpdate("UPDATE `erp_cn_varient` SET 
  `company_id`='" . $company_id . "',
  `title`='" . $varient_name . "',
  `verient_serialized`='" . $serialized_data . "',
  `iv_number_example`='" . $iv_number_example . "',
  `seperator`='" . $seperator . "',
  `reset_time`='" . $reset_time . "',
  `description`='',
  `updated_by`='" . $updated_by . "' WHERE `id`=" . $id);

  // console($insert);
  // dd();

  if ($update["status"] == "success") {
    swalToast($update["status"], $update["message"]);
  } else {
    swalToast($update["status"], $update["message"]);
  }
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

  /* .dividerDiv {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0px;
    border-radius: 5px;
  } */

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
</style>

<?php
if (isset($_GET['create'])) {

  $financial_year = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`='$company_id' ORDER BY `created_at` DESC", false);
  $proper_finacial_year = $financial_year["data"]["year_variant_name"];
  $rowNo = 0;
  $add_update = 0;
?>
  <div class="content-wrapper">
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

          <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Credit Note Number Format</a></li>

          <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Add Format</a>
          </li>

          <li class="back-button">

            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">

              <i class="fa fa-reply po-list-icon"></i>

            </a>

          </li>

        </ol>

      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
          <input type="hidden" name="createdata" value="createdata">
          <div class="row brances-create">
            <div class="col-lg-12 col-md-12 col-sm-12">
              <div class="card invoice-format-card">
                <div class="card-header p-3" style="display: flex;align-items: center;justify-content: space-between;">
                  <h4>Credit Note Number Format</h4>
                  <i class="fa fa-plus-circle shadow-sm p-2" style="cursor: pointer; user-select:none" id="addPrefix">
                    Add</i>
                </div>
                <div class="row  mx-2">
                  <div class="row">
                    <div class="col-lg-5 col-md-6 col-sm-12 mx-12">
                      <div class="form-input">
                        <label for="">Varient Name <span class="text-danger">*</span> <i>[ Example: For IT
                            Operation]</i></label>
                        <input type="text" id="variant_name" name="varient_name" class="form-control">
                      </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 mx-2">
                      <div class="form-input">
                        <label for="">Select Divider <span class="text-danger">*</span></label>
                        <select name="prefixDivider" class="form-control" id="prefixDividerDropDown">
                          <option value="/">/</option>
                          <option value="-">-</option>
                          <option value="">No Divider</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row d-flex inputDivSec mx-2"></div>

                <div class="row p-2">
                  <div class="col-12 previewDiv">
                    <strong>Preview:</strong>
                    <h5 class="invoiceNoPreview"></h5>
                    <input type="hidden" id="iv_number_example" name="iv_number_example" value="">
                  </div>

                  <div class="col-12 reset_time" style="display: none;">
                    <strong>Choose one: </strong>
                    <div class="form-input my-2">
                      <input class="reset_radio" type="radio" name="reset_time" value="never"><label><b> I would like to continue the
                          serial</b> <i>[Where serial will not reset and not start from beginning while the financial-year
                          or Calendar year is changed. e.i- CN-2021/22-00567 it will continue as
                          CN-2022/23-00568]</i>.</label>
                    </div>
                    <div class="form-input mb-2">
                      <input class="reset_radio" type="radio" name="reset_time" value="fyearly"><label><b> I would like to Reset the Credit Note Number
                          serial on financial-year change</b> <i>[Where serial will be reset and start from beginning
                          while the financial-year is changed. e.i- CN-2021/22-00567 it will start from
                          CN-2022/23-00001]</i>.</label>
                    </div>
                    <div class="form-input mb-2">
                      <input class="reset_radio" type="radio" name="reset_time" value="yearly"><label><b> I would like to Reset the Credit Note Number
                          serial on Calendar year change</b> <i>[Where serial will be reset and start from beginning while
                          the Calendar year is changed. e.i- CN-2022-00567 it will start from
                          CN-2023-00001]</i>.</label>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="btn-section mt-2 mb-2 ml-auto">
                    <button type="submit" id="submitInvFormatBtn" class="btn btn-primary save-close-btn float-right add_data waves-effect waves-light" name="submitInvNumber" value="Submit Number">Submit</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </section>

  <?php
} elseif (isset($_GET['update']) && $_GET['update'] != "") {
  $financial_year = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`='$company_id' ORDER BY `created_at` DESC", false);
  $proper_finacial_year = $financial_year["data"]["year_variant_name"];

  $id = $_GET['update'];
  $getQuery = queryGet("SELECT * FROM `erp_cn_varient` WHERE `company_id`='$company_id' AND `id`='$id'", false);
  $queryData = $getQuery["data"];
  $add_update = 1;
  // console($queryData);
  ?>
    <div class="content-wrapper">
      <!-- Modal -->
      <div class="modal fade" id="exampleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="exampleModalContent modal-content card">
            <div class="modal-header card-header py-2 px-3">
              <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
              <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div id="exampleModalBody" class="modal-body card-body">
            </div>
          </div>
        </div>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="prefixModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="prefixModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="prefixModalContent modal-content card">
            <div class="modal-header card-header py-2 px-3">
              <h4 class="modal-title font-monospace text-md text-white" id="prefixModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
              <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div id="prefixModalBody" class="modal-body card-body">
            </div>
          </div>
        </div>
      </div>

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

            <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Credit Note Number Format</a></li>

            <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Add Format</a>
            </li>

            <li class="back-button">

              <a href="<?= basename($_SERVER['PHP_SELF']); ?>">

                <i class="fa fa-reply po-list-icon"></i>

              </a>

            </li>

          </ol>

        </div>
      </div>
      <section class="content">
        <div class="container-fluid">
          <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
            <input type="hidden" name="updatedata" value="<?= $id ?>">
            <div class="row brances-create">
              <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card invoice-format-card">
                  <div class="card-header p-3" style="display: flex;align-items: center;justify-content: space-between;">
                    <h4>Credit Note Number Format</h4>
                    <i class="fa fa-plus-circle shadow-sm p-2" style="cursor: pointer; user-select:none" id="addPrefix">
                      Add</i>
                  </div>

                  <div class="row  mx-2">
                    <div class="row">
                      <div class="col-lg-5 col-md-6 col-sm-12 mx-12">
                        <div class="form-input">
                          <label for="">Varient Name <span class="text-danger">*</span> <i>[ Example: For IT
                              Operation]</i></label>
                          <input type="text" name="varient_name" class="form-control" value="<?= $queryData["title"] ?>" required>
                        </div>
                      </div>
                      <div class="col-lg-2 col-md-2 col-sm-2 mx-2">
                        <div class="form-input">
                          <label for="">Select Divider <span class="text-danger">*</span></label>
                          <select name="prefixDivider" class="form-control" id="prefixDividerDropDown" required>
                            <option value="/" <?php if ($queryData["seperator"] == "/")
                                                echo "selected" ?>>/</option>
                            <option value="-" <?php if ($queryData["seperator"] == "-")
                                                echo "selected" ?>>-</option>
                            <option value="" <?php if ($queryData["seperator"] == "")
                                                echo "selected" ?>>No Divider</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>



                  <div class="row d-flex inputDivSec mx-2">
                    <?php
                    $chooseOne = 1;
                    $unserialized_datas = unserialize($queryData["verient_serialized"]);
                    foreach ($unserialized_datas as $key => $unserialized_data) {
                      $number = $key;
                      $value = $unserialized_data;
                      if ($key == "yyyy" || $key == "fy") {
                        $chooseOne++;
                      }
                    ?>
                      <div class="col-lg-2 col-md-2 col-sm-2 my-2 invContainer" id="invContainer_<?= $key ?>">
                        <div class="form-input div002">
                          <div style="display: flex;justify-content: space-between;">
                            <span>Prefix <span class="text-danger">*</span></span>
                            <span><i class="fa fa-times-circle text-danger removeInvNoPrefixDivBtn" id="removeInvNoPrefixDivBtn_<?= $key ?>" style="cursor:pointer"></i></span>
                          </div>
                          <select class="form-control prefixDropDown" name="prefix[]" id="prefixDropDown_<?= $key ?>" required>
                            <option value="">Select Prefix</option>
                            <?php
                            $query = queryGet("SELECT * FROM `erp_iv_charecterstics` WHERE (`company_id`='$company_id' OR `company_id`='0' OR `company_id`='') ", true);
                            $datas = $query["data"];


                            foreach ($datas as $data) {
                            ?>
                              <option value="<?= $data["slug"] ?>" <?php if ($number == $data["slug"])
                                                                      echo ("selected") ?>><?= $data["title"] ?></option>
                            <?php
                            }
                            ?>
                          </select>
                          <?php
                          if ($number == "month") {
                          ?>
                            <input type="text" name="prefixInput[]" value="<?= $value ?>" class="form-control my-2 prefixInput" id="prefixInput_<?= $key ?>" readonly>
                          <?php
                          } elseif ($number == "yyyy") {
                          ?>
                            <input type="text" name="prefixInput[]" value="<?= $value ?>" class="form-control my-2 prefixInput" id="prefixInput_<?= $key ?>" readonly>
                          <?php
                          } elseif ($number == "fy") {
                          ?>
                            <input type="text" name="prefixInput[]" value="<?= $value ?>" class="form-control my-2 prefixInput" id="prefixInput_<?= $key ?>" readonly>
                          <?php
                          } else {
                          ?>
                            <input type="text" name="prefixInput[]" value="<?= $value ?>" class="form-control my-2 prefixInput" id="prefixInput_<?= $key ?>">
                          <?php
                          }
                          ?>
                        </div>
                      </div>
                    <?php
                    }
                    $rowNo = $key;
                    ?>
                  </div>

                  <div class="row p-2">
                    <div class="col-12 previewDiv">
                      <strong>Preview:</strong>
                      <h5 class="invoiceNoPreview"></h5>
                      <input type="hidden" id="iv_number_example" name="iv_number_example" value="<?= $queryData["iv_number_example"]; ?>">
                    </div>

                    <div class="col-12 reset_time" <?php if ($chooseOne == 1) { ?>style="display: none;" <?php } ?>>
                      <strong>Choose one: </strong>
                      <div class="form-input my-2">
                        <input class="reset_radio" type="radio" name="reset_time" value="never" <?php if ($queryData["reset_time"] == "never")
                                                                                                  echo "checked" ?>><label><b> I would like to continue the serial</b> <i>[Where serial will not
                            reset and not start from beginning while the financial-year or Calendar year is changed. e.i-
                            CN-2021/22-00567 it will continue as CN-2022/23-00568]</i>.</label>
                      </div>
                      <div class="form-input mb-2">
                        <input class="reset_radio" type="radio" name="reset_time" value="fyearly" <?php if ($queryData["reset_time"] == "fyearly")
                                                                                                    echo "checked" ?>><label><b> I would like to Reset the
                            Credit Note Number serial on financial-year change</b> <i>[Where serial will be reset and start from
                            beginning while the financial-year is changed. e.i- CN-2021/22-00567 it will start from
                            CN-2022/23-00001]</i>.</label>
                      </div>
                      <div class="form-input mb-2">
                        <input class="reset_radio" type="radio" name="reset_time" value="yearly" <?php if ($queryData["reset_time"] == "yearly")
                                                                                                    echo "checked" ?>><label><b> I would like to Reset the Credit Note Number serial on Calendar year
                            change</b> <i>[Where serial will be reset and start from beginning while the Calendar year is
                            changed. e.i- CN-2022-00567 it will start from CN-2023-00001]</i>.</label>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="btn-section mt-2 mb-2 ml-auto">
                      <button type="submit" class="btn btn-primary save-close-btn float-right add_data waves-effect waves-light" name="submitInvNumber" value="Submit Number">Update</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </section>

    <?php
  } else {
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
              <li class="breadcrumb-item"><a href="#" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage
                  Credit Note Number Format</a></li>
              <li class="back-button">
                <a href="?create" class="btn btn-primary text-white">
                  <i class="fa fa-plus mr-2"></i>Add
                </a>
              </li>

            </ol>

            <table class="table defaultDataTable table-hover">
              <thead>
                <tr class="alert-light">
                  <th>Sl. No.</th>
                  <th>Varients</th>
                  <th>CN Number</th>
                  <th>Reset Info</th>
                  <th>Edit</th>
                  <th>Delete</th>
                </tr>
              </thead>
              <tbody>
                <?php

                $Invquery = queryGet("SELECT * FROM `erp_cn_varient` WHERE `company_id`='$company_id' AND status='active'", true);
                $Invdatas = $Invquery["data"];
                $sl = 1;
                foreach ($Invdatas as $key => $Invdata) { ?>
                  <tr>
                    <td>
                      <?= $sl ?>
                    </td>
                    <td>
                      <?= $Invdata["title"] ?>
                    </td>
                    <?php
                    $unserialized_datas = unserialize($Invdata["verient_serialized"]);
                    $number = "";
                    foreach ($unserialized_datas as $key => $unserialized_data) {
                      $number .= $unserialized_data . $Invdata["seperator"];
                    }

                    ?>
                    <td>
                      <?= substr($number, 0, strlen($number) - 1) ?>
                    </td>
                    <td>
                      <?php if ($Invdata["reset_time"] == 'never') {
                        $reset_time = "Reset not required, continue the serial.";
                      } elseif ($Invdata["reset_time"] == 'fyearly') {
                        $reset_time = "Reset the CN serial on financial-year change.";
                      } elseif ($Invdata["reset_time"] == 'yearly') {
                        $reset_time = "Reset the CN serial on Calendar year change";
                      } else {
                        $reset_time = "-";
                      }
                      echo $reset_time;
                      ?>

                    </td>
                    <td>
                      <?php if (!empty($Invdata["last_inv_no"]) || $Invdata["flag_default"] == '0') {
                        if ($Invdata["flag_default"] == '0') {
                          $titlemsg = "This is Default Variant, no permission to change.";
                        } else {
                          $titlemsg = "Not editable, It already in used";
                        }
                      ?>
                        <button class="btn btn-sm" href="" title="<?= $titlemsg; ?>"><i class="fa fa-lock po-list-icon"></i></button>
                      <?php } else { ?>
                        <a class="btn btn-sm" href="?update=<?= $Invdata['id'] ?>"><i class="fa fa-edit po-list-icon"></i></a>
                      <?php } ?>

                    </td>
                    <td>
                      <?php if (!empty($Invdata["last_inv_no"]) || $Invdata["flag_default"] == '0') {
                        if ($Invdata["flag_default"] == '0') {
                          $titlemsg = "This is Default Variant, no permission to delete.";
                        } else {
                          $titlemsg = "Not deletable, It already in used.";
                        }
                      ?>
                        <button class="btn btn-sm" href="" title="<?= $titlemsg; ?>"><i class="fa fa-lock po-list-icon" aria-hidden="true"></i></button>
                      <?php } else { ?>
                        <form action="" method="POST">
                          <input type="hidden" name="id" value="<?php echo $Invdata['id'] ?>">
                          <input type="hidden" name="changeStatus" value="delete">
                          <button title="Delete Cost Center" type="submit" onclick="return confirm('Are you sure to delete?')" class="btn btn-sm" style="cursor: pointer;"><i class="fa fa-trash po-list-icon"></i></button>
                        </form>
                      <?php } ?>


                    </td>

                  </tr>


                <?php
                  $sl++;
                }
                ?>

              </tbody>
            </table>

          </div>
        </div>
      </div>
    <?php
  }
  include("common/footer.php");
    ?>
    <script>
      $(document).ready(function() {

        var fin_year = <?= json_encode($proper_finacial_year) ?>;
        var add_update = <?= json_encode($add_update) ?>;
        // =========================================================================================

        function addInvNoPrefixDiv(rowNo = 0) {
          $('.inputDivSec').append(`<div class="col-lg-2 col-md-2 col-sm-2 my-2 invContainer" id="invContainer_${rowNo}">
            <div class="form-input div002">
              <div style="display: flex;justify-content: space-between;">
                <span>Prefix <span class="text-danger">*</span></span>
                <span><i class="fa fa-times-circle text-danger removeInvNoPrefixDivBtn" id="removeInvNoPrefixDivBtn_${rowNo}" style="cursor:pointer"></i></span>
              </div>
              <select class="form-control prefixDropDown" name = "prefix[]" id="prefixDropDown_${rowNo}">
                <option value="">Select Prefix</option>
                <?php
                $query = queryGet("SELECT * FROM `erp_iv_charecterstics` WHERE (`company_id`='$company_id' OR `company_id`='0' OR `company_id`='')", true);
                $datas = $query["data"];

                foreach ($datas as $data) {
                ?>
                      <option value="<?= $data["slug"] ?>"><?= $data["title"] ?></option>
                    <?php
                  }
                    ?>
              </select>
              <input type="text" name="prefixInput[]" class="form-control my-2 prefixInput" id="prefixInput_${rowNo}">
            </div>
          </div>`);
        }

        $(document).on("keyup", ".prefixInput", function() {
          let rowNo = ($(this).attr("id")).split("_")[1];
          let val = $(`#prefixInput_${rowNo}`).val();
          showTheFinalPrefixPreview();
        });


        if (add_update == 0) {
          addInvNoPrefixDiv();
        } else {
          showTheFinalPrefixPreview();
        }
        let addPrefixRowNo = <?= json_encode($rowNo) ?>;
        $('#addPrefix').click(function() {
          addInvNoPrefixDiv(addPrefixRowNo += 1);
          showTheFinalPrefixPreview();
        });

        $(document).on("click", ".removeInvNoPrefixDivBtn", function() {
          let rowNo = ($(this).attr("id")).split("_")[1];
          $(this).parent().parent().parent().parent().remove();
          showTheFinalPrefixPreview();
        });

        $(document).on("change", "#prefixDividerDropDown", function() {
          showTheFinalPrefixPreview();
        });

        function showTheFinalPrefixPreview() {
          let prefixInputArr = [];
          $(".prefixInput").each(function(index, element) {
            let val = $(this).val();
            prefixInputArr.push(val.trim());
          });
          let prefixInputData = prefixInputArr.join($("#prefixDividerDropDown").val());
          console.log(prefixInputData);
          $(".invoiceNoPreview").html(prefixInputData);
          $("#iv_number_example").val(prefixInputData);
        }

        $(document).on("change", ".prefixDropDown", function() {

          let rowNo = ($(this).attr("id")).split("_")[1];
          let val = $(`#prefixDropDown_${rowNo}`).val();

          if (val == "month") {
            var m_names = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
            const d = new Date();
            let month = d.getMonth();
            $(`#prefixInput_${rowNo}`).val(m_names[month]);
            $(`#prefixInput_${rowNo}`).prop("readonly", true);
            showTheFinalPrefixPreview();
          } else if (val == "yyyy") {
            const d = new Date();
            let year = d.getFullYear();
            $(`#prefixInput_${rowNo}`).val(year.toString());
            $(`#prefixInput_${rowNo}`).prop("readonly", true);
            showTheFinalPrefixPreview();
          } else if (val == "fy") {
            $(`#prefixInput_${rowNo}`).val(fin_year);
            $(`#prefixInput_${rowNo}`).prop("readonly", true);
            showTheFinalPrefixPreview();
          } else {
            $(`#prefixInput_${rowNo}`).prop("readonly", false);
            $(`#prefixInput_${rowNo}`).val("");
            showTheFinalPrefixPreview();
          }

          // alert(val);
        });


        // =============================================================================

      });

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


    <script src="<?= BASE_URL; ?>public/validations/invoiceFormatValidation.js"></script>