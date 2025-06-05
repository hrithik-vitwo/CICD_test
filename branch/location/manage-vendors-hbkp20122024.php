<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-vendors.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/branch/func-grn-controller.php");


if (isset($_POST['changeStatus'])) {
  //console($_POST);
  $status = $_POST['status'];
  $id = $_POST['id'];
  if ($status == 'active') {

    $update = queryUpdate("UPDATE `erp_vendor_details` SET `vendor_status` = 'inactive' WHERE `vendor_id` = $id");
  } else {
    $update =  queryUpdate("UPDATE `erp_vendor_details` SET `vendor_status` = 'active' WHERE `vendor_id` = $id");
  }

  if ($update['status'] == 'success') {
    swalToast('Success', 'Vendor status changed successfully');
  } else {
    swalToast('Warning', 'Something went wrong');
  }
}


// if (isset($_POST["changeStatus"])) {
//   $newStatusObj = ChangeStatusVendor($_POST, "vendor_id", "vendor_status");
//   swalToast($newStatusObj["status"], $newStatusObj["message"]);
// }


if (isset($_POST["createdata"])) {
  $addNewObj = createDataVendor($_POST);

  // console($addNewObj);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}
if (isset($_GET['delete'])) {
  // echo 1;
  $VendorId = base64_decode($_GET['delete']);
  $del = queryUpdate("UPDATE `erp_vendor_details` SET `vendor_status`='deleted' WHERE `vendor_id`=$VendorId");
  //console($del);

  if ($del['status'] == "success") {
    swalToast("success", "Deleted Successfully", "manage-vendors.php");
  } else {
    swalToast("warning", "Something Went Wrong", "manage-vendors.php");
  }
}
if (isset($_POST["editData"])) {
  //console($_SESSION);
  $editDataObj = updateDataVendor($_POST);

  swalToast($editDataObj["status"], $editDataObj["message"]);
}


if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
} ?>

<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
  .multisteps-form__progress .multisteps-form__progress-btn {
    pointer-events: none;
  }

  .custom-date-div {
    opacity: 0;
    position: fixed;
    top: 0;
    right: 370px;
    width: auto;
    height: auto;
    background-color: #ffffff;
    border: 1px solid #ccc;
    padding: 7px;
    border-radius: 5px;
    z-index: 9;
    transition: opacity 0.3s ease-in-out;
    /* Animation transition */
  }

  .close-btn {
    position: relative;
    top: 0;
    right: 0;
    background: none;
    border: none;
    cursor: pointer;
    color: #333;
  }


  .vendor-modal .modal-header {
    height: 260px !important;
    padding: 10px 30px;
  }

  h2.accordion-header {
    display: block !important;
  }

  .matrix-card .row:nth-child(1):hover {

    pointer-events: none;

  }

  .matrix-card .row:hover {

    border-radius: 0 0 10px 10px;

  }

  .matrix-card .row:nth-child(1) {

    background: #fff;

  }

  .matrix-card .row .col {

    display: flex;

    align-items: center;

  }

  .matrix-accordion button {

    color: #fff;

    border-radius: 15px !important;

    /* margin: 20px 0; */

  }

  .accordion-button:not(.collapsed) {

    color: #fff;

  }

  .accordion-button::after {

    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");

  }

  .accordion-button:not(.collapsed)::after {

    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");

  }

  .accordion-item {

    border-radius: 15px !important;

    margin-bottom: 2em;

    border: 0;

  }

  .info-h4 {

    font-size: 20px;

    font-weight: 600;

    color: #003060;

    padding: 0px 10px;

  }


  .phone-alt-number,
  .email-alt {
    display: flex;
    justify-content: start;
    gap: 3px;
    align-items: center;
    font-size: 14px;
  }

  .phone-alt-number a,
  .email-alt a {
    color: #fff;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .vendor-modal .nav.nav-tabs li.nav-item a.nav-link {
    font-size: 12px;
  }


  .display-flex-space-between .hamburger.show:hover .tab-content {
    filter: blur(5px) !important;
  }

  .display-flex-space-between .nav-action {
    width: 40px;
    height: 40px;
    bottom: 52px;
    right: 88px;
  }

  .display-flex-space-between .nav-action a {
    display: flex;
  }

  .display-flex-space-between #reminder.show {
    transform: translateX(-500%);
  }

  .display-flex-space-between #thumb.show {
    transform: translateX(-375%);
  }

  .display-flex-space-between #create.show {
    transform: translateX(-250%);
  }

  .display-flex-space-between #edit.show {
    transform: translateX(-125%);
  }

  .vendor-modal .nav.nav-tabs li.nav-item a.nav-link {
    font-size: 12px;
  }



  .blur-body .tab-content .tab-pane:nth-child(5) .card.mb-0 {
    background: transparent;
    max-width: 97%;
  }

  .blur-body .tab-content .tab-pane:nth-child(5) .card .card-header {
    border-bottom: 1px solid #0000001c;
    border-radius: 0;
    background: transparent;
    padding: 0;
  }

  .blur-body .tab-content .tab-pane:nth-child(5) .card .card-header h4 {
    font-size: 10px;
    color: #012141;
    font-weight: 600;
    display: flex;
    align-items: center;
  }

  .blur-body .tab-content .tab-pane:nth-child(5) .card .card-header h4 ion-icon {
    font-size: 13px;
    color: #003060;
  }

  .blur-body .tab-content .tab-pane:nth-child(5) .card .card-body {
    margin-top: 15px;
  }

  .blur-body .tab-content .tab-pane:nth-child(5) .card .card-body p,
  .blur-body .tab-content .tab-pane:nth-child(5) .card .card-body span,
  .blur-body .tab-content .tab-pane:nth-child(5) .card .card-body th,
  .blur-body .tab-content .tab-pane:nth-child(5) .card .card-body td {
    font-size: 10px;
  }

  .blur-body .tab-content .tab-pane:nth-child(5) .card .card-body p {
    font-weight: 600;
    margin: 3px 0 8px;
  }

  .blur-body .tab-content .tab-pane:nth-child(5) .card .card-body span {
    font-weight: 400 !important;
    color: #909090;
  }

  .gst-return-data table th {
    background: #c5ced6 !important;
    color: #000 !important;
    font-weight: 600;
  }

  .gst-return-data table td {
    background: #fff !important;
    border-bottom: 1px solid #cccccc12 !important;
    border-color: #a0a0a06e !important;
    padding: 7px 16px;
    font-weight: 600;
  }

  .gst-return-data table tr:nth-child(2n) td {
    background-color: #fff !important;
  }

  .row .col.col-head {
    font-size: 10px;
    color: #777;
    font-weight: 600;
    padding: 10px 7px;
    border-bottom: 2px solid #fff;
    text-align: left;
    max-width: 100%;
    white-space: nowrap;
  }

  .row .col.col-body {
    font-size: 10px;
    color: #003060;
    font-weight: 600;
    padding: 10px 7px;
    border-bottom: 2px solid #fff;
    text-align: left;
    max-width: 100%;
    white-space: nowrap;
  }

  #recon_preview .amount-section p {
    display: flex;
    flex-direction: column;
    border-right: 1px solid #999999;
    padding: 0px 111px;
    font-size: 0.88rem !important;
    color: #8c8c8c;
    font-weight: 500;
  }

  #recon_preview .amount-section {
    background: #eff3ff;
    padding: 15px 0;
    border-radius: 12px;
  }

  #recon_preview .amount-section p:nth-child(3) {
    border-right: 0;
  }

  #recon_preview .amount-section p span {
    color: #000;
  }

  .status-custom {
    font-size: 10px;
  }

  ul#experienceTab.nav-pills {
    position: sticky;
    top: calc(50% - 172px);
  }

  div#experienceTabContent {
    padding-left: 2em;
  }

  div#experienceTabContent .card-header {
    padding: 0px 20px;
  }

  div#experienceTabContent .card-body {
    border: 1px solid #26262626;
    padding: 0 20px;
  }

  div#experienceTabContent .card-body::-webkit-scrollbar {
    width: 0px;
    background-color: transparent;
  }

  div#experienceTabContent .card-body::-webkit-scrollbar-thumb:hover {
    background-color: rgba(0, 0, 0, 0.2);
    /* Change this value as per your requirement */
    border-radius: 20px;
  }

  div#experienceTabContent .card-body::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0);
  }

  div#experienceTabContent .card,
  div#experienceTabContent .card .card-body {
    background-color: #fff;
  }

  div#experienceTabContent .tab-pane h3 {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  div#experienceTabContent table tr th {
    font-size: 10px;
  }

  div#experienceTabContent table tr td {
    font-size: 10px;
    background: transparent !important;
  }


  #experienceTab.nav-pills .nav-link {
    border-radius: 5px;
    background: #dbe5ee;
    border: 2px solid #dbe5ee;
    color: #003060;
    font-size: 10px;
    font-weight: 600;
    text-align: center;
    margin: 10px 0px;
    white-space: nowrap;
  }

  #experienceTab.nav-pills .nav-item {
    border-bottom: 0;
  }

  #experienceTab.nav-pills .nav-link.active {
    background: #003060;
    border: 2px solid #003060;
    color: #fff;
  }

  #containerThreeDot {
    width: 100% !important;
  }

  #containerThreeDot #menu-wrap .dots>div,
  #containerThreeDot #menu-wrap .dots>div:after,
  #containerThreeDot #menu-wrap .dots>div:before {
    background-color: #fff !important;
  }

  #containerThreeDot #menu-wrap .toggler:checked~.menu {
    background: #dce2e7;
  }



  .mail-tab .card {
    border-radius: 0;
    background: transparent;
    border-bottom: 1px solid #0000001a;
  }

  .vendor-modal.modal.fade.right .nav.nav-tabs {
    gap: 3px;
  }

  .vendor-modal.modal.fade.right .nav.nav-tabs li.nav-item a {
    padding: 10px 10px 15px;
    border: 0;
    border-radius: 10px 10px 0 0;
  }

  .vendor-modal.modal.fade.right .nav.nav-tabs li.nav-item a:hover {
    border: 0;
    border-radius: 10px 10px 0 0;
    background: transparent;
    color: #fff;
  }

  .nav-action ion-icon {
    color: #fff;
  }

  .hamburger {
    font-size: 23px;
  }

  .hamburger .wrapper-action {
    display: flex;
  }

  .blur-body .tab-content {
    filter: blur(0);
    transition: filter 0.5s ease-in-out;
  }

  .blur-body .tab-content.blur {
    filter: blur(2px);
  }

  .chartContainer {
    width: 100%;
    height: 500px;
  }

  /* 
  .estimate-tab .card-body {
    display: flex;
    padding: 0;
    height: 413px;
    align-items: center;
    justify-content: center;
  } */

  .mail-tab .card-body::after {
    display: none;
  }

  .mail-tab .card-body {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 13px 20px !important;
    color: #000;
  }

  .text p {
    margin-bottom: 4px;
    color: #5b5b5b;
  }

  .mail-tab .card-body .left-details {
    display: flex;
    align-items: center;
    font-size: 10px;
    gap: 15px;
  }

  .date-time-details p {
    font-size: 10px;
  }

  .icon-font {
    background: #003060;
    color: #fff;
    width: 27px;
    height: 27px;
    padding: 10px;
    border-radius: 50%;
    font-size: 11px;
    font-weight: 600;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .action-btns a ion-icon {
    color: #fff;
    font-size: 20px;
  }

  .modal.fade.right .nav.nav-tabs li.nav-item a {
    display: flex;
    align-items: center;
  }

  .modal.fade.right .nav.nav-tabs li.nav-item a ion-icon {
    font-size: 18px;
  }

  .display-flex-space-between p {
    font-size: 10px !important;
  }

  .display-flex-space-between p {
    width: 77%;
    text-align: left !important;
  }

  .matrix-accordion button {
    background-color: #dbe5ee !important;
    color: #000 !important;
    font-weight: 600;
  }

  .matrix-accordion button::after {
    background-image: url(../../public/assets/ion-icon/chevron-up-outline.svg) !important;
  }

  .hidden-modal {
    display: none;
  }

  @media (max-width: 575px) {
    .vendor-modal .modal-body {
      padding: 20px !important;
    }
  }

  .statement-section {
    padding: 10px 25px;
  }

  .statement-section .select-year select {
    max-width: 170px;
    background: #e8e8e8;
  }

  .statement-section .btns {
    text-align: right;
  }

  .row.state-head {
    margin-top: 30px;
  }

  .statement-section .intro-head {
    width: 258px;
  }

  .statement-section .intro-head h2 {
    font-size: 15px;
    font-weight: 600;
    border-bottom: 1px solid #d4d4d4;
    padding-bottom: 4px
  }

  .statement-section .state-head p {
    font-size: 11px !important;
  }


  #printable-content {
    display: none;
  }

  #non-printable-content {
    display: block;
  }

  .acc-summary .row .col-12:first-child p {
    font-weight: 600;
    background: #c5ced6;
  }

  .acc-summary .row .col-lg-12:nth-child(2) hr {
    margin: 0;
    padding: 0;
  }

  .acc-summary .row .display-flex-space-between {
    margin: 0;
    padding: 7px 15px;
  }

  .acc-summary .row .display-flex-space-between p:last-child {
    text-align: right !important;
  }

  .row.state-table {
    font-size: 11px !important;
    margin-top: 30px;
  }

  .state-col-th {
    background: #003060;
    color: #fff;
    padding: 7px 15px;
  }

  .state-col-td {
    color: #000;
    padding: 7px 15px;
    font-size: 10px;
  }

  .row.body-state-table:nth-child(odd) .state-col-td {
    background: #bdc5cd96;
  }

  .statement-section .btns button ion-icon.md.hydrated {
    position: relative;
    top: 2px;
    margin-right: 2px;
  }

  /* .row-company-logo-address {
    display: block;
  } */

  #printable-content .row {
    align-items: center;
  }

  #printable-content .row .right p {
    font-size: 12px;
    margin-bottom: 3px;
  }

  .custom-Range {
    justify-content: flex-end;
  }




  @media (min-width: 1500px) {
    .white-scroll-space {
      height: 70%;
    }

    div#experienceTabContent .card-header {
      padding: 0px 20px;
      border-radius: 0;
      position: sticky;
      top: calc(100% - 597px);
      z-index: 999;
    }

    div#experienceTabContent h3 {
      position: sticky;
      top: calc(100% - 650px);
      background-color: #fff;
      z-index: 99;
      padding: 10px 0;
    }
  }


  .multisteps-form__panel[data-animation=scaleIn].js-active {
    overflow: hidden !important;
  }

  .multisteps-form__panel .card-body {
    overflow: auto !important;
  }
</style>

<!-- Resources CHART_ONLY -->
<script src="<?= BASE_URL ?>public/assets/core.js"></script>
<script src="<?= BASE_URL ?>public/assets/charts.js"></script>
<script src="<?= BASE_URL ?>public/assets/animated.js"></script>
<script src="<?= BASE_URL ?>public/assets/forceDirected.js"></script>
<script src="<?= BASE_URL ?>public/assets/sunburst.js"></script>

<!-- Styles CHART_ONLY -->
<style>
  .chartContainer {
    width: 100%;
    height: 400px;
    font-size: 10px;
  }

  .pieChartContainer {
    width: 100%;
    height: 400px;
    font-size: 10px;
  }

  .card.flex-fill h5 {
    color: #000;
    font-size: 12px;
    white-space: nowrap;
    font-weight: 600;
  }

  .card.flex-fill .card-header {
    padding: 5px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: transparent;
  }

  .card.flex-fill .card-header input,
  .card.flex-fill .card-header select {
    max-width: 155px;
  }




  .card-body::after,
  .card-footer::after,
  .card-header::after {
    display: none;
  }

  .pin-tab {
    cursor: pointer;
    text-decoration: none;
  }

  .robo-element {
    height: 30vh;
    /*50vh*/
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 25px;
  }

  .robo-element img {
    width: 200px;
    height: 200px;
    object-fit: contain;
  }

  #containerThreeDot {
    width: 100% !important;
  }

  #containerThreeDot #menu-wrap .dots>div,
  #containerThreeDot #menu-wrap .dots>div:after,
  #containerThreeDot #menu-wrap .dots>div:before {
    background-color: #003060 !important;
  }
</style>


<?php
if (isset($_GET['create'])) {
  $countryHtml = '';
  $country_sql = queryGet("SELECT * FROM `erp_countries`", true);
  $country_data = $country_sql['data'];
  foreach ($country_data as $data) {

    $countryHtml .= '<option value="' . $data['name'] . '" >' . $data['name'] . '</option>';
  }

  $stateHtml = '';
  $state_sql = queryGet("SELECT * FROM `erp_gst_state_code`", true);
  $state_data = $state_sql['data'];
  foreach ($state_data as $data) {

    $stateHtml .= '<option value="' . $data['gstStateName'] . '" >' . $data['gstStateName'] . '</option>';
  }
?>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card">
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
    <div class="content-header mb-2 p-0">
      <div class="container-fluid">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Vendor List</a></li>
          <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Vendor</a></li>
          <li class="back-button">
            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
              <i class="fa fa-reply po-list-icon"></i>
            </a>
          </li>
        </ol>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- /.row -->
        <div class="row">
          <div class="col-md-12">
            <div class="card2 card-primary">
              <div class="card-header2 pb-5">
                <!-- <h3 class="card-title">Create New Vendor</h3>-->
              </div>
              <div class="card-body p-0 gstfield" id="gstform">
                <div class="row p-0 m-0">
                  <?php

                  ?>
                </div>
                <div class="vendor-gstin" id="VerifyGstinBtnDiv">
                  <div class="card">
                    <div class="card-header">
                      <div class="head">
                        <i class="fa fa-user"></i>
                        <h4>Vendor GSTIN</h4>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="info-vendor-gstin"><span>Put your GSTIN and click on below verify button to get your Bussiness details!</span></div>
                      <div class="form-inline">
                        <label for="">Enter your GSTIN number</label>
                        <input type="text" class="form-control vendor-gstin-input w-75" name="vendorGstNoInput" id="vendorGstNoInput" oninput="this.value = this.value.toUpperCase();">
                        <button class="btn btn-primary verify-btn checkAndVerifyGstinBtn">
                          <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </button>
                      </div>


                      <div class="row mt-2 ml-auto mr-auto">
                        <div class="px-0">
                          <span class="text-xs font-bold px-0">Don't have GSTIN? Check me </span>
                          <div class="icheck-primary d-inline ml-2">
                            <input type="checkbox" id="isGstRegisteredCheckBoxBtn" class="checkbox">
                            <label for="isGstRegisteredCheckBoxBtn">
                            </label>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <!-- <div class="row m-0 p-0 mt-3" id="VerifyGstinBtnDiv">
                  <div class="card gst-card ml-auto mr-auto">
                    <div class="card-header text-center h4 text-bold">Verify GSTIN</div>
                    <div class="card-body pt-4 pb-5">
                      <h6 class="mt-2 mb-3 text-muted text-center">Put your GSTIN and click on below verify button<br> to get your Bussiness details!</h6>
                      <div class="form-input">
                        <input type="text" name="vendorGstNoInput" id="vendorGstNoInput">
                        <label>Enter your GSTIN number</label>
                        <span class="btn-block2 send-btn checkAndVerifyGstinBtn">
                          <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                </div> -->
              </div>

              <!--multisteps-form-->
              <div class="multisteps-form" id="multistepform" style="display:none;">
                <!--<div id="vendorCreateMainForm"></div>-->

              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
      </div>
    </section>

    <!-- /.content -->
    <!-- right modal start here  -->
    <div class="modal fade gst-field-status-modal" id="gst-field-status-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
      <div class="modal-dialog field-status modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content p-0">
          <div class="modal-header">
            <div class="head p-2">
              <h4 class="mb-0">
                <ion-icon name="document-text-outline" role="img" class="md hydrated" aria-label="document text outline"></ion-icon>
                GST Filed Status
              </h4>
            </div>
            <div class="gst-number d-flex gap-2">
              <span class="text-xs font-bold">GSTIN :</span>
              <p id="mdl_gstin_span" class="text-xs">XXXXXXXXXXXXXXXX</p>
            </div>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card mb-0 bg-transparent">
                  <div class="card-header p-0 rounded mb-2">
                    <div class="head p-2">
                      <h4>
                        <ion-icon name="document-text-outline" role="img" class="md hydrated" aria-label="document text outline"></ion-icon>&nbsp; GST Filed Status For GSTR1
                      </h4>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="row">


                      <!-- <div class="col-lg-3 col-md-3 col-sm-6 mb-2">
                                                <span class="text-xs font-bold">Last Update&nbsp;</span>
                                                <p id="mdl_gstin_last_update_comp_span29ACJFS5232R1ZA" class="text-xs">XX/XX/XXXX</p>
                                              </div> -->

                    </div>
                    <div class="row">
                      <div class="col-lg-12 col-md-12 col-sm-12 gst-return-data" id="gstinReturnsDatacomp_Div">

                      </div>
                    </div>
                  </div>
                </div>

                <div class="card mb-0 bg-transparent">
                  <div class="card-header p-0 rounded mb-2">
                    <div class="head p-2">
                      <h4>
                        <ion-icon name="document-text-outline" role="img" class="md hydrated" aria-label="document text outline"></ion-icon>&nbsp; GST Filed Status For GSTR3B
                      </h4>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="row">


                      <!-- <div class="col-lg-3 col-md-3 col-sm-6 mb-2">
                                                <span class="text-xs font-bold">Last Update&nbsp;</span>
                                                <p id="mdl_gstin_last_update_comp_span29ACJFS5232R1ZA" class="text-xs">XX/XX/XXXX</p>
                                              </div> -->

                    </div>
                    <div class="row">
                      <div class="col-lg-12 col-md-12 col-sm-12 gst-return-data" id="gstinReturnsDatacomp3b_Div">

                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- right modal end here  -->

    <script>
      $(document).ready(function() {
        $(document).on('click', '#getGstinReturnFiledStatusBtn', function() {
          // url: `ajaxs/vendor/ajax-gst-filed-status.php?gstin=${gstin}`,
          let gstin = $(this).data('gstin');
          let gstin_status = $(this).data('gstin_status');
          let gstin_reg_date = $(this).data('gstin_reg_date');
          let gstin_last_update = $(this).data('gstin_last_update');
          console.log("Getting gstin return status of", gstin);
          $.ajax({
            url: `ajaxs/vendor/ajax-gst-review.php?gstin=${gstin}`,
            type: 'get',
            beforeSend: function() {
              $("#gstinReturnsDataDiv").html(`Loading...`);
            },

            success: function(response) {

              responseObj = JSON.parse(response);
              let fy = responseObj['fy'];
              responseData = responseObj["data"];
              //  console.log(responseData["fillingFreq"][Object.keys(responseData["fillingFreq"])[0]]);
              // alert(responseData["lstupdt"]);
              // $("#mdl_gstin_comp_span" + vendorGst).html(responseData["gstin"]); 

              // $("#mdl_gstin_reg_comp_span" + vendorGst).html(responseData["rgdt"]);

              // var gstin_status = responseData["sts"];

              // $("#mdl_gstin_last_update_comp_span" + vendorGst).html(responseData["lstupdt"]);

              // $("#mdl_gstin_reg_comp_span" + vendorGst).html(responseData["rgdt"]);

              // mdl_gstin_status_comp_span

              // $("#mdl_gstin_last_update_comp_span"+vendorGst).html(gstin_last_update);

              // if (gstin_status == "Active") {
              //   $("#mdl_gstin_status_comp_span" + vendorGst).html(`<span class="bg-success text-light rounded px-1">${gstin_status}</span>`);
              // } else {
              //   $("#mdl_gstin_status_comp_span" + vendorGst).html(`<span class="bg-warning text-light rounded px-1">${gstin_status}</span>`);
              // }
              // let returnType = responseData["fillingFreq"][Object.keys(responseData["fillingFreq"])[0]] ?? "M";

              // alert(returnType);

              let gstinReturnsDataDivHtml = `
    <table class="table table-striped table-bordered w-100">
    <thead>
      <tr>
        <th>Financial Year</th>
        <th>Tax Period</th>
        <th>Date of Filing</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>`;


              responseData["EFiledlist"].forEach(function(rowVal, rowId) {
                //  console.log(rowVal);


                if (rowVal['rtntype'] == 'GSTR1') {
                  var dateString = rowVal["ret_prd"];

                  // Extract the first two characters as the month
                  var monthString = dateString.substr(0, 2);

                  // Convert the month string to an integer
                  var month = parseInt(monthString, 10);

                  // Array of month names
                  var monthNames = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                  ];

                  // Get the month name based on the numeric month
                  var monthName = monthNames[month - 1]; // Subtract 1 because arrays are 0-based




                  gstinReturnsDataDivHtml += `
          <tr>
            <td>${fy}</td>
            <td>${monthName ?? "-"}</td>
            <td>${rowVal["dof"] ?? "-"}</td>
            <td>${rowVal["status"] ? '<i class="fa fa-check" style="color: green;"> FILED</i>' : '<i class="fa fa-window-close" style="color: red;"> NOT FILED</i>'}</td>
         
          </tr>
        `;
                }
              });


              gstinReturnsDataDivHtml += `</tbody></table>`;


              //3b


              let gstinReturnsDataDivHtml3b = `
    <table class="table table-striped table-bordered w-100">
    <thead>
      <tr>
        <th>Financial Year</th>
        <th>Tax Period</th>
        <th>Date of Filing</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>`;


              responseData["EFiledlist"].forEach(function(rowVal, rowId) {
                //  console.log(rowVal);


                if (rowVal['rtntype'] == 'GSTR3B') {
                  var dateString = rowVal["ret_prd"];

                  // Extract the first two characters as the month
                  var monthString = dateString.substr(0, 2);

                  // Convert the month string to an integer
                  var month = parseInt(monthString, 10);

                  // Array of month names
                  var monthNames = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                  ];

                  // Get the month name based on the numeric month
                  var monthName = monthNames[month - 1]; // Subtract 1 because arrays are 0-based




                  gstinReturnsDataDivHtml3b += `
          <tr>
            <td>${fy}</td>
            <td>${monthName ?? "-"}</td>
            <td>${rowVal["dof"] ?? "-"}</td>
            <td>${rowVal["status"] ? '<i class="fa fa-check" style="color: green;"> FILED</i>' : '<i class="fa fa-window-close" style="color: red;"> NOT FILED</i>'}</td>
         
          </tr>
        `;
                }
              });


              gstinReturnsDataDivHtml3b += `</tbody></table>`;




              $("#gstinReturnsDatacomp_Div").html(gstinReturnsDataDivHtml);
              $("#gstinReturnsDatacomp3b_Div").html(gstinReturnsDataDivHtml3b);
              $("#mdl_gstin_span").html(gstin);
              console.log(gstinReturnsDataDivHtml);
            }
          });
        });
      });
    </script>
  </div>
<?php

} else if (isset($_GET['edit'])) {

  $stateHtml = '';
  $state_sql = queryGet("SELECT * FROM `erp_gst_state_code`", true);
  $state_data = $state_sql['data'];
  foreach ($state_data as $data) {

    $stateHtml .= '<option value="' . $data['gstStateName'] . '" >' . $data['gstStateName'] . '</option>';
  }

?>

  <!-- 
  #############################################  
  #############################################  
  edit / update page -->
  <!-- ########################################  
  #############################################   -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Modal -->
    <div class="modal fade" id="validateMessage" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card">
          <div class="modal-header card-header py-2 px-3">
            <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
            <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
          </div>
          <div id="notesModalBodyUpdate" class="modal-body card-body">
          </div>
        </div>
      </div>
    </div>
    <div class="content-header mb-2 p-0 border-bottom">
      <div class="container-fluid">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Vendors</a></li>
          <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-edit po-list-icon"></i>
              Edit Vendor</a></li>
        </ol>
      </div>
    </div>

    <?php
    $editVendorId = base64_decode($_GET['edit']);
    // console($editVendorId);
    $sql = queryGet("SELECT * FROM `erp_vendor_details` WHERE vendor_id=$editVendorId");
    //echo  $sql = "SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=$editVendorId";
    // $res = $dbCon->query($sql);
    $row = $sql['data'];
    $vendor_id = $row['vendor_id'];
    //  console($row);
    // echo "<pre>";
    // print_r($row);
    // echo "</pre>";
    $b_places = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=$vendor_id AND `vendor_business_primary_flag` = 1");
    $b_row = $b_places['data'];
    //console($b_places);
    $vendor_poc = queryGet("SELECT * FROM `tbl_vendor_admin_details` WHERE `fldAdminVendorId`=$vendor_id AND `fldAdminRole` = 1");
    //console($vendor_poc['data']);
    $vendor_pass = $vendor_poc['data']['fldAdminPassword'];
    ?>

    <section class="content">
      <div class="container-fluid">
        <!--progress bar-->
        <div class="row">
          <div class="col-12 col-lg-8 ml-auto mr-auto mb-4">
            <div class="multisteps-form__progress">
              <button class="multisteps-form__progress-btn js-active" type="button" title="User Info">Basic Details</button>
              <button class="multisteps-form__progress-btn" type="button" title="Address" disabled>Others Address</button>
              <button class="multisteps-form__progress-btn" type="button" title="Order Info" disabled>Accounting</button>
              <button class="multisteps-form__progress-btn" type="button" title="Comments" disabled>POC Details</button>
            </div>
          </div>
        </div>
        <!--form panels-->
        <div class="row">

          <div class="col-12 col-lg-8 m-auto">
            <form class="multisteps-form__form edit_frm" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="edit_frm" name="edit_frm">
              <input type="hidden" name="editData" id="editData" value="">
              <input type="hidden" name="type" id="type" value="edit">
              <input type="hidden" name="company_id" id="company_id" value="<?= $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"]; ?>">
              <input type="hidden" name="company_branch_id" id="company_branch_id" value="<?= $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]; ?>">

              <!--single form panel-->
              <div class="multisteps-form__panel js-active" data-animation="scaleIn">

                <div class="card vendor-details-card">
                  <div class="card-header">
                    <div class="display-flex">
                      <div class="head">
                        <i class="fa fa-info"></i>
                        <h4>Basic Details</h4>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="multisteps-form__content">
                      <input type="hidden" name="vendor_id" value="<?= $row['vendor_id'] ?>" id="vendor_id_edit">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Vendor Code</label>
                            <input type="text" class="form-control" name="vendor_code" id="vendor_code" value="<?= $row['vendor_code'] ?>" readonly>

                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-input">
                            <label>GSTIN</label>
                            <input type="text" class="form-control" name="vendor_gstin" id="vendor_gstin" value="<?= $row['vendor_gstin'] ?>" <?php if (!empty($row['vendor_gstin'])) { ?>readonly<?php } ?>>
                          </div>

                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Pan *</label>
                            <input type="text" class="form-control" name="vendor_pan" id="vendor_pan" value="<?= $row['vendor_pan'] ?>">
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Trade Name</label>
                            <input type="text" class="form-control" name="trade_name" id="trade_name" value="<?= $row['trade_name'] ?>">

                          </div>
                        </div>
                        <!-- <div class="col-md-6">
                          <div class="form-input">
                            <label>State</label>
                            <input type="text" class="form-control" name="state" id="state" value="<?= $b_row['vendor_business_state'] ?>">

                          </div>
                        </div> -->
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>City</label>
                            <input type="text" class="form-control" name="city" id="city" value="<?= $b_row['vendor_business_city'] ?>">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>District</label>
                            <input type="text" class="form-control" name="district" id="district" value="<?= $b_row['vendor_business_district'] ?>">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Location</label>
                            <input type="text" class="form-control" name="location" id="location" value="<?= $b_row['vendor_business_location'] ?>">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Building Number</label>
                            <input type="text" class="form-control" name="build_no" id="build_no" value="<?= $b_row['vendor_business_building_no'] ?>">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Flat Number</label>
                            <input type="text" class="form-control" name="flat_no" id="flat_no" value="<?= $b_row['vendor_business_flat_no'] ?>">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Street Name</label>
                            <input type="text" class="form-control" name="street_name" id="street_name" value="<?= $b_row['vendor_business_street_name'] ?>">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Pin Code</label>
                            <input type="number" class="form-control" name="pincode" id="pincode" value="<?= $b_row['vendor_business_pin_code'] ?>">
                          </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                          <div class="forform-control">
                            <label for="">Country</label>
                            <?php
                            if (empty($row['vendor_gstin']) || $row['vendor_gstin'] == 0) {
                              // echo 0;

                            ?>
                              <select id="countries" name="countries" class="form-control countriesDropDown_edit">
                                <?php
                                $countries_sql = queryGet("SELECT * FROM `erp_countries`", true);
                                $countries_data = $countries_sql['data'];
                                foreach ($countries_data as $data) {

                                ?>

                                  <option value="<?= $data['name'] ?>" <?php if ($data['name'] ==  $b_row['vendor_business_country']) {
                                                                          echo "selected";
                                                                        } ?>><?= $data['name'] ?></option>
                                <?php
                                }
                                ?>
                              </select>
                            <?php
                            } else {
                              // echo 1;
                            ?>
                              <select id="countries" name="countries" class="form-control countriesDropDown_edit" disabled>
                                <?php
                                $countries_sql = queryGet("SELECT * FROM `erp_countries`", true);
                                $countries_data = $countries_sql['data'];
                                foreach ($countries_data as $data) {

                                ?>

                                  <option value="<?= $data['name'] ?>" <?php if ($data['name'] ==  $b_row['vendor_business_country']) {
                                                                          echo "selected";
                                                                        } ?>><?= $data['name'] ?></option>
                                <?php
                                }
                                ?>
                              </select>

                            <?php
                            }
                            ?>
                            <!-- <input type="text" class="form-control" name="countries" id="countries" value="<?php echo $gstDetails['pradr']['addr']['stcd']; ?>"> -->
                          </div>
                        </div>
                        <?php

                        if (empty($row['vendor_gstin']) || $row['vendor_gstin'] == 0) {

                        ?>
                          <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control stateDivDropDown">
                              <label for="">State</label>
                              <?php
                              if ($b_row['vendor_business_country']  == 'India') {
                                //  echo 1;
                              ?>
                                <select id="state" name="state" class="form-control secect2 stateDropDown">
                                  <?php

                                  $state_sql = queryGet("SELECT * FROM `erp_gst_state_code`", true);
                                  $state_data = $state_sql['data'];
                                  foreach ($state_data as $data) {

                                  ?>

                                    <option value="<?= $data['gstStateName'] ?>" <?php if ($data['gstStateName'] ==  $b_row['vendor_business_state']) {
                                                                                    echo "selected";
                                                                                  } ?>><?= $data['gstStateName'] ?></option>
                                  <?php
                                  }
                                  ?>
                                </select>
                              <?php
                              } else {
                                //echo 2;
                              ?>
                                <input type="text" class="form-control" name="state" id="state" value="<?php echo $b_row['vendor_business_state'] ?>">

                              <?php
                              }
                              ?>


                            </div>
                          </div>
                        <?php
                        } else {
                          //echo 3;

                        ?>


                          <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control stateDivDropDown">
                              <label for="">State</label>
                              <input type="text" class="form-control" name="state" id="state" value="<?php echo $b_row['vendor_business_state'] ?>" readonly>
                            </div>
                          </div>


                        <?php
                        }
                        ?>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="button-row d-flex mt-2 mb-2">
                      <button class="btn btn-primary ml-auto js-btn-next" type="button" title="Next">Next</button>
                    </div>
                  </div>
                </div>



              </div>
              <!--single form panel-->
              <div class="multisteps-form__panel step2" style="height: 65vh; overflow:scroll" data-animation="scaleIn">
                <div class="card">
                  <div class="card-header">
                    <div class="head">
                      <h4>Other Address</h4>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="insertOtherAddress text-success"></div>
                    <div class="multisteps-form__content">
                      <div class="form-table" id="customFields">
                        <?php
                        $editVendorId = base64_decode($_GET['edit']);
                        $sql = "SELECT * FROM `erp_vendor_bussiness_places` WHERE vendor_id='" . $row['vendor_id'] . "' AND `vendor_business_primary_flag`=0";
                        $res = queryGet($sql, true);

                        $fetchOtherAddress = $res['data'];
                        // console($fetchOtherAddress);
                        if ($res['numRows'] == 0) {
                        ?>
                          <input type="hidden" name="other_b_places" value="new">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-input">
                                <label>Flat Number</label>
                                <input type="text" class="form-control" name="vendor_business_flat_no" class="form-control" id="vendor_business_flat_no" value="">
                              </div>
                              <div class="form-input">
                                <label>Pin Code</label>
                                <input type="text" class="form-control" name="vendor_business_pin_code" class="form-control" id="vendor_business_pin_code" value="">

                              </div>
                              <div class="form-input">
                                <label>District</label>
                                <input type="text" class="form-control" name="vendor_business_district" class="form-control" id="vendor_business_district" value="">

                              </div>
                              <div class="form-input">
                                <label>Location</label>
                                <input type="text" class="form-control" name="vendor_business_location" class="form-control" id="vendor_business_location" value="">

                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-input">
                                <label>Building Number</label>
                                <input type="text" class="form-control" name="vendor_business_building_no" class="form-control" id="vendor_business_building_no" value="">

                              </div>

                              <div class="form-input">
                                <label>Street Name</label>
                                <input type="text" class="form-control" name="vendor_business_street_name" class="form-control" id="vendor_business_street_name" value="">

                              </div>

                              <div class="form-input">
                                <label>City</label>
                                <input type="text" class="form-control" name="vendor_business_city" class="form-control" id="vendor_business_city" value="">

                              </div>

                              <div class="form-input">
                                <label>State</label>
                                <input type="text" class="form-control" name="vendor_business_state" class="form-control" id="vendor_business_state" value="">

                              </div>

                            </div>
                          </div>

                        <?php
                        } else {
                        ?>
                          <input type="hidden" name="other_b_places" value="update">
                          <?php
                          foreach ($fetchOtherAddress as $key => $oneAddress) {
                          ?>
                            <div class="row">
                              <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="removeID"></div>
                                <a href="javascript:void(0);" class="btn btn-primary mt-2 mb-4" data-toggle="modal" data-target="#otherAddress">
                                  <i class="fa fa-plus"></i>
                                </a>
                              </div>
                              <div class="col-md-6" style="text-align: right;">
                                <?php
                                if ($key > 0) {
                                ?>
                                  <a href="javascript:void(0);" id="remove_<?= $oneAddress['vendor_business_id'] ?>" class="updateRemCF btn btn-danger mt-3 mb-4">
                                    <i class="fa fa-minus"></i>
                                  </a>
                                <?php
                                }
                                ?>
                              </div>
                              <input type="hidden" class="form-control" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][b_id]" class="form-control" id="vendor_business_building_no" value="<?php echo $oneAddress['vendor_business_id']; ?>">
                              <div class="col-md-6">
                                <div class="form-input">
                                  <label>Flat Number</label>
                                  <input type="text" class="form-control" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][vendor_business_flat_no]" class="form-control" id="vendor_business_flat_no" value="<?php echo $oneAddress['vendor_business_flat_no']; ?>">
                                </div>
                                <div class="form-input">
                                  <label>Pin Code</label>
                                  <input type="text" class="form-control" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][vendor_business_pin_code]" class="form-control" id="vendor_business_pin_code" value="<?php echo $oneAddress['vendor_business_pin_code']; ?>">

                                </div>
                                <div class="form-input">
                                  <label>District</label>
                                  <input type="text" class="form-control" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][vendor_business_district]" class="form-control" id="vendor_business_district" value="<?php echo $oneAddress['vendor_business_district']; ?>">

                                </div>
                                <div class="form-input">
                                  <label>Location</label>
                                  <input type="text" class="form-control" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][vendor_business_location]" class="form-control" id="vendor_business_location" value="<?php echo $oneAddress['vendor_business_location']; ?>">

                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-input">
                                  <label>Building Number</label>
                                  <input type="text" class="form-control" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][vendor_business_building_no]" class="form-control" id="vendor_business_building_no" value="<?php echo $oneAddress['vendor_business_building_no']; ?>">

                                </div>

                                <div class="form-input">
                                  <label>Street Name</label>
                                  <input type="text" class="form-control" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][vendor_business_street_name]" class="form-control" id="vendor_business_street_name" value="<?php echo $oneAddress['vendor_business_street_name']; ?>">

                                </div>

                                <div class="form-input">
                                  <label>City</label>
                                  <input type="text" class="form-control" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][vendor_business_city]" class="form-control" id="vendor_business_city" value="<?php echo $oneAddress['vendor_business_city']; ?>">

                                </div>

                                <div class="form-input">
                                  <label>State</label>
                                  <input type="text" class="form-control" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][vendor_business_state]" class="form-control" id="vendor_business_state" value="<?php echo $oneAddress['vendor_business_state']; ?>">
                                </div>
                              </div>
                            </div>
                            <hr>
                        <?php
                          }
                        }
                        ?>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="button-row d-flex mt-2 mb-2">
                      <button class="btn btn-primary js-btn-prev" type="button" title="Prev">Prev</button>
                      <button class="btn btn-primary ml-auto js-btn-next" type="button" title="Next">Next</button>
                    </div>
                  </div>
                </div>
              </div>
              <!--single form panel-->
              <div class="multisteps-form__panel" data-animation="scaleIn">
                <div class="card">
                  <div class="card-header">
                    <div class="head">
                      <h4>
                        Accounting
                      </h4>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="multisteps-form__content">
                      <?php
                      $sql_acc = queryGet("SELECT * FROM `erp_vendor_bank_details` WHERE vendor_id='" . $editVendorId . "'");
                      $fetchAccounting = $sql_acc['data'];
                      // console($fetchAccounting);
                      // echo "<pre>";
                      // print_r($fetchAccounting);
                      // echo "</pre>";
                      ?>
                      <div class="row">
                        <div class="col-md-6" style="display:none;">
                          <div class="form-input">
                            <label>Opening Blance</label>
                            <input step="0.01" type="number" class="form-control" name="opening_balance" value="<?php echo $fetchAccounting['opening_balance'] ?>" id="vendor_opening_balance">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label for="">Company Currency</label>
                            <select id="company_currency" name="currency" class="form-control form-control-border borderColor">
                              <!--<option value="">Select Currency</option>-->
                              <?php
                              $listResult = getAllCurrencyType();
                              if ($listResult["status"] == "success") {
                                foreach ($listResult["data"] as $listRow) {
                              ?>
                                  <option <?php if ($fetchAccounting['currency'] == $listRow['currency_id']) {
                                            echo "selected";
                                          } ?> value="<?php echo $listRow['currency_id']; ?>"><?php echo $listRow['currency_name']; ?></option>
                              <?php }
                              } ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Credit Period(In Days)</label>
                            <input type="text" class="form-control" name="credit_period" value="<?= $fetchAccounting['credit_period'] ?>" id="vendor_credit_period">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <div class="check-img-edit mt-2 mb-2">
                              <img width="120" src="../../public/assets/img/cheque-book.jpg" alt="">
                            </div>

                            <!-- <br> Upload Cancled Ckecked <i class="fa fa-upload"></i>  -->
                            <input type="file" class="vendor_bank_cancelled_cheque form-control" name="vendor_bank_cancelled_cheque" id="vendor_bank_cancelled_cheque">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>IFSC</label>
                            <input type="text" class="form-control IFSClass" name="vendor_bank_ifsc" value="<?= $fetchAccounting['vendor_bank_ifsc'] ?>" id="vendor_bank_ifsc">
                            <span class="tick-icon"></span>
                          </div>
                          <span class="text-xs" id="ifscCodeMsg"></span>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Bank Name</label>
                            <input type="text" class="form-control" name="vendor_bank_name" value="<?= $fetchAccounting['vendor_bank_name'] ?>" id="vendor_bank_name">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Bank Branch Name</label>
                            <input type="text" class="form-control" name="vendor_bank_branch" value="<?= $fetchAccounting['vendor_bank_branch'] ?>" id="vendor_bank_branch">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Bank Address</label>
                            <input type="text" class="form-control" name="vendor_bank_address" value="<?= $fetchAccounting['vendor_bank_address'] ?>" id="vendor_bank_address">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Bank Account Number</label>
                            <input type="text" class="form-control account_number" name="vendor_bank_account_no" value="<?= $fetchAccounting['vendor_bank_account_no'] ?>" id="account_number">
                            <p id="bank_detail_error"></p>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Bank Account Holder</label>
                            <input type="text" class="form-control" name="account_holder" id="account_holder" value="<?= $fetchAccounting['account_holder'] ?>">
                          </div>
                        </div>

                      </div>

                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="row">
                      <div class="button-row d-flex mt-2 mb-2">
                        <button class="btn btn-primary js-btn-prev" type="button" title="Prev">Prev</button>
                        <button class="btn btn-primary ml-auto js-btn-next" type="button" title="Next" id="next_last">Next</button>
                      </div>
                    </div>
                  </div>
                </div>



              </div>
              <!--single form panel-->
              <div class="multisteps-form__panel" data-animation="scaleIn">

                <div class="card">
                  <div class="card-header">
                    <div class="head">
                      <h4>POC Details</h4>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="multisteps-form__content">
                      <div class="row">
                        <div class="col-md-6">
                          <label>Name of Person*</label>
                          <div class="form-input">
                            <input type="text" class="form-control" name="vendor_authorised_person_name" value="<?= $row['vendor_authorised_person_name'] ?>" id="adminName">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Designation*</label>
                            <input type="text" class="form-control" name="vendor_authorised_person_designation" value="<?= $row['vendor_authorised_person_designation'] ?>" id="vendor_authorised_person_designation">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Phone Number*</label>
                            <input type="text" class="form-control" name="vendor_authorised_person_phone" value="<?= $row['vendor_authorised_person_phone'] ?>" id="adminPhone">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Alternative Phone </label>
                            <input type="number" class="form-control" name="vendor_authorised_alt_phone" value="<?= $row['vendor_authorised_alt_phone'] ?>" id="vendor_authorised_person_phone">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Email*</label>
                            <input type="text" class="form-control" name="vendor_authorised_person_email" value="<?= $row['vendor_authorised_person_email'] ?>" id="adminEmail">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Alternative Email</label>
                            <input type="email" class="form-control" name="vendor_authorised_alt_email" value="<?= $row['vendor_authorised_alt_email'] ?>" id="vendor_authorised_person_email">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Login Password [Will be send to the POC email]</label>
                            <input class="form-control" type="text" name="adminPassword" id="adminPassword" value="<?php echo $vendor_pass ?>">
                          </div>
                        </div>
                        <!-- <div class="col-md-3">
                        <div class="form-input">
                          <input type="file" name="vendor_picture" id="vendor_picture">
                        </div>
                      </div> -->
                        <div class="col-md-6">
                          <div class="form-input">
                            <label for="" class="label-hidden">Label</label>
                            <select id="vendor_visible_to_all" name="vendor_visible_to_all" class="select2 form-control mt-0">
                              <option value="" selected>Visible For All</option>
                              <option <?php if ($row['vendor_visible_to_all'] == 'No') {
                                        echo "";
                                      } ?> value="No">No</option>
                              <option <?php if ($row['vendor_visible_to_all'] == 'Yes') {
                                        echo "selected";
                                      } ?> value="Yes">Yes</option>
                            </select>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="button-row d-flex mt-2 mb-2">
                      <button class="btn btn-primary js-btn-prev" type="button" title="Prev">Prev</button>
                      <!-- <button class="btn ml-auto btn-danger add_data" type="button" title="Save As Draft" value="add_draft">Save As Draft</button> -->
                      <button id="vendorEditBtn" class="btn btn-primary ml-auto edit_data" type="submit" title="update">Update</button>
                    </div>
                  </div>
                </div>


              </div>
            </form>




          </div>
        </div>
      </div>
    </section>


    <div class="otherAddressAddModal modal fade" id="otherAddress" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="false" aria-hidden="true" append-by="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Address</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="POST" id="addOtherForm">
              <input type="hidden" id="vendor_idd" value="<?= $oneAddress['vendor_id'] ?>">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Flat Number</label>
                    <input type="text" name="vendor_business_flat_no" class="form-control" id="vendor_business_flat_no_add">
                  </div>
                  <div class="form-input">
                    <label>Pin Code</label>
                    <input type="text" name="vendor_business_pin_code" class="form-control" id="vendor_business_pin_code_add">
                  </div>
                  <div class="form-input">
                    <label>District</label>
                    <input type="text" name="vendor_business_district" class="form-control" id="vendor_business_district_add">
                  </div>
                  <div class="form-input">
                    <label>Location</label>
                    <input type="text" name="vendor_business_location" class="form-control" id="vendor_business_location_add">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Building Number</label>
                    <input type="text" name="vendor_business_building_no" class="form-control" id="vendor_business_building_no_add">
                  </div>

                  <div class="form-input">
                    <label>Street Name</label>
                    <input type="text" name="vendor_business_street_name" class="form-control" id="vendor_business_street_name_add">
                  </div>

                  <div class="form-input">
                    <label>City</label>
                    <input type="text" name="vendor_business_city" class="form-control" id="vendor_business_city_add">
                  </div>

                  <div class="form-input">
                    <label>State</label>
                    <input type="text" name="vendor_business_state" class="form-control" id="vendor_business_state_add">
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" name="addOtherAddressBtn" id="addOtherAddressBtn">Save changes</button>
          </div>
        </div>
      </div>
    </div>


    <!-- /.content -->
  </div>

  <script>
    // *** multi step form *** //
    //DOM elements
    const DOMstrings = {
      stepsBtnClass: 'multisteps-form__progress-btn',
      stepsBtns: document.querySelectorAll(`.multisteps-form__progress-btn`),
      stepsBar: document.querySelector('.multisteps-form__progress'),
      stepsForm: document.querySelector('.multisteps-form__form'),
      stepsFormTextareas: document.querySelectorAll('.multisteps-form__textarea'),
      stepFormPanelClass: 'multisteps-form__panel',
      stepFormPanels: document.querySelectorAll('.multisteps-form__panel'),
      stepPrevBtnClass: 'js-btn-prev',
      stepNextBtnClass: 'js-btn-next'
    };
    //remove class from a set of items
    const removeClasses = (elemSet, className) => {

      elemSet.forEach(elem => {

        elem.classList.remove(className);

      });

    };

    //return exect parent node of the element
    const findParent = (elem, parentClass) => {

      let currentNode = elem;

      while (!currentNode.classList.contains(parentClass)) {
        currentNode = currentNode.parentNode;
      }

      return currentNode;

    };

    //get active button step number
    const getActiveStep = elem => {
      return Array.from(DOMstrings.stepsBtns).indexOf(elem);
    };

    //set all steps before clicked (and clicked too) to active
    const setActiveStep = activeStepNum => {

      //remove active state from all the state
      removeClasses(DOMstrings.stepsBtns, 'js-active');

      //set picked items to active
      DOMstrings.stepsBtns.forEach((elem, index) => {

        if (index <= activeStepNum) {
          elem.classList.add('js-active');
        }

      });
    };

    //get active panel
    const getActivePanel = () => {

      let activePanel;

      DOMstrings.stepFormPanels.forEach(elem => {

        if (elem.classList.contains('js-active')) {

          activePanel = elem;

        }

      });

      return activePanel;

    };

    //open active panel (and close unactive panels)
    const setActivePanel = activePanelNum => {

      //remove active class from all the panels
      removeClasses(DOMstrings.stepFormPanels, 'js-active');

      //show active panel
      DOMstrings.stepFormPanels.forEach((elem, index) => {
        if (index === activePanelNum) {

          elem.classList.add('js-active');

          setFormHeight(elem);

        }
      });

    };

    //set form height equal to current panel height
    const formHeight = activePanel => {

      const activePanelHeight = activePanel.offsetHeight;

      DOMstrings.stepsForm.style.height = `${activePanelHeight}px`;

    };

    const setFormHeight = () => {
      const activePanel = getActivePanel();

      formHeight(activePanel);
    };

    //STEPS BAR CLICK FUNCTION
    DOMstrings.stepsBar.addEventListener('click', e => {

      //check if click target is a step button
      const eventTarget = e.target;

      if (!eventTarget.classList.contains(`${DOMstrings.stepsBtnClass}`)) {
        return;
      }

      //get active button step number
      const activeStep = getActiveStep(eventTarget);

      //set all steps before clicked (and clicked too) to active
      setActiveStep(activeStep);

      //open active panel
      setActivePanel(activeStep);
    });

    //PREV/NEXT BTNS CLICK
    DOMstrings.stepsForm.addEventListener('click', e => {

      const eventTarget = e.target;

      //check if we clicked on `PREV` or NEXT` buttons
      if (!(eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`) || eventTarget.classList.contains(`${DOMstrings.stepNextBtnClass}`))) {
        return;
      }

      //find active panel
      const activePanel = findParent(eventTarget, `${DOMstrings.stepFormPanelClass}`);

      let activePanelNum = Array.from(DOMstrings.stepFormPanels).indexOf(activePanel);

      //set active step and active panel onclick
      if (eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`)) {
        activePanelNum--;

      } else {

        activePanelNum++;

      }

      setActiveStep(activePanelNum);
      setActivePanel(activePanelNum);

    });

    //SETTING PROPER FORM HEIGHT ONLOAD
    window.addEventListener('load', setFormHeight, false);

    //SETTING PROPER FORM HEIGHT ONRESIZE
    window.addEventListener('resize', setFormHeight, false);

    //changing animation via animation select !!!YOU DON'T NEED THIS CODE (if you want to change animation type, just change form panels data-attr)

    const setAnimationType = newType => {
      DOMstrings.stepFormPanels.forEach(elem => {
        elem.dataset.animation = newType;
      });
    };

    //selector onchange - changing animation
    const animationSelect = document.querySelector('.pick-animation__select');

    animationSelect.addEventListener('change', () => {
      const newAnimationType = animationSelect.value;

      setAnimationType(newAnimationType);
    });
  </script>
<?php } else {
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
            <div class="p-0 pt-1 my-2">
              <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                  <h3 class="card-title">Manage Vendor</h3>
                  <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary btnstyle m-2 float-add-btn" style="line-height: 32px;"><i class="fa fa-plus"></i></a>
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
                          <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                        </div>
                      </div>

                    </div>

                    <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Filter Purchase Request</h5>

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

                  $sts = " AND `vendor_status` !='deleted'";
                  if (isset($_REQUEST['vendor_status_s']) && $_REQUEST['vendor_status_s'] != '') {
                    $sts = ' AND vendor_status="' . $_REQUEST['vendor_status_s'] . '"';
                  }

                  if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                    $cond .= " AND branch_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                  }

                  if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                    $cond .= " AND (`vendor_code` like '%" . $_REQUEST['keyword'] . "%' OR `trade_name` like '%" . $_REQUEST['keyword'] . "%' OR `vendor_gstin` like '%" . $_REQUEST['keyword'] . "%')";
                  }


                  if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                    $cond .= " AND (`vendor_code` like '%" . $_REQUEST['keyword2'] . "%' OR `trade_name` like '%" . $_REQUEST['keyword2'] . "%' OR `vendor_gstin` like '%" . $_REQUEST['keyword2'] . "%')";
                  } else {
                    if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                      $cond .= " AND (`vendor_code` like '%" . $_REQUEST['keyword'] . "%' OR `trade_name` like '%" . $_REQUEST['keyword'] . "%' OR `vendor_gstin` like '%" . $_REQUEST['keyword'] . "%')";
                    }
                  }

                  $sql_list = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE 1 " . $cond . "  AND company_id='" . $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] . "' " . $sts . "  ORDER BY vendor_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                  $qry_list = mysqli_query($dbCon, $sql_list);
                  $num_list = mysqli_num_rows($qry_list);


                  $countShow = "SELECT count(*) FROM `" . ERP_VENDOR_DETAILS . "` WHERE 1 " . $cond . " AND company_id='" . $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] . "' " . $sts . " ";
                  $countQry = mysqli_query($dbCon, $countShow);
                  $rowCount = mysqli_fetch_array($countQry);
                  $count = $rowCount[0];
                  $cnt = $GLOBALS['start'] + 1;
                  $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_VENDOR_DETAILS", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                  $settingsCheckbox = unserialize($settingsCh);
                  if ($num_list > 0) {
                  ?>
                    <table class="table defaultDataTable table-hover">
                      <thead>
                        <tr class="alert-light">
                          <th>#</th>
                          <?php if (in_array(1, $settingsCheckbox)) { ?>
                            <th>Vendor Code</th>
                          <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                            <th>Trade Name</th>
                          <?php }
                          if (in_array(3, $settingsCheckbox)) { ?>
                            <th>Vendor PAN</th>
                          <?php  }
                          if (in_array(4, $settingsCheckbox)) { ?>
                            <th>Constitution of Business</th>
                          <?php }
                          if (in_array(5, $settingsCheckbox)) { ?>
                            <th>GSTIN</th>
                          <?php  }
                          if (in_array(6, $settingsCheckbox)) { ?>
                            <th>Email</th>
                          <?php }
                          if (in_array(7, $settingsCheckbox)) { ?>
                            <th>Phone</th>
                          <?php  } ?>
                          <th>Status</th>

                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $vendorModalHtml = "";
                        while ($row = mysqli_fetch_assoc($qry_list)) {
                          $vendorId = $row['vendor_id'];
                          $vendor_authorised_person_name = $row['vendor_authorised_person_name'];
                          $vendor_authorised_person_designation = $row['vendor_authorised_person_designation'];
                          $vendor_authorised_person_phone = $row['vendor_authorised_person_phone'];
                          $vendor_authorised_alt_phone = $row['vendor_authorised_alt_phone'];
                          $vendor_authorised_person_email = $row['vendor_authorised_person_email'];
                          $vendor_authorised_alt_email = $row['vendor_authorised_alt_email'];
                          $trade_name = $row['trade_name'];
                        ?>
                          <tr>
                            <td><?= $cnt++ ?></td>
                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                              <td><?= $row['vendor_code'] ?></td>
                            <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                              <td><?= $row['trade_name'] ?></td>
                            <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                              <td><?= $row['vendor_pan'] ?></td>
                            <?php }
                            if (in_array(4, $settingsCheckbox)) { ?>
                              <td><?= $row['constitution_of_business'] ?></td>
                            <?php }
                            if (in_array(5, $settingsCheckbox)) { ?>
                              <td><?= $row['vendor_gstin'] ?></td>
                            <?php }
                            if (in_array(6, $settingsCheckbox)) { ?>
                              <td><?= $row['vendor_authorised_person_email'] ?></td>
                            <?php }
                            if (in_array(7, $settingsCheckbox)) { ?>
                              <td><?= $row['vendor_authorised_person_phone'] ?></td>
                            <?php } ?>
                            <td>
                             
      
                                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">

                                  <input type="hidden" name="id" value="<?php echo $row['vendor_id'] ?>">
                                  <input type="hidden" name="changeStatus" value="active_inactive">

                                  <input type="hidden" name="status" value="<?= $row['vendor_status'] ?>">

                                  <button <?php if ($row['vendor_status'] == "draft") { ?> type="button" class="btn btn-sm" <?php } else { ?>type="submit" onclick="return confirm('Are you sure change vendor_status?')" style="cursor: pointer; border:none" <?php } ?> class="btn btn-sm" data-toggle="tooltip" data-placement="top" title="<?php echo $row['vendor_status'] ?>">
                                    <?php if ($row['vendor_status'] == "active") { ?>
                                      <p class="status text-xs"><?php echo ucfirst($row['vendor_status']); ?></p>
                                    <?php } else if ($row['vendor_status'] == "inactive") { ?>
                                      <p class="status-danger text-xs"><?php echo ucfirst($row['vendor_status']); ?></p>
                                    <?php } else if ($row['vendor_status'] == "draft") { ?>
                                      <p class="status-warning text-xs"><?php echo ucfirst($row['vendor_status']); ?></p>
                                    <?php } else if ($row['vendor_status'] == "guest") { ?>
                                      <p class="status-warning text-xs"><?php echo ucfirst($row['vendor_status']); ?></p>
                                    <?php } ?>

                                  </button>
                                </form>
                              
                            </td>
                            <td>
                              <a style="cursor: pointer;" class="btn btn-sm" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $row['vendor_id'] ?>"><i class="fa fa-eye po-list-icon"></i></a>
                              <!-- right modal start here  -->
                              <div class="modal fade right customer-modal vendor-modal" id="fluidModalRightSuccessDemo_<?= $row['vendor_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                  <!--Content-->
                                  <div class="modal-content">
                                    <!--Header-->
                                    <div class="modal-header pt-4">
                                      <div class="row mb-4">
                                        <div class="col-lg-8 col-md-8 col-sm-8">
                                          <p class="heading lead text-sm text-uppercase font-bold mb-2 mt-2"><?= $trade_name ?></p>
                                          <p class="text-xs mt-2"><?= $row['constitution_of_business'] ?></p>
                                          <p class="text-xs text-uppercase mt-2">Code : <?= $row['vendor_code'] ?></p>
                                          <p class="text-xs text-uppercase mt-2">GSTIN : <?= $row['vendor_gstin'] ?></p>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                          <p class="text-sm font-bold text-uppercase mt-2"><?= $vendor_authorised_person_name ?></p>
                                          <p class="text-xs font-italic mt-2">(<?= $vendor_authorised_person_designation ?>)</p>
                                          <div class="d-flex text-xs phone-alt-number mt-2">
                                            <?php if (empty($vendor_authorised_alt_phone)) { ?>
                                              <a href="<?= $vendor_authorised_person_phone ?>"><ion-icon name="call-outline"></ion-icon><?= $vendor_authorised_person_phone ?></a>
                                            <?php } else { ?>
                                              <a href="<?= $vendor_authorised_person_phone ?>"><ion-icon name="call-outline"></ion-icon><?= $vendor_authorised_person_phone ?>/<?= $vendor_authorised_alt_phone ?></a>
                                            <?php } ?>
                                          </div>
                                          <div class="d-flex text-xs email-alt mt-2">
                                            <?php if (empty($vendor_authorised_alt_email)) { ?>
                                              <a href="<?= $vendor_authorised_person_email ?>"><ion-icon name="mail-outline"></ion-icon><?= $vendor_authorised_person_email ?></a>
                                            <?php } else { ?>
                                              <a href="<?= $vendor_authorised_person_email ?>"><ion-icon name="mail-outline"></ion-icon><?= $vendor_authorised_person_email ?>/<?= $vendor_authorised_alt_email ?></a>
                                            <?php } ?>
                                          </div>
                                        </div>

                                      </div>
                                      <!-- <div class="display-flex-right mt-2 mb-2">
                                    <p class="text-sm"><?= $vendor_authorised_person_name ?></p>
                                    <p class="text-xs text-italic">(<?= $vendor_authorised_person_designation ?>)</p>
                                  </div>
                                  <p class="text-right text-xs mt-2 mb-2"><?= $vendor_authorised_person_phone; ?><?php if (!empty($vendor_authorised_alt_phone)) echo '/' . $vendor_authorised_alt_phone; ?></p>
                                  <p class="text-right text-xs mt-2 mb-2"><?= $vendor_authorised_person_email; ?><?php if (!empty($vendor_authorised_person_email)) echo '/' . $vendor_authorised_person_email; ?></p> -->

                                      <div class="display-flex-space-between mb-3">

                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                          <li class="nav-item">
                                            <a class="nav-link active" id="home-tab<?= $row['vendor_id'] ?>" data-toggle="tab" href="#home<?= $row['vendor_id'] ?>" role="tab" aria-controls="home<?= $row['vendor_id'] ?>" aria-selected="true"><ion-icon name="information-outline" class="mr-2"></ion-icon>Overview</a>
                                          </li>
                                          <li class="nav-item">
                                            <a class="nav-link" id="transaction-tab<?= $row['vendor_id'] ?>" data-toggle="tab" href="#transaction<?= $row['vendor_id'] ?>" role="tab" aria-controls="transaction<?= $row['vendor_id'] ?>" aria-selected="true" class="mr-2"><ion-icon name="repeat-outline" class="mr-2"></ion-icon>Transactions</a>
                                          </li>
                                          <li class="nav-item">
                                            <a class="nav-link mailtabbtn" data-vendorid="<?= $row['vendor_id'] ?>" data-vendorcode="<?= $row['vendor_code'] ?>" id="mail-tab<?= $row['vendor_id'] ?>" data-toggle="tab" href="#mail<?= $row['vendor_id'] ?>" role="tab" aria-controls="mail<?= $row['vendor_id'] ?>" aria-selected="true"><ion-icon name="mail-outline" class="mr-2"></ion-icon>Mails</a>
                                          </li>
                                          <li class="nav-item">
                                            <a class="nav-link" id="statement-tab<?= $row['vendor_id'] ?>" data-toggle="tab" href="#statement<?= $row['vendor_id'] ?>" role="tab" aria-controls="statement<?= $row['vendor_id'] ?>" aria-selected="true"><ion-icon name="document-text-outline" class="mr-2"></ion-icon>Statement</a>
                                          </li>

                                          <li class="nav-item">
                                            <a class="nav-link" id="reconciliation-tab<?= $row['vendor_gstin'] ?>" data-toggle="tab" href="#reconciliation<?= $row['vendor_code'] ?>" role="tab" aria-controls="reconciliation<?= $row['vendor_id'] ?>" aria-selected="true"><ion-icon name="document-text-outline" class="mr-2"></ion-icon>Reconciliation</a>
                                          </li>

                                          <li class="nav-item">
                                            <a class="nav-link" id="compliance-tab<?= $row['vendor_gstin'] ?>" data-toggle="tab" href="#compliance<?= $row['vendor_gstin'] ?>" role="tab" aria-controls="compliance<?= $row['vendor_gstin'] ?>" aria-selected="true"><ion-icon name="document-text-outline" class="mr-2"></ion-icon>Compliance Status</a>
                                          </li>

                                          <!-- -------------------Audit History Button Start------------------------- -->
                                          <li class="nav-item">
                                            <a class="nav-link auditTrailVendor" id="history-tab<?= $row['vendor_id'] ?>" data-toggle="tab" data-ccode="<?= $row['vendor_code'] ?>" data-ids="<?= $row['vendor_id'] ?>" href="#history<?= $row['vendor_id'] ?>" role="tab" aria-controls="history<?= $row['vendor_id'] ?>" aria-selected="false"><ion-icon name="time-outline" class="mr-2"></ion-icon>Trail</a>
                                          </li>
                                          <!-- -------------------Audit History Button End------------------------- -->
                                        </ul>


                                        <!-- <form action="" method="POST">
                                          <div class="hamburger">
                                            <div class="wrapper-action">
                                              <ion-icon name="settings"></ion-icon>
                                            </div>
                                          </div>
                                          <div class="nav-action" id="reminder">
                                            <a title="Mail the customer" href="#" name="customerReminerBtn">
                                              <ion-icon name="notifications"></ion-icon>
                                            </a>
                                          </div>
                                          <div class="nav-action" id="edit">
                                            <a title="Mail the customer" href="#" name="customerEditBtn">
                                              <ion-icon name="create"></ion-icon>
                                            </a>
                                          </div>
                                          <div class="nav-action bg-danger" id="thumb">
                                            <a title="Chat the customer" href="#" name="customerEditBtn">
                                              <ion-icon name="trash"></ion-icon>
                                            </a>
                                          </div>
                                          <div class="nav-action" id="create">
                                            <a title="Call the customer" href="#" name="customerEditBtn">
                                              <ion-icon name="toggle"></ion-icon>
                                            </a>
                                          </div>
                                        </form> -->

                                        <div class="action-btns display-flex-gap" id="action-navbar">
                                          <?php $vendor_id = base64_encode($row['vendor_id']) ?>
                                          <form action="" method="POST">
                                            <!-- <a href="#" name="vendorRemindBtn">
                                          <ion-icon name="notifications"></ion-icon>
                                        </a> -->
                                            <a href="manage-vendors.php?edit=<?= $vendor_id ?>" name="vendorEditBtn">
                                              <ion-icon name="create"></ion-icon>
                                            </a>
                                            <!-- <a href="manage -vendors.php?delete=<?= $vendor_id ?>" name="vendorDeleteBtn">
                                          <ion-icon name="trash"></ion-icon>
                                        </a> -->
                                            <!-- <a href="">
                                          <ion-icon name="toggle"></ion-icon>
                                        </a> -->
                                          </form>
                                        </div>
                                      </div>

                                    </div>
                                    <!--Body-->
                                    <div class="modal-body blur-body p-4">
                                      <div class="tab-content p-0" id="myTabContent">
                                        <div class="tab-pane fade show active" id="home<?= $row['vendor_id'] ?>" role="tabpanel" aria-labelledby="home-tab">

                                          <div class="row">

                                            <!---------CHART_ONLY------->

                                            <div class="col-lg-12 col-md-12 col-xs-12">
                                              <div class="card flex-fill bg-transparent">
                                                <div class="card-header bg-transparent p-0 border-bottom-0">
                                                  <h5 class="card-title text-nowrap pl-3">Chart View</h5>

                                                  <div id="containerThreeDot">

                                                    <div id="menu-wrap">
                                                      <input type="checkbox" class="toggler bg-transparent" />
                                                      <div class="dots">
                                                        <div></div>
                                                      </div>
                                                      <?php
                                                      $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);
                                                      ?>
                                                      <div class="menu ">
                                                        <div>
                                                          <ul>
                                                            <li>
                                                              <select name="fYDropdown" id="fYDropdown_<?= $row['vendor_id'] ?>" data-attr="<?= $row['vendor_id'] ?> " class="form-control fYDropdown">
                                                                <?php
                                                                foreach ($variant_sql['data'] as $key => $data) {
                                                                ?>
                                                                  <option value="<?= $data['year_variant_id'] ?>"><?= $data['year_variant_name'] ?></option>
                                                                <?php
                                                                }
                                                                ?>
                                                              </select>
                                                            </li>
                                                            <li><label class="mb-0" for="">OR</label></li>
                                                            <li>
                                                              <input type="month" name="monthRange" id="monthRange_<?= $row['vendor_id'] ?>" data-attr="<?= $row['vendor_id'] ?> " class="form-control monthRange" style="max-width: 100%;" />
                                                            </li>
                                                          </ul>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>

                                                </div>
                                                <div class="card-body">
                                                  <div class="load-wrapp">
                                                    <div class="load-1">
                                                      <div class="line"></div>
                                                      <div class="line"></div>
                                                      <div class="line"></div>
                                                    </div>
                                                  </div>
                                                  <div id="chartDivSalesVsCollection_<?= $row['vendor_id'] ?>" class="chartContainer"></div>
                                                </div>
                                              </div>
                                            </div>

                                            <!---------CHART_ONLY------->

                                            <div class="col-lg-6 col-md-6 col-sm-6">






                                              <!--------BAsic Details--------->
                                              <div class="accordion matrix-accordion p-0" id="accordionExample">
                                                <div class="accordion-item">
                                                  <h2 class="accordion-header" id="headingOne">
                                                    <button class="accordion-button collpased btn btn-primary mt-0" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails" aria-expanded="false" aria-controls="collapseOne">
                                                      Basic Details
                                                    </button>
                                                  </h2>
                                                  <div id="basicDetails" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body p-0">
                                                      <div class="card bg-transparent">

                                                        <div class="card-body p-2">

                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Vendor Code :</p>
                                                            <p class="font-bold text-xs"><?= $row['vendor_code'] ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">GSTIN :</p>
                                                            <p class="font-bold text-xs"><?= $row['vendor_gstin'] ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Pan :</p>
                                                            <p class="font-bold text-xs"><?= $row['vendor_pan'] ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Trade Name :</p>
                                                            <p class="font-bold text-xs"><?= $row['trade_name'] ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Constitution of Business :</p>
                                                            <p class="font-bold text-xs"><?= $row['constitution_of_business'] ?></p>
                                                          </div>

                                                        </div>



                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>

                                              <!--------Address Details--------->
                                              <div class="accordion matrix-accordion p-0" id="accordionExample">
                                                <div class="accordion-item">
                                                  <h2 class="accordion-header" id="headingTwo">
                                                    <button class="accordion-button collpased btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#address" aria-expanded="false" aria-controls="collapseTwo">
                                                      Address
                                                    </button>
                                                  </h2>
                                                  <div id="address" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body p-0">

                                                      <div class="card bg-transparent">
                                                        <div class="card-body p-3">
                                                          <?php
                                                          $sqlAddress = "SELECT * FROM erp_vendor_bussiness_places WHERE vendor_id=" . $row['vendor_id'] . " AND  vendor_business_primary_flag=1 ";
                                                          if ($resAddress = $dbCon->query($sqlAddress)) {
                                                            if ($resAddress->num_rows > 0) {
                                                              while ($rowAddress = $resAddress->fetch_assoc()) {
                                                          ?>
                                                                <div class="display-flex-space-between">
                                                                  <p class="font-bold text-xs">State :</p>
                                                                  <p class="font-bold text-xs"><?= $rowAddress['vendor_business_state'] ?></p>
                                                                </div>
                                                                <div class="display-flex-space-between">
                                                                  <p class="font-bold text-xs">City :</p>
                                                                  <p class="font-bold text-xs"><?= $rowAddress['vendor_business_city'] ?></p>
                                                                </div>
                                                                <div class="display-flex-space-between">
                                                                  <p class="font-bold text-xs">District :</p>
                                                                  <p class="font-bold text-xs"><?= $rowAddress['vendor_business_district'] ?></p>
                                                                </div>
                                                                <div class="display-flex-space-between">
                                                                  <p class="font-bold text-xs">Location :</p>
                                                                  <p class="font-bold text-xs"><?= $rowAddress['vendor_business_location'] ?></p>
                                                                </div>
                                                                <div class="display-flex-space-between">
                                                                  <p class="font-bold text-xs">Building No :</p>
                                                                  <p class="font-bold text-xs"><?= $rowAddress['vendor_business_building_no'] ?></p>
                                                                </div>
                                                                <div class="display-flex-space-between">
                                                                  <p class="font-bold text-xs">Flat No :</p>
                                                                  <p class="font-bold text-xs"><?= $rowAddress['vendor_business_flat_no'] ?></p>
                                                                </div>
                                                                <div class="display-flex-space-between">
                                                                  <p class="font-bold text-xs">Street Name :</p>
                                                                  <p class="font-bold text-xs"><?= $rowAddress['vendor_business_street_name'] ?></p>
                                                                </div>

                                                                <hr>

                                                          <?php

                                                              }
                                                            } else {
                                                              echo "<p style='font-size: 10px;'>Data not found</p>";
                                                            }
                                                          } else {
                                                            echo "<p style='font-size: 10px;'>Somthing went wrong</p>";
                                                          }
                                                          ?>
                                                        </div>
                                                      </div>



                                                    </div>
                                                  </div>
                                                </div>

                                              </div>



                                              <!--------Account Details--------->
                                              <div class="accordion matrix-accordion p-0" id="accordionExample">
                                                <div class="accordion-item">
                                                  <h2 class="accordion-header" id="headingThree">
                                                    <button class="accordion-button collpased btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#accounting" aria-expanded="false" aria-controls="collapseThree">
                                                      Accounting
                                                    </button>
                                                  </h2>
                                                  <div id="accounting" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body p-0">

                                                      <div class="card bg-transparent">
                                                        <div class="card-body p-3">
                                                          <?php
                                                          $sqlAccount = "SELECT * FROM erp_vendor_bank_details WHERE vendor_id='$vendorId'";
                                                          if ($resAccount = $dbCon->query($sqlAccount)) {
                                                            if ($resAccount->num_rows > 0) {
                                                              while ($rowAccount = $resAccount->fetch_assoc()) {
                                                          ?>
                                                                <div class="display-flex-space-between">
                                                                  <p class="font-bold text-xs">Credit Period (in days) :</p>
                                                                  <p class="font-bold text-xs"><?= $rowAccount['credit_period'] ?></p>
                                                                </div>
                                                                <div class="display-flex-space-between">
                                                                  <p class="font-bold text-xs">Bank Name :</p>
                                                                  <p class="font-bold text-xs"><?= $rowAccount['vendor_bank_name'] ?></p>
                                                                </div>
                                                                <div class="display-flex-space-between">
                                                                  <p class="font-bold text-xs">Account Number :</p>
                                                                  <p class="font-bold text-xs"><?= $rowAccount['vendor_bank_account_no'] ?></p>
                                                                </div>
                                                                <div class="display-flex-space-between">
                                                                  <p class="font-bold text-xs">IFSC :</p>
                                                                  <p class="font-bold text-xs"><?= $rowAccount['vendor_bank_ifsc'] ?></p>
                                                                </div>
                                                                <div class="display-flex-space-between">
                                                                  <p class="font-bold text-xs">Branch Name :</p>
                                                                  <p class="font-bold text-xs"><?= $rowAccount['vendor_bank_branch'] ?></p>
                                                                </div>
                                                                <div class="display-flex-space-between">
                                                                  <p class="font-bold text-xs"> Address :</p>
                                                                  <p class="font-bold text-xs w-75"><?= $rowAccount['vendor_bank_address'] ?></p>
                                                                </div>

                                                                <hr>

                                                          <?php
                                                              }
                                                            } else {
                                                              echo "<p style='font-size: 10px;'>Data not found</p>";
                                                            }
                                                          } else {
                                                            echo "<p style='font-size: 10px;'>Somthing went wrong</p>";
                                                          }
                                                          ?>
                                                        </div>
                                                      </div>



                                                    </div>
                                                  </div>
                                                </div>

                                              </div>



                                              <!--------OtheraddressDetails--------->
                                              <div class="accordion matrix-accordion p-0" id="accordionExample">
                                                <div class="accordion-item">
                                                  <h2 class="accordion-header" id="headingFour">
                                                    <button class="accordion-button collpased btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#otherAddress" aria-expanded="false" aria-controls="collapseFour">
                                                      Other Address
                                                    </button>
                                                  </h2>
                                                  <div id="otherAddress" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body p-0">

                                                      <div class="card bg-transparent">
                                                        <div class="card-body p-3">
                                                          <?php
                                                          $sqlbplace = "SELECT * FROM erp_vendor_bussiness_places WHERE vendor_business_primary_flag=0 AND vendor_id='$vendorId'";
                                                          $returnbplace = queryGet($sqlbplace, true);

                                                          if ($returnbplace['status'] = 'success') {
                                                            foreach ($returnbplace['data'] as $rowbplace) {
                                                          ?>
                                                              <div class="display-flex-space-between">
                                                                <p class="font-bold text-xs">State :</p>
                                                                <p class="font-bold text-xs"><?= $rowbplace['vendor_business_state'] ?></p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="font-bold text-xs">City :</p>
                                                                <p class="font-bold text-xs"><?= $rowbplace['vendor_business_city'] ?></p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="font-bold text-xs">District :</p>
                                                                <p class="font-bold text-xs"><?= $rowbplace['vendor_business_district'] ?></p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="font-bold text-xs">Location :</p>
                                                                <p class="font-bold text-xs"><?= $rowbplace['vendor_business_location'] ?></p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="font-bold text-xs">Building No :</p>
                                                                <p class="font-bold text-xs"><?= $rowbplace['vendor_business_building_no'] ?></p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="font-bold text-xs">Flat No :</p>
                                                                <p class="font-bold text-xs"><?= $rowbplace['vendor_business_flat_no'] ?></p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="font-bold text-xs">Street Name :</p>
                                                                <p class="font-bold text-xs"><?= $rowbplace['vendor_business_street_name'] ?></p>
                                                              </div>
                                                          <?php

                                                            }
                                                          } else {
                                                            echo "<p style='font-size: 10px;'>No Address Found</p>";
                                                          }
                                                          ?>
                                                        </div>
                                                      </div>



                                                    </div>
                                                  </div>
                                                </div>

                                              </div>



                                            </div>

                                            <!---------CHART_ONLY------->

                                            <div class="col-lg-6 col-md-6 col-xs-12">
                                              <div class="card flex-fill bg-transparent">
                                                <div class="card-header">

                                                  <h5 class="card-title text-nowrap pl-3">Payables Ageing</h5>


                                                  <div id="containerThreeDot">

                                                    <div id="menu-wrap">
                                                      <input type="checkbox" class="toggler bg-transparent" />
                                                      <div class="dots">
                                                        <div></div>
                                                      </div>
                                                      <?php
                                                      $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);
                                                      ?>
                                                      <div class="menu ">
                                                        <div>
                                                          <ul>
                                                            <li>
                                                              <select name="piefYDropdown" id="piefYDropdown_<?= $row['vendor_id'] ?>" data-attr="<?= $row['vendor_id'] ?> " class="form-control piefYDropdown">
                                                                <?php
                                                                foreach ($variant_sql['data'] as $key => $data) {
                                                                ?>
                                                                  <option value="<?= $data['year_variant_id'] ?>"><?= $data['year_variant_name'] ?></option>
                                                                <?php
                                                                }
                                                                ?>
                                                              </select>
                                                            </li>
                                                          </ul>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="card-body">
                                                  <div class="load-wrapp">
                                                    <div class="load-1">
                                                      <div class="line"></div>
                                                      <div class="line"></div>
                                                      <div class="line"></div>
                                                    </div>
                                                  </div>
                                                  <div id="chartDivPayableAgeing_<?= $row['vendor_id'] ?>" class="pieChartContainer">

                                                    <!-- <div class="head-title h-100">
                                                      <h5 class="card-title w-100 h-100 chartDivPayableAgeing_<?= $row['vendor_id'] ?>">Payables Ageing</h5>
                                                    </div> -->
                                                  </div>

                                                </div>
                                              </div>
                                            </div>

                                            <!---------CHART_ONLY------->




                                          </div>
                                          <!--/.Content-->
                                        </div>
                                        <div class="tab-pane fade" id="transaction<?= $row['vendor_id'] ?>" role="tabpanel" aria-labelledby="transaction-tab">
                                          <div class="row p-3">
                                            <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                              <ul class="nav nav-pills flex-row justify-content-center" id="experienceTab" role="tablist">
                                                <li class="nav-item">
                                                  <a class="nav-link active" id="estimate-tab<?= $row['vendor_id'] ?>" data-toggle="tab" href="#estimate<?= $row['vendor_id'] ?>" role="tab" aria-controls="estimate<?= $row['vendor_id'] ?>" aria-selected="false">Quotations</a>
                                                </li>
                                                <li class="nav-item">
                                                  <a class="nav-link" id="po-tab<?= $row['vendor_id'] ?>" data-toggle="tab" href="#po<?= $row['vendor_id'] ?>" role="tab" aria-controls="po<?= $row['vendor_id'] ?>" aria-selected="false">Purchase Orders</a>
                                                </li>
                                                <li class="nav-item">
                                                  <a class="nav-link" id="invoices-tab<?= $row['vendor_id'] ?>" data-toggle="tab" href="#invoices<?= $row['vendor_id'] ?>" role="tab" aria-controls="invoices<?= $row['vendor_id'] ?>" aria-selected="true">Bills</a>
                                                </li>
                                                <li class="nav-item">
                                                  <a class="nav-link" id="vendorPayments-tab<?= $row['vendor_id'] ?>" data-toggle="tab" href="#vendorPayments<?= $row['vendor_id'] ?>" role="tab" aria-controls="vendorPayments<?= $row['vendor_id'] ?>" aria-selected="false"> Payments</a>
                                                </li>
                                                <!-- <li class="nav-item">
                                                  <a class="nav-link" id="daybook-tab<?= $row['vendor_id'] ?>" data-toggle="tab" href="#daybook<?= $row['vendor_id'] ?>" role="tab" aria-controls="daybook<?= $row['vendor_id'] ?>" aria-selected="false">Daybook</a>
                                                </li> -->
                                                <li class="nav-item">
                                                  <a class="nav-link" id="journals-tab<?= $row['vendor_id'] ?>" data-toggle="tab" href="#journals<?= $row['vendor_id'] ?>" role="tab" aria-controls="journals<?= $row['vendor_id'] ?>" aria-selected="false">Journals</a>
                                                </li>
                                                <li class="nav-item">
                                                  <a class="nav-link debitNoteBtn" id="debitNote-tab<?= $row['vendor_id'] ?>" data-toggle="tab" href="#debitNote<?= $row['vendor_id'] ?>" role="tab" aria-controls="debitNote<?= $row['vendor_id'] ?>" aria-selected="false" data-vid="<?= $row['vendor_id'] ?>">Debit Note</a>
                                                </li>
                                                <li class="nav-item">
                                                  <a class="nav-link creditNoteBtn" id="creditNote-tab<?= $row['vendor_id'] ?>" data-toggle="tab" href="#creditNote<?= $row['vendor_id'] ?>" role="tab" aria-controls="creditNote<?= $row['vendor_id'] ?>" aria-selected="false" data-vid="<?= $row['vendor_id'] ?>">Credit Note</a>
                                                </li>
                                              </ul>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                              <div class="tab-content pl-0" id="experienceTabContent">
                                                <div class="tab-pane fade active show text-left text-light estimate-tab" id="estimate<?= $row['vendor_id'] ?>" role="tabpanel" aria-labelledby="estimate-tab">
                                                  <h3>Quotations
                                                    <a href="manage-rfq.php" target="_blank" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                                                  </h3>
                                                  <div class="card">
                                                    <div class="card-body p-0" style="overflow: auto;">
                                                      <table>
                                                        <thead>
                                                          <tr>
                                                            <th>RFQ Code</th>
                                                            <th>Item</th>
                                                            <th>MOQ</th>
                                                            <th>Price</th>
                                                            <th>Discount</th>
                                                            <th>Total</th>
                                                            <th>GST</th>
                                                            <th>Lead Time</th>
                                                          </tr>
                                                        </thead>
                                                        <tbody>
                                                          <?php
                                                          $quotation_sql = queryGet("SELECT * FROM `erp_vendor_item` as item LEFT JOIN `erp_vendor_response` as response ON response.erp_v_id = item.erp_v_id WHERE response.vendor_id = '" . $row['vendor_id'] . "'", true);
                                                          // console($quotation_sql);
                                                          foreach ($quotation_sql['data'] as $quotation) {
                                                          ?>
                                                            <tr>

                                                              <td>
                                                                <p class="company-name mt-1"><?= $quotation['rfq_code'] ?></p>
                                                              </td>
                                                              <td>
                                                                <?= $quotation['item_name'] . "(" . $quotation['item_code'] . ")" ?>
                                                              </td>
                                                              <td>
                                                                <?= $quotation['moq'] ?>
                                                              </td>
                                                              <td>
                                                                <?= $quotation['price'] ?>
                                                              </td>
                                                              <td>
                                                                <?= $quotation['discount'] ?>
                                                              </td>
                                                              <td>
                                                                <?= $quotation['total'] ?>
                                                              </td>
                                                              <td>
                                                                <?= $quotation['gst'] ?>
                                                              </td>
                                                              <td>
                                                                <?= $quotation['lead_time'] ?>
                                                              </td>
                                                            </tr>
                                                          <?php
                                                          }
                                                          ?>
                                                        </tbody>
                                                      </table>
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="tab-pane fade text-left text-light" id="invoices<?= $row['vendor_id'] ?>" role="tabpanel" aria-labelledby="hoinvoicesme-tab">
                                                  <h3>Bills
                                                    <a href="manage-vendor-invoice.php" target="_blank" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                                                  </h3>
                                                  <div class="card">
                                                    <div class="card-body p-0" style="overflow: auto;">
                                                      <table>
                                                        <thead>
                                                          <tr>

                                                            <th> Number</th>
                                                            <th> Date</th>
                                                            <th>GRN/SRN Code</th>
                                                            <th>GRN/SRN Date</th>
                                                            <th>PO Number</th>
                                                            <th>PO Date</th>
                                                            <th>IV Number</th>
                                                            <th>IV Date</th>
                                                            <th>Due Date</th>
                                                            <th> Basic Amount</th>
                                                            <th>GST</th>
                                                            <th>TDS</th>
                                                            <th>Net Payable</th>
                                                            <th>Payment Made</th>
                                                            <th>Due Amt</th>
                                                            <th>Due%</th>
                                                            <th>Status</th>
                                                            <th>Reversal Status</th>

                                                          </tr>
                                                        </thead>
                                                        <tbody>
                                                          <?php
                                                          $vendor_id = $row['vendor_id'];
                                                          $grnObj = new GrnController();
                                                          $fetchInvoice = queryGet("SELECT grniv.*, grn.`grnCreatedAt` AS grnDate, grn.`po_date` AS poDate FROM `" . ERP_GRNINVOICE . "` AS grniv, `erp_grn` AS grn WHERE grniv.`companyId`='$company_id' AND grniv.`grnId` = grn.`grnId` AND grniv.`branchId`='$branch_id' AND grniv.`locationId`='$location_id' AND grniv.`grnStatus`!='deleted' AND grniv.vendorId = $vendor_id ORDER BY grniv.`grnIvId` DESC", true);
                                                          // $fetchInvoice = queryGet("SELECT grniv.*, grn.`grnCreatedAt` AS grnDate, grn.`po_date` AS poDate FROM `" . ERP_GRNINVOICE . "` AS grniv LEFT JOIN `erp_grn` AS grn ON grn.`grnId` = grniv.`grnId` WHERE 1 ".$cond." AND grniv.`companyId`='$company_id' AND grniv.`branchId`='$branch_id' AND grniv.`locationId`='$location_id' AND `vendorId` = $vendor_id ORDER BY grniv.`grnIvId` DESC limit ");
                                                          // console($fetchInvoice);
                                                          foreach ($fetchInvoice['data'] as  $key => $inv) {
                                                            //console($inv);
                                                            $days = $inv['credit_period'];
                                                            $date = date_create($inv['invoice_date']);
                                                            date_add($date, date_interval_create_from_date_string($days . " days"));
                                                            $creditPeriod = date_format($date, "Y-m-d");
                                                            // console($inv);
                                                            $statusLabel = fetchStatusMasterByCode($inv['paymentStatus'])['data']['label'];
                                                            $statusClass = "";
                                                            if ($statusLabel == "paid") {
                                                              $statusClass = "status";
                                                            } elseif ($statusLabel == "pending") {
                                                              $statusClass = "status-warning";
                                                            } elseif ($statusLabel == "partial paid") {
                                                              $statusClass = "status-secondary";
                                                            } elseif ($statusLabel == "Payment Initiated") {
                                                              $statusClass = "status-info";
                                                            } else {
                                                              $statusClass = "status-danger";
                                                            }
                                                            if ($one['grnStatus'] == 'reverse') {
                                                              $statusLabel = 'Reversed';
                                                              $statusClass = "status-warning";
                                                            }

                                                          ?>
                                                            <tr>
                                                              <td>
                                                                <p class="company-name mt-1"><?= $inv['vendorDocumentNo'] ?></p>
                                                              </td>
                                                              <td>
                                                                <?= formatDateORDateTime($inv['vendorDocumentDate']) ?>
                                                              </td>
                                                              <td>
                                                                <?= $inv['grnCode'] ?>
                                                              </td>
                                                              <td>
                                                                <?= formatDateORDateTime($inv['grnDate']) ?>
                                                              </td>
                                                              <td>
                                                                <?= $inv['grnPoNumber'] ?>
                                                              </td>
                                                              <td>
                                                                <?= formatDateORDateTime($inv['poDate']) ?>
                                                              </td>
                                                              <td>
                                                                <?= $inv['grnIvCode'] ?>
                                                              </td>
                                                              <td>
                                                                <?= formatDateORDateTime($inv['postingDate']) ?>
                                                              </td>
                                                              <td>
                                                                <?= formatDateORDateTime($inv['dueDate']) ?>
                                                              </td>
                                                              <td>
                                                                <?= $inv['grnSubTotal'] ?>
                                                              </td>
                                                              <td>
                                                                <?= $inv['grnTotalCgst'] + $inv['grnTotalSgst'] + $inv['grnTotalIgst'] ?>
                                                              </td>
                                                              <td>
                                                                <?= $inv['grnTotalTds'] ?>
                                                              </td>
                                                              <td>
                                                                <?= $inv['grnTotalAmount'] ?>
                                                              </td>
                                                              <td>
                                                                <?= $inv['grnTotalAmount'] -  $inv['dueAmt']  ?>
                                                              </td>
                                                              <td>
                                                                <?= $inv['dueAmt'] ?>
                                                              </td>
                                                              <?php
                                                              $due_amt = $inv['dueAmt'];
                                                              $inv_amt = $inv['grnTotalAmount'];
                                                              $duePercentage = ($due_amt / $inv_amt) * 100;
                                                              ?>
                                                              <td>
                                                                <?= round($duePercentage);  ?>
                                                              </td>
                                                              <td>
                                                                <span class="text-uppercase <?= $statusClass ?>">
                                                                  <?= $statusLabel ?>
                                                                </span>
                                                              </td>
                                                              <td>
                                                                <?php
                                                                if ($inv['grnStatus'] == 'reverse') {
                                                                  echo 'Reversed';
                                                                } else {
                                                                  echo '-';
                                                                }
                                                                ?>
                                                              </td>
                                                            </tr>
                                                          <?php
                                                          }
                                                          ?>
                                                        </tbody>
                                                      </table>
                                                    </div>
                                                  </div>
                                                </div>


                                                <div class="tab-pane fade text-left text-light" id="vendorPayments<?= $row['vendor_id'] ?>" role="tabpanel" aria-labelledby="vendorPayments-tab">
                                                  <h3> Payments
                                                    <a href="manage-payments.php" target="_blank" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                                                  </h3>
                                                  <div class="card">
                                                    <div class="card-body p-0" style="overflow: auto;">
                                                      <table>
                                                        <thead>
                                                          <tr>
                                                            <th>Transaction Id</th>
                                                            <th>Payment Type</th>
                                                            <th>Collect Payment</th>
                                                            <th>Posting Date</th>
                                                            <th> Status</th>
                                                          </tr>
                                                        </thead>
                                                        <tbody>
                                                          <?php
                                                          $payment_sql = queryGet("SELECT * FROM " . ERP_GRN_PAYMENTS . " WHERE company_id='" . $company_id . "' AND `vendor_id` = '" . $row['vendor_id'] . "' ORDER BY payment_id DESC", true);
                                                          // console($payment_sql);
                                                          foreach ($payment_sql['data'] as $payment) {
                                                          ?>
                                                            <tr>
                                                              <td>
                                                                <p class="company-name mt-1"><?= $payment['transactionId'] ?></p>
                                                              </td>
                                                              <td>
                                                              <?php echo ($payment['paymentCollectType'] == "collect") ? "Receipt" : $payment['paymentCollectType'];?>

                                                              </td>
                                                              <td>
                                                              <?= $payment['collect_payment'] ?>
                                                              </td>
                                                              <td>
                                                                <?= $payment['postingDate'] ?>
                                                              </td>
                                                              <td>
                                                              <td>
                                                                <?= $payment['status'] ?>
                                                              </td>
                                                            </tr>
                                                          <?php
                                                          }
                                                          ?>
                                                        </tbody>
                                                      </table>
                                                    </div>
                                                  </div>
                                                </div>


                                                <div class="tab-pane fade text-left text-light" id="po<?= $row['vendor_id'] ?>" role="tabpanel" aria-labelledby="po-tab">
                                                  <h3>Purchase Orders
                                                    <a href="manage-purchases-orders.php" target="_blank" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>

                                                  </h3>
                                                  <div class="card">
                                                    <div class="card-body p-0" style="overflow: auto;">
                                                      <table>
                                                        <thead>
                                                          <tr>
                                                            <th>PO Number</th>
                                                            <th>Reference Number</th>
                                                            <th>PO Type</th>
                                                            <th>Delivery Date</th>
                                                            <th>Posting Date</th>
                                                            <th> Total Amount</th>
                                                            <th> Total Quantity</th>
                                                          </tr>
                                                        </thead>
                                                        <tbody>
                                                          <?php
                                                          $po_sql = queryGet("SELECT * FROM `erp_branch_purchase_order` WHERE company_id='" . $company_id . "' AND `vendor_id` = '" . $row['vendor_id'] . "'", true);
                                                          // console($po_sql);
                                                          foreach ($po_sql['data'] as $po) {
                                                          ?>
                                                            <tr>
                                                              <td>
                                                                <p class="company-name mt-1"><?= $po['po_number'] ?></p>
                                                              </td>
                                                              <td>
                                                                <?= $po['ref_no'] ?>
                                                              </td>
                                                              <td>
                                                                <?= $po['use_type'] ?>
                                                              </td>
                                                              <td>
                                                                <?= formatDateORDateTime($po['delivery_date']) ?>
                                                              </td>
                                                              <td>
                                                                <?= formatDateORDateTime($po['po_date']) ?>
                                                              </td>
                                                              <td>
                                                                <?= $po['totalAmount'] ?>
                                                              </td>
                                                              <td>
                                                                <?= $po['totalItems'] ?>
                                                              </td>
                                                            </tr>

                                                          <?php
                                                          }
                                                          ?>
                                                        </tbody>
                                                      </table>


                                                    </div>
                                                  </div>
                                                </div>




                                                <div class="tab-pane fade text-left text-light" id="journals<?= $row['vendor_id'] ?>" role="tabpanel" aria-labelledby="journals-tab">
                                                  <h3>Journals</h3>
                                                  <div class="card bg-transparent">
                                                    <div class="card-body p-0">
                                                      <table>
                                                        <thead>
                                                          <tr>
                                                            <th>Journal Number</th>
                                                            <th>Reference Code</th>
                                                            <th>Document Number</th>
                                                            <th> Document Date</th>
                                                            <th>Posting Date</th>
                                                            <th> Narration</th>

                                                          </tr>
                                                        </thead>
                                                        <tbody>
                                                          <?php

                                                          $journal = queryGet("SELECT * FROM `erp_acc_journal` WHERE `party_code`='" . $row['vendor_code'] . "' AND `company_id`=$company_id AND `location_id`=$location_id AND `parent_slug`='journal'
                                                             ", true);

                                                          // console($journal);

                                                          $journal_data = $journal['data'];
                                                          foreach ($journal_data as $data) {

                                                            // console($data);


                                                          ?>
                                                            <td>
                                                              <?= $data['jv_no'] ?>
                                                            </td>
                                                            <td>
                                                              <?= $data['refarenceCode'] ?>
                                                            </td>
                                                            <td>
                                                              <?= $data['documentNo'] ?>
                                                            </td>
                                                            <td>
                                                              <?= $data['documentDate'] ?>
                                                            </td>
                                                            <td>
                                                              <?= $data['postingDate'] ?>
                                                            </td>

                                                            <td>
                                                              <?php echo WordLimiter($data['remark'], 5);
                                                              ?>
                                                            </td>

                                                          <?php

                                                          }

                                                          ?>
                                                        </tbody>
                                                      </table>
                                                    </div>
                                                  </div>
                                                </div>



                                                <div class="tab-pane fade text-left text-light" id="daybook<?= $row['vendor_id'] ?>" role="tabpanel" aria-labelledby="daybook-tab">
                                                  <h3>Daybook</h3>
                                                  <span class="date-range code-font">Other Details</span>



                                                  <div class="card">
                                                    <div class="card-body p-0" style="overflow: auto;">
                                                      <table>
                                                        <thead>
                                                          <tr>
                                                            <th>Accounting Document No</th>
                                                            <th>Document Number</th>
                                                            <th>Posting Date</th>
                                                            <th>Delivery Date</th>
                                                            <th>Created By</th>
                                                            <th>Order No</th>
                                                            <th>Order Date</th>
                                                            <th>GL Code</th>
                                                            <th>GL Name</th>
                                                            <th>Transaction Type</th>
                                                            <th>Narration</th>
                                                            <th>Type(Dr/Cr)</th>
                                                            <th>Amount</th>
                                                            <th>Clearing Document No</th>
                                                            <th>Clearing Document Date</th>
                                                            <th>Cleared By</th>
                                                          </tr>
                                                        </thead>
                                                        <tbody>
                                                          <?php

                                                          $sql = queryGet("SELECT
                                                            summary1.*,
                                                            CASE WHEN Order_num LIKE 'PO%' THEN(
                                                            SELECT
                                                            po_date
                                                            FROM
                                                            erp_branch_purchase_order
                                                            WHERE
                                                            po_number = summary1.Order_num AND company_id = 1
                                                            ) WHEN Order_num LIKE 'SO%' THEN(
                                                            SELECT
                                                            so_date
                                                            FROM
                                                            erp_branch_sales_order
                                                            WHERE
                                                            so_number = summary1.Order_num AND company_id = 1
                                                            )
                                                            END AS order_date
                                                            FROM
                                                            (
                                                            SELECT
                                                            table1.jid AS jid,
                                                            table1.company_id AS company_id,
                                                            table1.branch_id AS branch_id,
                                                            table1.location_id AS location_id,
                                                            table1.jv_no AS jv_no,
                                                            table1.party_code AS party_code,
                                                            table1.party_name AS party_name,
                                                            table1.refarenceCode AS referenceCode,
                                                            table1.parent_id AS parent_id,
                                                            table1.parent_slug AS parent_slug,
                                                            table1.journal_entry_ref AS journal_entry_ref,
                                                            table1.documentNo AS documentNo,
                                                            table1.order_no AS Order_num,
                                                            table1.documentDate AS document_date,
                                                            table1.postingDate AS postingDate,
                                                            table1.remark AS remark,
                                                            table1.glId AS glId,
                                                            coa.gl_code AS gl_code,
                                                            coa.gl_label AS gl_label,
                                                            coa.typeAcc AS typeAcc,
                                                            table1.Amount AS Amount,
                                                            table1.Type AS TYPE,
                                                            table1.journal_created_at AS journal_created_at,
                                                            table1.journal_created_by AS journal_created_by,
                                                            table1.journal_updated_at AS journal_updated_at,
                                                            table1.journal_updated_by AS journal_updated_by
                                                            FROM
                                                            (
                                                            (
                                                            SELECT
                                                                *,
                                                                CASE WHEN parent_slug = 'PGI' THEN(
                                                                SELECT
                                                                    so_number
                                                                FROM
                                                                    erp_branch_sales_order_delivery_pgi
                                                                WHERE
                                                                    so_delivery_pgi_id = main_report.parent_id
                                                                LIMIT 1
                                                            ) WHEN parent_slug = 'SOInvoicing' THEN(
                                                            SELECT
                                                                so_number
                                                            FROM
                                                                erp_branch_sales_order_invoices
                                                            WHERE
                                                                so_invoice_id = main_report.parent_id
                                                            LIMIT 1
                                                            ) WHEN parent_slug = 'grn' THEN(
                                                            SELECT
                                                            grnPoNumber
                                                            FROM
                                                            erp_grn
                                                            WHERE
                                                            grnId = main_report.parent_id
                                                            LIMIT 1
                                                            ) WHEN parent_slug = 'grniv' THEN(
                                                            SELECT
                                                            grnPoNumber
                                                            FROM
                                                            erp_grn
                                                            WHERE
                                                            grnId = main_report.parent_id
                                                            LIMIT 1
                                                            )
                                                            END AS Order_no
                                                            FROM
                                                            (
                                                            SELECT
                                                            journal.id AS jid,
                                                            journal.company_id AS company_id,
                                                            journal.branch_id AS branch_id,
                                                            journal.location_id AS location_id,
                                                            journal.jv_no AS jv_no,
                                                            journal.party_code AS party_code,
                                                            journal.party_name AS party_name,
                                                            journal.refarenceCode AS refarenceCode,
                                                            journal.parent_id AS parent_id,
                                                            journal.parent_slug AS parent_slug,
                                                            journal.journalEntryReference AS journal_entry_ref,
                                                            journal.documentNo AS documentNo,
                                                            journal.documentDate AS documentDate,
                                                            journal.postingDate AS postingDate,
                                                            journal.remark AS remark,
                                                            journal.journal_status AS journal_status,
                                                            debit.glId AS glId,
                                                            debit.debit_amount AS Amount,
                                                            'DR' AS TYPE,
                                                            journal.journal_created_at AS journal_created_at,
                                                            journal.journal_created_by AS journal_created_by,
                                                            journal.journal_updated_at AS journal_updated_at,
                                                            journal.journal_updated_by AS journal_updated_by
                                                            FROM
                                                            `erp_acc_journal` AS journal
                                                            INNER JOIN(
                                                            SELECT
                                                                journal_id,
                                                                glId,
                                                                SUM(debit_amount) AS debit_amount
                                                            FROM
                                                                `erp_acc_debit`
                                                            GROUP BY
                                                                journal_id,
                                                                glId
                                                            ) AS debit
                                                            ON
                                                            debit.journal_id = journal.id
                                                            WHERE
                                                            journal.journal_status = 'active' AND journal.company_id = 1 AND journal.branch_id = 1 AND journal.location_id = 8 AND journal.postingDate BETWEEN '2022-01-01' AND '2023-12-31' AND journal.party_code=61221101
                                                            ) AS main_report
                                                            )
                                                            UNION
                                                            (
                                                            SELECT
                                                            *,
                                                            CASE WHEN parent_slug = 'PGI' THEN(
                                                            SELECT
                                                                so_number
                                                            FROM
                                                                erp_branch_sales_order_delivery_pgi
                                                            WHERE
                                                                so_delivery_pgi_id = mainReport.parent_id
                                                            LIMIT 1
                                                            ) WHEN parent_slug = 'SOInvoicing' THEN(
                                                            SELECT
                                                            so_number
                                                            FROM
                                                            erp_branch_sales_order_invoices
                                                            WHERE
                                                            so_invoice_id = mainReport.parent_id
                                                            LIMIT 1
                                                            ) WHEN parent_slug = 'grn' THEN(
                                                            SELECT
                                                            grnPoNumber
                                                            FROM
                                                            erp_grn
                                                            WHERE
                                                            grnId = mainReport.parent_id
                                                            LIMIT 1
                                                            ) WHEN parent_slug = 'grniv' THEN(
                                                            SELECT
                                                            grnPoNumber
                                                            FROM
                                                            erp_grn
                                                            WHERE
                                                            grnId = mainReport.parent_id
                                                            LIMIT 1
                                                            )
                                                            END AS Order_no
                                                            FROM
                                                            (
                                                            SELECT
                                                            journal.id AS jid,
                                                            journal.company_id AS company_id,
                                                            journal.branch_id AS branch_id,
                                                            journal.location_id AS location_id,
                                                            journal.jv_no AS jv_no,
                                                            journal.party_code AS party_code,
                                                            journal.party_name AS party_name,
                                                            journal.refarenceCode AS refarenceCode,
                                                            journal.parent_id AS parent_id,
                                                            journal.parent_slug AS parent_slug,
                                                            journal.journalEntryReference AS journal_entry_ref,
                                                            journal.documentNo AS documentNo,
                                                            journal.documentDate AS documentDate,
                                                            journal.postingDate AS postingDate,
                                                            journal.remark AS remark,
                                                            journal.journal_status AS journal_status,
                                                            credit.glId AS glId,
                                                            credit.credit_amount *(-1) AS Amount,
                                                            'CR' AS TYPE,
                                                            journal.journal_created_at AS journal_created_at,
                                                            journal.journal_created_by AS journal_created_by,
                                                            journal.journal_updated_at AS journal_updated_at,
                                                            journal.journal_updated_by AS journal_updated_by
                                                            FROM
                                                            `erp_acc_journal` AS journal
                                                            INNER JOIN(
                                                            SELECT
                                                            journal_id,
                                                            glId,
                                                            SUM(credit_amount) AS credit_amount
                                                            FROM
                                                            `erp_acc_credit`
                                                            GROUP BY
                                                            journal_id,
                                                            glId
                                                            ) AS credit
                                                            ON
                                                            credit.journal_id = journal.id
                                                            WHERE
                                                            journal.journal_status = 'active' AND journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '2022-01-01' AND '2023-12-31' AND journal.party_code='" . $row['vendor_code'] . "'
                                                            ) AS mainReport
                                                            )
                                                            ) AS table1
                                                            INNER JOIN `erp_acc_coa_1_table` AS coa
                                                            ON
                                                            table1.glId = coa.id
                                                            ) AS summary1
                                                            ORDER BY
                                                            summary1.jid
                                                            DESC", true);

                                                          //  console($sql['data']);

                                                          foreach ($sql['data'] as $journal) {
                                                          ?>
                                                            <tr>
                                                              <td><?= $journal['jv_no'] ?></td>
                                                              <td><?= $journal['documentNo'] ?></td>
                                                              <td><?= formatDateORDateTime($journal['postingDate']); ?></td>
                                                              <td><?= formatDateORDateTime($journal['journal_created_at']) ?></td>
                                                              <td><?= getCreatedByUser($journal['journal_created_by']) ?></td>
                                                              <td> <?= $journal['Order_num']  ?></td>
                                                              <td><?= formatDateORDateTime($journal['document_date']) ?></td>
                                                              <td><?= $journal['gl_code'] ?? '-' ?></td>
                                                              <td><?= $journal['gl_label'] ?? '-' ?></td>
                                                              <td><?= $journal['journal_entry_ref'] ?? '-' ?></td>
                                                              <td><?= WordLimiter($journal['remark'], 5) ?></td>
                                                              <td><?= $journal['type'] ?? '-' ?></td>
                                                              <td><?= $journal['Amount'] ?? '-' ?></td>
                                                              <td><?= '-' ?></td>
                                                              <td><?= '-' ?></td>
                                                              <td><?= '-' ?></td>
                                                            </tr>
                                                          <?php
                                                          }
                                                          ?>
                                                        </tbody>
                                                      </table>

                                                      <!-- <div class="row">

                                                          <div class="col col-body">
                                                            <?= $journal['jv_no'] ?>
                                                          </div>
                                                          <div class="col col-body">
                                                            <?= $journal['documentNo'] ?>
                                                          </div>
                                                          <div class="col col-body">
                                                            <?= formatDateORDateTime($journal['postingDate']); ?>
                                                          </div>
                                                          <div class="col col-body">
                                                            <?= formatDateORDateTime($journal['journal_created_at']) ?>
                                                          </div>
                                                          <div class="col col-body">
                                                            <?= getCreatedByUser($journal['journal_created_by']) ?>
                                                          </div>
                                                          <div class="col col-body">
                                                            <?= $journal['Order_num']  ?>
                                                          </div>
                                                          <div class="col col-body">
                                                            <?= formatDateORDateTime($journal['document_date']) ?>
                                                          </div>

                                                          <div class="col col-body">
                                                            <?= $journal['gl_code'] ?? '-' ?>
                                                          </div>

                                                          <div class="col col-body">
                                                            <?= $journal['gl_label'] ?? '-' ?>
                                                          </div>

                                                          <div class="col col-body">
                                                            <?= $journal['journal_entry_ref'] ?? '-' ?>
                                                          </div>

                                                          <div class="col col-body">
                                                            <?= WordLimiter($journal['remark'], 5) ?>
                                                          </div>

                                                          <div class="col col-body">
                                                            <?= $journal['type'] ?? '-' ?>
                                                          </div>

                                                          <div class="col col-body">
                                                            <?= $journal['Amount'] ?? '-' ?>
                                                          </div>

                                                          <div class="col col-body">
                                                            <?= '-' ?>
                                                          </div>


                                                          <div class="col col-body">
                                                            <?= '-' ?>
                                                          </div>

                                                          <div class="col col-body">
                                                            <?= '-' ?>
                                                          </div>
                                                        </div>
                                                      -->

                                                    </div>
                                                  </div>
                                                </div>

                                                <div class="tab-pane fade text-left text-light" id="debitNote<?= $row['vendor_id'] ?>" role="tabpanel" aria-labelledby="debitNote-tab">
                                                  <h3>Debit Note</h3>


                                                  <div class="card bg-transparent">


                                                    <div class="card-body p-0">

                                                      <table>
                                                        <thead>
                                                          <tr>
                                                            <th>Debit Note Number</th>
                                                            <th>Party Code</th>
                                                            <th>Party Name</th>
                                                            <th>Invoice Number</th>
                                                            <th>Total</th>
                                                            <th>Posting Date</th>
                                                          </tr>
                                                        </thead>



                                                        <tbody class="debitnotetable">
                                                        </tbody>

                                                      </table>
                                                    </div>
                                                  </div>
                                                </div>



                                                <div class="tab-pane fade text-left text-light" id="creditNote<?= $row['vendor_id'] ?>" role="tabpanel" aria-labelledby="creditNote-tab">
                                                  <h3>Credit Note</h3>


                                                  <div class="card bg-transparent">


                                                    <div class="card-body p-0">

                                                      <table>
                                                        <thead>
                                                          <tr>
                                                            <th>Credit Note Number</th>
                                                            <th>Party Code</th>
                                                            <th>Party Name</th>
                                                            <th>Invoice Number</th>
                                                            <th>Total</th>
                                                            <th>Posting Date</th>
                                                          </tr>
                                                        </thead>



                                                        <tbody class="creditnotetable">
                                                        </tbody>

                                                      </table>
                                                    </div>
                                                  </div>
                                                </div>

                                              </div>
                                              <!--tab content end-->
                                            </div>
                                          </div>
                                        </div>
                                        <div class="tab-pane fade mail-tab" id="mail<?= $row['vendor_id'] ?>" role="tabpanel" aria-labelledby="mail-tab">

                                          <div class="card mb-2">
                                            <div class="card-body">
                                              <div class="left-details">
                                                <div class="icon">
                                                  <i class="fa fa-user icon-font"></i>
                                                </div>
                                                <div class="text">
                                                  <p class="font-bold">To: xxxxxxx@xxxxxx.xx</p>
                                                  <p>xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</p>
                                                </div>
                                              </div>
                                              <div class="right-details">
                                                <div class="date-time-details">
                                                  <p>xx/xx/xxxx, xx:xxxx</p>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="card mb-2">
                                            <div class="card-body">
                                              <div class="left-details">
                                                <div class="icon">
                                                  <i class="fa fa-user icon-font"></i>
                                                </div>
                                                <div class="text">
                                                  <p class="font-bold">To: xxxxxxx@xxxxxx.xx</p>
                                                  <p>xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</p>
                                                </div>
                                              </div>
                                              <div class="right-details">
                                                <div class="date-time-details">
                                                  <p>xx/xx/xxxx, xx:xxxx</p>
                                                </div>
                                              </div>
                                            </div>
                                          </div>


                                        </div>

                                        <div class="tab-pane fade" id="statement<?= $row['vendor_id'] ?>" role="tabpanel" aria-labelledby="statement-tab">
                                          <div class="statement-section" id="statementPrint">
                                            <div class="row state-date-action d-flex justify-content-between justify-content-md-between">
                                              <div class="col-lg-4 col-md-4 col-sm-6 select-year noprint">
                                                <select name="" id="dateDrop" class="form-control dateDrop dateDrop_<?= $row['vendor_code']  ?>">
                                                  <option>Select Date</option>

                                                  <option value="1" data-val="<?= $row['vendor_code'] ?>">This Month</option>

                                                  <option value="CustomSlide" data-attr="<?= $row['vendor_code'] ?>">
                                                    Custom
                                                  </option>
                                                </select>
                                                <div id="customDateDiv" class="custom-date-div customDateDiv_<?= $row['vendor_code'] ?>">
                                                  <div class="date-field">
                                                    <div class="form-input d-flex gap-2">
                                                      <label for="">From Date</label>
                                                      <input type="date" class="form-control from_<?= $row['vendor_code'] ?>">
                                                      <label for="">To Date</label>
                                                      <input type="date" class="form-control to_<?= $row['vendor_code'] ?>">
                                                      <button type="button" class="btn btn-primary date_apply" data-attr="<?= $row['vendor_code'] ?>">Apply</button>
                                                      <button type="button" class="close-btn close-btn_<?= $row['vendor_code'] ?>">x</button>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="col-lg-8 col-md-8 col-sm-6 actions-btn">
                                                <div class="btns">
                                                  <!-- <button type="button" class="btn btn-primary print-btn" onclick="printContent()">
                                                       <ion-icon name="print-outline"></ion-icon>
                                                       Print
                                                     </button>
                                                     <button type="button" class="btn btn-primary print-btn" id="pdfButton">
                                                       <ion-icon name="document-outline"></ion-icon>
                                                       PDF
                                                     </button> -->
                                                  <button type="button" class="btn btn-primary print-btn" onclick="exportToExcelStatement()">
                                                    <ion-icon name="document-text-outline"></ion-icon>
                                                    Excel
                                                  </button>
                                                  <!-- <button type="button" class="btn btn-primary print-btn">
                                                       <ion-icon name="send-outline"></ion-icon>
                                                       Send Email
                                                     </button> -->
                                                </div>
                                              </div>
                                            </div>
                                            <div id="non-printable-content" class="stateTable_<?= $row['vendor_code'] ?>">

                                            </div>
                                            <div id="printable-content">
                                              <table class="table defaultDataTable table-nowrap" id="printable-content">
                                                <tbody>
                                                  <tr>
                                                    <td><img src="../../public/assets/img/logo/vitwo-logo.png" alt="Logo-Company" style="max-width: 150px;"></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td colspan="3">
                                                      <p class="text-lg mb-0 text-bold">ABC Pvt. Ltd.</p>
                                                      <p class="text-lg mb-0">AIC DSU Innovation Foundation</p>
                                                      <p class="text-lg mb-0">Kolkata</p>
                                                      <p class="text-lg mb-0">Kolkata 111111 India</p>
                                                      <p class="text-lg mb-0">+91 1234567897</p>
                                                      <p class="text-lg mb-0">test@test.com</p>
                                                      <p class="text-lg mb-0">www.test.com</p>
                                                      <p class="text-lg mb-0">GSTIN: 1111111111</p>
                                                    </td>
                                                  </tr>
                                                </tbody>
                                                <tbody>
                                                  <tr>
                                                    <td>
                                                      <p class="text-lg text-bold mb-0">To,</p>
                                                      <p class="text-lg text-bold mb-0">AIC DSU Innovation Foundation</p>
                                                      <p class="text-lg mb-0">Kolkata</p>
                                                      <p class="text-lg mb-0">Kolkata 111111 India</p>
                                                      <p class="text-lg mb-0">+91 1234567897</p>
                                                      <p class="text-lg mb-0">test@test.com</p>
                                                      <p class="text-lg mb-0">www.test.com</p>
                                                      <p class="text-lg mb-0">GSTIN: 1111111111</p>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td colspan="3">
                                                      <p class="text-xl text-bold border-bottom mb-0">STATEMENT OF ACCOUNTS</p>
                                                      <p class="text-lg border-bottom mb-1 mt-1 text-right">01/04/2023 To 31/03/2024</p>
                                                      <p class="text-lg text-bold bg-grey border-0 text-center mb-2 mt-1" style="background-color: #ccc;">Account Summary</p>
                                                      <table width="100%">
                                                        <tr>
                                                          <td class="border-0">
                                                            <p class="text-lg mb-1">Opening Balance</p>
                                                          </td>
                                                          <td class="text-right border-0" style="text-align: right;">
                                                            <p class="text-lg mb-1">Rs 43,365.28</p>
                                                          </td>
                                                        </tr>
                                                        <tr>
                                                          <td class="border-0">
                                                            <p class="text-lg mb-1">Billed Amount</p>
                                                          </td>
                                                          <td class="text-right border-0">
                                                            <p class="text-lg mb-1">Rs 43,365.28</p>
                                                          </td>
                                                        </tr>
                                                        <tr>
                                                          <td class="border-0">
                                                            <p class="text-lg mb-1">Amount paid</p>
                                                          </td>
                                                          <td class="text-right border-0">
                                                            <p class="text-lg mb-1">Rs 43,365.28</p>
                                                          </td>
                                                        </tr>
                                                        <tr>
                                                          <td class="border-0">
                                                            <p class="text-lg mb-1">Balance Due</p>
                                                          </td>
                                                          <td class="text-right border-0">
                                                            <p class="text-lg mb-1">Rs 43,365.28</p>
                                                          </td>
                                                        </tr>
                                                      </table>
                                                    </td>
                                                  </tr>
                                                </tbody>
                                                <tbody class="pt-3">
                                                  <tr>
                                                    <th class="text-lg">Date</th>
                                                    <th class="text-lg">Transaction</th>
                                                    <th class="text-lg">Details</th>
                                                    <th class="text-right text-lg">Amount</th>
                                                    <th class="text-right text-lg">Payments</th>
                                                    <th class="text-right text-lg">Balance</th>
                                                  </tr>
                                                  <tr>
                                                    <td class="text-lg">03/06/2023</td>
                                                    <td class="text-lg">Bill</td>
                                                    <td class="text-lg">FY2023-24-00045 - due on 23/05/2023</td>
                                                    <td class="text-right text-lg">43,365.25</td>
                                                    <td class="text-right text-lg"></td>
                                                    <td class="text-right text-lg">43,675.00</td>
                                                  </tr>
                                                  <tr>
                                                    <td class="text-lg">03/06/2023</td>
                                                    <td class="text-lg">Bill</td>
                                                    <td class="text-lg">FY2023-24-00045 - due on 23/05/2023</td>
                                                    <td class="text-right text-lg"></td>
                                                    <td class="text-right text-lg">-43,365.25</td>
                                                    <td class="text-right text-lg">0</td>
                                                  </tr>
                                                  <tr>
                                                    <td class="text-lg"></td>
                                                    <td class="text-lg"></td>
                                                    <td class="text-lg"></td>
                                                    <td colspan="2" class="text-right text-lg text-bold">Balance Due</td>
                                                    <td class="text-right text-lg text-bold">-43,365.25</td>
                                                  </tr>
                                                </tbody>
                                              </table>
                                            </div>
                                          </div>
                                          <!-- <script>
                                            var val = <?php echo 2; ?>
                                            var vendor_code = <?php echo $row['vendor_code']; ?>
                                            $.ajax({

                                              url: `ajaxs/vendor/ajax-statement.php?type=date&date=${val}&vendor_code=${vendor_code}`,
                                              type: 'get',
                                              beforeSend: function() {
                                                $("#stateTable").html(`Loading...`);
                                              },
                                              success: function(response) {
                                                console.log(response);
                                                $("#stateTable").html(response);

                                              }

                                            });
                                          </script> -->
                                        </div>


                                        <div class="tab-pane fade text-left text-light" id="reconciliation<?= $row['vendor_code'] ?>" role="tabpanel" aria-labelledby="reconciliation-tab">
                                          <div class="customrange-section">

                                            <form action="" class="date_<?= $row['vendor_code'] ?> custom-Range" id="date">
                                              <div class="date-range-input d-flex gap-2">
                                                <div class="form-input">
                                                  <label class="mb-0 text-black" for="">From</label>
                                                  <input type="date" class="form-control from_recon_<?= $row['vendor_code'] ?>">
                                                </div>
                                                <div class="form-input">
                                                  <label class="mb-0 text-black" for="">To</label>
                                                  <input type="date" class="form-control to_recon_<?= $row['vendor_code'] ?>">
                                                </div>
                                              </div>
                                              <button type="button" class="btn btn-primary date_apply_recon" data-attr="<?= $row['vendor_code'] ?>">Apply</button>
                                            </form>
                                          </div>
                                          <div class="card bg-transparent">
                                            <div class="card-body p-0">


                                              <!-- <div id="date" class="date_<?= $row['vendor_code'] ?>">
                                                <div class="date-field">
                                                  <div class="form-input d-flex gap-2">
                                                    <label for="">From Date</label>
                                                    <input type="date" class="form-control from_recon_<?= $row['vendor_code'] ?>">
                                                    <label for="">To Date</label>
                                                    <input type="date" class="form-control to_recon_<?= $row['vendor_code'] ?>">
                                                    <button type="button" class="btn btn-primary date_apply_recon" data-attr="<?= $row['vendor_code'] ?>">Apply</button>
                                                  </div>
                                                </div>
                                              </div> -->


                                              <div id="recon_preview" class="recon_preview_<?= $row['vendor_code'] ?>">





                                              </div>


                                              <div id="recon_preview" class="recon_preview_first_<?= $row['vendor_code'] ?>">





                                              </div>




                                            </div>



                                          </div>

                                          <p class="recon-note text-sm text-black">Note: All values are in <strong>INR</strong></p>


                                        </div>


                                        <div class="tab-pane fade" id="compliance<?= $row['vendor_gstin'] ?>" role="tabpanel" aria-labelledby="compliance-tab">

                                          <div class="card mb-0">
                                            <div class="card-header p-0 rounded mb-2">
                                              <div class="head p-2">
                                                <h4>
                                                  <ion-icon name="document-text-outline"></ion-icon>&nbsp; GST Filed Status For GSTR1
                                                </h4>
                                              </div>
                                            </div>
                                            <?php
                                            if ($row['vendor_gstin'] != "") {
                                            ?>
                                              <div class="card-body">
                                                <div class="row">


                                                  <!-- <div class="col-lg-3 col-md-3 col-sm-6 mb-2">
                                                <span class="text-xs font-bold">Last Update&nbsp;</span>
                                                <p id="mdl_gstin_last_update_comp_span<?= $row['vendor_gstin'] ?>" class="text-xs">XX/XX/XXXX</p>
                                              </div> -->

                                                </div>
                                                <div class="row">
                                                  <div class="col-lg-12 col-md-12 col-sm-12 gst-return-data" id="gstinReturnsDatacomp_Div<?= $row['vendor_gstin'] ?>">




                                                  </div>
                                                </div>
                                              </div>
                                            <?php
                                            } else {
                                              echo "No GSTIN FOUND";
                                            }
                                            ?>
                                          </div>

                                          <div class="card mb-0">
                                            <div class="card-header p-0 rounded mb-2">
                                              <div class="head p-2">
                                                <h4>
                                                  <ion-icon name="document-text-outline"></ion-icon>&nbsp; GST Filed Status For GSTR3B
                                                </h4>
                                              </div>
                                            </div>
                                            <?php
                                            if ($row['vendor_gstin'] != "") {
                                            ?>
                                              <div class="card-body">
                                                <div class="row">


                                                  <!-- <div class="col-lg-3 col-md-3 col-sm-6 mb-2">
                                                <span class="text-xs font-bold">Last Update&nbsp;</span>
                                                <p id="mdl_gstin_last_update_comp_span<?= $row['vendor_gstin'] ?>" class="text-xs">XX/XX/XXXX</p>
                                              </div> -->

                                                </div>
                                                <div class="row">
                                                  <div class="col-lg-12 col-md-12 col-sm-12 gst-return-data" id="gstinReturnsDatacomp3b_Div<?= $row['vendor_gstin'] ?>">




                                                  </div>
                                                </div>
                                              </div>
                                            <?php
                                            } else {
                                              echo "No GSTIN FOUND";
                                            }
                                            ?>
                                          </div>


                                        </div>

                                        <!-- -------------------Audit History Tab Body Start------------------------- -->
                                        <div class="tab-pane fade" id="history<?= $row['vendor_id'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                          <div class="audit-head-section mb-3 mt-3 ">
                                            <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($row['vendor_created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['vendor_created_at']) ?></p>
                                            <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($row['vendor_updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['vendor_updated_at']) ?></p>
                                          </div>
                                          <hr>
                                          <div class="audit-body-section mt-2 mb-3 auditTrailBodyContentVendor<?= $row['vendor_code'] ?>">

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




                                      <!-- right modal end here  -->
                            </td>
                          </tr>




                        <?php } ?>







                      </tbody>
                      <tbody>
                        <tr>
                          <td colspan="15">
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
              <?= $vendorModalHtml ?>
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
                          <table class="row-selection">
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                Vendor Code</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                Trade Name</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                Vendor PAN</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                Vendor TAN</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                GSTIN</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="6" />
                                Email</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="7" />
                                Phone</td>
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
require_once("../common/footer.php");
?>

<script>
  function exportToExcelStatement() {
    // Select the table element containing the ledger report
    var table = document.querySelector('.statement-table');

    // Convert the table to a workbook
    var wb = XLSX.utils.table_to_book(table, {
      sheet: "Statement Report"
    });

    // Save the workbook as an Excel file
    XLSX.writeFile(wb, 'Statement_Report.xlsx');
  }

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




  $('.hamburger').click(function() {
    $('.hamburger').toggleClass('show');
    $('#overlay').toggleClass('show');
    $('.nav-action').toggleClass('show');
    $('.blur-body .tab-content').toggleClass('blur');
  });


  //********************************************************************************************************** */

  var BASE_URL = `<?= BASE_URL ?>`;
  var BRANCH_URL = `<?= BRANCH_URL ?>`;

  function check_account(ifsc, acc) {
    //  alert(1);
    // console.log(ifsc);
    // console.log(acc);
    $.ajax({
      url: `ajaxs/vendor/ajax-account.php`,
      type: 'POST',
      data: {
        ifsc: ifsc,
        acc: acc

      },
      beforeSend: function() {

      },
      success: function(response) {
        //alert(response);
        console.log(response);
        if (response > 0) {

          $('#bank_detail_error').html('bank account already exists');

          document.getElementById("next_last").disabled = true;

        } else {
          $('#bank_detail_error').html(``);

          document.getElementById("next_last").disabled = false;
        }
      }

    });
  }

  function check_account_edit(ifsc, acc, vendor_id) {
    // alert(vendor_id);
    // console.log(ifsc);
    // console.log(acc);
    $.ajax({
      url: `ajaxs/vendor/ajax-account-edit.php`,
      type: 'POST',
      data: {
        ifsc: ifsc,
        acc: acc,
        vendor_id: vendor_id

      },
      beforeSend: function() {

      },
      success: function(response) {
        // alert(response);
        // console.log(response);
        if (response > 0) {

          $('#bank_detail_error').html('bank account already exists');

          document.getElementById("next_last").disabled = true;

        } else {
          $('#bank_detail_error').html(``);

          document.getElementById("next_last").disabled = false;
        }
      }

    });
  }


  $(document).ready(function() {


    $(document).on("keyup blur", "#vendor_bank_ifsc", function() {
      // echo 1;
      let ifsc = $(this).val();
      let acc = $('.account_number').val();

      // alert(acc);

      let vendor_id = $("#vendor_id_edit").val();
      if (vendor_id != '') {
        check_account_edit(ifsc, acc, vendor_id);
      } else {
        check_account(ifsc, acc);
      }
      $.ajax({
        url: `https://ifsc.razorpay.com/${ifsc}`,
        method: "GET",
        success: function(response) {
          $(".IFSClass").addClass(`border border-success`);
          $(".tick-icon").text(`✅`);
          $(".IFSClass").removeClass(`border-danger`);
          $("#ifscCodeMsg").html(`<span class="text-success">ifsc code is valid!</span>`);

          $("#vendor_bank_address").val(response.ADDRESS);
          $("#vendor_bank_name").val(response.BANK);
          $("#vendor_bank_branch").val(response.BRANCH);
        },
        error: function(xhr, status, error) {
          $(".IFSClass").addClass(`border border-danger`);
          $(".tick-icon").text(`❌`);
          $(".IFSClass").removeClass(`border-success`);
          $("#ifscCodeMsg").html(`<span class="text-danger">ifsc code is not valid!</span>`);

          $("#vendor_bank_address").val('');
          $("#vendor_bank_name").val('');
          $("#vendor_bank_branch").val('');
        }
      });


    });

    $(document).on("change", ".countriesDropDownloop", function() {
      let country = $(this).val();
      let htmll = '';
      if (country == 'India') {
        htmll = '<label for="">State</label> <select name="vendorOtherAddress[0][vendor_business_state]" class="form-control secect2 countriesDropDownloop"><?= $stateHtml ?> </select>';
      } else {
        htmll = '<label for="">State</label><input type="text" name="vendorOtherAddress[0][vendor_business_state]" class="form-control" value="">';
      }
      $(this).parent().parent().parent().find('.stateDropDownloop').html(htmll);
    });

    $(document).on("change", ".countriesDropDown", function() {
      let country = $(this).val();
      let htmll = '';
      if (country == 'India') {
        htmll = '<label for="">State</label> <select id="state" name="state" class="form-control secect2 stateDropDown"><?= $stateHtml ?> </select>';
      } else {
        htmll = '<label for="">State</label><input type="text" class="form-control" name="state" id="state" value="<?php echo $gstDetails['pradr']['addr']['stcd']; ?>">';
      }

      $('.stateDivDropDown').html(htmll);
    });


    $(document).on("change", ".countriesDropDown_edit", function() {
      let country = $(this).val();
      let htmll = '';
      if (country == 'India') {
        htmll = '<label for="">State</label> <select id="state" name="state" class="form-control secect2 stateDropDown"><?= $stateHtml ?> </select>';
      } else {
        htmll = '<label for="">State</label><input type="text" class="form-control" name="state" id="state" value="">';
      }

      $('.stateDivDropDown').html(htmll);
    });



    $(document).on("change", "#isGstRegisteredCheckBoxBtn", function() {
      let isChecked = $(this).is(':checked');
      if (isChecked) {
        $("#vendorGstNoInput").attr("readonly", "readonly");
        $("#vendorPanNo").removeAttr("readonly");

        $.ajax({
          type: "GET",
          url: `ajaxs/ajax-vendor-with-out-verify-gstin.php`,
          beforeSend: function() {
            $('.checkAndVerifyGstinBtn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Loading...');
            $(".checkAndVerifyGstinBtn").toggleClass("disabled");
          },
          success: function(response) {
            $(".checkAndVerifyGstinBtn").toggleClass("disabled");
            // $('.checkAndVerifyGstinBtn').html("Re-Verify");
            responseObj = (response);
            //  $('.checkAndVerifyGstinBtn').html("Re-Verify");
            responseObj = (response);
            //responseObj = JSON.parse(responseObj);
            $("#VerifyGstinBtnDiv").hide();
            $("#multistepform").show();
            $("#multistepform").html(responseObj);
            // console.log(responseObj);
          }
        });

      } else {
        $("#vendorCreateMainForm").html("");
        $("#vendorGstNoInput").removeAttr("readonly");
        $("#vendorPanNo").attr("readonly", "readonly");
      }
      $(".checkAndVerifyGstinBtn").toggleClass("disabled");
    });

    $(".checkAndVerifyGstinBtn").click(function() {
      let vendorGstNo = $("#vendorGstNoInput").val();
      if (vendorGstNo != "") {
        $.ajax({
          type: "GET",
          url: `ajaxs/ajax-vendor-verify-gstin.php?gstin=${vendorGstNo}`,
          beforeSend: function() {
            $('.checkAndVerifyGstinBtn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
            $(".checkAndVerifyGstinBtn").toggleClass("disabled");
          },
          success: function(response) {
            $(".checkAndVerifyGstinBtn").toggleClass("disabled");
            //  $('.checkAndVerifyGstinBtn').html("Re-Verify");
            responseObj = (response);
            //responseObj = JSON.parse(responseObj);
            $("#VerifyGstinBtnDiv").hide();
            $("#multistepform").show();
            $("#multistepform").html(responseObj);
            //console.log(responseObj);
            load_js();
          }
        });
      } else {
        let Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        });
        Toast.fire({
          icon: `warning`,
          title: `&nbsp;Please provide GSTIN No!`
        });
      }
    });


    $(document).on("click", ".deleteOtherAddressBtns", function() {
      let deleteAddNo = ($(this).attr("id")).split("_")[1];
      $(`#otherAddressItem_${deleteAddNo}`).remove();
    });

    let otherAddressItemCounter = 1;
    $(document).on("click", ".addNewOtherAddress", function() {
      otherAddressItemCounter += 1;
      let formHtml = `
                                                <div id="otherAddressItem_${otherAddressItemCounter}">
                                                    <div class="row m-0 p-2 bg-secondary">
                                                        <!-- <div class="h5 text-bold ml-1">1. Address</div> -->
                                                        <div class="ml-auto mr-2">
                                                            <span class="btn btn-warning btn-sm text-light deleteOtherAddressBtns" id="deleteOtherAddressBtn_${otherAddressItemCounter}">Delete</span>
                                                            <span class="btn btn-success btn-sm addNewOtherAddress">Add New</span>
                                                        </div>
                                                    </div>
                                                    <div class="row m-0 p-0">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="text-muted">GST Legal Name</label>
                                                                <input type="text" class="form-control" placeholder="GST Legal Name" name="vendorBranchGstLegalName[]" required>
                                                            </div>

                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="text-muted">GST Trade Name</label>
                                                                <input type="text" class="form-control" placeholder="GST Trade Name" name="vendorBranchGstTradeName[]" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row m-0 p-0">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="text-muted">Constitution of Business</label>
                                                                <input type="text" class="form-control" placeholder="GST Legal Name" name="vendorBranchConstitutionBusiness[]" required>
                                                            </div>

                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="text-muted">Building Number</label>
                                                                <input type="text" class="form-control" placeholder="Building Number" name="vendorBranchBuildingNumber[]" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row m-0 p-0">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="text-muted">Flat Number</label>
                                                                <input type="text" class="form-control" placeholder="Flat Number" name="vendorBranchFlatNumber[]" required>
                                                            </div>

                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="text-muted">Street Name</label>
                                                                <input type="text" class="form-control" placeholder="Street Name" name="vendorBranchStreetName[]" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row m-0 p-0">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="text-muted">Pin Code</label>
                                                                <input type="text" class="form-control" placeholder="Pin Code" name="vendorBranchPinCode[]" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="text-muted">Location</label>
                                                                <input type="text" class="form-control" placeholder="Location" name="vendorBranchLocation[]" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row m-0 p-0">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="text-muted">City</label>
                                                                <input type="text" class="form-control" placeholder="City" name="vendorBranchCity[]" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="text-muted">District</label>
                                                                <input type="text" class="form-control" placeholder="District" name="vendorBranchDistrict[]" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row m-0 p-0">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="text-muted">State</label>
                                                                <input type="text" class="form-control" placeholder="State" name="vendorBranchState[]" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>`;
      $("#otherAddressesListDiv").append(formHtml);
    });

  });

  $(document).ready(function() {
    $(document).on('change', '.vendor_bank_cancelled_cheque', function() {
      var file_data = $('.vendor_bank_cancelled_cheque').prop('files')[0];
      let vendor_id = $("#vendor_id_edit").val();
      var form_data = new FormData();
      form_data.append('file', file_data);
      // alert(form_data);
      $.ajax({
        url: 'ajaxs/ajax_cancelled_cheque_upload.php', // <-- point to server-side PHP script 
        dataType: 'text', // <-- what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() {
          $('.Ckecked_loder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          $(".Ckecked_loder").toggleClass("disabled");
        },
        success: function(responseData) {

          $('.Ckecked_loder').html('<i class="fa fa-upload"></i>');
          $(".Ckecked_loder").toggleClass("enabled");
          responseObj = JSON.parse(responseData);
          console.log(responseObj);

          $("#vendor_bank_ifsc").val(responseObj["payload"]["cheque_details"]["ifsc"]["value"]);
          $("#account_number").val(responseObj["payload"]["cheque_details"]["acc no"]["value"]);
          $("#account_holder").val(responseObj["payload"]["cheque_details"]["acc holder"]["value"]);

          $("#vendor_bank_address").val(responseObj["payload"]["bank_details"]["ADDRESS"]);
          $("#vendor_bank_name").val(responseObj["payload"]["bank_details"]["BANK"]);
          $("#vendor_bank_branch").val(responseObj["payload"]["bank_details"]["BRANCH"]);
          var ifsc = responseObj["payload"]["cheque_details"]["ifsc"]["value"];
          var acc = responseObj["payload"]["cheque_details"]["acc no"]["value"];

          if (vendor_id != '') {
            check_account_edit(ifsc, acc, vendor_id);
          } else {
            check_account(ifsc, acc);
          }

        }
      });
    });


    $(document).on('click', '.vendor_bank_cancelled_cheque_btn', function() {
      var file_data = $('#invoiceFileInput').prop('files')[0];
      console.log(file_data);
      var form_data = new FormData();
      let vendor_id = $("#vendor_id_edit").val();
      form_data.append('file', file_data);
      // alert(form_data);
      $.ajax({
        url: 'ajaxs/ajax_cancelled_cheque_upload.php', // <-- point to server-side PHP script 
        dataType: 'text', // <-- what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() {
          $('.vendor_bank_cancelled_cheque_btn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          $(".vendor_bank_cancelled_cheque_btn").toggleClass("disabled");
        },
        success: function(responseData) {
          responseObj = JSON.parse(responseData);

          $("#vendor_bank_ifsc").val(responseObj["payload"]["cheque_details"]["ifsc"]["value"]);
          $("#account_number").val(responseObj["payload"]["cheque_details"]["acc no"]["value"]);
          $("#account_holder").val(responseObj["payload"]["cheque_details"]["acc holder"]["value"]);

          $("#vendor_bank_address").val(responseObj["payload"]["bank_details"]["ADDRESS"]);
          $("#vendor_bank_name").val(responseObj["payload"]["bank_details"]["BANK"]);
          $("#vendor_bank_branch").val(responseObj["payload"]["bank_details"]["BRANCH"]);
          $('.vendor_bank_cancelled_cheque_btn').html('Upload');
          $(".vendor_bank_cancelled_cheque_btn").toggleClass("enabled");
          $('#checkUpload').css('display', 'none');
          var ifsc = responseObj["payload"]["cheque_details"]["ifsc"]["value"];
          var acc = responseObj["payload"]["cheque_details"]["acc no"]["value"];

          if (vendor_id != '') {
            check_account_edit(ifsc, acc, vendor_id);
          } else {
            check_account(ifsc, acc);
          }

        }
      });
    });

    $(document).on('click', '.visiting_card_btn', function() {
      var file_data = $('#visitingFileInput').prop('files')[0];
      var form_data = new FormData();
      form_data.append('file', file_data);
      // alert(form_data);
      $.ajax({
        url: 'ajaxs/ajax_visiting_card.php', // <-- point to server-side PHP script 
        dataType: 'text', // <-- what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() {
          $('.visiting_card_btn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          $(".visiting_card_btn").toggleClass("disabled");
        },
        success: function(responseData) {
          $('.visiting_card_btn').html('Submit');
          $(".visiting_card_btn").toggleClass("enabled");
          $("#visitingCard").css({
            "display": "none"
          });
          responseObj = JSON.parse(responseData);
          // console.log(responseObj);
          // $("#adminName").val(responseObj["payload"]["ContactNames"]["value"]['0']["content"]);
          // $("#vendor_authorised_person_designation").val('');
          // $("#adminPhone").val(responseObj["payload"]["WorkPhones"]["value"]['0']['value']);
          // $("#vendor_authorised_person_phone").val(responseObj["payload"]["WorkPhones"]["value"]['1']['value']);
          // $("#adminEmail").val(responseObj["payload"]["Emails"]["value"]['0']["content"]);
          // $("#vendor_authorised_person_email").val(responseObj["payload"]["Emails"]["value"]['1']["content"]);

          $("#adminName").val(responseObj["payload"]["ContactNames"]["value"][0]["content"]);
          $("#adminEmail").val(responseObj["payload"]["Emails"]["value"][0]["content"]);
          $("#adminPhone").val(responseObj["payload"]["WorkPhones"]["value"][0]['value']);
          $("#vendor_authorised_person_phone").val(responseObj["payload"]["WorkPhones"]["value"][0]['value']);
          $("#vendor_authorised_person_email").val(responseObj["payload"]["Emails"]["value"][0]["content"]);
          let designationArr = [];
          let jobTitle = responseObj["payload"]["JobTitles"]["value"][0]["content"] ?? "";
          let departments = responseObj["payload"]["Departments"]["value"][0]["content"] ?? "";
          if (jobTitle != "") {
            designationArr.push(jobTitle);
          }
          if (departments != "") {
            designationArr.push(departments);
          }
          $("#vendor_authorised_person_designation").val(designationArr.join(", "));
        }
      });
    });

    $(document).on('change', '.visiting_card', function() {
      var file_data = $('.visiting_card').prop('files')[0];
      var form_data = new FormData();
      form_data.append('file', file_data);
      // alert(form_data);
      $.ajax({
        url: 'ajaxs/ajax_visiting_card.php', // <-- point to server-side PHP script 
        dataType: 'text', // <-- what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() {
          $('.visiting_loder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          $(".visiting_loder").toggleClass("disabled");
        },
        success: function(responseData) {
          $('.visiting_loder').html('<i class="fa fa-upload"></i>');
          $(".visiting_loder").toggleClass("enabled");
          responseObj = JSON.parse(responseData);
          // console.log(responseObj);
          // $("#adminName").val(responseObj["payload"]["ContactNames"]["value"]['0']["content"]);
          // $("#vendor_authorised_person_designation").val('');

          // $("#adminPhone").val(responseObj["payload"]["WorkPhones"]["value"]['0']['value']);
          // $("#vendor_authorised_person_phone").val(responseObj["payload"]["WorkPhones"]["value"]['1']['value']);

          // $("#adminEmail").val(responseObj["payload"]["Emails"]["value"]['0']["content"]);
          // $("#vendor_authorised_person_email").val(responseObj["payload"]["Emails"]["value"]['1']["content"]);

          $("#adminName").val(responseObj["payload"]["ContactNames"]["value"][0]["content"]);
          $("#adminEmail").val(responseObj["payload"]["Emails"]["value"][0]["content"]);
          $("#adminPhone").val(responseObj["payload"]["WorkPhones"]["value"][0]['value']);
          $("#vendor_authorised_person_phone").val(responseObj["payload"]["WorkPhones"]["value"][0]['value']);
          $("#vendor_authorised_person_email").val(responseObj["payload"]["Emails"]["value"][0]["content"]);
          let designationArr = [];
          let jobTitle = responseObj["payload"]["JobTitles"]["value"][0]["content"] ?? "";
          let departments = responseObj["payload"]["Departments"]["value"][0]["content"] ?? "";
          if (jobTitle != "") {
            designationArr.push(jobTitle);
          }
          if (departments != "") {
            designationArr.push(departments);
          }
          $("#vendor_authorised_person_designation").val(designationArr.join(", "));

        }
      });
    });


    $(document).on("click", ".addCF", function() {
      let addressRandNo = Math.ceil(Math.random() * 100000);
      $("#customFields").append(`<div class="row">
          <div class="col-md-12 mt-5 mb-2"><a href="javascript:void(0);" class="remCF btn btn-danger float-right"><i class="fa fa-minus"></i></a></div>
          <div class="col-md-6">
              <div class="form-input">
              <label>Flat Number</label>    
              <input type="text" name="vendorOtherAddress[${addressRandNo}]['vendor_business_flat_no']" class="form-control"
                      id="vendor_business_flat_no"/>
                  
              </div>
              <div class="form-input">
              <label>Pin Code</label>    
              <input type="text" name="vendorOtherAddress[${addressRandNo}]['vendor_business_pin_code']" class="form-control"
                      id="vendor_business_pin_code"/>
                  
              </div>
              <div class="form-input">
              <label>District</label>    
              <input type="text" name="vendorOtherAddress[${addressRandNo}]['vendor_business_district']" class="form-control"
                      id="vendor_business_district"/>
                  
              </div>
              <div class="form-input">
              <label>Location</label>    
              <input type="text" name="vendorOtherAddress[${addressRandNo}]['vendor_business_location']" class="form-control"
                      id="vendor_business_location"/>
                  
              </div>
          </div>
          <div class="col-md-6">
            <div class="form-input">
            <label>Building Number</label>    
            <input type="text" name="vendorOtherAddress[${addressRandNo}]['vendor_business_building_no']"
                    class="form-control" id="vendor_business_building_no"/>
                
            </div>

            <div class="form-input">
            <label>Street Name</label>    
            <input type="text" name="vendorOtherAddress[${addressRandNo}]['vendor_business_street_name']"
                    class="form-control" id="vendor_business_street_name"/>
                
            </div>

            <div class="form-input">
            <label>City</label>    
            <input type="text" name="vendorOtherAddress[${addressRandNo}]['vendor_business_city']" class="form-control"
                    id="vendor_business_city"/>
                
            </div>

            <div class="form-input">
            <label>State</label>    
            <input type="text" name="vendorOtherAddress[${addressRandNo}]['vendor_business_state']" class="form-control"
                    id="vendor_business_state"/>
                
            </div>
          </div>
        </div>`);
    });





    $(document).on("click", '.remCF', function() {
      $(this).parent().parent().remove();
    });

    $(document).on("click", '.updateRemCF', function() {

      let otherAddressId = ($(this).attr("id")).split("_")[1];
      console.log(otherAddressId);

      $.ajax({
        url: 'ajaxs/ajax_other_address.php',
        data: {
          otherAddressId
        },
        type: 'POST',
        beforeSend: function() {
          // $('.vendor_bank_cancelled_cheque').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          //$(".vendor_bank_cancelled_cheque").toggleClass("disabled");
        },
        success: function(responseData) {
          // responseObj = JSON.parse(responseData);
          console.log(responseData);
          $(".removeID").html(responseData);
        }
      });


      $(this).parent().parent().remove();
    });


    $(document).on("click", "#addOtherAddressBtn", function() {
      let vendor_idd = $('#vendor_idd').val();
      let vendor_business_flat_no_add = $('#vendor_business_flat_no_add').val();
      let vendor_business_pin_code_add = $('#vendor_business_pin_code_add').val();
      let vendor_business_district_add = $('#vendor_business_district_add').val();
      let vendor_business_location_add = $('#vendor_business_location_add').val();
      let vendor_business_building_no_add = $('#vendor_business_building_no_add').val();
      let vendor_business_street_name_add = $('#vendor_business_street_name_add').val();
      let vendor_business_city_add = $('#vendor_business_city_add').val();
      let vendor_business_state_add = $('#vendor_business_state_add').val();

      $.ajax({
        url: 'ajaxs/ajax_other_address_add.php',
        data: {
          vendor_id: vendor_idd,
          flatNo: vendor_business_flat_no_add,
          pinCode: vendor_business_pin_code_add,
          district: vendor_business_district_add,
          location: vendor_business_location_add,
          buildingNo: vendor_business_building_no_add,
          streetName: vendor_business_street_name_add,
          city: vendor_business_city_add,
          state: vendor_business_state_add
        },
        type: 'POST',
        beforeSend: function() {
          // $('.vendor_bank_cancelled_cheque').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          //$(".vendor_bank_cancelled_cheque").toggleClass("disabled");
        },
        success: function(responseData) {
          // responseObj = JSON.parse(responseData);
          // console.log(responseData);
          $(".insertOtherAddress").html(responseData);
          $(".otherAddressAddModal").modal('hide');
          // $("#addOtherForm").reset();
          $('#vendor_idd').val('');
          $('#vendor_business_flat_no_add').val('');
          $('#vendor_business_pin_code_add').val('');
          $('#vendor_business_district_add').val('');
          $('#vendor_business_location_add').val('');
          $('#vendor_business_building_no_add').val('');
          $('#vendor_business_street_name_add').val('');
          $('#vendor_business_city_add').val('');
          $('#vendor_business_state_add').val('');
        }
      });

    });

    $(document).on("click", ".add_data", function() {
      var data = this.value;
      $("#createdata").val(data);
      // confirm('Are you sure to Submit?')
      $("#add_frm").submit();
    });

    $(document).on("click", ".edit_data", function() {
      var data = this.value;
      $("#editData").val(data);
      // confirm('Are you sure to Submit?')
      $("#edit_frm").submit();
    });

    // $(".edit_data").click(function() {
    //    var data = this.value;
    //    $("#editData").val(data);
    //    //confirm('Are you sure to Submit?')
    //    $("#edit_frm").submit();
    //  });

    // $(document).on("click", ".js-btn-next", function() {
    //   console.log("hi there!");
    // });


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


  });

  // datatable
  // $('#mytable2').DataTable({
  //   "paging": false,
  //   "searching": false,
  //   "ordering": true,
  // });

  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  };

  window.onscroll = function() {
    myFunction()
  };

  var navbar = document.getElementById("action-navbar");
  var sticky = action - navbar.offsetTop;

  function myFunction() {
    if (window.pageYOffset >= sticky) {
      action - navbar.classList.add("sticky")
    } else {
      action - navbar.classList.remove("sticky");
    }
  };
</script>
<script>
  const targetDiv = document.getElementById("third");
  const btn = document.getElementById("toggle");
  btn.onclick = function() {
    if (targetDiv.style.display !== "none") {
      targetDiv.style.display = "none";
    } else {
      targetDiv.style.display = "block";
    }
  };
</script>
<script>
  // function toggle(elem) {
  //   sec = elem.parentElement;
  //   if (sec.style.width != '100%') sec.style.width = '100%';
  //   else sec.style.width = 'auto';
  //   coll = sec.getElementsByClassName("collapsible-content")[0];
  //   if (coll.style.height != 'auto') coll.style.height = 'auto';
  //   else coll.style.height = '0px';
  // }
</script>

<script src="<?= BASE_URL; ?>public/validations/vendorValidation.js"></script>

<!-- CHART_ONLY -->

<script>
  $(document).ready(function() {
    for (elem of $(".chartContainer")) {
      let dataAttrValue = elem.getAttribute("id").split("_")[1];
      let id = $(`#fYDropdown_${dataAttrValue}`).val();

      $.ajax({
        type: "GET",
        url: `<?= LOCATION_URL ?>ajaxs/ajax-vendor-chart.php?id=${id}&vend_id=${dataAttrValue}`,
        beforeSend: function() {
          $(".load-wrapp").show();
          $(".load-wrapp").css('opacity', 1);
        },
        success: function(result) {
          $(".load-wrapp").hide();
          $(".load-wrapp").css('opacity', 0);

          let res = jQuery.parseJSON(result);

          salesVsCollection(res, "chartDivSalesVsCollection", dataAttrValue);
        }
      });
    };
  });

  $(document).on("change", '.fYDropdown', function() {

    // function monthWiseChart() {
    var dataAttrValue = $(this).data('attr');
    var id = $(`#fYDropdown_${dataAttrValue}`).val();

    $.ajax({
      type: "GET",
      url: `<?= LOCATION_URL ?>ajaxs/ajax-vendor-chart.php?id=${id}&vend_id=${dataAttrValue}`,
      beforeSend: function() {
        $(".load-wrapp").show();
        $(".load-wrapp").css('opacity', 1);
      },
      success: function(result) {
        $(".load-wrapp").hide();
        $(".load-wrapp").css('opacity', 0);

        let res = jQuery.parseJSON(result);

        salesVsCollection(res, "chartDivSalesVsCollection", dataAttrValue);
      }
    });
    // };

    // monthWiseChart();
  });

  $(document).on("change", '.monthRange', function() {

    // function dayWiseChart() {
    var dataAttrValue = $(this).data('attr');
    var month = $(`#monthRange_${dataAttrValue}`).val();

    $.ajax({
      type: "GET",
      url: `<?= LOCATION_URL ?>ajaxs/ajax-vendor-chart.php?month=${month}&vend_id=${dataAttrValue}`,
      beforeSend: function() {
        $(".load-wrapp").show();
        $(".load-wrapp").css('opacity', 1);
      },
      success: function(result) {
        $(".load-wrapp").hide();
        $(".load-wrapp").css('opacity', 0);

        let res = jQuery.parseJSON(result);

        salesVsCollection(res, "chartDivSalesVsCollection", dataAttrValue);
      }
    });
    // };

    // dayWiseChart();
  });


  // ====================================== Combined bullet/column and line graphs with multiple value axes ======================================

  function salesVsCollection(chartData, chartTitle, vendId) {

    $(`.${chartTitle}_${vendId.trim()}`).text(`Payable Vs Paid`);

    if (chartData.sql_list_all_cust.length == 0 && chartData.sql_list_specific_cust.length == 0) {
      const currentDate = new Date();
      const year = currentDate.getFullYear();
      const month = String(currentDate.getMonth() + 1).padStart(2, '0');
      const day = String(currentDate.getDate()).padStart(2, '0');

      const formattedDate = `${year}-${month}-${day}`;

      chartData = {
        "sql_list_all_cust": [{
          date_: formattedDate,
          total_payable_all: 0,
          total_paid_all: 0
        }],
        "sql_list_specific_cust": [{
          date_: formattedDate,
          total_payable: 0,
          total_paid: 0
        }]
      };
    };

    am4core.ready(function() {

      // Themes begin
      am4core.useTheme(am4themes_animated);
      // Themes end

      // Create chart instance
      var chart = am4core.create(`${chartTitle}_${vendId.trim()}`, am4charts.XYChart);
      chart.logo.disabled = true;

      let finalData = [];
      let outerIndex = 0;

      for (obj of chartData.sql_list_all_cust) {
        obj.total_payable_all = Number(obj.total_payable);
        obj.total_paid_all = Number(obj.total_paid);
        obj.total_payable = 0;
        obj.total_paid = 0;
        finalData.push(obj);
      };

      for (obj of chartData.sql_list_specific_cust) {

        const outerObj = finalData.map(obj => {
          return obj.date_
        })
        outerIndex = outerObj.indexOf(obj.date_)

        if (outerIndex !== -1) {
          finalData[outerIndex].total_payable = Number(obj.total_payable);
          finalData[outerIndex].total_paid = Number(obj.total_paid);
        } else {
          obj.total_payable = Number(obj.total_payable);
          obj.total_paid = Number(obj.total_paid);
          obj.total_payable_all = 0;
          obj.total_paid_all = 0;
          finalData.push(obj);
        }
      }

      finalData.sort((a, b) => (a.date_ > b.date_) ? 1 : ((b.date_ > a.date_) ? -1 : 0))

      // Add data
      chart.data = finalData;

      // Create axes
      var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
      //dateAxis.renderer.grid.template.location = 0;
      //dateAxis.renderer.minGridDistance = 30;

      var valueAxis1 = chart.yAxes.push(new am4charts.ValueAxis());
      valueAxis1.title.text = "This Vendor";

      var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
      valueAxis2.title.text = "All Vendors";
      valueAxis2.renderer.opposite = true;
      valueAxis2.renderer.grid.template.disabled = true;

      // Create series
      var series1 = chart.series.push(new am4charts.ColumnSeries());
      series1.dataFields.valueY = "total_payable";
      series1.dataFields.dateX = "date_";
      series1.yAxis = valueAxis1;
      series1.name = "Payable";
      series1.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";
      series1.fill = chart.colors.getIndex(0);
      series1.strokeWidth = 0;
      series1.clustered = false;
      series1.columns.template.width = am4core.percent(40);

      var series2 = chart.series.push(new am4charts.ColumnSeries());
      series2.dataFields.valueY = "total_paid";
      series2.dataFields.dateX = "date_";
      series2.yAxis = valueAxis1;
      series2.name = "Paid";
      series2.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";
      series2.fill = chart.colors.getIndex(0).lighten(0.5);
      series2.strokeWidth = 0;
      series2.clustered = false;
      series2.toBack();

      var series3 = chart.series.push(new am4charts.LineSeries());
      series3.dataFields.valueY = "total_paid_all";
      series3.dataFields.dateX = "date_";
      series3.name = "Paid (all vendors)";
      series3.strokeWidth = 2;
      series3.tensionX = 0.7;
      series3.yAxis = valueAxis2;
      series3.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";

      var bullet3 = series3.bullets.push(new am4charts.CircleBullet());
      bullet3.circle.radius = 3;
      bullet3.circle.strokeWidth = 2;
      bullet3.circle.fill = am4core.color("#fff");

      var series4 = chart.series.push(new am4charts.LineSeries());
      series4.dataFields.valueY = "total_payable_all";
      series4.dataFields.dateX = "date_";
      series4.name = "Payable (all vendors)";
      series4.strokeWidth = 2;
      series4.tensionX = 0.7;
      series4.yAxis = valueAxis2;
      series4.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";
      series4.stroke = chart.colors.getIndex(0).lighten(0.5);
      series4.strokeDasharray = "3,3";

      var bullet4 = series4.bullets.push(new am4charts.CircleBullet());
      bullet4.circle.radius = 3;
      bullet4.circle.strokeWidth = 2;
      bullet4.circle.fill = am4core.color("#fff");

      // Add cursor
      chart.cursor = new am4charts.XYCursor();

      // Add legend
      chart.legend = new am4charts.Legend();
      chart.legend.position = "top";

      // Add scrollbar
      chart.scrollbarX = new am4charts.XYChartScrollbar();
      chart.scrollbarX.series.push(series1);
      chart.scrollbarX.series.push(series3);
      chart.scrollbarX.parent = chart.bottomAxesContainer;

    });
  };
  // ++++++++++++++++++++++++++++++++++++++ Combined bullet/column and line graphs with multiple value axes ++++++++++++++++++++++++++++++++++++++

  function statement_date(vendor_code) {

    $.ajax({
      url: `ajaxs/customer/ajax-dateRange-statement.php`,
      type: 'POST',
      data: {
        from_date: from_date,
        to_date: to_date,
        vendor_code: vendor_code

      },
      beforeSend: function() {

      },
      success: function(response) {
        console.log(response);
        // alert(response);
        var obj = jQuery.parseJSON(response);
        $('.stateTable_' + attr).html(obj['html']);


      }

    });

  }

  //chart tab 

  $(document).ready(function() {
    for (elem of $(".pieChartContainer")) {
      let dataAttrValue = elem.getAttribute("id").split("_")[1];
      let id = $(`#piefYDropdown_${dataAttrValue}`).val();
      let dataAttrCode = elem.getAttribute("id").split("_")[2];
      $.ajax({
        type: "GET",
        url: `<?= LOCATION_URL ?>ajaxs/ajax-vendor-chart.php?id=${id}&vendor_id=${dataAttrValue}`,
        beforeSend: function() {
          $(".load-wrapp").show();
          $(".load-wrapp").css('opacity', 1);
        },
        success: function(result) {
          $(".load-wrapp").hide();
          $(".load-wrapp").css('opacity', 0);
          let res = jQuery.parseJSON(result);
          pieChart(res, "chartDivPayableAgeing", dataAttrValue);
        }
      });
    };
  });



  $(document).on("change", '.piefYDropdown', function() {
    var dataAttrValue = $(this).data('attr');
    var id = $(`#piefYDropdown_${dataAttrValue}`).val();
    $.ajax({
      type: "GET",
      url: `<?= LOCATION_URL ?>ajaxs/ajax-vendor-chart.php?id=${id}&vendor_id=${dataAttrValue}`,
      beforeSend: function() {
        $(".load-wrapp").show();
        $(".load-wrapp").css('opacity', 1);
      },
      success: function(result) {
        $(".load-wrapp").hide();
        $(".load-wrapp").css('opacity', 0);
        let res = jQuery.parseJSON(result);
        pieChart(res, "chartDivPayableAgeing", dataAttrValue);
      }
    });
  });

  function pieChart(chartData, chartTitle, vendId) {
    am4core.ready(function() {
      // Themes
      am4core.useTheme(am4themes_animated);
      var chart = am4core.create(`${chartTitle}_${vendId.trim()}`, am4charts.PieChart3D);
      chart.responsive.enabled = true;
      chart.logo.disabled = true;
      chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
      chart.legend = new am4charts.Legend();
      let finalData = [{
          "category": "0-30 days",
          "value": 0
        },
        {
          "category": "31-60 days",
          "value": 0
        },
        {
          "category": "61-90 days",
          "value": 0
        },
        {
          "category": "91-180 days",
          "value": 0
        },
        {
          "category": "181-365 days",
          "value": 0
        },
        {
          "category": "More than 365 days",
          "value": 0
        },
      ];

      for (elem of chartData.data) {
        let due_days = parseInt(elem.due_days);
        if (due_days >= 0 && due_days <= 30) {
          finalData[0].value += Number(elem.total_due_amount);
        } else if (due_days >= 31 && due_days <= 60) {
          finalData[1].value += Number(elem.total_due_amount);
        } else if (due_days >= 61 && due_days <= 90) {
          finalData[2].value += Number(elem.total_due_amount);
        } else if (due_days >= 91 && due_days <= 180) {
          finalData[3].value += Number(elem.total_due_amount);
        } else if (due_days >= 181 && due_days <= 365) {
          finalData[4].value += Number(elem.total_due_amount);
        } else {
          finalData[5].value += Number(elem.total_due_amount);
        };
      };

      // Data 
      chart.data = finalData;
      chart.innerRadius = 50;
      var series = chart.series.push(new am4charts.PieSeries3D());
      series.dataFields.value = "value";
      series.dataFields.category = "category";
      series.ticks.template.disabled = true;
      series.labels.template.disabled = true;
      chart.legend.position = "right";
      chart.legend.valign = "middle";
    });
  };
</script>

<script>
  $(document).ready(function() {
    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function(e) {
      var targetTab = $(e.target).attr("href"); // Get the target tab ID or class
      if (targetTab.startsWith("#compliance")) {
        var vendorGst = targetTab.substring("#compliance".length);
        $.ajax({
          url: `ajaxs/vendor/ajax-gst-review.php?gstin=${vendorGst}`,
          type: 'get',
          beforeSend: function() {
            $("#gstinReturnsDatacomp_Div" + vendorGst).html(`Loading...`);
            $("#gstinReturnsDatacomp3b_Div" + vendorGst).html(`Loading...`);
          },
          success: function(response) {
            responseObj = JSON.parse(response);
            let fy = responseObj['fy'];
            responseData = responseObj["data"];
            //  console.log(responseData["fillingFreq"][Object.keys(responseData["fillingFreq"])[0]]);
            // alert(responseData["lstupdt"]);
            // $("#mdl_gstin_comp_span" + vendorGst).html(responseData["gstin"]); 

            // $("#mdl_gstin_reg_comp_span" + vendorGst).html(responseData["rgdt"]);

            // var gstin_status = responseData["sts"];

            // $("#mdl_gstin_last_update_comp_span" + vendorGst).html(responseData["lstupdt"]);

            // $("#mdl_gstin_reg_comp_span" + vendorGst).html(responseData["rgdt"]);

            // mdl_gstin_status_comp_span

            // $("#mdl_gstin_last_update_comp_span"+vendorGst).html(gstin_last_update);

            // if (gstin_status == "Active") {
            //   $("#mdl_gstin_status_comp_span" + vendorGst).html(`<span class="bg-success text-light rounded px-1">${gstin_status}</span>`);
            // } else {
            //   $("#mdl_gstin_status_comp_span" + vendorGst).html(`<span class="bg-warning text-light rounded px-1">${gstin_status}</span>`);
            // }
            // let returnType = responseData["fillingFreq"][Object.keys(responseData["fillingFreq"])[0]] ?? "M";

            // alert(returnType);

            let gstinReturnsDataDivHtml = `
                <table class="table table-striped table-bordered w-100">
                <thead>
                  <tr>
                    <th>Financial Year</th>
                    <th>Tax Period</th>
                    <th>Date of Filing</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>`;

            responseData["EFiledlist"].forEach(function(rowVal, rowId) {
              //  console.log(rowVal);
              if (rowVal['rtntype'] == 'GSTR1') {
                var dateString = rowVal["ret_prd"];
                // Extract the first two characters as the month
                var monthString = dateString.substr(0, 2);
                // Convert the month string to an integer
                var month = parseInt(monthString, 10);
                // Array of month names
                var monthNames = [
                  "January", "February", "March", "April", "May", "June",
                  "July", "August", "September", "October", "November", "December"
                ];
                // Get the month name based on the numeric month
                var monthName = monthNames[month - 1]; // Subtract 1 because arrays are 0-based
                gstinReturnsDataDivHtml += `
                      <tr>
                        <td>${fy}</td>
                        <td>${monthName ?? "-"}</td>
                        <td>${rowVal["dof"] ?? "-"}</td>
                        <td>${rowVal["status"] ? '<i class="fa fa-check" style="color: green;"> FILED</i>' : '<i class="fa fa-window-close" style="color: red;"> NOT FILED</i>'}</td>
                     
                      </tr>
                    `;
              }
            });
            gstinReturnsDataDivHtml += `</tbody></table>`;
            //3b
            let gstinReturnsDataDivHtml3b = `
                <table class="table table-striped table-bordered w-100">
                <thead>
                  <tr>
                    <th>Financial Year</th>
                    <th>Tax Period</th>
                    <th>Date of Filing</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>`;
            responseData["EFiledlist"].forEach(function(rowVal, rowId) {
              //  console.log(rowVal);
              if (rowVal['rtntype'] == 'GSTR3B') {
                var dateString = rowVal["ret_prd"];
                // Extract the first two characters as the month
                var monthString = dateString.substr(0, 2);
                // Convert the month string to an integer
                var month = parseInt(monthString, 10);
                // Array of month names
                var monthNames = [
                  "January", "February", "March", "April", "May", "June",
                  "July", "August", "September", "October", "November", "December"
                ];
                // Get the month name based on the numeric month
                var monthName = monthNames[month - 1]; // Subtract 1 because arrays are 0-based
                gstinReturnsDataDivHtml3b += `
                      <tr>
                        <td>${fy}</td>
                        <td>${monthName ?? "-"}</td>
                        <td>${rowVal["dof"] ?? "-"}</td>
                        <td>${rowVal["status"] ? '<i class="fa fa-check" style="color: green;"> FILED</i>' : '<i class="fa fa-window-close" style="color: red;"> NOT FILED</i>'}</td>
                     
                      </tr>
                    `;
              }
            });
            gstinReturnsDataDivHtml3b += `</tbody></table>`;
            $("#gstinReturnsDatacomp_Div" + vendorGst).html(gstinReturnsDataDivHtml);
            $("#gstinReturnsDatacomp3b_Div" + vendorGst).html(gstinReturnsDataDivHtml3b);
            console.log(gstinReturnsDataDivHtml);
          }
        });
      }
    });
  });

  // statement_date();
  $(document).on("change", "#dateDrop", function() {
    //alert(1);
    var val = $(this).val();
    var vendor_code = $(this).find("option:selected").data("val");
    // alert(vendor_code);
    statement_date(vendor_code, val);
  });
</script>


<script>
  $(document).ready(function() {
    $('.dateDrop').change(function() {
      var selectedValue = $(this).val();
      var dataAttr = $(this).find('option:selected').data('attr');
      if (selectedValue === 'CustomSlide') {
        // alert(dataAttr);
        $('.customDateDiv_' + dataAttr).fadeIn().css('opacity', 1); // Fade in and set opacity to 1
        // Your other actions here
      } else {
        $('#customDateDiv_' + dataAttr).fadeOut().css('opacity', 0);
      }
    });
    $('.close-btn_' + dataAttr).click(function() {
      $('#customDateDiv_' + dataAttr).fadeOut().css('opacity', 0);
    });
  });
</script>


<script>
  $(".date_apply").click(function() {
    //alert(1);
    var attr = $(this).data('attr');
    // alert(attr);
    var from_date = $(".from_" + attr).val();
    var to_date = $(".to_" + attr).val();
    // alert(from_date);
    // alert(to_date);
    $.ajax({
      url: `ajaxs/vendor/ajax-dateRange-statement.php`,
      type: 'POST',
      data: {
        from_date: from_date,
        to_date: to_date,
        vendor_code: attr
      },
      beforeSend: function() {

      },
      success: function(response) {
        // alert(response);
        console.log(response);
        var obj = jQuery.parseJSON(response);
        $('.stateTable_' + attr).html(obj['html']);
      }
    });
  });
</script>

<script>
  function reconciliation(from_date, to_date, attr) {
    //  alert(1);
    $.ajax({
      url: `ajaxs/vendor/ajax-reconciliation.php`,
      type: 'POST',
      data: {
        from_date: from_date,
        to_date: to_date,
        party_code: attr

      },
      beforeSend: function() {
        $(".recon_preview_" + attr).html(` <div class="spinner-border text-dark" role="status">
                                                  <span class="visually-hidden">Loading...</span>
                                                </div>`)

      },
      success: function(response) {
        // alert(response);
        console.log(response);
        $(".recon_preview_" + attr).html(response);
      }
    });
  }


  $(".date_apply_recon").click(function() {
    var attr = $(this).data('attr');
    //alert(attr);
    var from_date = $(".from_recon_" + attr).val();
    var to_date = $(".to_recon_" + attr).val();
    // alert(to_date);
    reconciliation(from_date, to_date, attr);
  });


  $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function(e) {
    var targetTab = $(e.target).attr("href"); // Get the target tab ID or class
    if (targetTab.startsWith("#reconciliation")) {
      // alert(1);
      var attr = targetTab.substring("#reconciliation".length);
      //alert(attr);
      const currentDate = new Date();
      const year = currentDate.getFullYear();
      const month = String(currentDate.getMonth() + 1).padStart(2, '0');
      const lastDay = new Date(year, month, 0).getDate();
      const from_date = `${year}-${month}-01`;
      const to_date = `${year}-${month}-${lastDay}`;

      $('.from_recon_' + attr).val(from_date);
      $('.to_recon_' + attr).val(to_date);
      reconciliation(from_date, to_date, attr);
    }
  });


  $(document).keyup('.vendor_pan', function() {
    //alert(1);
    var pan = $('.vendor_pan').val();
    //alert(pan);
    $.ajax({
      url: `ajaxs/vendor/ajax-vendor-pan.php`,
      type: 'POST',
      data: {
        pan: pan
      },
      beforeSend: function() {

      },
      success: function(response) {
        //  alert(response);
        if (response > 0) {
          $('#pan_error').html('pan already exists');
          document.getElementById("next_first").disabled = true;
        } else {
          $('#pan_error').html(``);
          document.getElementById("next_first").disabled = false;
        }
      }
    });
  });
  $(document).keyup('.account_number', function() {
    //alert(1);
    let acc = $('.account_number').val();
    // alert(acc);
    let ifsc = $('#vendor_bank_ifsc').val();
    //alert(ifsc);
    let vendor_id = $("#vendor_id_edit").val();
    //alert(vendor_id);
    if (vendor_id != '') {
      // alert(1);
      check_account_edit(ifsc, acc, vendor_id);
    } else {
      // alert(2);
      check_account(ifsc, acc);
    }
  });

  $(document).keydown('.account_number', function() {
    //  alert(1);
    let acc = $('.account_number').val();
    // alert(acc);
    let ifsc = $('#vendor_bank_ifsc').val();
    // alert(ifsc);

    let vendor_id = $("#vendor_id_edit").val();
    if (vendor_id != '') {
      check_account_edit(ifsc, acc, vendor_id);
    } else {
      check_account(ifsc, acc);
    }
  });

  // ------------------ Mail Trail script Start----------------------------
  $(document).on("click", ".mailtabbtn", function() {
    var ccode = $(this).data('vendorcode');
    var id = $(this).data('vendorid');
    // alert(ccode);
    $.ajax({
      url: 'ajaxs/mail/ajax-mail-trail.php', // <-- point to server-side PHP script 
      type: 'POST',
      data: {
        ccode,
        id
      },
      beforeSend: function() {
        // $('.Ckecked_loder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
        // $(".Ckecked_loder").toggleClass("disabled");
      },
      success: function(responseData) {
        $(`#mail${id}`).html(responseData);
      }
    });
  });



  // ------------------ Audit Trail script Start----------------------------
  $(document).on("click", ".auditTrailVendor", function() {
    var ccode = $(this).data('ccode');
    var id = $(this).data('ids');
    // alert(ccode);
    $.ajax({
      url: 'ajaxs/audittrail/ajax-audit-trail-vendor.php?auditTrailBodyContent', // <-- point to server-side PHP script 
      type: 'POST',
      data: {
        ccode,
        id
      },
      beforeSend: function() {
        // $('.Ckecked_loder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
        // $(".Ckecked_loder").toggleClass("disabled");
      },
      success: function(responseData) {
        $(`.auditTrailBodyContentVendor${ccode}`).html(responseData);
      }
    });
  });

  $(document).on("click", ".auditTrailBodyContentLineVendor", function() {
    $(`.auditTrailBodyContentLineDiv`).html(`<div class="modal-header">
          <div class="head-audit">
            <p><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading ...</p>
          </div>
          <div class="head-audit">
            <p>xxxxxxxxxxxxxx</p>
            <p>xxxxxxxxx</p>
          </div>

        </div>
        <div class="modal-body p-0">
          <div class="free-space-bg">
            <div class="color-define-text">
              <p class="update"><span></span> Record Updated </p>
              <p class="all"><span></span> New Added </p>
            </div>
            <ul class="nav nav-tabs pb-0" id="myTab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="concise-tab" data-toggle="tab" href="#consize" role="tab" aria-controls="concise" aria-selected="true"><i class="fa fa-th-large mr-2" aria-hidden="true"></i> Concised View</a>
              </li>

              <li class="nav-item">
                <a class="nav-link" id="detail-tab" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="false"><i class="fa fa-list mr-2" aria-hidden="true"></i>Detailed View</a>
              </li>
            </ul>
          </div>
          <div class="tab-content pt-0" id="myTabContent">
            <div class="tab-pane fade show active" id="consize" role="tabpanel" aria-labelledby="consize-tab">
              <div class="dotted-box">
                <p class="overlap-title">Loading ...</p>
                <div class="box-content hightlight">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
              </div>
            </div>

            <!-- -------------------Audit History Tab Body Start------------------------- -->
            <div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab">
              <div class="dotted-box">
                <p class="overlap-title">Loading ...</p>
                <div class="box-content hightlight">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
              </div>
              <div class="dotted-box">
                <p class="overlap-title">Loading ...</p>
                <div class="box-content hightlight">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
              </div>
            </div>
            <!-- -------------------Audit History Tab Body End------------------------- -->
          </div>
        </div>`);
    var ccode = $(this).data('ccode');
    var id = $(this).data('id');
    // alert(ccode);
    $.ajax({
      url: 'ajaxs/audittrail/ajax-audit-trail-vendor.php?auditTrailBodyContentLine', // <-- point to server-side PHP script 
      type: 'POST',
      data: {
        ccode,
        id
      },
      beforeSend: function() {
        // $('.Ckecked_loder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
        // $(".Ckecked_loder").toggleClass("disabled");
      },
      success: function(responseData) {
        $(`.auditTrailBodyContentLineDiv`).html(responseData);
      }
    });
  });
</script>

<!-- start of ajax to vendor debit note table -->

<script>
  $(document).on("click", ".debitNoteBtn", function() {
    let vid = $(this).data('vid');
    console.log("button clicked. debitnote... ", vid);
    $.ajax({
      type: "GET",
      url: `ajaxs/debit-note/ajax-customer-vendor-debitnote.php`,
      data: {
        act: "debit-note",
        id: vid,
        creditorsType: "vendor"
      },
      beforeSend: function() {
        $('.debitnotetable').html('');
      },
      success: function(res) {
        console.log(res);
        $('.debitnotetable').html(res);
      }
    });
  })

  // <!-- start of ajax to vendor credit note table -->

  $(document).on("click", ".creditNoteBtn", function() {
    let vid = $(this).data('vid');
    console.log("button clicked by creditnote.... ", vid);
    $.ajax({
      type: "GET",
      url: `ajaxs/credit-note/ajax-customer-vendor-creditnote.php`,
      data: {
        act: "credit-note",
        id: vid,
        creditorsType: "vendor"
      },
      beforeSend: function() {
        $('.creditnotetable').html('');
      },
      success: function(res) {
        console.log(res);

        $('.creditnotetable').html(res);
      }
    });
  })
</script>