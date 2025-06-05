<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");


// console($_SESSION);

if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"]);
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

$BranchSoObj = new BranchSo();

if (isset($_POST['addNewSOFormSubmitBtn'])) {
  // console($_POST);
  // exit;
  $addBranchSo = $BranchSoObj->addBranchSo($_POST);
  //console($addBranchSo);
  if ($addBranchSo['status'] == "success") {
    $addBranchSoItems = $BranchSoObj->addBranchSoItems($_POST, $addBranchSo['lastID']);
    //console($addBranchSoItems);
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
<style>
  .filter-list a.active {
    background-color: #003060;
    color: #fff;
  }

  .loading {
    cursor: wait;
  }
</style>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<?php
if (isset($_GET['customer-so-creation'])) { ?>
  ...
<?php } else { ?>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- row -->
        <?php
        $soDetails = $BranchSoObj->fetchBranchSoListing()['data'];
        // $lists = $BranchSoObj->fetchAllSoDeliverySchedule()['data'];
        ?>
        <div class="row p-0 m-0">
          <div class="col-12 mt-2 p-0">
            <div class="p-0 pt-1 my-2">
              <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                  <h3 class="card-title">Manage Item order List</h3>
                  <!-- <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?customer-so-creation" class="btn btn-sm btn-primary btnstyle m-2 float-add-btn" style="line-height: 32px;"><i class="fa fa-plus"></i></a> -->
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
                            <?php require_once('salesorder-filter-list.php'); ?>
                            <div class="section serach-input-section">
                              <input type="text" name="keyword" id="myInput" placeholder="Search..." class="field form-control" value="<?php echo $keywd; ?>">
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
                        <!-- <div class="col-lg-1 col-md-1 col-sm-1">
                          <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?customer-so-creation" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                        </div> -->
                        <div class="col-lg-1 col-md-1 col-sm-12">
                          <a href="direct-create-invoice.php?sales_order_creation" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                        </div>
                      </div>

                    </div>

                    <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Filter Purchase Order</h5>

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

              <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="tab-content" id="custom-tabs-two-tabContent">
                  <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                    <?php
                    $cond = '';

                    $sts = " AND `sales_order`.status !='deleted'";
                    if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                      $sts = ' AND `sales_order`.status="' . $_REQUEST['status_s'] . '"';
                    }

                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                      $cond .= " AND `sales_order`.created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                    }

                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                      $cond .= "AND `sales_order`.so_number 
                              like '%" . $_REQUEST['keyword2'] . "%' OR `sales_order`.so_date 
                              like '%" . $_REQUEST['keyword2'] . "%' OR `delivery`.deliveryStatus 
                              like '%" . $_REQUEST['keyword2'] . "%'
                    ";
                    } else {
                      if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                        $cond .= " AND `sales_order`.so_number like '%" . $_REQUEST['keyword'] . "%'  OR `sales_order`.so_date like '%" . $_REQUEST['keyword'] . "%' OR `delivery`.deliveryStatus like '%" . $_REQUEST['keyword'] . "%'";
                      }
                    }

                    $sql_list = "SELECT 
                                    sales_order.so_id as so_id, 
                                    sales_order.so_number as so_number, 
                                    sales_order.delivery_date as delivery_date, 
                                    sales_order.customer_id as customer_id, 
                                    sales_order.billingAddress as billing_address, 
                                    sales_order.shippingAddress as shipping_address, 
                                    sales_order.so_date as so_date, 
                                    sales_order.credit_period as credit_period, 
                                    items.so_item_id as so_item_id, 
                                    items.itemCode as itemCode, 
                                    items.itemName as itemName, 
                                    items.qty as total_quantity, 
                                    items.uom as uom, 
                                    items.tax as tax, 
                                    items.totalDiscount as total_discount, 
                                    items.totalPrice as item_total_price, 
                                    delivery.so_delivery_id, 
                                    delivery.delivery_date, 
                                    delivery.deliveryStatus, 
                                    delivery.qty as delivery_qty 
                                  FROM 
                                    `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` as items, 
                                    `" . ERP_BRANCH_SALES_ORDER . "` as sales_order, 
                                    `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` as delivery 
                                  WHERE 
                                    1 " . $cond . " 
                                    AND sales_order.so_id = items.so_id 
                                    AND sales_order.company_id = $company_id
                                    AND sales_order.branch_id = $branch_id
                                    AND sales_order.location_id = $location_id
                                    AND sales_order.approvalStatus != 14 
                                    AND items.so_item_id = delivery.so_item_id " . $sts . " 
                                  ORDER BY 
                                    items.so_item_id DESC 
                                  limit 
                                    " . $GLOBALS['start'] . ", 
                                    " . $GLOBALS['show'] . "
                  ";
                    $qry_list = queryGet($sql_list, true);

                    // count sql for pagination 
                    $countSql = "SELECT 
                                    sales_order.so_id as so_id, 
                                    sales_order.so_number as so_number, 
                                    sales_order.delivery_date as delivery_date, 
                                    sales_order.customer_id as customer_id, 
                                    sales_order.billingAddress as billing_address, 
                                    sales_order.shippingAddress as shipping_address, 
                                    sales_order.so_date as so_date, 
                                    sales_order.credit_period as credit_period, 
                                    items.so_item_id as so_item_id, 
                                    items.itemCode as itemCode, 
                                    items.itemName as itemName, 
                                    items.qty as total_quantity, 
                                    items.uom as uom, 
                                    items.tax as tax, 
                                    items.totalDiscount as total_discount, 
                                    items.totalPrice as item_total_price, 
                                    delivery.so_delivery_id, 
                                    delivery.delivery_date, 
                                    delivery.deliveryStatus, 
                                    delivery.qty as delivery_qty 
                                  FROM 
                                    `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` as items, 
                                    `" . ERP_BRANCH_SALES_ORDER . "` as sales_order, 
                                    `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` as delivery 
                                  WHERE 
                                    1 " . $cond . " 
                                    AND sales_order.so_id = items.so_id 
                                    AND sales_order.company_id = $company_id
                                    AND sales_order.branch_id = $branch_id
                                    AND sales_order.location_id = $location_id
                                    AND sales_order.approvalStatus != 14 
                                    AND items.so_item_id = delivery.so_item_id " . $sts . " 
                                  ORDER BY 
                                    items.so_item_id DESC
                  ";
                    $qryCount_list = queryGet($countSql, true);
                    $count = $qryCount_list['numRows'];

                    // Calculate the total number of pages
                    $totalPages = ceil($count / $recordsPerPage);

                    // ***********************************************************
                    $cnt = $GLOBALS['start'] + 1;
                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_SALES_ORDER-ITEM-WISE", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                    $settingsCheckbox = unserialize($settingsCh);

                    if ($qry_list['numRows'] > 0) {
                    ?>
                      <table class="table defaultDataTable table-hover tableDataBody">
                        <thead>
                          <tr class="alert-light">
                            <th>#</th>
                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                              <th>SO Number</th>
                            <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                              <th>So Date</th>
                            <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                              <th>Delivery Date</th>
                            <?php  }
                            if (in_array(4, $settingsCheckbox)) { ?>
                              <th>Customer Name</th>
                            <?php }
                            if (in_array(5, $settingsCheckbox)) { ?>
                              <th>Item Name</th>
                            <?php  }
                            if (in_array(6, $settingsCheckbox)) { ?>
                              <th>Item Code</th>
                            <?php  }
                            if (in_array(8, $settingsCheckbox)) { ?>
                              <th>Qty</th>
                            <?php }
                            if (in_array(9, $settingsCheckbox)) { ?>
                              <th>UOM</th>
                            <?php }
                            if (in_array(10, $settingsCheckbox)) { ?>
                              <th>Tax (%)</th>
                            <?php }
                            if (in_array(11, $settingsCheckbox)) { ?>
                              <th>Discount (%)</th>
                            <?php }
                            if (in_array(12, $settingsCheckbox)) { ?>
                              <th>Total Price</th>
                            <?php }
                            if (in_array(13, $settingsCheckbox)) { ?>
                              <th>Delivery Status</th>
                            <?php }
                            if (in_array(14, $settingsCheckbox)) { ?>
                              <th>Delivery Qty</th>
                            <?php }
                            ?>
                            <!-- <th>Action</th> -->

                          </tr>
                        </thead>
                        <tbody class="tableBody">
                          <?php
                          // console($BranchSoObj->fetchBranchSoListing()['data']);
                          foreach ($qry_list['data'] as $oneSoList) {
                            $uomName = getUomDetail($oneSoList['uom'])['data']['uomName'];
                            console($getUomDetail);
                            if (fetchStatusMasterByCode($oneSoList['approvalStatus'])['data']['label'] == "approved") {
                              $approvalStatus = '<strong class="text-success">APPROVED</strong>';
                            } else {
                              $approvalStatus = '<strong class="text-dark">EXCEPTIONAL</strong>';
                            }
                          ?>
                            <tr class="tableOneRow">
                              <td><?= $cnt++ ?></td>
                              <?php if (in_array(1, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['so_number'] ?></td>
                              <?php }
                              if (in_array(2, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['so_date'] ?></td>
                              <?php }
                              if (in_array(3, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['delivery_date'] ?></td>
                              <?php }
                              if (in_array(4, $settingsCheckbox)) { ?>
                                <td><?= $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0]['trade_name'] ?></td>
                              <?php }
                              if (in_array(5, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['itemName'] ?></td>
                              <?php }
                              if (in_array(6, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['itemCode'] ?></td>
                              <?php }
                              if (in_array(8, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['total_quantity'] ?></td>
                              <?php }
                              if (in_array(9, $settingsCheckbox)) { ?>
                                <td><?= $uomName ?></td>
                              <?php }
                              if (in_array(10, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['tax'] ?></td>
                              <?php }
                              if (in_array(11, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['total_discount'] ?></td>
                              <?php }
                              if (in_array(12, $settingsCheckbox)) { ?>
                                <td class="text-right"><?= $oneSoList['item_total_price'] ?></td>
                              <?php }
                              if (in_array(13, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['deliveryStatus'] ?></td>
                              <?php }
                              if (in_array(14, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['delivery_qty'] ?></td>
                              <?php } ?>
                            </tr>
                            <?php $customerDetails =  $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0] ?>
                          <?php } ?>
                        </tbody>
                        <tbody>
                          <tr>
                            <td colspan="13">
                              <!-- Start .pagination -->
                              <?php
                              if ($count > 0 && $count > $GLOBALS['show']) {
                              ?>
                                <div class="pagination align-right">
                                  <?php pagination($count, "frm_opts"); ?>
                                </div>
                              <?php } ?>
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
                      <input type="hidden" name="pageTableName" value="ERP_BRANCH_SALES_ORDER-ITEM-WISE" />
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
                                So Date</td>
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
                                Item Name</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                Item Code</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="7" />
                                Qty</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(9, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="8" />
                                UOM</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(10, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="9" />
                                Tax (%)</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(11, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="10" />
                                Discount (%)</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(12, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="11" />
                                Total Price</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(13, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="12" />
                                Delivery Status</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(14, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="13" />
                                Delivery Qty</td>
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
  </div> <!-- For Pegination------->
  <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                echo  $_REQUEST['pageNo'];
                                              } ?>">
  </form>
  <!-- End Pegination from------->
<?php } ?>

<?php
require_once("../common/footer.php");
?>

<script>
  $(document).on("click", ".dlt-popup", function() {
    $(this).parent().parent().remove();
  });

  function rm() {
    // $(event.target).closest("tr").remove();
    $(this).parent().parent().parent().remove();
  }

  function addMultiQty(id) {
    let addressRandNo = Math.ceil(Math.random() * 100000);
    //$(`.modal-add-row_${id}`).append(`<tr><td><span class='has-float-label'><input type='date' name='listItem[${id}][deliverySchedule][${addressRandNo}][multiDeliveryDate]' class='form-control' placeholder='delivery date' required><label>Delivery date</label></span></td><td><span class='has-float-label'><input type='text' name='listItem[${id}][deliverySchedule][${addressRandNo}][quantity]' class='form-control multiQuantity' data-itemid="${id}" id='multiQuantity_${addressRandNo}' placeholder='quantity' required><label>quantity</label></span></td><td><a class='btn btn-danger' onclick='rm()'><i class='fa fa-minus'></i></a></td></tr>`);
    $(`.modal-add-row_${id}`).append(`
      <div class="modal-add-row">
        <div class="row modal-cog-right">
          <div class="col-lg-5 col-md-5 col-sm-5">
              <div class="form-input">
                  <label>Delivery date</label>
                  <input type="date" name="listItem[${id}][deliverySchedule][${id}][multiDeliveryDate]" class="form-control multiDeliveryDate" id="multiDeliveryDate_${id}" placeholder="delivery date" value="<?= $_GET['deliveryDate'] ?>">

              </div>
          </div>
          <div class="col-lg-5 col-md-5 col-sm-5">
              <div class="form-input">
                  <label>Quantity</label>
                  <input type="text" name="listItem[${id}][deliverySchedule][${id}][quantity]" class="form-control multiQuantity" data-itemid="${id}" id="multiQuantity_${id}" placeholder="quantity" value="1">

              </div>
          </div>
          <div class="col-lg-2 col-md-2 col-sm-2 dlt-popup">
              <a style="cursor: pointer" class="btn btn-danger">
                  <i class="fa fa-minus"></i>
              </a>
          </div>
        </div>
      </div>`);
  }
</script>



<script>
  $(document).ready(function() {
    loadItems();

    loadCustomers();


    // **************************************
    function loadItems() {
      // alert();
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
    // get customer details by id
    $("#customerDropDown").on("change", function() {
      let customerId = $(this).val();

      if (customerId > 0) {
        $(document).on("click", ".billToCheckbox", function() {
          if ($('input.billToCheckbox').is(':checked')) {
            // $(".shipTo").html(`checked ${customerId}`);
            $.ajax({
              type: "GET",
              url: `ajaxs/so/ajax-customers-address.php`,
              data: {
                act: "customerAddress",
                customerId
              },
              beforeSend: function() {
                $("#shipTo").html(`Loding...`);
              },
              success: function(response) {
                console.log(response);
                $("#shipTo").html(response);
              }
            });
          } else {
            $(".changeAddress").click();
            // $("#shipTo").html(`unchecked ${customerId}`);
          }
        });

        $(".customerIdInp").val(customerId);
        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-customers-list.php`,
          data: {
            act: "listItem",
            customerId
          },
          beforeSend: function() {
            $("#customerInfo").html(`<option value="">Loding...</option>`);
          },
          success: function(response) {
            console.log(response);
            $("#customerInfo").html(response);
            let creditPeriod = $("#spanCreditPeriod").text();
            $("#inputCreditPeriod").val(creditPeriod);
          }
        });
      }
    });

    $(document).on("click", "#pills-home-tab", function() {
      $("#saveChanges").html('<button type="button" class="btn btn-primary go">Go</button>');
    });
    $(document).on("click", "#pills-profile-tab", function() {
      $("#saveChanges").html('<button type="button" class="btn btn-primary" id="save">Save</button>');
    });

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
      calculateGrandTotalAmount();
    });

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
      $(`#itemTotalDiscount1_${rowNo}`).val(totalDiscount.toFixed(0));
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

    // function itemMaxDiscount(rowNo, keyValue = 0) {
    //   let itemMaxDis = $(`#itemMaxDiscount_${rowNo}`).html();
    //   console.log('this is max discount', itemMaxDis);
    //   console.log('this is key value', keyValue);
    //   if (parseFloat(keyValue) > parseFloat(itemMaxDis)) {
    //     console.log('max discount is over');
    //     $(`#itemSpecialDiscount_${rowNo}`).text(`Special Discount`);
    //     $(`#itemSpecialDiscount_${rowNo}`).show();
    //     // $(`#specialDiscount`).show();
    //   } else {
    //     $(`#itemSpecialDiscount_${rowNo}`).hide();
    //     // $(`#specialDiscount`).hide();
    //   }
    // }

    $(document).on("keyup blur click", ".itemQty", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      calculateOneItemAmounts(rowNo);
    });

    function checkSpecialDiscount() {
      let isSpecialDiscountApplied = false;

      $(".itemDiscount").each(function() {
        let rowNum = ($(this).attr("id")).split("_")[1];
        let discountPercentage = parseFloat($(this).val());
        discountPercentage = discountPercentage > 0 ? discountPercentage : 0;
        let maxDiscountPercentage = parseFloat($(`#itemMaxDiscount_${rowNum}`).html());
        maxDiscountPercentage = maxDiscountPercentage > 0 ? maxDiscountPercentage : 0;
        if (discountPercentage > maxDiscountPercentage) {
          isSpecialDiscountApplied = true;
        }
      });

      if (isSpecialDiscountApplied) {
        $(`#approvalStatus`).val(`12`);
        console.log('max');
      } else {
        $(`#approvalStatus`).val(`11`);
        console.log('ok');
      }
    }


    $(document).on("keyup", ".itemDiscount", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let keyValue = $(this).val();
      calculateOneItemAmounts(rowNo);
      // itemMaxDiscount(rowNo, keyValue);
      checkSpecialDiscount();
      // $(`#itemTotalDiscount1_${rowNo}`).attr('disabled', 'disabled');
    });

    // #######################################################
    $(document).on("keyup blur click change", ".multiQuantity", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let itemid = ($(this).data("itemid"));
      let thisVal = ($(this).val());
      calculateQuantity(rowNo, itemid, thisVal);
    });

    // #######################################################
    $(document).on("keyup", ".itemTotalDiscount1", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let itemDiscountAmt = ($(this).val());

      let itemQty = (parseFloat($(`#itemQty_${rowNo}`).val()) > 0) ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
      let itemUnitPrice = (parseFloat($(`#itemUnitPrice_${rowNo}`).val()) > 0) ? parseFloat($(`#itemUnitPrice_${rowNo}`).val()) : 0;

      let totalAmt = itemQty * itemUnitPrice;
      let discountPercentage = itemDiscountAmt * 100 / totalAmt;

      $(`#itemDiscount_${rowNo}`).val(discountPercentage.toFixed(0));

      // let itemDiscount = (parseFloat($(`#itemDiscount_${rowNo}`).val())) ? parseFloat($(`#itemDiscount_${rowNo}`).val()) : 0;

      console.log('total', itemQty, itemUnitPrice, discountPercentage);
      calculateOneItemAmounts(rowNo);

      // $(`#itemDiscount_${rowNo}`).attr('disabled', 'disabled');
      // discountCalculate(rowNo, thisVal);
    });

    // allItemsBtn
    $("#allItemsBtn").on('click', function() {
      window.location.href = "";
    })

    // itemWiseSearch
    $("#itemWiseSearch").on('click', function() {
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-so-list.php`,
        data: {
          act: "itemWiseSearch"
        },
        beforeSend: function() {
          $(".tableDataBody").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          console.log(response);
          $(".tableDataBody").html(response);
        }
      });
    })

    $(function() {
      $("#datepicker").datepicker({
        autoclose: true,
        todayHighlight: true
      }).datepicker('update', new Date());
    });

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


  $('.hamburger').click(function() {
    $('.hamburger').toggleClass('show');
    $('#overlay').toggleClass('show');
    $('.nav-action').toggleClass('show');
  });



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
</script>

<script>
  $(document).ready(function() {
    // Add the 'loading' class to the body element when the page starts loading
    $("body").addClass("loading");
  });

  $(window).on("load", function() {
    // Remove the 'loading' class from the body element when the page finishes loading
    $("body").removeClass("loading");
  });
</script>