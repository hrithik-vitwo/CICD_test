<?php
require_once("../../app/v1/connection-company-admin.php");
$pageName =  basename($_SERVER['PHP_SELF'], '.php');
//administratorLocationAuth();
if (!isset($_SESSION["logedCompanyAdminInfo"]["adminId"]) || !isset($_SESSION["logedCompanyAdminInfo"]["adminRole"])) {
  echo "Session Timeout";
  exit;
}
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

// Add Functions
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");


if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}



?>


<style>
  .chartContainer {
    width: 100%;
    height: 500px;
    margin-top: 6em;
  }

  .content-wrapper table tr:nth-child(2n+1) td {
    background: #b5c5d3;
  }

  tfoot.individual-search tr th {
    padding: 5px !important;
    border-right: 1px solid #fff !important;
  }

  .vertical-align {
    vertical-align: middle;
  }

  /* .green-text {
    color: #14ca14 !important;
    font-weight: 600;
  }

  .red-text {
    color: red !important;
    font-weight: 600;
  } */

  .dataTables_scrollHeadInner tr th {
    position: sticky;
    top: -1px;
  }

  div.dataTables_wrapper div.dataTables_filter,
  .dataTables_wrapper .row {
    display: flex !important;
    align-items: center;
    justify-content: end;
  }

  /* div.dataTables_wrapper {
    overflow: hidden;
  } */

  div.dataTables_wrapper div.dataTables_filter,
  .dataTables_wrapper .row:nth-child(1),
  div.dataTables_wrapper div.dataTables_filter,
  .dataTables_wrapper .row:nth-child(3) {
    padding: 10px 20px;
  }

  div.dataTables_wrapper div.dataTables_length select {
    width: 60% !important;
    appearance: none !important;
    -webkit-appearance: none;
    -moz-appearance: none;
  }

  .dataTables_scroll {
    position: relative;
    margin-bottom: 10px;
  }

  .dataTables_scroll::-webkit-scrollbar {
    visibility: hidden;
  }

  .dataTables_scrollBody tfoot th {
    background: none !important;
  }

  .dataTables_scrollHead {
    margin-bottom: 40px;
  }

  .dataTables_scrollBody {
    max-height: 75vh !important;
    height: 75% !important;
    overflow: scroll !important;
  }

  .dataTables_scrollFoot {
    position: absolute;
    top: 37px;
    height: 50px;
    overflow: scroll;
  }

  div.dataTables_wrapper div.dataTables_filter input {
    margin-left: 10px;
  }

  div.dataTables_scrollFoot>.dataTables_scrollFootInner th {
    border: 0;
  }

  .dataTables_filter {
    padding-right: 0 !important;
  }

  div.dataTables_wrapper div.dataTables_paginate ul.pagination {
    padding: 0;
    border: 0;
  }

  .dt-top-container {
    display: flex;
    align-items: center;
    padding: 0 20px;
    gap: 0;
  }

  .transactional-book-table tr td {
    white-space: pre-line !important;
  }

  .dataTables_length {
    margin-left: 50px;
  }

  a.btn.add-col.setting-menu.waves-effect.waves-light {
    position: absolute !important;
    display: flex;
    justify-content: space-between;
    top: 10px !important;
  }

  div.dataTables_wrapper div.dataTables_length label {
    margin-bottom: 0;
  }

  div.dataTables_wrapper div.dataTables_info {
    padding-left: 20px;
    position: relative;
    top: 0;
  }

  .dataTables_paginate {
    position: relative;
    right: 20px;
    bottom: 20px;
    margin-top: -15px;
  }

  .dt-center-in-div {
    display: block;
    /* order: 3; */
    margin-left: auto;
  }

  .dt-buttons.btn-group.flex-wrap button {
    background-color: #003060 !important;
    border-color: #003060 !important;
    border-radius: 7px !important;
  }

  /* .setting-row .col .btn.setting-menu {
    position: absolute !important;
    right: 255px;
    top: 10px;
  } */

  .dt-buttons.btn-group.flex-wrap {
    gap: 10px;
  }


  table.dataTable>thead .sorting:before,
  table.dataTable>thead .sorting:after,
  table.dataTable>thead .sorting_asc:before,
  table.dataTable>thead .sorting_asc:after,
  table.dataTable>thead .sorting_desc:before,
  table.dataTable>thead .sorting_desc:after,
  table.dataTable>thead .sorting_asc_disabled:before,
  table.dataTable>thead .sorting_asc_disabled:after,
  table.dataTable>thead .sorting_desc_disabled:before,
  table.dataTable>thead .sorting_desc_disabled:after {

    display: block !important;

  }

  .dataTable thead tr th,
  .dataTable tfoot.individual-search tr th {
    padding-right: 30px !important;
    border-right: 0 !important;
  }

  select.fy-dropdown {
    max-width: 100px;
  }

  .report-wrapper .daybook-filter-list.filter-list {
    display: flex;
    gap: 6px;
    justify-content: flex-start;
    position: relative;
    top: 45px;
    left: 255px;
    float: right;
  }

  .daybook-filter-list.filter-list a.active {
    background-color: #003060;
    color: #fff;
  }

  .date-range-input {
    gap: 7px;
  }

  .date-range-input .form-input {
    width: 100%;
  }

  .report-wrapper table tr td {
    background: #e7ebef;
  }

  .reports-card .filter-list a {
    background: #dedede;
    color: #003060;
    z-index: 9;
  }

  .report-wrapper .reports-card {
    background: #fff;
  }

  .report-wrapper table tr:nth-child(2n+1) td {
    background: #ffffff;
  }

  .label-select {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  @media (max-width: 769px) {
    .dt-buttons.btn-group.flex-wrap {
      gap: 10px;
      position: absolute;
      top: -39px;
      right: 60px;
    }

    .dt-buttons.btn-group.flex-wrap button {
      max-width: 60px;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
      margin-top: -10px;
    }


  }

  @media (max-width :575px) {
    .dataTables_scrollFoot {
      position: absolute;
      top: 28px;
    }

    .dt-top-container {
      display: flex;
      align-items: baseline;
      padding: 0 20px;
      gap: 20px;
      flex-direction: column-reverse;
      flex-wrap: nowrap;
    }

    .dataTables_length {
      margin-left: 0;
      margin-bottom: 1em;
    }



    div.dataTables_wrapper div.dataTables_length select {
      width: 164px !important;
    }

    .dt-center-in-div {
      margin: 3px auto;
    }

    div.dataTables_filter {
      right: 0;
      margin-top: 0;
      position: relative;
      right: -43px;
    }

    .dt-buttons.btn-group.flex-wrap {
      gap: 10px;
      position: relative;
      top: 0;
      right: 0;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
      margin-top: 40px;
    }

    .dataTables_length label {
      font-size: 0;
    }
  }

  @media (max-width: 376px) {
    div.dataTables_wrapper div.dataTables_filter {
      margin-top: 0;
      padding-left: 0 !important;
    }



    div.dataTables_wrapper div.dataTables_filter input {
      max-width: 150px;
    }

    select.fy-dropdown {
      max-width: 100px;
    }



    /* div.dataTables_wrapper div.dataTables_length select {
      width: 164px !important;
    } */
  }
</style>

<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<!-- Resources -->
<script src="../../public/assets/core.js"></script>
<script src="../../public/assets/charts.js"></script>
<script src="../../public/assets/animated.js"></script>
<script src="../../public/assets/forceDirected.js"></script>
<script src="../../public/assets/sunburst.js"></script>


<?php
// One single Query



if (isset($_GET['detailed-view'])) {
?>
  <!-- Content Wrapper detailed-view -->
  <div class="content-wrapper report-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <!-- row -->
        <div class="row p-0 m-0">
          <div class="col-12 mt-2 p-0">
            <div class="card card-tabs reports-card">
              <div class="p-0 pt-1 my-2">
                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                  <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                    <div class="label-select">
                      <h3 class="card-title mb-0">Exception Report</h3>
                      <!-- <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                        <option value="2022">FY-2022</option>
                        <option value="2023">FY-2023</option>
                        <option value="customrange">
                          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                        </option>
                      </select> -->
                    </div>
                    <!-- <div class="modal fade custom-range-modal" id="customRange" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title text-sm">Select Date Range</h4>
                          </div>
                          <div class="modal-body">
                            <div class="date-range-input d-flex">
                              <div class="form-input">
                                <label for="">From Date</label>
                                <input type="date" class="form-control" name="from_date">
                              </div>
                              <div class="form-input">
                                <label for="">To Date</label>
                                <input type="date" class="form-control" name="to_date">
                              </div>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button class="btn btn-primary float-right">Apply</button>
                          </div>
                        </div>
                      </div>
                    </div> -->
                    <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>
                  </li>
                </ul>
              </div>
              <div class="daybook-filter-list filter-list">
                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn waves-effect waves-light"><i class="fa fa-clock mr-2 "></i>Concised View</a>
                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active"></i>Detailed View</a>
              </div>
              <div class="card card-tabs mb-0" style="border-radius: 20px;">

                <div class="tab-content" id="custom-tabs-two-tabContent">
                  <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                    <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>



                    <?php
                    $cond = '';

                    $sql_list = "SELECT branch.branch_name AS branch_name,loc.othersLocation_name AS loc_name,invoices.invoice_no AS doc_num,invoices.invoice_date AS doc_date,customer.customer_code AS customer_code,customer.trade_name AS customer_name,items.itemCode AS item_code,items.itemName AS item_name,items.unitPrice AS so_unit_price,summary.itemPrice AS target_price,summary.priceType AS so_on FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id LEFT JOIN erp_inventory_stocks_summary AS summary ON items.inventory_item_id=summary.itemId LEFT JOIN erp_customer AS customer ON invoices.customer_id=customer.customer_id LEFT JOIN erp_branch_otherslocation AS loc ON invoices.location_id=loc.othersLocation_id LEFT JOIN erp_branches AS branch ON invoices.branch_id=branch.branch_id WHERE invoices.company_id=$company_id ORDER BY doc_date desc;";

                    $queryset = queryGet($sql_list, true);
                    // console($queryset);
                    $num_list = $queryset['numRows'];

                    $settingsTable = getTableSettings(TBL_COMPANY_ADMIN_TABLESETTINGS, "ERP_REPORT_DETAILED_VIEW_" . $pageName, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                    $settingsCheckbox = unserialize($settingsCh);
                    //console($settingsCheckbox);


                    if ($num_list > 0) {
                      $i = 1;
                    ?>
                      <!-- <table class="table defaultDataTable table-hover" data-paging="true" data-responsive="false"> -->
                      <table id="dataTable" class="table table-hover transactional-book-table" style="position: relative;">

                        <thead>
                          <tr>
                            <?php if (in_array($i, $settingsCheckbox)) { ?>
                              <th>SL NO.</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Branch Name</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Location Name</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Document No.</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Document Date</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Customer Code</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Customer Name</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Product Code</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Product Name</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>SO Unit Price</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Target Price</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>SO On</th>
                            <?php } ?>
                          </tr>
                        </thead>

                        <tbody>
                          <?php
                          $datas = $queryset['data'];
                          $sl = 0;
                          foreach ($datas as $data) {
                            $i = 1;
                            // console($data);
                            $sl++;
                          ?>
                            <tr>
                              <?php if (in_array($i, $settingsCheckbox)) { ?>
                                <td> <?php echo  $sl; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['branch_name']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['loc_name']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td> <?php echo $data['doc_num']; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td> <?php echo formatDateORDateTime($data['doc_date']); ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td> <?php echo $data['customer_code']; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td> <?php echo $data['customer_name']; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td> <?php echo $data['item_code']; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td> <?php echo $data['item_name']; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td class="text-right"><?php echo $data['so_unit_price']; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td class="text-right"><?php echo ($data['target_price']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['so_on']);  ?></td>
                              <?php } ?>
                            </tr>
                          <?php
                          }
                          ?>
                        </tbody>
                        <?php $j = 1; ?>
                        <tfoot class="individual-search">
                          <tr>
                            <?php if (in_array($j, $settingsCheckbox)) { ?>
                              <th>SL NO.</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Branch Name</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Location Name</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Document No.</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Document Date</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Customer Code</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Customer Name</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Product Code</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Product Name</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>SO Unit Price</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Target Price</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>SO On</th>
                            <?php } ?>
                          </tr>
                        </tfoot>

                      </table>
                    <?php } else { ?>
                      <table id="mytable" class="table defaultDataTable table-hover">
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

                <!---------------------------------Detailed View  Table settings Model Start--------------------------------->

                <div class="modal" id="myModal2">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title">Detailed View Column Settings</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                      </div>
                      <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                        <div class="modal-body" style="max-height: 450px;">
                          <input type="hidden" name="tablename" value="<?= TBL_COMPANY_ADMIN_TABLESETTINGS; ?>" />
                          <input type="hidden" name="pageTableName" value="ERP_REPORT_DETAILED_VIEW_<?= $pageName ?>" />
                          <div class="modal-body">
                            <div id="dropdownframe"></div>
                            <div id="main2">
                              <div class="checkAlltd d-flex gap-2 mb-2">
                                <input type="checkbox" class="grand-checkbox" value="" />
                                <p class="text-xs font-bold">Check All</p>
                              </div>
                              <?php $p = 1; ?>
                              <table class="colomnTable">
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    SL NO.</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Branch Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Location Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Document No.</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Document Date</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Customer Code</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Customer Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Product Code</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Product Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    SO Unit Price</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Target Price</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    SO On</td>
                                </tr>
                              </table>
                            </div>
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
    </section>
    <!-- /.content -->
  </div>
  <!-- /.Content Wrapper detailed-view-->
<?php
} else {
?>
  <!-- Content Wrapper concised-view -->
  <div class="content-wrapper report-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <!-- row -->
        <div class="row p-0 m-0">
          <div class="col-12 mt-2 p-0">
            <div class="card card-tabs reports-card">
              <div class="p-0 pt-1 my-2">
                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                  <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                    <div class="label-select">
                      <h3 class="card-title mb-0">Exception Report</h3>
                      <!-- <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                        <option value="2022">FY-2022</option>
                        <option value="2023">FY-2023</option>
                        <option value="customrange">
                          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                        </option>
                      </select> -->

                    </div>
                    <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>
                  </li>
                </ul>
              </div>
              <div class="daybook-filter-list filter-list">
                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn active waves-effect waves-light"><i class="fa fa-clock mr-2  active"></i>Concised View</a>
                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>Detailed View</a>
              </div>
              <div class="card card-tabs mb-0" style="border-radius: 20px;">

                <div class="tab-content" id="custom-tabs-two-tabContent">
                  <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                    <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>


                    <!-- <div class="modal fade custom-range-modal" id="customRange" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title text-sm">Select Date Range</h4>
                          </div>
                          <div class="modal-body">
                            <div class="date-range-input d-flex">
                              <div class="form-input">
                                <label for="">From Date</label>
                                <input type="date" class="form-control" name="from_date">
                              </div>
                              <div class="form-input">
                                <label for="">To Date</label>
                                <input type="date" class="form-control" name="to_date">
                              </div>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button class="btn btn-primary float-right">Apply</button>
                          </div>
                        </div>
                      </div>
                    </div> -->

                    <?php
                    $cond = '';

                    $sql_list = "SELECT items.itemCode AS item_code,items.itemName AS item_name,ROUND(AVG(items.unitPrice),2) AS avg_so_unit_price,summary.itemPrice AS target_price FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id=items.so_invoice_id LEFT JOIN erp_inventory_stocks_summary AS summary ON items.inventory_item_id=summary.itemId WHERE invoices.company_id=$company_id GROUP BY item_code,item_name,target_price;";

                    $queryset = queryGet($sql_list, true);
                    //  console($queryset);
                    $num_list = $queryset['numRows'];

                    $settingsTable = getTableSettings(TBL_COMPANY_ADMIN_TABLESETTINGS, "ERP_REPORT_CONCISED_VIEW_" . $pageName, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                    $settingsCheckbox_concised_view = unserialize($settingsCh);
                    //console($settingsCheckbox_concised_view);


                    if ($num_list > 0) {
                      $i = 1;
                    ?>
                      <!-- <table class="table defaultDataTable table-hover" data-paging="true" data-responsive="false"> -->
                      <table id="dataTable" class="table table-hover transactional-book-table" style="width: 100%; position: relative;">

                        <thead>
                          <tr>
                            <?php if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>SL NO.</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Product Code</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Product Name</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Average SO Unit Price</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Target Price</th>
                            <?php } ?>
                          </tr>
                        </thead>

                        <tbody class="">
                          <?php
                          $datas = $queryset['data'];
                          $sl = 0;
                          foreach ($datas as $data) {
                            $i = 1;
                            // console($data);
                            $sl++;
                          ?>
                            <tr>
                              <?php if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td> <?php echo $sl; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td> <?php echo $data['item_code']; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td> <?php echo $data['item_name']; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td class="text-right"><?= decimalValuePreview($data['avg_so_unit_price'])??'-'; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td class="text-right"><?= decimalValuePreview($data['target_price'])??'-'; ?></td>
                              <?php } ?>
                            </tr>
                          <?php
                          }
                          ?>
                        </tbody>
                        <?php $j = 1; ?>
                        <tfoot class="individual-search">
                          <tr>
                            <?php if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                              <th>SL NO.</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                              <th>Product Code</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                              <th>Product Name</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                              <th>Average SO Unit Price</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                              <th>Target Price</th>
                            <?php } ?>
                          </tr>
                        </tfoot>

                      </table>
                    <?php } else { ?>
                      <table id="mytable" class="table defaultDataTable table-hover">
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

                <!---------------------------------Concised View Table settings Model Start--------------------------------->

                <div class="modal" id="myModal2">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title">Concised View Column Settings</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                      </div>
                      <form name="table_settings_concised_view" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings_concised_view();">
                        <div class="modal-body" style="max-height: 450px;">
                          <input type="hidden" name="tablename" value="<?= TBL_COMPANY_ADMIN_TABLESETTINGS; ?>" />
                          <input type="hidden" name="pageTableName" value="ERP_REPORT_CONCISED_VIEW_<?= $pageName ?>" />
                          <div class="modal-body">
                            <div id="dropdownframe"></div>
                            <div id="main2">
                              <div class="checkAlltd d-flex gap-2 mb-2">
                                <input type="checkbox" class="grand-checkbox" value="" />
                                <p class="text-xs font-bold">Check All</p>
                              </div>
                              <?php $p = 1; ?>
                              <table class="colomnTable">
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    SL NO.</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    Product Code</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    Product Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    Average SO Unit Price</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    Target Price</td>
                                </tr>
                              </table>
                            </div>
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
    </section>
    <!-- /.content -->
  </div>
  <!-- /.Content Wrapper concised-view -->
<?php
}
require_once("../common/footer.php");
?>

<script>
  //check all
  $(document).ready(function() {
    $(".grand-checkbox").on("click", function() {

      // Check or uncheck all checkboxes within the table based on the grand checkbox state
      $(".colomnTable tr td input[type='checkbox']").prop("checked", this.checked);

    });
  });
</script>

<script>
  function table_settings_concised_view() {
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


  $(document).ready(function() {



    $('.select2')
      .select2()
      .on('select2:open', () => {
        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal3">
    Add New
  </a></div>`);
      });
    //**************************************************************
    $('.select4')
      .select4()
      .on('select4:open', () => {
        $(".select4-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal4">
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

  var inputs = document.getElementsByClassName("m-input");
  for (var i = 0; i < inputs.length; i++) {
    var el = inputs[i];
    el.addEventListener("blur", function() {
      leaveInput(this);
    });
  }



  // *** autocomplite select *** //
  wow = new WOW({
    boxClass: 'wow', // default
    animateClass: 'animated', // default
    offset: 0, // default
    mobile: true, // default
    live: true // default
  })
  wow.init();
</script>

<script>
  $(document).ready(function() {

    $("#dataTable tfoot th").each(function() {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
    });

    // DataTable
    var columnSl = 0;
    var table = $("#dataTable").DataTable({
      dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
      buttons: ['copy', 'csv', 'excel', 'print'],
      "lengthMenu": [
        [1000, 5000, 10000, -1],
        [1000, 5000, 10000, 'All'],
      ],
      "scrollY": 200,
      "scrollX": true,
      "ordering": false,


      initComplete: function() {
        this.api()
          .columns()
          .every(function() {
            columnSl++;
            console.log(`columnSl=${columnSl}`);
            if (columnSl == 8 || columnSl == 10) {
              //For Dropdown column search
              /*var column = this;
              var select = $('<select class="form-control p-0"><option value="">All</option></select>')
                .appendTo($(column.footer()).empty())
                .on('change', function() {
                  var val = $.fn.dataTable.util.escapeRegex($(this).val());
                  console.log(val);
                  column.search(val ? '^' + val + '$' : '', true, false).draw();
                });

              column
                .data()
                .unique()
                .sort()
                .each(function(d, j) {
                  select.append('<option value="' + d + '">' + d + '</option>');
                });*/
            }
            // if (columnSl == 4 || columnSl == 5) {
            //   var column = this;
            //   var select = $('<input type="text" class="form-control" placeholder="dd-mm-yyyy">')
            //     .appendTo($(column.footer()).empty());
            // }
          });
      },
    });
    // Apply the search
    columnSl2 = 0;
    table.columns().every(function() {
      columnSl2++;
      if (columnSl2 == 4 || columnSl2 == 5) {
        var that = this;
        $('input', this.footer()).on('keyup change', function() {
          let searchVal = `${(this.value).split("-")[2]}-${(this.value).split("-")[1]}-${(this.value).split("-")[0]}`;
          that.search(searchVal).draw();
        });
      } else {
        var that = this;
        $('input', this.footer()).on('keyup change', function() {
          that.search(this.value).draw();
        });
      }
    });

  });
</script>

<script>
  var elem = document.getElementById("listTabPan");

  function openFullscreen() {
    if (elem.requestFullscreen) {
      elem.requestFullscreen();
    } else if (elem.webkitRequestFullscreen) {
      /* Safari */
      elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) {
      /* IE11 */
      elem.msRequestFullscreen();
    }
  }
</script>

<script>
  $(function() {
    $('input[name="daterange"]').daterangepicker({
        opens: 'left'
      },
      function(start, end, label) {
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
      });
  });
</script>

<script>
  $(function() {
    $('input[name="daterange"]').daterangepicker({
      opens: 'left'
    }, function(start, end, label) {
      console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    });
  });
  $('#fYDropdown').change(function() {
    var title = $(this).val();
    if (title == "customrange") {
      $('.modal-title').html(title);
      $('.custom-range-modal').modal('show');
    }
  });
</script>