<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../../app/v1/functions/branch/func-compliance-controller.php");
require_once("controller/gstr1-view-data.controller.php");
require_once("controller/gstr1-json-data.controller.php");
require_once("controller/gstr1-file.controller.php");
// administratorAuth();
?>
<style>
    .filter-list a {
        background: #fff;
        box-shadow: 1px 2px 5px -1px #8e8e8e;
    }

    .filter-list {
        margin-bottom: 2em;
    }

    li.nav-item.complince a {
        background: #fff;
        color: #003060;
        z-index: 9;
        margin-bottom: 1em;
    }
</style>
<link rel="stylesheet" href="../../public/assets/listing.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid my-4">
            <div class="row">
                <?php
                $fromDate = (isset($_GET["fromDate"]) && $_GET["fromDate"] != "") ? $_GET["fromDate"] : date("Y-m-d", strtotime('first day of last month'));
                $toDate = (isset($_GET["toDate"]) && $_GET["toDate"] != "") ? $_GET["toDate"] : date("Y-m-d", strtotime('last day of last month'));
                $returnPeriod = date("mY", strtotime($fromDate));
                $jsonObj = new ComplianceGstr1Json($returnPeriod, $fromDate, $toDate);
                ?>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-body px-0 pb-3">
                            <div class="row p-0 m-0 px-2">
                                <div class="col-md-4 d-flex gap-2 text-nowrap pl-0">
                                    <label>Select FY</label>
                                    <input type="date" name="fromDate" value="<?= $fromDate ?>" id="fyFromDate" class="fyDate form-control"><label>to</label>
                                    <input type="date" name="toDate" value="<?= $toDate ?>" id="fyToDate" class="fyDate form-control">
                                    <script>
                                        $(document).ready(function() {
                                            $('.fyDate').change(function() {
                                                window.location.href = `?fromDate=${$("#fyFromDate").val()}&toDate=${$("#fyToDate").val()}`;
                                            });
                                        });
                                    </script>
                                </div>
                                <div class="btn-group col-md-4 p-0 pb-1 ml-auto" role="group">
                                    <a href="gstr1-review.php" type="button" class="btn btn-secondary">Review</a>
                                    <a href="gstr1-action.php" type="button" class="btn btn-secondary active">Action</a>
                                </div>
                            </div>
                            <div class="row p-2 m-0">
                                <ul class="nav nav-tabs" role="tablist" style="background-color: #001621; padding: 2px;">
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-xs active" data-toggle="tab" href="#gstr1JsonDataTabDiv" role="tab" aria-selected="true">JSON Data</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-xs" data-toggle="tab" href="#gstr1FileTabDiv" role="tab" aria-selected="true">FILE GSTR-1</a>
                                    </li>
                                </ul>
                                <div class="tab-content p-1">
                                    <div class="tab-pane fade show active" id="gstr1JsonDataTabDiv" role="tabpanel" aria-labelledby="listTab">
                                        <div class="row p-0 m-0">
                                            <!-- Container to display JSON data -->
                                            <div class="card p-0">
                                                <div class="card-header py-1 px-3 text-white">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-white">Json View</span>
                                                        <a href="" id="json-download-button" download="gstr1-<?= $returnPeriod ?>.json" title="Download Json File" class="btn btn-sm bg-light">Download</a>
                                                    </div>
                                                </div>
                                                <div class="card-body p-2" style="white-space: pre-wrap;" id="json-container">
                                                </div>
                                            </div>
                                            <script>
                                                $(document).ready(function() {
                                                    try {
                                                        var jsonData = JSON.parse(`<?= json_encode($jsonObj->getJson(), JSON_PRETTY_PRINT) ?>`);
                                                        var jsonContainer = document.getElementById('json-container');
                                                        jsonContainer.textContent = JSON.stringify(jsonData, null, 2);
                                                        var downloadButton = document.getElementById('json-download-button');
                                                        downloadButton.href = 'data:application/json;charset=utf-8,' + encodeURIComponent(JSON.stringify(jsonData, null, 2));
                                                    } catch (error) {
                                                        console.log('Invalid JSON input');
                                                    }
                                                });
                                            </script>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="gstr1FileTabDiv" role="tabpanel" aria-labelledby="listTab">
                                        <div class="row p-0 m-0">
                                            <?php
                                            require_once(BASE_DIR . "branch/gstr/auth-component.php");
                                            ?>
                                        </div>
                                        <div class="row p-0 m-0 mt-2">
                                            <?php
                                            $authObj = $authGstinPortalObj->checkAuth();
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
                                                if(isset($_POST["newProceedfile"])) {
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
                                                if(isset($_POST["generateEVC"])){
                                                    $pan = $_POST["pan"] ?? "";
                                                    $actionObj = $complianceGSTR1FileObj->generateOtpForEvc($pan);
                                                    console("Generate OTP for EVC");
                                                    console($actionObj);
                                                }

                                                // STEP 7:
                                                if(isset($_POST["submitOtpAndCompleteGstr1File"])){
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

                                                
                                            ?>
                                                <form action="" method="post" class="col-md-3 m-2">
                                                    <button class="btn btn-primary" type="submit" name="saveGstr1PortalData" onclick="return confirm('Are you sure?');">1. Save Data to Portal</button>
                                                </form>
                                                <form action="" method="post" class="col-md-3 m-2">
                                                    <input type="text" name="ref_id" value="" placeholder="Enter Ref Id">
                                                    <button class="btn btn-primary" type="submit" name="checkReturnStatus" onclick="return confirm('Are you sure?');">2,4. Check Return Status</button>
                                                </form>
                                                <form action="" method="post" class="col-md-3 m-2">
                                                    <button class="btn btn-primary" type="submit" name="newProceedfile" onclick="return confirm('Are you sure?');">3. Procced to File GSTR1</button>
                                                </form>
                                                <form action="" method="post" class="col-md-3 m-2">
                                                    <button class="btn btn-primary" type="submit" name="getSummaryGstr1PortalData" onclick="return confirm('Are you sure?');">5. Get / Generate Summary</button>
                                                </form>

                                                <form action="" method="post" class="col-md-3 m-2">
                                                    <input type="text" name="pan" value="" placeholder="Pan Number">
                                                    <button class="btn btn-primary" type="submit" name="generateEVC" onclick="return confirm('Are you sure?');">6. Generate EVC</button>
                                                </form>

                                                <form action="" method="post" class="col-md-3 m-2">
                                                    <input type="text" name="pan" value="" placeholder="PAN">
                                                    <input type="text" name="otp" value="" placeholder="EVC OTP">
                                                    <!-- <input type="text" name="checksum" value="" placeholder="CHECKSUM FROM SUMMARY"> -->
                                                    <button class="btn btn-primary" type="submit" name="submitOtpAndCompleteGstr1File" onclick="return confirm('Are you sure?');">7. File GSTR1</button>
                                                </form>


                                                <!-- <form action="" method="post" class="col-md-2 ml-2">
                                                    <button class="btn btn-primary" type="submit" name="submitGstr1PortalData" onclick="return confirm('Are you sure?');">Submit Portal Data</button>
                                                </form> -->
                                                <form action="" method="post" class="col-md-3 m-2">
                                                    <button class="btn btn-primary" type="submit" name="resetGstr1PortalData" onclick="return confirm('Are you sure?');">Reset Portal Data</button>
                                                </form>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->
<?php
require_once("../common/footer.php");
?>
<script src="../../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../../public/assets/apexchart/chart-data.js"></script>
<script src="../../public/assets/piechart/piecore.js"></script>
<script src="https://amcharts.com/lib/4/charts.js"></script>
<script src="https://amcharts.com/lib/4/themes/animated.js"></script>
<script src="../../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../../public/assets/apexchart/chart-data.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://amcharts.com/lib/3/amcharts.js?x"></script>
<script src="https://amcharts.com/lib/3/serial.js?x"></script>
<script src="https://amcharts.com/lib/3/themes/dark.js"></script>
<script>
    $(document).ready(function() {
        console.log("Document loaded");
    });
</script>