<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

require_once("../../app/v1/functions/branch/func-compliance-controller.php");
require_once("controller/gstr1-view-data.controller.php");
require_once("controller/gstr1-json-data.controller.php");
require_once("controller/gstr1-file.controller.php");

$branch_gstin_file_frequency = $_SESSION["branch_gstin_file_frequency"] ?? "";
$branch_gstin_file_r1_day = $_SESSION["branch_gstin_file_r1_day"] ?? "";
$branch_gstin_file_r2b_day = $_SESSION["branch_gstin_file_r2b_day"] ?? "";
$branch_gstin_file_r3b_day = $_SESSION["branch_gstin_file_r3b_day"] ?? "";
$gstr1ReturnPeriod = ($_GET["period"] ?? "") != "" ? base64_decode($_GET["period"]) : "";



$dbObj = new Database();
$gstr1SummaryObj = $dbObj->queryGet('SELECT * FROM `erp_compliance_gstr1` WHERE `company_id` = ' . $company_id . ' AND `branch_id` = ' . $branch_id . ' AND `gstr1_return_period` = "' . $gstr1ReturnPeriod . '"');
$gstr1SummaryData = $gstr1SummaryObj["data"];


$returnPeriod = $gstr1ReturnPeriod;
$fromDate = date("Y-m-01", strtotime($returnPeriod));
$toDate = date("Y-m-t", strtotime($returnPeriod));
$jsonObj = new ComplianceGstr1Json($returnPeriod, $fromDate, $toDate);



$authGstinPortalObj = new AuthGstinPortal();
$authObj = $authGstinPortalObj->checkAuth();

$complianceGSTR1FileObj = new ComplianceGSTR1File($returnPeriod, $authObj);
if ($authObj["status"] == "success") {
    // console($authObj);
    //STEP 1:
    $complianceGSTR1FileObj = new ComplianceGSTR1File($returnPeriod, $authObj);
    if (isset($_POST["saveGstr1PortalData"])) {
        $actionObj = $complianceGSTR1FileObj->saveGstr1Data(json_encode($jsonObj->getJson(), true));
        console("Save the data to compliance portal.");
        console($actionObj);
    }

    // STEP 2, 4:
    if (isset($_POST["checkReturnStatus"])) {
        $ref_id = $_POST["ref_id"] ?? "";
        $actionObj = $complianceGSTR1FileObj->getReturnStatus($ref_id);
        console("Return status");
        console($actionObj);
    }

    // STEP 3:
    if (isset($_POST["newProceedfile"])) {
        $actionObj = $complianceGSTR1FileObj->newProceedfile();
        console("Proceed to file GSTR1");
        console($actionObj);
    }

    // STEP 5:
    if (isset($_POST["getSummaryGstr1PortalData"])) {
        $actionObj = $complianceGSTR1FileObj->getGstr1Summary();
        console("Summary Gstr1 Portal Data:");
        console(json_encode($actionObj["data"], true));
        console($actionObj);
    }

    // STEP 6:
    if (isset($_POST["generateEVC"])) {
        $pan = $_POST["pan"] ?? "";
        $actionObj = $complianceGSTR1FileObj->generateOtpForEvc($pan);
        console("Generate OTP for EVC");
        console($actionObj);
    }

    // STEP 7:
    if (isset($_POST["submitOtpAndCompleteGstr1File"])) {
        $otp = $_POST["otp"] ?? "";
        $pan = $_POST["pan"] ?? "";
        $checksum = $_POST["checksum"] ?? "";
        $actionObj = $complianceGSTR1FileObj->fileGstr1($pan, $otp);
        console("Final submit for filling");
        console($actionObj);
    }


    // if (isset($_POST["submitGstr1PortalData"])) {
    //     $actionObj = $complianceGSTR1FileObj->submitGstr1Data();
    //     console("Submit the data to compliance portal.");
    //     console($actionObj);
    // }

    if (isset($_POST["resetGstr1PortalData"])) {
        $actionObj = $complianceGSTR1FileObj->resetSavedGstr1Data();
        console("reseting the data to compliance portal.");
        console($actionObj);
    }
} else {
    // redirect(BRANCH_URL."gstr1/gst1-report-graphical.php");
    echo "Redirecting to graphical view";
}

?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">


<style>
    section.gstr-1 {
        padding: 0px 20px;
    }

    .head-btn-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .gst-one-filter {
        left: 0;
        top: 0;
    }

    .gst-one-filter a.active {
        background-color: #003060;
        color: #fff;
    }

    .proceedToFile {
        display: grid;
        align-items: center;
        place-content: center;
        justify-items: center;
        gap: 17px;
    }

    .proceedToFile img {
        max-width: 150px;
        margin: 20px auto;
    }

    .proceedToFile .text {
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>


<div class="content-wrapper">
    <section class="gstr-1">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
            <li class="breadcrumb-item active"><a href="<?= BRANCH_URL ?>gstr1/gst1-report-graphical.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>GSTR1</a></li>
            <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-list po-list-icon"></i>Generate EVC <?= $branch_gstin_file_frequency != "" ? "(" . date("F, Y", strtotime($gstr1ReturnPeriod)) . " - " . strtoupper($branch_gstin_file_frequency) . ")" : "" ?></a></li>
            <li class="back-button">
                <a href="gst1-report-concised.php">
                    <i class="fa fa-reply po-list-icon"></i>
                </a>
            </li>
        </ol>
        <!-- <h4 class="text-lg font-bold mt-4 mb-4">GSTR-1</h4> -->
        <div class="head-btn-section mb-3">
            <div class="filter-list gst-one-filter">
                <a href="./gst1-preview.php<?= isset($_GET["period"]) ? '?period=' . $_GET["period"] : '' ?>" class="btn"><i class="fas fa-chart-bar mr-2"></i>Preview</a>
                <a href="" class="btn active"><i class="fa fa-list mr-2"></i>Pending Filling</a>
            </div>
        </div>

        <div class="card bg-light">
            <div class="card-header p-3 rounded">
                <h3 class="text-sm text-white mb-0 pl-3">Pending Filling</h3>
            </div>
            <div class="card-body p-0">

                <div class="row">
                    <div class="col-lg-8 col-sm-8 col-sm-8">
                        <h4 class="text-sm font-bold m-4">
                            Generate EVC & Confirm Filling
                        </h4>
                        <div class="proceedToFile">
                            <input type="hidden" name="gstr1ReturnPeriodInput" value="<?= $gstr1ReturnPeriod ?>">
                            <img src="../../public/assets/img/VitNew 1.png" alt="">
                            <div class="text">
                                <p class="text-sm">If you want to generate EVC then click on </p>
                                <a class="btn btn-primary border" href="gst1-generate-evc-2.php" onclick="return confirm('Are you sure to save data?')">Proceed to file</a>
                            </div>
                            <span class="text-sm">Or</span>
                            <form action="" method="post">
                                <div class="text">
                                    <p class="text-sm">If you want to reset the portal data then click on </p>
                                    <button class="btn btn-primary reset-btn" name="resetGstr1PortalData" type="submit" onclick="return confirm('Are you sure to reset data?')">Reset Data</button>
                                </div>
                            </form>
                        </div>


                    </div>
                    <div class="col-lg-4 col-sm-4 col-sm-4">
                        <div class="card w-75 ml-auto timeline-card mb-0">
                            <div class="card-body">
                                <div id="content">
                                    <ul class="timeline">
                                        <li class="event progress-success">
                                            <h3>Initiation</h3>
                                            <!-- <p>Mr.Guria</p>
                                            <p>16-08-2023</p>
                                            <p>Mr.Guria</p> -->
                                        </li>
                                        <li class="event progress-success">
                                            <h3>Connect</h3>
                                            <!-- <p>Mr.Guria</p>
                                            <p>16-08-2023</p>
                                            <p>Mr.Guria</p> -->
                                        </li>
                                        <li class="event progress-success border-color-light">
                                            <h3>Save File</h3>
                                            <!-- <p>Mr.Guria</p>
                                            <p>16-08-2023</p>
                                            <p>Mr.Guria</p> -->
                                        </li>
                                        <li class="event progress-disable">
                                            <h3>Generate EVC</h3>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>



<?php
require_once("../common/footer.php");
?>