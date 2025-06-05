<div class="content-wrapper">

    <div class="container-fluid">

        <!-- <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
            <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>PGI List</a></li>
            <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                    Create PGI</a></li>
            <li class="back-button">
                <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                    <i class="fa fa-reply po-list-icon"></i>
                </a>
            </li>
        </ol> -->

        <section class="recived-item">
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">

                    <div class="card card-tabs" style="border-radius: 20px;">

                        <div class="p-0 pt-1 my-2">

                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                    <h3 class="card-title">Quality Analysis</h3>
                                    <div class="filter-list vendor-invoice-tab">
                                        <a href="<?= LOCATION_URL; ?>recieved-item.php?value=all" class="btn waves-effect waves-light"><i class="fa fa-stream mr-2"></i>All</a>
                                        <a href="<?= LOCATION_URL; ?>recieved-item.php?value=fg" class="btn waves-effect waves-light active"><i class="fa fa-clock mr-2"></i>FG</a>
                                        <a href="<?= LOCATION_URL; ?>recieved-item.php?value=sfg" class="btn waves-effect waves-light"><i class="fa fa-clock mr-2"></i>SFG</a>
                                        <a href="<?= LOCATION_URL; ?>recieved-item.php?value=rm" class="btn waves-effect waves-light"><i class="fa fa-clock mr-2"></i>RM</a>
                                        <a href="<?= LOCATION_URL; ?>recieved-item.php?value=rejected" class="btn waves-effect waves-light"><i class="fa fa-clock mr-2"></i>Rejected</a>
                                    </div>
                                    <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>
                                </li>
                            </ul>
                        </div>

                        <div class="card card-tabs" style="border-radius: 20px;">
                            <form name="search" id="search" action="" method="post" onsubmit="return srch_frm();">
                                <div class="card-body" style="overflow: hidden;">
                                    <div class="row filter-serach-row">
                                        <div class="col-lg-2 col-md-2 col-sm-12">
                                            <a type="button" class="btn add-col waves-effect waves-light" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-lg-10 col-md-10 col-sm-12">
                                            <div class="row table-header-item">
                                                <div class="col-lg-12 col-md-11 col-sm-12">
                                                    <div class="section serach-input-section">
                                                        <input type="text" name="keyword" id="myInput" placeholder="" class="field form-control" value="">
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
                                                            <!-- <div class="col-lg-6 col-md-6 col-sm-6 col-12">
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
                                                    </div> -->
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
                                            <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                                <?php
                                                $cond = '';

                                                // $sts = " AND grniv.`grnStatus`!='deleted'";
                                                // if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                                //     $sts = ' AND grniv.`grnStatus`="' . $_REQUEST['status_s'] . '"';
                                                // }

                                                if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                                    $cond .= " AND grniv.`bornDate` between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                                }


                                                if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                                    $cond .= " AND (grn.`vendorCode` like '%" . $_REQUEST['keyword2'] . "%' OR grn.`vendorName` like '%" . $_REQUEST['keyword2'] . "%' OR grniv.`vendorDocumentNo` like '%" . $_REQUEST['keyword2'] . "%' OR grniv.`grnCode` like '%" . $_REQUEST['keyword2'] . "%' OR grniv.`grnIvCode` like '%" . $_REQUEST['keyword2'] . "%')";
                                                } else {
                                                    if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                                        $cond .= " AND (grn.`vendorCode` like '%" . $_REQUEST['keyword'] . "%' OR grn.`vendorName` like '%" . $_REQUEST['keyword'] . "%' OR grniv.`vendorDocumentNo` like '%" . $_REQUEST['keyword'] . "%'  OR grniv.`grnCode` like '%" . $_REQUEST['keyword'] . "%' OR grniv.`grnIvCode` like '%" . $_REQUEST['keyword'] . "%')";
                                                    }
                                                }

                                                $mode = "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))";
                                                queryGet($mode);

                                                $sql_list = "SELECT
                                                stocklog.`stockLogId`,
                                                stocklog.`companyId`,
                                                stocklog.`branchId`,
                                                stocklog.`locationId`,
                                                stocklog.`storageLocationId`,
                                                stocklog.`storageType`,
                                                stocklog.`itemId`,
                                                SUM(stocklog.itemQty) AS itemQty,
                                                stocklog.`remainingQty`,
                                                stocklog.`itemUom`,
                                                stocklog.`itemPrice`,
                                                stocklog.`refActivityName`,
                                                stocklog.`logRef`,
                                                stocklog.`refNumber`,
                                                stocklog.`min_stock`,
                                                stocklog.`max_stock`,
                                                stocklog.`bornDate`,
                                                stocklog.`postingDate`,
                                                stocklog.`createdAt`,
                                                stocklog.`createdBy`,
                                                stocklog.`updatedAt`,
                                                stocklog.`updatedBy`,
                                                stocklog.`status`,
                                                -- Include non-aggregated columns from grn
                                                item.*,
                                                stloc.*,
                                                grn.*
                                                
                                            FROM
                                                `erp_inventory_stocks_log` AS stocklog
                                            LEFT JOIN `erp_storage_location` AS stloc
                                            ON
                                                stloc.`storage_location_id` = stocklog.`storageLocationId`
                                            LEFT JOIN `erp_grn` AS grn
                                            ON
                                                grn.`grnCode` = stocklog.`logRef`
                                            LEFT JOIN `erp_inventory_items` AS item
                                            ON
                                                item.`itemId` = stocklog.`itemId`
                                            LEFT JOIN `erp_inventory_mstr_uom` AS uom
                                            ON
                                                uom.`uomId` = item.`baseUnitMeasure`
                                            WHERE
                                                1 AND stocklog.`companyId` = '$company_id' AND stocklog.`branchId` = '$branch_id' AND stocklog.`locationId` = '$location_id' AND(
                                                    stocklog.`refActivityName` = 'GRN' OR stocklog.`refActivityName` = 'PRODUCTION'
                                                ) AND stocklog.`storageType` = 'QaLocation' AND item.`goodsType` IN ('3','4') 
                                            GROUP BY
                                                item.`itemId`, -- Include non-aggregated columns in GROUP BY
                                                stocklog.`logRef`,
                                                stocklog.`storageLocationId` 
                                            ORDER BY
                                                stocklog.stockLogId
                                            DESC
                                            LIMIT  " . $GLOBALS['start'] . "," . $GLOBALS['show'] . ";";

                                                //     $sql_list = "SELECT 
                                                //     stocklog.*, stloc.*, grn.*, item.* 
                                                //   FROM `erp_inventory_stocks_log` AS stocklog 
                                                //   LEFT JOIN `erp_storage_location` AS stloc ON stloc.`storage_location_id` = stocklog.`storageLocationId` 
                                                //   LEFT JOIN `erp_grn` AS grn ON grn.`grnCode` = stocklog.`logRef` 
                                                //   LEFT JOIN `erp_inventory_items` AS item ON item.`itemId` = stocklog.`itemId` 
                                                //   LEFT JOIN `erp_inventory_mstr_uom` AS uom ON uom.`uomId` = item.`baseUnitMeasure`
                                                //   WHERE 1 " . $cond . " 
                                                //     AND stocklog.`companyId`='$company_id' 
                                                //     AND stocklog.`branchId`='$branch_id' 
                                                //     AND stocklog.`locationId`='$location_id' 
                                                //     AND (stocklog.`refActivityName`='GRN' 
                                                //     OR stocklog.`refActivityName`='PRODUCTION') 
                                                //     AND stocklog.`storageType`='QaLocation' 
                                                //     AND item.`goodsType` IN ('3','4') 
                                                //   ORDER BY stocklog.`stockLogId` DESC 
                                                //   LIMIT " . $GLOBALS['start'] . "," . $GLOBALS['show'] . ";";

                                                $qry_list = queryGet($sql_list, true);
                                                $num_list = $qry_list['numRows'];

                                                $countShow = "SELECT count(*) 
                                              FROM `erp_inventory_stocks_log` AS stocklog 
                                              LEFT JOIN `erp_storage_location` AS stloc ON stloc.`storage_location_id` = stocklog.`storageLocationId` 
                                              LEFT JOIN `erp_grn` AS grn ON grn.`grnCode` = stocklog.`logRef` 
                                              LEFT JOIN `erp_inventory_items` AS item ON item.`itemId` = stocklog.`itemId` 
                                              LEFT JOIN `erp_inventory_mstr_uom` AS uom ON uom.`uomId` = item.`baseUnitMeasure`
                                              WHERE 1 " . $cond . " 
                                                AND stocklog.`companyId`='$company_id' 
                                                AND stocklog.`branchId`='$branch_id' 
                                                AND stocklog.`locationId`='$location_id' 
                                                AND (stocklog.`refActivityName`='GRN' 
                                                OR stocklog.`refActivityName`='PRODUCTION') 
                                                AND stocklog.`storageType`='QaLocation' 
                                                AND item.`goodsType` IN ('3','4') 
                                              ORDER BY stocklog.`stockLogId` DESC;";

                                                $countQry = mysqli_query($dbCon, $countShow);
                                                $rowCount = mysqli_fetch_array($countQry);
                                                $count = $rowCount[0];

                                                // console($qry_list);
                                                ?>
                                                <table class="table recived-item-table defaultDataTable table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>sl</th>
                                                            <th>Item</th>
                                                            <th>Batch</th>
                                                            <th>Received Qty</th>
                                                            <th>Passed Qty</th>
                                                            <th>Rejected Qty</th>
                                                            <th>Remaining Qty</th>
                                                            <th>Date</th>
                                                            <th>PO Number</th>
                                                            <th>INV Number</th>
                                                            <th>Vendor Name</th>
                                                            <th>Vendor code</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $sl = 0;
                                                        foreach ($qry_list["data"] as $key => $one) {
                                                            $sl++;
                                                        ?>
                                                            <tr>
                                                            <?php
                                                                $stock_id = $one["stockLogId"];
                                                                $get_last_updated_qty = queryGet("SELECT * FROM `erp_qa_summary` WHERE `companyId` = '$company_id' AND `branchId`='$branch_id' AND `locationId`='$location_id' AND `stock_log_id`='$stock_id'", false);
                                                                $received_qty = $one["itemQty"];


                                                                if ($get_last_updated_qty["numRows"] == 0) {
                                                                    $remaining_qty = $one["itemQty"] ?? 0;
                                                                    $status = 0;
                                                                } else {
                                                                    $remaining_qty = $one["itemQty"] - (($get_last_updated_qty["data"]["passed"] ?? 0) + ($get_last_updated_qty["data"]["rejected"] ?? 0));
                                                                    $status = $get_last_updated_qty["data"]["status"];
                                                                }

                                                                ?>
                                                                <td><?= $sl; ?></td>
                                                                <td><?= $one["itemName"] ?></td>
                                                                <td><?= $one["logRef"] ?></td>
                                                                <td><?= decimalQuantityPreview($one["itemQty"]) . " " . $one["uomName"] ?></td>
                                                                <td><?= decimalQuantityPreview($get_last_updated_qty["data"]["passed"]) . " " . $one["uomName"] ?></td>
                                                                <td><?= decimalQuantityPreview($get_last_updated_qty["data"]["rejected"]) . " " . $one["uomName"] ?></td>
                                                                <td><?= decimalQuantityPreview($remaining_qty) . " " . $one["uomName"] ?></td>
                                                                <td><?= formatDateORDateTime($one["bornDate"]) ?></td>
                                                                <td><?= $one["grnPoNumber"] ?></td>
                                                                <td><?= $one["vendorDocumentNo"] ?></td>
                                                                <td><?= $one["vendorName"] ?></td>
                                                                <td><?= $one["vendorCode"] ?></td>
                                                                <td>
                                                                    <a style="cursor:pointer" data-toggle="modal" data-target="#recivedItemView_<?= $sl ?>" class="btn btn-sm waves-effect waves-light"><i class="fa fa-eye po-list-icon"></i></a>
                                                                    <!-- right view -->

                                                                    <div class="modal fade right recivedItemView customer-modal classic-view-modal" id="recivedItemView_<?= $sl ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                                        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                                            <!--Content-->
                                                                            <div class="modal-content">
                                                                                <!--Header-->
                                                                                
                                                                                <div class="modal-header">
                                                                                    <div class="qa-view-header d-flex justidy-content-between">
                                                                                        <div class="qa-vendor-detail">
                                                                                            <p class="text-sm my-3">Vendor Name : <?= $one["vendorName"] ?></p>
                                                                                            <p class="text-xs my-3">Vendor Code : <?= $one["vendorCode"] ?></p>
                                                                                            <p class="text-xs my-3">Invoice Number : <?= $one["vendorDocumentNo"] ?></p>
                                                                                        </div>
                                                                                        <div class="qa-item-recieve-block">
                                                                                            <p class="text-sm my-2 font-bold">Total Received : <?= $one["itemQty"] . " " . $one["uomName"] ?></p>
                                                                                            <div class="qa-item-recieve-block-sub-item">
                                                                                                <p class="text-sm my-2 font-bold">Checked :</p>
                                                                                                <div class="qa-checked-item">
                                                                                                    <p class="text-xs my-2" id="htmlpassed_<?= $sl ?>">Passed : <?= $get_last_updated_qty["data"]["passed"] . " " . $one["uomName"] ?></p>
                                                                                                    <p class="text-xs my-2" id="htmlreject_<?= $sl ?>">Failed : <?= $get_last_updated_qty["data"]["rejected"] . " " . $one["uomName"] ?></p>
                                                                                                </div>
                                                                                                <p class="text-xs my-2 font-bold" id="htmlremain_<?= $sl ?>">Checked Required : <?= $remaining_qty . " " . $one["uomName"] ?></p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>




                                                                                    <div class="display-flex-space-between justify-content-between mb-3">

                                                                                        <input type="hidden" id="stock_id_<?= $sl ?>" name="" value="<?= $one["stockLogId"] ?>">
                                                                                        <input type="hidden" id="remain_qty_<?= $sl ?>" name="" value="<?= $remaining_qty ?>">
                                                                                        <input type="hidden" id="received_qty_<?= $sl ?>" name="" value="<?= $received_qty ?>">
                                                                                        <input type="hidden" id="total_passed_qty_<?= $sl ?>" name="" value="<?= $get_last_updated_qty["data"]["passed"] ?>">
                                                                                        <input type="hidden" id="total_reject_qty_<?= $sl ?>" name="" value="<?= $get_last_updated_qty["data"]["rejected"] ?>">

                                                                                        <ul class="nav nav-tabs history-tabs" id="myTab" role="tablist">

                                                                                            <li class="nav-item waves-effect waves-light">
                                                                                                <a class="nav-link active border-0 pb-3 d-flex" id="qualitycheck-tab" data-toggle="tab" href="#nav-qualitycheck_<?= $sl ?>" role="tab" aria-controls="qualitycheck" aria-selected="true"><ion-icon name="document-text-outline" class="mr-2 md hydrated" role="img" aria-label="document text outline"></ion-icon>Quality Check</a>
                                                                                            </li>

                                                                                            <li class="nav-item waves-effect waves-light">
                                                                                                <a class="nav-link border-0 pb-3 d-flex" id="relativehistory-tab" data-toggle="tab" href="#nav-relativehistory_<?= $sl ?>" role="tab" aria-controls="reconciliation2656" aria-selected="true"><ion-icon name="document-text-outline" class="mr-2 md hydrated" role="img" aria-label="document text outline"></ion-icon>Relative History</a>
                                                                                            </li>

                                                                                            <!-- -------------------Audit History Button Start------------------------- -->
                                                                                            <li class="nav-item waves-effect waves-light">
                                                                                                <a class="nav-link border-0 pb-3 d-flex" id="history-tab" data-toggle="tab" data-ccode="52300045" data-ids="2656" href="#nav-trailHistory_<?= $sl ?>" role="tab" aria-controls="history" aria-selected="false"><ion-icon name="time-outline" class="mr-2 md hydrated" role="img" aria-label="time outline"></ion-icon>Trail</a>
                                                                                            </li>

                                                                                            <!-- -------------------Audit History Button End------------------------- -->
                                                                                        </ul>

                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <div class="tab-content" id="pills-tabContent">
                                                                                        <div class="tab-pane fade show active" id="nav-qualitycheck_<?= $sl ?>" role="tabpanel" aria-labelledby="pill-qualitycheck">
                                                                                            <div class="row">
                                                                                                <div class="col-lg-12 col-md-12 col-sm-12 col-12">

                                                                                                    <div class="accordion item-classification accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                                        <div class="accordion-item">

                                                                                                            <h2 class="accordion-header" id="flush-headingOne">
                                                                                                                <button class="accordion-button btn btn-primary qa-modal-body-acc-btn waves-effect waves-light" type="button" data-bs-toggle="collapse" data-bs-target="#itemClassification" aria-expanded="true" aria-controls="flush-collapseOne">
                                                                                                                    Select Item Status :
                                                                                                                </button>
                                                                                                            </h2>
                                                                                                            <div class="item-status d-flex gap-4">

                                                                                                                <label class="status-common status-reserve d-flex gap-2 radio-button-label">
                                                                                                                    <input type="radio" <?php if ($status == '0') echo "checked" ?> id="input1" name="status_radio_<?= $sl ?>" value="0">
                                                                                                                    <span class="text-xs">Todo</span>
                                                                                                                </label>
                                                                                                                <label class="status-common status-cip d-flex gap-2 radio-button-label">
                                                                                                                    <input type="radio" id="input3" <?php if ($status == '1') echo "checked" ?> name="status_radio_<?= $sl ?>" value="1">
                                                                                                                    <span class="text-xs">Check In Progress</span>
                                                                                                                </label>
                                                                                                                <label class="status-common status-release d-flex gap-2 radio-button-label">
                                                                                                                    <input type="radio" id="input2" <?php if ($status == '2') echo "checked" ?> name="status_radio_<?= $sl ?>" value="2">
                                                                                                                    <span class="text-xs">Done</span>
                                                                                                                </label>

                                                                                                                <label class="status-common status-release d-flex gap-2 radio-button-label">
                                                                                                                    <input type="number" id="input_passed_<?= $sl ?>" name="passed" class="form-control">
                                                                                                                    <span class="text-xs">Passed</span>
                                                                                                                </label>

                                                                                                                <label class="status-common status-release d-flex gap-2 radio-button-label">
                                                                                                                    <input type="number" id="input_reject_<?= $sl ?>" name="rejected" class="form-control">
                                                                                                                    <span class="text-xs">Rejected</span>
                                                                                                                </label>


                                                                                                            </div>

                                                                                                        </div>
                                                                                                    </div>


                                                                                                    <div class="accordion item-classification accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                                        <div class="accordion-item">
                                                                                                            <h2 class="accordion-header" id="flush-headingOne">
                                                                                                                <button class="accordion-button btn btn-primary qa-modal-body-acc-btn waves-effect waves-light" type="button" data-bs-toggle="collapse" data-bs-target="#itemClassification" aria-expanded="true" aria-controls="flush-collapseOne">
                                                                                                                    Specifications
                                                                                                                </button>
                                                                                                            </h2>
                                                                                                            <div id="itemClassification" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#itemClassification">
                                                                                                                <div class="accordion-body p-0">
                                                                                                                    <div class="card bg-transparent border border-rounded-3 mb-2">
                                                                                                                        <div class="card-body p-3">
                                                                                                                            <div class="display-flex-space-between gap-4 border-bottom pb-2 qa-specification">
                                                                                                                                <p>Item Code </p>
                                                                                                                                <p>: <?= $one["itemCode"] ?></p>
                                                                                                                            </div>
                                                                                                                            <div class="display-flex-space-between gap-4 border-bottom pb-2 qa-specification">
                                                                                                                                <p>Item Name </p>
                                                                                                                                <p>: <?= $one["itemName"] ?></p>
                                                                                                                            </div>
                                                                                                                            <div class="display-flex-space-between gap-4 border-bottom pb-2 qa-specification">
                                                                                                                                <p>Type </p>
                                                                                                                                <p>: Material</p>
                                                                                                                            </div>
                                                                                                                            <div class="display-flex-space-between gap-4 border-bottom pb-2 qa-specification">
                                                                                                                                <p>Avalability Check </p>
                                                                                                                                <p>: <?= $one["availabilityCheck"] ?></p>
                                                                                                                            </div>
                                                                                                                            <div class="display-flex-space-between gap-4 border-bottom pb-2 qa-specification">
                                                                                                                                <p>Status </p>
                                                                                                                                <p>: <?= $one["status"] ?></p>
                                                                                                                            </div>

                                                                                                                            <div class="dotted-border-area detailRecievedItem">
                                                                                                                                <label for="">Specifications</label>
                                                                                                                                <div class="display-flex-space-between gap-4 border-bottom pb-2 qa-specification">
                                                                                                                                <p>Item Description : </p><p><?= $one["itemDesc"] ?></p>
                                                                                                                                </div>
                                                                                                                                <div class="display-flex-space-between gap-4 border-bottom pb-2 qa-specification">
                                                                                                                                <p>Net Weight : </p><p><?= $one["netWeight"] ?></p>
                                                                                                                                </div>
                                                                                                                                <div class="display-flex-space-between gap-4 border-bottom pb-2 qa-specification">
                                                                                                                                <p>Gross Weight : </p><p><?= $one["grossWeight"] ?></p>
                                                                                                                                </div>
                                                                                                                                <div class="display-flex-space-between gap-4 border-bottom pb-2 qa-specification">
                                                                                                                                <p>Volume : </p><p><?= $one["volume"] ?></p>
                                                                                                                                </div>
                                                                                                                                <div class="display-flex-space-between gap-4 border-bottom pb-2 qa-specification">
                                                                                                                                <p>Volume Cube Cm : </p><p><?= $one["volumeCubeCm"] ?></p>
                                                                                                                                </div>
                                                                                                                                <div class="display-flex-space-between gap-4 border-bottom pb-2 qa-specification">
                                                                                                                                <p>Height : </p><p><?= $one["height"] ?></p>
                                                                                                                                </div>
                                                                                                                                <div class="display-flex-space-between gap-4 border-bottom pb-2 qa-specification">
                                                                                                                                <p>Width : </p><p><?= $one["width"] ?></p>
                                                                                                                                </div>
                                                                                                                                <div class="display-flex-space-between gap-4 border-bottom pb-2 qa-specification">
                                                                                                                                <p>Length : </p><p><?= $one["length"] ?></p>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <span class="remarks-text font-italic font-bold">* Lorem ipsum text dolor sit</span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="accordion item-classification accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                                        <div class="accordion-item">
                                                                                                            <h2 class="accordion-header" id="flush-headingOne">
                                                                                                                <button class="accordion-button btn btn-primary qa-modal-body-acc-btn waves-effect waves-light" type="button" data-bs-toggle="collapse" data-bs-target="#pdfUploadItem" aria-expanded="true" aria-controls="flush-collapseOne">
                                                                                                                    PDF Upload
                                                                                                                </button>
                                                                                                            </h2>
                                                                                                            <div id="pdfUploadItem" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#itemClassification">
                                                                                                                <div class="accordion-body p-0">
                                                                                                                    <div class="card bg-transparent">
                                                                                                                        <div class="card-body p-2">
                                                                                                                            <div class="upload-section">
                                                                                                                                <div class="row">
                                                                                                                                    <div class="col-md-12">
                                                                                                                                        <div class="container">
                                                                                                                                            <div class="row">
                                                                                                                                                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                                                                                                                                    <input id="pdf_file_<?= $sl ?>" type="file" accept=".pdf" class="pdf-upload-input">
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
                                                                                                    <div class="accordion item-classification accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                                        <div class="accordion-item">
                                                                                                            <h2 class="accordion-header" id="flush-headingOne">
                                                                                                                <button class="accordion-button btn btn-primary qa-modal-body-acc-btn waves-effect waves-light" type="button" data-bs-toggle="collapse" data-bs-target="#imgUrlUploadItem" aria-expanded="true" aria-controls="flush-collapseOne">
                                                                                                                    Multiple Image URL Upload
                                                                                                                </button>
                                                                                                            </h2>
                                                                                                            <div id="imgUrlUploadItem" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#itemClassification">
                                                                                                                <div class="accordion-body p-0">
                                                                                                                    <div class="card bg-transparent border border-rounded-3">
                                                                                                                        <div class="card-body py-3">
                                                                                                                            <div class="upload-section">
                                                                                                                                <div class="row">
                                                                                                                                    <div class="col-md-12">
                                                                                                                                        <div class="container">
                                                                                                                                            <div class="row">
                                                                                                                                                <div class="col-lg-4 col-md-4 col-sm-4 col-12 imgUp">
                                                                                                                                                    <!-- <div class="imagePreview"></div> -->
                                                                                                                                                    <input type="text" class="form-control my-2 all-link" placeholder="upload image link">
                                                                                                                                                    <!-- <label class="btn btn-primary">
                                                                                                                                                    Upload
                                                                                                                                                    <input type="file" class="uploadFile img" accept="image/*" value="Upload Photo" style="width: 0px;height: 0px;overflow: hidden;">
                                                                                                                                                </label> -->
                                                                                                                                                </div>
                                                                                                                                                <i class="fa fa-plus imgAdd"></i>
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

                                                                                            <button type="button" id="submitid_<?= $sl ?>" class="btn btn-primary submit_frm">SUBMIT</button>
                                                                                        </div>
                                                                                        <div class="tab-pane fade" id="nav-relativehistory_<?= $sl ?>" role="tabpanel" aria-labelledby="pill-relativehistory">



                                                                                            <table class="table table-hover">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>SL.</th>
                                                                                                        <th>Doc No.</th>
                                                                                                        <th>Passed</th>
                                                                                                        <th>Rejected</th>
                                                                                                        <th>Status</th>
                                                                                                        <th>Done By</th>
                                                                                                        <th>Done On</th>
                                                                                                        <th>Action</th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>

                                                                                                    <?php
                                                                                                    $sql_query_log = queryGet("SELECT * FROM `erp_qa_log` WHERE `companyId` = '$company_id' AND `branchId`='$branch_id' AND `locationId`='$location_id' AND `stock_log_id`='$stock_id'", true);
                                                                                                    $sl_no = 0;
                                                                                                    foreach ($sql_query_log["data"] as $key => $one_data) {
                                                                                                        $sl_no++;
                                                                                                        $get_qa_log_id = $one_data["qa_log_Id"];
                                                                                                    ?>
                                                                                                        <tr>
                                                                                                            <td><?= $sl_no ?></td>
                                                                                                            <td><?= $one_data["doc_no"] ?></td>
                                                                                                            <td><?= $one_data["passed"] ?></td>
                                                                                                            <td><?= $one_data["rejected"] ?></td>
                                                                                                            <td><?php
                                                                                                            if($one_data["status"] == "0")
                                                                                                             {
                                                                                                                echo "ToDo";
                                                                                                             }
                                                                                                             elseif($one_data["status"] == "1")
                                                                                                             {
                                                                                                                echo "InProgress";
                                                                                                             }
                                                                                                             else
                                                                                                             {
                                                                                                                echo "Done";
                                                                                                             }
                                                                                                             ?></td>
                                                                                                            <td><?= getCreatedByUser($one_data["qaCreatedBy"]) ?></td>
                                                                                                            <td><?= $one_data["qaCreatedAt"] ?></td>

                                                                                                            <td>
                                                                                                                <a type="button" class="btn btn-transparent" data-toggle="modal" data-target="#detailedHistory_<?= $sl ?>">
                                                                                                                    <i class="fa fa-eye po-list-icon"></i>
                                                                                                                </a>

                                                                                                                <?php

                                                                                                                $get_link = queryGet("SELECT * FROM `erp_qa_link` WHERE `companyId` = '$company_id' AND `branchId`='$branch_id' AND `locationId`='$location_id' AND `qa_log_Id`='$get_qa_log_id'", true);

                                                                                                                ?>

                                                                                                                <div class="modal fade customer-modal" id="detailedHistory_<?= $sl ?>" tabindex="-1" role="dialog" aria-labelledby="detailedHistoryLabel" aria-hidden="true">
                                                                                                                    <div class="modal-dialog w-25" role="document">
                                                                                                                        <div class="modal-content">
                                                                                                                            <div class="modal-body">
                                                                                                                                <div class="pdf-view">
                                                                                                                                    <span class="float-label">PDF View</span>
                                                                                                                                    <p><?= $one_data["qa_file"] ?></p>
                                                                                                                                </div>
                                                                                                                                <div class="img-view">
                                                                                                                                    <span class="float-label">Image View</span>
                                                                                                                                    <?php
                                                                                                                                    foreach($get_link["data"] as $link)
                                                                                                                                    {
                                                                                                                                    ?>
                                                                                                                                    <p><?= $link ?></p>
                                                                                                                                    <?php
                                                                                                                                    }
                                                                                                                                    ?>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <!-- <div class="modal-footer">
                                                                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                                                                <button type="button" class="btn btn-primary">Save changes</button>
                                                                                                                            </div> -->
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>



                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    <?php
                                                                                                    }
                                                                                                    ?>
                                                                                                </tbody>
                                                                                            </table>


                                                                                        </div>
                                                                                        <div class="tab-pane fade" id="nav-trailHistory_<?= $sl ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                                            <div class="audit-head-section mb-3 mt-3 ">
                                                                                                <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> Somdutta Sengupta <span class="font-bold text-normal"> on </span> 29-09-2023 16:37:01</p>
                                                                                                <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> Somdutta Sengupta <span class="font-bold text-normal"> on </span> 29-09-2023 16:37:01</p>
                                                                                            </div>
                                                                                            <hr>
                                                                                            <div class="audit-body-section mt-2 mb-3 auditTrailBodyContentCustomer52300045">
                                                                                                <ol class="timeline">

                                                                                                    <li class="timeline-item mb-0 bg-transparent auditTrailBodyContentLineCustomer" type="button" data-toggle="modal" data-id="2308" data-ccode="52300045" data-target="#innerModal">
                                                                                                        <span class="timeline-item-icon | filled-icon"><img src="https://devalpha.vitwo.ai/public/storage/audittrail/ADD.png" width="25" height="25"></span>
                                                                                                        <span class="step-count">1</span>
                                                                                                        <div class="new-comment font-bold">
                                                                                                            <p>Somdutta Sengupta </p>
                                                                                                            <ul class="ml-3 pl-0">
                                                                                                                <li style="list-style: disc; color: #a7a7a7;">29-09-2023 16:37:02</li>
                                                                                                            </ul>
                                                                                                            <p></p>
                                                                                                        </div>
                                                                                                    </li>
                                                                                                    <p class="mt-0 mb-5 ml-5">New Customer added</p>


                                                                                                </ol>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- right view -->
                                                                </td>
                                                            </tr>
                                                        <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="<?= $settingsCheckboxCount + 7; ?>">
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

                                                    <!-- <a href="#" style="text-decoration:none" onmouseout="javascript:window.status='Done';" onmousemove="javascript:window.status='Go to this Page';" class="number current">&nbsp;&nbsp;1&nbsp;</a>&nbsp;&nbsp;<a href="javascript:disPage(2)" onmouseout="javascript:window.status='Done';" onmousemove="javascript:window.status='Go to this Page';" class="number">2</a>&nbsp;&nbsp;<a href="javascript:disPage(3)" onmouseout="javascript:window.status='Done';" onmousemove="javascript:window.status='Go to this Page';" class="number">3</a>&nbsp;&nbsp;<a href="javascript:disPage(4)" onmouseout="javascript:window.status='Done';" onmousemove="javascript:window.status='Go to this Page';" class="number">4</a> <a href="javascript:nextPage(1)" onmouseout="javascript:window.status='Done';" onmousemove="javascript:window.status='Go to Next Page';" style="text-decoration:none">Next</a>
                                                        <a href="javascript:disPage(4)" title="Last Page">Last</a> -->
                                            </div>
                                            <!-- End .pagination -->
                                            <!-- End .pagination -->
                                            </td>
                                            </tr>
                                            </tbody>
                                            </table>
                                        </div>
                                        <!---------------------------------Table settings Model Start--------------------------------->
                                        <div class="modal" id="myModal2">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Table Column Settings</h4>
                                                        <button type="button" class="close" data-dismiss="modal">×</button>
                                                    </div>
                                                    <form name="table-settings" method="post" action="" onsubmit="return table_settings();">
                                                        <input type="hidden" name="tablename" value="tbl_branch_admin_tablesettings">
                                                        <input type="hidden" name="pageTableName" value="ERP_BRANCH_SALES_ORDER_DELIVERY_PGI">
                                                        <div class="modal-body">
                                                            <div id="dropdownframe"></div>
                                                            <div id="main2">
                                                                <table>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" checked="checked" name="settingsCheckbox[]" id="settingsCheckbox1" value="1">
                                                                                PGI No.</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" checked="checked" name="settingsCheckbox[]" id="settingsCheckbox2" value="2">
                                                                                Customer PO</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" checked="checked" name="settingsCheckbox[]" id="settingsCheckbox3" value="3">
                                                                                Delivery Date</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" checked="checked" name="settingsCheckbox[]" id="settingsCheckbox3" value="4">
                                                                                Customer Name</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" checked="checked" name="settingsCheckbox[]" id="settingsCheckbox3" value="5">
                                                                                Status</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" name="settingsCheckbox[]" id="settingsCheckbox3" value="6">
                                                                                Total Items</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="submit" name="add-table-settings" class="btn btn-success waves-effect waves-light">Save</button>
                                                            <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!---------------------------------Table Model End--------------------------------->

                                    </div>
                                </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

    </div>

</div>

<script>
    $(document).ready(function() {
        $(document).on("click", ".submit_frm", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];

            var status = $('input[name="status_radio_' + rowNo + '"]:checked').val();
            var passed = $(`#input_passed_${rowNo}`).val();
            var reject = $(`#input_reject_${rowNo}`).val();
            var stock_id = $(`#stock_id_${rowNo}`).val();
            let passed_value = (parseFloat(passed) > 0) ? parseFloat(passed) : 0;
            let reject_value = (parseFloat(reject) > 0) ? parseFloat(reject) : 0;
            var remain_qty = (parseFloat($(`#remain_qty_${rowNo}`).val()) > 0) ? parseFloat($(`#remain_qty_${rowNo}`).val()) : 0;
            var received_qty = $(`#received_qty_${rowNo}`).val();
            var previous_passed = (parseFloat($(`#total_passed_qty_${rowNo}`).val()) > 0) ? parseFloat($(`#total_passed_qty_${rowNo}`).val()) : 0;
            var previous_rejected = (parseFloat($(`#total_reject_qty_${rowNo}`).val()) > 0) ? parseFloat($(`#total_reject_qty_${rowNo}`).val()) : 0;

            // var all_link = $('.all-link').val();

            const all_link = [];

            $(".all-link").each(function(index) {
                var value = $(this).val();
                if (value.trim() !== '') {
                    all_link.push(value);
                }
            });
            console.log(all_link);

            if ((passed_value + reject_value) > remain_qty) {
                let Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                Toast.fire({
                    icon: `error`,
                    title: `&nbsp;Please Give Passed and Rejected Quantity less than Remaining Quantity!`
                });
                console.log("error: " + e.message);
            } else {
                // if ((passed_value + reject_value) == remain_qty) {
                //     status = 2;
                // } else if ((passed_value + reject_value) < remain_qty) {
                //     status = 1;
                // }

                var formData = new FormData();

                var selectedFile = $(`#pdf_file_${rowNo}`)[0].files[0];

                if (selectedFile) {
                    formData.append('file', selectedFile);
                }

                // Append the file to the FormData object

                formData.append('status', status);
                formData.append('passed_value', passed_value);
                formData.append('reject_value', reject_value);
                formData.append('stock_id', stock_id);
                formData.append('all_link', all_link);
                formData.append('received_qty', received_qty);

                $.ajax({
                    url: "ajaxs/qa/ajax-post-qa.php",
                    type: "POST",
                    data: formData,
                    processData: false, // Prevent jQuery from automatically processing the data
                    contentType: false, // Prevent jQuery from setting the content type
                    beforeSend: function() {
                        console.log("Mapping...");
                    },
                    success: function(response) {
                        let responseObj = JSON.parse(response);
                        if (responseObj["status"] == "success") {
                            let mapData = responseObj["data"];
                            let Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            Toast.fire({
                                icon: `${responseObj["status"]}`,
                                title: `&nbsp;${responseObj["message"]}`
                            });

                            //HTML Modification
                            var current_total_remaining = remain_qty - (passed_value + reject_value);
                            var current_total_passed = previous_passed + passed_value;
                            var current_total_reject = previous_rejected + reject_value;
                            $(`#total_passed_qty_${rowNo}`).val(current_total_passed);
                            $(`#total_reject_qty_${rowNo}`).val(current_total_reject);
                            $(`#remain_qty_${rowNo}`).val(current_total_remaining);
                            $(`#htmlpassed_${rowNo}`).html("Total Passed Quantity : " + current_total_passed);
                            $(`#htmlreject_${rowNo}`).html("Total Failed Quantity : " + current_total_reject);
                            $(`#htmlremain_${rowNo}`).html("Remaining Quantity : " + current_total_remaining);

                        }

                    },
                    error: function(e) {
                        let Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        Toast.fire({
                            icon: `error`,
                            title: `&nbsp;Mapping failed, please try again!`
                        });
                        console.log("error: " + e.message);
                    }
                });



            }



        });

    });
</script>