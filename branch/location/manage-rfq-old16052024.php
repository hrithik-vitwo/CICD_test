 <?php
  require_once("../../app/v1/connection-branch-admin.php");
  // administratorLocationAuth();
  require_once("../common/header.php");
  require_once("../common/navbar.php");
  require_once("../common/sidebar.php");
  require_once("../common/pagination.php");
  require_once("../../app/v1/functions/company/func-branches.php");
  require_once("../../app/v1/functions/branch/func-branch-pr-controller.php");


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

 <link rel="stylesheet" href="../../public/assets/sales-order.css">
 <link rel="stylesheet" href="../../public/assets/listing.css">
 <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

 <style>
   .rfq-reachemail-error {
     position: relative;
     margin: 7px 0;
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

     margin: 20px 0;

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

   }

   .info-h4 {

     font-size: 20px;

     font-weight: 600;

     color: #003060;

     padding: 0px 10px;

   }

   .rfq-modal .tab-content li a span,
   .rfq-modal .tab-content li a i {

     font-weight: 600 !important;

   }


   .float-add-btn {

     display: flex !important;

   }

   .items-search-btn {

     display: flex;

     align-items: center;

     gap: 5px;

     border: 1px solid #fff !important;

   }

   .card.existing-vendor .card-header,
   .card.other-vendor .card-header {

     display: flex;

     justify-content: space-between;

   }

   .card.existing-vendor a.btn-primary,
   .card.other-vendor a.btn-primary {

     padding: 3px 12px;

     margin-right: 10px;

     float: right;

     border: 1px solid #fff !important;

   }



   .card-body::after,
   .card-footer::after,
   .card-header::after {

     display: none;

   }

   .row.rfq-vendor-list-row-value {

     border-bottom: 1px solid #fff;

     margin: 0;

     align-items: center;

   }

   .row.rfq-vendor-list-row {

     margin: 0;

     border-bottom: 1px solid #fff;

     align-items: center;

   }

   .rfq-email-filter-modal .modal-dialog {

     max-width: 70%;

   }

   .rfq-email-filter-modal .modal-dialog .modal-body {
     height: 500px;
   }

   table.filter-add-item.table.table-hover tr td:first-child {
     position: sticky;
     left: 0;
   }

   .is-rfq .accordion .display-flex-space-between p {
    text-align: left;
   }

   .is-rfq .accordion .display-flex-space-between p:nth-child(2) {
    position: absolute;
    left: 40%;
   }

  .text-elipse-item-name {
    width: 400px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }


   @media (max-width: 575px) {

     .rfq-modal .modal-body {

       padding: 20px !important;

     }

   }

   @media(max-width: 390px) {

     .display-flex-space-between .matrix-btn {

       position: relative;

       top: 10px;

     }

   }
 </style>

 <?php
  if (isset($_GET['sendEmail']) && $_GET['sendEmail'] > 0) { ?>
   <div class="content-wrapper is-rfq">

     <!-- Modal -->
     <div class="modal fade" id="exampleRfqModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleRfqModalLabel" aria-hidden="true">
       <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content card">
           <div class="modal-header card-header py-2 px-3">
             <h4 class="modal-title font-monospace text-md text-white" id="exampleRfqModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
             <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
           </div>
           <div id="notesModalBody" class="modal-body card-body">
           </div>
         </div>
       </div>
     </div>

     <section class="content">
       <div class="container-fluid">
         <form action="" method="POST" id="addNewSOForm">

           <div class="row">

             <div class="col-lg-12 col-md-12 col-sm-12">
               <div class="card send-email-card">
                 <div class="card-header p-3">
                   <div class="row">
                     <div class="col-lg-12 col-md-12 col-sm-12">
                       <div class="head">
                         <i class="fa fa-info"></i>
                         <h4>Info</h4>
                       </div>
                     </div>
                   </div>
                 </div>
                 <?php
                  // console($BranchPrObj->fetchBranchSoListing()['data']);
                  $id = $_GET['sendEmail'];
                  //console($id);
                  $rfq = $BranchPrObj->fetchBranchRFQSingle($id);
                  //console($rfq['data'][0]['rfqId']);
                  ?>

                 <input type="hidden" id="rfqId" value="<?= $rfq['data'][0]['rfqId'] ?>">
                 <div class="card-body">
                   <div class="row">
                     <div class="col-lg-12 col-md-12 col-sm-12">
                       <div class="row">
                         <div class="col-lg-6 col-md-6 col-sm-12">
                           <div class="form-input">
                             <label for="date">PR Number</label>
                             <input type="text" name="expDate" class="form-control" value="<?= $rfq['data'][0]['prCode'] ?>" readonly />
                           </div>
                         </div>
                         <div class="col-lg-6 col-md-6 col-sm-12">
                           <div class="form-input">
                             <label for="date">RFQ Number</label>
                             <input type="text" name="refNo" id="rfqNum" class="form-control" value="<?= $rfq['data'][0]['rfqCode'] ?>" readonly />
                           </div>
                         </div>
                       </div>
                       <div class="row others-info-form-view">
                         <div class="col-lg-6 col-md-6 col-sm-12">
                           <div class="form-input">
                             <label for="date">Expected Date</label>
                             <input type="date" name="expDate" id="expDate" class="form-control" value="<?= $rfq['data'][0]['expectedDate'] ?>" readonly />
                           </div>
                         </div>
                         <div class="col-lg-6 col-md-6 col-sm-12">
                           <div class="form-input">
                             <label for="date">Reference Number</label>
                             <input type="text" name="refNo" class="form-control" value="<?= $rfq['data'][0]['refNo'] ?>" readonly />
                           </div>
                         </div>
                       </div>
                       <div class="row others-info-form-view">
                         <div class="col-lg-6 col-md-6 col-sm-12">
                           <div class="form-input">
                             <label for="date">Closing Date</label>
                             <input type="date" name="" id="closingDate" class="form-control" value="<?= $rfq['data'][0]['closing_date'] ?>" required />
                             <?php if ($rfq['data'][0]['closing_date'] > $rfq['data'][0]['expectedDate'] && $rfq['data'][0]['closing_date'] < date("Y-m-d")) { ?>

                               <p id="error">closing date cannot be greater than expected date</p>
                             <?php
                              } else {
                              ?>
                               <p id="error"></p>
                             <?php
                              }
                              ?>
                           </div>
                         </div>
                         <div class="col-lg-6 col-md-6 col-sm-12">
                           <div class="form-input">
                             <label for="date">Attachment</label>
                             <input type="file" name="attachment" class="form-control" value="" />
                           </div>
                         </div>
                       </div>
                     </div>
                   </div>
                 </div>
               </div>
             </div>
           </div>
           <div class="row">
             <div class="col-lg-12 col-md-12 col-sm-12">
               <div class="card existing-vendor">
                 <div class="card-header p-2">
                   <div class="head pl-2">
                     <i class="fa fa-pen"></i>
                     <h4>Existing Vendor</h4>
                   </div>
                   <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                     <i class="fa fa-plus mr-2"></i>ADD
                   </a>
                 </div>
                 <div class="row rfq-vendor-list-row ">
                   <div class="col-md-3 text-center rfq-vendor-list-col">
                     <p class="text-xs font-bold mb-2 mt-2">Vendor Code</p>
                   </div>
                   <div class="col-md-3 text-center rfq-vendor-list-col">
                     <p class="text-xs font-bold mb-2 mt-2">Vendor Name</p>
                   </div>
                   <div class="col-md-3 text-center rfq-vendor-list-col">
                     <p class="text-xs font-bold mb-2 mt-2">Email Id</p>
                   </div>
                   <div class="col-md-3 text-center rfq-vendor-list-col">
                   </div>
                 </div>

                 <?php
                  // console($BranchPrObj->fetchBranchSoListing()['data']);
                  $rfqId = $_GET['sendEmail'];
                  // console($rfqId);
                  // $rfqVendor = $BranchPrObj->fetchexistingRFQVendor($rfqId)['data'];
                  // foreach ($rfqVendor as $onerfqVendor) {
                  ?>
                 <div id="newLogic">


                 </div>
                 <?php
                  // }
                  ?>
               </div>
             </div>
           </div>
           <!-- <form method="POST"  > -->
           <input type="hidden" name="rfqId" value="<?= $_GET['sendEmail'] ?>">
           <div class="row">
             <div class="col-lg-12 col-md-12 col-sm-12">
               <div class="card other-vendor">
                 <div class="card-header p-2">
                   <div class="head pl-2">
                     <i class="fa fa-pen"></i>
                     <h4>Other Vendor</h4>
                   </div>
                   <a style="cursor: pointer" class="btn btn-primary" onclick="addMultiQty(538)">
                     <i class="fa fa-plus"></i>
                   </a>
                 </div>

                 <div class="card-body">
                   <div class="row">
                     <div class="col-lg-12 col-md-12 col-sm-12">
                       <div class="row others-vendor modal-add-row_538"></div>
                     </div>
                   </div>
                 </div>


                 </table>
                 <div class="modal fade zoom-in rfq-email-filter-modal items-filter-modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog">
                     <div class="modal-content">
                       <div class="modal-header">
                         <h5 class="modal-title text-white" id="exampleModalLabel">Advanced Filter Search</h5>
                       </div>
                       <div class="modal-body">

                         <div class="accordion-item filter-serch-accodion">
                           <h2 class="accordion-header" id="flush-headingOne">
                             <button class="accordion-button collapsed btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                               Advanced Search Filter
                             </button>
                             </button>
                           </h2>
                           <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                             <div class="accordion-body">
                               <div class="row">
                                 <div class="col-lg-12 col-md-12 col-sm-12">
                                   <div class="card filter-search-card">
                                     <div class="card-body">
                                       <div class="serch-input">
                                         <input type="text" class="form-control" placeholder="search">
                                         <select name="" id="" class="form-control form-select filter-select">
                                           <option value="">search</option>
                                           <option value="">search</option>
                                           <option value="">search</option>
                                         </select>
                                         <input type="text" class="form-control" placeholder="search">
                                         <select name="" id="" class="form-control form-select filter-select">
                                           <option value="">search</option>
                                           <option value="">search</option>
                                           <option value="">search</option>
                                         </select>
                                         <input type="text" class="form-control" placeholder="search">
                                         <select name="" id="" class="form-control form-select filter-select">
                                           <option value="">search</option>
                                           <option value="">search</option>
                                           <option value="">search</option>
                                         </select>
                                       </div>
                                       <button class="btn btn-primary items-search-btn"><i class="fa fa-search mr-2"></i>Search</button>
                                     </div>
                                   </div>
                                 </div>
                               </div>
                             </div>
                           </div>
                         </div>

                         <form method="POST">
                           <input type="hidden" name="rfqId" value="<?= $_GET['sendEmail'] ?>">
                           <div class="card filter-add-item-card">
                             <div class="card-header">
                               <button type="button" id="addNewVendorListId" class="btn btn-primary"><i class="fa fa-plus"></i>Add</button>
                             </div>
                             <div class="card-body" style="overflow: auto;">
                               <table class="filter-add-item table table-hover">
                                 <thead>
                                   <tr>
                                     <th><input type="checkbox"></th>
                                     <th>Vendor Code</th>
                                     <th>Vendor Name</th>
                                     <th>Vendor Constitution of Bussiness</th>
                                     <th>Vendor Email</th>
                                     <th>Vendor Phone</th>
                                     <th>Vendor Status</th>
                                   </tr>
                                 </thead>
                                 <tbody>
                                   <?php
                                    // console($BranchPrObj->fetchBranchSoListing()['data']);
                                    $vendorList = $BranchPrObj->fetchBranchVendor()['data'];
                                    foreach ($vendorList as $oneVendorList) {
                                    ?>
                                     <tr>
                                       <td><input type="checkbox" name="vendorId[]" value="<?= $oneVendorList['vendor_id'] . "|" . $oneVendorList['vendor_code'] . "|" . $oneVendorList['trade_name'] . "|" . $oneVendorList['vendor_authorised_person_email'] . "|existing" ?>"></td>
                                       <td><?= $oneVendorList['vendor_code'] ?></td>
                                       <td><?= $oneVendorList['trade_name'] ?></td>
                                       <td><?= $oneVendorList['constitution_of_business'] ?></td>
                                       <td><?= $oneVendorList['vendor_authorised_person_email'] ?></td>
                                       <td><?= $oneVendorList['vendor_authorised_person_phone'] ?></td>
                                       <td><?= $oneVendorList['vendor_status'] ?></td>
                                     </tr>

                                   <?php }
                                    ?>
                                 </tbody>
                               </table>
                             </div>
                           </div>
                         </form>
                       </div>
                       <div class="modal-footer">

                       </div>
                     </div>
                   </div>
                 </div>
               </div>
               <div class="vndr-rqrd"></div>

               <?php if ($rfq['data'][0]['closing_date'] > $rfq['data'][0]['expectedDate'] && $rfq['data'][0]['closing_date'] < date("Y-m-d")) { ?>
                 <div class="vendor-submit-section">
                   <button type="button" name="addNewOtherVendor" id="addNewOtherVendorId" class="btn btn-primary vendor-submit-btn float-right mt-3 mb-3" disabled>Send Quotation</button>
                 </div>
               <?php
                } else {
                ?>
                 <div class="vendor-submit-section">
                   <button type="button" name="addNewOtherVendor" id="addNewOtherVendorId" class="btn btn-primary vendor-submit-btn float-right mt-3 mb-3">Send Quotation</button>
                 </div>

               <?php
                }
                ?>
             </div>
           </div>
       </div>

   </div>


   <script>
     var test = new Array();
     $("#addNewVendorListId").click(function() {

       $("input[name='vendorId[]']:checked").each(function(i) {
         test.push($(this).val());
       });

       $.each(test, function(index, value) {

         const myArray = value.split("|");


         $("#newLogic").append(`<div class="row rfq-vendor-list-row-value">
                    <div class="col-md-3 text-center rfq-vendor-list-col mb-2 mt-2">
                      <p class="text-xs">` + myArray[1] + `</p>
                    </div>
                    <div class="col-md-3 text-center rfq-vendor-list-col mb-2 mt-2">
                      <p class="text-xs">` + myArray[2] + `</p>
                    </div>
                    <div class="col-md-3 text-center rfq-vendor-list-col mb-2 mt-2">
                      <p class="text-xs">` + myArray[3] + `</p>
                    </div>
                    <div class="col-md-3 text-center rfq-vendor-list-col mb-2 mt-2">
                      <a class="btn btn-danger remove_row" value = "` + myArray[0] + `" data-value = "` + myArray[0] + `" type="button" >
                        <i class="fa fa-minus"></i></a>
                    </div>
                  </div>`);

       });
       $('#exampleModal').modal('hide');


     });
   </script>

   <script>
     // $("#addNewOtherVendorId").click(function() {

     //   var newArray = new Array();
     //   var newArray1 = new Array();
     //   var arr3 = new Array();
     //   $.each($('.each_name'), function(i, value) {
     //     newArray.push($(this).val());
     //   });
     //   $.each($('.each_email'), function(j, values) {
     //     newArray1.push($(this).val());
     //   });

     //   console.log(newArray);
     //   console.log(newArray1);
     //   let i = 0,
     //     j = 0,
     //     k = 0;

     //   while (i < newArray.length && j < newArray1.length) {
     //     if (newArray[i] == "" && newArray1[j] == "") {
     //       i++;
     //       j++;
     //       continue;
     //     } else {
     //       arr3[k++] = null + "|" + null + "|" + newArray[i++] + "|" + newArray1[j++] + "|others";
     //     }
     //   }


     //   $.ajax({
     //     type: "POST",
     //     url: `ajaxs/pr/ajax-rfq-submit.php`,
     //     data: {
     //       data: arr3.concat(test),
     //       rfq_code: $("#rfqNum").val(),
     //       rfq_item_list_id: $("#rfqId").val(),
     //       closing_date: $("#closingDate").val()

     //     },
     //     beforeSend: function() {
     //       $("#addNewOtherVendorId").html(`Submitting...`);
     //       alert("88")
     //     },
     //     success: function(response) {
     //       console.log(JSON.parse(response));
     //       $("#addNewOtherVendorId").html(`Submitted`);
     //       alert("88")
     //       window.location.href = "<?= LOCATION_URL ?>manage-rfq.php";
     //     }
     //   });


     // });
   </script>


   </form>
   <!-- modal -->
   <div class="modal" id="addNewItemsFormModal">
     <div class="modal-dialog">
       <div class="modal-content">
         <div class="modal-header py-1" style="background-color: #003060; color:white;">
           <h4 class="modal-title">Add New Items</h4>
           <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body">
           <!-- <form action="" method="post" id="addNewItemsForm"> -->
           <div class="col-md-12 mb-3">
             <div class="input-group">
               <input type="text" name="itemName" class="m-input" required>
               <label>Item Name</label>
             </div>
           </div>
           <div class="col-md-12">
             <div class="input-group">
               <input type="text" name="itemDesc" class="m-input" required>
               <label>Item Description</label>
             </div>
           </div>
           <div class="col-md-12">
             <div class="input-group btn-col">
               <button type="submit" class="btn btn-primary btnstyle">Submit</button>
             </div>
           </div>
           <!-- </form> -->
         </div>
       </div>
     </div>
   </div>
   <!-- modal end -->
   </div>
   </section>
   </div>
 <?php } else { ?>
   <div class="content-wrapper is-rfq">
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
                  <h3 class="card-title">Manage Request For Quotations</h3>
                  <!-- <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?pr-creation" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></a> -->
                </li>
              </ul>
            </div>
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
                     // console($qry_list);

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
                             <th>PR Code</th>
                           <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                             <th>Reference Number</th>
                           <?php  }
                            if (in_array(4, $settingsCheckbox)) { ?>
                             <th>Expected Date</th>
                           <?php }
                            if (in_array(5, $settingsCheckbox)) { ?>
                             <th>Created By</th>

                           <?php } ?>

                           <th>Closing Date</th>
                           <th>Days Left</th>

                           <th>Action</th>
                         </tr>
                       </thead>

                       <tbody>
                         <?php
                          // console($BranchPrObj->fetchBranchSoListing()['data']);
                          $soList = $qry_list['data'];
                          foreach ($soList as $onePrList) {
                          ?>
                           <tr style="cursor:pointer">
                             <td><?= $cnt++ ?></td>
                             <?php if (in_array(1, $settingsCheckbox)) { ?>
                               <td><?= $onePrList['rfqCode'] ?></td>
                             <?php }
                              if (in_array(2, $settingsCheckbox)) { ?>
                               <td><?= $onePrList['prCode'] ?></td>
                             <?php }
                              if (in_array(3, $settingsCheckbox)) { ?>
                               <td><?= $onePrList['refNo'] ?></td>
                             <?php }
                              if (in_array(4, $settingsCheckbox)) { ?>
                               <td><?= $onePrList['expectedDate'] ?></td>
                             <?php }
                              if (in_array(5, $settingsCheckbox)) { ?>
                               <td><?= getCreatedByUser($onePrList['created_by']) ?></td>
                             <?php } ?>
                             <td><?= $onePrList['closing_date'] ?></td>
                             <td>
                               <?php
                                $date1 = date_create($onePrList['closing_date']);
                                $date2 = date_create(date('Y-m-d'));
                                $diff = date_diff($date1, $date2);
                                echo $diff->format("%R%a days");
                                ?>

                             </td>
                             <td>
                               <a style="cursor: pointer;" class="btn btn-sm" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $onePrList['rfqId'] ?>"><i class="fa fa-info po-list-icon"></i></a>
                             </td>
                           </tr>
                           <!-- right modal start here  -->

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

               <?php
                // console($BranchPrObj->fetchBranchSoListing()['data']);
                $soList = $qry_list['data'];
                foreach ($soList as $onePrList) {
                ?>
                 <div class="modal fade right rfq-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $onePrList['rfqId'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                   <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                     <div class="modal-content">
                       <div class="modal-header">
                         <p class="heading lead text-right mt-2 mb-2">RFQ Code : <?= $onePrList['rfqCode'] ?></p>
                         <p class="text-sm text-right mt-2 mb-2">Reference No : <?= $onePrList['refNo'] ?></p>
                         <p class="text-sm text-right mt-2 mb-2">Expected Date : <?= $onePrList['expectedDate'] ?></p>
                         <p class="text-sm text-right mt-2 mb-2">Status : <span class="status status-modal ml-2"><?php if ($onePrList['status'] != null) {
                                                                                                                    echo $onePrList['status'];
                                                                                                                  } else {
                                                                                                                    echo "PENDING";
                                                                                                                  }  ?></span>
                         </p>
                         <div class="display-flex-space-between mt-4 mb-3 tabs-for-rfq">
                           <ul class="nav nav-tabs" id="myTab" role="tablist">
                             <li class="nav-item">
                               <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= $onePrList['rfqId'] ?>">RFQ-Info</a>
                             </li>
                             <li class="nav-item">
                               <a class="nav-link" id="vendor-tab" data-toggle="tab" href="#vendor<?= $onePrList['rfqId'] ?>">RFQ-Vendor List</a>
                             </li>
                             <!-- -------------------Audit History Button Start------------------------- -->
                             <li class="nav-item">
                               <a class="nav-link auditTrail" id="history-tab<?= $onePrList['rfqId'] ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $onePrList['rfqCode']) ?>" href="#history<?= $onePrList['rfqId'] ?>" role="tab" aria-controls="history<?= $onePrList['rfqId']  ?>" aria-selected="false"><i class="fa fa-history mr-2"></i> Trail</a>
                             </li>
                             <!-- -------------------Audit History Button End------------------------- -->
                           </ul>
                           <a href="<?= LOCATION_URL ?>matrix.php?rfq=<?= $onePrList['rfqId'] ?>" class="btn btn-primary float-right matrix-btn">Matrix</a>
                         </div>


                         <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" class="white-text">×</span>
                                  </button> -->
                       </div>
                       <div class="modal-body pl-4 pr-4 pt-1">



                         <div class="tab-content" id="myTabContent">
                           <div class="tab-pane fade show active" id="home<?= $onePrList['rfqId'] ?>" role="tabpanel" aria-labelledby="home-tab">
                             <h4 class="info-h4">
                               Info
                               <hr class="mt-1 mb-1">
                             </h4>




                             <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                               <div class="accordion-item">
                                 <h2 class="accordion-header" id="flush-headingOne">
                                   <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#itemDetails" aria-expanded="true" aria-controls="flush-collapseOne">
                                     Items
                                   </button>
                                 </h2>
                                 <div id="itemDetails" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                   <div class="accordion-body p-0">

                                     <div class="card">

                                       <div class="card-body p-3">
                                         <?php
                                          $itemDetails = $BranchPrObj->fetchBranchRFQItems($onePrList['rfqId'])['data'];
                                          foreach ($itemDetails as $oneItem) {
                                          ?>
                                           <div class="accordion accordion-flush matrix-accordion p-0" id="accordionSubFlushExample">
                                             <div class="accordion-item">
                                               <h2 class="accordion-header" id="flush-headingOne">
                                                 <button class="accordion-button btn btn-primary collapsed gap-3" type="button" data-bs-toggle="collapse" data-bs-target="#itemCountDetails-<?= $oneItem['itemId'] ?>" aria-expanded="true" aria-controls="flush-collapseOne">
                                                   <?= $oneItem['itemName'] ?>
                                                 </button>
                                               </h2>
                                               <div id="itemCountDetails-<?= $oneItem['itemId'] ?>" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionSubFlushExample">
                                                 <div class="accordion-body px-3">
                                                   <div class="display-flex-space-between">
                                                     <p class="font-bold text-xs">Item Code :</p>
                                                     <p class="font-bold text-xs"><?= $oneItem['itemCode'] ?></p>
                                                   </div>
                                                   <div class="display-flex-space-between">
                                                     <p class="font-bold text-xs">Item Name :</p>
                                                     <p class="font-bold text-xs text-elipse-item-name" title="Name : <?= $oneItem['itemName'] ?>"> <?= $oneItem['itemName'] ?></p>
                                                   </div>
                                                   <div class="display-flex-space-between">
                                                     <p class="font-bold text-xs">Item Id :</p>
                                                     <p class="font-bold text-xs"> <?= $oneItem['itemId'] ?></p>
                                                   </div>
                                                 </div>
                                               </div>
                                             </div>
                                           </div>
                                         <?php } ?>
                                       </div>
                                     </div>
                                   </div>
                                 </div>
                               </div>
                             </div>
                           </div>
                           <div class="tab-pane fade" id="vendor<?= $onePrList['rfqId'] ?>" role="tabpanel" aria-labelledby="vendor-tab">
                             <div class="row">
                               <div class="col-md-12">
                                 <div class="float-add-btn-center">
                                   <a href="<?= $_SERVER["PHP_SELF"]; ?>?sendEmail=<?= $onePrList['rfqId'] ?>" class="btn btn-primary float-add-btn vendor-list-add-btn-modal">
                                     <i class="fa fa-plus"></i>
                                   </a>
                                 </div>
                               </div>
                             </div>
                             <h4 class="info-h4">
                               Vendor List
                               <hr class="mt-1 mb-1">
                             </h4>
                             <div class="card rfq-vendor-list-row mt-3">
                               <div class="card-body" style="overflow-x: auto; border-radius: 12px;">
                                 <table class="table defaultDataTable table-nowrap table-hover">
                                   <thead>
                                     <th>Vendor Code</th>
                                     <th>Vendor Name</th>
                                     <th>Email Id</th>
                                   </thead>
                                   <tbody>
                                     <?php
                                      $rfqId = $onePrList['rfqId'];
                                      $rfqVendor = $BranchPrObj->fetchRFQVendor($rfqId)['data'];
                                      foreach ($rfqVendor as $onerfqVendor) {
                                      ?>
                                       <tr>
                                         <td><?= $onerfqVendor['vendorCode'] ?></td>
                                         <td><?= $onerfqVendor['vendor_name'] ?></td>
                                         <td><?= $onerfqVendor['vendor_email'] ?></td>
                                       </tr>
                                     <?php } ?>
                                   </tbody>
                                 </table>
                               </div>
                             </div>
                           </div>
                           <!-- -------------------Audit History Tab Body Start------------------------- -->
                           <div class="tab-pane fade" id="history<?= $onePrList['rfqId']  ?>" role="tabpanel" aria-labelledby="history-tab">

                             <div class="audit-head-section mb-3 mt-3 ">
                               <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($onePrList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePrList['created_at']) ?></p>
                               <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($onePrList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePrList['updated_at']) ?></p>
                             </div>
                             <hr>
                             <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $onePrList['rfqCode']) ?>">

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
                     <!--/.Content-->
                   </div>
                 </div>
               <?php } ?>


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
                                 PR Code</td>
                             </tr>
                             <tr>
                               <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                 Expected Date </td>
                             </tr>
                             <tr>
                               <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                 Reference Number</td>
                             </tr>
                             <tr>
                               <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                 PO</td>
                             </tr>
                             <tr>
                               <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                 Created By</td>
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
 <?php } ?>

 <?php
  require_once("../common/footer.php");
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
    <div class="modal-body pl-3 pr-3" style="overflow: hidden;">
    <div class="row" style="align-items: end;">
                          <div class="col-lg-5 col-md-5 col-sm-12">
                            <div class="form-input">
                              <label for="date">Vendor Name</label>
                              <input type="text" id="eachName_${addressRandNo}" name="OthersVendor[${addressRandNo}][name]" class="form-control each_name" placeholder="Vendor Name" />
                            </div>
                          </div>
                            <div class="col-lg-5 col-md-5 col-sm-12">
                              <div class="form-input">
                                <label for="date">Vendor Email</label>
                                <input type="text" id="eachEmail_${addressRandNo}" name="OthersVendor[${addressRandNo}][email]" class="form-control each_email" placeholder="Vendor Email" />
                              </div>
                            </div>
                              <div class="col-lg-2 col-md-2 text-center remove_row_other" data-value="${addressRandNo}">
                                <a class="btn btn-danger" type="button">
                                  <i class="fa fa-minus"></i></a>
                              </div>
                            </div>
                            </div>`);
   }
 </script>
 <script>
   var BASE_URL = "<?= BASE_URL ?>";
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

   function compare_dates() {
     let closing_date = $("#closingDate").val();
     let exp_date = $("#expDate").val();
     if (closing_date > exp_date && closing_date < date) {
       $("#error").html(`<p id="error">closing date cannot be greater than expected date</p>`);
       document.getElementById("addNewOtherVendorId").disabled = true;
     } else {
       $("#error").html('');
       document.getElementById("addNewOtherVendorId").disabled = false;
     }

   }
   $("#closingDate").change(function() {

     compare_dates();

   });
 </script>

 <script src="<?= BASE_URL; ?>public/validations/rfqValidation.js"></script>