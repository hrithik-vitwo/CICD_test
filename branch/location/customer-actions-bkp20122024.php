<?php
require_once("../../app/v1/connection-branch-admin.php");
//   administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/branch/func-customers-controller.php");
$customerDetailsObj = new CustomersController();
if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusCustomer($_POST, "customer_id", "customer_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"]);
}
if (isset($_POST["createdata"])) {
  $addNewObj = createDataCustomer($_POST);
  //  console($addNewObj);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}
if (isset($_POST["editData"])) {
  // console($_SESSION);
  $editDataObj = updateDataCustomer($_POST);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}
if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}
if (isset($_GET['delete'])) {
  // echo 1;
  $CustomerId = base64_decode($_GET['delete']);
  $del = queryUpdate("UPDATE `erp_customer` SET `customer_status`='deleted' WHERE `customer_id`=$CustomerId");
  //console($del);

  if ($del['status'] == "success") {
    swalToast("success", "Deleted Successfully", "manage-customers.php");
  } else {
    swalToast("warning", "Something Went Wrong", "manage-customers.php");
  }
}

if (isset($_POST["customerMailShootingSubmitBtn"])) {


  $addCustomerMail = $customerDetailsObj->addCustomerMail($_POST);
  // console($addCustomerMail);
  if ($addCustomerMail['status'] == "success") {
    swalAlert($addCustomerMail["status"], $addCustomerMail["message"]);
  } else {
    swalAlert($addCustomerMail["status"], $addCustomerMail["message"]);
  }
}
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<style>
  .phone-alt-number,
  .email-alt {
    display: flex;
    gap: 3px;
    align-items: center;
  }

  .phone-alt-number a,
  .email-alt a {
    color: #fff;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .customer-modal .modal-header {
    height: 260px !important;
  }

  h2.accordion-header {
    display: block !important;
  }

  .customer-modal .modal-header .right-info p {
    display: flex;
    gap: 8px;
    align-items: center;
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

  .customer-modal .nav.nav-tabs li.nav-item a.nav-link {
    font-size: 12px;
  }

  .vendor-modal .modal-header {
    height: 370px !important;
  }

  .accordion {
    background-color: transparent !important;
  }

  .matrix-accordion button::after {
    background-image: url(../../public/assets/ion-icon/chevron-up-outline.svg) !important;
  }

  .display-flex-space-between p {
    width: 77%;
    text-align: left !important;
    font-size: 10px !important;
  }

  .customer-modal .matrix-accordion button {
    margin: 0px 0px;
  }

  .row .col.col-head {
    font-size: 10px;
    color: #fff;
    font-weight: 600;
    padding: 10px 7px;
    border-bottom: 1px solid #54545426;
    text-align: left;
    /* background: #003060; */
    border-right: 1px solid #fff;
  }

  .row .col.col-body {
    font-size: 10px;
    color: #003060;
    font-weight: 600;
    padding: 10px 7px;
    border-bottom: 1px solid #54545426;
    text-align: left;
  }

  .row .col.col-head:nth-child(1),
  .row .col.col-body:nth-child(1) {
    max-width: 100px;
  }

  .row .col.col-head:last-child {
    border-right: 0;
  }

  .row .col.col-body:nth-child(3) {
    text-align: right;
  }

  .status-custom {
    font-size: 10px;
  }

  ul#experienceTab.nav-pills {
    position: sticky;
    top: 70px;
  }

  div#experienceTabContent {
    padding-left: 2em;
    margin-top: -2em;
  }

  div#experienceTabContent h3 {
    position: sticky;
    top: calc(150% - 627px);
    background-color: #fff;
    z-index: 99;
    padding: 10px 0;
  }

  div#experienceTabContent .status,
  div#experienceTabContent .status-secondary {
    font-weight: 500;
    width: 70px;
  }

  div#experienceTabContent .card-header {
    padding: 0px 20px;
    border-radius: 0;
    position: sticky;
    top: calc(150% - 575px);
    z-index: 999;
  }

  div#experienceTabContent .card-body {
    border: 1px solid #26262626;
    padding: 0 20px;
    z-index: 0;
  }

  div#experienceTabContent .card-body .avatar {
    width: 30px;
    height: 30px;
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

  div#experienceTabContent .card,
  div#experienceTabContent .card .card-body {
    background-color: #fff;
  }

  div#experienceTabContent .card .card-body {
    overflow: auto;
  }

  div#experienceTabContent table tr th {
    font-size: 10px;
  }

  div#experienceTabContent table tr td {
    font-size: 10px;
    background: transparent !important;
  }

  .chartContainer {
    width: 100%;
    height: 500px;
  }

  .pieChartContainer {
    width: 100%;
    height: 400px;
    font-size: 10px;
  }

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

  .chartContainer {
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
    background: #dbe5ee;
  }

  .card.flex-fill .card-header input,
  .card.flex-fill .card-header select {
    max-width: 155px;
  }

  .head-title,
  .head-input {
    display: flex;
    gap: 10px;
    align-items: center;
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
    height: auto !important;
  }

  #containerThreeDot #menu-wrap .dots>div,
  #containerThreeDot #menu-wrap .dots>div:after,
  #containerThreeDot #menu-wrap .dots>div:before {
    background-color: #003060a6 !important;
  }

  #containerThreeDot #menu-wrap .toggler:checked~.menu {
    background: #fafafa !important;
  }

  .matrix-accordion button {
    background-color: #dbe5ee !important;
    color: #000 !important;
    font-weight: 600;
  }

  input.toggler.bg-transparent:hover {
    background-color: #003060 !important;
  }

  .mail-tab .card {
    border-radius: 0;
    background: transparent;
    border-bottom: 1px solid #0000001a;
  }

  .customer-modal.modal.fade.right .nav.nav-tabs {
    gap: 3px;
  }

  .customer-modal.modal.fade.right .nav.nav-tabs li.nav-item a {
    padding: 10px 10px 15px;
    border: 0;
    border-radius: 10px 10px 0 0;
  }

  .customer-modal.modal.fade.right .nav.nav-tabs li.nav-item a:hover {
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

  .blur-body .tab-content {
    filter: blur(0);
    transition: filter 0.5s ease-in-out;
    height: 100%;
  }

  .blur-body .tab-content.blur {
    filter: blur(2px);
  }

  .wrapper-action {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  #form-mail .row {
    align-items: flex-end;
    margin: 15px 0;
  }

  #form-mail .row button {
    max-width: 100px;
  }

  .row.appendOperatorDiv {
    align-items: flex-end;
  }

  .col-table,
  .col-btn,
  .dlt-btn {
    margin: 9px 0;
  }

  .row.appendOperatorDiv .row {
    align-items: end;
  }

  .white-scroll-space {
    position: fixed;
    overflow-y: auto;
    height: 417px;
    overflow-x: hidden;
    width: 68%;
    background: #fff;
    z-index: 9;
    right: 15px;
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
    font-size: 10px !important;
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
    background: #c5ced6;
    color: #000;
    font-weight: 600;
  }

  .gst-return-data table td {
    background: #fff !important;
    border-bottom: 1px solid #cccccc12;
    border-color: #a0a0a06e !important;
    padding: 7px 16px;
    font-weight: 600;
  }

  .gst-return-data table tr:nth-child(2n) td {
    background-color: #fff !important;
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
    margin-top: 30px;
    overflow: auto;
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
    font-size: 22px !important;
  }




  table.statement-table tr th,
  table.statement-table tr td {
    font-size: 0.7rem !important;
  }

  table.statement-table tr:nth-child(odd) td {
    background: #f2f2f2 !important;
  }

  table.statement-table tr:nth-child(even) td {
    background: #fff !important;
  }



  /* @media print {

    body {
      font-size: 15px !important;
    }

    .noprint {
      display: none;
    }

    .modal.fade.right.customer-modal .modal-dialog {
      max-width: 98% !important;
      width: 100%;
      border: 0;
    }

    .modal.fade.right.customer-modal .modal-dialog .modal-content {
      width: 100% !important;
    }

    .row.state-date-action .btns {
      display: none;
    }

    .modal.fade.right.customer-modal .modal-dialog .modal-header {
      display: none;
    }


    .modal.fade.right.customer-modal .modal-dialog .modal-body .white-scroll-space {
      width: 97%;
      height: 97%;
    }



  } */


  .multisteps-form__progress .multisteps-form__progress-btn {
    pointer-events: none;
  }

  .custom-date-div {
    opacity: 0;
    position: relative;
    top: 0;
    margin-top: 0.7rem;
    width: auto;
    height: auto;
    background-color: #ffffff;
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

  #printable-content {
    display: none;
  }

  #non-printable-content {
    display: block;
  }

  .blur-body .tab-content .tab-pane:nth-child(5) .card .card-header {
    border-bottom: 1px solid #0000001c;
    border-radius: 0;
    background: transparent;
    padding: 0;
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
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
  }


  @media (max-width: 575px) {
    .customer-modal .modal-header {
      height: 270px !important;
    }
  }


  /* CSS styles for printing */
  @media print {

    * {
      font-size: 50pt !important;
    }

    #printable-content {
      display: block;

    }

    #printable-content p {
      font-size: 50px !important;
    }

    #non-printable-content {
      display: none;
    }
  }

  div.dataTables_wrapper div.dataTables_filter,
  .dataTables_wrapper .row:nth-child(3) {
    display: flex !important;
  }

  .recon-table th,
  .recon-table td {
    font-size: 10px !important;
  }
</style>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">
<link rel="stylesheet" type="text/css" media="print" href="../../public/assets/print.css">
<script src="../../public/assets/core.js"></script>
<script src="../../public/assets/charts.js"></script>
<script src="../../public/assets/animated.js"></script>
<script src="../../public/assets/forceDirected.js"></script>
<script src="../../public/assets/sunburst.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<?php
if (isset($_GET['create'])) {
  $stateHtml = '';
  $state_sql = queryGet("SELECT * FROM `erp_gst_state_code`", true);
  $state_data = $state_sql['data'];
  foreach ($state_data as $data) {
    $stateHtml .= '<option value="' . $data['gstStateName'] . '" >' . $data['gstStateName'] . '</option>';
  }
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper is-customer">

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card">
          <div class="modal-header card-header py-2 px-3">
            <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-info text-sm"></i>&nbsp;Notes</h4>
            <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
          </div>
          <div id="notesModalBody" class="modal-body card-body">
          </div>
        </div>
      </div>
    </div>
    <!-- Content Header (Page header) -->
    <div class="content-header mb-2 p-0 border-bottom">
      <div class="container-fluid">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Customer List</a></li>
          <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Customer</a></li>
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
                <!-- <h3 class="card-title">Create New Customer</h3>-->
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
                        <h4>Customer GSTIN</h4>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="info-vendor-gstin"><span>Put your GSTIN and click on below verify button to get your Bussiness details!</span></div>
                      <div class="form-inline">
                        <label for="">Enter your GSTIN number</label>
                        <input type="text" class="form-control vendor-gstin-input w-75" name="customerGstNoInput" id="customerGstNoInput" oninput="this.value = this.value.toUpperCase();">
                        <button class="btn btn-primary verify-btn checkAndVerifyGstinBtn">
                          <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </button>
                      </div>
                      <div class="d-flex mt-2">
                        <span class="text-xs font-bold">Don't have GSTIN? Check me </span>
                        <div class="d-inline ml-0 pl-2">
                          <input type="checkbox" id="isGstRegisteredCheckBoxBtn">
                          <label for="isGstRegisteredCheckBoxBtn">
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- <div class="card-body p-0 gstfield" id="gstform">
                <div class="row p-0 m-0">
                  <?php

                  ?>
                </div>
                <div class="row m-0 p-0 mt-3" id="VerifyGstinBtnDiv">
                  <div class="card gst-card ml-auto mr-auto">
                    <div class="card-header text-center h4 text-bold">Verify GSTIN</div>
                    <div class="card-body pt-4 pb-5">
                      <h6 class="mt-2 mb-3 text-muted text-center">Put your GSTIN and click on below verify button<br> to get your Bussiness details!</h6>
                      <div class="form-input">
                        <input type="text" name="customerGstNoInput" id="customerGstNoInput">
                        <label>Enter your GSTIN number</label>
                        <span class="btn-block2 send-btn checkAndVerifyGstinBtn">
                          <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div> -->

              <!--multisteps-form-->
              <div class="multisteps-form" id="multistepform" style="display:none;">
                <!--<div id="customerCreateMainForm"></div>-->

              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
      </div>
    </section>

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
                          <th>Finalcial Year</th>
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
                          <th>Finalcial Year</th>
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
    <!-- /.content -->
  </div>
<?php } else if (isset($_GET['edit'])) {

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
  <div class="content-wrapper is-customer">
    <!-- Content Header (Page header) -->

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card">
          <div class="modal-header card-header py-2 px-3">
            <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-info text-sm"></i>&nbsp;Notes</h4>
            <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
          </div>
          <div id="notesModalBody" class="modal-body card-body">
          </div>
        </div>
      </div>
    </div>

    <div class="content-header mb-2 p-0 border-bottom">
      <div class="container-fluid">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Customer</a></li>
          <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-edit po-list-icon"></i>
              Edit Customer</a></li>
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
        <!--progress bar-->
        <div class="row">
          <div class="col-12 col-lg-8 ml-auto mr-auto mb-4">
            <div class="multisteps-form__progress">
              <button class="multisteps-form__progress-btn js-active text-xs" type="button" title="User Info">Basic Details</button>
              <button class="multisteps-form__progress-btn text-xs" type="button" title="Comments" id="poc_btn" disabled>POC Details</button>
            </div>
          </div>
        </div>
        <!--form panels-->
        <div class="row">
          <div class="col-12 col-lg-8 m-auto">
            <form class="multisteps-form__form" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="edit_frm" name="edit_frm">
              <input type="hidden" name="editData" id="editData" value="">
              <input type="hidden" name="company_id" id="company_id" value="<?= $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"]; ?>">
              <input type="hidden" name="company_branch_id" id="company_branch_id" value="<?= $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]; ?>">

              <!--single form panel-->
              <div class="multisteps-form__panel bg-white js-active" data-animation="scaleIn">
                <div class="card vendor-details-card withOutGST-card mb-0">
                  <div class="card-header p-3">
                    <div class="display-flex">
                      <div class="head">
                        <i class="fa fa-user"></i>
                        <h4>Basic Details</h4>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="multisteps-form__content">
                      <?php
                      $editCustomerId = base64_decode($_GET['edit']);
                      //  $sql = "SELECT " . ERP_CUSTOMER . ".*, " . ERP_CUSTOMER_ADDRESS . ".* FROM `" . ERP_CUSTOMER . "`,`" . ERP_CUSTOMER_ADDRESS . "` WHERE `" . ERP_CUSTOMER . "`.`customer_id`=`" . ERP_CUSTOMER_ADDRESS . "`.`customer_id` AND `" . ERP_CUSTOMER_ADDRESS . "`.`customer_address_primary_flag`=1 AND `" . ERP_CUSTOMER . "`.`customer_id`=$editCustomerId";
                      //echo  $sql = "SELECT * FROM `".ERP_CUSTOMER."` WHERE `customer_id`=$editCustomerId";
                      $sql = "SELECT 
                        " . ERP_CUSTOMER . ".*, 
                        " . ERP_CUSTOMER_ADDRESS . ".* 
                    FROM 
                        `" . ERP_CUSTOMER . "`
                    LEFT JOIN 
                        `" . ERP_CUSTOMER_ADDRESS . "`
                    ON 
                        `" . ERP_CUSTOMER . "`.customer_id = `" . ERP_CUSTOMER_ADDRESS . "`.customer_id 
                        AND `" . ERP_CUSTOMER_ADDRESS . "`.`customer_address_primary_flag` = 1 
                        WHERE 
                        `" . ERP_CUSTOMER . "`.customer_id = $editCustomerId;
                    ";


                      $res = queryGet($sql);
                      $row = $res['data'];
                      // $res = $dbCon->query($sql);
                      // $row = $res->fetch_assoc();


                      $state_code = substr($row['customer_gstin'], 0, 2);
                      $state_sql = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode` = $state_code");
                      //console($row);
                      $state_name = $state_sql['data']['gstStateName'];
                      //console($row);
                      // echo "<pre>";
                      // print_r($row);
                      // echo "</pre>";
                      ?>
                      <input type="hidden" name="customer_id" value="<?= $editCustomerId ?>" id="">
                      <input type="hidden" name="customer_code" value="<?= $row['customer_code'] ?>" id="">
                      <div class="row">
                        <!-- <div class="col-md-6">
                      <div class="form-input">
                        <input type="text" name="customer_code" id="customer_code" value="<?= $row['customer_code'] ?>" readonly>
                        <label>Customer Code</label>
                      </div>
                    </div> -->

                        <div class="col-md-6">
                          <div class="form-input">
                            <label>GSTIN</label>
                            <?php

                            if (empty($row['customer_gstin']) || $row['customer_gstin'] == 0) {
                            ?>
                              <input class="form-control" type="text" name="customer_gstin" id="customer_gstin" value="<?= $row['customer_gstin'] ?>">
                            <?php
                            } else {
                            ?>
                              <input class="form-control" type="text" name="customer_gstin" id="customer_gstin" value="<?= $row['customer_gstin'] ?>" readonly>
                            <?php
                            }
                            ?>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Legal Name *</label>
                            <?php
                            if ($row['legal_name'] == '' || $row['legal_name'] == 0) {

                            ?>
                              <input class="form-control" type="text" name="legal_name" id="legal_name" value="<?= $row['legal_name'] ?>">
                            <?php
                            } else {
                            ?>
                              <input class="form-control" type="text" name="legal_name" id="legal_name" value="<?= $row['legal_name'] ?>" readonly>
                            <?php
                            }
                            ?>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Pan </label>
                            <input class="form-control" type="text" name="customer_pan" id="customer_pan" value="<?= $row['customer_pan'] ?>">
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Trade Name</label>
                            <?php
                            if ($row['trade_name'] == '' || $row['trade_name'] == 0) {

                            ?>
                              <input class="form-control" type="text" name="trade_name" id="trade_name" value="<?= $row['trade_name'] ?>">
                            <?php
                            } else {
                            ?>
                              <input class="form-control" type="text" name="trade_name" id="trade_name" value="<?= $row['trade_name'] ?>" readonly>
                            <?php
                            }
                            ?>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Constitution of Business</label>
                            <input class="form-control" type="text" name="constitution_of_business" id="constitution_of_business" value="<?= $row['constitution_of_business'] ?>">
                          </div>
                        </div>


                        <div class="col-md-6">
                          <div class="form-input">
                            <label>City</label>
                            <input class="form-control" type="text" name="city" id="city" value="<?= $row['customer_address_city'] ?>">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>District</label>
                            <input class="form-control" type="text" name="district" id="district" value="<?= $row['customer_address_district'] ?>">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Location</label>
                            <input class="form-control" type="text" name="location" id="location" value="<?= $row['customer_address_location'] ?>">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Building Number</label>
                            <input class="form-control" type="text" name="build_no" id="build_no" value="<?= $row['customer_address_building_no'] ?>">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Flat Number</label>
                            <input class="form-control" type="text" name="flat_no" id="flat_no" value="<?= $row['customer_address_flat_no'] ?>">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Street Name</label>
                            <input class="form-control" type="text" name="street_name" id="street_name" value="<?= $row['customer_address_street_name'] ?>">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Pin Code</label>
                            <input class="form-control" type="number" name="pincode" id="pincode" value="<?= $row['customer_address_pin_code'] ?>">
                          </div>
                        </div>
                        <?php

                        if ($row['customer_gstin'] == null) {

                        ?>
                          <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-input">
                              <label for="">Country</label>

                              <select id="countries" name="countries" class="form-control countriesDropDown_edit">
                                <?php
                                $countries_sql = queryGet("SELECT * FROM `erp_countries`", true);
                                $countries_data = $countries_sql['data'];
                                foreach ($countries_data as $data) {

                                ?>

                                  <option value="<?= $data['name'] ?>" <?php if ($data['name'] ==  $row['customer_address_country']) {
                                                                          echo "selected";
                                                                        } ?>><?= $data['name'] ?></option>
                                <?php
                                }
                                ?>
                              </select>
                              <!-- <input type="text" class="form-control" name="countries" id="countries" value="<?php echo $gstDetails['pradr']['addr']['stcd']; ?>"> -->
                            </div>
                          </div>

                          <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-input stateDivDropDown">
                              <label for="">State</label>
                              <?php

                              //  echo $row['customer_address_country'];
                              if ($row['customer_address_country']  == 'India') {
                              ?>
                                <select id="state" name="state" class="form-control secect2 stateDropDown">
                                  <?php

                                  $state_sql = queryGet("SELECT * FROM `erp_gst_state_code`", true);
                                  $state_data = $state_sql['data'];
                                  foreach ($state_data as $data) {

                                  ?>

                                    <option value="<?= $data['gstStateName'] ?>" <?php if ($data['gstStateName'] ==  $row['customer_address_state']) {
                                                                                    echo "selected";
                                                                                  } ?>><?= $data['gstStateName'] ?></option>
                                  <?php
                                  }
                                  ?>
                                </select>
                              <?php
                              } else {
                              ?>
                                <input class="form-control" type="text" name="state" id="state" value="<?= $state_name ?>">
                              <?php

                              }

                              ?>
                            </div>
                          </div>
                        <?php
                        } else {
                          //  echo 1;

                        ?>
                          <div class="col-md-6">
                            <div class="form-input">
                              <label>State</label>
                              <input class="form-control" type="text" name="state" id="state" value="<?= $state_name ?>">
                            </div>
                          </div>
                        <?php
                        }
                        ?>


                        <div class="col-md-6">
                          <div class="form-input">
                            <label for="">Company Currency</label>
                            <select id="customer_currency" name="currency" class="form-control">
                              <!--<option value="">Select Currency</option>-->
                              <?php
                              $listResult = getAllCurrencyType();
                              if ($listResult["status"] == "success") {
                                foreach ($listResult["data"] as $listRow) {
                              ?>
                                  <option <?php if ($row['customer_currency'] == $listRow['currency_id']) {
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
                            <input class="form-control" type="text" name="credit_period" value="<?= $row['customer_credit_period'] ?>" id="customer_credit_period">
                          </div>
                        </div>

                        <div class="col-md-6 display-none">
                          <div class="form-input">
                            <label style="visibility :hidden;">Opening Blance</label>
                            <input class="form-control" type="hidden" name="opening_balance" id="customer_opening_balance" value="<?php echo $row['customer_opening_balance'] ?>" id="customer_opening_balance">
                          </div>
                        </div>







                        <div class="col-md-6">
                          <div class="form-input">
                            <label for="">Discount Group</label>

                            <select id="discount_group" name="discount_group" class="form-control mt-0 form-control-border borderColor">
                              <option value="0">Select Customer Discount Group</option>
                              <?php
                              $discountGroups = queryGet("SELECT * FROM `erp_customer_discount_group` WHERE company_id = $company_id", true);
                              if ($discountGroups["status"] == "success") {
                                foreach ($discountGroups["data"] as $discountGroup) {
                              ?>
                                  <option value="<?php echo $discountGroup['customer_discount_group_id']; ?>" <?php if ($row['customer_discount_group'] == $discountGroup['customer_discount_group_id']) {
                                                                                                                echo "selected";
                                                                                                              } ?>><?php echo $discountGroup['customer_discount_group']; ?></option>
                              <?php }
                              } ?>
                            </select>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-input">
                            <label for="">Customer Mrp Group</label>
                            <select id="customer_mrp_group" name="customer_mrp_group" class="form-control mt-0 form-control-border borderColor">
                              <option value="">Select Customer Mrp Group</option>
                              <?php
                              $customerMrpGroup = queryGet("SELECT customer_mrp_group_id,customer_mrp_group FROM erp_customer_mrp_group WHERE company_id=$company_id", true);
                              if ($customerMrpGroup["status"] == "success") {
                                foreach ($customerMrpGroup["data"] as $data) {
                              ?>
                                  <option value="<?php echo $data['customer_mrp_group_id']; ?>" <?php echo ($data['customer_mrp_group_id'] == $row['customer_mrp_group']) ? 'selected' : ''; ?>><?php echo $data['customer_mrp_group']; ?></option>
                              <?php }
                              } ?>
                            </select>
                          </div>
                        </div>


                      </div>

                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="button-row d-flex mt-4">
                      <button class="btn btn-primary ml-auto js-btn-next" type="button" title="Next">Next</button>
                    </div>
                  </div>
                </div>
              </div>
              <!--single form panel-->
              <div class="multisteps-form__panel shadow rounded bg-white" data-animation="scaleIn">
                <div class="card vendor-details-card withOutGST-card mb-0">
                  <div class="card-header p-3">
                    <div class="display-flex">
                      <div class="head">
                        <i class="fa fa-user"></i>
                        <h4>POC Details</h4>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="multisteps-form__content">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Name of Person*</label>
                            <input class="form-control" type="text" name="customer_authorised_person_name" value="<?= $row['customer_authorised_person_name'] ?>" id="adminName">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Designation</label>
                            <input class="form-control" type="text" name="customer_authorised_person_designation" value="<?= $row['customer_authorised_person_designation'] ?>" id="customer_authorised_person_designation">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Phone Number*</label>
                            <input class="form-control" type="text" name="customer_authorised_person_phone" value="<?= $row['customer_authorised_person_phone'] ?>" id="adminPhone">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Alternative Phone </label>
                            <input class="form-control" type="text" name="customer_authorised_alt_phone" value="<?= $row['customer_authorised_alt_phone'] ?>" id="customer_authorised_person_phone">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Email*</label>
                            <input class="form-control" type="email" name="customer_authorised_person_email" value="<?= $row['customer_authorised_person_email'] ?>" id="adminEmail">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Alternative Email</label>
                            <input class="form-control" type="email" name="customer_authorised_alt_email" value="<?= $row['customer_authorised_alt_email'] ?>" id="customer_authorised_person_email">
                          </div>
                        </div>
                        <!-- <div class="col-md-6">
                        <div class="form-input">
                          <input type="text" name="adminPassword" id="adminPassword" value="<?php echo rand(00000, 999999) ?>">
                          <label>Login Password [Will be send to the POC email]</label>
                        </div>
                      </div> -->
                        <!-- <div class="col-md-3">
                        <div class="form-input">
                          <input type="file" name="customer_picture" id="customer_picture">
                        </div>
                      </div> -->
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Login Password [Will be send to the POC email]</label>
                            <input type="text" class="form-control" name="adminPassword" id="adminPassword" value="<?= $row['fldAdminPassword'] ?>">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label for="" class="label-hidden">label</label>
                            <select id="customer_visible_to_all" name="customer_visible_to_all" class="select2 form-control">
                              <option <?php if ($row['customer_visible_to_all'] == 'No') {
                                        echo "selected";
                                      } ?> value="No">Only for this location</option>
                              <option <?php if ($row['customer_visible_to_all'] == 'Yes') {
                                        echo "selected";
                                      } ?> value="Yes">Visible For ALL</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="button-row d-flex mt-4">
                      <button class="btn btn-secondary js-btn-prev" type="button" title="Prev">Prev</button>
                      <!-- <button class="btn ml-auto btn-danger add_data" type="button" title="Save As Draft" value="add_draft">Save As Draft</button> -->
                      <button id="customerCreateBtn" class="btn btn-primary ml-auto edit_data" type="submit" title="update" name="customerUpdateBtn">Update</button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>



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
  $url = BRANCH_URL . 'location/manage-customers.php';
?>
  <script>
    window.location.href = "<?= $url ?>";
  </script>
<?php
}

require_once("../common/footer.php");
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>



<script>
  // document.addEventListener("DOMContentLoaded", function() {
  //   var dropdownToggle = document.getElementById("navbarDropdown");

  //   function attachEventListeners() {
  //     var dropdownItems = document.querySelectorAll(".dropdown-item");
  //     dropdownItems.forEach(function(item) {
  //       item.addEventListener("click", function() {
  //         var itemName = item.textContent.trim();
  //         console.log(itemName);
  //         dropdownToggle.innerText = itemName;
  //       });
  //     });
  //   }

  //   // Attach event listeners initially
  //   attachEventListeners();


  // });

  //    function update(event) {
  //     console.log(event.target); // Log the event target
  //     if (event.target.classList.contains('dropdown-item')) {
  //         const selectedText = event.target.innerText;
  //         const dropdownAncestor = event.target.closest('.dropdown-menu');

  //         const headingId =
  //             'headingTab' +
  //             dropdownAncestor.querySelector('.dropdown-toggle').id.slice(-1);

  //         const heading = document.querySelector(`#${headingId}`);
  //         heading.textContent = selectedText;
  //     }
  // }
</script>



<script>
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
    // if ($('.blur-body').css('filter') === 'blur(2px)') {
    //     $('.blur-body').css('filter', 'none');
    // } else {
    //     $('.blur-body').css('filter', 'blur(2px)');
    // }
    $('.blur-body .tab-content').toggleClass('blur');
  });

  //********************************************************************************************************** */

  var BASE_URL = `<?= BASE_URL ?>`;
  var BRANCH_URL = `<?= BRANCH_URL ?>`;
  var LOCATION_URL = `<?= LOCATION_URL ?>`;
  $(document).ready(function() {
    $(document).on("change", "#isGstRegisteredCheckBoxBtn", function() {
      let isChecked = $(this).is(':checked');
      if (isChecked) {
        $("#customerGstNoInput").attr("readonly", "readonly");
        $("#customerPanNo").removeAttr("readonly");

        $.ajax({
          type: "GET",
          url: `${LOCATION_URL}ajaxs/ajax-customer-with-out-verify-gstin.php`,
          beforeSend: function() {
            $('.checkAndVerifyGstinBtn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Loading...');
            $(".checkAndVerifyGstinBtn").toggleClass("disabled");
          },
          success: function(response) {
            // console.log(response)
            // alert(response)
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
        $("#customerCreateMainForm").html("");
        $("#customerGstNoInput").removeAttr("readonly");
        $("#customerPanNo").attr("readonly", "readonly");
      }
      $(".checkAndVerifyGstinBtn").toggleClass("disabled");
    });

    $(".checkAndVerifyGstinBtn").click(function() {
      let customerGstNo = $("#customerGstNoInput").val();
      if (customerGstNo != "") {
        $.ajax({
          type: "GET",
          url: `${LOCATION_URL}ajaxs/ajax-customer-verify-gstin.php?gstin=${customerGstNo}`,
          beforeSend: function() {
            $('.checkAndVerifyGstinBtn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
            $(".checkAndVerifyGstinBtn").toggleClass("disabled");
          },
          success: function(response) {
            // console.log(response);
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

  });


  $("#customer_gstin").keyup(function() {
    //alert(1);

    let customerGstNo = $(this).val();
    //alert(customerGstNo);
    if (customerGstNo != "") {
      $.ajax({
        type: "GET",
        url: `${LOCATION_URL}ajaxs/ajax-customer-verify-gstin-edit.php?gstin=${customerGstNo}`,
        beforeSend: function() {
          $('.checkAndVerifyGstinBtn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          $(".checkAndVerifyGstinBtn").toggleClass("disabled");
        },
        success: function(response) {
          // console.log(response);
          // $(".checkAndVerifyGstinBtn").toggleClass("disabled");
          //  $('.checkAndVerifyGstinBtn').html("Re-Verify");
          responseObj = JSON.parse(response);
          console.log(responseObj);
          //console.log(responseObj['status']);
          //responseObj = JSON.parse(responseObj);
          //  $("#VerifyGstinBtnDiv").hide();
          // $("#multistepform").show();
          // $("#edit_frm").html(responseObj);
          //console.log(responseObj);
          $("#customer_pan").val(responseObj['customerPan']);
          $("#legal_name").val(responseObj['legal_name']);
          $("#trade_name").val(responseObj['customer_name']);
          $("#constitution_of_business").val(responseObj['ctb']);
          $("#city").val(responseObj['city']);
          $("#district").val(responseObj['district']);
          $("#location").val(responseObj['loc']);
          $("#flat_no").val(responseObj['flno']);

          $("#street_name").val(responseObj['st']);

          $("#pincode").val(responseObj['pncd']);

          $("#countries").html(`<option value="India">India</option>`);
          $("#state").val(responseObj['statename']);

          // $("#state").show();
          //$("#state").html(`<option>`responseObj['statename']`</option>`);

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





  $(document).ready(function() {
    $(document).on('change', '.customer_bank_cancelled_cheque', function() {
      var file_data = $('.customer_bank_cancelled_cheque').prop('files')[0];
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
          $("#customer_bank_ifsc").val(responseObj["payload"]["cheque_details"]["ifsc"]["value"]);
          $("#account_number").val(responseObj["payload"]["cheque_details"]["acc no"]["value"]);
          $("#account_holder").val(responseObj["payload"]["cheque_details"]["acc holder"]["value"]);

          $("#customer_bank_address").val(responseObj["payload"]["bank_details"]["ADDRESS"]);
          $("#customer_bank_name").val(responseObj["payload"]["bank_details"]["BANK"]);
          $("#customer_bank_branch").val(responseObj["payload"]["bank_details"]["BRANCH"]);
        }
      });
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
        htmll = '<label for="">State</label><input type="text" class="form-control" name="state" id="state" value="<?php echo $gstDetails['pradr']['addr']['stcd']; ?>">';
      }

      $('.stateDivDropDown').html(htmll);
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
          $("#visitingCard").hide();
          $("body").removeClass("modal-open");
          responseObj = JSON.parse(responseData);
          // console.log("=======================");
          // console.log(responseObj);
          $("#adminName").val(responseObj["payload"]["ContactNames"]["value"][0]["content"]);
          $("#adminEmail").val(responseObj["payload"]["Emails"]["value"][0]["content"]);
          $("#adminPhone").val(responseObj["payload"]["WorkPhones"]["value"][0]['value']);
          $("#customer_authorised_person_phone").val(responseObj["payload"]["WorkPhones"]["value"][0]['value']);
          $("#customer_authorised_person_email").val(responseObj["payload"]["Emails"]["value"][0]["content"]);
          let designationArr = [];
          let jobTitle = responseObj["payload"]["JobTitles"]["value"][0]["content"] ?? "";
          let departments = responseObj["payload"]["Departments"]["value"][0]["content"] ?? "";
          if (jobTitle != "") {
            designationArr.push(jobTitle);
          }
          if (departments != "") {
            designationArr.push(departments);
          }
          $("#customer_authorised_person_designation").val(designationArr.join(", "));

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
          console.log(responseObj);
          $("#adminName").val(responseObj["payload"]["ContactNames"]["value"]['0']["content"]);
          $("#customer_authorised_person_designation").val('');

          $("#adminPhone").val(responseObj["payload"]["WorkPhones"]["value"]['0']['value']);
          $("#customer_authorised_person_phone").val(responseObj["payload"]["WorkPhones"]["value"]['1']['value']);

          $("#adminEmail").val(responseObj["payload"]["Emails"]["value"]['0']["content"]);
          $("#customer_authorised_person_email").val(responseObj["payload"]["Emails"]["value"]['1']["content"]);

        }
      });
    });


    $(document).on("click", ".add_data", function() {
      var data = this.value;
      $("#createdata").val(data);
      // confirm('Are you sure to Submit?')
      $("#add_frm").submit();
    });

    // $(document).on("click", ".edit_data", function() {
    //   var data = this.value;
    //   $("#editData").val(data);
    //   alert(data);
    //   $("#edit_frm").submit();
    // });

    $(".edit_data").click(function() {
      var data = this.value;
      $("#editData").val(data);
      //confirm('Are you sure to Submit?')
      $("#edit_frm").submit();
    });

    // $(document).on("click", ".js-btn-next", function() {
    //   console.log("hi there!!!!!");
    // });

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


  /*************modal for custom select date************/




  // ====================================== Combined bullet/column and line graphs with multiple value axes ======================================
  am4core.ready(function() {

    // Themes begin
    am4core.useTheme(am4themes_animated);
    // Themes end

    // Create chart instance
    var chart = am4core.create("chartDivCombinedColumnAndLineChart", am4charts.XYChart);
    chart.logo.disabled = true;

    // Add data
    chart.data = [{
      "date": "2013-01-16",
      "market1": 71,
      "market2": 75,
      "sales1": 5,
      "sales2": 8
    }, {
      "date": "2013-01-17",
      "market1": 74,
      "market2": 78,
      "sales1": 4,
      "sales2": 6
    }, {
      "date": "2013-01-18",
      "market1": 78,
      "market2": 88,
      "sales1": 5,
      "sales2": 2
    }, {
      "date": "2013-01-19",
      "market1": 85,
      "market2": 89,
      "sales1": 8,
      "sales2": 9
    }, {
      "date": "2013-01-20",
      "market1": 82,
      "market2": 89,
      "sales1": 9,
      "sales2": 6
    }, {
      "date": "2013-01-21",
      "market1": 83,
      "market2": 85,
      "sales1": 3,
      "sales2": 5
    }, {
      "date": "2013-01-22",
      "market1": 88,
      "market2": 92,
      "sales1": 5,
      "sales2": 7
    }, {
      "date": "2013-01-23",
      "market1": 85,
      "market2": 90,
      "sales1": 7,
      "sales2": 6
    }, {
      "date": "2013-01-24",
      "market1": 85,
      "market2": 91,
      "sales1": 9,
      "sales2": 5
    }, {
      "date": "2013-01-25",
      "market1": 80,
      "market2": 84,
      "sales1": 5,
      "sales2": 8
    }, {
      "date": "2013-01-26",
      "market1": 87,
      "market2": 92,
      "sales1": 4,
      "sales2": 8
    }, {
      "date": "2013-01-27",
      "market1": 84,
      "market2": 87,
      "sales1": 3,
      "sales2": 4
    }, {
      "date": "2013-01-28",
      "market1": 83,
      "market2": 88,
      "sales1": 5,
      "sales2": 7
    }, {
      "date": "2013-01-29",
      "market1": 84,
      "market2": 87,
      "sales1": 5,
      "sales2": 8
    }, {
      "date": "2013-01-30",
      "market1": 81,
      "market2": 85,
      "sales1": 4,
      "sales2": 7
    }];

    // Create axes
    var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
    //dateAxis.renderer.grid.template.location = 0;
    //dateAxis.renderer.minGridDistance = 30;

    var valueAxis1 = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis1.title.text = "Sales";

    var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis2.title.text = "Market Days";
    valueAxis2.renderer.opposite = true;
    valueAxis2.renderer.grid.template.disabled = true;

    // Create series
    var series1 = chart.series.push(new am4charts.ColumnSeries());
    series1.dataFields.valueY = "sales1";
    series1.dataFields.dateX = "date";
    series1.yAxis = valueAxis1;
    series1.name = "Target Sales";
    series1.tooltipText = "{name}\n[bold font-size: 20]${valueY}M[/]";
    series1.fill = chart.colors.getIndex(0);
    series1.strokeWidth = 0;
    series1.clustered = false;
    series1.columns.template.width = am4core.percent(40);

    var series2 = chart.series.push(new am4charts.ColumnSeries());
    series2.dataFields.valueY = "sales2";
    series2.dataFields.dateX = "date";
    series2.yAxis = valueAxis1;
    series2.name = "Actual Sales";
    series2.tooltipText = "{name}\n[bold font-size: 20]${valueY}M[/]";
    series2.fill = chart.colors.getIndex(0).lighten(0.5);
    series2.strokeWidth = 0;
    series2.clustered = false;
    series2.toBack();

    var series3 = chart.series.push(new am4charts.LineSeries());
    series3.dataFields.valueY = "market1";
    series3.dataFields.dateX = "date";
    series3.name = "Market Days";
    series3.strokeWidth = 2;
    series3.tensionX = 0.7;
    series3.yAxis = valueAxis2;
    series3.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";

    var bullet3 = series3.bullets.push(new am4charts.CircleBullet());
    bullet3.circle.radius = 3;
    bullet3.circle.strokeWidth = 2;
    bullet3.circle.fill = am4core.color("#fff");

    var series4 = chart.series.push(new am4charts.LineSeries());
    series4.dataFields.valueY = "market2";
    series4.dataFields.dateX = "date";
    series4.name = "Market Days ALL";
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
  // ++++++++++++++++++++++++++++++++++++++ Combined bullet/column and line graphs with multiple value axes ++++++++++++++++++++++++++++++++++++++
</script>

<!-- CHART_ONLY -->
<script>
  $(document).ready(function() {
    for (elem of $(".chartContainer")) {
      let dataAttrValue = elem.getAttribute("id").split("_")[1];
      let id = $(`#fYDropdown_${dataAttrValue}`).val();

      $.ajax({
        type: "GET",
        url: `<?= LOCATION_URL ?>ajaxs/ajax-customer-chart.php?id=${id}&cust_id=${dataAttrValue}`,
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
      url: `<?= LOCATION_URL ?>ajaxs/ajax-customer-chart.php?id=${id}&cust_id=${dataAttrValue}`,
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
      url: `<?= LOCATION_URL ?>ajaxs/ajax-customer-chart.php?month=${month}&cust_id=${dataAttrValue}`,
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

  $(document).ready(function() {
    for (elem of $(".pieChartContainer")) {
      let dataAttrValue = elem.getAttribute("id").split("_")[1];
      let id = $(`#piefYDropdown_${dataAttrValue}`).val();

      let dataAttrCode = elem.getAttribute("id").split("_")[2];

      //  statement_date(dataAttrCode, val = null);


      $.ajax({
        type: "GET",
        url: `<?= LOCATION_URL ?>ajaxs/ajax-customer-chart.php?id=${id}&customer_id=${dataAttrValue}`,
        beforeSend: function() {
          $(".load-wrapp").show();
          $(".load-wrapp").css('opacity', 1);
        },
        success: function(result) {
          $(".load-wrapp").hide();
          $(".load-wrapp").css('opacity', 0);

          let res = jQuery.parseJSON(result);
          console.log(res['message']);
          let status = res['status'];
          if (status == 'success') {
            $(`#chartDivReceivableAgeing_${dataAttrValue}`).show();
            pieChart(res, "chartDivReceivableAgeing", dataAttrValue);
          } else {
            $(`#noTransactionFound_${dataAttrValue}`).show();
          }
        }
      });
    };
  });

  $(document).on("change", '.piefYDropdown', function() {

    var dataAttrValue = $(this).data('attr');
    var id = $(`#piefYDropdown_${dataAttrValue}`).val();

    $.ajax({
      type: "GET",
      url: `<?= LOCATION_URL ?>ajaxs/ajax-customer-chart.php?id=${id}&customer_id=${dataAttrValue}`,
      beforeSend: function() {
        $(".load-wrapp").show();
        $(".load-wrapp").css('opacity', 1);
      },
      success: function(result) {
        $(".load-wrapp").hide();
        $(".load-wrapp").css('opacity', 0);

        let res = jQuery.parseJSON(result);

        pieChart(res, "chartDivReceivableAgeing", dataAttrValue);
      }
    });
  });

  function pieChart(chartData, chartTitle, custId) {

    am4core.ready(function() {

      // Themes
      am4core.useTheme(am4themes_animated);

      var chart = am4core.create(`${chartTitle}_${custId.trim()}`, am4charts.PieChart3D);
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

      // chart.paddingLeft = 50;
      // chart.paddingRight = 40;
      // chart.paddingTop = 20;
      // chart.paddingBottom = 20;

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


  // ====================================== Combined bullet/column and line graphs with multiple value axes ======================================

  function salesVsCollection(chartData, chartTitle, custId) {


    $(`.${chartTitle}_${custId.trim()}`).text(`Recievable Vs Recieved`);

    if (chartData.sql_list_all_cust.length == 0 && chartData.sql_list_specific_cust.length == 0) {
      const currentDate = new Date();
      const year = currentDate.getFullYear();
      const month = String(currentDate.getMonth() + 1).padStart(2, '0');
      const day = String(currentDate.getDate()).padStart(2, '0');

      const formattedDate = `${year}-${month}-${day}`;

      chartData = {
        "sql_list_all_cust": [{
          date_: formattedDate,
          total_receivable_all: 0,
          total_received_all: 0
        }],
        "sql_list_specific_cust": [{
          date_: formattedDate,
          total_receivable: 0,
          total_received: 0
        }]
      };
    };

    am4core.ready(function() {

      // Themes begin
      am4core.useTheme(am4themes_animated);
      // Themes end

      // Create chart instance
      var chart = am4core.create(`${chartTitle}_${custId.trim()}`, am4charts.XYChart);
      chart.logo.disabled = true;

      let finalData = [];
      let outerIndex = 0;

      for (obj of chartData.sql_list_all_cust) {
        obj.total_receivable_all = Number(obj.total_receivable);
        obj.total_received_all = Number(obj.total_received);
        obj.total_receivable = 0;
        obj.total_received = 0;
        finalData.push(obj);
      };

      for (obj of chartData.sql_list_specific_cust) {

        const outerObj = finalData.map(obj => {
          return obj.date_
        })
        outerIndex = outerObj.indexOf(obj.date_)

        if (outerIndex !== -1) {
          finalData[outerIndex].total_receivable = Number(obj.total_receivable);
          finalData[outerIndex].total_received = Number(obj.total_received);
        } else {
          obj.total_receivable = Number(obj.total_receivable);
          obj.total_received = Number(obj.total_received);
          obj.total_receivable_all = 0;
          obj.total_received_all = 0;
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
      valueAxis1.title.text = "This Customer";

      var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
      valueAxis2.title.text = "All Customers";
      valueAxis2.renderer.opposite = true;
      valueAxis2.renderer.grid.template.disabled = true;

      // Create series
      var series1 = chart.series.push(new am4charts.ColumnSeries());
      series1.dataFields.valueY = "total_receivable";
      series1.dataFields.dateX = "date_";
      series1.yAxis = valueAxis1;
      series1.name = "Receivable";
      series1.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";
      series1.fill = chart.colors.getIndex(0);
      series1.strokeWidth = 0;
      series1.clustered = false;
      series1.columns.template.width = am4core.percent(40);

      var series2 = chart.series.push(new am4charts.ColumnSeries());
      series2.dataFields.valueY = "total_received";
      series2.dataFields.dateX = "date_";
      series2.yAxis = valueAxis1;
      series2.name = "Recieved";
      series2.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";
      series2.fill = chart.colors.getIndex(0).lighten(0.5);
      series2.strokeWidth = 0;
      series2.clustered = false;
      series2.toBack();

      var series3 = chart.series.push(new am4charts.LineSeries());
      series3.dataFields.valueY = "total_received_all";
      series3.dataFields.dateX = "date_";
      series3.name = "Recieved (all customers)";
      series3.strokeWidth = 2;
      series3.tensionX = 0.7;
      series3.yAxis = valueAxis2;
      series3.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";

      var bullet3 = series3.bullets.push(new am4charts.CircleBullet());
      bullet3.circle.radius = 3;
      bullet3.circle.strokeWidth = 2;
      bullet3.circle.fill = am4core.color("#fff");

      var series4 = chart.series.push(new am4charts.LineSeries());
      series4.dataFields.valueY = "total_receivable_all";
      series4.dataFields.dateX = "date_";
      series4.name = "Recievable (all customers)";
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
</script>

<script>
  function statement_date(customer_code) {
    // alert(customer_code);
    var attr = $(this).data('attr');

    $.ajax({
      url: `ajaxs/vendor/ajax-statement.php`,
      type: 'POST',
      data: {

        customer_code: attr
      },
      beforeSend: function() {

      },
      success: function(response) {
        // alert(response);
        //console.log(response);
        var obj = jQuery.parseJSON(response);
        //console.log(obj['html']);
        $('.stateTable_' + attr).html(obj['html']);
      }
    });
  }


  $(document).on("change", ".dateDrop", function() {
    //alert(1);

    // var val = $(this).val();
    var customer_code = $(this).find("option:selected").data("val");
    // alert(customer_code);
    statement_date(customer_code);

  });




  $(document).ready(function() {
    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function(e) {
      var targetTab = $(e.target).attr("href"); // Get the target tab ID or class
      if (targetTab.startsWith("#compliance")) {
        var vendorGst = targetTab.substring("#compliance".length);
        console.log('vendorGst');
        console.log(vendorGst);

        $.ajax({
          url: `ajaxs/vendor/ajax-gst-review.php?gstin=${vendorGst}`,
          type: 'get',
          beforeSend: function() {
            $("#gstinReturnsDatacomp_Div" + vendorGst).html(`Loding...`);
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

  // Get the container element
  const container = document.getElementById('columns-container');

  // Get the add button
  const addButton = container.querySelector('.add-btn');

  // Add event listener to the add button
  addButton.addEventListener('click', addRow);

  // Function to add a new row
  // function addRow() {
  //   // Create a new row element
  //   const newRow = document.createElement('div');
  //   newRow.classList.add('row');

  //   // Create the first column
  //   const column1 = document.createElement('div');
  //   column1.classList.add('col-lg-4', 'col-md-4', 'col-sm-4', 'col-table');
  //   column1.innerHTML = `
  //   <div class="head">
  //     <h5 class="font-bold text-xs">Days</h5>
  //   </div>
  //   <div class="body">
  //     <input type="text" class="form-control">
  //   </div>
  //   `;

  //   // Create the second column
  //   const column2 = document.createElement('div');
  //   column2.classList.add('col-lg-4', 'col-md-4', 'col-sm-4', 'col-table');
  //   column2.innerHTML = `
  //   <div class="head">
  //     <h5 class="font-bold text-xs">Operator</h5>
  //   </div>
  //   <div class="body">
  //     <select name="" id="" class="form-control">
  //       <option value="">Post of Invoice date</option>
  //       <option value="">Post of Due date</option>
  //       <option value="">Early of Due date</option>
  //     </select>
  //   </div>
  //   `;

  //   // Create the plus icon
  //   const plusIcon = document.createElement('i');
  //   plusIcon.classList.add('fa', 'fa-plus', 'mr-2');

  //   // Create the add button with the plus icon
  //   const addButton = document.createElement('button');
  //   addButton.classList.add('btn', 'btn-primary', 'add-btn');
  //   addButton.appendChild(plusIcon);
  //   addButton.addEventListener('click', addRow);

  //   // Create the third column for the plus icon button
  //   const column3 = document.createElement('div');
  //   column3.classList.add('col-lg-4', 'col-md-4', 'col-sm-4', 'col-btn');
  //   column3.appendChild(addButton);

  //   // Append the columns to the new row
  //   newRow.appendChild(column1);
  //   newRow.appendChild(column2);
  //   newRow.appendChild(column3);

  //   // Insert the new row below the existing row
  //   container.insertBefore(newRow, addButton.parentElement.parentElement.nextSibling);
  // }

  function addMultiOperator(id) {
    let addressRandNo = Math.ceil(Math.random() * 100000);

    $(`#appendOperatorDiv_${id}`).append(`
      <div class="row">
          <div class="col-lg-5 col-md-5 col-sm-5 col-table">
            <div class="head">
              <h5 class="font-bold text-xs">Days</h5>
            </div>
            <input type="hidden" name="customerMail[${addressRandNo}][customer_id]" value="${id}" class="form-control">
            <div class="body">
              <input type="text" name="customerMail[${addressRandNo}][shootingDays]" placeholder="Enter days | Number" class="form-control">
            </div>
          </div>
          <div class="col-lg-5 col-md-5 col-sm-5 col-table">
            <div class="head">
              <h5 class="font-bold text-xs">Operator</h5>
            </div>
            <div class="body">
              <select name="customerMail[${addressRandNo}][operator]" class="form-control">
                <option value="post_of_invoice_date">Post of Invoice date</option>
                <option value="post_of_due_date">Post of Due date</option>
                <option value="early_of_due_date">Early of Due date</option>
              </select>
            </div>
          </div>
          <div class="col-lg-2 col-md-2 col-sm-2 dlt-btn">
              <button type="button" style="cursor: pointer" class="btn btn-danger">
                  <i class="fa fa-minus"></i>
              </button>
          </div>
      </div>
    `);

    $(document).on("click", ".dlt-btn", function() {
      $(this).parent().remove();
    });
  }
</script>

<script>
  function printContent() {
    var printContents = document.getElementById("printable-content").innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
  }

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
</script>


<script>
  //  $(document).ready(function() {
  //       $('#dateDrop').change(function() {
  //           var selectedValue = $(this).val();

  //           if (selectedValue === 'CustomSlide') {
  //             var attr = $('option:selected', this).data('attr'); // Find the selected option and retrieve its data attribute
  //           alert(attr);

  //               $('#customDateDiv').fadeIn().css('opacity', 1); // Fade in and set opacity to 1

  //           } else {
  //               $('#customDateDiv').fadeOut().css('opacity', 0); // Fade out and set opacity to 0
  //           }

  //       });

  //       $('.close-btn').click(function() {
  //           $('#customDateDiv').fadeOut().css('opacity', 0); // Fade out and set opacity to 0
  //       });
  //   });






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
    //  alert(1);
    var attr = $(this).data('attr');
    //  alert(attr);
    var from_date = $(".from_" + attr).val();
    var to_date = $(".to_" + attr).val();
    // alert(from_date);
    // alert(to_date);

    $.ajax({
      url: `ajaxs/customer/ajax-dateRange-statement.php`,
      type: 'POST',
      data: {
        from_date: from_date,
        to_date: to_date,
        customer_code: attr

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




  });
</script>





<script>
  function reconciliation(from_date, to_date, attr) {
    $.ajax({
      url: `ajaxs/customer/ajax-reconciliation.php`,
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






  // ------------------ Mail Trail script Start----------------------------
  $(document).on("click", ".mailtabbtn", function() {
    var ccode = $(this).data('customercode');
    var id = $(this).data('customerid');
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
        $(`#list-mail${id}`).html(responseData);
      }
    });
  });

  // ------------------ Audit Trail script Start----------------------------
  $(document).on("click", ".auditTrailCustomer", function() {
    var ccode = $(this).data('ccode');
    var id = $(this).data('ids');
    // alert(ccode);
    $.ajax({
      url: 'ajaxs/audittrail/ajax-audit-trail-customer.php?auditTrailBodyContent', // <-- point to server-side PHP script 
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
        $(`.auditTrailBodyContentCustomer${ccode}`).html(responseData);
      }
    });
  });

  $(document).on("click", ".auditTrailBodyContentLineCustomer", function() {
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
      url: 'ajaxs/audittrail/ajax-audit-trail-customer.php?auditTrailBodyContentLine', // <-- point to server-side PHP script 
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

<script>
  //  $(document).on('keyup', '.customer_pan_without_gst', function() {
  //     alert(1);

  //   });

  $(document).on('keyup', '.customer_pan_without_gst', function() {
    //alert(1);

    var pan = $('.customer_pan').val();
    //alert(pan);

    $.ajax({
      url: `ajaxs/customer/ajax-customer-pan.php`,
      type: 'POST',
      data: {
        pan: pan

      },
      beforeSend: function() {

      },
      success: function(response) {
        // alert(response);
        if (response > 0) {

          $('#pan_error').html('pan already exists');

          document.getElementById("customerRegFrmNextBtn").disabled = true;

        } else {
          $('#pan_error').html(``);
          document.getElementById("customerRegFrmNextBtn").disabled = false;
        }

      }

    });


  });
</script>


<script>
  am4core.ready(function() {

    // Themes begin
    am4core.useTheme(am4themes_animated);
    // Themes end

    var chart = am4core.create(`.chartDiv3DPieChart`, am4charts.PieChart3D);
    chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
    chart.logo.disabled = true;

    chart.legend = new am4charts.Legend();

    chart.data = [{
        country: "Lithuania",
        litres: 501.9
      },
      {
        country: "Czech Republic",
        litres: 301.9
      },
      {
        country: "Ireland",
        litres: 201.1
      },
      {
        country: "Germany",
        litres: 165.8
      },
      {
        country: "Australia",
        litres: 139.9
      },
      {
        country: "Austria",
        litres: 128.3
      },
      {
        country: "UK",
        litres: 99
      },
      {
        country: "Belgium",
        litres: 60
      },
      {
        country: "The Netherlands",
        litres: 50
      }
    ];

    var series = chart.series.push(new am4charts.PieSeries3D());
    series.dataFields.value = "litres";
    series.dataFields.category = "country";

  });
</script>



<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>

<script src="<?= BASE_URL; ?>public/validations/customerValidation.js"></script>





<script>
  $(document).on("click", ".creditNoteBtn", function() {

    let cust_id = $(this).data('custid');
    console.log("button clicked by creditnote.... ", cust_id);
    $.ajax({
      type: "GET",
      url: `ajaxs/credit-note/ajax-customer-vendor-creditnote.php`,
      data: {
        act: "credit-note",
        id: cust_id,
        creditorsType: "customer"
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


  $(document).on("click", ".debitNoteBtn", function() {
    let vid = $(this).data('vid');
    console.log("button clicked. debitnote... ", vid);
    $.ajax({
      type: "GET",
      url: `ajaxs/debit-note/ajax-customer-vendor-debitnote.php`,
      data: {
        act: "debit-note",
        id: vid,
        creditorsType: "customer"
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
</script>