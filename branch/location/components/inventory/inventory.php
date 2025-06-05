<style>
    .dataTables_length {
        display: none;
    }

    /* .inventory-modal .modal-dialog {
        max-width: 100%;
        width: 50%;
    } */

    .inventory-modal .modal-header {
        height: auto;
    }

    .inventory-modal .modal-body {
        /* width: 100%; */
        top: -24px;
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= LOCATION_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Inventory</a></li>
                <!-- <li class="breadcrumb-item active">
                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?post-grn" class="text-dark"><i class="fa fa-plus po-list-icon"></i>Add New</a>
                </li> -->
                <li class="back-button">
                    <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                        <i class="fa fa-reply po-list-icon"></i>
                    </a>
                </li>
            </ol>
        </div>
        <div class="container-fluid">
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div class="card card-tabs" style="border-radius: 20px;">
                        <?php
                        $today = date("Y-m-d");
                        ?>

                        <form>
                            <div class="d-flex w-50 justify-content-end gap-2 ml-auto p-3">
                                Stock As On :
                                <input class="fld form-control w-25" type="date" name="date" id="form_date_s" value="<?php if (isset($_REQUEST['date'])) {
                                                                                                                            echo $_REQUEST['date'];
                                                                                                                        } else {
                                                                                                                            echo $today;
                                                                                                                        } ?>" />
                                <button type="submit" class="btn btn-primary w-auto d-flex gap-2"><i class="fa fa-search" aria-hidden="true"></i>Search</button>
                            </div>

                        </form>

                        <div class="col-lg-2 col-md-2 col-sm-12">

                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                        </div>
                        <!-- <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">

                            <div class="card-body">

                                <div class="row filter-serach-row">

                                    <div class="col-lg-2 col-md-2 col-sm-12">

                                        <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                                    </div>

                                    <div class="col-lg-10 col-md-10 col-sm-12">

                                        <div class="row table-header-item">

                                            <div class="col-lg-11 col-md-11 col-sm-11">

                                                <div class="section serach-input-section">

                                                    <input type="text" id="myInput" name="keyword" placeholder="" class="field form-control" value="<?php echo $keywd; ?>" />

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

                                            <div class="col-lg-1 col-md-1 col-sm-1">

                                                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>

                                            </div>

                                        </div>



                                    </div>

                                </div>

                            </div>
                            <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Filter </h5>

                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <input type="text" name="keyword2" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<? php ?>">
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
                                           <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync "></i>Reset</a>-
                                            <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                                                Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form> -->


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

                        <div>







                            <?php


                            $cond = '';


                            // if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                            //     $cond .= " AND (invItems.`itemCode` like '%" . $_REQUEST['keyword2'] . "%' OR invItems.`itemName` like '%" . $_REQUEST['keyword2'] . "%' OR invItems.`goodsType` like '%" . $_REQUEST['keyword2'] . "%')";
                            // } else {
                            //     if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                            //         $cond .= " AND (invItems.`itemCode` like '%" . $_REQUEST['keyword'] . "%' OR invItems.`itemName` like '%" . $_REQUEST['keyword'] . "%' OR invItems.`goodsType` like '%" . $_REQUEST['keyword'] . "%')";
                            //     }
                            // }
                            $inventorySummaryObj = queryGet('SELECT invSummary.*, goodTypes.`goodTypeName` AS goodType,  invItems.`itemCode`, invItems.`itemName`, invItems.`itemDesc`, invItems.`baseUnitMeasure`, invItems.`goodsType`, goodUoms.`uomName`, goodUoms.`uomDesc` FROM `erp_inventory_stocks_summary` AS invSummary, `erp_inventory_items` AS invItems, `erp_inventory_mstr_good_types` AS goodTypes, `erp_inventory_mstr_uom` AS goodUoms WHERE invSummary.itemId = invItems.itemId AND invSummary.`company_id` =' . $company_id . ' AND invSummary.`branch_id` =' . $branch_id . ' AND invSummary.`location_id` =' . $location_id . ' AND invItems.`goodsType` = goodTypes.`goodTypeId` AND invItems.`baseUnitMeasure`=goodUoms.`uomId` ' . $cond . ' ORDER BY invSummary.`updatedAt` DESC ', true);

                        //  console($inventorySummaryObj);
                            // limit ' . $GLOBALS['start'] . "," . $GLOBALS['show'] . ' 


                            $countShow = "SELECT count(*) FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . "   AND  `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id ";

                            $countQry = mysqli_query($dbCon, $countShow);

                            $rowCount = mysqli_fetch_array($countQry);

                            $count = $rowCount[0];

                            $cnt = $GLOBALS['start'] + 1;


                            $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_INVENTORY_ITEMS", $_SESSION["logedBranchAdminInfo"]["adminId"]);

                            $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);

                            $settingsCheckbox = unserialize($settingsCh);

                            $condstock = '';
                            if (isset($_REQUEST['date'])) {
                                $date = $_REQUEST['date'];

                                $condstock .= " AND DATE_FORMAT(postingDate, '%Y %m %d') <= DATE_FORMAT('" . $date . "', '%Y %m %d')";
                            } else {
                                $date = $today;

                                $condstock .= " AND DATE_FORMAT(postingDate, '%Y %m %d') <= DATE_FORMAT('" . $today . "', '%Y %m %d')";
                            }




                            ?>
                        </div>





                        <table class="table defaultDataTable table-hover invertory-table" id="invertoryDataTable">
                            <thead>
                                <tr class="alert-light">
                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                        <th>Item Code</th>
                                    <?php
                                    }
                                    if (in_array(2, $settingsCheckbox)) { ?>
                                        <th>Item Name</th>
                                    <?php
                                    }
                                    if (in_array(3, $settingsCheckbox)) { ?>

                                        <th> Type</th>
                                    <?php
                                    }
                                    if (in_array(4, $settingsCheckbox)) { ?>

                                        <!-- <th>Movement Type</th> -->
                                        <th> Total Qty </th>
                                    <?php
                                    }
                                    if (in_array(5, $settingsCheckbox)) { ?>

                                        <th>UOM </th>
                                    <?php
                                    }
                                    if (in_array(6, $settingsCheckbox)) { ?>

                                        <th>Valuation Class</th>
                                    <?php
                                    }
                                    if (in_array(7, $settingsCheckbox)) { ?>

                                        <th>Price(MW)</th>
                                    <?php
                                    }
                                    if (in_array(8, $settingsCheckbox)) { ?>

                                        <!-- <th>Resarve Qty</th> -->
                                        <th> Total Value</th>
                                    <?php
                                    }
                                    if (in_array(9, $settingsCheckbox)) { ?>

                                        <th>Last Received On </th>
                                    <?php

                                    }

                                    ?>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                foreach ($inventorySummaryObj["data"] as $oneInvItem) {
                                    $itemId = $oneInvItem['itemId'];
                                    // $total_qty = $oneInvItem['rmWhOpen']+$oneInvItem['rmWhReserve']+$oneInvItem['rmProdOpen']+$oneInvItem['rmProdReserve']+$oneInvItem['sfgStockOpen']+$oneInvItem['sfgStockReserve']+$oneInvItem['fgWhOpen']+$oneInvItem['fgWhReserve']+$oneInvItem['fgMktOpen']+$oneInvItem['fgMktReserve'];
                                    // console($oneInvItem);
                                    $stock_log_sql = queryGet("SELECT
                                    MAX(bornDate) AS bornDate,
                                    SUM(itemQty) AS qty,
                                    SUM(CASE WHEN `storageType` = 'rmWhOpen' THEN itemQty ELSE 0 END) AS rmWhOpen_qty,
                                    SUM(CASE WHEN `storageType` = 'rmWhReserve' THEN itemQty ELSE 0 END) AS rmWhReserve_qty,
                                    SUM(CASE WHEN `storageType` = 'rmProdOpen' THEN itemQty ELSE 0 END) AS rmProdOpen_qty,
                                    SUM(CASE WHEN `storageType` = 'rmProdReserve' THEN itemQty ELSE 0 END) AS rmProdReserve_qty,
                                    SUM(CASE WHEN `storageType` = 'sfgStockOpen' THEN itemQty ELSE 0 END) AS sfgStockOpen_qty,
                                    SUM(CASE WHEN `storageType` = 'sfgStockReserve' THEN itemQty ELSE 0 END) AS sfgStockReserve_qty,
                                    SUM(CASE WHEN `storageType` = 'fgWhOpen' THEN itemQty ELSE 0 END) AS fgWhOpen_qty,
                                    SUM(CASE WHEN `storageType` = 'fgWhReserve' THEN itemQty ELSE 0 END) AS fgWhReserve_qty,
                                    SUM(CASE WHEN `storageType` = 'fgMktOpen' THEN itemQty ELSE 0 END) AS fgMktOpen_qty,
                                    SUM(CASE WHEN `storageType` = 'fgMktReserve' THEN itemQty ELSE 0 END) AS fgMktReserve_qty,
                                    SUM(CASE WHEN storageType = 'QaLocation' THEN itemQty ELSE 0 END) AS QaLocation_qty
                                FROM
                                    `erp_inventory_stocks_log`
                                WHERE
                                    `itemId` = $itemId AND `locationId` = $location_id AND `branchId` = $branch_id AND `companyId` = $company_id
                                    " . $condstock . ";
                                ");
                                $total_qty_sql = queryGet("SELECT * FROM `erp_inventory_stocks_log_report` WHERE  `item_Id` = $itemId AND `location_id` = $location_id AND `branch_id` = $branch_id AND `company_id` = $company_id AND DATE_FORMAT(report_date, '%Y %m %d') <= DATE_FORMAT('" . $date . "', '%Y %m %d') ORDER BY `report_id` DESC");

                                    // console($stock_log_sql);
                                    $total_qty = $total_qty_sql['data']['total_closing_qty'] ?? 0;
                                    if ($total_qty == 0) {
                                        $born_date = '-';
                                    } else {
                                        $born_date = formatDateORDateTime($stock_log_sql['data']['bornDate']) ?? '-';
                                    }

                                ?>
                                    <tr>
                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                            <td><?= $oneInvItem["itemCode"] ?></td>
                                        <?php
                                        }
                                        if (in_array(2, $settingsCheckbox)) { ?>

                                            <td class="pre-normal"><?= ucfirst($oneInvItem["itemName"]) ?></td>
                                        <?php
                                        }
                                        if (in_array(3, $settingsCheckbox)) { ?>

                                            <td><?= $oneInvItem["goodType"] ?></td>
                                        <?php
                                        }
                                        if (in_array(4, $settingsCheckbox)) { ?>

                                            <!-- <td></td> -->
                                            <td><?= $total_qty ?></td>
                                        <?php
                                        }
                                        if (in_array(5, $settingsCheckbox)) { ?>

                                            <td> <?= $oneInvItem["uomName"] ?> </td>
                                        <?php
                                        }
                                        if (in_array(6, $settingsCheckbox)) { ?>

                                            <td><?= $oneInvItem['priceType'] ?></td>
                                        <?php
                                        }
                                        if (in_array(7, $settingsCheckbox)) { ?>

                                            <td><?= number_format($oneInvItem["movingWeightedPrice"], 2); ?> </td>
                                        <?php
                                        }
                                        if (in_array(8, $settingsCheckbox)) { ?>


                                            <td> <?php echo number_format($oneInvItem["movingWeightedPrice"]*$total_qty, 2); ?> </td>
                                        <?php
                                        }
                                        if (in_array(9, $settingsCheckbox)) { ?>

                                            <td> <?= ($born_date); ?></td>
                                        <?php
                                        }

                                        ?>

                                        <td>
                                            <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $oneInvItem["itemCode"] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>


                                            <div class="modal fade right inventory-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $oneInvItem["itemCode"] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                    <!--Content-->
                                                    <div class="modal-content">
                                                        <!--Header-->
                                                        <div class="modal-header">
                                                            <?= $oneInvItem["itemCode"] ?><br>
                                                            <?= $oneInvItem["itemName"] ?>
                                                            <p>Item Price : <?= $oneInvItem["itemPrice"] ?> </p>
                                                            <p>Item MWP : <?= $oneInvItem["movingWeightedPrice"] ?> </p>
                                                            <p>Item Valuation : <?= $oneInvItem["priceType"] ?></p>
                                                            <p>Item Total Quantity : <?= $oneInvItem["itemTotalQty"] ?></p>

                                                            <ul class="nav nav-pills nav-tabs mb-3" id="pills-tab" role="tablist">
                                                                <!-- <li class="nav-item" role="presentation">
                                                                    <a class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" href="#pills_home_<?= $oneInvItem["stockSummaryId"] ?>" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Home</a>
                                                                </li> -->
                                                                <li class="nav-item">
                                                                    <a class="nav-link" id="home-tab" data-toggle="tab" href="#home_<?= $oneInvItem["stockSummaryId"] ?>" role="tab" aria-controls="home" aria-selected="true">Stock Details</a>
                                                                </li>

                                                                <!-- 
                                                                    <li class="nav-item" role="presentation">
                                                                    <a class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" href="#pills_profile_<?= $oneInvItem["stockSummaryId"]  ?>" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Profile</a>
                                                                </li> -->
                                                                <li class="nav-item">
                                                                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile_<?= $oneInvItem["stockSummaryId"] ?>" role="tab" aria-controls="profile" aria-selected="false">Stock log</a>
                                                                </li>
                                                                <!-- <li class="nav-item" role="presentation">
                                                                    <a class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" href="#pills_contact_<?= $oneInvItem["stockSummaryId"]  ?>" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Contact</a>
                                                                </li> -->
                                                                <li class="nav-item">
                                                                    <a class="nav-link" id="contact-tab" href="<?= BASE_URL ?>branch/location/manage-stock-transfer.php" target="_blank">Transfer</a>
                                                                </li>
                                                            </ul>

                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="tab-content">


                                                                <div class="tab-pane fade show active" id="home_<?= $oneInvItem["stockSummaryId"] ?>" role="tabpanel" aria-labelledby="pills-contact-tab">

                                                                    <?php
                                                                    // console($stock_log_sql);




                                                                    ?>


                                                                    <div class="row">
                                                                        <div class="col-4">
                                                                            Type
                                                                        </div>
                                                                        <div class="col-4">
                                                                            Open
                                                                        </div>
                                                                        <div class="col-4">
                                                                            Reserve
                                                                        </div>
                                                                        <div class="col-4">
                                                                            RM warehouse
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $stock_log_sql['data']['rmWhOpen_qty'] ?? 0  ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $stock_log_sql['data']['rmWhReserve_qty'] ?? 0 ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            RM production
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $stock_log_sql['data']['rmProdOpen_qty'] ?? 0 ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $stock_log_sql['data']['rmProdReserve_qty'] ?? 0 ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            SFG Stock
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $stock_log_sql['data']["sfgStockOpen_qty"] ?? 0 ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $stock_log_sql['data']["sfgStockReserve_qty"] ?? 0 ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            FG warehouse
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $stock_log_sql['data']["fgWhOpen_qty"] ?? 0 ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $stock_log_sql['data']["fgWhReserve_qty"] ?? 0 ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            FG Marketing
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $stock_log_sql['data']["fgMktOpen_qty"] ?? 0 ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $stock_log_sql['data']["fgMktReserve_qty"] ?? 0 ?>
                                                                        </div>

                                                                        <div class="col-4">
                                                                           QA Location
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $stock_log_sql['data']["QaLocation_qty"] ?? 0 ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?=  0 ?>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="profile_<?= $oneInvItem["stockSummaryId"] ?>" role="tabpanel" aria-labelledby="pills-contact-tab">
                                                                    <table>
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Storage Type</th>
                                                                                <th>Item Quantity</th>
                                                                                <th>Item UOM</th>
                                                                                <th>Item Price</th>
                                                                                <th>Reference</th>
                                                                                <th>Minimum Stock</th>
                                                                                <th>Maximum Stock</th>
                                                                                <th>Created By</th>
                                                                                <th>Created At</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <?php
                                                                        $sql = queryGet("SELECT * FROM `erp_inventory_stocks_log` WHERE `itemId` ='" . $oneInvItem["itemId"] . "'  $condstock  ", true);
                                                                      //  console($sql);
                                                                        $sql_data =  $sql['data'];
                                                                        ?>
                                                                        <tbody>
                                                                            <?php
                                                                            foreach ($sql_data as $data) {

                                                                            ?>
                                                                                <tr>
                                                                                    <td><?= $data['storageType'] ?></td>
                                                                                    <td><?= $data['itemQty']  ?></td>
                                                                                    <td><?= $data['itemUom']  ?></td>
                                                                                    <td><?= $data['itemPrice']  ?></td>
                                                                                    <td><?= $data['logRef']  ?></td>
                                                                                    <td><?= $data['min_stock']  ?></td>
                                                                                    <td><?= $data['max_stock']  ?></td>
                                                                                    <td><?= getCreatedByUser($data['createdBy']) ?></td>
                                                                                    <td><?= formatDateORDateTime($data['createdAt']) ?></td>
                                                                                </tr>

                                                                            <?php
                                                                            }
                                                                            ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="tab-pane fade" id="contact_<?= $oneInvItem["stockSummaryId"]  ?>" role="tabpanel" aria-labelledby="pills-contact-tab">
                                                                    <div class="card">
                                                                        <div class="card-body pt-3 pl-4 pr-4 pb-4">

                                                                            <form action="" method="POST" id="transfer" name="transfer">

                                                                                <input type="hidden" name="createData" id="createData" value="">
                                                                                <div class="row po-form-creation">

                                                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                        <div class="card so-creation-card po-creation-card">
                                                                                            <div class="card-header">
                                                                                                <div class="row others-info-head">
                                                                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                                        <div class="head">
                                                                                                            <i class="fa fa-info"></i>
                                                                                                            <h4>Movement</h4>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="card-body others-info vendor-info so-card-body">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                                        <div class="row info-form-view">
                                                                                                            <div class="col-lg-6 col-md-6 col-sm-12 form-inline">
                                                                                                                <label for="">Movement Types</label>
                                                                                                                <select name="movemenrtypesDropdown" id="movemenrtypesDropdown" class="form-control">
                                                                                                                    <option value="">Select</option>
                                                                                                                    <option value="storage_location">Storage Location to Storage Location</option>
                                                                                                                    <!-- <option value="item">Item To Item</option> -->

                                                                                                                </select>

                                                                                                            </div>

                                                                                                            <div class="col-lg-6 col-md-6 col-sm-12 cost-center-col">
                                                                                                                <!-- <div class="item">
                                    <label for="">Item</label>
                                    <select name="item_name" id="itemdropdown" data-val="10" class="select2 form-control  itemdropdown itemdropdown_10">
                                        <option value="">Items</option>
                                        <?php
                                        $funcList = queryGet("SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `erp_inventory_items` as goods ON stock.itemId=goods.itemId WHERE stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId desc", true);

                                        foreach ($funcList["data"] as $func) {
                                        ?>
                                            <option value="<?= $func['itemId'] ?>">
                                                <?= $func['itemName'] ?>(<?= $func['itemCode'] ?>)</option>
                                        <?php } ?>
                                    </select>
                                </div> -->
                                                                                                                <!-- <div class="item_sl">
                                    <select name="item_sl" data-val="1" id="item_sl" class="select2 form-control item_sl item_sl_10">
                                        <option value="">Destination Storage Location</option>

                                    </select>
                                </div> -->
                                                                                                                <div class="sl">

                                                                                                                    <label for="">Destination Storage Location</label>
                                                                                                                    <select name="sl" class="select2 form-control ">
                                                                                                                        <option value="">Select Storage Location</option>
                                                                                                                        <option value="rmWhOpen">RM Open</option>
                                                                                                                        <option value="rmProdOpen">RM Production Open</option>
                                                                                                                        <option value="sfgStockOpen">SFG Open</option>
                                                                                                                        <option value="fgWhOpen">FG Open</option>
                                                                                                                        <option value="fgMktOpen">FG Market Open</option>

                                                                                                                    </select>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row info-form-view">

                                                                                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                                                                                    <label for="date">Creation Dates</label>
                                                                                                                    <input type="date" name="creationDate" class="form-control" min="<?= $min ?>" max="<?= $max ?>">
                                                                                                                </div>

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

                                                                                            <div class="col-lg col-md-6 col-sm-6">

                                                                                            </div>

                                                                                            <table class="table tabel-hover table-nowrap">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>Item </th>
                                                                                                        <th>UOM</th>
                                                                                                        <th>Source Storage Location</th>
                                                                                                        <th>Qty</th>



                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody id="">
                                                                                                    <tr id="">
                                                                                                        <td><select name="item[1][name]" id="itemsDropDown_<?= $oneInvItem["stockSummaryId"] ?>" data-val="<?= $oneInvItem["stockSummaryId"] ?>" class="select2 form-control itemsDropDown itemsDropDown_<?= $oneInvItem["stockSummaryId"] ?>">
                                                                                                                <option value="<?= $oneInvItem["itemId"] ?>" selected><?= $oneInvItem["itemName"]  ?> </option>

                                                                                                            </select>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <?php
                                                                                                            $buom_id = $oneInvItem['baseUnitMeasure'];
                                                                                                            $iuom_id = $oneInvItem['issueUnitMeasure'];
                                                                                                            $buom_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTER_UOM . "` WHERE `uomId`=$buom_id");
                                                                                                            $buom = $buom_sql["data"]["uomName"];
                                                                                                            $iuom_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTER_UOM . "` WHERE `uomId`=$iuom_id");
                                                                                                            $iuom = $iuom_sql["data"]["uomName"];

                                                                                                            ?>
                                                                                                            <select name="item[1][uom]" id="uom_<?= $oneInvItem["stockSummaryId"] ?>" class="select2 form-control uom uom_<?= $oneInvItem["stockSummaryId"] ?>">
                                                                                                                <option value="">UOM</option>


                                                                                                                <option value="<?= $oneInvItem['baseUnitMeasure'] ?>"><?= $buom ?></option>
                                                                                                                <option value="<?= $oneInvItem['issueUnitMeasure'] ?>"><?= $iuom ?></option>
                                                                                                            </select>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <select name="item[1][storagelocation]" data-val="<?= $oneInvItem["stockSummaryId"] ?>" id="storagelocation_<?= $oneInvItem["stockSummaryId"] ?>" class="select2 form-control storagelocation storagelocation_<?= $oneInvItem["stockSummaryId"] ?>">
                                                                                                                <option value="">Select Storage Location</option>
                                                                                                                <option value="rmWhOpen">RM Open</option>


                                                                                                                <option value="rmProdOpen">RM Production Open</option>
                                                                                                                <option value="sfgStockOpen">SFG Open</option>
                                                                                                                <option value="fgWhOpen">FG Open</option>
                                                                                                                <option value="fgMktOpen">FG Market Open</option>


                                                                                                            </select>
                                                                                                        </td>
                                                                                                        <td><input id="quantity_<?= $oneInvItem["stockSummaryId"] ?>" class="form-control quantity quantity_<?= $oneInvItem["stockSummaryId"] ?>" type="number" name="item[1][quantity]">
                                                                                                            <p id="quan_error_<?= $oneInvItem["stockSummaryId"] ?>" class="text-danger"></p>
                                                                                                        </td>



                                                                                                    </tr>

                                                                                                </tbody>
                                                                                                <!-- <tbody class="total-calculate">
                        <tr>
                            <td colspan="4" class="text-right" style="border: none;"> </td>
                            <td colspan="0" class="text-right pr-3" style="border: none;">Total Amount</td>
                            <input type="hidden" name="totalAmt" id="grandTotalAmountInput" value="0.00">
                            <td colspan="2" class="text-right pr-3" style="border: none; background: none;" id="grandTotalAmount">0.00</th>
                        </tr> -->

                            </tbody>

                        </table>

                        <?php

                                    if ($count > 0 && $count > $GLOBALS['show']) {

                        ?>

                            <div class="pagination align-right">

                                <?php pagination($count, "frm_opts"); ?>

                            </div>



                            <!-- End .pagination -->



                        <?php  } ?>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">

                    <button type="submit" id="subBtn" name="addNewPOFormSubmitBtn" class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Save & Close</button>

                </div>
            </div>
        </div>


        </form>
</div>
</div>
</div>
</div>
</div>
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

                <input type="hidden" name="pageTableName" value="ERP_INVENTORY_ITEMS" />

                <div class="modal-body">

                    <div id="dropdownframe"></div>

                    <div id="main2">

                        <table>

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />

                                    Item Code</td>

                            </tr>

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />

                                    Item Name</td>

                            </tr>

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />

                                    Metarial Type</td>

                            </tr>

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />

                                    Total Qty </td>

                            </tr>

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />

                                    UOM </td>

                            </tr>

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />

                                    Valuation Class</td>

                            </tr>

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />

                                    Price(MW) </td>

                            </tr>

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox8" value="8" />

                                    Total Value </td>

                            </tr>

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(9, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox9" value="9" />

                                    Last Received On </td>

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



</section>
</div>

<script>
    $(document).on("click", ".add-btn-minus", function() {
        $(this).parent().parent().remove();
    });


    $(document).on("click", "#shipToAddressSaveBtn", function() {
        document.getElementById("addresscheckbox").checked = false;

        console.log("clickinggggggggg");
        let radioBtnVal = $('input[name="shipToAddress"]:checked').val();
        let addressHead = ($(`#shipToAddressHeadText_${radioBtnVal}`).html()).trim();
        let addressBody = ($(`#shipToAddressBodyText_${radioBtnVal}`).html()).trim();
        console.log(addressBody);
        $("#shipToAddressDiv").html(addressBody);
    });

    // $(document).on("click","#addresscheckbox", function(){
    //   console.log("clickinggggggggg");
    //     let radioBtnVal = $('input[name="shipToAddress"]:checked').val();
    //     let addressHead = ($(`#shipToAddressHeadText_${radioBtnVal}`).html()).trim();
    //     let addressBody = ($(`#shipToAddressBodyText_${radioBtnVal}`).html()).trim();
    //     console.log(addressBody);
    //     $("#shipToAddressDiv").html(addressBody);
    // });

    function addTransferItem() {
        let addressRandNo = Math.ceil(Math.random() * 100000);
        $(`#item_add`).append(`   <tr>
                                                    <td><select name="item[${addressRandNo}][name]" id="itemsDropDown_${addressRandNo}"   data-val="${addressRandNo}" class="select2 form-control itemsDropDown itemsDropDown_${addressRandNo}">
                                                            <option value="">Items</option>
                                                            <?php
                                                            $funcList = queryGet("SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `erp_inventory_items` as goods ON stock.itemId=goods.itemId WHERE stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId desc", true);

                                                            foreach ($funcList["data"] as $func) {
                                                            ?>
                                                                <option value="<?= $func['itemId'] ?>">
                                                                    <?= $func['itemName'] ?>(<?= $func['itemCode'] ?>)</option>
                                                            <?php } ?>
                                                        </select></td>
                                                    <td>
                                                    <select name="item[${addressRandNo}][uom]" id="uom_${addressRandNo}" class="select2 form-control uom uom_${addressRandNo}">
                                                            <option value="">UOM</option>
                                                            
                                                            
                                                        </select> 
                                                    </td>
                                                    <td>
                                                    <select name="item[${addressRandNo}][storagelocation]" id="storagelocation_${addressRandNo}"  data-val="${addressRandNo}" class="select2 form-control storagelocation storagelocation_${addressRandNo}" required>
                                                            <option value="">Storage Location</option>
                                                        </select> 
                                                    </td>
                                                    <td><input id="quantity_${addressRandNo}" class="form-control quantity_${addressRandNo} qty_validation" data-val="${addressRandNo}" type = "number" name="item[${addressRandNo}][quantity]">  <p id="quan_error_${addressRandNo}" class="text-danger"></p></td>
                                                    
                                                    
                                                   
                                                </tr>`);
    }



    function loadItems() {
        $.ajax({
            type: "GET",
            url: `ajaxs/transfer/ajax-items.php`,
            beforeSend: function() {
                $("#itemsDropDown").html(`<option value="">Loding...</option>`);
            },
            success: function(response) {
                $("#itemsDropDown").html(response);
            }
        });
    }
    loadItems();
</script>
<script>
    $(document).ready(function() {

        $(".add_data").click(function() {
            var data = this.value;
            $("#creatData").val(data);
            //confirm('Are you sure to Submit?')
            $("#submitPoForm").submit();
        });
    });
    $(document).ready(function() {
        $('#itemsDropDown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });
        $('#vendorDropdown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });


        $(document).ready(function() {
            $('input[type="radio"]').click(function() {
                var inputValue = $(this).attr("value");
                var targetBox = $("." + inputValue);
                $(".box").not(targetBox).hide();
                $(targetBox).show();
            });
        });
        // **************************************

        // get item details by id
        $(document).on("change", ".itemsDropDown", function() {
            let itemId = $(this).val();
            let itemRowVal = $(this).data('val');
            //alert(itemRowVal);

            $.ajax({
                type: "GET",
                url: `ajaxs/transfer/ajax-items-list.php`,
                data: {
                    act: "listItem",
                    itemId
                },
                beforeSend: function() {
                    $(".uom_" + itemRowVal).html(`<option value="">Loding...</option>`);
                    $(".storagelocation_" + itemRowVal).html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    console.log(response);
                    var obj = jQuery.parseJSON(response);
                    $(".uom_" + itemRowVal).html(obj['uom']);
                    $(".storagelocation_" + itemRowVal).html(obj['slocation']);


                }

            });
        });


        ///item wise sl on itwm to item 
        $(document).on("change", ".itemdropdown", function() {
            let itemId = $(this).val();
            let itemRowVal = $(this).data('val');
            //alert(itemRowVal);

            $.ajax({
                type: "GET",
                url: `ajaxs/transfer/ajax-items-list.php`,
                data: {
                    act: "listItem",
                    itemId
                },
                beforeSend: function() {
                    //  $(".uom_" + itemRowVal).html(`<option value="">Loding...</option>`);
                    $(".item_sl_" + itemRowVal).html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    console.log(response);
                    var obj = jQuery.parseJSON(response);
                    //  $(".uom_" + itemRowVal).html(obj['uom']);
                    $(".item_sl_" + itemRowVal).html(obj['slocation']);


                }

            });
        });

        // get item details by id
        $(document).on("change", ".storagelocation", function() {
            let storagelocationId = $(this).val();
            let storagelocationRowVal = $(this).data('val');
            let ItemId = $(".itemsDropDown_" + storagelocationRowVal).val();
            // alert(ItemId);

            $.ajax({
                type: "GET",
                url: `ajaxs/transfer/ajax-items-list.php`,
                data: {
                    act: "maxlimit",
                    storagelocationId,
                    ItemId
                },
                beforeSend: function() {
                    // $(".uom_"+itemRowVal).html(`<option value="">Loding...</option>`);
                },
                success: function(response) {

                    //  alert(response);
                    //  var obj = jQuery.parseJSON(response);
                    $(".quantity_" + storagelocationRowVal).val(response);


                    // calculate_max();


                    $(".quantity_" + storagelocationRowVal).keyup(function() {

                        calculate_max(storagelocationRowVal, response);

                    });




                }

            });

            // $(".quantity_"+storagelocationRowVal).val('666');
        });

        function calculate_max(storagelocationRowVal, response) {

            let val = $(".quantity_" + storagelocationRowVal).val();

            // alert()


            if (Number(val) > Number(response)) {
                // console.log(true);
                $("#quan_error_" + storagelocationRowVal).html(`<p id="quan_error" class="text-danger" > Limit Exceeded</p>`);
                document.getElementById("subBtn").disabled = true;

            } else {
                $("#quan_error_" + storagelocationRowVal).html('')
                document.getElementById("subBtn").disabled = false;
            }
        }

        $(document).on("click", ".delItemBtn", function() {
            // let id = ($(this).attr("id")).split("_")[1];
            // $(`#delItemRowBtn_${id}`).remove();
            $(this).parent().parent().remove();
            calculateAllItemsGrandAmount();
        });

        $(document).on('submit', '#addNewItemForm', function(event) {
            event.preventDefault();
            let formData = $("#addNewItemsForm").serialize();
            $.ajax({
                type: "POST",
                url: `ajaxs/po/ajax-items.php`,
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

        // $(document).on("keyup change", ".qty", function() {
        //     let id = $(this).val();
        //     var sls = $(this).attr("sls");
        //     alert(sls);
        //     $.ajax({
        //         type: "GET",
        //         url: `ajaxs/po/ajax-items-list.php`,
        //         data: {
        //             act: "totalPrice",
        //             itemId: "ss",
        //             id
        //         },
        //         beforeSend: function() {
        //             $(".totalPrice").html(`<option value="">Loding...</option>`);
        //         },
        //         success: function(response) {
        //             console.log(response);
        //             $(".totalPrice").html(response);
        //         }
        //     });
        // })


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
</script>




<script>
    $('#movemenrtypesDropdown').change(function() {
        var select = this.value;
        if ($(this).val() == 'item') {
            $('.item').show();
            $('.sl').hide();
            $('.item_sl').show();

        } else if ($(this).val() == 'storage_location') {
            $('.item').hide();
            $('.sl').show();
            $('.item_sl').hide();

        } else {
            $('.sl').hide();
            $('.item').hide();
            $('.item_sl').hide();

        }
    });

    // $('#item_transfer').change(function(){

    //     var select = this.value;






    // });





    /********************************************** */
    function calculateAllItemsGrandAmount() {
        let grandTotal = 0;
        $(".itemTotalPrice").each(function() {
            let itemTotalPrice = parseFloat($(this).val());
            grandTotal += itemTotalPrice > 0 ? itemTotalPrice : 0;
        });
        $("#grandTotalAmount").html(grandTotal.toFixed(2));
        $("#grandTotalAmountInput").val(grandTotal.toFixed(2));
    }

    function calculateOneItemRowAmount(rowNum) {
        let qty = parseFloat($(`#itemQty_${rowNum}`).val());
        qty = qty > 0 ? qty : 0;
        let unitPrice = parseFloat($(`#itemUnitPrice_${rowNum}`).val());
        unitPrice = unitPrice > 0 ? unitPrice : 0;
        let totalPrice = unitPrice * qty;
        $(`#itemTotalPrice_${rowNum}`).val(totalPrice.toFixed(2));
        calculateAllItemsGrandAmount();
    }

    $(document).on("keyup", ".itemQty", function() {
        let rowNum = ($(this).attr("id")).split("_")[1];
        calculateOneItemRowAmount(rowNum);
    });
    $(document).on("keyup", ".itemUnitPrice", function() {
        let rowNum = ($(this).attr("id")).split("_")[1];
        calculateOneItemRowAmount(rowNum);
    });
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />