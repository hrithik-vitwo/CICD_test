<style>
    .dataTables_length {
        display: none;
    }


    .inventory-modal .modal-header {
        height: auto;
    }

    .inventory-modal .modal-body {
        top: -24px;
    }

    .tab-pane.profile-tab-stock-log {
        overflow: auto;
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= LOCATION_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Inventory</a></li>

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
						$keywd='';
						if (isset($_REQUEST['keyword'])){
                        $keywd=$_REQUEST['keyword'];
                        }
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
                        <!-- 
                        <div class="col-lg-2 col-md-2 col-sm-12">

                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                        </div> -->
                        <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                            <div class="card-body">
                                <div class="row filter-serach-row">
                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                        <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-lg-10 col-md-10 col-sm-12">
                                        <div class="row table-header-item">
                                            <div class="col-lg-11 col-md-11 col-sm-11">
                                                <div class="section serach-input-section">
                                                    <input type="text" name="keyword" id="myInput" placeholder="" class="field form-control" value="<?php echo $keywd; ?>">
                                                    <div class="icons-container">

                                                        <div class="icon-close">
                                                            <i class="fa fa-search po-list-icon" id="myBtn"></i>

                                                        </div>
                                                    </div>
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


                        <div>







                            <?php



                            $ddate = "";

                            if (isset($_REQUEST['date'])) {
                                $ddate = $_REQUEST['date'];
                            } else {
                                $ddate = $today;
                            }



                            $cond = '';

                            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                $cond .= "AND (
                                                item.itemCode LIKE '%" . $_REQUEST['keyword'] . "%'
                                                OR item.itemName LIKE '%" . $_REQUEST['keyword'] . "%'
                                                OR types.goodTypeName LIKE '%" . $_REQUEST['keyword'] . "%'
                                                OR summary.priceType LIKE '%" . $_REQUEST['keyword'] . "%'
                                                OR summary.updatedAt LIKE '%" . $_REQUEST['keyword'] . "%'
                                                OR UOM.uomName LIKE '%" . $_REQUEST['keyword'] . "%'  
                                                )";
                            }
                            // $inventorySummaryObj = queryGet("SELECT invSummary.*, goodTypes.`goodTypeName` AS goodType,  item.`itemCode`, item.`itemName`, invItems.`itemDesc`, invItems.`baseUnitMeasure`, invItems.`goodsType`, goodUoms.`uomName`, goodUoms.`uomDesc` FROM `erp_inventory_stocks_summary` AS invSummary, `erp_inventory_items` AS invItems, `erp_inventory_mstr_good_types` AS goodTypes, `erp_inventory_mstr_uom` AS goodUoms WHERE invSummary.itemId = invItems.itemId AND invSummary.`company_id` =' . $company_id . ' AND invSummary.`branch_id` =' . $branch_id . ' AND invSummary.`location_id` =' . $location_id . ' AND invItems.`goodsType` = goodTypes.`goodTypeId` AND invItems.`baseUnitMeasure`=goodUoms.`uomId` ' . $cond . ' ORDER BY invSummary.`updatedAt` DESC LIMIT $GLOBALS['start'], $GLOBALS['show'", true);

                            // $inventorySummaryObj = queryGet('SELECT invSummary.*, goodTypes.`goodTypeName` AS goodType,  invItems.`itemCode`, invItems.`itemName`, invItems.`itemDesc`, invItems.`baseUnitMeasure`, invItems.`goodsType`, goodUoms.`uomName`, goodUoms.`uomDesc` FROM `erp_inventory_stocks_summary` AS invSummary, `erp_inventory_items` AS invItems, `erp_inventory_mstr_good_types` AS goodTypes, `erp_inventory_mstr_uom` AS goodUoms WHERE invSummary.itemId = invItems.itemId AND invSummary.`company_id` =' . $company_id . ' AND invSummary.`branch_id` =' . $branch_id . ' AND invSummary.`location_id` =' . $location_id . ' AND invItems.`goodsType` = goodTypes.`goodTypeId` AND invItems.`baseUnitMeasure`=goodUoms.`uomId` ' . $cond . ' ORDER BY invSummary.`updatedAt` DESC limit  ' . $GLOBALS['start'] . ' , ' . $GLOBALS['show'] . ' ', true);

                            $newSql = "SELECT 
                                item.itemId,
                                item.itemCode,
                                item.itemName,
                                types.goodTypeName AS material_type,
                                SUM(last_closing_quantity) AS total_quantity,
                                UOM.uomName AS uom,
                                summary.priceType AS valuation_class,
                                summary.movingWeightedPrice AS price,
                                ROUND(SUM(last_closing_quantity) * summary.movingWeightedPrice, 2) AS total_value,
                                summary.updatedAt AS last_received_on
                            FROM (
                                SELECT 
                                    report.item_id,
                                    report.storage_id,
                                    SUM(report.total_closing_qty) AS last_closing_quantity
                                FROM 
                                    erp_inventory_stocks_log_report AS report
                                INNER JOIN (
                                    SELECT 
                                        item_id,
                                        storage_id,
                                        MAX(report_date) AS max_date
                                    FROM 
                                        erp_inventory_stocks_log_report
                                    WHERE
                                        report_date <= '" . $ddate . "' AND company_id = $company_id AND branch_id = $branch_id AND location_id = $location_id 
                                    GROUP BY 
                                        item_id, storage_id
                                ) AS max_dates
                                ON 
                                    report.item_id = max_dates.item_id
                                    AND report.storage_id = max_dates.storage_id
                                    AND report.report_date = max_dates.max_date
                                GROUP BY 
                                    report.item_id, report.storage_id
                            ) AS last_closing_quantities
                            LEFT JOIN erp_inventory_items AS item ON last_closing_quantities.item_id = item.itemId
                            LEFT JOIN erp_inventory_mstr_good_types AS types ON item.goodsType = types.goodTypeId
                            LEFT JOIN erp_inventory_stocks_summary AS summary ON item.itemId = summary.itemId 
                            LEFT JOIN erp_inventory_mstr_uom AS UOM ON item.baseUnitMeasure = UOM.uomId
                            WHERE
								 item.company_id = $company_id  " . $cond . "
                            GROUP BY 
                                item_id
                            LIMIT " . $GLOBALS['start'] . ", " . $GLOBALS['show'] . "
                            
                                ";
                            //".$cond."
                            // LIMIT " . $GLOBALS['start'] . ", " . $GLOBALS['show'] . ";

                            $countsql = "SELECT COUNT(*) FROM
                            (SELECT 
                                item.itemCode,
                                item.itemName,
                                types.goodTypeName AS material_type,
                                SUM(last_closing_quantity) AS total_quantity,
                                UOM.uomName AS uom,
                                summary.priceType AS valuation_class,
                                summary.movingWeightedPrice AS price,
                                ROUND(SUM(last_closing_quantity) * summary.movingWeightedPrice, 2) AS total_value,
                                summary.updatedAt AS last_received_on
                            FROM (
                                SELECT 
                                    report.item_id,
                                    report.storage_id,
                                    SUM(report.total_closing_qty) AS last_closing_quantity
                                FROM 
                                    erp_inventory_stocks_log_report AS report
                                INNER JOIN (
                                    SELECT 
                                        item_id,
                                        storage_id,
                                        MAX(report_date) AS max_date
                                    FROM 
                                        erp_inventory_stocks_log_report
                                    WHERE
                                          report_date <= '" . $ddate . "' AND company_id = $company_id AND branch_id = $branch_id AND location_id = $location_id
                                    GROUP BY 
                                        item_id, storage_id
                                ) AS max_dates
                                ON 
                                    report.item_id = max_dates.item_id
                                    AND report.storage_id = max_dates.storage_id
                                    AND report.report_date = max_dates.max_date
                                GROUP BY 
                                    report.item_id, report.storage_id
                            ) AS last_closing_quantities
                            LEFT JOIN erp_inventory_items AS item ON last_closing_quantities.item_id = item.itemId
                            LEFT JOIN erp_inventory_mstr_good_types AS types ON item.goodsType = types.goodTypeId
                            LEFT JOIN erp_inventory_stocks_summary AS summary ON item.itemId = summary.itemId 
                            LEFT JOIN erp_inventory_mstr_uom AS UOM ON item.baseUnitMeasure = UOM.uomId
                             WHERE
								 item.company_id = $company_id  " . $cond . "                            
                            GROUP BY 
                                item_id) AS table_count
                            ";
                            $dbObj = new Database();

                            $dbObj->queryUpdate("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))", true);
                            $resultDataObj = $dbObj->queryGet($newSql, true);
                            $countObj = $dbObj->queryGet($countsql, false);
                            $count = $countObj['data']['COUNT(*)'];

                            // console($resultDataObj);

                            $cnt = $GLOBALS['start'] + 1;
                            $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_INVENTORY_ITEMS", $_SESSION["logedBranchAdminInfo"]["adminId"]);

                            $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);

                            $settingsCheckbox = unserialize($settingsCh);

                            //                             $condstock = '';
                            //                             if (isset($_REQUEST['date'])) {
                            //                                 $date = $_REQUEST['date'];

                            //                                 $condstock .= " AND DATE_FORMAT(postingDate, '%Y %m %d') <= DATE_FORMAT('" . $date . "', '%Y %m %d')";
                            //                             } else {

                            //                                 $condstock .= " AND DATE_FORMAT(postingDate, '%Y %m %d') <= DATE_FORMAT('" . $today . "', '%Y %m %d')";
                            //                             }

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
                                foreach ($resultDataObj["data"] as $oneInvItem) {
                                ?>

                                    <tr>
                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                            <td><?= $oneInvItem["itemCode"] ?></td>
                                        <?php
                                        }
                                        if (in_array(2, $settingsCheckbox)) { ?>

                                            <td><?= ucfirst($oneInvItem["itemName"]) ?></td>
                                        <?php
                                        }
                                        if (in_array(3, $settingsCheckbox)) { ?>

                                            <td><?= $oneInvItem["material_type"] ?></td>
                                        <?php
                                        }
                                        if (in_array(4, $settingsCheckbox)) { ?>

                                            <td><?= $oneInvItem["total_quantity"] ?></td>
                                        <?php
                                        }
                                        if (in_array(5, $settingsCheckbox)) { ?>

                                            <td> <?= $oneInvItem["uom"] ?> </td>
                                        <?php
                                        }
                                        if (in_array(6, $settingsCheckbox)) { ?>

                                            <td><?= $oneInvItem['valuation_class'] ?></td>
                                        <?php
                                        }
                                        if (in_array(7, $settingsCheckbox)) { ?>

                                            <td><?= number_format($oneInvItem["price"], 2); ?> </td>
                                        <?php
                                        }
                                        if (in_array(8, $settingsCheckbox)) { ?>


                                            <td> <?php echo number_format($oneInvItem["total_value"], 2); ?> </td>
                                        <?php
                                        }
                                        if (in_array(9, $settingsCheckbox)) { ?>

                                            <td> <?= $oneInvItem["last_received_on"]; ?></td>
                                        <?php
                                        }

                                        ?>

                                        <td>
                                            <a style="cursor:pointer" data-id="<?= $oneInvItem["itemId"]; ?>" data-itemname="<?= $oneInvItem["itemName"] ?>" data-itemcode="<?= $oneInvItem["itemCode"] ?>" data-valuationclass="<?= $oneInvItem["valuation_class"] ?>" data-totalquan="<?= $oneInvItem["total_quantity"] ?>" data-pricemwt="<?= $oneInvItem["price"] ?>" data-toggle="modal" class="btn btn-sm btn-modal"><i class="fa fa-eye po-list-icon"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>

                            </tbody>



                            <tbody>
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
                            </tbody>
                        </table>




                    </div>
                </div>



                <!-- right modal main start -->
                <div class="modal fade right inventory-modal customer-modal" id="fluidModalRightSuccessDemo_" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                        <!--Content-->
                        <div class="modal-content">
                            <!--Header-->
                            <div class="modal-header">
                                <p id="itemCodeModal"></p>
                                <p id="itemNameModal"></p>
                                <p>Item MWP: <span id="itemPriceModal"></span></p>
                                <p>Item Valuation: <span id="itemValModal"></span></p>
                                <p>Item Total Quantity: <span id="itemTotalModal"></span></p>

                                <ul class="nav nav-pills nav-tabs mb-3" id="pills-tab" role="tablist">

                                    <li class="nav-item">
                                        <a class="nav-link" id="home-tab" data-toggle="tab" href="#home_" role="tab" aria-controls="home" aria-selected="true">Stock Details</a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link stock-log" id="profile-tab" data-toggle="tab" href="#profile_" role="tab" aria-controls="profile" aria-selected="false" data-attr="<?= $oneInvItem["stockSummaryId"] ?>" data-item="<?= $oneInvItem["itemId"] ?>">Stock log</a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact_" role="tab" aria-controls="contact" aria-selected="false">Transfer</a>
                                    </li>
                                </ul>

                            </div>
                            <div class="modal-body">
                                <div class="tab-content">

                                    <div class="tab-pane fade show active" id="home_" role="tabpanel" aria-labelledby="pills-contact-tab">
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
                                                <p id="rmWareopen"></p>
                                            </div>
                                            <div class="col-4">
                                                <p id="rmWareres"></p>

                                            </div>
                                            <div class="col-4">
                                                RM production
                                            </div>
                                            <div class="col-4">
                                                <p id="rmProdopen"></p>

                                            </div>
                                            <div class="col-4">
                                                <p id="rmProdres"></p>

                                            </div>
                                            <div class="col-4">
                                                SFG Stock
                                            </div>
                                            <div class="col-4">
                                                <p id="sfgStockopen"></p>
                                            </div>
                                            <div class="col-4">
                                                <p id="sfgStockres"></p>
                                            </div>
                                            <div class="col-4">
                                                FG warehouse
                                            </div>
                                            <div class="col-4">
                                                <p id="fgwareopen"></p>

                                            </div>
                                            <div class="col-4">
                                                <p id="fgwareres"></p>

                                            </div>
                                            <div class="col-4">
                                                FG Marketing
                                            </div>
                                            <div class="col-4">
                                                <p id="fgMarkopen"></p>

                                            </div>
                                            <div class="col-4">
                                                <p id="fgMarkres"></p>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade profile-tab-stock-log stock-log" id="profile_" role="tabpanel" aria-labelledby="pills-contact-tab">
                                        <table class="table table-hover stockLogTable transactional-book-table" data-paging="true" data-responsive="false" style="position: relative;">
                                            <thead>
                                                <tr>
                                                    <th>Sl No</th>
                                                    <th>Location</th>
                                                    <th>Document Number</th>
                                                    <th>Item Group</th>
                                                    <th>Item Code</th>
                                                    <th>Item Name</th>
                                                    <th>Storage Location</th>
                                                    <th>Party Code</th>
                                                    <th>Party Name</th>
                                                    <th>Batch Number</th>
                                                    <th>UOM</th>
                                                    <th>Movement Type</th>
                                                    <th>Quantity</th>
                                                    <th>Value</th>
                                                    <th>Currency</th>
                                                    <th>Date</th>

                                                </tr>
                                            </thead>

                                            <?php
                                            ?>
                                            <tbody class="stock-log-body">
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="tab-pane fade" id="contact_" role="tabpanel" aria-labelledby="pills-contact-tab">
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
                                                                </table>


                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                                            <button type="submit" id="subBtn" name="addNewPOFormSubmitBtn" class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Save & Close</button>

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
                <!-- right modal main end -->


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


<!-- For Pegination------->
<form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                    echo  $_REQUEST['pageNo'];
                                                } ?>">
</form>
<!-- End Pegination from------->

<?php

require_once("../common/footer.php");

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_stocklog_' . $currentDateTime

?>

<!-- script for modal start -->
<script>
    let table;

    table = $('.stockLogTable').DataTable({
        dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
        "lengthMenu": [10, 25, 50, 100, 200, 250],
        "ordering": false,
        info: true,


        buttons: [{
            extend: 'collection',
            text: '<ion-icon name="download-outline"></ion-icon> Export',
            buttons: [{
                extend: 'excel',
                text: '<ion-icon name="document-outline" class="ion-excel"></ion-icon> Excel',
                filename: '<?= $newFileName ?> '
            }]
        }],
        // select: true,
        "bPaginate": true,
    });


    $(".btn-modal").on("click", function() {
        let itemId = $(this).data('id');
        $("#itemNameModal").html($(this).data('itemname'));
        $("#itemCodeModal").html($(this).data('itemcode'));
        $("#itemValModal").html($(this).data('valuationclass'));
        $("#itemPriceModal").html($(this).data('pricemwt'));
        $("#itemTotalModal").html($(this).data('totalquan'));

        console.log(itemId);


        $.ajax({
            type: "GET",
            url: `ajaxs/ajax-inventory-stock-detail.php`,
            data: {
                act: "stock-detail",
                itemId: itemId,
                dDate: "<?= $ddate ?>"
            },
            beforeSend: function() {},
            success: function(res) {
                // console.log(res)
                var jsonObject = JSON.parse(res);

                $("#rmWareopen").html(jsonObject.rmWhOpen_qty);
                $("#rmWareres").html(jsonObject.rmWhReserve_qty);

                $("#rmProdopen").html(jsonObject.rmProdOpen_qty);
                $("#rmProdres").html(jsonObject.rmProdReserve_qty);

                $("#sfgStockopen").html(jsonObject.sfgStockOpen_qty);
                $("#sfgStockres").html(jsonObject.sfgStockReserve_qty);

                $("#fgwareopen").html(jsonObject.fgWhOpen_qty);
                $("#fgwareres").html(jsonObject.fgWhReserve_qty);

                $("#fgMarkopen").html(jsonObject.fgMarketOpen_qty);
                $("#fgMarkres").html(jsonObject.fgMarketReserve_qty);


            }
        });



        $.ajax({
            type: "GET",
            url: `ajaxs/ajax-inventory-stock-log.php`,
            data: {
                act: "stock-log",
                itemId: itemId,
                ddate: "<?= $ddate ?>"
            },
            beforeSend: function() {
                $(`.stock-log-body`).html(` <tr>
                                            <td class="text-center" colspan="15">Please Wait Data Is Loading...</td>
                                            </tr>`);

            },
            success: function(res) {

                // console.log(res);

                let resObj = JSON.parse(res);
                // console.log(resObj);
                let sl = 1;
                table.clear().draw();

                $.each(resObj, function(index, value) {

                    table.row.add([
                        sl++,
                        value.location,
                        value.document_no,
                        value.itemGroup,
                        value.itemCode,
                        value.itemName,
                        value.storage_location,
                        value.party_code,
                        value.party_code,
                        value.logRef,
                        value.uom,
                        value.movement_type,
                        value.qty,
                        value.rate,
                        value.currency,
                        value.Ddate,

                    ]).draw(false);
                });

            }
        });

        $('#fluidModalRightSuccessDemo_').one('shown.bs.modal', function() {
            $('#fluidModalRightSuccessDemo_').modal('show');
        }).modal('show'); // Initiate the modal show

    })
</script>
<!-- script for modal End -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />