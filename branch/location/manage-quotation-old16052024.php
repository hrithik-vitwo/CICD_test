<?php
include("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
include("../common/header.php");
include("../common/navbar.php");
include("../common/sidebar.php");
require_once("../common/pagination.php");
include("../../app/v1/functions/company/func-branches.php");
include("../../app/v1/functions/branch/func-branch-pr-controller.php");


// console($_SESSION);

if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"],);
}

if (isset($_POST["visit"])) {
  $newStatusObj = VisitBranches($_POST);
  redirect(BRANCH_URL);
}

if (isset($_POST["createdata"])) {
  $addNewObj = createDataBranches($_POST);
  if ($addNewObj["status"] == "success") {
    $branchId = base64_encode($addNewObj['branchId']);
    redirect($_SERVER['PHP_SELF'] . "?branchLocation=" . $branchId);
    swalToast($addNewObj["status"], $addNewObj["message"]);
    // console($addNewObj);
  } else {
    swalToast($addNewObj["status"], $addNewObj["message"]);
  }
}

if (isset($_POST["editdata"])) {
  $editDataObj = updateDataBranches($_POST);

  swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩  
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩  

$BranchPrObj = new BranchPr();

if (isset($_POST['addNewSOFormSubmitBtn'])) {
  //console($_SESSION);
  $addBranchPr = $BranchPrObj->addBranchPr($_POST);

  swalToast($addBranchPr["status"], $addBranchPr["message"]);
}
if (isset($_POST['addNewVendorList'])) {
  //console($_SESSION);
  $addBranchPr = $BranchPrObj->addVendorList($_POST);

  swalToast($addBranchPr["status"], $addBranchPr["message"]);
}
if (isset($_POST['addNewOtherVendor'])) {
  //console($_SESSION);
  $addBranchPr = $BranchPrObj->addOtherVendorList($_POST);

  swalToast($addBranchPr["status"], $addBranchPr["message"]);
}

if (isset($_GET['vendor-delete'])) {
  $id = $_GET['vendor-delete'];
  $addBranchPr = $BranchPrObj->deleteRfqVendor($_GET, $id);

  swalToast($addBranchPr["status"], $addBranchPr["message"]);
}

if (isset($_POST['addNewRFQFormSubmitBtn'])) {

  $addBranchPr = $BranchPrObj->addBranchRFQ($_POST);

  if ($addBranchPr["status"] == "success") {
    swalToast($addBranchPr["status"], $addBranchPr["message"], $_SERVER['PHP_SELF']);
  } else {
    swalToast($addBranchPr["status"], $addBranchPr["message"]);
  }
}



?>
<!-- <link rel="stylesheet" href="../../public/assets/manage-rfq.css">
<link rel="stylesheet" href="../../public/assets/animate.css"> -->

<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">



<div class="content-wrapper is-quotation">
  <!-- Content Header (Page header) -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- row -->
      <div class="row p-0 m-0">
        <div class="col-12 mt-2 p-0">
          <!-- <div class="p-0 pt-1 my-2">
              <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                  <h3 class="card-title">Manage Request For Quotations</h3>
                  <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?pr-creation" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></a>
                </li>
              </ul>
            </div> -->
          <div class="card card-tabs" style="border-radius: 20px;">
            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
              <div class="card-body">
                <div class="row filter-serach-row">
                  <div class="col-lg-1 col-md-1 col-sm-12">
                    <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position: absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                  </div>
                  <div class="col-lg-11 col-md-11 col-sm-12">
                    <div class="row table-header-item">
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="filter-search">
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
                      </div>
                    </div>
                  </div>

                  <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLongTitle">Filter
                            RFQ</h5>

                        </div>
                        <div class="modal-body">
                          <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                              <input type="text" name="keyword" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php if (isset($_REQUEST['keyword'])) {
                                                                                                                                                    echo $_REQUEST['keyword'];
                                                                                                                                                  } ?>">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                              <select id="pr" name="pr" class="fld form-control m-input">
                                <option value="">ALL</option>
                                <?php

                                $pr_query = "SELECT * FROM erp_branch_purchase_request WHERE company_id = '$company_id' AND branch_id = '$branch_id' AND location_id = '$location_id'";
                                $pr_query_list = queryGet($pr_query, true);
                                $pr_list = $pr_query_list['data'];
                                foreach ($pr_list as $pr_row) {
                                ?>
                                  <option value="<?= $pr_row['purchaseRequestId'] ?>" <?php if (isset($_GET['prid']) && $_GET['prid'] == $pr_row['purchaseRequestId']) echo ("selected"); ?>><?= $pr_row['prCode'] ?></option>
                                <?php
                                }
                                ?>
                              </select>
                            </div>

                            <!-- <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <select name="vendor_status_s" id="vendor_status_s" class="fld form-control" style="appearance: auto;">
                                  <option value=""> Status </option>
                                  <option value="active" <?php if (isset($_REQUEST['vendor_status_s']) && 'active' == $_REQUEST['vendor_status_s']) {
                                                            echo 'selected';
                                                          } ?>>Active
                                  </option>
                                  <option value="inactive" <?php if (isset($_REQUEST['vendor_status_s']) && 'inactive' == $_REQUEST['vendor_status_s']) {
                                                              echo 'selected';
                                                            } ?>>Inactive
                                  </option>
                                  <option value="draft" <?php if (isset($_REQUEST['vendor_status_s']) && 'draft' == $_REQUEST['vendor_status_s']) {
                                                          echo 'selected';
                                                        } ?>>Draft</option>
                                </select>
                              </div> -->
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
                          <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync fa-spin"></i>Reset</a>
                          <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                            Search</button>
                        </div>
                      </div>
                    </div>
                  </div>

            </form>
            <!-- <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog" aria-hidden="true"></i></a> -->
            <div class="tab-content" id="custom-tabs-two-tabContent">
              <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                <?php
                $cond = '';
                global $company_id;
                global $branch_id;
                global $location_id;
                // $sts = " AND `vendor_status` !='deleted'";
                // if (isset($_REQUEST['vendor_status_s']) && $_REQUEST['vendor_status_s'] != '') {
                //   $sts = ' AND vendor_status="' . $_REQUEST['vendor_status_s'] . '"';
                // }

                if (isset($_GET['prid']) && $_GET['prid'] != '') {
                  $cond .= " AND rfq.prId = '" . $_GET['prid'] . "'";
                }

                if (isset($_REQUEST['pr']) && $_REQUEST['pr'] != '') {
                  $cond .= " AND rfq.prId = '" . $_REQUEST['pr'] . "'";
                }

                if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                  $cond .= " AND rfq.created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                }

                if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                  $cond .= " AND (rfq.rfqCode like '%" . $_REQUEST['keyword'] . "%' OR rfq.prCode like '%" . $_REQUEST['keyword'] . "%' OR pr.refNo like '%" . $_REQUEST['keyword'] . "%')";
                }

                $sql_list = "SELECT * FROM `" . ERP_RFQ_LIST . "` as rfq LEFT JOIN `" . ERP_BRANCH_PURCHASE_REQUEST . "` as pr ON rfq.prId = pr.purchaseRequestId  WHERE 1 " . $cond . "  AND rfq.company_id='" . $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] . "' AND rfq.branch_id = '$branch_id' AND rfq.location_id = '$location_id' ORDER BY rfq.rfqId desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                $qry_list = queryGet($sql_list, true);
                $num_list = $qry_list['numRows'];


                $countShow = "SELECT count(*) FROM `" . ERP_RFQ_LIST . "` as rfq LEFT JOIN `" . ERP_BRANCH_PURCHASE_REQUEST . "` as pr ON rfq.prId = pr.purchaseRequestId  WHERE 1 " . $cond . "  AND rfq.company_id='" . $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] . "' AND rfq.branch_id = '$branch_id' AND rfq.location_id = '$location_id'";
                // console($countShow);
                $countQry = mysqli_query($dbCon, $countShow);
                $rowCount = mysqli_fetch_array($countQry);
                $count = $rowCount[0];
                // console($count);
                $cnt = $GLOBALS['start'] + 1;
                $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_RFQ_LIST", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                $settingsCheckbox = unserialize($settingsCh);
                if ($num_list > 0) {
                ?>
                  <table class="table defaultDataTable table-hover text-nowrap p-0 m-0">
                    <thead>
                      <tr class="alert-light">
                        <th>#</th>
                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                          <th>RFQ Code</th>
                        <?php }
                        if (in_array(2, $settingsCheckbox)) { ?>
                          <th>Vendor-Response Ratio</th>
                        <?php }
                        if (in_array(3, $settingsCheckbox)) { ?>
                          <th>Deadline Date</th>
                        <?php  }
                        if (in_array(4, $settingsCheckbox)) { ?>
                          <th>Created By</th>
                        <?php }
                        if (in_array(5, $settingsCheckbox)) { ?>
                          <th>View</th>

                        <?php } ?>


                      </tr>
                    </thead>

                    <tbody>
                      <?php
                      // console($BranchPrObj->fetchBranchSoListing()['data']);
                      $soList = $qry_list['data'];
                      foreach ($soList as $onePrList) {
                        //console($onePrList);
                        $rfq_code = $onePrList['rfqCode'];
                        $rfq_id = $onePrList['rfqId'];
                        $vendor_sql = "SELECT * FROM `" . ERP_RFQ_VENDOR_LIST . "` WHERE `rfqCode`='" . $rfq_code . "'";
                        $vendor_get = queryGet($vendor_sql, true);
                        $getvendordata = $vendor_get['data'];
                        $vendor_count = count($getvendordata);
                        $vendor_response_sql = "SELECT * FROM `erp_vendor_response` WHERE `rfq_code`='" . $rfq_code . "'";
                        $vendor_response_get = queryGet($vendor_response_sql, true);
                        $getvendor_response = $vendor_response_get['data'];
                        $vendor_response_count = count($getvendor_response);
                        // console($vendor_response_get);

                      ?>
                        <tr style="cursor:pointer">
                          <td><?= $cnt++ ?></td>
                          <?php if (in_array(1, $settingsCheckbox)) { ?>
                            <td><?= $onePrList['rfqCode'] ?></td>
                          <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                            <td><?= $vendor_response_count ?>/<?= $vendor_count ?></td>
                          <?php }
                          if (in_array(3, $settingsCheckbox)) { ?>
                            <td><?= $onePrList['expectedDate'] ?></td>
                          <?php }
                          if (in_array(4, $settingsCheckbox)) { ?>
                            <td><?= getCreatedByUser($onePrList['created_by']) ?></td>
                          <?php }
                          if (in_array(5, $settingsCheckbox)) { ?>
                            <td> <a href="matrix.php?rfq=<?= $rfq_id ?>" title="View Vendor Response Matrix" style="text-decoration:none;">
                                <i class="fa fa-eye po-list-icon"></i>
                              </a>
                            </td>
                          <?php } ?>
                          <!-- <td>

                              <a style="cursor: pointer;" class="btn btn-sm" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $onePrList['rfqId'] ?>"><i class="fa fa-info po-list-icon"></i></a>
                            </td> -->
                        </tr>
                        <!-- right modal start here  -->
                        <div class="modal fade right" id="fluidModalRightSuccessDemo_<?= $onePrList['rfqId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                          <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                            <div class="modal-content">
                              <div class="modal-header " style="background: none; border:none; color:#424242">

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true" class="white-text">×</span>
                                </button>
                              </div>
                              <div class="modal-body" style="padding: 0;">
                                <a href="<?= LOCATION_URL ?>matrix.php?rfq=<?= $onePrList['rfqId'] ?>" class="btn btn-primary float-right mr-2">Matrix</a>


                                <ul class="nav nav-tabs" style="padding-left: 16px;" id="myTab" role="tablist">
                                  <li class="nav-item">
                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= $onePrList['rfqId'] ?>">RFQ-Info</a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link" id="vendor-tab" data-toggle="tab" href="#vendor<?= $onePrList['rfqId'] ?>">RFQ-Vendor List</a>
                                  </li>
                                </ul>

                                <div class="tab-content" id="myTabContent">
                                  <div class="tab-pane fade show active" id="home<?= $onePrList['rfqId'] ?>" role="tabpanel" aria-labelledby="home-tab">
                                    <div class="col-md-12">
                                      <div class="shadow-sm bg-light py-2 mx-2 my-2" id="action-navbar" style="text-align:right">
                                        <form action="" method="POST">


                                      </div>
                                    </div>
                                    <div class="row px-3 p-0 m-0 mb-2" style="place-items: self-start;">
                                      <div class="col-md-12">
                                        <div class="shadow-sm py-2 px-2" style="background: #dfdfdf; margin-bottom: 15px;">
                                          Existing Vendor
                                          </a>
                                        </div>
                                      </div>




                                      <div class="col-md-12">
                                        <div class="row border mx-2 mt-n2 py-2 shadow-sm bg-light">
                                          <div class="col-md-6">
                                            <span class="text-secondary"><strong>RFQ Code:</strong>
                                              <?= $onePrList['rfqCode'] ?></span>
                                          </div>
                                          <div class="col-md-6">
                                            <span class="text-secondary"><strong>Expected Date:</strong>
                                              <?= $onePrList['expectedDate'] ?></span>
                                          </div>
                                          <div class="col-md-6">
                                            <span class="text-secondary"><strong>Reference Number :</strong>
                                              <?= $onePrList['refNo'] ?></span>
                                          </div>
                                          <div class="col-md-6">
                                            <span class="text-secondary"><strong>Status :</strong> <?php if ($onePrList['status'] != null) {
                                                                                                      echo $onePrList['status'];
                                                                                                    } else {
                                                                                                      echo "PENDING";
                                                                                                    }  ?></span>
                                          </div>

                                        </div>
                                      </div>
                                    </div>




                                    <div class="row px-3 p-0 m-0 mb-2" style="place-items: self-start;">
                                      <div class="col-md-12">
                                        <div class="shadow-sm py-2 px-2" style="background: #dfdfdf; margin-bottom: 15px;">
                                          Items
                                        </div>
                                      </div>
                                      <?php

                                      $itemDetails = $BranchPrObj->fetchBranchRFQItems($onePrList['rfqId'])['data'];
                                      // console($itemDetails);
                                      // console($_POST);
                                      foreach ($itemDetails as $oneItem) {
                                        // console('hello imran');
                                      ?>
                                        <div class="col-md-12">
                                          <div class="row border shadow-sm bg-light" style="flex-direction: row; align-items: center;">

                                            <div class="">
                                              <div class="accordion accordion-flush" id="accordionFlushExample">
                                                <div class="accordion-item">
                                                  <h2 class="accordion-header" id="flush-headingOne">
                                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne-<?= $oneItem['itemId'] ?>" aria-expanded="true" aria-controls="collapseOne">
                                                      <strong> <?= $oneItem['itemName'] ?> </strong>
                                                    </button>
                                                  </h2>
                                                  <div id="collapseOne-<?= $oneItem['itemId'] ?>" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body">
                                                      <div class="row">
                                                        <div class="col-md-6">
                                                          <span class="text-secondary"><strong>Item Code:</strong>
                                                            <?= $oneItem['itemCode'] ?></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                          <span class="text-secondary"><strong>Item Name:</strong>
                                                            <?= $oneItem['itemName'] ?> </span>
                                                        </div>
                                                        <div class="col-md-6">
                                                          <span class="text-secondary"><strong>Item Id :</strong>
                                                            <?= $oneItem['itemId'] ?>
                                                          </span>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                            </form>
                                            <!-- <div class="col-md-6">
                                              <span class="text-secondary"><strong>Item Code:</strong> <?= $oneItem['itemCode'] ?></span>
                                            </div>
                                            <div class="col-md-6">
                                              <span class="text-secondary"><strong>Item Name:</strong> <?= $oneItem['itemName'] ?> </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span class="text-secondary"><strong>Item Id :</strong> <?= $oneItem['itemId'] ?></span>
                                            </div> -->
                                          </div>
                                        </div>
                                      <?php } ?>
                                    </div>
                                  </div>
                                  <div class="tab-pane fade" id="vendor<?= $onePrList['rfqId'] ?>" role="tabpanel" aria-labelledby="vendor-tab">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <div class="float-add-btn-center">
                                          <a href="<?= $_SERVER["PHP_SELF"]; ?>?sendEmail=<?= $onePrList['rfqId'] ?>" class="btn btn-primary btn--shockwave is-active vendor-list-add-btn-modal">
                                            <i class="fa fa-plus text-white"></i>
                                          </a>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="row rfq-vendor-list-row">
                                      <div class="col-md-4 text-center rfq-vendor-list-col border">
                                        <p><b>Vendor Code</b></p>
                                      </div>
                                      <div class="col-md-4 text-center rfq-vendor-list-col border">
                                        <p><b>Vendor Name</b></p>
                                      </div>
                                      <div class="col-md-4 text-center rfq-vendor-list-col border">
                                        <p><b>Email Id</b></p>
                                      </div>
                                    </div>
                                    <?php
                                    // console($BranchPrObj->fetchBranchSoListing()['data']);
                                    $rfqId = $onePrList['rfqId'];
                                    // console($rfqId);
                                    $rfqVendor = $BranchPrObj->fetchRFQVendor($rfqId)['data'];
                                    foreach ($rfqVendor as $onerfqVendor) {
                                    ?>
                                      <div class="row rfq-vendor-list-row-value">
                                        <div class="col-md-4 text-center rfq-vendor-list-col border">
                                          <p><?= $onerfqVendor['vendorCode'] ?></p>
                                        </div>
                                        <div class="col-md-4 text-center rfq-vendor-list-col border">
                                          <p><?= $onerfqVendor['vendor_name'] ?></p>
                                        </div>
                                        <div class="col-md-4 text-center rfq-vendor-list-col border">
                                          <p><?= $onerfqVendor['vendor_email'] ?></p>
                                        </div>
                                      </div>
                                    <?php } ?>


                                  </div>
                                </div>

                              </div>
                            </div>
                            <!--/.Content-->
                          </div>
                        </div>
                      <?php } ?>
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


                    <!-- right modal end here  -->

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
                    <input type="hidden" name="pageTableName" value="ERP_VENDOR_DETAILS" />
                    <div class="modal-body">
                      <div id="dropdownframe"></div>
                      <div id="main2">
                        <table>
                          <tr>
                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                              RFQ Code</td>
                          </tr>
                          <tr>
                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                              Vendor Response Ratio </td>
                          </tr>
                          <tr>
                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                              Deadline date</td>
                          </tr>
                          <tr>
                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                              Created By</td>
                          </tr>
                          <tr>
                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                              View Matrix</td>
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
</div>
<!-- End Pegination from------->

<form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
  <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                              echo  $_REQUEST['pageNo'];
                                            } ?>">
</form>


<?php
include("../common/footer.php");
?>
<script>
  $(document).on("click", ".remove_row", function() {

    var value = $(this).data('value');

    for (let l = 0; l < test.length; l++) {
      var array_each = test[l].split("|");
      if (array_each[0].includes(value) == true) {
        test.splice(l, 1);
      }
    }
    $(this).parent().parent().remove();
  })


  $(document).on("click", ".remove_row_other", function() {
    $(this).parent().parent().remove();
  })

  function rm() {
    $(event.target).closest("<div class='row others-vendor'>").remove();
  }


  function addMultiQty(id) {
    let addressRandNo = Math.ceil(Math.random() * 100000);



    $(`.modal-add-row_${id}`).append(`
                  <div class="modal-body">
                      <div class="row">
                        <div class="col-lg-5 col-md-5 col-sm-12">
                          <span class="has-float-label">
                            <input type="text" name="OthersVendor[${addressRandNo}][name]"  class="form-control each_name" placeholder = "Vendor Name"/>
                            <label for="date">Vendor Name</label>
                          </span>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-12">
                          <span class="has-float-label">
                            <input type="text" name="OthersVendor[${addressRandNo}][email]"  class="form-control each_email" placeholder = "Vendor Email"/>
                            <label for="date">Vendor Email</label>
                          </span>
                        </div>
                        <div class="col-lg-2 col-md-2 text-center remove_row_other" data-value = "${addressRandNo}">
                           <a class="btn btn-danger" type="button">
                             <i class="fa fa-minus"></i></a>
                         </div>
                      </div>
                  </div>`);



    // $(`.modal-add-row_${id}`).append(` <div class="col-lg-5 col-md-5 col-sm-12">
    //                       <span class="has-float-label">
    //                         <input type="label" name="OthersVendor[${addressRandNo}][name]" class="form-control" />
    //                         <label for="v_email"></label>
    //                       </span>
    //                     </div>
    //                                       <div class="col-lg-5 col-md-5 col-sm-12">
    //                       <span class="has-float-label">
    //                         <input type="label" name="OthersVendor[${addressRandNo}][email]" class="form-control" />
    //                         <label for="v_email"></label>
    //                       </span>
    //                     </div>
    //                     <div class="col-lg-2 col-md-2 text-center">
    //                       <a class="btn btn-danger" type="button">
    //                         <i class="fa fa-minus"></i></a>
    //                     </div>`);
  }
</script>
<script>
  $(document).ready(function() {
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
    // customers ********************************
    function loadCustomers() {
      $.ajax({
        type: "GET",
        url: `ajaxs/pr/ajax-customers.php`,
        beforeSend: function() {
          $("#customerDropDown").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          $("#customerDropDown").html(response);
        }
      });
    }
    loadCustomers();
    // get customer details by id
    $("#customerDropDown").on("change", function() {
      let itemId = $(this).val();
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-customers-list.php`,
        data: {
          act: "listItem",
          itemId
        },
        beforeSend: function() {
          $("#customerInfo").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          console.log(response);
          $("#customerInfo").html(response);
        }
      });
    });
    // **************************************
    function loadItems() {
      $.ajax({
        type: "GET",
        url: `ajaxs/pr/ajax-items.php`,
        beforeSend: function() {
          $("#itemsDropDown").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          $("#itemsDropDown").html(response);
        }
      });
    }
    loadItems();
    // get item details by id
    $("#itemsDropDown").on("change", function() {
      let itemId = $(this).val();
      $.ajax({
        type: "GET",
        url: `ajaxs/pr/ajax-items-list.php`,
        data: {
          act: "listItem",
          itemId
        },
        beforeSend: function() {
          //  $("#itemsTable").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          console.log(response);
          $("#itemsTable").append(response);
        }
      });
    });
    $(document).on("click", ".delItemBtn", function() {
      // let id = ($(this).attr("id")).split("_")[1];
      // $(`#delItemRowBtn_${id}`).remove();
      $(this).parent().parent().remove();
    })
    $(document).on('submit', '#addNewItemForm', function(event) {
      event.preventDefault();
      let formData = $("#addNewItemsForm").serialize();
      $.ajax({
        type: "POST",
        url: `ajaxs/so/ajax-items.php`,
        data: formData,
        beforeSend: function() {
          $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
          $("#addNewItemsFormSubmitBtn").html(
            '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...'
          );
        },
        success: function(response) {
          $("#goodTypeDropDown").html(response);
          $('#addNewItemsForm').trigger("reset");
          $("#addNewItemsFormModal").modal('toggle');
          $("#addNewItemsFormSubmitBtn").html("Submit");
          $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
        }
      });
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
          itemId: "ss",
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
    })
  })
</script>