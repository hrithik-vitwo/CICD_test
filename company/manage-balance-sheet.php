<?php
include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/company/func-branches.php");
include("../app/v1/functions/company/func-balance-sheet.php");


if (isset($_POST["changeStatus"])) {
    $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
    swalToast($newStatusObj["status"], $newStatusObj["message"],);
}
if (isset($_POST["visit"])) {
    $newStatusObj = VisitBranches($_POST);
    if ($newStatusObj["status"] == "success") {
        redirect(BRANCH_URL);
        swalToast($newStatusObj["status"], $newStatusObj["message"]);
    } else {
        swalToast($newStatusObj["status"], $newStatusObj["message"]);
    }
}


if (isset($_POST["createdata"])) {
    // console($_POST);
    $addNewObj = createDataBranches($_POST);
    if ($addNewObj["status"] == "success") {
        redirect($_SERVER['PHP_SELF']);
        swalToast($addNewObj["status"], $addNewObj["message"]);
    } else {
        swalToast($addNewObj["status"], $addNewObj["message"]);
    }
}

if (isset($_POST["editdata"])) {
    //console($_POST);
    $editNewObj = updateDataBranches($_POST);
    if ($editNewObj["status"] == "success") {
        redirect($_SERVER['PHP_SELF']);
        swalToast($editNewObj["status"], $editNewObj["message"]);
    } else {
        swalToast($editNewObj["status"], $editNewObj["message"]);
    }
}

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}
?>
<style>
    td.font-bold.bg-alter {
        background: #afc1d2;
    }

    td.bg-grey.text-white {
        background: #003060;
    }
</style>
<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">

                    <ul class="nav nav-tabs border-0 mb-3" id="custom-tabs-two-tab" role="tablist">
                        <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                            <h3 class="card-title">Manage Balance Sheet</h3>
                            <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
                        </li>
                    </ul>

                    <div class="row">
                        <?php
                            $balanceSheet = fetchBalanceSheet();
                            console($balanceSheet);
                        ?>
                    </div>

                    <div class="card card-tabs">
                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="overflow: auto;">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Particulars</th>
                                            <th>April 21</th>
                                            <th>May 21</th>
                                            <th>June 21</th>
                                            <th>July 21</th>
                                            <th>Aug 21</th>
                                            <th>Sept 21</th>
                                            <th>Oct 21</th>
                                            <th>Nov 21</th>
                                            <th>Dec 21</th>
                                            <th>Jan 21</th>
                                            <th>Feb 21</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="font-bold text-lg">EQUITY AND LIABILITY</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold bg-alter">Shareholder's Fund :</td>
                                            <td class="font-bold bg-alter">950.52</td>
                                            <td class="font-bold bg-alter">950.52</td>
                                            <td class="font-bold bg-alter">950.52</td>
                                            <td class="font-bold bg-alter">950.52</td>
                                            <td class="font-bold bg-alter">950.52</td>
                                            <td class="font-bold bg-alter">950.52</td>
                                            <td class="font-bold bg-alter">950.52</td>
                                            <td class="font-bold bg-alter">950.52</td>
                                            <td class="font-bold bg-alter">950.52</td>
                                            <td class="font-bold bg-alter">950.52</td>
                                            <td class="font-bold bg-alter">950.52</td>
                                        </tr>
                                        <tr>
                                            <td>Share Capital</td>
                                            <td>200.00</td>
                                            <td>200.00</td>
                                            <td>200.00</td>
                                            <td>200.00</td>
                                            <td>200.00</td>
                                            <td>200.00</td>
                                            <td>200.00</td>
                                            <td>200.00</td>
                                            <td>200.00</td>
                                            <td>200.00</td>
                                            <td>200.00</td>
                                        </tr>
                                        <tr>
                                            <td>Reverse and Supplies</td>
                                            <td>750.05</td>
                                            <td>750.05</td>
                                            <td>750.05</td>
                                            <td>750.05</td>
                                            <td>750.05</td>
                                            <td>750.05</td>
                                            <td>750.05</td>
                                            <td>750.05</td>
                                            <td>750.05</td>
                                            <td>750.05</td>
                                            <td>750.05</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold bg-alter">Non-current liabilities :</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                        </tr>
                                        <tr>
                                            <td>Long Terms Borrowings</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Deferred Tax Liability</td>
                                            <td>2.02</td>
                                            <td>2.02</td>
                                            <td>2.02</td>
                                            <td>2.02</td>
                                            <td>2.02</td>
                                            <td>2.02</td>
                                            <td>2.02</td>
                                            <td>2.02</td>
                                            <td>2.02</td>
                                            <td>2.02</td>
                                            <td>2.02</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold bg-alter">Current liabilities :</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                            <td class="font-bold bg-alter">2.02</td>
                                        </tr>
                                        <tr>
                                            <td>Short Term Borrowings</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Trade payables</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                        </tr>
                                        <tr>
                                            <td>Short Term Provisions</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                        </tr>
                                        <tr>
                                            <td>Other Current Liability</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                            <td>39.96</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold bg-alter">Total :</td>
                                            <td class="font-bold bg-alter">1,168.77</td>
                                            <td class="font-bold bg-alter">1,168.77</td>
                                            <td class="font-bold bg-alter">1,168.77</td>
                                            <td class="font-bold bg-alter">1,168.77</td>
                                            <td class="font-bold bg-alter">1,168.77</td>
                                            <td class="font-bold bg-alter">1,168.77</td>
                                            <td class="font-bold bg-alter">1,168.77</td>
                                            <td class="font-bold bg-alter">1,168.77</td>
                                            <td class="font-bold bg-alter">1,168.77</td>
                                            <td class="font-bold bg-alter">1,168.77</td>
                                            <td class="font-bold bg-alter">1,168.77</td>
                                        </tr>
                                    </tbody>
                                    <tbody>
                                        <tr>
                                            <td class="font-bold text-lg">ASSETS</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold bg-alter">Non-current assets :</td>
                                            <td class="font-bold bg-alter">254.27</td>
                                            <td class="font-bold bg-alter">254.27</td>
                                            <td class="font-bold bg-alter">254.27</td>
                                            <td class="font-bold bg-alter">254.27</td>
                                            <td class="font-bold bg-alter">254.27</td>
                                            <td class="font-bold bg-alter">254.27</td>
                                            <td class="font-bold bg-alter">254.27</td>
                                            <td class="font-bold bg-alter">254.27</td>
                                            <td class="font-bold bg-alter">254.27</td>
                                            <td class="font-bold bg-alter">254.27</td>
                                            <td class="font-bold bg-alter">254.27</td>
                                        </tr>
                                        <tr>
                                            <td>Fixed Assets</td>
                                            <td>71.65</td>
                                            <td>71.65</td>
                                            <td>71.65</td>
                                            <td>71.65</td>
                                            <td>71.65</td>
                                            <td>71.65</td>
                                            <td>71.65</td>
                                            <td>71.65</td>
                                            <td>71.65</td>
                                            <td>71.65</td>
                                            <td>71.65</td>
                                        </tr>
                                        <tr>
                                            <td>Long term loans and advances</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Long term loans and advances</td>
                                            <td>182.62</td>
                                            <td>182.62</td>
                                            <td>182.62</td>
                                            <td>182.62</td>
                                            <td>182.62</td>
                                            <td>182.62</td>
                                            <td>182.62</td>
                                            <td>182.62</td>
                                            <td>182.62</td>
                                            <td>182.62</td>
                                            <td>182.62</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold bg-alter">Current assets :</td>
                                            <td class="font-bold bg-alter">914.50</td>
                                            <td class="font-bold bg-alter">914.50</td>
                                            <td class="font-bold bg-alter">914.50</td>
                                            <td class="font-bold bg-alter">914.50</td>
                                            <td class="font-bold bg-alter">914.50</td>
                                            <td class="font-bold bg-alter">914.50</td>
                                            <td class="font-bold bg-alter">914.50</td>
                                            <td class="font-bold bg-alter">914.50</td>
                                            <td class="font-bold bg-alter">914.50</td>
                                            <td class="font-bold bg-alter">914.50</td>
                                            <td class="font-bold bg-alter">914.50</td>
                                        </tr>
                                        <tr>
                                            <td>Stock in Hand</td>
                                            <td>9.61</td>
                                            <td>9.61</td>
                                            <td>9.61</td>
                                            <td>9.61</td>
                                            <td>9.61</td>
                                            <td>9.61</td>
                                            <td>9.61</td>
                                            <td>9.61</td>
                                            <td>9.61</td>
                                            <td>9.61</td>
                                            <td>9.61</td>
                                        </tr>
                                        <tr>
                                            <td>Trade receivables</td>
                                            <td>802.38</td>
                                            <td>802.38</td>
                                            <td>802.38</td>
                                            <td>802.38</td>
                                            <td>802.38</td>
                                            <td>802.38</td>
                                            <td>802.38</td>
                                            <td>802.38</td>
                                            <td>802.38</td>
                                            <td>802.38</td>
                                            <td>802.38</td>
                                        </tr>
                                        <tr>
                                            <td>Cash and cash equivalents</td>
                                            <td>-26.80</td>
                                            <td>-26.80</td>
                                            <td>-26.80</td>
                                            <td>-26.80</td>
                                            <td>-26.80</td>
                                            <td>-26.80</td>
                                            <td>-26.80</td>
                                            <td>-26.80</td>
                                            <td>-26.80</td>
                                            <td>-26.80</td>
                                            <td>-26.80</td>
                                        </tr>
                                        <tr>
                                            <td>Current Investments</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Other Current Assets</td>
                                            <td>30.19</td>
                                            <td>30.19</td>
                                            <td>30.19</td>
                                            <td>30.19</td>
                                            <td>30.19</td>
                                            <td>30.19</td>
                                            <td>30.19</td>
                                            <td>30.19</td>
                                            <td>30.19</td>
                                            <td>30.19</td>
                                            <td>30.19</td>
                                        </tr>
                                        <tr>
                                            <td>Short Term Loans and Advances</td>
                                            <td>99.12</td>
                                            <td>99.12</td>
                                            <td>99.12</td>
                                            <td>99.12</td>
                                            <td>99.12</td>
                                            <td>99.12</td>
                                            <td>99.12</td>
                                            <td>99.12</td>
                                            <td>99.12</td>
                                            <td>99.12</td>
                                            <td>99.12</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold bg-alter">Total :</td>
                                            <td class="font-bold bg-alter">1,168.77 </td>
                                            <td class="font-bold bg-alter">1,168.77 </td>
                                            <td class="font-bold bg-alter">1,168.77 </td>
                                            <td class="font-bold bg-alter">1,168.77 </td>
                                            <td class="font-bold bg-alter">1,168.77 </td>
                                            <td class="font-bold bg-alter">1,168.77 </td>
                                            <td class="font-bold bg-alter">1,168.77 </td>
                                            <td class="font-bold bg-alter">1,168.77 </td>
                                            <td class="font-bold bg-alter">1,168.77 </td>
                                            <td class="font-bold bg-alter">1,168.77 </td>
                                            <td class="font-bold bg-alter">1,168.77 </td>
                                        </tr>
                                        <tr>
                                            <td class="bg-grey text-white">Net Worth</td>
                                            <td class="bg-grey text-white">950.09</td>
                                            <td>950.09</td>
                                            <td class="bg-grey text-white">950.09</td>
                                            <td>950.09</td>
                                            <td class="bg-grey text-white">950.09</td>
                                            <td>950.09</td>
                                            <td class="bg-grey text-white">950.09</td>
                                            <td>950.09</td>
                                            <td class="bg-grey text-white">950.09</td>
                                            <td>950.09</td>
                                            <td class="bg-grey text-white">950.09</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--  -->


                            <!---------------------------------Table settings Model Start--------------------------------->

                            <div class="modal" id="myModal2">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Table Column Settings</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                                            <input type="hidden" name="tablename" value="<?= TBL_COMPANY_ADMIN_TABLESETTINGS; ?>" />
                                            <input type="hidden" name="pageTableName" value="ERP_BRANCHES" />
                                            <div class="modal-body">
                                                <div id="dropdownframe"></div>
                                                <div id="main2">
                                                    <table>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                                Branches Code</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                                Branches Name</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                GSTIN</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox10" value="4" />
                                                                Address</td>
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
        <!-- /.row -->
</div>
</section>
<!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->
<!-- For Pegination------->
<form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                    echo  $_REQUEST['pageNo'];
                                                } ?>">
</form>
<!-- End Pegination from------->

<?php
include("common/footer.php");
?>
<script>
    $('.form-control').on('keyup', function() {
        $(this).parent().children('.error').hide()
    });
    $(".add_data").click(function() {
        var data = this.value;
        $("#createdata").val(data);
        let flag = 1;
        if (data == 'add_post') {
            if ($("#branch_gstin").val() == "") {
                $(".branch_gstin").show();
                $(".branch_gstin").html("GSTIN  is requried.");
                flag++;
            } else {
                $(".branch_gstin").hide();
                $(".branch_gstin").html("");
            }
            if ($("#branch_name").val() == "") {
                $(".branch_name").show();
                $(".branch_name").html(" Trade name is requried.");
                flag++;
            } else {
                $(".branch_name").hide();
                $(".branch_name").html("");
            }
            if ($("#con_business").val() == "") {
                $(".con_business").show();
                $(".con_business").html("Constitution of Business is requried.");
                flag++;
            } else {
                $(".con_business").hide();
                $(".con_business").html("");
            }
            if ($("#build_no").val() == "") {
                $(".build_no").show();
                $(".build_no").html("Build number is requried.");
                flag++;
            } else {
                $(".build_no").hide();
                $(".build_no").html("");
            }
            if ($("#flat_no").val() == "") {
                $(".flat_no").show();
                $(".flat_no").html("Flat number is requried.");
                flag++;
            } else {
                $(".flat_no").hide();
                $(".flat_no").html("");
            }
            if ($("#street_name").val() == "") {
                $(".street_name").show();
                $(".street_name").html(" is requried.");
                flag++;
            } else {
                $(".street_name").hide();
                $(".street_name").html("");
            }
            if ($("#pincode").val() == "") {
                $(".pincode").show();
                $(".pincode").html("pincode is requried.");
                flag++;
            } else {
                $(".pincode").hide();
                $(".pincode").html("");
            }
            if ($("#location").val() == "") {
                $(".location").show();
                $(".location").html("location is requried.");
                flag++;
            } else {
                $(".location").hide();
                $(".location").html("");
            }
            if ($("#city").val() == "") {
                $(".city").show();
                $(".city").html("city is requried.");
                flag++;
            } else {
                $(".city").hide();
                $(".city").html("");
            }
            if ($("#district").val() == "") {
                $(".district").show();
                $(".district").html("district is requried.");
                flag++;
            } else {
                $(".district").hide();
                $(".district").html("");
            }
            if ($("#state").val() == "") {
                $(".state").show();
                $(".state").html("state is requried.");
                flag++;
            } else {
                $(".state").hide();
                $(".state").html("");
            }
            if ($("#adminName").val() == "") {
                $(".adminName").show();
                $(".adminName").html("username is requried.");
                flag++;
            } else {
                $(".adminName").hide();
                $(".adminName").html("");
            }
            var Regex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
            if ($("#adminEmail").val() == "") {
                $(".adminEmail").show();
                $(".adminEmail").html("Email is requried.");
                flag++;
            } else {
                if ($("#adminEmail").val().match(Regex)) {
                    console.log($("#adminEmail").val())
                    $(".adminEmail").show();
                    $(".adminEmail").html("");
                    flag++;
                } else {
                    console.log("1")
                    $(".adminEmail").show();
                    $(".adminEmail").html("Enter a valid email.");
                }
            }
            if ($("#adminPhone").val() == "") {
                $(".adminPhone").show();
                $(".adminPhone").html("Phone number is requried.");
                flag++;
            } else {
                $(".adminPhone").hide();
                $(".adminPhone").html("");
            }
            if ($("#adminPassword").val() == "") {
                $(".adminPassword").show();
                $(".adminPassword").html("Password is requried.");
                flag++;
            } else {
                $(".adminPassword").hide();
                $(".adminPassword").html("");
            }
        }
        if (flag != 1) {
            return false;
        } else {
            $("#add_frm").submit();
        }

    });
    $(".edit_data").click(function() {
        var data = this.value;
        $("#editdata").val(data);
        alert(data);
        //$( "#edit_frm" ).submit();
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


    $(document).ready(function() {


        $(document).on("keyup paste keydown", "#branch_gstin", function() {
            var branch_gstin = $("#branch_gstin").val();
            var leng_gstin = branch_gstin.length;
            if (leng_gstin > 14) {
                $("#vendorPanNo").val(branch_gstin.substr(2, 10));

                $.ajax({
                    type: "GET",
                    url: `ajaxs/ajax-gst-details.php?gstin=${branch_gstin}`,
                    beforeSend: function() {
                        $('#gstinloder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                    },
                    success: function(response) {

                        $('#gstinloder').html("");
                        responseObj = JSON.parse(response);
                        if (responseObj["status"] == "success") {
                            responseData = responseObj["data"];

                            console.log(responseData);

                            $("#branch_name").val(responseData["tradeNam"]);
                            $("#con_business").val(responseData["ctb"]);
                            $("#build_no").val(responseData['pradr']['addr']['bno']);
                            $("#flat_no").val(responseData['pradr']['addr']['flno']);
                            $("#street_name").val(responseData['pradr']['addr']['st']);
                            $("#pincode").val(responseData['pradr']['addr']['pncd']);
                            $("#location").val(responseData['pradr']['addr']['loc']);
                            $("#city").val(responseData['pradr']['addr']['city']);
                            $("#district").val(responseData['pradr']['addr']['dst']);
                            $("#state").val(responseData['pradr']['addr']['stcd']);

                            //$("#status").val(responseData["sts"]);

                        } else {
                            let Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            Toast.fire({
                                icon: `warning`,
                                title: `&nbsp;Invalid GSTIN No!`
                            });
                        }
                    }
                });
            }

        });


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

    var inputs = document.getElementsByClassName("form-control");
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