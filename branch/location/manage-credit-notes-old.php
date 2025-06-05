<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");
require_once("../../app/v1/functions/branch/func-debit-credit-notes.php");

$company_data = getCompanyDataDetails($company_id);
$gl_account_length = $company_data['data']['gl_account_length'];

if (isset($_POST["createdata"])) {
  // console($_POST);
    $addNewObj = createCreditNote($_POST);
  // console($addNewObj);
  swalToast($addNewObj["status"], $addNewObj["message"], $_SERVER['PHP_SELF']);
}

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}
?>
<style>
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
</style>

<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
 
<?php
if(isset($_GET['create'])){

}
else{
  

?>



<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= LOCATION_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Credit Notes</a></li>
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
                                            <h5 class="modal-title" id="exampleModalLongTitle">Filter Purchase Request</h5>

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


                        <div>

                            <?php

                            $cond = '';


                            if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                $cond .= " AND (invItems.`itemCode` like '%" . $_REQUEST['keyword2'] . "%' OR invItems.`itemName` like '%" . $_REQUEST['keyword2'] . "%' OR invItems.`goodsType` like '%" . $_REQUEST['keyword2'] . "%')";
                            } else {

                                if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {

                                    $cond .= " AND (invItems.`itemCode` like '%" . $_REQUEST['keyword'] . "%' OR invItems.`itemName` like '%" . $_REQUEST['keyword'] . "%' OR invItems.`goodsType` like '%" . $_REQUEST['keyword'] . "%')";
                                }
                            }
                            //$inventoryObj = new Inventory();



                            $inventorySummaryObj = queryGet("SELECT * FROM `erp_credit_notes` WHERE `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id AND `status` = 'active' ORDER BY `id` DESC", true);

                           // console($inventorySummaryObj);


                            $countShow = "SELECT count(*) FROM  `erp_credit_notes` WHERE 1 AND`company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id AND `status` = 'active' ";

                            $countQry = mysqli_query($dbCon, $countShow);

                            $rowCount = mysqli_fetch_array($countQry);

                            $count = $rowCount[0];

                            $cnt = $GLOBALS['start'] + 1;



                            $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_CREDIT_NOTE", $_SESSION["logedBranchAdminInfo"]["adminId"]);

                            $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);

                            $settingsCheckbox = unserialize($settingsCh);

                            ?>
                        </div>





                        <table class="table defaultDataTable table-hover invertory-table" id="invertoryDataTable">
                            <thead>
                                <tr class="alert-light">

                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                        <th>Credit Note Number</th>
                                    <?php
                                    }
                                    if (in_array(2, $settingsCheckbox)) { ?>
                                        <th>Creditor Type</th>
                                    <?php
                                    }
                                    if (in_array(3, $settingsCheckbox)) { ?>

                                        <th> Party Code</th>
                                    <?php
                                    }
                                    if (in_array(4, $settingsCheckbox)) { ?>

                                        <!-- <th>Movement Type</th> -->
                                        <th>  Party Name </th>
                                    <?php
                                    }
                                    if (in_array(5, $settingsCheckbox)) { ?>

                                        <th>Reference Code </th>
                                    <?php
                                    }
                                    if (in_array(6, $settingsCheckbox)) { ?>

                                        <th>Credit Note Reference</th>
                                    <?php
                                    }
                                    if (in_array(7, $settingsCheckbox)) { ?>

                                        <th>Document Number</th>
                                    <?php
                                    }
                                    if (in_array(8, $settingsCheckbox)) { ?>

                                        <!-- <th>Resarve Qty</th> -->
                                        <th>Document Date</th>
                                    <?php
                                    }
                                    if (in_array(9, $settingsCheckbox)) { ?>

                                        <th>Posting Date</th>
                                    <?php
                                    
}
if (in_array(10, $settingsCheckbox)) { ?>

    <th>Remark </th>

                    <?php                }

                                    ?>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                foreach ($inventorySummaryObj["data"] as $oneInvItem) {

                                    // $total_qty = $oneInvItem['rmWhOpen'] + $oneInvItem['rmWhReserve'] + $oneInvItem['rmProdOpen'] + $oneInvItem['rmProdReserve'] + $oneInvItem['sfgStockOpen'] + $oneInvItem['sfgStockReserve'] + $oneInvItem['fgWhOpen'] + $oneInvItem['fgWhReserve'] + $oneInvItem['fgMktOpen'] + $oneInvItem['fgMktReserve'];

                                     //console($oneInvItem);
                                ?>
                                    <tr>
                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                            <td><?= $oneInvItem["credit_note_no"] ?></td>
                                        <?php
                                        }
                                        if (in_array(2, $settingsCheckbox)) { ?>

                                            <td><?= ucfirst($oneInvItem["creditors_type"]) ?></td>
                                        <?php
                                        }
                                        if (in_array(3, $settingsCheckbox)) { ?>

                                            <td><?= $oneInvItem["party_code"] ?></td>
                                        <?php
                                        }
                                        if (in_array(4, $settingsCheckbox)) { ?>

                                            <!-- <td></td> -->
                                            <td><?= $oneInvItem['party_name'] ?></td>
                                        <?php
                                        }
                                        if (in_array(5, $settingsCheckbox)) { ?>

                                            <td> <?= $oneInvItem["refarenceCode"] ?> </td>
                                        <?php
                                        }
                                        if (in_array(6, $settingsCheckbox)) { ?>

                                            <td><?= $oneInvItem['creditNoteReference'] ?></td>
                                        <?php
                                        }
                                        if (in_array(7, $settingsCheckbox)) { ?>

                                            <td><?= $oneInvItem['documentNo'] ?> </td>
                                        <?php
                                        }
                                        if (in_array(8, $settingsCheckbox)) { ?>


                                            <td> <?= $oneInvItem["documentDate"] ?> </td>
                                        <?php
                                        }
                                        if (in_array(9, $settingsCheckbox)) { ?>
                                            <td> <?= $oneInvItem["postingDate"] ?></td>
                                        <?php
                                        }
                                        if (in_array(10, $settingsCheckbox)) { ?>
                                          <td> <?= $oneInvItem["remark"] ?></td>
                                          <?php
                                        }
                                        ?>
                                        <td>
                                            <!-- <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $oneInvItem["itemCode"] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a> -->


                                            <div class="modal fade right inventory-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $oneInvItem["itemCode"] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                    <!--Content-->
                                                    <div class="modal-content">
                                                        <!--Header-->
                                                        <div class="modal-header">
                                                            <?= $oneInvItem["itemCode"] ?><br>
                                                            <?= $oneInvItem["itemName"] ?>
                                                            <p>Item Price - <?= $oneInvItem["itemPrice"] ?> </p>
                                                            <p>Item MWP - <?= $oneInvItem["movingWeightedPrice"] ?> </p>
                                                            <p>Item Valuation - <?= $oneInvItem["priceType"] ?></p>
                                                            <p>Item Total Quantity - <?= $oneInvItem["itemTotalQty"] ?></p>

                                                            <ul class="nav nav-pills nav-tabs mb-3" id="pills-tab" role="tablist">
                                                                <!-- <li class="nav-item" role="presentation">
                                                                    <a class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" href="#pills_home_<?= $oneInvItem["stockSummaryId"] ?>" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Home</a>
                                                                </li> -->
                                                                <li class="nav-item">
                                                                    <a class="nav-link" id="home-tab" data-toggle="tab" href="#home_<?= $oneInvItem["stockSummaryId"] ?>" role="tab" aria-controls="home" aria-selected="true">Stock Details</a>
                                                                </li>

                                                                <!-- <li class="nav-item" role="presentation">
                                                                    <a class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" href="#pills_profile_<?= $oneInvItem["stockSummaryId"]  ?>" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Profile</a>
                                                                </li> -->
                                                                <li class="nav-item">
                                                                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile_<?= $oneInvItem["stockSummaryId"] ?>" role="tab" aria-controls="profile" aria-selected="false">Stock log</a>
                                                                </li>
                                                                <!-- <li class="nav-item" role="presentation">
                                                                    <a class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" href="#pills_contact_<?= $oneInvItem["stockSummaryId"]  ?>" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Contact</a>
                                                                </li> -->
                                                                <li class="nav-item">
                                                                    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact_<?= $oneInvItem["stockSummaryId"] ?>" role="tab" aria-controls="contact" aria-selected="false">Transfer</a>
                                                                </li>
                                                            </ul>

                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="tab-content">


                                                                <div class="tab-pane fade show active" id="home_<?= $oneInvItem["stockSummaryId"] ?>" role="tabpanel" aria-labelledby="pills-contact-tab">

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
                                                                            <?= $oneInvItem["rmWhOpen"] ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $oneInvItem["rmWhReserve"] ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            RM production
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $oneInvItem["rmProdOpen"] ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $oneInvItem["rmProdReserve"] ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            SFG Stock
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $oneInvItem["sfgStockOpen"] ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $oneInvItem["sfgStockReserve"] ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            FG warehouse
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $oneInvItem["fgWhOpen"] ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $oneInvItem["fgWhReserve"] ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            FG Marketing
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $oneInvItem["fgMktOpen"] ?>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <?= $oneInvItem["fgMktReserve"] ?>
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
                                                                        $sql = queryGet("SELECT * FROM `erp_inventory_stocks_log` WHERE `itemId` ='" . $oneInvItem["itemId"] . "'", true);
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

                <input type="hidden" name="pageTableName" value="ERP_CREDIT_NOTE" />

                <div class="modal-body">

                    <div id="dropdownframe"></div>

                    <div id="main2">

                        <table>

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />

                                Credit Note Number</td>

                            </tr>

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />

                                Creditor Type</td>

                            </tr>

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />

                                Party Code</td>

                            </tr>

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />

                                Party Name </td>

                            </tr>

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />

                                Reference Code  </td>

                            </tr>

                           

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />

                                Credit Note Reference </td>

                            </tr>

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />

                                Document Number</td>

                            </tr>

                            <tr>

                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox8" value="8" />

                                Document Date </td>

                            </tr>

                            <tr>

<td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(9, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox9" value="9" />

Posting Date </td>

</tr>

<tr>

<td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(10, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox10" value="10" />

Remark </td>

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

<?php
}
require_once("../common/footer.php");
?>
<script>
  $('#addNewJournalForm').on('submit', function() {
    let dtotal = 0;
    $(".dr-amount").each(function() {
      let velu = parseFloat($(this).val());
      if (velu > 0) {
        dtotal += parseFloat(velu);
      }
    });
    let ctotal = 0;
    $(".cr-amount").each(function() {
      let velu = parseFloat($(this).val());
      if (velu > 0) {
        ctotal += parseFloat(velu);
      }
    });

    if (dtotal != ctotal) {
      if (dtotal != ctotal) {
        let Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        });
        Toast.fire({
          icon: `warning`,
          title: `&nbsp;Debit and credit mismatch!`
        });
        return false;
      }
      return false;
    }
  });

  $(document).on("keyup keydown paste", '.dr-amount', function() {
    let valllAc = $(this).val();
    calculateDrAmount();
  });
  function calculateDrAmount(){
    let sum = 0;
    $(".dr-amount").each(function() {
      let velu = parseFloat($(this).val());
      if (velu > 0) {
        sum += parseFloat(velu);
      }
    });
    sum = sum.toFixed(2);
    $('.debit-total').html(sum);
  }

  $(document).on("keyup keydown paste", '.cr-amount', function() {
    let valllAc = $(this).val();
    calculateCrAmount();
  });
  function calculateCrAmount(){
    let sum = 0;
    $(".cr-amount").each(function() {
      let velu = parseFloat($(this).val());
      if (velu > 0) {
        sum += parseFloat(velu);
      }
    });
    sum = sum.toFixed(2);
    $('.credit-total').html(sum);
  }

  $(document).on("click", ".add-debit", function() {
    let function_id = $(this).val();
    let rand_no = Math.ceil(Math.random() * 100000);
    var bullet_point_html = `<div class="row"><div class="col-lg-7 col-md-7 col-sm-7">
                          <select id="debit_${rand_no}" name="journal[debit][gl][]" class="form-control" required>
                          <option value="">Select Debit G/L</option>
                           <?= $list; ?>
                          </select>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2">
                          <input step="0.01" type="number" id="dr_${rand_no}" name="journal[debit][amount][]" class="form-control dr-amount" value="" placeholder="Enter Amount" required>                                    
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2"></div>
                          <div class="col-lg-1 col-md-1 col-sm-1">
                          <button type="button" class="btn btn-danger delete_new_bullet_point">
                            <i class="fa fa-minus"></i>
                          </button>
                        </div></div>`;
    $('.debit-main').append(bullet_point_html);
  });

  $(document).on("click", ".add-credit", function() {
    let function_id = $(this).val();
    let rand_no = Math.ceil(Math.random() * 100000);
    var bullet_point_html = `<div class="row"><div class="col-lg-7 col-md-7 col-sm-7">
                          <select id="credit_${rand_no}" name="journal[credit][gl][]" class="form-control" required>
                          <option value="">Select Credit G/L</option>
                           <?= $list; ?>
                          </select>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2"></div>
                          <div class="col-lg-2 col-md-2 col-sm-2">
                          <input step="0.01" type="number" id="cr_${rand_no}" name="journal[credit][amount][]" class="form-control cr-amount" value="" placeholder="Enter Amount" required>    
                          </div>
                          <div class="col-lg-1 col-md-1 col-sm-1">
                          <button type="button" class="btn btn-danger delete_new_bullet_point">
                            <i class="fa fa-minus"></i>
                          </button>
                        </div></div>`;
    $('.credit-main').append(bullet_point_html);
  });

  $(document).on("click", ".delete_new_bullet_point", function() {
    $(this).parent().parent().remove();
    calculateDrAmount();
    calculateCrAmount();
  });

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