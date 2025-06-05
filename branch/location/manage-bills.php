<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-bills-controller.php");


//console($_SESSION);

if (isset($_POST['saveBillFormBtn']) || isset($_POST['draftBillFormBtn'])) {
  $_POST["billStatus"] = "draft";
  if (isset($_POST['saveBillFormBtn'])) {
    $_POST["billStatus"] = "active";
  }
  $_POST["companyId"] = $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"];
  $_POST["branchId"] = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];
  $_POST["adminId"] = $_SESSION["logedBranchAdminInfo"]["adminId"];

  $billObj = new BillController();
  $createBillObj = $billObj->createNewBill($_POST);
  swalToast($createBillObj["status"], $createBillObj["message"]);
}

//console($_POST);


?>
<style>
  .qty button {
    padding: 0;
    margin: 0;
    border-style: none;
    touch-action: manipulation;
    display: inline-block;
    border: none;
    background: none;
    cursor: pointer;
  }

  .step {
    display: none;
  }

  .tab-pane img {
    max-width: 100%;
  }

  /* End Reset for the demo */
  /* Sass Config */
  /* Contrast : 7.2:1 */
  /* End Sass Config */
  .qty {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    text-align: center;
  }

  .qty label {
    flex: 1 0 100%;
  }

  .qty input {
    width: 50px;
    height: 35px;
    font-size: 16px;
    text-align: center;
    border: 1px solid #003060;
  }

  .qty button {
    width: 35px;
    height: 35px;
    color: #fff;
    font-size: 20px;
    background: #003060;
  }

  .qty button.qtyminus {
    margin-right: 0.3rem;
  }

  .qty button.qtyplus {
    margin-left: 0.3rem;
  }

  .buttons {
    position: absolute;
    z-index: 9999;
    right: 0;
    top: 0;
    width: 150px;
    display: flex;
    justify-content: flex-end;
  }

  .zoom-in {
    width: 32px;
    height: 32px;
    background: #737474;
    color: #fff;
    margin-right: 5px;
    border-radius: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
  }

  .zoom-in:hover {
    color: #000;
  }

  .zoom-out {
    width: 32px;
    height: 32px;
    background: #737474;
    color: #fff;
    margin-right: 5px;
    border-radius: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
  }

  .zoom-out:hover {
    color: #000;
  }

  .reset {
    width: 32px;
    height: 32px;
    background: #737474;
    color: #fff;
    margin-right: 5px;
    border-radius: 50px;
    display: flex;
    font-size: 18px;
    align-items: center;
    justify-content: center;
  }

  .reset:hover {
    color: #000;
  }

  .tab-col {
    height: 375px;
    overflow-y: auto;
  }

  .btn-danger,
  .btn-info {
    padding: 0 10px;
  }

  .btn-info {

    background-color: #003060;
    border-color: #003060;
  }

  .form__group {
    position: relative;
    padding: 15px 0 0;
    margin-bottom: 10px;
  }

  .form__field {
    font-family: inherit;
    width: 100%;
    border: 0;
    border-bottom: 1px solid #d2d2d2;
    outline: 0;
    font-size: 12px;
    color: #212121;
    padding: 5px 0;
    background: transparent;
    transition: border-color 0.2s;
  }

  .form__field::placeholder {
    color: transparent;
  }

  .form__field:placeholder-shown~.form__label {
    font-size: 12px;
    cursor: text;
    top: 20px;
  }

  label,
  .form__field:focus~.form__label {
    position: absolute;
    top: 0;
    display: block;
    transition: 0.2s;
    font-size: 12px;
    color: #9b9b9b;
  }

  .form__field:focus~.form__label {
    color: #003060;
  }

  .form__field:focus {
    padding-bottom: 4px;
    border-bottom: 1px solid #003060;
  }
</style>
<?php
if (isset($_GET["upload-bill"])) {
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header mb-2 p-0  border-bottom">
      <?php if (isset($msg)) { ?>
        <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
          <?= $msg ?>
        </div>
      <?php } ?>
      <div class="container-fluid">
        <div class="row pt-2 pb-2">
          <div class="col-md-6">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home"></i>
                  Home</a></li>
              <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark butt">Manage
                  Bills</a></li>
              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Add
                  Bill</a></li>
            </ol>
          </div>

        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row m-0 p-0">
          <div class="col-md-6 ml-auto mr-auto">
            <form action="" method="POST" enctype="multipart/form-data">
              <input type="file" name="billFile" accept=".pdf" class="form-control mt-2" required>
              <button type="submit" name="pdfUploadForm" class="form-control mt-1 btn btn-primary">Upload Bill</button>
              <!-- <div style="display:flex;">
                          <button type="button" class="btn btn-primary mr-2" id="nextBtn" onclick="nextPrev(1)">Next</button>
                          <button type="button" class="btn btn-primary" id="prevBtn" onclick="nextPrev(-1)">Back</button>
                        </div> -->
            </form>
          </div>

          <?php



          $billData = [];
          if (isset($_POST['pdfUploadForm'])) {
            $billFileUploadObj = uploadFile($_FILES["billFile"], "bills/", ["pdf", ".jpeg", "jpg", "png"]);
            if ($billFileUploadObj["status"] == "success") {
              $fullPath = "bills/" . $billFileUploadObj["data"];
              $curl = curl_init();
              curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://ocrserver.centralindia.cloudapp.azure.com:8000/api/v1/ocr/azure/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('file' => new CURLFILE($fullPath)),
              ));

              $response = curl_exec($curl);
              curl_close($curl);

              $responseData = json_decode($response, true);
              //unlink($fullPath);
              if (isset($responseData['payload'])) {
                $billData = $responseData['payload'];
              }
            }
          }

          //console($billData);

          ?>

        </div>
        <?php
        if (isset($_POST["pdfUploadForm"])) {

          $billSubTotal = (isset($billData["SubTotal"]["value"]["amount"])) ? $billData["SubTotal"]["value"]["amount"] : 0;
          $billTotalTax = (isset($billData["TotalTax"]["value"]["amount"])) ? $billData["TotalTax"]["value"]["amount"] : 0;
          $billGrandTotal = (isset($billData["InvoiceTotal"]["value"]["amount"])) ? $billData["InvoiceTotal"]["value"]["amount"] : 0;

          $billCustomerName = (isset($billData["CustomerName"]["value"])) ? $billData["CustomerName"]["value"] : "";
          $billVendorName = (isset($billData["VendorName"]["value"])) ? $billData["VendorName"]["value"] : "";
          $billVendorAddress = (isset($billData["VendorAddress"]["value"])) ? $billData["VendorAddress"]["value"] : "";

          $billInvoiceDate = (isset($billData["InvoiceDate"]["value"])) ? $billData["InvoiceDate"]["value"] : "";

          $billInvoiceNumber = (isset($billData["InvoiceId"]["value"])) ? $billData["InvoiceId"]["value"] : "";

          $billVendorGSTIN = (isset($billData["VendorTaxId"]["value"])) ? $billData["VendorTaxId"]["value"] : "";

          $billOrderNumber = "";
          $billStateCode = "";
          $billDueDate = "";
          $billStateName = "";
          $vendorId = "";

        ?>
          <form action="" method="POST">
            <div class="row">
              <div class="col-md-6 mt-1 d-flex">

              </div>
              <div class="col-md-6 mb-3 mt-1 d-flex align-items-center" style="justify-content:flex-end;">

              </div>
              <div class="col-md-8">
                <div class="card ">
                  <div class="card-header" style="border-top: solid darkblue;">
                    <h4 class="card-title w-100">
                      <a class="d-block w-100 text-dark">
                        New Bill
                      </a>
                    </h4>
                  </div>
                  <div id="collapseOne" class="collapse show" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="hidden" name="vendorId" id="vendorId" value="<?= $vendorId ?>" class="form-control itemTotalPriceInput">
                            <select id="" name="vendor" class="form-control form-control-border borderColor">
                              <option value="">Select Vendor</option>
                              <option value="<?= $billVendorName ?>" selected><?= $billVendorName ?></option>
                            </select>
                            <small><? $billVendorAddress ?></small>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form__group">
                            <input type="text" class="form__field" name="billDate" placeholder="Bill Date" value="<?= $billInvoiceDate ?>" autocomplete="off">
                            <label for="" class="form__label">Bill Date</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form__group">
                            <input type="text" name="vendorBillNumber" value="<?= $billInvoiceNumber ?>" class="form__field" id="exampleInputBorderWidth2" placeholder="Vendor Bill No">
                            <label for="exampleInputBorderWidth2" class="form__label">Vendor Bill No.</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form__group">
                            <input type="text" name="billRefNumber" value="" class="form__field" id="exampleInputBorderWidth2" placeholder="Bill Number">
                            <label for="exampleInputBorderWidth2" class="form__label">Bill Ref Number</label>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form__group">
                            <input type="date" name="billDueDate" value="<?= $billDueDate ?>" class="form__field active" id="exampleInputBorderWidth2" placeholder="">
                            <label for="exampleInputBorderWidth2" class="form__label">Due Date</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form__group">
                            <input type="text" name="billOrderNumber" value="<?= $billOrderNumber ?>" class="form__field active" id="exampleInputBorderWidth2">
                            <label for="exampleInputBorderWidth2" class="form__label">Order Number</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form__group">
                            <input type="text" name="billStateCode" value="<?= $billStateCode ?>" class="form__field active" id="exampleInputBorderWidth2">
                            <label for="exampleInputBorderWidth2" class="form__label">State Code</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form__group">
                            <input type="text" name="billStateName" value="<?= $billStateName ?>" class="form__field active" id="exampleInputBorderWidth2">
                            <label for="exampleInputBorderWidth2" class="form__label">State Name</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <select id="" name="vendorGstin" class="form-control form-control-border borderColor">
                              <option value="">GSTIN</option>
                              <option value="<?= $billVendorGSTIN ?>" selected><?= $billVendorGSTIN ?></option>
                            </select>
                            <label class="form__label">Vendor GSTIN</label>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="input-group">

                            <select name="billToAddress" class=" form-control form-control-border borderColor">
                              <option value="">Bill To Address</option>
                              <option value="<?= $billCustomerName ?>" selected><?= $billCustomerName ?></option>
                            </select>
                            <label class="form__label">Bill To Address</label>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form__group">
                            <input type="text" name="billNote" class="form__field active" id="exampleInputBorderWidth2">
                            <label for="exampleInputBorderWidth2" class="form__label">Notes</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
              <div class="col-md-4">
                <div class="card card-primary card-outline card-tabs">
                  <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true">Suggestions</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false">Uploaded Bill</a>
                      </li>
                    </ul>
                  </div>
                  <div class="card-body fontSize">
                    <div class="tab-content tab-col" id="custom-tabs-three-tabContent">
                      <div class="tab-pane fade show active" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                        Suggestions yet not available!
                      </div>
                      <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab">
                        <!-- <div>
                              <div class="step">
                                <div id="panzoom" style="text-align: center">
                                  <img id="pdf_preview">
                                  <span id="preview_msg">Preview loading</span>
                                </div>
                                <section class="buttons">
                                  <a class="zoom-in"><i class="fas fa-search-plus"></i></a>
                                  <a class="zoom-out"><i class="fas fa-search-minus"></i></a>
                                  <a class="reset"><i class="fas fa-search-minus"></i></a>
                                </section>
                              </div>

                              <div class="step">
                                <input type="text" id="your_name">
                              </div>
                              <canvas id="the-canvas" style="direction: ltr; display: none"></canvas>
                            </div> -->
                        <div>
                          <!-- <object data='http://localhost/vitwo-alpha/branch/<?= $fullPath ?>' type="application/pdf" width="500" height="678">
                                
                              </object> -->
                          <iframe src='<?= BRANCH_URL . $fullPath ?>' width="500" height="678">
                            <p>This browser does not support PDF!</p>
                          </iframe>
                        </div>

                      </div>
                    </div>
                  </div>
                  <!-- /.card -->
                </div>
                <!-- <div class="w-100 mt-3">
                    <button type="submit" name="addInventoryItem" class="btn-primary mb-3 btnstyle btn btn-block btn-sm">
                      <i class="fa fa-plus fontSize"></i>
                      Add New
                    </button>
                  </div> -->
              </div>
            </div>
            <div class="card ">
              <div class="row">
                <div class="col-md-12">
                  <table class="table form-table customFields">

                    <tr>
                      <th scope="col" width="10%">Items</th>
                      <th scope="col" width="15%">Hsn</th>
                      <th scope="col" width="35%">Description</th>
                      <th scope="col" width="10%">Quantity</th>
                      <th scope="col" width="10%">Price</th>
                      <th scope="col" width="10%">Amount</th>
                      <th scope="col" width="10%"> <a href="javascript:void(0);" class="addCF btn btn-info"><i class="fa fa-plus"></i></a></th>
                    </tr>

                    <?php
                    if (isset($billData['Items']) && count($billData['Items']) > 0) {
                      foreach ($billData['Items']['value'] as $oneItemObj) {
                        $oneItem = $oneItemObj['value'];

                        $oneItemName = (isset($oneItem['Description']['value'])) ? $oneItem['Description']['value'] : "";
                        $oneItemHSN = "";
                        $oneItemDescription = (isset($oneItem['Description']['value'])) ? $oneItem['Description']['value'] : "";
                        $oneItemQuantity = (isset($oneItem['Quantity']['value'])) ? $oneItem['Quantity']['value'] : "";
                        $oneItemUnitPrice = (isset($oneItem['UnitPrice']['value']['amount'])) ? $oneItem['UnitPrice']['value']['amount'] : "";
                        $oneItemTotalPrice = (isset($oneItem['Amount']['value']['amount'])) ? $oneItem['Amount']['value']['amount'] : "";


                    ?>

                        <tr>
                          <td width="10%">
                            <input type="text" name="itemName[]" class="form-control" placeholder="Name" value="<?= $oneItemName ?>" readonly="readonly">
                          </td>
                          <td width="15%">
                            <input type="text" name="itemHSN[]" class="form-control" placeholder="HSN" value="<?= $oneItemHSN ?>">
                          </td>
                          <td width="35%">
                            <input type="text" name="itemDescription[]" class="form-control" placeholder="Description" value="<?= $oneItemDescription ?>">
                          </td>
                          <td width="10%">
                            <input step="0.01" type="number" name="itemQuantity[]" class="form-control itemQuantityInput" min="1" value="<?= $oneItemQuantity ?>">
                          </td>
                          <td width="10%">
                            <input step="0.01" type="number" name="itemUnitPrice[]" class="form-control itemUnitPriceInput" value="<?= $oneItemUnitPrice ?>">
                          </td>
                          <td width="10%" class="pt-3">
                            <input type="hidden" name="itemTotalPrice[]" class="form-control itemTotalPriceInput" value="<?= $oneItemTotalPrice ?>">
                            <span class="itemTotalPriceSpan"><?= number_format($oneItemTotalPrice, 2) ?></span>
                          </td>
                          <td width="10%">
                            <button class="btn btn-danger remove"><i class="fa fa-times" aria-hidden="true"></i></button>
                          </td>
                        </tr>
                      <?php
                      }
                    } else {
                      ?>
                      <tr>
                        <td width="10%">
                          <input type="text" name="itemName[]" class="form-control" placeholder="Name" value="Laptop" readonly="readonly">
                        </td>
                        <td width="15%">
                          <input type="text" name="itemHSN[]" class="form-control" placeholder="HSN">
                        </td>
                        <td width="35%">
                          <input type="text" name="itemDescription[]" class="form-control" placeholder="Description">
                        </td>
                        <td width="10%">
                          <input step="0.01" type="number" name="itemQuantity[]" class="form-control itemQuantityInput" min="1" value="1">
                        </td>
                        <td width="10%">
                          <input step="0.01" type="number" name="itemUnitPrice[]" class="form-control itemUnitPriceInput" value="0.00">
                        </td>
                        <td width="10%" class="pt-3">
                          <input type="hidden" name="itemTotalPrice[]" class="form-control itemTotalPriceInput">
                          <span class="itemTotalPriceSpan">0.00</span>
                        </td>
                        <td width="10%">
                          <button class="btn btn-danger remove"><i class="fa fa-times" aria-hidden="true"></i></button>
                        </td>
                      </tr>
                    <?php
                    }

                    ?>

                  </table>
                </div>

              </div>

              <div class="row pl-3 pr-3">
                <div class="col-md-6"></div>
                <div class="col-md-6" style="text-align:right;">
                  <table style="width:100%;">
                    <tr>
                      <td style="text-align:right;">
                        <p>Subtotal</p>
                      <td>
                      <td style="text-align:right;"><i class="fa fa-inr" aria-hidden="true"></i>
                        <input type="hidden" name="billSubTotal" id="billSubTotalInput" value="<?= $billSubTotal; ?>">
                        <span id="billSubTotalSpan"><?= number_format($billSubTotal, 2); ?></span>
                      <td>
                    </tr>
                    <tr>
                      <td style="text-align:right;">
                        <p>Tax</p>
                      <td>
                      <td style="text-align:right;"><i class="fa fa-inr" aria-hidden="true"></i> <?php echo $billTotalTax; ?>
                      <td>
                    </tr>
                    <tr>
                      <td style="text-align:right;">
                        <p>Add Discount</p>
                      <td>
                      <td style="text-align:right;"><i class="fa fa-inr" aria-hidden="true"></i> 0.00
                      <td>
                    </tr>
                    <tr>
                      <td class="d-flex align-items-center" style="text-align:right;justify-content:flex-end;">
                        <P class="mb-0 mr-2">Grand Total</p>
                        <select name="billCurrency" class="form-control form-control-border borderColor" style="width:100px">
                          <option value="">Indian Rupe</option>

                        </select>
                      <td>
                      <td style="text-align:right;">
                        <input type="hidden" name="billGrandTotal" id="billGrandTotalInput" value="<?= $billSubTotal ?>">
                        <span id="billGrandTotalSpan"><?= number_format($billGrandTotal, 2) ?></span>
                      <td>
                    </tr>
                  </table>
                </div>
                <div class="col-md-12 mb-4 mt-4 d-flex" style="justify-content:flex-end;">
                  <input type="submit" name="saveBillFormBtn" value="Save" class="btn btn-primary btnstyle">
                  <input type="submit" name="draftBillFormBtn" value="Draft" class="btn-danger ml-2 btn btnstyle">
                </div>
              </div>
            </div>
          </form>
        <?php
        }

        ?>
      </div>
    </section>
    <!-- /.content -->
  </div>
<?php
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
            <div class="card card-tabs">
              <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                  <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                    <h3 class="card-title">Manage Bills</h3>
                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?upload-bill" class="btn btn-sm btn-primary btnstyle m-2"><i class="fa fa-plus"></i> Upload bill</a>
                  </li>
                </ul>
              </div>
              <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                <div class="card-body">
                  <div class="filter-col">
                    <div class="row">
                      <div class="col-md-2">
                        <div class="input-group">
                          <select name="bill_status_s" id="bill_status_s" class="form-control form-control-border borderColor">
                            <option value="">--- Status --</option>
                            <option value="active" <?php if (isset($_REQUEST['bill_status_s']) && 'active' == $_REQUEST['bill_status_s']) {
                                                      echo 'selected';
                                                    } ?>>Active</option>
                            <option value="inactive" <?php if (isset($_REQUEST['bill_status_s']) && 'inactive' == $_REQUEST['bill_status_s']) {
                                                        echo 'selected';
                                                      } ?>>Inactive</option>
                            <option value="draft" <?php if (isset($_REQUEST['bill_status_s']) && 'draft' == $_REQUEST['bill_status_s']) {
                                                    echo 'selected';
                                                  } ?>>Draft</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="input-group"> <input class="fld" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                              echo $_REQUEST['form_date_s'];
                                                                                                                            } ?>" />
                        </div>
                      </div>

                      <div class="col-md-2">
                        <div class="input-group"> <input class="fld" type="date" name="to_date_s" id="to_date_s" value="<?php if (isset($_REQUEST['to_date_s'])) {
                                                                                                                          echo $_REQUEST['to_date_s'];
                                                                                                                        } ?>" />
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="input-group">
                          <input type="text" name="keyword" class="m-input" id="keyword" placeholder="Enter Keyword" value="<?php if (isset($_REQUEST['keyword'])) {
                                                                                                                              echo $_REQUEST['keyword'];
                                                                                                                            } ?>">
                          <!--<label>Keyword</label>-->
                        </div>
                      </div>
                      <div class="col-md-3" style="display: flex;">
                        <button type="submit" class="btn btn-primary btnstyle">Search</button> &nbsp;
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger btnstyle">Reset</a>
                      </div>
                    </div>
                  </div>
              </form>
              <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog" aria-hidden="true"></i></a>
              <div class="tab-content" id="custom-tabs-two-tabContent">
                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                  <?php
                  $cond = '';

                  $sts = " AND `bill_status` !='deleted'";
                  if (isset($_REQUEST['bill_status_s']) && $_REQUEST['bill_status_s'] != '') {
                    $sts = ' AND bill_status="' . $_REQUEST['bill_status_s'] . '"';
                  }

                  if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                    $cond .= " AND bill_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                  }

                  if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                    $cond .= " AND (`bill_number` like '%" . $_REQUEST['keyword'] . "%' OR `bill_ref_number` like '%" . $_REQUEST['keyword'] . "%' OR `bill_gstin` like '%" . $_REQUEST['keyword'] . "%')";
                  }

                  $sql_list = "SELECT * FROM `" . ERP_PURCHASE_BILLS . "` WHERE 1 " . $cond . "  AND company_id='" . $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] . "' " . $sts . "  ORDER BY bill_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                  $qry_list = mysqli_query($dbCon, $sql_list);
                  $num_list = mysqli_num_rows($qry_list);


                  $countShow = "SELECT count(*) FROM `" . ERP_PURCHASE_BILLS . "` WHERE 1 " . $cond . " AND company_id='" . $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] . "' " . $sts . " ";
                  $countQry = mysqli_query($dbCon, $countShow);
                  $rowCount = mysqli_fetch_array($countQry);
                  $count = $rowCount[0];
                  $cnt = $GLOBALS['start'] + 1;
                  $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_PURCHASE_BILLS", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                  $settingsCheckbox = unserialize($settingsCh);
                  if ($num_list > 0) {
                  ?>
                    <table id="mytable" class="table defaultDataTable table-hover text-nowrap">
                      <thead>
                        <tr>
                          <th>#</th>
                          <?php if (in_array(1, $settingsCheckbox)) { ?>
                            <th>Bill Number</th>
                          <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                            <th>Ref Number</th>
                          <?php }
                          if (in_array(3, $settingsCheckbox)) { ?>
                            <th>Billed Date</th>
                          <?php  }
                          if (in_array(4, $settingsCheckbox)) { ?>
                            <th>Total Amount</th>
                          <?php }  ?>
                          <th>Status</th>

                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        while ($row = mysqli_fetch_assoc($qry_list)) {
                        ?>
                          <tr>
                            <td><?= $cnt++ ?></td>
                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                              <td><?= $row['bill_number'] ?></td>
                            <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                              <td><?= $row['bill_ref_number'] ?></td>
                            <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                              <td><?= $row['billed_date'] ?></td>
                            <?php }
                            if (in_array(4, $settingsCheckbox)) { ?>
                              <td><?= $row['bill_total_amount'] ?></td>
                            <?php } ?>
                            <td>
                              <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['bill_id'] ?>">
                                <input type="hidden" name="changeStatus" value="active_inactive">
                                <button <?php if ($row['bill_status'] == "draft") { ?> type="button" style="cursor: inherit; border:none" <?php } else { ?>type="submit" onclick="return confirm('Are you sure change bill_status?')" style="cursor: pointer; border:none" <?php } ?> class="p-0 m-0 ml-2" data-toggle="tooltip" data-placement="top" title="<?php echo $row['bill_status'] ?>">
                                  <?php if ($row['bill_status'] == "active") { ?>
                                    <span class="badge badge-success"><?php echo ucfirst($row['bill_status']); ?></span>
                                  <?php } else if ($row['bill_status'] == "inactive") { ?>
                                    <span class="badge badge-danger"><?php echo ucfirst($row['bill_status']); ?></span>
                                  <?php } else if ($row['bill_status'] == "draft") { ?>
                                    <span class="badge badge-warning"><?php echo ucfirst($row['bill_status']); ?></span>
                                  <?php } ?>

                                </button>
                              </form>
                            </td>
                            <td>

                              <a href="<?= basename($_SERVER['PHP_SELF']) . "?view=" . $row['bill_id']; ?>" style="cursor: pointer;" class="btn btn-sm"><i class="fa fa-eye"></i></a>
                              <a href="<?= basename($_SERVER['PHP_SELF']) . "?edit=" . $row['bill_id']; ?>" style="cursor: pointer;" class="btn btn-sm"><i class="fa fa-edit"></i></a>
                              <form action="" method="POST" class="btn btn-sm">
                                <input type="hidden" name="id" value="<?php echo $row['bill_id'] ?>">
                                <input type="hidden" name="changeStatus" value="delete">
                                <button type="submit" onclick="return confirm('Are you sure to delete?')" style="cursor: pointer; border:none"><i class='fa fa-trash '></i></button>
                              </form>
                            </td>
                          </tr>
                        <?php  } ?>
                      <tfoot>
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
                      </tfoot>
                      </tbody>

                    </table>
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
                      <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                      <input type="hidden" name="pageTableName" value="ERP_PURCHASE_BILLS" />
                      <div class="modal-body">
                        <div id="dropdownframe"></div>
                        <div id="main2">
                          <table>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                Bill Number</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                Ref Number</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                Billed Date</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                Total Amount</td>
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

?>
<?php
require_once("../common/footer.php");
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://unpkg.com/pdfjs-dist/build/pdf.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.panzoom/2.0.6/jquery.panzoom.min.js"></script>
<script>
  $("#panzoom").panzoom({
    $zoomIn: $(".zoom-in"),
    $zoomOut: $(".zoom-out"),
    $zoomRange: $(".zoom-range"),
    $reset: $(".reset"),
    contain: 'invert',
  });
</script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-zoom/1.7.21/jquery.zoom.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function() {
    $('.select2')
      .select2()
      .on('select2:open', () => {
        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal">
    Add New
  </a></div>`);
      });
    $('.select3')
      .select2()
      .on('select2:open', () => {
        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModalGroup">
    Add New
  </a></div>`);
      });
    $('.select5')
      .select2()
      .on('select2:open', () => {
        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModalUOM">
    Add New
  </a></div>`);
      });
  });
</script>
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

  var inputs = document.getElementsByClassName("form__field");
  for (var i = 0; i < inputs.length; i++) {
    var el = inputs[i];
    el.addEventListener("blur", function() {
      leaveInput(this);
    });
  }

  // *** autocomplite select *** //
  $(document).ready(function() {
    $(".addCF").click(function() {
      $(".customFields").append(`
                <tr>
                  <td width="10%">
                    <input type="text" class="form-control" placeholder="Name">
                  </td>
                  <td width="15%">
                    <input type="text" class="form-control" placeholder="HSN">
                  </td>
                  <td width="35%">
                    <input type="text" class="form-control" placeholder="Description">
                  </td>
                  <td width="10%">
                    <input step="0.01" type="number" class="form-control" name="qty" id="qty" min="1" value="1">
                  </td>
                  <td width="10%">
                    <input step="0.01" type="number" class="form-control" value="0.00">
                  </td>
                  <td width="10%" class="pt-3">
                    <span>0.00</span>
                  </td>
                  <td width="10%">
                    <button class="btn btn-danger remove"><i class="fa fa-times" aria-hidden="true"></i></button>
                  </td>
                </tr>
      `);
    });

    $(document).on('click', '.remove', function() {

      $(this).parents('tr').remove();
    });

    $('.feature-image')
      .wrap('<span style="display:block;"></span>')
      .css('display', 'block')
      .parent()
      .zoom();
  });



  var input = document.querySelector('#qty');
  var btnminus = document.querySelector('.qtyminus');
  var btnplus = document.querySelector('.qtyplus');

  if (input !== undefined && btnminus !== undefined && btnplus !== undefined && input !== null && btnminus !== null && btnplus !== null) {

    var min = Number(input.getAttribute('min'));
    var max = Number(input.getAttribute('max'));
    var step = Number(input.getAttribute('step'));

    function qtyminus(e) {
      var current = Number(input.value);
      var newval = (current - step);
      if (newval < min) {
        newval = min;
      } else if (newval > max) {
        newval = max;
      }
      input.value = Number(newval);
      e.preventDefault();
    }

    function qtyplus(e) {
      var current = Number(input.value);
      var newval = (current + step);
      if (newval > max) newval = max;
      input.value = Number(newval);
      e.preventDefault();
    }

    btnminus.addEventListener('click', qtyminus);
    btnplus.addEventListener('click', qtyplus);

  } // End if test


  // pdf zoom //

  var currentTab = 0; // Current tab is set to be the first tab (0)
  showTab(currentTab); // Display the current tab

  // Setup the webworker so rendering doesn't block the main UI thread
  pdfjsLib.GlobalWorkerOptions.workerSrc = "https://unpkg.com/pdfjs-dist/build/pdf.worker.js"


  window.onload = function() {
    var pdf = document.getElementById("the_pdf");
    pdf.onchange = function(ev) {
      document.getElementById("preview_msg").style.display = "unset";
      file = document.getElementById("the_pdf").files[0];

      fileReader = new FileReader();
      fileReader.onload = function(ev) {
        console.log("loading PDF");
        pdfjsLib.getDocument({
          data: fileReader.result
        }).promise.then((pdf) => {
          pdf.getPage(1).then(function(page) {
            var desiredWidth = 1200;
            var viewport = page.getViewport({
              scale: 1
            });
            var scale = desiredWidth / viewport.width;
            var scaledViewport = page.getViewport({
              scale: scale
            });

            var canvas = document.getElementById("the-canvas");
            var context = canvas.getContext("2d");

            canvas.height = scaledViewport.height;
            canvas.width = scaledViewport.width;

            var renderContext = {
              canvasContext: context,
              viewport: scaledViewport
            };
            var renderTask = page.render(renderContext);
            renderTask.promise.then(function() {
              console.log("Page rendered");
              document.getElementById("preview_msg").style.display = "none";
              var png_data = canvas.toDataURL('image/png')
              document.getElementById("pdf_preview").src = png_data;
            });
          });
        });
      };
      fileReader.readAsArrayBuffer(file);
    };
  };

  function showTab(n) {
    // This function will display the specified tab of the form
    var x = document.getElementsByClassName("step");
    x[n].style.display = "block";
    // ... and fix the Previous/Next buttons:
    if (n == 0) {
      document.getElementById("prevBtn").style.display = "none";
    } else {
      document.getElementById("prevBtn").style.display = "inline";
      document.getElementById("nextBtn").style.display = "none";
    }
    if (n == x.length - 1) {
      document.getElementById("nextBtn").innerHTML = "Submit";
    } else {
      document.getElementById("nextBtn").innerHTML = "Next";
    }
  }

  function nextPrev(n) {
    console.log("next is", currentTab + n);
    // This function will figure out which tab to display
    var x = document.getElementsByClassName("step");

    // Hide the current tab:
    x[currentTab].style.display = "none";
    // Increase or decrease the current tab by 1:
    currentTab = currentTab + n;
    // if you have reached the end of the form... :
    if (currentTab >= x.length) {
      //...the form gets submitted:
      document.getElementById("pdfUpload").submit();
      return false;
    }
    // Otherwise, display the correct tab:
    showTab(currentTab);
  }


  $(document).ready(function() {

    $(document).on('submit', '#pdfUploadForm', function(event) {
      event.preventDefault();

      let formData = $("#pdfUploadForm").serialize();

      console.log(formData);
      // var form = new FormData();
      // form.append("file", fileInput.files[0]);

      // var settings = {
      //   "url": "http://ocrserver.centralindia.cloudapp.azure.com:8000/api/v1/ocr/azure/",
      //   "method": "POST",
      //   "timeout": 0,
      //   "processData": false,
      //   "mimeType": "multipart/form-data",
      //   "contentType": false,
      //   "data": form
      // };

      // $.ajax(settings).done(function(response) {
      //   console.log(response);
      // });
    });
  });
</script>