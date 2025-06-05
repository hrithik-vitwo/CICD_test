<section class="content">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= LOCATION_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                <li class="breadcrumb-item active">
                    <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Pending GRN</a>
                    <a href="<?php echo LOCATION_URL; ?>manage-grn-invoice.php" class="btn btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
                </li>
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
                    <div class="filter-list">
                        <a href="<?= LOCATION_URL; ?>manage-pending-grn.php?pending" class="btn active"><i class="fa fa-stream mr-2 active"></i>Pendings List</a>
                        <a href="<?= LOCATION_URL; ?>manage-pending-grn.php?posting" class="btn"><i class="fa fa-list mr-2"></i>Posted List</a>
                    </div>
                    <div class="card card-tabs" style="border-radius: 20px;">
                        <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                            <div class="card-body">
                                <div class="row filter-serach-row">
                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                        <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-lg-10 col-md-10 col-sm-12">
                                        <div class="row table-header-item">
                                            <div class="col-lg-11 col-md-11 col-sm-11">
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
                                            <div class="col-lg-1 col-md-1 col-sm-1">
                                                <a href="<?php echo LOCATION_URL; ?>manage-grn-invoice.php" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Filter Vendors</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                            <input type="text" name="keyword" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?= $_REQUEST['keyword'] ?? "" ?>">
                                                        </div>
                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                            <select name="vendor_status_s" id="vendor_status_s" class="fld form-control" style="appearance: auto;">
                                                                <option value=""> Status </option>
                                                                <option value="active" <?= (isset($_REQUEST['vendor_status_s']) && 'active' == $_REQUEST['vendor_status_s']) ? 'selected' : ""; ?>>Active</option>
                                                                <option value="inactive" <?= (isset($_REQUEST['vendor_status_s']) && 'inactive' == $_REQUEST['vendor_status_s']) ? 'selected' : ""; ?>>Inactive</option>
                                                                <option value="draft" <?= (isset($_REQUEST['vendor_status_s']) && 'draft' == $_REQUEST['vendor_status_s']) ? 'selected' : "" ?>>Draft</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                                            <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?= $_REQUEST['form_date_s'] ?? "" ?>" />
                                                        </div>
                                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                                            <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?= $_REQUEST['form_date_s'] ?? "" ?>" />
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync fa-spin"></i>Reset</a>
                                                    <a type="button" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>Search</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div>
                            <?php
                            $grnListObj = $grnObj->getPendingGrnList();
                            //console($grnListObj);

                            ?>
                        </div>
                        <table class="table defaultDataTable table-hover">
                            <thead>
                                <tr class="alert-light">
                                    <th class="borderNone">Invoice Number</th>
                                    <th class="borderNone">PO No</th>
                                    <th class="borderNone">Vendor Name</th>
                                    <th class="borderNone">Vendor Code</th>
                                    <th class="borderNone">GST No</th>
                                    <th class="borderNone">Total Amount</th>
                                    <th class="borderNone">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                if ($grnListObj["status"] == "success") {
                                    foreach ($grnListObj["data"] as $oneGrnRow) { ?>
                                        <tr>
                                            <td><?= $oneGrnRow["inv_no"] ?></td>
                                            <td><?= $oneGrnRow["po_no"] ?></td>
                                            <td><?= $oneGrnRow["vendor_name"] ?></td>
                                            <td><?= $oneGrnRow["vendor_code"] ?></td>
                                            <td><?= $oneGrnRow["gst_no"] ?></td>
                                            <td><?= $oneGrnRow["total_amt"] ?></td>
                                            <td>

                                                <?php
                                                $documentNo = $oneGrnRow["inv_no"];
                                                $vendorCode = $oneGrnRow["vendor_code"];
                                                $checkGrnExist = queryGet('SELECT `grnId` FROM `erp_grn` WHERE `vendorDocumentNo`="' . $documentNo . '" AND `vendorCode` ="' . $vendorCode . '"');
                                                if ($checkGrnExist["numRows"] > 0) {
                                                    echo "Posted";
                                                } else {
                                                ?>
                                                    <a style="cursor:pointer" href="?view=<?= $oneGrnRow["grn_mul_id"] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                <?php }
                                }
                                ?>

                            </tbody>
                        </table>

                    </div>
                </div>
    </section>