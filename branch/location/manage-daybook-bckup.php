<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");

$company_data = getCompanyDataDetails($company_id);
$gl_account_length = $company_data['data']['gl_account_length'];


if (isset($_POST["createdata"])) {
  $addNewObj = createDataJournal($_POST);
  swalToast($addNewObj["status"], $addNewObj["message"], $_SERVER['PHP_SELF']);
}

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}
?>


<style>
  .content-wrapper {
    height: 100vh !important;
  }

  .content-wrapper table tr:nth-child(even) td {
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

  .date-range-input.keyword-input .form-input {
    display: block;
    width: 100% !important;
  }

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

  .transactional-book-table tr th {
    padding: 10px 8px !important;
  }


  .dataTables_scrollBody tfoot th {
    background: none !important;
  }

  .dataTables_scrollHead {
    margin-bottom: 40px;
  }

  .dataTables_scrollBody {
    max-height: 100% !important;
    height: 60vh !important;
    overflow-x: auto !important;
    overflow-y: auto !important;
    transition-delay: 0.2s;
  }

  .content-wrapper.fullscreen-mode .dataTables_scrollBody {
    height: 82vh !important;
    max-height: 78vh !important;
    overflow-x: scroll !important;
  }

  .dataTables_scrollBody::-webkit-scrollbar {
    background-color: transparent;
    width: 0px;
    height: 0px;
    cursor: pointer;
  }

  .dataTables_scrollBody:hover::-webkit-scrollbar {
    width: 8px;
    height: 8px;
  }

  .dataTables_scrollBody:hover::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.2);
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

  .transactional-book-table tr th {
    text-align: center !important;
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
    margin-right: 3rem;
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
    padding-right: 5px !important;
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
    top: 0px;
    left: 0;
    margin: 15px 0;
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

  .daybook-tabs {
    flex-direction: row-reverse;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0px;
  }

  #containerThreeDot #menu-wrap .dots>div,
  #containerThreeDot #menu-wrap .dots>div:after,
  #containerThreeDot #menu-wrap .dots>div:before {
    background-color: #003060 !important;
  }

  #containerThreeDot #menu-wrap .menu {
    box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;
  }

  #containerThreeDot #menu-wrap .toggler:checked~.menu {
    width: 350px !important;
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
      gap: 10px;
      flex-direction: column-reverse;
      flex-wrap: nowrap;
    }

    .dataTables_length {
      margin-left: 10px;
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
      margin: 0px;
      display: flex;
      gap: 10px;
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
      margin-bottom: 0px;
      width: 100%;
      padding: 0px 10px;
    }

    div.dataTables_wrapper div.dataTables_filter {
      padding-bottom: 0px;
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

  @media only screen and (max-width: 1023px) {
    #containerThreeDot {
      width: 100% !important;
      padding: 0;
    }

    #containerThreeDot #menu-wrap .toggler:checked~.menu {
      width: 100%;
    }

    .chartContainer {
      width: 100%;
      height: 500px;
      margin-top: 2em;
    }

    .reports-card .filter-list a {
      position: static !important;
      margin: 0;
      height: 30px;
    }

    .chartContainer {
      width: 100%;
      height: 500px;
      margin-top: 2em;
    }

    .daybook-tabs {
      margin-bottom: 0px;
    }

    .daybook-filter-list.filter-list {
      display: flex;
      gap: 7px;
      justify-content: space-between;
      top: 0px;
      left: 0px;
      margin: 10px 0;
      width: 100%;
    }
  }

  /* 
  .dataTables_scrollHeadInner,
  .dataTables_scrollHeadInner table {
    width: 100% !important;
  } */


  td.dataTables_empty {
    position: absolute;
    left: 35%;
    top: 30%;
    transform: translate(100px, 50px);
    background: transparent !important;
  }

  .is-daybook #containerThreeDot {
    height: 50px;
    margin: 0px;
    width: auto !important;
    position: absolute;
    z-index: 9;
    top: 3px;
    right: 17.5rem;
  }

  @media (min-width: 768px) and (max-width: 1023px) {}

  @media (min-width: 980px) and (max-width: 1023px) {}
</style>

<link rel="stylesheet" href="../../public/assets/new_listing.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper is-daybook">
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid px-0 px-md-3">
      <?php
      $keyword = '';
      $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);
      if (!isset($_POST['from_date']) || empty($_POST['from_date'])) {
        $_POST['from_date'] = date('Y-m-d', strtotime('-1 day'));
        $_POST['to_date'] = date('Y-m-d');

        $start_date = $_POST['from_date'];
        $end_date = $_POST['to_date'];
      } else {
        //echo 1;
        if (isset($_POST['from_date']) || (count($_SESSION["reportFilter"] ?? []) > 0)) {
          $start_date = $_POST['from_date'] ?? $_SESSION["reportFilter"]["from_date"];
          $end_date = $_POST['to_date'] ?? $_SESSION["reportFilter"]["to_date"];
          $_POST['from_date'] = $start_date;
          $_POST['to_date'] = $end_date;
          $_SESSION["reportFilter"] = $_POST;
        } else {
          $start = explode('-', $variant_sql['data'][0]['year_start']);
          $end = explode('-', $variant_sql['data'][0]['year_end']);
          $start_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
          $end_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
          if (isset($_GET["to_date"]) && $_GET["to_date"] != "") {
            $end_date = $_GET["to_date"];
          }
          $_POST['from_date'] = $start_date;
          $_POST['to_date'] = $end_date;
          $_POST['drop_val'] = 'fYDropdown';
          $_POST['drop_id'] = $variant_sql['data'][0]['year_variant_id'];
        }
      }

      $cond = '';
      //     WHEN Order_num LIKE 'PO%' THEN (SELECT vendor_id FROM erp_branch_purchase_order WHERE po_number = summary1.Order_num)
      //     WHEN Order_num LIKE 'SO%' THEN (SELECT customer_id FROM erp_branch_sales_order WHERE so_number = summary1.Order_num)
      // END as party_id,
      // CASE
      if (isset($_POST['keyword']) && !empty($_POST['keyword'])) {
        $keyword = $_POST['keyword'];
        $cond .= " AND (journal.jv_no LIKE '%" . $keyword . "%' OR journal.party_code LIKE '%" . $keyword . "%' OR journal.party_name LIKE '%" . $keyword . "%' OR journal.refarenceCode LIKE '%" . $keyword . "%' OR journal.parent_slug LIKE '%" . $keyword . "%' OR journal.journalEntryReference LIKE '%" . $keyword . "%' OR journal.documentNo LIKE '%" . $keyword . "%')";
      }
      ?>
      <!-- row -->
      <div class="row p-0 m-0">
        <div class="col-12 p-0">
          <?php if ($_GET['mode'] == 'sablager') { ?>

            <div class="card card-tabs bg-transparent">
              <div class="p-0 my-2">
                <ul class="nav nav-tabs daybook-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                  <li class="pt-4 pt-md-0 px-md-3 d-flex justify-content-between align-items-center gap-4" style="width:  100%;">
                    <div class="label-select">
                      <h3 class="card-title font-bold text-md">Transactional Day Book (From <?= formatDateORDateTime($start_date) ?> TO <?= formatDateORDateTime($end_date) ?>)</h3>
                    </div>
                    <div class="d-flex align-items-center">
                      <div class="daybook-filter-list filter-list">
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn waves-effect waves-light"><i class="fa fa-stream mr-2"></i> Concise View ( Transactional level)</a>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=sablager" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active"></i>Detailed view ( Item level)</a>
                      </div>
                      <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand fa-2x"></i></button>
                    </div>
                  </li>
                </ul>
              </div>

              <div class="card card-tabs mb-0" style="border-radius: 20px;">
                <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                </form>
                <div class="tab-content" id="custom-tabs-two-tabContent">
                  <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #dbe5ee; border-radius: 20px;">
                    <div id="containerThreeDot">
                      <div id="menu-wrap">
                        <input type="checkbox" class="toggler bg-transparent searchboxop" checked />
                        <div class="dots">
                          <div></div>
                        </div>
                        <div class="menu ">
                          <div class="fy-custom-section fy-dropdown">
                            <div class="fy-dropdown-section">
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
                              <h6 class="text-xs font-bold ">Custom Range</h6>
                              <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                                <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="" />
                                <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange" />
                                <div class="date-range-input d-flex">
                                  <div class="form-input">
                                    <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $_POST['from_date']; ?>" required>
                                  </div>
                                  <div class="form-input">
                                    <label class="mb-0" for="">To</label>
                                    <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
                                  </div>
                                </div>
                                <div class="date-range-input keyword-input">
                                  <div class="form-input">
                                    <label class="text-xs font-bold" for="">Keyword</label>
                                    <input type="text" class="form-control w-100" name="keyword" id="keyword" value="<?= $_POST['keyword']; ?>">
                                  </div>
                                </div>
                                <button type="submit" class="btn btn-primary float-right" id="rangeid" name="add_date_form">Apply</button>
                              </form>
                              <h6 class="text-xs font-bold "><span class="customRangeCla"></span></h6>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                    <?php
                    $drcond = '';
                    $crcond = '';
                    if (isset($_GET['glId'])) {
                      $drcond .= " AND debit.glId=" . $_GET['glId'] . "";
                      $crcond .= " AND credit.glId=" . $_GET['glId'] . "";
                    }
                    if (isset($_GET['subGlCode'])) {
                      $drcond .= " AND debit.subGlCode=" . $_GET['subGlCode'] . "";
                      $crcond .= " AND credit.subGlCode=" . $_GET['subGlCode'] . "";
                    }


                    //     WHEN Order_num LIKE 'PO%' THEN (SELECT vendor_id FROM erp_branch_purchase_order WHERE po_number = summary1.Order_num)
                    //     WHEN Order_num LIKE 'SO%' THEN (SELECT customer_id FROM erp_branch_sales_order WHERE so_number = summary1.Order_num)
                    // END as party_id,
                    // CASE

                    $sql_list = "SELECT summary1.*,
                    CASE
                        
                        WHEN Order_num LIKE 'PO%' THEN (SELECT po_date FROM erp_branch_purchase_order WHERE po_number = summary1.Order_num AND company_id=$company_id)
                            WHEN Order_num LIKE 'SO%' THEN (SELECT so_date FROM erp_branch_sales_order WHERE so_number = summary1.Order_num AND company_id=$company_id)
                        END as order_date
                    FROM
                        (SELECT
                            table1.jid as jid,
                            table1.company_id as company_id,
                            table1.branch_id as branch_id,
                            table1.location_id as location_id,
                            table1.jv_no as jv_no,
                            table1.party_code AS party_code,
                            table1.party_name AS party_name,
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
                            table1.sub_gl_code,
                            table1.sub_gl_name,
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
                                journal.party_code AS party_code,
                                journal.party_name AS party_name,
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
                                debit.subGlCode AS sub_gl_code,
                                debit.subGlName AS sub_gl_name,
                                debit.debit_amount AS Amount,
                                'DR' AS Type,
                                journal.journal_created_at as journal_created_at,
                                journal.journal_created_by as journal_created_by,
                                journal.journal_updated_at as journal_updated_at,
                                journal.journal_updated_by as journal_updated_by
                            FROM
                                `" . ERP_ACC_JOURNAL . "` AS journal
                            INNER JOIN
                                (SELECT journal_id,glId,debit_amount,subGlCode,subGlName FROM `" . ERP_ACC_DEBIT . "`) AS debit
                                ON
                                    debit.journal_id = journal.id
                            WHERE
                                journal.journal_status='active' AND
                                journal.company_id=$company_id AND
                                journal.branch_id=$branch_id AND
                                journal.location_id=$location_id AND
                                journal.postingDate BETWEEN '" . $start_date . "' AND '" . $end_date . "' " . $drcond . "" . $cond . ") AS main_report)
                            UNION ALL
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
                                journal.party_code AS party_code,
                                journal.party_name AS party_name,
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
                                credit.subGlCode AS sub_gl_code,
                                credit.subGlName AS sub_gl_name,
                                credit.credit_amount*(-1) AS Amount,
                                'CR' AS Type,
                                journal.journal_created_at as journal_created_at,
                                journal.journal_created_by as journal_created_by,
                                journal.journal_updated_at as journal_updated_at,
                                journal.journal_updated_by as journal_updated_by
                            FROM
                                `" . ERP_ACC_JOURNAL . "` AS journal
                            INNER JOIN
                                (SELECT journal_id,glId,credit_amount,subGlCode,subGlName FROM `" . ERP_ACC_CREDIT . "`) AS credit
                                ON
                                    credit.journal_id = journal.id
                            WHERE
                                journal.journal_status='active' AND
                                journal.company_id=$company_id AND
                                journal.branch_id=$branch_id AND
                                journal.location_id=$location_id AND
                                journal.postingDate BETWEEN '" . $start_date . "' AND '" . $end_date . "'  " . $crcond . " " . $cond . ") as mainReport)) as table1
                            INNER JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` as coa
                            ON table1.glId = coa.id
                            ) AS summary1 ORDER BY summary1.jid DESC ;";

                    // console($sql_list);
                    $queryset = queryGet($sql_list, true);
                    // console($queryset);
                    $num_list = $queryset['numRows'];

                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_ACC_JOURNAL_SUB", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                    $settingsCheckbox = unserialize($settingsCh);
                    //console($settingsCheckbox);


                    if ($num_list > 0) {
                      $i = 1;
                    ?>
                      <!-- <table class="table defaultDataTable table-hover" data-paging="true" data-responsive="false"> -->
                      <table id="dataTable" class="table table-hover transactional-book-table" width="100%" data-paging="true" data-responsive="false" style="position: relative;">

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
                              <th>Reference No</th>
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
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Party Code</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Party Name</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>GL Code</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>GL Name</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Sub GL Code</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Sub GL Name</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Transaction Type</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Narration</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Type(Dr/Cr)</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Amount</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Clearing Document No</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Clearing Document Date</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Cleared By</th>
                            <?php } ?>
                          </tr>
                        </thead>

                        <tbody class="">
                          <?php
                          $datas = $queryset['data'];

                          // console($datas);
                          foreach ($datas as $data) {
                            $i = 1;
                            $ClearingDocumentNo = '-';
                            $ClearingDocumentDate = '-';
                            $ClearedBy = '-';


                          ?>
                            <tr>
                              <?php if (in_array($i, $settingsCheckbox)) { ?>
                                <td> <?php echo $branchNameNav; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo $locationNameNav; ?></td>
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
                                <td><?php echo ($data['referenceCode']);  ?></td>
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
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['party_code'] ?? '-');  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['party_name'] ?? '-');  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['gl_code']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['gl_label']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['sub_gl_code']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['sub_gl_name']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['journal_entry_ref']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php
                                    echo WordLimiter($data['remark'], 5);

                                    ?>

                                </td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td class="<?php if ($data['type'] == 'DR') {
                                              echo 'red-text';
                                            } else {
                                              echo 'green-text';
                                            }  ?>"><?php echo ($data['type']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td class="text-right"><?= decimalValuePreview($data['Amount'])  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo $ClearingDocumentNo;  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo $ClearingDocumentDate;  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo $ClearedBy;  ?></td>
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
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Location</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Accounting Document No</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Document No</th>
                            <?php }
                             $j++;
                             if (in_array($j, $settingsCheckbox)) { ?>
                               <th>Reference No</th>
                             <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Posting Date</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Created Date</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Created By</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Order No</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Order Date</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Party Code</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Party Name</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>GL Code</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>GL Name</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Sub GL Code</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Sub GL Name</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Transaction Type</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Narration</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Type(Dr/Cr)</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Amount</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Clearing Document No</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Clearing Document Date</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Cleared By</th>
                            <?php } ?>
                          </tr>
                        </tfoot>

                      </table>
                    <?php } else { ?>
                      <table id="dataTable" class="table defaultDataTable table-hover" width="100%">
                        <thead>
                          <tr>
                            <td>
                              Data not Found
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
                        <div class="modal-body" style="max-height: 450px;">
                          <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                          <input type="hidden" name="pageTableName" value="ERP_ACC_JOURNAL_SUB" />
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
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Party Code</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Party Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    GL Code</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    GL Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Sub GL Code</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Sub GL Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Transaction Type</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Narration</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Type(Dr/Cr)</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Amount</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Clearing Document No</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Clearing Document Date</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Cleared By</td>
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
          <?php } else { ?>

            <div class="card card-tabs bg-transparent">
              <div class="p-0 my-0">
                <ul class="nav nav-tabs daybook-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                  <li class="pt-4 pt-md-0 px-md-3 d-flex justify-content-between align-items-center gap-4" style="width:  100%;">
                    <div class="label-select">
                      <h3 class="card-title font-bold text-md">Transactional Day Book (From <?= formatDateORDateTime($start_date) ?> TO <?= formatDateORDateTime($end_date) ?>)</h3>
                    </div>
                    <div class="d-flex align-items-center">
                      <div class="daybook-filter-list filter-list">
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn active waves-effect waves-light"><i class="fa fa-stream mr-2 active"></i> Concise View ( Transactional level)</a>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=sablager" class="btn waves-effect waves-light"><i class="fa fa-list mr-2"></i>Detailed view ( Item level)</a>
                      </div>
                      <button class="btn btn-sm" onclick="openFullscreen()"><i class="fa fa-expand fa-2x"></i></button>
                    </div>
                  </li>


                </ul>
              </div>

              <div class="card card-tabs mb-0" style="border-radius: 20px;">
                <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">


                </form>

                <div class="tab-content" id="custom-tabs-two-tabContent">
                  <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #dbe5ee; border-radius: 20px;">
                    <div id="containerThreeDot">
                      <div id="menu-wrap">
                        <input type="checkbox" class="toggler bg-transparent searchboxop" checked />
                        <div class="dots">
                          <div></div>
                        </div>
                        <div class="menu ">
                          <div class="fy-custom-section fy-dropdown">
                            <div class="fy-dropdown-section">
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
                              <h6 class="text-xs font-bold ">Custom Range</h6>
                              <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                                <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="" />
                                <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange" />
                                <div class="date-range-input d-flex">
                                  <div class="form-input">
                                    <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $_POST['from_date']; ?>">
                                  </div>
                                  <div class="form-input">
                                    <label class="mb-0" for="">To</label>
                                    <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>">
                                  </div>
                                </div>
                                <div class="date-range-input keyword-input">
                                  <div class="form-input">
                                    <label class="text-xs font-bold" for="">Keyword</label>
                                    <input type="text" class="form-control" name="keyword" id="keyword" value="<?= $_POST['keyword']; ?>">
                                  </div>
                                </div>
                                <button type="submit" class="btn btn-primary float-right" id="rangeid" name="add_date_form">Apply</button>
                              </form>
                              <h6 class="text-xs font-bold "><span class="customRangeCla"></span></h6>
                            </div>


                          </div>
                        </div>
                      </div>
                    </div>
                    <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>



                    <?php


                    $sql_list = "SELECT summary1.*,
                    CASE
                        
                        WHEN Order_num LIKE 'PO%' THEN (SELECT po_date FROM erp_branch_purchase_order WHERE po_number = summary1.Order_num AND company_id=$company_id)
                            WHEN Order_num LIKE 'SO%' THEN (SELECT so_date FROM erp_branch_sales_order WHERE so_number = summary1.Order_num AND company_id=$company_id)
                        END as order_date
                    FROM
                        (SELECT
                            table1.jid as jid,
                            table1.company_id as company_id,
                            table1.branch_id as branch_id,
                            table1.location_id as location_id,
                            table1.jv_no as jv_no,
                            table1.party_code AS party_code,
                            table1.party_name AS party_name,
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
                                journal.party_code AS party_code,
                                journal.party_name AS party_name,
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
                                journal.location_id=$location_id AND
                                journal.postingDate BETWEEN '" . $start_date . "' AND '" . $end_date . "' " . $cond . ") AS main_report)
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
                                journal.party_code AS party_code,
                                journal.party_name AS party_name,
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
                                journal.location_id=$location_id AND
                                journal.postingDate BETWEEN '" . $start_date . "' AND '" . $end_date . "' " . $cond . ") as mainReport)) as table1
                            INNER JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` as coa
                            ON table1.glId = coa.id
                            ) AS summary1 ORDER BY summary1.jid DESC ;";

                    $queryset = queryGet($sql_list, true);
                    // console($queryset);
                    $num_list = $queryset['numRows'];

                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_ACC_JOURNAL", $_SESSION["logedBranchAdminInfo"]["adminId"]);
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
                              <th>Reference No</th>
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
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Party Code</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Party Name</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>GL Code</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>GL Name</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Transaction Type</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Transaction Activity</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Narration</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Type(Dr/Cr)</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Amount</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Clearing Document No</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Clearing Document Date</th>
                            <?php }
                            $i++;
                            if (in_array($i, $settingsCheckbox)) { ?>
                              <th>Cleared By</th>
                            <?php } ?>
                          </tr>
                        </thead>

                        <tbody class="">
                          <?php
                          $datas = $queryset['data'];

                          foreach ($datas as $data) {
                            $i = 1;
                            //console($data);

                            $ClearingDocumentNo = '-';
                            $ClearingDocumentDate = '-';
                            $ClearedBy = '-';

                            if ($data['parent_slug'] == 'grn') {
                              $grnIvSelect = queryGet("SELECT grnIvCode,postingDate,grnCreatedBy FROM erp_grninvoice WHERE grnId=" . $data['parent_id'] . " ");
                              // console($grnIvSelect);
                              $ClearingDocumentNo = $grnIvSelect['data']['grnIvCode'] ?? '-';
                              $ClearingDocumentDate = $grnIvSelect['data']['postingDate'] ? formatDateORDateTime($grnIvSelect['data']['postingDate']) : '-';
                              $ClearedBy = $grnIvSelect['data']['grnCreatedBy'] ? getCreatedByUser($grnIvSelect['data']['grnCreatedBy']) : '-';
                            } else if ($data['parent_slug'] == 'grniv') {

                              $grnIvSelect = queryGet("SELECT l.grn_id,l.created_at as logcreated_at,l.created_by as logcreated_by, p.paymentCode, p.postingDate, p.transactionId, p.created_by as paymentcreated_by
                              FROM erp_grn_payments_log l
                              LEFT JOIN erp_grn_payments p ON l.payment_id = p.payment_id
                              WHERE l.grn_id = " . $data['parent_id'] . "", true);

                              // console($grnIvSelect);
                              if ($grnIvSelect['numRows'] > 0) {
                                $ClearingDocumentNo = '';
                                $ClearingDocumentDate = '';
                                $ClearedBy = '';
                                foreach ($grnIvSelect as $rowpaylog) {
                                  $ClearingDocumentNo .= $grnIvSelect['data']['paymentCode'] ?? '-' . ',';
                                  $ClearingDocumentDate .= $grnIvSelect['data']['logcreated_at'] ? formatDateORDateTime($grnIvSelect['data']['logcreated_at']) : '-' . ',';
                                  $ClearedBy .= $grnIvSelect['data']['logcreated_by'] ? getCreatedByUser($grnIvSelect['data']['logcreated_by']) : '-' . ',';
                                }
                              }
                            } else if ($data['parent_slug'] == 'Payment') {

                              $grnIvSelect = queryGet("SELECT paymentCode,postingDate,transactionId,created_by FROM erp_grn_payments WHERE journal_id=" . $data['jid'] . " ");
                              // console($grnIvSelect);
                              $ClearingDocumentNo = $grnIvSelect['data']['paymentCode'] . '(' . $grnIvSelect['data']['transactionId'] . ')' ?? '-';
                              $ClearingDocumentDate = $grnIvSelect['data']['postingDate'] ? formatDateORDateTime($grnIvSelect['data']['postingDate']) : '-';
                              $ClearedBy = $grnIvSelect['data']['created_by'] ? getCreatedByUser($grnIvSelect['data']['created_by']) : '-';
                            }
                          ?>
                            <tr>
                              <?php if (in_array($i, $settingsCheckbox)) { ?>
                                <td> <?php echo $branchNameNav; ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo $locationNameNav; ?></td>
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
                                <td><?php echo ($data['referenceCode']);  ?></td>
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
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['party_code'] ?? '-');  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['party_name'] ?? '-');  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['gl_code']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['gl_label']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['journal_entry_ref']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo ($data['parent_slug']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php
                                    echo WordLimiter($data['remark'], 5);
                                    ?>

                                  <button type="button" class="btn" data-toggle="tooltip" data-placement="top" title="<?= $data['remark'] ?>">
                                    <i class="fa fa-info font-bold"></i>
                                  </button>
                                </td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td class="<?php if ($data['type'] == 'DR') {
                                              echo 'red-text';
                                            } else {
                                              echo 'green-text';
                                            }  ?>"><?php echo ($data['type']);  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td class="text-right"><?= decimalValuePreview($data['Amount'])  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo $ClearingDocumentNo;  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo $ClearingDocumentDate;  ?></td>
                              <?php }
                              $i++;
                              if (in_array($i, $settingsCheckbox)) { ?>
                                <td><?php echo $ClearedBy;  ?></td>
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
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Location</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Accounting Document No</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Document No</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Reference No</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Posting Date</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Created Date</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Created By</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Order No</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Order Date</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Party Code</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Party Name</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>GL Code</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>GL Name</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Transaction Type</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Transaction Activity</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Narration</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Type(Dr/Cr)</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Amount</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Clearing Document No</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Clearing Document Date</th>
                            <?php }
                            $j++;
                            if (in_array($j, $settingsCheckbox)) { ?>
                              <th>Cleared By</th>
                            <?php } ?>
                          </tr>
                        </tfoot>

                      </table>
                    <?php } else { ?>
                      <table id="dataTable" class="table defaultDataTable table-hover">
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
                        <div class="modal-body" style="max-height: 450px;">
                          <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                          <input type="hidden" name="pageTableName" value="ERP_ACC_JOURNAL" />
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
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Party Code</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Party Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    GL Code</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    GL Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Transaction Type</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Transaction Activity</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Narration</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Type(Dr/Cr)</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Amount</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Clearing Document No</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Clearing Document Date</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                    Cleared By</td>
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
          <?php } ?>
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->
</div>

<?php
require_once("../common/footer.php");
?>


<!-- CHANGES -->
<script>
  $(document).ready(function() {
    var numlist = <?= $num_list ?>;
    console.log(numlist);
    if (numlist > 0) {
      $(".searchboxop").prop('checked', false);
    } else {
      $(".searchboxop").prop('checked', true);
    }
  });
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

    $(".grand-checkbox").on("click", function() {

      // Check or uncheck all checkboxes within the table based on the grand checkbox state
      $(".colomnTable tr td input[type='checkbox']").prop("checked", this.checked);

    });




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
  //var elem = document.getElementById("listTabPan");

  // function openFullscreen() {
  //   if (elem.requestFullscreen) {
  //     elem.requestFullscreen();
  //   } else if (elem.webkitRequestFullscreen) {
  //     /* Safari */
  //     elem.webkitRequestFullscreen();
  //   } else if (elem.msRequestFullscreen) {
  //     /* IE11 */
  //     elem.msRequestFullscreen();
  //   }
  //   $(".content-wrapper").toggleClass("fulldcreen-mode");
  // }


  function openFullscreen() {
    var elem = document.getElementById("listTabPan") // Assuming you want to fullscreen the entire document

    if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
      if (elem.requestFullscreen) {
        elem.requestFullscreen();
      } else if (elem.webkitRequestFullscreen) {
        /* Safari */
        elem.webkitRequestFullscreen();
      } else if (elem.msRequestFullscreen) {
        /* IE11 */
        elem.msRequestFullscreen();
      }
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.webkitExitFullscreen) {
        /* Safari */
        document.webkitExitFullscreen();
      } else if (document.msExitFullscreen) {
        /* IE11 */
        document.msExitFullscreen();
      }
    }
  }

  document.addEventListener('fullscreenchange', exitHandler);
  document.addEventListener('webkitfullscreenchange', exitHandler);
  document.addEventListener('MSFullscreenChange', exitHandler);

  function exitHandler() {
    if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
      $(".content-wrapper").removeClass("fullscreen-mode");
    } else {
      $(".content-wrapper").addClass("fullscreen-mode");
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
<style>
  .dataTable thead {
    /* position: sticky; */
    top: 0 !important;
    
  }
</style>