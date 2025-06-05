<?php
require_once("../../app/v1/connection-branch-admin.php");
$pageName =  basename($_SERVER['PHP_SELF'], '.php');
//administratorLocationAuth();
if (!isset($_SESSION["logedBranchAdminInfo"]["adminId"]) || !isset($_SESSION["logedBranchAdminInfo"]["adminRole"])) {
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
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
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
              <div class="p-0 pt-1 my-2 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                <!---------------------- Search START -->
                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                  <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                    <div class="label-select">
                      <h3 class="card-title mb-0">Purchase Register Location Item Wise</h3>
                    </div>

                    <div class="fy-custom-section">
                      <div class="fy-dropdown-section">
                        <?php
                        $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);

                        if (isset($_POST['from_date'])) {
                          $f_date = $_POST['from_date'];
                          $to_date = $_POST['to_date'];
                          //echo 1;


                        } else {

                          $start = explode('-', $variant_sql['data'][0]['year_start']);
                          $end = explode('-', $variant_sql['data'][0]['year_end']);
                          $f_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                          $to_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
                          $_POST['from_date'] = $f_date;
                          $_POST['to_date'] = $to_date;
                          $_POST['drop_val'] = 'fYDropdown';
                          $_POST['drop_id'] = $variant_sql['data'][0]['year_variant_id'];
                        }

                        ?>
                        <h6 class="text-xs font-bold">Financial Year</h6>
                        <div class="dropdown-fyear">
                          <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                            <option value="">--Select FY--</option>
                            <?php
                            foreach ($variant_sql['data'] as $key => $data) {
                              $start = explode('-', $data['year_start']);
                              $end = explode('-', $data['year_end']);
                              $startDate = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                              $endDate = date('Y-m-t', strtotime("$end[0]-$end[1]"));

                            ?>
                              <option value="<?= $data['year_variant_id'] ?>" data-start="<?= $startDate ?>" data-end="<?= $endDate ?>" <?php if (($_POST['drop_val'] == 'fYDropdown' && $_POST['drop_id'] == $data['year_variant_id'])) {
                                                                                                                                          echo "selected";
                                                                                                                                        } ?>><?= $data['year_variant_name'] ?></option>
                            <?php
                            }
                            ?>

                            <option value="customrange" <?php if ($_POST['drop_id'] == '') {
                                                          echo "selected";
                                                        } ?>>
                              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                            </option>
                          </select>

                          <label class="mb-0" for="">OR</label>


                          <select name="quickDropdown" id="quickDropdown" class="form-control quick-dropdown">
                            <option value="">--Select One--</option>
                            <option value="0" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 0) {
                                                echo "selected";
                                              } ?>>Today Report</option>
                            <option value="6" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 6) {
                                                echo "selected";
                                              } ?>>Last 7 Days</option>
                            <option value="14" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 14) {
                                                  echo "selected";
                                                } ?>>Last 15 Days</option>
                            <option value="29" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 29) {
                                                  echo "selected";
                                                } ?>>Last 30 Days</option>
                            <option value="44" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 44) {
                                                  echo "selected";
                                                } ?>>Last 45 Days</option>
                            <option value="59" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 59) {
                                                  echo "selected";
                                                } ?>>Last 60 Days</option>
                          </select>
                        </div>
                        <h6 class="text-xs font-bold "><span class="finacialYearCla"></span></h6>
                      </div>

                      <div class="customrange-section">
                        <h6 class="text-xs font-bold">Custom Range</h6>
                        <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                          <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="" />
                          <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange" />
                          <div class="date-range-input d-flex">
                            <div class="form-input">
                              <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $_POST['from_date']; ?>" required>
                            </div>
                            <div class="form-input">
                              <label class="mb-0" for="">TO</label>
                              <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
                            </div>
                          </div>
                          <button type="submit" class="btn btn-primary float-right" id="rangeid" name="add_date_form">Apply</button>
                        </form>
                        <h6 class="text-xs font-bold "><span class="customRangeCla"></span></h6>
                      </div>

                      <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>

                    </div>

                  </li>
                </ul>
                <!---------------------- Search END -->
              </div>
              <div class="daybook-filter-list filter-list">
                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn  waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Visual Representation</a>
                <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn waves-effect waves-light"><i class="fa fa-clock mr-2 "></i>Concised View</a>
                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active"></i>Detailed View</a>
              </div>
              <div class="card card-tabs mb-0" style="border-radius: 20px;">

                <div class="tab-content" id="custom-tabs-two-tabContent">
                  <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                    <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>



                    <?php
                    $cond = '';

                    $sql_list = "SELECT location.othersLocation_name AS location_name,grn.vendorCode AS vendor_code,grn.vendorName AS vendor_name,grn.grnIvCode AS doc_num,goods.goodCode AS item_code,items.itemName AS item_name,goods.goodQty AS ordered_qty,goods.receivedQty AS received_qty,goods.itemUOM AS uom,goods.unitPrice AS rate,goods.cgst AS cgst,goods.sgst,goods.igst,goods.tds,goods.totalAmount AS total_amount,goods.grnGoodCreatedAt AS created_at,goods.grnGoodCreatedBy AS created_by,goods.grnGoodUpdatedAt AS updated_at,goods.grnGoodUpdatedBy AS updated_by FROM erp_grninvoice AS grn INNER JOIN erp_grninvoice_goods AS goods ON grn.grnIvId=goods.grnIvId LEFT JOIN erp_inventory_items AS items ON goods.goodCode=items.itemCode LEFT JOIN erp_branch_otherslocation AS location ON grn.locationId=location.othersLocation_id WHERE grn.companyId=$company_id AND grn.branchId=$branch_id AND items.company_id=$company_id AND grn.postingDate BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND grn.grnStatus='active' ORDER BY doc_num desc;";

                    $queryset = queryGet($sql_list, true);
                    // console($queryset);
                    $num_list = $queryset['numRows'];

                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_REPORT_DETAILED_VIEW_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
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
                              <th>Location Name</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Document Number</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Vendor Code</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Vendor Name</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Item Code</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Item Name</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Ordered Quantity</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Received Quantity</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>UOM</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Rate</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>CGST</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>SGST</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>IGST</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>TDS</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Total Amount</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Created By</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Created At</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Updated By</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Updated At</th>
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
                                <td><?php echo ($data['location_name']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td> <?php echo $data['doc_num']; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td> <?php echo $data['vendor_code']; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td> <?php echo $data['vendor_name']; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['item_code']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['item_name']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?=decimalQuantityPreview($data['ordered_qty']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?=decimalQuantityPreview($data['received_qty']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['uom']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td class="text-right"><?= decimalValuePreview($data['rate']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td class="text-right"><?= decimalValuePreview($data['cgst']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td class="text-right"><?= decimalValuePreview($data['sgst']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td class="text-right"><?= decimalValuePreview($data['igst']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td class="text-right"><?= decimalValuePreview($data['tds']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td class="text-right"><?= decimalValuePreview($data['total_amount']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo getCreatedByUser($data['created_by']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo formatDateORDateTime($data['created_at']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo getCreatedByUser($data['updated_by']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo formatDateORDateTime($data['updated_at']);  ?></td>
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
                              <th>Location Name</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Document Number</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Vendor Code</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Vendor Name</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Item Code</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Item Name</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Ordered Quantity</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Received Quantity</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>UOM</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Rate</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>CGST</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>SGST</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>IGST</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>TDS</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Total Amount</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Created By</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Created At</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Updated By</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Updated At</th>
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
                          <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
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
                                    Location Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Document Number</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Vendor Code</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Vendor Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Item Code</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Item Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Ordered Quantity</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Received Quantity</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    UOM</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Rate</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    CGST</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    SGST</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    IGST</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    TDS</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Total Amount</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Created By</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Created At</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Updated By</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Updated At</td>
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
} else if (isset($_GET['concised-view'])) {
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
              <div class="p-0 pt-1 my-2 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                <!---------------------- Search START -->
                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                  <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                    <div class="label-select">
                      <h3 class="card-title mb-0">Purchase Register Location Item Wise</h3>
                    </div>

                    <div class="fy-custom-section">
                      <div class="fy-dropdown-section">
                        <?php
                        $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);

                        if (isset($_POST['from_date'])) {
                          $f_date = $_POST['from_date'];
                          $to_date = $_POST['to_date'];
                          //echo 1;


                        } else {

                          $start = explode('-', $variant_sql['data'][0]['year_start']);
                          $end = explode('-', $variant_sql['data'][0]['year_end']);
                          $f_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                          $to_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
                          $_POST['from_date'] = $f_date;
                          $_POST['to_date'] = $to_date;
                          $_POST['drop_val'] = 'fYDropdown';
                          $_POST['drop_id'] = $variant_sql['data'][0]['year_variant_id'];
                        }

                        ?>
                        <h6 class="text-xs font-bold">Financial Year</h6>
                        <div class="dropdown-fyear">
                          <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                            <option value="">--Select FY--</option>
                            <?php
                            foreach ($variant_sql['data'] as $key => $data) {
                              $start = explode('-', $data['year_start']);
                              $end = explode('-', $data['year_end']);
                              $startDate = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                              $endDate = date('Y-m-t', strtotime("$end[0]-$end[1]"));

                            ?>
                              <option value="<?= $data['year_variant_id'] ?>" data-start="<?= $startDate ?>" data-end="<?= $endDate ?>" <?php if (($_POST['drop_val'] == 'fYDropdown' && $_POST['drop_id'] == $data['year_variant_id'])) {
                                                                                                                                          echo "selected";
                                                                                                                                        } ?>><?= $data['year_variant_name'] ?></option>
                            <?php
                            }
                            ?>

                            <option value="customrange" <?php if ($_POST['drop_id'] == '') {
                                                          echo "selected";
                                                        } ?>>
                              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                            </option>
                          </select>

                          <label class="mb-0" for="">OR</label>


                          <select name="quickDropdown" id="quickDropdown" class="form-control quick-dropdown">
                            <option value="">--Select One--</option>
                            <option value="0" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 0) {
                                                echo "selected";
                                              } ?>>Today Report</option>
                            <option value="6" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 6) {
                                                echo "selected";
                                              } ?>>Last 7 Days</option>
                            <option value="14" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 14) {
                                                  echo "selected";
                                                } ?>>Last 15 Days</option>
                            <option value="29" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 29) {
                                                  echo "selected";
                                                } ?>>Last 30 Days</option>
                            <option value="44" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 44) {
                                                  echo "selected";
                                                } ?>>Last 45 Days</option>
                            <option value="59" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 59) {
                                                  echo "selected";
                                                } ?>>Last 60 Days</option>
                          </select>
                        </div>
                        <h6 class="text-xs font-bold "><span class="finacialYearCla"></span></h6>
                      </div>

                      <div class="customrange-section">
                        <h6 class="text-xs font-bold">Custom Range</h6>
                        <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                          <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="" />
                          <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange" />
                          <div class="date-range-input d-flex">
                            <div class="form-input">
                              <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $_POST['from_date']; ?>" required>
                            </div>
                            <div class="form-input">
                              <label class="mb-0" for="">TO</label>
                              <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
                            </div>
                          </div>
                          <button type="submit" class="btn btn-primary float-right" id="rangeid" name="add_date_form">Apply</button>
                        </form>
                        <h6 class="text-xs font-bold "><span class="customRangeCla"></span></h6>
                      </div>

                      <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>

                    </div>

                  </li>
                </ul>
                <!---------------------- Search END -->
              </div>
              <div class="daybook-filter-list filter-list">
                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Visual Representation</a>
                <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn active waves-effect waves-light"><i class="fa fa-clock mr-2  active"></i>Concised View</a>
                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>Detailed View</a>
              </div>
              <div class="card card-tabs mb-0" style="border-radius: 20px;">

                <div class="tab-content" id="custom-tabs-two-tabContent">
                  <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                    <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                    <?php
                    $cond = '';

                    $sql_list = "SELECT location.othersLocation_name AS location_name,goods.goodCode AS item_code,items.itemName AS item_name,SUM(goods.goodQty) AS total_ordered_qty,SUM(goods.receivedQty) AS total_received_qty,goods.itemUOM AS uom,SUM(goods.totalAmount) AS total_amount FROM erp_grninvoice AS grn INNER JOIN erp_grninvoice_goods AS goods ON grn.grnIvId=goods.grnIvId LEFT JOIN erp_inventory_items AS items ON goods.goodCode=items.itemCode LEFT JOIN erp_branch_otherslocation AS location ON grn.locationId=location.othersLocation_id WHERE grn.companyId=$company_id AND grn.branchId=$branch_id AND items.company_id=1 AND grn.postingDate BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND grn.grnStatus='active' GROUP BY location_name,item_code,item_name,uom;";

                    $queryset = queryGet($sql_list, true);
                    // console($queryset);
                    $num_list = $queryset['numRows'];

                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_REPORT_CONCISED_VIEW_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
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
                              <th>Location Name</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Item Code</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Item Name</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Total Ordered Quantity</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Total Received Quantity</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>UOM</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Total Amount</th>
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
                                <td> <?php echo $data['location_name']; ?></td>
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
                                <td> <?= decimalQuantityPreview($data['total_ordered_qty']); ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td> <?= decimalQuantityPreview($data['total_received_qty']); ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td> <?php echo $data['uom']; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td class="text-right"><?=decimalValuePreview($data['total_amount']); ?></td>
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
                              <th>Location Name</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                              <th>Item Code</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                              <th>Item Name</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                              <th>Total Ordered Quantity</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                              <th>Total Received Quantity</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                              <th>UOM</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                              <th>Total Amount</th>
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
                          <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
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
                                    Location Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    Item Code</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    Item Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    Total Ordered Quantity</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    Total Received Quantity</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    UOM</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    Total Amount</td>
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
} else {

?>
  <!-- Content Wrapper. Graph View -->
  <div class="content-wrapper report-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <!-- row -->
        <div class="row p-0 m-0">
          <div class="col-12 mt-2 p-0">
            <div class="card card-tabs reports-card">
              <div class="p-0 pt-1 my-2 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                <!---------------------- Search START -->
                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                  <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                    <div class="label-select">
                      <h3 class="card-title mb-0">Purchase Register Location Item Wise</h3>
                    </div>

                    <div class="fy-custom-section">
                      <div class="fy-dropdown-section">
                        <?php
                        $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);

                        if (isset($_POST['from_date'])) {
                          $f_date = $_POST['from_date'];
                          $to_date = $_POST['to_date'];
                          //echo 1;


                        } else {

                          $start = explode('-', $variant_sql['data'][0]['year_start']);
                          $end = explode('-', $variant_sql['data'][0]['year_end']);
                          $f_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                          $to_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
                          $_POST['from_date'] = $f_date;
                          $_POST['to_date'] = $to_date;
                          $_POST['drop_val'] = 'fYDropdown';
                          $_POST['drop_id'] = $variant_sql['data'][0]['year_variant_id'];
                        }

                        ?>
                        <h6 class="text-xs font-bold">Financial Year</h6>
                        <div class="dropdown-fyear">
                          <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                            <option value="">--Select FY--</option>
                            <?php
                            foreach ($variant_sql['data'] as $key => $data) {
                              $start = explode('-', $data['year_start']);
                              $end = explode('-', $data['year_end']);
                              $startDate = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                              $endDate = date('Y-m-t', strtotime("$end[0]-$end[1]"));

                            ?>
                              <option value="<?= $data['year_variant_id'] ?>" data-start="<?= $startDate ?>" data-end="<?= $endDate ?>" <?php if (($_POST['drop_val'] == 'fYDropdown' && $_POST['drop_id'] == $data['year_variant_id'])) {
                                                                                                                                          echo "selected";
                                                                                                                                        } ?>><?= $data['year_variant_name'] ?></option>
                            <?php
                            }
                            ?>

                            <option value="customrange" <?php if ($_POST['drop_id'] == '') {
                                                          echo "selected";
                                                        } ?>>
                              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                            </option>
                          </select>

                          <label class="mb-0" for="">OR</label>


                          <select name="quickDropdown" id="quickDropdown" class="form-control quick-dropdown">
                            <option value="">--Select One--</option>
                            <option value="0" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 0) {
                                                echo "selected";
                                              } ?>>Today Report</option>
                            <option value="6" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 6) {
                                                echo "selected";
                                              } ?>>Last 7 Days</option>
                            <option value="14" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 14) {
                                                  echo "selected";
                                                } ?>>Last 15 Days</option>
                            <option value="29" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 29) {
                                                  echo "selected";
                                                } ?>>Last 30 Days</option>
                            <option value="44" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 44) {
                                                  echo "selected";
                                                } ?>>Last 45 Days</option>
                            <option value="59" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 59) {
                                                  echo "selected";
                                                } ?>>Last 60 Days</option>
                          </select>
                        </div>
                        <h6 class="text-xs font-bold "><span class="finacialYearCla"></span></h6>
                      </div>

                      <div class="customrange-section">
                        <h6 class="text-xs font-bold">Custom Range</h6>
                        <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                          <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="" />
                          <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange" />
                          <div class="date-range-input d-flex">
                            <div class="form-input">
                              <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $_POST['from_date']; ?>" required>
                            </div>
                            <div class="form-input">
                              <label class="mb-0" for="">TO</label>
                              <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
                            </div>
                          </div>
                          <button type="submit" class="btn btn-primary float-right" id="rangeid" name="add_date_form">Apply</button>
                        </form>
                        <h6 class="text-xs font-bold "><span class="customRangeCla"></span></h6>
                      </div>

                      <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>

                    </div>

                  </li>
                </ul>
                <!---------------------- Search END -->
              </div>
              <div class="daybook-filter-list filter-list">
                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn active waves-effect waves-light"><i class="fa fa-stream mr-2  active"></i>Visual Representation</a>
                <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn waves-effect waves-light"><i class="fa fa-clock mr-2"></i>Concised View</a>
                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>Detailed View</a>
              </div>

              <div class="tab-content" id="custom-tabs-two-tabContent">
                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">

                  <?php
                  //Graph View SQL

                  $sql_list = "SELECT location.othersLocation_name AS location_name,goods.goodCode AS item_code,items.itemName AS item_name,SUM(goods.goodQty) AS total_ordered_qty,SUM(goods.receivedQty) AS total_received_qty,goods.itemUOM AS uom,SUM(goods.totalAmount) AS total_amount FROM erp_grninvoice AS grn INNER JOIN erp_grninvoice_goods AS goods ON grn.grnIvId=goods.grnIvId LEFT JOIN erp_inventory_items AS items ON goods.goodCode=items.itemCode LEFT JOIN erp_branch_otherslocation AS location ON grn.locationId=location.othersLocation_id WHERE grn.companyId=$company_id AND grn.branchId=$branch_id AND items.company_id=1 AND grn.postingDate BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND grn.grnStatus='active' GROUP BY location_name,item_code,item_name,uom;";

                  $queryset = queryGet($sql_list, true);
                  // console($queryset);
                  $chartData = json_encode($queryset, true);

                  $num_list = $queryset['numRows'];


                  if ($num_list > 0) {
                    $i = 1;
                  ?>

                    <div class="container-fluid mt-10">

                      <div class="row">
                        <div class="col-md-12 col-sm-12 d-flex">
                          <div class="card flex-fill reports-card">
                            <!-- <div class="card-header">
                                <h5 class="card-title chartDivCustomerWiseSalesRegister"></h5>
                              </div> -->
                            <div class="card-body">
                              <div id="chartDivPurchaseRegisterLocationItemWise" class="chartContainer"></div>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>

                  <?php } else { ?>
                    <p>No data Found</p>
                  <?php } ?>
                </div>


              </div>
            </div>
          </div>
        </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.Content Wrapper. Graph View -->


<?php
}
require_once("../common/footer.php");
?>

<script>
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

<!-- CHANGES -->
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
      $("#drop_val").val('customrange');
      $("#from_date").val('');
      $("#to_date").val('');
      $("#from_date").focus();
    } else {
      let start = $(this).find(':selected').data('start');
      let end = $(this).find(':selected').data('end');
      //alert(start);
      $("#from_date").val(start);
      $("#to_date").val(end);
      $("#drop_val").val('fYDropdown');
      $("#drop_id").val(title);
      $('#date_form').submit();
    }
  });

  $('#quickDropdown').change(function() {
    var days = $(this).val();
    var today = new Date();
    var seven_days_ago = new Date(today.getTime() - (days * 24 * 60 * 60 * 1000));

    var end = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
    var start = seven_days_ago.getFullYear() + '-' + ('0' + (seven_days_ago.getMonth() + 1)).slice(-2) + '-' + ('0' + seven_days_ago.getDate()).slice(-2);

    // alert(start);
    // alert(end);
    $("#from_date").val(start);
    $("#to_date").val(end);
    $("#drop_val").val('quickDrop');
    $("#drop_id").val(days);

    $('#date_form').submit();
  });

  function compare_date() {
    let fromDate = $("#from_date").val();
    let toDate = $("#to_date").val();

    const date1 = new Date(fromDate);
    const date2 = new Date(toDate);
    const diffTime = Math.abs(date2 - date1);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));


    if (fromDate && toDate) {
      if (diffDays > 366) {
        document.getElementById("rangeid").disabled = true;
        $(".customRangeCla").html(`<p class="text-danger text-xs prdatelabel">Date Range can not be greater than 1 year</p>`);
      } else {
        $(".customRangeCla").html('');
        document.getElementById("rangeid").disabled = false;

        if (toDate < fromDate) {
          $(".customRangeCla").html(`<p class="text-danger text-xs prdatelabel">From Date can not be greater than To Date</p>`);
          document.getElementById("rangeid").disabled = true;

        } else {
          $(".customRangeCla").html('');
          document.getElementById("rangeid").disabled = false;
        }
      }
    }
  }

  $("#to_date").keyup(function() {
    compare_date();
  });

  $("#from_date").change(function() {
    compare_date();
  });

  $("#to_date").change(function() {
    compare_date();
  });
</script>
<!-- CHANGES -->


<!-- CHART FUNCTION -->
<script>
  var chartData = <?php echo $chartData; ?>;
  am4core.ready(function() {

    // Themes
    am4core.useTheme(am4themes_animated);

    // Create chart instance
    var chart = am4core.create("chartDivPurchaseRegisterLocationItemWise", am4charts.XYChart);
    chart.logo.disabled = true;

    // Export
    // chart.exporting.menu = new am4core.ExportMenu();

    let finalData = [];

    for (elem of chartData.data) {
      finalData.push({
        "label": elem.item_name,
        "uom": elem.uom,
        "total_qty": Number(elem.total_received_qty),
        "total_amount": Number(elem.total_amount),
      });
    };

    // Data for both series
    chart.data = finalData;

    /* Create axes */
    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "label";
    categoryAxis.renderer.minGridDistance = 30;

    /* Create value axis */
    var valueAxis1 = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis1.title.text = "Total Amount";

    var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis2.title.text = "Total Quantity";
    valueAxis2.renderer.opposite = true;
    valueAxis2.renderer.grid.template.disabled = true;

    /* Create series */
    var columnSeries = chart.series.push(new am4charts.ColumnSeries());
    columnSeries.yAxis = valueAxis1;
    columnSeries.name = "Total Amount";
    columnSeries.dataFields.valueY = "total_amount";
    columnSeries.dataFields.categoryX = "label";


    columnSeries.columns.template.tooltipText = "{categoryX}:\n[/][#fff font-size: 20px]{valueY}"
    columnSeries.columns.template.propertyFields.fillOpacity = "fillOpacity";
    columnSeries.columns.template.propertyFields.stroke = "stroke";
    columnSeries.columns.template.propertyFields.strokeWidth = "strokeWidth";
    columnSeries.columns.template.propertyFields.strokeDasharray = "columnDash";
    columnSeries.tooltip.label.textAlign = "middle";

    var lineSeries = chart.series.push(new am4charts.LineSeries());
    lineSeries.yAxis = valueAxis2;
    lineSeries.name = "Total Quantity";
    lineSeries.dataFields.valueY = "total_qty";
    lineSeries.dataFields.categoryX = "label";

    lineSeries.stroke = am4core.color("#fdd400");
    lineSeries.strokeWidth = 3;
    lineSeries.propertyFields.strokeDasharray = "lineDash";
    lineSeries.tooltip.label.textAlign = "middle";

    var bullet = lineSeries.bullets.push(new am4charts.Bullet());
    bullet.fill = am4core.color("#fdd400"); // tooltips grab fill from parent by default
    bullet.tooltipText = "{categoryX}:\n[/][#fff font-size: 20px]{valueY} {uom}"
    var circle = bullet.createChild(am4core.Circle);
    circle.radius = 4;
    circle.fill = am4core.color("#fff");
    circle.strokeWidth = 3;

    categoryAxis.renderer.labels.template.horizontalCenter = "right";
    categoryAxis.renderer.labels.template.verticalCenter = "middle";
    categoryAxis.renderer.labels.template.rotation = -30;
    categoryAxis.tooltip.disabled = true;
    categoryAxis.renderer.minHeight = 10;
    categoryAxis.renderer.labels.template.fontSize = 10;
  });
</script>