<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-open-close.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");

$company_data = getCompanyDataDetails($company_id);
$gl_account_length = $company_data['data']['gl_account_length'];
$opening_date = $company_data['data']['opening_date'];



if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

?>
<style>
  
  .content-wrapper{
    overflow: auto !important;
  }
  .content-wrapper table tr.debot-credit-tr td {
    font-size: 12px;
    text-align: left;
    color: #3b3b3b;
    vertical-align: middle;
    background: #f0f5fa;
    padding: 0px 15px;
    white-space: nowrap;
  }

  tbody.debit-credit-1 td {
    padding: 5px;
    border: none;
  }


  tbody.debit-credit-1 tr.debot-credit-tr td {
    background: #b5c5d3;
    text-align: center;
    padding: 0.25rem;
  }

  .green-text {
    color: #14ca14 !important;
    font-weight: 600;
  }

  .red-text {
    color: red !important;
    font-weight: 600;
  }

  p#warningAmountsub {
    position: relative;
    top: 20px;
  }

  .col-storage-location .select2-container {
    display: block;
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

  #selectDebitSub {
    display: block;
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

  .ledger-view-table tbody tr td {
    background: #fff !important;
  }

  .ledger-view-table tbody tr:nth-child(even) td {
    background: #f4f4f496 !important;
  }

  .ledger-list-view {
    overflow-x: auto;
  }

  .ledger-tab .nav-link,
  .ledger-tab .nav-link:hover {
    color: #000;
  }

  .ledger-tab .nav-link.active {
    color: #fff;
    background: #003060;
    border-radius: 5px;
  }

  .ledger-tab .nav-link:not(.active):hover {
    color: #000;
  }

  .ledger-tab .nav-link.active,
  .ledger-tab .nav-link.active:hover {
    color: #fff;
    background: #003060;
    border-radius: 5px;
  }

  footer.main-footer.text-muted {
    display: none !important;
  }

  .is-subLeger-report .subledger-select span.select2.select2-container.select2-container--default {
    width: 47% !important;
  }

  .is-subLeger-report .ledger-select span.select2.select2-container.select2-container--default {
    width: 47% !important;
  }


  @media (max-width: 575px) {
    .ledger-list-view .ledger-view-table tr td {
      white-space: nowrap !important;
    }

  }
</style>

<link rel="stylesheet" href="../../public/assets/listing-new.css">
<link rel="stylesheet" href="../../public/assets/stock-report-new1.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">



<div class="content-wrapper is-invoices is-subLeger-report is-sales-orders vitwo-alpha-global">
  <section class="content">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= LOCATION_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
        <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create</a></li>
        <li class="back-button">
          <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
            <i class="fa fa-reply po-list-icon"></i>
          </a>
        </li>
      </ol>

      <div class="card pgi-body-card bg-white">
        <div class="card-header">
          <div class="head p-2">
            <h4>Ledger View Report</h4>
          </div>
        </div>
        <div class="card-body px-4">
          <div class="pgi-body">
            <div class="row function_row_main">
              <?php

              $chartOfAcc = getAllChartOfAccountsByconditionForMappingLedger($company_id, true);

              // console($chartOfAcc);

              if ($chartOfAcc['status'] == 'success') {
                $list = '';
                foreach ($chartOfAcc['data'] as $chart) {
                  $list .= '<option value="' . $chart['id'] . '" data-attr="' . $chart['gl_label'] . '">' . $chart['gl_label'] . '&nbsp;||&nbsp;' . $chart['gl_code'] . '</option>';
                }
              }



              $subchartOfAcc = queryGet("SELECT customer_code AS code, trade_name AS name, parentGlId, 'Customer' AS type
              FROM erp_customer
              UNION ALL
              SELECT vendor_code AS code, trade_name AS name, parentGlId, 'Vendor' AS type
              FROM erp_vendor_details
              UNION ALL
              SELECT itemCode AS code, itemName AS name, parentGlId, 'Item' AS type
              FROM erp_inventory_items
              UNION ALL
              SELECT sl_code AS code, sl_name AS name, parentGlId, 'SubGL' AS type
              FROM erp_extra_sub_ledger", true);
              // console($chartOfAcc);

              // console($chartOfAcc);

              if ($subchartOfAcc['status'] == 'success') {
                $list = '';
                foreach ($subchartOfAcc['data'] as $subchart) {

                  $list .= '<option value="' . $subchart['code'] . '" data-attr="' . $subchart['parentGlId'] . '">' . $subchart['name'] . '&nbsp;||&nbsp;' . $subchart['code'] . '</option>';
                }
              }

              $function_id = rand(0000, 9999);

              ?>
              <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="form-input function-mapp-main">


                  <div class="row">

                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="d-flex align-items-center gap-4 text-nowrap ledger-select">
                        <label for="" class="mb-0">Select a Ledger</label>
                        <select id="ledger_<?= rand(0000, 9999); ?>" name="gl" class="form-control select2 ledger" required>
                          <option value="">Select G/L</option>
                          <?php if ($chartOfAcc['status'] == 'success') {
                            foreach ($chartOfAcc['data'] as $chart) {

                          ?>
                              <option value=<?= $chart['id'] ?> data-attr='<?= $chart['gl_label'] ?>'><?= $chart['gl_label'] . '&nbsp;||&nbsp;' . $chart['gl_code'] ?></option>
                          <?php
                            }
                          }
                          ?>
                        </select>
                      </div>
                    </div>


                    <div class="col-lg-4 col-md-4 col-sm-4" id="subLedger_div" style="display:none" ;>
                      <div class="d-flex align-items-center gap-4 text-nowrap subledger-select">
                        <label for="" class="mb-0">Select a Sub Ledger</label>
                        <select id="subLedgerList debit_<?= rand(0000, 9999); ?>" name="gl" class="form-control select2 selectDebitSub" required>
                          <option value="">Select Sub Ledger</option>
                          <?php if ($subchartOfAcc['status'] == 'success') {
                            foreach ($subchartOfAcc['data'] as $subchart) {

                          ?>
                              <option value=<?= $subchart['code'] ?> data-parent=<?= $subchart['parentGlId'] ?>><?= $subchart['name'] . '&nbsp;||&nbsp;' . $subchart['code'] ?></option>
                          <?php
                            }
                          }
                          ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-4 text-right">
                      <div class="custom-date-filter d-flex justify-content-end align-items-center gap-4 text-nowrap">
                        <label for="" class="mb-0">Select Date</label>
                        <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="">
                        <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange">
                        <div class="date-range-input d-flex">
                          <div class="form-input">
                            <input type="date" class="form-control" name="from_date" id="from_date" required="" value="<?php echo date('Y-m-01'); ?>">
                          </div>
                          <div class="form-input gap-0">
                            <label class="mb-0 mx-2" for="">To</label>
                            <input type="date" class="form-control" name="to_date" id="to_date" required="" value="<?php echo date('Y-m-t'); ?>">
                          </div>
                          <button type="submit" class="btn btn-primary float-right ml-3" id="rangeid" name="add_date_form">Apply</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="ledger-view"></div>
        </div>


        <!-- global modal -->

        <div class="modal right fade global-view-modal" id="viewGlobalModal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true">
          <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <div class="top-details">
                  <div class="left">
                    <p class="info-detail amount" id="amounts">
                      <ion-icon name="wallet-outline"></ion-icon>
                      <span class="amount-value" id="amount"> </span>
                    </p>
                    <span class="amount-in-words" id="amount-words"></span>
                    <p class="info-detail po-number"><ion-icon name="information-outline"></ion-icon><span id="po-numbers"> </span></p>
                  </div>
                  <div class="right">
                    <p class="info-detail name"><ion-icon name="business-outline"></ion-icon><span id="cus_name"></span></p>
                    <p class="info-detail default-address"><ion-icon name="location-outline"></ion-icon><span id="default_address"></span></p>
                  </div>
                </div>
              </div>
              <div class="modal-body">
                <nav>
                  <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                    <button class="nav-link ViewfirstTab active" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button>
                    <button class="nav-link classicview-btn" id="nav-company-tab" data-id="" data-bs-toggle="tab" data-bs-target="#nav-companyview" type="button" role="tab" aria-controls="nav-classicview" aria-selected="true"><ion-icon name="print-outline"></ion-icon>Company <span id="compCurrencyNavBtn"></span></button>
                    <button class="nav-link classicview-btn customerPrintView" id="nav-customer-tab" data-id="" data-bs-toggle="tab" data-bs-target="#nav-customerview" type="button" role="tab" aria-controls="nav-classicview" aria-selected="true"><ion-icon name="print-outline"></ion-icon>Customer <span id="custInvNav"></span></button>
                    <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
                  </div>
                </nav>
                <div class="tab-content global-tab-content" id="nav-tabContent">

                  <!-- Overview -->
                  <div class="tab-pane fade transactional-data-tabpane show active" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                    <div class="d-flex nav-overview-tabs">

                      <button class="btn btn-sm btn-primary generateEInvoice" id="generateEInvoiceBtn">Generate</button>
                      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" id="generateEwayBillModalBtn" data-bs-target="#generateEBillModal">Generate E-way Bill</button>


                    </div>

                    <div class="row">
                      <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                        <div class="items-table">
                          <h4>Customer Details</h4>
                          <div class="customer-details">
                            <div class="name-code">
                              <div class="details name">
                                <p id="custName"></p>
                              </div>
                              <div class="details code">
                                <p id="custCode"></p>
                              </div>
                            </div>
                            <div class="details gstin">
                              <label for="">GSTIN</label>
                              <p id="custgst"></p>
                            </div>
                            <div class="details pan">
                              <label for="">PAN</label>
                              <p id="custpan"></p>
                            </div>
                            <div class="address-contact">
                              <div class="address-customer">
                                <div class="details">
                                  <label for="">Billing Address</label>
                                  <p id="billAddress" class="pre-normal"></p>
                                </div>
                                <div class="details">
                                  <label for="">Shipping Address</label>
                                  <p class="pre-normal" id="shipAddress"></p>
                                </div>
                                <div class="details">
                                  <label for="">Place of Supply</label>
                                  <p id="placeofSup"></p>
                                </div>
                              </div>
                              <div class="contact-customer">
                                <div class="details dotted-border-area">
                                  <label for="">Contacts</label>
                                  <p> <ion-icon name="mail-outline"></ion-icon><span id="custEmail"> </span></p>
                                  <p> <ion-icon name="call-outline"></ion-icon><span id="custPhone"></span></p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="items-table">
                          <h4>Other Details</h4>
                          <div class="other-info">
                            <div class="details">
                              <label for="">Invoice Date</label>
                              <p id="invDate"></p>
                            </div>
                            <div class="details">
                              <label for="">Invoice Time</label>
                              <p id="invTime"> </p>
                            </div>
                            <!-- <div class="details">
                                                                                        <label for="">Posting Period</label>
                                                                                        <p id="postingPeriod"></p>
                                                                                    </div> -->
                            <!-- <div class="details">
                                                                                        <label for="">Valid Till</label>
                                                                                        <p id="validTill"></p>
                                                                                    </div> -->

                            <div class="details">
                              <label for="">Credit Period</label>
                              <p id="creditPeriod"></p>
                            </div>
                            <div class="details">
                              <label for="">Sales Person</label>
                              <p id="salesPerson"></p>
                            </div>
                            <div class="details">
                              <label for="">Functional Area</label>
                              <p id="funcnArea"></p>
                            </div>
                            <div class="details">
                              <label for="">Compliance Invoice Type</label>
                              <p id="compilaceInv"></p>
                            </div>

                            <!-- <div class="details">
                                                                                        <label for="">Reference Document Link</label>
                                                                                        <p>: <a href="#" id="refDoc"></a></p>
                                                                                    </div>
                                                                                     -->
                          </div>
                        </div>

                      </div>

                      <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <div class="items-view items-calculation" id="item-div-main">
                          <div class="card item-cards">
                            <div class="card-body">
                              <div class="row-section row-first">
                                <div class="left-info">
                                  <ion-icon name="cube-outline"></ion-icon>
                                  <div class="item-info">
                                    <p class="code" id="cardSoNo"></p>
                                    <p class="name" id="cardCustPo"></p>
                                  </div>
                                </div>
                                <div class="right-info">
                                  <div class="item-info">
                                    <p class="code" id="totalItem"></p>
                                    <!-- <p class="name" id="subTotal_inr"></p> -->
                                  </div>
                                </div>
                              </div>
                              <div class="row-section row-tax">
                                <div class="left-info">
                                  <div class="item-info">
                                    <p>Sub Total</p>
                                    <p>Total Discount</p>
                                    <p>Taxable Amount</p>
                                    <p id="igstP" style="display: none;">IGST</p>
                                    <div id="csgst" style="display: none;">
                                      <p>CGST</p>
                                      <p>SGST</p>
                                    </div>
                                  </div>
                                </div>
                                <div class="right-info">
                                  <div class="item-info">
                                    <p id="sub_total"></p>
                                    <p id="totalDis"></p>
                                    <p id="taxableAmt"></p>
                                    <p id="igst"></p>
                                    <div id="csgstVal">
                                      <p id="cgstVal"></p>
                                      <p id="sgstVal"></p>
                                    </div>

                                  </div>
                                </div>
                              </div>
                              <hr>
                              <div class="row-section row-total-amount">
                                <div class="left-info">
                                  <div class="item-info">
                                    <p class="total">Total Amount</p>
                                  </div>
                                </div>
                                <div class="right-info">
                                  <div class="item-info">
                                    <p class="amount" id="total_amount"></p>
                                  </div>
                                </div>
                              </div>
                              <div class="del_status">
                              </div>
                            </div>
                            <div class="items-table">
                              <div class="details">
                                <label for="">Remarks</label>
                                <p id="remark"></p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row orders-table">
                      <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="items-table">
                          <h4>Item Details</h4>
                          <table class="multiple-item-table">
                            <thead>
                              <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>HSN</th>
                                <th>Qty</th>
                                <th>Currency</th>
                                <th>Unit Price</th>
                                <th>Base Amount</th>
                                <th>Discount</th>
                                <th>Taxable Amount</th>
                                <th>GST(%)</th>
                                <th>GST Amount</th>
                                <th>Total Amount</th>
                              </tr>
                            </thead>
                            <tbody id="itemTableBody">

                            </tbody>
                            <!-- <div class="row head-state-table">
                              <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Code</div>
                              <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Name</div>
                              <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">HSN</div>
                              <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Qty</div>
                              <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Currency</div>
                              <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Unit Price</div>
                              <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Base Amount</div>
                              <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Discount</div>
                              <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Taxable Amount</div>
                              <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">GST(%)</div>
                              <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">GST Amount(<span id="currencyHead"></span>)</div>
                              <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Total Amount</div>
                            </div>
                            <div id="itemTableBody">

                            </div> -->
                          </table>
                        </div>
                      </div>
                    </div>


                  </div>



                  <!-- company printView -->
                  <div class="tab-pane fade  classicview-pane " id="nav-companyview" role="tabpanel" aria-labelledby="nav-classicview-tab">

                    <div class="template-div">
                      <h6>Company Copy</h6>
                      <select title="Select Template" class="form-control handleTemplates" id="templateSelectorCompany">
                        <option value="0">Default Template</option>
                        <option value="1">Template 2</option>
                        <option value="2">Template 3</option>
                      </select>
                    </div>

                    <a href="" class="btn btn-primary classic-view-btn float-right" id="classicViewPrintCompany" target="_blank">Print</a>
                    <div class="card classic-view bg-transparent" id="compnayPreview">

                    </div>
                  </div>

                  <!-- customer printView -->
                  <div class="tab-pane fade classicview-pane" id="nav-customerview" role="tabpanel" aria-labelledby="nav-classicview-tab">

                    <div class="template-div">
                      <h3>Customer Copy</h3>
                      <select title="Select Template" class="form-control handleTemplates" id="templateSelectorCustomer">
                        <option value="0">Default Template</option>
                        <option value="1">Template 2</option>
                        <option value="2">Template 3</option>
                      </select>
                    </div>

                    <a href="" class="btn btn-primary classic-view-btn float-right" id="classicViewPrintCustomer" target="_blank">Print</a>
                    <div class="card classic-view bg-transparent" id="customerPreview">


                    </div>
                  </div>

                  <!-- trail -->
                  <div class="tab-pane fade" id="nav-trail" role="tabpanel" aria-labelledby="nav-trail-tab">
                    <div class="inner-content">
                      <div class="audit-head-section mb-3 mt-3 ">
                        <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span><span class="created_by_trail"></span></p>
                        <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span><span class="updated_by"> </span></p>
                      </div>
                      <hr>
                      <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent">


                      </div>
                      <div class="modal fade right audit-history-modal" id="innerModal" role="dialog" aria-labelledby="innerModalLabel" aria-modal="true">
                        <div class="modal-dialog">
                          <div class="modal-content auditTrailBodyContentLineDiv">

                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
              <div class="modal-footer"></div>
            </div>
          </div>
        </div>


        <!--- end global modal --->

        <!-- jouranl global modal -->


        <div class="modal right fade global-view-modal" id="viewJournalGlobalModal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true">
                        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <div class="top-details">
                                <div class="left">
                                  <p class="info-detail amount" id="amounts">
                                    <ion-icon name="wallet-outline"></ion-icon>
                                    <span class="amount-value" id="amount"> </span>
                                  </p>
                                  <span class="amount-in-words" id="amount-words"></span>
                                  <p class="info-detail journal-number"><ion-icon name="information-outline"></ion-icon><span id="journal-numbers"> </span></p>
                                </div>
                                <div class="right">
                                  <p class="info-detail name"><ion-icon name="business-outline"></ion-icon><span id="cus_name"></span></p>
                                  <p class="info-detail default-address"><ion-icon name="location-outline"></ion-icon><span id="default_address">

                                    </span></p>
                                </div>
                              </div>
                            </div>
                            <div class="modal-body">
                              <nav>
                                <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                                  <!-- <button class="nav-link " id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button> -->
                                  <button class="nav-link classicview-btn classicview-link ViewfirstTab active" id="nav-classicview-tab" data-id="" data-bs-toggle="tab" data-bs-target="#nav-classicview" type="button" role="tab" aria-controls="nav-classicview" aria-selected="true"><ion-icon name="print-outline"></ion-icon>Print Preview</button>
                                  <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
                                </div>
                              </nav>
                              <div class="tab-content global-tab-content" id="nav-tabContent">

                                <div class="tab-pane fade transactional-data-tabpane" id="nav-classicview" role="tabpanel" aria-labelledby="nav-overview-tab">
                                  <div class="d-flex nav-overview-tabs">

                                  </div>
                                  <div class="row">
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                      <div class="items-table">
                                        <h4>Customer Details</h4>
                                        <div class="customer-details">
                                          <div class="name-code">
                                            <div class="details name">
                                              <p id="custName"></p>
                                            </div>
                                            <div class="details code">
                                              <p id="custCode"></p>
                                            </div>
                                          </div>
                                          <div class="details gstin">
                                            <label for="">GSTIN</label>
                                            <p id="custgst"></p>
                                          </div>
                                          <div class="details pan">
                                            <label for="">PAN</label>
                                            <p id="custpan"></p>
                                          </div>
                                          <div class="address-contact">
                                            <div class="address-customer">
                                              <div class="details">
                                                <label for="">Billing Address</label>
                                                <p id="billAddress" class="pre-normal"></p>
                                              </div>
                                              <div class="details">
                                                <label for="">Shipping Address</label>
                                                <p class="pre-normal" id="shipAddress"></p>
                                              </div>
                                              <div class="details">
                                                <label for="">Place of Supply</label>
                                                <p id="placeofSup"></p>
                                              </div>
                                            </div>
                                            <div class="contact-customer">
                                              <div class="details dotted-border-area">
                                                <label for="">Contacts</label>
                                                <p> <ion-icon name="mail-outline"></ion-icon><span id="custEmail"> </span></p>
                                                <p> <ion-icon name="call-outline"></ion-icon><span id="custPhone"></span></p>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>

                                      <div class="items-table">
                                        <h4>Other Details</h4>
                                        <div class="other-info">
                                          <div class="details">
                                            <label for="">Posting Date</label>
                                            <p id="postingDate"></p>
                                          </div>
                                          <div class="details">
                                            <label for="">Posting Time</label>
                                            <p id="postingTime"> </p>
                                          </div>
                                          <div class="details">
                                            <label for="">Delivery Date</label>
                                            <p id="delvDate"></p>
                                          </div>
                                          <div class="details">
                                            <label for="">Valid Till</label>
                                            <p id="validTill"></p>
                                          </div>

                                          <div class="details">
                                            <label for="">Credit Period</label>
                                            <p id="creditPeriod"></p>
                                          </div>
                                          <div class="details">
                                            <label for="">Sales Person</label>
                                            <p id="salesPerson"></p>
                                          </div>
                                          <div class="details">
                                            <label for="">Functional Area</label>
                                            <p id="funcnArea"></p>
                                          </div>
                                          <div class="details">
                                            <label for="">Compliance Invoice Type</label>
                                            <p id="compilaceInv"></p>
                                          </div>
                                          <div class="details">
                                            <label for="">Reference Document Link</label>
                                            <p>: <a href="#" id="refDoc"></a></p>
                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                                      <div class="items-view items-calculation" id="item-div-main">
                                        <div class="card item-cards">
                                          <div class="card-body">
                                            <div class="row-section row-first">
                                              <div class="left-info">
                                                <ion-icon name="cube-outline"></ion-icon>
                                                <div class="item-info">
                                                  <p class="code" id="cardSoNo"></p>
                                                  <p class="name" id="cardCustPo"></p>
                                                </div>
                                              </div>
                                              <div class="right-info">
                                                <div class="item-info">
                                                  <p class="code" id="totalItem"></p>
                                                  <!-- <p class="name" id="subTotal_inr"></p> -->
                                                </div>
                                              </div>
                                            </div>
                                            <div class="row-section row-tax">
                                              <div class="left-info">
                                                <div class="item-info">
                                                  <p>Sub Total</p>
                                                  <p>Total Discount</p>
                                                  <p>Taxable Amount</p>
                                                  <p id="igstP">IGST</p>
                                                  <div id="csgst" style="display: none;">
                                                    <p>CGST</p>
                                                    <p>SGST</p>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="right-info">
                                                <div class="item-info">
                                                  <p id="sub_total"></p>
                                                  <p id="totalDis"></p>
                                                  <p id="taxableAmt"></p>
                                                  <p id="igst"></p>
                                                  <div id="csgstVal">
                                                    <p id="cgstVal"></p>
                                                    <p id="sgstVal"></p>
                                                  </div>

                                                </div>
                                              </div>
                                            </div>
                                            <hr>
                                            <div class="row-section row-total-amount">
                                              <div class="left-info">
                                                <div class="item-info">
                                                  <p class="total">Total Amount</p>
                                                </div>
                                              </div>
                                              <div class="right-info">
                                                <div class="item-info">
                                                  <p class="amount" id="total_amount"></p>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="del_status">
                                            </div>
                                          </div>
                                          <div class="items-table">
                                            <div class="details">
                                              <label for="">Remarks</label>
                                              <p id="remark"></p>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>

                                  <div class="row orders-table">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                      <div class="items-table">
                                        <h4>Item Details</h4>
                                        <div class="multiple-item-table">
                                          <div class="row head-state-table">
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Code</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Name</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">HSN</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Qty</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Currency</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Unit Price</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Base Amount</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Discount</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Taxable Amount</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">GST(%)</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">GST Amount(<span id="currencyHead"></span>)</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Total Amount</div>
                                          </div>
                                          <div id="itemTableBody">

                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>


                                </div>

                                <div class="tab-pane classicview-pane fade show active" id="nav-classicview" role="tabpanel" aria-labelledby="nav-classicview-tab">
                                  <a href="" class="btn btn-primary classic-view-btn float-right" id="classicViewPrint" target="_blank">Print</a>
                                  <div class="card classic-view bg-transparent" id="innerClassicView">

                                  </div>
                                </div>
                                <div class="tab-pane fade" id="nav-trail" role="tabpanel" aria-labelledby="nav-trail-tab">
                                  <div class="inner-content">
                                    <div class="audit-head-section mb-3 mt-3 ">
                                      <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span><span class="created_by_trail"></span></p>
                                      <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span><span class="updated_by"> </span></p>
                                    </div>
                                    <hr>
                                    <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent">


                                    </div>
                                    <div class="modal fade right audit-history-modal" id="innerModal" role="dialog" aria-labelledby="innerModalLabel" aria-modal="true">
                                      <div class="modal-dialog">
                                        <div class="modal-content auditTrailBodyContentLineDiv">

                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer"></div>
                          </div>
                        </div>
                      </div>


        <!-- end journal modal -->



      </div>

    </div>
  
  </section>
</div>
<?php
require_once("../common/footer.php");
?>
<script>
  // var selectedOption = <?= $gl ?>;
  // //  alert(selectedOption);
  // let start_date = "<?= $get_start_date ?>";
  // let to_date = "<?= $get_to_date ?>";
  //   get_ledger_report(selectedOption, start_date, to_date);

  // $(document).on("change", '.selectDebitSub', function() {


  //   var dataAttrValue = $(this).find('option:selected').data('attr');
  //   console.log(dataAttrValue);
  //   let valllAc = $(this).val();
  //   // alert(valllAc);

  //   $.ajax({
  //     type: "GET",
  //     url: `<?= LOCATION_URL ?>ajaxs/ledger/ajax-gl-value.php?gl=${valllAc}`,
  //     beforeSend: function() {
  //       // $('.credit-sub').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
  //     },
  //     success: function(response) {
  //       //console.log(11111111);
  //       console.log(response);
  //       $('.ledger-view').html(response);
  //     }
  //   });
  // });

  $('.selectDebitSub')

    .select2()

    .on('select2:open', () => {


    });

  $('.ledger')

    .select2()

    .on('select2:open', () => {


    });


  $(".ledger").on("change", function() {
    // alert(1);
    var selectedOption = $(this).find("option:selected").val();
    //  alert(selectedOption);
    var start_date = $("#from_date").val();
    var to_date = $("#to_date").val();


    $.ajax({
      type: "POST",
      url: `<?= LOCATION_URL ?>ajaxs/ledger/ajax-subledger-list.php`,
      data: {
        selectedOption

      },
      beforeSend: function() {
      //  $('.credit-sub').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
     
      },
      success: function(response) {
        // console.log(response);
        /// alert('ok');
        // alert(response);
        var obj = JSON.parse(response);
        //  alert(obj['list']);
        //  alert(obj['numRows']);
        if (obj['numRows'] > 0) {
          $("#subLedger_div").show();
          $('.selectDebitSub').html(obj['list']);
        } else {
          $("#subLedger_div").hide();
          $('.selectDebitSub').html();
        }
      }
    });
    get_ledger_report(selectedOption, start_date, to_date)

  });



  $(".selectDebitSub").on("change", function() {
    // alert(1);
    var code = $(this).val();
    var start_date = $("#from_date").val();
    var to_date = $("#to_date").val();
    var selectedOption = $(this).find("option:selected");

    // Retrieve the value of data-parent attribute


    var parentgl = selectedOption.data("parent");
    // alert(parentgl);


    get_sub_ledger_report(code, start_date, to_date, parentgl)
    //alert(gl);
  });

  $(document).on("click", '#rangeid', function() {

    //alert(1);
    var code = $(".selectDebitSub").val() ?? '0';
    var start_date = $("#from_date").val();
    var to_date = $("#to_date").val();

    var selectedOption = $('.selectDebitSub').find("option:selected");

    // Retrieve the value of data-parent attribute
    var gl = $('.ledger').val();

    var parentgl = selectedOption.data("parent") ?? '0';
    //  alert(parentgl);
    // alert(code);
    // alert(gl);
    // alert(parentgl);
    if (code != 0 || parentgl == gl) {
      //  alert(0);
      get_sub_ledger_report(code, start_date, to_date, parentgl)
    } else {
      // alert(1);
      get_ledger_report(gl, start_date, to_date)
    }




  });

  function get_ledger_report(gl, start_date, to_date) {
    // alert(1);
    $.ajax({
      type: "POST",
      url: `<?= LOCATION_URL ?>ajaxs/ledger/ajax-gl-value.php`,
      data: {
        start_date,
        to_date,
        gl

      },
      beforeSend: function() {
       $('.ledger-view').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
      },
      success: function(response) {
        //console.log(11111111);
        console.log(response);
        $('.ledger-view').html(response);
      }
    });

  }

  function get_sub_ledger_report(code, start_date, to_date, parentgl) {
    $.ajax({
      type: "POST",
      url: `<?= LOCATION_URL ?>ajaxs/ledger/ajax-subgl-value.php`,
      data: {
        start_date,
        to_date,
        code,
        parentgl

      },
      beforeSend: function() {
       $('.ledger-view').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
      },
      success: function(response) {
        //console.log(11111111);
        console.log(response);
        $('.ledger-view').html(response);
      }
    });

  }
</script>

<script>
 function exportToExcel() {
    var table = document.querySelector('.ledger-view-table');
    var rows = table.querySelectorAll('tr');
    var data = [];

    // Loop through table rows
    rows.forEach((row, rowIndex) => {
        var rowData = [];
        var cells = row.querySelectorAll('th, td');

        cells.forEach((cell, cellIndex) => {
            var cellValue = cell.innerText.trim();

            // Fix the date format issue (assuming the date is in "DD-MM-YYYY")
            if (cellIndex === 0 && rowIndex !== 0) { // Check if it's the date column (index 0)
                let dateParts = cellValue.split('-'); // Split date by '-'
                if (dateParts.length === 3) {
                    cellValue = `${dateParts[0]}-${dateParts[1]}-${dateParts[2]}`; // Convert to "YYYY-MM-DD"
                }
            }

            rowData.push(cellValue);
        });

        data.push(rowData);
    });

    // Convert data array to worksheet
    var ws = XLSX.utils.aoa_to_sheet(data);
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Ledger Report");

    // Save as Excel file
    XLSX.writeFile(wb, 'ledger_report.xlsx');
}

</script>

<script>
  function exportToExcelMonth() {
    // Select the table element containing the ledger report
    var table = document.querySelector('.ledger-view-table-month');

    // Convert the table to a workbook
    var wb = XLSX.utils.table_to_book(table, {
      sheet: "Month on Month Ledger Report"
    });

    // Save the workbook as an Excel file
    XLSX.writeFile(wb, 'ledger_report_month_on_month.xlsx');
  }

  function loadPrintView(invId, invType) {
        let invoiceId = invId;
        let templateId
        let invoiceType
        if (invType == 'company') {
            invoiceType = 'company';
            templateId = $("#templateSelectorCompany").val();
        } else {
            templateId = $("#templateSelectorCustomer").val();
            invoiceType = 'customer';
        }
        console.log("function calling ");
        console.log(invId);
        console.log(invoiceType);
        console.log(templateId);

        $.ajax({
            type: "GET",
            url: "ajaxs/modals/so/ajax-manage-invoices-modal.php",
            data: {
                act: "classicView",
                invoiceId,
                templateId,
                invoiceType
            },
            beforeSend: function() {

            },
            success: function(response) {
                // console.log(response);
                if (invoiceType == "company") {
                    $("#compnayPreview").html(response);
                } else {
                    $("#customerPreview").html(response);
                }

            },
            error: function(error) {
                console.log(error);
            }
        });
    }


  $(document).on("click", ".soModal", function() {
        $('#viewGlobalModal').modal('show');
        $('.ViewfirstTab').tab('show');

        soInvId = $(this).data('id');
        console.log(soInvId);

        // add print href to print button
        let customerPrint=`classic-view/invoice-preview-print.php?invoice_id=${btoa(soInvId)}&type=customer&template_id=0`;
        $('#classicViewPrintCustomer').attr("href", customerPrint);

        let companyPrint=`classic-view/invoice-preview-print.php?invoice_id=${btoa(soInvId)}&type=company&template_id=0`;
        $('#classicViewPrintCompany').attr("href", companyPrint);


        $('.auditTrail').attr("data-ccode", soInvId);
        // adding ccode to for trail
        $('#generateEInvoiceBtn').attr("data-id", soInvId);
        $("#eWayFormInvId").val(soInvId);
        
        // ajax to load modal data
        $.ajax({
            type: "GET",
            url: "ajaxs/modals/so/ajax-manage-invoices-modal.php",
            dataType: 'json',
            data: {
                act: "modalData",
                soInvId
            },
            // beforeSend: function() {

            //     $("#itemTableBody").html('');
            //     $("#generateEwayBillModalBtn").hide();
            //     $("#generateEInvoiceBtn").hide();

            //     let loader = `<div class="load-wrapp" id="globalModalLoader">
            //                         <div class="load-1">
            //                             <div class="line"></div>
            //                             <div class="line"></div>
            //                             <div class="line"></div>
            //                         </div>
            //                     </div>`;

            //     $('#viewGlobalModal .modal-body').append(loader);

            // },
            success: function(response) {
                if (response.status) {

                    let responseObj = response.data;
                    let dataObj = responseObj.dataObj;
                    console.log(responseObj);

                    // Nav head 
                    $(".left #amount").html(`${responseObj.companyCurrency}` + " " + dataObj.all_total_amt);
                    $("#po-numbers").html(dataObj.invoice_no);
                    $("#amount-words").html("(" + responseObj.currecy_name_words + ")");
                    $(".right #cus_name").html(dataObj.trade_name);
                    $("#default_address").html(dataObj.customer_code);
                    $("#compCurrencyNavBtn").html(`(${responseObj.companyCurrency})`);

                    // overview section
                    // overview action button 
                    if (dataObj.irn == null || dataObj.irn == '') {
                        console.log("irn is not defined or null");
                        $("#generateEInvoiceBtn").show();
                    } else {
                        $("#generateEwayBillModalBtn").show();
                    }

                    // customer details section 
                    $("#custName").html(dataObj.trade_name);
                    $("#custCode").html(dataObj.customer_code);
                    $("#custgst").html(dataObj.customer_gstin);
                    // set Title by Given Id 
                  //  setTitleAttributeById('custgst', dataObj.customer_gstin);
                    $("#custpan").html(dataObj.customer_pan);
                    $("#billAddress").html(dataObj.customer_billing_address);
                    $("#shipAddress").html(dataObj.customer_shipping_address);
                    $("#placeofSup").html(dataObj.placeOfSupply + "(" + responseObj.placeOfsupply + ")");
                    $("#custEmail").html(dataObj.customer_authorised_person_email);
                    $("#custPhone").html(dataObj.customer_authorised_person_phone);

                    //others details section
                    //$("#invDate").html(" : " + formatDate(dataObj.invoice_date));
                    $("#invTime").html(" : " + dataObj.invoice_time);
                    $("#delvDate").html(" : " + dataObj.delivery_date);
                    // $("#validTill").html(" : " + dataObj.validityperiod);
                    // $("#cusOrderno").html(" : " + dataObj.customer_po_no);
                    $("#creditPeriod").html(" : " + dataObj.credit_period);
                    $("#salesPerson").html(" : " + dataObj.kamName);
                    $("#funcnArea").html(" : " + dataObj.functionalities_name);
                    $("#compilaceInv").html(" : " + dataObj.compInvoiceType);
                    // $("#refDoc").html(" : " + dataObj.fileName);

                    // card calculation
                    let taxableAmt = 0;
                    let igst = 0;
                    let cgst = 0;
                    let sgst = 0;

                    let subTotal = responseObj.allSubTotal;

                    let totalTax = dataObj.totalTax;
                    let disCount = parseFloat(dataObj.totalDiscount) + parseFloat(dataObj.totalCashDiscount);
                    let totalAmt = dataObj.all_total_amt;


                    if (disCount == 0) {
                        taxableAmt = subTotal;
                    } else {
                        taxableAmt = parseFloat(subTotal) - disCount;
                    }

                    if (dataObj.igst == 0 && (dataObj.cgst > 0 || dataObj.sgst > 0)) {
                        cgst = dataObj.cgst;
                        sgst = dataObj.sgst;
                    } else {
                        igst = dataObj.igst;
                    }

                    // console.log("print")
                    // console.log(`${dataObj.totalDiscount} dis end dis 2  ${dataObj.totalCashDiscount}`);
                    // console.log(disCount)
                    // console.log(taxableAmt)
                    // console.log("print end")



                    // card details section

                    // let num="900877.8900887"
                    // console.log("testing");
                    // console.log(num)
                    
                    // console.log(decimalQuantity(""));
                    // console.log(decimalQuantity(0));
                    
                    // console.log(decimalQuantity(245678.998655));
                    // console.log(decimalQuantity(num));

                    $("#cardSoNo").html(dataObj.so_number);
                    $("#cardCustPo").html(dataObj.customer_po_no);
                    $("#totalItem").html(dataObj.totalItems + " " + "Items");
                    $("#sub_total").html(responseObj.companyCurrency + " " + decimalAmount(subTotal));
                    $("#totalDis").html(responseObj.companyCurrency + " " + decimalAmount(disCount));
                    $("#taxableAmt").html(responseObj.companyCurrency + " " + decimalAmount(taxableAmt));
                    $("#total_amount").html(responseObj.companyCurrency + " " + dataObj.all_total_amt);
                    $("#remark").html(responseObj.dataObj.remarks);

                    if (dataObj.igst == 0 && (dataObj.cgst > 0 || dataObj.sgst > 0)) {
                        $("#csgst").css("display", "block");
                        $("#igstP").css("display", "none");
                        $("#igstP").hide();
                        $("#igst").hide();
                        $("#cgstVal").html(responseObj.companyCurrency + " " + decimalAmount(cgst));
                        $("#sgstVal").html(responseObj.companyCurrency + " " + decimalAmount(sgst));
                    } else if (dataObj.igst > 0) {
                        $("#csgst").css("display", "none");
                        $("#igstP").css("display", "block");
                        $("#igst").html(responseObj.companyCurrency + " " + decimalAmount(igst));
                    }

                    // item table section
                    let itemsObj = responseObj.itemDetail;
                    $.each(itemsObj, function(index, val) {

                        let td = `<tr>
                                                                <td>${val.itemCode}</td>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-elipse w-30 text-dark" title="${val.itemName}">${val.itemName}</div>
                                                                <td>${val.hsnCode}</td>
                                                                <td>${decimalQuantity(val.qty)}</td>
                                                                <td>${responseObj.companyCurrency}</td>
                                                                <td>${responseObj.companyCurrency} ${decimalAmount(val.unitPrice)}</td>
                                                                <td>${responseObj.companyCurrency} ${decimalAmount(val.subTotal)}</td>
                                                                <td>${responseObj.companyCurrency} ${decimalAmount(val.total_discount)}</td>
                                                                <td>${responseObj.companyCurrency} ${decimalAmount(val.taxAbleAmount)}</td>
                                                                <td>${decimalAmount(val.tax)}%</td>
                                                                <td>${responseObj.companyCurrency} ${decimalAmount(val.gstAmount)}</td>
                                                                <td>${responseObj.companyCurrency} ${decimalAmount(val.itemTotalAmount)}</td>
                                                            </tr>
                                                            `;
                        $("#currencyHead").html(responseObj.companyCurrency);
                        $("#itemTableBody").append(td);

                    });


                    // customer printpreview
                    if (dataObj.currency_id != responseObj.compCurrencyId) {
                        $(".customerPrintView").show();
                        $("#custInvNav").html(`(${dataObj.currency_name})`);
                        loadPrintView(soInvId, "customer");
                    } else {
                        $(".customerPrintView").hide();
                    }
                    loadPrintView(soInvId, "company");

                    // trail create and update 
                    $(".created_by_trail").html(responseObj.created_by + "<span class='font-bold text-normal'> on </span>" + responseObj.created_at);
                    $(".updated_by").html(responseObj.updated_by + "<span class='font-bold text-normal'> on </span>" + responseObj.updated_at);

                } else {
                    // console.log(response);
                }
                $("#globalModalLoader").remove();
            },
            complete: function() {
                $("#globalModalLoader").remove();
            }
        });


    });


    $(document).on("change", "#templateSelectorCompany", function() {
        loadPrintView(soInvId, "company");
        let templateId = $("#templateSelectorCompany").val();
        let companyPrint=`classic-view/invoice-preview-print.php?invoice_id=${btoa(soInvId)}&type=company&template_id=${templateId}`;
        $('#classicViewPrintCompany').attr("href", companyPrint);

    })
    // select print template dropdown for customer

    $(document).on("change", "#templateSelectorCustomer", function() {
        loadPrintView(soInvId, "customer");
        let templateId = $("#templateSelectorCustomer").val();
        let customerPrint=`classic-view/invoice-preview-print.php?invoice_id=${btoa(soInvId)}&type=customer&template_id=${templateId}`;
        $('#classicViewPrintCustomer').attr("href", customerPrint);        
    })


$(document).on("click", ".journalModal", function() {

$('#viewJournalGlobalModal').modal('show');
$('.ViewfirstTab').tab('show');
let id = $(this).data('id');
// console.log(id);
$('.auditTrail').attr("data-ccode", id);
$("#innerClassicView").html('');

$.ajax({
  type: "GET",
  url: "ajaxs/modals/fa/ajax-manage-journal-modal.php",
  dataType: 'json',
  data: {
    act: "modalData",
    id
  },
  beforeSend: function() {},
  success: function(value) {
   // console.log(value);
    if (value.status) {
      var responseObj = value.data;
    // console.log(value.data);
      $(".left #amount").html(responseObj.companyCurrency + " " + responseObj.dataObj.total_debit);
      $("#amount-words").html("(" + responseObj.totalDebitInWord + ")");
      $("#journal-numbers").html(responseObj.dataObj.jv_no);
      $(".right #cus_name").html(responseObj.dataObj.documentNo);
    //  $("#default_address").html(formatDate(responseObj.dataObj.postingDate));

      $("#classicViewPrint").attr('href', `classic-view/invoice-preview-print.php?journalId=${btoa(id)}`);
      $(".created_by_trail").html('');
      $(".updated_by").html('');
      // $(".created_by_trail").html(responseObj.created_by + "<span class='font-bold text-normal'> on </span>" + responseObj.created_at);
      // $(".updated_by").html(responseObj.updated_by + "<span class='font-bold text-normal'> on </span>" + responseObj.updated_at);
    }
  },
  error: function(error) {
    console.log(error);
  }
});
$.ajax({
  type: "GET",
  url: "ajaxs/modals/fa/ajax-manage-journal-modal.php",
  data: {
    act: "classicView",
    id
  },
  beforeSend: function() {
    let loader = `<div class="load-wrapp" id="globalModalLoader">
                                <div class="load-1">
                                    <div class="line"></div>
                                    <div class="line"></div>
                                    <div class="line"></div>
                                </div>
                            </div>`;

    // Append the new HTML to the modal-body element
    $('#viewJournalGlobalModal .modal-body').append(loader);

  },
  success: function(response) {
    // console.log(response);
    $("#innerClassicView").html(response);

  },
  complete: function() {
    $("#globalModalLoader").remove();

  },
  error: function(error) {
    console.log(error);
  }
});

});
</script>