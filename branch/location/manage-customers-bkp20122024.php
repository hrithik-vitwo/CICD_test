 <?php
  require_once("../../app/v1/connection-branch-admin.php");
  // administratorLocationAuth();
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
                        // $res = $dbCon->query($sql);
                        // $row = $res->fetch_assoc();
                        $res=queryGet($sql);
                        $row=$res['data'];

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
  ?>
   <!-- Content Wrapper. Contains page content -->
   <div class="content-wrapper is-customer">
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
                   <h3 class="card-title">Manage Customer</h3>
                   <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
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
                     <div class="col-lg-1 col-md-1 col-sm-12">
                       <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                     </div>
                     <div class="col-lg-11 col-md-11 col-sm-12">
                       <div class="row table-header-item">
                         <div class="col-lg-11 col-md-11 col-sm-12">
                           <div class="filter-search">
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
                             <h5 class="modal-title" id="exampleModalLongTitle">Filter Customer</h5>

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

                    $sts = " AND `customer_status` !='deleted'";
                    if (isset($_REQUEST['customer_status_s']) && $_REQUEST['status_s'] != '') {
                      $sts = ' AND customer_status="' . $_REQUEST['status_s'] . '"';
                    }

                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                      $cond .= " AND customer_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                    }

                    if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                      $cond .= " AND (`customer_code` like '%" . $_REQUEST['keyword'] . "%' OR `trade_name` like '%" . $_REQUEST['keyword'] . "%' OR `customer_gstin` like '%" . $_REQUEST['keyword'] . "%')";
                    }


                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                      $cond .= " AND (`customer_code` like '%" . $_REQUEST['keyword2'] . "%' OR `trade_name` like '%" . $_REQUEST['keyword2'] . "%' OR `customer_gstin` like '%" . $_REQUEST['keyword2'] . "%')";
                    } else {
                      if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                        $cond .= " AND (`customer_code` like '%" . $_REQUEST['keyword'] . "%' OR `trade_name` like '%" . $_REQUEST['keyword'] . "%' OR `customer_gstin` like '%" . $_REQUEST['keyword'] . "%')";
                      }
                    }


                    $sql_list = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE 1 " . $cond . "  AND company_id='" . $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] . "' " . $sts . "  ORDER BY customer_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                    $qry_list = mysqli_query($dbCon, $sql_list);
                    $num_list = mysqli_num_rows($qry_list);


                    $countShow = "SELECT count(*) FROM `" . ERP_CUSTOMER . "` WHERE 1 " . $cond . " AND company_id='" . $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] . "' " . $sts . " ";
                    $countQry = mysqli_query($dbCon, $countShow);
                    $rowCount = mysqli_fetch_array($countQry);
                    $count = $rowCount[0];
                    $cnt = $GLOBALS['start'] + 1;
                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_CUSTOMER", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                    $settingsCheckbox = unserialize($settingsCh);
                    if ($num_list > 0) {
                    ?>
                     <table class="table defaultDataTable table-hover text-nowrap">
                       <thead>
                         <tr class="alert-light">
                           <th>#</th>
                           <?php if (in_array(1, $settingsCheckbox)) { ?>
                             <th>Customer Code</th>
                           <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                             <th>Customer Icon</th>
                           <?php }

                            if (in_array(3, $settingsCheckbox)) { ?>
                             <th>Customer Name</th>
                           <?php }

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
                           <?php  }
                            if (in_array(8, $settingsCheckbox)) { ?>

                             <th>Order Volume</th>
                           <?php  }
                            if (in_array(9, $settingsCheckbox)) {
                            ?>
                             <th>Receipt Amount</th>
                           <?php } ?>
                           <th>Status</th>

                           <th>Action</th>
                         </tr>
                       </thead>
                       <tbody>
                         <?php
                          $customerModalHtml = "";
                          while ($row = mysqli_fetch_assoc($qry_list)) {
                            // console($row);
                            $customerId = $row['customer_id'];
                            $customer_code = $row['customer_code'];
                            $customer_authorised_person_name = $row['customer_authorised_person_name'];
                            $customer_authorised_person_designation = $row['customer_authorised_person_designation'];
                            $customer_authorised_person_phone = $row['customer_authorised_person_phone'];
                            $customer_authorised_alt_phone = $row['customer_authorised_alt_phone'];
                            $customer_authorised_person_email = $row['customer_authorised_person_email'];
                            $customer_authorised_alt_email = $row['customer_authorised_alt_email'];
                            $trade_name = $row['trade_name'];
                            $ordercustomer = "SELECT SUM( IF( invoiceStatus = '4', all_total_amt, 0 ) ) AS sentInvoiceAmount FROM erp_branch_sales_order_invoices WHERE `customer_id`=$customerId";
                            $getorder = queryGet($ordercustomer, true);
                            // console($getorder['data'][0]['sentInvoiceAmount']);
                            $ordervol = "SELECT * FROM erp_branch_sales_order_invoices WHERE `customer_id`=$customerId";
                            $getvol = queryGet($ordervol);
                            // console($getvol['numRows']);
                          ?>
                           <tr>
                             <td><?= $cnt++ ?></td>
                             <?php if (in_array(1, $settingsCheckbox)) { ?>
                               <td><?= $row['customer_code'] ?></td>
                               <?php }

                              if (in_array(2, $settingsCheckbox)) {
                                if ($row['customer_picture'] != "") { ?>
                                 <td><?= $row['customer_picture'] ?></td>
                               <?php
                                } else {
                                ?>
                                 <td>
                                   <div class="flex-display">

                                     <div id="profileImage"> <?php echo ucfirst(substr($row['trade_name'], 0, 1)) ?></div>
                                   </div>
                                 </td>
                               <?php }
                              }
                              if (in_array(3, $settingsCheckbox)) { ?>
                               <td><?= $row['trade_name'] ?></td>
                             <?php }
                              if (in_array(4, $settingsCheckbox)) { ?>
                               <td><?= $row['constitution_of_business'] ?></td>
                             <?php }
                              if (in_array(5, $settingsCheckbox)) { ?>
                               <td><?= $row['customer_gstin'] ?></td>
                             <?php }
                              if (in_array(6, $settingsCheckbox)) { ?>
                               <td><?= $row['customer_authorised_person_email'] ?></td>
                             <?php }
                              if (in_array(7, $settingsCheckbox)) { ?>
                               <td><?= $row['customer_authorised_person_phone'] ?></td>
                             <?php }
                              if (in_array(8, $settingsCheckbox)) { ?>
                               <td><?= $getvol['numRows']   ?></td>
                             <?php }
                              if (in_array(9, $settingsCheckbox)) { ?>
                               <td><?= decimalValuePreview($getorder['data'][0]['sentInvoiceAmount']) ?></td>
                             <?php } ?>
                             <td>

                               <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                                 <input type="hidden" name="id" value="<?php echo $row['customer_id'] ?>">
                                 <input type="hidden" name="changeStatus" value="active_inactive">
                                 <button <?php if ($row['customer_status'] == "draft") { ?> type="button" style="cursor: inherit; border:none" <?php } else { ?>type="submit" onclick="return confirm('Are you sure change customer_status?')" <?php } ?> class="btn btn-sm" data-toggle="tooltip" data-placement="top" title="<?php echo $row['customer_status'] ?>">
                                   <?php if ($row['customer_status'] == "active") { ?>
                                     <div class="status"><?php echo ucfirst($row['customer_status']); ?></div>
                                   <?php } else if ($row['customer_status'] == "inactive") { ?>
                                     <p class="status-danger"><?php echo ucfirst($row['customer_status']); ?></p>
                                   <?php } else if ($row['customer_status'] == "draft") { ?>
                                     <p class="status-warning"><?php echo ucfirst($row['customer_status']); ?></p>
                                   <?php } ?>

                                 </button>
                               </form>

                             </td>
                             <td>
                               <a style="cursor: pointer;" data-toggle="modal" id="customerID_<?= $row['customer_id'] ?>" data-target="#fluidModalRightSuccessDemo_<?= $row['customer_id'] ?>" class="btn btn-sm customerID"><i class="fa fa-eye po-list-icon"></i></a>
                               <!-- right modal start here  -->
                               <div class="modal fade right customer-modal" id="fluidModalRightSuccessDemo_<?= $row['customer_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                 <div class="modal-dialog modal-right modal-notify modal-success" role="document">

                                   <!--Content-->
                                   <div class="modal-content">
                                     <!--Header-->
                                     <div class="modal-header pt-4">
                                       <div class="row customer-head-row mb-4">
                                         <div class="col-lg-8 col-md-8 col-sm-8 left-info">
                                           <p class="heading lead text-sm text-uppercase font-bold mb-2 mt-2" title="<?= $trade_name ?>"><?= $trade_name ?></p>
                                           <p class="text-xs mt-2"><?= $row['constitution_of_business'] ?></p>
                                           <p class="text-xs text-uppercase mt-2">Code : <?php echo  $customer_code; ?></p>
                                           <p class="text-xs text-uppercase mt-2">GSTIN : <?= $row['customer_gstin'] ?></p>
                                         </div>
                                         <div class="col-lg-4 col-md-4 col-sm-4 right-info">
                                           <p class="text-sm font-bold text-uppercase mt-2"><ion-icon name="person-outline"></ion-icon><?= $customer_authorised_person_name ?></p>
                                           <p class="text-xs font-italic mt-2"><ion-icon name="document-outline"></ion-icon><?= $customer_authorised_person_designation ?></p>
                                           <div class="d-flex text-xs phone-alt-number mt-2">
                                             <!-- <p class="text-sm"><a href="tel:<?= $customer_authorised_person_phone ?>"><ion-icon name="call-outline"></ion-icon><?= $customer_authorised_person_phone ?></a></p> / <p class="text-xs text-right"><a href="tel:<?= $customer_authorised_alt_phone ?>"><?= $customer_authorised_alt_phone ?></a></p> -->
                                             <?php if (empty($customer_authorised_alt_phone)) { ?>
                                               <a href="tel:<?= $customer_authorised_person_phone ?>"><ion-icon name="call-outline"></ion-icon><?= $customer_authorised_person_phone ?></a>
                                             <?php } else { ?>
                                               <a href="tel:<?= $customer_authorised_person_phone ?>"><ion-icon name="call-outline"></ion-icon><?= $customer_authorised_person_phone ?>/<?= $vendor_authorised_alt_phone ?></a>
                                             <?php } ?>
                                           </div>
                                           <div class="d-flex text-xs email-alt mt-2">
                                             <!-- <p class="text-sm"><a href="mailto:<?= $customer_authorised_person_email ?>"><ion-icon name="mail-outline"></ion-icon><?= $customer_authorised_person_email ?></a></p>/ <p class="text-xs text-right"><a href="mailto:<?= $customer_authorised_alt_email ?>"><?= $customer_authorised_alt_email ?></a></p> -->
                                             <?php if (empty($customer_authorised_alt_email)) { ?>
                                               <a href="mailto:<?= $customer_authorised_person_email ?>"><ion-icon name="mail-outline"></ion-icon><?= $customer_authorised_person_email ?></a>
                                             <?php } else { ?>
                                               <a href="mailto:<?= $customer_authorised_person_email ?>"><ion-icon name="mail-outline"></ion-icon><?= $customer_authorised_person_email ?>/<?= $vendor_authorised_alt_email ?></a>
                                             <?php } ?>
                                           </div>
                                         </div>
                                       </div>
                                       <div class="display-flex-space-between mb-3">
                                         <ul class="nav nav-tabs desktop-tab" id="myTab" role="tablist">
                                           <li class="nav-item">
                                             <a class="nav-link active" id="home-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#home<?= $row['customer_id'] ?>" role="tab" aria-controls="home<?= $row['customer_id'] ?>" aria-selected="true"><ion-icon name="information-outline" class="mr-2"></ion-icon>Overview</a>
                                           </li>
                                           <li class="nav-item">
                                             <a class="nav-link" id="transaction-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#transaction<?= $row['customer_id'] ?>" role="tab" aria-controls="transaction<?= $row['customer_id'] ?>" aria-selected="true" class="mr-2"><ion-icon name="repeat-outline" class="mr-2"></ion-icon>Transactions</a>
                                           </li>
                                           <!-- <li class="nav-item">
                                             <a class="nav-link mailtabbtn" data-customerid="<?= $row['customer_id'] ?>" data-customercode="<?= $row['customer_code'] ?>" id="mail-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#mail<?= $row['customer_id'] ?>" role="tab" aria-controls="mail<?= $row['customer_id'] ?>" aria-selected="true"><ion-icon name="mail-outline" class="mr-2"></ion-icon>Mails</a>
                                           </li> -->
                                           <li class="nav-item">
                                             <a class="nav-link" id="statement-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#statement<?= $row['customer_id'] ?>" role="tab" aria-controls="statement<?= $row['customer_id'] ?>" aria-selected="true"><ion-icon name="document-text-outline" class="mr-2"></ion-icon>Statement</a>
                                           </li>
                                           <li class="nav-item">
                                             <a class="nav-link" id="compliance-tab<?= $row['customer_gstin'] ?>" data-toggle="tab" href="#compliance<?= $row['customer_gstin'] ?>" role="tab" aria-controls="compliance<?= $row['customer_gstin'] ?>" aria-selected="true"><ion-icon name="document-text-outline" class="mr-2"></ion-icon>Compliance Status</a>
                                           </li>
                                           <li class="nav-item">
                                             <a class="nav-link" id="reconciliation-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#reconciliation<?= $row['customer_code'] ?>" role="tab" aria-controls="reconciliation<?= $row['customer_id'] ?>" aria-selected="true"><ion-icon name="document-text-outline" class="mr-2"></ion-icon>Reconciliation</a>
                                           </li>

                                           <!-- -------------------Audit History Button Start------------------------- -->
                                           <li class="nav-item">
                                             <a class="nav-link auditTrailCustomer" id="history-tab<?= $row['customer_id'] ?>" data-toggle="tab" data-ccode="<?= $row['customer_code'] ?>" data-ids="<?= $row['customer_id'] ?>" href="#history<?= $row['customer_id'] ?>" role="tab" aria-controls="history<?= $row['customer_id'] ?>" aria-selected="false"><ion-icon name="time-outline" class="mr-2"></ion-icon>Trail</a>
                                           </li>

                                           <!-- -------------------Audit History Button End------------------------- -->
                                         </ul>

                                         <ul class="nav nav-tabs mobile-tab" id="mobileTab" role="tablist">
                                           <li class="nav-item dropdown">
                                             <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                               Select Tab
                                             </a>
                                             <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                               <a class="dropdown-item" data-toggle="tab" href="#home<?= $row['customer_id'] ?>"><ion-icon name="information-outline" class="mr-2"></ion-icon>Overview</a>
                                               <a class="dropdown-item" data-toggle="tab" href="#transaction<?= $row['customer_id'] ?>"><ion-icon name="repeat-outline" class="mr-2"></ion-icon>Transactions</a>
                                               <a class="dropdown-item" data-toggle="tab" href="#mail<?= $row['customer_id'] ?>" data-customerid="<?= $row['customer_id'] ?>" data-customercode="<?= $row['customer_code'] ?>"><ion-icon name="mail-outline" class="mr-2"></ion-icon>Mails</a>
                                               <a class="dropdown-item" data-toggle="tab" href="#statement<?= $row['customer_id'] ?>"><ion-icon name="document-text-outline" class="mr-2"></ion-icon>Statement</a>
                                               <a class="dropdown-item" data-toggle="tab" href="#compliance<?= $row['customer_gstin'] ?>"><ion-icon name="document-text-outline" class="mr-2"></ion-icon>Compliance Status</a>
                                               <a class="dropdown-item" data-toggle="tab" href="#reconciliation<?= $row['customer_code'] ?>"><ion-icon name="document-text-outline" class="mr-2"></ion-icon>Reconciliation</a>
                                               <a class="dropdown-item auditTrailCustomer" data-toggle="tab" href="#history<?= $row['customer_id'] ?>" data-ccode="<?= $row['customer_code'] ?>" data-ids="<?= $row['customer_id'] ?>"><ion-icon name="time-outline" class="mr-2"></ion-icon>Trail</a>
                                             </div>
                                           </li>
                                         </ul>





                                         <!-- <form action="" method="POST">
                                          <div class="hamburger">
                                            <div class="wrapper-action">
                                              <ion-icon name="settings"></ion-icon>
                                            </div>
                                          </div>
                                          <div class="nav-action" id="reminder">
                                            <a title="Mail the customer" href="#" name="vendorReminerBtn">
                                              <ion-icon name="notifications"></ion-icon>
                                            </a>
                                          </div>
                                          <div class="nav-action" id="edit">
                                            <a title="Mail the customer" href="#" name="vendorEditBtn">
                                              <ion-icon name="create"></ion-icon>
                                            </a>
                                          </div>
                                          <div class="nav-action bg-danger" id="thumb">
                                            <a title="Chat the customer" href="#" name="vendorEditBtn">
                                              <ion-icon name="trash"></ion-icon>
                                            </a>
                                          </div>
                                          <div class="nav-action" id="create">
                                            <a title="Call the customer" href="#" name="vendorEditBtn">
                                              <ion-icon name="toggle"></ion-icon>
                                            </a>
                                          </div>
                                        </form> -->
                                         <div class="action-btns display-flex-gap" id="action-navbar">
                                           <?php $customer_id = base64_encode($row['customer_id']) ?>
                                           <form action="" method="POST">
                                             <!-- <a href="#" name="customerRemindBtn">
                                          <ion-icon name="notifications"></ion-icon>
                                        </a> -->
                                             <a href="manage-customers.php?edit=<?= $customer_id ?>" name="customerEditBtn">
                                               <ion-icon name="create"></ion-icon>
                                             </a>
                                             <!-- <a href="manage-customers.php?delete=<?= $customer_id ?>">
                                          <ion-icon name="trash"></ion-icon>
                                        </a>  -->

                                           </form>
                                         </div>
                                       </div>
                                     </div>
                                     <!--Body-->
                                     <div class="modal-body blur-body p-4" style="width: 100%;">
                                       <div class="white-scroll-space">
                                         <div class="tab-content pt-0" id="myTabContent">
                                           <div class="tab-pane fade show active" id="home<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="home-tab">

                                             <div class="row px-3 p-0 m-0">

                                               <!---------CHART_ONLY------->

                                               <div class="col-lg-12 col-md-12 col-xs-12">
                                                 <div class="card flex-fill bg-transparent">
                                                   <div class="card-header p-0 border-bottom-0">
                                                     <!-- <h5 class="card-title chartDivSalesVsCollection_<?= $row['customer_id'] ?>"></h5> -->
                                                     <h5 class="card-title text-nowrap pl-3">Chart View</h5>

                                                     <div id="containerThreeDot">

                                                       <div id="menu-wrap">
                                                         <input type="checkbox" class="toggler bg-transparent" />
                                                         <div class="dots">
                                                           <div></div>
                                                         </div>
                                                         <?php
                                                          $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);

                                                          // $currentDate = date('Y-m-d');
                                                          // $sql = queryGet("SELECT * FROM erp_year_variant WHERE '$currentDate' BETWEEN year_start AND year_end AND `company_id` = $company_id");

                                                          // $id = $sql['data']['year_variant_id'];

                                                          ?>
                                                         <div class="menu ">
                                                           <div>
                                                             <ul>
                                                               <li>
                                                                 <select name="fYDropdown" id="fYDropdown_<?= $row['customer_id'] ?>" data-attr="<?= $row['customer_id'] ?> " class="form-control fYDropdown">
                                                                   <!-- <option value="">--Select FY--</option> -->
                                                                   <?php
                                                                    foreach ($variant_sql['data'] as $key => $data) {
                                                                    ?>
                                                                     <option value="<?= $data['year_variant_id'] ?>"><?= $data['year_variant_name'] ?></option>

                                                                     <!-- <option value="<?= $data['year_variant_id'] ?>" <?php if ($data['year_variant_id'] = $id) {
                                                                                                                            echo "selected";
                                                                                                                          } ?>><?= $data['year_variant_name'] ?></option> -->
                                                                   <?php
                                                                    }
                                                                    ?>
                                                                 </select>
                                                               </li>
                                                               <li><label class="mb-0" for="">OR</label></li>
                                                               <li>
                                                                 <input type="month" name="monthRange" id="monthRange_<?= $row['customer_id'] ?>" data-attr="<?= $row['customer_id'] ?> " class="form-control monthRange" style="max-width: 100%;" />
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
                                                     <div id="chartDivSalesVsCollection_<?= $row['customer_id'] ?>" class="chartContainer">

                                                     </div>
                                                   </div>
                                                 </div>
                                               </div>

                                               <!---------CHART_ONLY------->

                                               <div class="col-lg-6 col-md-6 col-xs-12">
                                                 <!---Address Details---->
                                                 <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                   <div class="accordion-item">
                                                     <h2 class="accordion-header" id="flush-headingOne">
                                                       <button class="accordion-button btn btn-primary collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#addressDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                         Address Details
                                                       </button>
                                                     </h2>
                                                     <div id="addressDetails" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                       <div class="accordion-body p-0">
                                                         <?php
                                                          $sql_addrress = queryGet("SELECT * FROM " . ERP_CUSTOMER_ADDRESS . " WHERE customer_id='" . $row['customer_id'] . "' AND customer_address_primary_flag=1");
                                                          //  $res_addrress = queryGet($sql_addrress);
                                                          // if ($res_addrress['status'] == 'success') {
                                                          $rowAddress = $sql_addrress['data'];
                                                          // foreach ($sql_addrress['data'] as $rowAddress) {

                                                          ?>
                                                         <h1></h1>
                                                         <div class="card bg-transparent">
                                                           <div class="card-body p-3">
                                                             <div class="display-flex-space-between">
                                                               <p class="font-bold text-xs">State :</p>
                                                               <p class="font-bold text-xs"><?= $rowAddress['customer_address_state'] ?></p>
                                                             </div>
                                                             <div class="display-flex-space-between">
                                                               <p class="font-bold text-xs">City :</p>
                                                               <p class="font-bold text-xs"><?= $rowAddress['customer_address_city'] ?></p>
                                                             </div>
                                                             <div class="display-flex-space-between">
                                                               <p class="font-bold text-xs">District :</p>
                                                               <p class="font-bold text-xs"><?= $rowAddress['customer_address_district'] ?></p>
                                                             </div>
                                                             <div class="display-flex-space-between">
                                                               <p class="font-bold text-xs">Location :</p>
                                                               <p class="font-bold text-xs"><?= $rowAddress['customer_address_location'] ?></p>
                                                             </div>
                                                             <div class="display-flex-space-between">
                                                               <p class="font-bold text-xs">Building Number :</p>
                                                               <p class="font-bold text-xs w-75"><?= $rowAddress['customer_address_building_no'] ?></p>
                                                             </div>
                                                             <div class="display-flex-space-between">
                                                               <p class="font-bold text-xs">Flat Number :</p>
                                                               <p class="font-bold text-xs w-75"><?= $rowAddress['customer_address_flat_no'] ?></p>
                                                             </div>
                                                             <div class="display-flex-space-between">
                                                               <p class="font-bold text-xs">Street Name :</p>
                                                               <p class="font-bold text-xs w-75"><?= $rowAddress['customer_address_street_name'] ?></p>
                                                             </div>
                                                             <div class="display-flex-space-between">
                                                               <p class="font-bold text-xs">PIN Code :</p>
                                                               <p class="font-bold text-xs w-75"><?= $rowAddress['customer_address_pin_code'] ?></p>
                                                             </div>
                                                           </div>
                                                         </div>
                                                         <?php
                                                          //  }
                                                          // } else {
                                                          //   echo "Data not found";
                                                          // }
                                                          ?>
                                                       </div>
                                                     </div>
                                                   </div>
                                                 </div>
                                               </div>


                                               <!---------Souvik work for chart------->



                                               <!---------CHART_ONLY------->

                                               <div class="col-lg-6 col-md-6 col-xs-8">
                                                 <div class="card flex-fill bg-transparent">
                                                   <div class="card-header">

                                                     <h5 class="card-title text-nowrap pl-3">Recievables Ageing</h5>

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
                                                                 <select name="piefYDropdown" id="piefYDropdown_<?= $row['customer_id'] ?>" data-attr="<?= $row['customer_id'] ?> " class="form-control piefYDropdown">
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
                                                     <div id="chartDivReceivableAgeing_<?= $row['customer_id'] ?>" class="pieChartContainer" style="display: none">

                                                     </div>
                                                     <div id="noTransactionFound_<?= $row['customer_id'] ?>" class="text-center py-2" style="display: none;">
                                                       <img src="../../public/assets/gif/no-transaction.gif" width="150" alt="">
                                                       <p>No Transactoin Found</p>
                                                     </div>
                                                   </div>
                                                 </div>
                                               </div>

                                               <!---------CHART_ONLY------->

                                               <!---------Souvik work for chart------->

                                             </div>
                                           </div>

                                           <div class="tab-pane fade" id="transaction<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="transaction-tab">
                                             <div class="row p-3">
                                               <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                                 <ul class="nav nav-pills flex-row justify-content-center" id="experienceTab" role="tablist">
                                                   <li class="nav-item">
                                                     <a class="nav-link active" id="invoices-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#invoices<?= $row['customer_id'] ?>" role="tab" aria-controls="invoices<?= $row['customer_id'] ?>" aria-selected="true">Invoices</a>
                                                   </li>
                                                   <li class="nav-item">
                                                     <a class="nav-link" id="customerPayments-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#customerPayments<?= $row['customer_id'] ?>" role="tab" aria-controls="customerPayments<?= $row['customer_id'] ?>" aria-selected="false">Collections</a>
                                                   </li>
                                                   <li class="nav-item">
                                                     <a class="nav-link" id="estimate-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#estimate<?= $row['customer_id'] ?>" role="tab" aria-controls="estimate<?= $row['customer_id'] ?>" aria-selected="false">Estimates</a>
                                                   </li>
                                                   <li class="nav-item">
                                                     <a class="nav-link" id="salesOrder-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#salesOrder<?= $row['customer_id'] ?>" role="tab" aria-controls="salesOrder<?= $row['customer_id'] ?>" aria-selected="false">Sales Orders</a>
                                                   </li>
                                                   <!-- <li class="nav-item">
                                                    <a class="nav-link" id="daybooks-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#daybooks<?= $row['customer_id'] ?>" role="tab" aria-controls="daybooks<?= $row['customer_id'] ?>" aria-selected="false">daybooks</a>
                                                  </li> -->

                                                   <li class="nav-item">
                                                     <a class="nav-link" id="journals-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#journals<?= $row['customer_id'] ?>" role="tab" aria-controls="journals<?= $row['customer_id'] ?>" aria-selected="false">Journals</a>
                                                   </li>
                                                   <li class="nav-item">
                                                     <a class="nav-link debitNoteBtn" id="debitNote-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#debitNote<?= $row['customer_id'] ?>" role="tab" aria-controls="debitNote<?= $row['customer_id'] ?>" aria-selected="false" data-vid="<?= $row['customer_id'] ?>">Debit Note</a>
                                                   </li>
                                                   <li class="nav-item">
                                                     <a class="nav-link creditNoteBtn" id="creditNote-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#creditNote<?= $row['customer_id'] ?>" data-custid="<?= $row['customer_id'] ?>" role="tab" aria-controls="creditNote<?= $row['customer_id'] ?>" aria-selected="false">Credit Notes</a>
                                                   </li>

                                                 </ul>
                                               </div>
                                               <div class="col-lg-12 col-md-12 col-sm-12">
                                                 <div class="tab-content pl-0" id="experienceTabContent">
                                                   <div class="tab-pane fade show active text-left text-light" id="invoices<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="hoinvoicesme-tab">
                                                     <h3>Invoice
                                                       <a href="direct-create-invoice.php" target="_blank" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                                                     </h3>
                                                     <div class="card bg-transparent">
                                                       <div class="card-body p-0">
                                                         <table>
                                                           <thead>
                                                             <tr>
                                                               <th>Icon
                                                               </th>
                                                               <th>Invoice Number
                                                               </th>
                                                               <th>Amount</th>
                                                               <th>Date</th>
                                                               <th>Due in (day/s)</th>
                                                               <th>Status</th>
                                                             </tr>
                                                           </thead>
                                                           <tbody>
                                                             <?php
                                                              $invoice = queryGet("SELECT * FROM `erp_branch_sales_order_invoices` WHERE `customer_id` = '" . $row['customer_id'] . "'", true);
                                                              // console($invoice);
                                                              foreach ($invoice['data'] as $invoice) {

                                                                $temDueDate = date_create($invoice["invoice_date"]);
                                                                $todayDate = new DateTime(date("Y-m-d"));
                                                                $oneInvDueDays = $todayDate->diff(new DateTime(date_format($temDueDate, "Y-m-d")))->format("%r%a");

                                                                $customerDtls = $customerDetailsObj->getDataCustomerDetails($invoice['customer_id'])['data'][0];
                                                                $customerPic = $customerDtls['customer_picture'];
                                                                $customerName = $customerDtls['trade_name'];

                                                                $customerPicture = '';
                                                                $customer_name = mb_substr($customerName, 0, 1);

                                                                ($customerPic != '') ? ($customerPicture = '<img src="' . BASE_URL . 'public/storage/avatar/' . $customerPic . '" class="img-fluid avatar rounded-circle" alt="">') : ($customerPicture = '<div class="img-fluid avatar rounded-circle d-flex justify-content-center align-items-center" style="border: 1px solid grey;">' . $customer_name . '</div>');

                                                              ?>
                                                               <tr>
                                                                 <td>
                                                                   <p class="company-name mt-1"> <?= $customerPicture ?> </p>
                                                                 </td>
                                                                 <td>
                                                                   <?= $invoice['invoice_no'] ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= $invoice['all_total_amt'] ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= formatDateORDateTime($invoice['invoice_date']) ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= $oneInvDueDays ?>
                                                                 </td>
                                                                 <td>
                                                                   <div class="status-custom w-75 text-secondary">
                                                                     <?php if ($invoice['mailStatus'] == 1) {
                                                                        echo 'SENT <div class="round">
                                                              <ion-icon name="checkmark-sharp"></ion-icon>
                                                            </div>';
                                                                      } elseif ($invoice['mailStatus'] == 2) {
                                                                        echo '<span class="text-primary">VIEW</span> <div class="round text-primary">
                                                              <ion-icon name="checkmark-done-sharp"></ion-icon>
                                                            </div>';
                                                                      } ?>
                                                                   </div>
                                                                   <p class="status-date"><?= formatDateORDateTime($invoice['updated_at']) ?></p>
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

                                                   <div class="tab-pane fade text-left text-light" id="customerPayments<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="customerPayments-tab">
                                                     <h3>Collections
                                                       <a href="#" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                                                     </h3>
                                                     <div class="card bg-transparent">
                                                       <div class="card-body p-0">
                                                         <table>
                                                           <thead>
                                                             <tr>
                                                               <th>Collection Advice</th>
                                                               <th> Transaction Id</th>
                                                               <th>Collection Amount</th>
                                                               <th>Collection Type</th>
                                                               <th>Date</th>
                                                             </tr>
                                                           </thead>
                                                           <tbody>
                                                             <?php
                                                              $collection = queryGet("SELECT * FROM `erp_branch_sales_order_payments_log` as log LEFT JOIN `erp_branch_sales_order_payments` as payment ON log.payment_id = payment.payment_id WHERE log.`customer_id` = '" . $row['customer_id'] . "'", true);

                                                              foreach ($collection['data'] as $collection_data) {
                                                                $paymentType = ($collection_data['payment_type']) === 'pay' ? 'against invoice' : $collection_data['payment_type'];

                                                              ?>
                                                               <tr>
                                                                 <td>
                                                                   <p class="company-name mt-1"><?= $collection_data['payment_advice'] ?></p>
                                                                 </td>
                                                                 <td>
                                                                   <?= $collection_data['transactionId'] ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= $collection_data['payment_amt'] ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= $paymentType ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= $collection_data['created_at'] ?>
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

                                                   <div class="tab-pane fade text-left text-light estimate-tab" id="estimate<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="estimate-tab">
                                                     <h3>Estimates</h3>
                                                     <div class="card bg-transparent">
                                                       <div class="card-body p-0">
                                                         <table>
                                                           <thead>
                                                             <tr>
                                                               <th>Quotation Number</th>
                                                               <th> Total Items</th>
                                                               <th>Total Amount</th>
                                                               <th>Goods Type</th>
                                                               <th>Posting Date</th>
                                                             </tr>
                                                           </thead>
                                                           <tbody>
                                                             <?php
                                                              $estimates = queryGet("SELECT * FROM `erp_branch_quotations`WHERE `customer_id` = '" . $row['customer_id'] . "'", true);
                                                              // console($estimates);

                                                              foreach ($estimates['data'] as $estimates_data) {

                                                              ?>
                                                               <tr>
                                                                 <td>
                                                                   <p class="company-name mt-1"><?= $estimates_data['quotation_no'] ?></p>
                                                                 </td>
                                                                 <td>
                                                                   <?= $estimates_data['totalItems'] ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= $estimates_data['totalAmount'] ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= $estimates_data['goodsType'] ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= formatDateORDateTime($estimates_data['posting_date']) ?>
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

                                                   <div class="tab-pane fade text-left text-light" id="salesOrder<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="salesOrder-tab">
                                                     <h3>Sales Order
                                                       <a href="#" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                                                     </h3>
                                                     <div class="card bg-transparent">
                                                       <div class="card-body p-0">
                                                         <table>
                                                           <thead>
                                                             <tr>
                                                               <th>SO Number</th>
                                                               <th>Customer PO</th>
                                                               <th>Delivery Date</th>
                                                               <th> Total Items</th>
                                                               <th>Status</th>
                                                             </tr>
                                                           </thead>
                                                           <tbody>
                                                             <?php

                                                              $so = queryGet("SELECT * FROM `erp_branch_sales_order` WHERE `customer_id` = '" . $row['customer_id'] . "'", true);

                                                              $so_list = $so['data'];
                                                              foreach ($so_list as $oneSoList) {

                                                                if (fetchStatusMasterByCode($oneSoList['approvalStatus'])['data']['label'] == "open") {
                                                                  $approvalStatus = '<div class="status">OPEN</div>';
                                                                } elseif (fetchStatusMasterByCode($oneSoList['approvalStatus'])['data']['label'] == "pending") {
                                                                  $approvalStatus = '<div class="status-warning">PENDING</div>';
                                                                } elseif (fetchStatusMasterByCode($oneSoList['approvalStatus'])['data']['label'] == "exceptional") {
                                                                  $approvalStatus = '<div class="status-warning">EXCEPTIONAL</div>';
                                                                } elseif (fetchStatusMasterByCode($oneSoList['approvalStatus'])['data']['label'] == "closed") {
                                                                  $approvalStatus = '<div class="status-secondary">CLOSED</div>';
                                                                }

                                                              ?>
                                                               <tr>
                                                                 <td>
                                                                   <p class="company-name mt-1"><?= $oneSoList['so_number'] ?></p>
                                                                 </td>
                                                                 <td>
                                                                   <?= $oneSoList['customer_po_no'] ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= formatDateORDateTime($oneSoList['delivery_date']) ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= $oneSoList['totalItems'] ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= $approvalStatus ?>
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

                                                   <div class="tab-pane fade text-left text-light" id="expenses<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="expenses-tab">
                                                     <h3>Expenses</h3>
                                                     <span class="date-range code-font">Other Details</span>
                                                     <ul class="pt-2">
                                                       <li>The volcano is eruting.</li>
                                                       <li>Everything is on fire.</li>
                                                     </ul>
                                                   </div>



                                                   <div class="tab-pane fade text-left text-light" id="journals<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="journals-tab">
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

                                                              $journal = queryGet("SELECT * FROM `erp_acc_journal` WHERE `party_code`='" . $row['customer_code'] . "' AND `company_id`=$company_id AND `location_id`=$location_id AND `parent_slug`='journal'
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





                                                   <div class="tab-pane fade text-left text-light" id="daybooks<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="daybooks-tab">
                                                     <h3>daybooks</h3>
                                                     <span class="date-range code-font">Other Details</span>

                                                     <div class="card">
                                                       <div class="card-body p-0">
                                                         <table>
                                                           <thead>
                                                             <tr>
                                                               <th>Accounting Document No</th>
                                                               <th>Document Number</th>
                                                               <th>Posting Date</th>
                                                               <th>Delivery Date</th>
                                                               <th> Created By</th>
                                                               <th>Order No</th>
                                                               <th>Order Date</th>
                                                               <th>GL Code</th>
                                                               <th>GL Name</th>
                                                               <th>Transaction Type</th>
                                                               <th>Narration </th>
                                                               <th> Type(Dr/Cr)</th>
                                                               <th>Amount</th>
                                                               <th> Clearing Document No</th>
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
                    journal.journal_status = 'active' AND journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '2022-01-01' AND '2023-12-31' AND journal.party_code='" . $row['customer_code'] . "'
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

                                                              // console($sql['data']);

                                                              foreach ($sql['data'] as $journal) {
                                                              ?>
                                                               <tr>
                                                                 <td>
                                                                   <?= $journal['jv_no'] ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= $journal['documentNo'] ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= formatDateORDateTime($journal['postingDate']); ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= formatDateORDateTime($journal['journal_created_at']) ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= getCreatedByUser($journal['journal_created_by']) ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= $journal['Order_num']  ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= formatDateORDateTime($journal['document_date']) ?>
                                                                 </td>

                                                                 <td>
                                                                   <?= $journal['gl_code'] ?? '-' ?>
                                                                 </td>

                                                                 <td>
                                                                   <?= $journal['gl_label'] ?? '-' ?>
                                                                 </td>

                                                                 <td>
                                                                   <?= $journal['journal_entry_ref'] ?? '-' ?>
                                                                 </td>

                                                                 <td>
                                                                   <?= WordLimiter($journal['remark'], 5) ?>
                                                                 </td>

                                                                 <td>
                                                                   <?= $journal['type'] ?? '-' ?>
                                                                 </td>

                                                                 <td>
                                                                   <?= $journal['Amount'] ?? '-' ?>
                                                                 </td>

                                                                 <td>
                                                                   <?= '-' ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= '-' ?>
                                                                 </td>
                                                                 <td>
                                                                   <?= '-' ?>
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

                                                   <div class="tab-pane fade text-left text-light" id="bills<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="bills-tab">
                                                     <h3>Bills</h3>
                                                     <span class="date-range code-font">Other Details</span>
                                                     <ul class="pt-2">
                                                       <li>The volcano is eruting.</li>
                                                       <li>Everything is on fire.</li>
                                                     </ul>
                                                   </div>

                                                   <div class="tab-pane fade text-left text-light" id="debitNote<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="debitNote-tab">
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

                                                   <div class="tab-pane fade text-left text-light" id="creditNote<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="creditNote-tab">
                                                     <h3>Credit Notes</h3>
                                                     <div class="card bg-transparent">
                                                       <div class="card-body p-0">
                                                         <table>
                                                           <thead>
                                                             <tr>
                                                               <th>Credit Note Number</th>
                                                               <th>Party Code</th>
                                                               <th>Party Name</th>
                                                               <th>Invoice Number</th>
                                                               <th>Amount</th>
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
                                               </div>
                                             </div>
                                           </div>

                                           <!-- <div class="tab-pane mail-tab fade" id="mail<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="mail-tab">
                                             <nav>
                                               <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                                 <a class="nav-item nav-link active" id="list-mail-tab" data-toggle="tab" href="#list-mail<?= $row['customer_id'] ?>" role="tab" aria-controls="list-mail" aria-selected="true">Home</a>
                                                 <a class="nav-item nav-link" id="form-mail-tab" data-toggle="tab" href="#form-mail<?= $row['customer_id'] ?>" role="tab" aria-controls="form-mail" aria-selected="false">Profile</a>
                                               </div>
                                             </nav>
                                             <div class="tab-content" id="nav-tabContent">
                                               <div class="tab-pane fade show active mailtabbody" id="list-mail<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="list-mail-tab">

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
                                               <div class="tab-pane fade" id="form-mail<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="form-mail-tab">
                                                 <div id="columns-container">
                                                   <?php $randVal = rand(0000, 9999); ?>
                                                   <form name="customer-data" action="<?= $_SERVER['PHP_SELF']; ?>" id="myCustomerFormData" method="POST">

                                                     <div class="row appendOperatorDiv" id="appendOperatorDiv_<?= $row['customer_id'] ?>">
                                                       <div class="row">
                                                         <div class="col-lg-5 col-md-5 col-sm-5 col-table">
                                                           <div class="head">
                                                             <h5 class="font-bold text-xs">Days</h5>
                                                           </div>
                                                           <input type="hidden" name="customerMail[<?= $randVal ?>][customer_id]" value="<?= $row['customer_id'] ?>">
                                                           <div class="body">
                                                             <input type="text" name="customerMail[<?= $randVal ?>][shootingDays]" placeholder="Enter days | Number" class="form-control">
                                                           </div>
                                                         </div>
                                                         <div class="col-lg-5 col-md-5 col-sm-5 col-table">
                                                           <div class="head">
                                                             <h5 class="font-bold text-xs">Operator</h5>
                                                           </div>
                                                           <div class="body">
                                                             <select name="customerMail[<?= $randVal ?>][operator]" class="form-control">
                                                               <option value="post_of_invoice_date">Post of Invoice date</option>
                                                               <option value="post_of_due_date">Post of Due date</option>
                                                               <option value="early_of_due_date">Early of Due date</option>
                                                             </select>
                                                           </div>
                                                         </div>
                                                         <div class="col-lg-2 col-md-2 col-sm-2 col-btn">
                                                           <button type="button" class="btn btn-primary" onclick="addMultiOperator(<?= $row['customer_id'] ?>)"><i class="fa fa-plus"></i></button>
                                                         </div>
                                                       </div>
                                                     </div>
                                                     <div class="col-lg-12 col-md-12 col-sm-12 col-btn pl-0 mt-4">
                                                       <button type="submit" name="customerMailShootingSubmitBtn" id="customerMailShootingSubmitBtn" class="btn btn-primary">Submit</button>
                                                     </div>
                                                   </form>
                                                 </div>
                                               </div>
                                             </div>
                                           </div> -->

                                           <div class="tab-pane fade" id="statement<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="statement-tab">
                                             <div class="statement-section" id="statementPrint">
                                               <div class="row state-date-action d-flex justify-content-between justify-content-md-between">
                                                 <div class="col-lg-4 col-md-4 col-sm-6 select-year noprint">
                                                   <select name="" id="dateDrop" class="form-control dateDrop dateDrop_<?= $row['customer_code']  ?>">
                                                     <option>Select Date</option>

                                                     <option value="1" data-val="<?= $row['customer_code'] ?>">This Month</option>
                                                     <option value="CustomSlide" data-attr="<?= $row['customer_code'] ?>">
                                                       Custom
                                                     </option>
                                                   </select>

                                                   <div id="customDateDiv" class="custom-date-div customDateDiv_<?= $row['customer_code'] ?>">
                                                     <div class="date-field">
                                                       <div class="form-input d-flex gap-2">
                                                         <label for="">From Date</label>
                                                         <input type="date" class="form-control from_<?= $row['customer_code'] ?>">
                                                         <label for="">To Date</label>
                                                         <input type="date" class="form-control to_<?= $row['customer_code'] ?>">
                                                         <button type="button" class="btn btn-primary date_apply" data-attr="<?= $row['customer_code'] ?>">Apply</button>
                                                         <button type="button" class="close-btn close-btn_<?= $row['customer_code'] ?>">x</button>
                                                       </div>
                                                     </div>
                                                   </div>



                                                 </div>
                                                 <div class="col-lg-8 col-md-8 col-sm-6 actions-btn">
                                                   <div class="btns">

                                                     <button type="button" class="btn btn-primary print-btn" onclick="exportToExcelStatement()">
                                                       <ion-icon name="document-text-outline"></ion-icon>
                                                       Excel
                                                     </button>

                                                   </div>
                                                 </div>
                                               </div>
                                               <div id="non-printable-content" class="stateTable_<?= $row['customer_code']  ?>">


                                               </div>

                                               <!-- <div id="printable-content" class="print_stateTable_<?= $row['customer_code'] ?>">
                                                          </div> -->
                                               <div id="printable-content" class="print_stateTable_<?= $row['customer_code'] ?>">


                                                 <table class="table defaultDataTable table-nowrap">
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
                                             <!--  -->
                                           </div>


                                           <div class="tab-pane fade text-left text-light reconciliation-tab" id="reconciliation<?= $row['customer_code'] ?>" role="tabpanel" aria-labelledby="reconciliation-tab">
                                             <div class="customrange-section">
                                               <form action="" class="date_<?= $row['customer_code'] ?> custom-Range" id="date">
                                                 <div class="date-range-input d-flex gap-2">
                                                   <div class="form-input">
                                                     <label class="mb-0 text-black" for="">From</label>
                                                     <input type="date" class="form-control from_recon_<?= $row['customer_code'] ?>">
                                                   </div>
                                                   <div class="form-input">
                                                     <label class="mb-0 text-black" for="">To</label>
                                                     <input type="date" class="form-control to_recon_<?= $row['customer_code'] ?>">
                                                   </div>
                                                 </div>
                                                 <button type="button" class="btn btn-primary date_apply_recon" data-attr="<?= $row['customer_code'] ?>">Apply</button>
                                               </form>
                                             </div>
                                             <div class="card bg-transparent">
                                               <div class="card-body p-0">


                                                 <!-- <div id="date" class="date_<?= $row['customer_code'] ?>">
                                                <div class="date-field">
                                                  <div class="form-input d-flex gap-2">
                                                    <label for="">From Date</label>
                                                    <input type="date" class="form-control from_recon_<?= $row['customer_code'] ?>">
                                                    <label for="">To Date</label>
                                                    <input type="date" class="form-control to_recon_<?= $row['customer_code'] ?>">
                                                    <button type="button" class="btn btn-primary date_apply_recon" data-attr="<?= $row['customer_code'] ?>">Apply</button>
                                                  </div>
                                                </div>
                                              </div> -->


                                                 <div id="recon_preview" class="recon_preview_<?= $row['customer_code'] ?>">





                                                 </div>


                                                 <div id="recon_preview" class="recon_preview_first_<?= $row['customer_code'] ?>">





                                                 </div>




                                               </div>



                                             </div>

                                             <p class="recon-note text-sm text-black">Note: All values are in <strong>INR</strong></p>

                                           </div>




                                           <div class="tab-pane fade" id="compliance<?= $row['customer_gstin'] ?>" role="tabpanel" aria-labelledby="compliance-tab">

                                             <div class="card mb-0">
                                               <div class="card-header p-0 rounded mb-2">
                                                 <div class="head p-2">
                                                   <h4>
                                                     <ion-icon name="document-text-outline"></ion-icon>&nbsp; GST Filed Status For GSTR1
                                                   </h4>
                                                 </div>
                                               </div>
                                               <?php
                                                if ($row['customer_gstin'] != "") {
                                                ?>
                                                 <div class="card-body">
                                                   <div class="row">


                                                     <!-- <div class="col-lg-3 col-md-3 col-sm-6 mb-2">
                                                <span class="text-xs font-bold">Last Update&nbsp;</span>
                                                <p id="mdl_gstin_last_update_comp_span<?= $row['customer_gstin'] ?>" class="text-xs">XX/XX/XXXX</p>
                                              </div> -->

                                                   </div>
                                                   <div class="row">
                                                     <div class="col-lg-12 col-md-12 col-sm-12 gst-return-data" id="gstinReturnsDatacomp_Div<?= $row['customer_gstin'] ?>">




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
                                                if ($row['customer_gstin'] != "") {
                                                ?>
                                                 <div class="card-body">
                                                   <div class="row">


                                                     <!-- <div class="col-lg-3 col-md-3 col-sm-6 mb-2">
                                                <span class="text-xs font-bold">Last Update&nbsp;</span>
                                                <p id="mdl_gstin_last_update_comp_span<?= $row['customer_gstin'] ?>" class="text-xs">XX/XX/XXXX</p>
                                              </div> -->

                                                   </div>
                                                   <div class="row">
                                                     <div class="col-lg-12 col-md-12 col-sm-12 gst-return-data" id="gstinReturnsDatacomp3b_Div<?= $row['customer_gstin'] ?>">




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

                                           <div class="tab-pane fade" id="recon<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="recon-tab">

                                             <section class="content">
                                               <div class="container-fluid my-4">
                                                 <div class="row">
                                                   <div class="col-lg-12 col-md-12 col-sm-12">
                                                     Customer Reconciliation
                                                   </div>
                                                 </div>
                                                 <div class="row">
                                                   <div class="col-lg-12 col-md-12 col-sm-12">
                                                     <div class="card bg-transparent">
                                                       <div class="card-body">
                                                         <div class="div w-100 p-0 d-flex justify-content-between">
                                                           <div class="m-1 md-12">
                                                             <p class="text-xs text-grey my-2">Carry Forwared</p>
                                                             <p class="text-md font-bold my-2"><i class="fas fa-rupee-sign"></i> 9,55,000.00</p>
                                                           </div>
                                                           <div style="background-color:#f7f8f9;margin-top: 20px;" class="p-1 md-12">
                                                             <table>
                                                               <tr>
                                                                 <td class="text-left" style="background-color: #f7f8f9!important;">
                                                                   <p>Available ITC (Current month)</p>
                                                                 </td>
                                                                 <td class="pl-2" style="background-color: #f7f8f9!important;">
                                                                   <p style="font-size: 20px;"><i class="fas fa-rupee-sign"></i> 9,55,000.00</p>
                                                                 </td>
                                                               </tr>
                                                               <tr>
                                                                 <td class="text-left" style="background-color: #f7f8f9!important;">
                                                                   <p>Left to recon</p>
                                                                 </td>
                                                                 <td class="pl-2" style="background-color: #f7f8f9!important;">
                                                                   <p style="font-size: 20px;"><i class="fas fa-rupee-sign"></i> 5,00,000.00</p>
                                                                 </td>
                                                               </tr>
                                                             </table>
                                                           </div>
                                                           <div class="m-1 d-flex md-12">
                                                             <div class="align-self-end">
                                                               <button class="btn btn-sm btn-primary mr-3" id="matchTheTableRowBtn"><i class="fa fa-match text-light"></i> Match </button>
                                                               <!-- <button class="btn btn-sm btn-primary mr-3" id="calculateMatchedTableRowBtn"><i class="fa fa-match text-light"></i> Calculate </button> -->
                                                               <button class="btn btn-sm btn-primary mr-3" id="addMatchedRowToBusketBtn"><i class="fa fa-check text-light"></i> Add to List </button>
                                                             </div>
                                                             <div class="align-self-end fs-4 mx-2">
                                                               <p>Reconcile Amount</p>
                                                               <div><span><i class="fas fa-rupee-sign"></i><span class="reconListAmountSpan">0.00</span></span></div>
                                                             </div>
                                                             <div class="align-self-end">
                                                               <!-- <a style="cursor:pointer" data-toggle="modal" data-target="#tempReconListModal" class="btn btn-sm waves-effect waves-light"><i class="fas fa-file po-list-icon"></i></a> -->
                                                               <a style="cursor: pointer;" data-toggle="modal" id="tempReconListModalBtn" data-target="#tempReconListModal"><i class="fas fa-file" style="font-size:65px;"></i></a>
                                                               <span class="badge badge-pill badge-info p-1" style="font-size: 10px!important;" id="reconListCounterSpan"><?= $countPendingReconDataObj["numRows"] ?? 0; ?></span>

                                                             </div>
                                                           </div>
                                                         </div>
                                                       </div>

                                                     </div>
                                                   </div>
                                                 </div>


                                                 <div class="row p-0 m-0 recon-table">
                                                   <div class="col-6 pr-0">
                                                     <p class="text-center">Portal Invoices</p>
                                                     <div id="gstr2aPortalTable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                                       <div class="row">
                                                         <div class="col-sm-12 col-md-6"></div>
                                                         <div class="col-sm-12 col-md-6">
                                                           <div id="gstr2aPortalTable_filter" class="dataTables_filter"><label>Search:<input type="search" class="form-control form-control-sm" placeholder="" aria-controls="gstr2aPortalTable"></label></div>
                                                         </div>
                                                       </div>
                                                       <div class="row">
                                                         <div class="col-sm-12">
                                                           <table class="table gstr2aTable dataTable no-footer" id="gstr2aPortalTable" role="grid">
                                                             <thead>
                                                               <tr role="row">
                                                                 <th class="sorting sorting_asc" tabindex="0" aria-controls="gstr2aPortalTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ITC: activate to sort column descending" style="width: 23.6875px;">ITC</th>
                                                                 <th class="sorting" tabindex="0" aria-controls="gstr2aPortalTable" rowspan="1" colspan="1" aria-label="Date: activate to sort column ascending" style="width: 36.025px;">Date</th>
                                                                 <th style="width: 113.312px;" class="sorting" tabindex="0" aria-controls="gstr2aPortalTable" rowspan="1" colspan="1" aria-label="GSTIN: activate to sort column ascending">GSTIN</th>
                                                                 <th class="sorting" tabindex="0" aria-controls="gstr2aPortalTable" rowspan="1" colspan="1" aria-label="VENDOR NAME: activate to sort column ascending" style="width: 128.312px;">VENDOR NAME</th>
                                                                 <th class="sorting" tabindex="0" aria-controls="gstr2aPortalTable" rowspan="1" colspan="1" aria-label="INVOICE NO: activate to sort column ascending" style="width: 97.9px;">INVOICE NO</th>
                                                                 <th class="sorting" tabindex="0" aria-controls="gstr2aPortalTable" rowspan="1" colspan="1" aria-label="INV AMOUNT: activate to sort column ascending" style="width: 92.9875px;">INV AMOUNT</th>
                                                                 <th class="sorting" tabindex="0" aria-controls="gstr2aPortalTable" rowspan="1" colspan="1" aria-label="TAX AMOUNT: activate to sort column ascending" style="width: 96.175px;">TAX AMOUNT</th>
                                                                 <th style="color: white; background-color: rgb(1, 26, 60) !important; width: 51.375px;" class="sorting" tabindex="0" aria-controls="gstr2aPortalTable" rowspan="1" colspan="1" aria-label="RECON: activate to sort column ascending">RECON</th>
                                                                 <th style="color: white; background-color: rgb(1, 26, 60) !important; width: 53.825px;" class="sorting" tabindex="0" aria-controls="gstr2aPortalTable" rowspan="1" colspan="1" aria-label="MATCH: activate to sort column ascending">MATCH</th>
                                                               </tr>
                                                             </thead>
                                                             <tbody id="portalGstr2bTableBody">




                                                               <tr class="odd matchedRowColor-25">
                                                                 <td class="portalInvoiceItc sorting_1"><i class="fas fa-check"></i></td>
                                                                 <td class="portalInvoiceDate"></td>
                                                                 <td class="portalVendorGstin">19AAMCS8857L1Z9</td>
                                                                 <td class="portalVendorName">SBI GENERAL INS</td>
                                                                 <td class="portalInvoiceNo">84841089</td>
                                                                 <td class="portalInvoiceAmt text-right">47200.00</td>
                                                                 <td class="portalInvoiceTaxAmt text-right">7200.00</td>
                                                                 <td class="reconColumn">
                                                                   <input type="checkbox" name="" id="" class="reconCheckBox">
                                                                 </td>
                                                                 <td class="reconPercentageColumn reconColumn">25%</td>
                                                               </tr>
                                                               <tr class="even matchedRowColor-25">
                                                                 <td class="portalInvoiceItc sorting_1"><i class="fas fa-check"></i></td>
                                                                 <td class="portalInvoiceDate"></td>
                                                                 <td class="portalVendorGstin">06AACCG0527D1Z8</td>
                                                                 <td class="portalVendorName">GOOGLE INDIA PV</td>
                                                                 <td class="portalInvoiceNo">4653340861</td>
                                                                 <td class="portalInvoiceAmt text-right">16107.00</td>
                                                                 <td class="portalInvoiceTaxAmt text-right">2457.00</td>
                                                                 <td class="reconColumn">
                                                                   <input type="checkbox" name="" id="" class="reconCheckBox">
                                                                 </td>
                                                                 <td class="reconPercentageColumn reconColumn">25%</td>
                                                               </tr>
                                                               <tr class="odd matchedRowColor-25">
                                                                 <td class="portalInvoiceItc sorting_1"><i class="fas fa-check"></i></td>
                                                                 <td class="portalInvoiceDate"></td>
                                                                 <td class="portalVendorGstin">06AACCG0527D1Z8</td>
                                                                 <td class="portalVendorName">GOOGLE INDIA PV</td>
                                                                 <td class="portalInvoiceNo">4654258584</td>
                                                                 <td class="portalInvoiceAmt text-right">9961.56</td>
                                                                 <td class="portalInvoiceTaxAmt text-right">1519.56</td>
                                                                 <td class="reconColumn">
                                                                   <input type="checkbox" name="" id="" class="reconCheckBox">
                                                                 </td>
                                                                 <td class="reconPercentageColumn reconColumn">25%</td>
                                                               </tr>
                                                               <tr class="even matchedRowColor-25">
                                                                 <td class="portalInvoiceItc sorting_1"><i class="fas fa-check"></i></td>
                                                                 <td class="portalInvoiceDate"></td>
                                                                 <td class="portalVendorGstin">19AABCZ0038M1Z2</td>
                                                                 <td class="portalVendorName">6 LIVO TECHNOLO</td>
                                                                 <td class="portalInvoiceNo">T/22-23/0130</td>
                                                                 <td class="portalInvoiceAmt text-right">94400.00</td>
                                                                 <td class="portalInvoiceTaxAmt text-right">14400.00</td>
                                                                 <td class="reconColumn">
                                                                   <input type="checkbox" name="" id="" class="reconCheckBox">
                                                                 </td>
                                                                 <td class="reconPercentageColumn reconColumn">25%</td>
                                                               </tr>
                                                             </tbody>
                                                           </table>
                                                         </div>
                                                       </div>
                                                       <div class="row">
                                                         <div class="col-sm-12 col-md-5"></div>
                                                         <div class="col-sm-12 col-md-7"></div>
                                                       </div>
                                                     </div>
                                                   </div>

                                                   <div class="col-6 pl-0">
                                                     <p class="text-center">Local Invoices</p>
                                                     <div id="gstr2aLocalTable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                                       <div class="row">
                                                         <div class="col-sm-12 col-md-6"></div>
                                                         <div class="col-sm-12 col-md-6">
                                                           <div id="gstr2aLocalTable_filter" class="dataTables_filter"><label>Search:<input type="search" class="form-control form-control-sm" placeholder="" aria-controls="gstr2aLocalTable"></label></div>
                                                         </div>
                                                       </div>
                                                       <div class="row">
                                                         <div class="col-sm-12">
                                                           <table class="table gstr2aTable dataTable no-footer" id="gstr2aLocalTable" role="grid">
                                                             <thead>
                                                               <tr role="row">
                                                                 <th class="sorting sorting_asc" tabindex="0" aria-controls="gstr2aLocalTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Date: activate to sort column descending" style="width: 73.4875px;">Date</th>
                                                                 <th class="sorting" tabindex="0" aria-controls="gstr2aLocalTable" rowspan="1" colspan="1" aria-label="GSTIN: activate to sort column ascending" style="width: 114.25px;">GSTIN</th>
                                                                 <th class="sorting" tabindex="0" aria-controls="gstr2aLocalTable" rowspan="1" colspan="1" aria-label="VENDOR NAME: activate to sort column ascending" style="width: 118.662px;">VENDOR NAME</th>
                                                                 <th class="sorting" tabindex="0" aria-controls="gstr2aLocalTable" rowspan="1" colspan="1" aria-label="INVOICE NO: activate to sort column ascending" style="width: 223.788px;">INVOICE NO</th>
                                                                 <th class="sorting" tabindex="0" aria-controls="gstr2aLocalTable" rowspan="1" colspan="1" aria-label="INV AMOUNT: activate to sort column ascending" style="width: 75.775px;">INV AMOUNT</th>
                                                                 <th class="sorting" tabindex="0" aria-controls="gstr2aLocalTable" rowspan="1" colspan="1" aria-label="TAX AMOUNT: activate to sort column ascending" style="width: 78.3875px;">TAX AMOUNT</th>
                                                                 <th class="sorting" tabindex="0" aria-controls="gstr2aLocalTable" rowspan="1" colspan="1" aria-label=": activate to sort column ascending" style="width: 10.85px;"><i class="fas fa-bars"></i></th>
                                                               </tr>
                                                             </thead>
                                                             <tbody id="localGstr2bTableBody" class="ui-sortable">






































































                                                               <tr id="rightRow-69" class="odd ui-sortable-handle matchedRowColor-25" style="">
                                                                 <td class="localInvoiceDate sorting_1">2022-09-23</td>
                                                                 <td class="localVendorGstin">19AABCT1296R1ZK</td>
                                                                 <td class="localVendorName">INGRAM</td>
                                                                 <td class="localInvoiceNo">60ID2200981461</td>
                                                                 <td class="localInvoiceAmt text-right">277394.75</td>
                                                                 <td class="localInvoiceTaxAmt text-right">42314.48</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-65" class="odd ui-sortable-handle matchedRowColor-25">
                                                                 <td class="localInvoiceDate sorting_1">2022-10-19</td>
                                                                 <td class="localVendorGstin">29AADCT7657G1ZV</td>
                                                                 <td class="localVendorName">TECHNOBIND</td>
                                                                 <td class="localInvoiceNo">BLINV-1710/22-23</td>
                                                                 <td class="localInvoiceAmt text-right">132750</td>
                                                                 <td class="localInvoiceTaxAmt text-right">20250</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-66" class="even ui-sortable-handle matchedRowColor-25">
                                                                 <td class="localInvoiceDate sorting_1">2022-10-19</td>
                                                                 <td class="localVendorGstin">29AADCT7657G1ZV</td>
                                                                 <td class="localVendorName">TECHNOBIND</td>
                                                                 <td class="localInvoiceNo">BLINV-1710/22-23</td>
                                                                 <td class="localInvoiceAmt text-right">132750</td>
                                                                 <td class="localInvoiceTaxAmt text-right">20250</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-68" class="odd ui-sortable-handle matchedRowColor-25">
                                                                 <td class="localInvoiceDate sorting_1">2022-10-27</td>
                                                                 <td class="localVendorGstin">18AQGPP8674F1ZA</td>
                                                                 <td class="localVendorName">NORTH EAST INFR</td>
                                                                 <td class="localInvoiceNo">NEI/2022 -23/025</td>
                                                                 <td class="localInvoiceAmt text-right">5569571</td>
                                                                 <td class="localInvoiceTaxAmt text-right">849596</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-37" class="even ui-sortable-handle" style="">
                                                                 <td class="localInvoiceDate sorting_1">2022-11-01</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/22-23IA/0023</td>
                                                                 <td class="localInvoiceAmt text-right">35754</td>
                                                                 <td class="localInvoiceTaxAmt text-right">5454</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-63" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2022-10-31</td>
                                                                 <td class="localVendorGstin">20AATPA8834D1ZY</td>
                                                                 <td class="localVendorName">Cable link</td>
                                                                 <td class="localInvoiceNo">CL/22-23/0081</td>
                                                                 <td class="localInvoiceAmt text-right">13955</td>
                                                                 <td class="localInvoiceTaxAmt text-right">2129</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-3" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2022-11-01</td>
                                                                 <td class="localVendorGstin">07AACCJ3219P1ZB</td>
                                                                 <td class="localVendorName">BHAVNA ENTERPRI</td>
                                                                 <td class="localInvoiceNo">JML/21-23IA/2621</td>
                                                                 <td class="localInvoiceAmt text-right">24337</td>
                                                                 <td class="localInvoiceTaxAmt text-right">3807</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-64" class="even ui-sortable-handle" style="">
                                                                 <td class="localInvoiceDate sorting_1">2022-10-19</td>
                                                                 <td class="localVendorGstin">29AADCT7657G1ZV</td>
                                                                 <td class="localVendorName">TECHNOBIND</td>
                                                                 <td class="localInvoiceNo">BLINV-1710/22-23</td>
                                                                 <td class="localInvoiceAmt text-right">132750</td>
                                                                 <td class="localInvoiceTaxAmt text-right">20250</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-42" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2022-11-01</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/22-23IA/0023</td>
                                                                 <td class="localInvoiceAmt text-right">35754</td>
                                                                 <td class="localInvoiceTaxAmt text-right">5454</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-57" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2022-11-01</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/22-23IA/0023</td>
                                                                 <td class="localInvoiceAmt text-right">35754</td>
                                                                 <td class="localInvoiceTaxAmt text-right">0</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-58" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2022-11-01</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">INV /112211</td>
                                                                 <td class="localInvoiceAmt text-right">132160</td>
                                                                 <td class="localInvoiceTaxAmt text-right">20160</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-59" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2022-11-01</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">INV /112215</td>
                                                                 <td class="localInvoiceAmt text-right">132160</td>
                                                                 <td class="localInvoiceTaxAmt text-right">20160</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-60" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2022-11-01</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/22-23IA/0011</td>
                                                                 <td class="localInvoiceAmt text-right">132160</td>
                                                                 <td class="localInvoiceTaxAmt text-right">20160</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-62" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2022-11-01</td>
                                                                 <td class="localVendorGstin">19AACCJ3219P1Z6</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/22-23GK/0039</td>
                                                                 <td class="localInvoiceAmt text-right">18172</td>
                                                                 <td class="localInvoiceTaxAmt text-right">2772</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-67" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2022-11-01</td>
                                                                 <td class="localVendorGstin">07AACCJ3219P1ZB</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/22-23ID/2521</td>
                                                                 <td class="localInvoiceAmt text-right">24957</td>
                                                                 <td class="localInvoiceTaxAmt text-right">3807</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-35" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-01-06</td>
                                                                 <td class="localVendorGstin">19AABCT6577B1Z7</td>
                                                                 <td class="localVendorName">CLOUDKAPTAN CON</td>
                                                                 <td class="localInvoiceNo">INV900006</td>
                                                                 <td class="localInvoiceAmt text-right">23600000</td>
                                                                 <td class="localInvoiceTaxAmt text-right">3600000</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-27" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-01-09</td>
                                                                 <td class="localVendorGstin">29AAMFD8452H1ZZ</td>
                                                                 <td class="localVendorName">DS Infotech (Au</td>
                                                                 <td class="localInvoiceNo">DSTFI115</td>
                                                                 <td class="localInvoiceAmt text-right">58320.00</td>
                                                                 <td class="localInvoiceTaxAmt text-right">9720</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-24" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-01-10</td>
                                                                 <td class="localVendorGstin">19AAFCC6857M1ZY</td>
                                                                 <td class="localVendorName">Something Pvt. </td>
                                                                 <td class="localInvoiceNo">CSPL/22-23/084</td>
                                                                 <td class="localInvoiceAmt text-right">462000.00</td>
                                                                 <td class="localInvoiceTaxAmt text-right">72000</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-2" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-01-20</td>
                                                                 <td class="localVendorGstin">19AAFCC8614A1ZW</td>
                                                                 <td class="localVendorName">CLOUDKAPTAN CON</td>
                                                                 <td class="localInvoiceNo">INV90001111</td>
                                                                 <td class="localInvoiceAmt text-right">29500</td>
                                                                 <td class="localInvoiceTaxAmt text-right">4500</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-41" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-01-20</td>
                                                                 <td class="localVendorGstin">19AAFCC8614A1ZW</td>
                                                                 <td class="localVendorName">CLOUDKAPTAN CON</td>
                                                                 <td class="localInvoiceNo">INV90001111</td>
                                                                 <td class="localInvoiceAmt text-right">29500</td>
                                                                 <td class="localInvoiceTaxAmt text-right">4500</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-23" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-01-24</td>
                                                                 <td class="localVendorGstin">19AALPT4899D1ZX</td>
                                                                 <td class="localVendorName">N K Enterprise</td>
                                                                 <td class="localInvoiceNo">NKE/07989/21-23</td>
                                                                 <td class="localInvoiceAmt text-right">8850</td>
                                                                 <td class="localInvoiceTaxAmt text-right">1350</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-25" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-01-24</td>
                                                                 <td class="localVendorGstin">19AALPT4899D1ZX</td>
                                                                 <td class="localVendorName">N K Enterprise</td>
                                                                 <td class="localInvoiceNo">NKE/17989/22-23</td>
                                                                 <td class="localInvoiceAmt text-right">8100</td>
                                                                 <td class="localInvoiceTaxAmt text-right">1350</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-29" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-01-26</td>
                                                                 <td class="localVendorGstin">19AAACE3803E1Z2</td>
                                                                 <td class="localVendorName">EMU LINES PVT. </td>
                                                                 <td class="localInvoiceNo">CAL2223DD003887</td>
                                                                 <td class="localInvoiceAmt text-right">7347.00</td>
                                                                 <td class="localInvoiceTaxAmt text-right">2998.98</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-32" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-01-26</td>
                                                                 <td class="localVendorGstin">19AAACE3803E1Z2</td>
                                                                 <td class="localVendorName">EMU LINES PVT. </td>
                                                                 <td class="localInvoiceNo">CAL2223DD003887</td>
                                                                 <td class="localInvoiceAmt text-right">8261.28</td>
                                                                 <td class="localInvoiceTaxAmt text-right">2998.98</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-28" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-01-31</td>
                                                                 <td class="localVendorGstin">27AACCR7740M1ZX</td>
                                                                 <td class="localVendorName">Route Mobile Li</td>
                                                                 <td class="localInvoiceNo">RM/01-23/PP0401</td>
                                                                 <td class="localInvoiceAmt text-right">5302.24</td>
                                                                 <td class="localInvoiceTaxAmt text-right">900.46</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-34" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-01-31</td>
                                                                 <td class="localVendorGstin">19AABCB1232H1Z3</td>
                                                                 <td class="localVendorName">BINARY SOLUTION</td>
                                                                 <td class="localInvoiceNo">BSPL/G2511/22-23
                                                                   BSPL/G2511/22-23</td>
                                                                 <td class="localInvoiceAmt text-right">5841</td>
                                                                 <td class="localInvoiceTaxAmt text-right">891</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-36" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-01-31</td>
                                                                 <td class="localVendorGstin">19AABCB1232H1Z3</td>
                                                                 <td class="localVendorName">BINARY SOLUTION</td>
                                                                 <td class="localInvoiceNo">BSPL/G2510/22-23
                                                                   BSPL/G2510/22-23</td>
                                                                 <td class="localInvoiceAmt text-right">5841</td>
                                                                 <td class="localInvoiceTaxAmt text-right">891</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-43" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-07</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/21-23IA/9846</td>
                                                                 <td class="localInvoiceAmt text-right">89208</td>
                                                                 <td class="localInvoiceTaxAmt text-right">13608</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-44" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-07</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/21-23IA/9846</td>
                                                                 <td class="localInvoiceAmt text-right">89208</td>
                                                                 <td class="localInvoiceTaxAmt text-right">13608</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-45" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-07</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/21-23IA/9846</td>
                                                                 <td class="localInvoiceAmt text-right">89208</td>
                                                                 <td class="localInvoiceTaxAmt text-right">13608</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-46" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-07</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/21-23IA/9846</td>
                                                                 <td class="localInvoiceAmt text-right">89208</td>
                                                                 <td class="localInvoiceTaxAmt text-right">13608</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-47" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-07</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/21-23IA/9846</td>
                                                                 <td class="localInvoiceAmt text-right">89208</td>
                                                                 <td class="localInvoiceTaxAmt text-right">13608</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-48" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-07</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/21-23IA/9846</td>
                                                                 <td class="localInvoiceAmt text-right">89208</td>
                                                                 <td class="localInvoiceTaxAmt text-right">13608</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-49" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-07</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/21-23IA/9846</td>
                                                                 <td class="localInvoiceAmt text-right">89208</td>
                                                                 <td class="localInvoiceTaxAmt text-right">13608</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-50" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-07</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/21-23IA/9846</td>
                                                                 <td class="localInvoiceAmt text-right">89208</td>
                                                                 <td class="localInvoiceTaxAmt text-right">13608</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-51" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-07</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/21-23IA/9846</td>
                                                                 <td class="localInvoiceAmt text-right">89208</td>
                                                                 <td class="localInvoiceTaxAmt text-right">13608</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-52" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-07</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/21-23IA/9846</td>
                                                                 <td class="localInvoiceAmt text-right">89208</td>
                                                                 <td class="localInvoiceTaxAmt text-right">13608</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-53" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-07</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/21-23IA/9846</td>
                                                                 <td class="localInvoiceAmt text-right">89208</td>
                                                                 <td class="localInvoiceTaxAmt text-right">13608</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-54" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-07</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/21-23IA/9846</td>
                                                                 <td class="localInvoiceAmt text-right">89208</td>
                                                                 <td class="localInvoiceTaxAmt text-right">13608</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-55" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-07</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/21-23IA/9846</td>
                                                                 <td class="localInvoiceAmt text-right">89208</td>
                                                                 <td class="localInvoiceTaxAmt text-right">13608</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-56" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-07</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/21-23IA/9846</td>
                                                                 <td class="localInvoiceAmt text-right">89208</td>
                                                                 <td class="localInvoiceTaxAmt text-right">13608</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-61" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-07</td>
                                                                 <td class="localVendorGstin">24AACCJ3219P1ZF</td>
                                                                 <td class="localVendorName">JRS Global Netw</td>
                                                                 <td class="localInvoiceNo">JRS/21-23IA/0001</td>
                                                                 <td class="localInvoiceAmt text-right">89208</td>
                                                                 <td class="localInvoiceTaxAmt text-right">13608</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-11" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-14</td>
                                                                 <td class="localVendorGstin">07AAACR4849R1ZN</td>
                                                                 <td class="localVendorName">Tata Consultanc</td>
                                                                 <td class="localInvoiceNo">INV/15022023/03</td>
                                                                 <td class="localInvoiceAmt text-right">51740</td>
                                                                 <td class="localInvoiceTaxAmt text-right">0</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-12" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-14</td>
                                                                 <td class="localVendorGstin">07AAACR4849R1ZN</td>
                                                                 <td class="localVendorName">Tata Consultanc</td>
                                                                 <td class="localInvoiceNo">INV/15022023/02</td>
                                                                 <td class="localInvoiceAmt text-right">51740</td>
                                                                 <td class="localInvoiceTaxAmt text-right">0</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-13" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-14</td>
                                                                 <td class="localVendorGstin">07AAACR4849R1ZN</td>
                                                                 <td class="localVendorName">Tata Consultanc</td>
                                                                 <td class="localInvoiceNo">INV/15022023/01</td>
                                                                 <td class="localInvoiceAmt text-right">51740</td>
                                                                 <td class="localInvoiceTaxAmt text-right">0</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-14" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-14</td>
                                                                 <td class="localVendorGstin">07AAACR4849R1ZN</td>
                                                                 <td class="localVendorName">Tata Consultanc</td>
                                                                 <td class="localInvoiceNo">INV/15022023/03</td>
                                                                 <td class="localInvoiceAmt text-right">51740</td>
                                                                 <td class="localInvoiceTaxAmt text-right">0</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-15" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-14</td>
                                                                 <td class="localVendorGstin">07AAACR4849R1ZN</td>
                                                                 <td class="localVendorName">Tata Consultanc</td>
                                                                 <td class="localInvoiceNo">INV/15022023/02</td>
                                                                 <td class="localInvoiceAmt text-right">51740</td>
                                                                 <td class="localInvoiceTaxAmt text-right">0</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-16" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-14</td>
                                                                 <td class="localVendorGstin">07AAACR4849R1ZN</td>
                                                                 <td class="localVendorName">Tata Consultanc</td>
                                                                 <td class="localInvoiceNo">INV/15022023/01</td>
                                                                 <td class="localInvoiceAmt text-right">51740</td>
                                                                 <td class="localInvoiceTaxAmt text-right">0</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-17" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-14</td>
                                                                 <td class="localVendorGstin">07AAACR4849R1ZN</td>
                                                                 <td class="localVendorName">Tata Consultanc</td>
                                                                 <td class="localInvoiceNo">INV/15022023/03</td>
                                                                 <td class="localInvoiceAmt text-right">51740</td>
                                                                 <td class="localInvoiceTaxAmt text-right">0</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-18" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-14</td>
                                                                 <td class="localVendorGstin">07AAACR4849R1ZN</td>
                                                                 <td class="localVendorName">Tata Consultanc</td>
                                                                 <td class="localInvoiceNo">INV/15022023/02</td>
                                                                 <td class="localInvoiceAmt text-right">51740</td>
                                                                 <td class="localInvoiceTaxAmt text-right">0</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-19" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-14</td>
                                                                 <td class="localVendorGstin">07AAACR4849R1ZN</td>
                                                                 <td class="localVendorName">Tata Consultanc</td>
                                                                 <td class="localInvoiceNo">INV/15022023/01</td>
                                                                 <td class="localInvoiceAmt text-right">51740</td>
                                                                 <td class="localInvoiceTaxAmt text-right">0</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-38" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-14</td>
                                                                 <td class="localVendorGstin">07AAACR4849R1ZN</td>
                                                                 <td class="localVendorName">Tata Consultanc</td>
                                                                 <td class="localInvoiceNo">INV/15022023/03</td>
                                                                 <td class="localInvoiceAmt text-right">51740</td>
                                                                 <td class="localInvoiceTaxAmt text-right">0</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-39" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-14</td>
                                                                 <td class="localVendorGstin">07AAACR4849R1ZN</td>
                                                                 <td class="localVendorName">Tata Consultanc</td>
                                                                 <td class="localInvoiceNo">INV/15022023/02</td>
                                                                 <td class="localInvoiceAmt text-right">51740</td>
                                                                 <td class="localInvoiceTaxAmt text-right">0</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-40" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-14</td>
                                                                 <td class="localVendorGstin">07AAACR4849R1ZN</td>
                                                                 <td class="localVendorName">Tata Consultanc</td>
                                                                 <td class="localInvoiceNo">INV/15022023/01</td>
                                                                 <td class="localInvoiceAmt text-right">51740</td>
                                                                 <td class="localInvoiceTaxAmt text-right">0</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-30" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-22</td>
                                                                 <td class="localVendorGstin">19AAFCC8614A1ZW</td>
                                                                 <td class="localVendorName">CLOUDKAPTAN CON</td>
                                                                 <td class="localInvoiceNo">INV2023/104</td>
                                                                 <td class="localInvoiceAmt text-right">283200</td>
                                                                 <td class="localInvoiceTaxAmt text-right">43200</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-31" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-22</td>
                                                                 <td class="localVendorGstin">19AAFCC8614A1ZW</td>
                                                                 <td class="localVendorName">CLOUDKAPTAN CON</td>
                                                                 <td class="localInvoiceNo">INV2023/104</td>
                                                                 <td class="localInvoiceAmt text-right">283200</td>
                                                                 <td class="localInvoiceTaxAmt text-right">43200</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-26" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-23</td>
                                                                 <td class="localVendorGstin">19AADCE3321L1ZN</td>
                                                                 <td class="localVendorName">ENCODERS TECHNO</td>
                                                                 <td class="localInvoiceNo">INV00000001</td>
                                                                 <td class="localInvoiceAmt text-right">1200</td>
                                                                 <td class="localInvoiceTaxAmt text-right">0</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-33" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-02-23</td>
                                                                 <td class="localVendorGstin">19AADCE3321L1ZN</td>
                                                                 <td class="localVendorName">ENCODERS TECHNO</td>
                                                                 <td class="localInvoiceNo">Encoder/001/001</td>
                                                                 <td class="localInvoiceAmt text-right">129800</td>
                                                                 <td class="localInvoiceTaxAmt text-right">19800</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-22" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-03-27</td>
                                                                 <td class="localVendorGstin">19AADCE3321L1ZN</td>
                                                                 <td class="localVendorName">ENCODERS TECHNO</td>
                                                                 <td class="localInvoiceNo">Encoder/1110/201</td>
                                                                 <td class="localInvoiceAmt text-right">129800</td>
                                                                 <td class="localInvoiceTaxAmt text-right">19800</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-21" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-03-28</td>
                                                                 <td class="localVendorGstin">19AABCT1375R1ZO</td>
                                                                 <td class="localVendorName">TURTLE LTD.</td>
                                                                 <td class="localInvoiceNo">Turtle/2803/002</td>
                                                                 <td class="localInvoiceAmt text-right">129800</td>
                                                                 <td class="localInvoiceTaxAmt text-right">19800</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-10" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-03-29</td>
                                                                 <td class="localVendorGstin">19AAACJ3814E1ZU</td>
                                                                 <td class="localVendorName">Shubham Casa</td>
                                                                 <td class="localInvoiceNo">Encoder/0002/003</td>
                                                                 <td class="localInvoiceAmt text-right">8800.00</td>
                                                                 <td class="localInvoiceTaxAmt text-right">1800</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-20" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-03-29</td>
                                                                 <td class="localVendorGstin">19AAACJ3814E1ZU</td>
                                                                 <td class="localVendorName">Shubham Casa</td>
                                                                 <td class="localInvoiceNo">Encoder/0002/001</td>
                                                                 <td class="localInvoiceAmt text-right">153400</td>
                                                                 <td class="localInvoiceTaxAmt text-right">23400</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-5" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-04-18</td>
                                                                 <td class="localVendorGstin">19AABCT6577B1Z7</td>
                                                                 <td class="localVendorName">CLOUDKAPTAN CON</td>
                                                                 <td class="localInvoiceNo">INV202304183626 INV203304183626</td>
                                                                 <td class="localInvoiceAmt text-right">62988.20</td>
                                                                 <td class="localInvoiceTaxAmt text-right">9498.2</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-9" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-04-30</td>
                                                                 <td class="localVendorGstin">09AAFCA7387Q1ZQ</td>
                                                                 <td class="localVendorName">Amrit vendor</td>
                                                                 <td class="localInvoiceNo">INV7536374</td>
                                                                 <td class="localInvoiceAmt text-right">2546.00</td>
                                                                 <td class="localInvoiceTaxAmt text-right">346</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-8" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-05-05</td>
                                                                 <td class="localVendorGstin">07AAACR4849R1ZN</td>
                                                                 <td class="localVendorName">Tata Consultanc</td>
                                                                 <td class="localInvoiceNo">INV7536374</td>
                                                                 <td class="localInvoiceAmt text-right">472</td>
                                                                 <td class="localInvoiceTaxAmt text-right">72</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-7" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-05-19</td>
                                                                 <td class="localVendorGstin">29AAACH3235M1ZF</td>
                                                                 <td class="localVendorName">ACCENTURE SOLUT</td>
                                                                 <td class="localInvoiceNo">INV7536374</td>
                                                                 <td class="localInvoiceAmt text-right">5040</td>
                                                                 <td class="localInvoiceTaxAmt text-right">240</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-6" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-05-31</td>
                                                                 <td class="localVendorGstin">19AABCZ0038M1Z2</td>
                                                                 <td class="localVendorName">6 LIVO TECHNOLO</td>
                                                                 <td class="localInvoiceNo">33</td>
                                                                 <td class="localInvoiceAmt text-right">2625.00</td>
                                                                 <td class="localInvoiceTaxAmt text-right">125</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-4" class="even ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-06-13</td>
                                                                 <td class="localVendorGstin">29AAACH3235M1ZF</td>
                                                                 <td class="localVendorName">ACCENTURE SOLUT</td>
                                                                 <td class="localInvoiceNo">01</td>
                                                                 <td class="localInvoiceAmt text-right">200000</td>
                                                                 <td class="localInvoiceTaxAmt text-right">0</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                               <tr id="rightRow-1" class="odd ui-sortable-handle">
                                                                 <td class="localInvoiceDate sorting_1">2023-07-07</td>
                                                                 <td class="localVendorGstin">07AAACR4849R1ZN</td>
                                                                 <td class="localVendorName">Tata Consultanc</td>
                                                                 <td class="localInvoiceNo">INv123456789</td>
                                                                 <td class="localInvoiceAmt text-right">168000</td>
                                                                 <td class="localInvoiceTaxAmt text-right">18000</td>
                                                                 <td><i class="fa fa-sort"></i></td>
                                                               </tr>
                                                             </tbody>
                                                           </table>
                                                         </div>
                                                       </div>
                                                       <div class="row">
                                                         <div class="col-sm-12 col-md-5"></div>
                                                         <div class="col-sm-12 col-md-7"></div>
                                                       </div>
                                                     </div>
                                                   </div>
                                                 </div>

                                               </div>
                                               <div class="row">

                                               </div>
                                             </section>




                                           </div>

                                           <!-- -------------------Audit History Tab Body Start------------------------- -->
                                           <div class="tab-pane fade" id="history<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                             <div class="audit-head-section mb-3 mt-3 ">
                                               <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($row['customer_created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['customer_created_at']) ?></p>
                                               <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($row['customer_updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['customer_updated_at']) ?></p>
                                             </div>
                                             <hr>
                                             <div class="audit-body-section mt-2 mb-3 auditTrailBodyContentCustomer<?= $row['customer_code'] ?>">

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
                                 <!--/.Content-->
                               </div>
                 </div>
                 <!-- right modal end here  -->
                 </td>


                 </tr>




               <?php } ?>



               </tbody>
               <tbody>
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
             <?= $customerModalHtml ?>





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
                     <input type="hidden" name="pageTableName" value="ERP_CUSTOMER" />
                     <div class="modal-body">
                       <div id="dropdownframe"></div>
                       <div id="main2">
                         <table>
                           <tr>
                             <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                               Customer Code</td>
                           </tr>
                           <tr>
                             <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                               Customer Icon</td>
                           </tr>
                           <tr>
                             <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                               Customer Name</td>
                           </tr>
                           <tr>
                             <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                               Customer TAN</td>
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
                           <tr>
                             <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="8" />
                               Order Volume</td>
                           </tr>
                           <tr>
                             <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(9, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="9" />
                               Receipt Amount</td>
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
   </section>
   </div>

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