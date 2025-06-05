<div class="content-wrapper">
    <section class="content">
      <div class="container-fluid">

        <div class="row p-0 m-0">
          <div class="col-12 mt-2 p-0">
            <div class="card card-tabs">
              <div class="p-0 pt-1 my-2">

                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                  <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                    <h3 class="card-title">Manage Vendor Invoice</h3>
                    <div class="filter-list vendor-invoice-tab">
                      <a href="<?= LOCATION_URL; ?>manage-vendor-invoice.php?value=all" class="btn waves-effect waves-light"><i class="fa fa-stream mr-2"></i>All</a>
                      <a href="<?= LOCATION_URL; ?>manage-vendor-invoice.php?value=payable" class="btn waves-effect waves-light"><i class="fa fa-clock mr-2"></i>Payable</a>
                      <a href="<?= LOCATION_URL; ?>manage-vendor-invoice.php?value=due" class="btn waves-effect waves-light"><i class="fa fa-clock mr-2"></i>Due</a>
                      <a href="<?= LOCATION_URL; ?>manage-vendor-invoice.php?value=overdue" class="btn active waves-effect waves-light"><i class="fa fa-clock mr-2 active"></i>OverDue</a>
                      <a href="<?= LOCATION_URL; ?>manage-vendor-invoice.php?value=paid" class="btn waves-effect waves-light"><i class="fa fa-clock mr-2"></i>Paid</a>
                    </div>
                    <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>
                  </li>
                </ul>
              </div>
              <div class="card card-tabs mb-0" style="border-radius: 20px;">
              <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return srch_frm();">
                <div class="card-body">
                  <div class="row filter-serach-row">
                    <div class="col-lg-2 col-md-2 col-sm-12">
                      <a type="button" class="btn btn-info" data-toggle="modal" id = "initiate_id" style="position:absolute;"> Initiate Payment </a>
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-12">
                      <div class="section serach-input-section">
                        <input type="text" name="keyword" id="myInput" placeholder="" class="field form-control" value="<?php echo $keywd; ?>">
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

                    <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Filter Vendor Invoice</h5>

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


                <div class="tab-content" id="custom-tabs-two-tabContent">
                  <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #dbe5ee; overflow: auto;">

                  <?php
                    $cond = '';

                    $sts = " AND grniv.`grnStatus`!='deleted'";
                    if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                      $sts = ' AND grniv.`grnStatus`="' . $_REQUEST['status_s'] . '"';
                    }
  
                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                      $cond .= " AND grniv.`postingDate` between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                    }
  
  
                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                      $cond .= " AND (grn.`vendorCode` like '%" . $_REQUEST['keyword2'] . "%' OR grn.`vendorName` like '%" . $_REQUEST['keyword2'] . "%' OR grniv.`vendorDocumentNo` like '%" . $_REQUEST['keyword2'] . "%' OR grniv.`grnCode` like '%" . $_REQUEST['keyword2'] . "%' OR grniv.`grnIvCode` like '%" . $_REQUEST['keyword2'] . "%')";
                    } else {
                      if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                        $cond .= " AND (grn.`vendorCode` like '%" . $_REQUEST['keyword'] . "%' OR grn.`vendorName` like '%" . $_REQUEST['keyword'] . "%' OR grniv.`vendorDocumentNo` like '%" . $_REQUEST['keyword'] . "%'  OR grniv.`grnCode` like '%" . $_REQUEST['keyword'] . "%' OR grniv.`grnIvCode` like '%" . $_REQUEST['keyword'] . "%')";
                      }
                    }

                    $sql_list = "SELECT grniv.*, grn.`grnCreatedAt` AS grnDate, grn.`po_date` AS poDate FROM `" . ERP_GRNINVOICE . "` AS grniv LEFT JOIN `erp_grn` AS grn ON grn.`grnId` = grniv.`grnId` WHERE 1 ".$cond." AND grniv.`companyId`='$company_id' AND grniv.`branchId`='$branch_id' AND grniv.`locationId`='$location_id' AND grniv.`paymentStatus`='15' AND DATE_FORMAT(now(), '%Y-%m-%d') > DATE_FORMAT(grniv.`dueDate`,'%Y-%m-%d') ".$sts." ORDER BY grniv.`grnIvId` DESC limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                    $qry_list = queryGet($sql_list, true);
                    $num_list = $qry_list['numRows'];
  
  
                    $countShow = "SELECT count(*) FROM `" . ERP_GRNINVOICE . "` AS grniv LEFT JOIN `erp_grn` AS grn ON grn.`grnId` = grniv.`grnId` WHERE grniv.`companyId`='$company_id' AND grniv.`branchId`='$branch_id' AND grniv.`locationId`='$location_id' AND grniv.`paymentStatus`='15' AND DATE_FORMAT(now(), '%Y-%m-%d') > DATE_FORMAT(grniv.`dueDate`,'%Y-%m-%d') ".$sts." ORDER BY grniv.`grnIvId` DESC";
                    $countQry = mysqli_query($dbCon, $countShow);
                    $rowCount = mysqli_fetch_array($countQry);
                    $count = $rowCount[0];
                  ?>

                    <table id="dataTable" class="table table-hover transactional-book-table" data-paging="true" data-responsive="false" style="position: relative;">
                      <thead>
                        <tr>
                        <th></th>
                          <th>Vendor Code</th>
                          <th>Vendor Name</th>
                          <th>Invoice Number</th>
                          <th>Invoice Date</th>
                          <th>GRN/SRN Number</th>
                          <th>GRN/SRN Date</th>
                          <th>PO Number</th>
                          <th>PO Date</th>
                          <th>IV Number</th>
                          <th>IV Date</th>
                          <th>Due Date</th>
                          <th>Basic Amount</th>
                          <th>GST</th>
                          <th>TDS</th>
                          <th>Net Payable</th>
                          <th>Payment Made</th>
                          <th>Due Amt.</th>
                          <th>Due %</th>
                          <th>Status</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $sl = 0;
                        foreach ($qry_list["data"] as $key => $one) {
                          $rand = rand(10, 1000);
                          $statusLabel = fetchStatusMasterByCode($one['paymentStatus'])['data']['label'];
                          $statusClass = "";
                          if ($statusLabel == "paid") {
                            $statusClass = "status";
                          }
                          elseif ($statusLabel == "pending") {
                            $statusClass = "status-warning";
                          } 
                          elseif ($statusLabel == "partial paid") {
                            $statusClass = "status-secondary";
                          } else {
                            $statusClass = "status-danger";
                          }
                          if ($one['grnStatus'] == 'reverse') {
                            $statusLabel = 'Reversed';
                            $statusClass = "status-warning";
                          }
                          // console('imranali59059');
                          // console($paymentStatus);

                          $days = $one['credit_period'];
                          $date = date_create($one['invoice_date']);
                          date_add($date, date_interval_create_from_date_string($days . " days"));
                          $creditPeriod = date_format($date, "Y-m-d");
                          $sl += 1;
                        ?>
                          <tr>
                            <input type="hidden" name ="" id = "id_<?= $sl ?>" value = "<?= $one['grnIvId'] ?>" >
                            <?php 
                            if($statusLabel == "payable")
                            {
                            ?>
                            <td><input type="checkbox" id="check_box_<?= $sl ?>" name="check_box" class="checkbx" value="<?= $sl ?>"></td>
                            <?php
                            }
                            else
                            {
                              echo "<td></td>";
                            }
                            ?>
                            <td><?= $one['vendorCode'] ?></td>
                            <td><?= $one['vendorName'] ?></td>
                            <td><?= $one['vendorDocumentNo'] ?></td>
                            <td><?= date("d-m-Y", strtotime($one['vendorDocumentDate'])) ?></td>
                            <td><?= $one['grnCode'] ?? "-"?></td>
                            <td><?= date("d-m-Y", strtotime($one['grnDate'])) ?? "-"?></td>
                            <td><?= $one['grnPoNumber'] ?></td>
                            <td><?= $one['poDate'] != "" ? date("d-m-Y", strtotime($one['poDate'])) : "" ?></td>
                            <td><?= $one['grnIvCode'] ?></td>
                            <td><?= formatDateORDateTime($one['postingDate']) ?></td>
                            <td><?= date("d-m-Y", strtotime($one['dueDate'])); ?></td>
                            <td><?= $one['grnSubTotal'] ?></td>
                            <td><?= $one['grnTotalCgst'] + $one['grnTotalSgst'] + $one['grnTotalIgst'] ?></td>
                            <td><?= $one['grnTotalTds'] ?></td>
                            <td class="invAmt invoiceAmt" id="invoiceAmt_<?= $one['grnId'] ?>"><?= $one['grnTotalAmount'] ?></td>
                            <td><?= $one['grnTotalAmount'] -  $one['dueAmt'] ?></td>

                            <td class="dueAmt" id="dueAmt_<?= $one['grnId'] ?>"><?= $one['dueAmt'] ?></td>

                            <?php
                            $due_amt = $one['dueAmt'];
                            $inv_amt = $one['grnTotalAmount'];
                            $duePercentage = ($due_amt / $inv_amt) * 100;
                            ?>
                            <td class="duePercentage" id="duePercentage_<?= $one['grnId'] ?>"><?= round($duePercentage); ?>%</td>
                            <td><span class="text-uppercase text-nowrap <?= $statusClass ?> listStatus"><?= $statusLabel ?></span></td>
                            <td>
                              <div class="d-flex">
                                <input type="hidden" class="attr attr_<?= $rand ?>" value="<?= $one['dueAmt'] ?>">
                                <?php if ($one['grnStatus'] == 'active') { ?>
                                  <a style="cursor:pointer" data-id="<?= $one['grnIvId']; ?>" class="btn btn-sm reverseGRNIV" title="Reverse Now">
                                    <i class="far fa-undo po-list-icon"></i>
                                  </a>
                                <?php } ?>
                              </div>
                            </td>
                          </tr>
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
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- <div class="card pgi-body-card">
          <div class="card-header">
            <div class="head p-2">
              <h4>Manage Vendor Invoice</h4>
            </div>
          </div>
          <div class="card-body">
            <div class="pgi-body"> -->

        <!-- </div>
          </div>
        </div> -->
      </div>
    </section>
  </div>
  <!-- For Pegination------->
  <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                echo  $_REQUEST['pageNo'];
                                              } ?>">
  </form>