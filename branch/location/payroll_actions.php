<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-payroll-controller.php");

require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");


$accountingControllerObj = new Accounting();
// console( $_SESSION["logedBranchAdminInfo"]["adminId"]);
// exit();

if (isset($_POST['submit-payroll'])) {

    $submit = submit_payroll($_POST);
    swalToast($submit["status"], $submit["message"]);
}

if (isset($_POST['apikey'])) {
    // console($_POST);
    $submit_api = submit_api($_POST);
    swalToast($submit_api["status"], $submit_api["message"], BASE_URL . "branch/location/payroll.php?payroll");
}

if (isset($_POST['drop_val'])) {
    $year = $_POST['year'];
    $month = $_POST['month'];
} else {
    $year = date('Y');
    $month = date('m');
}

// echo $month;

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



if (isset($_POST['manual_submit'])) {

    // console($_POST);
    // exit();

    $submit = manual_payroll($_POST);
    swalToast($submit["status"], $submit["message"],"payroll.php");
}
if (isset($_POST['accpost'])) {
    $postingData = $_POST;
    $id = $postingData["payroll_main_id"];
    $verusql = queryGet("SELECT * FROM `erp_payroll_main` WHERE payroll_main_id=$id AND `company_id`=$company_id AND `location_id`=$location_id AND `branch_id`=$branch_id ORDER BY `payroll_year`, `payroll_month`");

    if ($verusql['status'] == 'success' && $verusql['data']['acconting_status'] == 'Pending') {


        // console($postingData);

        // exit();

        $dateString = $postingData["payroll_month"] . '-' . $postingData["payroll_year"];
        $dateObj = DateTime::createFromFormat('m-Y', $dateString);
        $monthYear = $dateObj->format('F Y');

        $sum_pf_employee = $postingData["sum_pf_employee"];
        $sum_pf_employeer = $postingData["sum_pf_employeer"] + $postingData["sum_pf_admin"];
        $sum_ptax = $postingData["sum_ptax"];
        $sum_esi_employee = $postingData["sum_esi_employee"];
        $sum_esi_employeer = $postingData["sum_esi_employeer"];
        $sum_tds = $postingData["sum_tds"];
        $sum_gross = $postingData["sum_gross"];

        $PostingInputData = [

            "BasicDetails" => [

                "documentNo" => $postingData["documentNo"], // Invoice Doc Number

                "documentDate" => date("Y-m-d"), // Invoice number

                "postingDate" => date("Y-m-d"), // current date

                "reference" => $postingData["documentNo"], // grn code

                "remarks" => "Payroll Posting for - " . $monthYear,

                "journalEntryReference" => "payroll"

            ],
            "payrollDetails" => [

                "sum_pf_employee" => $sum_pf_employee,
                "sum_pf_employeer" => $sum_pf_employeer,
                "sum_esi_employee" => $sum_esi_employee,
                "sum_esi_employeer" => $sum_esi_employeer,
                "sum_ptax" => $sum_ptax,
                "sum_tds" => $sum_tds,
                "sum_gross" => $sum_gross

            ]

        ];

        //console($PostingInputData);
        $ivPostingObj = $accountingControllerObj->payrollAccountingPosting($PostingInputData, "payroll", $id);
        if ($ivPostingObj['status'] == "success") {
            $queryObj = queryUpdate('UPDATE `erp_payroll_main` SET `journal_id`=' . $ivPostingObj["journalId"] . ', `acconting_status`="Posted" WHERE `payroll_main_id`=' . $id);
        }
        // console($ivPostingObj);

        swalAlert($ivPostingObj["status"], ucfirst($ivPostingObj["status"]), $ivPostingObj["message"],"payroll.php");
    } else {
        swalAlert('warning', 'Warning', 'Document Already Posted.');
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

    .goods-flex-btn form {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .filter-list a.active {
        background-color: #003060;
        color: #fff;
    }

    .customrange-section {
        position: absolute;
        bottom: 20px;
        right: 270px;
    }

    .vendor-gstin {
        margin: 90px auto;
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
if (isset($_GET['payroll'])) {


    $key_sql = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id`=$location_id");
    //console($key_sql['data']);
    // echo 'okayyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy';
    // echo  $key_sql['data']['emp_api_key'];
    // echo "1233333333333333333333333333333333333";
    echo  $unique_key = $key_sql['data']['emp_api_key'];

    //$transdate = date('m-d-Y', time());

    //echo $transdate;

    // $month = date('m', strtotime($transdate));
    // $year = date('Y', strtotime($transdate));

    // API endpoint URL
    $url = 'https://console.claimz.in/admin-api/api/costcenter';

    // Data to be sent in the request body
    $data = array(
        // 'param1' => 'value1',
        // 'param2' => 'value2'

        'unique_id' => $unique_key,
        'year' => $year,
        'month' => $month
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
    //  console($response_data);


    if (empty($response_data)) {
    } else {
    }

    // console($response);

?>



    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">

                        <div class="filter-list">
                            <a href="payroll.php" class="btn  waves-effect waves-light"><i class="fa fa-stream mr-2 "></i>Payroll</a>
                            <!-- <a href="payroll.php?payroll" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active"></i>Claimz Report</a> -->
                            <a href="payroll.php?manual" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>Manual Payroll Form</a>
                        </div>
                        <?php
                        $select_api = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id`=$location_id ");
                        // console($select_api);
                        if (!empty($select_api['data']['emp_api_key']) || $select_api['data']['emp_api_key'] != null) {

                        ?>
                            <div class="card card-tabs" style="border-radius: 20px;">

                                <div class="card-body">
                                    <div class="row filter-serach-row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="row custom-range-row">
                                                <!-- <div class="col-lg-2 col-md-2 col-sm-12">
                                                    <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position: absolute; z-index: 999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                                </div> -->
                                                <div class="col-lg-10 col-md-10 col-sm-12">
                                                    <div class="section serach-input-section">
                                                        <input type="text" id="myInput" placeholder="" class="field form-control" />
                                                        <div class="icons-container">
                                                            <div class="icon-search">
                                                                <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="customrange-section">
                                                        <form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" class="custom-Range" id="date_form" name="date_form">
                                                            <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="">
                                                            <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange">
                                                            <div class="date-range-input d-flex">
                                                                <div class="form-input">
                                                                    <label for="selectMonth" class="mb-0">Select Month</label>
                                                                    <select name="month" id="" class="form-control">
                                                                        <option value="01" <?php if ($month == '01') {
                                                                                                echo "selected";
                                                                                            } ?>>January</option>
                                                                        <option value="02" <?php if ($month == '02') {
                                                                                                echo "selected";
                                                                                            } ?>>February</option>
                                                                        <option value="03" <?php if ($month == '03') {
                                                                                                echo "selected";
                                                                                            } ?>>March</option>
                                                                        <option value="04" <?php if ($month == '04') {
                                                                                                echo "selected";
                                                                                            } ?>>April</option>

                                                                        <option value="05" <?php if ($month == '05') {
                                                                                                echo "selected";
                                                                                            } ?>>May</option>
                                                                        <option value="06" <?php if ($month == '06') {
                                                                                                echo "selected";
                                                                                            } ?>>June</option>
                                                                        <option value="07" <?php if ($month == '07') {
                                                                                                echo "selected";
                                                                                            } ?>>July</option>
                                                                        <option value="08" <?php if ($month == '08') {
                                                                                                echo "selected";
                                                                                            } ?>>August</option>

                                                                        <option value="09" <?php if ($month == '09') {
                                                                                                echo "selected";
                                                                                            } ?>>September</option>
                                                                        <option value="10" <?php if ($month == '10') {
                                                                                                echo "selected";
                                                                                            } ?>>October</option>
                                                                        <option value="11" <?php if ($month == '11') {
                                                                                                echo "selected";
                                                                                            } ?>>November</option>
                                                                        <option value="12" <?php if ($month == '12') {
                                                                                                echo "selected";
                                                                                            } ?>>December</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-input">
                                                                    <label for="selectYear" class="mb-0">Select Year</label>
                                                                    <select name="year" id="" class="form-control">
                                                                        <?php for ($i = $year; $i >= 2020; $i--) { ?>
                                                                            <option value="<?= $i; ?>" <?php if ($year == $i) {
                                                                                                            echo "selected";
                                                                                                        } ?>><?= $i; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <button type="submit" class="btn btn-primary float-right waves-effect waves-light" id="rangeid" name="add_date_form">Apply</button>
                                                            </div>
                                                        </form>
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
                                                                                                                                                                                        $_REQUEST['keyword'];
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

                                        <div class="tab-content" id="custom-tabs-two-tabContent">
                                            <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                                <?php
                                                $cond = '';
                                                global $company_id;
                                                global $branch_id;
                                                global $location_id;
                                                // $sts = " AND `vendor_status` !='deleted'";
                                                // if (isset($_REQUEST['vendor_status_s']) && $_REQUEST['vendor_status_s'] != '') {
                                                //   $sts = ' AND vendor_status="' . $_REQUEST['vendor_status_s'] . '"';
                                                // }

                                                if (isset($_GET['prid']) && $_GET['prid'] != '') {
                                                    $cond .= " AND rfq.prId = '" . $_GET['prid'] . "'";
                                                }

                                                if (isset($_REQUEST['pr']) && $_REQUEST['pr'] != '') {
                                                    $cond .= " AND rfq.prId = '" . $_REQUEST['pr'] . "'";
                                                }

                                                if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                                    $cond .= " AND rfq.created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                                }

                                                if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                                    $cond .= " AND (rfq.rfqCode like '%" . $_REQUEST['keyword'] . "%' OR rfq.prCode like '%" . $_REQUEST['keyword'] . "%' OR pr.refNo like '%" . $_REQUEST['keyword'] . "%')";
                                                }

                                                $cnt = $GLOBALS['start'] + 1;
                                                $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_SALARY_LIST", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                                $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                                $settingsCheckbox = unserialize($settingsCh);
                                                // if ($num_list > 0) {


                                                ?>
                                                <form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
                                                    <table class="table defaultDataTable table-hover text-nowrap p-0 m-0">
                                                        <thead>
                                                            <tr class="alert-light">
                                                                <th>#</th>

                                                                <th>Claimz Cost Center</th>


                                                                <th>Map Cost Center</th>

                                                                <th>Gross</th>


                                                                <th> PF Employee</th>


                                                                <th>PF Employer</th>



                                                                <th>PF Admin</th>



                                                                <th>PTax</th>

                                                                <th>ESI</th>








                                                            </tr>
                                                        </thead>


                                                        <input type="hidden" name="submit-payroll">
                                                        <input type="hidden" name="year" value="<?= $year ?>">
                                                        <input type="hidden" name="month" value="<?= $month ?>">
                                                        <tbody>
                                                            <?php
                                                            // console($BranchPrObj->fetchBranchSoListing()['data']);
                                                            $sal = $response_data['data'];
                                                            foreach ($sal as $data) {
                                                                $cc_id = $data['id'];

                                                            ?>


                                                                <tr style="cursor:pointer">
                                                                    <td><?= $cnt++ ?></td>

                                                                    <td><?= $data['cost_center_name'] ?>
                                                                        <input type="hidden" name="payroll[<?= $data['id'] ?>][cost_center_id]" value="<?= $data['id'] ?>">
                                                                    </td>

                                                                    <?php

                                                                    $cc_sql = queryGet("SELECT * FROM `erp_payroll` WHERE `location_id`=$location_id AND `costcenter_id`= $cc_id ORDER BY `payroll_id` DESC");
                                                                    // console($cc_sql['data']);
                                                                    $alpha_cc = queryGet("SELECT * FROM `erp_cost_center` WHERE `location_id`=$location_id", true);
                                                                    // console($alpha_cc);

                                                                    $cc = $cc_sql['data']['alpha_costcenter_id'];

                                                                    ?>

                                                                    <td>
                                                                        <select name="payroll[<?= $data['id'] ?>][map_cc]" class="form-control">
                                                                            <option>map cost center</option>
                                                                            <?php
                                                                            foreach ($alpha_cc['data'] as $cc_data) {

                                                                            ?>
                                                                                <option value="<?= $cc_data['CostCenter_id'] ?>" <?php if ($cc_data['CostCenter_id'] ==  $cc) {
                                                                                                                                        echo "selected";
                                                                                                                                    } ?>><?= $cc_data['CostCenter_code'] . "(" . $cc_data['CostCenter_desc'] . ")" ?></option>
                                                                            <?php
                                                                            }

                                                                            ?>
                                                                        </select>
                                                                    </td>








                                                                    <td><?= $data['gross'] ?>
                                                                        <input type="hidden" name="payroll[<?= $data['id'] ?>][gross]" value="<?= $data['gross'] ?>">
                                                                    </td>



                                                                    <td><?= $data['pf_employee'] ?>
                                                                        <input type="hidden" name="payroll[<?= $data['id'] ?>][pf_employee]" value="<?= $data['pf_employee'] ?>">
                                                                    </td>


                                                                    <td><?= $data['pf_employer'] ?> <input name="payroll[<?= $data['id'] ?>][pf_employer]" type="hidden" value="<?= $data['pf_employer'] ?>">
                                                                    </td>


                                                                    <td><?= $data['pf_admin'] ?>
                                                                        <input type="hidden" name="payroll[<?= $data['id'] ?>][pf_admin]" value="<?= $data['pf_admin'] ?>">
                                                                    </td>


                                                                    <td><?= $data['ptax'] ?>
                                                                        <input type="hidden" name="payroll[<?= $data['id'] ?>][ptax]" value="<?= $data['ptax'] ?>">
                                                                    </td>


                                                                    <td><?= $data['esi_employee'] ?>
                                                                        <input type="hidden" name="payroll[<?= $data['id'] ?>][esi]" value="<?= $data['esi_employee'] ?>">
                                                                    </td>


                                                                </tr>
                                                            <?php
                                                            }
                                                            ?>
                                                        </tbody>



                                                    </table>






                                                    <div class="input-group btn-col">
                                                        <?php
                                                        $year;
                                                        $month;
                                                        $date = date($year . "-" . $month);

                                                        $check = queryGet("SELECT * FROM `erp_payroll` WHERE `payroll_year`=$year AND `payroll_month`= $month AND `company_id`=1");

                                                        if ($check['numRows'] > 0) {



                                                        ?>

                                                            <button type="submit" id="addNewGoodTypesFormSubmitBtn" class="btn btn-primary btnstyle" disabled>Added</button>
                                                        <?php
                                                        } else {

                                                        ?>
                                                            <button type="submit" id="addNewGoodTypesFormSubmitBtn" class="btn btn-primary btnstyle">Add</button>


                                                        <?php
                                                        }

                                                        ?>

                                                    </div>

                                                </form>

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
                                                                <input type="hidden" name="pageTableName" value="ERP_SALARY_LIST" />
                                                                <div class="modal-body">
                                                                    <div id="dropdownframe"></div>
                                                                    <div id="main2">
                                                                        <table>
                                                                            <tr>
                                                                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                                                    Cost Center</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                                                    Gross </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                                    PF</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                                                                    PTax</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                                                                    ESI</td>
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
    </div>
    <!-- End Pegination from------->

    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                        echo  $_REQUEST['pageNo'];
                                                    } ?>">
    </form>



<?php
                        } else {
?>
    <div class="vendor-gstin" id="VerifyGstinBtnDiv">
        <div class="card">
            <div class="card-header">
                <div class="head">
                    <i class="fa fa-user"></i>
                    <h4>API KEY</h4>
                </div>
            </div>
            <form action="<?= basename($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="card-body">
                    <div class="info-vendor-gstin"></div>
                    <div class="form-inline">

                        <label for="">Enter Location Unique API Key</label>
                        <input type="text" class="form-control vendor-gstin-input w-75" name="apikey" id="apikey" placeholder="enter api key">
                        <button class="btn btn-primary verify-btn checkAndVerifyGstinBtn">
                            <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </button>

                    </div>




                </div>
            </form>
        </div>
    </div>

<?php
                        }
                    } else if (isset($_GET['manual'])) {

?>




<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">


                    <div class="filter-list">
                        <a href="payroll.php" class="btn  waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Payroll</a>
                        <!-- <a href="payroll.php?payroll" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>Claimz Report</a> -->
                        <a href="payroll.php?manual" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active"></i>Manual Payroll Form</a>
                    </div>

                    <div class="card card-tabs" style="border-radius: 20px;">

                        <div class="card-body">
                            <div class="row filter-serach-row">


                                <div class="col-lg-12 col-md-12 col-sm-12">

                                    <div class="row custom-range-row">
                                        <div class="col-lg-2 col-md-2 col-sm-12">
                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position: absolute; z-index: 999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        </div>

                                        <div class="col-lg-10 col-md-10 col-sm-12">
                                            <div class="section serach-input-section">
                                                <input type="text" id="myInput" placeholder="" class="field form-control" />
                                                <div class="icons-container">
                                                    <div class="icon-search">
                                                        <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
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

                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">

                                        <form method="post">
                                            <input type="hidden" value="" name="manual_submit">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="row info-form-view" style="row-gap: 17px;">
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label for=""> Month </label>
                                                        <select name="month" class="form-control">
                                                            <option>Select Month</option>
                                                            <option value='1'>January</option>
                                                            <option value='2'>February</option>
                                                            <option value='3'>March</option>
                                                            <option value='4'>April</option>
                                                            <option value='5'>May</option>
                                                            <option value='6'>June</option>
                                                            <option value='7'>July</option>
                                                            <option value='8'>August</option>
                                                            <option value='9'>September</option>
                                                            <option value='10'>October</option>
                                                            <option value='11'>November</option>
                                                            <option value='12'>December</option>

                                                        </select>
                                                    </div>



                                                    <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                                                        <label for=""> Year </label>
                                                        <select name="year" class="form-control">
                                                            <?php for ($i = $year; $i >= 2020; $i--) { ?>
                                                                <option value="<?= $i; ?>" <?php if ($year == $i) {
                                                                                                echo "selected";
                                                                                            } ?>><?= $i; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <table class="table defaultDataTable table-hover text-nowrap p-0 m-0">
                                                <thead>
                                                    <tr class="alert-light">
                                                        <th>Cost Center</th>
                                                        <th>Gross</th>
                                                        <th>PF Employee</th>
                                                        <th>PF Employer</th>
                                                        <th>PF Admin</th>
                                                        <th>ESI Employee</th>
                                                        <th>ESI Employer</th>
                                                        <th>PTax</th>
                                                        <th>TDS</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php
                                                    $alpha_cc = queryGet("SELECT * FROM `erp_cost_center` WHERE `location_id`=$location_id", true);

                                                    foreach ($alpha_cc['data'] as $cc_data) {


                                                    ?>
                                                        <tr style="cursor:pointer">
                                                            <td>
                                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                                    <div class="row info-form-view" style="row-gap: 17px;">
                                                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                                                            <?= $cc_data['CostCenter_code'] . "(" . $cc_data['CostCenter_desc'] . ")"  ?>
                                                                            <input type="hidden" name="costcenter[<?= $cc_data['CostCenter_id'] ?>][costcenter_id]" value="<?= $cc_data['CostCenter_id'] ?>">
                                                                        </div>
                                                            </td>

                                                            <td>
                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                    <input type="text" name="costcenter[<?= $cc_data['CostCenter_id'] ?>][gross_amount]" class="form-control">


                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                    <input type="text" name="costcenter[<?= $cc_data['CostCenter_id'] ?>][pf_empamount]" class="form-control">


                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                    <input type="text" name="costcenter[<?= $cc_data['CostCenter_id'] ?>][pf_emplramount]" class="form-control">


                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                    <input type="text" name="costcenter[<?= $cc_data['CostCenter_id'] ?>][pf_adamount]" class="form-control">


                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                    <input type="text" name="costcenter[<?= $cc_data['CostCenter_id'] ?>][esi_empamount]" class="form-control">


                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                    <input type="text" name="costcenter[<?= $cc_data['CostCenter_id'] ?>][esi_emplramount]" class="form-control">


                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                    <input type="text" name="costcenter[<?= $cc_data['CostCenter_id'] ?>][ptaxamount]" class="form-control">


                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                    <input type="text" name="costcenter[<?= $cc_data['CostCenter_id'] ?>][tdsamount]" class="form-control" value="0">


                                                                </div>
                                                            </td>

                                    </div>
                                </div>
                                </tr>
                            <?php
                                                    }

                            ?>
                            </tbody>


                            </table>

                            <button type="submit" name="addmanualpayroll" id="addmanualpayroll" class="btn btn-xs btn-primary items-search-btn float-right">Submit</button>

                            </form>











                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>



<?php

                    } else if (isset($_GET['salary'])) {
?>


    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">


                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">'
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <h3 class="text-lg mb-0">Manage Salary</h3>
                            </div>
                        </div>


                        <div class="filter-list">
                            <a href="payroll.php" class="btn  waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Payroll</a>
                            <a href="payroll.php?salary" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active"></i>Salary</a>
                            <a href="payroll.php?tds" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>TDS</a>
                            <a href="payroll.php?esi" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>ESI</a>
                            <a href="payroll.php?pf" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>PF</a>
                            <a href="payroll.php?ptax" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>P-TAX</a>
                        </div>

                        <div class="card card-tabs" style="border-radius: 20px;">

                            <div class="card-body">
                                <div class="row filter-serach-row">


                                    <div class="col-lg-12 col-md-12 col-sm-12">

                                        <div class="row custom-range-row">
                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                                <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position: absolute; z-index: 999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                            </div>

                                            <div class="col-lg-10 col-md-10 col-sm-12">
                                                <div class="section serach-input-section">
                                                    <input type="text" id="myInput" placeholder="" class="field form-control" />
                                                    <div class="icons-container">
                                                        <div class="icon-search">
                                                            <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>


                                    <div class="tab-content" id="custom-tabs-two-tabContent">
                                        <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                            <?php
                                            $cond = '';
                                            $sal = queryGet("SELECT m.*, m.sum_gross - COALESCE(SUM(p.amount), 0) AS due_amount, COALESCE(SUM(p.amount), 0) AS total_paidamount
                                            FROM erp_payroll_main m
                                            LEFT JOIN erp_payroll_processing p ON m.payroll_main_id = p.payroll_main_id AND p.pay_type = 'salary'
                                            WHERE m.company_id = $company_id 
                                            AND m.branch_id = $branch_id
                                            AND m.location_id = $location_id 
                                            GROUP BY m.payroll_main_id
                                            ORDER BY m.payroll_year, m.payroll_month", true);





                                            $num_list = $sal['numRows'];
                                            $count = $rowCount[0];
                                            $cnt = $GLOBALS['start'] + 1;
                                            $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_PAYROLL", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                            $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                            $settingsCheckbox = unserialize($settingsCh);
                                            $settingsCheckboxCount = count($settingsCheckbox);

                                            ?>

                                            <table class="table defaultDataTable table-hover text-nowrap p-0 m-0">
                                                <thead>
                                                    <tr class="alert-light">
                                                        <th>#</th>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <th>Month and Year</th>

                                                        <?php }

                                                        if (in_array(2, $settingsCheckbox)) { ?>

                                                            <th>Total Gross</th>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>

                                                            <th>Total Paid</th>
                                                        <?php }
                                                        if (in_array(4, $settingsCheckbox)) { ?>

                                                            <th>Total Due</th>
                                                        <?php } ?>

                                                        <th>Action</th>
                                                    </tr>
                                                </thead>


                                                <input type="hidden" name="submit-payroll">
                                                <input type="hidden" name="year" value="<?= $year ?>">
                                                <input type="hidden" name="month" value="<?= $month ?>">
                                                <tbody>
                                                    <?php
                                                    // console($BranchPrObj->fetchBranchSoListing()['data']);

                                                    foreach ($sal['data'] as $data) {
                                                        // console($data);
                                                        $myr = $data['payroll_month'] . '-' . $data['payroll_year'];
                                                        $dateObj = DateTime::createFromFormat('m-Y', $myr);
                                                        $datemyr = $dateObj->format('F Y');

                                                    ?>


                                                        <tr style="cursor:pointer">
                                                            <td><?= $cnt++ ?></td>
                                                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                                <td><?= $datemyr; ?>

                                                                </td>

                                                            <?php }
                                                            if (in_array(2, $settingsCheckbox)) { ?>
                                                                <td><?= $data['sum_gross'] ?>

                                                                </td>

                                                            <?php }
                                                            if (in_array(3, $settingsCheckbox)) { ?>
                                                                <td><?= $data['total_paidamount'] ?> </td>

                                                            <?php }
                                                            if (in_array(4, $settingsCheckbox)) { ?>
                                                                <td><?= $data['due_amount'] ?> </td>

                                                            <?php } ?>
                                                            <td>
                                                                <!-- <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" class="btn btn-sm">

                                                                    <i class="fa fa-eye po-list-icon"></i>

                                                                </a> -->
                                                                <?php
                                                                if ($data['acconting_status'] == 'posted') {
                                                                    if ($data['due_amount'] > 0) {                                                                ?>
                                                                        <form action="" method="POST" class="btn btn-sm">
                                                                            <input type="hidden" name="accpost" value=''>
                                                                            <input type="hidden" name="payroll_main_id" value='<?= $data['payroll_main_id'] ?>'>
                                                                            <input type="hidden" name="documentNo" value='<?= $data['payroll_code'] ?>'>
                                                                            <input type="hidden" name="payroll_month" value='<?= $data['payroll_month'] ?>'>
                                                                            <input type="hidden" name="payroll_year" value='<?= $data['payroll_year'] ?>'>
                                                                            <input type="hidden" name="sum_gross" value='<?= $data['sum_gross'] ?>'>
                                                                            <button title="Post to accounting" type="submit" onclick="return confirm('Are you sure to Post?')" class="p-0 btn btn-sm" style="cursor: pointer; border:none; background: none;">
                                                                                <i class="fa fa-book po-list-icon" aria-hidden="true"></i>
                                                                            </button>
                                                                        </form>
                                                                <?php
                                                                    } else {
                                                                        echo '<a title="Accounting Posted" class="btn btn-sm"><i class="fa fa-check po-list-icon" aria-hidden="true"></i></a>';
                                                                    }
                                                                } else {
                                                                    echo 'Payroll pending';
                                                                } ?>
                                                                <!-- right modal start here  -->


                                                                <div class="modal fade right goods-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">

                                                                    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">

                                                                        <!--Content-->

                                                                        <div class="modal-content">

                                                                            <!--Header-->

                                                                            <div class="modal-header pt-4">

                                                                                <div class="row">

                                                                                    <div class="col-lg-6 col-md-6 col-sm-6">


                                                                                    </div>

                                                                                    <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                        <p class="heading lead text-xs text-right mt-2 mb-2">Month & Year : <?= $datemyr; ?></p>


                                                                                    </div>

                                                                                </div>
                                                                                <div class="display-flex-space-between mt-4 mb-3">
                                                                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                                        <li class="nav-item">
                                                                                            <a class="nav-link active" id="home-tab<?= $data['month'] . "-" . $data['year'] ?>" data-toggle="tab" href="#home<?= $data['month'] . "-" . $data['year'] ?>" role="tab" aria-controls="home<?= $data['month'] . "-" . $data['year'] ?>" aria-selected="true">Info</a>
                                                                                        </li>

                                                                                        <!-- -------------------Audit History Button Start------------------------- -->
                                                                                        <li class="nav-item">
                                                                                            <a class="nav-link auditTrail" id="history-tab<?= $row['itemId'] ?>" data-toggle="tab" data-ccode="<?= $row['itemCode'] ?>" href="#history<?= $row['itemId'] ?>" role="tab" aria-controls="history<?= $row['itemId'] ?>" aria-selected="false"><i class="fas fa-history mr-2"></i>Trail</a>
                                                                                        </li>
                                                                                        <!---------------------Audit History Button End--------------------------->
                                                                                    </ul>


                                                                                    <div class="action-btns display-flex-gap goods-flex-btn" id="action-navbar">

                                                                                        <a href="" name="customerEditBtn">

                                                                                            <i title="Edit" class="fa fa-edit po-list-icon-invert"></i>

                                                                                        </a>

                                                                                        <a href="">

                                                                                            <i title="Delete" class="fa fa-trash po-list-icon-invert"></i>

                                                                                        </a>

                                                                                        <a href="">

                                                                                            <i title="Toggle" class="fa fa-toggle-on po-list-icon-invert"></i>

                                                                                        </a>

                                                                                    </div>

                                                                                </div>

                                                                            </div>



                                                                            <!--Body-->

                                                                            <div class="modal-body p-3">






                                                                                <div class="tab-content" id="myTabContent">



                                                                                    <div class="tab-pane fade show active" id="home<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" role="tabpanel" aria-labelledby="home-tab">


                                                                                        <!-- <div class="tab-pane fade active" id="home<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" role="tabpanel" aria-labelledby="home-tab" style="overflow: auto;"> -->
                                                                                        <?php
                                                                                        // console($data);
                                                                                        ?>
                                                                                        <!-- </div> -->



                                                                                        <table>
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th>Cost Center</th>
                                                                                                    <th>Gross</th>
                                                                                                    <th>PF Employee</th>
                                                                                                    <th>PF Employer</th>
                                                                                                    <th>PF Admin</th>
                                                                                                    <th>ESI Employee</th>
                                                                                                    <th>ESI Employer</th>
                                                                                                    <th>PTax</th>
                                                                                                    <th>TDS</th>

                                                                                                </tr>
                                                                                            </thead>
                                                                                            <?php

                                                                                            //  console($data);
                                                                                            $month = $data['payroll_month'];
                                                                                            $year = $data['payroll_year'];
                                                                                            $sql_c = queryGet("SELECT `alpha_costcenter_id`,SUM(`gross`) as gross , SUM(`pf_employee`) as pf_employee, SUM(`pf_employeer`) as pf_employeer , SUM(`pf_admin`) as pf_admin, SUM(`esi_employee`) as esi_employee, SUM(`esi_employeer`) as esi_employeer, SUM(`ptax`) as ptax,SUM(`tds`) as tds FROM `erp_payroll`  WHERE `payroll_month` = $month AND `payroll_year`= $year AND `location_id`=$location_id GROUP BY `alpha_costcenter_id`", true);
                                                                                            //console($sql);
                                                                                            $sql_data =  $sql_c['data'];



                                                                                            ?>
                                                                                            <tbody>
                                                                                                <?php
                                                                                                foreach ($sql_data as $dataC) {

                                                                                                    $cost_center = queryGet("SELECT * FROM `erp_cost_center` WHERE `CostCenter_id` = '" . $dataC['alpha_costcenter_id'] . "' AND `location_id`=$location_id");
                                                                                                    //console($cost_center['dataC']['CostCenter_code']);

                                                                                                ?>
                                                                                                    <tr>
                                                                                                        <td><?= $cost_center['data']['CostCenter_code'] ?></td>
                                                                                                        <td><?= $dataC['gross']  ?></td>
                                                                                                        <td><?= $dataC['pf_employee']  ?></td>
                                                                                                        <td><?= $dataC['pf_employeer']  ?></td>
                                                                                                        <td><?= $dataC['pf_admin']  ?></td>
                                                                                                        <td><?= $dataC['esi_employee']  ?></td>
                                                                                                        <td><?= $dataC['esi_employeer']  ?></td>
                                                                                                        <td><?= $dataC['ptax']  ?></td>
                                                                                                        <td><?= $dataC['tds']  ?></td>


                                                                                                    </tr>

                                                                                                <?php
                                                                                                }
                                                                                                ?>
                                                                                            </tbody>
                                                                                        </table>



                                                                                    </div>


                                                                                    <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                                    <div class="tab-pane fade" id="history<?= $oneSoList['so_id'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                                        <div class="audit-head-section mb-3 mt-3 ">
                                                                                            <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($oneSoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['created_at']) ?></p>
                                                                                            <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($oneSoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['updated_at']) ?></p>
                                                                                        </div>
                                                                                        <hr>
                                                                                        <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $oneSoList['so_number'] ?>">
                                                                                            <ol class="timeline">
                                                                                                <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                                    <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                                    <div class="new-comment font-bold">
                                                                                                        <p>Loading...
                                                                                                        <ul class="ml-3 pl-0">
                                                                                                            <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                        </ul>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                </li>
                                                                                                <p class="mt-0 mb-5 ml-5">Loading...</p>
                                                                                                <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                                    <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                                    <div class="new-comment font-bold">
                                                                                                        <p>Loading...
                                                                                                        <ul class="ml-3 pl-0">
                                                                                                            <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                        </ul>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                </li>
                                                                                                <p class="mt-0 mb-5 ml-5">Loading...</p>


                                                                                            </ol>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- -------------------Audit History Tab Body End------------------------- -->


                                                                                </div>

                                                                            </div>

                                                                        </div>

                                                                        <!--/.Content-->

                                                                    </div>

                                                                </div>

                                                                <!-- right modal end here  -->

                                                            </td>

                                                        </tr>


                                                    <?php

                                                    }
                                                    ?>
                                                </tbody>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="<?= $settingsCheckboxCount + 2; ?>">
                                                            <!-- Start .pagination -->

                                                            <?php
                                                            if ($count > 0 && $count > $GLOBALS['show']) {
                                                            ?>
                                                                <div class="pagination align-right">
                                                                    <?php pagination($count, "frm_opts"); ?>
                                                                </div>

                                                                <!-- End .pagination -->

                                                            <?php } ?>

                                                            <!-- End .pagination -->
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
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
                                                            <input type="hidden" name="pageTableName" value="ERP_PAYROLL" />
                                                            <div class="modal-body">
                                                                <div id="dropdownframe"></div>
                                                                <div id="main2">
                                                                    <table>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                                                Cost Center</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                                                Gross </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                                PF Employee</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                                                                PF Employer</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                                                                PF Admin</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                                                                ESI Employee</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />
                                                                                ESI Employer</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="8" />
                                                                                PTax</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(9, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="9" />
                                                                                TDS</td>
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
    </div>
    <!-- End Pegination from------->

    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                        echo  $_REQUEST['pageNo'];
                                                    } ?>">
    </form>



<?php
                    } else if (isset($_GET['tds'])) {
?>


    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">


                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">'
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <h3 class="text-lg mb-0">Manage TDS</h3>
                            </div>
                        </div>


                        <div class="filter-list">
                            <a href="payroll.php" class="btn  waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Payroll</a>
                            <a href="payroll.php?salary" class="btn waves-effect waves-light"><i class="fa fa-list mr-2"></i>Salary</a>
                            <a href="payroll.php?tds" class="btn active  waves-effect waves-light"><i class="fa fa-list mr-2 active "></i>TDS</a>
                            <a href="payroll.php?esi" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>ESI</a>
                            <a href="payroll.php?pf" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>PF</a>
                            <a href="payroll.php?ptax" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>P-TAX</a>
                        </div>

                        <div class="card card-tabs" style="border-radius: 20px;">

                            <div class="card-body">
                                <div class="row filter-serach-row">


                                    <div class="col-lg-12 col-md-12 col-sm-12">

                                        <div class="row custom-range-row">
                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                                <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position: absolute; z-index: 999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                            </div>

                                            <div class="col-lg-10 col-md-10 col-sm-12">
                                                <div class="section serach-input-section">
                                                    <input type="text" id="myInput" placeholder="" class="field form-control" />
                                                    <div class="icons-container">
                                                        <div class="icon-search">
                                                            <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>


                                    <div class="tab-content" id="custom-tabs-two-tabContent">
                                        <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                            <?php
                                            $cond = '';
                                            $sal = queryGet("SELECT m.*, m.sum_tds - COALESCE(SUM(p.amount), 0) AS due_amount, COALESCE(SUM(p.amount), 0) AS total_paidamount
                                                                    FROM erp_payroll_main m
                                                                    LEFT JOIN erp_payroll_processing p ON m.payroll_main_id = p.payroll_main_id AND p.pay_type = 'tds' 
                                                                    WHERE m.company_id = $company_id 
                                                                    AND m.branch_id = $branch_id
                                                                    AND m.location_id = $location_id                                                                    
                                                                    GROUP BY m.payroll_main_id
                                                                    ORDER BY m.payroll_year, m.payroll_month", true);


                                            $num_list = $sal['numRows'];
                                            $count = $rowCount[0];
                                            $cnt = $GLOBALS['start'] + 1;
                                            $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_PAYROLL", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                            $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                            $settingsCheckbox = unserialize($settingsCh);
                                            $settingsCheckboxCount = count($settingsCheckbox);

                                            ?>

                                            <table class="table defaultDataTable table-hover text-nowrap p-0 m-0">
                                                <thead>
                                                    <tr class="alert-light">
                                                        <th>#</th>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <th>Month and Year</th>

                                                        <?php }

                                                        if (in_array(2, $settingsCheckbox)) { ?>

                                                            <th>Total TDS</th>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>

                                                            <th>Total Paid</th>
                                                        <?php }
                                                        if (in_array(4, $settingsCheckbox)) { ?>

                                                            <th>Total Due</th>
                                                        <?php } ?>

                                                        <th>Action</th>
                                                    </tr>
                                                </thead>


                                                <input type="hidden" name="submit-payroll">
                                                <input type="hidden" name="year" value="<?= $year ?>">
                                                <input type="hidden" name="month" value="<?= $month ?>">
                                                <tbody>
                                                    <?php
                                                    // console($BranchPrObj->fetchBranchSoListing()['data']);

                                                    foreach ($sal['data'] as $data) {
                                                        // console($data);
                                                        $myr = $data['payroll_month'] . '-' . $data['payroll_year'];
                                                        $dateObj = DateTime::createFromFormat('m-Y', $myr);
                                                        $datemyr = $dateObj->format('F Y');

                                                    ?>


                                                        <tr style="cursor:pointer">
                                                            <td><?= $cnt++ ?></td>
                                                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                                <td><?= $datemyr; ?>

                                                                </td>

                                                            <?php }
                                                            if (in_array(2, $settingsCheckbox)) { ?>
                                                                <td><?= $data['sum_tds'] ?>

                                                                </td>

                                                            <?php }
                                                            if (in_array(3, $settingsCheckbox)) { ?>
                                                                <td><?= $data['total_paidamount'] ?> </td>

                                                            <?php }
                                                            if (in_array(4, $settingsCheckbox)) { ?>
                                                                <td><?= $data['due_amount'] ?> </td>

                                                            <?php } ?>
                                                            <td>
                                                                <!-- <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" class="btn btn-sm">
                        
                                                                                            <i class="fa fa-eye po-list-icon"></i>
                        
                                                                                        </a> -->
                                                                <?php
                                                                if ($data['acconting_status'] == 'posted') {
                                                                    if ($data['due_amount'] > 0) {
                                                                ?>
                                                                        <form action="" method="POST" class="btn btn-sm">
                                                                            <input type="hidden" name="accpost" value=''>
                                                                            <input type="hidden" name="payroll_main_id" value='<?= $data['payroll_main_id'] ?>'>
                                                                            <input type="hidden" name="documentNo" value='<?= $data['payroll_code'] ?>'>
                                                                            <input type="hidden" name="payroll_month" value='<?= $data['payroll_month'] ?>'>
                                                                            <input type="hidden" name="payroll_year" value='<?= $data['payroll_year'] ?>'>
                                                                            <input type="hidden" name="sum_tds" value='<?= $data['sum_tds'] ?>'>
                                                                            <button title="Post to accounting" type="submit" onclick="return confirm('Are you sure to Post?')" class="p-0 btn btn-sm" style="cursor: pointer; border:none; background: none;">
                                                                                <i class="fa fa-book po-list-icon" aria-hidden="true"></i>
                                                                            </button>
                                                                        </form>

                                                                <?php

                                                                    } else {
                                                                        echo '<a title="Accounting Posted" class="btn btn-sm"><i class="fa fa-check po-list-icon" aria-hidden="true"></i></a>';
                                                                    }
                                                                } else {
                                                                    echo 'Payroll pending';
                                                                } ?>
                                                                <!-- right modal start here  -->


                                                                <div class="modal fade right goods-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">

                                                                    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">

                                                                        <!--Content-->

                                                                        <div class="modal-content">

                                                                            <!--Header-->

                                                                            <div class="modal-header pt-4">

                                                                                <div class="row">

                                                                                    <div class="col-lg-6 col-md-6 col-sm-6">


                                                                                    </div>

                                                                                    <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                        <p class="heading lead text-xs text-right mt-2 mb-2">Month & Year : <?= $datemyr; ?></p>


                                                                                    </div>

                                                                                </div>
                                                                                <div class="display-flex-space-between mt-4 mb-3">
                                                                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                                        <li class="nav-item">
                                                                                            <a class="nav-link active" id="home-tab<?= $data['month'] . "-" . $data['year'] ?>" data-toggle="tab" href="#home<?= $data['month'] . "-" . $data['year'] ?>" role="tab" aria-controls="home<?= $data['month'] . "-" . $data['year'] ?>" aria-selected="true">Info</a>
                                                                                        </li>

                                                                                        <!-- -------------------Audit History Button Start------------------------- -->
                                                                                        <li class="nav-item">
                                                                                            <a class="nav-link auditTrail" id="history-tab<?= $row['itemId'] ?>" data-toggle="tab" data-ccode="<?= $row['itemCode'] ?>" href="#history<?= $row['itemId'] ?>" role="tab" aria-controls="history<?= $row['itemId'] ?>" aria-selected="false"><i class="fas fa-history mr-2"></i>Trail</a>
                                                                                        </li>
                                                                                        <!---------------------Audit History Button End--------------------------->
                                                                                    </ul>


                                                                                    <div class="action-btns display-flex-gap goods-flex-btn" id="action-navbar">

                                                                                        <a href="" name="customerEditBtn">

                                                                                            <i title="Edit" class="fa fa-edit po-list-icon-invert"></i>

                                                                                        </a>

                                                                                        <a href="">

                                                                                            <i title="Delete" class="fa fa-trash po-list-icon-invert"></i>

                                                                                        </a>

                                                                                        <a href="">

                                                                                            <i title="Toggle" class="fa fa-toggle-on po-list-icon-invert"></i>

                                                                                        </a>

                                                                                    </div>

                                                                                </div>

                                                                            </div>



                                                                            <!--Body-->

                                                                            <div class="modal-body p-3">






                                                                                <div class="tab-content" id="myTabContent">



                                                                                    <div class="tab-pane fade show active" id="home<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" role="tabpanel" aria-labelledby="home-tab">


                                                                                        <!-- <div class="tab-pane fade active" id="home<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" role="tabpanel" aria-labelledby="home-tab" style="overflow: auto;"> -->
                                                                                        <?php
                                                                                        // console($data);
                                                                                        ?>
                                                                                        <!-- </div> -->



                                                                                        <table>
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th>Cost Center</th>
                                                                                                    <th>Gross</th>
                                                                                                    <th>PF Employee</th>
                                                                                                    <th>PF Employer</th>
                                                                                                    <th>PF Admin</th>
                                                                                                    <th>ESI Employee</th>
                                                                                                    <th>ESI Employer</th>
                                                                                                    <th>PTax</th>
                                                                                                    <th>TDS</th>

                                                                                                </tr>
                                                                                            </thead>
                                                                                            <?php

                                                                                            //  console($data);
                                                                                            $month = $data['payroll_month'];
                                                                                            $year = $data['payroll_year'];
                                                                                            $sql_c = queryGet("SELECT `alpha_costcenter_id`,SUM(`gross`) as gross , SUM(`pf_employee`) as pf_employee, SUM(`pf_employeer`) as pf_employeer , SUM(`pf_admin`) as pf_admin, SUM(`esi_employee`) as esi_employee, SUM(`esi_employeer`) as esi_employeer, SUM(`ptax`) as ptax,SUM(`tds`) as tds FROM `erp_payroll`  WHERE `payroll_month` = $month AND `payroll_year`= $year AND `location_id`=$location_id GROUP BY `alpha_costcenter_id`", true);
                                                                                            //console($sql);
                                                                                            $sql_data =  $sql_c['data'];



                                                                                            ?>
                                                                                            <tbody>
                                                                                                <?php
                                                                                                foreach ($sql_data as $dataC) {

                                                                                                    $cost_center = queryGet("SELECT * FROM `erp_cost_center` WHERE `CostCenter_id` = '" . $dataC['alpha_costcenter_id'] . "' AND `location_id`=$location_id");
                                                                                                    //console($cost_center['dataC']['CostCenter_code']);

                                                                                                ?>
                                                                                                    <tr>
                                                                                                        <td><?= $cost_center['data']['CostCenter_code'] ?></td>
                                                                                                        <td><?= $dataC['gross']  ?></td>
                                                                                                        <td><?= $dataC['pf_employee']  ?></td>
                                                                                                        <td><?= $dataC['pf_employeer']  ?></td>
                                                                                                        <td><?= $dataC['pf_admin']  ?></td>
                                                                                                        <td><?= $dataC['esi_employee']  ?></td>
                                                                                                        <td><?= $dataC['esi_employeer']  ?></td>
                                                                                                        <td><?= $dataC['ptax']  ?></td>
                                                                                                        <td><?= $dataC['tds']  ?></td>


                                                                                                    </tr>

                                                                                                <?php
                                                                                                }
                                                                                                ?>
                                                                                            </tbody>
                                                                                        </table>



                                                                                    </div>


                                                                                    <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                                    <div class="tab-pane fade" id="history<?= $oneSoList['so_id'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                                        <div class="audit-head-section mb-3 mt-3 ">
                                                                                            <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($oneSoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['created_at']) ?></p>
                                                                                            <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($oneSoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['updated_at']) ?></p>
                                                                                        </div>
                                                                                        <hr>
                                                                                        <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $oneSoList['so_number'] ?>">
                                                                                            <ol class="timeline">
                                                                                                <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                                    <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                                    <div class="new-comment font-bold">
                                                                                                        <p>Loading...
                                                                                                        <ul class="ml-3 pl-0">
                                                                                                            <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                        </ul>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                </li>
                                                                                                <p class="mt-0 mb-5 ml-5">Loading...</p>
                                                                                                <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                                    <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                                    <div class="new-comment font-bold">
                                                                                                        <p>Loading...
                                                                                                        <ul class="ml-3 pl-0">
                                                                                                            <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                        </ul>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                </li>
                                                                                                <p class="mt-0 mb-5 ml-5">Loading...</p>


                                                                                            </ol>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- -------------------Audit History Tab Body End------------------------- -->


                                                                                </div>

                                                                            </div>

                                                                        </div>

                                                                        <!--/.Content-->

                                                                    </div>

                                                                </div>

                                                                <!-- right modal end here  -->

                                                            </td>

                                                        </tr>


                                                    <?php

                                                    }
                                                    ?>
                                                </tbody>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="<?= $settingsCheckboxCount + 2; ?>">
                                                            <!-- Start .pagination -->

                                                            <?php
                                                            if ($count > 0 && $count > $GLOBALS['show']) {
                                                            ?>
                                                                <div class="pagination align-right">
                                                                    <?php pagination($count, "frm_opts"); ?>
                                                                </div>

                                                                <!-- End .pagination -->

                                                            <?php } ?>

                                                            <!-- End .pagination -->
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
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
                                                            <input type="hidden" name="pageTableName" value="ERP_PAYROLL" />
                                                            <div class="modal-body">
                                                                <div id="dropdownframe"></div>
                                                                <div id="main2">
                                                                    <table>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                                                Cost Center</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                                                Gross </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                                PF Employee</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                                                                PF Employer</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                                                                PF Admin</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                                                                ESI Employee</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />
                                                                                ESI Employer</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="8" />
                                                                                PTax</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(9, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="9" />
                                                                                TDS</td>
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
    </div>
    <!-- End Pegination from------->

    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                        echo  $_REQUEST['pageNo'];
                                                    } ?>">
    </form>



<?php
                    } else if (isset($_GET['esi'])) {
?>


    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">


                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">'
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <h3 class="text-lg mb-0">Manage ESI</h3>
                            </div>
                        </div>


                        <div class="filter-list">
                            <a href="payroll.php" class="btn  waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Payroll</a>
                            <a href="payroll.php?salary" class="btn waves-effect waves-light"><i class="fa fa-list mr-2"></i>Salary</a>
                            <a href="payroll.php?tds" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>TDS</a>
                            <a href="payroll.php?esi" class="btn active  waves-effect waves-light"><i class="fa fa-list mr-2 active "></i>ESI</a>
                            <a href="payroll.php?pf" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>PF</a>
                            <a href="payroll.php?ptax" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>P-TAX</a>
                        </div>

                        <div class="card card-tabs" style="border-radius: 20px;">

                            <div class="card-body">
                                <div class="row filter-serach-row">


                                    <div class="col-lg-12 col-md-12 col-sm-12">

                                        <div class="row custom-range-row">
                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                                <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position: absolute; z-index: 999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                            </div>

                                            <div class="col-lg-10 col-md-10 col-sm-12">
                                                <div class="section serach-input-section">
                                                    <input type="text" id="myInput" placeholder="" class="field form-control" />
                                                    <div class="icons-container">
                                                        <div class="icon-search">
                                                            <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>


                                    <div class="tab-content" id="custom-tabs-two-tabContent">
                                        <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                            <?php
                                            $cond = '';
                                            $sal = queryGet("SELECT m.*, m.sum_esi_employee - COALESCE(SUM(p.amount), 0) AS due_amount, COALESCE(SUM(p.amount), 0) AS total_paidamount
                                                FROM erp_payroll_main m
                                                LEFT JOIN erp_payroll_processing p ON m.payroll_main_id = p.payroll_main_id AND p.pay_type = 'esi'
                                                WHERE m.company_id = $company_id 
                                                AND m.branch_id = $branch_id
                                                AND m.location_id = $location_id                                                 
                                                GROUP BY m.payroll_main_id
                                                ORDER BY m.payroll_year, m.payroll_month", true);





                                            $num_list = $sal['numRows'];
                                            $count = $rowCount[0];
                                            $cnt = $GLOBALS['start'] + 1;
                                            $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_PAYROLL", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                            $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                            $settingsCheckbox = unserialize($settingsCh);
                                            $settingsCheckboxCount = count($settingsCheckbox);

                                            ?>

                                            <table class="table defaultDataTable table-hover text-nowrap p-0 m-0">
                                                <thead>
                                                    <tr class="alert-light">
                                                        <th>#</th>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <th>Month and Year</th>

                                                        <?php }

                                                        if (in_array(2, $settingsCheckbox)) { ?>

                                                            <th>Total ESI</th>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>

                                                            <th>Total Paid</th>
                                                        <?php }
                                                        if (in_array(4, $settingsCheckbox)) { ?>

                                                            <th>Total Due</th>
                                                        <?php } ?>

                                                        <th>Action</th>
                                                    </tr>
                                                </thead>


                                                <input type="hidden" name="submit-payroll">
                                                <input type="hidden" name="year" value="<?= $year ?>">
                                                <input type="hidden" name="month" value="<?= $month ?>">
                                                <tbody>
                                                    <?php
                                                    // console($BranchPrObj->fetchBranchSoListing()['data']);

                                                    foreach ($sal['data'] as $data) {
                                                        // console($data);
                                                        $myr = $data['payroll_month'] . '-' . $data['payroll_year'];
                                                        $dateObj = DateTime::createFromFormat('m-Y', $myr);
                                                        $datemyr = $dateObj->format('F Y');

                                                    ?>


                                                        <tr style="cursor:pointer">
                                                            <td><?= $cnt++ ?></td>
                                                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                                <td><?= $datemyr; ?>

                                                                </td>

                                                            <?php }
                                                            if (in_array(2, $settingsCheckbox)) { ?>
                                                                <td><?= $data['sum_esi_employee'] ?>

                                                                </td>

                                                            <?php }
                                                            if (in_array(3, $settingsCheckbox)) { ?>
                                                                <td><?= $data['total_paidamount'] ?> </td>

                                                            <?php }
                                                            if (in_array(4, $settingsCheckbox)) { ?>
                                                                <td><?= $data['due_amount'] ?> </td>

                                                            <?php } ?>
                                                            <td>
                                                                <!-- <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" class="btn btn-sm">
                                                
                                                                                                                    <i class="fa fa-eye po-list-icon"></i>
                                                
                                                                                                                </a> -->
                                                                <?php
                                                                if ($data['acconting_status'] == 'Posted') {
                                                                    if ($data['due_amount'] > 0) {
                                                                ?>
                                                                        <form action="" method="POST" class="btn btn-sm">
                                                                            <input type="hidden" name="accpost" value=''>
                                                                            <input type="hidden" name="payroll_main_id" value='<?= $data['payroll_main_id'] ?>'>
                                                                            <input type="hidden" name="documentNo" value='<?= $data['payroll_code'] ?>'>
                                                                            <input type="hidden" name="payroll_month" value='<?= $data['payroll_month'] ?>'>
                                                                            <input type="hidden" name="payroll_year" value='<?= $data['payroll_year'] ?>'>
                                                                            <input type="hidden" name="sum_esi_employee" value='<?= $data['sum_esi_employee'] ?>'>
                                                                            <button title="Post to accounting" type="submit" onclick="return confirm('Are you sure to Post?')" class="p-0 btn btn-sm" style="cursor: pointer; border:none; background: none;">
                                                                                <i class="fa fa-book po-list-icon" aria-hidden="true"></i>
                                                                            </button>
                                                                        </form>
                                                                <?php

                                                                    } else {
                                                                        echo '<a title="Accounting Posted" class="btn btn-sm"><i class="fa fa-check po-list-icon" aria-hidden="true"></i></a>';
                                                                    }
                                                                } else {
                                                                    echo 'Payroll pending';
                                                                } ?>
                                                                <!-- right modal start here  -->


                                                                <div class="modal fade right goods-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">

                                                                    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">

                                                                        <!--Content-->

                                                                        <div class="modal-content">

                                                                            <!--Header-->

                                                                            <div class="modal-header pt-4">

                                                                                <div class="row">

                                                                                    <div class="col-lg-6 col-md-6 col-sm-6">


                                                                                    </div>

                                                                                    <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                        <p class="heading lead text-xs text-right mt-2 mb-2">Month & Year : <?= $datemyr; ?></p>


                                                                                    </div>

                                                                                </div>
                                                                                <div class="display-flex-space-between mt-4 mb-3">
                                                                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                                        <li class="nav-item">
                                                                                            <a class="nav-link active" id="home-tab<?= $data['month'] . "-" . $data['year'] ?>" data-toggle="tab" href="#home<?= $data['month'] . "-" . $data['year'] ?>" role="tab" aria-controls="home<?= $data['month'] . "-" . $data['year'] ?>" aria-selected="true">Info</a>
                                                                                        </li>

                                                                                        <!-- -------------------Audit History Button Start------------------------- -->
                                                                                        <li class="nav-item">
                                                                                            <a class="nav-link auditTrail" id="history-tab<?= $row['itemId'] ?>" data-toggle="tab" data-ccode="<?= $row['itemCode'] ?>" href="#history<?= $row['itemId'] ?>" role="tab" aria-controls="history<?= $row['itemId'] ?>" aria-selected="false"><i class="fas fa-history mr-2"></i>Trail</a>
                                                                                        </li>
                                                                                        <!---------------------Audit History Button End--------------------------->
                                                                                    </ul>


                                                                                    <div class="action-btns display-flex-gap goods-flex-btn" id="action-navbar">

                                                                                        <a href="" name="customerEditBtn">

                                                                                            <i title="Edit" class="fa fa-edit po-list-icon-invert"></i>

                                                                                        </a>

                                                                                        <a href="">

                                                                                            <i title="Delete" class="fa fa-trash po-list-icon-invert"></i>

                                                                                        </a>

                                                                                        <a href="">

                                                                                            <i title="Toggle" class="fa fa-toggle-on po-list-icon-invert"></i>

                                                                                        </a>

                                                                                    </div>

                                                                                </div>

                                                                            </div>



                                                                            <!--Body-->

                                                                            <div class="modal-body p-3">






                                                                                <div class="tab-content" id="myTabContent">



                                                                                    <div class="tab-pane fade show active" id="home<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" role="tabpanel" aria-labelledby="home-tab">


                                                                                        <!-- <div class="tab-pane fade active" id="home<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" role="tabpanel" aria-labelledby="home-tab" style="overflow: auto;"> -->
                                                                                        <?php
                                                                                        // console($data);
                                                                                        ?>
                                                                                        <!-- </div> -->



                                                                                        <table>
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th>Cost Center</th>
                                                                                                    <th>Gross</th>
                                                                                                    <th>PF Employee</th>
                                                                                                    <th>PF Employer</th>
                                                                                                    <th>PF Admin</th>
                                                                                                    <th>ESI Employee</th>
                                                                                                    <th>ESI Employer</th>
                                                                                                    <th>PTax</th>
                                                                                                    <th>TDS</th>

                                                                                                </tr>
                                                                                            </thead>
                                                                                            <?php

                                                                                            //  console($data);
                                                                                            $month = $data['payroll_month'];
                                                                                            $year = $data['payroll_year'];
                                                                                            $sql_c = queryGet("SELECT `alpha_costcenter_id`,SUM(`gross`) as gross , SUM(`pf_employee`) as pf_employee, SUM(`pf_employeer`) as pf_employeer , SUM(`pf_admin`) as pf_admin, SUM(`esi_employee`) as esi_employee, SUM(`esi_employeer`) as esi_employeer, SUM(`ptax`) as ptax,SUM(`tds`) as tds FROM `erp_payroll`  WHERE `payroll_month` = $month AND `payroll_year`= $year AND `location_id`=$location_id GROUP BY `alpha_costcenter_id`", true);
                                                                                            //console($sql);
                                                                                            $sql_data =  $sql_c['data'];



                                                                                            ?>
                                                                                            <tbody>
                                                                                                <?php
                                                                                                foreach ($sql_data as $dataC) {

                                                                                                    $cost_center = queryGet("SELECT * FROM `erp_cost_center` WHERE `CostCenter_id` = '" . $dataC['alpha_costcenter_id'] . "' AND `location_id`=$location_id");
                                                                                                    //console($cost_center['dataC']['CostCenter_code']);

                                                                                                ?>
                                                                                                    <tr>
                                                                                                        <td><?= $cost_center['data']['CostCenter_code'] ?></td>
                                                                                                        <td><?= $dataC['gross']  ?></td>
                                                                                                        <td><?= $dataC['pf_employee']  ?></td>
                                                                                                        <td><?= $dataC['pf_employeer']  ?></td>
                                                                                                        <td><?= $dataC['pf_admin']  ?></td>
                                                                                                        <td><?= $dataC['esi_employee']  ?></td>
                                                                                                        <td><?= $dataC['esi_employeer']  ?></td>
                                                                                                        <td><?= $dataC['ptax']  ?></td>
                                                                                                        <td><?= $dataC['tds']  ?></td>


                                                                                                    </tr>

                                                                                                <?php
                                                                                                }
                                                                                                ?>
                                                                                            </tbody>
                                                                                        </table>



                                                                                    </div>


                                                                                    <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                                    <div class="tab-pane fade" id="history<?= $oneSoList['so_id'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                                        <div class="audit-head-section mb-3 mt-3 ">
                                                                                            <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($oneSoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['created_at']) ?></p>
                                                                                            <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($oneSoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['updated_at']) ?></p>
                                                                                        </div>
                                                                                        <hr>
                                                                                        <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $oneSoList['so_number'] ?>">
                                                                                            <ol class="timeline">
                                                                                                <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                                    <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                                    <div class="new-comment font-bold">
                                                                                                        <p>Loading...
                                                                                                        <ul class="ml-3 pl-0">
                                                                                                            <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                        </ul>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                </li>
                                                                                                <p class="mt-0 mb-5 ml-5">Loading...</p>
                                                                                                <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                                    <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                                    <div class="new-comment font-bold">
                                                                                                        <p>Loading...
                                                                                                        <ul class="ml-3 pl-0">
                                                                                                            <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                        </ul>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                </li>
                                                                                                <p class="mt-0 mb-5 ml-5">Loading...</p>


                                                                                            </ol>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- -------------------Audit History Tab Body End------------------------- -->


                                                                                </div>

                                                                            </div>

                                                                        </div>

                                                                        <!--/.Content-->

                                                                    </div>

                                                                </div>

                                                                <!-- right modal end here  -->

                                                            </td>

                                                        </tr>


                                                    <?php

                                                    }
                                                    ?>
                                                </tbody>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="<?= $settingsCheckboxCount + 2; ?>">
                                                            <!-- Start .pagination -->

                                                            <?php
                                                            if ($count > 0 && $count > $GLOBALS['show']) {
                                                            ?>
                                                                <div class="pagination align-right">
                                                                    <?php pagination($count, "frm_opts"); ?>
                                                                </div>

                                                                <!-- End .pagination -->

                                                            <?php } ?>

                                                            <!-- End .pagination -->
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
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
                                                            <input type="hidden" name="pageTableName" value="ERP_PAYROLL" />
                                                            <div class="modal-body">
                                                                <div id="dropdownframe"></div>
                                                                <div id="main2">
                                                                    <table>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                                                Cost Center</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                                                Gross </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                                PF Employee</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                                                                PF Employer</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                                                                PF Admin</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                                                                ESI Employee</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />
                                                                                ESI Employer</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="8" />
                                                                                PTax</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(9, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="9" />
                                                                                TDS</td>
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
    </div>
    <!-- End Pegination from------->

    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                        echo  $_REQUEST['pageNo'];
                                                    } ?>">
    </form>



<?php
                    } else if (isset($_GET['pf'])) {
?>


    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">


                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">'
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <h3 class="text-lg mb-0">Manage PF</h3>
                            </div>
                        </div>


                        <div class="filter-list">
                            <a href="payroll.php" class="btn  waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Payroll</a>
                            <a href="payroll.php?salary" class="btn waves-effect waves-light"><i class="fa fa-list mr-2"></i>Salary</a>
                            <a href="payroll.php?tds" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>TDS</a>
                            <a href="payroll.php?esi" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>ESI</a>
                            <a href="payroll.php?pf" class="btn active  waves-effect waves-light"><i class="fa fa-list mr-2 active "></i>PF</a>
                            <a href="payroll.php?ptax" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>P-TAX</a>
                        </div>

                        <div class="card card-tabs" style="border-radius: 20px;">

                            <div class="card-body">
                                <div class="row filter-serach-row">


                                    <div class="col-lg-12 col-md-12 col-sm-12">

                                        <div class="row custom-range-row">
                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                                <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position: absolute; z-index: 999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                            </div>

                                            <div class="col-lg-10 col-md-10 col-sm-12">
                                                <div class="section serach-input-section">
                                                    <input type="text" id="myInput" placeholder="" class="field form-control" />
                                                    <div class="icons-container">
                                                        <div class="icon-search">
                                                            <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>


                                    <div class="tab-content" id="custom-tabs-two-tabContent">
                                        <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                            <?php
                                            $cond = '';
                                            $sal = queryGet("SELECT m.*, m.sum_pf_employee - COALESCE(SUM(p.amount), 0) AS due_amount, COALESCE(SUM(p.amount), 0) AS total_paidamount
                                                        FROM erp_payroll_main m
                                                        LEFT JOIN erp_payroll_processing p ON m.payroll_main_id = p.payroll_main_id AND p.pay_type = 'pf' 
                                                        WHERE m.company_id = $company_id 
                                                        AND m.branch_id = $branch_id
                                                        AND m.location_id = $location_id                                                         
                                                        GROUP BY m.payroll_main_id
                                                        ORDER BY m.payroll_year, m.payroll_month", true);





                                            $num_list = $sal['numRows'];
                                            $count = $rowCount[0];
                                            $cnt = $GLOBALS['start'] + 1;
                                            $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_PAYROLL", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                            $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                            $settingsCheckbox = unserialize($settingsCh);
                                            $settingsCheckboxCount = count($settingsCheckbox);

                                            ?>

                                            <table class="table defaultDataTable table-hover text-nowrap p-0 m-0">
                                                <thead>
                                                    <tr class="alert-light">
                                                        <th>#</th>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <th>Month and Year</th>

                                                        <?php }

                                                        if (in_array(2, $settingsCheckbox)) { ?>

                                                            <th>Total PF</th>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>

                                                            <th>Total Paid</th>
                                                        <?php }
                                                        if (in_array(4, $settingsCheckbox)) { ?>

                                                            <th>Total Due</th>
                                                        <?php } ?>

                                                        <th>Action</th>
                                                    </tr>
                                                </thead>


                                                <input type="hidden" name="submit-payroll">
                                                <input type="hidden" name="year" value="<?= $year ?>">
                                                <input type="hidden" name="month" value="<?= $month ?>">
                                                <tbody>
                                                    <?php
                                                    // console($BranchPrObj->fetchBranchSoListing()['data']);

                                                    foreach ($sal['data'] as $data) {
                                                        // console($data);
                                                        $myr = $data['payroll_month'] . '-' . $data['payroll_year'];
                                                        $dateObj = DateTime::createFromFormat('m-Y', $myr);
                                                        $datemyr = $dateObj->format('F Y');

                                                    ?>


                                                        <tr style="cursor:pointer">
                                                            <td><?= $cnt++ ?></td>
                                                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                                <td><?= $datemyr; ?>

                                                                </td>

                                                            <?php }
                                                            if (in_array(2, $settingsCheckbox)) { ?>
                                                                <td><?= $data['sum_pf_employee'] ?>

                                                                </td>

                                                            <?php }
                                                            if (in_array(3, $settingsCheckbox)) { ?>
                                                                <td><?= $data['total_paidamount'] ?> </td>

                                                            <?php }
                                                            if (in_array(4, $settingsCheckbox)) { ?>
                                                                <td><?= $data['due_amount'] ?> </td>

                                                            <?php } ?>
                                                            <td>
                                                                <!-- <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" class="btn btn-sm">
                                                                        
                                                                                                                                            <i class="fa fa-eye po-list-icon"></i>
                                                                        
                                                                                                                                        </a> -->
                                                                <?php
                                                                if ($data['acconting_status'] == 'Pending') {
                                                                    if ($data['due_amount'] > 0) {
                                                                ?>
                                                                        <form action="" method="POST" class="btn btn-sm">
                                                                            <input type="hidden" name="accpost" value=''>
                                                                            <input type="hidden" name="payroll_main_id" value='<?= $data['payroll_main_id'] ?>'>
                                                                            <input type="hidden" name="documentNo" value='<?= $data['payroll_code'] ?>'>
                                                                            <input type="hidden" name="payroll_month" value='<?= $data['payroll_month'] ?>'>
                                                                            <input type="hidden" name="payroll_year" value='<?= $data['payroll_year'] ?>'>
                                                                            <input type="hidden" name="sum_pf_employee" value='<?= $data['sum_pf_employee'] ?>'>
                                                                            <button title="Post to accounting" type="submit" onclick="return confirm('Are you sure to Post?')" class="p-0 btn btn-sm" style="cursor: pointer; border:none; background: none;">
                                                                                <i class="fa fa-book po-list-icon" aria-hidden="true"></i>
                                                                            </button>
                                                                        </form>
                                                                <?php

                                                                    } else {
                                                                        echo '<a title="Accounting Posted" class="btn btn-sm"><i class="fa fa-check po-list-icon" aria-hidden="true"></i></a>';
                                                                    }
                                                                } else {
                                                                    echo 'Payroll pending';
                                                                } ?>
                                                                <!-- right modal start here  -->


                                                                <div class="modal fade right goods-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">

                                                                    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">

                                                                        <!--Content-->

                                                                        <div class="modal-content">

                                                                            <!--Header-->

                                                                            <div class="modal-header pt-4">

                                                                                <div class="row">

                                                                                    <div class="col-lg-6 col-md-6 col-sm-6">


                                                                                    </div>

                                                                                    <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                        <p class="heading lead text-xs text-right mt-2 mb-2">Month & Year : <?= $datemyr; ?></p>


                                                                                    </div>

                                                                                </div>
                                                                                <div class="display-flex-space-between mt-4 mb-3">
                                                                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                                        <li class="nav-item">
                                                                                            <a class="nav-link active" id="home-tab<?= $data['month'] . "-" . $data['year'] ?>" data-toggle="tab" href="#home<?= $data['month'] . "-" . $data['year'] ?>" role="tab" aria-controls="home<?= $data['month'] . "-" . $data['year'] ?>" aria-selected="true">Info</a>
                                                                                        </li>

                                                                                        <!-- -------------------Audit History Button Start------------------------- -->
                                                                                        <li class="nav-item">
                                                                                            <a class="nav-link auditTrail" id="history-tab<?= $row['itemId'] ?>" data-toggle="tab" data-ccode="<?= $row['itemCode'] ?>" href="#history<?= $row['itemId'] ?>" role="tab" aria-controls="history<?= $row['itemId'] ?>" aria-selected="false"><i class="fas fa-history mr-2"></i>Trail</a>
                                                                                        </li>
                                                                                        <!---------------------Audit History Button End--------------------------->
                                                                                    </ul>


                                                                                    <div class="action-btns display-flex-gap goods-flex-btn" id="action-navbar">

                                                                                        <a href="" name="customerEditBtn">

                                                                                            <i title="Edit" class="fa fa-edit po-list-icon-invert"></i>

                                                                                        </a>

                                                                                        <a href="">

                                                                                            <i title="Delete" class="fa fa-trash po-list-icon-invert"></i>

                                                                                        </a>

                                                                                        <a href="">

                                                                                            <i title="Toggle" class="fa fa-toggle-on po-list-icon-invert"></i>

                                                                                        </a>

                                                                                    </div>

                                                                                </div>

                                                                            </div>



                                                                            <!--Body-->

                                                                            <div class="modal-body p-3">






                                                                                <div class="tab-content" id="myTabContent">



                                                                                    <div class="tab-pane fade show active" id="home<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" role="tabpanel" aria-labelledby="home-tab">


                                                                                        <!-- <div class="tab-pane fade active" id="home<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" role="tabpanel" aria-labelledby="home-tab" style="overflow: auto;"> -->
                                                                                        <?php
                                                                                        // console($data);
                                                                                        ?>
                                                                                        <!-- </div> -->



                                                                                        <table>
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th>Cost Center</th>
                                                                                                    <th>Gross</th>
                                                                                                    <th>PF Employee</th>
                                                                                                    <th>PF Employer</th>
                                                                                                    <th>PF Admin</th>
                                                                                                    <th>ESI Employee</th>
                                                                                                    <th>ESI Employer</th>
                                                                                                    <th>PTax</th>
                                                                                                    <th>TDS</th>

                                                                                                </tr>
                                                                                            </thead>
                                                                                            <?php

                                                                                            //  console($data);
                                                                                            $month = $data['payroll_month'];
                                                                                            $year = $data['payroll_year'];
                                                                                            $sql_c = queryGet("SELECT `alpha_costcenter_id`,SUM(`gross`) as gross , SUM(`pf_employee`) as pf_employee, SUM(`pf_employeer`) as pf_employeer , SUM(`pf_admin`) as pf_admin, SUM(`esi_employee`) as esi_employee, SUM(`esi_employeer`) as esi_employeer, SUM(`ptax`) as ptax,SUM(`tds`) as tds FROM `erp_payroll`  WHERE `payroll_month` = $month AND `payroll_year`= $year AND `location_id`=$location_id GROUP BY `alpha_costcenter_id`", true);
                                                                                            //console($sql);
                                                                                            $sql_data =  $sql_c['data'];



                                                                                            ?>
                                                                                            <tbody>
                                                                                                <?php
                                                                                                foreach ($sql_data as $dataC) {

                                                                                                    $cost_center = queryGet("SELECT * FROM `erp_cost_center` WHERE `CostCenter_id` = '" . $dataC['alpha_costcenter_id'] . "' AND `location_id`=$location_id");
                                                                                                    //console($cost_center['dataC']['CostCenter_code']);

                                                                                                ?>
                                                                                                    <tr>
                                                                                                        <td><?= $cost_center['data']['CostCenter_code'] ?></td>
                                                                                                        <td><?= $dataC['gross']  ?></td>
                                                                                                        <td><?= $dataC['pf_employee']  ?></td>
                                                                                                        <td><?= $dataC['pf_employeer']  ?></td>
                                                                                                        <td><?= $dataC['pf_admin']  ?></td>
                                                                                                        <td><?= $dataC['esi_employee']  ?></td>
                                                                                                        <td><?= $dataC['esi_employeer']  ?></td>
                                                                                                        <td><?= $dataC['ptax']  ?></td>
                                                                                                        <td><?= $dataC['tds']  ?></td>


                                                                                                    </tr>

                                                                                                <?php
                                                                                                }
                                                                                                ?>
                                                                                            </tbody>
                                                                                        </table>



                                                                                    </div>


                                                                                    <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                                    <div class="tab-pane fade" id="history<?= $oneSoList['so_id'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                                        <div class="audit-head-section mb-3 mt-3 ">
                                                                                            <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($oneSoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['created_at']) ?></p>
                                                                                            <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($oneSoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['updated_at']) ?></p>
                                                                                        </div>
                                                                                        <hr>
                                                                                        <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $oneSoList['so_number'] ?>">
                                                                                            <ol class="timeline">
                                                                                                <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                                    <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                                    <div class="new-comment font-bold">
                                                                                                        <p>Loading...
                                                                                                        <ul class="ml-3 pl-0">
                                                                                                            <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                        </ul>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                </li>
                                                                                                <p class="mt-0 mb-5 ml-5">Loading...</p>
                                                                                                <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                                    <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                                    <div class="new-comment font-bold">
                                                                                                        <p>Loading...
                                                                                                        <ul class="ml-3 pl-0">
                                                                                                            <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                        </ul>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                </li>
                                                                                                <p class="mt-0 mb-5 ml-5">Loading...</p>


                                                                                            </ol>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- -------------------Audit History Tab Body End------------------------- -->


                                                                                </div>

                                                                            </div>

                                                                        </div>

                                                                        <!--/.Content-->

                                                                    </div>

                                                                </div>

                                                                <!-- right modal end here  -->

                                                            </td>

                                                        </tr>


                                                    <?php

                                                    }
                                                    ?>
                                                </tbody>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="<?= $settingsCheckboxCount + 2; ?>">
                                                            <!-- Start .pagination -->

                                                            <?php
                                                            if ($count > 0 && $count > $GLOBALS['show']) {
                                                            ?>
                                                                <div class="pagination align-right">
                                                                    <?php pagination($count, "frm_opts"); ?>
                                                                </div>

                                                                <!-- End .pagination -->

                                                            <?php } ?>

                                                            <!-- End .pagination -->
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
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
                                                            <input type="hidden" name="pageTableName" value="ERP_PAYROLL" />
                                                            <div class="modal-body">
                                                                <div id="dropdownframe"></div>
                                                                <div id="main2">
                                                                    <table>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                                                Cost Center</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                                                Gross </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                                PF Employee</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                                                                PF Employer</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                                                                PF Admin</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                                                                ESI Employee</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />
                                                                                ESI Employer</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="8" />
                                                                                PTax</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(9, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="9" />
                                                                                TDS</td>
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
    </div>
    <!-- End Pegination from------->

    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                        echo  $_REQUEST['pageNo'];
                                                    } ?>">
    </form>



<?php
                    } else if (isset($_GET['ptax'])) {
?>


    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">


                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">'
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <h3 class="text-lg mb-0">Manage P-Tax</h3>
                            </div>
                        </div>


                        <div class="filter-list">
                            <a href="payroll.php" class="btn  waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Payroll</a>
                            <a href="payroll.php?salary" class="btn waves-effect waves-light"><i class="fa fa-list mr-2"></i>Salary</a>
                            <a href="payroll.php?tds" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>TDS</a>
                            <a href="payroll.php?esi" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>ESI</a>
                            <a href="payroll.php?pf" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>PF</a>
                            <a href="payroll.php?ptax" class="btn active  waves-effect waves-light"><i class="fa fa-list mr-2 active "></i>P-TAX</a>
                        </div>

                        <div class="card card-tabs" style="border-radius: 20px;">

                            <div class="card-body">
                                <div class="row filter-serach-row">


                                    <div class="col-lg-12 col-md-12 col-sm-12">

                                        <div class="row custom-range-row">
                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                                <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position: absolute; z-index: 999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                            </div>

                                            <div class="col-lg-10 col-md-10 col-sm-12">
                                                <div class="section serach-input-section">
                                                    <input type="text" id="myInput" placeholder="" class="field form-control" />
                                                    <div class="icons-container">
                                                        <div class="icon-search">
                                                            <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>


                                    <div class="tab-content" id="custom-tabs-two-tabContent">
                                        <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                            <?php
                                            $cond = '';
                                            $sal = queryGet("SELECT m.*, m.sum_ptax - COALESCE(SUM(p.amount), 0) AS due_amount, COALESCE(SUM(p.amount), 0) AS total_paidamount
                                                                            FROM erp_payroll_main m
                                                                            LEFT JOIN erp_payroll_processing p ON m.payroll_main_id = p.payroll_main_id AND p.pay_type = 'ptax' 
                                                                            WHERE m.company_id = $company_id 
                                                                            AND m.branch_id = $branch_id
                                                                            AND m.location_id = $location_id                                                                            
                                                                            GROUP BY m.payroll_main_id
                                                                            ORDER BY m.payroll_year, m.payroll_month", true);





                                            $num_list = $sal['numRows'];
                                            $count = $rowCount[0];
                                            $cnt = $GLOBALS['start'] + 1;
                                            $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_PAYROLL", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                            $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                            $settingsCheckbox = unserialize($settingsCh);
                                            $settingsCheckboxCount = count($settingsCheckbox);

                                            ?>

                                            <table class="table defaultDataTable table-hover text-nowrap p-0 m-0">
                                                <thead>
                                                    <tr class="alert-light">
                                                        <th>#</th>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <th>Month and Year</th>

                                                        <?php }

                                                        if (in_array(2, $settingsCheckbox)) { ?>

                                                            <th>Total PTAX</th>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>

                                                            <th>Total Paid</th>
                                                        <?php }
                                                        if (in_array(4, $settingsCheckbox)) { ?>

                                                            <th>Total Due</th>
                                                        <?php } ?>

                                                        <th>Action</th>
                                                    </tr>
                                                </thead>


                                                <input type="hidden" name="submit-payroll">
                                                <input type="hidden" name="year" value="<?= $year ?>">
                                                <input type="hidden" name="month" value="<?= $month ?>">
                                                <tbody>
                                                    <?php
                                                    // console($BranchPrObj->fetchBranchSoListing()['data']);

                                                    foreach ($sal['data'] as $data) {
                                                        // console($data);
                                                        $myr = $data['payroll_month'] . '-' . $data['payroll_year'];
                                                        $dateObj = DateTime::createFromFormat('m-Y', $myr);
                                                        $datemyr = $dateObj->format('F Y');

                                                    ?>


                                                        <tr style="cursor:pointer">
                                                            <td><?= $cnt++ ?></td>
                                                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                                <td><?= $datemyr; ?>

                                                                </td>

                                                            <?php }
                                                            if (in_array(2, $settingsCheckbox)) { ?>
                                                                <td><?= $data['sum_ptax'] ?>

                                                                </td>

                                                            <?php }
                                                            if (in_array(3, $settingsCheckbox)) { ?>
                                                                <td><?= $data['total_paidamount'] ?> </td>

                                                            <?php }
                                                            if (in_array(4, $settingsCheckbox)) { ?>
                                                                <td><?= $data['due_amount'] ?> </td>

                                                            <?php } ?>
                                                            <td>
                                                                <!-- <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" class="btn btn-sm">
                                                                                                
                                                                                                                                                                    <i class="fa fa-eye po-list-icon"></i>
                                                                                                
                                                                                                                                                                </a> -->
                                                                <?php
                                                                if ($data['acconting_status'] == 'Pending') {
                                                                    if ($data['due_amount'] > 0) {
                                                                ?>
                                                                        <form action="" method="POST" class="btn btn-sm">
                                                                            <input type="hidden" name="accpost" value=''>
                                                                            <input type="hidden" name="payroll_main_id" value='<?= $data['payroll_main_id'] ?>'>
                                                                            <input type="hidden" name="documentNo" value='<?= $data['payroll_code'] ?>'>
                                                                            <input type="hidden" name="payroll_month" value='<?= $data['payroll_month'] ?>'>
                                                                            <input type="hidden" name="payroll_year" value='<?= $data['payroll_year'] ?>'>
                                                                            <input type="hidden" name="sum_ptax" value='<?= $data['sum_ptax'] ?>'>
                                                                            <button title="Post to accounting" type="submit" onclick="return confirm('Are you sure to Post?')" class="p-0 btn btn-sm" style="cursor: pointer; border:none; background: none;">
                                                                                <i class="fa fa-book po-list-icon" aria-hidden="true"></i>
                                                                            </button>
                                                                        </form>
                                                                <?php

                                                                    } else {
                                                                        echo '<a title="Accounting Posted" class="btn btn-sm"><i class="fa fa-check po-list-icon" aria-hidden="true"></i></a>';
                                                                    }
                                                                } else {
                                                                    echo 'Payroll pending';
                                                                } ?>
                                                                <!-- right modal start here  -->


                                                                <div class="modal fade right goods-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">

                                                                    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">

                                                                        <!--Content-->

                                                                        <div class="modal-content">

                                                                            <!--Header-->

                                                                            <div class="modal-header pt-4">

                                                                                <div class="row">

                                                                                    <div class="col-lg-6 col-md-6 col-sm-6">


                                                                                    </div>

                                                                                    <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                        <p class="heading lead text-xs text-right mt-2 mb-2">Month & Year : <?= $datemyr; ?></p>


                                                                                    </div>

                                                                                </div>
                                                                                <div class="display-flex-space-between mt-4 mb-3">
                                                                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                                        <li class="nav-item">
                                                                                            <a class="nav-link active" id="home-tab<?= $data['month'] . "-" . $data['year'] ?>" data-toggle="tab" href="#home<?= $data['month'] . "-" . $data['year'] ?>" role="tab" aria-controls="home<?= $data['month'] . "-" . $data['year'] ?>" aria-selected="true">Info</a>
                                                                                        </li>

                                                                                        <!-- -------------------Audit History Button Start------------------------- -->
                                                                                        <li class="nav-item">
                                                                                            <a class="nav-link auditTrail" id="history-tab<?= $row['itemId'] ?>" data-toggle="tab" data-ccode="<?= $row['itemCode'] ?>" href="#history<?= $row['itemId'] ?>" role="tab" aria-controls="history<?= $row['itemId'] ?>" aria-selected="false"><i class="fas fa-history mr-2"></i>Trail</a>
                                                                                        </li>
                                                                                        <!---------------------Audit History Button End--------------------------->
                                                                                    </ul>


                                                                                    <div class="action-btns display-flex-gap goods-flex-btn" id="action-navbar">

                                                                                        <a href="" name="customerEditBtn">

                                                                                            <i title="Edit" class="fa fa-edit po-list-icon-invert"></i>

                                                                                        </a>

                                                                                        <a href="">

                                                                                            <i title="Delete" class="fa fa-trash po-list-icon-invert"></i>

                                                                                        </a>

                                                                                        <a href="">

                                                                                            <i title="Toggle" class="fa fa-toggle-on po-list-icon-invert"></i>

                                                                                        </a>

                                                                                    </div>

                                                                                </div>

                                                                            </div>



                                                                            <!--Body-->

                                                                            <div class="modal-body p-3">






                                                                                <div class="tab-content" id="myTabContent">



                                                                                    <div class="tab-pane fade show active" id="home<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" role="tabpanel" aria-labelledby="home-tab">


                                                                                        <!-- <div class="tab-pane fade active" id="home<?= $data['payroll_month'] . "-" . $data['payroll_year'] ?>" role="tabpanel" aria-labelledby="home-tab" style="overflow: auto;"> -->
                                                                                        <?php
                                                                                        // console($data);
                                                                                        ?>
                                                                                        <!-- </div> -->



                                                                                        <table>
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th>Cost Center</th>
                                                                                                    <th>Gross</th>
                                                                                                    <th>PF Employee</th>
                                                                                                    <th>PF Employer</th>
                                                                                                    <th>PF Admin</th>
                                                                                                    <th>ESI Employee</th>
                                                                                                    <th>ESI Employer</th>
                                                                                                    <th>PTax</th>
                                                                                                    <th>TDS</th>

                                                                                                </tr>
                                                                                            </thead>
                                                                                            <?php

                                                                                            //  console($data);
                                                                                            $month = $data['payroll_month'];
                                                                                            $year = $data['payroll_year'];
                                                                                            $sql_c = queryGet("SELECT `alpha_costcenter_id`,SUM(`gross`) as gross , SUM(`pf_employee`) as pf_employee, SUM(`pf_employeer`) as pf_employeer , SUM(`pf_admin`) as pf_admin, SUM(`esi_employee`) as esi_employee, SUM(`esi_employeer`) as esi_employeer, SUM(`ptax`) as ptax,SUM(`tds`) as tds FROM `erp_payroll`  WHERE `payroll_month` = $month AND `payroll_year`= $year AND `location_id`=$location_id GROUP BY `alpha_costcenter_id`", true);
                                                                                            //console($sql);
                                                                                            $sql_data =  $sql_c['data'];



                                                                                            ?>
                                                                                            <tbody>
                                                                                                <?php
                                                                                                foreach ($sql_data as $dataC) {

                                                                                                    $cost_center = queryGet("SELECT * FROM `erp_cost_center` WHERE `CostCenter_id` = '" . $dataC['alpha_costcenter_id'] . "' AND `location_id`=$location_id");
                                                                                                    //console($cost_center['dataC']['CostCenter_code']);

                                                                                                ?>
                                                                                                    <tr>
                                                                                                        <td><?= $cost_center['data']['CostCenter_code'] ?></td>
                                                                                                        <td><?= $dataC['gross']  ?></td>
                                                                                                        <td><?= $dataC['pf_employee']  ?></td>
                                                                                                        <td><?= $dataC['pf_employeer']  ?></td>
                                                                                                        <td><?= $dataC['pf_admin']  ?></td>
                                                                                                        <td><?= $dataC['esi_employee']  ?></td>
                                                                                                        <td><?= $dataC['esi_employeer']  ?></td>
                                                                                                        <td><?= $dataC['ptax']  ?></td>
                                                                                                        <td><?= $dataC['tds']  ?></td>


                                                                                                    </tr>

                                                                                                <?php
                                                                                                }
                                                                                                ?>
                                                                                            </tbody>
                                                                                        </table>



                                                                                    </div>


                                                                                    <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                                    <div class="tab-pane fade" id="history<?= $oneSoList['so_id'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                                        <div class="audit-head-section mb-3 mt-3 ">
                                                                                            <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($oneSoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['created_at']) ?></p>
                                                                                            <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($oneSoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['updated_at']) ?></p>
                                                                                        </div>
                                                                                        <hr>
                                                                                        <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $oneSoList['so_number'] ?>">
                                                                                            <ol class="timeline">
                                                                                                <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                                    <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                                    <div class="new-comment font-bold">
                                                                                                        <p>Loading...
                                                                                                        <ul class="ml-3 pl-0">
                                                                                                            <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                        </ul>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                </li>
                                                                                                <p class="mt-0 mb-5 ml-5">Loading...</p>
                                                                                                <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                                    <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                                    <div class="new-comment font-bold">
                                                                                                        <p>Loading...
                                                                                                        <ul class="ml-3 pl-0">
                                                                                                            <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                        </ul>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                </li>
                                                                                                <p class="mt-0 mb-5 ml-5">Loading...</p>


                                                                                            </ol>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- -------------------Audit History Tab Body End------------------------- -->


                                                                                </div>

                                                                            </div>

                                                                        </div>

                                                                        <!--/.Content-->

                                                                    </div>

                                                                </div>

                                                                <!-- right modal end here  -->

                                                            </td>

                                                        </tr>


                                                    <?php

                                                    }
                                                    ?>
                                                </tbody>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="<?= $settingsCheckboxCount + 2; ?>">
                                                            <!-- Start .pagination -->

                                                            <?php
                                                            if ($count > 0 && $count > $GLOBALS['show']) {
                                                            ?>
                                                                <div class="pagination align-right">
                                                                    <?php pagination($count, "frm_opts"); ?>
                                                                </div>

                                                                <!-- End .pagination -->

                                                            <?php } ?>

                                                            <!-- End .pagination -->
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
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
                                                            <input type="hidden" name="pageTableName" value="ERP_PAYROLL" />
                                                            <div class="modal-body">
                                                                <div id="dropdownframe"></div>
                                                                <div id="main2">
                                                                    <table>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                                                Cost Center</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                                                Gross </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                                PF Employee</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                                                                PF Employer</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                                                                PF Admin</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                                                                ESI Employee</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />
                                                                                ESI Employer</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="8" />
                                                                                PTax</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(9, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="9" />
                                                                                TDS</td>
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
    </div>
    <!-- End Pegination from------->

    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                        echo  $_REQUEST['pageNo'];
                                                    } ?>">
    </form>



<?php
                    } else {
                        $url = BRANCH_URL . 'location/goods.php';
?>
    <script>
        window.location.href = "<?php echo $url; ?>";
    </script>
<?php

                    }

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