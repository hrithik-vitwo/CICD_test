<?php
require_once("../app/v1/connection-customer-admin.php");
//administratorAuth();
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
require_once("common/pagination.php");
require_once("../app/v1/functions/company/func-branches.php");
require_once("../app/v1/functions/branch/func-brunch-so-controller.php");

//console($_SESSION);

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
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCustomerAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$BranchSoObj = new BranchSo();

if (isset($_POST['addNewSOFormSubmitBtn'])) {
  // console($_POST);
  // exit;
  $addBranchSo = $BranchSoObj->addBranchSo($_POST);
  // console($addBranchSo);
  if ($addBranchSo['status'] == "success") {
    $addBranchSoItems = $BranchSoObj->addBranchSoItems($_POST, $addBranchSo['lastID']);
    // console($addBranchSoItems);
    if ($addBranchSoItems['status'] == "success") {
      // swalToast($addBranchSoItems["status"], $addBranchSoItems["message"]);
      swalToast($addBranchSoItems["status"], $addBranchSoItems["message"], $_SERVER['PHP_SELF']);
    } else {
      swalToast($addBranchSoItems["status"], $addBranchSoItems["message"]);
    }
  } else {
    swalToast($addBranchSo["status"], $addBranchSo["message"]);
  }
}
?>
<link rel="stylesheet" href="../public/assets/sales-order.css">
<link rel="stylesheet" href="../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<?php
if (isset($_GET['customer-so-creation'])) { ?>
  <div class="content-wrapper">
    <section class="content">
      <div class="container-fluid">

        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Sales Order List</a></li>
          <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
              Create Sales Order</a></li>
          <li class="back-button">
            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
              <i class="fa fa-reply po-list-icon"></i>
            </a>
          </li>
        </ol>

        <form action="" method="POST" id="addNewSOForm">


          <div class="row po-form-creation">
            <div class="col-lg-6 col-md-6 col-sm-12">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="card so-creation-card po-creation-card po-creation-card">
                    <div class="card-header">
                      <div class="row customer-info-head">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="head">
                            <i class="fa fa-user"></i>
                            <h4>Vendor Info</h4>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-body vendor-info">
                      <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="row info-form-view">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                              <div class="form-inline input-box customer-select">
                                <label for="">Select Customer <span class="text-danger">*</span></label>
                                &nbsp; &nbsp;
                                <select name="customerId" id="customerDropDown" class="selct-vendor-dropdown" required>
                                  <option value="">Select Customer</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                              <div class="customer-info-text po-customer-info-text" id="customerInfo">

                              </div>
                              </span>
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
                  <div class="card">
                    <div class="card-header">
                      <div class="row others-info-head">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="head">
                            <i class="fa fa-info"></i>
                            <h4>Items Info</h4>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-body quickadd" style="gap: 10px;">
                      <div class="row">
                        <div class=" col-lg-6 col-md-6 col-sm-12">
                          <label for="">Quick Add <span class="text-danger">*</span></label>
                          <select id="itemsDropDown" class="form-control">
                            <option value="">Goods Type</option>
                            <option value="hello">hello</option>
                            <option value="hello1">hello1</option>
                          </select>
                        </div>
                        <div class=" col-lg-6 col-md-6 col-sm-12">
                          <a class="btn btn-primary advanced-search-btn btn-xs" data-bs-toggle="modal" data-bs-target="#exampleModal"> <i class="fa fa-search mr-2"></i>Advance Search</a>

                          <div class="modal fade items-filter-modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Advanced
                                    Filter Search</h5>
                                </div>
                                <div class="modal-body">

                                  <div class="accordion-item filter-serch-accodion">
                                    <h2 class="accordion-header" id="flush-headingOne">
                                      <button class="accordion-button collapsed btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                        Advanced Search Filter
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
                                                    <option value="">
                                                      search</option>
                                                    <option value="">
                                                      search</option>
                                                    <option value="">
                                                      search</option>
                                                  </select>
                                                  <input type="text" class="form-control" placeholder="search">
                                                  <select name="" id="" class="form-control form-select filter-select">
                                                    <option value="">
                                                      search</option>
                                                    <option value="">
                                                      search</option>
                                                    <option value="">
                                                      search</option>
                                                  </select>
                                                  <input type="text" class="form-control" placeholder="search">
                                                  <select name="" id="" class="form-control form-select filter-select">
                                                    <option value="">
                                                      search</option>
                                                    <option value="">
                                                      search</option>
                                                    <option value="">
                                                      search</option>
                                                  </select>
                                                </div>
                                                <button class="btn btn-primary btn-xs"><i class="fa fa-search mr-2"></i>Search</button>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>

                                  <div class="card filter-add-item-card">
                                    <div class="card-header">
                                      <button class="btn btn-primary"><i class="fa fa-plus"></i> Add</button>
                                    </div>
                                    <div class="card-body">
                                      <table class="filter-add-item">
                                        <thead>
                                          <tr>
                                            <th><input type="checkbox"></th>
                                            <th>Item Code</th>
                                            <th>Item Code</th>
                                            <th>Item Code</th>
                                            <th>Item Code</th>
                                            <th>Item Code</th>
                                            <th>Item Code</th>
                                            <th>Item Code</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr>
                                            <td><input type="checkbox"></td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                          </tr>
                                          <tr>
                                            <td><input type="checkbox"></td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                          </tr>
                                          <tr>
                                            <td><input type="checkbox"></td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                          </tr>
                                          <tr>
                                            <td><input type="checkbox"></td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                          </tr>
                                          <tr>
                                            <td><input type="checkbox"></td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                          <tr>
                                            <td><input type="checkbox"></td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                          <tr>
                                            <td><input type="checkbox"></td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                          <tr>
                                            <td><input type="checkbox"></td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                          <tr>
                                            <td><input type="checkbox"></td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                            <td>12</td>
                                          </tr>
                                        </tbody>
                                      </table>
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
                </div>
              </div>


            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
              <div class="card so-creation-card po-creation-card">
                <div class="card-header">
                  <div class="row others-info-head">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="head">
                        <i class="fa fa-info"></i>
                        <h4>Others Info</h4>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-body others-info">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="row info-form-view">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                          <label>SO Date: <span class="text-danger">*</span></label>
                          <div>
                            <input type="date" value="<?= date("Y-m-d") ?>" name="soDate" id="soDate" class="form-control" required />
                            <span class="input-group-addon"></span>
                          </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                          <label>Delivery Date: <span class="text-danger">*</span></label>
                          <div>
                            <input type="date" value="<?= date("Y-m-d") ?>" name="deliveryDate" id="deliveryDate" class="form-control" required />
                            <span class="input-group-addon"></span>
                          </div>
                        </div>
                      </div>
                      <div class="row info-form-view">
                        <div class="col-lg-6 col-md-6 col-sm-12 form-inline">
                          <label for="">Profile Center <span class="text-danger">*</span></label>
                          <select name="profitCenter" class="form-control" id="profitCenterDropDown" required>
                            <option value="">Profit Center</option>
                            <?php
                            $funcList = $BranchSoObj->fetchFunctionality()['data'];
                            foreach ($funcList as $func) {
                            ?>
                              <option value="<?= $func['functionalities_id'] ?>"><?= $func['functionalities_name'] ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 cost-center-col">
                          <label for="">Select KAM <span class="text-danger">*</span></label>
                          <select name="kamId" class="form-control" id="kamDropDown" required>
                            <option value="">Select KAM</option>
                            <?php
                            $funcList = $BranchSoObj->fetchKamDetails()['data'];
                            foreach ($funcList as $func) {
                            ?>
                              <option value="<?= $func['kamId'] ?>"><?= $func['kamName'] ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                      <div class="row info-form-view">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <label for="">Customer PO Number <span class="text-danger">*</span></label>
                          <input type="text" name="customerPO" class="form-control" placeholder="customer po number" required />
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
              <div class="card items-select-table">
                <table class="table tabel-hover table-nowrap">
                  <thead>
                    <tr>
                      <th>Item Code</th>
                      <th>Item Name</th>
                      <th>HSN Code</th>
                      <th>Qty</th>
                      <th>Unit Price</th>
                      <th>Disc %</th>
                      <th>Disc. Amt.</th>
                      <th>Tax</th>
                      <th>Total Tax</th>
                      <th>Total Price</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <span id="spanItemsTable"></span>
                  <tbody id="itemsTable"></tbody>
                  <tbody class="total-calculate">
                    <tr>
                      <td colspan="9" class="text-right" style="border: none;"> </td>
                      <td colspan="0" class="text-left font-weight-bold totalCal" style="border: none;">Sub <sup class="text-primary">[TOTAL]</sup></td>
                      <input type="hidden" name="subTotal" value="0">
                      <td colspan="2" id="grandSubTotalAmt" style="border: none;">0.00</th>
                    </tr>
                    <tr>
                      <td colspan="9" class="text-right" style="border: none;"> </td>
                      <td colspan="0" class="text-left font-weight-bold totalCal" style="border: none;">TOTAL <sup class="text-danger">[DISCOUNT]</sup></td>
                      <input type="hidden" name="totalDiscount" value="0">
                      <td colspan="2" id="grandTotalDiscount" style="border: none;">0.00</td>
                    </tr>
                    <tr>
                      <td colspan="9" class="text-right" style="border: none;"> </td>
                      <td colspan="0" class="text-left font-weight-bold totalCal" style="border: none;">TOTAL <sup class="text-info">[TAX]</sup></td>
                      <input type="hidden" name="taxAmount" value="0">
                      <td colspan="2" id="grandTaxAmt" style="border: none;">0.00</td>
                    </tr>
                    <tr>
                      <td colspan="9" class="text-right" style="border: none;"> </td>
                      <td colspan="0" class="text-left font-weight-bold totalCal" style="border: none;">TOTAL <sup class="text-success">[VALUE]</sup></td>
                      <input type="hidden" name="totalAmt" value="0">
                      <td colspan="2" id="grandTotalAmt" style="border: none;">0.00</th>
                    </tr>
                  </tbody>
                  <tfoot>
                    <th colspan="8" class="text-right" style="border: none; background: none;"> </th>
                    <th colspan="0" class="text-right" style="border: none; background: none;"></th>
                    <td colspan="2" style="border: none; background: none;">
                      <!-- <button type="submit" name="addNewSOFormSubmitBtn" class="btn btn-primary items-search-btn float-right">Final Submit</button> -->
                    </td>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
              <div class="card so-creation-card po-creation-card po-others-info">
                <div class="card-header">
                  <div class="row others-info-head">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="head">
                        <i class="fa fa-info"></i>
                        <h4>Others Cost info</h4>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-body others-accordion-info" id="cost">

                  <div class="row accordion-other-cost">
                    <div class="col">
                      <div class="tabs">

                        <div class="tab">
                          <input type="checkbox" id="chck1" style="display: none;">
                          <label class="tab-label" for="chck1">Freight Cost</label>
                          <div class="tab-content">
                            <div class="row othe-cost-infor modal-add-row_538">
                              <div class="row othe-cost-infor">
                                <div class="col-lg-2 col-md-12 col-sm-12">
                                  <div class="form-input">
                                    <label for="">Vendor Select</label>
                                    <select class="form-control">
                                      <option value="">Tata Consultancy Limited
                                      </option>
                                      <option value="">ITC Limited</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-lg-2 col-md-12 col-sm-12">
                                  <div class="form-input">
                                    <label for="">Amount</label>
                                    <input step="0.01" type="number" class="form-control" placeholder="placeholder">
                                  </div>
                                </div>
                                <div class="col-lg-2 col-md-12 col-sm-12">
                                  <div class="form-input">
                                    <label for="">Service Description</label>
                                    <input type="text" class="form-control" placeholder="placeholder">
                                  </div>
                                </div>
                                <div class="col-lg-2 col-md-12 col-sm-12">
                                  <div class="form-input">
                                    <label for="">GST</label>
                                    <input type="text" class="form-control" placeholder="placeholder">
                                  </div>
                                </div>
                                <div class="col-lg-2 col-md-12 col-sm-12">
                                  <div class="form-input">
                                    <label for="">Total Amount</label>
                                    <input type="text" class="form-control" placeholder="placeholder">
                                  </div>
                                </div>
                                <div class="col-lg col-md-6 col-sm-6">
                                  <div class="form-check-rcm">
                                    <input type="checkbox" name="" id="">
                                    <label for="">RCM</label>
                                  </div>
                                </div>
                                <div class="col-lg col-md-6 col-sm-6">
                                  <div class="add-btn-plus">
                                    <a style="cursor: pointer" class="btn btn-primary" onclick="addMultiQty(538)">
                                      <i class="fa fa-plus"></i>
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="tab">
                          <input type="checkbox" id="chck2" style="display: none;">
                          <label class="tab-label" for="chck2">Others Cost</label>
                          <div class="tab-content">
                            <div class="row othe-cost-infor modal-add-row_538">
                              <div class="row othe-cost-infor">
                                <div class="col-lg-2 col-md-12 col-sm-12">
                                  <div class="form-input">
                                    <label for="">Vendor Name</label>
                                    <select class="form-control">
                                      <option value="">Tata Consultancy Limited
                                      </option>
                                      <option value="">ITC Limited</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-lg-2 col-md-12 col-sm-12">
                                  <div class="form-input">
                                    <label for="">Amount</label>
                                    <input step="0.01" type="number" class="form-control" placeholder="placeholder">
                                  </div>
                                </div>
                                <div class="col-lg-2 col-md-12 col-sm-12">
                                  <div class="form-input">
                                    <label for="">Service Description</label>
                                    <input type="text" class="form-control" placeholder="placeholder">
                                  </div>
                                </div>
                                <div class="col-lg-2 col-md-12 col-sm-12">
                                  <div class="form-input">
                                    <label for="">GST</label>
                                    <input type="text" class="form-control" placeholder="placeholder">
                                  </div>
                                </div>
                                <div class="col-lg-2 col-md-12 col-sm-12">
                                  <div class="form-input">
                                    <label for="">Total Amount</label>
                                    <input type="text" class="form-control" placeholder="placeholder">
                                  </div>
                                </div>
                                <div class="col-lg col-md-6 col-sm-6">
                                  <div class="form-check-rcm">
                                    <input type="checkbox" name="" id="">
                                    <label for="">RCM</label>
                                  </div>
                                </div>
                                <div class="col-lg col-md-6 col-sm-6">
                                  <div class="add-btn-plus">
                                    <a style="cursor: pointer" class="btn btn-primary" onclick="addMultiQty(538)">
                                      <i class="fa fa-plus"></i>
                                    </a>
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
              </div>

              <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-primary save-close-btn btn-xs float-right">Save & Close</button>
              <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-danger save-close-btn btn-xs float-right">Save as Draft</button>
            </div>
          </div>

          <!-- <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
              <div class="card so-creation-card">
                <div class="card-header">
                  <div class="row customer-info-head">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="head">
                        <i class="fa fa-user"></i>
                        <h4>Customer Info</h4>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-body customer-info">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="row customer-info-form-view">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                          <div class="input-box customer-select">
                            <span class="has-float-label">
                              <select name="customerId" id="customerDropDown" class="form-control" required>
                                <option value="">Select Customer</option>
                              </select>
                              <label for="">Select Customer <span class="text-danger">*</span></label>
                            </span>
                          </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                          <div class="customer-info-text" id="customerInfo">

                          </div>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
              <div class="card so-creation-card">
                <div class="card-header">
                  <div class="row others-info-head">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="head">
                        <i class="fa fa-info"></i>
                        <h4>Others Info</h4>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-body others-info">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="row others-info-form-view">
                        <div class="col-lg-4 col-md-4 col-sm-12">
                          <label>SO Date: <span class="text-danger">*</span></label>
                          <div>
                            <input type="date" value="<?= date("Y-m-d") ?>" name="soDate" id="soDate" class="form-control" required />
                            <span class="input-group-addon"></span>
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                          <label>Delivery Date: <span class="text-danger">*</span></label>
                          <div>
                            <input type="date" value="<?= date("Y-m-d") ?>" name="deliveryDate" id="deliveryDate" class="form-control" required />
                            <span class="input-group-addon"></span>
                          </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-12">
                          <span class="has-float-label">
                            <select name="profitCenter" class="form-control" id="profitCenterDropDown" required>
                              <option value="">Profit Center</option>
                              <?php
                              $funcList = $BranchSoObj->fetchFunctionality()['data'];
                              foreach ($funcList as $func) {
                              ?>
                                <option value="<?= $func['functionalities_id'] ?>"><?= $func['functionalities_name'] ?></option>
                              <?php } ?>
                            </select>
                            <label for="">Profile Center <span class="text-danger">*</span></label>
                          </span>
                        </div>
                      </div>
                      <div class="row others-info-form-view">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                          <span class="has-float-label">
                            <input type="text" name="customerPO" class="form-control" placeholder="customer po number" required />
                            <label for="">Customer PO Number <span class="text-danger">*</span></label>
                          </span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                          <span class="has-float-label">
                            <select name="kamId" class="form-control" id="kamDropDown" required>
                              <option value="">Select KAM</option>
                              <?php
                              $funcList = $BranchSoObj->fetchKamDetails()['data'];
                              foreach ($funcList as $func) {
                              ?>
                                <option value="<?= $func['kamId'] ?>"><?= $func['kamName'] ?></option>
                              <?php } ?>
                            </select>
                            <label for="">Select KAM <span class="text-danger">*</span></label>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div> -->
          <!-- <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
              <div class="card">
                <div class="card-header">
                  <div class="row others-info-head">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="head">
                        <i class="fa fa-info"></i>
                        <h4>Items Info</h4>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-body quickadd form-inline" style="gap: 10px;">
                  <label for="">Quick Add <span class="text-danger">*</span></label>
                  <span class="has-float-label">
                    <select id="itemsDropDown" class="form-control">
                      <option value="">Goods Type</option>
                      <option value="hello">hello</option>
                      <option value="hello1">hello1</option>
                    </select>
                  </span>
                  <a class="btn btn-primary items-search-btn" data-bs-toggle="modal" data-bs-target="#exampleModal"> <i class="fa fa-search mr-2"></i>Advance Search</a>
                  <small class="py-2 px-1 rounded alert-warning specialDiscount" id="specialDiscount" style="display: none;">Special Discount</small>
                  <table class="table-sales-order">
                    <thead>
                      <tr>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>HSN Code</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Disc %</th>
                        <th>Disc. Amt.</th>
                        <th>Tax</th>
                        <th>Total Tax</th>
                        <th>Total Price</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <span id="spanItemsTable"></span>
                    <tbody id="itemsTable"></tbody>
                    <tbody>
                      <tr>
                        <td colspan="7" class="text-right" style="border: none;"> </td>
                        <td colspan="0" class="text-left font-weight-bold totalCal">Sub <sup class="text-primary">[TOTAL]</sup></td>
                        <input type="hidden" name="subTotal" value="0">
                        <td colspan="2" id="grandSubTotalAmt">0.00</th>
                      </tr>
                      <tr>
                        <td colspan="7" class="text-right" style="border: none;"> </td>
                        <td colspan="0" class="text-left font-weight-bold totalCal">TOTAL <sup class="text-danger">[DISCOUNT]</sup></td>
                        <input type="hidden" name="totalDiscount" value="0">
                        <td colspan="2" id="grandTotalDiscount">0.00</td>
                      </tr>
                      <tr>
                        <td colspan="7" class="text-right" style="border: none;"> </td>
                        <td colspan="0" class="text-left font-weight-bold totalCal">TOTAL <sup class="text-info">[TAX]</sup></td>
                        <input type="hidden" name="taxAmount" value="0">
                        <td colspan="2" id="grandTaxAmt">0.00</td>
                      </tr>
                      <tr>
                        <td colspan="7" class="text-right" style="border: none;"> </td>
                        <td colspan="0" class="text-left font-weight-bold totalCal">TOTAL <sup class="text-success">[VALUE]</sup></td>
                        <input type="hidden" name="totalAmt" value="0">
                        <td colspan="2" id="grandTotalAmt">0.00</th>
                      </tr>
                    </tbody>
                    <tfoot>
                      <th colspan="7" class="text-right" style="border: none;"> </th>
                      <th colspan="0" class="text-right" style="border: none;"></th>
                      <td colspan="2" style="border: none;">
                        <button type="submit" name="addNewSOFormSubmitBtn" class="btn btn-primary items-search-btn float-right">Final Submit</button>
                      </td>
                    </tfoot>
                  </table>
                  <div class="modal fade items-filter-modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Advanced Filter Search</h5>
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

                          <div class="card filter-add-item-card">
                            <div class="card-header">
                              <button class="btn btn-primary"><i class="fa fa-plus"></i> Add</button>
                            </div>
                            <div class="card-body">
                              <table class="filter-add-item">
                                <thead>
                                  <tr>
                                    <th><input type="checkbox"></th>
                                    <th>Item Code</th>
                                    <th>Item Code</th>
                                    <th>Item Code</th>
                                    <th>Item Code</th>
                                    <th>Item Code</th>
                                    <th>Item Code</th>
                                    <th>Item Code</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td><input type="checkbox"></td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                  </tr>
                                  <tr>
                                    <td><input type="checkbox"></td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                  </tr>
                                  <tr>
                                    <td><input type="checkbox"></td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                  </tr>
                                  <tr>
                                    <td><input type="checkbox"></td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                  </tr>
                                  <tr>
                                    <td><input type="checkbox"></td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                  <tr>
                                    <td><input type="checkbox"></td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                  <tr>
                                    <td><input type="checkbox"></td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                  <tr>
                                    <td><input type="checkbox"></td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                  <tr>
                                    <td><input type="checkbox"></td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                    <td>12</td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div> -->
      </div>


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
                  <h3 class="card-title">Manage Sales order</h3>
                  <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?customer-so-creation" class="btn btn-sm btn-primary"><i class="fa fa-plus" style="margin-right: 0;"></i></a>
                </li>
              </ul>
            </div>
            <div class="card card-tabs" style="border-radius: 20px;">
              <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                <div class="card-body">
                  <div class="row filter-serach-row">
                    <div class="col-lg-2 col-md-2 col-sm-12">
                      <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-12">
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

                    <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Filter
                              Vendors</h5>

                          </div>
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <input type="text" name="keyword" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php if (isset($_REQUEST['keyword'])) {
                                                                                                                                                      echo $_REQUEST['keyword'];
                                                                                                                                                    } ?>">
                              </div>
                              <div class="col-lg-6 col-md-6 col-sm-6 col-12">
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
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-6 col-md-6 col-sm-6">
                                <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                          echo $_REQUEST['form_date_s'];
                                                                                                                        } ?>" />
                              </div>
                              <div class="col-lg-6 col-md-6 col-sm-6">
                                <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                          echo $_REQUEST['form_date_s'];
                                                                                                                        } ?>" />
                              </div>
                            </div>

                          </div>
                          <div class="modal-footer">
                            <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync fa-spin"></i>Reset</a>
                            <a type="button" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                              Search</a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
              </form>

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
                    $cond .= " AND (`vendor_code` like '%" . $_REQUEST['keyword'] . "%' OR `vendor_name` like '%" . $_REQUEST['keyword'] . "%' OR `vendor_gstin` like '%" . $_REQUEST['keyword'] . "%')";
                  }

                  $sql_list = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE 1 " . $cond . "  AND company_id='" . $_SESSION["logedCustomerAdminInfo"]["company_id"] . "' " . $sts . "  ORDER BY vendor_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                  $qry_list = mysqli_query($dbCon, $sql_list);
                  $num_list = mysqli_num_rows($qry_list);


                  $countShow = "SELECT count(*) FROM `" . ERP_VENDOR_DETAILS . "` WHERE 1 " . $cond . " AND company_id='" . $_SESSION["logedCustomerAdminInfo"]["company_id"] . "' " . $sts . " ";
                  $countQry = mysqli_query($dbCon, $countShow);
                  $rowCount = mysqli_fetch_array($countQry);
                  $count = $rowCount[0];
                  $cnt = $GLOBALS['start'] + 1;
                  $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_VENDOR_DETAILS", $_SESSION["logedCustomerAdminInfo"]["adminId"]);
                  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                  $settingsCheckbox = unserialize($settingsCh);
                  $id = $_SESSION['logedCustomerAdminInfo']['customer_id'];
                  $soList = $BranchSoObj->fetchBranchSoListingByCustomer($id)['data'];
                  //console($soList);
                  if ($soList > 0) {
                  ?>

                    <table class="table defaultDataTable table-hover">

                      <thead>
                        <tr class="alert-light">
                          <th>#</th>
                          <?php if (in_array(1, $settingsCheckbox)) { ?>
                            <th>SO Number</th>
                          <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                            <th>Customer PO</th>
                          <?php }
                          if (in_array(3, $settingsCheckbox)) { ?>
                            <th>Delivery Date</th>
                          <?php  }
                          if (in_array(4, $settingsCheckbox)) { ?>
                            <th>Customer Name</th>
                          <?php }
                          if (in_array(5, $settingsCheckbox)) { ?>
                            <th>Status</th>
                          <?php  }
                          if (in_array(6, $settingsCheckbox)) { ?>
                            <th>Total Items</th>
                          <?php } ?>

                          <th>Action</th>
                        </tr>
                      </thead>


                      <tbody>
                        <?php
                        foreach ($soList as $oneSoList) {
                        ?>
                          <tr>
                            <td><?= $cnt++ ?></td>
                            <?php
                            if (in_array(1, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['so_number'] ?></td>
                            <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['customer_po_no'] ?></td>
                            <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['delivery_date'] ?></td>
                            <?php }
                            if (in_array(4, $settingsCheckbox)) { ?>
                              <td><?= $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0]['trade_name'] ?></td>
                            <?php }
                            if (in_array(5, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['soStatus'] ?></td>
                            <?php }
                            if (in_array(6, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['totalItems'] ?></td>
                            <?php }  ?>
                            <td>
                              <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $oneSoList['so_number'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                            </td>
                          </tr>






                          <?php $customerDetails =  $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0] ?>
                          <div class="modal fade right customer-modal" id="fluidModalRightSuccessDemo_<?= $oneSoList['so_number'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                              <!--Content-->
                              <div class="modal-content">
                                <!--Header-->
                                <div class="modal-header">

                                  <div class="customer-head-info">
                                    <div class="customer-name-code">
                                      <h2 style="font-size: 22px;"><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $oneSoList['totalAmount'] ?></h2>
                                      <p class="heading lead"><?= $oneSoList['so_number'] ?></p>
                                      <p>Cust CO/REF :&nbsp;<?= $oneSoList['customer_po_no'] ?></p>
                                    </div>
                                    <div class="customer-image">
                                      <div class="name-item-count">
                                        <h5 style="font-size: .8rem;"><?= $customerDetails['trade_name'] ?></h5>
                                        <span>
                                          <div class="round-item-count"><?= $oneSoList['totalItems'] ?></div> Items
                                        </span>
                                      </div>
                                      <i class="fa fa-user"></i>
                                    </div>
                                  </div>


                                  <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true" class="white-text">×</span>
                                </button> -->

                                  <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                      <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= $oneSoList['so_number'] ?>" role="tab" aria-controls="home" aria-selected="true">Item Info</a>
                                    </li>
                                    <li class="nav-item">
                                      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile<?= $oneSoList['so_number'] ?>" role="tab" aria-controls="profile" aria-selected="false">Customer Info</a>
                                    </li>
                                  </ul>
                                </div>
                                <div class="modal-body">

                                  <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home<?= $oneSoList['so_number'] ?>" role="tabpanel" aria-labelledby="home-tab">

                                      <form action="" method="POST">
                                        <div class="hamburger">
                                          <div class="wrapper-action">
                                            <i class="fa fa-cog fa-2x"></i>
                                          </div>
                                        </div>
                                        <div class="nav-action" id="settings">
                                          <a title="Delivery Creation" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($oneSoList['so_number']) ?>" name="vendorEditBtn">
                                            <i class="fa fa-box"></i>
                                          </a>
                                        </div>
                                        <div class="nav-action" id="thumb">
                                          <a title="Notify Me" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($oneSoList['so_number']) ?>" name="vendorEditBtn">
                                            <i class="fa fa-bell"></i>
                                          </a>
                                        </div>
                                        <div class="nav-action" id="create">
                                          <a title="Edit" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($oneSoList['so_number']) ?>" name="vendorEditBtn">
                                            <i class="fa fa-edit"></i>
                                          </a>
                                        </div>
                                        <div class="nav-action trash" id="share">
                                          <a title="Delete" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($oneSoList['so_number']) ?>" name="vendorEditBtn">
                                            <i class="fa fa-trash"></i>
                                          </a>
                                        </div>
                                      </form>

                                      <?php
                                      $customerDetails = $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0];
                                      // console($customerDetails);
                                      $customerAddressDetails = $BranchSoObj->fetchCustomerAddressDetails($customerDetails['customer_id'])['data'][0];
                                      ?>
                                      <div class="item-detail-section">
                                        <!-- <h6>Items Details</h6> -->
                                        <?php
                                        $itemDetails = $BranchSoObj->fetchBranchSoItems($oneSoList['so_id'])['data'];
                                        // console($itemDetails);
                                        foreach ($itemDetails as $oneItem) {
                                        ?>
                                          <div class="card">
                                            <div class="card-body">
                                              <div class="row">
                                                <div class="col-lg-8 col-md-8 col-sm-8">
                                                  <div class="left-section">
                                                    <i class="fa fa-box po-list-icon mr-0 ml-0"></i>
                                                    <div class="code-des">
                                                      <h4><?= $oneItem['itemCode'] ?></h4>
                                                      <p><?= $oneItem['itemName'] ?></p>
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                  <div class="right-section">
                                                    <p><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $oneItem['unitPrice'] ?> * <?= $oneItem['qty'] ?> <?= $oneItem['uom'] ?></p>
                                                    <!-- <p><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $oneItem['unitPrice'] * $oneItem['qty'] ?></p> -->
                                                    <div class="discount">
                                                      <p><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $oneItem['unitPrice'] * $oneItem['qty'] ?></p>
                                                      (-<?= $oneItem['totalDiscount'] ?>%)
                                                    </div>
                                                    <p>(GST: <?= $oneItem['tax'] ?>%)</p>
                                                    <div class="font-weight-bold">
                                                      <span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span> <?= $oneItem['totalPrice'] ?>
                                                    </div>
                                                    <!-- <div class="discount">
                                                    <p><?= $oneItem['itemTotalDiscount'] ?></p>
                                                    (-<?= $oneItem['totalDiscount'] ?>%)
                                                  </div> -->
                                                  </div>
                                                </div>
                                              </div>
                                              <hr>
                                              <?php
                                              $deliverySchedule = $BranchSoObj->fetchBranchSoItemsDeliverySchedule2($oneItem['so_item_id'])['data'];
                                              // console($deliverySchedule);
                                              foreach ($deliverySchedule as $dSchedule) {
                                              ?>
                                                <div class="row">
                                                  <div class="col-lg-8 col-md-8 col-sm-8">
                                                    <div class="left-section">
                                                      <i class="fa fa-clock po-list-icon mr-0 ml-0"></i>
                                                      <div class="date-time-parent">
                                                        <div class="date-time">
                                                          <div class="code-des">
                                                            <h4>
                                                              <?php

                                                              echo $dSchedule['delivery_date'];
                                                              ?>
                                                              <small class="text-secondary text-capitalize">(<?= $dSchedule['deliveryStatus'] ?>)</small>
                                                              <?php
                                                              // $date=date_create($dSchedule['delivery_date']);
                                                              // echo date_format($date,"Y/F/d");
                                                              ?>
                                                            </h4>
                                                          </div>
                                                          <p>
                                                            <?php
                                                            // echo $timestamp = $dSchedule['delivery_date'];
                                                            // $dt2 = date("Y", strtotime($timestamp));
                                                            // echo $dt2;
                                                            ?>
                                                          </p>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                  <div class="col-lg-4 col-md-4 col-sm-4">
                                                    <div class="right-section unit">
                                                      <div class="dropdown weight">
                                                        <button class="btn btn-secondary dropdown-toggle date-time-item" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                          <?= $dSchedule['qty'] ?> <?= $oneItem['uom'] ?>
                                                        </button>
                                                      </div>
                                                    </div>

                                                  </div>
                                                </div>
                                              <?php } ?>
                                            </div>
                                          </div>
                                        <?php } ?>
                                      </div>
                                    </div>
                                    <div class="tab-pane fade" id="profile<?= $oneSoList['so_number'] ?>" role="tabpanel" aria-labelledby="profile-tab">
                                      <div class="item-detail-section">
                                        <div class="card">
                                          <div class="card-body">
                                            <div class="row">
                                              <!-- <div class="col-lg-2 col-md-2 col-sm-2">
                                            <div class="icon">
                                              <i class="fa fa-hashtag"></i>
                                            </div>
                                          </div> -->
                                              <div class="col-lg-6 col-md-6 col-sm-6">
                                                <div class="code-des">
                                                  <h4>Code: </h4>
                                                </div>
                                              </div>
                                              <div class="col-lg-6 col-md-6 col-sm-6">
                                                <p>
                                                  <?= $customerDetails['customer_code'] ?>
                                                </p>
                                              </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                              <!-- <div class="col-lg-2 col-md-2 col-sm-2">
                                            <div class="icon">
                                              <i class="fa fa-hashtag"></i>
                                            </div>
                                          </div> -->
                                              <div class="col-lg-6 col-md-6 col-sm-6">
                                                <div class="code-des">
                                                  <h4>Pan:</h4>
                                                </div>
                                              </div>
                                              <div class="col-lg-6 col-md-6 col-sm-6">
                                                <p>
                                                  <?= $customerDetails['customer_pan'] ?>
                                                </p>
                                              </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                              <!-- <div class="col-lg-2 col-md-2 col-sm-2">
                                            <div class="icon">
                                              <i class="fa fa-hashtag"></i>
                                            </div>
                                          </div> -->
                                              <div class="col-lg-6 col-md-6 col-sm-4">
                                                <div class="code-des">
                                                  <h4>GST: </h4>
                                                </div>
                                              </div>
                                              <div class="col-lg-6 col-md-6 col-sm-6">
                                                <p>
                                                  <?= $customerDetails['customer_gstin'] ?>
                                                </p>
                                              </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                              <!-- <div class="col-lg-2 col-md-2 col-sm-2">
                                            <div class="icon">
                                              <i class="fa fa-hashtag"></i>
                                            </div>
                                          </div> -->
                                              <div class="col-lg-6 col-md-6 col-sm-4">
                                                <div class="code-des">
                                                  <h4>Address: </h4>
                                                </div>
                                              </div>
                                              <div class="col-lg-6 col-md-6 col-sm-6">
                                                <p>
                                                  <?= $customerAddressDetails['customer_address_building_no'] . ', ' . $customerAddressDetails['customer_address_flat_no'] . ', ' . $customerAddressDetails['customer_address_street_name'] . ', ' . $customerAddressDetails['customer_address_pin_code'] . ', ' . $customerAddressDetails['customer_address_location'] . ', ' . $customerAddressDetails['customer_address_city'] . ', ' . $customerAddressDetails['customer_address_district'] . ', ' . $customerAddressDetails['customer_address_state'] ?>
                                                </p>
                                              </div>
                                            </div>
                                            <hr>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <!--/.Content-->
                            </div>
                          </div>
                          <!-- right modal end here  -->

                        <?php } ?>

                      </tbody>

                    </table>
                </div>

              <?php } else { ?>
                <table class="table defaultDataTable table-hover text-nowrap">
                  <thead>
                    <tr>
                      <td>

                      </td>
                    </tr>
                  </thead>
                </table>
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
                                SO Number</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                Customer PO Number</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                Delivery Date</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                Customer Name</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                Status</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="6" />
                                Total Items</td>
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
<?php } ?>

<?php
require_once("common/footer.php");
?>

<script>
  $(document).on("click", ".add-btn-minus", function() {
    $(this).parent().parent().remove();
  });

  function addMultiQty(id) {
    let addressRandNo = Math.ceil(Math.random() * 100000);
    $(`.modal-add-row_${id}`).append(`<div class='row othe-cost-infor'>
        <div class="col-lg-2 col-md-12 col-sm-12">
                            <div class="form-input">
                              <label for="">Vendor Select</label>
                              <select class="form-control">
                                <option value="">Tata Consultancy Limited</option>
                                <option value="">ITC Limited</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-12 col-sm-12">
                            <div class="form-input">
                              <label for="">Amount</label>
                              <input step="0.01" type="number" class="form-control" placeholder="placeholder">
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-12 col-sm-12">
                            <div class="form-input">
                              <label for="">Service Description</label>
                              <input type="text" class="form-control" placeholder="placeholder">
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-12 col-sm-12">
                            <div class="form-input">
                              <label for="">GST</label>
                              <input type="text" class="form-control" placeholder="placeholder">
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-12 col-sm-12">
                            <div class="form-input">
                              <label for="">Total Amount</label>
                              <input type="text" class="form-control" placeholder="placeholder">
                            </div>
                          </div>
                          <div class="col-lg col-md-6 col-sm-6">
                            <div class="form-check-rcm">
                              <input type="checkbox" name="" id="">
                              <label for="">RCM</label>
                            </div>
                          </div>
                                          <div class="col-lg-1 col-md-1 col-sm-1">
                                          <div class="add-btn-minus">
                                            <a style="cursor: pointer" class="btn btn-danger" onclick="rm(538)">
                                              <i class="fa fa-minus"></i>
                                            </a>
                                            </div>
                                          </div>
                                          </div>`);
  }

  function addDeliveryQty(id) {
    let addressRandNo = Math.ceil(Math.random() * 100000);
    $(`.modal-add-row-delivery_${id}`).append(`
                                          <div class="row">
                                          <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
                                            <div class="form-input">
                                                <label>Delivery date</label>
                                                <input type="date" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][multiDeliveryDate]" class="form-control" id="delivery-date" placeholder="delivery date" value="">
                                            </div>
                                        </div>
                                        <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                            <div class="form-input">
                                                <label>Quantity</label>
                                                <input type="text" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][quantity]" class="form-control multiQuantity" id="multiQuantity_<?= $randCode ?>" placeholder="quantity" value="">
                                            </div>
                                        </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                    <div class="add-btn-minus">
                                            <a style="cursor: pointer" class="btn btn-danger" onclick="rm(538)">
                                              <i class="fa fa-minus"></i>
                                            </a>
                                            </div>
                                    </div>
                                </div>`);
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
    // customers ********************************
    function loadCustomers() {
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-customers.php`,
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
      if (itemId > 0) {
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
      }
    });
    // **************************************
    function loadItems() {
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-items.php`,
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
      if (itemId > 0) {
        let deliveryDate = $('#deliveryDate').val();
        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-items-list.php`,
          data: {
            act: "listItem",
            itemId,
            deliveryDate
          },
          beforeSend: function() {
            //  $(`#spanItemsTable`).html(`Loding...`);
          },
          success: function(response) {
            console.log(response);
            $("#itemsTable").append(response);
            calculateGrandTotalAmount();
          }
        });
      }
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
          $("#addNewItemsFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');
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

    // 🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴
    // auto calculation 
    function calculateGrandTotalAmount() {
      let totalAmount = 0;
      let totalTaxAmount = 0;
      let totalDiscountAmount = 0;
      $(".itemTotalPrice").each(function() {
        totalAmount += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      });
      $(".itemTotalTax").each(function() {
        totalTaxAmount += (parseFloat($(this).html()) > 0) ? parseFloat($(this).html()) : 0;
      });
      $(".itemTotalDiscount").each(function() {
        totalDiscountAmount += (parseFloat($(this).html()) > 0) ? parseFloat($(this).html()) : 0;
      });
      console.log("Grand = ", totalAmount, totalTaxAmount, totalDiscountAmount);
      let grandSubTotalAmt = totalAmount - totalTaxAmount - totalDiscountAmount;
      $("#grandSubTotalAmt").html(grandSubTotalAmt.toFixed(2));
      $("#grandTotalDiscount").html(totalDiscountAmount.toFixed(2));
      $("#grandTaxAmt").html(totalTaxAmount.toFixed(2));
      $("#grandTotalAmt").html(totalAmount.toFixed(2));
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
      $(`#itemTotalDiscount1_${rowNo}`).val(totalDiscount.toFixed(2));
      $(`#itemTotalTax_${rowNo}`).html(totalTax.toFixed(2));
      $(`#itemTotalTax1_${rowNo}`).val(totalTax.toFixed(2));
      $(`#itemTotalPrice_${rowNo}`).val(totalItemPrice.toFixed(2));
      $(`#itemTotalPrice1_${rowNo}`).html(totalItemPrice.toFixed(2));
      $(`#mainQty_${rowNo}`).html(itemQty);
      calculateGrandTotalAmount();
    }

    // #######################################################
    function calculateQuantity(rowNo, itemId, thisVal) {
      // console.log("code", rowNo);
      let itemQty = (parseFloat($(`#itemQty_${itemId}`).val()) > 0) ? parseFloat($(`#itemQty_${itemId}`).val()) : 0;
      let totalQty = 0;
      // console.log("calculateQuantity() ========== Row:", rowNo);
      // console.log("Total qty", itemQty);
      $(".multiQuantity").each(function() {
        if ($(this).data("itemid") == itemId) {
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
          if ($(this).data("itemid") == itemId) {
            totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            // console.log('Qtys":', $(this).val());
          }
        });
        let avlQty = itemQty - totalQty;

        $(`#mainQtymsg_${itemId}`).show();
        $(`#mainQtymsg_${itemId}`).html("[Error! Delivery QTY should equal to order QTY.]");
        $(`#mainQty_${itemId}`).html(avlQty);
      } else {
        let totalQty = 0;
        $(".multiQuantity").each(function() {
          if ($(this).data("itemid") == itemId) {
            totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            // console.log('Qtys":', $(this).val());
          }
        });

        let avlQty = itemQty - totalQty;

        $(`#mainQtymsg_${itemId}`).hide();
        $(`#mainQty_${itemId}`).html(avlQty);
      }
      if (avlQty == 0) {
        $(`#saveClose_${itemId}`).show();
        $(`#saveCloseLoading_${itemId}`).hide();
      } else {
        $(`#saveClose_${itemId}`).hide();
        $(`#saveCloseLoading_${itemId}`).show();
        $(`#setAvlQty_${itemId}`).html(avlQty);
      }
    }

    function itemMaxDiscount(rowNo, keyValue = 0) {
      let itemMaxDis = $(`#itemMaxDiscount_${rowNo}`).html();
      console.log('this is max discount', itemMaxDis);
      console.log('this is key value', keyValue);
      if (parseFloat(keyValue) > parseFloat(itemMaxDis)) {
        console.log('max discount is over');
        $(`#itemSpecialDiscount_${rowNo}`).text(`Special Discount`);
        $(`#itemSpecialDiscount_${rowNo}`).show();
        // $(`#specialDiscount`).show();
      } else {
        $(`#itemSpecialDiscount_${rowNo}`).hide();
        // $(`#specialDiscount`).hide();
      }
    }

    $(document).on("keyup blur click", ".itemQty", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      calculateOneItemAmounts(rowNo);
    })

    $(document).on("keyup", ".itemDiscount", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let keyValue = $(this).val();
      calculateOneItemAmounts(rowNo);
      itemMaxDiscount(rowNo, keyValue);
      // $(`#itemTotalDiscount1_${rowNo}`).attr('disabled', 'disabled');
    })

    // #######################################################
    $(document).on("keyup blur click change", ".multiQuantity", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let itemid = ($(this).data("itemid"));
      let thisVal = ($(this).val());
      calculateQuantity(rowNo, itemid, thisVal);
    })

    // #######################################################
    $(document).on("keyup", ".itemTotalDiscount1", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let itemDiscountAmt = ($(this).val());

      let itemQty = (parseFloat($(`#itemQty_${rowNo}`).val()) > 0) ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
      let itemUnitPrice = (parseFloat($(`#itemUnitPrice_${rowNo}`).val()) > 0) ? parseFloat($(`#itemUnitPrice_${rowNo}`).val()) : 0;

      let totalAmt = itemQty * itemUnitPrice;
      let discountPercentage = itemDiscountAmt * 100 / totalAmt;

      $(`#itemDiscount_${rowNo}`).val(discountPercentage.toFixed(2));

      // let itemDiscount = (parseFloat($(`#itemDiscount_${rowNo}`).val())) ? parseFloat($(`#itemDiscount_${rowNo}`).val()) : 0;

      console.log('total', itemQty, itemUnitPrice, discountPercentage);
      calculateOneItemAmounts(rowNo);

      // $(`#itemDiscount_${rowNo}`).attr('disabled', 'disabled');
      // discountCalculate(rowNo, thisVal);
    })

    $(function() {
      $("#datepicker").datepicker({
        autoclose: true,
        todayHighlight: true
      }).datepicker('update', new Date());
    });

  })


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
</script>
<script>
  $('.hamburger').click(function() {
    $('.hamburger').toggleClass('show');
    $('#overlay').toggleClass('show');
    $('.nav-action').toggleClass('show');
  });
</script>
<script>
  $(document).on("ready", function() {
    $(".field").on("focus", function() {
      $("body").addClass("is-focus");
    });

    $(".field").on("blur", function() {
      $("body").removeClass("is-focus is-type");
    });

    $(".field").on("keydown", function(event) {
      $("body").addClass("is-type");
      if (event.which === 8 && $(this).val() === "") {
        $("body").removeClass("is-type");
      }
    });
  });
</script>