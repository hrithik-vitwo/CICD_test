<?php
require_once("../../app/v1/connection-branch-admin.php");
$pageName=  basename($_SERVER['PHP_SELF'], '.php');
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
    gap: 20px;
  }

  .transactional-book-table tr td {
    white-space: pre-line !important;
  }

  .dataTables_length {
    margin-left: 4em;
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
    position: absolute;
    max-width: 100px;
    top: 14px;
    left: 255px;
  }

  .daybook-filter-list.filter-list {
    display: flex;
    gap: 7px;
    justify-content: flex-end;
    position: relative;
    top: -35px;
    left: -75px;
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

    select.fy-dropdown {
      position: absolute;
      max-width: 125px;
      top: 155px;
      left: 189px;
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

    select.fy-dropdown {
      position: absolute;
      max-width: 109px;
      top: 144px;
      left: 189px;
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

<?php
// One single Query



if (isset($_GET['detailed-view'])) {
?>
  <!-- Content Wrapper detailed-view -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <!-- row -->
        <div class="row p-0 m-0">
          <div class="col-12 mt-2 p-0">
            <div class="card card-tabs">
              <div class="p-0 pt-1 my-2">
                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                  <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                    <h3 class="card-title">Detailed View</h3>
                    <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>
                  </li>
                </ul>
              </div>
              <div class="daybook-filter-list filter-list">
                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn  waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Graph View</a>
                <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn waves-effect waves-light"><i class="fa fa-clock mr-2 "></i>Concised View</a>
                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active"></i>Detailed View</a>
              </div>
              <div class="card card-tabs mb-0" style="border-radius: 20px;">

                <div class="tab-content" id="custom-tabs-two-tabContent">
                  <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #dbe5ee; border-radius: 20px;">
                    <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                    <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                      <option value="2022">FY-2022</option>
                      <option value="2023">FY-2023</option>
                      <option value="customrange">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                      </option>
                    </select>
                    <div class="modal fade custom-range-modal" id="customRange" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title text-sm">Select Date Range</h4>
                          </div>
                          <div class="modal-body">
                            <!-- <input type="text" name="daterange" class="form-control" value="01/01/2018 - 01/15/2018" /> -->
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
                    </div>

                    <?php
                    $cond = '';

                    $sql_list = "SELECT summary1.*,
                    CASE
                        WHEN Order_num LIKE 'PO%' THEN (SELECT vendor_id FROM erp_branch_purchase_order WHERE po_number = summary1.Order_num)
                            WHEN Order_num LIKE 'SO%' THEN (SELECT customer_id FROM erp_branch_sales_order WHERE so_number = summary1.Order_num)
                        END as party_id,
                        CASE
                        WHEN Order_num LIKE 'PO%' THEN (SELECT po_date FROM erp_branch_purchase_order WHERE po_number = summary1.Order_num)
                            WHEN Order_num LIKE 'SO%' THEN (SELECT so_date FROM erp_branch_sales_order WHERE so_number = summary1.Order_num)
                        END as order_date
                    FROM
                        (SELECT
                            table1.jid as jid,
                            table1.company_id as company_id,
                            table1.branch_id as branch_id,
                            table1.location_id as location_id,
                            table1.jv_no as jv_no,
                            table1.refarenceCode as referenceCode,
                            table1.parent_id AS parent_id,
                            table1.parent_slug AS parent_slug,
                            table1.journal_entry_ref as journal_entry_ref,
                            table1.documentNo as documentNo,
                            table1.order_no as Order_num,
                            table1.documentDate as document_date,
                            table1.postingDate as postingDate,
                            table1.remark as remark,
                            table1.glId as glId,
                            coa.gl_code as gl_code,
                            coa.gl_label as gl_label,
                            coa.typeAcc as typeAcc,
                            table1.Amount as Amount,
                            table1.Type as type,
                            table1.journal_created_at as journal_created_at,
                            table1.journal_created_by as journal_created_by,
                            table1.journal_updated_at as journal_updated_at,
                            table1.journal_updated_by as journal_updated_by
                        FROM ( 
                          (SELECT *,
                            CASE
                                WHEN parent_slug ='PGI' THEN (SELECT so_number FROM erp_branch_sales_order_delivery_pgi WHERE so_delivery_pgi_id = main_report.parent_id LIMIT 1)
                                WHEN parent_slug = 'SOInvoicing' THEN (SELECT so_number FROM erp_branch_sales_order_invoices WHERE so_invoice_id = main_report.parent_id LIMIT 1)
                                WHEN parent_slug = 'grn' THEN (SELECT grnPoNumber FROM erp_grn WHERE grnId = main_report.parent_id LIMIT 1)
                                WHEN parent_slug = 'grniv' THEN (SELECT grnPoNumber FROM erp_grn WHERE grnId = main_report.parent_id LIMIT 1)
                            END as Order_no
                        FROM
                        (SELECT
                                journal.id AS jid,
                                journal.company_id AS company_id,
                                journal.branch_id AS branch_id,
                                journal.location_id AS location_id,
                                journal.jv_no AS jv_no,
                                journal.refarenceCode AS refarenceCode,
                                journal.parent_id AS parent_id,
                                journal.parent_slug AS parent_slug,
                                journal.journalEntryReference as journal_entry_ref,
                                journal.documentNo AS documentNo,
                                journal.documentDate AS documentDate,
                                journal.postingDate AS postingDate,
                                journal.remark AS remark,
                                journal.journal_status AS journal_status,
                                debit.glId AS glId,
                                debit.debit_amount AS Amount,
                                'DR' AS Type,
                                journal.journal_created_at as journal_created_at,
                                journal.journal_created_by as journal_created_by,
                                journal.journal_updated_at as journal_updated_at,
                                journal.journal_updated_by as journal_updated_by
                            FROM
                                `" . ERP_ACC_JOURNAL . "` AS journal
                            INNER JOIN
                                (SELECT journal_id,glId,SUM(debit_amount) as debit_amount FROM `" . ERP_ACC_DEBIT . "` GROUP BY journal_id,glId) AS debit
                                ON
                                    debit.journal_id = journal.id
                            WHERE
                                journal.journal_status='active' AND
                                journal.company_id=$company_id AND
                                journal.branch_id=$branch_id AND
                                journal.location_id=$location_id) AS main_report)
                            UNION 
                            (SELECT *,
                                CASE
                                    WHEN parent_slug ='PGI' THEN (SELECT so_number FROM erp_branch_sales_order_delivery_pgi WHERE so_delivery_pgi_id = mainReport.parent_id LIMIT 1)
                                    WHEN parent_slug = 'SOInvoicing' THEN (SELECT so_number FROM erp_branch_sales_order_invoices WHERE so_invoice_id = mainReport.parent_id LIMIT 1)
                                    WHEN parent_slug = 'grn' THEN (SELECT grnPoNumber FROM erp_grn WHERE grnId = mainReport.parent_id LIMIT 1)
                                    WHEN parent_slug = 'grniv' THEN (SELECT grnPoNumber FROM erp_grn WHERE grnId = mainReport.parent_id LIMIT 1)
                                END as Order_no
                            FROM
                            (SELECT
                                journal.id AS jid,
                                journal.company_id AS company_id,
                                journal.branch_id AS branch_id,
                                journal.location_id AS location_id,
                                journal.jv_no AS jv_no,
                                journal.refarenceCode AS refarenceCode,
                                journal.parent_id AS parent_id,
                                journal.parent_slug AS parent_slug,
                                journal.journalEntryReference as journal_entry_ref,
                                journal.documentNo AS documentNo,
                                journal.documentDate AS documentDate,
                                journal.postingDate AS postingDate,
                                journal.remark AS remark,
                                journal.journal_status AS journal_status,
                                credit.glId AS glId,
                                credit.credit_amount*(-1) AS Amount,
                                'CR' AS Type,
                                journal.journal_created_at as journal_created_at,
                                journal.journal_created_by as journal_created_by,
                                journal.journal_updated_at as journal_updated_at,
                                journal.journal_updated_by as journal_updated_by
                            FROM
                                `" . ERP_ACC_JOURNAL . "` AS journal
                            INNER JOIN
                                (SELECT journal_id,glId,SUM(credit_amount) as credit_amount FROM `" . ERP_ACC_CREDIT . "` GROUP BY journal_id,glId) AS credit
                                ON
                                    credit.journal_id = journal.id
                            WHERE
                                journal.journal_status='active' AND
                                journal.company_id=$company_id AND
                                journal.branch_id=$branch_id AND
                                journal.location_id=$location_id) as mainReport)) as table1
                            INNER JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` as coa
                            ON table1.glId = coa.id
                            ORDER BY table1.jid DESC) AS summary1;";

                    $queryset = queryGet($sql_list, true);
                    // console($queryset);
                    $num_list = $queryset['numRows'];

                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_REPORT_DETAILED_VIEW_".$pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                    $settingsCheckbox = unserialize($settingsCh);
                    //console($settingsCheckbox);


                    if ($num_list > 0) {
                      $i = 1;
                    ?>
                      <!-- <table class="table defaultDataTable table-hover" data-paging="true" data-responsive="false"> -->
                      <table id="dataTable" class="table table-hover transactional-book-table" data-paging="true" data-responsive="false" style="position: relative;">

                        <thead>
                          <tr>
                            <?php if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Branch</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Location</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Accounting Document No</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Document No</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Posting Date</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Created Date</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Created By</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Order No</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Order Date</th>
                            <?php } ?>
                          </tr>
                        </thead>

                        <tbody class="">
                          <?php
                          $datas = $queryset['data'];

                          foreach ($datas as $data) {
                            $i = 1;
                            //console($data);

                          ?>
                            <tr>
                              <?php if (in_array($i, $settingsCheckbox)) { ?>
                                <td> <?php echo $data['branch_id']; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo $data['location_id']; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['jv_no']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['documentNo']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo formatDateORDateTime($data['postingDate']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo formatDateORDateTime($data['journal_created_at']); ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo getCreatedByUser($data['journal_created_by']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['Order_num'] ?? '-');  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo formatDateORDateTime($data['document_date']);  ?></td>
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
                              <th>Branch</th>
                            <?php }
                            if (in_array($j++, $settingsCheckbox)) { ?>
                              <th>Location</th>
                            <?php }
                            if (in_array($j++, $settingsCheckbox)) { ?>
                              <th>Accounting Document No</th>
                            <?php }
                            if (in_array($j++, $settingsCheckbox)) { ?>
                              <th>Document No</th>
                            <?php }
                            if (in_array($j++, $settingsCheckbox)) { ?>
                              <th>Posting Date</th>
                            <?php }
                            if (in_array($j++, $settingsCheckbox)) { ?>
                              <th>Created Date</th>
                            <?php }
                            if (in_array($j++, $settingsCheckbox)) { ?>
                              <th>Created By</th>
                            <?php }
                            if (in_array($j++, $settingsCheckbox)) { ?>
                              <th>Order No</th>
                            <?php }
                            if (in_array($j++, $settingsCheckbox)) { ?>
                              <th>Order Date</th>
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
                          <input type="hidden" name="pageTableName" value="ERP_REPORT_DETAILED_VIEW_<?=$pageName?>" />
                          <div class="modal-body">
                            <div id="dropdownframe"></div>
                            <div id="main2">
                              <?php $p = 1; ?>
                              <table>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Branch</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Location</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Accounting Document No</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Document No</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="<?php echo $p; ?>" />
                                    Posting Date</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?php echo $p; ?>" />
                                    Created Date</td>
                                </tr>

                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="<?php echo $p; ?>" />
                                    Created By</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Order No</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Order Date</td>
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
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <!-- row -->
        <div class="row p-0 m-0">
          <div class="col-12 mt-2 p-0">
            <div class="card card-tabs">
              <div class="p-0 pt-1 my-2">
                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                  <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                    <h3 class="card-title">Concised View</h3>
                    <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>
                  </li>
                </ul>
              </div>
              <div class="daybook-filter-list filter-list">
                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Graph View</a>
                <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn active waves-effect waves-light"><i class="fa fa-clock mr-2  active"></i>Concised View</a>
                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>Detailed View</a>
              </div>
              <div class="card card-tabs mb-0" style="border-radius: 20px;">

                <div class="tab-content" id="custom-tabs-two-tabContent">
                  <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #dbe5ee; border-radius: 20px;">
                    <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                    <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                      <option value="2022">FY-2022</option>
                      <option value="2023">FY-2023</option>
                      <option value="customrange">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                      </option>
                    </select>
                    <div class="modal fade custom-range-modal" id="customRange" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title text-sm">Select Date Range</h4>
                          </div>
                          <div class="modal-body">
                            <!-- <input type="text" name="daterange" class="form-control" value="01/01/2018 - 01/15/2018" /> -->
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
                    </div>

                    <?php
                    $cond = '';

                    $sql_list = "SELECT summary1.*,
                    CASE
                        WHEN Order_num LIKE 'PO%' THEN (SELECT vendor_id FROM erp_branch_purchase_order WHERE po_number = summary1.Order_num)
                            WHEN Order_num LIKE 'SO%' THEN (SELECT customer_id FROM erp_branch_sales_order WHERE so_number = summary1.Order_num)
                        END as party_id,
                        CASE
                        WHEN Order_num LIKE 'PO%' THEN (SELECT po_date FROM erp_branch_purchase_order WHERE po_number = summary1.Order_num)
                            WHEN Order_num LIKE 'SO%' THEN (SELECT so_date FROM erp_branch_sales_order WHERE so_number = summary1.Order_num)
                        END as order_date
                    FROM
                        (SELECT
                            table1.jid as jid,
                            table1.company_id as company_id,
                            table1.branch_id as branch_id,
                            table1.location_id as location_id,
                            table1.jv_no as jv_no,
                            table1.refarenceCode as referenceCode,
                            table1.parent_id AS parent_id,
                            table1.parent_slug AS parent_slug,
                            table1.journal_entry_ref as journal_entry_ref,
                            table1.documentNo as documentNo,
                            table1.order_no as Order_num,
                            table1.documentDate as document_date,
                            table1.postingDate as postingDate,
                            table1.remark as remark,
                            table1.glId as glId,
                            coa.gl_code as gl_code,
                            coa.gl_label as gl_label,
                            coa.typeAcc as typeAcc,
                            table1.Amount as Amount,
                            table1.Type as type,
                            table1.journal_created_at as journal_created_at,
                            table1.journal_created_by as journal_created_by,
                            table1.journal_updated_at as journal_updated_at,
                            table1.journal_updated_by as journal_updated_by
                        FROM ( 
                          (SELECT *,
                            CASE
                                WHEN parent_slug ='PGI' THEN (SELECT so_number FROM erp_branch_sales_order_delivery_pgi WHERE so_delivery_pgi_id = main_report.parent_id LIMIT 1)
                                WHEN parent_slug = 'SOInvoicing' THEN (SELECT so_number FROM erp_branch_sales_order_invoices WHERE so_invoice_id = main_report.parent_id LIMIT 1)
                                WHEN parent_slug = 'grn' THEN (SELECT grnPoNumber FROM erp_grn WHERE grnId = main_report.parent_id LIMIT 1)
                                WHEN parent_slug = 'grniv' THEN (SELECT grnPoNumber FROM erp_grn WHERE grnId = main_report.parent_id LIMIT 1)
                            END as Order_no
                        FROM
                        (SELECT
                                journal.id AS jid,
                                journal.company_id AS company_id,
                                journal.branch_id AS branch_id,
                                journal.location_id AS location_id,
                                journal.jv_no AS jv_no,
                                journal.refarenceCode AS refarenceCode,
                                journal.parent_id AS parent_id,
                                journal.parent_slug AS parent_slug,
                                journal.journalEntryReference as journal_entry_ref,
                                journal.documentNo AS documentNo,
                                journal.documentDate AS documentDate,
                                journal.postingDate AS postingDate,
                                journal.remark AS remark,
                                journal.journal_status AS journal_status,
                                debit.glId AS glId,
                                debit.debit_amount AS Amount,
                                'DR' AS Type,
                                journal.journal_created_at as journal_created_at,
                                journal.journal_created_by as journal_created_by,
                                journal.journal_updated_at as journal_updated_at,
                                journal.journal_updated_by as journal_updated_by
                            FROM
                                `" . ERP_ACC_JOURNAL . "` AS journal
                            INNER JOIN
                                (SELECT journal_id,glId,SUM(debit_amount) as debit_amount FROM `" . ERP_ACC_DEBIT . "` GROUP BY journal_id,glId) AS debit
                                ON
                                    debit.journal_id = journal.id
                            WHERE
                                journal.journal_status='active' AND
                                journal.company_id=$company_id AND
                                journal.branch_id=$branch_id AND
                                journal.location_id=$location_id) AS main_report)
                            UNION 
                            (SELECT *,
                                CASE
                                    WHEN parent_slug ='PGI' THEN (SELECT so_number FROM erp_branch_sales_order_delivery_pgi WHERE so_delivery_pgi_id = mainReport.parent_id LIMIT 1)
                                    WHEN parent_slug = 'SOInvoicing' THEN (SELECT so_number FROM erp_branch_sales_order_invoices WHERE so_invoice_id = mainReport.parent_id LIMIT 1)
                                    WHEN parent_slug = 'grn' THEN (SELECT grnPoNumber FROM erp_grn WHERE grnId = mainReport.parent_id LIMIT 1)
                                    WHEN parent_slug = 'grniv' THEN (SELECT grnPoNumber FROM erp_grn WHERE grnId = mainReport.parent_id LIMIT 1)
                                END as Order_no
                            FROM
                            (SELECT
                                journal.id AS jid,
                                journal.company_id AS company_id,
                                journal.branch_id AS branch_id,
                                journal.location_id AS location_id,
                                journal.jv_no AS jv_no,
                                journal.refarenceCode AS refarenceCode,
                                journal.parent_id AS parent_id,
                                journal.parent_slug AS parent_slug,
                                journal.journalEntryReference as journal_entry_ref,
                                journal.documentNo AS documentNo,
                                journal.documentDate AS documentDate,
                                journal.postingDate AS postingDate,
                                journal.remark AS remark,
                                journal.journal_status AS journal_status,
                                credit.glId AS glId,
                                credit.credit_amount*(-1) AS Amount,
                                'CR' AS Type,
                                journal.journal_created_at as journal_created_at,
                                journal.journal_created_by as journal_created_by,
                                journal.journal_updated_at as journal_updated_at,
                                journal.journal_updated_by as journal_updated_by
                            FROM
                                `" . ERP_ACC_JOURNAL . "` AS journal
                            INNER JOIN
                                (SELECT journal_id,glId,SUM(credit_amount) as credit_amount FROM `" . ERP_ACC_CREDIT . "` GROUP BY journal_id,glId) AS credit
                                ON
                                    credit.journal_id = journal.id
                            WHERE
                                journal.journal_status='active' AND
                                journal.company_id=$company_id AND
                                journal.branch_id=$branch_id AND
                                journal.location_id=$location_id) as mainReport)) as table1
                            INNER JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` as coa
                            ON table1.glId = coa.id
                            ORDER BY table1.jid DESC) AS summary1;";

                    $queryset = queryGet($sql_list, true);
                    // console($queryset);
                    $num_list = $queryset['numRows'];

                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_REPORT_CONCISED_VIEW_".$pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                    $settingsCheckbox_concised_view = unserialize($settingsCh);
                    //console($settingsCheckbox_concised_view);


                    if ($num_list > 0) {
                      $i = 1;
                    ?>
                      <!-- <table class="table defaultDataTable table-hover" data-paging="true" data-responsive="false"> -->
                      <table id="dataTable" class="table table-hover transactional-book-table" data-paging="true" data-responsive="false" style="position: relative;">

                        <thead>
                          <tr>
                            <?php if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Branch</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Location</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Accounting Document No</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Document No</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Posting Date</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Created Date</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Created By</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Order No</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                              <th>Order Date</th>
                            <?php } ?>
                          </tr>
                        </thead>

                        <tbody class="">
                          <?php
                          $datas = $queryset['data'];

                          foreach ($datas as $data) {
                            $i = 1;
                            //console($data);

                          ?>
                            <tr>
                              <?php if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td> <?php echo $data['branch_id']; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td><?php echo $data['location_id']; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td><?php echo ($data['jv_no']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td><?php echo ($data['documentNo']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td><?php echo formatDateORDateTime($data['postingDate']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td><?php echo formatDateORDateTime($data['journal_created_at']); ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td><?php echo getCreatedByUser($data['journal_created_by']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td><?php echo ($data['Order_num'] ?? '-');  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                <td><?php echo formatDateORDateTime($data['document_date']);  ?></td>
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
                              <th>Branch</th>
                            <?php }
                            if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                              <th>Location</th>
                            <?php }
                            if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                              <th>Accounting Document No</th>
                            <?php }
                            if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                              <th>Document No</th>
                            <?php }
                            if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                              <th>Posting Date</th>
                            <?php }
                            if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                              <th>Created Date</th>
                            <?php }
                            if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                              <th>Created By</th>
                            <?php }
                            if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                              <th>Order No</th>
                            <?php }
                            if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                              <th>Order Date</th>
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
                          <input type="hidden" name="pageTableName" value="ERP_REPORT_CONCISED_VIEW_<?=$pageName?>" />
                          <div class="modal-body">
                            <div id="dropdownframe"></div>
                            <div id="main2">
                              <?php $p = 1; ?>
                              <table>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    Branch</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    Location</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    Accounting Document No</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    Document No</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view2" value="<?php echo $p; ?>" />
                                    Posting Date</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view3" value="<?php echo $p; ?>" />
                                    Created Date</td>
                                </tr>

                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view4" value="<?php echo $p; ?>" />
                                    Created By</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view5" value="<?php echo $p; ?>" />
                                    Order No</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view5" value="<?php echo $p; ?>" />
                                    Order Date</td>
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
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <!-- row -->
        <div class="row p-0 m-0">
          <div class="col-12 mt-2 p-0">
            <div class="card card-tabs">
              <div class="p-0 pt-1 my-2">
                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                  <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                    <h3 class="card-title">Graph View</h3>
                    <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>
                  </li>
                </ul>
              </div>
              <div class="daybook-filter-list filter-list">
                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn active waves-effect waves-light"><i class="fa fa-stream mr-2  active"></i>Graph View</a>
                <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn waves-effect waves-light"><i class="fa fa-clock mr-2"></i>Concised View</a>
                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>Detailed View</a>
              </div>
              <div class="card card-tabs mb-0" style="border-radius: 20px;">

                <div class="tab-content" id="custom-tabs-two-tabContent">
                  <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #dbe5ee; border-radius: 20px;">
                    
                    <?php
                    //Graph View SQL

                    $sql_list = "SELECT * FROM " . ERP_CUSTOMER . " WHERE company_id = $company_id";

                    $queryset = queryGet($sql_list, true);
                    // console($queryset);
                    $num_list = $queryset['numRows'];


                    if ($num_list > 0) {
                      $i = 1;
                    ?>

                      <div class="container-fluid mt-10">

                        <div class="row">
                          <div class="col-md-12 col-sm-12 d-flex">

                            Graph View
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
            if (columnSl == 4 || columnSl == 5) {
              var column = this;
              var select = $('<input type="text" class="form-control" placeholder="dd-mm-yyyy">')
                .appendTo($(column.footer()).empty());
            }
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