<?php
require_once("../app/v1/connection-company-admin.php");
administratorAuth();
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/admin/func-packages.php");

require_once("../app/v1/functions/company/func-licence.php");


// console($_SESSION);

if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"]);
}
if (isset($_POST["goToPayment"])) {
  //  console($_POST);
  $addNewObj = createLicence($_POST);
  if ($addNewObj["status"] == "success") {
    swalToast($addNewObj["status"], $addNewObj["message"]);
    // console($addNewObj);
  } else {
    swalToast($addNewObj["status"], $addNewObj["message"]);
  }
}


if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

?>

<style>
  .customer-modal .nav.nav-tabs li.nav-item a.nav-link {
    font-size: 12px;
  }

  .display-flex-gap {
    gap: 0 !important;
  }

  .card-body.others-info.vendor-info.so-card-body {
    height: 250px !important;
  }

  .fob-section div {
    align-items: center;
    gap: 3px;
  }

  .so-delivery-create-btn {
    display: flex;
    align-items: center;
    gap: 20px;
    max-width: 250px;
    margin-left: auto;
  }

  .customer-modal .modal-header {
    height: 250px !important;
  }

  .filter-list a.active {
    background-color: #003060;
    color: #fff;
  }

  .display-flex-space-between p {
    width: 77%;
    text-align: left;
  }

  .package-modal .modal-dialog {
    max-width: 700px;
  }

  .package-modal .modal-body {
    max-height: 500px;
  }

  .package-modal-table tbody tr:nth-child(even) td {
    background-color: #b4c7d9;
  }

  .package-modal-table tbody tr td p {
    white-space: pre-line;
  }

  .package-modal .filter-list {
    display: flex;
    gap: 7px;
    justify-content: flex-end;
    position: relative;
    top: 0;
    left: 0;
    margin: 15px 0;
  }

  .advanced-serach {
    background: #003060;
    width: 160px;
    border-radius: 25px;
    cursor: pointer;
  }

  .hamburger.quickadd-hamburger {
    border: 1px solid #fff;
  }

  .advanced-serach p {
    position: absolute;
    top: 18px;
    color: #fff;
  }

  .package-modal .modal-body ul {
    gap: 15px;
    border: 0;
  }

  .package-modal .modal-body ul li a,
  .package-modal .modal-body ul li a:hover {
    color: #003060;
    border: 1px solid #b4c7d9;
    border-radius: 12px;
    background: #b4c7d9;
    font-size: 12px;
  }

  .package-modal .modal-body ul li a.active {
    color: #fff;
    background: #003060;
    border: 1px solid #b4c7d9;
  }


  @media (max-width: 575px) {

    .filter-serach-row {
      align-items: center;
      padding-top: 9px;
      margin-bottom: 0 !important;
    }

    .customer-modal .nav.nav-tabs li.nav-item a.nav-link {
      padding: 7px;
    }

    .card-body.others-info.vendor-info.so-card-body {
      height: auto !important;
    }

    .customer-modal .modal-header {
      height: 285px !important;
    }

    .customer-modal .nav.nav-tabs {
      top: 0 !important;
    }

  }
</style>


<link rel="stylesheet" href="../public/assets/sales-order.css">
<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/accordion.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<?php
if (isset($_GET['licence-creation'])) { ?>
  <div class="content-wrapper">
    <section class="content">
      <div class="container-fluid">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>My Licence List</a></li>
          <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Licence</a></li>
          <li class="back-button">
            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
              <i class="fa fa-reply po-list-icon"></i>
            </a>
          </li>
        </ol>
      </div>
      <form action="" method="POST" id="goToPaymentForm">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card items-select-table">
              <div class="head-item-table">
                <div class="advanced-serach" data-toggle="modal" data-target="#myModal3">
                  <p class="text-xs pl-5">Select Packages</p>
                  <div class="hamburger quickadd-hamburger">
                    <div class="wrapper-action"> <i class="fa fa-plus"></i></div>
                  </div>

                </div>
              </div>
              <table class="table table-sales-order mt-0">
                <thead>
                  <tr>
                    <th>Package Name</th>
                    <th>Variant Name</th>
                    <th>Variant Description</th>
                    <th>No. Of Licence</th>
                    <th>Cost per Licence</th>
                    <th>Total Price</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <span id="spanItemsTable"></span>
                <tbody id="itemsTable"></tbody>
                <tbody>
                  <tr>
                    <td colspan="4" class="text-right p-2" style="border: none; background: none;"> </td>
                    <td colspan="0" class="text-left p-2 font-weight-bold totalCal" style="border: none; background: none;">Sub <sup class="text-primary">[TOTAL]</sup></td>
                    <input type="hidden" name="subTotal" id="subTotal" value="0">
                    <td id="grandSubTotalAmt" class="p-2" style="border: none; background: none;">0.00</th>
                  </tr>
                  <tr class="p-2">
                    <td colspan="4" class="text-right p-2" style="border: none; background: none;"> </td>
                    <td colspan="0" class="text-left p-2 font-weight-bold totalCal" style="border: none; background: none;">TOTAL <sup class="text-info">[TAX]</sup></td>
                    <input type="hidden" name="taxAmount" id="taxAmount" value="0">
                    <td id="grandTaxAmt" class="p-2" style="border: none; background: none;">0.00</td>
                  </tr>
                  <tr class="p-2">
                    <td colspan="4" class="text-right p-2" style="border: none; background: none;"> </td>
                    <td colspan="0" class="text-left p-2 font-weight-bold totalCal" style="border: none; background: none;">TOTAL <sup class="text-success">[VALUE]</sup></td>
                    <input type="hidden" name="totalAmt" id="totalAmt" value="0">
                    <td id="grandTotalAmt" class="p-2" style="border: none; background: none;">0.00</th>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <button type="submit" name="goToPayment" class="btn btn-primary items-search-btn float-right">Buy Now <i class="fa fa-angle-double-right" aria-hidden="true"></i></button>
          </div>
        </div>
      </form>

      <!---------------------------------Package Buy Licence Model Start--------------------------------->
      <div class="modal package-modal" id="myModal3">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Select Package Buy Licence</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div id="dropdownframe"></div>
              <?php
              $packageSql = "SELECT * FROM `" . ERP_PACKAGE_MANAGEMENT . "` WHERE status = 'active' ORDER BY `ordering` ASC  ";
              $packageList = queryGet($packageSql, true);
              ?>
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <?php
                foreach ($packageList['data'] as $pacKey => $package) {
                ?>
                  <li class="nav-item" role="presentation">
                    <a class="nav-link <?php echo $pacKey == 0 ? 'active' : ''; ?>" id="<?= str_replace(' ', '', $package['packageTitle']) ?>-tab" data-bs-toggle="tab" data-bs-target="#<?= str_replace(' ', '', $package['packageTitle']) ?>" type="button" role="tab" aria-controls="<?= str_replace(' ', '', $package['packageTitle']) ?>" aria-selected="true"><i class="fa fa-stream mr-2 
                    <?php echo $pacKey == 0 ? 'active' : ''; ?>"></i><?= $package['packageTitle'] ?></a>
                  </li>
                <?php } ?>

              </ul>


              <div class="tab-content" id="myTabContent">
                <?php
                foreach ($packageList['data'] as $pacKey => $package) {
                  $sqlVariant = "SELECT * FROM `" . ERP_PACKAGE_VARIANT . "` WHERE packageId=" . $package['packageId'] . " AND (forCompany_id=0 OR forCompany_id=1) AND `status` = 'active' ";
                  $VariantList = queryGet($sqlVariant, true);
                ?>
                  <div class="tab-pane fade <?php echo $pacKey == 0 ? 'show active' : ''; ?>" id="<?= str_replace(' ', '', $package['packageTitle']) ?>" role="tabpanel" aria-labelledby="<?= str_replace(' ', '', $package['packageTitle']) ?>-tab">

                    <table class="table table-hover package-modal-table" id="packageModal">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Variant Name</th>
                          <th>Description</th>
                          <th>Total Price</th>
                        </tr>
                      </thead>
                      <tbody class="hsn_tbody">
                        <?php
                        foreach ($VariantList['data'] as $variKey => $variant) {
                          $packageValue = ($package['packageDuration'] / 30) * ($variant['variantPrice']);
                        ?>
                          <tr>
                            <td> <input type="radio" name="variant" value="<?= $variant['packageVariantId']; ?>"></td>
                            <td>
                              <p><?= $variant['variantTitle']; ?></p>
                            </td>
                            <td>
                              <p><b>Base Price :</b> <span style="font-family: 'Source Sans Pro'">₹</span><?= number_format($variant['variantPrice'], 2); ?>/30 Days</p>
                              <p><b>OCR Limit :</b> <?= $variant['OCR']; ?> for 30 Days</p>
                              <p><b>Transaction :</b> <?= $variant['transaction']; ?> for 30 Days</p>
                            </td>
                            <td>
                              <p><span style="font-family: 'Source Sans Pro'">₹</span><?= number_format(round($packageValue), 2); ?></p>
                            </td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>

                  </div>
                <?php } ?>

              </div>





            </div>

            <div class="modal-footer">
              <button type="button" id="addToList" name="addToList" class="btn btn-success">Add to List</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <!---------------------------------Table Model End--------------------------------->
    </section>
  </div>
<?php } else { ?>
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
                  <h3 class="card-title">Manage Licence</h3>
                  <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?licence-creation" class="btn btn-sm btn-primary btnstyle m-2 float-add-btn" style="line-height: 32px;"><i class="fa fa-plus"></i></a>
                </li>
              </ul>
            </div>
            <?php
            $keywd = '';
            if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
              $keywd = $_REQUEST['keyword'];
            } else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
              $keywd = $_REQUEST['keyword2'];
            } ?>
            <div class="card card-tabs" style="border-radius: 20px;">
              <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return srch_frm();">
                <div class="card-body">
                  <div class="row filter-serach-row">
                    <div class="col-lg-2 col-md-2 col-sm-12">
                      <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
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
                          <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?licence-creation" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                        </div>
                      </div>

                    </div>

                    <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Filter Licence</h5>

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
                                                            } ?>>Inactive
                                  </option>
                                  <option value="draft" <?php if (isset($_REQUEST['status_s']) && 'draft' == $_REQUEST['status_s']) {
                                                          echo 'selected';
                                                        } ?>>Draft</option>
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
              </form>
              <script>
                var input = document.getElementById("myInput");
                input.addEventListener("keypress", function(event) {
                  if (event.key === "Enter") {
                    event.preventDefault();
                    document.getElementById("myBtn").click();
                  }
                });
                var form = document.getElementById("search");
                document.getElementById("myBtn").addEventListener("click", function() {
                  form.submit();
                });
              </script>

              <div class="tab-content" id="custom-tabs-two-tabContent">
                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                  <?php
                  $cond = '';

                  $sts = " AND `licence_status` !='deleted'";
                  if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                    $sts = ' AND licence_status="' . $_REQUEST['status_s'] . '"';
                  }

                  if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                    $cond .= " AND licence_created_by between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                  }

                  if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                    $cond .= " AND `licence_code` like '%" . $_REQUEST['keyword2'] . "%' OR `licence_title` like '%" . $_REQUEST['keyword2'] . "%'";
                  } else {
                    if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                      $cond .= " AND `licence_code` like '%" . $_REQUEST['keyword'] . "%'  OR `licence_title` like '%" . $_REQUEST['keyword'] . "%'";
                    }
                  }


                  $sql_list = "SELECT * FROM `" . ERP_COMPANY_LICENCE . "` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "' " . $sts . "  ORDER BY licence_code asc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                  $qry_list = mysqli_query($dbCon, $sql_list);
                  $num_list = mysqli_num_rows($qry_list);


                  $countShow = "SELECT count(*) FROM `" . ERP_COMPANY_LICENCE . "` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "' " . $sts . " ";
                  $countQry = mysqli_query($dbCon, $countShow);
                  $rowCount = mysqli_fetch_array($countQry);

                  $count = $rowCount[0];
                  $cnt = $GLOBALS['start'] + 1;
                  $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_COMPANY_LICENCE", $_SESSION["logedCompanyAdminInfo"]["adminId"]);
                  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                  $settingsCheckbox = unserialize($settingsCh);
                  $settingsCheckboxCount = count($settingsCheckbox);
                  if ($num_list > 0) {
                    $ss = 0;
                  ?>
                    <table class="table defaultDataTable table-hover tableDataBody">
                      <thead>
                        <tr>
                          <th>#</th>
                          <?php $ss++;
                          if (in_array($ss, $settingsCheckbox)) { ?>
                            <th <?= $ss; ?>>Licence Number</th>
                          <?php }
                          $ss++;
                          if (in_array($ss, $settingsCheckbox)) { ?>
                            <th <?= $ss; ?>>Licence Type</th>
                          <?php }
                          $ss++;
                          if (in_array($ss, $settingsCheckbox)) { ?>
                            <th <?= $ss; ?>>Package Name</th>
                          <?php }
                          $ss++;
                          if (in_array($ss, $settingsCheckbox)) { ?>
                            <th <?= $ss; ?>>Package Desc</th>
                          <?php }
                          $ss++;
                          if (in_array($ss, $settingsCheckbox)) { ?>
                            <th <?= $ss; ?>>OCR Blance</th>
                          <?php  }
                          $ss++;
                          if (in_array($ss, $settingsCheckbox)) { ?>
                            <th <?= $ss; ?>>Transaction Blance</th>
                          <?php }
                          $ss++;
                          if (in_array($ss, $settingsCheckbox)) { ?>
                            <th <?= $ss; ?>>Expire Date</th>
                          <?php
                          }
                          $ss++;
                          if (in_array($ss, $settingsCheckbox)) { ?>
                            <th <?= $ss; ?>> Map User </th>
                          <?php
                          }
                          $ss++;
                          if (in_array($ss, $settingsCheckbox)) {
                          ?>
                            <th>Status</th>
                          <?php  } ?>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody class="tableBody">
                        <?php
                        // console($BranchSoObj->fetchBranchSoListing()['data']);
                        foreach ($qry_list as $oneLicence) {
                          $sd = 0;
                          $rand = rand(100, 1000);
                        ?>
                          <tr class="tableOneRow">
                            <td><?= $cnt++ ?></td>
                            <?php $sd++;
                            if (in_array($sd, $settingsCheckbox)) { ?>
                              <td><?= $oneLicence['licence_code'] ?></td>
                            <?php }
                            $sd++;
                            if (in_array($sd, $settingsCheckbox)) { ?>
                              <td><?= $oneLicence['licence_type'] ?></td>
                            <?php }
                            $sd++;
                            if (in_array($sd, $settingsCheckbox)) { ?>
                              <td><?= $oneLicence['licence_title'] ?></td>
                            <?php }
                            $sd++;
                            if (in_array($sd, $settingsCheckbox)) { ?>
                              <td><?= $oneLicence['licence_desc'] ?></td>
                            <?php }
                            $sd++;
                            if (in_array($sd, $settingsCheckbox)) { ?>
                              <td><?= $oneLicence['ocr_limit'] ?></td>
                            <?php }
                            $sd++;
                            if (in_array($sd, $settingsCheckbox)) { ?>
                              <td><?= $oneLicence['transaction_limit'] ?></td>
                            <?php }
                            $sd++;
                            if (in_array($sd, $settingsCheckbox)) { ?>
                              <td><?= formatDateORDateTime($oneLicence['enddate']) ?></td>
                            <?php }
                            $sd++;
                            if (in_array($sd, $settingsCheckbox)) { ?>
                              <td><?php
                                  if ($oneLicence['user_id'] != 0) {
                                    $user_id = $oneLicence['user_id'];
                                    $sql = queryGet("SELECT * FROM `tbl_branch_admin_details` WHERE `fldAdminKey`=$user_id");
                                    //lconsole($sql);
                                    echo $sql['data']['fldAdminName'];
                                  } else {
                                  ?>
                                  <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#addToLocation_<?= $oneLicence['licence_id'] ?>">Map</button>
                                <?php
                                  }
                                ?>

                              </td>
                            <?php }
                            $sd++;
                            if (in_array($sd, $settingsCheckbox)) { ?>
                              <td><?= ucfirst($oneLicence['licence_status']) ?></td>
                            <?php } ?>
                            <td>
                              <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $oneLicence['licence_id'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                            </td>
                          </tr>
                          <!-- right modal start here  -->
                          <div class="modal fade right customer-modal" id="fluidModalRightSuccessDemo_<?= $oneLicence['licence_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                              <!--Content-->
                              <div class="modal-content">
                                <!--Header-->
                                <div class="modal-header">
                                  <p class="heading lead text-right mt-2 mb-2"><?= $oneLicence['licence_code'] ?></p>
                                  <div class="display-flex-right mt-2 mb-2">
                                    <p class="text-sm"><?= $oneLicence['licence_title'] ?></p><br>
                                    <p class="text-xs text-italic"><?= $oneLicence['licence_desc'] ?></p>
                                  </div>

                                  <div class="display-flex-space-between mt-4 mb-3">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                      <li class="nav-item">
                                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= str_replace('/', '-', $oneLicence['licence_code']) ?>">Info</a>
                                      </li>

                                      <!-- -------------------Audit History Button Start------------------------- -->
                                      <li class="nav-item">
                                        <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $oneLicence['licence_code']) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $oneLicence['licence_code']) ?>" href="#history<?= str_replace('/', '-', $oneLicence['licence_code']) ?>" role="tab" aria-controls="history" aria-selected="false"><i class="fa fa-history mr-2"></i> Trail</a>
                                      </li>
                                      <!-- -------------------Audit History Button End------------------------- -->
                                    </ul>
                                    <div class="action-btns display-flex-gap" id="action-navbar">
                                      <?php ?>
                                      <form action="" method="POST">
                                        <a href="" name="vendorEditBtn">
                                          <i title="Edit" class="fa fa-edit po-list-icon"></i>
                                        </a>
                                        <a href="">
                                          <i title="Delete" class="fa fa-trash po-list-icon"></i>
                                        </a>
                                        <a href="">
                                          <i title="Toggle" class="fa fa-toggle-on po-list-icon"></i>
                                        </a>
                                      </form>
                                    </div>
                                  </div>

                                </div>
                                <!--Body-->
                                <div class="modal-body" style="padding: 0;">

                                  <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $oneLicence['licence_code']) ?>" role="tabpanel" aria-labelledby="home-tab">
                                      <div class="container my-3">
                                        <div class="card shadow-sm p-2">
                                          <p>Licence Code: <strong><?= $oneLicence['licence_code'] ?>(<?= $oneLicence['licence_type'] ?>)</strong></p>
                                          <p>OCR Blance: <strong><?= $oneLicence['ocr_limit'] ?></strong></p>
                                          <p>Transaction Blance: <strong><?= $oneLicence['transaction_limit'] ?></strong></p>
                                          <p>Expire Date: <strong><?= formatDateORDateTime($oneLicence['enddate'], true) ?></strong></p>
                                        </div>
                                      </div>
                                    </div>
                                    <!-- -------------------Audit History Tab Body Start------------------------- -->
                                    <div class="tab-pane fade" id="history<?= str_replace('/', '-', $oneLicence['licence_code']) ?>" role="tabpanel" aria-labelledby="history-tab">

                                      <div class="audit-head-section mb-3 mt-3 ">
                                        <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($oneLicence['lincence_created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneLicence['lincence_created_at']) ?></p>
                                        <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($oneLicence['lincence_updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneLicence['lincence_updated_at']) ?></p>
                                      </div>
                                      <hr>
                                      <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $oneLicence['licence_code']) ?>">

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
                                <!--/.Content-->
                              </div>
                            </div>
                            <!-- right modal end here  -->



                            <!-----add form modal start --->
                            <div class="modal fade hsn-dropdown-modal" id="addToLocation_<?= $oneLicence['licence_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                              <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                                <div class="modal-content card">
                                  <div class="modal-header card-header p-3">
                                    <h4>Map Licence</h4>
                                  </div>

                                  <div class="modal-body card-body p-3">
                                    <form id="addLicenceForm" class="addLicenceForm addLicenceForm_<?= $rand ?>">
                                      <!-- <input type="hidden" name="createLocationItem" id="createLocationItem" value=""> -->
                                      <input type="hidden" name="licence" class="select2 form-control licence_<?= $rand ?> " value="<?= $oneLicence['licence_id']  ?>">
                                      <div class="form-input">
                                        <label class="label" for="">Select Licence</label>
                                        <select id="user_id" name="user_id" class="select2 form-control user_id_<?= $rand ?>">
                                          <option>Select User</option>
                                          <?php

                                          if ($oneLicence['licence_type'] == "Creator") {
                                            $sql = "SELECT `tbl_branch_admin_details`.*," . ERP_COMPANIES . ".company_code," . ERP_COMPANIES . ".company_name," . ERP_BRANCHES . ".branch_code," . ERP_BRANCHES . ".state," . ERP_BRANCH_OTHERSLOCATION . ".othersLocation_code," . ERP_BRANCH_OTHERSLOCATION . ".othersLocation_location,`tbl_branch_admin_roles_a2`.fldRoleName,`tbl_branch_admin_roles_a2`.fldRoleAccesses,`tbl_branch_admin_roles_a2`.grandParent,`tbl_branch_admin_roles_a2`.subParent,`tbl_branch_admin_roles_a2`.subChild FROM `tbl_branch_admin_details`," . ERP_COMPANIES . "," . ERP_BRANCHES . "," . ERP_BRANCH_OTHERSLOCATION . ",`tbl_branch_admin_roles_a2` WHERE `tbl_branch_admin_details`.`fldAdminRole`=`tbl_branch_admin_roles_a2`.`fldRoleKey` AND `tbl_branch_admin_details`.`fldAdminCompanyId`=" . ERP_COMPANIES . ".`company_id` AND `tbl_branch_admin_details`.`fldAdminBranchId`=" . ERP_BRANCHES . ".`branch_id` AND `tbl_branch_admin_details`.`fldAdminBranchLocationId`=" . ERP_BRANCH_OTHERSLOCATION . ".`othersLocation_id` AND `tbl_branch_admin_details`.`fldAdminCompanyId`='" . $company_id . "' AND `tbl_branch_admin_details`.`fldAdminStatus`!='deleted' AND (`tbl_branch_admin_details`.`fldAdminBranchLocationId` IS NOT NULL)AND `tbl_branch_admin_roles_a2`.`fldRoleStatus`!='deleted' AND tbl_branch_admin_details.user_type='Creator' AND tbl_branch_admin_details.licence_id = 0";

                                            $sql_get = queryGet($sql, true);
                                            $sql_data = $sql_get['data'];
                                            // console($sql_data);
                                            foreach ($sql_data as $data) {
                                          ?>
                                              <option value="<?= $data['fldAdminKey'] ?>"><?= $data['fldAdminName'] . "(" . $data['fldAdminEmail'] . ")" ?></option>
                                            <?php
                                            }
                                          } elseif ($oneLicence['licence_type'] == "Approver") {
                                            $sql = "SELECT `tbl_branch_admin_details`.*," . ERP_COMPANIES . ".company_code," . ERP_COMPANIES . ".company_name," . ERP_BRANCHES . ".branch_code," . ERP_BRANCHES . ".state," . ERP_BRANCH_OTHERSLOCATION . ".othersLocation_code," . ERP_BRANCH_OTHERSLOCATION . ".othersLocation_location,`tbl_branch_admin_roles_a2`.fldRoleName,`tbl_branch_admin_roles_a2`.fldRoleAccesses,`tbl_branch_admin_roles_a2`.grandParent,`tbl_branch_admin_roles_a2`.subParent,`tbl_branch_admin_roles_a2`.subChild FROM `tbl_branch_admin_details`," . ERP_COMPANIES . "," . ERP_BRANCHES . "," . ERP_BRANCH_OTHERSLOCATION . ",`tbl_branch_admin_roles_a2` WHERE `tbl_branch_admin_details`.`fldAdminRole`=`tbl_branch_admin_roles_a2`.`fldRoleKey` AND `tbl_branch_admin_details`.`fldAdminCompanyId`=" . ERP_COMPANIES . ".`company_id` AND `tbl_branch_admin_details`.`fldAdminBranchId`=" . ERP_BRANCHES . ".`branch_id` AND `tbl_branch_admin_details`.`fldAdminBranchLocationId`=" . ERP_BRANCH_OTHERSLOCATION . ".`othersLocation_id` AND `tbl_branch_admin_details`.`fldAdminCompanyId`='" . $company_id . "' AND `tbl_branch_admin_details`.`fldAdminStatus`!='deleted' AND (`tbl_branch_admin_details`.`fldAdminBranchLocationId` IS NOT NULL)AND `tbl_branch_admin_roles_a2`.`fldRoleStatus`!='deleted' AND tbl_branch_admin_details.user_type='Approver' AND tbl_branch_admin_details.licence_id = 0";
                                            $sql_get = queryGet($sql, true);
                                            $sql_data = $sql_get['data'];
                                            // console($sql_data);
                                            foreach ($sql_data as $data) {
                                            ?>
                                              <option value="<?= $data['fldAdminKey'] ?>"><?= $data['fldAdminName'] . "(" . $data['fldAdminEmail'] . ")" ?></option>
                                          <?php
                                            }
                                          }

                                          ?>

                                        </select>
                                      </div>
                                    </form>
                                  </div>
                                  <div class="modal-footer">
                                    <button class="btn btn-primary save-close-btn float-right addLicence" value="<?= $rand ?>">Submit</button>
                                  </div>

                                </div>
                              </div>
                            </div>
                          </div>


                          <!---end modal --->


                        <?php } ?>
                      </tbody>
                      <tbody>
                        <tr>
                          <td colspan="<?= $settingsCheckboxCount + 2; ?>">
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
              <!---------------------------------Table settings Model Start--------------------------------->
              <div class="modal" id="myModal2">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title">Table Column Settings</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                      <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                      <input type="hidden" name="pageTableName" value="ERP_COMPANY_LICENCE" />
                      <div class="modal-body">
                        <div id="dropdownframe"></div>
                        <div id="main2">
                          <?php $sm = 0; ?>
                          <table>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php $sm++;
                                                                                            echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?= $sm; ?>" />
                                Licence Code</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php $sm++;
                                                                                            echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?= $sm; ?>" />
                                Licence Type</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php $sm++;
                                                                                            echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="<?= $sm; ?>" />
                                Package Name</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php $sm++;
                                                                                            echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?= $sm; ?>" />
                                Package Desc/td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php $sm++;
                                                                                            echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?= $sm; ?>" />
                                OCR Blance</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php $sm++;
                                                                                            echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?= $sm; ?>" />
                                Transaction Blance</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php $sm++;
                                                                                            echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?= $sm; ?>" />
                                Expire Date</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php $sm++;
                                                                                            echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?= $sm; ?>" />
                                User</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php $sm++;
                                                                                            echo (in_array($sm, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?= $sm; ?>" />
                                Status</td>
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
  </div>
  </section>
  </div> <!-- For Pegination------->
  <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                echo  $_REQUEST['pageNo'];
                                              } ?>">
  </form>
  <!-- End Pegination from------->
<?php } ?>

<?php
require_once("common/footer.php");
?>

<script>
  $(document).on("click", ".dlt-popup", function() {
    $(this).parent().parent().remove();
  });

  function rm() {
    // $(event.target).closest("tr").remove();
    $(this).parent().parent().parent().remove();
  }


  $(document).on("click", ".add-btn-minus", function() {
    $(this).parent().parent().remove();
  });
</script>



<script>
  $(document).ready(function() {



    // get item details by id
    $(document).on("click", "#addToList", function() {
      var count = $("input[name='variant']:checked").length;
      if (count > 0) {
        var selectedValue = $('input[name=variant]:checked').val();
        $.ajax({
          type: "POST",
          url: `ajaxs/ajax-licence-items-list.php`,
          data: {
            selectedValue
          },
          beforeSend: function() {
            $("#addToList").toggleClass("disabled");
            $("#myModal3").modal('toggle');
            $(`#addToList`).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Processing...');
          },
          success: function(response) {
            console.log(response);
            $(`#addToList`).html('Add to List');
            $("#addToList").toggleClass("disabled");
            $("#itemsTable").append(response);
            $("#myModal3").modal('toggle');
            calculateGrandTotalAmount();
          }
        });
      } else {
        alert("Please select atlast one package!");
      }
    });
    $(document).on("click", ".delItemBtn", function() {
      // let id = ($(this).attr("id")).split("_")[1];
      // $(`#delItemRowBtn_${id}`).remove();
      $(this).parent().parent().remove();
      calculateGrandTotalAmount();
    });

    $(document).on("keyup change", ".qty", function() {
      let id = $(this).val();
      var sls = $(this).attr("sls");
      alert(sls);
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-items-list.php`,
        data: {
          act: "totalPrice",
          packageVariantId: "ss",
          id
        },
        beforeSend: function() {
          $(".totalPrice").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          console.log(response);
          $(".totalPrice").html(response);
        }
      });
    });

    // 🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴
    // auto calculation 
    function calculateGrandTotalAmount() {
      let totalAmount = 0;
      let totalTaxAmount = 0;
      let totalDiscountAmount = 0;
      $(".itemTotalPrice").each(function() {
        totalAmount += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      });
      // $(".itemTotalTax").each(function() {
      //   var taxPercent=18;
      //   var taxPar=100+taxPercent;
      //   var taxBackCalculation=$totalPrice-totalAmount*100/taxPar;

      //   totalTaxAmount += (parseFloat($(this).html()) > 0) ? parseFloat($(this).html()) : 0;
      // });
      var taxPercent = 18;
      var taxPar = 100 + taxPercent;
      var taxBackCalculation = totalAmount - totalAmount * 100 / taxPar;

      totalTaxAmount = taxBackCalculation;
      // $(".itemTotalDiscount").each(function() {
      //   totalDiscountAmount += (parseFloat($(this).html()) > 0) ? parseFloat($(this).html()) : 0;
      // });

      console.log("Grand = ", totalAmount, totalTaxAmount, totalDiscountAmount);
      let grandSubTotalAmt = totalAmount - totalTaxAmount - totalDiscountAmount;
      $("#grandSubTotalAmt").html(grandSubTotalAmt.toFixed(2));
      // $("#grandTotalDiscount").html(totalDiscountAmount.toFixed(2));
      $("#grandTaxAmt").html(totalTaxAmount.toFixed(2));
      $("#grandTotalAmt").html(totalAmount.toFixed(2));

      $("#subTotal").val(grandSubTotalAmt.toFixed(2));
      // $("#grandTotalDiscount").html(totalDiscountAmount.toFixed(2));
      $("#taxAmount").val(totalTaxAmount.toFixed(2));
      $("#totalAmt").val(totalAmount.toFixed(2));
    }

    function calculateOneItemAmounts(rowNo) {
      let itemQty = (parseFloat($(`#itemQty_${rowNo}`).val()) > 0) ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
      let itemUnitPrice = (parseFloat($(`#itemUnitPrice_${rowNo}`).val()) > 0) ? parseFloat($(`#itemUnitPrice_${rowNo}`).val()) : 0;
      let itemDiscount = (parseFloat($(`#itemDiscount_${rowNo}`).val())) ? parseFloat($(`#itemDiscount_${rowNo}`).val()) : 0;
      let itemTax = (parseFloat($(`#itemTax_${rowNo}`).val())) ? parseFloat($(`#itemTax_${rowNo}`).val()) : 0;

      $(`#multiQuantity_${rowNo}`).val(itemQty);

      let basicPrice = itemUnitPrice * itemQty;
      let totalDiscount = basicPrice * itemDiscount / 100;
      let priceWithDiscount = basicPrice - totalDiscount;
      let totalTax = priceWithDiscount * itemTax / 100;
      let totalItemPrice = priceWithDiscount + totalTax;

      console.log(itemQty, itemUnitPrice, itemDiscount, itemTax);

      $(`#itemTotalDiscount_${rowNo}`).html(totalDiscount.toFixed(2));
      $(`#itemTotalDiscount1_${rowNo}`).val(totalDiscount.toFixed(0));
      $(`#itemTotalTax_${rowNo}`).html(totalTax.toFixed(2));
      $(`#itemTotalTax1_${rowNo}`).val(totalTax.toFixed(2));
      $(`#itemTotalPrice_${rowNo}`).val(totalItemPrice.toFixed(2));
      $(`#itemTotalPrice1_${rowNo}`).html(totalItemPrice.toFixed(2));
      $(`#mainQty_${rowNo}`).html(itemQty);
      calculateGrandTotalAmount();
    }

    // #######################################################
    function calculateQuantity(rowNo, packageVariantId, thisVal) {
      // console.log("code", rowNo);
      let itemQty = (parseFloat($(`#itemQty_${packageVariantId}`).val()) > 0) ? parseFloat($(`#itemQty_${packageVariantId}`).val()) : 0;
      let totalQty = 0;
      // console.log("calculateQuantity() ========== Row:", rowNo);
      // console.log("Total qty", itemQty);
      $(".multiQuantity").each(function() {
        if ($(this).data("packageVariantId") == packageVariantId) {
          totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
          // console.log('Qtys":', $(this).val());
        }
      });

      let avlQty = itemQty - totalQty;

      // console.log("Avl qty:", avlQty);

      if (avlQty < 0) {
        let totalQty = 0;
        $(`#multiQuantity_${rowNo}`).val('');
        $(".multiQuantity").each(function() {
          if ($(this).data("packageVariantId") == packageVariantId) {
            totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            // console.log('Qtys":', $(this).val());
          }
        });
        let avlQty = itemQty - totalQty;

        $(`#mainQtymsg_${packageVariantId}`).show();
        $(`#mainQtymsg_${packageVariantId}`).html("[Error! Delivery QTY should equal to order QTY.]");
        $(`#mainQty_${packageVariantId}`).html(avlQty);
      } else {
        let totalQty = 0;
        $(".multiQuantity").each(function() {
          if ($(this).data("packageVariantId") == packageVariantId) {
            totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            // console.log('Qtys":', $(this).val());
          }
        });

        let avlQty = itemQty - totalQty;

        $(`#mainQtymsg_${packageVariantId}`).hide();
        $(`#mainQty_${packageVariantId}`).html(avlQty);
      }
      if (avlQty == 0) {
        $(`#saveClose_${packageVariantId}`).show();
        $(`#saveCloseLoading_${packageVariantId}`).hide();
      } else {
        $(`#saveClose_${packageVariantId}`).hide();
        $(`#saveCloseLoading_${packageVariantId}`).show();
        $(`#setAvlQty_${packageVariantId}`).html(avlQty);
      }
    }

    function itemMaxDiscount(rowNo, keyValue = 0) {
      let itemMaxDis = $(`#itemMaxDiscount_${rowNo}`).html();
      console.log('this is max discount', itemMaxDis);
      console.log('this is key value', keyValue);
      if (parseFloat(keyValue) > parseFloat(itemMaxDis)) {
        console.log('max discount is over');
        $(`#itemBasePrice_${rowNo}`).text(`Special Discount`);
        $(`#itemBasePrice_${rowNo}`).show();
        // $(`#specialDiscount`).show();
      } else {
        $(`#itemBasePrice_${rowNo}`).hide();
        // $(`#specialDiscount`).hide();
      }
    }

    $(document).on("keyup blur click", ".itemQty", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      calculateOneItemAmounts(rowNo);
    });

    function checkSpecialDiscount() {
      let isSpecialDiscountApplied = false;

      $(".itemDiscount").each(function() {
        let rowNum = ($(this).attr("id")).split("_")[1];
        let discountPercentage = parseFloat($(this).val());
        discountPercentage = discountPercentage > 0 ? discountPercentage : 0;
        let maxDiscountPercentage = parseFloat($(`#itemMaxDiscount_${rowNum}`).html());
        maxDiscountPercentage = maxDiscountPercentage > 0 ? maxDiscountPercentage : 0;
        if (discountPercentage > maxDiscountPercentage) {
          isSpecialDiscountApplied = true;
        }
      });

      if (isSpecialDiscountApplied) {
        $(`#approvalStatus`).val(`12`);
        console.log('max');
      } else {
        $(`#approvalStatus`).val(`14`);
        console.log('ok');
      }
    }


    $(document).on("keyup", ".itemDiscount", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let keyValue = $(this).val();
      calculateOneItemAmounts(rowNo);
      itemMaxDiscount(rowNo, keyValue);
      checkSpecialDiscount();
      // $(`#itemTotalDiscount1_${rowNo}`).attr('disabled', 'disabled');
    });

    // #######################################################
    $(document).on("keyup blur click change", ".multiQuantity", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let packageVariantId = ($(this).data("packageVariantId"));
      let thisVal = ($(this).val());
      calculateQuantity(rowNo, packageVariantId, thisVal);
    });

    // #######################################################
    $(document).on("keyup", ".itemTotalDiscount1", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let itemDiscountAmt = ($(this).val());

      let itemQty = (parseFloat($(`#itemQty_${rowNo}`).val()) > 0) ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
      let itemUnitPrice = (parseFloat($(`#itemUnitPrice_${rowNo}`).val()) > 0) ? parseFloat($(`#itemUnitPrice_${rowNo}`).val()) : 0;

      let totalAmt = itemQty * itemUnitPrice;
      let discountPercentage = itemDiscountAmt * 100 / totalAmt;

      $(`#itemDiscount_${rowNo}`).val(discountPercentage.toFixed(0));

      // let itemDiscount = (parseFloat($(`#itemDiscount_${rowNo}`).val())) ? parseFloat($(`#itemDiscount_${rowNo}`).val()) : 0;

      console.log('total', itemQty, itemUnitPrice, discountPercentage);
      calculateOneItemAmounts(rowNo);

      // $(`#itemDiscount_${rowNo}`).attr('disabled', 'disabled');
      // discountCalculate(rowNo, thisVal);
    });

    // allItemsBtn
    $("#allItemsBtn").on('click', function() {
      window.location.href = "";
    })

    // itemWiseSearch
    $("#itemWiseSearch").on('click', function() {
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-so-list.php`,
        data: {
          act: "itemWiseSearch"
        },
        beforeSend: function() {
          $(".tableDataBody").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          console.log(response);
          $(".tableDataBody").html(response);
        }
      });
    })

    $(function() {
      $("#datepicker").datepicker({
        autoclose: true,
        todayHighlight: true
      }).datepicker('update', new Date());
    });

  });

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






  $('#itemsDropDown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
  $('#customerDropDown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
  $('#profitCenterDropDown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
  $('#kamDropDown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });

  $(document).ready(function() {

    /*@ Registration start */
    $('.addLicence').click(function(event) {
      var attr = $(this).val();
      alert(attr);
      // alert(1);
      // $(document).on('submit', '#addLicence', function(event) {
      //     alert(1); 

      event.preventDefault();

      let formData = $(".addLicenceForm_" + (attr)).serialize();
      //    var user_id = $(".user_id_"+attr).val();
      //    alert(user_id)

      console.log(formData);
      $.ajax({

        type: "POST",

        url: `ajaxs/ajax-licence.php`,

        data: formData,

        beforeSend: function() {

          $(".addLicence").toggleClass("disabled");

          $(".addLicence").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

        },

        success: function(response) {

          console.log(response);

          $('.addLicenceForm').trigger("reset");

          $(".addNewPurchaseGroupFormModal").modal('toggle');

          $(".addLicence").html("Submit");

          $(".addLicence").toggleClass("disabled");
          location.reload();

        }

      });

    });
  });
</script>