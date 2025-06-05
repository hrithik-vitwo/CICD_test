<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-branch-pr-controller.php");

// console( $_SESSION["logedBranchAdminInfo"]["adminId"]);
// exit();

if (isset($_POST["visit"])) {
    $newStatusObj = VisitBranches($_POST);
    redirect(BRANCH_URL);
}




if (isset($_POST["add-table-settings"])) {

    // console($_POST);
    // exit();

    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    // console($editDataObj);
    // exit();
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩  
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩  

$BranchPrObj = new BranchPr();

if (isset($_POST['addNewSOFormSubmitBtn'])) {
    //console($_SESSION);
    $addBranchPr = $BranchPrObj->addBranchPr($_POST);

    swalToast($addBranchPr["status"], $addBranchPr["message"]);
}
if (isset($_POST['addNewVendorList'])) {
    //console($_SESSION);
    $addBranchPr = $BranchPrObj->addVendorList($_POST);

    swalToast($addBranchPr["status"], $addBranchPr["message"]);
}
if (isset($_POST['addNewOtherVendor'])) {
    //console($_SESSION);
    $addBranchPr = $BranchPrObj->addOtherVendorList($_POST);

    swalToast($addBranchPr["status"], $addBranchPr["message"]);
}

if (isset($_GET['vendor-delete'])) {
    $id = $_GET['vendor-delete'];
    $addBranchPr = $BranchPrObj->deleteRfqVendor($_GET, $id);

    swalToast($addBranchPr["status"], $addBranchPr["message"]);
}

if (isset($_POST['addNewRFQFormSubmitBtn'])) {

    $addBranchPr = $BranchPrObj->addBranchRFQ($_POST);

    if ($addBranchPr["status"] == "success") {
        swalToast($addBranchPr["status"], $addBranchPr["message"], $_SERVER['PHP_SELF']);
    } else {
        swalToast($addBranchPr["status"], $addBranchPr["message"]);
    }
}



?>

<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
    .matrix-card .row:nth-child(1):hover {

        pointer-events: none;

    }

    .matrix-card .row:hover {

        border-radius: 0 0 10px 10px;

    }

    .matrix-card .row:nth-child(1) {

        background: #fff;

    }

    .matrix-card .row .col {

        display: flex;

        align-items: center;

    }

    .matrix-accordion button {

        color: #fff;

        border-radius: 15px !important;

        margin: 20px 0;

    }

    .accordion-button:not(.collapsed) {

        color: #fff;

    }

    .accordion-button::after {

        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");

    }

    .accordion-button:not(.collapsed)::after {

        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");

    }

    .accordion-item {

        border-radius: 15px !important;

        margin-bottom: 2em;

    }

    .info-h4 {

        font-size: 20px;

        font-weight: 600;

        color: #003060;

        padding: 0px 10px;

    }

    .rfq-modal .tab-content li a span,
    .rfq-modal .tab-content li a i {

        font-weight: 600 !important;

    }


    .float-add-btn {

        display: flex !important;

    }

    .items-search-btn {

        display: flex;

        align-items: center;

        gap: 5px;

        border: 1px solid #fff !important;

    }

    .card.existing-vendor .card-header,
    .card.other-vendor .card-header {

        display: flex;

        justify-content: space-between;

    }

    .card.existing-vendor a.btn-primary,
    .card.other-vendor a.btn-primary {

        padding: 3px 12px;

        margin-right: 10px;

        float: right;

        border: 1px solid #fff !important;

    }



    .card-body::after,
    .card-footer::after,
    .card-header::after {

        display: none;

    }

    .row.rfq-vendor-list-row-value {

        border-bottom: 1px solid #fff;

        margin: 0;

        align-items: center;

    }

    .row.rfq-vendor-list-row {

        margin: 0;

        border-bottom: 1px solid #fff;

        align-items: center;

    }

    .rfq-email-filter-modal .modal-dialog {

        max-width: 650px;

    }


    .date-range-input {
        gap: 13px;
        justify-content: flex-end;
    }

    .row.custom-range-row {
        align-items: center;
    }


    @media (max-width: 575px) {

        .rfq-modal .modal-body {

            padding: 20px !important;

        }

    }

    @media(max-width: 390px) {

        .display-flex-space-between .matrix-btn {

            position: relative;

            top: 10px;

        }

    }
</style>

<?php

try {
    // API endpoint URL
    $url = 'https://console.claimz.in/admin-api/api/employee-ptax';
    // Data to be sent in the request body
    $data = array(
        // 'param1' => 'value1',
        // 'param2' => 'value2'

        'unique_id' => 'ABC890',
        'from' => '2023-01-01',
        'to' => '2023-04-01'
    );
    // Set the HTTP headers for the request
    $headers = array(
        'Content-Type: application/json'
    );
    // Initialize cURL session
    $ch = curl_init($url);
    // Set cURL options
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    // Execute the request and fetch the response
    $response = curl_exec($ch);
    // Close the cURL session
    curl_close($ch);
    // Process the response data
    if ($response === false) {
        // Handle cURL error
    } else {
        $response_data = json_decode($response, true);
        // Process the response data as needed
    }
    //console($response_data['slab']);

    $slab_det = $response_data['slab'];
    //$slab_ids = array_column($slab_det,"slab_id");
    // console($slab_det);
    // console($slab_ids);


    //  $pf_report_data = [];



    //  foreach($response_data['data'] as $data){
    //     $pf_report_data[] = array()

    //     console($data['date']);
    //     console($data['data']);

    //  }



} catch (Exception $e) {
    echo $e;
}

?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <!-- <div class="p-0 pt-1 my-2">
              <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                  <h3 class="card-title">Manage Request For Quotations</h3>
                  <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?pr-creation" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></a>
                </li>
              </ul>
            </div> -->
                    <div class="card card-tabs" style="border-radius: 20px;">
                        <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                            <div class="card-body">
                                <div class="row filter-serach-row">

                                    <!-- <div class="col-lg-10 col-md-10 col-sm-12">
                                        <div class="section serach-input-section">
                                        <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                            <option value="">--Select FY--</option>
                                                          <option value="7" data-start="2022-01-01" data-end="2023-12-31" selected>FY-2022/23</option>
                                                          <option value="6" data-start="2021-01-01" data-end="2022-12-31" >FY-2021/22</option>
                            
                            
                          </select>

                          <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                            <option value="">--Select FY--</option>
                                                          <option value="7" data-start="2022-01-01" data-end="2023-12-31" selected>FY-2022/23</option>
                                                          <option value="6" data-start="2021-01-01" data-end="2022-12-31" >FY-2021/22</option>
                            
                            <option value="customrange" >
                              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                            </option>
                          </select>
                                           
                                        </div>
                                    </div> -->

                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="row custom-range-row">
                                            <div class="col-lg-8 col-md-8 col-sm-12">
                                                <div class="customrange-section">
                                                    <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                                                        <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="">
                                                        <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange">
                                                        <div class="date-range-input d-flex">
                                                            <h6 class="text-xs font-bold">Custom Range</h6>
                                                            <div class="form-input">
                                                                <input type="date" class="form-control" name="from_date" id="from_date" value="2022-01-01" required="">
                                                            </div>
                                                            <div class="form-input">
                                                                <label class="mb-0" for="">TO</label>
                                                                <input type="date" class="form-control" name="to_date" id="to_date" value="2023-12-31" required="">
                                                            </div>
                                                            <button type="submit" class="btn btn-primary float-right waves-effect waves-light" id="rangeid" name="add_date_form">Apply</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
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
                                        </div>

                                    </div>

                                    <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Filter
                                                        Payroll</h5>

                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                            <input type="text" name="keyword" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php if (isset($_REQUEST['keyword'])) {
                                                                                                                                                                                    echo $_REQUEST['keyword'];
                                                                                                                                                                                } ?>">
                                                        </div>
                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                            <select id="pr" name="pr" class="fld form-control m-input">
                                                                <option value="">ALL</option>
                                                                <?php

                                                                $pr_query = "SELECT * FROM erp_branch_purchase_request WHERE company_id = '$company_id' AND branch_id = '$branch_id' AND location_id = '$location_id'";
                                                                $pr_query_list = queryGet($pr_query, true);
                                                                $pr_list = $pr_query_list['data'];
                                                                foreach ($pr_list as $pr_row) {
                                                                ?>
                                                                    <option value="<?= $pr_row['purchaseRequestId'] ?>" <?php if (isset($_GET['prid']) && $_GET['prid'] == $pr_row['purchaseRequestId']) echo ("selected"); ?>><?= $pr_row['prCode'] ?></option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>

                                                        <!-- <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <select name="vendor_status_s" id="vendor_status_s" class="fld form-control" style="appearance: auto;">
                                  <option value=""> Status </option>
                                  <option value="active" <?php if (isset($_REQUEST['vendor_status_s']) && 'active' == $_REQUEST['vendor_status_s']) {
                                                                echo 'selected';
                                                            } ?>>Active
                                  </option>
                                  <option value="inactive" <?php if (isset($_REQUEST['vendor_status_s']) && 'inactive' == $_REQUEST['vendor_status_s']) {
                                                                echo 'selected';
                                                            } ?>>Inactive
                                  </option>
                                  <option value="draft" <?php if (isset($_REQUEST['vendor_status_s']) && 'draft' == $_REQUEST['vendor_status_s']) {
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
                                                    <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync fa-spin"></i>Reset</a>
                                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                                                        Search</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                        </form>

                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">

                                <table class="table defaultDataTable table-hover text-nowrap p-0 m-0">
                                    <thead>
                                        <tr class="alert-light">


                                            <th>Slab</th>
                                            <?php


                                            foreach ($response_data['data'] as $data) {
                                            ?>


                                                <th><?= date('M', strtotime($data['date']['date'])) ?></th>

                                            <?php
                                            }
                                            ?>










                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        foreach ($slab_det as $slab) {
                                            $tmp_slab_id = $slab['slab_id'];
                                        ?>
                                            <tr>
                                                <td><?= $slab['lower_limit'] . ">=" . $slab['upper_limit'] ?></td>
                                                <?php
                                                foreach ($response_data['data'] as $month_data) {
                                                    $slab_count = 0;
                                                    foreach ($month_data['data'] as $month_slab_data) {
                                                        if ($month_slab_data['slab_id'] == $tmp_slab_id) {
                                                            $slab_count++;
                                                        }
                                                    }
                                                ?>
                                                    <td><?= $slab_count ?></td>
                                                <?php
                                                }
                                                ?>
                                            </tr>
                                        <?php
                                        }
                                        ?>

                                    </tbody>

                                    <tbody>
                                        <tr>
                                            <td colspan="9">
                                                <!-- Start .pagination -->



                                                <!-- End .pagination -->
                                            </td>
                                        </tr>
                                    </tbody>


                                    <!-- right modal end here  -->

                                </table>
                                <!-- <?php

                                        ?> -->
                                <!-- <table class="table defaultDataTable table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <td>

                                                </td>
                                            </tr>
                                        </thead>
                                    </table> -->
                                <?php
                                //}
                                ?>


                            </div>




                            <!---------------------------------Table settings Model Start--------------------------------->
                            <!-- <div class="modal" id="myModal2">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Table Column Settings</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                                            <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                            <input type="hidden" name="pageTableName" value="ERP_PF" />
                                            <div class="modal-body">
                                                <div id="dropdownframe"></div>
                                                <div id="main2">
                                                    <table>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                            UAN</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                            Employee Name </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                           Gross</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                                           EPF Wages</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                                            EPF Contribution (12%)</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                                            EPF Contribution (8.33%)</td>
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
                            </div> -->
                            <!---------------------------------Table Model End--------------------------------->
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
</section>
</div>
<!-- End Pegination from------->

<!-- <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                    echo  $_REQUEST['pageNo'];
                                                } ?>">
</form> -->


<?php
require_once("../common/footer.php");
?>
<script>
    $(document).on("click", ".remove_row", function() {

        var value = $(this).data('value');

        for (let l = 0; l < test.length; l++) {
            var array_each = test[l].split("|");
            if (array_each[0].includes(value) == true) {
                test.splice(l, 1);
            }
        }
        $(this).parent().parent().remove();
    })


    $(document).on("click", ".remove_row_other", function() {
        $(this).parent().parent().remove();
    })

    function rm() {
        $(event.target).closest("<div class='row others-vendor'>").remove();
    }


    function addMultiQty(id) {
        let addressRandNo = Math.ceil(Math.random() * 100000);



        $(`.modal-add-row_${id}`).append(`
    <div class="modal-body pl-3 pr-3" style="overflow: hidden;">
    <div class="row" style="align-items: end;">
                          <div class="col-lg-5 col-md-5 col-sm-12">
                            <div class="form-input">
                              <label for="date">Vendor Name</label>
                              <input type="text" id="eachName_${addressRandNo}" name="OthersVendor[${addressRandNo}][name]" class="form-control each_name" placeholder="Vendor Name" />
                            </div>
                          </div>
                            <div class="col-lg-5 col-md-5 col-sm-12">
                              <div class="form-input">
                                <label for="date">Vendor Email</label>
                                <input type="text" id="eachEmail_${addressRandNo}" name="OthersVendor[${addressRandNo}][email]" class="form-control each_email" placeholder="Vendor Email" />
                              </div>
                            </div>
                              <div class="col-lg-2 col-md-2 text-center remove_row_other" data-value="${addressRandNo}">
                                <a class="btn btn-danger" type="button">
                                  <i class="fa fa-minus"></i></a>
                              </div>
                            </div>
                            </div>`);
    }
</script>
<script>
    $(document).ready(function() {
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
        // customers ********************************
        function loadCustomers() {
            $.ajax({
                type: "GET",
                url: `ajaxs/pr/ajax-customers.php`,
                beforeSend: function() {
                    $("#customerDropDown").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    $("#customerDropDown").html(response);
                }
            });
        }
        loadCustomers();
        // get customer details by id
        $("#customerDropDown").on("change", function() {
            let itemId = $(this).val();
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-customers-list.php`,
                data: {
                    act: "listItem",
                    itemId
                },
                beforeSend: function() {
                    $("#customerInfo").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    console.log(response);
                    $("#customerInfo").html(response);
                }
            });
        });
        // **************************************
        function loadItems() {
            $.ajax({
                type: "GET",
                url: `ajaxs/pr/ajax-items.php`,
                beforeSend: function() {
                    $("#itemsDropDown").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    $("#itemsDropDown").html(response);
                }
            });
        }
        loadItems();
        // get item details by id
        $("#itemsDropDown").on("change", function() {
            let itemId = $(this).val();
            $.ajax({
                type: "GET",
                url: `ajaxs/pr/ajax-items-list.php`,
                data: {
                    act: "listItem",
                    itemId
                },
                beforeSend: function() {
                    //  $("#itemsTable").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    console.log(response);
                    $("#itemsTable").append(response);
                }
            });
        });
        $(document).on("click", ".delItemBtn", function() {
            // let id = ($(this).attr("id")).split("_")[1];
            // $(`#delItemRowBtn_${id}`).remove();
            $(this).parent().parent().remove();
        })
        $(document).on('submit', '#addNewItemForm', function(event) {
            event.preventDefault();
            let formData = $("#addNewItemsForm").serialize();
            $.ajax({
                type: "POST",
                url: `ajaxs/so/ajax-items.php`,
                data: formData,
                beforeSend: function() {
                    $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
                    $("#addNewItemsFormSubmitBtn").html(
                        '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...'
                    );
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
    })

    function compare_dates() {
        let closing_date = $("#closingDate").val();
        let exp_date = $("#expDate").val();
        if (closing_date > exp_date && closing_date < date) {
            $("#error").html(`<p id="error">closing date cannot be greater than expected date</p>`);
            document.getElementById("addNewOtherVendorId").disabled = true;
        } else {
            $("#error").html('');
            document.getElementById("addNewOtherVendorId").disabled = false;
        }

    }
    $("#closingDate").change(function() {

        compare_dates();

    });
</script>

<script src="<?= BASE_URL; ?>public/validations/rfqValidation.js"></script>