<?php
require_once("../../app/v1/connection-branch-admin.php");
//   administratorLocationAuth();
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

  $addBranchPr = $BranchPrObj->addVendorList($_POST);

  swalToast($addBranchPr["status"], $addBranchPr["message"]);
}
if (isset($_POST['addNewOtherVendor'])) {
  // console($_POST);
  // exit();

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
  /* Ensure the search box is displayed */
  div.dataTables_wrapper div.dataTables_filter,
  .dataTables_wrapper .row:nth-child(3) {
    display: block !important;
  }

  /* Optional: Additional styling for the search box (if needed) */
  .dataTables_filter input[type="search"] {
    width: 200px;
    /* Adjust width if needed */
  }


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
                        <h5 class="modal-title text-white" id="exampleModalLabel">Add Vendor</h5>
                      </div>
                      <div class="modal-body">

                        <div class="accordion-item filter-serch-accodion">
                          <h2 class="accordion-header" id="flush-headingOne">
                            <!-- <button class="accordion-button collapsed btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                               Advanced Search Filter
                             </button> -->
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
                              <table class="filter-add-item table table-hover" id="vendordetails">
                                <thead style="top: 0 !important;">
                                  <tr>
                                    <th></th>
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
    $("#addNewVendorListId").prop('disabled', true);
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


    $(document).on('change', "input[name='vendorId[]']", function() {
      // Check if at least one checkbox is checked
      if ($("input[name='vendorId[]']:checked").length > 0) {
        $('#addNewVendorListId').prop('disabled', false); // Enable the button
      } else {
        $('#addNewVendorListId').prop('disabled', true); // Disable the button
      }
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
  <script>
    let url = `<?= LOCATION_URL ?>manage-rfq.php`;
    window.location.href = url;
  </script>
<?php
}
require_once("../common/footer.php");
?>
<script>
  // dataTable = $("#vendordetails").DataTable({
  //                 dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
  //                 "lengthMenu": [10, 25, 50, 100, 200, 250],
  //                 "ordering": false,
  //                 info: false,
  //                 "initComplete": function(settings, json) {
  //                     $('#vendordetails_filter input[type="search"]').attr('placeholder', 'Search....');
  //                     console.log($('#vendordetails_filter'));
  //                 },

  //                 buttons: [],
  //                 // select: true,
  //                 "bPaginate": false,
  //             });
  table = $('#vendordetails').DataTable({
    dom: '<"top"f>rt<"bottom"ip>',
    "lengthMenu": [10, 25, 50, 100, 200],
    "ordering": false,
    info: false,
    "pageLength": true,
    searching: true,
    "initComplete": function(settings, json) {
      $('#vendordetails_filter input[type="search"]').attr('placeholder', 'Search...');
    },
    buttons: [{
      extend: 'collection',
      text: '<ion-icon name="download-outline"></ion-icon> Export',
      buttons: [{
        extend: 'excel',
        text: '<ion-icon name="document-outline" class="ion-excel"></ion-icon> Excel',
      }]
    }],
    "bPaginate": false,
  });


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

    var regEmail =
  /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;

      $(document).on("click", "#addNewOtherVendorId", function (e) {
  // alert("test");
    // e.preventDefault();
    let validStatus = 0;
    let specStatus = 0;

    // CLOSING DATE VALIDATION
    if ($("#closingDate").val() == "") {
      $(".rfq_closingDate").remove();
      $("#closingDate")
        .parent()
        .append(
          '<span class="error rfq_closingDate">Closing Date is required</span>'
        );
      $(".rfq_closingDate").show();

      $(".notesclosingDate").remove();
      $("#notesModalBody").append(
        '<p class="notesclosingDate font-monospace text-danger">Closing Date is required</p>'
      );
    } else {
      $(".rfq_closingDate").remove();

      $(".notesclosingDate").remove();
      validStatus++;
    }

    // VENDOR VALIDATION
    if (
      $(".modal-add-row_538").children().length == 0 &&
      $("#newLogic").children().length == 0
    ) {
      $(".vndr-rqrd_538").remove();
      $(".vndr-rqrd").append(
        '<span class="error vndr-rqrd_538">Atleast One Vendor is required</span>'
      );
      $(".vndr-rqrd_538").show();

      $(".notesmodal-add-row_538").remove();
      $("#notesModalBody").append(
        '<p class="notesmodal-add-row_538 font-monospace text-danger">Atleast One Vendor is required</p>'
      );
    } else if (
      $(".modal-add-row_538").children().length != 0 &&
      $("#newLogic").children().length == 0
    ) {
      let elemId = $(".each_name")[0].getAttribute("id").split("_")[1];

      if (
        $(`#eachName_${elemId}`).val() == "" ||
        $(`#eachEmail_${elemId}`).val() == ""
      ) {
        $(".vndr-rqrd_538").remove();
        $(".vndr-rqrd").append(
          '<span class="error vndr-rqrd_538">Vendor Name and Email both is required</span>'
        );
        $(".vndr-rqrd_538").show();

        $(".notesmodal-add-row_538").remove();
        $("#notesModalBody").append(
          '<p class="notesmodal-add-row_538 font-monospace text-danger">Vendor Name and Email both is required</p>'
        );
      } else {
        $(".vndr-rqrd_538").remove();

        $(".notesmodal-add-row_538").remove();
        validStatus++;
      }
    } else {
      $(".vndr-rqrd_538").remove();

      $(".notesmodal-add-row_538").remove();
      validStatus++;
    }

    for (elem of $(".each_name").get()) {
      let element = elem.getAttribute("id").split("_")[1];

      // NAME VALIDATION
      if (
        $(`#eachName_${element}`).val() == "" &&
        $(`#eachEmail_${element}`).val() != ""
      ) {
        $(`.rfq_eachName_${element}`).remove();
        $(`#eachName_${element}`)
          .parent()
          .parent()
          .parent()
          .append(
            `<span class="error rfq_eachName_${element}">Vendor Name is required</span>`
          );
        $(`.rfq_eachName_${element}`).show();

        $(`.noteseachName_${element}`).remove();
        $("#notesModalBody").append(
          `<p class="noteseachName_${element} font-monospace text-danger">Vendor Name is required</p>`
        );
      } else {
        $(`.rfq_eachName_${element}`).remove();

        $(`.noteseachName_${element}`).remove();
        specStatus++;
      }

      // EMAIL VALIDATION
      if (
        $(`#eachEmail_${element}`).val() == "" &&
        $(`#eachName_${element}`).val() != ""
      ) {
        $(`.rfq_eachEmail_${element}`).remove();
        $(`#eachEmail_${element}`)
          .parent()
          .parent()
          .parent()
          .append(
            `<span class="error rfq-reachemail-error rfq_eachEmail_${element}">Vendor Email is required</span>`
          );
        $(`.rfq_eachEmail_${element}`).show();

        $(`.noteseachEmail_${element}`).remove();
        $("#notesModalBody").append(
          `<p class="noteseachEmail_${element} font-monospace text-danger">Vendor Email is required</p>`
        );
      } else {
        $(`.rfq_eachEmail_${element}`).remove();

        $(`.noteseachEmail_${element}`).remove();
        specStatus++;
      }

      // EMAIL REGEX VALIDATION
      if ($(`#eachEmail_${element}`).val() != "") {
        if (regEmail.test($(`#eachEmail_${element}`).val())) {
          $(`.rfq_eachEmail_${element}`).remove();

          $(`.noteseachEmail_${element}`).remove();
          specStatus++;
        } else {
          $(`.rfq_eachEmail_${element}`).remove();
          $(`#eachEmail_${element}`)
            .parent()
            .parent()
            .parent()
            .append(
              `<span class="error rfq_eachEmail_${element}">Check your email</span>`
            );
          $(`.rfq_eachEmail_${element}`).show();

          $(`.noteseachEmail_${element}`).remove();
          $("#notesModalBody").append(
            `<p class="noteseachEmail_${element} font-monospace text-danger">Check your email</p>`
          );
        }
      }
    }

    if (validStatus !== 2) {
      e.preventDefault();
      $("#exampleRfqModal").modal("show");
    } else if (specStatus !== $(".each_name").length * 3) {
      e.preventDefault();
      $("#exampleRfqModal").modal("show");
    } else {
      var newArray = new Array();
      var newArray1 = new Array();
      var arr3 = new Array();
      $.each($(".each_name"), function (i, value) {
        newArray.push($(this).val());
      });
      $.each($(".each_email"), function (j, values) {
        newArray1.push($(this).val());
      });

      console.log(newArray);
      console.log(newArray1);
      let i = 0,
        j = 0,
        k = 0;

      while (i < newArray.length && j < newArray1.length) {
        if (newArray[i] == "" && newArray1[j] == "") {
          i++;
          j++;
          continue;
        } else {
          arr3[k++] =
            null +
            "|" +
            null +
            "|" +
            newArray[i++] +
            "|" +
            newArray1[j++] +
            "|others";
        }
      }

      $.ajax({
        type: "POST",
        url: `ajaxs/pr/ajax-rfq-submit.php`,
        data: {
          data: arr3.concat(test),
          rfq_code: $("#rfqNum").val(),
          rfq_item_list_id: $("#rfqId").val(),
          closing_date: $("#closingDate").val(),
        },
        beforeSend: function () {
          $("#addNewOtherVendorId").html(`Submitting...`);
        },
        success: function (response) {
          console.log(JSON.parse(response));
          $("#addNewOtherVendorId").html(`Submitted`);
          window.location.href =
            BASE_URL+"branch/location/manage-rfq.php";
        },
      });
    }
  });
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

<!-- <script src="<?= BASE_URL; ?>public/validations/rfqValidation.js"></script> -->