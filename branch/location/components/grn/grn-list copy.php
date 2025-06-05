<?php

function getGrnList()
{

    $loginCompanyId = $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"];
    $loginBranchId = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];
    $loginLocationId = $_SESSION["logedBranchAdminInfo"]["fldAdminLocationId"];
    $loginAdminId = $_SESSION["logedBranchAdminInfo"]["adminId"];
    $loginAdminType = $_SESSION["logedBranchAdminInfo"]["adminType"];
    return queryGet('SELECT * FROM `erp_grn` WHERE `companyId`=' . $loginCompanyId . ' AND `branchId`=' . $loginBranchId . ' AND `locationId`=' . $loginLocationId . ' AND `grnStatus`!="deleted"', true);
}




?>



<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div class="p-0 pt-1 my-2">
                        <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                            <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                <h3 class="card-title">Manage GRN</h3>
                                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?post-grn" class="btn btn-sm btn-primary"><i class="fa fa-plus" style="margin-right: 0;"></i></a>
                            </li>
                        </ul>
                    </div>
                    <div class="card card-tabs" style="border-radius: 20px;">
                        <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                            <div class="card-body">
                                <div class="row filter-serach-row">
                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                        <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-lg-10 col-md-10 col-sm-12">
                                        <div class="section serach-input-section">
                                            <input type="text" id="myInput" placeholder="" class="field form-control" />
                                            <div class="icons-container">
                                                <div class="icon-search">
                                                    <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                                </div>
                                                <div class="icon-close">
                                                    <i class="fa fa-search po-list-icon" onclick="javascript:alert('Hello World!')" id="myBtn"></i>
                                                    <script>
                                                        var input = document.getElementById("myInput");
                                                        input.addEventListener("keypress", function(event) {
                                                            if (event.key === "Enter") {
                                                                event.preventDefault();
                                                                document.getElementById("myBtn").click();
                                                            }
                                                        });
                                                    </script>
                                                </div>
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

                        <table class="table defaultDataTable table-hover">
                            <thead>
                                <tr class="alert-light">
                                    <th class="borderNone">GRN Code</th>
                                    <th class="borderNone">PO No</th>
                                    <th class="borderNone">Vendor Code</th>
                                    <th class="borderNone">Document No</th>
                                    <th class="borderNone">Document Date</th>
                                    <th class="borderNone">Posting Date</th>
                                    <th class="borderNone">Total Amount</th>
                                    <th class="borderNone">Approve Status</th>
                                    <th class="borderNone">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $grnListObj = getGrnList();
                                if ($grnListObj["status"] == "success") {
                                    foreach ($grnListObj["data"] as $oneGrnRow) {?>
                                        <tr>
                                            <td><?= $oneGrnRow["grnCode"] ?></td>
                                            <td><?= $oneGrnRow["grnPoNumber"] ?></td>
                                            <td><?= $oneGrnRow["vendorCode"] ?></td>
                                            <td><?= $oneGrnRow["vendorDocumentNo"] ?></td>
                                            <td><?= $oneGrnRow["vendorDocumentDate"] ?></td>
                                            <td><?= $oneGrnRow["postingDate"] ?></td>
                                            <td><?= $oneGrnRow["grnTotalAmount"] ?></td>
                                            <td><?= ucfirst($oneGrnRow["grnApprovedStatus"]) ?></td>
                                            <td>
                                                <a style="cursor:pointer" href="" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
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
</div>